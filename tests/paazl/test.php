<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "usernam", "1dsasasr", "31234", "sdniasdh");

/*
MANY MORE OPTIONS, but this is the least format
*/

$shippingAddress =array(
    "customerName"=>"Daan Hage",
    "street"=>"ADmiraal de RUijterweg",
    "housenumber"=>"244",
    "zipcode"=>"1055MN",
    "city"=>"Amsterdam",
);

$shippingMethod =array(
    "type"=>"delivery",
    "identifier"=>"0",
    "option"=>"D",
    "price"=>"0.5",
);
$orderID = 23;
$random = sha1(uniqid());

//var_dump($paazl->getShippingOptions($orderID));

 var_dump($paazl->orderDetails($orderID, 'dsadsadsasdaadsdas'));





