<?php
declare(strict_types=1);

// Direct-access guard: this file is an include only; redirect to index.php if requested by URL.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    header('Location: index.php');
    exit;
}

// Fallback escape helper, used only if the project has not already defined e().
if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

/**
 * Print a <ul> with one <li> per non-empty line of $text.
 * Lines are separated by newlines, as stored in the jobs table.
 */
function lines_to_list($text): void
{
    $lines = preg_split('/\R/u', (string) $text) ?: [];

    echo "            <ul>\n";
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        echo '              <li>', e($line), "</li>\n";
    }
    echo "            </ul>\n";
}

/**
 * Print one job <article> for a row of the jobs table.
 * Expected keys: job_ref, title, summary, salary, reports_to,
 * responsibilities, essential_reqs, preferable_reqs.
 */
function render_job_card(array $row): void
{
    $ref = $row['job_ref'] ?? '';

    echo '        <article class="jobs-job" id="job-', e($ref), '" aria-labelledby="title-', e($ref), "\">\n";
    echo '          <h3 id="title-', e($ref), '">', e($row['title'] ?? ''), "</h3>\n";
    echo '          <p>', e($row['summary'] ?? ''), "</p>\n";

    echo "          <dl class=\"jobs-meta\">\n";
    echo "            <dt>Reference number</dt>\n";
    echo '            <dd>', e($ref), "</dd>\n";
    echo "            <dt>Salary</dt>\n";
    echo '            <dd>', e($row['salary'] ?? ''), "</dd>\n";
    echo "            <dt>Reports to</dt>\n";
    echo '            <dd>', e($row['reports_to'] ?? ''), "</dd>\n";
    echo "          </dl>\n";

    echo '          <section class="jobs-block" aria-labelledby="resp-', e($ref), "\">\n";
    echo '            <h4 id="resp-', e($ref), "\">Key responsibilities</h4>\n";
    lines_to_list($row['responsibilities'] ?? '');
    echo "          </section>\n";

    echo '          <section class="jobs-block" aria-labelledby="ess-', e($ref), "\">\n";
    echo '            <h4 id="ess-', e($ref), "\">Essential requirements</h4>\n";
    lines_to_list($row['essential_reqs'] ?? '');
    echo "          </section>\n";

    echo '          <section class="jobs-block" aria-labelledby="pref-', e($ref), "\">\n";
    echo '            <h4 id="pref-', e($ref), "\">Preferable requirements</h4>\n";
    lines_to_list($row['preferable_reqs'] ?? '');
    echo "          </section>\n";

    echo '          <p><a class="btn" href="apply.php?job=', e(urlencode((string) $ref)), '">Apply for ', e($ref), "</a></p>\n";
    echo "        </article>\n";
}
