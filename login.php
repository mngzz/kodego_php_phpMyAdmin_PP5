<?php
session_start();
require_once 'User.php';
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = new User($pdo);
    if ($user->login($username, $password)) {
        $_SESSION['user_id'] = $user->getUserIdByUsername($username);
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
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
        <h1>Login</h1>
        <form method="post">
            <input type="text" class="username" name="username" required placeholder="Username">
            <input type="password" class="password" name="password" required placeholder="Password">
            <button type="submit" class="button">Login</button>
        </form>
        <p>New user? Sign up <a href="register.php">here</a>.</p>
        <?php if (isset($error)) echo '<p>' . $error . '</p>'; ?>
        </div>
    </div>
</body>
</html>
