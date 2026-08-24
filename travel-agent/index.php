<?php
/**
 * index.php - Homepage
 * ---------------------
 * Shows a search form (destination + dates + travelers) and a
 * grid of featured packages pulled live from the database.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

// Fetch a few packages to feature on the homepage.
// JOIN pulls in the destination name/country alongside each package.
$stmt = $pdo->query("
    SELECT packages.*, destinations.name AS destination_name, destinations.country
    FROM packages
    JOIN destinations ON packages.destination_id = destinations.id
    WHERE packages.is_active = 1
    ORDER BY packages.created_at DESC
    LIMIT 4
");
$featured = $stmt->fetchAll();
?>

<section class="hero">
    <div class="container">
        <h1>Find Your Next Adventure</h1>
        <p>Search hundreds of curated trips and book in minutes.</p>

        <!-- This form submits to destinations.php via GET, so the search
             terms show up in the URL (e.g. destinations.php?q=paris)
             and can be bookmarked/shared. -->
        <form action="destinations.php" method="GET" class="search-form">
            <div class="field">
                <label for="q">Destination</label>
                <input type="text" id="q" name="q" placeholder="e.g. Paris, Bali...">
            </div>
            <div class="field">
                <label for="travel_date">Travel Date</label>
                <input type="date" id="travel_date" name="travel_date">
            </div>
            <div class="field">
                <label for="travelers">Travelers</label>
                <input type="number" id="travelers" name="travelers" min="1" value="2">
            </div>
            <button type="submit">Search Trips</button>
        </form>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Featured Packages</h2>
    <div class="package-grid">
        <?php foreach ($featured as $pkg): ?>
            <div class="package-card">
                <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                <div class="package-card-body">
                    <h3><?php echo htmlspecialchars($pkg['title']); ?></h3>
                    <p class="muted"><?php echo htmlspecialchars($pkg['destination_name'] . ', ' . $pkg['country']); ?></p>
                    <p><?php echo htmlspecialchars(substr($pkg['description'], 0, 90)); ?>...</p>
                    <div class="package-card-footer">
                        <span class="price">$<?php echo number_format($pkg['price'], 2); ?></span>
                        <a href="package.php?id=<?php echo $pkg['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="contact" class="container">
    <h2 class="section-title">Questions? Get in Touch</h2>
    <p>Email us at <a href="mailto:hello@wanderlux.example">hello@wanderlux.example</a> or use the booking form on any package page.</p>
</section>

<?php include 'includes/footer.php'; ?>
