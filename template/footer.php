</div>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/jquery-ui.min.js"></script>

<?php
    if ($page_title=="Job Orders" || $page_title=="Purchase Orders"){ ?>
<script src="assets/js/datatables.min.js"></script>
<script src="assets/js/dataTables.rowGroup.min.js"></script>

<?php 
    }
?>
<?php
    if ($page_title=="Job Orders" || $page_title=="Products"){ ?>
<script src="assets/js/lazyload.min.js"></script>
<?php 
    }
?>
<script src="assets/js/script.js"></script>

</body>
</html>