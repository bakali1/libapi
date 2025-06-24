<?php
class BookLib {
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
    function query ($sql, $data=null) : void {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($data);
    }

    // (D) CREATE/UPDATE BOOK
    function save ($title, $author, $year, $quantity, $privilege, $id=null) {
        // Validate privilege level
        $validPrivileges = ['P', 'C', 'L', 'A'];
        if (!in_array($privilege, $validPrivileges)) {
            $this->error = "Invalid privilege level";
            return false;
        }
        
        $data = [$title, $author, $year, $quantity, $privilege];
        if ($id===null) {
            $this->query("INSERT INTO `books` (`book_title`, `author`, `year`, `number_of_books`, `level_of_privilege`) VALUES (?,?,?,?,?)", $data);
        } else {
            $data[] = $id;
            $this->query("UPDATE `books` SET `book_title`=?, `author`=?, `year`=?, `number_of_books`=?, `level_of_privilege`=? WHERE `book_id`=?", $data);
        }
        return true;
    }

    // (E) DELETE BOOK
    function del ($id) {
        $this->query("DELETE FROM `books` WHERE `book_id`=?", [$id]);
        return true;
    }

    // (F) GET BOOK
function get($id) {
    // Fetch the book by its ID
    $this->query("SELECT * FROM `books` WHERE `book_id` = ?", [$id]);
    $data = $this->stmt->fetch();
    if (!$data) {
        $this->error = "Book not found";
        return false;
    }
    // if null give them an P level of privilege
    $userPrivilege = $_SESSION['user']['privilege'] ?? 'P';

    // Allow if user is admin
    if ($userPrivilege === 'A') {
        return $data;
    }

    // Allow only if user's level matches the book's level
    if ($data['level_of_privilege'] === $userPrivilege) {
        return $data;
    }
    // Otherwise, deny access
    $this->error = "Insufficient privileges to view this book";
    return false;
}


    // (G) GET ALL BOOKS
    function getAll() {
        $privilege = $_SESSION['user']['privilege'] ?? 'P';

        if ($privilege === 'A') {
            $this->query("SELECT * FROM `books`");
        } else {
            $this->query("SELECT * FROM `books` WHERE `level_of_privilege` = ?", [$privilege]);
        }

        return $this->stmt->fetchAll();
    }


    // (H) SEARCH BOOKS
    function search ($query) {
        $search = "%$query%";
        $privilege = $_SESSION['user']['privilege'] ?? 'P';
        if($privilege === 'A') {
            $this->query("SELECT * FROM `books` WHERE `book_title` LIKE ? OR `author` LIKE ?", [$search, $search]);
            return $this->stmt->fetchAll();
        }
        $this->query("SELECT * FROM `books` WHERE (`book_title` LIKE ? OR `author` LIKE ?) AND `level_of_privilege` = ?", [$search, $search, $privilege]);
        return $this->stmt->fetchAll();
    }
}

// (I) DATABASE SETTINGS - CHANGE TO YOUR OWN!
define("DB_HOST", "mysql-libapi.alwaysdata.net");
define("DB_NAME", "libapi_database");
define("DB_CHARSET", "utf8mb4");
define("DB_USER", "libapi");
define("DB_PASSWORD", "Bakali1");

// (J) START!
session_start();
$LIB = new BookLib();
