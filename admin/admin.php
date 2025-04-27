<?php
session_start();
include '../includes/config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT password FROM admins WHERE username = :username");
            $stmt->execute([':username' => $username]);

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $hashed_password = $row['password'];

                if (password_verify($password, $hashed_password)) {
                    $_SESSION["admin_logged_in"] = true;
                    header("Location: manageEvents.php");
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/admin_validation.js" defer></script>
</head>

<body>

<div class="container">
    <h2>Login</h2>

    <form method="POST" action="admin.php" onsubmit="return validateAdminLogin();">
        <div id="adminLoginError" style="color: red; font-size: 0.9em; margin-bottom: 10px;"></div>

        <?php if (!empty($error)): ?>
            <p id="errorMsg"><?php echo $error; ?></p>
        <?php endif; ?>

        <input type="text" id="username" name="username" placeholder="Username">
        <input type="password" id="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
