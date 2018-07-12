<?php
class User{
 
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $type;
    public $created;
    public $modified;
    public $isDeleted;
    public $fromuser, $touser, $code, $content, $url; //notifications
 
    public function __construct($db){
        $this->conn = $db;
    }

    function addUser(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');
        $query1 = "INSERT INTO users
                SET 
                        nickname = :nickname,
                        username = :username,
                        password = :password,
                        role     = :role,
                        isAdmin  = :isAdmin,
                        created  = :created,
                        modified = :modified";

        $stmt1 = $this->conn->prepare($query1);

        $this->nickname   = htmlspecialchars(strip_tags($this->nickname));
        $this->username   = htmlspecialchars(strip_tags($this->username));
        $this->password   = htmlspecialchars(strip_tags($this->password));
        $this->role       = htmlspecialchars(strip_tags($this->role));  
        $this->created    = htmlspecialchars(strip_tags($this->created));
        $this->modified   = htmlspecialchars(strip_tags($this->modified));

        if($this->role == "admin" || $this->role == "hans") $isAdmin = 'Y';
        else $isAdmin = '';

        $stmt1->bindParam(':nickname', $this->nickname);    
        $stmt1->bindParam(':username', $this->username);
        $stmt1->bindParam(':password', $this->password);
        $stmt1->bindParam(':role',     $this->role); 
        $stmt1->bindParam(':isAdmin',  $isAdmin);
        $stmt1->bindParam(':created',  $this->created);
        $stmt1->bindParam(':modified', $this->modified);

        if($stmt1->execute())
                return true;
        else
            return false;
    }

    function updateUser($uname){
        $this->modified = date('Y-m-d H:i:s');
        $query1 = "UPDATE users
            SET 
                `nickname` = :nickname,
                `username` = :username,
                `role`     = :role,
                `isAdmin`  = :isAdmin,
                `modified` = :modified,
            WHERE
                `username` = '". $uname . "'";

        $stmt1 = $this->conn->prepare($query1);

        $this->nickname   = htmlspecialchars(strip_tags($this->nickname));
        $this->username   = htmlspecialchars(strip_tags($this->username));
        $this->role       = htmlspecialchars(strip_tags($this->role));  
        $this->modified   = htmlspecialchars(strip_tags($this->modified));

        if($this->role == "admin") $isAdmin = 'Y';
        else $isAdmin = '';

        $stmt1->bindParam(':nickname', $this->nickname);    
        $stmt1->bindParam(':username', $this->username);
        $stmt1->bindParam(':role',     $this->role); 
        $stmt1->bindParam(':isAdmin',  $isAdmin);
        $stmt1->bindParam(':modified', $this->modified);

        if($stmt1->execute())
                return true;
        else
            return false;
    }

    function userExists($val){
        $query = "SELECT `userid`, `username`, `nickname`, `password`, `role`, isAdmin, created, modified 
                FROM " . $this->table_name . "
                WHERE username = '" . $val ."'
                LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->username=htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(1, $this->username);
        $stmt->execute();   
        $num = $stmt->rowCount();
    
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id       = $row['userid'];
            $this->nickname = $row['nickname'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->role     = $row['role'];
            $this->isAdmin  = $row['isAdmin'];

            return true;
        }
    
        return false;
    }

    function read(){
        $query = "SELECT * FROM `users`
                    WHERE isDeleted <> 'Y'
                    ORDER BY nickname ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function getUser($uid){
        $query = "SELECT job_order.id as JOID,
                        users.nickname,
                        job_order.created
                        FROM `job_order`
                        JOIN users on job_order.userid = users.userid
                        WHERE job_order.id = $uid";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->nickname  = $row['nickname'];
        $this->created   = $row['created'];
        $stmt->execute();
        return $stmt;
    }

    function setPassword($uid){
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

    function updatePassword(){
        $this->modified = date('Y-m-d');
        $query = "UPDATE `users`
            SET
                `password` = :password,
                `modified` = :modified
            WHERE
                `username` = :username";

        $stmt = $this->conn->prepare($query);

        $this->password  = htmlspecialchars(strip_tags($this->password));
        $this->modified  = htmlspecialchars(strip_tags($this->modified));
        $this->username  = htmlspecialchars(strip_tags($this->username));

        $stmt->bindParam(':password',  $this->password);
        $stmt->bindParam(':modified',  $this->modified);
        $stmt->bindParam(':username',  $this->username);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function deleteUser(){
        $this->modified = date('Y-m-d');
        $query = "UPDATE `users`
            SET
                `isDeleted` = :isDeleted,
                `modified` = :modified
            WHERE
                `username` = :username";

        $stmt = $this->conn->prepare($query);

        $this->isDeleted  = htmlspecialchars(strip_tags($this->isDeleted));
        $this->modified  = htmlspecialchars(strip_tags($this->modified));
        $this->username  = htmlspecialchars(strip_tags($this->username));

        $stmt->bindParam(':isDeleted',  $this->isDeleted);
        $stmt->bindParam(':modified',  $this->modified);
        $stmt->bindParam(':username',  $this->username);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function setNotification(){
        $this->created = date('Y-m-d');
        $query = "INSERT INTO `users_notify`
                SET `code`        = :code, 
                    `content`     = :content,
                    `from_userid` = :fromuserid,
                    `to_userid`   = :touserid,
                    `url`         = :urll,
                    `created`     = :created";
    
        $stmt = $this->conn->prepare($query);

        $this->fromuser   = htmlspecialchars(strip_tags($this->fromuser));
        $this->touser     = htmlspecialchars(strip_tags($this->touser));
        $this->code       = htmlspecialchars(strip_tags($this->code));
        $this->content    = htmlspecialchars(strip_tags($this->content));
        $this->url        = htmlspecialchars(strip_tags($this->url));
        $this->created    = htmlspecialchars(strip_tags($this->created));

        $stmt->bindParam(':fromuserid',  $this->fromuser);
        $stmt->bindParam(':touserid',    $this->touser);
        $stmt->bindParam(':code',        $this->code);
        $stmt->bindParam(':content',     $this->content);
        $stmt->bindParam(':urll',        $this->url);
        $stmt->bindParam(':created',     $this->created);

        if($stmt->execute()){
            return true;
        }
        return false;
    }
}