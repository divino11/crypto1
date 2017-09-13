<?php
require_once 'functions.php';
require_once 'logs.php';
echo "<link rel='stylesheet' href='style.css'>";
echo "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css\" integrity=\"sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M\" crossorigin=\"anonymous\">";
session_start();
InsertLogs("[wall-mode]Отправил в обработку скрипт на оформление стенки заказов");
$val = $_SESSION['rez'];
$min_order = $_GET['min_order'];
$max_order = $_GET['max_order'];
$count_ord = $_GET['count_order'];
$count_query1 = $_GET['count_query'];
$replace_val = str_replace("_", "/", $val);

if ($_GET['buy_sell'] == 'buy') {
    SetWall('Buy', $count_query1, $count_ord, $min_order, $max_order);
} else {
    SetWall('Sell', $count_query1, $count_ord, $min_order, $max_order);
}

function SetWall($typeOperation, $count_query1, $count_ord, $min_order, $max_order)
{
    $val = $_SESSION['rez'];
    $replace_val = str_replace("_", "/", $val);
    $arr = array();
    for ($i = 0; $i < $count_query1; $i++)
    {
        $rand_val1 =  $min_order + mt_rand() / mt_getrandmax() * ($max_order - $min_order);
        $rand_val = sprintf('%.8f', $rand_val1);
        $ArrayObj1[$i]= null;
        $ArrayObj1[$i]->Market = $replace_val;
        $ArrayObj1[$i]->Type = $typeOperation;
        $ArrayObj1[$i]->Rate = $rand_val;
        $ArrayObj1[$i]->Amount = $count_ord;
        $arr[$i] = $ArrayObj1[$i];
    }
    api_query1("SubmitTrade",  $arr);
    InsertLogs("[wall-mode]Тип операции: " . $typeOperation . ", валюта: " . $val . ", количество: " . $count_ord . ", по цене: " . $rand_val . " [" . $i . "] раз(а).");
}
?>