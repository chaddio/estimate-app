<?php

class newdefault extends CI_Controller{
    
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
    function index($show_error = FALSE, $alertLevel = 'danger') {
        //default redirect
        $data = array();
        $data['error'] = $show_error;
        $data['alertLevel'] = $alertLevel;
        $data['class'] = get_class();
        $data['email'] = $this->session->userdata('email');
        $data['name'] = ucwords($this->session->userdata('name'));
        $splitName = explode(' ', $data['name']);
        $data['fname'] = $splitName[0];
        $data['appTitle'] = $this->config->item('app_title', 'config_app');
        $data['navTitle'] = 'Under Construction';
        //$data['class'] = get_class();
        $data['serviceCompany'] = $this->config->item('company', 'config_app');
        $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - Under Construction';
        if($this->session->userdata('app_userlevel') == '0'){
            $data['isAdmin'] = TRUE;
        }else{
            $data['isAdmin'] = FALSE;
        }

        $data['app_pages'] = $this->session->userdata('app_pages');
        $data['h4'] = 'Under Construction - ' . ucwords(preg_replace('/\//','', $_SERVER['REQUEST_URI']));
        $data['bodyMsg'] = 'This page is currently being constructed, please check back for updates';

        $this->load->view('newdefault/newdefault',$data);
    }

}
