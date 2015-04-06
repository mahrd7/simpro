<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Posisi Biaya Konstruksi</title>

	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
</head>
<body>
	<div class="titlegroup" id="titlegroup">Posisi Biaya Konstruksi (PBK-01)</div>
	<div class="headgroup" id="headgroup">			
		<form action="<?php echo base_url();?>laporan/pbk01" method="post">
			Periode s/d Tanggal : 
					<select name="bln">
						<?php 
							$bl[1]="January";
						    $bl[2]="February";
						    $bl[3]="March";
						    $bl[4]="April";
						    $bl[5]="May";
						    $bl[6]="Juni";
						    $bl[7]="July";
						    $bl[8]="August";
						    $bl[9]="September";
						    $bl[10]="October";
						    $bl[11]="November";
						    $bl[12]="December";

							for ($i=1; $i <= 12; $i++) { 
						    	$tulis = "";

								if ($i==$sel_bln){
						            $tulis =" selected";
						        }
								echo "<option value=$i $tulis>$bl[$i]</option>";
							}
						?>
					</select>
					<select name="thn">
						<?php 
							$date =  date('Y');				        

							for ($i=2000; $i <= $date; $i++) { 
								$tulis = "";

								if ($i==$sel_thn){
						            $tulis =" selected";
						        }
								echo "<option value=$i $tulis>$i</option>";
							}
						?>
					</select>
		<input type="submit" value="GO">
		<div class="actions">
			<a href="<?=base_url();?>laporan/printed_pbk?thn=<?=$sel_thn;?>&bln=<?=$sel_bln;?>" title="Print"><span class="icon-print">Print</span></a>
		</div>
		<!-- <input type="button" value=" INSERT CASHOUT " onclick="addwindow()"> -->
		</form>
	</div>
	<div class="parameter" id="parameter">
		<table width="100%">
			<tr>
				<td width="20%"><b>PT NINDYA KARYA (Persero)</b></td>
				<td width="2%" align="center"></td>
				<td width="78%"></td>
			</tr>
			<tr>
				<td width="20%">Divisi</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><?php echo $divisi->divisi_name; ?></td>
			</tr>	
			<tr>
				<td width="20%">Proyek</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><?php echo $proyek->proyek; ?></td>
			</tr>	
			<tr>
				<td width="20%">Periode s/d Tanggal</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><?php echo $tgl_rab; ?></td>
			</tr>
			<tr>
				<td width="20%">FM</td>
				<td width="2%" align="center">:</td>
				<td width="78%">PBK-01</td>
			</tr>							
		</table>
		</form>
	</div>	
	<table id="cashflow-grid" class="grid">
		<thead>
			<tr>
				<th rowspan="3">KODE<br>RAP</th>
				<th rowspan="3">URAIAN</th>
				<th rowspan="3">SATUAN</th>	
				<th colspan="3">RAPA AWAL</th>					
				<th colspan="3">RAPA KINI</th>				
				<th colspan="2">TOTAL BK SD<br>BLN <?php echo $tgl_rab_lalu; ?></th>				
				<th colspan="14">BIAYA KONSTRUKSI BULAN <?php echo $tgl_rab; ?></th>				
				<th rowspan="3">BK BLN <?php echo $tgl_rab_lanjut; ?></th>	
			</tr>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th colspan="2">TUNAI</th>
				<th colspan="2">HUTANG</th>
				<th colspan="2">ANTISIPASI</th>
				<th colspan="2">TOTAL</th>
				<th colspan="3">SISA<br>ANGGARAN</th>
				<th colspan="2">PERK SD<br>SELESAI</th>
				<th rowspan="2">Total<br>DEVIASI</th>
			</tr>	
			<tr>
				<th>VOL</th>
				<th>HARGA</th>
				<th>JUMLAH</th>
				<th>VOL</th>
				<th>HARGA</th>
				<th>JUMLAH</th>
				<th>VOL</th>
				<th>JUMLAH</th>
				<th>VOL</th>
				<th>JUMLAH</th>
				<th>VOL</th>
				<th>JUMLAH</th>
				<th>VOL</th>
				<th>JUMLAH</th>
				<th>VOL</th>
				<th>JUMLAH</th>
				<th>VOL</th>
				<th>HARGA</th>
				<th>JUMLAH</th>
				<th>VOL</th>
				<th>JUMLAH</th>
			</tr>			
		</thead>	
		<?php 
		$grand_jml_rap = 0;
		$grand_jml_rap_kini = 0;
		$grand_jml_cash_lalu = 0;
		$grand_jml_cash = 0;
		$grand_jml_hutang = 0;
		$grand_jml_antisipasi = 0;
		$grand_jml_total = 0;
		$grand_jml_ctg = 0;
		$grand_jml_selesai = 0;
		$grand_jml_deviasi = 0;
		$grand_jml_lanjut = 0;

		for ($i=1; $i <= 9 ; $i++) { 
			switch ($i) {
				case 1:
					$kode_judul = 'A';
					$uraian_judul = 'MATERIAL (500)';
					$kode_subbidang = '500';
				break;
				case 2:
					$kode_judul = 'B';
					$uraian_judul = 'UPAH (501)';
					$kode_subbidang = '501';
				break;
				case 3:
					$kode_judul = 'C';
					$uraian_judul = 'PERALATAN (502)';
					$kode_subbidang = '502';
				break;
				case 4:
					$kode_judul = 'D';
					$uraian_judul = 'SUB KONTRAKTOR (503)';
					$kode_subbidang = '503';
				break;
				case 5:
					$kode_judul = 'E';
					$uraian_judul = 'BIAYA BANK (504)';
					$kode_subbidang = '504';
				break;
				case 6:
					$kode_judul = 'F';
					$uraian_judul = 'BAU PROYEK (505)';
					$kode_subbidang = '505';
				break;
				case 7:
					$kode_judul = 'G';
					$uraian_judul = 'RUPA-RUPA (506)';
					$kode_subbidang = '506';
				break;
				case 8:
					$kode_judul = 'H';
					$uraian_judul = 'PERS.PENYELESAIAN (508)';
					$kode_subbidang = '508';
				break;
				case 9:
					$kode_judul = 'I';
					$uraian_judul = 'LAIN-LAIN';
					$kode_subbidang = '510';
				break;
			}
		?>
		<tbody>
			<tr class="list_data_induk">
				<td><?php echo $kode_judul; ?></td>
				<td><?php echo $uraian_judul; ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<?php 
				$sql = "select * from simpro_tbl_po2 where proyek_id = $proyek->proyek_id and tahap_tanggal_kendali >= (select tahap_tanggal_kendali from simpro_tbl_po2 where tahap_tanggal_kendali <= '$sel_thn-$sel_bln-01' group by tahap_tanggal_kendali order by tahap_tanggal_kendali desc limit 1) and tahap_tanggal_kendali <= '$sel_thn-$sel_bln-01' and left(detail_material_kode,3) = '$kode_subbidang'"; 
				$d_sql = $this->db->query($sql);

				$jml_rap = 0;
				$jml_rap_kini = 0;
				$jml_cash_lalu = 0;
				$jml_cash = 0;
				$jml_hutang = 0;
				$jml_antisipasi = 0;
				$jml_total = 0;
				$jml_ctg = 0;
				$jml_selesai = 0;
				$jml_deviasi = 0;
				$jml_lanjut = 0;

				if ($d_sql->result()) {
					foreach ($d_sql->result() as $row) {

				$sql_lalu = "select volume_cost_td,jumlah_cost_td from simpro_tbl_po2 where proyek_id = $proyek->proyek_id and tahap_tanggal_kendali = (select tahap_tanggal_kendali from simpro_tbl_po2 where tahap_tanggal_kendali < '$row->tahap_tanggal_kendali' group by tahap_tanggal_kendali order by tahap_tanggal_kendali desc limit 1) and lower(kode_rap) = lower('$row->kode_rap')";
				$query_lalu = $this->db->query($sql_lalu);

				if ($query_lalu->result()) {
					$row_lalu = $query_lalu->row();
					$vol_lalu = $row_lalu->volume_cost_td;
					$jml_lalu = $row_lalu->jumlah_cost_td;
				} else {
					$vol_lalu = 0;
					$jml_lalu = 0;
				}
			?>
				<tr>
					<td><?php echo $row->kode_rap; ?></td>
					<td><?php echo $row->detail_material_nama; ?></td>
					<td><?php echo $row->detail_material_satuan; ?></td>
					<td><?php echo number_format($row->volume_ob,2,",","."); ?></td>
					<td><?php echo number_format($row->harga_sat_ob,2,",","."); ?></td>
					<td><?php echo number_format($row->jumlah_ob,2,",","."); ?></td>
					<td><?php echo number_format($row->volume_cb,2,",","."); ?></td>
					<td><?php echo number_format($row->hargasat_cb,2,",","."); ?></td>
					<td><?php echo number_format($row->jumlah_cb,2,",","."); ?></td>
					<td><?php echo number_format($vol_lalu,2,",","."); ?></td>
					<td><?php echo number_format($jml_lalu,2,",","."); ?></td>
					<td><?php echo number_format($row->volume_cash_td,2,",","."); ?></td>
					<td><?php echo number_format($row->jumlah_cash_td,2,",","."); ?></td>
					<td><?php echo number_format($row->volume_hutang,2,",","."); ?></td>
					<td><?php echo number_format($row->jumlah_hutang,2,",","."); ?></td>
					<td><?php echo number_format($row->volume_hp,2,",","."); ?></td>
					<td><?php echo number_format($row->jumlah_hp,2,",","."); ?></td>
					<td><?php echo number_format($row->volume_cost_td,2,",","."); ?></td>
					<td><?php echo number_format($row->jumlah_cost_td,2,",","."); ?></td>
					<td><?php echo number_format($row->volume_cost_tg,2,",","."); ?></td>
					<td><?php echo number_format($row->hargasat_cost_tg,2,",","."); ?></td>
					<td><?php echo number_format($row->jumlah_cost_tg,2,",","."); ?></td>
					<td><?php echo number_format($row->volume_cf,2,",","."); ?></td>
					<td><?php echo number_format($row->jumlah_cf,2,",","."); ?></td>
					<td><?php echo number_format($row->trend,2,",","."); ?></td>
					<td><?php echo number_format($row->total_volume_rencana,2,",","."); ?></td>
				</tr>
			<?php

					$jml_rap = $jml_rap + $row->jumlah_ob;
					$jml_rap_kini = $jml_rap_kini + $row->jumlah_cb;
					$jml_cash_lalu = $jml_cash_lalu + $jml_lalu;
					$jml_cash = $jml_cash + $row->jumlah_cash_td;
					$jml_hutang = $jml_hutang + $row->jumlah_hutang;
					$jml_antisipasi = $jml_antisipasi + $row->jumlah_hp;
					$jml_total = $jml_total + $row->jumlah_cost_td;
					$jml_ctg = $jml_ctg + $row->jumlah_cost_tg;
					$jml_selesai = $jml_selesai + $row->jumlah_cf;
					$jml_deviasi = $jml_deviasi + $row->trend;
					$jml_lanjut = $jml_lanjut + $row->total_volume_rencana;

					}
				}
			?>
			<tr class="list_data">
				<td colspan="3"><b>SUB TOTAL(<?php echo $kode_judul; ?>)</b></td>
				<td colspan="3"><center><?php echo number_format($jml_rap,2,",","."); ?></center></td>
				<td colspan="3"><center><?php echo number_format($jml_rap_kini,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($jml_cash_lalu,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($jml_cash,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($jml_hutang,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($jml_antisipasi,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($jml_total,2,",","."); ?></center></td>
				<td colspan="3"><center><?php echo number_format($jml_ctg,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($jml_selesai,2,",","."); ?></center></td>
				<td><center><?php echo number_format($jml_deviasi,2,",","."); ?></center></td>
				<td><center><?php echo number_format($jml_lanjut,2,",","."); ?></center></td>
			</tr>
			<?php 

				$grand_jml_rap += $jml_rap;
				$grand_jml_rap_kini += $jml_rap_kini;
				$grand_jml_cash_lalu += $jml_cash_lalu;
				$grand_jml_cash += $jml_cash;
				$grand_jml_hutang += $jml_hutang;
				$grand_jml_antisipasi += $jml_antisipasi;
				$grand_jml_total += $jml_total;
				$grand_jml_ctg += $jml_ctg;
				$grand_jml_selesai += $jml_selesai;
				$grand_jml_deviasi += $jml_deviasi;
				$grand_jml_lanjut += $jml_lanjut;
			} 

			?>
			
			<tr class="list_data_grand">
				<td colspan="3"><b>GRAND TOTAL</b></td>
				<td colspan="3"><center><?php echo number_format($grand_jml_rap,2,",","."); ?></center></td>
				<td colspan="3"><center><?php echo number_format($grand_jml_rap_kini,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($grand_jml_cash_lalu,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($grand_jml_cash,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($grand_jml_hutang,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($grand_jml_antisipasi,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($grand_jml_total,2,",","."); ?></center></td>
				<td colspan="3"><center><?php echo number_format($grand_jml_ctg,2,",","."); ?></center></td>
				<td colspan="2"><center><?php echo number_format($grand_jml_selesai,2,",","."); ?></center></td>
				<td><center><?php echo number_format($grand_jml_deviasi,2,",","."); ?></center></td>
				<td><center><?php echo number_format($grand_jml_lanjut,2,",","."); ?></center></td>
			</tr>
			<tr height="24px">
				<td colspan="26"></td>
			</tr>
			<?php

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

		    $hari_lalu=e_getTotalDayGlobal($sel_thn_lalu,$sel_bln_lalu);
		    $tgl_lalu=$sel_thn_lalu."-".$sel_bln_lalu."-".$hari_lalu;
		    
		    $hari_ini=e_getTotalDayGlobal($sel_thn,$sel_bln);
		    $tgl_ini=$sel_thn."-".$sel_bln."-".$hari_ini;

				$tot_jumlah_ob = $this->db->query(
					"select 
					sum(jumlah_ob)as volumerab,
					sum(jumlah_cb)as volumecb 
					from simpro_tbl_po2 
					where 
					tahap_tanggal_kendali >= '$tg'
					and tahap_tanggal_kendali <= '$sel_thn-$sel_bln-01' 
					and proyek_id=$proyek->proyek_id group by proyek_id"
				)->row();

				if(!$tot_jumlah_ob) $tot_jumlah_ob=0; else $tot_jumlah_ob = $tot_jumlah_ob->volumerab;

			    $tot_jumlah_ctd_sblm = $this->db->query(
					"select 
					sum(jumlah_cost_td)as bhn2ll 
					from 
					simpro_tbl_po2 
					where 
					proyek_id=$proyek->proyek_id 
					and tahap_tanggal_kendali <='$tgl_lalu'  
					and jumlah_cost_td !=0 group by proyek_id"
				)->row();

			    if(!$tot_jumlah_ctd_sblm) $tot_jumlah_ctd_sblm=0; else $tot_jumlah_ctd_sblm = $tot_jumlah_ctd_sblm->bhn2ll;

			    $tot_rencana = $this->db->query(
					"select 
					sum(rpbk_rrk1 * komposisi_harga_satuan_kendali) 
					as bhn3ll 
					from 
					simpro_tbl_rpbk 
					where proyek_id='$proyek->proyek_id' 
					and date_part('month',tahap_tanggal_kendali)='$sel_bln_lalu' 
					and date_part('year',tahap_tanggal_kendali)='$sel_thn_lalu' group by proyek_id"
				)->row();

			    if(!$tot_rencana) $tot_rencana=0; else $tot_rencana = $tot_rencana->bhn3ll;

			    $tot_jumlah_ctd_sd = $this->db->query(
					"select 
					sum(jumlah_cost_td)as bhn4ll 
					from 
					simpro_tbl_po2 
					where proyek_id='$proyek->proyek_id'  
					and tahap_tanggal_kendali <='$tgl_ini' 
					and jumlah_cost_td !=0 group by proyek_id"
				)->row();

			    if(!$tot_jumlah_ctd_sd) $tot_jumlah_ctd_sd=0; else $tot_jumlah_ctd_sd = $tot_jumlah_ctd_sd->bhn4ll;

			    $tot_jumlah_ctd = $tot_jumlah_ctd_sd - $tot_jumlah_ctd_sblm;

			    $tot_deviasi = $tot_jumlah_ctd - $tot_rencana;
			    $tot_jumlah_cost_tg = $tot_jumlah_ob - $tot_jumlah_ctd_sd;
			    $tot_jumlah_cf = $tot_jumlah_cost_tg + $tot_jumlah_ctd_sd;
			    $tot_jumlah_trend = $tot_jumlah_ob - $tot_jumlah_cf;
			    $tot_jumlah_rencana = $this->db->query(
					"select 
					sum(rpbk_rrk1 * komposisi_harga_satuan_kendali) as rencanabhn8 
					from 
					simpro_tbl_rpbk where proyek_id='$proyek->proyek_id' 
					and date_part('month',tahap_tanggal_kendali)='$sel_bln' 
					and date_part('year',tahap_tanggal_kendali)='$sel_thn' group by proyek_id"
				)->row();

			    if(!$tot_jumlah_rencana) $tot_jumlah_rencana=0; else $tot_jumlah_rencana = $tot_jumlah_rencana->rencanabhn8;

				$proyekxxx="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id=$proyek->proyek_id and date_part('month',tanggal)<='$sel_bln_lalu' and date_part('year',tanggal)<='$sel_thn_lalu' and pilihan='1' group by proyek_id";
			    $rproyek=pg_query($proyekxxx);
			    $xproyek=pg_fetch_array($rproyek);
			    $proyek2="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id=$proyek->proyek_id and date_part('month',tanggal)<='$sel_bln_lalu' and date_part('year',tanggal)<='$sel_thn_lalu' and pilihan='3' group by proyek_id";
			    $rproyek2=pg_query($proyek2);
			    $xproyek2=pg_fetch_array($rproyek2);
			    $proyek3="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id=$proyek->proyek_id and date_part('month',tanggal)<='$sel_bln_lalu' and date_part('year',tanggal)<='$sel_thn_lalu' and pilihan='4' group by proyek_id";
			    $rproyek3=pg_query($proyek3);
			    $xproyek3=pg_fetch_array($rproyek3);
			    
			    //REALISASI
			    $realisasiproyek="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek->proyek_id' and date_part('month',tanggal) = '$sel_bln' and date_part('year',tanggal)='$sel_thn' and pilihan='1' group by proyek_id";
			    $rrealisasiproyek=pg_query($realisasiproyek);
			    $xrealisasiproyek=pg_fetch_array($rrealisasiproyek);
			    $realisasiproyek2="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek->proyek_id' and date_part('month',tanggal) = '$sel_bln' and date_part('year',tanggal)='$sel_thn' and pilihan='3' group by proyek_id";
			    $rrealisasiproyek2=pg_query($realisasiproyek2);
			    $xrealisasiproyek2=pg_fetch_array($rrealisasiproyek2);
			    $realisasiproyek3="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek->proyek_id' and date_part('month',tanggal) = '$sel_bln' and date_part('year',tanggal)='$sel_thn' and pilihan='4' group by proyek_id";
			    $rrealisasiproyek3=pg_query($realisasiproyek3);
			    $xrealisasiproyek3=pg_fetch_array($rrealisasiproyek3);
			    
			    //SD MINGGU INI
			    $mgllproyek="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek->proyek_id' and tanggal<='$tgl_ini' and pilihan='1' group by proyek_id";
			    $rmgllproyek=pg_query($mgllproyek);
			    $xmgllproyek=pg_fetch_array($rmgllproyek);
			    $mgllproyek2="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek->proyek_id' and tanggal<='$tgl_ini'  and pilihan='3' group by proyek_id";
			    $rmgllproyek2=pg_query($mgllproyek2);
			    $xmgllproyek2=pg_fetch_array($rmgllproyek2);
			    $mgllproyek3="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek->proyek_id' and tanggal<='$tgl_ini'  and pilihan='4' group by proyek_id";
			    $rmgllproyek3=pg_query($mgllproyek3);
			    $xmgllproyek3=pg_fetch_array($rmgllproyek3);

			    $cb0="select sum(tahap_volume_kendali*tahap_harga_satuan_kendali) as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek->proyek_id' and date_part('month',tahap_tanggal_kendali)='$sel_bln' and date_part('year',tahap_tanggal_kendali)='$sel_thn' group by proyek_id ";
			    $rcb0=pg_query($cb0);
			    $xcb0=pg_fetch_array($rcb0);
			    
			    
			    $proyekxxx=$tot_jumlah_ob - round($xcb0['total'] * 3/100);
			    
			    /*rencana bln dpan*/
			    $rencanapo="select sum(rproyeksi2)as rencana1,sum(rproyeksi3) as rencana2 from simpro_tbl_cashin where proyek_id='$proyek->proyek_id' and date_part('month',tahap_tanggal_kendali)='$sel_bln' and date_part('year',tahap_tanggal_kendali)='$sel_thn' and ket_id in('7','8','9','10','11','12')";
			    $xlrencanapo=pg_query($rencanapo);
			    $rrencanapo=pg_fetch_array($xlrencanapo);
			    
			    $proy1=$rrencanapo['rencana1']+$rrencanapo['rencana2'];
			    
			    $rencana1bk="select sum(rproyeksi2)as rencana1,sum(rproyeksi3) as rencana2 from simpro_tbl_cashin where proyek_id='$proyek->proyek_id' and date_part('month',tahap_tanggal_kendali)='$sel_bln_lalu' and date_part('year',tahap_tanggal_kendali)='$sel_thn_lalu' and ket_id in('7','8','9','10','11','12')";
			    $rrencana1bk=pg_query($rencana1bk);
			    $xrencana1bk=pg_fetch_array($rrencana1bk);

				//Proyek
			    $pco1=$tot_jumlah_ob;
			    $pco2=$xproyek['total'];
			    $pco3=$xrencana1bk['rencana1']+$xrencana1bk['rencana2'];
			    $pco4=$xrealisasiproyek['total'];
			    $pco5=$pco3-$pco4;//$xrealisasiproyek[total]-0;
			    $pco6=$xmgllproyek['total'];
			    $pco7=$pco1-$pco6;//$tot_jumlah_cost_tg;//$tot_jumlah_ob-$xmgllproyek[total];
			    $pco8=$pco6+$pco7;//$xmgllproyek[total]+($tot_jumlah_ob-$xmgllproyek[total]);
			    $pco10=$proy1;

			    //divisi
			    $dco1=0;
			    $dco2=$xproyek2['total'];
			    $dco4=$xrealisasiproyek2['total'];
			    $dco5=$xrealisasiproyek2['total']-0;
			    $dco6=$xmgllproyek2['total'];
			    $dco7=0-$dco6;

			    //pusat
			    $puco2=$xproyek3['total'];
			    $puco4=$xrealisasiproyek3['total'];
			    $puco5=$xrealisasiproyek3['total']-0;
			    $puco6=$xmgllproyek3['total'];
			    
			    
			    $total_co1=$proyekxxx;
			    
			    $co1=$pco1+$dco1;
			    $co2=$pco2+$dco2;//$xproyek[total]+$xproyek2[total]+$xproyek3[total];
			    $co3=$pco3;
			    $co4=$pco4+$dco4+$puco4;//$xrealisasiproyek[total]+$xrealisasiproyek2[total]+$xrealisasiproyek3[total];
			    $co5=$pco5+$dco5+$puco5;//$xrealisasiproyek[total]+$xrealisasiproyek2[total]+$xrealisasiproyek3[total];
			    $co6=$pco6+$dco6+$puco6;//$xmgllproyek[total]+$xmgllproyek2[total]+$xmgllproyek3[total];
			    $co7=$pco7+$dco7;//($tot_jumlah_ob-$xmgllproyek[total])+(0-$xmgllproyek2[total]);
			    $co8=$pco8;//$xmgllproyek[total]+($tot_jumlah_ob-$xmgllproyek[total]);
			    $co9=0;
			    $co10=$proy1;
			?>
			<tr class="list_data_induk">
				<td colspan="26"><b>CASH OUT</b></td>
			</tr>
			<tr>
				<td colspan="3">MELALUI PROYEK</td>
				<td colspan="3"><?php echo number_format($pco1,2,",","."); ?></td>
				<td colspan="3"><?php echo number_format($pco6,2,",","."); ?></td>
				<td colspan="2"><?php echo number_format($pco2,2,",","."); ?></td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="3"><?php echo number_format($pco7,2,",","."); ?></td>
				<td colspan="2"><?php echo number_format($pco8,2,",","."); ?></td>
				<td>-</td>
				<td><?php echo number_format($pco10,2,",","."); ?></td>
			</tr>
			<tr>
				<td colspan="3">MELALUI DIVISI</td>
				<td colspan="3">-</td>
				<td colspan="3"><?php echo number_format($dco6,2,",","."); ?></td>
				<td colspan="2"><?php echo number_format($dco2,2,",","."); ?></td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="3"><?php echo number_format($dco7,2,",","."); ?></td>
				<td colspan="2">-</td>
				<td>-</td>
				<td>-</td>
			</tr>
			<tr>
				<td colspan="3">MELALUI PUSAT</td>
				<td colspan="3">-</td>
				<td colspan="3"><?php echo number_format($puco6,2,",","."); ?></td>
				<td colspan="2"><?php echo number_format($puco2,2,",","."); ?></td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="3">-</td>
				<td colspan="2">-</td>
				<td>-</td>
				<td>-</td>
			</tr>
			<tr class="list_data_grand">
				<td colspan="3"><b>Total Cash Out</b></td>
				<td colspan="3"><?php echo number_format($co1,2,",","."); ?></td>
				<td colspan="3"><?php echo number_format($co6,2,",","."); ?></td>
				<td colspan="2"><?php echo number_format($co2,2,",","."); ?></td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="2">-</td>
				<td colspan="3"><?php echo number_format($co7,2,",","."); ?></td>
				<td colspan="2"><?php echo number_format($co8,2,",","."); ?></td>
				<td><?php echo number_format($co9,2,",","."); ?></td>
				<td><?php echo number_format($co10,2,",","."); ?></td>
			</tr>
		</tbody>																																																																																																								
	</table>
</body>
</html>