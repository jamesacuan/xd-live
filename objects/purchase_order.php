<?php
class PurchaseOrder{

    private $conn;
    private $table1_name = "purchase_order";
    private $table2_name = "purchase_order_details";
    private $table3_name = "purchase_order_status";

    public $userid;
    public $image_url;
    public $expectedJO;
    public $product; //if HH / TH
    public $created, $modified, $isDeleted;
    public $productitemid;
    public $quantity;
    public $color;
    public $note;
    public $status;
    public $total;
    public $type;
    public $sum;
    public $PODID, $POID;
    public $purchase_orderid;
    public $productname;


    public $count;

    public function __construct($db){
        $this->conn = $db;
    }

    function create(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');

        $query = "INSERT INTO " . $this->table1_name . "
                SET 
                    userid   = :userid,
                    created  = :created,
                    modified = :modified";

        $stmt = $this->conn->prepare($query);

        $this->userid     = htmlspecialchars(strip_tags($this->userid));     
        $this->created    = htmlspecialchars(strip_tags($this->created));
        $this->modified   = htmlspecialchars(strip_tags($this->modified));

        $stmt->bindParam(':userid', $this->userid);        
        $stmt->bindParam(':created', $this->created);
        $stmt->bindParam(':modified', $this->modified);

        if($stmt->execute()){
            return true;
        }
        else{
            $this->showError($stmt);
            return false;
        }
    }

