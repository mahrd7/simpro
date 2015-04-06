<?php

$file_type="x-msexcel";
$file_ending="xls";
                        foreach ($proyek as $proyeks) {
                            echo $proyeks->proyek;
                        }
header("Content-Type: application/$file_type");
header("Content-Disposition: attachment; filename=LBP-01-$proyeks->proyek.$file_ending");
header("Pragma: no-cache");
header("Expires: 0");

$cekada="select * from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id'";
$rescekada= $this->db->query($cekada);
$hasilcekada= $rescekada->num_rows();


if($hasilcekada>0)
{

function combo_bulan($nama,$sel){
    
    $bulan[1]="January";
    $bulan[2]="February";
    $bulan[3]="March";
    $bulan[4]="April";
    $bulan[5]="May";
    $bulan[6]="Juni";
    $bulan[7]="July";
    $bulan[8]="August";
    $bulan[9]="September";
    $bulan[10]="October";
    $bulan[11]="November";
    $bulan[12]="December";

    $txtout="<select name=$nama style=\"background-color: #FFFFFF\" class=\"tombol\">";
    for ($i=1;$i<=12;$i++){
        $tulis = "";
        if ($i==$sel){
            $tulis =" selected";
        }

        if($i==10 || $i==11|| $i==12){
            $itampil = $i;
        } else {
            $itampil = "0".$i;
        }

        $txtout .= "<option value='$itampil' $tulis>".$bulan[$i]."</option>";
        
    }
    $txtout .= "</select>";
    return $txtout;
}

function combo_tanggal($nama,$sel,$awal,$akhir){
    $txtout="<select name=$nama style=\"background-color: #FFFFFF\" class=\"tombol\">";
    for ($i=$awal;$i<=$akhir;$i++){
        $tulis = "";
        if ($i==$sel){
            $tulis =" selected";
        }
        $txtout .= "<option value=$i $tulis>".$i."</option>";
        
    }
    $txtout .= "</select>";
    return $txtout;
}


$bulan_ = date("m");
$tahun_ = date("Y");

if(isset($_GET['bulan_'])){
    $bulan_ = $_GET['bulan_'];
}
else if($bulan_==""){
    $bulan_=date('m');
}
else{
    $bulan_=$bulan_;
    }
if(isset($_GET['tahun_'])){
    $tahun_ = $_GET['tahun_'];
    }
else if($tahun_==""){
    $tahun_=date('Y');
}else{
    $tahun_=$tahun_;
}

if($bulan_==10 ||$bulan_==11||$bulan_==12){
    $tgl_lpp=$tahun_."-".$bulan_."-"."01";
}
else{
    $tgl_lpp=$tahun_."-".$bulan_."-"."01";
}
$tanggal=$tgl_lpp;

$tglbrutolalu=$tahun_."/".$bulan_."/"."31";
//TGL S/D BULAN LALU

    $tgl_="select distinct(tahap_tanggal_kendali)as tgl from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' ORDER BY tahap_tanggal_kendali DESC";
    $reskkon=$this->db->query($tgl_);

    foreach ($reskkon->result_array() as $val) {
    	$hasiltglkon = $val["tgl"];
    }

    $cekjudul=substr($tanggal,0,7);
    $cektgl=substr($hasiltglkon,0,7);

    if($cekjudul == $cektgl){
        $JUDUL="ANGGARAN KONTRAK ASLI";
        
    }
    else{
        $JUDUL="KONTRAK ASLI";
    }

    $tglcek="select tahap_tanggal_kendali from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id'";//and tahap_tanggal_kendali='$tanggal'
    $rescek= $this->db->query($tglcek);
    $hasilcek= $rescek->num_rows();  
    $tglkontrak= $rescek->row();
        if($hasilcek == 0){
              $tgl_lpp = $hasiltglkon;
        }
        else{
            
             $tgl_lpp = $tgl_lpp;
        }
        
        
        if($tgl_lpp < $hasiltglkon ){
            
              $tgl_lppblnll_ =  $hasiltglkon;
                   
        }    
        else{   
            if($bulan_ =="1"){
                $blns_=12;
            	$thns= $tahun_ - 1;
                 $tgl_lppblnll_= $thns."-".$blns_."-01";
                }
            else{
                $bulanll=$bulan_ - 1;
                if($bulanll ==10 ||$bulanll ==11){
                $tgl_lppblnll_ =$tahun_."-".$bulanll."-"."01";
                }
                else{
                    $tgl_lppblnll_ =$tahun_."-0".$bulanll."-"."01";
                }
            }
        }

	$rr=preg_split("/-/", $tgl_lpp);
    $lalu=preg_split("/-/", $tgl_lppblnll_);
	$bln=$rr[1];
	$blnlalu=$lalu[1];
	for ($j=1;$j<=12;$j++){
		if ($j==$bln){
		 $bln=$j;
         $blndpn = $j+1;
         if($blndpn==13){$blndpn=1;}
         $blnlalu=$j-1;
         if($blnlalu==0 ){
            $blnlalu=12;
         }
		}
	}

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

    $blntgl_lpp=substr($tanggal,5,2) + 1 ;
    $thntgl_lpp=substr($tanggal,0,4);
    if($blntgl_lpp == 13){
        $blntgl_lpp = 01;
        $thntgl_lpp = $thntgl_lpp + 1;
        $tanggal_depan=$thntgl_lpp."-".$blntgl_lpp."-"."01";
    }
    else{
        $blntgl_lpp=$blntgl_lpp;
        $thntgl_lpp = $thntgl_lpp;
        $tanggal_depan=$thntgl_lpp."-".$blntgl_lpp."-"."01";
    }
    
    
    $bulan=substr($tanggal,5,2);
    $tahun=substr($tanggal,0,4);
   
    $blnll=$bulan - 1;
    if($blnll == 0){
        $blnll=12;
        $thnll=$tahun_ -1;
      
    }
    else{
        $blnll=$blnll;
        $thnll=$tahun_;
        
    }
    
    if($blnll==10 ||$blnll==11||$blnll==12){
        $tanggal_lalu=$thnll."-".$blnll."-"."31";
    }
    else{
        $tanggal_lalu=$thnll."-0".$blnll."-"."31";
    }
    
    $bulan_lalu=substr($tanggal_lalu,5,2);
    $tahun_lalu=substr($tanggal_lalu,0,4);
    
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

    function dateadd($per,$n,$d) {
		switch($per) {
			case "yyyy": $n*=12;
			case "m":
				$d=mktime(date("H",$d),date("i",$d)
						,date("s",$d),date("n",$d)+$n
						,date("j",$d),date("Y",$d));
			$n=0; break;
			case "ww": $n*=7;
			case "d": $n*=24;
			case "h": $n*=60;
			case "n": $n*=60;
		}
		return $d+$n;
	}
    
    $hari_lalu=e_getTotalDayGlobal($tahun_lalu,$bulan_lalu);
    $tgl_lalu=$tahun_lalu."-".$bulan_lalu."-".$hari_lalu;
    
    $hari_ini=e_getTotalDayGlobal($tahun,$bulan);
    $tgl_ini=$tahun."-".$bulan."-".$hari_ini;

    foreach ($proyek as $proyeks) {
    	$arr_tgl_fho=preg_split("/-/",$proyeks->berakhir);
    	$pho=dateadd('day',30,$proyeks->berakhir);
    }

    $bl[01]="January";
    $bl[02]="February";
    $bl[03]="March";
    $bl[04]="April";
    $bl[05]="May";
    $bl[06]="Juni";
    $bl[07]="July";
    $bl[08]="August";
    $bl[09]="September";
    $bl[10]="October";
    $bl[11]="November";
    $bl[12]="December";

    $fho=$arr_tgl_fho[2]."-".$arr_tgl_fho[1]."-".$arr_tgl_fho[0];

    foreach ($proyek as $proyeks) {
    	$jo=$proyeks->status_pekerjaan;
    }

    
    if($jo=='1'){
        $status='JO';
        foreach ($proyek as $proyeks) {
	    	$porsi=$proyeks->sts_pekerjaan;
	    }        
    }
    else{
        $status='NON JO';
        $porsi=100;
    }

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Laporan Biaya Proyek</title>
    <style type="text/css">
    body
{
    /*line-height: 1.6em;*/
    line-height:20px;
}

div.titlegroup{height:25px;line-height: 25px;font-size:11px;font-weight:bold;color:#04468c;font-family:tahoma,arial,verdana,sans-serif;
    padding-left:10px;
    border-radius:4px 4px 0 0;
    -moz-border-radius:4px 4px 0 0;
    -webkit-border-radius:4px 4px 0 0;
    background: #deefff; /* Old browsers */
    background: -moz-linear-gradient(top,  #deefff 0%, #98bede 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#deefff), color-stop(100%,#98bede)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #deefff 0%,#98bede 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #deefff 0%,#98bede 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  #deefff 0%,#98bede 100%); /* IE10+ */
    background: linear-gradient(to bottom,  #deefff 0%,#98bede 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#deefff', endColorstr='#98bede',GradientType=0 ); /* IE6-9 */
    border-left:1px solid #69c;
    border-right:1px solid #69c;
    border-top:1px solid #69c;
}
div.headgroup{height:30px;line-height:30px;
    padding:0 10px 0 10px;
    font-size:11px;
    font-family: tahoma, arial, verdana, sans-serif;
    background:-webkit-gradient(linear, 50% 0, 50% 100%, from(#DFE9F5), to(#D3E1F1));
    background: -webkit-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -moz-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -ms-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -o-linear-gradient(top, #D3E1F1, #DFE9F5);
    border-left:1px solid #69c;
    border-right:1px solid #69c;
    border-top:1px solid #69c;
    position: relative;
}
div.headgroup form.antisipasi {display: inline-block;float:left}
div.headgroup form.rincian_hutang {display:inline-block}
div.headgroup .deskripsi{float:left;display:inline-block;margin-right:20px;font-weight:bold}
div.headgroup .actions{float:right}
div.headgroup .actions a{text-decoration:none;color:#000000;margin-right:5px;}

div.headgroup .actions span.icon-delete {
    background:url(images/delete.png) no-repeat 0 4px;
    padding:5px 5px 5px 20px;
    text-align:left;
}
div.headgroup .actions span.icon-print {
    background:url(images/print.png) no-repeat 0 4px;
    padding:5px 5px 5px 20px;
    text-align:left;
}
div.headgroup .actions span.icon-back {
    background:url(images/back.png) no-repeat 0 4px;
    padding:5px 5px 5px 20px;
    text-align:left;
}
div.parameter{min-height:25px;
    padding:0 10px 0 10px;
    font-size:11px;
    font-family: tahoma, arial, verdana, sans-serif;
    background:white;
    border-left:1px solid #69c;
    border-right:1px solid #69c;
    border-top:1px solid #69c;
    position: relative;
}

div.parameter2{min-height:50px;
    padding:5px;
    font-size:11px;
    font-family: tahoma, arial, verdana, sans-serif;
    background:white;
    border:1px solid #69c;
    position: relative;
    border-radius:0 0 4px 4px;
}
div.parameter .title{width:30%;float:left;display:inline-block}
#antisipasi-grid
{
    font-family: tahoma, arial, verdana, sans-serif;
    font-size: 11px;
    margin: 0 0 10px 0;
    width: 100%;
    text-align: left;
    border-collapse: collapse;
    border:1px solid #69c;
}
#antisipasi-grid th
{
    font-size: 11px;
    font-weight: normal;
    padding: 0 8px;
    color: #000;
    background:#d0dafd; 
    border-right:1px solid #ccc;
    text-align: center;
    background: #ffffff; /* Old browsers */
    background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#e5e5e5)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* IE10+ */
    background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
    
}
#antisipasi-grid th:last-child {border:none;}
#antisipasi-grid td
{
    padding: 0;
    color: #000;
    padding-right:10px;

}
#antisipasi-grid tr:nth-child(2n+2)
{
    text-align:right;
}
#antisipasi-grid tr:nth-child(2n+1)
{
    text-align:center;
}
#antisipasi-grid .odd
{
    background: #fafafa; 
}
#antisipasi-grid .count
{
    text-align:right;
    border-top: 1px solid #D0D0D0;
    border-right: 1px solid #D0D0D0;
}
#antisipasi-grid .foots {
    padding:0 10px 0 10px;
    font-size:11px;
    font-family: tahoma, arial, verdana, sans-serif;
    background:-webkit-gradient(linear, 50% 0, 50% 100%, from(#DFE9F5), to(#D3E1F1));
    background: -webkit-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -moz-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -ms-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -o-linear-gradient(top, #D3E1F1, #DFE9F5);
    border-left:1px solid #69c;
    border-right:1px solid #69c;
    border-top:1px solid #69c;  
}
#antisipasi-grid .foots .content {text-align:left;padding:0 10px 0 10px;line-height:30px;height:30px;}
#antisipasi-grid .foots .content a {text-decoration:none;color:#000000}
#antisipasi-grid .icons-add{
    background:url(images/add.png) no-repeat 0 4px;
    padding:5px 5px 5px 20px;
    text-align:left;
}

/* Rincian Hutang Grid */
#rincian-hutang-grid
{
    font-family: tahoma, arial, verdana, sans-serif;
    font-size: 11px;
    margin: 0 0 10px 0;
    width: 100%;
    text-align: left;
    border-collapse: collapse;
    border:1px solid #69c;
}
#rincian-hutang-grid th
{
    font-size: 11px;
    font-weight: normal;
    padding: 0 8px;
    color: #000;
    background:#d0dafd; 
    border-right:1px solid #ccc;
    text-align: center;
    background: #ffffff; /* Old browsers */
    background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#e5e5e5)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* IE10+ */
    background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
    
}
#rincian-hutang-grid th:last-child {border:none;}
#rincian-hutang-grid td
{
    padding: 0;
    color: #000;
    padding-left:10px;
    border-right:1px solid #ccc;
    border-bottom:1px solid #ccc;

}
#rincian-hutang-grid td:last-child{border-right: none}

#rincian-hutang-grid .odd
{
    background: #fafafa; 
}
#rincian-hutang-grid .title_list
{
    background: #CCCCCC;
    text-align:left;
    padding-left:20px;
    font-weight:bold; 
}
#rincian-hutang-grid .count
{
    text-align:right;
    border-top: 1px solid #D0D0D0;
    border-right: 1px solid #D0D0D0;
}
#rincian-hutang-grid .foots {
    padding:0 10px 0 10px;
    font-size:11px;
    font-family: tahoma, arial, verdana, sans-serif;
    background:-webkit-gradient(linear, 50% 0, 50% 100%, from(#DFE9F5), to(#D3E1F1));
    background: -webkit-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -moz-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -ms-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -o-linear-gradient(top, #D3E1F1, #DFE9F5);
}
#rincian-hutang-grid .foots .content {text-align:center;padding:0 10px 0 10px;line-height:30px;height:30px;border:1px solid #69c;}
#rincian-hutang-grid .foots .content a {text-decoration:none;color:#000000}
#rincian-hutang-grid .icons-reload{
    background:url(images/reload.png) no-repeat 0 4px;
    padding:5px 5px 5px 20px;
    text-align:center;
}

/* Cashflow Grid */
#cashflow-grid
{
    font-family: tahoma, arial, verdana, sans-serif;
    font-size: 11px;
    margin: 0 0 10px 0;
    width: 100%;
    text-align: left;
    border-collapse: collapse;
    border:1px solid #69c;
}
#cashflow-grid th
{
    font-size: 11px;
    font-weight: normal;
    padding: 0 8px;
    color: #000;
    background:#d0dafd; 
    border-right:1px solid #ccc;
    text-align: center;
    background: #ffffff; /* Old browsers */
    background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#e5e5e5)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* IE10+ */
    background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
    
}
#cashflow-grid th:last-child {border-right:1px solid #69c;}
#cashflow-grid td
{
    padding: 0;
    color: #000;
    padding-left:10px;
    border-right:1px solid #ccc;
    border-bottom:1px solid #ccc;

}
#cashflow-grid td:last-child{border-right: 1px solid #69c}

#cashflow-grid .odd
{
    background: #fafafa; 
}
#cashflow-grid .title_list
{
    background: #CCCCCC;
    text-align:left;
    padding-left:20px;
    font-weight:bold; 
}
#cashflow-grid .list_data
{
    background: #CCCCCC;
    font-weight:bold;
}

#cashflow-grid .list_data_header
{
    background: #E9E7E7;
    font-weight:bold;
}

#cashflow-grid .list_data_induk
{
    background: #9E9E9E;
    font-weight:bold;
}

#cashflow-grid .list_data_grand
{
    background: #635F5F;
    font-weight:bold;
}

#cashflow-grid .count
{
    text-align:right;
    border-top: 1px solid #D0D0D0;
    border-right: 1px solid #D0D0D0;
}
#cashflow-grid .foots {
    padding:0 10px 0 10px;
    font-size:11px;
    font-family: tahoma, arial, verdana, sans-serif;
    background:-webkit-gradient(linear, 50% 0, 50% 100%, from(#DFE9F5), to(#D3E1F1));
    background: -webkit-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -moz-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -ms-linear-gradient(top, #D3E1F1, #DFE9F5);
    background: -o-linear-gradient(top, #D3E1F1, #DFE9F5);
}
#cashflow-grid .foots .content {text-align:center;padding:0 10px 0 10px;line-height:30px;height:30px;border:1px solid #69c;}
#cashflow-grid .foots .content a {text-decoration:none;color:#000000}
#cashflow-grid .icons-reload{
    background:url(images/reload.png) no-repeat 0 4px;
    padding:5px 5px 5px 20px;
    text-align:center;
}
input[type="submit"]{
    background: #ffffff; /* Old browsers */
    background: -moz-linear-gradient(top,  #ffffff 0%, #f1f1f1 50%, #e1e1e1 51%, #f6f6f6 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(50%,#f1f1f1), color-stop(51%,#e1e1e1), color-stop(100%,#f6f6f6)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #ffffff 0%,#f1f1f1 50%,#e1e1e1 51%,#f6f6f6 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #ffffff 0%,#f1f1f1 50%,#e1e1e1 51%,#f6f6f6 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  #ffffff 0%,#f1f1f1 50%,#e1e1e1 51%,#f6f6f6 100%); /* IE10+ */
    background: linear-gradient(to bottom,  #ffffff 0%,#f1f1f1 50%,#e1e1e1 51%,#f6f6f6 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f6f6f6',GradientType=0 ); /* IE6-9 */
    border:1px solid #d1d1d1;
    border-radius:3px;
    -moz-border-radius:3px;
    -webkit-border-radius:3px;
    min-width:25px;

}

.antisipasi input[type="text"]{
    width: 110px;
}

div#titlegroup{
    width: 1390px;
}

div#headgroup{
    width: 1380px;
}

div#parameter{
    width: 1380px;
}

div#wrapper_atas{
    padding: 5px; 
}

#data_umum{
    width: 57%;
    float: left;
    margin: 4px;
}

#rencana{
    width: 20%;
    float: left;
    margin: 4px;
}

#sisa_waktu{
    width: 20%;
    float: left;
    margin: 4px;
}

#data_pengendalian{
    width: 98%;
    margin: 9px;
}

#data_struktur{
    width: 98%;
    margin: 9px;
}

#data_sketsa{
    width: 98%;
    margin: 9px;
}

#data_foto{
    width: 98%;
    margin: 9px;
}

#data_inner{    
    height: 160px;
    overflow:auto;
}

.hari_lagi{
    padding-bottom: 10px;
}
    </style>
</head>
<body>
	<div><font size="6">Laporan Biaya Proyek (LBP-01) Bulan <?php echo $bl[$bln] ." ". $tahun_; ?></font></div>
	<div>&nbsp;</div>
    <div class="parameter">
		<table width="100%">
			<tr>
				<td width="20%">Nama Proyek</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><b>
					<?php
						foreach ($proyek as $proyeks) {
							echo $proyeks->proyek;
						}
					?>
				</b></td>
                <td colspan="10"></td>
			</tr>
			<tr>
				<td width="20%">Pemilik Proyek</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><b>
					<?php
						foreach ($proyek as $proyeks) {
							echo $proyeks->pemberi_kerja;
						}
					?>
				</b></td>
                <td colspan="10"></td>
			</tr>	
			<tr>
				<td width="20%">Pembayaran</td>
				<td width="2%" align="center">:</td>
				<td width="78%">UANG MUKA : <strong>
					<?php
						foreach ($proyek as $proyeks) {
							echo $proyeks->uang_muka." %";
						}
					?>
				</strong> &nbsp;&nbsp;&nbsp; TERMIJN : <strong>
					<?php
						foreach ($proyek as $proyeks) {
							echo $proyeks->termijn;
						}
					?>
				</strong></td>
                <td colspan="10"></td>
			</tr>	
			<tr>
				<td width="20%">Sumber Dana</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><strong>
					<?php
						foreach ($proyek as $proyeks) {
							echo $proyeks->proyek_nama_sumber_1;
						}
					?>
					</strong>&nbsp;&nbsp;&nbsp;&nbsp;
					FHO : <strong>
					<?php echo $fho; ?>
					</strong>&nbsp;&nbsp;
					PROYEK JO/NON JO : <strong>
					<?php echo $status; ?>
					</strong>&nbsp;&nbsp;
					Porsi JO,NINDYA=&nbsp;<strong>
					<?php echo $porsi; ?>
					</strong>%
				</td>
                <td colspan="10"></td>
			</tr>
			<tr>
				<td width="20%">Total Waktu Pelaksanaan (HARI)</td>
				<td width="2%" align="center">:</td>
				<td width="78%"><strong>
					<?php
						foreach ($proyek as $proyeks) {
							echo $proyeks->total_waktu_pelaksanaan;
						}
					?>
					</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					PHO : <strong> <?php echo $fho; ?> </strong>&nbsp;&nbsp;
					POTONGAN PPH FINAL &nbsp;<strong>
					<?php
						foreach ($proyek as $proyeks) {
							echo $proyeks->pph_final;
						}
					?>&nbsp;%</strong>
				</td>
                <td colspan="10"></td>
			</tr>									
		</table>
	</div>	
	<table id="cashflow-grid">
			<tr class="list_data_header">
				<td rowspan="2">No</td>
				<td rowspan="2">Uraian</td>
				<td rowspan="2"><?php echo $JUDUL; ?></td>
				<td rowspan="2">SD.Bulan <?php echo $bl[$blnlalu]; ?></td>
				<td colspan="7">Realisasi Bulan <?php echo $bl[$bln]; ?></td>
				<td rowspan="2">Rencana Bulan <td /><?php echo $bl[$blndpn]; ?></td>
				<td rowspan="2">Keterangan</td>				
			</tr>
			<tr class="list_data_header">
				<td>Rencana</td>
				<td>Realisasi</td>
				<td>Deviasi</td>
				<td>SD.Bulan ini</td>
				<td>Sisa Anggaran</td>
				<td>Perkiraan <td />SD.Selesai</td>
				<td>Deviasi Total</td>
			</tr>
		
	<?php

	//=======================PROGRESS===================================================================//
    /*$sqlkini="select sum(tahap_harga_satuan_kendali * tahap_volume_kendali)as total from tbl_input_kontrak where no_spk='$cnospk_pilih' and tahap_kode_induk_kendali !='' ";
	$reskini=dbresult($sqlkini);//echo "$sql1<br>";
	   $rkini=pg_fetch_array($reskini);
       $totalkini=$rkini[total];*/
    
    $sqlkini="select sum(tahap_harga_satuan_kendali * tahap_volume_kendali)as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' "; //and tahap_kode_induk_kendali !='' 
	$reskini= $this->db->query($sqlkini);//echo "$sql1<br>";
	   $rkini= $reskini->result_array();
	   foreach ($rkini as $rkinis) {
	   		$totalkini=$rkinis['total'];
	   }

    //===================MENCARI PROGRES SD/BLN INI===================================//
    $blnprogll=substr($tgl_lpp,5,2);
    $thnprogll=substr($tgl_lpp,0,4);
    $sqlprogress="select a.tahap_diakui_bobot,b.tahap_harga_satuan_kendali from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  date_part('month',a.tahap_tanggal_kendali) <= '$bulan' and date_part('year',a.tahap_tanggal_kendali) <='$tahun'  and a.tahap_diakui_bobot!=0";
    $resprogress= $this->db->query($sqlprogress);

    $hasilprog__ = 0;

    foreach ($resprogress->result_array() as $hasilprogress){ 
        $hasilprog=$hasilprogress['tahap_diakui_bobot'];
        $hrg=$hasilprogress['tahap_harga_satuan_kendali'];
        $hasilprog_= $hasilprog * $hrg;
        $hasilprog__ += $hasilprog_;
    }
    //=================MENCARI PROGRESS S/D BLN LALU  ===============================//
   
    $sqlprogressll="select a.tahap_diakui_bobot,b.tahap_harga_satuan_kendali from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  date_part('month',a.tahap_tanggal_kendali) <= '$bulan_lalu' and date_part('year',a.tahap_tanggal_kendali) <='$tahun_lalu'  and a.tahap_diakui_bobot!=0";
    $resprogressll= $this->db->query($sqlprogressll);

    $hasilprogll__=0;

    foreach ($resprogressll->result_array() as $hasilprogressll){
        $hasilprogll=$hasilprogressll['tahap_diakui_bobot'];
        $hrgll=$hasilprogressll['tahap_harga_satuan_kendali'];
        $hasilprogll_= $hasilprogll * $hrgll;
        $hasilprogll__ += $hasilprogll_;
    }
    //====================MENCARI PROGRES RENCANA============================//
    
    $tgl_lppblnll_1=substr($tgl_lppblnll_,5,2);
    $thnblnlalu=substr($tgl_lppblnll_,0,4);
    $sqlprogrencana="select sum ((a.tahap_volume_bln1 + a.tahap_volume_bln2) * b.tahap_harga_satuan_kendali) as total from simpro_tbl_total_rkp as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  date_part('month',a.tahap_tanggal_kendali)= '$bulan_lalu' and date_part('year',a.tahap_tanggal_kendali)= '$tahun_lalu'  group by a.proyek_id ";//
    $resprogren= $this->db->query($sqlprogrencana);
    $hasilproren = $resprogren->result_array();

    if ($hasilproren)
    {
        foreach ($hasilproren as $hasilprorens) {
            $hasilprogren_=$hasilprorens['total'];
        }
    } else {
        $hasilprogren_ = 0;
    }
    
    
    //===============MENCARI PROGRES RENCANA BULAN DEPAN========================//
    $progresblndpn_ =$bulan_ + 1;
    if($progresblndpn_ ==13){
        $progresblndpn_ = 01;
        $tahundpn_=$tahun_ + 1;
    }    
    else{
        $progresblndpn_=$progresblndpn_;
        $tahundpn_ =$tahun_;
    }
    $sqlprogrencanadpn="select sum ((a.tahap_volume_bln1 + a.tahap_volume_bln2) * b.tahap_harga_satuan_kendali) as total from simpro_tbl_total_rkp as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  date_part('month',a.tahap_tanggal_kendali)= '$bulan' and date_part('year',a.tahap_tanggal_kendali)= '$tahun'  group by a.proyek_id ";
    $resprogrendpn = $this->db->query($sqlprogrencanadpn);
    $hasilprorendpn= $resprogrendpn->result_array();
    foreach ($hasilprorendpn as $hasilprorendpns) {
    	$hasilprogrendpn_=$hasilprorendpns['total'];
    }
    
    
   
    if($totalkini==0){
        $progressdblnini=0;
        $progressdblnll=0;
        $progresrencana=0;
    }else{
    $progressdblnini = round($hasilprog__ / $totalkini * 100);
    $progressdblnll  = round($hasilprogll__ / $totalkini * 100);
   
    $progresrencana  = round(($hasilprogren_ / $totalkini) * 100);
    $hasilprogrendpn_persen  = round($hasilprogrendpn_ / $totalkini * 100);
    }
    $progresrealisasi =  $progressdblnini - $progressdblnll ;
    $progresdeviasi = $progresrealisasi - $progresrencana;
    $progressisa = 100 - $progressdblnini;
    $progresperkiraan = $progressdblnini + $progressisa;
    $progresdeviasito = 100 - $progresperkiraan;
    
    //====SD BLN LALU==    
    $sql11="select sum(tahap_volume_kendali * tahap_harga_satuan_kendali)as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' group by proyek_id,tahap_tanggal_kendali ";//and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun'
	$res11= $this->db->query($sql11);//echo "$sql1<br>";

    $tata=0;

	    foreach($res11->result_array() as $r11){
		   $tata=$r11['total'];
	    }

    if($tata==0){
        $sched1=0;
        $sched2=0;
        $sched3=0;
        $sched4=0;
        $sched5=0;
        $sched6=0;
        $sched7=0;
        $sched8=0;
        $sched9=0;
        $sdproglalu=0;
        $sdprogini=0;
        $sdprogrc=0;
        $sdprogrcdpn=0;
    }else{
    //SD BLN LALU
    $sqlprog="select sum(a.tahap_diakui_bobot * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  a.tahap_tanggal_kendali <='$tgl_lalu' and a.tahap_diakui_bobot !=0 group by a.proyek_id";
    $resprog= $this->db->query($sqlprog);
    $xprog=$resprog->result_array();
    $sdproglalu=0;
    foreach ($xprog as $xprogs) {
    	$sdproglalu=$xprogs['total'];
    }
    
    
    
    //====SD.BLN INI===
    $sqlprogini="select sum(a.tahap_diakui_bobot * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and a.tahap_tanggal_kendali <='$tgl_ini' and a.tahap_diakui_bobot !=0 group by a.proyek_id";
    $resprogini=$this->db->query($sqlprogini);
    $xprogini=$resprogini->result_array();
    $sdprogini=0;
    foreach ($xprogini as $xproginis) {
    	$sdprogini=$xproginis['total'];
    }
    
    //====RENCANA===
    $blnrcn=$bulan_lalu-1;
    
    $tglklik=preg_split("/-/",$tanggal);
    $sqltgl="select distinct(tahap_tanggal_kendali)as tgl from simpro_tbl_total_rkp where proyek_id='$proyek_id'order by tgl asc";
    $r_sqltgl=$this->db->query($sqltgl);
    $x_sqltgl=$r_sqltgl->result_array();
    
    foreach ($x_sqltgl as $x_sqltgls) {
    	$arr_tglll=preg_split("/-/",$x_sqltgls['tgl']);
    }


    if($tglklik[1]==$arr_tglll[1]){
                    $blnrcn=$tglklik[1]-1;
                    $txtsql="sum((tahap_volume_bln1 + tahap_volume_bln2)*tahap_harga_satuan_kendali) as total";
                    
    }
    else{
            if($blnrcn < $arr_tglll[1]){
                    $blnrcn=$arr_tglll[1];
                    $txtsql="sum((tahap_volume_bln1 + tahap_volume_bln2)*tahap_harga_satuan_kendali) as total";
                    
            }
            else{
                $blnrcn=$blnrcn;
                $txtsql="sum((tahap_volume_bln3 + tahap_volume_bln4)*tahap_harga_satuan_kendali) as total";
                
            }
    }
    //$sqlprogrc="select $txtsql from tbl_total_rkp where no_spk='$cnospk_pilih' and date_part('month',tahap_tanggal_kendali)='$blnrcn' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' group by no_spk";
    $sqlprogrc="select sum(tahap_jumlah_bln1) as a,sum(tahap_jumlah_bln2) as b from simpro_tbl_total_rkp where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' group by proyek_id";
    $resprogrc=$this->db->query($sqlprogrc);
    $xprogrc=$resprogrc->result_array();
    $sdprogrc=0;
    foreach ($xprogrc as $xprogrcs) {
    	$sdprogrc=$xprogrcs['a']+$xprogrcs['b'];
    }
    
    
    $realisasi = $sdprogini - $sdproglalu;
    $deviasi = $realisasi - $sdprogrc;
    $sisa =100 - $sdprogini;
    $perkiraan = $sdprogini + $sisa;
    $devtotal= 100 - $perkiraan;
    
     $tes=$bulan-1;
    if($tglklik[1]==$arr_tglll[1]){
        
        $tes=$tglklik[1];
        $txtsql_="tahap_volume_bln1 as a ,tahap_volume_bln2 as b,tahap_harga_satuan_kendali as c";
    }
    else{
        
            if($tes < $arr_tglll[1]){
                    
                    $tes=$tes;
                    if($tglklik[0]==$arr_tglll[0]){
                         $tes=$arr_tglll[1];
                    }
                    $txtsql_="tahap_volume_bln1 as a,tahap_volume_bln2 as b,tahap_harga_satuan_kendali as c";
                    
            }
            else{
                
                $tes=$tes;
                $txtsql_="tahap_volume_bln3 as a, tahap_volume_bln4 as b,tahap_harga_satuan_kendali as c";
                
            }
    
    }
    
     //$sqlprogrcdpn="select $txtsql_ from tbl_total_rkp where no_spk='$cnospk_pilih' and date_part('month',tahap_tanggal_kendali)='$tes' and date_part('year',tahap_tanggal_kendali)='$tahun' ";
     $sqlprogrcdpn="select sum(tahap_jumlah_bln1) as a,sum(tahap_jumlah_bln2) as b from simpro_tbl_total_rkp where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' ";
    $resprogrcdpn=$this->db->query($sqlprogrcdpn);
    $xprogrcdpn=$resprogrcdpn->result_array();
    foreach ($xprogrcdpn as $xprogrcdpns) {
    	$sdprogrcdpn=$xprogrcdpns['a']+$xprogrcdpns['b'];
    }
        
      //  $sdprogrcdpn+=$sdprogrcdpn_;
   
    
    
    
    $sched1=($sdproglalu /$tata) * 100;
    
    $sched2=($sdprogrc /$tata) * 100;
    $sched6=($sdprogini/$tata) * 100;
   
    $sched3= $sched6 - $sched1;
    $sched4= $sched3 - $sched2;
    $sched5= 100 - $sched6;
    $sched7= $sched5 + $sched6;
    $sched8= 100 - $sched7;
    $sched9=($sdprogrcdpn/$tata) * 100;
    
    $sql_kkp="select * from simpro_tbl_kkp where proyek_id='$proyek_id' and date_part('month',kkp_tgl)='$bulan' and date_part('year',kkp_tgl)='$tahun'";
    $r_sql=$this->db->query($sql_kkp);
    $xsql=$r_sql->result_array();
    }

    function format_mede($angka,$digit=0,$xx="",$yy=""){
		$juta=1;
		if($angka < 0){
			$str_temp= "(".number_format((0-$angka),2,",",".").")";
		}else{
			$str_temp= number_format($angka,2,",",".");
		}
		return $str_temp;
	}

    ?>


		<tbody>
			<tr>
				<td>-</td>
				<td>Schedule</td>
				<td>100%</td>
				<td><?php echo format_mede($sched1,2); ?> %</td>
				<td><?php echo format_mede($sched2,2); ?> %</td>
				<td><?php echo format_mede($sched3,2); ?> %</td>
				<td><?php echo format_mede($sched4,2); ?> %</td>
				<td><?php echo format_mede($sched5,2); ?> %</td>
				<td><?php echo format_mede($sched6,2); ?> %</td>
				<td><?php echo format_mede($sched7,2); ?> %</td>
				<td><?php echo format_mede($sched8,2); ?> %</td>
				<td><?php echo format_mede($sched9,2); ?> %</td>
				<td rowspan="11">KENDALA PROYEK : 
					<?php
					if(isset($xsql)){
						foreach ($xsql as $xsqls) {
							echo $xsqls['kkp_uraian'];
						}
					}
					?></td>
			</tr>
			<tr>
				<td>-</td>
				<td>Progress</td>
				<td>100%</td>
				<td><?php echo format_mede($sched1,2); ?> %</td>
				<td><?php echo format_mede($sched2,2); ?> %</td>
				<td><?php echo format_mede($sched3,2); ?> %</td>
				<td><?php echo format_mede($sched4,2); ?> %</td>
				<td><?php echo format_mede($sched5,2); ?> %</td>
				<td><?php echo format_mede($sched6,2); ?> %</td>
				<td><?php echo format_mede($sched7,2); ?> %</td>
				<td><?php echo format_mede($sched8,2); ?> %</td>
				<td><?php echo format_mede($sched9,2); ?> %</td>
			</tr>

	<?php

	//========================PENDAPATAN USAHA======================================================================//
    
    //================PEKERJAAN TAMBAH ANGGARAN KONTRAK ASLI==============================================//
    $blntbh=substr($tanggal,5,2);
    $thntbh=substr($tanggal,0,4);
    /*hide by octa $sqltambah="select sum(tahap_volume_kendali_new * tahap_harga_satuan_kendali)as total from tbl_kontrak_terkini where no_spk='$cnospk_pilih' and tahap_kode_induk_kendali !='' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun'  and is_nilai=1 group by no_spk";
	$restambah=dbresult($sqltambah);//echo "$sql1<br>";
	//$sub_jumlah_ctd_sblm=0;
	while($r1tbh=pg_fetch_array($restambah)){
		$tata1=$r1tbh[total];
	}*/
    $sqltambah="select sum(tahap_volume_kendali_new * tahap_harga_satuan_kendali)as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and (tahap_volume_kendali_new!=0 or tahap_volume_kendali_new is not null ) group by proyek_id"; //and tahap_kode_induk_kendali !='' 
	$restambah=$this->db->query($sqltambah);//echo "$sql1<br>";
	//$sub_jumlah_ctd_sblm=0;
	$r1tbh=$restambah->result_array();
    $tata1=0;
	foreach ($r1tbh as $r1tbhs) {
		$tata1=$r1tbhs['total'];
	}
		
	
    //=================PEKERJAAN TAMBAH S/D BULAN LALU==============================================//
    $blnhasiltglkon=substr($hasiltglkon,5,2);
    $thnhasiltglkon=substr($hasiltglkon,0,4);
    
    $sqlksdblnll="select tahap_volume_kendali_new,tahap_harga_satuan_kendali from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and  date_part('month',tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',tahap_tanggal_kendali) <='$tahun_lalu' and is_nilai=1";// '  and tahap_kode_induk_kendali !=''  and tahap_volume_kendali_new !=0 and tahap_tanggal_kendali<= '$tgl_lppblnll_'
	$hasil2sdblnll_ = 0;
	$ressdblnll=$this->db->query($sqlksdblnll);//echo $sqlk;
        foreach ($ressdblnll->result_array() as $x11sdblnll){
        $total1sdblnll= $x11sdblnll['tahap_volume_kendali_new'];
        $hargasat1sdblnll=$x11sdblnll['tahap_harga_satuan_kendali'];
        $hasil2sdblnll = $total1sdblnll * $hargasat1sdblnll;
        $hasil2sdblnll_ += $hasil2sdblnll;
        }
     //==================PEKERJAAN TAMBAH KOLOM SD BLN INI========================================================//
    $sqltgl="select tahap_tanggal_kendali from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id'";
    $restglkndli=$this->db->query($sqltgl);//echo $sqlk;
	$tglkndali=$restglkndli->result_array();
	foreach ($tglkndali as $tglkndalis) {
		$hasiltgl=$tglkndalis['tahap_tanggal_kendali'];
	}
    
    
    $sqlk="select tahap_volume_kendali_new,tahap_harga_satuan_kendali from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and  date_part('month',tahap_tanggal_kendali) <='$bulan'  and date_part('year',tahap_tanggal_kendali) <='$tahun' and tahap_volume_kendali_new != 0 and is_nilai=1";//and tahap_kode_induk_kendali !='' and tahap_tanggal_kendali >= '$hasiltgl' and tahap_tanggal_kendali<= '$tgl_lpp'
    $hasil2tbh = 0;
	$res=$this->db->query($sqlk);//echo $sqlk;
	    foreach ($res->result_array() as $x11){
        $hargasat1=$x11['tahap_harga_satuan_kendali'];
        $total1= $x11['tahap_volume_kendali_new'];
        $hasil2 = $total1 * $hargasat1;
        $hasil2tbh += $hasil2;
        }
    //=================PEKERJAAN TAMBAH KOLOM RENCANA ==========================================================//
    
    // $sqlrencana="select volume_rencana,tahap_harga_satuan_kendali from simpro_tbl_kontrak_terkini where date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)= '$tahun_lalu' and volume_rencana !=0 and proyek_id='$proyek_id'  ";// tahap_kode_induk_kendali !='' and $tgl_lppblnll_
	$sqlrencana="select 
        tahap_volume_kendali_new as volume_rencana,
        tahap_harga_satuan_kendali 
        from simpro_tbl_rencana_kontrak_terkini 
        where date_part('month',tahap_tanggal_kendali)='$bulan_lalu' 
        and date_part('year',tahap_tanggal_kendali)= '$tahun_lalu' 
        and tahap_volume_kendali_new !=0 
        and proyek_id='$proyek_id'";
    $resrencana=$this->db->query($sqlrencana);//echo "$sql1<br>";
	$rencanatbhvolhrg_ = 0;
    	foreach ($resrencana->result_array() as $r1rencana){
    	$rencanavol=$r1rencana['volume_rencana'];
        $rencanahrg=$r1rencana['tahap_harga_satuan_kendali'];
        $rencanatbhvolhrg = $rencanavol * $rencanahrg;
        $rencanatbhvolhrg_ += $rencanatbhvolhrg;
    }
    //=================PEKERJAAN TAMBAH KOLOM REALISASI==========================================================//
    
    $realisaitambah= $hasil2tbh - $hasil2sdblnll_;
    $deviasitambah = $realisaitambah - $rencanatbhvolhrg_;
    $sisaanggarantambah  = $tata1 - $hasil2tbh;
    $perkiraansdselesaitmbah= $hasil2tbh + $sisaanggarantambah;
    $deviasitotaltambah = $perkiraansdselesaitmbah-$tata1 ;//- $sisaanggarantambah;
    
    //=================PEKERJAAN TAMBAH ISIAN RENCANA BULAN =======================================================//
    // $sqllbprcntb="select volume_rencana,tahap_harga_satuan_kendali from simpro_tbl_kontrak_terkini where date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and volume_rencana !=0 and proyek_id='$proyek_id' "; // tahap_kode_induk_kendali !='' and
	$sqllbprcntb="select 
        tahap_volume_kendali_new as volume_rencana,
        tahap_harga_satuan_kendali 
        from simpro_tbl_rencana_kontrak_terkini 
        where date_part('month',tahap_tanggal_kendali)='$bulan' 
        and date_part('year',tahap_tanggal_kendali)= '$tahun' 
        and tahap_volume_kendali_new !=0 
        and proyek_id='$proyek_id'";
    $res1lbprcntb=$this->db->query($sqllbprcntb);//echo "$sql1<br>";

    $hasil2tb = 0;
    	foreach($res1lbprcntb->result_array() as $r1rncntb){
    		 $rncnlbptb=$r1rncntb['volume_rencana'];
             $cektb=$r1rncntb['tahap_harga_satuan_kendali'];
             $hasiltb = $rncnlbptb * $cektb;
           $hasil2tb += $hasiltb;
    }  
    
    //===================PEKERJAAN KURANG KOLOM ANGGARAN KONTRAK ASLI===========================================//
    $sqlkrg="select sum(tahap_volume_kendali_kurang * tahap_harga_satuan_kendali)as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and tahap_volume_kendali_kurang != 0 and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and is_nilai=1 group by proyek_id  "; // and tahap_kode_induk_kendali !=''
	$reskrg=$this->db->query($sqlkrg);//echo "$sql1<br>";
    $tata2=0;
	foreach($reskrg->result_array() as $r1krg){
		$tata2=$r1krg['total'];
	}

    //==================PEKERJAAN KURANG KOLOM S/D BULAN LALU/JULI ==============================================//
    $sqlk2sdblnll="select tahap_volume_kendali_kurang,tahap_harga_satuan_kendali from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and  date_part('month',tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',tahap_tanggal_kendali) <='$tahun_lalu' and is_nilai=1";// and tahap_kode_induk_kendali !='' and tahap_volume_kendali_kurang !=0 tahap_tanggal_kendali >= '$hasiltglkon' and tahap_tanggal_kendali<= '$tgl_lppblnll_'
	$res2sdblnll=$this->db->query($sqlk2sdblnll);//echo $sqlk;
	$hasil2sdblnll_2 = 0;
       foreach($res2sdblnll->result_array() as $x11sdblnll2){
          $total2sdblnll= $x11sdblnll2['tahap_volume_kendali_kurang'];
          $hargasat2sdblnll=$x11sdblnll2['tahap_harga_satuan_kendali'];
           $hasil2sdblnll2 = $total2sdblnll * $hargasat2sdblnll;
          $hasil2sdblnll_2 += $hasil2sdblnll2;
        }
    // =================PEKERJAAN KURANG KOLOM SD BLN INI=====================================================//date_part('month',tahap_tanggal_kendali)='$blntbh' and date_part('year',tahap_tanggal_kendali)='$thntbh'
    $sqlk="select tahap_volume_kendali_kurang,tahap_harga_satuan_kendali from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and  date_part('month',tahap_tanggal_kendali) <='$bulan'  and date_part('year',tahap_tanggal_kendali) <='$tahun'  and tahap_volume_kendali_kurang!= 0 and is_nilai=1";//and tahap_kode_induk_kendali !='' tahap_tanggal_kendali >= '$hasiltgl' and tahap_tanggal_kendali<= '$tgl_lpp'
	$res=$this->db->query($sqlk);//echo $sqlk;
	$hasil2krng_ = 0;
        foreach($res->result_array() as $x11){
          $total1krng = $x11['tahap_volume_kendali_kurang'];
          $hargasat1=$x11['tahap_harga_satuan_kendali'];
          $hasil3krng_ = $total1krng * $hargasat1;
           $hasil2krng_ += $hasil3krng_;
        }
    //==================PEKERJAAN KURANG  KOLOM RENCANA======================================================//
     // $sqlrencanakrg="select volume_rencana1,tahap_harga_satuan_kendali from simpro_tbl_kontrak_terkini where date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)= '$tahun_lalu' and volume_rencana1 !=0 "; //tahap_kode_induk_kendali !='' 
	$sqlrencanakrg="select 
    tahap_volume_kendali_kurang as volume_rencana1,
    tahap_harga_satuan_kendali 
    from simpro_tbl_rencana_kontrak_terkini 
    where date_part('month',tahap_tanggal_kendali)='$bulan_lalu' 
    and date_part('year',tahap_tanggal_kendali)= '$tahun_lalu' 
    and tahap_volume_kendali_new !=0 
    and proyek_id='$proyek_id'";
    $resrencanakrg=$this->db->query($sqlrencanakrg);//echo "$sql1<br>";
    $rencanatbhvolhrgkg_=0;
    	foreach($resrencanakrg->result_array() as $r1rencanakrg){
    	$rencanavolkrg=$r1rencanakrg['volume_rencana1'];
        $rencanahrgkrg=$r1rencanakrg['tahap_harga_satuan_kendali'];
        $rencanatbhvolhrgkg = $rencanavolkrg * $rencanahrgkrg;
        $rencanatbhvolhrgkg_ += $rencanatbhvolhrgkg;
    }
     
    //==================PEKERJAAN KURANG  KOLOM REALISASI======================================================//
    $realisasikurang= $hasil2krng_ - $hasil2sdblnll_2;
    $deviasikurang = $realisasikurang - $rencanatbhvolhrgkg_;
    $sisaanggarankurang  = $tata2 - $hasil2krng_;
    $perkiraansdselesaikurng= $hasil2tbh + $sisaanggarankurang;
    $deviasitotalkurang = $perkiraansdselesaikurng-$tata2;// - $sisaanggarankurang;
   
    //================PEKERJAAN KURANG KOLOM ISIAN RENCANA BULAN==================================================//
    // $sqllbprcn2="select volume_rencana1,tahap_harga_satuan_kendali from simpro_tbl_kontrak_terkini where date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and volume_rencana1 !=0 and proyek_id='$proyek_id' ";//tahap_kode_induk_kendali !='' tahap_tanggal_kendali='$tgl_lpp'
	$sqllbprcn2="select 
        tahap_volume_kendali_kurang as volume_rencana1,
        tahap_harga_satuan_kendali 
        from simpro_tbl_rencana_kontrak_terkini 
        where date_part('month',tahap_tanggal_kendali)='$bulan' 
        and date_part('year',tahap_tanggal_kendali)= '$tahun' 
        and tahap_volume_kendali_new !=0 
        and proyek_id='$proyek_id'";
    $res1lbprcn2=$this->db->query($sqllbprcn2);//echo "$sql1<br>";
    $hasil21=0;
    	foreach($res1lbprcn2->result_array() as $r1rncn2){
   		  $rncnlbp2=$r1rncn2['volume_rencana1'];
          $cek2=$r1rncn2['tahap_harga_satuan_kendali'];
          $hasil2 = $rncnlbp2 * $cek2;
          $hasil21 += $hasil2;
    } 
    //===============================ESKALASI===========================================//
    //====================S/D BULAN LALU================//
    $sql_es2="select sum(harga_satuan_eskalasi) as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and  date_part('month',tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',tahap_tanggal_kendali) <='$tahun_lalu'  and harga_satuan_eskalasi !=0 "; //and tahap_kode_induk_kendali !=''
    $r_es2  =$this->db->query($sql_es2);
    foreach($r_es2->result_array() as $x_res2){
        $es2=$x_res2['total'];
    }
    //=================RENCANA======================//
    // $sql_es3="select sum(rencana_harga_satuan_eskalasi) as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) = '$bulan_lalu' and date_part('year',tahap_tanggal_kendali) = '$tahun_lalu'  and rencana_harga_satuan_eskalasi !=0"; // and tahap_kode_induk_kendali !=''
    $sql_es3="select sum(harga_satuan_eskalasi) as total from simpro_tbl_rencana_kontrak_terkini where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) = '$bulan_lalu' and date_part('year',tahap_tanggal_kendali) = '$tahun_lalu'  and harga_satuan_eskalasi !=0"; // and tahap_kode_induk_kendali !=''
    $r_es3  =$this->db->query($sql_es3);
    foreach($r_es3->result_array() as $x_res3){
        $es3=$x_res3['total'];
    }
    
    //=================SD BLN INI======================//
    $sql_es6="select sum(harga_satuan_eskalasi) as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and  date_part('month',tahap_tanggal_kendali) <='$bulan'  and date_part('year',tahap_tanggal_kendali) <='$tahun' and harga_satuan_eskalasi !=0"; // and tahap_kode_induk_kendali !=''
    $r_es6  =$this->db->query($sql_es6);
    foreach($r_es6->result_array() as $x_res6){
        $es6=$x_res6['total'];
    }
    //=================ISIAN RENCANA ======================//
    // $sql_es10="select sum(rencana_harga_satuan_eskalasi) as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) = '$bulan' and date_part('year',tahap_tanggal_kendali) = '$tahun' and rencana_harga_satuan_eskalasi !=0"; // and tahap_kode_induk_kendali !=''
    $sql_es10="select sum(harga_satuan_eskalasi) as total from simpro_tbl_rencana_kontrak_terkini where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) = '$bulan' and date_part('year',tahap_tanggal_kendali) = '$tahun' and harga_satuan_eskalasi !=0"; // and tahap_kode_induk_kendali !=''
    $r_es10 =$this->db->query($sql_es10);
    foreach($r_es10->result_array() as $x_res10){
        $es10=$x_res10['total'];
    }
    $es4= $es6 - $es2;
    $es5= $es3- $es2;
    //***************************************************KONTRAK AWAL ****************************//
    $kontrakrealisasi=$sdprogini-$sdproglalu;
    $kontrakdeviasi=($sdprogini-$sdproglalu)-$sdprogrc;
    $kontraksisaanggaran=$tata-$sdprogini;
    $kontrakperkiraan=$sdprogini + $kontraksisaanggaran;
    $kontrakdeviasii=$tata-$kontrakperkiraan;
    //*************************************************END **************************************//
    $pukontrak = $tata + ($tata1 - $tata2); 
    $pudblnll = $sdproglalu+$hasil2sdblnll_2 + $hasil2sdblnll_ + $es2;
    $purencan = $sdprogrc+$rencanatbhvolhrgkg_ + $rencanatbhvolhrg_ + $es3;
    $purealisasi = $kontrakrealisasi + $realisaitambah + $realisasikurang + $es4;
    $pudeviasi = $kontrakdeviasi + $deviasikurang + $deviasitambah+ $es5;
    $pusdblnini = $sdprogini + $hasil2krng_ + $hasil2tbh + $es6;
    $puanggaran = $kontraksisaanggaran+ $sisaanggarankurang + $sisaanggarantambah;
    $puperkiraan = $kontrakperkiraan + $perkiraansdselesaitmbah + $perkiraansdselesaikurng;
    $pudeviasito = $kontrakdeviasii + $deviasitotaltambah + $deviasitotalkurang;
    $purencanadpn = $sdprogrcdpn + $hasil2tb+$hasil21 +  $es10;
    
    if($pudeviasi < 0 ){
        $bgcolor5_='font color=#DD0000';
    }

    function format_nindya($angka,$digit=0,$xx="",$yy=""){
        $juta=1;
        if($angka < 0){
            $str_temp= "(".number_format((0-$angka/$juta),2,",",".").")";
        }else{
            $str_temp= number_format(($angka/$juta),2,",",".");
        }
        return $str_temp;
    }
    
    function format_mede2($angka,$digit=0,$xx="",$yy=""){
        $ribu=1;
        if($angka < 0){
            $str_temp= "(".number_format((0-$angka/$ribu),2,",",".").")";
        }else{
            $str_temp= number_format($angka/$ribu,2,",",".");
        }
        return $str_temp;
    }

	?>


			<tr>
				<td>-</td>
				<td>Pendapatan Usaha</td>
				<td><?php echo format_nindya($pukontrak,2); ?></td>
				<td><?php echo format_nindya($pudblnll,2); ?></td>
				<td><?php echo format_nindya($purencan,2); ?></td>
				<td><?php echo format_nindya($purealisasi,2); ?></td>
				<td><?php echo format_nindya($pudeviasi,2); ?></td>
				<td><?php echo format_nindya($pusdblnini,2); ?></td>
				<td><?php echo format_nindya($puanggaran,2); ?></td>
				<td><?php echo format_nindya($puperkiraan,2); ?></td>
				<td><?php echo format_nindya($pudeviasito,2); ?></td>
				<td><?php echo format_nindya($purencanadpn,2); ?></td>
			</tr>

    <?php

    //UNTUK MENCARI REALISASI PIUTANG
    $totaltag="select sum(a.vol_total_tagihan * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and date_part('month',a.tahap_tanggal_kendali)='$bulan' and date_part('year',a.tahap_tanggal_kendali)='$tahun' and a.tahap_kode_induk_kendali!='' and a.tagihan_cair !=0 group by a.proyek_id ";
    $rtotaltag=$this->db->query($totaltag);
    $xtotaltag=$rtotaltag->row();
    $xtotaltag_row=$rtotaltag->num_rows();

    if($xtotaltag_row==0) $xtotaltag_total=0; else $xtotaltag_total = $xtotaltag->total;

    $tagcair="select sum(a.tagihan_cair * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and date_part('month',a.tahap_tanggal_kendali)='$bulan' and date_part('year',a.tahap_tanggal_kendali)='$tahun' and a.tahap_kode_induk_kendali!='' and a.tagihan_cair !=0 group by a.proyek_id";
    $rtagcair=$this->db->query($tagcair);
    $xtagcair=$rtagcair->row();
    $xtagcair_row=$rtagcair->num_rows();
    
    if($xtagcair_row==0) $xtagcair_total=0; else $xtagcair_total = $xtagcair->total;

    //UNTUK MENCARI SD BLN INI PIUTANG
    $totaltag_="select sum(a.vol_total_tagihan * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  date_part('month',a.tahap_tanggal_kendali) <='$bulan'  and date_part('year',a.tahap_tanggal_kendali) <='$tahun' and a.tahap_kode_induk_kendali!='' and a.tagihan_cair !=0 group by a.proyek_id ";
    $rtotaltag_=$this->db->query($totaltag_);
    $xtotaltag_=$rtotaltag_->row();
    $xtotaltag__row=$rtotaltag_->num_rows();

    if($xtotaltag__row==0) $xtotaltag__total=0; else $xtotaltag__total = $xtotaltag_->total;

    $tagcair_="select sum(a.tagihan_cair * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  date_part('month',a.tahap_tanggal_kendali) <='$bulan'  and date_part('year',a.tahap_tanggal_kendali) <='$tahun' and a.tahap_kode_induk_kendali!='' and a.tagihan_cair !=0 group by a.proyek_id";
    $rtagcair_=$this->db->query($tagcair_);
    $xtagcair_=$rtagcair_->row();
    $xtagcair__row=$rtagcair_->num_rows();
    
    if($xtagcair__row==0) $xtagcair__total=0; else $xtagcair__total = $xtagcair_->total;

    $rencanapiut="select sum(tagihan_rencana_piutang)as total from simpro_tbl_total_pekerjaan where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and tahap_kode_induk_kendali!='' and tagihan_rencana_piutang !=0 group by proyek_id ";
    $rrencanapiut=$this->db->query($rencanapiut);
    $xrencanapiut=$rrencanapiut->row();
    $xrencanapiut_row=$rrencanapiut->num_rows();

    if($xrencanapiut_row==0) $xrencanapiut_total=0; else $xrencanapiut_total = $xrencanapiut->total;
    
    $total_piutang = $xtotaltag_total - $xtagcair_total;
    $total_piutang_= $xtotaltag__total - $xtagcair__total ;
    $piutanggaran= $pukontrak - $total_piutang_;
    $piutperkiraan = $total_piutang_ + $piutanggaran;
    $deviasitotal= $pukontrak - $piutperkiraan;
    
    
    //================================MENCARI TAGIHAN BRUTO S/D LALU==============================================//
    $bruto="select a.tahap_volume_kendali_new,b.tahap_harga_satuan_kendali from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  date_part('month',a.tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',a.tahap_tanggal_kendali) <='$tahun_lalu' and a.tahap_volume_kendali_new !=0 "; //and a.tahap_kode_induk_kendali !='' 
    $resbruto=$this->db->query($bruto);
    $hasilbr_ = 0;
    foreach($resbruto->result_array() as $r1bruto){
          $rnvolbr=$r1bruto['tahap_volume_kendali_new'];
          $rnttlkendali=$r1bruto['tahap_harga_satuan_kendali'];
          $hasilbr = $rnvolbr * $rnttlkendali;
          $hasilbr_ += $hasilbr;
    } 
    //===================MENCARI TAGIHAN BRUTO SD/BLN INI===================================//
    $sqlbrini="select a.tahap_volume_kendali_new,b.tahap_harga_satuan_kendali from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  date_part('month',a.tahap_tanggal_kendali) <='$bulan'  and date_part('year',a.tahap_tanggal_kendali) <='$tahun' and a.tahap_volume_kendali_new !=0";
    $resbrini=$this->db->query($sqlbrini);
    $hasilbrini_ = 0;
    foreach($resbrini->result_array() as $hasilbrini){
    
        $rnvolbrini=$hasilbrini['tahap_volume_kendali_new'];
        $hrgbrini=$hasilbrini['tahap_harga_satuan_kendali'];
        $hasilbrini= $rnvolbrini * $hrgbrini;
        $hasilbrini_ += $hasilbrini;
    }
    
    $realisasibr = $hasilbrini_ - $hasilbr_;
    //=====================================================TOTAL CAIR============================//
    $cair6="select sum(a.tagihan_cair * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  a.tahap_tanggal_kendali <='$tgl_ini' group by a.proyek_id";
    $r_cair6=$this->db->query($cair6);
    $xcair6=$r_cair6->row();
    $xcair6_row=$r_cair6->num_rows();

    if($xcair6_row==0) $tot_cair6=0; else $tot_cair6=$xcair6->total;
    
    $cair2="select sum(a.tagihan_cair * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and a.tahap_tanggal_kendali<='$tgl_lalu' group by a.proyek_id";
    $r_cair2=$this->db->query($cair2);
    $xcair2=$r_cair2->row();
    $xcair2_row=$r_cair2->num_rows();

    if($xcair2_row==0) $tot_cair2=0; else $tot_cair2=$xcair2->total;

    $cair4="select sum(a.tagihan_cair * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and date_part('month',a.tahap_tanggal_kendali) ='$bulan' and date_part('year',a.tahap_tanggal_kendali) ='$tahun' group by a.proyek_id";
    $r_cair4=$this->db->query($cair4);
    $xcair4=$r_cair4->row();
    $xcair4_row=$r_cair4->num_rows();

    if($xcair4_row==0) $tot_cair4=0; else $tot_cair4=$xcair4->total;
    
    ?>

			<tr>
				<td>-</td>
				<td>Total Cair</td>
				<td>0</td>
				<td><?php echo format_nindya($tot_cair2); ?></td>
				<td>0</td>
				<td><?php echo format_nindya($tot_cair4); ?></td>
				<td>0</td>
				<td><?php echo format_nindya($tot_cair6); ?></td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
			</tr>

    <?php

    //=====================================================TOTAL PIUTANG============================//
    $piutang6="select sum(a.vol_total_tagihan * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and a.tahap_tanggal_kendali <='$tgl_ini' group by a.proyek_id";
    $r_piutang6=$this->db->query($piutang6);
    $xpiutang6=$r_piutang6->row();
    $xpiutang6_row=$r_piutang6->num_rows();

    if($xpiutang6_row==0) $tot_piutang_=0; else $tot_piutang_=$xpiutang6->total;

    $tot_piutang6=$tot_piutang_ - $tot_cair6;

    ?>

			<tr>
				<td>-</td>
				<td>Total Piutang</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td><?php echo format_nindya($tot_piutang6); ?></td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
			</tr>
    <?php
    if($pudeviasi < 0){
        $bgcolor6_='font color=#DD0000';
    }
    
    //rumus tagihan bruto = total_progress sd bln ini - total tagihan
    $sql_bruto="select sum(a.tahap_diakui_bobot * b.tahap_harga_satuan_kendali)as total from simpro_tbl_total_pekerjaan as a INNER JOIN simpro_tbl_kontrak_terkini as b ON a.kontrak_terkini_id=b.id_kontrak_terkini where a.proyek_id='$proyek_id' and  a.tahap_tanggal_kendali <='$tgl_ini' group by a.proyek_id";
    $rsql_bruto=$this->db->query($sql_bruto);
    $x_bruto=$rsql_bruto->row();
    $x_bruto_row=$rsql_bruto->num_rows();

    if($x_bruto_row==0) $bruto6=0; else $bruto6=$x_bruto->total - $tot_piutang_;
    ?>

			<tr>
				<td>-</td>
				<td>Tagihan Bruto</td>
				<td>0</td>
				<!--<td><?php echo format_nindya($hasilbr_); ?></td>-->
				<td>0</td>
				<td>0</td>
				<!--<td><?php echo format_nindya($realisasibr); ?></td>-->
				<td>0</td>
				<td>0</td>
				<td><?php echo format_nindya($bruto6); ?></td>
				<td>0</td>
				<td>0</td>
                <td>0</td>
                <td>0</td>
			</tr>	

    <!--========================================================================KONTRAK AWAL===========================================================================-->

			<tr>
				<td>-</td>
				<td>Kontrak Awal</td>
				<td><?php echo format_nindya($tata); ?></td>
				<td><?php echo format_mede2($sdproglalu,0); ?></td>
				<td><?php echo format_mede2($sdprogrc,0); ?></td>
				<td><?php echo format_mede2($kontrakrealisasi,0); ?></td>
				<td><?php echo format_mede2($kontrakdeviasi,0); ?></td>
				<td><?php echo format_mede2($sdprogini,0); ?></td>
				<td><?php echo format_mede2($kontraksisaanggaran,0); ?></td>
				<td><?php echo format_mede2($kontrakperkiraan,0); ?></td>
				<td><?php echo format_mede2($kontrakdeviasii,0); ?></td>
				<td><?php echo format_mede2($sdprogrcdpn,0); ?></td>
			</tr>	
			<tr>
				<td>-</td>
				<td>Pekerjaan Tambah</td>
				<td><?php echo format_nindya($tata1); ?></td>
				<td><?php echo format_nindya($hasil2sdblnll_); ?></td>
				<td><?php echo format_nindya($rencanatbhvolhrg_); ?></td>
				<td><?php echo format_nindya($realisaitambah); ?></td>
				<td><?php echo format_nindya($deviasitambah); ?></td>
				<td><?php echo format_nindya($hasil2tbh); ?></td>
				<td><?php echo format_nindya($sisaanggarantambah); ?></td>
				<td><?php echo format_nindya($perkiraansdselesaitmbah); ?></td>
				<td><?php echo format_nindya($deviasitotaltambah); ?></td>
				<td><?php echo format_nindya($hasil2tb); ?></td>
			</tr>
			<tr>
				<td>-</td>
				<td>Pekerjaan Kurang</td>
				<td><?php echo format_nindya($tata2); ?></td>
                <td><?php echo format_nindya($hasil2sdblnll_2); ?></td>
                <td><?php echo format_nindya($rencanatbhvolhrgkg_); ?></td>
                <td><?php echo format_nindya($realisasikurang); ?></td>
                <td><?php echo format_nindya($deviasikurang); ?></td>
                <td><?php echo format_nindya($hasil2krng_); ?></td>
                <td><?php echo format_nindya($sisaanggarankurang); ?></td>
                <td><?php echo format_nindya($perkiraansdselesaikurng); ?></td>
                <td><?php echo format_nindya($deviasitotalkurang); ?></td>
                <td><?php echo format_nindya($hasil21); ?></td>
			</tr>
			<tr>
				<td>-</td>
				<td>Beda Kurs</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
			</tr>
			<tr>
				<td>-</td>
				<td>Eskalasi</td>
				<td>0</td>
                <td><?php echo format_mede2($es2,2); ?></td>
                <td><?php echo format_mede2($es3,2); ?></td>
                <td><?php echo format_mede2($es4,2); ?></td>
                <td><?php echo format_mede2($es5,2); ?></td>
                <td><?php echo format_mede2($es6,2); ?></td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td><?php echo format_mede2($es10,2); ?></td>
			</tr>

    <?php
    $sql="select * from simpro_tbl_subbidang where subbidang_kode<>'509' and subbidang_kode<>'507' and length(subbidang_kode)=3 order by urutan";// 
    $res=$this->db->query($sql);
    $jml_row11 = $res->num_rows() + 8;
    ?>

			<tr>
				<td></td>
				<td><b>Beban Kontrak (BK)</b></td>
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
				<td rowspan="<?php echo $jml_row11; ?>">Rencana Penanggulangan : 
                    <?php
                    if(isset($xsql)){
                        foreach ($xsql as $xsqls) {
                            echo $xsqls['kkp_rencana'];
                        }
                    }
                    ?>
                </td>
			</tr>

    <?php

     $tot_jumlah_ob =0;
     $tot_jumlah_ctd_sblm =0;
     $tot_rencana =0;
     $tot_jumlah_ctd =0;
     $tot_deviasi =0;
     $tot_jumlah_ctd_sd =0;
     $tot_jumlah_cost_tg =0;
     $tot_jumlah_cf =0;
     $tot_jumlah_trend =0;
     $tot_jumlah_rencana =0;

    $sqlz="select * from simpro_tbl_subbidang where subbidang_kode<>'509' and subbidang_kode<>'507' and length(subbidang_kode)=3 order by urutan";//
    $resz=$this->db->query($sqlz);

    
    //===========BEBAN KONTRAK=========//
    //echo $sql;
    
    
    foreach($res->result_array() as $s){
 
    $sub_kode=substr($s['subbidang_kode'],0,3);
    
    //======================BAHAN==================================//
    $bhn1="select sum(jumlah_ob)as volumerab,sum(jumlah_cb)as volumecb from simpro_tbl_po2 where date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and proyek_id='$proyek_id' and detail_material_kode like'$sub_kode%' group by proyek_id";
    $rbhn1=$this->db->query($bhn1);
    $xbhn1=$rbhn1->row();
    $xbhn1_row=$rbhn1->num_rows();

    if($xbhn1_row==0) $volumerab=0; else $volumerab=$xbhn1->volumerab;

    //=====================S.D BLN LALU=======================//
    $bhn2="select sum(jumlah_cost_td)as bhn2ll from simpro_tbl_po2 where proyek_id='$proyek_id' and  tahap_tanggal_kendali <='$tgl_lalu'  and jumlah_cost_td !=0 and detail_material_kode like '$sub_kode%' group by proyek_id";//date_part('month',tanggal) <='$bulan_lalu'  and date_part('year',tanggal) <='$tahun_lalu'
    $rbhn2=$this->db->query($bhn2);//echo "$sql1<br>";
    $sub_jumlah_ctd_sblm=0;
    $xbhn2=$rbhn2->row();
    $xbhn2_row=$rbhn2->num_rows();
    if($xbhn2_row==0) $sub_jumlah_ctd_sblm=0; else $sub_jumlah_ctd_sblm=$xbhn2->bhn2ll==''?0:$xbhn2->bhn2ll;

    //========================RENCANA===================================//$thnlalu
    //$bhn3="select sum(volume_rencana * harga_sat_ob)as bhn3ll from tbl_po2 where no_spk='$cnospk_pilih'  and  date_part('month',tanggal)='$bulan_lalu' and date_part('year',tanggal)= '$tahun_lalu' and detail_material_kode like '$sub_kode%' group by no_spk";// and  date_part('month',tanggal)='$bulan_lalu' and date_part('year',tanggal)= '$tahun_lalu'
    $bhn3="select sum(rpbk_rrk1 * komposisi_harga_satuan_kendali) as bhn3ll from simpro_tbl_rpbk where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' and detail_material_kode like '$sub_kode%' group by proyek_id";
    $rbhn3=$this->db->query($bhn3);//echo "$sql1<br>";
    $sub_jumlah_rencana_bhn3=0;
    $xbhn3=$rbhn3->row();
    $xbhn3_row=$rbhn3->num_rows();

    if($xbhn3_row==0) $sub_jumlah_rencana_bhn3=0; else $sub_jumlah_rencana_bhn3=$xbhn3->bhn3ll;

    //=======================S.D BULAN INI============================//
    $bhn4="select sum(jumlah_cost_td)as bhn4ll from simpro_tbl_po2 where proyek_id='$proyek_id'  and tahap_tanggal_kendali <='$tgl_ini'  and detail_material_kode like '$sub_kode%' and jumlah_cost_td !=0 group by proyek_id";// date_part('month',tanggal) <='$bulan'  and date_part('year',tanggal) <='$tahun'
    $rbhn4=$this->db->query($bhn4);//echo "$sql1<br>";
    $sub_jumlah_rencana_bhn4=0;
    $xbhn4=$rbhn4->row();
    $xbhn4_row=$rbhn4->num_rows();

    if($xbhn4_row==0) $sub_jumlah_rencana_bhn4=0; else $sub_jumlah_rencana_bhn4=$xbhn4->bhn4ll;
    
    $sub_jumlah_rencana_bhn4;
    $realbhn5=$sub_jumlah_rencana_bhn4 - $sub_jumlah_ctd_sblm;
    $deviasibhn5 = $realbhn5 - $sub_jumlah_rencana_bhn3;
    //======================SISA ANGGARAN===========================//
    //nilai kontrak di kurangi sampai dengan bulan agustus
    $bhn5="select sum(jumlah_cost_tg)as bhn2ll from simpro_tbl_po2 where proyek_id='$proyek_id'  and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun'  and jumlah_cost_tg !=0 and detail_material_kode like '$sub_kode%' group by proyek_id";//and tanggal >= '$hasiltglkon' and tanggal<= '$thnlalu'
    $rbhn5=$this->db->query($bhn5);//echo "$sql1<br>";
    $nilaianggaranbhn5=0;
    $xbhn5=$rbhn5->row();
    $xbhn5_row=$rbhn5->num_rows();

    if($xbhn5_row==0) $nilaianggaranbhn5=0; else $nilaianggaranbhn5=$xbhn5->bhn2ll==''?0:$xbhn5->bhn2ll;

    if($xbhn1_row==0) $volume_cb=0; else $volume_cb=$xbhn1->volumecb;

    //$nilaianggaranbhn5=$volumerab - $sub_jumlah_rencana_bhn4."<br>";
    //$nilaianggaranbhn5=$xbhn5[bhn2ll];
    $perkiraanbhn6 = $sub_jumlah_rencana_bhn4 + $nilaianggaranbhn5;

    $deviasibhn7= $perkiraanbhn6-$volumerab ;
    
    //hide by octa $bhn8="select sum(volume_rencana_pbk * komposisi_harga_satuan_kendali) as rencanabhn8 from tbl_rpbk where no_spk='$cnospk_pilih' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and detail_material_kode like '$sub_kode%' group by tahap_tanggal_kendali,no_spk";//date_part('month',tanggal)='$bln_rncanadpn' and date_part('year',tanggal)='$tahun_rncn'
    $bhn8="select sum(rpbk_rrk1 * komposisi_harga_satuan_kendali) as rencanabhn8 from simpro_tbl_rpbk where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and detail_material_kode like '$sub_kode%' group by proyek_id";
    $resbhn8=$this->db->query($bhn8);//echo "$sql1<br>";

    $rbhn8=$resbhn8->row();
    $rbhn8_row=$resbhn8->num_rows();

    if($rbhn8_row==0) $rencanabhn8=0; else $rencanabhn8=$rbhn8->rencanabhn8;
    
    if($tanggal!=$tglkontrak->tahap_tanggal_kendali){
        if($xbhn1_row==0) $volumerab=0; else $volumerab=$xbhn1->volumecb;
    }
    
    $nilaianggaranbhn5=$volumerab-$sub_jumlah_rencana_bhn4;
    $perkiraanbhn6=$nilaianggaranbhn5+$sub_jumlah_rencana_bhn4;
    $deviasibhn7=$volumerab-$perkiraanbhn6;
    echo "<tr> 
    <td>$s[subbidang_kode]</td>
    <td>$s[subbidang_name]</td>
    <td>".format_nindya($volumerab)."</td>
    <td>".format_nindya($sub_jumlah_ctd_sblm)."</td>
    <td>".format_nindya($sub_jumlah_rencana_bhn3)."</td>
    <td>".format_nindya($realbhn5)."</td>
    <td>".format_nindya($deviasibhn5)."</td>
    <td>".format_nindya($sub_jumlah_rencana_bhn4)."</td><!---->
    <td>".format_nindya($nilaianggaranbhn5)."</td>
    <td>".format_nindya($perkiraanbhn6)."</td>
    <td>".format_nindya($deviasibhn7)."</td>
    <td>".format_nindya($rencanabhn8)."</td>
    </tr>";

     $tot_jumlah_ob +=$volumerab;
     $tot_jumlah_ctd_sblm +=$sub_jumlah_ctd_sblm;
     $tot_rencana +=$sub_jumlah_rencana_bhn3;
     $tot_jumlah_ctd +=$realbhn5;
     $tot_deviasi +=$deviasibhn5;
     $tot_jumlah_ctd_sd +=$sub_jumlah_rencana_bhn4;
     $tot_jumlah_cost_tg +=$nilaianggaranbhn5;
     $tot_jumlah_cf +=$perkiraanbhn6;
     $tot_jumlah_trend +=$deviasibhn7;
     $tot_jumlah_rencana +=$rencanabhn8;
    }
    //$sql_anggarantbh="select sum(tahap_volume_kendali * tahap_harga_satuan_kendali)as total from simpro_tbl_cost_togo where proyek_id='$proyek_id' and  tahap_kode_induk_kendali !='' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and tahap_volume_kendali !=0";
    
    $sql_anggarantbh="with costogo as (SELECT 
                simpro_costogo_item_tree.*,
                simpro_costogo_analisa_item_apek.kode_analisa,
                COALESCE(tbl_harga.harga, 0) AS harga,
                (COALESCE(tbl_harga.harga, 0) * simpro_costogo_item_tree.volume) as subtotal
            FROM simpro_costogo_item_tree 
            LEFT JOIN simpro_costogo_analisa_item_apek ON simpro_costogo_analisa_item_apek.kode_tree = simpro_costogo_item_tree.kode_tree
            LEFT JOIN (
                SELECT 
                DISTINCT ON(kode_analisa)
                                    kode_analisa,
                                    SUM(subtotal) AS harga
                FROM (
                (
                    SELECT                  
                        (simpro_costogo_analisa_asat.kode_analisa) AS kode_analisa, 
                        (simpro_costogo_analisa_asat.harga * simpro_costogo_analisa_asat.koefisien) AS subtotal
                    FROM 
                        simpro_costogo_analisa_asat
                    LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_costogo_analisa_asat.kode_material
                    LEFT JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_asat.kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_asat.id_proyek)
                    LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
                    WHERE simpro_costogo_analisa_asat.id_proyek= 9
                    and date_part('month',simpro_costogo_analisa_asat.tanggal_kendali)='02' and date_part('year',simpro_costogo_analisa_asat.tanggal_kendali)='2008'
                    ORDER BY 
                        simpro_costogo_analisa_asat.kode_analisa,
                        simpro_tbl_detail_material.detail_material_kode                
                    ASC
                )
                UNION ALL 
                (
                    SELECT 
                        (simpro_costogo_analisa_apek.parent_kode_analisa) AS kode_analisa, 
                        COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
                    FROM 
                        simpro_costogo_analisa_apek
                    INNER JOIN simpro_costogo_analisa_daftar ad ON (ad.kode_analisa = simpro_costogo_analisa_apek.kode_analisa AND ad.id_proyek= simpro_costogo_analisa_apek.id_proyek)
                    INNER JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_apek.parent_kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_apek.id_proyek)           
                    INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
                    LEFT JOIN (
                        SELECT 
                            DISTINCT ON(kode_analisa)
                            kode_analisa,
                            SUM(harga * koefisien) AS harga
                        FROM simpro_costogo_analisa_asat 
                        WHERE id_proyek= 9
                        and date_part('month',tanggal_kendali)='02' and date_part('year',tanggal_kendali)='2008'
                        GROUP BY kode_analisa           
                    ) as tbl_harga ON tbl_harga.kode_analisa = simpro_costogo_analisa_apek.kode_analisa         
                    WHERE simpro_costogo_analisa_apek.id_proyek= 9
                    and date_part('month',simpro_costogo_analisa_apek.tanggal_kendali)='02' and date_part('year',simpro_costogo_analisa_apek.tanggal_kendali)='2008'
                    ORDER BY 
                        simpro_costogo_analisa_apek.parent_kode_analisa,                
                        simpro_costogo_analisa_apek.kode_analisa
                    ASC                 
                )       
                ) AS tbl_analisa_satuan
                GROUP BY kode_analisa               
            ) as tbl_harga ON tbl_harga.kode_analisa = simpro_costogo_analisa_item_apek.kode_analisa                        
            WHERE simpro_costogo_item_tree.id_proyek = 9 and date_part('month',simpro_costogo_item_tree.tanggal_kendali)='02' and date_part('year',simpro_costogo_item_tree.tanggal_kendali)='2008'
            ORDER BY simpro_costogo_item_tree.kode_tree ASC)
            select sum(volume * harga) as total from costogo where volume!=0";

    $r_anggaran=$this->db->query($sql_anggarantbh);
    $volume_anggaran_ = 0;
    foreach($r_anggaran->result_array() as $x_anggaran){
        $volume_anggaran = $x_anggaran['total'];
        $volume_anggaran_ +=$volume_anggaran;
    }
    //echo $tot_jumlah_trend;

    ?>
			<tr class="list_data">
				<td></td>
				<td>Total BK</b></td>
                <td><?php echo format_mede2($tot_jumlah_ob,2); ?></td>
                <td><?php echo format_mede2($tot_jumlah_ctd_sblm,2); ?></td>
                <td><?php echo format_mede2($tot_rencana,2); ?></td>
                <td><?php echo format_mede2($tot_jumlah_ctd,2); ?></td>
                <td><?php echo format_mede2($tot_deviasi,2); ?></td>
                <td><?php echo format_mede2($tot_jumlah_ctd_sd,2); ?></td>
                <td><?php echo format_mede2($tot_jumlah_cost_tg,2); ?></td>
                <td><?php echo format_mede2($tot_jumlah_cf,2); ?></td>
                <td><?php echo format_mede2($tot_jumlah_trend,2); ?></td>
                <td><?php echo format_mede2($tot_jumlah_rencana,2); ?></td>
			</tr>
    <?php
    if($pukontrak==0)
        $bkkk1 = 0;
    else
        $bkkk1 = $tot_jumlah_ob/$pukontrak;

    if($pudblnll==0)
        $bkkk2 = 0;
    else
        $bkkk2 = $tot_jumlah_ctd_sblm/$pudblnll;

    if($purencan==0)
        $bkkk3 = 0;
    else
        $bkkk3 = $tot_rencana/$purencan;

    if($purealisasi==0)
        $bkkk4 = 0;
    else
        $bkkk4 = $tot_jumlah_ctd/$purealisasi;

    if($pudeviasi==0)
        $bkkk5 = 0;
    else
        $bkkk5 = $tot_deviasi/$pudeviasi;

    if($pusdblnini==0)
        $bkkk6 = 0;
    else
        $bkkk6 = $tot_jumlah_ctd_sd/$pusdblnini;

    if($puanggaran==0)
        $bkkk7 = 0;
    else
        $bkkk7 = $tot_jumlah_cost_tg/$puanggaran;

    if($puperkiraan==0)
        $bkkk8 = 0;
    else
        $bkkk8 = $tot_jumlah_cf/$puperkiraan;

    if($pudeviasito==0)
        $bkkk9 = 0;
    else
        $bkkk9 = $tot_jumlah_trend/$pudeviasito;

    if($purencanadpn==0)
        $bkkk10 = 0;
    else
        $bkkk10 = $tot_jumlah_rencana/$purencanadpn;

    ?>	
			<tr>
				<td></td>
				<td>% Beban Kontrak (BK)</b></td>
				<td><?php echo format_mede(($bkkk1)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk2)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk3)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk4)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk5)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk6)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk7)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk8)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk9)*100,2); ?> %</td>
                <td><?php echo format_mede(($bkkk10)*100,2); ?> %</td>
			</tr>

    <?php
    $spkontrak = $pukontrak - $tot_jumlah_ob; 
    $spdblnll = ($sdproglalu + $hasil2sdblnll_2 + $hasil2sdblnll_) - $tot_jumlah_ctd_sblm ;
    $sprencan = ($sdprogrc + $rencanatbhvolhrgkg_ + $rencanatbhvolhrg_) - $tot_rencana;
    $sprealisasi = ($kontrakrealisasi + $realisaitambah + $realisasikurang) - $tot_jumlah_ctd;
    $spdeviasi = ($kontrakdeviasi + $deviasikurang + $deviasitambah) - $tot_deviasi ;
    $spsdblnini = ($sdprogini + $hasil2krng_ + $hasil2tbh) - $tot_jumlah_ctd_sd ;
    $spanggaran = ($kontraksisaanggaran + $sisaanggarankurang + $sisaanggarantambah) - $tot_jumlah_cost_tg;
    $spperkiraan = ($kontrakperkiraan + $perkiraansdselesaitmbah + $perkiraansdselesaikurng) - $tot_jumlah_cf;
    $spdeviasito = ($kontrakdeviasii + $deviasitotaltambah + $deviasitotalkurang) -$tot_jumlah_trend ;
    $sprencanadpn = ($sdprogrcdpn + $hasil2tb+$hasil21) - $tot_jumlah_rencana;
    ?>


			<tr class="list_data">
				<td>-</td>
				<td>Selisih Pendapatan (SP)</td>
				<td><?php echo format_mede2($spkontrak,2); ?></td>
                <td><?php echo format_mede2($spdblnll,2); ?></td>
                <td><?php echo format_mede2($sprencan,2); ?></td>
                <td><?php echo format_mede2($sprealisasi,2); ?></td>
                <td><?php echo format_mede2($spdeviasi,2); ?></td>
                <td><?php echo format_mede2($spsdblnini,2); ?></td>
                <td><?php echo format_mede2($spanggaran,2); ?></td>
                <td><?php echo format_mede2($spperkiraan,2); ?></td>
                <td><?php echo format_mede2($spdeviasito,2); ?></td>
                <td><?php echo format_mede2($sprencanadpn,2); ?></td>
			</tr>

    <?php
    //MOS
    //awal
    $mos="select sum(mos_total_volume)as total from simpro_tbl_mos where proyek_id='$proyek_id' group by proyek_id";
    $result_mos=$this->db->query($mos);
    $x_mos=$result_mos->row();
    $x_mos_row=$result_mos->num_rows();

    if($x_mos_row==0) $hasilmos=0; else $hasilmos=$x_mos->total;
    //sd bln lalu
    $mos1="select sum(mos_diakui_volume)as total from simpro_tbl_mos where proyek_id='$proyek_id' and mos_tgl <='$tgl_lalu' group by proyek_id";//date_part('month',mos_tgl) <='$bulan_lalu'  and date_part('year',mos_tgl) <='$tahun_lalu'
    $result_mos1=$this->db->query($mos1);
    $x_mos1=$result_mos1->row();
    $x_mos1_row=$result_mos1->num_rows();
    
    if($x_mos1_row==0) $hasilmos1=0; else $hasilmos1=$x_mos1->total;
    //realisasi
    $mos2="select sum(mos_diakui_volume)as total from simpro_tbl_mos where proyek_id='$proyek_id' and  date_part('month',mos_tgl)='$bulan_lalu' and date_part('year',mos_tgl)='$tahun_lalu' group by proyek_id";
    $result_mos2=$this->db->query($mos2);
    $x_mos2=$result_mos2->row();
    $x_mos2_row=$result_mos2->num_rows();
    
    if($x_mos2_row==0) $hasilmos2=0; else $hasilmos2=$x_mos2->total;
    //sdbln ini
    $mos3="select sum(mos_diakui_volume)as total from simpro_tbl_mos where proyek_id='$proyek_id' and mos_tgl <='$tgl_ini' group by proyek_id";//date_part('month',mos_tgl) <='$bulan'  and date_part('year',mos_tgl) <='$tahun'
    $result_mos3=$this->db->query($mos3);
    $x_mos3=$result_mos3->row();
    $x_mos3_row=$result_mos3->num_rows();

    if($x_mos3_row==0) $hasilmos3=0; else $hasilmos3=$x_mos3->total;
    
    $hasilmos4=$hasilmos - $hasilmos3;
    $hasilmos5=$hasilmos3 + $hasilmos4;
    $hasilmos6=$hasilmos - $hasilmos5;
    ?>


			<tr>
				<td>-</td>
				<td>Stock Bahan</b></td>
				<td><?php echo $hasilmos; ?></td>
                <td><?php echo $hasilmos1; ?></td>
                <td>0</td>
                <td><?php echo $hasilmos2; ?></td>
                <td><?php echo $hasilmos2; ?></td>
                <td><?php echo $hasilmos3; ?></td>
                <td><?php echo $hasilmos4; ?></td>
                <td><?php echo $hasilmos5; ?></td>
                <td><?php echo $hasilmos6; ?></td>
                <td>0</td>
			</tr>

    <?php

    function kolom_pph1($proyek_id,$bulan,$tahun){
        
        $bhn1="select sum(jumlah_ob)as volumerab,sum(jumlah_cb)as volumecb from simpro_tbl_po2 where date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and proyek_id='$proyek_id' and detail_material_kode like'507%' group by proyek_id";
        $rbhn1=pg_query($bhn1);
        $xbhn1=pg_fetch_array($rbhn1);
        $volumerab=$xbhn1['volumerab'];
        return format_nindya($volumerab);
    }
    function kolom_pph2($proyek_id,$tgl_lalu){
        
        $bhn2="select sum(jumlah_cost_td)as bhn2ll from simpro_tbl_po2 where proyek_id='$proyek_id' and  tahap_tanggal_kendali <='$tgl_lalu'  and (jumlah_cost_td !=0 or jumlah_cost_td is not null) and detail_material_kode like '507%' group by proyek_id";
        $rbhn2=pg_query($bhn2);
        $sub_jumlah_ctd_sblm=0;
        $xbhn2=pg_fetch_array($rbhn2);
        $sub_jumlah_ctd_sblm=$xbhn2['bhn2ll']==''?0:$xbhn2['bhn2ll'];
        $kolom1=format_nindya($sub_jumlah_ctd_sblm);
        
        return $kolom1 ;
    }
    function kolom_pph3($proyek_id,$bulan_lalu,$tahun_lalu){
        
        $bhn3="select sum(volume_rencana * harga_sat_ob)as bhn3ll from simpro_tbl_po2 where proyek_id='$proyek_id'  and  date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)= '$tahun_lalu' and detail_material_kode like '507%' group by proyek_id";
        $rbhn3=pg_query($bhn3);
        $sub_jumlah_rencana_bhn3=0;
        $xbhn3=pg_fetch_array($rbhn3);
        $sub_jumlah_rencana_bhn3=$xbhn3['bhn3ll'];
        
        return format_nindya($sub_jumlah_rencana_bhn3);
    }
    function kolom_pph4($proyek_id,$tgl_ini){
        $bhn4="select sum(jumlah_cost_td)as bhn4ll from simpro_tbl_po2 where proyek_id='$proyek_id'  and tahap_tanggal_kendali <='$tgl_ini'  and detail_material_kode like '507%' and jumlah_cost_td !=0 group by proyek_id";// date_part('month',tanggal) <='$bulan'  and date_part('year',tanggal) <='$tahun'
        $rbhn4=pg_query($bhn4);//echo "$sql1<br>";
        $sub_jumlah_rencana_bhn4=0;
        $xbhn4=pg_fetch_array($rbhn4);
        $sub_jumlah_rencana_bhn4=$xbhn4['bhn4ll'];
        return format_nindya($sub_jumlah_rencana_bhn4);
    }
    function kolom_pph7($proyek_id,$tgl_ini){
        $bhn4="select sum(jumlah_cost_td)as bhn4ll from simpro_tbl_po2 where proyek_id='$proyek_id'  and tahap_tanggal_kendali <='$tgl_ini'  and detail_material_kode like '507%' and jumlah_cost_td !=0 group by proyek_id";// date_part('month',tanggal) <='$bulan'  and date_part('year',tanggal) <='$tahun'
        $rbhn4=pg_query($bhn4);//echo "$sql1<br>";
        $sub_jumlah_rencana_bhn4=0;
        $xbhn4=pg_fetch_array($rbhn4);
        $sub_jumlah_rencana_bhn4=$xbhn4['bhn4ll'];
        return format_nindya($sub_jumlah_rencana_bhn4);
    }
    function kolom_pph8($proyek_id,$bulan,$tahun){
        $bhn5="select sum(jumlah_cost_tg)as bhn2ll from simpro_tbl_po2 where proyek_id='$proyek_id'  and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun'  and jumlah_cost_tg !=0 and detail_material_kode like '507%' group by proyek_id";
        $rbhn5=pg_query($bhn5);//echo "$sql1<br>";
        $nilaianggaranbhn5=0;
        $xbhn5=pg_fetch_array($rbhn5);
        $nilaianggaranbhn5=$xbhn5['bhn2ll'];
        return format_nindya($nilaianggaranbhn5);
    }
    function kolom_pph11($proyek_id,$bulan,$tahun){
        $bhn8="select sum(rpbk_rrk1 * komposisi_harga_satuan_kendali) as rencanabhn8 from simpro_tbl_rpbk where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and detail_material_kode like '507%' group by proyek_id";
        $resbhn8=pg_query($bhn8);
        $rbhn8=pg_fetch_array($resbhn8);
        $rencanabhn8=$rbhn8['rencanabhn8'];
        return format_nindya($rencanabhn8);
    }
    $kolom_pph9 = kolom_pph4($proyek_id,$tgl_ini) + kolom_pph8($proyek_id,$bulan,$tahun);
    $kolom_pph5=kolom_pph4($proyek_id,$tgl_ini) - kolom_pph2($proyek_id,$tgl_lalu);
    $kolom_pph6 = $kolom_pph5 - kolom_pph3($proyek_id,$bulan_lalu,$tahun_lalu);
    $kolom_pph10= kolom_pph1($proyek_id,$bulan,$tahun) - $kolom_pph9;
    
    function pot_pph($proyek_id,$pukontrak,$pudblnll,$purencan,$purealisasi,$pudeviasi,$pusdblnini,$puanggaran,$puperkiraan,$pudeviasito,$purencanadpn){
        $sql="select * from simpro_tbl_proyek where proyek_id='$proyek_id'";
        $result=pg_query($sql);
        $r=pg_fetch_array($result);
        
        $pot_pph1=($r["pph_final"]/100)*$pukontrak;
        $pot_pph2=($r["pph_final"]/100)*$pudblnll;
        $pot_pph3=($r["pph_final"]/100)*$purencan;
        $pot_pph4=($r["pph_final"]/100)*$purealisasi;
        $pot_pph5=($r["pph_final"]/100)*$pudeviasi;
        $pot_pph6=($r["pph_final"]/100)*$pusdblnini;
        $pot_pph7=($r["pph_final"]/100)*$puanggaran;
        $pot_pph8=($r["pph_final"]/100)*$puperkiraan;
        $pot_pph9=($r["pph_final"]/100)*$pudeviasito;
        $pot_pph10=($r["pph_final"]/100)*$purencanadpn;
        return array($pot_pph1,$pot_pph2,$pot_pph3,$pot_pph4,$pot_pph5,$pot_pph6,$pot_pph7,$pot_pph8,$pot_pph9,$pot_pph10);
    }
    $potongan_pph=pot_pph($proyek_id,$pukontrak,$pudblnll,$purencan,$purealisasi,$pudeviasi,$pusdblnini,$puanggaran,$puperkiraan,$pudeviasito,$purencanadpn);
    
    $lp1=$hasilmos + $spkontrak ;//+ $potongan_pph[0]
    $lp2=$hasilmos1 + $spdblnll ;
    $lp3=$hasilmos2 + $sprealisasi;
    $lp4=$hasilmos2 + $spdeviasi ;
    $lp5=$hasilmos3 + $spsdblnini ;
    $lp6=$hasilmos4 + $spanggaran ;
    $lp7=$hasilmos5 + $spperkiraan ;
    $lp8=$spdeviasito + $hasilmos6;
    ?>

			<tr class="list_data">
				<td>-</td>
				<td>Laba Proyek</td>
				<td><?php echo format_mede2($lp1,2); ?></td>
                <td><?php echo format_mede2($lp2,2); ?></td>
                <td>0</td>
                <td><?php echo format_mede2($lp3,2); ?></td>
                <td><?php echo format_mede2($lp4,2); ?></td>
                <td><?php echo format_mede2($lp5,2); ?></td>
                <td><?php echo format_mede2($lp6,2); ?></td>
                <td><?php echo format_mede2($lp7,2); ?></td>
                <td><?php echo format_mede2($lp8,2); ?></td>
                <td>0</td>
			</tr>	
			<tr>
				<td></td>
				<td>Potongan PPH</b></td>
				<td><?php echo format_nindya($potongan_pph[0]); ?></td>
                <td><?php echo format_nindya($potongan_pph[1]); ?></td>
                <td><?php echo format_nindya($potongan_pph[2]); ?></td>
                <td><?php echo format_nindya($potongan_pph[3]); ?></td>
                <td><?php echo format_nindya($potongan_pph[4]); ?></td>
                <td><?php echo format_nindya($potongan_pph[5]); ?></td>
                <td><?php echo format_nindya($potongan_pph[6]); ?></td>
                <td><?php echo format_nindya($potongan_pph[7]); ?></td>
                <td><?php echo format_nindya($potongan_pph[8]); ?></td>
                <td><?php echo format_nindya($potongan_pph[9]); ?></td>
			</tr>
			<tr class="list_data">
				<td></td>
				<td>Laba Bersih</td>
				<td><?php echo format_mede2($lp1-$potongan_pph[0],2); ?></td>
                <td><?php echo format_mede2($lp2-$potongan_pph[1],2); ?></td>
                <td></td>
                <td><?php echo format_mede2($lp3-$potongan_pph[3],2); ?></td>
                <td><?php echo format_mede2($lp4-$potongan_pph[4],2); ?></td>
                <td><?php echo format_mede2($lp5-$potongan_pph[5],2); ?></td>
                <td><?php echo format_mede2($lp6-$potongan_pph[6],2); ?></td>
                <td><?php echo format_mede2($lp7-$potongan_pph[7],2); ?></td>
                <td><?php echo format_mede2($lp8-$potongan_pph[8],2); ?></td>
                <td></td>
			</tr>
			<tr>
				<td>-</td>
				<td colspan="12">Cash Proyek</td>
			</tr>
			<tr class="list_data">
				<td>-</td>
				<td colspan="12">Cash in</td>
			</tr>

    <?php
    //CASHFLOW IN CURENT BUGET
    $cb0="select sum(tahap_volume_kendali*tahap_harga_satuan_kendali) as total from simpro_tbl_kontrak_terkini where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' group by proyek_id ";
    $rcb0=pg_query($cb0);
    $xcb0=pg_fetch_array($rcb0);
    $uang_muka="select uang_muka from simpro_tbl_proyek where proyek_id='$proyek_id'";
    $r_uangmuka=pg_query($uang_muka);
    $uangmuka=pg_fetch_array($r_uangmuka);
    
    $nilaicb0 =round($xcb0['total'] * ($uangmuka['uang_muka']/100));
    
     $nilaitermin="select uang_muka from simpro_tbl_proyek where proyek_id='$proyek_id'"; 
     $rmuka=pg_query($nilaitermin);
     $nilai_uangmuka=pg_fetch_array($rmuka);
     $umcash=round($xcb0['total'] * ($nilai_uangmuka['uang_muka']/100),2);
    
    $retensi=round($xcb0['total'] * 5/100);    
    //SD BL LALU
    $mg0="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',tahap_tanggal_kendali) <='$tahun_lalu' and ket_id='1' group by proyek_id";//
    $rmg0=pg_query($mg0);
    $xmg0=pg_fetch_array($rmg0);
    $mg1="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',tahap_tanggal_kendali) <='$tahun_lalu' and ket_id='2' group by proyek_id";
    $rmg1=pg_query($mg1);
    $xmg1=pg_fetch_array($rmg1);
    $mg3="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',tahap_tanggal_kendali) <='$tahun_lalu' and ket_id='4' group by proyek_id";
    $rmg3=pg_query($mg3);
    $xmg3=pg_fetch_array($rmg3);
    $mg4="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',tahap_tanggal_kendali) <='$tahun_lalu' and ket_id='5' group by proyek_id";
    $rmg4=pg_query($mg4);
    $xmg4=pg_fetch_array($rmg4);
    $mg5="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) <='$bulan_lalu'  and date_part('year',tahap_tanggal_kendali) <='$tahun_lalu' and ket_id='6' group by proyek_id";
    $rmg5=pg_query($mg5);
    $xmg5=pg_fetch_array($rmg5);
    
    //REALISASI
    $sql0="select * from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='1'";
    $rsql0=pg_query($sql0);
    $xsql0=pg_fetch_array($rsql0);
    $sql1="select * from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='2'";
    $rsql1=pg_query($sql1);
    $xsql1=pg_fetch_array($rsql1);
    $sql3="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='4' ";
    $rsql3=pg_query($sql3);
    $xsql3=pg_fetch_array($rsql3);
    $sql4="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='5' ";
    $rsql4=pg_query($sql4);
    $xsql4=pg_fetch_array($rsql4);
    $sql5="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='6' ";
    $rsql5=pg_query($sql5);
    $xsql5=pg_fetch_array($rsql5);
    
    //SD BULAN INI
    $mgll0="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and tahap_tanggal_kendali <='$tgl_ini'  and ket_id='1' group by proyek_id";
    $rmgll0=pg_query($mgll0);
    $xmgll0=pg_fetch_array($rmgll0);
    $xmgll0=$xmgll0['realisasi']==''?0:$xmgll0['realisasi'];
    
    $mgll1="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) <='$bulan'  and date_part('year',tahap_tanggal_kendali) <='$tahun'  and ket_id='2' group by  proyek_id";
    $rmgll1=pg_query($mgll1);
    $xmgll1=pg_fetch_array($rmgll1);
    $xmgll1=$xmgll1['realisasi']==''?0:$xmgll1['realisasi'];
    
     $mgll3="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and tahap_tanggal_kendali <='$tgl_ini' and ket_id='4' group by proyek_id";
    $rmgll3=pg_query($mgll3);
    $xmgll3=pg_fetch_array($rmgll3);
    $xmgll3=$xmgll3['realisasi']==''?0:$xmgll3['realisasi'];
    
    $mgll4="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and tahap_tanggal_kendali <='$tgl_ini'  and ket_id='5' group by proyek_id";
    $rmgll4=pg_query($mgll4);
    $xmgll4=pg_fetch_array($rmgll4);
    $xmgll4=$xmgll4['realisasi']==''?0:$xmgll4['realisasi'];
    
    $mgll5="select sum(realisasi) as realisasi from simpro_tbl_cashin where proyek_id='$proyek_id' and tahap_tanggal_kendali <='$tgl_ini'  and ket_id='6' group by proyek_id";
    $rmgll5=pg_query($mgll5);
    $xmgll5=pg_fetch_array($rmgll5);
    $xmgll5_=$xmgll5['realisasi']==''?0:$xmgll5['realisasi'];
    
    //RENCANA
    $bulancfrencana=$bulan-1;
    $rencana1="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' and ket_id='1'";
    $rrencana1=pg_query($rencana1);
    $xrencana1=pg_fetch_array($rrencana1);
    
    $rencana2="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' and ket_id='2'";
    $rrencana2=pg_query($rencana2);
    $xrencana2=pg_fetch_array($rrencana2);
    
    $rencana4="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' and ket_id='4'";
    $rrencana4=pg_query($rencana4);
    $xrencana4=pg_fetch_array($rrencana4);
    
    $rencana5="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' and ket_id='5'";
    $rrencana5=pg_query($rencana5);
    $xrencana5=pg_fetch_array($rrencana5);
    
    $rencana6="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' and ket_id='6'";
    $rrencana6=pg_query($rencana6);
    $xrencana6=pg_fetch_array($rrencana6);
    
    
    //DEVIASI
    $cfdev=$umcash - ($xrencana1['rproyeksi1'] + $xrencana1['rproyeksi2']);
    $cfdev2=$xsql1['realisasi'] - ($xrencana2['rproyeksi1'] + $xrencana2['rproyeksi2']);
    $cfdev4=$xsql3['realisasi'] - ($xrencana4['rproyeksi1'] + $xrencana4['rproyeksi2']);
    $cfdev5=$xsql4['realisasi'] - ($xrencana5['rproyeksi1'] + $xrencana5['rproyeksi2']);
    $cfdev6=(-$xsql5['realisasi']) - (-($xrencana6['rproyeksi2'] + $xrencana6['rproyeksi3']));
    //SISA ANGGARAN
    $cfsisaanggaran1=$nilaicb0 - $xmgll0;
    $cfsisaanggaran2=$xcb0['total'] ;//- $nilaicb0;//round($xcb0[total] - $xmgll1);
    $cfsisaanggaran4=-$xsql0['realisasi']-$xmgll3['realisasi'];//(-$nilaicb0)-($xmgll3[realisasi]);//(-$nilaicb0) - $xmgll3[realisasi];
    $cfsisaanggaran5=(-$retensi) - $xmgll4['realisasi'];
    $cfsisaanggaran6=-(round($xcb0['total'] * 3/100))-(-$xmgll5_);//(round($xcb0[total] * 3/100)) - $xmgll5[realisasi];
    
    //PERKIRAAN
    //$xmgll0."-".$cfsisaanggaran1;
    
    $cfperkiraan1=$xmgll0 + $cfsisaanggaran1;
    $cfperkiraan2=$xmgll1['realisasi'] + $cfsisaanggaran2;
    $cfperkiraan4=$xmgll3['realisasi'] + $cfsisaanggaran4;
    $cfperkiraan5=$xmgll4['realisasi'] + $cfsisaanggaran5;
    $cfperkiraan6=-$xmgll5_+$cfsisaanggaran6;//(-$cfsisaanggaran6)+(-$xmgll5_);//$xmgll5[realisasi] + $cfsisaanggaran6;
    //DEVIASI TOTAL
    $cfdevtotal1=$xsql0['realisasi'] - $cfperkiraan1;//$nilaicb0
    $cfdevtotal2=$cfperkiraan2-$cfsisaanggaran2;//$xcb0[total] - $cfperkiraan2;
    $cfdevtotal4=-$xsql0['realisasi']-$cfperkiraan4;//(-$nilaicb0) - $cfperkiraan4;
    $cfdevtotal5=(-$retensi) - $cfperkiraan5;
    $xcb0['total']."-".$cfperkiraan5."<br>";
    $cfperkiraan5;
    $cfdevtotal6=-round($xcb0['total'] * 3/100)-$cfperkiraan6;
    //(round($xcb0[total] * 3/100)) - ;
    
    //RENCANA BLN DEPAN
    $rencanadpn1="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='1'";
    $rrencanadpn1=pg_query($rencanadpn1);
    $xrencanadpn1=pg_fetch_array($rrencanadpn1);
    $rencanadpn2="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='2'";
    $rrencanadpn2=pg_query($rencanadpn2);
    $xrencanadpn2=pg_fetch_array($rrencanadpn2);
    $rencanadpn4="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='4'";
    $rrencanadpn4=pg_query($rencanadpn4);
    $xrencanadpn4=pg_fetch_array($rrencanadpn4);
    $rencanadpn5="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='5'";
    $rrencanadpn5=pg_query($rencanadpn5);
    $xrencanadpn5=pg_fetch_array($rrencanadpn5);
    $rencanadpn6="select *  from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='6'";
    $rrencanadpn6=pg_query($rencanadpn6);
    $xrencanadpn6=pg_fetch_array($rrencanadpn6);
    
    function hutang_pusat($proyek_id,$tahun_,$bulan_){
        $sql="select sum(jumlah) as total from simpro_tbl_hutangonkeu where date_part('month',tanggal)='$bulan_' and date_part('year',tanggal)='$tahun_' and proyek_id='$proyek_id' and pilihan='4' group by proyek_id";
        $r_sql=pg_query($sql);
        $xsql=pg_fetch_array($r_sql);
        return format_mede2($xsql['total']);
    }
    function hutang_proyek($proyek_id,$tahun_,$bulan_){
        $sql="select sum(jumlah) as total from simpro_tbl_hutangonkeu where date_part('month',tanggal)='$bulan_' and date_part('year',tanggal)='$tahun_' and proyek_id='$proyek_id' and pilihan='1' group by proyek_id";
        $r_sql=pg_query($sql);
        $xsql=pg_fetch_array($r_sql);
        return $xsql['total'];
    }
    function bayar_kini_proyek($proyek_id,$tahun_,$bulan_){
  
    $sql="select sum(terbayar)as jlh from simpro_tbl_hutangonkeu where proyek_id='$proyek_id' and date_part('month',tanggal)='$bulan_' and date_part('year',tanggal)='$tahun_' and pilihan='1' group by proyek_id ";
    $res11=pg_query($sql);
    $r11=pg_fetch_array($res11);
        return $r11['jlh'];
    }
    function hutang_divisi($proyek_id,$tahun_,$bulan_){
        $sql="select sum(jumlah) as total from simpro_tbl_hutangonkeu where proyek_id='$proyek_id' and date_part('month',tanggal)='$bulan_' and date_part('year',tanggal)='$tahun_'  and pilihan in('2','3') group by proyek_id";
        $r_sql=pg_query($sql);
        $xsql=pg_fetch_array($r_sql);
        return $xsql['total'];
    }
    function bayar_kini_divisi($proyek_id,$tahun_,$bulan_){
  
    $sql="select sum(terbayar)as jlh from simpro_tbl_hutangonkeu where proyek_id='$proyek_id' and date_part('month',tanggal)='$bulan_' and date_part('year',tanggal)='$tahun_' and pilihan in('2','3') group by proyek_id ";
    $res11=pg_query($sql);
    $r11=pg_fetch_array($res11);
    return $r11['jlh'];
    }    
    function antisipasi($proyek_id,$bulan,$tahun){
        $sql="select sum(jumlah) as total from simpro_tbl_hutang_proses where proyek_id='$proyek_id' and date_part('month',tanggal)='$bulan' and date_part('year',tanggal)='$tahun'  group by proyek_id";
        $r_sql=pg_query($sql);
        $xsql=pg_fetch_array($r_sql);
        return format_mede2($xsql['total'],0);
    }
    $total_hutang=hutang_pusat($proyek_id,$tahun_,$bulan_)+hutang_proyek($proyek_id,$tahun_,$bulan_)+hutang_divisi($proyek_id,$tahun_,$bulan_);
    $sisa_hut_proyek=hutang_proyek($proyek_id,$tahun_,$bulan_)- bayar_kini_proyek($proyek_id,$tahun_,$bulan_);
    $sisa_hut_divisi=hutang_divisi($proyek_id,$tahun_,$bulan_)- bayar_kini_divisi($proyek_id,$tahun_,$bulan_);
    $cfsisaanggaran1=$xsql0['realisasi']-$xmgll0;//$xmgll0-$cfdev;
    
     $cfsisaanggaran1=$xsql0['realisasi']-$xsql0['realisasi'];//$xmgll0-$cfdev;
     
    $cfsisaanggaran1=$xsql0['realisasi']-$xsql0['realisasi'];//$xmgll0-$cfdev;
    
    
     
    
    $um1=$umcash;//$xsql0[realisasi];
    $um2=$xmg0['realisasi'];
    $um3=($xrencana1['rproyeksi2'] + $xrencana1['rproyeksi3']);
    $um4=$xsql0['realisasi'];
    $um5=$um3-$um4;//$cfdev;
    $um6=$xmgll0;
    $um7=$um1-$um6;//$cfsisaanggaran1;
    $um8=$um6+$um7;//$cfperkiraan1;
    $um9=$um1-$um8;//$cfdevtotal1;
    $um10=($xrencanadpn1['rproyeksi2'] + $xrencanadpn1['rproyeksi3']);
    
    $pum1=(-$umcash);
    $pum2=-$xmg3['realisasi'];
    $pum3=-($xrencana4['rproyeksi2'] + $xrencana4['rproyeksi3']);
    $pum4=-$xsql3['realisasi'];
    $pum5=$pum3-$pum4;//$cfdev4;
    $pum6=-$xmgll3;
    $pum7=$pum1-$pum6;//$cfsisaanggaran4;
    $pum8=$pum6+$pum7;//$cfperkiraan4;
    $pum9=$pum1-$pum8;
    $pum10=-($xrencanadpn4['rproyeksi2'] + $xrencanadpn4['rproyeksi3']);
    
    $ter1=$xcb0['total'];
    $ter2=$xmg1['realisasi'];
    $ter3=($xrencana2['rproyeksi2'] + $xrencana2['rproyeksi3']);
    $ter4=$xsql1['realisasi'];
    $ter5=$ter3-$ter4;
    $ter6=$xmgll1;
    $ter7=$ter1-$ter6;
    $ter8=$ter6+$ter7;
    $ter9=$ter1-$ter8;
    $ter10=($xrencanadpn2['rproyeksi2'] + $xrencanadpn2['rproyeksi3']);
    
    $pph1=-(round($xcb0['total'] * 3/100));
    $pph2=-($xmg5['realisasi']);
    $pph3=-($xrencana6['rproyeksi2'] + $xrencana6['rproyeksi3']);
    $pph4=(-$xsql5['realisasi']);
    $pph5=$pph3-$pph4;//$cfdev6;
    $pph6=(-$xmgll5_);
    $pph7=$pph1-$pph6;//$cfsisaanggaran6;
    $pph8=$pph6+$pph7;//$cfperkiraan6;
    $pph9=$pph1-$pph8;//$cfdevtotal6;
    $pph10=-($xrencanadpn6['rproyeksi2'] + $xrencanadpn6['rproyeksi3']);
    
    
    $ret1=-$retensi;
    $ret2=$xmg4['realisasi'];
    $ret3=($xrencana5['rproyeksi2'] + $xrencana4['rproyeksi3']);
    $ret4=$xsql4['realisasi'];
    $ret5=$ret3-$ret4;//$cfdev5;
    $ret6=$xmgll4;
    $ret7=$ret1-$ret6;//$cfsisaanggaran5;
    $ret8=$ret6+$ret7;//$cfperkiraan5;
    $ret9=$ret1-$ret8;//$cfdevtotal5;
    $ret10=-($xrencanadpn5['rproyeksi2'] + $xrencanadpn5['rproyeksi3']);
  
    $sqlsbp="select * from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali) = '$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id='16'";
    $rsqlsbp=pg_query($sqlsbp);
    $xrsqlsbp=pg_fetch_array($rsqlsbp);
    $hasilsqlsbp=$xrsqlsbp['sbp']==''?0:$xrsqlsbp['sbp'];
    $hasilsqlspp=$xrsqlsbp['spp']==''?0:$xrsqlsbp['spp'];
    ?>


			<tr>
				<td>-</td>
				<td>Uang Muka</td>
				<td><?php echo format_nindya($um1); ?></td>
                <td><?php echo format_nindya($um2); ?></td>
                <td><?php echo format_mede2($um3,0); ?></td>
                <td><?php echo format_mede2($um4,0); ?></td>
                <td><?php echo format_mede2($um5,0); ?></td>
                <td><?php echo format_mede2($um6,0); ?></td>
                <td><?php echo format_mede2($um7,0); ?></td>
                <td><?php echo format_mede2($um8,0); ?></td>
                <td><?php echo format_mede2($um9,0); ?></td>
                <td><?php echo format_mede2($um10,0); ?></td>
				<td rowspan="13">
					<p><b>Catatan</b></p>
					- Hutang Lewat Pusat =	Rp&nbsp;&nbsp;<?php echo hutang_pusat($proyek_id,$bulan,$tahun); ?><br />
					- Hutang Lewat Divis =	Rp&nbsp;&nbsp;<?php echo format_mede2($sisa_hut_divisi,0); ?><br />
					- Hutang Lewat Proyek =	Rp&nbsp;&nbsp;<?php echo format_mede2($sisa_hut_proyek,0); ?><br />
					TOTAL	Rp&nbsp;&nbsp;<?php echo format_mede2($total_hutang,0); ?><br />
					Pos Antisipasi =	Rp&nbsp;&nbsp;<?php echo antisipasi($proyek_id,$bulan,$tahun); ?><br />
					Saldo Bank Proyek=	Rp&nbsp;&nbsp;<?php echo format_mede2($hasilsqlsbp,0); ?><br />
					Saldo Panj.Pelaksana=	Rp&nbsp;&nbsp;<?php echo format_mede2($hasilsqlspp,0); ?>					
				</td>
			</tr>
			<tr>
				<td></td>
				<td>Pengembalian Uang Muka</b></td>
				<td><?php echo format_nindya($pum1); ?></td>
                <td><?php echo format_nindya($pum2); ?></td>
                <td><?php echo format_nindya($pum3); ?></td>
                <td><?php echo format_nindya($pum4); ?></td>
                <td><?php echo format_nindya($pum5); ?></td>
                <td><?php echo format_nindya($pum6); ?></td>
                <td><?php echo format_nindya($pum7); ?></td>
                <td><?php echo format_nindya($pum8); ?></td>
                <td><?php echo format_nindya($pum9); ?></td>
                <td><?php echo format_nindya($pum10); ?></td>
			</tr>
    <?php
    $sum1=$nilaicb0 + (-$nilaicb0);
    $sum2=$xmg0['realisasi'] + $xmg3['realisasi'];
    $sum3=($xrencana1['rproyeksi1'] + $xrencana1['rproyeksi2']) + ($xrencana4['rproyeksi1'] + $xrencana4['rproyeksi2']);
    $sum4=$xsql0['realisasi'] + $xsql3['realisasi'];
    $sum5=$cfdev + $cfdev4;
    $sum6=$xmgll0 - $xmgll3['realisasi'];
    $sum7=$cfsisaanggaran1 + $cfsisaanggaran4;
    $sum8=$cfperkiraan1 + $cfperkiraan4;
    $sum9=$cfdevtotal1 + $cfdevtotal4;
    $sum10=($xrencanadpn1['rproyeksi1'] + $xrencanadpn1['rproyeksi2'])+ ($xrencanadpn4['rproyeksi1'] + $xrencanadpn4['rproyeksi2']);
    ?>
			<tr>
				<td></td>
				<td>Sisa Uang Muka</b></td>
				<td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
                <td><?php echo format_nindya(0); ?></td>
			</tr>	
			<tr>
				<td></td>
				<td>Termijn</b></td>
				<td><?php echo format_mede2($ter1); ?></td>
                <td><?php echo format_nindya($ter2); ?></td>
                <td><?php echo format_mede2($ter3); ?></td>
                <td><?php echo format_mede2($ter4,0); ?></td>
                <td><?php echo format_mede2($ter5,0); ?></td>
                <td><?php echo format_mede2($ter6,0); ?></td>
                <td><?php echo format_mede2($ter7,0); ?></td>
                <td><?php echo format_mede2($ter8,0); ?></td>
                <td><?php echo format_mede2($ter9,0); ?></td>
                <td><?php echo format_nindya($ter10,0); ?></td>
			</tr>	
			<tr>
				<td></td>
				<td>Retensi</b></td>
				<td><?php echo format_nindya($ret1,0); ?></td>
                <td><?php echo format_nindya($ret2,0); ?></td>
                <td><?php echo format_nindya($ret3); ?></td>
                <td><?php echo format_nindya($ret4); ?></td>
                <td><?php echo format_nindya($ret5); ?></td>
                <td><?php echo format_nindya($ret6); ?></td>
                <td><?php echo format_nindya($ret7); ?></td>
                <td><?php echo format_nindya($ret8); ?></td>
                <td><?php echo format_nindya($ret9); ?></td>
                <td><?php echo format_nindya($ret10); ?></td>
			</tr>	
			<tr>
				<td></td>
				<td>PPH Final</b></td>
				<td><?php echo format_nindya($pph1); ?></td>
                <td><?php echo format_nindya($pph2); ?></td>
                <td><?php echo format_nindya($pph3); ?></td>
                <td><?php echo format_nindya($pph4); ?></td>
                <td><?php echo format_nindya($pph5); ?></td>
                <td><?php echo format_nindya($pph6); ?></td>
                <td><?php echo format_nindya($pph7); ?></td>
                <td><?php echo format_nindya($pph8); ?></td>
                <td><?php echo format_nindya($pph9); ?></td>
                <td><?php echo format_nindya($pph10); ?></td>
			</tr>

    <?php
    $ci1=$um1+$pum1+$ter1+$pph1;
    $ci2=$um2+$pum2+$ter2+$pph2;
    $ci3=$um3+$pum3+$ter3+$pph3;
    $ci4=$um4+$pum4+$ter4+$pph4;
    $ci5=$um5+$pum5+$ter5+$pph5;//+$cfdev2+$cfdev5+(-$cfdev6);
    $ci6=$um6+$pum6+$ter6+$pph6;
    $ci7=$um7+$pum7+$ter7+$pph7;//$cfsisaanggaran2+(-$cfperkiraan6);//(-$cfsisaanggaran6);//$cfsisaanggaran1+$cfsisaanggaran4+
    $ci8=$um8+$pum8+$ter8+$pph8;
    $ci9=$um9+$pum9+$ter9+$pph9;
    $ci10=$um10+$pum10+$ter10+$pph10;
    ?>


			<tr>
				<td></td>
				<td>Total Cash In</b></td>
				<td><?php echo format_nindya($ci1); ?></td>
                <td><?php echo format_nindya($ci2); ?></td>
                <td><?php echo format_nindya($ci3); ?></td>
                <td><?php echo format_nindya($ci4); ?></td>
                <td><?php echo format_nindya($ci5); ?></td>
                <td><?php echo format_nindya($ci6); ?></td>
                <td><?php echo format_nindya($ci7); ?></td>
                <td><?php echo format_nindya($ci8); ?></td>
                <td><?php echo format_nindya($ci9); ?></td>
                <td><?php echo format_nindya($ci10); ?></td>
			</tr>

    <?php
    //SD MINGGU LALU
    
    //$proyek="select sum(jumlah_cash_td)as total from tbl_po2 where no_spk='$cnospk_pilih' and date_part('month',tanggal)=$bulan and date_part('year',tanggal)=$tahun and pilihan='1' group by no_spk,tanggal ";
    $proyek="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and date_part('month',tanggal)<='$bulan_lalu' and date_part('year',tanggal)<='$tahun_lalu' and pilihan='1' group by proyek_id";
    $rproyek=pg_query($proyek);
    $xproyek=pg_fetch_array($rproyek);
    $proyek2="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and date_part('month',tanggal)<='$bulan_lalu' and date_part('year',tanggal)<='$tahun_lalu' and pilihan='3' group by proyek_id";
    $rproyek2=pg_query($proyek2);
    $xproyek2=pg_fetch_array($rproyek2);
    $proyek3="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and date_part('month',tanggal)<='$bulan_lalu' and date_part('year',tanggal)<='$tahun_lalu' and pilihan='4' group by proyek_id";
    $rproyek3=pg_query($proyek3);
    $xproyek3=pg_fetch_array($rproyek3);
    
    //REALISASI
    $realisasiproyek="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and date_part('month',tanggal) = '$bulan' and date_part('year',tanggal)='$tahun' and pilihan='1' group by proyek_id";
    $rrealisasiproyek=pg_query($realisasiproyek);
    $xrealisasiproyek=pg_fetch_array($rrealisasiproyek);
    $realisasiproyek2="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and date_part('month',tanggal) = '$bulan' and date_part('year',tanggal)='$tahun' and pilihan='3' group by proyek_id";
    $rrealisasiproyek2=pg_query($realisasiproyek2);
    $xrealisasiproyek2=pg_fetch_array($rrealisasiproyek2);
    $realisasiproyek3="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and date_part('month',tanggal) = '$bulan' and date_part('year',tanggal)='$tahun' and pilihan='4' group by proyek_id";
    $rrealisasiproyek3=pg_query($realisasiproyek3);
    $xrealisasiproyek3=pg_fetch_array($rrealisasiproyek3);
    
    //SD MINGGU INI
    $mgllproyek="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and tanggal<='$tgl_ini' and pilihan='1' group by proyek_id";
    $rmgllproyek=pg_query($mgllproyek);
    $xmgllproyek=pg_fetch_array($rmgllproyek);
    $mgllproyek2="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and tanggal<='$tgl_ini'  and pilihan='3' group by proyek_id";
    $rmgllproyek2=pg_query($mgllproyek2);
    $xmgllproyek2=pg_fetch_array($rmgllproyek2);
    $mgllproyek3="select sum(jumlah)as total from simpro_tbl_cashtodate where proyek_id='$proyek_id' and tanggal<='$tgl_ini'  and pilihan='4' group by proyek_id";
    $rmgllproyek3=pg_query($mgllproyek3);
    $xmgllproyek3=pg_fetch_array($rmgllproyek3);
    
    
    $proyek=$tot_jumlah_ob - round($xcb0['total'] * 3/100);
    
    /*rencana bln dpan*/
    $rencanapo="select sum(rproyeksi2)as rencana1,sum(rproyeksi3) as rencana2 from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan' and date_part('year',tahap_tanggal_kendali)='$tahun' and ket_id in('7','8','9','10','11','12')";
    $xlrencanapo=pg_query($rencanapo);
    $rrencanapo=pg_fetch_array($xlrencanapo);
    
    $proy1=$rrencanapo['rencana1']+$rrencanapo['rencana2'];
    
    $rencana1bk="select sum(rproyeksi2)as rencana1,sum(rproyeksi3) as rencana2 from simpro_tbl_cashin where proyek_id='$proyek_id' and date_part('month',tahap_tanggal_kendali)='$bulan_lalu' and date_part('year',tahap_tanggal_kendali)='$tahun_lalu' and ket_id in('7','8','9','10','11','12')";
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
    
    
    $total_co1=$proyek;
    
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

			<tr class="list_data">
				<td>-</td>
				<td colspan="11">Cash Out</b></td>
			</tr>	
			<tr>
				<td></td>
				<td>Proyek</b></td>
				<td><?php echo format_mede2($pco1,2); ?></td>
                <td><?php echo format_nindya($pco2); ?></td>
                <td><?php echo format_nindya($pco3); ?></td>
                <td><?php echo format_nindya($pco4); ?></td>
                <td><?php echo format_nindya($pco5); ?></td>
                <td><?php echo format_nindya($pco6); ?></td>
                <td><?php echo format_mede2($pco7,2); ?></td>
                <td><?php echo format_nindya($pco8); ?></td>
                <td></td>
                <td><?php echo format_nindya($pco10); ?></td>
			</tr>
			<tr>
				<td></td>
				<td>Divisi</b></td>
				<td>-</td>
                <td><?php echo format_nindya($dco2); ?></td>
                <td></td>
                <td><?php echo format_nindya($dco4); ?></td>
                <td><?php echo format_nindya($dco5); ?></td>
                <td><?php echo format_nindya($dco6); ?></td>
                <td><?php echo format_nindya($dco7); ?></td>
                <td></td>
                <td></td>
                <td></td>
			</tr>	
			<tr>
				<td></td>
				<td>Pusat</b></td>
				<td>-</td>
				<td><?php echo format_nindya($puco2); ?></td>
                <td></td>
                <td><?php echo format_nindya($puco4); ?></td>
                <td><?php echo format_nindya($puco5); ?></td>
                <td><?php echo format_nindya($puco6); ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
			</tr>	
			<tr>
				<td></td>
				<td>Total Cash Out</b></td>
				<td><?php echo format_mede2($co1,2); ?></td>
                <td><?php echo format_nindya($co2); ?></td>
                <td></td>
                <td><?php echo format_nindya($co4); ?></td>
                <td><?php echo format_nindya($co5); ?></td>
                <td><?php echo format_nindya($co6); ?></td>
                <td><?php echo format_mede2($co7,2); ?></td>
                <td><?php echo format_nindya($co8); ?></td>
                <td><?php echo format_nindya($co9); ?></td>
                <td><?php echo format_nindya($co10); ?></td>
			</tr>

    <?php
    $pka1=$ci1-$co1;//($sum1 + $xcb0[total]+(-$retensi)+round($xcb0[total] * 3/100))- $total_co1;
    $pka2=$ci2-$co2;
    $pka3=$ci3-$co3;
    $pka4=$ci4-$co4;//$sum4+$xsql2[realisasi]+$xsql4[realisasi]+$xsql5[realisasi]+$xrealisasiproyek[total]+$xrealisasiproyek2[total]+$xrealisasiproyek3[total];
    $pka5=$ci5-$co5;//$sum5+$cfdev2+$cfdev5+(-$cfdev6)
    
    $pka6=$ci6-$co6;//$sum6+$xmgll1[realisasi]+$xmgll4[realisasi]+$xmgll5[realisasi]+$xmgllproyek[total]+$xmgllproyek2[total]+$xmgllproyek3[total];
    $pka7=$ci7-$co7;//($cfsisaanggaran2+(-$cfsisaanggaran6)-($tot_jumlah_ob-$xmgllproyek[total]))-(0-$xmgllproyek2[total]);//$sum7+$cfsisaanggaran2+$cfsisaanggaran5+$cfsisaanggaran6;
    $pka8=$ci8-$co8;//$cfperkiraan2-($xmgllproyek[total]+($tot_jumlah_ob-$xmgllproyek[total]));//$cfperkiraan1+($cfperkiraan2)-($xmgllproyek[total]+($tot_jumlah_ob-$xmgllproyek[total]));//$sum8+$cfperkiraan2+$cfperkiraan5+$cfperkiraan6;
    $pka9=$ci9-$co9;
    $pka10=$ci10-$co10;
    ?>
			<tr class="list_data">
				<td></td>
				<td>Posisi Kas Akhir</td>
				<td><?php echo format_nindya($pka1); ?></td>
                <td><?php echo format_nindya($pka2); ?></td>
                <td><?php echo format_nindya($pka3); ?></td>
                <td><?php echo format_nindya($pka4); ?></td>
                <td><?php echo format_nindya($pka5); ?></td>
                <td><?php echo format_nindya($pka6); ?></td>
                <td><?php echo format_nindya($pka7); ?></td>
                <td><?php echo format_nindya($pka8); ?></td>
                <td><?php echo format_nindya($pka9); ?></td>
                <td><?php echo format_nindya($pka10); ?></td>
			</tr>	
    <?php
    $sqlapp="select a.*, b.first_name,b.last_name from simpro_tbl_approve a left join simpro_tbl_user b on a.username=b.user_name where a.proyek_id='$proyek_id' and a.tgl_approve='$tanggal' and a.form_approve='ALL'";
    $resapp=pg_query($sqlapp);
    $i=0;
    $countapp=pg_num_rows($resapp);
    if($countapp>0){
        echo "<tr bgcolor=#ffffff height=88><td bgcolor=#ffffff colspan=13 > &nbsp;Yang melakukan Approval : <br />";
        while($rowapp=pg_fetch_array($resapp)){
        $j=$i+1;
            if($rowapp[status]=='close'){
                echo "&nbsp; $j. $rowapp[first_name] $rowapp[last_name]<br />";
            }
        $i++;
        }
        echo "</td></tr>";
    }

    // function beban_kontrak1($tanggal,$cnospk_pilih){
    
    //     $sql="select sum(komposisi_harga_satuan_kendali*komposisi_volume_total_kendali) as total from simpro_tbl_komposisi_budget where proyek_id='$proyek_id' and tahap_tanggal_kendali='$tanggal' group by tahap_tanggal_kendali,proyek_id";
    //     $rsql=pg_query($sql);
    //     $xsql=pg_fetch_array($rsql);
        
    //     return format_nindya($xsql[total],0);
    // }

    ?>																																																																																																										
	</table>
</body>
</html>

<?php
} else {
    echo "<h1>Kontrak Kini Belum Terisi</h1>";
}
?>