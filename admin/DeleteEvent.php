<<?php
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

try {
    // Prepare and execute the query
    $event_stmt = $conn->prepare("SELECT * FROM events WHERE id = :id");
    $event_stmt->execute([':id' => $event_id]);

    // Fetch the event
    $event = $event_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die("Event not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
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
