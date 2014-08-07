<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "usernam", "1dsasasr", "31234", "sdniasdh");
$items = array();
$items[] =array(
    "quantity"=>"3",
    "packagesPerUnit"=>"2",
    "matrix"=>"A",
    "weight"=>"0.5",
    "width"=>"15",
    "length"=>"13",
    "height"=>"20",
    "volume"=>"10",
    "code"=>"haisdads6",
    "description"=>"asd a dasd as",
    "countryOfManufacture"=>"JO",
    "unitPrice"=>"5.80",
    "unitPriceCurrency"=>"EUR"
);
$items[] =array(
    "quantity"=>"1",
    "packagesPerUnit"=>"2",
    "matrix"=>"B",
    "weight"=>"0.5",
    "width"=>"15",
    "length"=>"13",
    "height"=>"20",
    "volume"=>"10",
    "code"=>"haisdads6",
    "description"=>"asd a dasd as",
    "countryOfManufacture"=>"JO",
    "unitPrice"=>"534.80",
    "unitPriceCurrency"=>"EUR"
);

$orderID = sha1(uniqid()); // random test id
$paazl->createOrder($orderID, $items);
