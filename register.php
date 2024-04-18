<?php
// Database connection
$db_connect = mysqli_connect("localhost", "root", "", "cryptousersdb") or die("Error connecting to the database");

// Check if the registration form is submitted
if (isset($_POST['submit'])) {
    // Sanitize input data
    $email = mysqli_real_escape_string($db_connect, $_POST['email']);
    $pass = mysqli_real_escape_string($db_connect, $_POST['pass']);
    $pass2 = mysqli_real_escape_string($db_connect, $_POST['pass2']);

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
    if ($pass != $pass2) {
        $err[] = 'Passwords do not match!';
    }

    // Check if email already exists in the database
    $sql = "SELECT * FROM users WHERE login = '$email'";
    $res = mysqli_query($db_connect, $sql);

    if (!$res) {
        $err[] = 'Error querying the database: ' . mysqli_error($db_connect);
    } else {
        if (mysqli_num_rows($res) > 0) {
            $err[] = 'Email already exists!';
        }
    }

    // Generate salt and hash password
    $salt = substr(md5(uniqid()), -8);
    $pass = md5(md5($pass) . $salt);

    // Insert user data into the database
    if (empty($err)) {
        $sql = "INSERT INTO users (login, pass, salt, active_hex, status) VALUES ('$email', '$pass', '$salt', '', 0)";
        $res = mysqli_query($db_connect, $sql);

        if (!$res) {
            $err[] = 'Error inserting data into the database: ' . mysqli_error($db_connect);
        } else {
            // Generate activation key and send activation email
            $activation_key = md5($salt);
            $url = CRYPTO_HOST . 'less/reg/?mode=reg&key=' . $activation_key;
            $title = 'Registration on crypto';
            $message = 'To activate your account, please click on the following link: <a href="' . $url . '">' . $url . '</a>';

            sendMessageMail($email, CRYPTO_MAIL_AUTOR, $title, $message);

            // Redirect to success page
            header('Location:' . CRYPTO_HOST . 'less/reg/?mode=reg&status=ok');
            exit;
        }
    }

    // Display error messages
    if (!empty($err)) {
        echo '<ul>';
        foreach ($err as $error) {
            echo '<li style="color:red;">' . $error . '</li>';
        }
        echo '</ul>';
    }
}
// Function to send activation email
function sendMessageMail($to, $from, $title, $message) {
    // Formatting email headers
    $subject = $title;
    $subject = '=?utf-8?b?' . base64_encode($subject) . '?=';
    $headers = "Content-type: text/html; charset=\"utf-8\"\r\n";
    $headers .= "From: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Date: " . date('D, d M Y h:i:s O') . "\r\n";

    // Sending email
    if (!mail($to, $subject, $message, $headers)) {
        return 'Error sending email!';
    }
}

$activation_key = md5($salt);
$url = CRYPTO_HOST . 'less/reg/?mode=reg&key=' . $activation_key;
$title = 'Registration on crypto';
$message = 'To activate your account, please click on the following link: <a href="' . $url . '">' . $url . '</a>';

sendMessageMail($email, CRYPTO_MAIL_AUTOR, $title, $message);