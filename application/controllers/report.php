<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in')) redirect("login");		
		$this->load->model(array("main_mdl", "setting_mdl", "report_mdl"));
		$this->load->library(array('Pagination'));				
	}

	public function index()
	{
		$this->load->view('header');
		$this->load->view('uc');
		$this->load->view('footer');
	}
	
	public function uplift()
	{
		# initialize 
		$s['periode'] = 0;
		$s['tgl_awal'] = '';
		$s['tgl_akhir'] = '';
		$s['station'] = '';
		$s['flt_number'] = '';		
		$s['bulan'] = date('m');
		$s['tahun'] = date('Y');			

		$uri = $this->uri->uri_to_assoc(3);			
		if(isset($uri['offset']))
		{
			$this->session->set_userdata("offset_rpt_uplift", $uri['offset']);		
		}		
		$offset = isset($uri['offset']) ? $this->session->userdata("offset_rpt_uplift") : 0;
		$limit = 25;
		
		if($this->input->post("id_station"))
		{
			$this->session->set_userdata("stat_origin", $this->input->post("id_station"));
			$this->session->set_userdata("master_artikel_station", $this->input->post("id_station"));
			$this->session->set_userdata("statid", $this->report_mdl->get_stat_id($this->session->userdata("master_artikel_station")));
		}
		
		if($this->input->post("clear"))
		{
			$this->session->unset_userdata("stat_origin");
			$this->session->unset_userdata("master_artikel_station");
			$this->session->unset_userdata("s_admin_statid");			
		}
				
		$id_station = ($this->session->userdata("master_artikel_station")) ? $this->session->userdata("master_artikel_station") : FALSE;
		#$idstat = $this->session->userdata("statid") <> "" ? $this->session->userdata("statid") : 0;

		# jika yg login sbg admin
		if($this->session->userdata('gid') == 1)
		{			
			$idstat = $this->session->userdata("s_admin_statid") ? $this->session->userdata("s_admin_statid") : 0; 
		} else $idstat = $this->session->userdata("statid") <> "" ? $this->session->userdata("statid") : 0;
		
		if($this->input->post('clear_filter'))
		{
			$this->session->unset_userdata('s_rpt_uplift');
			$this->session->unset_userdata('search_uplift');			
		}  
		
		if($this->input->post('submit_filter'))
		{
			$this->session->set_userdata('s_rpt_uplift', $this->input->post());
		}  
		
		if(is_array($this->session->userdata('s_rpt_uplift')))
		{
			$s_data = $this->session->userdata('s_rpt_uplift');
			if($s_data['periode'] == 1)
			{
				$s['periode'] = 1;
				$s['tgl_awal'] = $s_data['tgl_awal'];
				$s['tgl_akhir'] = $s_data['tgl_akhir'];
				$s['station'] = $s_data['station'];
				$s['flt_number'] = $s_data['flt_number'];
				$s['stat_origin'] = $this->session->userdata("stat_origin") ? $this->session->userdata("stat_origin") : '';
				
			} else if($s_data['periode'] == 2)
			{
				$s['periode'] = 2;
				$s['bulan'] = $s_data['bulan'];
				$s['tahun'] = $s_data['tahun'];
				$s['station'] = $s_data['station'];
				$s['stat_origin'] = $this->session->userdata("stat_origin") ? $this->session->userdata("stat_origin") : '';
				$s['flt_number'] = $s_data['flt_number'];
			}
			$this->session->set_userdata('search_uplift', $s);			
		} 
		
		$idstat = isset($idstat) ? $idstat : false;
		$data = 
		$this->session->userdata('search_uplift') 
		? 
		$this->report_mdl->data_uplift($idstat, $offset, $limit, $this->session->userdata('search_uplift')) 
		: $this->report_mdl->data_uplift($idstat, $offset, $limit);
		
		# pagination
		$config['base_url'] = base_url() . '/index.php/report/uplift/offset';
		$config['total_rows'] = $data['num_rows'];
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		$this->pagination->initialize($config);
								
		$station = $this->report_mdl->get_station();
		$artikel = $this->setting_mdl->_get_artikel();

		$content = array(
			'station' => $this->setting_mdl->combo($station),
			'id_station' => $id_station,
			'data' => $data['data'],
			'bulan' => $this->report_mdl->bulan(),
			'paginator' => $this->pagination->create_links(),
			'page' => $offset,
			'pil_periode' => $s['periode'],
			'tgl_awal' => $s['tgl_awal'],
			'tgl_akhir' => $s['tgl_akhir'],
			'pil_bulan' => $s['bulan'],
			'pil_tahun' => $s['tahun'],
			'pil_station' => $s['station'],
			'flt_number' => $s['flt_number']
		);	
		$this->load->view('header');
		$this->load->view('report_uplift', $content);
		$this->load->view('footer');
	}
	
	public function offloading()
	{
		# initialize 
		$s['periode'] = 0;
		$s['tgl_awal'] = '';
		$s['tgl_akhir'] = '';
		$s['station'] = '';
		$s['flt_number'] = '';		
		$s['bulan'] = date('m');
		$s['tahun'] = date('Y');			

		$uri = $this->uri->uri_to_assoc(3);			
		if(isset($uri['offset']))
		{
			$this->session->set_userdata("offset_rpt_uplift", $uri['offset']);		
		}		
		$offset = isset($uri['offset']) ? $this->session->userdata("offset_rpt_uplift") : 0;
		$limit = 25;
		
		if($this->input->post("id_station"))
		{
			$this->session->set_userdata("stat_dest", $this->input->post("id_station"));
			$this->session->set_userdata("master_artikel_station", $this->input->post("id_station"));
			$this->session->set_userdata("s_admin_statid", $this->report_mdl->get_stat_id($this->input->post("id_station")));
		}
		
		if($this->input->post("clear"))
		{
			$this->session->unset_userdata("stat_dest");
			$this->session->unset_userdata("master_artikel_station");
			$this->session->unset_userdata("s_admin_statid");
		}
		
		$id_station = ($this->session->userdata("master_artikel_station")) ? $this->session->userdata("master_artikel_station") : FALSE;
		
		# jika yg login sbg admin
		if($this->session->userdata('gid') == 1)
		{			
			$idstat = $this->session->userdata("s_admin_statid") ? $this->session->userdata("s_admin_statid") : 0; 
		} else $idstat = $this->session->userdata("statid") <> "" ? $this->session->userdata("statid") : 0;
		
		if($this->input->post('clear_filter'))
		{
			$this->session->unset_userdata('s_rpt_offloading');
		}  
		
		if($this->input->post('submit_filter'))
		{
			$this->session->set_userdata('s_rpt_offloading', $this->input->post());
		}  
		
		if(is_array($this->session->userdata('s_rpt_offloading')))
		{
			$s_data = $this->session->userdata('s_rpt_offloading');
			if($s_data['periode'] == 1)
			{
				$s['periode'] = 1;
				$s['tgl_awal'] = $s_data['tgl_awal'];
				$s['tgl_akhir'] = $s_data['tgl_akhir'];
				$s['station'] = $s_data['station'];
				$s['stat_dest'] = $this->session->userdata("stat_dest") ? $this->session->userdata("stat_dest") : "";				
				$s['flt_number'] = $s_data['flt_number'];
			} else if($s_data['periode'] == 2)
			{
				$s['periode'] = 2;
				$s['bulan'] = $s_data['bulan'];
				$s['tahun'] = $s_data['tahun'];
				$s['station'] = $s_data['station'];
				$s['stat_dest'] = $this->session->userdata("stat_dest") ? $this->session->userdata("stat_dest") : "";				
				$s['flt_number'] = $s_data['flt_number'];
			}
		} 
		$this->session->set_userdata('search_offloading', $s);
		$idstat = isset($idstat) ? $idstat : false;
		$data = $this->session->userdata('search_offloading')
				? 
				$this->report_mdl->data_offloading($idstat, $offset, $limit, $this->session->userdata('search_offloading')) 
				: 
				$this->report_mdl->data_offloading($idstat, $offset, $limit);
		
		# pagination
		$config['base_url'] = base_url() . '/index.php/report/offloading/offset';
		$config['total_rows'] = $data['num_rows'];
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		$this->pagination->initialize($config);
								
		$station = $this->report_mdl->get_station();
		$artikel = $this->setting_mdl->_get_artikel();

		$content = array(
			'station' => $this->setting_mdl->combo($station),
			'id_station' => $id_station,
			'data' => $data['data'],
			'bulan' => $this->report_mdl->bulan(),
			'paginator' => $this->pagination->create_links(),
			'page' => $offset,
			'pil_periode' => $s['periode'],
			'tgl_awal' => $s['tgl_awal'],
			'tgl_akhir' => $s['tgl_akhir'],
			'pil_bulan' => $s['bulan'],
			'pil_tahun' => $s['tahun'],
			'pil_station' => $s['station'],
			'flt_number' => $s['flt_number']
		);	
		$this->load->view('header');
		$this->load->view('report_offloading', $content);
		$this->load->view('footer');
	}

	public function uplift_detail()
	{
		$uri_def = array('trx_date', 'wtt');
		$uri = $this->uri->uri_to_assoc(3, $uri_def);
		$data = $this->report_mdl->detail_uplift($uri['wtt'], $uri['trx_date']);
		$content = array(
			'data' => $data
		);
		$this->load->view('header');
		$this->load->view('rpt_detail_uplift', $content);
		$this->load->view('footer');
	}
	
	public function offloading_detail()
	{
		$uri_def = array('trx_date', 'wtt');
		$uri = $this->uri->uri_to_assoc(3, $uri_def);
		$data = $this->report_mdl->detail_offloading($uri['wtt'], $uri['trx_date']);
		$content = array(
			'data' => $data
		);	
		$this->load->view('header');
		$this->load->view('rpt_detail_offloading', $content);
		$this->load->view('footer');
	}

	
	public function floating()
	{	
		/*
		if($this->session->userdata('gid') <> '1') 
			show_error(sprintf("Permission Denied. You can not acces this page. <a href='%s'>Click here to go back</a>", base_url() .'/index.php/main/index'), 505);
		*/
		
		# initialize 
		$s['periode'] = 0;
		$s['tgl_awal'] = '';
		$s['tgl_akhir'] = '';
		$s['station'] = '';
		$s['flt_number'] = '';		
		$s['bulan'] = date('m');
		$s['tahun'] = date('Y');			

		$uri = $this->uri->uri_to_assoc(3);			
		if(isset($uri['offset']))
		{
			$this->session->set_userdata("offset_rpt_floating", $uri['offset']);		
		}		
		$offset = isset($uri['offset']) ? $this->session->userdata("offset_rpt_floating") : 0;
		$limit = 25;
		
		if($this->input->post("id_station"))
		{
			$this->session->set_userdata("master_artikel_station", $this->input->post("id_station"));
			$this->session->set_userdata("statid", $this->report_mdl->get_stat_id($this->session->userdata("master_artikel_station")));
			$this->session->set_userdata("rpt_id_stat", $this->session->userdata("master_artikel_station"));
			$this->session->set_userdata("s_admin_statid", $this->report_mdl->get_stat_id($this->input->post("id_station")));			
		}

		if($this->input->post("clear"))
		{
			$this->session->unset_userdata("master_artikel_station");
			$this->session->unset_userdata("rpt_id_stat");
			$this->session->unset_userdata("s_admin_statid");			
		}
		
		if($this->input->post('clear_filter'))
		{
			$this->session->unset_userdata('s_rpt_floating');
		}  
		
		if($this->input->post('submit_filter'))
		{
			$this->session->set_userdata('s_rpt_floating', $this->input->post());
		}  
		
		$id_station = ($this->session->userdata("rpt_id_stat")) ? $this->session->userdata("rpt_id_stat") : FALSE;
		
		# jika yg login sbg admin
		if($this->session->userdata('gid') == 1)
		{			
			$idstat = $this->session->userdata("s_admin_statid") ? $this->session->userdata("s_admin_statid") : 0; 
		} else $idstat = $this->session->userdata("statid") <> "" ? $this->session->userdata("statid") : 0;
		
		if(is_array($this->session->userdata('s_rpt_floating')))
		{
			$s_data = $this->session->userdata('s_rpt_floating');
			if($s_data['periode'] == 1)
			{
				$s['periode'] = 1;
				$s['tgl_awal'] = $s_data['tgl_awal'];
				$s['tgl_akhir'] = $s_data['tgl_akhir'];
				$s['station'] = $s_data['station'];
				$s['flt_number'] = $s_data['flt_number'];
			} else if($s_data['periode'] == 2)
			{
				$s['periode'] = 2;
				$s['bulan'] = $s_data['bulan'];
				$s['tahun'] = $s_data['tahun'];
				$s['station'] = $s_data['station'];
				$s['flt_number'] = $s_data['flt_number'];
			}
		} 
		$idstat = isset($idstat) ? $idstat : false;

		$data = is_array($this->session->userdata('s_rpt_floating')) 
				?
				$this->report_mdl->report_floating_all($offset, $limit, $idstat, $this->session->userdata('s_rpt_floating'))
				: 
				$this->report_mdl->report_floating_all($offset, $limit, $idstat);
		
		# pagination
		$config['base_url'] = base_url() . '/index.php/report/floating/offset';
		$config['total_rows'] = $data['num_rows'];
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		$this->pagination->initialize($config);
								
		$station = $this->report_mdl->get_station();
		$artikel = $this->setting_mdl->_get_artikel();
			
		$content = array(
			'station' => $this->setting_mdl->combo($station),
			'id_station' => $id_station,
			'data' => $data['data'],
			'bulan' => $this->report_mdl->bulan(),
			'paginator' => $this->pagination->create_links(),
			'page' => $offset,
			'pil_periode' => $s['periode'],
			'tgl_awal' => $s['tgl_awal'],
			'tgl_akhir' => $s['tgl_akhir'],
			'pil_bulan' => $s['bulan'],
			'pil_tahun' => $s['tahun'],
			'pil_station' => $s['station'],
			'flt_number' => $s['flt_number']
		);	
			
		$this->load->view('header');
		$this->load->view('rpt_floating', $content);
		$this->load->view('footer');
	}
	
	function offload_to_excel()
	{
		$title = "REKAP OFFLOAD ". $this->session->userdata('kd_station');		
		$idstat = $this->session->userdata("statid") ? $this->session->userdata("statid") : 0;		
		$data = $this->session->userdata('search_offloading') 
				? 
				$this->report_mdl->data_offload_station($idstat, 0, 10000, $this->session->userdata('search_offloading'))
				: 
				$this->report_mdl->data_offload_station($idstat, 0, 10000);
		$sheet_title = "Rekap Offload";							
		$col = array(
			'stat_origin' => 'Origin',
			'stat_kd_dest' => 'Destination',
			'trak_date' => 'Date',
			'type_ac' => 'Type A/C',
			'flight_number' => 'Flight Number',
			'std' => 'ETD',
			'sta' => 'ETA',
			'kd_artikel' => 'Kode Artikel',
			'nama_artikel' => 'Artikel',
			'std_ul' => 'STD Uplift',
			'qty_off' => 'QTY Offload'
		);
		$this->report_mdl->to_excel($title, $sheet_title, $col, $data['data']);		
	}

	function floating_to_excel()
	{
		$title = "REKAP FLOATING ". $this->session->userdata('kd_station');		
		if($this->session->userdata('gid') == 1)
		{			
			$idstat = $this->session->userdata("s_admin_statid") ? $this->session->userdata("s_admin_statid") : 0; 
		} else $idstat = $this->session->userdata("statid") <> "" ? $this->session->userdata("statid") : 0;
		
		if(is_array($this->session->userdata('s_rpt_floating')))
		{
			$s_data = $this->session->userdata('s_rpt_floating');
			if($s_data['periode'] == 1)
			{
				$search['periode'] = 1;
				$search['tgl_awal'] = $s_data['tgl_awal'];
				$search['tgl_akhir'] = $s_data['tgl_akhir'];
				$search['station'] = $s_data['station'];
				$search['flt_number'] = $s_data['flt_number'];
			} else if($s_data['periode'] == 2)
			{
				$search['periode'] = 2;
				$search['bulan'] = $s_data['bulan'];
				$search['tahun'] = $s_data['tahun'];
				$search['station'] = $s_data['station'];
				$search['flt_number'] = $s_data['flt_number'];
			}
		} else $search = false; 		
		$data = $this->report_mdl->report_floating_all(0, 10000, $idstat, $search);
		$sheet_title = "Rekap Floating";							
		$col = array(
			'kd_stat_origin' => 'Origin',
			'kd_stat_dest' => 'Destination',
			'tgl_trx' => 'Date',
			'type_ac' => 'Type A/C',
			'flight_number' => 'Flight Number',
			'std' => 'ETD',
			'sta' => 'ETA',
			'kd_artikel' => 'Kode Artikel',
			'nama_artikel' => 'Artikel',
			'std_ul' => 'STD UL',
			'qty_ul' => 'QTY Uplift',
			'qty_off' => 'QTY Offload',
			'selisih_ul' => '(Qty Uplift - Std Uplift)',
			'selisih' => '(QTY Offload - Qty Uplift)'		
		);
		$this->report_mdl->to_excel($title, $sheet_title, $col, $data['data']);		
	}
	
	function uplift_to_excel()
	{
		$title = "REKAP UPLIFT ".$this->session->userdata('kd_station');
		$idstat = $this->session->userdata("statid") ? $this->session->userdata("statid") : 0;		
		$data = $this->session->userdata('search_uplift') 
				? 
				$this->report_mdl->data_uplift_station($idstat, 0, 10000, $this->session->userdata('search_uplift'))
				: 
				$this->report_mdl->data_uplift_station($idstat, 0, 10000);
		
		$sheet_title = "Rekap Uplift";							
		$col = array(
			'stat_origin' => 'Origin',
			'stat_kd_dest' => 'Destination',
			'trak_date' => 'Date',
			'type_ac' => 'Type A/C',
			'flight_number' => 'Flight Number',
			'std' => 'ETD',
			'sta' => 'ETA',
			'kd_artikel' => 'Kode Artikel',
			'nama_artikel' => 'Artikel',
			'std_ul' => 'STD Uplift',
			'qty_ul' => 'QTY Uplift'
		);
		$this->report_mdl->to_excel($title, $sheet_title, $col, $data['data']);		
	}

	function chart_station()
	{		
		$station = $this->report_mdl->get_station();
		$artikel = $this->report_mdl->get_artikel();
	
		$content = array(
			'station' => $this->setting_mdl->combo($station),
			'artikel' => $this->setting_mdl->combo($artikel),
			'bulan' => $this->report_mdl->bulan()
		);	
		$this->load->view('header');
		$this->load->view('report_chart_station', $content);
		$this->load->view('footer');	
	}

	function get_chart_per_station()
	{
		if($this->input->post("id_station"))
		{
			$statid = $this->report_mdl->get_stat_id($this->input->post("id_station"));
		}
										
		if($this->input->post('submit_filter') &&
			$this->input->post('periode') &&
			$this->input->post('station') ||
			(($this->input->post('tgl_awal') && $this->input->post('tgl_akhir')) || ($this->input->post('bulan') && $this->input->post('tahun'))) &&
			$this->input->post('artikel')
		)
		{
			$s_data = $this->input->post();
			$idstat = $this->report_mdl->get_stat_id($this->input->post('station'));
			//$artid = $this->report_mdl->get_artikel_id($this->input->post('artikel'));
			if($s_data['periode'] == 1)
			{
				$s['periode'] = 1;
				$s['tgl_awal'] = $s_data['tgl_awal'];
				$s['tgl_akhir'] = $s_data['tgl_akhir'];
				$s['station'] = $idstat;
				$s['artikel'] = $s_data['artikel'];
			} else if($s_data['periode'] == 2)
			{
				$s['periode'] = 2;
				$s['bulan'] = $s_data['bulan'];
				$s['tahun'] = $s_data['tahun'];
				$s['station'] = $idstat;
				$s['artikel'] = $s_data['artikel'];
			}
			$this->session->set_userdata("chart_per_station_param", $s);
			$data = $this->report_mdl->report_chart_station($s);
			if(isset($data))
			{
				$this->session->set_userdata("chart_per_station", $data);
			} else $this->session->set_userdata("chart_per_station", false);
			$this->load->view("get_report_station", array("data" => $data));			
		} else
		{
			echo '<div id="error" align="center">Silahkan pilih periode bulan / tanggal!</div>';
		}
	}

	function show_chart_per_station($callback)
	{
		$cb = explode("=",$callback);
		$datapm = $this->session->userdata("chart_per_station");
		if($datapm)
		{
			$ntotime = "";
			foreach($datapm as $dm)
			{
				$totime = strtotime($dm['trx_date']);
				$ntotime = $totime . '000';
				$totul_c[] = sprintf("[%d, %s]", $ntotime, $dm['total']);
			}
		
			$code = sprintf("%s([", $cb[1]);		
			$totdata = count($totul_c) - 1;
			$i=0;		
			foreach($totul_c as $k=>$v)
			{
				if($i == $totdata) $code .= $v;
					else $code .= $v . ",";
				$i++;
			}
			$code .= "]);"; 
			echo $code;			
		} else 
		{
			$code = sprintf("%s([", $cb[1]);		
			$code .= "[null,null,null]";
			$code .= "]);"; 
			echo $code;
		}	
	}
	
	function chart_station_to_excel()
	{
		$data = $this->session->userdata("chart_per_station");
		$stat = $data[0]['kd_station'] .' - '. $data[0]['stat_name'];
		$art = $data[0]['kd_artikel'] .' - '. $data[0]['nama_artikel'];
		$param = $this->session->userdata("chart_per_station_param");
		if(isset($param['bulan'])) $periode = $param['bulan'] .' '. $param['tahun']; 
			else $periode = $param['tgl_awal'] .' s/d '. $param['tgl_akhir']; 
		$title = "Stock Excess/Short Per Item/Station ". $stat ." ". $art ." ".$periode;		
		$sheet_title = "Stock Floating per Station";							
		$cols = array(
			'kd_station' => 'Station',
			'stat_name' => '',
			'trx_date' => 'Trx Date',
			'kd_artikel' => 'Kode Artikel',
			'nama_artikel' => 'Artikel',
			'total' => 'QTY Excess'
		);
		$this->report_mdl->to_excel($title, $sheet_title, $cols, $data);		
	}
	
	function chart_item()
	{	
		$artikel = $this->report_mdl->get_artikel();
	
		$content = array(
			'artikel' => $this->setting_mdl->combo($artikel),
			'bulan' => $this->report_mdl->bulan()
		);	
		$this->load->view('header');
		$this->load->view('report_chart_artikel', $content);
		$this->load->view('footer');		
	}
		
	function get_chart_per_artikel()
	{									
		$bul = $this->report_mdl->bulan();	
		$station = $this->report_mdl->get_station();
		if($this->input->post('submit_filter') &&
			$this->input->post('periode') ||
			(($this->input->post('tgl_awal') && $this->input->post('tgl_akhir')) || ($this->input->post('bulan') && $this->input->post('tahun'))) &&
			$this->input->post('artikel')
		)
		{
			$s_data = $this->input->post();
			if($s_data['periode'] == 1)
			{
				$s['periode'] = 1;
				$s['tgl_awal'] = $s_data['tgl_awal'];
				$s['tgl_akhir'] = $s_data['tgl_akhir'];
				$s['artikel'] = $s_data['artikel'];
				$data = $this->report_mdl->report_chart_artikel($s);
				$datachart = array(
					'station' => json_encode($this->gen_val($station, 'kd_station'), JSON_NUMERIC_CHECK)
				);
				$this->load->view("get_report_artikel_daily", $datachart);			
			} else if($s_data['periode'] == 2)
			{
				$s['periode'] = 2;
				$s['bulan'] = $s_data['bulan'];
				$s['tahun'] = $s_data['tahun'];
				$s['artikel'] = $s_data['artikel'];
				$data = $this->report_mdl->report_chart_artikel($s);
				$datachart = array (
					'station' => json_encode($this->gen_val($data, 'kd_station'), JSON_NUMERIC_CHECK),
					'datas' => json_encode($this->gen_val($data, 'total'), JSON_NUMERIC_CHECK),
					'periode' => $bul[$s_data['bulan']] . " ". $s_data['tahun'],
					'data' => $data
				);				
				$this->load->view("get_report_artikel", $datachart);			
			}
			$this->session->set_userdata("chart_per_artikel_param", $s);
			if(isset($data))
			{
				$this->session->set_userdata("chart_per_artikel", $data);
			} else $this->session->set_userdata("chart_per_artikel", false);
		} else
		{
			echo '<div id="error" align="center">Silahkan pilih periode bulan / tanggal dan artikel!</div>';
		}	
	}
	
	function show_chart_per_artikel_daily($stat,$callback)
	{
		$cb = explode("=",$callback);
		$idstat = $this->report_mdl->get_stat_id($stat);		
		$params = $this->session->userdata("chart_per_artikel_param");
		$newparam = array_merge($params, array('station' => $idstat));
		$datapm = $this->report_mdl->report_chart_artikel($newparam);
		if($datapm)
		{
			$ntotime = "";
			foreach($datapm as $dm)
			{
				$totime = strtotime($dm['trx_date']);
				$ntotime = $totime . '000';
				$totul_c[] = sprintf("[%d, %s]", $ntotime, $dm['total']);
			}
		
			$code = sprintf("%s([", $cb[1]);		
			$totdata = count($totul_c) - 1;
			$i=0;		
			foreach($totul_c as $k=>$v)
			{
				if($i == $totdata) $code .= $v;
					else $code .= $v . ",";
				$i++;
			}
			$code .= "]);"; 
			echo $code;			
		} else 
		{
			$code = sprintf("%s([", $cb[1]);		
			$code .= "[null,null]";
			$code .= "]);"; 
			echo $code;
		}		
	}
	
	function show_chart_per_artikel($callback)
	{
		$cb = explode("=",$callback);
		$datapm = $this->session->userdata("chart_per_artikel");
		if($datapm)
		{
			$ntotime = "";
			foreach($datapm as $dm)
			{
				$art = strtotime($dm['kd_artikel']);
				$totul_c[] = sprintf("[%d, %s]", $art, $dm['total']);
			}
		
			$code = sprintf("%s([", $cb[1]);		
			$totdata = count($totul_c) - 1;
			$i=0;		
			foreach($totul_c as $k=>$v)
			{
				if($i == $totdata) $code .= $v;
					else $code .= $v . ",";
				$i++;
			}
			$code .= "]);"; 
			echo $code;			
		} else 
		{
			$code = sprintf("%s([", $cb[1]);		
			$code .= "[null,null,null,null]";
			$code .= "]);"; 
			echo $code;
		}		
	}
	
	function chart_artikel_to_excel()
	{
		$data = $this->session->userdata("chart_per_artikel");
		$art = $data[0]['kd_artikel'] .' - '. $data[0]['nama_artikel'];
		$param = $this->session->userdata("chart_per_artikel_param");
		if(isset($param['bulan'])) $periode = $param['bulan'] .' '. $param['tahun']; 
			else $periode = $param['tgl_awal'] .' s/d '. $param['tgl_akhir']; 
		$title = "Stock Excess/Short Per Item All Station ". $art ." ".$periode;		
		$sheet_title = "Stock Item All Station";							
		$cols = array(
			'kd_station' => 'Station',
			'stat_name' => '',
			'kd_artikel' => 'Kode Artikel',
			'nama_artikel' => 'Artikel',
			'total' => 'Exces'
		);
		$this->report_mdl->to_excel($title, $sheet_title, $cols, $data);		
	}

	function chart_artikel_daily_to_excel()
	{
		$data = $this->session->userdata("chart_per_station");
		$stat = $data[0]['kd_station'] .' - '. $data[0]['stat_name'];
		$art = $data[0]['kd_artikel'] .' - '. $data[0]['nama_artikel'];
		$param = $this->session->userdata("chart_per_station_param");
		if(isset($param['bulan'])) $periode = $param['bulan'] .' '. $param['tahun']; 
			else $periode = $param['tgl_awal'] .' s/d '. $param['tgl_akhir']; 
		$title = "Stock Excess/Short Per Item/Station ". $stat ." ". $art ." ".$periode;		
		$sheet_title = "Stock Floating per Station";							
		$cols = array(
			'kd_station' => 'Station',
			'stat_name' => '',
			'trx_date' => 'Trx Date',
			'kd_artikel' => 'Kode Artikel',
			'nama_artikel' => 'Artikel',
			'total' => 'QTY'
		);
		$this->report_mdl->to_excel($title, $sheet_title, $cols, $data);		
	}
	
	function chart_ga()
	{
		$artikel = $this->report_mdl->get_artikel();
		$ga = $this->report_mdl->code_ga();				
		$content = array(
			'artikel' => $this->setting_mdl->combo($artikel),
			'bulan' => $this->report_mdl->bulan(),
			'ga' => $this->setting_mdl->combo($ga)
		);	
		$this->load->view('header');
		$this->load->view('report_chart_perga', $content);
		$this->load->view('footer');			
	}
	
	function get_chart_per_ga()
	{
		$bul = $this->report_mdl->bulan();	
		if($this->input->post('submit_filter') &&
			$this->input->post('periode') ||
			(($this->input->post('tgl_awal') && $this->input->post('tgl_akhir')) || ($this->input->post('bulan') && $this->input->post('tahun'))) &&
			$this->input->post('ga_code')
		)
		{
			$s_data = $this->input->post();
			if($s_data['periode'] == 1)
			{
				$s['periode'] = 1;
				$s['tgl_awal'] = $s_data['tgl_awal'];
				$s['tgl_akhir'] = $s_data['tgl_akhir'];
				$s['ga'] = $s_data['ga_code'];
			} else if($s_data['periode'] == 2)
			{
				$s['periode'] = 2;
				$s['bulan'] = $s_data['bulan'];
				$s['tahun'] = $s_data['tahun'];
				$s['ga'] = $s_data['ga_code'];
			}
			
			$this->session->set_userdata("chart_per_ga_param", $s);
			$data = $this->report_mdl->report_chart_per_ga($s);
			if(isset($data))
			{
				$this->session->set_userdata("chart_per_ga", $data);
			} else $this->session->set_userdata("chart_per_ga", false);			
			$datachart = array(
				'ga' => $this->input->post("ga_code"),
				'data' => $data
			);
			$this->load->view("get_report_perga", $datachart);			
			
		} else
		{
			echo '<div id="error" align="center">Silahkan pilih periode bulan / tanggal dan kode GA!</div>';
		}		
	}
	
	function show_chart_per_ga($ga,$callback)
	{
		$cb = explode("=",$callback);
		$datapm = $this->session->userdata("chart_per_ga");
		if($datapm)
		{
			$ntotime = "";
			foreach($datapm as $dm)
			{
				$totime = strtotime($dm['trx_date']);
				$ntotime = $totime . '000';
				$totul_c[] = sprintf("[%d, %s]", $ntotime, $dm['total']);
			}
		
			$code = sprintf("%s([", $cb[1]);		
			$totdata = count($totul_c) - 1;
			$i=0;		
			foreach($totul_c as $k=>$v)
			{
				if($i == $totdata) $code .= $v;
					else $code .= $v . ",";
				$i++;
			}
			$code .= "]);"; 
			echo $code;			
		} else 
		{
			$code = sprintf("%s([", $cb[1]);		
			$code .= "[null,null]";
			$code .= "]);"; 
			echo $code;
		}		
	}
	
	function chart_ga_to_excel()
	{
		$data = $this->session->userdata("chart_per_ga");
		$param = $this->session->userdata("chart_per_ga_param");
		if(isset($param['bulan'])) $periode = $param['bulan'] .' '. $param['tahun']; 
			else $periode = $param['tgl_awal'] .' s/d '. $param['tgl_akhir']; 
		$title = "Stock Excess/Short Per GA ". $param['ga'] .' Periode '.$periode;		
		$sheet_title = "Stock Per GA";							
		$cols = array(
			'flight_number' => 'Flight Number',
			'trx_date' => 'Trx Date',
			'kd_stat_orig' => 'Origin',
			'kd_stat_dest' => 'Destination',
			'total' => 'Exces'
		);
		$this->report_mdl->to_excel($title, $sheet_title, $cols, $data);		
	}
	
	function chart_ga_daily()
	{	
		$artikel = $this->report_mdl->get_artikel();
		$station = $this->report_mdl->get_station();
		$content = array(
			'bulan' => $this->report_mdl->bulan(),
			'station' => $this->setting_mdl->combo($station)
		);	
		$this->load->view('header');
		$this->load->view('report_chart_per_stat_ga', $content);
		$this->load->view('footer');			
	}

	function get_chart_ga_perstat()
	{	
		$bul = $this->report_mdl->bulan();	
		if($this->input->post('submit_filter') &&
			$this->input->post('periode') ||
			(($this->input->post('tgl_awal') && $this->input->post('tgl_akhir')) || ($this->input->post('bulan') && $this->input->post('tahun'))) &&
			$this->input->post('station')
		)
		{
			$s_data = $this->input->post();
			if($s_data['periode'] == 1)
			{
				$s['periode'] = 1;
				$s['tgl_awal'] = $s_data['tgl_awal'];
				$s['tgl_akhir'] = $s_data['tgl_akhir'];
				#$s['station'] = $s_data['station'];
			} else if($s_data['periode'] == 2)
			{
				$s['periode'] = 2;
				$s['bulan'] = $s_data['bulan'];
				$s['tahun'] = $s_data['tahun'];
				#$s['station'] = $s_data['station'];
			}
			
			$this->session->set_userdata("chart_per_stat_ga_param", $s);
			
			/*
			$data = $this->report_mdl->report_chart_per_ga($s);
			if(isset($data))
			{
				$this->session->set_userdata("chart_per_stat_ga", $data);
			} else $this->session->set_userdata("chart_per_stat_ga", false);			
			*/
			
			$gacode = $this->report_mdl->code_ga_perstat($this->input->post('station'));							
			$datachart = array(
				'ga' => json_encode($this->gen_val($gacode, 'code_ga'))
			);
			$this->load->view("get_report_stat_ga", $datachart);						
		} else
		{
			echo '<div id="error" align="center">Silahkan pilih periode bulan / tanggal dan kode GA!</div>';
		}		
	}

	function show_chart_ga_daily($ga,$callback)
	{	
		$cb = explode("=",$callback);
		$sesparam = $this->session->userdata("chart_per_stat_ga_param");
		$param = array_merge(array("ga"=>$ga), $sesparam);
		$datapm = $this->report_mdl->report_chart_per_ga($param);
		if(count($datapm))
		{
			$ntotime = "";
			foreach($datapm as $dm)
			{
				$totime = strtotime($dm['trx_date']);
				$ntotime = $totime . '000';
				$totul_c[] = sprintf("[%d, %s]", $ntotime, $dm['total']);
			}
		
			$code = sprintf("%s([", $cb[1]);		
			$totdata = count($totul_c) - 1;
			$i=0;		
			foreach($totul_c as $k=>$v)
			{
				if($i == $totdata) $code .= $v;
					else $code .= $v . ",";
				$i++;
			}
			$code .= "]);"; 
			echo $code;			
		} else 
		{
			$code = sprintf("%s([", $cb[1]);		
			$code .= "[null,null]";
			$code .= "]);"; 
			echo $code;
		}					
	}
		
	private function gen_val($data,$key)
	{
		$ret="";
		foreach($data as $k=>$v)
		{
			$ret[] = $v[$key];
		}
		if(is_array($ret)) return $ret;			
	}
	
	function dump($data)
	{
		print("<pre>");
		print_r($data);
		print("</pre>");	
	}
}