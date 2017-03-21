<div class="col-xs-10">
    {productImg}
    <table class="table table-hover">
        <thead>
        <tr>
            {thcols}
        </tr>
        </thead>
        <tbody>
            <img src="/assets/img/loader-huge.gif" id="loading-indicator-main" style="display: none;" />
            {products}    
        </tbody>
    </table>
    {button}
</div>
<script src="<?php echo base_url() . 'assets/js/jquery.number.js';?>"></script>
<script>
$('#estTotalAll').affix({
    offset: {
    top: 100
  }
})
</script>
<script>
$(function(){
var eid = '<?php echo $eid; ?>';
$('input:radio').change(
        function(){
            $//('#loading-indicator-total').show();
            $.ajax({
                url: '/ajax/setRadioOption/' + eid + '/' + encodeURIComponent(this.value),
                success:function(treslt){
                    //$('#loading-indicator-total').hide();
                    $('#estTotal').html(treslt); // Load data into a <div> as HTML
                }
            });
        })

//$('input:text').on("change",
$('input.qtyInput').on("change",
        function(){
            var matches = $(this).val().match(/[0-9 -()+]+$/);
            //alert(this.value);
            if(matches){
            //$('#loading-indicator-total').show();
                $.ajax({
                    url: '/ajax/setQuantityForOption/' + eid + '/' + encodeURIComponent(this.name + '|' + this.value),
                    success:function(treslt){
                        //$('#loading-indicator-total').hide();
                        $('#estTotal').html(treslt); // Load data into a <div> as HTML
                    }
                });
            }else{
                alert('Sorry, you can only use numeric values, value will remain unchanged until corrected');
                this.value = this.defaultValue;
            }
        })

$("[id^=priceDiv]").on("click",function(e){
    e.preventDefault();
    var divId = this.id;
    var selectorDivId = '#' + divId;
    if(!$(this).closest('tr').find('[type=checkbox]:checked').length){
        $(this).closest('tr').find('[type=checkbox]').prop('checked',true).change();
    }
    var chkBox = $(this).closest('tr').find('[type=checkbox]');
    var inpId = '<?php echo $stage;?>' + '_' + divId;
    var selectorInpId =  '#' + inpId;
    var strArray = $(this).closest('tr').find('[type=checkbox]').val().split('|');
 
    $(selectorDivId).html('<input type="number" id="' + inpId + '" maxlength="4" value="' + strArray[2] + '" style="width: 60px;" class="form-control input-sm">');
    
    $(selectorInpId).focus();
    var me = $(selectorDivId);
    var val1 = strArray[0];
    var val2 = strArray[1];
    $(selectorInpId).on("change blur",function(){
        var matched = $(this).val().match(/^[0-9\.]+$/);
            if(matched){
                var inpVal = $(this).val();
                $.ajax({
                    url: '/ajax/setPriceForOption/' + eid + '/' + encodeURIComponent(this.id + '|' + $.number(this.value, 2)),
                    success:function(oreslt){
                        //$('#loading-indicator-total').hide();
                        $('#estTotal').html(oreslt); // Load data into a <div> as HTML
                        $(me).html('$' + $.number(inpVal, 2));
                        $(chkBox).val(val1 + '|' + val2 + '|' + $.number(inpVal, 2));
                    }
                });
            }else{
                alert('Sorry, you can only use numeric/dollar, value will remain unchanged until corrected');
                this.value = this.defaultValue;
            }
        
    });
});



$('input:checkbox').on("change",
        function(){
            if(this.checked){
                //$('#loading-indicator-total').show();
                $(this).next('.hideInput').show('500');
                console.log(this.value);
                $.ajax({
                    url: '/ajax/addCheckOption/' + eid + '/' + encodeURIComponent(this.value),
                    success:function(treslt){
                        //$('#loading-indicator-total').hide();
                        $('#estTotal').html(treslt); // Load data into a <div> as HTML
                    }
                });
            }else{
                var inpID = "input[id='" + this.value + "']";
                //alert(inpID);
                $(inpID).val("1");
                $(this).next('.hideInput').hide('500');
                //$('#loading-indicator-total').show();
                $.ajax({
                    url: '/ajax/delCheckOption/' + eid + '/' + encodeURIComponent(this.value),
                    success:function(treslt){
                        //$('#loading-indicator-total').hide();
                        $('#estTotal').html(treslt); // Load data into a <div> as HTML
                    }
                });
            }
            
        });

if (!$("[name=<?php echo $stage;?>]:checked").length) {
        
        $("input:radio[name=<?php echo $stage;?>]:first").change();
        $("[name=<?php echo $stage;?>]").val(["<?php echo @$radioDefault;?>"]);
    }
});
</script>
<?php if($stage == '8') : ?>
<div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div>
          <iframe width="100%" height="350" src=""></iframe>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$("#estContent").find('[data-toggle="modal"]').click(function (){ 
    var theModal = $(this).data( "target" ),
    videoSRC = $(this).attr( "data-theVideo" ), 
    videoSRCauto = videoSRC+"?html5=1" ;
    $(theModal+' iframe').attr('src', videoSRCauto); 
});

$('#videoModal').on('hide.bs.modal', function () {
        $('#videoModal iframe').attr('src',"");
    });
</script>
<?php endif; ?>
