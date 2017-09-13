<?php
require_once 'functions.php';
require_once 'logs.php';
echo "<link rel='stylesheet' href='style.css'>";
echo "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css\" integrity=\"sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M\" crossorigin=\"anonymous\">";
session_start();
ignore_user_abort(1);  // Игнорировать обрыв связи с браузером
set_time_limit(0);       // Время работы скрипта неограниченно
InsertLogs("[wall_fake-mode]Отправил в обработку скрипт на оформление фейковой стенки заказов");
$val = $_SESSION['rez'];
$replace_val = str_replace("_", "/", $val);
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
$dec1 = api_query("GetMarketOrders", array('market' => $val, 100));
$count = 0;
foreach ($dec1->Data->Sell as $item) {
    $count++;
    if ($count > 1) break;
    $price = sprintf('%.8f', $item->Price);
}
InsertLogs("[wall_fake-mode]fix_price: $price");
$fix_price = $price - 0.00000005;
InsertLogs("fix_price: $fix_price");
$count_coins = $_GET['coins_count'];
InsertLogs("[wall_fake-mode]count_coins: $count_coins");
$count_query = $_GET['query_count1'];
//$count_query = 9;
InsertLogs("[wall_fake-mode]count_query: $count_query");
$percent1 = $_GET['percents'];
$percent = (100 - $percent1) / 100;
$count_coins_percent = ($count_coins * $count_query) * 0.85;
InsertLogs("[wall_fake-mode]Процентная часть: $percent");
for ($i = 0; $i <= $count_query; $i++)
{
    if ($_GET['buy_sell'] == 'Buy')
    {
        api_query("SubmitTrade", array('Market' => $val, 'Type' => "Buy", 'Rate' => $fix_price, 'Amount' => $count_coins));
        InsertLogs("[wall_fake-mode]Была выставленна стенка на ПОКУПКУ. Кол-во: $count_coins, по цене: $fix_price");
    } else
    {
        api_query("SubmitTrade", array('Market' => $val, 'Type' => "Sell", 'Rate' => $fix_price, 'Amount' => $count_coins));
        InsertLogs("[wall_fake-mode]Была выставленна стенка на ПРОДАЖУ. Кол-во: $count_coins, по цене: $fix_price");
    }
}
sleep(2);
while (true) {
    $dec = api_query("GetMarketOrders", array('market' => $val, 100));
    $count = 0;
    foreach ($dec->Data->Sell as $item)
    {
        $count++;
        if ($count > 1) break;
        //$last_price = sprintf('%.8f', $item->Price);
        $last_count_coins = $item->Volume;
        if ($last_count_coins < $count_coins_percent)
        {
            InsertLogs("[wall_fake-mode]Начался слив. Отмена стенки.");
            api_query("CancelTrade", array('Type' => "All"));
            InsertLogs("[wall_fake-mode]Стенка была отменена!");
            InsertLogs("Начинается перезапуск стенки...");
            for ($i = 0; $i < $count_query; $i++)
            {
                $fix_price_change = $fix_price * $percent;
                InsertLogs("[wall_fake-mode]fix_price_change: $fix_price_change");
                if ($_GET['buy_sell'] == 'Buy')
                {
                    InsertLogs("[wall_fake-mode]Цена была понижена на " . $percent1 . "%");
                    api_query("SubmitTrade", array('Market' => $val, 'Type' => "Buy", 'Rate' => $fix_price_change, 'Amount' => $count_coins));
                    InsertLogs("[wall_fake-mode]Была выставленна новая стенка на ПОКУПКУ. Кол-во: $count_coins, по цене: $fix_price_change");
                } else
                {
                    InsertLogs("[wall_fake-mode]Цена была понижена на " . $percent1 . "%");
                    api_query("SubmitTrade", array('Market' => $val, 'Type' => "Sell", 'Rate' => $fix_price_change, 'Amount' => $count_coins));
                    InsertLogs("[wall_fake-mode]Была выставленна новая стенка на ПРОДАЖУ. Кол-во: $count_coins, по цене: $fix_price_change");
                }
            }
        }
    }
    sleep(5);
}
?>