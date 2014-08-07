<?php

namespace Atabix\Paazl;


class PaazlClient
{
    
    public $liveurl     = "https://ost.paazl.com/parcelshipperservice/orderRequest.wsdl";
    public $stagingurl  = "http://staging.paazl.com/parcelshipperservice/orderRequest.wsdl";
    
    // Variables filled at construction once
    private $islive;
    private $username;
    private $password;
    private $webshopid;
    private $integrationpassword;
    private $distributor = "DPD";
    
    public static $status = array(
        "LABELS_NOT_CREATED" => "label not yet created",
        "LABELS_CREATED" => "label created, ready for shipment",
        "SCANNED" =>  "label scanned in sorting centre of distributor",
        "DELIVERED" =>  "delivered at address of receptor",
        "DELIVEREDBB" => "delivered at neighboring address of receptor",
        "PICKEDUP" => "picked up at drop off location, e.g. DHL Servicepoint"
    );

    /**
     * @param string $username username given by Paazl
     * @param string $password password given by Paazl
     * @param string $webshopid webshopid given by Paazl
     * @param string $integrationpassword integrationpassword given by Paazl
     */
    public function __construct($islive, $username, $password, $webshopid, $integrationpassword, $distributor)
    {
        $this->islive = $islive;
        $this->username = $username;
        $this->password = $password;
        $this->webshopid = $webshopid;
        $this->integrationpassword = $integrationpassword;
        $this->distributor = $distributor;
    }
    
    public function generateHash($orderReference)
    {
        $hash = sha1($this->webshopid . $this->integrationpassword . $orderReference);
        return $hash;
    }
    
    public function mandatoryTags($orderReference)
    {
        return '
            <hash>'.$this->generateHash($orderReference).'</hash>
            <webshop>'.$this->webshopid.'</webshop>
            <orderReference>'.$orderReference.'</orderReference> 
        ';
    }
    
    public function addressRequest($orderReference, $zipcode, $number, $addition = null)
    {
        $env = '
            <addressRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                '.$this->mandatoryTags($orderReference).'
                <zipcode>'.$zipcode.'</zipcode>
                <housenumber>'.$number.'</housenumber> 
        ';
        
        if ($addition) {
            $env .= '<addition>'.$addition.'</addition></addressRequest>';
        } else {
            $env .= '</addressRequest>';
        }
        
        return $this->doCall($env);
    }


    
    public function createOrder($orderReference, $products)
    {
        $productElements = array(
            "quantity",
            "packagesPerUnit",
            "matrix",
            "weight",
            "width",
            "length",
            "height",
            "volume",
            "code",
            "description",
            "countryOfManufacture",
            "unitPrice",
            "unitPriceCurrency"
        );

        $env = '
            <orderRequest xmlns="http://www.paazl.com/schemas/matrix"> 
                '.$this->mandatoryTags($orderReference).'
                <products>
        ';

        foreach ($products as $item) {
            $env.= '<product>';
            foreach ($item as $element => $value) {
                if (in_array($element, $productElements)) {
                    $env .= '<'.$element.'>'.$value.'</'.$element.'>';
                }
            }
            $env.= '</product>';
        }

        $env .= '
                </products>
            </orderRequest>
        ';
        
        return $this->doCall($env);
    }
            
    public function getShippingOptions(
        $orderReference,
        $country = "NL",
        $zipcode = null,
        $shippingOption = null,
        $targetWebshop = null
    ) {
        $env = '
            <shippingOptionRequest xmlns="http://www.paazl.com/schemas/matrix"> 
                '.$this->mandatoryTags($orderReference).'
                <country>'.$country.'</country>
        ';

        if ($zipcode) {
            $env .= '<postcode>'.$zipcode.'</postcode>';
        }
        if ($shippingOption) {
            $env .= '<shippingOption>'.$shippingOption.'</shippingOption>';
        }
        if ($shippingOption) {
            $env .= '<shippingOption>'.$shippingOption.'</shippingOption>';
        }
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }

        $env .= '</shippingOptionRequest>';
        
