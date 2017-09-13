<?php
function api_query1($method, array $req = array()) {
//echo var_dump($req);
 $API_KEY = '307fa1b1f2e34b139d2a6d60478bc071';
 $API_SECRET = 'l8rTx0IGcCha9J7SxQHLwps8aFMJYVD5EmhEqdUD7Qw';
 $public_set = array( "GetCurrencies", "GetTradePairs", "GetMarkets", "GetMarket", "GetMarketHistory", "GetMarketOrders" );
 $private_set = array( "GetBalance", "GetDepositAddress", "GetOpenOrders", "GetTradeHistory", "GetTransactions", "SubmitTrade", "CancelTrade", "SubmitTip" );
 $ch = array();
    $mh = curl_multi_init();
    for($i = 0; $i < count($req); $i++) {     
   $ch[$i] = curl_init();
   curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch[$i], CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Cryptopia.co.nz API PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
 if ( in_array( $method ,$public_set ) ) {
   $url = "https://www.cryptopia.co.nz/api/" . $method;
   if ($req[$i]) { foreach ($req[$i] as $r ) { $url = $url . '/' . $r; } }
   curl_setopt($ch[$i], CURLOPT_URL, $url );
 } elseif ( in_array( $method, $private_set ) ) {
   $url = "https://www.cryptopia.co.nz/Api/" . $method;
   $nonce = explode(' ', microtime())[0];
   $post_data = json_encode( $req[$i] );
   $m = md5( $post_data, true );
   $requestContentBase64String = base64_encode( $m );
   $signature = $API_KEY . "POST" . strtolower( urlencode( $url ) ) . $nonce . $requestContentBase64String;
   $hmacsignature = base64_encode( hash_hmac("sha256", $signature, base64_decode( $API_SECRET ), true ) );
   $header_value = "amx " . $API_KEY . ":" . $hmacsignature . ":" . $nonce;
   $headers = array("Content-Type: application/json; charset=utf-8", "Authorization: $header_value");
   curl_setopt($ch[$i], CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch[$i], CURLOPT_URL, $url );
   curl_setopt($ch[$i], CURLOPT_POSTFIELDS, json_encode( $req[$i] ) );       
 }
     $res = curl_exec($ch[$i]);

     $dec = json_decode($res);
curl_multi_add_handle($mh,$ch[$i]);
 }
   $running=null;
//запускаем дескрипторы
do {
    curl_multi_exec($mh,$running);
} while($running > 0);
  
for($i = 0; $i < count($req); $i++) {
//закрываем все дескрипторы
curl_multi_remove_handle($mh, $ch[$i]);
}
curl_multi_close($mh); 
}
?>