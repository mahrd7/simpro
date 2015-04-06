<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Main extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');	
		$this->load->library("nuSoap_lib");	
	}
	
	public function index()
	{	
		$this->load->helper('text');	
		$this->load->model('rencana/mdl_rencana');
		$divid = $this->session->userdata('divisi_id');
		$divisi = $this->session->userdata('divisi');
		$uname = $this->session->userdata('uname');
		$idtender = ($this->session->userdata('id_tender') > 0) ? $this->session->userdata('id_tender') : 0;
		$idproyek = ($this->session->userdata('proyek_id') > 0) ? $this->session->userdata('proyek_id') : 0;
		$data_tender = ($idtender > 0) ? $this->mdl_rencana->get_data_tender($idtender) : '';
		$data_proyek = ($idproyek > 0) ? $this->mdl_rencana->data_proyek($idproyek) : '';
		$out = array(
			'divisi' => $divisi,
			'username' => $uname,
			'nama_tender' => !empty($data_tender) ? character_limiter($data_tender['data']['nama_proyek'],40) : '',
			'nama_proyek' => !empty($data_proyek) ? character_limiter($data_proyek['data']['proyek'],40) : ''
		);		
		$this->load->view('dashboard', $out);
	}
	
	function set_proyek_id()
	{
		if($this->input->post('id_proyek'))
		{
			$proyek_id = $this->input->post('id_proyek');
			$sql_check = $this->db->query("select id_tender from simpro_tbl_proyek where proyek_id = $proyek_id")->row()->id_tender;
		   // var_dump($sql_check);
			if ($sql_check <> 0) {
				$pid = array(
					'id_tender' => $sql_check,
					'proyek_id' => $this->input->post('id_proyek')
					);
				$_SESSION['idtender'] = $sql_check;
				$_SESSION['proyek_id'] = $this->input->post('id_proyek');
				$this->session->set_userdata($pid);
			} else {
				$pid = array(
					'id_tender' => '',
					'proyek_id' => $this->input->post('id_proyek')
					);
				$_SESSION['idtender'] = '';
				$_SESSION['proyek_id'] = $this->input->post('id_proyek');
				$this->session->set_userdata($pid);
			}

			echo "Proyek Berhasil dipilih...";
		}
	}

	function hapus_proyek()
	{
		if($this->input->post('id_proyek'))
		{
			$this->db->trans_begin();

			$proyek_id = $this->input->post('id_proyek');

			$arr_proyek_id = array('proyek_id' => $proyek_id);
			$arr_id_proyek = array('id_proyek' => $proyek_id);
			$arr_id_proyek_sch = array('id_sch_proyek' => $proyek_id);
			$arr_id_proyek_guna_alat = array('id_guna_alat' => $proyek_id);

			$this->db->delete('simpro_tbl_approve',$arr_proyek_id);
			$this->db->delete('simpro_costogo_analisa_apek',$arr_id_proyek);
			$this->db->delete('simpro_costogo_analisa_asat',$arr_id_proyek);
			$this->db->delete('simpro_costogo_analisa_daftar',$arr_id_proyek);
			$this->db->delete('simpro_costogo_analisa_item_apek',$arr_id_proyek);
			$this->db->delete('simpro_costogo_item_tree',$arr_id_proyek);
			$this->db->delete('simpro_current_budget_analisa_apek',$arr_id_proyek);
			$this->db->delete('simpro_current_budget_analisa_asat',$arr_id_proyek);
			$this->db->delete('simpro_current_budget_analisa_daftar',$arr_id_proyek);
			$this->db->delete('simpro_current_budget_analisa_item_apek',$arr_id_proyek);
			$this->db->delete('simpro_current_budget_item_tree',$arr_id_proyek);
			$this->db->delete('simpro_rap_analisa_apek',$arr_id_proyek);
			$this->db->delete('simpro_rap_analisa_asat',$arr_id_proyek);
			$this->db->delete('simpro_rap_analisa_daftar',$arr_id_proyek);
			$this->db->delete('simpro_rap_analisa_item_apek',$arr_id_proyek);
			$this->db->delete('simpro_rap_item_tree',$arr_id_proyek);
			$this->db->delete('simpro_tbl_skbdn',$arr_proyek_id);
			$this->db->delete('simpro_tbl_rincian_rencana_pengadaan',$arr_proyek_id);
			$this->db->delete('simpro_tbl_checklist_dokumen',$arr_proyek_id);
			$this->db->delete('simpro_tbl_dokumen_proyek',$arr_proyek_id);
			$this->db->delete('simpro_tbl_sketsa_proyek',$arr_proyek_id);
			$this->db->delete('simpro_tbl_input_kontrak',$arr_proyek_id);
			$this->db->delete('simpro_tbl_kontrak_terkini',$arr_proyek_id);
			$this->db->delete('simpro_tbl_total_pekerjaan',$arr_proyek_id);			
			$this->db->delete('simpro_tbl_daftar_peralatan',$arr_proyek_id);
			$this->db->delete('simpro_tbl_rencana_kontrak_terkini',$arr_proyek_id);
			$this->db->delete('simpro_tbl_rpbk',$arr_proyek_id);
			$this->db->delete('simpro_tbl_mos',$arr_proyek_id);
			$this->db->delete('simpro_tbl_total_rkp',$arr_proyek_id);
			$this->db->delete('simpro_tbl_kkp',$arr_proyek_id);
			$this->db->delete('simpro_tbl_po2',$arr_proyek_id);
			$this->db->delete('simpro_tbl_cashin',$arr_proyek_id);
			$this->db->delete('simpro_tbl_pilih_toko',$arr_proyek_id);
			$this->db->delete('simpro_tbl_cashtodate',$arr_proyek_id);
			$this->db->delete('simpro_tbl_kladbank',$arr_proyek_id);
			$this->db->delete('simpro_tbl_hutangonkeu',$arr_proyek_id);
			$this->db->delete('simpro_tbl_hutang_proses',$arr_proyek_id);
			$this->db->delete('simpro_tbl_bayar_hutang',$arr_proyek_id);
			$this->db->delete('simpro_tbl_piutang',$arr_proyek_id);
			$this->db->delete('simpro_tbl_rencana_realisasi_mutu',$arr_proyek_id);
			$this->db->delete('simpro_tbl_daftar_risiko',$arr_proyek_id);
			$this->db->delete('simpro_tbl_penanganan_risiko',$arr_proyek_id);
			$this->db->delete('simpro_tbl_analisis_risiko',$arr_proyek_id);
			$this->db->delete('simpro_tbl_foto_proyek',$arr_proyek_id);
			$this->db->delete('simpro_tbl_dok_k3',$arr_proyek_id);
			$this->db->delete('simpro_tbl_inovasi',$arr_proyek_id);
			$this->db->delete('simpro_tbl_sch_proyek_parent',$arr_id_proyek_sch);
			$this->db->delete('simpro_tbl_sch_proyek_parent_alat',$arr_id_proyek_sch);
			$this->db->delete('simpro_tbl_sch_proyek_parent_bahan',$arr_id_proyek_sch);
			$this->db->delete('simpro_tbl_sch_proyek_parent_person',$arr_id_proyek_sch);
			$this->db->delete('simpro_tbl_guna_alat_parent',$arr_id_proyek_guna_alat);
			$this->db->delete('simpro_tbl_sch_proyek',$arr_proyek_id);
			$this->db->delete('simpro_tbl_sch_proyek_alat',$arr_proyek_id);
			$this->db->delete('simpro_tbl_sch_proyek_bahan',$arr_proyek_id);
			$this->db->delete('simpro_tbl_sch_proyek_person',$arr_proyek_id);
			$this->db->delete('simpro_tbl_guna_alat',$arr_proyek_id);

			$id_tender = $this->db->query("select * from simpro_tbl_proyek where proyek_id = $proyek_id")->row()->id_tender;
			$this->db->delete('simpro_rab_approve',array('id_tender' => $id_tender));

			$arr_up_p_tender = array('proyek_id' => null);
			$this->db->update('simpro_m_rat_proyek_tender',$arr_up_p_tender,array('id_proyek_rat' => $id_tender));

			$this->db->delete('simpro_tbl_proyek',$arr_proyek_id);

			if($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();		
				echo "Data Tender GAGAL dihapus!";					
			} else
			{
				$this->db->trans_commit();
				$idsess = $this->session->userdata('proyek_id');
				if( $idsess == $proyek_id) {
					unset($_SESSION['idtender']);
					unset($_SESSION['proyek_id']);					
					$items = array('id_tender' => '','proyek_id' => '');
					$this->session->unset_userdata($items);
				}
				echo "Proyek Berhasil dihapus...";
			}
		}
	}

	function set_tender_id()
	{
		if($this->input->post('tender_id'))
		{
			$idtender = $this->input->post('tender_id');
			$sql_check = $this->db->query("select proyek_id from simpro_tbl_proyek where id_tender = $idtender");

			if ($sql_check->result()) {
				$pid = array(
					'id_tender' => $this->input->post('tender_id'),
					'proyek_id' => $sql_check->row()->proyek_id
					);
				$_SESSION['idtender'] = $this->input->post('tender_id');
				$_SESSION['proyek_id'] = $sql_check;
				$this->session->set_userdata($pid);
			} else {				
				$pid = array(
					'id_tender' => $this->input->post('tender_id'),
					'proyek_id' => ''
					);
				$_SESSION['idtender'] = $this->input->post('tender_id');
				$_SESSION['proyek_id'] = '';
				$this->session->set_userdata($pid);
			}

			echo "Tender Berhasil dipilih...";
		}
	}
	
	function _dump($d)
	{
		print('<pre>');
		print_r($d);
		print('</pre>');
	}


	function generate_kumpulan_laporan()
	{
		$this->load->model('mdl_dashboard');

		if ($this->input->post('bulan') && $this->input->post('tahun')) {
			$tahun = $this->input->post('tahun');
			$bulan = $this->input->post('bulan');
			$this->mdl_dashboard->generate_kumpulan_laporan($tahun,$bulan);
		}
		// $tahun = date('Y');
		// $bulan = date('m');

	}

	function realisasi_pengendalian_proyek_divisi()
	{
		if ($this->input->get('kode')) {
			$div_kode = $this->input->get('kode');
			$q_r_proyek = $this->db->query("select *,
				case when pu_sd_bulanini = 0 or pu_awal = 0
				then 0
				else (pu_sd_bulanini/pu_awal) * 100
				end as prog,
				0 as perpu,
				0 as perbk from simpro_tbl_kumpulan_laporan a join simpro_m_status_pekerjaan b on a.sp = b.id_status_pekerjaan where divisi_kode=$div_kode order by sp asc");

			if ($q_r_proyek->result()) {
				$data = $q_r_proyek->result_object();
				$total = $q_r_proyek->num_rows();
			} else {
				$data = '';
				$total = '';
			}

			echo json_encode(array('total' => $total,'data' => $data));
		}
	}

	function realisasi_pengendalian_proyek()
	{
		$q_r_proyek = $this->db->query("select 
			'Realisasi Pengendalian Proyek' as grup,
			simpro_tbl_kumpulan_laporan.divisi_kode,
			unit_usaha,sum(kontrak_kini)as kontrak_kini,
			sum(pu_awal)as pu_awal,
			sum(bk_awal) as bk_awal,
			sum(laba_kotor)as laba_kotor,
			sum(pu_sd_bulanini)as pu_sd_bulanini,
			sum(bk_sd_bulanini)as bk_sd_bulanini,
			sum(selisihpu_bk) as selisihpu_bk,
			sum(mos) as mos,
			sum(laba_kotor_sd_blnini)as laba_kotor_sd_blnini,
			sum(cash_in) as cash_in,
			sum(cash_out)as cash_out,
			sum(sisa_anggaran) as sisa_anggaran,
			sum(pu_proyeksi) as pu_proyeksi,
			sum(bk_proyeksi) as bk_proyeksi,
			sum(mos_proyeksi) as mos_proyeksi,
			sum(laba_kotor_proyeksi)as laba_kotor_proyeksi,
			sum(deviasi) as deviasi 
			from simpro_tbl_divisi 
			join simpro_tbl_kumpulan_laporan  
			on simpro_tbl_kumpulan_laporan.divisi_kode = simpro_tbl_divisi.divisi_id
			and simpro_tbl_divisi.divisi_id!=21 
			group by simpro_tbl_kumpulan_laporan.divisi_kode,simpro_tbl_kumpulan_laporan.unit_usaha 
			order by simpro_tbl_kumpulan_laporan.unit_usaha");

		if ($q_r_proyek->result()) {
			$data = $q_r_proyek->result_object();
			$total = $q_r_proyek->num_rows();
		} else {
			$data = '';
			$total = '';
		}

		echo json_encode(array('total' => $total,'data' => $data));
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

	function get_divisi()
	{
		$divisi_id = $this->session->userdata('divisi_id'); 
		if ($divisi_id == 6 || $divisi_id == 21) {
			$q = $this->db->query("select divisi_id as value, divisi_name as name from simpro_tbl_divisi order by urut");
		} else {
			$q = $this->db->query("select divisi_id as value, divisi_name as name from simpro_tbl_divisi where divisi_id = $divisi_id order by urut");
		}
		if ($q->result()) {
			$data = $q->result_object();
		} else {
			$data = "";
		}
		
		$totdata = $q->num_rows();
		$dat = array('total'=>$totdata, 'success'=>true, 'data'=>$data);
		$this->_out($dat);	
	}

	function get_sbu()
	{
		$q = $this->db->query("select sbu_id as value, sbu_nama as name from simpro_tbl_sbu");
		if ($q->result()) {
			$data = $q->result_object();
		} else {
			$data = "";
		}
		$totdata = $q->num_rows();
		$dat = array('total'=>$totdata, 'success'=>true, 'data'=>$data);
		$this->_out($dat);	
	}

	function get_store_pekerjaan()
	{
		$q = $this->db->query("select id_status_pekerjaan as value, status_pekerjaan as name from simpro_m_status_pekerjaan");
		if ($q->result()) {
			$data = $q->result_object();
		} else {
			$data = "";
		}
		$totdata = $q->num_rows();
		$dat = array('total'=>$totdata, 'success'=>true, 'data'=>$data);
		$this->_out($dat);	
	}

	function get_ekskalasi()
	{
		$q = $this->db->query("select id_ekskalasi as value, ekskalasi as name from simpro_m_ekskalasi");
		if ($q->result()) {
			$data = $q->result_object();
		} else {
			$data = "";
		}
		$totdata = $q->num_rows();
		$dat = array('total'=>$totdata, 'success'=>true, 'data'=>$data);
		$this->_out($dat);	
	}

	function get_propinsi()
	{
		if ($this->input->get('divisi_id')) {
			$divisi_id = $this->input->get('divisi_id');

			if($divisi_id=='30'){
		        $kondisi="trim(substring(kode_provinsi from 1 for 2)) = '$divisi_id'";
		        $kondisi2='';
		    }elseif($divisi_id=='3'){
		        $kondisi="trim(substring(kode_provinsi from 1 for 1)) = '$divisi_id'";
		        $kondisi2="and trim(substring(kode_provinsi from 1 for 2)) !=30";
		    }elseif($divisi_id=='5'){
		        $kondisi="kode_provinsi != ''";
		        $kondisi2='';
		    }
		    else{
		        $kondisi="trim(substring(kode_provinsi from 1 for 1)) = '$divisi_id'";
		        $kondisi2='';
		    }

			$q = $this->db->query("select id_provinsi as value, nama_provinsi as name from simpro_tbl_provinsi where $kondisi order by nama_provinsi");
			
			if ($q->result()) {
				$data = $q->result_object();
			} else {
				$data = "";
			}
		} else {
			$data = "";
		}

		
		$totdata = $q->num_rows();
		$dat = array('total'=>$totdata, 'success'=>true, 'data'=>$data);
		$this->_out($dat);	
	}

	function get_status_proyek()
	{
		$q = $this->db->query("select id_proyek_status as value, status_proyek as name from simpro_tbl_status_proyek");
		if ($q->result()) {
			$data = $q->result_object();
		} else {
			$data = "";
		}
		$totdata = $q->num_rows();
		$dat = array('total'=>$totdata, 'success'=>true, 'data'=>$data);
		$this->_out($dat);	
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

	function ubah_pass(){
		$uid = $this->session->userdata('uid');
		$pass_lama = $this->input->post('password_lama');
		$pass_baru = $this->input->post('password_baru');
		$pass_baru_ulang = $this->input->post('ulang_password_baru');

		$q_cek_pass_lama = $this->db->query("select password,user_name from simpro_tbl_user where user_id = $uid");

		$p_lama = $q_cek_pass_lama->row()->password;
		if (trim($p_lama) == trim($pass_lama)) {
			if (trim($pass_baru) == trim($pass_baru_ulang)) {
				$arr_user_p = array(
					'password' => $pass_baru,
					'user_update' => $uid,
					'tgl_update' => date('Y-m-d'),
					'ip_update' => $this->session->userdata('ip_address'),
					'divisi_update' => $this->session->userdata('divisi_id'),
					'waktu_update' => date('H:i:s')
				);

				$arr_user_p_old = array(
					'password' => $pass_baru
				);

				$where_uid = array('user_id' => $uid);
				$where_uid_old = array('user_name' => $q_cek_pass_lama->row()->user_name);

				if ($this->db->update('simpro_tbl_user',$arr_user_p,$where_uid) && $this->db->update('tbl_user',$arr_user_p_old,$where_uid_old)){
					$array_items = array(
						'uid'  => '',
					  	'uname'     => '',
					  	'logdate'     => '',
					  	'fullname'     => '',
					   	'divisi'     => '',
					   	'divisi_id'     => '',
					   	'logged_in' => False
				   );
					$this->session->unset_userdata($array_items);
					echo json_encode(array('success'=>true,'message' => "Password Berhasil Diupdate..", 'status' => true));
				} else {
					echo json_encode(array('success'=>true,'message' => "Password Gagal Diupdate..", 'status' => false));
				}
			} else {
				echo json_encode(array('success'=>true,'message' => "Password Baru dan Ulang Password Baru Salah..", 'status' => false));
			}
		} else {
			echo json_encode(array('success'=>true,'message' => "Password Lama Salah..", 'status' => false));
		}
	} 

	function import_tender()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv|txt';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);					
		$this->db->query("TRUNCATE TABLE simpro_tmp_import_tender");
		
		try {
			if(!$this->upload->do_upload('upload_analisa'))
			{
				throw new Exception(sprintf('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: csv|txt.\nErrors: %s', $this->upload->display_errors()));
			} else
			{
				$data = $this->upload->data();
				# CSV HEADER
				$sql = sprintf("COPY simpro_tmp_import_tender FROM '%s' DELIMITER ';' ENCODING 'LATIN1' CSV HEADER", realpath("./uploads/".$data['file_name']));
				if($this->db->query($sql))
				{
					$sql_cek_nama_tender = "select
						nama_proyek
						from
						simpro_m_rat_proyek_tender a
						join simpro_tmp_import_tender b
						on trim(lower(a.nama_proyek)) = trim(lower(b.proyek_nama_proyek))";

					$q_cek_nama_tender = $this->db->query($sql_cek_nama_tender);
					if ($q_cek_nama_tender->num_rows() > 0) {
						$nt = $q_cek_nama_tender->row()->nama_proyek;
						$nama_proyek = "'".$nt."_1'";
					} else {
						$nama_proyek = "proyek_nama_proyek";
					}

					$sql_insert_tender = "with p as (
						insert into simpro_m_rat_proyek_tender (
						nama_proyek,
						id_status_rat,
						lingkup_pekerjaan,
						waktu_pelaksanaan,
						waktu_pemeliharaan,
						nilai_pagu_proyek,
						lokasi_proyek,
						pemilik_proyek,
						konsultan_pelaksana,
						konsultan_pengawas,
						tanggal_tender,
						nilai_penawaran,
						divisi_id,
						tgl_update_status,
						keterangan,
						user_entry,
						divisi_name,
						xlong,
						xlat,
						mulai,
						akhir,
						nilai_kontrak_ppn,
						nilai_kontrak_excl_ppn,
						peta_lokasi_proyek,
						jenis_proyek
						)
						select
						distinct(".$nama_proyek."),
						proyek_id_status_rat::int,
						proyek_lingkup_pekerjaan,
						proyek_waktu_pelaksanaan::int,
						proyek_waktu_pemeliharaan::int,
						proyek_nilai_pagu_proyek::bigint,
						proyek_lokasi_proyek,
						proyek_pemilik_proyek,
						proyek_konsultan_pelaksana,
						proyek_konsultan_pengawas,
						proyek_tanggal_tender::date,
						proyek_nilai_penawaran::numeric,
						proyek_divisi_id::int,
						proyek_tgl_update_status::date,
						proyek_keterangan,
						proyek_user_entry,
						proyek_divisi_name,
						proyek_xlong,
						proyek_xlat,
						proyek_mulai::date,
						proyek_akhir::date,
						proyek_nilai_kontrak_ppn::numeric,
						proyek_nilai_kontrak_excl_ppn::numeric,
						proyek_peta_lokasi_proyek,
						proyek_jenis_proyek::oid
						from
						simpro_tmp_import_tender returning id_proyek_rat
						)
						select id_proyek_rat from p";
					
					$get_id_tender = $this->db->query($sql_insert_tender);

					$id_tender = $get_id_tender->row()->id_proyek_rat;

					$sql_insert_rat_item_tree = "insert into simpro_rat_item_tree (
						kode_tree,
						id_proyek_rat,
						tree_item,
						tree_satuan,
						volume,
						tree_parent_kode
						)
						select
						distinct(tree_kode_tree),
						".$id_tender.",
						tree_tree_item,
						tree_tree_satuan,
						tree_volume::numeric,
						tree_tree_parent_kode
						from
						simpro_tmp_import_tender
						order by tree_kode_tree";
						
					

					$this->db->query($sql_insert_rat_item_tree);

					$this->update_id_rat_tree($id_tender);
					$this->set_satuan_induk($id_tender);

					$sql_insert_item_apek = "insert into simpro_rat_analisa_item_apek (
						rat_item_tree,
						id_proyek_rat,
						kode_analisa,
						harga,
						kode_tree
						)
						select 
						distinct(a.rat_item_tree),
						a.id_proyek_rat,
						b.ia_kode_analisa,
						0,
						a.kode_tree 
						from simpro_rat_item_tree a 
						join simpro_tmp_import_tender b on a.kode_tree = b.tree_kode_tree 
						where a.id_proyek_rat = $id_tender and b.ia_kode_analisa is not null";

					$this->db->query($sql_insert_item_apek);

					$sql_insert_daftar = "insert into simpro_rat_analisa_daftar (
						kode_analisa,  
						id_tender,
						id_kat_analisa,
						nama_item,
						id_satuan
						)
						select 
						distinct(ia_kode_analisa),
						$id_tender,
						da_id_kat_analisa::int,
						da_nama_item,
						da_id_satuan::int
						from simpro_tmp_import_tender
						where ia_kode_analisa is not null";

					$this->db->query($sql_insert_daftar);

					$sql_insert_asat = "insert into simpro_rat_analisa_asat (
						id_tender,
						kode_material,
						koefisien,
						harga,
						kode_analisa,
						kode_rap,
						keterangan
						)
						select 
						$id_tender,
						replace(asat_kode_material,'-','.'),
						asat_koefisien::numeric,
						asat_harga::numeric,
						ia_kode_analisa,
						asat_kode_rap,
						asat_keterangan
						from simpro_tmp_import_tender
						where asat_kode_material is not null
						group by asat_kode_material,
						asat_koefisien,
						asat_harga,
						ia_kode_analisa,
						asat_kode_rap,
						asat_keterangan";

					$this->db->query($sql_insert_asat);

					$sql_insert_apek = "insert into simpro_rat_analisa_apek (
						id_tender,
						kode_analisa,
						koefisien,
						harga,
						parent_kode_analisa
						)
						select 
						$id_tender,
						pek_parent_kode_analisa,
						apek_koefisien::numeric,
						apek_harga::numeric,
						ia_kode_analisa
						from simpro_tmp_import_tender
						where pek_parent_kode_analisa is not null
						group by pek_parent_kode_analisa,
						apek_koefisien,
						apek_harga,
						ia_kode_analisa";

					$this->db->query($sql_insert_apek);

					$this->update_item_apek($id_tender);
					$this->update_asat($id_tender);
					$this->update_apek($id_tender);

					$sql_insert_bu = "insert into simpro_t_rat_idc_biaya_umum (
						kode_material,
						id_proyek_rat,
						icitem,
						icvolume,
						icharga,
						satuan_id,
						satuan
						)
						SELECT 
						distinct(bu_kode_material),
						$id_tender,
						bu_icitem,
						bu_icvolume::numeric,
						bu_icharga::numeric,
						bu_satuan_id::int,
						bu_satuan
						FROM 
						simpro_tmp_import_tender";

					$sql_insert_bank = "insert into simpro_t_rat_idc_bank (
						kode_material,
						id_proyek_rat,
						icitem_bank,
						persentase,
						id_satuan,
						harga,
						satuan
						)
						SELECT 
						distinct(bank_kode_material),
						$id_tender,
						bank_icitem_bank,
						bank_persentase::numeric,
						bank_id_satuan::int,
						bank_harga::numeric,
						bank_satuan
						FROM 
						simpro_tmp_import_tender";

					$sql_insert_asuransi = "insert into simpro_t_rat_idc_asuransi (
						kode_material,
						id_proyek_rat,
						icitem_asuransi,
						id_satuan,
						persentase,
						harga,
						satuan
						)
						SELECT 
						distinct(asuransi_kode_material),
						$id_tender,
						asuransi_icitem_asuransi,
						asuransi_id_satuan::int,
						asuransi_persentase::numeric,
						asuransi_harga::numeric,
						asuransi_satuan
						FROM 
						simpro_tmp_import_tender";

					$sql_insert_vc = "insert into simpro_t_rat_varcost (
						id_varcost_item,
						id_proyek_rat,
						persentase
						)
						SELECT 
						distinct(vc_id_varcost_item::int),
						$id_tender,
						vc_persentase::numeric
						FROM 
						simpro_tmp_import_tender";
						
					$this->db->query($sql_insert_bu);
					$this->db->query($sql_insert_bank);
					$this->db->query($sql_insert_asuransi);
					$this->db->query($sql_insert_vc);
					
					$this->db->query("TRUNCATE TABLE simpro_tmp_import_tender");
					// if($this->db->query($sql_insert)) 
					// {
					// 	$this->db->query("TRUNCATE TABLE simpro_tmp_import_rat");
						echo json_encode(
							array(	"success"=>true, 
									"message"=>"Data berhasil di Import..", 
									"file" => $data['file_name'])
						);
					// } else echo json_encode(array("success"=>true, 
					// 		"message"=>"Data GAGAL diupload.", 
					// 		"file" => $data['file_name']));
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

	function update_id_rat_tree($id_tender)
	{
		$sql_update_rat_tree = "update simpro_rat_item_tree set
			tree_parent_id = x.id_parent
			from (select *,
			coalesce((
			select distinct(rat_item_tree) from simpro_rat_item_tree b
			where b.id_proyek_rat = a.id_proyek_rat and a.tree_parent_kode = b.kode_tree
			),0) as id_parent from
			simpro_rat_item_tree a
			where a.id_proyek_rat = $id_tender 
			order by kode_tree) x
			where simpro_rat_item_tree.rat_item_tree = x.rat_item_tree";

		$this->db->query($sql_update_rat_tree);
	}

	function set_satuan_induk($idtender)
	{
		$sql_set_satuan_induk = "
		update simpro_rat_item_tree set
		tree_satuan = 'Ls',
		volume = 1
		from (
		select * from (select
		a.*,
		(SELECT 
		count('a') as count
		FROM simpro_rat_item_tree
		WHERE id_proyek_rat = a.id_proyek_rat
		and tree_parent_id = a.rat_item_tree) as count
		from
		simpro_rat_item_tree a
		where a.id_proyek_rat = $idtender) n
		where n.count != 0
		) x
		where simpro_rat_item_tree.rat_item_tree = x.rat_item_tree
		";

		$this->db->query($sql_set_satuan_induk);
	}

	function update_item_apek($idtender)
	{
		$sql = "update simpro_rat_analisa_item_apek set
		id_data_analisa = a.id_data_analisa
		from (
		select 
		a.id_analisa_item_apek,
		b.id_data_analisa
		from simpro_rat_analisa_item_apek a
		join simpro_rat_analisa_daftar b on a.id_proyek_rat = b.id_tender and a.kode_analisa = b.kode_analisa 
		where a.id_proyek_rat = $idtender) a
		where simpro_rat_analisa_item_apek.id_proyek_rat = $idtender and simpro_rat_analisa_item_apek.id_analisa_item_apek = a.id_analisa_item_apek";

		$this->db->query($sql);
	}

	function update_asat($idtender)
	{
		$sql = "update simpro_rat_analisa_asat set
		id_detail_material = a.detail_material_id,
		id_data_analisa = a.id_data_analisa
		from (
		select
		a.id_analisa_asat,
		b.id_data_analisa,
		c.detail_material_id
		from simpro_rat_analisa_asat a
		join simpro_rat_analisa_daftar b on a.kode_analisa = b.kode_analisa and a.id_tender = b.id_tender
		join simpro_tbl_detail_material c on a.kode_material = c.detail_material_kode
		where a.id_tender = $idtender) a
		where simpro_rat_analisa_asat.id_tender = $idtender and simpro_rat_analisa_asat.id_analisa_asat = a.id_analisa_asat";

		$this->db->query($sql);
	}

	function update_apek($idtender)
	{
		$sql = "update simpro_rat_analisa_apek set
		id_data_analisa = a.id_data,
		parent_id_analisa = a.id_data_parent
		from (
		select
		a.id_analisa_apek,
		(select id_data_analisa from simpro_rat_analisa_daftar where id_tender = a.id_tender and kode_analisa = a.kode_analisa) as id_data,
		(select id_data_analisa from simpro_rat_analisa_daftar where id_tender = a.id_tender and kode_analisa = a.parent_kode_analisa) as id_data_parent
		from simpro_rat_analisa_apek a
		where a.id_tender = $idtender) a
		where simpro_rat_analisa_apek.id_tender = $idtender and simpro_rat_analisa_apek.id_analisa_apek = a.id_analisa_apek";

		$this->db->query($sql);
	}

	function cek_proyek_online($page='',$no_spk='')
	{
      $url_soap = $this->get_url('soap');
      $client = new SoapClient($url_soap);

		switch ($page) {
			case 'get_proyek':
				$q_cek_spk_s = json_decode($client->query("select count('a') as count from tbl_proyek where no_spk = '$no_spk'"),true);
				if ($q_cek_spk_s['data'][0]['count'] == 0) {
					$message = 'Proyek tidak ditemukan..';
					$proyek_cek = false;
				} else {
					$message = 'Proyek Ok silahkan masukan Username dan Password';
					$proyek_cek = true;
				}
    			echo json_encode(array("success"=>true,"message" => $message,"proyek_cek"=>$proyek_cek,"no_spk"=>$no_spk));
			break;
			case 'match_userpass':
				$no_spk = $this->input->post('no_spk');
				$username = $this->input->post('username');
				$password = $this->input->post('password');

				$q_cek_spk_s = json_decode($client->query("select count('a') as count from tbl_user where trim(user_name) = trim('$username') and trim(password) = trim('$password')"),true);
    			if ($q_cek_spk_s['data'][0]['count'] == 0) {
    				$message = "Username dan Password Salah..";
    			} else {
    				$q_get_proyek = "select count('a') as count from (select 
						unnest(string_to_array(no_spk, ',', ',')) as no_spk
						from tbl_user where lower(trim(user_name)) = lower(trim('$username')) and lower(trim(password)) = lower(trim('$password'))) x where lower(trim(no_spk)) = lower(trim('$no_spk'))";
    				$q_cek_spk_s = json_decode($client->query($q_get_proyek),true); 
    				// $message = $no_spk;
    				if ($q_cek_spk_s['data'][0]['count'] == 0) {
    					$message = $username." tidak mempunyai hak akses untuk proyek dengan No SPK ".$no_spk;
    				} else {
    					$this->sync_proyek_online($client,$no_spk);
    					$message = "Import Proyek Online Berhasil..";
    				}
    			}
    			echo json_encode(array("success"=>true,"message" => $message));
			break;
			default:
				echo "Perintah Tidak Dikenal..";
			break;
		}
	}

	function get_url($url)
    {
      if ($url == 'soap') {
        // $val = "http://simpro.nindyakarya.co.id/simpro-d/sync/index.php?wsdl";
         $val = "http://localhost/simpro/sinkronisasi_buat_server_simpro/index.php?wsdl";
      } elseif ($url == 'con') {
        // $val = 'http://simpro.nindyakarya.co.id/';
        $val = 'http://localhost/simpro/';
      }

      return $val;
    }

    function is_connected()
    {
    	$url_con = $this->get_url('con');
    	if ($this->is_valid_url($url_con))
    	{
    		$no_spk = $this->input->post('no_spk');
    		$this->cek_proyek_online('get_proyek',$no_spk);
    	}
    	else
    	{
    		$con = 'false';
    		echo json_encode(array("success"=>true,"message" => "Tidak Terkoneksi.."));
    	}
    }

    function is_valid_url($url)
    {
    	if (!($url = @parse_url($url)))
    	{
    		return false;
    	}

    	$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
    	$url['path'] = (!empty($url['path'])) ? $url['path'] : '/';
    	$url['path'] .= (isset($url['query'])) ? "?$url[query]" : '';

    	if (isset($url['host']) AND $url['host'] != @gethostbyname($url['host']))
    	{
    		if (PHP_VERSION >= 5)
    		{
    			$headers = @implode('', @get_headers("$url[scheme]://$url[host]:$url[port]$url[path]"));
    		}
    		else
    		{
    			if (!($fp = @fsockopen($url['host'], $url['port'], $errno, $errstr, 10)))
    			{
    				return false;
    			}
    			fputs($fp, "HEAD $url[path] HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
    			$headers = fread($fp, 4096);
    			fclose($fp);
    		}
    		return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
    	}
    	return false;
    }

	function sync_proyek_online($client,$no_spk)
	{
		ini_set('max_execution_time', 3600);

		$this->db->trans_begin();

		$q_get_data_umum = json_decode($client->query("select * from tbl_proyek where no_spk = '$no_spk'"),true);

		if ($q_get_data_umum['data']) {
			foreach ($q_get_data_umum['data'] as $r_proyek) {
				$arr_proyek = array(
					'proyek' => $r_proyek['proyek'],
					'lingkup_pekerjaan' => $r_proyek['lingkup_pekerjaan'],
					'proyek_alamat' => $r_proyek['proyek_alamat'],
					'proyek_telp' => $r_proyek['proyek_telp'],
					'lokasi_proyek' => $r_proyek['lokasi_proyek'],
					'pemberi_kerja' => $r_proyek['pemberi_kerja'],
					'kepala_proyek' => $r_proyek['kepala_proyek'],
					'proyek_konsultan_pengawas' => $r_proyek['proyek_konsultan_pengawas'],
					'no_spk' => $r_proyek['no_spk'],
					'nilai_kontrak_ppn' => $r_proyek['nilai_kontrak_ppn'],
					'nilai_kontrak_non_ppn' => $r_proyek['nilai_kontrak_non_ppn'],
					'proyek_nama_sumber_1' => $r_proyek['proyek_nama_sumber_1'],
					'proyek_nilai_sumber_1' => $r_proyek['proyek_nilai_sumber_1'],
					'bast_1' => $r_proyek['bast_1'],
					'bast_2' => $r_proyek['bast_2'],
					'uang_muka' => $r_proyek['uang_muka'],
					'termijn' => $r_proyek['termijn'],
					'retensi' => $r_proyek['retensi'],
					'jaminan_pelaksanaan' => $r_proyek['jaminan_pelaksanaan'],
					'asuransi_pekerjaan' => $r_proyek['asuransi_pekerjaan'],
					'denda_minimal' => $r_proyek['denda_minimal'],
					'denda_maksimal' => $r_proyek['denda_maksimal'],
					'sifat_kontrak' => $r_proyek['sifat_kontrak'],
					'manajemen_pelaksanaan' => $r_proyek['manajemen_pelaksanaan'],
					'eskalasi' => $r_proyek['eskalasi'],
					'beda_kurs' => $r_proyek['beda_kurs'],
					'pek_tambah_kurang' => $r_proyek['pek_tambah_kurang'],
					'rap_usulan' => $r_proyek['rap_usulan'],
					'rap_ditetapkan' => $r_proyek['rap_ditetapkan'],
					'mulai' => $r_proyek['mulai'],
					'berakhir' => $r_proyek['berakhir'],
					'jangka_waktu' => $r_proyek['jangka_waktu'],
					'perpanjangan_waktu' => $r_proyek['perpanjangan_waktu'],
					'masa_pemeliharaan' => $r_proyek['masa_pemeliharaan'],
					'total_waktu_pelaksanaan' => $r_proyek['total_waktu_pelaksanaan'],
					'tgl_tender' => $r_proyek['tgl_tender'],
					'tgl_pengumuman' => $r_proyek['tgl_pengumuman'],
					'user_update' => $this->session->userdata('uid'),
					'tgl_update' => date('Y-m-d'),
					'ip_update' => $this->session->userdata('ip_address'),
					'divisi_update' => $this->session->userdata('divisi_id'),
					'waktu_update' => date('H:i:s'),
					'wo' => $r_proyek['wo'],
					'kode_wilayah' => $r_proyek['kode_wilayah'],
					'kode_propinsi1' => $r_proyek['kode_propinsi1'],
					'no_kontrak' => $r_proyek['no_kontrak'],
					'status_pekerjaan' => $r_proyek['status_pekerjaan'],
					'pph_final' => $r_proyek['pph_final'],
					'struktur_organisasi' => $r_proyek['struktur_organisasi'],
					'sketsa_proyek' => $r_proyek['sketsa_proyek'],
					'lokasi_latitude' => $r_proyek['lokasi_latitude'],
					'lokasi_longitude' => $r_proyek['lokasi_longitude'],
					'no_kontrak2' => $r_proyek['no_kontrak2'],
					'no_spk_2' => $r_proyek['no_spk_2'],
					'id_excel' => $r_proyek['id_excel'],
					'sts_pekerjaan' => $r_proyek['sts_pekerjaan'],
					'sbu_kode' => $this->get_think('sbu_id','simpro_tbl_sbu',array('sbu_kode' => $r_proyek['sbu_kode']))->row()->sbu_id,
					'divisi_kode' => $this->get_think('divisi_id','simpro_tbl_divisi',array('divisi_kode' => $r_proyek['divisi_kode']))->row()->divisi_id,
					'proyek_status' => $this->get_think('id_proyek_status','simpro_tbl_status_proyek',array('status_proyek' => $r_proyek['proyek_status']))->row()->id_proyek_status,
					'propinsi' => ($this->get_think('id_provinsi','simpro_tbl_provinsi',array('kode_provinsi' => $r_proyek['propinsi']))->result()) ? $this->get_think('id_provinsi','simpro_tbl_provinsi',array('kode_provinsi' => $r_proyek['propinsi']))->row()->id_provinsi : null 
				);

				$this->db->insert('simpro_tbl_proyek',$arr_proyek);
			}
		}

		$proyek_id = $this->db->insert_id();

		$q_get_rab = json_decode($client->query("select * from tbl_input_kontrak where no_spk = '$no_spk'"),true);
		if ($q_get_rab['data']) {
			foreach ($q_get_rab['data'] as $r_rab) {
				$arr_rab = array(
					'tahap_kode_kendali' => $r_rab['tahap_kode_kendali'],
					'tahap_nama_kendali' => $r_rab['tahap_nama_kendali'],
					'proyek_id' => $proyek_id,
					'tahap_volume_kendali' => $r_rab['tahap_volume_kendali'],
					'tahap_harga_satuan_kendali' => $r_rab['tahap_harga_satuan_kendali'],
					'tahap_total_kendali' => $r_rab['tahap_total_kendali'],
					'tahap_kode_induk_kendali' => $r_rab['tahap_kode_induk_kendali'],
					'user_update' => $this->session->userdata('uid'),
					'tgl_update' => date('Y-m-d'),
					'ip_update' => $this->session->userdata('ip_address'),
					'divisi_update' => $this->session->userdata('divisi_id'),
					'waktu_update' => date('H:i:s'),
					'tahap_satuan_kendali' => $r_rab['tahap_satuan_kendali'],
				);

				$this->db->insert('simpro_tbl_input_kontrak',$arr_rab);
			}

			$this->update_tree_parent_id_rab($proyek_id);
			$this->set_satuan_induk_rab($proyek_id);
		}

		$q_get_rap = json_decode($client->query("select * from tbl_tahap_kendali where no_spk = '$no_spk'"),true);

		if ($q_get_rap['data']) {
			foreach ($q_get_rap['data'] as $r_rap) {

				$arr_item_tree = array(
					'id_proyek' => $proyek_id,
					'kode_tree' => $r_rap['tahap_kode_kendali'],
					'tree_item' => $r_rap['tahap_nama_kendali'],
					'tree_satuan' => $r_rap['tahap_satuan_kendali'],
					'volume' => $r_rap['tahap_volume_kendali'],
					'tree_parent_kode' => $r_rap['tahap_kode_induk_kendali']
				);

				$this->db->insert('simpro_rap_item_tree',$arr_item_tree);

				$rap_item_tree = $this->db->insert_id();

				$q_cek_analisa = json_decode($client->query("select * from tbl_komposisi_kendali where no_spk = '$no_spk' and tahap_kode_kendali = '".$r_rap['tahap_kode_kendali']."'"),true);
				if ($q_cek_analisa['data']) {
					$last_kode_analisa = $this->get_last_kode_analisa($proyek_id);
					$arr_daftar_analisa = array(
						'kode_analisa' => $last_kode_analisa,
						'id_kat_analisa' => 10,
						'nama_item' => $r_rap['tahap_nama_kendali'],
						'id_satuan' => ($this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_rap['tahap_satuan_kendali']))))->result()) ? $this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_rap['tahap_satuan_kendali']))))->row()->satuan_id : 31,
						'id_proyek' => $proyek_id
					);

					$this->db->insert('simpro_rap_analisa_daftar',$arr_daftar_analisa);

					$id_data_analisa = $this->db->insert_id();

					$q_k_kendali = json_decode($client->query("select distinct(detail_material_kode) as d,* from tbl_komposisi_kendali where no_spk = '$no_spk' and tahap_kode_kendali = '".$r_rap['tahap_kode_kendali']."'"),true);
					if ($q_k_kendali['data']) {
						foreach ($q_k_kendali['data'] as $r_komposisi_kendali) {
							$arr_where_asat = array(
								'id_data_analisa' => $id_data_analisa, 
								'kode_material' => $r_komposisi_kendali['detail_material_kode'], 
								'id_detail_material' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_komposisi_kendali['detail_material_kode']))->row()->detail_material_id, 
								'kode_analisa' => $last_kode_analisa, 
								'id_proyek' => $proyek_id
							);	
							$this->db->where($arr_where_asat);
							$q_cek_asat_l = $this->db->get('simpro_rap_analisa_asat');
								if ($q_cek_asat_l->num_rows() == 0) {
								$arr_asat = array(
									'id_data_analisa' => $id_data_analisa,
									'kode_material' => $r_komposisi_kendali['detail_material_kode'],
									'id_detail_material' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_komposisi_kendali['detail_material_kode']))->row()->detail_material_id,
									'koefisien' => $r_komposisi_kendali['komposisi_koefisien_kendali'],
									'harga' => $r_komposisi_kendali['komposisi_harga_satuan_kendali'],
									'kode_analisa' => $last_kode_analisa,
									'id_proyek' => $proyek_id,
									'keterangan' => $r_komposisi_kendali['keterangan'],
		  							'kode_rap' => $r_komposisi_kendali['kode_rap']
								);

								$this->db->insert('simpro_rap_analisa_asat',$arr_asat);
							}					
						}
					}

					$arr_item_apek = array(
						'id_proyek' => $proyek_id,
						'id_data_analisa' => $id_data_analisa,
						'kode_analisa' => $last_kode_analisa,
						'harga' => 0,
						'rap_item_tree' => $rap_item_tree,
						'kode_tree' => $r_rap['tahap_kode_kendali']
					);

					$this->db->insert('simpro_rap_analisa_item_apek',$arr_item_apek);
				}
			}
			
			$this->update_kode_rap($proyek_id);
			$this->update_tree_parent_id_rap($proyek_id);
		}

		$q_get_kontrak_kini_s = json_decode($client->query("select * from tbl_kontrak_terkini where no_spk = '$no_spk'"),true);
		if ($q_get_kontrak_kini_s['data']) {
			foreach ($q_get_kontrak_kini_s['data'] as $r_kontrak_kini) {
				$arr_kontrak_kini = array(
					'tahap_kode_kendali' => $r_kontrak_kini['tahap_kode_kendali'],
					'tahap_nama_kendali' => $r_kontrak_kini['tahap_nama_kendali'],
					'tahap_volume_kendali' => $r_kontrak_kini['tahap_volume_kendali'],
					'tahap_harga_satuan_kendali' => $r_kontrak_kini['tahap_harga_satuan_kendali'],
					'tahap_total_kendali' => $r_kontrak_kini['tahap_total_kendali'],
					'tahap_kode_induk_kendali' => $r_kontrak_kini['tahap_kode_induk_kendali'],
					'tahap_tanggal_kendali' => $r_kontrak_kini['tahap_tanggal_kendali'],
					'tgl_update' => date('Y-m-d'),
					'ip_update' => $this->session->userdata('ip_address'),
					'divisi_update' => $this->session->userdata('divisi_id'),
					'waktu_update' => date('H:i:s'),
					'tahap_volume_kendali_new' => $r_kontrak_kini['tahap_volume_kendali_new'],
					'tahap_total_kendali_new' => $r_kontrak_kini['tahap_total_kendali_new'],
					'tahap_harga_satuan_kendali_new' => $r_kontrak_kini['tahap_harga_satuan_kendali_new'],
					'tahap_volume_kendali_kurang' => $r_kontrak_kini['tahap_volume_kendali_kurang'],
					'tgl_rencana_aak' => $r_kontrak_kini['tgl_rencana_aak'],
					'volume_rencana' => $r_kontrak_kini['volume_rencana'],
					'volume_rencana1' => $r_kontrak_kini['volume_rencana1'],
					'volume_eskalasi' => $r_kontrak_kini['volume_eskalasi'],
					'harga_satuan_eskalasi' => $r_kontrak_kini['harga_satuan_eskalasi'],
					'rencana_volume_eskalasi' => $r_kontrak_kini['rencana_volume_eskalasi'],
					'rencana_harga_satuan_eskalasi' => $r_kontrak_kini['rencana_harga_satuan_eskalasi'],
					'is_nilai' => $r_kontrak_kini['is_nilai'],
					'tahap_total_kendali_kurang' => $r_kontrak_kini['tahap_total_kendali_kurang'],
					'total_tambah_kurang' => $r_kontrak_kini['total_tambah_kurang'],
					'tot_rencana1' => $r_kontrak_kini['tot_rencana1'],
					'tot_rencana2' => $r_kontrak_kini['tot_rencana2'],
					'vol_tambah_kurang' => $r_kontrak_kini['vol_tambah_kurang'],
					'proyek_id' => $proyek_id,
					'user_update' => $this->session->userdata('uid'),
					'tahap_satuan_kendali' => ($this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_kontrak_kini['tahap_satuan_kendali']))))->result()) ? $this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_kontrak_kini['tahap_satuan_kendali']))))->row()->satuan_id : 31,
					'tgl_akhir' => $r_kontrak_kini['tahap_tanggal_kendali']
				);

				$this->db->insert('simpro_tbl_kontrak_terkini',$arr_kontrak_kini);

				$kontrak_kini_id = $this->db->insert_id();

				$q_get_lpf = json_decode($client->query("select * from tbl_total_pekerjaan where no_spk = '$no_spk' and tahap_kode_kendali = '".$r_kontrak_kini['tahap_kode_kendali']."' and tahap_tanggal_kendali = '".$r_kontrak_kini['tahap_tanggal_kendali']."'"),true);
				if ($q_get_lpf['data']) {
					foreach ($q_get_lpf['data'] as $r_lpf) {
						$arr_lpf = array(
							'proyek_id' => $proyek_id,
							'tahap_tanggal_kendali' => $r_lpf['tahap_tanggal_kendali'],
							'user_id_update' => $this->session->userdata('uid'),
							'tgl_update' => date('Y-m-d'),
							'ip_update' => $this->session->userdata('ip_address'),
							'divisi_id_update' => $this->session->userdata('divisi_id'),
							'waktu_update' => date('H:i:s'),
							'tahap_diakui_bobot' => $r_lpf['tahap_diakui_bobot'],
							'tahap_diakui_jumlah' => $r_lpf['tahap_diakui_jumlah'],
							'tagihan_cair' => $r_lpf['tagihan_cair'],
							'vol_total_tagihan' => $r_lpf['vol_total_tagihan'],
							'tagihan_rencana_piutang' => $r_lpf['tagihan_rencana_piutang'],
							'kontrak_terkini_id' => $kontrak_kini_id
						);

						$this->db->insert('simpro_tbl_total_pekerjaan',$arr_lpf);
					}
				}

				$q_get_rk = json_decode($client->query("select * from tbl_total_rkp where no_spk = '$no_spk' and tahap_kode_kendali = '".$r_kontrak_kini['tahap_kode_kendali']."' and tahap_tanggal_kendali = '".$r_kontrak_kini['tahap_tanggal_kendali']."'"),true);
				if ($q_get_rk['data']) {
					foreach ($q_get_rk['data'] as $r_rk) {
						$arr_rk = array(
							'proyek_id' => $proyek_id,
							'tahap_tanggal_kendali' => $r_rk['tahap_tanggal_kendali'],
							'user_update' => $this->session->userdata('uid'),
							'tgl_update' => date('Y-m-d'),
							'ip_update' => $this->session->userdata('ip_address'),
							'divisi_update' => $this->session->userdata('divisi_id'),
							'waktu_update' => date('H:i:s'),
							'tahap_volume_bln1' => $r_rk['tahap_volume_bln1'],
							'tahap_jumlah_bln1' => $r_rk['tahap_jumlah_bln1'],
							'tahap_volume_bln2' => $r_rk['tahap_volume_bln2'],
							'tahap_jumlah_bln2' => $r_rk['tahap_jumlah_bln2'],
							'tahap_volume_bln3' => $r_rk['tahap_volume_bln3'],
							'tahap_volume_bln4' => $r_rk['tahap_volume_bln4'],
							'tahap_jumlah_bln3' => $r_rk['tahap_jumlah_bln3'],
							'tahap_jumlah_bln4' => $r_rk['tahap_jumlah_bln4'],
							'kontrak_terkini_id' => $kontrak_kini_id
						);

						$this->db->insert('simpro_tbl_total_rkp',$arr_rk);
					}
				}

				$arr_r_kontrak_kini = array(
					'tahap_kode_kendali' => $r_kontrak_kini['tahap_kode_kendali'],
					'tahap_nama_kendali' => $r_kontrak_kini['tahap_nama_kendali'],
					'tahap_volume_kendali' => $r_kontrak_kini['tahap_volume_kendali'],
					'tahap_harga_satuan_kendali' => $r_kontrak_kini['tahap_harga_satuan_kendali'],
					'tahap_total_kendali' => $r_kontrak_kini['tahap_total_kendali'],
					'tahap_kode_induk_kendali' => $r_kontrak_kini['tahap_kode_induk_kendali'],
					'tahap_tanggal_kendali' => $r_kontrak_kini['tahap_tanggal_kendali'],
					'tgl_update' => date('Y-m-d'),
					'ip_update' => $this->session->userdata('ip_address'),
					'divisi_update' => $this->session->userdata('divisi_id'),
					'waktu_update' => date('H:i:s'),
					'tahap_volume_kendali_new' => $r_kontrak_kini['volume_rencana'],
					'tahap_total_kendali_new' => $r_kontrak_kini['tahap_total_kendali_new'],
					'tahap_harga_satuan_kendali_new' => $r_kontrak_kini['tahap_harga_satuan_kendali_new'],
					'tahap_volume_kendali_kurang' => $r_kontrak_kini['volume_rencana1'],
					'tgl_rencana_aak' => $r_kontrak_kini['tgl_rencana_aak'],
					'volume_eskalasi' => $r_kontrak_kini['rencana_volume_eskalasi'],
					'harga_satuan_eskalasi' => $r_kontrak_kini['rencana_harga_satuan_eskalasi'],
					'is_nilai' => $r_kontrak_kini['is_nilai'],
					'tahap_total_kendali_kurang' => $r_kontrak_kini['tahap_total_kendali_kurang'],
					'total_tambah_kurang' => $r_kontrak_kini['total_tambah_kurang'],
					'tot_rencana1' => $r_kontrak_kini['tot_rencana1'],
					'tot_rencana2' => $r_kontrak_kini['tot_rencana2'],
					'vol_tambah_kurang' => $r_kontrak_kini['vol_tambah_kurang'],
					'proyek_id' => $proyek_id,
					'user_update' => $this->session->userdata('uid'),
					'tahap_satuan_kendali' => ($this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_kontrak_kini['tahap_satuan_kendali']))))->result()) ? $this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_kontrak_kini['tahap_satuan_kendali']))))->row()->satuan_id : 31,
				);

				$this->db->insert('simpro_tbl_rencana_kontrak_terkini',$arr_r_kontrak_kini);
			}
		}

		$this->set_satuan_induk_kk($proyek_id);

		$q_get_rap_tanggal = json_decode($client->query("select distinct(tahap_tanggal_kendali) as tahap_tanggal_kendali from tbl_cost_togo where no_spk = '$no_spk' order by tahap_tanggal_kendali"),true);
		if ($q_get_rap_tanggal['data']) {
			foreach ($q_get_rap_tanggal['data'] as $r_ctg_tgl) {

				$q_get_rap = json_decode($client->query("select * from tbl_cost_togo where no_spk = '$no_spk' and tahap_tanggal_kendali = '".$r_ctg_tgl['tahap_tanggal_kendali']."' order by tahap_kode_kendali,tahap_tanggal_kendali"),true);

				if ($q_get_rap['data']) {
					foreach ($q_get_rap['data'] as $r_ctg) {

						$arr_item_tree = array(
							'id_proyek' => $proyek_id,
							'kode_tree' => $r_ctg['tahap_kode_kendali'],
							'tree_item' => $r_ctg['tahap_nama_kendali'],
							'tree_satuan' => $r_ctg['tahap_satuan_kendali'],
							'volume' => $r_ctg['tahap_volume_kendali'],
							'tree_parent_kode' => $r_ctg['tahap_kode_induk_kendali'],
							'tanggal_kendali' => $r_ctg['tahap_tanggal_kendali']
						);

						$this->db->insert('simpro_costogo_item_tree',$arr_item_tree);

						$ctg_item_tree = $this->db->insert_id();

						$q_cek_analisa = json_decode($client->query("select * from tbl_komposisi_togo where no_spk = '$no_spk' and tahap_kode_kendali = '".$r_ctg['tahap_kode_kendali']."' and tahap_tanggal_kendali = '".$r_ctg['tahap_tanggal_kendali']."'"),true);
						if ($q_cek_analisa['data']) {
							$last_kode_analisa_tgl = $this->get_last_kode_analisa_tgl($proyek_id,$r_ctg['tahap_tanggal_kendali'],'ctg');
							$arr_daftar_analisa = array(
								'kode_analisa' => $last_kode_analisa_tgl,
								'id_kat_analisa' => 10,
								'nama_item' => $r_ctg['tahap_nama_kendali'],
								'id_satuan' => ($this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_ctg['tahap_satuan_kendali']))))->result()) ? $this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_ctg['tahap_satuan_kendali']))))->row()->satuan_id : 31,
								'id_proyek' => $proyek_id,
								'tanggal_kendali' => $r_ctg['tahap_tanggal_kendali']
							);

							$this->db->insert('simpro_costogo_analisa_daftar',$arr_daftar_analisa);

							$id_data_analisa = $this->db->insert_id();

							$q_k_kendali = json_decode($client->query("select distinct(detail_material_kode) as d,* from tbl_komposisi_togo where no_spk = '$no_spk' and tahap_kode_kendali = '".$r_ctg['tahap_kode_kendali']."' and tahap_tanggal_kendali = '".$r_ctg['tahap_tanggal_kendali']."'"),true);
							if ($q_k_kendali['data']) {
								foreach ($q_k_kendali['data'] as $r_komposisi_togo) {
									$arr_where_asat = array(
										'id_data_analisa' => $id_data_analisa, 
										'kode_material' => $r_komposisi_togo['detail_material_kode'], 
										'id_detail_material' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_komposisi_togo['detail_material_kode']))->row()->detail_material_id, 
										'kode_analisa' => $last_kode_analisa_tgl, 
										'id_proyek' => $proyek_id,
										'tanggal_kendali' => $r_komposisi_togo['tahap_tanggal_kendali']
									);	
									$this->db->where($arr_where_asat);
									$q_cek_asat_l = $this->db->get('simpro_costogo_analisa_asat');
									// if ($q_cek_asat_l->num_rows() == 0) {
										$arr_asat = array(
											'id_data_analisa' => $id_data_analisa,
											'kode_material' => $r_komposisi_togo['detail_material_kode'],
											'id_detail_material' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_komposisi_togo['detail_material_kode']))->row()->detail_material_id,
											'koefisien' => $r_komposisi_togo['komposisi_koefisien_kendali'],
											'harga' => $r_komposisi_togo['komposisi_harga_satuan_kendali'],
											'kode_analisa' => $last_kode_analisa_tgl,
											'id_proyek' => $proyek_id,
											'keterangan' => $r_komposisi_togo['keterangan'],
				  							'kode_rap' => $r_komposisi_togo['kode_rap'],
				  							'tanggal_kendali' => $r_komposisi_togo['tahap_tanggal_kendali']
										);

										$this->db->insert('simpro_costogo_analisa_asat',$arr_asat);
									// }					
								}
							}

							$arr_item_apek = array(
								'id_proyek' => $proyek_id,
								'id_data_analisa' => $id_data_analisa,
								'kode_analisa' => $last_kode_analisa_tgl,
								'harga' => 0,
								'costogo_item_tree' => $ctg_item_tree,
								'kode_tree' => $r_ctg['tahap_kode_kendali'],
								'tanggal_kendali' => $r_ctg['tahap_tanggal_kendali']
							);

							$this->db->insert('simpro_costogo_analisa_item_apek',$arr_item_apek);
						}
					}
				}

				$this->update_kode_rap_ctg($proyek_id,$r_ctg_tgl['tahap_tanggal_kendali']);
				$this->update_tree_parent_id_rap_tgl($proyek_id,$r_ctg_tgl['tahap_tanggal_kendali'],'ctg');
			}
		}

		$q_get_rap_tanggal = json_decode($client->query("select distinct(tahap_tanggal_kendali) as tahap_tanggal_kendali from tbl_current_budget where no_spk = '$no_spk' order by tahap_tanggal_kendali"),true);
		if ($q_get_rap_tanggal['data']) {
			foreach ($q_get_rap_tanggal['data'] as $r_cbd_tgl) {

				$q_get_rap = json_decode($client->query("select * from tbl_current_budget where no_spk = '$no_spk' and tahap_tanggal_kendali = '".$r_cbd_tgl['tahap_tanggal_kendali']."' order by tahap_kode_kendali,tahap_tanggal_kendali"),true);

				if ($q_get_rap['data']) {
					foreach ($q_get_rap['data'] as $r_cbd) {

						$arr_item_tree = array(
							'id_proyek' => $proyek_id,
							'kode_tree' => $r_cbd['tahap_kode_kendali'],
							'tree_item' => $r_cbd['tahap_nama_kendali'],
							'tree_satuan' => $r_cbd['tahap_satuan_kendali'],
							'volume' => $r_cbd['tahap_volume_kendali'],
							'tree_parent_kode' => $r_cbd['tahap_kode_induk_kendali'],
							'tanggal_kendali' => $r_cbd['tahap_tanggal_kendali']
						);

						$this->db->insert('simpro_current_budget_item_tree',$arr_item_tree);

						$cbd_item_tree = $this->db->insert_id();

						$q_cek_analisa = json_decode($client->query("select * from tbl_komposisi_budget where no_spk = '$no_spk' and tahap_kode_kendali = '".$r_cbd['tahap_kode_kendali']."' and tahap_tanggal_kendali = '".$r_cbd['tahap_tanggal_kendali']."'"),true);
						if ($q_cek_analisa['data']) {
							$last_kode_analisa_tgl = $this->get_last_kode_analisa_tgl($proyek_id,$r_cbd['tahap_tanggal_kendali'],'cbd');
							$arr_daftar_analisa = array(
								'kode_analisa' => $last_kode_analisa_tgl,
								'id_kat_analisa' => 10,
								'nama_item' => $r_cbd['tahap_nama_kendali'],
								'id_satuan' => ($this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_cbd['tahap_satuan_kendali']))))->result()) ? $this->get_think('satuan_id','simpro_tbl_satuan',array('lower(satuan_nama)' => strtolower(str_replace('.', ' ', $r_cbd['tahap_satuan_kendali']))))->row()->satuan_id : 31,
								'id_proyek' => $proyek_id,
								'tanggal_kendali' => $r_cbd['tahap_tanggal_kendali']
							);

							$this->db->insert('simpro_current_budget_analisa_daftar',$arr_daftar_analisa);

							$id_data_analisa = $this->db->insert_id();

							$q_k_kendali = json_decode($client->query("select distinct(detail_material_kode) as d,* from tbl_komposisi_budget where no_spk = '$no_spk' and tahap_kode_kendali = '".$r_cbd['tahap_kode_kendali']."' and tahap_tanggal_kendali = '".$r_cbd['tahap_tanggal_kendali']."'"),true);
							if ($q_k_kendali['data']) {
								foreach ($q_k_kendali['data'] as $r_komposisi_budget) {
									$arr_where_asat = array(
										'id_data_analisa' => $id_data_analisa, 
										'kode_material' => $r_komposisi_budget['detail_material_kode'], 
										'id_detail_material' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_komposisi_budget['detail_material_kode']))->row()->detail_material_id, 
										'kode_analisa' => $last_kode_analisa_tgl, 
										'id_proyek' => $proyek_id,
										'tanggal_kendali' => $r_komposisi_budget['tahap_tanggal_kendali']
									);	
									$this->db->where($arr_where_asat);
									$q_cek_asat_l = $this->db->get('simpro_current_budget_analisa_asat');
									// if ($q_cek_asat_l->num_rows() == 0) {
										$arr_asat = array(
											'id_data_analisa' => $id_data_analisa,
											'kode_material' => $r_komposisi_budget['detail_material_kode'],
											'id_detail_material' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_komposisi_budget['detail_material_kode']))->row()->detail_material_id,
											'koefisien' => $r_komposisi_budget['komposisi_koefisien_kendali'],
											'harga' => $r_komposisi_budget['komposisi_harga_satuan_kendali'],
											'kode_analisa' => $last_kode_analisa_tgl,
											'id_proyek' => $proyek_id,
											'keterangan' => $r_komposisi_budget['keterangan'],
				  							'kode_rap' => $r_komposisi_budget['kode_rap'],
				  							'tanggal_kendali' => $r_komposisi_budget['tahap_tanggal_kendali']
										);

										$this->db->insert('simpro_current_budget_analisa_asat',$arr_asat);
									// }					
								}
							}

							$arr_item_apek = array(
								'id_proyek' => $proyek_id,
								'id_data_analisa' => $id_data_analisa,
								'kode_analisa' => $last_kode_analisa_tgl,
								'harga' => 0,
								'current_budget_item_tree' => $cbd_item_tree,
								'kode_tree' => $r_cbd['tahap_kode_kendali'],
								'tanggal_kendali' => $r_cbd['tahap_tanggal_kendali']
							);

							$this->db->insert('simpro_current_budget_analisa_item_apek',$arr_item_apek);
						}
					}
				}

				$this->update_kode_rap_cbd($proyek_id,$r_cbd_tgl['tahap_tanggal_kendali']);
				$this->update_tree_parent_id_rap_tgl($proyek_id,$r_cbd_tgl['tahap_tanggal_kendali'],'cbd');
			}
		}

		$q_get_rpbk_s = json_decode($client->query("select * from tbl_rpbk where no_spk = '$no_spk'"),true);
		if ($q_get_rpbk_s['data']) {
			foreach ($q_get_rpbk_s['data'] as $r_rpbk) {
				$arr_rpbk = array(
					'proyek_id' => $proyek_id,
					'detail_material_id' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_rpbk['detail_material_kode']))->row()->detail_material_id,
					'tahap_kode_kendali' => $r_rpbk['tahap_kode_kendali'],
					'komposisi_volume_kendali' => $r_rpbk['komposisi_volume_kendali'],
					'komposisi_harga_satuan_kendali' => $r_rpbk['komposisi_harga_satuan_kendali'],
					'komposisi_total_kendali' => $r_rpbk['komposisi_total_kendali'],
					'komposisi_koefisien_kendali' => $r_rpbk['komposisi_koefisien_kendali'],
					'tahap_tanggal_kendali' => $r_rpbk['tahap_tanggal_kendali'],
					'user_update' => $this->session->userdata('uid'),
					'tgl_update' => date('Y-m-d'),
					'ip_update' => $this->session->userdata('ip_address'),
					'divisi_update' => $this->session->userdata('divisi_id'),
					'waktu_update' => date('H:i:s'),
					'komposisi_volume_total_kendali' => $r_rpbk['komposisi_volume_total_kendali'],
					'kode_komposisi_kendali' => $r_rpbk['kode_komposisi_kendali'],
					'volume_rencana_pbk' => $r_rpbk['volume_rencana_pbk'],
					'total_rencana_pbk' => $r_rpbk['total_rencana_pbk'],
					'rpbk_rrk1' => $r_rpbk['rpbk_rrk1'],
					'rpbk_rrk2' => $r_rpbk['rpbk_rrk2'],
					'detail_material_kode' => $r_rpbk['detail_material_kode'],
					'kode_rap' => $this->get_think('kode_rap','simpro_rap_analisa_asat',array('kode_material' => $r_rpbk['detail_material_kode'],'id_proyek'=>$proyek_id))->row()->kode_rap
				);

				$this->db->insert('simpro_tbl_rpbk',$arr_rpbk);
			}
		}

		$q_get_mos = json_decode($client->query("select * from tbl_mos where no_spk = '$no_spk'"),true);
		if ($q_get_mos['data']) {
			foreach ($q_get_mos['data'] as $r_mos) {
				$arr_mos = array(
					'proyek_id' => $proyek_id,
					'mos_tgl' => $r_mos['mos_tgl'],
					'detail_material_id' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_mos['detail_material_kode']))->row()->detail_material_id,
					'mos_total_harsat' => $r_mos['mos_total_harsat'],
					'mos_total_jumlah' => $r_mos['mos_total_jumlah'],
					'mos_diakui_harsat' => $r_mos['mos_diakui_harsat'],
					'mos_diakui_jumlah' => $r_mos['mos_diakui_jumlah'],
					'mos_belum_volume' => $r_mos['mos_belum_volume'],
					'mos_belum_jumlah' => $r_mos['mos_belum_jumlah'],
					'mos_keterangan' => $r_mos['mos_keterangan'],
					'mos_diakui_volume' => $r_mos['mos_diakui_volume'],
					'mos_total_volume' => $r_mos['mos_total_volume'],
					'kode_rap' => $this->get_think('kode_rap','simpro_rap_analisa_asat',array('kode_material' => $r_mos['detail_material_kode'],'id_proyek'=>$proyek_id))->row()->kode_rap,
					'detail_material_kode' => $r_mos['detail_material_kode']
				);

				$this->db->insert('simpro_tbl_mos',$arr_mos);
			}
		}

		$get_kkp_s = json_decode($client->query("select * from tbl_kkp where no_spk = '$no_spk'"),true);
		if ($get_kkp_s['data']) {
			foreach ($get_kkp_s['data'] as $r_kkp) {
				$arr_kkp = array(
					'proyek_id' => $proyek_id,
					'kkp_uraian' => $r_kkp['kkp_uraian'],
					'kkp_tempat' => $r_kkp['kkp_tempat'],
					'kkp_rencana' => $r_kkp['kkp_rencana'],
					'kkp_tgl' => $r_kkp['kkp_tgl'],
					'jabatan' => $r_kkp['kkp_pelaku']
				);

				$this->db->insert('simpro_tbl_kkp',$arr_kkp);
			}
		}

		$get_cashflow_s = json_decode($client->query("select * from tbl_cashin where no_spk = '$no_spk'"),true);
		if ($get_cashflow_s['data']) {
			foreach ($get_cashflow_s['data'] as $r_cashflow) {
				$arr_cashflow = array(
					'proyek_id' => $proyek_id,
					'uraian' => $r_cashflow['uraian'],
					'tahap_tanggal_kendali' => $r_cashflow['tahap_tanggal_kendali'],
					'realisasi' => $r_cashflow['realisasi'],
					'rproyeksi1' => $r_cashflow['rproyeksi1'],
					'rproyeksi2' => $r_cashflow['rproyeksi2'],
					'rproyeksi3' => $r_cashflow['rproyeksi3'],
					'rproyeksi4' => $r_cashflow['rproyeksi4'],
					'user_id' => $this->session->userdata('uid'),
					'tgl_update' => date('Y-m-d'),
					'ip_update' => $this->session->userdata('ip_address'),
					'divisi_id' => $this->session->userdata('divisi_id'),
					'waktu_update' => date('H:i:s'),
					'rproyeksi5' => $r_cashflow['rproyeksi5'],
					'rproyeksi6' => $r_cashflow['rproyeksi6'],
					'curentbuget' => $r_cashflow['curentbuget'],
					'sbp' => $r_cashflow['sbp'],
					'spp' => $r_cashflow['spp'],
					'ket_id' => $r_cashflow['id']
				);

				$this->db->query('simpro_tbl_cashin',$arr_cashflow);
			}
		}

		$q_get_po2_s = json_decode($client->query("select * from tbl_po2 where no_spk = '$no_spk'"),true);
		if ($q_get_po2_s['data']) {
			foreach ($q_get_po2_s['data'] as $r_po2) {
				$arr_po2 = array(
					'proyek_id' => $proyek_id,
					'tahap_tanggal_kendali' => $r_po2['tanggal'],
					'detail_material_id' => $this->get_think('detail_material_id','simpro_tbl_detail_material',array('detail_material_kode' => $r_po2['detail_material_kode']))->row()->detail_material_id,
					'volume_ob' => $r_po2['volume_ob'],
					'harga_sat_ob' => $r_po2['harga_sat_ob'],
					'jumlah_ob' => $r_po2['jumlah_ob'],
					'volume_cash_td' => $r_po2['volume_cash_td'],
					'jumlah_cash_td' => $r_po2['jumlah_cash_td'],
					'volume_hutang' => $r_po2['volume_hutang'],
					'jumlah_hutang' => $r_po2['jumlah_hutang'],
					'volume_hp' => $r_po2['volume_hp'],
					'jumlah_hp' => $r_po2['jumlah_hp'],
					'volume_cost_td' => $r_po2['volume_cost_td'],
					'jumlah_cost_td' => $r_po2['jumlah_cost_td'],
					'volume_cost_tg' => $r_po2['volume_cost_tg'],
					'hargasat_cost_tg' => $r_po2['hargasat_cost_tg'],
					'jumlah_cost_tg' => $r_po2['jumlah_cost_tg'],
					'volume_cf' => $r_po2['volume_cf'],
					'jumlah_cf' => $r_po2['jumlah_cf'],
					'trend' => $r_po2['trend'],
					'volume_tot_hutang' => $r_po2['volume_tot_hutang'],
					'total_hutang' => $r_po2['total_hutang'],
					'vol_cash_hutang' => $r_po2['vol_cash_hutang'],
					'jum_cash_hutang' => $r_po2['jum_cash_hutang'],
					'volume_cb' => $r_po2['volume_cb'],
					'hargasat_cb' => $r_po2['hargasat_cb'],
					'jumlah_cb' => $r_po2['jumlah_cb'],
					'vol_pres' => $r_po2['vol_pres'],
					'hargasat_pres' => $r_po2['hargasat_pres'],
					'jumlah_pres' => $r_po2['jumlah_pres'],
					'uraian' => $r_po2['uraian'],
					'volume_rencana' => $r_po2['volume_rencana'],
					'total_volume_rencana' => $r_po2['total_volume_rencana'],
					'pilihan' => $r_po2['pilihan'],
					'kode_rap' => $r_po2['kode_rap'],
					'jlh_tambah' => $r_po2['jlh_tambah'],
					'jlh_kurang' => $r_po2['jlh_kurang'],
					'detail_material_kode' => $r_po2['detail_material_kode'],
					'detail_material_nama' => $r_po2['detail_material_nama'],
					'detail_material_satuan' => $r_po2['detail_material_satuan']
				);

				$this->db->insert('simpro_tbl_po2',$arr_po2);
			}
		}

		$q_get_daftar_peralatan_s = json_decode($client->query("select * from tbl_daftar_peralatan where no_spk = '$no_spk'"),true);
		if ($q_get_daftar_peralatan_s['data']) {
			foreach ($q_get_daftar_peralatan_s['data'] as $r_daftar_peralatan) {
				$arr_daftar_peralatan = array(
					'daftar_peralatan_id' => $r_daftar_peralatan['daftar_peralatan_id'],
					'proyek_id' => $proyek_id,
					'tgl' => $r_daftar_peralatan['tgl'],
					'user_id' => $this->session->userdata('uid'),
					'keterangan' => $r_daftar_peralatan['keterangan'],
					'master_peralatan_id' => $r_daftar_peralatan['alat_id'],
					'status_kepemilikan' => $r_daftar_peralatan['status_kepemilikan'] + 1,
					'kondisi' => $r_daftar_peralatan['kondisi'] + 1,
					'status_operasi' => $r_daftar_peralatan['status_operasi'] + 1
				);

				$this->db->insert('simpro_tbl_daftar_peralatan',$arr_daftar_peralatan);
			}
		}

		if($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();							
			} else
			{
				$this->db->trans_commit();
			}
	}

	function get_think($select,$from,$where)
	{
		$this->db->select($select);
		$this->db->where($where);
		$q = $this->db->get($from);
		return $q;
	}

	function update_tree_parent_id_rab($id_proyek)
	{
		$sql = "
			update simpro_tbl_input_kontrak
			set tree_parent_id = x.id_parent
			from(
			SELECT 
			case when (select input_kontrak_id from simpro_tbl_input_kontrak where tahap_kode_kendali = a.tahap_kode_induk_kendali and proyek_id = a.proyek_id) isnull
			then 0
			else (select input_kontrak_id from simpro_tbl_input_kontrak where tahap_kode_kendali = a.tahap_kode_induk_kendali and proyek_id = a.proyek_id)
			end as id_parent,
			a.input_kontrak_id, a.tahap_kode_kendali 
			FROM simpro_tbl_input_kontrak a
			WHERE a.proyek_id = $id_proyek) x
			where proyek_id = $id_proyek and simpro_tbl_input_kontrak.input_kontrak_id = x.input_kontrak_id	
			";
		$this->db->query($sql);
	}

	function update_tree_parent_id_rap($id_proyek)
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
			WHERE a.id_proyek = $id_proyek) x
			where id_proyek = $id_proyek and simpro_rap_item_tree.rap_item_tree = x.rap_item_tree	
			";
		$this->db->query($sql);
	}

	function update_tree_parent_id_rap_tgl($id_proyek,$tgl_kendali,$page)
	{
		switch ($page) {
			case 'ctg':
				$info = 'costogo';
			break;
			case 'cbd':
				$info = 'current_budget';
			break;
		}
		$sql = "
			update simpro_".$info."_item_tree
			set tree_parent_id = x.id_parent
			from(
			SELECT 
			case when (select ".$info."_item_tree from simpro_".$info."_item_tree where kode_tree = a.tree_parent_kode and id_proyek = a.id_proyek) isnull
			then 0
			else (select ".$info."_item_tree from simpro_".$info."_item_tree where kode_tree = a.tree_parent_kode and id_proyek = a.id_proyek)
			end as id_parent,
			a.".$info."_item_tree, a.kode_tree 
			FROM simpro_".$info."_item_tree a
			WHERE a.id_proyek = $id_proyek and tanggal_kendali = '$tgl_kendali') x
			where id_proyek = $id_proyek and simpro_".$info."_item_tree.".$info."_item_tree = x.".$info."_item_tree
			";
		$this->db->query($sql);
	}

	function set_satuan_induk_rab($proyek_id)
	{
		$sql_set_satuan_induk = "
		update simpro_tbl_input_kontrak set
		tahap_satuan_kendali = 'Ls',
		tahap_volume_kendali = 1,
		tahap_harga_satuan_kendali = 0
		from (
		select * from (select
		a.*,
		(SELECT 
		count('a') as count
		FROM simpro_tbl_input_kontrak
		WHERE proyek_id = a.proyek_id
		and tahap_kode_induk_kendali = a.tahap_kode_kendali) as count
		from
		simpro_tbl_input_kontrak a
		where a.proyek_id = $proyek_id) n
		where n.count != 0
		) x
		where simpro_tbl_input_kontrak.tahap_kode_kendali = x.tahap_kode_kendali
		";

		$this->db->query($sql_set_satuan_induk);
	}

	function set_satuan_induk_kk($proyek_id)
	{
		$sql1 = "update simpro_tbl_kontrak_terkini set
		tahap_satuan_kendali = 31,
		tahap_volume_kendali = 1,
		tahap_harga_satuan_kendali = 0
		from (
		select * from (select
		a.*,
		(SELECT 
		count('a') as count
		FROM simpro_tbl_kontrak_terkini
		WHERE proyek_id = a.proyek_id
		and tahap_kode_induk_kendali = a.tahap_kode_kendali) as count
		from
		simpro_tbl_kontrak_terkini a
		where a.proyek_id = $proyek_id) n
		where n.count != 0
		) x
		where simpro_tbl_kontrak_terkini.tahap_kode_kendali = x.tahap_kode_kendali";

		$this->db->query($sql1);

		$sql2 = "update simpro_tbl_rencana_kontrak_terkini set
		tahap_satuan_kendali = 31,
		tahap_volume_kendali = 1,
		tahap_harga_satuan_kendali = 0
		from (
		select * from (select
		a.*,
		(SELECT 
		count('a') as count
		FROM simpro_tbl_rencana_kontrak_terkini
		WHERE proyek_id = a.proyek_id
		and tahap_kode_induk_kendali = a.tahap_kode_kendali) as count
		from
		simpro_tbl_rencana_kontrak_terkini a
		where a.proyek_id = $proyek_id) n
		where n.count != 0
		) x
		where simpro_tbl_rencana_kontrak_terkini.tahap_kode_kendali = x.tahap_kode_kendali";

		$this->db->query($sql2);
	}

	function get_last_kode_analisa($id_proyek)
	{
		if ($id_proyek) {
			$sql = "select right(kode_analisa,(length(kode_analisa) - 2))::numeric + 1 last_kode_analisa from simpro_rap_analisa_daftar where id_proyek = $id_proyek order by last_kode_analisa desc limit 1";
			$q = $this->db->query($sql);
			if ($q->result()) {
				$val = $q->row()->last_kode_analisa;
			} else {
				$val = 1;
			}

			$v = sprintf("AN%03d",$val);
			return $v;
		}		
	}

	function get_last_kode_analisa_tgl($id_proyek,$tgl_kendali,$page)
	{
		switch ($page) {
			case 'ctg':
				$tbl_info = 'simpro_costogo_analisa_daftar';
			break;
			case 'cbd':
				$tbl_info = 'simpro_current_budget_analisa_daftar';
			break;
		}
		if ($id_proyek) {
			$sql = "select right(kode_analisa,(length(kode_analisa) - 2))::numeric + 1 last_kode_analisa from $tbl_info where id_proyek = $id_proyek order by last_kode_analisa desc limit 1";
			$q = $this->db->query($sql);
			if ($q->result()) {
				$val = $q->row()->last_kode_analisa;
			} else {
				$val = 1;
			}

			$v = sprintf("AN%03d",$val);
			return $v;
		}		
	}

	function update_kode_rap($id)
	{
		$error = FALSE;
		$sql_kode = "
			SELECT kode_kat FROM simpro_rat_analisa_kategori
			WHERE (subbidang_kode <> '' OR subbidang_kode IS NOT NULL)
			ORDER BY kode_kat ASC		
		";
		$krap = $this->db->query($sql_kode)->result_array();
		foreach($krap as $k=>$v)
		{
			$kd_kat = $v['kode_kat'];
			$sql_kode_null = "				select *,
				case when keterangan isnull
				then (select keterangan from simpro_rap_analisa_asat where kode_material = analisa.kode_material and id_proyek = analisa.id_proyek and keterangan is not null group by keterangan)
				else keterangan
				end as keterangan_ad,
				case when length(kd_rap_int) = 1
				then kode_kat || '000' || kd_rap_int
				when length(kd_rap_int) = 2
				then kode_kat || '00' || kd_rap_int
				when length(kd_rap_int) = 3
				then kode_kat || '0' || kd_rap_int
				when length(kd_rap_int) = 4
				then kode_kat || kd_rap_int
				end as rap_kd
				from (
				select *, 
				case when kode_rap is not null and kode_rap != ''
				then kode_rap_numb::int 
				when (kode_rap isnull) and (select count(kode_rap) from simpro_rap_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek) = 0
				then (ROW_NUMBER() OVER (ORDER BY 'a')) - coalesce((select count(kode_rap) from simpro_rap_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek),0) + coalesce((select (right(kode_rap,4)::int) as int_rap from simpro_rap_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek order by int_rap desc limit 1),0)
				when (kode_rap isnull) and (select count(kode_rap) from simpro_rap_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek) > 0
				then (ROW_NUMBER() OVER (ORDER BY 'a'))
				end::text as kd_rap_int
				from (SELECT 
					simpro_rap_analisa_asat.kode_material as kode_material,
					simpro_rap_analisa_asat.id_proyek,
					simpro_rap_analisa_asat.keterangan,
					case when (select count(a.kode_material) from simpro_rap_analisa_asat a where a.kode_material = simpro_rap_analisa_asat.kode_material and a.id_proyek = id_proyek) > 1
					then right((select b.kode_rap from simpro_rap_analisa_asat b where b.kode_material = simpro_rap_analisa_asat.kode_material and b.id_proyek = simpro_rap_analisa_asat.id_proyek and b.kode_rap is not null group by b.kode_rap),4)
					else right(kode_rap,4)
					end kode_rap_numb,
					case when (select count(a.kode_material) from simpro_rap_analisa_asat a where a.kode_material = simpro_rap_analisa_asat.kode_material and a.id_proyek = id_proyek) > 1
					then (select b.kode_rap from simpro_rap_analisa_asat b where b.kode_material = simpro_rap_analisa_asat.kode_material and b.id_proyek = simpro_rap_analisa_asat.id_proyek and b.kode_rap is not null group by b.kode_rap)
					else kode_rap
					end kode_rap,
					simpro_rat_analisa_kategori.kode_kat
				FROM simpro_rap_analisa_asat
				JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_rap_analisa_asat.kode_material,3)
				WHERE simpro_rap_analisa_asat.id_proyek = $id
					AND simpro_rat_analisa_kategori.kode_kat = '$kd_kat'
				) analisa_asat
				GROUP BY kode_material, 
					keterangan,
					kode_kat,
					kode_rap,
					kode_rap_numb,
					id_proyek
				order by kode_rap) analisa";

			$q_cek_material = $this->db->query($sql_kode_null);

			if ($q_cek_material->result()) {
				foreach ($q_cek_material->result() as $r) {
					$updata = array('kode_rap'=>$r->rap_kd,'keterangan'=>$r->keterangan_ad);
					$this->db->where(array('kode_material' => $r->kode_material,'id_proyek' => $r->id_proyek));
					if(!$this->db->update('simpro_rap_analisa_asat', $updata)) $error = TRUE;
				}
			}		
		}
		if(!$error) return true;
			else return false;
	}

	function update_kode_rap_ctg($id,$tgl_rab)
	{
		$error = FALSE;
		$sql_kode = "
			SELECT kode_kat FROM simpro_rat_analisa_kategori
			WHERE (subbidang_kode <> '' OR subbidang_kode IS NOT NULL)
			ORDER BY kode_kat ASC		
		";
		$kcostogo = $this->db->query($sql_kode)->result_array();
		foreach($kcostogo as $k=>$v)
		{	

			$kd_kat = $v['kode_kat'];
			$sql_kode_null = "				select *,
				case when keterangan isnull
				then (select keterangan from simpro_costogo_analisa_asat where kode_material = analisa.kode_material and id_proyek = analisa.id_proyek and keterangan is not null group by keterangan)
				else keterangan
				end as keterangan_ad,
				case when length(kd_rap_int) = 1
				then kode_kat || '000' || kd_rap_int
				when length(kd_rap_int) = 2
				then kode_kat || '00' || kd_rap_int
				when length(kd_rap_int) = 3
				then kode_kat || '0' || kd_rap_int
				when length(kd_rap_int) = 4
				then kode_kat || kd_rap_int
				end as rap_kd
				from (
				select *, 
				case when kode_rap is not null and kode_rap != ''
				then kode_rap_numb::int 
				when (kode_rap isnull) and (select count(kode_rap) from simpro_costogo_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali) = 0
				then (ROW_NUMBER() OVER (ORDER BY 'a')) - coalesce((select count(kode_rap) from simpro_costogo_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali),0) + coalesce((select (right(kode_rap,4)::int) as int_rap from simpro_costogo_analisa_asat where kode_rap is not null 
					and tanggal_kendali = analisa_asat.tanggal_kendali and id_proyek = analisa_asat.id_proyek order by int_rap desc limit 1),0)
				when (kode_rap isnull) and (select count(kode_rap) from simpro_costogo_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali) > 0
				then (ROW_NUMBER() OVER (ORDER BY 'a'))
				end::text as kd_rap_int
				from (SELECT 
					simpro_costogo_analisa_asat.tanggal_kendali,
					simpro_costogo_analisa_asat.kode_material as kode_material,
					simpro_costogo_analisa_asat.id_proyek,
					simpro_costogo_analisa_asat.keterangan,
					case when (select count(a.kode_material) from simpro_costogo_analisa_asat a where a.kode_material = simpro_costogo_analisa_asat.kode_material and a.id_proyek = id_proyek 
					and a.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali) > 1
					then right((select b.kode_rap from simpro_costogo_analisa_asat b where b.kode_material = simpro_costogo_analisa_asat.kode_material and b.id_proyek = simpro_costogo_analisa_asat.id_proyek and b.kode_rap is not null 
					and b.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali group by b.kode_rap),4)
					else right(kode_rap,4)
					end kode_rap_numb,
					case when (select count(a.kode_material) from simpro_costogo_analisa_asat a where a.kode_material = simpro_costogo_analisa_asat.kode_material and a.id_proyek = id_proyek 
					and a.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali) > 1
					then (select b.kode_rap from simpro_costogo_analisa_asat b where b.kode_material = simpro_costogo_analisa_asat.kode_material and b.id_proyek = simpro_costogo_analisa_asat.id_proyek and b.kode_rap is not null 
					and b.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali group by b.kode_rap)
					else kode_rap
					end kode_rap,
					simpro_rat_analisa_kategori.kode_kat
				FROM simpro_costogo_analisa_asat
				JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_costogo_analisa_asat.kode_material,3)
				WHERE simpro_costogo_analisa_asat.id_proyek = $id
					AND simpro_rat_analisa_kategori.kode_kat = '$kd_kat'
					and simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
				) analisa_asat
				GROUP BY kode_material, 
					keterangan,
					tanggal_kendali,
					kode_kat,
					kode_rap,
					kode_rap_numb,
					id_proyek
				order by kode_rap) analisa";

			$q_cek_material = $this->db->query($sql_kode_null);

			if ($q_cek_material->result()) {
				foreach ($q_cek_material->result() as $r) {
					$updata = array('kode_rap'=>$r->rap_kd,'keterangan'=>$r->keterangan_ad);
					$this->db->where(array('kode_material' => $r->kode_material,'id_proyek' => $r->id_proyek, 'tanggal_kendali'=>$tgl_rab));
					if(!$this->db->update('simpro_costogo_analisa_asat', $updata)) $error = TRUE;
				}
			}		
		}
		if(!$error) return true;
			else return false;
	}

	function update_kode_rap_cbd($id,$tgl_rab)
	{
		$error = FALSE;
		$sql_kode = "
			SELECT kode_kat FROM simpro_rat_analisa_kategori
			WHERE (subbidang_kode <> '' OR subbidang_kode IS NOT NULL)
			ORDER BY kode_kat ASC		
		";
		$kcurrent_budget = $this->db->query($sql_kode)->result_array();
		foreach($kcurrent_budget as $k=>$v)
		{
			$kd_kat = $v['kode_kat'];
			$sql_kode_null = "				select *,
				case when keterangan isnull
				then (select keterangan from simpro_current_budget_analisa_asat where kode_material = analisa.kode_material and id_proyek = analisa.id_proyek and keterangan is not null group by keterangan)
				else keterangan
				end as keterangan_ad,
				case when length(kd_rap_int) = 1
				then kode_kat || '000' || kd_rap_int
				when length(kd_rap_int) = 2
				then kode_kat || '00' || kd_rap_int
				when length(kd_rap_int) = 3
				then kode_kat || '0' || kd_rap_int
				when length(kd_rap_int) = 4
				then kode_kat || kd_rap_int
				end as rap_kd
				from (
				select *, 
				case when kode_rap is not null and kode_rap != ''
				then kode_rap_numb::int 
				when (kode_rap isnull) and (select count(kode_rap) from simpro_current_budget_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali) = 0
				then (ROW_NUMBER() OVER (ORDER BY 'a')) - coalesce((select count(kode_rap) from simpro_current_budget_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali),0) + coalesce((select (right(kode_rap,4)::int) as int_rap from simpro_current_budget_analisa_asat where kode_rap is not null 
					and tanggal_kendali = analisa_asat.tanggal_kendali and id_proyek = analisa_asat.id_proyek order by int_rap desc limit 1),0)
				when (kode_rap isnull) and (select count(kode_rap) from simpro_current_budget_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali) > 0
				then (ROW_NUMBER() OVER (ORDER BY 'a'))
				end::text as kd_rap_int
				from (SELECT 
					simpro_current_budget_analisa_asat.tanggal_kendali,
					simpro_current_budget_analisa_asat.kode_material as kode_material,
					simpro_current_budget_analisa_asat.id_proyek,
					simpro_current_budget_analisa_asat.keterangan,
					case when (select count(a.kode_material) from simpro_current_budget_analisa_asat a where a.kode_material = simpro_current_budget_analisa_asat.kode_material and a.id_proyek = id_proyek 
					and a.tanggal_kendali = simpro_current_budget_analisa_asat.tanggal_kendali) > 1
					then right((select b.kode_rap from simpro_current_budget_analisa_asat b where b.kode_material = simpro_current_budget_analisa_asat.kode_material and b.id_proyek = simpro_current_budget_analisa_asat.id_proyek and b.kode_rap is not null 
					and b.tanggal_kendali = simpro_current_budget_analisa_asat.tanggal_kendali group by b.kode_rap),4)
					else right(kode_rap,4)
					end kode_rap_numb,
					case when (select count(a.kode_material) from simpro_current_budget_analisa_asat a where a.kode_material = simpro_current_budget_analisa_asat.kode_material and a.id_proyek = id_proyek 
					and a.tanggal_kendali = simpro_current_budget_analisa_asat.tanggal_kendali) > 1
					then (select b.kode_rap from simpro_current_budget_analisa_asat b where b.kode_material = simpro_current_budget_analisa_asat.kode_material and b.id_proyek = simpro_current_budget_analisa_asat.id_proyek and b.kode_rap is not null 
					and b.tanggal_kendali = simpro_current_budget_analisa_asat.tanggal_kendali group by b.kode_rap)
					else kode_rap
					end kode_rap,
					simpro_rat_analisa_kategori.kode_kat
				FROM simpro_current_budget_analisa_asat
				JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_current_budget_analisa_asat.kode_material,3)
				WHERE simpro_current_budget_analisa_asat.id_proyek = $id
					AND simpro_rat_analisa_kategori.kode_kat = '$kd_kat'
					and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_rab'
				) analisa_asat
				GROUP BY kode_material, 
					keterangan,
					tanggal_kendali,
					kode_kat,
					kode_rap,
					kode_rap_numb,
					id_proyek
				order by kode_rap) analisa";

			$q_cek_material = $this->db->query($sql_kode_null);

			if ($q_cek_material->result()) {
				foreach ($q_cek_material->result() as $r) {
					$updata = array('kode_rap'=>$r->rap_kd,'keterangan'=>$r->keterangan_ad);
					$this->db->where(array('kode_material' => $r->kode_material,'id_proyek' => $r->id_proyek, 'tanggal_kendali'=>$tgl_rab));
					if(!$this->db->update('simpro_current_budget_analisa_asat', $updata)) $error = TRUE;
				}
			}
		}
		if(!$error) return true;
			else return false;
	}
}