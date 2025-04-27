 <?php
 
session_start();
include('../includes/config.php');


if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$event_id = intval($_GET['id']);

$query = "SELECT * FROM events WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $event_id, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Event not found.";
    exit();
}


//  Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = intval($_POST['quantity']);
    if ($quantity <= 0 || $quantity > $event['max_tickets']) {
        $error = "Invalid ticket quantity selected.";
    } else {
        $_SESSION['cart'][$event_id] = [
              'event_id' => $event_id,
            'event_name' => $event['name'],
            'event_date' => $event['event_date'],
            'price' => $event['ticket_price'],
            'quantity' => $quantity,
            'total' => $event['ticket_price'] * $quantity
        ];
        header("Location: cart.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>


<header>
  <div class="header-top">
    <h1>🎟️ Event Booking System</h1>
    <div class="header-buttons">
      <span>Welcome, <?= $_SESSION['customer_name'] ?? 'Guest'; ?></span>
  
      <a href="cart.php">🛒 Cart</a>
      <a href="logout.php">🚪 Logout</a>
    </div>
  </div>
</header>



<main>
    <div class="event-container">
        <img src="../images/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['name']) ?>" class="event-img">
        <div class="event-info">
            <h2><?= htmlspecialchars($event['name']) ?></h2>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
            <p><strong>Price:</strong> $<?= htmlspecialchars($event['ticket_price']) ?></p>
      
        </div>

        <form method="POST">
            <label for="quantity">Select Number of Tickets:</label><br>
            <select name="quantity" id="quantity" required>
                <?php for ($i = 1; $i <= $event['max_tickets']; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select><br>
            <button type="submit">Add to Cart</button>
            <?php if (isset($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
        </form>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> Event Booking System</p>
</footer>

</body>
</html>

