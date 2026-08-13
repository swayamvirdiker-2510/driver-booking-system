<?php
require_once "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $customer_sql = "
        SELECT customer_id AS user_id,
               cfname AS first_name,
               cemail AS email,
               password,
               'customer' AS role
        FROM customers
        WHERE cemail = ?
    ";

    $driver_sql = "
        SELECT driver_id AS user_id,
               dfname AS first_name,
               demail AS email,
               password,
               'driver' AS role
        FROM drivers
        WHERE demail = ?
    ";

    $stmt = $conn->prepare("
        SELECT * FROM (
            $customer_sql
            UNION ALL
            $driver_sql
        ) AS users
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->bind_param("sss", $email, $email, $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["user_name"] = $user["first_name"];
            $_SESSION["role"] = $user["role"];

            header("Location: dashboard.php");
            exit;
        }
    }

    $message = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <p>New user? <a href="register.php">Create an account</a></p>
</div>

</body>
</html>
