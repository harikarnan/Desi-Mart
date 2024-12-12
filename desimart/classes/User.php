<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($data) {
        $role = 'user';
        $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password, :role)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':role' => $role
        ]);
    }    

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password']) && $user['role'] == 'user') {
            return $user;
        }
        return false;
    }
}
?>
