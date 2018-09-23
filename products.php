<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/product.php";

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

$page_title= "Products";
$type = "";
if (!isset($_GET['type']))
    $type = "";
else {
    if(strtolower($_GET['type'])=='hh') $type="HH";
    elseif(strtolower($_GET['type'])=='th') $type="TH";
    else $type="";
}

$require_login=true;
$page_ribbon="F";
$page_url = "products.php?";

if($_POST){
    if(isset($_POST['xd-delete'])){
        $purchase_order->userid = $_SESSION["userid"];
        $purchase_order->PODID    = $_POST['xd-pod-id'];
        $purchase_order->POID     = $poid;
        $purchase_order->deletePOD();
    }
}


include_once "login_check.php";
include 'template/header.php'
?>

<div class="container">
<div class="row">

    <div class="col-md-3">
        <div class="thumbnail panel panel-default">
            <div class="caption">
                <span><?php echo $product->getItemCount('')?></span>
                <h3>Products</h3>
            </div>
            <ul class="list-group">
                <li class="list-group-item"><a href="<?php echo $home_url ?>products.php?type=HH">Helmet Holder</a></li>
                <li class="list-group-item"><a href="<?php echo $home_url ?>products.php?type=TH">Ticket Holder</a></li>
            </ul>
        </div>
    </div>
    
    <div class="col-md-9">
    
    <?php
    if (!isset($_GET['type'])){}
    else{
        echo "<div class=\"row\">";
            echo "<span>Showing ";
            echo $product->getProductItemsCount($type);
            echo " results for</span> <span class=\"label label-success\">";
            if ($type=="HH") echo "Helmet Holder";
            else if ($type="TH") echo "Ticket Holder";
            echo "&nbsp;<a href=\"{$home_url}products.php\"><span>×</span></a></span>";
        echo "</div>";
    }
    ?>
    <div class="row">
    <?php   
            $total_rows = $product->getProductItemsCount($type);  
            $stmt = $product->readItems($type, $from_record_num, $records_per_page);
            $num  = $stmt->rowCount();
            $temp=0;

            if($num>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    echo "<div class=\"col-sm-3 col-md-3 xd-product\">";
                    echo "<div class=\"thumbnail  xd-product-thumbnail\">";
                    if(!empty($_SESSION['admin'])){
                    ?>
                    <div class="dropdown pull-right">
                        <button class="btn btn-xs btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" data-toggle="modal" data-target="#warn" data-id="<?php echo $id?>">Delete</a></li>
                        </ul>
                    </div>
                    <?php
                    }
                    if($image_url=="none") echo  "<img src=\"{$home_url}images/def.png\">";
                    else   echo  "<img data-src=\"{$home_url}images/{$image_url}\" class=\"xd-img\">";
                    echo  "<div class=\"caption\">";
                    if($type=="HH"){
                        echo "<h4>{$name}</h4>";
                    }
                    else if($type=='TH')
                        echo    "<h4>{$name}</h4>";
                    echo    "<p>";
                        if($type=="HH") echo "Helmet Holder";
                        else if($type=='TH') echo "Ticket Holder";
                    echo "</p>";
                    //echo "<p>{$id}</p>";
                    //echo    "<p>";
                    //if (!empty($code)) echo "{$code}";
                    //else echo "manually added";
                    //echo "</p>";
                    /*if($visibility==$_SESSION['userid']) echo    " - <span>Visible only to you</span>";
                    echo "</p>";
                    //echo   "<p><a href=\"#\" class=\"btn btn-primary\" role=\"button\">Button</a>";
                    echo  "</div>";
                    */
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "<div class=\"row\">";
                echo "<div class=\"col-sm-12\">";
                include 'template/paging.php';
                echo "</div></div>";
            }
    ?>
    </div>
    </div>
</div>
</div>
<div class="modal fade" id="warn" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Heads up!</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to delete this item?</p>
    </div>
      <div class="modal-footer">
        <input type="hidden" id="deleteid" name="id" value="" />
        <button name="submit" value="" class="btn btn-sm btn-default btnmodal">Yes</button>
        <a href="#" class="btn btn-primary" data-dismiss="modal">No</a>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/products.js"></script>
<?php include_once "template/footer.php" ?>>