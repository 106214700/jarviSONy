<?php
// manage.php | owner: Quan (login protection by Vinh) | HR manager page
require_once __DIR__ . '/functions.php';
require_login();                                   // MUST stay the first thing: not logged in -> login.php, before any output

$page_title  = "Manage applications";
$body_class  = "page-manage";
$active_page = "manage";

include __DIR__ . '/header.inc';
?>
        <div class="container">
            <h1>Manage applications</h1>
            <p>Logged in as <strong><?php echo e($_SESSION['username'] ?? ''); ?></strong>. <a href="logout.php">Log out</a></p>

            <?php /* TODO(Quan): list all EOIs, filter by job reference / first name / last name / both,
                     sort by a chosen field, change status, delete all EOIs for a job reference.
                     Use prepared statements, a sort whitelist and csrf_field()/csrf_check() (guide Part B). */ ?>
            <div class="msg" role="status">
                The management tools are being built (Quan). You are logged in, so the login protection works.
            </div>
        </div>
<?php include __DIR__ . '/footer.inc'; ?>
