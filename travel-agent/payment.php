<?php
/**
 * payment.php
 * -----------
 * Shows the application summary and lets the applicant choose how to
 * pay the application fee:
 *   - Card, via Stripe Checkout
 *   - Bank Transfer (shows account details, applicant submits a
 *     reference once they have sent the payment)
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/payment_config.php';
require_login();

include 'includes/header.php';

$application_id = (int)($_GET['application_id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT applications.*, scholarships.title, scholarships.image_url, universities.name AS university_name
    FROM applications
    JOIN scholarships ON applications.scholarship_id = scholarships.id
    JOIN universities ON scholarships.university_id = universities.id
    WHERE applications.id = ? AND applications.user_id = ?
");
$stmt->execute([$application_id, $_SESSION['user_id']]);
$application = $stmt->fetch();

if (!$application) {
    echo '<div class="container"><p>Application not found.</p></div>';
    include 'includes/footer.php';
    exit;
}

$error = $_GET['error'] ?? null;
$stripeConfigured = STRIPE_SECRET_KEY !== '';
$bank = BANK_TRANSFER_DETAILS;
?>

<section class="container payment-page">
    <h1>Complete Your Payment</h1>

    <?php if ($error): ?>
        <p class="alert error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($application['payment_status'] === 'paid'): ?>
        <p class="alert success">This application fee has been paid in full. Thank you!</p>
    <?php elseif ($application['payment_status'] === 'pending_verification'): ?>
        <p class="alert">
            We have received your bank transfer reference and are verifying it.
            You will get a confirmation email once it is matched — this usually
            takes 1-2 business days.
        </p>
    <?php endif; ?>

    <div class="application-summary">
        <img src="<?php echo htmlspecialchars($application['image_url']); ?>" alt="">
        <div>
            <h2><?php echo htmlspecialchars($application['title']); ?></h2>
            <p class="muted"><?php echo htmlspecialchars($application['university_name']); ?></p>
            <ul class="detail-list">
                <li><strong>Preferred Start:</strong> <?php echo htmlspecialchars($application['deadline_date']); ?></li>
                <li><strong>Applicants:</strong> <?php echo (int)$application['num_applicants']; ?></li>
                <li><strong>Application ID:</strong> #<?php echo $application['id']; ?></li>
                <li><strong>Total Due:</strong> <span class="amount">$<?php echo number_format($application['total_fee'], 2); ?></span></li>
            </ul>
        </div>
    </div>

    <?php if ($application['payment_status'] === 'unpaid'): ?>
    <div class="payment-methods">

        <div class="payment-option">
            <h3>Pay by Card</h3>
            <p class="muted">Securely handled by Stripe. We never see or store your card details.</p>

            <?php if ($stripeConfigured): ?>
                <form action="create_stripe_checkout.php" method="POST">
                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                    <button type="submit">Pay $<?php echo number_format($application['total_fee'], 2); ?> with Card</button>
                </form>
            <?php else: ?>
                <p class="alert">
                    Card payments are not set up yet. Add your Stripe API keys in
                    <code>includes/payment_config.php</code> to enable this.
                </p>
            <?php endif; ?>
        </div>

        <div class="payment-option">
            <h3>Pay by Bank Transfer</h3>
            <p class="muted">Transfer the total to the account below, then submit your reference number.</p>

            <ul class="bank-details">
                <li><strong>Account Name:</strong> <?php echo htmlspecialchars($bank['account_name']); ?></li>
                <li><strong>Account Number:</strong> <?php echo htmlspecialchars($bank['account_number']); ?></li>
                <li><strong>Sort Code / Routing:</strong> <?php echo htmlspecialchars($bank['sort_code']); ?></li>
                <li><strong>Bank Name:</strong> <?php echo htmlspecialchars($bank['bank_name']); ?></li>
            </ul>
            <p class="muted"><?php echo htmlspecialchars($bank['reference_note']); ?></p>

            <form action="process_payment.php" method="POST" class="bank-transfer-form">
                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                <div class="field">
                    <label for="transfer_reference">Your Transfer Reference / Confirmation Number</label>
                    <input type="text" id="transfer_reference" name="transfer_reference" required>
                </div>
                <button type="submit">I have Sent the Transfer</button>
            </form>
        </div>

    </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
