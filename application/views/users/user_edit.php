<?php $this->load->view('header.php') ?>
<?php $this->load->view('nav.php') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <div class="well well-center">
            <?php echo form_open($formAction, array('class' => 'form-horizontal')) . "\n"?>
                <fieldset>
                <legend><?php echo $viewTitle; ?></legend>
                <?php if (isset($error) && $error): ?>
                    <div class="alert fade in alert-<?php echo $alertLevel?>">
                        <a class="close" data-dismiss="alert" href="#">×</a><?php if($error == 'invalid'){echo validation_errors();}else{echo $error;} ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    User Added:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $userObject->added; ?><br>
                    Last Modified:&nbsp;&nbsp;<?php echo $userObject->modified; ?><br><br>
                </div>
                <?php foreach ($formData as $key => $val) : ?>
                    <?php if(@$formData[$key]['options']): ?>
                        <div class="form-group">
                            <div class="col-sm-6 col-sm-push-3">
                                <?php echo form_dropdown($key,$formData[$key]['options'],$formData[$key]['optionSel'],$formData[$key]['extra']);?>
                            </div>
                        </div>
                    <?php elseif(count($formData[$key]) == 1)  : ?>
                        <?php echo form_hidden($formData[$key]);?>
                    <?php else : ?>
                        <div class="form-group">
                            <div class="col-sm-6 col-sm-push-3">
                                <?php echo form_input($formData[$key]);?>
                            </div>
                        </div>
                    <?php endif; ?>
                 <?php endforeach ; ?>
                <?php if($userObject->active == 0 ):?>
                    <div class="form-group">
                        <a href="#" data-toggle="modal" data-action="Deactivate" data-info="<?php echo $userObject->email;?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/activate/<?php echo $id;?>" class="right" title="Click to re-activate user '<?php echo $userObject->email;?>'">Activate User</a>            
                    </div>
                <?php else :?>    
                    <div class="form-group">
                        <a href="#" data-toggle="modal" data-action="Re-activate" data-info="<?php echo $userObject->email;?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/delete/<?php echo $id;?>" class="right" title="Click to deactivate user '<?php echo $userObject->email;?>'">Deactivate User</a>
                    </div>
                    <div class="form-group">
                        <a href="#" data-toggle="modal" data-action="Reset Password for " data-info="<?php echo $userObject->email;?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/reset_pwd/<?php echo $id;?>" class="right" title="Click to reset user password for '<?php echo $userObject->email;?>'">Reset Password</a>
                    </div>
                <?php endif;?>
                <div class="form-group"> 
                    <a type="button" href="javascript:void()" name="cancel" class="btn btn-default btn-small">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="submit" class="btn btn-primary btn-small">Update</button>
                </div>
                </fieldset>
            <?php echo form_close();?>   
        </div>
    </div> 
</div>
<script>
$(function(){
    $("[name=cancel]").on('click', function() {
        //window.location = '/estimates/';
        $(location).attr('href','/users/'); //TODO, add additional params to get to page/row, etc
    });
});
</script>
<?php $this->load->view('users/confirm_delete') ?>
<?php $this->load->view('footer.php') ?>