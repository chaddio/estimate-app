<?php $this->load->view('header') ?>
<?php $this->load->view('nav') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <?php if (isset($error) && $error): ?>
            <div class="alert fade in alert-<?php echo $alertLevel;?>">
                <a class="close" data-dismiss="alert" href="#">&times;</a><?php echo $error;?>
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
            <img src="/assets/img/loader-huge.gif" id="loading-indicator-resume" style="display: none;" />
            <?php echo br(5);?>
            <p class="text-center"></p>
            <?php echo br(5);?>
                <!-- ##################### load estimate staged content ##############################-->
        </div>
    </div>
</div>
<?php $this->load->view('estimates/est_ajax') ?>
<?php $this->load->view('estimates/confirm_nav_away') ?>
<?php $this->load->view('estimates/confirm_stage1') ?>
<?php $this->load->view('footer') ?>
<script>

$(document).ready(function () {
    $('#loading-indicator-resume').show();
    getAjaxHtml('<?php echo $level;?>', '<?php echo $type;?>', '<?php echo $stage;?>');
});
</script>