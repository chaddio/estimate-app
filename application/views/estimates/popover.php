<script>
$(document).ready(function(){
    $('#color-legend').popover({
        title: 'Color Legend',
        html: true,
        placement: 'top',
        content: function() {
            return $('#poColors').html();
        }
        
    });
});

$("#color-legend").on('click', function(){
    $('.edit-link').popover('hide');
});

$(".edit-link").popover({
    placement : 'right',
    html : true,
    
});

$(".edit-link").on('click',function(p){
   $('#color-legend').popover('hide');
    var edit = $(this).data('edit');
    var view = $(this).data('view');
    var closed = $(this).data('closed');
    //var myPopover = $(".edit-link").data('popover');
    if(view == '0'){
        if(closed == '0'){
            $(".popover-content").html('<div><a href="/estimates/resume/' + edit + '" class="btn btn-primary btn-xs">Resume Estimate</a>&nbsp;&nbsp;<a href="/estimates/edit/' + edit + '" class="btn btn-primary btn-xs">Edit Estimate</a></div>');
        }else{
           $(".popover-content").html('<div><a href="/estimates/edit/' + edit + '" class="btn btn-primary btn-xs">Edit Estimate</a></div>'); 
        }
        
    }else{
        $(".popover-content").html('<div><a href="/pdfgen/view/' + view + '" target="pdfWindow" class="btn btn-primary btn-xs">View / Print</a>&nbsp;&nbsp;<a href="/estimates/edit/' + edit + '" class="btn btn-primary btn-xs">Edit Estimate</a></div>');
    }
    $(".edit-link").not(this).popover('hide');
});

$(".edit-link").on('blur',function(){
    $('.edit-link').popover('hide');
    $('#color-legend').popover('hide');
});
       
</script>
<div id="poColors" style="display: none">
    <div>
        <div><span class="alert-success" style="border:1px #aaaaaa solid; "><?php echo nbs(10)?></span><?php echo nbs(1)?>=<?php echo nbs(1)?>Sold</div>
        <div><span class="alert-active" style="border:1px #aaaaaa solid; "><?php echo nbs(10)?></span><?php echo nbs(1)?>=<?php echo nbs(1)?>Estimate Only</div>
        <div><span class="alert-danger" style="border:1px #aaaaaa solid; "><?php echo nbs(10)?></span><?php echo nbs(1)?>=<?php echo nbs(1)?>Unfinished</div>
        <div><span class="alert-info" style="border:1px #aaaaaa solid; "><?php echo nbs(10)?></span><?php echo nbs(1)?>=<?php echo nbs(1)?>Not finalized</div>
        <div><span class="alert-warning" style="border:1px #aaaaaa solid; "><?php echo nbs(10)?></span><?php echo nbs(1)?>=<?php echo nbs(1)?>Failed Credit</div>
        
    </div>
</div>