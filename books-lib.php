<?php

class BookLib{
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

    // (D) CREATE/UPDATE BOOK
    function save ($book_title, $author, $year, $number_of_books, $level_of_privilege, $id=null) {
        $data = [$book_title, $author, $year, $number_of_books, $level_of_privilege];
        if ($id===null) {
        $this->query("INSERT INTO `books` (`book_title`, `author`, `year`,`number_of_books`,`level_of_privilege`) VALUES (?,?,?,?,?)", $data);
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
    function get ($id) {
        $this->query("SELECT * FROM `books` WHERE `book_id`=?", [$id]);
        return $this->stmt->fetch();
    }

    function all () {
        $this->query("SELECT * FROM `books`", []);
        return $this->stmt->fetchAll();
    }


    // (I) START!

}
    define("DB_HOST", "mysql-libapi.alwaysdata.net");
    define("DB_NAME", "libapi_database");
    define("DB_CHARSET", "utf8mb4");
    define("DB_USER", "libapi");
    define("DB_PASSWORD", "Bakali1");

// (I) START!
session_start();
$Book = new BookLib();

?>