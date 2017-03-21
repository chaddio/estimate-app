<?php

class main extends CI_Controller{
    
  public $row;
  public $serviceKey = 'install_id';
  public $serviceTitle = 'install';

  public function __construct()
  {
    parent::__construct();
    //$this->benchmark->mark('code_start');
    if($this->session->userdata('reset')){
        redirect('/login/reset_pwd/flag');
    }
    if($this->session->userdata('register')){
        redirect('/login/register/flag');
    }
    if( !$this->session->userdata('isLoggedIn') ) {
        redirect('/login/timeout');
    }
    
  }

  /**
   * This is the controller method that drives the application.
   * After a user logs in, () is called and the main
   * application screen is set up.
   * alertLevel can be danger, info, or success (Red, Blue, Green)
   */
  function index($show_error = FALSE, $alertLevel = 'danger') {
    //$this->session->set_userdata(array('row',$row));
//    $is_admin = ($this->session->userdata('wprole') == 'administrator' ? TRUE : FALSE);
    $data = array();
    $data['email'] = $this->session->userdata('email');
    $data['name'] = ucwords($this->session->userdata('name'));
    $splitName = explode(' ',$data['name']);
    $data['fname'] = $splitName[0];
    $data['appTitle'] = $this->config->item('app_title', 'config_app');
    $data['navTitle'] = $this->config->item('app_title', 'config_app') . ' - ' . $data['name'];
    $data['class'] = get_class();
    $data['serviceCompany'] = $this->config->item('company', 'config_app');
    $data['pageTitle'] = $this->config->item('company', 'config_app') . ' - '  . $this->config->item('app_title', 'config_app') . ' - ' . ucwords($data['class']);
    $data['message'] = 'There are no new messages or past messages at this time. ';
    if($this->session->userdata('app_userlevel') == '0'){
        $data['isAdmin'] = TRUE;
    }else{
        $data['isAdmin'] = FALSE;
    }
    
    $data['app_pages'] = $this->session->userdata('app_pages');
    
    $data['entriesCnt'] = 0;
    
    
    if($show_error == 'pwdchgd'){
        if(@$this->input->cookie('welcome')){
            $data['error'] = 'Your registration is now complete, Welcome To Webtools!';
            $data['alertLevel'] = 'success'; // error, info, or success is the string value passed to the view for css/color (red, blue or green)
            $this->input->set_cookie(array('name' =>'welcome','value' => '0','expire' => '','domain' => $this->input->server('HTTP_HOST'),'path' => '/')); //unset cookie (zero length expire time)
        }else{
            $data['error'] = 'Your password was changed successfully';
            $data['alertLevel'] = 'success'; // error, info, or success is the string value passed to the view for css/color (red, blue or green)
        }
    }elseif($show_error == 'pwdrec'){
        $data['error'] = 'Your password recovery is now complete - Welcome Back ' . $data['fname'] . '!';
        $data['alertLevel'] = 'success';
    }elseif($show_error){
        $data['error'] = $show_error;
        $data['alertLevel'] = $alertLevel;
    }
    $this->load->view('main/main',$data);
  }
  
}
