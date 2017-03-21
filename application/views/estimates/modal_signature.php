<div class="modal fade" id="sigModal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3>Signature Confirmation</h3>
            </div>
            <?php echo form_open('/estimates/finish_estimate', array('name' => 'f_est','class' => 'horizontal-form','id' => 'f_est')) . "\n"?>
            <?php echo form_hidden('signature_svg','default');?>
            <?php echo form_hidden('id',$id);?>
            <div class="modal-body">
                <img src="/assets/img/loader-huge.gif" id="loading-indicator-modal" style="display: none;" />
                <fieldset>
                    <div class="form-group">
                        <p>Prices are subject to change at anytime. You are confirming this current estimate and the choices within in it. You will receive a full copy of this estimate in your email after completing.</p>
                    </div>
                    <div class="form-group">
                        <div id="sigDiv" class="kbw-signature"></div>
                        <div class="col-xs-10"><a class="btn btn-default btn-small" id="clear">Clear</a><!--<button class="btn btn-default btn-small" id="json">To JSON</button> <button class="btn btn-default btn-small" id="svg">To SVG</button>--></div>
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnSigModal" class="btn btn-primary">Finish</a>
            </div>
            <?php echo form_close("\n");?>
        </div>
    </div>
</div>
<script>
$(function() {
	$('#sigDiv').signature({guideline: true,
            guidelineOffset: 25, guidelineIndent: 20});
	$('#clear').click(function() {
		$('#sigDiv').signature('clear');
	});
	/* $('#json').click(function() {
		alert($('#sigDiv').signature('toJSON'));
	});
	$('#svg').click(function() {
		alert($('#sigDiv').signature('toSVG'));
	});*/
        $('#btnSigModal').on("click",function(){
            $('#loading-indicator-modal').show();
            $("[name=signature_svg]").val($('#sigDiv').signature('toSVG'));
            $('#f_est').submit();
           
                
        });
});
</script>
