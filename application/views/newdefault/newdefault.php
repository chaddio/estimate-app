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
        <div class="well">
            <legend><?php echo $h4;?></legend>
            <?php echo $bodyMsg ?>
        </div>
    </div>
</div>   
<?php $this->load->view('footer') ?>