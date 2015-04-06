<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_admin extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	function getdata($tbl_get)
	{
		$q = $this->db->get($tbl_get);
		return $q;
	}

	function getdatasearch($tbl_get,$param)
	{
		switch ($tbl_get) {
			case 'tbl_propinsi':
			$this->db->where('propinsi_induk',$param);
			$q = $this->db->get($tbl_get);
			return $q;
			break;
			case 'tbl_divisi_propinsi':
			$this->db->where('divisi_kode',$param);
			$q = $this->db->get($tbl_get);
			return $q;
			break;
			case 'tbl_propinsi':
			$q = $this->db->query("select * from tbl_propinsi where (propinsi_induk is null or propinsi_induk='')");
			// return $q;
			echo '{"data":'.json_encode($q->result_object()).'}';
			break;
		}
	}

	function insertdata($tbl_get,$data)
	{
		$this->db->insert($tbl_get,$data);
	}

	function deletedata($tbl_get,$id)
	{
		switch($tbl_get){
			case 'tbl_satuan':			
			$this->db->where('satuan_nama', $id);
			break;
			case 'tbl_sumber_dana':			
			$this->db->where('sumberdana_id', $id);
			break;
			case 'tbl_pemilik_proyek':			
			$this->db->where('pemilik_id', $id);			
			break;
			case 'tbl_sbu':			
			$this->db->where('sbu_id', $id);			
			break;
			case 'subbidang':			
			$this->db->where('subbidang_id', $id);			
			break;
			case 'tbl_master_peralatan':			
			$this->db->where('alat_id', $id);			
			break;
			case 'tbl_target_dashboard':			
			$this->db->where('id', $id);			
			break;
			case 'tbl_divisi':			
			$this->db->where('divisi_id', $id);			
			break;
			case 'tbl_propinsi':			
			$this->db->where('propinsi_id', $id);			
			break;
			case 'tbl_direktorat':			
			$this->db->where('id_dir', $id);			
			break;
			case 'tbl_toko':			
			$this->db->where('toko_id', $id);			
			break;
		}
		$this->db->delete($tbl_get);
	}

	function editdata($tbl_get,$data,$id)
	{
		switch($tbl_get){
			case 'tbl_satuan':			
			$this->db->where('id', $id);
			break;
			case 'tbl_sumber_dana':			
			$this->db->where('sumberdana_id', $id);
			break;
			case 'tbl_pemilik_proyek':			
			$this->db->where('pemilik_id', $id);
			break;
			case 'tbl_sbu':			
			$this->db->where('sbu_id', $id);
			break;
			case 'subbidang':			
			$this->db->where('subbidang_id', $id);
			break;
			case 'tbl_master_peralatan':			
			$this->db->where('alat_id', $id);
			break;
			case 'tbl_target_dashboard':			
			$this->db->where('id', $id);
			break;
			case 'tbl_divisi':			
			$this->db->where('divisi_id', $id);
			break;
			case 'tbl_propinsi':			
			$this->db->where('propinsi_id', $id);
			break;
			case 'tbl_direktorat':			
			$this->db->where('id_dir', $id);
			break;
			case 'tbl_toko':			
			$this->db->where('toko_id', $id);
			break;
		}		
		$this->db->update($tbl_get,$data);
	}
}