<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_muturesiko extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	function insertdata($tbl_get,$data)
	{
		$this->db->insert($tbl_get,$data);
	}
	
	function deletedata($tbl_get,$id)
	{
		switch($tbl_get){
			case 'simpro_tbl_rencana_realisasi_mutu':			
			$this->db->where('rencana_realisasi_mutu_id', $id);
			break;
			case 'simpro_tbl_analisis_risiko':			
			$this->db->where('ar_id', $id);
			break;
			case 'simpro_tbl_penanganan_risiko':			
			$this->db->where('penanganan_risiko_id', $id);
			break;
		}
		$this->db->delete($tbl_get);
	}
	
	function editdata($tbl_get,$data,$id)
	{
		switch($tbl_get){
			case 'simpro_tbl_rencana_realisasi_mutu':			
			$this->db->where('rencana_realisasi_mutu_id', $id);
			break;
			case 'simpro_tbl_analisis_risiko':			
			$this->db->where('ar_id', $id);
			break;
			case 'simpro_tbl_daftar_risiko':			
			$this->db->where('daftar_risiko_id', $id);
			break;
			case 'simpro_tbl_penanganan_risiko':			
			$this->db->where('penanganan_risiko_id', $id);
			break;
		}		
		$this->db->update($tbl_get,$data);
	}
	
	function get_status_jenis()
	{
		$query = "
			SELECT 
				rencana_realisasi_mutu_jenis_id AS value, UPPER(rencana_realisasi_mutu_jenis) AS text
			FROM 
				simpro_m_rencana_realisasi_mutu_jenis
			";
		return $this->_retdata($query);			
	}
	
	function get_status_risiko()
	{
		$query = "
			SELECT 
				status_risiko_id AS value, UPPER(status_risiko) AS text
			FROM 
				simpro_m_status_risiko
			";
		return $this->_retdata($query);			
	}
	
	function get_tingkat_akibat()
	{
		$query = "
			SELECT 
				tingkat_akibat_id AS value, UPPER(tingkat_akibat_nama) AS text
			FROM 
				simpro_m_tingkat_akibat
			";
		return $this->_retdata($query);			
	}
	
	function get_tingkat_kemungkinan()
	{
		$query = "
			SELECT 
				tingkat_kemungkinan_id AS value, UPPER(tingkat_kemungkinan_nama) AS text
			FROM 
				simpro_m_tingkat_kemungkinan
			";
		return $this->_retdata($query);			
	}
	
	function get_tingkat_risiko()
	{
		$query = "
			SELECT 
				tingkat_risiko_id AS value, UPPER(tingkat_risiko_nama) AS text
			FROM 
				simpro_m_tingkat_risiko
			";
		return $this->_retdata($query);			
	}
	
	function get_sisa_risiko()
	{
		$query = "
			SELECT 
				sisa_risiko_id AS value, UPPER(sisa_risiko_nama) AS text
			FROM 
				simpro_m_sisa_risiko
			";
		return $this->_retdata($query);			
	}
	
	function getdata($tbl_info,$start,$limit){
		$data = array();
		$dat = array();
		//$proyek_id = "1";
		$proyek_id = $this->session->userdata('proyek_id');
		switch ($tbl_info) {
			case 'simpro_tbl_rencana_realisasi_mutu':
				$query="select * from $tbl_info a inner join simpro_m_rencana_realisasi_mutu_jenis b on a.rencana_realisasi_mutu_jenis_id = b.rencana_realisasi_mutu_jenis_id where a.proyek_id=$proyek_id";
				$q = $this->db->query($query);
				if ($q->num_rows() > 0) {
					foreach($q->result() as $row) {
					//$jenis = $row->rencana_realisasi_mutu_jenis_id;
					//if ($jenis == 0){
					//$jenis = 'Umum';
					//} elseif ($jenis == 1) {
					//$jenis = 'Khusus / Teknis';
				//}
    			$data['rr_id'] = $row->rencana_realisasi_mutu_id;
    			$data['proyek_id'] = $row->proyek_id;
    			$data['rr_uraian_rencana'] = $row->rr_uraian_rencana;
    			$data['rr_uraian_realisasi'] = $row->rr_uraian_realisasi;
    			$data['rr_tgl'] = $row->rr_tgl;
    			$data['user_id'] = $row->user_id;
    			$data['rr_jenis'] = $row->rencana_realisasi_mutu_jenis;
    			$data['rr_jenis_id'] = $row->rencana_realisasi_mutu_jenis_id;

    			$dat[] = $data;
    			}
				}
			break;

			case 'simpro_tbl_analisis_risiko':
				$query="select * from $tbl_info a inner join simpro_m_tingkat_akibat b on a.tingkat_akibat = b.tingkat_akibat_id inner join simpro_m_tingkat_kemungkinan c on a.tingkat_kemungkinan = c.tingkat_kemungkinan_id inner join simpro_m_tingkat_risiko d on a.tingkat_risiko = d.tingkat_risiko_id inner join simpro_m_sisa_risiko e on a.sisa_risiko = e.sisa_risiko_id where a.proyek_id=$proyek_id ";
				$q = $this->db->query($query);
				if ($q->num_rows() > 0) {
				foreach($q->result() as $row) {

    			$data['ar_id'] = $row->ar_id;
    			$data['proyek_id'] = $row->proyek_id;
    			$data['risiko'] = $row->risiko;
    			$data['akibat'] = $row->akibat;
    			$data['analisis'] = $row->analisis;
    			$data['rencana_penanganan'] = $row->rencana_penanganan;
    			$data['batas_waktu'] = $row->batas_waktu;
    			$data['keputusan'] = $row->keputusan;    			
    			$data['tingkat_akibat'] = $row->tingkat_akibat_nama;
    			$data['tingkat_akibat_id'] = $row->tingkat_akibat;
    			$data['tingkat_kemungkinan'] = $row->tingkat_kemungkinan_nama;
    			$data['tingkat_kemungkinan_id'] = $row->tingkat_kemungkinan;
    			$data['tingkat_risiko'] = $row->tingkat_risiko_nama;    			
    			$data['tingkat_risiko_id'] = $row->tingkat_risiko;    			
    			$data['sisa_risiko'] = $row->sisa_risiko_nama;
    			$data['sisa_risiko_id'] = $row->sisa_risiko;
    			$data['pic'] = $row->pic;
    			$data['tgl'] = $row->tgl;
    			$data['user_id'] = $row->user_id;
				$query = "select * from simpro_tbl_approve where proyek_id=$proyek_id and form_approve='RAB'";
						$res=$this->db->query($query);						
						if($res->num_rows() >=2){
							$data['status'] = 0;
							$data['status_risiko'] = $res->row()->status;
						}else{
							$data['status'] = 1;
							$data['status_risiko'] = "Open";
						}
    			$dat[] = $data;
    			}
				}
			break;

			case 'simpro_tbl_daftar_risiko':
				$query="select * from $tbl_info z inner join simpro_tbl_analisis_risiko a on z.ar_id=a.ar_id inner join simpro_m_tingkat_akibat b on a.tingkat_akibat = b.tingkat_akibat_id inner join simpro_m_tingkat_kemungkinan c on a.tingkat_kemungkinan = c.tingkat_kemungkinan_id inner join simpro_m_tingkat_risiko d on a.tingkat_risiko = d.tingkat_risiko_id inner join simpro_m_sisa_risiko e on a.sisa_risiko = e.sisa_risiko_id where a.proyek_id=$proyek_id";
				$q = $this->db->query($query);
				if ($q->num_rows() > 0) {
				foreach($q->result() as $row) {

    			$data['daftar_risiko_id'] = $row->daftar_risiko_id;
    			$data['proyek_id'] = $row->proyek_id;
    			$data['konteks'] = $row->konteks;
    			$data['akibat'] = $row->akibat;
    			$data['penyebab'] = $row->penyebab;
    			$data['kemungkinan_terjadi'] = $row->kemungkinan_terjadi;
    			$data['faktor_positif'] = $row->faktor_positif;
    			$data['tingkat_akibat'] = $row->tingkat_akibat_nama;
    			$data['tingkat_kemungkinan'] = $row->tingkat_kemungkinan_nama;    
    			$data['tingkat_risiko'] = $row->tingkat_risiko_nama; 
    			$data['tingkat_akibat_no'] = $row->tingkat_akibat;
    			$data['tingkat_kemungkinan_no'] = $row->tingkat_kemungkinan;    
    			$data['tingkat_risiko_no'] = $row->tingkat_risiko; 
    			$data['prioritas'] = $row->prioritas;
    			$data['ar_id'] = $row->ar_id;
    			$data['tgl'] = $row->tgl;
    			$data['user_id'] = $row->user_id;
				$query = "select * from simpro_tbl_approve where proyek_id=$proyek_id and form_approve='RAB'";
						$res=$this->db->query($query);						
						if($res->num_rows() >=2){
							$data['status'] = 0;
							$data['status_risiko'] = $res->row()->status;
						}else{
							$data['status'] = 1;
							$data['status_risiko'] = "Open";
						}
    			$dat[] = $data;
    			}
				}
			break;
			
			
			case 'lap_penanganan':
				//$proyek_id = "1";
				$proyek_id = $this->session->userdata('proyek_id');
				$query="select extract(year from tahap_tanggal_kendali) as tahun,extract(month from tahap_tanggal_kendali) as bulan from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id group by extract(year from tahap_tanggal_kendali),extract(month from tahap_tanggal_kendali) order by extract(year from tahap_tanggal_kendali),extract(month from tahap_tanggal_kendali) asc";
				$q = $this->db->query($query);
				if ($q->num_rows() > 0) {
					foreach($q->result() as $row) {

						$data['bulan'] = $this->bulan($row->bulan);
						$data['tahun'] = $row->tahun;
						
						$query2="select * from simpro_tbl_approve where extract(year from tgl_approve) =".$row->tahun." and extract(month from tgl_approve) =".$row->bulan."  and proyek_id = $proyek_id and form_approve='ALL'";
						$q2 = $this->db->query($query2);
						if ($q2->num_rows() >= 2) {
							if($q2->row["status"]=='close'){
								$data['status'] = "APPROVED";   
							 }elseif($q2->row["status"]=='open'){
								$data['status'] = "NOT APPROVE";
							 }
						}else{
							$data['status'] = "NOT APPROVE";
						}
						$dat[] = $data;
					}
				}
		
			break;
	}
		return $dat;
	}	
	function getdata2($tbl_info,$start,$limit,$bln,$thn){
		$proyek_id = $this->session->userdata('proyek_id');
		$tgl_now = date('Y-m-d');
		$tgl_rab = $thn.'-'.$bln.'-01';
		$cek_analisis = $this->db->query("select * from simpro_tbl_analisis_risiko where proyek_id = $proyek_id");
		if ($cek_analisis->result()) {
			foreach ($cek_analisis->result() as $r_analisis) {
				$cek_analisis_penanganan = $this->db->query("select * from simpro_tbl_penanganan_risiko where proyek_id = $proyek_id and ar_id = $r_analisis->ar_id");
				if ($cek_analisis_penanganan->num_rows() == 0) {
					$ar_data = array(
						'proyek_id' => $r_analisis->proyek_id,
						'risiko' => $r_analisis->risiko,
						'tgl_aak' => $tgl_rab,
						'tgl' => $tgl_now,
						'user_id' => $r_analisis->user_id,
						'ar_id' => $r_analisis->ar_id,
						'tingkat_risiko_id' => 1,
						'status_risiko_id' => 1,
						'realisasi_sisa_risiko_id' => 1,
						'target_sisa_risiko_id' => 1
					);

					$this->db->insert('simpro_tbl_penanganan_risiko',$ar_data);
				}
			}
		}

		$data = array();
		$dat = array();
		$user_id = $this->session->userdata('uid');
		switch ($tbl_info) {
			case 'lap_penanganan':
				//$proyek_id = "1";
				$query="select *, a.pic as pica, a.risiko as risikoa, e.sisa_risiko_nama as realisasi_sisa_risiko,c.sisa_risiko_nama as target_sisa_risiko  from simpro_tbl_penanganan_risiko a inner join simpro_m_tingkat_risiko b on a.tingkat_risiko_id = b.tingkat_risiko_id inner join simpro_m_sisa_risiko c on a.target_sisa_risiko_id = c.sisa_risiko_id inner join simpro_m_status_risiko d on a.status_risiko_id = d.status_risiko_id inner join simpro_m_sisa_risiko e on a.realisasi_sisa_risiko_id = e.sisa_risiko_id inner join simpro_tbl_analisis_risiko f on a.ar_id = f.ar_id where a.proyek_id=$proyek_id and tgl_aak='$thn-$bln-01'";
				//echo $query;
				$q = $this->db->query($query);
				if ($q->num_rows() <= 0) {
					//foreach($q->result() as $row) {
					$tglskrg = date("Y-m-d");
					$query2="insert into simpro_tbl_penanganan_risiko(proyek_id,risiko,tgl_aak,tgl,user_id,ar_id) select $proyek_id,risiko,'$thn-$bln-01','$tglskrg',$user_id,ar_id from simpro_tbl_analisis_risiko where proyek_id=$proyek_id";
					$this->db->query($query2);
						//$dat[] = $data;
					//}
				}
					$query = $this->db->query("select sum(biaya_memitigasi + biaya_sisa_risiko) as total_all from simpro_tbl_penanganan_risiko where proyek_id=$proyek_id and tgl_aak ='$thn-$bln-01'");				
					$total_all = $query->row()->total_all;
					foreach($q->result() as $row) {
						$data['penanganan_risiko_id'] = $row->penanganan_risiko_id;
						$data['proyek_id'] = $row->proyek_id;
						$data['risiko'] = $row->risikoa;
						$data['nilai_risiko'] = $row->nilai_risiko;
						$data['tgl'] = $row->tgl;
						$data['user_id'] = $row->user_id;
						$data['realisasi_tindakan'] = $row->realisasi_tindakan;
						$data['rencana_penanganan'] = $row->rencana_penanganan;
						
						$data['tingkat_risiko'] = $row->tingkat_risiko_nama;
						$data['tingkat_risiko_id'] = $row->tingkat_risiko_id;
						
						$data['status_risiko'] = $row->status_risiko;
						$data['status_risiko_id'] = $row->status_risiko_id;
						
						$data['realisasi_sisa_risiko'] = $row->realisasi_sisa_risiko;
						$data['realisasi_sisa_risiko_id'] = $row->realisasi_sisa_risiko_id;
						
						$data['target_sisa_risiko'] = $row->target_sisa_risiko;
						$data['target_sisa_risiko_id'] = $row->target_sisa_risiko_id;
						
						
						$data['pic'] = $row->pica;
						$data['biaya_memitigasi'] = $row->biaya_memitigasi;
						$data['biaya_sisa_risiko'] = $row->biaya_sisa_risiko;
						$data['tgl_aak'] = $row->tgl_aak;
						$data['ar_id'] = $row->ar_id;
						$data['total'] = $row->biaya_memitigasi+$row->biaya_sisa_risiko;
						$query = "select * from simpro_tbl_approve where proyek_id=$proyek_id and tgl_approve='$thn-$bln-01' and form_approve='ALL'";
						$res=$this->db->query($query);
						if($res->num_rows() >=2){
							$data['status'] = 0;
						}else{
							$data['status'] = 1;
						}
						//$data['totalall'] = $total_all;
						$dat[] = $data;
					}
					$data['penanganan_risiko_id'] = "bawah";
					$data['proyek_id'] = "";
					$data['risiko'] = "<b>NILAI RISIKO yang terjadi : ".$total_all."</b>";
					$data['nilai_risiko'] = "";
					$data['tgl'] = "";
					$data['user_id'] = "";
					$data['realisasi_tindakan'] = "";
					$data['rencana_penanganan'] = "";
					
					$data['tingkat_risiko'] = "";
					$data['tingkat_risiko_id'] = "";
					
					$data['status_risiko'] = "";
					$data['status_risiko_id'] = "";
					
					$data['realisasi_sisa_risiko'] = "";
					$data['realisasi_sisa_risiko_id'] = "";
					
					$data['target_sisa_risiko'] = "";
					$data['target_sisa_risiko_id'] = "";
					
					
					$data['pic'] ="";
					$data['biaya_memitigasi'] = "";
					$data['biaya_sisa_risiko'] = "";
					$data['tgl_aak'] = "";
					$data['ar_id'] = "";
					$data['total'] = "";
					
						$data['status'] = "";
					
					
					$dat[] = $data;
					
			break;
		}
		return $dat;
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
	function status($no){
		switch ($no) {
			case 0: $no = 'Open'; break;
			case 1: $no = 'Close'; break;
		}
		return $no;
	}
	public function getinfo($id){
		$data = array();
		$dat = array();
		$query = "
			SELECT 
				*
			FROM 
				simpro_tbl_proyek a inner join simpro_tbl_divisi b on a.divisi_kode = b.divisi_id
			WHERE 
				a.proyek_id = $id
			";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
			foreach($q->result() as $row) {			
				$dat['divisi_name'] = $row->divisi_name;
				$dat['proyek'] = $row->proyek;
				$dat['mulai'] = $row->mulai;
				$dat['berakhir'] = $row->berakhir;
				$dat['nilai_kontrak_non_ppn'] = $row->nilai_kontrak_non_ppn;
				//$nilai_kontrak_non_ppn = $row->nilai_kontrak_non_ppn?$row->nilai_kontrak_non_ppn:1;
				if ($row->rap_ditetapkan <> 0){
					$sasaran = $row->rap_ditetapkan/$dat['nilai_kontrak_non_ppn']; 
				}else{
					$sasaran = 0;
				}
				$dat['sasaran'] = $sasaran;
				
				$data = $dat;
			}
		}
		
		return $data;			
	}
	
	public function _retdata($qry)
	{
		$rs = $this->db->query($qry);
		$totdata = $rs->num_rows();
		if($totdata > 0)
		{
			return array('total'=>$totdata, 'success'=>true, 'data'=>$rs->result_array());
		} else return false;	
	}
}