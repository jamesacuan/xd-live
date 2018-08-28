$(document).ready(function(){
    $('#editdialog').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var output = button.data('value');
        var modal  = $(this);
        //modal.find('#username').val(output);        
    })

    $('#deldialog').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var output = button.data('value');
        var modal  = $(this);
        $("#del_form span").innerHTML = output;
        //modal.find('#username').val(output);        
    })

    $('.btn-update').on('click', function() {
        var $row = jQuery(this).closest('tr');
        var $columns = $row.find('td');
        var values = [];
        
        jQuery.each($columns, function(i, item) {
            values[i] = item.innerHTML;
        });
        $("#displayname").val(values[0]);
        $("#username").val(values[1]);
        if(values[2]=="user") $("#editdialog input[name=role]:nth(0)").prop('checked',true);
        else if(values[2]=="admin") $("#editdialog input[name=role]:nth(1)").prop('checked',true);
    });

    $('.btn-change').on('click', function() {
        var $row = jQuery(this).closest('tr');
        var $columns = $row.find('td');
        var values = [];
        
        jQuery.each($columns, function(i, item) {
            values[i] = item.innerHTML;
        });
        $("#namechange").val(values[1]);
    });

    //var image_id = $(this).attr("id");
    /*$('#btnadduser').click(function(){  
        $('#user_form')[0].reset();
        $('#username').val()="";
        $('#action').val('insert');
        $('#button_action').val("Insert");  
    });

    $('#user_form').submit(function(){
    //$("#button_action").click(function(event){  
        alert("hi");
            var displayname = $('#displayname').val();  
            var username = $('#username').val();
            var password = $('#password').val();
            var post_url = $('#user_form').attr("action"); //get form action url
            var request_method = $('#user_form').attr("method");
            
            if(displayname != '' && username != '' && password != '') {  
                    $.post('objects/functions/fetch_user.php',{username: $('#username').val()}, function(data){
                        if(data.exists){
                            $("#users").append("error");
                        }else{
                        }
                    }, 'JSON');
            }
            else{  
                alert("All Fields are Required");  
            }
    )};*/
});  