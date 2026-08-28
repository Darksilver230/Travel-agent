<?php
/**
 * db.php
 * ------
 * Connects to MySQL and ensures all tables exist.
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

    // Users table (shared)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            phone VARCHAR(30),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Scholarship tables
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

    // Travel tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS destinations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            country VARCHAR(100) NOT NULL,
            description TEXT,
            image_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS packages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            destination_id INT NOT NULL,
            title VARCHAR(150) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            duration_days INT NOT NULL,
            max_travelers INT DEFAULT 10,
            image_url VARCHAR(255),
            available_from DATE,
            available_to DATE,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            package_id INT NOT NULL,
            user_id INT NOT NULL,
            num_travelers INT NOT NULL DEFAULT 1,
            travel_date DATE NOT NULL,
            total_price DECIMAL(10,2) NOT NULL,
            status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
            special_requests TEXT,
            payment_method ENUM('card','bank_transfer') DEFAULT NULL,
            payment_status ENUM('unpaid','pending_verification','paid') DEFAULT 'unpaid',
            payment_reference VARCHAR(255) DEFAULT NULL,
            paid_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (package_id) REFERENCES packages(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");

    // Seed sample data so a fresh install isn't blank. Skips if the
    // tables already have content (e.g. after schema.sql import).
    $uniCount = (int)$pdo->query("SELECT COUNT(*) FROM universities")->fetchColumn();
    if ($uniCount === 0) {
        $pdo->exec("
            INSERT INTO universities (name, country, description, image_url) VALUES
            ('University of Oxford', 'United Kingdom', 'One of the oldest and most prestigious universities in the world.', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800'),
            ('MIT', 'USA', 'Leading institution in science, engineering, and technology.', 'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=800'),
            ('University of Toronto', 'Canada', 'Canada top research university with generous scholarships.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800'),
            ('University of Melbourne', 'Australia', 'Leading Australian university with generous scholarship programs.', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800')
        ");
        $pdo->exec("
            INSERT INTO scholarships (university_id, title, description, amount, duration_months, max_applicants, image_url, deadline_from, deadline_to) VALUES
            (1, 'Oxford Clarendon Fund Scholarship', 'Fully-funded scholarship covering tuition, living expenses, and travel for graduate students.', 45000.00, 24, 50, 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', '2026-09-01', '2027-06-30'),
            (2, 'MIT International Science Scholars', 'Merit-based scholarship for outstanding international students in STEM.', 52000.00, 48, 30, 'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=800', '2026-09-01', '2027-06-30'),
            (3, 'UofT Lester B. Pearson Scholarship', 'Full scholarship for international students demonstrating exceptional achievement.', 40000.00, 48, 40, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '2026-09-01', '2027-06-30')
        ");
    }

    $destCount = (int)$pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
    if ($destCount === 0) {
        $pdo->exec("
            INSERT INTO destinations (name, country, description, image_url) VALUES
            ('Paris', 'France', 'The City of Light — iconic landmarks, world-class food, and art.', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800'),
            ('Bali', 'Indonesia', 'Tropical beaches, rice terraces, and vibrant culture.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800'),
            ('Tokyo', 'Japan', 'Neon skylines, ancient temples, and world-class cuisine.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800')
        ");
        $pdo->exec("
            INSERT INTO packages (destination_id, title, description, price, duration_days, max_travelers, image_url, available_from, available_to) VALUES
            (1, '5-Day Paris Getaway', 'Eiffel Tower, Louvre, Seine river cruise, and guided walking tours.', 1200.00, 5, 12, 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', '2026-09-01', '2027-06-30'),
            (2, '7-Day Bali Escape', 'Beach resorts, temple visits, and a sunrise volcano trek.', 1450.00, 7, 15, 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800', '2026-09-01', '2027-06-30'),
            (3, '6-Day Tokyo Discovery', 'Shibuya, Mt. Fuji day trip, sushi-making class, and temple tours.', 1850.00, 6, 14, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '2026-09-01', '2027-06-30')
        ");
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
