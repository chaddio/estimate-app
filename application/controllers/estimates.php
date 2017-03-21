<?php
/* @TODO clean out old property class methods and code */ 
class estimates extends CI_Controller{
    //public $custDateFormat = 'M. jS, Y g:ia';
    protected $_resultsPerPage = CI_APP_ROWS;
    //private $_orderDir = 'asc'; 
    private $_colUri1 = 'e';
    private $_colUri2 = 'p';
    private $_colUri3 = 'n';
    private $_colUri4 = 'd';
    private $_colUri5 = 's';
    private $_th1Icon = '';
    private $_th2Icon = '';
    private $_th3Icon = '';
    private $_th4Icon = '';
    private $_th5Icon = '';
    public $eid;
    
    public function __construct()
    {
        parent::__construct();
        //$this->benchmark->mark('code_start');
        if(!$this->session->userdata('isLoggedIn')){ 
                redirect('/login/timeout');
        }
        //$this->modelObject = 'estimates_m';
        $this->load->model('estimates_m');  
    }
    
    
    /**
     * @todo refactor to other methods to shorten code for this method
     * @param int $active
     * @param string $orderby
     * @param int $row
     * @param string $show_error
     * @param string $alertLevel
     */
    function index($active = 1, $orderby = 'i', $row = 0,  $show_error = FALSE, $alertLevel = 'info') {
        // Get some data from the user's session
        $data = array();
        $data['mobile'] = $this->estimates_m->getAppMobileStatus();
        $data['row'] = $row;
        $data['active'] = $active;
        if($this->input->cookie('completed')){
            $cust_email = $this->estimates_m->getEstimateField($this->input->cookie('completed'),'email');
            $cust_hash = $this->estimates_m->getEstimateDetailField($this->input->cookie('completed'),'pdf_hash');
            $data['error'] = 'Estimate #' . $this->input->cookie('completed') . ' was completed! Customer will receive their <a target="pdfWindow" href="/pdfgen/view/' . $cust_hash . '" >estimate/pdf</a> at "' . $cust_email . '" shortly';
            $data['alertLevel'] = 'success';
            $this->input->set_cookie(array('name' =>'completed','value' => '0','expire' => '','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
        }else{
            $data['error'] = $show_error;
            $data['alertLevel'] = $alertLevel;
        }
        $data['email'] = $this->session->userdata('email');
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ',$data['name']);
        $data['fname'] = $splitName[0];
        $data['serviceCompany'] = $this->config->item('company', 'config_app');
        $data['appTitle'] = $this->config->item('app_title', 'config_app');
        $data['class'] = get_class();
        //$data['navTitle'] = ucfirst($data['class']);
        $data['navTitle'] = ucwords($data['class']);
        $data['viewTitle'] = ucwords($data['class']);
        //activates a search link on the nav bar which loads a search modal for that module/class
        $data['search'] = TRUE;
        $data['pageTitle'] = $data['serviceCompany'] . ' - '  . $data['appTitle'] . ' - ' . ucwords($data['class']);
        //for nav view, set $isAdmin
        if($this->session->userdata('app_userlevel') == '0'){
            $data['isAdmin'] = TRUE;
        }else{
            $data['isAdmin'] = FALSE;
        }

        $data['app_pages'] = $this->session->userdata('app_pages');
        //$this->$modelObj->isAdmin = $data['isAdmin']; //model loaded from __construct
        //set up pagination for the view
        $this->load->library('pagination');
        //declare $method for pagination base url - called 
        $classMethod = __METHOD__;
        $expl = explode('::', $classMethod);
        $data['method'] = $method = $expl[1];
        //set orderby and data['orderby'] to carry var -- also sets url info for column reordering
        $data['orderby'] = $this->_getNSetOrderBy($orderby);
        $data['colUri1'] = $this->_colUri1;
        $data['colUri2'] = $this->_colUri2;
        $data['colUri3'] = $this->_colUri3;
        $data['colUri4'] = $this->_colUri4;
        $data['colUri5'] = $this->_colUri5;
        $data['thI1'] = $this->_th1Icon;
        $data['thI2'] = $this->_th2Icon;
        $data['thI3'] = $this->_th3Icon;
        $data['thI4'] = $this->_th4Icon;
        $data['thI5'] = $this->_th5Icon;
        
        //configs for pagination
        $config['base_url'] = base_url() . $data['class'] . '/' . $method . '/' . $active . '/' . $data['orderby'] . '/';
        $config['per_page']= $this->_resultsPerPage;
        if($this->session->userdata('app_userlevel') < 3){
            $config['total_rows'] = $this->estimates_m->getEstimateCountWhere('active',$active);
        }else{
            $config['total_rows'] = $this->estimates_m->getEstimateCountWhere(array('active','added_by'),array($active,$this->session->userdata('login_id')));
        }
        $config['uri_segment'] = 5;
        $pagetags = $this->_getPaginationTags();
        $config = array_merge($config, $pagetags);
        $this->pagination->initialize($config);
        if($this->session->userdata('app_userlevel') < 3){
            $data['items'] = $this->estimates_m->getEstimatesWhere('active',$active,$config['per_page'], $row);
        }else{
            $data['items'] = $this->estimates_m->getEstimatesWhere(array('active','added_by'),array($active,$this->session->userdata('login_id')),$config['per_page'], $row);
        }
        //modify return items to format special for phone and date
        $this->load->helper('date');
        foreach($data['items'] as $key => $val){
            $time = strtotime($data['items'][$key]['added']);
            $data['items'][$key]['rowClass'] = $this->_getRowClass($data['items'][$key]['sale_status'], $data['items'][$key]['completed'],$data['items'][$key]['closed']);
            $data['items'][$key]['added'] = mdate(($data['mobile'] ? CI_HUMAN_DATE_COND : CI_HUMAN_DATE), $time);//CI_HUMAN_DATE is defined in application/config/constants.php
            $data['items'][$key]['phone_number'] = substr($data['items'][$key]['phone_number'],0,3) . '-' . substr($data['items'][$key]['phone_number'],3,3) . '-' . substr($data['items'][$key]['phone_number'],6,4);
            $data['items'][$key]['pdf_hash'] = $this->estimates_m->getEstimateDetailField($data['items'][$key]['id'],'pdf_hash');
        }

        $data['header'] = 'User List';
        $data['links'] = $this->pagination->create_links();
        //for modal and modal2 divs in modal_users.php view
        //pagination form sort/view options
        //get form items setup for modal_users view
        $data['mFormAction'] = $data['class'] . '/search/';
        $data['attr'] = array('id' => 'modal','name' => 'modal','class' => 'form-horizontal');
        $data['mFormAction2'] = $data['class'] . '/estimate/1/';
        $data['attr2'] = array('id' => 'modal2','name' => 'modal2','class' => 'form-horizontal'); 
        $data['salespeople'] = $this->estimates_m->getAllSalesReps();// TODO add logic for inactive users with last mod of 2 weeks
        
        $this->load->helper('form');
        $this->load->view($data['class'] . '/estimates',$data);
    }
    /**
     * @todo add automatic city state via example URL/AIP http://maps.googleapis.com/maps/api/geocode/json?address=85138&sensor=true 
     * @param integer $stage all other stages (!=1) are being handled by Jquery connecting to ajax controller
     * @param type $show_error
     * @param type $alertLevel
     * @return type
     */
    function estimate($stage = 1, $show_error = FALSE, $alertLevel = 'info'){
        $data = array();
        $this->estimates_m->stage = $stage;
        if(!$this->input->post()){
            //test cookie value
            $eid = $this->input->cookie('eid');
            $data['eid'] = $eid;
            $dbReturn = $this->estimates_m->getEstimateBasic($eid);
            $data['type'] = $dbReturn['unit_type'];
            $cust_first_name = $dbReturn['first_name'];
            $cust_last_name = $dbReturn['last_name'];
            $resumeStage = $dbReturn['resume'];
        }else{
            //javascript/jQuery validation also in place - delete previous cookie(s) for the eid
            $this->input->set_cookie(array('name' =>'eid','value' => '0','expire' => '','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
            $this->load->library('form_validation');
            if($this->form_validation->run(__METHOD__)){
                $est_id = $this->estimates_m->setNewEstimate(); ///TODO currently staticly returning id 6 for testing
            }else{
                $this->index($this->input->post('active'),$this->input->post('orderby'),$this->input->post('row'),'invalid','danger');
                return;
            }
            //print_r($this->input->post());
            //exit;
            $cust_first_name = $this->input->post('first_name');
            $cust_last_name = $this->input->post('last_name');
            $data['type'] = $this->input->post('unit_type');
            $data['estTotal'] = '0.00';
            //test cookie write
            $this->input->set_cookie(array('name' =>'eid','value' => $est_id,'expire' => '86500','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
            $data['eid'] = $est_id;
            // delete cookie: $this->input->set_cookie(array('name' =>'est_id','value' => '12321','expire' => '','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
            //$data['est'];
        }
        $data['estTotal'] = $this->estimates_m->getRunningTotal($data['eid']);
        $data['class'] = get_class();
        //$data['row'] = $row;
        if($show_error == 'resume'){
            $data['error'] = '<a href="' . base_url() . $data['class'] . '/resume/' . $data['eid'] . '" >Resume</a> the last completed step';
            $data['alertLevel'] = 'info';
        }else{
            $data['error'] = $show_error;
            $data['alertLevel'] = $alertLevel;
        }
        
        $data['email'] = $this->session->userdata('email');
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ',$data['name']);
        $data['fname'] = $splitName[0];
        $data['serviceCompany'] = $this->config->item('company', 'config_app');
        $data['appTitle'] = $this->config->item('app_title', 'config_app');
        
        //this is for locking the nav bar so people don't accidentaly navigate away from an estimate
        $data['lockNav'] = TRUE;
        $data['navTitle'] = 'New Estimate';
        $data['custEstTitle'] = '' . $data['navTitle'] . ': &nbsp;&nbsp;<span class="text-info">' . ucwords($cust_first_name . ' ' . $cust_last_name) . '</span>';
        $data['viewTitle'] = 'Choose A Base System';
        //ucwords($this->input->post('first_name')) . ' ' . 
        //ucwords($this->input->post('last_name')) . '</span><br> <a href=#>' . $this->input->post('email') . '</a></h5>';
        $data['pageTitle'] = $data['serviceCompany'] . ' - '  . $data['appTitle'] . ' - ' . ucwords($data['class']);
        //for nav view, set $isAdmin
        if($this->session->userdata('app_userlevel') == '0'){
            $data['isAdmin'] = TRUE;
        }else{
            $data['isAdmin'] = FALSE;
        }
        $data['breadcrumb'] = $this->_getBreadCrumb($stage, $data['type'], 0);
        $data['baseprices'] = $this->_getBasePrices($data['type']);

        $data['app_pages'] = $this->session->userdata('app_pages');
        $this->load->view($data['class'] . '/' . substr($data['class'], 0, -1),$data);
    }
    
    function resume($id, $show_error = FALSE, $alertLevel = 'success'){
        $data = array();
        $data['eid'] = $id;
        $this->input->set_cookie(array('name' =>'eid','value' => $data['eid'],'expire' => '86500','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
        $estimateInfo = $this->estimates_m->getEstimateBasic($data['eid']);
        $data['type'] = $estimateInfo['unit_type'];
        $cust_first_name = $estimateInfo['first_name'];
        $cust_last_name = $estimateInfo['last_name'];
        $obj = json_decode($estimateInfo['running_data']);
        $rdArray = json_decode(json_encode($obj),true);//moving everything from multi-dimensional object into array
        $data['level'] = $rdArray[1]['level'];
        $data['stage'] = $estimateInfo['resume'];
        
        $data['estTotal'] = $this->estimates_m->getRunningTotal($data['eid']);
        //$data['row'] = $row;
        $data['error'] = $show_error;
        $data['alertLevel'] = $alertLevel;
        $data['email'] = $this->session->userdata('email');
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ',$data['name']);
        $data['fname'] = $splitName[0];
        $data['serviceCompany'] = $this->config->item('company', 'config_app');
        $data['appTitle'] = $this->config->item('app_title', 'config_app');
        $data['class'] = get_class();
        //this is for locking the nav bar so people don't accidentaly navigate away from an estimate
        $data['lockNav'] = TRUE;
        $data['navTitle'] = 'New Estimate';
        $data['custEstTitle'] = '' . $data['navTitle'] . ': &nbsp;&nbsp;<span class="text-info">' . ucwords($cust_first_name . ' ' . $cust_last_name) . '</span>';
        $data['viewTitle'] = 'Choose A Base System';
       
        $data['pageTitle'] = $data['serviceCompany'] . ' - '  . $data['appTitle'] . ' - ' . ucwords($data['class']);
        //for nav view, set $isAdmin
        if($this->session->userdata('app_userlevel') == '0'){
            $data['isAdmin'] = TRUE;
        }else{
            $data['isAdmin'] = FALSE;
        }
        $data['breadcrumb'] = $this->_getBreadCrumb($data['stage'], $data['type'], 0);
        //$data['baseprices'] = $this->_getBasePrices($data['type']);

        $data['app_pages'] = $this->session->userdata('app_pages');
        $this->load->view($data['class'] . '/estimate_resume',$data);
    }

    function edit($id, $show_error = false, $alertLevel = 'danger'){
        if($this->session->userdata('app_userlevel') == '0'){
            $data['isAdmin'] = TRUE;
        }else{
            $data['isAdmin'] = FALSE;
        }
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ',$data['name']);
        $data['fname'] = $splitName[0];
        $data['id'] = $id;
        $data['app_pages'] = $this->session->userdata('app_pages');
        $data['formAction'] = 'estimates/update';
       
        $data['class'] = get_class();
        $data['navTitle'] = 'Edit ' . ucwords($data['class']);
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - Edit Estimate #' . $id;
        //$data['disableHelp'] = TRUE;
        $orderby = 'i';
        $data['orderby'] = $this->_getNSetOrderBy($orderby);
        $data['formData'] = $this->estimates_m->getEditFormData(__METHOD__, $id);
        //transform the phone number
        if(@!$this->input->post('phone_number')){
            $data['formData']['phone_number']['value'] = '(' . substr($data['formData']['phone_number']['value'],0,3) . ') ' . substr($data['formData']['phone_number']['value'],3,3) . '-' . substr($data['formData']['phone_number']['value'],6,4);
        } 
        //object created from above to use as formData has data but in a wrapped,form helper format 
        $data['estimateObject'] = $this->estimate = $this->estimates_m->estimate_detail;
        $this->load->helper('date');
        //transform the dates from mysql datetime to CI_HUMAN_DATE constant/format
        $addedTime = strtotime($data['estimateObject']->added);
        $modifiedTime = strtotime($data['estimateObject']->modified);
        if($data['estimateObject']->install_date != '0000-00-00'){
            $explDate = explode('-', $data['estimateObject']->install_date);
            $data['estimateObject']->install_date = $explDate[1] . '/' . $explDate[2] . '/' . $explDate[0];
        }else{
            $data['estimateObject']->install_date = '';
        }
        $data['estimateObject']->added = mdate(CI_HUMAN_DATE, $addedTime);//CI_HUMAN_DATE is defined in application/config/constants.php
        $data['estimateObject']->modified = mdate(CI_HUMAN_DATE, $modifiedTime);
        $data['estimateObject']->pdf_hash = @$this->estimates_m->getEstimateDetailField($id,'pdf_hash');
        if($show_error == 'flag'){
            $data['error'] = 'Please fill out all fields to get started';
            $data['alertLevel'] = 'info'; //error, info, success = red, blue, green
        }elseif($show_error == 'completed'){
            $data['error'] = 'Estimate #' . $id . ' was completed! Customer will receive their estimate/pdf @ ' . $this->estimate->email . ' shortly';
            $data['alertLevel'] = 'success'; //error, info, success = red, blue, green
        }else{
            $data['error'] = $show_error;
            $data['alertLevel'] = $alertLevel;
        }
        $data['viewTitle'] = 'Edit Estimate: <span class="text-success"># ' . $this->estimate->id . ' - ' . $this->estimate->first_name . ' ' . $this->estimate->last_name .  '</span>';
        $this->load->helper('form');
        $this->load->view('estimates/estimate_edit', $data);
        
    }
    
    public function update(){
        $this->load->library('form_validation');
        if($this->form_validation->run(__METHOD__)){
            if($this->estimates_m->setEstimateUpdate()){
                //$detail = $this->estimates_m->getDetailsWhere('id',$this->input->post('login_id'),'login');
                $this->index(1, 'i', 0, 'Estimate #' . $this->input->post('id') . ' was updated');
                $this->form_validation->unset_field_data();
            }else{
                $this->edit($this->input->post('id'), 'Something whacky happened when trying to update estimate: ' . $this->input-post('id'), 'alert');
                $this->form_validation->unset_field_data();
            }
        }else{
            $this->edit($this->input->post('id'), 'invalid', 'danger');
        }
    }
    
    function estimate_finalize($id,$show_error = false, $alertLevel = 'danger'){
        if($show_error == 'flag'){
            $data['error'] = 'Please fill out ALL fields to complete estimate';
            $data['alertLevel'] = 'danger'; //error, info, success = red, blue, green
        }else{
            $data['error'] = $show_error;
            $data['alertLevel'] = $alertLevel;
        }
        $data['navTitle'] = 'Edit User';
        if($this->session->userdata('app_userlevel') == '0'){
            $data['isAdmin'] = TRUE;
        }else{
            $data['isAdmin'] = FALSE;
        }
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ',$data['name']);
        $data['fname'] = $splitName[0];
        $data['id'] = $id;
        $data['app_pages'] = $this->session->userdata('app_pages');
        $data['formAction'] = 'estimates/estimate_final';
        $data['formAttr'] = array('class' => 'form-horizontal','name' => 'estimate_finalize','id' => 'est_finalize');
        $data['lockNav'] = TRUE;
        $data['class'] = get_class();
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - Finalize Estimate #' . $id;
        //$data['disableHelp'] = TRUE;
        $orderby = 'i';
        $data['orderby'] = $this->_getNSetOrderBy($orderby);
        $data['formData'] = $this->estimates_m->getEditFormData(__METHOD__, $id);
        //transform the phone number
        if(@!$this->input->post('phone_number')){
            $data['formData']['phone_number']['value'] = '(' . substr($data['formData']['phone_number']['value'],0,3) . ') ' . substr($data['formData']['phone_number']['value'],3,3) . '-' . substr($data['formData']['phone_number']['value'],6,4);
        }
        //object created from above to use as formData has data but in a wrapped,form helper format 
        $data['estimateObject'] = $this->estimate = $this->estimates_m->estimate_detail;
        $data['total'] = $this->estimates_m->getRunningTotal($id);
        $this->load->helper('date');
        //transform the dates from mysql datetime to CI_HUMAN_DATE constant/format
        $addedTime = strtotime($data['estimateObject']->added);
        $modifiedTime = strtotime($data['estimateObject']->modified);
        $data['estimateObject']->added = mdate(CI_HUMAN_DATE, $addedTime);//CI_HUMAN_DATE is defined in application/config/constants.php
        $data['estimateObject']->modified = mdate(CI_HUMAN_DATE, $modifiedTime);
        $data['viewTitle'] = 'Finalize Estimate: <span class="text-success"># ' . $this->estimate->id . ' - ' . $this->estimate->first_name . ' ' . $this->estimate->last_name .  '</span>';
        $this->load->helper('form');
        $this->load->view('estimates/estimate_final', $data);
        
    }
    
    public function estimate_close(){
        $this->load->library('form_validation');
        if($this->form_validation->run(__METHOD__)){
            $this->estimates_m->setEstimateClose($this->input->post('id'));
            redirect('/estimates/estimate_finalize/' . $this->input->post('id') . '/flag');
        }
    }
    
    public function estimate_final(){
        $this->load->library('form_validation');
        if($this->form_validation->run(__METHOD__)){
            if($this->estimates_m->setEstimateUpdate()){
                //$detail = $this->estimates_m->getDetailsWhere('id',$this->input->post('login_id'),'login');
                $this->form_validation->unset_field_data();
                echo 1;
                exit;
            }else{
                $this->edit($this->input->post('id'), 'Something whacky happened when trying to update estimate: ' . $this->input-post('id'), 'alert');
                $this->form_validation->unset_field_data();
            }
        }else{
            $this->estimate_finalize($this->input->post('id'), 'invalid', 'danger');
        }
    }
    
    public function finish_estimate(){
        //$this->index(1,'i',0,$indexMsg,'success');
        $eid = $this->input->post('id');
        $svg = $this->input->post('signature_svg');
        if($this->estimates_m->setNewEstimateDetail($eid, $svg)){
            $this->input->set_cookie(array('name' =>'completed','value' => $eid,'expire' => '500','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
            $this->input->set_cookie(array('name' =>'eid','value' => '0','expire' => '','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
            redirect('/estimates/');
        }else{
            echo 'fooey! something is messed up';
            exit;
        }
        
    }

    public function delete($eid,$row = 0){
        //$this->load->model('estimates_m');
        $return = $this->estimates_m->setEstimateArchived($eid);
        if($return){
            $this->index(1, '', $row, $this->estimates_m->msg,'danger');//first param is show/hide 'active' 
        }else{
            echo "There was a problem";
            print_r($return);
            exit;
        }
    }
    
    public function activate($eid,$row = 0){
        //$this->load->model('estimates_m');
        $return = $this->estimates_m->setEstimateActive($eid);
        if($return){
            $this->index(0,'', $row, $this->estimates_m->msg,'info');
        }else{
            echo "There was a problem";
            print_r($return);
            exit;
        }
    }
    
    public function duplicate($eid){
        $newId = $this->estimates_m->setEstimateDuplicate($eid);
        echo $newId;
        exit;
    }
    
    function search($row=0, $show_error = FALSE, $alertLevel = 'info') {
    
        // Get some data from the user's session
        $data = array();
        $data['mobile'] = $this->estimates_m->getAppMobileStatus();
        $data['row'] = $row;
        $data['active'] = 1;
        $data['error'] = $show_error;
        $data['alertLevel'] = $alertLevel;
        $data['email'] = $this->session->userdata('email');
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ',$data['name']);
        $data['fname'] = $splitName[0];
        $data['serviceCompany'] = $this->config->item('company', 'config_app');
        $data['appTitle'] = $this->config->item('app_title', 'config_app');
        $data['class'] = get_class();
        //$data['navTitle'] = ucfirst($data['class']);
        $data['navTitle'] = 'Search Estimates';
        $data['viewTitle'] = $data['navTitle'];
        //activates a search link on the nav bar which loads a search modal for that module/class
        $data['search'] = TRUE;
        $data['pageTitle'] = $data['serviceCompany'] . ' - '  . $data['appTitle'] . ' - ' . ucwords($data['class']);
        //for nav view, set $isAdmin
        if($this->session->userdata('app_userlevel') == '0'){
            $data['isAdmin'] = TRUE;
        }else{
            $data['isAdmin'] = FALSE;
        }

        $data['app_pages'] = $this->session->userdata('app_pages');
        //$this->load->model('estimates_m');
        $this->estimates_m->isAdmin = $data['isAdmin'];
        //set up pagination for the view
        $this->load->library('pagination');
        //declare $method for pagination base url - called 
        $classMethod = __METHOD__;
        $expl = explode('::', $classMethod);
        $method = $expl[1];
        //configs for pagination
        $config['base_url'] = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/' . $data['class'] . '/' . $method . '/';
        $config['per_page']= $this->_resultsPerPage;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        //$config['use_page_numbers'] = TRUE;
        //$config['uri_segment'] = 4;
        $field = array('CONCAT(' . $this->estimates_m->itemTable . ".first_name,  ' ', " . $this->estimates_m->itemTable . ".last_name )",$this->estimates_m->itemTable . '.email',$this->estimates_m->itemTable . '.phone_number',$this->estimates_m->itemTable . '.zip_code',$this->estimates_m->itemTable . '.added_by',);
        if(!$this->input->post()){
            $params = array( $this->session->userdata('post_full_name'),
                            $this->session->userdata('post_email'),
                            $this->session->userdata('post_phone_number'),
                            $this->session->userdata('post_zip_code'),
                            $this->session->userdata('post_sales_person'),
                           );
        }else{
            $params = array( $this->input->post('full_name'),
                            $this->input->post('email'),
                            $this->input->post('phone_number'),
                            $this->input->post('zip_code'),
                            $this->input->post('sales_person'),
                           );
            $this->session->set_userdata(array( 'post_full_name' => $this->input->post('full_name'),
                                                'post_email' => $this->input->post('email'),
                                                'post_phone_number' => $this->input->post('phone_number'),
                                                'post_zip_code' => $this->input->post('zip_code'),
                                                'post_sales_person' => $this->input->post('sales_person'),
                                               )
                                        );
        }
        $data['params'] = $params;
        $data['items'] = $this->estimates_m->getEstimateListLike($field, $params, $config['per_page'], $row);
        foreach($data['items'] as $key => $val){   
            $data['items'][$key]['rowClass'] = $this->_getRowClass($data['items'][$key]['sale_status'], $data['items'][$key]['completed'],$data['items'][$key]['closed']);
            $data['items'][$key]['pdf_hash'] = $this->estimates_m->getEstimateDetailField($data['items'][$key]['id'],'pdf_hash');
        }
        $config['total_rows'] = $this->estimates_m->searchCount;
        $pagetags = $this->_getPaginationTags();
        $config = array_merge($config, $pagetags);
        $this->pagination->initialize($config);
        
        
        //print_r($data['users']);
        //exit;
        //$data['allProps'] = $config['total_rows'];
        $data['header'] = 'User List';
        $data['links'] = $this->pagination->create_links();
        //for modal and modal2 divs in modal_users.php view
        $class = get_class();
        //pagination form sort/view options
        $data['self'] = 
        //get form items setup for modal's
        $data['mFormAction'] = $class . '/search/';
        $data['attr'] = array('id' => 'modal','name' => 'modal');
        $data['mFormAction2'] = $class . '/create';
        $data['attr2'] = array('id' => 'modal2','name' => 'modal2');
        $data['salespeople'] = $this->estimates_m->getAllSalesReps();// TODO add logic for inactive users with last mod of 2 weeks
        $data['orderby'] = 'id';
        
        $this->load->helper('form');
        $this->load->view('estimates/estimates',$data);
     
    }

    private function _getPaginationTags(){
        $config = array();
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = '&lt;&lt;';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = '&gt;&gt;';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&gt;';
        $config['next_tag_open'] = '<li>';
        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        return $config;
    }
    private function _getRowClass($sale_status,$completed,$closed){
        switch (TRUE){
            case ($sale_status == 1 && $completed == 1):
                return 'success';
            case ($sale_status == 2 && $completed == 1 && $closed == 1):
                return 'warning';    
            case ($completed == 0 && $closed == 1):
                return 'info';
            case ($completed == 0 && $closed == 0):
                return 'danger';
            case ($sale_status == 0 && $completed == 1):
            default:
                return '';
            break;
        }
        
        
        
        
    }
    
    private function _getBreadCrumb($stage, $type, $level){
        return $this->estimates_m->getBreadCrumb($stage, $type, $level);
    }
    
    private function _getBasePrices($type){
        return $this->estimates_m->getBasePrices($type);
    }
    
    private function _getNSetOrderBy($orderby){
        $downIcon = '<span aria-hidden="true" class="glyphicon glyphicon-arrow-down">';
        $upIcon = '<span aria-hidden="true" class="glyphicon glyphicon-arrow-up">';
        switch ($orderby){
            case 'n': case 'n_a':
                $this->estimates_m->orderby = 'full_name asc';
                $this->_colUri3 = 'n_d';
                $this->_th3Icon = $downIcon;
            break;
            case 'n_d':
                $this->estimates_m->orderby =  'full_name desc';
                //$this->_orderDir = 'desc';
                $this->_colUri3 = 'n';
                $this->_th3Icon = $upIcon;
            break;
            case 'p': case 'p_a':
                $this->estimates_m->orderby = $this->estimates_m->itemTable . '.phone_number asc';
                $this->_colUri2 = 'p_d';
                $this->_th2Icon = $downIcon;
            break;
            case 'p_d':
                $this->estimates_m->orderby = $this->estimates_m->itemTable . '.phone_number desc';
                //$this->_orderDir = 'desc';
                $this->_colUri2 = 'p';
                $this->_th2Icon = $upIcon;
            break;
            case 'd_a':
                $this->estimates_m->orderby = $this->estimates_m->itemTable . '.added asc';
                //$this->_orderDir = 'desc';
                $this->_colUri4 = 'd';
                $this->_th4Icon = $downIcon;
            break;
            case 'e_d':
                $this->estimates_m->orderby = $this->estimates_m->itemTable . '.email desc';
                //$this->_orderDir = 'desc';
                $this->_colUri1 = 'e';
                $this->_th1Icon = $upIcon;
            break;
            case 'e_a': case 'e':
                $this->estimates_m->orderby = $this->estimates_m->itemTable . '.email asc';
                $this->_colUri1 = 'e_d';
                $this->_th1Icon = $downIcon;
            break;
            case 's' : case 's_a' :
                $this->estimates_m->orderby = $this->estimates_m->itemTable . '.sales_person asc';
                $this->_colUri5 = 's_d';
                $this->_th5Icon = $downIcon;
            break;
            case 's_d' :
                $this->estimates_m->orderby = $this->estimates_m->itemTable . '.sales_person desc';
                $this->_colUri5 = 's';
                $this->_th5Icon = $upIcon;
            break;
            case 'd': case 'd_d':case '':
            default:
                $this->estimates_m->orderby = $this->estimates_m->itemTable . '.id desc';
                $this->_colUri4 = 'd_a';
                $this->_th4Icon = $upIcon;
        }
        return $orderby;
    }
    
    function test_json(){
        if($this->input->cookie('eid')){
        //if(1 === 1){ // use to statically test a specific estimate
            echo "<pre>";
            //$estInfo = $this->estimates_m->getEstimateBasic('13'); // use for statically testing a specific estimate
            $estInfo = $this->estimates_m->getEstimateBasic($this->input->cookie('eid'));
            $obj = json_decode($estInfo['running_data']);
            //$array = (array) $json;
            $array = json_decode(json_encode($obj),true);
            print_r($obj);
            exit;
        }else{
            echo "<pre>";
            echo "cookie not set!";
            exit;
        }
    }

}
