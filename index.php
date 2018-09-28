<?php
// core configuration
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/job_order.php";
include_once "objects/purchase_order.php";
include_once "objects/product.php";
include_once "objects/settings.php";

$database = new Database();
$db = $database->getConnection();

$job_order = new JobOrder($db);
$settings  =  new Settings($db);
$Product   =  new Product($db);
$purchase_order = new PurchaseOrder($db);

$page_title= "Dashboard";

$require_login=true;
$page_ribbon="F";

$today     = date("m/d/Y");
$yesterday = date("m/d/Y", strtotime($today . ' -1 days'));

if($_SESSION['role']=="superadmin" && isset($_GET['truncate'])){
    if(isset($_GET['truncate'])){
        $settings->truncate();
    }
    $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $current_url = explode('?', $current_url);
    header("Location: {$current_url[0]}");
}

include_once "login_check.php";
include 'template/header.php'
?>

<?php
/*
echo "<div class='col-md-12'>";
$action = isset($_GET['action']) ? $_GET['action'] : "";
if($action=='login_success'){
        echo "<h3>Hi, " . $_SESSION['nickname'] . ". Welcome back!</h3>";
}
echo "</div>";
*/
?>
<div>
<div class="container">

<div class="row">
    <div class="col-md-12 clearfix">
        <div class="pull-left">
        </div>

        <div class="pull-right btn-group">
        <?php
           /* if($_SESSION['role']=="user"){        
                echo "<button type=\"button\" onclick=\"location.href='addjoborder.php'\" class=\"btn btn-default\">+ Job Order</button>";
                echo "<button type=\"button\" onclick=\"location.href='addpurchaseorder.php'\" class=\"btn btn-default\">+ Purchase Order</button>";
            }*/
        ?>
        <?php
            if($_SESSION['role']=="superadmin"){
                echo "<a href=\"#\" class=\"btn btn-danger\" data-id=\"truncate\" data-toggle=\"modal\" data-target=\"#clear\">Truncate</a>";
            }
        ?>
        </div>
    </div>
</div>
<div class="row home-approval">
    <div class="col-md-3">
        <div class="thumbnail panel panel-default">
            <div class="caption">
                <h3>Welcome <?php echo $_SESSION["nickname"]?>!</h3>
                <!--<p><?php echo $_SESSION["role"]?></p>-->
            </div>
            <ul class="list-group">
                <li class="list-group-item"><a href="<?php echo $home_url . "joborders.php" ?>">Job Orders</a></li>
                <li class="list-group-item"><a href="<?php echo $home_url . "purchaseorders.php" ?>">Purchase Orders</a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-9">
    <h3>Activity</h3>
        <div class="panel panel-info">
            <div class="panel-body" style="padding: 0">
                <textarea class="form-control" style="width: 100%; height: 100%; border: 1px solid #ccc;resize: none;"></textarea>
            </div>
            <div class="panel-footer">
                <div class="pull-left">
                    <input type="button" class="btn btn-primary btn-sm" value="Submit" />
                </div>
                <div class="form-group pull-right" style="margin: 0">
                    <label>
                        <input type="checkbox"> Pin post.
                    </label>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <?php include 'functions/stream.php' ?>
  
  </div>
</div>

</div>
</div>

<div class="modal fade" id="clear" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Are you sure</h4>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-sm btn-default delmodal">Yes</a>
        <a href="#" class="btn btn-primary" data-dismiss="modal">No</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="image" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <img class="job-order-for-render" />"
      </div>
    </div>
  </div>
</div>

<div class='container hide' id='cont'>
     <a data-toggle="modal" data-target="#facebook"
        class="btn btn-default">
        Facebook 
        </a>
         <a onclick="twitter();"
        class="btn btn-default">
        Twitter 
        </a>
         <a class="btn btn-default"
        data-placement="bottom" data-toggle="popover" data-title="Login" data-container="body"
        type="button" data-html="true" href="#" id="login">
        Email 
        </a>
</div>

<script>
var i=0;
$(window).scroll(function() {
    if($(window).scrollTop() == $(document).height() - $(window).height()) {
        $.ajax({
            url:"functions/activity.php",
            method:"POST",
            data:{i:i},
            success:function(data){  
                i+=10;
                $('.home-approval .col-md-9').append(data);  
            }
        })
    }
});
</script>
<?php
    //include 'template/content.php';
    include 'template/footer.php';
?>