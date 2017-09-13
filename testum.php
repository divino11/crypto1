<?php
/**
 * Created by PhpStorm.
 * User: barza
 * Date: 05.09.2017
 * Time: 19:17
 */
$mass = array();
for ( $i =1;$i<=100;$i++)
{
    $mass[$i-1]=$i;

}
echo var_dump($mass);
for($j=0;$j<count($mass);$j++)
{
   // echo $mass[$j]%2 . "|";
    if($mass[$j]%2!=0&&$mass[$j]%3!=0&& $mass[$j]%5!=0)
    {
       echo $mass[$j]."|";
    }
}
