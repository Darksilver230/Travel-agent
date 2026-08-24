<?php
/**
 * header.php
 * ----------
 * Reusable top-of-page HTML: <head>, nav bar, etc.
 * Every page includes this with: <?php include 'includes/header.php'; ?>
 * so we don't repeat the same HTML on every file.
 *
 * NOTE: any page that includes this header must already have
 * required 'includes/auth.php' (which calls session_start()) BEFORE
 * any HTML is echoed. session_start() has to run before output.
 */
$__user = function_exists('current_user') && isset($pdo) ? current_user($pdo) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLOWOLUX travel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="logo">OLOWOLUX<span>Travel</span></a>
        <nav>
            <a href="index.php">Home</a>
            <a href="destinations.php">Destinations</a>
            <a href="index.php#contact">Contact</a>
            <?php if ($__user): ?>
                <a href="my_bookings.php">My Bookings</a>
                <span class="nav-user">YO, <?php echo htmlspecialchars(explode(' ', $__user['full_name'])[0]); ?></span>
                <a href="logout.php">Log Out</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
                <a href="register.php" class="nav-cta">Sign Up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
