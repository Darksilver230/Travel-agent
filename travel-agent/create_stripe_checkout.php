<?php
/**
 * create_stripe_checkout.php
 * ---------------------------
 * Creates a Stripe Checkout Session and redirects the applicant to
 * Stripe's hosted payment page.
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/payment_config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || STRIPE_SECRET_KEY === '') {
    header('Location: index.php');
    exit;
}

$application_id = (int)($_POST['application_id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT applications.*, scholarships.title
    FROM applications
    JOIN scholarships ON applications.scholarship_id = scholarships.id
    WHERE applications.id = ? AND applications.user_id = ? AND applications.payment_status = 'unpaid'
");
$stmt->execute([$application_id, $_SESSION['user_id']]);
$application = $stmt->fetch();

if (!$application) {
    header('Location: index.php');
    exit;
}

$amount_cents = (int) round($application['total_fee'] * 100);

$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

$params = [
    'mode' => 'payment',
    'success_url' => $baseUrl . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}&application_id=' . $application_id,
    'cancel_url'  => $baseUrl . '/payment.php?application_id=' . $application_id,
    'line_items' => [
        [
            'quantity' => 1,
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $amount_cents,
                'product_data' => [
                    'name' => $application['title'] . ' (Application #' . $application_id . ')',
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
    header('Location: payment.php?application_id=' . $application_id . '&error=' . urlencode($message));
    exit;
}

header('Location: ' . $session['url']);
exit;
