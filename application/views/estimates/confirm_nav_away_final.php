<script>
$(document).ready(function(){
    $('#confirm-away').on('show.bs.modal', function(e) {
        $('.popover').popover('hide');
        $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
    });
});
</script>
<div class="modal fade" id="confirm-away" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Leave Page?
            </div>
            <div class="modal-body">
                If you leave this page, this <?php echo substr($class, 0, -1);?> will not finish and process. Click leave to continue to leave this estimate or click cancel to return to the estimate
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-primary danger">Leave</a>
            </div>
        </div>
    </div>
</div>
