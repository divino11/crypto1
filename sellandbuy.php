<?php
session_start();
$val = $_SESSION['rez']; 

//echo $wall_val;
echo "<link rel='stylesheet' href='style.css'>";
function api_query($method, array $req = array()) {
 $API_KEY = '307fa1b1f2e34b139d2a6d60478bc071';
 $API_SECRET = 'l8rTx0IGcCha9J7SxQHLwps8aFMJYVD5EmhEqdUD7Qw';
 $public_set = array( "GetCurrencies", "GetTradePairs", "GetMarkets", "GetMarket", "GetMarketHistory", "GetMarketOrders" );
 $private_set = array( "GetBalance", "GetDepositAddress", "GetOpenOrders", "GetTradeHistory", "GetTransactions", "SubmitTrade", "CancelTrade", "SubmitTip" );
 static $ch = null;
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Cryptopia.co.nz API PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
 if ( in_array( $method ,$public_set ) ) {
   $url = "https://www.cryptopia.co.nz/api/" . $method;
   if ($req) { foreach ($req as $r ) { $url = $url . '/' . $r; } }
   curl_setopt($ch, CURLOPT_URL, $url );
 } elseif ( in_array( $method, $private_set ) ) {
   $url = "https://www.cryptopia.co.nz/Api/" . $method;
   $nonce = explode(' ', microtime())[1];
   $post_data = json_encode( $req );
   $m = md5( $post_data, true );
   $requestContentBase64String = base64_encode( $m );
   $signature = $API_KEY . "POST" . strtolower( urlencode( $url ) ) . $nonce . $requestContentBase64String;
   $hmacsignature = base64_encode( hash_hmac("sha256", $signature, base64_decode( $API_SECRET ), true ) );
   $header_value = "amx " . $API_KEY . ":" . $hmacsignature . ":" . $nonce;
   $headers = array("Content-Type: application/json; charset=utf-8", "Authorization: $header_value");
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_URL, $url );
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $req ) );
 }
 // run the query
  //  echo time();
 $res = curl_exec($ch);
    $dec = json_decode($res);
    $count = 0;
    $ryka = 0;
    foreach($dec->Data as $item) {

        echo "<table class='table_blur'>";
        echo "<tr>";
        echo "<th>";
        if ($ryka == 0) {
            echo "Валюта Sell";
        } else {
            echo "Валюта Buy";
        }
        $ryka = 1;
        echo "</th>";
        echo "<th>";
        echo "Цена";
        echo "</th>";
        echo "<th>";
        echo "Кол-во";
        echo "</th>";
        echo "<th>";
        echo "Общая цена";
        echo "</th>";
        echo "</tr>";
        foreach ($item as $item1) {
            $count++;
            if ($count > 10) break;
            $price_table = sprintf('%.8f', $item1->Price);
            /*$price_change = $price_table - 0.00000005;
            $price_max_sell_change = sprintf('%.8f', $price_change);*/
            echo "<tr>";
            echo "<td>";
            echo $item1->Label; 
            echo "</td>";
            echo "<td>";
            echo $price_table;
            echo "</td>";
            echo "<td>";
            echo $item1->Volume; 
            echo "</td>";
            echo "<td>";
            echo $item1->Total; 
            echo "</td>";
            echo "</tr>";
        }
        $count = 0;
        echo "</table>";
    }

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
    }
    if (!is_array($_SESSION['x1'])) {
        $_SESSION['x1'] = $price_max_buy;
        $_SESSION['x2'] = $price_max_sell;
    }
 if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
 //return $res;
}



//function ViewOrder ($buy_order) {
//    
//    
//}



// Public:
// +
// echo api_query( "GetCurrencies" );
// +
// echo api_query( "GetTradePairs" );
// +
// echo api_query( "GetMarkets" );
// +
//echo api_query( "GetMarkets", array( 6 ) );
// +
// echo api_query("GetMarket", array( 100, 6 ) );
// +
//echo api_query("GetMarketHistory", array( 100 ) );
// Выводит все buy и sell

echo api_query("GetMarketOrders", array( 'market' => $val, 100 ) );



// Private:
// +
//echo api_query("GetBalance", array( 'CurrencyId'=> 1 ) ) . PHP_EOL;

// +
//echo api_query("GetOpenOrders", array( 'Market'=> "DOT/DOGE" ) );


//echo api_query("SubmitTrade", array ( 'Market' => "DOT/BTC", 'Type' => "Sell", 'Rate' => "0.10000", 'Amount' => "123.00000000" ));

 
?>