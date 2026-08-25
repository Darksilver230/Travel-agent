<?php
/**
 * payment_success.php
 * --------------------
 * Stripe redirects the applicant here after payment. We verify
 * directly with Stripe that the session was actually paid before
 * marking the application as paid.
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/payment_config.php';
require_login();

$session_id     = $_GET['session_id'] ?? '';
$application_id = (int)($_GET['application_id'] ?? 0);

if ($session_id === '' || $application_id <= 0) {
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
    $stmt = $pdo->prepare("
        UPDATE applications
        SET payment_status = 'paid',
            payment_method = 'card',
            payment_reference = ?,
            paid_at = NOW(),
            status = 'confirmed'
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$session_id, $application_id, $_SESSION['user_id']]);
}

include 'includes/header.php';
?>

<section class="container payment-page">
    <?php if ($paid): ?>
        <div class="alert success">
            <h1>Payment Successful!</h1>
            <p>Your application #<?php echo $application_id; ?> is confirmed. A receipt has been sent to your email by Stripe.</p>
        </div>
        <a href="my_applications.php" class="btn">View My Applications</a>
    <?php else: ?>
        <div class="alert error">
            <h1>Payment Not Confirmed</h1>
            <p>We could not verify this payment. If you were charged, please contact support with application #<?php echo $application_id; ?>.</p>
        </div>
        <a href="payment.php?application_id=<?php echo $application_id; ?>" class="btn">Try Again</a>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
