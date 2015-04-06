<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Antisipasi Grid</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
</head>
<body>
	<div class="titlegroup">Antisipasi</div>
	<div class="headgroup">
		<form class="antisipasi">
			Pengelompokan Berdasar: <input type="text" size="20" />
			<select name="pilihan">
				<option value="pilihan">Pilihan</option>
			</select>
			Periode :
			<select name="periodetgl">
				<option value="1">1</option>
			</select>
			<select name="periodebln">
				<option value="april">April</option>
			</select>	
			<select name="periodethn">
				<option value="2013">2013</option>
			</select>		
			s/d :		
			<select name="periodetgl">
				<option value="1">1</option>
			</select>
			<select name="periodebln">
				<option value="april">April</option>
			</select>	
			<select name="periodethn">
				<option value="2013">2013</option>
			</select>
			<input type="submit" value="Go >">									
		</form>
		
		<div class="actions">
			<a href="#" title="Delete"><span class="icon-delete">Delete</span></a>
			<a href="#" title="Print"><span class="icon-print">Print</span></a>
		</div>
	</div>
	<table id="antisipasi-grid">
		<thead>
			<tr>
				<th scope="col">NO Bukti</td>
				<th scope="col">Kode Toko</td>
				<th scope="col">Nama Toko</td>
				<th scope="col">KODE</td>
				<th scope="col">Tanggal</td>
				<th scope="col">Nama</td>
				<th scope="col">Uraian</td>
				<th scope="col">Volume</td>
				<th scope="col">Jumlah</td>
				<th scope="col">Kontrol</td>
			</tr>
		</thead>
		
		<tbody>
			<tr>
				<td colspan="10">Material (500)</td>
			</tr>
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>
			<tr>
				<td colspan="10">Upah (501)</td>
			</tr>
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>
			<tr>
				<td colspan="10">Peralatan (502)</td>
			</tr>
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>
			<tr>
				<td colspan="10">Biaya Subkontraktor (503)</td>
			</tr>
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>		
			<tr>
				<td colspan="10">Biaya Bank (504)</td>
			</tr>
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>
			<tr>
				<td colspan="10">Biaya Umum Proyek (505)</td>
			</tr>																				
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>
			<tr>
				<td colspan="10">Rupa rupa (506)</td>
			</tr>																				
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>
			<tr>
				<td colspan="10">PPH Final</td>
			</tr>																				
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>
			<tr>
				<td colspan="10">Persiapan Penyelesaian</td>
			</tr>																				
			<tr class="odd">
				<td colspan="10">0.0</td>
			</tr>
			<tr class="odd">
				<td colspan="9" class="count">Total</td>
				<td>0.0</td>
			</tr>
		</tbody>
		
		<tfoot>
			<tr class="foots">
				<td colspan="10" class="content"><a href="#"><span class="icons-add">Tambah Data</span></a></td>
			</tr>
		</tfoot>								
	</table>
</body>
</html>