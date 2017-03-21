<script>
$(document).ready(function(){
    $('#basic').popover({
        title: 'Two Ton Basic System',
        html: true,
        placement: 'top',
        content: function() {
            return $('#poBasic').html();
        }
        
    })
    $('#deluxe').popover({
        title: 'Two Ton Deluxe System',
        html: true,
        placement: 'top',
        content: function() {
            return $('#poDeluxe').html();
        }
        
    })
    $('#premier').popover({
        title: 'Two Ton Premier System',
        html: true,
        placement: 'top',
        content: function() {
            return $('#poPremier').html();
        }
    })
    //$('#basic').click( function() { alert('basic'); });
    //$('#deluxe').click( function() { alert('deluxe'); });
    //$('#premier').click( function() { alert('premier'); });
    //onclick="getAjaxHtml('Basic','<?php echo $type?>', '2')
});
</script>
<div id="poBasic" style="display: none">
    <div>Basic models are the same starter type system you probably have now, just a little more efficient. <br>&nbsp;</div>
    <a class="btn btn-primary btn-xs" id="btnBasic" onclick="getNSetFirstOption('Basic','<?php echo $type?>','1|<?php echo $baseprices["basic"]["id"] . "|" . $baseprices["basic"]["price"]?>');">Choose</a>
</div>
<div id="poDeluxe" style="display: none">
    <div>Deluxe systems are more efficient, quieter and provide better airflow then a 'Basic' unit. Also some units provide 2 stage heat, and have Variable Speed technologies.<br>&nbsp;</div>
    <a class="btn btn-primary btn-xs" id="btnDeluxe" onclick="getNSetFirstOption('Deluxe','<?php echo $type?>','1|<?php echo $baseprices["deluxe"]["id"] . "|" . $baseprices["deluxe"]["price"]?>');">Choose</a>
</div>
<div id="poPremier" style="display: none">
    <div>Premier systems are like having multiple systems in one using 2-Stage compressors and other Variable speed technologies. A Premier system will perform the highest with the most amount efficiency.<br>&nbsp;</div>
    <a class="btn btn-primary btn-xs" id="btnDeluxe" onclick="getNSetFirstOption('Premier','<?php echo $type?>','1|<?php echo $baseprices["premier"]["id"] . "|" . $baseprices["premier"]["price"]?>');">Choose</a>
</div>