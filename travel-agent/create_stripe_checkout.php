<?php
/**
 * create_stripe_checkout.php
 * ---------------------------
 * Creates a Stripe "Checkout Session" and redirects the customer to
 * Stripe's hosted payment page — this is where they actually type
 * their card number, on Stripe's servers, not ours.
 *
 * This talks to Stripe's REST API directly with cURL so you don't
 * need Composer/the Stripe PHP SDK for this starter project. If you
 * later install the official stripe-php library, this whole file
 * becomes a few lines shorter — but this works fine as-is.
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/payment_config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || STRIPE_SECRET_KEY === '') {
    header('Location: index.php');
    exit;
}

$booking_id = (int)($_POST['booking_id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT bookings.*, packages.title
    FROM bookings
    JOIN packages ON bookings.package_id = packages.id
    WHERE bookings.id = ? AND bookings.user_id = ? AND bookings.payment_status = 'unpaid'
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: index.php');
    exit;
}

// Stripe wants the amount in the smallest currency unit (cents for
// USD), so multiply by 100 and round to a whole number.
$amount_cents = (int) round($booking['total_price'] * 100);

// Figure out the base URL so Stripe knows where to send the customer
// back to after they pay (or cancel).
$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

// Stripe's API takes regular POST fields, using [] array syntax
// for nested objects — this mirrors what their docs show.
$params = [
    'mode' => 'payment',
    'success_url' => $baseUrl . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $booking_id,
    'cancel_url'  => $baseUrl . '/payment.php?booking_id=' . $booking_id,
    'line_items' => [
        [
            'quantity' => 1,
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $amount_cents,
                'product_data' => [
                    'name' => $booking['title'] . ' (Booking #' . $booking_id . ')',
                ],
            ],
        ],
    ],
];

// Stripe expects standard form-encoded data with PHP-style bracket
// notation for nested arrays — http_build_query does this for us.
$postFields = http_build_query($params);

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    // Stripe auths via HTTP Basic Auth using your secret key as the
    // username and an empty password.
    CURLOPT_USERPWD => STRIPE_SECRET_KEY . ':',
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$session = json_decode($response, true);

if ($httpCode !== 200 || empty($session['url'])) {
    $message = $session['error']['message'] ?? 'Could not start checkout. Please try again.';
    header('Location: payment.php?booking_id=' . $booking_id . '&error=' . urlencode($message));
    exit;
}

// Send the customer to Stripe's hosted checkout page.
header('Location: ' . $session['url']);
exit;
