<?php
echo "<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js\"></script>";
echo "<link rel='stylesheet' href='log.css'>";
?>
<?php
$host = 'xnode.mysql.ukraine.com.ua'; // адрес сервера
$database = 'xnode_andr'; // имя базы данных
$user = 'xnode_andr'; // имя пользователя
$password = '2qn3q6zg'; // пароль
// подключаемся к серверу
$link = mysqli_connect($host, $user, $password, $database)
or die("Ошибка " . mysqli_error($link));
mysqli_set_charset($link, 'utf8');
$sql = "SELECT * FROM `log_crypto` ORDER BY `id` DESC";
$result = mysqli_query($link, $sql);

echo "<div class='color_text' id='show_log'>";
    while ($row = mysqli_fetch_assoc($result)) {

            echo "<br>" . "{$row['value']}";

    }
echo "</div>";

?>