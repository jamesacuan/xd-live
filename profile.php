<?php
include_once "config/core.php";
include_once "config/database.php";

$database = new Database();
$db = $database->getConnection();

// set page title
$page_title="View Profile";

if(isset($_GET["u"])){
    $username = strtolower($_GET['u']);
}


// include login checker
$require_login=true;
$page_ribbon = "false";
include_once "login_check.php";
include 'template/header.php';
?>

<?php
echo "<h2>{$username}</h2>";
?>


<?php include 'template/footer.php' ?>