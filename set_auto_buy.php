<?php
require_once 'functions.php';
require_once 'classCrypto.php';
require_once 'logs.php';
echo "<link rel='stylesheet' href='style.css'>";
echo "<script async src=\'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js\'></script>";
ignore_user_abort(1);  // Игнорировать обрыв связи с браузером
set_time_limit(0);       // Время работы скрипта неограниченно
session_start();
$val = $_SESSION['rez'];
echo "<br>Coins " . $val;
$replace_val = str_replace("_", "/", $val);
InsertLogs("[auto-mode]Отправил в обработку скрипт автоматического режима");
function api_query($method, array $req = array())
{
    $API_KEY = '307fa1b1f2e34b139d2a6d60478bc071';
    $API_SECRET = 'l8rTx0IGcCha9J7SxQHLwps8aFMJYVD5EmhEqdUD7Qw';
    $public_set = array("GetCurrencies", "GetTradePairs", "GetMarkets", "GetMarket", "GetMarketHistory", "GetMarketOrders");
    $private_set = array("GetBalance", "GetDepositAddress", "GetOpenOrders", "GetTradeHistory", "GetTransactions", "SubmitTrade", "CancelTrade", "SubmitTip");
    static $ch = null;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Cryptopia.co.nz API PHP client; ' . php_uname('s') . '; PHP/' . phpversion() . ')');
    if (in_array($method, $public_set)) {
        $url = "https://www.cryptopia.co.nz/api/" . $method;
        if ($req) {
            foreach ($req as $r) {
                $url = $url . '/' . $r;
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
    } elseif (in_array($method, $private_set)) {
        $url = "https://www.cryptopia.co.nz/Api/" . $method;
        $nonce = explode(' ', microtime())[1];
        $post_data = json_encode($req);
        $m = md5($post_data, true);
        $requestContentBase64String = base64_encode($m);
        $signature = $API_KEY . "POST" . strtolower(urlencode($url)) . $nonce . $requestContentBase64String;
        $hmacsignature = base64_encode(hash_hmac("sha256", $signature, base64_decode($API_SECRET), true));
        $header_value = "amx " . $API_KEY . ":" . $hmacsignature . ":" . $nonce;
        $headers = array("Content-Type: application/json; charset=utf-8", "Authorization: $header_value");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req));
    }
    // run the query
    $res = curl_exec($ch);
    $dec = json_decode($res);
    return $dec;
}
$volume = null;
$_SESSION['flag'] = $_GET['hide_false'];
if ($_SESSION['flag'] == 0)
    {
        $volume = new PropertyVolume();
        $volume->count_currency = $_GET['set_count'];
        $volume->time_start = $_GET['interval_time_start'];
        $volume->time_end = $_GET['interval_time_end'];
        InsertLogs("[auto-mode]Переданное кол-во валюты: " . $volume->count_currency);
        InsertLogs("[auto-mode]Время начала: " . $volume->time_start . ":00");
        InsertLogs("[auto-mode]Конец: " . $volume->time_end . ":00");
        $volume->select_time = abs(($volume->time_end - $volume->time_start) * 60);
        InsertLogs("[auto-mode]Общее время выполнения скрипта в минутах: " . $volume->select_time);
        $_SESSION['flag'] = "1";
    } else {
        $volume = $_SESSION['volume'];
    }
