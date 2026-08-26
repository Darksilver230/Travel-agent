<?php
/**
 * create_stripe_checkout.php
 * ---------------------------
 * Creates a Stripe Checkout Session for both applications and trips.
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/payment_config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || STRIPE_SECRET_KEY === '') {
    header('Location: index.php');
    exit;
}

$type = $_POST['type'] ?? 'application';
$id   = (int)($_POST['id'] ?? 0);

$record = null;
$item_name = '';

if ($type === 'trip') {
    $stmt = $pdo->prepare("
        SELECT bookings.*, packages.title
        FROM bookings
        JOIN packages ON bookings.package_id = packages.id
        WHERE bookings.id = ? AND bookings.user_id = ? AND bookings.payment_status = 'unpaid'
    ");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $record = $stmt->fetch();
    if ($record) {
        $total = $record['total_price'];
        $item_name = $record['title'] . ' (Booking #' . $id . ')';
    }
} else {
    $stmt = $pdo->prepare("
        SELECT applications.*, scholarships.title
        FROM applications
        JOIN scholarships ON applications.scholarship_id = scholarships.id
        WHERE applications.id = ? AND applications.user_id = ? AND applications.payment_status = 'unpaid'
    ");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $record = $stmt->fetch();
    if ($record) {
        $total = $record['total_fee'];
        $item_name = $record['title'] . ' (Application #' . $id . ')';
    }
}

if (!$record) {
    header('Location: index.php');
    exit;
}

$amount_cents = (int) round($total * 100);

$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

$params = [
    'mode' => 'payment',
    'success_url' => $baseUrl . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}&type=' . $type . '&id=' . $id,
    'cancel_url'  => $baseUrl . '/payment.php?type=' . $type . '&id=' . $id,
    'line_items' => [
        [
            'quantity' => 1,
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $amount_cents,
                'product_data' => [
                    'name' => $item_name,
                ],
            ],
        ],
    ],
];

$postFields = http_build_query($params);

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_USERPWD => STRIPE_SECRET_KEY . ':',
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$session = json_decode($response, true);

if ($httpCode !== 200 || empty($session['url'])) {
    $message = $session['error']['message'] ?? 'Could not start checkout. Please try again.';
    header('Location: payment.php?type=' . $type . '&id=' . $id . '&error=' . urlencode($message));
    exit;
}

header('Location: ' . $session['url']);
exit;
