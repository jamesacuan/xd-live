<?php
include 'dbcon.php';

if(isset($_GET['id'])){
    $id=$_GET['id'];
}


$table = "users"; 
$filename = "export"; 
$sql1 = "CREATE VIEW test as
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
                    WHERE p1.`purchase_orderid` = ". $id ."
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
                    p2.`purchase_orderid` = ". $id ."
                   AND p2.isDeleted <> 'Y'";

$sql2="CREATE VIEW testbody AS SELECT productname as PRODUCTS,
(CASE
 	WHEN color = 'Blue' THEN quantity
 	ELSE ''
END) AS BLUE,
(CASE
 	WHEN color = 'Gray' THEN quantity
 	ELSE ''
END) AS GRAY,
(CASE
 	WHEN color = 'White' THEN quantity
 	ELSE ''
END) AS WHITE
FROM test;";

$sql3= "SELECT * from testbody
UNION
SELECT 'TOTAL', SUM(BLUE), SUM(GRAY), SUM(WHITE) from testbody";

$sql4="DROP VIEW test;
DROP VIEW testbody;";

mysqli_query($connect, $sql1);
mysqli_query($connect, $sql2);

$result = mysqli_query($connect,$sql3) or die("Couldn't execute query:<br>" . mysqli_error(). "<br>" . mysqli_errno()); 
$file_ending = "xls";
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=$filename.xls");
header("Pragma: no-cache"); 
header("Expires: 0");
$sep = "\t"; 
$names = mysqli_fetch_fields($result) ;
foreach($names as $name){
    print ($name->name . $sep);
    }
print("\n");
while($row = mysqli_fetch_row($result)) {
    $schema_insert = "";
    for($j=0; $j<mysqli_num_fields($result);$j++) {
        if(!isset($row[$j]))
            $schema_insert .= "NULL".$sep;
        elseif ($row[$j] != "")
            $schema_insert .= "$row[$j]".$sep;
        else
            $schema_insert .= "".$sep;
    }
    $schema_insert = str_replace($sep."$", "", $schema_insert);
    $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
    $schema_insert .= "\t";
    print(trim($schema_insert));
    print "\n";
}

mysqli_query($connect, $sql4);
?>