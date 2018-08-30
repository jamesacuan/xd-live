<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php 
            if (isset($page_title))
                echo $home_title . " - " . $page_title;
            else echo "Index";
        ?>
    </title>
    <link href="assets/favicon.png" rel="shortcut icon" />
    <link href="lib/css/bootstrap.min.css" rel="stylesheet"/>   
    <link href="lib/css/datatables.css" rel="stylesheet" />
    <link href="lib/css/dataTables.bootstrap.min.css" rel="stylesheet" />
    <link href="lib/css/material.bootstrap.css" rel="stylesheet" />
    <link href="lib/css/ripples.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />

</head>