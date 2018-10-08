<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/user.php";

$database = new Database();
$db = $database->getConnection();

$user = new User($db);


// set page title
$page_title="View Profile";

if(isset($_GET["u"])){
    $username = strtolower($_GET['u']);

    /* GET USER ID FROM USRENAME*/
    $userid = $user->getUserID($username);

    if(!isset($userid)){
        header("Location: 404.php");
    }
}


// include login checker
$require_login=true;
$page_ribbon = "false";
$profiles = true;

include_once "login_check.php";
include 'template/header.php';
?>
<div class="row" style="margin: 0">
    <div class="col-md-12" style="background-color: #171d5b">
        <div class="container">
        <?php
        echo "<h2>{$username}</h2>";
        ?>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="thumbnail panel panel-default">
                <ul class="list-group">
                    <li class="list-group-item">Contact Number</li>
                    <li class="list-group-item">Email</li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
        test
        </div>
    </div>
</div>



<?php include 'template/footer.php' ?>