<?php

class auth extends CI_Controller {
    
    private $_authString = '03fx21ju43';
    private $_urlFrom = 'ccvcs.net';

    function index() {
        $dfltUArray = array('site','passkey','user');
        $uArray = $this->uri->uri_to_assoc(3, $dfltUArray);
        $this->site = $uArray['site'];
        $this->passkey = $uArray['passkey'];
        $this->user = $uArray['user'];
        if(!$this->site && !$this->passkey){
            $errString = 'No auth string or passkey provided';
            $this->unauthorized($errString);
        } else {
            if($uArray['passkey'] == $this->_authString && $uArray['site'] == $this->_urlFrom){
                $this->strap_user($uArray['user']);
            }else{
                $errString = 'Data provided did not pass integrity check';
                $this->unauthorized($errString);
                
            } 
        }
    }
    
    function strap_user($wp_user) {
        // Create an instance of the user model
        $this->load->model('wpuser_m');

       //Ensure values exist for email and pass, and validate the user's credentials
        if( $wp_user &&  $this->wpuser_m->validate_user($wp_user)) {
            // If the user is valid, redirect to the main view
            redirect('/main/show_main');
        } else {
            // Otherwise show the login screen with an error message.
            $this->unauthorized('You may not be authorized or need to relogin to www.ccvcs.net');
        }
    }
    
    function logout_user() {
      $this->session->sess_destroy();
      $data['pageTitle'] = 'Auth Check: Logged Out';
        $data['error'] = TRUE;
        $data['errString'] = "You are now logged out, refresh this page inside wordpress to reauthorize ";
        $this->load->view('auth', $data);
    }
    
    function unauthorized($errString = 'General Issues'){
        $data['pageTitle'] = 'Auth Check: Unathorized';
        $data['error'] = TRUE;
        $data['errString'] = "You are not authorized to view this page: " . $errString;
        $this->load->view('auth', $data);
    }

}
