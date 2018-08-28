<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/job_order.php";
include_once "objects/settings.php";

$database = new Database();
$db = $database->getConnection();

$job_order = new JobOrder($db);
$settings  = new Settings($db);
$page_title="Create New Job Order";
$page_theme="";

$require_login=true;
include_once "login_check.php";

include 'template/header.php';
$newJO ="";
$newJOD ="";

?>
<div class="row xd-heading">
    <div class="clearfix">
        <div class="page-header pull-left">
            <h1><?php echo isset($page_title) ? $page_title : "Index"; ?></h1>
        </div>
    </div>
</div>

<div class="row xd-content">

<?php
if($_POST){
    if(isset($_POST['joid'])){
        $newJO = $_POST['joid'];
    }
    else{
        $job_order->getJobOrderCount();
        $newJO = $job_order->jocount + 1;
    }

    $newJOD = $job_order->getJobOrderDetailsCount() + 1;

    $job_order->getTypeCount($_POST['type']);
    $newTY = $job_order->tycount + 1;
    
    if($newTY<10)         $job_order->code = $_POST['type'] . "-000" . $newTY;
    elseif($newTY<100)    $job_order->code = $_POST['type'] . "-00" . $newTY;
    elseif($newTY<1000)   $job_order->code = $_POST['type'] . "-0" . $newTY;
    else                  $job_order->code = $_POST['type'] . "-" . $newTY;
    
    $job_order->type        = $_POST['type'];
    $job_order->note        = $_POST['note'];
    $job_order->status      = "For Approval";
    $job_order->expectedJO  = $newJO;
    $job_order->expectedJOD = $newJOD;
    $job_order->userid      = $_SESSION['userid'];

    if($_FILES["image"]["error"] == 4){
        $job_order->image_url = "";
        if(isset($_POST['joid'])){
            if($job_order->addJOItem() && $job_order->setStatus()) {
                echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
                echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">";
                echo "<span>&times;</span>";
                echo "</button>";
                    echo "<h4>Your requested job order is added to Job Order #{$newJO}.</h4>";
                    echo "<span>You may continue to request an image for render by adding it below, or go back to dashboard.</span>";
                echo "</div></div></div>";
            }
        
            else{
                echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
                echo "<h4>Unable to create job order.</h4>";
                print_r($errors);
                echo "</div></div></div>";
            }
        }
        else{
            if($job_order->addJOItem() && $job_order->createJO() && $job_order->setStatus()) {
                echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
                    echo "<h4>Job Order #{$newJO} was created.</h4>";
                    echo "<span>You may continue to request an image for render by adding it below, or go back to dashboard.</span>";
                echo "</div></div></div>";
            }
        
            else{
                echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
                echo "<h4>Unable to create job order.</h4>";
                print_r($errors);
                echo "</div></div></div>";
            }
        }
        
    }

    else if(isset($_FILES['image']) && $_POST){
        $errors= array();
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp  = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $tmp       = explode('.',$file_name);
        $file_ext  = strtolower(end($tmp));
        $expensions= array("jpeg","jpg","png");

        $source_properties = getimagesize($file_tmp);  //reseize
        $image_type = $source_properties[2];           //resize

        if(in_array($file_ext,$expensions)=== false){
            $errors[]="extension not allowed, please choose a JPEG or PNG file.";
        }
         
        if($file_size > 2097152) {
            $errors[]='File size must be excately 2 MB';
        }
        //rename file
        $tmpfile_name = substr(sha1($job_order->code), -20) . substr(sha1($_POST['note']), -10);
        $file_name = $tmpfile_name . "." .$file_ext;

        $job_order->image_url  = $file_name;

        if(isset($_POST['joid'])){
            if(empty($errors)==true && $job_order->addJOItem() && $job_order->setStatus()) {
                $tmp       = explode('.',$file_name);
                $filename  = $tmp[0];

                if( $image_type == IMAGETYPE_JPEG ) {   
                    $image_resource_id = imagecreatefromjpeg($file_tmp);  
                    $target_layer = $settings->fn_resize($image_resource_id, $source_properties[0],$source_properties[1]);
                    imagejpeg($target_layer, "images/thumbs/" . $filename . "." . $file_ext);
                }
                elseif( $image_type == IMAGETYPE_GIF )  {  
                    $image_resource_id = imagecreatefromgif($file_tmp);
                    $target_layer = $settings->fn_resize($image_resource_id, $source_properties[0],$source_properties[1]);
                    imagegif($target_layer, "images/thumbs/" . $filename . "." . $file_ext);
                }
                elseif( $image_type == IMAGETYPE_PNG ) {
                    $image_resource_id = imagecreatefrompng($file_tmp); 
                    $target_layer = $settings->fn_resize($image_resource_id, $source_properties[0],$source_properties[1]);
                    imagepng($target_layer, "images/thumbs/" . $filename . "." . $file_ext);
                }

                move_uploaded_file($file_tmp,"images/".$file_name);
                echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
                echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">";
                echo "<span>&times;</span>";
                echo "</button>";
                    echo "<h4>Your requested job order is added to Job Order #{$newJO}.</h4>";
                    echo "<span>You may continue to request an image for render by adding it below, or go back to dashboard.</span>";
                echo "</div></div></div>";
            }
        
            else{
                echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
                echo "<h4>Unable to create job order.</h4>";
                print_r($errors);
                echo "</div></div></div>";
            }
        }
    
        else{
            if(empty($errors)==true && $job_order->addJOItem() && $job_order->createJO() && $job_order->setStatus()) {
                $tmp       = explode('.',$file_name);
                $filename  = $tmp[0];

                if( $image_type == IMAGETYPE_JPEG ) {   
                    $image_resource_id = imagecreatefromjpeg($file_tmp);  
                    $target_layer = $settings->fn_resize($image_resource_id, $source_properties[0],$source_properties[1]);
                    imagejpeg($target_layer, "images/thumbs/" . $filename . "." . $file_ext);
                }
                elseif( $image_type == IMAGETYPE_GIF )  {  
                    $image_resource_id = imagecreatefromgif($file_tmp);
                    $target_layer = $settings->fn_resize($image_resource_id, $source_properties[0],$source_properties[1]);
                    imagegif($target_layer, "images/thumbs/" . $filename . "." . $file_ext);
                }
                elseif( $image_type == IMAGETYPE_PNG ) {
                    $image_resource_id = imagecreatefrompng($file_tmp); 
                    $target_layer = $settings->fn_resize($image_resource_id, $source_properties[0],$source_properties[1]);
                    imagepng($target_layer, "images/thumbs/" . $filename . "." . $file_ext);
                }

                move_uploaded_file($file_tmp,"images/".$file_name);
                echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
                    echo "<h4>Job Order #{$newJO} was created.</h4>";
                    echo "<span>You may continue to request an image for render by adding it below, or go back to dashboard.</span>";
                echo "</div></div></div>";
            }
        
            else{
                echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
                echo "<h4>Unable to create job order.</h4>";
                print_r($errors);
                echo "</div></div></div>";
            }
        }
    }

}

