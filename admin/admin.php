<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    }
    // ✅ Only check credentials if fields are not empty
    else if ($username === "admin" && $password === "admin123") {
        $_SESSION["admin_logged_in"] = true;
        header("Location: manageEvents.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Jost&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <div class="main">
    <div class="login">
      <form method="POST" action="admin.php">
        <label>Login</label>

        <?php if (!empty($error)): ?>
          <p style="color: red; font-size: 0.9em; margin-bottom: 10px;">
            <?php echo $error; ?>
          </p>
        <?php endif; ?>

        <input type="text" name="username" placeholder="Username" >
        <input type="password" name="password" placeholder="Password" >
        <button type="submit">Login</button>
      </form>
    </div>
  </div>

</body>
</html>
