<?php
include_once "functions/login_check.php";
$require_login=true;

include_once "config/core.php";
include_once "config/database.php";
include_once "objects/functions/dbcon.php";  //for mysqli conn

include_once "objects/purchase_order.php";

$database = new Database();
$db = $database->getConnection();

$purchase_order = new PurchaseOrder($db);
$page_title="Create New Purchase Order";

if($_POST){
    $userid   = $_SESSION['userid'];
    $product  = $_POST['product'];
    $type     = $_POST['type'];
    $color    = $_POST['color'];
    $custom   = $_POST['custom'];
    $quantity = $_POST['quantity'];
    $note     = $_POST['note'];
    
    $purchase_order->userid = $userid;
    $purchase_order->create();
    $poid    = $purchase_order->getLastPurchaseOrder();
    $purchase_order->purchase_orderid = $poid;

    foreach($product as $key => $n ) {
        print "The product is ".$n."<br>";
        print "&nbsp;&nbsp;&nbsp;&nbsp;product is ".$product[$key];
        print "&nbsp;&nbsp;&nbsp;&nbsp;custom is ".$custom[$key];
        print "<br>&nbsp;&nbsp;&nbsp;&nbsp;type is ".$type[$key];
        print "<br>&nbsp;&nbsp;&nbsp;&nbsp;color is ".$color[$key];
        print "<br>&nbsp;&nbsp;&nbsp;&nbsp;quantity is ".$quantity[$key];
        print "<br>&nbsp;&nbsp;&nbsp;&nbsp;note is ".$note[$key];
        print "<br><br><br>";
        $purchase_order->product  = $product[$key];
        $purchase_order->type     = $type[$key];
        if($custom[$key]=="undefined")
       print    $purchase_order->productitemid = 0;
       else
        print    $purchase_order->productitemid = $custom[$key];
        $purchase_order->quantity = $quantity[$key];
        $purchase_order->color    = $color[$key];
        $purchase_order->note     = $note[$key];
        $purchase_order->addItem();
    }

    $purchase_order->status = "New";
    $purchase_order->setStatus();
    $_SESSION['modal'] = "Successfully added Purchase order #" . $poid . ".";
    header("Location: {$home_url}purchaseorders.php");
}

include 'template/header.php';

?>
  <style>

input.es-input { padding-right: 20px !important; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAICAYAAADJEc7MAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAG2YAABzjgAA4DIAAIM2AAB5CAAAxgwAADT6AAAgbL5TJ5gAAABGSURBVHjaYvz//z8DOYCJgUzA0tnZidPK8vJyRpw24pLEpwnuVHRFhDQxMDAwMPz//x+OOzo6/iPz8WFGuocqAAAA//8DAD/sORHYg7kaAAAAAElFTkSuQmCC) right center no-repeat; }
input.es-input.open {
	-webkit-border-bottom-left-radius: 0; -moz-border-radius-bottomleft: 0; border-bottom-left-radius: 0;
	-webkit-border-bottom-right-radius: 0; -moz-border-radius-bottomright: 0; border-bottom-right-radius: 0; }
.es-list { position: absolute; padding: 0; margin: 0; border: 1px solid #d1d1d1; display: none; z-index: 1000; background: #fff; max-height: 160px; overflow-y: auto;
	-moz-box-shadow: 0 2px 3px #ccc; -webkit-box-shadow: 0 2px 3px #ccc; box-shadow: 0 2px 3px #ccc; }
.es-list li { display: block; padding: 5px 10px; margin: 0; }
.es-list li.selected { background: #f3f3f3; }
.es-list li[disabled] { opacity: .5; }
  </style>
<div class="row xd-heading">
    <div class="clearfix">
        <div class="page-header pull-left">
            <h1><?php echo isset($page_title) ? $page_title : "Index"; ?></h1>
        </div>
    </div>
</div>

<div class="row xd-content">
<div class="col-md-12" style="padding:30px" >
    <div align="right" style="margin-bottom:5px;">
        <button type="button" name="add" id="add_item" class="btn btn-success" data-toggle="modal" data-target="#addItemModal">Add Item</button>
   </div>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="purchase_order">
<!--<form action="functions/post_purchaseorder.php" method="post" id="purchase_order">-->
    <table class="table table-hover table-bordered table-striped">
        <thead>
            <tr>
                <th class="col-xs-1">Item</th>
                <th class="col-xs-5">Product</th>
                <th class="col-xs-2">Color</th>
                <th class="col-xs-1">Qty</th>
                <th class="col-xs-3">Actions</th>
            </tr>
        </thead>
        <tbody id="po_table">      
        </tbody>
    </table>
    <div style="padding:20px 0" >
     <input type="hidden" id="uid" value="<?php echo $_SESSION['userid'] ?>" /> 
     <input type="submit" name="insert" id="insert" class="btn btn-primary" value="Submit" />
    </div>
</form>
</div>
</div>
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title">Add Item</h4>
        </div>
        <div class="modal-body form-horizontal">
            <p class="bg-danger" id="dialog_warn"></p>
            <div class="form-group" id="item_product">
                <label class="control-label col-sm-3">Product<span class="required">*</span></label>
                <div class="radio col-sm-9">
                        <label for="HH" class="radio-inline">
                            <input type="radio" value="HH" name="product" id="HH">Helmet Holder
                        </label>
                        <label for="TH" class="radio-inline">
                            <input type="radio" value="TH" name="product" id="TH">Ticket Holder
                        </label>
                    </div>
                </div>
            <div class="form-group" id="item_type">
                <label class="control-label col-sm-3">Type<span class="required">*</span></label>
                <div class="radio radio col-sm-9">
                        <label for="pln">
                            <input type="radio" value="plain" name="type" id="pln">Plain
                        </label>
                        <label for="csm">
                            <input type="radio" value="custom" name="type" id="csm">Custom
                        </label>
                    </div>
            </div>
            <div class="form-group" id="item_color">
                <label class="control-label col-sm-3">Color<span class="required">*</span></label>
                <div class="col-sm-9">
                    <select class="form-control input-sm" name="color" id="colors"></select>
                </div>
            </div>
            <div class="form-group" id="item_custom">
                <label class="control-label col-sm-3">Custom Logo<span class="required">*</span></label>
                <div class="col-sm-9">
                    <!--<input type="text" list="custom" required/>
                    <datalist id="custom"></datalist>
                    <div></div>-->
                    <select class="form-control input-sm selectpicker" name="custom" id="custom">
                </div>
            </div>
            <div class="form-group" id="item_quantity">
                <label class="control-label col-sm-3">Quantity<span class="required">*</span></label>
                <div class="col-sm-9">
                    <input type="number" name="quantity" class="form-control" min="1" />
                </div>
            </div>
            <div class="form-group" id="item_note">
                <label class="control-label col-sm-3">Note</label>
                <div class="col-sm-9">
                    <textarea name="note" class="form-control" maxlength="100"> </textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input type="hidden" name="row_id" id="hidden_row_id" />
            <button type="button" name="save" id="save" class="btn btn-info">Add Item</button>
        </div>
        </div>
    </div>
</div>
<script src="assets/js/select.js"></script>
<script src="assets/js/po_script.js"></script>

<?php 
include 'template/footer.php';
?>