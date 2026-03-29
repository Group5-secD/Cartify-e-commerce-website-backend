CREATE Cartifydb;
USE Cartifydb;
CREATE TABLE accounts (
    accID INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE category (
    categoryID INT PRIMARY KEY AUTO_INCREMENT,
    categoryname VARCHAR(100) NOT NULL
);

CREATE TABLE product (
    productID INT PRIMARY KEY AUTO_INCREMENT,
    categoryID INT NOT NULL,
    productName VARCHAR(100) NOT NULL,
    productDescription TEXT,
    price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_prod_cat FOREIGN KEY (categoryID) REFERENCES category(categoryID) 
        ON UPDATE CASCADE ON DELETE CASCADE
);
CREATE TABLE feedback (
    feedbackID INT PRIMARY KEY AUTO_INCREMENT,
    accID INT NOT NULL,
    accName VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    messageText TEXT NOT NULL,
    CONSTRAINT fk_feed_acc FOREIGN KEY (accID) REFERENCES accounts(accID) 
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE wishlist (
    wishlistID INT PRIMARY KEY AUTO_INCREMENT,
    productID INT NOT NULL,
    accID INT NOT NULL,
    CONSTRAINT fk_wish_prod FOREIGN KEY (productID) REFERENCES product(productID) 
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_wish_acc FOREIGN KEY (accID) REFERENCES accounts(accID) 
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE cart (
    cartID INT PRIMARY KEY AUTO_INCREMENT,
    productID INT NOT NULL,
    accID INT NOT NULL,
    quantity INT DEFAULT 1,
    CONSTRAINT fk_cart_prod FOREIGN KEY (productID) REFERENCES product(productID) 
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_cart_acc FOREIGN KEY (accID) REFERENCES accounts(accID) 
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE orderDetail (
    orderID INT PRIMARY KEY AUTO_INCREMENT,
    accID INT NOT NULL,
    orderDate DATETIME,
    accName VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    streetAddress VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    zipCode VARCHAR(50) NOT NULL,
    totalAmount DECIMAL(10,2),
    CONSTRAINT fk_ord_acc FOREIGN KEY (accID) REFERENCES accounts(accID) 
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE orderedItems (
    orderedItemsID INT PRIMARY KEY AUTO_INCREMENT,
    orderID INT NOT NULL,
    productID INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_prod FOREIGN KEY (productID) REFERENCES product(productID) 
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_item_ord FOREIGN KEY (orderID) REFERENCES orderDetail(orderID) 
        ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO accounts (accID, email, passwordHash, createdAt)
VALUES (1, 'johndoe@gmail.com', '$2a$12$R9h/lZ9bVf1pZ9.S09f8veY', '2026-03-27 18:45:00');

INSERT INTO category (categoryID, categoryname)
VALUES 
(1, 'Electronics'),
(2, 'Accessories'),
(3, 'Home Office'),
(4, 'Sports Gear');

INSERT INTO product (productID, categoryID, productName, productDescription, price)
VALUES 
(1, 1, 'Wireless Headphones', 'Premium sound quality with noise cancellation', 4469.99),
(2, 1, 'Smart Watch', 'Track your fitness and stay connected',3209.99),
(3, 1, 'O AirPods Max original', 'Over-ear wireless headphones with stylish design and rich audio output.',5034.99),
(4, 1, 'MacBook Air 13- and 15-inc', 'A powerful and stylish ultra-slim laptop designed for performance and portability.',140334.99),
(5, 1, 'Bluetooth Speaker', 'Portable with powerful bass',1059.99),
(6, 2, 'Laptop Backpack', 'Durable and spacious for daily use',3049.99),
(7, 2, 'Black Single Clover Gold Plated Necklace', 'Elegant gold-plated necklace suitable for daily wear and special occasions.',849.99),
(8, 2, 'stainless steel silver men rings', 'Stylish stainless steel ring designed for everyday mens fashion.', 549.99),
(9, 3, 'Coffee Maker', 'Brew perfect coffee every morning.',5089.99),
(10, 3, 'Desk Lamp', 'Adjustable LED lighting for your workspace.',1034.99),
(11, 4, 'Running Shoes', 'Comfortable and lightweight design',2019.99),
(12, 4, 'Yoga Mat', 'Non-slip surface for your practice',929.99);

-- some querys to test the database and some variable declaration
INSERT INTO feedback (feedbackID, accID, accName, email, messageText)
VALUES (1, 1, 'John Doe', 'johndoe@gmail.com', 'Well done!');

INSERT INTO wishlist (wishlistID, productID, accID)
VALUES (1, 1, 1);
INSERT INTO cart (cartID, productID, accID, quantity)
VALUES (1, 1, 1, 1);


SET @tempTotal = 
(   SELECT SUM(c.quantity * p.price)
    FROM cart c JOIN product p 
    ON c.productID = p.productID
    WHERE c.accID = 1
);

SET @totalItemPrice=
(
 SELECT SUM(o.quantity*p.price)
 FROM orderedItems o JOIN product p
 ON o.productId= p.productId
 WHERE o.productID=1
);

INSERT INTO orderDetail (orderID, accID, orderDate, accName, email, streetAddress, city, zipCode, totalAmount)
VALUES 
(1, 1, '2026-03-27', 'John Doe', 'johndoe@gmail.com', 'Bole 2345 Street', 'Addis Ababa', '99922', @tempTotal);

INSERT INTO orderedItems (orderedItemsID, orderID, productID, quantity, price)
VALUES (1, 1, 1, 1, @totalItemPrice);
