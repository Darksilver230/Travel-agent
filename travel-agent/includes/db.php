<?php
/**
 * db.php
 * ------
 * This file's ONLY job is to connect to the MySQL database and
 * hand back a $pdo object that every other PHP file can use to
 * run queries safely.
 *
 * WHY PDO?
 * PDO (PHP Data Objects) lets us use "prepared statements" —
 * these protect us from SQL injection attacks, which is the #1
 * security mistake beginners make when writing raw SQL with
 * variables plugged directly into the query string.
 *
 * EDIT THESE VALUES to match your local setup (XAMPP/MAMP defaults
 * are usually: host=localhost, user=root, password="" (empty)).
 */

$DB_HOST = 'localhost';
$DB_NAME = 'travel_agent';
$DB_USER = 'root';
$DB_PASS = '';        // set this if your MySQL root user has a password

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            // Turn MySQL errors into PHP exceptions (easier to debug)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Return rows as associative arrays, e.g. $row['name']
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // In production you'd log this instead of showing it to users.
    die("Database connection failed: " . $e->getMessage());
}
