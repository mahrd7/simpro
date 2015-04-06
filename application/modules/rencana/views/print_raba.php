<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>RAB(A)</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
</head>
<body>
	<div class="titlegroup">RAB(A)</div>
	<div class="headgroup">		
		<div class="actions">
			<a href="<?php echo base_url();?>rencana/rab/adjust_rab/<?php echo $idtender;?>" title="Back"><span class="icon-back">Back</span></a>&nbsp;
			<a href="#" title="Print" onClick="window.print();"><span class="icon-print">Print</span></a>
		</div>
	</div>
	<div class="parameter">
		<table width="100%">
			<tr><td colspan="3"><center><b><?php echo $data_proyek['nama_proyek'], ' - ', $data_proyek['lokasi_proyek']; ?></b></center></td></tr>
			<tr>
				<td width="15%">Divisi</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><b><?php echo $data_proyek['divisi_name'];?></b></td>
			</tr>
			<tr>
				<td>Proyek</td>
				<td align="center">:</td>
				<td><b><?php echo $data_proyek['nama_proyek'];?></b></td>
			</tr>			
			<tr>
				<td>Pagu</td>
				<td align="center">:</td>
				<td><b><?php echo number_format($data_proyek['nilai_pagu_proyek'],2);?></b></td>
			</tr>
			<tr>
				<td>Nilai Penawaran</td>
				<td align="center">:</td>
				<td><b><?php echo number_format($data_proyek['nilai_penawaran'],2);?></b></td>
			</tr>												
			<tr>
				<td> Nilai Kontrak (excl. PPN)</td>
				<td align="center">:</td>
				<td><b><?php echo number_format($data_proyek['nilai_kontrak_excl_ppn'],2);?></b></td>
			</tr>
			<tr>
				<td width="20%">Nilai Kontrak (incl. PPN)</td>
				<td align="center">:</td>
				<td><b><?php echo number_format($data_proyek['nilai_kontrak_ppn'],2);?></b></td>
			</tr>
			<tr>
				<td>Waktu Pelaksanaan</td>
				<td align="center">:</td>
				<td><b><?php echo $data_proyek['waktu_pelaksanaan'];?> hari</b></td>
			</tr>									
		</table>
	</div>			
	<table id="cashflow-grid" cellpadding="5" cellspacing="5">
		<thead>
			<tr>
				<th rowspan="2">NO</th>
				<th rowspan="2">KODE RAP</th>
				<th rowspan="2">KODE MATERIAL</th>
				<th rowspan="2">NAMA</th>
				<th rowspan="2">SATUAN</th>
				<th rowspan="2">VOLUME</th>
				<th rowspan="2">HARGA SATUAN</th>
				<th rowspan="2">SUBTOTAL</th>
			</tr>				
		</thead>	
		<tbody>
		<?php
			if(count($raba) > 0)
			{
				$i = 1;
				$subbidang = "";
				$subtotal = 0;
				$nextsub = "";
				for($a=0; $a < count($raba); $a++)
				{
					$sub = $raba[$a]['simpro_tbl_subbidang'];
					if($sub <> $subbidang)
					{
						echo '
						<tr>
							<td colspan="8" width="100%"><b>',$raba[$a]['simpro_tbl_subbidang'],'</b></td>
						</tr>
						';				
						$subbidang = $sub;
						$subtotal = $raba[$a]['subtotal'];
					} else 
					{
						$subtotal = $subtotal + $raba[$a]['subtotal'];
					}
					
					echo '
						<tr>
							<td width="2%">',$i,'</td>
							<td width="5%">',$raba[$a]['kode_rap'],'</td>
							<td width="5%">',$raba[$a]['kd_material'],'</td>
							<td width="30%">',$raba[$a]['detail_material_nama'],'</td>
							<td width="2%">',$raba[$a]['detail_material_satuan'],'</td>
							<td width="2%">',number_format($raba[$a]['total_volume'],2),'</td>
							<td width="5%" align="right">',number_format($raba[$a]['harga'],2),'</td>
							<td width="10%" align="right">',number_format($raba[$a]['subtotal'],2),'</td>
						</tr>					
					';
					$nextsub = isset($raba[$a+1]['simpro_tbl_subbidang']) ? $raba[$a+1]['simpro_tbl_subbidang'] : '';
					if($nextsub <> $sub)
					{
						echo '
						<tr>
							<td></td>						
							<td colspan="6" align="right"><b>SUBTOTAL&nbsp;</b></td>						
							<td align="right"><b>',number_format($subtotal,2),'<b></td>
						</tr>';				
					}
					$i++;
				}
			}
		?>
			<tr>
				<td>&nbsp;</td>
				<td colspan="6" align="right"><b>TOTAL RAB(A)&nbsp;</b></td>
				<td align="right"><b><?php echo $total_raba;?></b></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="6" align="right"><b>PERSENTASE TERHADAP KONTRAK&nbsp;</b></td>
				<td align="right"><b><?php echo $persen_raba;?>%</b></td>
			</tr>
		</tbody>																																																																																																								
	</table>
</body>
</html>