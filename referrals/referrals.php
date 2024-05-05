<?php
session_start();

// Database connection
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'cryptousersdb';

$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die ("Could not connect to mysql because ".mysqli_error());
mysqli_select_db($con, $dbname) or die ("Could not select to mysql because ".mysqli_error());

if (isset($_COOKIE['referral'])) {
    $referral_code = $_COOKIE['referral'];
    $sql = "UPDATE referrals SET clicks = clicks + 1 WHERE referral_code = '$referral_code'";
    mysqli_query($con, $sql);
}
//cookie for visitor
setcookie("referral", $referral_code, time() + (86400 * 7), "/");