<?php
// login.php | owner: Vinh | manager login (guide Part C2). Protects manage.php.
require_once __DIR__ . '/functions.php';

if (is_logged_in()) {
    redirect('manage.php');                       // already logged in
}
csrf_token();                                     // creates the session + token BEFORE any HTML is printed

$page_title  = "Manager login";
$body_class  = "page-login";
$active_page = "login";

$error    = '';
$username = '';
$notice   = (($_GET['msg'] ?? '') === 'loggedout') ? 'You have been logged out.' : '';
// ONE message for every failure: unknown user, wrong password and locked account look the same.
$generic  = 'Invalid login, or the account is temporarily locked. Please try again later.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');         // never trim or change a password

    if ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{1,30}$/', $username) || strlen($password) > 200) {
        $error = $generic;                                   // not a possible username: do not even query
    } else {
        $conn = db_connect();
        if (!$conn) {
            $error = 'Sorry, the login service is not available right now. Please try again later.';
        } elseif (attempt_login($conn, $username, $password)) {
            mysqli_close($conn);
            redirect('manage.php');
        } else {
            $error = $generic;
        }
        if ($conn) {
            mysqli_close($conn);
        }
    }
}

include __DIR__ . '/header.inc';
?>
        <div class="container">
            <h1>Manager login</h1>
            <p class="login-intro">This area is for Pedapigogy Learn HR managers only.</p>

<?php if ($notice !== '') { ?>
            <div class="msg msg--success" role="status"><?php echo e($notice); ?></div>
<?php } ?>
<?php if ($error !== '') { ?>
            <div class="msg msg--error" role="alert"><?php echo e($error); ?></div>
<?php } ?>

            <form class="login-form" action="login.php" method="post">
                <?php echo csrf_field(); ?>

                <div class="login-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo e($username); ?>"
                        autocomplete="username" maxlength="30">
                </div>
                <div class="login-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password">
                </div>
                <div class="login-actions">
                    <button class="btn" type="submit">Log in</button>
                </div>
            </form>
        </div>
<?php include __DIR__ . '/footer.inc'; ?>
