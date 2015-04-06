<?php
	$file_type="x-msexcel";
	$file_ending="xls";
	$dt = date('Y-m-d');
	header("Content-Type: application/$file_type");
	header("Content-Disposition: attachment; filename=RAT_$dt.$file_ending");
	header("Pragma: no-cache");
	header("Expires: 0");

	$ppn10persen = $data_tender['nilai_penawaran'] * 0.1;
?>
<style>

p {
    margin:5px;
	font-size: 10px;	
	font-family: sans-serif; 	
}

table, th, tr, td {
	border: 1px solid #cccccc;
	font-family: sans-serif; 	
	font-size: 12px;	
}

.new-tab {
    background-image:url(<?php echo base_url(); ?>assets/images/new_tab.gif) !important;
}

.icon-add {
    background-image:url(<?php echo base_url(); ?>assets/images/add.gif) !important;
}

.tabs {
    background-image:url(<?php echo base_url(); ?>assets/images/tabs.gif ) !important;
}

.task .x-grid-cell-inner {
	padding-left: 15px;
}
.x-grid-row-summary .x-grid-cell-inner {
	font-weight: bold;
	font-size: 11px;
}
.icon-grid {
	background: url(<?php echo base_url(); ?>assets/images/grid.png) no-repeat 0 -1px;
}
		
</style>
<!--
<script type="text/javascript">
    Ext.require(['*']);
	
    Ext.onReady(function() {
        Ext.QuickTips.init();
		
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		
	});
</script>
-->

<p align="center">
<table width="95%" cellpadding="2" cellspacing="0" border="1" align="center" style="background:#ffffff;">
<tr>
	<td colspan="5" bgcolor="#cccccc">Data Proyek</td>
</tr>
<tr>
	<td width="15%">Divisi</td>
	<td></td>
	<td width="20%"><?=$data_tender['divisi_name'];?></td>
	<td width="15%">Pagu</td>
	<td width="20%"><?=number_format($data_tender['nilai_pagu_proyek'],0);?></td>
</tr>
<tr>
	<td width="15%">Proyek</td>
	<td></td>
	<td width="20%"><?=$data_tender['nama_proyek'];?></td>
	<td width="15%">Nilai Kontrak (excl. PPN)</td>
	<td width="20%">
	<?=number_format($total,0,'.',',');?>
	</td>
</tr>
<tr>
	<td width="15%">Waktu Pelaksanaan</td>
	<td></td>
	<td width="20%"><?=$data_tender['waktu_pelaksanaan'];?> hari</td>
	<td width="15%">Nilai Kontrak (incl. PPN)</td>
	<td width="20%"><?=number_format($total_rab,0,'.',',')?></td>
</tr>
<tr>
	<td width="15%">Masa Pemeliharaan</td>
	<td></td>
	<td width="20%"><?=$data_tender['waktu_pemeliharaan'];?> hari</td>
	<td width="15%">Nilai Penawaran</td>
	<td width="20%"><?//number_format($data_tender['nilai_penawaran'],0);?></td>
</tr>
</table>
</p>
<p>
<table width="95%" cellpadding="2" cellspacing="0" align="center" style="background:#ffffff;">
<tr>
	<th width="15%" bgcolor="#cccccc">&nbsp;</th>
	<th width="15%" bgcolor="#cccccc">Item</th>
	<th width="35%" bgcolor="#cccccc">Uraian</th>
	<th width="10%" bgcolor="#cccccc">Diajukan</th>
	<th width="5%" bgcolor="#cccccc">% (bobot terhadap total kontrak)</th>
</tr>
<tr>
	<td>A. DIRECT COST</td>
	<td><strong>A.1. RAT</strong></td>
	<td>Biaya Konstruksi</td>
	<td align="right"><?=number_format($total_bk,0);?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan=3 align="right">SUBTOTAL (DIRECT COST)</td>
	<td align="right"><?=number_format($total_bk,0);?></td>
	<td align="center"><?=$persen_bk;?> %</td>
</tr>
<tr>
	<td>B. IN-DIRECT COST</td>
	<td colspan=4><strong>B.1. BANK</strong></td>
</tr>
<?php	
if($data_bank['total'])
{
					
foreach($data_bank['data'] as $db)
{		
	?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$db['icitem_bank']?></td>
	<td align="right"><?php echo number_format(round(($db['persentase'] * $nilai_kontrak) / 100),0,'.',','); ?></td>
	<td align="center"><?=$db['persentase']?> %</td>
</tr>
<?php 
} 
}
?>
<tr>
	<td>&nbsp;</td>
	<td colspan="4"><strong>B.2. ASURANSI</strong></td>		
