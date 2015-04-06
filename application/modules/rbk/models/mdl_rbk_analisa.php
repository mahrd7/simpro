<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_Rbk_Analisa extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_detailmaterial_kode($kode)
	{
		$query = "
			SELECT *
			FROM simpro_tbl_detail_material 
			WHERE subbidang_kode LIKE '%".strtolower(addslashes($kode))."%' 
			OR detail_material_nama LIKE '%".strtolower(addslashes($kode))."%'
			OR detail_material_kode LIKE '%".strtolower(addslashes($kode))."%'
			";
		$data = $this->reqcari($query, array('detail_material_kode', 'detail_material_nama'));
		if(count($data))
		{
			return $data;
		} else return false;
	}	

	function get_tree_item($idpro, $tree_parent=0,$param='')
	{
		// $query = sprintf(
		// 	"
		// 	SELECT 
		// 		simpro_rap_item_tree.*, 
		// 		(simpro_rap_item_tree.kode_tree || ' ' || simpro_rap_item_tree.tree_item) AS task,
		// 		simpro_rap_analisa_item_apek.kode_analisa,
		// 		COALESCE(tbl_harga.harga, 0) AS harga,
		// 		(COALESCE(tbl_harga.harga, 0) * simpro_rap_item_tree.volume) as subtotal
		// 	FROM simpro_rap_item_tree 
		// 	LEFT JOIN simpro_rap_analisa_item_apek ON simpro_rap_analisa_item_apek.rap_item_tree = simpro_rap_item_tree.rap_item_tree
		// 	LEFT JOIN (
		// 		SELECT 
		// 			DISTINCT ON(kode_analisa)
		// 			kode_analisa,
		// 			SUM(harga * koefisien) AS harga
		// 		FROM simpro_rap_analisa_asat 
		// 		WHERE id_proyek = '%d'
		// 		GROUP BY kode_analisa			
		// 	) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_item_apek.kode_analisa						
		// 	WHERE simpro_rap_item_tree.id_proyek = '%d' 
		// 	AND simpro_rap_item_tree.tree_parent_id = '%d'
		// 	ORDER BY simpro_rap_item_tree.kode_tree ASC
		// 	", 
		// 	$idpro, $idpro, $tree_parent
		// 	);
		$query = "
			select 
			case when
			(select count(kode_tree) from (select * from simpro_rap_item_tree where lower(tree_item) like lower('%$param%') and id_proyek = $idpro and rap_item_tree = x.rap_item_tree) hj) > 0
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
			from simpro_rap_item_tree a
			join simpro_rap_analisa_item_apek b
			on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek
			where a.id_proyek = $idpro and a.kode_tree = x.kode_tree) as kode_analisa,
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
			AND x.tree_parent_id = $tree_parent
			group by x.rap_item_tree			
			ORDER BY xnm, urut
			";
		$rs = $this->db->query($query);	
		return $rs;
	}

	function update_harga_satuan($idproyek)
	{
		$sql = "update simpro_rap_analisa_asat set
			harga = a.harga
			from (
			select * from simpro_rap_analisa_asat where id_proyek = $idproyek and harga != 0
			) a
			where simpro_rap_analisa_asat.id_proyek = 44 and simpro_rap_analisa_asat.harga = 0 and simpro_rap_analisa_asat.kode_material = a.kode_material";
		$this->db->query($sql);
	}

	function get_daftar_analisa_satuan($idproyek)
	{	
		$this->update_harga_satuan($idproyek);
		$query = sprintf("
				SELECT * FROM (
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
					WHERE simpro_rap_analisa_asat.id_proyek= '%d'
					ORDER BY 
						simpro_rap_analisa_asat.kode_analisa,
						simpro_tbl_detail_material.detail_material_kode				
					ASC
				)
				UNION ALL 
				(
					SELECT 
						simpro_rap_analisa_apek.id_analisa_apek AS id_analisa_asat,
						simpro_rap_analisa_apek.kode_analisa,
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
						WHERE id_proyek= '%d'
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_apek.kode_analisa			
					WHERE simpro_rap_analisa_apek.id_proyek= '%d'
					ORDER BY 
						simpro_rap_analisa_apek.parent_kode_analisa,				
						simpro_rap_analisa_apek.kode_analisa
					ASC					
				)		
				) AS tbl_analisa_satuan
			", $idproyek,$idproyek,$idproyek);
			
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
	
	function get_tree_item_harga($idpro, $tree_parent)
	{
		$query = sprintf(
			"
			SELECT 
				(
					SELECT
						SUM(aharga * akoefisien) as subtotal
					FROM 
						simpro_rap_analisa
					WHERE 
						id_proyek = '%d' AND 
						rap_item_tree = '%d'
					GROUP BY 
						id_proyek, rap_item_tree						
				) AS harga,				
				(
					SELECT
						SUM(aharga * akoefisien) as subtotal
					FROM 
						simpro_rap_analisa
					WHERE 
						id_proyek = '%d' AND 
						rap_item_tree = '%d'
					GROUP BY 
						id_proyek, rap_item_tree						
				) * volume AS subtotal,
				CONCAT(kode_tree,' ',tree_item) AS task 
			FROM simpro_rap_item_tree 
			WHERE id_proyek = '%d' 
			AND rap_item_tree = '%d'", 
			$idpro, $tree_parent,
			$idpro, $tree_parent,
			$idpro, $tree_parent
			);
		$rs = $this->db->query($query);	
		return $rs;
	}
	
	function rat_rata($idproyek)
	{
		$sql = sprintf("		
				SELECT 
					DISTINCT(tbl_total_koef.kode_material) as kd_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					avg(tbl_harga.harga),
					SUM(tbl_total_koef.tot_koef) as total_volume,
					(avg(tbl_harga.harga) * SUM(tbl_total_koef.tot_koef)) as subtotal,
					tbl_total_koef.id_proyek
				FROM (
						(
						SELECT 
							DISTINCT(kode_material), 
							id_proyek,
							COUNT(kode_material) * koefisien as tot_koef,
							kode_analisa
						FROM 
						simpro_rap_analisa_asat
						WHERE id_proyek= '%d'
						GROUP BY kode_material,kode_analisa,id_proyek,koefisien 
						ORDER BY kode_material ASC
						)
						UNION ALL
						(
						SELECT 
							DISTINCT(tbl_asat.kode_material) as kode_material,
							simpro_rap_analisa_apek.id_proyek,
							(simpro_rap_analisa_apek.koefisien * SUM(tbl_asat.jml_material)) * tbl_asat.koefisien as tot_koef,
							simpro_rap_analisa_apek.kode_analisa
						FROM simpro_rap_analisa_apek
						LEFT JOIN (
							SELECT 
								DISTINCT(kode_material), 
								COUNT(kode_material) as jml_material,
								koefisien,
								id_proyek,
								kode_analisa
							FROM 
							simpro_rap_analisa_asat
							WHERE id_proyek= '%d'
							GROUP BY kode_material,kode_analisa,id_proyek,koefisien
							ORDER BY kode_material ASC
						) tbl_asat ON tbl_asat.id_proyek= simpro_rap_analisa_apek.id_proyek AND tbl_asat.kode_analisa = simpro_rap_analisa_apek.kode_analisa
						WHERE simpro_rap_analisa_apek.id_proyek= '%d'
						GROUP BY  
						tbl_asat.kode_material,
						tbl_asat.koefisien,
						simpro_rap_analisa_apek.kode_analisa,
						simpro_rap_analisa_apek.koefisien,
						simpro_rap_analisa_apek.id_proyek							
					)
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_material), 
					harga 
					FROM simpro_rap_analisa_asat
					WHERE id_proyek= '%d'
					GROUP BY kode_material,harga
				) as tbl_harga ON (tbl_harga.kode_material = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_detail_material ON ( simpro_tbl_detail_material.detail_material_kode = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))
				GROUP BY 
					tbl_total_koef.kode_material,
					tbl_total_koef.id_proyek,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name		
				ORDER BY tbl_total_koef.kode_material ASC
			", $idproyek, $idproyek, $idproyek, $idproyek);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		} else return false;	
	}
	
	function rap_rapa($idproyek)
	{
		$sql = sprintf("		
				SELECT 
					DISTINCT(tbl_total_koef.kode_material) as kd_material,
					tbl_total_koef.kode_rap,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					avg(tbl_harga.harga) as harga,
					ROUND(SUM(tbl_total_koef.volume_total),4) as total_volume,
					(avg(tbl_harga.harga) * SUM(tbl_total_koef.volume_total)) as subtotal,
					tbl_total_koef.id_proyek
				FROM (				
					SELECT 
					tbl_asat_apek.*,
					simpro_rap_analisa_item_apek.kode_tree,
					simpro_rap_item_tree.volume,
					(simpro_rap_item_tree.volume * tbl_asat_apek.tot_koef) as volume_total
					FROM
					(				
						(
						SELECT 
							DISTINCT(kode_material), 
							id_proyek,
							COUNT(kode_material) * koefisien as tot_koef,
							kode_analisa,
							kode_analisa as parent_kode_analisa,
							kode_rap
						FROM 
						simpro_rap_analisa_asat
						WHERE id_proyek = '%d'
						GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
						ORDER BY kode_material ASC
						)
						UNION ALL
						(
							SELECT 
								DISTINCT(tbl_asat.kode_material) as kode_material,
								simpro_rap_analisa_apek.id_proyek,
								(simpro_rap_analisa_apek.koefisien * SUM(tbl_asat.jml_material)) * tbl_asat.koefisien as tot_koef,
								simpro_rap_analisa_apek.kode_analisa,
								simpro_rap_analisa_apek.parent_kode_analisa,
								tbl_asat.kode_rap
							FROM simpro_rap_analisa_apek
							LEFT JOIN (
								SELECT 
									DISTINCT(kode_material), 
									COUNT(kode_material) as jml_material,
									koefisien,
									id_proyek,
									kode_analisa,
									kode_rap
								FROM 
								simpro_rap_analisa_asat
								WHERE id_proyek = '%d'
								GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
								ORDER BY kode_material ASC
							) tbl_asat ON tbl_asat.id_proyek = simpro_rap_analisa_apek.id_proyek AND tbl_asat.kode_analisa = simpro_rap_analisa_apek.kode_analisa
							WHERE simpro_rap_analisa_apek.id_proyek = '%d'
							GROUP BY  
							tbl_asat.kode_material,
							tbl_asat.koefisien,
							simpro_rap_analisa_apek.kode_analisa,
							simpro_rap_analisa_apek.parent_kode_analisa,						
							simpro_rap_analisa_apek.koefisien,
							simpro_rap_analisa_apek.id_proyek,
							tbl_asat.kode_rap						
						)
					) as tbl_asat_apek
					INNER JOIN simpro_rap_analisa_item_apek ON simpro_rap_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_rap_analisa_item_apek.id_proyek = '%d'
					INNER JOIN simpro_rap_item_tree ON simpro_rap_item_tree.id_proyek = simpro_rap_analisa_item_apek.id_proyek AND simpro_rap_item_tree.kode_tree = simpro_rap_analisa_item_apek.kode_tree					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_material), 
					harga 
					FROM simpro_rap_analisa_asat
					WHERE id_proyek = '%d'
					GROUP BY kode_material,harga
				) as tbl_harga ON (tbl_harga.kode_material = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_detail_material ON ( simpro_tbl_detail_material.detail_material_kode = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))
				GROUP BY 
					tbl_total_koef.kode_material,
					tbl_total_koef.id_proyek,
					tbl_total_koef.kode_rap,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name		
				ORDER BY tbl_total_koef.kode_material ASC		
			", $idproyek, $idproyek, $idproyek, $idproyek, $idproyek);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->result_array());
		} else return false;	
	}

	function total_rapa($idproyek)
	{
		$sql = sprintf("
			SELECT ROUND(SUM(subtotal)) AS total_rapa FROM (
				SELECT 
					DISTINCT(tbl_total_koef.kode_material) as kd_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					tbl_harga.harga,
					SUM(tbl_total_koef.volume_total) as total_volume,
					(tbl_harga.harga * SUM(tbl_total_koef.volume_total)) as subtotal,
					tbl_total_koef.id_proyek,
					tbl_total_koef.kode_rap					
				FROM (				
					SELECT 
					tbl_asat_apek.*,
					simpro_rap_analisa_item_apek.kode_tree,
					simpro_rap_item_tree.volume,
					(simpro_rap_item_tree.volume * tbl_asat_apek.tot_koef) as volume_total
					FROM
					(				
						(
						SELECT 
							DISTINCT(kode_material), 
							id_proyek,
							COUNT(kode_material) * koefisien as tot_koef,
							kode_analisa,
							kode_analisa as parent_kode_analisa,
							kode_rap
						FROM 
						simpro_rap_analisa_asat
						WHERE id_proyek = '%d'
						GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
						ORDER BY kode_material ASC
						)
						UNION ALL
						(
							SELECT 
								DISTINCT(tbl_asat.kode_material) as kode_material,
								simpro_rap_analisa_apek.id_proyek,
								(simpro_rap_analisa_apek.koefisien * SUM(tbl_asat.jml_material)) * tbl_asat.koefisien as tot_koef,
								simpro_rap_analisa_apek.kode_analisa,
								simpro_rap_analisa_apek.parent_kode_analisa,
								tbl_asat.kode_rap
							FROM simpro_rap_analisa_apek
							LEFT JOIN (
								SELECT 
									DISTINCT(kode_material), 
									COUNT(kode_material) as jml_material,
									koefisien,
									id_proyek,
									kode_analisa,
									kode_rap
								FROM 
								simpro_rap_analisa_asat
								WHERE id_proyek = '%d'
								GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
								ORDER BY kode_material ASC
							) tbl_asat ON tbl_asat.id_proyek = simpro_rap_analisa_apek.id_proyek AND tbl_asat.kode_analisa = simpro_rap_analisa_apek.kode_analisa
							WHERE simpro_rap_analisa_apek.id_proyek = '%d'
							GROUP BY  
							tbl_asat.kode_material,
							tbl_asat.koefisien,
							simpro_rap_analisa_apek.kode_analisa,
							simpro_rap_analisa_apek.parent_kode_analisa,						
							simpro_rap_analisa_apek.koefisien,
							simpro_rap_analisa_apek.id_proyek,
							tbl_asat.kode_rap						
						)
					) as tbl_asat_apek
					INNER JOIN simpro_rap_analisa_item_apek ON simpro_rap_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_rap_analisa_item_apek.id_proyek = '%d'
					INNER JOIN simpro_rap_item_tree ON simpro_rap_item_tree.id_proyek = simpro_rap_analisa_item_apek.id_proyek AND simpro_rap_item_tree.kode_tree = simpro_rap_analisa_item_apek.kode_tree					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_material), 
					harga 
					FROM simpro_rap_analisa_asat
					WHERE id_proyek = '%d'
					GROUP BY kode_material,harga
				) as tbl_harga ON (tbl_harga.kode_material = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_detail_material ON ( simpro_tbl_detail_material.detail_material_kode = tbl_total_koef.kode_material)
				INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))
				GROUP BY 
					tbl_total_koef.kode_material,
					tbl_total_koef.id_proyek,
					tbl_total_koef.kode_rap,
					tbl_harga.harga,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name		
				ORDER BY tbl_total_koef.kode_material ASC
		) as tbl_total_rapa
		GROUP BY id_proyek		
			", $idproyek, $idproyek, $idproyek, $idproyek, $idproyek);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->row_array());
		} else return false;	
	}
	
	function get_daftar_analisa_pekerjaan($idproyek, $idkat=FALSE)
	{				
		if($idkat)
		{
			$q_idkat = sprintf("AND simpro_rap_analisa_daftar.id_kat_analisa = '%d'", $idkat);
		} else $q_idkat = "";
		$query = "
			SELECT 
				simpro_rap_analisa_daftar.*, 
				(simpro_rat_analisa_kategori.kode_kat || '. ' ||simpro_rat_analisa_kategori.kat_name) AS kategori,				
				simpro_rat_analisa_kategori.kat_name AS nama_kategori,				
				simpro_tbl_satuan.satuan_nama AS satuan,
				COALESCE(tbl_harga.harga, 0) + COALESCE(tbl_harga_apek.harga, 0) AS harga_satuan,
				coalesce(tbl_harga.c,0) as c_asat,
				coalesce(tbl_harga_apek.c,0) as c_apek
			FROM 
			simpro_rap_analisa_daftar
			LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
			LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.id_kat_analisa = simpro_rap_analisa_daftar.id_kat_analisa			
			LEFT JOIN (
				SELECT 
					DISTINCT ON(kode_analisa)
					kode_analisa,
					SUM(harga * koefisien) AS harga,
					coalesce((count('a')),0) as c
				FROM simpro_rap_analisa_asat 
				WHERE id_proyek= '".$idproyek."'
				GROUP BY kode_analisa			
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_daftar.kode_analisa		
			LEFT JOIN 
			(
				SELECT 
					DISTINCT ON(simpro_rap_analisa_apek.parent_kode_analisa)
					simpro_rap_analisa_apek.parent_kode_analisa,
					SUM(simpro_rap_analisa_apek.koefisien * tbl_harga_asat.harga) as harga,
					coalesce((count('a')),0) as c
				FROM simpro_rap_analisa_apek
				INNER JOIN (
					SELECT 
						DISTINCT ON(kode_analisa)
						kode_analisa,
						SUM(harga * koefisien) AS harga
					FROM simpro_rap_analisa_asat 
					WHERE id_proyek= '".$idproyek."'
					GROUP BY kode_analisa							
				) as tbl_harga_asat ON tbl_harga_asat.kode_analisa = simpro_rap_analisa_apek.kode_analisa
				WHERE simpro_rap_analisa_apek.id_proyek= '".$idproyek."'
				GROUP BY simpro_rap_analisa_apek.parent_kode_analisa			
			) as tbl_harga_apek ON tbl_harga_apek.parent_kode_analisa = simpro_rap_analisa_daftar.kode_analisa			
			WHERE 
				simpro_rap_analisa_daftar.id_proyek= '".$idproyek."'
				".$q_idkat."
		";
		
		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "AND (simpro_rap_analisa_daftar.nama_item @@ to_tsquery('".addslashes($search).":*') OR simpro_rap_analisa_daftar.kode_analisa @@ to_tsquery('".addslashes($search).":*'))";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		$qadd = "ORDER BY simpro_rap_analisa_daftar.kode_analisa ASC";
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
	
	
	function get_harga_satuan_asat($idtender)
	{
		// $query = sprintf("
		// 	SELECT 
		// 		DISTINCT ON (simpro_rap_analisa_asat.kode_material)
		// 		simpro_rap_analisa_asat.kode_material,
		// 		tbl_detail_material.detail_material_nama,
		// 		tbl_detail_material.detail_material_satuan,
		// 		tbl_detail_material.detail_material_nama,
		// 		simpro_rap_analisa_asat.harga,
		// 		simpro_rap_analisa_asat.kode_rap,
		// 		simpro_rap_analisa_asat.keterangan,
		// 		subbidang.subbidang_name as kategori
		// 	FROM 
		// 		simpro_rap_analisa_asat
		// 	LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
		// 	INNER JOIN simpro_tbl_subbidang ON simpro_tbl_subbidang.subbidang_kode = LEFT(tbl_detail_material.detail_material_kode,3)
		// 	WHERE simpro_rap_analisa_asat.id_proyek = '%d'
		// 	ORDER BY simpro_rap_analisa_asat.kode_material ASC		
		// ", $idtender);

		$query = sprintf("
			SELECT 
				simpro_rap_analisa_asat.kode_material,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				simpro_tbl_detail_material.detail_material_nama,
				avg(simpro_rap_analisa_asat.harga) as harga,
				simpro_rap_analisa_asat.kode_rap,
				simpro_rap_analisa_asat.keterangan,
				simpro_tbl_subbidang.subbidang_name as kategori
			FROM 
				simpro_rap_analisa_asat
			LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
			INNER JOIN simpro_tbl_subbidang ON simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3)
			WHERE simpro_rap_analisa_asat.id_proyek = '%d'
			group by
				simpro_rap_analisa_asat.kode_material,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_tbl_detail_material.detail_material_satuan,
				simpro_tbl_detail_material.detail_material_nama,
				simpro_rap_analisa_asat.kode_rap,
				simpro_rap_analisa_asat.keterangan,
				kategori
			ORDER BY simpro_rap_analisa_asat.kode_material ASC		
		", $idtender);
		$data = $this->reqcari($query, array('simpro_rap_analisa_asat.kode_material', 'simpro_tbl_detail_material.detail_material_nama'));
		if(count($data))
		{
			return $data;
		} else return false;
	}
	
	function get_data_apek($idtender)
	{	
		$query = sprintf("
			SELECT 
				simpro_rap_analisa_apek.*,
				(simpro_rap_analisa_apek.parent_kode_analisa || ' - ' || simpro_rap_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ') ') AS apek_kat, 				
				ad.nama_item AS nama_item,
				simpro_rap_analisa_daftar.nama_item AS parent_item,
				simpro_rap_analisa_daftar.id_satuan,
				simpro_tbl_satuan.satuan_nama,
				COALESCE(tbl_harga.harga,0) AS harga,
				COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
			FROM 
				simpro_rap_analisa_apek
			INNER JOIN simpro_rap_analisa_daftar ad ON (ad.kode_analisa = simpro_rap_analisa_apek.kode_analisa AND ad.id_proyek = simpro_rap_analisa_apek.id_proyek)
			INNER JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_apek.parent_kode_analisa AND simpro_rap_analisa_daftar.id_proyek = simpro_rap_analisa_apek.id_proyek)
			INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
			LEFT JOIN (
				SELECT 
					DISTINCT ON(kode_analisa)
					kode_analisa,
					SUM(harga * koefisien) AS harga
				FROM simpro_rap_analisa_asat 
				WHERE id_proyek = '".$idtender."'
				GROUP BY kode_analisa			
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_apek.kode_analisa			
			WHERE simpro_rap_analisa_apek.id_proyek = '%d'
			", $idtender);
			
		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "AND (
				simpro_rap_analisa_daftar.nama_item @@ to_tsquery('".addslashes($search).":*') 
				OR simpro_rap_analisa_apek.kode_analisa @@ to_tsquery('".addslashes($search).":*')
				OR simpro_rap_analisa_apek.parent_kode_analisa @@ to_tsquery('".addslashes($search).":*')
				OR ad.nama_item @@ to_tsquery('".addslashes($search).":*')
			)";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		
		$q_order = "
			ORDER BY 
				simpro_rap_analisa_apek.parent_kode_analisa,				
				simpro_rap_analisa_apek.kode_analisa
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
	
	function get_data_analisa_pekerjaan($idtender)
	{				
		$query = "
			SELECT 
				simpro_rap_analisa_daftar.*, 
				(simpro_rat_analisa_kategori.kode_kat || '. ' ||simpro_rat_analisa_kategori.kat_name) AS kategori,				
				simpro_rat_analisa_kategori.kat_name AS nama_kategori,				
				simpro_tbl_satuan.satuan_nama AS satuan,
				COALESCE(tbl_harga.harga, 0) + COALESCE(tbl_harga_apek.harga, 0) AS harga_satuan
			FROM 
			simpro_rap_analisa_daftar
			LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
			LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.id_kat_analisa = simpro_rap_analisa_daftar.id_kat_analisa			
			LEFT JOIN (
				SELECT 
					DISTINCT ON(kode_analisa)
					kode_analisa,
					SUM(harga * koefisien) AS harga
				FROM simpro_rap_analisa_asat 
				WHERE id_proyek = '".$idtender."'
				GROUP BY kode_analisa			
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_daftar.kode_analisa
			LEFT JOIN 
			(
				SELECT 
					DISTINCT ON(simpro_rap_analisa_apek.parent_kode_analisa)
					simpro_rap_analisa_apek.parent_kode_analisa,
					SUM(simpro_rap_analisa_apek.koefisien * tbl_harga_asat.harga) as harga
				FROM simpro_rap_analisa_apek
				INNER JOIN (
					SELECT 
						DISTINCT ON(kode_analisa)
						kode_analisa,
						SUM(harga * koefisien) AS harga
					FROM simpro_rap_analisa_asat 
					WHERE id_proyek = '".$idtender."'
					GROUP BY kode_analisa							
				) as tbl_harga_asat ON tbl_harga_asat.kode_analisa = simpro_rap_analisa_apek.kode_analisa
				WHERE simpro_rap_analisa_apek.id_proyek = '".$idtender."'
				GROUP BY simpro_rap_analisa_apek.parent_kode_analisa			
			) as tbl_harga_apek ON tbl_harga_apek.parent_kode_analisa = simpro_rap_analisa_daftar.kode_analisa
			WHERE 
				simpro_rap_analisa_daftar.id_proyek = '".$idtender."'
				AND simpro_rap_analisa_daftar.id_kat_analisa = '10'
		";

		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "AND (simpro_rap_analisa_daftar.nama_item @@ to_tsquery('".addslashes($search).":*') OR simpro_rap_analisa_daftar.kode_analisa @@ to_tsquery('".addslashes($search).":*'))";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		$qadd = "ORDER BY simpro_rap_analisa_daftar.kode_analisa ASC";
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

	function get_data_analisa_pekerjaan_copy($idtender)
	{				
		$query = "
			SELECT 
				simpro_rap_analisa_daftar.*, 
				(simpro_rat_analisa_kategori.kode_kat || '. ' ||simpro_rat_analisa_kategori.kat_name) AS kategori,				
				simpro_rat_analisa_kategori.kat_name AS nama_kategori,				
				simpro_tbl_satuan.satuan_nama AS satuan,
				COALESCE(tbl_harga.harga, 0) + COALESCE(tbl_harga_apek.harga, 0) AS harga_satuan,
				case when tbl_harga_apek.parent_kode_analisa isnull 
				then 'ASAT'
				else 'APEK'
				end as jenis_analisa
			FROM 
			simpro_rap_analisa_daftar
			LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
			LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.id_kat_analisa = simpro_rap_analisa_daftar.id_kat_analisa			
			LEFT JOIN (
				SELECT 
					DISTINCT ON(kode_analisa)
					kode_analisa,
					SUM(harga * koefisien) AS harga
				FROM simpro_rap_analisa_asat 
				WHERE id_proyek = '".$idtender."'
				GROUP BY kode_analisa			
			) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_daftar.kode_analisa
			LEFT JOIN 
			(
				SELECT 
					DISTINCT ON(simpro_rap_analisa_apek.parent_kode_analisa)
					simpro_rap_analisa_apek.parent_kode_analisa,
					SUM(simpro_rap_analisa_apek.koefisien * tbl_harga_asat.harga) as harga
				FROM simpro_rap_analisa_apek
				INNER JOIN (
					SELECT 
						DISTINCT ON(kode_analisa)
						kode_analisa,
						SUM(harga * koefisien) AS harga
					FROM simpro_rap_analisa_asat 
					WHERE id_proyek = '".$idtender."'
					GROUP BY kode_analisa							
				) as tbl_harga_asat ON tbl_harga_asat.kode_analisa = simpro_rap_analisa_apek.kode_analisa
				WHERE simpro_rap_analisa_apek.id_proyek = '".$idtender."'
				GROUP BY simpro_rap_analisa_apek.parent_kode_analisa
			) as tbl_harga_apek ON tbl_harga_apek.parent_kode_analisa = simpro_rap_analisa_daftar.kode_analisa
			WHERE 
				simpro_rap_analisa_daftar.id_proyek = '".$idtender."'
				AND simpro_rap_analisa_daftar.id_kat_analisa = '10'
		";

		if(isset($_REQUEST['query']))
		{
			$search = $_REQUEST['query'];
			$q_search = "AND (simpro_rap_analisa_daftar.nama_item @@ to_tsquery('".addslashes($search).":*') OR simpro_rap_analisa_daftar.kode_analisa @@ to_tsquery('".addslashes($search).":*'))";
		} else
		{
			$search = "";
			$q_search = "";			
		}		
		$qadd = "ORDER BY simpro_rap_analisa_daftar.kode_analisa ASC";
		$query .= $q_search . $qadd;
		
		$total_data = $this->db->query($query)->num_rows();
		
		$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
		$limit = $_REQUEST['limit'];		
		$limits = sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
		$query .= $limits;
		
		$rs = $this->db->query($query);
		foreach ($rs->result() as $row) {
			if ($row->jenis_analisa == 'APEK') {
				$q_kd = $this->db->query("select * from simpro_rap_analisa_apek where id_proyek = $idtender and parent_kode_analisa = '$row->kode_analisa' order by kode_analisa");
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
				'id_proyek' => $row->id_proyek, 
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
		$qwhere = is_numeric($kode) ? sprintf("WHERE simpro_tbl_detail_material.subbidang_kode = '%s'", addslashes($kode)) : "";
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

	function get_data_proyek()
	{
		$query = "SELECT proyek_id AS id_proyek, proyek AS nama_proyek from simpro_tbl_proyek";
		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		}
		$this->_out($data);		
	}
	
	function get_proyek_info($id)
	{
		$sql = sprintf("SELECT * FROM simpro_tbl_proyek WHERE proyek_id = '%d'", $id);	
		$data = $this->db->query($sql)->row_array();
		if(isset($data['proyek'])) return $data;
	}
	
}

?>