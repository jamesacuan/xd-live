<?php
class Database{
 
    //private $host = "mysql.hostinger.ph";
    private $host = "localhost";
    private $db_name = "u166415122_xd";
    /*private $username = "u166415122_xuser";
    private $password = "Fe15N9oqD8";*/
    
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