<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin.php");
    exit();
}

try {
    $query = "
        SELECT 
            c.name AS customer_name,
            c.email AS customer_email,
            b.booking_date,
            e.name AS event_name,
            e.event_date,
            b.num_tickets,
            b.total_price
        FROM bookings b
        JOIN customers c ON b.customer_id = c.id
        JOIN events e ON b.event_id = e.id
        ORDER BY b.booking_date DESC
    ";

    $stmt = $conn->query($query);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Bookings</title>
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
    <h2>All Bookings</h2>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Booking Date</th>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Tickets</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['customer_email']) ?></td>
                        <td><?= date("Y-m-d", strtotime($row['booking_date'])) ?></td>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= date("Y-m-d", strtotime($row['event_date'])) ?></td>
                        <td><?= htmlspecialchars($row['num_tickets']) ?></td>
                        <td>$<?= number_format($row['total_price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
