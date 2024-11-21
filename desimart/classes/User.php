<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($data) {
        $query = "INSERT INTO users (name, email, password, address) 
                  VALUES (:name, :email, :password, :address)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':address' => $data['address']
        ]);
    }

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
