<?php
/**
 * index.php - Homepage
 * ---------------------
 * Shows a search form (university/country) and a grid of featured
 * scholarships pulled live from the database.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$stmt = $pdo->query("
    SELECT scholarships.*, universities.name AS university_name, universities.country
    FROM scholarships
    JOIN universities ON scholarships.university_id = universities.id
    WHERE scholarships.is_active = 1
    ORDER BY scholarships.created_at DESC
    LIMIT 4
");
$featured = $stmt->fetchAll();
?>

<section class="hero">
    <div class="container">
        <h1>Find Your Dream Scholarship</h1>
        <p>Browse thousands of scholarships for international students and apply in minutes.</p>

        <form action="universities.php" method="GET" class="search-form">
            <div class="field">
                <label for="q">University or Country</label>
                <input type="text" id="q" name="q" placeholder="e.g. Oxford, Canada...">
            </div>
            <button type="submit">Search Scholarships</button>
        </form>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Featured Scholarships</h2>
    <div class="scholarship-grid">
        <?php foreach ($featured as $sch): ?>
            <div class="scholarship-card">
                <img src="<?php echo htmlspecialchars($sch['image_url']); ?>" alt="<?php echo htmlspecialchars($sch['title']); ?>">
                <div class="scholarship-card-body">
                    <h3><?php echo htmlspecialchars($sch['title']); ?></h3>
                    <p class="muted"><?php echo htmlspecialchars($sch['university_name'] . ', ' . $sch['country']); ?></p>
                    <p><?php echo htmlspecialchars(substr($sch['description'], 0, 90)); ?>...</p>
                    <div class="scholarship-card-footer">
                        <span class="amount">$<?php echo number_format($sch['amount'], 2); ?></span>
                        <a href="scholarship.php?id=<?php echo $sch['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="contact" class="container">
    <h2 class="section-title">Questions? Get in Touch</h2>
    <p>Email us at <a href="mailto:olowolux@gmail.com">olowolux@gmail.com</a> or use the application form on any scholarship page.</p>
</section>

<?php include 'includes/footer.php'; ?>
