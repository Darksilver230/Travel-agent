<?php
/**
 * process_booking.php
 * --------------------
 * Receives the POST from the booking form on package.php, validates
 * it, saves a booking record tied to the LOGGED-IN user, then sends
 * them to payment.php to actually pay for it.
 *
 * The booking is created with payment_status = 'unpaid' — it only
 * becomes 'paid' once payment.php / process_payment.php confirms
 * payment. This mirrors how real booking systems work: you reserve
 * first, then pay, and the record exists either way so an admin can
 * follow up on abandoned/unpaid bookings.
 */
require 'includes/db.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Must be logged in to book — if the session expired mid-form, bounce
// to login and remember they were mid-booking isn't trivial via GET
// redirect here (POST data would be lost), so just send them to login.
if (!is_logged_in()) {
    header('Location: login.php?redirect=' . urlencode('index.php'));
    exit;
}

$user_id           = $_SESSION['user_id'];
$package_id        = (int)($_POST['package_id'] ?? 0);
$travel_date       = $_POST['travel_date'] ?? '';
$num_travelers     = (int)($_POST['num_travelers'] ?? 1);
$special_requests  = trim($_POST['special_requests'] ?? '');

function redirect_with_error($package_id, $message) {
    header("Location: package.php?id=$package_id&error=" . urlencode($message));
    exit;
}

// ---- Validation ----
if ($package_id <= 0 || $travel_date === '') {
    redirect_with_error($package_id, 'Please fill in the travel date.');
}
if ($num_travelers < 1) {
    redirect_with_error($package_id, 'Number of travelers must be at least 1.');
}

// Look up the package so we know it exists, and calculate the total
// price ourselves (never trust a price sent from the browser).
$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ? AND is_active = 1");
$stmt->execute([$package_id]);
$pkg = $stmt->fetch();

if (!$pkg) {
    redirect_with_error($package_id, 'That package is no longer available.');
}
if ($num_travelers > $pkg['max_travelers']) {
    redirect_with_error($package_id, 'Too many travelers for this package (max ' . $pkg['max_travelers'] . ').');
}

$total_price = $pkg['price'] * $num_travelers;

$stmt = $pdo->prepare("
    INSERT INTO bookings (package_id, user_id, num_travelers, travel_date, total_price, special_requests, status, payment_status)
    VALUES (?, ?, ?, ?, ?, ?, 'pending', 'unpaid')
");
$stmt->execute([$package_id, $user_id, $num_travelers, $travel_date, $total_price, $special_requests]);
$booking_id = $pdo->lastInsertId();

// Send them to pay for the booking they just created.
header("Location: payment.php?booking_id=$booking_id");
exit;
