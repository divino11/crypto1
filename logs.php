<?php
header('Content-Type: text/html; charset=utf-8');
echo "<link rel='stylesheet' href='style.css'>";
echo "<script async src=\'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js\'></script>";
ignore_user_abort(1);  // Игнорировать обрыв связи с браузером
set_time_limit(0);       // Время работы скрипта неограниченно
session_start();

function InsertLogs ($value) {
    $host = 'xnode.mysql.ukraine.com.ua'; // адрес сервера
    $database = 'xnode_andr'; // имя базы данных
    $user = 'xnode_andr'; // имя пользователя
    $password = '2qn3q6zg'; // пароль
    // подключаемся к серверу
    $link = mysqli_connect($host, $user, $password, $database)
    or die("Ошибка " . mysqli_error($link));
    mysqli_set_charset($link, 'utf8');
$time = "[" . date("Y-m-d H:i:s") . "]" . $value;
    $sql = mysqli_query($link, "INSERT INTO `log_crypto` (`value`) 
                        VALUES ('$time')");
}

?>