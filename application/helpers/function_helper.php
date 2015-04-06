<?php

function dump($data)
{
	print("<pre>");
	print_r($data);
	print("</pre>");
}

function add_post_var($key_post, $value)
{
    $_POST[$key_post] = $value;
}

function zero_tgl($tgl)
{
    if(strlen($tgl)==1) return "0".$tgl;
    else return $tgl;
}

function get_nama_hari($tgl)
{
	$arr = explode("-",$tgl);
	$date 	= $arr[2];
	$month 	= $arr[1];
	$year	= $arr[0];
	$day = date('N', mktime(0,0,0, $month, $date, $year));
	switch($day)
	{
		case 1 : $hari = 'Senin'; break;
		case 2 : $hari = 'Selasa'; break;
		case 3 : $hari = 'Rabu'; break;
		case 4 : $hari = 'Kamis'; break;
		case 5 : $hari = 'Jumat'; break;
		case 6 : $hari = 'Sabtu'; break;
		case 7 : $hari = 'Minggu'; break;
	}
	return $hari;
}

function indonesian_date($date)
{
    if(empty($date)) return "";
    $tgl = substr($date,8,2);
    $thn = substr($date,0,4);
    $bulan = get_nama_bulan($date);
    return $tgl." ".$bulan." ".$thn;
}

function indonesian_date_time($date)
{
    if(empty($date)) return "";
    $tgl = substr($date,8,2);
    $thn = substr($date,0,4);
    $_date = substr($date,0,10);
    $bulan = get_nama_bulan($_date);
    $time = substr($date,11);
    return $tgl." ".$bulan." ".$thn.', '.$time;
}


function short_date($date)
{        
    $thn = substr($date,0,4);
    $bln = substr($date,5,2);
    $tgl = substr($date,8,2);
    return $tgl.'/'.$bln.'/'.$thn;
}

function get_bulan()
{
	for($i=1;$i <= 12; $i++)
	{		
		switch($i)
		{
			case "1" : $bulan[$i] = "Januari"; break;
			case "2" : $bulan[$i] = "Februari"; break;
			case "3" : $bulan[$i] = "Maret"; break;
			case "4" : $bulan[$i] = "April"; break;
			case "5" : $bulan[$i] = "Mei"; break;
			case "6" : $bulan[$i] = "Juni"; break;
			case "7" : $bulan[$i] = "Juli"; break;
			case "8" : $bulan[$i] = "Agustus"; break;
			case "9" : $bulan[$i] = "September"; break;
			case "10" : $bulan[$i] = "Oktober"; break;
			case "11" : $bulan[$i] = "November"; break;
			case "12" : $bulan[$i] = "Desember"; break;
		}
	}
	return $bulan;
}


function get_nama_bulan($tgl)
{
	$arrtgl = explode("-",$tgl);
	switch($arrtgl[1])
	{
		case "01" : $bulan = "Januari"; break;
		case "02" : $bulan = "Februari"; break;
		case "03" : $bulan = "Maret"; break;
		case "04" : $bulan = "April"; break;
		case "05" : $bulan = "Mei"; break;
		case "06" : $bulan = "Juni"; break;
		case "07" : $bulan = "Juli"; break;
		case "08" : $bulan = "Agustus"; break;
		case "09" : $bulan = "September"; break;
		case "10" : $bulan = "Oktober"; break;
		case "11" : $bulan = "November"; break;
		default : $bulan = "Desember"; break;
	}
	return $bulan;
}

function nama_bulan($bul)
{
	switch($bul)
	{
		case "1" : $bulan = "Januari"; break;
		case "2" : $bulan = "Februari"; break;
		case "3" : $bulan = "Maret"; break;
		case "4" : $bulan = "April"; break;
		case "5" : $bulan = "Mei"; break;
		case "6" : $bulan = "Juni"; break;
		case "7" : $bulan = "Juli"; break;
		case "8" : $bulan = "Agustus"; break;
		case "9" : $bulan = "September"; break;
		case "10" : $bulan = "Oktober"; break;
		case "11" : $bulan = "November"; break;
		default : $bulan = "Desember"; break;
	}
	return $bulan;
}

function number($number)
{
    return number_format($number, 0);
}

function date_time($datetime)
{
    $arr_tanggal = explode(" ",$datetime);
    $tgl = $arr_tanggal[0];
    $time = $arr_tanggal[1];
    $arr_tgl = explode("-",$tgl);
    return $arr_tgl[2].'/'.$arr_tgl[1].'/'.$arr_tgl[0].' '.$time;
}


function print_if_not_empty($var, $default = "-")
{
    if(isset($var) && !empty($var)) echo $var;    
    else echo $default;
}

function text_kelamin($kelamin)
{
    if($kelamin == 'L') return 'PRIA';
    else return 'WANITA';
}

function changeDateFormat1($dateValue)
{
    if(empty($dateValue)) return NULL;
    $arr = explode("/", $dateValue);
    $newFormat = $arr[2].'-'.$arr[1].'-'.$arr[0];
    return $newFormat;
}

function changeDateFormat2($dateValue)
{
    if(empty($dateValue)) return NULL;
    $arr = explode("-", $dateValue);
    $newFormat = $arr[2].'/'.$arr[1].'/'.$arr[0];
    return $newFormat;
}

?>