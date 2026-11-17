<?php
// process_eoi.php | owner: Dung | receives the apply form (POST only), validates, inserts into eoi
require_once __DIR__ . '/functions.php';

// Block direct URL access: only a POST that carries the form's fields gets through.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['job_ref'])) {
    redirect('apply.php');
}

$page_title  = "Application";
$body_class  = "page-apply";
$active_page = "apply";

include __DIR__ . '/header.inc';
?>
        <div class="container">
            <h1>Application</h1>
            <?php /* TODO(Dung): validate every field on the server, CREATE TABLE IF NOT EXISTS eoi,
                     prepared INSERT, show the EOInumber (guide Part B). */ ?>
            <div class="msg" role="status">
                Application processing is being built (Dung). Your form reached the server.
            </div>
            <p><a href="apply.php">Back to the form</a></p>
        </div>
<?php include __DIR__ . '/footer.inc'; ?>
