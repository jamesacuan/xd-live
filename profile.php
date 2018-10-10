<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/user.php";
include_once "objects/job_order.php";
include_once "objects/purchase_order.php";
include_once "objects/product.php";
include_once "objects/settings.php";


$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$job_order = new JobOrder($db);
$settings  =  new Settings($db);
$Product   =  new Product($db);
$purchase_order = new PurchaseOrder($db);


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
else{
    $username = $_SESSION['username'];
    $userid = $user->getUserID($username);
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
        <div class="container" style="text-align: center">
            <?php
            echo "<h2>{$username}</h2>";
            ?>
            <button class="btn btn-sm btn-primary">Follow</button>
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

        <?php
        $records_per_page=32;
        $stmt = $user->readProfileActivity($userid, 0, $records_per_page);

        $num  = $stmt->rowCount();

        if($num>0){
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                

                if($XTABLE=="JO"){
                    echo "<div class=\"panel panel-info\" style=\"margin:30px 0\">";
                        echo "<div class=\"panel-heading clearfix\" role=\"tab\">";
                        echo "<div class=\"xd-circle pull-left\" style=\"background-color: #" . $settings->getColor(substr($nickname, 0, 1)) . "\">" . substr($nickname, 0, 1) . "</div>";
                        //echo "<div class=\"pull-left\" style=\"margin-left:20px\">";
                        echo "<div class=\"pull-left\">";
                            echo "<a href=\"{$home_url}joborder.php?&id={$ID}\" >";
                            echo "<h4 style=\"margin: 2px 0\">Job Order #{$ID}</h4>";
                            echo "</a>";
                            echo "<span class=\"text-muted\">By <a href=\"profile.php?&u={$username}\" class=\"profile-link\" data-toggle=\"popover\" data-placement=\"bottom\">{$nickname}</a> | On ";
                            $test = date_format(date_create($created),"F d, Y h:i a");
                            echo $settings->time_elapsed_string($test, "true");
                            echo  " at " . date_format(date_create($created),"h:i a") . "</span>";
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
                        echo "<div class=\"xd-circle pull-left\" style=\"background-color: #" . $settings->getColor(substr($nickname, 0, 1)) . "\">" . substr($nickname, 0, 1) . "</div>";
                        //echo "<div class=\"pull-left\" style=\"margin-left:20px\">";
                        echo "<div class=\"pull-left\">";
                            echo "<a href=\"{$home_url}purchaseorder.php?&id={$ID}\">";
                            echo "<h4 style=\"margin: 2px 0\">Purchase Order #{$ID}</h4>";
                            echo "</a>";
                            echo "<span class=\"text-muted\">By <a href=\"profile.php?&u={$username}\" class=\"profile-link\" data-toggle=\"popover\" data-placement=\"bottom\">{$nickname}</a> | On ";
                            echo $settings->time_elapsed_string(date_format(date_create($created),"F d, Y h:i a"));
                            echo " at " . date_format(date_create($created),"H:i a") . "</span>";
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
                else if($XTABLE == "PRD"){
                    $Product->product_id = $ID;
                    $Product->getProductItem();
                                       
                    echo "<div class=\"panel panel-warning\" style=\"margin:30px 0\">";
                        echo "<div class=\"panel-heading clearfix\" role=\"tab\">";
                        if ($Product->image_url == "none" || !isset($Product->image_url))
                            $image_url = "def.png";
                        //echo "<div><img class=\"img-rounded\"  width=\"40\" height=\"40\" /></div>";
                        echo "<div class=\"pull-left\"><img src=\"{$home_url}images/" . $Product->image_url . "\" width=\"80\" height=\"80\" /> </div>";
                        //echo "<div class=\"pull-left\" style=\"margin-left:20px\"></div>";
                        echo "<div class=\"pull-left\" style=\"margin-left: 20px\">";
                            //echo "<a href=\"{$home_url}purchaseorder.php?&id={$ID}\">";
                            echo "<h4 style=\"margin: 2px 0\">" . $Product->name . "</h4>";
                            //echo "</a>";
                            //{$nickname} is from outside loop.
                            echo "<span class=\"text-muted\">By <a href=\"profile.php?&u={$username}\" class=\"profile-link\" data-toggle=\"popover\" data-placement=\"bottom\">{$nickname}</a> | On " . date_format(date_create($created),"F d, Y") . " at " . date_format(date_create($created),"H:i a") . "</span>";
                            echo "<span style=\"display:block\">";
                            if ($Product->type == "HH") echo "Helmet Holder";
                            else if ($Product->type == "TH") echo "Ticket Holder";
                            echo "</span>";
                        //echo $product->jod_id 
                        echo "</div></div>";
                    echo "</div>";
                }
            }
            echo "<button class=\"btn btn-default btn-md\" style=\"width:100%\">";
                echo "<span>Load more...</span>";
            echo "</button>";
        }
        
        else echo "<div class='alert alert-info'>No recent activity.</div>";
        ?>

        </div>
    </div>
</div>



<?php include 'template/footer.php' ?>