<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Anggaran BK Akuntan Revisi</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
</head>
<body>
	<?php
		function angka($v=0)
		{
			$angka = number_format($v,2,",",".");
			return $angka;
		}
	?>
	<div class="titlegroup">BIAYA RAPA ANGGARAN BEBAN KONTRAK REVISI</div>
	<div class="headgroup">		
		<div class="actions">
			<a href="#" title="Print"><span class="icon-print">Print</span></a>
		</div>
	</div>
	<div class="parameter">
		<table width="100%">
			<tr>
				<td width="20%">PEKERJAAN</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><?php echo $proyek->proyek; ?></td>
			</tr>
			<tr>
				<td width="20%">URAIAN BIAYA</td>
				<td width="2%" align="center">:</td>
				<td width="78%">
					<form action="<?php echo base_url();?>laporan/anggaran_bk_akuntan_revisi" method="post">
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
		<!-- <input type="button" value=" INSERT CASHOUT " onclick="addwindow()"> -->
		</form>
				</td>
			</tr>									
		</table>
	</div>	
	<table id="cashflow-grid">
		<thead>
			<tr>
				<th>KODE</th>
				<th>NAMA</th>
				<th>SATUAN</th>
				<th>VOLUME</th>	
				<th>HARGA SATUAN</th>
				<th>JUMLAH</th>
				<th>KETERANGAN</th>		
			</tr>				
		</thead>	
		<tbody>
			<?php
				$total_data = 0;
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
			<tr class="list_data">
				<td colspan="7"><?php echo $uraian_judul; ?></td>
			</tr>
			<?php 
				$sql_data = "SELECT					
								DISTINCT(tbl_total_koef.kode_material) as kd_material,
								tbl_total_koef.kode_rap,
								simpro_tbl_detail_material.detail_material_id as id_detail_material,
								simpro_tbl_detail_material.detail_material_nama,
								simpro_tbl_detail_material.detail_material_satuan,
								simpro_tbl_detail_material.detail_material_spesifikasi,
								(simpro_tbl_subbidang.subbidang_kode || ' - ' || simpro_tbl_subbidang.subbidang_name) as simpro_tbl_subbidang,
								tbl_harga.harga,
								ROUND(SUM(tbl_total_koef.volume_total),4) as koefisien,
								(tbl_harga.harga * SUM(tbl_total_koef.volume_total)) as subtotal,
								tbl_total_koef.id_proyek
							FROM (				
								SELECT 
								tbl_asat_apek.*,
								simpro_current_budget_analisa_item_apek.kode_tree,
								simpro_current_budget_item_tree.volume,
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
									WHERE id_proyek = $idproyek and tanggal_kendali = '$tgl_rab'
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
											WHERE id_proyek = $idproyek and tanggal_kendali = '$tgl_rab'
											GROUP BY kode_material,kode_analisa,id_proyek,koefisien,kode_rap
											ORDER BY kode_material ASC
										) tbl_asat ON tbl_asat.id_proyek = simpro_current_budget_analisa_apek.id_proyek AND tbl_asat.kode_analisa = simpro_current_budget_analisa_apek.kode_analisa
										WHERE simpro_current_budget_analisa_apek.id_proyek = $idproyek and tanggal_kendali = '$tgl_rab'
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
								INNER JOIN simpro_current_budget_analisa_item_apek ON simpro_current_budget_analisa_item_apek.kode_analisa = tbl_asat_apek.parent_kode_analisa AND simpro_current_budget_analisa_item_apek.id_proyek = $idproyek and tanggal_kendali = '$tgl_rab'
								INNER JOIN simpro_current_budget_item_tree ON simpro_current_budget_item_tree.id_proyek = simpro_current_budget_analisa_item_apek.id_proyek AND simpro_current_budget_item_tree.kode_tree = simpro_current_budget_analisa_item_apek.kode_tree					
							) as tbl_total_koef 
							INNER JOIN (
								SELECT 
								DISTINCT(kode_material), 
								harga 
								FROM simpro_current_budget_analisa_asat
								WHERE id_proyek = $idproyek and tanggal_kendali = '$tgl_rab'
								GROUP BY kode_material,harga
							) as tbl_harga ON (tbl_harga.kode_material = tbl_total_koef.kode_material)
							INNER JOIN simpro_tbl_detail_material ON ( simpro_tbl_detail_material.detail_material_kode = tbl_total_koef.kode_material)
							INNER JOIN simpro_tbl_subbidang ON ( simpro_tbl_subbidang.subbidang_kode = LEFT(simpro_tbl_detail_material.detail_material_kode,3))
							where left(tbl_total_koef.kode_material,3)='$kode_subbidang'
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

			$q_data = $this->db->query($sql_data);

			if ($q_data->result()) {
				$total_sub = 0;
				foreach ($q_data->result() as $row) {					
				?>
				<tr>
					<td><?php echo $row->kd_material; ?></td>
					<td><?php echo $row->detail_material_nama; ?></td>
					<td><?php echo $row->detail_material_satuan; ?></td>
					<td><?php echo angka($row->koefisien); ?></td>
					<td><?php echo angka($row->harga); ?></td>
					<td><?php echo angka($row->subtotal); ?></td>
					<td><?php echo $row->detail_material_spesifikasi; ?></td>
				</tr>
				<?php 
				$total_sub += $row->subtotal;
				$total_data += $row->subtotal;
				}
				?>
				<tr>
					<td colspan="5"><b>TOTAL <?php echo $uraian_judul; ?><b></td>
					<td colspan="2"><?php echo angka($total_sub); ?></td>
				</tr>
				<?php
			}	

			} ?>
			<!-- <tr>
				<td></td>
				<td>xxxxxxxxxx</td>
				<td>0.00%</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr>
			<tr class="list_data">
				<td colspan="7">UPAH(501)</td>
			</tr>
			<tr>
				<td></td>
				<td>xxxxxxxxxx</td>
				<td>0.00%</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td>xxxxxxxxxx</td>
				<td>0.00%</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td>xxxxxxxxxx</td>
				<td>0.00%</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr>
			<tr class="list_data">
				<td colspan="7">PERALATAN(502)</td>
			</tr>
			<tr>
				<td></td>
				<td>xxxxxxxxxx</td>
				<td>0.00%</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr>
			<tr class="list_data">
				<td colspan="7">BIAYA BANK(504)</td>
			</tr>
			<tr>
				<td></td>
				<td>xxxxxxxxxx</td>
				<td>0.00%</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td>xxxxxxxxxx</td>
				<td>0.00%</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr>
			<tr class="list_data">
				<td colspan="7">BIAYA UMUM PROYEK(505)</td>
			</tr>
			<tr>
				<td></td>
				<td>xxxxxxxxxx</td>
				<td>0.00%</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr> -->
			<tr  class="list_data">
				<td></td>
				<td>TOTAL KESELURUHAN</td>
				<td colspan="5"><?php echo angka($total_data); ?></td>
			</tr>
			<tr  class="list_data">
				<td></td>
				<td>PROSENTASE THD KONTAK</td>
				<td colspan="5">0.00%</td>
			</tr>
		</tbody>																																																																																																								
	</table>
</body>
</html>