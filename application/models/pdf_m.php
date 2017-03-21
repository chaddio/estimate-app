<?php

class pdf_m extends CI_Model {
    public $fileDrop;
    
    public $totals;
    //added for public use by controller
    public $itemTable = 'estimates';
    public $detailTable = 'estimates_detail';
    public $statusTable = 'sale_statuses';
    
    public function __construct() {
        parent::__construct();
        $this->load->library('pdf');
        $this->fileDrop = FCPATH . 'filedrop/';
    }
    
    function getPDFDetail($hash){
        //$strQuery = "SELECT $this->detailTable.added,$this->detailTable.modified,$this->itemTable.active,$this->itemTable.userlevel,$this->detailTable.login_id,$this->itemTable.email,$this->detailTable.first_name,$this->detailTable.last_name,$this->detailTable.address,$this->detailTable.city,$this->detailTable.state,$this->detailTable.zip_code,CONCAT('(',left($this->detailTable.phone_number,3),') ',substring($this->detailTable.phone_number,4,3),'-',right($this->detailTable.phone_number,4)) as `phone_number` FROM " . $this->itemTable . "," . $this->detailTable . " WHERE $this->itemTable.id = $id and $this->itemTable.id = $this->detailTable.login_id";
        $this->db2->where($this->detailTable . '.pdf_hash',$hash);
        $this->db2->from($this->itemTable);
        
        $this->db2->join($this->detailTable, $this->detailTable . '.estimate_id = '. $this->itemTable . '.id');

        $query = $this->db2->get();
        //print_r($query);
        //exit;
        //creates object to be used outside of result array
        $array = $query->result();
        ///move object value from array
        $return = $array[0];
        return $return;
    }
    
