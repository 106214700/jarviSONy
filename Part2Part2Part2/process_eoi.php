<?php
/*
 * process_eoi.php | owner: Dung | Jira P2-05 / P2-06
 * Receives the apply.php form, validates every field on the server,
 * stores a valid application in the eoi table and shows the EOInumber.
 *
 * Flow:  1 guard -> 2 read -> 3 validate (collect EVERY error) -> 4 database checks
 *        -> 5 error page  OR  6 sanitise -> 7 insert -> 8 confirmation
 *
 * Note: functions.php sets mysqli_report(MYSQLI_REPORT_OFF), so mysqli functions
 * return false on failure (no exceptions). Every database step is checked.
 */
require_once __DIR__ . '/functions.php';   // Vinh: db_connect(), e(), clean_input(), redirect()

/* ---------- 1. GUARD: block direct URL access ----------
 * Opening process_eoi.php in the browser is a GET request with no form data,
 * so we send the visitor to the form. redirect() calls exit, so nothing below runs. */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['job_ref'])) {
    redirect('apply.php');
}

/* ---------- Allowed values (whitelists) ---------- */
const EOI_GENDERS = ['female', 'male', 'non-binary', 'not-stated'];
const EOI_STATES  = ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'];
const EOI_SKILLS  = ['html', 'css', 'accessibility', 'responsive', 'git', 'wireframing', 'research', 'writing'];

/* First digit(s) a postcode may start with in each state (rule I).
 * ACT accepts 0 or 2 (02xx and 26xx-29xx), as agreed in the guide. */
const EOI_POSTCODE_START = [
    'VIC' => '/^[38]/', 'NSW' => '/^[12]/', 'QLD' => '/^[49]/', 'NT' => '/^0[89]/',
    'WA'  => '/^6/',    'SA'  => '/^5/',    'TAS' => '/^7/',    'ACT' => '/^[02]/',
];

/* Same DDL as the eoi block of pedapigogy.sql, with IF NOT EXISTS (brief: create the table in code). */
const EOI_CREATE_SQL = "CREATE TABLE IF NOT EXISTS eoi (
  EOInumber INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_ref VARCHAR(5) NOT NULL,
  first_name VARCHAR(20) NOT NULL, last_name VARCHAR(20) NOT NULL,
  dob DATE NOT NULL,
  gender ENUM('female','male','non-binary','not-stated') NOT NULL,
  street VARCHAR(255) NOT NULL, suburb VARCHAR(255) NOT NULL,
  state ENUM('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL,
  postcode CHAR(4) NOT NULL,
  email VARCHAR(320) NOT NULL, phone VARCHAR(12) NOT NULL,
  skill_html TINYINT(1) NOT NULL DEFAULT 0, skill_css TINYINT(1) NOT NULL DEFAULT 0,
  skill_accessibility TINYINT(1) NOT NULL DEFAULT 0, skill_responsive TINYINT(1) NOT NULL DEFAULT 0,
  skill_git TINYINT(1) NOT NULL DEFAULT 0, skill_wireframing TINYINT(1) NOT NULL DEFAULT 0,
  skill_research TINYINT(1) NOT NULL DEFAULT 0, skill_writing TINYINT(1) NOT NULL DEFAULT 0,
  other_skills TEXT NULL,
  status ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

/* Shows a page with a heading and a list of messages, then stops the script.
 * Used for validation errors and for database problems (never the raw DB error). */
function eoi_show_problem_page(string $heading, array $messages): void
{
    $page_title  = 'Application not sent';
    $body_class  = 'page-apply';
    $active_page = 'apply';
    include __DIR__ . '/header.inc';
    echo '    <div class="container">', "\n";
    echo '      <div class="msg msg--error apply-errors" role="alert">', "\n";
    echo '        <h1>', e($heading), '</h1>', "\n";
    echo '        <ul>', "\n";
    foreach ($messages as $message) {
        echo '          <li>', e($message), '</li>', "\n";
    }
    echo '        </ul>', "\n";
    echo '      </div>', "\n";
    echo '      <p><a class="btn" href="apply.php">Back to the form</a></p>', "\n";
    echo '    </div>', "\n";
    include __DIR__ . '/footer.inc';
    exit;   // without exit, the insert below would still run
}

/* Database failure: log the real error for the team, show a friendly message, stop. */
function eoi_database_failed($conn, string $step): void
{
    error_log('process_eoi.php ' . $step . ' failed: ' . ($conn ? mysqli_error($conn) : 'no connection'));
    if ($conn) {
        mysqli_close($conn);
    }
    eoi_show_problem_page('Sorry, something went wrong',
        ['We could not save your application right now. Please try again later.']);
}

/* ---------- 2. READ ----------
 * $posted = the value exactly as sent ("" if missing or not text).
 * $raw    = trimmed, slashes removed: this is what we VALIDATE (lengths, patterns).
 * Later, clean_input($posted) gives the escaped value we STORE (decision D08). */
$text_fields = ['job_ref', 'first_name', 'last_name', 'dob', 'gender', 'street', 'suburb',
                'state', 'postcode', 'email', 'phone', 'other_skills'];
$posted = [];
$raw    = [];
foreach ($text_fields as $field) {
    $value = $_POST[$field] ?? '';           // ?? avoids "undefined array key" when a field is missing
    if (!is_string($value)) {               // someone posted an array, e.g. first_name[]=x
        $value = '';
    }
    $posted[$field] = $value;
    $raw[$field]    = trim(stripslashes($value));
}

// No box ticked = "skills" is not posted at all, so default to an empty list.
$skills = $_POST['skills'] ?? [];
if (!is_array($skills)) {
    $skills = [$skills];
}

/* ---------- 3. VALIDATE: check every rule, collect every message ---------- */
$errors = [];

// A. job_ref: exactly 5 letters/digits (existence in jobs is checked in step 4)
$job_ref_format_ok = preg_match('/^[A-Za-z0-9]{5}$/', $raw['job_ref']) === 1;
if (!$job_ref_format_ok) {
    $errors[] = 'Job reference must be 5 letters or digits that match an open job (for example LL101).';
}

// B, C. names: 1 to 20 letters only
if (preg_match('/^[A-Za-z]{1,20}$/', $raw['first_name']) !== 1) {
    $errors[] = 'First name must be 1 to 20 letters.';
}
if (preg_match('/^[A-Za-z]{1,20}$/', $raw['last_name']) !== 1) {
    $errors[] = 'Last name must be 1 to 20 letters.';
}

// D. dob: dd/mm/yyyy, a real date, not in the future, age 15 to 80 (team rule)
$dob_for_db = null;
if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $raw['dob'], $parts) !== 1) {
    $errors[] = 'Date of birth must be a real date in dd/mm/yyyy format.';
} else {
    $day   = (int) $parts[1];
    $month = (int) $parts[2];
    $year  = (int) $parts[3];
    if (!checkdate($month, $day, $year)) {              // note the order: month, day, year
        $errors[] = 'Date of birth must be a real date in dd/mm/yyyy format.';
    } else {
        $dob   = new DateTime(sprintf('%04d-%02d-%02d', $year, $month, $day));
        $today = new DateTime('today');
        if ($dob > $today) {
            $errors[] = 'Date of birth cannot be in the future.';
        } else {
            $age = $dob->diff($today)->y;               // full years between dob and today
            if ($age < 15 || $age > 80) {
                $errors[] = 'Applicants must be between 15 and 80 years old.';
            } else {
                $dob_for_db = $dob->format('Y-m-d');    // MySQL DATE format
            }
        }
    }
}

