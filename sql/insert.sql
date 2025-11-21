INSERT INTO users (name, email, password, email_verified) 
VALUES ('Admin', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
-- password is "admin123"

-- 1. Categories
INSERT INTO categories (name, slug, image, description) VALUES
('T-Shirts',        't-shirts',       'cat-tshirt.jpg',    'Premium cotton & poly T-shirts'),
('Hoodies',         'hoodies',        'cat-hoodie.jpg',    'Warm & stylish hoodies for winter'),
('Jackets',         'jackets',        'cat-jacket.jpg',    'Light & heavy jackets – street to formal');

-- 2. Sample Users (Bangladeshi names)
INSERT INTO users (name, email, password, email_verified) VALUES
('Rahim Khan',          'rahim@gmail.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1), -- pass: password
('Ayesha Siddiqua',     'ayesha@outlook.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Md. Salman Hossain',  'salman@yahoo.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Fahmida Akter',       'fahmida@gmail.com',    NULL, 1), -- Google user
('Arif Islam',          'arif@royalman.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- 3. Products (T-shirts, Hoodies, Jackets)
INSERT INTO products (name, slug, description, price, category_id, main_image) VALUES
-- T-Shirts
('Royal Black Premium T-Shirt', 'royal-black-premium-tshirt', '180 GSM pure cotton, gold embroidery', 1299.00, 1, 'royal-black.jpg'),
('Dhaka City Oversized Tee',    'dhaka-city-oversized',       'Street style oversized fit', 1499.00, 1, 'dhaka-city.jpg'),
('Minimal White Pocket Tee',    'minimal-white-pocket',       'Clean look with chest pocket', 1099.00, 1, 'minimal-white.jpg'),
('Bengali Tiger Graphic',       'bengali-tiger-graphic',      'Royal Bengal Tiger print', 1399.00, 1, 'tiger.jpg'),

-- Hoodies
('Royal Man Black Hoodie',      'royal-man-black-hoodie',     'Fleece inside, gold logo', 2799.00, 2, 'hoodie-black.jpg'),
('Navy Blue Zipper Hoodie',     'navy-zipper-hoodie',         'Heavy 350 GSM with zipper', 3299.00, 2, 'hoodie-navy.jpg'),
('Grey Melange Pullover',       'grey-pullover-hoodie',       'Soft brushed fabric', 2599.00, 2, 'hoodie-grey.jpg'),

-- Jackets
('Black Leather Jacket',        'black-leather-jacket',       'Premium PU leather', 5999.00, 3, 'leather-black.jpg'),
('Denim Blue Jacket',           'denim-blue-jacket',          'Washed denim, perfect fit', 4499.00, 3, 'denim-jacket.jpg'),
('Bomber Green Jacket',         'bomber-green-jacket',        'Lightweight bomber style', 3799.00, 3, 'bomber-green.jpg');

-- 4. Multiple Images for some products (product_id from above)
INSERT INTO product_images (product_id, image) VALUES
(1, 'royal-black-back.jpg'),
(1, 'royal-black-side.jpg'),
(2, 'dhaka-city-back.jpg'),
(4, 'tiger-back.jpg'),
(4, 'tiger-zoom.jpg'),
(5, 'hoodie-black-inside.jpg'),
(8, 'leather-detail.jpg');

-- 5. Product Variants (Size + Color + Stock)
INSERT INTO product_variants (product_id, size, color, stock, sku) VALUES
-- Royal Black T-Shirt
(1, 'M', 'Black', 50, 'RM-T01-M-BK'),
(1, 'L', 'Black', 80, 'RM-T01-L-BK'),
(1, 'XL', 'Black', 30, 'RM-T01-XL-BK'),

-- Dhaka City Oversized
(2, 'L', 'White', 40, 'RM-T02-L-WH'),
(2, 'XL', 'White', 25, 'RM-T02-XL-WH'),

-- Black Hoodie
(5, 'M', 'Black', 35, 'RM-H01-M-BK'),
(5, 'L', 'Black', 60, 'RM-H01-L-BK'),
(5, 'XL', 'Black', 20, 'RM-H01-XL-BK'),

-- Leather Jacket
(8, 'M', 'Black', 15, 'RM-J01-M-BK'),
(8, 'L', 'Black', 20, 'RM-J01-L-BK');