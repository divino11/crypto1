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
$replace_val = str_replace("_", "/", $val);
InsertLogs("[eat-order]Отправил в обработку скрипт на сьедание ордера");
function api_query($method, array $req = array())
{
    echo var_dump($req);
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
    $amount = 0;
    $objectParams=null;
    //$_SESSION['flag'] = $_GET['hide_false'];
    if ($_SESSION['flag'] == null) {
        if ($_GET['buy_sell'] == 'Buy') {
            foreach ($dec->Data->Buy as $item) {
                $price_buy1 = $item->Price;
                $price_buy = sprintf('%.8f', $price_buy1);
                if ($price_buy == $price_fix) {
                    $amount += $item->Volume;
                    break;
                } else {
                    $amount += $item->Volume;
                }
            }
        } else {
            foreach ($dec->Data->Sell as $item) {
                $price_sell1 = $item->Price;
                $price_sell = sprintf('%.8f', $price_sell1);
                if ($price_sell == $price_fix) {
                    $amount += $item->Volume;
                    break;
                }
                else {
                    $amount += $item->Volume;
                }
            }
        }
        $objectParams->amount = $amount / $_GET['count_quer'];
        $objectParams->countQuery = $_GET['count_quer'];
        echo $_GET['count_quer'];
        $objectParams->tick = $_GET['interval_time'];
        $objectParams->fixprice = $_GET['fixed_price'];
        $objectParams->typeOperation = $_GET['buy_sell'];
        $_SESSION['Eat'] = $objectParams;
        $_SESSION['flag'] = "1";
        InsertLogs("[eat-order]Кол-во валюты: " . $objectParams->amount . " " . $val);
        InsertLogs("[eat-order]Кол-во запросов: " . $objectParams->countQuery);
        InsertLogs("[eat-order]Интервал времени: " . $objectParams->tick);
        InsertLogs("[eat-order]Цена за валюту: " . $objectParams->fixprice);
    }
    else
    {
       $objectParams = $_SESSION['Eat'];
    }
    echo var_dump($objectParams);

    sleep($objectParams->tick);
api_query("SubmitTrade", array('Market' => $val, 'Type' => $objectParams->typeOperation, 'Rate' => $objectParams->fixprice, 'Amount' => $objectParams->amount));
InsertLogs("[eat-order]Операция выполнена!");
InsertLogs("[eat-order]Тип операции " .$objectParams->typeOperation . " в количестве: "  . $objectParams->amount . " валюты, за " . $objectParams->fixprice . ".");
    $objectParams->countQuery--;
    $_SESSION['Eat'] = $objectParams;
?>