<?php
include('dbcon.php');

$query = "SELECT * FROM `product_items` WHERE `type`= '" . $_POST["type"] ."' AND ( visibility = " . $_POST["id"] ." OR visibility = 0 ) ORDER BY visibility DESC, name ASC";
//$query = "SELECT * FROM `product_items` WHERE `type`= '" . $_POST["type"] ."' ORDER BY name ASC";
$result = mysqli_query($connect, $query);
$opt1 = 0;
$opt2 = 0;
while($row = mysqli_fetch_array($result)){
    if($row['visibility'] != 0 && $opt1 != 1) {
        $output .= "<optgroup label='Personal'>";
        $opt1 = 1;
    }
    if($row['visibility'] == 0 && $opt2 != 1){
        if($opt1==1) $output .= "</optgroup>";
        $output .= "<optgroup label='Public'>";
        $opt2 = 1;
    }
    $output .= '<option/ value="'.$row["id"].'">'.$row["name"].'</option>';
}
$output .= "</optgroup>";

echo $output;
//echo "<option>test</option>";
?>