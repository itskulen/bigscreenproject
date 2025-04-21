<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if ($user === 'admin' && $pass === 'admin') {
        $_SESSION['logged_in'] = true;
        header(header: 'Location: admin.php');
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<form method="POST">
    <h2>Login Admin</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</form>