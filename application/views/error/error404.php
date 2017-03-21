<?php $this->load->view('header') ?>
<?php $this->load->view('nav') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <?php if (isset($error) && $error): ?>
            <div class="alert fade in alert-<?php echo $alertLevel?>">
                <a class="close" data-dismiss="alert" href="#">×</a><?php echo $error; ?>
            </div>
        <?php endif; ?>
        <div class="well well-center">
            <legend class="legend-center"><?php echo $h4;?></legend>
            <p><?php echo $bodyMsg . br(2)?></p>
            <img class="error404" src="<?php base_url();?>/assets/img/404orangutan-trans-rounded.png">
            <p><?php echo $footerMsg . br(2)?></p>
        </div>
    </div>
</div>   
<?php $this->load->view('footer') ?>