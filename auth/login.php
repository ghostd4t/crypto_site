<?php
// Database connection
$db_connect = mysqli_connect("localhost", "root", "", "cryptousersdb") or die("Error connecting to the database");

// Check if the login form is submitted
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Sanitize input data
    $username = mysqli_real_escape_string($db_connect, $_POST['username']);
    $pass = mysqli_real_escape_string($db_connect, $_POST['password']);

    // Validate input data
    if (empty($username)) {
        $err[] = 'Username field cannot be empty!';
    }

    if (empty($pass)) {
        $err[] = 'Password field cannot be empty!';
    }

    // Check if user exists and password is correct
    $sql = "SELECT * FROM users WHERE login = '$username'";
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
            } else {
                // Store user information in a session or use token-based authentication
                session_start();
                $_SESSION['user_id'] = $user['id']; // Add this line
                $_SESSION['user_email'] = $user['login']; // Add this line

                // Redirect to a protected area or display a success message
                $user_id = $_SESSION['user_id'];
                $user_email = $_SESSION['user_email'];
                header('Location:..\..\user.php?user_id=' . $user_id . '&user_email=' . urlencode($user_email));
                exit;
            }
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
?>