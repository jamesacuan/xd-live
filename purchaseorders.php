<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/purchase_order.php";

$database = new Database();
$db = $database->getConnection();
$purchase_order = new PurchaseOrder($db);
$page_title="Purchase Orders";
$require_login=true;
$role = $_SESSION['role'];


include_once "functions/login_check.php";
include_once "functions/purchaseorders_post.php";
include 'template/header.php';
?>

<div class="row xd-heading">
    <div class="clearfix">
        <div class="page-header pull-left">
            <h1><?php echo isset($page_title) ? $page_title : "Index"; ?></h1>
        </div>
        <div class="btn-group pull-right">
            <?php if($_SESSION['role']=="user")
                echo "<button type=\"button\" onclick=\"location.href='addpurchaseorder.php'\" class=\"btn btn-primary pull-right\">+ Purchase Order</button>";
            ?>
        </div>
        
    </div>
</div>

<div class="row xd-content">
    <div class="col-md-12">
    
      <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" style="position:relative; width:100%">
            <div class="row form-inline clearfix" style="border-bottom:1px solid #ddd; padding: 10px 0;z-index:999;background-color:#fff;" data-spy="affi" data-offset-top="250">
            <div class="pull-left">
                <?php 
                if($role=='hans' || $role=='designer'){
                    echo "<button id=\"softaccept\" name=\"submit\" value=\"accept\" class=\"btn btn-sm btn-default\"><span class=\"glyphicon glyphicon-ok\"></span> Accept Request</button>";
                }
                else if($role=="user"){
                    echo "<button type=\"button\" onclick=\"location.href='addpurchaseorder.php'\" class=\"btn btn-sm btn-primary\"><span class=\"glyphicon glyphicon-plus\"></span> Create</button>";
                    echo "&nbsp;<button id=\"softdelete\" class=\"btn btn-sm btn-default\"><span class=\"glyphicon glyphicon-trash\"></span> Delete</button>";
                }
                
                ?>
            </div>
            <div class="pull-right">
                <div id="xd-page" style="float:left; margin-right:10px">
                </div>
                <div class="dropdown" style="float:left; margin-right:10px">
                    <button class="btn btn-default btn-sm" id="dLabel" type="button" data-toggle="dropdown" aria-expanded="true">
                        Filter <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a><label><input name="filterme" id="filterme" type="checkbox"> By me</label></a></li>
                        <li><a><label><input name="filterpublish" id="filterpublish" type="checkbox"> Show Published</label></a></li>
                    </ul>
                </div>
                <input type="search" id="search" placeholder="search" class="form-control input-sm" />

            </div>
    </div>
        <div class="col-md-12 xd-pos">
        <table id="purchaseorders" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class="col-xs-1"><input type="checkbox" /></th>
                        <th class="col-xs-1">PO</th>
                        <th class="col-xs-4">By</th>
                        <th class="col-xs-4">Date</th>
                        <th class="col-xs-2">Status</th>
                        <th class="col-xs-1">Action</th>
                    </tr>
                </thead>
                <tbody> 
                <?php
                $stmt = $purchase_order->read();
                $num  = $stmt->rowCount();
                $date_today   = date("m/d/Y");
                $i=0;
                if($num>0){
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);

                        $date_created = date_format(date_create($created),"m/d/Y");
                        $diff = (new DateTime($date_today))->diff(new DateTime($date_created));

                        echo "<tr ";
                        if($status=='paid')
                            echo "data-status='Done' ";
                        echo "data-code=\"{$id}\" ";
                        if($username == $_SESSION["username"])
                            echo " data-user=\"mine\" ";
                        if(($username == $_SESSION['username'] && ($status == "For Approval" || $status == "On-queue")) || (($role=="hans" || $role=="admin" || $role=="superadmin") && ($status=="New" || $status=="On-queue") && $i==0))
                            echo "class=\"enable\" ";
                        echo " >";
                        echo "<td scope=\"row\">";
                            if(($role=="hans" || $role=="admin" || $role=="superadmin") && ($status=="New" || $status=="On-queue")){
                                if($i==0)
                                    echo "<input type=\"checkbox\" name=\"JOH[]\" data-increment=\"{$i}\" value=\"{$id}\">";
                                else  echo "<input type=\"checkbox\" name=\"JOH[]\" data-increment=\"{$i}\" value=\"{$id}\" disabled>";
                                $i++;
                            }
                            else if($username == $_SESSION['username'] && ($status=="New" || $status=="On-queue"))
                                echo "<input type=\"checkbox\" name=\"JOH[]\" value=\"{$id}\">"; 
                            else echo "<input type=\"checkbox\" name=\"JOH[]\" disabled>";
                            
                            echo "</td>";
                            //data-remote false to disable deprecated bootstrap load function
                        echo "<td><a href=\"#\" data-toggle=\"modal\" data-remote=\"false\" data-target=\"#preview\">{$id}</a></td>";
                        echo "<td>{$nickname}</td>";
                        echo "<td><span class=\"dtime\"  data-toggle=\"tooltip\" title=\"" . date_format(date_create($created),"F d, Y h:i:s A") . "\">" . date_format(date_create($created),"m-d-Y h:i:s A") . "</span>";
                        if(($diff->d)>4 && ($status!="Denied" && $status != 'paid')){
                            echo " <span class=\"label label-danger\">Overdue</span>";
                        }else if(($diff->d)<2){
                            echo " <span class=\"label label-primary\">New</span>";
                        }
                        echo "</td>";

                        /* STATUS */
                        echo "<td><span class=\"label ";
                        if($status == 'New' || $status == 'On-queue') echo  'label-default">On-queue';
                        else if($status == 'paid') echo 'label-success">Done';
                        else if($status == 'processing') echo 'label-info">Processing';
                        else if($status == 'delivered') echo 'label-primary">Delivered';
                        else echo 'label-primary">' . $status;
                        echo "</span></td>";
                        
                        echo "<td><a href=\"purchaseorder.php?&amp;id={$id}\" class=\"btn btn-xs btn-default\">View</a></td>";
                        echo "</tr>";
                    }
                }//
                ?>    
                </tbody>
            </table>
            </div>
            </form>

<div class="modal fade" id="warn" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Heads up!</h4>
      </div>
      <div class="modal-body">
          <p></p>
    </div>
      <div class="modal-footer">
        <button name="submit" value="" class="btn btn-sm btn-default btnmodal">Yes</button>
        <a href="#" class="btn btn-primary" data-dismiss="modal">No</a>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="preview" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Heads up!</h4>
      </div>
      <div class="modal-body">
          <p>tests</p>
       </div>
    </div>
  </div>
</div>


        </div>
    </div>
</div>


</div>
<script src="assets/js/purchaseorders.js"></script>
<?php
include 'template/footer.php';
?>