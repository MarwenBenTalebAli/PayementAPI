<?php
namespace PayementAPI\connectDataBase;

class DB_Functions
{

    private $conn;

    // constructor
    function __construct()
    {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct()
    {}

    /**
     * Storing new user
     * returns user details
     */
    public function createUser($userName, $email, $password)
    {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        
        $stmt = $this->conn->prepare("INSERT INTO users(unique_id, userName, email, encrypted_password, salt, created_at) VALUES(?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $uuid, $userName, $email, $encrypted_password, $salt);
        $result = $stmt->execute();
        $stmt->close();
        
        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE userName = ?");
            $stmt->bind_param("s", $userName);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Get user by userName and password
     */
    public function getUserByUserNameAndPassword($userName, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE userName = ?");
        
        $stmt->bind_param("s", $userName);
        
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            // verifying user password
            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($userName)
    {
        $stmt = $this->conn->prepare("SELECT userName from users WHERE userName = ?");
        
        $stmt->bind_param("s", $userName);
        
        $stmt->execute();
        
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // user already exist
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    /**
     * Storing new order
     * returns order details
     */
    public function createOrder($userName, $password, $orderNumber, $amount, $currency, $returnUrl, $failUrl, $description, $language, $pageView, $clientId, $jsonParams, $sessionTimeoutSecs, $expirationDate, $bindingId)
    {
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $expiration_Date = date_format($expirationDate, "Y-m-d H:i:s");
        $stmt = $this->conn->prepare("INSERT INTO orders(userName, password, orderNumber, amount, currency, returnUrl, failUrl, description, language, pageView, clientId, jsonParams, sessionTimeoutSecs, expirationDate, bindingId, slat, created_at) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("???i??????i?i?i?", $userName, $password, $orderNumber, $amount, $currency, $returnUrl, $failUrl, $description, $language, $pageView, $clientId, (string) $jsonParams, $sessionTimeoutSecs, $expiration_Date, $bindingId, $slat);
        $result = $stmt->execute();
        $stmt->close();
        
        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE orderNumber = ?");
            $stmt->bind_param("s", $orderNumber);
            $stmt->execute();
            $order = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $order;
        } else {
            return false;
        }
    }
    
    /**
     * Check order is existed or not
     */
    public function isOrderExisted($orderNumber)
    {
        $stmt = $this->conn->prepare("SELECT orderNumber from orders WHERE orderNumber = ?");
        
        $stmt->bind_param("i", $orderNumber);
        
        $stmt->execute();
        
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // order already exist
            $stmt->close();
            return true;
        } else {
            // order not existed
            $stmt->close();
            return false;
        }
    }

    /**
     * Encrypting password
     *
     * @param
     *            password
     *            returns salt and encrypted password
     */
    public function hashSSHA($password)
    {
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array(
            "salt" => $salt,
            "encrypted" => $encrypted
        );
        return $hash;
    }

    /**
     * Decrypting password
     *
     * @param
     *            salt, password
     *            returns hash string
     */
    public function checkhashSSHA($salt, $password)
    {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        
        return $hash;
    }
}

?>
