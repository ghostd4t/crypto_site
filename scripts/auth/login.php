<?php

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

//Если нажата кнопка то обрабатываем данные
if(isset($_POST['submit']))
{
    if(empty($_POST['email']))
        $err[] = 'Не введен Логин';

    if(empty($_POST['pass']))
        $err[] = 'Не введен Пароль';

    //Проверяем наличие ошибок и выводим пользователю
    if ((bool)$err === true)
        echo showErrorMessage($err);
    else
    {
        /*Создаем запрос на выборку из базы
        данных для проверки подлиности пользователя*/
        $sql = 'SELECT *
                    FROM `users`
                    WHERE `login` = "'. escape_str($_POST['email']) .'"
                    AND `status` = 1';
        $res = mysqlQuery($sql);

        //Если логин совподает, проверяем пароль
        if(mysqli_num_rows($res) > 0)
        {
            //Получаем данные из таблицы
            $row = mysqli_fetch_assoc($res);

            if(md5(md5($_POST['pass']).$row['salt']) == $row['pass'])
            {
                $_SESSION['user'] = true;

                //Сбрасываем параметры
                header('Location:'. BEZ_HOST .'less/reg/?mode=auth');
                exit;
            }
            else
                echo showErrorMessage('Неверный пароль!');
        }
        else
            echo showErrorMessage('Логин <b>'. $_POST['email'] .'</b> не найден!');
    }

}
