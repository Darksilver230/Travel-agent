<?php
/**
 * my_bookings.php
 * ----------------
 * Combined page showing the logged-in user's scholarship applications
 * and travel bookings with payment status.
 */
require 'includes/db.php';
require 'includes/auth.php';
require_login();

include 'includes/header.php';

$userId = $_SESSION['user_id'];

$appStmt = $pdo->prepare("
    SELECT applications.*, scholarships.title, scholarships.image_url, universities.name AS university_name
    FROM applications
    JOIN scholarships ON applications.scholarship_id = scholarships.id
    JOIN universities ON scholarships.university_id = universities.id
    WHERE applications.user_id = ?
    ORDER BY applications.created_at DESC
");
$appStmt->execute([$userId]);
$applications = $appStmt->fetchAll();

$tripStmt = $pdo->prepare("
    SELECT bookings.*, packages.title, packages.image_url, destinations.name AS destination_name
    FROM bookings
    JOIN packages ON bookings.package_id = packages.id
    JOIN destinations ON packages.destination_id = destinations.id
    WHERE bookings.user_id = ?
    ORDER BY bookings.created_at DESC
");
$tripStmt->execute([$userId]);
$bookings = $tripStmt->fetchAll();

function booking_status_label(string $status): string {
    return match ($status) {
        'paid' => 'Paid',
        'pending_verification' => 'Verifying Payment',
        'unpaid' => 'Payment Needed',
        default => ucfirst($status),
    };
}
?>

<section class="container page-top">
    <h1 class="section-title">My Bookings</h1>

    <?php if (empty($applications) && empty($bookings)): ?>
        <p>You have no bookings yet. <a href="universities.php">Browse scholarships</a> or <a href="travel.php">explore travel plans</a>.</p>
    <?php endif; ?>

    <?php if (!empty($applications)): ?>
        <h2 class="booking-section-title">Scholarship Applications</h2>
        <div class="applications-list">
            <?php foreach ($applications as $app): ?>
                <div class="application-row">
                    <img src="<?php echo htmlspecialchars($app['image_url']); ?>" alt="">
                    <div class="application-row-body">
                        <h3><?php echo htmlspecialchars($app['title']); ?></h3>
                        <p class="muted"><?php echo htmlspecialchars($app['university_name']); ?> &middot; <?php echo htmlspecialchars($app['deadline_date']); ?> &middot; <?php echo (int)$app['num_applicants']; ?> applicant(s)</p>
                        <p class="amount">$<?php echo number_format($app['total_fee'], 2); ?></p>
                    </div>
                    <div class="application-row-status">
                        <span class="status-badge status-<?php echo htmlspecialchars($app['payment_status']); ?>">
                            <?php echo booking_status_label($app['payment_status']); ?>
                        </span>
                        <?php if ($app['payment_status'] !== 'paid'): ?>
                            <a href="payment.php?application_id=<?php echo $app['id']; ?>" class="btn">Go to Payment</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($bookings)): ?>
        <h2 class="booking-section-title">Travel Bookings</h2>
        <div class="applications-list">
            <?php foreach ($bookings as $b): ?>
                <div class="application-row">
                    <img src="<?php echo htmlspecialchars($b['image_url']); ?>" alt="">
                    <div class="application-row-body">
                        <h3><?php echo htmlspecialchars($b['title']); ?></h3>
                        <p class="muted"><?php echo htmlspecialchars($b['destination_name']); ?> &middot; <?php echo htmlspecialchars($b['travel_date']); ?> &middot; <?php echo (int)$b['num_travelers']; ?> traveler(s)</p>
                        <p class="amount">$<?php echo number_format($b['total_price'], 2); ?></p>
                    </div>
                    <div class="application-row-status">
                        <span class="status-badge status-<?php echo htmlspecialchars($b['payment_status']); ?>">
                            <?php echo booking_status_label($b['payment_status']); ?>
                        </span>
                        <?php if ($b['payment_status'] !== 'paid'): ?>
                            <a href="payment.php?type=trip&id=<?php echo $b['id']; ?>" class="btn">Go to Payment</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
