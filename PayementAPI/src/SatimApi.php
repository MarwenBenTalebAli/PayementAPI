<?php

/**
 * Represents global settings common to all payement operations. Settings can be
 * enabled here, or with each request.
 */
class SatimSettings
{

    private static $endPoint = 'http://test.satim.dz/payment/rest';

    private static $secureEndPoint = 'https://test.satim.dz/payment/rest';

    private static $enableEncryption = true;

    private static $enableCompression = true;

    private static $merchantName = 'dscs2018';

    private static $merchantPassword = 'satim120';

    private static $forceTerminalId = 'E005005093';

    public static function setEndPoint($endPoint)
    {
        if (! is_string($endPoint)) {
            throw new Exception('SATIM API Error: Invalid HTTP Endpoint');
        }
        
        self::$endPoint = $endPoint;
    }

    public static function getEndPoint()
    {
        return self::$endPoint;
    }

    public static function setSecureEndPoint($endPoint)
    {
        if (! is_string($endPoint)) {
            throw new Exception('SATIM API Error: Invalid HTTPS Endpoint');
        }
        
        self::$secureEndPoint = $endPoint;
    }

    public static function getSecureEndPoint()
    {
        return self::$secureEndPoint;
    }

    public static function setEnableCompression($enableCompression)
    {
        if (! is_bool($enableCompression)) {
            throw new Exception('SATIM API Error: enableCompression must be a bool');
        }
        
        self::$enableCompression = $enableCompression;
    }

    public static function getEnableCompression()
    {
        return self::$enableCompression;
    }

    public static function setEnableEncryption($enableEncryption)
    {
        if (! is_bool($enableEncryption)) {
            throw new Exception('SATIM API Error: enableEncryption must be a bool');
        }
        
        self::$enableEncryption = $enableEncryption;
    }

    public static function getEnableEncryption()
    {
        return self::$enableEncryption;
    }

    public static function setmerchantName($merchantName)
    {
        if (! is_string($merchantName)) {
            throw new Exception('SATIM API Error: merchantName must be a bool');
        }
        
        self::$merchantName = $merchantName;
    }

    public static function getmerchantName()
    {
        return self::$merchantName;
    }

    public static function setmerchantPassword($merchantPassword)
    {
        if (! is_string($merchantPassword)) {
            throw new Exception('SATIM API Error: merchantPassword must be a bool');
        }
        
        self::$merchantPassword = $merchantPassword;
    }

    public static function getchantPassword()
    {
        return self::$merchantPassword;
    }

    public static function setforceTerminalId($forceTerminalId)
    {
        if (! is_string($forceTerminalId)) {
            throw new Exception('SATIM API Error: forceTerminalId must be a bool');
        }
        
        self::$forceTerminalId = $forceTerminalId;
    }

    public static function getforceTerminalId()
    {
        return self::$forceTerminalId;
    }
}

class SatimConnection
{

    private $endPoint;

    private $secureEndPoint;

    private $enableEncryption;

    private $enableCompression;

    private $merchantName;

    private $merchantPassword;

    private $forceTerminalId;

    function __construct()
    {
        $this->endPoint = SatimSettings::getEndPoint();
        $this->secureEndPoint = SatimSettings::getSecureEndpoint();
        $this->enableEncryption = SatimSettings::getEnableEncryption();
        $this->enableCompression = SatimSettings::getEnableCompression();
        $this->merchantName = SatimSettings::getmerchantName();
        $this->merchantPassword = SatimSettings::getchantPassword();
        $this->forceTerminalId = SatimSettings::getforceTerminalId();
        
        if (! function_exists('curl_version')) {
            throw new Exception('SATIM API Error: API requires cURL support to be enabled on your PHP installation');
        }
    }

    public function setSecureEndPoint($endPoint)
    {
        if (! is_string($endPoint)) {
            throw new Exception('SATIM API Error: Invalid HTTPS Endpoint');
        }
        
        $this->secureEndPoint = $endPoint;
    }

    public function setEnableCompression($enableCompression)
    {
        if (! is_bool($enableCompression)) {
            throw new Exception('SATIM API Error: enableCompression must be a bool');
        }
        
        $this->enableCompression = $enableCompression;
    }

    public function setEnableEncryption($enableEncryption)
    {
        if (! is_bool($enableEncryption)) {
            throw new Exception('SATIM API Error: enableEncryption must be a bool');
        }
        
        $this->enableEncryption = $enableEncryption;
    }

    public static function setmerchantName($merchantName)
    {
        if (! is_string($merchantName)) {
            throw new Exception('SATIM API Error: merchantName must be a bool');
        }
        
        $this->merchantName = $merchantName;
    }

    public static function setmerchantPassword($merchantPassword)
    {
        if (! is_string($merchantPassword)) {
            throw new Exception('SATIM API Error: merchantPassword must be a bool');
        }
        
        $this->merchantPassword = $merchantPassword;
    }

    public static function setforceTerminalId($forceTerminalId)
    {
        if (! is_string($forceTerminalId)) {
            throw new Exception('SATIM API Error: forceTerminalId must be a bool');
        }
        
        $this->forceTerminalId = $forceTerminalId;
    }

