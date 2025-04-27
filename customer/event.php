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
    <link rel="stylesheet" href="../css/home.css">
    <style>
        /* Reset */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html, body {
  height: 100%;
  font-family: 'Jost', sans-serif;
  background: linear-gradient(to bottom, #3b0a70, #5f0f99, #7b2cbf);
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

body.page-content {
  background: linear-gradient(to bottom, #3b0a70, #5f0f99, #7b2cbf); 
  height: auto;
  display: block;
  overflow-x: hidden;
  font-family: 'Jost', sans-serif;
}

/* Shared Form Layout */
.main, .container {
  width: 100%;
  max-width: 400px;
  background: #ffffff;
  padding: 30px 20px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(123, 44, 191, 0.3); /* softer purple shadow */
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Form Title */
h2 {
  color: #7b2cbf;
  font-size: 2em;
  font-weight: bold;
  margin-bottom: 20px;
  text-align: center;
}

/* Form Structure */
form {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Input Fields */
input[type="text"],
input[type="email"],
input[type="password"],
input[type="submit"],
button {
  width: 90%;
  background: #f3f0ff; /* very light purple */
  margin: 10px 0;
  padding: 12px;
  border: none;
  outline: none;
  border-radius: 8px;
  font-size: 1em;
  transition: background-color 0.3s;
}

/* Buttons */
button, input[type="submit"] {
  background: #7b2cbf;
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover, input[type="submit"]:hover {
  background: #5a189a;
}

/* Error and Success Messages */
#errorMsg {
  color: #ff4d4d; /* lighter, soft red */
  margin-top: 10px;
  text-align: center;
}

.successMsg {
  color: #28a745; /* nice green for success */
  margin-top: 10px;
  text-align: center;
}

/* Responsive Design */
@media (max-width: 600px) {
  .main, .container {
    margin: 20px;
    padding: 20px;
    max-width: 90%;
  }

  input[type="text"],
  input[type="email"],
  input[type="password"],
  button,
  input[type="submit"] {
    width: 100%;
  }
}
    </style>
</head>
<body class="page-content">


<header>
    <h1>Event Booking System</h1>
    <div>
        <span>Welcome, <?= $_SESSION['customer_name'] ?? 'Guest'; ?> | </span>
        <a href="home.php">Home</a>  <a href="cart.php">Cart</a> | <a href="logout.php">Logout</a>
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

