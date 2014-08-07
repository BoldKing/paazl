<?php
$baseDir = __DIR__."/../..";
require_once $baseDir.'/vendor/autoload.php';

$paazl = new Atabix\Paazl\PaazlClient(false, "atabix", "1101br", "372", "haaksberg01", "DPD");
$items = array();



/*
<?xml version="1.0" encoding="UTF-8"?>
<commitOrderRequest xmlns="http://www.paazl.com/schemas/matrix">
   <hash>d3ec78ecf39d6652159a56d95e2ef01827ce10c7</hash>
   <orderReference>100000035</orderReference>
   <pendingOrderReference>mijnreferentie</pendingOrderReference>
   <webshop>3</webshop>
   <totalAmount>1256</totalAmount>
   <customerEmail>customer@test.com</customerEmail>
   <shippingAddress>
      <street>Cornelis schuytstraat</street>
      <housenumber>51</housenumber>
      <zipcode>1071JG</zipcode>
      <city>Amsterdam</city>
      <country>NL</country>
      <customerName>Meneer Paazl</customerName>
   </shippingAddress>
   <shipperAddress>
      <addresseeLine>Webshop1.NL</addresseeLine>
      <street>Anna Bijnsring</street>
      <housenumber>182</housenumber>
      <zipcode>7321HJ</zipcode>
      <city>Apeldoorn</city>
      <country>NL</country>
   </shipperAddress>
   <returnAddress>
      <addresseeLine>Webshop1.NL</addresseeLine>
      <street>Adelaarstraat</street>
      <housenumber>76</housenumber>
      <zipcode>3514CH</zipcode>
      <city>Utrecht</city>
      <country>NL</country>
   </returnAddress>
   <shippingMethod>
      <type>delivery</type>
      <distributor>TNT</distributor>
      <identifier>50</identifier>
      <option>AVG_HIGH_LIABILITY</option>
      <price>5.0</price>
      <assuredAmount>1000</assuredAmount>
      <maxLabels>1</maxLabels>
      <orderWeight>7.5</orderWeight>
   </shippingMethod>
</commitOrderRequest>



<?xml version="1.0" encoding="UTF-8"?>
<commitOrderRequest xmlns="http://www.paazl.com/schemas/matrix">
   <hash>d3ec78ecf39d6652159a56d95e2ef01827ce10c7</hash>
   <orderReference>100000035</orderReference>
   <pendingOrderReference>mijnreferentie</pendingOrderReference>
   <webshop>3</webshop>
   <totalAmount>1256</totalAmount>
   <customerEmail>customer@test.com</customerEmail>
   <shippingAddress>
      <street>Cornelis schuytstraat</street>
      <housenumber>51</housenumber>
      <zipcode>1071JG</zipcode>
      <city>Amsterdam</city>
      <country>NL</country>
      <customerName>Meneer Paazl</customerName>
   </shippingAddress>
   <shipperAddress>
      <addresseeLine>Webshop1.NL</addresseeLine>
      <street>Anna Bijnsring</street>
      <housenumber>182</housenumber>
      <zipcode>7321HJ</zipcode>
      <city>Apeldoorn</city>
      <country>NL</country>
   </shipperAddress>
   <returnAddress>
      <addresseeLine>Webshop1.NL</addresseeLine>
      <street>Adelaarstraat</street>
      <housenumber>76</housenumber>
      <zipcode>3514CH</zipcode>
      <city>Utrecht</city>
      <country>NL</country>
   </returnAddress>
   <shippingMethod>
      <type>servicepoint</type>
      <option>DHL_SERVICE_POINT</option>
      <identifier>NL-105302</identifier>
      <servicepointNotificationEmail>notify@paazl.com</servicepointNotificationEmail>
      <price>2.5</price>
      <maxLabels>1</maxLabels>
   </shippingMethod>
</commitOrderRequest>
*/

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
    "description"=>"as",
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
    "description"=>"as",
    "countryOfManufacture"=>"JO",
    "unitPrice"=>"534.80",
    "unitPriceCurrency"=>"EUR"
);
$orderID = 23;
var_dump($paazl->commitOrder($orderID, $items));





