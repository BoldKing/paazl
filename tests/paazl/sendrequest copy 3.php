<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "atabix", "1101br", "372", "haaksberg01", "DPD");
/* 
 * $paazl->addressRequest(23, "1055MN", "244") 
 *
 *
 *
 *
 */

/*
<orderRequest xmlns="http://www.paazl.com/schemas/matrix">
<hash>d3ec78ecf39d6652159a56d95e2ef01827ce10c7</hash>
 <orderReference>mijnreferentie</orderReference> 
 <webshop>3</webshop>
 <products> 
 <product><weight>50</weight>
  <width>100</width> 
  <height>30</height>
  <length>120</length>
  <unitPrice>4.50</unitPrice>
   <quantity>2</quantity>
    </product>
     <product>
     <matrix>B</matrix> 
     </product>
    </products>
</orderRequest>

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
*/
$paazl->createOrder();
$random = sha1(uniqid());
$orderID = 23;
var_dump($paazl->getShippingOptions($orderID, null, '1055MN'));