<!-- ****************************************************************** -->
<!--                        Search Modal Window                         -->
<!-- ****************************************************************** -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3>Forgot Login</h3>
            </div>
            <div class="modal-body">
                Your login should be your email. <br>Please email <a href="mailto:<?php echo $this->config->item('admin_email', 'config_app')?>?subject=<?php echo $this->config->item('app_title', 'config_app')?>%20Login%20Info"><?php echo $this->config->item('admin_email', 'config_app')?></a> if you are unsure <br> what that is or contact an application administrator.
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-warning" data-dismiss="modal">Close</a>&nbsp;&nbsp;
            </div>
        </div>
    </div>
</div>
