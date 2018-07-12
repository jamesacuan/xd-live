$('.btn-edit-qty').click(function(){
    var initqty  = $(this).closest("tr").find("td span.xd-qty").text();
    var inittype = $(this).closest("tr").find("td b").text();
    var initcust = $(this).closest("tr").find("td.xd-custom").text();
    var initpod  = $(this).closest("tr").attr("data-id");

    $("input[name='xd-edit-qty']").val(initqty);
    $("#edit .modal-header strong").text("Edit " + inittype + " - " + initcust);
    $("input[name='xd-pod-id']").val(initpod);
})


$('.btn-delete').click(function(){
    var inittype = $(this).closest("tr").find("td b").text();
    var initcust = $(this).closest("tr").find("td.xd-custom").text();
    var initpod  = $(this).closest("tr").attr("data-id");

    $("#warn .modal-body p").text("Do you wish to permanently delete " + inittype + " - " + initcust + "?");
    $("input[name='xd-delete']").val("1");
    $("input[name='xd-pod-id']").val(initpod);
})