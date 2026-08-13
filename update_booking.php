<?php
require_once "config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "driver") {
    header("Location: login.php");
    exit;
}

$driver_id = $_SESSION["user_id"];
$booking_id = (int) $_POST["booking_id"];
$status = $_POST["status"];

$allowed_statuses = [
    "Accepted",
    "Rejected",
    "Completed"
];

if (!in_array($status, $allowed_statuses, true)) {
    die("Invalid booking status.");
}

$sql = "
    UPDATE bookings
    SET status = ?
    WHERE booking_id = ?
    AND d_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $status, $booking_id, $driver_id);
$stmt->execute();

header("Location: dashboard.php");
exit;
?>
