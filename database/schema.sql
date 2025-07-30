-- FoodExpress Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS foodexpress;
USE foodexpress;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(50),
    postal_code VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    payment_method ENUM('mpesa', 'cash', 'card') DEFAULT 'mpesa',
    delivery_address TEXT NOT NULL,
    delivery_phone VARCHAR(20) NOT NULL,
    delivery_instructions TEXT,
    mpesa_transaction_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    special_instructions TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- M-Pesa transactions table
CREATE TABLE mpesa_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    transaction_id VARCHAR(50) UNIQUE NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    response_code VARCHAR(10),
    response_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Delivery zones table
CREATE TABLE delivery_zones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    delivery_fee DECIMAL(10,2) NOT NULL,
    estimated_time VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insert sample data
INSERT INTO categories (name, description) VALUES
('Pizza', 'Delicious Italian pizzas with fresh toppings'),
('Burgers', 'Juicy burgers with premium ingredients'),
('Pasta', 'Authentic Italian pasta dishes'),
('Salads', 'Fresh and healthy salad options'),
('Desserts', 'Sweet treats and desserts'),
('Beverages', 'Refreshing drinks and beverages');

INSERT INTO products (category_id, name, description, price, is_featured) VALUES
(1, 'Margherita Pizza', 'Classic tomato sauce with mozzarella cheese', 1200.00, TRUE),
(1, 'Pepperoni Pizza', 'Spicy pepperoni with melted cheese', 1400.00, TRUE),
(1, 'BBQ Chicken Pizza', 'BBQ sauce with grilled chicken and onions', 1600.00, FALSE),
(2, 'Classic Burger', 'Beef patty with lettuce, tomato, and cheese', 800.00, TRUE),
(2, 'Chicken Burger', 'Grilled chicken with special sauce', 900.00, TRUE),
(2, 'Veggie Burger', 'Plant-based patty with fresh vegetables', 750.00, FALSE),
(3, 'Spaghetti Carbonara', 'Creamy pasta with bacon and parmesan', 1000.00, TRUE),
(3, 'Penne Arrabbiata', 'Spicy tomato sauce with penne pasta', 950.00, FALSE),
(4, 'Caesar Salad', 'Fresh romaine lettuce with caesar dressing', 600.00, TRUE),
(4, 'Greek Salad', 'Mixed greens with feta cheese and olives', 650.00, FALSE),
(5, 'Chocolate Cake', 'Rich chocolate cake with cream', 400.00, TRUE),
(5, 'Ice Cream Sundae', 'Vanilla ice cream with toppings', 350.00, TRUE),
(6, 'Fresh Juice', 'Orange, apple, or mango juice', 200.00, TRUE),
(6, 'Soda', 'Coca Cola, Sprite, or Fanta', 150.00, TRUE);

INSERT INTO delivery_zones (name, delivery_fee, estimated_time) VALUES
('Nairobi CBD', 200.00, '20-30 minutes'),
('Westlands', 250.00, '25-35 minutes'),
('Kilimani', 200.00, '20-30 minutes'),
('Lavington', 300.00, '30-40 minutes'),
('Karen', 400.00, '35-45 minutes'); 