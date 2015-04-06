<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Laporan Biaya Proyek</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
</head>
<body>
	<div class="titlegroup">LAPORAN RENCANA DAN REALISASI DROPPING DANA BULAN April TAHUN 2013</div>
	<div class="headgroup">		
		<div class="actions">
			<a href="#" title="Print"><span class="icon-print">Print</span></a>
		</div>
	</div>
	<div class="parameter">
		<table width="100%">
			<tr>
				<td width="20%">PERIODE</td>
				<td width="2%" align="center">:</td>
				<td width="78%">
					<form>
						<select name="divisi">
							<option value="I">Divisi I</option>
						</select>
						<select name="bulan">
							<option value="1">Januari</option>
							<option value="2">Februari</option>
							<option value="3">Maret</option>
							<option value="4">April</option>
							<option value="5">Mei</option>
							<option value="6">Juni</option>
							<option value="7">Juli</option>
							<option value="8">Agustus</option>
							<option value="9">September</option>
							<option value="10">Oktober</option>
							<option value="11">November</option>
							<option value="12">Desember</option>
						</select>
						<select name="tahun">
							<?php
								for ($i=2000; $i <= 2060; $i++) { 
									echo '<option value="'.$i.'">'.$i.'</option>';
								}
							?>
						</select>
						<input type="button" value="Go">
					</form>
				</td>
			</tr>									
		</table>
	</div>	
	<table id="cashflow-grid">
		<thead>
			<tr>
				<th rowspan="4">No</th>
				<th rowspan="4">NAMA PROYEK</th>		
				<th colspan="15">BULAN NOVEMBER 2011</th>
				<th rowspan="4">Ket</th>	
			</tr>
				<th colspan="5">Realisasi Droppng Ke</th>
				<th colspan="6">Inflow Cair</th>	
				<th colspan="4">-</th>	
			<tr>	
				<th colspan="3">Divisi</th>	
				<th rowspan="2">Proyek</th>
				<th rowspan="2">Dev</th>
				<th colspan="3">Rencana</th>
				<th rowspan="2">Real cair</th>
				<th rowspan="2">Dikirim Ke Pst</th>
				<th rowspan="2">Kurang Cair</th>
				<th colspan="3">Progress</th>
				<th rowspan="2">SKBDN Jatuh Tempo</th>
			</tr>
			<tr>
				<th>SKBDN terbit</th>
				<th>TUNAI / CASH</th>
				<th>TOTAL</th>
				<th>SISA BLN LALU</th>
				<th>BULAN INI</th>
				<th>TOTAL</th>
				<th>Renc</th>
				<th>REAL</th>
				<th>DEV</th>		
			</tr>				
		</thead>	
		<tbody>
			<tr>
				<td>1</td>
				<td>DATA ANALISA-SUBKON-BTL-KODE TOKO & PEMBAYARAN</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>-</td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
			</tr>
			<tr>
				<td>2</td>
				<td>Pembangunan Masjid Agung Al Muhsinin Solok (Sondym Barkah)</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>-</td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
			</tr>
			<tr>
				<td>3</td>
				<td>Pembangunan Rusunawa Batam - 2 (Alif Usman)</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>-</td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
			</tr>
			<tr>
				<td>4</td>
				<td>Training:rsud rabain (Bambang Asmoro)</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>-</td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
			</tr>
			<tr>
				<td>5</td>
				<td>Training 5 (Eka Ermalia)</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>-</td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td></td>
				<td></td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
			</tr>
		</tbody>																																																																																																								
	</table>
</body>
</html>