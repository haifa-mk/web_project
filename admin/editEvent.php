<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No event selected.");
}

$event_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Event not found.");
}

$event = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $ticket_price = $_POST['ticket_price'];
    $max_tickets = $_POST['max_tickets'];


    $image = $event['image']; // keep existing image 
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp_name, "../images/" . $image);
    }

    $update = $conn->prepare("UPDATE events SET name = ?, event_date = ?, location = ?, ticket_price = ?, max_tickets = ?, image = ? WHERE id = ?");
    $update->bind_param("sssdisi", $name, $event_date, $location, $ticket_price, $max_tickets, $image, $event_id);
    $update->execute();
if ($update->affected_rows > 0) {
    echo "Update successful!";
} else {
    echo "Update failed or no changes made.";
}
exit();

    header("Location: manageEvents.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-container">

<div class="sidebar">
    <h3>Admin Panel</h3>
    <a href="manageEvents.php">Manage Events</a>
    <a href="addEvent.php">Add Event</a>
    <a href="viewBookings.php">View Bookings</a>
    <a href="logout.php">Logout</a>
</div>

<div class="admin-main">
    <form action="" method="POST" enctype="multipart/form-data" class="event-form">
        <h2>Edit Event</h2>

        <label for="name">Event Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($event['name']) ?>" required>

        <label for="event_date">Event Date:</label>
        <input type="datetime-local" name="event_date" value="<?= date('Y-m-d\TH:i', strtotime($event['event_date'])) ?>" required>

        <label for="location">Location:</label>
        <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>

        <label for="ticket_price">Ticket Price (SAR):</label>
        <input type="number" step="0.01" name="ticket_price" value="<?= $event['ticket_price'] ?>" required>

        <label for="max_tickets">Maximum Tickets:</label>
        <input type="number" name="max_tickets" value="<?= $event['max_tickets'] ?>" required>

        <label for="image">Event Image:</label>
        <input type="file" name="image" accept="image/*">
        <small>Current image: <?= htmlspecialchars($event['image']) ?></small>


        <button type="submit" class="update-btn">Update Event</button>
    </form>
</div>

</body>
</html>
