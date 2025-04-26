<?php
session_start();
require_once '../includes/config.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['customer_id'] = $user['id'];
        $_SESSION['customer_name'] = $user['name'];
        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script>
    function validateLoginForm(e) {
        e.preventDefault();

        const email = document.forms["loginForm"]["email"].value.trim();
        const password = document.forms["loginForm"]["password"].value;
        const errorDiv = document.getElementById("errorMsg");

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!email || !password) {
            errorDiv.innerText = "Email and password are required.";
        } else if (!emailPattern.test(email)) {
            errorDiv.innerText = "Invalid email format.";
        } else {
            document.forms["loginForm"].submit(); // only submit if everything is good
        }
    }
    </script>
</head>
<body>
    <h2>Login</h2>
    <form name="loginForm" method="POST" onsubmit="validateLoginForm(event)">
        <input type="email" name="email" required placeholder="Email"><br>
        <input type="password" name="password" required placeholder="Password"><br>
        <button type="submit">Login</button>
        <p style="color:red;"><?php echo $error; ?></p>
    </form>
    <p>Not a member yet? <a href="register.php">Register here</a></p>
</body>
</html>
