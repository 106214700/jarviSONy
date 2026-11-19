<?php
declare(strict_types=1);

// Template variables read by header.inc and nav.inc
$page_title  = "Search results";
$body_class  = "page-jobs";
$active_page = "jobs";

require_once "functions.php";
require_once "job_functions.php";

// 1. Read the keyword. trim() removes spaces at both ends.
//    The value is never printed raw: every echo below goes through e().
$q = trim((string) ($_GET['q'] ?? ''));

// 2. Limit to 100 characters. mb_substr counts characters, not bytes,
//    so a Vietnamese letter such as "ệ" is never cut in half.
$q = mb_substr($q, 0, 100, 'UTF-8');

$rows        = [];      // matching jobs
$db_error    = false;   // true when the database could not be used
$has_keyword = ($q !== '');

if ($has_keyword) {
    $conn = db_connect();

    if (!$conn) {
        $db_error = true;
    } else {
        // 3. In SQL LIKE, % means "any text" and _ means "any one character".
        //    addcslashes() puts a backslash in front of %, _ and \ so they are
        //    searched as plain characters. The % at each end is added to the
        //    VALUE (not the SQL) to mean "contains".
        $like = '%' . addcslashes($q, '%_\\') . '%';

        // 4. Six ? placeholders, one per column. User text never enters the SQL string.
        $sql = "SELECT * FROM jobs
                WHERE title LIKE ? OR summary LIKE ? OR job_ref LIKE ?
                   OR responsibilities LIKE ? OR essential_reqs LIKE ? OR preferable_reqs LIKE ?
                ORDER BY job_ref";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            error_log('search.php prepare failed: ' . mysqli_error($conn));
            $db_error = true;
        } else {
            // 5. Type string "ssssss": one letter per ? placeholder, in order.
            //      s = string, i = integer, d = double (decimal), b = blob.
            //    Every placeholder is compared with a text column using a text
            //    pattern, so all six are "s". The same $like is bound six times
            //    because bind_param takes variables (by reference), not literals.
            mysqli_stmt_bind_param($stmt, "ssssss", $like, $like, $like, $like, $like, $like);
            mysqli_stmt_execute($stmt);

            // get_result + fetch_assoc work with XAMPP's mysqlnd driver.
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $rows[] = $row;
                }
                mysqli_free_result($result);
            } else {
                error_log('search.php get_result failed: ' . mysqli_stmt_error($stmt));
                $db_error = true;
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
    }
}

$count = count($rows);

include "header.inc";
?>
  <main id="main">
    <div class="container jobs-wrap">
      <h1>Search jobs</h1>

      <!-- Search box: GET form, keeps what the visitor typed (escaped with e()) -->
      <form class="jobs-search" role="search" action="search.php" method="get">
        <label for="q">Search jobs</label>
        <input type="search" id="q" name="q" maxlength="100" value="<?php echo e($q); ?>" placeholder="e.g. accessibility">
        <button class="btn" type="submit">Search</button>
      </form>

<?php if ($db_error): ?>
      <p class="msg msg--error">Sorry, the search is not available right now. Please try again later.</p>

<?php elseif (!$has_keyword): ?>
      <p class="msg">Please type a keyword to search, for example "accessibility". Or <a href="jobs.php">view all jobs</a>.</p>

<?php else: ?>
      <h2 role="status"><?php echo $count; ?> <?php echo ($count === 1) ? 'result' : 'results'; ?> for "<?php echo e($q); ?>"</h2>

<?php if ($count === 0): ?>
      <p class="msg">No jobs match "<?php echo e($q); ?>". Try "accessibility", "tutor" or "developer", or <a href="jobs.php">view all jobs</a>.</p>
<?php else: ?>
<?php foreach ($rows as $row) { render_job_card($row); } ?>
<?php endif; ?>
<?php endif; ?>
    </div>
  </main>

<?php include "footer.inc"; ?>
