<script>
$(document).ready(function(){
    $('#confirm-duplicate').on('show.bs.modal', function(e) {
        $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
        $('.item-info-dupe').html($(e.relatedTarget).data('info'));
        $('.item-action-header-dupe').html($(e.relatedTarget).data('action'))
        $('.item-action-dupe').html($(e.relatedTarget).data('action').toLowerCase())
         //$('.debug-url').html('Delete URL: <strong>' + $(this).find('.danger').attr('href') + '</strong>');
    });
});
</script>
<div class="modal fade" id="confirm-duplicate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <span class="item-action-header-dupe"></span> <?php echo ucwords(substr($class, 0, -1));?>?
                </h3>
            </div>
            <div class="modal-body">
                Are you sure you want to <span class="item-action-dupe"></span> <?php echo substr($class, 0, -1);?> id #<span class="item-info-dupe"></span> ?<br> Click "Confirm" to proceed.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-primary danger">Confirm</a>
            </div>
        </div>
    </div>
</div>
