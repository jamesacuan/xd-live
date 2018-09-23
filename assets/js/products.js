$('#warn').on('shown.bs.modal', function (event) {
    var button   = $(event.relatedTarget);
    var did = button.data('id');
    var modal    = $(this);
    //modal.find('.test').text(did);
    modal.find('#deleteid').val(did);
});