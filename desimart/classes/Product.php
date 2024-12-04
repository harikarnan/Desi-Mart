<?php
class Product
{
    private $db;

    public function __construct($db)
    {
        if (!$db) {
            throw new Exception("Database connection is not established.");
        }
        $this->db = $db;
    }

    // Fetch all products
    public function getAllProducts($limit = null)
    {
        if (isset($limit)) {
            $query = "SELECT product_id, name, price, products_image_path, description FROM products LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $query = "SELECT product_id, name, price, products_image_path, description FROM products";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    // Fetch a product by ID
    public function getProductById($id)
    {
        $query = "SELECT product_id, name, price, description, products_image_path 
                  FROM products WHERE product_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new product
    public function addProduct($data)
    {
        $query = "INSERT INTO products (name, category_id, price, stock_quantity, description, products_image_path) 
                  VALUES (:name, :category_id, :price, :stock_quantity, :description, :products_image_path)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':name' => $data['name'],
            ':category_id' => $data['category_id'],
            ':price' => $data['price'],
            ':stock_quantity' => $data['stock_quantity'],
            ':description' => $data['description'],
            ':products_image_path' => $data['products_image_path']
        ]);
    }
}