/*if(isset($_FILES['image']) && $_POST){
    $errors= array();
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp  = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $tmp       = explode('.',$file_name);
    $file_ext  = strtolower(end($tmp));
    $expensions= array("jpeg","jpg","png");
    
    if(isset($_POST['joid'])){
        $newJO = $_POST['joid'];
    }
    else{
        $job_order->getJobOrderCount();
        $newJO = $job_order->jocount + 1;
    }

    $newJOD = $job_order->getJobOrderDetailsCount() + 1;

    $job_order->getTypeCount($_POST['type']);
    $newTY = $job_order->tycount + 1;

    $job_order->userid = $_SESSION['userid'];
    $job_order->type = $_POST['type'];
    
    if(in_array($file_ext,$expensions)=== false){
       $errors[]="extension not allowed, please choose a JPEG or PNG file.";
    }
    
    if($file_size > 2097152) {
       $errors[]='File size must be excately 2 MB';
    }
    
    if($newTY<10){
        $job_order->code = $_POST['type'] . "-000" . $newTY;
    }
    elseif($newTY<100){
        $job_order->code = $_POST['type'] . "-00" . $newTY;
    }
    elseif($newTY<1000){
        $job_order->code = $_POST['type'] . "-0" . $newTY;
    }
    else{
        $job_order->code = $_POST['type'] . "-" . $newTY;
    }
    $job_order->code;

    //rename file
    $tmpfile_name = substr(sha1($job_order->code), -20) . substr(sha1($_POST['note']), -10);
    $file_name = $tmpfile_name . "." .$file_ext;

    $job_order->note       = $_POST['note'];
    $job_order->image_url  = $file_name;
    $job_order->status     = "For Approval";
    $job_order->expectedJO  = $newJO;
    $job_order->expectedJOD = $newJOD;
    $job_order->userid     = $_SESSION['userid'];

    if(isset($_POST['joid'])){
        if(empty($errors)==true && $job_order->addJOItem() && $job_order->setStatus()) {
            move_uploaded_file($file_tmp,"images/".$file_name);
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
            echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">";
            echo "<span>&times;</span>";
            echo "</button>";
                echo "<h4>Your requested job order is added to Job Order #{$newJO}.</h4>";
                echo "<span>You may continue to request an image for render by adding it below, or go back to dashboard.</span>";
            echo "</div></div></div>";
        }
    
        else{
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
            echo "<h4>Unable to create job order.</h4>";
            print_r($errors);
            echo "</div></div></div>";
        }
    }

    else{
        if(empty($errors)==true && $job_order->addJOItem() && $job_order->createJO() && $job_order->setStatus()) {
            move_uploaded_file($file_tmp,"images/".$file_name);
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
                echo "<h4>Job Order #{$newJO} was created.</h4>";
                echo "<span>You may continue to request an image for render by adding it below, or go back to dashboard.</span>";
            echo "</div></div></div>";
        }
    
        else{
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
            echo "<h4>Unable to create job order.</h4>";
            print_r($errors);
            echo "</div></div></div>";
        }
    }
}

else if(!isset($_FILES['image']) && $_POST){    
    if(isset($_POST['joid'])){
        $newJO = $_POST['joid'];
    }
    else{
        $job_order->getJobOrderCount();
        $newJO = $job_order->jocount + 1;
    }

    $newJOD = $job_order->getJobOrderDetailsCount() + 1;

    $job_order->getTypeCount($_POST['type']);
    $newTY = $job_order->tycount + 1;

    $job_order->userid = $_SESSION['userid'];
    $job_order->type = $_POST['type'];
    
    
    if($newTY<10){
        $job_order->code = $_POST['type'] . "-000" . $newTY;
    }
    elseif($newTY<100){
        $job_order->code = $_POST['type'] . "-00" . $newTY;
    }
    elseif($newTY<1000){
        $job_order->code = $_POST['type'] . "-0" . $newTY;
    }
    else{
        $job_order->code = $_POST['type'] . "-" . $newTY;
    }
    $job_order->code;

    $job_order->note       = $_POST['note'];
    $job_order->image_url  = "";
    $job_order->status     = "For Approval";
    $job_order->expectedJO  = $newJO;
    $job_order->expectedJOD = $newJOD;
    $job_order->userid     = $_SESSION['userid'];

    if(isset($_POST['joid'])){
        if($job_order->addJOItem() && $job_order->setStatus()) {
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
            echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">";
            echo "<span>&times;</span>";
            echo "</button>";
                echo "<h4>Your requested job order is added to Job Order #{$newJO}.</h4>";
                echo "<span>You may continue to request an image for render by adding it below, or go back to dashboard.</span>";
            echo "</div></div></div>";
        }
    
        else{
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
            echo "<h4>Unable to create job order.</h4>";
            print_r($errors);
            echo "</div></div></div>";
        }
    }

    else{
        if($job_order->addJOItem() && $job_order->createJO() && $job_order->setStatus()) {
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
                echo "<h4>Job Order #{$newJO} was created.</h4>";
                echo "<span>You may continue to request an image for render by adding it below, or go back to dashboard.</span>";
            echo "</div></div></div>";
        }
    
        else{
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
            echo "<h4>Unable to create job order.</h4>";
            print_r($errors);
            echo "</div></div></div>";
        }
    }
}*/
?>
<?php if($_SESSION['role']=="hans"||$_SESSION['role']=="designer"){
    echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
    echo "<h4>This page is for users only</h4>";
    echo "</div></div></div>";
}
?>



