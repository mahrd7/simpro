<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Login extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('common');				
	}
	
	public function index()
	{
		if($this->session->userdata('logged_in'))
			redirect('main/index');					
		$this->load->view('login_new');
	}

	function check_session()
	{
		$uid = $this->session->userdata('uid');
		if ($uid) {
			$var = true;
		} else {
			$var = false;
		}

		echo $var;
	}
	
	function dump($dm)
	{
		print('<pre>');
		print_r($dm);
		print('</pre>');
	}
	
	function cek_login()
	{
		if($this->input->post('uname') && $this->input->post('upass'))
		{
			$this->load->helper('string');						
			$reslog = $this->common->login(strip_quotes($this->input->post('uname')), strip_quotes($this->input->post('upass')));
			if($reslog)
			{
				$logdata = array(
				   'uid'  => $reslog['user_id'],
				   'uname'     => trim($reslog['user_name']),
				   'logdate'     => date('Y-m-d H:i:s'),
				   'fullname'     => $reslog['fullname'],
				   'divisi'     => $reslog['divisi_name'],
				   'divisi_id'     => $reslog['divisi_id'],
				   'logged_in' => TRUE
				   );
				$this->session->set_userdata($logdata);
				$_SESSION = array();
				redirect('main/index');				
			} else 
			{
				$this->session->set_flashdata('login_error', 'Login Error, silahkan masukan user dan password anda dengan benar!');
				redirect('main/login');
			}
		}
	}
	
	function logout()
	{
		if(isset($_SESSION['idtender']) && isset($_SESSION['proyek_id']))
		{
			unset($_SESSION['idtender']);
			unset($_SESSION['proyek_id']);
			session_unset();
			session_destroy();
			$_SESSION = array();			
		}
		$this->session->unset_userdata();
		$this->session->sess_destroy();		
		redirect('main/login'); 
	}
		
}