<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_laporan extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	function lbp01()
	{
	}

	function get_data_proyek($proyek_id)
	{
		$sql = "select * from simpro_tbl_proyek where proyek_id='$proyek_id'";
		$q = $this->db->query($sql);
		return $q->result();
	}	
	function get_data_proyek_pbk($proyek_id)
	{
		$sql = "select * from simpro_tbl_proyek where proyek_id='$proyek_id'";
		$q = $this->db->query($sql);
		return $q->row();
	}	
	function get_data_divisi_pbk($divisi_id)
	{
		$sql = "select * from simpro_tbl_divisi where divisi_id='$divisi_id'";
		$q = $this->db->query($sql);
		return $q->row();
	}	

	function get_total_rab($proyek_id)
	{
		$sql = "select sum(
				(CASE WHEN tahap_volume_kendali is null
				THEN 0
				ELSE tahap_volume_kendali
				END) * 
				(CASE WHEN tahap_harga_satuan_kendali is null
				THEN 0
				ELSE tahap_harga_satuan_kendali
				END)) as total 
				from simpro_tbl_kontrak_terkini 
				where proyek_id = $proyek_id 
				and tahap_tanggal_kendali =
				(select tahap_tanggal_kendali 
				from simpro_tbl_kontrak_terkini 
				where proyek_id = $proyek_id 
				group by tahap_tanggal_kendali 
				order by tahap_tanggal_kendali asc limit 1)";
		$q = $this->db->query($sql);
		return $q->row()->total;
	}

	function get_total_total_kerja($proyek_id,$tgl_rab)
	{
		$sql = "select sum(
				(CASE WHEN b.tahap_diakui_bobot is null
				THEN 0
				ELSE b.tahap_diakui_bobot
				END) * 
				(CASE WHEN a.tahap_harga_satuan_kendali is null
				THEN 0
				ELSE a.tahap_harga_satuan_kendali
				END)) as total 
				from simpro_tbl_kontrak_terkini a
				join simpro_tbl_total_pekerjaan b
				on a.id_kontrak_terkini = b.kontrak_terkini_id
				where b.proyek_id = $proyek_id 
				and b.tahap_tanggal_kendali <= '$tgl_rab'";
		$q = $this->db->query($sql);
		return $q->row()->total;
	}

	function get_total_rap($proyek_id)
	{
		$sql = "SELECT 
					sum(tbl_harga.harga * tbl_total_koef.volume_total) as total
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
				INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))";
		$q = $this->db->query($sql);
		return $q->row()->total;
	}

	function get_rekap_proyek($proyek_id)
	{
		// $sqlprog= $this->db->query("select 
		// 	sum(a.tahap_diakui_bobot * b.tahap_harga_satuan_kendali)as 
		// 	total from simpro_tbl_total_pekerjaan a
		// 	join simpro_tbl_kontrak_terkini b
		// 	on a.kontrak_terkini_id = b.id_kontrak_terkini
		// 	where a.proyek_id=$proyek_id
		// 	and a.tahap_tanggal_kendali <='$tgl_rab' 
		// 	and a.tahap_diakui_bobot !=0")->row()->total;

		// $tata= $this->db->query("select sum(
		// 		(CASE WHEN tahap_volume_kendali is null
		// 		THEN 0
		// 		ELSE tahap_volume_kendali
		// 		END) * 
		// 		(CASE WHEN tahap_harga_satuan_kendali is null
		// 		THEN 0
		// 		ELSE tahap_harga_satuan_kendali
		// 		END)) as total 
		// 		from simpro_tbl_kontrak_terkini 
		// 		where proyek_id = $proyek_id 
		// 		and tahap_tanggal_kendali =
		// 		(select tahap_tanggal_kendali 
		// 		from simpro_tbl_kontrak_terkini 
		// 		where proyek_id = $proyek_id 
		// 		group by tahap_tanggal_kendali 
		// 		order by tahap_tanggal_kendali asc limit 1)");

		// $hasil1= ($sqlprog/$tata) * 100;

		// $sqlk_1="select sum(tahap_volume_kendali * tahap_harga_satuan_kendali)as volume,sum(tahap_volume_kendali_new * tahap_harga_satuan_kendali)as newvolume,sum(tahap_volume_kendali_kurang * tahap_harga_satuan_kendali )as volkrg from tbl_kontrak_terkini where no_spk='$cnospk_pilih' and date_part('month',tahap_tanggal_kendali) ='$k' and tahap_kode_induk_kendali !='' group by no_spk";
	 //    $res_1=dbresult($sqlk_1);//echo $sqlk;
	 //    $x11_=pg_fetch_array($res_1);
  //       $hasilkontrak1 = $x11_[volume];
  //       $hasiltbh1 = $x11_[newvolume];
  //       $hasilkrg1 = $x11_[volkrg];

		$tgl_skrg = date('Y-m-d');
		$bln=date("m");
		$thn=date("Y");

		$bln1=$bln-2;

		if($bln1<0){
			$angka=1;
			$bln1=11;
			$bln=13;
		}
		if($bln1==0){
			$angka=2;
			$bln1=12;
			$bln=14;
		}

		$bulan[1]="January";
        $bulan[2]="February";
        $bulan[3]="March";
        $bulan[4]="April";
        $bulan[5]="May";
        $bulan[6]="Juni";
        $bulan[7]="July";
        $bulan[8]="August";
        $bulan[9]="September";
        $bulan[10]="October";
        $bulan[11]="November";
        $bulan[12]="December";

		for ($k=$bln1; $k<=$bln; $k++) {

			$thn_skrng = $thn;

			if($k>12 and $angka==1)
			{ 
				$k=$bln-12; 
				$bln=$k;
				$angka=1;
				$thn_skrng = $thn + 1;
			} else if ($k>12 and $angka==2)
			{  
				$k=$bln-13; 
				$bln=$k+1;
				$angka=2;
				$thn_skrng = $thn + 1;
			}

			$tgl_rab_awal = $this->db->query("select tahap_tanggal_kendali 
						from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id
						group by tahap_tanggal_kendali 
						order by tahap_tanggal_kendali 
						asc limit 1")->row()->tahap_tanggal_kendali;

			$sql_hasil1 = "with lpf as (
						select
						sum(
						case when a.tahap_diakui_bobot isnull
						then 0
						else a.tahap_diakui_bobot
						end * b.tahap_harga_satuan_kendali) as total_lpf
						from
						simpro_tbl_total_pekerjaan a
						join simpro_tbl_kontrak_terkini b
						on a.kontrak_terkini_id = b.id_kontrak_terkini
						where a.proyek_id = $proyek_id and a.tahap_tanggal_kendali <='$tgl_skrg'
						),
						rab as (
						select
						sum(
						case when tahap_volume_kendali isnull
						then 0
						else tahap_volume_kendali
						end * tahap_harga_satuan_kendali) as total_rab
						from simpro_tbl_kontrak_terkini 
						where tahap_tanggal_kendali = '$tgl_rab_awal' 
						and proyek_id = $proyek_id
						)
						select lpf.total_lpf + rab.total_rab as total1 from lpf,rab";

			if ($this->db->query($sql_hasil1)->row()->total1 != null) {
				$hasil1 = $this->db->query($sql_hasil1)->row()->total1 * 100;
			} else {
				$hasil1 = 0;
			}

			$sql_hasil2 = "select
					sum((
					case when tahap_volume_kendali isnull
					then 0
					else tahap_volume_kendali
					end + 
					case when tahap_volume_kendali_new isnull
					then 0
					else tahap_volume_kendali_new
					end + 
					case when tahap_volume_kendali_kurang isnull
					then 0
					else tahap_volume_kendali_kurang
					end) * 
					tahap_harga_satuan_kendali) as total2
					from
					simpro_tbl_kontrak_terkini
					where proyek_id = $proyek_id 
					and extract(month from tahap_tanggal_kendali) = '$k' 
					and extract(year from tahap_tanggal_kendali) = '$thn_skrng'";

			if ($this->db->query($sql_hasil2)->row()->total2 != null) {
				$hasil2 = $this->db->query($sql_hasil2)->row()->total2;
			} else {
				$hasil2 = 0;
			}

			$sql_hasil3 = "select
					sum(jumlah_cost_td) as total3
					from
					simpro_tbl_po2
					where
					detail_material_kode like '500%'
					or detail_material_kode like '501%'
					or detail_material_kode like '502%'
					or detail_material_kode like '503%'
					or detail_material_kode like '504%'
					or detail_material_kode like '505%'
					and proyek_id = $proyek_id 
					and extract(month from tahap_tanggal_kendali) = '$k' 
					and extract(year from tahap_tanggal_kendali) = '$thn_skrng'";

			if ($this->db->query($sql_hasil3)->row()->total3 != null) {
				$hasil3 = $this->db->query($sql_hasil3)->row()->total3;
			} else {
				$hasil3 = 0;
			}

			$sql_hasil4 = "with po2 as (select
						sum(jumlah_cost_td) as total_po2
						from
						simpro_tbl_po2
						where
						detail_material_kode like '500%'
						or detail_material_kode like '501%'
						or detail_material_kode like '502%'
						or detail_material_kode like '503%'
						or detail_material_kode like '504%'
						or detail_material_kode like '505%'
						and proyek_id = $proyek_id
						and	tahap_tanggal_kendali = '$tgl_rab_awal'),
						kk as (
						select
						sum(
						case when tahap_volume_kendali isnull
						then 0
						else tahap_volume_kendali
						end * tahap_harga_satuan_kendali) as total_rab
						from simpro_tbl_kontrak_terkini 
						where tahap_tanggal_kendali = '$tgl_rab_awal' 
						and proyek_id = $proyek_id
						)
						select 
						case when po2.total_po2 = 0 or kk.total_rab = 0
						then 0
						else
						(po2.total_po2/kk.total_rab) * 100 
						end
						as total4 from po2,kk";

			if ($this->db->query($sql_hasil4)->row()->total4 != null) {
				$hasil4 = $this->db->query($sql_hasil4)->row()->total4;
			} else {
				$hasil4 = 0;
			}

			if ($hasil3 == 0 || $hasil2 == 0) {
				$hasil5 = 0;
			} else {
				$hasil5 = ($hasil3 / $hasil2) * 100;
			}

			$hasil6 = $hasil2 - $hasil3;

			$sql_hasil7 = "SELECT  
					sum(a.mos_diakui_volume * c.harga) as total7
					FROM 
					simpro_tbl_mos a 
					join simpro_rap_analisa_asat c
					on a.kode_rap = c.kode_rap and c.id_proyek = a.proyek_id
					where a.proyek_id = $proyek_id
					and extract(month from mos_tgl) = '$k' 
					and extract(year from mos_tgl) = '$thn_skrng'";

			if ($this->db->query($sql_hasil7)->row()->total7 != null) {
				$hasil7 = $this->db->query($sql_hasil7)->row()->total7;
			} else {
				$hasil7 = 0;
			}

			$hasil8 = $hasil6 + $hasil7;

			$sql_hasil9 = "select
					sum(realisasi)as total9
					from
					simpro_tbl_cashin
					where proyek_id = $proyek_id
					and tahap_tanggal_kendali <= '$tgl_skrg'
					and ket_id = 1
					or ket_id = 2
					or ket_id = 4
					or ket_id = 5
					or ket_id = 6";

			if ($this->db->query($sql_hasil9)->row()->total9 != null) {
				$hasil9 = $this->db->query($sql_hasil9)->row()->total9;
			} else {
				$hasil9 = 0;
			}

			$sql_hasil10 = "select
					sum(jumlah) as total10
					from
					simpro_tbl_cashtodate
					where proyek_id = $proyek_id
					and tanggal <= '$tgl_skrg'
					and pilihan = 1
					or pilihan = 3
					or pilihan = 4";

			if ($this->db->query($sql_hasil10)->row()->total10 != null) {
				$hasil10 = $this->db->query($sql_hasil10)->row()->total10;
			} else {
				$hasil10 = 0;
			}

			$data['hasil1'] = $hasil1;
			$data['hasil2'] = $hasil2;
			$data['hasil3'] = $hasil3;
			$data['hasil4'] = $hasil4;
			$data['hasil5'] = $hasil5;
			$data['hasil6'] = $hasil6;
			$data['hasil7'] = $hasil7;
			$data['hasil8'] = $hasil8;
			$data['hasil9'] = $hasil9;
			$data['hasil10'] = $hasil10;
			$data['bulan'] = $bulan[$k];

			$result[] = $data;
		}

		return $result;
	}

	function get_rekap_laporan_proyek($proyek_id)
	{
		$tgl_skrg = date('Y-m-d');
		$bln=date("m");
		$thn=date("Y");

		$tgl_rab_awal = $this->db->query("select tahap_tanggal_kendali 
			from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id
			group by tahap_tanggal_kendali 
			order by tahap_tanggal_kendali 
			asc limit 1")->row()->tahap_tanggal_kendali;

		$sql_hasil1 = "with lpf as (
			select
			sum(
				case when a.tahap_diakui_bobot isnull
				then 0
				else a.tahap_diakui_bobot
				end * b.tahap_harga_satuan_kendali) as total_lpf
			from
			simpro_tbl_total_pekerjaan a
			join simpro_tbl_kontrak_terkini b
			on a.kontrak_terkini_id = b.id_kontrak_terkini
			where a.proyek_id = $proyek_id and a.tahap_tanggal_kendali <='$tgl_skrg'
			),
			rab as (
				select
				sum(
					case when tahap_volume_kendali isnull
					then 0
					else tahap_volume_kendali
					end * tahap_harga_satuan_kendali) as total_rab
			from simpro_tbl_kontrak_terkini 
			where tahap_tanggal_kendali = '$tgl_rab_awal' 
			and proyek_id = $proyek_id
			)
			select 
			case when lpf.total_lpf = 0 
			then 0
			else lpf.total_lpf / rab.total_rab
			end as total1 from lpf,rab";

		if ($this->db->query($sql_hasil1)->row()->total1 != null) {
			$progress = ($this->db->query($sql_hasil1)->row()->total1 * 100);
		} else {
			$progress = 0;
		}

		$sql_hasil2 = "select
			sum((
			case when tahap_volume_kendali isnull
			then 0
			else tahap_volume_kendali
			end + 
			case when tahap_volume_kendali_new isnull
			then 0
			else tahap_volume_kendali_new
			end + 
			case when tahap_volume_kendali_kurang isnull
			then 0
			else tahap_volume_kendali_kurang
			end) * 
			tahap_harga_satuan_kendali) +
			sum (case when harga_satuan_eskalasi isnull
			then 0
			else harga_satuan_eskalasi
			end) as total2
			from
			simpro_tbl_kontrak_terkini
			where proyek_id = $proyek_id and
			tahap_tanggal_kendali <= '$tgl_skrg'";

		if ($this->db->query($sql_hasil2)->row()->total2 != null) {
			$pu = $this->db->query($sql_hasil2)->row()->total2;
		} else {
			$pu = 0;
		}

		$sql_hasil3 = "select
		sum(jumlah_cost_td) as total3
		from
		simpro_tbl_po2
		where proyek_id = $proyek_id and
		tahap_tanggal_kendali = 
		(select
		tahap_tanggal_kendali
		from
		simpro_tbl_po2
		where proyek_id = $proyek_id and
		tahap_tanggal_kendali <= '$tgl_skrg' group by tahap_tanggal_kendali order by tahap_tanggal_kendali desc limit 1)";

		if ($this->db->query($sql_hasil3)->row()->total3 != null) {
			$bk = $this->db->query($sql_hasil3)->row()->total3;
		} else {
			$bk = 0;
		}

		$lp = $pu - $bk;

		$sql_hasil9 = "select
		sum(realisasi)as total9
		from
		simpro_tbl_cashin
		where proyek_id = $proyek_id
		and tahap_tanggal_kendali <= '$tgl_skrg'
		and ket_id = 1
		or ket_id = 2
		or ket_id = 4
		or ket_id = 5";

		if ($this->db->query($sql_hasil9)->row()->total9 != null) {
			$cashin = $this->db->query($sql_hasil9)->row()->total9;
		} else {
			$cashin = 0;
		}

		$sql_hasil10 = "select
		sum(jumlah) as total10
		from
		simpro_tbl_cashtodate
		where proyek_id = $proyek_id
		and tanggal <= '$tgl_skrg'
		and pilihan = 1
		or pilihan = 3
		or pilihan = 4";

		if ($this->db->query($sql_hasil10)->row()->total10 != null) {
			$cashout = $this->db->query($sql_hasil10)->row()->total10;
		} else {
			$cashout = 0;
		}

		$data['progress'] = $progress;
		$data['pu'] = $pu;
		$data['bk'] = $bk;
		$data['lp'] = $lp;
		$data['cashin'] = $cashin;
		$data['cashout'] = $cashout;

		$result[] = $data;

		return $result;

	}
}