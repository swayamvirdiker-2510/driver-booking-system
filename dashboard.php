<?php
require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav>
    <h2>Dashboard</h2>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <h2>
        Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?>
    </h2>

    <?php if ($role === "customer"): ?>

        <p>You are logged in as a customer.</p>

        <a class="button" href="add_vehicle.php">
            Add Vehicle
        </a>

        <a class="button" href="book_driver.php">
            Book a Driver
        </a>

        <h3>Your Bookings</h3>

        <?php
        $sql = "
            SELECT
                b.booking_id,
                b.pickup_loc,
                b.drop_loc,
                b.bdate,
                b.btime,
                b.status,
                d.dfname,
                d.dlname,
                v.reg_no
            FROM bookings b
            JOIN drivers d ON b.d_id = d.driver_id
            JOIN vehicles v ON b.v_id = v.vehicle_id
            WHERE b.c_id = ?
            ORDER BY b.created_at DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $bookings = $stmt->get_result();

        while ($booking = $bookings->fetch_assoc()):
        ?>

            <div class="card">
                <p>
                    <strong>Booking ID:</strong>
                    <?= $booking["booking_id"] ?>
                </p>

                <p>
                    <?= htmlspecialchars($booking["pickup_loc"]) ?>
                    to
                    <?= htmlspecialchars($booking["drop_loc"]) ?>
                </p>

                <p>
                    Date: <?= $booking["bdate"] ?>
                    Time: <?= $booking["btime"] ?>
                </p>

                <p>
                    Driver:
                    <?= htmlspecialchars($booking["dfname"] . " " . $booking["dlname"]) ?>
                </p>

                <p>
                    Vehicle: <?= htmlspecialchars($booking["reg_no"]) ?>
                </p>

                <p>Status: <?= htmlspecialchars($booking["status"]) ?></p>
            </div>

        <?php endwhile; ?>

    <?php elseif ($role === "driver"): ?>

        <p>You are logged in as a driver.</p>

        <h3>Assigned Bookings</h3>

        <?php
        $sql = "
            SELECT
                b.booking_id,
                b.pickup_loc,
                b.drop_loc,
                b.bdate,
                b.btime,
                b.status,
                c.cfname,
                c.clname,
                c.phone_no
            FROM bookings b
            JOIN customers c ON b.c_id = c.customer_id
            WHERE b.d_id = ?
            ORDER BY b.created_at DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $bookings = $stmt->get_result();

        while ($booking = $bookings->fetch_assoc()):
        ?>

            <div class="card">
                <p>
                    <strong>Booking ID:</strong>
                    <?= $booking["booking_id"] ?>
                </p>

                <p>
                    <?= htmlspecialchars($booking["pickup_loc"]) ?>
                    to
                    <?= htmlspecialchars($booking["drop_loc"]) ?>
                </p>

                <p>
                    Date: <?= $booking["bdate"] ?>
                    Time: <?= $booking["btime"] ?>
                </p>

                <p>
                    Customer:
                    <?= htmlspecialchars($booking["cfname"] . " " . $booking["clname"]) ?>
                </p>

                <p>Phone: <?= htmlspecialchars($booking["phone_no"]) ?></p>

                <p>Status: <?= htmlspecialchars($booking["status"]) ?></p>

                <form method="POST" action="update_booking.php">
                    <input
                        type="hidden"
                        name="booking_id"
                        value="<?= $booking["booking_id"] ?>"
                    >

                    <select name="status" required>
                        <option value="Accepted">Accept</option>
                        <option value="Rejected">Reject</option>
                        <option value="Completed">Completed</option>
                    </select>

                    <button type="submit">Update Status</button>
                </form>
            </div>

        <?php endwhile; ?>

    <?php endif; ?>
</div>

</body>
</html>
