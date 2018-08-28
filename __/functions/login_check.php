<?php
// if $require_login was set and value is 'true'
if(isset($require_login) && $require_login==true){
    // if user not yet logged in, redirect to login page
    if(!isset($_SESSION['role'])){
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        header("Location: {$home_url}login.php?action=please_login&goto={$actual_link}");
    }
}

// if it was the 'login' or 'register' or 'sign up' page but the customer was already logged in
else if(isset($page_title) && ($page_title=="Login" || $page_title=="Sign Up")){
    // if user not yet logged in, redirect to login page
    if(isset($_SESSION['role']) && $_SESSION['role']=="user"){
        header("Location: {$home_url}index.php?action=already_logged_in");
    }
}
 
else{
    // no problem, stay on current page
}
?>