<?php $this->load->view('header.php') ?>
<?php $this->load->view('nav.php') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <div class="well well-center">
            <?php echo form_open($formAction, array('class' => 'form-horizontal')) . "\n"?>
                <fieldset>
                    <legend class="legend-center"><?php echo $viewTitle; ?></legend>
                <?php if (isset($error) && $error): ?>
                    <div class="alert fade in alert-<?php echo $alertLevel?>">
                        <a class="close" data-dismiss="alert" href="#">×</a><?php if($error == 'invalid'){echo validation_errors();}else{echo $error;} ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="col-sm-6 col-sm-push-3 text-left">
                        <?php if($estimateObject->added_by != $this->session->userdata('login_id')| $isAdmin) :?>
                            Sales Person: <?php echo nbs(13) . $estimateObject->sales_person . br(); ?>
                        <?php endif ;?>
                        Estimate Added: <?php echo nbs(7) . $estimateObject->added . br(); ?>
                        Last Modified: <?php echo nbs(11) .$estimateObject->modified . br(2); ?>
                        <?php if($estimateObject->completed == 1 && $estimateObject->closed == 1) :?>
                            <a href="<?php echo base_url()?>pdfgen/view/<?php echo $estimateObject->pdf_hash;?>" class="right" title="Click to print/view estimate #'<?php echo $estimateObject->id;?>'" target="pdfWindow" onclick=";">View / Print Estimate</a>
                        <?php elseif($estimateObject->completed == 0 && $estimateObject->closed == 1) : ?>
                            <a href="<?php echo base_url() . $class;?>/estimate_finalize/<?php echo $id;?>/flag" class="right" title="Click to sign and finish estimate #'<?php echo $estimateObject->id;?>'" onclick=";">Sign and Complete Estimate</a>
                        <?php else : ?>
                            <a href="<?php echo base_url() . $class;?>/resume/<?php echo $id;?>" class="right" title="Click to resume estimate #'<?php echo $estimateObject->id;?>'" onclick=";">Resume / Finish Estimate</a>
                        <?php endif ; ?>
                    </div>
                </div>
                <?php foreach ($formData as $key => $val) : ?>
                    <?php if(@$formData[$key]['options']): ?>
                        <div class="form-group" id="<?php echo $key . 'Div';?>">
                            <div class="col-sm-6 col-sm-push-3">
                                <?php echo form_dropdown($key,$formData[$key]['options'],$formData[$key]['optionSel'],$formData[$key]['extra']);?>
                            </div>
                        </div>
                    <?php elseif(count($formData[$key]) == 1)  : ?>
                        <?php echo form_hidden($formData[$key]);?>
                    <?php else : ?>
                        <div id="<?php echo $key . 'Div';?>" class="form-group">
                            <div class="col-sm-6 col-sm-push-3">
                                <?php echo form_input($formData[$key]);//TODO add ability to use new html form_$type?>
                            </div>
                        </div>
                    <?php endif; ?>
                 <?php endforeach ; ?>
                <div class="form-group" style="<?php echo ($estimateObject->sale_status == 1) ? '' : 'display: none;';?>" id="install_dateDiv">
                    <div class="col-sm-6 col-sm-push-3">
                        <input class="form-control" type="text" value='<?php echo @$estimateObject->install_date;?>' placeholder="Choose Install Date" data-toggle="tooltip" data-placement="top" id="install_date" name="install_date" title="Install Date">
                    </div>
                </div>
                <?php if($estimateObject->completed == 1 && $estimateObject->closed == 1):?>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-push-3">
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Estimate Notes"><?php echo $estimateObject->notes?></textarea>
                        </div>
                    </div>
                <?php endif ;?>
                <?php if($estimateObject->active == 0 ):?>
                    <div class="form-group">
                        <a data-target="#confirm-delete" href="#" data-toggle="modal" data-info="<?php echo $estimateObject->first_name . ' ' . $estimateObject->last_name ;?>" data-action="Un-Archive" data-href="<?php echo base_url() . $class;?>/activate/<?php echo $id;?>" class="right" title="Click to un-archive estimate: #<?php echo $estimateObject->id;?>">Un-Archive Estimate</a>            
                    </div>
                <?php else :?>    
                    <div class="form-group">
                        <a data-target="#confirm-delete" href="#" data-action="Archive" data-info="<?php echo $estimateObject->first_name . ' ' . $estimateObject->last_name ;?>" data-toggle="modal" data-href="<?php echo base_url() . $class;?>/delete/<?php echo $id;?>" class="right" title="Click to archive estimate: #<?php echo $estimateObject->id;?>">Archive Estimate</a>
                    </div>
                <?php endif;?>
                <?php if($estimateObject->completed == 1 && $estimateObject->closed == 1):?>    
                    <div class="form-group">
                        <a href="<?php echo base_url();?>pdfgen/view/<?php echo $estimateObject->pdf_hash;?>" class="right" title="Click to print/view estimate #'<?php echo $estimateObject->id;?>'" target="pdfWindow" onclick=";">View / Print Estimate</a>
                    </div> 
                <?php elseif($estimateObject->completed == 0 && $estimateObject->closed == 1) : ?>
                <div class="form-group">
                    <a href="<?php echo base_url() . $class;?>/estimate_finalize/<?php echo $id;?>/flag" class="right" title="Click to sign and finish estimate #'<?php echo $estimateObject->id;?>'" onclick=";">Sign and Complete Estimate</a>
                </div>    
                <?php else :?>
                    <div class="form-group">
                        <a href="<?php echo base_url() . $class;?>/resume/<?php echo $id;?>" class="right" title="Click to resume estimate #'<?php echo $estimateObject->id;?>'">Resume / Finish Estimate</a>
                    </div>
                <?php endif; ?>
                <div class="form-group"> 
                    <a name="cancel" href="javascript:void()" class="btn btn-default btn-small">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="submit" class="btn btn-primary btn-small">Update</button>
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
        $(location).attr('href','/estimates/');//TODO, add additional params to get to page/row, etc
    });
});
</script>
<?php if($estimateObject->completed != '1' || $estimateObject->closed != '1'):?>
    <script>
    $(function(){    
        $('#sale_statusDiv').hide();
        $('#notesDiv').hide();
    });
    </script>
<?php endif ; ?>
<script>
$(function(){
    $('#sale_status').on("change", function (){
        if($(this).find(':selected').attr('value') == 1){
            $('#install_dateDiv').show();
        }else{
            $('#install_dateDiv').hide();
        }
    })
})
</script>
<script>
$( "#install_date" ).datepicker({
    dateFormat: "mm/dd/yy"
    });
</script>
<?php $this->load->view('estimates/confirm_delete') ?>
    <?php $this->load->view('estimates/confirm_duplicate') ?>
<?php $this->load->view('footer.php') ?>