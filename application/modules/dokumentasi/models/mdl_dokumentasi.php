<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_dokumentasi extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	function rr_mutu()
	{
	}
	
	function insertdok($tbl_info,$data)
	{
		$this->db->insert($tbl_info,$data);
	}

	function deletedok($tbl_info,$id)
	{
		switch ($tbl_info) {
			case 'simpro_tbl_foto_proyek':
			$this->db->where('foto_proyek_id',$id);
			break;
		}
		switch ($tbl_info) {
			case 'simpro_tbl_dok_k3':
			$this->db->where('dok_k3_id',$id);
			break;
		}
		switch ($tbl_info) {
			case 'simpro_tbl_inovasi':
			$this->db->where('inovasi_id',$id);
			break;
		}
		$this->db->delete($tbl_info);
	}
}