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
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary btn-small">Next</button>
                    </div>   
                </fieldset>
            <?php echo form_close();?>
      </div>
    </div>
    
  </div>
<?php $this->load->view('footer.php') ?>