</tr>
<?php	
if($data_asuransi['total'])
{
foreach($data_asuransi['data'] as $da)
{		
	?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$da['icitem_asuransi']?></td>
	<td align="right"><?php echo number_format(round(($da['persentase'] * $nilai_kontrak) / 100),0,'.',','); ?></td>
	<td align="center"><?=$da['persentase']?> %</td>
</tr>
<?php } } ?>
<tr>
	<td>&nbsp;</td>
	<td colspan="4"><strong>B.3. BIAYA UMUM</strong></td>
</tr>
<?php	
if($data_biaya_umum['total'])
{
foreach($data_biaya_umum['data'] as $bu)
{		
	?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$bu['icitem']?></td>
	<td align="right"><?=number_format($bu['subtotal'],0);?></td>
	<td align="center"><?php 
	if ($bu['subtotal'] == 0 || $nilai_kontrak == 0) {
		$numb = 0;
	} else {
		$numb = $bu['subtotal'] / $nilai_kontrak;
	}
	echo number_format($numb,2,'.',','); ?> %</td>
</tr>
<?php } } ?>
<tr>
	<td colspan=3 align="right">SUBTOTAL (INDIRECT COST)</td>
	<td align="right"><?=number_format(round($total_idc),0,'.',',');?></td>
	<td align="center"><?=number_format(round($persen_idc),0,'.',',');?> %</td>
</tr>
<tr>
	<td colspan=3 align="right">SUBTOTAL (DIRECT + INDIRECT)</td>
	<td align="right"><?=number_format($ab,0,'.',',');?></td>
	<td align="center"><?=number_format($persen_idc_bk,0,'.',',');?> %</td>
</tr>
<tr>
	<td>C. VARIABLE COST</td>
	<td>C.1.</td>
	<td>Biaya Resiko</td>
	<td align="right"><?=number_format(($varcost['biaya_resiko'] * $nilai_kontrak) / 100, 0,'.',',');?></td>
	<td align="center"><?=$persen_biaya_resiko;?> %</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>Biaya Pemasaran</td>
	<td align="right"><?=number_format(($varcost['biaya_pemasaran'] * $nilai_kontrak) / 100, 0,'.',',');?></td>
	<td align="center"><?=$persen_biaya_pemasaran;?> %</td>
</tr>
<!-- <tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>Kontingensi</td>
	<td align="right"><?=number_format(($varcost['contingency'] * $nilai_kontrak) / 100, 0,'.',',');?></td>
	<td align="center"><?=$persen_biaya_contingensi;?> %</td>
</tr> -->
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>Biaya Lain-lain</td>
	<td align="right"><?=number_format(($varcost['biaya_lain'] * $nilai_kontrak) / 100, 0,'.',',');?></td>
	<td align="center"><?=$persen_biaya_lain2;?> %</td>
</tr>
<tr>
	<td colspan=3 align="right">SUBTOTAL (C.1.)</td>
	<td align="right">
	<?php
		echo number_format($c1,0);
	?>
	</td>
	<td align="center"><?=$persen_c1;?> %</td>
</tr>
<tr>
	<td colspan=3 align="right">SUBTOTAL (DIRECT + INDIRECT + C.1.)</td>
	<td align="right">
	<?php
		echo number_format($abc1,0);
	?>
	</td>
	<td align="center"><?=$persen_abc1;?> %</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>C.2.</td>
	<td>PPH 3%</td>
	<td align="right"><?=number_format($pph3persen,0);?></td>
	<td align="center"><?=$persen_pph3persen;?> %</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>LAPEK</td>
	<td align="right"><?=number_format($lapek,0);?></td>
	<td align="center"><?=$persen_lapek;?> %</td>
</tr>
<tr>
	<td colspan=3 align="right">SUBTOTAL (C.2.)</td>
	<td align="right"><?php echo number_format($c2,0);?></td>
	<td align="center"><?=$persen_c2;?> %</td>
</tr>
<tr>
	<td colspan=3 align="right">SUBTOTAL (A + B + C)</td>
	<td align="right"><?php echo number_format($total,0); ?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan=5 align="right">&nbsp;</td>
</tr>
<tr>
	<td colspan=3 align="right">TOTAL</td>
	<td align="right">
	<?php
		echo number_format($total,0);
	?>
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan=3 align="right">PPN 10%</td>
	<td align="right">
	<?php 
		$ppn10 = $total * 0.10;
		echo number_format($ppn10, 0);
	;?>
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan=5 align="right">&nbsp;</td>
</tr>
<tr>
	<td colspan=3 align="right">R A B</td>
	<td align="right">
	<strong>
	<?php 
		$rab = $total + $ppn10;
		echo number_format($rab);
	?>
	</strong>
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan=5 align="right">&nbsp;</td>
</tr>
</table>
</p>