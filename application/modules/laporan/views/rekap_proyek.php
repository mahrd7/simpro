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

		function datediff($per,$d1,$d2) {
			$d = $d2-$d1;
			switch($per) {
				case "yyyy": $d/=12;
				case "m": $d*=12*7/365.25;
				case "ww": $d/=7;
				case "d": $d/=24;
				case "h": $d/=60;
				case "n": $d/=60;
			}
			return round($d);
		}

		$tgl_now=date("Y-m-d");
		$date_now=substr($tgl_now,8,2);
		$month_now=substr($tgl_now,5,2);
		$year_now=substr($tgl_now,0,4);

		$tglakhir = $proyek->berakhir;
		$date_akhir=substr($tglakhir,8,2);
		$month_akhir=substr($tglakhir,5,2);
		$year_akhir=substr($tglakhir,0,4);

		$d1=mktime(0,0,0,$month_akhir,$date_akhir,$year_akhir);//m-d-y
		$d2=mktime(0,0,0,$month_now,$date_now,$year_now);
		$jangka=datediff("d",$d2,$d1);
	?>
	<div class="titlegroup">REKAP PROYEK</div>
	<!-- <div class="headgroup">		
		<div class="actions">
			<a href="#" title="Print"><span class="icon-print">Print</span></a>
		</div>
	</div> -->
	<div class="parameter2">
		<!-- <table width="100%">
			<tr>
				<td width="20%">Unit Bisnis</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><b>Divisi X</b></td>
			</tr>
			<tr>
				<td width="20%">Nama Proyek</td>
				<td width="2%" align="center">:</td>
				<td width="78%"></td>
			</tr>									
		</table> -->
		<table width="100%">
			<tr>
				<td>
		<div id="wrapper_atas">
		<div id="data_umum">
			<div class="titlegroup">Data Umum</div>
			<div class="parameter2">
				<div id="data_inner">
				<table>
				<tr>
					<td>Nama</td>
					<td>:</td>
					<td><?php echo $proyek->proyek; ?></td>
				</tr>
				<tr>
					<td>Jo/Non Jo</td>
					<td>:</td>
					<td><?php $jo = $proyek->status_pekerjaan; if($jo =='1'){$tata='JO';}else if($jo=='2'){$tata='NON JO';} else{$tata='-';} echo $tata; ?></td>
				</tr>
				<tr>
					<td>Owner</td>
					<td>:</td>
					<td><?php $owner = $proyek->pemberi_kerja; if($owner<>''){$owner=$owner;}else{$owner='-';} echo $owner; ?></td>
				</tr>
				<tr>
					<td>Lokasi</td>
					<td>:</td>
					<td><?php $lokasi = $proyek->lokasi_proyek; if($lokasi<>''){$lokasi=$lokasi;}else{$lokasi='-';} echo $lokasi; ?></td>
				</tr>
				<tr>
					<td>Sumber Dana</td>
					<td>:</td>
					<td><?php $sumberdana = $proyek->proyek_nama_sumber_1; if($sumberdana<>''){$sumberdana=$sumberdana;}else{$sumberdana='-';} echo $sumberdana; ?></td>
				</tr>
				<tr>
					<td>Waktu Pelaksanaan</td>
					<td>:</td>
					<td><?php $waktu = $proyek->total_waktu_pelaksanaan; if($waktu<>''){$waktu=$waktu;}else{$waktu='-';} echo $waktu; ?></td>
				</tr>
				<tr>
					<td>Lingkup Kerja</td>
					<td>:</td>
					<td><?php $lingkup = $proyek->lingkup_pekerjaan; if($lingkup<>''){$lingkup=$lingkup;}else{$lingkup='-';} echo $lingkup; ?></td>
				</tr>
				<tr>
					<td>Kepala Proyek</td>
					<td>:</td>
					<td><?php $kapro = $proyek->kepala_proyek; if($kapro<>''){$kapro=$kapro;}else{$kapro='-';} echo $kapro; ?></td>
				</tr>
				<tr>
					<td>Termin</td>
					<td>:</td>
					<td><?php $termijn = $proyek->termijn; if($termijn<>''){$termijn=$termijn;}else{$termijn='-';} echo $termijn; ?></td>
				</tr>
				<tr>
					<td>Retensi</td>
					<td>:</td>
					<td><?php $retensi = $proyek->retensi; if($retensi<>''){$retensi=$retensi;}else{$retensi='-';} echo $retensi; ?></td>
				</tr>
				<tr>
					<td>TGL FHO</td>
					<td>:</td>
					<td><?php $fho = $proyek->tgl_tender; if($fho<>''){$fho=$fho;}else{$fho='-';} echo $fho; ?></td>
				</tr>
				<tr>
					<td>TGL PHO</td>
					<td>:</td>
					<td><?php $pho = $proyek->tgl_pengumuman; if($pho<>''){$pho=$pho;}else{$pho='-';} echo $pho; ?></td>
				</tr>
				<tr>
					<td>Eskalasi</td>
					<td>:</td>
					<td><?php $eskalasi = $proyek->eskalasi; if($eskalasi<>''){$eskalasi=$eskalasi;}else{$eskalasi='-';} echo $eskalasi; ?></td>
				</tr>
				</table>
				</div>
			</div>
		</div>
		<div id="rencana">
			<div class="titlegroup">Rencana</div>
			<div class="parameter2">
				<div id="data_inner">
					<table>
						<tr>
							<td>Nilai Kontrak</td>
							<td>:</td>
							<td><?php $kontrak = $proyek->nilai_kontrak_non_ppn; if($kontrak<>''){$kontrak=$kontrak;}else{$kontrak=0;} echo angka($kontrak); ?></td>
						</tr>
						<tr>
							<td>RAB</td>
							<td>:</td>
							<td><?php echo angka($total_rab); ?></td>
						</tr>
						<tr>
							<td>Anggaran BK</td>
							<td>:</td>
							<td><?php echo angka($total_rap); ?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div id="sisa_waktu">
			<div class="titlegroup">Sisa Waktu</div>
			<div class="parameter2">
				<div id="data_inner">
					<center><b><font size="4px">SISA PROGRESS</b></font></center>
					<br>
					<center><b><font size="80px" color="red"><?php echo $jangka.'/'.$waktu; ?></font><b></center>
					<br>
					<center><div class="hari_lagi"><b>HARI LAGI</b></div></center>
					<center><b>SISA PROGRESS 
					<font size="5px" color="red"><?php 
						if ($total_pek == 0) {
							$hasil_ = 0;
						} else {
							$hasil_= $total_pek / $total_rab *100; 
						}
						$hasil = 100 - $hasil_;
						echo $hasil;
					?>%</font></b></center>
				</div>
			</div>
		</div>
		</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="data_pengendalian">
			<div class="titlegroup">Pengendalian</div>
			<div class="parameter2">
				<div id="data_inner">
	<table id="cashflow-grid">
		<thead>
			<tr>
				<th rowspan="2">No</th>
				<th rowspan="2">Unit Usaha</th>
				<th rowspan="2">Progress(%)</th>
				<th rowspan="2">P.U</th>		
				<th rowspan="2">B.K</th>
				<th colspan="2">% BK/PU</th>
				<th rowspan="2">S.P</th>
				<th rowspan="2">MOS</th>
				<th rowspan="2">L.P</th>
				<th rowspan="2">Cash In</th>
				<th rowspan="2">Cash Out</th>	
			</tr>	
			<tr>
				<th>Awal</th>
				<th>SD Bln Ini</th>
			</tr>			
		</thead>	
		<tbody>
			<?php
				for ($i=0; $i <= 2; $i++) { 

			?>
			<tr>
				<td colspan="12"><b>Bulan <?php echo $row[$i]['bulan']; ?></b></td>
			</tr>
			<tr>
				<td><?php echo $i+1; ?></td>
				<td><?php echo $proyek->proyek; ?></td>
				<td><?php echo angka($row[$i]['hasil1']); ?></td>
				<td><?php echo angka($row[$i]['hasil2']); ?></td>
				<td><?php echo angka($row[$i]['hasil3']); ?></td>
				<td><?php echo angka($row[$i]['hasil4']); ?></td>
				<td><?php echo angka($row[$i]['hasil5']); ?></td>
				<td><?php echo angka($row[$i]['hasil6']); ?></td>
				<td><?php echo angka($row[$i]['hasil7']); ?></td>
				<td><?php echo angka($row[$i]['hasil8']); ?></td>
				<td><?php echo angka($row[$i]['hasil9']); ?></td>
				<td><?php echo angka($row[$i]['hasil10']); ?></td>
			</tr>
			<?php
				}
			?>
			<!--<tr>
				<td colspan="12"><b>Bulan Maret</b></td>
			</tr>
			<tr>
				<td>2</td>
				<td>Irigasi Namu Sira-sira (JO 51%)</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
			</tr>
			<tr>
				<td colspan="12"><b>Bulan April</b></td>
			</tr>
			<tr>
				<td>3</td>
				<td>Irigasi Namu Sira-sira (JO 51%)</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
				<td>0.00</td>
			</tr>
		-->
		</tbody>																																																																																																								
	</table>
				</div>
			</div>
		</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="data_struktur">
			<div class="titlegroup">Struktur Organisasi</div>
			<div class="parameter2">
				<?php
					$strukturorganisasi = $proyek->struktur_organisasi;
					if ($strukturorganisasi == '') {
						$strukturorganisasi = 'no-image.jpg';
					} else {
						$strukturorganisasi = $strukturorganisasi;
					}
				?>
				<center><img src="<?php echo base_url(); ?>uploads/<?php echo $strukturorganisasi; ?>"></center>
			</div>
		</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="data_sketsa">
			<div class="titlegroup">Sketsa Proyek</div>
			<div class="parameter2">
				<center>
				<?php
					$sql_sketsa="select * from simpro_tbl_sketsa_proyek where proyek_id=$proyek->proyek_id order by foto_no desc limit 2";
                    $q_sketsa = $this->db->query($sql_sketsa);
                    if ($q_sketsa->result()) {
                    	foreach ($q_sketsa->result() as $r) { ?>
                    		<img src="<?php echo base_url(); ?>uploads/<?php echo $r->foto_proyek_file ;?>">	
                    	<?php }
                    } else {
                    	echo "<img src='".base_url()."uploads/no-image.jpg'>";	
                    }
				?>
				</center>
			</div>
		</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="data_foto">
			<div class="titlegroup">Foto Proyek</div>
			<div class="parameter2">
				<center>
				<?php
					$sql_foto="select * from simpro_tbl_foto_proyek where proyek_id='$proyek->proyek_id' order by foto_proyek_id desc limit 2";
                  	$q_foto = $this->db->query($sql_foto);
                    if ($q_foto->result()) {
                    	foreach ($q_foto->result() as $r_f) { ?>
                    		<img src="<?php echo base_url(); ?>uploads/<?php echo $r_f->foto_proyek_file ;?>">	
                    	<?php }
                    } else {
                    	echo "<img src='".base_url()."assets/images/no-image.jpg'>";	
                    }  
				?>
				</center>
			</div>
		</div>
		</td>
	</tr>
</table>
	</div>	
	
</body>
</html>