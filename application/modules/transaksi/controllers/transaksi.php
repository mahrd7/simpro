<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Transaksi extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_transaksi');		
		if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");		
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}
	
    public function toko()
	{
		$this->load->view('toko');
	}
	
	public function cash()
	{
		$this->load->view('cash_to_date');
	}
	
	public function antisipasi()
	{
		$this->load->view('antisipasi');
	}
	
	public function hutang()
	{
		$this->load->view('hutang');
	}
	
	public function klad_kas()
	{
		$this->load->view('klad_kas');
	}
	
	public function saldo_kas()
	{
		$this->load->view('saldo_kas');
	}
		
	public function kartu_termin()
	{
		$this->load->view('kartu_piutang');
	}

	function kartu_piutang_action($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		////$proyek_id = '1';
		
		$ppn = ($this->input->post('nilai_tagihan') + $this->input->post('uangmuka_pot') + $this->input->post('retensi_pot')) / 11;
		$netto = $this->input->post('nilai_tagihan') + $this->input->post('uangmuka_pot') + $this->input->post('retensi_pot') + $ppn;
		$jumlah = $netto + $this->input->post('pph23_pot') + $this->input->post('bank_pot') + $this->input->post('lain_pot');

		$data = array(
			'proyek_id' => $proyek_id,
			'no' => $this->input->post('no'), 
			'uraian' => $this->input->post('uraian'),
			'tanggal_pengajuan_tagihan' => $this->input->post('tgl_tagihan'),
			'prog_pengajuan_tagihan' => $this->input->post('prog_tagihan'),
			'nilai_pengajuan_tagihan' => $this->input->post('nilai_tagihan'),
			'potongan_uang_muka' => $this->input->post('uangmuka_pot'),
			'potongan_retensi' => $this->input->post('retensi_pot'),
			'potongan_ppn' => $ppn,
			'jumlah_neto' => $netto,
			'potongan_pph23' => $this->input->post('pph23_pot'),
			'potongan_bank' => $this->input->post('bank_pot'),
			'potongan_lain' => $this->input->post('lain_pot'),
			'tanggal_penerimaan_bersih' => $this->input->post('tgl_penerimaan'),
			'jumlah' => $jumlah,
			'keterangan' => $this->input->post('keterangan')
		);

		switch ($info) {
			case 'tambah':
				$this->mdl_transaksi->kartu_piutang_action($info,$data,'');
			break;
			case 'edit':
				$id = $this->input->post('id');
				$this->mdl_transaksi->kartu_piutang_action($info,$data,$id);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}
	
    public function item_satuan()
	{
		$this->load->view('item_satuan');
	}
	
	public function rincian_hutang()
	{
		$this->load->view('rincian_hutang');
	}
	
    public function klad_bank()
	{
		$this->load->view('klad_bank');
	}
		
	function get_data_toko()
	{
		$limit = $this->input->get('limit');
		$offset = $this->input->get('start');

		if ($this->input->get('text')) {
			$data = $this->mdl_transaksi->get_data_toko($limit,$offset,$this->input->get('text'));
		} else {
			$data = $this->mdl_transaksi->get_data_toko($limit,$offset,'');
		}
		echo $data;
	}

	function insert_pilih_toko()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');

		$toko_id = $this->input->post('toko_id');

		$data = array(
			'proyek_id' => $proyek_id,
			'toko_id'=> $toko_id,
			'user_id' =>$user_update,
			'tgl_update'=> $tgl_update,
			'ip_update' =>$ip_update,
			'divisi_id' =>$divisi_id,
			'waktu_update'=> $waktu_update
		);

		$cek_pilih_toko = $this->mdl_transaksi->cek_pilih_toko($toko_id,$proyek_id);
		
		if ($cek_pilih_toko == 'kosong') {
			$this->mdl_transaksi->insert_pilih_toko($data);
		}
	}

	function get_data_pilih_toko()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$limit = $this->input->get('limit');
		$offset = $this->input->get('start');

		$data = $this->mdl_transaksi->get_data_pilih_toko($limit,$offset,$proyek_id);
		
		echo $data;
	}

	function deletedata($page="")
	{
		$id = $this->input->post('id');

		switch ($page) {
			case 'toko':
				if($id)
				{
					$this->mdl_transaksi->deletedata($id);
				}
				break;
			
			default:
				# code...
				break;
		}
	}

	function deletedataall($page="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($page) {
			case 'toko':
				if($proyek_id)
				{
					$this->mdl_transaksi->deletedataall($proyek_id);
				}
				break;
			
			default:
				# code...
				break;
		}
	}

	function get_data_divisi()
	{
		$data = $this->mdl_transaksi->get_data_divisi();
		echo $data;
	}

	function get_data_proyek()
	{
		if (!$this->input->get('divisi')) {
			echo "Acces Forbidden";
		} else {
			$divisi = $this->input->get('divisi');
			$data = $this->mdl_transaksi->get_data_proyek($divisi);
			echo $data;
		}
	}

	function get_data_tanggal()
	{
		if (!$this->input->get('proyek')) {
			echo "Acces Forbidden";
		} else {
			$proyek = $this->input->get('proyek');
			$data = $this->mdl_transaksi->get_data_tanggal($proyek);
			echo $data;
		}
	}

	function insertdatacopy()
	{
		$divisi = $this->input->post('divisi');
		$proyek = $this->input->post('proyek');
		$tanggal = $this->input->post('tanggal');

		if (empty($tanggal) && empty($divisi) && empty($proyek)) {
			echo "Acces Forbidden";
		} else {
			$data = $this->mdl_transaksi->insertdatacopy($divisi,$proyek,$tanggal);
			echo $data;
		}

	}

	function get_data_cashtodate()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$sort = $this->input->get('sort');
		$pilihan_sort = $this->input->get('pilihan_sort');
		$tgl_awal = $this->convertdate($this->input->get('tgl_awal'));
		$tgl_akhir = $this->convertdate($this->input->get('tgl_akhir'));

		$get = array(
			'sort' => $sort,
			'pilihan_sort' => $pilihan_sort,
			'tgl_awal' => $tgl_awal,
			'tgl_akhir' => $tgl_akhir
		);

		//$proyek_id = '1';

		// var_dump($get);
		$data = $this->mdl_transaksi->get_data_cashtodate($proyek_id,$get);
		echo $data;
	}

	function get_data_hutang()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$sort = $this->input->get('sort');
		$pilihan_sort = $this->input->get('pilihan_sort');
		$tgl_awal = $this->convertdate($this->input->get('tgl_awal'));
		$tgl_akhir = $this->convertdate($this->input->get('tgl_akhir'));

		$get = array(
			'sort' => $sort,
			'pilihan_sort' => $pilihan_sort,
			'tgl_awal' => $tgl_awal,
			'tgl_akhir' => $tgl_akhir
		);

		//$proyek_id = '1';

		// var_dump($get);
		$data = $this->mdl_transaksi->get_data_hutang($proyek_id,$get);
		echo $data;
	}

	function get_data_antisipasi()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$sort = $this->input->get('sort');
		$pilihan_sort = $this->input->get('pilihan_sort');
		$tgl_awal = $this->convertdate($this->input->get('tgl_awal'));
		$tgl_akhir = $this->convertdate($this->input->get('tgl_akhir'));

		$get = array(
			'sort' => $sort,
			'pilihan_sort' => $pilihan_sort,
			'tgl_awal' => $tgl_awal,
			'tgl_akhir' => $tgl_akhir
		);

		//$proyek_id = '1';

		// var_dump($get);
		$data = $this->mdl_transaksi->get_data_antisipasi($proyek_id,$get);
		echo $data;
	}

	function get_combo_item_toko()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$data = $this->mdl_transaksi->get_combo_item_toko($proyek_id);
		echo $data;
	}

	function get_combo_item_material()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$data = $this->mdl_transaksi->get_combo_item_material($proyek_id);
		echo $data;
	}

	function get_combo_item_material_antisipasi()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$data = $this->mdl_transaksi->get_combo_item_material_antisipasi($proyek_id);
		echo $data;
	}

	function get_combo_pilihan()
	{
		$data = $this->mdl_transaksi->get_combo_pilihan();
		echo $data;
	}

	function insertdata($info="")
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($info) {
			case 'cashtodate':
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$kode_toko = $this->input->post('kode_toko');
				$tanggal = $this->input->post('tanggal');
				$pilihan = $this->input->post('pilihan');
				$item = $this->input->post('item');
				$volume = $this->input->post('volume');
				$jumlah_bayar = $this->input->post('jumlah_bayar');
				$uraian = $this->input->post('uraian');
				$debet = $this->input->post('debet');
				$status = $this->input->post('status');
				$keterangan_item = $this->input->post('keterangan_item');
				$kode_rap = $this->input->post('kode_rap');
				$detail_material_kode = $this->input->post('detail_material_kode');

				if ($status == 'kredit') {
					$data = array(
						'no_bukti' => $no_bukti, 
						'pic' => $pic, 
						'pilih_toko_id' => $kode_toko, 
						'tanggal' => $tanggal, 
						'pilihan' => $pilihan, 
						'detail_material_id' => $item, 
						'volume' => $volume, 
						'jumlah' => $jumlah_bayar,
						'uraian' => $uraian,
						'proyek_id' => $proyek_id,
						'keterangan_item' => $keterangan_item,
						'detail_material_kode' => $detail_material_kode,
						'kode_rap' => $kode_rap,
						'debet' => 0
					);
				} else {
					$data = array(
						'no_bukti' => $no_bukti, 
						'pic' => $pic, 
						'pilih_toko_id' => 0, 
						'tanggal' => $tanggal, 
						'pilihan' => 0, 
						'detail_material_id' => 0, 
						'volume' => 0, 
						'jumlah' => 0,
						'uraian' => $uraian,
						'proyek_id' => $proyek_id,
						'keterangan_item' => '-',
						'detail_material_kode' => '-',
						'kode_rap' => '-',
						'debet' => $debet
					);
				}

				

				// var_dump($status);
				$this->mdl_transaksi->insertdata($info,$data);

			break;
			case 'kladbank':
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$kode_toko = $this->input->post('kode_toko');
				$tanggal = $this->input->post('tanggal');
				$pilihan = $this->input->post('pilihan');
				$item = $this->input->post('item');
				$volume = $this->input->post('volume');
				$jumlah_bayar = $this->input->post('jumlah_bayar');
				$uraian = $this->input->post('uraian');
				$debet = $this->input->post('debet');
				$status = $this->input->post('status');
				$keterangan_item = $this->input->post('keterangan_item');
				$kode_rap = $this->input->post('kode_rap');
				$detail_material_kode = $this->input->post('detail_material_kode');

				if ($status == 'kredit') {
					$data = array(
						'no_bukti' => $no_bukti, 
						'pic' => $pic, 
						'pilih_toko_id' => $kode_toko, 
						'tanggal' => $tanggal, 
						'pilihan' => $pilihan, 
						'detail_material_id' => $item, 
						'volume' => $volume, 
						'jumlah' => $jumlah_bayar,
						'uraian' => $uraian,
						'proyek_id' => $proyek_id,
						'keterangan_item' => $keterangan_item,
						'detail_material_kode' => $detail_material_kode,
						'kode_rap' => $kode_rap,
						'debet' => 0
					);
				} else {
					$data = array(
						'no_bukti' => $no_bukti, 
						'pic' => $pic, 
						'pilih_toko_id' => 0, 
						'tanggal' => $tanggal, 
						'pilihan' => 0, 
						'detail_material_id' => 0, 
						'volume' => 0, 
						'jumlah' => 0,
						'uraian' => $uraian,
						'proyek_id' => $proyek_id,
						'keterangan_item' => '-',
						'detail_material_kode' => '-',
						'kode_rap' => '-',
						'debet' => $debet
					);
				}
				

				// var_dump($status);
				$this->mdl_transaksi->insertdata($info,$data);

			break;
			case 'hutang':
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$kode_toko = $this->input->post('kode_toko');
				$tanggal = $this->input->post('tanggal');
				$pilihan = $this->input->post('pilihan');
				$item = $this->input->post('item');
				$volume = $this->input->post('volume');
				$jumlah_bayar = $this->input->post('jumlah_bayar');
				$uraian = $this->input->post('uraian');
				$keterangan_item = $this->input->post('keterangan_item');
				$kode_rap = $this->input->post('kode_rap');
				$detail_material_kode = $this->input->post('detail_material_kode');

				$data = array(
					'no_bukti' => $no_bukti, 
					'pic' => $pic, 
					'pilih_toko_id' => $kode_toko, 
					'tanggal' => $tanggal, 
					'pilihan' => $pilihan, 
					'detail_material_id' => $item, 
					'volume' => $volume, 
					'jumlah' => $jumlah_bayar,
					'uraian' => $uraian,
					'proyek_id' => $proyek_id,
					'keterangan_item' => $keterangan_item,
					'detail_material_kode' => $detail_material_kode,
					'kode_rap' => $kode_rap
				);

				// var_dump($data);
				$this->mdl_transaksi->insertdata($info,$data);

			break;
			case 'antisipasi':
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$kode_toko = $this->input->post('kode_toko');
				$tanggal = $this->input->post('tanggal');
				$pilihan = $this->input->post('pilihan');
				$item = $this->input->post('item');
				$volume = $this->input->post('volume');
				$jumlah_bayar = $this->input->post('jumlah_bayar');
				$uraian = $this->input->post('uraian');
				$kode_rap = $this->input->post('kode_rap');
				$keterangan_item = $this->input->post('keterangan_item');
				$detail_material_kode = $this->input->post('detail_material_kode');

				$data = array(
					'no_bukti' => $no_bukti, 
					'pic' => $pic, 
					'pilih_toko_id' => $kode_toko, 
					'tanggal' => $tanggal, 
					'pilihan' => $pilihan, 
					'detail_material_id' => $item, 
					'volume' => $volume, 
					'jumlah' => $jumlah_bayar,
					'uraian' => $uraian,
					'proyek_id' => $proyek_id,
					'keterangan_item' => $keterangan_item,
					'detail_material_kode' => $detail_material_kode,
					'kode_rap' => $kode_rap
				);

				// var_dump($data);
				$this->mdl_transaksi->insertdata($info,$data);

			break;
			case 'bayar_hutang':
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$tanggal = $this->input->post('tanggal');
				$bayar = $this->input->post('bayar');
				$no_bukti_bayar_hutang = $this->input->post('no_bukti_bayar_hutang');

				$data = array(
					'no_bukti' => $no_bukti, 
					'pic' => $pic, 
					'tanggal' => $tanggal,
					'bayar' => $bayar,
					'proyek_id' => $proyek_id,
					'no_bukti_bayar_hutang' => $no_bukti_bayar_hutang
				);

				// var_dump($data);
				$this->mdl_transaksi->insertdata($info,$data);

			break;
			// case 'kladbank':
			// 	$tanggal = $this->input->post('tanggal');
			// 	$pic = $this->input->post('pic');
			// 	$no_bukti = $this->input->post('no_bukti');
			// 	$status = $this->input->post('status');
			// 	$keterangan = $this->input->post('keterangan');
			// 	$jumlah = $this->input->post('jumlah');

			// 	if ($status == 'debet') {
			// 		$debet = $jumlah;
			// 		$kredit = 0;
			// 	} elseif ($status == 'kredit') {
			// 		$debet = 0;
			// 		$kredit = $jumlah;
			// 	}

			// 	$data = array(
			// 		'no_bukti' => $no_bukti, 
			// 		'pic' => $pic, 
			// 		'keterangan' => $keterangan, 
			// 		'tanggal' => $tanggal, 
			// 		'debet' => $debet, 
			// 		'kredit' => $kredit, 
			// 		'proyek_id' => $proyek_id
			// 	);

			// 	$this->mdl_transaksi->insertdata($info,$data);
			// break;
			// default:
			// 	echo "Access Forbidden";
			// break;
		}
	}

	function delete_data($info=""){
		switch ($info) {
			case 'cashtodate':
				$id = $this->input->post('id');
				$data = array(
					'id' => $id, 
				);
				$this->mdl_transaksi->delete_data($info,$data);
			break;
			case 'hutang':
				$id = $this->input->post('id');
				$no_bukti = $this->input->post('no_bukti');
				$data = array(
					'id' => $id, 
					'no_bukti' => $no_bukti
				);

				$this->mdl_transaksi->delete_data($info,$data);
			break;
			case 'antisipasi':
				$id = $this->input->post('id');
				$data = array(
					'id' => $id
				);
				$this->mdl_transaksi->delete_data($info,$data);
			break;
			case 'kladbank':
				$id = $this->input->post('id');
				$data = array(
					'id' => $id
				);
				$this->mdl_transaksi->delete_data($info,$data);
			break;
			case 'bayar_hutang':
				$id = $this->input->post('id');
				$data = array(
					'id' => $id 
				);
				$this->mdl_transaksi->delete_data($info,$data);
			break;
			case 'piutang':
				$id = $this->input->post('id');
				$data = array(
					'id' => $id 
				);
				$this->mdl_transaksi->delete_data($info,$data);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function editdata($info=""){
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		switch ($info) {
			case 'cashtodate':

				$id = $this->input->post('id');
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$kode_toko = $this->input->post('kode_toko');
				$tanggal = $this->input->post('tanggal');
				$pilihan = $this->input->post('pilihan');
				$item = $this->input->post('item');
				$volume = $this->input->post('volume');
				$jumlah_bayar = $this->input->post('jumlah_bayar');
				$uraian = $this->input->post('uraian');
				$debet = $this->input->post('debet');
				$status = $this->input->post('status');
				$keterangan_item = $this->input->post('keterangan_item');
				$kode_rap = $this->input->post('kode_rap');

				$data = array(
					'no_bukti' => $no_bukti, 
					'pic' => $pic, 
					'pilih_toko_id' => $kode_toko, 
					'tanggal' => $tanggal, 
					'pilihan' => $pilihan, 
					'detail_material_id' => $item, 
					'volume' => $volume, 
					'jumlah' => $jumlah_bayar,
					'uraian' => $uraian,
					'proyek_id' => $proyek_id,
					'keterangan_item' => $keterangan_item,
					'debet' => $debet,
					'kode_rap' => $kode_rap
				);

				$this->mdl_transaksi->editdata($info,$data,$id);
			break;
			case 'kladbank':

				$id = $this->input->post('id');
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$kode_toko = $this->input->post('kode_toko');
				$tanggal = $this->input->post('tanggal');
				$pilihan = $this->input->post('pilihan');
				$item = $this->input->post('item');
				$volume = $this->input->post('volume');
				$jumlah_bayar = $this->input->post('jumlah_bayar');
				$uraian = $this->input->post('uraian');
				$debet = $this->input->post('debet');
				$status = $this->input->post('status');
				$keterangan_item = $this->input->post('keterangan_item');
				$kode_rap = $this->input->post('kode_rap');

				$data = array(
					'no_bukti' => $no_bukti, 
					'pic' => $pic, 
					'pilih_toko_id' => $kode_toko, 
					'tanggal' => $tanggal, 
					'pilihan' => $pilihan, 
					'detail_material_id' => $item, 
					'volume' => $volume, 
					'jumlah' => $jumlah_bayar,
					'uraian' => $uraian,
					'proyek_id' => $proyek_id,
					'keterangan_item' => $keterangan_item,
					'debet' => $debet,
					'kode_rap' => $kode_rap
				);

				$this->mdl_transaksi->editdata($info,$data,$id);
			break;
			case 'hutang':

				$id = $this->input->post('id');
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$kode_toko = $this->input->post('kode_toko');
				$tanggal = $this->input->post('tanggal');
				$pilihan = $this->input->post('pilihan');
				$item = $this->input->post('item');
				$volume = $this->input->post('volume');
				$jumlah_bayar = $this->input->post('jumlah_bayar');
				$uraian = $this->input->post('uraian');
				$keterangan_item = $this->input->post('keterangan_item');
				$kode_rap = $this->input->post('kode_rap');

				$data = array(
					'no_bukti' => $no_bukti, 
					'pic' => $pic, 
					'pilih_toko_id' => $kode_toko, 
					'tanggal' => $tanggal, 
					'pilihan' => $pilihan, 
					'detail_material_id' => $item, 
					'volume' => $volume, 
					'jumlah' => $jumlah_bayar,
					'uraian' => $uraian,
					'proyek_id' => $proyek_id,
					'keterangan_item' => $keterangan_item,
					'kode_rap' => $kode_rap
				);

				$this->mdl_transaksi->editdata($info,$data,$id);
			break;
			case 'antisipasi':

				$id = $this->input->post('id');
				$no_bukti = $this->input->post('no_bukti');
				$pic = $this->input->post('pic');
				$kode_toko = $this->input->post('kode_toko');
				$tanggal = $this->input->post('tanggal');
				$pilihan = $this->input->post('pilihan');
				$item = $this->input->post('item');
				$volume = $this->input->post('volume');
				$jumlah_bayar = $this->input->post('jumlah_bayar');
				$uraian = $this->input->post('uraian');
				$kode_rap = $this->input->post('kode_rap');
				$keterangan_item = $this->input->post('keterangan_item');

				$data = array(
					'no_bukti' => $no_bukti, 
					'pic' => $pic, 
					'pilih_toko_id' => $kode_toko, 
					'tanggal' => $tanggal, 
					'pilihan' => $pilihan, 
					'detail_material_id' => $item, 
					'volume' => $volume, 
					'jumlah' => $jumlah_bayar,
					'uraian' => $uraian,
					'proyek_id' => $proyek_id,
					'keterangan_item' => $keterangan_item,
					'kode_rap' => $kode_rap
				);

				$this->mdl_transaksi->editdata($info,$data,$id);
			break;
			default:
				echo "Access Forbidden";
			break;
		}
	}

	function get_data_kladbank()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		$sort = $this->input->get('sort');
		$pilihan_sort = $this->input->get('pilihan_sort');
		$tgl_awal = $this->convertdate($this->input->get('tgl_awal'));
		$tgl_akhir = $this->convertdate($this->input->get('tgl_akhir'));

		$get = array(
			'sort' => $sort,
			'pilihan_sort' => $pilihan_sort,
			'tgl_awal' => $tgl_awal,
			'tgl_akhir' => $tgl_akhir
		);

		//$proyek_id = '1';

		// var_dump($get);
		$data = $this->mdl_transaksi->get_data_kladbank($proyek_id,$get);
		echo $data;
	}

	function get_combo_no_bukti()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		$data = $this->mdl_transaksi->get_combo_no_bukti($proyek_id);
		echo $data;
	}

	function get_bayar_hutang()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';
		$no_bukti = $this->input->get('no_bukti');

		$data = $this->mdl_transaksi->get_bayar_hutang($proyek_id,$no_bukti);
		echo $data;
	}

	function convertdate($val)
	{		
		$tgl = date("Y-m-d", strtotime($val));
		return $tgl;
	}

	function get_data_laporan_hutang()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 

		$tgl_awal = $this->convertdate($this->input->get('tgl_awal'));
		$tgl_akhir = $this->convertdate($this->input->get('tgl_akhir'));

		$get = array(
			'tgl_awal' => $tgl_awal,
			'tgl_akhir' => $tgl_akhir
		);

		//$proyek_id = '1';

		// var_dump($get);
		$data = $this->mdl_transaksi->get_data_laporan_hutang($proyek_id,$get);
		echo $data;
	}

	function get_data_saldo_kas()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		//$proyek_id = '1';

		// var_dump($get);
		// $tgl_awal = $this->convertdate($this->input->get('tgl_awal'));
		$tgl_akhir = $this->convertdate($this->input->get('tgl_akhir'));
		
		$get = array(
			// 'tgl_awal' => $tgl_awal,
			'tgl_akhir' => $tgl_akhir
		);

		$data = $this->mdl_transaksi->get_data_saldo_kas($proyek_id,$get);
		echo $data;
	}

	function get_data_piutang()
	{
		$proyek_id = $this->session->userdata('proyek_id'); 
		////$proyek_id = '1';

		// var_dump($get);
		$data = $this->mdl_transaksi->get_data_piutang($proyek_id);
		echo $data;
	}
}