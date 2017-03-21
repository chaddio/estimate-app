<?php

class estimates_m extends CI_Model {
    //private $_dfltInputClass = 'span4';
    //private $_dfltChkClass = '';
    //private $_dfltAppPages = '1';
    private $_estXML = 'assets/xml/estimatePricing.xml';
    protected $_userPasswd;
    protected $_userId;
    protected $_pdfHash;
    public $searchCount;
    public $results;
    public $isAdmin;
    public $locked = FALSE;
    public $row;
    public $msg;
    public $orderby;
    //added for public use by controller
    public $itemTable = 'estimates';
    public $detailTable = 'estimates_detail';
    public $agentTable = 'profile';
    public $user_detail;
    public $stage; //estimate stage/step
    public $level; //basic, deluxe or premier
    public $type;
    public $radioDefault;
    public $qty;
    public $pdfInfo;
    public $pdfLocation;
    
    public function __construct() {
        parent::__construct();
    }
    
    function getAllEstimates(){
        $query = $this->db1->get($this->itemTable);
        if($query->num_rows() > 0){
            // return result set as an associative array
            return $query->result_array();
        }
    }
    
    
    /**
     * 
     * @param type $field
     * @param type $param
     * @param type $limit
     * @param type $offset
     * @return type
     */
    function getEstimatesWhere($field,$param,$limit=100,$offset=0){
        $where = '';
        if(is_array($field) && is_array($param)){
            $i = count($field);
            foreach($field as $key => $value){
                if($i == 1){
                    $where .= $field[$key] . " = " . $param[$key]; 
                }else{
                    
                    $where .= $field[$key] . " = " . $param[$key] . ' and ';
                }
            $i--;
            }
        }else{
            $where .= $field . " = " . $param;
        }
        $strQuery = "SELECT $this->itemTable.id, $this->itemTable.email,CONCAT($this->itemTable.first_name,' ',$this->itemTable.last_name) as `full_name`,$this->itemTable.phone_number,$this->itemTable.added,$this->itemTable.sales_person,$this->itemTable.sale_status,$this->itemTable.completed,$this->itemTable.closed FROM " . $this->itemTable . " WHERE $where ORDER BY $this->orderby LIMIT $offset,$limit";
        $query = $this->db2->query($strQuery);
        return $query->result_array();
    }
    
