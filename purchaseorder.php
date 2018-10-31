<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/purchase_order.php";

$database       = new Database();
$db             = $database->getConnection();
$purchase_order = new PurchaseOrder($db);

$require_login =true;
include_once "functions/login_check.php";

$role          = $_SESSION['role'];
$poid = $_GET['id'];

$stmt = $purchase_order->readPOD($poid);
$num  = $stmt->rowCount();

if($num>0){
    $page_title    = "Purchase Order #" . $poid;
}
else{
    header("Location: {$home_url}404.php");
}

$jocount="";
$i = 1;

if(($role=="admin" || $role=="superadmin" || $role=="hans" || $_SESSION['admin']=="Y") && isset($_GET['status'])){
    if(isset($_GET['status'])){
        //echo $_GET['status'];
        //echo $_GET['id'];
        $purchase_order->status           = $_GET['status'];
        $purchase_order->purchase_orderid = $_GET['id'];
        $purchase_order->userid           = $_SESSION['userid'];

        $purchase_order->setStatus();

        $_SESSION['modal'] = 'This PO has been updated.';
        
        /*$job_order->userid = $_SESSION["userid"];
        $job_order->code   = $itemcode;
        $job_order->joborderdetailsid = $jodid;

        if($_GET['status'] == 'Deny')
            $job_order->status = "Denied";
        elseif($_GET['status'] == 'Approve')
            $job_order->status = "Approved";
        else
            $job_order->status = $_GET['status'];
        //$job_order->approve();
        $job_order->setStatus();
        */
        
        header("Location: {$home_url}purchaseorder.php?&id={$poid}");

    }
}

if($_POST){
    if(isset($_POST['xd-edit-qty'])){
        $purchase_order->userid = $_SESSION["userid"];

        $purchase_order->quantity = $_POST['xd-edit-qty'];
        $purchase_order->PODID    = $_POST['xd-pod-id'];
        $purchase_order->updateQty();
        
    }
    
    if(isset($_POST['xd-delete'])){
        $purchase_order->userid = $_SESSION["userid"];

         $purchase_order->PODID    = $_POST['xd-pod-id'];
         $purchase_order->POID     = $poid;
        $purchase_order->deletePOD();
        
    }

    /*if(isset($_GET['status'])){
        $job_order->userid = $_SESSION["userid"];
        $job_order->code   = $itemcode;
        $job_order->joborderdetailsid = $jodid;

        if($_GET['status'] == 'Deny')
            $job_order->status = "Denied";
        elseif($_GET['status'] == 'Approve')
            $job_order->status = "Approved";
        else
            $job_order->status = $_GET['status'];
        //$job_order->approve();
        $job_order->setStatus();
        header("Location: {$home_url}joborderitem.php?&code={$itemcode}");
    }*/
    //header("Location: {$home_url}purchaseorder.php?&id=" . $poid, true, 303);
}

include 'template/header.php';
?>

<div class="xd-snip">
    <ol class="breadcrumb">
        <li><a href="<?php echo $home_url ?>">Home</a></li>
        <li><a href="<?php echo $home_url . "purchaseorder.php?&amp;id=" . $poid?>" class="active">Purchase Order #<?php echo $poid?></a></li>
    </ol>
</div>

