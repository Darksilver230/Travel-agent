<?php
/**
 * auth.php
 * --------
 * Session + login helper functions, included at the top of any page
 * that needs to know whether someone is logged in.
 *
 * HOW SESSIONS WORK (quick primer):
 * When a user logs in successfully, we store their user id in PHP's
 * $_SESSION superglobal. PHP sets a cookie in the browser containing
 * a session ID; on every later request, PHP uses that cookie to load
 * the matching $_SESSION data back up on the server. The browser
 * never sees the actual session contents — just an opaque ID — so
 * this is safe to store things like "logged_in_user_id" in.
 */

// session_start() must run before ANY HTML is output, which is why
// every page that uses auth.php includes it at the very top.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Is anyone currently logged in?
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Fetch the full row for the logged-in user, or null if not logged in.
 * Cheap to call repeatedly since it's usually just one small query.
 */
function current_user(PDO $pdo): ?array {
    if (!is_logged_in()) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT id, full_name, email, phone FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

/**
 * Call this at the top of any page that REQUIRES a login (e.g. the
 * booking form, the payment page). If the visitor isn't logged in,
 * send them to login.php and remember where they were trying to go
 * via ?redirect=..., so login.php can bounce them back after success.
 */
function require_login(): void {
    if (!is_logged_in()) {
        $current_url = $_SERVER['REQUEST_URI'] ?? 'index.php';
        header('Location: login.php?redirect=' . urlencode($current_url));
        exit;
    }
}
