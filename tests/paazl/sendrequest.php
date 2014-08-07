<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\Paazl("fagassan", "sdfH4YGBusn4df4HDY54G");
var_dump($paazl->sendRequest());