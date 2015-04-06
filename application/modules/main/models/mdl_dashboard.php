<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_dashboard extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	function generate_kumpulan_laporan($tahun,$bulan)
	{
		$tgl_skrg = $tahun.'-'.$bulan.'-01';
		$thn_now = date('Y');
		$waktu = date('h:i:s');
		$tgl_up = date('Y-m-d');

		$this->db->query("TRUNCATE TABLE simpro_tbl_kumpulan_laporan");

		$cek_proyek = "select 
			simpro_tbl_proyek.proyek_id,
			simpro_tbl_proyek.id_tender,
			simpro_tbl_proyek.proyek,
			simpro_tbl_proyek.status_pekerjaan,
			simpro_tbl_proyek.proyek_nama_sumber_1,
			simpro_tbl_proyek.mulai,
			simpro_tbl_proyek.berakhir,
			simpro_tbl_proyek.divisi_kode,
			simpro_tbl_divisi.divisi_name,
			simpro_tbl_proyek.no_spk 
			from simpro_tbl_divisi,
			simpro_tbl_proyek where 
			simpro_tbl_divisi.divisi_id = simpro_tbl_proyek.divisi_kode 
			 
			and (date_part('year',mulai) <='$thn_now' 
			and date_part('year',berakhir) >='$thn_now') 
			and simpro_tbl_proyek.proyek_status=2
			and date_part('year',tgl_pengumuman) >= '$thn_now' 
			and simpro_tbl_divisi.urut != '0'  
			and simpro_tbl_divisi.divisi_id!= '6'
			order by simpro_tbl_divisi.urut"; 

		// and simpro_tbl_proyek.bast_2 >='$tgl_skrg'  

		$q_cek_proyek = $this->db->query($cek_proyek);

		if ($q_cek_proyek->result()) {
			foreach ($q_cek_proyek->result() as $proyek) {

				$q_rab_awal = $this->db->query("select tahap_tanggal_kendali 
						from simpro_tbl_kontrak_terkini where proyek_id = $proyek->proyek_id
						and date_part('month',tahap_tanggal_kendali) <='$bulan' and date_part('year',tahap_tanggal_kendali) <='$tahun' 
						group by tahap_tanggal_kendali 
						order by tahap_tanggal_kendali 
						desc limit 1");

				if ($q_rab_awal->result()) {
					$tgl_cek = $q_rab_awal->row()->tahap_tanggal_kendali=='0001-01-01'?0:$q_rab_awal->row()->tahap_tanggal_kendali;
				} else {
					$tgl_cek = '0001-01-01';
				}

		        $arr_tgl = preg_split('[-]', $tgl_cek, -1, PREG_SPLIT_DELIM_CAPTURE);

		        $tgl_terkini= $this->e_getTotalDayGlobal($arr_tgl[0],$arr_tgl[1]);

		        $tgl_cashout=$arr_tgl[0]."-".$arr_tgl[1]."-".$tgl_terkini;

				$q_tata = $this->db->query("select 
					sum(tahap_volume_kendali * tahap_harga_satuan_kendali)as total 
					from simpro_tbl_kontrak_terkini 
					where proyek_id=$proyek->proyek_id
					and tahap_tanggal_kendali='$tgl_cek'");

				if ($q_tata->result()) {
					$tata = $q_tata->row()->total==''?0:$q_tata->row()->total;
				} else {
					$tata = 0;
				}

				$q_rab = $this->db->query("SELECT sum(subtotal_rab) as total FROM (
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
									WHERE simpro_rat_rab_analisa.id_tender = $proyek->id_tender
									AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
								)
								UNION ALL
								(
									SELECT 
									simpro_rat_rab_analisa.*,
									simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
									FROM simpro_rat_rab_analisa
									INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
									WHERE simpro_rat_rab_analisa.id_tender = $proyek->id_tender
								)
								) as tbl_rab_analisa
								GROUP BY kode_analisa_rat
							) as harga_analisa on harga_analisa.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
							WHERE simpro_rat_item_tree.id_proyek_rat = $proyek->id_tender	
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
									WHERE simpro_rat_rab_analisa.id_tender = $proyek->id_tender
									AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
								)
								UNION ALL
								(
									SELECT 
									simpro_rat_rab_analisa.*,
									simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
									FROM simpro_rat_rab_analisa
									INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
									WHERE simpro_rat_rab_analisa.id_tender = $proyek->id_tender
								)
								) as tbl_rab_analisa
								GROUP BY kode_analisa_rat
							) as harga_analisa on harga_analisa.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
							WHERE simpro_rat_item_tree.id_proyek_rat = $proyek->id_tender
						GROUP BY simpro_rat_item_tree.tree_parent_kode
						) as tbl_harga_rab 
						WHERE subtotal_rab IS NOT NULL
						and length(kode_tree) = 1");

				if ($q_rab->result()) {
					$hasil2awal = $q_rab->row()->total==''?0:$q_rab->row()->total;
				} else {
					$hasil2awal = 0;
				}

				$q_hasil3awal = $this->db->query("SELECT 
					sum((COALESCE(tbl_harga.harga, 0) * simpro_rap_item_tree.volume)) as total
					FROM simpro_rap_item_tree 
					LEFT JOIN simpro_rap_analisa_item_apek ON simpro_rap_analisa_item_apek.kode_tree = simpro_rap_item_tree.kode_tree and simpro_rap_analisa_item_apek.id_proyek = $proyek->proyek_id
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
							WHERE simpro_rap_analisa_asat.id_proyek= $proyek->proyek_id
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
								WHERE id_proyek= $proyek->proyek_id
								
								GROUP BY kode_analisa			
							) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_apek.kode_analisa			
							WHERE simpro_rap_analisa_apek.id_proyek= $proyek->proyek_id
							
							ORDER BY 
								simpro_rap_analisa_apek.parent_kode_analisa,				
								simpro_rap_analisa_apek.kode_analisa
							ASC					
						)		
						) AS tbl_analisa_satuan
						GROUP BY kode_analisa				
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_item_apek.kode_analisa						
					WHERE simpro_rap_item_tree.id_proyek = $proyek->proyek_id");

				if ($q_hasil3awal->result()) {
					$hasil3awal = $q_hasil3awal->row()->total==''?0:$q_hasil3awal->row()->total;
				} else {
					$hasil3awal = 0;
				}

				$nilai5 = $hasil2awal-$hasil3awal;

				$tgl_kini=$tahun."-01-01";

				$q_sdprogini = $this->db->query("select
					sum(
						case when a.tahap_diakui_bobot isnull
						then 0
						else a.tahap_diakui_bobot
						end * b.tahap_harga_satuan_kendali) as total
					from
					simpro_tbl_total_pekerjaan a
					join simpro_tbl_kontrak_terkini b
					on a.kontrak_terkini_id = b.id_kontrak_terkini
					where a.proyek_id = $proyek->proyek_id 
					and a.tahap_tanggal_kendali >='$tgl_kini' and a.tahap_tanggal_kendali <='$tgl_cek'");

				if ($q_sdprogini->result()) {
					$sdprogini = $q_sdprogini->row()->total==''?0:$q_sdprogini->row()->total;
				} else {
					$sdprogini = 0;
				}

				$q_bk_kini = $this->db->query("select sum(jumlah_cost_td)as total from simpro_tbl_po2 where proyek_id=$proyek->proyek_id and tahap_tanggal_kendali <='$tgl_cek' and jumlah_cost_td !=0");

				if ($q_bk_kini->result()) {
					$sub_jumlah_rencana_bhn1 = $q_bk_kini->row()->total==''?0:$q_bk_kini->row()->total;
				} else {
					$sub_jumlah_rencana_bhn1 = 0;
				}

				$q_pu_bk = $this->db->query("select pu,bk,pu_bk from simpro_tbl_pendukung where proyek_id=$proyek->proyek_id");
				if ($q_pu_bk->result()) {
					$pu = $q_pu_bk->row()->pu==''?0:$q_pu_bk->row()->pu;
					$bk = $q_pu_bk->row()->bk==''?0:$q_pu_bk->row()->bk;
					$pu_bk = $q_pu_bk->row()->pu_bk==''?0:$q_pu_bk->row()->pu_bk;
				} else {
					$pu = 0;
					$bk = 0;
					$pu_bk = 0;
				}

				$hasil2 = $sdprogini - $pu;
				$hasil3 =$sub_jumlah_rencana_bhn1 - $bk;
				$nilai8 = ($sdprogini - $sub_jumlah_rencana_bhn1)- $pu_bk;

				$q_mos = $this->db->query("SELECT  
					sum(a.mos_total_volume * c.harga) as total
					FROM 
					simpro_tbl_mos a 
					join simpro_rap_analisa_asat c
					on a.kode_rap = c.kode_rap and c.id_proyek = a.proyek_id
					where a.proyek_id = $proyek->proyek_id
					and a.mos_tgl  <= '$tgl_skrg'");

				if ($q_mos->result()) {
					$nilai7 = $q_mos->row()->total==''?0:$q_mos->row()->total;
				} else {
					$nilai7 = 0;
				}

				$q_cashin = $this->db->query("select
					sum(realisasi)as total
					from
					simpro_tbl_cashin
					where proyek_id = $proyek->proyek_id
					and tahap_tanggal_kendali <= '$tgl_cek'
					and ket_id = 1
					or ket_id = 2
					or ket_id = 4
					or ket_id = 5
					or ket_id = 6");

				if ($q_cashin->result()) {
					$nilai9 = $q_cashin->row()->total==''?0:$q_cashin->row()->total;
				} else {
					$nilai9 = 0;
				}

				$q_cashtodate = $this->db->query("select
					sum(jumlah) as total
					from
					simpro_tbl_cashtodate
					where proyek_id = $proyek->proyek_id
					and tanggal <= '$tgl_cashout'
					and pilihan = 1
					or pilihan = 3
					or pilihan = 4");

				if ($q_cashtodate->result()) {
					$nilai10 = $q_cashtodate->row()->total==''?0:$q_cashtodate->row()->total;
				} else {
					$nilai10 = 0;
				}

				$q_budget = $this->db->query("SELECT 
					sum((COALESCE(tbl_harga.harga, 0) * simpro_current_budget_item_tree.volume)) as total
					FROM simpro_current_budget_item_tree 
					LEFT JOIN simpro_current_budget_analisa_item_apek ON simpro_current_budget_analisa_item_apek.kode_tree = simpro_current_budget_item_tree.kode_tree and simpro_current_budget_analisa_item_apek.id_proyek = $proyek->proyek_id
					LEFT JOIN (
						SELECT 
						DISTINCT ON(kode_analisa)
											kode_analisa,
											SUM(subtotal) AS harga
						FROM (
						(
							SELECT 					
								(simpro_current_budget_analisa_asat.kode_analisa) AS kode_analisa, 
								(simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal
							FROM 
								simpro_current_budget_analisa_asat
							LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
							LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek)
							LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
							WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek->proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_cek'
							ORDER BY 
								simpro_current_budget_analisa_asat.kode_analisa,
								simpro_tbl_detail_material.detail_material_kode				
							ASC
						)
						UNION ALL 
						(
							SELECT 
								(simpro_current_budget_analisa_apek.parent_kode_analisa) AS kode_analisa, 
								COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
							FROM 
								simpro_current_budget_analisa_apek
							INNER JOIN simpro_current_budget_analisa_daftar ad ON (ad.kode_analisa = simpro_current_budget_analisa_apek.kode_analisa AND ad.id_proyek = simpro_current_budget_analisa_apek.id_proyek)
							INNER JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_apek.parent_kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_apek.id_proyek)			
							INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
							LEFT JOIN (
								SELECT 
									DISTINCT ON(kode_analisa)
									kode_analisa,
									SUM(harga * koefisien) AS harga
								FROM simpro_current_budget_analisa_asat 
								WHERE id_proyek= $proyek->proyek_id and tanggal_kendali = '$tgl_cek'
								
								GROUP BY kode_analisa			
							) as tbl_harga ON tbl_harga.kode_analisa = simpro_current_budget_analisa_apek.kode_analisa			
							WHERE simpro_current_budget_analisa_apek.id_proyek= $proyek->proyek_id and simpro_current_budget_analisa_apek.tanggal_kendali = '$tgl_cek'
							
							ORDER BY 
								simpro_current_budget_analisa_apek.parent_kode_analisa,				
								simpro_current_budget_analisa_apek.kode_analisa
							ASC					
						)		
						) AS tbl_analisa_satuan
						GROUP BY kode_analisa				
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_current_budget_analisa_item_apek.kode_analisa						
					WHERE simpro_current_budget_item_tree.id_proyek = $proyek->proyek_id and simpro_current_budget_item_tree.tanggal_kendali = '$tgl_cek'");
				
				if ($q_budget->result()) {
					$nilai14 = $q_budget->row()->total==''?0:$q_budget->row()->total;
				} else {
					$nilai14 = 0;
				}

				$nilai15 = $tata;

				$nilai16 = $hasil3awal;

				$nilai17 = $nilai7;

				$hasil18 = 0; //($totHasil15[$key] - $totHasil16[$key]);

				$nilai19 = 0; //($totHasil14[$key] - ($totHasil15[$key] - $totHasil16[$key]));

				$cnama = $this->session->userdata('uname');

				$uid = $this->session->userdata('uid');

				$jam = $waktu;

				$tgl_update = $tgl_up;

				$q_cek_approve = $this->db->query("select * from simpro_tbl_approve where proyek_id=$proyek->proyek_id and form_approve='ALL' and status='close' order by tgl_approve desc");

				if ($q_cek_approve->num_rows() > 0) {
					$tgl_approve = $q_cek_approve->row()->tgl_approve;
					$status_proyek = 'SELESAI';
				} else {
					$tgl_approve = null;
					$status_proyek = 'BELUM SELESAI';
				}

				$data = array(
					'proyek_id' => $proyek->proyek_id, 
					'divisi_kode' => $proyek->divisi_kode, 
					'unit_usaha' => $proyek->divisi_name, 
					'kontrak_kini' => $tata, 
					'pu_awal' => $hasil2awal, 
					'bk_awal' => $hasil3awal, 
					'laba_kotor' => $nilai5, 
					'pu_sd_bulanini' => $hasil2, 
					'bk_sd_bulanini' => $hasil3, 
					'selisihpu_bk' => $nilai8, 
					'mos' => $nilai7, 
					'laba_kotor_sd_blnini' => $nilai8, 
					'cash_in' => $nilai9, 
					'cash_out' => $nilai10, 
					'sisa_anggaran' => $nilai14, 
					'pu_proyeksi' => $nilai15,
					'bk_proyeksi' => $nilai16, 
					'mos_proyeksi'=> $nilai17, 
					'laba_kotor_proyeksi' => $hasil18, 
					'deviasi' => $nilai19,
					'no_spk' => $proyek->no_spk,
					'input_by' => $cnama,
					'sb_dana' => $proyek->proyek_nama_sumber_1,
					'mulai' => $proyek->mulai,
					'selesai' => $proyek->berakhir,
					'sp' => $proyek->status_pekerjaan,
					'nama_proyek' => $proyek->proyek,
					'user_update' => $cnama,
					'waktu_update' => $jam,
					'tgl_update' => $tgl_update,
					'tgl_approve' => $tgl_approve,
					'status' => $status_proyek
				);

				$this->db->insert('simpro_tbl_kumpulan_laporan',$data);

				// echo $proyek->proyek_id.'+'.$tata.'+'.$tgl_cek.'+'.$hasil2awal.'+'.$hasil3awal.'+'.$nilai5.'+'.$sdprogini.'<br>';
			}
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
}