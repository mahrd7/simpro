<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Laporan extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_laporan');		
		if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}
	
	public function printed_lbp()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$data['proyek_id'] = $this->session->userdata('proyek_id');
			$data['proyek'] = $this->mdl_laporan->get_data_proyek($proyek_id);
			$this->load->view('printed_lbp', $data);
		}
	}

	public function lbp01()
	{		
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$data['proyek_id'] = $this->session->userdata('proyek_id');
			$data['proyek'] = $this->mdl_laporan->get_data_proyek($proyek_id);
			$this->load->view('laporan_biaya_proyek', $data);
		}
	}

	public function antisipasi()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			redirect(base_url().'transaksi/antisipasi/report');
		}
		// $this->load->view('antisipasi');
	}

	public function perincian_hutang()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$this->load->view('perincian_hutang');
		}
	}

	public function cashflow_project()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$date = date('Y-m-d');
			redirect(base_url().'pengendalian/cashflow/close/'.$date.'/report');
			// $this->load->view('cashflow');
		}
	}

	public function pbk01()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			if ($this->input->post('bln') && $this->input->post('thn')) {
				$b = $this->input->post('bln');
				$t = $this->input->post('thn');
				$tgl_rab = $this->tgl_to_pbk($b,$t);
			} else {
				$b = date('n');
				$t = date('Y');
				$tgl_rab = $this->tgl_to_pbk($b,$t);
			}

			$q_tg = $this->db->query("select tahap_tanggal_kendali from simpro_tbl_po2 where tahap_tanggal_kendali <= '$t-$b-01' order by tahap_tanggal_kendali desc limit 1");

			if ($q_tg->result()) {
				$tg = $q_tg->row()->tahap_tanggal_kendali;
			} else {
				$tg = '0001-01-01';
			}

			$divisi_id = $this->session->userdata('divisi_id');
			$data['proyek'] = $this->mdl_laporan->get_data_proyek_pbk($proyek_id);
			$data['divisi'] = $this->mdl_laporan->get_data_divisi_pbk($divisi_id);
			$data['tgl_rab'] = $tgl_rab;
			$data['sel_bln'] = $b;
			$data['sel_thn'] = $t;

			$data['tg'] = $tg;

			$b_min = $b - 1;
			$b_plus = $b + 1;

			if ($b_min == 0) {
				$b_lalu = 12;
				$t_lalu = $t-1;
				$tgl_rab_lalu = $this->tgl_to_pbk(12,($t-1));
			} else {
				$b_lalu = $b_min;
				$t_lalu = $t;
				$tgl_rab_lalu = $this->tgl_to_pbk($b_min,($t));
			}

			if ($b_plus == 13) {
				$b_lanjut = 1;
				$t_lanjut = $t+1;
				$tgl_rab_lanjut = $this->tgl_to_pbk(1,($t+1));
			} else {
				$b_lanjut = $b_plus;
				$t_lanjut = $t;
				$tgl_rab_lanjut = $this->tgl_to_pbk($b_plus,($t));
			}

			$data['sel_bln_lalu'] = $b_lalu;
			$data['sel_thn_lalu'] = $t_lalu;
			$data['sel_bln_lanjut'] = $b_lanjut;
			$data['sel_thn_lanjut'] = $t_lanjut;

			$data['tgl_rab_lalu'] = $tgl_rab_lalu;		
			$data['tgl_rab_lanjut'] = $tgl_rab_lanjut;

			$this->load->view('posisi_biaya_konstruksi',$data);
		}
	}

	public function printed_pbk()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			if ($this->input->get('bln') && $this->input->get('thn')) {
				$b = $this->input->get('bln');
				$t = $this->input->get('thn');
				$tgl_rab = $this->tgl_to_pbk($b,$t);
			} else {
				$b = date('n');
				$t = date('Y');
				$tgl_rab = $this->tgl_to_pbk($b,$t);
			}

			$q_tg = $this->db->query("select tahap_tanggal_kendali from simpro_tbl_po2 where tahap_tanggal_kendali <= '$t-$b-01' order by tahap_tanggal_kendali desc limit 1");

			if ($q_tg->result()) {
				$tg = $q_tg->row()->tahap_tanggal_kendali;
			} else {
				$tg = '0001-01-01';
			}

			$divisi_id = $this->session->userdata('divisi_id');
			$data['proyek'] = $this->mdl_laporan->get_data_proyek_pbk($proyek_id);
			$data['divisi'] = $this->mdl_laporan->get_data_divisi_pbk($divisi_id);
			$data['tgl_rab'] = $tgl_rab;
			$data['sel_bln'] = $b;
			$data['sel_thn'] = $t;

			$data['tg'] = $tg;

			$b_min = $b - 1;
			$b_plus = $b + 1;

			if ($b_min == 0) {
				$b_lalu = 12;
				$t_lalu = $t-1;
				$tgl_rab_lalu = $this->tgl_to_pbk(12,($t-1));
			} else {
				$b_lalu = $b_min;
				$t_lalu = $t;
				$tgl_rab_lalu = $this->tgl_to_pbk($b_min,($t));
			}

			if ($b_plus == 13) {
				$b_lanjut = 1;
				$t_lanjut = $t+1;
				$tgl_rab_lanjut = $this->tgl_to_pbk(1,($t+1));
			} else {
				$b_lanjut = $b_plus;
				$t_lanjut = $t;
				$tgl_rab_lanjut = $this->tgl_to_pbk($b_plus,($t));
			}

			$data['sel_bln_lalu'] = $b_lalu;
			$data['sel_thn_lalu'] = $t_lalu;
			$data['sel_bln_lanjut'] = $b_lanjut;
			$data['sel_thn_lanjut'] = $t_lanjut;

			$data['tgl_rab_lalu'] = $tgl_rab_lalu;		
			$data['tgl_rab_lanjut'] = $tgl_rab_lanjut;

			$this->load->view('printed_pbk',$data);
		}
	}

	function tgl_to_pbk($bln,$thn)
	{
		$bl[1]="January";
		$bl[2]="February";
		$bl[3]="March";
		$bl[4]="April";
		$bl[5]="May";
		$bl[6]="Juni";
		$bl[7]="July";
		$bl[8]="August";
		$bl[9]="September";
		$bl[10]="October";
		$bl[11]="November";
		$bl[12]="December";

		$tgl_b = $bl[$bln].' - '.$thn;
		return $tgl_b;
	}

	function insert_cashout($tgl_rab='0001-01-01')
	{
		$proyek_id = $this->session->userdata('proyek_id');

		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$data = array(
				'tanggal_kendali' => $tgl_rab,
				'proyek_id' => $proyek_id,
				'melalui_proyek' => $this->input->post('melalui_proyek'),
				'melalui_divisi' => $this->input->post('melalui_divisi'),
				'melalui_pusat' => $this->input->post('melalui_pusat'),
				'jenis_cashout' => $this->input->post('jenis_cashout')
			);

			$this->db->insert('simpro_tbl_cashout_pbk',$data);
		}
	}
	
	public function rekap_proyek()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$tgl_rab = date('Y-m-d');
			$data['proyek'] = $this->mdl_laporan->get_data_proyek_pbk($proyek_id);
			$data['total_rab'] = $this->mdl_laporan->get_total_rab($proyek_id);
			$data['total_rap'] = $this->mdl_laporan->get_total_rap($proyek_id);
			$data['total_pek'] = $this->mdl_laporan->get_total_total_kerja($proyek_id,$tgl_rab);
			$data['row'] = $this->get_rekap_proyek();

			$this->load->view('rekap_proyek',$data);
		}
	}
	
	public function fm_rtc11()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			redirect(base_url().'pengendalian/schedule_cart/proyek');
		// $this->load->view('rtc11');
		}
	}
	
	public function fm_rtc21()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			redirect(base_url().'pengendalian/kurvas');
		// $this->load->view('rtc21');
		}
	}
	
	public function rincian_saldo_kas()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			redirect(base_url().'transaksi/saldo_kas');
		// $this->load->view('rincian_saldo_kas');
		}
	}
	
	public function rekap_lap_proyek()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$t = date('Y');
			$b = date('n');
			$divisi_id = $this->session->userdata('divisi_id');
			$data['divisi'] = $this->mdl_laporan->get_data_divisi_pbk($divisi_id);
			$data['proyek'] = $this->mdl_laporan->get_data_proyek_pbk($proyek_id);
			$data['row'] = $this->mdl_laporan->get_rekap_laporan_proyek($proyek_id);
			$data['tgl'] = $this->tgl_to_pbk($b,$t);
			$this->load->view('rekap_laporan_proyek',$data);
		}
	}

	public function printed_rlp()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$t = date('Y');
			$b = date('n');
			$divisi_id = $this->session->userdata('divisi_id');
			$data['divisi'] = $this->mdl_laporan->get_data_divisi_pbk($divisi_id);
			$data['proyek'] = $this->mdl_laporan->get_data_proyek_pbk($proyek_id);
			$data['row'] = $this->mdl_laporan->get_rekap_laporan_proyek($proyek_id);
			$data['tgl'] = $this->tgl_to_pbk($b,$t);
			$this->load->view('printed_rlp',$data);
		}
	}
	
	public function lap_akhir_proyek()
	{
	}
	
	public function lap_penggunaan_bulanan()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			redirect(base_url().'pengendalian/schedule_cart/peralatan');
		}
	}

	public function sisa_anggaran_bk_akuntan()
	{

		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			if ($this->input->post('bln') && $this->input->post('thn')) {
				$b = $this->input->post('bln');
				$t = $this->input->post('thn');
			} else {
				$b = date('n');
				$t = date('Y');
			}

			$data['proyek'] = $this->mdl_laporan->get_data_proyek_pbk($proyek_id);

			$data['tgl_rab'] = sprintf("$t-%02s-01",$b);
			$data['sel_bln'] = $b;
			$data['sel_thn'] = $t;
			$data['idproyek'] = $proyek_id;

			$this->load->view('sisa_anggaran_bk_akuntan',$data);
		}

		
	}

	public function anggaran_bk_akuntan_revisi()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			if ($this->input->post('bln') && $this->input->post('thn')) {
				$b = $this->input->post('bln');
				$t = $this->input->post('thn');
			} else {
				$b = date('n');
				$t = date('Y');
			}

			$data['proyek'] = $this->mdl_laporan->get_data_proyek_pbk($proyek_id);

			$data['tgl_rab'] = sprintf("$t-%02s-01",$b);
			$data['sel_bln'] = $b;
			$data['sel_thn'] = $t;
			$data['idproyek'] = $proyek_id;

			$this->load->view('anggaran_bk_akuntan_revisi',$data);
		}
	}

	public function laporan_realisasi()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->cek_pengendalian($proyek_id) <= 0) {
			echo "<h2 align='center'>Silahkan buat pengendalian terlebih dahulu!</h2>";
		} else {
			$this->load->view('laporan_realisasi');
		}
	}

	function get_rekap_proyek()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		$data = $this->mdl_laporan->get_rekap_proyek($proyek_id);

		return $data;
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

		echo json_encode(array('data' => $bln));
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
		echo json_encode(array('data' => $thn));
	}

	function cek_pengendalian($proyek_id)
	{
		$sql_hasil = "select * from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id";
		$q = $this->db->query($sql_hasil)->num_rows();
		return $q;
	}

}