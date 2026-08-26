<?php
/**
 * process_register.php
 * ---------------------
 * Handles the POST from register.php: validates input, hashes the
 * password, creates the user, logs them in, and redirects onward.
 */
require 'includes/db.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$full_name        = trim($_POST['full_name'] ?? '');
$email             = trim($_POST['email'] ?? '');
$phone             = trim($_POST['phone'] ?? '');
$password          = $_POST['password'] ?? '';
$password_confirm  = $_POST['password_confirm'] ?? '';
$redirect          = $_POST['redirect'] ?? 'index.php';

function fail(string $message, string $redirect) {
    header('Location: register.php?error=' . urlencode($message) . '&redirect=' . urlencode($redirect));
    exit;
}

// ---- Validation ----
if ($full_name === '' || $email === '' || $password === '') {
    fail('Please fill in all required fields.', $redirect);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail('Please enter a valid email address.', $redirect);
}
if (strlen($password) < 8) {
    fail('Password must be at least 8 characters.', $redirect);
}
if ($password !== $password_confirm) {
    fail('Passwords do not match.', $redirect);
}

// Check email isn't already registered.
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    fail('An account with that email already exists. Try logging in instead.', $redirect);
}

// password_hash() turns the plain password into a secure, salted
// hash (using bcrypt by default). We store ONLY this hash — the
// plain password is never written to disk or database anywhere.
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (full_name, email, password_hash, phone)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$full_name, $email, $password_hash, $phone]);

// Log the new user in immediately by storing their id in the session.
$_SESSION['user_id'] = $pdo->lastInsertId();

$_SESSION['flash'] = 'Welcome, ' . htmlspecialchars(explode(' ', $full_name)[0]) . '! Your account has been created.';
$_SESSION['flash_type'] = 'success';

header('Location: ' . $redirect);
exit;
