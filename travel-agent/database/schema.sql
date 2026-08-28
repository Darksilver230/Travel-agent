-- =========================================================
-- OLOWO Corp - Scholarships & Travel Platform - Database Schema
-- =========================================================
-- HOW TO USE:
-- 1. Open phpMyAdmin (comes with XAMPP/MAMP) or the mysql CLI
-- 2. Create a database called `travel_agent`
-- 3. Import/run this whole file against that database
-- =========================================================

CREATE DATABASE IF NOT EXISTS travel_agent;
USE travel_agent;

-- =========================================================
-- SCHOLARSHIPS TABLES
-- =========================================================

CREATE TABLE IF NOT EXISTS universities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    country VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
);

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
);

-- =========================================================
-- TRAVEL TABLES
-- =========================================================

CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
);

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
);

-- =========================================================
-- USERS TABLE (shared)
-- =========================================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================================
-- Sample Data: Scholarships
-- =========================================================

INSERT INTO universities (name, country, description, image_url) VALUES
('University of Oxford', 'United Kingdom', 'One of the oldest and most prestigious universities in the world.', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800'),
('Massachusetts Institute of Technology', 'USA', 'Leading institution in science, engineering, and technology.', 'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=800'),
('University of Toronto', 'Canada', 'Canada top research university with generous scholarships.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800'),
('ETH Zurich', 'Switzerland', 'World-renowned university for science and technology.', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800'),
('University of Melbourne', 'Australia', 'Leading Australian university with generous scholarship programs.', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800'),
('Seoul National University', 'South Korea', 'Top-ranked Korean university offering the Global Korea Scholarship.', 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800'),
('Technical University of Munich', 'Germany', 'Germany top technical university with tuition-free education.', 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800'),
('University of Tokyo', 'Japan', 'Japan premier university offering the MEXT scholarship.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800'),
('Sorbonne University', 'France', 'Historic Parisian university with excellent programs.', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800'),
('National University of Singapore', 'Singapore', 'Asia top university with competitive scholarships.', 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800');

INSERT INTO scholarships (university_id, title, description, amount, duration_months, max_applicants, image_url, deadline_from, deadline_to) VALUES
(1, 'Oxford Clarendon Fund Scholarship', 'Fully-funded scholarship covering tuition, living expenses, and travel for graduate students.', 45000.00, 24, 50, 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', '2026-09-01', '2027-06-30'),
(2, 'MIT International Science Scholars', 'Merit-based scholarship for outstanding international students in STEM.', 52000.00, 48, 30, 'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=800', '2026-09-01', '2027-06-30'),
(3, 'UofT Lester B. Pearson Scholarship', 'Full scholarship for international students demonstrating exceptional achievement.', 40000.00, 48, 40, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '2026-09-01', '2027-06-30'),
(4, 'ETH Excellence Scholarship', 'Partial to full tuition coverage for excellent master students at ETH Zurich.', 35000.00, 24, 60, 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', '2026-09-01', '2027-06-30'),
(5, 'Melbourne International Research Scholarship', 'Fully-funded PhD scholarship with stipend and tuition fee remission.', 38000.00, 36, 25, 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800', '2026-09-01', '2027-06-30'),
(6, 'GKS Korean Government Scholarship', 'Full scholarship including tuition, airfare, and monthly allowance.', 25000.00, 48, 100, 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800', '2026-09-01', '2027-06-30'),
(7, 'TUM Scholarship for International Students', 'Merit-based tuition reduction and stipend for international students.', 15000.00, 36, 80, 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800', '2026-09-01', '2027-06-30'),
(8, 'MEXT Japanese Government Scholarship', 'Fully-funded scholarship from the Japanese government.', 30000.00, 48, 150, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '2026-09-01', '2027-06-30'),
(9, 'Sorbonne International Excellence Award', 'Tuition waiver plus monthly stipend for outstanding applicants.', 22000.00, 24, 45, 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', '2026-09-01', '2027-06-30'),
(10, 'NUS Global Merit Scholarship', 'Full tuition and living allowance for exceptional international undergraduates.', 42000.00, 48, 30, 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800', '2026-09-01', '2027-06-30');

-- =========================================================
-- Sample Data: Travel
-- =========================================================

INSERT INTO destinations (name, country, description, image_url) VALUES
('Paris', 'France', 'The City of Light — iconic landmarks, world-class food, and art.', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800'),
('Bali', 'Indonesia', 'Tropical beaches, rice terraces, and vibrant culture.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800'),
('Cape Town', 'South Africa', 'Stunning coastline, Table Mountain, and safari access.', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800'),
('Dubai', 'UAE', 'Ultra-modern skyline, desert adventures, and luxury shopping.', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800'),
('Tokyo', 'Japan', 'Neon skylines, ancient temples, and world-class cuisine.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800'),
('Rome', 'Italy', 'Ancient ruins, Renaissance art, and unforgettable pasta.', 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800'),
('Santorini', 'Greece', 'Whitewashed cliffside villages and legendary sunsets.', 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800'),
('Bangkok', 'Thailand', 'Vibrant street markets, ornate temples, and lively nightlife.', 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800');

INSERT INTO packages (destination_id, title, description, price, duration_days, max_travelers, image_url, available_from, available_to) VALUES
(1, '5-Day Paris Getaway', 'Eiffel Tower, Louvre, Seine river cruise, and guided walking tours.', 1200.00, 5, 12, 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', '2026-09-01', '2027-06-30'),
(2, '7-Day Bali Escape', 'Beach resorts, temple visits, and a sunrise volcano trek.', 1450.00, 7, 15, 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800', '2026-09-01', '2027-06-30'),
(3, '6-Day Cape Town Safari & City', 'Big five safari plus Table Mountain and wine country.', 2100.00, 6, 10, 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', '2026-09-01', '2027-06-30'),
(4, '4-Day Dubai Luxury Weekend', 'Desert safari, Burj Khalifa, and shopping festival.', 1600.00, 4, 20, 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', '2026-09-01', '2027-06-30'),
(5, '6-Day Tokyo Discovery', 'Shibuya, Mt. Fuji day trip, sushi-making class, and temple tours.', 1850.00, 6, 14, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '2026-09-01', '2027-06-30'),
(6, '5-Day Roman Holiday', 'Colosseum, Vatican Museums, and a Tuscan day trip.', 1350.00, 5, 16, 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800', '2026-09-01', '2027-06-30'),
(7, '4-Day Santorini Sunset Escape', 'Cliffside villas, catamaran cruise, and volcanic wine tours.', 1950.00, 4, 8, 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800', '2026-09-01', '2027-06-30'),
(8, '6-Day Bangkok & Beyond', 'Grand Palace, floating markets, and a day trip to Ayutthaya.', 1100.00, 6, 18, 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800', '2026-09-01', '2027-06-30');