    function getPDFItems(){
        $return = array();
        $this->load->model('estimates_m'); //TODO make more reusable, model loaded and methods called
        $this->load->library('estxpath');
        $type = $this->dataObj->unit_type;
        $displayType = $this->getUnitTypeDisplay($type);
        $obj = json_decode($this->dataObj->running_data);
        $rdArray = json_decode(json_encode($obj),true);//moving everything from multi-dimensional object into array
        $baseId = $rdArray[1]['id'];
        $level = $rdArray[1]['level'];
        $basePrice = $this->estimates_m->setDollarFormat($rdArray[1]['price']);
        
        //base system and brand option logic
        if(@$rdArray[2]['id'] != $baseId){
            $brandId = $rdArray[2]['id'];
            $brandPrice = $this->estimates_m->setDollarFormat($rdArray[2]['price']);
            $brandPath = '/estimatePricing/'. $type . 'Systems/' . $type . 'Systems' . $level . '/brandOptions/item[id = "' . $brandId . '"]/brand | ' . '/estimatePricing/'. $type . 'Systems/' . $type . 'Systems' . $level . '/brandOptions/item[id = "' . $brandId . '"]/description' ;
            $brandXml = $this->estimates_m->getXpathValue($brandPath);
            
        }
        $basePath = '/estimatePricing/'. $type . 'Systems/' . $type . 'Systems' . $level . '/baseSystem/brand |' . '/estimatePricing/'. $type . 'Systems/' . $type . 'Systems' . $level . '/baseSystem/description';
        $baseBrand = $this->estimates_m->getXpathValue($basePath);
        
        $return['baseSystem']['quantity'] = 1; 
        $return['baseSystem']['description'] = '2 Ton ' . $level . ", " . $displayType . (@$brandId ? ' system' : ' (' . $baseBrand[0] . ' - ' . $baseBrand[1] . ')');
        $return['baseSystem']['price'] = $basePrice;
        $return['baseSystem']['total'] = $basePrice;
        //set brand option after baseSystem based on description/brand mods
        if(@$rdArray[2]['id'] != $baseId){
            $return['brandOption']['quantity'] =  1;
            $return['brandOption']['description'] =  'Brand Option: ' . $brandXml[0] . ", " . $brandXml[1];
            $return['brandOption']['price'] = $brandPrice;
            $return['brandOption']['total'] = $brandPrice;
        }
        //upsize logic
        if(@$rdArray[3]['id'] != 'wlQdRMB2ig'){
            $sizeId = $rdArray[3]['id'];
            $sizePrice = $this->estimates_m->setDollarFormat($rdArray[3]['price']);
            $sizeXml =  $this->estimates_m->getXpathValue('/estimatePricing/sizeOptions/size[id = "' . $sizeId . '"]/description');
            $return['sizeOption']['quantity'] = 1;
            $return['sizeOption']['description'] = 'Upsize Option: ' . $sizeXml[0];
            $return['sizeOption']['price'] = $sizePrice;
            $return['sizeOption']['total'] = $sizePrice;
            
        }
        
        //thermostat upgrade
        if(@$rdArray[4]['price'] > '0.00'){
            $thermostatId = $rdArray[4]['id'];
            $thermostatPrice = $this->estimates_m->setDollarFormat($rdArray[4]['price']);
            $thermostatXml =  $this->estimates_m->getXpathValue('/estimatePricing/thermostats/item[id = "' . $thermostatId . '"]/description');
            $return['thermostatOption']['quantity'] = 1;
            $return['thermostatOption']['description'] = 'Thermostat: ' . $thermostatXml[0];
            $return['thermostatOption']['price'] = $thermostatPrice;
            $return['thermostatOption']['total'] = $thermostatPrice;
        }
        
        //Maintenance option
        if(@$rdArray[5]['price'] > '0.00'){
            $maintId = $rdArray[5]['id'];
            $maintPrice = $this->estimates_m->setDollarFormat($rdArray[5]['price']);
            $maintXml =  $this->estimates_m->getXpathValue('/estimatePricing/maintenancePlanOptions/item[id = "' . $maintId . '"]/description');
            $return['maintOption']['quantity'] = 1;
            $return['maintOption']['description'] = $maintXml[0];
            $return['maintOption']['price'] = $maintPrice;
            $return['maintOption']['total'] = $maintPrice;
        }
        
        if(count(@$rdArray[6]) >= 1){
            $i = 0;
            foreach($rdArray[6] as $key => $val){
                $miscId = $val['id'];
                $miscPrice = $this->estimates_m->setDollarFormat($val['price']);
                $miscXml =  $this->estimates_m->getXpathValue('/estimatePricing/miscellaneousOptions/item[id = "' . $miscId . '"]/description');
                $miscTotal = $this->estimates_m->setDollarFormat($val['price'] * $val['qty']);
                $return['miscOptions'][$i]['quantity'] = $val['qty'];
                $return['miscOptions'][$i]['description'] = 'Add-on / Misc: ' . $miscXml[0];
                $return['miscOptions'][$i]['price'] = $miscPrice;
                $return['miscOptions'][$i]['total'] = $miscTotal;
                $i++;
            }
            unset($i);
        }
        
        if(count(@$rdArray[8]) >= 1){
            $i = 0;
            foreach($rdArray[8] as $key => $val){
                $aeroId = $val['id'];
                $aeroPrice = $this->estimates_m->setDollarFormat($val['price']);
                $aeroXml =  $this->estimates_m->getXpathValue('/estimatePricing/atticSolutions/aeroseal/item[id = "' . $aeroId . '"]/description');
                $aeroTotal = $this->estimates_m->setDollarFormat($val['price'] * $val['qty']);
                $return['aerosealOptions'][$i]['quantity'] = $val['qty'];
                $return['aerosealOptions'][$i]['description'] = $aeroXml[0];
                $return['aerosealOptions'][$i]['price'] = $aeroPrice;
                $return['aerosealOptions'][$i]['total'] = $aeroTotal;
                $i++;
            }
            unset($i);
        }
        
        if(count(@$rdArray[9]) >= 1){
            $i = 0;
            foreach($rdArray[9] as $key => $val){
                $insulId = $val['id'];
                $insulPrice = $this->estimates_m->setDollarFormat($val['price']);
                $insulXml =  $this->estimates_m->getXpathValue('/estimatePricing/atticSolutions/additionalAtticInsulation/item[id = "' . $insulId . '"]/description');
                $insulTotal = $this->estimates_m->setDollarFormat($val['price'] * $val['qty']);
                $return['insulationOptions'][$i]['quantity'] = $val['qty'];
                if($insulId == 'RgdPXyz5w4'){
                    $return['insulationOptions'][$i]['description'] = $insulXml[0];
                }else{
                    $return['insulationOptions'][$i]['description'] = $val['qty'] . ' sq. ft. - ' . $insulXml[0];
                }
                $return['insulationOptions'][$i]['price'] = $insulPrice;
                $return['insulationOptions'][$i]['total'] = $insulTotal;
                $i++;
            }
            unset($i);
        }
        
        //financing and payment options 
        if(@$rdArray[7]['id'] != 'YspSETu8KQ'){
            $financeId = $rdArray[7]['id'];
            $financePrice = $this->estimates_m->setDollarFormat($rdArray[7]['price']);
            $financeXml =  $this->estimates_m->getXpathValue('/estimatePricing/financingOptions/item[id = "' . $financeId . '"]/description');
            
            $paymentXml = $this->estimates_m->getXpathValue('/estimatePricing/financingOptions/item[id = "' . $financeId . '"]/calcPayment | /estimatePricing/financingOptions/item[id = "' . $financeId . '"]/extra');
            $paymentExtra = $paymentXml[1]; //textual info maybe to be used
            $payment = $this->estimates_m->setDollarFormat($this->estimates_m->getPayment($this->estimates_m->getRunningTotal($this->dataObj->estimate_id,false), $paymentXml[0]));
            
            $return['financeOption']['quantity'] = 1;
            $return['financeOption']['description'] = 'Financing: ' .$financeXml[0] ;
            $return['financeOption']['price'] = $financePrice;
            $return['financeOption']['total'] = $financePrice;
            $return['financeOption']['extra'] = 'Monthly Pymt. info: ' . $paymentExtra;
            
            
        }
        
        $total = $this->estimates_m->getRunningTotal($this->dataObj->estimate_id,false);
        if(@$rdArray[10] && @$rdArray[10]['price'] <= 0){
            
            $this->totals['subtotalValue'] = $this->estimates_m->setDollarFormat($total + preg_replace('/\-/','', $rdArray[10]['price']));
            $this->totals['promoValue'] = preg_replace('/\-/','',$this->estimates_m->setDollarFormat($rdArray[10]['price']));
            $this->totals['promoCode'] = $this->getPDFPromo($rdArray[10]['id']);
            //$return['promoVisibility'] = $return['subtotalVisibility'] = '';
        }
        $this->totals['total'] = $this->estimates_m->setDollarFormat($total);
        if(@$paymentExtra){$this->totals['paymentExtra'] = $paymentExtra;}
        if(@$payment){$this->totals['payment'] = $payment;}
        
        
        return $return;
    }
    
