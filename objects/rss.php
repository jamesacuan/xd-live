<?php
class Rss{

    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    function joborderstream(){
        $query = "SELECT job_order.id as ID,
        CONCAT('JO') as XTABLE,
        users.nickname,
        job_order.created as created
        FROM job_order
        JOIN users ON users.userid = job_order.userid
        WHERE job_order.isDeleted <> 'Y'
 UNION
 SELECT purchase_order.id as ID,
        CONCAT('PO') as XTABLE,
       users.nickname,
       purchase_order.created as created
       FROM purchase_order
       JOIN users ON users.userid = purchase_order.userid
       WHERE purchase_order.isDeleted <> 'Y'
       ORDER BY created DESC
       LIMIT 20";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}