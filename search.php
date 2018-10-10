<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/product.php";

$database = new Database();
$db = $database->getConnection();

$page_ribbon = "f";
$product = new Product($db);

$page_title= "Search results";


include_once "login_check.php";
include 'template/header.php'
?>


<?php
echo $_GET['q'];
?>

<?php include_once "template/footer.php" ?>