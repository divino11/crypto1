<?php
require_once 'functions.php';
require_once 'logs.php';
session_start();
$val = $_SESSION['rez'];
$replace_val = str_replace("_", "/", $val);
echo "<link rel='stylesheet' href='style.css'>";
InsertLogs("[percent-mode]Отправил в обработку скрипт на операцию с процентами");
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
ignore_user_abort(1);  // Игнорировать обрыв связи с браузером
set_time_limit(0);       // Время работы скрипта неограниченно
for ($j = 0; $j < 1000; $j++) {
    $dec = api_query("GetMarketOrders", array('market' => $val, 100));
    $count = 0;
    foreach ($dec->Data->Buy as $item) {
        $count++;
        if ($count > 1) break;
        $price_max_buy = sprintf('%.8f', $item->Price);
    }

    $count = 0;
    echo "<br>";

    foreach ($dec->Data->Sell as $item) {
        $count++;
        if ($count > 1) break;
        $price_max_sell = sprintf('%.8f', $item->Price);
        echo "Sell: " . $price_max_sell;
    }
    if (!is_array($_SESSION['res_last'])) {
        $_SESSION['res_last'] = $price_max_buy;
        $_SESSION['res_last_sell'] = $price_max_sell;
    }

    $count_buy = $_GET['count_prices'];
    echo $k;
    $step = $_GET[step_orders];
    $count_coins = $_GET[count_coins];
    $price = $price_max_buy / $price_max_sell;
    $price_minus = $price - 1;
    $percent_price = ($price_minus * 100) * -1;
    $percents = $_GET['percents'];
InsertLogs("[percent-mode]Шаг выполнения: " . $step);
InsertLogs("[percent-mode]Переданное количество валюты: " . $count_coins);
InsertLogs("[percent-mode]Переданное значение процентов: " . $percents);
    if ($percents < $percent_price) {
        for ($i = 0; $i < $_GET['count_prices']; $i++) {
            $price_max_buy += $step;
            $arr = array();
            $ArrayObj1[0] = null;
            $ArrayObj1[0]->Market = $replace_val;
            $ArrayObj1[0]->Type = "Buy";
            $ArrayObj1[0]->Rate = $price_max_buy;
            $ArrayObj1[0]->Amount = $count_coins;
            $price_max_buy1 = sprintf('%.8f', $price_max_buy);
            InsertLogs("[percent-mode]Цена: " . $price_max_buy1);
            InsertLogs("[percent-mode]Кол-во валюты: " . $ArrayObj1[0]->Amount);
            $arr[0] = $ArrayObj1[0];
            api_query1("SubmitTrade", $arr);
            InsertLogs("[percent-mode]Валюта: " . $ArrayObj1[0]->Market . ". Тип операции: " . $ArrayObj1[0]->Type . " по " . $price_max_buy . " в количестве: " . $ArrayObj1[0]->Amount);
        }
        sleep(300);
    }
    sleep(1);
}
InsertLogs("[percent-mode]ОПЕРАЦИЯ ВЫПОЛНЕНА!");
?>