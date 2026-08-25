<?php
/**
 * db.php
 * ------
 * Connects to MySQL and ensures the scholarship tables exist.
 * If the old travel tables exist, they are preserved; the new
 * tables are created alongside them so nothing breaks.
 */

$DB_HOST = 'localhost';
$DB_NAME = 'travel_agent';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // Ensure the scholarship tables exist (idempotent — safe to run every time).
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS universities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            country VARCHAR(100) NOT NULL,
            description TEXT,
            image_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS scholarships (
            id INT AUTO_INCREMENT PRIMARY KEY,
            university_id INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            amount DECIMAL(10,2) NOT NULL,
            duration_months INT NOT NULL,
            max_applicants INT DEFAULT 10,
            image_url VARCHAR(255),
            deadline_from DATE,
            deadline_to DATE,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            scholarship_id INT NOT NULL,
            user_id INT NOT NULL,
            num_applicants INT NOT NULL DEFAULT 1,
            deadline_date DATE NOT NULL,
            total_fee DECIMAL(10,2) NOT NULL,
            status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
            special_requests TEXT,
            payment_method ENUM('card','bank_transfer') DEFAULT NULL,
            payment_status ENUM('unpaid','pending_verification','paid') DEFAULT 'unpaid',
            payment_reference VARCHAR(255) DEFAULT NULL,
            paid_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (scholarship_id) REFERENCES scholarships(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
