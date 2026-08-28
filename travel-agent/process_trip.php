<?php
/**
 * process_trip.php
 * -----------------
 * Receives the POST from the booking form on trip.php, validates it,
 * saves a booking record, then sends them to payment.
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: travel.php');
    exit;
}

if (!is_logged_in()) {
    header('Location: login.php?redirect=' . urlencode('travel.php'));
    exit;
}

$user_id        = $_SESSION['user_id'];
$package_id     = (int)($_POST['package_id'] ?? 0);
$travel_date    = $_POST['travel_date'] ?? '';
$num_travelers  = (int)($_POST['num_travelers'] ?? 1);
$special_requests = trim($_POST['special_requests'] ?? '');

function redirect_trip_error($package_id, $message) {
    header("Location: trip.php?id=$package_id&error=" . urlencode($message));
    exit;
}

if ($package_id <= 0 || $travel_date === '') {
    redirect_trip_error($package_id, 'Please fill in the travel date.');
}
if ($num_travelers < 1) {
    redirect_trip_error($package_id, 'Number of travelers must be at least 1.');
}

$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ? AND is_active = 1");
$stmt->execute([$package_id]);
$pkg = $stmt->fetch();

if (!$pkg) {
    redirect_trip_error($package_id, 'That trip is no longer available.');
}
if ($num_travelers > $pkg['max_travelers']) {
    redirect_trip_error($package_id, 'Too many travelers (max ' . $pkg['max_travelers'] . ').');
}

$total_price = $pkg['price'] * $num_travelers;

$stmt = $pdo->prepare("
    INSERT INTO bookings (package_id, user_id, num_travelers, travel_date, total_price, special_requests, status, payment_status)
    VALUES (?, ?, ?, ?, ?, ?, 'pending', 'unpaid')
");
$stmt->execute([$package_id, $user_id, $num_travelers, $travel_date, $total_price, $special_requests]);
$booking_id = $pdo->lastInsertId();

$user = current_user($pdo);
if ($user) {
    $subject = 'Booking Received - ' . $pkg['title'];
    $body  = 'Hi ' . $user['full_name'] . ",\n\n";
    $body .= 'Thank you for booking: ' . $pkg['title'] . "\n";
    $body .= 'Travel date: ' . $travel_date . "\n";
    $body .= 'Booking ID: #' . $booking_id . "\n";
    $body .= 'Travelers: ' . $num_travelers . "\n";
    $body .= 'Total due: $' . number_format($total_price, 2) . "\n\n";
    $body .= 'A payment of the booking total is required to confirm your trip.';
    $body .= email_footer();
    send_email($user['email'], $subject, $body);
}

header("Location: payment.php?type=trip&id=$booking_id");
exit;
