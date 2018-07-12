<?php
include_once "config/core.php";
include_once "config/database.php";

if($_POST){
    echo $_POST['form'];
}

$page_ribbon="F";
$page_title = "playground";

include 'template/header.php';
?>

<div class="row">
<form style="border: 1px solid black" class="col-md-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" enctype="multipart/form-data"> 

<input type="submit" name="form" value="Submit" />
</form>

<form style="border: 1px solid navy" class="col-md-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" enctype="multipart/form-data"> 

<input type="submit" name="form" value="Publish" />
</form>



<?php echo password_hash("H4milton", PASSWORD_BCRYPT); ?>
</div>

<?php include 'template/header.php';
?>