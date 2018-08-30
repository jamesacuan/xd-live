<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/job_order.php";
include_once "objects/settings.php";

$database = new Database();
$db = $database->getConnection();
$type = "";
$job_order = new JobOrder($db);
$settings  = new Settings($db);
$page_title="Job Orders";
$role = $_SESSION['role'];

$page_theme="";

$require_login=true;
include_once "login_check.php";

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>JS Bin</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet" media="screen" />   
    <link href="assets/css/datatables.css" rel="stylesheet" />
    <link href="assets/css/jquery-ui.min.css" rel="stylesheet" />
    <link href="assets/css/dataTables.bootstrap.min.css" rel="Stylesheet" />
    <link href="assets/favicon.png" rel="shortcut icon" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <script src="assets/js/jquery-3.2.1.js"></script>


</head>
<body>
<style>
html {
  height: 100%;
  font-family: sans-serif;
}
body {
  height: 100%;
  overflow: hidden;
  margin: 0px;
  display: flex;
}
.column {
  height: 100%;
  display: flex;
  flex-direction: column;
}
#left {
  flex-shrink: 0;
  background-color: white;
}
#right {
  background-color: #f3f3f3;
  width:100%;
}
.top-left {
  flex-shrink: 0;
  background-color: #171d5b;
  color: white;
  padding: 20px;
}
.top-right {
  display: inline-flex;
  flex-shrink: 0;
  background-color: #171d5b;
  color: white;
  padding: 20px;
}
.bottom {
  flex-grow: 1;
  padding: 20px;
  overflow-y: auto;
}
.xd2-content{
  flex-grow:1;
  display: inline-flex;
  overflow-y: auto;
}
.xd2-content .content{
  overflow-y: auto;
  flex-grow:1;
  width: 80% !important;
}
.xd2-content .sidenav{
  width: 20% !important;
overflow-y: auto;
  border: 1px solid red;
}

ul{
  display: inline-flex;
  list-style: none;
  margin: 0;
}
li{
  margin-right: 20px;
}
</style>
      <div id="left" class="column">
			<div class="top-left">Top Left</div>
			<div class="bottom">
				<p>one</p>
				<p>two</p>
				<p>three</p>
				<p>four</p>

			</div>
		</div>
		<div id="right" class="column">
			<div class="top-right">
				<ul>
					<li>one</li>
					<li>two</li>
					<li>three</li>
					<li>four</li>
				</ul>
			</div>
            <div class="" style="border: 1px solid blue">
              Another headers
            </div>
            <div class="" style="border: 1px solid blue">
              another one
            </div>
			<div class="xd2-content">
                <div class="content">
                <table id="joborders" class="table table-hover">
                    <thead style="background-color: #fff">
                        <tr>
                            <th class="col-xs-1"><input type="checkbox" /></th>
                            <th>JO</th>
                            <th class="col-xs-1">Image</th>
                            <th class="col-xs-1">Code</th>
                            <th class="col-xs-1">By</th>
                            <th class="col-xs-5">Note</th>
                            <th class="col-xs-1">Status</th>
                            <th class="col-xs-2">Last Modified</th>
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
                            if(($diff->d)>4 && $status!="Denied" && $status != "Published"){
                                echo " <span class=\"label label-danger\">Overdue</span>";
                            }
                            else if(($diff->d)<2 && strcmp($status,"For Approval")==0){
                                echo " <span class=\"label label-primary\">New</span>";
                            }
                            echo "&nbsp;<span>" . $note ."</span><span class=\"label label-warning\">{$tag}</span>";
                            //if($date_today == $date_created) echo " <span class=\"label label-default\">New</span>";
                            //echo  $date_today . " - " . $date_created;
                            //$datediff = $date_today - $date_created;
                            echo "</td>";
                            //echo "<span class=\"glyphicon glyphicon-picture pull-right\" data-toggle=\"modal\" data-target=\"#image\" data-file=\"{$image_url}\" title=\"{$image_url}\"></span></td>";
                            //echo "<td><span title=\"" . date_format(date_create($created),"F d, Y h:i:s A") . "\">{$date_created}</span></td>";

                            echo "<td><span class=\"label ";
                                if     ($status=="For Approval") echo "label-default\">On-queue";
                                elseif ($status=="Approved") echo "label-info\">Approved";
                                elseif ($status=="Done") echo "label-primary\">Done";
                                elseif ($status=="Published") echo "label-success\">Published";
                                else   echo "label-default";
                            echo "</span>";
                            echo "</td>";
                            echo "<td class=\"datetime\"><span class=\"dtime\" data-toggle=\"tooltip\" title=\"" . date_format(date_create($created),"F d, Y h:i:s A") . "\">" . date_format(date_create($modified),"m-d-Y h:i:s A") . "</span>";
                            
                            echo "</td>";
                            
                            

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
                </div>


              <div class="sidenav">
				<p>one</p>
				<p>two</p>
				<p>three</p>
                <p>one</p>
				<p>two</p>
				<p>three</p>
                <p>one</p>
				<p>two</p>
				<p>three</p>
             </div>
          </div>
            
		</div>
        <script src="assets/js/joborders.js"></script>

<?php
include 'template/footer.php';
?>