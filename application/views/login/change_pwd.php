<?php $this->load->view('header.php') ?>
<?php $this->load->view('nav-login.php') ?>
<div class="container"> 
    <div class="page-header"></div>
    <div class="row">
        <div class="well well-center">
            <img class="app-logo" src="<?php base_url();?>/assets/img/applogo.png" border="0">
            <?php echo form_open($formAction, array('class' => 'form-horizontal')) . "\n"?> 
                <fieldset>
                    <legend><?php echo $viewTitle; ?></legend>
                    <?php if (isset($error) && $error): ?>
                        <div class="alert fade in alert-<?php echo $alertLevel?>">
                            <a class="close" data-dismiss="alert" href="#">×</a><?php if($error == 'invalid'){echo validation_errors();}else{echo $error;} ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-push-3">
                            <input data-toggle="tooltip" data-placement="top" type="password" id="password" class="form-control" name="password" title="Current Password" placeholder="Current Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-push-3">
                            <input data-toggle="tooltip" data-placement="top" type="password" id="newPassword" class="form-control" name="newPassword" title="New Password" placeholder="New Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-push-3">
                            <input data-toggle="tooltip" data-placement="top" type="password" id="confirmPassword" class="form-control" name="confirmPassword" title="Confirm New Password" placeholder="Confirm New Password">
                        </div>
                    </div>
                    <div class="registrationFormAlert" id="divCheckPasswordMatch"></div>
                </fieldset>
            <?php echo form_close();?>
        </div>
    </div>  
</div>
<script type="text/javascript">
    $(function() {
    $("#confirmPassword").keyup(function() {
        var password = $("#newPassword").val();
        $("#divCheckPasswordMatch").html(password == $(this).val() ? "<span style=\"color: green; font-weight: bold;\">Passwords Match</span><br><br><button type=\"submit\" name=\"submit\" class=\"btn btn-primary btn-small\">Change</button>" : "<span style=\"color: red; font-style: italic;\">Passwords do not match!</span>");
    });

});
</script>
<?php $this->load->view('footer.php') ?>