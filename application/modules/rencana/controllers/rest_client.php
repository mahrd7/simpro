<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');  

class rest_client extends REST_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mdl_rencana');
	}
	
    function view_get($id)
    {	
		$data = $this->mdl_rencana->rat_analisa_satuan_pekerjaan($id);
		echo json_encode(array(
			'success' => true,
			'message' => 'Data berhasil diambil',
			'data' => $data['data']
			)
		);
		/* $this->response($data); */
    }
    
    function update_post($id)
    {		
		$post_data = json_decode(file_get_contents('php://input'), true);
		$this->db->where('detail_material_kode', $post_data['data']['detail_material_kode']);
		$this->db->where('id_proyek_rat', $id);
		$this->db->update('simpro_rat_analisa', $post_data['data']);
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