    public function getPDFPromo($promoId){
        $allcodes = $this->estimates_m->getPromoCodes();
        foreach ($allcodes as $key => $val){
            if($promoId == $val['id']){
                return $val['promo_code'];
            }
        }
    }
    
    public function objectToArray($obj){
        if (is_object($obj)) $obj = (array)$obj;
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = $this->objectToArray($val);
            }
        } else {
            $new = $obj;
        }
        
        return $new;
    }
    
    function getPDFField($id,$field){
        //$strQuery = "SELECT $this->detailTable.added,$this->detailTable.modified,$this->itemTable.active,$this->itemTable.userlevel,$this->detailTable.login_id,$this->itemTable.email,$this->detailTable.first_name,$this->detailTable.last_name,$this->detailTable.address,$this->detailTable.city,$this->detailTable.state,$this->detailTable.zip_code,CONCAT('(',left($this->detailTable.phone_number,3),') ',substring($this->detailTable.phone_number,4,3),'-',right($this->detailTable.phone_number,4)) as `phone_number` FROM " . $this->itemTable . "," . $this->detailTable . " WHERE $this->itemTable.id = $id and $this->itemTable.id = $this->detailTable.login_id";
        $this->db2->from($this->detailTable);
        $this->db2->where('estimate_id', $id);
        $query = $this->db2->get()->result();
        //move single item to object from array
        $return = $query[0];
        return $return->$field;
    }
    
    /**
     * Send new user email with the optional reActivation (skipping registration) and 
     * sending a different message body
     * @param bool $reActivate 
     */
    private function _sendSetNewPDFEmail($email, $reActivate = FALSE){
        $this->load->library('email');
        $passwd = $this->_userPasswd;
        $config['wrapchars'] = '130';
        
        $this->email->initialize($config);
        $coAndApp = $this->config->item('company', 'config_app') . ' '  . $this->config->item('app_title', 'config_app');
        $domain = $_SERVER['HTTP_HOST'];
        $this->email->from('noreply@' . $domain, $coAndApp);
        $this->email->reply_to('noreply@' . $domain, $coAndApp);
        $this->email->to($email); 
        //$this->email->cc('another@another-example.com'); 
        //$this->email->bcc('them@their-example.com');   
        $this->email->subject($coAndApp);
        $site = base_url();
        //get string message from _getUserEmailMessage
        if($reActivate){
            $logPrefix = "Reactivation of user email sent to ";
            $this->email->message( 'Welcome to  ' . $coAndApp . '. Please use the following credentials to login: ' . 
                                    "\n\nUsername: \t" . $email . "\nPassword: \t" . $passwd . "\n\nSite/Link: " . $site . "\n\n" .
                                    'The password provided is temporary, you will need to know this when logging in. ' .
                                    'If you have any problems logging in, please email webmaster@' . $domain);
        }else{
            $logPrefix = "New User email sent to ";
            $this->email->message( 'Welcome to  ' . $coAndApp . '. Please use the following credentials to complete setting up your account: ' . 
                                    "\n\nUsername: \t" . $email . "\nPassword: \t" . $passwd . "\n\nSite/Link: " . $site . "\n\n" .
                                    'The password provided is temporary, you will need to know it until you login and finish the registration process. ' .
                                    'If you have any problems logging in or completing your registration, please email webmaster@' . $domain);
        }
        $this->email->send();
        log_message('debug', $logPrefix . $email . ", initiated from " . $_SERVER['REMOTE_ADDR']);
        
    }
    
    function getSaleStatus($saleStatusId){
        $this->db2->from($this->statusTable);
        $this->db2->where('sale_status', $saleStatusId);
        $query = $this->db2->get()->result();
        //move single item to object from array
        $return = $query[0];
        return $return->name; 
    }
    
    function getUnitTypeDisplay($type){
        switch ($type){
            case 'splitHP':
                return 'Split H.P.';
            case 'splitGas':
                return 'Split Gas';
            case 'packagedHP':
                return 'Pac. H.P.';
            case 'packagedGas':
                return 'Pac. gas';
        }
    }
    
}
