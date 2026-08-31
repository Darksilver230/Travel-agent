<?php
/**
 * services.php
 * -------------
 * Shows the travel and advisory services we offer.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$services = [
    [
        'icon' => '📋',
        'title' => 'Personalized Itineraries',
        'description' => 'We take the time to understand your interests and preferences to create a travel experience that\'s uniquely yours.',
    ],
    [
        'icon' => '💡',
        'title' => 'Expert Advice',
        'description' => 'Our travel consultants are well-versed in a multitude of destinations and are always up to date with the latest travel trends, ensuring you receive the best recommendations.',
    ],
    [
        'icon' => '🌍',
        'title' => 'Comprehensive Services',
        'description' => 'From visa application to flights and accommodations down to excursions and local experiences, we handle all aspects of your travel plans, leaving you free to enjoy your adventures.',
    ],
    [
        'icon' => '📞',
        'title' => '24/7 Support',
        'description' => 'Travel is unpredictable, and we are here for you at any time, providing support and solutions whenever you need them.',
    ],
];
?>

<section class="hero">
    <div class="container">
        <h1>Our Services</h1>
        <p>Everything you need to plan and enjoy a seamless travel experience.</p>
    </div>
</section>

<section class="container">
    <h2 class="section-title">How We Help You Travel Better</h2>
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
        <p>Reach out to us today and let us help you plan your next unforgettable adventure.</p>
        <a href="contact.php" class="btn">Contact Us</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
