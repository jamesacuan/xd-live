<?php

//$total_pages = ceil($total_rows / $records_per_page);
//$range = 2;


//$initial_num = $page - $range;

include('dbcon.php');

/*STREAM */
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
        limit " . $_POST['i'] . ", 10";

/*JOBORDER*/

function JO($connect, $home_url, $JOID, $NICKNAME, $CREATED){
    $output = "";
    $query1="SELECT job_order.id as JOID,
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
       $output .= "<div class=\"panel panel-info\" style=\"margin:30px 0\">";
            $output .= "<div class=\"panel-heading clearfix\" role=\"tab\">";
            $output .= "<div class=\"pull-left\">";
            $output .= "<h4 style=\"margin: 2px 0\">Job Order #{$JOID}</h4>";
            $output .= "<span class=\"text-muted\">By <a href=\"#\" data-toggle=\"popover\" title=\"Popover Header\" data-content=\"Some content inside the popover\">{$NICKNAME}</a> | On " . date_format(date_create($CREATED),"F d, Y") . " at " . date_format(date_create($CREATED),"h:i a") . "</span>";
            $output .= "</div>";
            $output .= "</div>";
        $output .= "<table class=\"table table-hover\">";
       $result = mysqli_query($connect, $query1);
       while($row = mysqli_fetch_array($result)){
            if($row["image_url"]=="") $image_url = "def.png";
            else $image_url = $row["image_url"];

            $code = $row["code"];
            $note = $row["note"];
            $status = $row["status"];

            $output .= "<tr>";
            $output .= "<td class=\"col-xs-1\" style=\"padding-left: 15px\"><a href=\"{$home_url}joborderitem.php?&code={$code}\"><img class=\"img-rounded\" src=\"{$home_url}images/{$image_url}\" width=\"40\" height=\"40\" /></a></td>";          
            $output .= "<td class=\"col-xs-9\"><a href=\"{$home_url}joborderitem.php?&code={$code}\">{$code}</a><br/>{$note}</td>";
            $output .= "<td class=\"col-xs-2\">{$status}</td>";
            $output .= "</tr>";
       }
       $output .= "</table>";
       $output .= "</div>";
       return $output;
}

function PO($connect, $home_url, $POID, $NICKNAME, $CREATED){
    $output = "";
    $query1="SELECT p1.`id`,
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
    LIMIT 3";

       $output .= "<div class=\"panel panel-success\" style=\"margin:30px 0\">";
            $output .= "<div class=\"panel-heading clearfix\" role=\"tab\">";
            $output .= "<div class=\"pull-left\">";
            $output .= "<h4 style=\"margin: 2px 0\">Purchase Order #{$POID}</h4>";
            $output .= "<span class=\"text-muted\">By {$NICKNAME} | On " . date_format(date_create($CREATED),"F d, Y") . " at " . date_format(date_create($CREATED),"h:i a") . "</span>";
            $output .= "</div>";
            $output .= "</div>";
        $output .= "<table class=\"table table-hover\">";

       $inc = 0;
       $result = mysqli_query($connect, $query1);
       while($row = mysqli_fetch_array($result)){
            if($inc < 3){
                if($row["image_url"]=="") $image_url = "def.png";
                else $image_url = $row["image_url"];

                $image_url = $row["image_url"];
                $color = $row["color"];
                $product = $row["product"];
                $note = $row["note"];
                $quantity = $row["quantity"];
                $productname = $row["productname"];

                if($productname=="0" || $productname =="") $productname = $row["type"];
                if($image_url=="undefined" || $image_url=="none" || $image_url=="0" || $image_url=="") $image_url = "def.png";
                if($product == "HH") $product = "Helmet Holder";
                else if($product == "TH") $product = "Ticket Holder";

                $output .= "<tr>";
                $output .= "<td class=\"col-xs-1\" style=\"padding-left: 15px\"><img class=\"img-rounded\" src=\"{$home_url}images/{$image_url}\" width=\"40\" height=\"40\" /></td>";          
                
                $output .= "<td class=\"col-xs-3\">{$product}";
                $output .=  "<br/><span class=\"text-muted\">{$color}</span>";
                $output .= "</td>";
                
                $output .="<td class=\"col-xs-6\">{$productname}";
                $output .= "<br/><span class=\"text-muted\">{$note}</span>";
                $output .= "</td>";

                $output .= "<td class=\"col-xs-2\">";
                $output .= "{$inc}x{$quantity}</td>";
                $output .= "</tr>";
            }
            else{
                echo "<tr>";
                echo "<td colspan=\"3\"><a href=\"{$home_url}purchaseorder.php?&id={$ID}\" >Show All...</a></td>";
                echo "</tr>";
                break;
            }
          
            $inc++;
       }
       $output .= "</table>";
       $output .= "</div>";
       return $output;
}

$result = mysqli_query($connect, $query);

while($row = mysqli_fetch_array($result)){
    if($row["XTABLE"]=="JO"){
        $output .= JO($connect, $home_url, $row["ID"], $row["nickname"], $row["created"]);
    }
    else if($row["XTABLE"]=="PO"){
        $output .= PO($connect, $home_url, $row["ID"], $row['nickname'], $row["created"]);
    }
    /*
    else if($row["XTABLE"]=="PRD"){
        $output .= "<div class=\"panel panel-warning\" style=\"margin:30px 0\">";
            $output .= "<div class=\"panel-heading clearfix\" role=\"tab\">";
                $output .= "<div class=\"pull-left\">";
                $output .= "<h4 style=\"margin: 2px 0\">PRODUCT #" . $row["ID"] . "</h4>";
                $output .= "</div>";
            $output .= "</div>";
        $output .= "</div>";
    }*/
}
echo $output;

?>