-- Create database
CREATE DATABASE IF NOT EXISTS truecare_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
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

-- Campaigns table
CREATE TABLE campaigns (
    campaign_id INT AUTO_INCREMENT PRIMARY KEY,
    orphanage_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    category ENUM('education', 'medical', 'food', 'shelter', 'clothing', 'other') NOT NULL,
    target_amount DECIMAL(10,2) NOT NULL,
    current_amount DECIMAL(10,2) DEFAULT 0,
    deadline DATE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (orphanage_id) REFERENCES orphanages(orphanage_id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_orphanage_id (orphanage_id)
);

-- Donations table
CREATE TABLE donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    campaign_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('mpesa', 'paypal', 'card') NOT NULL,
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(campaign_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_campaign_id (campaign_id),
    INDEX idx_status (status)
);

-- Verifications table
CREATE TABLE verifications (
    verification_id INT AUTO_INCREMENT PRIMARY KEY,
    orphanage_id INT,
    gov_record_id VARCHAR(100),
    verified_by INT,
    verification_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('verified', 'rejected') NOT NULL,
    notes TEXT,
    FOREIGN KEY (orphanage_id) REFERENCES orphanages(orphanage_id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_orphanage_id (orphanage_id),
    INDEX idx_status (status)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('System Administrator', 'admin@truecare.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');