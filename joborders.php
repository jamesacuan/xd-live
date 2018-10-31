<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/job_order.php";
include_once "objects/settings.php";

$require_login=true;
include_once "functions/login_check.php";

$database = new Database();
$db = $database->getConnection();
$type = "";
$job_order = new JobOrder($db);
$settings = new Settings($db);

$page_title="Job Orders";
$role = $_SESSION['role'];


include_once "functions/joborders_post.php";
include 'template/header.php';

if(($role=="admin" || $role=="superadmin" || $role=="hans") && isset($_GET['status'])){
    $id = $_GET['id'];
    if(isset($_GET['status'])){
        $job_order->joborderdetailsid = $id;
        if($_GET['status'] == 'Deny')
            $job_order->status = "Denied";
        elseif($_GET['status'] == 'Approve')
            $job_order->status = "Approved";
        else
            $job_order->status = $_GET['status'];
        if($job_order->approve() && $job_order->setStatus())
            echo "Done";
    }
}
elseif($role=="user" && isset($_GET['delete']) || ($role=="superadmin" && isset($_GET['delete']))){
    $id = $_GET['id'];
    if(isset($_GET['delete'])){
        $job_order->joborderdetailsid = $id;
        $job_order->status = 'Y';
        $job_order->delete();
    }
    $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $current_url = explode('?', $current_url);
    header("Location: {$current_url[0]}");
}
else{
    if (!isset($_GET['type']))
        $type = "";

    else {
        if(strtolower($_GET['type'])=='hh') $type="HH";
        elseif(strtolower($_GET['type'])=='th') $type="TH";
        else $type="";
    }
}
function truncate($string, $length, $dots = "...") {
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}
//echo $_SESSION['JOH'];
?>

<div class="row xd-heading">
    <div class="clearfix">
        <div class="page-header pull-left">
            <h1><?php echo isset($page_title) ? $page_title : "Index"; ?></h1>
        </div>
        <div class="btn-group pull-right">
            <?php if($_SESSION['role']=="user")
                echo "<button type=\"button\" onclick=\"location.href='addjoborder.php'\" class=\"btn btn-primary\">+ Job Order</button>";
                
            ?>
            <!-- <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-option-vertical"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" onclick="window.print();">Print...</a></li>
                        <li><a href="objects/functions/export.php">Export to Excel...</a></li>
                    </ul>
            -->
        </div>
    </div>
</div>



