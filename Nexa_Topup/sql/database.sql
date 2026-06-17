-- ============================================================
-- NEXA_TOPUP — DATABASE SCHEMA & SEED DATA
-- ============================================================

CREATE DATABASE IF NOT EXISTS nexa_topup CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexa_topup;

-- ── Users ──
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  UNIQUE NOT NULL,
    email      VARCHAR(100) UNIQUE NOT NULL,
    password   VARCHAR(255) NOT NULL,
    phone      VARCHAR(20)  DEFAULT NULL,
    role       ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Kategori Produk ──
CREATE TABLE categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(50) NOT NULL,
    slug       VARCHAR(50) UNIQUE NOT NULL,
    icon       VARCHAR(10) DEFAULT '📦',
    sort_order INT DEFAULT 0
);

-- ── Produk ──
CREATE TABLE products (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    category_id    INT NOT NULL,
    name           VARCHAR(100) NOT NULL,
    description    TEXT,
    diamond_amount INT DEFAULT 0,
    price          DECIMAL(12,2) NOT NULL,
    original_price DECIMAL(12,2) DEFAULT NULL,
    badge          VARCHAR(30)   DEFAULT NULL,
    is_active      TINYINT(1) DEFAULT 1,
    sort_order     INT DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ── Orders ──
CREATE TABLE orders (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    order_code     VARCHAR(20) UNIQUE NOT NULL,
    user_id        INT DEFAULT NULL,
    ml_user_id     VARCHAR(30) NOT NULL,
    ml_server_id   VARCHAR(10) NOT NULL,
    ml_nickname    VARCHAR(60) DEFAULT NULL,
    product_id     INT NOT NULL,
    product_name   VARCHAR(100) NOT NULL,
    total_price    DECIMAL(12,2) NOT NULL,
    payment_method VARCHAR(30) NOT NULL,
    order_status   ENUM('pending','processing','completed','failed') DEFAULT 'pending',
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- ── Metode Pembayaran ──
CREATE TABLE payment_methods (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(60) NOT NULL,
    code        VARCHAR(20) UNIQUE NOT NULL,
    icon        VARCHAR(10) DEFAULT '💳',
    fee_percent DECIMAL(5,2) DEFAULT 0.00,
    fee_fixed   DECIMAL(10,2) DEFAULT 0.00,
    is_active   TINYINT(1) DEFAULT 1
);

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO categories (name, slug, icon, sort_order) VALUES
('Diamond',      'diamond',    '💎', 1),
('Membership',   'membership', '👑', 2),
('Bundle',       'bundle',     '🎁', 3),
('Weekly Pass',  'weekly',     '📅', 4),
('Skin Pass',    'skin',       '🎨', 5);

-- Diamond
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(1, '12 Diamonds',           'Top up 12 Diamond ML',                       12,   2000,   NULL,  NULL,          1),
(1, '25 Diamonds',           'Top up 25 Diamond ML',                       25,   4000,   NULL,  NULL,          2),
(1, '60 Diamonds',           'Top up 60 Diamond ML',                       60,   9500,  10000, 'HOT',          3),
(1, '125 Diamonds',          'Top up 125 Diamond ML',                     125,  19000,  20000,  NULL,          4),
(1, '195 Diamonds',          'Top up 195 Diamond ML',                     195,  30000,   NULL,  NULL,          5),
(1, '257 Diamonds',          'Top up 257 Diamond ML',                     257,  40000,   NULL, 'BEST VALUE',   6),
(1, '344 Diamonds',          'Top up 344 Diamond ML',                     344,  55000,   NULL,  NULL,          7),
(1, '429 Diamonds',          'Top up 429 Diamond ML',                     429,  69000,  75000, 'SALE',         8),
(1, '514 Diamonds',          'Top up 514 Diamond ML',                     514,  82000,   NULL,  NULL,          9),
(1, '706 Diamonds',          'Top up 706 Diamond ML',                     706, 112000, 120000, 'HOT',         10),
(1, '878 Diamonds',          'Top up 878 Diamond ML',                     878, 140000,   NULL,  NULL,         11),
(1, '1220 Diamonds',         'Top up 1220 Diamond ML',                   1220, 194000, 200000, 'BEST VALUE',  12),
(1, '2010 Diamonds',         'Top up 2010 Diamond ML',                   2010, 319000,   NULL, 'HOT',         13),
(1, '3688 Diamonds + Bonus', 'Top up 3688 Diamond ML + bonus eksklusif', 3688, 579000, 600000, 'PROMO',       14),
(1, '5532 Diamonds + Bonus', 'Top up 5532 Diamond ML + bonus eksklusif', 5532, 869000, 900000, 'SALE',        15);

-- Membership
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(2, 'Starlight Member',       'Skin eksklusif + bonus harian selama 1 bulan',                0, 29000,   NULL, 'POPULER',    1),
(2, 'Starlight Member Plus',  'Skin premium + avatar border + bonus diamond selama 1 bulan', 0, 49000,   NULL, 'HOT',        2),
(2, 'Twilight Pass',          'Akses misi eksklusif & reward spesial',                       0, 59000,  70000, 'NEW',        3),
(2, 'Starlight 3 Bulan',      'Paket hemat Starlight Member selama 3 bulan',                 0, 79000,  87000, 'BEST VALUE', 4);

-- Weekly Pass
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(4, 'Weekly Diamond Pass',      'Daily diamond selama 7 hari, total 70 diamond',         70,  9500,  NULL, 'HOT', 1),
(4, 'Weekly Diamond Pass Plus', 'Daily diamond selama 7 hari, total 140 diamond + bonus',140, 15000,  NULL,  NULL, 2);

-- Bundle
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(3, 'Starter Pack', 'Bundle pemula: 100 Diamond + Hero Trial + BP x3',     100, 15000, 20000, 'SALE',    1),
(3, 'Elite Pack',   'Bundle elite: 500 Diamond + Skin Fragment x5 + Badge', 500, 75000, 90000, 'POPULER', 2),
(3, 'Lucky Bundle', 'Bundle lucky: 300 Diamond + Lucky Chest x2',           300, 50000,  NULL, 'NEW',     3);

-- Skin Pass
INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order) VALUES
(5, 'Hero Skin Pass',       'Tukar poin untuk skin hero pilihan',      0, 39000,  NULL,  NULL,  1),
(5, 'Premium Skin Chest',   'Chest berisi skin random grade A atau S', 0, 25000,  NULL, 'HOT',  2);

-- Payment Methods
INSERT INTO payment_methods (name, code, icon, fee_percent, fee_fixed) VALUES
('GoPay',              'gopay',     '🟢', 0.00, 0),
('OVO',                'ovo',       '🔵', 0.00, 0),
('DANA',               'dana',      '🟣', 0.00, 0),
('ShopeePay',          'shopeepay', '🟠', 0.00, 0),
('QRIS',               'qris',      '🌐', 0.70, 0),
('Transfer Bank BCA',  'bca',       '🏦', 0.00, 0),
('Transfer Bank Mandiri','mandiri', '🏦', 0.00, 0),
('Kartu Kredit/Debit', 'cc',        '💳', 2.90, 0),
('Pulsa Telkomsel',    'telkomsel', '📱', 5.00, 0),
('Pulsa XL/Axis',      'xl',        '📱', 5.00, 0);

-- Admin default (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@nexatopup.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');