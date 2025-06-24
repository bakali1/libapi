<?php
class Student {
    private $conn;
    private $table = 'students';

    public $student_id;
    public $first_name;
    public $last_name;
    public $cnie;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . '
            SET first_name = :first_name,
                last_name = :last_name,
                cnie = :cnie';

        $stmt = $this->conn->prepare($query);

        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->cnie = htmlspecialchars(strip_tags($this->cnie));

        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':cnie', $this->cnie);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getBorrowedBooks() {
        $query = 'SELECT b.* FROM books b
                  JOIN borrowings br ON b.book_id = br.book_id
                  WHERE br.student_id = :student_id AND br.returned_at IS NULL';
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $this->student_id);
        $stmt->execute();
        
        return $stmt;
    }

    public function search($field, $searchTerm) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $field . ' LIKE :searchTerm';
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $searchTerm . '%';
        $stmt->bindParam(':searchTerm', $searchTerm);
        $stmt->execute();
        return $stmt;
    }
}
?>