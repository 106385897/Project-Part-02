<?php
require_once 'settings.php';

// Connect to your database
$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Generate the TRUE secure hash for the word "Admin"
$real_hash = password_hash("Admin", PASSWORD_DEFAULT);

// 2. Update the database with this real hash
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'Admin'");
$stmt->bind_param("s", $real_hash);

if ($stmt->execute()) {
    echo "<h1>Success!</h1><p>The password has been fixed. You can safely delete this fix.php file now.</p>";
    echo "<a href='login.php'>Click here to go log in</a>";
} else {
    echo "Error updating record: " . $conn->error;
}

$stmt->close();
$conn->close();
?>