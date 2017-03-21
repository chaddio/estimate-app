<?php $this->load->view('header.php') ?>
<?php $this->load->view('nav.php') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <div class="well well-center">
            <?php echo form_open($formAction, $formAttr) . "\n"?>
                <fieldset>
                    <legend class="legend-center"><?php echo $viewTitle; ?></legend>
                <?php if (isset($error) && $error): ?>
                    <div id="final_alert" class="alert fade in alert-<?php echo $alertLevel?>">
                        <a class="close" data-dismiss="alert" href="#">×</a><?php if($error == 'invalid'){echo validation_errors();}else{echo $error;} ?>
                    </div>
                <?php endif; ?>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-push-3 text-left">
                            Sales Person: <?php echo nbs(13) . $estimateObject->sales_person . br(); ?>
                            Estimate Total: <span class='text-success'><?php echo nbs(9) . '$'  .$total . '</span>' . br(2); ?>
                                <p class="text-info strong"><em>** Full estimate will be emailed to customer after form is completed **</em></p>
                        </div>
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
                                <?php echo form_input($formData[$key]);//TODO add ability to use new html form_$type?>
                            </div>
                        </div>
                    <?php endif; ?>
                 <?php endforeach ; ?>
                <div class="form-group" style="display: none;" id="install_dateDiv">
                    <div class="col-sm-6 col-sm-push-3">
                        <input class="form-control" type="text" placeholder="Choose Install Date" data-toggle="tooltip" data-placement="top" id="install_date" name="install_date" title="Install Date">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-6 col-sm-push-3">
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Estimate Notes"><?php echo @$estimateObject->notes;?></textarea>
                    </div>
                </div>
                <div class="form-group"> 
                    <a data-toggle="modal" data-action="Archive" data-info="<?php echo $estimateObject->first_name . ' ' . $estimateObject->last_name ?>" data-target="#confirm-delete" href="#" data-href="<?php echo base_url() . 'estimates/delete/' . $id?>" class="btn btn-default btn-small">Cancel Estimate</a>&nbsp;&nbsp;&nbsp;&nbsp;<button href="#" id="btnSign" class="btn btn-primary btn-small">Sign and Complete</button><!--<input type="submit" class="btn btn-primary btn-small" value="Sign and Complete">-->
                </div>
                
                </fieldset>
            <?php echo form_close();?>   
        </div>
    </div> 
</div>
<script>
$(function(){
    $('#sale_status').on("change", function (){
        if($(this).find(':selected').attr('value') == 1){
            $('#install_dateDiv').show();
        }else{
            $('#install_dateDiv').hide();
        }
    });
});
</script>
<script>
$( "#install_date" ).datepicker({
    dateFormat: "mm/dd/yy"
    });
</script>
<script>
$(function() {
    $('#est_finalize').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            first_name: {
                message: 'The first name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The first name is required and cannot be empty'
                    },
                    stringLength: {
                        min: 2,
                        message: 'The first name must be 2 or more characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z\ \-]+$/,
                        message: 'First name can only be letters,dashes and spaces'
                    }
                }
            },
            last_name: {
                message: 'The last name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The last name is required and cannot be empty'
                    },
                    stringLength: {
                        min: 2,
                        message: 'The last name must be 2 or more characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z\ \-]+$/,
                        message: 'Last name can only be letters,dashes and spaces'
                    }
                }
            },
            address: {
                message: 'Address is not valid',
                validators: {
                    notEmpty: {
                        message: 'Address is required and cannot be empty'
                    },
                    stringLength: {
                        min: 5,
                        message: 'Address must be longer than that, please correct'
                    },
                    regexp: {
                        regexp: /^[0-9a-zA-Z\ \-\.]+$/,
                        message: 'Address can only be letters,dashes,dots and spaces'
                    }
                }
            },
            state: {
                message: 'Select a state',
                validators: {
                    notEmpty: {
                        message: 'State is required, please select one'
                    },
                    stringLength: {
                        min: 2,
                        message: 'State is required, please select one'
                    },
                }
            },
            city: {
                message: 'City is not valid',
                validators: {
                    notEmpty: {
                        message: 'City is required, please enter one'
                    },
                    stringLength: {
                        min: 2,
                        message: 'City needs to 2 chars or longer'
                    },
                    regexp: {
                        regexp: /^[0-9a-zA-Z\ \-\.]+$/,
                        message: 'City be letters,dashes,dots and spaces'
                    }
                }
            },
            phone_number: {
                message: 'The last name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The phone number is required and cannot be empty'
                    },
                    stringLength: {
                        min: 10,
                        max:14,
                        message: 'The phone number is not a correct length'
                    },
                    regexp: {
                        regexp: /^[0-9\ \(\)\-]+$/,
                        message: 'Phone number can be digits, dashes and parens. for area code only'
                    }
                }
            },
            zip_code: {
                message: 'Zip Code is not valid',
                validators: {
                    notEmpty: {
                        message: 'Zip code is required and cannot be empty'
                    },
                    stringLength: {
                        min: 5,
                        max: 5,
                        message: 'Zip code must be 5 characters long'
                    },
                    regexp: {
                        regexp: /^[0-9]+$/,
                        message: 'Zip code can only be digits'
                    }
                }
            },
            sale_status: {
                message: 'Please select if this is an estimate only or sold',
                validators: {
                    notEmpty: {
                        message: 'Please select a Sale Status'
                    },
                    stringLength: {
                        min: 1,
                        max: 1,
                        message: 'Please select a Sale Status'
                    },
                    
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'The email is required and cannot be empty'
                    },
                    regexp: {
                        regexp: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i,
                        message: 'The email provided is invalid'
                    }
                }
            },
        }
    }).on('success.form.bv', function(e){
        e.preventDefault();
        var $form = $(e.target),
        fv = $(e.target).data('bootstrapValidator');      
             $('#sigModal').modal('show');
             $('#final_alert').remove();
             $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                success: function(result) {
                    // ... Process the result ...
                }
            });
    });
    
});
</script>
<?php $this->load->view('estimates/modal_signature') ?>
<?php $this->load->view('estimates/confirm_delete') ?>
<?php $this->load->view('estimates/confirm_nav_away_final') ?>
<?php $this->load->view('footer.php') ?>