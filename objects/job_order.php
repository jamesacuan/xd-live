<?php
class JobOrder{

    private $conn;
    private $table1_name = "job_order";
    private $table2_name = "job_order_details";
    private $table3_name = "job_order_feedback";
    private $table4_name = "job_order_status";

    public $joborderid;
    public $userid;
    public $type;
    public $code, $note, $image_url, $expectedJO;
    public $expectedJOD, $created, $modified, $isDeleted, $joborderdetailsid;
    public $jocount, $tycount;
    public $nickname;
    public $tag;

    public function __construct($db){
        $this->conn = $db;
    }

    function addJOItem(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');

        $query2 = "INSERT INTO " . $this->table2_name . "
                SET 
                    type = :type,
                    code = :code,
                    note = :note,
                    image_url = :image_url,
                    created = :created,
                    modified = :modified,
                    job_orderid = :job_orderid";

        $stmt2 = $this->conn->prepare($query2);

        $this->type       = htmlspecialchars(strip_tags($this->type));
        $this->code       = htmlspecialchars(strip_tags($this->code));
        $this->note       = htmlspecialchars(strip_tags($this->note));  
        $this->image_url  = htmlspecialchars(strip_tags($this->image_url));
        $this->expectedJO = htmlspecialchars(strip_tags($this->expectedJO));     
        $this->created    = htmlspecialchars(strip_tags($this->created));
        $this->modified   = htmlspecialchars(strip_tags($this->modified));

        $stmt2->bindParam(':type', $this->type);        
        $stmt2->bindParam(':code', $this->code);
        $stmt2->bindParam(':note', $this->note);
        $stmt2->bindParam(':image_url',  $this->image_url);
        $stmt2->bindParam(':created',    $this->created);
        $stmt2->bindParam(':modified',   $this->modified);
        $stmt2->bindParam(':job_orderid', $this->expectedJO);

        if($stmt2->execute()){
            return true;
        }
        else{
            $this->showError($stmt2);
            return false;
        }
    }

    function createJO(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');

        $query1 = "INSERT INTO " . $this->table1_name . "
                SET 
                    userid = :userid,
                    created = :created,
                    modified = :modified";

        $stmt1 = $this->conn->prepare($query1);

        $this->userid     = htmlspecialchars(strip_tags($this->userid));    
        $this->created    = htmlspecialchars(strip_tags($this->created));
        $this->modified   = htmlspecialchars(strip_tags($this->modified));

        $stmt1->bindParam(':userid', $this->userid);        
        $stmt1->bindParam(':created', $this->created);
        $stmt1->bindParam(':modified', $this->modified);

        if($stmt1->execute()){
            return true;
        }else{
            $this->showError($stmt1);
            return false;
        }  
    }

    function addJOItemFeedback(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');
        $query = "INSERT INTO " . $this->table3_name . "
                SET 
                    image_url = :image_url,
                    note      = :note,
                    tag       = :tag,
                    status    = :status,
                    created   = :created,
                    modified  = :modified,
                    userid    = :userid,
                    job_order_detailsid = :job_order_detailsid";

        $stmt = $this->conn->prepare($query);

        $this->image_url  = htmlspecialchars(strip_tags($this->image_url));
        $this->note       = htmlspecialchars(strip_tags($this->note));
        $this->tag        = htmlspecialchars(strip_tags($this->tag));
        $this->status     = htmlspecialchars(strip_tags($this->status));
        $this->created    = htmlspecialchars(strip_tags($this->created));  
        $this->modified   = htmlspecialchars(strip_tags($this->modified));
        $this->userid     = htmlspecialchars(strip_tags($this->userid));
        $this->expectedJOD = htmlspecialchars(strip_tags($this->expectedJOD));     

        $stmt->bindParam(':image_url', $this->image_url);        
        $stmt->bindParam(':note',      $this->note);
        $stmt->bindParam(':tag',       $this->tag);
        $stmt->bindParam(':status',    $this->status);
        $stmt->bindParam(':created',   $this->created);
        $stmt->bindParam(':modified',  $this->modified);
        $stmt->bindParam(':userid',    $this->userid);
        $stmt->bindParam(':job_order_detailsid', $this->expectedJOD);

        if($stmt->execute()){
            return true;
        }
        else{           
            $this->showError($stmt);
            return false;
        }
    }

