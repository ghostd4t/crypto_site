<?php
session_start();

// Retrieve user ID and email from query parameters
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$user_email = isset($_GET['user_email']) ? $_GET['user_email'] : '';

// Store user information in session variables
$_SESSION['user_id'] = $user_id;
$_SESSION['user_email'] = $user_email;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
<h1>Welcome to the User Dashboard</h1>
<p>You are now logged in. Here is some user-specific information:</p>
<ul>
    <li>User ID: <?php echo $_SESSION['user_id']; ?></li>
    <li>User Email: <?php echo $_SESSION['user_email']; ?></li>
</ul>
<a href="logout.php">Logout</a>
</body>
</html>
