<?php
/**
 * services.php
 * -------------
 * Shows the services we offer to help international students
 * with their scholarship journey.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$services = [
    [
        'icon' => '📋',
        'title' => 'Expert Scholarship Advice',
        'description' => 'One-on-one consultation with experienced advisors who will guide you through the entire scholarship process, from choosing the right programs to submitting winning applications.',
    ],
    [
        'icon' => '📝',
        'title' => 'Document Review',
        'description' => 'Our team will review your transcripts, certificates, and supporting documents to ensure they meet the requirements of your chosen universities and scholarship programs.',
    ],
    [
        'icon' => '✍️',
        'title' => 'Essay & Personal Statement Help',
        'description' => 'Get professional feedback on your scholarship essays and personal statements. We help you craft compelling narratives that stand out from thousands of applicants.',
    ],
    [
        'icon' => '🎤',
        'title' => 'Interview Preparation',
        'description' => 'Mock interviews and coaching sessions to prepare you for scholarship interviews. Learn how to present yourself confidently and answer common questions effectively.',
    ],
    [
        'icon' => '🌍',
        'title' => 'Visa Application Support',
        'description' => 'Step-by-step guidance on student visa applications for your destination country. We help you prepare all required documents and avoid common mistakes.',
    ],
    [
        'icon' => '🏠',
        'title' => 'Accommodation Assistance',
        'description' => 'Help finding safe and affordable housing near your university. We connect you with verified accommodations and provide tips for settling into your new city.',
    ],
];
?>

<section class="hero">
    <div class="container">
        <h1>Our Services</h1>
        <p>Everything you need to secure your scholarship and start your academic journey abroad.</p>
    </div>
</section>

<section class="container">
    <h2 class="section-title">How We Help You Succeed</h2>
    <div class="services-grid">
        <?php foreach ($services as $service): ?>
            <div class="service-card">
                <div class="service-icon"><?php echo $service['icon']; ?></div>
                <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                <p><?php echo htmlspecialchars($service['description']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container">
    <div class="cta-box">
        <h2>Ready to Get Started?</h2>
        <p>Book a free consultation with one of our scholarship advisors today and take the first step toward your dream education.</p>
        <a href="contact.php" class="btn">Contact Us</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
