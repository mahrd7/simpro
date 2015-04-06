<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Rencana extends MX_Controller {

	var $idpro = '';
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_rencana');
		$this->load->model('mdl_analisa');
		$this->db->query("SET CLIENT_ENCODING TO 'UTF8'");		
	}
			
	public function tambah_tender()
	{
		$resP = false;
		if($this->input->post('nama_proyek') && $this->input->post('jenis_proyek')
		&& $this->input->post('tanggal_tender') && $this->input->post('divisi_id'))
		{
			$resdb = $this->db->insert('simpro_m_rat_proyek_tender', $this->input->post());
			if($resdb)
			{
				$resP = true;
			} else $resP = false;
		} 		
		if($resP) echo json_encode(array('success' => true, 'message' => 'Data tender berhasil ditambahkan!'));		
			else echo json_encode(array('success' => false, 'message' => 'Data tender gagal ditambahkan!'));
	}

	public function get_data_tender_dashboard()
	{
		$div_id = $this->session->userdata('divisi_id');
		$datatender = $this->mdl_rencana->data_tender($div_id);
		$this->_out($datatender);
	}
	
	function get_proyek()
	{
		$proyek = $this->mdl_rencana->pilih_proyek($this->session->userdata('divisi_id'));
		if($proyek['total'] > 0)
		{
			$data = array('success'=>true, 'data'=>$proyek['data'], 'total'=>$proyek['total']);
			$this->_out($data);
		} else json_encode(array('success'=>true, 'data'=>NULL, 'total'=>0));
	}

	function get_proyek_pilih()
	{
		$divisi_id = $this->input->get('divisi_id');
		$proyek = $this->mdl_rencana->pilih_proyek_pilih($divisi_id);
		if($proyek['total'] > 0)
		{
			$data = array('success'=>true, 'data'=>$proyek['data'], 'total'=>$proyek['total']);
			$this->_out($data);
		} else json_encode(array('success'=>true, 'data'=>NULL, 'total'=>0));
	}
	
	public function get_data_tender()
	{
		$div_id = $this->session->userdata('divisi_id');
		$idtender = ($this->session->userdata('id_tender') <> '' ) ? $this->session->userdata('id_tender') : @$_SESSION['idtender'];
		if(isset($idtender))
		{
			$datatender = $this->mdl_rencana->data_tender($div_id, $idtender);
		} else $datatender = $this->mdl_rencana->data_tender($div_id);
		$this->_out($datatender);
	}

	function del_item_apek()
	{
		if($this->input->post('id_analisa_item_apek'))
		{
			$this->db->where('id_analisa_item_apek',$this->input->post('id_analisa_item_apek'));
			$this->db->delete('simpro_rat_analisa_item_apek');
		}	
	}
	
	public function get_data_analisa_apek($idtender)
	{	
		$data = $this->mdl_rencana->get_data_item_analisa_apek($idtender);
		$this->_out($data);
	}

	# direct cost
	public function get_data_dc($idtender)
	{	
		$data = $this->mdl_rencana->data_direct_cost($idtender);
		$this->_out($data);
	}
	
	public function get_status_tender()
	{
		$data = $this->mdl_rencana->status_tender();
		$this->_out($data);	
	}
	
	public function get_tipe_rat()
	{		
		$data = $this->mdl_rencana->type_rat();
		$this->_out($data);
	}
	
	public function get_sub_rat()
	{		
		$data = $this->mdl_rencana->kat_rat();
		$this->_out($data);
	}
	
	public function get_subbidang()
	{				
		$data = $this->mdl_rencana->get_subbidang();
		$this->_out($data);
	}
	
	public function set_tender_id()
	{		
		$this->session->set_userdata('id_tender', $this->input->post('tenderid'));
		$_SESSION['idtender'] = $this->input->post('tenderid');
	}
	
	public function get_detailmaterial_kode()
	{
		$data = $this->mdl_rencana->get_detailmaterial_kode($this->input->get('query'));
		$this->_out($data);	
	}
	
	public function get_harga_satuan()
	{	
		$data = $this->mdl_rencana->harga_satuan_kerja();
		$this->_out($data);
	}
	
	public function get_tbl_data_umum()
	{	
		$data = $this->mdl_rencana->get_detailmaterial_kode('505');
		$this->_out($data);
	}
	
	function get_kategori_pekerjaan() 
	{		
		$data = $this->mdl_rencana->kategori_pekerjaan();	
		$this->_out($data);
	}

	function get_divisi() 
	{		
		$data = $this->mdl_rencana->get_divisi();	
		$this->_out($data);
	}
	
	public function tambah_direct_cost($idtender)
	{
		// && $this->input->post('id_satuan_pekerjaan')
		if($this->input->post('volume')  && $this->input->post('harga') && $this->input->post('id_type_rat') && $this->input->post('id_kat_rat'))
		{
			if(is_numeric($this->input->post('id_satuan_pekerjaan'))) $satker = $this->input->post('id_satuan_pekerjaan');
				else $satker = NULL;
			if(is_numeric($this->input->post('id_kategori_pekerjaan'))) $katker = $this->input->post('id_kategori_pekerjaan');
				else $katker = NULL;
				
			//$idtender =  ($this->session->userdata('id_tender') <> '') ? $this->session->userdata('id_tender') :  $_SESSION['idtender'];

			$datainsert = array(
				'id_type_rat' => $this->input->post('id_type_rat'),
				'id_kat_rat' => $this->input->post('id_kat_rat'),
				'id_proyek_rat' => $idtender,
				'id_satuan_pekerjaan' =>  $satker,
				'id_kategori_pekerjaan' => $katker,
				'volume' => $this->input->post('volume'),
				'harga' => $this->input->post('harga')
			);
			$resin = $this->db->insert('simpro_t_rat_direct_cost', $datainsert);
			if($resin)
			{
				echo json_encode(array('success'=>true, 'message'=>'Data Direct Cost berhasil disimpan!'));
			} else json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));
		} 
	}
		
	public function tambah_indirect_cost($idtender)
	{
		if($this->input->post('icvolume')  && $this->input->post('icharga') && $this->input->post('id_kat_rat'))
		{
			if(is_numeric($this->input->post('id_satuan_pekerjaan'))) $satker = $this->input->post('id_satuan_pekerjaan');
				else $satker = NULL;
				
			$idtender = $this->input->post('idtender');

			$datainsert = array(
				'id_kat_rat' => $this->input->post('id_kat_rat'),
				'id_proyek_rat' => $idtender,
				'id_satuan_pekerjaan' =>  $satker,
				'icitem' => $this->input->post('icitem'),
				'icvolume' => $this->input->post('icvolume'),
				'icharga' => $this->input->post('icharga'),
				'persentase' => $this->input->post('persentase')
			);
			$resin = $this->db->insert('simpro_t_rat_indirect_cost', $datainsert);
			if($resin)
			{
				echo json_encode(array('success'=>true, 'message'=>'Data In-Direct Cost berhasil disimpan!'));
			} else json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));
		} 	
	}

	public function tambah_idc_ba($idtender)
	{
		if($this->input->post('icvolume')  && $this->input->post('icharga') && $this->input->post('id_kat_rat'))
		{				
			$idtender = $this->input->post('idtender');
			$datainsert = array(
				'id_kat_rat' => $this->input->post('id_kat_rat'),
				'id_proyek_rat' => $idtender,
				'icharga' => $this->input->post('icharga'),
				'persentase' => $this->input->post('persentase')
			);
			$resin = $this->db->insert('simpro_t_rat_indirect_cost', $datainsert);
			if($resin)
			{
				echo json_encode(array('success'=>true, 'message'=>'Data In-Direct Cost berhasil disimpan!'));
			} else json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));
		} 	
	}
	
	public function tambah_variable_cost($idtender)
	{
		if($this->input->post('lapek')  && $this->input->post('pph3persen') && $this->input->post('idtender') && 
			$this->input->post('biaya_resiko') && $this->input->post('biaya_pemasaran')
		)
		{				
			$isdata = $this->db->query(sprintf("SELECT * FROM simpro_t_rat_varcost WHERE id_proyek_rat='%d'", $idtender))->num_rows();
			if($isdata)
			{
				// update			
				$data = array(
					'biaya_resiko' => $this->input->post('biaya_resiko'),
					'biaya_pemasaran' => $this->input->post('biaya_pemasaran'),
					'pph3persen' => $this->input->post('pph3persen'),
					'lapek' => $this->input->post('lapek')
				);
				if(!$this->db->update("simpro_t_rat_varcost", $data, sprintf("id_proyek_rat = '%d'", $idtender)))
				{
					echo json_encode(array('success'=>false, 'message'=>'Data Variable Cost GAGAL diupdate!'));
				} else echo json_encode(array('success'=>true, 'message'=>'Data Variable Cost BERHASIL diupdate!'));
			} else
			{
				// insert
				$is_success = TRUE;
				$idtender = $this->input->post('idtender');
				$datainsert = array(
						'id_proyek_rat' => $idtender,
						'biaya_resiko' => $this->input->post('biaya_resiko'), 
						'biaya_pemasaran' => $this->input->post('biaya_pemasaran'), 
						'lapek' => $this->input->post('lapek'), 
						'pph3persen' => $this->input->post('pph3persen')
					);
				try {
					$resin = $this->db->insert('simpro_t_rat_varcost', $datainsert);
					if(!$resin) {
						throw new Exception('Error Message: ' . $this->db->_error_message());
						$is_success = FALSE;
					} 
				} catch(Exception $e) 
				{
					echo json_encode(
						array(
						'success'=>false, 
						'message'=>'Data GAGAL disimpan!. Error Messages: ' . $e->getMessage()
						)
					);
				}
				if($is_success)
				{
					echo json_encode(array('success'=>true, 'message'=>'Data Variable Direct Cost berhasil disimpan!'));
				} else json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));
			}
		} else echo json_encode(array('success'=>false, 'message'=>'Silahkan isi semua form!'));
	}
	
	public function get_variable_cost($id)
	{
		$data = $this->mdl_rencana->get_varcost_item($id);
		$datavc = $data['data'];
		#$this->_dump($data);
		$rat = $this->hitung_summary_rat($id);
		$nilai_kontrak = $rat['nilai_kontrak'];	
		foreach($datavc as $kvc=>$vc)
		{
			$diajukan = round(($vc['persentase'] * $nilai_kontrak ) / 100);
			$arr[] = array(
				'id_rat_varcost' => $vc['id_rat_varcost'],
				'id_proyek_rat' => $vc['id_proyek_rat'],
				'persentase' => $vc['persentase'],
				'id_varcost_item' => $vc['id_varcost_item'],
				'diajukan' => $diajukan,
				'vitem' => $vc['vitem']			
			);
		}					
		if($arr) $this->_out(array('total'=>count($arr), 'data'=>$arr));
	}
	
	public function update_varcost()
	{
		if($this->input->post('id_proyek_rat') && $this->input->post('id_rat_varcost'))
		{
			$persentase = ($this->input->post('persentase') == 0) ? 0 : $this->input->post('persentase');
			$data = array(
				'id_proyek_rat' => $this->input->post('id_proyek_rat'),
				'persentase' => $persentase
			);			
			$this->db->where('id_proyek_rat',$this->input->post('id_proyek_rat'));
			$this->db->where('id_rat_varcost',$this->input->post('id_rat_varcost'));
			if($this->db->update('simpro_t_rat_varcost', $data)) echo "Data berhasil diupdate!";
				else echo "Data GAGAL diupdate!, persentase: ", $persentase;
		} else echo "Update data GAGAL...";
	}

	function update_total_rat($idtender)
	{
		$data = $this->hitung_summary_rat($idtender);	
		if(isset($data['total_rab'])) 
		{
			$updata = array(
				'nilai_kontrak_ppn' => $data['total_rab'],
				'nilai_kontrak_excl_ppn' => $data['total']
			);
		} else
		{
			$updata = array(
				'nilai_kontrak_ppn' => 0,
				'nilai_kontrak_excl_ppn' => 0
			);
		}
		$this->db->where('id_proyek_rat', $idtender);
		$this->db->update('simpro_m_rat_proyek_tender', $updata);
	}
	
	
	function total_rat($idtender)
	{
		$this->update_total_rat($idtender);
		$data = $this->hitung_summary_rat($idtender);	
		if(isset($data['total_rab'])) echo number_format($data['total_rab'],0);
			else echo 0;
	}

	public function get_total_rat()
	{
		if($data = $this->mdl_rencana->total_rat($_SESSION['idtender']))
		{
			echo number_format($data,0);
		} else echo "0";
	}
			
	public function del_item_indirect_cost()
	{
		if($this->input->post('id_rat_indirect_cost'))
		{
			if($this->mdl_rencana->del_item_indirect_cost($this->input->post('id_rat_indirect_cost')))
				echo "Data berhasil dihapus";
					else echo "Data GAGAL dihapus";
		}
	}
	
	public function del_item_rat()
	{
		if($this->input->post('id_rat_item_analisa'))
		{
			if($this->mdl_rencana->del_item_rat($this->input->post('id_rat_item_analisa')))
				echo "Data berhasil dihapus";
					else echo "Data GAGAL dihapus";
		}
	}
	
	public function rat()
	{
		$this->load->view('rat');
	}
	
	function hitung_rat($idtender)
	{
		$nilai_kontrak = $this->mdl_rencana->hitung_nilai_kontrak($idtender);
		echo $nilai_kontrak;
	}
	
	function hitung_summary_rat($idtender)
	{	
		# inisiasi variable
		$nilai_kontrak = 0;
		$persen_bk = 0;
		$persen_idc = 0;
		$persen_idc_bk = 0;
		$persen_biaya_resiko = 0;
		$persen_biaya_pemasaran = 0;
		$persen_c1 = 0;
		$persen_abc1 = 0;
		$persen_lapek = 0;
		$persen_pph3persen = 0;
		$persen_c2 = 0;
		// $persen_biaya_contingensi = 0;
		$persen_biaya_lain2 = 0;
		
		$nilai_kontrak = $this->mdl_rencana->hitung_nilai_kontrak($idtender);
		$nilai_kontrak = ($nilai_kontrak > 0) ? $nilai_kontrak : 0;
				
		$data_tender = $this->mdl_rencana->get_data_tender($idtender);
		$total_bk = $this->mdl_rencana->hitung_biaya_konstruksi($idtender);
		
		//$total_idc = $this->mdl_rencana->total_indirect_cost($idtender);
		
		$data_bank = $this->mdl_rencana->get_data_bank($idtender);
		$data_asuransi = $this->mdl_rencana->get_data_rat_asuransi($idtender);		
		$data_biaya_umum = $this->mdl_rencana->get_data_rat_biayaumum($idtender);
		
		$total_persen_bu = 0;
		$tmp_total_bu = 0;
		if($data_biaya_umum['total'])
		{
			foreach($data_biaya_umum['data'] as $bu)
			{
				
				if ($bu['subtotal'] == 0 || $nilai_kontrak == 0) {
					$pbu = 0;
				} else {
					$pbu = number_format(($bu['subtotal'] / $nilai_kontrak),2,'.',',');
				}
				$tmp_total_bu = $tmp_total_bu + $bu['subtotal'];
				$total_persen_bu = $total_persen_bu + $pbu;
			}
		}

		$total_persen_da = 0;
		$tmp_total_da = 0;
		foreach($data_asuransi['data'] as $da)
		{
			$pda = number_format($da['persentase'],2,'.',',');
			$tmp_total_da = $tmp_total_da + (($pda * $nilai_kontrak )/100);
			$total_persen_da = $total_persen_da + $pda;
		}

		$total_persen_bank = 0;
		$tmp_total_bank = 0;
		foreach($data_bank['data'] as $db)
		{
			$pdb = number_format($db['persentase'],2,'.',',');
			$tmp_total_bank = $tmp_total_bank + (($pdb * $nilai_kontrak )/100);			
			$total_persen_bank = $total_persen_bank + $pdb;
		}
		
		$varcost = $this->mdl_rencana->hitung_varcost_item($idtender);		
		$total_vc = $this->mdl_rencana->total_varcost($idtender);		
				
		$group_idc = array(
			1 => 'Asuransi',
			7 => 'BiayaBank',
			8 => 'BiayaUmum'
		);
		
		$dataidc = array();
		foreach($group_idc as $k=>$v)
		{
			$dataidc[$v] = $this->mdl_rencana->data_indirect_cost_group($idtender, $k);
		}		
		
		$nilai_penawaran = $nilai_kontrak;
				
		# nominal
		$total_idc = $tmp_total_bank + $tmp_total_da + $tmp_total_bu;
		$ab = $total_bk['total_bk'] + $total_idc;		
		$biaya_resiko_pemasaran = (($varcost['data']['biaya_resiko'] * $nilai_kontrak) / 100) + (($varcost['data']['biaya_pemasaran'] * $nilai_kontrak) / 100) + (($varcost['data']['biaya_lain'] * $nilai_kontrak) / 100) ; //(($varcost['data']['contingency'] * $nilai_kontrak) / 100)+
		$abc1 = $ab + $biaya_resiko_pemasaran;
		$lapek = (($varcost['data']['lapek'] * $nilai_kontrak) / 100);
		$pph3persen = (($varcost['data']['pph'] * $nilai_kontrak) / 100);	
		$c2 = $lapek + $pph3persen;
		$total = $abc1 + $lapek + $pph3persen;
		$ppn10 = $total * 0.1;
		$rab = $total + $ppn10;
				
		/*
		pph = 3%
		lapek = 15%
		pemasaran = 1%
		lain2 = 0.25%
		ppn = 10%
		nilai kontrak = (biaya konstruksi + biaya umum)/(100-bank-asuransi-variablecost-pphlapek) * 100		
		*/
		
		#persentase
		if($total > 0)
		{

			if ($total_bk['total_bk'] == 0 || $total == 0) {
				$persen_bk = 0;
			} else {
				$persen_bk = ($total_bk['total_bk'] / $total) * 100;
			}

			$persen_idc = $total_persen_bu + $total_persen_da + $total_persen_bank;
			$persen_idc_bk = $persen_bk + $persen_idc;
			$persen_biaya_resiko = $varcost['data']['biaya_resiko'];
			$persen_biaya_pemasaran = $varcost['data']['biaya_pemasaran'];

			
			if ($biaya_resiko_pemasaran == 0 || $nilai_kontrak == 0) {
				$persen_c1 = 0;
			} else {
				$persen_c1 = ($biaya_resiko_pemasaran / $nilai_kontrak) * 100;
			}

			
			if ($abc1 == 0 || $total == 0) {
				$persen_abc1 = 0;
			} else {
				$persen_abc1 = ($abc1 / $total) * 100;
			}

			$persen_lapek = $varcost['data']['lapek'];
			$persen_pph3persen = $varcost['data']['pph'];

			
			if ($c2 == -0 || $total == 0) {
				$persen_c2 = 0;
			} else {
				$persen_c2 = ($c2 / $total) * 100;
			}

			// $persen_biaya_contingensi = $varcost['data']['contingency'];
			$persen_biaya_lain2 = $varcost['data']['biaya_lain'];
		} else
		{
			$persen_bk = 0;
			$persen_idc = 0;
			$persen_idc_bk = 0;
			$persen_biaya_resiko = 0;
			$persen_biaya_pemasaran = 0;
			$persen_c1 = 0;
			$persen_abc1 = 0;
			$persen_lapek = 0;
			$persen_pph3persen = 0;
			$persen_c2 = 0;
			// $persen_biaya_contingensi = 0;
			$persen_biaya_lain2 = 0;			
		}
		
		$data = array(
			'nilai_kontrak' => $nilai_kontrak,		
			'data_tender' => $data_tender['data'],
			'total_bk' => $total_bk['total_bk'],
			'data_idc' => $dataidc,
			
			'data_bank' => $data_bank,
			'data_asuransi' => $data_asuransi,
			'data_biaya_umum' => $data_biaya_umum,

			'total_idc' => $total_idc,
			'varcost' => $varcost['data'],
			'total_vc' => $total_vc['data']['total_vc'],
			'ab'	=> $ab,
			'abc1'	=> $abc1,
			'lapek' => $lapek, 
			'pph3persen' => $pph3persen,
			'c1' => $biaya_resiko_pemasaran,
			'c2' => $c2,			
			'total' => $total,
			'ppn10' => $ppn10,
			'total_rab' => $rab,
			'persen_bk' => round($persen_bk,2),
			'persen_idc' => round($persen_idc,2),
			'persen_idc_bk' => $persen_idc_bk,
			'persen_biaya_resiko' => $persen_biaya_resiko,
			'persen_biaya_pemasaran' => $persen_biaya_pemasaran,
			'persen_c1' => round($persen_c1,2),
			'persen_c2' => round($persen_c2,2),
			'persen_abc1' => round($persen_abc1,2),
			'persen_lapek' => $persen_lapek,
			'persen_pph3persen' => $persen_pph3persen,
			// 'persen_biaya_contingensi' => $persen_biaya_contingensi,
			'persen_biaya_lain2' => $persen_biaya_lain2
		);
		return $data;
	}
	
	public function summary_rat($idtender)
	{	
		$data = $this->hitung_summary_rat($idtender);
		$this->load->view('entry_data_rat', $data);
	}
	
	public function printed_rat($idtender)
	{	
		$data = $this->hitung_summary_rat($idtender);
		$this->load->view('printed_rat', $data);
	}

	function get_persen_bk($idtender)
	{
		$data = $this->hitung_summary_rat($idtender);
		$persen_bk = array(		
			'item' => 'Direct Cost', 
			'uraian' => 'Biaya Konstruksi (BK)', 
			'persen_bobot' => $data['persen_bk'], 
			'diajukan' => $data['total_bk']
		);
		echo json_encode(array('total'=>1,'data'=>$persen_bk));
	}

	function get_persen_idc($idtender)
	{
		$data = $this->hitung_summary_rat($idtender);
		$persen_idc = array();
		
		$bank = $data['data_bank']['data'];
		$asuransi = $data['data_asuransi']['data'];
		$biaya_umum = $data['data_biaya_umum']['data'];
		
		$nilai_kontrak = $data['nilai_kontrak'];
		foreach($bank as $kb=>$vb)
		{
			$diajukan = round(($vb['persentase']* $nilai_kontrak ) / 100);
			$persen = isset($vb['persentase']) ? $vb['persentase'] : 0;
			$db[] = array(
				'item' => 'Bank',
				'uraian' => $vb['icitem_bank'],
				'persen_bobot' => $persen,
				'diajukan' => $diajukan
			);
		}		
		
		foreach($asuransi as $ka=>$va)
		{
			$diajukan = round(($va['persentase'] * $nilai_kontrak ) / 100);
			$persen = isset($va['persentase']) ? $va['persentase'] : 0;
			$da[] = array(
				'item' => 'Asuransi',
				'uraian' => $va['icitem_asuransi'],
				'persen_bobot' => $persen,
				'diajukan' => $diajukan
			);
		}		

		foreach($biaya_umum as $kbu=>$vbu)
		{
			$persen = ($vbu['icharga'] / $nilai_kontrak );
			$dbu[] = array(
				'item' => 'Biaya Umum',
				'uraian' => $vbu['icitem'],
				'persen_bobot' => number_format($persen,2),
				'diajukan' => $vbu['icharga']
			);
		}
		$idc = array_merge($dbu,$da,$db);
		echo json_encode(array('total'=>count($idc),'data'=>$idc));
	}
	
	public function entry_rat()
	{
		$data = array(
			'id_tender' => $this->session->userdata('id_tender')
		);
		$this->load->view('entry_rat', $data);
	}

	public function entry_detail_rat($idpro)
	{
		$_SESSION['idtender'] = $idpro;
		$satid = $this->get_satuan_id('Ls');
		$data_tender = $this->mdl_rencana->get_data_tender($idpro);
		$frmdata = array(
			'idtender' => $idpro,
			'data_tender' => $data_tender['data']
		);
		
		# varcost
		$sql = sprintf("SELECT * FROM simpro_t_rat_varcost WHERE id_proyek_rat = '%d'", $idpro);
		$is_data = $this->db->query($sql)->num_rows();
		if($is_data <= 0)
		{		
			$sql2 = sprintf("INSERT INTO simpro_t_rat_varcost(id_varcost_item, id_proyek_rat)
			SELECT id_rat_varcost, '%d' as id_tender FROM simpro_rat_m_varcost", $idpro);
			$this->db->query($sql2);
		}

		# bank
		$sql_bank = sprintf("SELECT * FROM simpro_t_rat_idc_bank WHERE id_proyek_rat = '%d'", $idpro);
		$is_data_bank = $this->db->query($sql_bank)->num_rows();
		if($is_data_bank <= 0)
		{
			$data_bank = array(
				array(
						'id_proyek_rat' => $idpro,
						'icitem_bank' => 'Provisi Jaminan',
						'id_satuan' => $satid,
						'satuan' => 'Ls',
						'kode_material' => '504.0012'						
					),
				array(
						'id_proyek_rat' => $idpro,
						'icitem_bank' => 'Bunga Bank',
						'id_satuan' => $satid,
						'satuan' => 'Ls',
						'kode_material' => '504.0002'
					)				
			);
			$this->db->insert_batch('simpro_t_rat_idc_bank', $data_bank); 
		}		
		
		# asuransi
		$sql_asuransi = sprintf("SELECT * FROM simpro_t_rat_idc_asuransi WHERE id_proyek_rat = '%d'", $idpro);
		$is_data_asuransi = $this->db->query($sql_asuransi)->num_rows();
		if($is_data_asuransi <= 0)
		{		
			// $data_asuransi = array(
			// 	array(
			// 			'id_proyek_rat' => $idpro,
			// 			'icitem_asuransi' => 'C.A.R',
			// 			'id_satuan' => $satid,
			// 			'satuan' => 'Ls',
			// 			'kode_material' => '505.9014'
			// 		),
			// 	array(
			// 			'id_proyek_rat' => $idpro,
			// 			'icitem_bank' => 'ASTEK',
			// 			'id_satuan' => $satid,
			// 			'satuan' => 'Ls',
			// 			'kode_material' => '505.9013'
			// 		)				
			// );
			$asur1 = array(
						'id_proyek_rat' => $idpro,
						'icitem_asuransi' => 'C.A.R',
						'id_satuan' => $satid,
						'satuan' => 'Ls',
						'kode_material' => '505.9014'
					);
			$asur2 = array(
						'id_proyek_rat' => $idpro,
						'icitem_asuransi' => 'ASTEK',
						'id_satuan' => $satid,
						'satuan' => 'Ls',
						'kode_material' => '505.9013'
					);

			$this->db->insert('simpro_t_rat_idc_asuransi', $asur1); 	
			$this->db->insert('simpro_t_rat_idc_asuransi', $asur2); 		
		}		
		
		$this->load->view('entry_detail_rat', $frmdata);
	}
	
	public function get_data_idc($id)
	{	
		$data = $this->mdl_rencana->data_indirect_cost($id);
		$this->_out($data);
	}

	function entry_rat_tree($idtender)
	{
		$frmdata = array('idtender' => $idtender);
		$this->load->view('entry_rat_tree', $frmdata);	
	}
				
	public function get_satuan()
	{
		$rs = $this->db->query("SELECT satuan_id, satuan_nama FROM simpro_tbl_satuan")->result_array();
		foreach($rs as $k=>$v)
		{
			$satuan[] = array(
				'satuan_id'=>$v['satuan_id'],
				'satuan_kode'=>$v['satuan_nama']
			);
		}
		echo json_encode(array('success'=>true,'total'=>count($satuan),'data'=>$satuan));
	}
	
	function update_tree_item()
	{		
		if($this->input->post('kode_tree') && $this->input->post('tree_item') && $this->input->post('satuan_id') 
		&& $this->input->post('id_proyek_rat') && $this->input->post('rat_item_tree'))
		{
			$du = array(
				'rat_item_tree' => $this->input->post('rat_item_tree'),
				'tree_parent_id' => $this->input->post('tree_parent_id'),
				'id_proyek_rat' => $this->input->post('id_proyek_rat'),
				'kode_tree' => $this->input->post('kode_tree'),
				'tree_satuan' => $this->input->post('satuan_id'),
				'tree_item' => $this->input->post('tree_item'),
				'volume' => $this->input->post('volume')
			);
			$this->db->where('rat_item_tree', $this->input->post('rat_item_tree'));
			$this->db->where('id_proyek_rat', $this->input->post('id_proyek_rat'));
			$this->db->where('tree_parent_id', $this->input->post('tree_parent_id'));
			if($this->db->update('simpro_rat_item_tree', $du)) echo "Data berhasil diupdate";
				else echo "Data GAGAL diupdate!";
		}
	}
	
	function get_task_tree_item($idpro)
	{
		$param = $this->input->get('param');
		// var_dump($param);
		$arr = $this->tree($idpro, $depth=0,$param);
		echo json_encode(array('text'=>'.', 'children'=>$arr));
	}
	
	function set_parent_tree_id()
	{
		if($this->input->post('parent_id'))
		{
			$datasess = array(
				'parent_tree_id' => $this->input->post('parent_id'),
				'parent_kode_tree' => $this->input->post('parent_kode_tree')
			);
			$_SESSION['sess_parent_id'] = $this->input->post('parent_id');
			$_SESSION['sess_parent_kode_tree'] = $this->input->post('parent_kode_tree');
			$this->session->set_userdata($datasess);
		} else
		{
			$datasess = array(
				'parent_tree_id' => 0,
				'parent_kode_tree' => ''
			);
			$_SESSION['sess_parent_id'] = 0;
			$_SESSION['sess_parent_kode_tree'] = '';
			$this->session->set_userdata($datasess);
		}
	}
	
	function get_parent_tree_id()
	{
		//$parentid = isset($_SESSION['sess_parent_id']) ? $_SESSION['sess_parent_id'] : 0;
		$parentid = @$_SESSION['sess_parent_id'];
		return $this->session->userdata('parent_tree_id') <> '' ? $this->session->userdata('parent_tree_id') : $parentid;
	}

	function set_rat_item_tree()
	{
		if($this->input->post('id_tender') && $this->input->post('rat_item_tree') && $this->input->post('kode_tree'))
		{
			$this->session->set_userdata('sess_id_tender', $this->input->post('id_tender'));
			$this->session->set_userdata('sess_rat_item_tree', $this->input->post('rat_item_tree'));
			$this->session->set_userdata('sess_kode_tree', $this->input->post('kode_tree'));
			$_SESSION['sess_id_tender'] = $this->input->post('id_tender');
			$_SESSION['sess_rat_item_tree'] = $this->input->post('rat_item_tree');
			$_SESSION['sess_kode_tree'] = $this->input->post('kode_tree');
		}
	}	

	function get_rat_item_tree()
	{
		if(isset($_SESSION['sess_id_tender']) && isset($_SESSION['sess_rat_item_tree']))
		{
			$data = array(
				'id_tender' => $_SESSION['sess_id_tender'],
				'rat_item_tree' => $_SESSION['sess_rat_item_tree'],
				'kode_tree' => $_SESSION['sess_kode_tree']
			);
			return $data;
		}
	}	

	function tambah_apek()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_tender'))
		{
			$is_data = 0;
			$var_item = $this->get_rat_item_tree();			
			# cek data
			$q_cek = sprintf("SELECT * FROM simpro_rat_analisa_item_apek WHERE rat_item_tree='%d' AND id_proyek_rat='%d'",
							$var_item['rat_item_tree'], $this->input->post('id_tender')
							);						
			$is_data = $this->db->query($q_cek)->num_rows();

			$data = array(
				'id_proyek_rat' => $this->input->post('id_tender'),
				'id_data_analisa' => $this->input->post('id_data_analisa'),
				'kode_analisa' => $this->input->post('kode_analisa'),
				'harga' => $this->input->post('harga_satuan'),
				'rat_item_tree' => $var_item['rat_item_tree'],
				'kode_tree' => $var_item['kode_tree']
			);			
			
			/*
			$harga = explode(',',$this->input->post('harga_satuan'));
			$km = explode(',',$this->input->post('kode_analisa'));
			$dmid = explode(',',$this->input->post('id_data_analisa'));
			$i=0;
			foreach($km as $k=>$v)
			{
				$data[] = array(
					'id_proyek_rat' => $this->input->post('id_tender'),
					'id_data_analisa' => $dmid[$i],
					'kode_analisa' => strtoupper($v),
					'harga' => ($harga[$i] <> 0) ? $harga[$i] : 0,
					'rat_item_tree' => $var_item['rat_item_tree'],
					'kode_tree' => $var_item['kode_tree']
				);			
				$i++;
			}
			*/
			
			if($is_data > 0)
			{			
				$this->db->where('id_proyek_rat', $this->input->post('id_tender'));
				$this->db->where('rat_item_tree', $var_item['rat_item_tree']);
				if($this->db->update('simpro_rat_analisa_item_apek', $data)) echo "Data telah diupdate.";
					else echo "Data GAGAL diupdate!";
					
			} else 
			{
				if($this->db->insert('simpro_rat_analisa_item_apek',$data)) echo "Data telah disimpan.";
					else echo "Data GAGAL disimpan!";
			}			
		}
	}
	
	function get_parent_kode_tree()
	{
		//$parent = isset($_SESSION['sess_parent_kode_tree']) ? $_SESSION['sess_parent_kode_tree'] : 0;
		$parent = @$_SESSION['sess_parent_kode_tree'];
		return $this->session->userdata('parent_kode_tree') <> '' ? $this->session->userdata('parent_kode_tree') : $parent;
	}
	
	function tree($idpro, $depth=0,$param)
	{
		$result=array();
		$temp=array();
		$temp = $this->mdl_rencana->get_tree_item($idpro, $depth, $param)->result();
		if(count($temp))
		{			
			$i = 0;
			$n = 1;
			// mb_detect_encoding($s, "UTF-8") == "UTF-8" ? : $s = utf8_encode($s);
			foreach($temp as $row){
				$kode_tree = $row->kode_tree;

				if ($row->tree_parent_id) {
					$tree_kode_parent = $this->db->query("select kode_tree from simpro_rat_item_tree where rat_item_tree = $row->tree_parent_id")->row()->kode_tree;
				} else {
					$tree_kode_parent = "";
				}

				if ($row->tree_parent_kode.'.'.$n <> $row->kode_tree && strlen($row->tree_parent_kode) <> 0 || $tree_kode_parent <> $row->tree_parent_kode) {
					$kode_tree = $tree_kode_parent.'.'.$n;

					// $data_del = "delete from simpro_rap_item_tree where kode_tree = '$row->kode_tree' and id_proyek = $idpro";
					// $this->db->query($data_del);

					$data_up_ap = "update simpro_rat_analisa_item_apek set kode_tree = '$kode_tree' 
					where kode_tree = '$row->kode_tree' and id_proyek_rat = $idpro";
					$this->db->query($data_up_ap);

					$data_up = "update simpro_rat_item_tree set kode_tree = '$kode_tree',
					tree_parent_kode = '$tree_kode_parent' 
					where rat_item_tree = $row->rat_item_tree";
					$this->db->query($data_up);
				}

				$temp_harga = $this->mdl_rencana->get_tree_item_harga($idpro, $row->rat_item_tree)->row_array();
				$data[] = array(
					'rat_item_tree' => $row->rat_item_tree,
					'id_proyek_rat' => $row->id_proyek_rat,
					'id_satuan' => $row->id_satuan,
					'kode_tree' => $kode_tree,
					'kode_analisa' => $row->kode_analisa,
					'tree_item' => utf8_encode($row->tree_item),
					'tree_satuan' => $row->tree_satuan,
					'volume' => $row->volume,
					'harga' => $row->hrg,
					'subtotal' => $row->sub,
					'tree_parent_id' => $row->tree_parent_id
					/* 
					'harga' => $temp_harga['harga'], 
					'subtotal' => $temp_harga['subtotal'],
					*/
				);
				
				if($depth == 0) $data[$i] = array_merge($data[$i], array('expanded' => true));
				
				## check if have a child
				$q = sprintf("SELECT * FROM simpro_rat_item_tree WHERE id_proyek_rat = '%d' AND tree_parent_id = '%d'", $row->id_proyek_rat, $row->rat_item_tree);
				$query = $this->db->query($q);
				$is_child = $query->num_rows();
				if($is_child)
				{				
					$result[] = array_merge(
						$data[$i],
						array(
							'iconCls' => 'task-folder',
							'ishaschild' => 1,
							'children'=>$this->tree($idpro, $row->rat_item_tree,$param)
						)
					);
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
		
	function gen_kode_tree($proyekid, $parentid, $kode_tree)
	{
		$sql_tree = sprintf("
					SELECT 
						kode_tree 
					FROM simpro_rat_item_tree 
					WHERE 
						id_proyek_rat = '%d' 
						AND tree_parent_id = '%d'
					ORDER BY rat_item_tree DESC", 
					$proyekid, 
					$parentid
					);
		$qry = $this->db->query($sql_tree);
		$kt = $qry->row_array();
		$tot_data = $qry->num_rows();
		if($tot_data)
		{
			$old_kode = $kode_tree;
			$kt_inc = $tot_data + 1; 
		} else 
		{
			if(isset($kt['kode_tree']))
			{
				$old_kode = $kt['kode_tree'];
				$kt_inc = $old_kode + 1;
			} else 
			{
				$old_kode = $kode_tree;
				$kt_inc = 1;
			}
		}
		if($parentid) $new_kt = $old_kode.'.'.$kt_inc;
			else $new_kt = $kt_inc;
		return $new_kt;
	}
	
	function del_tree_item()
	{
		if($this->input->post('tree_item_id'))
		{
			$query = sprintf("				
				DELETE FROM simpro_rat_analisa_item_apek WHERE kode_tree = (select kode_tree from simpro_rat_item_tree where rat_item_tree = '%d');				
			", $this->input->post('tree_item_id'));
			$qry_2 = $this->db->query($query);
			$qry = $this->db->query(sprintf("DELETE FROM simpro_rat_item_tree WHERE rat_item_tree = '%d'", $this->input->post('tree_item_id')));
			
			if($qry && $qry_2) echo json_encode(array("success"=>true, "message"=>"Data berhasil dihapus!"));
		}
	}
	
	function get_parent_kode($idp, $treeid)
	{
		$sql = sprintf("
			SELECT rat_item_tree, kode_tree
			FROM simpro_rat_item_tree
			WHERE rat_item_tree = '%d'
			AND id_proyek_rat = '%d'		
		", $treeid, $idp);
		$kode = $this->db->query($sql)->row_array();
		return $kode['kode_tree'];
	}

	public function tambah_rat_tree_item($idtender)
	{		
		$pid = $this->get_parent_tree_id();
		$pkd = $this->get_parent_kode_tree();		
		/* $this->input->post('kode_tree') */
		$parentid = isset($pid) ? $pid : $this->input->post('tree_parent_id');
		$new_kt = $this->gen_kode_tree($this->input->post('id_proyek_rat'), $parentid, $pkd);
		if($this->input->post('tree_item') && $this->input->post('tree_satuan')	&& $this->input->post('id_proyek_rat'))
		{				
			$datainsert = array(
				'tree_parent_id' => $parentid,
				'tree_item' => $this->input->post('tree_item'),
				'tree_parent_kode' => $pkd,
				'tree_satuan' => $this->input->post('tree_satuan'),
				'id_proyek_rat' =>  $this->input->post('id_proyek_rat'),
				'volume' => $this->input->post('volume') <> '' ? $this->input->post('volume') : 0,
				'kode_tree' =>  $new_kt
			);
			$resin = $this->db->insert('simpro_rat_item_tree', $datainsert);
			if($resin)
			{
				echo json_encode(array('success'=>true, 'message'=>'Data RAT berhasil disimpan!'));
			} else json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));
		} else 
		{
			echo json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));		
		}
	}
	
	function tambah_ansat()
	{
		if(($this->input->post('detail_material_kode') || $this->input->post('detail_material_id')))
		{
			$var = $this->get_rat_item_tree();
			$data_material_id = explode(',',$this->input->post('detail_material_id'));
			$data_material_kode = explode(',',$this->input->post('detail_material_kode'));
			$data_koefisien = explode(',',$this->input->post('koefisien'));
			$idproyek = $var['id_tender']; //$this->input->post('id_proyek_rat');
			$iditem =  $var['rat_item_tree']; //$this->input->post('rat_item_tree');
			$i = 0;
			foreach($data_material_id as $k=>$v)
			{				
				$data[] = array(
					'id_proyek_rat' => $idproyek,
					'rat_item_tree' => $iditem,
					'akoefisien' => $data_koefisien[$i],
					'detail_material_kode' => $data_material_kode[$i]
				);
				$i++;
			}
			if($this->db->insert_batch('simpro_rat_analisa', $data))
			{
				echo "Data berhasil ditambah.";
			} else echo "Data gagal ditambahkan";
		}
	}
		
	function tambah_uraian_asuransi($idtender)
	{			
		if($this->input->post('icitem_asuransi')  && $this->input->post('persentase'))
		{
			$datainsert = array(
				'id_proyek_rat' => $this->input->post('idtender'),
				'icitem_asuransi' => $this->input->post('icitem_asuransi'),
				'id_satuan' => $this->input->post('id_satuan'),
				'persentase' => is_numeric($this->input->post('persentase')) ? $this->input->post('persentase') : 0
			);
			if($this->db->insert('simpro_t_rat_idc_asuransi', $datainsert))
			{
				echo json_encode(array('success'=>true, 'message'=>'Data Asuransi berhasil disimpan!'));
			} else echo json_encode(array('success'=>true, 'message'=>'Data Asuransi GAGAL disimpan!'));
		} else echo json_encode(array('success'=>true, 'message'=>'Data Asuransi GAGAL disimpan!'));
	}
	
	function get_data_rat_asuransi($idtender)
	{
		if(isset($idtender))
		{
			$data = $this->mdl_rencana->get_data_rat_asuransi($idtender);
			$this->_out($data);		
		}	
	}

	function del_item_idc_asuransi()
	{
		if($this->input->post('id_rat_idc_asuransi'))
		{
			if($this->db->delete('simpro_t_rat_idc_asuransi', array('id_rat_idc_asuransi' => $this->input->post('id_rat_idc_asuransi'))))
			{	
				echo "Data berhasil dihapus.";
			} else echo "Data gagal dihapus!";
		}
	}
	
	function update_idc_asuransi()
	{
		$idsat = $this->input->post('id_satuan');
		if(!is_numeric($idsat))
		{
			$get_satuan = $this->db->query(sprintf("SELECT satuan_id FROM simpro_tbl_satuan WHERE satuan_nama = '%s'",$idsat))->row_array();
			$satuan_id = $get_satuan['satuan_id'];
		} else $satuan_id = $this->input->post('id_satuan');
		
		if($this->input->post('id_rat_idc_asuransi') && $this->input->post('icitem_asuransi') 
		&& !empty($satuan_id) && $this->input->post('persentase') && $this->input->post('id_proyek_rat'))
		{
			$data = array(
				'id_proyek_rat' => $this->input->post('id_proyek_rat'),
				'id_rat_idc_asuransi' =>$this->input->post('id_rat_idc_asuransi'),
				'icitem_asuransi' => $this->input->post('icitem_asuransi'),
				'id_satuan' => $satuan_id,
				'persentase' => $this->input->post('persentase')
			);
			
			$this->db->where('id_proyek_rat', $this->input->post('id_proyek_rat'));
			$this->db->where('id_rat_idc_asuransi', $this->input->post('id_rat_idc_asuransi'));
			$this->db->update('simpro_t_rat_idc_asuransi', $data);
			echo 'Data Asuransi BERHASIL diupdate!';
		} else echo 'Data Asuransi GAGAL diupdate!';	
	}
		
	function get_data_rat_biayaumum($id)
	{
		if(isset($id))
		{
			$data = $this->mdl_rencana->get_data_rat_biayaumum($id);
			$this->_out($data);		
		}		
	}
	
	function del_item_biaya_umum()
	{
		if($this->input->post('id_rat_biaya_umum'))
		{
			if($this->db->delete('simpro_t_rat_idc_biaya_umum', array('id_rat_biaya_umum' => $this->input->post('id_rat_biaya_umum'))))
			{	
				echo "Data berhasil dihapus.";
			} else echo "Data gagal dihapus!";
		}	
	}

	function del_biaya_umum()
	{
		if($this->input->post('id_rat_biaya_umum'))
		{
			//$idbiayaumum = explode(",", $this->input->post('id_rat_biaya_umum'));
			if($this->db->query(sprintf("DELETE FROM simpro_t_rat_idc_biaya_umum WHERE id_rat_biaya_umum IN (%s)", $this->input->post('id_rat_biaya_umum'))))
			{
				echo "Data berhasil dihapus.";
			} else echo "Data gagal dihapus!";
			/*
			if($this->db->delete('simpro_t_rat_idc_biaya_umum', array('id_rat_biaya_umum' => $idbiayaumum)))
			{	
				echo "Data berhasil dihapus.";
			} else echo "Data gagal dihapus!";
			*/
		}	
	}
	
	function tambah_biaya_umum()
	{
		if($this->input->post('icharga') && $this->input->post('icitem') && $this->input->post('icvolume') && $this->input->post('idtender'))
		{
			$data = array(
				'id_proyek_rat' => $this->input->post('idtender'),
				'icitem' => $this->input->post('icitem'),
				'icvolume' => $this->input->post('icvolume'),
				'icharga' => $this->input->post('icharga'),
				'satuan_id' => $this->input->post('satuan_id')
			);
			if($this->db->insert('simpro_t_rat_idc_biaya_umum', $data)) 
				echo json_encode(array('success'=>true, 'message'=>'Data berhasil ditambah ke tabel Biaya Umum!'));
			else echo json_encode(array('success'=>true, 'message'=>'Data GAGAL ditambah!'));
		} else echo json_encode(array('success'=>true, 'message'=>'Silahkan lengkapi form yang sudah disediakan!'));
	}

	function get_satuan_id($sat)
	{
		$get_satuan = $this->db->query(sprintf("SELECT satuan_id FROM simpro_tbl_satuan WHERE satuan_nama = '%s'",$sat))->row_array();
		$satuan_id = $get_satuan['satuan_id'];
		if(isset($satuan_id)) return $satuan_id;
			else return false;
	}
	

	function tambah_biaya_umum_updated()
	{
		if($this->input->post('kode_material') && $this->input->post('id_tender'))
		{
			$id_tender = $this->input->post('id_tender');
			$sudah = '';
			$kode = explode(",", $this->input->post('kode_material'));
			$nama = explode(",", $this->input->post('nama_material'));
			$sat = explode(",", $this->input->post('satuan'));
			$i=0;
			foreach($kode as $k=>$v)
			{
				$km = $kode[$i];

				$satid = $this->get_satuan_id($sat[$i]);
				$cek_kode = $this->db->query("select * from simpro_t_rat_idc_biaya_umum where id_proyek_rat = $id_tender and kode_material = '$km'");

				if ($cek_kode->num_rows() == 0) {
				
					$data = array(
						'id_proyek_rat' => $this->input->post('id_tender'),
						'icitem' => $nama[$i], 
						'kode_material' => $kode[$i],
						'satuan' => $sat[$i],
						'icvolume' => 1,
						'icharga' => 0,
						'satuan_id' => $satid
					);

					$this->db->insert('simpro_t_rat_idc_biaya_umum',$data);
				} else {
					$sudah = $sudah.$nama[$i]." telah ditambahkan sebelumnya..<br>";
				}
				$i++;
			}
			if ($sudah) {
				echo $sudah.'<br>';
			}
				echo "Data berhasil ditambah ke tabel Biaya Umum!";
		} else echo "Silahkan pilih item dari bank data!";
	}

	function tambah_biaya_asuransi_updated()
	{
		if($this->input->post('kode_material') && $this->input->post('id_tender'))
		{
			$id_tender = $this->input->post('id_tender');
			$sudah = '';
			$kode = explode(",", $this->input->post('kode_material'));
			$nama = explode(",", $this->input->post('nama_material'));
			$sat = explode(",", $this->input->post('satuan'));
			$i=0;
			foreach($kode as $k=>$v)
			{
				$km = $kode[$i];

				$satid = $this->get_satuan_id($sat[$i]);
				$cek_kode = $this->db->query("select * from simpro_t_rat_idc_asuransi where id_proyek_rat = $id_tender and kode_material = '$km'");

				if ($cek_kode->num_rows() == 0) {

					$data = array(				
						'id_proyek_rat' => $this->input->post('id_tender'),
						'icitem_asuransi' => $nama[$i], 
						'kode_material' => $kode[$i],
						'id_satuan' => $satid,
						'satuan' => $sat[$i],
						'harga' => 0,
						'persentase' => 0
					);

					$this->db->insert('simpro_t_rat_idc_asuransi',$data);
				} else {
					$sudah = $sudah.$nama[$i]." telah ditambahkan sebelumnya..<br>";
				}
				$i++;
			}
			if ($sudah) {
				echo $sudah.'<br>';
			}
				echo "Data berhasil ditambah ke tabel Biaya Asuransi!";
		} else echo "Silahkan pilih item dari bank data!";
	}

	function tambah_biaya_bank_updated()
	{
		if($this->input->post('kode_material') && $this->input->post('id_tender'))
		{
			$id_tender = $this->input->post('id_tender');
			$sudah = '';
			$kode = explode(",", $this->input->post('kode_material'));
			$nama = explode(",", $this->input->post('nama_material'));
			$sat = explode(",", $this->input->post('satuan'));
			$i=0;
			foreach($kode as $k=>$v)
			{
				$km = $kode[$i];

				$satid = $this->get_satuan_id($sat[$i]);
				$cek_kode = $this->db->query("select * from simpro_t_rat_idc_bank where id_proyek_rat = $id_tender and kode_material = '$km'");

				if ($cek_kode->num_rows() == 0) {
					$data = array(				
						'id_proyek_rat' => $this->input->post('id_tender'),
						'icitem_bank' => $nama[$i], 
						'kode_material' => $kode[$i],
						'satuan' => $sat[$i],
						'persentase' => 0,
						'harga' => 0,
						'id_satuan' => $satid
					);

					$this->db->insert('simpro_t_rat_idc_bank',$data);
				} else {
					$sudah = $sudah.$nama[$i]." telah ditambahkan sebelumnya..<br>";
				}
				$i++;
			}
			if ($sudah) {
				echo $sudah.'<br>';
			}
			echo "Data berhasil ditambah ke tabel Biaya Umum!";
		} else echo "Silahkan pilih item dari bank data!";
	}
	
	function update_idc_biaya_umum()
	{
		$idsat = $this->input->post('id_satuan');
		if(!is_numeric($idsat))
		{
			$get_satuan = $this->db->query(sprintf("SELECT satuan_id FROM simpro_tbl_satuan WHERE satuan_nama = '%s'",$idsat))->row_array();
			$satuan_id = $get_satuan['satuan_id'];
		} else $satuan_id = $this->input->post('id_satuan');
		if($this->input->post('icitem') && $this->input->post('icvolume') && !empty($satuan_id) && $this->input->post('icharga'))
		{
			$du = array(
				'icitem' => $this->input->post('icitem'),
				'icvolume' => $this->input->post('icvolume'),
				'icharga' => $this->input->post('icharga'),
				'satuan_id' => $satuan_id
			);		
			$this->db->where('id_rat_biaya_umum',$this->input->post('id_rat_biaya_umum'));
			$this->db->where('id_proyek_rat',$this->input->post('id_proyek_rat'));
			if($this->db->update('simpro_t_rat_idc_biaya_umum', $du)) echo "Data berhasil diupdate.";
				else echo "Data GAGAL diupdate!";			
		} else echo "Data GAGAL diupdate, ada kesalahan pengisian form!";
	}
	
	function get_data_ansat()
	{
		if($this->input->get('subbidang_kode'))
		{
			$data = $this->mdl_rencana->get_ansat($this->input->get('subbidang_kode'));
		} else $data = $this->mdl_rencana->get_ansat('');
		$this->_out($data);	
	}

	function get_data_ansat_tree()
	{
		if($this->input->post('kategori'))
		{
			$data = $this->mdl_rencana->get_ansat($this->input->post('kategori'));
		} else $data = $this->mdl_rencana->get_ansat('500');
	}
	
	function tambah_uraian_bank($idtender)
	{
		if($this->input->post('icitem_bank')  && $this->input->post('persentase'))
		{
			$datainsert = array(
				'id_proyek_rat' => $this->input->post('idtender'),
				'icitem_bank' => $this->input->post('icitem_bank'),
				'id_satuan' => $this->input->post('id_satuan'),
				'persentase' => is_numeric($this->input->post('persentase')) ? $this->input->post('persentase') : 0
			);
			if($this->db->insert('simpro_t_rat_idc_bank', $datainsert))
			{
				echo json_encode(array('success'=>true, 'message'=>'Data Bank berhasil disimpan!'));
			} else echo json_encode(array('success'=>false, 'message'=>'Data Bank GAGAL disimpan!'));
		} else echo json_encode(array('success'=>false, 'message'=>'Data Bank GAGAL disimpan!'));
	}
	
	function del_item_idc_bank()
	{
		if($this->input->post('id_rat_idc_bank'))
		{
			if($this->db->delete('simpro_t_rat_idc_bank', array('id_rat_idc_bank' => $this->input->post('id_rat_idc_bank'))))
			{	
				echo "Data berhasil dihapus.";
			} else echo "Data gagal dihapus!";
		}
	}
	
	function get_data_bank($idtender)
	{
		if(isset($idtender))
		{
			$data = $this->mdl_rencana->get_data_bank($idtender);
			$this->_out($data);		
		}
	}
	
	function update_data_bank()
	{
		$idsat = $this->input->post('id_satuan');
		if(!is_numeric($idsat))
		{
			$get_satuan = $this->db->query(sprintf("SELECT satuan_id FROM simpro_tbl_satuan WHERE satuan_nama = '%s'",$idsat))->row_array();
			$satuan_id = $get_satuan['satuan_id'];
		} else $satuan_id = $this->input->post('id_satuan');
		if($this->input->post('icitem_bank') && $this->input->post('persentase') && !empty($satuan_id) && $this->input->post('id_rat_idc_bank'))
		{
			$du = array(
				'icitem_bank' => $this->input->post('icitem_bank'),
				'id_satuan' => $satuan_id,
				'persentase' => is_numeric($this->input->post('persentase')) ? $this->input->post('persentase') : 0
			);		
			$this->db->where('id_rat_idc_bank',$this->input->post('id_rat_idc_bank'));
			$this->db->where('id_proyek_rat',$this->input->post('id_proyek_rat'));
			if($this->db->update('simpro_t_rat_idc_bank', $du)) echo "Data berhasil diupdate.";
				else echo "Data GAGAL diupdate!";			
		} else echo "Data GAGAL diupdate, ada kesalahan pengisian form!";
	}
	
	function update_rat_koefisien()
	{
		if($this->input->post('id_simpro_rat_analisa') && $this->input->post('akoefisien'))
		{
			$this->db->where('id_simpro_rat_analisa', $this->input->post('id_simpro_rat_analisa'));
			if($this->db->update('simpro_rat_analisa', $this->input->post()))
			{
				echo "Data berhasil diupdate.";
			} else echo "Data koefisien GAGAL diupdate!";
		}
	}
	
	function get_data_rab_dashboard()
	{
		$datatender = $this->mdl_rencana->data_rat_rab($this->session->userdata('divisi_id'), $this->session->userdata('id_tender'));
		$this->_out($datatender);	
	}
	
    function callbackFunction($value, $key) {
        echo "$key: $value<br />\n";
    }
	
    function printArray($foo) {
        array_walk_recursive($foo, array($this, 'callbackFunction'));
    }
	
	
	public function ubah_status_tender()
	{	
		if($this->input->post('id_proyek_rat') && $this->input->post('keterangan') && $this->input->post('status_tender'))
		{
			$data_update = array(
				'keterangan' => $this->input->post('keterangan'),
				'tgl_update_status' => date("Y-m-d"),
				'id_status_rat'	=> $this->input->post('status_tender')
			);
			$this->db->where('id_proyek_rat', $this->input->post('id_proyek_rat'));
			if($this->db->update('simpro_m_rat_proyek_tender', $data_update)) 
			{
				echo json_encode(array("succes"=>true, "message"=>"status RAT berhasil diupdate"));
			} else echo json_encode(array("succes"=>false, "message"=>"status RAT GAGAL diupdate"));
			
		} else echo json_encode(array("succes"=>false, "message"=>"Silahkan pilih  status tender dan isi keterangannya!"));
	}
	
	function reset_data_rat()
	{
		if($this->input->post('id_proyek_rat'))
		{
			/* cascading delete */
			// hapus analisa satuan id tender yg sama
			// hapus menu item dengan id tender yg sama
			echo "Reset all data succeed.";
		} 
	}
	
	function del_analisa_satuan_item()
	{
		if($this->input->post('tree_item_id'))
		{
			$ratid = $this->input->post('tree_item_id');
			// $query = sprintf("
			// 	DELETE FROM simpro_rat_analisa_asat WHERE kode_analisa = 
			// 	(
			// 		SELECT kode_analisa FROM simpro_rat_analisa_item_apek
			// 		WHERE rat_item_tree = '%d'
			// 	);

			// 	DELETE FROM simpro_rat_analisa_apek where kode_analisa = 
			// 	(
			// 		SELECT kode_analisa FROM simpro_rat_analisa_item_apek
			// 		WHERE rat_item_tree = '%d'
			// 	);			
				
			// 	DELETE FROM simpro_rat_analisa_item_apek WHERE rat_item_tree = '%d';				
			// ", $ratid, $ratid, $ratid);
			$query = sprintf("				
				DELETE FROM simpro_rat_analisa_item_apek WHERE kode_tree = (select kode_tree from simpro_rat_item_tree where rat_item_tree = '%d');				
			", $ratid);
			if($this->db->query($query)) echo "Data Analisa satuan berhasil dihapus.";
				else echo "Data Analisa satuan GAGAL dihapus!";
		} 
	}
	
	function copy_tree()
	{	
		if($this->input->post('kode_tree') && $this->input->post('id_tender'))
		{
			if(isset($_SESSION['copy_tree']['data'])) unset($_SESSION['copy_tree']);
			$kode_tree = explode(",",$this->input->post('kode_tree'));
			$uraian = explode(",",$this->input->post('tree_item'));
			$volume = explode(",",$this->input->post('volume'));
			$satuan = explode(",",$this->input->post('satuan'));
			$idtender = $this->input->post('id_tender');
			if(is_array($kode_tree))
			{
				$i=0;
				foreach($kode_tree as $k=>$v)
				{
					$copy_tree[] = array(
										'id_proyek_rat' => $idtender,
										'tree_item' => $uraian[$i],
										'tree_satuan' => $satuan[$i],
										'volume' => $volume[$i]
									);
					$i++;
				}
				$_SESSION['copy_tree'] = array(
						'id_tender' => $this->input->post('id_tender'), 
						'data' => $copy_tree
						);
			}
		}
	}	

	function paste_tree()	
	{		
		if($this->input->post('kode_tree') && $this->input->post('id_tender'))
		{
			if(isset($_SESSION['copy_tree']['data']))
			{
				$kt = explode(",",$this->input->post('kode_tree'));
				$parentid = explode(",",$this->input->post('tree_item_id'));
				$idtender = $this->input->post('id_tender');
				$copy = $_SESSION['copy_tree']['data'];
				$i=0;
				$error=FALSE;
				foreach($copy as $cp)
				{
					$new_kt = $this->gen_kode_tree($idtender, $parentid[0], $kt[0]);
					$pkd = $this->get_parent_kode($idtender, $parentid[0]);
					//echo $kt[0], ' ',$parentid[0], ' ', $new_kt;
					$data = array(
						'id_proyek_rat' => $cp['id_proyek_rat'],
						'tree_item' => $cp['tree_item'],
						'tree_satuan' => $cp['tree_satuan'],
						'volume' => $cp['volume'],
						'kode_tree' => $new_kt,
						'tree_parent_id' => $parentid[0],
						'tree_parent_kode' => $pkd
					);
					if(!$this->db->insert("simpro_rat_item_tree", $data)) $error=TRUE;
					$i++;
				}
				if(!$error) echo "Data berhasil di-paste";
					else echo "Data GAGAL dipaste!";
			} else echo "Tidak ada data yang di-copy, silahkan pilih item kemudian klik tombol 'Copy'";
		}	
	}

	function import_proyek_lain()
	{
		$id_tender = $this->session->userdata('id_tender');
		$idt = $this->input->post('id_tender');
		$this->db->delete("simpro_rat_item_tree",array('id_proyek_rat' => $id_tender));
		if($idt)
		{
			// if (count($id_array) > 0) {
			// 	for ($i=0; $i < count($id_array); $i++) { 
			// 		$id_rat = $id_array[$i];
					$sql_insert_rat_item_tree = "insert into simpro_rat_item_tree (
						kode_tree,
						id_proyek_rat,
						tree_item,
						tree_satuan,
						volume,
						tree_parent_kode
						)
						select
						kode_tree,
						".$id_tender.",
						tree_item,
						tree_satuan,
						volume,
						tree_parent_kode
						from
						simpro_rat_item_tree
						where id_proyek_rat = $idt
						order by kode_tree";

					$this->db->query($sql_insert_rat_item_tree);

					$this->update_id_rat_tree($id_tender);
					$this->set_satuan_induk($id_tender);
			// 	}
			// }
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

	function delete_tree_item()
	{
		if($this->input->post('tree_item_id') && $this->input->post('id_tender'))
		{
			$itemdel = explode(",", $this->input->post('tree_item_id'));
			$id_tender = $this->input->post('id_tender');
			$success = FALSE;
			if(count($itemdel) > 0)
			{
				foreach($itemdel as $k=>$v)
				{
					if($this->delete_tree_recursive($id_tender, $v)) $success=TRUE;
						else $success=FALSE;
				}
				echo "Data berhasil dihapus!";
			}
		}		
	}
	
	function copy_rat_proyek_lain()
	{		
		
		$idtender = ($this->input->get('id_proyek_rat')) <> '' ? $this->input->get('id_proyek_rat') : $_SESSION['idtender'];
		$query = sprintf("
			SELECT * FROM simpro_rat_item_tree
			WHERE id_proyek_rat = '%d'
			ORDER BY kode_tree, rat_item_tree ASC
		", $idtender);
		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
			$this->_out($data);
		}
	}
		
	function get_total_rata($idtender)
	{
		$data = $this->mdl_analisa->total_rata($idtender);
		if(isset($data)) echo number_format($data['data']['total_rata'],0);
			else echo '0';
	}
	
	function get_persen_rata($idtender)
	{
		$data = $this->hitung_summary_rat($idtender);
		if(isset($data['persen_bk'])) echo number_format($data['persen_bk'],2);
			else echo '0';
	}

	function total_rata($idtender)
	{
		$data = $this->mdl_analisa->total_rata($idtender);
		if(isset($data)) return number_format($data['data']['total_rata'],0);
			else return '0';
	}
	
	function persen_rata($idtender)
	{
		$data = $this->hitung_summary_rat($idtender);
		if(isset($data['persen_bk'])) return number_format($data['persen_bk'],2);
			else return '0';
	}
	
	function get_total_bk()
	{
		if($this->input->post('id_tender'))
		{
			$data = $this->mdl_rencana->hitung_biaya_konstruksi($this->input->post('id_tender'));
			if(isset($data)) echo number_format($data['total_bk'],0);
				else echo '0';
		}
	}
	
	function get_data_proyek()
	{
		$query = "SELECT id_proyek_rat, nama_proyek from simpro_m_rat_proyek_tender";
		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		}
		$this->_out($data);		
	}

	function get_data_proyek_copy()
	{
		$query = "SELECT distinct(b.id_proyek_rat), a.nama_proyek from simpro_m_rat_proyek_tender a join simpro_rat_item_tree b on a.id_proyek_rat = b.id_proyek_rat";
		// $query = "SELECT distinct(b.id_proyek_rat), a.nama_proyek from simpro_m_rat_proyek_tender a join simpro_rat_item_tree b on a.id_proyek_rat = b.id_proyek_rat join simpro_tbl_proyek c on a.proyek_id = c.proyek_id";

		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		}
		$this->_out($data);		
	}

	function data_proyek($id)
	{
		$query = sprintf("SELECT * from simpro_m_rat_proyek_tender WHERE id_proyek_rat = '%d'", $id);
		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			return $rs->row_array();
		}
	}
	
	private function delete_tree_recursive($id_tender, $id_tree)
	{				
			//rat_item_tree
			$query = sprintf("				
				DELETE FROM simpro_rat_analisa_item_apek WHERE kode_tree = (select kode_tree from simpro_rat_item_tree where rat_item_tree = '%d');				
			", $id_tree);
			$qry_2 = $this->db->query($query);
			$this->db->delete("simpro_rat_item_tree",array("rat_item_tree"=>$id_tree));
			// if($this->db->delete("simpro_rat_item_tree",array("rat_item_tree"=>$id_tree)) && $qry_2) return true;
			// 	else return false;

		$q = sprintf("SELECT * FROM simpro_rat_item_tree WHERE id_proyek_rat = '%d' AND tree_parent_id = '%d'", $id_tender, $id_tree);
		$query = $this->db->query($q);
		$is_data = $query->result_array();
		if(count($is_data) > 0)
		{				
			foreach($is_data as $k=>$v)
			{
				$this->delete_tree_recursive($id_tender, $v['rat_item_tree']);
			}
		}
	}
	
	private function _out($data)
	{
		$callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '';
		if ($data['total'] > 0)
		{
			$output = json_encode($data); //JSON_NUMERIC_CHECK
			if ($callback) {
				header('Content-Type: text/javascript; charset=utf-8');
				echo $callback . '(' . $output . ');';
			} else {
				header('Content-Type: application/x-json; charset=utf-8');
				echo $output;
			}		   
		} else 
		{
			if ($callback) {
				header('Content-Type: text/javascript; charset=utf-8');
				echo $callback . '(' . json_encode(array('data'=>array(), 'total'=>0)) . ');';
			} else {
				header('Content-Type: application/x-json; charset=utf-8');
				echo json_encode(array('data'=>array(), 'total'=>0));
			}		   
		}
	}

	public function view_summary_rat($idtender)
	{
		$data = array('idtender' => $idtender);	
		$this->load->view('summary_rat', $data);
	}
	
	function copy_analisa_tree()
	{		
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_tender'))
		{
			if(isset($_SESSION['copy_analisa']['kode_analisa'])) $this->unset_copy_analisa_tree();
			$_SESSION['copy_analisa']['kode_analisa'] = $this->input->post('kode_analisa');
			$_SESSION['copy_analisa']['harga'] = $this->input->post('harga');
			$_SESSION['copy_analisa']['id_data_analisa'] = $this->input->post('id_data_analisa');
			$_SESSION['copy_analisa']['id_tender'] = $this->input->post('id_tender');
			echo "Analisa berhasil di-copy.";
		} else echo "Analisa GAGAL di-copy!";
	}
		
	function paste_analisa_tree()
	{
		if($this->input->post('kode_tree') && $this->input->post('id_tender'))
		{
			if(isset($_SESSION['copy_analisa']['kode_analisa']))
			{
				$kt = explode(",",$this->input->post('kode_tree'));
				$tree_id = explode(",",$this->input->post('tree_item_id'));
				$idtender = $this->input->post('id_tender');
				$copy = $_SESSION['copy_analisa'];
				$i=0;
				$error=FALSE;
				foreach($kt as $k=>$v)
				{
					$q_data = sprintf("
						SELECT * 
						FROM simpro_rat_analisa_item_apek 
						WHERE id_proyek_rat = '%d' 
						AND rat_item_tree = '%d' 
						AND kode_tree = '%s'", 
						$idtender, $tree_id[$i], $v);
					$is_data = $this->db->query($q_data)->num_rows();
					$data = array(
						'id_proyek_rat' => $idtender,
						'kode_tree' => sprintf('%s',$v),
						'rat_item_tree' => $tree_id[$i], 
						'harga' => $_SESSION['copy_analisa']['harga'],
						'kode_analisa' => $_SESSION['copy_analisa']['kode_analisa'],
						'id_data_analisa' => $_SESSION['copy_analisa']['id_data_analisa']
					);
					if($is_data > 0)
					{
						# update
						$rw = $this->db->query($q_data)->row();
						$this->db->where('id_proyek_rat', $idtender);
						$this->db->where('kode_analisa', $rw->kode_analisa);
						$this->db->where('kode_tree', sprintf('%s',$rw->kode_tree));
						$this->db->update("simpro_rat_analisa_item_apek", $data);
					} else
					{
						# insert
						if(!$this->db->insert("simpro_rat_analisa_item_apek", $data)) $error=TRUE;
					}
					$i++;
				}
				if(!$error) echo "Data berhasil di-paste";
					else echo "Data GAGAL dipaste!";
			} else echo "Tidak ada data Analisa yang di-copy, silahkan pilih item kemudian klik tombol 'Copy Analisa'";
		}
	}
	
	function unset_copy_analisa_tree()
	{
		unset($_SESSION['copy_analisa']['kode_analisa']);
		unset($_SESSION['copy_analisa']['harga']);
		unset($_SESSION['copy_analisa']['id_data_analisa']);
		unset($_SESSION['copy_analisa']['id_tender']);
	}	
	
	private function _dump($d)
	{
		print('<pre>');
		print_r($d);
		print('</pre>');
	}

	function get_session()
	{
		$this->_dump($this->session->all_userdata());
		$this->_dump($_SESSION);
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
	
	function update_tree_parent_id($idtender)
	{
		$sql = sprintf("
			SELECT 
				rat_item_tree, kode_tree 
			FROM simpro_rat_item_tree
			WHERE id_proyek_rat = '%d'
			ORDER BY rat_item_tree ASC		
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
							rat_item_tree, kode_tree 
						FROM simpro_rat_item_tree
						WHERE id_proyek_rat = '%d'
						AND kode_tree = '%s'
						ORDER BY rat_item_tree ASC		
					", $idtender, $parent_kode);					
					$get_id = $this->db->query($sql)->row_array();
					$parent_id = isset($get_id['rat_item_tree']) ? $get_id['rat_item_tree'] : 0;
					# update db
					$update = sprintf("
							UPDATE simpro_rat_item_tree 
							SET tree_parent_kode = '%s', 
							tree_parent_id='%d' 
							WHERE id_proyek_rat='%d' 
							AND kode_tree='%s'", 
						$parent_kode, $parent_id, $idtender, $kd_tree);
					$this->db->query($update);
				}
			}
		}
	}
	
	function upload_rat_item()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv|txt';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);					
		$this->db->query("TRUNCATE TABLE simpro_tmp_import_rat");
		
		try {
			if(!$this->upload->do_upload('upload_analisa'))
			{
				throw new Exception(sprintf('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: csv|txt.\nErrors: %s', $this->upload->display_errors()));
			} else
			{
				$data = $this->upload->data();
				# CSV HEADER
				$sql = sprintf("COPY simpro_tmp_import_rat FROM '%s' DELIMITER ',' ENCODING 'LATIN1' CSV", realpath("./uploads/".$data['file_name']));
				if($this->db->query($sql))
				{
					$idpro = $this->input->post('id_proyek_rat');
					$qdel = sprintf("DELETE FROM simpro_rat_item_tree WHERE id_proyek_rat = '%d'",$this->input->post('id_proyek_rat'));
					$this->db->query($qdel);					
					$sql_insert = sprintf("
						INSERT INTO simpro_rat_item_tree(id_proyek_rat,kode_tree,tree_item,tree_satuan,volume)
						(
							SELECT
								%d as id_proyek,
								kode_tree,
								uraian,
								satuan,								
								volume::numeric
							FROM simpro_tmp_import_rat
						)
					", $idpro);
					if($this->db->query($sql_insert)) 
					{
						$this->update_tree_parent_id($idpro);
						$this->set_satuan_induk($idpro);
						$this->db->query(sprintf("DELETE FROM simpro_rat_analisa_item_apek WHERE id_proyek_rat = %d", $idpro));
						$this->db->query("TRUNCATE TABLE simpro_tmp_import_rat");
						echo json_encode(
							array(	"success"=>true, 
									"message"=>"Data berhasil diupload.", 
									"file" => $data['file_name'])
						);
					} else echo json_encode(array("success"=>true, 
							"message"=>"Data GAGAL diupload.", 
							"file" => $data['file_name']));
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
	
	function cetak_rata($id)
	{
		$dp = $this->mdl_rencana->get_data_tender($id);
		$rata = $this->mdl_analisa->rat_rata($id);
		$data = array(
			'idtender' => $id,
			'data_proyek' => $dp['data'],
			'rata' => $rata['data'],
			'total_rata' => $this->total_rata($id),
			'persen_rata' => $this->persen_rata($id)			
		);
		$this->load->view('print_rata', $data);
	}
	
	/**
	 * This function is to replace PHP's extremely buggy realpath().
	 * @param string The original path, can be relative etc.
	 * @return string The resolved path, it might not exist.
	 */
	function truepath($path){
		$unipath=strlen($path)==0 || $path{0}!='/';
		if(strpos($path,':')===false && $unipath)
			$path=getcwd().DIRECTORY_SEPARATOR.$path;
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ('.'  == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		$path=implode(DIRECTORY_SEPARATOR, $absolutes);
		if(file_exists($path) && linkinfo($path)>0)$path=readlink($path);
		$path=!$unipath ? '/'.$path : $path;
		return $path;
	}
	
	function export_project_csv($idtender)
	{	
		$this->load->library('zip');		
		$tables = array(
			'simpro_rat_item_tree' => 'id_proyek_rat', 
			'simpro_rat_analisa_daftar' => 'id_tender', 
			'simpro_rat_analisa_asat' => 'id_tender', 
			'simpro_rat_analisa_apek' => 'id_tender', 
			'simpro_t_rat_idc_asuransi' => 'id_proyek_rat', 
			'simpro_t_rat_idc_bank' => 'id_proyek_rat', 
			'simpro_t_rat_idc_biaya_umum' => 'id_proyek_rat', 
			'simpro_t_rat_varcost' => 'id_proyek_rat', 
			'simpro_rat_analisa_item_apek' => 'id_proyek_rat'
			);
			
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			$path = 'C://Windows//Temp//';		
		} else
		{
			$path = '/tmp/';		
		}
		foreach($tables as $k=>$v)
		{		
			$file = $k.'.csv';
			$dpath = realpath($path);
			$filename = $dpath. DIRECTORY_SEPARATOR . $file;
			$query = sprintf("COPY (SELECT * FROM %s WHERE %s = %d)  TO '%s' WITH CSV HEADER", $k, $v, $idtender, $filename);
			$this->db->query($query);
			$this->zip->read_file($filename);
		}		
		$this->zip->download(sprintf("backup-proyek-%s.zip",date('dmY')));
	}
	
	function hapus_tender()
	{
		if($this->input->post('tender_id'))
		{
			// cek dulu apakah proses sudah sampai ke rap?
			$id = $this->input->post('tender_id');
			$qcek = sprintf("SELECT proyek_id from simpro_m_rat_proyek_tender where id_proyek_rat = %d", $id);
			$dp = $this->db->query($qcek)->row_array();
			if(!empty($dp['proyek_id']))
			{
				echo "Data tender sudah diproses sampai RAP. Silahkan hubungi Administrator Untuk menghapus tender ini.";
			} else
			{
				$this->db->trans_begin();			
				$sql = "
					DELETE FROM simpro_t_rat_direct_cost WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_rat_analisa WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_rat_analisa_apek WHERE id_tender = ".$id.";
					DELETE FROM simpro_rat_analisa_asat WHERE id_tender = ".$id.";
					DELETE FROM simpro_rat_analisa_daftar WHERE id_tender = ".$id.";
					DELETE FROM simpro_rat_analisa_item_apek WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_rat_rab_item_tree WHERE rat_item_tree IN (SELECT rat_item_tree FROM simpro_rat_item_tree WHERE id_proyek_rat = ".$id.");
					DELETE FROM simpro_rat_item_tree WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_rat_dokumen_proyek WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_rat_rab_analisa WHERE id_tender = ".$id.";
					DELETE FROM simpro_rat_sketsa_proyek WHERE id_proyek_rat = ".$id."; 
					DELETE FROM simpro_t_rat_idc_asuransi WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_t_rat_idc_bank WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_t_rat_idc_biaya_umum WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_t_rat_varcost WHERE id_proyek_rat = ".$id.";
					DELETE FROM simpro_m_rat_proyek_tender WHERE id_proyek_rat=".$id.";
				";
				$this->db->query($sql);
				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();		
					echo "Data Tender GAGAL dihapus!";					
				} else
				{
					$this->db->trans_commit();
					$idsess = $this->session->userdata('id_tender');
					if( $idsess == $id) {
						unset($_SESSION['idtender']);
						unset($_SESSION['proyek_id']);					
						$items = array('id_tender' => '');
						$this->session->unset_userdata($items);
					}
					echo "Data Tender berhasil dihapus!";
				}
			}
		}
	}

	function hapus_tender_tes()
	{
		if($this->input->post('tender_id'))
		{
			$id = $this->input->post('tender_id');
			$this->db->trans_begin();			
			$sql = "
				DELETE FROM simpro_rat_analisa_apek WHERE id_tender = ".$id.";
				DELETE FROM simpro_rat_analisa_asat WHERE id_tender = ".$id.";
				DELETE FROM simpro_rat_analisa_daftar WHERE id_tender = ".$id.";
				DELETE FROM simpro_rat_analisa_item_apek WHERE id_proyek_rat = ".$id.";
				DELETE FROM simpro_rat_rab_item_tree WHERE rat_item_tree IN (SELECT rat_item_tree FROM simpro_rat_item_tree WHERE id_proyek_rat = ".$id.");
				DELETE FROM simpro_rat_item_tree WHERE id_proyek_rat = ".$id.";
				DELETE FROM simpro_rat_dokumen_proyek WHERE id_proyek_rat = ".$id.";
				DELETE FROM simpro_rat_rab_analisa WHERE id_tender = ".$id.";
				DELETE FROM simpro_rat_sketsa_proyek WHERE id_proyek_rat = ".$id."; 
				DELETE FROM simpro_t_rat_idc_asuransi WHERE id_proyek_rat = ".$id.";
				DELETE FROM simpro_t_rat_idc_bank WHERE id_proyek_rat = ".$id.";
				DELETE FROM simpro_t_rat_idc_biaya_umum WHERE id_proyek_rat = ".$id.";
				DELETE FROM simpro_t_rat_varcost WHERE id_proyek_rat = ".$id.";
				DELETE FROM simpro_m_rat_proyek_tender WHERE id_proyek_rat=".$id.";				
			";
			$this->db->query($sql);
			if($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				echo "Data Tender GAGAL dihapus!";
			} else
			{
				$this->db->trans_commit();
				$idsess = $this->session->userdata('id_tender');
				if($idsess == $id) {
					unset($_SESION['idtender']);
					unset($_SESION['proyek_id']);					
					$items = array('id_tender' => '');
					$this->session->unset_userdata($items);
				}				
				echo "Data Tender berhasil dihapus!";
			}
		}
	}
	
	function rat_to_excel()
	{
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('test worksheet');
		$this->excel->getActiveSheet()->setCellValue('A1', 'This is just some text value');
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		 
		$filename='just_some_random_name.xls'; 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="'.$filename.'"'); 
		header('Cache-Control: max-age=0'); 
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
		$objWriter->save('php://output');	
	}
	
	function rata_to_xls($idtender)
	{
		$this->load->library('excel');
		$dp = $this->mdl_rencana->get_data_tender($idtender);
		$ratas = $this->mdl_analisa->rat_rata($idtender);
		$rata = $ratas['data'];
		$total_rata = $this->total_rata($idtender);
		$persen_rata = $this->persen_rata($idtender);
		$data = array(
			'idtender' => $idtender,
			'data_proyek' => $dp['data']
		);
			
		$nama_proyek = $data['data_proyek']['nama_proyek'];
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('rata');
		$this->excel->getActiveSheet()->setCellValue('A1', $nama_proyek);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		 
		
		# informasi data umum proyek
		$data_proyek = array(
			'Divisi' => $data['data_proyek']['divisi_name'],
			'Proyek' => $data['data_proyek']['nama_proyek'], 
			'Pagu' => $data['data_proyek']['nilai_pagu_proyek'], 
			'Nilai Penawaran' => $data['data_proyek']['nilai_penawaran'], 
			'Nilai Kontrak (excl. PPN)' => $data['data_proyek']['nilai_kontrak_excl_ppn'], 
			'Nilai Kontrak (incl. PPN)' => $data['data_proyek']['nilai_kontrak_ppn'], 
			'Waktu Pelaksanaan' => $data['data_proyek']['waktu_pelaksanaan']
			);
		
		$a = 3;;
		foreach($data_proyek as $k=>$v)
		{
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $a), $k);
			$this->excel->getActiveSheet()->setCellValue(sprintf('B%d', $a), $v);
			$a++;
		}
		# end info proyek
		
		# data
		$this->excel->getActiveSheet()->setCellValue('A11','NO');
		$this->excel->getActiveSheet()->setCellValue('B11','KODE RAP');
		$this->excel->getActiveSheet()->setCellValue('C11','KODE MATERIAL');
		$this->excel->getActiveSheet()->setCellValue('D11','NAMA');
		$this->excel->getActiveSheet()->setCellValue('E11','SATUAN');
		$this->excel->getActiveSheet()->setCellValue('F11','VOLUME');
		$this->excel->getActiveSheet()->setCellValue('G11','HARGA SATUAN');
		$this->excel->getActiveSheet()->setCellValue('H11','SUBTOTAL');
		$lasta = 0;
		if(count($rata) > 0)
		{
			$i = 1;
			$subbidang = "";
			$subtotal = 0;
			$nextsub = "";
			$start_A = 12;
			for($a=0; $a < count($rata); $a++)
			{
				$sub = $rata[$a]['simpro_tbl_subbidang'];
				if($sub <> $subbidang)
				{				
					$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A),$rata[$a]['simpro_tbl_subbidang']);
					$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:H%d', $start_A, $start_A));
					$subbidang = $sub;
					$subtotal = $rata[$a]['subtotal'];
					$start_A++;					
				} else 
				{
					$subtotal = $subtotal + $rata[$a]['subtotal'];
				}
				
				$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A), $i);
				$this->excel->getActiveSheet()->setCellValue(sprintf('B%d', $start_A), $rata[$a]['kode_rap']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('C%d', $start_A), $rata[$a]['kd_material']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('D%d', $start_A), $rata[$a]['detail_material_nama']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('E%d', $start_A), $rata[$a]['detail_material_satuan']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('F%d', $start_A), number_format($rata[$a]['total_volume'],2));
				$this->excel->getActiveSheet()->setCellValue(sprintf('G%d', $start_A), number_format($rata[$a]['harga'],2));
				$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $start_A), number_format($rata[$a]['subtotal'],2));
				
				$nextsub = isset($rata[$a+1]['simpro_tbl_subbidang']) ? $rata[$a+1]['simpro_tbl_subbidang'] : '';
				if($nextsub <> $sub)
				{
					$start_A++;				
					$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:G%d', $start_A, $start_A));					
					$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A), 'SUBTOTAL');
					$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $start_A), number_format($subtotal,2));
					$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $start_A))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				}
				$i++;
				$start_A++;			
				$lasta = $start_A;
			}
			
			# footer		
			$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:G%d', $lasta, $lasta));		
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta))->getFont()->setBold(true);		
			$this->excel->getActiveSheet()->getStyle(sprintf('H%d', $lasta))->getFont()->setBold(true);		
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $lasta),'TOTAL RAT(A)');
			$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $lasta),$total_rata);
			$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:G%d', $lasta+1, $lasta+1));		
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $lasta+1),'PERSENTASE TERHADAP KONTRAK');
			$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $lasta+1),$persen_rata);
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta+1))->getFont()->setBold(true);		
			$this->excel->getActiveSheet()->getStyle(sprintf('H%d', $lasta+1))->getFont()->setBold(true);		
			
			# style
			$styleArray = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  )
			);
		  
			$this->excel->getActiveSheet()->getStyle(
				'A11:' . 
				$this->excel->getActiveSheet()->getHighestColumn() . 
				$this->excel->getActiveSheet()->getHighestRow()
			)->applyFromArray($styleArray);
			# end style		
		}
		# data			
		
		# output
		$filename = sprintf('rata-%s.xlsx', $nama_proyek); 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="'.$filename.'"'); 
		header('Cache-Control: max-age=0'); 
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
		$objWriter->save('php://output');						
		
	}	
	
	function excel($page="")
	{
		$idpro = $this->session->userdata('id_tender'); 

		if ($page=="") {
			echo "Access Forbidden..";
		} else {

			$nama_proyek = $this->db->query("select nama_proyek from simpro_m_rat_proyek_tender where id_proyek_rat = $idpro")
			->row()->nama_proyek;

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

			switch ($page) {
				case 'rat':					

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

					$this->excel->getActiveSheet()->setCellValue('A'.$x, 'Total');
					$this->excel->getActiveSheet()->setCellValue('G'.$x, $tot);

					$this->excel->getActiveSheet()->getStyle('A'.$x.':G'.$x)->getFont()->setBold(true);
	
					$this->excel->getActiveSheet()->getStyle('A4:G'.$x)->applyFromArray($styleArray);
					unset($styleArray);
	
					$this->excel->getActiveSheet()->mergeCells('A'.$x.':F'.$x);
				break;
			}

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

			$this->excel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleArray1);
			unset($styleArray1);

			$this->excel->getActiveSheet()->getStyle('C1:C'.$this->excel->getActiveSheet()->getHighestRow())
    		->getAlignment()->setWrapText(true);

			$this->excel->getActiveSheet()->mergeCells('A1:G1');
			$this->excel->getActiveSheet()->mergeCells('A2:G2');

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
		$arr = $this->tree($idpro, $depth=0,$param="");

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

	function canceling()
	{
		$this->db->trans_rollback();
	}

	function copy_analisa_proyek_lain()
	{
		$idtender = $this->session->userdata('id_tender');
		$id_tender_copy = $this->input->post('id_tender');
		$jml = $this->input->post('jml');
		$param = $this->input->post('param');
		$data_arr = $this->input->post('id_data');

		if ($param == 'copy_proyek_lain') {
			$data = explode(',', $data_arr);

			$x = 0;
			foreach ($data as $kv) {
				$cek_apek = "select * from simpro_rat_analisa_apek where parent_id_analisa = $kv and id_tender = $id_tender_copy";
				$q_cek_apek = $this->db->query($cek_apek);
				if ($q_cek_apek->result()) {
					if ($q_cek_apek->result()) {
						foreach ($q_cek_apek->result() as $rwe) {
							$da = $rwe->id_data_analisa;
							$q_cek_asat = $this->db->query("select * from simpro_rat_analisa_asat where id_data_analisa = $da and id_tender = $id_tender_copy");
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
					$sql_daftar_analisa = "select * from simpro_rat_analisa_daftar where id_data_analisa = $k";
					$q_daftar_analisa = $this->db->query($sql_daftar_analisa);
					if ($q_daftar_analisa->result()) {
						foreach ($q_daftar_analisa->result() as $row) {
							$last_analisa = "select right(kode_analisa,3)::int+1 as last_analisa from simpro_rat_analisa_daftar where id_tender = $idtender order by right(kode_analisa,3)::int desc limit 1";
							$get_an_mas = $this->db->query($last_analisa);
							if ($get_an_mas->result()) {
								$kda = sprintf("%03d",$get_an_mas->row()->last_analisa);
							} else {
								$kda = sprintf("%03d",1);
							}
							$data_daftar = array(
								'kode_analisa' => 'AN'.$kda,
								'id_kat_analisa' => $row->id_kat_analisa,
								'nama_item' => $row->nama_item,
								'id_satuan' => $row->id_satuan,
								'id_tender' => $idtender
							);
							$this->db->insert('simpro_rat_analisa_daftar',$data_daftar);
							$last_id_data_analisa = $this->db->insert_id();

							$data_id_analisa = $this->db->query("select * from simpro_rat_analisa_daftar where id_data_analisa = $last_id_data_analisa")->row();
							
							$arr_copy = array(
								'kode_analisa' => $data_id_analisa->kode_analisa,
								'id_data_analisa' => $data_id_analisa->id_data_analisa,
							);

							$sql_asat = "select * from simpro_rat_analisa_asat where kode_analisa = '$row->kode_analisa' and id_tender = $row->id_tender";
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
										'id_tender' => $idtender,
										'kode_rap' => $row_asat->kode_rap,
										'keterangan' => $row_asat->keterangan
									);
									$this->db->insert('simpro_rat_analisa_asat',$data_asat);
								}
								$arr_ses = array(
									'id_data_analisa' => $data_id_analisa->id_data_analisa,
									'kode_analisa' => $data_id_analisa->kode_analisa,
									'id_data_analisa_lama' => $row_asat->id_data_analisa,
									'kode_analisa_lama' => $row_asat->kode_analisa,
								);
								$arr_ses_all[] = $arr_ses;
							}

							$sql_apek = "select * from simpro_rat_analisa_apek where parent_kode_analisa = '$row->kode_analisa' and id_tender = $row->id_tender";
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
										'id_tender' => $idtender
									);
									$this->db->insert('simpro_rat_analisa_apek',$data_apek);
								}
							}

							$arr_copy_all[] = $arr_copy;
						}
					}
				}

				foreach ($arr_copy_all as $kc) {
					$d_dat_copy = $kc['kode_analisa'];
					$sql_apek = "select * from simpro_rat_analisa_apek where parent_kode_analisa = '$d_dat_copy' and id_tender = $idtender";
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
							$this->db->update('simpro_rat_analisa_apek',$arr_apek_up);
						}
					}	
				}

				echo "Data telah di-copy..";
			} else {
				echo "Tidak bisa meng-Copy Analisa apek,<br>Data yang anda pilih tidak mempunyai data item analisa apek..";
			}
		} else {
			$data = explode(',', $data_arr);

			if ($data != '') {
				for ($i=0; $i < count($data); $i++) { 
					$id_data_analisa = $data[$i];
					$sql_daftar_analisa = "select * from simpro_master_analisa_daftar where id_data_analisa = $id_data_analisa";
					$q_daftar_analisa = $this->db->query($sql_daftar_analisa);
					if ($q_daftar_analisa->result()) {
						foreach ($q_daftar_analisa->result() as $r_daftar_analisa) {
							$last_analisa = "select right(kode_analisa,3)::int+1 as last_analisa from simpro_rat_analisa_daftar where id_tender = $idtender order by right(kode_analisa,3)::int desc limit 1";
							$get_an_mas = $this->db->query($last_analisa);
							if ($get_an_mas->result()) {
								$kda = sprintf("%03d",$get_an_mas->row()->last_analisa);
							} else {
								$kda = sprintf("%03d",1);
							}
							$data_daftar = array(
								'kode_analisa' => 'AN'.$kda,
								'id_kat_analisa' => $r_daftar_analisa->id_kat_analisa,
								'nama_item' => $r_daftar_analisa->nama_item,
								'id_satuan' => $r_daftar_analisa->id_satuan,
								'id_tender' => $idtender
							);
							$this->db->insert('simpro_rat_analisa_daftar',$data_daftar);
							$last_id_data_analisa = $this->db->insert_id();

							$data_id_analisa = $this->db->query("select * from simpro_rat_analisa_daftar where id_data_analisa = $last_id_data_analisa")->row();
							

							$sql_asat = "select * from simpro_master_analisa_asat where kode_analisa = '$r_daftar_analisa->kode_analisa'";
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
										'id_tender' => $idtender,
										'kode_rap' => $row_asat->kode_rap,
										'keterangan' => $row_asat->keterangan
									);
									$this->db->insert('simpro_rat_analisa_asat',$data_asat);
								}
							}
						}
					}
				}
				echo "Data telah di-copy..";
			}
		}
		
	}

	function export_tender_old()
	{
		$id_tender = $this->session->userdata('id_tender');

		$nama_tender = $this->db->query("select nama_proyek from simpro_m_rat_proyek_tender where id_proyek_rat = $id_tender")->row()->nama_proyek;

		$filename = 'import_tender_'.str_replace(' ', '_', $nama_tender).'_'.date('Y-m-d').'.csv';
		 // force download  
	    header("Content-Type: application/force-download");
	    header("Content-Type: application/octet-stream");
	    header("Content-Type: application/download");

	    // disposition / encoding on response body
	    header("Content-Disposition: attachment;filename={$filename}");
	    header("Content-Transfer-Encoding: binary");

		$sql_get_data_tender = "
			select 
			a.id_status_rat as proyek_id_status_rat,
			a.nama_proyek as proyek_nama_proyek,
			a.lingkup_pekerjaan as proyek_lingkup_pekerjaan,
			a.waktu_pelaksanaan as proyek_waktu_pelaksanaan,
			a.waktu_pemeliharaan as proyek_waktu_pemeliharaan,
			a.nilai_pagu_proyek as proyek_nilai_pagu_proyek,
			a.lokasi_proyek as proyek_lokasi_proyek,
			a.pemilik_proyek as proyek_pemilik_proyek,
			a.konsultan_pelaksana as proyek_konsultan_pelaksana,
			a.konsultan_pengawas as proyek_konsultan_pengawas,
			a.tanggal_tender as proyek_tanggal_tender,
			a.nilai_penawaran as proyek_nilai_penawaran,
			a.divisi_id as proyek_divisi_id,
			a.tgl_update_status as proyek_tgl_update_status,
			a.keterangan as proyek_keterangan,
			a.user_entry as proyek_user_entry,
			a.divisi_name as proyek_divisi_name,
			a.xlong as proyek_xlong,
			a.xlat as proyek_xlat,
			a.mulai as proyek_mulai,
			a.akhir as proyek_akhir,
			a.nilai_kontrak_ppn as proyek_nilai_kontrak_ppn,
			a.nilai_kontrak_excl_ppn as proyek_nilai_kontrak_excl_ppn,
			a.peta_lokasi_proyek as proyek_peta_lokasi_proyek,
			a.jenis_proyek as proyek_jenis_proyek,
			b.kode_tree as tree_kode_tree,
			b.tree_item as tree_tree_item,
			b.tree_satuan as tree_tree_satuan,
			b.volume as tree_volume,
			b.tree_parent_kode as tree_tree_parent_kode,
			c.kode_analisa as ia_kode_analisa,
			c.harga as ia_harga,
			d.id_kat_analisa as da_id_kat_analisa,
			d.nama_item as da_nama_item,
			d.id_satuan as da_id_satuan,
			e.kode_material as asat_kode_material,
			e.koefisien as asat_koefisien,
			e.harga::int as asat_harga,
			e.kode_rap as asat_kode_rap,
			e.keterangan as asat_keterangan,
			f.koefisien as apek_koefisien,
			f.harga::int as apek_harga,
			f.kode_analisa as apek_parent_kode_analisa,
			g.icitem  as  bu_icitem,
			g.icvolume  as  bu_icvolume,
			g.icharga as  bu_icharga,
			g.satuan_id as  bu_satuan_id,
			g.kode_material as  bu_kode_material,
			g.satuan  as  bu_satuan,
			h.icitem_bank as  bank_icitem_bank,
			h.persentase  as  bank_persentase,
			h.id_satuan as  bank_id_satuan,
			h.kode_material as  bank_kode_material,
			h.harga as  bank_harga,
			h.satuan  as  bank_satuan,
			i.icitem_asuransi as  asuransi_icitem_asuransi,
			i.id_satuan as  asuransi_id_satuan,
			i.persentase  as  asuransi_persentase,
			i.kode_material as  asuransi_kode_material,
			i.harga as  asuransi_harga,
			i.satuan  as  asuransi_satuan,
			j.persentase as  vc_persentase,
			j.id_varcost_item  as  vc_id_varcost_item
			from simpro_m_rat_proyek_tender a
			join simpro_rat_item_tree b
			on a.id_proyek_rat = b.id_proyek_rat
			left join simpro_rat_analisa_item_apek c
			on a.id_proyek_rat = c.id_proyek_rat and b.kode_tree = c.kode_tree
			left join simpro_rat_analisa_daftar d
			on b.id_proyek_rat = d.id_tender and c.kode_analisa = d.kode_analisa
			left join simpro_rat_analisa_asat e on
			d.kode_analisa = e.kode_analisa and d.id_tender = e.id_tender
			left join simpro_rat_analisa_apek f on
			d.kode_analisa = f.parent_kode_analisa and d.id_tender = f.id_tender
			left join simpro_t_rat_idc_biaya_umum g on
			a.id_proyek_rat = g.id_proyek_rat
			left join simpro_t_rat_idc_bank h on
			a.id_proyek_rat = h.id_proyek_rat
			left join simpro_t_rat_idc_asuransi i on
			a.id_proyek_rat = i.id_proyek_rat
			left join simpro_t_rat_varcost j on
			a.id_proyek_rat = j.id_proyek_rat
			where a.id_proyek_rat = $id_tender
			order by b.kode_tree, e.kode_analisa, f.kode_analisa
		";


		$q = $this->db->query($sql_get_data_tender);

		if ($q->result()) {
			$n = 1;
			$head_data = array(
				'proyek_id_status_rat' => 'proyek_id_status_rat',
				'proyek_nama_proyek' => 'proyek_nama_proyek',
				'proyek_lingkup_pekerjaan' => 'proyek_lingkup_pekerjaan',
				'proyek_waktu_pelaksanaan' => 'proyek_waktu_pelaksanaan',
				'proyek_waktu_pemeliharaan' => 'proyek_waktu_pemeliharaan',
				'proyek_nilai_pagu_proyek' => 'proyek_nilai_pagu_proyek',
				'proyek_lokasi_proyek' => 'proyek_lokasi_proyek',
				'proyek_pemilik_proyek' => 'proyek_pemilik_proyek',
				'proyek_konsultan_pelaksana' => 'proyek_konsultan_pelaksana',
				'proyek_konsultan_pengawas' => 'proyek_konsultan_pengawas',
				'proyek_tanggal_tender' => 'proyek_tanggal_tender',
				'proyek_nilai_penawaran' => 'proyek_nilai_penawaran',
				'proyek_divisi_id' => 'proyek_divisi_id',
				'proyek_tgl_update_status' => 'proyek_tgl_update_status',
				'proyek_keterangan' => 'proyek_keterangan',
				'proyek_user_entry' => 'proyek_user_entry',
				'proyek_divisi_name' => 'proyek_divisi_name',
				'proyek_xlong' => 'proyek_xlong',
				'proyek_xlat' => 'proyek_xlat',
				'proyek_mulai' => 'proyek_mulai',
				'proyek_akhir' => 'proyek_akhir',
				'proyek_nilai_kontrak_ppn' => 'proyek_nilai_kontrak_ppn',
				'proyek_nilai_kontrak_excl_ppn' => 'proyek_nilai_kontrak_excl_ppn',
				'proyek_peta_lokasi_proyek' => 'proyek_peta_lokasi_proyek',
				'proyek_jenis_proyek' => 'proyek_jenis_proyek',
				'tree_kode_tree' => 'tree_kode_tree',
				'tree_tree_item' => 'tree_tree_item',
				'tree_tree_satuan' => 'tree_tree_satuan',
				'tree_volume' => 'tree_volume',
				'tree_tree_parent_kode' => 'tree_tree_parent_kode',
				'ia_kode_analisa' => 'ia_kode_analisa',
				'ia_harga' => 'ia_harga',
				'da_id_kat_analisa' => 'da_id_kat_analisa',
				'da_nama_item' => 'da_nama_item',
				'da_id_satuan' => 'da_id_satuan',
				'asat_kode_material' => 'asat_kode_material',
				'asat_koefisien' => 'asat_koefisien',
				'asat_harga' => 'asat_harga',
				'asat_kode_rap' => 'asat_kode_rap',
				'asat_keterangan' => 'asat_keterangan',
				'apek_koefisien' => 'apek_koefisien',
				'apek_harga' => 'apek_harga',
				'apek_parent_kode_analisa' => 'apek_parent_kode_analisa',
				'bu_icitem ' => 'bu_icitem',
				'bu_icvolume ' => 'bu_icvolume',
				'bu_icharga ' => 'bu_icharga',
				'bu_satuan_id ' => 'bu_satuan_id',
				'bu_kode_material ' => 'bu_kode_material',
				'bu_satuan ' => 'bu_satuan',
				'bank_icitem_bank ' => 'bank_icitem_bank',
				'bank_persentase ' => 'bank_persentase',
				'bank_id_satuan ' => 'bank_id_satuan',
				'bank_kode_material ' => 'bank_kode_material',
				'bank_harga ' => 'bank_harga',
				'bank_satuan ' => 'bank_satuan',
				'asuransi_icitem_asuransi ' => 'asuransi_icitem_asuransi',
				'asuransi_id_satuan ' => 'asuransi_id_satuan',
				'asuransi_persentase ' => 'asuransi_persentase',
				'asuransi_kode_material ' => 'asuransi_kode_material',
				'asuransi_harga ' => 'asuransi_harga',
				'asuransi_satuan ' => 'asuransi_satuan',
				'vc_persentase ' => 'vc_persentase',
				'vc_id_varcost_item ' => 'vc_id_varcost_item'

				);

			$data[] = $head_data;

			foreach ($q->result() as $r_data) {
				$d_data = array(
					'proyek_id_status_rat' => $r_data->proyek_id_status_rat,
					'proyek_nama_proyek' => $r_data->proyek_nama_proyek,
					'proyek_lingkup_pekerjaan' => $r_data->proyek_lingkup_pekerjaan,
					'proyek_waktu_pelaksanaan' => $r_data->proyek_waktu_pelaksanaan,
					'proyek_waktu_pemeliharaan' => $r_data->proyek_waktu_pemeliharaan,
					'proyek_nilai_pagu_proyek' => $r_data->proyek_nilai_pagu_proyek,
					'proyek_lokasi_proyek' => $r_data->proyek_lokasi_proyek,
					'proyek_pemilik_proyek' => $r_data->proyek_pemilik_proyek,
					'proyek_konsultan_pelaksana' => $r_data->proyek_konsultan_pelaksana,
					'proyek_konsultan_pengawas' => $r_data->proyek_konsultan_pengawas,
					'proyek_tanggal_tender' => $r_data->proyek_tanggal_tender,
					'proyek_nilai_penawaran' => $r_data->proyek_nilai_penawaran,
					'proyek_divisi_id' => $r_data->proyek_divisi_id,
					'proyek_tgl_update_status' => $r_data->proyek_tgl_update_status,
					'proyek_keterangan' => $r_data->proyek_keterangan,
					'proyek_user_entry' => $r_data->proyek_user_entry,
					'proyek_divisi_name' => $r_data->proyek_divisi_name,
					'proyek_xlong' => $r_data->proyek_xlong,
					'proyek_xlat' => $r_data->proyek_xlat,
					'proyek_mulai' => $r_data->proyek_mulai,
					'proyek_akhir' => $r_data->proyek_akhir,
					'proyek_nilai_kontrak_ppn' => $r_data->proyek_nilai_kontrak_ppn,
					'proyek_nilai_kontrak_excl_ppn' => $r_data->proyek_nilai_kontrak_excl_ppn,
					'proyek_peta_lokasi_proyek' => $r_data->proyek_peta_lokasi_proyek,
					'proyek_jenis_proyek' => $r_data->proyek_jenis_proyek,
					'tree_kode_tree' => $r_data->tree_kode_tree,
					'tree_tree_item' => $r_data->tree_tree_item,
					'tree_tree_satuan' => $r_data->tree_tree_satuan,
					'tree_volume' => $r_data->tree_volume,
					'tree_tree_parent_kode' => $r_data->tree_tree_parent_kode,
					'ia_kode_analisa' => $r_data->ia_kode_analisa,
					'ia_harga' => $r_data->ia_harga,
					'da_id_kat_analisa' => $r_data->da_id_kat_analisa,
					'da_nama_item' => $r_data->da_nama_item,
					'da_id_satuan' => $r_data->da_id_satuan,
					'asat_kode_material' => str_replace('.', '-', $r_data->asat_kode_material),
					'asat_koefisien' => $r_data->asat_koefisien,
					'asat_harga' => $r_data->asat_harga,
					'asat_kode_rap' => $r_data->asat_kode_rap,
					'asat_keterangan' => $r_data->asat_keterangan,
					'apek_koefisien' => $r_data->apek_koefisien,
					'apek_harga' => $r_data->apek_harga,
					'apek_parent_kode_analisa' => $r_data->apek_parent_kode_analisa,
					'bu_icitem' => $r_data->bu_icitem,
					'bu_icvolume' => $r_data->bu_icvolume,
					'bu_icharga' => $r_data->bu_icharga,
					'bu_satuan_id' => $r_data->bu_satuan_id,
					'bu_kode_material' => str_replace('.', '-', $r_data->bu_kode_material),
					'bu_satuan' => $r_data->bu_satuan,
					'bank_icitem_bank' => $r_data->bank_icitem_bank,
					'bank_persentase' => $r_data->bank_persentase,
					'bank_id_satuan' => $r_data->bank_id_satuan,
					'bank_kode_material' => str_replace('.', '-', $r_data->bank_kode_material),
					'bank_harga' => $r_data->bank_harga,
					'bank_satuan' => $r_data->bank_satuan,
					'asuransi_icitem_asuransi' => $r_data->asuransi_icitem_asuransi,
					'asuransi_id_satuan' => $r_data->asuransi_id_satuan,
					'asuransi_persentase' => $r_data->asuransi_persentase,
					'asuransi_kode_material' => str_replace('.', '-', $r_data->asuransi_kode_material),
					'asuransi_harga' => $r_data->asuransi_harga,
					'asuransi_satuan' => $r_data->asuransi_satuan,
					'vc_persentase' => $r_data->vc_persentase,
					'vc_id_varcost_item' => $r_data->vc_id_varcost_item
				);

				$data[] = $d_data;
				$n++;
			}
		}

		$this->outputCSV($data);

	}

	function outputCSV($data) {

	    $outstream = fopen("php://output", 'w');

	    function __outputCSV(&$vals, $key, $filehandler) {
	        fputcsv($filehandler, $vals, ';', '"');
	    }
	    array_walk($data, '__outputCSV', $outstream);

	    fclose($outstream);
	}

	function export_tender()
	{
		$id_tender = $this->session->userdata('id_tender');

		$nama_tender = $this->db->query("select nama_proyek from simpro_m_rat_proyek_tender where id_proyek_rat = $id_tender")->row()->nama_proyek;

		$file = 'import_tender_'.str_replace(' ', '_', $nama_tender).'_'.date('Y-m-d').'.csv';

		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			$path = 'C://Windows//Temp//';		
		} else
		{
			$path = '/tmp/';		
		}

		$dpath = realpath($path);
		$filename = $dpath. DIRECTORY_SEPARATOR . $file;
		$query = sprintf("COPY (
			select 
			a.id_status_rat as proyek_id_status_rat,
			a.nama_proyek as proyek_nama_proyek,
			a.lingkup_pekerjaan as proyek_lingkup_pekerjaan,
			a.waktu_pelaksanaan as proyek_waktu_pelaksanaan,
			a.waktu_pemeliharaan as proyek_waktu_pemeliharaan,
			a.nilai_pagu_proyek as proyek_nilai_pagu_proyek,
			a.lokasi_proyek as proyek_lokasi_proyek,
			a.pemilik_proyek as proyek_pemilik_proyek,
			a.konsultan_pelaksana as proyek_konsultan_pelaksana,
			a.konsultan_pengawas as proyek_konsultan_pengawas,
			a.tanggal_tender as proyek_tanggal_tender,
			a.nilai_penawaran as proyek_nilai_penawaran,
			a.divisi_id as proyek_divisi_id,
			a.tgl_update_status as proyek_tgl_update_status,
			a.keterangan as proyek_keterangan,
			a.user_entry as proyek_user_entry,
			a.divisi_name as proyek_divisi_name,
			a.xlong as proyek_xlong,
			a.xlat as proyek_xlat,
			a.mulai as proyek_mulai,
			a.akhir as proyek_akhir,
			a.nilai_kontrak_ppn as proyek_nilai_kontrak_ppn,
			a.nilai_kontrak_excl_ppn as proyek_nilai_kontrak_excl_ppn,
			a.peta_lokasi_proyek as proyek_peta_lokasi_proyek,
			a.jenis_proyek as proyek_jenis_proyek,
			b.kode_tree as tree_kode_tree,
			b.tree_item as tree_tree_item,
			b.tree_satuan as tree_tree_satuan,
			b.volume as tree_volume,
			b.tree_parent_kode as tree_tree_parent_kode,
			c.kode_analisa as ia_kode_analisa,
			c.harga as ia_harga,
			d.id_kat_analisa as da_id_kat_analisa,
			d.nama_item as da_nama_item,
			d.id_satuan as da_id_satuan,
			e.kode_material as asat_kode_material,
			e.koefisien as asat_koefisien,
			e.harga::int as asat_harga,
			e.kode_rap as asat_kode_rap,
			e.keterangan as asat_keterangan,
			f.koefisien as apek_koefisien,
			f.harga::int as apek_harga,
			f.kode_analisa as apek_parent_kode_analisa,
			g.icitem  as  bu_icitem,
			g.icvolume  as  bu_icvolume,
			g.icharga as  bu_icharga,
			g.satuan_id as  bu_satuan_id,
			g.kode_material as  bu_kode_material,
			g.satuan  as  bu_satuan,
			h.icitem_bank as  bank_icitem_bank,
			h.persentase  as  bank_persentase,
			h.id_satuan as  bank_id_satuan,
			h.kode_material as  bank_kode_material,
			h.harga as  bank_harga,
			h.satuan  as  bank_satuan,
			i.icitem_asuransi as  asuransi_icitem_asuransi,
			i.id_satuan as  asuransi_id_satuan,
			i.persentase  as  asuransi_persentase,
			i.kode_material as  asuransi_kode_material,
			i.harga as  asuransi_harga,
			i.satuan  as  asuransi_satuan,
			j.persentase as  vc_persentase,
			j.id_varcost_item  as  vc_id_varcost_item
			from simpro_m_rat_proyek_tender a
			join simpro_rat_item_tree b
			on a.id_proyek_rat = b.id_proyek_rat
			left join simpro_rat_analisa_item_apek c
			on a.id_proyek_rat = c.id_proyek_rat and b.kode_tree = c.kode_tree
			left join simpro_rat_analisa_daftar d
			on b.id_proyek_rat = d.id_tender and c.kode_analisa = d.kode_analisa
			left join simpro_rat_analisa_asat e on
			d.kode_analisa = e.kode_analisa and d.id_tender = e.id_tender
			left join simpro_rat_analisa_apek f on
			d.kode_analisa = f.parent_kode_analisa and d.id_tender = f.id_tender
			left join simpro_t_rat_idc_biaya_umum g on
			a.id_proyek_rat = g.id_proyek_rat
			left join simpro_t_rat_idc_bank h on
			a.id_proyek_rat = h.id_proyek_rat
			left join simpro_t_rat_idc_asuransi i on
			a.id_proyek_rat = i.id_proyek_rat
			left join simpro_t_rat_varcost j on
			a.id_proyek_rat = j.id_proyek_rat
			where a.id_proyek_rat = $id_tender
			order by b.kode_tree, e.kode_analisa, f.kode_analisa
		)  TO '$filename' WITH DELIMITER ';' CSV HEADER");
		$this->db->query($query);

		$this->load->library('zip');	

		$this->zip->read_file($filename);

		$this->zip->download(sprintf("backup-tender-$file-%s.zip",date('dmY')));
	}
}