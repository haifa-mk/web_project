<?php
session_start();
require_once '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit();
}

// Get events from database
$stmt = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Booking System</title>
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>

<header>
    <div class="header-top">
        <h1>🎟️ Event Booking System</h1>
        <div class="header-buttons">
            <span>Welcome <?php echo htmlspecialchars($_SESSION['customer_name']); ?>..</span>
            <a href="cart.php">🛒 Cart</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>
</header>

<main>
    <div class="event-grid">
        <?php foreach ($events as $event): ?>
            <div class="card">
                <img src="../images/<?php echo htmlspecialchars($event['image']); ?>" alt="Event Image">
                <div class="card-content">
                    <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                    <p><?php echo date("F j, Y - g:i A", strtotime($event['event_date'])); ?></p>
                    <form action="event.php" method="GET">
                        <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                        <button type="submit" class="book-btn">Book Now</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<footer>
    &copy; <?php echo date("Y"); ?> Event Booking System. All rights reserved.
</footer>

</body>
</html>