// E. gender: one of the radio values (strict = exact string match)
if (!in_array($raw['gender'], EOI_GENDERS, true)) {
    $errors[] = 'Please choose a gender option.';
}

// F, G. street and suburb: 1 to 40 characters (mb_strlen counts each letter once, even accented)
if ($raw['street'] === '' || mb_strlen($raw['street']) > 40) {
    $errors[] = 'Street address must be 1 to 40 characters.';
}
if ($raw['suburb'] === '' || mb_strlen($raw['suburb']) > 40) {
    $errors[] = 'Suburb or town must be 1 to 40 characters.';
}

// H. state
$state_ok = in_array($raw['state'], EOI_STATES, true);
if (!$state_ok) {
    $errors[] = 'Please choose a state.';
}

// I. postcode: 4 digits, and must match the state
if (preg_match('/^[0-9]{4}$/', $raw['postcode']) !== 1) {
    $errors[] = 'Postcode must be exactly 4 digits.';
} elseif ($state_ok && preg_match(EOI_POSTCODE_START[$raw['state']], $raw['postcode']) !== 1) {
    $errors[] = 'Postcode ' . $raw['postcode'] . ' does not match the state ' . $raw['state'] . '.';
}

// J. email
if (strlen($raw['email']) > 254 || filter_var($raw['email'], FILTER_VALIDATE_EMAIL) === false) {
    $errors[] = 'Please enter a valid email address.';
}

// K. phone: 8 to 12 digits
if (preg_match('/^[0-9]{8,12}$/', $raw['phone']) !== 1) {
    $errors[] = 'Phone number must be 8 to 12 digits.';
}

// L. skills: at least one, and every value must be in the allowed list
$chosen_skills = [];
foreach ($skills as $skill) {
    if (is_string($skill) && in_array($skill, EOI_SKILLS, true)) {
        $chosen_skills[] = $skill;
    } else {
        $errors[] = 'One of the selected skills is not valid.';
        break;
    }
}
if (count($chosen_skills) === 0) {
    $errors[] = 'Please tick at least one skill.';
}