<div class="row xd-content">
    <ul class="nav nav-tabs clearfix" role="tablist">
        <li role="presentation" <?php if($type=="") echo "class=\"active\"" ?>><a href="<?php echo $home_url ?>joborders.php">View All</a></li>
        <li role="presentation" <?php if($type=="HH") echo "class=\"active\"" ?>><a href="<?php echo $home_url ?>joborders.php?type=HH">Helmet Holder</a></li>
        <li role="presentation" <?php if($type=="TH") echo "class=\"active\"" ?>><a href="<?php echo $home_url ?>joborders.php?type=TH">Ticket Holder</a></li>
        <div class="pull-right form-inline">
        </div>
    </ul>


  <div role="tabpanel" class="tab-pane active" id="home">
      <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" style="position:relative; width:100%">
          <div class="row form-inline clearfix" style="border-bottom:1px solid #ddd; padding: 10px 0;z-index:999;background-color:#fff;" data-spy="affi" data-offset-top="250">
          <div class="pull-left">  
            
                <?php 
                if($role=='hans' || $role=='designer'){
                    echo "<button id=\"softaccept\" name=\"submit\" value=\"accept\" class=\"btn btn-sm btn-default\"><span class=\"glyphicon glyphicon-ok\"></span> Approve Request</button>";
                }
                else if($role=="user"){
                    echo "<button type=\"button\" onclick=\"location.href='addjoborder.php'\" class=\"btn btn-sm btn-primary\"><span class=\"glyphicon glyphicon-plus\"></span> Create</button>";
                    echo "&nbsp;<button id=\"softdelete\" class=\"btn btn-sm btn-default\"><span class=\"glyphicon glyphicon-trash\"></span> Delete</button>";
                }
                
                ?>
            </div>
            <!--
            <div class="dropdown pull-left">
                <button class="btn btn-default" id="dLabel" type="button" data-toggle="dropdown">
                    Filter <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a><label><input type="checkbox" name="filterme" id="filterme"/> By me</a></label></li>
                    <li><a><label><input type="checkbox" name="filterpublish" id="filterpublish"/> Show Published</a></label></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#" onclick="window.print();">Print...</a></li>
                    <li><a href="objects/functions/export.php">Export to Excel...</a></li>
                </ul>
            </div>
            -->
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

                <!--<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="glyphicon glyphicon-option-vertical"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#" onclick="window.print();">Print...</a></li>
                    <li><a href="objects/functions/export.php">Export to Excel...</a></li>
                </ul>
                -->
            </div>
          </div>
        <table id="joborders" class="table table-hover">
            <thead style="background-color: #fff">
                <tr>
                    <th class="col-xs-1"><input type="checkbox" /></th>
                    <th>JO</th>
                    <th class="col-xs-1">Image</th>
                    <th class="col-xs-1">Code</th>
                    <th class="col-xs-1">By</th>
                    <th class="col-xs-3">Note</th>
                    <th class="col-xs-1">Date Created</th>
                    <th class="col-xs-1">Last Update</th>
                    <th class="col-xs-3">Status</th>
                    <!--<th class="col-xs-1">Actions</th>-->
                </tr>
            </thead>
            <tbody>

            <?php       
                $stmt = $job_order->read($type);
                $num  = $stmt->rowCount();
                $date_today   = date("m/d/Y");
                $i=0;
                if($num>0){
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);

                        $date_created = date_format(date_create($created),"m/d/Y");
                        $diff = (new DateTime($date_today))->diff(new DateTime($date_created));

                        echo "<tr class=\"";
                        if(($diff->d)<2 && strcmp($status,"For Approval")==0)
                            echo "new ";
                        if(($username == $_SESSION['username'] && ($status == "For Approval" || $status == "On-queue")) || (($role=="hans" || $role=="admin" || $role=="superadmin") && ($status=="For Approval" || $status=="On-queue") && $i==0))
                            echo "enable ";
                        echo "\"";
                        if($status == "Published") echo " data-status=\"published\" ";
                        if($username == $_SESSION["username"]) echo " data-user=\"mine\" ";
                        echo "data-code=\"{$code}\" ";
                        echo ">";
                            //if($date_today == $date_created) $date_created = date_format(date_create($created),"h:i A");
                            //else $date_created = date_format(date_create($created),"F d");;
                            echo "<td scope=\"row\">";
                            if(($role=="hans" || $role=="admin" || $role=="superadmin") && ($status=="For Approval" || $status=="On-queue")){
                                if($i==0)
                                    echo "<input type=\"checkbox\" name=\"JOH[]\" data-increment=\"{$i}\" value=\"{$JODID}\">";
                                else  echo "<input type=\"checkbox\" name=\"JOH[]\" data-increment=\"{$i}\" value=\"{$JODID}\" disabled>";
                                $i++;
                            }
                            else if($username == $_SESSION['username'] && ($status=="For Approval" || $status=="On-queue"))
                                echo "<input type=\"checkbox\" name=\"JOH[]\" value=\"{$JODID}\">"; 
                            else echo "<input type=\"checkbox\" name=\"JOH[]\" disabled>";
                            
                            echo "</td>";
                            echo "<td>{$JOID}</td>";
                            if(empty($image_url)) $image_url = "def.png";
                            echo "<td><a href=\"#\" data-toggle=\"modal\" data-target=\"#image\" data-file=\"{$image_url}\"><img alt=\"t\" data-src=\"{$home_url}images/thumbs/{$image_url}\" class=\"img-circle xd-img\" width=\"30\" height=\"30\" /></a>";
                            echo "<td><a href=\"{$home_url}joborderitem.php?&amp;code={$code}\">{$code}</a></td>";
                            echo "<td>{$username}</td>";
                            //echo "<td><div class=\"xd-circle pull-left\" style=\"background-color: #" . $settings->getColor(substr($username, 0, 1)) . "\">" . substr($username, 0, 1) . "</div></td>";
                            
                            echo "<td class=\"clearfix\">";
                            echo "&nbsp;<span>" . truncate($note,60, "...") ."</span><span class=\"label label-warning\">{$tag}</span>";
                            //if($date_today == $date_created) echo " <span class=\"label label-default\">New</span>";
                            //echo  $date_today . " - " . $date_created;
                            //$datediff = $date_today - $date_created;
                            echo "</td>";
                            //echo "<span class=\"glyphicon glyphicon-picture pull-right\" data-toggle=\"modal\" data-target=\"#image\" data-file=\"{$image_url}\" title=\"{$image_url}\"></span></td>";
                            //echo "<td><span title=\"" . date_format(date_create($created),"F d, Y h:i:s A") . "\">{$date_created}</span></td>";

                            
                            echo "<td class=\"datetime\"><span class=\"dtime\" data-toggle=\"tooltip\" title=\"" . date_format(date_create($created),"F d, Y h:i:s A") . "\">" . date_format(date_create($created),"m-d-Y h:i:s A") . "</span>";
                            
                            echo "</td>";
                            echo "<td><span class=\"dtim\" data-toggle=\"tooltip\" title=\"" . date_format(date_create($modified),"F d, Y h:i:s A") . "\">" . date_format(date_create($modified),"M d, Y") . "</span> </td>";
                           

                            ?>
                            <?php
                                /*echo "<a href=\"joborderitem.php?&amp;code={$code}\" class=\"btn btn-xs btn-default\">View</a>";
                                if(($status=="For Approval" && $role=="user" && $_SESSION['username']==$username) || ($role=="hans" || $role=="admin" || $role=="superadmin") && $status=="For Approval"){ 
                                ?>
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <span class="glyphicon glyphicon-option-vertical"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                <?php 
                                if(($role=="hans" || $role=="admin" || $role=="superadmin") && $status=="For Approval"){
                                    echo "<li><a href=\"" . $home_url . "joborders.php?id={$JODID}&amp;status=Approve\">Approve</a></li>";
                                    echo "<li><a href=\"" . $home_url . "joborders.php?id={$JODID}&amp;status=Deny\">Deny</a></li>";
                                }
                                if(($status=="For Approval" && $role=="user" && $_SESSION['username']==$username) || ($status=="For Approval" && $role=="superadmin")){
                                    echo "<li><a href=\"#\" data-id={$JODID} data-toggle=\"modal\" data-target=\"#warn\">Delete</a></li>";
                                }?></ul>
                                </div>
                                <?php
                                    }*/

                            echo "<td><span class=\"label ";
                                if     ($status=="For Approval") echo "label-default\">On-queue";
                                elseif ($status=="Approved") echo "label-info\">Approved";
                                elseif ($status=="Done") echo "label-primary\">Done";
                                elseif ($status=="Published") echo "label-success\">Published";
                                else   echo "label-default";
                            echo "</span>";
                            if(($diff->d)>4 && $status!="Denied" && $status != "Published"){
                                echo " <span class=\"label label-danger\">Overdue</span>";
                            }
                            else if(($diff->d)<2 && strcmp($status,"For Approval")==0){
                                echo " <span class=\"label label-primary\">New</span>";
                            }
                            echo "</td>";
                                ?>
                            <?php

                        echo "</tr>";
                    }
                }
                else{
                   // echo "<div class='alert alert-info'>No products found.</div>";
                }
            ?>
            </tbody>
        </table> 

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
</form>
    </div>

<div class="modal fade" id="image" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <img class="job-order-for-render" />
      </div>
    </div>
  </div>
</div>


</div>
<script src="assets/js/joborders.js"></script>
<?php
include 'template/footer.php';
?>