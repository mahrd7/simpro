<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');  

class rest_rat_rab extends REST_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mdl_rencana');
	}
	
    function view_get($id)
    {	
		$data = $this->mdl_rencana->rat_rab_analisa_satuan_pekerjaan($id);
		echo json_encode(array(
			'success' => true,
			'message' => 'Data berhasil diambil',
			'data' => $data['data']
			)
		);
    }
    
    function update_post($id)
    {			
		$post_data = json_decode(file_get_contents('php://input'), true);
		$id_simpro_rat_analisa = $post_data['data']['id_simpro_rat_analisa'];
		$is_data = $this->db->query(sprintf("SELECT * FROM simpro_rat_rab_analisa WHERE id_simpro_rat_analisa='%d'", $id_simpro_rat_analisa))->num_rows();
		if($is_data)
		{		
			$this->db->where('id_simpro_rat_analisa', $post_data['data']['id_simpro_rat_analisa']);
			$this->db->update('simpro_rat_rab_analisa', $post_data['data']);
		} else
		{
			$di = array(
				'id_simpro_rat_analisa' => $post_data['data']['id_simpro_rat_analisa'],
				'harga_rab' => $post_data['data']['harga_rab'],
				'koefisien_rab' => 1
			);
			$this->db->insert('simpro_rat_rab_analisa', $di);
		}
		$data['data'] = $post_data['data'];
		$data['pesan'] = 'Data berhasil diupdate!';
		$this->to_json($data);
    }
	
	function dump($dm)
	{
		print('<pre>');
		print_r($dm);
		print('</pre>');
	}
	
	function to_json($data)
	{
		echo json_encode(array(
			'success' => true,
			'message' => $data['pesan'],
			'data' => $data['data']
			)
		);	
	}
	
}