<?php
include 'config.php';

$new_password = password_hash("123admin", PASSWORD_DEFAULT); // Hash new password
$query = "UPDATE account SET pw = ? WHERE un = 'admin'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $new_password);

if ($stmt->execute()) {
    echo "Admin password has been reset to '123admin'.";
} else {
    echo "Error updating password!";
}

$stmt->close();
?>
