<?php $this->load->view('header') ?>
<?php $this->load->view('nav') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <?php if (isset($error) && $error): ?>
            <div class="alert fade in alert-<?php echo $alertLevel?>">
                <a class="close" data-dismiss="alert" href="#">&times;</a><?php echo $error; ?>
            </div>
        <?php endif; ?>
        <div class="well">
            <legend><h3><?php echo $serviceCompany;?> - <?php echo $appTitle;?></h3></legend>
            <p><?php echo $message; ?></p>
            <p></p>
            <?php echo br(4);?>
        </div>
    </div>
</div>
<?php $this->load->view('footer') ?>