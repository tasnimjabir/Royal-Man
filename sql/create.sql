CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) DEFAULT NULL,
    `google_id` varchar(100) DEFAULT NULL,
    `otp_code` varchar(6) DEFAULT NULL,
    `otp_expires` datetime DEFAULT NULL,
    `email_verified` tinyint(4) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `role` enum('user','admin') DEFAULT 'user',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) UNIQUE NOT NULL,
    image VARCHAR(255),
    description TEXT
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) UNIQUE NOT NULL,
    description LONGTEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    main_image VARCHAR(255),
    views INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    size_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE(category_id, size_name)
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT,
    product_id INT,
    size_id INT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE SET NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(255),
    total DECIMAL(12,2),
    shipping_charge DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    customer_name VARCHAR(100),
    customer_email VARCHAR(100),
    shipping_address LONGTEXT,
    phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    size_id INT NULL,
    quantity INT,
    price_at_purchase DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE SET NULL
);

CREATE TABLE carousels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,          -- Background image file name
    big_text VARCHAR(200) NOT NULL,       -- Big headline
    small_text VARCHAR(300) DEFAULT '',   -- Small description
    button_text VARCHAR(100) DEFAULT 'Shop Now',  -- Button text
    button_link VARCHAR(255) DEFAULT '#shop',     -- Button URL
    sort_order INT DEFAULT 0,                -- Order in carousel
    active TINYINT DEFAULT 1,             -- 1 = show, 0 = hide
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Data (Optional)
INSERT INTO carousels (image, big_text, small_text, button_text, button_link, sort_order) VALUES
('hero1.jpg', 'Royal Man Collection', 'Premium T-Shirts for Kings', 'Shop Now', '#shop', 1),
('hero2.jpg', 'Winter Hoodies', 'Stay Warm in Style', 'Explore', '#hoodies', 2);

-- FAQ Table for frequently asked questions
CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ques VARCHAR(500) NOT NULL,
    ans LONGTEXT NOT NULL,
    page VARCHAR(100) NOT NULL DEFAULT 'cart',
    sort_order INT DEFAULT 0,
    active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Metadata table for key/value pairs used by the application (e.g., shipping prices)
CREATE TABLE IF NOT EXISTS metadata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT
);

-- Example metadata rows for shipping
INSERT INTO metadata (name, value) VALUES
('in_dhaka', '60.00'),
('outside_dhaka', '120.00')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- If you already have an existing database, run these ALTER statements to add the shipping column, metadata table, and FAQ table:
-- ALTER TABLE orders ADD COLUMN shipping_charge DECIMAL(10,2) DEFAULT 0;
-- CREATE TABLE IF NOT EXISTS metadata (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     name VARCHAR(100) NOT NULL UNIQUE,
--     value TEXT
-- );
-- INSERT INTO metadata (name, value) VALUES ('in_dhaka', '60.00'), ('outside_dhaka', '120.00') ON DUPLICATE KEY UPDATE value = VALUES(value);
-- CREATE TABLE IF NOT EXISTS faq (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     ques VARCHAR(500) NOT NULL,
--     ans LONGTEXT NOT NULL,
--     page VARCHAR(100) NOT NULL DEFAULT 'cart',
--     sort_order INT DEFAULT 0,
--     active TINYINT DEFAULT 1,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );
-- INSERT INTO faq (ques, ans, page, sort_order) VALUES
-- ('What is your return policy?', 'We offer 30-day returns on all products. Items must be in original condition with tags attached.', 'cart', 1),
-- ('How long does shipping take?', 'Standard shipping takes 3-5 business days. Express shipping available for 1-2 days.', 'cart', 2),
-- ('Do you offer international shipping?', 'Currently, we ship within Bangladesh only. International shipping coming soon!', 'cart', 3),
-- ('How can I track my order?', 'You will receive a tracking link via email once your order is dispatched.', 'cart', 4),
-- ('Are your products original?', 'Yes! All our products are 100% authentic and sourced directly from premium manufacturers.', 'cart', 5);