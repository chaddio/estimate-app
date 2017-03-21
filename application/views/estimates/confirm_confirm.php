<script>
$(document).ready(function(){
    $('#confirm-confirm').on('show.bs.modal', function() {
        $('.popover').popover('hide');
        //$('#estForm').submit();
        //$(this).find('.danger').attr('onclick', $('#estForm').submit());
    });
});
</script>
<div class="modal fade" id="confirm-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Process estimate and continue?</h3>
            </div>
            <div class="modal-body">
                If you click "Proceed", you will not be able to edit items any further within this quote. Click "Cancel" to return. 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-primary danger" onclick="$('#estForm').submit()">Proceed</a>
            </div>
        </div>
    </div>
</div>
