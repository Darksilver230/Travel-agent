<?php
/**
 * register.php
 * ------------
 * Shows the "create account" form. The actual signup logic lives in
 * process_register.php, which this form POSTs to.
 */
require 'includes/db.php';
require 'includes/auth.php';

// If already logged in, no need to register again.
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = $_GET['error'] ?? null;
$redirect = $_GET['redirect'] ?? 'index.php';

include 'includes/header.php';
?>

<section class="container auth-page">
    <div class="auth-box">
        <h1>Create an Account</h1>
        <p class="muted">You'll need an account to apply for scholarships and manage payments.</p>

        <?php if ($error): ?>
            <p class="alert error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="process_register.php" method="POST" class="auth-form">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">

            <div class="field">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="field">
                <label for="phone">Phone (optional)</label>
                <input type="tel" id="phone" name="phone">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="8" required>
            </div>
            <div class="field">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" minlength="8" required>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <p class="auth-switch">Already have an account? <a href="login.php?redirect=<?php echo urlencode($redirect); ?>">Log in</a></p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
