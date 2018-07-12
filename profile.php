<?php
// core configuration
include_once "config/core.php";

// set page title
$page_title="View Profile";

// include login checker
$require_login=true;
include_once "login_check.php";
include 'template-header.php';
?>

<?php include 'template-footer.php' ?>