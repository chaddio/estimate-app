<?php
/*
 * do not copy this controller...doesn't have the necessary __construct method code to check for sessions, maybe be a TODO but not sure
 *
 */
class login extends CI_Controller {
    
    public function __construct() {        
        parent::__construct();
        //$this->benchmark->mark('code_start');
        //$this->out_of_order();
    }
    
            
    function index() {
        if( $this->session->userdata('register') ) {
            redirect('/login/register/flag');
        }elseif( $this->session->userdata('reset') ) {
            redirect('/login/reset_pwd/flag');
        }elseif( $this->session->userdata('isLoggedIn') ) {
            redirect('/main/');
        } else {
            $this->show_login(false);
        }
    }

    function show_login( $show_error = false,$alertLevel = 'danger' ) {
        $data['navTitle'] = 'Login';
        $data['error'] = $show_error;
        $data['alertLevel'] = $alertLevel;
        $data['formAction'] = 'login/login_user';
        $data['app_title'] = $this->config->item('app_title', 'config_app');
        $data['class'] = get_class();
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - ' . ucwords($data['class']);
        $this->load->helper('form');
        $this->load->view('login/login', $data);
        
    }
    
    function login_user() {
        // Create an instance of the user model
        $this->load->model('login_m');

        // Grab the email and password from the form POST
        $email = $this->input->post('email');
        $pass  = $this->input->post('password');

        //Ensure values exist for email and pass, and validate the user's credentials
        if( $email && $pass && $this->login_m->validateUser($email,$pass)) {
            
            if($this->session->userdata('reset')){
                redirect('/login/reset_pwd/flag');
            }elseif($this->session->userdata('register')){
                redirect('/login/register/flag');
            }elseif($this->input->cookie('request_uri')){
                //example to create cookie from other controller's, etc
                /*if(!$this->session->userdata('isLoggedIn')){
                    //$this->input->set_cookie(array('name' => 'request_uri','value' => $this->input->server('REQUEST_URI'),'expire' => '500','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
                    redirect('/login/timeout');
                }*/   
                $goto = $this->input->cookie('request_uri');
                $this->input->set_cookie(array('name' => 'request_uri','value' => '','expire' => '','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
                redirect($goto);
            }else{
                redirect('/main/');
            }
        } else {
            // Otherwise show the login screen with an error message.
            $this->show_login('Unable To Verify Email / Password Combination Provided');
        }
    }
    
    function reset_pwd($show_error = false, $alertLevel = 'danger'){
        if(!$this->session->userdata('isLoggedIn')) {
            redirect('/');
        }
        if($show_error == 'flag'){
            $data['error'] = 'You have a temporary password and need to change it to proceed';
            $data['alertLevel'] = 'info'; //error, info, success = red, blue, green
        }else{
            $data['error'] = $show_error;
            $data['alertLevel'] = $alertLevel;
        }
        $data['navTitle'] = 'Change Password';
       
        $data['formAction'] = 'login/update_pwd';
        $data['viewTitle'] = 'Change Password - <em>' . ucwords($this->session->userdata('name')) . '</em>';
        $data['class'] = get_class();
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - Change Password';
        $data['disableHelp'] = TRUE;
        $this->load->helper('form');
        $this->load->view('login/change_pwd', $data);
        //echo "Under Construction:  Reset perm Password after a password reset (temp passwd) was inintiated <br /><a href='javascript: void(0);' onclick='javascript: history.go(-1);'>BACK</a>";
        //exit;
        
    }
    
    function register($show_error = false, $alertLevel = 'danger'){
        if(!$this->session->userdata('isLoggedIn')) {
            redirect('/');
        }
        if($show_error == 'flag'){
            $data['error'] = 'Please fill out all fields to get started';
            $data['alertLevel'] = 'info'; //error, info, success = red, blue, green
        }else{
            $data['error'] = $show_error;
            $data['alertLevel'] = $alertLevel;
        }
        $data['navTitle'] = 'Register';
        
        $data['formAction'] = 'login/register_complete';
        $data['viewTitle'] = 'Register - <em>' . $this->session->userdata('email') . '</em>';
        $data['class'] = get_class();
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - Register';
        $data['disableHelp'] = TRUE;
        $this->load->model('login_m'); 
        $data['formData'] = $this->login_m->getEmptyFormData(__METHOD__);
        
        //$data = array_merge($data,$form_data);
        $this->load->helper('form');
        $this->load->view('login/register', $data);
        //echo "Under Construction:  Reset perm Password after a password reset (temp passwd) was inintiated <br /><a href='javascript: void(0);' onclick='javascript: history.go(-1);'>BACK</a>";
        //exit;
        
    }
    
    function register_complete(){
         if(!$this->session->userdata('isLoggedIn')) {
            redirect('/');
        }
        //print_r($_POST);
        //exit;
        
        // Create an instance of the login model
        $this->load->model('login_m');
        $this->load->library('form_validation');
        //use the class::method action with config/form_validation.php for subsetted validation
        if(!$this->form_validation->run(__METHOD__)){
            $this->register('invalid');
        }elseif($this->login_m->setRegistration()){
            if($this->session->userdata('skippasswd')){
                $this->session->unset_userdata('skippasswd');
                redirect('/main/');
            }else{
                $this->input->set_cookie(array('name' =>'welcome','value' => '1','expire' => '86500','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
                $this->reset_pwd('Registration almost complete. <br>You must update your temporary password to a new one.', 'info');
            }
            
        }
       
    }
    
    function update_pwd(){  
        $this->load->model('login_m');
        $this->load->library('form_validation');
        // Grab the password, and new password from the form POST
        $password = $this->input->post('password');
        $newPassword  = $this->input->post('newPassword');
        $confirmPassword  = $this->input->post('confirmPassword');
        
        //check passwords match and form validation, match is done via jquery first but still just in case somehow js is disabled on browser and not checked
        if($newPassword != $confirmPassword){
            $this->reset_pwd('New Password and Confirm Password did not match, please try again');
        }elseif(!$this->form_validation->run(__METHOD__)){
            $this->reset_pwd('invalid'); // *** used on view to display errors: echo validation_errors()
        }else{
            if($password && $newPassword && $confirmPassword && $this->login_m->setNewPassword($password,$newPassword)){
                redirect('/main/index/pwdchgd');
            }  else {
                $this->reset_pwd($this->login_m->passWordMsg);
            }
        }
       
    }
    
    function forgot_pwd($show_error = false){
        if(!$show_error){
            $data['error'] = 'Enter your email/username and click next to proceed';
            $data['alertLevel'] = 'info'; //error, info, success = red, blue, green
        }else{
            $data['error'] = $show_error;
            $data['alertLevel'] = 'danger'; //danger, info, success = red, blue, green
        }
        $data['navTitle'] = 'Forgot Password';
        $data['viewTitle'] = $data['navTitle'];
        $data['formAction'] = 'login/forgot_send';
        $data['loginNavDropdown'] = TRUE;
        $data['class'] = get_class();
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - Forgot Password';
        //$data['disableHelp'] = TRUE; used to only show logout option nav-login
        $data['addtDropLinks'] = array(array('href' => '/', 'icon' => 'glyphicon glyphicon-home', 'label' => 'Return Home'));
        $this->load->helper('form');
        $this->load->view('login/forgot_pwd', $data);
        
    }
    
    function forgot_send(){
        $this->load->model('login_m');
        $this->load->library('form_validation');
        // Grab the email from the form POST
        $email = $this->input->post('email');
        //use the class::method action with config/form_validation.php for subsetted validation
        if(!$this->form_validation->run(__METHOD__)){
            $this->forgot_pwd('invalid');
            return;
        }
        if($this->login_m->setForgotPasswd($email)){
            $this->show_login('Password Recovery information was sent to: ' . $email, 'success');
        }else{
            $this->forgot_pwd($this->login_m->passWordMsg);
        }
        
    }
    
    function recover($reset_hash = FALSE, $show_error = FALSE, $alertLevel = 'danger'){
        if(!$reset_hash){
            redirect('/');
        }
        
        
        $this->load->model('login_m');
        if($this->login_m->checkResetHash($reset_hash)){
            $data['email'] = $this->login_m->emailuser;
            if($show_error){
                $data['error'] = $show_error;
                $data['alertLevel'] = $alertLevel;//error, info, success = red, blue, green
            }else{
                $data['error'] = 'Set a new password for ' . $data['email'];
                $data['alertLevel'] = 'info';//error, info, success = red, blue, green
            }
            
            
            $data['reset_hash'] = $reset_hash;
            $data['navTitle'] = 'Password Recover / Reset';
            $data['addtDropLinks'] = array(array('href' => '/', 'icon' => 'icon-home', 'label' => 'Return Home'));
            $data['formAction'] = 'login/set_recover_pwd';
            $data['viewTitle'] = 'Set New Password';
            $data['class'] = get_class();
            $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - Set New Password';
            //$data['disableHelp'] = TRUE;
            $this->load->helper('form');
            $this->load->view('login/recover_pwd', $data);
            //echo "Under Construction:  Reset perm Password after a password reset (temp passwd) was inintiated <br /><a href='javascript: void(0);' onclick='javascript: history.go(-1);'>BACK</a>";
            //exit;
        }else{
            $this->show_login($this->login_m->passWordMsg);
        }
        
        
    }
    
    function set_recover_pwd(){  
        // Create an instance of the user model
        $this->load->model('login_m');
        $this->load->library('form_validation');
        // Grab the password, and new password from the form POST
        //$password = $this->input->post('password');
        $email = $this->input->post('email');
        $reset_hash = $this->input->post('reset_hash');
        $newPassword  = $this->input->post('newPassword');
        $confirmPassword  = $this->input->post('confirmPassword');
        
        //check passwords match and form validation, match is done via jquery first but still just in case somehow js is disabled on browser and not checked
        if($newPassword != $confirmPassword){
            $this->recover($reset_hash, 'New Password and Confirm Password did not match, please try again');
        //use the class::method action with config/form_validation.php for subsetted validation
        }elseif(!$this->form_validation->run(__METHOD__)){
            $this->recover($reset_hash, 'invalid'); // *** used on view to display errors: echo validation_errors()
        }else{
            //if true, also runs login_m->validateUser()
            if($this->login_m->setRecoverPassword($email,$newPassword,$reset_hash)){ //
                if($this->session->userdata('register')){
                    $this->session->set_userdata('skippasswd', TRUE);//set to flag for not usual password change
                    redirect('/login/register/flag');
                }else{
                    redirect('/main/index/pwdrec');
                }
            }else {
                $this->recover($reset_hash, $this->login_m->passWordMsg);
            }
        }
       
    }

    function logout() {
        $this->session->sess_destroy();
        $this->show_login('You have been logged out', 'info');
    }
    
    function timeout() {
        $this->session->sess_destroy();
        if($this->input->cookie('request_uri')){
            $this->show_login('Please login to access the requested link.', 'info');
        }else{
            $this->show_login('Your session has expired, please log back in');
        }
    }
    
    function show_php_info(){
        $this->showphpinfo();
    }
    
    function php_info(){
        $this->showphpinfo();
    }

    function showphpinfo() {
        if(ENVIRONMENT != 'production'){
            echo phpinfo();
        }else{
            show_404();
        }
    }
    
    function csrf_error(){
        $this->show_login('You login/form cookie has expired, please login again. Thank you.','danger');
    }
    
    function out_of_order(){
        echo 'Sorry, ' . $this->config->item('app_title','config_app') . ' is currently down for maintenance, please check back shortly';
        exit;
    }

}
