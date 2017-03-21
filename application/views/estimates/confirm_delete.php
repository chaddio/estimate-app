<script>
$(document).ready(function(){
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $('.popover').popover('hide');
        $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
        $('.item-info').html($(e.relatedTarget).data('info'));
        $('.item-action-header').html($(e.relatedTarget).data('action'))
        $('.item-action').html($(e.relatedTarget).data('action').toLowerCase())
         //$('.debug-url').html('Delete URL: <strong>' + $(this).find('.danger').attr('href') + '</strong>');
    });
});
</script>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <span class="item-action-header"></span> <?php echo ucwords(substr($class, 0, -1));?>?
                </h3>
            </div>
            <div class="modal-body">
                Are you sure you want to <span class="item-action"></span> <?php echo substr($class, 0, -1);?> for: <span class="item-info"></span> ?<br> Click "Confirm" to proceed.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-primary danger">Confirm</a>
            </div>
        </div>
    </div>
</div>