<div class="xd-content">
<?php $purchase_order->readPOD($poid); ?>
<div class="row">
    <div class="col-md-12">
        <div class="row" style="margin: 20px 0">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-xs-12"><h2><?php echo $page_title ?></h2></div>
                </div>
                <div class="row">
                    <div class="col-xs-3">Requested By:</div>
                    <div class="col-xs-9"><?php echo $purchase_order->nickname?></div>
                </div>
                <div class="row">
                    <div class="col-xs-3">Date added:</div>
                    <div class="col-xs-9"><?php echo date_format(date_create($purchase_order->created),"F d, Y h:i A"); ?></div>
                </div>
                <div class="row">
                <div class="col-xs-3">Status:</div>
                    <div class="col-xs-9">
                        <?php echo "<span class=\"label "; 
                        if($purchase_order->status == 'On-queue' || $purchase_order->status == 'New')
                        echo "label-default\">On-queue";
                        else if ($purchase_order->status == 'paid')
                        echo "label-success\">Done";
                        else if ($purchase_order->status == 'Delivered')
                        echo "label-primary\">Delivered";
                        else echo "label-primary\">On-going";
                        echo "</span>";
                        ?>
                    </div>
                </div>
            </div>
    
    <div class="col-md-3">
                <div class="row">
                    <div class="col-sm-12 clearfix">
                        <div class="pull-right btn-group xd-joitem-details-btngroup">
                            <?php if (($role=='hans') && ($purchase_order->status=='On-queue' || $purchase_order->status=='New')){ ?>
                            <?php //<a href="<?php echo "{$home_url}purchaseorder.php?&id={$id}&status=processing";" class="btn btn-primary">Accept Request</a>?>
                            <a href="#" class="btn btn-primary" disabled="disabled" data-toggle="tooltip" title="Please use the accept request button from Purchase Orders list">Accept Request</a>

                            <?php } ?>

                            <?php if (($role=='hans') && ($purchase_order->status=='processing')){ ?>
                            <a href="<?php echo "{$home_url}purchaseorder.php?&id={$poid}&status=delivered";?>" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Delivered</a>
                            <?php } ?>

                            <?php if (($role=='hans') && ($purchase_order->status=='On-queue' || $purchase_order->status=='New')){ //for now  ?>
                            <a href="#" class="btn btn-primary" disabled="disabled" data-toggle="tooltip" title="Please accept PO request first in Purchase Orders list before proceeding"><span class="glyphicon glyphicon-ok"></span> Paid</a>
                            <?php } ?>

                            <?php if (($role=='hans') && ($purchase_order->status=='processing' || $purchase_order->status=='delivered')){ //for now  ?>
                            <a href="<?php echo "{$home_url}purchaseorder.php?&id={$poid}&status=paid";?>" class="btn btn-primary" data-toggle="tooltip" title="This will permanently close the PO and considered as paid and delivered."><span class="glyphicon glyphicon-ok"></span> Paid</a>
                            <?php } ?>
                            <!--<div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                 <span class="caret"></span></button>
                                <ul class="dropdown-menu" role="menu">
                                <li><a href="<?php echo "{$home_url}functions/export.php?&id={$poid}";?>">Export...</a></li>
                                </ul>
                            </div>
                            -->
                        </div>
                    </div>
                </div>
            </div>
</div>
</div>
<?php 

