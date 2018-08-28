<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/product.php";
include_once "objects/settings.php";


$database = new Database();
$db = $database->getConnection();
$product  = new Product($db);
$settings = new Settings($db);

$page_title="Create New Product";
$require_login=true;
$role = $_SESSION['role'];


include_once "functions/login_check.php";
include 'template/header.php';
?>

<div class="row xd-heading">
    <div class="clearfix">
        <div class="page-header pull-left">
            <h1><?php echo isset($page_title) ? $page_title : "Index"; ?> <small>For Existing Items only</small></h1>
        </div>
    </div>
</div>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data"> 
<div class="row xd-content">

<?php
if($_POST){
    $product->productitemname =  $_POST["name"];
    $product->type = $_POST["type"];

    if($_FILES["image"]["error"] == 4){ //empty file url
        $product->image_url = "none";
        if($product->addProduct()) {
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-success'>";
            echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">";
            echo "<span>&times;</span>";
            echo "</button>";
                echo "<h4>Successfully added.</h4>";
            echo "</div></div></div>";
        }
        else{
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
            echo "<h4>Unable to create product.</h4>";
            print_r($errors);
            echo "</div></div></div>";
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
        $tmpfile_name = substr(sha1(rand(10,100)), -20) . substr(sha1($_POST['name']), -10);
        $file_name = $tmpfile_name . "." .$file_ext;

        $product->image_url  = $file_name;

        if(empty($errors)==true && $product->addProduct()) {
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
                echo "<h4>Successfully added.</h4>";
            echo "</div></div></div>";
        }
        else{
            echo "<div class=\"row\"><div class=\"col-md-12\"><div class='alert alert-danger'>";
            echo "<h4>Unable to add product.</h4>";
            print_r($errors);
            echo "</div></div></div>";
        }
    }
}

?>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-8" style="padding:30px" >

            <div class="form-group">
                <label class="control-label">Name<span class="required">*</span></label>
                <div>
                    <input class="form-control" type="text" name="name" required />
                </div>
            </div>

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
                <label for="url" class="control-label">Upload Image</label>
                <span class="help-block">Please upload supported images (.jpg or .png) of up to 2MB.</span>
                <div>
                    <input type="file" name="image" id="url" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12" style="padding:20px 30px" >
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>

</form>
</div>


<?php include 'template/footer.php' ?>