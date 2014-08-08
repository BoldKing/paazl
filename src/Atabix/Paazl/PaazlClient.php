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
    
    private function mandatoryOrderRefTags($orderReference)
    {
        return '
            <hash>'.$this->generateHash($orderReference).'</hash>
            <webshop>'.$this->webshopid.'</webshop>
            <orderReference>'.$orderReference.'</orderReference> 
        ';
    }
    
    private function insertOptionalElements(&$env, $possibleValues, $permittedElements)
    {
        foreach ($possibleValues as $element => $value) {
            if (in_array($element, $permittedElements)) {
                if (is_array($value)) {
                    foreach ($value as $arrayValue) {
                        $env .= '<'.$element.'>'.$arrayValue.'</'.$element.'>';
                    }
                } else {
                    $env .= '<'.$element.'>'.$value.'</'.$element.'>';
                }
            }
        }
    }

    public function generateHash($orderReference)
    {
        $hash = sha1($this->webshopid . $this->integrationpassword . $orderReference);
        return $hash;
    }

    /************************************ CHAPTER 4 *************************************/
    public function addressRequest($orderReference, $zipcode, $number, $addition = null, $targetWebshop = null)
    {
        $env = '
            <addressRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                '.$this->mandatoryOrderRefTags($orderReference).'
                <zipcode>'.$zipcode.'</zipcode>
                <housenumber>'.$number.'</housenumber> 
        ';
        
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }

        if ($addition) {
            $env .= '<addition>'.$addition.'</addition></addressRequest>';
        } else {
            $env .= '</addressRequest>';
        }
        return $this->doCall($env);
    }


    
    /************************************ CHAPTER 5 *************************************/
    public function createOrder($orderReference, $products, $targetWebshop = null)
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
                '.$this->mandatoryOrderRefTags($orderReference).'
        ';
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }

        $env .= '<products>';
        foreach ($products as $item) {
            $env.= '<product>';
            $this->insertOptionalElements($env, $item, $productElements);
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
                '.$this->mandatoryOrderRefTags($orderReference).'
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
    
    
    public function updateOrder($orderReference, $products, $targetWebshop = null)
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
                '.$this->mandatoryOrderRefTags($orderReference).'
        ';
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }

                
        $env .= '<products>';
        foreach ($products as $item) {
            $env.= '<product>';
            $this->insertOptionalElements($env, $item, $productElements);
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
        // SET OPTIONAL ELEMENTS
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
        $env = '<commitOrderRequest xmlns="http://www.paazl.com/schemas/matrix"> 
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
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }

        // shippingMethod
        $env .= '<shippingMethod>
            <type>'.$shippingMethod['type'].'</type> 
            <identifier>'.$shippingMethod['identifier'].'</identifier> 
            <option>'.$shippingMethod['option'].'</option>
            <price>'.$shippingMethod['price'].'</price> 
        ';
        $this->insertOptionalElements($env, $shippingMethod, $optElemShippingMethod);
        $env .= '</shippingMethod>';

        // shippingAddress
        $env .= '<shippingAddress>
            <customerName>'.$shippingAddress['customerName'].'</customerName> 
            <street>'.$shippingAddress['street'].'</street> 
            <housenumber>'.$shippingAddress['housenumber'].'</housenumber>
            <zipcode>'.$shippingAddress['zipcode'].'</zipcode> 
            <city>'.$shippingAddress['city'].'</city> 
        ';
        $this->insertOptionalElements($env, $shippingAddress, $optElemShippingAddress);
        $env .= '</shippingAddress>';

        // (Optional) shipperAddress
        if (is_array($shipperAddress)) {
            $env .= '<shipperAddress>';
            $this->insertOptionalElements($env, $shipperAddress, $optShipperAndReturnAddress);
            $env .= '</shipperAddress>';
        }

        // (Optional) returnAddress
        if (is_array($returnAddress)) {
            $env .= '<returnAddress>';
            $this->insertOptionalElements($env, $returnAddress, $optShipperAndReturnAddress);
            $env .= '</returnAddress>';
        }

        $env .= '</commitOrderRequest>';
        
        return $this->doCall($env);
    }
    

    /************************************ CHAPTER 6 *************************************/
    public function orderStatus($orderReference, $targetWebshop = null, $includeLabels = null)
    {
        $env = '
            <orderStatusRequest xmlns="http://www.paazl.com/schemas/matrix"> 
            '.$this->mandatoryOrderRefTags($orderReference)
        ;
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }
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
            '.$this->mandatoryOrderRefTags($orderReference)
        ;
        
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }
        $env .= '</orderDetailsRequest>';
        
        return $this->doCall($env);
    }

    public function changeOrder(
        $orderReference,
        $newOrderReference = null,
        $targetWebshop = null,
        $totalAmount = null,
        $totalAmountCurrency = null,
        $customerEmail = null,
        $customerPhoneNumber = null,
        $shippingAddress = null,
        $shipperAddress = null,
        $returnAddress = null,
        $shippingMethod = null,
        $products = null
    ) {
        // SET OPTIONAL ELEMENTS
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
        // shippingMethod
        $optElemShippingMethod = array(
            "type",
            "identifier",
            "option",
            "price",
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
            "customerName",
            "street",
            "housenumber",
            "zipcode",
            "city",
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
        $env = '<changeOrderRequest xmlns="http://www.paazl.com/schemas/matrix"> 
            '.$this->mandatoryOrderRefTags($orderReference)
        ;

        if ($newOrderReference) {
            $env .= '<newOrderReference>'.$newOrderReference.'</newOrderReference>';
        }
        if ($totalAmount) {
            $env .= '<totalAmount>'.$totalAmount.'</totalAmount>';
        }
        if ($totalAmountCurrency) {
            $env .= '<totalAmountCurrency>'.$totalAmountCurrency.'</totalAmountCurrency>';
        }
        if ($customerEmail) {
            $env .= '<customerEmail>'.$customerEmail.'</customerEmail>';
        }
        if ($customerPhoneNumber) {
            $env .= '<customerPhoneNumber>'.$customerPhoneNumber.'</customerPhoneNumber>';
        }

        // shippingMethod
        if (is_array($shippingMethod)) {
            $env .= '<shippingMethod>';
            $this->insertOptionalElements($env, $shippingMethod, $optElemShippingMethod);
            $env .= '</shippingMethod>';
        }
        // shippingAddress
        if (is_array($shippingAddress)) {
            $env .= '<shippingAddress>';
            $this->insertOptionalElements($env, $shippingAddress, $optElemShippingAddress);
            $env .= '</shippingAddress>';
        }

        // (Optional) shipperAddress
        if (is_array($shipperAddress)) {
            $env .= '<shipperAddress>';
            $this->insertOptionalElements($env, $shipperAddress, $optShipperAndReturnAddress);
            $env .= '</shipperAddress>';
        }

        // (Optional) returnAddress
        if (is_array($returnAddress)) {
            $env .= '<returnAddress>';
            $this->insertOptionalElements($env, $returnAddress, $optShipperAndReturnAddress);
            $env .= '</returnAddress>';
        }

        //products
        if (is_array($products)) {
            $env .= '<products>';
            foreach ($products as $item) {
                $env.= '<product>';
                $this->insertOptionalElements($env, $item, $productElements);
                $env.= '</product>';
            }
            $env .= '</products>';
        }

        $env .= '</changeOrderRequest>';
        
        return $this->doCall($env);
    }
    
    public function ordersToShip($date = null, $targetWebshop = null, $dateDefaultTimezone = null)
    {
        if ($dateDefaultTimezone) {
            date_default_timezone_set($dateDefaultTimezone);
        }

        if (!$date) {
            $refDate = date("Ymd");
        } else {
            $refDate = date("Ymd", strtotime($date));
        }
        $env = '
            <ordersToShipRequest xmlns="http://www.paazl.com/schemas/matrix"> 
            <hash>'.$this->generateHash($refDate).'</hash>
            <webshop>'.$this->webshopid.'</webshop>
        ';

        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }
        if ($date) {
            $env .= '<deliveryDate>'.date("Y-m-d+H:i", strtotime($date)).'</deliveryDate>';
        }
        $env .= '</ordersToShipRequest>';
        return $this->doCall($env);
    }

    public function rateRequest(
        $orderReference,
        $targetWebshop = null,
        $postalCode = null,
        $country = null,
        $shippingOption = null
    ) {
        $env = '
            <rateRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                '.$this->mandatoryOrderRefTags($orderReference).'
        ';
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }
        if ($country) {
            $env .= '<country>'.$country.'</country>';
        }
        if ($postalCode) {
            $env .= '<postalCode>'.$postalCode.'</postalCode>';
        }
        if ($shippingOption) {
            $env .= '<shippingOption>'.$shippingOption.'</shippingOption>';
        }

        $env .= '</rateRequest>';
        return $this->doCall($env);
    }


    public function listOrders(
        $changedSince,
        $dateDefaultTimezone = null,
        $page = null,
        $targetWebshop = null,
        $country = null
    ) {
        if ($dateDefaultTimezone) {
            date_default_timezone_set($dateDefaultTimezone);
        }

        $env = '
            <listOrderRequest xmlns="http://www.paazl.com/schemas/matrix"> 
            <hash>'.$this->generateHash(date("YmdHis")).'</hash>
            <webshop>'.$this->webshopid.'</webshop>
            <changedSince>'.date("Ymd", strtotime($changedSince)).'</changedSince>
        ';
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }
        if ($country) {
            $env .= '<country>'.$country.'</country>';
        }
        if ($page) {
            $env .= '<page>'.$page.'</page>';
        }
        $env .= '</listOrderRequest>';
        return $this->doCall($env);
    }


    /************************************ CHAPTER 7 *************************************/
    public function generatePdfLabels($orders, $printer, $batch = null)
    {
        $env = '
            <generatePdfLabelsRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                <webshop>'.$this->webshopid.'</webshop>
                <printer>'.$printer.'</printer>
        ';
        foreach ($orders as $order) {
            $env .= '
                    <order>
                        <orderReference>'.$order['orderReference'].'</orderReference>
                        <hash>'.$this->generateHash($order['orderReference']).'</hash> 
            ';
            if ($order['targetWebshop']) {
                $env .= '<targetWebshop>'.$order['targetWebshop'].'</targetWebshop>';
            }
            $env .= '</order>';
        }

        if ($batch) {
            $env .= '<batch>'.$batch.'</batch>';
        }
        $env .= '</generatePdfLabelsRequest>';
        
        return $this->doCall($env);
    }

    public function generateExtraPdfLabelRequest($orderReference, $printer, $targetWebshop = null)
    {
        $env = '
            <generateExtraPdfLabelRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                '.$this->mandatoryOrderRefTags($orderReference).'
                <printer>'.$printer.'</printer>
        ';

        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }
        $env .= '</generateExtraPdfLabelRequest>';
        
        return $this->doCall($env);
    }
    

    public function generateImageLabels($orders)
    {
        $env = '
            <generateImageLabelsRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                <webshop>'.$this->webshopid.'</webshop>
        ';
        foreach ($orders as $order) {
            $env .= '
                    <order>
                        <orderReference>'.$order['orderReference'].'</orderReference>
                        <hash>'.$this->generateHash($order['orderReference']).'</hash> 
            ';
            if ($order['targetWebshop']) {
                $env .= '<targetWebshop>'.$order['targetWebshop'].'</targetWebshop>';
            }
            $env .= '</order>';
        }
        $env .= '</generateImageLabelsRequest>';
        return $this->doCall($env);
    }

    public function generateExtraImageLabel($orderReference, $targetWebshop = null)
    {
        $env = '
            <generateExtraImageLabelRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                '.$this->mandatoryOrderRefTags($orderReference).'
        ';

        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$targetWebshop.'</targetWebshop>';
        }
        $env .= '</generateExtraImageLabelRequest>';
        return $this->doCall($env);
    }

    /************************************ CHAPTER 8 *************************************/
    public function generatePdfReturnLabels($orders, $printer) // BETA, not confirmed that it works
    {
        $env = '
            <generatePdfReturnLabelsRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                <webshop>'.$this->webshopid.'</webshop>
                <printer>'.$printer.'</printer>
        ';
        foreach ($orders as $order) {
            $env .= '
                    <order>
                        <orderReference>'.$order['orderReference'].'</orderReference>
                        <hash>'.$this->generateHash($order['orderReference']).'</hash> 
            ';
            if ($order['targetWebshop']) {
                $env .= '<targetWebshop>'.$order['targetWebshop'].'</targetWebshop>';
            }
            if ($order['shippingOption']) {
                $env .= '<shippingOption>'.$order['shippingOption'].'</shippingOption>';
            }
            $env .= '</order>';
        }

        $env .= '</generatePdfReturnLabelsRequest>';
        return $this->doCall($env);
    }

    public function generateExtraPdfReturnLabel(
        $orderReference,
        $printer = null,
        $targetWebshop = null,
        $shippingOption = null
    ) { // BETA, not confirmed that it works
        $env = '
            <generateExtraPdfReturnLabelRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                '.$this->mandatoryOrderRefTags($orderReference).'
        ';
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$order['targetWebshop'].'</targetWebshop>';
        }
        if ($shippingOption) {
            $env .= '<shippingOption>'.$shippingOption.'</shippingOption>';
        }

        $env .= '</generateExtraPdfReturnLabelRequest>';
        return $this->doCall($env);
    }


    public function generateImageReturnLabels($orders) // BETA, not confirmed that it works
    {
        $env = '
            <generateImageReturnLabelsRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                <webshop>'.$this->webshopid.'</webshop>
        ';
        foreach ($orders as $order) {
            $env .= '
                    <order>
                        <orderReference>'.$order['orderReference'].'</orderReference>
                        <hash>'.$this->generateHash($order['orderReference']).'</hash> 
            ';
            if ($order['targetWebshop']) {
                $env .= '<targetWebshop>'.$order['targetWebshop'].'</targetWebshop>';
            }
            if ($order['shippingOption']) {
                $env .= '<shippingOption>'.$order['shippingOption'].'</shippingOption>';
            }
            $env .= '</order>';
        }
        $env .= '</generateImageReturnLabelsRequest>';
        return $this->doCall($env);
    }
    
    public function generateExtraImageReturnLabel(
        $orderReference,
        $targetWebshop = null,
        $shippingOption = null
    ) { // BETA, not confirmed that it works
        $env = '
            <generateExtraImageReturnLabelRequest xmlns="http://www.paazl.com/schemas/matrix"> 	
                '.$this->mandatoryOrderRefTags($orderReference).'
        ';
        if ($targetWebshop) {
            $env .= '<targetWebshop>'.$order['targetWebshop'].'</targetWebshop>';
        }
        if ($shippingOption) {
            $env .= '<shippingOption>'.$shippingOption.'</shippingOption>';
        }

        $env .= '</generateExtraImageReturnLabelRequest>';
        return $this->doCall($env);
    }
    

    // CHAPTER 9 TILL 15 COMING SOON....

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
