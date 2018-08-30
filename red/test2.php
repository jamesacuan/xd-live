<?php
include_once "config/core.php";
include_once "config/database.php";

$page_theme="";

$require_login=true;

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>JS Bin</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet" media="screen" />   
    <link href="assets/css/datatables.css" rel="stylesheet" />
    <link href="assets/css/dataTables.bootstrap.min.css" rel="Stylesheet" />
    <link href="assets/favicon.png" rel="shortcut icon" />
    <script src="assets/js/jquery-3.2.1.js"></script>
    <link href="lib/css/material.bootstrap.css" rel="stylesheet" />
    <link href="lib/css/ripples.css" rel="stylesheet" />

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
            <div class="form-group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#simple-dialog">Open dialog</button>            </div>
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
              <div class="form-group label-floating is-empty">
                <label for="i5" class="control-label">label-floating</label>
                <input class="form-control" id="i5" type="email">
                <span class="help-block">This is a hint as a <code>span.help-block.hint</code></span>
              </div>
             </div>
          </div>
            
		</div>
        <script src="lib/js/material.js"></script>
        <script src="lib/js/ripples.min.js"></script>
<script>
$.material.init()
</script>