// M. other_skills: optional, at most 1000 characters
if (mb_strlen($raw['other_skills']) > 1000) {
    $errors[] = 'Other skills must be 1000 characters or fewer.';
}

// N. status is never read from the form: the table default 'New' is used.

/* ---------- 4. DATABASE: connect, create the table if missing, check job_ref ----------
 * We connect before showing errors so "unknown job" appears in the same list. */
$conn = db_connect();
if ($conn === false) {
    eoi_database_failed(false, 'connect');
}

if (!mysqli_query($conn, EOI_CREATE_SQL)) {          // does nothing if the table already exists
    eoi_database_failed($conn, 'create table');
}

// A (part 2). The job reference must exist in the jobs table (prepared statement).
if ($job_ref_format_ok) {
    $stmt = mysqli_prepare($conn, 'SELECT job_ref FROM jobs WHERE job_ref = ?');
    if (!$stmt) {
        eoi_database_failed($conn, 'prepare job check');
    }
    mysqli_stmt_bind_param($stmt, 's', $raw['job_ref']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) === 0) {
        $errors[] = 'Job reference ' . $raw['job_ref'] . ' does not match an open job.';
    }
    mysqli_stmt_close($stmt);
}

/* ---------- 5. ERRORS: show them all and stop (nothing is inserted) ---------- */
if (count($errors) > 0) {
    mysqli_close($conn);
    eoi_show_problem_page('Please fix the following and send your application again', $errors);
}

/* ---------- 6. SANITISE for storage: trim + stripslashes + htmlspecialchars ---------- */
$clean = [];
foreach ($text_fields as $field) {
    $clean[$field] = clean_input($posted[$field]);
}
$job_ref_db      = strtoupper($clean['job_ref']);                                     // ll101 -> LL101
$other_skills_db = ($clean['other_skills'] === '') ? null : $clean['other_skills'];  // empty -> NULL

// One 0/1 value per skill column.
$skill_flags = [];
foreach (EOI_SKILLS as $skill) {
    $skill_flags[$skill] = in_array($skill, $chosen_skills, true) ? 1 : 0;
}

/* ---------- 7. INSERT (prepared: 20 placeholders, 20 type letters) ----------
 * Types: 11 x s (job_ref ... phone), 8 x i (skills), 1 x s (other_skills).
 * status, EOInumber and submitted_at are left out: the table fills them in. */
$sql = 'INSERT INTO eoi
          (job_ref, first_name, last_name, dob, gender, street, suburb, state, postcode, email, phone,
           skill_html, skill_css, skill_accessibility, skill_responsive,
           skill_git, skill_wireframing, skill_research, skill_writing, other_skills)
        VALUES (?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?)';
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    eoi_database_failed($conn, 'prepare insert');
}
mysqli_stmt_bind_param($stmt, 'sssssssssssiiiiiiiis',
    $job_ref_db, $clean['first_name'], $clean['last_name'], $dob_for_db, $clean['gender'],
    $clean['street'], $clean['suburb'], $clean['state'], $clean['postcode'], $clean['email'], $clean['phone'],
    $skill_flags['html'], $skill_flags['css'], $skill_flags['accessibility'], $skill_flags['responsive'],
    $skill_flags['git'], $skill_flags['wireframing'], $skill_flags['research'], $skill_flags['writing'],
    $other_skills_db);
if (!mysqli_stmt_execute($stmt)) {
    eoi_database_failed($conn, 'insert');
}

$eoi_number = mysqli_insert_id($conn);   // the AUTO_INCREMENT value just created, same connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

/* ---------- 8. CONFIRMATION ---------- */
$page_title  = 'Application received';
$body_class  = 'page-apply';
$active_page = 'apply';
include __DIR__ . '/header.inc';
?>
    <div class="container">
      <div class="msg msg--success apply-confirm" role="status">
        <h1>Thank you, <?php echo e($clean['first_name']); ?>.</h1>
        <p>Your application has been received. Please keep your Expression of Interest number:</p>
        <p class="apply-confirm__number">EOI number <strong><?php echo e((string) $eoi_number); ?></strong></p>
      </div>
      <h2>Summary</h2>
      <dl class="apply-confirm__summary">
        <dt>Job reference</dt><dd><?php echo e($job_ref_db); ?></dd>
        <dt>Name</dt><dd><?php echo e($clean['first_name'] . ' ' . $clean['last_name']); ?></dd>
        <dt>Email</dt><dd><?php echo e($clean['email']); ?></dd>
        <dt>Status</dt><dd>New</dd>
      </dl>
      <p>We use your details only to process your application.</p>
      <p><a class="btn" href="jobs.php">Back to jobs</a> <a class="btn" href="index.php">Home</a></p>
    </div>
<?php include __DIR__ . '/footer.inc'; ?>
