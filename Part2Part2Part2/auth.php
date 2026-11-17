<?php
/*
 * auth.php | owner: Vinh | sessions and login helpers (guide Part C2)
 * Loaded by functions.php. Needs functions.php helpers at call time (redirect, db_connect).
 */
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    header('Location: index.php');
    exit;
}

const MAX_LOGIN_ATTEMPTS = 3;     // wrong passwords before the account is locked
const LOCK_MINUTES       = 5;     // how long the lock lasts
const IDLE_TIMEOUT       = 1800;  // seconds of inactivity before the session expires (30 min)

/** session_start() with safe cookie settings. Safe to call many times. */
function start_secure_session()
{
    if (session_status() !== PHP_SESSION_NONE || headers_sent()) {
        return;
    }
    ini_set('session.use_strict_mode', '1');      // refuse session ids the server did not create
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,                       // JavaScript cannot read the cookie
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** True when a valid, non-expired manager session exists. */
function is_logged_in()
{
    // Visitors without a session cookie cannot be logged in: do not create a session for them.
    if (session_status() === PHP_SESSION_NONE && !isset($_COOKIE[session_name()])) {
        return false;
    }
    start_secure_session();
    if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION['user_id'])) {
        return false;
    }
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > IDLE_TIMEOUT) {
        do_logout_session();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

/** Put this at the very top of every protected page, before any output. */
function require_login()
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

/** Empty the session and its cookie. */
function do_logout_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}

/** Log out and go back to the login page. */
function do_logout()
{
    start_secure_session();
    do_logout_session();
    redirect('login.php?msg=loggedout');
}

/**
 * Check a username and password.
 * Returns true on success (session is created) or false. The caller shows ONE generic message
 * for every failure, so an attacker cannot tell unknown user, wrong password and locked account apart.
 */
function attempt_login($conn, $username, $password)
{
    $sql  = "SELECT user_id, username, password_hash, failed_attempts,
                    (locked_until IS NOT NULL AND locked_until > NOW()) AS is_locked
             FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log('login prepare failed: ' . mysqli_error($conn));
        return false;
    }
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);

    if (!$row) {
        // Unknown user: still do a password check so the response time looks the same.
        password_verify($password, '$2y$10$Z01ZYiU72g/Eg05c6Bc/Fe0C4dhterOmEYeF1yMRhG7MbWLOPwGPe');
        return false;
    }
    if ((int)$row['is_locked'] === 1) {
        return false;                                   // locked: do not even check the password
    }

    if (password_verify($password, $row['password_hash'])) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $row['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        start_secure_session();
        session_regenerate_id(true);                    // new id after login: stops session fixation
        $_SESSION['user_id']       = (int)$row['user_id'];
        $_SESSION['username']      = $row['username'];
        $_SESSION['login_time']    = time();
        $_SESSION['last_activity'] = time();
        return true;
    }

    // Wrong password: count it, lock the account after MAX_LOGIN_ATTEMPTS.
    $attempts = (int)$row['failed_attempts'] + 1;
    if ($attempts >= MAX_LOGIN_ATTEMPTS) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET failed_attempts = 0,
                 locked_until = DATE_ADD(NOW(), INTERVAL " . (int)LOCK_MINUTES . " MINUTE) WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $row['user_id']);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET failed_attempts = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $attempts, $row['user_id']);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return false;
}
