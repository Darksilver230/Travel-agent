<?php
/**
 * payment.php
 * -----------
 * Handles payment for both scholarship applications and trip bookings.
 * Use ?type=application&id=... or ?type=trip&id=...
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/payment_config.php';
require_login();

include 'includes/header.php';

$type = $_GET['type'] ?? 'application';
$id   = (int)($_GET['id'] ?? 0);

$record = null;
$title = '';
$subtitle = '';
$date_label = '';
$date_value = '';
$quantity_label = '';
$quantity_value = '';
$image_url = '';
$total = 0;
$redirect_base = 'payment.php';

if ($type === 'trip') {
    $stmt = $pdo->prepare("
        SELECT bookings.*, packages.title, packages.image_url, destinations.name AS destination_name
        FROM bookings
        JOIN packages ON bookings.package_id = packages.id
        JOIN destinations ON packages.destination_id = destinations.id
        WHERE bookings.id = ? AND bookings.user_id = ?
    ");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $record = $stmt->fetch();
    if ($record) {
        $title = $record['title'];
        $subtitle = $record['destination_name'];
        $date_label = 'Travel Date';
        $date_value = $record['travel_date'];
        $quantity_label = 'Travelers';
        $quantity_value = $record['num_travelers'];
        $image_url = $record['image_url'];
        $total = $record['total_price'];
    }
} else {
    $stmt = $pdo->prepare("
        SELECT applications.*, scholarships.title, scholarships.image_url, universities.name AS university_name
        FROM applications
        JOIN scholarships ON applications.scholarship_id = scholarships.id
        JOIN universities ON scholarships.university_id = universities.id
        WHERE applications.id = ? AND applications.user_id = ?
    ");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $record = $stmt->fetch();
    if ($record) {
        $title = $record['title'];
        $subtitle = $record['university_name'];
        $date_label = 'Preferred Start';
        $date_value = $record['deadline_date'];
        $quantity_label = 'Applicants';
        $quantity_value = $record['num_applicants'];
        $image_url = $record['image_url'];
        $total = $record['total_fee'];
    }
}

if (!$record) {
    echo '<div class="container"><p>Record not found.</p></div>';
    include 'includes/footer.php';
    exit;
}

$error = $_GET['error'] ?? null;
$stripeConfigured = STRIPE_SECRET_KEY !== '';
$bank = BANK_TRANSFER_DETAILS;
$id_label = $type === 'trip' ? 'Booking ID' : 'Application ID';
?>

<section class="container payment-page">
    <h1>Complete Your Payment</h1>

    <?php if ($error): ?>
        <p class="alert error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($record['payment_status'] === 'paid'): ?>
        <p class="alert success">This payment has been completed. Thank you!</p>
    <?php elseif ($record['payment_status'] === 'pending_verification'): ?>
        <p class="alert">
            We have received your bank transfer reference and are verifying it.
            You will get a confirmation email once it is matched — this usually
            takes 1-2 business days.
        </p>
    <?php endif; ?>

    <div class="application-summary">
        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="">
        <div>
            <h2><?php echo htmlspecialchars($title); ?></h2>
            <p class="muted"><?php echo htmlspecialchars($subtitle); ?></p>
            <ul class="detail-list">
                <li><strong><?php echo $date_label; ?>:</strong> <?php echo htmlspecialchars($date_value); ?></li>
                <li><strong><?php echo $quantity_label; ?>:</strong> <?php echo (int)$quantity_value; ?></li>
                <li><strong><?php echo $id_label; ?>:</strong> #<?php echo $id; ?></li>
                <li><strong>Total Due:</strong> <span class="amount">$<?php echo number_format($total, 2); ?></span></li>
            </ul>
        </div>
    </div>

    <?php if ($record['payment_status'] === 'unpaid'): ?>
    <div class="payment-methods">

        <div class="payment-option">
            <h3>Pay by Card</h3>
            <p class="muted">Securely handled by Stripe. We never see or store your card details.</p>

            <?php if ($stripeConfigured): ?>
                <form action="create_stripe_checkout.php" method="POST">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <button type="submit">Pay $<?php echo number_format($total, 2); ?> with Card</button>
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
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
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
