<?php
require_once "config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit;
}

$message = "";
$customer_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reg_no = trim($_POST["reg_no"]);
    $capacity = (int) $_POST["capacity"];
    $type = trim($_POST["type"]);

    if (empty($reg_no) || empty($capacity) || empty($type)) {
        $message = "All fields are required.";
    } else {
        $sql = "
            INSERT INTO vehicles
            (reg_no, capacity, type, c_id)
            VALUES (?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sisi",
            $reg_no,
            $capacity,
            $type,
            $customer_id
        );

        if ($stmt->execute()) {
            $message = "Vehicle added successfully.";
        } else {
            $message = "Vehicle registration number already exists.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Vehicle</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <h2>Add Vehicle</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Registration number</label>
        <input type="text" name="reg_no" required>

        <label>Vehicle capacity</label>
        <input type="number" name="capacity" min="1" required>

        <label>Vehicle type</label>
        <input type="text" name="type" placeholder="Sedan, SUV, Hatchback" required>

        <button type="submit">Add Vehicle</button>
    </form>

    <p><a href="dashboard.php">Back to dashboard</a></p>
</div>

</body>
</html>
