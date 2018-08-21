<?php

header("Content-Type: application/rss+xml; charset=ISO-8859-1");
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/job_order.php";
include_once "objects/purchase_order.php";


$database = new Database();
$db = $database->getConnection();

$job_order = new JobOrder($db);
$purchase_order = new PurchaseOrder($db);

$rss = '<?xml version="1.0" encoding="UTF-8" ?>';
$rss .= '<rss version="2.0">';
$rss .= '<channel>';
$rss .= '<title>HANC Additive</title>';
$rss .= '<link>' . $home_url . '</link>';
$rss .= '<description>This is an example RSS feed</description>';
$rss .= '<language>en-ph</language>';

$stmt = $job_order->readJODActivityStream();
$num  = $stmt->rowCount();
if($num>0){
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $rss .= '<item>';
        if($XTABLE=="JO"){
            $rss .= '<title>Job Order #' . $ID . '</title>';
            $rss .= '<link>' . $home_url . 'joborder.php?&amp;id=' .$ID .'</link>';
            $rss .= '<pubDate>' . date_format(date_create($created), 'r') .'</pubDate>';
            $rss .= '<author><name>'. $nickname . '</name><uri>' . $home_url . '</uri></author>';

            $stmt2 = $job_order->readJOD($ID);
            $num2 = $stmt2->rowCount();
            $i = 0;
            $tempjod = $ID;
            if($num2>0){
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
                    extract($row2);
                    if($i < 4){
                    /*    $rss_desc = "<tr>";
                        if($image_url=="") $image_url = "def.png";
                        $rss_desc .= "<td><img src='" . $home_url . "images/thumbs/" . $image_url . "'/></td>";
                        $rss_desc .= "<td>" . $note . "</td>";
                        $rss_desc .= "<td>" . $status . "</td>";
                        $rss_desc .= "</tr>";
                    */
                    $rss_desc = "&lt;tr&gt;";
                    if($image_url=="") $image_url = "def.png";
                    $rss_desc .= "&lt;td&gt;&lt;img src='" . $home_url . "images/thumbs/" . $image_url . "'/>&lt;/td&gt;";
                    $rss_desc .= "&lt;td&gt;" . $note . "&lt;/td&gt;";
                    $rss_desc .= "&lt;td&gt;" . $status . "&lt;/td&gt;";
                    $rss_desc .= "&lt;/tr&gt;";
                    }

                    else{
                        break;
                    }
                    $i++;
                }
            }

            /*$rss .= '<description>
                <![CDATA[
                <table width="500" border="1">' . $rss_desc . '</table>
                ]]></description>';
*/
            $rss .= '<description>
                    <![CDATA[<table border="1">' . $rss_desc . '</table>;
                    ]]></description>';
            $rss .= '<category>Job Order</category>';
            
        }
        else if($XTABLE == "PO"){
            $rss .= '<title>Purchase Order #' . $ID . '</title>';
            $rss .= '<link>' . $home_url . 'purchaseorder.php?&amp;id=' .$ID .'</link>';
            $rss .= '<pubDate>' . date_format(date_create($created), 'r') .'</pubDate>';
            $rss .= '<author><name>'. $nickname . '</name><uri>' . $home_url . '</uri></author>';

            $stmt2 = $purchase_order->readPOItem($ID);
            $num2 = $stmt2->rowCount();
            $i = 0;
            if($num2>0){
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
                    extract($row2);
                    if($i < 4){
                        $rss_desc = "<tr>";
                        $rss_desc .= "</tr>";
                    }
                    else{
                        $rss_desc .=  "<tr>";
                        $rss_desc .=  "<td colspan=\"3\"><a href=\"{$home_url}purchaseorder.php?&amp;id={$ID}\" >Show All...</a></td>";
                        $rss_desc .=  "</tr>";
                        break;
                    }
                    $i++;
                }
            }


            

            $rss .= '<description>
                <![CDATA[
                <table border="1" >' . $rss_desc . '</table>
                ]]></description>';
            $rss .= '<category>Purchase Order</category>';

        }
        $rss .= '</item>';
    }
}
$rss .= '</channel>';
$rss .= '</rss>';
echo $rss;

?>