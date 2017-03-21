<script>
function getAjaxHtml(level,type,est_stage){
var eid = '<?php echo $eid;?>';
    $('#loading-indicator-main').show();
    $.ajax({
        url: '/ajax/getEstOptions/' + eid + '/' + est_stage + '/' + level,
        success: function(reslt){
            $('#estContent').empty();
            $('#estContent').html(reslt); // Load data into a <div> as HTML
        },
        error: function(ereslt){
            $('#estContent').html(ereslt);
            $(location).attr('href','/login/timeout/');
            //return false;
        }
    });
    $.ajax({
        url: '/ajax/getBreadCrumb/' + est_stage + '/' + type + '/' + level,
        success: function(bcreslt){
            $('#estBreadcrumb').html(bcreslt); // Load data into a <div> as HTML
        }
    });
    $.ajax({
        url: '/ajax/getLegend/' + est_stage,
        success: function(lreslt){
            $('legend').html(lreslt); // Load data into <legend>
        }
    });
    //$.ajax({
    //    url: '/ajax/getTotal/' + eid + '/' + est_stage,
    //    success: function(treslt){
    //        $('#estTotal').html(treslt); // Load data into a <div> as HTML
    //    }
    //});

}
</script>
<script>
function keepSessionAlive() {
        $.get("/ajax/keep");
    }
    //interval 120000 = 120 seconds
    $(function() { window.setInterval("keepSessionAlive()", 120000); });
</script>
<script>
//    $(document).ajaxSend(function(event, request, settings) {
//        $('#loading-indicator').show();
//    });
//
//    $(document).ajaxComplete(function(event, request, settings) {
//        $('#loading-indicator').hide();
//    });
</script>
