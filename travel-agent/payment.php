<?php
/**
 * payment.php
 * -----------
 * Shows the booking summary and lets the customer choose how to pay:
 *   - Card, via Stripe Checkout (redirects to Stripe's hosted page)
 *   - Bank Transfer (shows account details, customer submits a
 *     reference once they've sent the money; an admin verifies it
 *     manually later — this is normal for bank transfers, since
 *     there's no instant confirmation like a card gives you)
 */
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/payment_config.php';
require_login(); // must be logged in to view/pay for a booking

include 'includes/header.php';

$booking_id = (int)($_GET['booking_id'] ?? 0);

// Only fetch bookings that belong to THIS logged-in user — otherwise
// someone could pay for/view another user's booking just by guessing
// ?booking_id=123 in the URL.
$stmt = $pdo->prepare("
    SELECT bookings.*, packages.title, packages.image_url, destinations.name AS destination_name
    FROM bookings
    JOIN packages ON bookings.package_id = packages.id
    JOIN destinations ON packages.destination_id = destinations.id
    WHERE bookings.id = ? AND bookings.user_id = ?
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    echo '<div class="container"><p>Booking not found.</p></div>';
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

    <?php if ($booking['payment_status'] === 'paid'): ?>
        <p class="alert success">This booking is already paid in full. Thank you!</p>
    <?php elseif ($booking['payment_status'] === 'pending_verification'): ?>
        <p class="alert">
            We've received your bank transfer reference and are verifying it.
            You'll get a confirmation email once it's matched — this usually
            takes 1-2 business days.
        </p>
    <?php endif; ?>

    <div class="booking-summary">
        <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="">
        <div>
            <h2><?php echo htmlspecialchars($booking['title']); ?></h2>
            <p class="muted"><?php echo htmlspecialchars($booking['destination_name']); ?></p>
            <ul class="detail-list">
                <li><strong>Travel Date:</strong> <?php echo htmlspecialchars($booking['travel_date']); ?></li>
                <li><strong>Travelers:</strong> <?php echo (int)$booking['num_travelers']; ?></li>
                <li><strong>Booking ID:</strong> #<?php echo $booking['id']; ?></li>
                <li><strong>Total Due:</strong> <span class="price">$<?php echo number_format($booking['total_price'], 2); ?></span></li>
            </ul>
        </div>
    </div>

    <?php if ($booking['payment_status'] === 'unpaid'): ?>
    <div class="payment-methods">

        <!-- ---------------- CARD (Stripe Checkout) ---------------- -->
        <div class="payment-option">
            <h3>Pay by Card</h3>
            <p class="muted">Securely handled by Stripe. We never see or store your card details.</p>

            <?php if ($stripeConfigured): ?>
                <form action="create_stripe_checkout.php" method="POST">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <button type="submit">Pay $<?php echo number_format($booking['total_price'], 2); ?> with Card</button>
                </form>
            <?php else: ?>
                <p class="alert">
                    Card payments aren't set up yet. Add your Stripe API keys in
                    <code>includes/payment_config.php</code> to enable this
                    (see the comments in that file for step-by-step instructions —
                    it's free to get test keys).
                </p>
            <?php endif; ?>
        </div>

        <!-- ---------------- BANK TRANSFER ---------------- -->
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
                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                <div class="field">
                    <label for="transfer_reference">Your Transfer Reference / Confirmation Number</label>
                    <input type="text" id="transfer_reference" name="transfer_reference" required>
                </div>
                <button type="submit">I've Sent the Transfer</button>
            </form>
        </div>

    </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
