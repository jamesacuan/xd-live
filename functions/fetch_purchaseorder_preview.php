<?php
include('dbcon.php');

$query1 = "SELECT purchase_order.id,
users.nickname,
users.username,
purchase_order.created,
s1.status
FROM `purchase_order`
JOIN users on users.userid = purchase_order.userid
JOIN purchase_order_status s1 on s1.purchase_orderid = purchase_order.id
WHERE purchase_order.id = '" . $_POST['code'] . "'
AND   s1.created = (SELECT MAX(s2.created) FROM purchase_order_status s2
                WHERE s2.purchase_orderid = s1.purchase_orderid)
LIMIT 1";

/*$stmt = $this->conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$this->username  = $row['username'];
$this->nickname  = $row['nickname'];
$this->created   = $row['created'];
$this->purchase_orderid = $row['id'];
$this->status   = $row['status'];

$stmt->execute();
return $stmt;";
*/


/* FOR SUMTOTAL */
$query3 = "SELECT sum(quantity) as sum from (
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
            WHERE p1.`purchase_orderid` = '". $_POST['code'] ."'
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
            p2.`purchase_orderid` = '". $_POST['code'] ."'
           AND p2.isDeleted <> 'Y'
    )SUM_PURCHASE_ORDER";

$result1 = mysqli_query($connect, $query1);
while($row = mysqli_fetch_array($result1)){
    $output .= "<div>";

    $output .= "<ul class=\"nav nav-tabs\">";
    $output .= "<li class=\"active\"><a data-toggle=\"tab\" href=\"#PO\">PO</a></li>";
    $output .= "<li><a data-toggle=\"tab\" href=\"#info\">Info</a></li>";
    $output .= "</ul>";
}


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
    WHERE p1.`purchase_orderid` = '" . $_POST['code'] . "'
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
    p2.`purchase_orderid` = '" . $_POST['code'] . "'";

$result2 = mysqli_query($connect, $query);

$output .= "<div class=\"tab-content\">";
$output .= "<div id=\"PO\" class=\"tab-pane fade in active\">";
$output .= "<table class=\"table table-bordered table-striped \">";
/*$output .= "<tr>";
$output .= "<td>Product</td>";
$output .= "<td>Color</td>";
$output .= "<td>Quantity</td>";
$output .= "</tr>";
*/

while($row = mysqli_fetch_array($result2)){
    $output .= "<tr>";
    $output .= "<td>" . $row["product"] . "</td>";
    if($row["color"] == "Blue") $color = "#0000ff";
    else if($row["color"] == "Black") $color = "#000000";
    else if($row["color"] == "Gray") $color = "#aaa";
    else if($row["color"] == "White") $color = "#fff";
    $output .= "<td><div style=\"width: 15px; height: 15px; display: block; border: 1px solid #333; background-color: " . $color . "\"></div></td>";
    $output .=  "<td>";
    if(strpos($row["productname"], "define") == true || $row["productname"]=='0') $output .=  "Plain";
    else $output .=  $row["productname"];
    $output .= "</td>";
    $output .= "<td>" . $row["quantity"] . "</td>";

    $output .= "</tr>";
}
    $output .= "<tfoot>";
$result3 = mysqli_query($connect, $query3);

while($row = mysqli_fetch_array($result3)){
    $output .= "<td colspan=\"3\">Total</td>";
    $output .= "<td>" . $row["sum"] . "</td>";
}
    $output .= "</tfoot>";
$output .= "</table>";


$output .= "</div>"; //end of info
$output .= "<div id=\"info\" class=\"tab-pane fade in\">";
$result1 = mysqli_query($connect, $query1);
while($row = mysqli_fetch_array($result1)){
    $output .= "<dt>Status:</dt><dd>" . $row["status"] . "</dd>";
    $output .= "<dt>By:</dt><dd>" . $row["nickname"] . "</dd>";
    $output .= "<dt>Created:</dt><dd>" . date_format(date_create($row["created"]),"F d, Y h:i:s A") . "</dd>";
}
$output .= "</div>"; //end of discussion
$output .= "</div>"; //end of parent div


echo $output;

?>