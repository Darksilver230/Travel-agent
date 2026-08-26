<?php
/**
 * index.php - Homepage
 * ---------------------
 * Dual-purpose homepage: scholarships + travel plans.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

// Featured scholarships
$schStmt = $pdo->query("
    SELECT scholarships.*, universities.name AS university_name, universities.country
    FROM scholarships
    JOIN universities ON scholarships.university_id = universities.id
    WHERE scholarships.is_active = 1
    ORDER BY scholarships.amount DESC
    LIMIT 3
");
$featuredScholarships = $schStmt->fetchAll();

// Featured travel packages
$tripStmt = $pdo->query("
    SELECT packages.*, destinations.name AS destination_name, destinations.country
    FROM packages
    JOIN destinations ON packages.destination_id = destinations.id
    WHERE packages.is_active = 1
    ORDER BY packages.price ASC
    LIMIT 3
");
$featuredTrips = $tripStmt->fetchAll();

$uniCount = $pdo->query("SELECT COUNT(*) FROM universities")->fetchColumn();
$schCount = $pdo->query("SELECT COUNT(*) FROM scholarships WHERE is_active = 1")->fetchColumn();
$destCount = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge">Your Gateway to Education & Adventure</span>
            <h1>Unlock Your Next Possibility<br>Beyond Limits,Beyond Borders.</h1>
            <p>We help international students find scholarships at top universities, and explore the world with curated travel plans. One platform, two possibilities.</p>            <div class="hero-tabs">
                <a href="universities.php" class="hero-tab hero-tab-active">Browse Scholarships</a>
                <a href="travel.php" class="hero-tab">Explore Travel Plans</a>
            </div>
        </div>
    </div>
</section>

<section class="stats-bar">
    <div class="container stats-inner">
        <div class="stat">
            <span class="stat-number"><?php echo $uniCount; ?>+</span>
            <span class="stat-label">Partner Universities</span>
        </div>
        <div class="stat">
            <span class="stat-number"><?php echo $schCount; ?>+</span>
            <span class="stat-label">Active Scholarships</span>
        </div>
        <div class="stat">
            <span class="stat-number"><?php echo $destCount; ?>+</span>
            <span class="stat-label">Destinations</span>
        </div>
        <div class="stat">
            <span class="stat-number">95%</span>
            <span class="stat-label">Success Rate</span>
        </div>
    </div>
</section>

<section class="container">
    <div class="section-header">
        <h2 class="section-title">Featured Scholarships</h2>
        <a href="universities.php" class="section-link">View All &rarr;</a>
    </div>
    <div class="scholarship-grid">
        <?php foreach ($featuredScholarships as $sch): ?>
            <div class="scholarship-card">
                <img src="<?php echo htmlspecialchars($sch['image_url']); ?>" alt="<?php echo htmlspecialchars($sch['title']); ?>">
                <div class="scholarship-card-body">
                    <span class="scholarship-badge"><?php echo htmlspecialchars($sch['country']); ?></span>
                    <h3><?php echo htmlspecialchars($sch['title']); ?></h3>
                    <p class="muted"><?php echo htmlspecialchars($sch['university_name']); ?></p>
                    <p><?php echo htmlspecialchars(substr($sch['description'], 0, 80)); ?>...</p>
                    <div class="scholarship-card-footer">
                        <div>
                            <span class="amount">$<?php echo number_format($sch['amount'], 2); ?></span>
                            <span class="muted"><?php echo $sch['duration_months']; ?> months</span>
                        </div>
                        <a href="scholarship.php?id=<?php echo $sch['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container">
    <div class="section-header">
        <h2 class="section-title">Popular Travel Plans</h2>
        <a href="travel.php" class="section-link">View All &rarr;</a>
    </div>
    <div class="scholarship-grid">
        <?php foreach ($featuredTrips as $pkg): ?>
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

<section class="container">
    <h2 class="section-title">How It Works</h2>
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">1</div>
            <h3>Browse & Choose</h3>
            <p>Explore scholarships from top universities or curated travel plans to exciting destinations.</p>
        </div>
        <div class="step-card">
            <div class="step-number">2</div>
            <h3>Apply or Book</h3>
            <p>Submit your scholarship application or book your dream trip directly through our platform.</p>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <h3>Get Expert Support</h3>
            <p>Our advisors help with applications, essays, visa guidance, and travel arrangements.</p>
        </div>
        <div class="step-card">
            <div class="step-number">4</div>
            <h3>Achieve Your Goals</h3>
            <p>Whether it is a scholarship or a dream trip, we help make it happen.</p>
        </div>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Why Students Choose Us</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🎯</div>
            <h3>Personalized Matching</h3>
            <p>We match you with scholarships and travel plans that fit your profile, budget, and goals.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👩‍🎓</div>
            <h3>Expert Advisors</h3>
            <p>Our team has helped thousands of students secure scholarships and book unforgettable trips.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📄</div>
            <h3>End-to-End Support</h3>
            <p>From application reviews to visa guidance and travel planning — we handle it all.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💰</div>
            <h3>Affordable & Transparent</h3>
            <p>No hidden fees. Scholarship advisory and travel booking at competitive prices.</p>
        </div>
    </div>
</section>

<section class="container">
    <div class="cta-box">
        <h2>Ready to Start Your Journey?</h2>
        <p>Whether you are seeking a world-class scholarship or planning your next adventure, we are here to help.</p>
        <div class="cta-buttons">
            <a href="universities.php" class="btn btn-accent">Browse Scholarships</a>
            <a href="travel.php" class="btn btn-outline">Explore Travel Plans</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
