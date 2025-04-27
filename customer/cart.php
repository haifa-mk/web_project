<?php
session_start();
require_once '../includes/config.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: ../customer/index.php');
    exit();
}


$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];


$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['quantity'] * $item['price'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_date = date('Y-m-d H:i:s');

    foreach ($cart as $item) {
        $event_id = $item['event_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $total = $quantity * $price;

        // إدخال الحجز في قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, quantity, total_price, booking_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $event_id, $quantity, $total, $current_date]);


        // تحديث التذاكر المتاحة
        $stmt_update = $conn->prepare("UPDATE events SET available_tickets = available_tickets - ? WHERE id = ?");
        $stmt_update->execute([$quantity, $event_id]);
    }

    // تفريغ السلة
    unset($_SESSION['cart']);
    header('Location: home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main>
        <h1>Your Cart</h1>
        <?php if (!empty($cart)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['event_name']) ?></td>
                            <td><?= htmlspecialchars($item['event_date']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td><?= htmlspecialchars($item['price']) ?> SAR</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Total Price:</strong> <?= $total_price ?> SAR</p>
            <p><strong>Date and Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
            <form method="POST">
                <button type="submit">Reserve Tickets</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty!</p>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
