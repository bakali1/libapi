<?php
class Book {
    private $conn;
    private $table = 'books';

    public $book_id;
    public $code;
    public $title;
    public $author;
    public $year;
    public $total_copies;
    public $available_copies;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . '
            SET code = :code,
                title = :title,
                author = :author,
                year = :year,
                total_copies = :total_copies,
                available_copies = :available_copies';

        $stmt = $this->conn->prepare($query);

        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->author = htmlspecialchars(strip_tags($this->author));

        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':total_copies', $this->total_copies);
        $stmt->bindParam(':available_copies', $this->available_copies);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE book_id = :book_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $this->book_id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    public function search($criteria, $value) {
        $validColumns = ['code', 'title', 'author', 'year'];
        
        if(!in_array($criteria, $validColumns)) {
            return false;
        }

        $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $criteria . ' LIKE :value';
        $stmt = $this->conn->prepare($query);
        $value = '%' . $value . '%';
        $stmt->bindParam(':value', $value);
        $stmt->execute();

        return $stmt;
    }
}
?>