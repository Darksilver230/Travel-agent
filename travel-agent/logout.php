<?php
/**
 * logout.php
 * ----------
 * Destroys the session (logs the user out) and redirects home.
 */
require 'includes/auth.php';

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
