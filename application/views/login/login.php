<?php $this->load->view('header.php'); ?>
<?php $this->load->view('nav-login.php'); ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <div class="well well-center">
            <img class="app-logo" src="<?php base_url();?>/assets/img/applogo.png" border="0">
            <?php echo form_open($formAction, array('class' => 'form-horizontal')) . "\n"?>
            <fieldset>
                <legend class="legend-center"><?php echo $app_title; ?></legend>

            <?php if (isset($error) && $error): ?>
                <div class="alert fade in alert-<?php echo $alertLevel?>">
                    <a class="close" data-dismiss="alert" href="#">&times;</a><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <div class="col-sm-6 col-sm-push-3">
                    <input type="email" data-toggle="tooltip" data-placement="top" value="<?php echo $this->input->post('email');?>" id="email" title="Email / Username" class="form-control" name="email" placeholder="Email Address">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-6 col-sm-push-3">
                    <input type="password" data-toggle="tooltip" data-placement="top" id="password" class="form-control " name="password" title="Password" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-primary btn-small">Sign in</button>
            </div>
            </fieldset>
            <?php echo form_close();?>
        </div>
    </div>
</div>
<?php $this->load->view('login/modal_userinfo.php') ?>
<?php $this->load->view('footer.php') ?>