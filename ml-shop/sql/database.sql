-- ============================================
-- ML SHOP - DATABASE SCHEMA
-- ============================================

CREATE DATABASE IF NOT EXISTS mlshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mlshop;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user','admin') DEFAULT 'user',
    balance DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    icon VARCHAR(10),
    sort_order INT DEFAULT 0
);

-- Products (diamond packs, memberships, bundles)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    diamond_amount INT DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    badge VARCHAR(30),       -- HOT, SALE, NEW, BEST VALUE
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    ml_user_id VARCHAR(30) NOT NULL,       -- Game User ID
    ml_server_id VARCHAR(10) NOT NULL,     -- Zone/Server ID
    ml_nickname VARCHAR(50),
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30),
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    order_status ENUM('pending','processing','completed','failed') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Payment methods
CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    icon VARCHAR(10),
    fee_percent DECIMAL(5,2) DEFAULT 0,
    fee_fixed DECIMAL(10,2) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

-- ============================================
-- SEED DATA
-- ============================================

INSERT INTO categories (name, slug, icon, sort_order) VALUES
('Diamond', 'diamond', '💎', 1),
('Membership', 'membership', '👑', 2),
('Bundle', 'bundle', '🎁', 3),
('Weekly Pass', 'weekly', '📅', 4),
('Skin Pass', 'skin', '🎨', 5);

-- Diamond packs
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(1, '12 Diamonds', 'Diamond Mobile Legends 12 butir', 12, 2000, NULL, NULL, 1),
(1, '25 Diamonds', 'Diamond Mobile Legends 25 butir', 25, 4000, NULL, NULL, 2),
(1, '60 Diamonds', 'Diamond Mobile Legends 60 butir', 60, 9500, 10000, 'HOT', 3),
(1, '125 Diamonds', 'Diamond Mobile Legends 125 butir', 125, 19000, 20000, NULL, 4),
(1, '195 Diamonds', 'Diamond Mobile Legends 195 butir', 195, 30000, NULL, NULL, 5),
(1, '257 Diamonds', 'Diamond Mobile Legends 257 butir', 257, 40000, NULL, 'BEST VALUE', 6),
(1, '344 Diamonds', 'Diamond Mobile Legends 344 butir', 344, 55000, NULL, NULL, 7),
(1, '429 Diamonds', 'Diamond Mobile Legends 429 butir', 429, 69000, 75000, 'SALE', 8),
(1, '514 Diamonds', 'Diamond Mobile Legends 514 butir', 514, 82000, NULL, NULL, 9),
(1, '706 Diamonds', 'Diamond Mobile Legends 706 butir', 706, 112000, 120000, 'HOT', 10),
(1, '878 Diamonds', 'Diamond Mobile Legends 878 butir', 878, 140000, NULL, NULL, 11),
(1, '1220 Diamonds', 'Diamond Mobile Legends 1220 butir', 1220, 194000, 200000, 'BEST VALUE', 12),
(1, '2010 Diamonds', 'Diamond Mobile Legends 2010 butir', 2010, 319000, NULL, 'HOT', 13),
(1, '3688 Diamonds + Bonus', 'Diamond Mobile Legends 3688 butir + bonus exclusive', 3688, 579000, 600000, 'PROMO', 14),
(1, '5532 Diamonds + Bonus', 'Diamond Mobile Legends 5532 butir + bonus exclusive', 5532, 869000, 900000, 'SALE', 15);

-- Memberships
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(2, 'Starlight Member', 'Starlight Member bulanan — skin eksklusif + bonus harian', 0, 29000, NULL, 'POPULER', 1),
(2, 'Starlight Member Plus', 'Starlight Plus — skin premium + avatar border + bonus diamond', 0, 49000, NULL, 'HOT', 2),
(2, 'Twilight Pass', 'Twilight Pass — akses misi eksklusif & reward spesial', 0, 59000, 70000, 'NEW', 3),
(2, 'Starlight 3 Bulan', 'Starlight Member paket 3 bulan — hemat lebih banyak!', 0, 79000, 87000, 'BEST VALUE', 4);

-- Weekly Pass
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(4, 'Weekly Diamond Pass', 'Daily diamond selama 7 hari — total 70 diamond', 70, 9500, NULL, 'HOT', 1),
(4, 'Weekly Diamond Pass Plus', 'Daily diamond selama 7 hari — total 140 diamond + bonus', 140, 15000, NULL, NULL, 2);

-- Bundles
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(3, 'Starter Pack', 'Bundle pemula — 100 Diamond + Hero Trial + Battle Point x3', 100, 15000, 20000, 'SALE', 1),
(3, 'Elite Pack', 'Bundle elite — 500 Diamond + Skin Fragment x5 + Badge', 500, 75000, 90000, 'POPULER', 2),
(3, 'Lucky Bundle', 'Bundle lucky draw — 300 Diamond + Lucky Chest x2', 300, 50000, NULL, 'NEW', 3);

-- Skin Pass
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(5, 'Hero Skin Pass', 'Tukar poin untuk skin hero pilihan', 0, 39000, NULL, NULL, 1),
(5, 'Premium Skin Chest', 'Chest berisi skin random grade A atau S', 0, 25000, NULL, 'HOT', 2);

-- Payment methods
INSERT INTO payment_methods (name, code, icon, fee_percent, fee_fixed) VALUES
('GoPay', 'gopay', '🟢', 0, 0),
('OVO', 'ovo', '🔵', 0, 0),
('DANA', 'dana', '🟣', 0, 0),
('ShopeePay', 'shopeepay', '🟠', 0, 0),
('QRIS', 'qris', '🌐', 0.7, 0),
('Transfer Bank BCA', 'bca', '🏦', 0, 0),
('Transfer Bank Mandiri', 'mandiri', '🏦', 0, 0),
('Kartu Kredit / Debit', 'cc', '💳', 2.9, 0),
('Pulsa Telkomsel', 'telkomsel', '📱', 5, 0),
('Pulsa XL/Axis', 'xl', '📱', 5, 0);

-- Admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@mlshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
