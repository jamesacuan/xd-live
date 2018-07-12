<?php
// core configuration
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/job_order.php";
include_once "objects/purchase_order.php";
include_once "objects/settings.php";

$database = new Database();
$db = $database->getConnection();

$job_order = new JobOrder($db);
$settings =  new Settings($db);
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

        <?php
        echo "<div class=\"panel-group\" id=\"accordion\" role=\"tablist\">";
        //if($_SESSION["admin"]=='Y')
            $stmt = $job_order->readJODActivityStream();
        //else
            //$stmt = $job_order->readJODwithUserandStatus($_SESSION['userid'], "For Approval");
        $num  = $stmt->rowCount();

        if($num>0){
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                if($XTABLE=="JO"){
                    echo "<div class=\"panel panel-info\" style=\"margin:30px 0\">";
                        echo "<div class=\"panel-heading clearfix\" role=\"tab\">";
                        //echo "<div class=\"xd-circle pull-left\" style=\"background-color: #" . $settings->getColor(substr($nickname, 0, 1)) . "\">" . substr($nickname, 0, 1) . "</div>";
                        //echo "<div class=\"pull-left\" style=\"margin-left:20px\">";
                        echo "<div class=\"pull-left\">";
                            echo "<a href=\"{$home_url}joborder.php?&id={$ID}\" >";
                            echo "<h4 style=\"margin: 2px 0\">Job Order #{$ID}</h4>";
                            echo "</a>";
                            echo "<span class=\"text-muted\">By {$nickname} | On " . date_format(date_create($created),"F d, Y") . " at " . date_format(date_create($created),"h:i a") . "</span>";
                        echo "</div></div>";
                        //echo "<div class=\"panel-body\">";
                        echo "<table class=\"table table-hover\">";
                    $stmt2 = $job_order->readJOD($ID);
                    $num2 = $stmt2->rowCount();
                    $i = 0;
                    $tempjod = $ID;
                    if($num2>0){
                        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
                            extract($row2);
                            if($i < 4){
                                echo "<tr>";
                                if($image_url=="") $image_url = "def.png";
                                echo "<td class=\"col-xs-1\" style=\"padding-left: 15px\"><a href=\"{$home_url}joborderitem.php?&code={$code}\"><img class=\"img-rounded\" src=\"{$home_url}images/{$image_url}\" width=\"40\" height=\"40\" /></a></td>";
                                echo "<td class=\"col-xs-9\"><a href=\"{$home_url}joborderitem.php?&code={$code}\">{$code}</a><br/>{$note}</td>";
                                echo "<td class=\"col-xs-2\">{$status}</td>";
                                echo "</tr>";
                            }
                            else{
                                echo "<tr>";
                                echo "<td colspan=\"3\"><a href=\"{$home_url}joborder.php?&id={$tempjod}\" >Show All...</a></td>";
                                echo "</tr>";
                                break;
                            }
                            $i++;
                        }
                    }
                    echo "</table>";
                    echo "</div>";
                }
                else if($XTABLE == "PO"){
                    echo "<div class=\"panel panel-success\" style=\"margin:30px 0\">";
                        echo "<div class=\"panel-heading clearfix\" role=\"tab\">";
                        //echo "<div class=\"xd-circle pull-left\" style=\"background-color: #" . $settings->getColor(substr($nickname, 0, 1)) . "\">" . substr($nickname, 0, 1) . "</div>";
                        //echo "<div class=\"pull-left\" style=\"margin-left:20px\">";
                        echo "<div class=\"pull-left\">";
                            echo "<a href=\"{$home_url}purchaseorder.php?&id={$ID}\">";
                            echo "<h4 style=\"margin: 2px 0\">Purchase Order #{$ID}</h4>";
                            echo "</a>";
                            echo "<span class=\"text-muted\">By {$nickname} | On " . date_format(date_create($created),"F d, Y") . " at " . date_format(date_create($created),"H:i a") . "</span>";
                        echo "</div></div>";
                        echo "<table class=\"table table-hover\">";
                    $stmt2 = $purchase_order->readPOItem($ID);
                    $num2 = $stmt2->rowCount();
                    $i = 0;
                    if($num2>0){
                        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
                            extract($row2);
                            if($i < 4){
                                //echo $JODID . " " . $code;
                                echo "<tr>";
                                if($image_url=="undefined" || $image_url=="none" || $image_url=="0" || $image_url=="") $image_url = "def.png";
                                echo "<td class=\"col-xs-1\" style=\"padding-left: 15px\"><img class=\"img-rounded\" src=\"{$home_url}images/{$image_url}\" width=\"40\" height=\"40\" /></td>";
                                echo "<td class=\"col-xs-3\">";
                                    if($product == "HH") echo "Helmet Holder";
                                    else if($product == "TH") echo "Ticket Holder";
                                echo "<br/><span class=\"text-muted\">{$color}</span>";
                                echo "</td>";
                                echo "<td class=\"col-xs-6\">";
                                    if($productname=="0" || $productname =="") echo $type;
                                else echo $productname;
                                echo "<br/><span class=\"text-muted\">{$note}</span>";
                                echo "</td>";
                                echo "<td class=\"col-xs-2\">";
                                echo "x{$quantity}</td>";
                                echo "</tr>";
                            }
                            else{
                                echo "<tr>";
                                echo "<td colspan=\"3\"><a href=\"{$home_url}purchaseorder.php?&id={$ID}\" >Show All...</a></td>";
                                echo "</tr>";
                                break;
                            }
                            $i++;
                        }
                    }
                    echo "</table>";
                    echo "</div>";
                }
            }
        }
        else echo "<div class='alert alert-info'>No recent activity.</div>";
        
        ?>
  
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
<?php
    //include 'template/content.php';
    include 'template/footer.php';
?>