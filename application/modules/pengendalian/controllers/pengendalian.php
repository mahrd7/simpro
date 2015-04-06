<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pengendalian extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');						
		$this->load->model('mdl_pengendalian');			
		if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");

		$proyek_id_utama = $this->session->userdata('proyek_id');
		$n_rat_id = $this->db->query("select * from simpro_rat_item_tree where id_proyek_rat = (select id_tender from simpro_tbl_proyek where proyek_id = $proyek_id_utama)")->num_rows();
		$n_rat_name = $this->db->query("select * from simpro_rat_item_tree where id_proyek_rat = (select id_proyek_rat from simpro_m_rat_proyek_tender
								where nama_proyek = (
								select proyek from simpro_tbl_proyek where proyek_id = $proyek_id_utama
								))")->num_rows();
		$n_rap = $this->db->query("select * from simpro_rap_item_tree where id_proyek = $proyek_id_utama")->num_rows();
		
		if ($n_rap <= 0) die("<h2 align='center'>Silahkan buat RAP terlebih dahulu!</h2>");
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}

	function pilih_daftar_analisa()
	{	
		$this->load->view('pilih_daftar_analisa');
	}

	function daftar_analisa($tgl_rab="")
	{
		$data['tgl_rab']= $tgl_rab;
		if ($this->input->get('proyek')) {
			$data['proyek']=$this->input->get('proyek');
		} else {
			$data['proyek']='';
		}
		$this->load->view('daftar_analisa',$data);
	}
	
	function getdata($info)
	{
		switch ($info) {
			case 'kontrak_kini':
			$tbl_info = 'simpro_tbl_kontrak_terkini';
			break;
		}
		$data = $this->mdl_pengendalian->getdata($tbl_info);
		
		echo '{"data":'.json_encode($data).'}';
	}

	public function kontrak_kini($kunci="",$bln="",$thn="")
	{	
		// $tgl_rab = $this->input->get('tgl_rab');
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data['bln']= $this->bulan($bln); 
		$data['blnno']= $bln;
		$data['thn']=$thn;
		if ($this->input->get('tgl_rab')) {
			$tgl_rab= $this->input->get('tgl_rab');
		} else {
			$tgl_rab = $thn.'-'.$bln.'-01';
		}

		if ($this->input->get('tgl_awal')) {
			$tgl_awal= $this->input->get('tgl_awal');
		} else {
			$tgl_awal = $thn.'-'.$bln.'-01';
		}
		$data['tgl_rab']= $tgl_rab;
		$data['tgl_awal']= $tgl_awal;

		$cek = $this->mdl_pengendalian->cek('proyek',$proyek_id,'');

		if ($cek == '') {
			$this->mdl_pengendalian->copy('awal',$tgl_rab,$proyek_id);
		}
		
		$kodes = $this->mdl_pengendalian->get_data_kode($proyek_id,$tgl_rab);
		$data['kodes']= $kodes;
		$data['kunci'] = $kunci;
		$this->load->view('kontrak_kini',$data);
	}

	public function get_data_kontrak_terkini()
	{	
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		if ($this->input->get('tgl_rab')) {
			$tgl_rab= $this->input->get('tgl_rab');
		} else {
			$tgl_rab = $this->input->get('thn').'-'.$this->input->get('bln').'-01';
		}
		$data = $this->mdl_pengendalian->get_data_kontrak_terkini($proyek_id,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_tanggal_kontrak_terkini()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_tanggal_kontrak_terkini($proyek_id);
		echo $data;
	}

	function get_tanggal_rencana_kontrak_terkini()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_tanggal_rencana_kontrak_terkini($proyek_id);
		echo $data;
	}

	function get_sub_kontrk_terkini($page="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($page) {
			case 'kk':
				$kode = $this->input->get('kode');
				$tgl_rab = $this->input->get('tgl_rab');
				$data = $this->mdl_pengendalian->get_sub_kontrk_terkini($page,$proyek_id,$kode,$tgl_rab);		
				echo '{"data":'.json_encode($data).'}';
			break;
			case 'rkk':
				$kode = $this->input->get('kode');
				$tgl_rab = $this->input->get('tgl_rab');
				$data = $this->mdl_pengendalian->get_sub_kontrk_terkini($page,$proyek_id,$kode,$tgl_rab);		
				echo '{"data":'.json_encode($data).'}';
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	public function kontrak_terkini()
	{			
		$this->load->view('kontrak_terkini');
	}

	public function pilih_lpf()
	{	
		$this->load->view('pilih_lpf');
	}
	
	public function lpf($kunci="",$bln="",$thn="")
	{	
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data['bln']= $this->bulan($bln);
		$data['blnno']= $bln;
		$data['thn']=$thn;
		if(!$this->input->get('tgl_rab')){
			echo "Access Forbidden";
		}else{
			$tgl_rab=$this->input->get('tgl_rab');
			$data['tgl_rab']= $tgl_rab;
			$data['kunci'] = $kunci;
			$this->load->view('lpf',$data);
		}
	}
	
	public function mos()
	{	
		$this->load->view('mos');
	}


	public function kajian_kendala_proyek()
	{	
		$this->load->view('kkp');
	}

	public function proses_laporan()
	{	
		$this->load->view('proses_laporan');
	}

	public function pilihcosttogo()
	{	
		$this->load->view('pilih_cost_to_go');
	}

	public function costtogo($kunci="",$tgl_rab="")
	{	
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		if(!$tgl_rab){
			echo "Access Forbidden";
		}else{
			$data['bln']= '';
			$data['thn']= '';
			$data['tgl_rab']= $tgl_rab;
			$data['kunci'] = $kunci;
			$this->load->view('cost_to_go',$data);
		}

	}
	
	public function pilihcurrentbudget()
	{	
		$this->load->view('pilih_current_budget_2');
	}

	public function currentbudget($kunci="",$tgl_rab="")
	{	
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		if(!$tgl_rab){
			echo "Access Forbidden";
		}else{
			$data['bln']= '';
			$data['thn']= '';
			$data['tgl_rab']= $tgl_rab;
			$data['kunci'] = $kunci;
			$this->load->view('current_budget_2',$data);
		}
	}

	public function pilih_rencana_kontrak_kini()
	{	
		$this->load->view('pilih_rencana_kontrak_kini');
	}

	public function rencana_kontrak_kini($kunci="",$tgl_rab="")
	{	
		if ($tgl_rab){			
			$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
			$blnno = trim($chars[1]);
			$bulanplus1 = $blnno + 1;
			$data['bln']= $this->bulan($blnno);
			$data['bln1']= $this->bulan($bulanplus1);
			$data['thn']= trim($chars[0]);
			if ($bulanplus1 > 12) {
				$data['thn'] = trim($chars[0] + 1);
			}
			$data['tgl_rab'] = $tgl_rab;
			$data['kunci'] = $kunci;
			$this->load->view('rencana_kontrak_kini_2',$data);
		} else {
			echo "Access Forbidden";
		}
	}
	
	public function pilih_rp_beban_kontrak()
	{	
		$this->load->view('pilih_rpbk');
	}

	public function rp_beban_kontrak($bln,$thn)
	{	
		$blnno = $this->bulantono($bln);
		$data['bln']= $this->bulan($blnno);
		$data['thn']=$thn;
		$this->load->view('rpbk',$data);
	}	

	public function edit_rpbk($kunci="",$tgl_rab="")
	{	
		if ($tgl_rab!=""){			
			$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
			$blnno = trim($chars[1]);
			$bulanmin1 = $blnno - 1;
			$bulanplus1 = $blnno + 1;
			$data['bln']= $this->bulan($blnno);
			$data['bln1']= $this->bulan($bulanplus1);
			$data['thn']= trim($chars[0]);
			if ($bulanplus1 > 12) {
				$data['thn'] = trim($chars[0] + 1);
			}
			// $data['bln']= $this->bulan($blnno);
			$data['bln_1']= $this->bulan($bulanmin1);
			// $data['bln1']= $this->bulan($bulanplus1);
			// $data['thn']= trim($chars[0]);
			$data['tgl_rab'] = $tgl_rab;
			$data['kunci'] = $kunci;
			$this->load->view('edit_rpbk',$data);
		} else {
			echo "Access Forbidden";
		}
	}
	
	public function pilih_rincian_rencana_kerja()
	{	
		$this->load->view('pilih_rencana_kerja');
	}

	public function rincian_rencana_kerja($kunci="",$tgl_rab="")
	{	
		if ($tgl_rab!=""){			
			$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
			$blnno = trim($chars[1]);
			$bulanplus1 = $blnno + 1;
			$bulanplus2 = $blnno + 2;
			$data['bln']= $this->bulan($blnno);
			$data['bln1']= $this->bulan($bulanplus1);
			$data['bln2']= $this->bulan($bulanplus2);
			$data['thn']= trim($chars[0]);
			$data['tgl_rab'] = $tgl_rab;
			$data['kunci'] = $kunci;
			$this->load->view('rencana_kerja',$data);
		} else {
			echo "Access Forbidden";
		}
	}
	
	public function getsubbidang(){
		$data = $this->mdl_pengendalian->getsubbidang();
		echo '{"data":'.json_encode($data).'}';
	}

	public function getsubbidangkode(){
		$data = $this->mdl_pengendalian->getsubbidangkode();
		echo '{"data":'.json_encode($data).'}';
	}

	public function getdivisicombo(){
		$data = $this->mdl_pengendalian->getdivisicombo();
		echo '{"data":'.json_encode($data).'}';
	}

	public function getproyekcombo(){
		$divisi_kode = $this->input->get('divisi_kode');
		// echo $divisi_kode;
		$data = $this->mdl_pengendalian->getproyekcombo($divisi_kode);
		echo '{"data":'.json_encode($data).'}';
	}

	public function gettanggalcombo(){		
		$no_spk = $this->input->get('no_spk');
		$data = $this->mdl_pengendalian->gettanggalcombo($no_spk);
		echo '{"data":'.json_encode($data).'}';
	}

	public function pilih_cashflow()
	{	
		$this->load->view('pilih_cashflow');
	}

	public function cashflow($kunci="",$tgl_rab="")
	{	
		if ($tgl_rab!=""){			
			$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
			$blnno = trim($chars[1]);
			$bulanplus1 = $blnno + 1;
			$bulanplus2 = $blnno + 2;
			$data['bln']= $this->bulan($blnno);
			$data['bln1']= $this->bulan($bulanplus1);
			$data['bln2']= $this->bulan($bulanplus2);
			$data['bln_no']= trim($chars[1]);
			$data['thn']= trim($chars[0]);
			$data['tgl_rab'] = $tgl_rab;
			$data['kunci'] = $kunci;
			$this->load->view('cashflow',$data);
		} else {
			echo "Access Forbidden";
		}
	}
		
	public function penggunaan_alat()
	{	
		$this->load->view('penggunaan_peralatan');
	}		
	
	public function schedule()
	{
		$this->load->view('schedule');
	}

	public function gantt()
	{
		$this->load->view('gantt');
	}

	public function daftar_alat()
	{
		$this->load->view('daftar_peralatan');
	}

	public function getlistalat()
	{
		$data = $this->mdl_pengendalian->getlistalat();
		echo '{"data":'.json_encode($data).'}';
	}

	function getdaftar_alat()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->getdaftar_alat($proyek_id);

		echo $data;
	}

	function getsch2()
	{
		$q = $this->db->query("select 
								id, 
								uraian, 
								unit, 
								tgl_awal, 
								tgl_akhir, 
								bobot, 
								(extract(day from tgl_akhir) - extract(day from tgl_awal)) as selisih,
								extract(day from tgl_awal) as tgl_awal_day, 
								extract(month from tgl_awal) as tgl_awal_month, 
								extract(year from tgl_awal) as tgl_awal_year 
								from simpro_tbl_sch_proyek order by id asc");     
		echo '{"data":'.json_encode($q->result_object()).'}';
	}

	function insertschparent()
	{
		$id = $this->input->get('lastid');
		$tgl = $this->input->get('tgl_full');
		$data = array(
			'id_sch_proyek' => $id,
			'tgl_sch_parent' => $tgl,
			'bobot_parent' => '0'
		 );
		// var_dump($data);
		$this->mdl_pengendalian->insertschparent($data);
	}

	function getlastid()
	{
		$q = $this->db->query("select 
								id, 
								uraian, 
								unit, 
								tgl_awal, 
								tgl_akhir, 
								bobot, 
								(extract(day from tgl_akhir) - extract(day from tgl_awal)) as selisih,
								extract(day from tgl_awal) as tgl_awal_day, 
								extract(month from tgl_awal) as tgl_awal_month, 
								extract(year from tgl_awal) as tgl_awal_year 
								from simpro_tbl_sch_proyek order by id desc limit 1");     
		echo '{"data":'.json_encode($q->result_object()).'}';
	}

	function getsendjson()
	{
		$id = $this->input->get('id');
		$q = $this->db->query("SELECT id, id_sch_proyek, tgl_sch_parent, bobot_parent FROM simpro_tbl_sch_proyek_parent where id_sch_proyek = '$id' order by tgl_sch_parent asc");     
		echo '{"data":'.json_encode($q->result_object()).'}';
	}

	function updateschparent()
	{
		$id = $this->input->get('idparent');
		$bobot_parent = $this->input->get('bobotparent');
		$data = array(
			'bobot_parent' => $bobot_parent
		);		
		$this->mdl_pengendalian->updateschparent($id,$data);
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
		} elseif ($no == 13) {
			$bulan = 'Januari';
		} elseif ($no == 14) {
			$bulan = 'Februari';
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

	function getlistsatuan(){
		$data = $this->mdl_pengendalian->getlistsatuan();
		echo '{"data":'.json_encode($data).'}';
	}

	function getedithargasatuan(){
		$no_spk=$this->input->get('no_spk');
		$tgl_rab=$this->input->get('tgl_rab');
		$data = $this->mdl_pengendalian->getedithargasatuan($no_spk,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_data_induk($tgl_rab)
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data_induk = $this->mdl_pengendalian->get_data_induk($proyek_id,$tgl_rab);
		echo $data_induk;
	}

	function insert($tbl_info="")
	{

		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';//proyek_id
		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');

		switch ($tbl_info) {
			case 'kontrak_terkini':

			$tgl_rab = $this->input->post('tgl_rab');
			$tgl_awal = $this->input->post('tgl_awal');
			$kode = $this->input->post('kode');
			$tahap_pekerjaan = $this->input->post('tahap_pekerjaan');
			$satuan = $this->input->post('satuan');
			$volume = $this->input->post('volume');
			$harga_satuan = $this->input->post('harga_satuan');

			$data = array(
				'proyek_id' => $proyek_id, 
				'ip_update' => $ip_update,
				'divisi_id' => $divisi_id,
				'user_update' => $user_update,
				'waktu_update' => $waktu_update,
				'tgl_update' => $tgl_update,
				'tgl_rab' => $tgl_rab,
				'tgl_awal' => $tgl_awal,
				'kode' => $kode,
				'tahap_pekerjaan' => $tahap_pekerjaan,
				'satuan' => $satuan,
				'volume' => $volume,
				'harga_satuan' => $harga_satuan
			);

			$this->mdl_pengendalian->insert($tbl_info,$data);
			break;
			case 'sub_kontrak_terkini':
				$kode_induk = $this->input->post('kds');
				$tgl_rab = $this->input->post('tgl_rab');
				$tgl_awal = $this->input->post('tgl_awal');
				$kode = $this->input->post('kode');
				$tahap_pekerjaan = $this->input->post('tahap_pekerjaan');
				$satuan = $this->input->post('satuan');
				$volume = $this->input->post('volume');
				$harga_satuan = $this->input->post('harga_satuan');

				$data = array(
					'proyek_id' => $proyek_id, 
					'ip_update' => $ip_update,
					'divisi_id' => $divisi_id,
					'user_update' => $user_update,
					'waktu_update' => $waktu_update,
					'tgl_update' => $tgl_update,
					'tgl_rab' => $tgl_rab,
					'tgl_awal' => $tgl_awal,
					'kode' => $kode,
					'tahap_pekerjaan' => $tahap_pekerjaan,
					'satuan' => $satuan,
					'volume' => $volume,
					'harga_satuan' => $harga_satuan,
					'kode_induk' => $kode_induk
				);

				$this->mdl_pengendalian->insert($tbl_info,$data);

				$new_kt = "1.".$kode;
				$new_kit = "1.".$kode_induk;

				$sql = "select * from simpro_costogo_item_tree where id_proyek='$proyek_id' and kode_tree='$new_kt'";
				$q = $this->db->query($sql);
				$row_ctg = $q->row();
				$itung_ctg = $q->num_rows();

				$sql2 = "select * from simpro_current_budget_item_tree where id_proyek='$proyek_id' and kode_tree='$new_kt'";
				$q2 = $this->db->query($sql2);
				$row_cbd = $q2->row();
				$itung_cbd = $q2->num_rows();

				$sql3 = "select * from simpro_tbl_satuan where satuan_id='$satuan'";
				$q3 = $this->db->query($sql3);
				$row_sat = $q3->row();

					$datainsert = array(
						'tree_parent_id' => "0",
						'tree_item' => $tahap_pekerjaan,
						'tree_parent_kode' => $new_kit,
						'tree_satuan' => $row_sat->satuan_nama,
						'id_proyek' =>  $proyek_id,
						'volume' => "1",
						'kode_tree' =>  $new_kt,
						'tanggal_kendali' => $tgl_rab
					);

				if($itung_ctg>0){
					$this->db->where('costogo_item_tree', $row_ctg->costogo_item_tree);
					$this->db->delete('simpro_costogo_item_tree');

					$resin = $this->db->insert('simpro_costogo_item_tree', $datainsert);
				} else {
					$resin = $this->db->insert('simpro_costogo_item_tree', $datainsert);
				}

				if($itung_cbd>0){
					$this->db->where('current_budget_item_tree', $row_ctg->current_budget_item_tree);
					$this->db->delete('simpro_current_budget_item_tree');

					$resin2 = $this->db->insert('simpro_current_budget_item_tree', $datainsert);
				} else {
					$resin2 = $this->db->insert('simpro_current_budget_item_tree', $datainsert);
				}
					


			break;
			case 'rencana_kontrak_terkini':

			$tgl_rab = $this->input->post('tgl_rab');
			$kode = $this->input->post('kode');
			$tahap_pekerjaan = $this->input->post('tahap_pekerjaan');
			$satuan = $this->input->post('satuan');
			$volume = $this->input->post('volume');
			$harga_satuan = $this->input->post('harga_satuan');

			$data = array(
				'proyek_id' => $proyek_id, 
				'ip_update' => $ip_update,
				'divisi_id' => $divisi_id,
				'user_update' => $user_update,
				'waktu_update' => $waktu_update,
				'tgl_update' => $tgl_update,
				'tgl_rab' => $tgl_rab,
				'kode' => $kode,
				'tahap_pekerjaan' => $tahap_pekerjaan,
				'satuan' => $satuan,
				'volume' => $volume,
				'harga_satuan' => $harga_satuan
			);

			$this->mdl_pengendalian->insert($tbl_info,$data);
			break;
			case 'sub_rencana_kontrak_terkini':
				$kode_induk = $this->input->post('kds');
				$tgl_rab = $this->input->post('tgl_rab');
				$kode = $this->input->post('kode');
				$tahap_pekerjaan = $this->input->post('tahap_pekerjaan');
				$satuan = $this->input->post('satuan');
				$volume = $this->input->post('volume');
				$harga_satuan = $this->input->post('harga_satuan');

				$data = array(
					'proyek_id' => $proyek_id, 
					'ip_update' => $ip_update,
					'divisi_id' => $divisi_id,
					'user_update' => $user_update,
					'waktu_update' => $waktu_update,
					'tgl_update' => $tgl_update,
					'tgl_rab' => $tgl_rab,
					'kode' => $kode,
					'tahap_pekerjaan' => $tahap_pekerjaan,
					'satuan' => $satuan,
					'volume' => $volume,
					'harga_satuan' => $harga_satuan,
					'kode_induk' => $kode_induk
				);

				$this->mdl_pengendalian->insert($tbl_info,$data);
			break;
		}
	}

	function insert_sub_kontrak_terkini()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');

		$kode = $this->input->get('kode');
		$tgl_rab = $this->input->get('tgl_rab');
		$total_kendali = $this->input->post('volume')*$this->input->post('harga_satuan');
		$sub_volume1 = '0';
		$sub_harga1 = '0';
		$total = '0';

		$data_kontrak_terkini = array(
			'tahap_kode_kendali'=> $this->input->post('kode'), 
			'tahap_nama_kendali'=> $this->input->post('tahap_pekerjaan'), 
			'tahap_satuan_kendali'=> $this->input->post('satuan'), 
			'proyek_id'=> $proyek_id, 
			'tahap_volume_kendali'=> $this->input->post('volume'), 
			'tahap_kode_induk_kendali'=> $kode, 
			'tahap_tanggal_kendali'=> $tgl_rab, 
			'tahap_harga_satuan_kendali'=> $this->input->post('harga_satuan'), 
			'tahap_total_kendali'=> $total_kendali, 
			'user_update'=> $user_update, 
			'tgl_update'=> $tgl_update, 
			'ip_update'=> $ip_update, 
			'divisi_update'=> $divisi_id, 
			'waktu_update'=> $waktu_update, 
			'tahap_volume_kendali_new'=> $sub_volume1, 
			'tahap_harga_satuan_kendali_new'=> $sub_harga1, 
			'tahap_total_kendali_new'=> $total
		);

		$data_total_pekerjaan = array(
			'tahap_kode_kendali'=> $this->input->post('kode'),
			'tahap_nama_kendali'=> $this->input->post('tahap_pekerjaan'),
			'tahap_satuan_kendali'=> $this->input->post('satuan'),
			'proyek_id'=> $proyek_id,
			'tahap_volume_kendali'=> $this->input->post('volume'),
			'tahap_kode_induk_kendali'=> $kode,
			'tahap_tanggal_kendali'=> $tgl_rab,
			'tahap_harga_satuan_kendali'=> $this->input->post('harga_satuan'),
			'tahap_total_kendali'=> $total_kendali,
			'user_id_update'=> $user_update,
			'tgl_update'=> $tgl_update,
			'ip_update'=> $ip_update, 
			'divisi_id_update'=> $divisi_id, 
			'waktu_update'=> $waktu_update, 
			'tahap_volume_kendali_new'=> $sub_volume1, 
			'tahap_harga_satuan_kendali_new'=> $sub_harga1, 
			'tahap_total_kendali_new'=> $total
		);

		$data_current_budget = array(
			'tahap_kode_kendali' => '1.'.$this->input->post('kode'), 
			'tahap_nama_kendali' => $this->input->post('tahap_pekerjaan'), 
			'tahap_satuan_kendali' => $this->input->post('satuan'), 
			'proyek_id' => $proyek_id, 
			'tahap_volume_kendali' => $this->input->post('volume'), 
			'tahap_kode_induk_kendali' => '1.'.$kode, 
			'tahap_tanggal_kendali' => $tgl_rab, 
			'tahap_harga_satuan_kendali' => $this->input->post('harga_satuan'), 
			'tahap_total_kendali' => $total_kendali, 
			'user_id' => $user_update, 
			'tgl_update' => $tgl_update, 
			'ip_update'=> $ip_update, 
			'divisi_id'=> $divisi_id, 
			'waktu_update'=> $waktu_update
		);

		$data_total_rkp = array(
			'tahap_kode_kendali' => $this->input->post('kode'), 
			'tahap_nama_kendali' => $this->input->post('tahap_pekerjaan'), 
			'tahap_satuan_kendali' => $this->input->post('satuan'), 
			'proyek_id' => $proyek_id, 
			'tahap_volume_kendali' => $this->input->post('volume'), 
			'tahap_kode_induk_kendali' => $kode, 
			'tahap_tanggal_kendali' => $tgl_rab, 
			'tahap_harga_satuan_kendali' => $this->input->post('harga_satuan'), 
			'tahap_total_kendali' => $total_kendali, 
			'user_update' => $user_update, 
			'tgl_update' => $tgl_update, 
			'ip_update'=> $ip_update, 
			'divisi_update'=> $divisi_id, 
			'waktu_update'=> $waktu_update
		);

		$data_cost_to_go = array(
			'tahap_kode_kendali' => '1.'.$this->input->post('kode'), 
			'tahap_nama_kendali' => $this->input->post('tahap_pekerjaan'), 
			'tahap_satuan_kendali' => $this->input->post('satuan'), 
			'proyek_id' => $proyek_id, 
			'tahap_volume_kendali' => $this->input->post('volume'), 
			'tahap_kode_induk_kendali' => '1.'.$kode, 
			'tahap_tanggal_kendali' => $tgl_rab, 
			'tahap_harga_satuan_kendali' => $this->input->post('harga_satuan'), 
			'tahap_total_kendali' => $total_kendali, 
			'user_id' => $user_update, 
			'tgl_update' => $tgl_update, 
			'ip_update'=> $ip_update, 
			'divisi_id'=> $divisi_id, 
			'waktu_update'=> $waktu_update
		);

		$this->mdl_pengendalian->insert(
				'kontrak_terkini',$data_kontrak_terkini,
				$data_total_pekerjaan,
				$data_current_budget,
				$data_total_rkp,
				$data_cost_to_go
				);
	}

	function get_sub_kode()
	{
		$kode = $this->input->get('kode');
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$info = $this->input->get('info');
		$tgl_rab= $this->input->get('tgl_rab');

		switch ($info) {
			case 'cost_to_go':
				$tbl_info='cost_togo';
			break;
			case 'kontrak_terkini':
				$tbl_info='kontrak_terkini';
			break;
			case 'current_budget':
				$tbl_info='current_budget';
			break;
			case 'rencana_kontrak_kini':
				$tbl_info='rencana_kontrak_kini';
			break;
		}
		$data = $this->mdl_pengendalian->get_sub_kode($proyek_id,$tbl_info,$kode,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_kode($page="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($page) {
			case 'kk':
				$tgl_rab= $this->input->get('tgl_rab');
				$data = $this->mdl_pengendalian->get_kode($page,$proyek_id,$tgl_rab);
				echo '{"data":'.json_encode($data).'}';
			break;
			case 'rkk':
				$tgl_rab= $this->input->get('tgl_rab');
				$data = $this->mdl_pengendalian->get_kode($page,$proyek_id,$tgl_rab);
				echo '{"data":'.json_encode($data).'}';
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_kontrak_terkini_new()
	{
		$tgl_rab=$this->input->get('tgl_rab');
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_kontrak_terkini_new($proyek_id,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function update_kk($page="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$id = $this->input->post('id');
		$kode = $this->input->post('kode');
		$tgl_rab = $this->input->post('tgl_rab');
		$vol = $this->input->post('vol');
		$vol_tambah = $this->input->post('vol_tambah');
		$vol_kurang = $this->input->post('vol_kurang');
		$vol_eskalasi = $this->input->post('vol_eskalasi');
		$harga_eskalasi = $this->input->post('harga_eskalasi');

		switch ($vol_tambah) {
			case '':
				$vol_tambah = 0;
			break;
		}
		switch ($vol_kurang) {
			case '':
				$vol_kurang = 0;
			break;
		}
		switch ($vol_eskalasi) {
			case '':
				$vol_eskalasi = 0;
			break;
		}
		switch ($harga_eskalasi) {
			case '':
				$harga_eskalasi = 0;
			break;
		}

		$jml_vol_kk = $vol + $vol_tambah - $vol_kurang;

		$data_kk = array(
		'tahap_volume_kendali_new' => $vol_tambah,
		'tahap_volume_kendali_kurang' => $vol_kurang,
		'volume_eskalasi' => $vol_eskalasi,
		'harga_satuan_eskalasi' => $harga_eskalasi,
		);

		$data = array(
			'tgl_rab' => $tgl_rab,
			'proyek_id' => $proyek_id,
			'kode' => $kode,
			'jml_vol_kk' => $jml_vol_kk
		);

		$this->mdl_pengendalian->update_kk($page,$id,$data_kk,$data);
	}

	function get_tgl($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		switch ($info) {
			case 'lpf':
				$data = $this->mdl_pengendalian->get_tgl($proyek_id,$info);
			break;
			case 'rkk':
				$data = $this->mdl_pengendalian->get_tgl($proyek_id,$info);
			break;
			case 'rencana_kontrak':
				$data = $this->mdl_pengendalian->get_tgl($proyek_id,$info);
			break;
			default:
			case 'rpbk':
				$data = $this->mdl_pengendalian->get_tgl($proyek_id,$info);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
		echo '{"data":'.json_encode($data).'}';
	}

	function get_data_lpf_induk($tgl_rab)
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_data_lpf_induk($proyek_id,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_data_lpf()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab = $this->input->get('tgl_rab');

		$data = $this->mdl_pengendalian->get_data_lpf($proyek_id,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_data_mos()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$data = $this->mdl_pengendalian->get_data_mos($proyek_id);
		echo '{"data":'.json_encode($data).'}';
	}

	function update_lpf()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$id = $this->input->post('id');
		$kode = $this->input->post('kode');
		$tgl_rab = $this->input->post('tgl_rab');
		$tahap_diakui_bobot = $this->input->post('tahap_diakui_bobot');
		$vol_total_tagihan = $this->input->post('vol_total_tagihan');
		$tagihan_cair = $this->input->post('tagihan_cair');
		$tagihan_rencana_piutang = $this->input->post('tagihan_rencana_piutang');

		switch ($tahap_diakui_bobot) {
			case '':
				$tahap_diakui_bobot = 0;
			break;
		}
		switch ($vol_total_tagihan) {
			case '':
				$vol_total_tagihan = 0;
			break;
		}
		switch ($tagihan_cair) {
			case '':
				$total_tagigan_cair = 0;
			break;
		}
		switch ($tagihan_rencana_piutang) {
			case '':
				$tagihan_rencana_piutang = 0;
			break;
		}

		$data_lpf = array(
			'tahap_diakui_bobot' => $tahap_diakui_bobot,
			'vol_total_tagihan' => $vol_total_tagihan,
			'tagihan_cair' => $tagihan_cair,
			'tagihan_rencana_piutang' => $tagihan_rencana_piutang
		);

		$data = array(
			'kode' => $kode, 
			'tgl_rab' => $tgl_rab, 
			'proyek_id' => $proyek_id
		);

		$this->mdl_pengendalian->update_lpf($id,$data_lpf,$data);
	}

	function get_data_cost_to_go($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab = $this->input->get('tgl_rab');

		switch ($info) {
			case 'cost_togo':
				$data = $this->mdl_pengendalian->get_data_cost_to_go($proyek_id,$tgl_rab,$info);
				echo $data;
			break;
			case 'current_budget':
				$data = $this->mdl_pengendalian->get_data_cost_to_go($proyek_id,$tgl_rab,$info);
				echo $data;
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function insert_ctg($info="",$tgl_rab="")
	{
		if ($tgl_rab=="") {
			echo "Access Forbidden";
		} else {
			$proyek_id = $this->session->userdata('proyek_id'); 
			//$proyek_id = '1';
			$ip_update = $this->session->userdata('ip_address');
			$divisi_id = $this->session->userdata('divisi_id');
			$user_update = $this->session->userdata('uid');
			$waktu_update=date('H:i:s');		
			$tgl_update=date('Y-m-d');

			$data = array(
			'tahap_kode_kendali' => $this->input->post('kode'),
			'tahap_kode_induk_kendali' => '',
			'tahap_nama_kendali' => $this->input->post('tahap_pekerjaan'),
			'tahap_satuan_kendali' => $this->input->post('satuan'),
			'proyek_id' => $proyek_id,
			'tahap_volume_kendali' => $this->input->post('volume_sisa_anggaran'),
			'tahap_tanggal_kendali' => $tgl_rab,
			'user_id' => $user_update,
			'tgl_update' => $tgl_update,
			'ip_update' => $ip_update,
			'divisi_id' => $divisi_id,
			'waktu_update' => $waktu_update
			);
			switch ($info) {
				case 'cost_togo':
					$this->mdl_pengendalian->insert_ctg($data,$info);
				break;
				case 'current_budget':
					$this->mdl_pengendalian->insert_ctg($data,$info);
				break;
				default:
					echo "Access Forbidden";
				break;
			}
		}
	}

	function update_ctg($info="")
	{
		$id = $this->input->post('id');

		$tahap_nama_kendali = $this->input->post('tahap_nama_kendali');
        $tahap_satuan_kendali = $this->input->post('tahap_satuan_kendali');
        $tahap_volume_kendali = $this->input->post('tahap_volume_kendali');
        $tahap_harga_satuan_kendali = $this->input->post('tahap_harga_satuan_kendali');

        switch ($tahap_volume_kendali) {
        	case null:
        		$tahap_volume_kendali = 0;
        	break;
        }

        switch ($tahap_harga_satuan_kendali) {
        	case null:
        		$tahap_harga_satuan_kendali = 1;
        	break;
        }

		$data = array(
            'tahap_nama_kendali' => $tahap_nama_kendali,
            'tahap_satuan_kendali' => $tahap_satuan_kendali,
            'tahap_volume_kendali' => $tahap_volume_kendali,
            'tahap_harga_satuan_kendali' => $tahap_harga_satuan_kendali
		);

		switch ($info) {
			case 'cost_togo':
				$this->mdl_pengendalian->update_ctg($id,$data,$info);
			break;
			case 'current_budget':
				$this->mdl_pengendalian->update_ctg($id,$data,$info);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_sub_ctg($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$kode = $this->input->get('kode');
		$tgl_rab = $this->input->get('tgl_rab');

		switch ($info) {
			case 'cost_togo':
				$data = $this->mdl_pengendalian->get_sub_ctg($proyek_id,$kode,$tgl_rab,$info);		
				echo '{"data":'.json_encode($data).'}';
			break;
			case 'current_budget':
				$data = $this->mdl_pengendalian->get_sub_ctg($proyek_id,$kode,$tgl_rab,$info);		
				echo '{"data":'.json_encode($data).'}';
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function insert_sub_ctg($info="",$kode="",$tgl_rab="")
	{

		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');

		if ($kode!="" || $tgl_rab!="") {
			$data = array(
				'tahap_kode_kendali' => $this->input->post('kode'), 
				'tahap_nama_kendali' => $this->input->post('tahap_pekerjaan'), 
				'tahap_satuan_kendali' => $this->input->post('satuan'), 
				'proyek_id' => $proyek_id, 
				'tahap_volume_kendali' => $this->input->post('volume'), 
				'tahap_kode_induk_kendali' => $kode, 
				'tahap_tanggal_kendali' => $tgl_rab, 
				'tahap_harga_satuan_kendali' => 0, 
				'tahap_total_kendali' => 0, 
				'user_id' => $user_update, 
				'tgl_update' => $tgl_update, 
				'ip_update'=> $ip_update, 
				'divisi_id'=> $divisi_id, 
				'waktu_update'=> $waktu_update
			);

			switch ($info) {
				case 'cost_togo':
					$this->mdl_pengendalian->insert_sub_ctg($data,$info);
				break;	
				case 'current_budget':
					$this->mdl_pengendalian->insert_sub_ctg($data,$info);
				break;			
				default:
					echo "Access Forbidden";
				break;
			}
		} else {
			echo "Access Forbidden";
		}

		
	}

	function get_tanggal_ctg()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_tanggal_ctg($proyek_id);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_tanggal_cb()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_tanggal_cb($proyek_id);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_tanggal_lpf()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_tanggal_lpf($proyek_id);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_tanggal_rencana_kerja()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_tanggal_rencana_kerja($proyek_id);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_data_analisa()
	{		
		$limit = $this->input->get('limit');
		$offset = $this->input->get('start');

		if (!$this->input->get('text')) {
			$text = '';
		} else {
			$text = $this->input->get('text');
		}

		if (!$this->input->get('cbo')) {
			$cbo = '';
		} else {
			$cbo = $this->input->get('cbo');
		}

		$data = $this->mdl_pengendalian->get_data_analisa($limit,$offset,$text,$cbo);
		echo $data;
	}

	function cek_data_induk_togo($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$kode = $this->input->get('id');
		$tgl_rab = $this->input->get('tgl_rab');

		switch ($info) {
			case 'cost_togo':
				$cek_data_induk_togo = $this->mdl_pengendalian->cek_data_induk_togo($kode,$proyek_id,$tgl_rab,$info);
				echo $cek_data_induk_togo;
			break;			
			default:
				echo "Access Forbidden";
			break;
		}
		
	}

	function insert_induk_komposisi_togo($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$id_material= $this->input->post('id_detail_material');
		$kode_material= $this->input->post('kode_detail_material');
		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');
		$kode = $this->input->post('id');
		$tgl_rab = $this->input->post('tgl_rab');
		$status = $this->input->post('status');

		$data_komposisi = array(
			'detail_material_id' => $id_material,
			'detail_material_kode' => $kode_material,
			'komposisi_volume_kendali' => 1,
			'komposisi_harga_satuan_kendali' => 0,
			'komposisi_koefisien_kendali' => 1,
			'komposisi_volume_total_kendali' => 1,
			'komposisi_total_kendali' => 0,
			'tahap_kode_kendali' => $kode,
			'proyek_id' => $proyek_id,
			'tahap_tanggal_kendali' => $tgl_rab,
			'user_update' => $user_update,
			'tgl_update' => $tgl_update,
			'ip_update' => $ip_update,
			'divisi_id' => $divisi_id,
			'waktu_update' => $waktu_update,
			'kode_komposisi_kendali' => 'X'
		);

		switch ($info) {
			case 'cost_togo':
				$cek_data_komposisi_togo = $this->mdl_pengendalian->cek_data_komposisi_togo($id_material,$kode,$proyek_id,$tgl_rab,$info);
				// echo $cek_data_komposisi_togo ;
				if ($cek_data_komposisi_togo == 'kosong') {
					$this->mdl_pengendalian->insert_induk_komposisi_togo($data_komposisi,$info);
				}
			break;
			case 'current_budget':
				$cek_data_komposisi_togo = $this->mdl_pengendalian->cek_data_komposisi_togo($id_material,$kode,$proyek_id,$tgl_rab,$info);
				// echo $cek_data_komposisi_togo ;
				if ($cek_data_komposisi_togo == 'kosong') {
					$this->mdl_pengendalian->insert_induk_komposisi_togo($data_komposisi,$info);
				}
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function insert_induk_togo_induk($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$kode = $this->input->post('id');
		$tgl_rab = $this->input->post('tgl_rab');
		$data_induk = array(
			'kode_komposisi' => 'X',
			'volume_komposisi' => 1,
			'koefisien_komposisi' => 1,
			'proyek_id' => $proyek_id,
			'tahap_kode_kendali' => $kode,
			'total_harga_satuan' => 1,
			'nama_komposisi' => 'ANALISA',
			'komposisi_satuan' => '',
			'harga_satuan' => 1,
			'tahap_tanggal_kendali' => $tgl_rab
		);

		switch ($info) {
			case 'cost_togo':
				$cek_data_induk_togo = $this->mdl_pengendalian->cek_data_induk_togo($kode,$proyek_id,$tgl_rab,$info);

				if ($cek_data_induk_togo == 'kosong') {
					$this->mdl_pengendalian->insert_induk_togo_induk($data_induk,$info);
				}
			break;
			case 'current_budget':
				$cek_data_induk_togo = $this->mdl_pengendalian->cek_data_induk_togo($kode,$proyek_id,$tgl_rab,$info);

				if ($cek_data_induk_togo == 'kosong') {
					$this->mdl_pengendalian->insert_induk_togo_induk($data_induk,$info);
				}
			break;
			default:
				echo "Access Forbidden";
			break;
		}		
	}

	function get_proyek_daftar_analisa()
	{
		$data = $this->mdl_pengendalian->get_proyek_daftar_analisa();
		echo '{"data":'.json_encode($data).'}';
	}

	function get_tanggal_daftar_analisa()
	{
		if ($this->input->get('proyek')) {
			$proyek_id=$this->input->get('proyek');
		} else {
			$proyek_id="";
		}
		$data = $this->mdl_pengendalian->get_tanggal_daftar_analisa($proyek_id);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_data_daftar_analisa()
	{
		$proyek_id = $this->input->get('proyek'); 
		$tgl_rab = $this->input->get('tgl_rab');
		$data = $this->mdl_pengendalian->get_data_daftar_analisa($proyek_id,$tgl_rab);
		echo $data;
	}

	function getdata_sub_ctg($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id');
		//$proyek_id = '1';  
		$tgl_rab = $this->input->get('tgl_rab');
		$kode_kendali = $this->input->get('kode_kendali');

		switch ($info) {
			case 'cost_togo':
				$data = $this->mdl_pengendalian->getdata_sub_ctg($proyek_id,$tgl_rab,$kode_kendali,$info);
				echo $data;
			break;
			case 'current_budget':
				$data = $this->mdl_pengendalian->getdata_sub_ctg($proyek_id,$tgl_rab,$kode_kendali,$info);
				echo $data;
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function update_analisa_ctg($info="")
	{
		$id = $this->input->post('id_komposisi');
		$data = array(
			'komposisi_harga_satuan_kendali' => $this->input->post('harga'),
			'komposisi_koefisien_kendali' => $this->input->post('koefisien')
		);
		switch ($info) {
			case 'cost_togo':
				$this->mdl_pengendalian->update_analisa_ctg($id,$data,$info);
			break;
			case 'current_budget':
				$this->mdl_pengendalian->update_analisa_ctg($id,$data,$info);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function getdata_edit_hs_ctg($info="")
	{
		//$proyek_id = '1';  
		$tgl_rab = $this->input->get('tgl_rab');

		switch ($info) {
			case 'cost_togo':
				$data = $this->mdl_pengendalian->getdata_edit_hs_ctg($proyek_id,$tgl_rab,$info);
				echo $data;
			break;	
			case 'current_budget':
				$data = $this->mdl_pengendalian->getdata_edit_hs_ctg($proyek_id,$tgl_rab,$info);
				echo $data;
			break;		
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function update_hs_ctg($info="")
	{
		//$proyek_id = '1';  
		$tgl_rab = $this->input->post('tgl_rab');
		$id = $this->input->post('kode');
		$data = array('komposisi_harga_satuan_kendali' => $this->input->post('harga'));

		switch ($info) {
			case 'cost_togo':
				$this->mdl_pengendalian->update_hs_ctg($proyek_id,$tgl_rab,$id,$data,$info);
			break;
			case 'current_budget':
				$this->mdl_pengendalian->update_hs_ctg($proyek_id,$tgl_rab,$id,$data,$info);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_kode_ctg($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab= $this->input->get('tgl_rab');

		switch ($info) {
			case 'cost_togo':
				$data = $this->mdl_pengendalian->get_kode_ctg($proyek_id,$tgl_rab,$info);
				echo '{"data":'.json_encode($data).'}';
			break;
			case 'current_budget':
				$data = $this->mdl_pengendalian->get_kode_ctg($proyek_id,$tgl_rab,$info);
				echo '{"data":'.json_encode($data).'}';
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function cek_pwd_hs()
	{
		$uname = $this->input->get('datasdm');
		$pwd = $this->input->get('datahs');

		// echo $uname.$pwd;
		$data = $this->mdl_pengendalian->cek_pwd_hs($uname,$pwd);
		echo $data;
	}

	function get_data_rk()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab = $this->input->get('tgl_rab');

		$data = $this->mdl_pengendalian->get_data_rk($proyek_id,$tgl_rab);
		echo $data;
	}

	function get_data_rencana_kontrak()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab = $this->input->get('tgl_rab');

		$data = $this->mdl_pengendalian->get_data_rencana_kontrak($proyek_id,$tgl_rab);
		echo $data;
	}

	function get_data_rpbk()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab = $this->input->get('tgl_rab');

		$data = $this->mdl_pengendalian->get_data_rpbk($proyek_id,$tgl_rab);
		echo $data;
	}

	function update_rpbk()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$id = $this->input->post('id');
		$tgl_rab = $this->input->post('tgl_rab');
		$harga = $this->input->post('harga');
		$detail_material = $this->input->post('detail_material');
		$detail_material_kode = $this->input->post('detail_material_kode');
		$kode_rap = $this->input->post('kode_rap');

		$data = array(
			'proyek_id' => $proyek_id,
			'volume_rencana_pbk' => $harga,
			'rpbk_rrk1' => $harga,
			'tahap_tanggal_kendali' => $tgl_rab,
			'detail_material_id' => $detail_material,
			'tahap_kode_kendali' => '0',
			'kode_komposisi_kendali' => 'X',
			'detail_material_kode' => $detail_material_kode,
			'kode_rap' => $kode_rap,
			'komposisi_harga_satuan_kendali' => $this->db->query("select harga from simpro_rap_analisa_asat where id_proyek = $proyek_id and kode_material = '$detail_material_kode'")->row()->harga
		);

		if ($id == null) {
			$this->mdl_pengendalian->rpbk('simpan',$data,'');
		} else {
			$this->mdl_pengendalian->rpbk('update',$data,$id);
		}
	}

	function get_uraian_mos()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$data = $this->mdl_pengendalian->get_uraian_mos($proyek_id);
		echo $data;
	}

	function mos_action($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($info) {
			case 'tambah':
				$data = array(
					'proyek_id' => $proyek_id,
					'mos_tgl'=> $this->input->post('tanggal'),
					'detail_material_id'=> $this->input->post('id_detail_material'),
					'detail_material_kode'=> $this->input->post('detail_material_kode'),
					'mos_total_volume'=> $this->input->post('volume_total'),
					'mos_diakui_volume'=> $this->input->post('volume_diakui'),
					'mos_belum_volume'=> $this->input->post('volume_total') - $this->input->post('volume_diakui'),
					'mos_keterangan'=> $this->input->post('keterangan_diakui'),
					'kode_rap'=> $this->input->post('uraian'),
					'mos_total_harsat'=> $this->input->post('harga_satuan')
				);
				$this->mdl_pengendalian->mos_action($info,$data,'');
			break;
			case 'edit':
				$id = $this->input->post('id');
				$data = array(
					'mos_total_volume'=> $this->input->post('volume_total'),
					'mos_diakui_volume'=> $this->input->post('volume_diakui'),
					'mos_belum_volume'=> $this->input->post('volume_total') - $this->input->post('volume_diakui'),
					'mos_keterangan'=> $this->input->post('keterangan_diakui')
				);
				$this->mdl_pengendalian->mos_action($info,$data,$id);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_data($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		if ($info == '') {
			$data = 'Access Forbidden';
		} else {
		if ($this->input->get('text') != '') {
			$text = $this->input->get('text');
		} else {
			$text = '';
		}
		if ($this->input->get('tglawal') != '') {
			$tgl_awal = $this->input->get('tglawal');
		} else {
			$tgl_awal = '';
		}
		if ($this->input->get('tglakhir') != '') {
			$tgl_akhir = $this->input->get('tglakhir');
		} else {
			$tgl_akhir = '';
		}

		if ($tgl_awal == '' && $tgl_akhir == '') {
			$var = "";			
		} else {	
			switch ($info) {
				case 'mos':
					$var = "and mos_tgl >= '$tgl_awal' and mos_tgl <= '$tgl_akhir' and lower(b.detail_material_nama) like lower('%$text%')";		
				break;
				case 'kkp':
					$var = "and kkp_tgl >= '$tgl_awal' and kkp_tgl <= '$tgl_akhir' and lower(a.kkp_uraian) like lower('%$text%')";		
				break;
			}
		}

		$data = $this->mdl_pengendalian->get_data($info,$proyek_id,$var);
		}
		echo $data;

	}

	function get_jabatan()
	{
		$data = $this->mdl_pengendalian->get_jabatan();
		echo $data;
	}

	function kkp_action($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
				
		$data = array(
			'proyek_id' => $proyek_id,
			'kkp_uraian' => $this->input->post('uraian'), 
			'kkp_tempat' => $this->input->post('sebab'),
			'kkp_rencana' => $this->input->post('rencana_penanggulangan'),
			'kkp_tgl' => $this->input->post('waktu'),
			'jabatan' => $this->input->post('jabatan')
		);

		switch ($info) {
			case 'tambah':
				$this->mdl_pengendalian->kkp_action($info,$data,'');
			break;
			case 'edit':
				$id = $this->input->post('id');
				$this->mdl_pengendalian->kkp_action($info,$data,$id);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function getkondisi()
	{
		$data = $this->mdl_pengendalian->getkondisi();
		echo $data;
	} 

	function getstatusoperasi()
	{
		$data = $this->mdl_pengendalian->getstatusoperasi();
		echo $data;
	} 

	function getstatuskepemilikan()
	{
		$data = $this->mdl_pengendalian->getstatuskepemilikan();
		echo $data;
	} 

	function action_daftar_alat($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$user_id = $this->session->userdata('uid');	
		$tgl_update=date('Y-m-d');

		$data = array(
			'proyek_id' => $proyek_id, 
			'tgl' => $tgl_update,
			'user_id' => $user_id,
			'keterangan' => $this->input->post('keterangan'),
			'master_peralatan_id' => $this->input->post('uraian_jenis_alat'),
			'status_kepemilikan' => $this->input->post('milik'),
			'kondisi' => $this->input->post('kondisi'),
			'status_operasi' => $this->input->post('operasi')
		);

		// var_dump($data);
		switch ($info) {
			case 'simpan':
				$this->mdl_pengendalian->action_daftar_alat($info,$data,'');
			break;
			case 'edit':
				$id = $this->input->post('id');
				$this->mdl_pengendalian->action_daftar_alat($info,$data,$id);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function update_data($info="")
	{
		$id = $this->input->post('id');
		switch ($info) {
			case 'rencana_kerja':
				$id = $this->input->post('id');
				$data = array(
					'tahap_volume_bln1' => $this->input->post('data1'),
					'tahap_volume_bln2' => $this->input->post('data2'),
					'tahap_volume_bln3' => $this->input->post('data3'),
					'tahap_volume_bln4' => $this->input->post('data4') 
				);
				$this->mdl_pengendalian->update_data($info,$data,$id);
			break;
			case 'rencana_kontrak_kini':
				$data = array(
					'volume_rencana' => $this->input->post('data1'),
					'volume_rencana1' => $this->input->post('data2'),
					'rencana_volume_eskalasi' => $this->input->post('data3'),
					'harga_satuan_eskalasi' => $this->input->post('data4') 
				);
				$this->mdl_pengendalian->update_data($info,$data,$id);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function approve($tgl="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$username = $this->input->post('username'); 
		$password = $this->input->post('password');
		$uid = $this->session->userdata('uid');

		$data = $this->mdl_pengendalian->approve($proyek_id,$username,$password,$tgl,$uid);

		echo $data;
	}

	function delete_data($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($info) {
			case 'all_kontrak_terkini':
				$tgl_rab = $this->input->post('tgl_rab');
				$data = array(
					'proyek_id' => $proyek_id,
					'tgl_rab' => $tgl_rab
				);
				
				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'all_kontrak_terkini_new':
				$tgl_rab = $this->input->post('tgl_rab');
				$data = array(
					'proyek_id' => $proyek_id,
					'tgl_rab' => $tgl_rab
				);
				$this->mdl_pengendalian->delete_data($info,$data);				
			break;
			case 'cost_togo':
				$tgl_rab = $this->input->post('tgl_rab');
				$kode = $this->input->post('kode');
				$data = array(
					'proyek_id' => $proyek_id,
					'tgl_rab' => $tgl_rab,
					'kode' => $kode
				);
				$this->mdl_pengendalian->delete_data($info,$data);
			break;	
			case 'current_budget':
				$tgl_rab = $this->input->post('tgl_rab');
				$kode = $this->input->post('kode');
				$data = array(
					'proyek_id' => $proyek_id,
					'tgl_rab' => $tgl_rab,
					'kode' => $kode
				);
				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'analisa_cost_togo':
				$tgl_rab = $this->input->post('tgl_rab');
				$kode = $this->input->post('id');
				$data = array(
					'proyek_id' => $proyek_id,
					'tgl_rab' => $tgl_rab,
					'kode' => $kode
				);
				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'analisa_current_budget':
				$tgl_rab = $this->input->post('tgl_rab');
				$kode = $this->input->post('id');
				$data = array(
					'proyek_id' => $proyek_id,
					'tgl_rab' => $tgl_rab,
					'kode' => $kode
				);
				$this->mdl_pengendalian->delete_data($info,$data);
			break;	
			case 'kkp':
				$kode = $this->input->post('id');
				$data = array(
					'proyek_id' => $proyek_id,
					'kode' => $kode
				);
				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'mos':
				$kode = $this->input->post('id');
				$data = array(
					'proyek_id' => $proyek_id,
					'kode' => $kode
				);
				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'daftar_peralatan':
				$kode = $this->input->post('id');
				$data = array(
					'proyek_id' => $proyek_id,
					'kode' => $kode
				);
				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'currentbudgetall':				
				$tgl_rab = $this->input->post('tgl_rab');
				$data = array(
					'proyek_id' => $proyek_id,
					'tgl_rab' => $tgl_rab
				);
				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'item_kontrak_terkini':
				$id = $this->input->post('id');
				$kode = $this->input->post('kode');
				$tgl_rab = $this->input->post('tgl_rab');

				$data = array(
					'proyek_id' => $proyek_id,
					'id' => $id, 
					'kode' => $kode, 
					'tgl_rab' => $tgl_rab 
				);

				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'item_rencana_kontrak_terkini':
				$id = $this->input->post('id');
				$kode = $this->input->post('kode');
				$tgl_rab = $this->input->post('tgl_rab');

				$data = array(
					'proyek_id' => $proyek_id,
					'id' => $id, 
					'kode' => $kode, 
					'tgl_rab' => $tgl_rab 
				);

				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			case 'analisa_ctg_all':
				$kode = $this->input->post('kode');
				$tgl_rab = $this->input->post('tgl_rab');

				$data = array(
					'proyek_id' => $proyek_id,
					'kode' => $kode, 
					'tgl_rab' => $tgl_rab 
				);
				
				$this->mdl_pengendalian->delete_data($info,$data);
			break;
			default:
				echo "Access Forbidden";
			break;
		}


	}

	function get_status_approve()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_status_approve($proyek_id);
		echo $data;
	}

	function get_status_approve_cb()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_status_approve_cb($proyek_id);
		echo $data;
	}

	function copy_data($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		
		switch ($info) {
			case 'rab_to_kontrak_kini':
				$tgl_rab = $this->input->post('tgl_rab');
				$this->mdl_pengendalian->copy_data($info,$tgl_rab,$proyek_id);
			break;			
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function copy($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		
		switch ($info) {
			case 'rab_to_kontrak_kini':
				$tgl_rab = $this->input->post('tgl_rab');
				$this->mdl_pengendalian->copy($info,$tgl_rab,$proyek_id);
			break;
			case 'analisa_to_an_ctg':
				$tgl_rab = array(
					'copy_proyek' => $this->input->post('proyek'),
					'copy_tgl_rab' => $this->input->post('tanggal'),
					'copy_kode' => $this->input->post('analisa'),
					'tgl_rab' => $this->input->post('tgl_rab'),
					'kode' => $this->input->post('kode')
				);
				// var_dump($tgl_rab);
				$this->mdl_pengendalian->copy($info,$tgl_rab,$proyek_id);
			break;		
			case 'analisa_to_an_cb':
				$tgl_rab = array(
					'copy_proyek' => $this->input->post('proyek'),
					'copy_tgl_rab' => $this->input->post('tanggal'),
					'copy_kode' => $this->input->post('analisa'),
					'tgl_rab' => $this->input->post('tgl_rab'),
					'kode' => $this->input->post('kode')
				);
				// var_dump($tgl_rab);
				$this->mdl_pengendalian->copy($info,$tgl_rab,$proyek_id);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_value($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($info) {
			case 'approve_terakhir':
				$data = $this->mdl_pengendalian->get_value($info,$proyek_id);
			break;			
			default:
				echo "Access Forbidden";
			break;
		}

		echo $data;
	}

	function add_data($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($info) {
			case 'kontrak_terkini':
				$info = $this->input->post('status_tambah_kontrak');
				$this->mdl_pengendalian->copy($info,'',$proyek_id);
			break;		
			case 'currentbudget':
				$tgl_rab = array(
					'tgl_awal' => $this->input->post('tgl_rab'), 
					'tgl_akhir' => $this->input->post('tgl_akhir')
				);
				var_dump($tgl_rab);
				$this->mdl_pengendalian->copy($info,$tgl_rab,$proyek_id);
			break;	
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_last_tgl_kontrak_kini()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_last_tgl_kontrak_kini($proyek_id);
		echo $data;
	}

	function get_status_tgl_cb()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$data = $this->mdl_pengendalian->get_status_tgl_cb($proyek_id);
		echo $data;
	}

	// function get_task_tree_item($idpro,$tgl_rab)
	// {
	// 	$param = $this->input->get('param');
	// 	$arr = $this->tree($idpro, $depth=0,$tgl_rab,$param);
	// 	echo json_encode(array('text'=>'.', 'children'=>$arr));
	// }
	// $proyek_id, $depth=0,$tgl_rab,$page
	function tree($idpro, $depth='isnull',$tgl_rab,$page)
	{
		switch ($page) {
			case 'kk':
				$id_info = 'id_kontrak_terkini';
				$where_tgl_info = 'tgl_akhir';
				$tbl_info = 'simpro_tbl_kontrak_terkini';
			break;
			case 'rkk':
				$id_info = 'id_rencana_kontrak_terkini';
				$where_tgl_info = 'tahap_tanggal_kendali';
				$tbl_info = 'simpro_tbl_rencana_kontrak_terkini';
			break;
		}

		$result=array();
		$temp=array();
		$temp = $this->mdl_pengendalian->get_tree_item($idpro, $depth, $tgl_rab,$page)->result();
		if(count($temp))
		{			
			$i = 0;
			foreach($temp as $row){
		
				//$temp_harga = $this->mdl_ctg_analisa->get_tree_item_harga($idpro, $row->costogo_item_tree)->row_array();
				if ($page == 'kk' || $page == 'rkk') {
					$data[] = array(
						$id_info => $row->id_kontrak_terkini,
						'rab_tahap_kode_kendali' => $row->rab_tahap_kode_kendali,
						'rab_tahap_nama_kendali' => $row->rab_tahap_nama_kendali,
						'rab_tahap_satuan_kendali' => $row->rab_tahap_satuan_kendali,         
						'rab_tahap_volume_kendali' => $row->rab_tahap_volume_kendali,
						'rab_tahap_harga_satuan_kendali' => $row->rab_tahap_harga_satuan_kendali,
						'jml_rab' => $row->jml_rab,
						'tahap_kode_kendali' => $row->tahap_kode_kendali,          
						'tahap_nama_kendali' => $row->tahap_nama_kendali,
						'tahap_satuan_kendali' => $row->tahap_satuan_kendali,
						'tahap_volume_kendali' => $row->tahap_volume_kendali,
						'tahap_harga_satuan_kendali' => $row->tahap_harga_satuan_kendali,
						'jml_kontrak_kini' => $row->jml_kontrak_kini,         
						'tahap_volume_kendali_new' => $row->tahap_volume_kendali_new,
						'jml_tambah' => $row->jml_tambah,
						'tahap_volume_kendali_kurang' => $row->tahap_volume_kendali_kurang,
						'jml_kurang' => $row->jml_kurang,  
						'volume_eskalasi' => $row->volume_eskalasi,
						'harga_satuan_eskalasi' => $row->harga_satuan_eskalasi,
						'jml_eskalasi' => $row->jml_eskalasi,
						'vol_total' => $row->vol_total,
						'jml_total' => $row->jml_total
						/* 
						'harga' => $temp_harga['harga'], 
						'subtotal' => $temp_harga['subtotal'],
						*/
					);
				} elseif ($page == 'lpf') {
					$data[] = array(
						'id_tahap_pekerjaan' => $row->id_tahap_pekerjaan,
						'tahap_kode_kendali' => $row->tahap_kode_kendali,
						'tahap_nama_kendali' => $row->tahap_nama_kendali,
						'tahap_satuan_kendali' => $row->tahap_satuan_kendali,
						'vol_kk' => $row->vol_kk,
						'tahap_harga_satuan_kendali' => $row->tahap_harga_satuan_kendali,
						'jml_lpf_kini' => $row->jml_lpf_kini,
						'jlm_sd_bln_lalu' => $row->jlm_sd_bln_lalu,
						'tahap_diakui_bobot' => $row->tahap_diakui_bobot,
						'jlm_sd_bln_ini' => $row->jlm_sd_bln_ini,
						'vol_total_tagihan' => $row->vol_total_tagihan,
						'jml_tagihan' => $row->jml_tagihan,
						'vol_bruto' => $row->vol_bruto,
						'jml_bruto' => $row->jml_bruto,
						'tagihan_cair' => $row->tagihan_cair,
						'jml_cair' => $row->jml_cair,
						'vol_sisa_pekerjaan' => $row->vol_sisa_pekerjaan,
						'jml_sisa_pekerjaan' => $row->jml_sisa_pekerjaan,
						'tagihan_rencana_piutang' => $row->tagihan_rencana_piutang
						/* 
						'harga' => $temp_harga['harga'], 
						'subtotal' => $temp_harga['subtotal'],
						*/
					);
				} elseif ($page == 'rencana_kerja') {
					$data[] = array(
						'total_rkp_id' => $row->total_rkp_id,
						'tahap_kode_kendali' => $row->tahap_kode_kendali,
						'tahap_nama_kendali' => $row->tahap_nama_kendali,
						'tahap_satuan_kendali' => $row->tahap_satuan_kendali,
						'vol_kk' => $row->vol_kk,
						'tahap_harga_satuan_kendali' => $row->tahap_harga_satuan_kendali,
						'jml_rkp_kini' => $row->jml_rkp_kini,
						'vol_sd_bln_ini' => $row->vol_sd_bln_ini,
						'jml_sd_bln_ini' => $row->jml_sd_bln_ini,
						'tahap_volume_bln1' => $row->tahap_volume_bln1,
						'jml_bln1' => $row->jml_bln1,
						'tahap_volume_bln2' => $row->tahap_volume_bln2,
						'jml_bln2' => $row->jml_bln2,
						'tahap_volume_bln3' => $row->tahap_volume_bln3,
						'jml_bln3' => $row->jml_bln3,
						'tahap_volume_bln4' => $row->tahap_volume_bln4,
						'jml_bln4' => $row->jml_bln4,
						'deviasi' => $row->deviasi
						/* 
						'harga' => $temp_harga['harga'], 
						'subtotal' => $temp_harga['subtotal'],
						*/
					);
				}
				

				if($depth == 0) $data[$i] = array_merge($data[$i], array('expanded' => true));
				
				## check if have a child
				if ($page == 'kk' || $page == 'rkk') {
					$q = sprintf("SELECT * FROM $tbl_info WHERE proyek_id = '%d' AND trim(tahap_kode_induk_kendali) = '$row->tahap_kode_kendali' and $where_tgl_info = '$tgl_rab'",$idpro,$row->tahap_kode_kendali);
				} elseif ($page == 'lpf') {
					$q = sprintf("SELECT * FROM simpro_tbl_total_pekerjaan a
						join simpro_tbl_kontrak_terkini b
						on b.id_kontrak_terkini = a.kontrak_terkini_id
						WHERE a.proyek_id = '%d' 
						AND trim(b.tahap_kode_induk_kendali) = '$row->tahap_kode_kendali' 
						and a.tahap_tanggal_kendali = '$tgl_rab'",$idpro,$row->tahap_kode_kendali);
				} elseif ($page == 'rencana_kerja') {
					$q = sprintf("SELECT * FROM simpro_tbl_total_rkp a
						join simpro_tbl_kontrak_terkini b
						on b.id_kontrak_terkini = a.kontrak_terkini_id
						WHERE a.proyek_id = '%d' 
						AND trim(b.tahap_kode_induk_kendali) = '$row->tahap_kode_kendali' 
						and a.tahap_tanggal_kendali = '$tgl_rab'",$idpro,$row->tahap_kode_kendali);
				}

				
				$query = $this->db->query($q);
				$is_child = $query->num_rows();

				if($is_child)
				{				
					// $result[] = $data;
					// var_dump($row->costogo_item_tree);
					$result[] = array_merge(
						$data[$i],
						array(
							'iconCls' => 'task-folder',
							'ishaschild' => 1,
							'children'=> $this->tree($idpro,$row->tahap_kode_kendali,$tgl_rab,$page)
						)
					);
					// $this->tree($idpro,$row->tahap_kode_kendali,$tgl_rab,$page);
				} 
				else 
				{
					// if ($row->ktr != 0){
						$result[] = array_merge(
							$data[$i],
							array(
								'iconCls' => 'task-folder',
								'leaf' => true
							)
						);
					// }
				}
				// $result[] = $data;
				$i++;
			}

			// var_dump($result);
		}
		return array_filter($result);
	}

	function get_data_kk($page="")
	{
		// header( 'Content-Type: application/json' );
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		switch ($page) {
			case 'kk':
				$tgl_rab=$this->input->get('tgl_rab');
				$data = $this->tree($proyek_id, $depth=0,$tgl_rab,$page);
				echo json_encode(array("text"=>".","children"=>$data));
				// $data = $this->mdl_pengendalian->get_data_kk($page,$proyek_id,$tgl_rab);
				// echo $data;
			break;
			case 'rkk':
				$tgl_rab=$this->input->get('tgl_rab');
				$data = $this->tree($proyek_id, $depth=0,$tgl_rab,$page);
				echo json_encode(array("text"=>".","children"=>$data));
				// $data = $this->mdl_pengendalian->get_data_kk($page,$proyek_id,$tgl_rab);
				// echo $data;
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function reset($info)
	{
		switch ($info) {
			case 'item_kontrak_terkini':
				$id = $this->input->post('id');
				$this->mdl_pengendalian->reset($info,$id);
			break;
			case 'item_rencana_kontrak_terkini':
				$id = $this->input->post('id');
				$this->mdl_pengendalian->reset($info,$id);
			break;			
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_data_total_pekerjaan()
	{
		header( 'Content-Type: application/json' );
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab = $this->input->get('tgl_rab');


		$page = 'lpf';
		$data = $this->tree($proyek_id, $depth=0,$tgl_rab,$page);
		echo json_encode(array("text"=>".","children"=>$data));
		
		// $data = $this->mdl_pengendalian->get_data_total_pekerjaan($proyek_id,$tgl_rab);
		// echo $data;
	}

	function get_data_rkp()
	{
		header( 'Content-Type: application/json' );
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab = $this->input->get('tgl_rab');

		$page = 'rencana_kerja';
		$data = $this->tree($proyek_id, $depth=0,$tgl_rab,$page);
		echo json_encode(array("text"=>".","children"=>$data));
	
		// $data = $this->mdl_pengendalian->get_data_rkp($proyek_id,$tgl_rab);
		// echo $data;
	}

	function get_data_cashflow()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$tgl_rab = $this->input->get('tgl_rab');

		$data = $this->mdl_pengendalian->get_data_cashflow($proyek_id,$tgl_rab);
		echo $data;
	}

	function get_data_cut_off($tgl_rab='')
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$data = $this->mdl_pengendalian->get_data_cut_off($proyek_id,$tgl_rab);
		echo $data;
	}

	function get_combo($info="",$page="")
	{
		switch ($info) {
			case 'copy_divisi':
				$data = '';
				$dat = $this->mdl_pengendalian->get_combo($info,$page,$data);
				echo $dat;
			break;
			case 'copy_proyek':
				$kode = $this->input->post('kode');
				switch ($kode) {
					case '':	
						$kode  = 0;
					break;
				}
				$data = array(
					'kode' => $kode
				);
				$dat = $this->mdl_pengendalian->get_combo($info,$page,$data);
				echo $dat;
			break;
			case 'copy_tgl':
				$kode = $this->input->post('kode');
				switch ($kode) {
					case '':	
						$kode  = 0;
					break;
				}
				$data = array(
					'kode' => $kode
				);
				$dat = $this->mdl_pengendalian->get_combo($info,$page,$data);
				echo $dat;
			break;
			case 'copy_analisa':
				$proyek = $this->input->post('proyek');
				switch ($proyek) {
					case '':	
						$proyek  = 0;
					break;
				}
				$tgl = $this->input->post('tgl');
				switch ($tgl) {
					case '':	
						$tgl = '0001-01-01';
					break;
				}
				$data = array(
					'proyek' => $proyek,
					'tgl' => $tgl
				);
				$dat = $this->mdl_pengendalian->get_combo($info,$page,$data);
				echo $dat;
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_isi_uraian_mos()
	{
		$id = $this->input->post('id');

		$data = $this->mdl_pengendalian->get_isi_uraian_mos($id);
		echo $data;
	}

	function insert_cashflow()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');

		$ket_id=$this->input->post('ket_id');
		$currentbudget=$this->input->post('currentbudget');
		$realisasi=$this->input->post('realisasi');
        $proyeksi1=$this->input->post('proyeksi1');
        $proyeksi2=$this->input->post('proyeksi2');
        $proyeksi3=$this->input->post('proyeksi3');
        $proyeksi4=$this->input->post('proyeksi4');
        $proyeksi5=$this->input->post('proyeksi5');
        $spp=$this->input->post('spp');
        $sbp=$this->input->post('sbp');
        $tgl_rab=$this->input->post('tgl_rab');

        $data = array(
        	'ket_id'=>$ket_id,
        	'ip_update'=>$ip_update,
			'divisi_id'=>$divisi_id,
			'user_id'=>$user_update,
			'waktu_update'=>$waktu_update,		
			'tgl_update'=>$tgl_update,
			'realisasi'=>$realisasi,
	        'rproyeksi1'=>$proyeksi1,
	        'rproyeksi2'=>$proyeksi2,
	        'rproyeksi3'=>$proyeksi3,
	        'rproyeksi4'=>$proyeksi4,
	        'rproyeksi5'=>$proyeksi5,
	        'curentbuget'=>$currentbudget,
	        'spp'=>$spp,
	        'sbp'=>$sbp,
	        'tahap_tanggal_kendali'=>$tgl_rab,
	        'proyek_id'=>$proyek_id
        );

        var_dump($data);
        // echo json_encode($data);
        $this->mdl_pengendalian->insert_cashflow($data);
	}

	//add by dena

	function getsch($info="")
	{
		//$proyek_id = '1';

		$proyek_id = $this->session->userdata('proyek_id');

		switch ($info) {
			case 'proyek':
				$q = $this->db->query("select *	from simpro_rap_item_tree where id_proyek = '$proyek_id' and left(kode_tree,2) = '1.' order by kode_tree asc");     	
		
				if ($q->result()) {

					foreach($q->result() as $row) {
						
						$satuan_id = $row->id_satuan;
						$tahap_kendali_id = $row->rap_item_tree;

						// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_id = '$satuan_id'");     	
						// $satuan = $sql->row();

						$sql = $this->db->query("select * from simpro_tbl_sch_proyek where proyek_id = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id'");     	
						$sch_proyek = $sql->row();

						$cek_anak = $this->db->query("select * from simpro_rap_item_tree where id_proyek = '$proyek_id' and left(kode_tree,".strlen($row->kode_tree).") = '$row->kode_tree'");

						if ($cek_anak->num_rows() > 1) {
							$ceks = 1;
						} else {
							$ceks = 0;
						}
						// var_dump(expression);
						$data['anak'] = $ceks;
						$data['tahap_kendali_id'] = $tahap_kendali_id;
						$data['proyek_id'] 	= $row->id_proyek;
						$data['id'] 		= $row->kode_tree;
						$data['uraian'] 	= $row->tree_item;
						$data['unit_id'] 	= $satuan_id;
						$data['unit'] 		= $row->tree_satuan;
						$data['tgl_awal'] 	= isset($sch_proyek->tgl_awal) ? $sch_proyek->tgl_awal : '';
						$data['tgl_akhir'] 	= isset($sch_proyek->tgl_akhir) ? $sch_proyek->tgl_akhir : '';
						$data['bobot'] 	= isset($sch_proyek->bobot) ? $sch_proyek->bobot : '';

						$dat[] = $data;
					}

				} 
				else {
					$dat = '';
				}

				echo '{"data":'.json_encode($dat).'}';
			break;
			case 'alat':
				$q = $this->db->query("select 
				a.id_analisa_asat,
				a.id_proyek as proyek_id,
				a.kode_material  as m_kode,
				b.detail_material_nama as m_nama,
				d.satuan_id,
				b.detail_material_satuan as m_satuan,
				c.tgl_awal,
				c.tgl_akhir,
				c.bobot
				from simpro_rap_analisa_asat a
				join simpro_tbl_detail_material b
				on a.kode_material = b.detail_material_kode
				left join simpro_tbl_sch_proyek_alat c
				on c.tahap_kendali_id = a.id_analisa_asat  and c.proyek_id = a.id_proyek
				join simpro_tbl_satuan d
				on lower(b.detail_material_satuan) = lower(d.satuan_nama)
				where a.id_proyek = $proyek_id and left(b.detail_material_kode,3)='502'
				order by detail_material_kode");     	
		
				if ($q->result()) {

					foreach($q->result() as $row) {
						
						// $satuan_id = $row->m_satuan;
						// $tahap_kendali_id = $row->komposisi_kendali_id;

						// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_nama = '$satuan_id'");     	
						// $satuan = $sql->row();

						// $sql = $this->db->query("select * from simpro_tbl_sch_proyek_alat where proyek_id = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id'");     	
						// $sch_proyek = $sql->row();

						$data['tahap_kendali_id'] = $row->id_analisa_asat;
						$data['proyek_id'] 	= $row->proyek_id;
						$data['id'] 		= $row->m_kode;
						$data['uraian'] 	= $row->m_nama;
						$data['unit_id'] 	= $row->satuan_id; 
						$data['unit'] 		= $row->m_satuan;
						$data['tgl_awal'] 	= isset($row->tgl_awal) ? $row->tgl_awal : '';
						$data['tgl_akhir'] 	= isset($row->tgl_akhir) ? $row->tgl_akhir : '';
						$data['bobot'] 	= isset($row->bobot) ? $row->bobot : '';

						$dat[] = $data;
					}

				} 
				else {
					$dat = '';
				}

				echo '{"data":'.json_encode($dat).'}';
			break;
			case 'bahan':
				$q = $this->db->query("select 
				a.id_analisa_asat,
				a.id_proyek as proyek_id,
				a.kode_material  as m_kode,
				b.detail_material_nama as m_nama,
				d.satuan_id,
				b.detail_material_satuan as m_satuan,
				c.tgl_awal,
				c.tgl_akhir,
				c.bobot
				from simpro_rap_analisa_asat a
				join simpro_tbl_detail_material b
				on a.kode_material = b.detail_material_kode
				left join simpro_tbl_sch_proyek_bahan c
				on c.tahap_kendali_id = a.id_analisa_asat  and c.proyek_id = a.id_proyek
				join simpro_tbl_satuan d
				on lower(b.detail_material_satuan) = lower(d.satuan_nama)
				where a.id_proyek = $proyek_id and left(b.detail_material_kode,3)='500'
				order by detail_material_kode");     	
		
				if ($q->result()) {

					foreach($q->result() as $row) {
						
						// $satuan_id = $row->m_satuan;
						// $tahap_kendali_id = $row->komposisi_kendali_id;

						// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_nama = '$satuan_id'");     	
						// $satuan = $sql->row();

						// $sql = $this->db->query("select * from simpro_tbl_sch_proyek_alat where proyek_id = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id'");     	
						// $sch_proyek = $sql->row();

						$data['tahap_kendali_id'] = $row->id_analisa_asat;
						$data['proyek_id'] 	= $row->proyek_id;
						$data['id'] 		= $row->m_kode;
						$data['uraian'] 	= $row->m_nama;
						$data['unit_id'] 	= $row->satuan_id; 
						$data['unit'] 		= $row->m_satuan;
						$data['tgl_awal'] 	= isset($row->tgl_awal) ? $row->tgl_awal : '';
						$data['tgl_akhir'] 	= isset($row->tgl_akhir) ? $row->tgl_akhir : '';
						$data['bobot'] 	= isset($row->bobot) ? $row->bobot : '';

						$dat[] = $data;
					}

				} 
				else {
					$dat = '';
				}

				echo '{"data":'.json_encode($dat).'}';
			break;
			case 'person':
				$q = $this->db->query("select 
				a.id_analisa_asat,
				a.id_proyek as proyek_id,
				a.kode_material  as m_kode,
				b.detail_material_nama as m_nama,
				d.satuan_id,
				b.detail_material_satuan as m_satuan,
				c.tgl_awal,
				c.tgl_akhir,
				c.bobot
				from simpro_rap_analisa_asat a
				join simpro_tbl_detail_material b
				on a.kode_material = b.detail_material_kode
				left join simpro_tbl_sch_proyek_person c
				on c.tahap_kendali_id = a.id_analisa_asat and c.proyek_id = a.id_proyek
				join simpro_tbl_satuan d
				on lower(b.detail_material_satuan) = lower(d.satuan_nama)
				where a.id_proyek = $proyek_id and left(b.detail_material_kode,3)='501'
				order by detail_material_kode");     	
		
				if ($q->result()) {

					foreach($q->result() as $row) {
						
						// $satuan_id = $row->m_satuan;
						// $tahap_kendali_id = $row->komposisi_kendali_id;

						// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_nama = '$satuan_id'");     	
						// $satuan = $sql->row();

						// $sql = $this->db->query("select * from simpro_tbl_sch_proyek_alat where proyek_id = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id'");     	
						// $sch_proyek = $sql->row();

						$data['tahap_kendali_id'] = $row->id_analisa_asat;
						$data['proyek_id'] 	= $row->proyek_id;
						$data['id'] 		= $row->m_kode;
						$data['uraian'] 	= $row->m_nama;
						$data['unit_id'] 	= $row->satuan_id; 
						$data['unit'] 		= $row->m_satuan;
						$data['tgl_awal'] 	= isset($row->tgl_awal) ? $row->tgl_awal : '';
						$data['tgl_akhir'] 	= isset($row->tgl_akhir) ? $row->tgl_akhir : '';
						$data['bobot'] 	= isset($row->bobot) ? $row->bobot : '';

						$dat[] = $data;
					}

				} 
				else {
					$dat = '';
				}

				echo '{"data":'.json_encode($dat).'}';
			break;
			case 'peralatan':
				$q = $this->db->query("select *
									  from
									  simpro_rap_analisa_asat as a LEFT JOIN simpro_tbl_detail_material as b
									  ON a.kode_material=b.detail_material_kode
									  where
									  a.id_proyek = '$proyek_id' and
									  a.kode_material LIKE '502%'");     	
		
				if ($q->result()) {

					foreach($q->result() as $row) {
						
						$id_analisa_asat = $row->id_analisa_asat;

						// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_id = '$satuan_id'");     	
						// $satuan = $sql->row();

						$sql = $this->db->query("select * from simpro_tbl_guna_alat where proyek_id = '$proyek_id' and id_analisa_asat='$id_analisa_asat'");     	
						$sch_proyek = $sql->row();

						$data['id_analisa_asat'] = $id_analisa_asat;
						$data['proyek_id'] 	= $row->id_proyek;
						$data['id'] 		= $row->kode_material;
						$data['alat'] 	= $row->detail_material_nama;
						$data['unit'] 		= $row->detail_material_satuan;
						$data['tgl_awal'] 	= isset($sch_proyek->tgl_awal) ? $sch_proyek->tgl_awal : '';
						$data['tgl_akhir'] 	= isset($sch_proyek->tgl_akhir) ? $sch_proyek->tgl_akhir : '';
						$data['jumlah'] 		= isset($sch_proyek->jumlah) ? $sch_proyek->jumlah : '';

						$dat[] = $data;
					}

				} 
				else {
					$dat = '';
				}

				echo '{"data":'.json_encode($dat).'}';
			break;
		}

	}

	function getschdetail($info="")
	{
		switch ($info) {
			case 'proyek':
				$proyek_id = $this->input->get('proyek_id');
				$tahap_kendali_id = $this->input->get('tahap_kendali_id');

				$q = $this->db->query("SELECT id, id_sch_proyek, tgl_sch_parent, bobot_parent FROM simpro_tbl_sch_proyek_parent where tahap_kendali_id='$tahap_kendali_id' and id_sch_proyek = '$proyek_id' order by minggu_ke asc");     
				echo '{"data":'.json_encode($q->result_object()).'}';
			break;
			case 'alat':
				$proyek_id = $this->input->get('proyek_id');
				$tahap_kendali_id = $this->input->get('tahap_kendali_id');

				$q = $this->db->query("SELECT id, id_sch_proyek, tgl_sch_parent, bobot_parent FROM simpro_tbl_sch_proyek_parent_alat where tahap_kendali_id='$tahap_kendali_id' and id_sch_proyek = '$proyek_id' order by minggu_ke asc");     
				echo '{"data":'.json_encode($q->result_object()).'}';
			break;
			case 'bahan':
				$proyek_id = $this->input->get('proyek_id');
				$tahap_kendali_id = $this->input->get('tahap_kendali_id');

				$q = $this->db->query("SELECT id, id_sch_proyek, tgl_sch_parent, bobot_parent FROM simpro_tbl_sch_proyek_parent_bahan where tahap_kendali_id='$tahap_kendali_id' and id_sch_proyek = '$proyek_id' order by minggu_ke asc");     
				echo '{"data":'.json_encode($q->result_object()).'}';
			break;
			case 'person':
				$proyek_id = $this->input->get('proyek_id');
				$tahap_kendali_id = $this->input->get('tahap_kendali_id');

				$q = $this->db->query("SELECT id, id_sch_proyek, tgl_sch_parent, bobot_parent FROM simpro_tbl_sch_proyek_parent_person where tahap_kendali_id='$tahap_kendali_id' and id_sch_proyek = '$proyek_id' order by minggu_ke asc");     
				echo '{"data":'.json_encode($q->result_object()).'}';
			break;	
			case 'peralatan':
				$proyek_id = $this->input->get('proyek_id');
				$id_analisa_asat = $this->input->get('id_analisa_asat');

				$q = $this->db->query("SELECT id, id_guna_alat, tgl_sch_parent, jumlah_parent FROM simpro_tbl_guna_alat_parent where id_analisa_asat='$id_analisa_asat' and id_guna_alat = '$proyek_id' order by minggu_ke asc");     
				echo '{"data":'.json_encode($q->result_object()).'}';
			break;		
			default:
				echo "Access Forbidden";
			break;
		}

	}

	function insertschproyek($info="")
	{
		switch ($info) {
			case 'proyek':
				$proyek_id = $this->input->post('proyek_id');
				$tahap_kendali_id = $this->input->post('tahap_kendali_id');
				$uraian 	= $this->input->post('uraian');
				$unit 	    = $this->input->post('unit_id');
				$tgl_awal 	= $this->input->post('tgl_awal');
				$tgl_akhir 	= $this->input->post('tgl_akhir');
				$bobot 	    = $this->input->post('bobot');	

				$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = '$proyek_id' ");
				$proyek = $sql->row_array();

				$tanggal_awal_project 	= $proyek['mulai'];
				$tanggal_akhir_project 	= $proyek['berakhir'];

				$data = array(
					'proyek_id' => $proyek_id,
					'tahap_kendali_id' => $tahap_kendali_id,
					'uraian'   => $uraian,
					'unit'     => $unit,
					'tgl_awal' => $tgl_awal,
					'tgl_akhir' => $tgl_akhir,
					'bobot'    => $bobot			
				 );

				$sql = $this->db->query("select * from simpro_tbl_sch_proyek where proyek_id = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id'");     	
				$rows = $sql->num_rows();

				if ($rows == 0)

					$this->mdl_pengendalian->add_sch_project($info,$data);

				else

					$this->mdl_pengendalian->update_sch_project($info,$proyek_id, $tahap_kendali_id, $data);


				// insert detail bobot
				$awal_minggu  = $this->weeks_between($tanggal_awal_project, $tgl_awal);

				if ($awal_minggu == 0)
					$awal_minggu = 1;

				$total_minggu = $this->weeks_between($tgl_awal, $tgl_akhir);

				$total_minggu = $total_minggu + $awal_minggu;

				//var_dump($awal_minggu);

				// var_dump($total_minggu);
				// exit();

				$minggu_ke = $awal_minggu;
				$margin_minggu = '';

				$tgl_sch_parent = $tgl_awal;

				$no = 1;

				for ($i=$awal_minggu; $i < $total_minggu; $i++) {

					$sql = $this->db->query("select * from simpro_tbl_sch_proyek_parent where id_sch_proyek = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id' and minggu_ke='$minggu_ke'");     	
					$rows = $sql->num_rows();

					if ($rows == 0){
						$data_sch_proyek_detail = array(
							'id_sch_proyek' => $proyek_id,
							'tgl_sch_parent' => $tgl_sch_parent,
							'minggu_ke' => $minggu_ke,
							'tahap_kendali_id' => $tahap_kendali_id
							);

						$this->mdl_pengendalian->insertdetailsch($info,$data_sch_proyek_detail);
						
					}
					else{
						$data_sch_proyek_detail = array(
							'tgl_sch_parent' => $tgl_sch_parent
							);

						$this->mdl_pengendalian->updatedetailsch($info,$data_sch_proyek_detail, $proyek_id, $tahap_kendali_id, $minggu_ke);
					}

					$margin_minggu .= $minggu_ke.',';
					$minggu_ke = $minggu_ke + 1;

					$tgl_sch_parent = date('Y-m-d', strtotime(date('Y-m-d', strtotime($tgl_sch_parent)). "+".$no." week"));

					$no++;
					
				}

				$margin_minggu = substr($margin_minggu, 0, -1);

				$this->db->query("delete from simpro_tbl_sch_proyek_parent where id_sch_proyek = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id' and minggu_ke NOT IN($margin_minggu)");
			break;
			case 'alat':
				$proyek_id = $this->input->post('proyek_id');
				$tahap_kendali_id = $this->input->post('tahap_kendali_id');
				$uraian 	= $this->input->post('uraian');
				$unit 	    = $this->input->post('unit_id');
				$tgl_awal 	= $this->input->post('tgl_awal');
				$tgl_akhir 	= $this->input->post('tgl_akhir');
				$bobot 	    = $this->input->post('bobot');	

				$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = '$proyek_id' ");
				$proyek = $sql->row_array();

				$tanggal_awal_project 	= $proyek['mulai'];
				$tanggal_akhir_project 	= $proyek['berakhir'];

				$data = array(
					'proyek_id' => $proyek_id,
					'tahap_kendali_id' => $tahap_kendali_id,
					'uraian'   => $uraian,
					'unit'     => $unit,
					'tgl_awal' => $tgl_awal,
					'tgl_akhir' => $tgl_akhir,
					'bobot'    => $bobot			
				 );

				$sql = $this->db->query("select * from simpro_tbl_sch_proyek_alat where proyek_id = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id'");     	
				$rows = $sql->num_rows();

				if ($rows == 0)

					$this->mdl_pengendalian->add_sch_project($info,$data);

				else

					$this->mdl_pengendalian->update_sch_project($info,$proyek_id, $tahap_kendali_id, $data);


				// insert detail bobot
				$awal_minggu  = $this->weeks_between($tanggal_awal_project, $tgl_awal);

				if ($awal_minggu == 0)
					$awal_minggu = 1;

				$total_minggu = $this->weeks_between($tgl_awal, $tgl_akhir);

				$total_minggu = $total_minggu + $awal_minggu;

				//var_dump($awal_minggu);

				// var_dump($total_minggu);
				// exit();

				$minggu_ke = $awal_minggu;
				$margin_minggu = '';

				$tgl_sch_parent = $tgl_awal;

				$no = 1;

				for ($i=$awal_minggu; $i < $total_minggu; $i++) {

					$sql = $this->db->query("select * from simpro_tbl_sch_proyek_parent_alat where id_sch_proyek = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id' and minggu_ke='$minggu_ke'");     	
					$rows = $sql->num_rows();

					if ($rows == 0){
						$data_sch_proyek_detail = array(
							'id_sch_proyek' => $proyek_id,
							'tgl_sch_parent' => $tgl_sch_parent,
							'minggu_ke' => $minggu_ke,
							'tahap_kendali_id' => $tahap_kendali_id
							);

						$this->mdl_pengendalian->insertdetailsch($info,$data_sch_proyek_detail);
						
					}
					else{
						$data_sch_proyek_detail = array(
							'tgl_sch_parent' => $tgl_sch_parent
							);

						$this->mdl_pengendalian->updatedetailsch($info,$data_sch_proyek_detail, $proyek_id, $tahap_kendali_id, $minggu_ke);
					}

					$margin_minggu .= $minggu_ke.',';
					$minggu_ke = $minggu_ke + 1;

					$tgl_sch_parent = date('Y-m-d', strtotime(date('Y-m-d', strtotime($tgl_sch_parent)). "+".$no." week"));

					$no++;
					
				}

				$margin_minggu = substr($margin_minggu, 0, -1);

				$this->db->query("delete from simpro_tbl_sch_proyek_parent_alat where id_sch_proyek = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id' and minggu_ke NOT IN($margin_minggu)");
			break;
			case 'bahan':
				$proyek_id = $this->input->post('proyek_id');
				$tahap_kendali_id = $this->input->post('tahap_kendali_id');
				$uraian 	= $this->input->post('uraian');
				$unit 	    = $this->input->post('unit_id');
				$tgl_awal 	= $this->input->post('tgl_awal');
				$tgl_akhir 	= $this->input->post('tgl_akhir');
				$bobot 	    = $this->input->post('bobot');	

				$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = '$proyek_id' ");
				$proyek = $sql->row_array();

				$tanggal_awal_project 	= $proyek['mulai'];
				$tanggal_akhir_project 	= $proyek['berakhir'];

				$data = array(
					'proyek_id' => $proyek_id,
					'tahap_kendali_id' => $tahap_kendali_id,
					'uraian'   => $uraian,
					'unit'     => $unit,
					'tgl_awal' => $tgl_awal,
					'tgl_akhir' => $tgl_akhir,
					'bobot'    => $bobot			
				 );

				$sql = $this->db->query("select * from simpro_tbl_sch_proyek_bahan where proyek_id = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id'");     	
				$rows = $sql->num_rows();

				if ($rows == 0)

					$this->mdl_pengendalian->add_sch_project($info,$data);

				else

					$this->mdl_pengendalian->update_sch_project($info,$proyek_id, $tahap_kendali_id, $data);


				// insert detail bobot
				$awal_minggu  = $this->weeks_between($tanggal_awal_project, $tgl_awal);

				if ($awal_minggu == 0)
					$awal_minggu = 1;

				$total_minggu = $this->weeks_between($tgl_awal, $tgl_akhir);

				$total_minggu = $total_minggu + $awal_minggu;

				//var_dump($awal_minggu);

				// var_dump($total_minggu);
				// exit();

				$minggu_ke = $awal_minggu;
				$margin_minggu = '';

				$tgl_sch_parent = $tgl_awal;

				$no = 1;

				for ($i=$awal_minggu; $i < $total_minggu; $i++) {

					$sql = $this->db->query("select * from simpro_tbl_sch_proyek_parent_bahan where id_sch_proyek = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id' and minggu_ke='$minggu_ke'");     	
					$rows = $sql->num_rows();

					if ($rows == 0){
						$data_sch_proyek_detail = array(
							'id_sch_proyek' => $proyek_id,
							'tgl_sch_parent' => $tgl_sch_parent,
							'minggu_ke' => $minggu_ke,
							'tahap_kendali_id' => $tahap_kendali_id
							);

						$this->mdl_pengendalian->insertdetailsch($info,$data_sch_proyek_detail);
						
					}
					else{
						$data_sch_proyek_detail = array(
							'tgl_sch_parent' => $tgl_sch_parent
							);

						$this->mdl_pengendalian->updatedetailsch($info,$data_sch_proyek_detail, $proyek_id, $tahap_kendali_id, $minggu_ke);
					}

					$margin_minggu .= $minggu_ke.',';
					$minggu_ke = $minggu_ke + 1;

					$tgl_sch_parent = date('Y-m-d', strtotime(date('Y-m-d', strtotime($tgl_sch_parent)). "+".$no." week"));

					$no++;
					
				}

				$margin_minggu = substr($margin_minggu, 0, -1);

				$this->db->query("delete from simpro_tbl_sch_proyek_parent_bahan where id_sch_proyek = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id' and minggu_ke NOT IN($margin_minggu)");
			break;
			case 'person':
				$proyek_id = $this->input->post('proyek_id');
				$tahap_kendali_id = $this->input->post('tahap_kendali_id');
				$uraian 	= $this->input->post('uraian');
				$unit 	    = $this->input->post('unit_id');
				$tgl_awal 	= $this->input->post('tgl_awal');
				$tgl_akhir 	= $this->input->post('tgl_akhir');
				$bobot 	    = $this->input->post('bobot');	

				$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = '$proyek_id' ");
				$proyek = $sql->row_array();

				$tanggal_awal_project 	= $proyek['mulai'];
				$tanggal_akhir_project 	= $proyek['berakhir'];

				$data = array(
					'proyek_id' => $proyek_id,
					'tahap_kendali_id' => $tahap_kendali_id,
					'uraian'   => $uraian,
					'unit'     => $unit,
					'tgl_awal' => $tgl_awal,
					'tgl_akhir' => $tgl_akhir,
					'bobot'    => $bobot			
				 );

				$sql = $this->db->query("select * from simpro_tbl_sch_proyek_person where proyek_id = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id'");     	
				$rows = $sql->num_rows();

				if ($rows == 0)

					$this->mdl_pengendalian->add_sch_project($info,$data);

				else

					$this->mdl_pengendalian->update_sch_project($info,$proyek_id, $tahap_kendali_id, $data);


				// insert detail bobot
				$awal_minggu  = $this->weeks_between($tanggal_awal_project, $tgl_awal);

				if ($awal_minggu == 0)
					$awal_minggu = 1;

				$total_minggu = $this->weeks_between($tgl_awal, $tgl_akhir);

				$total_minggu = $total_minggu + $awal_minggu;

				//var_dump($awal_minggu);

				// var_dump($total_minggu);
				// exit();

				$minggu_ke = $awal_minggu;
				$margin_minggu = '';

				$tgl_sch_parent = $tgl_awal;

				$no = 1;

				for ($i=$awal_minggu; $i < $total_minggu; $i++) {

					$sql = $this->db->query("select * from simpro_tbl_sch_proyek_parent_person where id_sch_proyek = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id' and minggu_ke='$minggu_ke'");     	
					$rows = $sql->num_rows();

					if ($rows == 0){
						$data_sch_proyek_detail = array(
							'id_sch_proyek' => $proyek_id,
							'tgl_sch_parent' => $tgl_sch_parent,
							'minggu_ke' => $minggu_ke,
							'tahap_kendali_id' => $tahap_kendali_id
							);

						$this->mdl_pengendalian->insertdetailsch($info,$data_sch_proyek_detail);
						
					}
					else{
						$data_sch_proyek_detail = array(
							'tgl_sch_parent' => $tgl_sch_parent
							);

						$this->mdl_pengendalian->updatedetailsch($info,$data_sch_proyek_detail, $proyek_id, $tahap_kendali_id, $minggu_ke);
					}

					$margin_minggu .= $minggu_ke.',';
					$minggu_ke = $minggu_ke + 1;

					$tgl_sch_parent = date('Y-m-d', strtotime(date('Y-m-d', strtotime($tgl_sch_parent)). "+".$no." week"));

					$no++;
					
				}

				$margin_minggu = substr($margin_minggu, 0, -1);

				$this->db->query("delete from simpro_tbl_sch_proyek_parent_person where id_sch_proyek = '$proyek_id' and tahap_kendali_id='$tahap_kendali_id' and minggu_ke NOT IN($margin_minggu)");
			break;		
			case 'peralatan':
				$proyek_id = $this->input->post('proyek_id');
				$id_analisa_asat = $this->input->post('id_analisa_asat');
				$alat 	= $this->input->post('alat');
				$unit 	    = $this->input->post('unit_id');
				$tgl_awal 	= $this->input->post('tgl_awal');
				$tgl_akhir 	= $this->input->post('tgl_akhir');
				$jumlah 	    = $this->input->post('jumlah');	

				$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = $proyek_id ");
				$proyek = $sql->row_array();

				$tanggal_awal_project 	= $proyek['mulai'];
				$tanggal_akhir_project 	= $proyek['berakhir'];

				$data = array(
					'proyek_id' => $proyek_id,
					'id_analisa_asat' => $id_analisa_asat,
					'uraian'   => $alat,
					'unit'     => $unit,
					'tgl_awal' => $tgl_awal,
					'tgl_akhir' => $tgl_akhir,
					'jumlah'    => $jumlah			
				 );

				$sql = $this->db->query("select * from simpro_tbl_guna_alat where proyek_id = '$proyek_id' and id_analisa_asat='$id_analisa_asat'");     	
				$rows = $sql->num_rows();

				if ($rows == 0)

					$this->mdl_pengendalian->add_sch_project($info,$data);

				else

					$this->mdl_pengendalian->update_sch_project($info,$proyek_id, $id_analisa_asat, $data);


				// insert detail bobot
				$awal_minggu  = $this->weeks_between($tanggal_awal_project, $tgl_awal);

				if ($awal_minggu == 0)
					$awal_minggu = 1;

				$total_minggu = $this->weeks_between($tgl_awal, $tgl_akhir);

				$total_minggu = $total_minggu + $awal_minggu;

				//var_dump($awal_minggu);

				// var_dump($total_minggu);
				// exit();

				$minggu_ke = $awal_minggu;
				$margin_minggu = '';

				$tgl_sch_parent = $tgl_awal;

				$no = 1;

				for ($i=$awal_minggu; $i < $total_minggu; $i++) {

					$sql = $this->db->query("select * from simpro_tbl_guna_alat_parent where id_guna_alat = '$proyek_id' and id_analisa_asat='$id_analisa_asat' and minggu_ke='$minggu_ke'");     	
					$rows = $sql->num_rows();

					if ($rows == 0){
						$data_sch_proyek_detail = array(
							'id_guna_alat' => $proyek_id,
							'tgl_sch_parent' => $tgl_sch_parent,
							'minggu_ke' => $minggu_ke,
							'id_analisa_asat' => $id_analisa_asat
							);

						$this->mdl_pengendalian->insertdetailsch($info,$data_sch_proyek_detail);
						
					}
					else{
						$data_sch_proyek_detail = array(
							'tgl_sch_parent' => $tgl_sch_parent
							);

						$this->mdl_pengendalian->updatedetailsch($info,$data_sch_proyek_detail, $proyek_id, $id_analisa_asat, $minggu_ke);
					}

					$margin_minggu .= $minggu_ke.',';
					$minggu_ke = $minggu_ke + 1;

					$tgl_sch_parent = date('Y-m-d', strtotime(date('Y-m-d', strtotime($tgl_sch_parent)). "+".$no." week"));

					$no++;
					
				}

				$margin_minggu = substr($margin_minggu, 0, -1);

				$this->db->query("delete from simpro_tbl_guna_alat_parent where id_guna_alat = '$proyek_id' and id_analisa_asat='$id_analisa_asat' and minggu_ke NOT IN($margin_minggu)");
			break;	
			default:
				echo "Access Forbidden";
			break;
		}

	}

	function getWeeks($date, $rollover)
    {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $i = 1;
        $weeks = 1;

        for($i; $i<=$elapsed; $i++)
        {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }

        return $weeks;
    }

    function weeks_between($datefrom, $dateto)
    {
    	$diff = strtotime($datefrom, 0) - strtotime($dateto, 0);
    	$week = abs(floor($diff / 604800));

    	return $week;
    }

    function update_bobot_sch_parent($info="")
    {
    	switch ($info) {
			case 'proyek':
		    	$id = $this->input->post('id');
		    	$bobot_parent = $this->input->post('bobot_parent');

		    	$this->db->query("update simpro_tbl_sch_proyek_parent set bobot_parent='$bobot_parent' where id = '$id'"); 
			break;
			case 'alat':
		    	$id = $this->input->post('id');
		    	$bobot_parent = $this->input->post('bobot_parent');

		    	$this->db->query("update simpro_tbl_sch_proyek_parent_alat set bobot_parent='$bobot_parent' where id = '$id'"); 
			break;
			case 'bahan':
		    	$id = $this->input->post('id');
		    	$bobot_parent = $this->input->post('bobot_parent');

		    	$this->db->query("update simpro_tbl_sch_proyek_parent_bahan set bobot_parent='$bobot_parent' where id = '$id'"); 
			break;
			case 'person':
		    	$id = $this->input->post('id');
		    	$bobot_parent = $this->input->post('bobot_parent');

		    	$this->db->query("update simpro_tbl_sch_proyek_parent_person set bobot_parent='$bobot_parent' where id = '$id'"); 
			break;	
			case 'peralatan':
		    	$id = $this->input->post('id');
		    	$jumlah_parent = $this->input->post('jumlah_parent');

		    	$this->db->query("update simpro_tbl_guna_alat_parent set jumlah_parent='$jumlah_parent' where id = '$id'"); 
			break;		
			default:
				echo "Access Forbidden";
			break;
		}    	

    }

    function deletesch($info="")
	{
		switch ($info) {
			case 'proyek':
				$proyek_id = $this->input->post('proyek_id');
				$tahap_kendali_id = $this->input->post('tahap_kendali_id');

				$this->mdl_pengendalian->deletesch($info,$proyek_id,$tahap_kendali_id);
			break;
			case 'alat':
				$proyek_id = $this->input->post('proyek_id');
				$tahap_kendali_id = $this->input->post('tahap_kendali_id');

				$this->mdl_pengendalian->deletesch($info,$proyek_id,$tahap_kendali_id);
			break;
			case 'bahan':
				$proyek_id = $this->input->post('proyek_id');
				$tahap_kendali_id = $this->input->post('tahap_kendali_id');

				$this->mdl_pengendalian->deletesch($info,$proyek_id,$tahap_kendali_id);
			break;
			case 'person':
				$proyek_id = $this->input->post('proyek_id');
				$tahap_kendali_id = $this->input->post('tahap_kendali_id');

				$this->mdl_pengendalian->deletesch($info,$proyek_id,$tahap_kendali_id);
			break;		
			case 'peralatan':
				$proyek_id = $this->input->post('proyek_id');
				$id_analisa_asat = $this->input->post('id_analisa_asat');

				$this->mdl_pengendalian->deletesch($info,$proyek_id,$id_analisa_asat);
			break;	
			default:
				echo "Access Forbidden";
			break;
		}

	}

	function deleteschparent($info="")
	{		
		switch ($info) {
			case 'proyek':
				$id = $this->input->post('id');

				$this->mdl_pengendalian->deleteschparent($info,$id);
			break;
			case 'alat':
				$id = $this->input->post('id');

				$this->mdl_pengendalian->deleteschparent($info,$id);
			break;
			case 'bahan':
				$id = $this->input->post('id');

				$this->mdl_pengendalian->deleteschparent($info,$id);
			break;
			case 'person':
				$id = $this->input->post('id');

				$this->mdl_pengendalian->deleteschparent($info,$id);
			break;			
			case 'peralatan':
				$id = $this->input->post('id');

				$this->mdl_pengendalian->deleteschparent($info,$id);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}


	function schedule_alat()
	{	
		$this->load->view('schedule_alat');
	}

	function schedule_bahan()
	{	
		$this->load->view('schedule_bahan');
	}

	function schedule_person()
	{	
		$this->load->view('schedule_person');
	}

	function schedule_cart($page='',$setting='')
	{
		if ($page == 'proyek') {
			$info = 'proyek';
		} elseif ($page == 'alat') {
			$info = 'alat';
		} elseif ($page == 'bahan') {
			$info = 'bahan';
		} elseif ($page == 'person') {
			$info = 'person';	
		} elseif ($page == 'peralatan') {
			$info = 'peralatan';	
		}

		$this->load->library('pey_chart');

		$cart_data = array();

		$proyek_id = $this->session->userdata('proyek_id');

		$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = '$proyek_id' ");
		$proyek = $sql->row_array();

		$tanggal_awal 	= $proyek['mulai'];
		$tanggal_akhir 	= $proyek['berakhir'];

		$jumlah_bobot = $this->mdl_pengendalian->get_jml_sch_proyek($info,$proyek_id);

		// setting cart

		if ($setting == '' || ($setting != 1 && $setting != 2))
			$setting = 2;

		$this->pey_chart->set_chart($proyek_id, $tanggal_awal, $tanggal_akhir, $jumlah_bobot, $setting);

		// setting data cart

		$jml_unit_project = $this->mdl_pengendalian->get_jml_unit_project($info,$proyek_id);

		foreach ($jml_unit_project as $key => $val) {

			$proyek_id = $val['proyek_id'];

			if ($info == 'peralatan') {
				$tahap_kendali_id = $val['id_analisa_asat'];
			} else {
				$tahap_kendali_id = $val['tahap_kendali_id'];
			}

			$id_unit = $val['id'];

			if ($info == 'peralatan') {
				$bobot_unit = $val['jumlah'];
			} else {
				$bobot_unit = $val['bobot'];
			}
			
			$jumlah_bobot_per_unit = $this->mdl_pengendalian->get_jml_bobot_per_unit($info,$proyek_id, $tahap_kendali_id);		

			if ($jumlah_bobot_per_unit == 0 || $bobot_unit == 0) {
				$bobot_percent = 0;
			} else {
				$bobot_percent = $jumlah_bobot_per_unit / $bobot_unit * 100;	
			}

			(int)$bobot_percent;

			$label = trim($val['uraian']);

			//cari minggu pertama ke dari tanggal input
			$tanggal_input_awal = $this->mdl_pengendalian->get_minggu_awal_per_input_bobot_unit($info,$proyek_id, $tahap_kendali_id);
		    $margin_star = $this->getWeeks($tanggal_input_awal, 'monday');

		    $format_tgl_awal = explode('-', $tanggal_awal);
		    $tanggal_awal_thn = $format_tgl_awal[0];
		    $tanggal_awal_bln = $format_tgl_awal[1];

		    $format_tgl_input_awal = explode('-', $tanggal_input_awal);

		    $tanggal_input_awal_bln = 0;

		    if(count($format_tgl_input_awal) > 1){
			    $tanggal_input_awal_thn = ($format_tgl_input_awal[0] - $tanggal_awal_thn) * (12 * 4);
			    $tanggal_input_awal_bln = $tanggal_input_awal_thn + (($format_tgl_input_awal[1] - $tanggal_awal_bln) * 4);
			}
			
		    $margin_star = $margin_star + $tanggal_input_awal_bln;

		    //cari minggu terakhir ke dari tanggal input
			$tanggal_input_akhir = $this->mdl_pengendalian->get_minggu_akhir_per_input_boot_unit($info,$id_unit);
		    $width = $this->getWeeks($tanggal_input_akhir, 'monday');
		    $new_margin_star = $this->getWeeks($tanggal_input_awal, 'monday');

		    $width = abs($width - $new_margin_star) + 1;

		    if ($margin_star == 0)
		    	$margin_star = 1;

		    if($width == 0)
		    	$width = 1;

		    $proyek_id = $val['proyek_id'];

		    if ($info == 'peralatan') {
				$tahap_kendali_id = $val['id_analisa_asat'];
			} else {
		    	$tahap_kendali_id = $val['tahap_kendali_id'];
			}

			if ($info == 'peralatan') {
				$val_bobot = $val['jumlah'];
			} else {
		    	$val_bobot = $val['bobot'];
			}

		    if ($page == 'proyek') {
				$sql_info = "select * from simpro_rap_item_tree where id_proyek = $proyek_id and rap_item_tree='$tahap_kendali_id'";
			} elseif ($page == 'alat') {
				$sql_info = "select 
							a.kode_material as kode_tree,
							b.detail_material_satuan as tree_satuan
							from simpro_rap_analisa_asat a 
							join simpro_tbl_detail_material b 
							on a.kode_material = b.detail_material_kode 
							where a.id_proyek = $proyek_id
							and a.id_analisa_asat='$tahap_kendali_id'
							and left(a.kode_material,3) = '502'";
			} elseif ($page == 'bahan') {
				$sql_info = "select 
							a.kode_material as kode_tree,
							b.detail_material_satuan as tree_satuan
							from simpro_rap_analisa_asat a 
							join simpro_tbl_detail_material b 
							on a.kode_material = b.detail_material_kode 
							where a.id_proyek = $proyek_id
							and a.id_analisa_asat='$tahap_kendali_id'
							and left(a.kode_material,3) = '500'";
			} elseif ($page == 'person') {
				$sql_info = "select 
							a.kode_material as kode_tree,
							b.detail_material_satuan as tree_satuan
							from simpro_rap_analisa_asat a 
							join simpro_tbl_detail_material b 
							on a.kode_material = b.detail_material_kode 
							where a.id_proyek = $proyek_id
							and a.id_analisa_asat='$tahap_kendali_id'
							and left(a.kode_material,3) = '501'";
			} elseif ($page == 'peralatan') {
				$sql_info = "select 
							a.kode_material as kode_tree,
							b.detail_material_satuan as tree_satuan
							from simpro_rap_analisa_asat a 
							join simpro_tbl_detail_material b 
							on a.kode_material = b.detail_material_kode 
							where a.id_proyek = $proyek_id
							and a.id_analisa_asat='$tahap_kendali_id'
							and left(a.kode_material,3) = '502'";
			}
			
		    $sql = $this->db->query($sql_info);     	
			$data_tahap_kendali = $sql->row_array();

			// $satuan_id = $data_tahap_kendali['tahap_satuan_kendali'];
			// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_id = '$satuan_id'");     	
			// $satuan = $sql->row_array();

			$new_margin_star = $this->mdl_pengendalian->get_minggu_awal_per_unit($info,$proyek_id, $tahap_kendali_id);

			$new_width = $this->mdl_pengendalian->get_total_minggu_per_unit($info,$proyek_id, $tahap_kendali_id);

			$new_width = count($new_width);

			$cart_data[] = array(
				'tahap_kendali_id' => $tahap_kendali_id,
				'tahap_kode_kendali' => $data_tahap_kendali['kode_tree'],
				'label' => $label,
				'unit' => $data_tahap_kendali['tree_satuan'],
				'bobot_unit' => $val_bobot,			
				'value' => $jumlah_bobot_per_unit,
				'total_percent' => $bobot_percent,
				//'margin_star' => $margin_star,
				'margin_star' => $new_margin_star['minggu_ke'],
				'width' => $new_width
			);

			// $cart_data3[$data_tahap_kendali['tahap_kode_kendali']] = array(
			// 	'tahap_kode_kendali' => $data_tahap_kendali['tahap_kode_kendali'],
			// 	'label' => $label,
			// 	'unit' => $satuan['satuan_nama'],
			// 	'bobot_unit' => $val['bobot'],			
			// 	'value' => $jumlah_bobot_per_unit,
			// 	'total_percent' => $bobot_percent,
			// 	'margin_star' => $margin_star,
			// 	'width' => $width
			// 	);

			//var_dump($cart_data3[$data_tahap_kendali['tahap_kode_kendali']]);
			//echo "<br><br>";
		}


		$this->pey_chart->chart_data($cart_data);

		$data['chart'] = $this->pey_chart->get_chart($info);


		// var_dump($cart_data);
		$this->load->view('schedule_cart', $data);
	}

	function schedule_cart_alat($setting='')
	{
		$this->load->library('pey_chart');

		$cart_data = array();

		$proyek_id = $this->session->userdata('proyek_id');
		$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = '$proyek_id' ");
		$proyek = $sql->row_array();

		$tanggal_awal 	= $proyek['mulai'];
		$tanggal_akhir 	= $proyek['berakhir'];

		$jumlah_bobot = $this->mdl_pengendalian->get_jml_sch_proyek('alat',$proyek_id);

		// setting cart

		if ($setting == '' || ($setting != 1 && $setting != 2))
			$setting = 2;

		$this->pey_chart->set_chart($proyek_id, $tanggal_awal, $tanggal_akhir, $jumlah_bobot, $setting);

		// setting data cart

		$jml_unit_project = $this->mdl_pengendalian->get_jml_unit_project('alat',$proyek_id);

		foreach ($jml_unit_project as $key => $val) {

			$proyek_id = $val['proyek_id'];

			$tahap_kendali_id = $val['tahap_kendali_id'];

			$id_unit = $val['id'];

			$bobot_unit = $val['bobot'];

			$jumlah_bobot_per_unit = $this->mdl_pengendalian->get_jml_bobot_per_unit('alat',$proyek_id, $tahap_kendali_id);		

			$bobot_percent = $jumlah_bobot_per_unit;

			(int)$bobot_percent;

			$label = trim($val['uraian']);

			//cari minggu pertama ke dari tanggal input
			$tanggal_input_awal = $this->mdl_pengendalian->get_minggu_awal_per_input_bobot_unit('alat',$proyek_id, $tahap_kendali_id);
		    $margin_star = $this->getWeeks($tanggal_input_awal, 'monday');

		    $format_tgl_awal = explode('-', $tanggal_awal);
		    $tanggal_awal_thn = $format_tgl_awal[0];
		    $tanggal_awal_bln = $format_tgl_awal[1];

		    $format_tgl_input_awal = explode('-', $tanggal_input_awal);

		    $tanggal_input_awal_bln = 0;

		    if(count($format_tgl_input_awal) > 1){
			    $tanggal_input_awal_thn = ($format_tgl_input_awal[0] - $tanggal_awal_thn) * (12 * 4);
			    $tanggal_input_awal_bln = $tanggal_input_awal_thn + (($format_tgl_input_awal[1] - $tanggal_awal_bln) * 4);
			}
			
		    $margin_star = $margin_star + $tanggal_input_awal_bln;

		    //cari minggu terakhir ke dari tanggal input
			$tanggal_input_akhir = $this->mdl_pengendalian->get_minggu_akhir_per_input_boot_unit('alat',$id_unit);
		    $width = $this->getWeeks($tanggal_input_akhir, 'monday');
		    $new_margin_star = $this->getWeeks($tanggal_input_awal, 'monday');

		    $width = abs($width - $new_margin_star) + 1;

		    if ($margin_star == 0)
		    	$margin_star = 1;

		    if($width == 0)
		    	$width = 1;

		    $proyek_id = $val['proyek_id'];
		    $tahap_kendali_id = $val['tahap_kendali_id'];

		    $sql = $this->db->query("select a.*, 
					b.detail_material_kode as m_kode, 
					b.detail_material_nama as m_nama, 
					b.detail_material_satuan as m_satuan
					from simpro_tbl_komposisi_kendali a 
					join simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					where a.proyek_id = $proyek_id
					and komposisi_kendali_id='$tahap_kendali_id'");

			$data_tahap_kendali = $sql->row_array();

			// $satuan_id = $data_tahap_kendali['m_satuan'];
			// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_nama = '$satuan_id'");     	
			// $satuan = $sql->row_array();

			$new_margin_star = $this->mdl_pengendalian->get_minggu_awal_per_unit('alat',$proyek_id, $tahap_kendali_id);

			$new_width = $this->mdl_pengendalian->get_total_minggu_per_unit('alat',$proyek_id, $tahap_kendali_id);

			$new_width = count($new_width);

			$cart_data[] = array(
				'tahap_kendali_id' => $tahap_kendali_id,
				'tahap_kode_kendali' => $data_tahap_kendali['m_kode'],
				'label' => $label,
				'unit' => $data_tahap_kendali['m_satuan'],
				'bobot_unit' => $val['bobot'],			
				'value' => $jumlah_bobot_per_unit,
				'total_percent' => $bobot_percent,
				//'margin_star' => $margin_star,
				'margin_star' => $new_margin_star['minggu_ke'],
				'width' => $new_width
				);

			// $cart_data3[$data_tahap_kendali['tahap_kode_kendali']] = array(
			// 	'tahap_kode_kendali' => $data_tahap_kendali['tahap_kode_kendali'],
			// 	'label' => $label,
			// 	'unit' => $satuan['satuan_nama'],
			// 	'bobot_unit' => $val['bobot'],			
			// 	'value' => $jumlah_bobot_per_unit,
			// 	'total_percent' => $bobot_percent,
			// 	'margin_star' => $margin_star,
			// 	'width' => $width
			// 	);

			//var_dump($cart_data3[$data_tahap_kendali['tahap_kode_kendali']]);
			//echo "<br><br>";
		}

		$this->pey_chart->chart_data($cart_data);

		$data['chart'] = $this->pey_chart->get_chart();

		$this->load->view('schedule_cart_alat', $data);
	}

	function schedule_cart_bahan($setting='')
	{
		$this->load->library('pey_chart');

		$cart_data = array();

		$proyek_id = $this->session->userdata('proyek_id');
		$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = '$proyek_id' ");
		$proyek = $sql->row_array();

		$tanggal_awal 	= $proyek['mulai'];
		$tanggal_akhir 	= $proyek['berakhir'];

		$jumlah_bobot = $this->mdl_pengendalian->get_jml_sch_proyek('bahan',$proyek_id);

		// setting cart

		if ($setting == '' || ($setting != 1 && $setting != 2))
			$setting = 2;

		$this->pey_chart->set_chart($proyek_id, $tanggal_awal, $tanggal_akhir, $jumlah_bobot, $setting);

		// setting data cart

		$jml_unit_project = $this->mdl_pengendalian->get_jml_unit_project('bahan',$proyek_id);

		foreach ($jml_unit_project as $key => $val) {

			$proyek_id = $val['proyek_id'];

			$tahap_kendali_id = $val['tahap_kendali_id'];

			$id_unit = $val['id'];

			$bobot_unit = $val['bobot'];

			$jumlah_bobot_per_unit = $this->mdl_pengendalian->get_jml_bobot_per_unit('bahan',$proyek_id, $tahap_kendali_id);		

			$bobot_percent = $jumlah_bobot_per_unit;

			(int)$bobot_percent;

			$label = trim($val['uraian']);

			//cari minggu pertama ke dari tanggal input
			$tanggal_input_awal = $this->mdl_pengendalian->get_minggu_awal_per_input_bobot_unit('bahan',$proyek_id, $tahap_kendali_id);
		    $margin_star = $this->getWeeks($tanggal_input_awal, 'monday');

		    $format_tgl_awal = explode('-', $tanggal_awal);
		    $tanggal_awal_thn = $format_tgl_awal[0];
		    $tanggal_awal_bln = $format_tgl_awal[1];

		    $format_tgl_input_awal = explode('-', $tanggal_input_awal);

		    $tanggal_input_awal_bln = 0;

		    if(count($format_tgl_input_awal) > 1){
			    $tanggal_input_awal_thn = ($format_tgl_input_awal[0] - $tanggal_awal_thn) * (12 * 4);
			    $tanggal_input_awal_bln = $tanggal_input_awal_thn + (($format_tgl_input_awal[1] - $tanggal_awal_bln) * 4);
			}
			
		    $margin_star = $margin_star + $tanggal_input_awal_bln;

		    //cari minggu terakhir ke dari tanggal input
			$tanggal_input_akhir = $this->mdl_pengendalian->get_minggu_akhir_per_input_boot_unit('bahan',$id_unit);
		    $width = $this->getWeeks($tanggal_input_akhir, 'monday');
		    $new_margin_star = $this->getWeeks($tanggal_input_awal, 'monday');

		    $width = abs($width - $new_margin_star) + 1;

		    if ($margin_star == 0)
		    	$margin_star = 1;

		    if($width == 0)
		    	$width = 1;

		    $proyek_id = $val['proyek_id'];
		    $tahap_kendali_id = $val['tahap_kendali_id'];

		    $sql = $this->db->query("select a.*, 
					b.detail_material_kode as m_kode, 
					b.detail_material_nama as m_nama, 
					b.detail_material_satuan as m_satuan
					from simpro_tbl_komposisi_kendali a 
					join simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					where a.proyek_id = $proyek_id
					and komposisi_kendali_id='$tahap_kendali_id'");

			$data_tahap_kendali = $sql->row_array();

			// $satuan_id = $data_tahap_kendali['m_satuan'];
			// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_nama = '$satuan_id'");     	
			// $satuan = $sql->row_array();

			$new_margin_star = $this->mdl_pengendalian->get_minggu_awal_per_unit('bahan',$proyek_id, $tahap_kendali_id);

			$new_width = $this->mdl_pengendalian->get_total_minggu_per_unit('bahan',$proyek_id, $tahap_kendali_id);

			$new_width = count($new_width);

			$cart_data[] = array(
				'tahap_kendali_id' => $tahap_kendali_id,
				'tahap_kode_kendali' => $data_tahap_kendali['m_kode'],
				'label' => $label,
				'unit' => $data_tahap_kendali['m_satuan'],
				'bobot_unit' => $val['bobot'],			
				'value' => $jumlah_bobot_per_unit,
				'total_percent' => $bobot_percent,
				//'margin_star' => $margin_star,
				'margin_star' => $new_margin_star['minggu_ke'],
				'width' => $new_width
				);

			// $cart_data3[$data_tahap_kendali['tahap_kode_kendali']] = array(
			// 	'tahap_kode_kendali' => $data_tahap_kendali['tahap_kode_kendali'],
			// 	'label' => $label,
			// 	'unit' => $satuan['satuan_nama'],
			// 	'bobot_unit' => $val['bobot'],			
			// 	'value' => $jumlah_bobot_per_unit,
			// 	'total_percent' => $bobot_percent,
			// 	'margin_star' => $margin_star,
			// 	'width' => $width
			// 	);

			//var_dump($cart_data3[$data_tahap_kendali['tahap_kode_kendali']]);
			//echo "<br><br>";
		}

		$this->pey_chart->chart_data($cart_data);

		$data['chart'] = $this->pey_chart->get_chart();

		$this->load->view('schedule_cart_bahan', $data);
	}

	function schedule_cart_person($setting='')
	{
		$this->load->library('pey_chart');

		$cart_data = array();

		$proyek_id = $this->session->userdata('proyek_id');
		$sql = $this->db->query("select * from simpro_tbl_proyek where proyek_id = '$proyek_id' ");
		$proyek = $sql->row_array();

		$tanggal_awal 	= $proyek['mulai'];
		$tanggal_akhir 	= $proyek['berakhir'];

		$jumlah_bobot = $this->mdl_pengendalian->get_jml_sch_proyek('person',$proyek_id);

		// setting cart

		if ($setting == '' || ($setting != 1 && $setting != 2))
			$setting = 2;

		$this->pey_chart->set_chart($proyek_id, $tanggal_awal, $tanggal_akhir, $jumlah_bobot, $setting);

		// setting data cart

		$jml_unit_project = $this->mdl_pengendalian->get_jml_unit_project('person',$proyek_id);

		foreach ($jml_unit_project as $key => $val) {

			$proyek_id = $val['proyek_id'];

			$tahap_kendali_id = $val['tahap_kendali_id'];

			$id_unit = $val['id'];

			$bobot_unit = $val['bobot'];

			$jumlah_bobot_per_unit = $this->mdl_pengendalian->get_jml_bobot_per_unit('person',$proyek_id, $tahap_kendali_id);		

			$bobot_percent = $jumlah_bobot_per_unit;

			(int)$bobot_percent;

			$label = trim($val['uraian']);

			//cari minggu pertama ke dari tanggal input
			$tanggal_input_awal = $this->mdl_pengendalian->get_minggu_awal_per_input_bobot_unit('person',$proyek_id, $tahap_kendali_id);
		    $margin_star = $this->getWeeks($tanggal_input_awal, 'monday');

		    $format_tgl_awal = explode('-', $tanggal_awal);
		    $tanggal_awal_thn = $format_tgl_awal[0];
		    $tanggal_awal_bln = $format_tgl_awal[1];

		    $format_tgl_input_awal = explode('-', $tanggal_input_awal);

		    $tanggal_input_awal_bln = 0;

		    if(count($format_tgl_input_awal) > 1){
			    $tanggal_input_awal_thn = ($format_tgl_input_awal[0] - $tanggal_awal_thn) * (12 * 4);
			    $tanggal_input_awal_bln = $tanggal_input_awal_thn + (($format_tgl_input_awal[1] - $tanggal_awal_bln) * 4);
			}
			
		    $margin_star = $margin_star + $tanggal_input_awal_bln;

		    //cari minggu terakhir ke dari tanggal input
			$tanggal_input_akhir = $this->mdl_pengendalian->get_minggu_akhir_per_input_boot_unit('person',$id_unit);
		    $width = $this->getWeeks($tanggal_input_akhir, 'monday');
		    $new_margin_star = $this->getWeeks($tanggal_input_awal, 'monday');

		    $width = abs($width - $new_margin_star) + 1;

		    if ($margin_star == 0)
		    	$margin_star = 1;

		    if($width == 0)
		    	$width = 1;

		    $proyek_id = $val['proyek_id'];
		    $tahap_kendali_id = $val['tahap_kendali_id'];

		    $sql = $this->db->query("select a.*, 
					b.detail_material_kode as m_kode, 
					b.detail_material_nama as m_nama, 
					b.detail_material_satuan as m_satuan
					from simpro_tbl_komposisi_kendali a 
					join simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					where a.proyek_id = $proyek_id
					and komposisi_kendali_id='$tahap_kendali_id'");

			$data_tahap_kendali = $sql->row_array();

			// $satuan_id = $data_tahap_kendali['m_satuan'];
			// $sql = $this->db->query("select * from simpro_tbl_satuan where satuan_nama = '$satuan_id'");     	
			// $satuan = $sql->row_array();

			$new_margin_star = $this->mdl_pengendalian->get_minggu_awal_per_unit('person',$proyek_id, $tahap_kendali_id);

			$new_width = $this->mdl_pengendalian->get_total_minggu_per_unit('person',$proyek_id, $tahap_kendali_id);

			$new_width = count($new_width);

			$cart_data[] = array(
				'tahap_kendali_id' => $tahap_kendali_id,
				'tahap_kode_kendali' => $data_tahap_kendali['m_kode'],
				'label' => $label,
				'unit' => $data_tahap_kendali['m_satuan'],
				'bobot_unit' => $val['bobot'],			
				'value' => $jumlah_bobot_per_unit,
				'total_percent' => $bobot_percent,
				//'margin_star' => $margin_star,
				'margin_star' => $new_margin_star['minggu_ke'],
				'width' => $new_width
				);

			// $cart_data3[$data_tahap_kendali['tahap_kode_kendali']] = array(
			// 	'tahap_kode_kendali' => $data_tahap_kendali['tahap_kode_kendali'],
			// 	'label' => $label,
			// 	'unit' => $satuan['satuan_nama'],
			// 	'bobot_unit' => $val['bobot'],			
			// 	'value' => $jumlah_bobot_per_unit,
			// 	'total_percent' => $bobot_percent,
			// 	'margin_star' => $margin_star,
			// 	'width' => $width
			// 	);

			//var_dump($cart_data3[$data_tahap_kendali['tahap_kode_kendali']]);
			//echo "<br><br>";
		}

		$this->pey_chart->chart_data($cart_data);

		$data['chart'] = $this->pey_chart->get_chart();

		$this->load->view('schedule_cart_person', $data);
	}
    //add
	
	function get_proyek_info($id)
	{
		$sql = sprintf("SELECT * FROM simpro_tbl_proyek WHERE proyek_id = '%d'", $id);	
		$data = $this->db->query($sql)->row_array();
		if(isset($data['proyek'])) return $data;
	}
	
	function cost_to_go($kunci="",$tgl_rab="")
	{
		if(!$tgl_rab || !$kunci){
			echo "Access Forbidden";
		}else{
			$idproyek = $this->session->userdata('proyek_id');

			$data['bln']= '';
			$data['thn']= '';
			$data['tgl_rab']= $tgl_rab;
			$data['kunci'] = $kunci;
			$data['data_proyek']= $this->get_proyek_info($idproyek);
			$data['id_proyek'] = $idproyek;

			$this->load->view('cost_to_go_new',$data);
		}
	}

	function current_budget($kunci="",$tgl_rab="")
	{
		if(!$tgl_rab || !$kunci){
			echo "Access Forbidden";
		}else{
			$idproyek = $this->session->userdata('proyek_id');

			$data['bln']= '';
			$data['thn']= '';
			$data['tgl_rab']= $tgl_rab;
			$data['kunci'] = $kunci;
			$data['data_proyek']= $this->get_proyek_info($idproyek);
			$data['id_proyek'] = $idproyek;

			$this->load->view('current_budget_new',$data);
		}
	}

	function edit_analisa_costogo($kunci="",$tgl_rab="")
	{		
		$idproyek = $this->session->userdata('proyek_id');
		$data = array(
			'id_proyek'=>$this->session->userdata('proyek_id'),
			'data_proyek'=>$this->get_proyek_info($idproyek),
			'tgl_rab'=>$tgl_rab,
			'kunci'=>$kunci
			);
		$this->load->view("ctg_analisa",$data);
	}

	function edit_analisa_current_budget($kunci="",$tgl_rab="")
	{		
		$idproyek = $this->session->userdata('proyek_id');
		$data = array(
			'id_proyek'=>$this->session->userdata('proyek_id'),
			'data_proyek'=>$this->get_proyek_info($idproyek),
			'tgl_rab'=>$tgl_rab,
			'kunci'=>$kunci
			);
		$this->load->view("cbd_analisa",$data);
	}

	function excel($page="",$tgl_rab="")
	{
		//load our new PHPExcel library
		$this->load->library('excel');
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('test worksheet');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'This is just some text value');
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//merge cell A1 until D1
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		//set aligment to center for that merged cell (A1 to D1)
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 
		$filename='asli.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		             
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function ext_excel()
	{
		$this->load->view('example/index.php');
	}

	function kurvas()
	{
		$data['chart'] = $this->get_line_chart('proyek');		
		$data['week'] = $this->get_line_week('proyek');
		$this->load->view('kurvas.php',$data);
	}

	function get_line_chart($info="")
	{
		if ($info == 'proyek') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent';
		} elseif ($info == 'alat') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_alat';
		} elseif ($info == 'bahan') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_bahan';
		} elseif ($info == 'person') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_person';	
		} elseif ($info == 'peralatan') {
			$tbl_info = 'simpro_tbl_guna_alat_parent';	
		}

		if ($info == 'peralatan') {
			$field_info = 'id_guna_alat';
			$field_info_b = 'id_analisa_asat';
			$field_info_c = 'jumlah_parent';
		} else {
		    $field_info = 'id_sch_proyek';
		    $field_info_b = 'tahap_kendali_id';
		    $field_info_c = 'bobot_parent';
		}

		$proyek_id = $this->session->userdata('proyek_id');
		$this->db->where('proyek_id',$proyek_id);
		$proyek = $this->db->get('simpro_tbl_proyek')->row();
		$tglawal = $proyek->mulai;
		$tglakhir = $proyek->berakhir;

		$thn_awl = date("Y",strtotime($tglawal));
		$thn_akr = date("Y",strtotime($tglakhir));

		$bulan_awal = date("n",strtotime($tglawal));
		$bulan_akhir = date("n",strtotime($tglakhir)) + 1;

		if ($thn_akr > $thn_awl) {
			$sel_thn = $thn_akr - $thn_awl;
			$bulan_akhir = $bulan_akhir + (12 * $sel_thn);
		}

		$week = ($bulan_akhir - $bulan_awal) * 4;

		$sql = $this->db->query("select * from $tbl_info where $field_info = '$proyek_id' order by minggu_ke DESC LIMIT 1");     	
		$data_minggu_akhir = $sql->row_array();

		if (!empty($data_minggu_akhir['minggu_ke']))
		$minggu_akhir = $data_minggu_akhir['minggu_ke'];
		else
		$minggu_akhir = 0;

		$no_minggu = 1;
			
		for ($w=1; $w <= ($week/4); $w++) {				

		 	for ($a=1; $a <=4 ; $a++) {

		 		if ($no_minggu <= $minggu_akhir){
			 		if (!isset($minggu_kumulatif))
						$minggu_kumulatif = $no_minggu;
					else
						$minggu_kumulatif .= ','.$no_minggu;

					if (substr($minggu_kumulatif, 0, -1) == ',')
						$minggu_kumulatif = substr($minggu_kumulatif, 0, -1);

					$sql = $this->db->query("select sum($field_info_c) as bobot_kumulatif from $tbl_info where $field_info = '$proyek_id' and minggu_ke IN($minggu_kumulatif)");     	
					$bobot_kumulatif = $sql->row_array();

					if (!empty($bobot_kumulatif['bobot_kumulatif']))
						$data = intval($bobot_kumulatif['bobot_kumulatif']);
					else
						$data= 0;
					
			 		$no_minggu++;
			 	} else {
			 		if (!empty($bobot_kumulatif['bobot_kumulatif']))
						$data = intval($bobot_kumulatif['bobot_kumulatif']);
					else
						$data= 0;
			 	}

			 	$result[] = $data;
		 	}	 	

		 }

		 return json_encode($result);

	}

	function get_line_week($info="")
	{
		if ($info == 'proyek') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent';
		} elseif ($info == 'alat') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_alat';
		} elseif ($info == 'bahan') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_bahan';
		} elseif ($info == 'person') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_person';	
		} elseif ($info == 'peralatan') {
			$tbl_info = 'simpro_tbl_guna_alat_parent';	
		}

		if ($info == 'peralatan') {
			$field_info = 'id_guna_alat';
			$field_info_b = 'id_analisa_asat';
			$field_info_c = 'jumlah_parent';
		} else {
		    $field_info = 'id_sch_proyek';
		    $field_info_b = 'tahap_kendali_id';
		    $field_info_c = 'bobot_parent';
		}

		$proyek_id = $this->session->userdata('proyek_id');
		$this->db->where('proyek_id',$proyek_id);
		$proyek = $this->db->get('simpro_tbl_proyek')->row();
		$tglawal = $proyek->mulai;
		$tglakhir = $proyek->berakhir;

		$thn_awl = date("Y",strtotime($tglawal));
		$thn_akr = date("Y",strtotime($tglakhir));

		$bulan_awal = date("n",strtotime($tglawal));
		$bulan_akhir = date("n",strtotime($tglakhir)) + 1;

		if ($thn_akr > $thn_awl) {
			$sel_thn = $thn_akr - $thn_awl;
			$bulan_akhir = $bulan_akhir + (12 * $sel_thn);
		}

		$week = ($bulan_akhir - $bulan_awal) * 4;

		$no_minggu = 1;
		$nox = 1;
			
		for ($w=1; $w <= $week; $w++) {	
			 	$result[] = 'Week '.$nox++;
		 }

		return json_encode($result);

	}
}