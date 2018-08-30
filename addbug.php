<?php
include_once "config/core.php";
include_once "config/database.php";

$database = new Database();
$db = $database->getConnection();

$page_title = "New Issue";
$require_login = true;

include_once "login_check.php";
include 'template/header.php';
?>

<div class="row xd-heading">
    <div class="clearfix">
        <div class="page-header pull-left">
            <h1><?php echo isset($page_title) ? $page_title : "Index"; ?></h1>
        </div>
    </div>
</div>

<div class="row xd-content">

<?php
if($_POST){

}
?>

<div class="col-md-12">
    <div class="row">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="col-md-7" style="padding:30px" >
            <div class="form-group">
                <label class="control-label">Request Title</label>
                <input type="text" class="form-control" placeholder="summary" name="title" />
            </div>
            <div class="form-group">
                <label class="control-label">Request Description</label>
                <textarea class="form-control" placeholder="describe your issue here" name="description"></textarea>
            </div>
        </div>
        <div class="col-md-5" style="padding:30px 30px 30px 0">
            <div class="form-group">
                <label class="control-label">URL</label>
                <input type="text" class="form-control" placeholder="url" name="url" />
            </div>
            <div class="form-group">
                <label class="control-label">Browser</label>
                <input type="text" class="form-control" placeholder="url" name="browser" />
            </div>
            <div class="form-group">
                <label class="control-label">Platform</label>
                <input type="text" class="form-control" placeholder="url" name="platform" />
            </div>
        </div>
        </form> 
    </div>
</div>
</div>