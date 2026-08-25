<?php
/**
 * process_application.php
 * -------------------------
 * Receives the POST from the application form on scholarship.php,
 * validates it, saves an application record tied to the LOGGED-IN
 * user, then sends them to payment.php to pay the application fee.
 */
require 'includes/db.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!is_logged_in()) {
    header('Location: login.php?redirect=' . urlencode('index.php'));
    exit;
}

$user_id          = $_SESSION['user_id'];
$scholarship_id   = (int)($_POST['scholarship_id'] ?? 0);
$deadline_date    = $_POST['deadline_date'] ?? '';
$num_applicants   = (int)($_POST['num_applicants'] ?? 1);
$special_requests = trim($_POST['special_requests'] ?? '');

function redirect_with_error($scholarship_id, $message) {
    header("Location: scholarship.php?id=$scholarship_id&error=" . urlencode($message));
    exit;
}

if ($scholarship_id <= 0 || $deadline_date === '') {
    redirect_with_error($scholarship_id, 'Please fill in the preferred start date.');
}
if ($num_applicants < 1) {
    redirect_with_error($scholarship_id, 'Number of applicants must be at least 1.');
}

$stmt = $pdo->prepare("SELECT * FROM scholarships WHERE id = ? AND is_active = 1");
$stmt->execute([$scholarship_id]);
$sch = $stmt->fetch();

if (!$sch) {
    redirect_with_error($scholarship_id, 'That scholarship is no longer available.');
}
if ($num_applicants > $sch['max_applicants']) {
    redirect_with_error($scholarship_id, 'Too many applicants for this scholarship (max ' . $sch['max_applicants'] . ').');
}

$total_fee = $sch['amount'] * $num_applicants;

$stmt = $pdo->prepare("
    INSERT INTO applications (scholarship_id, user_id, num_applicants, deadline_date, total_fee, special_requests, status, payment_status)
    VALUES (?, ?, ?, ?, ?, ?, 'pending', 'unpaid')
");
$stmt->execute([$scholarship_id, $user_id, $num_applicants, $deadline_date, $total_fee, $special_requests]);
$application_id = $pdo->lastInsertId();

header("Location: payment.php?application_id=$application_id");
exit;
