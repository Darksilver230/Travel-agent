<?php
/**
 * header.php
 * ----------
 * Reusable top-of-page HTML: <head>, nav bar, etc.
 */
$__user = function_exists('current_user') && isset($pdo) ? current_user($pdo) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarships & Travel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="logo">OLOWO Corp</a>
        <button class="menu-toggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <nav class="main-nav">
            <a href="index.php">Home</a>
            <div class="nav-dropdown">
                <a href="services.php" class="nav-dropdown-toggle">Services <span class="dropdown-arrow">&#9662;</span></a>
                <div class="nav-dropdown-menu">
                    <a href="universities.php">Scholarships</a>
                    <a href="travel.php">Travel</a>
                    <a href="services.php">All Services</a>
                </div>
            </div>
            <a href="index.php#contact">Contact</a>
            <?php if ($__user): ?>
                <a href="my_bookings.php">My Bookings</a>
                <a href="logout.php">Log Out</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
                <a href="register.php" class="nav-cta">Sign Up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash-alert flash-<?php echo htmlspecialchars($_SESSION['flash_type'] ?? 'success'); ?>">
        <div class="container flash-inner">
            <span><?php echo $_SESSION['flash']; ?></span>
            <button class="flash-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    </div>
    <?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
<?php endif; ?>
