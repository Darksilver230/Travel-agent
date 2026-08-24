<?php
/**
 * destinations.php
 * -----------------
 * Shows all packages, OR filters them if the user searched from
 * the homepage (via ?q=...&travel_date=...&travelers=...).
 *
 * KEY BEGINNER LESSON: never build SQL by concatenating strings like
 *   "WHERE name = '" . $_GET['q'] . "'"
 * That's how SQL injection happens. Instead we use "?" placeholders
 * and pass the real values in separately via execute([...]). PDO
 * handles escaping for us.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

// Grab and sanitize search inputs. trim() removes stray whitespace;
// if a field wasn't submitted, default to an empty string.
$q         = trim($_GET['q'] ?? '');
$travelers = (int)($_GET['travelers'] ?? 0);

// Build the query in pieces depending on whether a search term was given.
$sql = "
    SELECT packages.*, destinations.name AS destination_name, destinations.country
    FROM packages
    JOIN destinations ON packages.destination_id = destinations.id
    WHERE packages.is_active = 1
";
$params = [];

if ($q !== '') {
    // Search matches destination name, country, or package title.
    $sql .= " AND (destinations.name LIKE ? OR destinations.country LIKE ? OR packages.title LIKE ?)";
    $like = "%$q%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($travelers > 0) {
    $sql .= " AND packages.max_travelers >= ?";
    $params[] = $travelers;
}

$sql .= " ORDER BY packages.price ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<section class="container">
    <h1 class="section-title">
        <?php echo $q !== '' ? 'Results for "' . htmlspecialchars($q) . '"' : 'All Packages'; ?>
    </h1>

    <form action="destinations.php" method="GET" class="search-form inline">
        <input type="text" name="q" placeholder="Search destination..." value="<?php echo htmlspecialchars($q); ?>">
        <input type="number" name="travelers" min="1" placeholder="Travelers" value="<?php echo $travelers ?: ''; ?>">
        <button type="submit">Search</button>
    </form>

    <p class="muted"><?php echo count($results); ?> package(s) found.</p>

    <div class="package-grid">
        <?php if (empty($results)): ?>
            <p>No packages matched your search. Try a different destination.</p>
        <?php endif; ?>

        <?php foreach ($results as $pkg): ?>
            <div class="package-card">
                <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                <div class="package-card-body">
                    <h3><?php echo htmlspecialchars($pkg['title']); ?></h3>
                    <p class="muted"><?php echo htmlspecialchars($pkg['destination_name'] . ', ' . $pkg['country']); ?></p>
                    <p><?php echo $pkg['duration_days']; ?> days &middot; up to <?php echo $pkg['max_travelers']; ?> travelers</p>
                    <div class="package-card-footer">
                        <span class="price">$<?php echo number_format($pkg['price'], 2); ?></span>
                        <a href="package.php?id=<?php echo $pkg['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
