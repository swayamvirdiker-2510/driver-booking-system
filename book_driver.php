<?php
require_once "config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION["user_id"];
$message = "";

$vehicles_sql = "
    SELECT vehicle_id, reg_no, type
    FROM vehicles
    WHERE c_id = ?
";

$stmt = $conn->prepare($vehicles_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$vehicles = $stmt->get_result();

$drivers_sql = "
    SELECT driver_id, dfname, dlname, area
    FROM drivers
    WHERE availability = 'Available'
    ORDER BY dfname
";

$drivers = $conn->query($drivers_sql);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pickup_loc = trim($_POST["pickup_loc"]);
    $drop_loc = trim($_POST["drop_loc"]);
    $bdate = $_POST["bdate"];
    $btime = $_POST["btime"];
    $duration = (int) $_POST["duration"];
    $vehicle_id = (int) $_POST["vehicle_id"];
    $driver_id = (int) $_POST["driver_id"];

    if (
        empty($pickup_loc) ||
        empty($drop_loc) ||
        empty($bdate) ||
        empty($btime) ||
        $duration <= 0 ||
        $vehicle_id <= 0 ||
        $driver_id <= 0
    ) {
        $message = "Please complete all fields.";
    } else {
        $sql = "
            INSERT INTO bookings
            (pickup_loc, drop_loc, bdate, btime, duration, c_id, v_id, d_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssiiii",
            $pickup_loc,
            $drop_loc,
            $bdate,
            $btime,
            $duration,
            $customer_id,
            $vehicle_id,
            $driver_id
        );

        if ($stmt->execute()) {
            $message = "Driver booking created successfully.";
        } else {
            $message = "Booking failed.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Driver</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <h2>Book a Driver</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($vehicles->num_rows === 0): ?>
        <p>You must add a vehicle before booking a driver.</p>
        <a class="button" href="add_vehicle.php">Add Vehicle</a>
    <?php elseif ($drivers->num_rows === 0): ?>
        <p>No drivers are currently available.</p>
    <?php else: ?>

        <form method="POST">
            <label>Pickup location</label>
            <input type="text" name="pickup_loc" required>

            <label>Drop location</label>
            <input type="text" name="drop_loc" required>

            <label>Booking date</label>
            <input type="date" name="bdate" required>

            <label>Booking time</label>
            <input type="time" name="btime" required>

            <label>Duration in hours</label>
            <input type="number" name="duration" min="1" required>

            <label>Select vehicle</label>
            <select name="vehicle_id" required>
                <option value="">Select vehicle</option>

                <?php while ($vehicle = $vehicles->fetch_assoc()): ?>
                    <option value="<?= $vehicle["vehicle_id"] ?>">
                        <?= htmlspecialchars($vehicle["reg_no"]) ?>
                        -
                        <?= htmlspecialchars($vehicle["type"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Select driver</label>
            <select name="driver_id" required>
                <option value="">Select driver</option>

                <?php while ($driver = $drivers->fetch_assoc()): ?>
                    <option value="<?= $driver["driver_id"] ?>">
                        <?= htmlspecialchars(
                            $driver["dfname"] . " " .
                            $driver["dlname"] . " - " .
                            $driver["area"]
                        ) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Confirm Booking</button>
        </form>

    <?php endif; ?>

    <p><a href="dashboard.php">Back to dashboard</a></p>
</div>

</body>
</html>
