<?php
/**
 * process_payment.php
 * --------------------
 * Handles the "I've Sent the Transfer" form from payment.php. We
 * can't automatically confirm a bank transfer landed (no live
 * connection to your bank), so we just record the customer's
 * reference number and mark the booking 'pending_verification'.
 * An admin then checks their bank statement and manually marks it
 * paid (see the README for a simple way to do that via phpMyAdmin,
 * or build an admin page — noted as a next step).
 */
require 'includes/db.php';
require 'includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$booking_id = (int)($_POST['booking_id'] ?? 0);
$reference  = trim($_POST['transfer_reference'] ?? '');

if ($reference === '') {
    header("Location: payment.php?booking_id=$booking_id&error=" . urlencode('Please enter your transfer reference.'));
    exit;
}

// Make sure this booking belongs to the logged-in user before touching it.
$stmt = $pdo->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    UPDATE bookings
    SET payment_method = 'bank_transfer',
        payment_status = 'pending_verification',
        payment_reference = ?
    WHERE id = ?
");
$stmt->execute([$reference, $booking_id]);

header("Location: payment.php?booking_id=$booking_id");
exit;
