<div class="col-xs-12">
    <!-- base system -->
    <hr>
    <div class="col-xs-4">
        <p class="text-right"><strong>Base System:</strong>&nbsp;&nbsp;</p>
    </div>
    <div class="col-xs-8">
        <p><?php echo $items['baseSystem'] ?></p>             
    </div>
    
    <!-- brand option -->
    <?php if(@$items['brandOption']) : ?>
        <div class="col-xs-4">
            <p class="text-right"><strong>Brand Option:</strong>&nbsp;&nbsp;</p>
        </div>
        <div class="col-xs-8">    
            <p><?php echo $items['brandOption']; ?></p>
        </div> 
    <?php endif ; ?>
    
    <!--size option-->
    <?php if(@$items['sizeOption']) : ?>
        <div class="col-xs-4">
            <p class="text-right"><strong>Size Option:</strong>&nbsp;&nbsp;</p>
        </div>
        <div class="col-xs-8">
            <p><?php echo $items['sizeOption']; ?></p>
        </div>
    <?php endif ; ?>
    
    <!--thermostat option-->
    <?php if(@$items['thermostatOption']) : ?>
        <div class="col-xs-4">
            <p class="text-right"><strong>Thermostat Upgrade:</strong>&nbsp;&nbsp;</p>
        </div>
        <div class="col-xs-8">
            <p><?php echo $items['thermostatOption']; ?></p>
        </div>
    <?php endif ; ?>
    
    <!-- maintenance plan option -->
    <?php if(@$items['maintOption']) : ?>
        <div class="col-xs-4">
            <p class="text-right"><strong>Maintenance Plan:</strong>&nbsp;&nbsp;</p>
        </div>
        <div class="col-xs-8">
            <p><?php echo $items['maintOption']; ?></p>
        </div>
    <?php endif ; ?>
    
    <?php if(@$items['miscOption']) : ?>
        <div class="col-xs-4">
            <p class="text-right"><strong>Accessories / Add-ons:</strong>&nbsp;&nbsp;</p>
        </div>
        <div class="col-xs-8">
            <p><?php echo $items['miscOption']; ?></p>
        </div>
    <?php endif ; ?>
    
    <!-- financing options -->
    <?php if(@$items['financeOption']) : ?>
        <div class="col-xs-4">
            <p class="text-right"><strong>Financing Plan:</strong>&nbsp;&nbsp;</p>
        </div>
        <div class="col-xs-8">
            <p><?php echo $items['financeOption']; ?></p>
        </div>
    <?php endif ; ?>
    
    <!-- aeroseal options -->
    <?php if(@$items['aerosealOption']) : ?>
        <div class="col-xs-4">
            <p class="text-right"><strong>Aeroseal</strong><small>&reg;</small>&nbsp;<strong>:</strong>&nbsp;&nbsp;</p>
        </div>
        <div class="col-xs-8">
            <p><?php echo $items['aerosealOption']; ?></p>
        </div>
    <?php endif ; ?>
    
    <!-- insulation options -->
    <?php if(@$items['insulationOption']) : ?>
        <div class="col-xs-4">
            <p class="text-right"><strong>Insulation:</strong>&nbsp;&nbsp;</p>
        </div>
        <div class="col-xs-8">
            <p><?php echo $items['insulationOption']; ?></p>
        </div>
    <?php endif ; ?>
    
    <!--used for spacing -->
    <div class="col-xs-12"><p>&nbsp;</p></div>
    
    <?php echo form_open($formAction, $formAttr); ?>
    <!-- subtotal (total before discounts) hidden on load -->
    <div id="subTotalAmtDiv" <?php echo $items['subtotalVisibility'];?> class="form-group" >
        <label for="foo" class="col-xs-4 control-label">Subtotal:</label>
        <div class="col-xs-3">
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" class="form-control" id="subtotal" name="subtotal" readonly placeholder="Subtotal" value="<?php echo @$items['subtotalValue'];?>">      
            </div>  
        </div>
    </div>
    
    <!-- promo / discounts, hidden on load -->
    <div id="promoAmtDiv" <?php echo $items['promoVisibility'];?> class="form-group" >
        <label for="foo" class="col-xs-4 control-label">Promo / Discounts:</label>
        <div class="col-xs-3">
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" class="form-control" id="promotional" name="promotional" placeholder="Promotions" readonly value="<?php echo @$items['promoValue'];?>">      
            </div>  
        </div>
    </div>
    
    <!-- Estimate Total -->
    <div class="form-group">
        <label for="estimate_total" class="col-xs-4 control-label">Estimate Total:</label>
        <div class="col-xs-3">
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" class="form-control" id="estimate_total" name="estimate_total" placeholder="System Total" readonly value="<?php echo $items['total'];?>">
            </div>  
        </div>
        <div class="col-xs-1"><a data-toggle="modal" data-target="#promoModal" href="#" ><img class="promo-img" src="/assets/img/promo-ico.png" ></a></div>
    </div>
    
    <!--Payment / Month -->
    <?php if(isset($items['payment'])) : ?>
    <div class="form-group">
        <label for="foo" class="col-xs-4 control-label">Est. Payment/Month:</label>
        <div class="col-xs-3">
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input data-type="text" class="form-control" id="payment_amount" name="payment_amount" placeholder="Payments" readonly value="<?php echo $items['payment'];?>">
            </div>  
        </div>
        <div class="col-xs-5">
            <br><p class="small"><em><?php echo $items['paymentExtra']?></em></p>
        </div> 
    </div>
    <?php endif; ?>
    {button}
    <?php echo form_hidden('id', $eid); ?>
    <?php echo form_close();?>
</div>
<?php $this->load->view('estimates/confirm_confirm'); ?>
<?php $this->load->view('estimates/modal_promo'); ?>