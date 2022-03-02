<?php

class Config {

    // Database identifiers
    private const HOST = 'localhost';
    private const USER = 'admin_m152';
    private const PASS = 'Super123';
    private const NAME = 'm152';

    // Data source network
    private $dsn = 'mysql:host ' . self::HOST . ';dbname=' . self::NAME . '';

    // connection variable
    protected $conn = null;

    // Constructor
    public function __construct()
    {
        try {
            $this->conn = new PDO($this->dsn, self::USER, self::PASS);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Connection Failed : ' . $e->getMessage());
        }
        return $this->conn;
    }

    // sanitize Inputs
    public function TestInput($data){
        $data = strip_tags($data);
        $data = htmlspecialchars($data);
        $data = stripslashes($data);
        $data = trim($data);
        return $data;
    }

    //JSON Format converter function
    public function message($content, $status) {
        return json_encode(['message' => $content, 'error' => $status]);
    }
}

?>