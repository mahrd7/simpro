<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<title>Surat Pernyataan</title>
</head>
<body>
<div style="padding: 60px;">
<font size="5" face="comicsans"><b><center>SURAT PERNYATAAN</center></b></font>
<br>
<br>
<br>
<br>
<font size="3" face="comicsans">Yang bertanda tangan di bawah ini :</font>
<br>
<br>
<table border="0">
<tr>
<td width="80px"><font size="3" face="comicsans">Nama</font></td>
<td width="10px"><font size="3" face="comicsans"> : </font></td>
<td><font size="3" face="comicsans"><?php echo $nama; ?></font></td>
</tr>
<tr>
<td><font size="3" face="comicsans">&nbsp;</font></td>
</tr>
<tr>
<td width="80px"><font size="3" face="comicsans">Jabatan</font></td>
<td width="10px"><font size="3" face="comicsans"> : </font></td>
<td><font size="3" face="comicsans"><?php echo $jabatan; ?></font></td>
</tr>
<tr>
<td><font size="3" face="comicsans">&nbsp;</font></td>
</tr>
<tr>
<td width="80px"><font size="3" face="comicsans">Alamat</font></td>
<td width="10px"><font size="3" face="comicsans"> : </font></td>
<td><font size="3" face="comicsans"><?php echo $alamat; ?></font></td>
</tr>
</table>
<br>
<font size="3" face="comicsans">
<div id="text">
  <p align="justify">
  Menyatakan bahwa usulan Rencana BK (Beban Kontrak) yang saya sampaikan ini sudah dihitung dan dibuat dengan sebenar-benarnya, mengacu pada :
  </p><br>
  <p align="justify">
1. Hasil survey kelokasi proyek, termasuk survey area proyek, stok dan harga material, tenaga kerja, jalan kerja serta lingkungan kerja.
  </p><br>
  <p align="justify">
2. Untuk semua item kontrak Lump Sum, sudah menghitung ulang besaran kebutuhan volume pekerjaan dan seluruh anggaran biaya nya sesuai gambar pelaksanaan, spek teknis kontrak serta metode pelaksanaan yang benar.
  </p><br>
  <p align="justify">
3. Semua harga satuan Bahan, Upah, Peralatan dan Sub Kontraktor yang disampaikan sudah sesuai dengan harga penawaran dari calon rekanan (copy terlampir), spek teknis kontrak, metode pelaksanaan dan ketentuan pengadaan yang masih berlaku.
  </p><br>
  <p align="justify">
4. Semua Biaya Provisi/Bunga Bank, BAU Proyek, Perpajakan dan Penyusutan Alat serta Cash Flow yang disampaikan sudah sesuai dengan ketentuan/aturan yang berlaku,  Schedule Pelaksanaan, Schedule Tenaga Kerja, Schedule Peralatan, Schedule Tagihan (Cash In), Schedule pengadaan dan Schedule pembayaran (Cash out).
  </p><br>
  <p align="justify">
5. Sudah mengantisipasi dan memperhitungkan semua kemungkinan resiko yang akan terjadi sesuai pasal-pasal dalam kontrak yang berlaku, termasuk kemungkinan resiko lingkungan sosial.
  </p><br>
  <p align="justify">
6. Sudah mengetahui dan memperhitungkan semua peluang yang ada dari rencana pelaksanaan sesuai kontrak proyek ini.
  </p><br>
  <p align="justify">
Bila ada data-data perhitungan volume, analisa harga satuan, pasal-pasal kontrak, BOQ dan hasil Survey lapangan untuk kebutuhan perhitungan usulan BK awal yang dengan sengaja tidak saya sampaikan dan beresiko akan merugikan Perusahaan atau pihak lain karena ketidak benaran informasi dan data-data yang saya sampaikan maka saya bersedia ditindak tegas sesuai dengan Peraturan/ketentuan Perusahaan yang berlaku.
  </p><br>
  <p align="justify">
Demikian surat pernyataan ini saya buat dan tanda tangani dengan benar, penuh kesadaran tanpa ada tekanan dari pihak manapun, untuk dipergunakan sebagai mana mestinya.
  </p><br><br>
  <table width="100%" border="0">
    <tr>
      <td colspan="2" width="70%" height="20px"><font size="3" face="comicsans"></td>
      <td width="30%" height="20px"><font size="3" face="comicsans"><center>Jakarta, <?php echo $date; ?></center></font></td>
    </tr>
    <tr>
      <td width="40%" height="20px" colspan="3"></td>
    </tr>
    <tr>
      <td colspan="2" width="70%" height="20px"><font size="3" face="comicsans"><b><center>Mengetahui,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</center></b></font></td>
      <td width="30%" height="20px"><font size="3" face="comicsans"><b><center>KEPALA PROYEK</center></b></font></td>
    </tr>
    <tr>
      <td width="30%" height="100px"></td>
      <td width="40%" height="100px"></td>
      <td width="30%" height="100px"></td>
    </tr>
    <tr>
      <td width="30%" height="20px"><font size="3" face="comicsans">
      <center><u>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></center></font></td>
      <td width="40%" height="20px"><font size="3" face="comicsans">
      <center><u>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></center></font></td>
      <td width="30%" height="20px"><font size="3" face="comicsans">
      <center><u>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $nama; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </u></center></font></td>
    </tr>
    <tr>
      <td width="30%" height="20px"><font size="3" face="comicsans"><b><center>( General Manager)</center></b></font></td>
      <td width="40%" height="20px"><font size="3" face="comicsans"><b><center>( Manager Tekmas )</center></b></font></td>
      <td width="30%" height="20px"><font size="3" face="comicsans"><b><center>( Manager Proyek )</center></b></font></td>
    </tr>
  </table>
</font>
</div>
</div>
</body>
</html>