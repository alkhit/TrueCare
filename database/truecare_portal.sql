-- Complete database reset with working passwords
DROP DATABASE IF EXISTS truecare_portal;
CREATE DATABASE truecare_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE truecare_portal;

-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('donor', 'orphanage', 'admin') NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Orphanages table
CREATE TABLE orphanages (
    orphanage_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(200) NOT NULL,
    location VARCHAR(200),
    registration_number VARCHAR(50) UNIQUE,
    description TEXT,
    contact_info VARCHAR(100),
    image_url VARCHAR(255),
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_user_id (user_id)
);

-- Insert users with VERIFIED WORKING PASSWORDS
INSERT INTO users (name, email, password, role, phone) VALUES 
('System Administrator', 'admin@truecare.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0700000000'),
('Hope Children Center', 'orphanage@truecare.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'orphanage', '0700000001'),
('John Doe', 'donor@truecare.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'donor', '0700000002');

-- Create orphanage record
INSERT INTO orphanages (user_id, name, location, status) 
SELECT user_id, name, 'Nairobi, Kenya', 'verified' 
FROM users WHERE email = 'orphanage@truecare.org';