<?php

//Выводим сообщение об удачной регистрации
if(isset($_GET['status']) and $_GET['status'] == 'ok')
    echo '<b>Вы успешно зарегистрировались! Пожалуйста активируйте свой аккаунт!</b>';

//Выводим сообщение об удачной регистрации
if(isset($_GET['active']) and $_GET['active'] == 'ok')
    echo '<b>Ваш аккаунт на crypto успешно активирован!</b>';

//Производим активацию аккаунта
if(isset($_GET['key']))
{
    //Проверяем ключ
    $sql = 'SELECT * FROM `'. CRYPTO_DBPREFIX .'reg` WHERE `active_hex` = "'. escape_str($_GET['key']) .'"';
    $res = mysqlQuery($sql);

    if(mysqli_num_rows($res) == 0)
        $err[] = 'Ключ активации не верен!';

    //Проверяем наличие ошибок и выводим пользователю
    if(count($err) > 0)
        echo showErrorMessage($err);
    else
    {
        //Получаем адрес пользователя
        $row = mysqli_fetch_assoc($res);
        $email = $row['login'];

        //Активируем аккаунт пользователя
        $sql = 'UPDATE `'. CRYPTO_DBPREFIX .'reg`
                    SET `status` = 1
                    WHERE `login` = "'. $email .'"';
        $res = mysqlQuery($sql);

        //Отправляем письмо для активации
        $title = 'Ваш аккаунт на crypto успешно активирован';
        $message = 'Поздравляю Вас, Ваш аккаунт на crypto успешно активирован';

        sendMessageMail($email, CRYPTO_MAIL_AUTOR, $title, $message);

        /*Перенаправляем пользователя на
        нужную нам страницу*/
        header('Location:'. CRYPTO_HOST .'less/reg/?mode=reg&active=ok');
        exit;
    }
}

function mysqlQuery($sql)
{
    $db_connect = mysqli_connect( "localhost", "root", "") or die(CRYPTO_ERROR_CONNECT);
    mysqli_select_db($db_connect, "cryptousersdb")or die(CRYPTO_NO_DB_SELECT);
    $res = mysqli_query($db_connect, $sql);
    /* Проверяем результат
    Это показывает реальный запрос, посланный к MySQL, а также ошибку. Удобно при отладке.*/
    if(!$res)
    {
        $message  = 'Неверный запрос: ' . mysqli_error($db_connect) . "\n";
        $message .= 'Запрос целиком: ' . $sql;
        die($message);
    }

    return $res;
}
function escape_str($data)
{
    $db_connect = mysqli_connect( "localhost", "root", "", 'cryptousersdb') or die(CRYPTO_ERROR_CONNECT);
    if(is_array($data))
    {
        if(get_magic_quotes_gpc())
            $strip_data = array_map("stripslashes", $data);
        $result = array_map("mysql_real_escape_string", $strip_data);
        return  $result;
    }
    else
    {
        if(get_magic_quotes_gpc())
            $data = stripslashes($data);
        $result = mysqli_real_escape_string($db_connect, $data);
        return $result;
    }
}

function salt()
{
    $salt = substr(md5(uniqid()), -8);
    return $salt;
}

function showErrorMessage($data)
{
    $err = '<ul>'."\n";

    if(is_array($data))
    {
        foreach($data as $val)
            $err .= '<li style="color:red;">'. $val .'</li>'."\n";
    }
    else
        $err .= '<li style="color:red;">'. $data .'</li>'."\n";

    $err .= '</ul>'."\n";

    return $err;
}

function sendMessageMail($to, $from, $title, $message)
{
    //Адресат с отправителем
    //$to = $to;
    //$from = $from;

    //Формируем заголовок письма
    $subject = $title;
    $subject = '=?utf-8?b?'. base64_encode($subject) .'?=';

    //Формируем заголовки для почтового сервера
    $headers = "Content-type: text/html; charset=\"utf-8\"\r\n";
    $headers .= "From: ". $from ."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n";

    //Отправляем данные на ящик админа сайта
    if(!mail($to, $subject, $message, $headers))
        return 'Ошибка отправки письма!';
    else
        return true;
}

/*Если нажата кнопка на регистрацию,
начинаем проверку*/
if(isset($_POST['submit']))
{
    //Утюжим пришедшие данные
    if(empty($_POST['email']))
        $err[] = 'Поле Email не может быть пустым!';
    else
    {
        if(!preg_match("/^[a-z0-9_.-]+@([a-z0-9]+\.)+[a-z]{2,6}$/i", $_POST['email']))
            $err[] = 'Не правильно введен E-mail'."\n";
    }

    if(empty($_POST['pass']))
        $err[] = 'Поле Пароль не может быть пустым';

    if(empty($_POST['pass2']))
        $err[] = 'Поле Подтверждения пароля не может быть пустым';

    //Проверяем наличие ошибок и выводим пользователю
    if ((bool)$err === true)
        echo showErrorMessage($err);
    else
    {
        /*Продолжаем проверять введеные данные
        Проверяем на совподение пароли*/
        if($_POST['pass'] != $_POST['pass2'])
            $err[] = 'Пароли не совпадают';

        //Проверяем наличие ошибок и выводим пользователю
        if ((bool)$err === true)
            echo showErrorMessage($err);
        else
        {
            /*Проверяем существует ли у нас
            такой пользователь в БД*/
            $sql = 'SELECT `login`
                        FROM `users`
                        WHERE `login` = "'. escape_str($_POST['email']) .'"';
            $res = mysqlQuery($sql);






            if(mysqli_num_rows($res) > 0)
                $err[] = 'К сожалению Логин: <b>'. $_POST['email'] .'</b> занят!';

            //Проверяем наличие ошибок и выводим пользователю
            if ((bool)$err === true)
                echo showErrorMessage($err);
            else
            {
                //Получаем ХЕШ соли
                $salt = salt();

                //Солим пароль
                $pass = md5(md5($_POST['pass']).$salt);

                /*Если все хорошо, пишем данные в базу*/
                $sql = 'INSERT INTO `users`
                            VALUES(
                                    , 
                                    "'. escape_str($_POST['email']) .'",
                                    "'. $pass .'",
                                    "'. $salt .'",
                                    "'. md5($salt) .'",
                                    0
                                    )';
                $res = mysqlQuery($sql);

                //Отправляем письмо для активации
                $url = CRYPTO_HOST .'less/reg/?mode=reg&key='. md5($salt);
                $title = 'Регистрация на crypto';
                $message = 'Для активации Вашего акаунта пройдите по ссылке
                    <a href="'. $url .'">'. $url .'</a>';

                sendMessageMail($_POST['email'], CRYPTO_MAIL_AUTOR, $title, $message);

                //Сбрасываем параметры
                header('Location:'. CRYPTO_HOST .'less/reg/?mode=reg&status=ok');
                exit;
            }
        }
    }
}