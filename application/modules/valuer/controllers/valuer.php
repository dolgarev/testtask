<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Valuer extends MX_Controller
{
    public function __construct() {
        parent::__construct();

//        if (!$this->ion_auth->logged_in()) {
//            redirect('auth/login');
//        }

        $this->load->model('valuer_model');
        $this->load->library('form_validation');
        $this->form_validation->CI = &$this;
    }


    public function index() {
        $this->form_validation->set_message('check_domain_name', 'The %s field has invalid value');
        $this->form_validation->set_rules('domain', 'Domain', 'trim|required|callback_check_domain_name');
        $this->form_validation->set_rules('keyword', 'Keyword', 'trim|required');

        $assign = array();
        if ($this->form_validation->run()) {
            $assign['domain'] = set_value('domain');
            $assign['keyword'] = set_value('keyword');
            $assign['rank'] = $this->valuer_model->get_rank($assign['domain'], $assign['keyword']);
        }

        $this->template->build('index', $assign);
    }

    public function check_domain_name($domain_name) {
        //[http://stackoverflow.com/questions/1755144/how-to-validate-domain-name-in-php]
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
                && preg_match("/^.{1,253}$/", $domain_name) //overall length check
                && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
    }
}
