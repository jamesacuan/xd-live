<?php
//fetch.php
$connect = mysqli_connect("localhost", "root", "", "xd");
$output = '';
$query = "SELECT * FROM `product_items` WHERE `type`= '" . $_POST["type"] ."' ORDER BY name ASC";

$result = mysqli_query($connect, $query);
while($row = mysqli_fetch_array($result)){
 $output .= '<option value="'.$row["name"].'">'.$row["name"].'</option>';
}
echo $output;
?>
