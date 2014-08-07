<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "dsad", "adsasd", "asd", "asdas");

$orders = array();
$orders[] = array('orderReference'=>'test');
/* $orders[] = array('orderReference'=>'test2', 'shippingOption'=>'UPS_STANDARD_PICKUP'); */
//var_dump($paazl->generateImageReturnLabels($orders));
var_dump($paazl->generateExtraImageReturnLabel('test'));


