<?php
require 'functions.php';
require_once 'logs.php';
InsertLogs("[random-mode]Отправил в обработку скрипт на единичную покупку или продажу валюты");
session_start();
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
$val = $_SESSION['rez']; 
$replace_val = str_replace("_", "/", $val);
$rand_input1 =  $_GET['min_order_rand'] + mt_rand() / mt_getrandmax() * ($_GET['max_order_rand'] - $_GET['min_order_rand']);
$rand_input = sprintf('%.8f', $rand_input1);
$arr = array();
$ArrayObj1[0];
$ArrayObj1[0]->Market = $replace_val;
$ArrayObj1[0]->Type = "Buy";
$ArrayObj1[0]->Rate = $rand_input;
$ArrayObj1[0]->Amount = $_GET['count_order_rand'];
$arr[0] = $ArrayObj1[0]; 
 if ($_GET['buy_sel_rand'] == 'buy_rand')
  {
      InsertLogs("[random-mode]Цена покупки: " . $rand_input);
      InsertLogs("[random-mode]Кол-во валюты: " . $_GET['count_order_rand']);
	  api_query1 ("SubmitTrade", $arr);
      InsertLogs("[random-mode]Валюта: " . $val . ", было КУПЛЕНО " . $_GET['count_order_rand'] . " валюты, за " . $rand_input);
  } else
  {
      InsertLogs("[random-mode]Цена продажи: " . $rand_input);
      InsertLogs("[random-mode]replace_val: " . $replace_val);
      InsertLogs("[random-mode]Кол-во валюты: " . $_GET['count_order_rand']);
      api_query("SubmitTrade", array ( 'Market' => $replace_val, 'Type' => "Sell", 'Rate' => $rand_input, 'Amount' => $_GET['count_order_rand'] ));
      InsertLogs("[random-mode]Валюта: " . $val . ", было ПРОДАНО " . $_GET['count_order_rand'] . " валюты, за " . $rand_input);
  }
  InsertLogs("[random-mode]ОПЕРАЦИЯ ВЫПОЛНЕНА!");
?>