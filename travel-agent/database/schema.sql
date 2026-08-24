-- =========================================================
-- Travel Agent Website - Database Schema
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
-- Table: destinations
-- One row = one place you can travel to (e.g. "Paris, France")
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,          -- e.g. "Paris"
    country VARCHAR(100) NOT NULL,       -- e.g. "France"
    description TEXT,
    image_url VARCHAR(255),              -- path/URL to a photo
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: packages
-- One row = one bookable trip/package tied to a destination
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,         -- e.g. "5-Day Paris Getaway"
    description TEXT,
    price DECIMAL(10,2) NOT NULL,        -- price per person
    duration_days INT NOT NULL,
    max_travelers INT DEFAULT 10,
    image_url VARCHAR(255),
    available_from DATE,
    available_to DATE,
    is_active TINYINT(1) DEFAULT 1,      -- 1 = shown on site, 0 = hidden
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Table: users
-- One row = one registered account. Passwords are stored as
-- a HASH (via PHP's password_hash()), never as plain text —
-- there is no way to "look up" a user's real password, only
-- to check whether a submitted password matches the hash.
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
-- Table: bookings
-- One row = one booking of a package by a user.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id INT NOT NULL,
    user_id INT NOT NULL,
    num_travelers INT NOT NULL DEFAULT 1,
    travel_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    special_requests TEXT,

    -- Payment tracking. IMPORTANT: we never store card numbers, CVVs,
    -- or expiry dates here — see payment.php / process_payment.php
    -- for why. This table only ever stores a *reference* to a
    -- payment (a Stripe transaction ID, or a bank transfer reference
    -- the customer typed in), never the card itself.
    payment_method ENUM('card','bank_transfer') DEFAULT NULL,
    payment_status ENUM('unpaid','pending_verification','paid') DEFAULT 'unpaid',
    payment_reference VARCHAR(255) DEFAULT NULL,  -- Stripe payment_intent id, or bank transfer ref
    paid_at TIMESTAMP NULL DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =========================================================
-- Sample data so you have something to see immediately
-- =========================================================

INSERT INTO destinations (name, country, description, image_url) VALUES
('Paris', 'France', 'The City of Light — iconic landmarks, world-class food, and art.', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800'),
('Bali', 'Indonesia', 'Tropical beaches, rice terraces, and vibrant culture.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800'),
('Cape Town', 'South Africa', 'Stunning coastline, Table Mountain, and safari access.', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800'),
('Dubai', 'UAE', 'Ultra-modern skyline, desert adventures, and luxury shopping.', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800'),
('Tokyo', 'Japan', 'Neon skylines, ancient temples, and world-class cuisine.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800'),
('Rome', 'Italy', 'Ancient ruins, Renaissance art, and unforgettable pasta.', 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800'),
('Santorini', 'Greece', 'Whitewashed cliffside villages and legendary sunsets.', 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800'),
('Bangkok', 'Thailand', 'Vibrant street markets, ornate temples, and lively nightlife.', 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800'),
('Machu Picchu', 'Peru', 'Iconic Inca ruins high in the Andes mountains.', 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800'),
('Cairo', 'Egypt', 'The Great Pyramids, the Nile, and millennia of history.', 'https://images.unsplash.com/photo-1568322445389-f64ac2515020?w=800'),
('New York City', 'USA', 'Iconic skyline, Broadway shows, and world-class museums.', 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800'),
('Sydney', 'Australia', 'Harbour views, beaches, and the iconic Opera House.', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800');

INSERT INTO packages (destination_id, title, description, price, duration_days, max_travelers, image_url, available_from, available_to) VALUES
(1, '5-Day Paris Getaway', 'Eiffel Tower, Louvre, Seine river cruise, and guided walking tours.', 1200.00, 5, 12, 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', '2026-09-01', '2027-06-30'),
(2, '7-Day Bali Escape', 'Beach resorts, temple visits, and a sunrise volcano trek.', 1450.00, 7, 15, 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800', '2026-09-01', '2027-06-30'),
(3, '6-Day Cape Town Safari & City', 'Big five safari plus Table Mountain and wine country.', 2100.00, 6, 10, 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', '2026-09-01', '2027-06-30'),
(4, '4-Day Dubai Luxury Weekend', 'Desert safari, Burj Khalifa, and shopping festival.', 1600.00, 4, 20, 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', '2026-09-01', '2027-06-30'),
(5, '6-Day Tokyo Discovery', 'Shibuya, Mt. Fuji day trip, sushi-making class, and temple tours.', 1850.00, 6, 14, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '2026-09-01', '2027-06-30'),
(6, '5-Day Roman Holiday', 'Colosseum, Vatican Museums, and a Tuscan day trip with wine tasting.', 1350.00, 5, 16, 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800', '2026-09-01', '2027-06-30'),
(7, '4-Day Santorini Sunset Escape', 'Cliffside villas, catamaran cruise, and volcanic wine tours.', 1950.00, 4, 8, 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800', '2026-09-01', '2027-06-30'),
(8, '6-Day Bangkok & Beyond', 'Grand Palace, floating markets, and a day trip to Ayutthaya.', 1100.00, 6, 18, 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800', '2026-09-01', '2027-06-30'),
(9, '8-Day Peru Andes Trek', 'Cusco acclimatization, Sacred Valley, and guided Machu Picchu trek.', 2400.00, 8, 10, 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800', '2026-09-01', '2027-06-30'),
(10, '5-Day Cairo & Nile Explorer', 'Pyramids of Giza, the Egyptian Museum, and a Nile felucca sail.', 1500.00, 5, 12, 'https://images.unsplash.com/photo-1568322445389-f64ac2515020?w=800', '2026-09-01', '2027-06-30'),
(11, '4-Day New York City Break', 'Broadway show, Statue of Liberty cruise, and top museums.', 1300.00, 4, 20, 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800', '2026-09-01', '2027-06-30'),
(12, '7-Day Sydney & Coast', 'Opera House tour, Bondi Beach, and a Blue Mountains day trip.', 2050.00, 7, 14, 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800', '2026-09-01', '2027-06-30');