    function setTag(){
        $query = "UPDATE " . $this->table2_name . "
                 SET
                    tag   = :tag
                 WHERE
                    id    = :id";

        $stmt = $this->conn->prepare($query);

        $this->tag         = htmlspecialchars(strip_tags($this->tag));
        $this->expectedJOD = htmlspecialchars(strip_tags($this->expectedJOD));

        $stmt->bindParam(':tag',  $this->tag);
        $stmt->bindParam(':id',   $this->expectedJOD);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function approve(){
        $this->modified = date('Y-m-d H:i:s');

        $query = "UPDATE " . $this->table2_name . "
                 SET
                    status   = :status,
                    modified = :modified
                 WHERE
                    id       = :id";

        $stmt = $this->conn->prepare($query);

        $this->status            = htmlspecialchars(strip_tags($this->status));
        $this->modified          = htmlspecialchars(strip_tags($this->modified));
        $this->joborderdetailsid = htmlspecialchars(strip_tags($this->joborderdetailsid));

        $stmt->bindParam(':status',   $this->status);
        $stmt->bindParam(':modified', $this->modified);
        $stmt->bindParam(':id',       $this->joborderdetailsid);

        if($stmt->execute()){
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

   function getJobOrderCount(){
        $query = "SELECT max(id) AS total FROM job_order";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->jocount = $row['total'];
            return $this->jocount;
        }
        return false;
    }


    function getActiveJobOrderCount($id){
        $query = "SELECT count(*) as total
        FROM `job_order` a
        JOIN users c ON a.userid = c.userid
        JOIN job_order_details b ON a.id = b.job_orderid
        JOIN job_order_status s1 ON s1.job_order_code = b.code
        WHERE b.type LIKE '%%'
        AND b.isDeleted <> 'Y'
        AND s1.status <> 'Published'
        AND s1.status <> 'Denied'
        AND c.userid = {$id}
        AND s1.created = (
        SELECT MAX( s2.created )
        FROM job_order_status s2
        WHERE s2.job_order_code = s1.job_order_code )
        ORDER BY b.created ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        }
        return false;
    }

    function getJobOrderDetailsCount(){
        $query = "SELECT count(*) AS total FROM job_order_details";

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

    function getJobOrderFeedbackCount($JODID){
        $query = "SELECT count(*) AS total
                FROM " . $this->table3_name . "
                WHERE `job_order_detailsid` = {$JODID}";

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
    /*function getJobOrderDetailsCount($JOID){
        $query = "SELECT count(*) AS total FROM job_order_details where job_orderid={$JOID}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->answer = $row['total'];
            return $this->answer;
        }
        return false;
    }*/
    function getJobOrderDetailsCode($JODID){
        $query = "SELECT code FROM job_order_details WHERE 
        job_order_details.id={$JODID}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();

        if($num>0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->answer = $row['code'];
        return $this->answer;
        }
        return false;
    }

    function getJobOrderUser($JOID){
        $query = "SELECT nickname, job_order.created FROM job_order 
                    JOIN users ON job_order.userid = users.userid
                    where job_order.id={$JOID}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->answer = $row['nickname'];
            return $this->answer;
        }
        return false;
    }


    function getTypeCount($type_){
        $query = "SELECT count(*) AS total FROM `job_order_details` WHERE `type`='{$type_}'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->tycount = $row['total'];
            return $this->tycount;
        }
        return false;
    }

    function readJO($JOID){
        $query = "SELECT job_order.id as JOID,
                        users.nickname,
                        job_order.created
                        FROM `job_order`
                        JOIN users on job_order.userid = users.userid
                        WHERE job_order.id = $JOID";
                        /*ORDER BY job_order_details.modified DESC
                        LIMIT {$from_record_num}, {$records_per_page}";
                        */
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->nickname  = $row['nickname'];
        $this->created   = $row['created'];
        $stmt->execute();
        return $stmt;
    }

    function readJOD($JOID){
        $query = "SELECT job_order.id as JOID,
                job_order_details.id as JODID,
                job_order_details.type,
                job_order_details.code,
                users.username,
                job_order_details.note,
                job_order_details.image_url,
                job_order_details.modified,
                s1.status
                FROM `job_order`
                JOIN users on job_order.userid = users.userid
                JOIN job_order_details on job_order.id = job_order_details.job_orderid
                JOIN job_order_status s1 on s1.job_order_code = job_order_details.code
                WHERE job_order.id = $JOID
                AND job_order_details.isDeleted <> 'Y'
                AND s1.created = (SELECT MAX(s2.created) FROM job_order_status s2
                          WHERE s2.job_order_code = s1.job_order_code)";

                        /*ORDER BY job_order_details.modified DESC
                        LIMIT {$from_record_num}, {$records_per_page}";
                        */
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readJODFeedback($val){
        $query = "SELECT job_order_feedback.`image_url`, job_order_feedback.`note`, job_order_feedback.`tag`, job_order_feedback.`created`, job_order_feedback.`modified`, users.username, users.role, `job_order_detailsid`, job_order_details.code
        FROM `job_order_feedback`
        JOIN users on job_order_feedback.userid = users.userid
        JOIN job_order_details on job_order_feedback.job_order_detailsid = job_order_details.id
        WHERE job_order_details.code LIKE '%$val%'
        ORDER BY job_order_feedback.created ASC";

        /*$stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->code);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->image_url   = $row['image_url'];
        $this->note        = $row['note'];
        $this->tag         = $row['tag'];
        //$this->type        = $row['type'];
        $this->username    = $row['username'];
        //$this->nickname    = $row['nickname'];
        //$this->modified    = $row['modified'];
       // $this->status      = $row['status'];
       */

      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    function getFeedbackReviewer($val){
        $query = "SELECT DISTINCT users.nickname, users.username
        FROM `job_order_feedback`
        JOIN users on job_order_feedback.userid = users.userid
        JOIN job_order_details on job_order_feedback.job_order_detailsid = job_order_details.id
        WHERE job_order_details.code LIKE '%$val%'
        AND users.role LIKE 'hans'
        ORDER BY job_order_feedback.created ASC";

        /*$stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->code);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->image_url   = $row['image_url'];
        $this->note        = $row['note'];
        $this->tag         = $row['tag'];
        //$this->type        = $row['type'];
        $this->username    = $row['username'];
        //$this->nickname    = $row['nickname'];
        //$this->modified    = $row['modified'];
       // $this->status      = $row['status'];
       */

      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    function readJODwithUserandStatus($userid, $status){
        /*$query = "SELECT job_order.id as JOID,
        job_order_details.id as JODID,
        job_order_details.type,
        job_order_details.code,
        users.username,
        users.nickname,
        job_order_details.note,
        job_order_details.image_url,
        job_order_details.modified,
        job_order_status.status
        FROM `job_order`
        JOIN users on job_order.userid = users.userid
        JOIN job_order_details on job_order.id = job_order_details.job_orderid
        JOIN job_order_status on job_order_status.job_order_code = job_order_details.code
        WHERE users.userid = $userid
        ORDER BY job_order_details.modified DESC";
        */
        $query = "SELECT job_order.id as ID,
        CONCAT('JO') as XTABLE,
        users.nickname,
        job_order.created as created
        FROM job_order
        JOIN users ON users.userid = job_order.userid
        WHERE users.userid = $userid
        AND job_order.isDeleted <> 'Y'
 UNION
 SELECT purchase_order.id as ID,
        CONCAT('PO') as XTABLE,
       users.nickname,
       purchase_order.created as created
       FROM purchase_order
       JOIN users ON users.userid = purchase_order.userid
       WHERE users.userid = $userid
       AND purchase_order.isDeleted <> 'Y'
       ORDER BY created DESC";


        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    function readJODActivityStream($from_record_num, $records_per_page){
        /*$query = "SELECT job_order.id as JOID,
        job_order_details.id as JODID,
        job_order_details.type,
        job_order_details.code,
        users.username,
        users.nickname,
        job_order_details.note,
        job_order_details.image_url,
        job_order_details.created,
        job_order_details.modified,
        s1.status,
        job_order_details.isDeleted
        FROM `job_order`
        JOIN users on job_order.userid = users.userid
        JOIN job_order_details on job_order.id = job_order_details.job_orderid
        JOIN job_order_status s1 on s1.job_order_code = job_order_details.code
        WHERE job_order_details.isDeleted <> 'Y'
        AND s1.created = (SELECT MAX(s2.created) FROM job_order_status s2
                          WHERE s2.job_order_code = s1.job_order_code)
        ORDER BY job_order_details.modified DESC";*/

        /*$query = "SELECT job_order.id as ID,
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
       LIMIT 20*/
       
       
       $query = "SELECT job_order.id as ID,
       CONCAT('JO') as XTABLE,
       users.nickname,
       users.username,
       job_order.created as created
       FROM job_order
       JOIN users ON users.userid = job_order.userid
       WHERE job_order.isDeleted <> 'Y'


        UNION
SELECT purchase_order.id as ID,
      CONCAT('PO') as XTABLE,
      users.nickname,
      users.username,
      purchase_order.created as created
      FROM purchase_order
      JOIN users ON users.userid = purchase_order.userid
      WHERE purchase_order.isDeleted <> 'Y'
UNION

SELECT id, CONCAT('PRD') as XTABLE, nickname, username, created
       
	FROM (SELECT
        product_items.id,
        product_items.name,
        product_items.image_url,
        product_items.created,
        product_items.jodid,
        users.nickname,
	users.username
        FROM `product_items`
        JOIN job_order_details ON job_order_details.id = product_items.jodid
        JOIN job_order ON job_order.id = job_order_details.job_orderid
        JOIN users ON users.userid = job_order.userid
        WHERE product_items.isDeleted <> 'Y'

        UNION ALL

        SELECT
        product_items.id,
        product_items.name,
        product_items.image_url,
        product_items.created,
        'manual',
          'manual',
        'manual'
        FROM `product_items`
        WHERE product_items.isDeleted <> 'Y' AND
            product_items.jodid = 0)TEST
        ORDER BY created DESC
        limit {$from_record_num}, {$records_per_page}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    function read($typeval){
        /*$query = "SELECT * FROM
        (SELECT a.id AS JOID, b.id AS JODID, b.type, b.code, c.username, b.tag, b.note, b.image_url, b.modified, b.created, s1.status
        FROM `job_order` a
        JOIN users c ON a.userid = c.userid
        JOIN job_order_details b ON a.id = b.job_orderid
        JOIN job_order_status s1 ON s1.job_order_code = b.code
        WHERE b.type LIKE '%{$typeval}%'
        AND b.isDeleted <> 'Y'
        AND s1.status <> 'Published'
        AND s1.status <> 'Denied'
        AND s1.created = (
        SELECT MAX( s2.created )
        FROM job_order_status s2
        WHERE s2.job_order_code = s1.job_order_code )
        ORDER BY b.created ASC 
        )DUMMY_ALIAS1
        
        UNION
        
        SELECT * FROM (
        SELECT e.id AS JOID, f.id AS JODID, f.type, f.code, g.username, f.tag, f.note, f.image_url, f.modified, f.created, t1.status
        FROM `job_order` e
        JOIN users g ON e.userid = g.userid
        JOIN job_order_details f ON e.id = f.job_orderid
        JOIN job_order_status t1 ON t1.job_order_code = f.code
        WHERE f.type LIKE '%{$typeval}%'
        AND f.isDeleted <> 'Y'
        AND t1.status = 'Published'
        AND t1.created = (
        SELECT MAX( t2.created )
        FROM job_order_status t2
        WHERE t2.job_order_code = t1.job_order_code )
        ORDER BY f.created ASC
        )DUMMY_ALIAS2";
        */
        $query = "SELECT a.id AS JOID, b.id AS JODID, b.type, b.code, c.username, b.tag, b.note, b.image_url, b.modified, b.created, s1.status
        FROM `job_order` a
        JOIN users c ON a.userid = c.userid
        JOIN job_order_details b ON a.id = b.job_orderid
        JOIN job_order_status s1 ON s1.job_order_code = b.code
        WHERE b.type LIKE '%{$typeval}%'
        AND b.isDeleted <> 'Y'
        /*AND s1.status <> 'Published'
        */AND s1.status <> 'Denied'
        AND s1.created = (
        SELECT MAX( s2.created )
        FROM job_order_status s2
        WHERE s2.job_order_code = s1.job_order_code )
        ORDER BY b.created ASC ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function getJOItem(){
        $query = "SELECT job_order.id,
        job_order_details.type,
        job_order_details.code,
        job_order_details.id as 'jodid',
        users.username,
        users.nickname,
        job_order_details.note,
        job_order_details.image_url,
        job_order_details.modified,
        s1.status,
        job_order.userid,
        job_order_details.isDeleted
        FROM `job_order`
        JOIN users on job_order.userid = users.userid
        JOIN job_order_details on job_order.id = job_order_details.job_orderid
        JOIN job_order_status s1 on s1.job_order_code = job_order_details.code
        WHERE job_order_details.code LIKE ?
        	AND s1.created = (SELECT MAX(s2.created)
                              FROM job_order_status s2
                              WHERE s2.job_order_code = s1.job_order_code)";
        /*ORDER BY job_order_details.modified DESC*/
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->code);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->joborderid  = $row['id'];
        $this->type        = $row['type'];
        $this->username    = $row['username'];
        $this->nickname    = $row['nickname'];
        $this->note        = $row['note'];
        $this->userid      = $row['userid'];
        $this->joborderdetailsid = $row['jodid'];
        $this->image_url   = $row['image_url'];
        $this->modified    = $row['modified'];
        $this->status      = $row['status'];
        $this->isDeleted      = $row['isDeleted'];

        //return $stmt;
    }
    function setStatus(){
        if(!empty($this->status)){
            $this->created  = date('Y-m-d H:i:s');
            $query = "INSERT INTO `job_order_status`
                SET
                    `status` = :status, 
                    `userid` = :userid, 
                    `job_order_code` = :joborderdetailscode, 
                    `created` = :created";

            $stmt = $this->conn->prepare($query);

            $this->status   = htmlspecialchars(strip_tags($this->status));
            $this->userid   = htmlspecialchars(strip_tags($this->userid)); //updated by
            $this->code     = htmlspecialchars(strip_tags($this->code));
            $this->created  = htmlspecialchars(strip_tags($this->created));

            $stmt->bindParam(':status',  $this->status);
            $stmt->bindParam(':userid',  $this->userid);
            $stmt->bindParam(':joborderdetailscode',  $this->code);
            $stmt->bindParam(':created', $this->created);

            if($stmt->execute()){
                return true;
            }
                return false;
        }
        else
            return true;
    }
}
?>