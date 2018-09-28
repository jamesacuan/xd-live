<?php
class Settings{

    private $conn;
    private $supername = "admin";
    private $superpass = "";
    private $suprole   = "superadmin";
    private $isAdmin   = "Y";

    public $created, $modified;

    public function __construct($db){
        $this->conn = $db;
    }

    function truncate(){
        $this->created  = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');
        $this->password  = "$2y$10$PGyDtm3efQwftrblni9Gku6MvX0Z2k/F9eK5HXsm5BLwpOre9nBvS"; //H4mil
        
        //$tmppass = password_hash($this->$superpass, PASSWORD_BCRYPT);
        /*
TRUNCATE job_order;
TRUNCATE job_order_details;
TRUNCATE job_order_feedback;
TRUNCATE job_order_status;
TRUNCATE product;
TRUNCATE product_items;
TRUNCATE product_color;
TRUNCATE purchase_order;
TRUNCATE purchase_order_details;
TRUNCATE users;
*/

        $tables = array("job_order_feedback",
                        "job_order_details",
                        "job_order",
                        "product",
                        "product_items",
                        "purchase_order",
                        "purchase_order_details",
                        "users",
                        "job_order_status",
                        "purchase_order_status");
                        
        $max = sizeof($tables);
        for($i=0; $i<$max; $i++){
            $stmt = $this->conn->prepare("TRUNCATE " . $tables[$i]);
            $stmt->execute(); 
        }

        $query = "INSERT INTO `users`
            SET 
                `nickname` = :nickname, 
                `username` = :username,
                `password` = :password,
                `role`     = :role, 
                `isAdmin`  = :isAdmin,
                `created`  = :created, 
                `modified` = :modified";

        $stmt2 = $this->conn->prepare($query);
       
        $stmt2->bindParam(':nickname', $this->supername);
        $stmt2->bindParam(':username', $this->supername);
        $stmt2->bindParam(':password',  $this->superpass);
        $stmt2->bindParam(':role',     $this->suprole);
        $stmt2->bindParam(':isAdmin',  $this->isAdmin);
        $stmt2->bindParam(':created',  $this->created);
        $stmt2->bindParam(':modified', $this->modified);
        
        if($stmt2->execute()){
            return $stmt2;        
        }
        else
            echo "h1";   
    }

    function getColor($value){
        $colors = array("1A237E", "880E4F", "0D47A1", "006064", "1B5E20", "263238",
                        "4527A0", "4A148C", "00695C", "2E7D32", "311B92", "AD1457");
        $ord = ord(strtoupper($value)) - ord('A') + 1;
        if($ord>20) $ord = $ord-20;
        else if($ord>10) $ord = $ord-10;
        
        return $colors[$ord];
    }

    function resize($image_type, $file_tmp, $filename) {

        if( $image_type == IMAGETYPE_JPEG ) {   
            $image_resource_id = imagecreatefromjpeg($file_tmp);  
            $target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1]);
            imagejpeg($target_layer, "images/" . $filename . "_sm.jpg");
        }
        elseif( $image_type == IMAGETYPE_GIF )  {  
            $image_resource_id = imagecreatefromgif($file_tmp);
            $target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1]);
            imagegif($target_layer, "images/" . $filename . "_sm.gif");
        }
        elseif( $image_type == IMAGETYPE_PNG ) {
            $image_resource_id = imagecreatefrompng($file_tmp); 
            $target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1]);
            imagepng($target_layer, "images/" . $filename . "_sm.png");
        }

       
    }  

    function fn_resize($image_resource_id,$width,$height) {
        $target_width =200;
        $target_height =200;
        $target_layer=imagecreatetruecolor($target_width,$target_height);
        imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$target_width,$target_height, $width,$height);
        //echo "done";
        return $target_layer;
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

}