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
    <title>OLOWOLUX Scholarships</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="logo">OLOWOLUX<span>Scholarships</span></a>
        <nav>
            <a href="index.php">Home</a>
            <a href="universities.php">Universities</a>
            <a href="index.php#contact">Contact</a>
            <?php if ($__user): ?>
                <a href="my_applications.php">My Applications</a>
                <span class="nav-user">Hi, <?php echo htmlspecialchars(explode(' ', $__user['full_name'])[0]); ?></span>
                <a href="logout.php">Log Out</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
                <a href="register.php" class="nav-cta">Sign Up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
