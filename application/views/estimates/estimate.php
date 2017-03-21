<?php $this->load->view('header') ?>
<?php $this->load->view('nav') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <?php if (isset($error) && $error): ?>
            <div class="col-xs-12">
                <div id="estimate_alert" class="alert fade in alert-<?php echo $alertLevel;?>">
                    <a class="close" data-dismiss="alert" href="#">&times;</a><?php echo $error;?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="well"> 
        <div class="row">
            <div class="col-xs-6">
                <?php echo $custEstTitle;?>
            </div>
            <div id="estTotalAll" class="col-xs-6 text-right">
                Estimate Total:&nbsp;&nbsp<span class="text-info">$</span><span class="text-info" id="estTotal"><?php echo $estTotal;?></span><img src="/assets/img/loader.gif" id="loading-indicator-total" style="display:none" />
            </div>
        </div>
        <?php echo br();?>
        <legend><?php echo $viewTitle;?></legend>
        <div class="row">   
            <ul id="estBreadcrumb" class="breadcrumb">
                <?php echo $breadcrumb ?>
            </ul>
        </div>
        <div id="estContent" class="row clearfix">
            <img src="/assets/img/loader-huge.gif" id="loading-indicator-first" style="display: none;" />
            <div id="basic" class="col-xs-4">
                <h3 class="text-warning">$<?php echo $baseprices['basic']['price'];?></h3>
                <div style="width: 150px;">Basic System</div>
                <img src="<?php echo base_url() . "assets/img/$type/basic.png";?>">
            </div>
            <div id="deluxe" class="col-xs-4">
                <h3 class="text-success">$<?php echo $baseprices['deluxe']['price'];?></h3>
                <div style="width: 150px;">Deluxe System</div>
                <img  src="<?php echo base_url() . "assets/img/$type/deluxe.png";?>"></a>
            </div>
            <div id="premier" class="col-xs-4">
                <h3 class="text-info" >$<?php echo $baseprices['premier']['price'];?></h3>
                <div style="width: 150px;">Premier System</div>
                <img src="<?php echo base_url() . "assets/img/$type/premier.png";?>">
            </div>
            <script>
                function getNSetFirstOption(level, type, selection){
                    $('#loading-indicator-first').show();
                    $('.popover').popover('hide');
                    var eid = '<?php echo $eid?>';
                    $('#estimate_alert').remove();
                    $.ajax({
                    url: '/ajax/setFirstStage/' + eid + '/' + '/' + level + '/' + encodeURIComponent(selection),
                    success:function(treslt){
                        $('#estTotal').html(treslt); // Load data into a <div> as HTML
                    }
                });
                    getAjaxHtml(level, type, '2');
                }
            </script>
        </div>
    </div>
</div>
<?php $this->load->view('estimates/popover_estimate') ?>
<?php $this->load->view('estimates/est_ajax') ?>
<?php $this->load->view('estimates/confirm_nav_away') ?>
<?php $this->load->view('estimates/confirm_stage1') ?>
<?php $this->load->view('footer') ?>