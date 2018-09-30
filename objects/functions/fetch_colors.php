<?php
include('dbcon.php');


$query = "SELECT * FROM `product_color` ORDER BY `name` ASC";

$result = mysqli_query($connect, $query);
while($row = mysqli_fetch_array($result)){
  if($_POST["type"]=="TH" && $row['name'] == "Black")
    $output .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
  else if($_POST["type"]=="HH" && $row['name'] == "White")
    $output .= '<option value="'.$row["id"].'" selected>'.$row["name"].'</option>';
  else if($_POST["type"]=="HH" && $row['name'] != "Black")
    $output .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
}
echo $output;
?>