    public function getEstimateBasic($id){
        $this->db2->from($this->itemTable);
        $this->db2->where('id', $id);
        $query = $this->db2->get()->result();
        $obj = $query[0]; ///convert to object
        
        
        if($obj){
            $return = array();
            foreach($obj as $key => $val){
                $return[$key] = $obj->$key;
            }
            return $return;
        }else{
            return 'No results Found';
        }
        
    }
    /**
     * returns formatted html in a single string
     * TODO needs to be more dynamicly built instead of a very static switch
     * @param int $stage
     * @return string 
     */
    function getBreadCrumb($stage, $type, $level){
        switch ($stage){
            case 2:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . 'estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li class="active">Brand Options</li>' . "\n";
            break;
            case 3:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . 'estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li><a title="Second step which is the brand options available ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');" href="#">Brand Options</a></li>' . "\n";
                $return .= '<li class="active">Size Options</li>' . "\n";
            break;
            case 4:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier (' . ucwords($type) . ') system" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . '/estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li><a title="Second step which is the brand options available ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 2) . '\');" href="#">Brand Options</a></li>' . "\n";
                $return .= '<li><a title="Third step which is the size / upsize options ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');" href="#">Size Options</a></li>' . "\n";
                $return .= '<li class="active">Thermostat Options</li>' . "\n";
            break;
            case 5:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . 'estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li><a title="Second step which is the brand options available ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 3) . '\');" href="#">Brand Options</a></li>' . "\n";
                $return .= '<li><a title="Third step which is the size / upsize options ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 2) . '\');" href="#">Size Options</a></li>' . "\n";
                $return .= '<li><a title="Fourth step which is the thermostat selection ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');" href="#">Thermostat Options</a></li>' . "\n";
                $return .= '<li class="active">Maintenance Plans</li>' . "\n";
            break;
            case 6:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . 'estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li><a title="Second step which is the brand options available ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 4) . '\');" href="#">Brand</a></li>' . "\n";
                $return .= '<li><a title="Third step which is the size / upsize options ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 3) . '\');" href="#">Size</a></li>' . "\n";
                $return .= '<li><a title="Fourth step which is the thermostat selection ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 2) . '\');" href="#">Thermostat</a></li>' . "\n";
                $return .= '<li><a title="Fifth step which is the maintenance plan selection ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');" href="#">Maintenance Plans</a></li>' . "\n";
                $return .= '<li class="active">Accessories</li>' . "\n";
            break;
            case 7:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . 'estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li><a title="Second step which is the brand options available (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 5) . '\');" href="#">Brand</a></li>' . "\n";
                $return .= '<li><a title="Third step which is the size / upsize options (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 4) . '\');" href="#">Size</a></li>' . "\n";
                $return .= '<li><a title="Fourth step which is the thermostat selection (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 3) . '\');" href="#">Thermostat</a></li>' . "\n";
                $return .= '<li><a title="Fifth step which is the maintenance plan selection ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 2) . '\');" href="#">Maint. Plans</a></li>' . "\n";
                $return .= '<li><a title="Sixth step which is accessories ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ') system" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');" href="#">Accessories</a></li>' . "\n";
                $return .= '<li class="active">Financing</li>' . "\n";
            break;
            case 8:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier system('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . 'estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li><a title="Second step which is the brand options available (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 6) . '\');" href="#">Brand</a></li>' . "\n";
                $return .= '<li><a title="Third step which is the size / upsize options (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 5) . '\');" href="#">Size</a></li>' . "\n";
                $return .= '<li><a title="Fourth step which is the thermostat selection (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 4) . '\');" href="#">Thermostat</a></li>' . "\n";
                $return .= '<li><a title="Fifth step which is the maintenance plan selection ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 3) . '\');" href="#">Maint. Plans</a></li>' . "\n";
                $return .= '<li><a title="Sixth step which is accessories ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 2) . '\');" href="#">Accessories</a></li>' . "\n";
                $return .= '<li><a title="Seventh step which is financing ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');" href="#">Financing</a></li>' . "\n";
                $return .= '<li class="active">Aeroseal</li>' . "\n";
            break;
            case 9:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier system('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . 'estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li><a title="Second step which is the brand options available (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 7) . '\');" href="#">Brand</a></li>' . "\n";
                $return .= '<li><a title="Third step which is the size / upsize options (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 6) . '\');" href="#">Size</a></li>' . "\n";
                $return .= '<li><a title="Fourth step which is the thermostat selection (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 5) . '\');" href="#">Thermostat</a></li>' . "\n";
                $return .= '<li><a title="Fifth step which is the maintenance plan selection ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 4) . '\');" href="#">Maint. Plans</a></li>' . "\n";
                $return .= '<li><a title="Sixth step which is accessories ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 3) . '\');" href="#">Accessories</a></li>' . "\n";
                $return .= '<li><a title="Seventh step which is financing ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 2) . '\');" href="#">Financing</a></li>' . "\n";
                $return .= '<li><a title="Eight step which is Aeroseal ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');" href="#">Aeroseal</a></li>' . "\n";
                $return .= '<li class="active">Insulation</li>' . "\n";
            break;
            case 10:
                $return = '<li><a title="This is the first selection of Basic, Deluxe or Premier system('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" data-toggle="modal" href="#" data-target="#confirm-stage1" data-href="' . base_url() . 'estimates/estimate/1/resume">Base System</a></li>' . "\n";
                $return .= '<li><a title="Second step which is the brand options available (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 8) . '\');" href="#">Brand</a></li>' . "\n";
                $return .= '<li><a title="Third step which is the size / upsize options (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 7) . '\');" href="#">Size</a></li>' . "\n";
                $return .= '<li><a title="Fourth step which is the thermostat selection (' . ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 6) . '\');" href="#">Thermostat</a></li>' . "\n";
                $return .= '<li><a title="Fifth step which is the maintenance plan selection ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 5) . '\');" href="#">Maint. Plans</a></li>' . "\n";
                $return .= '<li><a title="Sixth step which is accessories ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 4) . '\');" href="#">Accessories</a></li>' . "\n";
                $return .= '<li><a title="Seventh step which is financing ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 3) . '\');" href="#">Financing</a></li>' . "\n";
                $return .= '<li><a title="Eight step which is Aeroseal ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 2) . '\');" href="#">Aeroseal</a></li>' . "\n";
                $return .= '<li><a title="Ninth step which is insulation ('. ucwords($level) . ', ' . $this->getUnitTypeDisplay($type) . ' selected)" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');" href="#">Insulation</a></li>' . "\n";
                $return .= '<li class="active">Summary</li>' . "\n";
            break;
            case 1:
            default;
                $return = '<li title="Choose a base system type (' . $this->getUnitTypeDisplay($type) . ')" class="active">Base System </li>' . "\n";
            break;
        }
        
        return $return;
    }
    /**
     * 
     * @param int $eid
     * @param bool $commas
     * @return string
     */
    public function getRunningTotal($eid,$commas = TRUE){
        $estInfo = $this->getEstimateBasic($eid);
        $total = '0.00';
        if($estInfo['running_data'] == ''){
            return $total;
        }else{
            //using all objects, no arrays in my json
            $objData = json_decode($estInfo['running_data']);
            //print_r($arrayData);
            //exit;
            foreach($objData as $key => $value){
                if($key == '6' || $key == '8' || $key == '9'){
                    foreach($objData->$key as $value){
                        if(@$value->qty){
                            $total = $total + ($value->price * $value->qty);
                        }else{
                            $total = ($total + $value->price);
                        }
                    }
                }else{
                    $total = ($total + $value->price);
                }
            }
            return $this->setDollarFormat($total,$commas);
        }
        
    }
    
    public function setDollarFormat($amount,$commas = TRUE){
//        if(preg_match('/\.[0-9]{2}$/', $amount)){
//                return $amount;
//            }elseif(preg_match('/\.[0-9]{1}$/', $amount)){
//                return $amount . '0';
//            }else{    
//                return $amount . '.00';
//            }
//            
        if($commas){
            return number_format ( (float) $amount , 2 ,".","," );
        }else{
            return sprintf('%0.2f', $amount);
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
    
    public function setQuantityForItem($eid, $selection){
        $estInfo = $this->getEstimateBasic($eid);
        $objData = json_decode($estInfo['running_data']);
        $objData->$selection['stage']->$selection['chkid'] = (object) array('stage' => $selection['stage']);
        $objData->$selection['stage']->$selection['chkid']->id = $selection['id'];
        $objData->$selection['stage']->$selection['chkid']->price = $selection['price'];
        $objData->$selection['stage']->$selection['chkid']->qty = $selection['qty'];
        return $this->setEstimateUpdateFields($eid, array('running_data' => json_encode($objData)));
    }
    
    public function setPriceForItem($eid, $selection){
        $estInfo = $this->getEstimateBasic($eid);
        $objData = json_decode($estInfo['running_data']);
        $objData->$selection['stage']->$selection['chkid']->price = $selection['price'];
        return $this->setEstimateUpdateFields($eid, array('running_data' => json_encode($objData)));
    }
    
    public function setPromo($eid, $selection){
        $estInfo = $this->getEstimateBasic($eid);
        $objData = json_decode($estInfo['running_data']);
        $objData->$selection['stage'] = new stdClass();
        $objData->$selection['stage']->stage = $selection['stage'];
        $objData->$selection['stage']->id = $selection['id'];
        $objData->$selection['stage']->price = $selection['price'];
        return $this->setEstimateUpdateFields($eid, array('running_data' => json_encode($objData),'resume' => '10'));
    }
    
    public function setRunningTotal($eid, $selection){
        $estInfo = $this->getEstimateBasic($eid);
        
        if($estInfo['running_data'] == ''|| $selection['stage'] == '1'){
            $json = json_encode(array($selection['stage'] => $selection));
            $return = $this->setEstimateUpdateFields($eid, array('running_data' => $json));
            usleep(500000); //sleep a half second
            return $return;
        }
        $objData = json_decode($estInfo['running_data'],true);
        if(($selection['stage'] == '6' || $selection['stage'] == '8'|| $selection['stage'] == '9') && isset($objData->$selection['stage'])){
            foreach($objData[$selection['stage']] as $key => $val){
                if($key == $selection['chkid']){
                    unset($objData[$selection['stage']][$key]);
                    $set = TRUE;
                }else{
                    $objData[$selection['stage']][$key] = (object) array('stage' => $selection['stage']);
                    $objData[$selection['stage']][$key]['id'] = $val->id;
                    $objData[$selection['stage']][$key]['price'] = $val->price;
                    $objData[$selection['stage']][$key]['qty'] = (!isset($val->qty) ? '1' : $val->qty);
                }
                
                
            }
        }else{
            foreach($objData as $key => $val){
                if(@$objData[$key]['stage'] == $selection['stage']){
                     $objData[$key]['stage'] = $selection['stage'];
                     $objData[$key]['id'] = $selection['id'];
                     $objData[$key]['price'] = $selection['price'];
                     $set = TRUE;
                }
            }
        }
        if(!@$set){
            if($selection['stage'] == '6'||$selection['stage'] == '8'||$selection['stage'] == '9'){
                $objData[$selection['stage']][$selection['chkid']] = array('stage' => $selection['stage']);
                $objData[$selection['stage']][$selection['chkid']]['id'] = $selection['id'];
                $objData[$selection['stage']][$selection['chkid']]['price'] = $selection['price'];
                $objData[$selection['stage']][$selection['chkid']]['qty'] = (!isset($selection['qty']) ? '1' : $selection['qty']);
                
            }else{
//                print_r($objData->$selection['stage']);
//                exit;
                //
                //$objData->$selection['stage'] = new stdClass();
                $objData[$selection['stage']]['stage'] = $selection['stage'];
                $objData[$selection['stage']]['id'] = $selection['id'];
                $objData[$selection['stage']]['price'] = $selection['price'];
                if(@$selection['level']){
                    $objData[$selection['level']]['price'] = $selection['level'];
                }
            }
        }
        //set a resume location for easy jump to stage
        $array = (array) $objData;
        $i = 1;
        $count = count($array);
        while ($i < $count){
            array_shift($array);
            $i++;
        }
        $lastStage = key($array);
        
        //return true if successful
        return $this->setEstimateUpdateFields($eid, array('running_data' => json_encode($objData),'resume' => $lastStage));
        
    }
    /**
     * 
     * @param int $stage
     * @return string
     */
    function getLegend($stage){
        switch($stage){
            case 1:
                $return = 'Choose a Base System';
            break;
            case 2:
                $return = 'Brand Options';
            break;
            case 3:
                $return = 'Size Options';
            break;
            case 4:
                $return = 'Thermostat Options';
            break;
            case 5:
                $return = 'Maintenance Plan Options';
            break;
            case 6:
                $return = 'Accessories - Install Requirements - Upgrade Items';
            break;
            case 7:
                $return = 'Financing Options';
            break;
            case 8:
                $return = 'Attic Solutions - Aeroseal<small>&reg;</small>';
            break;
            case 9:
                $return = 'Attic Solutions - Insulation';
            break;
            case 10:
                $return = 'Summary and Confirmation';
            break;
            default:
                $return = 'Still Working on this step - Stage: ' . $stage;
            break;
        }
        return $return;
    }
    
    function getBasePrices($type){
        $return = array();
        $return['basic'] = $this->getXML($type . 'Systems->' . $type . 'SystemsBasic', 'baseSystem');
        $return['deluxe'] = $this->getXML($type . 'Systems->' . $type . 'SystemsDeluxe', 'baseSystem');
        $return['premier'] = $this->getXML($type . 'Systems->' . $type . 'SystemsPremier', 'baseSystem');
        return $return;
    }
    
    function getEstimateDetail($id){
        //$strQuery = "SELECT $this->detailTable.added,$this->detailTable.modified,$this->itemTable.active,$this->itemTable.userlevel,$this->detailTable.login_id,$this->itemTable.email,$this->detailTable.first_name,$this->detailTable.last_name,$this->detailTable.address,$this->detailTable.city,$this->detailTable.state,$this->detailTable.zip_code,CONCAT('(',left($this->detailTable.phone_number,3),') ',substring($this->detailTable.phone_number,4,3),'-',right($this->detailTable.phone_number,4)) as `phone_number` FROM " . $this->itemTable . "," . $this->detailTable . " WHERE $this->itemTable.id = $id and $this->itemTable.id = $this->detailTable.login_id";
        $this->db2->where('id',$id);
        $query = $this->db2->get($this->itemTable);
        $estimate_detail = $query->result();
        //creates object to be used outside of result array
        $this->estimate_detail = $estimate_detail[0];
        return $query->result_array();
    }
    
    function getEstimateDetailField($id,$field){
        //$strQuery = "SELECT $this->detailTable.added,$this->detailTable.modified,$this->itemTable.active,$this->itemTable.userlevel,$this->detailTable.login_id,$this->itemTable.email,$this->detailTable.first_name,$this->detailTable.last_name,$this->detailTable.address,$this->detailTable.city,$this->detailTable.state,$this->detailTable.zip_code,CONCAT('(',left($this->detailTable.phone_number,3),') ',substring($this->detailTable.phone_number,4,3),'-',right($this->detailTable.phone_number,4)) as `phone_number` FROM " . $this->itemTable . "," . $this->detailTable . " WHERE $this->itemTable.id = $id and $this->itemTable.id = $this->detailTable.login_id";
        $this->db2->from($this->detailTable);
        $this->db2->where('estimate_id', $id);
        $query = $this->db2->get()->result();
        if(count($query) == 0 && $field == 'pdf_hash'){
            return 0;
        }else{
            $return = $query[0];
            return $return->$field;
        }
        //move single item to object from array
        
    }
    
    function getEstimateField($id,$field){
        //$strQuery = "SELECT $this->detailTable.added,$this->detailTable.modified,$this->itemTable.active,$this->itemTable.userlevel,$this->detailTable.login_id,$this->itemTable.email,$this->detailTable.first_name,$this->detailTable.last_name,$this->detailTable.address,$this->detailTable.city,$this->detailTable.state,$this->detailTable.zip_code,CONCAT('(',left($this->detailTable.phone_number,3),') ',substring($this->detailTable.phone_number,4,3),'-',right($this->detailTable.phone_number,4)) as `phone_number` FROM " . $this->itemTable . "," . $this->detailTable . " WHERE $this->itemTable.id = $id and $this->itemTable.id = $this->detailTable.login_id";
        $this->db2->from($this->itemTable);
        $this->db2->where('id', $id);
        $query = $this->db2->get()->result();
        //move single item to object from array
        $return = $query[0];
        return $return->$field;
    }
    
    function getAllSalesReps(){
        $this->db1->order_by('userlevel','desc');
        $this->db1->order_by('email','asc');
        $query = $this->db1->get('login');
        return $query->result_array();
    }
    
    function getSalesRep($login_id){
        $this->db1->from($this->agentTable);
        $this->db1->where('login_id', $login_id);
        $query = $this->db1->get()->result();
        //move single item to object from array
        $return = $query[0];
        return $return->first_name . ' ' . $return->last_name[0] . '.'; 
    }
    
    function getDetailsWhere($field,$param,$table){
        $this->db1->from($table);
        if(is_array($field) && is_array($param)){
            foreach($field as $key => $value){
                $this->db1->where($field[$key],$param[$key]);
            }
        }else{
            $this->db1->where($field,$param);
        }
        $query = $this->db1->get()->result();
        // return result as object
        return $query[0];
    }
    /**
     * @$field
     *
     * must have $field and $param with the same number of array elements if sending as arrays
     * also can send params as strings
     */
    function getEstimateListLike($field,$param,$limit=100,$offset=0){
        $like = '';
        if(is_array($field) && is_array($param)){
            $i = count($field);
            foreach($field as $key => $value){
                
                if($i == 1){
                    $like .= $field[$key] . ' LIKE "%' . $param[$key] . '%"'; 
                }else{
                    
                    $like .= $field[$key] . ' LIKE "%' . $param[$key] . '%" and ';
                }
                $i--;
                //$this->db1->like($field[$key],$param[$key]);
            }
        }else{
            $this->db2->like($field,$param);
        }
        if(!$this->session->userdata('app_userlevel') < 3){
            $like .= " and added_by = " . $this->session->userdata('login_id');
        }
        $limit = " LIMIT " . $offset . ',' . $limit;
        $strQuery = "SELECT $this->itemTable.id,$this->itemTable.active,$this->itemTable.sales_person,$this->itemTable.email,CONCAT($this->itemTable.first_name,' ',$this->itemTable.last_name) as `full_name`,$this->itemTable.phone_number,$this->itemTable.added_by,$this->itemTable.sale_status,$this->itemTable.completed,$this->itemTable.closed FROM " . $this->itemTable . " WHERE $like";
        $query = $this->db2->query($strQuery . ' ' . $limit);
        //$query = $this->db1->get($this->itemTable,$limit,$offset);
        // return result set as an associative array
        $cntQuery = $this->db2->query($strQuery);
        $this->searchCount = count($cntQuery->result_array());
        return $query->result_array();
    }
    
    function getAllUserCount(){
        return $this->db1->count_all($this->itemTable);
    }
    /**
     * 
     * @param array or string $field
     * @param array or string $param
     * @return int
     */
    function getEstimateCountWhere($field='', $param=''){
        if(is_array($field) && is_array($param)){
            foreach($field as $key => $value){
                $this->db2->where($field[$key],$param[$key]);
            }
        }elseif($field == '' && $param == ''){
            return $this->db2->count_all($this->itemTable);
        }else{
            $this->db2->where($field,$param);
        }
        $query = $this->db2->get($this->itemTable);
        
        return $query->num_rows();
    }
    /**
     * returns html string converted from xml then iterates through targetNode
     * @param string $contNode this is the xml/object hierachy to go to in the string
     * @param string $targetNode this is the oject/node to iterate through one step down from containing node
     * @return string 
     */
    public function getXML($contNode,$targetNode){
        $xml = simplexml_load_file($this->_estXML);
        $val = null;
        $path = preg_split('/->/', $contNode);
        $node = $xml;
        while (($prop = array_shift($path)) !== null) {
            if (!is_object($xml) || !property_exists($node, $prop)) {
                $val = null;
                break;
            }
            $val = $node->$prop;
            $node = $node->$prop;
        }
        switch ($targetNode){
            case "item":
                if($contNode === 'miscellaneousOptions'|| $contNode === 'atticSolutions->aeroseal' || $contNode === 'atticSolutions->additionalAtticInsulation'){
                    $return = $this->_getTableXMLReturn($val, $targetNode, FALSE);
                }else{
                    $return = $this->_getTableXMLReturn($val, $targetNode);
                }
            break;
            case "size":
                $return = $this->_getSizesTableXMLReturn($val, $targetNode);
            break;
            case "baseSystem":
            default:
                $return = $this->_getSingleXMLReturn($val, $targetNode);
            break;
        }
        return $return;
    }
    public function getTHs($stage){
        switch($stage){
            case 2:
                return "<th>Select</th>\n<th>Brand</th>\n<th>Description</th>\n<th>Amount</th>\n";
            case 3:
            default:
                return "<th>Select</th>\n<th>Description</th>\n<th>Amount</th>\n";
        }
    }
    protected function _getSingleXMLReturn($xmlobject, $targetNode){
        $return = array();
        foreach($xmlobject->$targetNode as $row){
            $return['id'] = $row->id;
            $return['price'] = $row->price;
        }
        return $return;
    }
    /**
     * TOD0, combine this logic into _getTableXMLReturn...also _getTableXMLReturn needs to be refactored to remove so many nested if/etc
     * @param object $xmlobject
     * @param string $targetNode
     */
    protected function _getSizesTableXMLReturn($xmlobject, $targetNode){
        $result = '';
        $this->load->model('form_m');
        $this->load->helper('form');
        $this->load->library('estxpath');
        $i = 0;
        $estInfo = $this->getEstimateBasic($this->input->cookie('eid'));
        $json = json_decode($estInfo['running_data']);
        $uStage = 2;
        $unitId = $json->$uStage->id;
        $upsizeVal =  $this->getXpathValue('//item[id = "' . $unitId . '"]/upsizePrices');
        $upsizePrices = explode('|', $upsizeVal[0]);
        //print_r($upsizePrices);
        foreach($xmlobject->$targetNode as $row){
            if($row->active == '1'){
                $result .= '<tr>';
                if($this->_setCheckedVal($row->id)){
                    $checked = TRUE;
                }
                switch ($row->price){
                    case '0.00':
           
                        $this->radioDefault = $this->estimates_m->stage . '|' . $row->id . '|' . $row->price;
                    break;
                    case 'NULL':
                        $row->price = $this->setDollarFormat($upsizePrices[$i],FALSE);
                        $i++;
                    break;
                    
                }
                $result .= '<td>' . form_radio($this->form_m->getRadioArr($this->estimates_m->stage,$this->estimates_m->stage . '|' . $row->id . '|' . $row->price,'',@$checked)) . '</td>';
                unset($checked);
                $result .= '<td>' . $row->description . '</td>';
                $result .= '<td>' . (@$row->compdesc ? '<em>' . $row->compdesc . '</em>' : '$' . $row->price) . '</td>';
                $result .= '</tr>';
            }   
        }
        return $result;
    }
    
    protected function _getTableXMLReturn($xmlobject, $targetNode, $radio = TRUE){
        $result = '';
        $this->load->model('form_m');
        $this->load->helper('form');
        $i = 0;
        foreach($xmlobject->$targetNode as $row){
            if($row->active == '1'){
                $result .= (@$row->qty ? '<tr class="success">' : '<tr>');
                if($radio === FALSE){
                    $checked = $this->_setCheckedVal($row->id);
                    if(@$this->price && $checked){
                        $row->price = $this->price;
                    }
                    if(@$row->qty && $checked){
                        $qty = $this->qty;
                    }
                    $result .= '<td> <div class="form-inline form-group">' . form_checkbox($this->form_m->getChkArr($this->estimates_m->stage . '|' . $i,'cb' .$this->estimates_m->stage . '_' . $i . '|' . $row->id . '|' . (@$checked ? $this->price : $row->price),'',@$checked)) . 
                                ($row->qty ? '&nbsp;<span class="hideInput" style="' . (isset($qty) ? '">': 'display: none">') . 
                                form_number($this->form_m->getInpArr($this->estimates_m->stage . '_' . $i . '|' . $row->id . '|' . $row->price,(isset($qty) ? $qty : '1'),'qty','qtyInput form-control input-sm',FALSE,'cb' . $this->estimates_m->stage . '_' . $i . '|' . $row->id . '|' . $row->price,array('maxlength' => '4', 'style' => 'width: 60px;'))) . '&nbsp;<small>Qty</small></span>': '') .   '</div></td>';
                    unset($checked);
                    unset($qty);
                }else{
                    if($this->_setCheckedVal($row->id)){
                        $checked = TRUE;
                    }
                    if(@$row->radioDefault){
                        $this->radioDefault = $this->estimates_m->stage . '|' . $row->id . '|' . $row->price;
                    }
                    $result .= '<td>' . form_radio($this->form_m->getRadioArr($this->estimates_m->stage,$this->estimates_m->stage . '|' . $row->id . '|' . $row->price,'',@$checked)) . '</td>';
                    unset($checked);
                }
                if(@$row->brand){
                    $result .= '<td>' . $row->brand . '</td>';
                }
                $result .= '<td>' . $row->description . '</td>';
                if($row->price == '0.00'){
                    if(@$row->modifyPrice){
                        $result .= '<td><div class="text-success" id="priceDiv' . $i . '"><em>'. $row->compdesc  . '</em></div></td>';
                    }else{
                        $result .= '<td><em>'. $row->compdesc  . '</em></td>';
                    }     
                }else{
                    if(@$row->modifyPrice){
                        $result .= '<td><div class="text-success" id="priceDiv' . $i . '">$' . $row->price . '</div></td>';
                    }else{
                        $result .= '<td>$' . $row->price . '</td>';
                    }
                    
                }   
                $result .= '</tr>';
                $i++;
            }
        }
        return $result;
        
    }
    
    private function _setCheckedVal($id){
        $estInfo = $this->getEstimateBasic($this->input->cookie('eid'));
        $json = json_decode($estInfo['running_data']);
        $stage = $this->estimates_m->stage;
        $json = @$json->$stage;
        if(is_object($json) ){
            if($stage == '6' || $stage == '8'|| $stage == '9'){
                foreach($json as $row){
                    if(@$row->id == $id){
                        $this->qty = $row->qty;
                        $this->price = $row->price;
                        return TRUE;
                    }
                }
            }else{
                if($json->id == $id){
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    /**
     * TODO Move data to XML 
     * @return array
     */
    function getPromoCodes(){
        
        $this->db2->order_by('id');
        $query = $this->db2->get('promotions');
        return $query->result_array();
    
    }
    
    /**
     * 
     * Get the summary page's html via ajax
     * @todo move xml and html functions to either Ajax or create a parser model to house the estimate stages
     * @param int $eid
     */
    function getSummaryValues($eid){
        $return = array();
        $this->load->library('estxpath');
        $estInfo = $this->getEstimateBasic($eid);
        $type = $estInfo['unit_type'];
        $displayType = $this->getUnitTypeDisplay($type);
        $obj = json_decode($estInfo['running_data']);
        $rdArray = json_decode(json_encode($obj),true);//moving everything from multi-dimensional object into array
        $baseId = $rdArray[1]['id'];
        $level = $rdArray[1]['level'];
        $basePrice = $this->setDollarFormat($rdArray[1]['price']);
        
        //base system and brand option logic
        if(@$rdArray[2]['id'] != $baseId){
            $brandId = $rdArray[2]['id'];
            $brandPrice = $this->setDollarFormat($rdArray[2]['price']);
            $brandPath = '/estimatePricing/'. $type . 'Systems/' . $type . 'Systems' . $level . '/brandOptions/item[id = "' . $brandId . '"]/brand | ' . '/estimatePricing/'. $type . 'Systems/' . $type . 'Systems' . $level . '/brandOptions/item[id = "' . $brandId . '"]/description' ;
            $brandXml = $this->getXpathValue($brandPath);
            $return['brandOption'] =  nbs(2) . '<span class="text-success">$' . $brandPrice . "</span> - " . $brandXml[0] . ", " . $brandXml[1];
        }
        $basePath = '/estimatePricing/'. $type . 'Systems/' . $type . 'Systems' . $level . '/baseSystem/brand';
        $baseBrand = $this->getXpathValue($basePath);
        $return['baseSystem'] = nbs(2) . '<span class="text-success">$' . $basePrice . '</span> - 2 Ton ' . $level . ", " . $displayType . " system " . (@$brandId ? '' : ' (' .$baseBrand[0] . ')');
  
        //upsize logic
        if(@$rdArray[3]['id'] != 'wlQdRMB2ig'){
            $sizeId = $rdArray[3]['id'];
            $sizePrice = $this->setDollarFormat($rdArray[3]['price']);
            $sizeXml =  $this->getXpathValue('/estimatePricing/sizeOptions/size[id = "' . $sizeId . '"]/description');
            $return['sizeOption'] = nbs(2) . '<span class="text-success">$' . $sizePrice . "</span> - " . $sizeXml[0];
        }
        
        //thermostat upgrade
        if(@$rdArray[4]['price'] > '0.00'){
            $thermostatId = $rdArray[4]['id'];
            $thermostatPrice = $this->setDollarFormat($rdArray[4]['price']);
            $thermostatXml =  $this->getXpathValue('/estimatePricing/thermostats/item[id = "' . $thermostatId . '"]/description');
            $return['thermostatOption'] = nbs(2) . '<span class="text-success">$' . $thermostatPrice . "</span> - " . $thermostatXml[0];
        }
        
        //Maintenance option
        if(@$rdArray[5]['price'] > '0.00'){
            $maintId = $rdArray[5]['id'];
            $maintPrice = $rdArray[5]['price'];
            $maintXml =  $this->getXpathValue('/estimatePricing/maintenancePlanOptions/item[id = "' . $maintId . '"]/description');
            $return['maintOption'] = nbs(2) . '<span class="text-success">$' . $this->setDollarFormat($maintPrice) . "</span> - " . $maintXml[0];
        }
        
        //misc option (accessories)
        if(count(@$rdArray[6]) == 1){
            foreach($rdArray[6] as $key => $val){
                $miscId = $val['id'];
                $miscTotal = ($val['price'] * $val['qty']);
                $miscXml =  $this->getXpathValue('/estimatePricing/miscellaneousOptions/item[id = "' . $miscId . '"]/description');
                $return['miscOption'] = nbs(2) . '<span class="text-success">$' . $this->setDollarFormat($miscTotal) . "</span> - " . $miscXml[0];
            }
        }elseif(count(@$rdArray[6]) >= 2){
            $miscTotal = '0.00';
            foreach(@$rdArray[6] as $key => $val){
                $miscTotal = $miscTotal + ($val['price'] * $val['qty']); 
                $miscId = $val['id'];    
            }
            $return['miscOption'] = nbs(2) . '<span class="text-success">$' . $this->setDollarFormat($miscTotal) . "</span> - Multiple items (full listing on final estimate)";
        }
        
        
        
        //Aeroseal
        if(count(@$rdArray[8]) == 1){
            foreach($rdArray[8] as $key => $val){
                $aeroId = $val['id'];
                $aeroTotal = ($val['price'] * $val['qty']);
                $aeroXml =  $this->getXpathValue('/estimatePricing/atticSolutions/aeroseal/item[id = "' . $aeroId . '"]/description');
                $return['aerosealOption'] = nbs(2) . '<span class="text-success">$' . $this->setDollarFormat($aeroTotal) . "</span> - " . $aeroXml[0];
            }
        }elseif(count(@$rdArray[8]) >= 2){
            $aeroTotal = '0.00';
            foreach($rdArray[8] as $key => $val){
                $aeroTotal = $aeroTotal + ($val['price'] * $val['qty']); 
            }
            $return['aerosealOption'] = nbs(2) . '<span class="text-success">$' . $this->setDollarFormat($aeroTotal) . "</span> - " . count($rdArray[8]) . " Units";
        }
        
        if(count(@$rdArray[9]) == 1){
            foreach($rdArray[9] as $key => $val){
                $insulId = $val['id'];
                $insulTotal = ($val['price'] * $val['qty']);
                $insulXml =  $this->getXpathValue('/estimatePricing/atticSolutions/additionalAtticInsulation/item[id = "' . $insulId . '"]/description');
                if($insulId == 'RgdPXyz5w4'){
                    $return['insulationOption'] = nbs(2) . '<span class="text-success">$' . $this->setDollarFormat($insulTotal)  . "</span> - " . $val['qty'] . " " . $insulXml[0];
                }else{
                    $return['insulationOption'] = nbs(2) . '<span class="text-success">$' .  $this->setDollarFormat($insulTotal) . "</span> - "  . $insulXml[0] . ', ' . $val['qty'] . " sq. feet";
                }
            }
        }elseif(count(@$rdArray[9]) >= 2){
            $insulTotal = '0.00';
            foreach($rdArray[9] as $key => $val){
                $insulTotal =  $insulTotal + ($val['price'] * $val['qty']); 
            }
            $return['insulationOption'] = nbs(2) . '<span class="text-success">$' . $this->setDollarFormat($insulTotal) . "</span> - " . count($rdArray[9]) . " different items (full listing on final est.) ";
        }else{
            
        }
        
        $total = $this->getRunningTotal($eid,false);
        if(@$rdArray[10] && @$rdArray[10]['price'] <= 0){
            $return['subtotalValue'] = $this->setDollarFormat($total + preg_replace('/\-/','', $rdArray[10]['price']));
            $return['promoValue'] = preg_replace('/\-/','',$this->setDollarFormat($rdArray[10]['price']));
            $return['promoVisibility'] = $return['subtotalVisibility'] = '';
        }else{
            $return['promoVisibility'] = $return['subtotalVisibility'] = 'style="display: none;"';
        }
        
        //financing and payment options 
        if(@$rdArray[7]['id'] != 'YspSETu8KQ'){
            $financeId = $rdArray[7]['id'];
            $financePrice = $rdArray[7]['price'];
            $financeXml =  $this->getXpathValue('/estimatePricing/financingOptions/item[id = "' . $financeId . '"]/description');
            $return['financeOption'] = nbs(2) . '<span class="text-success">$' . $this->setDollarFormat($financePrice) . "</span> - " . $financeXml[0];
            
            $paymentXml = $this->getXpathValue('/estimatePricing/financingOptions/item[id = "' . $financeId . '"]/calcPayment | /estimatePricing/financingOptions/item[id = "' . $financeId . '"]/extra');
            $paymentExtra = $paymentXml[1]; //textual info maybe to be used
            $return['paymentExtra'] = $paymentExtra;
            $return['payment'] = $this->setDollarFormat($this->getPayment($total, $paymentXml[0]));
            
        }
        
        $return['total'] = $this->setDollarFormat($total);
        return $return;
    }
    
    function getUnitTypeDisplay($type){
        switch ($type){
            case 'splitHP':
                return 'Split H.P.';
            case 'splitGas':
                return 'Split gas';
            case 'packagedHP':
                return 'Packaged H.P.';
            case 'packagedGas':
                return 'Packaged gas';
        }
    }
    /**
     * use a comma delimted $calculation (eg. x,*,.0120) x = total, * is the math, multiply/divide by
     * @param decimal $total
     * @param string $calculation
     */
    function getPayment($total, $calculation){
        $formula = explode(',', $calculation);
        switch ($formula[0]){
            case 'x':
            default:
            $amount = $total;
            break;
        }
        
        switch ($formula[1]){
            case '/':
                return ($amount / $formula[2]);
            case '*':
            default:
                return ($amount * $formula[2]);
        }
    }
    
    function getXpathValue($path){
        //echo ($path);
        //exit;
        $return = array();
        $entries = $this->estxpath->query($path);
        foreach($entries as $entry){
            $return[] = $entry->nodeValue;
        }
        return $return;
    }
// get total number of users
    /**
     * @deprecated 
     * @param type $field
     * @param type $value
     * @return type
     */
    function getNumEstimatesWhere($field='', $value=''){
        if($field && $value){
            $this->db1->where($field,$value);
        }
        return $this->db1->count_all($this->itemTable);
    }
    
    public function getEditFormData($method, $id){
        $estDetail = $this->getEstimateDetail($id);
        $this->load->model('form_m');
        return $this->form_m->getEditFormData($method, $estDetail);
    }
    
    /**
     * 
     * @param string $method should be class::method  '__METHOD__' constant called from controller
     * @return array
     */
    public function getEmptyFormData($method){
        $this->load->model('form_m');
        return $this->form_m->getEmptyFormData($method);
    }
    /**
     * $db should be a db1 or db2 string (db1 is login and reg info, 
     * $this->_userId needs to be set with user's login id otherwise this
     * will fail
     * db2 is the app db and also contains user's profile
     * relevant to the aoo
     * @param array $dbInfo  
     * @param type $column
     * @return type
     */
    private function _getEstimateData($column, $dbInfo){
        if(!is_array($dbInfo) || count($dbInfo) <> 2){
            return;
        }
        $db = $dbInfo[0];
        $table = $dbInfo[1];
        $this->$db->from($table);
        $this->$db->where('id', $this->_userId );
        $query = $this->$db->get()->result();
        return $query[0]->$column;
    }
    
    /**
     * 
     */
    //TODO CLean this up into a switch of phones to return a true on
    function getAppMobileStatus(){
        $this->load->library('user_agent');
        return $this->agent->isPhone;
    }
  
    function setLocked($id, $locked){
        $this->db1->set('locked', $locked, FALSE );
        $this->db1->where('id', $id);
        $this->db1->update($this->itemTable);
    }
    
    function setEstimateDuplicate($eid){
        //return if there's no post data, nothing to do
        return "666";
        $this->load->model('form_m');
        //reassign POST data to a new array and drop unnessary submit and other elements
        //$data = $this->form_m->transformPost();
        //update the app profile table
        $this->db2->set('modified','CURRENT_TIMESTAMP', FALSE );
        $this->db2->where('id', $data['id'] );
        $this->db2->update($this->itemTable,$data);
        //update login table
        return true;
    }
    
    function setEstimateUpdate(){
        //return if there's no post data, nothing to do
        if(!$this->input->post()){
            return false;
        }
        $this->load->model('form_m');
        //reassign POST data to a new array and drop unnessary submit and other elements
        $data = $this->form_m->transformPost();
        //update the app profile table
        $this->db2->set('modified','CURRENT_TIMESTAMP', FALSE );
        $this->db2->where('id', $data['id'] );
        $this->db2->update($this->itemTable,$data);
        //update login table
        return true;
    }
    
    function setEstimateClose($eid){
        if(!$this->input->post()){
            return false;
        }
        if($this->input->post('promotional') > '0'){
            $this->db2->set('promo',1);
        }
        $this->db2->set('closed',1);
        $this->db2->where('id', $eid );
        $this->db2->update($this->itemTable,$data);
        return true;
    }
    function setEstimateUpdateFields($eid, $fieldNValue = array()){
        //return if there's no post data, nothing to do
        if(empty($fieldNValue)){
            return FALSE;
        }
        $this->db2->set('modified','CURRENT_TIMESTAMP', FALSE );
        //$this->db2->set('modified_by', $this->session->userdata('login_id'));
        foreach($fieldNValue as $key => $val){
            $this->db2->set($key, $val);
        }
        $this->db2->where('id', $eid );
        $this->db2->update($this->itemTable);
        return true;
    }
    /**
     * this inserts the estimate entry in estimates_detail so it has the signature SVG
     * @param int $id
     * @param string $svg
     * @return bool
     */
    function setNewEstimateDetail($id,$svg){
        $this->db2->where('id', $id);
        $query = $this->db2->get($this->itemTable);
        if($query->num_rows() <> 1){
            print "false1<br>";
            print_r($query);
            return FALSE;
        }
        $this->db2->where('id', $id);
        $this->db2->set('completed',1);
        $this->db2->update($this->itemTable);
        
        $this->db2->where('estimate_id', $id);
        $query = $this->db2->get($this->detailTable);
        if($query->num_rows() > 0){
            print "false2<br>";
            print_r($query);
            return FALSE;
        }else{
            $this->load->helper('string');
            $this->_pdfHash = random_string('alnum', 12);
            $this->db2->set('pdf_hash', $this->_pdfHash); 
            $this->db2->set('estimate_id',$id);
            $this->db2->set('signature_svg', $svg);
            $this->db2->set('added', 'NOW()', FALSE );
            $this->db2->set('modified', 'NOW()', FALSE );
            $return = $this->db2->insert($this->detailTable);
            if($this->setWriteNSendPDF($this->_pdfHash)){
                return $return;
            }
        }
        
        
    }
    
    function setWriteNSendPDF($pdfHash){
        $url= base_url() . 'pdfgen/write/' . $pdfHash . '/' . $this->config->item('pdf_passkey','config_app');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, "tel=$demo_phone&act=callforcvc&id=ou812");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        
        $this->load->model('pdf_m');
        $this->pdfInfo = $this->pdf_m->getPDFDetail($pdfHash);
        $this->pdfLocation = $this->pdf_m->fileDrop . $this->config->item('pdf_file_prefix','config_app') . $pdfHash . '.pdf';
        $this->_sendSetNewEstimateEmail();
        $this->_sendSetNewEstimateEmailInternal();
        $removePdf = shell_exec('rm -rf ' . $this->pdfLocation);
        return TRUE;
    }
   
    /** 
     * Sets/creates a new user, in both the login database and app
     * @todo This function is currently returning 6 for an already added est in the db, need to re-enable model function
     * @param string $email
     * @return bool
     */
    function setNewEstimate(){
        $this->load->model('form_m');
        $fields = $this->form_m->transformPost();
        $this->db2->set('added_by',$this->session->userdata('login_id'));
        $this->db2->set('sales_person', $this->getSalesRep($this->session->userdata('login_id')));
        $this->db2->set('added', 'NOW()', FALSE );
        $this->db2->set('modified', 'NOW()', FALSE );
        
        $result = $this->db2->insert($this->itemTable, $fields);
        if($result){
            return $this->db2->insert_id();
        }else{
            log_message('debug', $result);
            return $result;
        }
        
    }
    
    function setType($eid, $selection = ''){
        $estInfo = $this->getEstimateBasic($eid);
        $this->type = $estInfo['unit_type'];
        //var_dump($selection);
    }
    /**
     * Send new user email with the optional reActivation (skipping registration) and 
     * sending a different message body
     * @param bool $reActivate 
     */
    private function _sendSetNewEstimateEmail(){
        $this->load->library('email');
        $email = $this->pdfInfo->email;
        $config['wrapchars'] = '130';
        
        $this->email->initialize($config);
        $co = $this->config->item('company', 'config_app');
        $noReplyEmail = $this->config->item('auto_email', 'config_app');
        $coPhone = $this->config->item('company_phone_number', 'config_app');
        $this->email->from($noReplyEmail, $co);
        $this->email->reply_to($noReplyEmail, $co);
        $this->email->to($email); 
        //$this->email->cc('another@another-example.com'); 
        //$this->email->bcc('them@their-example.com');   
        $this->email->subject($co . ' Estimate #' . $this->pdfInfo->estimate_id);
        //get string message from _getUserEmailMessage
        
        $logPrefix = "New Estimate Email with pdf sent to ";
        $this->email->message( "Hello " . $this->pdfInfo->first_name . ' ' . $this->pdfInfo->last_name  . "\n\nAttached is your estimate from " . 
                                    $co . ". If for any reason you can't view this attachment (.pdf file), please contact your salesperson: " . $this->pdfInfo->sales_person .
                                    ' Thanks again for choosing ' . $co . "We appreciate your business! \n\n" .
                                    'Please do not reply to this email as it is a system address and nobody will recieve it. Please call our office at ' . $coPhone . 
                                    " for any additional questions or concerns. \n\n Best Regards,\n" . $co );
        $attachment = $this->pdfLocation;
        $this->email->attach($attachment);
        $this->email->send();
        $this->email->clear(TRUE);
        log_message('debug', $logPrefix . $email . ", initiated from " . $_SERVER['REMOTE_ADDR']);
        
    }
    
    private function _sendSetNewEstimateEmailInternal(){
        //$this->load->library('email');
        if($this->pdfInfo->sale_status == '1'){
            $email = $this->config->item('sold_email', 'config_app');
            $soldStatus = 'SOLD';
        }else{
            $email = $this->config->item('estimate_email', 'config_app');
            $soldStatus = 'ESTIMATE ONLY';
        }
        $config['wrapchars'] = '130';
        
        $this->email->initialize($config);
        $co = $this->config->item('company', 'config_app');
        $app = $this->config->item('app_title', 'config_app');
        $coAndApp = $this->config->item('company', 'config_app') . ' '  . $this->config->item('app_title', 'config_app');
        $noReplyEmail = $this->config->item('auto_email', 'config_app');
        $adminEmail = $this->config->item('admin_email', 'config_app');
        $adminName = $this->config->item('admin_name', 'config_app');
        $coPhone = $this->config->item('company_phone_number', 'config_app');
        $this->email->from($noReplyEmail, $coAndApp);
        $this->email->reply_to($noReplyEmail, $coAndApp);
        $this->email->to($email); 
        //$this->email->cc('another@another-example.com'); 
        //$this->email->bcc('them@their-example.com');   
        $this->email->subject($co . ' Estimate #' . $this->pdfInfo->estimate_id);
        //get string message from _getUserEmailMessage
        
        $logPrefix = "New Estimate Email with pdf sent to ";
        $this->email->message( "Hello,\n\nA new estimate was generated from " . $app . ".\n\n" .
                                    "This estimate was statused as '" . $soldStatus . "' and was added by " . $this->pdfInfo->sales_person . ". Please click " .
                                    base_url() . "pdfgen/view/" . $this->_pdfHash . " to view. You may need to login to view\n\n" .
                                    'Please do not reply to this email as it is a system address and nobody will recieve it. If you are unable to ' . 
                                    'view the pdf/link, please contact ' . $adminName . ' (' . $adminEmail . ').');
        $this->email->send();
        log_message('debug', $logPrefix . $email . ", initiated from " . $_SERVER['REMOTE_ADDR']);
    }
    /**
     * deactivate user, delete method is written for this class but not deleting
     * users to break db relationships
     * @param int $login_id
     * @return boolean
     */
    function setEstimateArchived($eid){
        $email = $this->getEstimateField($eid, 'email'); //
        $this->db2->set('active', 0);
        $this->db2->where('id', $eid);
        $return = $this->db2->update($this->itemTable);
        if($return){
            $this->msg = 'Estimate #' . $eid . ' for: ' . $email . ' was archived and is no longer in your active estimates list.';
            return TRUE;
        }
        log_message('debug', $return);
        return $return;
    }
    
    /**
     * deactivate user, delete method is written for this class but not deleting
     * users to break db relationships
     * @param int $login_id
     * @return boolean
     */
    function setEstimateActive($eid){
        $email = $this->getEstimateField($eid, 'email'); //
        $this->db2->set('active', 1);
        $this->db2->where('id', $eid);
        $return = $this->db2->update($this->itemTable);
        if($return){
            $this->msg = 'Estimate #' . $eid . ' for: ' . $email . ' was un-archived and is now in your active estimates list.';
            return TRUE;
        }
        log_message('debug', $return);
        return $return;
    }
    /**
     * TODO this needs to check for userid in 
     * @param type $login_id
     * @return boolean
     */
    function delete($login_id){
        $this->db1->from($this->itemTable);
        $this->db1->where('id', $login_id);
        $userTable = $this->db1->delete();
        $this->db1->from($this->detailTable);
        $this->db1->where('login_id', $login_id);
        $profileTableDel = $this->db1->delete();
        $this->db2->from($this->detailTable);
        $this->db2->where('login_id', $login_id);
        $appProfileDel = $this->db1->delete();
        if($userTableDel && $profileTableDel & $appProfileDel){
            $this->msg = 'User: id #' . $login_id . 'was deleted. <br>They will no longer be able to login unless re-added.';
            return TRUE;
        }
            
    }
    
    function setResetPasswd($login_id){
        $this->db1->from($this->userTable);
        $this->db1->where('active', 1);
        $this->db1->where('id', $login_id );
        $results = $this->db1->get()->result();
        if ( is_array($results) && count($results) == 1 ) {
            $this->load->helper('string');
            //$randPass = random_string('alnum', 8);
            $reset_hash = random_string('unique');
            //$this->db1->set('password', sha1($randPass));
            $this->db1->set('reset_hash', $reset_hash);
            $this->db1->set('reset', 0);
            $this->db1->set('modified','CURRENT_TIMESTAMP', FALSE );
            $this->db1->where('id',$login_id );
            $this->db1->update($this->userTable);
            $this->_reset_hash = $reset_hash;
            //$this->_randPass = $randPass;
            $this->_sendForgotPasswdEmail($results[0]->email);
            $this->msg = 'Password Recovery email was sent to ' . $results[0]-> email . '. They need to click the link and then establish a new password';
            return true;
        }
        $this->msg = 'The email could not be verified, please contact a website administrator';
        log_message('debug', "Issue with user/reset_pwd for $login_id");
        return false;
    }
    
}
