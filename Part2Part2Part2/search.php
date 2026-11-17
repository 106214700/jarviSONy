<?php
// search.php | owner: Nguyen | search the jobs table (home page and jobs page send ?q= here)
require_once __DIR__ . '/functions.php';

$page_title  = "Search results";
$body_class  = "page-jobs";
$active_page = "jobs";

$q = trim((string)($_GET['q'] ?? ''));

include __DIR__ . '/header.inc';
?>
        <div class="container jobs-wrap">
            <h1>Search results</h1>
            <?php /* TODO(Nguyen): prepared LIKE query on the jobs table + render_job_card() (guide Part B). */ ?>
            <div class="msg" role="status">
                Job search is being built (Nguyen).
<?php if ($q !== '') { ?>
                You searched for <strong><?php echo e($q); ?></strong>.
<?php } ?>
                For now you can <a href="jobs.php">see all jobs</a>.
            </div>
        </div>
<?php include __DIR__ . '/footer.inc'; ?>
