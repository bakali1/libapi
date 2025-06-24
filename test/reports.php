<?php
session_start();
if(!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

include_once 'Database.php';
include_once 'Borrowing.php';

$database = new Database();
$db = $database->connect();

$borrowing = new Borrowing($db);

// Get overdue books
$query = 'SELECT b.title, b.code, s.first_name, s.last_name, br.due_date 
          FROM borrowings br
          JOIN books b ON br.book_id = b.book_id
          JOIN students s ON br.student_id = s.student_id
          WHERE br.returned_at IS NULL AND br.due_date < NOW()';
$overdue = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Get most popular books
$query = 'SELECT b.title, COUNT(br.borrowing_id) as borrow_count
          FROM books b
          LEFT JOIN borrowings br ON b.book_id = br.book_id
          GROUP BY b.book_id
          ORDER BY borrow_count DESC
          LIMIT 5';
$popular = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Reports</title>
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
        <h2>Library Reports</h2>
        
        <div class="report-section">
            <h3>Overdue Books</h3>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Book Code</th>
                        <th>Student</th>
                        <th>Due Date</th>
                        <th>Days Overdue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($overdue as $item): ?>
                    <tr>
                        <td><?php echo $item['title']; ?></td>
                        <td><?php echo $item['code']; ?></td>
                        <td><?php echo $item['first_name'] . ' ' . $item['last_name']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($item['due_date'])); ?></td>
                        <td><?php echo floor((time() - strtotime($item['due_date'])) / (60 * 60 * 24)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="report-section">
            <h3>Most Popular Books</h3>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrow Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($popular as $item): ?>
                    <tr>
                        <td><?php echo $item['title']; ?></td>
                        <td><?php echo $item['borrow_count']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>