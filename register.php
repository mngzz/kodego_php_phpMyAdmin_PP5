<?php
session_start();
require_once 'User.php';
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = new User($pdo);

    if ($user->getUserIdByUsername($username)) {
        $error = 'Username already exists. Please choose a different username.';
    } else {
        $user->register($username, $password);
        $_SESSION['user_id'] = $user->getUserIdByUsername($username);
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Donuts</title>
</head>
<body>
    <div class="container">
        <div class="card" style="padding-bottom: 35px;">
            <h1>Register now</h1>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="post">
                <input type="text" class="username" name="username" required placeholder="Username">
                <input type="password" class="password" name="password" required placeholder="Password">
                <button type="submit" class="button">Register</button>
            </form>
            <p>Already have an account? Sign in <a href="login.php">here</a>.</p>
        </div>
    </div>
</body>
</html>
