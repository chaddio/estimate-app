<?php

class users_m extends CI_Model {
    //private $_dfltInputClass = 'span4';
    //private $_dfltChkClass = '';
    //private $_stateSel;
    //below 2 are deprecated, need to use public property to maintain access level
    //private $_userTable = 'login'; //deprecated
    //private $_profileTable = 'profile'; //deprecated
    private $_dfltAppPages = '1';
    protected $_userPasswd;
    protected $_userId;
    public $searchCount;
    public $results;
    public $isAdmin;
    public $locked = FALSE;
    public $row;
    public $msg;
    public $orderby;
    //added for public use by controller
    public $userTable = 'login';
    public $profileTable = 'profile';
    public $user_detail;
    
    
    public function __construct() {
        parent::__construct();
        //$this->load->library('session'); //loads MY_Session for multiple database config
    }
    
    function getAllUsers(){
        $query = $this->db1->get($this->userTable);
        if($query->num_rows() > 0){
            // return result set as an associative array
            return $query->result_array();
        }
    }
    
    function getUserLevels(){
        $this->db1->order_by('userlevel');
        $query = $this->db1->get('userlevels');
        return $query->result_array();
    }
    
    /**
     * 
     * @param type $field
     * @param type $param
     * @param type $limit
     * @param type $offset
     * @return type
     */
    function getUsersWhere($field,$param,$limit=100,$offset=0){
        $where = '';
        if(is_array($field) && is_array($param)){
            $i = count($field);
            foreach($field as $key => $value){
                if($i == 0){
                    $where .= $field[$key] . " = " . $param[$key]; 
                }else{
                    
                    $where .= $field[$key] . " = " . $param[$key] . ', ';
                }
            $i--;
            }
        }else{
            $where .= $field . " = " . $param;
        }
        $strQuery = "SELECT $this->userTable.id,$this->userTable.email,CONCAT($this->profileTable.first_name,' ',$this->profileTable.last_name) as `full_name` FROM " . $this->userTable . "," . $this->profileTable . " WHERE $where and $this->userTable.id = $this->profileTable.login_id ORDER BY $this->orderby LIMIT $offset,$limit";
        $query = $this->db1->query($strQuery);
        return $query->result_array();
    }
    
    function getUserDetail($id){
        $strQuery = "SELECT $this->profileTable.added,$this->profileTable.modified,$this->userTable.active,$this->userTable.userlevel,$this->profileTable.login_id,$this->userTable.email,$this->profileTable.first_name,$this->profileTable.last_name,$this->profileTable.address,$this->profileTable.city,$this->profileTable.state,$this->profileTable.zip_code,CONCAT('(',left($this->profileTable.phone_number,3),') ',substring($this->profileTable.phone_number,4,3),'-',right($this->profileTable.phone_number,4)) as `phone_number` FROM " . $this->userTable . "," . $this->profileTable . " WHERE $this->userTable.id = $id and $this->userTable.id = $this->profileTable.login_id";
        $query = $this->db1->query($strQuery);
        $user_detail = $query->result();
        //creates object to be used outside of result array
        $this->user_detail = $user_detail[0];
        return $query->result_array();
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
        $return = $query[0];
        // return result as object
        return $return;
    }
    /**
     * @$field
     *
     * must have $field and $param with the same number of array elements if sending as arrays
     * also can send params as strings
     */
    function getUserListLike($field,$param,$limit=100,$offset=0){
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
            $this->db1->like($field,$param);
        }
        $limit = " LIMIT " . $offset . ',' . $limit;
        $strQuery = "SELECT $this->userTable.id,$this->userTable.active,$this->userTable.email,CONCAT($this->profileTable.first_name,' ',$this->profileTable.last_name) as `full_name`,$this->profileTable.phone_number,$this->profileTable.city,$this->profileTable.state,$this->profileTable.zip_code FROM " . $this->userTable . "," . $this->profileTable . " WHERE $like and $this->userTable.id = $this->profileTable.login_id";
        $query = $this->db1->query($strQuery . ' ' . $limit);
        //$query = $this->db1->get($this->userTable,$limit,$offset);
        // return result set as an associative array
        $cntQuery = $this->db1->query($strQuery);
        $this->searchCount = count($cntQuery->result_array());
        return $query->result_array();
    }
    
    function getAllUserCount(){
        return $this->db1->count_all($this->userTable);
    }
    /**
     * 
     * @param array or string $field
     * @param array or string $param
     * @return int
     */
    function getUserCountWhere($field='', $param=''){
        if(is_array($field) && is_array($param)){
            foreach($field as $key => $value){
                $this->db1->where($field[$key],$param[$key]);
            }
        }elseif($field == '' && $param == ''){
            return $this->db1->count_all($this->userTable);
        }else{
            $this->db1->where($field,$param);
        }
        $query = $this->db1->get($this->userTable);
        
        return $query->num_rows();
    }
    /**
     * 
     */
    //
    function getAppMobileStatus(){
        $this->load->library('user_agent');
        return $this->agent->isPhone;//placed in MY_User_agent.php
    }
