<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "dasd", "21323", "dasd", "3123");

/*
MANY MORE OPTIONS, but this is the least format
*/


$timezone = 'Europe/Amsterdam';
date_default_timezone_set($timezone);
var_dump($paazl->ordersToShip(date("Y-m-d H:i:s"), 'adas', $timezone));




