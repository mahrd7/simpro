<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Doc extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('pdf'); 
		#$this->pdf->fontpath = 'font/'; 
	}

	public function index()
	{
		// Generate PDF by saying hello to the world
		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','B',16);
		$this->pdf->Cell(40,10,'Hello World!');
		$this->pdf->Output();
	}
}

?>