if($purchase_order->username == $_SESSION['username'] || $_SESSION['role']=='hans'){ ?>

    <div class="row">
                    <div class="col-xs-12">
                    <?php
                    echo "<div class=\"md-stepper-horizontal xd-po-status";
                    if ($purchase_order->status == 'paid')
                    echo ' green ';
                    echo "\">";
                    
                    echo "<div class=\"md-step ";
                    if ($purchase_order->status != "On-queue" && $purchase_order->status != 'New') 
                    echo "active";                    
                    echo "\">";
                    ?>

                        <div class="md-step-circle">
                        <?php if ($purchase_order->status == "processing" || $purchase_order->status == "delivered" || $purchase_order->status == "paid") echo "<span class=\"glyphicon glyphicon-ok\"></span>";
                            else echo "<span>1</span>";?>
                        </div>
                        <div class="md-step-title">
                        <?php if ($purchase_order->status == "processing" || $purchase_order->status == "delivered" || $purchase_order->status == "paid") echo "Processing"; 
                            else echo "For Processing" ?>
                        </div>
                        <!--<div class="md-step-optional">Rendered Image</div>-->
                        <div class="md-step-bar-left"></div>
                        <div class="md-step-bar-right"></div>
                    </div>



                    <?php echo "<div class=\"md-step ";
                                if ($purchase_order->status == "processing" || $purchase_order->status == "delivered" || $purchase_order->status == "paid" || $purchase_order->status == "Published") echo "active\">";
                                else echo "inactive\">"; ?>
                        <div class="md-step-circle">
                            <?php if ($purchase_order->status == "delivered" || $purchase_order->status == "paid") echo "<span class=\"glyphicon glyphicon-ok\"></span>";
                                else echo "<span>2</span>"; ?>
                        </div>
                        <div class="md-step-title">
                        <?php if ($purchase_order->status == "delivered" || $purchase_order->status == "paid") echo "Delivered";
                            else echo "For Delivery"; ?>
                        </div>
                        <!--<div class="md-step-optional">Rendered Image</div>-->
                        <div class="md-step-bar-left"></div>
                        <div class="md-step-bar-right"></div>
                    </div>
                    <?php echo "<div class=\"md-step ";
                                if ($purchase_order->status == "delivered" || $purchase_order->status == "paid") echo "active\">";
                                else echo "inactive\">"; ?>

                        <div class="md-step-circle">
                            <?php if ($purchase_order->status == "paid") echo "<span class=\"glyphicon glyphicon-ok\"></span>";
                                else echo "<span>3</span>"; ?>
                        </div>
                        <div class="md-step-title">
                        <?php if ($purchase_order->status == "paid") echo "Paid";
                              else echo "For Payment"; ?>
                        </div>
                        <div class="md-step-bar-left"></div>
                        <div class="md-step-bar-right"></div>
                    </div>
                    
                    </div>
                </div>
</div>

<?php } ?>
<div>

  <!-- Nav tabs -->
  <ul class="nav nav-pills pull-right" role="tablist">
    <li class="active"><a href="#view1" data-toggle="tab"><span class="glyphicon glyphicon-th-list"></span></a></li>
    <li><a href="#view2" data-toggle="tab"><span class="glyphicon glyphicon-th"></span></a></li>
 </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="view1">
        <div class="row">
            <div class="col-md-12">
            <table id="purchaseorder" class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="col-xs-1">#</th>
                        <th class="col-xs-3">Name</th>
                        <th class="col-xs-2">Custom</th>
                        <th class="col-xs-2">Color</th>
                        <th class="col-xs-2">Quantity</th>
                        <?php
                        if(($purchase_order->username == $_SESSION['username'] && ($purchase_order->status=="New" || $purchase_order->status=="On-queue")) || ($role=="hans" && ($purchase_order->status=='processing' || $purchase_order->status=='delivered'))) 
                            {
                        ?>
                        <th class="col-xs-2">Action</th>
                        <?php
                            }
                        ?>
                    </tr>
                </thead>
                <tbody>


            <?php       
                $stmt = $purchase_order->readPOItem($poid);
                $num  = $stmt->rowCount();
                //echo "<div class=\"panel-group\" id=\"accordion\" role=\"tablist\">";
                
                if($num>0){
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);
                        echo "<tr data-id=\"" . $id . "\">";
                        //echo "<td>{$i}</td>";
                        if((strpos($image_url, "define") == true ) || $image_url=='none' || $image_url=='0' || $image_url=='undefined')
                            echo "<td><img src=\"{$home_url}images/def.png\" width=\"75\" height=\"75\" /></td>";
                        else
                            echo "<td><img src=\"{$home_url}images/thumbs/{$image_url}\" width=\"75\" height=\"75\" /></td>";
                        echo "<td><b>";
                        if($product == "HH") echo "Helmet Holder";
                        else if($product == "TH") echo "Ticket Holder";
                        else if($product == "CM") echo "Chin Mount";
                        echo "</b>";
                        if(!empty($note))echo "<p>Note: {$note}</p></td>";
                        echo "<td class=\"xd-custom\">";
                        if(strpos($productname, "define") == true || $productname=='0') echo "Plain";
                        else echo "{$productname} </td>";
                        echo "<td class=\"xd-color\">{$color}</td>";
                        echo "<td><span class=\"xd-qty\">{$quantity}</span></td>";

                        if(($purchase_order->username == $_SESSION['username'] && ($purchase_order->status=="New" || $purchase_order->status=="On-queue")) || ($role=="hans" && ($purchase_order->status=='processing' || $purchase_order->status=='delivered'))) 
                            {
                        echo "<td>";
                        echo "<button data-id={" . $id . "} data-toggle=\"modal\" data-target=\"#edit\" class=\"btn btn-sm btn-edit-qty btn-default\">Edit Quantity</button> ";
                        echo "<button data-toggle=\"modal\" data-target=\"#warn\" class=\"btn btn-sm btn-delete btn-danger\">Delete</button>";
                        echo "</td>";
                            }

                        echo "</tr>";
                        $i+=1;
                        //echo $num;
                    }
                    
                }
                else{
                    echo "<div class='alert alert-info'>No products found.</div>";
                }
                //echo "</div>";

            ?>
            </tbody>
            <tfoot>
                <td colspan="4"><span class="pull-right">Total</span></td>
                <td>
                <?php
                $stmt = $purchase_order->readPOSum($poid);
                echo $purchase_order->sum;
                ?>
                </td>
            </tfoot>
            </table>
                </div>
        </div><!--end of view1-->
    </div><!--end of tab1-->
    <div role="tabpanel" class="tab-pane" id="view2">
        <div class="row">
            <div class="col-md-12">
                <table id="purchaseorder" class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="col-xs-1">#</th>
                            <th class="col-xs-4">Logos</th>
                            <th class="col-xs-2"><div style="width: 14px; height: 14px; display: block; border: 1px solid #333; background-color: #fff" class="pull-left"></div>&nbsp; White</th>
                            <th class="col-xs-2"><div style="width: 14px; height: 14px; display: block; border: 1px solid #333; background-color: #00f" class="pull-left"></div>&nbsp; Blue</th>
                            <th class="col-xs-2"><div style="width: 14px; height: 14px; display: block; border: 1px solid #333; background-color: #aaa" class="pull-left"></div>&nbsp; Gray</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $purchase_order->readPOItem2($poid);
                        $num  = $stmt->rowCount();
                        
                        if($num>0){
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                extract($row);
                                if($product == "HH"){
                                    echo "<tr>";
                                    if((strpos($image_url, "define") == true ) || $image_url=='none' || $image_url=='0' || $image_url=='undefined')
                                        echo "<td><img src=\"{$home_url}images/def.png\" width=\"75\" height=\"75\" /></td>";
                                    else
                                        echo "<td><img src=\"{$home_url}images/thumbs/{$image_url}\" width=\"75\" height=\"75\" /></td>";
                                    echo "<td><b>";
                                    if(strpos($productname, "define") == true || $productname=='0') echo "Plain";
                                        else echo "{$productname}";
                                    echo "</b>";
                                    if(!empty($note))echo "<p>Note: {$note}</p>";
                                    echo "</td>";
                                    echo "<td>" . $purchase_order->getPOItemQuantityByColor($poid, $productitemid, 'White') . "</td>";
                                    echo "<td>" . $purchase_order->getPOItemQuantityByColor($poid, $productitemid, 'Blue') . "</td>";
                                    echo "<td>" . $purchase_order->getPOItemQuantityByColor($poid, $productitemid, 'Gray') . "</td>";

                                    echo "</tr>";
                                }
                            }
                        }
                        ?>
                        
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><span class="pull-right">Total</span></td>
                            <?php
                                echo "<td>" . $purchase_order->getPOSumByColor($poid, 'White') . "</td>";
                                echo "<td>" . $purchase_order->getPOSumByColor($poid, 'Blue') . "</td>";
                                echo "<td>" . $purchase_order->getPOSumByColor($poid, 'Gray') . "</td>";
                            ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div><!-- end of tab2-->
  </div>

</div>



<div class="modal fade" id="edit" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ."?&id=" . $poid;?>" method="post">
      <div class="modal-header">
        <strong>Edit</strong>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="xd-edit-qty">Quantity</label><br/>
            <input type="number" min=1 name="xd-edit-qty" />
        </div>
        <input type="hidden" name="xd-pod-id" />
      </div>
      <div class="modal-footer">
        <button class="btn btn-sm btn-primary">Save Changes</button>
        <button class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
      </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="warn" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ."?&id=" . $poid;?>" method="post">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Are you sure</h4>
      </div>
      <div class="modal-body">
        <p>
        </p>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="xd-pod-id" />
        <input type="hidden" name="xd-delete" />
        <button class="btn btn-sm btn-default delmodal">Yes</button>
        <a href="#" class="btn btn-sm btn-primary" data-dismiss="modal">No</a>
      </div>
      </form>
    </div>
  </div>
</div>

<script src="assets/js/pod_script.js"> </script>
<?php include_once 'template/footer.php' ?>