for (;;) {
    $rand_time = rand(1, 2);
echo "<br> random time " . $rand_time;
    $dec = api_query("GetMarketOrders", array('market' => $val, 100));
    $count = 0;
    foreach ($dec->Data->Sell as $item) {
        $count++;
        if ($count > 1) break;
        $price_max_sell = sprintf('%.8f', $item->Price);
        $price_change = $price_max_sell - 0.00000005;
        $price_max_sell_change = sprintf('%.8f', $price_change);
    }
    InsertLogs("[auto-mode]Последняя цена продажи на рынке: " . $price_max_sell_change);
//всего валюты
    $count_sell_currency = $volume->count_currency / $price_max_sell_change;
    InsertLogs("[auto-mode]Общее количество валюты в операции: " . $count_sell_currency);
    echo "<br> всего валюты будет торговаться " . $count_sell_currency;
    //кол-во в минуту
    $count_in_min = $count_sell_currency / $volume->select_time;
    InsertLogs("[auto-mode]Количество валюты в минуту: " . $count_in_min);
    echo "<br> кол-во в минуту " . $count_in_min;
//сколько продавать за рандомное время
    $select_count_currency1 = $rand_time * $count_in_min;
    $select_count_currency = round($select_count_currency1, 7);
    InsertLogs("[auto-mode]Количество продаваемой валюты за указанное время в минутах: " . $select_count_currency);
echo "<br> сколько продавать за рандомное время" . $select_count_currency;
//продажа валюты
        api_query("SubmitTrade", array('Market' => $val, 'Type' => "Sell", 'Rate' => $price_max_sell_change, 'Amount' => $select_count_currency));
        InsertLogs("[auto-mode]Ордер был выставлен на ПРОДАЖУ. Количество: " . $select_count_currency . ". По цене: " . $price_max_sell_change);
        sleep($rand_time);
echo "<br>Первая цена продажи" . $price_max_sell_change;

    //Сколько времени осталось
    $volume->select_time -= $rand_time;
    InsertLogs("[auto-mode]Сколько времени выполнения скрипта осталось: " . $volume->select_time);
    echo "<br>Сколько времени осталось" . $volume->select_time;
    //выбираем открытые ордера
    $dec1 = api_query("GetMarketOrders", array('market' => $val, 100));
    $count = 0;
    foreach ($dec1->Data->Sell as $item3) {
        $count++;
        if ($count > 1) break;
        $price_max_sell1 = sprintf('%.8f', $item3->Price);
        $price_change1 = $price_max_sell1 - 0.00000005;
        $price_max_sell_change1 = sprintf('%.8f', $price_change1);
/*echo "<br> Последняя цена продажи " . $price_max_sell_change1;
InsertLogs("|||||[auto-mode]price_max_sell_change: " . $price_max_sell_change);
InsertLogs("|||||[auto-mode]price_max_sell_change1: " . $price_max_sell_change1);
InsertLogs("|||||[auto-mode]price_change1: " . $price_change1);
InsertLogs("|||||[auto-mode]price_max_sell1: " . $price_max_sell1);*/
        $count_sell_orders = $item3->Volume;
        InsertLogs("[auto-mode]Количество продаваемой валюты: " . $count_sell_orders);
        echo "<br> Первая цена " . $price_max_sell_change;
        echo "<br> Последняя цена " . $price_max_sell_change1;
        echo "<br> сколько продавать за рандомное время " . $select_count_currency;
        echo "<br> Последнее кол-во на рынке " . $count_sell_orders;
        if ($price_max_sell_change == $price_max_sell1 && $select_count_currency >= $count_sell_orders) {
            /*InsertLogs("зашли в первый if");
            InsertLogs("price_max_sell1: " . $price_max_sell1);
            InsertLogs("price_max_sell_change: " . $price_max_sell_change);
            InsertLogs("price_max_sell_change1: " . $price_max_sell_change1);*/
            api_query("SubmitTrade", array('Market' => $val, 'Type' => "Buy", 'Rate' => $price_max_sell_change, 'Amount' => $select_count_currency));
            InsertLogs("[auto-mode]Ордер был КУПЛЕН за " . $price_max_sell_change . ", в количестве: " . $select_count_currency);
        }
        else
        {
            $dec2 = api_query("GetOpenOrders", array('Market' => $val));
            foreach ($dec2->Data as $item1) {
                $count_opens = $item1->Amount;
                $buy_or_sell = $item1->Type;
                $price = sprintf('%.8f', $item1->Rate);
                $order_id = $item1->OrderId;
                InsertLogs("[auto-mode]Проверяем наши открытые ордера!");
                InsertLogs("[auto-mode]Количество валюты выставленых на рынок: " . $count_opens);
                InsertLogs("[auto-mode]Цена на наши ордера: " . $price);
                InsertLogs("[auto-mode]ID ордера: " . $order_id);
                if ($price == $price_max_sell_change && $count_opens <= $select_count_currency && $buy_or_sell == "Sell") {
                   /* InsertLogs("Зашли в if в открытых ордерах");
                    InsertLogs("Market " . $val);
                    InsertLogs("Rate " . $price_max_sell_change);
                    InsertLogs("Price " . $price);
                    InsertLogs("Amount " . $select_count_currency);
                    InsertLogs("count_opens " . $count_opens);*/
                    api_query("SubmitTrade", array('Market' => $val, 'Type' => "Buy", 'Rate' => $price, 'Amount' => $count_opens));
                    InsertLogs("[auto-mode]Ордер был КУПЛЕН в количестве: " . $select_count_currency . ", за " . $price_max_sell_change);
                } else if ($buy_or_sell == "Sell" || $buy_or_sell == "Buy") {
                    api_query("CancelTrade", array('Type' => "All"));
                    InsertLogs("[auto-mode]Ордер был отменен!");
                }
            }
        }
    }
        $rand_time = rand(1, 2);
        sleep($rand_time * 10);
        $volume->select_time -= $rand_time;
        InsertLogs("[auto-mode]Осталось времени на выполнение: " . $volume->select_time);
        echo "<br>Сколько времени всего осталось " . $volume->select_time;
}
?>