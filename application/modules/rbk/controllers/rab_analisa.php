<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Rab_Analisa extends MX_Controller {
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');
		$this->load->model('mdl_rab_analisa');		
		if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");
	}

	function get_total_rap() //pakai
	{
		if($this->input->post('id_proyek'))
		{
			$data = $this->mdl_rab_analisa->total_rapa($this->input->post('id_proyek'));
			if(isset($data['data']['subtotal'])) echo number_format($data['data']['subtotal'],0);
			//$this->_out($data);		
		}
	}

	function set_parent_tree_id() //pakai
	{
		if($this->input->post('parent_id') && $this->input->post('proyek_id'))
		{		
			$datasess = array(
				'proyek_id' => $this->input->post('proyek_id'),
				'rap_parent_tree_id' => $this->input->post('parent_id'),
				'rap_parent_kode_tree' => $this->input->post('parent_kode_tree')
			);
			$_SESSION['sess_proyek_id'] = $this->input->post('proyek_id');
			$_SESSION['sess_rap_parent_id'] = $this->input->post('parent_id');
			$_SESSION['sess_rap_parent_kode_tree'] = $this->input->post('parent_kode_tree');
			$this->session->set_userdata($datasess);
		} else
		{
			$datasess = array(
				'rap_parent_tree_id' => 0,
				'rap_parent_kode_tree' => ''
			);
			$_SESSION['sess_rap_parent_id'] = 0;
			$_SESSION['sess_rap_parent_kode_tree'] = '';
			$this->session->set_userdata($datasess);
		}
	}
	
	function get_parent_kode_tree() //pakai
	{
		$parent = @$_SESSION['sess_rap_parent_kode_tree'];
		return $this->session->userdata('rap_parent_kode_tree') <> '' ? $this->session->userdata('rap_parent_kode_tree') : $parent;
	}
	
		
	public function tambah_rap_tree_item($idproyek) //pakai
	{		
		$pid = $this->get_parent_tree_id();
		$pkd = $this->get_parent_kode_tree();
		/* 
		$this->input->post('kode_tree') 
		$kdt = $this->input->post('kode_tree');
		*/
		$parentid = isset($pid) ? $pid : $this->input->post('tree_parent_id');
		$new_kt = $this->gen_kode_tree($this->input->post('id_proyek'), $parentid, $pkd); //$kdt
		if($this->input->post('tree_item') && $this->input->post('tree_satuan')	&& $this->input->post('id_proyek'))
		{				
			$datainsert = array(
				'tree_parent_id' => $parentid,
				'tahap_nama_kendali' => $this->input->post('tree_item'),
				'tahap_kode_induk_kendali' => $pkd,
				'tahap_satuan_kendali' => $this->input->post('tree_satuan'),
				'proyek_id' =>  $this->input->post('id_proyek'),
				'tahap_volume_kendali' => $this->input->post('volume') <> '' ? $this->input->post('volume') : 0,
				'tahap_kode_kendali' =>  $new_kt,
				'tahap_harga_satuan_kendali' => $this->input->post('harga')
			);

			$resin = $this->db->insert('simpro_tbl_input_kontrak', $datainsert);
			if($resin)
			{
				echo json_encode(array('success'=>true, 'message'=>'Data RAP berhasil disimpan!'));
			} else json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));
		} else 
		{
			echo json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));		
		}
	}

	public function get_detailmaterial_kode()
	{
		$data = $this->mdl_rab_analisa->get_detailmaterial_kode($this->input->get('query'));
		$this->_out($data);	
	}
	
	public function get_satuan() //pakai
	{
		$rs = $this->db->query("SELECT satuan_id, satuan_nama FROM simpro_tbl_satuan")->result_array();
		foreach($rs as $k=>$v)
		{
			$satuan[] = array(
				'satuan_id'=>$v['satuan_id'],
				'satuan_kode'=>$v['satuan_nama']
			);
		}
		echo json_encode(array('success'=>true,'total'=>count($satuan),'data'=>$satuan));
	}
	
	function copy_rat_proyek_lain() //pakai
	{		
		$idproyek = $this->input->get('id_proyek');
		$query = sprintf("
			SELECT 
				simpro_tbl_input_kontrak.tahap_kode_kendali AS kode_tree,
				simpro_tbl_input_kontrak.tahap_nama_kendali AS tree_item,
				simpro_tbl_input_kontrak.tahap_volume_kendali AS volume,
				simpro_tbl_input_kontrak.tahap_satuan_kendali AS tree_satuan,
				simpro_tbl_input_kontrak.proyek_id
			FROM simpro_tbl_input_kontrak		
			WHERE simpro_tbl_input_kontrak.proyek_id = '%d'
			order by kode_tree
		", $idproyek);
		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
			$this->_out($data);
		} else $this->_out(array('total'=>0, 'data'=>'', '_dc'=>$_REQUEST['_dc']));
	}

	function get_data_proyek() //pakai
	{
		if ($this->input->get('divisi_id')) {
			$divisi = $this->input->get('divisi_id');

			// $query = "SELECT proyek_id AS id_proyek, proyek AS nama_proyek from simpro_tbl_proyek";
			$query = "select distinct(a.proyek_id) as value, b.proyek AS name from simpro_tbl_input_kontrak a join simpro_tbl_proyek b on a.proyek_id = b.proyek_id where b.divisi_kode = $divisi";
			
			$rs = $this->db->query($query);
			$tot = $rs->num_rows();
			if($tot > 0)
			{
				$data = array('total'=>$tot, 'data'=>$rs->result_object());
			} else {
				$data = array('total'=>"", 'data'=>"");
			}
			$this->_out($data);
		}		
	}

	function copy_tree() //pakai
	{			 
		if($this->input->post('kode_tree') && $this->input->post('id_proyek'))
		{
			if(isset($_SESSION['copy_tree']['data'])) unset($_SESSION['copy_tree']);
			$kode_tree = explode(",",$this->input->post('kode_tree'));
			$uraian = explode(",",$this->input->post('tree_item'));
			$volume = explode(",",$this->input->post('volume'));
			$satuan = explode(",",$this->input->post('satuan'));
			$idproyek = $this->input->post('id_proyek');
			if(is_array($kode_tree))
			{
				$i=0;
				foreach($kode_tree as $k=>$v)
				{
					$copy_tree[] = array(
										'id_proyek' => $idproyek,
										'tree_item' => $uraian[$i],
										'tree_satuan' => $satuan[$i],
										'volume' => $volume[$i]
									);
					$i++;
				}
				$_SESSION['copy_tree'] = array(
						'id_proyek' => $this->input->post('id_proyek'), 
						'data' => $copy_tree
						);
			}
		}
	}	

	function paste_tree()	 //pakai
	{		
		if($this->input->post('kode_tree') && $this->input->post('id_proyek'))
		{
			if(isset($_SESSION['copy_tree']['data']))
			{
				$kt = explode(",",$this->input->post('kode_tree'));
				$parentid = explode(",",$this->input->post('tree_item_id'));
				$idproyek = $this->input->post('id_proyek');
				$copy = $_SESSION['copy_tree']['data'];
				$i=0;
				$error=FALSE;
				foreach($copy as $cp)
				{
					$new_kt = $this->gen_kode_tree($idproyek, $parentid[0], $kt[0]);
					//echo $kt[0], ' ',$parentid[0], ' ', $new_kt;
					$data = array(
						'proyek_id' => $cp['id_proyek'],
						'tahap_nama_kendali' => $cp['tree_item'],
						'tahap_satuan_kendali' => $cp['tree_satuan'],
						'tahap_volume_kendali' => $cp['volume'],
						'tahap_kode_induk_kendali' => $new_kt,
						'tree_parent_id' => $parentid[0]
					);
					if(!$this->db->insert("simpro_tbl_input_kontrak", $data)) $error=TRUE;
					$i++;
				}
				//$this->_dump($data);
				if(!$error) echo "Data berhasil di-paste";
					else echo "Data GAGAL dipaste!";
			} else echo "Tidak ada data yang di-copy, silahkan pilih item kemudian klik tombol 'Copy'";
		}	
	}	

	function get_task_tree_item($idpro) //pakai
	{
		$param = $this->input->get('param');
		$arr = $this->tree($idpro, $depth=0,$param);
		echo json_encode(array('text'=>'.', 'children'=>$arr));
		// $data = array('text'=>'.', 'children'=>$arr);
		// var_dump($data);
	}
	
	function tree($idpro, $depth=0,$param) //pakai
	{
		$result=array();
		$temp=array();
		$temp = $this->mdl_rab_analisa->get_tree_item($idpro, $depth, $param)->result();
		if(count($temp))
		{			
			$i = 0;
			$n = 1;
			foreach($temp as $row){
				$kode_tree = $row->tahap_kode_kendali;

				if ($row->tree_parent_id) {
					$tree_kode_parent = $this->db->query("select tahap_kode_kendali from simpro_tbl_input_kontrak where input_kontrak_id = $row->tree_parent_id")->row()->tahap_kode_kendali;
				} else {
					$tree_kode_parent = "";
				}

				if ($row->tahap_kode_induk_kendali.'.'.$n <> $row->tahap_kode_kendali || $tree_kode_parent <> $row->tahap_kode_induk_kendali) {
					if ($tree_kode_parent == '' || $tree_kode_parent == null) {
						$kode_tree = $n;
					} else {
						$kode_tree = $tree_kode_parent.'.'.$n;
					}

					// $data_del = "delete from simpro_tbl_input_kontrak where kode_tree = '$row->kode_tree' and id_proyek = $idpro";
					// $this->db->query($data_del);

					$data_up = "update simpro_tbl_input_kontrak set tahap_kode_kendali = '$kode_tree',
					tahap_kode_induk_kendali = '$tree_kode_parent' 
					where input_kontrak_id = $row->input_kontrak_id";
					$this->db->query($data_up);
				}

				//$temp_harga = $this->mdl_rab_analisa->get_tree_item_harga($idpro, $row->rap_item_tree)->row_array();
				$data[] = array(
					'rap_item_tree' => $row->input_kontrak_id,
					'id_proyek' => $row->proyek_id,
					'kode_tree' => $kode_tree,
					'tree_item' => utf8_encode($row->tahap_nama_kendali),
					'tree_satuan' => $row->tahap_satuan_kendali,
					'volume' => $row->tahap_volume_kendali,
					'harga' => $row->hrg,
					'subtotal' => $row->sub,
					'tree_parent_id' => $row->tree_parent_id,
					'kdt' => $row->tahap_kode_induk_kendali.'.'.$n
					/* 
					'harga' => $temp_harga['harga'], 
					'subtotal' => $temp_harga['subtotal'],
					*/
				);
				
				if($depth == 0) $data[$i] = array_merge($data[$i], array('expanded' => true));
				
				## check if have a child
				$q = sprintf("SELECT * FROM simpro_tbl_input_kontrak WHERE proyek_id = '%d' AND tree_parent_id = '%d'", $row->proyek_id, $row->input_kontrak_id);
				$query = $this->db->query($q);
				$is_child = $query->num_rows();
				if($is_child)
				{			
						$result[] = array_merge(
							$data[$i],
							array(
								'iconCls' => 'task-folder',
								'ishaschild' => 1,
								'children'=>$this->tree($idpro, $row->input_kontrak_id, $param)
							)
						);	
				} else 
				{
					if ($row->ktr != 0){
						$result[] = array_merge(
							$data[$i],
							array(
								'iconCls' => 'task-folder',
								'leaf' => true
							)
						);
					}
				}
				$n++;
				$i++;
			}
		}
		return array_filter($result);
	}
		
	function gen_kode_tree($proyekid, $parentid, $kode_tree) //pakai
	{
		$sql_tree = sprintf("
					SELECT 
						tahap_kode_kendali 
					FROM simpro_tbl_input_kontrak 
					WHERE 
						proyek_id = '%d' 
						AND tree_parent_id = '%d'
					ORDER BY input_kontrak_id DESC", 
					$proyekid, 
					$parentid
					);
		$qry = $this->db->query($sql_tree);
		$kt = $qry->row_array();
		$tot_data = $qry->num_rows();
		if($tot_data)
		{
			$old_kode = $kode_tree;
			$kt_inc = $tot_data + 1; 
		} else 
		{
			if(isset($kt['tahap_kode_kendali']))
			{
				$old_kode = $kt['tahap_kode_kendali'];
				$kt_inc = $old_kode + 1;
			} else 
			{
				$old_kode = $kode_tree;
				$kt_inc = 1;
			}
		}
		if($parentid) $new_kt = $old_kode.'.'.$kt_inc;
			else $new_kt = $kt_inc;
		return $new_kt;
	}
	
	function del_tree_item() //pakai
	{
		if($this->input->post('tree_item_id'))
		{	
			$qry = $this->db->query(sprintf("DELETE FROM simpro_tbl_input_kontrak WHERE input_kontrak_id = '%d'", $this->input->post('tree_item_id')));
			if($qry) echo json_encode(array("success"=>true, "message"=>"Data berhasil dihapus!"));
		}
	}
	
	function update_tree_item() //pakai
	{		
		if($this->input->post('kode_tree') && $this->input->post('tree_item') && $this->input->post('satuan_id') 
		&& $this->input->post('id_proyek') && $this->input->post('rap_item_tree'))
		{
			$du = array(
				'input_kontrak_id' => $this->input->post('rap_item_tree'),
				'tree_parent_id' => $this->input->post('tree_parent_id'),
				'proyek_id' => $this->input->post('id_proyek'),
				'tahap_kode_kendali' => $this->input->post('kode_tree'),
				'tahap_satuan_kendali' => $this->input->post('satuan_id'),
				'tahap_nama_kendali' => $this->input->post('tree_item'),
				'tahap_volume_kendali' => $this->input->post('volume'),
				'tahap_harga_satuan_kendali' => $this->input->post('harga')
			);
			$this->db->where('input_kontrak_id', $this->input->post('rap_item_tree'));
			$this->db->where('proyek_id', $this->input->post('id_proyek'));
			$this->db->where('tree_parent_id', $this->input->post('tree_parent_id'));
			if($this->db->update('simpro_tbl_input_kontrak', $du)) echo "Data berhasil diupdate";
				else echo "Data GAGAL diupdate!";
		}
	}
		
	function get_parent_tree_id() //pakai
	{
		//$parentid = isset($_SESSION['sess_parent_id']) ? $_SESSION['sess_parent_id'] : 0;
		$parentid = @$_SESSION['sess_rap_parent_id'];		
		return $this->session->userdata('rap_parent_tree_id') <> '' ? $this->session->userdata('rap_parent_tree_id') : $parentid;
	}

	/*
	function set_rap_item_tree()
	{
		if($this->input->post('id_proyek') && $this->input->post('rap_item_tree') && $this->input->post('kode_tree'))
		{
			$this->session->set_userdata('sess_id_proyek', $this->input->post('id_proyek'));
			$this->session->set_userdata('sess_rap_item_tree', $this->input->post('rap_item_tree'));
			$this->session->set_userdata('sess_kode_tree', $this->input->post('kode_tree'));
			$_SESSION['sess_id_proyek'] = $this->input->post('id_proyek');
			$_SESSION['sess_rap_item_tree'] = $this->input->post('rap_item_tree');
			$_SESSION['sess_kode_tree'] = $this->input->post('kode_tree');
		}
	}	
	
	function get_rap_item_tree()
	{
		if(isset($_SESSION['sess_id_proyek']) && isset($_SESSION['sess_rap_item_tree']))
		{
			$data = array(
				'id_proyek' => $_SESSION['sess_id_proyek'],
				'rap_item_tree' => $_SESSION['sess_rap_item_tree'],
				'kode_tree' => $_SESSION['sess_kode_tree']
			);
			return $data;
		}
	}	
	*/

	function delete_tree_item() //pakai
	{
		if($this->input->post('tree_item_id') && $this->input->post('id_proyek'))
		{
			$itemdel = explode(",", $this->input->post('tree_item_id'));
			$id_proyek = $this->input->post('id_proyek');
			$success = FALSE;
			if(count($itemdel) > 0)
			{
				foreach($itemdel as $k=>$v)
				{
					if($this->delete_tree_recursive($id_proyek, $v)) $success=TRUE;
						else $success=FALSE;
				}
				echo "Data berhasil dihapus!";
			}
		}		
	}
	
	private function delete_tree_recursive($id_proyek, $id_tree) //pakai
	{				
		$q = sprintf("SELECT * FROM simpro_tbl_input_kontrak WHERE proyek_id = '%d' AND tree_parent_id = '%d'", $id_proyek, $id_tree);
		$query = $this->db->query($q);
		$is_data = $query->result_array();
		if(count($is_data) > 0)
		{				
			foreach($is_data as $k=>$v)
			{
				$this->delete_tree_recursive($id_proyek, $v['input_kontrak_id']);
			}
		} else
		{
			//rap_item_tree
			if($this->db->delete("simpro_tbl_input_kontrak",array("input_kontrak_id"=>$id_tree))) return true;
				else return false;
		}
	}	

	private function _out($data)
	{
		$callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '';
		if ($data['total'] > 0)
		{
			$output = json_encode($data, 1);
			if ($callback) {
				header('Content-Type: text/javascript');
				echo $callback . '(' . $output . ');';
			} else {
				header('Content-Type: application/x-json');
				echo $output;
			}		   
		} else 
		{
			if ($callback) {
				header('Content-Type: text/javascript');
				echo $callback . '(' . json_encode(array('data'=>array(), 'total'=>0)) . ');';
			} else {
				header('Content-Type: application/x-json');
				echo json_encode(array('data'=>array(), 'total'=>0));
			}		   
		}
	}

	private function _dump($d)
	{
		print('<pre>');
		print_r($d);
		print('</pre>');
	}
	
	function copy_rab()
	{
		$proyek_id = $this->session->userdata('proyek_id');
		if ($this->input->post('divisi_kode') && $this->input->post('id_proyek')) {

			$idpro = $this->input->post('id_proyek');

			$this->db->delete('simpro_tbl_input_kontrak',array('proyek_id' => $proyek_id));

			$q_copy = "
				insert into simpro_tbl_input_kontrak (
					proyek_id,
					tahap_kode_kendali,
					tahap_nama_kendali,
					tahap_satuan_kendali,
					tahap_kode_induk_kendali,
					tahap_volume_kendali,
					tahap_harga_satuan_kendali
				)
				select 
				$proyek_id,
				tahap_kode_kendali,
				tahap_nama_kendali,
				tahap_satuan_kendali,
				tahap_kode_induk_kendali,
				tahap_volume_kendali,
				tahap_harga_satuan_kendali
				from simpro_tbl_input_kontrak where proyek_id = $idpro
			";

			$this->db->query($q_copy);
			$this->update_tree_parent_id_rab($proyek_id);
		}
	}

	function update_tree_parent_id_rab($idtender)
	{
		$sql = "
			update simpro_tbl_input_kontrak
			set tree_parent_id = x.id_parent
			from(
			SELECT 
			case when (select input_kontrak_id from simpro_tbl_input_kontrak where tahap_kode_kendali = a.tahap_kode_induk_kendali and proyek_id = a.proyek_id) isnull
			then 0
			else (select input_kontrak_id from simpro_tbl_input_kontrak where tahap_kode_kendali = a.tahap_kode_induk_kendali and proyek_id = a.proyek_id)
			end as id_parent,
			a.input_kontrak_id, a.tahap_kode_kendali 
			FROM simpro_tbl_input_kontrak a
			WHERE a.proyek_id = $idtender) x
			where proyek_id = $idtender and simpro_tbl_input_kontrak.input_kontrak_id = x.input_kontrak_id	
			";
		$this->db->query($sql);
	}

	function excel($page="")
	{
		$idpro = $this->session->userdata('proyek_id'); 

		if ($page=="") {
			echo "Access Forbidden..";
		} else {

			$nama_proyek = $this->db->query("select proyek from simpro_tbl_proyek where proyek_id = $idpro")
			->row()->proyek;

			$date = date('Y-m-d');

			$nama_proyek_u = substr(str_replace(' ', '_', $nama_proyek), 0, 12);
			// var_dump($nama_proyek_u);

			//load our new PHPExcel library
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle(substr($page, 0,7).'_'.$nama_proyek_u.'_'.$date);

			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', strtoupper($page));
			$this->excel->getActiveSheet()->setCellValue('A2', strtoupper($nama_proyek).' ('.$date.')');
			$this->excel->getActiveSheet()->setCellValue('A4', 'Kode');
			$this->excel->getActiveSheet()->setCellValue('B4', 'Uraian');
			$this->excel->getActiveSheet()->setCellValue('C4', 'Satuan');
			$this->excel->getActiveSheet()->setCellValue('D4', 'Volume');
			$this->excel->getActiveSheet()->setCellValue('E4', 'Harga');
			$this->excel->getActiveSheet()->setCellValue('F4', 'Jumlah');

			switch ($page) {
				case 'rab':					

					$this->db->query("TRUNCATE TABLE simpro_tmp_print_pekerjaan");

					$q_rapa = $this->get_data_print($idpro);

					$x = 5;

					$tot = 0;
					
					if ($q_rapa) {
						foreach ($q_rapa as $row) {
							$kode_tree = $row->kode_tree;
							$kode_analisa = $row->kode_analisa;
							$tree_item = $row->tree_item;
							$tree_satuan = $row->tree_satuan;
							$volume = $row->volume;
							$hrg = $row->hrg;
							$sub = $row->sub;

							$this->excel->getActiveSheet()->setCellValueExplicit('A'.$x, $kode_tree, PHPExcel_Cell_DataType::TYPE_STRING);
							$this->excel->getActiveSheet()->setCellValue('B'.$x, $tree_item);
							$this->excel->getActiveSheet()->setCellValue('C'.$x, $tree_satuan);
							$this->excel->getActiveSheet()->setCellValue('D'.$x, $volume);
							if (count(explode(".", $kode_tree)) == 1) {
								$this->excel->getActiveSheet()->getStyle('E'.$x)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('F'.$x)->getFont()->setBold(true);

								$tot = $tot + $sub;
							}
							$this->excel->getActiveSheet()->setCellValue('E'.$x, $hrg);
							$this->excel->getActiveSheet()->setCellValue('F'.$x, $sub);

							$x++;
						}
					} 
				break;
				default:
					echo "Gagal";
				break;
			}


			$this->excel->getActiveSheet()->setCellValue('A'.$x, 'Total');
			$this->excel->getActiveSheet()->setCellValue('F'.$x, $tot);

			$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->getFont()->setBold(true);

			$styleArray = array(
			'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			),
			  'font'  => array(
		        'size'  => 7.5,
		        'name'  => 'verdana'
		    )
			);

			$styleArray1 = array(
			  'font'  => array(
		        'bold'  => true,
		        'uppercase' => true,
		        'size'  => 11,
		        'name'  => 'Calibri'
		    )
			);

			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(34);

			$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

			$this->excel->getActiveSheet()->getStyle('E1:F'.$this->excel->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00'); 

			$this->excel->getActiveSheet()->getStyle('A4:F'.$x)->applyFromArray($styleArray);
			unset($styleArray);

			$this->excel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($styleArray1);
			unset($styleArray1);

			$this->excel->getActiveSheet()->getStyle('C1:C'.$this->excel->getActiveSheet()->getHighestRow())
    		->getAlignment()->setWrapText(true);

			$this->excel->getActiveSheet()->mergeCells('A1:F1');
			$this->excel->getActiveSheet()->mergeCells('A2:F2');
			$this->excel->getActiveSheet()->mergeCells('A'.$x.':E'.$x);

			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$this->db->query("TRUNCATE TABLE simpro_tmp_print_pekerjaan");

			$filename=$page.'_'.$nama_proyek.'_'.$date.'.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			             
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');

			
		}
	}

	function get_data_print($idpro)
	{
		$arr = $this->tree($idpro, $depth=0, $param="");

		$this->get_array($arr);

		$q_data = $this->db->query('select * from simpro_tmp_print_pekerjaan order by id_print');

		if ($q_data->result()) {
			return $q_data->result();
		}
	} 

	function get_array($value='')
	{
		if (is_array($value)) {
			foreach ($value as $key => $row) {
				
				$data = array(
					'kode_tree' => $row['kode_tree'],
					'tree_item' => trim($row['tree_item']),
					'tree_satuan' => $row['tree_satuan'],
					'volume' => $row['volume'],
					'hrg' => $row['harga'] <> '' ? $row['harga'] : 0,
					'sub' => $row['subtotal'] <> '' ? $row['subtotal'] : 0,
				);

				$this->db->insert('simpro_tmp_print_pekerjaan',$data);
				// fwrite($handle, $dat);			

				if (isset($row['children'])) {
					$this->get_array($row['children']);
				}
			}
		}
	}
}

?>