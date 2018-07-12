<?php 
/* FUCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC */

if(isset($_SESSION['JOH']) && isset($_SESSION['submit'])){
    if($_SESSION['submit']=='delete'){
        foreach($_SESSION['JOH'] as $key => $n ) {
            $purchase_order->status = "Y";
            $purchase_order->POID = $n;
            $purchase_order->deletePO();
        }
        $i = $key +1;
        $_SESSION['modal'] = "{$i} entries have been sucessfully deleted";
    }

    if($_SESSION['submit']=='accept'){
        $purchase_order->userid = $_SESSION['userid'];
        
        foreach($_SESSION['JOH'] as $key => $n ) {
            $purchase_order->status = "processing";
            $purchase_order->purchase_orderid = $n;
            $purchase_order->setStatus();
        }

        $i = $key +1;
        if($i==0)
            $_SESSION['modal'] = "Nothing is selected.";
        else
            $_SESSION['modal'] = "Successfully accepted {$i} entries.";
    }
    unset($_SESSION['JOH']);
    unset($_SESSION['submit']);
}

if($_POST){
    //$userid   = $_SESSION['userid'];
    $_SESSION['JOH'] = $_POST['JOH'];
    $_SESSION['submit'] = $_POST['submit'];
    header("Location:{$home_url}purchaseorders.php?");
}
?>