<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'functions.php';
require_once 'classCrypto.php';
require_once 'logs.php';
echo "<link rel='stylesheet' href='style.css'>";
echo "<script async src=\'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js\'></script>";
$time_value = date("Y-m-d H:i:s");
ignore_user_abort(1);  // Игнорировать обрыв связи с браузером
set_time_limit(0);       // Время работы скрипта неограниченно
session_start();
$val = $_SESSION['rez'];
$replace_val = str_replace("_", "/", $val);
InsertLogs("[random-mode]Отправил на обработку скрипт покупки или продажи меньше имеющейся цены");
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
$dec = api_query("GetMarketOrders", array('market' => $val, 100));
$amount_coin = $_GET['count_order_rand'];
if ($_GET['buy_sel_rand'] == 'buy_rand') {
    $count = 0;
    //цена покупки
    foreach ($dec->Data->Sell as $item) {
        $count++;
        if ($count > 1) break;
        $price_max_sell = sprintf('%.8f', $item->Price);
    }
    InsertLogs("[random-mode]Цена покупки: " . $price_max_sell);
    InsertLogs("[random-mode]Кол-во валюты: " . $amount_coin);
    api_query("SubmitTrade", array('Market' => $val, 'Type' => "Buy", 'Rate' => $price_max_sell, 'Amount' => $amount_coin));
    InsertLogs("[random-mode]Валюта: " . $val . ", была КУПЛЕНА по " . $price_max_sell . ", в количестве: " . $amount_coin);
} else {
    $count = 0;
    //цена продажи
    foreach ($dec->Data->Buy as $item) {
        $count++;
        if ($count > 1) break;
        $price_max_buy = sprintf('%.8f', $item->Price);
    }
    InsertLogs("[random-mode]Цена продажи: " . $price_max_buy);
    InsertLogs("[random-mode]Кол-во валюты: " . $amount_coin);
    api_query("SubmitTrade", array('Market' => $val, 'Type' => "Sell", 'Rate' => $price_max_buy, 'Amount' => $amount_coin));
    InsertLogs("[random-mode]Валюта: " . $val . ", была ПРОДАНА по " . $price_max_buy . ", в количестве: " . $amount_coin);
}



?>