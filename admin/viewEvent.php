<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "No event selected.";
    exit();
}

$event_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Event not found.";
    exit();
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title>View Event</title>
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
        <h2>Event Details</h2>

        <?php if (!empty($event['image'])): ?>
            <img src="../images/<?= $event['image'] ?>" class="view-image">
        <?php endif; ?>

        <div >
            <h3><?= htmlspecialchars($event['name']) ?></h3>
            <p><strong>Date:</strong> <?= date("F j, Y", strtotime($event['event_date'])) ?></p>
            <p><strong>Time:</strong> <?= date("g:i A", strtotime($event['event_date'])) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
            <p><strong>Price:</strong> $<?= htmlspecialchars($event['ticket_price']) ?></p>
            <p><strong>Available Tickets:</strong> <?= htmlspecialchars($event['max_tickets']) ?></p>
            <p><strong>Description:</strong> <?= !empty($event['description']) ? htmlspecialchars($event['description']) : "No description provided." ?></p>
            <br>
            <a href="manageEvents.php">Back to Events</a>
        </div>
    </div>

</body>
</html>
