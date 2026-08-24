<?php
/**
 * my_bookings.php
 * ---------------
 * Lists the logged-in user's own bookings and their payment status.
 */
require 'includes/db.php';
require 'includes/auth.php';
require_login();

include 'includes/header.php';

$stmt = $pdo->prepare("
    SELECT bookings.*, packages.title, packages.image_url, destinations.name AS destination_name
    FROM bookings
    JOIN packages ON bookings.package_id = packages.id
    JOIN destinations ON packages.destination_id = destinations.id
    WHERE bookings.user_id = ?
    ORDER BY bookings.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

// Small helper just for displaying a friendly status badge.
function status_label(string $status): string {
    return match ($status) {
        'paid' => 'Paid',
        'pending_verification' => 'Verifying Payment',
        'unpaid' => 'Payment Needed',
        default => ucfirst($status),
    };
}
?>

<section class="container">
    <h1 class="section-title">My Bookings</h1>

    <?php if (empty($bookings)): ?>
        <p>You haven't booked any trips yet. <a href="destinations.php">Browse packages</a>.</p>
    <?php endif; ?>

    <div class="bookings-list">
        <?php foreach ($bookings as $b): ?>
            <div class="booking-row">
                <img src="<?php echo htmlspecialchars($b['image_url']); ?>" alt="">
                <div class="booking-row-body">
                    <h3><?php echo htmlspecialchars($b['title']); ?></h3>
                    <p class="muted"><?php echo htmlspecialchars($b['destination_name']); ?> &middot; <?php echo htmlspecialchars($b['travel_date']); ?> &middot; <?php echo (int)$b['num_travelers']; ?> traveler(s)</p>
                    <p class="price">$<?php echo number_format($b['total_price'], 2); ?></p>
                </div>
                <div class="booking-row-status">
                    <span class="status-badge status-<?php echo htmlspecialchars($b['payment_status']); ?>">
                        <?php echo status_label($b['payment_status']); ?>
                    </span>
                    <?php if ($b['payment_status'] !== 'paid'): ?>
                        <a href="payment.php?booking_id=<?php echo $b['id']; ?>" class="btn">Go to Payment</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
