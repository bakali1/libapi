<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simple admin login (in real app, use proper authentication)
    if($_POST['username'] === 'admin' && $_POST['password'] === 'admin123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = 'Admin';
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Library System Login</h1>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>