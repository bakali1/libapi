<?php
// add_book.php
session_start();
if(!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once 'config/Database.php';
    include_once 'models/Book.php';

    $database = new Database();
    $db = $database->connect();

    $book = new Book($db);
    
    $book->code = $_POST['code'];
    $book->title = $_POST['title'];
    $book->author = $_POST['author'];
    $book->year = $_POST['year'];
    $book->total_copies = $_POST['total_copies'];
    $book->available_copies = $_POST['available_copies'];

    if($book->create()) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Failed to add book";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Book</title>
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
        <h2>Add New Book</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Code:</label>
                <input type="text" name="code" required>
            </div>
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Author:</label>
                <input type="text" name="author" required>
            </div>
            <div class="form-group">
                <label>Year:</label>
                <input type="number" name="year">
            </div>
            <div class="form-group">
                <label>Total Copies:</label>
                <input type="number" name="total_copies" value="1" min="1" required>
            </div>
            <div class="form-group">
                <label>Available Copies:</label>
                <input type="number" name="available_copies" value="1" min="0" required>
            </div>
            <button type="submit" class="btn">Add Book</button>
        </form>
    </main>
</body>
</html>