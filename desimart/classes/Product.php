<?php
class Product {
    private $db;

    public function __construct($db) {
        if (!$db) {
            throw new Exception("Database connection is not established.");
        }
        $this->db = $db;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM products";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addProduct($data) {
        $query = "INSERT INTO products (name, category_id, price, stock, description, image) 
                  VALUES (:name, :category_id, :price, :stock, :description, :image)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':name' => $data['name'],
            ':category_id' => $data['category_id'],
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':description' => $data['description'],
            ':image' => $data['image']
        ]);
    }
}
?>
