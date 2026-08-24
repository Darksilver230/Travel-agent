<?php
/**
 * process_login.php
 * ------------------
 * Handles the POST from login.php: checks email/password against
 * the database and starts a session if they match.
 */
require 'includes/db.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$redirect = $_POST['redirect'] ?? 'index.php';

function login_fail(string $redirect) {
    // Deliberately vague message — we don't reveal whether the EMAIL
    // or the PASSWORD was wrong. Saying "email not found" lets an
    // attacker use your login form to discover which emails have
    // accounts, so both cases show the same generic error.
    header('Location: login.php?error=' . urlencode('Invalid email or password.') . '&redirect=' . urlencode($redirect));
    exit;
}

if ($email === '' || $password === '') {
    login_fail($redirect);
}

$stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// password_verify() checks the submitted password against the stored
// hash. It's the counterpart to password_hash() used at registration.
if (!$user || !password_verify($password, $user['password_hash'])) {
    login_fail($redirect);
}

$_SESSION['user_id'] = $user['id'];

header('Location: ' . $redirect);
exit;
