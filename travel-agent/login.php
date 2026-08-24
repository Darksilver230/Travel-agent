<?php
/**
 * login.php
 * ---------
 * Shows the login form. Actual login logic is in process_login.php.
 */
require 'includes/db.php';
require 'includes/auth.php';

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
        <h1>Log In</h1>

        <?php if ($error): ?>
            <p class="alert error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="process_login.php" method="POST" class="auth-form">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Log In</button>
        </form>

        <p class="auth-switch">Don't have an account? <a href="register.php?redirect=<?php echo urlencode($redirect); ?>">Create one</a></p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
