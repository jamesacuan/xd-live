$(document).ready(function(){ 
    $('#publish').click(function(){
        clearDialog();
    });

    $('#preview').on('shown.bs.modal', function (event) {
      var button   = $(event.relatedTarget);
      var filename = button.data('value');
      var modal    = $(this);
      modal.find('.preview').attr('src', home_url + "images/" + filename);
      modal.find('.xd-download').attr('href', home_url + "images/" + filename);
      modal.find('.xd-download').attr('download', filename);
    })

    $('#finish').on('shown.bs.modal', function (event) {
      var button   = $(event.relatedTarget);

    })
});

$( function() {
    $.widget( "custom.iconselectmenu", $.ui.selectmenu, {
      _renderItem: function( ul, item ) {
        var li = $( "<li>" ),
          wrapper = $( "<div>", { text: item.label } );
 
        if ( item.disabled ) {
          li.addClass( "ui-state-disabled" );
        }
 
        $( "<span>", {
          style: item.element.attr( "data-style" ),
          "class": "ui-icon " + item.element.attr( "data-class" )
        })
          .appendTo( wrapper );
 
        return li.append( wrapper ).appendTo( ul );
      }
    });

 
    $( "#productimage" )
      .iconselectmenu()
      .iconselectmenu( "menuWidget")
        .addClass( "ui-menu-icons avatar" );

});

function clearDialog(){
    $('input[name="name"]').val('');
    $('input[name="image"]').prop('checked',false);
    $('input[name="visibility"]').prop('checked',false);
    $('.form-group').removeClass('has-error');
 }