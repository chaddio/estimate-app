<?php


class login_m extends CI_Model {
    
    public $login;
    public $profile;
    public $reset_data;
    public $userlevels;
    public $passWordMsg;
    public $emailuser;
    public $results;
    
    private $_table = 'login';
    private $_appUserTable = 'profile';
    private $_appPagesTable = 'app_pages';
    private $_reset_hash;
    
    function __construct() {
        parent::__construct();
    }
/**
 * 
 * @param type $email
 * @param type $password
 * @return boolean
 */
    function validateUser( $email, $password ) {
        // Build a query to retrieve the user's details
        // based on the received username and password
        $this->db1->from($this->_table);
        $this->db1->where('email',$email );
        $this->db1->where('active', 1);
        $this->db1->where( 'password', sha1($password) );
        $login = $this->db1->get()->result();

        // The results of the query are stored in $login.
        // If a value exists, then the user account exists and is validated
        if ( is_array($login) && count($login) == 1 ) {
            // Set the users details into the $details property of this class
            $this->login = $login[0];
            // Call set_session to set the user's session vars via CodeIgniter
            $this->setSession();
            return true;
        }

        return false;
    }
    /**
     * set a new password is for a user to reset his/her password
     * @param string $password
     * @param string $newPassword
     * @return boolean
     */
    function setNewPassword($password, $newPassword){
        $email = $this->session->userdata('email');
        $this->db1->from($this->_table);
        $this->db1->where('email',$email );
        $this->db1->where('active', 1);
        $this->db1->where( 'password', sha1($password) );
        $results = $this->db1->get()->result();
        if ( is_array($results) && count($results) == 1 ) {
            if($password == $newPassword){
                $this->passWordMsg = 'Your new password cannot be the same as your temporary/current one';
                return false;
            }
            $this->db1->set('password', sha1($newPassword));
            $this->db1->set('reset', 0);
            $this->db1->where('email',$email );
            $this->db1->where('password', sha1($password) );
            $this->db1->update($this->_table);
            $this->session->unset_userdata('reset');
            return true;
        }
        $this->passWordMsg = 'Your current password could not be verified, please try again';
        return false;
    }
    
    function setRecoverPassword($email, $newPassword, $resetHash){
        $this->db1->from($this->_table);
        $this->db1->where('email',$email );
        $this->db1->where('reset_hash', $resetHash);
        $this->db1->where('active', 1);
        $results = $this->db1->get()->result();
        if ( is_array($results) && count($results) == 1 ) {
            $this->db1->set('password', sha1($newPassword));
            $this->db1->set('reset_hash', '');
            $this->db1->set('reset', 0);
            $this->db1->set('modified','CURRENT_TIMESTAMP', FALSE );
            $this->db1->where('email',$email );
            $this->db1->where('reset_hash',$resetHash );
            $this->db1->update($this->_table);
            $this->validateUser($email,$newPassword);
            return true;
        }
        $this->passWordMsg = 'There was a problem, please restart the Forgot Password process.<br> if this problem, persists contact the System Administrator';
        return false;
    }
    /**
     *
     * @param array $params
     * @return array
     * _getLoginInformation use internally for verifying/validating a user based on criteria
     */
    private function _getLoginInformation($params = array()){
        if(count($params) > 1){
            foreach($params as $key => $value){
                $this->db1->where($params["$key"], $params["$value"]);
            }
        }
        $this->db1->from($this->_table);
        $results = $this->db1->get()->result();
        return $results;
        
    }
    
    function checkResetHash($reset_hash){
        $falseaction = '<br/ >Click "Forgot Password" under Login Help to resend';
        $this->db1->where('reset_hash', $reset_hash);
        $this->db1->where('active', '1');
        $this->db1->from($this->_table);
        $results = $this->db1->get()->result();
        if ( is_array($results) && count($results) == 1 ){
            $this->results = $results[0]; //drop array to object
        }else{
            $this->passWordMsg = 'Unable to verify your password recovery link.' . $falseaction;
            return false;
        }
        $this->load->helper('date');
        $now = now();
        $reset_timestamp = human_to_unix($this->results->modified);
        $this->emailuser = $this->results->email;
        if(($now - $reset_timestamp) > 3600){ // check to make sure the forgot password reset hasn't been done more than an hour ago
            $this->passWordMsg = 'Your Forgot Password link has expired.' . $falseaction;
            return false;    
        }
        return true;
        
    }
    
    function setForgotPasswd($email){
        $this->db1->from($this->_table);
        $this->db1->where('active', 1);
        $this->db1->where('email',$email );
        $results = $this->db1->get()->result();
        if ( is_array($results) && count($results) == 1 ) {
            $this->load->helper('string');
            //$randPass = random_string('alnum', 8);
            $reset_hash = random_string('unique');
            //$this->db1->set('password', sha1($randPass));
            $this->db1->set('reset_hash', $reset_hash);
            $this->db1->set('reset', 0);
            $this->db1->set('modified','CURRENT_TIMESTAMP', FALSE );
            $this->db1->where('email',$email );
            $this->db1->update($this->_table);
            $this->_reset_hash = $reset_hash;
            //$this->_randPass = $randPass;
            $this->_sendForgotPasswdEmail($email);
            return true;
        }
        $this->passWordMsg = 'Your email could not be verified, please contact a website administrator';
        return false;
    }
    
