var home_url = "http://localhost/xd-live/";
//var home_url = "http://taxcalculator.pe.hu/";

$('#image').on('shown.bs.modal', function (event) {
    var button   = $(event.relatedTarget);
    var filename = button.data('file');
    var modal    = $(this);
    modal.find('.job-order-for-render').attr('src', home_url + "images/" + filename);
})

$('#clear').on('shown.bs.modal', function (event) {
    var modal  = $(this);
    modal.find('.delmodal').attr('href', home_url + "?truncate=Y");
})


$("button[data-close='alert']").click(function(){
    console.log('closing');
    $('.xd-alert').fadeToggle();
})


$(document).ready( function () {
     $('[data-toggle="tooltip"]').tooltip();
     $('[data-toggle="popover"]').popover();
    
});

moment().format();
setMoment();
function setMoment(){
    x=document.getElementsByClassName("dtime");  // Find the elements
    for(var i = 0; i < x.length; i++){
        x[i].innerText = moment(x[i].innerText, 'MM-DD-YYYY h:m:s A').fromNow();
    }//
    $(window).scroll(function (event) {
        var $sbar = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
        if($sbar == 0){
            $('.xd-navbar').css('box-shadow','none');
            console.log($sbar);
        }
        else{
            $('.xd-navbar').css('box-shadow','0 4px 5px 0 rgba(0,0,0,0.14),0 1px 10px 0 rgba(0,0,0,0.12),0 2px 4px -1px rgba(0,0,0,0.2)');
        }
    
        /*if($sbar>=50){
            $('.xd-toolbar').addClass("xd-sticky");
        }
        else{
            $('.xd-toolbar').removeClass("xd-sticky");
        }*/
    });
}
$(function() {
    $('span.required').parent().attr('data-toggle', "tooltip");
    $('span.required').parent().attr('title','this field is required');

    $("img.xd-img").lazyload();

    var availableTags = [
        "ActionScript",
        "AppleScript",
        "Asp",
        "BASIC",
        "C",
        "C++",
        "Clojure",
        "COBOL",
        "ColdFusion",
        "Erlang",
        "Fortran",
        "Groovy",
        "Haskell",
        "Java",
        "JavaScript",
        "Lisp",
        "Perl",
        "PHP",
        "Python",
        "Ruby",
        "Scala",
        "Scheme"
      ];

      $(".nav-search input[type='search']").autocomplete({
        source: 'http://localhost/xd-live/functions/autocomplete.php',

        /*source: function (request, response){
            $.ajax({
                url: 'http://localhost/xd-live/functions/autocomplete.php',
                type: "GET"
            });
        },*/
        select: function (event, ui) {
            event.preventDefault();
            this.value = ui.item.name;
            alert(ui.item.value);
        }
      });
});