<?php
//TODO remove excess code/fluff from the copy.
class presentation extends CI_Controller{

    protected $_resultsPerPage = CI_APP_ROWS;
    private $_orderDir = 'asc';
    private $_colUri1 = 'e';
    private $_colUri2 = 'n';
    private $_th1Icon = '';
    private $_th2Icon = '';
    public function __construct()
    {
        parent::__construct();
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

        //$data['users'] = $this->users_m->getUsersWhere('active',$active,$config['per_page'], $row);
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
        $this->load->view('presentation/presentation',$data);
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