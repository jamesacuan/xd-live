<div class="navbar navbar-default navbar-static-top infobar alert" role="navigation">
    <div class="container clearfix">
         <div class="navbar-header pull-left">Some pages and functions may break at any time. If you have encountered any issues with the system,
         kindly report it to our group chat. 
         <a href="https://m.me/join/AbZ5ZrLkMQoxDgVv" target="_blank" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-share-alt"></span> Open Messenger</a>
        </div>
         <span class="pull-right">    
         <button type="button" data-dismiss="alert" data-toggle="tooltip" data-placement="left" title="I accept the risk!" class="close" aria-label="Close"><span style="color:#fff" aria-hidden="true">&times;</span></button>    
        </span>
    </div>
</div>
<script>
$('.infobar').on('closed.bs.alert', function () {
  var url = window.location.href;
  window.location = url + "?&betagree=1";
})
</script>
<style>
    .infobar{
        min-height: 10px;
        background-color: #252736;
        color: #aaa;
        font-size: 12px;
        padding: 5px;
        border-radius: 0px;
    }
    </style>