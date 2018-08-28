 /*$("#joborders button[value='delete']").on('click', function(){
    if ($("input[name='JOH']").is(':checked')) {
        alert('yes');
    }
    else alert('no');
  });
*/

/*  $('#home form').submit(function() {
    var count = $("[name='JOH[]']:checked").length;
    if($("input[name='JOH[]']").is(':checked')) {
        $('#warn').modal('show');
        return false
    }
    else{
        
        return false
    }
     // return false to cancel form action
});*/
$('#joborders tr').click(function(event) {
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
 $("#joborders thead th input[type='checkbox']").on('click',  function(){
    $length = $("input[name='JOH[]'").length;
    $i = 0;

    if($(this).is(':checked')){
      for($i=0; $i < $length; $i++){
          $("input[data-increment='" + $i + "']").prop('checked', true);
          $('body').find("input[data-increment='" + $i + "']").prop("disabled", false);
          $('body').find("input[data-increment='" + $i + "']").closest('tr').addClass('enable');
          $('body').find("input[data-increment='" + $i + "']").closest('tr').closest('tr').addClass('active');
          $('#joborders').find("tr[data-user='mine'] input[type='checkbox']").not("[disabled]").prop('checked', true);
          $('#joborders').find("tr[data-user='mine'] input[type='checkbox']").closest('tr').addClass('active');
      }
    }
    else{
      for($i=$length; $i > 0; $i--){
          $("input[data-increment='" + $i + "']").prop('checked', false);
          $('body').find("input[data-increment='" + $i + "']").prop("disabled", true);
          $('body').find("input[data-increment='" + $i + "']").closest('tr').removeClass('enable');
          $('body').find("input[data-increment='" + $i + "']").closest('tr').closest('tr').removeClass('active');
          $('#joborders').find("tr[data-user='mine'] input[type='checkbox']").prop('checked', false);
          $('#joborders').find("tr[data-user='mine'] input[type='checkbox']").closest('tr').removeClass('active');
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
    $("#joborders thead th input[type='checkbox']").prop('checked', false);
    var count = $("[name='JOH[]']:checked").length;
  });

/*$("#softaccept").click(function(){
    var count = $("[name='JOH[]']:checked").length;
    if($("input[name='JOH[]']").is(':checked')) {
        //$('#warn').modal('show');
        //$('#warn .modal-body p').text('Are you sure you want to accept ' + count+ ' entries?');
        //$("#warn a[data-dismiss='modal'").text('No');
        //$('#warn .btnmodal').removeClass('hide');
        //$('#warn .btnmodal').val('accept');
        return false
    }
    else{
        $('#warn').modal('show');
        $('#warn .modal-body p').text('You need to select some entries first.');
        $("#warn a[data-dismiss='modal'").text('Okay');
        $('#warn .btnmodal').addClass('hide');
        return false
    }
});*/



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

/*$("button[value='delete']").click(function(){
    $('#home form')[0].submit();
});*/

$('#joborders tbody tr').dblclick(function(){
    var id = $(this).attr('data-code');
    window.location = home_url + "joborderitem.php?&code=" + id;
})
$(document).ready( function () {
    var jobordertable = $('#joborders').DataTable({
        fixedHeader: {
            header: true
        },
        order: [[1, 'asc']],
        rowGroup: {
            startRender: function ( rows, group ) {
               
                return  $('<tr>')
                .append( '<td colspan="7"><a href="joborder.php?&amp;id='+group+'">Job Order #' + group + '</a></td></tr>' );
            },
            dataSrc: 1
        },
        "aLengthMenu": [[10, 15, 50, 75, -1], [10, 15, 50, 75, "All"]],
        "pageLength": 15,
        drawCallback: function(){
            $("img.xd-img").lazyload();
        },
        fnInitComplete : function( oSettings, json ){
            $("#joborders_length select").detach().prependTo("#xd-page");
        }
    });
    var filteredjotable = jobordertable
        .columns(6)
        .data()
        //.search('^(?:(?!Published).)*$\r?\n?', true, false) //The RegExp search all string that not cointains USA
        .filter( function ( value, index ) {
            //return value != 20 ? true : false;
            if(value != "Published")
                return true;
            else return false;
        } )
        .draw();

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                return $(jobordertable.row(dataIndex).node()).attr('data-status') != 'published';
              }
          );
          jobordertable.draw();


    $("#search").keyup(function() {
        jobordertable.search($(this).val()).draw();
     });

    $("input[name='filterme']").on('click', function(){
       if ( $(this).is(':checked') ) {
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    return $(jobordertable.row(dataIndex).node()).attr('data-user') == 'mine';
                  }
              );
              jobordertable.draw();
        } 
        else {
            $.fn.dataTable.ext.search.pop();
            jobordertable.draw();
            //alert('no');
        }
    });
    $('select[name="selpublish"]').on('change', function() {
        //var optionSelected = $("option:selected", this);
        //var valueSelected = optionSelected.val();
        //alert(valueSelected);
        if(this.value==1){
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    return $(jobordertable.row(dataIndex).node()).attr('data-status') != 'published';
                  }
              );
              jobordertable.draw();
            console.log(1);
        }
        else if(this.value==2){
            $.fn.dataTable.ext.search.pop();
            jobordertable.draw();
            console.log(2);
        }
        else if(this.value==3){
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    return $(jobordertable.row(dataIndex).node()).attr('data-status') == 'published';
                  }
              );
              jobordertable.draw();
              console.log(3); 
        }
      })

    $("input[name='filterpublish']").on('click', function(){
        if ( $(this).is(':checked') ) {
            $.fn.dataTable.ext.search.pop();
            jobordertable.draw();
        } 
        else {
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    return $(jobordertable.row(dataIndex).node()).attr('data-status') != 'published';
                }
            );
            jobordertable.draw();
        }


    });



});