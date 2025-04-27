<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $ticket_price = $_POST['ticket_price'];
    $max_tickets = $_POST['max_tickets'];
  
    
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    move_uploaded_file($image_tmp, "../images/" . $image);

    $stmt = $conn->prepare("INSERT INTO events (name, event_date, location, ticket_price, max_tickets, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdis", $name, $event_date, $location, $ticket_price, $max_tickets, $image);
    $stmt->execute();

    header("Location: manageEvents.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <link rel="stylesheet" href="../css/admin.css">
    <script src="../js/admin_validation.js" defer></script>
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
    <?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $ticket_price = $_POST['ticket_price'];
    $max_tickets = $_POST['max_tickets'];
  
    
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    move_uploaded_file($image_tmp, "../images/" . $image);

    $stmt = $conn->prepare("INSERT INTO events (name, event_date, location, ticket_price, max_tickets, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdis", $name, $event_date, $location, $ticket_price, $max_tickets, $image);
    $stmt->execute();

    header("Location: manageEvents.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <link rel="stylesheet" href="../css/admin.css">
    <script src="../js/admin_validation.js" defer></script>
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
    <form action="" method="POST" enctype="multipart/form-data" class="event-form" onsubmit="return validateAddEvent();">
            <h2>Add New Event</h2>

            <label for="eventName">Event Name:</label>
            <input type="text" name="name" id="eventName" required>

            <label for="eventDate">Event Date:</label>
            <input type="datetime-local" name="event_date" id="eventDate" required>

            <label for="location">Location:</label>
            <input type="text" name="location" id="location" required>

            <label for="ticketPrice">Ticket Price ($):</label>
            <input type="number" name="ticket_price" id="ticketPrice" step="0.01" required>

            <label for="maxTickets">Maximum Tickets:</label>
            <input type="number" name="max_tickets" id="maxTickets" required>

            <label for="eventImage">Event Image:</label>
            <input type="file" name="image" id="eventImage" accept="image/*" required>

            <div id="eventAddError" style="color: red; margin-bottom: 10px;"></div> <!-- Div for JS error messages -->

            <button type="submit">Add Event</button>
        </form>
    </div>

</body>
</html>

    </div>

</body>
</html>
