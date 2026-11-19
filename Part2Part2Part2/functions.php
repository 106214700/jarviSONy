<?php
/*
 * functions.php | owner: Vinh | shared helpers for every page (guide Part A6)
 * Pages do:  require_once __DIR__ . '/functions.php';  before including header.inc
 */

// Opening this file directly in the browser does nothing useful: send the visitor home.
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    header('Location: index.php');
    exit;
}

// mysqli returns false on errors (we check for it) instead of throwing exceptions (PHP 8.1+ default).
mysqli_report(MYSQLI_REPORT_OFF);

// Shared list of allowed EOI statuses (Dung: default New; Quan: dropdown + whitelist).
const STATUSES = ['New', 'Current', 'Final'];

/**
 * Open a database connection using settings.php.
 * Returns a mysqli connection, or false if the database is not available.
 * The real error goes to the server log; pages show a friendly message.
 */
function db_connect()
{
    require __DIR__ . '/settings.php';   // plain require: the variables are local to this function
    $conn = @mysqli_connect($host, $user, $pwd, $sql_db);
    if (!$conn) {
        error_log('DB connect failed: ' . mysqli_connect_error());
        return false;
    }
    mysqli_set_charset($conn, 'utf8mb4');
    return $conn;
}

/** Escape a value for printing in HTML. Already-escaped text is not escaped twice (decision D08). */
function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8', false);
}

/** Sanitise user input: trim + strip slashes + escape HTML (decision D08 / brief). */
function clean_input($value)
{
    $value = trim((string)($value ?? ''));
    $value = stripslashes($value);
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/** Redirect to a local page and stop the script. Absolute URLs are refused (no open redirects). */
function redirect($url)
{
    if (!is_string($url) || preg_match('#^([a-z][a-z0-9+.-]*:|//)#i', $url)) {
        $url = 'index.php';
    }
    header('Location: ' . $url);
    exit;
}

/** Returns ' aria-current="page"' when $key is the current page (used by nav.inc). */
function nav_current($key)
{
    global $active_page;
    return (isset($active_page) && $active_page === $key) ? ' aria-current="page"' : '';
}

/* ---------- CSRF protection (every POST that changes data) ---------- */

/** The session token. Call it BEFORE any HTML is printed on a page that has a form. */
function csrf_token()
{
    start_secure_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Hidden input to put inside a POST form. */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Stop the request with 403 unless the posted token matches the session token. */
function csrf_check()
{
    start_secure_session();
    $sent = $_POST['csrf_token'] ?? '';
    $real = $_SESSION['csrf'] ?? '';
    if (!is_string($sent) || $real === '' || !hash_equals($real, $sent)) {
        http_response_code(403);
        exit('Invalid or expired form. Please go back, reload the page and try again.');
    }
}

// Authentication helpers (is_logged_in, require_login ...) live in auth.php.
require_once __DIR__ . '/auth.php';

// If the visitor already has a session cookie, start the session now (before any output)
// so that nav.inc can show Manage / Logout. Visitors without a cookie get no session.
if (isset($_COOKIE[session_name()])) {
    start_secure_session();
}
