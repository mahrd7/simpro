<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Rbk extends MX_Controller {
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');
		$this->load->model('mdl_rbk');
		$this->load->model('mdl_rbk_analisa');	
		if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");		
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}

	function pilih_daftar_analisa()
	{	
		$this->load->view('pilih_daftar_analisa');
	}

	public function rab()
	{	
		$idproyek = $this->session->userdata('proyek_id');
		$data = array(
			'id_proyek'=>$idproyek,
			'data_proyek'=>$this->get_proyek_info($idproyek)
			);
		$this->load->view("rab", $data);
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

	public function editdata($tbl_info){
		$this->db->trans_begin();
		$aksi = 0;
		$tgl = date('m/d/Y');
		$id = $this->input->post('id');
		$user_id = $this->session->userdata('uid');
		$waktu = date('H:i:s');
		switch($tbl_info)
		{
			case 'daftar_item' :	
			$tbl_get = 'simpro_tbl_detail_material';
			$query = $this->db->query("select * from simpro_tbl_subbidang where subbidang_kode = '".$this->input->post('subbidang')."'");
			$query2 = $this->db->query("select * from simpro_tbl_user a inner join simpro_tbl_divisi b on a.divisi_update=b.divisi_id  where a.user_id = $user_id");
			$data = array(
				'detail_material_kode' => $this->input->post('kode'), 
				'detail_material_nama' => $this->input->post('nama'),
				'subbidang_id' => $query->row()->subbidang_id,
				'subbidang_kode' => $this->input->post('subbidang'),
				'detail_material_satuan' => $this->input->post('satuan'),
				'detail_material_harga' => $this->input->post('harga'),
				'user_update' => $user_id,
				'tgl_update' => $tgl,
				'ip_update' => $_SERVER['REMOTE_ADDR'],
				'divisi_update' => $query2->row()->divisi_id,
				'waktu_update' => $waktu,
				'detail_material_spesifikasi' => $this->input->post('spesifikasi'),
				'detail_material_propinsi' => $this->input->post('provinsi'),
			); 
			break;
		}
		if($aksi== 0)
			$this->mdl_rbk->editdata($tbl_get,$data,$id);
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else
		{
			$this->db->trans_commit();
		}
	}
	
	public function deletedata($tbl_info,$bln="",$thn=""){
		$proyek_id = $this->session->userdata('proyek_id');
		$aksi = 0;
		switch($tbl_info)
		{
			case 'pilih_rapa' : 
			$aksi=1;
			$this->db->query("delete from simpro_tbl_komposisi_kendali where proyek_id=$proyek_id and extract(year from tahap_tanggal_kendali)=$thn and extract(month from tahap_tanggal_kendali)=$bln;delete from simpro_tbl_induk_sd_kendali where proyek_id=$proyek_id and extract(year from tahap_tanggal_kendali)=$thn and extract(month from tahap_tanggal_kendali)=$bln;delete from simpro_tbl_tahap_kendali where proyek_id=$proyek_id and extract(year from tahap_tanggal_kendali)=$thn and extract(month from tahap_tanggal_kendali)=$bln;delete from simpro_tbl_approve where proyek_id=$proyek_id and extract(year from tahap_tanggal_kendali)=$thn and extract(month from tahap_tanggal_kendali)=$bln;delete from simpro_tbl_approve where proyek_id=$proyek_id and extract(year from tahap_tanggal_kendali)=$thn and extract(month from tahap_tanggal_kendali)=$bln and form_approve='RAB';");
			break;
			case 'daftar_item':
			$tbl_get="simpro_tbl_detail_material";
			$id = $this->input->post('id');
			break;
		}
		if($aksi==0)
			$this->mdl_rbk->deletedata($tbl_get,$id);
	}
	
	function rapi()
	{
		$this->load->view('rapi');
	}
	
	function daftar_item()
	{
		$this->load->view('daftar_item');
	}

	function kertas_kerja()
	{
	}
	
	function rapa($tgl_rab="")
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if(!isset($tgl_rab) || !$tgl_rab){
			echo "Access Forbidden";
		}else{
			$data['bln']= '';
			$data['thn']= '';
			$data['tgl_rab']= $tgl_rab;
			$this->load->view('rapa_2',$data);
		}
	}
	
	function pilih_rapa()
	{
		$this->load->view('pilih_rapa2');
	}

	public function insertdata($tbl_info){
		$this->db->trans_begin();
		$aksi = 0;
		$tgl = date('m/d/Y');
		$user_id = $this->session->userdata('uid');
		$waktu = date('H:i:s');
		$proyek_id = $this->session->userdata('proyek_id');

		switch($tbl_info)
		{
			case 'daftar_item' :			
			
			
			$tbl_get = 'simpro_tbl_detail_material';
			$query = $this->db->query("select * from simpro_tbl_subbidang where subbidang_kode = '".$this->input->post('subbidang')."'");
			$query2 = $this->db->query("select * from simpro_tbl_user a inner join simpro_tbl_divisi b on a.divisi_update=b.divisi_id  where a.user_id = $user_id");
			$data = array(
				'detail_material_kode' => $this->input->post('kode'), 
				'detail_material_nama' => $this->input->post('nama'),
				'subbidang_id' => $query->row()->subbidang_id,
				'subbidang_kode' => $this->input->post('subbidang'),
				'detail_material_satuan' => $this->input->post('satuan'),
				'detail_material_harga' => $this->input->post('harga'),
				'user_update' => $user_id,
				'tgl_update' => $tgl,
				'ip_update' => $_SERVER['REMOTE_ADDR'],
				'divisi_update' => $query2->row()->divisi_id,
				'waktu_update' => $waktu,
				'detail_material_spesifikasi' => $this->input->post('spesifikasi'),
			); 
			break;
			
		}
		if($aksi== 0)
			$this->mdl_rbk->insertdata($tbl_get,$data);
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else
		{
			$this->db->trans_commit();
		}
	}
	
	function getdata2($info,$bln="",$thn=""){
		$page= $_GET['page'];
		$start = $_GET['start'];
		$limit = $_GET['limit'];
		
		switch ($info) {
			case 'rapa_detail': 
			$tbl_info ='rapa_detail'; 
			break;
		}
		$this->mdl_rbk->getdata2($tbl_info,$start,$limit,$bln,$thn);
	}
	
	function getdata($info)
	{
		$page= $_GET['page'];
		$start = $_GET['start'];
		$limit = $_GET['limit'];
		
		switch ($info) {
			case 'rapa': 
			$tbl_info ='rap'; 
			break;
			case 'rapi': 
			$tbl_info ='rapi';
			break;
			case 'daftar_item': 
			$tbl_info ='simpro_tbl_detail_material';
			break;
			case 'skbdn': 
			$tbl_info ='simpro_tbl_detail_material';
			break;
		}
		$data = $this->mdl_rbk->getdata($tbl_info,$start,$limit);
		echo '{"data":'.json_encode($data).'}';
	}

	function rp_skbn()
	{
            $this->load->view('skbdnt01');
	}
	
	function surat_pernyataan()
	{
        $this->load->view('surat_pernyataan_home');
	}

	function surat_pernyataan_home()
	{
		$user_id = $this->session->userdata('uid');
		$data_user = $this->mdl_rbk->get_user($user_id);
		$tgl = date('j');
		$bln = $this->bulan(date('n'));
		$thn = date('Y');
		$data['date']= $tgl.' '.$bln.' '.$thn;
		$data['nama']=$data_user['fullname'];
		$data['jabatan']=$data_user['jabatan'];
		$data['alamat']=$data_user['alamat'];
        $this->load->view('surat_pernyataan',$data);
	}
	
	function get_data_skbdn()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 	
		$limit = $this->input->get('limit');
		$start = $this->input->get('start');
		$sort = $this->input->get('sort');
		if (!$limit || $limit == '' || $limit == null) {
			$limit = 0;
		}
		if (!$start || $start == '' || $start == null) {
			$start = 0;
		}
		if (!$sort || $sort == '' || $sort == null) {
			$sort = '';
		}
		$data_var = array(
			'proyek_id' => $proyek_id, 
			'limit' => $limit,
			'start' => $start,
			'sort' => $sort
		);		
		$data = $this->mdl_rbk->get_data_skbdn($data_var);
		echo $data;
	}

	function get_data_rrp()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$limit = $this->input->get('limit');
		$start = $this->input->get('start');
		$sort = $this->input->get('sort');
		if (!$limit || $limit == '' || $limit == null) {
			$limit = 0;
		}
		if (!$start || $start == '' || $start == null) {
			$start = 0;
		}
		if (!$sort || $sort == '' || $sort == null) {
			$sort = '';
		}
		$data_var = array(
			'proyek_id' => $proyek_id, 
			'limit' => $limit,
			'start' => $start,
			'sort' => $sort
		);		
		$data = $this->mdl_rbk->get_data_rrp($data_var);
		echo $data;
	}
	
	function get_data_checklist_dokumen()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$limit = $this->input->get('limit');
		$start = $this->input->get('start');
		$sort = $this->input->get('sort');
		if (!$limit || $limit == '' || $limit == null) {
			$limit = 0;
		}
		if (!$start || $start == '' || $start == null) {
			$start = 0;
		}
		if (!$sort || $sort == '' || $sort == null) {
			$sort = '';
		}
		$data_var = array(
			'proyek_id' => $proyek_id, 
			'limit' => $limit,
			'start' => $start,
			'sort' => $sort
		);		
		$data = $this->mdl_rbk->get_data_checklist_dokumen($data_var);
		echo $data;
	}
	
	function get_sumberdaya()
	{
		$sort = $this->input->get('sort');
		$cbosort= $this->input->get('cbosort');
		if (!$sort || $sort == '' || $sort == null) {
			$sort = '';
		}
		if (!$cbosort || $cbosort == '' || $cbosort == null) {
			$cbosort = '';
		}
		$data_var = array(
			'sort' => $sort,
			'cbosort' => $cbosort
		);
		$data = $this->mdl_rbk->get_sumberdaya($data_var);
		echo $data;
	}
	
	function insert($page="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		switch ($page) {
			case 'skbdn':
				$kode_material = $this->input->post('id');
				$tgl = date('Y-m-d');

				$data = array(
					'proyek_id' => $proyek_id,
					'tanggal' => $tgl, 
					'detail_material_id' => $kode_material, 
					'volume' => '1', 
					'harga_satuan' => '0', 
				);
				$this->mdl_rbk->insert($page,$data);
			break;
			case 'rrp':
				$kode_material = $this->input->post('id');
				$tgl = date('Y-m-d');

				$data = array(
					'proyek_id' => $proyek_id,
					'tanggal' => $tgl, 
					'detail_material_id' => $kode_material, 
					'volume' => '1', 
					'harga_satuan' => '0', 
				);
				$this->mdl_rbk->insert($page,$data);
			break;
			case 'checklist_dokumen':
				$uraian_pekerjaan = $this->input->post('uraian_pekerjaan');
				$nama_suplier = $this->input->post('nama_suplier');
				$satuan = $this->input->post('satuan');
				$harga_satuan = $this->input->post('harga_satuan');
				$spen = $this->input->post('spen');
				$rekan = $this->input->post('rekan');
				$keterangan = $this->input->post('keterangan');
				$tgl = date('Y-m-d');

				$data = array(
					'proyek_id' => $proyek_id,
					'tanggal' => $tgl,
					'suplier' => $nama_suplier,
					'harga_satuan' => $harga_satuan,
					'status_penawaran' => $spen,
					'keterangan' => $keterangan,
					'rekan_usul' => $rekan,
					'satuan_id' => $satuan,
					'uraian_pekerjaan' => $uraian_pekerjaan
				);
				$this->mdl_rbk->insert($page,$data);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}
	
	function delete($page="")
	{
		switch ($page) {
			case 'skbdn':
				$id = $this->input->post('id');
				$data = array(
					'id' => $id
				);
				$this->mdl_rbk->delete($page,$data);
			break;
			case 'rrp':
				$id = $this->input->post('id');
				$data = array(
					'id' => $id
				);
				$this->mdl_rbk->delete($page,$data);
			break;
			case 'checklist_dokumen':
				$id = $this->input->post('id');
				$data = array(
					'id' => $id
				);
				$this->mdl_rbk->delete($page,$data);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function edit($page="")
	{
		switch ($page) {
			case 'skbdn':
				$id = $this->input->post('id');
				$volume = $this->input->post('volume');
				$harga_satuan = $this->input->post('harga_satuan');
				$data = array(
					'id' => $id,
					'volume' => $volume,
					'harga_satuan' => $harga_satuan
				);
				$this->mdl_rbk->edit($page,$data);
			break;
			case 'rrp':
				$id = $this->input->post('id');
				$volume = $this->input->post('volume');
				$harga_satuan = $this->input->post('harga_satuan');
				$data = array(
					'id' => $id,
					'volume' => $volume,
					'harga_satuan' => $harga_satuan
				);
				$this->mdl_rbk->edit($page,$data);
			break;
			case 'checklist_dokumen':
				$id = $this->input->post('id');
				$uraian_pekerjaan = $this->input->post('uraian_pekerjaan');
				$nama_suplier = $this->input->post('nama_suplier');
				$satuan = $this->input->post('satuan');
				$harga_satuan = $this->input->post('harga_satuan');
				$spen = $this->input->post('spen');
				$rekan = $this->input->post('rekan');
				$keterangan = $this->input->post('keterangan');
				$tgl = date('Y-m-d');

				$data = array(
					'id' => $id,
					'suplier' => $nama_suplier,
					'harga_satuan' => $harga_satuan,
					'status_penawaran' => $spen,
					'keterangan' => $keterangan,
					'rekan_usul' => $rekan,
					'satuan_id' => $satuan,
					'uraian_pekerjaan' => $uraian_pekerjaan
				);
				$this->mdl_rbk->edit($page,$data);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
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
	

	function get_subbidang()
	{
		$data = $this->mdl_rbk->get_subbidang();
		echo $data;
	}
	
	
	function rrpp()
	{
           $this->load->view('fmalatt01');   
	}
	
	function get_evaluasi_rab_rat_rapi()
	{
		$id_proyek = $this->session->userdata('proyek_id');
		$sql = sprintf("
			SELECT 
				kode_tree, tree_item, 
				tree_satuan AS satuan, 
				COALESCE(volume,0) AS rap_volume,
				(kode_tree ||  '. ' || tree_item) AS item_pekerjaan
			FROM simpro_rap_item_tree 
			WHERE id_proyek = %d
			ORDER BY kode_tree ASC
		", $id_proyek);
		$data = $this->db->query($sql)->result_array();
		echo json_encode(array('total'=>count($data), 'data'=>$data));
	}
	
	function evaluasi_rab_rat_rapi()
	{
		$id = $this->session->userdata('proyek_id');
		$data = array('id_proyek' => $id);
		$this->load->view('eva_perbandingan', $data);
	}
	
	function ceklis_dok_penawaran()
	{
		$this->load->view('checklist_dokumen');
	}

	public function getsubbidang(){
		$data = $this->mdl_rbk->getsubbidang();
		echo '{"data":'.json_encode($data).'}';
	}

	public function getsubbidangkode(){
		$data = $this->mdl_rbk->getsubbidangkode();
		echo '{"data":'.json_encode($data).'}';
	}
	public function getprovinsi(){
		$data = $this->mdl_rbk->getprovinsi();
		echo '{"data":'.json_encode($data).'}';
	}

	public function gettanggalcombo(){		
		$no_spk = $_GET['no_spk'];
		$data = $this->mdl_pengendalian->gettanggalcombo($no_spk);
		echo '{"data":'.json_encode($data).'}';
	}

	function getedithargasatuan(){
		$no_spk=$_GET['no_spk'];
		$tgl_rab=$_GET['tgl_rab'];
		$data = $this->mdl_rbk->getedithargasatuan($no_spk,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_proyek_daftar_analisa()
	{
		$data = $this->mdl_rbk->get_proyek_daftar_analisa();
		echo '{"data":'.json_encode($data).'}';
	}

	function get_tanggal_daftar_analisa()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		if ($this->input->get('proyek')) {
			$proyek_id=$this->input->get('proyek');
		} else {
			$proyek_id="";
		}
		$data = $this->mdl_rbk->get_tanggal_daftar_analisa($proyek_id);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_data_daftar_analisa()
	{
		$proyek_id = $this->input->get('proyek'); 
		$tgl_rab = $this->input->get('tgl_rab');
		$data = $this->mdl_rbk->get_data_daftar_analisa($proyek_id,$tgl_rab);
		echo $data;
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
		$thns = date('Y');
		for ($i=2000; $i <= $thns; $i++) { 
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
	
	function rapa_2($tgl_rab="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		if(!isset($tgl_rab) || !$tgl_rab){
			echo "Access Forbidden";
		}else{
			$data['bln']= '';
			$data['thn']= '';
			$data['tgl_rab']= $tgl_rab;
			$this->load->view('rapa_2',$data);
		}
	}

	function cek_data_induk_togo()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$kode = $this->input->get('id');
		$tgl_rab = $this->input->get('tgl_rab');

		$cek_data_induk_togo = $this->mdl_rbk->cek_data_induk_togo($kode,$proyek_id,$tgl_rab);
	
		echo $cek_data_induk_togo;
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
		$data = $this->mdl_rbk->get_data_analisa($limit,$offset,$text,$cbo);
		echo $data;
	}

	function get_sub_ctg()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$kode = $this->input->get('kode');
		$tgl_rab = $this->input->get('tgl_rab');
		$data = $this->mdl_rbk->get_sub_ctg($proyek_id,$kode,$tgl_rab);		
		echo '{"data":'.json_encode($data).'}';
	}

	function getdata_sub_ctg()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		$tgl_rab = $this->input->get('tgl_rab');
		$kode_kendali = $this->input->get('kode_kendali');
		$data = $this->mdl_rbk->getdata_sub_ctg($proyek_id,$tgl_rab,$kode_kendali);
		echo $data;
	}

	function get_sub_kode()
	{
		$kode = $this->input->get('kode');
		$proyek_id = $this->session->userdata('proyek_id'); 
		$info = $this->input->get('info');
		$tgl_rab= $this->input->get('tgl_rab');

		switch ($info) {
			case 'rapa':
				$tbl_info='rapa';
			break;
		}
		$data = $this->mdl_rbk->get_sub_kode($proyek_id,$tbl_info,$kode,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_kode_ctg()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$tgl_rab= $this->input->get('tgl_rab');
		$data = $this->mdl_rbk->get_kode_ctg($proyek_id,$tgl_rab);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_data_cost_to_go()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$tgl_rab = $this->input->get('tgl_rab');
		$data = $this->mdl_rbk->get_data_cost_to_go($proyek_id,$tgl_rab);
		echo $data;
	}

	function getlistsatuan(){
		$data = $this->mdl_rbk->getlistsatuan();
		echo '{"data":'.json_encode($data).'}';
	}

	public function getdivisicombo(){
		$data = $this->mdl_rbk->getdivisicombo();
		echo '{"data":'.json_encode($data).'}';
	}

	public function getproyekcombo(){
		$divisi_kode = $_GET['divisi_kode'];
		// echo $divisi_kode;
		$data = $this->mdl_rbk->getproyekcombo($divisi_kode);
		echo '{"data":'.json_encode($data).'}';
	}

	function getdata_edit_hs_ctg()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 	
		$tgl_rab = $this->input->get('tgl_rab');
		$data = $this->mdl_rbk->getdata_edit_hs_ctg($proyek_id, $tgl_rab);
		echo $data;
	}

	function update_ctg()
	{
		$id = $this->input->post('id');
		$satuan_awal = $this->input->post('tahap_satuan_kendali');
		if (preg_match('/^[0-9]+$/', $satuan_awal)) {
			$satuan = $satuan_awal;
		} else {
			$satuan = $this->mdl_rbk->get_id_satuan($satuan_awal);	
		}

		$data = array(
            'tahap_nama_kendali' => $this->input->post('tahap_nama_kendali'),
            'tahap_satuan_kendali' => $satuan,
            'tahap_volume_kendali' => $this->input->post('tahap_volume_kendali'),
            'tahap_harga_satuan_kendali' => $this->input->post('tahap_harga_satuan_kendali')
		);

		$this->mdl_rbk->update_ctg($id,$data);
	}

	function insert_ctg($tgl_rab="")
	{
		if ($tgl_rab=="") {
			echo "Access Forbidden";
		} else {
			$proyek_id = $this->session->userdata('proyek_id'); 
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
			$this->mdl_rbk->insert_ctg($data);
		}
	}

	function insert_sub_ctg($kode,$tgl_rab)
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');

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

		$this->mdl_rbk->insert_sub_ctg($data);
	}

	function update_analisa_ctg()
	{
		$id = $this->input->post('id_komposisi');
		$data = array(
			'komposisi_harga_satuan_kendali' => $this->input->post('harga'),
			'komposisi_koefisien_kendali' => $this->input->post('koefisien')
		);
		$this->mdl_rbk->update_analisa_ctg($id,$data);
	}

	function insert_induk_togo_induk()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$kode = $this->input->post('id');
		$tgl_rab = $this->input->post('tgl_rab');
		$data_induk = array(
			'kode_komposisi' => 'X',
			'volume_komposisi' => 1,
			'koefisien_komposisi' => 1,
			'proyek_id' => $proyek_id,
			'kode_tahap_kendali' => $kode,
			'total_harga_satuan' => 1,
			'nama_komposisi' => 'ANALISA',
			'komposisi_satuan' => '',
			'harga_satuan' => 1,
			'tahap_tanggal_kendali' => $tgl_rab
		);

		$cek_data_induk_togo = $this->mdl_rbk->cek_data_induk_togo($kode,$proyek_id,$tgl_rab);

		if ($cek_data_induk_togo == 'kosong') {
			$this->mdl_rbk->insert_induk_togo_induk($data_induk);
		}		
	}

	function insert_induk_komposisi_togo()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
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

		$cek_data_komposisi_togo = $this->mdl_rbk->cek_data_komposisi_togo($id_material,$kode,$proyek_id,$tgl_rab);
		// echo $cek_data_komposisi_togo ;
		if ($cek_data_komposisi_togo == 'kosong') {
			$this->mdl_rbk->insert_induk_komposisi_togo($data_komposisi);
		}
	}

	function update_hs_ctg()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$tgl_rab = $this->input->post('tgl_rab');
		$id = $this->input->post('kode');
		$data = array(
			'komposisi_harga_satuan_kendali' => $this->input->post('harga'),
			'keterangan' => $this->input->post('keterangan')
		);
		$this->mdl_rbk->update_hs_ctg($proyek_id,$tgl_rab,$id,$data);
	}

	function pilih_rapa2()
	{
		$this->load->view('pilih_rapa2');
	}

	function get_tanggal_ctg()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$data = $this->mdl_rbk->get_tanggal_ctg($proyek_id);
		echo '{"data":'.json_encode($data).'}';
	}

	function get_hs_pwd()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$data = $this->mdl_rbk->get_hs_pwd($proyek_id);
		echo $data;
	}

	function update_hs_ctg_pwd()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$id = $this->input->post('data67');
		$data = array(
			'username' => $this->input->post('data12'), 
			'password' => $this->input->post('data90')
		);

		$this->mdl_rbk->update_hs_ctg_pwd($id, $data, $proyek_id);
	}

	function cek_pwd_hs()
	{
		$uname = $this->input->get('datasdm');
		$pwd = $this->input->get('datahs');
		$data = $this->mdl_rbk->cek_pwd_hs($uname,$pwd);
		echo $data;
	}

	
	function get_data_proyek()
	{
		$proyek = $this->mdl_rbk->pilih_proyek($this->session->userdata('divisi_id'));
		if($proyek['total'] > 0)
		{
			$data = array('success'=>true, 'data'=>$proyek['data'], 'total'=>$proyek['total']);
			$this->_out($data);
		} else json_encode(array('success'=>true, 'data'=>NULL, 'total'=>0));
	}

	function get_data_proyek_rap()
	{
		$proyek = $this->mdl_rbk->pilih_proyek_rap($this->session->userdata('proyek_id'),$this->session->userdata('divisi_id'));
		if($proyek['total'] > 0)
		{
			$data = array('success'=>true, 'data'=>$proyek['data'], 'total'=>$proyek['total']);
			$this->_out($data);
		} else json_encode(array('success'=>true, 'data'=>NULL, 'total'=>0));
	}
	
	function edit_analisa_rap($idproyek)
	{		
		$data = array(
			'id_proyek'=>$this->session->userdata('proyek_id'),
			'data_proyek'=>$this->get_proyek_info($idproyek)
			);
		$this->load->view("rap_analisa",$data);
	}
	
	function rap_rapa()
	{		
		$this->load->view("rap_rapa");
	}

	function get_proyek_info($id)
	{
		$sql = sprintf("SELECT * FROM simpro_tbl_proyek WHERE proyek_id = '%d'", $id);	
		$data = $this->db->query($sql)->row_array();
		if(isset($data['proyek'])) return $data;
	}

	function copy_from_rab()
	{
		$idproyek = $this->session->userdata('proyek_id');

		$sql_delete_analisa = $this->db->delete('simpro_rap_analisa_item_apek',array('id_proyek' => $idproyek));
		$sql_delete = $this->db->delete('simpro_rap_item_tree',array('id_proyek' => $idproyek));

		if ($sql_delete_analisa && $sql_delete) {
			$sql_insert = sprintf("
				INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume,tree_parent_kode)
					SELECT
					%d as id_proyek,
					case when right(('1.' || tahap_kode_kendali),1) = '.'
					then left(('1.' || tahap_kode_kendali),(length('1.' || tahap_kode_kendali)-1))
					else '1.' || tahap_kode_kendali
					end,
					tahap_nama_kendali,
					tahap_satuan_kendali,								
					tahap_volume_kendali,
					case when right(('1.' || tahap_kode_induk_kendali),1) = '.'
					then left(('1.' || tahap_kode_induk_kendali),(length('1.' || tahap_kode_induk_kendali)-1))
					else '1.' || tahap_kode_induk_kendali
					end
					FROM simpro_tbl_input_kontrak
					where proyek_id = $idproyek
			", $idproyek);
			$sql_insert_bl = sprintf("
				INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume,tree_parent_kode)
				values (
					%d,
					'1',
					'BIAYA LANGSUNG',
					'Ls',								
					1,
					null
					)
			", $idproyek);
			$sql_insert_btl = sprintf("
				INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume,tree_parent_kode)
				values (
					%d,
					'2',
					'BIAYA TIDAK LANGSUNG',
					'Ls',								
					1,
					null
					)
			", $idproyek);
			$this->db->query($sql_insert);
			$this->db->query($sql_insert_bl);
			$this->db->query($sql_insert_btl);

			$this->update_tree_parent_id_rab_copy($idproyek);
		}

		$data = array(
			'id_proyek'=>$idproyek,
			'data_proyek'=>$this->get_proyek_info($idproyek)
			);
		$this->load->view("rap", $data);

	}
	
	function edit_rap($idproyek)
	{
		$cek_isi_data = $this->db->query("select * from simpro_rap_item_tree where id_proyek = $idproyek");
		$cek_isi_data_rab = $this->db->query("select * from simpro_tbl_input_kontrak where proyek_id = $idproyek");

		if ($cek_isi_data->num_rows() <= 0 && $cek_isi_data_rab->num_rows() > 0) {
			$sql_insert = sprintf("
				INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume,tree_parent_kode)
					SELECT
					%d as id_proyek,
					case when right(('1.' || tahap_kode_kendali),1) = '.'
					then left(('1.' || tahap_kode_kendali),(length('1.' || tahap_kode_kendali)-1))
					else '1.' || tahap_kode_kendali
					end,
					tahap_nama_kendali,
					tahap_satuan_kendali,								
					tahap_volume_kendali,
					case when right(('1.' || tahap_kode_induk_kendali),1) = '.'
					then left(('1.' || tahap_kode_induk_kendali),(length('1.' || tahap_kode_induk_kendali)-1))
					else '1.' || tahap_kode_induk_kendali
					end
					FROM simpro_tbl_input_kontrak
					where proyek_id = $idproyek
			", $idproyek);
			$sql_insert_bl = sprintf("
				INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume,tree_parent_kode)
				values (
					%d,
					'1',
					'BIAYA LANGSUNG',
					'Ls',								
					1,
					null
					)
			", $idproyek);
			$sql_insert_btl = sprintf("
				INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume,tree_parent_kode)
				values (
					%d,
					'2',
					'BIAYA TIDAK LANGSUNG',
					'Ls',								
					1,
					null
					)
			", $idproyek);
			$this->db->query($sql_insert);
			$this->db->query($sql_insert_bl);
			$this->db->query($sql_insert_btl);

			$this->update_tree_parent_id_rab_copy($idproyek);
		}
		$data = array(
			'id_proyek'=>$idproyek,
			'data_proyek'=>$this->get_proyek_info($idproyek)
			);
		$this->load->view("rap", $data);
	}

	function update_tree_parent_id_rab_copy($idtender)
	{
		$sql = "
			update simpro_rap_item_tree
			set tree_parent_id = x.id_parent
			from(
			SELECT 
			case when (select rap_item_tree from simpro_rap_item_tree where kode_tree = a.tree_parent_kode and id_proyek = a.id_proyek) isnull
			then 0
			else (select rap_item_tree from simpro_rap_item_tree where kode_tree = a.tree_parent_kode and id_proyek = a.id_proyek)
			end as id_parent,
			a.rap_item_tree, a.kode_tree 
			FROM simpro_rap_item_tree a
			WHERE a.id_proyek = $idtender) x
			where id_proyek = $idtender and simpro_rap_item_tree.rap_item_tree = x.rap_item_tree	
			";
		$this->db->query($sql);
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
	
	function dump($data)
	{
		print('<pre>');
		print_r($data);
		print('</pre>');
	}

	function upload_rap_item()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv|txt';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);					
		$this->db->query("TRUNCATE TABLE simpro_tmp_import_rap");
		
		try {
			if(!$this->upload->do_upload('upload_analisa'))
			{
				throw new Exception(sprintf('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: csv|txt.\nErrors: %s', $this->upload->display_errors()));
			} else
			{
				$data = $this->upload->data();
				# CSV HEADER
				$sql = sprintf("COPY simpro_tmp_import_rap FROM '%s' DELIMITER ',' ENCODING 'LATIN1'", realpath("./uploads/".$data['file_name']));
				if($this->db->query($sql))
				{
					$idpro = $this->input->post('id_proyek');
					$qdel = sprintf("DELETE FROM simpro_rap_item_tree WHERE id_proyek = '%d'",$this->input->post('id_proyek'));
					$this->db->query($qdel);					
					
					if ($this->db->query("select * from simpro_tmp_import_rap where kode_tree = '1' and trim(lower(uraian)) = trim(lower('biaya langsung'))")->num_rows() < 1) {
						
						$sql_insert = sprintf("
							INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume)
							(
								SELECT
									%d as id_proyek,
									'1.' || kode_tree,
									uraian,
									satuan,								
									volume::numeric
								FROM simpro_tmp_import_rap
							)
						", $idpro);
						$sql_insert_bl = sprintf("
							INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume)
							values (
									%d,
									'1',
									'BIAYA LANGSUNG',
									'Ls',								
									1
							)
						", $idpro);
						$sql_insert_btl = sprintf("
							INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume)
							values (
									%d,
									'2',
									'BIAYA TIDAK LANGSUNG',
									'Ls',								
									1
							)
						", $idpro);
						if($this->db->query($sql_insert_bl) and $this->db->query($sql_insert_btl) and $this->db->query($sql_insert)) 
						{
							$this->update_tree_parent_id($idpro);
							$this->set_satuan_induk($idpro);
							$this->db->query(sprintf("DELETE FROM simpro_rap_analisa_item_apek WHERE id_proyek = %d", $idpro));
							$this->db->query("TRUNCATE TABLE simpro_tmp_import_rap");
							echo json_encode(
								array(	"success"=>true, 
										"message"=>"Data berhasil diupload.", 
										"file" => $data['file_name'])
							);
						} else echo json_encode(array("success"=>true, 
								"message"=>"Data GAGAL diupload.", 
								"file" => $data['file_name']));
					} else {
						$sql_insert = sprintf("
							INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume)
							(
								SELECT
									%d as id_proyek,
									kode_tree,
									uraian,
									satuan,							
									volume::numeric
								FROM simpro_tmp_import_rap
							)
						", $idpro);
						
						if ($this->db->query("select * from simpro_tmp_import_rap where kode_tree = '2' and trim(lower(uraian)) = trim(lower('biaya tidak langsung'))")->num_rows() < 1){
							$sql_insert_btl = sprintf("
								INSERT INTO simpro_rap_item_tree(id_proyek,kode_tree,tree_item,tree_satuan,volume)
								values (
										%d,
										'2',
										'BIAYA TIDAK LANGSUNG',
										'Ls',								
										1
								)
							", $idpro);
							$this->db->query($sql_insert_btl);
						}

						if($this->db->query($sql_insert)) 
						{
							$this->update_tree_parent_id($idpro);
							$this->set_satuan_induk($idpro);
							$this->db->query(sprintf("DELETE FROM simpro_rap_analisa_item_apek WHERE id_proyek = %d", $idpro));
							$this->db->query("TRUNCATE TABLE simpro_tmp_import_rap");
							echo json_encode(
								array(	"success"=>true, 
										"message"=>"Data berhasil diupload.", 
										"file" => $data['file_name'])
							);
						} else echo json_encode(array("success"=>true, 
								"message"=>"Data GAGAL diupload.", 
								"file" => $data['file_name']));
					}
				} else
				{
					echo json_encode(
						array(	"success"=>true, 
								"message"=>"Data GAGAL diupload.", 
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

	function upload_rap_item_rab()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv|txt';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);					
		$this->db->query("TRUNCATE TABLE simpro_tmp_import_rap");
		
		try {
			if(!$this->upload->do_upload('upload_analisa'))
			{
				throw new Exception(sprintf('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: csv|txt.\nErrors: %s', $this->upload->display_errors()));
			} else
			{
				$data = $this->upload->data();
				# CSV HEADER
				$sql = sprintf("COPY simpro_tmp_import_rap FROM '%s' DELIMITER ',' ENCODING 'LATIN1'", realpath("./uploads/".$data['file_name']));
				if($this->db->query($sql))
				{
					$idpro = $this->input->post('id_proyek');
					$qdel = sprintf("DELETE FROM simpro_tbl_input_kontrak WHERE proyek_id = '%d'",$this->input->post('id_proyek'));
					$this->db->query($qdel);					
					
					
					$sql_insert = sprintf("
						INSERT INTO simpro_tbl_input_kontrak(proyek_id,tahap_kode_kendali,tahap_nama_kendali,tahap_satuan_kendali,tahap_volume_kendali)
						(
							SELECT
							%d as id_proyek,
							kode_tree,
							uraian,
							satuan,									
							volume::numeric
							FROM simpro_tmp_import_rap
							)
					", $idpro);

					if($this->db->query($sql_insert)) 
					{
						$this->update_tree_parent_id_rab($idpro);
						$this->set_satuan_induk($idpro);
						$this->db->query("TRUNCATE TABLE simpro_tmp_import_rap");
						echo json_encode(
							array(	"success"=>true, 
								"message"=>"Data berhasil diupload.", 
								"file" => $data['file_name'])
							);
					} else echo json_encode(array("success"=>true, 
						"message"=>"Data GAGAL diupload.", 
						"file" => $data['file_name']));				
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

	function set_satuan_induk($id_proyek)
	{
		$sql_set_satuan_induk = "
		update simpro_rap_item_tree set
		tree_satuan = 'Ls',
		volume = 1
		from (
		select * from (select
		a.*,
		(SELECT 
		count('a') as count
		FROM simpro_rap_item_tree
		WHERE id_proyek = a.id_proyek
		and tree_parent_id = a.rap_item_tree) as count
		from
		simpro_rap_item_tree a
		where a.id_proyek = $id_proyek) n
		where n.count != 0
		) x
		where simpro_rap_item_tree.rap_item_tree = x.rap_item_tree
		";

		$this->db->query($sql_set_satuan_induk);
	}

	function update_tree_parent_id($idtender)
	{
		$sql = sprintf("
			SELECT 
				rap_item_tree, kode_tree 
			FROM simpro_rap_item_tree
			WHERE id_proyek = '%d'
			ORDER BY rap_item_tree ASC		
			", $idtender);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata > 0)
		{
			$data = $rs->result_array();
			foreach($data as $k=>$v)
			{
				$kd_tree = $v['kode_tree'];
				$kode = explode(".",$kd_tree);
				if(is_array($kode))
				{
					$parent_kode = NULL;
					$parent_arr = array();
					for($i=0;$i < count($kode) - 1; $i++)
					{
						$parent_arr[] = $kode[$i];
					}
					$parent_kode = implode(".",$parent_arr);
					$sql = sprintf("
						SELECT 
							rap_item_tree, kode_tree 
						FROM simpro_rap_item_tree
						WHERE id_proyek = '%d'
						AND kode_tree = '%s'
						ORDER BY rap_item_tree ASC		
					", $idtender, $parent_kode);					
					$get_id = $this->db->query($sql)->row_array();
					$parent_id = isset($get_id['rap_item_tree']) ? $get_id['rap_item_tree'] : 0;
					# update db
					$update = sprintf("
							UPDATE simpro_rap_item_tree 
							SET tree_parent_kode = '%s', 
							tree_parent_id='%d' 
							WHERE id_proyek='%d' 
							AND kode_tree='%s'", 
						$parent_kode, $parent_id, $idtender, $kd_tree);
					$this->db->query($update);
				}
			}
		}
	}

	function update_tree_parent_id_rab($idtender)
	{
		$sql = sprintf("
			SELECT 
				input_kontrak_id, tahap_kode_kendali 
			FROM simpro_tbl_input_kontrak
			WHERE proyek_id = '%d'
			ORDER BY input_kontrak_id ASC		
			", $idtender);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata > 0)
		{
			$data = $rs->result_array();
			foreach($data as $k=>$v)
			{
				$kd_tree = $v['tahap_kode_kendali'];
				$kode = explode(".",$kd_tree);
				if(is_array($kode))
				{
					$parent_kode = NULL;
					$parent_arr = array();
					for($i=0;$i < count($kode) - 1; $i++)
					{
						$parent_arr[] = $kode[$i];
					}
					$parent_kode = implode(".",$parent_arr);
					$sql = sprintf("
						SELECT 
							input_kontrak_id, tahap_kode_kendali 
						FROM simpro_tbl_input_kontrak
						WHERE proyek_id = '%d'
						AND tahap_kode_kendali = '%s'
						ORDER BY input_kontrak_id ASC		
					", $idtender, $parent_kode);					
					$get_id = $this->db->query($sql)->row_array();
					$parent_id = isset($get_id['input_kontrak_id']) ? $get_id['input_kontrak_id'] : 0;
					# update db
					$update = sprintf("
							UPDATE simpro_tbl_input_kontrak 
							SET tahap_kode_induk_kendali = '%s', 
							tree_parent_id='%d' 
							WHERE proyek_id='%d' 
							AND tahap_kode_kendali='%s'", 
						$parent_kode, $parent_id, $idtender, $kd_tree);
					$this->db->query($update);
				}
			}
		}
	}

	function excel($page="")
	{
		$idpro = $this->session->userdata('proyek_id'); 

		if ($page=="") {
			echo "Access Forbidden..";
		} else {

			$nama_proyek = $this->db->query("select proyek from simpro_tbl_proyek where proyek_id = $idpro")
			->row()->proyek;

			$date = date('Y-m-d');

			$nama_proyek_u = substr(str_replace(' ', '_', $nama_proyek), 0, 12);
			// var_dump($nama_proyek_u);

			//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle(substr($page, 0,7).'_'.$nama_proyek_u.'_'.$date);

			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', strtoupper($page));
			$this->excel->getActiveSheet()->setCellValue('A2', strtoupper($nama_proyek).' ('.$date.')');
			$this->excel->getActiveSheet()->setCellValue('A4', 'Kode');
			$this->excel->getActiveSheet()->setCellValue('B4', 'Kode Analisa');
			$this->excel->getActiveSheet()->setCellValue('C4', 'Uraian');
			$this->excel->getActiveSheet()->setCellValue('D4', 'Satuan');
			$this->excel->getActiveSheet()->setCellValue('E4', 'Volume');
			$this->excel->getActiveSheet()->setCellValue('F4', 'Harga');
			$this->excel->getActiveSheet()->setCellValue('G4', 'Jumlah');

			switch ($page) {
				case 'rap':					

					$this->db->query("TRUNCATE TABLE simpro_tmp_print_pekerjaan");

					$q_rapa = $this->get_data_print($idpro);

					$x = 5;

					$tot = 0;
					
					if ($q_rapa) {
						foreach ($q_rapa as $row) {
							$kode_tree = $row->kode_tree;
							$kode_analisa = $row->kode_analisa;
							$tree_item = $row->tree_item;
							$tree_satuan = $row->tree_satuan;
							$volume = $row->volume;
							$hrg = $row->hrg;
							$sub = $row->sub;

							$this->excel->getActiveSheet()->setCellValueExplicit('A'.$x, $kode_tree, PHPExcel_Cell_DataType::TYPE_STRING);
							$this->excel->getActiveSheet()->setCellValue('B'.$x, $kode_analisa);
							$this->excel->getActiveSheet()->setCellValue('C'.$x, $tree_item);
							$this->excel->getActiveSheet()->setCellValue('D'.$x, $tree_satuan);
							$this->excel->getActiveSheet()->setCellValue('E'.$x, $volume);
							if (count(explode(".", $kode_tree)) == 1) {
								$this->excel->getActiveSheet()->getStyle('F'.$x)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('G'.$x)->getFont()->setBold(true);

								$tot = $tot + $sub;
							}
							$this->excel->getActiveSheet()->setCellValue('F'.$x, $hrg);
							$this->excel->getActiveSheet()->setCellValue('G'.$x, $sub);

							$x++;
						}
					} 
				break;
				default:
					echo "Gagal";
				break;
			}


			$this->excel->getActiveSheet()->setCellValue('A'.$x, 'Total');
			$this->excel->getActiveSheet()->setCellValue('G'.$x, $tot);

			$this->excel->getActiveSheet()->getStyle('A'.$x.':G'.$x)->getFont()->setBold(true);

			$styleArray = array(
			'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			),
			  'font'  => array(
		        'size'  => 7.5,
		        'name'  => 'verdana'
		    )
			);

			$styleArray1 = array(
			  'font'  => array(
		        'bold'  => true,
		        'uppercase' => true,
		        'size'  => 11,
		        'name'  => 'Calibri'
		    )
			);

			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(34);

			$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

			$this->excel->getActiveSheet()->getStyle('E1:G'.$this->excel->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00'); 

			$this->excel->getActiveSheet()->getStyle('A4:G'.$x)->applyFromArray($styleArray);
			unset($styleArray);

			$this->excel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleArray1);
			unset($styleArray1);

			$this->excel->getActiveSheet()->getStyle('C1:C'.$this->excel->getActiveSheet()->getHighestRow())
    		->getAlignment()->setWrapText(true);

			$this->excel->getActiveSheet()->mergeCells('A1:G1');
			$this->excel->getActiveSheet()->mergeCells('A2:G2');
			$this->excel->getActiveSheet()->mergeCells('A'.$x.':F'.$x);

			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$this->db->query("TRUNCATE TABLE simpro_tmp_print_pekerjaan");

			$filename=$page.'_'.$nama_proyek.'_'.$date.'.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			             
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');

			
		}
	}

	function get_data_print($idpro)
	{
		$arr = $this->tree($idpro, $depth=0);

		$this->get_array($arr);

		$q_data = $this->db->query('select * from simpro_tmp_print_pekerjaan order by id_print');

		if ($q_data->result()) {
			return $q_data->result();
		}
	} 

	function get_array($value='')
	{
		if (is_array($value)) {
			foreach ($value as $key => $row) {
				
				$data = array(
					'kode_tree' => $row['kode_tree'],
					'kode_analisa'  => $row['kode_analisa'],
					'tree_item' => trim($row['tree_item']),
					'tree_satuan' => $row['tree_satuan'],
					'volume' => $row['volume'],
					'hrg' => $row['harga'],
					'sub' => $row['subtotal']
				);

				$this->db->insert('simpro_tmp_print_pekerjaan',$data);
				// fwrite($handle, $dat);			

				if (isset($row['children'])) {
					$this->get_array($row['children']);
				}
			}
		}
	}
	
	function tree($idpro, $depth=0)
	{
		$result=array();
		$temp=array();
		$temp = $this->mdl_rbk_analisa->get_tree_item($idpro, $depth)->result();
		if(count($temp))
		{			
			$i = 0;
			$n = 1;
			foreach($temp as $row){
				$kode_tree = $row->kode_tree;

				if ($row->tree_parent_id) {
					$tree_kode_parent = $this->db->query("select kode_tree from simpro_rap_item_tree where rap_item_tree = $row->tree_parent_id")->row()->kode_tree;
				} else {
					$tree_kode_parent = "";
				}

				if ($row->tree_parent_kode.'.'.$n <> $row->kode_tree && strlen($row->tree_parent_kode) <> 0 || $tree_kode_parent <> $row->tree_parent_kode) {
					$kode_tree = $tree_kode_parent.'.'.$n;

					// $data_del = "delete from simpro_rap_item_tree where kode_tree = '$row->kode_tree' and id_proyek = $idpro";
					// $this->db->query($data_del);

					$data_up_ap = "update simpro_rap_analisa_item_apek set kode_tree = '$kode_tree' 
					where kode_tree = '$row->kode_tree' and id_proyek = $idpro";
					$this->db->query($data_up_ap);

					$data_up = "update simpro_rap_item_tree set kode_tree = '$kode_tree',
					tree_parent_kode = '$tree_kode_parent' 
					where rap_item_tree = $row->rap_item_tree";
					$this->db->query($data_up);
				}

				//$temp_harga = $this->mdl_rbk_analisa->get_tree_item_harga($idpro, $row->rap_item_tree)->row_array();
				$data[] = array(
					'rap_item_tree' => $row->rap_item_tree,
					'id_proyek' => $row->id_proyek,
					'id_satuan' => $row->id_satuan,
					'kode_tree' => $kode_tree,
					'kode_analisa' => $row->kode_analisa,
					'tree_item' => utf8_encode($row->tree_item),
					'tree_satuan' => $row->tree_satuan,
					'volume' => $row->volume,
					'harga' => $row->hrg,
					'subtotal' => $row->sub,
					'tree_parent_id' => $row->tree_parent_id,
					'kdt' => $row->tree_parent_kode.'.'.$n
					/* 
					'harga' => $temp_harga['harga'], 
					'subtotal' => $temp_harga['subtotal'],
					*/
				);
				
				if($depth == 0) $data[$i] = array_merge($data[$i], array('expanded' => true));
				
				## check if have a child
				$q = sprintf("SELECT * FROM simpro_rap_item_tree WHERE id_proyek = '%d' AND tree_parent_id = '%d'", $row->id_proyek, $row->rap_item_tree);
				$query = $this->db->query($q);
				$is_child = $query->num_rows();
				if($is_child)
				{			
					// if ($row->kode_tree != '2.1'){
						$result[] = array_merge(
							$data[$i],
							array(
								'iconCls' => 'task-folder',
								'ishaschild' => 1,
								'children'=>$this->tree($idpro, $row->rap_item_tree)
							)
						);
					// }	
				} else 
				{
					if ($row->ktr != 0){
						$result[] = array_merge(
							$data[$i],
							array(
								'iconCls' => 'task-folder',
								'leaf' => true
							)
						);
					}
				}
				$n++;
				$i++;
			}
		}
		return array_filter($result);
	}

	function data_umum_proyek()
	{
		$idpro = $this->session->userdata('proyek_id'); 
		if($idpro)
		{
			// $this->update_data_umum_proyek();
			$data_tender = $this->mdl_rbk->get_data_tender($idpro);
			$data = array(
				'idtender' => $idpro,
				'data_tender' => $data_tender['data']
			);
			$this->load->view("data_umum_proyek", $data);	
		} else echo "<h2 align='center'>Silahkan pilih tender terlebih dahulu!</h2>";

		// $proyek_id = $this->session->userdata('proyek_id'); 
		// $data['proyek_id'] = $proyek_id;
		// $this->load->view("data_umum_proyek",$data);
	}

	function update_data_umum_proyek()
	{
		$proyek_id = $this->session->userdata('proyek_id');

		$sql = "
			update simpro_tbl_proyek set
				proyek	=	a.nama_proyek,
				lingkup_pekerjaan	=	a.lingkup_pekerjaan,
				tgl_tender	=	a.tanggal_tender,
				user_update	=	'antono',
				tgl_update	=	now(),
				ip_update	=	'::1',
				divisi_update	=	now(),
				divisi_kode	=	a.divisi_id,
				mulai	=	a.mulai,
				berakhir	=	a.akhir,
				jangka_waktu	=	a.waktu_pelaksanaan,
				masa_pemeliharaan	=	a.waktu_pemeliharaan,
				total_waktu_pelaksanaan	=	a.waktu_pelaksanaan * a.waktu_pemeliharaan,
				sbu_kode	=	a.jenis_proyek,
				lokasi_proyek	=	a.lokasi_proyek,
				proyek_konsultan_pengawas	=	a.konsultan_pengawas,
				pemberi_kerja =	a.pemilik_proyek	,
				kepala_proyek	=	a.konsultan_pelaksana,
				nilai_kontrak_ppn	=	a.nilai_kontrak_ppn,
				nilai_kontrak_non_ppn =	a.nilai_kontrak_excl_ppn	,
				lokasi_latitude	=	a.xlong,
				lokasi_longitude	=	a.xlat,
				sketsa_proyek	=	a.peta_lokasi_proyek
			from (
				select * from simpro_m_rat_proyek_tender where proyek_id = $proyek_id
			) a
			where simpro_tbl_proyek.proyek_id = a.proyek_id
		";

		$this->db->query($sql);
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
					'foto_proyek_tgl' => date("Y-m-d"),
					'foto_proyek_judul' => $this->input->post('du_proyek_judul'),
					'foto_proyek_keterangan' => $this->input->post('du_proyek_keterangan'),
					'foto_proyek_file' => $data['file_name'],
					'foto_proyek_file_type' => $data['file_type'],
					'proyek_id' => $this->input->post('id_proyek_rat')
				);						
				
				if($this->db->insert('simpro_tbl_sketsa_proyek',$arr_insert)) 
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

	function get_data_sketsa($id)
	{
		$ret = $this->mdl_rbk->get_data_sketsa_proyek($id);
		$this->_out($ret);
	}

	function delete_sketsa_proyek()
	{

		if($this->input->post('id') && $this->input->post('file'))
		{
			if(unlink('./uploads/'.$this->input->post('file')))
			{
				$this->db->query(sprintf("DELETE FROM simpro_tbl_sketsa_proyek WHERE foto_no = '%d'", $this->input->post('id')));
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

	function get_data_proyek_data($id)
	{
		$id = isset($id) ? $id : $this->input->post('id');
		if(isset($id))
		{
			$ret = $this->mdl_rbk->data_proyek_by_id($id);
			if(count($ret) > 0)
			{
				echo json_encode(array('success' => true, 'message'=> 'Data berhasil diload!', 'data' => $ret));
			} 
		} else echo json_encode(array('success' => true, 'message'=> 'Silahkan pilih proyek terlebih dahulu!'));
	}

	function update_data_proyek()
	{
		

		if ($this->input->post('proyek_id')) {

			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '204800';
			$config['remove_spaces']  = true;

			$this->load->library('upload', $config);
			/*   Upload gambar struktur organisasi dan sketsa proyek  */

			$q = $this->db->query("SELECT * FROM simpro_tbl_proyek WHERE proyek_id='".$this->input->post('proyek_id')."'")->row();

			if($_FILES['sketsa_proyek']['name'] != ''){

				if($q->sketsa_proyek != ''){
					unlink('./uploads/'.$q->sketsa_proyek);
				}

				if(!$this->upload->do_upload('sketsa_proyek'))
				{
					throw new Exception('File Gagal Upload, ukuran file tidak boleh lebih dari 2MB. Tipe file yg diperbolehkan: gif|jpg|png');
				} else {

					$data = $this->upload->data();

					$sketsa_proyek = $data['file_name'];
				}
			} else {
				$sketsa_proyek = $q->sketsa_proyek;
			}

			///////////////////////////////////////////////////////////////////////

			if($_FILES['struktur_organisasi']['name'] != ''){

				if($q->struktur_organisasi != ''){
					unlink('./uploads/'.$q->struktur_organisasi);
				}

				if(!$this->upload->do_upload('struktur_organisasi'))
				{
					throw new Exception('File Gagal Upload, ukuran file tidak boleh lebih dari 2MB. Tipe file yg diperbolehkan: gif|jpg|png');
				} else {

					$data_so = $this->upload->data();

					$struktur_organisasi = $data_so['file_name'];
				}
			} else {
				$struktur_organisasi = $q->struktur_organisasi;
			}

			/*   Upload gambar struktur organisasi dan sketsa proyek  */

			$tgl_berakhir = explode("-", $this->input->post('berakhir'));
			$num_akhir=mktime('0','0','0',$tgl_berakhir[2],$tgl_berakhir[1],$tgl_berakhir[0]);
			$jum_bast1= $this->dateadd("d",$this->input->post('perpanjangan_waktu'),$num_akhir);
			$jum_bast2= $this->dateadd("d",$this->input->post('masa_pemeliharaan'),$jum_bast1);
			$tgl_bast_1 = date('Y-m-d',$jum_bast1);
			$tgl_bast_2 = date('Y-m-d',$jum_bast2);

			$this->db->where('proyek_id', $this->input->post('proyek_id'));

			$arr_update = array(
				'sketsa_proyek' => $sketsa_proyek,
				'struktur_organisasi' => $struktur_organisasi,
				'divisi_kode' => $this->input->post('divisi_kode'),
				'propinsi' => $this->input->post('propinsi'),
				'kode_wilayah' => $this->input->post('kode_wilayah'),
				'status_pekerjaan' => $this->input->post('status_pekerjaan'),
				'sts_pekerjaan' => $this->input->post('sts_pekerjaan'),
				'sbu_kode' => $this->input->post('sbu_kode'),
				'proyek' => $this->input->post('proyek'),
				'lingkup_pekerjaan' => $this->input->post('lingkup_pekerjaan'),
				'proyek_alamat' => $this->input->post('proyek_alamat'),
				'proyek_telp' => $this->input->post('proyek_telp'),
				'lokasi_proyek' => $this->input->post('lokasi_proyek'),
				'pemberi_kerja' => $this->input->post('pemberi_kerja'),
				'kepala_proyek' => $this->input->post('kepala_proyek'),
				'proyek_konsultan_pengawas' => $this->input->post('proyek_konsultan_pengawas'),
				'no_spk' => $this->input->post('no_spk'),
				'no_spk_2' => $this->input->post('no_spk_2'),
				'no_kontrak' => $this->input->post('no_kontrak'),
				'no_kontrak2' => $this->input->post('no_kontrak2'),
				'wo' => trim($this->input->post('wo')),
				'nilai_kontrak_ppn' => $this->input->post('nilai_kontrak_ppn'),
				'nilai_kontrak_non_ppn' => $this->input->post('nilai_kontrak_non_ppn'),
				'pph_final' => $this->input->post('pph_final'),
				'proyek_nama_sumber_1' => $this->input->post('proyek_nama_sumber_1'),
				'uang_muka' => $this->input->post('uang_muka'),
				'termijn' => $this->input->post('termijn'),
				'retensi' => $this->input->post('retensi'),
				'jaminan_pelaksanaan' => $this->input->post('jaminan_pelaksanaan'),
				'asuransi_pekerjaan' => $this->input->post('asuransi_pekerjaan'),
				'denda_minimal' => $this->input->post('denda_minimal'),
				'denda_maksimal' => $this->input->post('denda_maksimal'),
				'sifat_kontrak' => $this->input->post('sifat_kontrak'),
				'manajemen_pelaksanaan' => $this->input->post('manajemen_pelaksanaan'),
				'eskalasi' => $this->input->post('eskalasi'),
				'beda_kurs' => $this->input->post('beda_kurs'),
				'pek_tambah_kurang' => $this->input->post('pek_tambah_kurang'),
				'rap_usulan' => $this->input->post('rap_usulan'),
				'rap_ditetapkan' => $this->input->post('rap_ditetapkan'),
				'mulai' => $this->input->post('mulai'),
				'berakhir' => $this->input->post('berakhir'),
				'perpanjangan_waktu' => $this->input->post('perpanjangan_waktu'),
				'masa_pemeliharaan' => $this->input->post('masa_pemeliharaan'),
				'tgl_tender' => $this->input->post('tgl_tender'),
				'tgl_pengumuman' => $this->input->post('tgl_pengumuman'),
				'proyek_status' => $this->input->post('proyek_status'),
				'lokasi_longitude' => $this->input->post('lokasi_longitude'),
				'lokasi_latitude' => $this->input->post('lokasi_latitude'),
				'bast_1' => $tgl_bast_1,
				'bast_2' => $tgl_bast_2
			);

			if($this->db->update('simpro_tbl_proyek', $arr_update))
			{
				echo json_encode(array('success'=>true, 'message'=>'Data Umum Proyek berhasil diupdate.'));
			} else echo json_encode(array('success'=>true, 'message'=>'Data Umum Proyek GAGAL diupdate!'));
		} else {

			$tgl_berakhir = explode("-", $this->input->post('berakhir'));
			$num_akhir=mktime('0','0','0',$tgl_berakhir[2],$tgl_berakhir[1],$tgl_berakhir[0]);
			$jum_bast1= $this->dateadd("d",$this->input->post('perpanjangan_waktu'),$num_akhir);
			$jum_bast2= $this->dateadd("d",$this->input->post('masa_pemeliharaan'),$jum_bast1);
			$tgl_bast_1 = date('Y-m-d',$jum_bast1);
			$tgl_bast_2 = date('Y-m-d',$jum_bast2);

			$arr_update = array(
				'divisi_kode' => $this->input->post('divisi_kode'),
				'propinsi' => $this->input->post('propinsi'),
				'kode_wilayah' => $this->input->post('kode_wilayah'),
				'status_pekerjaan' => $this->input->post('status_pekerjaan'),
				'sts_pekerjaan' => $this->input->post('sts_pekerjaan'),
				'sbu_kode' => $this->input->post('sbu_kode'),
				'proyek' => $this->input->post('proyek'),
				'lingkup_pekerjaan' => $this->input->post('lingkup_pekerjaan'),
				'proyek_alamat' => $this->input->post('proyek_alamat'),
				'proyek_telp' => $this->input->post('proyek_telp'),
				'lokasi_proyek' => $this->input->post('lokasi_proyek'),
				'pemberi_kerja' => $this->input->post('pemberi_kerja'),
				'kepala_proyek' => $this->input->post('kepala_proyek'),
				'proyek_konsultan_pengawas' => $this->input->post('proyek_konsultan_pengawas'),
				'no_spk' => $this->input->post('no_spk'),
				'no_spk_2' => $this->input->post('no_spk_2'),
				'no_kontrak' => $this->input->post('no_kontrak'),
				'no_kontrak2' => $this->input->post('no_kontrak2'),
				'wo' => trim($this->input->post('wo')),
				'nilai_kontrak_ppn' => $this->input->post('nilai_kontrak_ppn'),
				'nilai_kontrak_non_ppn' => $this->input->post('nilai_kontrak_non_ppn'),
				'pph_final' => $this->input->post('pph_final'),
				'proyek_nama_sumber_1' => $this->input->post('proyek_nama_sumber_1'),
				'uang_muka' => $this->input->post('uang_muka'),
				'termijn' => $this->input->post('termijn'),
				'retensi' => $this->input->post('retensi'),
				'jaminan_pelaksanaan' => $this->input->post('jaminan_pelaksanaan'),
				'asuransi_pekerjaan' => $this->input->post('asuransi_pekerjaan'),
				'denda_minimal' => $this->input->post('denda_minimal'),
				'denda_maksimal' => $this->input->post('denda_maksimal'),
				'sifat_kontrak' => $this->input->post('sifat_kontrak'),
				'manajemen_pelaksanaan' => $this->input->post('manajemen_pelaksanaan'),
				'eskalasi' => $this->input->post('eskalasi'),
				'beda_kurs' => $this->input->post('beda_kurs'),
				'pek_tambah_kurang' => $this->input->post('pek_tambah_kurang'),
				'rap_usulan' => $this->input->post('rap_usulan'),
				'rap_ditetapkan' => $this->input->post('rap_ditetapkan'),
				'mulai' => $this->input->post('mulai'),
				'berakhir' => $this->input->post('berakhir'),
				'perpanjangan_waktu' => $this->input->post('perpanjangan_waktu'),
				'masa_pemeliharaan' => $this->input->post('masa_pemeliharaan'),
				'tgl_tender' => $this->input->post('tgl_tender'),
				'tgl_pengumuman' => $this->input->post('tgl_pengumuman'),
				'proyek_status' => $this->input->post('proyek_status'),
				'bast_1' => $tgl_bast_1,
				'bast_2' => $tgl_bast_2
			);

			if($this->db->insert('simpro_tbl_proyek', $arr_update))
			{
				echo json_encode(array('success'=>true, 'message'=>'Data Umum Proyek berhasil disimpan.'));
			} else echo json_encode(array('success'=>true, 'message'=>'Data Umum Proyek GAGAL disimpan!'));
		}
		
	}

	function download($file="")
    {
    	$this->load->helper('download');
    	
    	if ($file) {
    		$file_path = base_url().'uploads/'.$file;

    		$data = file_get_contents($file_path); // Read the file's contents
			$name = $file;

			force_download($name, $data);
    	}
    }

	function get_data_dokumen($id)
	{
		$ret = $this->mdl_rbk->get_data_dokumen_proyek($id);
		$this->_out($ret);
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
					'proyek_id' => $this->input->post('proyek_id')
				);						

				if($this->db->insert('simpro_tbl_dokumen_proyek',$arr_insert)) 
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
				$this->db->query(sprintf("DELETE FROM simpro_tbl_dokumen_proyek WHERE id_dokumen_proyek = '%d'", $this->input->post('id')));
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

	function print_data_analisa($page="")
	{
		$idpro = $this->session->userdata('proyek_id'); 

		if ($page=="") {

		} else {

			$nama_proyek = $this->db->query("select proyek from simpro_tbl_proyek where proyek_id = $idpro")
			->row()->proyek;

			$date = date('Y-m-d');

			$nama_proyek_u = substr(str_replace(' ', '_', $nama_proyek), 0, 12);
			// var_dump($nama_proyek_u);

			//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle(substr($page, 0,7).'_'.$nama_proyek_u.'_'.$date);

			//set cell A1 content with some text

			$this->excel->getActiveSheet()->mergeCells('A1:F1');
			$this->excel->getActiveSheet()->mergeCells('A2:F2');

			$get_item_pekerjaan = $this->db->query("select 
					a.kode_tree,
					a.tree_item,
					b.kode_analisa,
					c.*
					from 
					simpro_rap_item_tree a 
					join simpro_rap_analisa_item_apek b 
					on a.rap_item_tree = b.rap_item_tree
					join
					(
					SELECT simpro_tbl_subbidang.subbidang_kode,simpro_tbl_subbidang.subbidang_name,tbl_analisa_satuan.* FROM (
					(
						SELECT 					
							simpro_rap_analisa_asat.id_analisa_asat,
							simpro_rap_analisa_asat.kode_analisa,
							(simpro_rap_analisa_asat.kode_analisa || ' - ' || simpro_rap_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
							simpro_rap_analisa_daftar.nama_item,
							simpro_rap_analisa_daftar.id_satuan,
							simpro_tbl_satuan.satuan_nama,
							simpro_tbl_detail_material.detail_material_kode, 
							simpro_tbl_detail_material.detail_material_nama, 
							simpro_tbl_detail_material.detail_material_satuan,
							simpro_rap_analisa_asat.harga,
							simpro_rap_analisa_asat.koefisien,
							(simpro_rap_analisa_asat.harga * simpro_rap_analisa_asat.koefisien) AS subtotal
						FROM 
							simpro_rap_analisa_asat
						LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
						LEFT JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_asat.kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_asat.id_proyek)
						LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
						WHERE simpro_rap_analisa_asat.id_proyek= $idpro
						ORDER BY 
							simpro_rap_analisa_asat.kode_analisa,
							simpro_tbl_detail_material.detail_material_kode				
						ASC
					)
					UNION ALL 
					(
						SELECT 
							simpro_rap_analisa_apek.id_analisa_apek AS id_analisa_asat,
							simpro_rap_analisa_apek.parent_kode_analisa as kode_analisa,
							(simpro_rap_analisa_apek.parent_kode_analisa || ' - ' || simpro_rap_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
							ad.nama_item AS nama_item,
							simpro_rap_analisa_daftar.id_satuan,
							simpro_tbl_satuan.satuan_nama,
							simpro_rap_analisa_apek.kode_analisa AS detail_material_kode,
							ad.nama_item as detail_material_nama,
							simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
							COALESCE(tbl_harga.harga,0) AS harga,
							simpro_rap_analisa_apek.koefisien,
							COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
						FROM 
							simpro_rap_analisa_apek
						INNER JOIN simpro_rap_analisa_daftar ad ON (ad.kode_analisa = simpro_rap_analisa_apek.kode_analisa AND ad.id_proyek= simpro_rap_analisa_apek.id_proyek)
						INNER JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_apek.parent_kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_apek.id_proyek)			
						INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
						LEFT JOIN (
							SELECT 
								DISTINCT ON(kode_analisa)
								kode_analisa,
								SUM(harga * koefisien) AS harga
							FROM simpro_rap_analisa_asat 
							WHERE id_proyek= $idpro
							GROUP BY kode_analisa			
						) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_apek.kode_analisa			
						WHERE simpro_rap_analisa_apek.id_proyek= $idpro
						ORDER BY 
							simpro_rap_analisa_apek.parent_kode_analisa,				
							simpro_rap_analisa_apek.kode_analisa
						ASC					
					)		
					) AS tbl_analisa_satuan
					left join simpro_tbl_subbidang
					on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
					) c
					on c.kode_analisa = b.kode_analisa
					where a.id_proyek = $idpro
					order by a.kode_tree,c.subbidang_kode");

			$x = 3;
			$n = 1;
			$na = 0;

			$item = 4;
			$judul = 5;
			$subbidang_no = 6;

			$kode_tree = '';
			$subbidang = '';

			$jml = 0;

			// var_dump($get_item_pekerjaan->result_object());
			if ($get_item_pekerjaan->result()) {

				$styleArrayBorder = array(
					'borders' => array(
						'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				);

				foreach ($get_item_pekerjaan->result() as $rows) {

					if ($kode_tree != $rows->kode_tree) {
						if ($kode_tree != '') {
							$this->excel->getActiveSheet()->mergeCells('A'.$x.':E'.$x);
							$this->excel->getActiveSheet()->setCellValue('A'.$x, 'TOTAL');
							$this->excel->getActiveSheet()->setCellValue('F'.$x, $jml);

							$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->applyFromArray($styleArrayBorder);
							unset($styleArray);

							$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->getFont()->setBold(true);
						}

						$jml = 0;
						$jml_rab = 0;
						$n = 1;
						$item = $x+2;
						$judul = $x+3;
						$subbidang_no = $x+4;
						$x=$x+5;
					} else {
						if ($subbidang != $rows->subbidang_name) {
							$n = 1;
							$subbidang_no = $x;
							$x=$x+1;
						}
					}

					$kode_tree = $rows->kode_tree;
					$item_pekerjaan = $rows->tree_item;
					$subbidang = $rows->subbidang_name;

					$dm_nama = $rows->detail_material_nama;
					$dm_kode = $rows->detail_material_kode;
					$dm_satuan = $rows->detail_material_satuan;
					$dm_koefisien = $rows->koefisien;
					$dm_harga = $rows->harga;
					$dm_jumlah = $rows->subtotal;

					$this->excel->getActiveSheet()->getStyle('A'.$item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					// $this->excel->getActiveSheet()->getStyle('A'.$subbidang_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A'.$item)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->mergeCells('A'.$item.':F'.$item);
					$this->excel->getActiveSheet()->setCellValue('A'.$item, $kode_tree.' '.$item_pekerjaan);

					$this->excel->getActiveSheet()->mergeCells('A'.$subbidang_no.':F'.$subbidang_no);
					$this->excel->getActiveSheet()->setCellValue('A'.$subbidang_no, $subbidang);
					$this->excel->getActiveSheet()->getStyle('A'.$subbidang_no.':F'.$subbidang_no)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->setCellValue('A'.$judul, 'NO');
					$this->excel->getActiveSheet()->setCellValue('B'.$judul, 'SUMBER DAYA');
					$this->excel->getActiveSheet()->setCellValue('C'.$judul, 'SATUAN');
					$this->excel->getActiveSheet()->setCellValue('D'.$judul, 'HARGA SATUAN');
					$this->excel->getActiveSheet()->setCellValue('E'.$judul, 'KOEFISIEN');
					$this->excel->getActiveSheet()->setCellValue('F'.$judul, 'JUMLAH');
					$this->excel->getActiveSheet()->getStyle('A'.$judul.':F'.$judul)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->setCellValueExplicit('A'.$x, '1.'.$n, PHPExcel_Cell_DataType::TYPE_STRING);
					$this->excel->getActiveSheet()->setCellValue('B'.$x, $dm_nama.'('.$dm_kode.')');
					$this->excel->getActiveSheet()->setCellValue('C'.$x, $dm_satuan);
					$this->excel->getActiveSheet()->setCellValue('D'.$x, $dm_harga);
					$this->excel->getActiveSheet()->setCellValue('E'.$x, $dm_koefisien);
					$this->excel->getActiveSheet()->setCellValue('F'.$x, $dm_jumlah);

					$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$item.':F'.$item)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$subbidang_no.':F'.$subbidang_no)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$judul.':F'.$judul)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					// $this->excel->getActiveSheet()->setCellValue('G'.$x, $kode_tree);
					// $this->excel->getActiveSheet()->setCellValue('H'.$x, $subbidang);

					$x++;
					$n++;
					$na++;
					$jml+=$dm_jumlah;

				}

				$this->excel->getActiveSheet()->mergeCells('A'.$x.':E'.$x);
				$this->excel->getActiveSheet()->setCellValue('A'.$x, 'TOTAL');
				$this->excel->getActiveSheet()->setCellValue('F'.$x, $jml);

				$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->applyFromArray($styleArrayBorder);
				unset($styleArray);

				$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->getFont()->setBold(true);
			}

			$this->excel->getActiveSheet()->setCellValue('A1', strtoupper($page));
			$this->excel->getActiveSheet()->setCellValue('A2', strtoupper($nama_proyek).' ('.$date.')');			

			$styleArray = array(
			// 'borders' => array(
			//     'allborders' => array(
			//       'style' => PHPExcel_Style_Border::BORDER_THIN
			//     )
			// ),
			  'font'  => array(
		        'size'  => 7,
		        'name'  => 'verdana'
		    )
			);

			$this->excel->getActiveSheet()->getStyle('A1:F'.$x)->applyFromArray($styleArray);
			unset($styleArray);

			$styleArray1 = array(
			  'font'  => array(
		        'bold'  => true,
		        'uppercase' => true,
		        'size'  => 11,
		        'name'  => 'Calibri'
		    )
			);

			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(34);

			$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

			$this->excel->getActiveSheet()->getStyle('D1:F'.$this->excel->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00'); 


			// $this->excel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleArray1);
			// unset($styleArray1);

			$this->excel->getActiveSheet()->getStyle('B1:B'.$this->excel->getActiveSheet()->getHighestRow())
    		->getAlignment()->setWrapText(true);

			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);

			$filename=$page.'_'.$nama_proyek.'_'.$date.'.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			             
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');

			
		}
	}

	function print_data($page="")
	{
		$idpro = $this->session->userdata('proyek_id'); 
		$id_tender = $this->session->userdata('id_tender');

		if ($page=="") {

		} else {

			$nama_proyek = $this->db->query("select proyek from simpro_tbl_proyek where proyek_id = $idpro")
			->row()->proyek;

			$date = date('Y-m-d');

			$nama_proyek_u = substr(str_replace(' ', '_', $nama_proyek), 0, 12);

			//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle(substr($page, 0,7).'_'.$nama_proyek_u.'_'.$date);

			/* ======================= Start DATA ==================== */

			if ($page=='skbdn') {
				$data = $this->db->query("
					select 
					c.subbidang_name as subbidang_nama,
					b.detail_material_kode as kode_meterial,
					b.detail_material_nama as jenis_meterial,
					b.detail_material_spesifikasi as spesifikasi_meterial,
					b.detail_material_satuan as satuan,
					a.volume as volume,
					a.harga_satuan as harga_satuan,
					(a.volume * a.harga_satuan) as jumlah_harga
					from simpro_tbl_skbdn a
					JOIN simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					JOIN simpro_tbl_subbidang c
					on left(b.subbidang_kode,3) = c.subbidang_kode
					where proyek_id = $idpro
					order by subbidang_nama, jenis_meterial
				");

				$this->excel->getActiveSheet()->setCellValue('A4', 'NO');
				$this->excel->getActiveSheet()->setCellValue('B4', 'JENIS MATERIAL');
				$this->excel->getActiveSheet()->setCellValue('C4', 'NAMA MATERIAL');
				$this->excel->getActiveSheet()->setCellValue('D4', 'SATUAN');
				$this->excel->getActiveSheet()->setCellValue('E4', 'VOLUME');
				$this->excel->getActiveSheet()->setCellValue('F4', 'HARGA');
				$this->excel->getActiveSheet()->setCellValue('G4', 'JUMLAH');

				$x=5;
				$n=1;
				if ($data->result()) {
					$sub_nama = '';
					$jml = 0;
					foreach ($data->result() as $row) {

						if ($sub_nama != $row->subbidang_nama) {
							$this->excel->getActiveSheet()->setCellValue('A'.$x, $row->subbidang_nama);
							$this->excel->getActiveSheet()->mergeCells('A'.$x.':'.$this->excel->getActiveSheet()->getHighestColumn().$x);
							$x++;
							$sub_nama = $row->subbidang_nama;
							$n=1;
						}

						$this->excel->getActiveSheet()->setCellValue('A'.$x, $n);
						$this->excel->getActiveSheet()->setCellValue('B'.$x, $row->kode_meterial);
						$this->excel->getActiveSheet()->setCellValue('C'.$x, $row->jenis_meterial);
						$this->excel->getActiveSheet()->setCellValue('D'.$x, $row->satuan);
						$this->excel->getActiveSheet()->setCellValue('E'.$x, $row->volume);
						$this->excel->getActiveSheet()->setCellValue('F'.$x, $row->harga_satuan);
						$this->excel->getActiveSheet()->setCellValue('G'.$x, $row->jumlah_harga);

						$jml = $jml+$row->jumlah_harga;
						$x++;
						$n++;
					}

					$this->excel->getActiveSheet()->mergeCells('A'.$x.':F'.$x);
					$this->excel->getActiveSheet()->setCellValue('A'.$x, 'TOTAL');
					$this->excel->getActiveSheet()->setCellValue('G'.$x, $jml);
				}
			} elseif ($page=='rencana_penggunaan_rrp') {
				$data = $this->db->query("
					select 
					b.detail_material_kode as kode_meterial,
					b.detail_material_nama as jenis_meterial,
					b.detail_material_spesifikasi as spesifikasi_meterial,
					b.detail_material_satuan as satuan,
					a.volume as volume,
					a.harga_satuan as harga_satuan,
					(a.volume * a.harga_satuan) as jumlah_harga,
					c.subbidang_name as subbidang_nama
					from simpro_tbl_rincian_rencana_pengadaan a
					JOIN simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					JOIN simpro_tbl_subbidang c
					on left(b.subbidang_kode,3) = c.subbidang_kode
					where proyek_id = $idpro
					order by subbidang_nama, jenis_meterial
				");

				$this->excel->getActiveSheet()->setCellValue('A4', 'NO');
				$this->excel->getActiveSheet()->setCellValue('B4', 'JENIS MATERIAL');
				$this->excel->getActiveSheet()->setCellValue('C4', 'NAMA MATERIAL');
				$this->excel->getActiveSheet()->setCellValue('D4', 'SPESIFIKASI MATERIAL');
				$this->excel->getActiveSheet()->setCellValue('E4', 'SATUAN');
				$this->excel->getActiveSheet()->setCellValue('F4', 'VOLUME');
				$this->excel->getActiveSheet()->setCellValue('G4', 'HARGA');
				$this->excel->getActiveSheet()->setCellValue('H4', 'JUMLAH');

				$x=5;
				$n=1;
				if ($data->result()) {
					$sub_nama = '';
					$jml = 0;
					foreach ($data->result() as $row) {

						if ($sub_nama != $row->subbidang_nama) {
							$this->excel->getActiveSheet()->setCellValue('A'.$x, $row->subbidang_nama);
							$this->excel->getActiveSheet()->mergeCells('A'.$x.':'.$this->excel->getActiveSheet()->getHighestColumn().$x);
							$x++;
							$sub_nama = $row->subbidang_nama;
							$n=1;
						}

						$this->excel->getActiveSheet()->setCellValue('A'.$x, $n);
						$this->excel->getActiveSheet()->setCellValue('B'.$x, $row->kode_meterial);
						$this->excel->getActiveSheet()->setCellValue('C'.$x, $row->jenis_meterial);
						$this->excel->getActiveSheet()->setCellValue('D'.$x, $row->spesifikasi_meterial);
						$this->excel->getActiveSheet()->setCellValue('E'.$x, $row->satuan);
						$this->excel->getActiveSheet()->setCellValue('F'.$x, $row->volume);
						$this->excel->getActiveSheet()->setCellValue('G'.$x, $row->harga_satuan);
						$this->excel->getActiveSheet()->setCellValue('H'.$x, $row->jumlah_harga);

						$jml = $jml+$row->jumlah_harga;
						$x++;
						$n++;
					}

					$this->excel->getActiveSheet()->mergeCells('A'.$x.':G'.$x);
					$this->excel->getActiveSheet()->setCellValue('A'.$x, 'TOTAL');
					$this->excel->getActiveSheet()->setCellValue('H'.$x, $jml);
				}
			} elseif ($page=='checklist_dokumen') {
				$data = $this->db->query("
					SELECT
					a.uraian_pekerjaan,
					a.suplier,
					b.satuan_nama,
					a.harga_satuan,
					CASE WHEN a.status_penawaran = 0
					THEN '-'
					ELSE 'v'
					END as spen_ya,
					CASE WHEN a.status_penawaran = 0
					THEN 'v'
					ELSE '-'
					END as spen_tidak,
					CASE WHEN a.rekan_usul = 0
					THEN '-'
					ELSE 'v'
					END as rekan,
					a.keterangan
					FROM
					simpro_tbl_checklist_dokumen a
					join simpro_tbl_satuan b
					on a.satuan_id = b.satuan_id
					WHERE proyek_id = $idpro
					order by uraian_pekerjaan
				");

				$this->excel->getActiveSheet()->setCellValue('A4', 'NO');
				$this->excel->getActiveSheet()->setCellValue('B4', 'ITEM PEKERJAAN');
				$this->excel->getActiveSheet()->setCellValue('C4', 'NAMA SUPPLIER / SUBKONTRAKTOR');
				$this->excel->getActiveSheet()->setCellValue('D4', 'SATUAN');
				$this->excel->getActiveSheet()->setCellValue('E4', 'HARGA SATUAN');
				$this->excel->getActiveSheet()->setCellValue('F4', 'SURAT PENAWARAN');
				$this->excel->getActiveSheet()->setCellValue('F5', 'YA');
				$this->excel->getActiveSheet()->setCellValue('G5', 'TIDAK');
				$this->excel->getActiveSheet()->setCellValue('H4', 'REKANAN YANG DIUSULKAN');
				$this->excel->getActiveSheet()->setCellValue('I4', 'KETERANGAN');

				$this->excel->getActiveSheet()->mergeCells('A4:A5');
				$this->excel->getActiveSheet()->mergeCells('B4:B5');
				$this->excel->getActiveSheet()->mergeCells('C4:C5');
				$this->excel->getActiveSheet()->mergeCells('D4:D5');
				$this->excel->getActiveSheet()->mergeCells('E4:E5');
				$this->excel->getActiveSheet()->mergeCells('F4:G4');
				$this->excel->getActiveSheet()->mergeCells('H4:H5');
				$this->excel->getActiveSheet()->mergeCells('I4:I5');

				$this->excel->getActiveSheet()->getStyle('A5:'.$this->excel->getActiveSheet()->getHighestColumn().'5')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle('A5:'.$this->excel->getActiveSheet()->getHighestColumn().'5')
				->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$x=6;
				$n=1;
				if ($data->result()) {

					foreach ($data->result() as $row) {

						$this->excel->getActiveSheet()->setCellValue('A'.$x, $n);
						$this->excel->getActiveSheet()->setCellValue('B'.$x, $row->uraian_pekerjaan);
						$this->excel->getActiveSheet()->setCellValue('C'.$x, $row->suplier);
						$this->excel->getActiveSheet()->setCellValue('D'.$x, $row->satuan_nama);
						$this->excel->getActiveSheet()->setCellValue('E'.$x, $row->harga_satuan);
						$this->excel->getActiveSheet()->setCellValue('F'.$x, $row->spen_ya);
						$this->excel->getActiveSheet()->setCellValue('G'.$x, $row->spen_tidak);
						$this->excel->getActiveSheet()->setCellValue('H'.$x, $row->rekan);
						$this->excel->getActiveSheet()->setCellValue('I'.$x, $row->keterangan);

						$x++;
						$n++;
					}
				}
				$this->excel->getActiveSheet()->getStyle('F6:H'.$this->excel->getActiveSheet()->getHighestRow())
				->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			} elseif ($page=='eva_perbandingan') {
				$data = $this->db->query("
					with rap as (select 
						x.*, 
						case when right(x.tree_parent_kode,1) = '.' then
						left(x.tree_parent_kode,(length(x.tree_parent_kode)-1))
						else
						x.tree_parent_kode
						end as xnm,
						(select a::int from (select a, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) as row from (select unnest as a from unnest ( string_to_array ( trim ( x.kode_tree, '.'), '.' ) )) x) r order by row desc limit 1) as urut,
						case when sum(totals.subtotal) = 0 or x.volume = 0 then
						0
						else
						sum(totals.subtotal) / x.volume
						end as hrg
						,
						(
						sum(totals.subtotal)
						) as sub
						from
						simpro_rap_item_tree x
						left join
						(SELECT 
							simpro_rap_item_tree.kode_tree,
							simpro_rap_analisa_item_apek.kode_analisa,
							COALESCE(tbl_harga.harga, 0) AS harga,
							(COALESCE(tbl_harga.harga, 0) * simpro_rap_item_tree.volume) as subtotal
						FROM simpro_rap_item_tree 
						LEFT JOIN simpro_rap_analisa_item_apek ON simpro_rap_analisa_item_apek.kode_tree = simpro_rap_item_tree.kode_tree and simpro_rap_analisa_item_apek.id_proyek = $idpro
						LEFT JOIN (
							SELECT 
							DISTINCT ON(kode_analisa)
												kode_analisa,
												SUM(subtotal) AS harga
							FROM (
							(
								SELECT 					
									(simpro_rap_analisa_asat.kode_analisa) AS kode_analisa, 
									(simpro_rap_analisa_asat.harga * simpro_rap_analisa_asat.koefisien) AS subtotal
								FROM 
									simpro_rap_analisa_asat
								LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
								LEFT JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_asat.kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_asat.id_proyek)
								LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
								WHERE simpro_rap_analisa_asat.id_proyek= $idpro
								ORDER BY 
									simpro_rap_analisa_asat.kode_analisa,
									simpro_tbl_detail_material.detail_material_kode				
								ASC
							)
							UNION ALL 
							(
								SELECT 
									(simpro_rap_analisa_apek.parent_kode_analisa) AS kode_analisa, 
									COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
								FROM 
									simpro_rap_analisa_apek
								INNER JOIN simpro_rap_analisa_daftar ad ON (ad.kode_analisa = simpro_rap_analisa_apek.kode_analisa AND ad.id_proyek = simpro_rap_analisa_apek.id_proyek)
								INNER JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_apek.parent_kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_apek.id_proyek)			
								INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
								LEFT JOIN (
									SELECT 
										DISTINCT ON(kode_analisa)
										kode_analisa,
										SUM(harga * koefisien) AS harga
									FROM simpro_rap_analisa_asat 
									WHERE id_proyek= $idpro
									
									GROUP BY kode_analisa			
								) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_apek.kode_analisa			
								WHERE simpro_rap_analisa_apek.id_proyek= $idpro
								
								ORDER BY 
									simpro_rap_analisa_apek.parent_kode_analisa,				
									simpro_rap_analisa_apek.kode_analisa
								ASC					
							)		
							) AS tbl_analisa_satuan
							GROUP BY kode_analisa				
						) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_item_apek.kode_analisa						
						WHERE simpro_rap_item_tree.id_proyek = $idpro
						ORDER BY simpro_rap_item_tree.kode_tree ASC) as totals
						on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
						WHERE x.id_proyek = $idpro
						group by x.rap_item_tree			
						ORDER BY xnm, urut),
			rab as (
			select 
						x.tahap_volume_kendali as volume,
						'1.' || x.tahap_kode_kendali as kode_tree,
						coalesce((case when sum(totals.subtotal) = 0 or x.tahap_volume_kendali = 0 then
						0
						else
						sum(totals.subtotal) / x.tahap_volume_kendali
						end),0) as hrg
						,
						coalesce((
						sum(totals.subtotal)
						),0) as sub
						from
						simpro_tbl_input_kontrak x
						left join
						(select
						tahap_kode_kendali,
						(tahap_volume_kendali * tahap_harga_satuan_kendali) as subtotal
						from
						simpro_tbl_input_kontrak
						where proyek_id = $idpro) as totals
						on left(totals.tahap_kode_kendali,length(x.tahap_kode_kendali)) = x.tahap_kode_kendali
						WHERE x.proyek_id = $idpro
						group by x.input_kontrak_id
			),
			rat as (
			select 
						x.volume,
						'1.' || x.kode_tree as kd,
						case when sum(totals.subtotal) = 0 or x.volume = 0 then
						0
						else
						sum(totals.subtotal) / x.volume
						end as hrg
						,
						(
						sum(totals.subtotal)
						) as sub
						from
						simpro_rat_item_tree x
						left join
						(SELECT 
							simpro_rat_item_tree.kode_tree,
							simpro_rat_analisa_item_apek.kode_analisa,
							COALESCE(tbl_harga.harga, 0) AS harga,
							(COALESCE(tbl_harga.harga, 0) * simpro_rat_item_tree.volume) as subtotal
						FROM simpro_rat_item_tree 
						LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.kode_tree = simpro_rat_item_tree.kode_tree and simpro_rat_analisa_item_apek.id_proyek_rat = $id_tender
						LEFT JOIN (
							SELECT 
							DISTINCT ON(kode_analisa)
												kode_analisa,
												SUM(subtotal) AS harga
							FROM (
							(
								SELECT 					
									(simpro_rat_analisa_asat.kode_analisa) AS kode_analisa, 
									(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
								FROM 
									simpro_rat_analisa_asat
								LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
								LEFT JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender= simpro_rat_analisa_asat.id_tender)
								LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
								WHERE simpro_rat_analisa_asat.id_tender= $id_tender
								ORDER BY 
									simpro_rat_analisa_asat.kode_analisa,
									simpro_tbl_detail_material.detail_material_kode				
								ASC
							)
							UNION ALL 
							(
								SELECT 
									(simpro_rat_analisa_apek.parent_kode_analisa) AS kode_analisa, 
									COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
								FROM 
									simpro_rat_analisa_apek
								INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
								INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender= simpro_rat_analisa_apek.id_tender)			
								INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
								LEFT JOIN (
									SELECT 
										DISTINCT ON(kode_analisa)
										kode_analisa,
										SUM(harga * koefisien) AS harga
									FROM simpro_rat_analisa_asat 
									WHERE id_tender= $id_tender
									
									GROUP BY kode_analisa			
								) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
								WHERE simpro_rat_analisa_apek.id_tender= $id_tender
								
								ORDER BY 
									simpro_rat_analisa_apek.parent_kode_analisa,				
									simpro_rat_analisa_apek.kode_analisa
								ASC					
							)		
							) AS tbl_analisa_satuan
							GROUP BY kode_analisa				
						) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_item_apek.kode_analisa						
						WHERE simpro_rat_item_tree.id_proyek_rat = $id_tender 
						ORDER BY simpro_rat_item_tree.kode_tree ASC) as totals
						on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
						WHERE x.id_proyek_rat = $id_tender 
						group by x.rat_item_tree
			)
			select 
			rap.rap_item_tree as rap_rap_tree_item,
						rap.id_proyek as rap_id_proyek,
						rap.kode_tree as kode_tree,
						rap.tree_item as tree_item,
						rap.tree_satuan as tree_satuan,
						rap.tree_parent_id as rap_tree_parent_id,
						rap.volume as volume,
						rap.tree_parent_kode as rap_tree_parent_kode,
						rap.hrg as harga,
						rap.sub as subtotal,
						rab.kode_tree as rab_kode_tree,
						rab.volume as volume_rab,
						rab.hrg as harga_rab,
						rab.sub as subtotal_rab,
						rat.kd as rat_kode_tree,
						rat.volume as volume_rat,
						rat.hrg as harga_rat,
						rat.sub as subtotal_rat
			from rap left join rab on rap.kode_tree = rab.kode_tree left join rat on rap.kode_tree=rat.kd order by rap.kode_tree
				");

				$this->excel->getActiveSheet()->setCellValue('A4', 'KODE');
				$this->excel->getActiveSheet()->setCellValue('B4', 'ITEM PEKERJAAN');
				$this->excel->getActiveSheet()->setCellValue('C4', 'SATUAN');
				$this->excel->getActiveSheet()->setCellValue('D4', 'RENCANA ANGGARAN TENDER (RAT)');
				$this->excel->getActiveSheet()->setCellValue('D5', 'VOLUME');
				$this->excel->getActiveSheet()->setCellValue('E5', 'HARGA SATUAN');
				$this->excel->getActiveSheet()->setCellValue('F5', 'JUMLAH HARGA');
				$this->excel->getActiveSheet()->setCellValue('G5', 'PROSENTASE (%)');
				$this->excel->getActiveSheet()->setCellValue('H4', 'RAB / KONTRAK');
				$this->excel->getActiveSheet()->setCellValue('H5', 'VOLUME');
				$this->excel->getActiveSheet()->setCellValue('I5', 'HARGA SATUAN');
				$this->excel->getActiveSheet()->setCellValue('J5', 'JUMLAH HARGA');
				$this->excel->getActiveSheet()->setCellValue('K5', 'PROSENTASE (%)');
				$this->excel->getActiveSheet()->setCellValue('L4', 'USULAN RENCANA ANGGARAN PELAKSANAAN INDUK (RAPI)');
				$this->excel->getActiveSheet()->setCellValue('L5', 'VOLUME');
				$this->excel->getActiveSheet()->setCellValue('M5', 'HARGA SATUAN');
				$this->excel->getActiveSheet()->setCellValue('N5', 'JUMLAH HARGA');
				$this->excel->getActiveSheet()->setCellValue('O5', 'PROSENTASE (%)');

				$this->excel->getActiveSheet()->mergeCells('A4:A5');
				$this->excel->getActiveSheet()->mergeCells('B4:B5');
				$this->excel->getActiveSheet()->mergeCells('C4:C5');
				$this->excel->getActiveSheet()->mergeCells('D4:G4');
				$this->excel->getActiveSheet()->mergeCells('H4:K4');
				$this->excel->getActiveSheet()->mergeCells('L4:O4');

				$this->excel->getActiveSheet()->getStyle('A5:'.$this->excel->getActiveSheet()->getHighestColumn().'5')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle('A5:'.$this->excel->getActiveSheet()->getHighestColumn().'5')
				->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$x=6;
				if ($data->result()) {

					foreach ($data->result() as $row) {

						$this->excel->getActiveSheet()->setCellValueExplicit('A'.$x, $row->kode_tree, PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValue('B'.$x, $row->tree_item);
						$this->excel->getActiveSheet()->setCellValue('C'.$x, $row->tree_satuan);
						$this->excel->getActiveSheet()->setCellValue('D'.$x, $row->volume_rat);
						$this->excel->getActiveSheet()->setCellValue('E'.$x, $row->harga_rat);
						$this->excel->getActiveSheet()->setCellValue('F'.$x, $row->subtotal_rat);
						$this->excel->getActiveSheet()->setCellValue('G'.$x, '');
						$this->excel->getActiveSheet()->setCellValue('H'.$x, $row->volume_rab);
						$this->excel->getActiveSheet()->setCellValue('I'.$x, $row->harga_rab);
						$this->excel->getActiveSheet()->setCellValue('J'.$x, $row->subtotal_rab);
						$this->excel->getActiveSheet()->setCellValue('K'.$x, '');
						$this->excel->getActiveSheet()->setCellValue('L'.$x, $row->volume);
						$this->excel->getActiveSheet()->setCellValue('M'.$x, $row->harga);
						$this->excel->getActiveSheet()->setCellValue('N'.$x, $row->subtotal);
						$this->excel->getActiveSheet()->setCellValue('O'.$x, '');

						$x++;
					}
				}
			}

			/* ======================= End DATA ==================== */
			//set cell A1 content with some text

			$this->excel->getActiveSheet()->mergeCells('A1:'.$this->excel->getActiveSheet()->getHighestColumn().'1');
			$this->excel->getActiveSheet()->mergeCells('A2:'.$this->excel->getActiveSheet()->getHighestColumn().'2');

			$this->excel->getActiveSheet()->setCellValue('A1', strtoupper($page));
			$this->excel->getActiveSheet()->setCellValue('A2', strtoupper($nama_proyek).' ('.$date.')');	

			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1:'.$this->excel->getActiveSheet()->getHighestColumn().'4')
			->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);		
			$this->excel->getActiveSheet()->getStyle('A4:'.$this->excel->getActiveSheet()->getHighestColumn().'4')->getFont()->setBold(true);

			$styleArray = array(
			'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			),
			  'font'  => array(
		        'size'  => 7,
		        'name'  => 'verdana'
		    )
			);

			$this->excel->getActiveSheet()->getStyle('A4:'.$this->excel->getActiveSheet()->getHighestColumn().$this->excel->getActiveSheet()->getHighestRow())
			->applyFromArray($styleArray);
			unset($styleArray);

			$styleArray1 = array(
			  	'font'  => array(
		        'bold'  => true,
		        'uppercase' => true,
		        'size'  => 11,
		        'name'  => 'Calibri'
		    )
			);

			for ($c='A'; $c !=$this->excel->getActiveSheet()->getHighestColumn() ; $c++) { 
				$this->excel->getActiveSheet()->getColumnDimension($c)->setAutoSize(true);
			}

			$this->excel->getActiveSheet()->getStyle('D1:'.$this->excel->getActiveSheet()->getHighestColumn().$this->excel->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00'); 

			// $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(34);

			// $this->excel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleArray1);
			// unset($styleArray1);

			// $this->excel->getActiveSheet()
			// ->getStyle('B1:B'.$this->excel->getActiveSheet()->getHighestRow())
   //  		->getAlignment()->setWrapText(true);

			$filename=$page.'_'.$nama_proyek.'_'.$date.'.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			             
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');

			
		}
	}

	function print_surat_pernyataan($page="")
	{
		$idpro = $this->session->userdata('proyek_id'); 

		if ($page=="") {

		} else {

			$nama_proyek_judul = $this->db->query("select proyek from simpro_tbl_proyek where proyek_id = $idpro")
			->row()->proyek;

			$date = date('Y-m-d');

			$nama_proyek_u = substr(str_replace(' ', '_', $nama_proyek_judul), 0, 12);

			//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle(substr($page, 0,7).'_'.$nama_proyek_u.'_'.$date);
			//set cell A1 content with some text

			$user_id = $this->session->userdata('uid');
			$data_user = $this->mdl_rbk->get_user($user_id);
			$tgl = date('j');
			$bln = $this->bulan(date('n'));
			$thn = date('Y');
			$date= $tgl.' '.$bln.' '.$thn;
			$nama=$data_user['fullname'];
			$jabatan=$data_user['jabatan'];
			$alamat=$data_user['alamat'];

			$title = "SURAT PERNYATAAN";

			$bertanda_tangan = "Yang bertanda tangan di bawah ini :";

			$nama = "NAMA";
			$jabatan = "JABATAN";
			$alamat = "ALAMAT";

			$isi1 = "Menyatakan bahwa usulan Rencana BK (Beban Kontrak) yang saya sampaikan ini sudah dihitung dan dibuat dengan sebenar-benarnya, mengacu pada :";
			$isi2 = "1. Hasil survey kelokasi proyek, termasuk survey area proyek, stok dan harga material, tenaga kerja, jalan kerja serta lingkungan kerja.";
			$isi3 = "2. Untuk semua item kontrak Lump Sum, sudah menghitung ulang besaran kebutuhan volume pekerjaan dan seluruh anggaran biaya nya sesuai gambar pelaksanaan, spek teknis kontrak serta metode pelaksanaan yang benar.";
			$isi4 = "3. Semua harga satuan Bahan, Upah, Peralatan dan Sub Kontraktor yang disampaikan sudah sesuai dengan harga penawaran dari calon rekanan (copy terlampir), spek teknis kontrak, metode pelaksanaan dan ketentuan pengadaan yang masih berlaku.";
			$isi5 = "4. Semua Biaya Provisi/Bunga Bank, BAU Proyek, Perpajakan dan Penyusutan Alat serta Cash Flow yang disampaikan sudah sesuai dengan ketentuan/aturan yang berlaku, Schedule Pelaksanaan, Schedule Tenaga Kerja, Schedule Peralatan, Schedule Tagihan (Cash In), Schedule pengadaan dan Schedule pembayaran (Cash out).";
			$isi6 = "5. Sudah mengantisipasi dan memperhitungkan semua kemungkinan resiko yang akan terjadi sesuai pasal-pasal dalam kontrak yang berlaku, termasuk kemungkinan resiko lingkungan sosial.";
			$isi7 = "6. Sudah mengetahui dan memperhitungkan semua peluang yang ada dari rencana pelaksanaan sesuai kontrak proyek ini.";
			$isi8 = "Bila ada data-data perhitungan volume, analisa harga satuan, pasal-pasal kontrak, BOQ dan hasil Survey lapangan untuk kebutuhan perhitungan usulan BK awal yang dengan sengaja tidak saya sampaikan dan beresiko akan merugikan Perusahaan atau pihak lain karena ketidak benaran informasi dan data-data yang saya sampaikan maka saya bersedia ditindak tegas sesuai dengan Peraturan/ketentuan Perusahaan yang berlaku.";
			$isi9 = "Demikian surat pernyataan ini saya buat dan tanda tangani dengan benar, penuh kesadaran tanpa ada tekanan dari pihak manapun, untuk dipergunakan sebagai mana mestinya.";

			$tgl = "";

			$mengetahui = "Mengetahui,	";

			$nama_proyek = "KEPALA PROYEK";

			$gm = "( General Manager )";
			$mt = "( Manager Tekmas )";
			$mp = "( Manager Proyek )";

			$styleArray = array(
			  	'font'  => array(
			        'size'  => 12,
			        'name'  => 'Times New Roman'
		    	)
			);

			$styleArrayTitle = array(
			  	'font'  => array(
			        'size'  => 18,
			        'name'  => 'Times New Roman'
		    	)
			);

			$this->excel->getActiveSheet()->mergeCells('A1:C1');
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);	
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(23.86);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(21.86);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(39.57);
			$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
			unset($styleArrayTitle);
			$this->excel->getActiveSheet()->setCellValue('A1',$title);

			$this->excel->getActiveSheet()->mergeCells('A3:B3');
			$this->excel->getActiveSheet()->setCellValue('A1',$bertanda_tangan);

			$this->excel->getActiveSheet()->mergeCells('A9:C9');
			$this->excel->getActiveSheet()->mergeCells('A10:C10');
			$this->excel->getActiveSheet()->mergeCells('A11:C11');
			$this->excel->getActiveSheet()->mergeCells('A12:C12');
			$this->excel->getActiveSheet()->mergeCells('A13:C13');
			$this->excel->getActiveSheet()->mergeCells('A14:C14');
			$this->excel->getActiveSheet()->mergeCells('A15:C15');
			$this->excel->getActiveSheet()->mergeCells('A16:C16');
			$this->excel->getActiveSheet()->mergeCells('A17:C17');

			$this->excel->getActiveSheet()->setCellValue('A9',$isi1);
			$this->excel->getActiveSheet()->setCellValue('A10',$isi2);
			$this->excel->getActiveSheet()->setCellValue('A11',$isi3);
			$this->excel->getActiveSheet()->setCellValue('A12',$isi4);
			$this->excel->getActiveSheet()->setCellValue('A13',$isi5);
			$this->excel->getActiveSheet()->setCellValue('A14',$isi6);
			$this->excel->getActiveSheet()->setCellValue('A15',$isi7);
			$this->excel->getActiveSheet()->setCellValue('A16',$isi8);
			$this->excel->getActiveSheet()->setCellValue('A17',$isi9);

			$this->excel->getActiveSheet()->mergeCells('A22:B22');
			$this->excel->getActiveSheet()->setCellValue('A22',$mengetahui);
			
			$this->excel->getActiveSheet()->setCellValue('A5',$nama);
			$this->excel->getActiveSheet()->setCellValue('A6',$jabatan);
			$this->excel->getActiveSheet()->setCellValue('A7',$alamat);

			$this->excel->getActiveSheet()->setCellValue('B5',':');
			$this->excel->getActiveSheet()->setCellValue('B6',':');
			$this->excel->getActiveSheet()->setCellValue('B7',':');			

			$this->excel->getActiveSheet()->setCellValue('C22','KEPALA PROYEK');

			$this->excel->getActiveSheet()->setCellValue('A27',$gm);
			$this->excel->getActiveSheet()->setCellValue('B27',$mt);
			$this->excel->getActiveSheet()->setCellValue('C27',$mp);

			$this->excel->getActiveSheet()->setCellValue('C20', 'Jakarta, '.$date);

			$this->excel->getActiveSheet()
			->getStyle('A1:C'.$this->excel->getActiveSheet()->getHighestRow())
    		->getAlignment()->setWrapText(true);

			$this->excel->getActiveSheet()->getStyle('A2:C'.$this->excel->getActiveSheet()->getHighestRow())->applyFromArray($styleArray);
			unset($styleArray);

			$this->excel->getActiveSheet()->getStyle('A18:C'.$this->excel->getActiveSheet()->getHighestRow())->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			for ($c=1; $c <=$this->excel->getActiveSheet()->getHighestRow() ; $c++) { 
				$this->excel->getActiveSheet()->getRowDimension($c)->setRowHeight(-1);
			}

			$filename=$page.'_'.$nama_proyek_judul.'_'.$date.'.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			             
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');

			
		}
	}

	function get_divisi() 
	{		
		$data = $this->mdl_rbk->get_divisi();	
		$this->_out($data);
	}

	function get_proyek_pilih()
	{
		$divisi_id = $this->input->get('divisi_id');
		$proyek = $this->mdl_rbk->pilih_proyek_pilih($divisi_id);
		if($proyek['total'] > 0)
		{
			$data = array('success'=>true, 'data'=>$proyek['data'], 'total'=>$proyek['total']);
			$this->_out($data);
		} else json_encode(array('success'=>true, 'data'=>NULL, 'total'=>0));
	}

	function copy_analisa_proyek_lain()
	{
		$idtender = $this->session->userdata('proyek_id');
		$id_proyek_copy = $this->input->post('id_proyek');
		$jml = $this->input->post('jml');

		$data_arr = $this->input->post('id_data');
		$data = explode(',', $data_arr);

		$x = 0;
		foreach ($data as $kv) {
			$cek_apek = "select * from simpro_rap_analisa_apek where parent_id_analisa = $kv and id_proyek = $id_proyek_copy";
			$q_cek_apek = $this->db->query($cek_apek);
			if ($q_cek_apek->result()) {
				if ($q_cek_apek->result()) {
					foreach ($q_cek_apek->result() as $rwe) {
						$da = $rwe->id_data_analisa;
						$q_cek_asat = $this->db->query("select * from simpro_rap_analisa_asat where id_data_analisa = $da and id_proyek = $id_proyek_copy");
						if ($q_cek_asat->result()) {
							foreach ($data as $ka) {
								if ($ka == $q_cek_asat->row()->id_data_analisa) {
									$x++;
								}
							}
						}
					}
				}
			}
		}
		if ($x == $jml) {
			foreach ($data as $k) {
				$sql_daftar_analisa = "select * from simpro_rap_analisa_daftar where id_data_analisa = $k";
				$q_daftar_analisa = $this->db->query($sql_daftar_analisa);
				if ($q_daftar_analisa->result()) {
					foreach ($q_daftar_analisa->result() as $row) {
						$last_analisa = "select right(kode_analisa,3)::int+1 as last_analisa from simpro_rap_analisa_daftar where id_proyek = $idtender order by right(kode_analisa,3)::int desc limit 1";
						$kda = sprintf("%03d",$this->db->query($last_analisa)->row()->last_analisa);
						$data_daftar = array(
							'kode_analisa' => 'AN'.$kda,
							'id_kat_analisa' => $row->id_kat_analisa,
							'nama_item' => $row->nama_item,
							'id_satuan' => $row->id_satuan,
							'id_proyek' => $idtender
						);
						$this->db->insert('simpro_rap_analisa_daftar',$data_daftar);
						$last_id_data_analisa = $this->db->insert_id();

						$data_id_analisa = $this->db->query("select * from simpro_rap_analisa_daftar where id_data_analisa = $last_id_data_analisa")->row();
						
						$arr_copy = array(
							'kode_analisa' => $data_id_analisa->kode_analisa,
							'id_data_analisa' => $data_id_analisa->id_data_analisa,
						);

						$sql_asat = "select * from simpro_rap_analisa_asat where kode_analisa = '$row->kode_analisa' and id_proyek = $row->id_proyek";
						$q_asat = $this->db->query($sql_asat);
						if ($q_asat->result()) {
							foreach ($q_asat->result() as $row_asat) {

								$data_asat = array(
									'id_data_analisa' => $data_id_analisa->id_data_analisa,
									'kode_material' => $row_asat->kode_material,
									'id_detail_material' => $row_asat->id_detail_material,
									'koefisien' => $row_asat->koefisien,
									'harga' => $row_asat->harga,
									'kode_analisa' => $data_id_analisa->kode_analisa,
									'id_proyek' => $idtender,
									'kode_rap' => $row_asat->kode_rap,
									'keterangan' => $row_asat->keterangan
								);
								$this->db->insert('simpro_rap_analisa_asat',$data_asat);
							}
							$arr_ses = array(
								'id_data_analisa' => $data_id_analisa->id_data_analisa,
								'kode_analisa' => $data_id_analisa->kode_analisa,
								'id_data_analisa_lama' => $row_asat->id_data_analisa,
								'kode_analisa_lama' => $row_asat->kode_analisa,
							);

							$arr_ses_all[] = $arr_ses;
						}

						$sql_apek = "select * from simpro_rap_analisa_apek where parent_kode_analisa = '$row->kode_analisa' and id_proyek = $row->id_proyek";
						$q_apek = $this->db->query($sql_apek);
						if ($q_apek->result()) {
							foreach ($q_apek->result() as $row_apek) {
								$data_apek = array(
									'parent_kode_analisa' => $data_id_analisa->kode_analisa,
									'parent_id_analisa' => $data_id_analisa->id_data_analisa,
									'kode_analisa' => $row_apek->kode_analisa,
									'id_data_analisa' => $row_apek->id_data_analisa,
									'koefisien' => $row_apek->koefisien,
									'harga' => $row_apek->harga,
									'id_proyek' => $idtender
								);
								$this->db->insert('simpro_rap_analisa_apek',$data_apek);
							}
						}

						$arr_copy_all[] = $arr_copy;
					}
				}
			}

			foreach ($arr_copy_all as $kc) {
				$d_dat_copy = $kc['kode_analisa'];
				$sql_apek = "select * from simpro_rap_analisa_apek where parent_kode_analisa = '$d_dat_copy' and id_proyek = $idtender";
				$q_apek = $this->db->query($sql_apek);
				if ($q_apek->result()) {
					foreach ($q_apek->result() as $rowa) {
						$get_kode_apek = $rowa->kode_analisa;
						$get_id_apek = $rowa->id_analisa_apek;
						
						foreach ($arr_ses_all as $ks) {
							if ($ks['kode_analisa_lama'] == $get_kode_apek) {
								$kode_analisa_new = $ks['kode_analisa'];
								$id_data_analisa_new = $ks['id_data_analisa'];
							}
						}
						$arr_apek_up = array(
							'kode_analisa' => $kode_analisa_new, 
							'id_data_analisa' => $id_data_analisa_new
						);

						$this->db->where('id_analisa_apek',$get_id_apek);
						$this->db->update('simpro_rap_analisa_apek',$arr_apek_up);
					}
				}	
			}

			echo "Data telah di-copy..";
		} else {
			echo "Tidak bisa meng-Copy Analisa apek,<br>Data yang anda pilih tidak mempunyai data item analisa apek..";
		}

		
	}

	function dateadd($per,$n,$d) {
		switch($per) {
			case "yyyy": $n*=12;
			case "m":
				$d=mktime(date("H",$d),date("i",$d)
						,date("s",$d),date("n",$d)+$n
						,date("j",$d),date("Y",$d));
			$n=0; break;
			case "ww": $n*=7;
			case "d": $n*=24;
			case "h": $n*=60;
			case "n": $n*=60;
		}
		return $d+$n;
	}
}