<?php
use PayementAPI\connectDataBase\DB_Functions;

require 'SatimApi.php';
require 'connectDataBase\DB_Functions.php';

class Test
{

    private $sATIM;

    private $db_functions;

    function __construct()
    {
        $this->sATIM = new SATIM();
        $this->db_functions = new DB_Functions();
    }

    /**
     * Tests SATIM->register()
     *
     * @param string $userName
     * @param string $password
     * @param string $orderNumber
     * @param int $amount
     * @param string $currency
     * @param string $returnUrl
     * @param NULL $failUrl
     * @param NULL $description
     * @param NULL $language
     * @param NULL $pageView
     * @param NULL $clientId
     * @param string $jsonParams
     * @param NULL $sessionTimeoutSecs
     * @param NULL $expirationDate
     * @param NULL $bindingId
     */
    public function testRegister($userName, $password, $orderNumber, $amount, $currency, $returnUrl, $failUrl = NULL, $description = NULL, $language = NULL, $pageView = NULL, $clientId = NULL, $jsonParams, $sessionTimeoutSecs = NULL, $expirationDate = NULL, $bindingId = NULL)
    {
        $this->addUserToDataBase($userName, '', $password);
        
        // $this->addOrderToDataBase($userName, $password, $orderNumber, $amount, $currency, $returnUrl, $failUrl, $description, $language, $pageView, $clientId, $jsonParams, $sessionTimeoutSecs, $expirationDate, $bindingId);
        
        print "-- test register method [api]: -- \n";
        $result = $this->sATIM->register($userName, $password, $orderNumber, $amount, $currency, $returnUrl, $failUrl = NULL, $description = NULL, $language = NULL, $pageView = NULL, $clientId = NULL, $jsonParams, $sessionTimeoutSecs = NULL, $expirationDate = NULL, $bindingId = NULL);
        print_r($result);
    }

    /**
     *
     * @param string $userName
     * @param string $password
     * @param
     *            NULL string $email
     */
    public function addUserToDataBase($userName, $emil=NULL, $password)
    {
        print "Storing user to DATABASE: ---> ";
        if (! $this->db_functions->isUserExisted($userName)) {
            $user_details = $this->db_functions->createUser($userName, $userName."@gmail.com", $password);
            if ($user_details) {
                print "user successfully created. \n\n";
                print_r($user_details);
            }
        } else {
            print "user already exist ! \n\n";
            $user_details = $this->db_functions->getUserByUserNameAndPassword($userName, $password);
            print_r($user_details);
        }
    }

    /**
     *
     * @param string $userName
     * @param string $password
     * @param string $orderNumber
     * @param int $amount
     * @param string $currency
     * @param string $returnUrl
     * @param
     *            NULL string $failUrl
     * @param
     *            NULL string $description
     * @param
     *            NULL string $language
     * @param
     *            NULL string $pageView
     * @param
     *            NULL int $clientId
     * @param
     *            NULL array $jsonParams
     * @param
     *            NULL int $sessionTimeoutSecs
     * @param
     *            NULL DateTime $expirationDate
     * @param
     *            NULL int $bindingId
     */
    public function addOrderToDataBase($userName, $password, $orderNumber, $amount, $currency, $returnUrl, $failUrl, $description, $language, $pageView, $clientId, $jsonParams, $sessionTimeoutSecs, $expirationDate, $bindingId)
    {
        print "Storing order to DATABASE: ---> ";
        if (! $this->db_functions->isOrderExisted($orderNumber)) {
            $order_details = $this->db_functions->createOrder($userName, $password, $orderNumber, $amount, $currency, $returnUrl, $failUrl, $description, $language, $pageView, $clientId, $jsonParams, $sessionTimeoutSecs, $expirationDate, $bindingId);
            if ($order_details) {
                print "order successfully created. \n\n";
                print_r($order_details);
            }
        } else {
            print "order already exist ! \n\n";
        }
    }

    public function testConfirmOrder($userName, $password, $orderId, $language = NULL)
    {
        $result = $this->sATIM->confirmOrder($userName, $password, $orderId, $language);
        print "-- test confirmOrder method [api]: -- \n";
        print_r($result);
    }

    /**
     * Tests SATIM->reverse()
     */
    public function testReverse($userName, $password, $orderId, $language = NULL)
    {
        $result = $this->sATIM->reverse($userName, $password, $orderId, $language);
        print "-- test reverse method [api]: -- \n";
        print_r($result);
    }

    /**
     * Tests SATIM->getOrderStatus()
     */
    public function testGetOrderStatus($userName, $password, $orderId, $language = NULL)
    {
        $result = $this->sATIM->getOrderStatus($userName, $password, $orderId, $language);
        print "-- test getOrderStatus method [api]: -- \n";
        print_r($result);
    }

    /**
     * Tests SATIM->getLastOrdersForMerchants()
     */
    public function testGetLastOrdersForMerchants($userName, $password, $language = NULL, $page = NULL, $size, $from, $to, $transactionStates, $merchants)
    {
        $result = $this->sATIM->getLastOrdersForMerchants($userName, $password, $language, $page, $size, $from, $to, $transactionStates, $merchants);
        print "-- test getLastOrdersForMerchants method [api]: -- \n";
        print_r($result);
    }

    public function testRefund($userName, $password, $orderId, $amount)
    {
        $result = $this->sATIM->refund($userName, $password, $orderId, $amount);
        print "-- test refund method [api]: -- \n";
        print_r($result);
    }
}

$test = new Test();

$test->testRegister('dscs2018', 'satim120', 't133', 150, '012', 'finish.html', null, null, null, null, null, '{"orderNumber":"t128"}', null, null, null);

$test->testConfirmOrder('dscs2018', 'satim120', 't133', 'en');

$test->testReverse('dscs2018', 'satim120', 't133', 'en');

$test->testGetOrderStatus('dscs2018', 'satim120', 't133', 'en');

$test->testRefund('dscs2018', 'satim120', 't133', 150);

// error 404 URL
//$test->testGetLastOrdersForMerchants('dscs2018', 'satim120', 'en', '0', '100', '20141009160000', '20141111000000', 'DEPOSITED', 'SevenEightNine');