<div class="row">
<div class="col-md-8" style="padding:30px" >
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data"> 
<fieldset <?php if($_SESSION['role']=="hans"|| $_SESSION['role']=="designer") echo "disabled"; ?>>
  <div class="form-group">
    <label class="control-label">Type<span class="required">*</span></label>
    <div class="radio">
        <label class="radio-inline">
            <input type="radio" name="type" value="HH" required>Helmet Holder
        </label>
        <label class="radio-inline">
            <input type="radio" name="type" value="TH">Ticket Holder
        </label>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label" for="noteArea">Note<span class="required">*</span></label>
    <textarea class="form-control" name="note" placeholder="Add a note" rows="3" required></textarea>
  </div>
  <div class="form-group">
    <label for="url" class="control-label">Upload Image</label>
    <span class="help-block">Please upload supported images (.jpg or .png) of up to 2MB.</span>

    <div>
        <input type="file" name="image" id="url"/>
        <!--
            <input type="text" name="url" class="form-control" id="url" placeholder="image url" required>
        -->
    </div>
  </div>
  <?php
  if($_POST){
    echo "<input type=\"hidden\" name='joid' value='{$newJO}'/>";
  }
  ?>
  <button type="submit" class="btn btn-primary">Submit</button>
</fieldset>
</form>
</div>
<div class="col-md-4">
<?php
  if($_POST){
    echo "<h4>Recently added to job order</h4>";
    echo "<div class='panel-group' id='accordion' role='tablist'>";
    echo "</div>";
  }
  ?>
  </div>
</div>
</div>
<script src="js/script.js"></script>
<?php include 'template/footer.php'; ?>

