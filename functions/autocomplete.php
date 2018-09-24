<?php
include 'dbcon.php';
//header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");

$query = "SELECT `id`, `name` FROM `product_items` WHERE isDeleted <> 'Y'";

$result = mysqli_query($connect, $query);
$total      = $result->num_rows;
$output = "[";
$i = 0;
while($row = mysqli_fetch_array($result)){
    $output .= "{";
    $output .= "\"name\": \"" .  htmlentities($row["name"]) . "\",";
    $output .= "\"id\": \"" . $row["id"] . "\"";
    $output .= "}";
    if($i < $total-1){
        $output .= ",";
        $i += 1;
    }
}
$output .= "]";

echo $output;