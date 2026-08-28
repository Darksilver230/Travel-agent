<?php
/**
 * process_payment.php
 * --------------------
 * Handles bank transfer submission for both applications and trips.
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/mailer.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$type       = $_POST['type'] ?? 'application';
$id         = (int)($_POST['id'] ?? 0);
$reference  = trim($_POST['transfer_reference'] ?? '');

if ($reference === '') {
    header("Location: payment.php?type=$type&id=$id&error=" . urlencode('Please enter your transfer reference.'));
    exit;
}

$table = ($type === 'trip') ? 'bookings' : 'applications';

$stmt = $pdo->prepare("SELECT id FROM $table WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    UPDATE $table
    SET payment_method = 'bank_transfer',
        payment_status = 'pending_verification',
        payment_reference = ?
    WHERE id = ?
");
$stmt->execute([$reference, $id]);

$user = current_user($pdo);
$title = ($type === 'trip') ? "Booking #$id" : "Application #$id";
if ($user) {
    $subject = 'Payment Reference Received - ' . ucfirst($type);
    $body  = 'Hi ' . $user['full_name'] . ",\n\n";
    $body .= 'We received your bank transfer reference (' . $reference . ") for " . $title . ".\n";
    $body .= 'We are verifying your payment and will confirm once it is matched — usually within 1-2 business days.';
    $body .= email_footer();
    send_email($user['email'], $subject, $body);
}

header("Location: payment.php?type=$type&id=$id");
exit;
