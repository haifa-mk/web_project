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
            'event_name' => $event['name'],
            'event_date' => $event['event_date'],
            'price' => $event['price'],
            'quantity' => $quantity,
            'total' => $event['price'] * $quantity
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
    <link rel="stylesheet" href="../css/home.css">
    <style>
        .event-container {
            max-width: 700px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 15px #ccc;
            border-radius: 8px;
        }
        .event-img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .event-info {
            margin-top: 20px;
        }
        .event-info h2 {
            margin-bottom: 10px;
        }
        form {
            margin-top: 20px;
        }
        select, button {
            padding: 10px;
            margin-top: 10px;
        }
        .error { color: red; }
    </style>
</head>
<body>


<header>
    <h1>Event Booking System</h1>
    <div>
        <span>Welcome, <?= $_SESSION['customer_name'] ?? 'Guest'; ?> | </span>
        <a href="cart.php">Cart</a> | <a href="logout.php">Logout</a>
    </div>
</header>

<main>
    <div class="event-container">
        <img src="../images/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['name']) ?>" class="event-img">
        <div class="event-info">
            <h2><?= htmlspecialchars($event['name']) ?></h2>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
            <p><strong>Price:</strong> $<?= htmlspecialchars($event['price']) ?></p>
            <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
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

