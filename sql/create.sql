CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `email_verified` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
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