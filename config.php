<?php
//Адрес базы данных
define('CRYPTO_DBSERVER','localhost');

//Логин БД
define('CRYPTO_DBUSER','root');

//Пароль БД
define('CRYPTO_DBPASSWORD','');

//БД
define('CRYPTO_DATABASE','cryptousersdb');

//Префикс БД
define('CRYPTO_DBPREFIX','users');

//Errors
define('CRYPTO_ERROR_CONNECT','Немогу соеденится с БД');

//Errors
define('CRYPTO_NO_DB_SELECT','Данная БД отсутствует на сервере');

//Адрес хоста сайта
define('CRYPTO_HOST','http://'. $_SERVER['HTTP_HOST'] .'/');

//Адрес почты от кого отправляем
define('CRYPTO_MAIL_AUTOR','Регистрация на crypto <galunenko.vlad@yandex.ru>');