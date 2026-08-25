<?php
/**
 * process_payment.php
 * --------------------
 * Handles the "I have Sent the Transfer" form from payment.php.
 * Records the reference and marks the application 'pending_verification'.
 */
require 'includes/db.php';
require 'includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$application_id = (int)($_POST['application_id'] ?? 0);
$reference      = trim($_POST['transfer_reference'] ?? '');

if ($reference === '') {
    header("Location: payment.php?application_id=$application_id&error=" . urlencode('Please enter your transfer reference.'));
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM applications WHERE id = ? AND user_id = ?");
$stmt->execute([$application_id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    UPDATE applications
    SET payment_method = 'bank_transfer',
        payment_status = 'pending_verification',
        payment_reference = ?
    WHERE id = ?
");
$stmt->execute([$reference, $application_id]);

header("Location: payment.php?application_id=$application_id");
exit;