    private function _sendForgotPasswdEmail($email){
        $this->load->library('email');
    
        $config['wrapchars'] = '130';
        
        $this->email->initialize($config);
        
        //TODO change static to config variables (2 lines)
        $coAndApp = $this->config->item('company', 'config_app') . ' '  . $this->config->item('app_title', 'config_app');
        $from = $this->config->item('auto_email', 'config_app');
        $this->email->from($from, $coAndApp);
        $this->email->reply_to($from, $coAndApp);
        $this->email->to($email); 
        //$this->email->cc('another@another-example.com'); 
        //$this->email->bcc('them@their-example.com'); 
            
        $this->email->subject('Forgot Password Reset');
        //TODO change to base_url() 
        $site = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        $this->email->message( 'A password reset/recovery was initiated from ' .$site . ' for the following user/email: ' . "\n" . $email . "\n\n" .
                                'If you did not initiate a password recovery/reset, you may disregard this message. If you keep getting these messages, please contact the program administrator. 
                                ' . "\n" . 'This password reset link will expire after 1 hour. Please click below to start the password recovery:'  . "\n" . $site  . '/login/recover/' . $this->_reset_hash);	

        $this->email->send();
        log_message('debug', "Forgot Password email sent to $email from " . $_SERVER['REMOTE_ADDR']);
        //log_message('debug', $this->email->print_debugger() . "\n");
        
    }
    /**
     * 
     * @return boolean
     */
    function setRegistration(){
        //return if there's no post data, nothing to do
        if(!isset($_POST)){
            return false;
        }
        $this->load->model('form_m');
        //reassign POST data to a new array and drop unnessary submit and other elements
        $data = $this->form_m->transformPost();
        //update the app profile table
        $this->db1->set('modified','CURRENT_TIMESTAMP', FALSE );
        $this->db1->set('modified_by', $this->session->userdata('login_id'));
        $this->db1->where('login_id', $this->session->userdata('login_id') );
        $this->db1->update($this->_appUserTable,$data);
        //update login table
        $this->db1->set('register', 0);
        $this->db1->where('id', $this->session->userdata('login_id') );
        $this->db1->update($this->_table);
        $this->session->unset_userdata('register');
        $this->session->set_userdata('name', ucwords($data['first_name'] . ' ' . $data['last_name']));
        //print_r($data);
        //exit;
        
       
        
        return true;
    }
/**
 * setSession sets session vars once a user is validated, this does not return anything
 * this should only be used after a proper user/password/active validation has been done
 */
    function setSession() {
        $this->db1->from('profile');
        $this->db1->where('login_id', $this->login->id );
        $profile = $this->db1->get()->result();
        if ( is_array($profile) && count($profile) == 1 ) {
            $this->profile = $profile[0];
            $this->profile->appPages = $this->getAppData('app_pages');
            $userNav = $this->setUserNavigation($this->profile->appPages);
            $this->profile->app_userlevel = $this->getAppData('userlevel');
            $this->session->set_userdata( array(
                    'login_id' => $this->login->id,
                    'app_pages' => $userNav,
                    'app_userlevel' => $this->profile->app_userlevel,
                    'name' => $this->profile->first_name . ' ' . $this->profile->last_name,
                    'email' => $this->login->email,
                    'isLoggedIn' => true,
                )
            );
            //only set TRUE if marked in the Database as 1, otherwise session vars should not be set
            if($this->login->reset == 1){
                $this->session->set_userdata('reset', TRUE);
            }
            if($this->login->register == 1){
                $this->session->set_userdata('register', TRUE);
            }
            
        }
        
    }
    
    function getAppData($field){
        //return '1';
        $table = $this->_appUserTable;
        $this->db2->from($table);
        $this->db2->where('login_id', $this->login->id );
        $query = $this->db2->get()->result();
        return $query[0]->$field;
    }

    public function update_tagline( $user_id, $tagline ) {
      $data = array('tagline'=>$tagline);
      $result = $this->db1->update('user', $data, array('id'=>$user_id));
      return $result;
    }
    private function setUserNavigation( $appPages ){
        //$appPages is stored as a comma seperated string
        
        $explodeAppPages = explode(',', $appPages);
        $table = $this->_appPagesTable;
        
        $pages = array();
        foreach($explodeAppPages as $page){
            $this->db2->from($table);
            $this->db2->where('id', $page);
            $query = $this->db2->get()->result();
            $pages[$page] = array('nav_heading' => $query[0]->nav_heading,
                                  'controller' => $query[0]->controller,
                                  'nav_title' => $query[0]->nav_title,
                                  'icon_css' => $query[0]->icon_css,
                                  'description' => $query[0]->description
                                );
        }
        
        
        return $pages;
    }

    /**
     * 
     * @param string $method should be class::method  '__METHOD__' constant called from controller
     * @return array
     */
    public function getEmptyFormData($method){
        $this->load->model('form_m');
        $return = $this->form_m->getEmptyFormData($method);
        return $return;
    }
    
}
