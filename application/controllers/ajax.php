<?php
class ajax extends CI_Controller{
    
    public $results;
    public $eid;


    public function __construct()
    {
        parent::__construct();
        switch(FALSE){
            case $this->session->userdata('isLoggedIn') : 
                show_404();
            break;
            case $this->input->is_ajax_request():
                exit('There was a problem, if this persists please tell an administrator that you have encountered an error: Error Code ID-10-T ');
            break;
        }
        
        //$this->load->model('form_m');

    }
    
    function index() {
        //redirect to something or do nothing 
        show_404();
    }
    
    function keep(){
        echo 1;
    }
    
    /* ####################### ESTIMATE METHODS ########################### */
    //TODO most of these functions do the exact same thing and the _addKeyToArray method transforms to different values, Need to consolidate to a general item
    function getBreadCrumb($stage, $type, $level){
        $this->load->model('estimates_m');
        echo $this->estimates_m->getBreadCrumb($stage,$type,$level);
    }
    
    function getLegend($stage){
        $this->load->model('estimates_m');
        echo $this->estimates_m->getLegend($stage);
    }
    
    function getEstOptions($eid, $stage, $level, $selection = FALSE){
        $this->load->model('estimates_m');
        $this->estimates_m->level = $level;
        $this->estimates_m->stage = $stage;
        $this->eid = $eid;
        $this->_setEstType($eid);
        return $this->_getParseXMLOptions($level, $this->estimates_m->type, $stage);
        //        $return = '<div class="col-xs-6">Premier - ' . $type . ' - ' . $estStage . "</div>\n";
        //        $return .= '<div class="col-xs-6">Premier - ' . $type . ' - ' . $estStage . "</div>\n";
    }
    function getTotal($eid){
        $this->load->model('estimates_m');
        echo $this->estimates_m->getRunningTotal($eid);
    }
    /**
     * _getParseXMLOptions is a wrapper for the ajax controller / module -> estimates_m->getXML
     * @param string $level
     * @param string $type
     * @param int $stage
     * @return string Html formatted string
     */
    private function _getParseXMLOptions($level, $type, $stage = ''){
        //load the parser library
        $this->load->library('parser');
        $this->load->helper('form');
        $data['thcols'] = $this->estimates_m->getTHs($stage);
        //$data['formAttr'] = array('id' => 'estForm');
        //$data['formAction'] = '/ajax/setOptions';
        $data['eid'] = $this->eid;
        $data['stage'] = $stage;
        $data['productImg'] = '';
        $parseView = 'estimates/options_parser_view';
        switch($stage){
            case '3':
                $data['products'] = $this->estimates_m->getXML('sizeOptions', 'size');
                $data['radioDefault'] = $this->estimates_m->radioDefault;
                $data['button'] = '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</button>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';            
            break;
            case '4':
                $data['products'] = $this->estimates_m->getXML('thermostats', 'item');
                $data['radioDefault'] = $this->estimates_m->radioDefault;
                $data['button'] = '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</button>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';            
            break;
            case '5':
                $data['products'] = $this->estimates_m->getXML('maintenancePlanOptions', 'item');
                $data['radioDefault'] = $this->estimates_m->radioDefault;
                $data['button'] = '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</button>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';            
            break;
            case '6':
                $data['products'] = $this->estimates_m->getXML('miscellaneousOptions', 'item');
                $data['button'] = '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</button>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';            
            break;
            case '7':
                $data['products'] = $this->estimates_m->getXML('financingOptions', 'item');
                $data['radioDefault'] = $this->estimates_m->radioDefault;
                $data['button'] = '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</button>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';            
            break;
            case '8':
                $data['products'] = $this->estimates_m->getXML('atticSolutions->aeroseal', 'item');
                $data['productImg'] = '<a href="#" data-toggle="modal" data-target="#videoModal" data-theVideo="https://www.youtube.com/embed/anbapms9tuc"><img class="productImg" alt="Aeroseal logo" title="Seal your ductwork from the inside" src="' . base_url() . 'assets/img/products/aeroseal-logo.png"/></a>'; 
                $data['button'] = '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</button>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';            
            break;
            case '9':
                $data['products'] = $this->estimates_m->getXML('atticSolutions->additionalAtticInsulation', 'item');
                $data['button'] = '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</button>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';            
            break;
            case '10':
                $data['button'] = '<a class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</a>&nbsp;&nbsp;'
                        //. '<input type="submit" class="btn btn-primary btn-sm" value="Continue >" >';
                          . '<a class="btn btn-primary btn-sm" href="#" data-target="#confirm-confirm" data-toggle="modal">Continue ></a>';
                $data['formAttr'] = array('id' => 'estForm','class' => 'form-horizontal');
                $data['formAction'] = '/estimates/estimate_close';
                $data['items'] = $this->estimates_m->getSummaryValues($this->eid); //TODO change in model to a more generic method, unless we use this only as a wrapper
                $data['promocodes'] = $this->estimates_m->getPromoCodes();
                $parseView = 'estimates/final_parser_view';
            break;
            case '2':
                $data['products'] = $this->estimates_m->getXML($type . 'Systems->' . $type . 'Systems' . $level . '->brandOptions', 'item');
                $data['radioDefault'] = $this->estimates_m->radioDefault;
                $data['button'] = '<a class="btn btn-primary btn-sm" href="#" data-target="#confirm-stage1" data-toggle="modal" data-href="' . base_url() . '/estimates/estimate/1/resume">< Prev</a>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';
            break;
            default:
                $data['products'] = $this->estimates_m->getXML($type . 'Systems->' . $type . 'Systems' . $level . '->brandOptions', 'item');
                $data['button'] = '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage - 1) . '\');">< Prev</button>&nbsp;&nbsp;'
                        . '<button class="btn btn-primary btn-sm" onclick="getAjaxHtml(\'' . $level . '\',\'' . $type . '\', \'' . ($stage + 1) . '\');">Next ></button>';
            break;
        }
       
        $this->parser->parse($parseView, $data);
        
    }
    function setFirstStage($eid, $level, $selection){
        $this->load->model('estimates_m');
        $foo = explode('|', urldecode($selection));
        $mSelection = $this->_addKeyToArray($foo);
        $mSelection['level'] = $level;
        //resetting to zero length for bug in restarting the first stage 
        $this->estimates_m->setEstimateUpdateFields($eid, array('running_data' => ''));
        if($this->estimates_m->setRunningTotal($eid, $mSelection)){
           echo $this->estimates_m->getRunningTotal($eid);
        }
    }
    
