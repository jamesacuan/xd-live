<?php
class Database{
    private $db_name = "db758618865";

    /*private $host = "db758618865.db.1and1.com";
    private $username = "dbo758618865";
    private $password = "Y2fqLVrV@";*/
    
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    public  $conn;
 
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>