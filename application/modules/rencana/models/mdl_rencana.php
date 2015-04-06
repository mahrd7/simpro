<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_rencana extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	function data_tender($div, $idtender=FALSE)
	{
		
		if ($div == 21 || $div == 6) {
			if($idtender) 
				$qad = " where simpro_m_rat_proyek_tender.id_proyek_rat = '".$idtender."' ";
					else $qad = "";

			$query = sprintf("
				SELECT 
					simpro_m_rat_proyek_tender.*, 
					simpro_m_status_rat_tender.status, 
					simpro_tbl_divisi.divisi_name as divisi,
					simpro_tbl_divisi.divisi_id as divisi_k
				FROM simpro_m_rat_proyek_tender 
				INNER JOIN simpro_m_status_rat_tender ON simpro_m_status_rat_tender.id_status_rat = simpro_m_rat_proyek_tender.id_status_rat
				INNER JOIN simpro_tbl_divisi ON simpro_tbl_divisi.divisi_id = simpro_m_rat_proyek_tender.divisi_id
				".$qad."
				ORDER BY simpro_m_rat_proyek_tender.id_proyek_rat DESC
			", $div);
		} else {
			if($idtender) 
				$qad = " AND simpro_m_rat_proyek_tender.id_proyek_rat = '".$idtender."' ";
					else $qad = "";

			$query = sprintf("
				SELECT 
					simpro_m_rat_proyek_tender.*, 
					simpro_m_status_rat_tender.status, 
					simpro_tbl_divisi.divisi_name as divisi,
					simpro_tbl_divisi.divisi_id as divisi_k
				FROM simpro_m_rat_proyek_tender 
				INNER JOIN simpro_m_status_rat_tender ON simpro_m_status_rat_tender.id_status_rat = simpro_m_rat_proyek_tender.id_status_rat
				INNER JOIN simpro_tbl_divisi ON simpro_tbl_divisi.divisi_id = simpro_m_rat_proyek_tender.divisi_id
				WHERE simpro_m_rat_proyek_tender.divisi_id = '%d'
				".$qad."
				ORDER BY simpro_m_rat_proyek_tender.id_proyek_rat DESC
			", $div);
		}

		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;
	}
	
	function data_proyek($id)
	{
		$query = sprintf("SELECT * FROM simpro_tbl_proyek WHERE proyek_id = %d", $id);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->row_array());
		} else return false;
	}
	
	function data_tender_by_id($id)
	{
		/*
			simpro_m_status_rat_tender.status, 
			tbl_divisi.divisi_name as divisi		
				INNER JOIN simpro_m_status_rat_tender ON simpro_m_status_rat_tender.id_status_rat = simpro_m_rat_proyek_tender.id_status_rat
				INNER JOIN simpro_tbl_divisi ON simpro_tbl_divisi.divisi_id = simpro_m_rat_proyek_tender.divisi_id
		*/
		$query = sprintf("
				SELECT 
					simpro_m_rat_proyek_tender.*
				FROM simpro_m_rat_proyek_tender 
				WHERE simpro_m_rat_proyek_tender.id_proyek_rat = '%d'
				ORDER BY simpro_m_rat_proyek_tender.id_proyek_rat DESC
			", $id);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return $rs->row_array();
		} else return false;
	}
	
	function get_data_tender($id)
	{
		$query = sprintf("
			SELECT 
				simpro_m_rat_proyek_tender.*, simpro_m_status_rat_tender.status,
				simpro_tbl_divisi.divisi_kode, simpro_tbl_divisi.divisi_name
			FROM simpro_m_rat_proyek_tender 
			INNER JOIN simpro_m_status_rat_tender ON simpro_m_status_rat_tender.id_status_rat = simpro_m_rat_proyek_tender.id_status_rat
			INNER JOIN simpro_tbl_divisi on simpro_tbl_divisi.divisi_id = simpro_m_rat_proyek_tender.divisi_id
			WHERE simpro_m_rat_proyek_tender.id_proyek_rat = '%d'
			", $id);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->row_array());
		} else return false;
	}
		
	function data_harga_satuan_rab($idproyek)
	{
		$query = sprintf("
  			SELECT 
				simpro_rat_analisa.*,
				simpro_tbl_detail_material.detail_material_kode,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) as subtotal,
				simpro_tbl_subbidang.subbidang_name,
				simpro_rat_item_tree.tree_item,
				simpro_rat_rab_analisa.harga_rab,
				simpro_rat_rab_analisa.koefisien_rab,
				(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) as subtotal_rab
			FROM 
				simpro_rat_analisa
				LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa.detail_material_kode
				LEFT JOIN simpro_tbl_subbidang ON simpro_tbl_subbidang.subbidang_kode  = simpro_tbl_detail_material.subbidang_kode
				LEFT JOIN simpro_rat_rab_analisa ON simpro_rat_rab_analisa.id_simpro_rat_analisa  = simpro_rat_analisa.id_simpro_rat_analisa
				INNER JOIN simpro_rat_item_tree ON simpro_rat_analisa.rat_item_tree = simpro_rat_item_tree.rat_item_tree
			WHERE 
				simpro_rat_analisa.id_proyek_rat = '%d'				
			ORDER BY simpro_rat_analisa.id_proyek_rat DESC			
		", $idproyek);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			$i=0;
			$char = "";
			$rows = $rs->result_array();
			foreach($rs->result_array() as $res_arr)
			{
				$seq = $i+1;
				switch(strlen($seq))
				{
					case 1: $char = "A00". $seq; break;
					case 2: $char = "A0". $seq; break;
					case 3: $char = "A". $seq; break;					
				}
				$koderap = array("kode_rap"=>$char);
				$data[] = array_merge($rows[$i], $koderap);
				$i++;
			}
			return array('total'=>$totdata, 'data'=> $data);
		} else return false;
	}

	function get_data_item_analisa_apek($idproyek)
	{
		$query = sprintf("
  			SELECT 
				simpro_rat_analisa_item_apek.*,
				( '[' || simpro_rat_analisa_item_apek.kode_analisa || '] ' || simpro_rat_item_tree.kode_tree || '. ' || simpro_rat_item_tree.tree_item) AS item_analisa,
				simpro_rat_item_tree.kode_tree,
				simpro_rat_item_tree.tree_item,
				simpro_rat_item_tree.tree_satuan,
				simpro_rat_item_tree.volume,
				simpro_rat_analisa_item_apek.harga,				
				COALESCE(simpro_rat_analisa_item_apek.harga * simpro_rat_item_tree.volume,0) as subtotal,
				simpro_rat_item_tree.tree_item,
				simpro_rat_analisa_daftar.nama_item AS uraian_analisa
			FROM 
				simpro_rat_analisa_item_apek
				INNER JOIN simpro_rat_item_tree ON simpro_rat_item_tree.rat_item_tree = simpro_rat_analisa_item_apek.rat_item_tree
				INNER JOIN simpro_rat_analisa_daftar ON simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_item_apek.kode_analisa
			WHERE 
				simpro_rat_analisa_item_apek.id_proyek_rat = '%d'				
			ORDER BY simpro_rat_item_tree.kode_tree, simpro_rat_analisa_item_apek.kode_analisa ASC 		
		", $idproyek);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata > 0)
		{
			$data = $rs->result_array();
			return array('total'=>$totdata, 'data'=> $data, '_dc' => @$_REQUEST['_dc']);
		} else return false;	
	}
	
	function data_direct_cost($idproyek)
	{
		#(simpro_rat_analisa.aharga * simpro_rat_analisa.avolume) as subtotal,
		$query = sprintf("
  			SELECT 
				simpro_rat_analisa.*,
				simpro_tbl_detail_material.detail_material_kode,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) as subtotal,
				simpro_tbl_subbidang.subbidang_name,
				simpro_rat_item_tree.tree_item
			FROM 
				simpro_rat_analisa
				LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa.detail_material_kode
				LEFT JOIN simpro_tbl_subbidang ON simpro_tbl_subbidang.subbidang_kode  = simpro_tbl_detail_material.subbidang_kode
				INNER JOIN simpro_rat_item_tree ON simpro_rat_analisa.rat_item_tree = simpro_rat_item_tree.rat_item_tree
			WHERE 
				simpro_rat_analisa.id_proyek_rat = '%d'				
			ORDER BY simpro_rat_analisa.id_proyek_rat DESC			
		", $idproyek);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			$i=0;
			$char = "";
			$rows = $rs->result_array();
			foreach($rs->result_array() as $res_arr)
			{
				$seq = $i+1;
				switch(strlen($seq))
				{
					case 1: $char = "A00". $seq; break;
					case 2: $char = "A0". $seq; break;
					case 3: $char = "A". $seq; break;					
				}
				$koderap = array("kode_rap"=>$char);
				$data[] = array_merge($rows[$i], $koderap);
				$i++;
			}
			return array('total'=>$totdata, 'data'=> $data);
		} else return false;
	}

	function rat_analisa_satuan_pekerjaan($idproyek)
	{
		$query = sprintf("
			SELECT 
				simpro_rat_analisa.detail_material_kode,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				COUNT(simpro_rat_analisa.detail_material_kode) as totitem,
				simpro_rat_analisa.id_proyek_rat,				
				simpro_rat_analisa.aketerangan,
				simpro_rat_analisa.kode_rap,
				simpro_rat_analisa.aharga,
				simpro_rat_analisa.avolume,
				simpro_rat_analisa.akoefisien,
				(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) AS subtotal,
				((simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) * COUNT(simpro_rat_analisa.detail_material_kode)) AS total
			FROM 
				simpro_rat_analisa
			INNER JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa.detail_material_kode
			WHERE simpro_rat_analisa.id_proyek_rat = '%d'
			GROUP BY 
				simpro_rat_analisa.detail_material_kode, 
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				simpro_rat_analisa.id_proyek_rat,				
				simpro_rat_analisa.aketerangan,
				simpro_rat_analisa.kode_rap,
				simpro_rat_analisa.aharga,
				simpro_rat_analisa.avolume,
				simpro_rat_analisa.akoefisien
			ORDER BY simpro_rat_analisa.detail_material_kode ASC
		", $idproyek);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			$i=0;
			$char = "";
			$rows = $rs->result_array();
			foreach($rs->result_array() as $res_arr)
			{
				$seq = $i+1;
				if($res_arr['kode_rap'] == '')
				{
					switch(strlen($seq))
					{
						case 1: $char = "A00". $seq; break;
						case 2: $char = "A0". $seq; break;
						case 3: $char = "A". $seq; break;					
					}
					$koderap = array("kode_rap"=>$char);
					$data[] = array_merge($rows[$i], $koderap);
				} else {
					$data[] = array_merge($rows[$i], array("kode_rap"=>$res_arr['kode_rap']));
				}
				$i++;
			}
			return array('total'=>$totdata, 'data'=> $data);
		} else return false;
	}

	function rat_rab_analisa_satuan_pekerjaan($idproyek)
	{
		/*	
		simpro_rat_rab_analisa.harga_rab,		
		COALESCE(simpro_rat_rab_analisa.harga_rab, simpro_rat_analisa.aharga) AS harga_rab,
		*/

		$query = sprintf("
			SELECT 
				simpro_rat_analisa.id_simpro_rat_analisa,
				simpro_rat_analisa.detail_material_kode,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				COUNT(simpro_rat_analisa.detail_material_kode) as totitem,
				simpro_rat_analisa.id_proyek_rat,				
				simpro_rat_analisa.aketerangan,
				simpro_rat_analisa.kode_rap,
				simpro_rat_analisa.aharga,
				simpro_rat_analisa.avolume,
				simpro_rat_analisa.akoefisien,
				(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) AS subtotal,
				simpro_rat_rab_analisa.harga_rab,
				COALESCE(simpro_rat_rab_analisa.koefisien_rab, '1') AS koefisien_rab,
				(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) AS subtotal_rab,
				((simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) * COUNT(simpro_rat_analisa.detail_material_kode)) AS total_rab,
				((simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) * COUNT(simpro_rat_analisa.detail_material_kode)) AS total
			FROM 
				simpro_rat_analisa
			INNER JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa.detail_material_kode
			LEFT JOIN simpro_rat_rab_analisa ON simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa
			WHERE simpro_rat_analisa.id_proyek_rat = '%d'
			GROUP BY 
				simpro_rat_analisa.id_simpro_rat_analisa,
				simpro_rat_analisa.detail_material_kode, 
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				simpro_rat_analisa.id_proyek_rat,				
				simpro_rat_analisa.aketerangan,
				simpro_rat_analisa.kode_rap,
				simpro_rat_analisa.aharga,
				simpro_rat_analisa.avolume,
				simpro_rat_analisa.akoefisien,
				simpro_rat_rab_analisa.id_simpro_rat_analisa,
				simpro_rat_rab_analisa.harga_rab,
				simpro_rat_rab_analisa.koefisien_rab				
			ORDER BY simpro_rat_analisa.detail_material_kode ASC
		", $idproyek);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			$i=0;
			$char = "";
			$rows = $rs->result_array();
			foreach($rs->result_array() as $res_arr)
			{
				$seq = $i+1;
				if($res_arr['kode_rap'] == '')
				{
					switch(strlen($seq))
					{
						case 1: $char = "A00". $seq; break;
						case 2: $char = "A0". $seq; break;
						case 3: $char = "A". $seq; break;					
					}
					$koderap = array("kode_rap"=>$char);
					$data[] = array_merge($rows[$i], $koderap);
				} else {
					$data[] = array_merge($rows[$i], array("kode_rap"=>$res_arr['kode_rap']));
				}								
				$i++;
			}
			return array('total'=>$totdata, 'data'=> $data);
		} else return false;
	}
	
	function data_indirect_cost($idproyek)
	{
		$query = sprintf("
 			SELECT 
				simpro_t_rat_indirect_cost.*,
				simpro_m_kat_rat.kat_rat,
				UPPER(
				CASE 
					WHEN simpro_m_harga_satuan.uraian IS NULL 
						THEN simpro_t_rat_indirect_cost.icitem 
					ELSE simpro_m_harga_satuan.uraian
				END) as uraian,					
				simpro_m_harga_satuan.kode,
				simpro_m_harga_satuan.satuan,
				simpro_t_rat_indirect_cost.icharga,
				(simpro_t_rat_indirect_cost.icharga * simpro_t_rat_indirect_cost.icvolume) as subtotal
			FROM 
				simpro_t_rat_indirect_cost
			LEFT JOIN simpro_m_harga_satuan ON simpro_m_harga_satuan.id_satuan_pekerjaan = simpro_t_rat_indirect_cost.id_satuan_pekerjaan
			LEFT JOIN simpro_m_kat_rat ON simpro_m_kat_rat.id_kat_rat = simpro_t_rat_indirect_cost.id_kat_rat
			WHERE 
				simpro_t_rat_indirect_cost.id_proyek_rat = '%d'
			ORDER BY simpro_m_kat_rat.id_kat_rat ASC
		", $idproyek);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;
	}
	
	function data_indirect_cost_group($idproyek, $gid)
	{
		$query = sprintf("
			SELECT 
				simpro_t_rat_indirect_cost.*, 
				UPPER(
					CASE
					WHEN simpro_m_harga_satuan.uraian IS NULL 
					THEN simpro_t_rat_indirect_cost.icitem 
					ELSE simpro_m_harga_satuan.uraian
					END
				) as uraian,
				simpro_m_kat_rat.kat_rat,
				(simpro_t_rat_indirect_cost.icharga * simpro_t_rat_indirect_cost.icvolume) as subtotal
			FROM 
			simpro_t_rat_indirect_cost
			INNER JOIN simpro_m_kat_rat on simpro_m_kat_rat.id_kat_rat = simpro_t_rat_indirect_cost.id_kat_rat
			LEFT JOIN simpro_m_harga_satuan ON simpro_m_harga_satuan.id_satuan_pekerjaan = simpro_t_rat_indirect_cost.id_satuan_pekerjaan
			WHERE simpro_t_rat_indirect_cost.id_proyek_rat = '%d'
			AND simpro_m_kat_rat.id_kat_rat = '%d'	
			ORDER BY simpro_m_kat_rat.id_kat_rat ASC	
		", $idproyek, $gid);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return $rs->result_array();
		} else return false;
	}
	
	function total_indirect_cost($idproyek)
	{
		$query = sprintf("
			SELECT 
				SUM(icharga * icvolume) as total_idc
			FROM 
				simpro_t_rat_indirect_cost
			WHERE 
				simpro_t_rat_indirect_cost.id_proyek_rat = '%d'
			GROUP BY id_proyek_rat 
		", $idproyek);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return $rs->row_array();
		} else return false;
	}
	
	function get_varcost_item($idproyek)
	{
		$query = sprintf("
			SELECT 
				simpro_t_rat_varcost.*, 
				simpro_rat_m_varcost.item as vitem
			FROM simpro_t_rat_varcost 
			LEFT JOIN simpro_rat_m_varcost ON simpro_rat_m_varcost.id_rat_varcost = simpro_t_rat_varcost.id_varcost_item
			WHERE id_proyek_rat = '%d'			
			ORDER BY id_rat_varcost ASC
			", $idproyek);
		$rs = $this->db->query($query);
		$totaldata = $rs->num_rows();
		if($totaldata)
		{
			return array('total'=>$totaldata, 'success'=>true, 'data'=> $rs->result_array());
		} else return false;	
	}

	function hitung_varcost_item($idproyek)
	{
		$query = sprintf("
			SELECT 
				simpro_t_rat_varcost.id_rat_varcost,
				simpro_t_rat_varcost.id_varcost_item,
				(
				CASE simpro_t_rat_varcost.id_varcost_item
					WHEN 1 THEN 'biaya_resiko' 
					WHEN 2 THEN 'biaya_pemasaran' 
					WHEN 3 THEN 'pph' 
					WHEN 4 THEN 'lapek' 
					WHEN 5 THEN 'biaya_lain'
				END
				) as uraian,
			simpro_t_rat_varcost.persentase,
			simpro_rat_m_varcost.item
			FROM simpro_t_rat_varcost 
			INNER JOIN simpro_m_rat_proyek_tender ON simpro_m_rat_proyek_tender.id_proyek_rat = simpro_t_rat_varcost.id_proyek_rat
			INNER JOIN simpro_rat_m_varcost ON simpro_rat_m_varcost.id_rat_varcost = simpro_t_rat_varcost.id_varcost_item
			WHERE simpro_t_rat_varcost.id_proyek_rat = '%d'				
		", 
			$idproyek);
					//WHEN 5 THEN 'contingency'
		$rs = $this->db->query($query);
		$totaldata = $rs->num_rows();
		$data = array();
		if($totaldata)
		{
			$row = $rs->result_array();
			foreach($row as $rk=>$rv)
			{				
				$data = array_merge($data, array($rv['uraian'] => $rv['persentase']));
			}			
			return array('total'=>$totaldata, 'success'=>true, 'data'=> $data);
		} else return false;	
	}
	
	function total_varcost($idproyek)
	{
		$nilai_kontrak = $this->hitung_nilai_kontrak($idproyek);
		$varcost = $this->hitung_varcost_item($idproyek);
		$biaya_resiko = ($varcost['data']['biaya_resiko'] * $nilai_kontrak ) / 100;
		// $contingency = ($varcost['data']['contingency'] * $nilai_kontrak ) / 100;
		$lapek = ($varcost['data']['lapek'] * $nilai_kontrak ) / 100;
		$biaya_pemasaran = ($varcost['data']['biaya_pemasaran'] * $nilai_kontrak ) / 100;
		$biaya_lain = ($varcost['data']['biaya_lain'] * $nilai_kontrak ) / 100;
		$pph = ($varcost['data']['pph'] * $nilai_kontrak ) / 100;
		$total_vc = $biaya_resiko + $lapek + $pph + $biaya_pemasaran + $biaya_lain; //$contingency + 
		if($total_vc > 0)
		{
			return array('total'=>1, 'success'=>true, 'data'=>array('total_vc' => $total_vc));
		} else return false;	
	}
	
	function del_item_indirect_cost($id)
	{
		if($res = $this->db->query(sprintf("DELETE FROM simpro_t_rat_indirect_cost WHERE id_rat_indirect_cost = '%d'", $id)))
		{
			return true;
		} else return false;
	}
	
	function del_item_rat($id)
	{
		if($res = $this->db->query(sprintf("DELETE FROM simpro_rat_analisa WHERE id_simpro_rat_analisa = '%d'", $id)))
		{
			return true;
		} else return false;
	}
	
	function total_rat($idtender)
	{
		$sql = sprintf("
			SELECT SUM(subtotal) AS total FROM 
			(
				SELECT
					simpro_t_rat_direct_cost.*,
					simpro_m_kat_rat.kat_rat,
					simpro_m_type_rat.type_rat, 
					simpro_m_kategori_pekerjaan.nama_kategori,
					simpro_m_harga_satuan.uraian,
					simpro_m_harga_satuan.satuan,
					simpro_t_rat_direct_cost.harga as mharga,
					(simpro_t_rat_direct_cost.harga * simpro_t_rat_direct_cost.volume) as subtotal
				FROM 
					simpro_t_rat_direct_cost
				LEFT JOIN simpro_m_kategori_pekerjaan ON simpro_m_kategori_pekerjaan.id_kategori_pekerjaan = simpro_t_rat_direct_cost.id_kategori_pekerjaan
				LEFT JOIN simpro_m_harga_satuan ON simpro_m_harga_satuan.id_satuan_pekerjaan = simpro_t_rat_direct_cost.id_satuan_pekerjaan
				LEFT JOIN simpro_m_kat_rat ON simpro_m_kat_rat.id_kat_rat = simpro_t_rat_direct_cost.id_kat_rat
				LEFT JOIN simpro_m_type_rat ON simpro_m_type_rat.id_type_rat = simpro_t_rat_direct_cost.id_type_rat
				WHERE 
					simpro_t_rat_direct_cost.id_proyek_rat = '%d'
				ORDER BY simpro_t_rat_direct_cost.id_proyek_rat DESC
			) as tbl_total_rat", $idtender);
		$rs = $this->db->query($sql);
		$data = $rs->row_array();
		if($data['total'] > 0) return $data['total']; 
			else return false;
	}

	function get_tree_item_rab_new($idpro, $tree_parent=0,$param)
	{
		// var_dump($param);
		$query = 
			"
			with rab_subtotal as (
	SELECT DISTINCT(kode_tree), subtotal_rab,
(select count(kode_tree) from simpro_rat_item_tree where simpro_rat_item_tree.tree_parent_kode = tbl_harga_rab.kode_tree and simpro_rat_item_tree.id_proyek_rat = $idpro) as count_kode
 FROM (
					SELECT 
						simpro_rat_item_tree.kode_tree,
						SUM(harga_analisa.harga_rab * simpro_rat_rab_item_tree.volume) as subtotal_rab
					FROM 
					simpro_rat_item_tree
					LEFT JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.id_proyek_rat = simpro_rat_item_tree.id_proyek_rat AND simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN (
						SELECT 
							kode_analisa_rat,
							SUM(harga_rab * koefisien_rab) harga_rab
						FROM (
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa and simpro_rat_analisa_apek.id_tender = simpro_rat_rab_analisa.id_tender
							WHERE simpro_rat_rab_analisa.id_tender = $idpro
							AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
						)
						UNION ALL
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa and simpro_rat_analisa_asat.id_tender = simpro_rat_rab_analisa.id_tender
							WHERE simpro_rat_rab_analisa.id_tender = $idpro
						)
						) as tbl_rab_analisa
						GROUP BY kode_analisa_rat
					) as harga_analisa on harga_analisa.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
					WHERE simpro_rat_item_tree.id_proyek_rat = $idpro	
					GROUP BY simpro_rat_item_tree.kode_tree

				UNION ALL

					SELECT 
						simpro_rat_item_tree.tree_parent_kode,
						SUM(harga_analisa.harga_rab * simpro_rat_rab_item_tree.volume) as subtotal_rab
					FROM 
					simpro_rat_item_tree
					INNER JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.id_proyek_rat = simpro_rat_item_tree.id_proyek_rat AND simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN (
						SELECT 
							kode_analisa_rat,
							SUM(harga_rab * koefisien_rab) harga_rab
						FROM (
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $idpro
							AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
						)
						UNION ALL
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $idpro
						)
						) as tbl_rab_analisa
						GROUP BY kode_analisa_rat
					) as harga_analisa on harga_analisa.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
					WHERE simpro_rat_item_tree.id_proyek_rat = $idpro
				GROUP BY simpro_rat_item_tree.tree_parent_kode
				) as tbl_harga_rab 
				where (select count(kode_tree) from simpro_rat_item_tree where simpro_rat_item_tree.tree_parent_kode = tbl_harga_rab.kode_tree and simpro_rat_item_tree.id_proyek_rat = $idpro) = 0
				and subtotal_rab IS NOT NULL
				GROUP BY kode_tree, subtotal_rab
				order by kode_tree
),
rab_harga_analisa as (
SELECT 
				kode_analisa_rat,
				SUM(harga_rab * koefisien_rab) harga_jadi_rab
			FROM (
			(
				SELECT 
				simpro_rat_rab_analisa.*,
				simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
				FROM simpro_rat_rab_analisa
				INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa
				WHERE simpro_rat_rab_analisa.id_tender = $idpro
				AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
			)
			UNION ALL
			(
				SELECT 
				simpro_rat_rab_analisa.*,
				simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
				FROM simpro_rat_rab_analisa
				INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
				WHERE simpro_rat_rab_analisa.id_tender = $idpro
			)
			) as tbl_rab_analisa
			GROUP BY kode_analisa_rat
),
rab_koefisien as (
SELECT 
				(
					SELECT
						SUM(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) as subtotal
					FROM 
						simpro_rat_analisa join simpro_rat_item_tree on simpro_rat_analisa.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					WHERE 
						simpro_rat_analisa.id_proyek_rat = $idpro
					GROUP BY 
						simpro_rat_analisa.id_proyek_rat, 
						simpro_rat_analisa.rat_item_tree					
				) AS harga,				
				(
					SELECT
						SUM(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) as subtotal
					FROM 
						simpro_rat_analisa join simpro_rat_item_tree on simpro_rat_analisa.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					WHERE 
						simpro_rat_analisa.id_proyek_rat = $idpro
					GROUP BY 
						simpro_rat_analisa.id_proyek_rat, 
						simpro_rat_analisa.rat_item_tree									
				) * simpro_rat_item_tree.volume AS subtotal,
				(
					SELECT
						SUM(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) AS subtotal
					FROM 
						simpro_rat_rab_analisa
					INNER JOIN simpro_rat_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa and simpro_rat_rab_analisa.id_tender = simpro_rat_analisa.id_proyek_rat)						
					WHERE 
						simpro_rat_analisa.id_proyek_rat = $idpro AND 
						simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa
					GROUP BY 
						simpro_rat_analisa.rat_item_tree
				) AS harga_rab,				
				(
					SELECT
						SUM(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) AS subtotal_rab					
					FROM 
						simpro_rat_rab_analisa						
					INNER JOIN simpro_rat_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa and simpro_rat_rab_analisa.id_tender = simpro_rat_analisa.id_proyek_rat)
					WHERE 
						simpro_rat_analisa.id_proyek_rat = $idpro AND 
						simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa						
					GROUP BY 
						simpro_rat_analisa.rat_item_tree
				) * simpro_rat_rab_item_tree.volume AS subtotal_rab,
				simpro_rat_item_tree.volume AS volume,
				simpro_rat_rab_item_tree.volume AS volume_rab,
				simpro_rat_rab_item_tree.id_rat_rab_item_tree,
				simpro_rat_item_tree.rat_item_tree,
				CONCAT(simpro_rat_item_tree.kode_tree,' ',simpro_rat_item_tree.tree_item) AS task 
			FROM simpro_rat_item_tree 
			LEFT JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree			
			WHERE simpro_rat_item_tree.id_proyek_rat = $idpro 
)
select 
			case when
			(select count(kode_tree) 
				from (select * from simpro_rat_item_tree 
					where lower(tree_item) like lower('%".$param."%') 
					and id_proyek_rat = $idpro 
					and rat_item_tree = x.rat_item_tree) hj) > 0
			then
			1
			else
			0
			end as ktr,
			x.*, 
			coalesce((select volume_rab from rab_koefisien where rat_item_tree = x.rat_item_tree),1) as volume_rab,
			case when (select volume_rab from rab_koefisien where rat_item_tree = x.rat_item_tree) = 0 or sum(totals.subtotal_rab) = 0
			then 0
			else coalesce(sum(totals.subtotal_rab),0) / coalesce((select volume_rab from rab_koefisien where rat_item_tree = x.rat_item_tree),1)
			end as harga_rab,
			sum(totals.subtotal_rab) as subtotal_rab,
			coalesce(sum(totals.subtotal_rab),0) - (sum(totals.subtotal)) as selisih,
			(x.kode_tree || ' ' || x.tree_item) AS task,
			case when right(x.tree_parent_kode,1) = '.' then
			left(x.tree_parent_kode,(length(x.tree_parent_kode)-1))
			else
			x.tree_parent_kode
			end as xnm,
			(select a::int from (select a, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) as row from (select unnest as a from unnest ( string_to_array ( trim ( x.kode_tree, '.'), '.' ) )) x) r order by row desc limit 1) as urut,
			(select b.kode_analisa
			from simpro_rat_item_tree a
			join simpro_rat_analisa_item_apek b
			on a.kode_tree = b.kode_tree and a.id_proyek_rat = b.id_proyek_rat
			where a.id_proyek_rat = $idpro and a.kode_tree = x.kode_tree) as kode_analisa,
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
				coalesce((select subtotal_rab from rab_subtotal where kode_tree = simpro_rat_item_tree.kode_tree),0) as subtotal_rab,
				COALESCE(tbl_harga.harga, 0) AS harga,
				(COALESCE(tbl_harga.harga, 0) * simpro_rat_item_tree.volume) as subtotal
			FROM simpro_rat_item_tree 
			LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.kode_tree = simpro_rat_item_tree.kode_tree and simpro_rat_analisa_item_apek.id_proyek_rat = $idpro
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
					WHERE simpro_rat_analisa_asat.id_tender= $idpro
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
						WHERE id_tender= $idpro
						
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
					WHERE simpro_rat_analisa_apek.id_tender= $idpro
					
					ORDER BY 
						simpro_rat_analisa_apek.parent_kode_analisa,				
						simpro_rat_analisa_apek.kode_analisa
					ASC					
				)		
				) AS tbl_analisa_satuan
				GROUP BY kode_analisa				
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_item_apek.kode_analisa						
			WHERE simpro_rat_item_tree.id_proyek_rat = $idpro 
			ORDER BY simpro_rat_item_tree.kode_tree ASC) as totals
			on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
			WHERE x.id_proyek_rat = $idpro 
			and x.tree_parent_id = $tree_parent
			group by x.rat_item_tree
			ORDER BY xnm, urut
			";
		$rs = $this->db->query($query);	
		return $rs;
		
		// var_dump($query);
	}
	
	function get_tree_item($idpro, $tree_parent=0,$param="")
	{
		// var_dump($param);
		$query = 
			"
			select 
			case when
			(select count(kode_tree) 
				from (select * from simpro_rat_item_tree 
					where lower(tree_item) like lower('%".$param."%') 
					and id_proyek_rat = $idpro 
					and rat_item_tree = x.rat_item_tree) hj) > 0
			then
			1
			else
			0
			end as ktr,
			x.*, 
			(x.kode_tree || ' ' || x.tree_item) AS task,
			case when right(x.tree_parent_kode,1) = '.' then
			left(x.tree_parent_kode,(length(x.tree_parent_kode)-1))
			else
			x.tree_parent_kode
			end as xnm,
			(select a::int from (select a, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) as row from (select unnest as a from unnest ( string_to_array ( trim ( x.kode_tree, '.'), '.' ) )) x) r order by row desc limit 1) as urut,
			(select b.kode_analisa
			from simpro_rat_item_tree a
			join simpro_rat_analisa_item_apek b
			on a.kode_tree = b.kode_tree and a.id_proyek_rat = b.id_proyek_rat
			where a.id_proyek_rat = $idpro and a.kode_tree = x.kode_tree) as kode_analisa,
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
			LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.kode_tree = simpro_rat_item_tree.kode_tree and simpro_rat_analisa_item_apek.id_proyek_rat = $idpro
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
					WHERE simpro_rat_analisa_asat.id_tender= $idpro
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
						WHERE id_tender= $idpro
						
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
					WHERE simpro_rat_analisa_apek.id_tender= $idpro
					
					ORDER BY 
						simpro_rat_analisa_apek.parent_kode_analisa,				
						simpro_rat_analisa_apek.kode_analisa
					ASC					
				)		
				) AS tbl_analisa_satuan
				GROUP BY kode_analisa				
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_item_apek.kode_analisa						
			WHERE simpro_rat_item_tree.id_proyek_rat = $idpro 
			ORDER BY simpro_rat_item_tree.kode_tree ASC) as totals
			on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
			WHERE x.id_proyek_rat = $idpro 
			and x.tree_parent_id = $tree_parent
			group by x.rat_item_tree
			ORDER BY xnm, urut
			";
		$rs = $this->db->query($query);	
		return $rs;
		
		// var_dump($query);
	}

	function subtotal_rab($idpro, $kd_tree)
	{
		$query = "
				SELECT DISTINCT(kode_tree), subtotal_rab FROM (
					SELECT 
						simpro_rat_item_tree.kode_tree,
						SUM(harga_analisa.harga_rab * simpro_rat_rab_item_tree.volume) as subtotal_rab
					FROM 
					simpro_rat_item_tree
					LEFT JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.id_proyek_rat = simpro_rat_item_tree.id_proyek_rat AND simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN (
						SELECT 
							kode_analisa_rat,
							SUM(harga_rab * koefisien_rab) harga_rab
						FROM (
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $idpro
							AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
						)
						UNION ALL
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $idpro
						)
						) as tbl_rab_analisa
						GROUP BY kode_analisa_rat
					) as harga_analisa on harga_analisa.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
					WHERE simpro_rat_item_tree.id_proyek_rat = $idpro	
					GROUP BY simpro_rat_item_tree.kode_tree

				UNION ALL

					SELECT 
						simpro_rat_item_tree.tree_parent_kode,
						SUM(harga_analisa.harga_rab * simpro_rat_rab_item_tree.volume) as subtotal_rab
					FROM 
					simpro_rat_item_tree
					INNER JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.id_proyek_rat = simpro_rat_item_tree.id_proyek_rat AND simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN (
						SELECT 
							kode_analisa_rat,
							SUM(harga_rab * koefisien_rab) harga_rab
						FROM (
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $idpro
							AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
						)
						UNION ALL
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $idpro
						)
						) as tbl_rab_analisa
						GROUP BY kode_analisa_rat
					) as harga_analisa on harga_analisa.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
					WHERE simpro_rat_item_tree.id_proyek_rat = $idpro
				GROUP BY simpro_rat_item_tree.tree_parent_kode
				) as tbl_harga_rab 
				WHERE subtotal_rab IS NOT NULL
				AND kode_tree = '$kd_tree'
				GROUP BY kode_tree, subtotal_rab
		";
		if($this->db->query($query)->num_rows())
		{
			$val = $this->db->query($query)->row_array();
			return $val['subtotal_rab'];			
		}
	}
	
	function get_tree_item_harga($idpro, $tree_parent)
	{
		$query = sprintf(
			"
			SELECT 
				(
					SELECT
						SUM(aharga * akoefisien) as subtotal
					FROM 
						simpro_rat_analisa
					WHERE 
						id_proyek_rat = '%d' AND 
						rat_item_tree = '%d'
					GROUP BY 
						id_proyek_rat, rat_item_tree						
				) AS harga,				
				(
					SELECT
						SUM(aharga * akoefisien) as subtotal
					FROM 
						simpro_rat_analisa
					WHERE 
						id_proyek_rat = '%d' AND 
						rat_item_tree = '%d'
					GROUP BY 
						id_proyek_rat, rat_item_tree						
				) * volume AS subtotal,
				CONCAT(kode_tree || ' ' || tree_item) AS task 
			FROM simpro_rat_item_tree 
			WHERE id_proyek_rat = '%d' 
			AND rat_item_tree = '%d'", 
			$idpro, $tree_parent,
			$idpro, $tree_parent,
			$idpro, $tree_parent
			);
		$rs = $this->db->query($query);	
		return $rs;
	}

	function get_tree_item_rab($idpro, $tree_parent=0)
	{
		$query = sprintf(
			"
			SELECT 
				(
					SELECT
						SUM(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) as subtotal
					FROM 
						simpro_rat_analisa
					WHERE 
						simpro_rat_analisa.id_proyek_rat = '%d' AND 
						simpro_rat_analisa.rat_item_tree = '%d'
					GROUP BY 
						simpro_rat_analisa.id_proyek_rat, 
						simpro_rat_analisa.rat_item_tree						
				) AS harga,				
				(
					SELECT
						SUM(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) as subtotal
					FROM 
						simpro_rat_analisa
					WHERE 
						simpro_rat_analisa.id_proyek_rat = '%d' AND 
						simpro_rat_analisa.rat_item_tree = '%d'
					GROUP BY 
						simpro_rat_analisa.id_proyek_rat, 
						simpro_rat_analisa.rat_item_tree									
				) * simpro_rat_item_tree.volume AS subtotal,
				(
					SELECT
						SUM(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) AS subtotal
					FROM 
						simpro_rat_rab_analisa
					INNER JOIN simpro_rat_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa)						
					WHERE 
						simpro_rat_analisa.id_proyek_rat = '%d' AND 
						simpro_rat_analisa.rat_item_tree = '%d' AND
						simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa
					GROUP BY 
						simpro_rat_analisa.rat_item_tree
				) AS harga_rab,				
				(
					SELECT
						SUM(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) AS subtotal_rab					
					FROM 
						simpro_rat_rab_analisa						
					INNER JOIN simpro_rat_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa)
					WHERE 
						simpro_rat_analisa.id_proyek_rat = '%d' AND 
						simpro_rat_analisa.rat_item_tree = '%d' AND
						simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa						
					GROUP BY 
						simpro_rat_analisa.rat_item_tree
				) * simpro_rat_rab_item_tree.volume AS subtotal_rab,
				simpro_rat_item_tree.volume AS volume,
				simpro_rat_rab_item_tree.volume AS volume_rab,
				simpro_rat_rab_item_tree.id_rat_rab_item_tree,
				simpro_rat_item_tree.rat_item_tree,
				CONCAT(simpro_rat_item_tree.kode_tree,' ',simpro_rat_item_tree.tree_item) AS task 
			FROM simpro_rat_item_tree 
			LEFT JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree			
			WHERE simpro_rat_item_tree.id_proyek_rat = '%d' 
			AND simpro_rat_item_tree.rat_item_tree = '%d'
			", 
			$idpro, $tree_parent,
			$idpro, $tree_parent,
			$idpro, $tree_parent,
			$idpro, $tree_parent,
			$idpro, $tree_parent
			);
		$rs = $this->db->query($query);	
		return $rs;
	}
	
	function harga_satuan_kerja()
	{
		$query = sprintf("
			SELECT 
				id_satuan_pekerjaan, 
				UPPER(uraian) as uraian,
				mharga,
				UPPER(TRIM(CONCAT('[',kode,']',' - ',uraian))) AS kode_satuan
			FROM 
				simpro_m_harga_satuan 
			");
		$data = $this->reqcari($query, array('kode', 'uraian'));
		if(count($data))
		{
			return $data;
		} else return false;
		
		/*
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;
		*/
	}

	function kategori_pekerjaan()
	{
		$query = sprintf("
			SELECT 
				id_kategori_pekerjaan,
				UPPER(nama_kategori) as nama_kategori
			FROM 
				simpro_m_kategori_pekerjaan
			");
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;
	}

	function status_tender()
	{
		$query = "
			SELECT 
				id_status_rat, UPPER(status) AS status
			FROM 
				simpro_m_status_rat_tender
			";
		return $this->_retdata($query);			
	}

	function kat_rat()
	{
		$query = "
			SELECT 
				id_kat_rat, UPPER(kat_rat) AS kategori
			FROM 
				simpro_m_kat_rat
			";
		return $this->_retdata($query);
	}

	function type_rat()
	{
		$query = "
			SELECT 
				id_type_rat, UPPER(type_rat) AS type_rat
			FROM 
				simpro_m_type_rat
			";
		return $this->_retdata($query);
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

	/*
	@$cari = array();
	*/
	public function reqcari($qry, $cari)
	{
		if(isset($_REQUEST['_dc']))
		{
			$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
			$limit = $_REQUEST['limit'];
			$rs = $this->db->query($qry);
			$total_data = $rs->num_rows();			
			if(!empty($_REQUEST['query']))
			{
				$tcari = count($cari);
				if($tcari > 0)
				{
					if(preg_match("/where/i", $qry)) 
					{					
						$newq = preg_split('/(\swhere\s)/i', trim(preg_replace('/\s+/',' ', strtolower($qry))), -1, PREG_SPLIT_NO_EMPTY);
						$qry = $newq[0] . " WHERE (";
						for($i=0; $i < $tcari; $i++)
						{
							if($i > 0) $qry .= " OR LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%' ";
								else $qry .= " LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%'";
						}					
						$qry .= " )";	
						$qry .= " AND ".$newq[1]." ";
					} else 
					{
						for($i=0; $i < $tcari; $i++)
						{
							if($i > 0) $qry .= " OR LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%' ";
								else $qry .= " WHERE LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%'";
						}
					}
				}
				$ress = $this->db->query($qry);				
				$ress_tot = $ress->num_rows();
			}
			$limits = sprintf('LIMIT %d OFFSET %d', $limit, $offset);
			$qry .= $limits;
			$res = $this->db->query($qry);
			$data = $res->result_array();
			$total_data = !empty($_REQUEST['query']) ? $ress_tot : $total_data;
			return array('total'=>$total_data, 'data'=>$data, '_dc'=>$_REQUEST['_dc']);
		}	
	}
	
	/* 
	# old query 
	public function reqcari($qry, $cari)
	{
		if(isset($_REQUEST['_dc']))
		{
			$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
			$limit = $_REQUEST['limit'];
			$rs = $this->db->query($qry);
			$total_data = $rs->num_rows();			
			if(!empty($_REQUEST['query']))
			{
				$tcari = count($cari);
				if($tcari > 0)
				{
					if(preg_match("/where/i", $qry)) 
					{
						$qry .= " AND (";
						for($i=0; $i < $tcari; $i++)
						{
							if($i > 0) $qry .= " OR LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%' ";
									else $qry .= " LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%'";
						}					
						$qry .= " )";					
					} else 
					{
						for($i=0; $i < $tcari; $i++)
						{
							if($i > 0) $qry .= " OR LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%' ";
									else $qry .= " WHERE LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%'";
						}
					}
				}
				$ress = $this->db->query($qry);				
				$ress_tot = $ress->num_rows();
			}
			$limits = sprintf('LIMIT %d OFFSET %d', $limit, $offset);
			$qry .= $limits;
			$res = $this->db->query($qry);
			$data = $res->result_array();
			$total_data = !empty($_REQUEST['query']) ? $ress_tot : $total_data;
			return array('total'=>$total_data, 'data'=>$data, '_dc'=>$_REQUEST['_dc']);
		}	
	}
	*/
	
	/*
	public function qsearch($req, $qry, $cari)
	{
		if(isset($req['_dc']))
		{
			$limit = $req['page'] * $req['start'];
			$max_limit = $req['limit'];
			$rs = $this->db->query($qry);
			$total_data = $rs->num_rows();			
			if(!empty($req['query']))
			{
				$tcari = count($cari);
				for($i=0; $i < $tcari; $i++)
				{
					if($i > 0) $qry .= " AND " . $cari[$i]." LIKE '%".$req['query']."%' ";
						else $qry .= " WHERE " . $cari[$i]." LIKE '%".$req['query']."%'";
				}
			}
			$limits = sprintf('LIMIT %d,%d', $limit, $max_limit);
			$qry .= $limits;
			$res = $this->db->query($qry);
			$data = $res->result_array();
			return array('total'=>$total_data, 'data'=>$data, '_dc'=>$req['_dc']);
		}	
	}
	*/
	
	function get_divisi()
	{
		$query = "
			SELECT 				
				divisi_id, divisi_kode, divisi_name, 
				CONCAT('[',divisi_kode,'] - ', divisi_name) as divisi
			FROM  simpro_tbl_divisi order by urut
			";
		return $this->_retdata($query);
	}	
	
	function hitung_biaya_konstruksi($idtender)
	{					
		$query = sprintf("
			SELECT
				SUM(simpro_rat_item_tree.volume * tbl_total_apek.subtotal) AS total_bk
			FROM  simpro_rat_item_tree
			INNER JOIN simpro_rat_analisa_item_apek ON (simpro_rat_analisa_item_apek.kode_tree = simpro_rat_item_tree.kode_tree AND simpro_rat_analisa_item_apek.id_proyek_rat = simpro_rat_item_tree.id_proyek_rat)
			JOIN (
				(
					SELECT 
						DISTINCT(kode_analisa), 
						SUM(harga * koefisien) as subtotal,
						id_tender
					FROM 
					simpro_rat_analisa_asat
					WHERE id_tender = '%d'
					GROUP BY kode_analisa,id_tender
					ORDER BY kode_analisa ASC
				)
				UNION ALL
				(
					SELECT 
						DISTINCT(simpro_rat_analisa_apek.parent_kode_analisa) as kode_analisa, 
						SUM(tbl_asat.harga * simpro_rat_analisa_apek.koefisien)  as subtotal,
						simpro_rat_analisa_apek.id_tender
					FROM simpro_rat_analisa_apek
					LEFT JOIN (
						SELECT 
						DISTINCT(kode_analisa), 
						SUM(harga * koefisien) as harga,
						id_tender
						FROM 
						simpro_rat_analisa_asat
						GROUP BY kode_analisa,id_tender
						ORDER BY kode_analisa ASC
					) tbl_asat ON tbl_asat.id_tender = simpro_rat_analisa_apek.id_tender AND tbl_asat.kode_analisa = simpro_rat_analisa_apek.kode_analisa
					WHERE simpro_rat_analisa_apek.id_tender = '%d'
					GROUP BY simpro_rat_analisa_apek.parent_kode_analisa, simpro_rat_analisa_apek.id_tender
				)
			) as tbl_total_apek ON (tbl_total_apek.id_tender = simpro_rat_analisa_item_apek.id_proyek_rat AND tbl_total_apek.kode_analisa = simpro_rat_analisa_item_apek.kode_analisa)
			WHERE simpro_rat_item_tree.id_proyek_rat = '%d'
			GROUP BY simpro_rat_item_tree.id_proyek_rat			
		", $idtender, $idtender, $idtender);
		
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return $rs->row_array();
		} else return false;
	}
	
	function get_subbidang()
	{
		$query = "
			SELECT 
				subbidang_kode, subbidang_name,
				CONCAT('[',subbidang_kode,'] ', subbidang_name) as kd_bidang
			FROM simpro_tbl_subbidang 
			ORDER BY simpro_tbl_subbidang ASC		
			";
		return $this->_retdata($query);
	}

	function get_detailmaterial_kode($kode)
	{
		$query = "
			SELECT *
			FROM simpro_tbl_detail_material 
			WHERE simpro_tbl_subbidang LIKE '%".strtolower(addslashes($kode))."%' 
			OR detail_material_nama LIKE '%".strtolower(addslashes($kode))."%'
			OR detail_material_kode LIKE '%".strtolower(addslashes($kode))."%'
			";
		$data = $this->reqcari($query, array('detail_material_kode', 'detail_material_nama'));
		if(count($data))
		{
			return $data;
		} else return false;
	}	

	function get_detailmaterial_kode_asli($kode)
	{
		$query = sprintf("
			SELECT *
			FROM simpro_tbl_detail_material 
			WHERE simpro_tbl_subbidang LIKE '%s' 
			", $kode);
		$data = $this->reqcari($query, array('detail_material_kode', 'detail_material_nama'));
		if(count($data))
		{
			return $data;
		} else return false;
	}	
	
	function get_ansat($kode)
	{
		$query = "
				SELECT 
					simpro_tbl_detail_material.*,
					simpro_tbl_subbidang.subbidang_name as kategori,
					'1' as koefisien
				FROM simpro_tbl_detail_material 
				INNER JOIN simpro_tbl_subbidang ON simpro_tbl_subbidang.subbidang_kode = simpro_tbl_detail_material.subbidang_kode
				";
		$qwhere = is_numeric($kode) ? sprintf("WHERE LEFT(simpro_tbl_detail_material.subbidang_kode,3) = '%s'", addslashes($kode)) : "";
		$query .= $qwhere;
		$data = $this->reqcari($query, array(
			'simpro_tbl_detail_material.subbidang_kode', 
			'simpro_tbl_detail_material.detail_material_kode', 
			'simpro_tbl_detail_material.detail_material_nama')
			);		
		if(count($data))
		{
			return $data;
		} else return false;
	}	
	
	function get_data_bank($idtender)
	{
		$query = sprintf("
			SELECT 
				simpro_t_rat_idc_bank.*, 
				simpro_tbl_satuan.satuan_nama
			FROM simpro_t_rat_idc_bank 
			INNER JOIN simpro_tbl_satuan ON simpro_t_rat_idc_bank.id_satuan = simpro_tbl_satuan.satuan_id
			WHERE 
				simpro_t_rat_idc_bank.id_proyek_rat = '%d'
			ORDER BY simpro_t_rat_idc_bank.id_rat_idc_bank DESC
			", $idtender);
		;
		return $this->_retdata($query);	
	}

	function get_data_rat_biayaumum($idtender)
	{
		$query = sprintf("
			SELECT 
				simpro_t_rat_idc_biaya_umum.*,
				(simpro_t_rat_idc_biaya_umum.icvolume * simpro_t_rat_idc_biaya_umum.icharga) AS subtotal,
				simpro_tbl_satuan.satuan_nama
			FROM simpro_t_rat_idc_biaya_umum 
			INNER JOIN simpro_tbl_satuan ON simpro_t_rat_idc_biaya_umum.satuan_id = simpro_tbl_satuan.satuan_id
			WHERE 
				simpro_t_rat_idc_biaya_umum.id_proyek_rat = '%d'
			ORDER BY simpro_t_rat_idc_biaya_umum.id_rat_biaya_umum DESC
			", $idtender);
		;
		return $this->_retdata($query);	
	}
	
	function get_data_rat_asuransi($idtender)
	{
		$query = sprintf("
			SELECT 
				simpro_t_rat_idc_asuransi.*, 
				simpro_tbl_satuan.satuan_nama
			FROM simpro_t_rat_idc_asuransi 
			INNER JOIN simpro_tbl_satuan ON simpro_t_rat_idc_asuransi.id_satuan = simpro_tbl_satuan.satuan_id
			WHERE 
				simpro_t_rat_idc_asuransi.id_proyek_rat = '%d'
			ORDER BY id_rat_idc_asuransi DESC
			", $idtender);
		;
		return $this->_retdata($query);	
	}
	
	function get_tbl_satuan()
	{
		$query = "SELECT satuan_nama FROM simpro_tbl_satuan ORDER BY satuan_nama ASC";
		return $this->_retdata($query);	
	}
	
	function get_data_sketsa_proyek($id)
	{
		$query = sprintf("SELECT * FROM simpro_rat_sketsa_proyek WHERE id_proyek_rat = '%d'", $id);
		return $this->_retdata($query);			
	}

	function get_data_dokumen_tender($id)
	{
		$query = sprintf("SELECT * FROM simpro_rat_dokumen_proyek WHERE id_proyek_rat = '%d'", $id);
		return $this->_retdata($query);			
	}
	
	function hitung_total_biaya_umum($id)
	{
		$query = sprintf("
			SELECT 
				SUM(simpro_t_rat_idc_biaya_umum.icvolume * simpro_t_rat_idc_biaya_umum.icharga) AS total
			FROM simpro_t_rat_idc_biaya_umum 
			INNER JOIN simpro_tbl_satuan ON simpro_t_rat_idc_biaya_umum.satuan_id = simpro_tbl_satuan.satuan_id
			WHERE 
				simpro_t_rat_idc_biaya_umum.id_proyek_rat = '%d'
			GROUP BY simpro_t_rat_idc_biaya_umum.id_proyek_rat 		
		", $id);
		$res = $this->db->query($query)->row_array();
		if($this->db->query($query)->num_rows() > 0) return $res['total'];
			else return false;
	}
	
	function hitung_nilai_kontrak($idtender)
	{
		/* 
		nilai kontrak = (biaya konstruksi + biaya umum)/(100-bank-asuransi-variablecost-pphlapek) * 100			
		*/
		$biaya_bk = $this->hitung_biaya_konstruksi($idtender); 
		$total_bk = $biaya_bk['total_bk'];
		$total_biaya_umum = $this->hitung_total_biaya_umum($idtender);
		$varcost = $this->hitung_varcost_item($idtender);
		$bank = $this->get_data_bank($idtender);
		$persen_bank = 0;
		foreach($bank['data'] as $bd)
		{
			$persen_bank = $persen_bank + $bd['persentase'];
		}
		$vc = (100 - $persen_bank - $varcost['data']['biaya_resiko'] - $varcost['data']['lapek'] - $varcost['data']['pph'] - $varcost['data']['biaya_pemasaran'] - $varcost['data']['biaya_lain']); // $varcost['data']['contingency'] -
		$nilai_kontrak = (($total_bk + $total_biaya_umum) / $vc ) * 100;
		$nilai_kontrak_ppn = ((($total_bk + $total_biaya_umum) / $vc ) * 100) * 1.1;		
		if($nilai_kontrak > 0) return $nilai_kontrak;
			else return false;
	}

	function data_rat_rab($div,$idtender=FALSE)
	{
		/*
		simpro_m_rat_proyek_tender.id_status_rat = '%d'		
		*/
		
			
		if ($div == 6 || $div == 21) {
			if($idtender) $q_add = " where simpro_m_rat_proyek_tender.id_proyek_rat = '".$idtender."' ";
			else $q_add = "";

			$query = sprintf("
				SELECT 
					simpro_m_rat_proyek_tender.*, 
					simpro_m_status_rat_tender.status, 
					simpro_tbl_divisi.divisi_name as divisi
				FROM simpro_m_rat_proyek_tender 
				INNER JOIN simpro_m_status_rat_tender ON simpro_m_status_rat_tender.id_status_rat = simpro_m_rat_proyek_tender.id_status_rat
				INNER JOIN simpro_tbl_divisi ON simpro_tbl_divisi.divisi_id = simpro_m_rat_proyek_tender.divisi_id
					".$q_add."
				ORDER BY 
					simpro_m_rat_proyek_tender.id_proyek_rat DESC 			
				", $div);
		} else {
			if($idtender) $q_add = " AND simpro_m_rat_proyek_tender.id_proyek_rat = '".$idtender."' ";
			else $q_add = "";

			$query = sprintf("
				SELECT 
					simpro_m_rat_proyek_tender.*, 
					simpro_m_status_rat_tender.status, 
					simpro_tbl_divisi.divisi_name as divisi
				FROM simpro_m_rat_proyek_tender 
				INNER JOIN simpro_m_status_rat_tender ON simpro_m_status_rat_tender.id_status_rat = simpro_m_rat_proyek_tender.id_status_rat
				INNER JOIN simpro_tbl_divisi ON simpro_tbl_divisi.divisi_id = simpro_m_rat_proyek_tender.divisi_id
				WHERE 
					simpro_m_rat_proyek_tender.divisi_id = '%d'
					".$q_add."
				ORDER BY 
					simpro_m_rat_proyek_tender.id_proyek_rat DESC 			
				", $div);			
		}
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;
	}
	
	function harga_analisa_rab($idtender, $kd_analisa)
	{
		$sql = sprintf("
			SELECT 
				kode_analisa_rat,
				SUM(harga_rab * koefisien_rab) harga_jadi_rab
			FROM (
			(
				SELECT 
				simpro_rat_rab_analisa.*,
				simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
				FROM simpro_rat_rab_analisa
				INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa
				WHERE simpro_rat_rab_analisa.id_tender = '%d'
				AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
			)
			UNION ALL
			(
				SELECT 
				simpro_rat_rab_analisa.*,
				simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
				FROM simpro_rat_rab_analisa
				INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
				WHERE simpro_rat_rab_analisa.id_tender = '%d'
			)
			) as tbl_rab_analisa
			WHERE kode_analisa_rat = '%s'
			GROUP BY kode_analisa_rat
		", $idtender, $idtender, $kd_analisa);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata > 0)
		{
			return $rs->row_array();
			//return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;
	}
	
	function pilih_proyek($div_id)
	{
		if ($div_id == 21 || $div_id == 6) {
			$sql = sprintf("
				SELECT 
				proyek_id,
				proyek,
				lokasi_proyek,
				no_spk,
				mulai,
				berakhir,
				total_waktu_pelaksanaan,
				tgl_tender
				FROM simpro_tbl_proyek
				ORDER BY mulai DESC
				", $div_id);
		} else {
			$sql = sprintf("
				SELECT 
				proyek_id,
				proyek,
				lokasi_proyek,
				no_spk,
				mulai,
				berakhir,
				total_waktu_pelaksanaan,
				tgl_tender
				FROM simpro_tbl_proyek
				WHERE divisi_kode = '%d'
				ORDER BY mulai DESC
				", $div_id);
		}
		
		$tot = $this->db->query($sql)->num_rows();
		if($tot > 0)
		{
			$data = $this->reqcari($sql, array('proyek', 'no_spk', 'lokasi_proyek'));			
			return $data;
		} else return false;
	}

	function pilih_proyek_pilih($div_id)
	{
			$sql = sprintf("
				SELECT 
					simpro_m_rat_proyek_tender.*, 
					simpro_m_status_rat_tender.status, 
					simpro_tbl_divisi.divisi_name as divisi,
					simpro_tbl_divisi.divisi_id as divisi_k
				FROM simpro_m_rat_proyek_tender 
				INNER JOIN simpro_m_status_rat_tender ON simpro_m_status_rat_tender.id_status_rat = simpro_m_rat_proyek_tender.id_status_rat
				INNER JOIN simpro_tbl_divisi ON simpro_tbl_divisi.divisi_id = simpro_m_rat_proyek_tender.divisi_id
				WHERE simpro_m_rat_proyek_tender.divisi_id = '%d'
				ORDER BY simpro_m_rat_proyek_tender.id_proyek_rat DESC
				", $div_id);
		
		$tot = $this->db->query($sql)->num_rows();
		if($tot > 0)
		{
			$data = $this->reqcari($sql, array('proyek', 'no_spk', 'lokasi_proyek'));			
			return $data;
		} else return false;
	}
	
	function dump($data)
	{
		print('<pre>');
		print_r($data);
		print('</pre>');
	}
	
}