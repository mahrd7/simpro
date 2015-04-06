<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_admin');		
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function page($page)
	{
		switch($page)
		{
			case 'peran': $tbl_view = 'peran'; break;
			case 'master': $tbl_view = 'master'; break;
			case 'user': $tbl_view = 'user'; break;
			case 'unit_usaha': $tbl_view = 'unit_usaha'; break;
			case 'sumber_dana': $tbl_view = 'sumber_dana'; break;
			case 'pemilik': $tbl_view = 'pemilik'; break;
			case 'sbu': $tbl_view = 'sbu'; break;
			case 'daftar_peralatan': $tbl_view = 'daftar_peralatan'; break;
			case 'propinsi': $tbl_view = 'propinsi'; break;
			case 'direktorat': $tbl_view = 'direktorat'; break;
			case 'sumber_daya': $tbl_view = 'sumber_daya'; break;
			case 'input_pu_bk_lk': $tbl_view = 'input_pu_bk_lk'; break;
			case 'calendar': $tbl_view = 'calendar'; break;
			case 'master_toko': $tbl_view = 'master_toko'; break;
			case 'target_dashboard': $tbl_view = 'target_dashboard'; break;
			case 'sumber_daya_material': $tbl_view = 'sumber_daya_material'; break;
			default: $tbl_view = 'welcome_message'; break;
		}
		$this->load->view($tbl_view);
	}

	public function getdata($tbl_info){
		switch($tbl_info)
		{
			case 'master' : $tbl_get = 'tbl_satuan'; break;
			case 'sumber_dana' : $tbl_get = 'tbl_sumber_dana'; break;
			case 'pemilik' : $tbl_get = 'tbl_pemilik_proyek'; break;
			case 'sbu' : $tbl_get = 'tbl_sbu'; break;
			case 'sumber_daya' : $tbl_get = 'subbidang'; break;
			case 'daftar_peralatan' : $tbl_get = 'tbl_master_peralatan'; break;
			case 'target_dashboard' : $tbl_get = 'tbl_target_dashboard'; break;
			case 'unit_usaha' : $tbl_get = 'tbl_divisi'; break;
			case 'propinsi' : $tbl_get = 'tbl_propinsi'; break;
			case 'direktorat' : $tbl_get = 'tbl_direktorat'; break;
			case 'master_toko' : $tbl_get = 'tbl_toko'; break;
		}
		$data = $this->mdl_admin->getdata($tbl_get);
		echo '{"data":'.json_encode($data->result_object()).'}';
	}

	public function getdatasearch($tbl_info){
		switch($tbl_info)
		{
			case 'propinsi' : 
			$param = $_GET['param'];
			$tbl_get = 'tbl_propinsi';
			break;

			case 'unit_usaha' : 
			$param = $_GET['param'];
			$tbl_get = 'tbl_divisi_propinsi';
			break;

			case 'unit_usaha_pro' : 
			$param = '';
			$tbl_get = 'tbl_propinsi';
			break;
		}
		// $data = 
		$this->mdl_admin->getdatasearch($tbl_get,$param);
		// echo '{"data":'.json_encode($data->result_object()).'}';
	}

	public function insertdata($tbl_info){
		$tgl = date('m/d/Y');
		$user = $this->session->userdata('fullname');
		$divisi = $this->session->userdata('divisi');
		$waktu = date('H:i:s');
		$ip = $this->session->userdata('ip_address');
		// $divisi = $this->mdl_admin->getdivisi($kd);

		switch($tbl_info)
		{
			case 'master' :			
			$satuan = $_POST['satuan'];
			$tbl_get = 'tbl_satuan';
			$data = array(
				'satuan_nama' => $satuan, 
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;
			case 'sumber_dana' :					
			$kode = $_POST['kode'];
			$nama = $_POST['nama'];
			$tbl_get = 'tbl_sumber_dana';
			$data = array(
				'sumberdana_kode' => $kode,
				'sumberdana_nama' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;
			case 'pemilik' :					
			$kode = $_POST['kode'];
			$nama = $_POST['nama'];
			$tbl_get = 'tbl_pemilik_proyek';
			$data = array(
				'pemilik_kode' => $kode,
				'pemilik_nama' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;
			case 'sbu' :					
			$kode = $_POST['kode'];
			$nama = $_POST['nama'];
			$tbl_get = 'tbl_sbu';
			$data = array(
				'sbu_kode' => $kode,
				'sbu_nama' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;
			case 'sumber_daya' :					
			$kode = $_POST['kode'];
			$nama = $_POST['nama'];
			$tbl_get = 'subbidang';
			$data = array(
				'subbidang_kode' => $kode,
				'subbidang_name' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'daftar_peralatan' :					
			$uraian_jenis_alat = $_POST['uraian_jenis_alat'];
			$merk_model = $_POST['merk_model'];
			$type_penggerak = $_POST['type_penggerak'];
			$kapasitas = $_POST['kapasitas'];
			$tbl_get = 'tbl_master_peralatan';
			$no_spk = '';
			$data = array(
				'uraian_jenis_alat' => $uraian_jenis_alat,
				'merk_model' => $merk_model,
				'type_penggerak' => $type_penggerak,
				'kapasitas' => $kapasitas,
				'no_spk' => $no_spk,
				'user_tambah' => $user,
				'tgl' => $tgl
			); 
			break;

			case 'target_dashboard' :					
			$tahun = $_POST['tahun'];
			$jumlah = $_POST['jumlah'];
			$kategori = $_POST['kategori'];
			$tbl_get = 'tbl_target_dashboard';
			$data = array(
				'tahun' => $tahun,
				'jumlah' => $jumlah,
				'kategori' => $kategori
			); 
			break;

			case 'unit_usaha' :					
			$kode = $_POST['kode'];
			$nama = $_POST['nama'];
			$tbl_get = 'tbl_divisi';
			$data = array(
				'divisi_kode' => $kode,
				'divisi_name' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'propinsi' :					
			$kode = $_POST['kode'];
			$nama = $_POST['nama'];
			$tbl_get = 'tbl_propinsi';
			$data = array(
				'kode_propinsi' => $kode,
				'propinsi' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'direktorat' :					
			$kode = $_POST['kode'];
			$nama = $_POST['nama'];
			$tbl_get = 'tbl_direktorat';
			$data = array(
				'kode_dir' => $kode,
				'nama_dir' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'master_toko' :					
			$kode = $_POST['kode'];
			$nama = $_POST['nama'];					
			$alamat = $_POST['alamat'];
			$contact = $_POST['contact'];					
			$telp = $_POST['telp'];
			$produk = $_POST['produk'];
			$tbl_get = 'tbl_toko';
			$data = array(
				'toko_kode' => $kode,
				'toko_nama' => $nama,				
				'toko_alamat' => $alamat,
				'toko_contact' => $contact,
				'toko_telp' => $telp,
				'toko_produk' => $produk,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'kota' :					
			$kode = $_POST['kode'];
			$kota = $_POST['kota'];		
			$kategori = $_POST['kategori'];
			$tbl_get = 'tbl_propinsi';
			$data = array(
				'kode_propinsi' => $kode,
				'propinsi' => $kota,
				'propinsi_induk' => $kategori,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;
		}
		$this->mdl_admin->insertdata($tbl_get,$data);
	}

	public function deletedata($tbl_info){

		switch($tbl_info)
		{
			case 'master' : 
			$id = $_POST['satuan_nama'];
			$tbl_get = 'tbl_satuan'; 
			break;

			case 'sumber_dana' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_sumber_dana'; 
			break;

			case 'pemilik' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_pemilik_proyek'; 
			break;

			case 'sbu' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_sbu'; 
			break;

			case 'sumber_daya' : 
			$id = $_POST['id'];
			$tbl_get = 'subbidang'; 
			break;

			case 'daftar_peralatan' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_master_peralatan'; 
			break;

			case 'target_dashboard' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_target_dashboard'; 
			break;

			case 'unit_usaha' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_divisi'; 
			break;

			case 'propinsi' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_propinsi'; 
			break;

			case 'direktorat' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_direktorat'; 
			break;

			case 'master_toko' : 
			$id = $_POST['id'];
			$tbl_get = 'tbl_toko'; 
			break;
		}
		$this->mdl_admin->deletedata($tbl_get,$id);
	}

	public function editdata($tbl_info){		
		
		$tgl = date('m/d/Y');
		$user = $this->session->userdata('fullname');
		$divisi = $this->session->userdata('divisi');
		$waktu = date('H:i:s');
		$ip = $this->session->userdata('ip_address');
		// $divisi = $this->mdl_admin->getdivisi($kd);

		switch($tbl_info)
		{
			case 'master' :
			$id = $_POST['editid'];
			$satuan = $_POST['editsatuan'];
			$tbl_get = 'tbl_satuan';
			$data = array(
				'satuan_nama' => $satuan, 
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'sumber_dana' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$nama = $_POST['editnama'];
			$tbl_get = 'tbl_sumber_dana';
			$data = array(
				'sumberdana_kode' => $kode, 
				'sumberdana_nama' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'pemilik' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$nama = $_POST['editnama'];
			$tbl_get = 'tbl_pemilik_proyek';
			$data = array(
				'pemilik_kode' => $kode, 
				'pemilik_nama' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'sbu' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$nama = $_POST['editnama'];
			$tbl_get = 'tbl_sbu';
			$data = array(
				'sbu_kode' => $kode, 
				'sbu_nama' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'sumber_daya' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$nama = $_POST['editnama'];
			$tbl_get = 'subbidang';
			$data = array(
				'subbidang_kode' => $kode, 
				'subbidang_name' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'daftar_peralatan' :	
			$id = $_POST['editid'];		
			$alat_id = $_POST['edituraian_jenis_alat'];
			$uraian_jenis_alat = $_POST['edituraian_jenis_alat'];
			$merk_model = $_POST['editmerk_model'];
			$type_penggerak = $_POST['edittype_penggerak'];
			$kapasitas = $_POST['editkapasitas'];
			$tbl_get = 'tbl_master_peralatan';
			$no_spk = '';
			$data = array(
				'uraian_jenis_alat' => $uraian_jenis_alat,
				'merk_model' => $merk_model,
				'type_penggerak' => $type_penggerak,
				'kapasitas' => $kapasitas,
				'no_spk' => $no_spk,
				'user_tambah' => $user,
				'tgl' => $tgl
			); 
			break;

			case 'target_dashboard' :	
			$id = $_POST['editid'];					
			$tahun = $_POST['edittahun'];
			$jumlah = $_POST['editjumlah'];
			$kategori = $_POST['editkategori'];
			$tbl_get = 'tbl_target_dashboard';
			$data = array(
				'tahun' => $tahun,
				'jumlah' => $jumlah,
				'kategori' => $kategori
			); 
			break;

			case 'unit_usaha' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$nama = $_POST['editnama'];
			$tbl_get = 'tbl_divisi';
			$data = array(
				'divisi_kode' => $kode, 
				'divisi_name' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'propinsi' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$nama = $_POST['editnama'];
			$tbl_get = 'tbl_propinsi';
			$data = array(
				'kode_propinsi' => $kode, 
				'propinsi' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'direktorat' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$nama = $_POST['editnama'];
			$tbl_get = 'tbl_direktorat';
			$data = array(
				'kode_dir' => $kode, 
				'nama_dir' => $nama,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'master_toko' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$nama = $_POST['editnama'];					
			$alamat = $_POST['editalamat'];
			$contact = $_POST['editcontact'];					
			$telp = $_POST['edittelp'];
			$produk = $_POST['editproduk'];
			$tbl_get = 'tbl_toko';
			$data = array(
				'toko_kode' => $kode,
				'toko_nama' => $nama,				
				'toko_alamat' => $alamat,
				'toko_contact' => $contact,
				'toko_telp' => $telp,
				'toko_produk' => $produk,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;

			case 'kota' :
			$id = $_POST['editid'];
			$kode = $_POST['editkode'];
			$kota = $_POST['editkota'];		
			$kategori = $_POST['editkategori'];
			$tbl_get = 'tbl_propinsi';
			$data = array(
				'kode_propinsi' => $kode,
				'propinsi' => $kota,
				'propinsi_induk' => $kategori,
				'user_update' => $user,
				'tgl_update' => $tgl,
				'ip_update' => $ip,
				'divisi_update' => $divisi,
				'waktu_update' => $waktu
			); 
			break;
		}
		$this->mdl_admin->editdata($tbl_get,$data,$id);
	}

	function get_combo($param="")
	{
		switch ($param) {
			case 'divisi':
				$data = $this->get_data($select="divisi_id as value, trim(divisi_name) as text",$tbl="simpro_tbl_divisi","");
				echo json_encode(array('success' => true,'data' => $data->result_object()));
			break;
			case 'proyek':
				if ($this->input->get('param_proyek') and $this->input->get('param_proyek')!='') {
					$param_proyek = $this->input->get('param_proyek');
				} else {
					$param_proyek = 0;
				}
				$arr_proyek_where = array('divisi_kode' => $param_proyek,'bast_2 >='=> date('Y-m-d'));
				$data = $this->get_data($select="proyek_id as value,trim(proyek) as text",$tbl="simpro_tbl_proyek",$arr_proyek_where);
				echo json_encode(array('success' => true,'data' => $data->result_object()));
			break;
			case 'jabatan':
				$data = $this->get_data($select="id_jabatan as value,trim(jabatan) as text",$tbl="simpro_tbl_jabatan","");
				echo json_encode(array('success' => true,'data' => $data->result_object()));
			break;
			case 'peran':
				$data = $this->get_data($select="id_peran as value,trim(nama_peran) as text",$tbl="simpro_tbl_peran","");
				echo json_encode(array('success' => true,'data' => $data->result_object()));
			break;
			case 'user':
				$arr_user = array('user_id' => $this->session->userdata('uid'));
				$data = $this->get_data($select="*",$tbl="simpro_tbl_user",$arr_user);
				echo json_encode(array('success' => true,'data' => $data->row_array(),'no_spk'=>$data->row()->no_spk,'proyek_check'=>$data->row()->proyek_check));
			break;
		}
	}

	function get_data($select="",$tbl="",$where="")
	{
		$this->db->select($select);
		if ($where != '') {
			$this->db->where($where);
		}
		$q = $this->db->get($tbl);

		return $q;
	}

	function update_user()
	{
		$uid = $this->session->userdata('uid');

		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '204800';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);

		$q = $this->db->query("SELECT foto FROM simpro_tbl_user WHERE user_id= $uid")->row();

		if($_FILES['foto']['name'] != ''){

			if($q->foto != ''){
				unlink('./uploads/'.$q->foto);
			}

			if(!$this->upload->do_upload('foto'))
			{
				throw new Exception('File Gagal Upload, ukuran file tidak boleh lebih dari 2MB. Tipe file yg diperbolehkan: gif|jpg|png');
			} else {

				$data_so = $this->upload->data();

				$foto = $data_so['file_name'];
			}
		} else {
			$foto = $q->foto;
		}

		if ($this->input->post('proyek_check')) {
			$proyek_check = 'ALL';
		} else {
			$proyek_check = 'NOT ALL';
		}

		$where_uid = array('user_id' => $uid);

		$arr_user = array(
			'foto' => $foto,
			'kode_entitas' => $this->input->post('kode_entitas'),
			'no_spk' => $this->input->post('no_spk'),
			'proyek_check' => $proyek_check,
			'user_name' => $this->input->post('user_name'),
			// 'password' => $this->input->post('password'),
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'jabatan' => $this->input->post('jabatan'),
			'nip' => $this->input->post('nip'),
			'email' => $this->input->post('email'),
			'no_hp' => $this->input->post('no_hp'),
			'tanggal_masuk' => $this->input->post('tanggal_masuk'),
			'level_akses' => $this->input->post('level_akses'),
			'jenis_user' => 'ALL',
			'user_update' => $uid,
			'tgl_update' => date('Y-m-d'),
			'ip_update' => $this->session->userdata('ip_address'),
			'divisi_update' => $this->session->userdata('divisi_id'),
			'waktu_update' => date('H:i:s')
        );

        $where_uid_old =  array('trim(user_name)' => trim($this->input->post('user_name')));

        $arr_user_old = array(
			'foto' => $foto,
			'kode_entitas' => $this->input->post('kode_entitas'),
			'proyek_check' => $proyek_check,
			'user_name' => $this->input->post('user_name'),
			// 'password' => $this->input->post('password'),
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'nip' => $this->input->post('nip'),
			'email' => $this->input->post('email'),
			'no_hp' => $this->input->post('no_hp'),
			'tanggal_masuk' => $this->input->post('tanggal_masuk'),
			'level_akses' => $this->input->post('level_akses'),
			'jenis_user' => 'ALL',
        );

		if($this->db->update('simpro_tbl_user', $arr_user,$where_uid) && $this->db->update('tbl_user', $arr_user_old,$where_uid_old))
		{
			echo json_encode(array('success'=>true, 'message'=>'Data Umum Proyek berhasil diupdate.'));
		} else echo json_encode(array('success'=>true, 'message'=>'Data Umum Proyek GAGAL diupdate!'));
	}

	function get_material()
	{
		$search = $this->input->get('search');
		$cbo = $this->input->get('cbo');
		$start = $this->input->get('start');
		$limit = $this->input->get('limit');

		$sql_total = "select a.*,b.subbidang_name,c.divisi_name from simpro_tbl_detail_material a join simpro_tbl_subbidang b on a.subbidang_kode = b.subbidang_kode  join simpro_tbl_divisi c on a.divisi_update = c.divisi_id where lower(detail_material_nama) like lower('%$search%') and b.subbidang_kode like '%$cbo%'";
		$q_total = $this->db->query($sql_total);

		$sql_get_material = "select a.*,b.subbidang_name,c.divisi_name from simpro_tbl_detail_material a join simpro_tbl_subbidang b on a.subbidang_kode = b.subbidang_kode  join simpro_tbl_divisi c on a.divisi_update = c.divisi_id where lower(detail_material_nama) like lower('%$search%') and b.subbidang_kode like '%$cbo%' order by btrim(detail_material_nama) offset $start limit $limit";
		$q_get_material = $this->db->query($sql_get_material);

		if ($q_get_material->result()) {
			$data = $q_get_material->result_object();
			$total = $q_total->num_rows();
		} else {
			$data = '';
			$total = 0;
		}

		echo json_encode(array('data' => $data,'total' => $total));
	}

	function get_subbidang()
	{
		$q_subbidang = $this->db->query("select subbidang_kode as value, subbidang_name as text from simpro_tbl_subbidang order by subbidang_kode");
		if ($q_subbidang->result()) {
			$data = $q_subbidang->result_object();
		} else {
			$data = '';
		}

		echo json_encode(array('data' => $data));
	}

	function get_satuan()
	{
		$q_satuan = $this->db->query("select satuan_nama as value, satuan_nama as text from simpro_tbl_satuan order by satuan_nama");
		if ($q_satuan->result()) {
			$data = $q_satuan->result_object();
		} else {
			$data = '';
		}

		echo json_encode(array('data' => $data));
	}

	function material($param='')
	{
		if ($param == 'edit') {
			$id = $this->input->post('detail_material_id');
			$this->db->update('simpro_tbl_detail_material',$this->input->post(),array('detail_material_id' => $id));
		} elseif ($param == 'tambah') {
			$this->db->insert('simpro_tbl_detail_material',$this->input->post());
		} elseif ($param == 'hapus') {
			$id = $this->input->post('id');
			$this->db->delete('simpro_tbl_detail_material',array('detail_material_id' => $id));
		}
	}

	function get_last_material()
	{
		$subbidang = $this->input->post('subbidang');
		$kepala = substr($subbidang, 0, 3);
		$belakang = substr($subbidang, -1);
		$kd = $kepala.".". (strlen($subbidang) < (int)4?"0":$belakang) ."";
		$q_satuan = $this->db->query("select right(detail_material_kode,(length(detail_material_kode) - 5))::int + 1 as value from simpro_tbl_detail_material where detail_material_kode like '%$kd%' order by value desc limit 1");
		if ($q_satuan->result()) {
			$data = $kd.$q_satuan->row()->value;
		} else {
			$data = $kd."001";
		}

		echo $data;
	}
}