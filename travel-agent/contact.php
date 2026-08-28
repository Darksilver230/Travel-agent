<?php
/**
 * contact.php
 * ------------
 * Contact us page with contact details and a simple message form.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$sent = false;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flash = '';
    $type = 'success';
    if ($name === '' || $email === '' || $message === '') {
        $flash = 'Please fill in all fields.';
        $type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $flash = 'Please enter a valid email address.';
        $type = 'error';
    } else {
        $_SESSION['flash'] = 'Thank you! Your message has been received. We will get back to you soon.';
        $_SESSION['flash_type'] = 'success';
        header('Location: contact.php');
        exit;
    }
    $_SESSION['flash'] = $flash;
    $_SESSION['flash_type'] = $type;
}
?>

<section class="hero hero-travel">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Have a question about a scholarship or a travel plan? We are here to help.</p>
    </div>
</section>

<section class="contact-section" style="margin-top:0;">
    <div class="container">
        <h2 class="section-title">Get In Touch</h2>
        <p class="contact-text">
            Whether you need advice on scholarships, help planning a trip, or support with an
            application, our team is ready to assist. Reach out and we will respond as soon as we can.
        </p>
        <div class="contact-grid">
            <div class="contact-item">
                <span class="contact-icon">📧</span>
                <div>
                    <h4>Email</h4>
                    <p>support@olowocorp.com</p>
                </div>
            </div>
            <div class="contact-item">
                <span class="contact-icon">📞</span>
                <div>
                    <h4>Phone</h4>
                    <p>+1 (555) 000-0000</p>
                </div>
            </div>
            <div class="contact-item">
                <span class="contact-icon">📍</span>
                <div>
                    <h4>Office</h4>
                    <p>123 Education Avenue, Somewhere</p>
                </div>
            </div>
        </div>

        <div class="application-box">
            <h2>Send Us a Message</h2>
            <form action="contact.php" method="POST" class="application-form">
                <div class="field">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="field">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="field full-width">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                </div>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
