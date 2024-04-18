<?php

//Соединение с БД MySQL

$db_connect = mysqli_connect( "localhost", "root", "") or die(CRYPTO_ERROR_CONNECT);


mysqli_select_db($db_connect, "cryptousersdb" )or die(CRYPTO_NO_DB_SELECT);

mysqli_query("SET NAMES utf8");
mysqli_query("set character_set_client='utf8'");
mysqli_query("set character_set_results='utf8'");
mysqli_query("set collation_connection='utf8_general_ci'");
?>