    function addItem(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');
        $query = "INSERT INTO " . $this->table2_name . "
            SET
                type       = :type,
                product    = :product,
                productitemid = :productitemid,
                quantity   = :quantity,
                color      = :color,
                note       = :note,
                purchase_orderid = :purchase_orderid";
                //////////////id 	product_id 	quantity 	type 	additional_detail 	purchaseorder_code 

        $stmt = $this->conn->prepare($query);

        $this->product    = htmlspecialchars(strip_tags($this->product));
        $this->productitemid = htmlspecialchars(strip_tags($this->productitemid));
        $this->quantity   = htmlspecialchars(strip_tags($this->quantity));
        $this->color      = htmlspecialchars(strip_tags($this->color));
        $this->note       = htmlspecialchars(strip_tags($this->note));  
        $this->type   = htmlspecialchars(strip_tags($this->type));
        $this->purchase_orderid   = htmlspecialchars(strip_tags($this->purchase_orderid));

        $stmt->bindParam(':product', $this->product);        
        $stmt->bindParam(':productitemid', $this->productitemid);        
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':note', $this->note);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':purchase_orderid', $this->purchase_orderid);

        if($stmt->execute()){
            return true;
        }
        else{
            $this->showError($stmt);
            return false;
        }
    }

    function setStatus(){
        $this->created  = date('Y-m-d H:i:s');
        $query = "INSERT INTO " . $this->table3_name . "
            SET 
                status     = :status,
                purchase_orderid = :purchase_orderid,
                userid     = :userid,
                created    = :created";

        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->purchase_orderid   = htmlspecialchars(strip_tags($this->purchase_orderid));
        $this->userid      = htmlspecialchars(strip_tags($this->userid));
        $this->created       = htmlspecialchars(strip_tags($this->created));  

        $stmt->bindParam(':status', $this->status);        
        $stmt->bindParam(':purchase_orderid', $this->purchase_orderid);
        $stmt->bindParam(':userid', $this->userid);
        $stmt->bindParam(':created', $this->created);

        if($stmt->execute()){
            return true;
        }
        else{
            $this->showError($stmt);
            return false;
        }
    }

    function getLastPurchaseOrder(){
        $query = "SELECT max(id) AS total FROM " . $this->table1_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->total = $row['total'];
            return $this->total;
        }
        else{
            $this->showError($stmt);
            return false;
        }
    }

    function read(){
        $query = "SELECT purchase_order.id,
                    users.nickname,
                    purchase_order.created,
                    s1.status,
                    users.username
                FROM `purchase_order`
                JOIN users on users.userid = purchase_order.userid
                JOIN purchase_order_status s1 on s1.purchase_orderid = purchase_order.id
                WHERE purchase_order.isDeleted <> 'Y'
                AND s1.created = (SELECT MAX(s2.created) FROM purchase_order_status s2
                                    WHERE s2.purchase_orderid = s1.purchase_orderid)";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function deletePO(){
        $this->modified = date('Y-m-d H:i:s');
        $query = "UPDATE purchase_order
                 SET
                    isDeleted   = 'Y',
                    modified    = :modified
                 WHERE
                    id          = :id";
        $stmt = $this->conn->prepare($query);

        $this->modified          = htmlspecialchars(strip_tags($this->modified));
        $this->POID = htmlspecialchars(strip_tags($this->POID));

        $stmt->bindParam(':modified', $this->modified);
        $stmt->bindParam(':id',       $this->POID);

        $stmt->execute();
        return;
    }

    function isDeleted(){

    }

    function deletePOD(){
        $this->modified = date('Y-m-d H:i:s');
        $query = "UPDATE purchase_order_details
                SET
                    isDeleted = 'Y'
                WHERE
                    id        = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->PODID);

        /* CHECK IF PO LIST WILL BE EMPTY */
        $query3 = "SELECT count(*) as cc 
        FROM `purchase_order_details`
        JOIN purchase_order ON purchase_order.id = purchase_order_details.purchase_orderid
        WHERE purchase_order.id = :POID
        AND purchase_order_details.isDeleted <> 'Y'";

        $stmt3 = $this->conn->prepare($query3);
        $stmt3->bindParam(':POID', $this->POID);
        $stmt3->execute();

        $num3 = $stmt3->rowCount();

        if($num3>0){
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            $count = $row3['cc'];
        }


        /* DELETE PURCHASE ORDER*/
        if($count==1){
            $query4 = "UPDATE `purchase_order`
                SET
                    isDeleted   = 'Y',
                    modified    = :modified
                WHERE
                    id          = :id";

            $stmt4 = $this->conn->prepare($query4);

            $this->modified          = htmlspecialchars(strip_tags($this->modified));

            $stmt4->bindParam(':modified', $this->modified);
            $stmt4->bindParam(':id',       $this->POID);
               
            if($stmt4->execute() && $stmt->execute()){
                return true;
                
            }
            return false;
        }

        else if($stmt->execute()){
            return true;
        }

        return false;

    }
    function delete(){
        $this->modified = date('Y-m-d H:i:s');
        $jobid = "";
        $count = "";
        $query = "UPDATE " . $this->table2_name . "
                 SET
                    isDeleted   = 'Y',
                    modified    = :modified
                 WHERE
                    id          = :id";

        $stmt = $this->conn->prepare($query);

        $this->modified          = htmlspecialchars(strip_tags($this->modified));
        $this->joborderdetailsid = htmlspecialchars(strip_tags($this->joborderdetailsid));

        $stmt->bindParam(':modified', $this->modified);
        $stmt->bindParam(':id',       $this->joborderdetailsid);


        /* GET TO KNOW JOBORDERID OF JOBORDERITEM */
        $query2 = "SELECT `job_orderid` 
                FROM `job_order_details`
                WHERE id=:id";
        
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(':id', $this->joborderdetailsid);
        $stmt2->execute();   
        $num2 = $stmt2->rowCount();
    
        if($num2>0){
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            $jobid = $row2['job_orderid'];
        }

        /* CHECK IF JOBORDER WILL BE EMPTY */
        $query3 = "SELECT count(*) as cc
        FROM `job_order_details`
        JOIN job_order ON job_order.id = job_order_details.job_orderid
        WHERE job_order_details.job_orderid=:id
        AND job_order_details.isDeleted <> 'Y'";
       
        $stmt3 = $this->conn->prepare($query3);
        $stmt3->bindParam(':id', $jobid);
        $stmt3->execute();   
        $num3 = $stmt3->rowCount();
    
        if($num3>0){
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            $count = $row3['cc'];
        }

        /* DELETE JOB ORDER*/
        if($count==1){
            $query4 = "UPDATE `job_order`
                SET
                    isDeleted   = 'Y',
                    modified    = :modified
                WHERE
                    id          = :id";

            $stmt4 = $this->conn->prepare($query4);

            $this->modified          = htmlspecialchars(strip_tags($this->modified));

            $stmt4->bindParam(':modified', $this->modified);
            $stmt4->bindParam(':id',       $jobid);
               
            if($stmt4->execute() && $stmt->execute()){
                return true;
            }
            return false;
        }

        else if($stmt->execute()){
            return true;
        }

        return false;
    }

    function readPOD($POID){
        $query = "SELECT purchase_order.id,
                    users.nickname,
                    users.username,
                    purchase_order.created,
                    s1.status
                FROM `purchase_order`
                JOIN users on users.userid = purchase_order.userid
                JOIN purchase_order_status s1 on s1.purchase_orderid = purchase_order.id
                WHERE purchase_order.id = $POID
                AND   s1.created = (SELECT MAX(s2.created) FROM purchase_order_status s2
                                    WHERE s2.purchase_orderid = s1.purchase_orderid)";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->username  = $row['username'];
        $this->nickname  = $row['nickname'];
        $this->created   = $row['created'];
        $this->purchase_orderid = $row['id'];
        $this->status   = $row['status'];

        $stmt->execute();
        return $stmt;
    }

    function readPOItem($POID){
        $query = "SELECT p1.`id`,
        p1.`product`,
        p1.`type`,
        p1.`quantity`,
        product_color.name as color,
        p1.`note`,
        product_items.name as productname,
        product_items.`image_url`
        FROM purchase_order_details p1
        JOIN product_color ON product_color.id = p1.color
        JOIN product_items ON p1.productitemid = product_items.id
        WHERE p1.`purchase_orderid` = $POID
        AND p1.isDeleted <> 'Y'
 UNION
 SELECT p2.`id`,
        p2.`product`,
        p2.`type`,
        p2.`quantity`,
        product_color.name as color,
        p2.`note`,
        p2.`productitemid` as productname,
        p2.`productitemid` as image_url
        FROM purchase_order_details p2
        JOIN product_color ON product_color.id = p2.color
        WHERE (p2.productitemid = '0' OR p2.productitemid = 'undefined') AND
        p2.`purchase_orderid` = $POID
        AND p2.isDeleted <> 'Y'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id           = $row['id'];
        $this->type         = $row['type'];
        $this->quantity     = $row['quantity'];
        $this->image_url     = $row['image_url'];
        $this->color        = $row['color'];
        $this->note         = $row['note'];
        $this->productname  = $row['productname'];

        $stmt->execute();
        return $stmt;
    }

    function readPOSum($POID){
        $query = "SELECT sum(quantity) as sum from (
            SELECT p1.`id`,
                    p1.`product`,
                    p1.`type`,
                    p1.`quantity`,
                    product_color.name as color,
                    p1.`note`,
                    product_items.name as productname,
                    product_items.`image_url`
                    FROM purchase_order_details p1
                    JOIN product_color ON product_color.id = p1.color
                    JOIN product_items ON p1.productitemid = product_items.id
                    WHERE p1.`purchase_orderid` = $POID
                    AND p1.isDeleted <> 'Y'
             UNION
             SELECT p2.`id`,
                    p2.`product`,
                    p2.`type`,
                    p2.`quantity`,
                    product_color.name as color,
                    p2.`note`,
                    p2.`productitemid` as productname,
                    p2.`productitemid` as image_url
                    FROM purchase_order_details p2
                    JOIN product_color ON product_color.id = p2.color
                    WHERE (p2.productitemid = '0' OR p2.productitemid = 'undefined') AND
                    p2.`purchase_orderid` = $POID
                   AND p2.isDeleted <> 'Y'
            )SUM_PURCHASE_ORDER";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->sum          = $row['sum'];

        $stmt->execute();
        return $stmt;
    }

    function updateQty(){
        $this->modified = date('Y-m-d H:i:s');
        $query = "UPDATE purchase_order_details
                 SET
                    quantity    = :quantity
                 WHERE
                    id          = :id";

        $stmt = $this->conn->prepare($query);

        $this->quantity    = htmlspecialchars(strip_tags($this->quantity));
        $this->PODID        = htmlspecialchars(strip_tags($this->PODID));

        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':id',       $this->PODID);

        $stmt->execute();
        return;
    }

}