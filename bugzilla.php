<?php

include_once "config/core.php";
include_once "config/database.php";


$database = new Database();
$db = $database->getConnection();

$page_title= "Issue Tracker";

$require_login=true;

$today     = date("m/d/Y");
$yesterday = date("m/d/Y", strtotime($today . ' -1 days'));

include_once "login_check.php";
include 'template/header.php'
?>
<div class="row xd-heading">
    <div class="clearfix">
        <div class="page-header pull-left">
            <h1><?php echo isset($page_title) ? $page_title : "Index"; ?></h1>
        </div>
    </div>
</div>

<div class="row xd-content">
    <div class="col-md-12">

    <table id="purchaseorders" class="table table-hover table-striped">
    <thead>
        <tr>
            <th class="col-xs-1"><input type="checkbox" /></th>
            <th class="col-xs-1">ID</th>
            <th class="col-xs-2">By</th>
            <th class="col-xs-4">Summary</th>
            <th class="col-xs-2">Date</th>
            <th class="col-xs-1">Status</th>
            <th class="col-xs-1">Priority</th>
        </tr>
    </thead>
    </table>
    </div>
</div>
<?php
include 'template/footer.php'
?>
