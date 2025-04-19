<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Events</title>
</head>
<body>
    <h1>Manage Events</h1>

</body>
</html>
