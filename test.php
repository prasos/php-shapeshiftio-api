<?php 
require __DIR__ . '/vendor/autoload.php';

use ShapeShiftIO\ShapeShiftApi;

$api = new ShapeShiftApi();

$rate = $api->rate('btc_ltc');
var_dump($api->quotedPrice(1.3, 'ltc_btc'));
//var_dump($rate);