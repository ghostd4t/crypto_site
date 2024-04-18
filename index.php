<?php


session_start();

header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);

ob_start();

$mode = isset($_GET['mode'])  ? $_GET['mode'] : false;
$user = isset($_SESSION['user']) ? $_SESSION['user'] : false;
$err = array();
define('CRYPTO_KEY', true);
include './config.php';
include './func/funct.php';
include './bd/bd.php';

switch($mode)
{
    case 'reg':
        include './scripts/reg/register.php';
        include './scripts/reg/register.html';
        break;

    case 'auth':
        include './scripts/auth/login.php';
        include './scripts/auth/login.html';
        include './scripts/auth/show.php';
        break;

}

$content = ob_get_contents();
ob_end_clean();

include './index.html';