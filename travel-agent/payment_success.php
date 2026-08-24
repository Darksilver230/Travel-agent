<?php
/**
 * payment_success.php
 * --------------------
 * Stripe redirects the customer here after they pay. IMPORTANT: we
 * do NOT just trust that the redirect happening means payment
 * succeeded — a redirect can be faked/replayed by anyone. Instead we
 * take the session_id Stripe gave us and ask Stripe's API directly
 * "was this session actually paid?" and only mark the booking paid
 * if Stripe confirms it.
 *
 * (For a production site, the fully-robust way to confirm payment is
 * a Stripe "webhook" — a server-to-server notification — since a
 * customer could close the tab before this page even loads. That's
 * a good next step once you're comfortable with this flow.)
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/payment_config.php';
require_login();

$session_id = $_GET['session_id'] ?? '';
$booking_id = (int)($_GET['booking_id'] ?? 0);

if ($session_id === '' || $booking_id <= 0) {
    header('Location: index.php');
    exit;
}

// Ask Stripe directly whether this checkout session was paid.
$ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . urlencode($session_id));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD => STRIPE_SECRET_KEY . ':',
]);
$response = curl_exec($ch);
curl_close($ch);
$session = json_decode($response, true);

$paid = isset($session['payment_status']) && $session['payment_status'] === 'paid';

if ($paid) {
    // Confirm this booking belongs to the logged-in user, then mark it paid.
    $stmt = $pdo->prepare("
        UPDATE bookings
        SET payment_status = 'paid',
            payment_method = 'card',
            payment_reference = ?,
            paid_at = NOW(),
            status = 'confirmed'
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$session_id, $booking_id, $_SESSION['user_id']]);
}

include 'includes/header.php';
?>

<section class="container payment-page">
    <?php if ($paid): ?>
        <div class="alert success">
            <h1>Payment Successful!</h1>
            <p>Your booking #<?php echo $booking_id; ?> is confirmed. A receipt has been sent to your email by Stripe.</p>
        </div>
        <a href="my_bookings.php" class="btn">View My Bookings</a>
    <?php else: ?>
        <div class="alert error">
            <h1>Payment Not Confirmed</h1>
            <p>We couldn't verify this payment. If you were charged, please contact support with booking #<?php echo $booking_id; ?>.</p>
        </div>
        <a href="payment.php?booking_id=<?php echo $booking_id; ?>" class="btn">Try Again</a>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
