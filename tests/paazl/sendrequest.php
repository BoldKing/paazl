<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "atabix", "1101br", "372", "haaksberg01", "DPD");
$paazl->addressRequest(23, "1055MN", "244") 
