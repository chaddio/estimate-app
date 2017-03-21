<?php
/* @TODO clean out old property class methods and code */ 
class users extends CI_Controller{
    
    protected $_resultsPerPage = CI_APP_ROWS;
    private $_orderDir = 'asc';
    private $_colUri1 = 'e';
    private $_colUri2 = 'n';
    private $_th1Icon = '';
    private $_th2Icon = '';
    public function __construct()
    {
        parent::__construct();
        //$this->benchmark->mark('code_start');
        switch(FALSE){
            case $this->session->userdata('isLoggedIn') : 
            case ($this->session->userdata('app_userlevel') == 0) :
                redirect('/login/timeout');
            break;
                
        }
        $this->load->model('users_m');

    }
    
    function index($active = 1, $orderby = 'i', $row = 0,  $show_error = FALSE, $alertLevel = 'info') {
        
        // Get some data from the user's session
        $data = array();
        $data['mobile'] = $this->users_m->getAppMobileStatus();
        $data['row'] = $row;
        $data['active'] = $active;
        $data['error'] = $show_error;
        $data['alertLevel'] = $alertLevel;
        $data['email'] = $this->session->userdata('email');
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ',$data['name']);
        $data['fname'] = $splitName[0];
        $data['serviceCompany'] = $this->config->item('company', 'config_app');
        $data['appTitle'] = $this->config->item('app_title', 'config_app');
        $data['class'] = get_class();
        $data['navTitle'] = ucfirst($data['class']);
        $data['viewTitle'] = 'Manage ' . ucfirst($data['class']);
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
        //$this->users_m->isAdmin = $data['isAdmin']; //model loaded from __construct
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
        $data['thI1'] = $this->_th1Icon;
        $data['thI2'] = $this->_th2Icon;
        
        //configs for pagination
        $config['base_url'] = base_url() . $data['class'] . '/' . $method . '/' . $active . '/' . $data['orderby'] . '/';
        $config['per_page']= $this->_resultsPerPage;
        $config['total_rows'] = $this->users_m->getUserCountWhere('active',$active);
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
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        //$config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);
     
        $data['users'] = $this->users_m->getUsersWhere('active',$active,$config['per_page'], $row);
        //print_r($data['users']);
        //exit;
        //$data['allProps'] = $config['total_rows'];
        $data['header'] = 'User List';
        $data['links'] = $this->pagination->create_links();
        //for modal and modal2 divs in modal_users.php view
        $class = get_class();
        //pagination form sort/view options
        //get form items setup for modal_users view
        $data['mFormAction'] = $class . '/search/';
        $data['attr'] = array('id' => 'modal','name' => 'modal','class' => 'form-horizontal');
        $data['mFormAction2'] = $class . '/create';
        $data['attr2'] = array('id' => 'modal2','name' => 'modal2','class' => 'form-horizontal');
        $data['userlevels'] = $this->users_m->getUserLevels();
        
        $this->load->helper('form');
        $this->load->view('users/users',$data);
    }

