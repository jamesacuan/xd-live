<?php
//fetch.php
$connect = mysqli_connect("localhost", "root", "", "xd");
$output = '';
$query = "SELECT * FROM `product_color` ORDER BY `name` ASC";

$result = mysqli_query($connect, $query);
while($row = mysqli_fetch_array($result)){
 $output .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
}
echo $output;
?>
