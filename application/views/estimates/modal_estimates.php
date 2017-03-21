<!-- ****************************************************************** -->
<!--                        Search Modal Window                         -->
<!-- ****************************************************************** -->
<div class="modal fade" id="myModal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3>Search <?php echo ucwords(substr($class, 0, -1)) ?></h3>
            </div>
            <?php echo form_open($mFormAction, $attr) . "\n"?>
            <div class="modal-body">
                <fieldset>
                    <div class="form-group"><input type="text" class="form-control" name="full_name" value="<?php echo (isset($params) ? $params[0] : '');?>" id="full_name" placeholder="Name"></div>
                    <div class="form-group"><input type="text" class="form-control" name="email" value="<?php echo (isset($params) ? $params[1] : '');?>" id="email" placeholder="Email"></div>
                    <div class="form-group"><input type="text" maxlength="10" class="form-control" name="phone_number" id="phone_number" maxlength="10" value="<?php echo (isset($params) ? $params[2] : '');?>" placeholder="Phone Number - Digits Only"></div>
                    <div class="form-group"><input type="text" maxlength="10" class="form-control" name="zip_code" id="zip_code" maxlength="5" value="<?php echo (isset($params) ? $params[3] : '');?>" placeholder="Zip Code"></div>
                    <?php if($this->session->userdata('app_userlevel') < 3): ?>
                        <div class="form-group">
                            <select class="form-control" name="sales_person" id="sales_person" placeholder="Salesperson"/>
                                <option value="" selected="selected">Salesperson</option>
                                <?php foreach ($salespeople as $salesperson) :?>
                                    <option value="<?php echo $salesperson['id']?>"><?php echo $salesperson['email']?></option>    
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif ; ?>
                </fieldset>
            </div>
            <div class="modal-footer">
              <a href="#" class="btn btn-default" id="btnReset">Reset</a>
              <a href="javascript:void(0);" id="btnModalSubmit" onclick="document.getElementById('modal').submit();" class="btn btn-primary">Search</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>
  <!-- ****************************************************************** -->
  <!--                      New Estimate Modal                            -->
  <!-- ****************************************************************** -->
<div class="modal fade" id="myModal2" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3>New <?php echo ucwords(substr($class, 0, -1)) ?></h3>
            </div>
            <?php echo form_open($mFormAction2, $attr2) . "\n"?>
            <div class="modal-body">
                <fieldset>
                        <div class="form-group"><input type="email" class="form-control" name="email" value="" id="email" placeholder="Customer Email"></div>
                        <div class="form-group"><input type="number" class="form-control" maxlength="10" name="phone_number" value="" id="phone_number" placeholder="Phone Number (digits only)"></div>
                        <div class="form-group"><input type="text" class="form-control" name="zip_code" value="" id="zip_code" placeholder="Zip Code"></div>
                        <div class="form-group"><input type="text" class="form-control" name="first_name" value="" id="first_name" placeholder="First Name"></div>
                        <div class="form-group"><input type="text" class="form-control" name="last_name" value="" id="last_name" placeholder="Last Name"></div>
                  
                    <div class="form-group">
                        <select class="form-control" name="unit_type" id="unit_type" placeholder="Unit Type" />
                            <option value="" selected="selected">Unit Type - Please Select</option>
                            <option value="splitGas">Split - Gas </option>
                            <option value="splitHP">Split - H.P. </option>
                            <option value="packagedGas">Packaged - Gas</option>
                            <option value="packagedHP">Packaged - H.P.</option>
                            <?php //foreach ($userlevels as $key => $val) :?>
                                <!--<option value="<?php //echo $userlevels[$key]['userlevel']?>"><?php //echo $userlevels[$key]['name']?></option>-->
                            <?php //endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="row" value="<?php echo $row;?>" />
                    <input type="hidden" name="active" value="<?php echo $active;?>" />
                    <input type="hidden" name="orderby" value="<?php echo $orderby;?>" />
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="submit" href="#" id="btnModalSubmit" class="btn btn-primary">Next</button>
            </div>
            <?php echo form_close("\n");?>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#modal2').bootstrapValidator({
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
            phone_number: {
                message: 'Phone Number is not valid',
                validators: {
                    notEmpty: {
                        message: 'The phone number is required and cannot be empty'
                    },
                    stringLength: {
                        min: 10,
                        max: 10,
                        message: 'The phone number must be 10 characters long'
                    },
                    regexp: {
                        regexp: /^[0-9]+$/,
                        message: 'Phone number can only be digits'
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
            unit_type: {
                validators: {
                    notEmpty: {
                        message: 'Please select a Unit Type'
                    }
                    
                }
            }
        }
    });
});
</script>
<script>
$(function(){
    $('#myModal #btnReset').click(function(){
        $(':input','#modal')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');
    }); 
    
});
</script>
