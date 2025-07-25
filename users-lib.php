<?php
header('Content-Type: application/json');
ob_start(); // Start output buffering

// Your existing Userlib class...

// (H) DATABASE SETTINGS
define("DB_HOST", "mysql-libapi.alwaysdata.net");
define("DB_NAME", "libapi_database");
define("DB_CHARSET", "utf8mb4");
define("DB_USER", "libapi");
define("DB_PASSWORD", "Bakali1");

// (I) START!
session_start();
$USR = new Userlib();

// Handle login request
if (isset($_POST['req']) && $_POST['req'] === 'in') {
    try {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $result = $USR->verify($email, $password);
        
        ob_end_clean(); // Clean any output
        echo json_encode([
            'status' => $result,
            'message' => $result ? "OK" : "Invalid email/password"
        ]);
        exit();
        
    } catch (Exception $e) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode([
            'status' => false,
            'message' => 'Server error',
            'error' => $e->getMessage() // Only for debugging
        ]);
        exit();
    }
}
class Userlib{
        // (A) CONSTRUCTOR - CONNECT TO DATABASE
    private $pdo = null;
    private $stmt = null;
    public $error = "";
    function __construct () {
        $this->pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    // (B) DESTRUCTOR - CLOSE DATABASE CONNECTION
    function __destruct () {
        if ($this->stmt!==null) { $this->stmt = null; }
        if ($this->pdo!==null) { $this->pdo = null; }
    }

    // (C) SUPPORT FUNCTION - SQL QUERY
    function query ($sql, $data) : void {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($data);
    }

    // (D) CREATE/UPDATE USER
    function save ($email, $pass, $id=null) {
        $data = [$email, password_hash($pass, PASSWORD_BCRYPT)];
        if ($id===null) {
        $this->query("INSERT INTO `users` (`user_email`, `user_password`) VALUES (?,?)", $data);
        } else {
        $data[] = $id;
        $this->query("UPDATE `users` SET `user_email`=?, `user_password`=? WHERE `user_id`=?", $data);
        }
        return true;
    }

    // (E) DELETE USER
    function del ($id) {
        $this->query("DELETE FROM `users` WHERE `user_id`=?", [$id]);
        return true;
    }

    // (F) GET USER
    function get ($id) {
        $this->query("SELECT * FROM `users` WHERE `user_".(is_numeric($id)?"id":"email")."`=?", [$id]);
        return $this->stmt->fetch();
    }

    // (G) VERIFY USER (FOR LOGIN)
    function verify ($email, $pass) {
    // (G1) GET USER
    $user = $this->get($email);
    if (!is_array($user)) { return false; }

    // (G2) PASSWORD CHECK
    if (password_verify($pass, $user["user_password"])) {
        $_SESSION["user"] = [
            "id" => $user["user_id"],
            "email" => $user["user_email"],
            "privilege" => $user["user_privilege"] // Make sure this field exists in your users table
        ];
        return true;
    } else { return false; }
}}


?>