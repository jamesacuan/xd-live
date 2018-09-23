<?php
class Product{
 
    private $conn;
    private $table1_name = "products";
    private $table2_name = "product_items";
    
    public $productitemid;
    public $productitemname;
    public $visibility; //userid
    public $image_url;
    public $name;
    public $code; //joborderdetails.code
    public $created, $modified, $isDeleted;
    public $jodid; //joborderdetails_id
    public $type;
    public $producttype;
    public $productname;
    public $productcategory;
    public $note;
    public $userid;
    public $jod_id;

    public function __construct($db){
        $this->conn = $db;
    }
    
    function setProduct(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');
        
        $query = "INSERT INTO " . $this->table1_name . "
            SET 
                name = :name,
                code = :code,
                image_url  = :image_url,
                created    = :created,
                modified   = :modified,
                visibility = :visibility,
                productid  = :productid,
                jodid      = :jodid";

        $stmt = $this->conn->prepare($query);

        $this->productitemname  = htmlspecialchars(strip_tags($this->productitemname));
        $this->image_url        = htmlspecialchars(strip_tags($this->image_url)); 
        $this->code       = htmlspecialchars(strip_tags($this->code));    
        $this->created    = htmlspecialchars(strip_tags($this->created));
        $this->modified   = htmlspecialchars(strip_tags($this->modified));
        $this->visibility = htmlspecialchars(strip_tags($this->visibility));     
        $this->productid  = htmlspecialchars(strip_tags($this->productid));
        $this->jodid      = htmlspecialchars(strip_tags($this->jodid));


        $stmt->bindParam(':name',       $this->productitemname);        
        $stmt->bindParam(':image_url',  $this->image_url);
        //$stmt->bindParam(':note',       $this->note);
        $stmt->bindParam(':created',    $this->created);
        $stmt->bindParam(':modified',   $this->modified);
        $stmt->bindParam(':visibility', $this->visibility);
        $stmt->bindParam(':productid',  $this->productid);
        $stmt->bindParam(':jodid',      $this->jodid);

        if($stmt->execute()){
            return true;
        }
        else{
            $this->showError($stmt);
            return false;
        }
    }

    function setProductItem(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');
        
        $query = "INSERT INTO " . $this->table2_name . "
            SET
                `code`       = :code, 
                `name`       = :name,
                `image_url`  = :image_url,
                `type`       = :type,
                `created`    = :created,
                `modified`   = :modified,
                `visibility` = :visibility,
                `jodid`      = :jodid";

        $stmt = $this->conn->prepare($query);

        $this->productitemname  = htmlspecialchars(strip_tags($this->productitemname));
        $this->image_url        = htmlspecialchars(strip_tags($this->image_url));
        $this->visibility = htmlspecialchars(strip_tags($this->visibility));    
        $this->jodid      = htmlspecialchars(strip_tags($this->jodid));
        $this->type       = htmlspecialchars(strip_tags($this->type));     
        $this->code      = htmlspecialchars(strip_tags($this->code));
        $this->created    = htmlspecialchars(strip_tags($this->created));
        $this->modified   = htmlspecialchars(strip_tags($this->modified));

        $stmt->bindParam(':name',       $this->productitemname);        
        $stmt->bindParam(':image_url',  $this->image_url);
        $stmt->bindParam(':type',       $this->type);
        $stmt->bindParam(':created',    $this->created);
        $stmt->bindParam(':modified',   $this->modified);
        $stmt->bindParam(':visibility', $this->visibility);
        //$stmt->bindParam(':productid',  $this->productid);
        $stmt->bindParam(':jodid',      $this->jodid);
        $stmt->bindParam(':code',      $this->code);

        if($stmt->execute()){
            return true;
        }
        else{
            $this->showError($stmt);
            return false;
        }
    }

    function addProduct(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');

        $query = "INSERT INTO " . $this->table2_name . "
            SET
                `name`       = :name,
                `image_url`  = :image_url,
                `type`       = :type,
                `created`    = :created,
                `modified`   = :modified,
                `visibility` = 0";

        $stmt = $this->conn->prepare($query);

        $this->productitemname  = htmlspecialchars(strip_tags($this->productitemname));
        $this->image_url        = htmlspecialchars(strip_tags($this->image_url));
        $this->type       = htmlspecialchars(strip_tags($this->type));     
        $this->created    = htmlspecialchars(strip_tags($this->created));
        $this->modified   = htmlspecialchars(strip_tags($this->modified));

        $stmt->bindParam(':name',       $this->productitemname);        
        $stmt->bindParam(':image_url',  $this->image_url);
        $stmt->bindParam(':type',       $this->type);
        $stmt->bindParam(':created',    $this->created);
        $stmt->bindParam(':modified',   $this->modified);

        if($stmt->execute()){
            return true;
        }
        else{
            $this->showError($stmt);
            return false;
        }
    }

    function getProductItemCount(){
        $query = "SELECT max(id) AS total FROM product_item";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];;
        }
        return false;
    }

    function getProductItem($id){
        $query = "SELECT 
        product_items.name,
        product_items.image_url,
        product_items.jodid,
        product_items.type
        FROM product_items
        WHERE product_items.id = $id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name      = $row['name'];
        $this->image_url = $row['image_url'];
        $this->jod_id    = $row['jodid'];

        $stmt->execute();
        return $stmt;
    }

    function readItems($type, $from_record_num, $records_per_page){
        $query = "SELECT
                product_items.id,
                product_items.`name`,
                product_items.`image_url`,
                product_items.`modified`,
                product_items.`code`,
                product_items.`type`,
                product_items.visibility
                FROM product_items
                WHERE product_items.isDeleted <> 'Y' AND
                product_items.type LIKE '%{$type}%'
                ORDER BY product_items.name ASC
                limit {$from_record_num}, {$records_per_page}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name  = $row['name'];
        $stmt->execute();
        return $stmt;
    }

    function getProductItemsCount($type){
        $query = "SELECT 
            count(*) as total
            FROM product_items
            WHERE product_items.type LIKE '%{$type}%'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name  = $row['total'];
        return $this->name;
    }
    
    function getItemCount($type){
        $query = "SELECT max(id) AS total
                FROM product_items 
                WHERE product_items.type='{$type}'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->answer = $row['total'];
            return $this->answer;
        }
        return false;
    }

    function delete(){
        $this->modified = date('Y-m-d H:i:s');
        $query = "UPDATE product_items
                 SET
                    isDeleted   = 'Y',
                    modified    = :modified
                 WHERE
                    id          = :id";

        $stmt = $this->conn->prepare($query);

        $this->modified          = htmlspecialchars(strip_tags($this->modified));
        $this->productitemid     = htmlspecialchars(strip_tags($this->productitemid));

        $stmt->bindParam(':modified', $this->modified);
        $stmt->bindParam(':id',       $this->productitemid);

        if($stmt->execute()){
            return true;
        }
        return false;
    }
}