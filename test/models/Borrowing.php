<?php
class Borrowing {
    private $conn;
    private $table = 'borrowings';

    public $borrowing_id;
    public $book_id;
    public $student_id;
    public $borrowed_at;
    public $returned_at;
    public $due_date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function borrow() {
        // First check if book is available
        $book_query = 'SELECT available_copies FROM books WHERE book_id = :book_id';
        $book_stmt = $this->conn->prepare($book_query);
        $book_stmt->bindParam(':book_id', $this->book_id);
        $book_stmt->execute();
        
        $book = $book_stmt->fetch(PDO::FETCH_ASSOC);
        
        if($book['available_copies'] < 1) {
            return false;
        }

        // Decrement available copies
        $update_query = 'UPDATE books SET available_copies = available_copies - 1 
                         WHERE book_id = :book_id';
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bindParam(':book_id', $this->book_id);
        $update_stmt->execute();

        // Create borrowing record
        $query = 'INSERT INTO ' . $this->table . '
            SET book_id = :book_id,
                student_id = :student_id,
                due_date = DATE_ADD(NOW(), INTERVAL 14 DAY),
                status = "borrowed"';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $this->book_id);
        $stmt->bindParam(':student_id', $this->student_id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function return() {
        // Update borrowing record
        $query = 'UPDATE ' . $this->table . '
            SET returned_at = NOW(),
                status = "returned"
            WHERE borrowing_id = :borrowing_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':borrowing_id', $this->borrowing_id);
        
        if(!$stmt->execute()) {
            return false;
        }

        // Increment available copies
        $update_query = 'UPDATE books SET available_copies = available_copies + 1 
                         WHERE book_id = :book_id';
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bindParam(':book_id', $this->book_id);
        
        return $update_stmt->execute();
    }
}
?>