<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

Class Data_umum extends MX_Controller {
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_rencana');
		$this->load->helper(array('form', 'url'));			
	}
	
	public function index()
	{
		//$idpro = isset($_SESSION['idtender']) ? $_SESSION['idtender'] : false;
		$idpro = $this->get_tender_id();
		if($idpro)
		{
			$data_tender = $this->mdl_rencana->get_data_tender($idpro);
			$data = array(
				'idtender' => $idpro,
				'data_tender' => $data_tender['data']
			);
			$this->load->view("data_umum", $data);	
		} else echo "<h2 align='center'>Silahkan pilih tender terlebih dahulu!</h2>";
	}
	
	public function set_tender_id()
	{
		if($this->input->post('id_tender'))
		{
			$_SESSION['idtender'] = $this->input->post('id_tender');
		}
	}

	public function get_tender_id()
	{
		return $_SESSION['idtender'];
	}
	
	function get_data_dokumen($id)
	{
		$ret = $this->mdl_rencana->get_data_dokumen_tender($id);
		$this->_out($ret);
	}
	
	function update_data_tender()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '204800';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);

		if($this->input->post('id_proyek_rat'))
		{
			$q = $this->db->query("SELECT * FROM simpro_m_rat_proyek_tender WHERE id_proyek_rat='".$this->input->post('id_proyek_rat')."'")->row();

			if($_FILES['peta_lokasi_proyek']['name'] != ''){

				if($q->peta_lokasi_proyek != ''){
					unlink('./uploads/'.$q->peta_lokasi_proyek);
				}

				if(!$this->upload->do_upload('peta_lokasi_proyek'))
				{
					//$this->upload->display_errors()
					throw new Exception('File Gagal Upload, ukuran file tidak boleh lebih dari 2MB. Tipe file yg diperbolehkan: gif|jpg|png');
				} else {

					$data = $this->upload->data();

					$arr_update = array(
						'divisi_id' => $this->input->post('divisi_id'),
						'nama_proyek' => $this->input->post('nama_proyek'),
						'jenis_proyek' => $this->input->post('jenis_proyek'),
						'lingkup_pekerjaan' => $this->input->post('lingkup_pekerjaan'),
						'waktu_pelaksanaan' => $this->input->post('waktu_pelaksanaan'),
						'waktu_pemeliharaan' => $this->input->post('waktu_pemeliharaan'),
						'nilai_pagu_proyek' => $this->input->post('nilai_pagu_proyek'),
						'nilai_penawaran' => $this->input->post('nilai_penawaran'),
						'lokasi_proyek' => $this->input->post('lokasi_proyek'),
						'peta_lokasi_proyek' => $data['file_name'],
						'xlong' => $this->input->post('xlong'),
						'xlat' => $this->input->post('xlat'),
						'pemilik_proyek' => $this->input->post('pemilik_proyek'),
						'konsultan_pelaksana' => $this->input->post('konsultan_pelaksana'),
						'konsultan_pengawas' => $this->input->post('konsultan_pengawas'),
						'tanggal_tender' => $this->input->post('tanggal_tender'),
						'mulai' => $this->input->post('mulai'),
						'akhir' => $this->input->post('akhir'),
						'id_status_rat' => $this->input->post('id_status_rat')
					);

					
					$this->db->where('id_proyek_rat', $this->input->post('id_proyek_rat'));
					if($this->db->update('simpro_m_rat_proyek_tender', $arr_update))
					{
						echo json_encode(array('success'=>true, 'message'=>'Data Umum RAT berhasil diupdate.'));
					} else echo json_encode(array('success'=>true, 'message'=>'Data Umum RAT GAGAL diupdate!'));

				}
			} else {

				$data = $this->upload->data();

					$arr_update = array(
						'divisi_id' => $this->input->post('divisi_id'),
						'nama_proyek' => $this->input->post('nama_proyek'),
						'jenis_proyek' => $this->input->post('jenis_proyek'),
						'lingkup_pekerjaan' => $this->input->post('lingkup_pekerjaan'),
						'waktu_pelaksanaan' => $this->input->post('waktu_pelaksanaan'),
						'waktu_pemeliharaan' => $this->input->post('waktu_pemeliharaan'),
						'nilai_pagu_proyek' => $this->input->post('nilai_pagu_proyek'),
						'nilai_penawaran' => $this->input->post('nilai_penawaran'),
						'lokasi_proyek' => $this->input->post('lokasi_proyek'),
						'peta_lokasi_proyek' => $q->peta_lokasi_proyek,
						'xlong' => $this->input->post('xlong'),
						'xlat' => $this->input->post('xlat'),
						'pemilik_proyek' => $this->input->post('pemilik_proyek'),
						'konsultan_pelaksana' => $this->input->post('konsultan_pelaksana'),
						'konsultan_pengawas' => $this->input->post('konsultan_pengawas'),
						'tanggal_tender' => $this->input->post('tanggal_tender'),
						'mulai' => $this->input->post('mulai'),
						'akhir' => $this->input->post('akhir'),
						'id_status_rat' => $this->input->post('id_status_rat')
					);

					
					$this->db->where('id_proyek_rat', $this->input->post('id_proyek_rat'));
					if($this->db->update('simpro_m_rat_proyek_tender', $arr_update))
					{
						echo json_encode(array('success'=>true, 'message'=>'Data Umum RAT berhasil diupdate.'));
					} else echo json_encode(array('success'=>true, 'message'=>'Data Umum RAT GAGAL diupdate!'));

			}

		}
	}
	
	function get_data_tender($id)
	{
		$id = isset($id) ? $id : $this->input->post('id');
		if(isset($id))
		{
			$ret = $this->mdl_rencana->data_tender_by_id($id);	
			if(count($ret))
			{
				echo json_encode(array('success' => true, 'message'=> 'Data berhasil diload!', 'data' => $ret));
			} 
		} else echo json_encode(array('success' => true, 'message'=> 'Silahkan pilih tender terlebih dahulu!'));
	}
		
	function tambah_dokumen()
	{				
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|doc|pdf|docx|ppt|pptx|xls|xlsx|zip';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);					
		
		try {
			if(!$this->upload->do_upload('du_proyek_file'))
			{
				//$this->upload->display_errors()
				throw new Exception('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: gif|jpg|png|doc|pdf|docx|ppt|pptx|xls|xlsx|zip');
			} else
			{
				$data = $this->upload->data();
				$arr_insert = array(
					'du_proyek_tgl_upload' => date("Y-m-d"),
					'du_proyek_judul' => $this->input->post('du_proyek_judul'),
					'du_proyek_keterangan' => $this->input->post('du_proyek_keterangan'),
					'du_proyek_file' => $data['file_name'],
					'du_proyek_file_type' => $data['file_type'],
					'id_proyek_rat' => $this->input->post('id_proyek_rat')
				);						

				if($this->db->insert('simpro_rat_dokumen_proyek',$arr_insert)) 
				{
					echo json_encode(
						array(	"success"=>true, 
								"message"=>"Dokumen berhasil diupload.", 
								"file" => $data['file_name'])
					);
				} else
				{
					echo json_encode(
						array(	"success"=>true, 
								"message"=>"Dokumen GAGAL diupload.", 
								"file" => $data['file_name'])
					);								
				}
			}
		} catch(Exception $e)
		{
			echo json_encode(array(
				'success' => false,
				'message' => $e->getMessage(),
				'file' => 'undefined'
			));			
		}
	}
	
	function delete_dokumen()
	{
		if($this->input->post('id') && $this->input->post('file'))
		{
			if(unlink('./uploads/'.$this->input->post('file')))
			{
				$this->db->query(sprintf("DELETE FROM simpro_rat_dokumen_proyek WHERE id_dokumen_tender = '%d'", $this->input->post('id')));
				$status = sprintf("File '%s' berhasil dihapus.", $this->input->post('file'));				
			} else 
			{
				$status = sprintf("File %s GAGAL dihapus.", $this->input->post('file'));						
			}
		} else {
			$status = sprintf("File %s GAGAL dihapus.", $this->input->post('file'));						
		}

		echo $status;
	}

	function get_data_sketsa($id)
	{
		$ret = $this->mdl_rencana->get_data_sketsa_proyek($id);
		$this->_out($ret);
	}
	
	function tambah_sketsa_proyek()
	{				
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '204800';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);					
		
		try {
			if(!$this->upload->do_upload('du_sketsa_file'))
			{
				//$this->upload->display_errors()
				throw new Exception('File Gagal Upload, ukuran file tidak boleh lebih dari 2MB. Tipe file yg diperbolehkan: gif|jpg|png');
			} else
			{
				$data = $this->upload->data();
				$arr_insert = array(
					'du_proyek_tgl_upload' => date("Y-m-d"),
					'du_proyek_judul' => $this->input->post('du_proyek_judul'),
					'du_proyek_keterangan' => $this->input->post('du_proyek_keterangan'),
					'du_proyek_file' => $data['file_name'],
					'du_proyek_file_type' => $data['file_type'],
					'id_proyek_rat' => $this->input->post('id_proyek_rat')
				);						
				
				if($this->db->insert('simpro_rat_sketsa_proyek',$arr_insert)) 
				{
					echo json_encode(
						array(	"success"=>true, 
								"message"=>"Dokumen berhasil diupload.", 
								"file" => $data['file_name'])
					);
				} else
				{
					echo json_encode(
						array(	"success"=>true, 
								"message"=>"Dokumen GAGAL diupload.", 
								"file" => $data['file_name'])
					);								
				}
			}
		} catch(Exception $e)
		{
			echo json_encode(array(
				'success' => false,
				'message' => $e->getMessage(),
				'file' => 'undefined'
			));			
		}
	}
	
	function delete_sketsa_proyek()
	{

		if($this->input->post('id') && $this->input->post('file'))
		{
			if(unlink('./uploads/'.$this->input->post('file')))
			{
				$this->db->query(sprintf("DELETE FROM simpro_rat_sketsa_proyek WHERE id_sketsa_file = '%d'", $this->input->post('id')));
				$status = sprintf("File '%s' berhasil dihapus.", $this->input->post('file'));						
			} else 
			{
				$status = sprintf("File %s GAGAL dihapus.", $this->input->post('file'));						
			}
		} else {
			$status = sprintf("File %s GAGAL dihapus.", $this->input->post('file'));						
		}

		echo $status;
	}
	
	
	private function _dump($d)
	{
		print('<pre>');
		print_r($d);
		print('</pre>');
	}

	private function _out($data)
	{
		$callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '';
		if ($data['total'] > 0)
		{
			$output = json_encode($data, 1);
			if ($callback) {
				header('Content-Type: text/javascript');
				echo $callback . '(' . $output . ');';
			} else {
				header('Content-Type: application/x-json');
				echo $output;
			}		   
		} else 
		{
			if ($callback) {
				header('Content-Type: text/javascript');
				echo $callback . '(' . json_encode(array('data'=>array(), 'total'=>0)) . ');';
			} else {
				header('Content-Type: application/x-json');
				echo json_encode(array('data'=>array(), 'total'=>0));
			}		   
		}
	}
	
	function get_session()
	{
		$this->_dump($this->session->all_userdata());
		$this->_dump($_SESSION);
	}	
	
}