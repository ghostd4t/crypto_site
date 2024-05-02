<?php
$db_connect = mysqli_connect("localhost", "root", "", "cryptousersdb") or die("Error connecting to the database");

// Get the confirmation token from the URL parameter
$confirm_token = isset($_GET['token']) ? $_GET['token'] : '';

// Query the database to find the user with the matching token
$sql = "SELECT * FROM users WHERE confirm_token = '$confirm_token' AND confirm_expiry > '" . time() . "'";
$res = mysqli_query($db_connect, $sql);

if (!$res) {
    // Query failed, display the error message
    echo 'Error: ' . mysqli_error($db_connect);
} else {
    if (mysqli_num_rows($res) > 0) {
        // Update the user's status in the database
        $sql = "UPDATE users SET status = 1 WHERE confirm_token = '$confirm_token'";
        $res = mysqli_query($db_connect, $sql);

        if ($res) {
            // Redirect to the success page
            header('Location: https://cryptoscammers/success.php'); // Replace with your website's URL
            exit;
        } else {
            // Update query failed, display the error message
            echo 'Error: ' . mysqli_error($db_connect);
        }
    } else {
        // Token is invalid or expired
        echo 'The confirmation token is invalid or has expired.';
    }
}

// Close the database connection
mysqli_close($db_connect);
?>