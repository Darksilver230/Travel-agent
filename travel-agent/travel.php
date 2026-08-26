<?php
/**
 * travel.php
 * -----------
 * Shows all travel packages, with optional search filtering.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$q = trim($_GET['q'] ?? '');

$sql = "
    SELECT packages.*, destinations.name AS destination_name, destinations.country
    FROM packages
    JOIN destinations ON packages.destination_id = destinations.id
    WHERE packages.is_active = 1
";
$params = [];

if ($q !== '') {
    $sql .= " AND (destinations.name LIKE ? OR destinations.country LIKE ? OR packages.title LIKE ?)";
    $like = "%$q%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY packages.price ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<section class="hero hero-travel">
    <div class="container">
        <h1>Explore Travel Plans</h1>
        <p>Curated trips to the world most beautiful destinations. Book your next adventure today.</p>

        <form action="travel.php" method="GET" class="search-form">
            <div class="field">
                <label for="q">Search Destination</label>
                <input type="text" id="q" name="q" placeholder="e.g. Paris, Bali, Dubai..." value="<?php echo htmlspecialchars($q); ?>">
            </div>
            <button type="submit">Search Trips</button>
        </form>
    </div>
</section>

<section class="container">
    <h1 class="section-title">
        <?php echo $q !== '' ? 'Results for "' . htmlspecialchars($q) . '"' : 'All Travel Plans'; ?>
    </h1>

    <p class="muted"><?php echo count($results); ?> trip(s) found.</p>

    <div class="scholarship-grid">
        <?php if (empty($results)): ?>
            <p>No trips matched your search. Try a different destination.</p>
        <?php endif; ?>

        <?php foreach ($results as $pkg): ?>
            <div class="scholarship-card">
                <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                <div class="scholarship-card-body">
                    <span class="scholarship-badge"><?php echo htmlspecialchars($pkg['country']); ?></span>
                    <h3><?php echo htmlspecialchars($pkg['title']); ?></h3>
                    <p class="muted"><?php echo htmlspecialchars($pkg['destination_name']); ?></p>
                    <p><?php echo $pkg['duration_days']; ?> days &middot; up to <?php echo $pkg['max_travelers']; ?> travelers</p>
                    <div class="scholarship-card-footer">
                        <div>
                            <span class="amount">$<?php echo number_format($pkg['price'], 2); ?></span>
                            <span class="muted">per person</span>
                        </div>
                        <a href="trip.php?id=<?php echo $pkg['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
