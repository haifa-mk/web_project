<?php
require_once '../includes/config.php';
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // Validations
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check unique email
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password and insert
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword]);
            $success = "Registered successfully. Redirecting to login...";
            header("refresh:2;url=index.php");
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <script>
    function validateRegisterForm(e) {
    e.preventDefault(); // Stop form submission first

    const name = document.forms["regForm"]["name"].value.trim();
    const email = document.forms["regForm"]["email"].value.trim();
    const password = document.forms["regForm"]["password"].value;
    const confirm = document.forms["regForm"]["confirm"].value;
    const errorDiv = document.getElementById("errorMsg");

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    let error = "";

    if (!name || !email || !password || !confirm) {
        error = "All fields are required.";
    } else if (!emailPattern.test(email)) {
        error = "Invalid email format.";
    } else if (password !== confirm) {
        error = "Passwords do not match.";
    }

    if (error) {
        errorDiv.innerText = error;
    } else {
        document.forms["regForm"].submit(); // Submit if no problems
    }
}
</script>
</head>
<body>
    <h2>Register</h2>
    <form name="regForm" method="POST" onsubmit="validateRegisterForm(event)">
        <input type="text" name="name" required placeholder="Name"><br>
        <input type="email" name="email" required placeholder="Email"><br>
        <input type="password" name="password" required placeholder="Password"><br>
        <input type="password" name="confirm" required placeholder="Confirm Password"><br>
        <button type="submit">Register</button>
    </form>
    <p id="errorMsg" style="color:red;"><?php echo $error; ?></p>
    <p style="color:green;"><?php echo $success; ?></p>
</body>
</html>
