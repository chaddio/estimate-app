<script>
$(document).ready(function(){
    $('#confirm-stage1').on('show.bs.modal', function(r) {
        $('.popover').popover('hide');
        $(this).find('.danger').attr('href', $(r.relatedTarget).data('href'));
    });
});
</script>
<div class="modal fade" id="confirm-stage1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Go to the first step?</h3>
            </div>
            <div class="modal-body">
                If you return to the first step and reselect any base system, you will clear out any progress made and your estimate will start over. You may look at the base prices and resume if you choose the "Resume" option. Do you want to continue?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-primary danger">Proceed</a>
            </div>
        </div>
    </div>
</div>
