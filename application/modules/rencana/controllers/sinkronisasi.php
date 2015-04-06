<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

Class Sinkronisasi extends MX_Controller {
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->library('nusoap');
	}
	
	function index()
	{
		define('SOAPSERVER', 'http://simpro.nindyakarya.co.id/simpro-d/sync/index.php?wsdl');	
		$client = new soapclient(SOAPSERVER, true); 
		$err = $client->getError();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
		}
		//$req = array('query' => 'select * from tbl_proyek');
		$result = $client->call('getData', array('subbidang'));
		if ($client->fault) {
			echo '<h2>Fault</h2><pre>';
			print_r($result);
			echo '</pre>';
		} else {
			$err = $client->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				echo '<h2>Result</h2><pre>';
				$result = json_decode($result);
				$data = $result->data;
				print_r($data);
			echo '</pre>';
			}
		}
		/*
		echo '<h2>Request</h2>';
		echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
		echo '<h2>Response</h2>';
		echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';	
		*/
	}
	
}