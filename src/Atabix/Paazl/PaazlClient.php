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

    /**
     * @param string $username username given by Paazl
     * @param string $password password given by Paazl
     * @param string $webshopid webshopid given by Paazl
     * @param string $integrationpassword integrationpassword given by Paazl
     */
    public function __construct($islive, $username, $password, $webshopid, $integrationpassword)
    {
        $this->islive = $islive;
        $this->username = $username;
        $this->password = $password;
        $this->webshopid = $webshopid;
        $this->integrationpassword = $integrationpassword;
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
    
    public function commitOrder(
        $orderReference,
        $pendingOrderReference,
        $totalAmount,
        $shippingMethod,
        $shippingAddress,
        $totalAmountCurrency = "EUR",
        $shipperAddress = null,
        $returnAddress = null,
        $customerEmail = null,
        $customerPhoneNumber = null,
        $targetWebshop = null
    ) {
        // SET OPTIONAL ELEMNTS
        // shippingMethod
        $optElemShippingMethod = array(
            "distributor", // is not in documentation as an element but it is in the example....
            "servicepointNotificationEmail",
            "servicepointNotificationMobile",
            "customsValue",
            "customsValueCurrency",
            "assuredAmount",
            "assuredAmountCurrency",
            "collo",
            "packageCount",
            "maxLabels",
            "packagingType",
            "preferredDeliveryDate",
            "description"
        );
        // shippinAddress
        $optElemShippingAddress = array(
            "accountNumber",
            "companyName",
            "nameOther",
            "additionalAddressLine",
            "addition",
            "province",
            "country",
            "localAddressValidation",
            "additionalInstruction"
        );

        // shipper and return Address Elements
        $optShipperAndReturnAddress = array(
            "addresseeLine",
            "street",
            "housenumber",
            "addition",
            "zipcode",
            "city",
            "country"
        );



        // BUILD XML FILE
        $env = '
            <commitOrderRequest xmlns="http://www.paazl.com/schemas/matrix"> 
                <hash>'.$this->generateHash($pendingOrderReference).'</hash>
                <webshop>'.$this->webshopid.'</webshop>
                <orderReference>'.$orderReference.'</orderReference> 
                <pendingOrderReference>'.$pendingOrderReference.'</pendingOrderReference> 
                <totalAmount>'.$totalAmount.'</totalAmount> 
                <totalAmountCurrency>'.$totalAmountCurrency.'</totalAmountCurrency> 
        ';
        if ($customerEmail) {
            $env .= '<customerEmail>'.$customerEmail.'</customerEmail>';
        }
        if ($customerPhoneNumber) {
            $env .= '<customerPhoneNumber>'.$customerPhoneNumber.'</customerPhoneNumber>';
        }

        // shippingMethod
        $env .= '
            <shippingMethod>
                <type>'.$shippingMethod['type'].'</type> 
                <identifier>'.$shippingMethod['identifier'].'</identifier> 
                <option>'.$shippingMethod['option'].'</option>
                <price>'.$shippingMethod['price'].'</price> 
        ';
        // insert optional elements
        foreach ($shippingMethod as $element => $value) {
            if (in_array($element, $optElemShippingMethod)) {
                $env .= '<'.$element.'>'.$value.'</'.$element.'>';
            }
        }
        $env .= '</shippingMethod>';

        // shippingAddress
        $env .= '
            <shippingAddress>
                <customerName>'.$shippingAddress['customerName'].'</customerName> 
                <street>'.$shippingAddress['street'].'</street> 
                <housenumber>'.$shippingAddress['housenumber'].'</housenumber>
                <zipcode>'.$shippingAddress['zipcode'].'</zipcode> 
                <city>'.$shippingAddress['city'].'</city> 
        ';
        // insert optional elements
        foreach ($shippingAddress as $element => $value) {
            if (in_array($element, $optElemShippingAddress)) {
                $env .= '<'.$element.'>'.$value.'</'.$element.'>';
            }
        }
        $env .= '</shippingAddress>';

        // (Optional) shipperAddress
        if (is_array($shipperAddress)) {
            $env .= '<shipperAddress>';
            foreach ($shipperAddress as $element => $value) {
                if (in_array($element, $optShipperAndReturnAddress)) {
                    $env .= '<'.$element.'>'.$value.'</'.$element.'>';
                }
            }
            $env .= '</shipperAddress>';
        }

        // (Optional) returnAddress
        if (is_array($returnAddress)) {
            $env .= '<returnAddress>';
            foreach ($returnAddress as $element => $value) {
                if (in_array($element, $optShipperAndReturnAddress)) {
                    $env .= '<'.$element.'>'.$value.'</'.$element.'>';
                }
            }
            $env .= '</returnAddress>';
        }

        $env .= '</commitOrderRequest>';
        
        return $this->doCall($env);
    }


    public function orderStatus($orderReference, $includeLabels = null)
    {
        $env = '
            <orderStatusRequest xmlns="http://www.paazl.com/schemas/matrix"> 
            '.$this->mandatoryTags($orderReference)
        ;
        
        if ($includeLabels) {
            $env .= '<includeLabels>'.$includeLabels.'</includeLabels>';
        }
        $env .= '</orderStatusRequest>';
        
        return $this->doCall($env);
    }
        
    public function orderDetails($orderReference, $targetWebshop = null)
    {
        $env = '
            <orderDetailsRequest xmlns="http://www.paazl.com/schemas/matrix"> 
            '.$this->mandatoryTags($orderReference)
        ;
        
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }
        $env .= '</orderDetailsRequest>';
        
        return $this->doCall($env);
    }
        
    // OUD
    public function fastCommit($order)
    {
        $paazlClient = new PaazlClient();
        $insert = $paazlClient->createOrder($order);
        
        $shippingoptions = $paazlClient->shippingOption($order);
        
        if ($this->hasMultipleOptions($shippingoptions)) {
            return;
        }
        $firstOption = $paazlClient->firstOption($shippingoptions);
        $commit = $paazlClient->commitOrder($order, $firstOption);
    }
    
    public function firstOption($shippingoptions)
    {
        if (strlen($shippingoptions['response']['error']['message']) > 0) {
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
            <soapenv:Envelope 
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                xmlns:web="'.$wsdl.'">
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
        curl_setopt($soap, CURLOPT_URL, $wsdl);
        curl_setopt($soap, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap, CURLOPT_TIMEOUT, 10000);
        curl_setopt($soap, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap, CURLOPT_POST, true);
        curl_setopt($soap, CURLOPT_POSTFIELDS, $req);
        curl_setopt($soap, CURLOPT_HTTPHEADER, $header);
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
