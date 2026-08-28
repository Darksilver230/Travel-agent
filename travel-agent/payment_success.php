<?php
/**
 * payment_success.php
 * --------------------
 * Stripe redirects here after payment. Verifies with Stripe
 * and marks the record as paid (works for both types).
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/mailer.php';
require 'includes/payment_config.php';
require_login();

$session_id = $_GET['session_id'] ?? '';
$type       = $_GET['type'] ?? 'application';
$id         = (int)($_GET['id'] ?? 0);

if ($session_id === '' || $id <= 0) {
    header('Location: index.php');
    exit;
}

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
    $table = ($type === 'trip') ? 'bookings' : 'applications';
    $stmt = $pdo->prepare("
        UPDATE $table
        SET payment_status = 'paid',
            payment_method = 'card',
            payment_reference = ?,
            paid_at = NOW(),
            status = 'confirmed'
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$session_id, $id, $_SESSION['user_id']]);

    $user = current_user($pdo);
    if ($user) {
        $label = ($type === 'trip') ? 'booking' : 'application';
        $subject = 'Payment Confirmed - ' . ucfirst($label) . ' #' . $id;
        $body  = 'Hi ' . $user['full_name'] . ",\n\n";
        $body .= "Your payment for $label #$id has been received and confirmed.\n";
        $body .= 'Thank you — your ' . $label . ' is now confirmed.';
        $body .= email_footer();
        send_email($user['email'], $subject, $body);
    }
}

$success_page = ($type === 'trip') ? 'my_trips.php' : 'my_applications.php';
$retry_url    = 'payment.php?type=' . $type . '&id=' . $id;

include 'includes/header.php';
?>

<section class="container payment-page">
    <?php if ($paid): ?>
        <div class="alert success">
            <h1>Payment Successful!</h1>
            <p>Your <?php echo $type === 'trip' ? 'booking' : 'application'; ?> #<?php echo $id; ?> is confirmed. A receipt has been sent to your email by Stripe.</p>
        </div>
        <a href="<?php echo $success_page; ?>" class="btn">View My <?php echo $type === 'trip' ? 'Trips' : 'Applications'; ?></a>
    <?php else: ?>
        <div class="alert error">
            <h1>Payment Not Confirmed</h1>
            <p>We could not verify this payment. If you were charged, please contact support with <?php echo $type === 'trip' ? 'booking' : 'application'; ?> #<?php echo $id; ?>.</p>
        </div>
        <a href="<?php echo $retry_url; ?>" class="btn">Try Again</a>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
