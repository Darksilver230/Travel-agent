<?php
/**
 * my_applications.php
 * --------------------
 * Lists the logged-in user's own scholarship applications and their
 * payment status.
 */
require 'includes/db.php';
require 'includes/auth.php';
require_login();

include 'includes/header.php';

$stmt = $pdo->prepare("
    SELECT applications.*, scholarships.title, scholarships.image_url, universities.name AS university_name
    FROM applications
    JOIN scholarships ON applications.scholarship_id = scholarships.id
    JOIN universities ON scholarships.university_id = universities.id
    WHERE applications.user_id = ?
    ORDER BY applications.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();

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
    <h1 class="section-title">My Applications</h1>

    <?php if (empty($applications)): ?>
        <p>You haven't applied for any scholarships yet. <a href="universities.php">Browse scholarships</a>.</p>
    <?php endif; ?>

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
                        <?php echo status_label($app['payment_status']); ?>
                    </span>
                    <?php if ($app['payment_status'] !== 'paid'): ?>
                        <a href="payment.php?application_id=<?php echo $app['id']; ?>" class="btn">Go to Payment</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
