<!-- ****************************************************************** -->
<!--                        Search Modal Window                         -->
<!-- ****************************************************************** -->
<div class="modal fade" id="myModal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></button>
                    <h3>Search <?php echo ucwords($class) ?></h3>
            </div>
            <?php echo form_open($mFormAction, $attr) . "\n"?>
            <div class="modal-body">
                <fieldset>
                    <div class="form-group"><input type="text" class="form-control" name="full_name" value="<?php echo (isset($params) ? $params[0] : '');?>" id="full_name" placeholder="Name"></div>
                    <div class="form-group"><input type="text" class="form-control" name="email" value="<?php echo (isset($params) ? $params[1] : '');?>" id="email" placeholder="Email"></div>
                    <div class="form-group"><input type="text" maxlength="10" class="form-control" name="phone_number" id="phone_number" value="<?php echo (isset($params) ? $params[2] : '');?>" placeholder="Phone Number - Digits Only"></div>
                    <div class="form-group"><input type="text" class="form-control" name="city" id="city" value="<?php echo (isset($params) ? $params[3] : '');?>"placeholder="City"></div>
                    <div class="form-group"><input type="text" class="form-control" name="zip_code" id="zip_code" value="<?php echo (isset($params) ? $params[4] : '');?>"placeholder="Zip Code"></div>
                </fieldset>
            </div>
            <div class="modal-footer">
              <a href="#" class="btn btn-default" id="btnReset" onclick="resetFields();">Reset</a>
              <a href="javascript:void(0);" id="btnModalSubmit" onclick="document.getElementById('modal').submit();" class="btn btn-primary">Search</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>
<!-- ****************************************************************** -->
<!--                      Add User Modal                                -->
<!-- ****************************************************************** -->
<div class="modal fade" id="myModal2" role="dialog" tabindex="-1" aria-labelledby="myModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></button>
                    <h3>New <?php echo ucwords(substr($class, 0, -1)) ?></h3>
            </div>
            <?php echo form_open($mFormAction2, $attr2) . "\n"?>
            <div class="modal-body">
                <div class="form-group"><input type="text" class="form-control" name="email" value="<?php echo $this->input->post('email');?>" id="email" placeholder="Email" /></div>
                <div class="form-group">
                    <select class="form-control" name="userlevel" id="userlevel" placeholder="Userlevel" />
                        <option value="" selected="selected">Userlevel - Please Select</option>
                        <?php foreach ($userlevels as $userlevel) :?>
                            <option value="<?php echo $userlevel['userlevel']?>"><?php echo $userlevel['name']?></option>    
                        <?php endforeach; ?>
                    </select>
                </div>
                <em>** login info will be emailed to user **</em>
                <input type="hidden" name="row" value="<?php echo $row;?>" />
                <input type="hidden" name="active" value="<?php echo $active;?>" />
                <input type="hidden" name="orderby" value="<?php echo $orderby;?>" />
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" id="btnModalSubmit2" onclick="document.getElementById('modal2').submit();" class="btn btn-primary">Submit</a>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>
<script>
/**
 $(document).ready(function() {
    $('#modal2').value('');
});*/
</script>
<script>
function resetFields(){
    $('#btnReset').click(function(){
       $(':input','#modal')
 .not(':button, :submit, :reset, :hidden')
 .val('')
 .removeAttr('checked')
 .removeAttr('selected');
    }); 
    
}
</script>