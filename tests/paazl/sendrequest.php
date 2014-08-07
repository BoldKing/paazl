<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "usernam", "1dsasasr", "31234", "sdniasdh");
$paazl->addressRequest(23, "1055MN", "244") 
