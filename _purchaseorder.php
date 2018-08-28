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
$id = $_GET['id'];

$stmt = $purchase_order->readPOD($id);
$num  = $stmt->rowCount();

if($num>0){
    $page_title    = "Purchase Order #" . $id;
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
        
        header("Location: {$home_url}purchaseorder.php?&id={$id}");

    }
}

include 'template/header.php';
?>

<div class="xd-snip">
    <ol class="breadcrumb">
        <li><a href="<?php echo $home_url ?>">Home</a></li>
        <li><a href="<?php echo $home_url . "purchaseorder.php?&amp;id=" . $id?>" class="active">Purchase Order #<?php echo $id?></a></li>
    </ol>
</div>

<div class="xd-content">
<?php $purchase_order->readPOD($id); ?>
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
                            <a href="<?php echo "{$home_url}purchaseorder.php?&id={$id}&status=processing";?>" class="btn btn-primary">Accept Request</a>
                            <?php } ?>

                            <?php if (($role=='hans') && ($purchase_order->status=='processing')){ ?>
                            <a href="<?php echo "{$home_url}purchaseorder.php?&id={$id}&status=delivered";?>" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Delivered</a>
                            <?php } ?>

                            <?php if (($role=='hans') && ($purchase_order->status=='delivered' || $purchase_order->status=='On-queue' || $purchase_order->status=='New')){ //for now  ?>
                            <a href="<?php echo "{$home_url}purchaseorder.php?&id={$id}&status=paid";?>" class="btn btn-primary" data-toggle="tooltip" title="This will permanently close the PO and considered as paid and delivered."><span class="glyphicon glyphicon-ok"></span> Paid</a>
                            <?php } ?>
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
<div class="row">
    <div class="col-md-12">
    <table id="purchaseorder" class="table table-hover table-bordered table-striped">
        <thead>
            <tr>
                <th class="col-xs-1">#</th>
                <th class="col-xs-5">Name</th>
                <th class="col-xs-2">Custom</th>
                <th class="col-xs-2">Color</th>
                <th class="col-xs-2">Quantity</th>
            </tr>
        </thead>
        <tbody>


    <?php       
        $stmt = $purchase_order->readPOItem($id);
        $num  = $stmt->rowCount();
        echo "<div class=\"panel-group\" id=\"accordion\" role=\"tablist\">";
        
        if($num>0){
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                echo "<tr>";
                //echo "<td>{$i}</td>";

                if(strpos($image_url, "define") == true )
                    echo "<td><img src=\"{$home_url}images/def.png\" width=\"75\" height=\"75\" /></td>";
                else
                    echo "<td><img src=\"{$home_url}images/{$image_url}\" width=\"75\" height=\"75\" /></td>";
                echo "<td><b>";
                if($product == "HH") echo "Helmet Holder";
                else if($product == "TH") echo "Ticket Holder";
                echo " - {$type}</b>";
                echo "<p/>Note: {$note}</p></td>";
                echo "<td>";
                if(strpos($productname, "define") == true) echo "";
                else echo "{$productname} </td>";
                echo "<td>{$color}</td>";
                echo "<td>{$quantity}</td>";
                echo "</tr>";
                $i+=1;
                //echo $num;
            }
            
        }
        else{
            echo "<div class='alert alert-info'>No products found.</div>";
        }
        echo "</div>";

    ?>
        </div>
    </div>
</div>
<script src="js/script.js"></script>
<?php include_once 'template/footer.php' ?>