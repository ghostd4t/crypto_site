<?php
session_start();

// Retrieve user ID and email from query parameters
$user_id = isset($_GET['user_id'])? (int)$_GET['user_id'] : 0;
$user_email = isset($_GET['user_email'])? $_GET['user_email'] : '';

// Retrieve referral code from database
$db_connect = mysqli_connect("localhost", "root", "", "cryptousersdb") or die("Error connecting to the database");

$sql = "SELECT referral_code FROM users WHERE login = '$user_email'";
$res = mysqli_query($db_connect, $sql);

if (!$res) {
    die("Error querying the database: " . mysqli_error($db_connect));
}

if (mysqli_num_rows($res) == 0) {
    die("User not found in the database.");
}

$user = mysqli_fetch_assoc($res);
$referral_code = $user['referral_code'];

// Store referral code in session variable
$_SESSION['referral_code'] = $referral_code;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/usercss.css">
    <link rel="shortcut icon" href="assets/img/logo2.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <div class="account">
        <title>User Dashboard</title>
    </div>
</head>
<body>
<div class="titles">
    <h1 class="intro">Welcome to the User Dashboard!</h1>
    <p class="log_greeting">You are now logged in. Here is some user-specific information:</p>
</div>
<ul>
    <li class="id">User ID: <?php echo $_SESSION['user_id'];?></li>
    <li class="email">Your referral code: <?php echo $_SESSION['referral_code'];?></li>
</ul>
<a href="logout.php" class="logout"><p class="logout_text">Logout</p></a>
</body>
</html>