<?php
session_start();
if(!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

include_once 'config/Database.php';
include_once 'models/Book.php';

$database = new Database();
$db = $database->connect();

$book = new Book($db);
$books = $book->search('title', '')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Dashboard</title>
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
        <h2>Book Management</h2>
        
        <div class="actions">
            <a href="add_book.php" class="btn">Add New Book</a>
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search books...">
                <button type="submit" class="btn">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($books as $book): ?>
                <tr>
                    <td><?php echo $book['code']; ?></td>
                    <td><?php echo $book['title']; ?></td>
                    <td><?php echo $book['author']; ?></td>
                    <td><?php echo $book['year']; ?></td>
                    <td><?php echo $book['available_copies']; ?>/<?php echo $book['total_copies']; ?></td>
                    <td>
                        <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="btn">Edit</a>
                        <a href="delete_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>