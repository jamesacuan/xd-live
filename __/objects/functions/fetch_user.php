<?php
/*$connect = mysqli_connect("localhost", "root", "", "xd");
$output = '';

$query = "SELECT `userid`, `username`, `nickname`, `password`, `role`, isAdmin, created, modified 
                FROM users
                WHERE username = ?
                LIMIT 0,1";
*/
include('database_connection.php');

$query = "SELECT * FROM users 
        WHERE username = '".$_POST["username"]."'
";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

foreach($result as $row)
{
 //$user = explode(".", $row["image_name"]);
 //$output['image_name'] = $file_array[0];
 $output['displayname'] = $row["nickname"];
 $output['username'] = $row["username"];
 $output['role'] = $row["role"];

}

echo json_encode($output);

?>