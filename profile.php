<?php
include_once "config/core.php";
include_once "config/database.php";

$database = new Database();
$db = $database->getConnection();

// set page title
$page_title="View Profile";

if(isset($_GET($))){

    if(strtolower($_GET['type'])=='hh') $type="HH";
        elseif(strtolower($_GET['type'])=='th') $type="TH";
        else $type="";
}


// include login checker
$require_login=true;
include_once "login_check.php";
include 'template/header.php';
?>

<?php
echo "<h2>Title</h2>";
?>


<?php include 'template/footer.php' ?>