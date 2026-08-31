<?php
/**
 * trip.php
 * --------
 * Shows full details for ONE travel package and the booking form.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT packages.*, destinations.name AS destination_name, destinations.country
    FROM packages
    JOIN destinations ON packages.destination_id = destinations.id
    WHERE packages.id = ? AND packages.is_active = 1
");
$stmt->execute([$id]);
$pkg = $stmt->fetch();

if (!$pkg) {
    echo '<div class="container"><p>Trip not found.</p><a href="travel.php">Back to all trips</a></div>';
    include 'includes/footer.php';
    exit;
}

$bookingError = $_GET['error'] ?? null;
$user = current_user($pdo);
?>

<section class="container scholarship-detail page-top">
    <a href="travel.php" class="back-link">&larr; Back to all trips</a>

    <div class="scholarship-detail-grid">
        <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">

        <div>
            <span class="scholarship-badge"><?php echo htmlspecialchars($pkg['country']); ?></span>
            <h1><?php echo htmlspecialchars($pkg['title']); ?></h1>
            <p class="muted"><?php echo htmlspecialchars($pkg['destination_name'] . ', ' . $pkg['country']); ?></p>
            <p><?php echo htmlspecialchars($pkg['description']); ?></p>
            <ul class="detail-list">
                <li><strong>Duration:</strong> <?php echo $pkg['duration_days']; ?> days</li>
                <li><strong>Max Travelers:</strong> <?php echo $pkg['max_travelers']; ?></li>
                <li><strong>Available:</strong> <?php echo $pkg['available_from']; ?> to <?php echo $pkg['available_to']; ?></li>
                <li><strong>Price:</strong> $<?php echo number_format($pkg['price'], 2); ?> per person</li>
            </ul>
        </div>
    </div>

    <div class="application-box">
        <h2>Book This Trip</h2>

        <?php if ($bookingError): ?>
            <p class="alert error"><?php echo htmlspecialchars($bookingError); ?></p>
        <?php endif; ?>

        <?php if (!$user): ?>
            <p class="alert">
                You need an account to book a trip.
                <a href="login.php?redirect=<?php echo urlencode('trip.php?id=' . $pkg['id']); ?>">Log in</a>
                or
                <a href="register.php?redirect=<?php echo urlencode('trip.php?id=' . $pkg['id']); ?>">create one</a>
                — it only takes a minute.
            </p>
        <?php else: ?>
            <p class="muted">Booking as <?php echo htmlspecialchars($user['full_name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)</p>

            <form action="process_trip.php" method="POST" class="application-form">
                <input type="hidden" name="package_id" value="<?php echo $pkg['id']; ?>">

                <div class="field">
                    <label for="travel_date">Travel Date</label>
                    <input type="date" id="travel_date" name="travel_date"
                           min="<?php echo $pkg['available_from']; ?>"
                           max="<?php echo $pkg['available_to']; ?>" required>
                </div>
                <div class="field">
                    <label for="num_travelers">Number of Travelers</label>
                    <input type="number" id="num_travelers" name="num_travelers"
                           min="1" max="<?php echo $pkg['max_travelers']; ?>" value="1" required>
                </div>
                <div class="field full-width">
                    <label for="special_requests">Special Requests (optional)</label>
                    <textarea id="special_requests" name="special_requests" rows="3"></textarea>
                </div>

                <button type="submit">Continue to Payment</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
