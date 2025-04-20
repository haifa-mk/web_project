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

//get event details
$event_stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$event_stmt->bind_param("i", $event_id);
$event_stmt->execute();
$event_result = $event_stmt->get_result();

if ($event_result->num_rows === 0) {
    die("Event not found.");
}

$event = $event_result->fetch_assoc();

//check bookings
$booking_check = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE event_id = ?");
$booking_check->bind_param("i", $event_id);
$booking_check->execute();
$bookings = $booking_check->get_result()->fetch_assoc()['total'];
$hasBookings = $bookings > 0;

//if confirmed and no bookings then delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$hasBookings) {
    $del = $conn->prepare("DELETE FROM events WHERE id = ?");
    $del->bind_param("i", $event_id);
    $del->execute();
    header("Location: manageEvents.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Event</title>
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
    <h2>Delete Event</h2>

    <div class="delete-box">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete the following event?</p>
        <p><strong><?= htmlspecialchars($event['name']) ?></strong></p>
        <p><strong>Date:</strong> <?= date("F j, Y", strtotime($event['event_date'])) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
        <p><strong>Bookings:</strong> <?= $bookings ?>
            <?php if ($hasBookings): ?>
                (This event has bookings and cannot be deleted)
            <?php endif; ?>
        </p>

        <form method="POST">
    <a href="manageEvents.php" class="cancel-btn">Cancel</a>
    <button type="submit" class="delete-btn" <?= $hasBookings ? 'disabled' : '' ?>>Yes, Delete this Event</button>
        </form>
    </div>
</div>

</body>
</html>
