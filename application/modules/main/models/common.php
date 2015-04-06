<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	function login($uname, $upass)
	{
		$query = sprintf("
			SELECT *,
				first_name || ' ' || last_name as fullname,
				simpro_tbl_divisi.divisi_name,
				simpro_tbl_divisi.divisi_id
			FROM 
				simpro_tbl_user 
			INNER JOIN simpro_tbl_divisi ON (simpro_tbl_divisi.divisi_id = simpro_tbl_user.kode_entitas)
			WHERE user_name='%s' 
			AND password='%s'		
		", addslashes(trim($uname)), addslashes(trim($upass)));
		$qry = $this->db->query($query); 
		if($qry->num_rows() > 0)
		{
			return $qry->row_array();
		} else return false;		
	}
	
}