    public function sendRequest($satimParams, $path = '', $method = 'POST', $contentType = NULL)
    {
        $ch = curl_init();
        
        if ($this->enableEncryption) {
            curl_setopt($ch, CURLOPT_URL, $this->secureEndPoint . $path);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->endPoint . $path);
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if ($this->enableCompression) {
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        }
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($satimParams));
        
        $headers = array();
        
        if ($contentType) {
            $headers[] = 'Content-Type: ' . $contentType;
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $reply = curl_exec($ch);
        
        $rc = curl_errno($ch);
        if (0 != $rc) {
            throw new Exception('SATIM API Error: Network problem connecting to SATIM API. CURL Error Code:' . $rc);
        }
        
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (200 != $httpStatus) {
            throw new Exception('SATIM API Error: sendRequest returned HTTP code: ' . $httpStatus . ' Message:' . $reply);
        }
        
        curl_close($ch);
        unset($ch);
        
        $jsonReply = json_decode($reply, true);
        
        return $jsonReply;
    }
}

class SATIM extends SatimConnection
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This request is designed for registering orders in the payment gate.
     */
    public function register($userName, $password, $orderNumber, $amount, $currency, $returnUrl, $failUrl = NULL, $description = NULL, $language = NULL, $pageView = NULL, $clientId = NULL, $jsonParams, $sessionTimeoutSecs = NULL, $expirationDate = NULL, $bindingId = NULL)
    {
        $request = array();
        
        if (! is_string($userName)) {
            throw new Exception('SATIM API Error: register userName is empty or in the wrong format.');
        } else {
            $request["userName"] = $userName;
        }
        
        if (! is_string($password)) {
            throw new Exception('SATIM API Error: register password is empty or in the wrong format.');
        } else {
            $request["password"] = $password;
        }
        
        if (! is_string($orderNumber)) {
            throw new Exception('SATIM API Error: register orderNumber is empty or in the wrong format.');
        } else {
            $request["orderNumber"] = $orderNumber;
        }
        
        if (! is_int($amount)) {
            throw new Exception('SATIM API Error: register amount is empty or in the wrong format.');
        } else {
            $request["amount"] = $amount;
        }
        
        if (! is_string($currency)) {
            throw new Exception('SATIM API Error: register currency is empty or in the wrong format.');
        } else {
            $request["currency"] = $currency;
        }
        
        if (! is_string($returnUrl)) {
            throw new Exception('SATIM API Error: register returnUrl is empty or in the wrong format.');
        } else {
            $request["returnUrl"] = $returnUrl;
        }
        
        if (! is_string($jsonParams)) {
            throw new Exception('SATIM API Error: register jsonParams is empty or in the wrong format.');
        } else {
            $request["jsonParams"] = $jsonParams;
        }
        
        if (isset($failUrl)) {
            $request["failUrl"] = $failUrl;
        }
        if (isset($description)) {
            $request["description"] = $description;
        }
        if (isset($language)) {
            $request["language"] = $language;
        }
        if (isset($pageView)) {
            $request["pageView"] = $pageView;
        }
        if (isset($clientId)) {
            $request["clientId"] = $clientId;
        }
        if (isset($sessionTimeoutSecs)) {
            $request["sessionTimeoutSecs"] = $sessionTimeoutSecs;
        }
        if (isset($expirationDate)) {
            $request["expirationDate"] = $expirationDate;
        }
        if (isset($bindingId)) {
            $request["bindingId"] = $bindingId;
        }
        
        $request = empty($request) ? [] : $request;
        
        // TODO Print result ERRORS & fill the remaining Params in the request
        return $this->sendRequest($request, "/register.do", "POST", 'application/x-www-form-urlencoded');
    }

    /**
     * The request should be used to confirm that a merchant has successfully handled a client redirection back to merchant page after successful payment attempt.
     * Returns order status details. If no confirmation request received by payment gateway then an order will be automatically
     * reversed after some delay.
     */
    public function confirmOrder($userName, $password, $orderId, $language = NULL)
    {
        $request = array();
        
        if (! is_string($userName)) {
            throw new Exception('SATIM API Error: confirmOrder userName is empty or in the wrong format.');
        } else {
            $request["userName"] = $userName;
        }
        
        if (! is_string($password)) {
            throw new Exception('SATIM API Error: confirmOrder password is empty or in the wrong format.');
        } else {
            $request["password"] = $password;
        }
        
        if (! is_string($orderId)) {
            throw new Exception('SATIM API Error: confirmOrder orderId is empty or in the wrong format.');
        } else {
            $request["orderId"] = $orderId;
        }
        
        if (isset($language)) {
            $request["language"] = $language;
        }
        
        $request = empty($request) ? [] : $request;
        
        // TODO print Result ERRORS & fill the remaining Params in the request
        return $this->sendRequest($request, "/confirmOrder.do", "POST", 'application/x-www-form-urlencoded');
    }

    /**
     * To cancel payment of the order, use reverse.do request.
     * This functionality is available for a limited period, that is specified by the Bank. The reversal operation may be performed only once. If the reversal request caused an error, the next try will not be successful. The reversal operation is available for merchants only under agreement with the Bank. To perform the reversal request a user must have an appropriate permission.
     */
    public function reverse($userName, $password, $orderId, $language = NULL)
    {
        if (! is_string($userName)) {
            throw new Exception('SATIM API Error: reverse userName is empty or in the wrong format.');
        } else {
            $request["userName"] = $userName;
        }
        
        if (! is_string($password)) {
            throw new Exception('SATIM API Error: reverse password is empty or in the wrong format.');
        } else {
            $request["password"] = $password;
        }
        
        if (! is_string($orderId)) {
            throw new Exception('SATIM API Error: reverse orderId is empty or in the wrong format.');
        } else {
            $request["orderId"] = $orderId;
        }
        
        if (isset($language)) {
            $request["language"] = $language;
        }
        
        $request = empty($request) ? [] : $request;
        
        // TODO print Result ERRORS & fill the remaining Params in the request
        return $this->sendRequest($request, "/reverse.do", "POST", 'application/x-www-form-urlencoded');
    }

    /**
     * This request returns money paid for the order back to the client.
     * Request causes an error if the client is not charged. The payment gate allows multiple refunds, but their total amount cannot exceed the amount that was diposited from the client's account as a result of the order.
     */
    public function refund($userName, $password, $orderId, $amount)
    {
        if (! is_string($userName)) {
            throw new Exception('SATIM API Error: refund userName is empty or in the wrong format.');
        } else {
            $request["userName"] = $userName;
        }
        
        if (! is_string($password)) {
            throw new Exception('SATIM API Error: refund password is empty or in the wrong format.');
        } else {
            $request["password"] = $password;
        }
        
        if (! is_string($orderId)) {
            throw new Exception('SATIM API Error: refund orderId is empty or in the wrong format.');
        } else {
            $request["orderId"] = $orderId;
        }
        
        if (! is_int($amount)) {
            throw new Exception('SATIM API Error: refund amount is empty or in the wrong format.');
        } else {
            $request["amount"] = $amount;
        }
        
        $request = empty($request) ? [] : $request;
        
        // TODO print Result ERRORS & fill the remaining Params in the request
        return $this->sendRequest($request, "/refund.do", "POST", 'application/x-www-form-urlencoded');
    }

    // get the current state of a registered order
    public function getOrderStatus($userName, $password, $orderId, $language = NULL)
    {
        if (! is_string($userName)) {
            throw new Exception('SATIM API Error: getOrderStatus userName is empty or in the wrong format.');
        } else {
            $request["userName"] = $userName;
        }
        
        if (! is_string($password)) {
            throw new Exception('SATIM API Error: getOrderStatus password is empty or in the wrong format.');
        } else {
            $request["password"] = $password;
        }
        
        if (! is_string($orderId)) {
            throw new Exception('SATIM API Error: getOrderStatus orderId is empty or in the wrong format.');
        } else {
            $request["orderId"] = $orderId;
        }
        
        if (isset($language)) {
            $request["language"] = $language;
        }
        
        $request = empty($request) ? [] : $request;
        
        // TODO print Result ERRORS & fill the remaining Params in the request
        return $this->sendRequest($request, "/getOrderStatus.do", "POST", 'application/x-www-form-urlencoded');
    }

    // method allows you to obtain statistics on payments for a certain period
    public function getLastOrdersForMerchants($userName, $password, $language = NULL, $page = NULL, $size, $from, $to, $transactionStates, $merchants)
    {
        if (! is_string($userName)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants userName is empty or in the wrong format.');
        } else {
            $request["userName"] = $userName;
        }
        
        if (! is_string($password)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants password is empty or in the wrong format.');
        } else {
            $request["password"] = $password;
        }
        
        if (! is_string($language)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants language is empty or in the wrong format.');
        } else {
            $request["language"] = $language;
        }
        
        if (! is_string($page)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants page is empty or in the wrong format.');
        } else {
            $request["page"] = $page;
        }
        
        if (! is_string($size)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants size is empty or in the wrong format.');
        } else {
            $request["size"] = $size;
        }
        
        if (! is_string($from)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants from is empty or in the wrong format.');
        } else {
            $request["from"] = $from;
        }
        
        if (! is_string($to)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants to is empty or in the wrong format.');
        } else {
            $request["to"] = $to;
        }
        
        if (! is_string($transactionStates)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants transactionStates is empty or in the wrong format.');
        } else {
            $request["transactionStates"] = $transactionStates;
        }
        
        if (! is_string($merchants)) {
            throw new Exception('SATIM API Error: getLastOrdersForMerchants merchants is empty or in the wrong format.');
        } else {
            $request["merchants"] = $merchants;
        }
        
        $request = empty($request) ? [] : $request;
        // TODO print Result ERRORS & fill the remaining Params in the request
        return $this->sendRequest($request, "/getLastOrdersForMerchants.do", "POST", 'application/x-www-form-urlencoded');
    }
}