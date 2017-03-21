<div class="modal fade" id="promoModal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3>Promotional Discounts</h3>
            </div>
            <?php //echo form_open($mFormAction2, $attr2) . "\n"?>
            <div class="modal-body">
                <fieldset>
                    <div class="form-group">
                        <select class="form-control" name="promo_codes" id="promo_codes" placeholder="Promotional Codes" >
                            <option value="" selected="selected">--Available Promotions--</option>
                            <?php foreach ($promocodes as $promo) :?>
                            <option value="<?php echo $promo['id']?>"><?php echo $promo['promo_code'] . " - " . $promo['description']?></option>    
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="promoAmtInput" style="display: none;"class="form-group"><input type="number" class="form-control" maxlength="10" name="promo_amount" value="" id="promo_amount" placeholder="Enter the approved amount"></div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnPromoModal" data-dismiss="modal" class="btn btn-primary">Process</a>
            </div>
            <?php //echo form_close("\n");?>
        </div>
    </div>
</div>
<script src="<?php echo base_url() . 'assets/js/jquery.number.js';?>"></script>
<script>
$('#promoModal #promo_codes').on("change", function(){
    $('#promoModal #promoAmtInput').show(); 
});
    
$('#promoModal').on('hidden.bs.modal', function (h) {
    $('#promoModal #promo_codes').val(h.defaultValue);
    $('#promoModal #promo_amount').val("");
    $('#promoModal #promoAmtInput').hide();
});
//TODO put commas back in for recalculated amounts
$('#promoModal #btnPromoModal').on("click", function(){
    var matches = $('#promo_amount').val().match(/^[0-9]+$/);
    var eid = <?php echo $eid;?>;
    var stage = <?php echo $stage;?>;
    var newTotal;
    if(matches){
        $.ajax({
            url: '/ajax/setPromo/' + eid + '/' + encodeURIComponent(stage + '|' + $('#promo_codes').val() + '|' + $('#promo_amount').val() + '.00'),
            async: false,// needed for treating value as string and not object
            success:function (data){
               // Load data into a <div> as HTML
                $('#estTotal').html($.number(data, 2));
                newTotal = data;
                
            }
        });
        if($('#payment_amount').val()){
            $.ajax({
                url: '/ajax/getNewPmtAmt/' + eid + '/' + newTotal,
                async: false,// needed for treating value as string and not object
                success:function (p){
                   // Load data into a <div> as HTML
                    $('#payment_amount').val($.number(p, 2));

                }
            });
        }
        $('#estContent #subTotalAmtDiv').show();
        $('#estContent #promoAmtDiv').show();
        var promoAmt = $('#promo_amount').val();
        var subtotal = (parseFloat(newTotal) + parseFloat(promoAmt));
        $('#promotional').val($.number(promoAmt, 2));
        $('#subtotal').val($.number(subtotal, 2));
        $('#estimate_total').val($.number(newTotal, 2));
        
    }else{
        alert('Whole numbers/dollars only for Approved Amount')
    }
    
    
});
</script>
