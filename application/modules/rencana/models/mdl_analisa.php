<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_Analisa extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->db->query("SET CLIENT_ENCODING TO 'UTF8'");
	}
	
	function get_daftar_analisa_bahan($idtender)
	{
		$query = sprintf("
			SELECT 
				simpro_rat_analisa_daftar.*, 
				simpro_rat_analisa_kategori.kat_name AS kategori,
				simpro_tbl_satuan.satuan_nama AS satuan
			FROM 
			simpro_rat_analisa_daftar
			LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
			LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.id_kat_analisa = simpro_rat_analisa_daftar.id_kat_analisa
			WHERE simpro_rat_analisa_daftar.id_tender = '%d'
			AND simpro_rat_analisa_daftar.id_kat_analisa IN (1,2,3,4)
			ORDER BY 
				simpro_rat_analisa_daftar.kode_analisa
			ASC
		", $idtender);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;
	}
		
	function get_daftar_analisa_pekerjaan($idtender, $idkat=FALSE)
	{				
		if($idkat)
		{
			$q_idkat = sprintf("AND simpro_rat_analisa_daftar.id_kat_analisa = '%d'", $idkat);
		} else $q_idkat = "";
		$query = "
			SELECT 
				simpro_rat_analisa_daftar.*, 
				(simpro_rat_analisa_kategori.kode_kat || '. ' ||simpro_rat_analisa_kategori.kat_name) AS kategori,				
				simpro_rat_analisa_kategori.kat_name AS nama_kategori,				
				simpro_tbl_satuan.satuan_nama AS satuan,
				COALESCE(tbl_harga.harga, 0) + COALESCE(tbl_harga_apek.harga, 0) AS harga_satuan,
				coalesce(tbl_harga.c,0) as c_asat,
				coalesce(tbl_harga_apek.c,0) as c_apek
			FROM 
			simpro_rat_analisa_daftar
			LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
			LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.id_kat_analisa = simpro_rat_analisa_daftar.id_kat_analisa			
			LEFT JOIN (
				SELECT 
					DISTINCT ON(kode_analisa)
					kode_analisa,
					SUM(harga * koefisien) AS harga,
					coalesce((count('a')),0) as c
				FROM simpro_rat_analisa_asat 
				WHERE id_tender = '".$idtender."'
				GROUP BY kode_analisa			
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_daftar.kode_analisa		
			LEFT JOIN 
			(
				SELECT 
					DISTINCT ON(simpro_rat_analisa_apek.parent_kode_analisa)
					simpro_rat_analisa_apek.parent_kode_analisa,
					SUM(simpro_rat_analisa_apek.koefisien * tbl_harga_asat.harga) as harga,
					coalesce((count('a')),0) as c
				FROM simpro_rat_analisa_apek
				INNER JOIN (
					SELECT 
						DISTINCT ON(kode_analisa)
						kode_analisa,
						SUM(harga * koefisien) AS harga
					FROM simpro_rat_analisa_asat 
					WHERE id_tender = '".$idtender."'
					GROUP BY kode_analisa							
				) as tbl_harga_asat ON tbl_harga_asat.kode_analisa = simpro_rat_analisa_apek.kode_analisa
				WHERE simpro_rat_analisa_apek.id_tender = '".$idtender."'
				GROUP BY simpro_rat_analisa_apek.parent_kode_analisa			
			) as tbl_harga_apek ON tbl_harga_apek.parent_kode_analisa = simpro_rat_analisa_daftar.kode_analisa			
			WHERE 
				simpro_rat_analisa_daftar.id_tender = '".$idtender."'
				".$q_idkat."
		";
		/*
		$data = $this->reqcari($query, array('simpro_rat_analisa_daftar.nama_item', 'simpro_rat_analisa_daftar.kode_analisa'));
		if(count($data))
		{
			return $data;
		} else return false;
		*/
		
		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "AND (simpro_rat_analisa_daftar.nama_item @@ to_tsquery('".addslashes($search).":*') OR simpro_rat_analisa_daftar.kode_analisa @@ to_tsquery('".addslashes($search).":*'))";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		$qadd = "ORDER BY simpro_rat_analisa_daftar.kode_analisa ASC";
		$query .= $q_search . $qadd;
		
		$total_data = $this->db->query($query)->num_rows();
		
		$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
		$limit = $_REQUEST['limit'];		
		$limits = sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
		$query .= $limits;
		
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$total_data, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		} else return false;

	}
	
	function get_daftar_analisa_satuan($idtender)
	{	
		$this->update_harga_satuan($idtender);
		$query = sprintf("
				SELECT *
				FROM (
				(
					SELECT 					
						simpro_rat_analisa_asat.id_analisa_asat,
						simpro_rat_analisa_asat.kode_analisa,
						(simpro_rat_analisa_asat.kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						simpro_rat_analisa_daftar.nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_tbl_detail_material.detail_material_kode, 
						simpro_tbl_detail_material.detail_material_nama, 
						simpro_tbl_detail_material.detail_material_satuan,
						simpro_rat_analisa_asat.harga,
						simpro_rat_analisa_asat.koefisien,
						(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
					FROM 
						simpro_rat_analisa_asat
					LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
					JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_asat.id_tender)
					LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					WHERE simpro_rat_analisa_asat.id_tender = '%d'
					ORDER BY 
						simpro_rat_analisa_asat.kode_analisa,
						simpro_tbl_detail_material.detail_material_kode				
					ASC
				)
				UNION ALL 
				(
					SELECT 
						simpro_rat_analisa_apek.id_analisa_apek AS id_analisa_asat,
						simpro_rat_analisa_apek.kode_analisa,
						(simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						ad.nama_item AS nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_rat_analisa_apek.kode_analisa AS detail_material_kode,
						ad.nama_item as detail_material_nama,
						simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
						COALESCE(tbl_harga.harga,0) AS harga,
						simpro_rat_analisa_apek.koefisien,
						COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
					FROM 
						simpro_rat_analisa_apek
					INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
					INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_apek.id_tender)			
					INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					LEFT JOIN (
						SELECT 
							DISTINCT ON(kode_analisa)
							kode_analisa,
							SUM(harga * koefisien) AS harga
						FROM simpro_rat_analisa_asat 
						WHERE id_tender = '%d'
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
					WHERE simpro_rat_analisa_apek.id_tender = '%d'
					ORDER BY 
						simpro_rat_analisa_apek.parent_kode_analisa,				
						simpro_rat_analisa_apek.kode_analisa
					ASC					
				)		
				) AS tbl_analisa_satuan
			", $idtender,$idtender,$idtender);
			/*
					'tbl_detail_material.detail_material_nama', 
					'simpro_rat_analisa_asat.kode_analisa',
					'simpro_rat_analisa_asat.kode_material'			
			*/
		
		/*
		$search = array('detail_material_nama', 'kode_analisa', 'kode_material');
		$data = $this->reqcari($query, $search);
		if(count($data))
		{
			return $data;
		} else return false;
		*/
		
		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "WHERE (
				nama_item @@ to_tsquery('".addslashes($search).":*') 
				OR detail_material_nama @@ to_tsquery('".addslashes($search).":*') 
				OR kode_analisa @@ to_tsquery('".addslashes($search).":*')
				OR detail_material_kode @@ to_tsquery('".addslashes($search).":*')
			)";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		
		$q_order = "
			ORDER BY 
				kode_analisa
			ASC		
		";
		$query .= $q_search . $q_order;
		
		$total_data = $this->db->query($query)->num_rows();
		
		$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
		$limit = $_REQUEST['limit'];		
		$limits = sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
		$query .= $limits;
		
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$total_data, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		} else return false;		
	}

	function update_harga_satuan($id_tender)
	{
		$sql = "update simpro_rat_analisa_asat set
			harga = a.harga
			from (
			select * from simpro_rat_analisa_asat where id_tender = $id_tender and harga != 0
			) a
			where simpro_rat_analisa_asat.id_tender = $id_tender and simpro_rat_analisa_asat.harga = 0 and simpro_rat_analisa_asat.kode_material = a.kode_material";
		$this->db->query($sql);
	}

	function update_harga_analisa_rab($idtender)
	{
		$up_an = "update
				simpro_rat_rab_analisa
				set
				harga_rab = y.jumlah
				from
				(select
				a.*,c.*
				from 
				simpro_rat_rab_analisa a
				join simpro_rat_analisa_apek b
				on a.id_simpro_rat_analisa = b.id_analisa_apek
				join 
				(select
				d.id_tender,
				d.kode_analisa,
				sum(e.harga_rab * e.koefisien_rab) as jumlah
				from
				simpro_rat_analisa_asat d
				join simpro_rat_rab_analisa e
				on d.id_analisa_asat = e.id_simpro_rat_analisa
				where d.id_tender = $idtender
				group by d.kode_analisa,d.id_tender
				) c
				on b.kode_analisa = c.kode_analisa and c.id_tender = b.id_tender
				where a.id_tender = $idtender) y
				where simpro_rat_rab_analisa.id_rab_analisa = y.id_rab_analisa";

		$this->db->query($up_an);
		// $q1 = sprintf("
		// 	SELECT
		// 		DISTINCT(kode_analisa) AS kode_analisa, id_tender 
		// 	FROM 
		// 		simpro_rat_rab_analisa 
		// 	WHERE id_tender = %d AND LEFT(kode_analisa,2)='AN'		
		// ", $idtender);
		// $is_data = $this->db->query($q1)->num_rows();
		// if($is_data > 0)
		// {
		// 	$analisa = $this->db->query($q1)->result_array();
		// 	foreach($analisa as $ka)
		// 	{
				
		// 		$q2 = sprintf("
		// 			SELECT SUM(harga_rab) AS hargarab FROM simpro_rat_rab_analisa WHERE id_simpro_rat_analisa IN (
		// 				SELECT id_analisa_asat  from simpro_rat_analisa_asat
		// 				WHERE kode_analisa = '%s'
		// 				AND id_tender = %d
		// 			)
		// 			AND id_tender = %d
		// 			GROUP BY id_tender
		// 		", $ka['kode_analisa'], $ka['id_tender'], $ka['id_tender']);
		// 		$harga_baru = $this->db->query($q2)->row_array();
		// 		$upd = array('harga_rab' => $harga_baru['hargarab']);
		// 		$this->db->where('id_tender', $ka['id_tender']);
		// 		$this->db->where('kode_analisa', $ka['kode_analisa']);
		// 		$this->db->update('simpro_rat_rab_analisa', $upd);
		// 	}
		// }
	}
	
	function get_daftar_analisa_satuan_rab($idtender)
	{	
		$this->update_harga_analisa_rab($idtender);
		$query = sprintf("
			SELECT 
				tbl_analisa_satuan.*,
				simpro_rat_rab_analisa.id_rab_analisa,
				simpro_rat_rab_analisa.id_simpro_rat_analisa,
				simpro_rat_rab_analisa.nilai_pengali,
				simpro_rat_rab_analisa.koefisien_rab,
				simpro_rat_rab_analisa.koefisien_rat,
				simpro_rat_rab_analisa.harga_rat,
				simpro_rat_rab_analisa.harga_rab,
				COALESCE(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab, 0) as subtotal_rab				
			FROM (
				(
					SELECT 					
						simpro_rat_analisa_asat.id_analisa_asat,
						simpro_rat_analisa_asat.kode_analisa,
						simpro_rat_analisa_asat.id_tender,						
						(simpro_rat_analisa_asat.kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						simpro_rat_analisa_daftar.nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_tbl_detail_material.detail_material_kode, 
						simpro_tbl_detail_material.detail_material_nama, 
						simpro_tbl_detail_material.detail_material_satuan,
						simpro_rat_analisa_asat.harga,
						simpro_rat_analisa_asat.koefisien,
						(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
					FROM 
						simpro_rat_analisa_asat
					LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
					JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_asat.id_tender)
					LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					WHERE simpro_rat_analisa_asat.id_tender = '%d'
					ORDER BY 
						simpro_rat_analisa_asat.kode_analisa,
						simpro_tbl_detail_material.detail_material_kode				
					ASC
				)
				UNION ALL 
				(
					SELECT 
						simpro_rat_analisa_apek.id_analisa_apek AS id_analisa_asat,
						simpro_rat_analisa_apek.kode_analisa,
						simpro_rat_analisa_apek.id_tender,
						(simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						ad.nama_item AS nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_rat_analisa_apek.kode_analisa AS detail_material_kode,
						ad.nama_item as detail_material_nama,
						simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
						COALESCE(tbl_harga.harga,0) AS harga,
						simpro_rat_analisa_apek.koefisien,
						COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
					FROM 
						simpro_rat_analisa_apek
					INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
					INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_apek.id_tender)			
					INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					LEFT JOIN (
						SELECT 
							DISTINCT ON(kode_analisa)
							kode_analisa,
							SUM(harga * koefisien) AS harga
						FROM simpro_rat_analisa_asat 
						WHERE id_tender = '%d'
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
					WHERE simpro_rat_analisa_apek.id_tender = '%d'
					ORDER BY 
						simpro_rat_analisa_apek.parent_kode_analisa,				
						simpro_rat_analisa_apek.kode_analisa
					ASC					
				)		
			) as tbl_analisa_satuan 
			LEFT JOIN simpro_rat_rab_analisa ON simpro_rat_rab_analisa.id_simpro_rat_analisa = tbl_analisa_satuan.id_analisa_asat AND simpro_rat_rab_analisa.id_tender = tbl_analisa_satuan.id_tender
			", $idtender,$idtender,$idtender);
			// 			ORDER BY id_analisa_asat ASC		

		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "WHERE (
				tbl_analisa_satuan.nama_item @@ to_tsquery('".addslashes($search).":*') 
				OR tbl_analisa_satuan.detail_material_nama @@ to_tsquery('".addslashes($search).":*') 
				OR tbl_analisa_satuan.kode_analisa @@ to_tsquery('".addslashes($search).":*')
				OR tbl_analisa_satuan.detail_material_kode @@ to_tsquery('".addslashes($search).":*')
			)";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		
		//tbl_analisa_satuan.kode_analisa
		$q_order = "
			ORDER BY 
				id_analisa_asat
			ASC		
		";
		$query .= $q_search . $q_order;
		
		$total_data = $this->db->query($query)->num_rows();
		
		$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
		$limit = $_REQUEST['limit'];		
		$limits = sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
		$query .= $limits;
		
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$total_data, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		} else return false;		
	}
	
	function get_daftar_analisa_pekerjaan_koef($idtender,$idkat=FALSE)
	{
		if($idkat)
		{
			$q_idkat = sprintf("AND simpro_rat_analisa_daftar.id_kat_analisa = '%d'", $idkat);
		} else $q_idkat = "";
		$query = "
			SELECT 
				simpro_rat_analisa_daftar.*, 
				(simpro_rat_analisa_kategori.kode_kat || '. ' ||simpro_rat_analisa_kategori.kat_name) AS kategori,				
				simpro_rat_analisa_kategori.kat_name AS nama_kategori,				
				simpro_tbl_satuan.satuan_nama AS satuan,
				'1' AS koefisien
			FROM 
			simpro_rat_analisa_daftar
			LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
			LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.id_kat_analisa = simpro_rat_analisa_daftar.id_kat_analisa			
			WHERE 
				simpro_rat_analisa_daftar.id_tender = '".addslashes($idtender)."'
			".$q_idkat."
			ORDER BY 
				simpro_rat_analisa_daftar.kode_analisa
			ASC		
		";		
		$data = $this->reqcari($query, array('simpro_rat_analisa_daftar.nama_item', 'simpro_rat_analisa_daftar.kode_analisa'));
		if(count($data))
		{
			return $data;
		} else return false;		
	}

	function get_harga_satuan_asat($idtender)
	{
		// auto update kode RAP disini
		// $query = sprintf("
		// 	SELECT 
		// 		DISTINCT ON (simpro_rat_analisa_asat.kode_material)
		// 		simpro_rat_analisa_asat.kode_material,
		// 		tbl_detail_material.detail_material_nama,
		// 		tbl_detail_material.detail_material_satuan,
		// 		tbl_detail_material.detail_material_nama,
		// 		simpro_rat_analisa_asat.harga,
		// 		simpro_rat_analisa_asat.kode_rap,
		// 		simpro_rat_analisa_asat.keterangan,
		// 		subbidang.subbidang_name as kategori
		// 	FROM 
		// 		simpro_rat_analisa_asat
		// 	LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
		// 	INNER JOIN simpro_tbl_subbidang ON simpro_tbl_subbidang.subbidang_kode = LEFT(tbl_detail_material.detail_material_kode,3)
		// 	WHERE simpro_rat_analisa_asat.id_tender = '%d'
		// 	ORDER BY simpro_rat_analisa_asat.kode_material ASC		
		// ", $idtender);

		$query = sprintf("
			SELECT 
				simpro_rat_analisa_asat.kode_material,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				simpro_tbl_detail_material.detail_material_nama,
				avg(simpro_rat_analisa_asat.harga) as harga,
				simpro_rat_analisa_asat.kode_rap,
				simpro_rat_analisa_asat.keterangan,
				simpro_tbl_subbidang.subbidang_name as kategori
			FROM 
				simpro_rat_analisa_asat
			JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
			INNER JOIN simpro_tbl_subbidang ON simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3)
			WHERE simpro_rat_analisa_asat.id_tender = '%d'
			group by 
				simpro_rat_analisa_asat.kode_material,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_rat_analisa_asat.kode_rap,
				simpro_rat_analisa_asat.keterangan,
				kategori
			ORDER BY simpro_rat_analisa_asat.kode_material ASC	
		", $idtender);

		$data = $this->reqcari($query, array('simpro_rat_analisa_asat.kode_material', 'simpro_tbl_detail_material.detail_material_nama'));
		if(count($data))
		{
			return $data;
		} else return false;
	}

	function get_harga_satuan_asat_rab($idtender)
	{
		// auto update kode RAP disini
		$query = sprintf("
			SELECT 
				DISTINCT ON (simpro_rat_analisa_asat.kode_material)
				simpro_rat_analisa_asat.kode_material,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_rat_analisa_asat.harga,
				simpro_rat_analisa_asat.kode_rap,
				simpro_rat_analisa_asat.keterangan,
				simpro_tbl_subbidang.subbidang_name as kategori,
				pengali_rab.nilai_pengali,
				(simpro_rat_analisa_asat.harga * pengali_rab.nilai_pengali) AS harga_rab
			FROM 
				simpro_rat_analisa_asat
			LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
			INNER JOIN simpro_tbl_subbidang ON simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3)
			LEFT JOIN (
						SELECT 
							id_tender,
							kode_analisa,
							nilai_pengali 
						FROM 
							simpro_rat_rab_analisa 
						GROUP BY 
							id_tender, kode_analisa, nilai_pengali 
			) pengali_rab ON pengali_rab.kode_analisa = simpro_rat_analisa_asat.kode_material AND pengali_rab.id_tender = simpro_rat_analisa_asat.id_tender
			WHERE simpro_rat_analisa_asat.id_tender = '%d'
			ORDER BY simpro_rat_analisa_asat.kode_material ASC
		", $idtender);
		$data = $this->reqcari($query, array('simpro_rat_analisa_asat.kode_material', 'simpro_tbl_detail_material.detail_material_nama'));
		if(count($data))
		{
			return $data;
		} else return false;
	}
	
	function get_data_apek($idtender)
	{	
		// (simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item) AS apek_kat, 
		$query = sprintf("
			SELECT 
				simpro_rat_analisa_apek.*,
				(simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ') ') AS apek_kat, 				
				ad.nama_item AS nama_item,
				simpro_rat_analisa_daftar.nama_item AS parent_item,
				simpro_rat_analisa_daftar.id_satuan,
				simpro_tbl_satuan.satuan_nama,
				COALESCE(tbl_harga.harga,0) AS harga,
				COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
			FROM 
				simpro_rat_analisa_apek
			INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
			INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_apek.id_tender)						
			INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
			LEFT JOIN (
				SELECT 
					DISTINCT ON(kode_analisa)
					kode_analisa,
					SUM(harga * koefisien) AS harga
				FROM simpro_rat_analisa_asat 
				WHERE id_tender = '".$idtender."'
				GROUP BY kode_analisa			
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
			WHERE simpro_rat_analisa_apek.id_tender = '%d'
			", $idtender);
			
		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "AND (
				simpro_rat_analisa_daftar.nama_item @@ to_tsquery('".addslashes($search).":*') 
				OR simpro_rat_analisa_apek.kode_analisa @@ to_tsquery('".addslashes($search).":*')
				OR simpro_rat_analisa_apek.parent_kode_analisa @@ to_tsquery('".addslashes($search).":*')
				OR ad.nama_item @@ to_tsquery('".addslashes($search).":*')
			)";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		
		$q_order = "
			ORDER BY 
				simpro_rat_analisa_apek.parent_kode_analisa,				
				simpro_rat_analisa_apek.kode_analisa
			ASC		
		";
		$query .= $q_search . $q_order;
		
		$total_data = $this->db->query($query)->num_rows();
		
		$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
		$limit = $_REQUEST['limit'];		
		$limits = sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
		$query .= $limits;
		
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$total_data, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		} else return false;
			
		/*
		$search = array(
					'simpro_rat_analisa_daftar.nama_item',
					'simpro_rat_analisa_apek.kode_analisa',
					'simpro_rat_analisa_apek.parent_kode_analisa',
					'ad.nama_item'
				);
		$data = $this->reqcari($query, $search);
		if(count($data))
		{
			return $data;
		} else return false;
		*/
	}	
	
	function get_data_analisa_pekerjaan($idtender)
	{				
		$query = "
			SELECT 
				simpro_rat_analisa_daftar.*, 
				(simpro_rat_analisa_kategori.kode_kat || '. ' ||simpro_rat_analisa_kategori.kat_name) AS kategori,				
				simpro_rat_analisa_kategori.kat_name AS nama_kategori,				
				simpro_tbl_satuan.satuan_nama AS satuan,
				COALESCE(tbl_harga.harga, 0) + COALESCE(tbl_harga_apek.harga, 0) AS harga_satuan
			FROM 
			simpro_rat_analisa_daftar
			LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
			LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.id_kat_analisa = simpro_rat_analisa_daftar.id_kat_analisa			
			LEFT JOIN (
				SELECT 
					DISTINCT ON(kode_analisa)
					kode_analisa,
					SUM(harga * koefisien) AS harga
				FROM simpro_rat_analisa_asat 
				WHERE id_tender = '".$idtender."'
				GROUP BY kode_analisa			
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_daftar.kode_analisa
			LEFT JOIN 
			(
				SELECT 
					DISTINCT ON(simpro_rat_analisa_apek.parent_kode_analisa)
					simpro_rat_analisa_apek.parent_kode_analisa,
					SUM(simpro_rat_analisa_apek.koefisien * tbl_harga_asat.harga) as harga
				FROM simpro_rat_analisa_apek
				INNER JOIN (
					SELECT 
						DISTINCT ON(kode_analisa)
						kode_analisa,
						SUM(harga * koefisien) AS harga
					FROM simpro_rat_analisa_asat 
					WHERE id_tender = '".$idtender."'
					GROUP BY kode_analisa							
				) as tbl_harga_asat ON tbl_harga_asat.kode_analisa = simpro_rat_analisa_apek.kode_analisa
				WHERE simpro_rat_analisa_apek.id_tender = '".$idtender."'
				GROUP BY simpro_rat_analisa_apek.parent_kode_analisa			
			) as tbl_harga_apek ON tbl_harga_apek.parent_kode_analisa = simpro_rat_analisa_daftar.kode_analisa
			WHERE 
				simpro_rat_analisa_daftar.id_tender = '".$idtender."'
				AND simpro_rat_analisa_daftar.id_kat_analisa = '10'
		";

		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "AND (simpro_rat_analisa_daftar.nama_item @@ to_tsquery('".addslashes($search).":*') OR simpro_rat_analisa_daftar.kode_analisa @@ to_tsquery('".addslashes($search).":*'))";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		$qadd = "ORDER BY simpro_rat_analisa_daftar.kode_analisa ASC";
		$query .= $q_search . $qadd;
		
		$total_data = $this->db->query($query)->num_rows();
		
		$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
		$limit = $_REQUEST['limit'];		
		$limits = sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
		$query .= $limits;
		
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$total_data, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		} else return false;	
	}

	function get_data_analisa_pekerjaan_copy($idtender,$param,$search)
	{			

		if ($param == 'copy_proyek_lain') {
			$query = "
				SELECT 
					simpro_rat_analisa_daftar.*, 
					(simpro_rat_analisa_kategori.kode_kat || '. ' ||simpro_rat_analisa_kategori.kat_name) AS kategori,				
					simpro_rat_analisa_kategori.kat_name AS nama_kategori,				
					simpro_tbl_satuan.satuan_nama AS satuan,
					COALESCE(tbl_harga.harga, 0) + COALESCE(tbl_harga_apek.harga, 0) AS harga_satuan,
					case when tbl_harga_apek.parent_kode_analisa isnull 
					then 'ASAT'
					else 'APEK'
					end as jenis_analisa
				FROM 
				simpro_rat_analisa_daftar
				LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
				LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.id_kat_analisa = simpro_rat_analisa_daftar.id_kat_analisa			
				LEFT JOIN (
					SELECT 
						DISTINCT ON(kode_analisa)
						kode_analisa,
						SUM(harga * koefisien) AS harga
					FROM simpro_rat_analisa_asat 
					WHERE id_tender = '".$idtender."'
					GROUP BY kode_analisa			
				) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_daftar.kode_analisa
				LEFT JOIN 
				(
					SELECT 
						DISTINCT ON(simpro_rat_analisa_apek.parent_kode_analisa)
						simpro_rat_analisa_apek.parent_kode_analisa,
						SUM(simpro_rat_analisa_apek.koefisien * tbl_harga_asat.harga) as harga
					FROM simpro_rat_analisa_apek
					INNER JOIN (
						SELECT 
							DISTINCT ON(kode_analisa)
							kode_analisa,
							SUM(harga * koefisien) AS harga
						FROM simpro_rat_analisa_asat 
						WHERE id_tender = '".$idtender."'
						GROUP BY kode_analisa							
					) as tbl_harga_asat ON tbl_harga_asat.kode_analisa = simpro_rat_analisa_apek.kode_analisa
					WHERE simpro_rat_analisa_apek.id_tender = '".$idtender."'
					GROUP BY simpro_rat_analisa_apek.parent_kode_analisa
				) as tbl_harga_apek ON tbl_harga_apek.parent_kode_analisa = simpro_rat_analisa_daftar.kode_analisa
				WHERE 
					simpro_rat_analisa_daftar.id_tender = '".$idtender."'
					AND simpro_rat_analisa_daftar.id_kat_analisa = '10'
					and lower(simpro_rat_analisa_daftar.nama_item) like lower('%$search%')

			";

			// if(isset($_REQUEST['query']))
			// {
			// 	$search = $_REQUEST['query'];
			// 	$q_search = "AND (simpro_rat_analisa_daftar.nama_item @@ to_tsquery('".addslashes($search).":*') OR simpro_rat_analisa_daftar.kode_analisa @@ to_tsquery('".addslashes($search).":*'))";
			// } else
			// {
			// 	$search = "";
			// 	$q_search = "";			
			// }		

			$qadd = "ORDER BY simpro_rat_analisa_daftar.kode_analisa ASC";
			$query .=  $qadd; //$q_search .
		} else {
			$query = "select 
				a.id_data_analisa,
				a.kode_analisa,
				a.id_kat_analisa,
				a.nama_item,
				a.id_satuan,
				0 as id_tender,
				(c.kode_kat || '. ' ||c.kat_name) AS kategori,				
				c.kat_name AS nama_kategori,	
				d.satuan_nama as satuan,
				sum(b.harga * b.koefisien) as harga_satuan,
				'ASAT' as jenis_analisa
				from simpro_master_analisa_daftar a
				join simpro_master_analisa_asat b
				on a.id_data_analisa = b.id_data_analisa
				join simpro_rat_analisa_kategori c
				on a.id_kat_analisa = c.id_kat_analisa
				join simpro_tbl_satuan d
				on a.id_satuan = d.satuan_id
				where lower(a.nama_item) like lower('%$search%')
				group by 
				a.id_data_analisa,
				a.kode_analisa,
				a.id_kat_analisa,
				a.nama_item,
				a.id_satuan,
				kategori,
				nama_kategori,
				d.satuan_nama ORDER BY a.kode_analisa ASC";
		}
		
		$total_data = $this->db->query($query)->num_rows();
		
		$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
		$limit = $_REQUEST['limit'];		
		$limits = sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
		$query .= $limits;
		
		$rs = $this->db->query($query);
		foreach ($rs->result() as $row) {
			if ($row->jenis_analisa == 'APEK') {
				$q_kd = $this->db->query("select * from simpro_rat_analisa_apek where id_tender = $idtender and parent_kode_analisa = '$row->kode_analisa' order by kode_analisa");
				if ($q_kd->result()) {
					$kde = '';
					$xn = 0;
					foreach ($q_kd->result() as $rkd) {
						$kde = $kde.'<br>'.$rkd->kode_analisa;
						$kd = substr($kde, 4);
						$xn++;
					}
				}
			} else {
				$kd = '';
				$xn = 0;
			}
			$arr_data = array(
				'id_data_analisa' => $row->id_data_analisa, 
				'kode_analisa' => $row->kode_analisa, 
				'id_kat_analisa' => $row->id_kat_analisa, 
				'nama_item' => $row->nama_item, 
				'id_satuan' => $row->id_satuan, 
				'id_tender' => $row->id_tender, 
				'kategori' => $row->kategori, 
				'nama_kategori' => $row->nama_kategori, 
				'satuan' => $row->satuan, 
				'harga_satuan' => $row->harga_satuan, 
				'jenis_analisa' => $row->jenis_analisa, 
				'kd' => $kd,
				'xn' => $xn
			);

			$data[] = $arr_data;
		}
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$total_data, 'data'=>$data, '_dc'=>$_REQUEST['_dc']);
		} else return false;	
	}
	
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
	
	function hapus_analisa_tree($id_tree_rat)
	{
		$query = sprintf("
			DELETE FROM simpro_rat_analisa_asat WHERE kode_analisa = 
			(
				SELECT kode_analisa FROM simpro_rat_analisa_item_apek
				WHERE rat_item_tree = '%d'
			);

			DELETE FROM simpro_rat_analisa_apek where kode_analisa = 
			(
				SELECT kode_analisa FROM simpro_rat_analisa_item_apek
				WHERE rat_item_tree = '%d'
			);			
			
			DELETE FROM simpro_rat_analisa_item_apek WHERE rat_item_tree = '%d';						
		", $id_tree_rat, $id_tree_rat, $id_tree_rat);
		if($this->db->query($query)) return true;
			else return false;		
	}
	
	function rat_rata($idtender)
	{
		//tbl_total_koef.kode_rap					
		$sql = sprintf("		
				SELECT 
					DISTINCT(tbl_total_koef.kode_material) as kd_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					tbl_total_koef.kode_rap,
					avg(tbl_harga.harga) as harga,
					ROUND(SUM(tbl_total_koef.volume_total),4) as total_volume,
					(avg(tbl_harga.harga) * SUM(tbl_total_koef.volume_total)) as subtotal,
					tbl_total_koef.id_tender
				FROM (				
					SELECT 
					tbl_asat_apek.*,
					simpro_rat_analisa_item_apek.kode_tree,
					simpro_rat_item_tree.volume,
					(simpro_rat_item_tree.volume * tbl_asat_apek.tot_koef) as volume_total
					FROM
					(				
						(
						SELECT 
							DISTINCT(kode_material), 
							id_tender,
							COUNT(kode_material) * koefisien as tot_koef,
							kode_analisa,
							kode_analisa as parent_kode_analisa,
							kode_rap
						FROM 
						simpro_rat_analisa_asat
						WHERE id_tender = '%d'
						GROUP BY kode_material,kode_analisa,id_tender,koefisien,kode_rap
						ORDER BY kode_material ASC
						)
						UNION ALL
						(
							SELECT 
								DISTINCT(tbl_asat.kode_material) as kode_material,
								simpro_rat_analisa_apek.id_tender,
								(simpro_rat_analisa_apek.koefisien * SUM(tbl_asat.jml_material)) * tbl_asat.koefisien as tot_koef,
								simpro_rat_analisa_apek.kode_analisa,
								simpro_rat_analisa_apek.parent_kode_analisa,
								tbl_asat.kode_rap
							FROM simpro_rat_analisa_apek
							LEFT JOIN (
								SELECT 
									DISTINCT(kode_material), 
									COUNT(kode_material) as jml_material,
									koefisien,
									id_tender,
									kode_analisa,
									kode_rap
								FROM 
								simpro_rat_analisa_asat
								WHERE id_tender = '%d'
								GROUP BY kode_material,kode_analisa,id_tender,koefisien,kode_rap
								ORDER BY kode_material ASC
							) tbl_asat ON tbl_asat.id_tender = simpro_rat_analisa_apek.id_tender AND tbl_asat.kode_analisa = simpro_rat_analisa_apek.kode_analisa
							WHERE simpro_rat_analisa_apek.id_tender = '%d'
							GROUP BY  
							tbl_asat.kode_material,
							tbl_asat.koefisien,
							simpro_rat_analisa_apek.kode_analisa,
							simpro_rat_analisa_apek.parent_kode_analisa,						
							simpro_rat_analisa_apek.koefisien,
							simpro_rat_analisa_apek.id_tender,
							tbl_asat.kode_rap						
						)
					) as tbl_asat_apek
					INNER JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_rat_analisa_item_apek.id_proyek_rat = '%d'
					INNER JOIN simpro_rat_item_tree ON simpro_rat_item_tree.id_proyek_rat = simpro_rat_analisa_item_apek.id_proyek_rat AND simpro_rat_item_tree.kode_tree = simpro_rat_analisa_item_apek.kode_tree					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_material), 
					harga 
					FROM simpro_rat_analisa_asat
					WHERE id_tender = '%d'
					GROUP BY kode_material,harga
				) as tbl_harga ON (tbl_harga.kode_material = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_detail_material ON ( simpro_tbl_detail_material.detail_material_kode = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))
				GROUP BY 
					tbl_total_koef.kode_material,
					tbl_total_koef.kode_rap,
					tbl_total_koef.id_tender,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name		
				ORDER BY tbl_total_koef.kode_material ASC
			", $idtender, $idtender, $idtender, $idtender, $idtender);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		// '_dc'=>$_REQUEST['_dc']
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;	
	}

	function get_total_rat($idtender)
	{
		$q = sprintf("SELECT nilai_kontrak_ppn FROM simpro_m_rat_proyek_tender WHERE id_proyek_rat = %d", $idtender);
		$isdata = $this->db->query($q)->num_rows();
		if($isdata > 0)
		{
			$res = $this->db->query($q)->row_array();
			return $res['nilai_kontrak_ppn'];
		} else return 0;		
	}
	
	
	function total_rata($idtender)
	{
		$sql = sprintf("
			SELECT ROUND(SUM(subtotal)) AS total_rata FROM (
				SELECT 
					DISTINCT(tbl_total_koef.kode_material) as kd_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					tbl_harga.harga,
					SUM(tbl_total_koef.volume_total) as total_volume,
					(tbl_harga.harga * SUM(tbl_total_koef.volume_total)) as subtotal,
					tbl_total_koef.id_tender,
					tbl_total_koef.kode_rap					
				FROM (				
					SELECT 
					tbl_asat_apek.*,
					simpro_rat_analisa_item_apek.kode_tree,
					simpro_rat_item_tree.volume,
					(simpro_rat_item_tree.volume * tbl_asat_apek.tot_koef) as volume_total
					FROM
					(				
						(
						SELECT 
							DISTINCT(kode_material), 
							id_tender,
							COUNT(kode_material) * koefisien as tot_koef,
							kode_analisa,
							kode_analisa as parent_kode_analisa,
							kode_rap
						FROM 
						simpro_rat_analisa_asat
						WHERE id_tender = '%d'
						GROUP BY kode_material,kode_analisa,id_tender,koefisien,kode_rap
						ORDER BY kode_material ASC
						)
						UNION ALL
						(
							SELECT 
								DISTINCT(tbl_asat.kode_material) as kode_material,
								simpro_rat_analisa_apek.id_tender,
								(simpro_rat_analisa_apek.koefisien * SUM(tbl_asat.jml_material)) * tbl_asat.koefisien as tot_koef,
								simpro_rat_analisa_apek.kode_analisa,
								simpro_rat_analisa_apek.parent_kode_analisa,
								tbl_asat.kode_rap
							FROM simpro_rat_analisa_apek
							LEFT JOIN (
								SELECT 
									DISTINCT(kode_material), 
									COUNT(kode_material) as jml_material,
									koefisien,
									id_tender,
									kode_analisa,
									kode_rap
								FROM 
								simpro_rat_analisa_asat
								WHERE id_tender = '%d'
								GROUP BY kode_material,kode_analisa,id_tender,koefisien,kode_rap
								ORDER BY kode_material ASC
							) tbl_asat ON tbl_asat.id_tender = simpro_rat_analisa_apek.id_tender AND tbl_asat.kode_analisa = simpro_rat_analisa_apek.kode_analisa
							WHERE simpro_rat_analisa_apek.id_tender = '%d'
							GROUP BY  
							tbl_asat.kode_material,
							tbl_asat.koefisien,
							simpro_rat_analisa_apek.kode_analisa,
							simpro_rat_analisa_apek.parent_kode_analisa,						
							simpro_rat_analisa_apek.koefisien,
							simpro_rat_analisa_apek.id_tender,
							tbl_asat.kode_rap						
						)
					) as tbl_asat_apek
					INNER JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_rat_analisa_item_apek.id_proyek_rat = '%d'
					INNER JOIN simpro_rat_item_tree ON simpro_rat_item_tree.id_proyek_rat = simpro_rat_analisa_item_apek.id_proyek_rat AND simpro_rat_item_tree.kode_tree = simpro_rat_analisa_item_apek.kode_tree					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_material), 
					harga 
					FROM simpro_rat_analisa_asat
					WHERE id_tender = '%d'
					GROUP BY kode_material,harga
				) as tbl_harga ON (tbl_harga.kode_material = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_detail_material ON ( simpro_tbl_detail_material.detail_material_kode = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))
				GROUP BY 
					tbl_total_koef.kode_material,
					tbl_total_koef.id_tender,
					tbl_total_koef.kode_rap,
					tbl_harga.harga,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name		
				ORDER BY tbl_total_koef.kode_material ASC
		) as tbl_total_rata
		GROUP BY id_tender
			", $idtender, $idtender, $idtender, $idtender, $idtender);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->row_array());
		} else return false;	
	}
	
	function rab_raba($idtender)
	{
		$sql = sprintf("		
				SELECT 
					DISTINCT(tbl_total_koef.kode_material) as kd_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					tbl_harga.harga,
					ROUND(SUM(tbl_total_koef.volume_total),4) as total_volume,
					(tbl_harga.harga * SUM(tbl_total_koef.volume_total)) as subtotal,
					tbl_total_koef.id_tender,
					tbl_total_koef.kode_rap
				FROM (				
					SELECT 
					tbl_asat_apek.*,
					simpro_rat_analisa_item_apek.kode_tree,
					simpro_rat_rab_item_tree.volume,
					(simpro_rat_rab_item_tree.volume * tbl_asat_apek.tot_koef) as volume_total
					FROM
					(				
						(
							SELECT 
								DISTINCT(simpro_rat_rab_analisa.kode_analisa) AS kode_material, 
								simpro_rat_rab_analisa.id_tender,
								simpro_rat_analisa_asat.kode_analisa,
								simpro_rat_analisa_asat.kode_analisa AS parent_kode_analisa,								
								simpro_rat_rab_analisa.koefisien_rab as tot_koef,
								simpro_rat_analisa_asat.kode_rap
							FROM 
							simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.kode_material = simpro_rat_rab_analisa.kode_analisa AND simpro_rat_analisa_asat.id_tender = simpro_rat_rab_analisa.id_tender
							WHERE simpro_rat_rab_analisa.id_tender = '%d'
							AND LEFT(simpro_rat_rab_analisa.kode_analisa,2) <> 'AN'						
							GROUP BY simpro_rat_rab_analisa.kode_analisa,
									simpro_rat_rab_analisa.id_tender,
									simpro_rat_rab_analisa.koefisien_rab,
									simpro_rat_analisa_asat.kode_analisa,
									simpro_rat_analisa_asat.kode_rap									
							ORDER BY simpro_rat_rab_analisa.kode_analisa ASC
						)
						UNION ALL
						(
							SELECT 
								DISTINCT(tbl_asat.kode_analisa) AS kode_material,
								simpro_rat_rab_analisa.id_tender,
								tbl_asat.kd_analisa AS kode_analisa,
								simpro_rat_analisa_apek.parent_kode_analisa,
								(simpro_rat_rab_analisa.koefisien_rab) * tbl_asat.koefisien as tot_koef,
								tbl_asat.kode_rap
							FROM simpro_rat_analisa_apek
							LEFT JOIN simpro_rat_rab_analisa ON simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa_apek.id_analisa_apek AND simpro_rat_rab_analisa.id_tender = simpro_rat_analisa_apek.id_tender
							LEFT JOIN (
								SELECT 
									DISTINCT(simpro_rat_rab_analisa.kode_analisa), 
									simpro_rat_rab_analisa.id_tender,
									COUNT(simpro_rat_rab_analisa.kode_analisa) as jml_material,
									simpro_rat_rab_analisa.koefisien_rab as koefisien,
									simpro_rat_rab_analisa.id_simpro_rat_analisa,
									simpro_rat_analisa_asat.kode_analisa as kd_analisa,
									simpro_rat_analisa_asat.kode_rap 
								FROM 
								simpro_rat_rab_analisa
								INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = id_simpro_rat_analisa
								WHERE simpro_rat_rab_analisa.id_tender = '%d'
								AND LEFT(simpro_rat_rab_analisa.kode_analisa,2) <> 'AN'						
								GROUP BY 
									simpro_rat_rab_analisa.kode_analisa,
									simpro_rat_rab_analisa.id_tender,koefisien_rab,
									simpro_rat_rab_analisa.id_simpro_rat_analisa,
									simpro_rat_analisa_asat.kode_analisa,
									simpro_rat_analisa_asat.kode_rap
								ORDER BY kode_analisa ASC
							) tbl_asat ON tbl_asat.id_tender = simpro_rat_analisa_apek.id_tender AND 
								tbl_asat.kd_analisa IN 
								(
									select kode_analisa from simpro_rat_analisa_apek where id_tender = tbl_asat.id_tender
								)							
							WHERE simpro_rat_analisa_apek.id_tender = '%d'
							GROUP BY  
							tbl_asat.kode_analisa,
							tbl_asat.koefisien,
							tbl_asat.kd_analisa,
							tbl_asat.kode_rap,							
							simpro_rat_rab_analisa.kode_analisa,
							simpro_rat_rab_analisa.koefisien_rab,
							simpro_rat_rab_analisa.id_tender,
							simpro_rat_analisa_apek.kode_analisa,
							simpro_rat_analisa_apek.parent_kode_analisa
						)
					) as tbl_asat_apek
					INNER JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_rat_analisa_item_apek.id_proyek_rat = '%d'
					INNER JOIN simpro_rat_rab_item_tree ON simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_rab_item_tree.rat_item_tree 					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_analisa) AS kode_material, 
					harga_rab AS harga
					FROM simpro_rat_rab_analisa
					WHERE id_tender = '%d'
					GROUP BY kode_analisa,harga_rab				
				) as tbl_harga ON (tbl_harga.kode_material = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_detail_material ON ( simpro_tbl_detail_material.detail_material_kode = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))
				GROUP BY 
					tbl_total_koef.kode_material,
					tbl_total_koef.id_tender,
					tbl_total_koef.kode_rap,
					tbl_harga.harga,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name		
				ORDER BY tbl_total_koef.kode_material ASC						
			", $idtender, $idtender, $idtender, $idtender, $idtender);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			//'_dc'=>$_REQUEST['_dc']
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;	
	}

	function total_raba($idtender)
	{
		$sql = sprintf("
			SELECT ROUND(SUM(subtotal)) AS total_raba FROM (
				SELECT 
					DISTINCT(tbl_total_koef.kode_material) as kd_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					tbl_harga.harga,
					SUM(tbl_total_koef.volume_total) as total_volume,
					(tbl_harga.harga * SUM(tbl_total_koef.volume_total)) as subtotal,
					tbl_total_koef.id_tender
				FROM (				
					SELECT 
					tbl_asat_apek.*,
					simpro_rat_analisa_item_apek.kode_tree,
					simpro_rat_rab_item_tree.volume,
					(simpro_rat_rab_item_tree.volume * tbl_asat_apek.tot_koef) as volume_total
					FROM
					(				
						(
							SELECT 
								DISTINCT(simpro_rat_rab_analisa.kode_analisa) AS kode_material, 
								simpro_rat_rab_analisa.id_tender,
								simpro_rat_analisa_asat.kode_analisa,
								simpro_rat_analisa_asat.kode_analisa AS parent_kode_analisa,								
								simpro_rat_rab_analisa.koefisien_rab as tot_koef
							FROM 
							simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.kode_material = simpro_rat_rab_analisa.kode_analisa AND simpro_rat_analisa_asat.id_tender = simpro_rat_rab_analisa.id_tender
							WHERE simpro_rat_rab_analisa.id_tender = '%d'
							AND LEFT(simpro_rat_rab_analisa.kode_analisa,2) <> 'AN'						
							GROUP BY simpro_rat_rab_analisa.kode_analisa,
									simpro_rat_rab_analisa.id_tender,
									simpro_rat_rab_analisa.koefisien_rab,
									simpro_rat_analisa_asat.kode_analisa
							ORDER BY simpro_rat_rab_analisa.kode_analisa ASC
						)
						UNION ALL
						(
							SELECT 
								DISTINCT(tbl_asat.kode_analisa) AS kode_material,
								simpro_rat_rab_analisa.id_tender,
								tbl_asat.kd_analisa AS kode_analisa,
								simpro_rat_analisa_apek.parent_kode_analisa,
								(simpro_rat_rab_analisa.koefisien_rab) * tbl_asat.koefisien as tot_koef
							FROM simpro_rat_analisa_apek
							LEFT JOIN simpro_rat_rab_analisa ON simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa_apek.id_analisa_apek AND simpro_rat_rab_analisa.id_tender = simpro_rat_analisa_apek.id_tender
							LEFT JOIN (
								SELECT 
									DISTINCT(simpro_rat_rab_analisa.kode_analisa), 
									simpro_rat_rab_analisa.id_tender,
									COUNT(simpro_rat_rab_analisa.kode_analisa) as jml_material,
									simpro_rat_rab_analisa.koefisien_rab as koefisien,
									simpro_rat_rab_analisa.id_simpro_rat_analisa,
									simpro_rat_analisa_asat.kode_analisa as kd_analisa
								FROM 
								simpro_rat_rab_analisa
								INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = id_simpro_rat_analisa
								WHERE simpro_rat_rab_analisa.id_tender = '%d'
								AND LEFT(simpro_rat_rab_analisa.kode_analisa,2) <> 'AN'						
								GROUP BY 
									simpro_rat_rab_analisa.kode_analisa,
									simpro_rat_rab_analisa.id_tender,koefisien_rab,
									simpro_rat_rab_analisa.id_simpro_rat_analisa,
								simpro_rat_analisa_asat.kode_analisa
								ORDER BY kode_analisa ASC
							) tbl_asat ON tbl_asat.id_tender = simpro_rat_analisa_apek.id_tender AND 
								tbl_asat.kd_analisa IN 
								(
									select kode_analisa from simpro_rat_analisa_apek where id_tender = tbl_asat.id_tender
								)							
							WHERE simpro_rat_analisa_apek.id_tender = '%d'
							GROUP BY  
							tbl_asat.kode_analisa,
							tbl_asat.koefisien,
							tbl_asat.kd_analisa,
							simpro_rat_rab_analisa.kode_analisa,
							simpro_rat_rab_analisa.koefisien_rab,
							simpro_rat_rab_analisa.id_tender,
							simpro_rat_analisa_apek.kode_analisa,
							simpro_rat_analisa_apek.parent_kode_analisa
						)
					) as tbl_asat_apek
					INNER JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_rat_analisa_item_apek.id_proyek_rat = '%d'
					INNER JOIN simpro_rat_rab_item_tree ON simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_rab_item_tree.rat_item_tree 
					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_analisa) AS kode_material, 
					harga_rab AS harga
					FROM simpro_rat_rab_analisa
					WHERE id_tender = '%d'
					GROUP BY kode_analisa,harga_rab				
				) as tbl_harga ON (tbl_harga.kode_material = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_detail_material ON ( simpro_tbl_detail_material.detail_material_kode = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))
				GROUP BY 
					tbl_total_koef.kode_material,
					tbl_total_koef.id_tender,
					tbl_harga.harga,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name		
				ORDER BY tbl_total_koef.kode_material ASC						
		) as tbl_total_raba
		GROUP BY id_tender
			", $idtender, $idtender, $idtender, $idtender, $idtender);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->row_array());
		} else return false;	
	}
	
	function total_rab($idtender)
	{					
		$query = sprintf("
			SELECT 
			ROUND(SUM(simpro_rat_rab_item_tree.volume * tbl_harga_rab.harga_rab)) as total_rab
			FROM simpro_rat_rab_item_tree
			INNER JOIN simpro_rat_item_tree on (simpro_rat_item_tree.rat_item_tree = simpro_rat_rab_item_tree.rat_item_tree)
			INNER JOIN simpro_rat_analisa_item_apek ON (simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_rab_item_tree.rat_item_tree)
			INNER JOIN (
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
				GROUP BY kode_analisa_rat
			) AS tbl_harga_rab ON tbl_harga_rab.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
			WHERE
			simpro_rat_item_tree.id_proyek_rat = '%d'
			GROUP BY simpro_rat_item_tree.id_proyek_rat		
		", $idtender, $idtender, $idtender);
		
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->row_array());
		} else return false;
	}
	
}

?>