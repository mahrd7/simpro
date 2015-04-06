<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

Class Pey_chart
{

	var $project_id;
	var $tanggal_awal;
	var $tanggal_akhir;
	var $setting;
	var $chart_data;
	var $sec_bulan;
	var $jumlah_bobot;
	var $total_bobot_pengerjaan;
	var $page;

	function __construct(){
		$this->ci =& get_instance();
	}

	function set_chart($project_id, $tanggal_awal, $tanggal_akhir, $jumlah_bobot, $setting){

		$this->project_id = $project_id;

		$this->tanggal_awal = $tanggal_awal;

		$this->tanggal_akhir = $tanggal_akhir;

		$this->setting = $setting;

		$tahun_awal = date("Y",strtotime($this->tanggal_awal));
		$tahun_akhir = date("Y",strtotime($this->tanggal_akhir));

		$bulan_awal = date("n",strtotime($this->tanggal_awal));
		$bulan_akhir = date("n",strtotime($this->tanggal_akhir));

		$sec_bulan = ($bulan_akhir - $bulan_awal) + 1;

		$sec_tahun = $tahun_akhir - $tahun_awal;

		$sec_bulan = $sec_bulan + ($sec_tahun * 12);

		$this->sec_bulan = $sec_bulan;

		$this->jumlah_bobot = $jumlah_bobot;

	}

	function chart_data($chart_data){		

		$project_id = $this->project_id;

		$jumlah_bobot = $this->jumlah_bobot;	

		$total_bobot_pengerjaan = 0;	

		foreach ($chart_data as $key => $val) {

			$total_bobot_pengerjaan = $total_bobot_pengerjaan + $val['value'];

		}
		
		$this->chart_data = $chart_data;

		$this->total_bobot_pengerjaan = $total_bobot_pengerjaan;
		
	}

	function get_chart($info){

		if ($info == 'proyek') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent';
		} elseif ($info == 'alat') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_alat';
		} elseif ($info == 'bahan') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_bahan';
		} elseif ($info == 'person') {
			$tbl_info = 'simpro_tbl_sch_proyek_parent_person';	
		} elseif ($info == 'peralatan') {
			$tbl_info = 'simpro_tbl_guna_alat_parent';	
		}

		if ($info == 'peralatan') {
			$field_info = 'id_guna_alat';
			$field_info_b = 'id_analisa_asat';
			$field_info_c = 'jumlah_parent';
		} else {
		    $field_info = 'id_sch_proyek';
		    $field_info_b = 'tahap_kendali_id';
		    $field_info_c = 'bobot_parent';
		}

		$project_id = $this->project_id;
		$setting = $this->setting;

		$data = $this->chart_data;

		if(count($data) == 0)
			return NULL;

		$jumlah_bobot = $this->jumlah_bobot;

		$total_bobot_pengerjaan = $this->total_bobot_pengerjaan;

		if ($total_bobot_pengerjaan == 0 || $jumlah_bobot == 0) {
			$bobot_persen = 0;
		} else {
			$bobot_persen = ($total_bobot_pengerjaan / $jumlah_bobot) * 100;
		}

		(int)$bobot_persen;

		$thn_awl = date("Y",strtotime($this->tanggal_awal));
		$thn_akr = date("Y",strtotime($this->tanggal_akhir));

		$bulan_awal = date("n",strtotime($this->tanggal_awal));
		$bulan_akhir = date("n",strtotime($this->tanggal_akhir));

		if ($thn_akr > $thn_awl) {
			$sel_thn = $thn_akr - $thn_awl;
			$bulan_akhir = $bulan_akhir + (12 * $sel_thn);
		}

		// var_dump(($bulan_akhir - $bulan_awal) * 4);

		$width = (400 * $this->sec_bulan) + 1;

		//var_dump($this->sec_bulan);

		$output = '<div class="box-label">';

		$output .= '<table class="table_cart_label" style="position:fixed;">';
		$output .= '<tr>
		<td class="title_label" style="width: 50px;background:#fff;">NO</td>
		<td class="title_label" style="width: 340px;background:#fff;">URAIAN</td>
		<td class="title_label" style="width: 80px;background:#fff;">UNIT</td>
		<td class="title_label" style="width: 80px;background:#fff;">BOBOT %</td></tr>';
		$output .= '</table>';

		$bobot_unit = 0;

		$output .= '<table class="table_cart" style="margin-top: 50px;">';
		foreach ($data as $key => $val) {

			$output .= '<tr>';
				
			$output .= '<td style="width: 50px;background:#fff;">'.$val['tahap_kode_kendali'].'</td>';
			$output .= '<td style="width: 340px;background:#fff;">'.$val['label'].'</td>';
			$output .= '<td style="width: 80px;background:#fff;text-align:center;">'.$val['unit'].'</td>';
			$output .= '<td style="width: 80px;background:#fff;text-align:center;">'.$val['bobot_unit'].' % </td>';			

			$output .= '</tr>';

			$bobot_unit = $bobot_unit + $val['bobot_unit'];

		}

		$output .= '<tr>
		<td style="width: 401px;background:#fff;" colspan="2">TOTAL BOBOT</td>
		<td style="width: 80px;background:#fff;text-align:center;"></td>
		<td style="width: 80px;background:#fff;text-align:center;">'.$bobot_unit.' %</td>
		</tr>';
		$output .= '<tr><td style="width: 583px;background:#fff;" colspan="4">BOBOT MINGGUAN</td></tr>';
		$output .= '<tr><td class="last" style="width: 583px;background:#fff;" colspan="4">BOBOT KUMULATIF</td></tr>';

		$output .= '</table>';
		$output .= '</div>';

		$chart_height = count($this->chart_data);

		$chart_height = (($chart_height + 3) * 50) + 60;

		$output .= '<div id="container" style="height:'.$chart_height.'px">';

		$output .= '<div class="box" style="width:'.$width.'px">';


		$output .= '<table id="month-week-title" style="width:'.$width.'px;table-layout:fixed;">';
		$output .= '<tr>';

		$set_bulan_awal = $bulan_awal;
		$set_bulan_akhir = $bulan_akhir;

		// var_dump($set_bulan_awal."+".$bulan_akhir);

		for ($i=$set_bulan_awal; $i <= $set_bulan_akhir; $i++) {
			$kpk12 = $i / 12;
			$thns = $thn_awl + (ceil($kpk12)-1);

			$output .= '<td class="month-title-field" colspan="4"><center>'.date("F", mktime(0, 0, 0, $i, 10)).' '.$thns.'</center></td>';
		}

		$output .= '</tr>';
		$output .= '<tr>';
		$nos = 1;
		for ($w=$bulan_awal; $w <= $bulan_akhir; $w++) {
		$no = 1;
			for ($a=1; $a <=4 ; $a++) {
				//var_dump($no % 4);

				if ($no % 4 == 0)
					$output .= '<td class="week-title" style="border-left: 1px solid #7E7E7E;border-right: 1px solid #7E7E7E;">w'.$no.' to '.$nos.'</td>';
				elseif ($no % 4 == 1)
					$output .= '<td class="week-title" style="border-left: 0;">w'.$no.' to '.$nos.'</td>';
				else
					$output .= '<td class="week-title">w'.$no.' to '.$nos.'</td>';

				$no++;
				$nos++;
			}
		}
		$output .= '</tr>';
		$output .= '</table>';

		if(is_array($this->chart_data)){

			$margin_top = 50;
			

			foreach ($data as $key => $val) {

				//$width_bobot = $width * ($val / 100);
				
				$margin_left = ($val['margin_star'] * 100) - 95;

				$width_bobot = ($val['width'] * 100) - 15;

				if ($setting == 1 || $setting == 2){

					$new_margin_top = $margin_top + 3;

					if ($setting == 2)
						$new_margin_top = $margin_top + 16;

					$metter_val = number_format((float)$val['total_percent'], 2, '.', '');

					$output .= '<div class="dmetter" style="width:'.$width_bobot.'px;margin-top:'.$new_margin_top.'px;margin-left:'.$margin_left.'px">
					<div class="metter-value">'.$metter_val.'%</div>
					<div class="progress progress-striped">
					<div class="bar" style="width:'.$metter_val.'%"></div>
					</div>
					</div>';
				}

				$margin_left = ($val['margin_star'] * 100) - 100;

				if ($setting == 2 || $setting == 3){

					$new_margin_top = $margin_top + 3;

					if ($setting == 2)
						$new_margin_top = $margin_top - 10;

					$output .= '<div class="dmetter" style="margin-top:'.$new_margin_top.'px;margin-left:'.$margin_left.'px">';
					
					$tahap_kendali_id = $val['tahap_kendali_id'];

					$project_id = $this->project_id ;

					$sql = $this->ci->db->query("select * from $tbl_info where $field_info = '$project_id' and $field_info_b='$tahap_kendali_id' order by tgl_sch_parent");     	
					$data_tahap_kendali = $sql->result_array();

					foreach ($data_tahap_kendali as $key_tkendali => $val_tkendali) {

						if (!empty($val_tkendali['bobot_parent']))
							$output .= '<div class="metter-percent-value">'.$val_tkendali['bobot_parent'].'</div>';
						else
							$output .= '<div class="metter-percent-value"></div>';
					}

					$output .= '</div>';

				}


				$margin_top = $margin_top + 50;
				//$margin_left = $margin_left + $width_bobot;
				//$margin_left = $margin_left + 405;
			}			
			
		}

		

		// bobot mingguan dan kumulatif
		$no_minggu = 1;

		$margin_top = $margin_top + 50;

		$sql = $this->ci->db->query("select * from $tbl_info where $field_info = '$project_id' order by minggu_ke DESC LIMIT 1");     	
		$data_minggu_akhir = $sql->row_array();

		$minggu_akhir = $data_minggu_akhir['minggu_ke'];

		$output .= '<div class="dmetter" style="margin-top:'.$margin_top.'px;margin-left:0px">';
			
		for ($w=$bulan_awal; $w <= $bulan_akhir; $w++) {				

		 	for ($a=1; $a <=4 ; $a++) {

		 		if ($no_minggu <= $minggu_akhir){
			 		$sql = $this->ci->db->query("select sum($field_info_c) as bobot_mingguan from $tbl_info where $field_info = '$project_id' and minggu_ke='$no_minggu'");     	
					$bobot_mingguan = $sql->row_array();

					if (!empty($bobot_mingguan['bobot_mingguan']))
						$output .= '<div class="metter-percent-value">'.$bobot_mingguan['bobot_mingguan'].' %</div>';
					else
						$output .= '<div class="metter-percent-value">0 %</div>';
					
			 		$no_minggu++;
		 		} else {
		 			$output .= '<div class="metter-percent-value">0 %</div>';
		 		}
		 	}	 	

		 }

		$output .= '</div>';

		$no_minggu = 1;

		$margin_top = $margin_top + 50;

		$output .= '<div class="dmetter" style="margin-top:'.$margin_top.'px;margin-left:0px">';
			
		for ($w=$bulan_awal; $w <= $bulan_akhir; $w++) {				

		 	for ($a=1; $a <=4 ; $a++) {

		 		if ($no_minggu <= $minggu_akhir){
			 		if (!isset($minggu_kumulatif))
						$minggu_kumulatif = $no_minggu;
					else
						$minggu_kumulatif .= ','.$no_minggu;

					if (substr($minggu_kumulatif, 0, -1) == ',')
						$minggu_kumulatif = substr($minggu_kumulatif, 0, -1);

					$sql = $this->ci->db->query("select sum($field_info_c) as bobot_kumulatif from $tbl_info where $field_info = '$project_id' and minggu_ke IN($minggu_kumulatif)");     	
					$bobot_kumulatif = $sql->row_array();

					if (!empty($bobot_kumulatif['bobot_kumulatif']))
						$output .= '<div class="metter-percent-value">'.$bobot_kumulatif['bobot_kumulatif'].' %</div>';
					else
						$output .= '<div class="metter-percent-value">0 %</div>';
					
			 		$no_minggu++;
			 	} else {
			 		$output .= '<div class="metter-percent-value">'.$bobot_kumulatif['bobot_kumulatif'].' %</div>';
			 	}
		 	}	 	

		 }

		$output .= '</div>';


		$list_data = count($this->chart_data);

		//$chart_height = ceil($list_data / 8 );

		$chart_height = ($list_data + 3 ) * 50;


		$set_bulan_awal = $bulan_awal;
		$set_bulan_akhir = $bulan_akhir;

		for ($i=$set_bulan_awal; $i <= $set_bulan_akhir; $i++) { 

			$output .= '<div class="wp-month" style="height:'.$chart_height.'px">';
			//$output .= '<div class="month-title">'.date("F", mktime(0, 0, 0, $i, 10)).'</div>';

			//$output .= '<div class="wp-week" style="position:absolute; top:20px;">';
			//for ($a=1; $a <=4 ; $a++) { 
			//	$output .= '<div class="week" style="float:left; width:98px; display:block; text-align:center;">'.$a.'</div>';
			//}
			//$output .= '</div>';

			$output .= '<div class="month" style="height:'.$chart_height.'px">';
			$output .= '';
			$output .= '</div>';
			$output .= '</div>';
		}

		$output .= '</div>';

		$output .= '</div>';

		return $output;
	}
}