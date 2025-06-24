<?php
session_start();
if(!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

include_once 'config/Database.php';
include_once 'models/Book.php';
include_once 'models/Student.php';
include_once 'models/Borrowing.php';

$database = new Database();
$db = $database->connect();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $borrowing = new Borrowing($db);
    $borrowing->book_id = $_POST['book_id'];
    $borrowing->student_id = $_POST['student_id'];
    
    if($borrowing->borrow()) {
        header('Location: borrowings.php');
        exit;
    } else {
        $error = "Failed to borrow book (may be unavailable)";
    }
}

// Get all books
$book = new Book($db);
$books = $book->search('title', '')->fetchAll(PDO::FETCH_ASSOC);

// Get all students
$student = new Student($db);
$students = $student->search('last_name', '')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Borrow Book</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Library Management System</h1>
        <nav>
            <a href="dashboard.php">Books</a>
            <a href="students.php">Students</a>
            <a href="borrowings.php">Borrowings</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <h2>Borrow a Book</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Book:</label>
                <select name="book_id" required>
                    <?php foreach($books as $book): ?>
                        <option value="<?php echo $book['book_id']; ?>">
                            <?php echo $book['title']; ?> (Available: <?php echo $book['available_copies']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Student:</label>
                <select name="student_id" required>
                    <?php foreach($students as $student): ?>
                        <option value="<?php echo $student['student_id']; ?>">
                            <?php echo $student['first_name'] . ' ' . $student['last_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn">Borrow Book</button>
        </form>
    </main>
</body>
</html>