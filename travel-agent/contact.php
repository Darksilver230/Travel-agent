<?php
/**
 * contact.php
 * ------------
 * Contact us page with contact details and a travel request form.
 * Submissions are validated and saved to the `contact_submissions` table.
 */
require 'includes/db.php';
require 'includes/auth.php';

$errors = [];
$old = [
    'full_name'     => '',
    'email'         => '',
    'destination'   => '',
    'travel_date'   => '',
    'num_travelers' => '1',
    'travel_type'   => 'vacation',
    'message'       => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['full_name']     = trim($_POST['full_name'] ?? '');
    $old['email']         = trim($_POST['email'] ?? '');
    $old['destination']   = trim($_POST['destination'] ?? '');
    $old['travel_date']   = trim($_POST['travel_date'] ?? '');
    $old['num_travelers'] = trim($_POST['num_travelers'] ?? '');
    $old['travel_type']   = trim($_POST['travel_type'] ?? '');
    $old['message']       = trim($_POST['message'] ?? '');

    $travelTypes = ['vacation', 'business', 'study', 'family', 'other'];

    if ($old['full_name'] === '') {
        $errors[] = 'Please enter your full name.';
    }
    if ($old['email'] === '') {
        $errors[] = 'Please enter your email address.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($old['destination'] === '') {
        $errors[] = 'Please enter your destination.';
    }
    if ($old['travel_date'] !== '' && DateTime::createFromFormat('Y-m-d', $old['travel_date']) === false) {
        $errors[] = 'Please enter a valid travel date.';
    }
    if ($old['num_travelers'] === '' || (int)$old['num_travelers'] < 1) {
        $errors[] = 'Please enter the number of travelers (at least 1).';
    }
    if (!in_array($old['travel_type'], $travelTypes, true)) {
        $old['travel_type'] = 'vacation';
    }
    if ($old['message'] === '') {
        $errors[] = 'Please enter your message or special requests.';
    }

    if ($errors === []) {
        $stmt = $pdo->prepare("
            INSERT INTO contact_submissions
                (full_name, email, destination, travel_date, num_travelers, travel_type, message)
            VALUES
                (:full_name, :email, :destination, :travel_date, :num_travelers, :travel_type, :message)
        ");
        $stmt->execute([
            ':full_name'     => $old['full_name'],
            ':email'         => $old['email'],
            ':destination'   => $old['destination'],
            ':travel_date'   => $old['travel_date'] !== '' ? $old['travel_date'] : null,
            ':num_travelers' => (int)$old['num_travelers'],
            ':travel_type'   => $old['travel_type'],
            ':message'       => $old['message'],
        ]);

        $_SESSION['flash'] = 'Thank you! Your travel request has been received. We will get back to you soon.';
        $_SESSION['flash_type'] = 'success';
        header('Location: contact.php');
        exit;
    }

    $_SESSION['flash'] = implode(' ', $errors);
    $_SESSION['flash_type'] = 'error';
}

include 'includes/header.php';
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
            <h2>Request a Travel Quote</h2>
            <form action="contact.php" method="POST" class="application-form" novalidate>
                <div class="field">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($old['full_name']); ?>" required>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($old['email']); ?>" required>
                </div>
                <div class="field">
                    <label for="destination">Destination</label>
                    <input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($old['destination']); ?>" placeholder="e.g. Paris, Japan, Bali">
                </div>
                <div class="field">
                    <label for="travel_date">Preferred Travel Date</label>
                    <input type="date" id="travel_date" name="travel_date" value="<?php echo htmlspecialchars($old['travel_date']); ?>">
                </div>
                <div class="field">
                    <label for="num_travelers">Number of Travelers</label>
                    <input type="number" id="num_travelers" name="num_travelers" min="1" value="<?php echo htmlspecialchars($old['num_travelers']); ?>">
                </div>
                <div class="field">
                    <label for="travel_type">Travel Type</label>
                    <select id="travel_type" name="travel_type">
                        <option value="vacation" <?php echo $old['travel_type'] === 'vacation' ? 'selected' : ''; ?>>Vacation</option>
                        <option value="business" <?php echo $old['travel_type'] === 'business' ? 'selected' : ''; ?>>Business</option>
                        <option value="study" <?php echo $old['travel_type'] === 'study' ? 'selected' : ''; ?>>Study</option>
                        <option value="family" <?php echo $old['travel_type'] === 'family' ? 'selected' : ''; ?>>Family</option>
                        <option value="other" <?php echo $old['travel_type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="field full-width">
                    <label for="message">Your Message / Special Requests</label>
                    <textarea id="message" name="message" rows="5"><?php echo htmlspecialchars($old['message']); ?></textarea>
                </div>
                <button type="submit">Send Request</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