// get total number of users}
    function getNumUsersWhere($field='', $value=''){
        if($field && $value){
            $this->db1->where($field,$value);
        }
        return $this->db1->count_all($this->userTable);
    }
  
    function setLocked($id, $locked){
        $this->db1->set('locked', $locked, FALSE );
        $this->db1->where('id', $id);
        $this->db1->update($this->userTable);
    }
    
    function setUserUpdate(){
        //return if there's no post data, nothing to do
        if(!isset($_POST)){
            return false;
        }
        $this->load->model('form_m');
        //reassign POST data to a new array and drop unnessary submit and other elements
        $data = $this->form_m->transformPost();
        $userlevel = $data['userlevel'];
        
        unset($data['userlevel']);
        //update the app profile table
        $this->db1->set('modified','CURRENT_TIMESTAMP', FALSE );
        $this->db1->set('modified_by', $this->session->userdata('login_id'));
        $this->db1->where('login_id', $data['login_id'] );
        $this->db1->update($this->profileTable,$data);
        //update login table
        $this->db1->set('userlevel', $userlevel);
        $this->db1->where('id', $data['login_id'] );
        $this->db1->update($this->userTable);
        //update app profile table
        $this->db2->set('userlevel', $userlevel);
        $this->db2->where('login_id', $data['login_id'] );
        $this->db2->update($this->profileTable);
        return true;
    }
    
    /**
     * Sets/creates a new user, in both the login database and app
     * @param string $email
     * @return bool
     */
    function setNewUser(){
        $fields['email'] = $this->input->post('email');
        $fields['userlevel'] = $this->input->post('userlevel');
        $this->db1->where('email', $fields['email']);
        $query = $this->db1->get($this->userTable);
        if($query->num_rows() > 0){
            return FALSE;
        }
        $this->load->helper('string');
        $this->_userPasswd = random_string('alnum', 8);
        $fields['password'] = sha1($this->_userPasswd);
        //$reset_hash = random_string('unique');  
        //$fields['phone'] = preg_replace('/[\(\)\-]/','',$this->input->post('phone'));
        //$fields['fax'] = preg_replace('/[\(\)\-]/','',$this->input->post('fax'));
        
        //first table add
        $this->db1->set('modified', 'NOW()', FALSE );
        $this->db1->set('register', 1, TRUE);
        $this->db1->set('reset', 0, TRUE);
        $this->db1->insert($this->userTable, $fields);
        //first table add
        $this->_userId = $this->db1->insert_id();
        
        //setup for 2nd table add
        $this->db1->set('modified', 'NOW()', FALSE );
        $this->db1->set('added', 'NOW()', FALSE );
        $this->db1->set('login_id', $this->_userId);
        $this->db1->set('added_by',$this->session->userdata('login_id'));
        $this->db1->set('modified_by', $this->session->userdata('login_id'));
        $this->db1->insert($this->profileTable);
        
        //setup for 3rd table, app db userlevel
        $this->db2->set('updated', 'NOW()', FALSE );
        $this->db2->set('login_id', $this->_userId);
        $this->db2->set('app_pages', $this->_dfltAppPages);
        $this->db2->set('userlevel', $fields['userlevel']);
        $this->db2->insert($this->profileTable);
        
        $this->_sendSetNewUserEmail($fields['email']);
        
        
        return true;
        
    }
    /**
     * Send new user email with the optional reActivation (skipping registration) and 
     * sending a different message body
     * @param bool $reActivate 
     */
    private function _sendSetNewUserEmail($email, $reActivate = FALSE){
        $this->load->library('email');
        $passwd = $this->_userPasswd;
        $config['wrapchars'] = '130';
        
        $this->email->initialize($config);
        $coAndApp = $this->config->item('company', 'config_app') . ' '  . $this->config->item('app_title', 'config_app');
        $domain = $_SERVER['HTTP_HOST'];
        $adminEmail = $this->config->item('admin_email', 'config_app');
        $noReplyEmail = $this->config->item('auto_email', 'config_app');
        $this->email->from($noReplyEmail, $coAndApp);
        $this->email->reply_to($noReplyEmail, $coAndApp);
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
                                    'If you have any problems logging in, please email ' . $adminEmail);
        }else{
            $logPrefix = "New User email sent to ";
            $this->email->message( 'Welcome to  ' . $coAndApp . '. Please use the following credentials to complete setting up your account: ' . 
                                    "\n\nUsername: \t" . $email . "\nPassword: \t" . $passwd . "\n\nSite/Link: " . $site . "\n\n" .
                                    'The password provided is temporary, you will need to know it until you login and finish the registration process. ' .
                                    'If you have any problems logging in or completing your registration, please email ' . $adminEmail);
        }
        $this->email->send();
        log_message('debug', $logPrefix . $email . ", initiated from " . $_SERVER['REMOTE_ADDR']);
        
    }
    /**
     * deactivate user, delete method is written for this class but not deleting
     * users to break db relationships
     * @param int $login_id
     * @return boolean
     */
    function setUserDeactivated($login_id){
        $this->_userId = $login_id;
        $email = $this->_getUserData('email', array('db1','login')); //
        $this->load->helper('string');
        $this->_userPasswd = random_string('alnum', 8);
        $this->db1->set('password', sha1($this->_userPasswd));
        $this->db1->set('active', 0);
        $this->db1->where('id', $login_id);
        $return = $this->db1->update($this->userTable);
        if($return){
            $this->msg = 'User: ' . $email . ' was deactivated. <br>They will no longer be able to login unless reactivated.';
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
    function setUserActive($login_id){
        $this->_userId = $login_id;
        $email = $this->_getUserData('email', array('db1','login'));
        $this->load->helper('string');
        $this->_userPasswd = random_string('alnum', 8);
        $this->db1->set('password', sha1($this->_userPasswd));
        $this->db1->set('active', 1);
        $this->db1->set('reset', 1);
        $this->db1->where('id', $login_id);
        $return = $this->db1->update($this->userTable);
       
        if($return){
            $this->_sendSetNewUserEmail($email, TRUE);
            $this->msg = 'User: ' . $email . ' was reactivated. <br>A new password for the user has been emailed to them.';
            return TRUE;
        }
        log_message('debug', $return);
        return $return;
    }
    
    public function getEditFormData($method, $id){
        $userDetail = $this->getUserDetail($id);
        $this->load->model('form_m');
        $return = $this->form_m->getEditFormData($method, $userDetail);
        return $return;
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
    private function _getUserData($column, $dbInfo){
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
     * TODO this needs to check for userid in 
     * @param type $login_id
     * @return boolean
     */
    function delete($login_id){
        $this->db1->from($this->userTable);
        $this->db1->where('id', $login_id);
        $userTable = $this->db1->delete();
        $this->db1->from($this->profileTable);
        $this->db1->where('login_id', $login_id);
        $profileTableDel = $this->db1->delete();
        $this->db2->from($this->profileTable);
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

    private function _sendForgotPasswdEmail($email){
        $this->load->library('email');
    
        $config['wrapchars'] = '130';
        
        $this->email->initialize($config);
        
        //TODO change static to config variables (2 lines)
        $coAndApp = $this->config->item('company', 'config_app') . ' '  . $this->config->item('app_title', 'config_app');
        $domain = $_SERVER['HTTP_HOST'];
        $from = $this->config->item('auto_email', 'config_app');
        $this->email->from($from, $coAndApp);
        $this->email->reply_to($from, $coAndApp);
        $this->email->to($email); 
        //$this->email->cc('another@another-example.com'); 
        //$this->email->bcc('them@their-example.com'); 
            
        $this->email->subject('Password Reset');
        //TODO change to base_url() 
        $site = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        $this->email->message( 'A password reset/recovery was initiated from ' .$site . ' by a site admin for the following user/email: ' . "\n" . $email . "\n\n" 
                                . 'This password reset link will expire after 1 hour. Please click below to start the password recovery:'  . "\n" . $site  . '/login/recover/' . $this->_reset_hash);	

        $this->email->send();
        log_message('debug', "Admin 'Forgot Password' link for email sent to $email from " . $_SERVER['REMOTE_ADDR']);
        //log_message('debug', $this->email->print_debugger() . "\n");
        
    }
}
