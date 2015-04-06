<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Laporan Biaya Proyek</title>
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
	<div class="titlegroup">REKAP LAPORAN PROYEK <?php echo strtoupper($tgl); ?> (RLP-01)</div>
	<div class="headgroup">		
		<div class="actions">
			<a href="<?=base_url();?>laporan/printed_rlp" title="Print"><span class="icon-print">Print</span></a>
		</div>
	</div>
	<div class="parameter">
		<table width="100%">
			<tr>
				<td width="20%">Unit Bisnis</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><b><?php echo $divisi->divisi_name; ?></b></td>
			</tr>									
		</table>
	</div>	
	<table id="cashflow-grid">
		<thead>
			<tr>
				<th>NO</th>
				<th>NAMA PROYEK</th>
				<th>PROGRESS</th>
				<th>P.U</th>	
				<th>B.K</th>
				<th>L.p</th>
				<th>CASH IN</th>
				<th>CASH OUT</th>
				<th>KET</th>		
			</tr>				
		</thead>	
		<tbody>
			<tr>
				<td>1</td>
				<td><?php echo $proyek->proyek; ?></td>
				<td><?php echo angka($row[0]['progress']); ?>%</td>
				<td><?php echo angka($row[0]['pu']); ?></td>
				<td><?php echo angka($row[0]['bk']); ?></td>
				<td><?php echo angka($row[0]['lp']); ?></td>
				<td><?php echo angka($row[0]['cashin']); ?></td>
				<td><?php echo angka($row[0]['cashout']); ?></td>
				<td></td>
			</tr>
			<tr  class="list_data">
				<td></td>
				<td>TOTAL</td>
				<td><?php echo angka($row[0]['progress']); ?>%</td>
				<td><?php echo angka($row[0]['pu']); ?></td>
				<td><?php echo angka($row[0]['bk']); ?></td>
				<td><?php echo angka($row[0]['lp']); ?></td>
				<td><?php echo angka($row[0]['cashin']); ?></td>
				<td><?php echo angka($row[0]['cashout']); ?></td>
				<td></td>
			</tr>
		</tbody>																																																																																																								
	</table>
</body>
</html>