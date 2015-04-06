<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dokumentasi extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_dokumentasi');	
		$this->load->helper('file');			
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}
	
	public function page($info)
	{
		switch ($info) {
			case 'dokumentasi_foto': $page = 'dokumentasi_foto'; break;
			case 'dokumentasi_k3': $page = 'dokumentasi_k3'; break;
			case 'dokumentasi_ve': $page = 'dokumentasi_ve'; break;			
		}
		$this->load->view($page);
	}
	
	function getdok($info)
	{
		// $data['db']=$this->mdl_rencana->cobajson();
	$proyek_id = $this->session->userdata('proyek_id');

	switch ($info) {
		case 'dokumentasi_foto':
		$tbl_info = 'simpro_tbl_foto_proyek';
		$id='foto_proyek_id';
		break;
		case 'dokumentasi_k3':
		$tbl_info = 'simpro_tbl_dok_k3';
		$id='dok_k3_id';
		break;
		case 'dokumentasi_ve':
		$tbl_info = 'simpro_tbl_inovasi';
		$id='inovasi_id';
		break;
	}

	$q = $this->db->query("select proyek_id, foto_proyek_keterangan, foto_proyek_judul, $id as foto_no, date_part('day', foto_proyek_tgl) as foto_proyek_tgl_day, date_part('month', foto_proyek_tgl) as foto_proyek_tgl_month, date_part('year', foto_proyek_tgl) as foto_proyek_tgl_year from $tbl_info where proyek_id = $proyek_id order by $id desc");     

	$data = array();
	$dat = array();
	if ($q->num_rows() > 0) {
	foreach($q->result() as $row) {
		$tglmonth = $row->foto_proyek_tgl_month;
		$tglyear = $row->foto_proyek_tgl_year;
		$tgl=$this->bulan($tglmonth)." ".$tglyear;
		$foto = $row->foto_proyek_judul;

    	$data['foto_no'] = $row->foto_no;    	
    	$data['file'] = $foto;
    	$data['foto_proyek_tgl'] = $tgl;
    	// $data['foto_proyek_judul'] = '<center><img src='.base_url().'assets/uploads/'.$foto.' width="200px" height="150px"></center>';
    	$data['foto_proyek_judul'] = $foto;
    	$data['foto_proyek_keterangan'] = $row->foto_proyek_keterangan;
    	$data['proyek_id'] = $row->proyek_id;
    	$data['tglmonth'] = $tglmonth;
    	$data['tglyear'] = $tglyear;


    	$dat[] = $data;
    	}
	}
		// echo '{"data":'.json_encode($q->result_object()).'}';
		echo '{"data":'.json_encode($dat).'}';
	}

	function deletedok($info)
	{
		switch ($info) {
			case 'dokumentasi_foto':
			$tbl_info = 'simpro_tbl_foto_proyek';
			break;
			case 'dokumentasi_k3':
			$tbl_info = 'simpro_tbl_dok_k3';
			break;
			case 'dokumentasi_ve':
			$tbl_info = 'simpro_tbl_inovasi';
			break;
		}
		$id = $this->input->post('id');
		$file = $this->input->post('file');
		$this->mdl_dokumentasi->deletedok($tbl_info,$id);
		// $path = ASSETS . 'uploads/' . $file;
		unlink('./uploads/'.$file);
	}

	public function insertdok($info)
	{
		switch ($info) {
			case 'dokumentasi_foto':
			$tbl_info = 'simpro_tbl_foto_proyek';
			$id_info='foto_proyek_id';
			break;
			case 'dokumentasi_k3':
			$tbl_info = 'simpro_tbl_dok_k3';
			$id_info='dok_k3_id';
			break;
			case 'dokumentasi_ve':
			$tbl_info = 'simpro_tbl_inovasi';
			$id_info='inovasi_id';
			break;
		}

		$proyek_id = $this->session->userdata('proyek_id');
		$user_id = $this->session->userdata('uid');
		$divisi_id = $this->session->userdata('divisi_id');

		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$blndoc = $this->input->post('blndoc');
		$thndoc = $this->input->post('thndoc');
		$tgl=$blndoc."/1/".$thndoc;
		$ketdoc = $this->input->post('ketdoc');

		$this->load->library('upload', $config);

		if (!$this->input->post('id')) {
			try {
				if(!$this->upload->do_upload('photo-path'))
				{
					//$this->upload->display_errors()
					throw new Exception('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: gif|jpg|png|doc|pdf|docx|ppt|pptx|xls|xlsx|zip');
				} else
				{
					$data = $this->upload->data();
					$data_insert = array(
						'user_id' => $user_id,
						'divisi_id' => $divisi_id,
						'proyek_id' => $proyek_id,
						'foto_proyek_tgl' => $tgl,
						'foto_proyek_keterangan' => $ketdoc,
						'foto_proyek_judul' => $data['file_name'],
						'foto_proyek_file' => $data['file_name']
					);						

					if($this->mdl_dokumentasi->insertdok($tbl_info,$data_insert)) 
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
		} else {
			$id = $this->input->post('id');
			$file_unset = $this->db->query("select foto_proyek_judul from $tbl_info where $id_info = $id")->row()->foto_proyek_judul;
				
			if ($_FILES['photo-path']['name'] != '') {				
				unlink('./uploads/'.$file_unset);

				try {
					if(!$this->upload->do_upload('photo-path'))
					{
						//$this->upload->display_errors()
						throw new Exception('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: gif|jpg|png|doc|pdf|docx|ppt|pptx|xls|xlsx|zip');
					} else
					{
						$data = $this->upload->data();
						$data_insert = array(
							'user_id' => $user_id,
							'divisi_id' => $divisi_id,
							'proyek_id' => $proyek_id,
							'foto_proyek_tgl' => $tgl,
							'foto_proyek_keterangan' => $ketdoc,
							'foto_proyek_judul' => $data['file_name'],
							'foto_proyek_file' => $data['file_name']
						);						

						$this->db->where(array($id_info => $id));
						if($this->db->update($tbl_info,$data_insert)) 
						{
							echo json_encode(
								array(	"success"=>true, 
										"message"=>"Dokumen berhasil diupdate.", 
										"file" => $data['file_name'])
							);
						} else
						{
							echo json_encode(
								array(	"success"=>true, 
										"message"=>"Dokumen GAGAL diupdate.", 
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
			} else {

				$data_insert = array(
					'user_id' => $user_id,
					'divisi_id' => $divisi_id,
					'proyek_id' => $proyek_id,
					'foto_proyek_tgl' => $tgl,
					'foto_proyek_keterangan' => $ketdoc
					);

				$this->db->where(array($id_info => $id));
				if($this->db->update($tbl_info,$data_insert)) 
				{
					echo json_encode(
						array(	"success"=>true, 
							"message"=>"Dokumen berhasil diupdate.", 
							"file" => $file_unset)
						);
				} else
				{
					echo json_encode(
						array(	"success"=>true, 
							"message"=>"Dokumen GAGAL diupdate.", 
							"file" => $file_unset)
						);								
				}

			}
		}
	}

	function getbulan(){
		$bulan = array();
		$bln = array();
		for ($i=1; $i <= 12 ; $i++) { 
			$bulan['value'] = $i;
			if ($i==1){
				$bul='Januari';
			} elseif ($i==2) {
				$bul='Februari';	
			} elseif ($i==3) {
				$bul='Maret';	
			} elseif ($i==4) {
				$bul='April';	
			} elseif ($i==5) {
				$bul='Mei';	
			} elseif ($i==6) {
				$bul='Juni';	
			} elseif ($i==7) {
				$bul='Juli';	
			} elseif ($i==8) {
				$bul='Agustus';	
			} elseif ($i==9) {
				$bul='September';	
			} elseif ($i==10) {
				$bul='Oktober';	
			} elseif ($i==11) {
				$bul='November';	
			} elseif ($i==12) {
				$bul='Desember';	
			}
			$bulan['text'] = $bul;
			$bulan['value'] = $i;
			$bln[] = $bulan;
		}

		echo '{"data":'.json_encode($bln).'}';
	}
	

	function gettahun(){
		$year = date('Y');
		$tahun = array();
		$thn = array();
		for ($i=2000; $i <= $year; $i++) { 
			$tahun['value'] = $i;
			$tahun['text'] = $i;
			$thn[] = $tahun;
		}
		echo '{"data":'.json_encode($thn).'}';
	}

	function bulan($no){
		if ($no == 1){
			$bulan = 'Januari';
		} elseif ($no == 2) {
			$bulan = 'Februari';
		} elseif ($no == 3) {
			$bulan = 'Maret';
		} elseif ($no == 4) {
			$bulan = 'April';
		} elseif ($no == 5) {
			$bulan = 'Mei';
		} elseif ($no == 6) {
			$bulan = 'Juni';
		} elseif ($no == 7) {
			$bulan = 'Juli';
		} elseif ($no == 8) {
			$bulan = 'Agustus';
		} elseif ($no == 9) {
			$bulan = 'September';
		} elseif ($no == 10) {
			$bulan = 'Oktober';
		} elseif ($no == 11) {
			$bulan = 'November';
		} elseif ($no == 12) {
			$bulan = 'Desember';
		} elseif ($no == 0) {
			$bulan = 'Desember';
		}
		return $bulan;
	}
}