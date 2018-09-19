
$('#purchaseorders tr').click(function(event) {
    if (event.target.type !== 'checkbox') {
      $(':checkbox', this).trigger('click');
      //$(this).toggleClass('active');
      if($(this).closest('tr').hasClass('active')==false){ // active != falsebaliktad?
          $(this).closest('tr').removeClass('active');
      }
      else {
          $(this).closest('tr').addClass('active');
      }
    }
  });

 /*checkbox in header */
 $("#purchaseorders thead th input[type='checkbox']").on('click',  function(){
    $length = $("input[name='JOH[]'").length;
    $i = 0;

    if($(this).is(':checked')){
      for($i=0; $i < $length; $i++){
          $("input[data-increment='" + $i + "']").prop('checked', true);
          $('body').find("input[data-increment='" + $i + "']").prop("disabled", false);
          $('body').find("input[data-increment='" + $i + "']").closest('tr').addClass('enable');
          $('body').find("input[data-increment='" + $i + "']").closest('tr').closest('tr').addClass('active');
          $('#purchaseorders').find("tr[data-user='mine'] input[type='checkbox']").not("[disabled]").prop('checked', true);
          $('#purchaseorders').find("tr[data-user='mine'] input[type='checkbox']").closest('tr').addClass('active');
      }
    }
    else{
      for($i=$length; $i > 0; $i--){
          $("input[data-increment='" + $i + "']").prop('checked', false);
          $('body').find("input[data-increment='" + $i + "']").prop("disabled", true);
          $('body').find("input[data-increment='" + $i + "']").closest('tr').removeClass('enable');
          $('body').find("input[data-increment='" + $i + "']").closest('tr').closest('tr').removeClass('active');
          $('#purchaseorders').find("tr[data-user='mine'] input[type='checkbox']").prop('checked', false);
          $('#purchaseorders').find("tr[data-user='mine'] input[type='checkbox']").closest('tr').removeClass('active');
      }
      if($i==0){
          $("input[data-increment='" + $i + "']").prop('checked', false);
          $('body').find("input[data-increment='" + $i + "']").closest('tr').closest('tr').removeClass('active');
      }
    }
});

/* checkboxes inside body */
  $("input[name='JOH[]']").on('click', function(){
    $length = $("input[name='JOH[]']").length;
    $i = $(this).attr('data-increment');
    $i++;
    if ($(this).is(':checked')) {
        $('body').find("input[data-increment='" + $i + "']").prop("disabled", false);
        $('body').find("input[data-increment='" + $i + "']").closest('tr').addClass('enable');
        $(this).closest('tr').addClass('active');
    }
    else {
        console.log($length);
        for($j = $i; $j < $length; $j++){
            $("input[data-increment='" + $j + "']").prop('checked', false);
            $('body').find("input[data-increment='" + $j + "']").prop("disabled", true);
            $('body').find("input[data-increment='" + $j + "']").closest('tr').removeClass('enable');
            console.log($j);
        }
        $(this).closest('tr').removeClass('active');
    }
    $("#purchaseorders thead th input[type='checkbox']").prop('checked', false);
    var count = $("[name='JOH[]']:checked").length;
  });

    $("#softdelete").click(function(){
        var count = $("[name='JOH[]']:checked").length;
        if($("input[name='JOH[]']").is(':checked')) {
            $('#warn').modal('show');
            $('#warn .modal-body p').text('Are you sure you want to delete ' + count+ ' entries?');
            $("#warn a[data-dismiss='modal'").text('No');
            $('#warn .btnmodal').removeClass('hide');
            $('#warn .btnmodal').val('delete');
            return false
        }
        else{
            $('#warn').modal('show');
            $('#warn .modal-body p').text('There is nothing to delete.');
            $("#warn a[data-dismiss='modal'").text('Okay');
            $('#warn .btnmodal').addClass('hide');
            return false
        }
    });

$('#purchaseorders tbody tr').dblclick(function(){
    var id = $(this).attr('data-code');
    window.location = home_url + "purchaseorder.php?&id=" + id;
})


$("#preview").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    //$(this).find(".modal-body").load(link.attr("href"));
    fetch_preview(link.text());
    $(this).find(".modal-title").html("<a href=\"purchaseorder.php?&amp;id=" +link.text()+ "\"><h4>PO - " + link.text() + "</h4></a>");
});


function fetch_preview(data){
    console.log(data);
    $.ajax({
        url:"functions/fetch_purchaseorder_preview.php",
        method:"POST",
        data:{code:data},
        success:function(data){
            $('#preview .modal-body').html(data);
        }
    })
   }


    $(document).ready(function(){
        var potable = $('#purchaseorders').DataTable({
            "aLengthMenu": [[10, 25, 50, 75, -1], [10, 25, 50, 75, "All"]],
            "pageLength": 25,
            "order": [[ 1, "asc" ]],
            fnInitComplete : function( oSettings, json ){
                $("#purchaseorders_length select").detach().prependTo("#xd-page");
            }
        });


    var filteredpotable = potable
        .columns(3)
        .data()
        //.search('^(?:(?!Published).)*$\r?\n?', true, false) //The RegExp search all string that not cointains USA
        .filter(function (value, index) {
            //return value != 20 ? true : false;
            if(value != "Done"){
                console.log( 'y');
                return true;
            }
            else return false;
        })
        .draw();

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            return $(potable.row(dataIndex).node()).attr('data-status') != 'Done';
            }
        );
        potable.draw();


        $("input[name='filterpublish']").on('click', function(){
            if ( $(this).is(':checked') ) {
                $.fn.dataTable.ext.search.pop();
                potable.draw();
            } 
            else {
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        return $(potable.row(dataIndex).node()).attr('data-status') != 'Done';
                    }
                );
                potable.draw();
            }
        });
    
        $("input[name='filterme']").on('click', function(){
            if ( $(this).is(':checked') ) {
                 $.fn.dataTable.ext.search.push(
                     function(settings, data, dataIndex) {
                         return $(potable.row(dataIndex).node()).attr('data-user') == 'mine';
                       }
                   );
                   potable.draw();
             } 
             else {
                 $.fn.dataTable.ext.search.pop();
                 potable.draw();
                 //alert('no');
             }
         });
         $("input[id='search'").keyup(function() {
            potable.search($(this).val()).draw();
         });
    
});