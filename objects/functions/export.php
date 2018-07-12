<?php
include 'dbcon.php';

$table = "users"; 
$filename = "export"; 
$sql = "SELECT * FROM
(SELECT a.id AS JOID, b.id AS JODID, b.type, b.code, c.username, b.tag, b.note, b.modified, b.created, s1.status
FROM `job_order` a
JOIN users c ON a.userid = c.userid
JOIN job_order_details b ON a.id = b.job_orderid
JOIN job_order_status s1 ON s1.job_order_code = b.code
WHERE b.type LIKE '%%'
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
SELECT e.id AS JOID, f.id AS JODID, f.type, f.code, g.username, f.tag, f.note, f.modified, f.created, t1.status
FROM `job_order` e
JOIN users g ON e.userid = g.userid
JOIN job_order_details f ON e.id = f.job_orderid
JOIN job_order_status t1 ON t1.job_order_code = f.code
WHERE f.type LIKE '%%'
AND f.isDeleted <> 'Y'
AND t1.status = 'Published'
AND t1.created = (
SELECT MAX( t2.created )
FROM job_order_status t2
WHERE t2.job_order_code = t1.job_order_code )
ORDER BY f.created ASC
)DUMMY_ALIAS2";
$result = mysqli_query($connect,$sql) or die("Couldn't execute query:<br>" . mysqli_error(). "<br>" . mysqli_errno()); 
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
?>