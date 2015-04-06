<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_Rab_Analisa extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}	

	function get_tree_item($idpro, $tree_parent=0,$param='') //pakai
	{
		$query = "
		select 
			x.*, 
			case when
			(select count(tahap_kode_kendali) from (select * from simpro_tbl_input_kontrak where lower(tahap_nama_kendali) like lower('%$param%') and proyek_id = $idpro and input_kontrak_id = x.input_kontrak_id) hj) > 0
			then
			1
			else
			0
			end as ktr,
			(x.tahap_kode_kendali || ' ' || x.tahap_nama_kendali) AS task,
			case when right(x.tahap_kode_induk_kendali,1) = '.' then
			left(x.tahap_kode_induk_kendali,(length(x.tahap_kode_induk_kendali)-1))
			else
			x.tahap_kode_induk_kendali
			end as xnm,
			(select a::int from (select a, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) as row from (select unnest as a from unnest ( string_to_array ( trim ( x.tahap_kode_kendali, '.'), '.' ) )) x) r order by row desc limit 1) as urut,
			case when sum(totals.subtotal) = 0 or x.tahap_volume_kendali = 0 then
			0
			else
			sum(totals.subtotal) / x.tahap_volume_kendali
			end as hrg
			,
			(
			sum(totals.subtotal)
			) as sub
			from
			simpro_tbl_input_kontrak x
			left join
			(select
			tahap_kode_kendali,
			(tahap_volume_kendali * tahap_harga_satuan_kendali) as subtotal
			from
			simpro_tbl_input_kontrak
			where proyek_id = $idpro) as totals
			on left(totals.tahap_kode_kendali || '.',length(x.tahap_kode_kendali || '.')) = x.tahap_kode_kendali || '.'
			WHERE x.proyek_id = $idpro
			AND x.tree_parent_id = $tree_parent
			group by x.input_kontrak_id			
			ORDER BY xnm, urut
			";
		$rs = $this->db->query($query);	
		return $rs;
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

	function total_rapa($idproyek) //pakai
	{
		$sql = sprintf("
			select
			sum(coalesce(tahap_volume_kendali,0) * coalesce(tahap_harga_satuan_kendali,0)) as subtotal
			from
			simpro_tbl_input_kontrak
			where proyek_id = '%d'		
			", $idproyek);
		$rs = $this->db->query($sql);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->row_array());
		} else return false;	
	}
}

?>