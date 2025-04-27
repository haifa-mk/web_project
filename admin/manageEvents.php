<?php
session_start();
include '../includes/config.php'; // Must be PDO connection inside config.php now!

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title>Manage Events</title>
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
        <h2>Manage Events</h2>

        <table class="admin-table">
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>

            <?php
            try {
                $stmt = $conn->query("SELECT * FROM events");
                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($events) > 0) {
                    foreach ($events as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars(date('Y-m-d', strtotime($row['event_date']))) . "</td>";
                        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                        echo "<td class='actions'>
                                <a class='view' href='viewEvent.php?id=" . $row['id'] . "'>View</a>
                                <a class='edit' href='editEvent.php?id=" . $row['id'] . "'>Edit</a>
                                <a class='delete' href='deleteEvent.php?id=" . $row['id'] . "'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No events found.</td></tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='4'>Error fetching events: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </table>
    </div>

</body>
</html>
