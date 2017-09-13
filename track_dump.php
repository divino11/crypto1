<?php
session_start();
$val = $_SESSION['rez']; 

//echo $wall_val;
echo "<link rel='stylesheet' href='style.css'>";
function api_query2($method, array $req = array()) {
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
     $res = curl_exec($ch);
    $dec = json_decode($res);
	$dec1 = json_decode($res);
	//return $dec;
	foreach($dec1->Data as $item) {
	  $balance = sprintf('%.8f', $item->Total);
 }
 echo $balance;
}
 $arr = array();
 for ($k = 0; $k < 100; $k++) {
	 echo time();
$dec = api_query2("GetMarket", array( 'market' => "BTC_USDT", 'hours' => 24 ) );
echo ":" . time();
	$last_price = sprintf('%.8f', $dec->Data->LastPrice);
	echo "Last Price: " . $last_price . " | ";
	$sec = 10;
	array_push($arr, $last_price);
	$fix_price = $arr[count($arr) - $sec];
	$min_price = $fix_price;
	if (count($arr) >= $sec) {
		for ($i = count($arr) - $sec; $i < count($arr); $i++) {
			if ($arr[$i] < $min_price) {
				$min_price = $arr[$i];
			}
		}
	}
	echo "min price: " . $min_price . " | ";
	$fix_percent = $fix_price / 100;
	$min_percent = 100 - ($min_price / $fix_percent);
	echo " persent: " . $min_percent . "<br>";
	if ($min_percent > 10) {
			//api_query2("SubmitTrade",  array( 'Market' => $val, 'Type' => "Sell", 'Rate' => $last_price, 'Amount' => "" );
		}
sleep(1);
 }
echo api_query2("GetBalance", array( 'CurrencyId'=> 1 ) ) . PHP_EOL;
?>