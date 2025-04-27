<?php
// Display PHP errors (for debugging, remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: ../customer/index.php');
    exit();
}

// Fetch the cart
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Calculate total price safely
$total_price = 0;
foreach ($cart as $item) {
    $item_price = $item['price'] ?? 0;
    $total_price += $item['quantity'] * $item_price;
}

// Handle reservation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['customer_id'];
    $current_date = date('Y-m-d H:i:s');

    try {
        foreach ($cart as $item) {
            if (!isset($item['event_id'])) {
                throw new Exception("Missing event ID in cart item.");
            }

            $event_id = $item['event_id'];
            $quantity = $item['quantity'];
            $price = $item['price'] ?? 0;
            $total = $quantity * $price;

            // Insert booking into database
            $stmt = $conn->prepare("INSERT INTO bookings (customer_id, event_id, num_tickets, total_price, booking_date) 
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $event_id, $quantity, $total, $current_date]);

            // Update max_tickets in events table
            $stmt_update = $conn->prepare("UPDATE events SET max_tickets = max_tickets - ? WHERE id = ?");
            $stmt_update->execute([$quantity, $event_id]);
        }

        // Clear the cart
        unset($_SESSION['cart']);

        // Show success message and redirect after 3 seconds
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Reservation Successful</title>
            <meta http-equiv='refresh' content='3;url=home.php'>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    padding: 100px;
                    background-color: #f8f8f8;
                }
                .success-message {
                    font-size: 24px;
                    color: green;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class='success-message'>🎉 Your reservation was successful!</div>
            <p>You will be redirected to the homepage shortly...</p>
        </body>
        </html>";
        exit();

    } catch (Exception $e) {
        echo "<h2 style='color: red;'>Error occurred:</h2>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
  
</head>
<header>
    <h1>Event Booking System</h1>
    <div>
        <span>Welcome, <?= $_SESSION['customer_name'] ?? 'Guest'; ?> | </span>
        <a href="home.php">Home</a> | <a href="logout.php">Logout</a>
    </div>
</header>
<body>

<main>
    <h1>Your Cart</h1>

    <?php if (!empty($cart)): ?>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Quantity</th>
                    <th>Price (per ticket)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['event_name']) ?></td>
                        <td><?= htmlspecialchars($item['event_date']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars(number_format($item['price'] ?? 0, 2)) ?> SAR</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><strong>Total Price:</strong> <?= number_format($total_price, 2) ?> SAR</p>
        <p><strong>Date and Time:</strong> <?= date('Y-m-d H:i:s') ?></p>

        <form method="POST">
            <button type="submit">Reserve Tickets</button>
        </form>

    <?php else: ?>
        <p>Your cart is empty!</p>
    <?php endif; ?>
</main>
<footer>
    <p>&copy; <?= date("Y") ?> Event Booking System</p>
</footer>

</body>
</html>
