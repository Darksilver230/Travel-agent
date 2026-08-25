<?php
/**
 * index.php - Homepage
 * ---------------------
 * Scholarship platform homepage with hero, stats, how it works,
 * featured scholarships, and a call-to-action.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$stmt = $pdo->query("
    SELECT scholarships.*, universities.name AS university_name, universities.country
    FROM scholarships
    JOIN universities ON scholarships.university_id = universities.id
    WHERE scholarships.is_active = 1
    ORDER BY scholarships.amount DESC
    LIMIT 6
");
$featured = $stmt->fetchAll();

$uniCount = $pdo->query("SELECT COUNT(*) FROM universities")->fetchColumn();
$schCount = $pdo->query("SELECT COUNT(*) FROM scholarships WHERE is_active = 1")->fetchColumn();
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge">Trusted by 10,000+ Students Worldwide</span>
            <h1>Your Gateway to World-Class Scholarships</h1>
            <p>We help international students find, apply for, and secure scholarships at top universities across the globe. From expert guidance to application support — we are with you every step of the way.</p>

            <form action="universities.php" method="GET" class="search-form">
                <div class="field">
                    <label for="q">Search by University or Country</label>
                    <input type="text" id="q" name="q" placeholder="e.g. Oxford, Canada, Japan...">
                </div>
                <button type="submit">Search Scholarships</button>
            </form>
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
            <span class="stat-number">50+</span>
            <span class="stat-label">Countries Covered</span>
        </div>
        <div class="stat">
            <span class="stat-number">95%</span>
            <span class="stat-label">Success Rate</span>
        </div>
    </div>
</section>

<section class="container">
    <h2 class="section-title">How It Works</h2>
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">1</div>
            <h3>Browse Scholarships</h3>
            <p>Explore our curated list of scholarships from top universities around the world. Filter by country, field of study, or amount.</p>
        </div>
        <div class="step-card">
            <div class="step-number">2</div>
            <h3>Apply Online</h3>
            <p>Found the right scholarship? Submit your application directly through our platform. Upload documents and track your progress in one place.</p>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <h3>Get Expert Support</h3>
            <p>Our advisors review your application, help with essays, and prepare you for interviews to maximize your chances of acceptance.</p>
        </div>
        <div class="step-card">
            <div class="step-number">4</div>
            <h3>Start Your Journey</h3>
            <p>Once accepted, we help with visa applications, accommodation, and everything else you need to begin your academic journey abroad.</p>
        </div>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Featured Scholarships</h2>
    <div class="scholarship-grid">
        <?php foreach ($featured as $sch): ?>
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
    <div style="text-align:center; margin-bottom: 48px;">
        <a href="universities.php" class="btn">View All Scholarships</a>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Why Students Choose Us</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🎯</div>
            <h3>Personalized Matching</h3>
            <p>We match you with scholarships that fit your academic profile, background, and goals — so you only apply where you have the best chance.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👩‍🎓</div>
            <h3>Expert Advisors</h3>
            <p>Our team has helped thousands of students secure scholarships at universities like Oxford, MIT, and the University of Tokyo.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📄</div>
            <h3>Application Support</h3>
            <p>From essay reviews to document preparation, we make sure your application is polished and submission-ready.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💰</div>
            <h3>Affordable Fees</h3>
            <p>Our service fees are transparent with no hidden costs. We only charge when your application is successful.</p>
        </div>
    </div>
</section>

<section class="container">
    <div class="cta-box">
        <h2>Ready to Study Abroad?</h2>
        <p>Join thousands of international students who have already secured their dream scholarships. Start your journey today.</p>
        <div class="cta-buttons">
            <a href="universities.php" class="btn btn-accent">Browse Scholarships</a>
            <a href="services.php" class="btn btn-outline">Our Services</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
