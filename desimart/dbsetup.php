<?php
// Database configuration
$host = "localhost"; // Host (default: localhost)
$user = "root"; // Database username
$password = ""; // Database password
$dbName = "desimart"; // Database name

// Establish MySQL connection
$conn = new mysqli($host, $user, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Create Database
$sqlCreateDB = "CREATE DATABASE IF NOT EXISTS $dbName";
if ($conn->query($sqlCreateDB) === TRUE) {
    echo "Database '$dbName' created successfully.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the newly created database
$conn->select_db($dbName);

// Step 2: Create Tables

// Create Users table
$sqlUsers = "CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(15)
)";
if ($conn->query($sqlUsers) === TRUE) {
    echo "Table 'Users' created successfully.<br>";
} else {
    die("Error creating 'Users' table: " . $conn->error);
}

// Create Products table
$sqlProducts = "CREATE TABLE IF NOT EXISTS Products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price FLOAT NOT NULL,
    stock_quantity INT NOT NULL,
    image_path VARCHAR(255)
)";
if ($conn->query($sqlProducts) === TRUE) {
    echo "Table 'Products' created successfully.<br>";
} else {
    die("Error creating 'Products' table: " . $conn->error);
}

// Create Cart table
$sqlCart = "CREATE TABLE IF NOT EXISTS Cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE
)";
if ($conn->query($sqlCart) === TRUE) {
    echo "Table 'Cart' created successfully.<br>";
} else {
    die("Error creating 'Cart' table: " . $conn->error);
}

// Create Orders table
$sqlOrders = "CREATE TABLE IF NOT EXISTS Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME NOT NULL,
    total_amount FLOAT NOT NULL,
    invoice_path VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
)";
if ($conn->query($sqlOrders) === TRUE) {
    echo "Table 'Orders' created successfully.<br>";
} else {
    die("Error creating 'Orders' table: " . $conn->error);
}

// Create OrderItems table
$sqlOrderItems = "CREATE TABLE IF NOT EXISTS OrderItems (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price FLOAT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE
)";
if ($conn->query($sqlOrderItems) === TRUE) {
    echo "Table 'OrderItems' created successfully.<br>";
} else {
    die("Error creating 'OrderItems' table: " . $conn->error);
}

// Step 3: Insert Sample Data

// Insert sample data into Users table
$sqlInsertUsers = "INSERT INTO Users (name, email, password, address, phone) VALUES
    ('John Doe', 'john@example.com', 'password123', '123 Main St', '1234567890'),
    ('Jane Smith', 'jane@example.com', 'password123', '456 Elm St', '0987654321')";
if ($conn->query($sqlInsertUsers) === TRUE) {
    echo "Sample data inserted into 'Users' table.<br>";
} else {
    echo "Error inserting data into 'Users': " . $conn->error . "<br>";
}

// Insert sample data into Products table
$sqlInsertProducts = "INSERT INTO Products (name, category, price, stock_quantity, image_path) VALUES
    ('Basmati Rice', 'Grains', 12.99, 50, 'images/rice.jpg'),
    ('Turmeric Powder', 'Spices', 4.99, 100, 'images/turmeric.jpg'),
    ('Paneer', 'Dairy', 8.99, 30, 'images/paneer.jpg')";
if ($conn->query($sqlInsertProducts) === TRUE) {
    echo "Sample data inserted into 'Products' table.<br>";
} else {
    echo "Error inserting data into 'Products': " . $conn->error . "<br>";
}

// Insert sample data into Cart table
$sqlInsertCart = "INSERT INTO Cart (user_id, product_id, quantity) VALUES
    (1, 1, 2),
    (2, 2, 1)";
if ($conn->query($sqlInsertCart) === TRUE) {
    echo "Sample data inserted into 'Cart' table.<br>";
} else {
    echo "Error inserting data into 'Cart': " . $conn->error . "<br>";
}

// Insert sample data into Orders table
$sqlInsertOrders = "INSERT INTO Orders (user_id, order_date, total_amount, invoice_path) VALUES
    (1, NOW(), 25.98, 'invoices/order1.pdf'),
    (2, NOW(), 4.99, 'invoices/order2.pdf')";
if ($conn->query($sqlInsertOrders) === TRUE) {
    echo "Sample data inserted into 'Orders' table.<br>";
} else {
    echo "Error inserting data into 'Orders': " . $conn->error . "<br>";
}

// Insert sample data into OrderItems table
$sqlInsertOrderItems = "INSERT INTO OrderItems (order_id, product_id, quantity, price) VALUES
    (1, 1, 2, 12.99),
    (2, 2, 1, 4.99)";
if ($conn->query($sqlInsertOrderItems) === TRUE) {
    echo "Sample data inserted into 'OrderItems' table.<br>";
} else {
    echo "Error inserting data into 'OrderItems': " . $conn->error . "<br>";
}

// Close the connection
$conn->close();

echo "Setup complete!";
?>