        return $this->doCall($env);
    }
    
    
    public function updateOrder($orderReference, $products)
    {
        $productElements = array(
            "quantity",
            "packagesPerUnit",
            "matrix",
            "weight",
            "width",
            "length",
            "height",
            "volume",
            "code",
            "description",
            "countryOfManufacture",
            "unitPrice",
            "unitPriceCurrency"
        );

        $env = '
            <updateOrderRequest xmlns="http://www.paazl.com/schemas/matrix"> 
                '.$this->mandatoryTags($orderReference).'
                <products>
        ';

        foreach ($products as $item) {
            $env.= '<product>';
            foreach ($item as $element => $value) {
                if (in_array($element, $productElements)) {
                    $env .= '<'.$element.'>'.$value.'</'.$element.'>';
                }
            }
            $env.= '</product>';
        }

        $env .= '
                </products>
            </updateOrderRequest>
        ';
        
        return $this->doCall($env);
    }
    
    public function commitOrder($orderReference, $shipping)
    {
        $env = '<commitOrderRequest xmlns="http://www.paazl.com/schemas/matrix"> 
            '.$this->mandatoryTags($orderReference).' 
            <pendingOrderReference>'.$orderReference.'</pendingOrderReference> 
            <totalAmount>'.$order->c['subtotal'].'</totalAmount> 
            <customerEmail>'.$mail.'</customerEmail> 
            <shippingAddress>
            <companyName>'.e($order->c['d_company']).'</companyName>
            <street>'.e($order->c['d_street']).'</street> 
            <housenumber>'.e($order->c['d_number']).e($order->c['d_number_add']).'</housenumber> 
            <zipcode>'.e($order->c['d_postalcode']).'</zipcode> 
            <city>'.e($order->c['d_city']).'</city>
            <country>'.e($order->c['d_country']).'</country>
            <customerName>'.trim(trim(e($order->c['d_fname'])." ".e($order->c['d_mname']))." ".e($order->c['d_lname'])).'</customerName>
            </shippingAddress> 
            <shippingMethod>
            <type>delivery</type> 
            <distributor>'.e($shipping['distributor']).'</distributor> 
            <identifier>'.e($shipping['deliverySchemeLineId']).'</identifier> 
            <option>'.e($shipping['type']).'</option>
            <price>0</price> 
            <maxLabels>1</maxLabels>
            </shippingMethod>
            </commitOrderRequest>
        ';
        
        return $this->doCall($env);
    }


        public function orderStatus($order)
        {
            $orderReference = $order->c['id'];	
            $env = '
            <orderStatusRequest xmlns="http://www.paazl.com/schemas/matrix"> 
            '.$this->mandatoryTags($orderReference).'
            <includeLabels>true</includeLabels>
            </orderStatusRequest>';
            
            return $this->doCall($env);
        }
        
    
            public function fastCommit($order)
            {
                Log::add("Fast commit order #".$order->c['id'], "paazl");
                $paazlClient = new PaazlClient();
                $insert = $paazlClient->createOrder($order);
                
                $shippingoptions = $paazlClient->shippingOption($order);
                
                if ($this->hasMultipleOptions($shippingoptions)) {
                    Log::add("Multiple shipping options, no auto selection", "paazl");
                    return;
                }
                $firstOption = $paazlClient->firstOption($shippingoptions);
                Log::add("First shipping option selected for order #".$order->c['id']." (".serialize($firstOption).")", "paazl");
                $commit = $paazlClient->commitOrder($order, $firstOption);
                Log::add("Commit order #".$order->c['id'], "paazl");
            }
            
            public function firstOption($shippingoptions)
            {			
                if (strlen($shippingoptions['response']['error']['message']) > 0) {
                    Log::add("Error shipping options: ".$shippingoptions['response']['error']['message'], "paazl");
                    return false;
                } elseif (count($shippingoptions['response']['shippingOptions']['shippingOption']) > 6) {
                    return $shippingoptions['response']['shippingOptions']['shippingOption'];
                } else {
                    return array_shift($shippingoptions['response']['shippingOptions']['shippingOption']);
                }
            }
            
            public function hasMultipleOptions($shippingoptions)
            {
                $count = count($shippingoptions['response']['shippingOptions']['shippingOption']);
                if ($count == 1 || $count > 6) {
                    return false;
                } else {
                    return true;
                }
            }
            
    
    
    /**
    * Download adress labels
    */
    public function getLabels($order) {
        $orderReference = $order->c['id'];
        $env = '
            <generatePdfLabelsRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                <webshop>'.$this->webshopid.'</webshop>
                <printer>laser</printer>
                <order>
                    <orderReference>'.$orderReference.'</orderReference>
                    <hash>'.$this->generateHash($orderReference).'</hash> 
                </order>
            </generatePdfLabelsRequest>
        ';
        
        $response = $this->doCall($env);
        
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=\"Paazl_labels_order_".$orderReference.".pdf\"");
        
        Log::add("Generate labels ".serialize($response), "paazl");
        
        exit(base64_decode($response['response']['labels'])); 
    }
    
    public function isNotCreated($status)
    {
        if ($status['response']['error']['code'] == 1002) {
            return true;
        } else {
            return false;
        }
    }
    
    public function printStatus($status)
    {
        if (isset($status['response']['error'])) {
            $s = $status['response']['error']['message']." (".$status['response']['error']['code'].")";
        } else {
            $s = $status['response']['orderStatus']['status'];
        }

        if ($this->status[$s]) {
            return $this->status[$s];
        } else {
            return $s;	
        }
    }


    /**
    *  doCall
    */
    private function doCall($env)
    {
        if ($this->islive) {
            $wsdl = $this->liveurl;
        } else {
            $wsdl = $this->stagingurl;
        }
        
        $req = '
            <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="'.$wsdl.'">
                <soapenv:Header/>
                <soapenv:Body>
                    '.$env.'
                </soapenv:Body>
            </soapenv:Envelope>
        ';
        
        $header = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: ".strlen($req)
        );
        
        
        
        $soap = curl_init();
        curl_setopt($soap, CURLOPT_URL, 		   $wsdl );
        curl_setopt($soap, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap, CURLOPT_TIMEOUT,        10000);
        curl_setopt($soap, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap, CURLOPT_POST,           true );
        curl_setopt($soap, CURLOPT_POSTFIELDS,     $req);
        curl_setopt($soap, CURLOPT_HTTPHEADER,     $header);
        curl_setopt($soap, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($soap);
        
        if ($res === false) { // If something went wrong print error
            $err = 'Curl error: ' . curl_error($soap);
            curl_close($soap);
            return array("status" => 0, "msg" => $err);
        } else {
            curl_close($soap);
            
            // Remove Soap tags
            $s = "<SOAP-ENV:Body>";
            $e = "</SOAP-ENV:Body>";
            $xml = substr($res, strpos($res, $s)+strlen($s), strpos($res, $e)-(strpos($res, $s)+strlen($s)));
            
            // Convert XML string to array
            $arr = json_decode(json_encode((array) simplexml_load_string($xml)), 1);
            
            return array("status" => 1, "response" => $arr);
        }	
    }	
}
