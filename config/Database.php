<?php 

class Database {
    private $host = "127.0.0.1";
    private $username = "root";
    private $password = "";
    private $dbname = "Cartify-e-commerce-website-database";
    private $pdo;

    public function __construct(){ 
        $this->connect();
    }

    protected function connect() {
        
        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->username, $this->password); 
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection Failed ".$e->getMessage());
        }     
    }

    public function getConnection() {
        return $this->pdo;
    }
}