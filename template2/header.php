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