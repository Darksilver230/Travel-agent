-- =========================================================
-- International Scholarships Platform - Database Schema
-- =========================================================
-- HOW TO USE:
-- 1. Open phpMyAdmin (comes with XAMPP/MAMP) or the mysql CLI
-- 2. Create a database called `travel_agent`
-- 3. Import/run this whole file against that database
--    (phpMyAdmin: click the DB, go to "Import", choose this file)
-- =========================================================

CREATE DATABASE IF NOT EXISTS travel_agent;
USE travel_agent;

-- ---------------------------------------------------------
-- Table: universities
-- One row = one university/institution offering scholarships
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS universities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,          -- e.g. "University of Oxford"
    country VARCHAR(100) NOT NULL,       -- e.g. "United Kingdom"
    description TEXT,
    image_url VARCHAR(255),              -- path/URL to a photo
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: scholarships
-- One row = one scholarship opportunity tied to a university
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS scholarships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    university_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,         -- e.g. "Full-Ride Merit Scholarship"
    description TEXT,
    amount DECIMAL(10,2) NOT NULL,       -- scholarship amount (or application fee if applicable)
    duration_months INT NOT NULL,        -- duration of the program/scholarship
    max_applicants INT DEFAULT 10,       -- maximum number of applicants accepted
    image_url VARCHAR(255),
    deadline_from DATE,
    deadline_to DATE,
    is_active TINYINT(1) DEFAULT 1,      -- 1 = shown on site, 0 = hidden
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Table: users
-- One row = one registered account. Passwords are stored as
-- a HASH (via PHP's password_hash()), never as plain text.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: applications
-- One row = one application for a scholarship by a user.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scholarship_id INT NOT NULL,
    user_id INT NOT NULL,
    num_applicants INT NOT NULL DEFAULT 1,
    deadline_date DATE NOT NULL,
    total_fee DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    special_requests TEXT,

    -- Payment tracking. We never store card numbers, CVVs,
    -- or expiry dates here. This table only stores a *reference*
    -- to a payment (a Stripe transaction ID, or a bank transfer
    -- reference the applicant typed in), never the card itself.
    payment_method ENUM('card','bank_transfer') DEFAULT NULL,
    payment_status ENUM('unpaid','pending_verification','paid') DEFAULT 'unpaid',
    payment_reference VARCHAR(255) DEFAULT NULL,
    paid_at TIMESTAMP NULL DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =========================================================
-- Sample data so you have something to see immediately
-- =========================================================

INSERT INTO universities (name, country, description, image_url) VALUES
('University of Oxford', 'United Kingdom', 'One of the oldest and most prestigious universities in the world, offering world-class education.', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800'),
('Massachusetts Institute of Technology', 'USA', 'Leading institution in science, engineering, and technology with generous financial aid.', 'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=800'),
('University of Toronto', 'Canada', 'Canada top research university with a wide range of scholarships for international students.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800'),
('ETH Zurich', 'Switzerland', 'World-renowned university for science and technology, offering fully-funded scholarships.', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800'),
('University of Melbourne', 'Australia', 'Leading Australian university with generous international scholarship programs.', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800'),
('Seoul National University', 'South Korea', 'Top-ranked Korean university offering the Global Korea Scholarship program.', 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800'),
('University of Cape Town', 'South Africa', 'Africa leading research university with diverse scholarship opportunities.', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800'),
('Technical University of Munich', 'Germany', 'Germany top technical university with tuition-free education and scholarship support.', 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800'),
('University of Tokyo', 'Japan', 'Japan premier university offering the MEXT scholarship for international students.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800'),
('Sorbonne University', 'France', 'Historic Parisian university with excellent programs and international partnerships.', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800'),
('National University of Singapore', 'Singapore', 'Asia top university with competitive scholarships for global talent.', 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800'),
('University of Edinburgh', 'United Kingdom', 'Scottish university with a rich heritage and strong international student support.', 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800');

INSERT INTO scholarships (university_id, title, description, amount, duration_months, max_applicants, image_url, deadline_from, deadline_to) VALUES
(1, 'Oxford Clarendon Fund Scholarship', 'Fully-funded scholarship covering tuition, living expenses, and travel for graduate students.', 45000.00, 24, 50, 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', '2026-09-01', '2027-06-30'),
(2, 'MIT International Science Scholars', 'Merit-based scholarship for outstanding international students in STEM fields.', 52000.00, 48, 30, 'https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=800', '2026-09-01', '2027-06-30'),
(3, 'UofT Lester B. Pearson Scholarship', 'Full scholarship for international students demonstrating exceptional academic achievement.', 40000.00, 48, 40, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '2026-09-01', '2027-06-30'),
(4, 'ETH Excellence Scholarship', 'Partial to full tuition coverage for excellent master students at ETH Zurich.', 35000.00, 24, 60, 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', '2026-09-01', '2027-06-30'),
(5, 'Melbourne International Research Scholarship', 'Fully-funded PhD scholarship with stipend and tuition fee remission.', 38000.00, 36, 25, 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800', '2026-09-01', '2027-06-30'),
(6, 'GKS Korean Government Scholarship', 'Full scholarship including tuition, airfare, monthly allowance, and settlement support.', 25000.00, 48, 100, 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800', '2026-09-01', '2027-06-30'),
(7, 'UCT International Postgraduate Scholarship', 'Partial tuition scholarship for high-achieving international postgraduate students.', 20000.00, 24, 35, 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', '2026-09-01', '2027-06-30'),
(8, 'TUM Scholarship for International Students', 'Merit-based tuition reduction and stipend for international degree-seeking students.', 15000.00, 36, 80, 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800', '2026-09-01', '2027-06-30'),
(9, 'MEXT Japanese Government Scholarship', 'Fully-funded scholarship from the Japanese government for undergraduate and graduate students.', 30000.00, 48, 150, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '2026-09-01', '2027-06-30'),
(10, 'Sorbonne International Excellence Award', 'Tuition waiver plus monthly stipend for outstanding international applicants.', 22000.00, 24, 45, 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', '2026-09-01', '2027-06-30'),
(11, 'NUS Global Merit Scholarship', 'Full tuition and living allowance for exceptional international undergraduates.', 42000.00, 48, 30, 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800', '2026-09-01', '2027-06-30'),
(12, 'Edinburgh Global Research Scholarship', 'Full PhD scholarship covering tuition and providing a generous living stipend.', 36000.00, 36, 20, 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800', '2026-09-01', '2027-06-30');
