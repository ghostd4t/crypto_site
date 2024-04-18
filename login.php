<?php
// Database connection
$db_connect = mysqli_connect("localhost", "root", "", "cryptousersdb") or die("Error connecting to the database");

// Check if the login form is submitted
if (isset($_POST['login'])) {
    // Sanitize input data
    $email = mysqli_real_escape_string($db_connect, $_POST['email']);
    $pass = mysqli_real_escape_string($db_connect, $_POST['pass']);

    // Validate input data
    if (empty($email)) {
        $err[] = 'Email field cannot be empty!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err[] = 'Invalid email format';
    }

    if (empty($pass)) {
        $err[] = 'Password field cannot be empty!';
    }

    // Check if user exists and password is correct
    $sql = "SELECT * FROM users WHERE login = '$email'";
    $res = mysqli_query($db_connect, $sql);

    if (!$res) {
        $err[] = 'Error querying the database: ' . mysqli_error($db_connect);
    } else {
        if (mysqli_num_rows($res) == 0) {
            $err[] = 'User does not exist!';
        } else {
            $user = mysqli_fetch_assoc($res);
            $salt = $user['salt'];
            $stored_pass = $user['pass'];
            $hashed_pass = md5(md5($pass) . $salt);

            if ($stored_pass != $hashed_pass) {
                $err[] = 'Invalid password!';
            }
        }
    }

    // If no errors, authenticate the user
    if (empty($err)) {
        // Store user information in a session or use token-based authentication
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['login'];

        // Redirect to a protected area or display a success message
        header('Location: ' . CRYPTO_HOST . 'protected_area.php');
        exit;
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
?>

<!-- Login form -->
<form method="post" action="">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="pass" placeholder="Password" required>
    <input type="submit" name="login" value="Login">
</form>
