<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "sadads", "dasd", "372", "dsaads");

$orders = array();
$orders[] = array('orderReference'=>'test');
$orders[] = array('orderReference'=>'test2');
var_dump($paazl->generatePdfLabels($orders, "laser"));


