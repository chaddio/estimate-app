<?php

class error extends CI_Controller{
    
    public $row;
    public $serviceKey = 'install_id';
    public $serviceTitle = 'install';

    public function __construct()
    {
        parent::__construct();

        if( !$this->session->userdata('isLoggedIn') ) {
            redirect('/login/timeout/');
        }

    }

    /**
     * This is the under construction / index controller method
     * this is used in conjunction with a 
     * application screen is set up.
     */
    function index() {
        //default redirect
        $data = array();
        $data['class'] = get_class();
        $data['email'] = $this->session->userdata('email');
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ', $data['name']);
        $data['fname'] = $splitName[0];
        $data['appTitle'] = $this->config->item('app_title', 'config_app');
        $data['navTitle'] = 'Page Not Found';
        //$data['class'] = get_class();
        $data['serviceCompany'] = $this->config->item('company', 'config_app');
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - 404 Page not found';
        if($this->session->userdata('app_userlevel') == '0'){
            $data['isAdmin'] = TRUE;
        }else{
            $data['isAdmin'] = FALSE;
        }

        $data['app_pages'] = $this->session->userdata('app_pages');
        $data['h4'] = 'Page Not Found - 404';
        $data['bodyMsg'] = 'The page requested: "<span class="text-primary">' . $_SERVER['REQUEST_URI'] . '</span>" doesn\'t exist or was moved :(';
        $data['footerMsg'] = 'Rest assured, this has been logged for review...no monkey\'n around here!';
        log_message('debug',"## 404 ERROR when REQUESTING '" . $_SERVER['REQUEST_URI'] . "', user agent: '" .$_SERVER['REMOTE_ADDR'] . "'");
        log_message('debug',"## 404 ERROR, user login: " . $this->session->userdata('email'));
        $this->load->view('error/error404',$data);
    }

}
