<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mutu_Resiko extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_muturesiko');
		if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");		
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function page($info)
	{
		$proyek_id = $this->session->userdata('proyek_id');

		switch ($info) {
			case 'rencana_realisasi_mutu': $page ='rencana_realisasi_mutu'; break;
			case 'identifikasi_resiko': $page ='identifikasi_resiko'; break;
			case 'daftar_resiko': $page ='daftar_resiko'; break;
			case 'pilih_lap_penanganan': $page ='pilih_lap_penanganan'; break;
		}
		$infos=$this->mdl_muturesiko->getinfo($proyek_id);
		$this->load->view($page,$infos);
	}
	
	public function getdata($info)
	{
		$page= $_GET['page'];
		$start = $_GET['start'];
		$limit = $_GET['limit'];
		
		switch ($info) {
			case 'rencana_realisasi_mutu': 
			$tbl_info ='simpro_tbl_rencana_realisasi_mutu'; 
			break;

			case 'identifikasi_resiko': 
			$tbl_info ='simpro_tbl_analisis_risiko'; 
			break;

			case 'daftar_resiko': 
			$tbl_info ='simpro_tbl_daftar_risiko'; 
			break;

			case 'lap_penanganan': 
			$tbl_info ='lap_penanganan'; 
			break;
		}
		$data = $this->mdl_muturesiko->getdata($tbl_info,$start,$limit);
		echo '{"data":'.json_encode($data).'}';
	}
	public function getdata2($info,$bln,$thn)
	{
		$page= $_GET['page'];
		$start = $_GET['start'];
		$limit = $_GET['limit'];
		
		switch ($info) {
			case 'lap_penanganan': 
			$tbl_info ='lap_penanganan';
			
			break;
		}
		$data = $this->mdl_muturesiko->getdata2($tbl_info,$start,$limit,$bln,$thn);
		echo '{"data":'.json_encode($data).'}';
	}
	
	public function insertdata($tbl_info){
		$this->db->trans_begin();
		$aksi = 0;
		$tgl = date('Y-m-d');
		$user_id = $this->session->userdata('uid');
		$waktu = date('H:i:s');
		// $proyek_id = "1";
		$proyek_id = $this->session->userdata('proyek_id');

		switch($tbl_info)
		{
			case 'rencana_realisasi_mutu' :			
			$jenis = $_POST['jenis'];
			$uraian_rencana_mutu = $_POST['uraian_rencana_mutu'];
			$tbl_get = 'simpro_tbl_rencana_realisasi_mutu';
			
			$data = array(
				'proyek_id' => $proyek_id, 
				'rr_uraian_rencana' => $uraian_rencana_mutu,
				'rr_tgl' => $tgl,
				'user_id' => $user_id,
				'rencana_realisasi_mutu_jenis_id' => $jenis,
			); 
			break;
			case 'identifikasi_resiko' :
			$aksi = 1;
			$risiko = $this->input->post('risiko');
			$rencana_penanganan = $this->input->post('rencana_penanganan');
			$akibat = $this->input->post('akibat');
			$analisis = $this->input->post('analisis');
			$batas_waktu = $this->input->post('batas_waktu');
			$keputusan = $this->input->post('keputusan');
			$tingkat_akibat = $this->input->post('tingkat_akibat');
			$tingkat_kemungkinan = $this->input->post('tingkat_kemungkinan');
			$tingkat_risiko = $this->input->post('tingkat_risiko');
			$sisa_risiko = $this->input->post('sisa_risiko');
			$pic = $this->input->post('pic');
			
			$tbl_get = 'simpro_tbl_analisis_risiko';
			
			$data2 = array(
				'proyek_id' => $proyek_id, 
				'risiko' => $risiko,
				'akibat' => $akibat,
				'tgl' => $tgl,
				'user_id' => $user_id,
				'analisis' => $analisis,
				'rencana_penanganan' => $rencana_penanganan,
				'batas_waktu' => $batas_waktu,
				'keputusan' => $keputusan,
				'tingkat_akibat' => $tingkat_akibat,
				'tingkat_kemungkinan' => $tingkat_kemungkinan,
				'tingkat_risiko' => $tingkat_risiko,
				'sisa_risiko' => $sisa_risiko,
				'pic' => $pic,
			);
			$analisis_resiko = $this->mdl_muturesiko->insertdata($tbl_get,$data2);
						
				$query = $this->db->query("select ar_id from simpro_tbl_analisis_risiko order by ar_id desc limit 1");
				if($query->num_rows() > 0){					
						$res = $query->row();
						$ar_id = $res->ar_id;
					
					$tbl_get = 'simpro_tbl_daftar_risiko';
					$data3 = array(
						'proyek_id' => $proyek_id,
						'konteks' => $risiko,
						'akibat' => $akibat,
						'tgl' => $tgl,
						'user_id' => $user_id,
						'ar_id' => $ar_id ,
					);
					$this->mdl_muturesiko->insertdata($tbl_get,$data3);
					/*$tbl_get = 'simpro_tbl_penanganan_risiko';
					$data4 = array(
						'proyek_id' => $proyek_id,
						'risiko' => $risiko,
						//'akibat' => $akibat,
						'tgl' => $tgl,
						'user_id' => $user_id,
						'ar_id' => $ar_id ,
					);
					$this->mdl_muturesiko->insertdata($tbl_get,$data4);*/
				}
			
			break;
			case 'lap_penanganan' :
			$risiko=$this->input->post('risiko');
			$tingkat_risiko=$this->input->post('tingkat_risiko');
			$nilai_risiko=$this->input->post('nilai_risiko');
			$realisasi_tindakan=$this->input->post('realisasi_tindakan');
			$biaya_memitigasi=$this->input->post('biaya_memitigasi');
			$biaya_sisa=$this->input->post('biaya_sisa');
			$pic=$this->input->post('pic');
			$target_tingkat_risiko=$this->input->post('target_tingkat_risiko');
			$realisasi_tingkat_sisa=$this->input->post('realisasi_tingkat_sisa');
			$status_risiko=$this->input->post('status_risiko');
			$tgl_aak=$this->input->post('tgl_aak');
			$ar_id = $this->db->query("select ar_id from simpro_tbl_analisis_risiko where proyek_id = $proyek_id limit 1");
		
			$tbl_get = 'simpro_tbl_penanganan_risiko';
			
			$data = array(
				'proyek_id' => $proyek_id, 
				'risiko' => $risiko,
				'tgl_aak' => $tgl_aak,
				'tgl' => $tgl,
				'user_id' => $user_id,
				'ar_id' => $ar_id->row()->ar_id,
				//'ar_id' => 0,
				'tingkat_risiko_id' => $tingkat_risiko,
				'realisasi_tindakan' => $realisasi_tindakan,
				'nilai_risiko' => $nilai_risiko,
				'realisasi_sisa_risiko_id' => $realisasi_tingkat_sisa,
				'biaya_memitigasi' => $biaya_memitigasi,
				'biaya_sisa_risiko' => $biaya_sisa,
				'pic' => $pic,
				'target_sisa_risiko_id' => $target_tingkat_risiko,
				'status_risiko_id' => $status_risiko,
				
			);
			break;
		}
		if($aksi== 0)
			$this->mdl_muturesiko->insertdata($tbl_get,$data);
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else
		{
			$this->db->trans_commit();
		}
	}
	
	public function deletedata($tbl_info){

		switch($tbl_info)
		{
			case 'rencana_realisasi_mutu' : 
			$id = $this->input->post('id');
			$tbl_get = 'simpro_tbl_rencana_realisasi_mutu'; 
			break;
			case 'identifikasi_resiko' : 
			$id = $this->input->post('id');
			$tbl_get = 'simpro_tbl_analisis_risiko'; 
			break;
			case 'lap_penanganan' : 
			$id = $this->input->post('id');
			$tbl_get = 'simpro_tbl_penanganan_risiko'; 
			break;
			
		}
		$this->mdl_muturesiko->deletedata($tbl_get,$id);
	}
	
	public function editdata($tbl_info){
		$this->db->trans_begin();
		$aksi = 0;
		$tgl = date('Y-m-d');
		$id = $this->input->post('editid');
		$user_id = $this->session->userdata('uid');
		switch($tbl_info)
		{
			case 'rencana_realisasi_mutu' :
			$jenis = $this->input->post('editjenis');
			$uraian_rencana_mutu = $_POST['edituraian_rencana_mutu'];
			$rr_uraian_realisasi = $_POST['edituraian_realisasi_mutu'];
			$tbl_get = 'simpro_tbl_rencana_realisasi_mutu';
			$data = array(
				//'proyek_id' => $proyek_id, 
				'rr_uraian_rencana' => $uraian_rencana_mutu,
				'rr_uraian_realisasi' => $rr_uraian_realisasi,
				'rr_tgl' => $tgl,
				'user_id' => $user_id,
				'rencana_realisasi_mutu_jenis_id' => $jenis,
			); 
			break;
			case 'identifikasi_resiko' :
			$aksi = 1;
			$risiko = $this->input->post('editrisiko');
			$rencana_penanganan = $this->input->post('editrencana_penanganan');
			$akibat = $this->input->post('editakibat');
			$analisis = $this->input->post('editanalisis');
			$batas_waktu = $this->input->post('editbatas_waktu');
			$keputusan = $this->input->post('editkeputusan');
			$tingkat_akibat = $this->input->post('edittingkat_akibat');
			$tingkat_kemungkinan = $this->input->post('edittingkat_kemungkinan');
			$tingkat_risiko = $this->input->post('edittingkat_risiko');
			$sisa_risiko = $this->input->post('editsisa_risiko');
			$pic = $this->input->post('editpic');
			
			$tbl_get = 'simpro_tbl_analisis_risiko';
			
			$data2 = array(
				//'proyek_id' => $proyek_id, 
				'risiko' => $risiko,
				'akibat' => $akibat,
				'tgl' => $tgl,
				'user_id' => $user_id,
				'analisis' => $analisis,
				'rencana_penanganan' => $rencana_penanganan,
				'batas_waktu' => $batas_waktu,
				'keputusan' => $keputusan,
				'tingkat_akibat' => $tingkat_akibat,
				'tingkat_kemungkinan' => $tingkat_kemungkinan,
				'tingkat_risiko' => $tingkat_risiko,
				'sisa_risiko' => $sisa_risiko,
				'pic' => $pic,
			);
			$analisis_resiko = $this->mdl_muturesiko->editdata($tbl_get,$data2,$id);
						
					$tbl_get = 'simpro_tbl_daftar_risiko';
					$data3 = array(
						//'proyek_id' => $proyek_id,
						'konteks' => $risiko,
						'akibat' => $akibat,
						'tgl' => $tgl,
						'user_id' => $user_id,
						//'ar_id' => $ar_id ,
					);
					$this->mdl_muturesiko->editdata($tbl_get,$data3,$id);
					$tbl_get = 'simpro_tbl_penanganan_risiko';
					$data4 = array(
						//'proyek_id' => $proyek_id,
						'risiko' => $risiko,
						//'akibat' => $akibat,
						'tgl' => $tgl,
						'user_id' => $user_id,
						//'ar_id' => $ar_id ,
					);
					$this->mdl_muturesiko->editdata($tbl_get,$data4,$id);
				
			break;
			case 'daftar_risiko' :
			$konteks = $this->input->post('editkonteks');
			$penyebab = $this->input->post('editpenyebab');
			$akibat = $this->input->post('editakibat');
			$kemungkinan_terjadi = $this->input->post('editkemungkinan_terjadi');
			$faktor_positif = $this->input->post('editfaktor_positif');
			$prioritas = $this->input->post('editprioritas');
			$tbl_get = 'simpro_tbl_daftar_risiko';
			$data = array(
				//'proyek_id' => $proyek_id, 
				'konteks' => $konteks,
				'akibat' => $akibat,
				'penyebab' => $penyebab,
				'tgl' => $tgl,
				'user_id' => $user_id,
				'kemungkinan_terjadi' => $kemungkinan_terjadi,
				'faktor_positif' => $faktor_positif,
				'prioritas' => $prioritas,
			); 


			$ar_id = $this->input->post('editar_id');
			$tingkat_akibat = $this->input->post('edittingkat_akibat');
			$tingkat_kemungkinan = $this->input->post('edittingkat_kemungkinan');
			$tingkat_risiko = $this->input->post('edittingkat_risiko');

			$data_ar = array(
				'tingkat_akibat' => $tingkat_akibat, 
				'tingkat_kemungkinan' => $tingkat_kemungkinan, 
				'tingkat_risiko' => $tingkat_risiko
			);

			$this->db->where(array('ar_id' => $ar_id));
			$this->db->update('simpro_tbl_analisis_risiko',$data_ar);

			break;
			case 'lap_penanganan' :
			$risiko=$this->input->post('editrisiko');
			$tingkat_risiko=$this->input->post('edittingkat_risiko');
			$nilai_risiko=$this->input->post('editnilai_risiko');
			$realisasi_tindakan=$this->input->post('reditealisasi_tindakan');
			$biaya_memitigasi=$this->input->post('editbiaya_memitigasi');
			$biaya_sisa=$this->input->post('editbiaya_sisa');
			$pic=$this->input->post('editpic');
			$target_tingkat_risiko=$this->input->post('edittarget_tingkat_risiko');
			$realisasi_tingkat_sisa=$this->input->post('editrealisasi_tingkat_sisa');
			$status_risiko=$this->input->post('editstatus_risiko');
			$tgl_aak=$this->input->post('edittgl_aak');
			//$ar_id = $this->db->query("select ar_id from simpro_tbl_analisis_risiko where proyek_id = $proyek_id limit 1");
		
			$tbl_get = 'simpro_tbl_penanganan_risiko';
			
			$data = array(
				//'proyek_id' => $proyek_id, 
				'risiko' => $risiko,
				'tgl_aak' => $tgl_aak,
				'tgl' => $tgl,
				'user_id' => $user_id,
				//'ar_id' => $ar_id->row()->ar_id,
				//'ar_id' => 0,
				'tingkat_risiko_id' => $tingkat_risiko,
				'realisasi_tindakan' => $realisasi_tindakan,
				'nilai_risiko' => $nilai_risiko,
				'realisasi_sisa_risiko_id' => $realisasi_tingkat_sisa,
				'biaya_memitigasi' => $biaya_memitigasi,
				'biaya_sisa_risiko' => $biaya_sisa,
				'pic' => $pic,
				'target_sisa_risiko_id' => $target_tingkat_risiko,
				'status_risiko_id' => $status_risiko,
				
			);
			break;
		}
		if($aksi== 0){
			$this->mdl_muturesiko->editdata($tbl_get,$data,$id);
			//var_dump($data);
		}
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else
		{	
			//var_dump($data);
			$this->db->trans_commit();
		}
	}

	function lap_penanganan($bln,$thn){
		$blnno = $this->bulantono($bln);
		//$data['bln']= $this->bulan($blnno);
		$data['bln']= $blnno;
		$data['thn']=$thn;
		$this->load->view('lap_penanganan',$data);
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

	function bulantono($no){
		if ($no == 'Januari'){
			$bulan = 1;
		} elseif ($no == 'Februari') {
			$bulan = 2;
		} elseif ($no == 'Maret') {
			$bulan = 3;
		} elseif ($no == 'April') {
			$bulan = 4;
		} elseif ($no == 'Mei') {
			$bulan = 5;
		} elseif ($no == 'Juni') {
			$bulan = 6;
		} elseif ($no == 'Juli') {
			$bulan = 7;
		} elseif ($no == 'Agustus') {
			$bulan = 8;
		} elseif ($no == 'September') {
			$bulan = 9;
		} elseif ($no == 'Oktober') {
			$bulan = 10;
		} elseif ($no == 'November') {
			$bulan = 11;
		} elseif ($no == 'Desember') {
			$bulan = 12;
		}
		return $bulan;
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
			$bln[] = $bulan;
		}

		echo '{"data":'.json_encode($bln).'}';
	}
	
	function gettahun(){
		$tahun = array();
		$thn = array();
		for ($i=2000; $i <= 2100; $i++) { 
			$tahun['value'] = $i;
			$tahun['text'] = $i;
			$thn[] = $tahun;
		}
		echo '{"data":'.json_encode($thn).'}';
	}
	
	function gettanggal(){
		$tanggal = array();
		$tgl = array();
		for ($i=1; $i <= 31; $i++) { 
			$tanggal['value'] = $i;
			$tanggal['text'] = $i;
			$tgl[] = $tanggal;
		}
		echo '{"data":'.json_encode($tgl).'}';
	}
	
	function get_status_jenis(){
		$data = $this->mdl_muturesiko->get_status_jenis();	
		$this->_out($data);
	}
	
	function get_status_risiko(){
		$data = $this->mdl_muturesiko->get_status_risiko();	
		$this->_out($data);
	}
	
	function get_tingkat_akibat(){
		$data = $this->mdl_muturesiko->get_tingkat_akibat();	
		$this->_out($data);
	}
	
	function get_tingkat_kemungkinan(){
		$data = $this->mdl_muturesiko->get_tingkat_kemungkinan();	
		$this->_out($data);
	}
	
	function get_tingkat_risiko(){
		$data = $this->mdl_muturesiko->get_tingkat_risiko();	
		$this->_out($data);
	}
	
	function get_sisa_risiko(){
		$data = $this->mdl_muturesiko->get_sisa_risiko();	
		$this->_out($data);
	}
	
	private function _out($data)
	{
		$callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '';
		if ($data['total'] > 0)
		{
			$output = json_encode($data);
			//$output = json_encode($data,JSON_NUMERIC_CHECK);
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
}