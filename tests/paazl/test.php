<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "atabix", "solutions1055", "372", "gvv14907");

$timezone = 'Europe/Amsterdam';
date_default_timezone_set($timezone);
var_dump($paazl->listOrders(date("2013-m-d H:i:s"), $timezone));



