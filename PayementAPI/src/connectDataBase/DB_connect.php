<?php
namespace PayementAPI\connectDataBase;

use mysqli;

/**
 * A class file to connect to database
 */
class DB_Connect
{

    private $conn;

    // Connecting to database
    public function connect()
    {
        require_once 'connectDataBase/DB_config.php';
        
        // Connecting to mysql database
        $this->conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
        
        // return database handler
        return $this->conn;
    }
}

?>