    function setRadioOption($eid, $selection){
       $this->load->model('estimates_m');
       $foo = explode('|', urldecode($selection));
       $mSelection = $this->_addKeyToArray($foo);
       if($this->estimates_m->setRunningTotal($eid, $mSelection)){
           echo $this->estimates_m->getRunningTotal($eid);
       }
       //echo $foo[0];
    }
    
    function setQuantityForOption($eid, $selection){
       $this->load->model('estimates_m');
       $foo = explode('|', urldecode($selection));
//       echo "<pre>";
//       print_r($foo);
//       exit;
       $mSelection = $this->_addKeyToArray($foo);
       if($this->estimates_m->setQuantityForItem($eid, $mSelection)){
           echo $this->estimates_m->getRunningTotal($eid);
       }
       
    }
    
    function setPriceForOption($eid, $selection){
       $this->load->model('estimates_m');
       $foo = explode('|', urldecode($selection));
       $bar = explode('_priceDiv', $foo[0]);
       $mSelection['price'] = $foo[1];
       $mSelection['chkid'] = $bar[1];
       $mSelection['stage'] = $bar[0];
       if($this->estimates_m->setPriceForItem($eid, $mSelection)){
           echo $this->estimates_m->getRunningTotal($eid);
       }
       
    }
    
    function setPromo($eid, $selection){
        $this->load->model('estimates_m');
        $foo = explode('|', urldecode($selection));
        //echo "<pre>";
        $mSelection = $this->_addKeyToArray($foo);
        $mSelection['price'] = '-' . $mSelection['price'];
        if($this->estimates_m->setPromo($eid, $mSelection)){
           echo $this->estimates_m->getRunningTotal($eid,false);
       }
    }
    function getNewPmtAmt($eid, $newTotal){
        $this->load->model('estimates_m');
        $this->load->library('estxpath');
        $estInfo = $this->estimates_m->getEstimateBasic($eid);
        $obj = json_decode($estInfo['running_data']);
        $rdArray = json_decode(json_encode($obj),true);//moving everything from multi-dimensional object into array
        $calculation = $this->estimates_m->getXpathValue('/estimatePricing/financingOptions/item[id = "' . $rdArray[7]['id'] . '"]/calcPayment');
        echo $this->estimates_m->getPayment($newTotal, $calculation[0]);
    }
    
    function addCheckOption($eid, $selection){
       $this->load->model('estimates_m');
       $foo = explode('|', urldecode($selection));
       $mSelection = $this->_addKeyToArray($foo);
       if($this->estimates_m->setRunningTotal($eid, $mSelection)){
           echo $this->estimates_m->getRunningTotal($eid);
       }
    }
    function delCheckOption($eid, $selection){
       $this->load->model('estimates_m');
       $foo = explode('|', urldecode($selection));
       $mSelection = $this->_addKeyToArray($foo);
       if($this->estimates_m->setRunningTotal($eid, $mSelection)){
           echo $this->estimates_m->getRunningTotal($eid);
       }
    }
    
    protected function _setEstType($eid){
        //$setStage = ($this->estimates_m->stage - 1);
        $this->estimates_m->setType($eid);
     
    }
    
    private function _addKeyToArray($array){
        if(!is_array($array) ){
            return false;
        }
        $return = array();
        foreach ($array as $key => $val){
            $array[$key] = preg_replace('/^cb/', '', $val);
        }
        foreach ($array as $key => $val){
            switch($key){
                case 0:
                    if(preg_match('/_/', $val)){
                        $splitStage = explode('_', $val);
                        $return['stage'] = $splitStage[0];
                        $return['chkid'] = $splitStage[1];
                        //TODO do something maybe with $splitStage[1]
                    }else{
                        $return['stage'] = $val;
                    }
                break;
                case 1:
                    $return['id'] = $val;
                break;
                case 2:
                    $return['price'] = $val;
                break;
                case 3:
                    $return['qty'] = $val;
                default:
                break;
            }
            
        }
        return $return;
    }
    
    
    /* ###################### END ESTIMATES METHODS ########################## */
    
}
