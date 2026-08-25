<?php
/**
 * universities.php
 * -----------------
 * Shows all scholarships, OR filters them if the user searched from
 * the homepage (via ?q=...).
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$q = trim($_GET['q'] ?? '');

$sql = "
    SELECT scholarships.*, universities.name AS university_name, universities.country
    FROM scholarships
    JOIN universities ON scholarships.university_id = universities.id
    WHERE scholarships.is_active = 1
";
$params = [];

if ($q !== '') {
    $sql .= " AND (universities.name LIKE ? OR universities.country LIKE ? OR scholarships.title LIKE ?)";
    $like = "%$q%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY scholarships.amount DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<section class="container">
    <h1 class="section-title">
        <?php echo $q !== '' ? 'Results for "' . htmlspecialchars($q) . '"' : 'All Scholarships'; ?>
    </h1>

    <form action="universities.php" method="GET" class="search-form inline">
        <input type="text" name="q" placeholder="Search university or country..." value="<?php echo htmlspecialchars($q); ?>">
        <button type="submit">Search</button>
    </form>

    <p class="muted"><?php echo count($results); ?> scholarship(s) found.</p>

    <div class="scholarship-grid">
        <?php if (empty($results)): ?>
            <p>No scholarships matched your search. Try a different university or country.</p>
        <?php endif; ?>

        <?php foreach ($results as $sch): ?>
            <div class="scholarship-card">
                <img src="<?php echo htmlspecialchars($sch['image_url']); ?>" alt="<?php echo htmlspecialchars($sch['title']); ?>">
                <div class="scholarship-card-body">
                    <h3><?php echo htmlspecialchars($sch['title']); ?></h3>
                    <p class="muted"><?php echo htmlspecialchars($sch['university_name'] . ', ' . $sch['country']); ?></p>
                    <p><?php echo $sch['duration_months']; ?> months &middot; up to <?php echo $sch['max_applicants']; ?> applicants</p>
                    <div class="scholarship-card-footer">
                        <span class="amount">$<?php echo number_format($sch['amount'], 2); ?></span>
                        <a href="scholarship.php?id=<?php echo $sch['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
