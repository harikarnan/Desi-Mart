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
$sqlUsers = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
)";
if ($conn->query($sqlUsers) === TRUE) {
    echo "Table 'users' created successfully.<br>";
} else {
    die("Error creating 'users' table: " . $conn->error);
}

// Create Products table
$sqlProducts = "CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    price FLOAT NOT NULL,
    stock_quantity INT NOT NULL,
    image_path VARCHAR(255),
    description TEXT
)";
if ($conn->query($sqlProducts) === TRUE) {
    echo "Table 'products' created successfully.<br>";
} else {
    die("Error creating 'products' table: " . $conn->error);
}

// Create Categories table
$sqlCategories = "CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
)";
if ($conn->query($sqlCategories) === TRUE) {
    echo "Table 'categories' created successfully.<br>";
} else {
    die("Error creating 'categories' table: " . $conn->error);
}

// Create Orders table
$sqlOrders = "CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME NOT NULL,
    total_amount FLOAT NOT NULL,
    address TEXT NOT NULL,
    email VARCHAR(100) NOT NULL,
    mobile_number VARCHAR(15) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(50) NOT NULL,
    country VARCHAR(50) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    invoice_path VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";
if ($conn->query($sqlOrders) === TRUE) {
    echo "Table 'orders' created successfully.<br>";
} else {
    die("Error creating 'orders' table: " . $conn->error);
}

// Create OrderItems table
$sqlOrderItems = "CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price FLOAT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
)";
if ($conn->query($sqlOrderItems) === TRUE) {
    echo "Table 'order_items' created successfully.<br>";
} else {
    die("Error creating 'order_items' table: " . $conn->error);
}

// Step 3: Insert Sample Data

// Insert sample categories
$sqlInsertCategories = "INSERT INTO categories (name) VALUES
    ('Grains'), ('Spices'), ('Dairy')";
if ($conn->query($sqlInsertCategories) === TRUE) {
    echo "Sample categories inserted into 'categories' table.<br>";
} else {
    echo "Error inserting categories into 'categories' table: " . $conn->error . "<br>";
}

// Insert sample products
$sqlInsertProducts = "INSERT INTO products (name, category_id, price, stock_quantity, image_path, description) VALUES
    ('Basmati Rice', 1, 12.99, 50, 'images/rice.jpeg', 'Premium quality Basmati rice, perfect for all cuisines.'),
    ('Turmeric Powder', 2, 4.99, 100, 'images/turmeric.jpeg', 'Organic turmeric powder for cooking and health benefits.'),
    ('Paneer', 3, 8.99, 30, 'images/paneer.jpeg', 'Fresh and soft paneer, ideal for curries and snacks.')";
if ($conn->query($sqlInsertProducts) === TRUE) {
    echo "Sample products inserted into 'products' table.<br>";
} else {
    echo "Error inserting products into 'products' table: " . $conn->error . "<br>";
}

// Insert sample users
$sqlInsertUsers = "INSERT INTO users (name, email, password) VALUES
    ('John Doe', 'john@example.com', 'password123'),
    ('Jane Smith', 'jane@example.com', 'password123')";
if ($conn->query($sqlInsertUsers) === TRUE) {
    echo "Sample users inserted into 'users' table.<br>";
} else {
    echo "Error inserting users into 'users' table: " . $conn->error . "<br>";
}

// Close the connection
$conn->close();

echo "Setup complete!";
?>