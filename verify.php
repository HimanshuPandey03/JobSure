<?php
require_once "db_config.php";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT id FROM users WHERE verification_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {

        // Mark email as verified
        $update = $conn->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE verification_token = ?");
        $update->bind_param("s", $token);
        $update->execute();

        echo "<h2>Email Verified Successfully!</h2>";
        echo "<a href='index.php'>Click here to login</a>";

    } else {
        echo "Invalid or expired verification link.";
    }

    $stmt->close();
}
$conn->close();
?>
