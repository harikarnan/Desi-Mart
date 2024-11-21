<?php
class Order {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createOrder($userId, $cartItems, $totalAmount) {
        $query = "INSERT INTO orders (user_id, total_amount) VALUES (:user_id, :total_amount)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':total_amount' => $totalAmount
        ]);
        $orderId = $this->db->lastInsertId();

        foreach ($cartItems as $item) {
            $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                      VALUES (:order_id, :product_id, :quantity, :price)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }

        return $orderId;
    }
}
?>
