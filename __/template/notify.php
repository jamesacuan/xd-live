
<?php
class Notify{
    public function __construct($db){
        $this->conn = $db;
    }

    public function getNotification($uid){
        $query = "SELECT COUNT(*) as TOTAL
                        FROM `users_notify`
                        WHERE `to_userid` = $uid
                        AND isRead <> 'Y'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->execute();
        return $row['TOTAL'];
    }
}
?>