<?php
include "config/core.php";
include "config/database.php";

$page_title = "Job Orders";

include_once "template/header.php";
include_once "template/navbar.php";
include_once "template/sidebar.php";
?>

<div class="column" id="content">
    <div id="toolbar">
        <h2>Job Orders</h2>
    </div>
    <div id="toolbar">
        <button class="btn btn-sm btn-primary">Create</button>
        <button class="btn btn-sm btn-default">Delete</button>
        <button class="btn btn-sm btn-default">Print</button>

    </div>
    <div class="xd2-content">
        <div class="content">
            <table id="joborders" class="table table-hover">
                <thead style="background-color: #fff">
                    <tr>
                        <th class="col-xs-1 form-group"><div class="checkbox"><label><input type="checkbox" /></label></div></th>
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
                
                </tbody>
            </table> 
        </div>
        <div class="sidenav">
        
        </div>
    </div>
</div>

<?php
include_once "template/footer.php";
?>