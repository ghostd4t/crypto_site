<?php
// Database connection
$db_connect = mysqli_connect("localhost", "root", "", "cryptousersdb") or die("Error connecting to the database");

// Check if the registration form is submitted
if (isset($_POST['submit'])) {
    // Sanitize input data
    $email = mysqli_real_escape_string($db_connect, $_POST['email']);
    $pass = mysqli_real_escape_string($db_connect, $_POST['pass']);
    $pass2 = mysqli_real_escape_string($db_connect, $_POST['pass2']);
    $referral_code = mysqli_real_escape_string($db_connect, $_POST['referral_code']);

    // Validate input data
    if (empty($email)) {
        $err[] = 'Email field cannot be empty!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err[] = 'Invalid email format';
    }

    if (empty($pass)) {
        $err[] = 'Password field cannot be empty!';
    }

    if (empty($pass2)) {
        $err[] = 'Confirm password field cannot be empty!';
    }

    // Check if passwords match
    if ($pass!= $pass2) {
        $err[] = 'Passwords do not match!';
    }

    // Check if email already exists in the database
    $sql = "SELECT * FROM users WHERE login = '$email'";
    $res = mysqli_query($db_connect, $sql);

    if (!$res) {
        $err[] = 'Error querying the database: '. mysqli_error($db_connect);
    } else {
        if (mysqli_num_rows($res) > 0) {
            $err[] = 'Email already exists!';
        }
    }

    // Check if referral code is valid
    $sql = "SELECT id FROM users WHERE referral_code = '$referral_code'";
    $res = mysqli_query($db_connect, $sql);

    if (mysqli_num_rows($res) == 0) {
        $err[] = 'Invalid referral code!';
    }

    // Generate salt and hash password
    $salt = substr(md5(uniqid()), -8);
    $pass = md5(md5($pass). $salt);
    $referral = substr(md5('$hot'. uniqid()), 0, 12);
    $new_referral_code = '$hot'.$referral;

    $activation_key = md5($salt);
    $activation_expiry = time() + 60 * 60; // expiry time is 1 hour from now

    // Insert user data into the database
    if (empty($err)) {
        $sql = "INSERT INTO users (login, pass, salt, active_hex, confirm_token, confirm_expiry, status, referral_code) VALUES ('$email', '$pass', '$salt', '', '$activation_key', '$activation_expiry', 0, '$new_referral_code')";
        $res = mysqli_query($db_connect, $sql);

        if (!$res) {
            $err[] = 'Error inserting data into the database: '. mysqli_error($db_connect);
        } else {
            // Update the referrer's points
            $sql = "UPDATE users SET points = points + 1 WHERE referral_code = '$referral_code'";
            mysqli_query($db_connect, $sql);

            // Redirect to success page
            header('Location:..\..\success.html');
            exit;
        }
    }

    // Display error messages
    if (!empty($err)) {
        echo '<ul>';
        foreach ($err as $error) {
            echo '<li style="color:red;">'. $error. '</li>';
        }
        echo '</ul>';
    }
}
?>