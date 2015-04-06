<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_pengendalian extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_tree_item($proyek_id, $tree_parent=0, $tgl_rab,$page)
	{
		switch ($page) {
			case 'kk':
				$id_info = 'id_kontrak_terkini';
				$where_tgl_info = 'tgl_akhir';
				$tbl_info = 'simpro_tbl_kontrak_terkini';
			break;
			case 'rkk':
				$id_info = 'id_rencana_kontrak_terkini';
				$where_tgl_info = 'tahap_tanggal_kendali';
				$tbl_info = 'simpro_tbl_rencana_kontrak_terkini';
			break;
		}

		if ($page == 'kk' || $page == 'rkk') {
			if ($tree_parent == 0) {
				$tambah = "and a.tahap_kode_induk_kendali = ''";
			} else {
				$tambah = "and a.tahap_kode_induk_kendali = '$tree_parent'";
			}
		} elseif ($page == 'lpf' || $page == 'rencana_kerja') {
			if ($tree_parent == 0) {
				$tambah = "and b.tahap_kode_induk_kendali = ''";
			} else {
				$tambah = "and b.tahap_kode_induk_kendali = '$tree_parent'";
			}
		}

		if ($page == 'kk' || $page == 'rkk') {
			$query ="
				with j as (SELECT 
					a.$id_info,
					b.rab_tahap_kode_kendali,
					b.rab_tahap_volume_kendali,
					b.rab_tahap_harga_satuan_kendali,
					b.jml_rab,
					a.tahap_kode_induk_kendali,
					a.tahap_kode_kendali,
					CASE WHEN a.tahap_volume_kendali is null
					THEN 0
					ELSE a.tahap_volume_kendali
					END,
					CASE WHEN a.tahap_harga_satuan_kendali is null
					THEN 0
					ELSE a.tahap_harga_satuan_kendali
					END,
					(
					(CASE WHEN a.tahap_volume_kendali is null
					THEN 0
					ELSE a.tahap_volume_kendali
					END) * 
					(CASE WHEN a.tahap_harga_satuan_kendali is null
					THEN 0
					ELSE a.tahap_harga_satuan_kendali
					END)
					) as jml_kontrak_kini,
					(
					(CASE WHEN a.tahap_volume_kendali is null
					THEN 0
					ELSE a.tahap_volume_kendali
					END) +
					(CASE WHEN a.tahap_volume_kendali_new is null
					THEN 0
					ELSE a.tahap_volume_kendali_new
					END) -
					(CASE WHEN a.tahap_volume_kendali_kurang is null
					THEN 0
					ELSE a.tahap_volume_kendali_kurang 
					END)
					) as vol_total,
					(
					(
					(CASE WHEN a.tahap_volume_kendali is null
					THEN 0
					ELSE a.tahap_volume_kendali
					END) +
					(CASE WHEN a.tahap_volume_kendali_new is null
					THEN 0
					ELSE a.tahap_volume_kendali_new
					END) -
					(CASE WHEN a.tahap_volume_kendali_kurang is null
					THEN 0
					ELSE a.tahap_volume_kendali_kurang 
					END)
					) * 
					(CASE WHEN a.tahap_harga_satuan_kendali is null
					THEN 0
					ELSE a.tahap_harga_satuan_kendali
					END)
					) as jml_total
					FROM
					$tbl_info a
					LEFT JOIN (
						select
							tahap_kode_kendali as rab_tahap_kode_kendali,
							tahap_nama_kendali as rab_tahap_nama_kendali,
							(select satuan_id from simpro_tbl_satuan where lower(satuan_nama) = lower(tahap_satuan_kendali)) as rab_tahap_satuan_kendali,
							coalesce(tahap_volume_kendali,0) as rab_tahap_volume_kendali,
							coalesce(tahap_harga_satuan_kendali,0) as rab_tahap_harga_satuan_kendali,
							(coalesce(tahap_volume_kendali,0) * coalesce(tahap_harga_satuan_kendali,0)) as jml_rab,
							proyek_id,
							tahap_kode_induk_kendali
							from 
							simpro_tbl_input_kontrak
							where proyek_id = $proyek_id
						) b 
					on a.proyek_id = b.proyek_id 
					and a.tahap_kode_kendali = b.rab_tahap_kode_kendali
					where a.proyek_id = $proyek_id and a.$where_tgl_info = '$tgl_rab'
					ORDER BY a.tahap_kode_kendali asc)

					SELECT 
					case when right(a.tahap_kode_induk_kendali,1) = '.' then
					left(a.tahap_kode_induk_kendali,(length(a.tahap_kode_induk_kendali)-1))
					else
					a.tahap_kode_induk_kendali
					end as xnm,
					(select a::int from (select a, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) as row from (select unnest as a from unnest ( string_to_array ( trim ( a.tahap_kode_kendali, '.'), '.' ) )) x) r order by row desc limit 1) as urut,
					a.$id_info as id_kontrak_terkini,
					a.tahap_kode_induk_kendali,
					b.rab_tahap_kode_kendali,
					b.rab_tahap_nama_kendali,
					b.rab_tahap_satuan_kendali,
					b.rab_tahap_volume_kendali,
					case when b.rab_tahap_volume_kendali = 0 then
					0
					else
					(
					(
					select
					sum(jml_rab)
					from
					j
					where
					left(j.tahap_kode_kendali || '.',length(b.rab_tahap_kode_kendali || '.')) = b.rab_tahap_kode_kendali || '.'
					group by left(j.tahap_kode_kendali || '.',length(b.rab_tahap_kode_kendali || '.'))
					)/
					(b.rab_tahap_volume_kendali)
					)
					end as rab_tahap_harga_satuan_kendali,
					(
					select
					sum(jml_rab)
					from
					j
					where
					left(j.tahap_kode_kendali || '.',length(b.rab_tahap_kode_kendali || '.')) = b.rab_tahap_kode_kendali || '.'
					group by left(j.tahap_kode_kendali || '.',length(b.rab_tahap_kode_kendali || '.'))
					) as jml_rab,
					a.tahap_kode_kendali,
					a.tahap_nama_kendali,
					a.tahap_satuan_kendali,
					CASE WHEN a.tahap_volume_kendali is null
					THEN 0
					ELSE a.tahap_volume_kendali
					END,
					case when a.tahap_volume_kendali = 0 then
					0
					else
					(
					(
					select
					sum(jml_kontrak_kini)
					from
					j
					where
					left(j.tahap_kode_kendali || '.',length(a.tahap_kode_kendali || '.')) = a.tahap_kode_kendali || '.'
					group by left(j.tahap_kode_kendali || '.',length(a.tahap_kode_kendali || '.'))
					)/
					(CASE WHEN a.tahap_volume_kendali is null
					THEN 0
					ELSE a.tahap_volume_kendali
					END)
					)
					end as tahap_harga_satuan_kendali,
					(
					select
					sum(jml_kontrak_kini)
					from
					j
					where
					left(j.tahap_kode_kendali || '.',length(a.tahap_kode_kendali || '.')) = a.tahap_kode_kendali || '.'
					group by left(j.tahap_kode_kendali || '.',length(a.tahap_kode_kendali || '.'))
					) as jml_kontrak_kini,
					CASE WHEN a.tahap_volume_kendali_new is null
					THEN 0
					ELSE a.tahap_volume_kendali_new
					END,
					(
					(CASE WHEN a.tahap_volume_kendali_new is null
					THEN 0
					ELSE a.tahap_volume_kendali_new
					END) *
					((CASE WHEN a.tahap_harga_satuan_kendali is null
					THEN 0
					ELSE a.tahap_harga_satuan_kendali
					END))
					) as jml_tambah,
					CASE WHEN a.tahap_volume_kendali_kurang is null
					THEN 0
					ELSE a.tahap_volume_kendali_kurang
					END,
					(
					(CASE WHEN a.tahap_volume_kendali_kurang is null
					THEN 0
					ELSE a.tahap_volume_kendali_kurang
					END) *
					(CASE WHEN a.tahap_harga_satuan_kendali is null
					THEN 0
					ELSE a.tahap_harga_satuan_kendali
					END)
					) as jml_kurang,
					CASE WHEN a.volume_eskalasi is null
					THEN 0
					ELSE a.volume_eskalasi
					END,
					CASE WHEN a.harga_satuan_eskalasi is null
					THEN 0
					ELSE a.harga_satuan_eskalasi
					END,
					(
					(CASE WHEN a.volume_eskalasi is null
					THEN 0
					ELSE a.volume_eskalasi
					END) * 
					(CASE WHEN a.harga_satuan_eskalasi is null
					THEN 0
					ELSE a.harga_satuan_eskalasi
					END)
					) as jml_eskalasi,
					(
					(CASE WHEN a.tahap_volume_kendali is null
					THEN 0
					ELSE a.tahap_volume_kendali
					END) +
					(CASE WHEN a.tahap_volume_kendali_new is null
					THEN 0
					ELSE a.tahap_volume_kendali_new
					END) -
					(CASE WHEN a.tahap_volume_kendali_kurang is null
					THEN 0
					ELSE a.tahap_volume_kendali_kurang 
					END)
					) as vol_total,
					(
					(
					(CASE WHEN a.tahap_volume_kendali is null
					THEN 0
					ELSE a.tahap_volume_kendali
					END) +
					(CASE WHEN a.tahap_volume_kendali_new is null
					THEN 0
					ELSE a.tahap_volume_kendali_new
					END) -
					(CASE WHEN a.tahap_volume_kendali_kurang is null
					THEN 0
					ELSE a.tahap_volume_kendali_kurang 
					END)
					) * 
					(CASE WHEN a.tahap_harga_satuan_kendali is null
					THEN 0
					ELSE a.tahap_harga_satuan_kendali
					END)
					) as jml_total,
					case when (select 
					count(n.$id_info) 
					from $tbl_info n
					where left(n.tahap_kode_kendali || '.',length(a.tahap_kode_kendali || '.')) = a.tahap_kode_kendali || '.'
					and n.proyek_id = $proyek_id
					and n.tahap_tanggal_kendali = '$tgl_rab') > 1 then
					0
					else
					1
					end as anak
					FROM
					$tbl_info a
					LEFT JOIN (
						select
							tahap_kode_kendali as rab_tahap_kode_kendali,
							tahap_nama_kendali as rab_tahap_nama_kendali,
							(select satuan_id from simpro_tbl_satuan where lower(satuan_nama) = lower(tahap_satuan_kendali)) as rab_tahap_satuan_kendali,
							coalesce(tahap_volume_kendali,0) as rab_tahap_volume_kendali,
							coalesce(tahap_harga_satuan_kendali,0) as rab_tahap_harga_satuan_kendali,
							(coalesce(tahap_volume_kendali,0) * coalesce(tahap_harga_satuan_kendali,0)) as jml_rab,
							proyek_id,
							tahap_kode_induk_kendali
							from 
							simpro_tbl_input_kontrak
							where proyek_id = $proyek_id
						) b 
					on a.proyek_id = b.proyek_id 
					and a.tahap_kode_kendali = b.rab_tahap_kode_kendali
					where a.proyek_id = $proyek_id and a.$where_tgl_info = '$tgl_rab'
					$tambah		
					ORDER BY xnm,urut asc
				";
		} elseif ($page == 'lpf') {
			$query="with j as (SELECT
				b.tahap_kode_kendali,
				(
				(CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)
				) as vol_kk,
				CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_lpf_kini
				FROM
				simpro_tbl_total_pekerjaan a 
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab' 
				ORDER BY b.tahap_kode_kendali)
				
				SELECT
				case when right(b.tahap_kode_induk_kendali,1) = '.' then
				left(b.tahap_kode_induk_kendali,(length(b.tahap_kode_induk_kendali)-1))
				else
				b.tahap_kode_induk_kendali
				end as xnm,
				(select a::int from (select a, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) as row from (select unnest as a from unnest ( string_to_array ( trim ( b.tahap_kode_kendali, '.'), '.' ) )) x) r order by row desc limit 1) as urut,
				a.id_tahap_pekerjaan,
				b.tahap_kode_kendali,
				b.tahap_nama_kendali,
				b.tahap_satuan_kendali,
				(
				select
				sum(jml_lpf_kini)
				from
				j
				where
				left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.')) = b.tahap_kode_kendali || '.'
				group by left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.'))
				) as jml_lpf_kini,
				(
				(CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)
				) as vol_kk,
				case when ((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) = 0 then
				0
				else
				(
				(select
				sum(jml_lpf_kini)
				from
				j
				where
				left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.')) = b.tahap_kode_kendali || '.'
				group by left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.'))) /
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END))
				) end as tahap_harga_satuan_kendali,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali < b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali < b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as jlm_sd_bln_lalu,
				a.tahap_diakui_bobot,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as jlm_sd_bln_ini,
				CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END,
				(
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_tagihan,
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) -
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END)
				) as vol_bruto,
				(
				((CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) -
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bruto,
				CASE WHEN a.tagihan_cair is null
				THEN 0
				ELSE a.tagihan_cair
				END,
				(
				(CASE WHEN a.tagihan_cair is null
				THEN 0
				ELSE a.tagihan_cair
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_cair,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(CASE WHEN a.tahap_diakui_bobot is null
				THEN 0
				ELSE a.tahap_diakui_bobot
				END)
				) as vol_sisa_pekerjaan,
				(
				(((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(CASE WHEN a.tahap_diakui_bobot is null
				THEN 0
				ELSE a.tahap_diakui_bobot
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_sisa_pekerjaan,
				CASE WHEN a.tagihan_rencana_piutang is null
				THEN 0
				ELSE a.tagihan_rencana_piutang
				END,
				case when (select 
				count(n.id_kontrak_terkini) 
				from simpro_tbl_total_pekerjaan m
				join simpro_tbl_kontrak_terkini n
				on m.kontrak_terkini_id = n.id_kontrak_terkini
				where left(n.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.')) = b.tahap_kode_kendali || '.'
				and m.proyek_id = $proyek_id
				and m.tahap_tanggal_kendali = '$tgl_rab') > 1 then
				0
				else
				1
				end as anak
				FROM
				simpro_tbl_total_pekerjaan a 
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab' 
				$tambah
				ORDER BY xnm,urut asc";
		} elseif ($page == 'rencana_kerja') {
			$query = "with j as (SELECT
				b.tahap_kode_kendali,
				(
				(CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)
				) as vol_kk,
				CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_rkp_kini
				FROM
				simpro_tbl_total_rkp a
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab'
				ORDER BY b.tahap_kode_kendali)

				SELECT
				case when right(b.tahap_kode_induk_kendali,1) = '.' then
				left(b.tahap_kode_induk_kendali,(length(b.tahap_kode_induk_kendali)-1))
				else
				b.tahap_kode_induk_kendali
				end as xnm,
				(select a::int from (select a, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) as row from (select unnest as a from unnest ( string_to_array ( trim ( b.tahap_kode_kendali, '.'), '.' ) )) x) r order by row desc limit 1) as urut,
				a.total_rkp_id,
				b.tahap_kode_kendali,
				b.tahap_nama_kendali,
				b.tahap_satuan_kendali,
				(
				(CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)
				) as vol_kk,
				(
				case when (
				select
				sum(jml_rkp_kini)
				from
				j
				where
				left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.')) = b.tahap_kode_kendali || '.'
				group by left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.'))
				) = 0
				then 0
				else
				(
				select
				sum(jml_rkp_kini)
				from
				j
				where
				left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.')) = b.tahap_kode_kendali || '.'
				group by left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.'))
				)/
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) end
				)  as tahap_harga_satuan_kendali,
				(
				select
				sum(jml_rkp_kini)
				from
				j
				where
				left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.')) = b.tahap_kode_kendali || '.'
				group by left(j.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.'))
				) as jml_rkp_kini,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as vol_sd_bln_ini,
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_sd_bln_ini,
				CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END,
				(
				(CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln1,
				CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END,
				(
				(CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln2,
				CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END,
				(
				(CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln3,
				CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END,
				(
				(CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln4,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END)+
				(CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END)+
				(CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END)+
				(CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END)+
				(CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END)
				)
				) as deviasi,
				case when (select 
				count(n.id_kontrak_terkini) 
				from simpro_tbl_total_pekerjaan m
				join simpro_tbl_kontrak_terkini n
				on m.kontrak_terkini_id = n.id_kontrak_terkini
				where left(n.tahap_kode_kendali || '.',length(b.tahap_kode_kendali || '.')) = b.tahap_kode_kendali || '.'
				and m.proyek_id = $proyek_id
				and m.tahap_tanggal_kendali = '$tgl_rab') > 1 then
				0
				else
				1
				end as anak
				FROM
				simpro_tbl_total_rkp a
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab'
				$tambah
				ORDER BY xnm, urut";
		}
		
		$rs = $this->db->query($query);	
		return $rs;
	}
		
	function getsubbidang()
	{
		$query = "select * from simpro_tbl_subbidang where subbidang_kode<>'509' and length(subbidang_kode)=3 order by urutan";
		$q = $this->db->query($query);
		return $q->result_object(); 
	}

	function getedithargasatuan($no_spk,$tgl_rab)
	{
		$query = "SELECT simpro_tbl_detail_material_kode,detail_material_nama,avg(komposisi_harga_satuan_kendali) as harga FROM qry_komposisi_budget where no_spk='$no_spk' and tahap_tanggal_kendali='$tgl_rab' group by detail_material_kode,detail_material_nama order by detail_material_kode asc";
		$q = $this->db->query($query);
		return $q->result_object(); 
	}

	function getsubbidangkode()
	{
		$query = "select * from simpro_tbl_subbidang";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->subbidang_id;
    		$data['text'] = $row->subbidang_name;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function getdivisicombo()
	{
		$query = "select * from simpro_tbl_divisi order by divisi_name";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->divisi_id;
    		$data['text'] = $row->divisi_name;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function getproyekcombo($divisi_kode)
	{
		$tgl_sekarang = date('Y-m-d');
		$query = "select * from simpro_tbl_proyek where simpro_tgl_pengumuman>='$tgl_sekarang' and divisi_kode='$divisi_kode' and (proyek_status='MENANG TENDER' or proyek_status='PENUNJUKAN')";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->proyek_id;
    		$data['text'] = $row->proyek;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function gettanggalcombo($no_spk)
	{
		$query = "select distinct tahap_tanggal_kendali from simpro_tbl_tahap_kendali where no_spk='$no_spk' order by tahap_tanggal_kendali desc";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->tahap_tanggal_kendali;
    		$data['text'] = $row->tahap_tanggal_kendali;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function updateschparent($id,$data)
	{
		$this->db->where('id', $id);
		$this->db->update('simpro_tbl_sch_proyek_parent',$data);
	}
	function insertschparent($data)
	{
		$this->db->insert('simpro_tbl_sch_proyek_parent',$data);
	}
	function schinsert($data)
	{
		$this->db->insert('simpro_tbl_sch_proyek',$data);
	}
	function getdata($tbl_info){
		switch ($tbl_info) {
			case 'simpro_tbl_kontrak_terkini':
				$query="select * from $tbl_info order by tahap_kode_kendali asc";
			break;
		}
		$q = $this->db->query($query);
		return $q->result_object();		
	}

	function getlistalat()
	{
		$query = "select * from simpro_tbl_master_peralatan";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->master_peralatan_id;
			$data['uraian_jenis_alat'] = $row->uraian_jenis_alat;
			$data['merk_model'] = $row->merk_model;
			$data['type_penggerak'] = $row->type_penggerak;
			$data['kapasitas'] = $row->kapasitas;
    		$data['text'] = $row->uraian_jenis_alat." ".$row->merk_model." ".$row->type_penggerak." ".$row->kapasitas;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function getlistsatuan()
	{
		$query = "select * from simpro_tbl_satuan";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->satuan_id;
    		$data['text'] = $row->satuan_nama;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function insert($tbl_info,$data)
	{
		$this->db->trans_begin();
		
		switch ($tbl_info) {
			case 'kontrak_terkini':

				$data_kontrak_terkini = array(
					'tahap_kode_kendali' => $data['kode'],
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => $data['volume'],
					'tahap_tanggal_kendali' => $data['tgl_awal'],
					'tgl_akhir' => $data['tgl_rab'],
					'user_update' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_update' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_kode_induk_kendali' => ''
				);

				$data_rencana_kontrak_terkini = array(
					'tahap_kode_kendali' => $data['kode'],
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => 0,
					'tahap_tanggal_kendali' => $data['tgl_rab'],
					'user_update' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_update' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_kode_induk_kendali' => ''
				);
				
				$data_current_budget = array(
					'tahap_kode_kendali' => '1.'.$data['kode'], 
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => $data['volume'],
					'tahap_kode_induk_kendali' => '1',
					'tahap_tanggal_kendali' => $data['tgl_awal'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_total_kendali' => 0,
					'user_id' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_id' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update']
				);

				$data_cost_to_go = array(
					'tahap_kode_kendali' => '1.'.$data['kode'], 
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => $data['volume'],
					'tahap_kode_induk_kendali' => '1',
					'tahap_tanggal_kendali' => $data['tgl_rab'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_total_kendali' => 0,
					'user_id' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_id' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update']
				);

				$tbl1='simpro_tbl_kontrak_terkini';
				$this->db->insert($tbl1,$data_kontrak_terkini);

				$last_id = $this->db->insert_id();
				$data_kk = array(
					'proyek_id' => $data['proyek_id'],
					'tahap_tanggal_kendali'=> $data['tgl_rab'],
					'kontrak_terkini_id' => $last_id
				);

				$tbl3='simpro_tbl_current_budget';
				$this->db->insert($tbl3,$data_current_budget);

				$tbl5='simpro_tbl_cost_togo';
				$this->db->insert($tbl5,$data_cost_to_go);

				$tbl2='simpro_tbl_total_pekerjaan';
				$this->db->insert($tbl2,$data_kk);

				$tbl4='simpro_tbl_total_rkp';
				$this->db->insert($tbl4,$data_kk);

				$tbl5='simpro_tbl_rencana_kontrak_terkini';
				$this->db->insert($tbl5,$data_rencana_kontrak_terkini);

			break;
			case 'sub_kontrak_terkini':
				$data_kontrak_terkini = array(
					'tahap_kode_kendali' => $data['kode'],
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => 0,
					'tahap_tanggal_kendali' => $data['tgl_awal'],
					'tgl_akhir' => $data['tgl_rab'],
					'user_update' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_update' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_kode_induk_kendali' => $data['kode_induk']
				);

				$data_rencana_kontrak_terkini = array(
					'tahap_kode_kendali' => $data['kode'],
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => 0,
					'tahap_tanggal_kendali' => $data['tgl_rab'],
					'user_update' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_update' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_kode_induk_kendali' => $data['kode_induk']
				);

				$data_current_budget = array(
					'tahap_kode_kendali' => '1.'.$data['kode'], 
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => 0,
					'tahap_kode_induk_kendali' => '1.'.$data['kode_induk'],
					'tahap_tanggal_kendali' => $data['tgl_awal'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_total_kendali' => 0,
					'user_id' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_id' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update']
				);

				$data_cost_to_go = array(
					'tahap_kode_kendali' => '1.'.$data['kode'], 
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => 0,
					'tahap_kode_induk_kendali' => '1.'.$data['kode_induk'],
					'tahap_tanggal_kendali' => $data['tgl_rab'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_total_kendali' => 0,
					'user_id' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_id' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update']
				);

				$tbl1='simpro_tbl_kontrak_terkini';
				$this->db->insert($tbl1,$data_kontrak_terkini);

				$last_id = $this->db->insert_id();
				$data_kk = array(
					'proyek_id' => $data['proyek_id'],
					'tahap_tanggal_kendali'=> $data['tgl_rab'],
					'kontrak_terkini_id' => $last_id
				);

				$tbl3='simpro_tbl_current_budget';
				$this->db->insert($tbl3,$data_current_budget);

				$tbl5='simpro_tbl_cost_togo';
				$this->db->insert($tbl5,$data_cost_to_go);

				$tbl2='simpro_tbl_total_pekerjaan';
				$this->db->insert($tbl2,$data_kk);

				$tbl4='simpro_tbl_total_rkp';
				$this->db->insert($tbl4,$data_kk);

				$tbl5='simpro_tbl_rencana_kontrak_terkini';
				$this->db->insert($tbl5,$data_rencana_kontrak_terkini);
			break;
			case 'rencana_kontrak_terkini':
				$data_kontrak_terkini = array(
					'tahap_kode_kendali' => $data['kode'],
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => $data['volume'],
					'tahap_tanggal_kendali' => $data['tgl_rab'],
					'user_update' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_update' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_kode_induk_kendali' => ''
				);
				$this->db->insert('simpro_tbl_rencana_kontrak_terkini',$data_kontrak_terkini);
			break;
			case 'sub_rencana_kontrak_terkini':
				$data_kontrak_terkini = array(
					'tahap_kode_kendali' => $data['kode'],
					'tahap_nama_kendali' => $data['tahap_pekerjaan'],
					'tahap_satuan_kendali' => $data['satuan'],
					'proyek_id' => $data['proyek_id'],
					'tahap_volume_kendali' => 0,
					'tahap_tanggal_kendali' => $data['tgl_rab'],
					'user_update' => $data['user_update'],
					'tgl_update' => $data['tgl_update'],
					'ip_update' => $data['ip_update'],
					'divisi_update' => $data['divisi_id'],
					'waktu_update' => $data['waktu_update'],
					'tahap_harga_satuan_kendali' => $data['harga_satuan'],
					'tahap_kode_induk_kendali' => $data['kode_induk']
				);
				$this->db->insert('simpro_tbl_rencana_kontrak_terkini',$data_kontrak_terkini);
			break;
		}


		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		}
		else
		{
		    $this->db->trans_commit();
		}
	}

	function get_data_kode($proyek_id,$tgl_rab)
	{
				$sql = "select * from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and tahap_tanggal_kendali='$tgl_rab' and tahap_kode_induk_kendali=''";
				$q = $this->db->query($sql);
				$res = $q->num_rows();
				$jum=$res;
				$data = $jum + 1;
				return $data;
	}

	function get_data_kontrak_terkini($proyek_id,$tgl_rab)
	{
		$sql="select * from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and tahap_tanggal_kendali='$tgl_rab' order by tahap_kode_kendali";
		$q = $this->db->query($sql);
		return $q->result_object();
	}

	function get_tanggal_rencana_kontrak_terkini($proyek_id)
	{

		$sql_jumah = "SELECT tahap_tanggal_kendali FROM simpro_tbl_rencana_kontrak_terkini where proyek_id = $proyek_id  GROUP BY tahap_tanggal_kendali order by tahap_tanggal_kendali asc";
		
		$q_jml = $this->db->query($sql_jumah);

		if ($q_jml->result()) {
			foreach ($q_jml->result() as $row_jml) {
				$sql = "select 
						a.tahap_tanggal_kendali,
						b.kuncitutup,
						(c.user_name) as username,
						CASE WHEN b.status is null
						    THEN 'open'
						ELSE b.status
						END
						from 
						simpro_tbl_rencana_kontrak_terkini a
						left join simpro_tbl_approve b on b.tgl_approve = a.tahap_tanggal_kendali  and b.form_approve='ALL' and b.proyek_id = $proyek_id
						left join simpro_tbl_user c on b.user_id = c.user_id
						WHERE a.tahap_tanggal_kendali = '$row_jml->tahap_tanggal_kendali'
						and a.proyek_id = $proyek_id
						group by tahap_tanggal_kendali, b.kuncitutup, c.user_name, b.proyek_id, b.status
						order by tahap_tanggal_kendali desc, b.proyek_id DESC";
				$q = $this->db->query($sql);

				$st_app ='';

				$q1 = $q->row_array(0);
				$q2 = $q->row_array(1);

				if ($q->result()) {
					if ($q->num_rows() >= 2 && $q1['tahap_tanggal_kendali'] == $q2['tahap_tanggal_kendali']) {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						}
						if ($q2['kuncitutup'] == '1') {
							$st_app.="<br />APPROVED BY ".$q2['username'];
						}
						if ($q1['kuncitutup'] == '0' && $q2['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						}
					} else {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						} elseif ($q1['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						} elseif ($q1['kuncitutup'] == '') {
							$st_app.="NOT APPROVE";
						}
					}

					$date = $q1['tahap_tanggal_kendali'];
					$chars = preg_split('[-]', $date, -1, PREG_SPLIT_DELIM_CAPTURE);
					$data['bln']= trim($chars[1]);
					$data['blnnama']= $this->bulan(trim($chars[1]));
					$data['thn']= trim($chars[0]);
					$data['tahap_tanggal_kendali']= $q1['tahap_tanggal_kendali'];
					$data['status']= $st_app;
					$data['kunci']=$q1['status'];
			    	$dat[] = $data;
				} else {
					$dat = "";
				}
			}
		} else {
			$dat = "";
		}
		
		return '{"data":'.json_encode($dat).'}';
	}

	function get_tanggal_kontrak_terkini($proyek_id)
	{

		$sql_jumah = "SELECT tgl_akhir FROM simpro_tbl_kontrak_terkini where proyek_id = $proyek_id GROUP BY tgl_akhir order by tgl_akhir asc";
		
		$q_jml = $this->db->query($sql_jumah);

		if ($q_jml->result()) {
			foreach ($q_jml->result() as $row_jml) {
				$sql = "select 
						a.tahap_tanggal_kendali,
						b.kuncitutup,
						(c.user_name) as username,
						CASE WHEN b.status is null
						    THEN 'open'
						ELSE b.status
						END,
						a.tgl_akhir,
						extract(month FROM a.tahap_tanggal_kendali) as blnnew,
						extract(year FROM a.tahap_tanggal_kendali) as thnnew
						from 
						simpro_tbl_kontrak_terkini a
						left join simpro_tbl_approve b on b.tgl_approve = a.tgl_akhir  and b.form_approve='ALL' and b.proyek_id = $proyek_id
						left join simpro_tbl_user c on b.user_id = c.user_id
						WHERE a.tgl_akhir = '$row_jml->tgl_akhir' and a.proyek_id = $proyek_id
						group by tahap_tanggal_kendali, b.kuncitutup, c.user_name, b.proyek_id, b.status, a.tgl_akhir
						order by tgl_akhir desc, b.proyek_id DESC";
				$q = $this->db->query($sql);

				$st_app ='';

				$q1 = $q->row_array(0);
				$q2 = $q->row_array(1);

				if ($q->result()) {
					if ($q->num_rows() >= 2 && $q1['tahap_tanggal_kendali'] == $q2['tahap_tanggal_kendali']) {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						}
						if ($q2['kuncitutup'] == '1') {
							$st_app.="<br />APPROVED BY ".$q2['username'];
						}
						if ($q1['kuncitutup'] == '0' && $q2['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						}
					} else {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						} elseif ($q1['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						} elseif ($q1['kuncitutup'] == '') {
							$st_app.="NOT APPROVE";
						}
					}

					$date = $q1['tgl_akhir'];
					$chars = preg_split('[-]', $date, -1, PREG_SPLIT_DELIM_CAPTURE);
					$data['bln']= trim($chars[1]);
					$data['blnnama']= $this->bulan(trim($chars[1])).' / '.trim($chars[0]);
					$data['blnnamanew']= $this->bulan(trim($q1['blnnew'])).' / '.$q1['thnnew'];
					$data['thn']= trim($chars[0]);
					$data['tahap_tanggal_kendali']= $q1['tahap_tanggal_kendali'];
					$data['status']= $st_app;
					$data['kunci']=$q1['status'];
					$data['tgl_akhir']=$q1['tgl_akhir'];
			    	$dat[] = $data;
				} else {
					$dat = "";
				}
			}
		} else {
			$dat = "";
		}
		
		return '{"data":'.json_encode($dat).'}';
	}

	function get_sub_kontrk_terkini($page,$proyek_id,$kode,$tgl_rab)
	{
		switch ($page) {
			case 'kk':
				$tbl_info = 'simpro_tbl_kontrak_terkini';
				$where_tgl_info = 'tgl_akhir';
			break;
			case 'rkk':
				$tbl_info = 'simpro_tbl_rencana_kontrak_terkini';
				$where_tgl_info = 'tahap_tanggal_kendali';
			break;
		}

		$subsql = "select
					tahap_kode_kendali,
					tahap_nama_kendali,
					tahap_satuan_kendali,
					CASE WHEN tahap_volume_kendali is null
					THEN 0
					ELSE tahap_volume_kendali
					END,
					CASE WHEN tahap_volume_kendali_new is null
					THEN 0
					ELSE tahap_volume_kendali_new
					END,
					(
					(CASE WHEN tahap_harga_satuan_kendali is null
					THEN 0
					ELSE tahap_harga_satuan_kendali
					END)*
					(CASE WHEN tahap_volume_kendali is null
					THEN 0
					ELSE tahap_volume_kendali
					END)
					) as harga_sub
					from $tbl_info
					where tahap_kode_induk_kendali='$kode' 
					and proyek_id=$proyek_id
					and $where_tgl_info='$tgl_rab' 
					order by tahap_kode_kendali";

		$q = $this->db->query($subsql);
		
		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}
		
		return $dat;
	}

	function get_sub_ctg($proyek_id,$kode,$tgl_rab,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$id_info = 'ctg_id';
				$tbl_info = 'simpro_tbl_cost_togo';
			break;
			case 'current_budget':
				$id_info = 'current_budget_id';
				$tbl_info = 'simpro_tbl_current_budget';
			break;
		}

		$subsql = "select * from $tbl_info
							where tahap_kode_induk_kendali='$kode' 
							and proyek_id=$proyek_id  
							and tahap_tanggal_kendali='$tgl_rab' 
							order by tahap_kode_kendali";

		$q = $this->db->query($subsql);
		$total=0;
		if ($q->result()) {
			foreach ($q->result() as $row) {
				$data['tahap_kode_kendali']=$row->tahap_kode_kendali;
				$data['tahap_nama_kendali']=$row->tahap_nama_kendali;
				$data['tahap_satuan_kendali']=$row->tahap_satuan_kendali;
				$data['tahap_volume_kendali']=$row->tahap_volume_kendali;
				$data['tahap_harga_satuan_kendali']=$row->tahap_harga_satuan_kendali;
				$data['harga_sub']=$data['tahap_harga_satuan_kendali']*$data['tahap_volume_kendali'];
				$data['ctg_id']=$row->$id_info;

				$dat[]=$data;
			}
		}
		
		return $dat;
	}

	function get_sub_kode($proyek_id,$tbl_info,$kode,$tgl_rab)
	{
		switch ($tbl_info) {
			case 'kontrak_terkini':
				$sql = "select count(tahap_kode_kendali) as jml from simpro_tbl_kontrak_terkini where tahap_kode_induk_kendali='$kode' and proyek_id=$proyek_id and tgl_akhir='$tgl_rab'";
			break;
			case 'cost_togo':
				$sql = "select count(tahap_kode_kendali) as jml from simpro_tbl_cost_togo where tahap_kode_induk_kendali='$kode' and proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab'";
			break;
			case 'current_budget':
				$sql = "select count(tahap_kode_kendali) as jml from simpro_tbl_current_budget where tahap_kode_induk_kendali='$kode' and proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab'";
			break;
			case 'rencana_kontrak_kini':
				$sql = "select count(tahap_kode_kendali) as jml from simpro_tbl_rencana_kontrak_terkini where tahap_kode_induk_kendali='$kode' and proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab'";
			break;
		}
		$q = $this->db->query($sql);

		foreach($q->result() as $row) {
			$jml = $row->jml;
			switch ($jml) {
			 	case '0':
			 		$jml = 1;
			 	break;			 	
			 	default:
			 		$jml = $jml +1;
			 	break;
			 } 
			$data['value'] = $jml;
			$dat[] = $data;
		}
		return $dat;
	}

	function get_kode($page,$proyek_id,$tgl_rab)
	{
		switch ($page) {
			case 'kk':
				$sql = "select count(tahap_kode_kendali) as jml from simpro_tbl_kontrak_terkini where tahap_kode_induk_kendali='' and proyek_id=$proyek_id and tgl_akhir='$tgl_rab'";
			break;
			case 'rkk':
				$sql = "select count(tahap_kode_kendali) as jml from simpro_tbl_rencana_kontrak_terkini where tahap_kode_induk_kendali='' and proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab'";
			break;
		}

		$q = $this->db->query($sql);

		foreach($q->result() as $row) {
			$jml = $row->jml;
			switch ($jml) {
			 	case '0':
			 		$jml = 1;
			 	break;			 	
			 	default:
			 		$jml = $jml +1;
			 	break;
			 } 
			$data['value'] = $jml;
			$dat[] = $data;
		}
		return $dat;
	}

	function get_kontrak_terkini_new($proyek_id,$tgl_rab)
	{

		$sqlterkini="select * from simpro_tbl_kontrak_terkini a join simpro_tbl_satuan b on a.tahap_satuan_kendali = b.satuan_id 
					where a.proyek_id='$proyek_id'
					and a.tahap_tanggal_kendali='$tgl_rab' order by tahap_kode_kendali";
		$qsqlterkini = $this->db->query($sqlterkini);

		if ($qsqlterkini->result()) {
			foreach ($qsqlterkini->result() as $rowterkini) {
			$kode_total = substr($rowterkini->tahap_kode_kendali, 0,1);
			$kode_t = $rowterkini->tahap_kode_kendali;
			$data['kode_terkini'] = $rowterkini->tahap_kode_kendali;
			$data['item_pekerjaan_terkini'] = $rowterkini->tahap_nama_kendali;
			$data['satuan_terkini'] = $rowterkini->satuan_nama;
			$data['volume_terkini'] = $rowterkini->tahap_volume_kendali;
			$data['harga_terkini'] = $rowterkini->tahap_harga_satuan_kendali;
			$data['jumlah_terkini'] = $rowterkini->tahap_volume_kendali*$rowterkini->tahap_harga_satuan_kendali;			
			
			
			$sql_get_total ="SELECT
			 sum(tahap_volume_kendali + 
				(CASE WHEN tahap_volume_kendali_new is null
			       THEN 0
			       ELSE tahap_volume_kendali_new
			  END) - 
				(CASE WHEN tahap_volume_kendali_kurang is null
			       THEN 0
			       ELSE tahap_volume_kendali_kurang
			  END)) as total_vol_kerja,
			sum((CASE WHEN tahap_harga_satuan_kendali is null
			       THEN 0
			       ELSE tahap_harga_satuan_kendali
			  END) * tahap_volume_kendali) as jumlah_harga_satuan,

			sum((tahap_volume_kendali + 
				(CASE WHEN tahap_volume_kendali_new is null
			       THEN 0
			       ELSE tahap_volume_kendali_new
			  END) - 
				(CASE WHEN tahap_volume_kendali_kurang is null
			       THEN 0
			       ELSE tahap_volume_kendali_kurang
			  END)) *
			(CASE WHEN tahap_harga_satuan_kendali is null
			       THEN 0
			       ELSE tahap_harga_satuan_kendali
			  END)
			) as total_harga_kerja
			FROM simpro_tbl_kontrak_terkini
			where proyek_id='$proyek_id'
			and tahap_tanggal_kendali='$tgl_rab'
			and left(tahap_kode_kendali,1) = '$kode_total'
			and tahap_kode_induk_kendali != ''
			GROUP BY left('$kode_total',1)";

			$q_total_kk = $this->db->query($sql_get_total);

			$row_total = $q_total_kk->row();
		
			$sql = "select * from simpro_tbl_input_kontrak a join simpro_tbl_satuan b on a.tahap_satuan_kendali = b.satuan_id 
					where a.proyek_id='$proyek_id' 
					and a.tahap_kode_kendali='$rowterkini->tahap_kode_kendali'";
			$q = $this->db->query($sql);
			if (!$q->result()) {
					$data['kode_kontrak'] = 0;
	                $data['uraian_kontrak']="-";
	                $data['satuan']="-";
	                $data['volume']=0;
	                $data['harga']=0;
	                $data['jumlah']=0;
					// $dat[] = $data;
			} else {
				foreach ($q->result() as $row) {
					$data['kode_kontrak'] = $row->tahap_kode_kendali;
					$data['uraian_kontrak']= $row->tahap_nama_kendali;
	                $data['satuan']= $row->satuan_nama;
	                $data['volume']= $row->tahap_volume_kendali;
	                $data['harga']= $row->tahap_harga_satuan_kendali;
	                $data['jumlah']= $row->tahap_volume_kendali*$row->tahap_harga_satuan_kendali;
					// $dat[] = $data;
				}
			}
			if ($rowterkini->tahap_volume_kendali_new==null) {				
				$data['volume_tambah'] = 0;
				$data['jumlah_tambah'] = 0;
			} else {
				$data['volume_tambah'] = $rowterkini->tahap_volume_kendali_new;
				$data['jumlah_tambah'] = 0;
			}

			if ($rowterkini->tahap_volume_kendali_kurang==null) {
				$data['volume_kurang'] = 0;
				$data['jumlah_kurang'] = 0;
			} else {
				$data['volume_kurang'] = $rowterkini->tahap_volume_kendali_kurang;
				$data['jumlah_kurang'] = 0;
			}

			switch ($rowterkini->volume_eskalasi) {
				case null: $data['volume_eskalasi'] = 0; break;				
				default: $data['volume_eskalasi'] = $rowterkini->volume_eskalasi; break;
			}

			switch ($rowterkini->harga_satuan_eskalasi) {
				case null: $data['harga_satuan_eskalasi'] = 0; break;				
				default: $data['harga_satuan_eskalasi'] = $rowterkini->harga_satuan_eskalasi; break;
			}

			$data['jumlah_eskalasi']=$data['volume_eskalasi']*$data['harga_satuan_eskalasi'];

			// if($data['volume_tambah']=='' or $data['volume_tambah']==0){
   //            	$data['tot_vol']=$data['volume_terkini'];
   //          }
   //          else if($data['volume_kurang']=='' or $data['volume_kurang']==0){
                
   //          }

			if (strlen($kode_t)==1) {
				$data['total_tambah_kurang'] = $row_total->total_harga_kerja;
				$data['tot_vol'] = $row_total->total_vol_kerja;
			} else {
            	$data['tot_vol']=$data['volume_terkini']+$data['volume_tambah']-$data['volume_kurang'];	
				switch ($rowterkini->total_tambah_kurang) {
					case null: $data['total_tambah_kurang'] = 0; break;				
					default: $data['total_tambah_kurang']= $rowterkini->total_tambah_kurang; break;
				} 
			}


                       

			$data['id_kontrak_terkini']=$rowterkini->id_kontrak_terkini;

			$dat[] = $data;
			}
		}

		return $dat;
	}

	function update_kk($page,$id,$data_kk,$data)
	{
		$this->db->trans_begin();

		$tgl_rab = $data['tgl_rab'];
		$proyek_id = $data['proyek_id'];
		$kode = $data['kode'];
		$jml_vol_kk = $data['jml_vol_kk'];

		switch ($page) {
			case 'kk':
				$sql_lpf_sd_bln_ini = "SELECT
						SUM(
						CASE WHEN d.tahap_diakui_bobot is null
						THEN 0
						ELSE d.tahap_diakui_bobot
						END) as value
						FROM
						simpro_tbl_total_pekerjaan d
						JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
						WHERE d.tahap_tanggal_kendali <= '$tgl_rab' AND e.tahap_kode_kendali='$kode' AND d.proyek_id = $proyek_id
						GROUP BY e.tahap_kode_kendali";

				$q_lpf_sd_bln_ini = $this->db->query($sql_lpf_sd_bln_ini);
				$row_lpf_sd_bln_ini = $q_lpf_sd_bln_ini->row();
				$lpf_sd_bln_ini = $row_lpf_sd_bln_ini->value;

				$var_where = array(
					'id_proyek' => $proyek_id,
					'kode_tree' => '1.'.$kode,
					'tanggal_kendali' => $tgl_rab
				);

				$var_where_rkk = array(
					'proyek_id' => $proyek_id,
					'tahap_kode_kendali' => $kode,
					'tahap_tanggal_kendali' => $tgl_rab
				);

				$var_set_ctg = array(
					'volume' => $jml_vol_kk - $lpf_sd_bln_ini, 
				);

				$var_set_cb = array(
					'volume' => $jml_vol_kk, 
				);

				$var_set_rrk = array(
					'tahap_volume_kendali' => $jml_vol_kk, 
				);

				$this->db->where($var_where);
				$this->db->update('simpro_costogo_item_tree',$var_set_ctg);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_item_tree',$var_set_cb);
				$this->db->where($var_where_rkk);
				$this->db->update('simpro_tbl_rencana_kontrak_terkini',$var_set_rrk);

				$this->db->where('id_kontrak_terkini',$id);
				$this->db->update('simpro_tbl_kontrak_terkini',$data_kk);
			break;
			case 'rkk':
				$this->db->where('id_rencana_kontrak_terkini',$id);
				$this->db->update('simpro_tbl_rencana_kontrak_terkini',$data_kk);
			break;
		}

		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		}
		else
		{
		    $this->db->trans_commit();
		}
	}

	function update_lpf($id,$data_lpf,$data)
	{
		$this->db->trans_begin();

		$kode = $data['kode'];
		$proyek_id = $data['proyek_id'];
		$tgl_rab = $data['tgl_rab'];

		$this->db->where('id_tahap_pekerjaan',$id);
		$this->db->update('simpro_tbl_total_pekerjaan',$data_lpf);

		$sql_lpf_sd_bln_ini = "SELECT
				(
				(CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END) +
				(CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END) -
				(CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang
				END)
				) as value_cb,
				(
				(
				(CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END) +
				(CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END) -
				(CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang
				END)
				) -
				(SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= '$tgl_rab' AND e.tahap_kode_kendali='$kode' AND d.proyek_id = $proyek_id
				GROUP BY e.tahap_kode_kendali)) as value_ctg
				FROM
				simpro_tbl_kontrak_terkini a
				WHERE tahap_tanggal_kendali <= '$tgl_rab' AND tahap_kode_kendali='$kode' AND proyek_id = $proyek_id";

		$q_lpf_sd_bln_ini = $this->db->query($sql_lpf_sd_bln_ini);
		$row_lpf_sd_bln_ini = $q_lpf_sd_bln_ini->row();
		$tot_vol_ctg = $row_lpf_sd_bln_ini->value_ctg;
		$tot_vol_cb = $row_lpf_sd_bln_ini->value_cb;

		$var_where = array(
			'id_proyek' => $proyek_id,
			'kode_tree' => '1.'.$kode,
			'tanggal_kendali' => $tgl_rab
		);

		$var_where_rkk = array(
			'proyek_id' => $proyek_id,
			'tahap_kode_kendali' => $kode,
			'tahap_tanggal_kendali' => $tgl_rab
		);

		$var_set_ctg = array(
			'volume' => $tot_vol_ctg, 
		);

		$var_set_cb = array(
			'volume' => $tot_vol_cb, 
		);

		$var_set_rkk = array(
			'tahap_volume_kendali' => $tot_vol_cb, 
		);

		var_dump($row_lpf_sd_bln_ini);

		$this->db->where($var_where);
		$this->db->update('simpro_costogo_item_tree',$var_set_ctg);
		$this->db->where($var_where);
		$this->db->update('simpro_current_budget_item_tree',$var_set_cb);
		$this->db->where($var_where_rkk);
		$this->db->update('simpro_tbl_rencana_kontrak_terkini',$var_set_rkk);

		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		}
		else
		{
		    $this->db->trans_commit();
		}
	}

	function get_tgl($proyek_id,$info){
		switch ($info) {
			case 'lpf':
				$sql = "select 
				b.tahap_tanggal_kendali,
				c.kuncitutup,
				(d.user_name) as username,
				c.status
				FROM simpro_tbl_total_pekerjaan a JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				left join simpro_tbl_approve c on c.tgl_approve = b.tahap_tanggal_kendali  and c.form_approve='ALL' and c.proyek_id = $proyek_id
				left join simpro_tbl_user d on c.user_id = d.user_id
				group by b.tahap_tanggal_kendali, c.kuncitutup, d.user_name, c.proyek_id, c.status
				order by b.tahap_tanggal_kendali desc, c.proyek_id DESC";
			break;
			case 'rkk':
				$sql = "select 
				b.tahap_tanggal_kendali,
				c.kuncitutup,
				(d.user_name) as username,
				c.status
				FROM simpro_tbl_total_rkp a JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				left join simpro_tbl_approve c on c.tgl_approve = b.tahap_tanggal_kendali  and c.form_approve='ALL' and c.proyek_id = $proyek_id
				left join simpro_tbl_user d on c.user_id = d.user_id
				group by b.tahap_tanggal_kendali, c.kuncitutup, d.user_name, c.proyek_id, c.status
				order by b.tahap_tanggal_kendali desc, c.proyek_id DESC";
			break;
			case 'rencana_kontrak':
				$sql = "select 
				a.tahap_tanggal_kendali,
				b.kuncitutup,
				(c.user_name) as username,
				b.status
				from 
				simpro_tbl_kontrak_terkini a
				left join simpro_tbl_approve b on b.tgl_approve = a.tahap_tanggal_kendali  and b.form_approve='ALL' and b.proyek_id = $proyek_id
				left join simpro_tbl_user c on b.user_id = c.user_id
				where a.proyek_id = $proyek_id
				group by tahap_tanggal_kendali, b.kuncitutup, c.user_name, b.proyek_id, b.status
				order by tahap_tanggal_kendali desc, b.proyek_id DESC";
			break;
			case 'rpbk':
				$sql = "select 
				a.tahap_tanggal_kendali,
				b.kuncitutup,
				(c.user_name) as username,
				b.status
				from 
				simpro_tbl_kontrak_terkini a
				left join simpro_tbl_approve b on b.tgl_approve = a.tahap_tanggal_kendali  and b.form_approve='ALL' and b.proyek_id = $proyek_id
				left join simpro_tbl_user c on b.user_id = c.user_id
				group by tahap_tanggal_kendali, b.kuncitutup, c.user_name, b.proyek_id, b.status
				order by tahap_tanggal_kendali desc, b.proyek_id DESC";
			break;
		}
		

		$q = $this->db->query($sql);

		$st_app ='';

		$q1 = $q->row_array(0);
		$q2 = $q->row_array(1);

		if ($q->result()) {
			if ($q->num_rows() >= 2) {
				if ($q1['kuncitutup'] == '1') {
					$st_app.="APPROVED BY ".$q1['username'];
				}
				if ($q2['kuncitutup'] == '1') {
					$st_app.="<br />APPROVED BY ".$q2['username'];
				}
				if ($q1['kuncitutup'] == '0' && $q2['kuncitutup'] == '0') {
					$st_app.="NOT APPROVE";
				}
			} else {
				if ($q1['kuncitutup'] == '1') {
					$st_app.="APPROVED BY ".$q1['username'];
				} elseif ($q1['kuncitutup'] == '0') {
					$st_app.="NOT APPROVE";
				} elseif ($q1['kuncitutup'] == '') {
					$st_app.="NOT APPROVE";
				}
			}
			// foreach($q->result() as $row) {
			$date = $q1['tahap_tanggal_kendali'];
			$chars = preg_split('[-]', $date, -1, PREG_SPLIT_DELIM_CAPTURE);
			$data['bln']= trim($chars[1]);
			$data['blnnama']= $this->bulan(trim($chars[1]));
			$data['thn']= trim($chars[0]);
			$data['tahap_tanggal_kendali']= $date;
			$data['status']= $st_app;
			$data['kunci']=$q1['status'];

	    	$dat[] = $data;
		} else {
			$dat = "";
		}


		return $dat;

	}

	function get_data_lpf_induk($proyek_id,$tgl_rab)
	{
		$proyek_id = $proyek_id;

		$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
		$bln2= trim($chars[1]);
		$thn2= trim($chars[0]);

		if($bln2=="1"){
		$bln2=12;
		$thn2=$thn2-1;
		$tgl2="$thn2-$bln2-01";
		}else{
		$bln2=$bln2-1;
		$tgl2="$thn2-$bln2-01";
		}

		$sql_r = "select * from simpro_tbl_total_pekerjaan a join simpro_tbl_satuan b on a.tahap_satuan_kendali = b.satuan_id 
		where a.proyek_id='$proyek_id' 
		and a.tahap_tanggal_kendali='$tgl_rab' 
		order by a.length(tahap_kode_kendali),a.tahap_kode_kendali";

		$query_sql_r = $this->db->query($sql_r);

		if ($query_sql_r->result()) {
			foreach ($query_sql_r->result() as $r) {
				$data['id_tahap_pekerjaan'] = $r->id_tahap_pekerjaan;
				$data['tahap_kode_induk_kendali'] = $r->tahap_kode_induk_kendali;
				$key = $data['tahap_kode_induk_kendali'];
				$data['tahap_kode_kendali'] = $r->tahap_kode_kendali;
				$data['tahap_nama_kendali'] = $r->tahap_nama_kendali;
				$data['tahap_satuan_kendali'] = $r->satuan_nama;
				$data['tahap_volume_kendali'] = $r->tahap_volume_kendali;
				$data['tahap_harga_satuan_kendali'] = $r->tahap_harga_satuan_kendali;
				$data['tahap_total_kendali'] = $r->tahap_total_kendali;
				$data['tahap_volume_kendali_new'] = $r->tahap_volume_kendali_new;
				$data['tahap_harga_satuan_kendali_new'] = $r->tahap_harga_satuan_kendali_new;
				$data['tahap_diakui_bobot'] = $r->tahap_diakui_bobot;
				$data['tahap_diakui_jumlah'] = $r->tahap_diakui_jumlah;
				$data['tahap_total_kendali_new'] = $r->tahap_total_kendali_new;
				$data['tagihan_cair'] = $r->tagihan_cair;
				$data['vol_total_tagihan'] = $r->vol_total_tagihan;
				$data['tagihan_rencana_piutang'] = $r->tagihan_rencana_piutang;

				$sql_b="select 
				tahap_total_kendali as total,
				sum(tahap_diakui_jumlah) as total_jumlah,
				sum(tahap_diakui_bobot) as total_bobot, 
				sum(tahap_volume_kendali_new) as total_volume_kendali_new, 
				sum(tagihan_cair) as total_tagihan_cair, 
				sum(tahap_volume_kendali) as total_tahap_volume_kendali, 
				sum(tahap_harga_satuan_kendali) as total_tahap_harga_satuan_kendali, 
				sum(vol_total_tagihan) as total_vol_total_tagihan, 
				sum(tagihan_rencana_piutang) as tot_tagihan_rencana_piutang 
				from simpro_tbl_total_pekerjaan 
				where proyek_id='$proyek_id'
				and tahap_tanggal_kendali='$tgl_rab' 
				and tahap_kode_kendali='$key' 
				group by tahap_total_kendali";//echo $sqlerd;

				$query_sql_b = $this->db->query($sql_b);

				if ($query_sql_b->result()) {
				foreach ($query_sql_b->result() as $b) {
					$data['total'] = $b->total;
					$data['total_jumlah'] = $b->total_jumlah;
					$data['total_bobot'] = $b->total_bobot;
					$data['total_volume_kendali_new'] = $b->total_volume_kendali_new;
					$data['total_tagihan_cair'] = $b->total_tagihan_cair;
					$data['total_tahap_volume_kendali'] = $b->total_tahap_volume_kendali;
					$data['total_tahap_harga_satuan_kendali'] = $b->total_tahap_harga_satuan_kendali;
					$data['total_vol_total_tagihan'] = $b->total_vol_total_tagihan;
					$data['tot_tagihan_rencana_piutang'] = $b->tot_tagihan_rencana_piutang;
				}
				} else {
					$data['total'] = 0;
					$data['total_jumlah'] = 0;
					$data['total_bobot'] = 0;
					$data['total_volume_kendali_new'] = 0;
					$data['total_tagihan_cair'] = 0;
					$data['total_tahap_volume_kendali'] = 0;
					$data['total_tahap_harga_satuan_kendali'] = 0;
					$data['total_vol_total_tagihan'] = 0;
					$data['tot_tagihan_rencana_piutang'] = 0;
				}

				$sql_sumtotprog="select 
				tahap_diakui_bobot as total_bobot_progress 
				from simpro_tbl_total_pekerjaan 
				where proyek_id='$proyek_id' 
				and tahap_tanggal_kendali <= '$tgl2' 
				and tahap_kode_kendali='$key'";//echo $sqlerd;
		    
				$query_sql_sumtotprog = $this->db->query($sql_sumtotprog);

				if ($query_sql_sumtotprog->result()) {			
				foreach ($query_sql_sumtotprog->result() as $sumtotprog) {
					$data['total_bobot_progress'] = $sumtotprog->total_bobot_progress;
				}
				} else {
					$data['total_bobot_progress'] = 0;
				}

		    	$sql_sumtotprogblnini="select 
		    	tahap_diakui_bobot as total_bobot_bln_ini 
		    	from simpro_tbl_total_pekerjaan 
		    	where proyek_id='$proyek_id' 
		    	and tahap_tanggal_kendali <= '$tgl_rab' 
		    	and tahap_kode_induk_kendali='$key'";//echo $sqlerd;

				$query_sql_sumtotprogblnini = $this->db->query($sql_sumtotprogblnini);
		    
		    	if ($query_sql_sumtotprogblnini->result()) {
		    	foreach ($query_sql_sumtotprogblnini->result() as $sumtotprogblnini) {
		    		$data['total_bobot_bln_ini'] = $sumtotprogblnini->total_bobot_bln_ini;
		    	}
		    	} else {
		    		$data['total_bobot_bln_ini'] = 0;
		    	}

		    	$sql_sumtotprogblniniall="select 
		    	sum(tahap_diakui_bobot) as total_bobot_bln_ini_all
		    	from simpro_tbl_total_pekerjaan 
		    	where proyek_id='$proyek_id' 
		    	and tahap_tanggal_kendali <= '$tgl_rab' 
		    	and tahap_kode_induk_kendali='$key'
		    	group by tahap_kode_kendali 
		    	order by length(tahap_kode_kendali),tahap_kode_kendali";//echo $sqlerd;


				$query_sql_sumtotprogblniniall = $this->db->query($sql_sumtotprogblniniall);
		    
		    	if ($query_sql_sumtotprogblniniall->result()) {
		    	foreach ($query_sql_sumtotprogblniniall->result() as $sumtotprogblniniall) {
		    		$data['total_bobot_bln_ini_all'] = $sumtotprogblniniall->total_bobot_bln_ini_all;
		    	}
		    	} else {
		    		$data['total_bobot_bln_ini_all'] = 0;
		    	}

		    	$sql_sumtotalbruto="select 
		    	sum(tahap_volume_kendali_new * tahap_harga_satuan_kendali ) as total_harga_bruto 
		    	from simpro_tbl_total_pekerjaan 
		    	where proyek_id='$proyek_id' 
		    	and tahap_tanggal_kendali='$tgl_rab' 
		    	and tahap_kode_induk_kendali ='$key'
		    	group by tahap_kode_induk_kendali";

				$query_sql_sumtotalbruto = $this->db->query($sql_sumtotalbruto);
		    
		    	if ($query_sql_sumtotalbruto->result()) {
		    	foreach ($query_sql_sumtotalbruto->result() as $sumtotalbruto) {
		    		$data['total_harga_bruto'] = $sumtotalbruto->total_harga_bruto;
		    	}
		    	} else {
		    		$data['total_harga_bruto'] = 0;
		    	}

		    	$sql_sumtotaltagihancair="select 
		    	sum(tagihan_cair * tahap_harga_satuan_kendali ) as total_harga_tagihan_cair 
		    	from simpro_tbl_total_pekerjaan 
		    	where proyek_id='$proyek_id' 
		    	and tahap_tanggal_kendali='$tgl_rab' 
		    	and tahap_kode_induk_kendali ='$key'
		    	group by tahap_kode_induk_kendali";

				$query_sql_sumtotaltagihancair = $this->db->query($sql_sumtotaltagihancair);

				if ($query_sql_sumtotaltagihancair->result()) {
		    	foreach ($query_sql_sumtotaltagihancair->result() as $sumtotaltagihancair) {
		    		$data['total_harga_tagihan_cair'] = $sumtotaltagihancair->total_harga_tagihan_cair;
		    	}
		    	} else {
		    		$data['total_harga_tagihan_cair'] = 0;
		    	}

		    	$sql_sumtotalvolsisapekerjaan="select 
		    	(tahap_volume_kendali - tahap_diakui_bobot ) as total_vol_sisa_pekerjaan 
		    	from simpro_tbl_total_pekerjaan 
		    	where proyek_id='$proyek_id' 
		    	and tahap_tanggal_kendali='$tgl_rab' 
		    	and tahap_kode_kendali ='$key'";

				$query_sql_sumtotalvolsisapekerjaan = $this->db->query($sql_sumtotalvolsisapekerjaan);
		    	
		    	if ($query_sql_sumtotalvolsisapekerjaan->result()) {
		    	foreach ($query_sql_sumtotalvolsisapekerjaan->result() as $sumtotalvolsisapekerjaan) {
		    		$data['total_vol_sisa_pekerjaan'] = $sumtotalvolsisapekerjaan->total_vol_sisa_pekerjaan;
		    	}
		    	} else {
		    		$data['total_vol_sisa_pekerjaan'] = 0;
		    	}

		    	$sql_jumlahsisapekerjaan="select 
		    	sum((tahap_volume_kendali - tahap_diakui_bobot) * tahap_harga_satuan_kendali ) as total_jumlah_sisa_pekerjaan 
		    	from simpro_tbl_total_pekerjaan 
		    	where proyek_id='$proyek_id' 
		    	and tahap_tanggal_kendali='$tgl_rab' 
		    	and tahap_kode_induk_kendali ='$key'
		    	group by tahap_kode_induk_kendali";
		    
				$query_sql_jumlahsisapekerjaan = $this->db->query($sql_jumlahsisapekerjaan);
				
				if ($query_sql_jumlahsisapekerjaan->result()) {
		    	foreach ($query_sql_jumlahsisapekerjaan->result() as $jumlahsisapekerjaan) {
		    		$data['total_jumlah_sisa_pekerjaan'] = $jumlahsisapekerjaan->total_jumlah_sisa_pekerjaan;
		    	}
		    	} else {
		    		$data['total_jumlah_sisa_pekerjaan'] = 0;
		    	}

				$dat[] = $data;
			}
			return $dat;
		}
		
		// return $query_sql_b->result_object();
		// return $query_sql_b->result_object();
		// return $query_sql_sumtotprog->result_object();
		// return $query_sql_sumtotprogblnini->result_object();
		// return $query_sql_sumtotprogblniniall->result_object();
		// return $query_sql_sumtotalbruto->result_object();
		// return $query_sql_sumtotaltagihancair->result_object();
		// return $query_sql_sumtotalvolsisapekerjaan->result_object();
		// return $query_sql_jumlahsisapekerjaan->result_object();
    
	}

	function get_data_lpf($proyek_id,$tgl_rab)
	{
		$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
		$bln2= trim($chars[1]);
		$thn2= trim($chars[0]);

		if($bln2=="1"){
		$bln2=12;
		$thn2=$thn2-1;
		$tgl2="$thn2-$bln2-01";
		}else{
		$bln2=$bln2-1;
		$tgl2="$thn2-$bln2-01";
		}

		$sql_r = "SELECT 
		a.id_tahap_pekerjaan,
		b.tahap_kode_induk_kendali,
		b.tahap_kode_kendali,
		b.tahap_nama_kendali,
		c.satuan_nama,
		b.tahap_volume_kendali,
		b.tahap_harga_satuan_kendali,
		b.tahap_total_kendali,
		a.tahap_volume_kendali_new,
		a.tahap_harga_satuan_kendali_new,
		a.tahap_diakui_bobot,
		a.tahap_diakui_jumlah,
		a.tahap_total_kendali_new,
		a.tagihan_cair,
		a.vol_total_tagihan,
		a.tagihan_rencana_piutang
		FROM simpro_tbl_total_pekerjaan a JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini join simpro_tbl_satuan c on b.tahap_satuan_kendali = c.satuan_id 
		where b.proyek_id=$proyek_id
		and b.tahap_tanggal_kendali='$tgl_rab' 
		order by b.tahap_kode_kendali";

		$query_sql_r = $this->db->query($sql_r);

		if ($query_sql_r->result()) {
			foreach ($query_sql_r->result() as $r) {
				$data['id_tahap_pekerjaan'] = $r->id_tahap_pekerjaan;
				$data['tahap_kode_induk_kendali'] = $r->tahap_kode_induk_kendali;
				$data['tahap_kode_kendali'] = $r->tahap_kode_kendali;
				$key = $data['tahap_kode_kendali'];
				$data['tahap_nama_kendali'] = $r->tahap_nama_kendali;
				$data['tahap_satuan_kendali'] = $r->satuan_nama;
				$data['tahap_volume_kendali'] = $r->tahap_volume_kendali;
				$data['tahap_harga_satuan_kendali'] = $r->tahap_harga_satuan_kendali;
				$data['tahap_total_kendali'] = $r->tahap_total_kendali;
				$data['tahap_volume_kendali_new'] = $r->tahap_volume_kendali_new;
				$data['tahap_harga_satuan_kendali_new'] = $r->tahap_harga_satuan_kendali_new;
				$data['tahap_diakui_bobot'] = $r->tahap_diakui_bobot;
				$data['tahap_diakui_jumlah'] = $r->tahap_diakui_jumlah;
				$data['tahap_total_kendali_new'] = $r->tahap_total_kendali_new;
				$data['tagihan_cair'] = $r->tagihan_cair;
				$data['vol_total_tagihan'] = $r->vol_total_tagihan;
				$data['tagihan_rencana_piutang'] = $r->tagihan_rencana_piutang;

				// $sqlk="select * from tbl_total_pekerjaan 
				// where proyek_id='$proyek_id' 
				// and tahap_tanggal_kendali='$tgl2' 
				// and tahap_kode_kendali='$key' 
				// order by length(tahap_kode_kendali),tahap_kode_kendali";

				// $query_sqlk = $this->db->query($sqlk);
				// if ($query_sqlk->result) {
				// 	foreach ($query_sqlk->result as $sqlk) {

				// 	}
				// }

				$sqlk22="select sum(tahap_diakui_bobot) as total_progress_bln_lalu 
				from simpro_tbl_total_pekerjaan 
				where proyek_id='$proyek_id' 
				and tahap_tanggal_kendali <= '$tgl2' 
				and tahap_kode_kendali='$key' 
				group by tahap_kode_kendali 
				order by length(tahap_kode_kendali),tahap_kode_kendali";

				$query_sqlk22 = $this->db->query($sqlk22);
				if ($query_sqlk22->result()) {
					foreach ($query_sqlk22->result() as $sqlk22) {
						$data['total_progress_bln_lalu']=$sqlk22->total_progress_bln_lalu;
					}
				} else {
					$data['total_progress_bln_lalu']=0;
				}
        
        		$sqlsdblnini="select sum(tahap_diakui_bobot) as total_progress_bln_ini 
        		from simpro_tbl_total_pekerjaan 
        		where proyek_id='$proyek_id' 
        		and tahap_tanggal_kendali <= '$tgl_rab' 
        		and tahap_kode_kendali='$key' 
        		group by tahap_kode_kendali 
        		order by length(tahap_kode_kendali),tahap_kode_kendali";

        		$query_sqlsdblnini = $this->db->query($sqlsdblnini);
				if ($query_sqlsdblnini->result()) {
					foreach ($query_sqlsdblnini->result() as $sqlsdblnini) {
						$data['total_progress_bln_ini']=$sqlsdblnini->total_progress_bln_ini;
					}
				} else {
						$data['total_progress_bln_ini']=0;
				}

				$jumlahtotaltagihan=$data['tahap_harga_satuan_kendali']*$data['vol_total_tagihan'];
		        $jumlahvoltotalbruto=$data['total_progress_bln_ini']-$data['vol_total_tagihan'];
		        $jumlahhargatotalbruto=$data['tahap_harga_satuan_kendali']*$jumlahvoltotalbruto;
		        $jumlah1=$data['tahap_harga_satuan_kendali']*$data['tahap_harga_satuan_kendali_new'];
		        $jumlahtagihancair=$data['tahap_harga_satuan_kendali']*$data['tagihan_cair'];
		        $totalx=$data['tahap_harga_satuan_kendali_new']*$data['tahap_harga_satuan_kendali'];
		        $sel_vol=$data['tahap_volume_kendali']-$data['total_progress_bln_ini'];
		        $jumlahvolkerja=$data['tahap_harga_satuan_kendali']*$sel_vol;
	    		
		    	$data['total_vol1']=$data['total_progress_bln_lalu'];
	            $data['total_vol2']=$data['total_progress_bln_ini'];
	            $data['total_vol3']=$jumlahtotaltagihan;
	            $data['total_vol4']=$jumlahvoltotalbruto;
	            $data['total_harga1']=$jumlahhargatotalbruto;
	            $data['total_harga2']=$jumlahhargatotalbruto;
	            $data['tagihan_cair_ind']=$jumlahtagihancair;
	            $data['sel_vol_ind'] =$sel_vol;
	            $data['jumlah_vol_kerja_ind'] =$jumlahvolkerja;
	            $data['jumlah']= $data['tahap_volume_kendali']*$data['tahap_harga_satuan_kendali'];

				$dat[] = $data;
			}
		} else {
				$dat[] = "";
		}
		return $dat;
	}

	function get_data_mos($proyek_id)
	{
		$sql="select 
				(b.detail_material_nama) as mos_uraian,
				(b.detail_material_satuan) as mos_satuan,
				(a.mos_diakui_volume) as mos_diakui_volume,
				(a.mos_total_harsat) as mos_diakui_harsat,
				(a.mos_diakui_volume * a.mos_total_harsat) as mos_diakui_jumlah
				from 
				simpro_tbl_mos a join simpro_tbl_detail_material b on a.detail_material_id = b.detail_material_id
				where proyek_id=$proyek_id";

		$q = $this->db->query($sql);
		
		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat="";

		}
		return $dat;
	}

	function get_data_cost_to_go($proyek_id,$tgl_rab,$info){

		switch ($info) {
			case 'cost_togo':
				$id_info = 'ctg_id';
				$tbl_info = 'simpro_tbl_cost_togo';
				$tbl_info_komposisi = 'simpro_tbl_komposisi_togo';
				// $kurangan = '(CASE WHEN (SELECT
				// SUM(
				// CASE WHEN d.tahap_diakui_bobot is null
				// THEN 0
				// ELSE d.tahap_diakui_bobot
				// END)
				// FROM
				// simpro_tbl_total_pekerjaan d
				// JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				// WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				// GROUP BY e.tahap_kode_kendali) is null
				// THEN 0
				// ELSE (SELECT
				// SUM(
				// CASE WHEN d.tahap_diakui_bobot is null
				// THEN 0
				// ELSE d.tahap_diakui_bobot
				// END)
				// FROM
				// simpro_tbl_total_pekerjaan d
				// JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				// WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				// GROUP BY e.tahap_kode_kendali)
				// END)';
			break;
			case 'current_budget':
				$id_info = 'current_budget_id';
				$tbl_info = 'simpro_tbl_current_budget';
				$tbl_info_komposisi = 'simpro_tbl_komposisi_budget';
				// $kurangan = 0;
			break;
		}

		// $sql = "SELECT
		// 		a.".$id_info.",
		// 		a.tahap_kode_kendali,
		// 		a.tahap_nama_kendali,
		// 		a.tahap_satuan_kendali,
		// 		(CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		$tbl_info_komposisi s
		// 		JOIN $tbl_info t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 1
		// 		ELSE ((CASE WHEN a.tahap_kode_kendali = '1.' || b.tahap_kode_kendali
				// THEN (
				// (CASE WHEN b.tahap_volume_kendali is null
				// THEN 0
				// ELSE b.tahap_volume_kendali
				// END) +
				// (CASE WHEN b.tahap_volume_kendali_new is null
				// THEN 0
				// ELSE b.tahap_volume_kendali_new
				// END) -
				// (CASE WHEN b.tahap_volume_kendali_kurang is null
				// THEN 0
				// ELSE b.tahap_volume_kendali_kurang
				// END)
				// )
		// 		ELSE a.tahap_volume_kendali
		// 		END) -
		// 		$kurangan
		// 		)
		// 		END) as vol_akhir,
		// 		(CASE WHEN (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		$tbl_info_komposisi z
		// 		right JOIN $tbl_info y  
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali) is null
		// 						THEN 0
		// 						ELSE (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		$tbl_info_komposisi z
		// 		right JOIN $tbl_info y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali)
		// 		END) as harga_satuan,
		// 		(
		// 		((CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		$tbl_info_komposisi s
		// 		JOIN $tbl_info t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 1
		// 		ELSE ((CASE WHEN a.tahap_kode_kendali = '1.' || b.tahap_kode_kendali
		// 		THEN (
		// 		(CASE WHEN b.tahap_volume_kendali is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali
		// 		END) +
		// 		(CASE WHEN b.tahap_volume_kendali_new is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_new
		// 		END) -
		// 		(CASE WHEN b.tahap_volume_kendali_kurang is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_kurang
		// 		END)
		// 		)
		// 		ELSE a.tahap_volume_kendali
		// 		END) -
		// 		$kurangan
		// 		)
		// 		END)
		// 		) *
		// 		(CASE WHEN (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		$tbl_info_komposisi z
		// 		right JOIN $tbl_info y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali) is null
		// 						THEN 0
		// 						ELSE (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		$tbl_info_komposisi z
		// 		right JOIN $tbl_info y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali)
		// 		END)
		// 		) as jml_ctg,
		// 		(CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		$tbl_info_komposisi s
		// 		JOIN $tbl_info t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 'kosong'
		// 		ELSE 'ada'
		// 		END) as status_analisa				
		// 		FROM
		// 		$tbl_info a
		// 		LEFT JOIN simpro_tbl_kontrak_terkini b 
		// 		on right(a.tahap_kode_kendali,(length(a.tahap_kode_kendali)-2)) = b.tahap_kode_kendali
		// 		AND a.tahap_tanggal_kendali <= b.tgl_akhir 
		// 		AND a.tahap_tanggal_kendali >= b.tahap_tanggal_kendali
		// 		AND left(a.tahap_kode_kendali,2) = '1.'
		// 		WHERE 
		// 		a.proyek_id = $proyek_id AND
		// 		a.tahap_tanggal_kendali = '$tgl_rab' 
		// 		and a.tahap_kode_induk_kendali= '' 
		// 		ORDER BY a.tahap_kode_kendali asc";

		$sql = "SELECT
				a.".$id_info.",
				a.tahap_kode_kendali,
				a.tahap_nama_kendali,
				a.tahap_satuan_kendali,
				a.tahap_volume_kendali as vol_akhir,
				(CASE WHEN (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				$tbl_info_komposisi z
				right JOIN $tbl_info y  
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali) is null
								THEN 0
								ELSE (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				$tbl_info_komposisi z
				right JOIN $tbl_info y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali)
				END) as harga_satuan,
				(
				a.tahap_volume_kendali *
				(CASE WHEN (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				$tbl_info_komposisi z
				right JOIN $tbl_info y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali) is null
								THEN 0
								ELSE (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				$tbl_info_komposisi z
				right JOIN $tbl_info y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali)
				END)
				) as jml_ctg,
				(CASE WHEN (SELECT
				s.tahap_kode_kendali
				FROM
				$tbl_info_komposisi s
				JOIN $tbl_info t on s.tahap_kode_kendali = t.tahap_kode_kendali
				WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY s.tahap_kode_kendali) is null
				THEN 'kosong'
				ELSE 'ada'
				END) as status_analisa				
				FROM
				$tbl_info a
				LEFT JOIN simpro_tbl_kontrak_terkini b 
				on right(a.tahap_kode_kendali,(length(a.tahap_kode_kendali)-2)) = b.tahap_kode_kendali
				AND a.tahap_tanggal_kendali <= b.tgl_akhir 
				AND a.tahap_tanggal_kendali >= b.tahap_tanggal_kendali
				AND left(a.tahap_kode_kendali,2) = '1.'
				WHERE 
				a.proyek_id = $proyek_id AND
				a.tahap_tanggal_kendali = '$tgl_rab' 
				and a.tahap_kode_induk_kendali= '' 
				ORDER BY a.tahap_kode_kendali asc";

		$query_sql = $this->db->query($sql);

		if ($query_sql->num_rows > 0) {
			foreach ($query_sql->result() as $row) {
				$key = $row->tahap_kode_kendali;
				$data['task'] = $key;

				$data['ctg_id']= $row->$id_info;
				$data['tahap_kode_kendali']= $row->tahap_kode_kendali;
				$data['tahap_nama_kendali']= $row->tahap_nama_kendali;
				$data['tahap_satuan_kendali']= $row->tahap_satuan_kendali;
				$data['tahap_volume_kendali']= $row->vol_akhir;
				$data['tahap_harga_satuan_kendali']= $row->harga_satuan;
				$data['status_analisa']= $row->status_analisa;
				$data['tahap_total_kendali']= $row->jml_ctg;

				$data['expanded'] = 'true';

				switch ($info) {
					case 'cost_togo':
						$child = $this->query_child_ctg($proyek_id,$tgl_rab,$key);
					break;
					case 'current_budget':
						$child = $this->query_child_cb($proyek_id,$tgl_rab,$key);
					break;
				}
				
				// var_dump($child);
				if ($child=='') {
					$data['leaf'] = 'true';
					$data['stat'] = 'true';
				} else {
					$data['stat'] = 'false';
					$data['children'] = $child;
				}

				$dat[]=$data;
				$data='';

				$return = '{"text":".","children":'.json_encode($dat).'}';
				// $data['children']=$child;
				// $datchild='';
			}
		} else {
			$return='';
		}

		return $return;

	}

	function query_child_ctg($proyek_id,$tgl_rab,$key)
	{
		// $sqlchild = "SELECT
		// 		a.ctg_id,
		// 		a.tahap_kode_kendali,
		// 		a.tahap_nama_kendali,
		// 		a.tahap_satuan_kendali,
		// 		(CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		simpro_tbl_komposisi_togo s
		// 		JOIN simpro_tbl_cost_togo t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 1
		// 		ELSE ((CASE WHEN a.tahap_kode_kendali = '1.' || b.tahap_kode_kendali
		// 		THEN (
		// 		(CASE WHEN b.tahap_volume_kendali is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali
		// 		END) +
		// 		(CASE WHEN b.tahap_volume_kendali_new is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_new
		// 		END) -
		// 		(CASE WHEN b.tahap_volume_kendali_kurang is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_kurang
		// 		END)
		// 		)
		// 		ELSE a.tahap_volume_kendali
		// 		END) -
		// 		(CASE WHEN (SELECT
		// 		SUM(
		// 		CASE WHEN d.tahap_diakui_bobot is null
		// 		THEN 0
		// 		ELSE d.tahap_diakui_bobot
		// 		END)
		// 		FROM
		// 		simpro_tbl_total_pekerjaan d
		// 		JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
		// 		WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
		// 		GROUP BY e.tahap_kode_kendali) is null
		// 		THEN 0
		// 		ELSE (SELECT
		// 		SUM(
		// 		CASE WHEN d.tahap_diakui_bobot is null
		// 		THEN 0
		// 		ELSE d.tahap_diakui_bobot
		// 		END)
		// 		FROM
		// 		simpro_tbl_total_pekerjaan d
		// 		JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
		// 		WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
		// 		GROUP BY e.tahap_kode_kendali)
		// 		END)
		// 		)
		// 		END) as vol_akhir,
		// 		(CASE WHEN (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		simpro_tbl_komposisi_togo z
		// 		right JOIN simpro_tbl_cost_togo y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali) is null
		// 						THEN 0
		// 						ELSE (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		simpro_tbl_komposisi_togo z
		// 		right JOIN simpro_tbl_cost_togo y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali)
		// 		END) as harga_satuan,
		// 		(
		// 		((CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		simpro_tbl_komposisi_togo s
		// 		JOIN simpro_tbl_cost_togo t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 1
		// 		ELSE ((CASE WHEN a.tahap_kode_kendali = '1.' || b.tahap_kode_kendali
		// 		THEN (
		// 		(CASE WHEN b.tahap_volume_kendali is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali
		// 		END) +
		// 		(CASE WHEN b.tahap_volume_kendali_new is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_new
		// 		END) -
		// 		(CASE WHEN b.tahap_volume_kendali_kurang is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_kurang
		// 		END)
		// 		)
		// 		ELSE a.tahap_volume_kendali
		// 		END) -
		// 		(CASE WHEN (SELECT
		// 		SUM(
		// 		CASE WHEN d.tahap_diakui_bobot is null
		// 		THEN 0
		// 		ELSE d.tahap_diakui_bobot
		// 		END)
		// 		FROM
		// 		simpro_tbl_total_pekerjaan d
		// 		JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
		// 		WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
		// 		GROUP BY e.tahap_kode_kendali) is null
		// 		THEN 0
		// 		ELSE (SELECT
		// 		SUM(
		// 		CASE WHEN d.tahap_diakui_bobot is null
		// 		THEN 0
		// 		ELSE d.tahap_diakui_bobot
		// 		END)
		// 		FROM
		// 		simpro_tbl_total_pekerjaan d
		// 		JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
		// 		WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
		// 		GROUP BY e.tahap_kode_kendali)
		// 		END)
		// 		)
		// 		END)
		// 		) *
		// 		(CASE WHEN (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		simpro_tbl_komposisi_togo z
		// 		right JOIN simpro_tbl_cost_togo y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali) is null
		// 						THEN 0
		// 						ELSE (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		simpro_tbl_komposisi_togo z
		// 		right JOIN simpro_tbl_cost_togo y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali)
		// 		END)
		// 		) as jml_ctg,
		// 		(CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		simpro_tbl_komposisi_togo s
		// 		JOIN simpro_tbl_cost_togo t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 'kosong'
		// 		ELSE 'ada'
		// 		END) as status_analisa			
		// 		FROM
		// 		simpro_tbl_cost_togo a
		// 		LEFT JOIN simpro_tbl_kontrak_terkini b 
		// 		on right(a.tahap_kode_kendali,(length(a.tahap_kode_kendali)-2)) = b.tahap_kode_kendali
		// 		AND a.tahap_tanggal_kendali <= b.tgl_akhir 
		// 		AND a.tahap_tanggal_kendali >= b.tahap_tanggal_kendali
		// 		AND left(a.tahap_kode_kendali,2) = '1.'
		// 		WHERE 
		// 		a.proyek_id = $proyek_id AND
		// 		a.tahap_tanggal_kendali = '$tgl_rab' 
		// 		and a.tahap_kode_induk_kendali= '$key' 
		// 		ORDER BY a.tahap_kode_kendali asc";

		$sqlchild = "SELECT
				a.ctg_id,
				a.tahap_kode_kendali,
				a.tahap_nama_kendali,
				a.tahap_satuan_kendali,
				a.tahap_volume_kendali as vol_akhir,
				(CASE WHEN (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				simpro_tbl_komposisi_togo z
				right JOIN simpro_tbl_cost_togo y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali) is null
								THEN 0
								ELSE (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				simpro_tbl_komposisi_togo z
				right JOIN simpro_tbl_cost_togo y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali)
				END) as harga_satuan,
				(
				a.tahap_volume_kendali *
				(CASE WHEN (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				simpro_tbl_komposisi_togo z
				right JOIN simpro_tbl_cost_togo y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali) is null
								THEN 0
								ELSE (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				simpro_tbl_komposisi_togo z
				right JOIN simpro_tbl_cost_togo y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali)
				END)
				) as jml_ctg,
				(CASE WHEN (SELECT
				s.tahap_kode_kendali
				FROM
				simpro_tbl_komposisi_togo s
				JOIN simpro_tbl_cost_togo t on s.tahap_kode_kendali = t.tahap_kode_kendali
				WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY s.tahap_kode_kendali) is null
				THEN 'kosong'
				ELSE 'ada'
				END) as status_analisa			
				FROM
				simpro_tbl_cost_togo a
				LEFT JOIN simpro_tbl_kontrak_terkini b 
				on right(a.tahap_kode_kendali,(length(a.tahap_kode_kendali)-2)) = b.tahap_kode_kendali
				AND a.tahap_tanggal_kendali <= b.tgl_akhir 
				AND a.tahap_tanggal_kendali >= b.tahap_tanggal_kendali
				AND left(a.tahap_kode_kendali,2) = '1.'
				WHERE 
				a.proyek_id = $proyek_id AND
				a.tahap_tanggal_kendali = '$tgl_rab' 
				and a.tahap_kode_induk_kendali= '$key' 
				ORDER BY a.tahap_kode_kendali asc
				";

		$query_sqlchild = $this->db->query($sqlchild);

		if ($query_sqlchild->num_rows > 0) {
			foreach ($query_sqlchild->result() as $rowchild) {

				$keychild = $rowchild->tahap_kode_kendali;
				$datachild['task'] = $keychild;

				$datachild['ctg_id']= $rowchild->ctg_id;
				$datachild['tahap_kode_kendali']= $rowchild->tahap_kode_kendali;
				$datachild['tahap_nama_kendali']= $rowchild->tahap_nama_kendali;
				$datachild['tahap_satuan_kendali']= $rowchild->tahap_satuan_kendali;
				$datachild['tahap_volume_kendali']= $rowchild->vol_akhir;
				$datachild['tahap_harga_satuan_kendali']= $rowchild->harga_satuan;
				$datachild['tahap_total_kendali']= $rowchild->jml_ctg;
				$datachild['status_analisa']= $rowchild->status_analisa;
				$datachild['expanded'] = 'true';

				$childs = $this->query_child_ctg($proyek_id,$tgl_rab,$keychild);

				if ($childs=='') {
					$datachild['leaf'] = 'true';
					$datachild['stat'] = 'true';
				} else {
					$datachild['stat'] = 'false';
					$datachild['children'] = $childs;
				}

				$datchild[]=$datachild;

				$datachild='';
			}					
		} else {
			$datchild='';
		}

		return $datchild;
	}

	function query_child_cb($proyek_id,$tgl_rab,$key)
	{
		// $sqlchild = "SELECT
		// 		a.current_budget_id,
		// 		a.tahap_kode_kendali,
		// 		a.tahap_nama_kendali,
		// 		a.tahap_satuan_kendali,
		// 		(CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		simpro_tbl_komposisi_budget s
		// 		JOIN simpro_tbl_current_budget t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 1
		// 		ELSE ((CASE WHEN a.tahap_kode_kendali = '1.' || b.tahap_kode_kendali
		// 		THEN (
		// 		(CASE WHEN b.tahap_volume_kendali is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali
		// 		END) +
		// 		(CASE WHEN b.tahap_volume_kendali_new is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_new
		// 		END) -
		// 		(CASE WHEN b.tahap_volume_kendali_kurang is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_kurang
		// 		END)
		// 		)
		// 		ELSE a.tahap_volume_kendali
		// 		END)
		// 		)
		// 		END) as vol_akhir,
		// 		(CASE WHEN (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		simpro_tbl_komposisi_budget z
		// 		right JOIN simpro_tbl_current_budget y  
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali) is null
		// 						THEN 0
		// 						ELSE (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		simpro_tbl_komposisi_budget z
		// 		right JOIN simpro_tbl_current_budget y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali)
		// 		END) as harga_satuan,
		// 		(
		// 		((CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		simpro_tbl_komposisi_budget s
		// 		JOIN simpro_tbl_current_budget t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 1
		// 		ELSE ((CASE WHEN a.tahap_kode_kendali = '1.' || b.tahap_kode_kendali
		// 		THEN (
		// 		(CASE WHEN b.tahap_volume_kendali is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali
		// 		END) +
		// 		(CASE WHEN b.tahap_volume_kendali_new is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_new
		// 		END) -
		// 		(CASE WHEN b.tahap_volume_kendali_kurang is null
		// 		THEN 0
		// 		ELSE b.tahap_volume_kendali_kurang
		// 		END)
		// 		)
		// 		ELSE a.tahap_volume_kendali
		// 		END)
		// 		)
		// 		END)
		// 		) *
		// 		(CASE WHEN (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		simpro_tbl_komposisi_budget z
		// 		right JOIN simpro_tbl_current_budget y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali) is null
		// 						THEN 0
		// 						ELSE (SELECT
		// 		sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
		// 		FROM
		// 		simpro_tbl_komposisi_budget z
		// 		right JOIN simpro_tbl_current_budget y 
		// 		on z.proyek_id = y.proyek_id 
		// 		and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
		// 		and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
		// 		WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY y.tahap_kode_kendali)
		// 		END)
		// 		) as jml_ctg,
		// 		(CASE WHEN (SELECT
		// 		s.tahap_kode_kendali
		// 		FROM
		// 		simpro_tbl_komposisi_budget s
		// 		JOIN simpro_tbl_current_budget t on s.tahap_kode_kendali = t.tahap_kode_kendali
		// 		WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
		// 		GROUP BY s.tahap_kode_kendali) is null
		// 		THEN 'kosong'
		// 		ELSE 'ada'
		// 		END) as status_analisa			
		// 		FROM
		// 		simpro_tbl_current_budget a
		// 		LEFT JOIN simpro_tbl_kontrak_terkini b 
		// 		on right(a.tahap_kode_kendali,(length(a.tahap_kode_kendali)-2)) = b.tahap_kode_kendali
		// 		AND a.tahap_tanggal_kendali <= b.tgl_akhir 
		// 		AND a.tahap_tanggal_kendali >= b.tahap_tanggal_kendali
		// 		AND left(a.tahap_kode_kendali,2) = '1.'
		// 		WHERE 
		// 		a.proyek_id = $proyek_id AND
		// 		a.tahap_tanggal_kendali = '$tgl_rab' 
		// 		and a.tahap_kode_induk_kendali= '$key' 
		// 		ORDER BY a.tahap_kode_kendali asc";

		$sqlchild = "SELECT
				a.current_budget_id,
				a.tahap_kode_kendali,
				a.tahap_nama_kendali,
				a.tahap_satuan_kendali,
				a.tahap_volume_kendali as vol_akhir,
				(CASE WHEN (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				simpro_tbl_komposisi_budget z
				right JOIN simpro_tbl_current_budget y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali) is null
								THEN 0
								ELSE (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				simpro_tbl_komposisi_budget z
				right JOIN simpro_tbl_current_budget y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali)
				END) as harga_satuan,
				(
				a.tahap_volume_kendali *
				(CASE WHEN (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				simpro_tbl_komposisi_budget z
				right JOIN simpro_tbl_current_budget y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali) is null
								THEN 0
								ELSE (SELECT
				sum(z.komposisi_harga_satuan_kendali * z.komposisi_koefisien_kendali) as a
				FROM
				simpro_tbl_komposisi_budget z
				right JOIN simpro_tbl_current_budget y 
				on z.proyek_id = y.proyek_id 
				and left(z.tahap_kode_kendali,length(y.tahap_kode_kendali)) = y.tahap_kode_kendali 
				and z.tahap_tanggal_kendali = y.tahap_tanggal_kendali
				WHERE y.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY y.tahap_kode_kendali)
				END)
				) as jml_ctg,
				(CASE WHEN (SELECT
				s.tahap_kode_kendali
				FROM
				simpro_tbl_komposisi_budget s
				JOIN simpro_tbl_current_budget t on s.tahap_kode_kendali = t.tahap_kode_kendali
				WHERE s.tahap_kode_kendali = a.tahap_kode_kendali
				GROUP BY s.tahap_kode_kendali) is null
				THEN 'kosong'
				ELSE 'ada'
				END) as status_analisa			
				FROM
				simpro_tbl_current_budget a
				LEFT JOIN simpro_tbl_kontrak_terkini b 
				on right(a.tahap_kode_kendali,(length(a.tahap_kode_kendali)-2)) = b.tahap_kode_kendali
				AND a.tahap_tanggal_kendali <= b.tgl_akhir 
				AND a.tahap_tanggal_kendali >= b.tahap_tanggal_kendali
				AND left(a.tahap_kode_kendali,2) = '1.'
				WHERE 
				a.proyek_id = $proyek_id AND
				a.tahap_tanggal_kendali = '$tgl_rab' 
				and a.tahap_kode_induk_kendali= '$key' 
				ORDER BY a.tahap_kode_kendali asc";

		$query_sqlchild = $this->db->query($sqlchild);

		if ($query_sqlchild->num_rows > 0) {
			foreach ($query_sqlchild->result() as $rowchild) {

				$keychild = $rowchild->tahap_kode_kendali;
				$datachild['task'] = $keychild;

				$datachild['ctg_id']= $rowchild->current_budget_id;
				$datachild['tahap_kode_kendali']= $rowchild->tahap_kode_kendali;
				$datachild['tahap_nama_kendali']= $rowchild->tahap_nama_kendali;
				$datachild['tahap_satuan_kendali']= $rowchild->tahap_satuan_kendali;
				$datachild['tahap_volume_kendali']= $rowchild->vol_akhir;
				$datachild['tahap_harga_satuan_kendali']= $rowchild->harga_satuan;
				$datachild['tahap_total_kendali']= $rowchild->jml_ctg;
				$datachild['status_analisa']= $rowchild->status_analisa;
				$datachild['expanded'] = 'true';

				$childs = $this->query_child_cb($proyek_id,$tgl_rab,$keychild);

				if ($childs=='') {
					$datachild['leaf'] = 'true';
					$datachild['stat'] = 'true';
				} else {
					$datachild['stat'] = 'false';
					$datachild['children'] = $childs;
				}

				$datchild[]=$datachild;

				$datachild='';
			}					
		} else {
			$datchild='';
		}

		return $datchild;
	}

	function get_data_induk($proyek_id,$tgl_rab)
	{
		$q = $this->db->query("
		select * from simpro_tbl_cost_togo 
		where proyek_id='$proyek_id' 
		and tahap_tanggal_kendali='$tgl_rab' 
		and tahap_kode_induk_kendali= '' 
		order by tahap_kode_kendali");

		if ($q->num_rows>0) {
			$status = 1;
		} else {
			$status = 0;
		}

		return $status;
	}

	function insert_induk_togo($data){
		$this->db->trans_begin();

		$this->db->insert('simpro_tbl_cost_togo',$data);
		$this->db->insert('simpro_tbl_current_budget',$data);

		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		}
		else
		{
		    $this->db->trans_commit();
		}
	}

	function insert_ctg($data,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_cost_togo';				
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_current_budget';
			break;
		}
		$this->db->insert($tbl_info,$data);
	}

	function update_ctg($id,$data,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$id_info = 'ctg_id';
				$tbl_info = 'simpro_tbl_cost_togo';
			break;
			case 'current_budget':
				$id_info = 'current_budget_id';
				$tbl_info = 'simpro_tbl_current_budget';
			break;
		}

		$this->db->where($id_info,$id);
		$this->db->update($tbl_info,$data);
	}

	function get_id_satuan($data)
	{
		$this->db->where('satuan_nama',$data);
		$q = $this->db->get('simpro_tbl_satuan');

		foreach ($q->result() as $row) {
			$id_satuan = $row->satuan_id;
		}

		return $id_satuan;
	}

	function insert_sub_ctg($data,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_cost_togo';
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_current_budget';
			break;
		}
		$this->db->insert($tbl_info,$data);
	}

	function get_tanggal_ctg($proyek_id)
	{

		$sql_jumah = "SELECT tanggal_kendali FROM simpro_costogo_item_tree where id_proyek = $proyek_id  GROUP BY tanggal_kendali order by tanggal_kendali asc";
		
		$q_jml = $this->db->query($sql_jumah);

		if ($q_jml->result()) {
			foreach ($q_jml->result() as $row_jml) {
				$sql = "select 
						a.tanggal_kendali,				
						extract(year from a.tanggal_kendali) as year,
						extract(month from a.tanggal_kendali) as month,
						b.kuncitutup,
						CASE WHEN b.status is null
						    THEN 'open'
						ELSE b.status
						END,
						(c.user_name) as username
						from 
						simpro_costogo_item_tree a
						left join simpro_tbl_approve b on b.tgl_approve = a.tanggal_kendali  and b.form_approve='ALL' and b.proyek_id = $proyek_id
						left join simpro_tbl_user c on b.user_id = c.user_id
						where tanggal_kendali = '$row_jml->tanggal_kendali'
						and a.id_proyek = $proyek_id
						group by tanggal_kendali, b.kuncitutup, c.user_name, b.proyek_id, b.status
						order by tanggal_kendali desc, b.proyek_id DESC";

				$q = $this->db->query($sql);

				$st_app ='';

				$q1 = $q->row_array(0);
				$q2 = $q->row_array(1);

				if ($q->result()) {
					if ($q->num_rows() >= 2  && $q1['tanggal_kendali'] == $q2['tanggal_kendali']) {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						}
						if ($q2['kuncitutup'] == '1') {
							$st_app.="<br />APPROVED BY ".$q2['username'];
						}
						if ($q1['kuncitutup'] == '0' && $q2['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						}
					} else {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						} elseif ($q1['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						} elseif ($q1['kuncitutup'] == '') {
							$st_app.="NOT APPROVE";
						}
					}
					// foreach($q->result() as $row) {
					$date = $q1['tanggal_kendali'];
					$chars = preg_split('[-]', $date, -1, PREG_SPLIT_DELIM_CAPTURE);
					$data['bln']= trim($chars[1]);
					$data['month_name']= $this->bulan(trim($chars[1]));
					$data['thn']= trim($chars[0]);
					$data['tgl_rab']= $date;
					$data['status']= $st_app;
					$data['year'] = $q1['year'];
					$data['month'] = $q1['month'];
					$data['kunci'] = $q1['status'];

			    	$dat[] = $data;
				} else {
					$dat = "";
				}
			}
		}
		

		return $dat;
	}

function get_tanggal_cb($proyek_id)
	{

		$sql_jumah = "SELECT tanggal_kendali FROM simpro_current_budget_item_tree where id_proyek = $proyek_id GROUP BY tanggal_kendali order by tanggal_kendali asc";
		
		$q_jml = $this->db->query($sql_jumah);

		if ($q_jml->result()) {
			foreach ($q_jml->result() as $row_jml) {
				$sql = "select 
						a.tanggal_kendali,			
						extract(year from a.tanggal_kendali) as year,
						extract(month from a.tanggal_kendali) as month,
						b.kuncitutup,
						CASE WHEN b.status is null
						THEN 'open'
						ELSE b.status
						END,
						(c.user_name) as username,
						CASE WHEN 
						(SELECT
						count(*) as jml_data
						FROM
						(SELECT
						tanggal_kendali
						FROM
						simpro_current_budget_item_tree
						where id_proyek = $proyek_id
						GROUP BY tanggal_kendali) as q_tgl) = 1
						THEN 
						(SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id)
						ELSE 
						(CASE WHEN (SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
						WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = $proyek_id
						ORDER BY tanggal_kendali desc limit 1)::date is null
						THEN (SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id)
						ELSE (SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
						WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = $proyek_id
						ORDER BY tanggal_kendali desc limit 1)::date
						END)
						END as tgl_awal
						from 
						simpro_current_budget_item_tree a
						left join simpro_tbl_approve b on b.tgl_approve = a.tanggal_kendali  and b.form_approve='ALL' and b.proyek_id = $proyek_id
						left join simpro_tbl_user c on b.user_id = c.user_id
						left join simpro_tbl_kontrak_terkini d on d.tgl_akhir >= a.tanggal_kendali and d.tahap_tanggal_kendali <= a.tanggal_kendali and d.proyek_id = a.id_proyek
						where a.tanggal_kendali = '$row_jml->tanggal_kendali'
						and a.id_proyek = $proyek_id
						group by a.tanggal_kendali, b.kuncitutup, c.user_name, b.proyek_id, b.status, d.tahap_tanggal_kendali
						order by a.tanggal_kendali asc, b.proyek_id asc";	

				$q = $this->db->query($sql);

				$st_app ='';

				$q1 = $q->row_array(0);
				$q2 = $q->row_array(1);

				if ($q->result()) {
					if ($q->num_rows() >= 2  && $q1['tanggal_kendali'] == $q2['tanggal_kendali']) {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						}
						if ($q2['kuncitutup'] == '1') {
							$st_app.="<br />APPROVED BY ".$q2['username'];
						}
						if ($q1['kuncitutup'] == '0' && $q2['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						}
					} else {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						} elseif ($q1['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						} elseif ($q1['kuncitutup'] == '') {
							$st_app.="NOT APPROVE";
						}
					}
					// foreach($q->result() as $row) {
					$date = $q1['tanggal_kendali'];
					$chars = preg_split('[-]', $date, -1, PREG_SPLIT_DELIM_CAPTURE);
					$date_start = $q1['tgl_awal'];
					$chars_start = preg_split('[-]', $date_start, -1, PREG_SPLIT_DELIM_CAPTURE);
					$data['bln']= trim($chars[1]);
					$data['month_name']= $this->bulan(trim($chars_start[1])).' / '.trim($chars_start[0]); 
					$data['month_name_new']= $this->bulan(trim($q1['month'])).' / '.$q1['year'];
					$data['thn']= trim($chars[0]);
					$data['tgl_rab']= $date;
					$data['status']= $st_app;
					$data['year'] = $q1['year'];
					$data['month'] = $q1['month'];
					$data['kunci'] = $q1['status'];

			    	$dat[] = $data;
				} else {
					$dat = "";
				}
			}
		} else {
			$dat = "";
		}
		

		return $dat;
	}

	function get_tanggal_rencana_kerja($proyek_id)
	{

		$sql_jumah = "SELECT tahap_tanggal_kendali FROM simpro_tbl_total_rkp where proyek_id = $proyek_id  GROUP BY tahap_tanggal_kendali order by tahap_tanggal_kendali asc";
		
		$q_jml = $this->db->query($sql_jumah);

		if ($q_jml->result()) {
			foreach ($q_jml->result() as $row_jml) {
				$sql = "select 
						a.tahap_tanggal_kendali,				
						extract(year from a.tahap_tanggal_kendali) as year,
						extract(month from a.tahap_tanggal_kendali) as month,
						b.kuncitutup,
						CASE WHEN b.status is null
						    THEN 'open'
						ELSE b.status
						END,
						(c.user_name) as username
						from 
						simpro_tbl_total_rkp a
						left join simpro_tbl_approve b on b.tgl_approve = a.tahap_tanggal_kendali  and b.form_approve='ALL' and b.proyek_id = $proyek_id
						left join simpro_tbl_user c on b.user_id = c.user_id
						where tahap_tanggal_kendali = '$row_jml->tahap_tanggal_kendali'
						and a.proyek_id = $proyek_id
						group by tahap_tanggal_kendali, b.kuncitutup, c.user_name, b.proyek_id, b.status
						order by tahap_tanggal_kendali desc, b.proyek_id DESC";

				$q = $this->db->query($sql);

				$st_app ='';

				$q1 = $q->row_array(0);
				$q2 = $q->row_array(1);

				if ($q->result()) {
					if ($q->num_rows() >= 2  && $q1['tahap_tanggal_kendali'] == $q2['tahap_tanggal_kendali']) {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						}
						if ($q2['kuncitutup'] == '1') {
							$st_app.="<br />APPROVED BY ".$q2['username'];
						}
						if ($q1['kuncitutup'] == '0' && $q2['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						}
					} else {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						} elseif ($q1['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						} elseif ($q1['kuncitutup'] == '') {
							$st_app.="NOT APPROVE";
						}
					}
					// foreach($q->result() as $row) {
					$date = $q1['tahap_tanggal_kendali'];
					$chars = preg_split('[-]', $date, -1, PREG_SPLIT_DELIM_CAPTURE);
					$data['bln']= trim($chars[1]);
					$data['month_name']= $this->bulan(trim($chars[1]));
					$data['thn']= trim($chars[0]);
					$data['tahap_tanggal_kendali']= $date;
					$data['status']= $st_app;
					$data['year'] = $q1['year'];
					$data['month'] = $q1['month'];
					$data['kunci'] = $q1['status'];

			    	$dat[] = $data;
				} else {
					$dat = "";
				}
			}
		}
		

		return $dat;
	}

	function get_tanggal_lpf($proyek_id)
	{

		$sql_jumah = "SELECT tahap_tanggal_kendali FROM simpro_tbl_total_pekerjaan where proyek_id = $proyek_id GROUP BY tahap_tanggal_kendali order by tahap_tanggal_kendali asc";
		
		$q_jml = $this->db->query($sql_jumah);

		if ($q_jml->result()) {
			foreach ($q_jml->result() as $row_jml) {
				$sql = "select 
						a.tahap_tanggal_kendali,				
						extract(year from a.tahap_tanggal_kendali) as year,
						extract(month from a.tahap_tanggal_kendali) as month,
						b.kuncitutup,
						CASE WHEN b.status is null
						    THEN 'open'
						ELSE b.status
						END,
						(c.user_name) as username
						from 
						simpro_tbl_total_pekerjaan a
						left join simpro_tbl_approve b on b.tgl_approve = a.tahap_tanggal_kendali  and b.form_approve='ALL' and b.proyek_id = $proyek_id
						left join simpro_tbl_user c on b.user_id = c.user_id
						where tahap_tanggal_kendali = '$row_jml->tahap_tanggal_kendali'
						and a.proyek_id = $proyek_id
						group by tahap_tanggal_kendali, b.kuncitutup, c.user_name, b.proyek_id, b.status
						order by tahap_tanggal_kendali desc, b.proyek_id DESC";

				$q = $this->db->query($sql);

				$st_app ='';

				$q1 = $q->row_array(0);
				$q2 = $q->row_array(1);

				if ($q->result()) {
					if ($q->num_rows() >= 2  && $q1['tahap_tanggal_kendali'] == $q2['tahap_tanggal_kendali']) {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						}
						if ($q2['kuncitutup'] == '1') {
							$st_app.="<br />APPROVED BY ".$q2['username'];
						}
						if ($q1['kuncitutup'] == '0' && $q2['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						}
					} else {
						if ($q1['kuncitutup'] == '1') {
							$st_app.="APPROVED BY ".$q1['username'];
						} elseif ($q1['kuncitutup'] == '0') {
							$st_app.="NOT APPROVE";
						} elseif ($q1['kuncitutup'] == '') {
							$st_app.="NOT APPROVE";
						}
					}
					// foreach($q->result() as $row) {
					$date = $q1['tahap_tanggal_kendali'];
					$chars = preg_split('[-]', $date, -1, PREG_SPLIT_DELIM_CAPTURE);
					$data['bln']= trim($chars[1]);
					$data['month_name']= $this->bulan(trim($chars[1]));
					$data['thn']= trim($chars[0]);
					$data['tahap_tanggal_kendali']= $date;
					$data['status']= $st_app;
					$data['year'] = $q1['year'];
					$data['month'] = $q1['month'];
					$data['kunci'] = $q1['status'];

			    	$dat[] = $data;
				} else {
					$dat = "";
				}
			}
		}
		

		return $dat;
	}

	function get_data_analisa($limit,$offset,$text,$cbo)
	{
		if ($text != '' && $cbo != '') {
			$sql = "select * from simpro_tbl_detail_material where simpro_tbl_subbidang_id = $cbo and lower(detail_material_nama) LIKE lower('%$text%')";		

			$sql2 = "SELECT * FROM simpro_tbl_detail_material  where simpro_tbl_subbidang_id = $cbo and lower(detail_material_nama) LIKE lower('%$text%') LIMIT $limit OFFSET $offset";
		} else if($text == '' && $cbo != '') {
			$sql = "select * from simpro_tbl_detail_material where simpro_tbl_subbidang_id = $cbo";		

			$sql2 = "SELECT * FROM simpro_tbl_detail_material  where simpro_tbl_subbidang_id = $cbo LIMIT $limit OFFSET $offset";
		}
		else {
			$sql = "select * from simpro_tbl_detail_material";		

			$sql2 = "SELECT * FROM simpro_tbl_detail_material LIMIT $limit OFFSET $offset";
		}

		$q = $this->db->query($sql2);

		$q_total = $this->db->query($sql);

		$totaldata = $q_total->num_rows();

		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$data['id'] = $row->detail_material_id;
				$data['kode'] = $row->detail_material_kode;
				$data['nama'] = $row->detail_material_nama;
				$data['spesifikasi'] = $row->detail_material_spesifikasi;
				$data['propinsi'] = $row->detail_material_propinsi;
				$data['harga'] = $row->detail_material_harga;
				$data['koefisien'] = '1';

				$dat[] = $data;
			}
		} else {
			$dat="";
		}

		// echo $totaldata;
		return '{"total":"'.$totaldata.'","data":'.json_encode($dat).'}';
	}

	function cek_data_induk_togo($kode,$proyek_id,$tgl_rab,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_induk_togo';
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_induk_budget';
			break;
		}
		$sql = "select * from $tbl_info 
						where kode_komposisi='X' 
						and proyek_id='$proyek_id' 
						and tahap_kode_kendali='$kode' 
						and tahap_tanggal_kendali='$tgl_rab'";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$data = 'ada';
		} else {
			$data = 'kosong';
		}

		return $data;
	}

	function cek_data_komposisi_togo($id_material,$kode,$proyek_id,$tgl_rab,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_komposisi_togo';
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_komposisi_budget';
			break;
		}
		$sql = "select * from $tbl_info 
						where detail_material_id=$id_material 
						and proyek_id='$proyek_id' 
						and tahap_kode_kendali='$kode' 
						and tahap_tanggal_kendali='$tgl_rab'";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$data = 'ada';
		} else {
			$data = 'kosong';
		}

		return $data;
	}

	function insert_induk_komposisi_togo($data,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_komposisi_togo';
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_komposisi_budget';
			break;
		}
		$this->db->insert($tbl_info,$data);
	}

	function insert_induk_togo_induk($data,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_induk_togo';
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_induk_budget';
			break;
		}
		$this->db->insert($tbl_info,$data);
	}

	function get_proyek_daftar_analisa()
	{
		$query = "select no_spk, (select proyek from tbl_proyek where no_spk = a.no_spk) as proyek from tbl_input_kontrak a group by no_spk";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->no_spk;
    		$data['text'] = $row->proyek;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function get_tanggal_daftar_analisa($proyek_id)
	{
		$sql = "select tahap_tanggal_kendali, 
		extract(year from tahap_tanggal_kendali) as year,
		extract(month from tahap_tanggal_kendali) as month
		from tbl_input_kontrak where no_spk = '$proyek_id'
		group by tahap_tanggal_kendali 
		order by tahap_tanggal_kendali asc";

		$q = $this->db->query($sql);

		if ($q->num_rows()>0) {
			foreach ($q->result() as $row) {
				$data['tgl_rab'] = $row->tahap_tanggal_kendali;
				$data['year'] = $row->year;
				$data['month'] = $row->month;
				$data['month_name'] = $this->bulan($row->month);
				$data['proyek']=$proyek_id;

				$dat[] = $data;
			}
		} else {
			$dat[]="";
		}

		return $dat;
	}

	function get_data_daftar_analisa($proyek_id,$tgl_rab){
		$sql = "select * from tbl_input_kontrak
		where no_spk='$proyek_id' 
		and tahap_tanggal_kendali='$tgl_rab' 
		and tahap_kode_induk_kendali= '' 
		order by tahap_kode_kendali";

		$query_sql = $this->db->query($sql);

		if ($query_sql->num_rows > 0) {
			foreach ($query_sql->result() as $row) {
				$key = $row->tahap_kode_kendali;
				$data['task'] = $key;

				$data['tahap_kode_kendali']= $row->tahap_kode_kendali;
				$data['tahap_nama_kendali']= $row->tahap_nama_kendali;
				$data['tahap_satuan_kendali']= $row->tahap_satuan_kendali;
				$data['expanded'] = 'true';

				$child = $this->query_child_daftar_analisa($proyek_id,$tgl_rab,$key);

				// var_dump($child);
				if ($child=='') {
					$data['leaf'] = 'true';
				} else {
					$data['children'] = $child;
				}

				$dat[]=$data;
				$data='';

				$return = '{"text":".","children":'.json_encode($dat).'}';
				// $data['children']=$child;
				// $datchild='';
			}
		} else {
			$return='';
		}

		return $return;

	}

	function query_child_daftar_analisa($proyek_id,$tgl_rab,$key)
	{
		$sqlchild = "select * from tbl_input_kontrak
		where no_spk='$proyek_id' 
		and tahap_tanggal_kendali='$tgl_rab' 
		and tahap_kode_induk_kendali= '$key' 
		order by tahap_kode_kendali";

		$query_sqlchild = $this->db->query($sqlchild);

		if ($query_sqlchild->num_rows > 0) {
			foreach ($query_sqlchild->result() as $rowchild) {

				$keychild = $rowchild->tahap_kode_kendali;
				$datachild['task'] = $keychild;

				$datachild['tahap_kode_kendali']= $rowchild->tahap_kode_kendali;
				$datachild['tahap_nama_kendali']= $rowchild->tahap_nama_kendali;
				$datachild['tahap_satuan_kendali']= $rowchild->tahap_satuan_kendali;
				$datachild['tahap_volume_kendali']= $rowchild->tahap_volume_kendali;
				$datachild['tahap_harga_satuan_kendali']= $rowchild->tahap_harga_satuan_kendali;
				$datachild['tahap_total_kendali']= $rowchild->tahap_total_kendali;
				// $datachild['expanded'] = 'true';

				$childs = $this->query_child_daftar_analisa($proyek_id,$tgl_rab,$keychild);

				if ($childs=='') {
					$datachild['leaf'] = 'true';
				} else {
					$datachild['children'] = $childs;
				}

				$datchild[]=$datachild;

				$datachild='';
			}					
		} else {
			$datchild='';
		}

		return $datchild;
	}

	function getdata_sub_ctg($proyek_id,$tgl_rab,$kode_kendali,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$id_info = 'komposisi_togo_id';
				$tbl_info = 'simpro_tbl_komposisi_togo';
			break;
			case 'current_budget':
				$id_info = 'komposisi_budget_id';
				$tbl_info = 'simpro_tbl_komposisi_budget';
			break;
		}

		$sql = "SELECT a.$id_info,	
						b.detail_material_nama, 
						b.detail_material_satuan,
						a.komposisi_harga_satuan_kendali, 
						a.komposisi_koefisien_kendali,
						(a.komposisi_harga_satuan_kendali * a.komposisi_koefisien_kendali) as total,
						c.subbidang_name, 
						c.inisial_rap FROM
						$tbl_info A
						JOIN simpro_tbl_detail_material b ON A .detail_material_id = b.detail_material_id
						JOIN simpro_tbl_subbidang C ON substr(b.subbidang_kode, 1, 3) = c.subbidang_kode
						where proyek_id=$proyek_id
						and tahap_tanggal_kendali = '$tgl_rab'
						and tahap_kode_kendali = '$kode_kendali'
						ORDER BY c.inisial_rap, b.detail_material_nama";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$rap="";
			$i=1;
			foreach ($q->result() as $row) {
				$data['id']=$row->$id_info;
				$data['nama']=$row->detail_material_nama;
				$data['satuan']=$row->detail_material_satuan;
				$data['harga']=$row->komposisi_harga_satuan_kendali;
				$data['koefisien']=$row->komposisi_koefisien_kendali;
				$data['total']=$row->total;
				$data['sub_nama']=$row->subbidang_name;

				$inisial_rap=$row->inisial_rap;
				if ($rap != $inisial_rap) {
					$rap = $inisial_rap;
					$i = 1;			
				}
				$data['kode_rap']=$inisial_rap.sprintf('%04d',$i++);
				$dat[]=$data;
			}
			
		} else {
			$dat='';
		}
		
		return '{"data":'.json_encode($dat).'}';		
	}

	function update_analisa_ctg($id,$data,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$id_info = 'komposisi_togo_id';
				$tbl_info = 'simpro_tbl_komposisi_togo';
			break;
			case 'current_budget':
				$id_info = 'komposisi_budget_id';
				$tbl_info = 'simpro_tbl_komposisi_budget';
			break;
		}

		$this->db->where($id_info, $id);
		$this->db->update($tbl_info, $data);
	}

	function getdata_edit_hs_ctg($proyek_id,$tgl_rab,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_komposisi_togo';
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_komposisi_budget';
			break;
		}
		
		$sql = "SELECT b.detail_material_kode,	
				b.detail_material_nama, 
				 avg(a.komposisi_harga_satuan_kendali) as rata_harga_satuan, 
				c.subbidang_name, 
				c.inisial_rap FROM
				$tbl_info A
				JOIN simpro_tbl_detail_material b ON A .detail_material_id = b.detail_material_id
				JOIN simpro_tbl_subbidang C ON substr(b.subbidang_kode, 1, 3) = c.subbidang_kode
				GROUP BY b.detail_material_kode, b.detail_material_nama, c.subbidang_name, c.inisial_rap
				ORDER BY c.inisial_rap, b.detail_material_nama";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$rap="";
			$i=1;
			foreach ($q->result() as $row) {
				$data['kode']=$row->detail_material_kode;
				$data['nama']=$row->detail_material_nama;
				$data['harga']=$row->rata_harga_satuan;
				$data['sub_nama']=$row->subbidang_name;

				$inisial_rap=$row->inisial_rap;
				if ($rap != $inisial_rap) {
					$rap = $inisial_rap;
					$i = 1;			
				}
				$data['kode_rap']=$inisial_rap.sprintf('%03d',$i++);
				$dat[]=$data;
			}
			
		} else {
			$dat='';
		}
		
		return '{"data":'.json_encode($dat).'}';		
	}

	function update_hs_ctg($proyek_id,$tgl_rab,$id,$data,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_komposisi_togo';
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_komposisi_budget';
			break;
		}
		$var = array('proyek_id' => $proyek_id, 'tahap_tanggal_kendali' => $tgl_rab, 'detail_material_kode' => $id);
		$this->db->where($var);
		$this->db->update($tbl_info, $data);
	}

	function get_kode_ctg($proyek_id,$tgl_rab,$info)
	{
		switch ($info) {
			case 'cost_togo':
				$tbl_info = 'simpro_tbl_cost_togo';
			break;
			case 'current_budget':
				$tbl_info = 'simpro_tbl_current_budget';
			break;
		}

		$sql = "select count(tahap_kode_kendali) as jml from $tbl_info where tahap_kode_induk_kendali='' and proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab'";
			
		$q = $this->db->query($sql);

		foreach($q->result() as $row) {
			$jml = $row->jml;
			switch ($jml) {
			 	case '0':
			 		$jml = 1;
			 	break;			 	
			 	default:
			 		$jml = $jml +1;
			 	break;
			 } 
			$data['value'] = $jml;
			$dat[] = $data;
		}
		return $dat;
	}

	function cek_pwd_hs($uname,$pwd)
	{
		$var = array('username' => $uname, 'password' => $pwd);
		$this->db->where($var);
		$q = $this->db->get('simpro_tbl_pass_edit');

		if ($q->result()) {
			$data['value'] = 'true' ;
		} else {
			$data['value'] = 'false' ;
		}
		$dat[] = $data;
		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_rk($proyek_id,$tgl_rab)
	{
		$sql="
		SELECT 
		a.total_rkp_id, 
		b.tahap_kode_kendali,
		b.tahap_nama_kendali,
		c.satuan_nama,
		b.tahap_volume_kendali,
		b.tahap_harga_satuan_kendali,
		(b.tahap_volume_kendali * b.tahap_harga_satuan_kendali) as jumlah,
		d.tahap_diakui_bobot,
		(d.tahap_diakui_bobot * b.tahap_harga_satuan_kendali) as jumlah_prestasi,
		COALESCE((a.tahap_volume_bln1),0) as volume_bln1,
		COALESCE((a.tahap_volume_bln1 * b.tahap_harga_satuan_kendali),0) as jumlah_bln1,
		COALESCE((a.tahap_volume_bln2),0) as volume_bln2,
		COALESCE((a.tahap_volume_bln2 * b.tahap_harga_satuan_kendali),0) as jumlah_bln2,
		COALESCE((a.tahap_volume_bln3),0) as volume_bln3,
		COALESCE((a.tahap_volume_bln3 * b.tahap_harga_satuan_kendali),0) as jumlah_bln3,
		COALESCE((a.tahap_volume_bln4),0) as volume_bln4,
		COALESCE((a.tahap_volume_bln4 * b.tahap_harga_satuan_kendali),0) as jumlah_bln4,
		COALESCE((b.tahap_volume_kendali - (d.tahap_diakui_bobot + a.tahap_volume_bln1 + a.tahap_volume_bln2 + a.tahap_volume_bln3 + a.tahap_volume_bln4)),0) as deviasi
		FROM
		simpro_tbl_total_rkp a join simpro_tbl_kontrak_terkini  b on a.kontrak_terkini_id = b.id_kontrak_terkini
		join simpro_tbl_satuan c on b.tahap_satuan_kendali = c.satuan_id
		join simpro_tbl_total_pekerjaan d on a.kontrak_terkini_id =  d.kontrak_terkini_id
		where a.proyek_id = $proyek_id and a.tahap_tanggal_kendali = '$tgl_rab'
		ORDER BY b.tahap_kode_kendali";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}
		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_rencana_kontrak($proyek_id,$tgl_rab)
	{
		$sql = "SELECT 
				a.id_kontrak_terkini,
				a.tahap_kode_kendali,
				a.tahap_nama_kendali,
				b.satuan_nama,
				a.tahap_volume_kendali,
				a.tahap_harga_satuan_kendali,
				(a.tahap_volume_kendali * a.tahap_harga_satuan_kendali) as total,
				COALESCE((a.volume_rencana),0) as volume_rencana,
				COALESCE((a.volume_rencana * a.tahap_harga_satuan_kendali),0) as jumlah,
				COALESCE((a.volume_rencana1),0) as volume_rencana1,
				COALESCE((a.volume_rencana1 * a.tahap_harga_satuan_kendali),0) as jumlah1,
				COALESCE((a.rencana_volume_eskalasi),0) as rencana_volume_eskalasi,
				COALESCE((a.harga_satuan_eskalasi),0) as harga_satuan_eskalasi,
				COALESCE((a.rencana_volume_eskalasi * a.harga_satuan_eskalasi),0) as jumlah_eskalasi
				FROM simpro_tbl_kontrak_terkini a join simpro_tbl_satuan b on a.tahap_satuan_kendali = b.satuan_id
				where a.proyek_id = $proyek_id and a.tahap_tanggal_kendali = '$tgl_rab'
				ORDER BY a.tahap_kode_kendali";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_rpbk($proyek_id,$tgl_rab)
	{
		$sql = "SELECT 
				f.rpbk_id as rpbk_id,
				a.kode_rap,
				g.inisial_rap,
				g.subbidang_name,
				a.detail_material_id,
				a.detail_material_kode, 
				b.detail_material_nama, 
				'' as keterangan, 
				b.detail_material_satuan ,
				sum(a.komposisi_volume_kendali) as jumlah_volume,
				avg(c.komposisi_harga_satuan_kendali) as rata_harga_satuan,
				(sum(a.komposisi_volume_kendali) * avg(c.komposisi_harga_satuan_kendali)) as jumlah_harga,
				(SELECT sum(d.volume_rencana_pbk) 
				from simpro_tbl_rpbk d
				WHERE d.tahap_tanggal_kendali < '$tgl_rab'
				and d.detail_material_id = a.detail_material_id GROUP BY d.detail_material_id)as total_volume_rpbk_lalu,
				f.rpbk_rrk1 as rpbk_rrk1
				FROM 
				(
				with get_analisa as (select
					b.kode_analisa,
					case when c.kode_analisa isnull
					then b.kode_analisa
					else c.kode_analisa
					end as analisa
					from
					simpro_rap_item_tree a
					join simpro_rap_analisa_item_apek b 
					on a.kode_tree = b.kode_tree and b.id_proyek = a.id_proyek
					join simpro_rap_analisa_apek c
					on b.kode_analisa = c.parent_kode_analisa and c.id_proyek = a.id_proyek
					where a.id_proyek = $proyek_id)
					select
					id_proyek as proyek_id,
					id_detail_material as detail_material_id,
					kode_material as detail_material_kode,
					detail_material_nama,
					detail_material_satuan,
					sum(koefisien) as komposisi_volume_kendali,
					harga,
					sum(subtotal) as subtotal,
					kode_rap from (select 
					e.id_proyek,
					e.id_detail_material,
					e.kode_material,
					f.detail_material_nama,
					f.detail_material_satuan,
					sum(e.koefisien) as koefisien,
					e.harga,
					sum(e.koefisien * e.harga) as subtotal,
					e.kode_rap
					from get_analisa d
					join simpro_rap_analisa_asat e
					on d.analisa = e.kode_analisa
					join simpro_tbl_detail_material f
					on e.kode_material = f.detail_material_kode
					group by
					e.id_proyek,
					e.id_detail_material,
					e.kode_material,
					f.detail_material_nama,
					f.detail_material_satuan,
					e.harga,
					e.kode_rap
					union all
					select
					c.id_proyek,
					c.id_detail_material,
					c.kode_material,
					d.detail_material_nama,
					d.detail_material_satuan,
					sum(c.koefisien) as koefisien,
					c.harga,
					sum(c.koefisien * c.harga) as subtotal,
					c.kode_rap
					from
					simpro_rap_item_tree a
					join simpro_rap_analisa_item_apek b 
					on a.kode_tree = b.kode_tree and b.id_proyek = a.id_proyek
					join simpro_rap_analisa_asat c
					on b.kode_analisa = c.kode_analisa and c.id_proyek = a.id_proyek
					join simpro_tbl_detail_material d
					on c.kode_material = d.detail_material_kode
					where a.id_proyek = $proyek_id
					group by
					c.id_proyek,
					c.id_detail_material,
					c.kode_material,
					d.detail_material_nama,
					d.detail_material_satuan,
					c.harga,
					c.kode_rap) detail
					group by
					id_proyek,
					id_detail_material,
					kode_material,
					detail_material_nama,
					detail_material_satuan,
					harga,
					kode_rap
					order by kode_rap
				) a join simpro_tbl_detail_material b 
				on a.detail_material_kode = b.detail_material_kode
				left join (
				with get_analisa as (select
					b.kode_analisa,
					case when c.kode_analisa isnull
					then b.kode_analisa
					else c.kode_analisa
					end as analisa
					from
					simpro_costogo_item_tree a
					join simpro_costogo_analisa_item_apek b 
					on a.kode_tree = b.kode_tree and b.id_proyek = a.id_proyek
					join simpro_costogo_analisa_apek c
					on b.kode_analisa = c.parent_kode_analisa and c.id_proyek = a.id_proyek
					where a.id_proyek = $proyek_id)
					select 
					id_detail_material as detail_material_id,
					kode_material as detail_material_kode,
					detail_material_nama,
					detail_material_satuan,
					sum(koefisien) as koefisien,
					harga as komposisi_harga_satuan_kendali,
					sum(subtotal) as subtotal,
					kode_rap,
					tanggal_kendali from (select 
					e.id_detail_material,
					e.kode_material,
					f.detail_material_nama,
					f.detail_material_satuan,
					sum(e.koefisien) as koefisien,
					e.harga,
					sum(e.koefisien * e.harga) as subtotal,
					e.kode_rap,
					e.tanggal_kendali
					from get_analisa d
					join simpro_costogo_analisa_asat e
					on d.analisa = e.kode_analisa
					join simpro_tbl_detail_material f
					on e.kode_material = f.detail_material_kode
					group by
					e.id_detail_material,
					e.kode_material,
					f.detail_material_nama,
					f.detail_material_satuan,
					e.harga,
					e.kode_rap,
					e.tanggal_kendali
					union all
					select
					c.id_detail_material,
					c.kode_material,
					d.detail_material_nama,
					d.detail_material_satuan,
					sum(c.koefisien) as koefisien,
					c.harga,
					sum(c.koefisien * c.harga) as subtotal,
					c.kode_rap,
					c.tanggal_kendali
					from
					simpro_costogo_item_tree a
					join simpro_costogo_analisa_item_apek b 
					on a.kode_tree = b.kode_tree and b.id_proyek = a.id_proyek
					join simpro_costogo_analisa_asat c
					on b.kode_analisa = c.kode_analisa and c.id_proyek = a.id_proyek
					join simpro_tbl_detail_material d
					on c.kode_material = d.detail_material_kode
					where a.id_proyek = $proyek_id
					group by
					c.id_detail_material,
					c.kode_material,
					d.detail_material_nama,
					d.detail_material_satuan,
					c.harga,
					c.kode_rap,
					c.tanggal_kendali) detail
					where tanggal_kendali = '$tgl_rab'
					group by
					id_detail_material,
					kode_material,
					detail_material_nama,
					detail_material_satuan,
					harga,
					kode_rap,
					tanggal_kendali
					order by kode_rap
				) c on a.detail_material_id = c.detail_material_id
				left join simpro_tbl_rpbk f on f.proyek_id = a.proyek_id and f.detail_material_id = a.detail_material_id
				join simpro_tbl_subbidang g on  b.subbidang_id = g.subbidang_id and substr(g.subbidang_kode, 1, 3) = g.subbidang_kode
				where a.proyek_id = $proyek_id and (g.subbidang_kode = '500' or g.subbidang_kode = '505')
				GROUP BY a.detail_material_id, total_volume_rpbk_lalu, a.detail_material_kode, b.detail_material_nama, b.detail_material_satuan, f.rpbk_rrk1, f.rpbk_id, g.subbidang_name, g.inisial_rap, a.kode_rap
				ORDER BY g.inisial_rap, a.kode_rap";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$rap="";
			foreach ($q->result() as $row) {
				$data['rpbk_id'] = $row->rpbk_id;
				$data['subbidang_name'] = $row->subbidang_name;
				$data['detail_material_id'] = $row->detail_material_id;
				$data['detail_material_kode'] = $row->detail_material_kode;
				$data['detail_material_nama'] = $row->detail_material_nama;
				$data['keterangan'] = $row->keterangan;
				$data['detail_material_satuan'] = $row->detail_material_satuan;
				$data['jumlah_volume'] = $row->jumlah_volume;
				$data['rata_harga_satuan'] = $row->rata_harga_satuan;
				$data['jumlah_harga'] = $row->jumlah_harga;
				$data['total_volume_rpbk_lalu'] = $row->total_volume_rpbk_lalu;
				$data['rpbk_rrk1'] = $row->rpbk_rrk1;

				$inisial_rap=$row->inisial_rap;
				if ($rap != $inisial_rap) {
					$rap = $inisial_rap;
					$i = 1;			
				}
				$data['kode_rap']=$inisial_rap.sprintf('%03d',$i++);

				$dat[] = $data;
			}
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function rpbk($mode,$data,$id)
	{
		switch ($mode) {
			case 'simpan':
				$this->db->insert('simpro_tbl_rpbk',$data);
			break;
			case 'update':
				$this->db->where('rpbk_id',$id);
				$this->db->update('simpro_tbl_rpbk',$data);
			break;
		}
	}

	function get_uraian_mos($proyek_id)
	{
		$sql = "select 
				a.kode_rap as value,
				b.detail_material_nama || ' => ' ||a.kode_rap as text,
				b.detail_material_satuan as satuan,
				a.id_detail_material,
				a.kode_rap,
				a.harga as harga,
				a.kode_material as detail_material_kode,
				a.koefisien as volume_total
				from simpro_rap_analisa_asat a
				join simpro_tbl_detail_material b 
				on a.kode_material = b.detail_material_kode
				where a.id_proyek = $proyek_id 
				group by
				value,
				text,
				a.kode_material,
				b.detail_material_nama,
				b.detail_material_satuan,
				a.kode_rap,
				a.harga,
				a.id_detail_material,
				a.koefisien
				order by a.kode_rap, text";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function mos_action($info,$data,$id)
	{
		switch ($info) {
			case 'tambah':
				$this->db->insert('simpro_tbl_mos',$data);
			break;
			case 'edit':
				$this->db->where('mos_id',$id);
				$this->db->update('simpro_tbl_mos',$data);
			break;
		}
	}

	function get_data($info,$proyek_id,$var)
	{
		switch ($info) {
			case 'mos':
				$sql = "SELECT  
				a.mos_id,
				b.detail_material_nama || ' => ' || a.kode_rap as detail_material_nama,
				b.detail_material_satuan,
				a.mos_total_volume,
				a.mos_total_harsat,
				(a.mos_total_volume * c.harga) as total_jumlah_mos,
				a.mos_diakui_volume,
				(a.mos_diakui_volume * c.harga) as total_mos_diakui,
				a.mos_belum_volume,
				(a.mos_belum_volume * c.harga) as total_mos_belum_diakui,
				a.mos_keterangan
				FROM 
				simpro_tbl_mos a 
				join simpro_tbl_detail_material b 
				on a.detail_material_kode = b.detail_material_kode
				join (select kode_rap, kode_material, harga, id_proyek from simpro_rap_analisa_asat where id_proyek = $proyek_id group by kode_rap, kode_material, harga, id_proyek) c
				on a.kode_rap = c.kode_rap and a.detail_material_kode = c.kode_material and c.id_proyek = a.proyek_id
				where a.proyek_id = $proyek_id";
			break;
			case 'kkp':
				$sql = "SELECT a.* FROM simpro_tbl_kkp a where a.proyek_id = $proyek_id";
			break;
		}

		if ($var != '') {
			$sql .= $var;
		}

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_jabatan()
	{
		$sql="SELECT 
			id_jabatan as value,
			trim(jabatan) as text
			FROM
			simpro_tbl_jabatan";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function kkp_action($info,$data,$id)
	{
		switch ($info) {
			case 'tambah':
				$this->db->insert('simpro_tbl_kkp',$data);
			break;
			case 'edit':
				$this->db->where('kkp_id',$id);
				$this->db->update('simpro_tbl_kkp',$data);
			break;
		}
	}

	function getkondisi()
	{
		$sql="SELECT 
			kondisi_id as value,
			trim(kondisi) as text
			FROM
			simpro_m_kondisi";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function getstatusoperasi()
	{
		$sql="SELECT 
			status_operasi_id as value,
			trim(status_operasi) as text
			FROM
			simpro_m_status_operasi";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function getstatuskepemilikan()
	{
		$sql="SELECT 
			status_kepemilikan_id as value,
			trim(status_kepemilikan) as text
			FROM
			simpro_m_status_kepemilikan";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function action_daftar_alat($info,$data,$id)
	{
		switch ($info) {
			case 'simpan':
				$this->db->insert('simpro_tbl_daftar_peralatan',$data);
			break;
			case 'edit':
				$this->db->where('daftar_peralatan_id',$id);
				$this->db->update('simpro_tbl_daftar_peralatan',$data);
			break;
		}
	}

	function getdaftar_alat($proyek_id)
	{
		$sql = "SELECT 
				a.daftar_peralatan_id,
				b.uraian_jenis_alat,
				b.merk_model,
				b.type_penggerak,
				b.kapasitas,
				c.status_kepemilikan,
				d.kondisi,
				e.status_operasi,
				a.keterangan,
				a.master_peralatan_id,
				(a.status_kepemilikan) as status_kepemilikan_id,
				(a.kondisi) as kondisi_id,
				(a.status_operasi) as status_operasi_id
				FROM
				simpro_tbl_daftar_peralatan a JOIN simpro_tbl_master_peralatan b on a.master_peralatan_id = b.master_peralatan_id
				join simpro_m_status_kepemilikan c on a.status_kepemilikan = c.status_kepemilikan_id
				join simpro_m_kondisi d on a.kondisi = d.kondisi_id
				join simpro_m_status_operasi e on a.status_operasi = e.status_operasi_id
				where a.proyek_id = $proyek_id
				order by a.daftar_peralatan_id";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function update_data($info,$data,$id)
	{
		switch ($info) {
			case 'rencana_kerja':
				$id_info = 'total_rkp_id';
				$tbl_info = 'simpro_tbl_total_rkp';
			break;
			case 'rencana_kontrak_kini':
				$id_info = 'id_kontrak_terkini';
				$tbl_info = 'simpro_tbl_kontrak_terkini';
			break;
		}
		$this->db->where($id_info,$id);
		$this->db->update($tbl_info,$data);
	}

	function approve($proyek_id,$username,$password,$tgl,$uid)
	{
		$sql = "SELECT
				a.user_id,
				c.user_name,
				c.password,
				b.tgl_approve,
				b.form_approve,
				b.kuncitutup,
				b.kuncibuka,
				b.status
				FROM
				simpro_tbl_isazowa_app_proyek a
				LEFT JOIN simpro_tbl_approve b on a.proyek_id = b.proyek_id and a.user_id = b.user_id and b.tgl_approve='$tgl' and b.form_approve = 'ALL' 
				JOIN simpro_tbl_user c on a.user_id = c.user_id
				WHERE 
				a.proyek_id = $proyek_id AND c.user_name = '$username' AND c.password = '$password'				
				ORDER BY b.tgl_approve desc, b.proyek_id desc";

		$sql_open = "select * from simpro_tbl_approve where proyek_id = $proyek_id and tgl_approve='$tgl' and status='open' and form_approve='ALL'";

		$sql_close = "select * from simpro_tbl_approve where proyek_id = $proyek_id and tgl_approve='$tgl' and status='close' and form_approve='ALL'";

		$sql2 = "select * from simpro_tbl_approve where tgl_approve='$tgl' and proyek_id=$proyek_id and form_approve='ALL'";

		$check_username = "select * from simpro_tbl_user where user_name = '$username'";

		$check_hak_proyek = "select * from simpro_tbl_isazowa_app_proyek where proyek_id = $proyek_id";

		$q_approve_open = $this->db->query($sql_open);
		$q_approve_close = $this->db->query($sql_close);

		$q_cek_user = $this->db->query($check_username);
		if ($q_cek_user->num_rows()>0) {
			$user_row = $q_cek_user->row();
		}

		$q_cek_proyek = $this->db->query($check_hak_proyek);
		if ($q_cek_proyek->num_rows()>0) {
			$proyek_row = $q_cek_proyek->row();
		}

		$q = $this->db->query($sql);
		
		if ($q_cek_user->result()) {
			if ($q->result()) {
				$q_row = $q->row();

				if ($q_row->status) {
					foreach ($q->result() as $row) {
						if($row->kuncitutup == 0){
		                    $kuncitutup = 1;
		                    $kuncibuka = 0;
		                    $status = "Anda telah melakukan APPROVAL Laporan Bulan ini!";
		                }elseif($row->kuncitutup == 1){
		                    $kuncitutup = 0;
		                    $kuncibuka = 1;
		                    $status = "Anda telah melakukan NOT APPROVAL Laporan Bulan ini!";
		                }			
		                $data = array(
		                	'kuncitutup' => $kuncitutup, 
		                	'kuncibuka' => $kuncibuka
		                );		
		                $where_update = array(
		                	'tgl_approve' => $tgl, 
		                	'proyek_id' => $proyek_id, 
		                	'user_id' => $row->user_id, 
		                	'form_approve' => 'ALL', 
		                );
		                $this->db->where($where_update);
		                $this->db->update('simpro_tbl_approve',$data);

					}
				} else {
					if ($q_approve_open->num_rows() == 2) {
						$this->db->query("delete from simpro_tbl_approve where proyek_id=$proyek_id and tgl_approve='$tgl' and status='open' and form_approve='ALL'");
						if ($uid == $user_row->user_id) {
							$kuncitutup = 1;
		                    $kuncibuka = 0;
						} else {
							$kuncitutup = 0;
		                    $kuncibuka = 1;
						}

						$data_approve = array(
							'proyek_id' => $proyek_id,
							'user_id' => $user_row->user_id,
							'tgl_approve' => $tgl,
							'form_approve' => 'ALL',
							'status' => 'open',
							'kuncitutup' => $kuncitutup,
							'kuncibuka' => $kuncibuka
						);			

						$this->db->insert('simpro_tbl_approve',$data_approve);	

						$status = "Anda telah melakukan APPROVAL Laporan Bulan ini!";	
					} else {
						if ($q_approve_close->num_rows() > 0) {
							$status = "Approval terkunci!";
						} else {
							if ($uid == $user_row->user_id) {
								$kuncitutup = 1;
			                    $kuncibuka = 0;
							} else {
								$kuncitutup = 0;
			                    $kuncibuka = 1;
							}

							$data_approve = array(
								'proyek_id' => $proyek_id,
								'user_id' => $user_row->user_id,
								'tgl_approve' => $tgl,
								'form_approve' => 'ALL',
								'status' => 'open',
								'kuncitutup' => $kuncitutup,
								'kuncibuka' => $kuncibuka
							);			

							$this->db->insert('simpro_tbl_approve',$data_approve);
							$status = "Anda telah melakukan APPROVAL Laporan Bulan ini!";
						}

					}
				}

				$q_update_status_approve = $this->db->query($sql2);

				$q_approve1 = $q_update_status_approve->row_array(0);
				$q_approve2 = $q_update_status_approve->row_array(1);

				if ($q_update_status_approve->num_rows() >= 2) {					
			        if ($q_approve1['kuncitutup'] == 1 && $q_approve2['kuncitutup'] == 1) {
			            $this->db->query("update simpro_tbl_approve set status='close' where tgl_approve='$tgl' and proyek_id='$proyek_id' and form_approve='ALL'");
			        } elseif ($q_approve1['kuncitutup'] == 0 && $q_approve2['kuncitutup'] == 0) {
			            $this->db->query("update simpro_tbl_approve set status='open' where tgl_approve='$tgl' and proyek_id='$proyek_id' and form_approve='ALL'");
			        }
				}
			} else {
				$status = "MAAF USER BELUM DITENTUKAN OLEH ADMIN UNTUK MELAKUKAN APPROVAL";
			}
		} else {
			$status = "MAAF ANDA TIDAK DIPERBOLEHKAN UNTUK MELAKUKAN APPROVAL";
		}


		return '{"success":"true","data":'.json_encode($status).'}';
	}

	function delete_data($info,$data)
	{
		$this->db->trans_begin();

		switch ($info) {
			case 'all_kontrak_terkini':
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];

				$var = array(
					'proyek_id' => $proyek_id, 
					'tahap_tanggal_kendali' => $tgl_rab
				);

				$var_ctg = array(
					'id_proyek' => $proyek_id, 
					'tanggal_kendali' => $tgl_rab
				);

				$sql_cek_jml_kk = "select tahap_tanggal_kendali from simpro_tbl_kontrak_terkini group by tahap_tanggal_kendali";
				$q_cek_jml_kk = $this->db->query($sql_cek_jml_kk);

				$sql_cek_detail_cb = "select
									CASE WHEN 
									(a.tanggal_kendali) = 
									(CASE WHEN 
									(SELECT
									count(*) as jml_data
									FROM
									(SELECT
									distinct(tanggal_kendali)
									FROM
									simpro_current_budget_item_tree where id_proyek = a.id_proyek
									) as q_tgl) = 1
									THEN 
									(SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = a.id_proyek)
									ELSE 
									(SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
									WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = a.id_proyek
									ORDER BY tanggal_kendali desc limit 1)::date
									END)
									THEN 'open'
									ELSE 'close'
									END as detail
									from 
									simpro_current_budget_item_tree a
									left join simpro_tbl_kontrak_terkini d on d.tgl_akhir = a.tanggal_kendali and d.proyek_id = a.id_proyek
									WHERE a.tanggal_kendali = '$tgl_rab' and a.id_proyek = $proyek_id
									group by a.tanggal_kendali, a.id_proyek
									order by a.tanggal_kendali desc";

				$q_cek_detail_cb = $this->db->query($sql_cek_detail_cb);

				$this->db->delete('simpro_tbl_total_rkp', $var);
				$this->db->delete('simpro_tbl_total_pekerjaan', $var);				
				$this->db->delete('simpro_tbl_kontrak_terkini', $var);
				$this->db->delete('simpro_tbl_po2', $var);
				$this->db->delete('simpro_tbl_rencana_kontrak_terkini', $var);
				$this->db->delete('simpro_tbl_rpbk', $var);
				$this->db->delete('simpro_tbl_cashin', $var);

				$this->db->delete('simpro_costogo_analisa_apek', $var_ctg);
				$this->db->delete('simpro_costogo_analisa_asat', $var_ctg);
				$this->db->delete('simpro_costogo_analisa_daftar', $var_ctg);
				$this->db->delete('simpro_costogo_analisa_item_apek', $var_ctg);
				$this->db->delete('simpro_costogo_item_tree', $var_ctg);

				if ($q_cek_jml_kk->num_rows() == 1) {
					$this->db->delete('simpro_tbl_kkp',  array(
						'proyek_id' => $proyek_id
					));
					$this->db->delete('simpro_tbl_mos',  array(
						'proyek_id' => $proyek_id
					));

					$q_sch_proyek_parent = $this->db->get('simpro_tbl_sch_proyek',array('proyek_id' => $proyek_id));
					if ($q_sch_proyek_parent->result()) {
						foreach ($q_sch_proyek_parent->result() as $r_proyek_parent) {
							$this->db->delete('simpro_tbl_sch_proyek_parent',  array(
								'id_sch_proyek' => $r_proyek_parent->id
							));
						}
					}

					$q_sch_proyek_parent_alat = $this->db->get('simpro_tbl_sch_proyek_alat',array('proyek_id' => $proyek_id));
					if ($q_sch_proyek_parent_alat->result()) {
						foreach ($q_sch_proyek_parent_alat->result() as $r_proyek_parent_alat) {
							$this->db->delete('simpro_tbl_sch_proyek_parent_alat',  array(
								'id_sch_proyek' => $r_proyek_parent_alat->id
							));
						}
					}

					$q_sch_proyek_parent_bahan = $this->db->get('simpro_tbl_sch_proyek_bahan',array('proyek_id' => $proyek_id));
					if ($q_sch_proyek_parent_bahan->result()) {
						foreach ($q_sch_proyek_parent_bahan->result() as $r_proyek_parent_bahan) {
							$this->db->delete('simpro_tbl_sch_proyek_parent_bahan',  array(
								'id_sch_proyek' => $r_proyek_parent_bahan->id
							));
						}
					}

					$q_sch_proyek_parent_person = $this->db->get('simpro_tbl_sch_proyek_person',array('proyek_id' => $proyek_id));
					if ($q_sch_proyek_parent_person->result()) {
						foreach ($q_sch_proyek_parent_person->result() as $r_proyek_parent_person) {
							$this->db->delete('simpro_tbl_sch_proyek_parent_person',  array(
								'id_sch_proyek' => $r_proyek_parent_person->id
							));
						}
					}

					$q_sch_guna_alat = $this->db->get('simpro_tbl_guna_alat',array('proyek_id' => $proyek_id));
					if ($q_sch_guna_alat->result()) {
						foreach ($q_sch_guna_alat->result() as $r_guna) {
							$this->db->delete('simpro_tbl_guna_alat_parent',  array(
								'id_guna_alat' => $r_guna->id
							));
						}
					}

					$this->db->delete('simpro_tbl_sch_proyek',  array(
						'proyek_id' => $proyek_id
					));
					$this->db->delete('simpro_tbl_sch_proyek_alat',  array(
						'proyek_id' => $proyek_id
					));
					$this->db->delete('simpro_tbl_sch_proyek_bahan',  array(
						'proyek_id' => $proyek_id
					));
					$this->db->delete('simpro_tbl_sch_proyek_person',  array(
						'proyek_id' => $proyek_id
					));
					$this->db->delete('simpro_tbl_guna_alat',  array(
						'proyek_id' => $proyek_id
					));

					$this->db->delete('simpro_tbl_daftar_peralatan',  array(
						'proyek_id' => $proyek_id
					));
				}

				$this->db->delete('simpro_tbl_approve', array(
					'proyek_id' => $proyek_id, 
					'tgl_approve' => $tgl_rab
				));

				$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
				$tahun = $chars[0];
				$bulan = $chars[1];
				$bulan_new = $chars[1]-1;

				if ($bulan_new == 0) {
					$bulan_new = '12';
					$tahun = $tahun - 1;
				}

				$tgl_rab_new = $tahun.'-'.$bulan_new.'-01';

				$row_cek_detail_cb = $q_cek_detail_cb->row();

				if ($row_cek_detail_cb->detail == 'close') {
					$var_set = array(
						'tanggal_kendali' => $tgl_rab_new
					);
					$var_where = array(
						'id_proyek' =>  $proyek_id,
						'tanggal_kendali' => $tgl_rab
					);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_analisa_apek',$var_set);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_analisa_asat',$var_set);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_analisa_daftar',$var_set);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_analisa_item_apek',$var_set);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_item_tree',$var_set);
				} else {
					$this->db->delete('simpro_current_budget_analisa_apek', $var_ctg);
					$this->db->delete('simpro_current_budget_analisa_asat', $var_ctg);
					$this->db->delete('simpro_current_budget_analisa_daftar', $var_ctg);
					$this->db->delete('simpro_current_budget_analisa_item_apek', $var_ctg);
					$this->db->delete('simpro_current_budget_item_tree', $var_ctg);
				}

				// $this->db->delete('simpro_current_budget_analisa_apek', $var_ctg);
				// $this->db->delete('simpro_current_budget_analisa_asat', $var_ctg);
				// $this->db->delete('simpro_current_budget_analisa_daftar', $var_ctg);
				// $this->db->delete('simpro_current_budget_analisa_item_apek', $var_ctg);
				// $this->db->delete('simpro_current_budget_item_tree', $var_ctg);
				
			break;
			case 'all_kontrak_terkini_new':
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];

				$var = array(
					'proyek_id' => $proyek_id, 
					'tahap_tanggal_kendali' => $tgl_rab
				);

				$var_ctg = array(
					'id_proyek' => $proyek_id, 
					'tanggal_kendali' => $tgl_rab
				);

				$sql_cek_detail_cb = "select
									CASE WHEN 
									(a.tanggal_kendali) = 
									(CASE WHEN 
									(SELECT
									count(*) as jml_data
									FROM
									(SELECT
									distinct(tanggal_kendali)
									FROM
									simpro_current_budget_item_tree where id_proyek = a.id_proyek
									) as q_tgl) = 1
									THEN 
									(SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = a.id_proyek)
									ELSE 
									(SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
									WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = a.id_proyek
									ORDER BY tanggal_kendali desc limit 1)::date
									END)
									THEN 'open'
									ELSE 'close'
									END as detail
									from 
									simpro_current_budget_item_tree a
									left join simpro_tbl_kontrak_terkini d on d.tgl_akhir = a.tanggal_kendali and d.proyek_id = a.id_proyek
									WHERE a.tanggal_kendali = '$tgl_rab' and a.id_proyek = $proyek_id
									group by a.tanggal_kendali, a.id_proyek
									order by a.tanggal_kendali desc";

				$q_cek_detail_cb = $this->db->query($sql_cek_detail_cb);

				$this->db->delete('simpro_tbl_total_rkp', $var);
				$this->db->delete('simpro_tbl_total_pekerjaan', $var);
				$this->db->delete('simpro_tbl_po2', $var); 
				$this->db->delete('simpro_tbl_rencana_kontrak_terkini', $var);
				$this->db->delete('simpro_tbl_cashin', $var);

				$this->db->delete('simpro_tbl_approve', array(
					'proyek_id' => $proyek_id, 
					'tgl_approve' => $tgl_rab
				));


				$this->db->delete('simpro_costogo_analisa_apek', $var_ctg);
				$this->db->delete('simpro_costogo_analisa_asat', $var_ctg);
				$this->db->delete('simpro_costogo_analisa_daftar', $var_ctg);
				$this->db->delete('simpro_costogo_analisa_item_apek', $var_ctg);
				$this->db->delete('simpro_costogo_item_tree', $var_ctg);


				$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
				$tahun = $chars[0];
				$bulan = $chars[1];
				$bulan_new = $chars[1]-1;

				if ($bulan_new == 0) {
					$bulan_new = '12';
					$tahun = $tahun - 1;
				}

				$tgl_rab_new = $tahun.'-'.$bulan_new.'-01';

				$row_cek_detail_cb = $q_cek_detail_cb->row();

				if ($row_cek_detail_cb->detail == 'close') {
					$var_set = array(
						'tanggal_kendali' => $tgl_rab_new
					);
					$var_where = array(
						'id_proyek' =>  $proyek_id,
						'tanggal_kendali' => $tgl_rab
					);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_analisa_apek',$var_set);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_analisa_asat',$var_set);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_analisa_daftar',$var_set);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_analisa_item_apek',$var_set);
					$this->db->where($var_where);
					$this->db->update('simpro_current_budget_item_tree',$var_set);
				} else {
					$this->db->delete('simpro_current_budget_analisa_apek', $var_ctg);
					$this->db->delete('simpro_current_budget_analisa_asat', $var_ctg);
					$this->db->delete('simpro_current_budget_analisa_daftar', $var_ctg);
					$this->db->delete('simpro_current_budget_analisa_item_apek', $var_ctg);
					$this->db->delete('simpro_current_budget_item_tree', $var_ctg);
				}

				$sql_up = "update simpro_tbl_kontrak_terkini set tgl_akhir = '$tgl_rab_new' where proyek_id=$proyek_id and tgl_akhir='$tgl_rab'";
				$this->db->query($sql_up);

			break;
			case 'cost_togo':
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];
				$kode = $data['kode'];

				$var_kode = array(
					'tahap_tanggal_kendali' => $tgl_rab,
					'proyek_id' => $proyek_id,
					'tahap_kode_kendali' => $kode
				);				
				$var_kode_induk = array(
					'tahap_tanggal_kendali' => $tgl_rab,
					'proyek_id' => $proyek_id,
					'left(tahap_kode_kendali,(length(tahap_kode_kendali))-2)' => $kode
				);

				$this->db->delete('simpro_tbl_cost_togo', $var_kode);
				$this->db->delete('simpro_tbl_komposisi_togo', $var_kode);
				$this->db->delete('simpro_tbl_induk_togo', $var_kode);
				$this->db->delete('simpro_tbl_cost_togo', $var_kode_induk);
				$this->db->delete('simpro_tbl_komposisi_togo', $var_kode_induk);
				$this->db->delete('simpro_tbl_induk_togo', $var_kode_induk);
			break;
			case 'current_budget':
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];
				$kode = $data['kode'];

				$var_kode = array(
					'tahap_tanggal_kendali' => $tgl_rab,
					'proyek_id' => $proyek_id,
					'tahap_kode_kendali' => $kode
				);				
				$var_kode_induk = array(
					'tahap_tanggal_kendali' => $tgl_rab,
					'proyek_id' => $proyek_id,
					'left(tahap_kode_kendali,(length(tahap_kode_kendali))-2)' => $kode
				);

				// $this->db->delete('simpro_tbl_current_budget', $var_kode_induk);

				$this->db->delete('simpro_tbl_current_budget', $var_kode);
				$this->db->delete('simpro_tbl_komposisi_budget', $var_kode);
				$this->db->delete('simpro_tbl_induk_budget', $var_kode);
				$this->db->delete('simpro_tbl_current_budget', $var_kode_induk);
				$this->db->delete('simpro_tbl_komposisi_budget', $var_kode_induk);
				$this->db->delete('simpro_tbl_induk_budget', $var_kode_induk);


			break;
			case 'analisa_cost_togo':
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];
				$kode = $data['kode'];

				$var = array('komposisi_togo_id' => $kode);
				$this->db->delete('simpro_tbl_komposisi_togo',$var);
			break;
			case 'analisa_current_budget':
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];
				$kode = $data['kode'];

				$var = array('komposisi_budget_id' => $kode);
				$this->db->delete('simpro_tbl_komposisi_budget',$var);
			break;
			case 'kkp':
				$proyek_id = $data['proyek_id'];
				$kode = $data['kode'];

				$var = array('kkp_id' => $kode);
				$this->db->delete('simpro_tbl_kkp',$var);
			break;
			case 'mos':
				$proyek_id = $data['proyek_id'];
				$kode = $data['kode'];

				$var = array('mos_id' => $kode);
				$this->db->delete('simpro_tbl_mos',$var);
			break;
			case 'daftar_peralatan':
				$proyek_id = $data['proyek_id'];
				$kode = $data['kode'];

				$var = array('daftar_peralatan_id' => $kode);
				$this->db->delete('simpro_tbl_daftar_peralatan',$var);
			break;
			case 'currentbudgetall':
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];

				$var = array(
					'id_proyek' => $proyek_id, 
					'tanggal_kendali' => $tgl_rab
				);
				$this->db->delete('simpro_current_budget_analisa_apek', $var);
				$this->db->delete('simpro_current_budget_analisa_asat', $var);
				$this->db->delete('simpro_current_budget_analisa_daftar', $var);
				$this->db->delete('simpro_current_budget_analisa_item_apek', $var);
				$this->db->delete('simpro_current_budget_item_tree', $var);

				$sql_get_last_cb = "select tanggal_kendali 
				from simpro_current_budget_item_tree
				where tanggal_kendali < '$tgl_rab'
				group by tanggal_kendali
				order by tanggal_kendali desc limit 1";

				$q_get_last_cb = $this->db->query($sql_get_last_cb);
				$row_cb = $q_get_last_cb->row();

				$tgl_rab_old = $row_cb->tanggal_kendali;

				$var_set = array(
					'tanggal_kendali' => $tgl_rab
				);
				$var_where = array(
					'tanggal_kendali' => $tgl_rab_old,
					'id_proyek' => $proyek_id
				);

				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_analisa_apek',$var_set);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_analisa_asat',$var_set);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_analisa_daftar',$var_set);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_analisa_item_apek',$var_set);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_item_tree',$var_set);

			break;
			case 'item_kontrak_terkini':	
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];
				$kode = $data['kode'];
				$kode_tree = "1.".$data['kode'];
				$id = $data['id'];

				$sql_cek_kode_analisa_ctg = "select
										*
										from
										simpro_costogo_analisa_item_apek
										where id_proyek=$proyek_id and
										kode_tree='$kode_tree' and
										tanggal_kendali='$tgl_rab'";

				$itung_kode_analisa_ctg = $this->db->query($sql_cek_kode_analisa_ctg)->num_rows();

					if($itung_kode_analisa_ctg > 0){
					$sql_get_kode_analisa_ctg = "select
												b.kode_analisa, b.id_proyek, b.kode_tree, b.tanggal_kendali
												from
												simpro_costogo_item_tree as a INNER JOIN simpro_costogo_analisa_item_apek as b ON
												a.id_proyek=b.id_proyek and a.tanggal_kendali=b.tanggal_kendali and a.kode_tree=b.kode_tree
												where
												a.id_proyek=$proyek_id and a.kode_tree='$kode_tree' and a.tanggal_kendali='$tgl_rab'";

					$q_kode_analisa_ctg = $this->db->query($sql_get_kode_analisa_ctg)->row();
					$kode_analisa_ctg = $q_kode_analisa_ctg->kode_analisa;

					$sql_cek_banyak_kode_analisa_ctg = "select * from simpro_costogo_analisa_item_apek where kode_analisa='$kode_analisa_ctg'";
					$itung_banyak_kode_analisa_ctg = $this->db->query($sql_cek_banyak_kode_analisa_ctg)->num_rows();

						$var_aiap_ctg = array(
							'id_proyek' => $proyek_id,
							'kode_tree' => $kode_tree, 
							'tanggal_kendali' => $tgl_rab
						);

						$var_aap_ctg = array(
							'id_proyek' => $proyek_id,
							'parent_kode_analisa' => $kode_analisa_ctg, 
							'tanggal_kendali' => $tgl_rab
						);

						$var_aas_ctg = array(
							'id_proyek' => $proyek_id,
							'kode_analisa' => $kode_analisa_ctg, 
							'tanggal_kendali' => $tgl_rab
						);

						if($itung_banyak_kode_analisa_ctg > 1){
							$this->db->delete('simpro_costogo_analisa_item_apek', $var_aiap_ctg);
						} else {
							$this->db->delete('simpro_costogo_analisa_item_apek', $var_aiap_ctg);
							$this->db->delete('simpro_costogo_analisa_apek', $var_aap_ctg);
							$this->db->delete('simpro_costogo_analisa_asat', $var_aas_ctg);
						}
					}

				$sql_cek_kode_analisa_cbd = "select
											*
											from
											simpro_current_budget_analisa_item_apek
											where id_proyek=$proyek_id and
											kode_tree='$kode_tree' and
											tanggal_kendali='$tgl_rab'";

				$itung_kode_analisa_cbd = $this->db->query($sql_cek_kode_analisa_cbd)->num_rows();

					if($itung_kode_analisa_cbd > 0){
					$sql_get_kode_analisa_cbd = "select
												b.kode_analisa, b.id_proyek, b.kode_tree, b.tanggal_kendali
												from
												simpro_current_budget_item_tree as a INNER JOIN simpro_current_budget_analisa_item_apek as b ON
												a.id_proyek=b.id_proyek and a.tanggal_kendali=b.tanggal_kendali and a.kode_tree=b.kode_tree
												where
												a.id_proyek=$proyek_id and a.kode_tree='$kode_tree' and a.tanggal_kendali='$tgl_rab'";

					$q_kode_analisa_cbd = $this->db->query($sql_get_kode_analisa_cbd)->row();
					$kode_analisa_cbd = $q_kode_analisa_cbd->kode_analisa;

					$sql_cek_banyak_kode_analisa_cbd = "select * from simpro_current_budget_analisa_item_apek where kode_analisa='$kode_analisa_cbd'";
					$itung_banyak_kode_analisa_cbd = $this->db->query($sql_cek_banyak_kode_analisa_cbd)->num_rows();

						$var_aiap_cbd = array(
							'id_proyek' => $proyek_id,
							'kode_tree' => $kode_tree, 
							'tanggal_kendali' => $tgl_rab
						);

						$var_aap_cbd = array(
							'id_proyek' => $proyek_id,
							'parent_kode_analisa' => $kode_analisa_cbd, 
							'tanggal_kendali' => $tgl_rab
						);

						$var_aas_cbd = array(
							'id_proyek' => $proyek_id,
							'kode_analisa' => $kode_analisa_cbd, 
							'tanggal_kendali' => $tgl_rab
						);

						if($itung_banyak_kode_analisa_cbd > 1){
							$this->db->delete('simpro_current_budget_analisa_item_apek', $var_aiap_cbd);
						} else {
							$this->db->delete('simpro_current_budget_analisa_item_apek', $var_aiap_cbd);
							$this->db->delete('simpro_current_budget_analisa_apek', $var_aap_cbd);
							$this->db->delete('simpro_current_budget_analisa_asat', $var_aas_cbd);
						}
					}

				$var_kk = array(
					'tahap_kode_kendali' => $kode,
					'proyek_id' => $proyek_id, 
					'tahap_tanggal_kendali' => $tgl_rab
				);

				$var_cbd_ctg = array(
					'kode_tree' => $kode_tree,
					'id_proyek' => $proyek_id, 
					'tanggal_kendali' => $tgl_rab
				);

				$var_kk_id = array(
					'kontrak_terkini_id' => $id
				);

				$this->db->delete('simpro_tbl_kontrak_terkini',$var_kk);

				$this->db->delete('simpro_current_budget_item_tree', $var_cbd_ctg);
				$this->db->delete('simpro_costogo_item_tree', $var_cbd_ctg);

				$this->db->delete('simpro_tbl_total_rkp', $var_kk_id);
				$this->db->delete('simpro_tbl_total_pekerjaan', $var_kk_id);
				$this->db->delete('simpro_tbl_rencana_kontrak_terkini', $var_kk);
			break;
			case 'item_rencana_kontrak_terkini':	
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];
				$kode = $data['kode'];
				$id = $data['id'];

				$var_rkk = array(
					'id_rencana_kontrak_terkini' => $id
				);

				$this->db->delete('simpro_tbl_rencana_kontrak_terkini', $var_rkk);
			break;
			case 'analisa_ctg_all':
				$proyek_id = $data['proyek_id'];
				$tgl_rab = $data['tgl_rab'];
				$kode = $data['kode'];

				$var = array(
					'proyek_id' => $proyek_id,
					'tahap_kode_kendali' => $kode,
					'tahap_tanggal_kendali' => $tgl_rab
				);

				$this->db->delete('simpro_tbl_komposisi_togo', $var);
			break;
		}

		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		}
		else
		{
		    $this->db->trans_commit();
		}
	}

	function get_status_approve($proyek_id)
	{
		$sql = "select 
				COALESCE((b.status),'open') as value
				from 
				simpro_tbl_kontrak_terkini a
				left join simpro_tbl_approve b on b.tgl_approve = a.tgl_akhir  and b.form_approve='ALL'
				where a.proyek_id = $proyek_id
				order by tgl_akhir desc, b.proyek_id DESC limit 1";
		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$data['value'] = "";
			$dat[] = $data;
		}
		
    	// }
		return '{"data":'.json_encode($dat).'}';
	}

	function get_status_approve_cb($proyek_id)
	{
		$sql = "select 
				COALESCE((b.status),'open') as value
				from 
				simpro_current_budget_item_tree a
				left join simpro_tbl_approve b on b.tgl_approve = a.tanggal_kendali  and b.form_approve='ALL'
				where a.id_proyek = $proyek_id
				order by tanggal_kendali desc, b.proyek_id DESC limit 1";
		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$data['value'] = "";
			$dat[] = $data;
		}
		
    	// }
		return '{"data":'.json_encode($dat).'}';
	}

	function copy_data($info,$tgl_rab,$proyek_id)
	{
		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');

		$data_cost_to_go_induk1 = array(
			'tahap_kode_kendali' => '1', 
			'tahap_nama_kendali' => 'Biaya Langsung',
			'tahap_satuan_kendali' => 31,
			'proyek_id' => $proyek_id,
			'tahap_volume_kendali' => '1',
			'tahap_kode_induk_kendali' => '',
			'tahap_tanggal_kendali' => $tgl_rab,
			'tahap_harga_satuan_kendali' => 0,
			'tahap_total_kendali' => 0,
			'user_id' => $user_update,
			'tgl_update' => $tgl_update,
			'ip_update' => $ip_update,
			'divisi_id' => $divisi_id,
			'waktu_update' => $waktu_update
		);

		$data_cost_to_go_induk2 = array(
			'tahap_kode_kendali' => '2', 
			'tahap_nama_kendali' => 'Biaya Tidak Langsung',
			'tahap_satuan_kendali' => 31,
			'proyek_id' => $proyek_id,
			'tahap_volume_kendali' => '1',
			'tahap_kode_induk_kendali' => '',
			'tahap_tanggal_kendali' => $tgl_rab,
			'tahap_harga_satuan_kendali' => 0,
			'tahap_total_kendali' => 0,
			'user_id' => $user_update,
			'tgl_update' => $tgl_update,
			'ip_update' => $ip_update,
			'divisi_id' => $divisi_id,
			'waktu_update' => $waktu_update
		);

		$sql = "WITH rows as (INSERT INTO
				simpro_tbl_kontrak_terkini
				(
				tahap_kode_kendali,
				tahap_nama_kendali,
				tahap_satuan_kendali,
				tahap_volume_kendali,
				tahap_harga_satuan_kendali,
				tahap_kode_induk_kendali,
				tahap_tanggal_kendali,
				proyek_id,
				user_update,
				divisi_update,
				tgl_update,
				waktu_update,
				ip_update
				)
				SELECT
				tahap_kode_kendali,
				tahap_nama_kendali,
				tahap_satuan_kendali,
				tahap_volume_kendali,
				tahap_harga_satuan_kendali,
				tahap_kode_induk_kendali,
				'$tgl_rab',
				$proyek_id,
				$user_update,
				$divisi_id,
				'$tgl_update',
				'$waktu_update',
				'$ip_update'
				FROM
				simpro_tbl_input_kontrak
				where proyek_id = $proyek_id RETURNING id_kontrak_terkini)
				SELECT id_kontrak_terkini from rows";

		$sql_budget = "INSERT INTO
				simpro_tbl_current_budget
				(
				tahap_kode_kendali,
				tahap_nama_kendali,
				tahap_satuan_kendali,
				tahap_volume_kendali,
				tahap_harga_satuan_kendali,
				tahap_kode_induk_kendali,
				tahap_tanggal_kendali,
				proyek_id,
				user_id,
				divisi_id,
				tgl_update,
				waktu_update,
				ip_update				
				)
				SELECT
				'1.' || tahap_kode_kendali,
				tahap_nama_kendali,
				tahap_satuan_kendali,
				tahap_volume_kendali,
				tahap_harga_satuan_kendali,
				left(('1.' || tahap_kode_kendali), length(('1.' || tahap_kode_kendali)) - 2),
				'$tgl_rab',
				$proyek_id,
				$user_update,
				$divisi_id,
				'$tgl_update',
				'$waktu_update',
				'$ip_update'
				FROM
				simpro_tbl_input_kontrak
				where proyek_id = $proyek_id";

		$sql_togo = "INSERT INTO
				simpro_tbl_cost_togo
				(
				tahap_kode_kendali,
				tahap_nama_kendali,
				tahap_satuan_kendali,
				tahap_volume_kendali,
				tahap_harga_satuan_kendali,
				tahap_kode_induk_kendali,
				tahap_tanggal_kendali,
				proyek_id,
				user_id,
				divisi_id,
				tgl_update,
				waktu_update,
				ip_update				
				)
				SELECT
				'1.' || tahap_kode_kendali,
				tahap_nama_kendali,
				tahap_satuan_kendali,
				tahap_volume_kendali,
				tahap_harga_satuan_kendali,
				left(('1.' || tahap_kode_kendali), length(('1.' || tahap_kode_kendali)) - 2),
				'$tgl_rab',
				$proyek_id,
				$user_update,
				$divisi_id,
				'$tgl_update',
				'$waktu_update',
				'$ip_update'
				FROM
				simpro_tbl_input_kontrak
				where proyek_id = $proyek_id";

		$this->db->trans_begin();

		$row = $this->db->query($sql);

		if ($row->result() > 0) {
			foreach ($row->result() as $r) {
				$data['id_kontrak_terkini'] = $r->id_kontrak_terkini;
				$data = array(
					'kontrak_terkini_id' => $data['id_kontrak_terkini']
				);
				$this->db->insert('simpro_tbl_total_pekerjaan',$data);

				$this->db->insert('simpro_tbl_total_rkp',$data);
				// $dat[] = $data;
			}
		}

		// $last_id = $this->db->insert_id();
		

		// var_dump($dat);

		$this->db->query($sql_budget);

		$this->db->query($sql_togo);

		$data_induk_id = $this->get_data_induk($proyek_id,$tgl_rab);
		
		if ($data_induk_id == 0) {
			$this->insert_induk_togo($data_cost_to_go_induk1);

			$this->insert_induk_togo($data_cost_to_go_induk2);
		}

		if ($this->db->trans_status() === FALSE)
		{
		$this->db->trans_rollback();
		}
		else
		{
		    $this->db->trans_commit();
		}
	}

	function copy($info,$tgl_rab,$proyek_id)
	{

		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');

		$this->db->trans_begin();

		switch ($info) {
			case 'awal':

				$sql_new = "WITH rows as (INSERT INTO
						simpro_tbl_kontrak_terkini
						(
						tahap_kode_kendali,
						tahap_nama_kendali,
						tahap_satuan_kendali,
						tahap_volume_kendali,
						tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						tahap_tanggal_kendali,
						proyek_id,
						user_update,
						divisi_update,
						tgl_update,
						waktu_update,
						ip_update,
						tgl_akhir
						)
						select
						tahap_kode_kendali as rab_tahap_kode_kendali,
						tahap_nama_kendali as rab_tahap_nama_kendali,
						(select satuan_id from simpro_tbl_satuan where lower(satuan_nama) = lower(tahap_satuan_kendali)) as rab_tahap_satuan_kendali,
						coalesce(tahap_volume_kendali,0) as rab_tahap_volume_kendali,
						coalesce(tahap_harga_satuan_kendali,0) as rab_tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						'$tgl_rab',
						$proyek_id,
						$user_update,
						$divisi_id,
						'$tgl_update',
						'$waktu_update',
						'$ip_update',
						'$tgl_rab'
						from 
						simpro_tbl_input_kontrak
						where proyek_id = $proyek_id
						RETURNING id_kontrak_terkini)
						SELECT id_kontrak_terkini from rows";

				$sql_copy_costogo_analisa_apek = "
				 insert into simpro_costogo_analisa_apek
				 (
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				'$tgl_rab'
				 from simpro_rap_analisa_apek
				 where id_proyek = $proyek_id";
				$sql_copy_costogo_analisa_asat = "
				 insert into simpro_costogo_analisa_asat
				 (
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				'$tgl_rab'
				 from
				 simpro_rap_analisa_asat
				 where id_proyek = $proyek_id";
				$sql_copy_costogo_analisa_daftar = "
				 insert into simpro_costogo_analisa_daftar
				 (
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				 tanggal_kendali
				 )
				 select
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				'$tgl_rab'
				 from 
				 simpro_rap_analisa_daftar
				 where id_proyek = $proyek_id";
				$sql_copy_costogo_analisa_item_apek = "
				 insert into simpro_costogo_analisa_item_apek
				 (
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 costogo_item_tree,
				 kode_tree,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 rap_item_tree,
				 kode_tree,
				'$tgl_rab'
				 from
				 simpro_rap_analisa_item_apek
				 where id_proyek = $proyek_id";
				$sql_copy_costogo_analisa_item_tree = "
				 insert into simpro_costogo_item_tree
				 (
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
				 tree_parent_kode,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
							case when right(tree_parent_kode,1) = '.'
							then left(tree_parent_kode,(length(tree_parent_kode)-1))
							else tree_parent_kode
							end,
				'$tgl_rab'
				 from
				 simpro_rap_item_tree
				 where id_proyek = $proyek_id";

				$sql_copy_current_budget_analisa_apek = "
				 insert into simpro_current_budget_analisa_apek
				 (
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				'$tgl_rab'
				 from simpro_rap_analisa_apek
				 where id_proyek = $proyek_id";
				$sql_copy_current_budget_analisa_asat = "
				 insert into simpro_current_budget_analisa_asat
				 (
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				'$tgl_rab'
				 from
				 simpro_rap_analisa_asat
				 where id_proyek = $proyek_id";
				$sql_copy_current_budget_analisa_daftar = "
				 insert into simpro_current_budget_analisa_daftar
				 (
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				 tanggal_kendali
				 )
				 select
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				'$tgl_rab'
				 from 
				 simpro_rap_analisa_daftar
				 where id_proyek = $proyek_id";
				$sql_copy_current_budget_analisa_item_apek = "
				 insert into simpro_current_budget_analisa_item_apek
				 (
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 current_budget_item_tree,
				 kode_tree,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 rap_item_tree,
				 kode_tree,
				'$tgl_rab'
				 from
				 simpro_rap_analisa_item_apek
				 where id_proyek = $proyek_id";
				$sql_copy_current_budget_analisa_item_tree = "
				 insert into simpro_current_budget_item_tree
				 (
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
				 tree_parent_kode,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
							case when right(tree_parent_kode,1) = '.'
							then left(tree_parent_kode,(length(tree_parent_kode)-1))
							else tree_parent_kode
							end,
				'$tgl_rab'
				 from
				 simpro_rap_item_tree
				 where id_proyek = $proyek_id";

				$sql_rkk_new = "INSERT INTO
						simpro_tbl_rencana_kontrak_terkini
						(
						tahap_kode_kendali,
						tahap_nama_kendali,
						tahap_satuan_kendali,
						tahap_volume_kendali,
						tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						tahap_tanggal_kendali,
						proyek_id,
						user_update,
						divisi_update,
						tgl_update,
						waktu_update,
						ip_update
						)
						select
						tahap_kode_kendali as rab_tahap_kode_kendali,
						tahap_nama_kendali as rab_tahap_nama_kendali,
						(select satuan_id from simpro_tbl_satuan where lower(satuan_nama) = lower(tahap_satuan_kendali)) as rab_tahap_satuan_kendali,
						coalesce(tahap_volume_kendali,0) as rab_tahap_volume_kendali,
						coalesce(tahap_harga_satuan_kendali,0) as rab_tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						'$tgl_rab',
						$proyek_id,
						$user_update,
						$divisi_id,
						'$tgl_update',
						'$waktu_update',
						'$ip_update'
						from 
						simpro_tbl_input_kontrak
						where proyek_id = $proyek_id";

				$row = $this->db->query($sql_new);

				if ($row->result() > 0) {
					foreach ($row->result() as $r) {
						$data['id_kontrak_terkini'] = $r->id_kontrak_terkini;
						$data = array(
							'proyek_id' => $proyek_id,
							'tahap_tanggal_kendali' => $tgl_rab,
							'kontrak_terkini_id' => $data['id_kontrak_terkini']
						);
						$this->db->insert('simpro_tbl_total_pekerjaan',$data);

						$this->db->insert('simpro_tbl_total_rkp',$data);

						// $this->db->insert('simpro_tbl_rencana_kontrak_terkini',$data);
					}
				}

				$this->db->query($sql_rkk_new);

				$this->db->query($sql_copy_costogo_analisa_item_tree);
				$this->db->query($sql_copy_costogo_analisa_apek);
				$this->db->query($sql_copy_costogo_analisa_asat);
				$this->db->query($sql_copy_costogo_analisa_daftar);
				$this->db->query($sql_copy_costogo_analisa_item_apek);

				$this->db->query($sql_copy_current_budget_analisa_item_tree);
				$this->db->query($sql_copy_current_budget_analisa_apek);
				$this->db->query($sql_copy_current_budget_analisa_asat);
				$this->db->query($sql_copy_current_budget_analisa_daftar);
				$this->db->query($sql_copy_current_budget_analisa_item_apek);

			break;
			case 'non_kontrak':
				$sql_tgl_rab = "select tgl_akhir, EXTRACT(YEAR FROM tgl_akhir) as tahun, EXTRACT(MONTH FROM tgl_akhir) as bulan, (EXTRACT(MONTH FROM tgl_akhir) + 1) as bulan_new
						from simpro_tbl_kontrak_terkini 
						where proyek_id=$proyek_id order by tgl_akhir desc limit 1";

				$q_rab = $this->db->query($sql_tgl_rab);
				$tanggal = $q_rab->row();

				$tahun = $tanggal->tahun;
				$bulan = $tanggal->bulan;
				$bulan_new = $tanggal->bulan_new;
				$tgl_rab = $tanggal->tgl_akhir;

				if ($bulan_new == 13) {
					$bulan_new = '01';
					$tahun = $tahun + 1;
				}

				$tgl_rab_new = $tahun.'-'.$bulan_new.'-01';

				$sql = "
					WITH rows as (update simpro_tbl_kontrak_terkini set tgl_akhir = '$tgl_rab_new' where proyek_id=$proyek_id and tgl_akhir='$tgl_rab' RETURNING id_kontrak_terkini)
					SELECT id_kontrak_terkini from rows
				";

				$sql_copy_costogo_analisa_apek = "
				 insert into simpro_costogo_analisa_apek
				 (
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				'$tgl_rab_new'
				 from simpro_costogo_analisa_apek
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";
				$sql_copy_costogo_analisa_asat = "
				 insert into simpro_costogo_analisa_asat
				 (
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				'$tgl_rab_new'
				 from
				 simpro_costogo_analisa_asat
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";
				$sql_copy_costogo_analisa_daftar = "
				 insert into simpro_costogo_analisa_daftar
				 (
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				 tanggal_kendali
				 )
				 select
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				'$tgl_rab_new'
				 from 
				 simpro_costogo_analisa_daftar
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";
				$sql_copy_costogo_analisa_item_apek = "
				 insert into simpro_costogo_analisa_item_apek
				 (
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 costogo_item_tree,
				 kode_tree,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 costogo_item_tree,
				 kode_tree,
				'$tgl_rab_new'
				 from
				 simpro_costogo_analisa_item_apek
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";
				$sql_copy_costogo_analisa_item_tree = "
				 insert into simpro_costogo_item_tree
				 (
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
				 tree_parent_kode,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
							case when right(tree_parent_kode,1) = '.'
							then left(tree_parent_kode,(length(tree_parent_kode)-1))
							else tree_parent_kode
							end,
				'$tgl_rab_new'
				 from
				 simpro_costogo_item_tree
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";

				$sql_rkk = "INSERT INTO
						simpro_tbl_rencana_kontrak_terkini
						(
						tahap_kode_kendali,
						tahap_nama_kendali,
						tahap_satuan_kendali,
						tahap_volume_kendali,
						tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						tahap_tanggal_kendali,
						proyek_id,
						user_update,
						divisi_update,
						tgl_update,
						waktu_update,
						ip_update
						)
						SELECT
						tahap_kode_kendali,
						tahap_nama_kendali,
						tahap_satuan_kendali,
						(
						(CASE WHEN tahap_volume_kendali is null
						THEN 0
						ELSE tahap_volume_kendali
						END) +
						(CASE WHEN tahap_volume_kendali_new is null
						THEN 0
						ELSE tahap_volume_kendali_new
						END) -
						(CASE WHEN tahap_volume_kendali_kurang is null
						THEN 0
						ELSE tahap_volume_kendali_kurang
						END)
						) as vol_kk,
						tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						'$tgl_rab_new',
						$proyek_id,
						$user_update,
						$divisi_id,
						'$tgl_update',
						'$waktu_update',
						'$ip_update'
						FROM
						simpro_tbl_rencana_kontrak_terkini
						where tahap_tanggal_kendali = '$tgl_rab' and
						proyek_id = $proyek_id";

				$this->db->query($sql_rkk);

				$sql_get_cb_end = "select tanggal_kendali from simpro_current_budget_item_tree order by tanggal_kendali desc limit 1";
				
				$q_get_cb_end = $this->db->query($sql_get_cb_end);
				$row_cb_end = $q_get_cb_end->row();

				$sql_update_cb_item_tree = "update simpro_current_budget_item_tree 
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$sql_update_cb_analisa_apek = "update simpro_current_budget_analisa_apek
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$sql_update_cb_analisa_asat = "update simpro_current_budget_analisa_asat
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$sql_update_cb_analisa_daftar = "update simpro_current_budget_analisa_daftar
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$sql_update_cb_analisa_item_apek = "update simpro_current_budget_analisa_item_apek
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$this->db->query($sql_update_cb_item_tree);
				$this->db->query($sql_update_cb_analisa_apek);
				$this->db->query($sql_update_cb_analisa_asat);
				$this->db->query($sql_update_cb_analisa_daftar);
				$this->db->query($sql_update_cb_analisa_item_apek);

				$this->db->query($sql_copy_costogo_analisa_item_tree);
				$this->db->query($sql_copy_costogo_analisa_apek);
				$this->db->query($sql_copy_costogo_analisa_asat);
				$this->db->query($sql_copy_costogo_analisa_daftar);
				$this->db->query($sql_copy_costogo_analisa_item_apek);

				$row = $this->db->query($sql);

				if ($row->result() > 0) {
					foreach ($row->result() as $r) {
						$data['id_kontrak_terkini'] = $r->id_kontrak_terkini;
						$data = array(
							'proyek_id' => $proyek_id,
							'tahap_tanggal_kendali' => $tgl_rab_new,
							'kontrak_terkini_id' => $data['id_kontrak_terkini']
						);
						$this->db->insert('simpro_tbl_total_pekerjaan',$data);

						$this->db->insert('simpro_tbl_total_rkp',$data);

						// $this->db->insert('simpro_tbl_rencana_kontrak_terkini',$data);
					}
				}

			break;
			case 'kontrak':
				$sql_tgl_rab = "select tgl_akhir, EXTRACT(YEAR FROM tgl_akhir) as tahun, EXTRACT(MONTH FROM tgl_akhir) as bulan, (EXTRACT(MONTH FROM tgl_akhir) + 1) as bulan_new
						from simpro_tbl_kontrak_terkini 
						where proyek_id=$proyek_id order by tgl_akhir desc limit 1";

				$sql_get_cb = "select tahap_tanggal_kendali from simpro_tbl_current_budget order by tahap_tanggal_kendali desc limit 1";
				$sql_get_tgl_kk = "select tahap_tanggal_kendali from simpro_tbl_kontrak_terkini order by tahap_tanggal_kendali desc limit 1";

				$q_rab = $this->db->query($sql_tgl_rab);
				$tanggal = $q_rab->row();

				$tahun = $tanggal->tahun;
				$bulan = $tanggal->bulan;
				$bulan_new = $tanggal->bulan_new;
				$tgl_rab = $tanggal->tgl_akhir;

				if ($bulan_new == 13) {
					$bulan_new = '01';
					$tahun = $tahun + 1;
				}

				$tgl_rab_new = $tahun.'-'.$bulan_new.'-01';

				$sql = "WITH rows as (INSERT INTO
						simpro_tbl_kontrak_terkini
						(
						tahap_kode_kendali,
						tahap_nama_kendali,
						tahap_satuan_kendali,
						tahap_volume_kendali,
						tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						tahap_tanggal_kendali,
						proyek_id,
						user_update,
						divisi_update,
						tgl_update,
						waktu_update,
						ip_update,
						tgl_akhir
						)
						SELECT
						tahap_kode_kendali,
						tahap_nama_kendali,
						tahap_satuan_kendali,
						(
						(CASE WHEN tahap_volume_kendali is null
						THEN 0
						ELSE tahap_volume_kendali
						END) +
						(CASE WHEN tahap_volume_kendali_new is null
						THEN 0
						ELSE tahap_volume_kendali_new
						END) -
						(CASE WHEN tahap_volume_kendali_kurang is null
						THEN 0
						ELSE tahap_volume_kendali_kurang
						END)
						) as vol_kk,
						tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						'$tgl_rab_new',
						$proyek_id,
						$user_update,
						$divisi_id,
						'$tgl_update',
						'$waktu_update',
						'$ip_update',
						'$tgl_rab_new'
						FROM
						simpro_tbl_kontrak_terkini
						where proyek_id = $proyek_id 
						and tgl_akhir = '$tgl_rab'
						RETURNING id_kontrak_terkini)
						SELECT id_kontrak_terkini from rows";

				$sql_copy_costogo_analisa_apek = "
				 insert into simpro_costogo_analisa_apek
				 (
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				'$tgl_rab_new'
				 from simpro_costogo_analisa_apek
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";
				$sql_copy_costogo_analisa_asat = "
				 insert into simpro_costogo_analisa_asat
				 (
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				'$tgl_rab_new'
				 from
				 simpro_costogo_analisa_asat
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";
				$sql_copy_costogo_analisa_daftar = "
				 insert into simpro_costogo_analisa_daftar
				 (
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				 tanggal_kendali
				 )
				 select
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				'$tgl_rab_new'
				 from 
				 simpro_costogo_analisa_daftar
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";
				$sql_copy_costogo_analisa_item_apek = "
				 insert into simpro_costogo_analisa_item_apek
				 (
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 costogo_item_tree,
				 kode_tree,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 costogo_item_tree,
				 kode_tree,
				'$tgl_rab_new'
				 from
				 simpro_costogo_analisa_item_apek
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";
				$sql_copy_costogo_analisa_item_tree = "
				 insert into simpro_costogo_item_tree
				 (
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
				 tree_parent_kode,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
							case when right(tree_parent_kode,1) = '.'
							then left(tree_parent_kode,(length(tree_parent_kode)-1))
							else tree_parent_kode
							end,
				'$tgl_rab_new'
				 from
				 simpro_costogo_item_tree
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_rab'";

				$sql_rkk = "INSERT INTO
						simpro_tbl_rencana_kontrak_terkini
						(
						tahap_kode_kendali,
						tahap_nama_kendali,
						tahap_satuan_kendali,
						tahap_volume_kendali,
						tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						tahap_tanggal_kendali,
						proyek_id,
						user_update,
						divisi_update,
						tgl_update,
						waktu_update,
						ip_update
						)
						SELECT
						tahap_kode_kendali,
						tahap_nama_kendali,
						tahap_satuan_kendali,
						(
						(CASE WHEN tahap_volume_kendali is null
						THEN 0
						ELSE tahap_volume_kendali
						END) +
						(CASE WHEN tahap_volume_kendali_new is null
						THEN 0
						ELSE tahap_volume_kendali_new
						END) -
						(CASE WHEN tahap_volume_kendali_kurang is null
						THEN 0
						ELSE tahap_volume_kendali_kurang
						END)
						) as vol_kk,
						tahap_harga_satuan_kendali,
						tahap_kode_induk_kendali,
						'$tgl_rab_new',
						$proyek_id,
						$user_update,
						$divisi_id,
						'$tgl_update',
						'$waktu_update',
						'$ip_update'
						FROM
						simpro_tbl_rencana_kontrak_terkini
						where tahap_tanggal_kendali = '$tgl_rab' and
						proyek_id = $proyek_id";

				$this->db->query($sql_rkk);

				$sql_get_cb_end = "select tanggal_kendali from simpro_current_budget_item_tree order by tanggal_kendali desc limit 1";
				
				$q_get_cb_end = $this->db->query($sql_get_cb_end);
				$row_cb_end = $q_get_cb_end->row();

				$sql_update_cb_item_tree = "update simpro_current_budget_item_tree 
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$sql_update_cb_analisa_apek = "update simpro_current_budget_analisa_apek
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$sql_update_cb_analisa_asat = "update simpro_current_budget_analisa_asat
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$sql_update_cb_analisa_daftar = "update simpro_current_budget_analisa_daftar
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$sql_update_cb_analisa_item_apek = "update simpro_current_budget_analisa_item_apek
				set tanggal_kendali = '$tgl_rab_new' 
				where id_proyek=$proyek_id 
				and tanggal_kendali='$row_cb_end->tanggal_kendali'";

				$this->db->query($sql_update_cb_item_tree);
				$this->db->query($sql_update_cb_analisa_apek);
				$this->db->query($sql_update_cb_analisa_asat);
				$this->db->query($sql_update_cb_analisa_daftar);
				$this->db->query($sql_update_cb_analisa_item_apek);

				$this->db->query($sql_copy_costogo_analisa_item_tree);
				$this->db->query($sql_copy_costogo_analisa_apek);
				$this->db->query($sql_copy_costogo_analisa_asat);
				$this->db->query($sql_copy_costogo_analisa_daftar);
				$this->db->query($sql_copy_costogo_analisa_item_apek);

				$row = $this->db->query($sql);

				if ($row->result() > 0) {
					foreach ($row->result() as $r) {
						$data['id_kontrak_terkini'] = $r->id_kontrak_terkini;
						$data = array(
							'proyek_id' => $proyek_id,
							'tahap_tanggal_kendali' => $tgl_rab_new,
							'kontrak_terkini_id' => $data['id_kontrak_terkini']
						);
						$this->db->insert('simpro_tbl_total_pekerjaan',$data);

						$this->db->insert('simpro_tbl_total_rkp',$data);
						
						// $this->db->insert('simpro_tbl_rencana_kontrak_terkini',$data);
					}
				}

			break;
			case 'currentbudget':
				$sql_tgl_rab = "select tanggal_kendali
						from simpro_current_budget_item_tree
						where id_proyek=$proyek_id order by tanggal_kendali desc limit 1";

				$q_rab = $this->db->query($sql_tgl_rab);
				$tanggal = $q_rab->row();

				$tgl_rab_old = $tanggal->tanggal_kendali;

				$tgl_awal = $tgl_rab['tgl_awal'];
				$tgl_akhir = $tgl_rab['tgl_akhir'];

				$var_set = array(
					'tanggal_kendali' => $tgl_awal
				);
				$var_where = array(
					'id_proyek' =>  $proyek_id,
					'tanggal_kendali' => $tgl_rab_old
				);

				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_analisa_apek',$var_set);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_analisa_asat',$var_set);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_analisa_daftar',$var_set);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_analisa_item_apek',$var_set);
				$this->db->where($var_where);
				$this->db->update('simpro_current_budget_item_tree',$var_set);

				$sql_copy_current_budget_analisa_apek = "
				 insert into simpro_current_budget_analisa_apek
				 (
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_analisa,
				 koefisien,
				 harga,
				 id_proyek,
				 parent_kode_analisa,
				 parent_id_analisa,
				'$tgl_akhir'
				 from simpro_current_budget_analisa_apek
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_awal'";
				$sql_copy_current_budget_analisa_asat = "
				 insert into simpro_current_budget_analisa_asat
				 (
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				 tanggal_kendali
				 )
				 select
				 id_data_analisa,
				 kode_material,
				 id_detail_material,
				 koefisien,
				 harga,
				 kode_analisa,
				 id_proyek,
				 keterangan,
				 kode_rap,
				'$tgl_akhir'
				 from
				 simpro_current_budget_analisa_asat
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_awal'";
				$sql_copy_current_budget_analisa_daftar = "
				 insert into simpro_current_budget_analisa_daftar
				 (
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				 tanggal_kendali
				 )
				 select
				 kode_analisa,
				 id_kat_analisa,
				 nama_item,
				 id_satuan,
				 id_proyek,
				'$tgl_akhir'
				 from 
				 simpro_current_budget_analisa_daftar
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_awal'";
				$sql_copy_current_budget_analisa_item_apek = "
				 insert into simpro_current_budget_analisa_item_apek
				 (
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 current_budget_item_tree,
				 kode_tree,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_data_analisa,
				 kode_analisa,
				 harga,
				 current_budget_item_tree,
				 kode_tree,
				'$tgl_akhir'
				 from
				 simpro_current_budget_analisa_item_apek
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_awal'";
				$sql_copy_current_budget_analisa_item_tree = "
				 insert into simpro_current_budget_item_tree
				 (
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
				 tree_parent_kode,
				 tanggal_kendali
				 )
				 select
				 id_proyek,
				 id_satuan,
				 kode_tree,
				 tree_item,
				 tree_satuan,
				 tree_parent_id,
				 volume,
							case when right(tree_parent_kode,1) = '.'
							then left(tree_parent_kode,(length(tree_parent_kode)-1))
							else tree_parent_kode
							end,
				'$tgl_akhir'
				 from
				 simpro_current_budget_item_tree
				 where id_proyek = $proyek_id
				 and tanggal_kendali = '$tgl_awal'";

				$this->db->query($sql_copy_current_budget_analisa_item_tree);
				$this->db->query($sql_copy_current_budget_analisa_apek);
				$this->db->query($sql_copy_current_budget_analisa_asat);
				$this->db->query($sql_copy_current_budget_analisa_daftar);
				$this->db->query($sql_copy_current_budget_analisa_item_apek);

			break;
			case 'analisa_to_an_ctg':
				$copy_proyek = $tgl_rab['copy_proyek'];
				$copy_tgl_rab = $tgl_rab['copy_tgl_rab'];
				$copy_kode = $tgl_rab['copy_kode'];
				$tgl_rab_new = $tgl_rab['tgl_rab'];
				$kode = $tgl_rab['kode'];

				$sql_select_data = "select
								detail_material_id,
								komposisi_volume_kendali,
								komposisi_harga_satuan_kendali,
								komposisi_total_kendali,
								komposisi_koefisien_kendali,
								komposisi_volume_total_kendali,
								kode_komposisi_kendali,
								keterangan,
								kode_rap,
								detail_material_kode
								from
								simpro_tbl_komposisi_togo
								where tahap_kode_kendali = '$copy_kode'
								and tahap_tanggal_kendali = '$copy_tgl_rab'
								and proyek_id = '$copy_proyek'";

				$q_select_data = $this->db->query($sql_select_data);

				if ($q_select_data->result()) {
					foreach ($q_select_data->result() as $row) {
						$detail_material_id = $row->detail_material_id;
						$komposisi_volume_kendali = $row->komposisi_volume_kendali;
						$komposisi_harga_satuan_kendali = $row->komposisi_harga_satuan_kendali;
						$komposisi_total_kendali = $row->komposisi_total_kendali;
						$komposisi_koefisien_kendali = $row->komposisi_koefisien_kendali;
						$komposisi_volume_total_kendali = $row->komposisi_volume_total_kendali;
						$kode_komposisi_kendali = $row->kode_komposisi_kendali;
						$keterangan = $row->keterangan;
						$kode_rap = $row->kode_rap;
						$detail_material_kode = $row->detail_material_kode;

						$sql_check_data = "select 
						* 
						from simpro_tbl_komposisi_togo
						where proyek_id = $proyek_id
						and tahap_tanggal_kendali = '$tgl_rab_new'
						and tahap_kode_kendali = '$kode'
						and detail_material_id = $detail_material_id";

						$q_check_data = $this->db->query($sql_check_data);

						if ($q_check_data->result()) {
							// echo "Ada";
							foreach ($q_check_data->result() as $row_check) {								
								$var_isi = array(
									'komposisi_volume_kendali' => $komposisi_volume_kendali + $row_check->komposisi_volume_kendali, 
									'komposisi_koefisien_kendali' => $komposisi_koefisien_kendali + $row_check->komposisi_koefisien_kendali 
								);
								$var_where = array( 
									'proyek_id' => $proyek_id,
									'tahap_tanggal_kendali' => $tgl_rab_new,
									'tahap_kode_kendali' => $kode,
									'detail_material_id' => $detail_material_id
								);
								$this->db->where($var_where);
								$this->db->update('simpro_tbl_komposisi_togo',$var_isi);
							}
						} else {
							// echo "kosong";
							$sql_insert = "insert into simpro_tbl_komposisi_togo
								(
								proyek_id,
								detail_material_id,
								tahap_kode_kendali,
								komposisi_volume_kendali,
								komposisi_harga_satuan_kendali,
								komposisi_total_kendali,
								komposisi_koefisien_kendali,
								tahap_tanggal_kendali,
								user_update,
								tgl_update,
								ip_update,
								divisi_id,
								waktu_update,
								komposisi_volume_total_kendali,
								kode_komposisi_kendali,
								keterangan,
								kode_rap,
								detail_material_kode
								) values (
								$proyek_id,
								$detail_material_id,
								'$kode',
								$komposisi_volume_kendali,
								$komposisi_harga_satuan_kendali,
								$komposisi_total_kendali,
								$komposisi_koefisien_kendali,
								'$tgl_rab_new',
								$user_update,
								'$tgl_update',
								'$ip_update',
								$divisi_id,
								'$waktu_update',
								$komposisi_volume_total_kendali,
								'$kode_komposisi_kendali',
								'$keterangan',
								'$kode_rap',
								$detail_material_kode)";
							$this->db->query($sql_insert);
						}
					}
				}

				// $sql_copy_ctg_komposisi = "insert into simpro_tbl_komposisi_togo
				// 				(
				// 				proyek_id,
				// 				detail_material_id,
				// 				tahap_kode_kendali,
				// 				komposisi_volume_kendali,
				// 				komposisi_harga_satuan_kendali,
				// 				komposisi_total_kendali,
				// 				komposisi_koefisien_kendali,
				// 				tahap_tanggal_kendali,
				// 				user_update,
				// 				tgl_update,
				// 				ip_update,
				// 				divisi_id,
				// 				waktu_update,
				// 				komposisi_volume_total_kendali,
				// 				kode_komposisi_kendali,
				// 				keterangan,
				// 				kode_rap,
				// 				detail_material_kode
				// 				)
				// 				select
				// 				$proyek_id,
				// 				detail_material_id,
				// 				'$kode',
				// 				komposisi_volume_kendali,
				// 				komposisi_harga_satuan_kendali,
				// 				komposisi_total_kendali,
				// 				komposisi_koefisien_kendali,
				// 				'$tgl_rab_new',
				// 				$user_update,
				// 				'$tgl_update',
				// 				'$ip_update',
				// 				$divisi_id,
				// 				'$waktu_update',
				// 				komposisi_volume_total_kendali,
				// 				kode_komposisi_kendali,
				// 				keterangan,
				// 				kode_rap,
				// 				detail_material_kode
				// 				from
				// 				simpro_tbl_komposisi_togo
				// 				where tahap_kode_kendali = '$copy_kode'
				// 				and tahap_tanggal_kendali = '$copy_tgl_rab'
				// 				and proyek_id = '$copy_proyek'
				// 				";

				// $this->db->query($sql_copy_ctg_komposisi);
			break;
			case 'analisa_to_an_cb':
				$copy_proyek = $tgl_rab['copy_proyek'];
				$copy_tgl_rab = $tgl_rab['copy_tgl_rab'];
				$copy_kode = $tgl_rab['copy_kode'];
				$tgl_rab_new = $tgl_rab['tgl_rab'];
				$kode = $tgl_rab['kode'];

				$sql_select_data = "select
								detail_material_id,
								komposisi_volume_kendali,
								komposisi_harga_satuan_kendali,
								komposisi_total_kendali,
								komposisi_koefisien_kendali,
								komposisi_volume_total_kendali,
								kode_komposisi_kendali,
								keterangan,
								kode_rap,
								detail_material_kode
								from
								simpro_tbl_komposisi_budget
								where tahap_kode_kendali = '$copy_kode'
								and tahap_tanggal_kendali = '$copy_tgl_rab'
								and proyek_id = '$copy_proyek'";

				$q_select_data = $this->db->query($sql_select_data);

				if ($q_select_data->result()) {
					foreach ($q_select_data->result() as $row) {
						$detail_material_id = $row->detail_material_id;
						$komposisi_volume_kendali = $row->komposisi_volume_kendali;
						$komposisi_harga_satuan_kendali = $row->komposisi_harga_satuan_kendali;
						$komposisi_total_kendali = $row->komposisi_total_kendali;
						$komposisi_koefisien_kendali = $row->komposisi_koefisien_kendali;
						$komposisi_volume_total_kendali = $row->komposisi_volume_total_kendali;
						$kode_komposisi_kendali = $row->kode_komposisi_kendali;
						$keterangan = $row->keterangan;
						$kode_rap = $row->kode_rap;
						$detail_material_kode = $row->detail_material_kode;

						$sql_check_data = "select 
						* 
						from simpro_tbl_komposisi_budget
						where proyek_id = $proyek_id
						and tahap_tanggal_kendali = '$tgl_rab_new'
						and tahap_kode_kendali = '$kode'
						and detail_material_id = $detail_material_id";

						$q_check_data = $this->db->query($sql_check_data);

						if ($q_check_data->result()) {
							// echo "Ada";
							foreach ($q_check_data->result() as $row_check) {								
								$var_isi = array(
									'komposisi_volume_kendali' => $komposisi_volume_kendali + $row_check->komposisi_volume_kendali, 
									'komposisi_koefisien_kendali' => $komposisi_koefisien_kendali + $row_check->komposisi_koefisien_kendali 
								);
								$var_where = array( 
									'proyek_id' => $proyek_id,
									'tahap_tanggal_kendali' => $tgl_rab_new,
									'tahap_kode_kendali' => $kode,
									'detail_material_id' => $detail_material_id
								);
								$this->db->where($var_where);
								$this->db->update('simpro_tbl_komposisi_budget',$var_isi);
							}
						} else {
							// echo "kosong";
							$sql_insert = "insert into simpro_tbl_komposisi_budget
								(
								proyek_id,
								detail_material_id,
								tahap_kode_kendali,
								komposisi_volume_kendali,
								komposisi_harga_satuan_kendali,
								komposisi_total_kendali,
								komposisi_koefisien_kendali,
								tahap_tanggal_kendali,
								user_update,
								tgl_update,
								ip_update,
								divisi_id,
								waktu_update,
								komposisi_volume_total_kendali,
								kode_komposisi_kendali,
								keterangan,
								kode_rap,
								detail_material_kode
								) values (
								$proyek_id,
								$detail_material_id,
								'$kode',
								$komposisi_volume_kendali,
								$komposisi_harga_satuan_kendali,
								$komposisi_total_kendali,
								$komposisi_koefisien_kendali,
								'$tgl_rab_new',
								$user_update,
								'$tgl_update',
								'$ip_update',
								$divisi_id,
								'$waktu_update',
								$komposisi_volume_total_kendali,
								'$kode_komposisi_kendali',
								'$keterangan',
								'$kode_rap',
								$detail_material_kode)";
							$this->db->query($sql_insert);
						}
					}
				}
			break;
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else
		{
		    $this->db->trans_commit();
		}
	}

	function cek($info,$proyek_id,$tgl_rab)
	{
		switch ($info) {
			case 'proyek':
				$sql = "select tahap_tanggal_kendali from simpro_tbl_kontrak_terkini where proyek_id=$proyek_id";
			break;
			case 'proyek_rab':
				$sql = "select tahap_tanggal_kendali from simpro_tbl_kontrak_terkini where proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab'";
			break;
		}
		
		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = 'ada';
		} else {
			$dat = '';
		}

		return $dat;
	}

	function get_value($info,$proyek_id)
	{
		switch ($info) {
			case 'approve_terakhir':
				$sql = "select tgl_akhir as value from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id group by tgl_akhir order by tgl_akhir desc limit 1";
				$q = $this->db->query($sql);

				if ($q->result()) {
					$dat = $q->result_object();
				} else {
					$data['value']="";
					$dat = $data;
				}
			break;
		}

		return '{"data":'.json_encode($dat).'}'; 
	}

	function get_last_tgl_kontrak_kini($proyek_id)
	{
		$sql = "select tgl_akhir, (tgl_akhir - interval '1 month')::date as value
				from simpro_tbl_kontrak_terkini
				where proyek_id = $proyek_id
				group by tgl_akhir
				order by tgl_akhir desc limit 1";
		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$data['tgl_akhir'] = "";
			$data['value'] = "";
			$dat[] = $data;
		}
		
    	// }
		return '{"data":'.json_encode($dat).'}';
	}

	function get_status_tgl_cb($proyek_id)
	{
		$sql = "select
				CASE WHEN 
				(a.tanggal_kendali) = 
				(CASE WHEN 
				(SELECT
				count(*) as jml_data
				FROM
				(SELECT
				distinct(tanggal_kendali)
				FROM
				simpro_current_budget_item_tree where id_proyek = a.id_proyek
				) as q_tgl) = 1
				THEN 
				(SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = a.id_proyek)
				ELSE 
				(SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
				WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = a.id_proyek
				ORDER BY tanggal_kendali desc limit 1)::date
				END)
				THEN 'open'
				ELSE 'close'
				END as detail
				from 
				simpro_current_budget_item_tree a
				left join simpro_tbl_kontrak_terkini d on d.tgl_akhir = a.tanggal_kendali and d.proyek_id = a.id_proyek
				WHERE a.id_proyek = $proyek_id
				group by a.tanggal_kendali, a.id_proyek
				order by a.tanggal_kendali desc limit 1";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$data['value'] = "";
			$dat[] = $data;
		}
		
    	// }
		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_kk($page,$proyek_id,$tgl_rab)
	{
		switch ($page) {
			case 'kk':
				$id_info = 'id_kontrak_terkini';
				$where_tgl_info = 'tgl_akhir';
				$tbl_info = 'simpro_tbl_kontrak_terkini';
			break;
			case 'rkk':
				$id_info = 'id_rencana_kontrak_terkini';
				$where_tgl_info = 'tahap_tanggal_kendali';
				$tbl_info = 'simpro_tbl_rencana_kontrak_terkini';
			break;
		}
		$sql = "with j as (SELECT 
				a.$id_info,
				b.rab_tahap_kode_kendali,
				b.rab_tahap_nama_kendali,
				b.rab_tahap_satuan_kendali,
				b.rab_tahap_volume_kendali,
				b.rab_tahap_harga_satuan_kendali,
				b.jml_rab,
				a.tahap_kode_kendali,
				a.tahap_nama_kendali,
				a.tahap_satuan_kendali,
				CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END,
				CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END,
				(
				(CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END) * 
				(CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END)
				) as jml_kontrak_kini,
				CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END,
				(
				(CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END) *
				((CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END))
				) as jml_tambah,
				CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang
				END,
				(
				(CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang
				END) *
				(CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END)
				) as jml_kurang,
				CASE WHEN a.volume_eskalasi is null
				THEN 0
				ELSE a.volume_eskalasi
				END,
				CASE WHEN a.harga_satuan_eskalasi is null
				THEN 0
				ELSE a.harga_satuan_eskalasi
				END,
				(
				(CASE WHEN a.volume_eskalasi is null
				THEN 0
				ELSE a.volume_eskalasi
				END) * 
				(CASE WHEN a.harga_satuan_eskalasi is null
				THEN 0
				ELSE a.harga_satuan_eskalasi
				END)
				) as jml_eskalasi,
				(
				(CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END) +
				(CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END) -
				(CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang 
				END)
				) as vol_total,
				(
				(
				(CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END) +
				(CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END) -
				(CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang 
				END)
				) * 
				(CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END)
				) as jml_total
				FROM
				$tbl_info a
				LEFT JOIN (
					select
							tahap_kode_kendali as rab_tahap_kode_kendali,
							tahap_nama_kendali as rab_tahap_nama_kendali,
							(select satuan_id from simpro_tbl_satuan where lower(satuan_nama) = lower(tahap_satuan_kendali)) as rab_tahap_satuan_kendali,
							coalesce(tahap_volume_kendali,0) as rab_tahap_volume_kendali,
							coalesce(tahap_harga_satuan_kendali,0) as rab_tahap_harga_satuan_kendali,
							(coalesce(tahap_volume_kendali,0) * coalesce(tahap_harga_satuan_kendali,0)) as jml_rab,
							proyek_id,
							tahap_kode_induk_kendali
							from 
							simpro_tbl_input_kontrak
							where proyek_id = $proyek_id
					) b 
				on a.proyek_id = b.proyek_id 
				and a.tahap_kode_kendali = b.rab_tahap_kode_kendali
				where a.proyek_id = $proyek_id and a.$where_tgl_info = '$tgl_rab'
				ORDER BY a.tahap_kode_kendali asc)

				SELECT 
				a.id_kontrak_terkini,
				b.rab_tahap_kode_kendali,
				b.rab_tahap_nama_kendali,
				b.rab_tahap_satuan_kendali,
				b.rab_tahap_volume_kendali,
				case when b.rab_tahap_volume_kendali = 0 then
				0
				else
				(
				(
				select
				sum(jml_rab)
				from
				j
				where
				left(j.tahap_kode_kendali,length(b.rab_tahap_kode_kendali)) = b.rab_tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(b.rab_tahap_kode_kendali))
				)/
				(b.rab_tahap_volume_kendali)
				)
				end as rab_tahap_harga_satuan_kendali,
				(
				select
				sum(jml_rab)
				from
				j
				where
				left(j.tahap_kode_kendali,length(b.rab_tahap_kode_kendali)) = b.rab_tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(b.rab_tahap_kode_kendali))
				) as jml_rab,
				a.tahap_kode_kendali,
				a.tahap_nama_kendali,
				a.tahap_satuan_kendali,
				CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END,
				case when a.tahap_volume_kendali = 0 then
				0
				else
				(
				(
				select
				sum(jml_kontrak_kini)
				from
				j
				where
				left(j.tahap_kode_kendali,length(a.tahap_kode_kendali)) = a.tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(a.tahap_kode_kendali))
				)/
				(CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END)
				)
				end as tahap_harga_satuan_kendali,
				(
				select
				sum(jml_kontrak_kini)
				from
				j
				where
				left(j.tahap_kode_kendali,length(a.tahap_kode_kendali)) = a.tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(a.tahap_kode_kendali))
				) as jml_kontrak_kini,
				CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END,
				(
				(CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END) *
				((CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END))
				) as jml_tambah,
				CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang
				END,
				(
				(CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang
				END) *
				(CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END)
				) as jml_kurang,
				CASE WHEN a.volume_eskalasi is null
				THEN 0
				ELSE a.volume_eskalasi
				END,
				CASE WHEN a.harga_satuan_eskalasi is null
				THEN 0
				ELSE a.harga_satuan_eskalasi
				END,
				(
				(CASE WHEN a.volume_eskalasi is null
				THEN 0
				ELSE a.volume_eskalasi
				END) * 
				(CASE WHEN a.harga_satuan_eskalasi is null
				THEN 0
				ELSE a.harga_satuan_eskalasi
				END)
				) as jml_eskalasi,
				(
				(CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END) +
				(CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END) -
				(CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang 
				END)
				) as vol_total,
				(
				(
				(CASE WHEN a.tahap_volume_kendali is null
				THEN 0
				ELSE a.tahap_volume_kendali
				END) +
				(CASE WHEN a.tahap_volume_kendali_new is null
				THEN 0
				ELSE a.tahap_volume_kendali_new
				END) -
				(CASE WHEN a.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE a.tahap_volume_kendali_kurang 
				END)
				) * 
				(CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END)
				) as jml_total,
				case when (select 
				count(n.id_kontrak_terkini) 
				from simpro_tbl_kontrak_terkini n
				where left(n.tahap_kode_kendali,length(a.tahap_kode_kendali)) = a.tahap_kode_kendali
				and n.proyek_id = $proyek_id
				and n.tahap_tanggal_kendali = '$tgl_rab') > 1 then
				0
				else
				1
				end as anak
				FROM
				simpro_tbl_kontrak_terkini a
				LEFT JOIN (
					select
							tahap_kode_kendali as rab_tahap_kode_kendali,
							tahap_nama_kendali as rab_tahap_nama_kendali,
							(select satuan_id from simpro_tbl_satuan where lower(satuan_nama) = lower(tahap_satuan_kendali)) as rab_tahap_satuan_kendali,
							coalesce(tahap_volume_kendali,0) as rab_tahap_volume_kendali,
							coalesce(tahap_harga_satuan_kendali,0) as rab_tahap_harga_satuan_kendali,
							(coalesce(tahap_volume_kendali,0) * coalesce(tahap_harga_satuan_kendali,0)) as jml_rab,
							proyek_id,
							tahap_kode_induk_kendali
							from 
							simpro_tbl_input_kontrak
							where proyek_id = $proyek_id
					) b 
				on a.proyek_id = b.proyek_id 
				and a.tahap_kode_kendali = b.rab_tahap_kode_kendali
				where a.proyek_id = $proyek_id and a.tgl_akhir = '$tgl_rab'
				ORDER BY a.tahap_kode_kendali asc";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';

	}

	function reset($info,$id)
	{
		switch ($info) {
			case 'item_kontrak_terkini':
				$data = array(
					'tahap_volume_kendali_new' => 0, 
					'tahap_volume_kendali_kurang' => 0, 
					'volume_eskalasi' => 0, 
					'harga_satuan_eskalasi' => 0, 
				);
				$this->db->where('id_kontrak_terkini',$id);
				$this->db->update('simpro_tbl_kontrak_terkini',$data);
			break;
			case 'item_rencana_kontrak_terkini':
				$data = array(
					'tahap_volume_kendali_new' => 0, 
					'tahap_volume_kendali_kurang' => 0, 
					'volume_eskalasi' => 0, 
					'harga_satuan_eskalasi' => 0, 
				);
				$this->db->where('id_rencana_kontrak_terkini',$id);
				$this->db->update('simpro_tbl_rencana_kontrak_terkini',$data);
			break;
		}
	}

	function get_data_total_pekerjaan($proyek_id,$tgl_rab)
	{
		$sql = "with j as (SELECT
				a.id_tahap_pekerjaan,
				b.tahap_kode_kendali,
				b.tahap_nama_kendali,
				b.tahap_satuan_kendali,
				(
				(CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)
				) as vol_kk,
				CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_lpf_kini,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali < b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali < b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as jlm_sd_bln_lalu,
				a.tahap_diakui_bobot,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as jlm_sd_bln_ini,
				CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END,
				(
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_tagihan,
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) -
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END)
				) as vol_bruto,
				(
				((CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) -
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bruto,
				CASE WHEN a.tagihan_cair is null
				THEN 0
				ELSE a.tagihan_cair
				END,
				(
				(CASE WHEN a.tagihan_cair is null
				THEN 0
				ELSE a.tagihan_cair
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_cair,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(CASE WHEN a.tahap_diakui_bobot is null
				THEN 0
				ELSE a.tahap_diakui_bobot
				END)
				) as vol_sisa_pekerjaan,
				(
				(((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(CASE WHEN a.tahap_diakui_bobot is null
				THEN 0
				ELSE a.tahap_diakui_bobot
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_sisa_pekerjaan,
				CASE WHEN a.tagihan_rencana_piutang is null
				THEN 0
				ELSE a.tagihan_rencana_piutang
				END
				FROM
				simpro_tbl_total_pekerjaan a 
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab' 
				ORDER BY b.tahap_kode_kendali)
				
				SELECT
				a.id_tahap_pekerjaan,
				b.tahap_kode_kendali,
				b.tahap_nama_kendali,
				b.tahap_satuan_kendali,
				(
				select
				sum(jml_lpf_kini)
				from
				j
				where
				left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))
				) as jml_lpf_kini,
				(
				(CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)
				) as vol_kk,
				case when ((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) = 0 then
				0
				else
				(
				(select
				sum(jml_lpf_kini)
				from
				j
				where
				left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))) /
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END))
				) end as tahap_harga_satuan_kendali,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali < b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali < b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as jlm_sd_bln_lalu,
				a.tahap_diakui_bobot,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as jlm_sd_bln_ini,
				CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END,
				(
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_tagihan,
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) -
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END)
				) as vol_bruto,
				(
				((CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) -
				(CASE WHEN a.vol_total_tagihan is null
				THEN 0
				ELSE a.vol_total_tagihan
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bruto,
				CASE WHEN a.tagihan_cair is null
				THEN 0
				ELSE a.tagihan_cair
				END,
				(
				(CASE WHEN a.tagihan_cair is null
				THEN 0
				ELSE a.tagihan_cair
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_cair,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(CASE WHEN a.tahap_diakui_bobot is null
				THEN 0
				ELSE a.tahap_diakui_bobot
				END)
				) as vol_sisa_pekerjaan,
				(
				(((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(CASE WHEN a.tahap_diakui_bobot is null
				THEN 0
				ELSE a.tahap_diakui_bobot
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_sisa_pekerjaan,
				CASE WHEN a.tagihan_rencana_piutang is null
				THEN 0
				ELSE a.tagihan_rencana_piutang
				END,
				case when (select 
				count(n.id_kontrak_terkini) 
				from simpro_tbl_total_pekerjaan m
				join simpro_tbl_kontrak_terkini n
				on m.kontrak_terkini_id = n.id_kontrak_terkini
				where left(n.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
				and m.proyek_id = $proyek_id
				and m.tahap_tanggal_kendali = '$tgl_rab') > 1 then
				0
				else
				1
				end as anak
				FROM
				simpro_tbl_total_pekerjaan a 
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab' 
				ORDER BY b.tahap_kode_kendali
				";
		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_rkp($proyek_id,$tgl_rab)
	{
		$sql = "with j as (SELECT
				a.total_rkp_id,
				b.tahap_kode_kendali,
				b.tahap_nama_kendali,
				b.tahap_satuan_kendali,
				(
				(CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)
				) as vol_kk,
				CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_rkp_kini,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as vol_sd_bln_ini,
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_sd_bln_ini,
				CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END,
				(
				(CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln1,
				CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END,
				(
				(CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln2,
				CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END,
				(
				(CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln3,
				CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END,
				(
				(CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln4,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END)+
				(CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END)+
				(CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END)+
				(CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END)+
				(CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END)
				)
				) as deviasi
				FROM
				simpro_tbl_total_rkp a
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab'
				ORDER BY b.tahap_kode_kendali)

				SELECT
				a.total_rkp_id,
				b.tahap_kode_kendali,
				b.tahap_nama_kendali,
				b.tahap_satuan_kendali,
				(
				(CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)
				) as vol_kk,
				(
				case when (
				select
				sum(jml_rkp_kini)
				from
				j
				where
				left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))
				) = 0
				then 0
				else
				(
				select
				sum(jml_rkp_kini)
				from
				j
				where
				left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))
				)/
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) end
				) as tahap_harga_satuan_kendali,
				(
				select
				sum(jml_rkp_kini)
				from
				j
				where
				left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
				group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))
				) as jml_rkp_kini,
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) as vol_sd_bln_ini,
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_sd_bln_ini,
				CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END,
				(
				(CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln1,
				CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END,
				(
				(CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln2,
				CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END,
				(
				(CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln3,
				CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END,
				(
				(CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln4,
				(
				((CASE WHEN b.tahap_volume_kendali is null
				THEN 0
				ELSE b.tahap_volume_kendali
				END) +
				(CASE WHEN b.tahap_volume_kendali_new is null
				THEN 0
				ELSE b.tahap_volume_kendali_new
				END) -
				(CASE WHEN b.tahap_volume_kendali_kurang is null
				THEN 0
				ELSE b.tahap_volume_kendali_kurang
				END)) -
				(
				(CASE WHEN (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali) is null
				THEN 0
				ELSE (SELECT
				SUM(
				CASE WHEN d.tahap_diakui_bobot is null
				THEN 0
				ELSE d.tahap_diakui_bobot
				END)
				FROM
				simpro_tbl_total_pekerjaan d
				JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
				WHERE d.tahap_tanggal_kendali <= b.tgl_akhir AND e.tahap_kode_kendali=b.tahap_kode_kendali AND d.proyek_id = b.proyek_id
				GROUP BY e.tahap_kode_kendali)
				END)+
				(CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END)+
				(CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END)+
				(CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END)+
				(CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END)
				)
				) as deviasi,
				case when (select 
				count(n.id_kontrak_terkini) 
				from simpro_tbl_total_pekerjaan m
				join simpro_tbl_kontrak_terkini n
				on m.kontrak_terkini_id = n.id_kontrak_terkini
				where left(n.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
				and m.proyek_id = $proyek_id
				and m.tahap_tanggal_kendali = '$tgl_rab') > 1 then
				0
				else
				1
				end as anak
				FROM
				simpro_tbl_total_rkp a
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab'
				ORDER BY b.tahap_kode_kendali";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_combo($info,$page,$data)
	{
		switch ($info) {
			case 'copy_divisi':
				$tbl_info = 'simpro_tbl_divisi';
				$where_info = 'order by divisi_name';
				$text_info = 'divisi_name';
				$value_info = 'divisi_id';
			break;
			case 'copy_proyek':
				$tbl_info = 'simpro_tbl_proyek';
				$where_info = 'where divisi_kode = '.$data['kode'];
				$text_info = 'proyek';
				$value_info = 'proyek_id';
			break;
			case 'copy_tgl':				
				switch ($page) {
					case 'ctg':
						$tbl_page_info = 'simpro_tbl_cost_togo';				
					break;
					case 'cb':
						$tbl_page_info = 'simpro_tbl_current_budget';	
					break;
				}
				$tbl_info = $tbl_page_info;
				$where_info = 'where proyek_id = '.$data['kode'].'group by tahap_tanggal_kendali order by tahap_tanggal_kendali';
				$text_info = 'tahap_tanggal_kendali';
				$value_info = 'tahap_tanggal_kendali';
			break;
			case 'copy_analisa':
				switch ($page) {
					case 'ctg':
						$tbl_page_info = 'simpro_tbl_cost_togo';
						$tbl_page_info_komposisi = 'simpro_tbl_komposisi_togo';			
					break;
					case 'cb':
						$tbl_page_info = 'simpro_tbl_current_budget';
						$tbl_page_info_komposisi = 'simpro_tbl_komposisi_budget';	
					break;
				}
				$tbl_info = $tbl_page_info.' a join '.$tbl_page_info_komposisi.' b on a.tahap_kode_kendali = b.tahap_kode_kendali and a.proyek_id = b.proyek_id and a.tahap_tanggal_kendali = b.tahap_tanggal_kendali';
				$where_info = "where a.proyek_id = ".$data['proyek']." and a.tahap_tanggal_kendali = '".$data['tgl']."' group by value, text order by a.tahap_kode_kendali";
				$text_info = "'(' || a.tahap_kode_kendali || ') => ' || a.tahap_nama_kendali";
				$value_info = 'a.tahap_kode_kendali';
			break;
		}

		$sql = "select
				$value_info as value,
				$text_info as text
				from
				$tbl_info
				$where_info
				";

		// return var_dump($data);
		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$data['value']='';
			$data['text']='';
			$dat = $data;
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_isi_uraian_mos($id)
	{
		$sql = "select
				b.detail_material_satuan as satuan,
				a.komposisi_harga_satuan_kendali as harga
				from
				simpro_tbl_komposisi_kendali a
				join simpro_tbl_detail_material b on a.detail_material_id = b.detail_material_id
				where a.detail_material_id = $id";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$data['satuan']='';
			$data['harga']='';
			$dat = $data;
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_cashflow($proyek_id,$tgl_rab)
	{

		$sql_rab = "select sum(volume * harga) as total_rab, $proyek_id as proyek_id from (select
							tahap_kode_kendali as rab_tahap_kode_kendali,
							tahap_nama_kendali as rab_tahap_nama_kendali,
							(select satuan_id from simpro_tbl_satuan where lower(satuan_nama) = lower(tahap_satuan_kendali)) as rab_tahap_satuan_kendali,
							coalesce(tahap_volume_kendali,0) as volume,
							coalesce(tahap_harga_satuan_kendali,0) as harga,
							(coalesce(tahap_volume_kendali,0) * coalesce(tahap_harga_satuan_kendali,0)) as jml_rab,
							proyek_id,
							tahap_kode_induk_kendali
							from 
							simpro_tbl_input_kontrak
							where proyek_id = $proyek_id)
						rab";

		$sql_realisasi = "SELECT
					SUM(
					(CASE WHEN d.tahap_diakui_bobot is null
					THEN 0
					ELSE d.tahap_diakui_bobot
					END)*
					(CASE WHEN e.tahap_harga_satuan_kendali is null
					THEN 0
					ELSE e.tahap_harga_satuan_kendali
					END)) as total_bln_kini
					FROM
					simpro_tbl_total_pekerjaan d
					JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
					WHERE d.tahap_tanggal_kendali <= '$tgl_rab' AND d.proyek_id = $proyek_id
					GROUP BY d.proyek_id";

		$sql_proyeksi = "SELECT
				sum(
				(CASE WHEN a.tahap_volume_bln1 is null
				THEN 0
				ELSE a.tahap_volume_bln1
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln1,
				sum(
				(CASE WHEN a.tahap_volume_bln2 is null
				THEN 0
				ELSE a.tahap_volume_bln2
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln2,
				sum(
				(CASE WHEN a.tahap_volume_bln3 is null
				THEN 0
				ELSE a.tahap_volume_bln3
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln3,
				sum(
				(CASE WHEN a.tahap_volume_bln4 is null
				THEN 0
				ELSE a.tahap_volume_bln4
				END) *
				(CASE WHEN b.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE b.tahap_harga_satuan_kendali
				END)
				) as jml_bln4
				FROM
				simpro_tbl_total_rkp a
				JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
				WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali <= '$tgl_rab'
				GROUP BY a.proyek_id";

		$sql_uang_muka = "select uang_muka from simpro_tbl_proyek where proyek_id='$proyek_id'"; 

		$sql_produksi = "WITH total_rab as(select sum(volume * harga) as total_rab, $proyek_id as proyek_id from (select
							tahap_kode_kendali as rab_tahap_kode_kendali,
							tahap_nama_kendali as rab_tahap_nama_kendali,
							(select satuan_id from simpro_tbl_satuan where lower(satuan_nama) = lower(tahap_satuan_kendali)) as rab_tahap_satuan_kendali,
							coalesce(tahap_volume_kendali,0) as volume,
							coalesce(tahap_harga_satuan_kendali,0) as harga,
							(coalesce(tahap_volume_kendali,0) * coalesce(tahap_harga_satuan_kendali,0)) as jml_rab,
							proyek_id,
							tahap_kode_induk_kendali
							from 
							simpro_tbl_input_kontrak
							where proyek_id = $proyek_id)
						rab
						),
						total_bln_lalu as (
						SELECT
						SUM(
						(CASE WHEN d.tahap_diakui_bobot is null
						THEN 0
						ELSE d.tahap_diakui_bobot
						END)*
						(CASE WHEN e.tahap_harga_satuan_kendali is null
						THEN 0
						ELSE e.tahap_harga_satuan_kendali
						END)) as total_bln_lalu,
						d.proyek_id
						FROM
						simpro_tbl_total_pekerjaan d
						JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
						WHERE d.tahap_tanggal_kendali < '$tgl_rab' AND d.proyek_id = $proyek_id
						GROUP BY d.proyek_id
						),
						total_bln_ini as (
						SELECT
						SUM(
						(CASE WHEN d.tahap_diakui_bobot is null
						THEN 0
						ELSE d.tahap_diakui_bobot
						END)*
						(CASE WHEN e.tahap_harga_satuan_kendali is null
						THEN 0
						ELSE e.tahap_harga_satuan_kendali
						END)) as total_bln_kini,
						d.proyek_id
						FROM
						simpro_tbl_total_pekerjaan d
						JOIN simpro_tbl_kontrak_terkini e on d.kontrak_terkini_id = e.id_kontrak_terkini
						WHERE d.tahap_tanggal_kendali <= '$tgl_rab' AND d.proyek_id = $proyek_id
						GROUP BY d.proyek_id
						),
						total_proyeksi as (
						SELECT
						sum(
						(CASE WHEN a.tahap_volume_bln1 is null
						THEN 0
						ELSE a.tahap_volume_bln1
						END) *
						(CASE WHEN b.tahap_harga_satuan_kendali is null
						THEN 0
						ELSE b.tahap_harga_satuan_kendali
						END)
						) as jml_bln1,
						sum(
						(CASE WHEN a.tahap_volume_bln2 is null
						THEN 0
						ELSE a.tahap_volume_bln2
						END) *
						(CASE WHEN b.tahap_harga_satuan_kendali is null
						THEN 0
						ELSE b.tahap_harga_satuan_kendali
						END)
						) as jml_bln2,
						sum(
						(CASE WHEN a.tahap_volume_bln3 is null
						THEN 0
						ELSE a.tahap_volume_bln3
						END) *
						(CASE WHEN b.tahap_harga_satuan_kendali is null
						THEN 0
						ELSE b.tahap_harga_satuan_kendali
						END)
						) as jml_bln3,
						sum(
						(CASE WHEN a.tahap_volume_bln4 is null
						THEN 0
						ELSE a.tahap_volume_bln4
						END) *
						(CASE WHEN b.tahap_harga_satuan_kendali is null
						THEN 0
						ELSE b.tahap_harga_satuan_kendali
						END)
						) as jml_bln4,
						a.proyek_id
						FROM
						simpro_tbl_total_rkp a
						JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
						WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali <= '$tgl_rab'
						GROUP BY a.proyek_id
						),
						uang_muka_proyek as (
						select uang_muka, proyek_id
						from simpro_tbl_proyek 
						where proyek_id=$proyek_id
						)
						SELECT
						CASE WHEN total_rab ISNULL
						THEN 0
						ELSE total_rab
						END :: INTEGER,
						CASE WHEN total_bln_lalu ISNULL
						THEN 0
						ELSE total_bln_lalu
						END :: INTEGER,
						CASE WHEN total_bln_kini ISNULL
						THEN 0
						ELSE total_bln_kini
						END :: INTEGER,
						CASE WHEN jml_bln1 ISNULL
						THEN 0
						ELSE jml_bln1
						END :: INTEGER,
						CASE WHEN jml_bln2 ISNULL
						THEN 0
						ELSE jml_bln2
						END :: INTEGER,
						CASE WHEN jml_bln3 ISNULL
						THEN 0
						ELSE jml_bln3
						END :: INTEGER,
						CASE WHEN jml_bln4 ISNULL
						THEN 0
						ELSE jml_bln4
						END :: INTEGER,
						CASE WHEN uang_muka ISNULL
						THEN 0
						ELSE uang_muka
						END :: INTEGER
						FROM total_rab a 
						FULL JOIN total_bln_lalu b on a.proyek_id = b.proyek_id
						FULL JOIN total_bln_ini c on a.proyek_id = c.proyek_id
						FULL JOIN total_proyeksi d on a.proyek_id = d.proyek_id
						FULL JOIN uang_muka_proyek e on a.proyek_id = e.proyek_id";

		$q_produksi = $this->db->query($sql_produksi);
		$row_produksi = $q_produksi->row();

		$total_rab = $row_produksi->total_rab;
		$total_bln_lalu = $row_produksi->total_bln_lalu;
		$total_bln_kini = $row_produksi->total_bln_kini;
		$total_bln_ini = $total_bln_kini - $total_bln_lalu;
		$proyeksi1 = $row_produksi->jml_bln1;
		$proyeksi2 = $row_produksi->jml_bln2;
		$proyeksi3 = $row_produksi->jml_bln3;
		$proyeksi4 = $row_produksi->jml_bln4;
		$jml_produksi = $total_bln_kini + $proyeksi1 + $proyeksi2 + $proyeksi3 + $proyeksi4;
		$sisa_produksi = $total_rab - $jml_produksi;

		$akumulasi1 = $total_bln_kini + $proyeksi1;
		$akumulasi2 = $total_bln_kini + $proyeksi1 + $proyeksi2;
		$akumulasi3 = $total_bln_kini + $proyeksi1 + $proyeksi2 + $proyeksi3;
		$akumulasi4 = $total_bln_kini + $proyeksi1 + $proyeksi2 + $proyeksi3 + $proyeksi4;

		if ($total_rab == 0) {
			$total_rab_persen = 0;
			$total_bln_lalu_persen = 0;
			$total_bln_ini_persen = 0;
			$total_bln_kini_persen = 0;
			$akumulasi1_persen = 0;
			$akumulasi2_persen = 0;
			$akumulasi3_persen = 0;
			$akumulasi4_persen = 0;
			$jml_produksi_persen = 0;
			$sisa_produksi_persen = 0;
			$retensi=0;
			$pph=0;
			$uang_muka =0;
		} else {			
			$total_rab_persen = $total_rab / $total_rab * 100;
			$total_bln_lalu_persen = $total_bln_lalu / $total_rab * 100;
			$total_bln_ini_persen = $total_bln_ini / $total_rab * 100;
			$total_bln_kini_persen = $total_bln_kini / $total_rab * 100;
			$akumulasi1_persen = $akumulasi1 / $total_rab * 100;
			$akumulasi2_persen = $akumulasi2 / $total_rab * 100;
			$akumulasi3_persen = $akumulasi3 / $total_rab * 100;
			$akumulasi4_persen = $akumulasi4 / $total_rab * 100;
			$jml_produksi_persen = $jml_produksi / $total_rab * 100;
			$sisa_produksi_persen = $sisa_produksi / $total_rab * 100;
			$retensi=round($total_rab * 5/100);
			$pph=round($total_rab * 3/100);
			$uang_muka = round($total_rab * $row_produksi->uang_muka/100);
		}

		for ($i=0; $i < 7; $i++) {
			switch ($i) {
				case 0:
					$kode_sub = '500';
				break;
				case 1:
					$kode_sub = '501';
				break;
				case 2:
					$kode_sub = '502';
				break;
				case 3:
					$kode_sub = '503';
				break;
				case 4:
					$kode_sub = '504';
				break;
				case 5:
					$kode_sub = '505';
				break;
				case 6:
					$kode_sub = '508';
				break;
			}
			$sql_pengeluaran = "SELECT
								a.proyek_id,
								left(b.detail_material_kode,3) as kode,
								sum(a.komposisi_volume_kendali * komposisi_harga_satuan_kendali) as total
								FROM
								(
					with get_analisa as (select
					b.kode_analisa,
					case when c.kode_analisa isnull
					then b.kode_analisa
					else c.kode_analisa
					end as analisa
					from
					simpro_rap_item_tree a
					join simpro_rap_analisa_item_apek b 
					on a.kode_tree = b.kode_tree and b.id_proyek = a.id_proyek
					join simpro_rap_analisa_apek c
					on b.kode_analisa = c.parent_kode_analisa and c.id_proyek = a.id_proyek
					where a.id_proyek = $proyek_id)
					select
					id_proyek as proyek_id,
					id_detail_material as detail_material_id,
					kode_material as detail_material_kode,
					detail_material_nama,
					detail_material_satuan,
					sum(koefisien) as komposisi_volume_kendali,
					harga as komposisi_harga_satuan_kendali,
					sum(subtotal) as subtotal,
					kode_rap from (select 
					e.id_proyek,
					e.id_detail_material,
					e.kode_material,
					f.detail_material_nama,
					f.detail_material_satuan,
					sum(e.koefisien) as koefisien,
					e.harga,
					sum(e.koefisien * e.harga) as subtotal,
					e.kode_rap
					from get_analisa d
					join simpro_rap_analisa_asat e
					on d.analisa = e.kode_analisa
					join simpro_tbl_detail_material f
					on e.kode_material = f.detail_material_kode
					group by
					e.id_proyek,
					e.id_detail_material,
					e.kode_material,
					f.detail_material_nama,
					f.detail_material_satuan,
					e.harga,
					e.kode_rap
					union all
					select
					c.id_proyek,
					c.id_detail_material,
					c.kode_material,
					d.detail_material_nama,
					d.detail_material_satuan,
					sum(c.koefisien) as koefisien,
					c.harga,
					sum(c.koefisien * c.harga) as subtotal,
					c.kode_rap
					from
					simpro_rap_item_tree a
					join simpro_rap_analisa_item_apek b 
					on a.kode_tree = b.kode_tree and b.id_proyek = a.id_proyek
					join simpro_rap_analisa_asat c
					on b.kode_analisa = c.kode_analisa and c.id_proyek = a.id_proyek
					join simpro_tbl_detail_material d
					on c.kode_material = d.detail_material_kode
					where a.id_proyek = $proyek_id
					group by
					c.id_proyek,
					c.id_detail_material,
					c.kode_material,
					d.detail_material_nama,
					d.detail_material_satuan,
					c.harga,
					c.kode_rap) detail
					group by
					id_proyek,
					id_detail_material,
					kode_material,
					detail_material_nama,
					detail_material_satuan,
					harga,
					kode_rap
					order by kode_rap
								) a
								JOIN simpro_tbl_detail_material b
								on a.detail_material_id = b.detail_material_id
								WHERE left(b.detail_material_kode,3) = '$kode_sub' and a.proyek_id = $proyek_id
								GROUP BY a.proyek_id, left(b.detail_material_kode,3)";

			$q_pengeluaran = $this->db->query($sql_pengeluaran);

			if ($q_pengeluaran->result()) {
				$row_pengeluaran = $q_pengeluaran->row();
				$data_p['kode'] = $kode_sub;
				$data_p['total'] = $row_pengeluaran->total;
			} else {				
				$data_p['kode'] = '';
				$data_p['total'] = 0;
			}

			$sql_pengeluaran_kini = "SELECT
								left (b.detail_material_kode,3),
								SUM(jumlah) as total
								FROM
								simpro_tbl_cashtodate a
								JOIN simpro_tbl_detail_material b
								on a.detail_material_id = b.detail_material_id
								WHERE left(b.detail_material_kode,3) = '$kode_sub' and a.proyek_id = $proyek_id and tanggal <= '$tgl_rab'
								GROUP BY a.proyek_id, left (b.detail_material_kode,3)";

			$sql_pengeluaran_lalu = "SELECT
								left (b.detail_material_kode,3),
								SUM(jumlah) as total
								FROM
								simpro_tbl_cashtodate a
								JOIN simpro_tbl_detail_material b
								on a.detail_material_id = b.detail_material_id
								WHERE left(b.detail_material_kode,3) = '$kode_sub' and a.proyek_id = $proyek_id and tanggal < '$tgl_rab'
								GROUP BY a.proyek_id, left (b.detail_material_kode,3)";

			$q_pengeluaran_kini = $this->db->query($sql_pengeluaran_kini);
			$q_pengeluaran_lalu = $this->db->query($sql_pengeluaran_lalu);

			if ($q_pengeluaran_kini->result()) {
				$row_pengeluaran_kini  = $q_pengeluaran_kini->row();
				$data_p['kode_kini'] = $kode_sub;
				$data_p['total_kini'] = $row_pengeluaran_kini->total;
			} else {				
				$data_p['kode_kini'] = '';
				$data_p['total_kini'] = 0;
			}

			if ($q_pengeluaran_lalu->result()) {
				$row_pengeluaran_lalu = $q_pengeluaran_lalu->row();
				$data_p['kode_lalu'] = $kode_sub;
				$data_p['total_lalu'] = $row_pengeluaran_lalu->total;
			} else {				
				$data_p['kode_lalu'] = '';
				$data_p['total_lalu'] = 0;
			}

			$data_p['total_ini'] = $data_p['total_kini'] - $data_p['total_lalu'];
			$data_pengeluaran[] = $data_p;
		}

		for ($i=1; $i <= 16; $i++) { 
			$sql_data_cashin = "SELECT
								cashin_id,
								ket_id,
								realisasi,
								rproyeksi1,
								rproyeksi2,
								rproyeksi3,
								rproyeksi4,
								rproyeksi5,
								curentbuget,
								sbp,
								spp
								FROM
								simpro_tbl_cashin
								where ket_id = $i and proyek_id = $proyek_id 
								and tahap_tanggal_kendali >= (select tahap_tanggal_kendali from simpro_tbl_cashin where tahap_tanggal_kendali <= '$tgl_rab' group by tahap_tanggal_kendali order by tahap_tanggal_kendali desc limit 1) 
								and tahap_tanggal_kendali <= '$tgl_rab'";

			$sql_data_cashin_lalu = "SELECT
								sum(realisasi) as cashin_lalu
								FROM
								simpro_tbl_cashin
								where ket_id = $i and proyek_id = $proyek_id and tahap_tanggal_kendali < '$tgl_rab'";

			$sql_data_cashin_kini = "SELECT
								sum(realisasi) as cashin_kini
								FROM
								simpro_tbl_cashin
								where ket_id = $i and proyek_id = $proyek_id and tahap_tanggal_kendali <= '$tgl_rab'";

			$q_data_cashin = $this->db->query($sql_data_cashin);
			$q_data_cashin_lalu = $this->db->query($sql_data_cashin_lalu);
			$q_data_cashin_kini = $this->db->query($sql_data_cashin_kini);


			if ($q_data_cashin->result()) {
				$row_data_cashin = $q_data_cashin->row();

				$data_c['cashin_id'] = $row_data_cashin->cashin_id;
				$data_c['ket_id'] = $row_data_cashin->ket_id;
				$data_c['realisasi'] = $row_data_cashin->realisasi;
				$data_c['rproyeksi1'] = $row_data_cashin->rproyeksi1;
				$data_c['rproyeksi2'] = $row_data_cashin->rproyeksi2;
				$data_c['rproyeksi3'] = $row_data_cashin->rproyeksi3;
				$data_c['rproyeksi4'] = $row_data_cashin->rproyeksi4;
				$data_c['rproyeksi5'] = $row_data_cashin->rproyeksi5;
				$data_c['curentbuget'] = $row_data_cashin->curentbuget;
				$data_c['sbp'] = $row_data_cashin->sbp;
				$data_c['spp'] = $row_data_cashin->spp;
			} else {				
				$data_c['cashin_id'] = 0;
				$data_c['ket_id'] = 0;
				$data_c['realisasi'] = 0;
				$data_c['rproyeksi1'] = 0;
				$data_c['rproyeksi2'] = 0;
				$data_c['rproyeksi3'] = 0;
				$data_c['rproyeksi4'] = 0;
				$data_c['rproyeksi5'] = 0;
				$data_c['curentbuget'] = 0;
				$data_c['sbp'] = 0;
				$data_c['spp'] = 0;
			}

			if ($q_data_cashin_lalu->result()) {
				$row_data_cashin_lalu = $q_data_cashin_lalu->row();;
				if ($row_data_cashin_lalu->cashin_lalu == null) {
					$data_c['cashin_lalu'] = 0;
				} else {
					$data_c['cashin_lalu'] = $row_data_cashin_lalu->cashin_lalu;
				}
			} else {		
				$data_c['cashin_lalu'] = 0;
			}

			if ($q_data_cashin_kini->result()) {
				$row_data_cashin_kini = $q_data_cashin_kini->row();
				if ($row_data_cashin_kini->cashin_kini == null) {
					$data_c['cashin_kini'] = 0;
				} else {
					$data_c['cashin_kini'] = $row_data_cashin_kini->cashin_kini;
				}
			} else {		
				$data_c['cashin_kini'] = 0;
			}

			$data_cashin[] = $data_c;
		}

		$data_sum_cash_lalu_penerimaan['realisasi_lalu'] = 0;
		$data_sum_cash_lalu_penerimaan['realisasi_sekarang'] = 0;
		$data_sum_cash_lalu_penerimaan['realisasi_kini'] = 0;
		$data_sum_cash_lalu_penerimaan['proyeksi1'] = 0;
		$data_sum_cash_lalu_penerimaan['proyeksi2'] = 0;
		$data_sum_cash_lalu_penerimaan['proyeksi3'] = 0;
		$data_sum_cash_lalu_penerimaan['proyeksi4'] = 0;

		for ($i=0; $i < 5 ; $i++) { 
			$data_sum_cash_lalu_penerimaan['realisasi_lalu'] += $data_cashin[$i]['cashin_lalu'];
			$data_sum_cash_lalu_penerimaan['realisasi_sekarang'] += $data_cashin[$i]['realisasi'];
			$data_sum_cash_lalu_penerimaan['realisasi_kini'] += $data_cashin[$i]['cashin_kini'];
			$data_sum_cash_lalu_penerimaan['proyeksi1'] += $data_cashin[$i]['rproyeksi2'];
			$data_sum_cash_lalu_penerimaan['proyeksi2'] += $data_cashin[$i]['rproyeksi3'];
			$data_sum_cash_lalu_penerimaan['proyeksi3'] += $data_cashin[$i]['rproyeksi4'];
			$data_sum_cash_lalu_penerimaan['proyeksi4'] += $data_cashin[$i]['rproyeksi5'];
		}

		$data_sum_cash_pengeluaran['current_cash_budget'] = 0;
		$data_sum_cash_pengeluaran['realisasi_lalu'] = 0;
		$data_sum_cash_pengeluaran['realisasi_sekarang'] = 0;
		$data_sum_cash_pengeluaran['realisasi_kini'] = 0;
		$data_sum_cash_pengeluaran['proyeksi1'] = 0;
		$data_sum_cash_pengeluaran['proyeksi2'] = 0;
		$data_sum_cash_pengeluaran['proyeksi3'] = 0;
		$data_sum_cash_pengeluaran['proyeksi4'] = 0;

		for ($i=1; $i <=6 ; $i++) {
			switch ($i) {
				case 1:
					$numb_dp = 0;
					$numb_dc = 6;
				break;
				case 2:
					$numb_dp = 1;
					$numb_dc = 7;
				break;
				case 3:
					$numb_dp = 2;
					$numb_dc = 8;
				break;
				case 4:
					$numb_dp = 3;
					$numb_dc = 9;
				break;
				case 5:
					$numb_dp = 4;
					$numb_dc = 10;
				break;
				case 6:
					$numb_dp = 5;
					$numb_dc = 11;
				break;
			}
			$data_sum_cash_pengeluaran['current_cash_budget'] += $data_pengeluaran[$numb_dp]['total'];
			$data_sum_cash_pengeluaran['realisasi_lalu'] += $data_pengeluaran[$numb_dp]['total_lalu'];
			$data_sum_cash_pengeluaran['realisasi_sekarang'] += $data_pengeluaran[$numb_dp]['total_ini'];
			$data_sum_cash_pengeluaran['realisasi_kini'] += $data_pengeluaran[$numb_dp]['total_kini'];
			$data_sum_cash_pengeluaran['proyeksi1'] += $data_cashin[$numb_dc]['rproyeksi2'];
			$data_sum_cash_pengeluaran['proyeksi2'] += $data_cashin[$numb_dc]['rproyeksi3'];
			$data_sum_cash_pengeluaran['proyeksi3'] += $data_cashin[$numb_dc]['rproyeksi4'];
			$data_sum_cash_pengeluaran['proyeksi4'] += $data_cashin[$numb_dc]['rproyeksi5'];
		}

		for ($i=0; $i < 31 ; $i++) {
			$data['id'] = $i;
			$data['sbp'] = $data_cashin[15]['sbp'];
			$data['spp'] = $data_cashin[15]['spp'];
			switch ($i) {
				case 0:
					$data['kode'] = 'I';
					$data['uraian'] = 'Produksi Excl.PPN';
					$data['current_cash_budget'] = '';
					$data['realisasi_lalu'] = '';
					$data['realisasi_sekarang'] = '';
					$data['realisasi_kini'] = '';
					$data['proyeksi1'] = '';
					$data['proyeksi2'] = '';
					$data['proyeksi3'] = '';
					$data['proyeksi4'] = '';
					$data['jumlah'] = '';
					$data['sisa'] = '';
				break;
				case 1:					
					$data['kode'] = '';
					$data['uraian'] = 'AKUMULASI (%)';
					$data['current_cash_budget'] = $total_rab_persen.'%';
					$data['realisasi_lalu'] = $total_bln_lalu_persen.'%';
					$data['realisasi_sekarang'] = $total_bln_ini_persen.'%';
					$data['realisasi_kini'] = $total_bln_kini_persen.'%';
					$data['proyeksi1'] = $akumulasi1_persen.'%';
					$data['proyeksi2'] = $akumulasi2_persen.'%';
					$data['proyeksi3'] = $akumulasi3_persen.'%';
					$data['proyeksi4'] = $akumulasi4_persen.'%';
					$data['jumlah'] = $jml_produksi_persen.'%';
					$data['sisa'] = $sisa_produksi_persen.'%';
				break;
				case 2:
					$data['kode'] = '';
					$data['uraian'] = '1. Produksi (Rp.)';
					$data['current_cash_budget'] = $total_rab;
					$data['realisasi_lalu'] = $total_bln_lalu;
					$data['realisasi_sekarang'] = $total_bln_ini;
					$data['realisasi_kini'] = $total_bln_kini;
					$data['proyeksi1'] = $proyeksi1;
					$data['proyeksi2'] = $proyeksi2;
					$data['proyeksi3'] = $proyeksi3;
					$data['proyeksi4'] = $proyeksi4;
					$data['jumlah'] = $jml_produksi;
					$data['sisa'] = $sisa_produksi;
				break;
				case 3:
					$data['kode'] = '';
					$data['uraian'] = '2. Akumulasi (Rp.)';
					$data['current_cash_budget'] = $total_rab;
					$data['realisasi_lalu'] = $total_bln_lalu;
					$data['realisasi_sekarang'] = $total_bln_ini;
					$data['realisasi_kini'] = $total_bln_kini;
					$data['proyeksi1'] = $akumulasi1;
					$data['proyeksi2'] = $akumulasi2;
					$data['proyeksi3'] = $akumulasi3;
					$data['proyeksi4'] = $akumulasi4;
					$data['jumlah'] = $jml_produksi;
					$data['sisa'] = $sisa_produksi;
				break;
				case 4:
					$data['kode'] = 'II';
					$data['uraian'] = 'PENERIMAAN Excl.PPN';
					$data['current_cash_budget'] = '';
					$data['realisasi_lalu'] = '';
					$data['realisasi_sekarang'] = '';
					$data['realisasi_kini'] = '';
					$data['proyeksi1'] = '';
					$data['proyeksi2'] = '';
					$data['proyeksi3'] = '';
					$data['proyeksi4'] = '';
					$data['jumlah'] = '';
					$data['sisa'] = '';
				break;
				case 5:
					$data['kode'] = '';
					$data['uraian'] = '1. Uang Muka';
					$data['current_cash_budget'] = $uang_muka;
					$data['realisasi_lalu'] = $data_cashin[0]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[0]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[0]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[0]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[0]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[0]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[0]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 6:
					$data['kode'] = '';
					$data['uraian'] = '2. Termijn';
					$data['current_cash_budget'] = $total_rab;
					$data['realisasi_lalu'] = $data_cashin[1]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[1]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[1]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[1]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[1]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[1]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[1]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 7:
					$data['kode'] = '';
					$data['uraian'] = '3. Pengembalian retensi';
					$data['current_cash_budget'] = $retensi;
					$data['realisasi_lalu'] = $data_cashin[2]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[2]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[2]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[2]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[2]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[2]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[2]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 8:
					$data['kode'] = '';
					$data['uraian'] = '4. PotUang Muka';
					$data['current_cash_budget'] = -$total_rab;
					$data['realisasi_lalu'] = $data_cashin[3]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[3]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[3]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[3]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[3]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[3]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[3]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 9:
					$data['kode'] = '';
					$data['uraian'] = '5. Pot Retensi';
					$data['current_cash_budget'] = -$retensi;
					$data['realisasi_lalu'] = $data_cashin[4]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[4]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[4]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[4]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[4]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[4]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[4]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 10:
					$data['kode'] = '';
					$data['uraian'] = '6. Pph Final';
					$data['current_cash_budget'] = -$pph;
					$data['realisasi_lalu'] = $data_cashin[5]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[5]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[5]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[5]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[5]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[5]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[5]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 11:
					$data['kode'] = '';
					$data['uraian'] = 'JUMLAH PENERIMAAN';
					$data['current_cash_budget'] = $uang_muka + $total_rab + $retensi + (-$total_rab) + (-$retensi) + (-$pph);
					$data['realisasi_lalu'] = $data_sum_cash_lalu_penerimaan['realisasi_lalu'];
					$data['realisasi_sekarang'] = $data_sum_cash_lalu_penerimaan['realisasi_sekarang'];
					$data['realisasi_kini'] = $data_sum_cash_lalu_penerimaan['realisasi_kini'];
					$data['proyeksi1'] = $data_sum_cash_lalu_penerimaan['proyeksi1'];
					$data['proyeksi2'] = $data_sum_cash_lalu_penerimaan['proyeksi2'];
					$data['proyeksi3'] = $data_sum_cash_lalu_penerimaan['proyeksi3'];
					$data['proyeksi4'] = $data_sum_cash_lalu_penerimaan['proyeksi4'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 12:
					$data['kode'] = 'III';
					$data['uraian'] = '1. Biaya Bahan';
					$data['current_cash_budget'] = $data_pengeluaran[0]['total'];
					$data['realisasi_lalu'] = $data_pengeluaran[0]['total_lalu'];
					$data['realisasi_sekarang'] = $data_pengeluaran[0]['total_ini'];
					$data['realisasi_kini'] = $data_pengeluaran[0]['total_kini'];
					$data['proyeksi1'] = $data_cashin[6]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[6]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[6]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[6]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];;
				break;
				case 13:
					$data['kode'] = '';
					$data['uraian'] = '2. Biaya Upah';
					$data['current_cash_budget'] = $data_pengeluaran[1]['total'];
					$data['realisasi_lalu'] = $data_pengeluaran[1]['total_lalu'];
					$data['realisasi_sekarang'] = $data_pengeluaran[1]['total_ini'];
					$data['realisasi_kini'] = $data_pengeluaran[1]['total_kini'];
					$data['proyeksi1'] = $data_cashin[7]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[7]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[7]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[7]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 14:
					$data['kode'] = '';
					$data['uraian'] = '3. Biaya Peralatan';
					$data['current_cash_budget'] = $data_pengeluaran[2]['total'];
					$data['realisasi_lalu'] = $data_pengeluaran[2]['total_lalu'];
					$data['realisasi_sekarang'] = $data_pengeluaran[2]['total_ini'];
					$data['realisasi_kini'] = $data_pengeluaran[2]['total_kini'];
					$data['proyeksi1'] = $data_cashin[8]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[8]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[8]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[8]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 15:
					$data['kode'] = '';
					$data['uraian'] = '4. Biaya Sub Kontraktor';
					$data['current_cash_budget'] = $data_pengeluaran[3]['total'];
					$data['realisasi_lalu'] = $data_pengeluaran[3]['total_lalu'];
					$data['realisasi_sekarang'] = $data_pengeluaran[3]['total_ini'];
					$data['realisasi_kini'] = $data_pengeluaran[3]['total_kini'];
					$data['proyeksi1'] = $data_cashin[9]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[9]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[9]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[9]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 16:
					$data['kode'] = '';
					$data['uraian'] = '5. Biaya Bank';
					$data['current_cash_budget'] = $data_pengeluaran[4]['total'];
					$data['realisasi_lalu'] = $data_pengeluaran[4]['total_lalu'];
					$data['realisasi_sekarang'] = $data_pengeluaran[4]['total_ini'];
					$data['realisasi_kini'] = $data_pengeluaran[4]['total_kini'];
					$data['proyeksi1'] = $data_cashin[10]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[10]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[10]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[10]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 17:
					$data['kode'] = '';
					$data['uraian'] = '6. BAU Proyek';
					$data['current_cash_budget'] = $data_pengeluaran[5]['total'];
					$data['realisasi_lalu'] = $data_pengeluaran[5]['total_lalu'];
					$data['realisasi_sekarang'] = $data_pengeluaran[5]['total_ini'];
					$data['realisasi_kini'] = $data_pengeluaran[5]['total_kini'];
					$data['proyeksi1'] = $data_cashin[11]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[11]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[11]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[11]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 18:
					$data['kode'] = '';
					$data['uraian'] = '7. Persiapan Penyelesaian';
					$data['current_cash_budget'] = $data_pengeluaran[6]['total'];
					$data['realisasi_lalu'] = '';
					$data['realisasi_sekarang'] = '';
					$data['realisasi_kini'] = '';
					$data['proyeksi1'] = '';
					$data['proyeksi2'] = '';
					$data['proyeksi3'] = '';
					$data['proyeksi4'] = '';
					$data['jumlah'] = '';
					$data['sisa'] = $data_pengeluaran[6]['total'];
				break;
				case 19:
					$data['kode'] = '';
					$data['uraian'] = 'JUMLAH III';
					$data['current_cash_budget'] = $data_sum_cash_pengeluaran['current_cash_budget'] + $data_pengeluaran[6]['total'];
					$data['realisasi_lalu'] = $data_sum_cash_pengeluaran['realisasi_lalu'];
					$data['realisasi_sekarang'] = $data_sum_cash_pengeluaran['realisasi_sekarang'];
					$data['realisasi_kini'] = $data_sum_cash_pengeluaran['realisasi_kini'];
					$data['proyeksi1'] = $data_sum_cash_pengeluaran['proyeksi1'];
					$data['proyeksi2'] = $data_sum_cash_pengeluaran['proyeksi2'];
					$data['proyeksi3'] = $data_sum_cash_pengeluaran['proyeksi3'];
					$data['proyeksi4'] = $data_sum_cash_pengeluaran['proyeksi4'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] + $data_pengeluaran[6]['total'] - $data['jumlah'];
				break;
				case 20:
					$data['kode'] = 'IV';
					$data['uraian'] = 'POSISI(II-III)';
					$data['current_cash_budget'] = ($uang_muka + $total_rab + $retensi + (-$total_rab) + (-$retensi) + (-$pph)) - ($data_sum_cash_pengeluaran['current_cash_budget'] + $data_pengeluaran[6]['total']);
					$data['realisasi_lalu'] = $data_sum_cash_lalu_penerimaan['realisasi_lalu'] - $data_sum_cash_pengeluaran['realisasi_lalu'];
					$data['realisasi_sekarang'] = $data_sum_cash_lalu_penerimaan['realisasi_sekarang'] - $data_sum_cash_pengeluaran['realisasi_sekarang'];
					$data['realisasi_kini'] = $data_sum_cash_lalu_penerimaan['realisasi_kini'] - $data_sum_cash_pengeluaran['realisasi_kini'];
					$data['proyeksi1'] = $data_sum_cash_lalu_penerimaan['proyeksi1'] - $data_sum_cash_pengeluaran['proyeksi1'];
					$data['proyeksi2'] = $data_sum_cash_lalu_penerimaan['proyeksi2'] - $data_sum_cash_pengeluaran['proyeksi2'];
					$data['proyeksi3'] = $data_sum_cash_lalu_penerimaan['proyeksi3'] - $data_sum_cash_pengeluaran['proyeksi3'];
					$data['proyeksi4'] = $data_sum_cash_lalu_penerimaan['proyeksi4'] - $data_sum_cash_pengeluaran['proyeksi4'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 21:
					$data['kode'] = 'V';
					$data['uraian'] = '1. Pajak Keluaran';
					$data['current_cash_budget'] = $data_cashin[12]['curentbuget'];
					$data['realisasi_lalu'] = $data_cashin[12]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[12]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[12]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[12]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[12]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[12]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[12]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 22:
					$data['kode'] = '';
					$data['uraian'] = '2. Pajak Masukan';
					$data['current_cash_budget'] = $data_cashin[13]['curentbuget'];
					$data['realisasi_lalu'] = $data_cashin[13]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[13]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[13]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[13]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[13]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[13]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[13]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 23:
					$data['kode'] = '';
					$data['uraian'] = 'JUMLAH V(1-2)';
					$data['current_cash_budget'] = $data_cashin[12]['curentbuget'] - $data_cashin[13]['curentbuget'];
					$data['realisasi_lalu'] = $data_cashin[12]['curentbuget'] - $data_cashin[13]['curentbuget'];
					$data['realisasi_sekarang'] = $data_cashin[12]['realisasi'] - $data_cashin[13]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[12]['cashin_kini'] - $data_cashin[13]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[12]['rproyeksi2'] - $data_cashin[13]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[12]['rproyeksi3'] - $data_cashin[13]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[12]['rproyeksi4'] - $data_cashin[13]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[12]['rproyeksi5'] - $data_cashin[13]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 24:
					$data['kode'] = 'VI';
					$data['uraian'] = 'POSISI V(IV + V)';
					$data['current_cash_budget'] = ($uang_muka + $total_rab + $retensi + (-$total_rab) + (-$retensi) + (-$pph)) - ($data_sum_cash_pengeluaran['current_cash_budget'] + $data_pengeluaran[6]['total']) + ($data_cashin[12]['curentbuget'] - $data_cashin[13]['curentbuget']);
					$data['realisasi_lalu'] = ($data_sum_cash_lalu_penerimaan['realisasi_lalu'] - $data_sum_cash_pengeluaran['realisasi_lalu']) + ($data_cashin[12]['curentbuget'] - $data_cashin[13]['curentbuget']);
					$data['realisasi_sekarang'] = ($data_sum_cash_lalu_penerimaan['realisasi_sekarang'] - $data_sum_cash_pengeluaran['realisasi_sekarang']) + ($data_cashin[12]['realisasi'] - $data_cashin[13]['realisasi']);
					$data['realisasi_kini'] = ($data_sum_cash_lalu_penerimaan['realisasi_kini'] - $data_sum_cash_pengeluaran['realisasi_kini']) + ($data_cashin[12]['cashin_kini'] - $data_cashin[13]['cashin_kini']);
					$data['proyeksi1'] = ($data_sum_cash_lalu_penerimaan['proyeksi1'] - $data_sum_cash_pengeluaran['proyeksi1']) + ($data_cashin[12]['rproyeksi2'] - $data_cashin[13]['rproyeksi2']);
					$data['proyeksi2'] = ($data_sum_cash_lalu_penerimaan['proyeksi2'] - $data_sum_cash_pengeluaran['proyeksi2']) + ($data_cashin[12]['rproyeksi3'] - $data_cashin[13]['rproyeksi3']);
					$data['proyeksi3'] = ($data_sum_cash_lalu_penerimaan['proyeksi3'] - $data_sum_cash_pengeluaran['proyeksi3']) + ($data_cashin[12]['rproyeksi4'] - $data_cashin[13]['rproyeksi4']);
					$data['proyeksi4'] = ($data_sum_cash_lalu_penerimaan['proyeksi4'] - $data_sum_cash_pengeluaran['proyeksi4']) + ($data_cashin[12]['rproyeksi5'] - $data_cashin[13]['rproyeksi5']);
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 25:
					$data['kode'] = 'VII';
					$data['uraian'] = '1. Penerimaan Pinjaman';
					$data['current_cash_budget'] = $data_cashin[14]['curentbuget'];
					$data['realisasi_lalu'] = $data_cashin[14]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[14]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[14]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[14]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[14]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[14]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[14]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 26:
					$data['kode'] = '';
					$data['uraian'] = '2. Pengembalian Pinjaman';
					$data['current_cash_budget'] = $data_cashin[15]['curentbuget'];
					$data['realisasi_lalu'] = $data_cashin[15]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[15]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[15]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[15]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[15]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[15]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[15]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 27:
					$data['kode'] = '';
					$data['uraian'] = 'JUMLAH VII';
					$data['current_cash_budget'] = $data_cashin[14]['curentbuget'] + $data_cashin[15]['curentbuget'];
					$data['realisasi_lalu'] = $data_cashin[14]['cashin_lalu'] + $data_cashin[15]['cashin_lalu'];
					$data['realisasi_sekarang'] = $data_cashin[14]['realisasi'] + $data_cashin[15]['realisasi'];
					$data['realisasi_kini'] = $data_cashin[14]['cashin_kini'] + $data_cashin[15]['cashin_kini'];
					$data['proyeksi1'] = $data_cashin[14]['rproyeksi2'] + $data_cashin[15]['rproyeksi2'];
					$data['proyeksi2'] = $data_cashin[14]['rproyeksi3'] + $data_cashin[15]['rproyeksi3'];
					$data['proyeksi3'] = $data_cashin[14]['rproyeksi4'] + $data_cashin[15]['rproyeksi4'];
					$data['proyeksi4'] = $data_cashin[14]['rproyeksi5'] + $data_cashin[15]['rproyeksi5'];
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 28:
					$data['kode'] = 'VIII';
					$data['uraian'] = 'POSISI(VI + VII)';
					$data['current_cash_budget'] = (($uang_muka + $total_rab + $retensi + (-$total_rab) + (-$retensi) + (-$pph)) - ($data_sum_cash_pengeluaran['current_cash_budget'] + $data_pengeluaran[6]['total']) + ($data_cashin[12]['curentbuget'] - $data_cashin[13]['curentbuget'])) + ($data_cashin[14]['curentbuget'] + $data_cashin[15]['curentbuget']);
					$data['realisasi_lalu'] =  (($data_sum_cash_lalu_penerimaan['realisasi_lalu'] - $data_sum_cash_pengeluaran['realisasi_lalu']) + ($data_cashin[12]['curentbuget'] - $data_cashin[13]['curentbuget'])) + ($data_cashin[14]['cashin_lalu'] + $data_cashin[15]['cashin_lalu']);
					$data['realisasi_sekarang'] =  (($data_sum_cash_lalu_penerimaan['realisasi_sekarang'] - $data_sum_cash_pengeluaran['realisasi_sekarang']) + ($data_cashin[12]['realisasi'] - $data_cashin[13]['realisasi'])) + ($data_cashin[14]['realisasi'] + $data_cashin[15]['realisasi']);
					$data['realisasi_kini'] = (($data_sum_cash_lalu_penerimaan['realisasi_kini'] - $data_sum_cash_pengeluaran['realisasi_kini']) + ($data_cashin[12]['cashin_kini'] - $data_cashin[13]['cashin_kini'])) + ($data_cashin[14]['cashin_kini'] + $data_cashin[15]['cashin_kini']);
					$data['proyeksi1'] = (($data_sum_cash_lalu_penerimaan['proyeksi1'] - $data_sum_cash_pengeluaran['proyeksi1']) + ($data_cashin[12]['rproyeksi2'] - $data_cashin[13]['rproyeksi2'])) + ($data_cashin[14]['rproyeksi2'] + $data_cashin[15]['rproyeksi2']);
					$data['proyeksi2'] = (($data_sum_cash_lalu_penerimaan['proyeksi2'] - $data_sum_cash_pengeluaran['proyeksi2']) + ($data_cashin[12]['rproyeksi3'] - $data_cashin[13]['rproyeksi3'])) + ($data_cashin[14]['rproyeksi3'] + $data_cashin[15]['rproyeksi3']);
					$data['proyeksi3'] = (($data_sum_cash_lalu_penerimaan['proyeksi3'] - $data_sum_cash_pengeluaran['proyeksi3']) + ($data_cashin[12]['rproyeksi4'] - $data_cashin[13]['rproyeksi4'])) + ($data_cashin[14]['rproyeksi4'] + $data_cashin[15]['rproyeksi4']);
					$data['proyeksi4'] = (($data_sum_cash_lalu_penerimaan['proyeksi4'] - $data_sum_cash_pengeluaran['proyeksi4']) + ($data_cashin[12]['rproyeksi5'] - $data_cashin[13]['rproyeksi5'])) + ($data_cashin[14]['rproyeksi5'] + $data_cashin[15]['rproyeksi5']);
					$data['jumlah'] = $data['realisasi_kini'] + $data['proyeksi1'] + $data['proyeksi2'] + $data['proyeksi3'] + $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;
				case 29:
					$data['kode'] = 'IX';
					$data['uraian'] = 'KAS AWAL';
					$data['current_cash_budget'] = '-';
					$data['realisasi_lalu'] = '';
					$data['realisasi_sekarang'] = '';
					$data['realisasi_kini'] = '';
					$data['proyeksi1'] = '';
					$data['proyeksi2'] = '';
					$data['proyeksi3'] = '';
					$data['proyeksi4'] = '';
					$data['jumlah'] = '';
					$data['sisa'] = '';
				break;
				case 30:
					$data['kode'] = 'X';
					$data['uraian'] = 'KAS AKHIR';
					$data['current_cash_budget'] = (($uang_muka + $total_rab + $retensi + (-$total_rab) + (-$retensi) + (-$pph)) - ($data_sum_cash_pengeluaran['current_cash_budget'] + $data_pengeluaran[6]['total']) + ($data_cashin[12]['curentbuget'] - $data_cashin[13]['curentbuget'])) + ($data_cashin[14]['curentbuget'] + $data_cashin[15]['curentbuget']);
					$data['realisasi_lalu'] =  (($data_sum_cash_lalu_penerimaan['realisasi_lalu'] - $data_sum_cash_pengeluaran['realisasi_lalu']) + ($data_cashin[12]['curentbuget'] - $data_cashin[13]['curentbuget'])) + ($data_cashin[14]['cashin_lalu'] + $data_cashin[15]['cashin_lalu']);
					$data['realisasi_sekarang'] =  (($data_sum_cash_lalu_penerimaan['realisasi_sekarang'] - $data_sum_cash_pengeluaran['realisasi_sekarang']) + ($data_cashin[12]['realisasi'] - $data_cashin[13]['realisasi'])) + ($data_cashin[14]['realisasi'] + $data_cashin[15]['realisasi']);
					$data['realisasi_kini'] = (($data_sum_cash_lalu_penerimaan['realisasi_kini'] - $data_sum_cash_pengeluaran['realisasi_kini']) + ($data_cashin[12]['cashin_kini'] - $data_cashin[13]['cashin_kini'])) + ($data_cashin[14]['cashin_kini'] + $data_cashin[15]['cashin_kini']);
					$data['proyeksi1'] = (($data_sum_cash_lalu_penerimaan['realisasi_kini'] - $data_sum_cash_pengeluaran['realisasi_kini']) + ($data_cashin[12]['cashin_kini'] - $data_cashin[13]['cashin_kini'])) + ($data_cashin[14]['cashin_kini'] + $data_cashin[15]['cashin_kini']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi1'] - $data_sum_cash_pengeluaran['proyeksi1']) + ($data_cashin[12]['rproyeksi2'] - $data_cashin[13]['rproyeksi2'])) + ($data_cashin[14]['rproyeksi2'] + $data_cashin[15]['rproyeksi2']);
					$data['proyeksi2'] = (($data_sum_cash_lalu_penerimaan['realisasi_kini'] - $data_sum_cash_pengeluaran['realisasi_kini']) + ($data_cashin[12]['cashin_kini'] - $data_cashin[13]['cashin_kini'])) + ($data_cashin[14]['cashin_kini'] + $data_cashin[15]['cashin_kini']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi1'] - $data_sum_cash_pengeluaran['proyeksi1']) + ($data_cashin[12]['rproyeksi2'] - $data_cashin[13]['rproyeksi2'])) + ($data_cashin[14]['rproyeksi2'] + $data_cashin[15]['rproyeksi2']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi2'] - $data_sum_cash_pengeluaran['proyeksi2']) + ($data_cashin[12]['rproyeksi3'] - $data_cashin[13]['rproyeksi3'])) + ($data_cashin[14]['rproyeksi3'] + $data_cashin[15]['rproyeksi3']);
					$data['proyeksi3'] = (($data_sum_cash_lalu_penerimaan['realisasi_kini'] - $data_sum_cash_pengeluaran['realisasi_kini']) + ($data_cashin[12]['cashin_kini'] - $data_cashin[13]['cashin_kini'])) + ($data_cashin[14]['cashin_kini'] + $data_cashin[15]['cashin_kini']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi1'] - $data_sum_cash_pengeluaran['proyeksi1']) + ($data_cashin[12]['rproyeksi2'] - $data_cashin[13]['rproyeksi2'])) + ($data_cashin[14]['rproyeksi2'] + $data_cashin[15]['rproyeksi2']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi2'] - $data_sum_cash_pengeluaran['proyeksi2']) + ($data_cashin[12]['rproyeksi3'] - $data_cashin[13]['rproyeksi3'])) + ($data_cashin[14]['rproyeksi3'] + $data_cashin[15]['rproyeksi3']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi3'] - $data_sum_cash_pengeluaran['proyeksi3']) + ($data_cashin[12]['rproyeksi4'] - $data_cashin[13]['rproyeksi4'])) + ($data_cashin[14]['rproyeksi4'] + $data_cashin[15]['rproyeksi4']);
					$data['proyeksi4'] = (($data_sum_cash_lalu_penerimaan['realisasi_kini'] - $data_sum_cash_pengeluaran['realisasi_kini']) + ($data_cashin[12]['cashin_kini'] - $data_cashin[13]['cashin_kini'])) + ($data_cashin[14]['cashin_kini'] + $data_cashin[15]['cashin_kini']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi1'] - $data_sum_cash_pengeluaran['proyeksi1']) + ($data_cashin[12]['rproyeksi2'] - $data_cashin[13]['rproyeksi2'])) + ($data_cashin[14]['rproyeksi2'] + $data_cashin[15]['rproyeksi2']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi2'] - $data_sum_cash_pengeluaran['proyeksi2']) + ($data_cashin[12]['rproyeksi3'] - $data_cashin[13]['rproyeksi3'])) + ($data_cashin[14]['rproyeksi3'] + $data_cashin[15]['rproyeksi3']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi3'] - $data_sum_cash_pengeluaran['proyeksi3']) + ($data_cashin[12]['rproyeksi4'] - $data_cashin[13]['rproyeksi4'])) + ($data_cashin[14]['rproyeksi4'] + $data_cashin[15]['rproyeksi4']) +
										(($data_sum_cash_lalu_penerimaan['proyeksi4'] - $data_sum_cash_pengeluaran['proyeksi4']) + ($data_cashin[12]['rproyeksi5'] - $data_cashin[13]['rproyeksi5'])) + ($data_cashin[14]['rproyeksi5'] + $data_cashin[15]['rproyeksi5']);
					$data['jumlah'] = $data['proyeksi4'];
					$data['sisa'] = $data['current_cash_budget'] - $data['jumlah'];
				break;

			}
			
			$dat[] = $data;
		}

		return '{"data":'.json_encode($dat).'}';

	}

	function insert_cashflow($data)
	{
		$proyek_id = $data['proyek_id'];
		$tgl_rab = $data['tahap_tanggal_kendali'];
		$ket_id = $data['ket_id'];

		$cek_data = $this->db->query("select * from simpro_tbl_cashin where proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab' and ket_id=$ket_id");
		if ($cek_data->result()) {
			$data_update = array(
	        	'ip_update'=>$data['ip_update'],
				'divisi_id'=>$data['divisi_id'],
				'user_id'=>$data['user_id'],
				'waktu_update'=>$data['waktu_update'],		
				'tgl_update'=>$data['tgl_update'],
				'realisasi'=>$data['realisasi'],
		        'rproyeksi1'=>$data['rproyeksi1'],
		        'rproyeksi2'=>$data['rproyeksi2'],
		        'rproyeksi3'=>$data['rproyeksi3'],
		        'rproyeksi4'=>$data['rproyeksi4'],
		        'rproyeksi5'=>$data['rproyeksi5'],
		        'curentbuget'=>$data['curentbuget'],
		        'spp'=>$data['spp'],
		        'sbp'=>$data['sbp']
	        );
			$var = array(
				'proyek_id' => $proyek_id, 
				'tahap_tanggal_kendali' => $tgl_rab, 
				'ket_id' => $ket_id
			);
			$this->db->where($var);
			$this->db->update('simpro_tbl_cashin',$data_update);
		} else {
			$this->db->insert('simpro_tbl_cashin',$data);
		}
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

	function get_data_cut_off($proyek_id,$tgl_rab)
	{

		$chars = preg_split('[-]', $tgl_rab, -1, PREG_SPLIT_DELIM_CAPTURE);
		$bln= trim($chars[1]);
		$thn= trim($chars[0]);

		$this->db->trans_begin();

		$sql_po2_cek="select * from simpro_tbl_po2 where tahap_tanggal_kendali='$tgl_rab' and proyek_id='$proyek_id'";
		$q_po2_cek = $this->db->query($sql_po2_cek);

		if ($q_po2_cek->num_rows() > 0) {
			$sql_delete_po2="delete from simpro_tbl_po2 where tahap_tanggal_kendali='$tgl_rab' and proyek_id='$proyek_id'";
			$this->db->query($sql_delete_po2);
		}

		$sql_rap = "SELECT 					
					DISTINCT(tbl_total_koef.kode_material) as kode_material,
					tbl_total_koef.kode_rap,
					simpro_tbl_detail_material.detail_material_id as id_detail_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					tbl_harga.harga,
					ROUND(SUM(tbl_total_koef.volume_total),4) as koefisien,
					(tbl_harga.harga * SUM(tbl_total_koef.volume_total)) as subtotal,
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
						WHERE id_proyek = $proyek_id
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
								WHERE id_proyek = $proyek_id
								GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
								ORDER BY kode_material ASC
							) tbl_asat ON tbl_asat.id_proyek = simpro_rap_analisa_apek.id_proyek AND tbl_asat.kode_analisa = simpro_rap_analisa_apek.kode_analisa
							WHERE simpro_rap_analisa_apek.id_proyek = $proyek_id
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
					INNER JOIN simpro_rap_analisa_item_apek ON simpro_rap_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_rap_analisa_item_apek.id_proyek = $proyek_id
					INNER JOIN simpro_rap_item_tree ON simpro_rap_item_tree.id_proyek = simpro_rap_analisa_item_apek.id_proyek AND simpro_rap_item_tree.kode_tree = simpro_rap_analisa_item_apek.kode_tree					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_material), 
					harga 
					FROM simpro_rap_analisa_asat
					WHERE id_proyek = $proyek_id
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
					simpro_tbl_detail_material.detail_material_id,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name		
				ORDER BY tbl_total_koef.kode_material ASC";

		$q_rap = $this->db->query($sql_rap);

		if ($q_rap->result()) {
			foreach ($q_rap->result() as $row_rap) {

				$id_detail_material = $row_rap->id_detail_material;
				$kode_material = $row_rap->kode_material;
				$detail_material_nama = $row_rap->detail_material_nama;
				$detail_material_satuan = $row_rap->detail_material_satuan;
				$koefisien = $row_rap->koefisien;
				$harga = $row_rap->harga;
				$subtotal = $row_rap->subtotal;
				$kode_rap = $row_rap->kode_rap;

				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {
					$sql_action_rap="update 
					simpro_tbl_po2 set 
					kode_rap='$kode_rap',
					volume_ob='$koefisien',
					harga_sat_ob='$harga',
					uraian='-',
					jumlah_ob='$subtotal'
					where proyek_id=$proyek_id 
					and tahap_tanggal_kendali='$tgl_rab' 
					and kode_rap='$kode_rap'";
				} else {
					$sql_action_rap="insert into 
					simpro_tbl_po2(proyek_id,
						tahap_tanggal_kendali,
						detail_material_kode,
						detail_material_id,
						detail_material_nama,
						detail_material_satuan,
						volume_ob,
						harga_sat_ob,
						jumlah_ob,
						volume_cash_td,
						jumlah_cash_td,
						volume_hutang,
						jumlah_hutang,
						volume_hp,
						jumlah_hp,
						volume_cost_td,
						jumlah_cost_td,
						volume_cost_tg,
						hargasat_cost_tg,
						jumlah_cost_tg,
						volume_cf,
						jumlah_cf,
						trend,
						volume_tot_hutang,
						total_hutang,
						vol_cash_hutang,
						jum_cash_hutang,
						volume_cb,
						hargasat_cb,
						jumlah_cb,
						uraian,
						kode_rap) values(
						'$proyek_id',
						'$tgl_rab',
						'$kode_material',
						'$id_detail_material',
						'$detail_material_nama',
						'$detail_material_satuan',
						'$koefisien',
						'$harga',
						'$subtotal',
						'0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0',
						'-',
						'$kode_rap')";
				}

				$this->db->query($sql_action_rap);
			}
		}

				$sql_cbd = "SELECT 
					
					DISTINCT(tbl_total_koef.kode_material) as kode_material,
					tbl_total_koef.kode_rap,
					simpro_tbl_detail_material.detail_material_id as id_detail_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					tbl_harga.harga,
					ROUND(SUM(tbl_total_koef.volume_total),4) as koefisien,
					(tbl_harga.harga * SUM(tbl_total_koef.volume_total)) as subtotal,
					tbl_total_koef.id_proyek,
					tbl_total_koef.tanggal_kendali
				FROM (				
					SELECT 
					tbl_asat_apek.*,
					simpro_current_budget_analisa_item_apek.kode_tree,
					simpro_current_budget_item_tree.volume,
					simpro_current_budget_item_tree.tanggal_kendali,
					(simpro_current_budget_item_tree.volume * tbl_asat_apek.tot_koef) as volume_total
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
						simpro_current_budget_analisa_asat
						WHERE id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
						GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
						ORDER BY kode_material ASC
						)
						UNION ALL
						(
							SELECT 
								DISTINCT(tbl_asat.kode_material) as kode_material,
								simpro_current_budget_analisa_apek.id_proyek,
								(simpro_current_budget_analisa_apek.koefisien * SUM(tbl_asat.jml_material)) * tbl_asat.koefisien as tot_koef,
								simpro_current_budget_analisa_apek.kode_analisa,
								simpro_current_budget_analisa_apek.parent_kode_analisa,
								tbl_asat.kode_rap
							FROM simpro_current_budget_analisa_apek
							LEFT JOIN (
								SELECT 
									DISTINCT(kode_material), 
									COUNT(kode_material) as jml_material,
									koefisien,
									id_proyek,
									kode_analisa,
									kode_rap
								FROM 
								simpro_current_budget_analisa_asat
								WHERE id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
								GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
								ORDER BY kode_material ASC
							) tbl_asat ON tbl_asat.id_proyek = simpro_current_budget_analisa_apek.id_proyek AND tbl_asat.kode_analisa = simpro_current_budget_analisa_apek.kode_analisa
							WHERE simpro_current_budget_analisa_apek.id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
							GROUP BY  
							tbl_asat.kode_material,
							tbl_asat.koefisien,
							simpro_current_budget_analisa_apek.kode_analisa,
							simpro_current_budget_analisa_apek.parent_kode_analisa,						
							simpro_current_budget_analisa_apek.koefisien,
							simpro_current_budget_analisa_apek.id_proyek,
							tbl_asat.kode_rap						
						)
					) as tbl_asat_apek
					INNER JOIN simpro_current_budget_analisa_item_apek ON simpro_current_budget_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_current_budget_analisa_item_apek.id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
					INNER JOIN simpro_current_budget_item_tree ON simpro_current_budget_item_tree.id_proyek = simpro_current_budget_analisa_item_apek.id_proyek AND simpro_current_budget_item_tree.kode_tree = simpro_current_budget_analisa_item_apek.kode_tree					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_material), 
					harga 
					FROM simpro_current_budget_analisa_asat
					WHERE id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
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
					simpro_tbl_detail_material.detail_material_id,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name,
					tbl_total_koef.tanggal_kendali		
				ORDER BY tbl_total_koef.kode_material ASC";

		$q_cbd = $this->db->query($sql_cbd);

		if ($q_cbd->result()) {
			foreach ($q_cbd->result() as $row_cbd) {

				$id_detail_material = $row_cbd->id_detail_material;
				$kode_material = $row_cbd->kode_material;
				$detail_material_nama = $row_cbd->detail_material_nama;
				$detail_material_satuan = $row_cbd->detail_material_satuan;
				$koefisien = $row_cbd->koefisien;
				$harga = $row_cbd->harga;
				$subtotal = $row_cbd->subtotal;
				$kode_rap = $row_cbd->kode_rap;
				$tanggal_kendali = $row_cbd->tanggal_kendali;

				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {
					$sql_action_cbd="update simpro_tbl_po2 set 
					volume_cb='$koefisien',
					hargasat_cb='$harga',
					jumlah_cb='$subtotal'
					where proyek_id='$proyek_id' 
					and tahap_tanggal_kendali='$tgl_rab' 
					and kode_rap='$kode_rap'";
				} else {					
					$sql_action_cbd="insert into simpro_tbl_po2(proyek_id,tahap_tanggal_kendali,detail_material_id,detail_material_kode,detail_material_nama,detail_material_satuan,volume_ob,harga_sat_ob,jumlah_ob,volume_cash_td,jumlah_cash_td,volume_hutang,jumlah_hutang,volume_hp,jumlah_hp,volume_cost_td,jumlah_cost_td,volume_cost_tg,hargasat_cost_tg,jumlah_cost_tg,volume_cf,jumlah_cf,trend,volume_tot_hutang,total_hutang,vol_cash_hutang,jum_cash_hutang,volume_cb,hargasat_cb,jumlah_cb) values(
						'$proyek_id',
						'$tgl_rab',
						'$id_detail_material',
						'$kode_material',
						'$detail_material_nama',
						'$detail_material_satuan',
						'0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0',
						'$koefisien',
						'$harga',
						'$subtotal')";
				}

				$this->db->query($sql_action_cbd);
			}
		}

		$sql_ctg = "SELECT 
					
					DISTINCT(tbl_total_koef.kode_material) as kode_material,
					tbl_total_koef.kode_rap,
					simpro_tbl_detail_material.detail_material_id as id_detail_material,
					simpro_tbl_detail_material.detail_material_nama,
					simpro_tbl_detail_material.detail_material_satuan,
					(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
					tbl_harga.harga,
					ROUND(SUM(tbl_total_koef.volume_total),4) as koefisien,
					(tbl_harga.harga * SUM(tbl_total_koef.volume_total)) as subtotal,
					tbl_total_koef.id_proyek,
					tbl_total_koef.tanggal_kendali
				FROM (				
					SELECT 
					tbl_asat_apek.*,
					simpro_costogo_analisa_item_apek.kode_tree,
					simpro_costogo_item_tree.volume,
					simpro_costogo_item_tree.tanggal_kendali,
					(simpro_costogo_item_tree.volume * tbl_asat_apek.tot_koef) as volume_total
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
						simpro_costogo_analisa_asat
						WHERE id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
						GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
						ORDER BY kode_material ASC
						)
						UNION ALL
						(
							SELECT 
								DISTINCT(tbl_asat.kode_material) as kode_material,
								simpro_costogo_analisa_apek.id_proyek,
								(simpro_costogo_analisa_apek.koefisien * SUM(tbl_asat.jml_material)) * tbl_asat.koefisien as tot_koef,
								simpro_costogo_analisa_apek.kode_analisa,
								simpro_costogo_analisa_apek.parent_kode_analisa,
								tbl_asat.kode_rap
							FROM simpro_costogo_analisa_apek
							LEFT JOIN (
								SELECT 
									DISTINCT(kode_material), 
									COUNT(kode_material) as jml_material,
									koefisien,
									id_proyek,
									kode_analisa,
									kode_rap
								FROM 
								simpro_costogo_analisa_asat
								WHERE id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
								GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
								ORDER BY kode_material ASC
							) tbl_asat ON tbl_asat.id_proyek = simpro_costogo_analisa_apek.id_proyek AND tbl_asat.kode_analisa = simpro_costogo_analisa_apek.kode_analisa
							WHERE simpro_costogo_analisa_apek.id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
							GROUP BY  
							tbl_asat.kode_material,
							tbl_asat.koefisien,
							simpro_costogo_analisa_apek.kode_analisa,
							simpro_costogo_analisa_apek.parent_kode_analisa,						
							simpro_costogo_analisa_apek.koefisien,
							simpro_costogo_analisa_apek.id_proyek,
							tbl_asat.kode_rap						
						)
					) as tbl_asat_apek
					INNER JOIN simpro_costogo_analisa_item_apek ON simpro_costogo_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_costogo_analisa_item_apek.id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
					INNER JOIN simpro_costogo_item_tree ON simpro_costogo_item_tree.id_proyek = simpro_costogo_analisa_item_apek.id_proyek AND simpro_costogo_item_tree.kode_tree = simpro_costogo_analisa_item_apek.kode_tree					
				) as tbl_total_koef 
				INNER JOIN (
					SELECT 
					DISTINCT(kode_material), 
					harga 
					FROM simpro_costogo_analisa_asat
					WHERE id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'
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
					simpro_tbl_detail_material.detail_material_id,
					simpro_tbl_subbidang.subbidang_kode,
					simpro_tbl_subbidang.subbidang_name,
					tbl_total_koef.tanggal_kendali		
				ORDER BY tbl_total_koef.kode_material ASC";

		$q_ctg = $this->db->query($sql_ctg);

		if ($q_ctg->result()) {
			foreach ($q_ctg->result() as $row_ctg) {

				$id_detail_material = $row_ctg->id_detail_material;
				$kode_material = $row_ctg->kode_material;
				$nama = $row_ctg->detail_material_nama;
				$satuan = $row_ctg->detail_material_satuan;
				$koefisien = $row_ctg->koefisien;
				$hs_togo = intval($row_ctg->harga);
				$jlh_tbh = intval($row_ctg->subtotal);
				$kode_rap = $row_ctg->kode_rap;
				$tanggal_kendali = $row_ctg->tanggal_kendali;
				$jlh_krg = 0;
//kemungkinan
				$jumlah_tg= $jlh_tbh;
				if ($jlh_tbh == 0 || $hs_togo == 0) {
					$volume_tg=0;
				} else {
            		$volume_tg=$jlh_tbh/$hs_togo;//$jumlah_tg/$hs_togo;
				}
			
				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {
					$jumlah_cf="
					$jlh_tbh+
					(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp)";

					$volume_cf="
					case when ($jlh_tbh+(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp)) = 0 or
					$hs_togo = 0 or
					($jlh_tbh+(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp)) isnull or
					$hs_togo isnull
					then 0
					else
					($jlh_tbh+(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp))/
					$hs_togo
					end";

             		$trend="$jlh_tbh+
					(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp)-simpro_tbl_po2.jumlah_cb";

             		$sql_action_ctg="update simpro_tbl_po2 set 
             		volume_cost_tg=(case when $volume_tg is null then 0 else $volume_tg end),
             		hargasat_cost_tg='$hs_togo',
             		jumlah_cost_tg=(case when $jumlah_tg is null then 0 else $jumlah_tg end),
             		volume_cf=(case when $volume_cf is null then 0 else $volume_cf end),
             		jumlah_cf=(case when $jumlah_cf is null then 0 else $jumlah_cf end),
             		jlh_tambah='$jlh_tbh',
             		jlh_kurang='$jlh_krg',
             		trend=(case when $trend is null then 0 else $trend end) 
             		where proyek_id='$proyek_id' 
             		and tahap_tanggal_kendali='$tgl_rab' 
             		and kode_rap='$kode_rap'";

				} else {
					$volume_cf=0+0+0+$volume_tg;
					$jumlah_cf=0+0+0+$jumlah_tg;
					$trend=0-$jumlah_cf;	                
					$sql_action_ctg="insert into simpro_tbl_po2(proyek_id,tahap_tanggal_kendali,detail_material_id,detail_material_kode,detail_material_nama,detail_material_satuan,volume_ob,harga_sat_ob,jumlah_ob,volume_cash_td,jumlah_cash_td,volume_hutang,jumlah_hutang,volume_hp,jumlah_hp,volume_cost_td,jumlah_cost_td,volume_cost_tg,hargasat_cost_tg,jumlah_cost_tg,volume_cf,jumlah_cf,trend,volume_tot_hutang,total_hutang,vol_cash_hutang,jum_cash_hutang,volume_cb,hargasat_cb,jumlah_cb,jlh_tambah,jlh_kurang) values(
						'$proyek_id',
						'$tgl_rab',
						'$id_detail_material',
						'$kode_material',
						'$nama',
						'$satuan',
						'0','0','0','0','0','0','0','0','0','0','0',
						'$volume_tg',
						'$hs_togo',
						'$jumlah_tg',
						$volume_cf,
						$jumlah_cf,
						$trend,
						'0','0','0','0','0','0','0',
						'$jlh_tbh',
						'$jlh_krg'
						)";
				
				}

				$this->db->query($sql_action_ctg);
			}
		}

		$sql_ctd = "select
					a.detail_material_id,
					b.detail_material_kode,
					b.detail_material_nama,
					b.detail_material_satuan,
					sum(a.volume) as volume,
					sum(a.jumlah) as jumlah,
					a.kode_rap,
					extract(year from a.tanggal) as tahun,
					extract(month from a.tanggal) as bulan
					from
					simpro_tbl_cashtodate a
					join simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					where
					extract(year from a.tanggal) = '$thn' and
					extract(month from a.tanggal) = '$bln' and
					a.proyek_id = $proyek_id
					group by
					a.detail_material_id,
					b.detail_material_kode,
					b.detail_material_nama,
					b.detail_material_satuan,
					a.kode_rap,
					tahun,
					bulan";

		$q_ctd = $this->db->query($sql_ctd);

		if ($q_ctd->result()) {
			foreach ($q_ctd->result() as $row_ctd) {

				$detail_material_id = $row_ctd->detail_material_id;
				$detail_material_kode = $row_ctd->detail_material_kode;
				$nama = $row_ctd->detail_material_nama;
				$satuan = $row_ctd->detail_material_satuan;
				$volume_ctd = $row_ctd->volume;
				$jumlah_ctd = $row_ctd->jumlah;
				$kode_rap = $row_ctd->kode_rap;

				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {
					$volume_cost_td="($volume_ctd+
						simpro_tbl_po2.volume_hutang+simpro_tbl_po2.volume_hp)";
					$jumlah_cost_td="($jumlah_ctd+
						simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp)";
					
					$jumlah_tg="
						simpro_tbl_po2.jlh_tambah";

	                $volume_tg="simpro_tbl_po2.jlh_tambah/simpro_tbl_po2.hargasat_cost_tg";
	                
	                $jumlah_cf="((simpro_tbl_po2.jumlah_cb-(
	                $jumlah_ctd+
	                simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp))+simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+
					($jumlah_ctd+simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp)";

	                $volume_cf="(((simpro_tbl_po2.jumlah_cb-(
	                	$jumlah_ctd+
	                	simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp))+simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+
						($jumlah_ctd
						+simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp))/simpro_tbl_po2.hargasat_cost_tg";

	                $trend="((simpro_tbl_po2.jumlah_cb-(
	                	$jumlah_ctd+
	                	simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp))+simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+
						($jumlah_ctd+
						simpro_tbl_po2.jumlah_hutang+simpro_tbl_po2.jumlah_hp)- simpro_tbl_po2.jumlah_cb";

	                $sql_action_ctd="update simpro_tbl_po2 
		                set detail_material_nama='$nama',
		                detail_material_satuan='$satuan',
		                volume_cash_td='$volume_ctd',
		                jumlah_cash_td='$jumlah_ctd',
		                uraian='$uraian',
		                volume_cost_td=(case when $volume_cost_td is null then 0 else $volume_cost_td end),
		                jumlah_cost_td=(case when $jumlah_cost_td is null then 0 else $jumlah_cost_td end),
		                volume_cf=(case when $volume_cf is null then 0 else $volume_cf end),
		                jumlah_cf=(case when $jumlah_cf is null then 0 else $jumlah_cf end),
		                trend=(case when $trend is null then 0 else $trend end),
		                jumlah_cost_tg=(case when $jumlah_tg is null then 0 else $jumlah_tg end),
		                volume_cost_tg=(case when $volume_tg is null then 0 else $volume_tg end)  
		                where proyek_id=$proyek_id
		                and kode_rap='$kode_rap' 
		                and tahap_tanggal_kendali='$tgl_rab'";
				} else {
					$volume_cost_td=$volume_ctd+0+0;
					$jumlah_cost_td=$jumlah_ctd+0+0;
					$volume_cf=$volume_ctd+0+0+0;
					$jumlah_cf=0-$jumlah_ctd;
					$trend=0+$jumlah_cf;
	                $jlh_togo=0-$jumlah_ctd;

					$sql_action_ctd="insert into simpro_tbl_po2(
						proyek_id,tahap_tanggal_kendali,detail_material_id,detail_material_kode,detail_material_nama,detail_material_satuan,volume_ob,harga_sat_ob,jumlah_ob,volume_cash_td,jumlah_cash_td,volume_hutang,jumlah_hutang,volume_hp,jumlah_hp,volume_cost_td,jumlah_cost_td,volume_cost_tg,hargasat_cost_tg,jumlah_cost_tg,volume_cf,jumlah_cf,trend,volume_tot_hutang,total_hutang,vol_cash_hutang,jum_cash_hutang,volume_cb,hargasat_cb,jumlah_cb,pilihan,uraian) values(
						'$proyek_id',
						'$tgl_rab',
						'$id_detail_material',
						'$kode_material',
						'$nama',
						'$satuan',
						'0','0','0',
						'$volume_ctd',
						'$jumlah_ctd',
						'0','0','0','0',
						$volume_cost_td,
						$jumlah_cost_td,
						'0','0',
						'$jlh_togo',
						$volume_cf,
						$jumlah_cf,
						$trend,
						'0','0','0','0','0','0','0',
						'-',
						'-')";
				}

				$this->db->query($sql_action_ctd);
			}
		}

		$sql_hutang = "select
					a.detail_material_id,
					b.detail_material_kode,
					b.detail_material_nama,
					b.detail_material_satuan,
					sum(a.volume) as volume,
					sum(a.jumlah) as jumlah,
					a.kode_rap,
					extract(year from a.tanggal) as tahun,
					extract(month from a.tanggal) as bulan
					from
					simpro_tbl_hutangonkeu a
					join simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					where
					extract(year from a.tanggal) = '$thn' and
					extract(month from a.tanggal) = '$bln' and
					a.proyek_id = $proyek_id
					group by
					a.detail_material_id,
					b.detail_material_kode,
					b.detail_material_nama,
					b.detail_material_satuan,
					a.kode_rap,
					tahun,
					bulan";

		$q_hutang = $this->db->query($sql_hutang);

		if ($q_hutang->result()) {
			foreach ($q_hutang->result() as $row_hutang) {

				$detail_material_id = $row_hutang->detail_material_id;
				$detail_material_kode = $row_hutang->detail_material_kode;
				$nama = $row_hutang->detail_material_nama;
				$satuan = $row_hutang->detail_material_satuan;
				$volume_hk = $row_hutang->volume;
				$jumlah_hk = $row_hutang->jumlah;
				$kode_rap = $row_hutang->kode_rap;

				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {
					$volume_cost_td="(simpro_tbl_po2.volume_cash_td+
						$volume_hk+
						simpro_tbl_po2.volume_hp)";
					$jumlah_cost_td="(simpro_tbl_po2.jumlah_cash_td+
						$jumlah_hk+
						simpro_tbl_po2.jumlah_hp)";
					
	                $jumlah_tg="simpro_tbl_po2.jlh_tambah";
	                $volume_tg="simpro_tbl_po2.jlh_tambah/simpro_tbl_po2.hargasat_cost_tg";
	                
					$jumlah_cf="((simpro_tbl_po2.jumlah_cb-(simpro_tbl_po2.jumlah_cash_td+
						$jumlah_hk+
						simpro_tbl_po2.jumlah_hp))+simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+(simpro_tbl_po2.jumlah_cash_td+
						$jumlah_hk+
						simpro_tbl_po2.jumlah_hp)";//"";
					$volume_cf="(((simpro_tbl_po2.jumlah_cb-(simpro_tbl_po2.jumlah_cash_td+
						$jumlah_hk+
						simpro_tbl_po2.jumlah_hp))+simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+(simpro_tbl_po2.jumlah_cash_td+
						$jumlah_hk+
						simpro_tbl_po2.jumlah_hp))/simpro_tbl_po2.hargasat_cost_tg";
	                $trend="((simpro_tbl_po2.jumlah_cb-(simpro_tbl_po2.jumlah_cash_td+
	                	$jumlah_hk+
	                	simpro_tbl_po2.jumlah_hp))+simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+(simpro_tbl_po2.jumlah_cash_td+
						$jumlah_hk+
						simpro_tbl_po2.jumlah_hp)-simpro_tbl_po2.jumlah_cb";
	                $sql_action_hutang="update simpro_tbl_po2 set 
		                detail_material_nama='$nama',
		                volume_hutang='$volume_hk',
		                jumlah_hutang='$jumlah_hk',
		                volume_cost_td=(case when $volume_cost_td is null then 0 else $volume_cost_td end),
		                jumlah_cost_td=(case when $jumlah_cost_td is null then 0 else $jumlah_cost_td end),
		                volume_cf=(case when $volume_cf is null then 0 else $volume_cf end),
		                jumlah_cf=(case when $jumlah_cf is null then 0 else $jumlah_cf end),
		                trend=(case when $trend is null then 0 else $trend end),
		                jumlah_cost_tg=(case when $jumlah_tg is null then 0 else $jumlah_tg end) ,
		                volume_cost_tg=(case when $volume_tg is null then 0 else $volume_tg end)  
		                where proyek_id='$proyek_id' 
		                and tahap_tanggal_kendali='$tgl_rab' 
		                and kode_rap='$kode_rap'";
				} else {
					$volume_cost_td=0+$volume_hk+0;
					$jumlah_cost_td=0+$jumlah_hk+0;
					$volume_cf=0+$volume_hk+0+0;
					$jumlah_cf=0-$jumlah_hk;
					$trend=0+$jumlah_cf;
	                $jlh_togo=0-$jumlah_hk;
					$sql_action_hutang="insert into simpro_tbl_po2(proyek_id,tahap_tanggal_kendali,detail_material_id,detail_material_kode,detail_material_nama,detail_material_satuan,volume_ob,harga_sat_ob,jumlah_ob,volume_cash_td,jumlah_cash_td,volume_hutang,jumlah_hutang,volume_hp,jumlah_hp,volume_cost_td,jumlah_cost_td,volume_cost_tg,hargasat_cost_tg,jumlah_cost_tg,volume_cf,jumlah_cf,trend,volume_tot_hutang,total_hutang,vol_cash_hutang,jum_cash_hutang,volume_cb,hargasat_cb,jumlah_cb) values(
						'$proyek_id',
						'$tgl_rab',
						'$id_detail_material',
						'$kode_material',
						'$nama',
						'$satuan',
						'0','0','0','0','0',
						'$volume_hk',
						'$jumlah_hk',
						'0','0',
						$volume_cost_td,
						$jumlah_cost_td,
						'0','0',
						'$jlh_togo',
						$volume_cf,
						$jumlah_cf,
						$trend,
						'0','0','0','0','0','0','0')";
				}

				$this->db->query($sql_action_hutang);
			}
		}

		$sql_antisipasi = "select
						a.detail_material_id,
						b.detail_material_kode,
						b.detail_material_nama,
						b.detail_material_satuan,
						sum(a.volume) as volume,
						sum(a.jumlah) as jumlah,
						a.kode_rap,
						extract(year from a.tanggal) as tahun,
						extract(month from a.tanggal) as bulan
						from
						simpro_tbl_hutang_proses a
						join simpro_tbl_detail_material b
						on a.detail_material_id = b.detail_material_id
						where
						extract(year from a.tanggal) = '$thn' and
						extract(month from a.tanggal) = '$bln' and
						a.proyek_id = $proyek_id
						group by
						a.detail_material_id,
						b.detail_material_kode,
						b.detail_material_nama,
						b.detail_material_satuan,
						a.kode_rap,
						tahun,
						bulan";

		$q_antisipasi = $this->db->query($sql_antisipasi);

		if ($q_antisipasi->result()) {
			foreach ($q_antisipasi->result() as $row_antisipasi) {

				$detail_material_id = $row_antisipasi->detail_material_id;
				$detail_material_kode = $row_antisipasi->detail_material_kode;
				$nama = $row_antisipasi->detail_material_nama;
				$satuan = $row_antisipasi->detail_material_satuan;
				$volume_an = $row_antisipasi->volume;
				$jumlah_an = $row_antisipasi->jumlah;
				$kode_rap = $row_antisipasi->kode_rap;

				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {
					$volume_cost_td="(simpro_tbl_po2.volume_cash_td+simpro_tbl_po2.volume_hutang+
						$volume_an)";
					$jumlah_cost_td="(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+
						$jumlah_an)";
	                
					$jumlah_tg="simpro_tbl_po2.jlh_tambah";
	                $volume_tg="simpro_tbl_po2.jlh_tambah/simpro_tbl_po2.hargasat_cost_tg";
	                	                
					$jumlah_cf="((simpro_tbl_po2.jumlah_cb-(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+
					$jumlah_an))+
					simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+
					$jumlah_an)";//"";
					$volume_cf="(((simpro_tbl_po2.jumlah_cb-(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+
						$jumlah_an))+
						simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+
						$jumlah_an))/
						simpro_tbl_po2.hargasat_cost_tg";
	                $trend="((simpro_tbl_po2.jumlah_cb-(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+
	                	$jumlah_an))+
						simpro_tbl_po2.jlh_tambah+simpro_tbl_po2.jlh_kurang)+(simpro_tbl_po2.jumlah_cash_td+simpro_tbl_po2.jumlah_hutang+
						$jumlah_an)-
						simpro_tbl_po2.jumlah_cb";
	                	                
					$sql_action_antisipasi="update simpro_tbl_po2 set 
						detail_material_nama='$nama',
						volume_hp='$volume_an',
						jumlah_hp='$jumlah_an',
						volume_cost_td=(case when $volume_cost_td is null then 0 else $volume_cost_td end),
						jumlah_cost_td=(case when $jumlah_cost_td is null then 0 else $jumlah_cost_td end),
						volume_cf=(case when $volume_cf is null then 0 else $volume_cf end),
						jumlah_cf=(case when $jumlah_cf is null then 0 else $jumlah_cf end),
						trend=(case when $trend is null then 0 else $trend end),
						jumlah_cost_tg=(case when $jumlah_tg is null then 0 else $jumlah_tg end) ,
						volume_cost_tg=(case when $volume_tg is null then 0 else $volume_tg end)  
						where proyek_id='$proyek_id' 
						and tahap_tanggal_kendali='$tgl_rab' 
						and kode_rap='$kode_rap'";
				} else {
					$volume_cost_td=0+0+$volume_an;
					$jumlah_cost_td=0+0+$jumlah_an;
					$volume_cf=0+0+$volume_an+0;
					$jumlah_cf=0-$jumlah_an;
					$trend=0+$jumlah_cf;
	                $jlh_togo=0-$jumlah_an;
					$sql_action_antisipasi="insert into simpro_tbl_po2(proyek_id,tahap_tanggal_kendali,detail_material_id,detail_material_kode,detail_material_nama,detail_material_satuan,volume_ob,harga_sat_ob,jumlah_ob,volume_cash_td,jumlah_cash_td,volume_hutang,jumlah_hutang,volume_hp,jumlah_hp,volume_cost_td,jumlah_cost_td,volume_cost_tg,hargasat_cost_tg,jumlah_cost_tg,volume_cf,jumlah_cf,trend,volume_tot_hutang,total_hutang,vol_cash_hutang,jum_cash_hutang,volume_cb,hargasat_cb,jumlah_cb) values(
						'$proyek_id',
						'$tgl_rab',
						'$id_detail_material',
						'$kode_material',
						'$nama',
						'$satuan',
						'0','0','0','0','0','0','0',
						'$volume_an',
						'$jumlah_an',
						$volume_cost_td,
						$jumlah_cost_td,
						'0','0',
						'$jlh_togo',
						$volume_cf,
						$jumlah_cf,
						$trend,
						'0','0','0','0','0','0','0')";	
				}

				$this->db->query($sql_action_antisipasi);
			}
		}

		$sql_cash_hutang = "select
						a.detail_material_id,
						b.detail_material_kode,
						b.detail_material_nama,
						b.detail_material_satuan,
						sum(a.volume) as volume,
						sum(a.jumlah) as jumlah,
						a.kode_rap,
						extract(year from a.tanggal) as tahun,
						extract(month from a.tanggal) as bulan
						from
						simpro_tbl_cash_hutang a
						join simpro_tbl_detail_material b
						on a.detail_material_id = b.detail_material_id
						where
						extract(year from a.tanggal) = '$thn' and
						extract(month from a.tanggal) = '$bln' and
						a.proyek_id = $proyek_id
						group by
						a.detail_material_id,
						b.detail_material_kode,
						b.detail_material_nama,
						b.detail_material_satuan,
						a.kode_rap,
						tahun,
						bulan";

		$q_cash_hutang = $this->db->query($sql_cash_hutang);

		if ($q_cash_hutang->result()) {
			foreach ($q_cash_hutang->result() as $row_cash_hutang) {

				$detail_material_id = $row_cash_hutang->detail_material_id;
				$detail_material_kode = $row_cash_hutang->detail_material_kode;
				$nama = $row_cash_hutang->detail_material_nama;
				$satuan = $row_cash_hutang->detail_material_satuan;
				$volume_ch = $row_cash_hutang->volume;
				$jumlah_ch = $row_cash_hutang->jumlah;
				$kode_rap = $row_cash_hutang->kode_rap;

				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {
					$sql_action_ch="update simpro_tbl_po2 set 
						detail_material_nama='$nama',
						detail_material_satuan='$satuan',
						vol_cash_hutang='$volume_ch',
						jum_cash_hutang='$jumlah_ch' 
						where proyek_id='$proyek_id' 
						and tahap_tanggal_kendali='$tgl_rab' 
						and kode_rap='$kode_rap'";
				} else {
					$sql_action_ch="insert into simpro_tbl_po2(proyek_id,tahap_tanggal_kendali,detail_material_id,detail_material_kode,detail_material_nama,detail_material_satuan,volume_ob,harga_sat_ob,jumlah_ob,volume_cash_td,jumlah_cash_td,volume_hutang,jumlah_hutang,volume_hp,jumlah_hp,volume_cost_td,jumlah_cost_td,volume_cost_tg,hargasat_cost_tg,jumlah_cost_tg,volume_cf,jumlah_cf,trend,volume_tot_hutang,total_hutang,vol_cash_hutang,jum_cash_hutang,volume_cb,hargasat_cb,jumlah_cb) values(
						'$proyek_id',
						'$tgl_rab',
						'$id_detail_material',
						'$kode_material',
						'$nama',
						'$satuan',
						'0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0',
						'$volume_ch',
						'$jumlah_ch',
						'0','0','0')";
				}

				$this->db->query($sql_action_ch);
			}
		}

		$sql_rpbk = "select 
					sum(a.volume_rencana_pbk) as volume_rencana_pbk,
					a.detail_material_id,
					a.detail_material_kode,
					b.detail_material_nama,
					b.detail_material_satuan,
					COALESCE(a.komposisi_harga_satuan_kendali,0) as komposisi_harga_satuan_kendali,
					COALESCE(a.total_rencana_pbk,0) as total_rencana_pbk,
					a.kode_rap 
					from simpro_tbl_rpbk a
					join simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					where proyek_id = $proyek_id
					and tahap_tanggal_kendali='$tgl_rab' 
					group by a.detail_material_id,
					a.detail_material_kode,
					b.detail_material_nama,
					b.detail_material_satuan,
					a.komposisi_harga_satuan_kendali,
					a.total_rencana_pbk,
					a.kode_rap ";

		$q_rpbk = $this->db->query($sql_rpbk);

		if ($q_rpbk->result()) {
			foreach ($q_rpbk->result() as $row_rpbk) {

				$detail_material_id = $row_rpbk->detail_material_id;
				$detail_material_kode = $row_rpbk->detail_material_kode;
				$nama = $row_rpbk->detail_material_nama;
				$satuan = $row_rpbk->detail_material_satuan;
				$hs_rc = $row_rpbk->komposisi_harga_satuan_kendali;
				$jumlah_rc = $row_rpbk->total_rencana_pbk;
				$kode_rap = $row_rpbk->kode_rap;

				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {
					$sql_action_rpbk="update simpro_tbl_po2 set 
						volume_rencana=$hs_rc,
						total_volume_rencana=$jumlah_rc
						where proyek_id=$proyek_id 
						and tahap_tanggal_kendali='$tgl_rab' 
						and kode_rap='$kode_rap'";
				} else {
					$sql_action_rpbk="insert into simpro_tbl_po2(proyek_id,tahap_tanggal_kendali,detail_material_id,detail_material_kode,detail_material_nama,detail_material_satuan,volume_ob,harga_sat_ob,jumlah_ob,volume_cash_td,jumlah_cash_td,volume_hutang,jumlah_hutang,volume_hp,jumlah_hp,volume_cost_td,jumlah_cost_td,volume_cost_tg,hargasat_cost_tg,jumlah_cost_tg,volume_cf,jumlah_cf,trend,volume_tot_hutang,total_hutang,vol_cash_hutang,jum_cash_hutang,volume_cb,hargasat_cb,jumlah_cb,volume_rencana,total_volume_rencana) values(
						'$proyek_id',
						'$tgl_rab',
						'$detail_material_id',
						'$detail_material_kode',
						'$nama',
						'$satuan',
						'0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0',
						'$hs_rc',
						'$jumlah_rc')";
				}

				$this->db->query($sql_action_rpbk);
			}
		}

		$q_ctg_2 = $this->db->query($sql_ctg);

		if ($q_ctg_2->result()) {
			foreach ($q_ctg_2->result() as $row_ctg_2) {

				$id_detail_material = $row_ctg_2->id_detail_material;
				$kode_material = $row_ctg_2->kode_material;
				$nama = $row_ctg_2->detail_material_nama;
				$satuan = $row_ctg_2->detail_material_satuan;
				$koefisien = $row_ctg_2->koefisien;
				$hs_togo = $row_ctg_2->harga;
				$subtotal = $row_ctg_2->subtotal;
				$kode_rap = $row_ctg_2->kode_rap;
				$tanggal_kendali = $row_ctg_2->tanggal_kendali;

				$sqlctd1="select sum(jumlah) as jumlah from simpro_tbl_cashtodate where proyek_id=$proyek_id and tanggal<'$tgl_rab' and kode_rap='$kode_rap'";
	            $q_ctd1 = $this->db->query($sqlctd1);
	            $row_ctd1 = $q_ctd1->row();

	            $sqlctd2="select sum(jumlah) as jumlah from simpro_tbl_hutangonkeu where proyek_id=$proyek_id and tanggal<'$tgl_rab' and kode_rap='$kode_rap'";
	            $q_ctd2 = $this->db->query($sqlctd2);
	            $row_ctd2 = $q_ctd2->row();

	            $sqlctd3="select sum(jumlah) as jumlah from simpro_tbl_hutang_proses where proyek_id=$proyek_id and tanggal<'$tgl_rab' and kode_rap='$kode_rap'";
	            $q_ctd3 = $this->db->query($sqlctd3);
	            $row_ctd3 = $q_ctd3->row();

	            $cost_bk_lalu=$row_ctd1->jumlah+$row_ctd2->jumlah+$row_ctd3->jumlah;

				$jumlah_tg= $koefisien*$hs_togo; //"(($koefisien*$hs_togo)-$cost_bk_lalu)";
            	$volume_tg= $koefisien; //"(($koefisien*$hs_togo)-$cost_bk_lalu)/$hs_togo";

				$ket = $this->cek_po2($proyek_id,$tgl_rab,$kode_rap);
				if ($ket == 'ada') {

					$sql_action_ctg2="update simpro_tbl_po2 set 
					volume_cost_tg=(case when $volume_tg is null then 0 else $volume_tg end),
					jumlah_cost_tg=(case when $jumlah_tg is null then 0 else $jumlah_tg end) 
					where proyek_id='$proyek_id' 
					and tahap_tanggal_kendali='$tgl_rab' 
					and kode_rap='$kode_rap'";
				} else {
					$volume_cf=0+0+0+$volume_tg;
					$jumlah_cf=0+0+0+$jumlah_tg;
					$trend=0-$jumlah_cf;
	                
					$sql_action_ctg2="insert into simpro_tbl_po2(proyek_id,tahap_tanggal_kendali,detail_material_id,detail_material_kode,detail_material_nama,detail_material_satuan,volume_ob,harga_sat_ob,jumlah_ob,volume_cash_td,jumlah_cash_td,volume_hutang,jumlah_hutang,volume_hp,jumlah_hp,volume_cost_td,jumlah_cost_td,volume_cost_tg,hargasat_cost_tg,jumlah_cost_tg,volume_cf,jumlah_cf,trend,volume_tot_hutang,total_hutang,vol_cash_hutang,jum_cash_hutang,volume_cb,hargasat_cb,jumlah_cb,jlh_tambah,jlh_kurang) values(
						'$proyek_id',
						'$tgl_rab',
						$id_detail_material,
						'$kode_material',
						'$nama',
						'$satuan',
						'0','0','0','0','0','0','0','0','0','0','0',
						$volume_tg,
						$hs_togo,
						$jumlah_tg,
						'0','0','0','0','0','0','0','0','0','0','0','0')";
			
				}

				// echo $sql_action_ctg2;
				// $this->db->query($sql_action_ctg2);
			}
		}

		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		}
		else
		{
		    $this->db->trans_commit();
		}
	}

	function e_getTotalDayGlobal($tahun,$bulan){
        if ($bulan==2){
            if ($tahun%4==0){
                $hari = 29;
            }else if($tahun%4!=0){
                $hari = 28;
            }
        }else if(($bulan==4 || $bulan==6 || $bulan==9 || $bulan==11)){
            $hari = 30;
        }else{
            $hari = 31;
        }
        return $hari;
    }

	function cek_po2($proyek_id,$tgl_rab,$kode_rap)
	{
		$sql_po2 = "select 
				kode_rap 
				from simpro_tbl_po2 
				where 
				proyek_id=$proyek_id
				and tahap_tanggal_kendali='$tgl_rab' 
				and kode_rap='$kode_rap' 
				group by 
				kode_rap";

		$q = $this->db->query($sql_po2);

		if ($q->result()) {
			$ket = 'ada';
		} else {
			$ket = 'kosong';
		}

		return $ket;
	}

	// add by dena

	function insertdetailsch($info,$data){
		switch ($info) {
			case 'proyek':
				$this->db->insert('simpro_tbl_sch_proyek_parent',$data);
			break;
			case 'alat':
				$this->db->insert('simpro_tbl_sch_proyek_parent_alat',$data);
			break;
			case 'bahan':
				$this->db->insert('simpro_tbl_sch_proyek_parent_bahan',$data);
			break;
			case 'person':
				$this->db->insert('simpro_tbl_sch_proyek_parent_person',$data);
			break;
			case 'peralatan':
				$this->db->insert('simpro_tbl_guna_alat_parent',$data);
			break;
		}
	}

	function updatedetailsch($info,$data, $id_sch_proyek, $tahap_kendali_id, $minggu_ke){
		switch ($info) {
			case 'proyek':
				$this->db->where('id_sch_proyek',$id_sch_proyek);
				$this->db->where('tahap_kendali_id',$tahap_kendali_id);
				$this->db->where('minggu_ke',$minggu_ke);
				$this->db->update('simpro_tbl_sch_proyek_parent',$data);
			break;
			case 'alat':
				$this->db->where('id_sch_proyek',$id_sch_proyek);
				$this->db->where('tahap_kendali_id',$tahap_kendali_id);
				$this->db->where('minggu_ke',$minggu_ke);
				$this->db->update('simpro_tbl_sch_proyek_parent_alat',$data);
			break;
			case 'bahan':
				$this->db->where('id_sch_proyek',$id_sch_proyek);
				$this->db->where('tahap_kendali_id',$tahap_kendali_id);
				$this->db->where('minggu_ke',$minggu_ke);
				$this->db->update('simpro_tbl_sch_proyek_parent_bahan',$data);
			break;
			case 'person':
				$this->db->where('id_sch_proyek',$id_sch_proyek);
				$this->db->where('tahap_kendali_id',$tahap_kendali_id);
				$this->db->where('minggu_ke',$minggu_ke);
				$this->db->update('simpro_tbl_sch_proyek_parent_person',$data);
			break;
			case 'peralatan':
				$this->db->where('id_guna_alat',$id_sch_proyek);
				$this->db->where('id_analisa_asat',$tahap_kendali_id);
				$this->db->where('minggu_ke',$minggu_ke);
				$this->db->update('simpro_tbl_guna_alat_parent',$data);
			break;
		}
	}

	function add_sch_project($info,$data){
		switch ($info) {
			case 'proyek':
				$this->db->insert('simpro_tbl_sch_proyek',$data);
			break;
			case 'alat':
				$this->db->insert('simpro_tbl_sch_proyek_alat',$data);
			break;
			case 'bahan':
				$this->db->insert('simpro_tbl_sch_proyek_bahan',$data);
			break;
			case 'person':
				$this->db->insert('simpro_tbl_sch_proyek_person',$data);
			break;
			case 'peralatan':
				$this->db->insert('simpro_tbl_guna_alat',$data);
			break;
		}
	}

	function update_sch_project($info,$proyek_id,$tahap_kendali_id,$data)
	{
		switch ($info) {
			case 'proyek':
				$this->db->where('proyek_id',$proyek_id);
				$this->db->where('tahap_kendali_id',$tahap_kendali_id);
				$this->db->update('simpro_tbl_sch_proyek',$data);
			break;
			case 'alat':
				$this->db->where('proyek_id',$proyek_id);
				$this->db->where('tahap_kendali_id',$tahap_kendali_id);
				$this->db->update('simpro_tbl_sch_proyek_alat',$data);
			break;
			case 'bahan':
				$this->db->where('proyek_id',$proyek_id);
				$this->db->where('tahap_kendali_id',$tahap_kendali_id);
				$this->db->update('simpro_tbl_sch_proyek_bahan',$data);
			break;
			case 'person':
				$this->db->where('proyek_id',$proyek_id);
				$this->db->where('tahap_kendali_id',$tahap_kendali_id);
				$this->db->update('simpro_tbl_sch_proyek_person',$data);
			break;
			case 'peralatan':
				$this->db->where('proyek_id',$proyek_id);
				$this->db->where('id_analisa_asat',$tahap_kendali_id);
				$this->db->update('simpro_tbl_guna_alat',$data);
			break;
		}
	}

	function deletesch($info,$proyek_id, $tahap_kendali_id)
	{
		switch ($info) {
			case 'proyek':
				$this->db->where('proyek_id', $proyek_id);
				$this->db->where('tahap_kendali_id', $tahap_kendali_id);		
				$this->db->delete('simpro_tbl_sch_proyek');
				echo $this->db->last_query();
				echo "<br>";

				$sch_proyek_parent['id_sch_proyek'] = $proyek_id;
				$sch_proyek_parent['tahap_kendali_id'] = $tahap_kendali_id;
				$this->db->delete('simpro_tbl_sch_proyek_parent', $sch_proyek_parent);
				echo $this->db->last_query();
			break;
			case 'alat':
				$this->db->where('proyek_id', $proyek_id);
				$this->db->where('tahap_kendali_id', $tahap_kendali_id);		
				$this->db->delete('simpro_tbl_sch_proyek_alat');
				echo $this->db->last_query();
				echo "<br>";

				$sch_proyek_parent['id_sch_proyek'] = $proyek_id;
				$sch_proyek_parent['tahap_kendali_id'] = $tahap_kendali_id;
				$this->db->delete('simpro_tbl_sch_proyek_parent_alat', $sch_proyek_parent);
				echo $this->db->last_query();
			break;
			case 'bahan':
				$this->db->where('proyek_id', $proyek_id);
				$this->db->where('tahap_kendali_id', $tahap_kendali_id);		
				$this->db->delete('simpro_tbl_sch_proyek_bahan');
				echo $this->db->last_query();
				echo "<br>";

				$sch_proyek_parent['id_sch_proyek'] = $proyek_id;
				$sch_proyek_parent['tahap_kendali_id'] = $tahap_kendali_id;
				$this->db->delete('simpro_tbl_sch_proyek_parent_bahan', $sch_proyek_parent);
				echo $this->db->last_query();
			break;
			case 'person':
				$this->db->where('proyek_id', $proyek_id);
				$this->db->where('tahap_kendali_id', $tahap_kendali_id);		
				$this->db->delete('simpro_tbl_sch_proyek_person');
				echo $this->db->last_query();
				echo "<br>";

				$sch_proyek_parent['id_sch_proyek'] = $proyek_id;
				$sch_proyek_parent['tahap_kendali_id'] = $tahap_kendali_id;
				$this->db->delete('simpro_tbl_sch_proyek_parent_person', $sch_proyek_parent);
				echo $this->db->last_query();
			break;
			case 'peralatan':
				$this->db->where('proyek_id', $proyek_id);
				$this->db->where('id_analisa_asat', $tahap_kendali_id);		
				$this->db->delete('simpro_tbl_guna_alat');
				echo $this->db->last_query();
				echo "<br>";

				$sch_proyek_parent['id_guna_alat'] = $proyek_id;
				$sch_proyek_parent['id_analisa_asat'] = $tahap_kendali_id;
				$this->db->delete('simpro_tbl_guna_alat_parent', $sch_proyek_parent);
				echo $this->db->last_query();
			break;
		}
	}

	function deleteschparent($info,$id)
	{
		switch ($info) {
			case 'proyek':
				$this->db->where('id', $id);
				$this->db->delete('simpro_tbl_sch_proyek_parent');
			break;
			case 'alat':
				$this->db->where('id', $id);
				$this->db->delete('simpro_tbl_sch_proyek_parent_alat');
			break;
			case 'bahan':
				$this->db->where('id', $id);
				$this->db->delete('simpro_tbl_sch_proyek_parent_bahan');
			break;
			case 'person':
				$this->db->where('id', $id);
				$this->db->delete('simpro_tbl_sch_proyek_parent_person');
			break;
			case 'peralatan':
				$this->db->where('id', $id);
				$this->db->delete('simpro_tbl_guna_alat_parent');
			break;
		}
	}

	function get_detail_unit_project($proyek_id){
		$query_bobot = $this->db->query("select * from simpro_tbl_sch_proyek where proyek_id='$proyek_id' order by id");
		$result = $query_bobot->result_array();

		return $result;
	}

	function get_jml_sch_proyek($info,$proyek_id){ //get_jml_sch_proyek

		switch ($info) {
			case 'proyek':
				$q = "select SUM(bobot) as bobot from simpro_tbl_sch_proyek where proyek_id='$proyek_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			case 'alat':
				$q = "select SUM(bobot) as bobot from simpro_tbl_sch_proyek_alat where proyek_id='$proyek_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			case 'bahan':
				$q = "select SUM(bobot) as bobot from simpro_tbl_sch_proyek_bahan where proyek_id='$proyek_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			case 'person':
				$q = "select SUM(bobot) as bobot from simpro_tbl_sch_proyek_person where proyek_id='$proyek_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			case 'peralatan':
				$q = "select SUM(jumlah) as bobot from simpro_tbl_guna_alat where proyek_id='$proyek_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			default:
				echo "Access Forbidden";
			break;
		}


	}

	function get_jml_unit_project($info,$proyek_id){ //get_jml_unit_project
		switch ($info) {
			case 'proyek':
				$query_bobot = $this->db->query("select * from simpro_tbl_sch_proyek where proyek_id='$proyek_id' order by id");
				$result = $query_bobot->result_array();

				return $result;
			break;
			case 'alat':
				$query_bobot = $this->db->query("select * from simpro_tbl_sch_proyek_alat where proyek_id='$proyek_id' order by id");
				$result = $query_bobot->result_array();

				return $result;
			break;
			case 'bahan':
				$query_bobot = $this->db->query("select * from simpro_tbl_sch_proyek_bahan where proyek_id='$proyek_id' order by id");
				$result = $query_bobot->result_array();

				return $result;
			break;
			case 'person':
				$query_bobot = $this->db->query("select * from simpro_tbl_sch_proyek_person where proyek_id='$proyek_id' order by id");
				$result = $query_bobot->result_array();

				return $result;
			break;
			case 'peralatan':
				$query_bobot = $this->db->query("select * from simpro_tbl_guna_alat where proyek_id='$proyek_id' order by id");
				$result = $query_bobot->result_array();

				return $result;
			break;
			default:
				echo "Access Forbidden";
			break;
		}

	}

	function get_jml_bobot_per_unit($info,$proyek_id,$tahap_kendali_id){ //get_jml_bobot_per_unit
		switch ($info) {
			case 'proyek':
				$q = "select SUM(bobot_parent) as bobot from simpro_tbl_sch_proyek_parent where id_sch_proyek='$proyek_id' and tahap_kendali_id='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			case 'alat':
				$q = "select SUM(bobot_parent) as bobot from simpro_tbl_sch_proyek_parent_alat where id_sch_proyek='$proyek_id' and tahap_kendali_id='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			case 'bahan':
				$q = "select SUM(bobot_parent) as bobot from simpro_tbl_sch_proyek_parent_bahan where id_sch_proyek='$proyek_id' and tahap_kendali_id='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			case 'person':
				$q = "select SUM(bobot_parent) as bobot from simpro_tbl_sch_proyek_parent_person where id_sch_proyek='$proyek_id' and tahap_kendali_id='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			case 'peralatan':
				$q = "select SUM(jumlah_parent) as bobot from simpro_tbl_guna_alat_parent where id_guna_alat='$proyek_id' and id_analisa_asat='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['bobot'];
				return 0;
			break;
			default:
				echo "Access Forbidden";
			break;
		}

	}

	function get_minggu_awal_per_input_bobot_unit($info,$id_sch_proyek, $tahap_kendali_id){ //get_minggu_awal_per_input_bobot_unit
		switch ($info) {
			case 'proyek':
				$q = "select * from simpro_tbl_sch_proyek_parent where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id' order by tgl_sch_parent ASC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;

			break;
			case 'alat':
				$q = "select * from simpro_tbl_sch_proyek_parent_alat where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id' order by tgl_sch_parent ASC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;

			break;
			case 'bahan':
				$q = "select * from simpro_tbl_sch_proyek_parent_bahan where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id' order by tgl_sch_parent ASC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;

			break;
			case 'person':
				$q = "select * from simpro_tbl_sch_proyek_parent_person where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id' order by tgl_sch_parent ASC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;
			break;
			case 'peralatan':
				$q = "select * from simpro_tbl_guna_alat_parent where id_guna_alat='$id_sch_proyek' and id_analisa_asat='$tahap_kendali_id' order by tgl_sch_parent ASC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;
			break;
			default:
				echo "Access Forbidden";
			break;
		}

	}
	
	function get_minggu_akhir_per_input_boot_unit($info,$id_sch_proyek){ //get_minggu_akhir_per_input_boot_unit
		switch ($info) {
			case 'proyek':
				$q = "select * from simpro_tbl_sch_proyek_parent where id_sch_proyek='$id_sch_proyek' order by tgl_sch_parent DESC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;
			break;
			case 'alat':
				$q = "select * from simpro_tbl_sch_proyek_parent_alat where id_sch_proyek='$id_sch_proyek' order by tgl_sch_parent DESC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;
			break;
			case 'bahan':
				$q = "select * from simpro_tbl_sch_proyek_parent_bahan where id_sch_proyek='$id_sch_proyek' order by tgl_sch_parent DESC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;
			break;
			case 'person':
				$q = "select * from simpro_tbl_sch_proyek_parent_person where id_sch_proyek='$id_sch_proyek' order by tgl_sch_parent DESC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;
			break;
			case 'peralatan':
				$q = "select * from simpro_tbl_guna_alat_parent where id_guna_alat='$id_sch_proyek' order by tgl_sch_parent DESC";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data['tgl_sch_parent'];
				return 0;
			break;
			default:
				echo "Access Forbidden";
			break;
		}

	}

	function get_minggu_awal_per_unit($info,$id_sch_proyek,$tahap_kendali_id){ //get_minggu_awal_per_unit
		switch ($info) {
			case 'proyek':
				$q = "select * from simpro_tbl_sch_proyek_parent where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id' order by tgl_sch_parent";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			case 'alat':
				$q = "select * from simpro_tbl_sch_proyek_parent_alat where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id' order by tgl_sch_parent";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			case 'bahan':
				$q = "select * from simpro_tbl_sch_proyek_parent_bahan where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id' order by tgl_sch_parent";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			case 'person':
				$q = "select * from simpro_tbl_sch_proyek_parent_person where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id' order by tgl_sch_parent";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			case 'peralatan':
				$q = "select * from simpro_tbl_guna_alat_parent where id_guna_alat='$id_sch_proyek' and id_analisa_asat='$tahap_kendali_id' order by tgl_sch_parent";
				$query = $this->db->query($q);
				$data = $query->row_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			default:
				echo "Access Forbidden";
			break;
		}

	}

	function get_total_minggu_per_unit($info,$id_sch_proyek,$tahap_kendali_id){ //get_total_minggu_per_unit
		switch ($info) {
			case 'proyek':
				$q = "select * from simpro_tbl_sch_proyek_parent where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->result_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			case 'alat':
				$q = "select * from simpro_tbl_sch_proyek_parent_alat where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->result_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			case 'bahan':
				$q = "select * from simpro_tbl_sch_proyek_parent_bahan where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->result_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			case 'person':
				$q = "select * from simpro_tbl_sch_proyek_parent_person where id_sch_proyek='$id_sch_proyek' and tahap_kendali_id='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->result_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			case 'peralatan':
				$q = "select * from simpro_tbl_guna_alat_parent where id_guna_alat='$id_sch_proyek' and id_analisa_asat='$tahap_kendali_id'";
				$query = $this->db->query($q);
				$data = $query->result_array();

				if($query->num_rows() > 0 ) return $data;
				return 1;
			break;
			default:
				echo "Access Forbidden";
			break;
		}

	}

	//add

}