    function edit($id, $show_error = false, $alertLevel = 'danger'){
        if($show_error == 'flag'){
            $data['error'] = 'Please fill out all fields to get started';
            $data['alertLevel'] = 'info'; //error, info, success = red, blue, green
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
        $data['id'] = $id;
        $data['app_pages'] = $this->session->userdata('app_pages');
        $data['formAction'] = 'users/update';
       
        $data['class'] = get_class();
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - Edit User #' . $id;
        //$data['disableHelp'] = TRUE;
        $orderby = 'i';
        $data['orderby'] = $this->_getNSetOrderBy($orderby);
        $data['formData'] = $this->users_m->getEditFormData(__METHOD__, $id);
        //object created from above to use as formData has data but in a wrapped,form helper format 
        $data['userObject'] = $this->user = $this->users_m->user_detail;
        //print_r($this->user);
        //exit;
        $data['viewTitle'] = 'Edit User - <em>' . $this->user->email . '</em>';
        $this->load->helper('form');
        $this->load->view('users/user_edit', $data);
        
    }
    
    public function create(){
        $this->load->library('form_validation');
        $row = $this->input->post('row');
        $active = $this->input->post('active');
        $email = $this->input->post('email');
        $orderby = $this->input->post('orderby');
        if($this->form_validation->run(__METHOD__)){
            //$this->load->model('users_m');
            $result = $this->users_m->setNewUser();
            if($result){
                $this->form_validation->unset_field_data(); 
                $this->index($active, $orderby, $row,"User Added! ... An email has been sent to <strong>$email</strong> for further instructions to activate their account",'success');
            }else{
                $this->index($active,$orderby, $row,"User not added ... user already exists or is deactivated",'danger');
            }
        }else{
            $this->index($active,$orderby, $row,'invalid', 'danger');
        }

    }

    public function update(){
        //$this->load->helper(array('form','url'));
        $this->load->library('form_validation');

       
      if($this->form_validation->run(__METHOD__)){
          if($this->users_m->setUserUpdate()){
              $detail = $this->users_m->getDetailsWhere('id',$this->input->post('login_id'),'login');
              $this->index(1, 'i', 0, 'User: \'' . $detail->email . '\' was updated');
          }else{
              $this->edit($this->input->post('login_id'), 'Something whacky happened when trying to update user', 'alert');
          }
      }else{
          $this->edit($this->input->post('login_id'), 'invalid', 'danger');
      }
         //$this->add($p,'You messed up dude');

    }

    public function delete($login_id,$row = 0){
        //$this->load->model('users_m');
        $return = $this->users_m->setUserDeactivated($login_id);
        if($return){
            $this->index(1, '', $row, $this->users_m->msg,'danger');//first param is show/hide 'active' 
        }else{
            echo "There was a problem";
            print_r($return);
            exit;
        }
    }
    
    public function reset_pwd($login_id, $row = 0){
        $return = $this->users_m->setResetPasswd($login_id);
        if($return){
            $this->index(1, '', $row, $this->users_m->msg,'danger');//first param is show/hide 'active' 
        }else{
            $this->edit($login_id, $this->users_m->msg, 'danger');
            //echo "There was a problem";
            //print_r($return);
            //exit;
        }
    }
    
    public function activate($login_id,$row = 0){
        //$this->load->model('users_m');
        $return = $this->users_m->setUserActive($login_id);
        if($return){
            $this->index(0,'', $row, $this->users_m->msg,'info');
        }else{
            echo "There was a problem";
            print_r($return);
            exit;
        }
    }
    
    
    function search($row=0, $show_error = FALSE, $alertLevel = 'info') {
    
        // Get some data from the user's session
        $data = array();
        $data['mobile'] = $this->users_m->getAppMobileStatus(); //TODO restructure how this is determined more globally
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
        $data['navTitle'] = 'Search Users';
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
        //$this->load->model('users_m');
        $this->users_m->isAdmin = $data['isAdmin'];
        //set up pagination for the view
        $this->load->library('pagination');
        //declare $method for pagination base url - called 
        $classMethod = __METHOD__;
        $expl = explode('::', $classMethod);
        $method = $expl[1];
        //configs for pagination
        $config['base_url'] = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/' . $data['class'] . '/' . $method . '/';
        $config['per_page']= $this->_resultsPerPage;
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
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        //$config['use_page_numbers'] = TRUE;
        //$config['uri_segment'] = 4;
        $field = array('CONCAT(' . $this->users_m->profileTable . ".first_name,  ' ', " . $this->users_m->profileTable . ".last_name )",$this->users_m->userTable . '.email',$this->users_m->profileTable . '.phone_number',$this->users_m->profileTable . '.city',$this->users_m->profileTable . '.zip_code');
        $params = array($this->input->post('full_name'),$this->input->post('email'),$this->input->post('phone_number'),$this->input->post('city'),$this->input->post('zip_code'));
        if(!$this->input->post()){
            $params = array( $this->session->userdata('post_full_name'),
                            $this->session->userdata('post_email'),
                            $this->session->userdata('post_phone_number'),
                            $this->session->userdata('post_city'),
                            $this->session->userdata('post_zip_code')
                           );
        }else{
            $params = array( $this->input->post('full_name'),
                            $this->input->post('email'),
                            $this->input->post('phone_number'),
                            $this->input->post('city'),
                            $this->input->post('zip_code')
                           );
            $this->session->set_userdata(array( 'post_full_name' => $this->input->post('full_name'),
                                                'post_email' => $this->input->post('email'),
                                                'post_phone_number' => $this->input->post('phone_number'),
                                                'post_city' => $this->input->post('city'),
                                                'post_zip_code' => $this->input->post('zip_code')
                                               )
                                        );
        }
        $data['params'] = $params;
        $data['users'] = $this->users_m->getUserListLike($field, $params, $config['per_page'], $row);
        $config['total_rows'] = $this->users_m->searchCount;
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
        $data['attr'] = array('id' => 'modal','name' => 'modal','class' => 'form-horizontal');
        $data['mFormAction2'] = $class . '/create';
        $data['attr2'] = array('id' => 'modal2','name' => 'modal2','class' => 'form-horizontal');
        $data['userlevels'] = $this->users_m->getUserLevels();
        $data['orderby'] = 'id';
        
        $this->load->helper('form');
        $this->load->view('users/users',$data);
     
    }
    
    private function _getNSetOrderBy($orderby){
        $downIcon = '<span aria-hidden="true" class="glyphicon glyphicon-arrow-down"></span>';
        $upIcon = '<span aria-hidden="true" class="glyphicon glyphicon-arrow-up"></span>';
        switch ($orderby){
            case 'n': case 'n_a':
                $this->users_m->orderby = 'full_name asc';
                $this->_colUri2 = 'n_d';
                $this->_th2Icon = $downIcon;
            break;
            case 'n_d':
                $this->users_m->orderby = 'full_name desc';
                $this->_orderDir = 'desc';
                $this->_colUri2 = 'n';
                $this->_th2Icon = $upIcon;
            break;
            case 'e_d':
                $this->users_m->orderby = $this->users_m->userTable . '.email desc';
                $this->_orderDir = 'desc';
                $this->_colUri1 = 'e';
                $this->_th1Icon = $upIcon;
            break;
            case 'e_a': case 'e':
                $this->users_m->orderby = $this->users_m->userTable . '.email asc';
                $this->_colUri1 = 'e_d';
                $this->_th1Icon = $downIcon;
            break;
            case 'i_a': case 'i':case '':
            default:
                $this->users_m->orderby = $this->users_m->profileTable . '.login_id';
                $orderby = 'i';
        }
        return $orderby;
    }

}
