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

// Create Categories table
$sqlCategories = "CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    categories_image_path VARCHAR(255)
)";
if ($conn->query($sqlCategories) === TRUE) {
    echo "Table 'categories' created successfully.<br>";
} else {
    die("Error creating 'categories' table: " . $conn->error);
}

// Create Products table
$sqlProducts = "CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    price FLOAT NOT NULL,
    stock_quantity INT NOT NULL,
    products_image_path VARCHAR(255),
    description TEXT,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
)";
if ($conn->query($sqlProducts) === TRUE) {
    echo "Table 'products' created successfully.<br>";
} else {
    die("Error creating 'products' table: " . $conn->error);
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
$sqlInsertCategories = "INSERT INTO categories (name, categories_image_path) VALUES
    ('Grains & Pulses', 'images/categories/grains-and-pulses.jpeg'), 
    ('Spices', 'images/categories/spices.jpeg'), 
    ('Dairy', 'images/categories/dairy.jpeg'), 
    ('Condiments', 'images/categories/condiments.jpeg'), 
    ('Cooking Oils', 'images/categories/cooking-oils.jpeg'),
    ('Kitchenwares', 'images/categories/kitchenwares.jpeg'),
    ('Beverages', 'images/categories/beverages.jpeg')
    ";

if ($conn->query($sqlInsertCategories) === TRUE) {
    echo "Sample categories inserted into 'categories' table.<br>";
} else {
    echo "Error inserting categories into 'categories' table: " . $conn->error . "<br>";
}

// Insert sample products
$sqlInsertProducts = "INSERT INTO products (name, category_id, price, stock_quantity, products_image_path, description) VALUES

    ('Premium Basmati Rice', 1, 12.99, 50, 
      'images/products/rice.jpeg', 
      'Premium quality Basmati rice, long-grained and aromatic. Known for its delicate flavor and fluffy texture, this rice is perfect for biryanis, pulaos, or any special meal. Cultivated in the fertile lands of India, each grain is aged for the perfect taste and quality. Non-sticky and highly nutritious, it is ideal for health-conscious families.'),

    ('Turmeric Powder', 2, 4.99, 100, 
      'images/products/turmeric.jpeg', 
      'Add a golden touch to your meals with Organic Turmeric Powder, known for its vibrant color and health benefits. Sourced from organically grown turmeric roots, its packed with curcumin, a powerful antioxidant with anti-inflammatory properties. Perfect for curries, soups, or golden milk, it enhances flavor while promoting wellness. Free from artificial colors or additives, it ensures purity and quality.'),

    ('Paneer', 3, 8.99, 30, 
      'images/products/paneer.jpeg', 
      'Soft, fresh, and made from 100% organic milk, this Organic Paneer is perfect for creating delicious Indian dishes like palak paneer, paneer tikka, or curries. High in protein and calcium, its a nutritious choice for vegetarians. Free from artificial additives, it offers the authentic taste of homemade paneer.'),

    ('Desi Ghee', 3, 12.99, 40, 
      'images/products/ghee.jpeg', 
      'Pure and wholesome, Desi Ghee is made from the milk of grass-fed Indian cows using traditional methods. Rich in nutrients, it enhances the flavor of your meals and offers numerous health benefits. Perfect for cooking, frying, or simply adding a dollop to your dal or roti, this ghee is a kitchen essential. Its golden color and nutty aroma evoke memories of home-cooked meals. A natural source of energy, its ideal for all ages.'),

    ('Multigrain Atta (Flour)', 1, 8.99, 60, 
      'images/products/flour.jpeg', 
      'Switch to healthier eating with Multigrain Atta, a nutritious blend of wheat, millet, oats, and barley. Perfect for soft and fluffy rotis, parathas, or puris, this flour is rich in fiber and essential nutrients.Switch to healthier eating with Multigrain Atta, a nutritious blend of wheat, millet, oats, and barley. Perfect for soft and fluffy rotis, parathas, or puris, this flour is rich in fiber and essential nutrients.'),

    ('Classic Mango Pickle', 4, 6.49, 30, 
      'images/products/mango-pickle.jpeg', 
      'Bursting with the tangy and spicy flavors of India, Classic Mango Pickle is a timeless delight. Made from handpicked raw mangoes and a blend of aromatic spices, its the perfect accompaniment to your meals. Preserved using traditional methods with mustard oil and natural ingredients, this pickle ensures an authentic taste.'),

    ('Mustard Oil', 5, 11.99, 35, 
      'images/products/mustard-oil.jpeg', 
      'Experience the natural goodness of Organic Cold-Pressed Mustard Oil, known for its bold flavor and numerous health benefits. Extracted using traditional methods, this oil retains all its nutrients and unique aroma. Perfect for frying, sautéing, or as a base for marinades, it adds a robust, earthy taste to your dishes. Packed with antioxidants and essential fatty acids, its great for heart health and boosts immunity.'),

    ('Handcrafted Clay Cookware Set', 6, 26.99, 10, 
      'images/products/clay-cookware-set.jpeg', 
      'Rediscover the ancient art of cooking with this Handcrafted Clay Cookware Set. Made by skilled artisans, these pots retain the natural flavor of your food while ensuring even cooking. Free from chemicals and synthetic materials, they are ideal for slow-cooked curries, dals, and traditional recipes. Its porous nature allows for enhanced moisture retention, keeping your dishes tender and flavorful. Perfect for eco-conscious cooks, this set adds authenticity to your kitchen and supports sustainable living.'),

    ('Masala Chai Premix', 7, 5.99, 30, 
      'images/products/masala-chai.jpeg', 
      'Aromatic and invigorating, Masala Chai Premix offers the essence of traditional Indian chai in every sip. Infused with premium black tea, ginger, cardamom, and cinnamon, this premix captures the authentic flavors of Indian households. Perfect for quick preparation, it ensures you never miss out on the taste of home. Just add hot water and stir for a steaming cup of goodness. Made with natural ingredients, its a healthier choice with no artificial preservatives.'),

    ('Ceramic Tea Cups Set', 6, 20.99, 11, 
      'images/products/ceramic-tea-cups-set.jpeg', 
      'Enjoy your tea in style with this Ceramic Tea Cups Set. Each cup is handcrafted and painted with traditional Indian motifs, adding a touch of elegance to your tea time. Durable and microwave-safe, these cups are perfect for daily use or gifting.'),

    ('Black Chana (Chickpeas)', 1, 7.49, 30, 
      'images/products/black-chana.jpeg', 
      'Black Chana (Chickpeas) is a versatile and nutritious pulse packed with protein, fiber, and essential vitamins. Its earthy flavor makes it ideal for curries, salads, and stews. Grown naturally, this chana is free from chemicals and additives, ensuring the highest quality.'),
      
    ('Organic Moong Dal', 1, 7.99, 50, 
      'images/products/moong-dal.jpeg',
      'Organic Moong Dal is a rich source of protein, making it an essential ingredient for a healthy diet. Sourced from certified organic farms, it is unpolished to retain its natural nutrients and flavor. Ideal for soups, dals, and khichdis, it cooks quickly and is light on the stomach. Its high fiber content supports digestion and overall health.'),

    ('Premium Sunflower Oil', 5, 12.99, 20, 
      'images/products/sunflower-oil.jpeg', 
      'Experience the light and healthy benefits of Premium Sunflower Oil. Made from the finest sunflower seeds, this oil is rich in Vitamin E and healthy fats, making it perfect for frying, sautéing, and baking. Its mild flavor allows the natural taste of your ingredients to shine through. Free from chemicals and additives, this oil is ideal for health-conscious cooking.'),

    ('Kashmiri Red Chili Powder', 2, 4.99, 25, 
      'images/products/red-chili-powder.jpeg', 
      'Add a vibrant red hue and mild heat to your dishes with Kashmiri Red Chili Powder. Made from sun-dried Kashmiri chilies, this powder is perfect for curries, marinades, and chutneys. Its smoky aroma and mild spiciness enhance the flavor of any dish without overpowering it.'),
      
    ('Garlic Pickle', 4, 6.99, 25, 
      'images/products/garlic-pickle.jpeg', 
      'Spicy and tangy, Garlic Pickle is a flavorful accompaniment to Indian meals. Made with fresh garlic cloves, mustard oil, and a blend of spices, it adds a punch of flavor to rice, rotis, or parathas. Preserved using traditional methods, it retains its rich taste and aroma.'),
      
    ('Organic Green Tea', 7, 10.99, 15, 
      'images/products/green-tea.jpeg', 
      'Rejuvenate your senses with Organic Green Tea, sourced from the lush tea gardens of Assam. Packed with antioxidants, this tea promotes relaxation, boosts metabolism, and improves overall wellness. Its mild and refreshing flavor makes it an excellent choice for health-conscious individuals.')
    ";

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