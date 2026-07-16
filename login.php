<?php
session_start();
require_once 'settings.php'; 

// Establish the database connection using your settings.php variables
$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // SECURE: Prepared statement to prevent SQL Injection
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // SECURE: Verify the hashed password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: manage.php");
            exit;
        } else {
            $error_msg = "Invalid password.";
        }
    } else {
        $error_msg = "Invalid username.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Manager Login</title>
    <link rel="stylesheet" href="styles/styles.css"> 
</head>
<body>

<?php require_once("header.inc"); ?>

 <?php require_once("nav.inc"); ?>

    <main>
        <div class="login-container">
            <h2>HR Manager Login</h2>
            
            <?php if($error_msg != '') echo "<p style='color:red; text-align:center;'>$error_msg</p>"; ?>
            
            <form method="POST" action="login.php" class="login-form">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Log In</button>
            </form>
        </div>
    </main>

    <?php require_once("footer.inc"); ?>


</body>
</html>