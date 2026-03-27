CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profilePicture VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE products(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT, 
    price DECIMAL(10, 2) NOT NULL CHECK (price > 0),
    stock INT NOT NULL DEFAULT 0 CHECK (stock >= 0), 
    category_id INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE product_image(
    image_url VARCHAR(500) PRIMARY KEY,
    product_id INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE addresses(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    country VARCHAR(100) NOT NULL, 
    city VARCHAR(100) NOT NULL, 
    zip_code VARCHAR(20) NOT NULL, 
    street_address VARCHAR(200) NOT NULL, 
    user_id INT NOT NULL, 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE orders(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    fullname VARCHAR(200) NOT NULL, 
    phone VARCHAR(100) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL CHECK (total_amount >= 0), 
    estimated_delivery_date DATE,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    delivered_at TIMESTAMP NULL,
    user_id INT NOT NULL,
    user_address INT NOT NULL,
    FOREIGN KEY (user_address) REFERENCES addresses(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

CREATE TABLE order_items(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    quantity INT NOT NULL CHECK (quantity > 0), 
    price DECIMAL(10, 2) NOT NULL, 
    product_id INT NOT NULL,
    order_id INT NOT NULL, 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT, 
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE cart(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1 CHECK (quantity >= 1),
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE wishlist(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE, 
    UNIQUE KEY unique_wishlist_item (user_id, product_id)
);