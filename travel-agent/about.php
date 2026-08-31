<?php
/**
 * about.php
 * ----------
 * About Us page: our story, mission, and team.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1>About Us</h1>
        <p>OLOWO Corp — crafting personalized travel experiences that enrich your soul.</p>
    </div>
</section>

<section class="container">
    <div class="about-title-row">
        <h2 class="section-title">Who We Are</h2>
    </div>
    <p class="contact-text">
        At OLOWO Corp, we believe that travel is not just a journey; it's an experience
        that enriches the soul. Established in year 2021, our mission is to inspire wanderlust,
        create memorable adventures, and provide personalized travel experiences that cater to
        every traveler's dream.
    </p>
    <p class="contact-text">
        We are a passionate team of travel enthusiasts, explorers, and adventure seekers,
        dedicated to crafting unique travel itineraries for our clients. Our diverse backgrounds
        and wealth of experience in the travel industry allow us to curate the best trips, whether
        you're seeking exhilarating adventures, relaxing retreats, cultural immersions, family
        vacations, or relocation.
    </p>
    <p class="contact-text">
        Our team is committed to understanding your travel preferences and desires, ensuring that
        every trip we plan is tailored just for you. We are here to guide you every step of the
        way, from selecting the perfect destination to finalizing your travel plans.
    </p>
</section>

<section class="container">
    <h2 class="section-title">Meet the Team</h2>
    <div class="team-grid">
        <div class="team-members">
            <div class="team-card">
                <div class="team-photo">
                    <img src="img/founder.jpg" alt="Founder" onerror="this.style.display='none'">
                    <span class="team-photo-fallback">F</span>
                </div>
                <h3>Founder &amp; CEO</h3>
                <p class="muted">Harry Kenny</p>
                <p>Visionary behind OLOWO Corp, dedicated to turning travel dreams into reality.</p>
            </div>
            <div class="team-card">
                <div class="team-photo">
                    <img src="img/staff1.jpg" alt="Staff" onerror="this.style.display='none'">
                    <span class="team-photo-fallback">S</span>
                </div>
                <h3>Travel Consultant</h3>
                <p class="muted">Team Member</p>
                <p>Expert in crafting personalized itineraries and seamless travel experiences.</p>
            </div>
            <div class="team-card">
                <div class="team-photo">
                    <img src="img/staff2.jpg" alt="Staff" onerror="this.style.display='none'">
                    <span class="team-photo-fallback">S</span>
                </div>
                <h3>Client Relations</h3>
                <p class="muted">Team Member</p>
                <p>Ensures every client is guided and supported at every stage of their journey.</p>
            </div>
        </div>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Our Values</h2>
    <p class="contact-text">At OLOWO Corp and Tour we prioritize:</p>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🤝</div>
            <div>
                <h3>Integrity</h3>
                <p>We believe in transparency and honesty in all our dealings with clients and partners.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🌿</div>
            <div>
                <h3>Sustainability</h3>
                <p>We are committed to promoting eco-friendly travel options and supporting local communities in the destinations we serve.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">❤️</div>
            <div>
                <h3>Passion for Travel</h3>
                <p>Our love for exploration drives us to create unforgettable travel experiences that ignite your wanderlust.</p>
            </div>
        </div>
    </div>
</section>

<section class="contact-section" style="margin-top:0;">
    <div class="container">
        <h2 class="section-title">Our Mission</h2>
        <p class="contact-text">
            To make world-class education and unforgettable travel accessible, affordable, and
            achievable for everyone — one student and one traveler at a time.
        </p>
        <div class="stats-bar" style="margin-top:-20px;">
            <div class="container stats-inner">
                <div class="stat">
                    <span class="stat-number">10+</span>
                    <span class="stat-label">Years of Experience</span>
                </div>
                <div class="stat">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Partner Universities</span>
                </div>
                <div class="stat">
                    <span class="stat-number">20+</span>
                    <span class="stat-label">Destinations</span>
                </div>
                <div class="stat">
                    <span class="stat-number">2,000+</span>
                    <span class="stat-label">Happy Clients</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container">
    <div class="cta-box">
        <h2>Ready to Start Your Journey?</h2>
        <p>Talk to our team today and let us help you find the right scholarship or plan your next adventure.</p>
        <a href="contact.php" class="btn">Contact Us</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
