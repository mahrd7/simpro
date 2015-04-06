<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class ctg_Analisa extends MX_Controller {
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');
		$this->load->model('mdl_ctg_analisa');
		if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");
		$this->db->query("SET CLIENT_ENCODING TO 'UTF8'");
	}
	
	public function get_asat($idproyek,$tgl_rab)
	{
		//$idproyek = isset($idproyek) ? $idproyek : $this->get_idproyek();
		$data = $this->mdl_ctg_analisa->get_daftar_analisa_satuan($idproyek,$tgl_rab);		
		$this->_out($data);
	}
	
	function delete_asat()
	{
		if($this->input->post('id_analisa_asat'))
		{
			if(substr($this->input->post('kode_analisa'),0,2) == 'AN')
			{
				if($this->db->delete('simpro_costogo_analisa_apek',array('id_analisa_apek'=>$this->input->post('id_analisa_asat'))))
					echo "Data berhasil dihapus.";
						else echo "Data GAGAL dihapus!";				
			} else
			{
				if($this->db->delete('simpro_costogo_analisa_asat',array('id_analisa_asat'=>$this->input->post('id_analisa_asat'))))
					echo "Data berhasil dihapus.";
						else echo "Data GAGAL dihapus!";
			}
		}
	}
	
	function get_rata($idproyek,$tgl_rab)
	{
		$data = $this->mdl_ctg_analisa->rat_rata($idproyek,$tgl_rab);
		$this->_out($data);		
	}

	function edit_koefisien_satuan($tgl_rab)
	{	
		if($this->input->post('id_proyek') && $this->input->post('detail_material_kode') 
		&& $this->input->post('kode_analisa') && $this->input->post('koefisien') 
		&& $this->input->post('id_analisa_asat')
		)
		{
			if(substr($this->input->post('detail_material_kode'),0,2) <> 'AN')
			{
				$update = array(
					'id_analisa_asat' => $this->input->post('id_analisa_asat'),
					'id_proyek' => $this->input->post('id_proyek'),
					'kode_analisa' => $this->input->post('kode_analisa'),
					'kode_material' => $this->input->post('detail_material_kode'),
					'koefisien' => $this->input->post('koefisien')
				);
				$this->db->where('tanggal_kendali', $tgl_rab);
				$this->db->where('id_analisa_asat', $this->input->post('id_analisa_asat'));
				if($this->db->update('simpro_costogo_analisa_asat', $update))
					echo "Data berhasil diupdate.";
						else echo "Data GAGAL diupdate!";
			} else
			{
				$update = array(
					'id_analisa_apek' => $this->input->post('id_analisa_asat'),
					'id_proyek' => $this->input->post('id_proyek'),
					'kode_analisa' => $this->input->post('kode_analisa'),
					'koefisien' => $this->input->post('koefisien')
				);
				$this->db->where('tanggal_kendali', $tgl_rab);
				$this->db->where('id_analisa_apek', $this->input->post('id_analisa_asat'));
				if($this->db->update('simpro_costogo_analisa_apek', $update))
					echo "Data berhasil diupdate.";
						else echo "Data GAGAL diupdate!";
			}
		} 
	}

	function set_parent_tree_id()
	{
		if($this->input->post('parent_id') && $this->input->post('proyek_id'))
		{		
			$datasess = array(
				'proyek_id' => $this->input->post('proyek_id'),
				'costogo_parent_tree_id' => $this->input->post('parent_id'),
				'costogo_parent_kode_tree' => $this->input->post('parent_kode_tree')
			);
			$_SESSION['sess_proyek_id'] = $this->input->post('proyek_id');
			$_SESSION['sess_costogo_parent_id'] = $this->input->post('parent_id');
			$_SESSION['sess_costogo_parent_kode_tree'] = $this->input->post('parent_kode_tree');
			$this->session->set_userdata($datasess);
		} else
		{
			$datasess = array(
				'costogo_parent_tree_id' => 0,
				'costogo_parent_kode_tree' => ''
			);
			$_SESSION['sess_costogo_parent_id'] = 0;
			$_SESSION['sess_costogo_parent_kode_tree'] = '';
			$this->session->set_userdata($datasess);
		}
	}
	
	function get_parent_kode_tree()
	{
		$parent = @$_SESSION['sess_costogo_parent_kode_tree'];
		return $this->session->userdata('costogo_parent_kode_tree') <> '' ? $this->session->userdata('costogo_parent_kode_tree') : $parent;
	}
	
		
	public function tambah_costogo_tree_item($idproyek,$tgl_rab)
	{		
		$pid = $this->get_parent_tree_id();
		$pkd = $this->get_parent_kode_tree();
		/* 
		$this->input->post('kode_tree') 
		$kdt = $this->input->post('kode_tree');
		*/
		$parentid = isset($pid) ? $pid : $this->input->post('tree_parent_id');
		$new_kt = $this->gen_kode_tree($this->input->post('id_proyek'), $parentid, $pkd, $tgl_rab); //$kdt
		// var_dump($new_kt);
		if($this->input->post('tree_item') && $this->input->post('tree_satuan')	&& $this->input->post('id_proyek'))
		{				
			if ($pkd == '') {
				$datainsert = array(
					'tree_parent_id' => $parentid,
					'tree_item' => $this->input->post('tree_item'),
					// 'tree_parent_kode' => $pkd,
					'tree_satuan' => $this->input->post('tree_satuan'),
					'id_proyek' =>  $this->input->post('id_proyek'),
					'volume' => $this->input->post('volume') <> '' ? $this->input->post('volume') : 0,
					'kode_tree' =>  $new_kt,
					'tanggal_kendali' => $tgl_rab
				);
			} else {
				$datainsert = array(
					'tree_parent_id' => $parentid,
					'tree_item' => $this->input->post('tree_item'),
					'tree_parent_kode' => $pkd,
					'tree_satuan' => $this->input->post('tree_satuan'),
					'id_proyek' =>  $this->input->post('id_proyek'),
					'volume' => $this->input->post('volume') <> '' ? $this->input->post('volume') : 0,
					'kode_tree' =>  $new_kt,
					'tanggal_kendali' => $tgl_rab
				);
			}
			//$this->_dump($datainsert);
			$resin = $this->db->insert('simpro_costogo_item_tree', $datainsert);
			if($resin)
			{
				echo json_encode(array('success'=>true, 'message'=>'Data costogo berhasil disimpan!'));
			} else json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));
		} else 
		{
			echo json_encode(array('success'=>false, 'message'=>'Data GAGAL disimpan!'));		
		}
	}

	public function get_detailmaterial_kode()
	{
		$data = $this->mdl_ctg_analisa->get_detailmaterial_kode($this->input->get('query'));
		$this->_out($data);	
	}
	
	public function get_satuan()
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
	
	function copy_rat_proyek_lain()
	{		
		$idproyek = ($this->input->get('id_proyek')) <> '' ? $this->input->get('id_proyek') : $_SESSION['proyek_id'];
		$query = sprintf("
			SELECT 
				simpro_tbl_tahap_kendali.tahap_kode_kendali AS kode_tree,
				simpro_tbl_tahap_kendali.tahap_nama_kendali AS tree_item,
				simpro_tbl_tahap_kendali.tahap_volume_kendali AS volume,
				simpro_tbl_tahap_kendali.tahap_satuan_kendali AS id_satuan,
				simpro_tbl_satuan.satuan_nama tree_satuan,
				proyek_id
			FROM simpro_tbl_tahap_kendali
			INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_tbl_tahap_kendali.tahap_satuan_kendali			
			WHERE simpro_tbl_tahap_kendali.proyek_id = '%d'
		", $idproyek);
		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
			$this->_out($data);
		} else $this->_out(array('total'=>0, 'data'=>'', '_dc'=>$_REQUEST['_dc']));
	}

	function get_data_proyek()
	{
		$query = "SELECT proyek_id AS id_proyek, proyek AS nama_proyek from simpro_tbl_proyek";
		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot, 'data'=>$rs->result_array(), '_dc'=>$_REQUEST['_dc']);
		}
		$this->_out($data);		
	}

	function copy_tree()
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

	function paste_tree($tgl_rab)	
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
					$new_kt = $this->gen_kode_tree($idproyek, $parentid[0], $kt[0],$tgl_rab);
					//echo $kt[0], ' ',$parentid[0], ' ', $new_kt;
					$data = array(
						'id_proyek' => $cp['id_proyek'],
						'tree_item' => $cp['tree_item'],
						'tree_satuan' => $cp['tree_satuan'],
						'volume' => $cp['volume'],
						'kode_tree' => $new_kt,
						'tree_parent_id' => $parentid[0],
						'tanggal_kendali' => $tgl_rab
					);
					if(!$this->db->insert("simpro_costogo_item_tree", $data)) $error=TRUE;
					$i++;
				}
				//$this->_dump($data);
				if(!$error) echo "Data berhasil di-paste";
					else echo "Data GAGAL dipaste!";
			} else echo "Tidak ada data yang di-copy, silahkan pilih item kemudian klik tombol 'Copy'";
		}	
	}
	
	function get_task_tree_item($idpro,$tgl_rab)
	{
		$param = $this->input->get('param');
		$arr = $this->tree($idpro, $depth=0,$tgl_rab,$param);
		echo json_encode(array('text'=>'.', 'children'=>$arr));
	}
	
	function tree($idpro, $depth='isnull',$tgl_rab,$param)
	{
		$result=array();
		$temp=array();
		$temp = $this->mdl_ctg_analisa->get_tree_item($idpro, $depth, $tgl_rab,$param)->result();
		if(count($temp))
		{			
			$i = 0;
			foreach($temp as $row){
		
				//$temp_harga = $this->mdl_ctg_analisa->get_tree_item_harga($idpro, $row->costogo_item_tree)->row_array();
				$data[] = array(
					'costogo_item_tree' => $row->costogo_item_tree,
					'id_proyek' => $row->id_proyek,
					'id_satuan' => $row->id_satuan,
					'kode_tree' => $row->kode_tree,
					'kode_analisa' => $row->kode_analisa,
					'tree_item' => utf8_encode($row->tree_item),
					'tree_satuan' => $row->tree_satuan,
					'volume' => $row->volume,
					'harga' => $row->hrg,
					'subtotal' => $row->sub,
					'tree_parent_id' => $row->tree_parent_id
					/* 
					'harga' => $temp_harga['harga'], 
					'subtotal' => $temp_harga['subtotal'],
					*/
				);
				

				if($depth == 0) $data[$i] = array_merge($data[$i], array('expanded' => true));
				
				## check if have a child
				$q = sprintf("SELECT * FROM simpro_costogo_item_tree WHERE id_proyek = '%d' AND trim(tree_parent_kode) = '$row->kode_tree' and tanggal_kendali = '$tgl_rab'",$row->id_proyek,$row->kode_tree);
				$query = $this->db->query($q);
				$is_child = $query->num_rows();

				if($is_child)
				{				
					// var_dump($row->costogo_item_tree);
					$result[] = array_merge(
						$data[$i],
						array(
							'iconCls' => 'task-folder',
							'ishaschild' => 1,
							'children'=> $this->tree($idpro,$row->kode_tree,$tgl_rab,$param)
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
				
				$i++;
			}

			// var_dump($result);
		}
		return array_filter($result);
	}
		
	function gen_kode_tree($proyekid, $parentid, $kode_tree, $tgl_rab)
	{
		$sql_tree = sprintf("
					SELECT 
						kode_tree 
					FROM simpro_costogo_item_tree 
					WHERE 
						id_proyek = '%d' 
						AND tree_parent_kode = '$kode_tree'
						and tanggal_kendali = '$tgl_rab'
					ORDER BY costogo_item_tree DESC", 
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
			if(isset($kt['kode_tree']))
			{
				$old_kode = $kt['kode_tree'];
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
	
	function del_tree_item($tgl_rab)
	{
		if($this->input->post('tree_item_id'))
		{
			$proyek_id = $this->session->userdata('proyek_id'); 
			$qry2 = $this->db->query(sprintf("DELETE FROM simpro_costogo_analisa_item_apek WHERE kode_tree = (select kode_tree from simpro_costogo_item_tree WHERE costogo_item_tree = '%d' and tanggal_kendali = '$tgl_rab') and tanggal_kendali = '$tgl_rab' and id_proyek = $proyek_id", $this->input->post('tree_item_id')));
			$qry = $this->db->query(sprintf("DELETE FROM simpro_costogo_item_tree WHERE costogo_item_tree = '%d' and tanggal_kendali = '$tgl_rab'", $this->input->post('tree_item_id')));
			if($qry && $qry2) echo json_encode(array("success"=>true, "message"=>"Data berhasil dihapus!"));
		}
	}
	
	function update_tree_item($tgl_rab)
	{		
		if($this->input->post('kode_tree') && $this->input->post('tree_item') && $this->input->post('satuan_id') 
		&& $this->input->post('id_proyek') && $this->input->post('costogo_item_tree'))
		{
			$du = array(
				'costogo_item_tree' => $this->input->post('costogo_item_tree'),
				'tree_parent_id' => $this->input->post('tree_parent_id'),
				'id_proyek' => $this->input->post('id_proyek'),
				'kode_tree' => $this->input->post('kode_tree'),
				'tree_satuan' => $this->input->post('satuan_id'),
				'tree_item' => $this->input->post('tree_item'),
				'volume' => $this->input->post('volume')
			);
			$this->db->where('tanggal_kendali', $tgl_rab);
			$this->db->where('costogo_item_tree', $this->input->post('costogo_item_tree'));
			$this->db->where('id_proyek', $this->input->post('id_proyek'));
			$this->db->where('tree_parent_id', $this->input->post('tree_parent_id'));
			if($this->db->update('simpro_costogo_item_tree', $du)) echo "Data berhasil diupdate";
				else echo "Data GAGAL diupdate!";
		}
	}
		
	function get_parent_tree_id()
	{
		//$parentid = isset($_SESSION['sess_parent_id']) ? $_SESSION['sess_parent_id'] : 0;
		$parentid = @$_SESSION['sess_costogo_parent_id'];		
		return $this->session->userdata('costogo_parent_tree_id') <> '' ? $this->session->userdata('costogo_parent_tree_id') : $parentid;
	}

	/*
	function set_costogo_item_tree()
	{
		if($this->input->post('id_proyek') && $this->input->post('costogo_item_tree') && $this->input->post('kode_tree'))
		{
			$this->session->set_userdata('sess_id_proyek', $this->input->post('id_proyek'));
			$this->session->set_userdata('sess_costogo_item_tree', $this->input->post('costogo_item_tree'));
			$this->session->set_userdata('sess_kode_tree', $this->input->post('kode_tree'));
			$_SESSION['sess_id_proyek'] = $this->input->post('id_proyek');
			$_SESSION['sess_costogo_item_tree'] = $this->input->post('costogo_item_tree');
			$_SESSION['sess_kode_tree'] = $this->input->post('kode_tree');
		}
	}	
	
	function get_costogo_item_tree()
	{
		if(isset($_SESSION['sess_id_proyek']) && isset($_SESSION['sess_costogo_item_tree']))
		{
			$data = array(
				'id_proyek' => $_SESSION['sess_id_proyek'],
				'costogo_item_tree' => $_SESSION['sess_costogo_item_tree'],
				'kode_tree' => $_SESSION['sess_kode_tree']
			);
			return $data;
		}
	}	
	*/
	
	function del_analisa_satuan_item($tgl_rab)
	{

		if($this->input->post('tree_item_id'))
		{
			$proyek_id = $this->session->userdata('proyek_id'); 

			$ratid = $this->input->post('tree_item_id');

			// $cek_data = "select 
			// 	count(kode_analisa) as count
			// 	from
			// 	simpro_costogo_analisa_item_apek
			// 	where
			// 	kode_analisa =
			// 	(SELECT kode_analisa FROM simpro_costogo_analisa_item_apek
			// 	WHERE tanggal_kendali = '$tgl_rab' and kode_tree = '$ratid' and id_proyek = $proyek_id)";
			// $q_cek = $this->db->query($cek_data)->row();
			// if ($q_cek->count > 1) {
			// 	$sql_del_item_apek = "delete from simpro_costogo_analisa_item_apek WHERE tanggal_kendali = '$tgl_rab' and kode_tree = '$ratid' and id_proyek = $proyek_id";
			// 	$q_del_item_apek = $this->db->query($sql_del_item_apek);
			// 	// $sql_upt_item_tree = "update simpro_costogo_item_tree set kode_analisa = '' WHERE tanggal_kendali = '$tgl_rab' and kode_tree = '$ratid' and id_proyek = $proyek_id";
			
			// 	if($q_del_item_apek) echo "Data Analisa satuan berhasil dihapus.";
			// 	else echo "Data Analisa satuan GAGAL dihapus!";
			// } else {
			// 	// $sql_del_apek = "delete from simpro_costogo_analisa_apek where parent_kode_analisa = 
			// 	// 	(SELECT kode_analisa FROM simpro_costogo_analisa_item_apek
			// 	// 	WHERE tanggal_kendali = '$tgl_rab' and kode_tree = '$ratid' and id_proyek = $proyek_id) and tanggal_kendali = '$tgl_rab' and id_proyek = $proyek_id";
			// 	// $q_del_apek = $this->db->query($sql_del_apek);

			// 	// $sql_del_asat = "delete from simpro_costogo_analisa_asat where kode_analisa = 
			// 	// 	(SELECT kode_analisa FROM simpro_costogo_analisa_item_apek
			// 	// 	WHERE tanggal_kendali = '$tgl_rab' and kode_tree = '$ratid' and id_proyek = $proyek_id) and tanggal_kendali = '$tgl_rab' and id_proyek = $proyek_id";
			// 	// $q_del_asat = $this->db->query($sql_del_asat);

			// 	$sql_del_item_apek = "delete from simpro_costogo_analisa_item_apek WHERE tanggal_kendali = '$tgl_rab' and kode_tree = '$ratid' and id_proyek = $proyek_id";
			// 	$q_del_item_apek = $this->db->query($sql_del_item_apek);
			// 	// $sql_upt_item_tree = "";

			$qry2 = $this->db->query(sprintf("DELETE FROM simpro_costogo_analisa_item_apek WHERE kode_tree = (select kode_tree from simpro_costogo_item_tree WHERE costogo_item_tree = '%d' and tanggal_kendali = '$tgl_rab') and tanggal_kendali = '$tgl_rab' and id_proyek = $proyek_id", $this->input->post('tree_item_id')));
			
				if($qry2) echo "Data Analisa satuan berhasil dihapus.";
				else echo "Data Analisa satuan GAGAL dihapus!";

			// }
			// $query = sprintf("
			// 	DELETE FROM simpro_costogo_analisa_asat WHERE tanggal_kendali = '$tgl_rab' and kode_analisa = 
			// 	(
			// 		SELECT kode_analisa FROM simpro_costogo_analisa_item_apek
			// 		WHERE tanggal_kendali = '$tgl_rab' and kode_tree = '%d' and id_proyek = $proyek_id
			// 	) and id_proyek = $proyek_id;

			// 	DELETE FROM simpro_costogo_analisa_apek where kode_analisa = 
			// 	(
			// 		SELECT kode_analisa FROM simpro_costogo_analisa_item_apek
			// 		WHERE tanggal_kendali = '$tgl_rab' and kode_tree = '%d' and id_proyek = $proyek_id
			// 	) and id_proyek = $proyek_id;			
				
			// 	DELETE FROM simpro_costogo_analisa_item_apek WHERE tanggal_kendali = '$tgl_rab' and id_proyek = $proyek_id and kode_tree = '%d';				
			// ", $ratid, $ratid, $ratid);		
			// if($this->db->query($query)) echo "Data Analisa satuan berhasil dihapus.";
			// 	else echo "Data Analisa satuan GAGAL dihapus!";
		} 
	}
	
	public function get_daftar_analisa($id,$tgl_rab)
	{
		$idproyek = isset($id) ? $id : $this->get_idproyek();	
		//$idproyek = ($this->get_idproyek() > 0) ? $this->get_idproyek() : $id;
		if($this->input->get('id_kat_analisa'))
		{
			$_SESSION['id_kat_analisa_daftar'] = $this->input->get('id_kat_analisa');
			$idkat = $this->input->get('id_kat_analisa');
		} else {
			$idkat = isset($_SESSION['id_kat_analisa_daftar']) ? $_SESSION['id_kat_analisa_daftar'] : FALSE;
		}
		$data = $this->mdl_ctg_analisa->get_daftar_analisa_pekerjaan($idproyek, $idkat,$tgl_rab);
		$this->_out($data);
	}
	
	function tambah_daftar_analisa($tgl_rab)
	{
		if($this->input->post('id_proyek') && $this->input->post('kode_analisa'))
		{
			$is_analisa_exists = $this->db->query(sprintf("SELECT * FROM simpro_costogo_analisa_daftar WHERE kode_analisa ='%s' AND id_proyek = '%d' and tanggal_kendali = '$tgl_rab'", $this->input->post('kode_analisa'), $this->input->post('id_proyek')))->num_rows();
			$satid = $this->get_satuan_id('Ls');
			if($this->input->post('id_satuan'))
			{
				$satuan = is_numeric($this->input->post('id_satuan')) ? $this->input->post('id_satuan') : $this->get_satuan_id($this->input->post('id_satuan'));
			} else $satuan = $satid;
			
			if($this->input->post('id_kat_analisa'))
			{
				$kategori = is_numeric($this->input->post('id_kat_analisa')) ? $this->input->post('id_kat_analisa') : $this->get_kategori_id($this->input->post('id_kat_analisa'));
			} else $kategori = 10;

			# generate kode analisa
			$lastkode = "";
			$qkode = sprintf("
				SELECT (TO_NUMBER(RIGHT(MAX(kode_analisa), 3), '9999')+1) as lastkode
				FROM simpro_costogo_analisa_daftar
				WHERE id_proyek = '%d' and tanggal_kendali = '$tgl_rab'", $this->input->post('id_proyek'));
			$kode = $this->db->query($qkode)->row();
			if($kode->lastkode <> '')
			{
				$num = $kode->lastkode;
				switch(strlen($num))
				{
					case 1: $lastkode = "AN00".$num; break;
					case 2: $lastkode = "AN0".$num; break;
					case 3: $lastkode = "AN".$num; break;
				}
			} else $lastkode = "AN001";
			# end generate kode analisa
			
			$lastkode = ($is_analisa_exists > 0) ? $this->input->post('kode_analisa') : $lastkode;
			
			$data = array(
				'id_proyek' => $this->input->post('id_proyek'),
				'id_kat_analisa' => $kategori,
				'kode_analisa' => $lastkode, 
				'nama_item' => $this->input->post('nama_item'),
				'id_satuan' => $satuan,
				'tanggal_kendali' => $tgl_rab
			);			
			
			if($is_analisa_exists)
			{
				$wdu = array(
					'id_proyek'=>$this->input->post('id_proyek'),
					'kode_analisa'=>$this->input->post('kode_analisa'),
					'tanggal_kendali'=>$tgl_rab
				);
				$this->db->update('simpro_costogo_analisa_daftar', $data, $wdu);			
				echo "Data berhasil diupdate.";
			} else
			{				
				$this->db->insert('simpro_costogo_analisa_daftar',$data);
				echo "Data berhasil ditambah.";
			}		
		} else 
		{
			echo "Update daftar Analisa GAGAL!";
		}
	}
	
	function edit_harga_satuan_asat($idproyek,$tgl_rab)
	{
		$idproyek = isset($idproyek) ? $idproyek : $this->get_idproyek();	
		$data = $this->mdl_ctg_analisa->get_harga_satuan_asat($idproyek,$tgl_rab);		
		$this->_out($data);	
	}
	
	function set_idproyek()
	{
		if($this->input->post('id_proyek'))
		{
			$this->session->set_userdata('id_proyek', $this->input->post('id_proyek'));
			$_SESSION['id_proyek'] = $this->input->post('id_proyek');
		}
	}	
	
	function get_idproyek()
	{
		if($this->session->userdata('sess_id_proyek') || $_SESSION['id_proyek'])
		{
			return ($this->session->userdata('sess_id_proyek') <> '') ? $this->session->userdata('sess_id_proyek') : $_SESSION['id_proyek'];
		} else return false;
	}
	
	function update_harga_asat($tgl_rab)
	{
		if($this->input->post('id_proyek') && $this->input->post('kode_material'))
		{
			if($this->input->post('harga')) $harga = $this->input->post('harga');
				else $harga = 0;
			$postdata = array(
				'kode_material' => $this->input->post('kode_material'),
				'id_proyek' => $this->input->post('id_proyek'),
				'keterangan' => $this->input->post('keterangan'),
				'harga' => $harga
			);
			$this->db->where('kode_material', $this->input->post('kode_material'));
			$this->db->where('id_proyek', $this->input->post('id_proyek'));
			$this->db->where('tanggal_kendali', $tgl_rab);
			if($this->db->update('simpro_costogo_analisa_asat', $postdata))
			{
				if($this->update_kode_costogo($this->input->post('id_proyek'),$tgl_rab)) echo "Harga berhasil diupdate.";
					else echo "GAGAL update kode costogo";
			} else echo "Harga satuan GAGAL diupdate!";
		}
	}
	
	function delete_tree_item($tgl_rab)
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
					if($this->delete_tree_recursive($id_proyek, $v, $tgl_rab)) $success=TRUE;
						else $success=FALSE;
				}
				echo "Data berhasil dihapus!";
			}
		}		
	}
	
	private function delete_tree_recursive($id_proyek, $id_tree, $tgl_rab)
	{				
		$q = sprintf("SELECT * FROM simpro_costogo_item_tree WHERE tanggal_kendali = '$tgl_rab' and id_proyek = '%d' AND tree_parent_id = '%d'", $id_proyek, $id_tree);
		$query = $this->db->query($q);
		$is_data = $query->result_array();
		if(count($is_data) > 0)
		{				
			foreach($is_data as $k=>$v)
			{
				$this->delete_tree_recursive($id_proyek, $v['costogo_item_tree'], $tgl_rab);
			}
		} else
		{
			//costogo_item_tree
			if($this->db->delete("simpro_costogo_item_tree",array(
				"id_proyek"=>$id_proyek,
				"costogo_item_tree"=>$id_tree,
				"tanggal_kendali"=>$tgl_rab
				))
			&& $this->db->delete("simpro_costogo_analisa_item_apek",array(
				"id_proyek"=>$id_proyek,
				"kode_tree"=>$v['kode_tree'],
				"tanggal_kendali"=>$tgl_rab
				))
			) return true;
				else return false;
		}
	}
	
	function upload_daftar_analisa($tgl_rab)
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv|txt';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);					
		
		try {
			if(!$this->upload->do_upload('upload_analisa'))
			{
				throw new Exception(sprintf('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: csv|txt.\nErrors: %s', $this->upload->display_errors()));
			} else
			{
				$data = $this->upload->data();
				$this->db->query("SET CLIENT_ENCODING TO 'SQL_ASCII'");				
				$sql = sprintf("COPY simpro_tmp_upload_daftar_analisa FROM '%s' DELIMITER ',' CSV HEADER", realpath("./uploads/".$data['file_name']));
				// cek dulu ada yang duplikat nggak ? mana saja yang duplikat?
				if($this->db->query($sql))
				{
					$qdel = sprintf("DELETE FROM simpro_costogo_analisa_daftar WHERE id_proyek = '%d' and tanggal_kendali = '$tgl_rab'",$this->input->post('id_proyek'));
					$this->db->query($qdel);
					$sql_insert = sprintf("
						INSERT INTO simpro_costogo_analisa_daftar(kode_analisa, nama_item, id_kat_analisa, id_satuan, id_proyek, tanggal_kendali)
						(
							SELECT 
							kode_analisa, 
							nama_item, 
							'%d' AS id_kat_analisa,
							'31' AS id_satuan,
							'%d' AS id_proyek,
							'$tgl_rab'
							FROM simpro_tmp_upload_daftar_analisa
						)
					", 10, $this->input->post('id_proyek'));
					if($this->db->query($sql_insert)) 
					{
						$this->db->query("TRUNCATE TABLE simpro_tmp_upload_daftar_analisa");
						echo json_encode(
							array(	"success"=>true, 
									"message"=>"Data berhasil diupload.", 
									"file" => $data['file_name'])
						);
					} else echo json_encode(array("success"=>true, 
							"message"=>"Data GAGAL diupload.", 
							"file" => $data['file_name']));
				} else
				{
					echo json_encode(
						array(	"success"=>true, 
								"message"=>"Data GAGAL diupload.", 
								"file" => $data['file_name'])
					);								
				}				
			}
		} catch(Exception $e)
		{
			echo json_encode(array(
				'success' => false,
				'message' => $e->getMessage(),
				'file' => 'undefined'
			));			
		}	
	}	
	
	function set_analisa_itemid()
	{
		if($this->input->post('id_proyek') && $this->input->post('kode_analisa'))
		{
			/* CI session problem, so using native PHP session */
			$this->session->set_userdata('id_proyek', $this->input->post('id_proyek'));
			$this->session->set_userdata('id_data_analisa', $this->input->post('id_data_analisa'));
			$this->session->set_userdata('kode_analisa', $this->input->post('kode_analisa'));
			$_SESSION['id_proyek'] = $this->input->post('id_proyek');
			$_SESSION['id_data_analisa'] = $this->input->post('id_data_analisa');
			$_SESSION['kode_analisa'] = $this->input->post('kode_analisa');
		}
	}	

	function get_analisa_itemid()
	{
		if(isset($_SESSION['id_proyek']) && isset($_SESSION['kode_analisa']))
		{
			$data = array(
				'id_proyek' => $_SESSION['id_proyek'],
				'id_data_analisa' => $_SESSION['id_data_analisa'],
				'kode_analisa' => $_SESSION['kode_analisa']
			);
			return $data;
		}
	}	
	
	function delete_analisa_pekerjaan($tgl_rab)
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_proyek'))
		{
			$where = array('kode_analisa'=>$this->input->post('kode_analisa'), 'id_proyek'=>$this->input->post('id_proyek'), 'tanggal_kendali'=>$tgl_rab);
			$del_daftar = $this->db->delete('simpro_costogo_analisa_daftar', $where);
			$del_asat = $this->db->delete('simpro_costogo_analisa_asat', $where);
			$del_apek = $this->db->delete('simpro_costogo_analisa_apek', array('parent_kode_analisa'=>$this->input->post('kode_analisa'), 'id_proyek'=>$this->input->post('id_proyek'), 'tanggal_kendali'=>$tgl_rab));
			if($del_daftar && $del_asat && $del_apek)
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus!";
			
		}
	}	
	
	function delete_asat_apek($tgl_rab)
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_proyek'))
		{
			// var_dump($this->input->post('kode_analisa').'x'.$this->input->post('id_proyek').'x'.$tgl_rab);
			$del_asat = $this->db->delete('simpro_costogo_analisa_asat', array(
				'kode_analisa'=>$this->input->post('kode_analisa'), 
				'id_proyek'=>$this->input->post('id_proyek'), 
				'tanggal_kendali'=>$tgl_rab
				));
			$del_apek = $this->db->delete('simpro_costogo_analisa_apek', array(
				'parent_kode_analisa'=>$this->input->post('kode_analisa'), 
				'id_proyek'=>$this->input->post('id_proyek'), 
				'tanggal_kendali'=>$tgl_rab
				));
			if($del_asat && $del_apek)
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus!";
			
		}
	}
		
	function clear_search_data_analisa()
	{
		unset($_SESSION['id_kat_analisa_daftar']);
		unset($_SESSION['id_kat_analisa_daftar_apek']);
	}
	
	function get_data_analisa_pekerjaan($idtender,$tgl_rab)
	{
		$data = $this->mdl_ctg_analisa->get_data_analisa_pekerjaan($idtender,$tgl_rab);
		$this->_out($data);	
	}
	
	function edit_koefisien_apek()
	{
		if($this->input->post('id_analisa_apek'))
		{
			$this->db->where('id_analisa_apek', $this->input->post('id_analisa_apek'));
			if($this->db->update('simpro_costogo_analisa_apek', array('koefisien'=>$this->input->post('koefisien'))))
				echo "Data berhasil diupdate.";
					else echo "data GAGAL diupdate!";
		}
	}	

	function get_satuan_id($satname)
	{
		$get_satuan = $this->db->query(sprintf("SELECT satuan_id FROM simpro_tbl_satuan WHERE satuan_nama = '%s'",$satname))->row_array();
		$satuan_id = $get_satuan['satuan_id'];	
		return $satuan_id;
	}

	function get_kategori_id($katname)
	{
		$get_kat = $this->db->query(sprintf("SELECT id_kat_analisa FROM simpro_rat_analisa_kategori WHERE kat_name = '%s'",$katname))->row_array();
		$kat_id = $get_kat['id_kat_analisa'];	
		return $kat_id;
	}
	
	public function get_kategori_bahan()
	{
		$rs = $this->db->query("SELECT id_kat_analisa, kode_kat || '. ' || kat_name AS kategori FROM simpro_rat_analisa_kategori")->result_array();
		foreach($rs as $k=>$v)
		{
			$kategori[] = array(
				'id_kat_analisa'=>$v['id_kat_analisa'],
				'kategori'=>$v['kategori']
			);
		}
		echo json_encode(array('success'=>true,'total'=>count($kategori),'data'=>$kategori));
	}
	
	function get_data_ansat()
	{
		if($this->input->get('subbidang_kode'))
		{
			$data = $this->mdl_ctg_analisa->get_ansat($this->input->get('subbidang_kode'));
		} else $data = $this->mdl_ctg_analisa->get_ansat('');
		$this->_out($data);	
	}
	
		
	function tambah_ansat($tgl_rab)
	{
		if($this->input->post('kode_material') && $this->input->post('id_detail_material') 
		&& $this->input->post('koefisien') && $this->input->post('id_proyek'))
		{

			$km = explode(',',$this->input->post('kode_material'));
			$dmid = explode(',',$this->input->post('id_detail_material'));
			$koef = explode(',',$this->input->post('koefisien'));
			$id_proyek = $this->input->post('id_proyek');
			$var_analisa = $this->get_analisa_itemid();
			$i=0;
			$msg = '';
			$cek_jml = 0;
			foreach($km as $k=>$v)
			{
				$cek_data = $this->db->query("SELECT simpro_tbl_detail_material.detail_material_nama 
					from simpro_costogo_analisa_asat 
					join simpro_tbl_detail_material 
					on simpro_costogo_analisa_asat.kode_material = simpro_tbl_detail_material.detail_material_kode 
					where simpro_costogo_analisa_asat.kode_material = '$v' 
					and simpro_costogo_analisa_asat.id_proyek = $var_analisa[id_proyek] 
					and simpro_costogo_analisa_asat.id_data_analisa = $var_analisa[id_data_analisa]");
				
				if ($cek_data->result()) {
					$msg = sprintf("%s,<br>%s",$msg,$cek_data->row()->detail_material_nama);
					$cek_jml++;
				} else {
					$data = array(
						'kode_material' => $v,
						'id_detail_material' => $dmid[$i],
						'id_data_analisa' => $var_analisa['id_data_analisa'],
						'kode_analisa' => $var_analisa['kode_analisa'],
						'id_proyek' => $var_analisa['id_proyek'],
						'koefisien' => $koef[$i],
						'tanggal_kendali'=>$tgl_rab,
						'harga' => 0
					);	
					$this->db->insert('simpro_costogo_analisa_asat',$data);
				}						
				$i++;
			}

			if ($msg != '') {
				$msg = $msg.'<br>telah disimpan sebelumnya<br><br>';
			}

			$m = $msg;

			if ($cek_jml < $i) {
				$m = $msg."Data telah disimpan.";
			}
			
			// if($this->db->insert_batch('simpro_costogo_analisa_asat',$data)) 
			// {
				if($this->update_kode_costogo($this->input->post('id_proyek'),$tgl_rab)) echo $m;
					else echo "GAGAL update kode RAP";
			// } else echo "Data GAGAL disimpan!";
		}
	}
	
	function update_kode_costogo($id,$tgl_rab)
	{
		$error = FALSE;
		$sql_kode = "
			SELECT kode_kat FROM simpro_rat_analisa_kategori
			WHERE (subbidang_kode <> '' OR subbidang_kode IS NOT NULL)
			ORDER BY kode_kat ASC		
		";
		$kcostogo = $this->db->query($sql_kode)->result_array();
		foreach($kcostogo as $k=>$v)
		{
			// $sql = sprintf("
			// 	SELECT 
			// 		DISTINCT(simpro_costogo_analisa_asat.kode_material) AS kode_material,
			// 		LEFT(simpro_costogo_analisa_asat.kode_material,3),	
			// 		simpro_costogo_analisa_asat.id_analisa_asat,			
			// 		simpro_rat_analisa_kategori.kode_kat,
			// 		COALESCE(simpro_costogo_analisa_asat.harga,0) AS harga				
			// 	FROM simpro_costogo_analisa_asat
			// 	LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_costogo_analisa_asat.kode_material,3)
			// 	WHERE simpro_costogo_analisa_asat.id_proyek = '%d' 
			// 	AND simpro_rat_analisa_kategori.kode_kat = '%s' 
			// 	AND simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
			// 	GROUP BY simpro_costogo_analisa_asat.kode_material, 				
			// 		simpro_rat_analisa_kategori.kode_kat,
			// 		simpro_costogo_analisa_asat.harga,
			// 		simpro_costogo_analisa_asat.id_analisa_asat
			// 	ORDER BY simpro_costogo_analisa_asat.kode_material ASC
			// 	", $id,$v['kode_kat']);
			// $row = $this->db->query($sql)->result_array();
			// if(count($row) > 0) 
			// {
			// 	$i = 1;
			// 	$lastkode = "";
			// 	foreach($row as $k=>$v)
			// 	{
			// 		switch(strlen($i))
			// 		{
			// 			case 1: $lastkode = '000'.$i; break;
			// 			case 2: $lastkode = '00'.$i; break;
			// 			case 3: $lastkode = '0'.$i; break;
			// 			case 4: $lastkode = $i; break;
			// 		}
			// 		$kode = $v['kode_kat'].$lastkode;
			// 		/* update data */
			// 		$updata = array('kode_rap'=>$kode);
			// 		$this->db->where('tanggal_kendali', $tgl_rab);
			// 		$this->db->where('id_analisa_asat', $v['id_analisa_asat']);
			// 		if(!$this->db->update('simpro_costogo_analisa_asat', $updata)) $error = TRUE;
			// 		/* end update data */
			// 		$i++;
			// 	}
			// }		

			$kd_kat = $v['kode_kat'];
			$sql_kode_null = "				select *,
				case when keterangan isnull
				then (select keterangan from simpro_costogo_analisa_asat where kode_material = analisa.kode_material and id_proyek = analisa.id_proyek and keterangan is not null group by keterangan)
				else keterangan
				end as keterangan_ad,
				case when length(kd_rap_int) = 1
				then kode_kat || '000' || kd_rap_int
				when length(kd_rap_int) = 2
				then kode_kat || '00' || kd_rap_int
				when length(kd_rap_int) = 3
				then kode_kat || '0' || kd_rap_int
				when length(kd_rap_int) = 4
				then kode_kat || kd_rap_int
				end as rap_kd
				from (
				select *, 
				case when kode_rap is not null and kode_rap != ''
				then kode_rap_numb::int 
				when (kode_rap isnull) and (select count(kode_rap) from simpro_costogo_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali) = 0
				then (ROW_NUMBER() OVER (ORDER BY 'a')) - coalesce((select count(kode_rap) from simpro_costogo_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali),0) + coalesce((select (right(kode_rap,4)::int) as int_rap from simpro_costogo_analisa_asat where kode_rap is not null 
					and tanggal_kendali = analisa_asat.tanggal_kendali and id_proyek = analisa_asat.id_proyek order by int_rap desc limit 1),0)
				when (kode_rap isnull) and (select count(kode_rap) from simpro_costogo_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek 
					and tanggal_kendali = analisa_asat.tanggal_kendali) > 0
				then (ROW_NUMBER() OVER (ORDER BY 'a'))
				end::text as kd_rap_int
				from (SELECT 
					simpro_costogo_analisa_asat.tanggal_kendali,
					simpro_costogo_analisa_asat.kode_material as kode_material,
					simpro_costogo_analisa_asat.id_proyek,
					simpro_costogo_analisa_asat.keterangan,
					case when (select count(a.kode_material) from simpro_costogo_analisa_asat a where a.kode_material = simpro_costogo_analisa_asat.kode_material and a.id_proyek = id_proyek 
					and a.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali) > 1
					then right((select b.kode_rap from simpro_costogo_analisa_asat b where b.kode_material = simpro_costogo_analisa_asat.kode_material and b.id_proyek = simpro_costogo_analisa_asat.id_proyek and b.kode_rap is not null 
					and b.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali group by b.kode_rap),4)
					else right(kode_rap,4)
					end kode_rap_numb,
					case when (select count(a.kode_material) from simpro_costogo_analisa_asat a where a.kode_material = simpro_costogo_analisa_asat.kode_material and a.id_proyek = id_proyek 
					and a.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali) > 1
					then (select b.kode_rap from simpro_costogo_analisa_asat b where b.kode_material = simpro_costogo_analisa_asat.kode_material and b.id_proyek = simpro_costogo_analisa_asat.id_proyek and b.kode_rap is not null 
					and b.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali group by b.kode_rap)
					else kode_rap
					end kode_rap,
					simpro_rat_analisa_kategori.kode_kat
				FROM simpro_costogo_analisa_asat
				JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_costogo_analisa_asat.kode_material,3)
				WHERE simpro_costogo_analisa_asat.id_proyek = $id
					AND simpro_rat_analisa_kategori.kode_kat = '$kd_kat'
					and simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
				) analisa_asat
				GROUP BY kode_material, 
					keterangan,
					tanggal_kendali,
					kode_kat,
					kode_rap,
					kode_rap_numb,
					id_proyek
				order by kode_rap) analisa";

			$q_cek_material = $this->db->query($sql_kode_null);

			if ($q_cek_material->result()) {
				foreach ($q_cek_material->result() as $r) {
					$updata = array('kode_rap'=>$r->rap_kd,'keterangan'=>$r->keterangan_ad);
					$this->db->where(array('kode_material' => $r->kode_material,'id_proyek' => $r->id_proyek, 'tanggal_kendali'=>$tgl_rab));
					if(!$this->db->update('simpro_costogo_analisa_asat', $updata)) $error = TRUE;
				}
			}		
		}
		if(!$error) return true;
			else return false;
	}
	
	function tambah_apek($tgl_rab)
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_proyek'))
		{
			$harga = 0;
			$km = explode(',',$this->input->post('kode_analisa'));
			$dmid = explode(',',$this->input->post('id_data_analisa'));
			//$koef = explode(',',$this->input->post('koefisien'));
			$harga = explode(',',$this->input->post('harga_satuan'));
			$var_analisa = $this->get_analisa_itemid();
			$i=0;
			$error_duplicate = FALSE;
			$error_level = FALSE;
			foreach($km as $k=>$v)
			{		
				if(strtoupper($var_analisa['kode_analisa']) == strtoupper($v)) $error_duplicate = TRUE;
				if(!$this->cek_level_apek(strtoupper($v))) $error_level = TRUE;

				$var_w = array(
					'kode_analisa' => strtoupper($v),
					'parent_kode_analisa' => strtoupper($var_analisa['kode_analisa']),
					'id_proyek' => $this->input->post('id_proyek'),
					'tanggal_kendali' => $tgl_rab
				);
				if ($this->db->get_where('simpro_costogo_analisa_apek',$var_w)->num_rows() > 0) {
					echo "Analisa Sudah Ditambahkan Sebelumnya..";
					$data = "";
				} else {
					$data[] = array(
						'kode_analisa' => strtoupper($v),
						'id_data_analisa' => $dmid[$i],
						'parent_kode_analisa' => strtoupper($var_analisa['kode_analisa']),
						'parent_id_analisa' => $var_analisa['id_data_analisa'],
						'id_proyek' => $this->input->post('id_proyek'),
						'koefisien' => 1, //$koef[$i],
						'harga' => $harga[$i],
						'tanggal_kendali' => $tgl_rab
					);
				}

				$i++;
			}
			
			if(strtoupper($var_analisa['kode_analisa']) != '')
			{
				if(!$error_duplicate)
				{
					if(!$error_level)
					{
						if ($data != "") {
							if($this->db->insert_batch('simpro_costogo_analisa_apek',$data)) echo "Data telah disimpan.";
								else echo "Data GAGAL disimpan!";
						}
					} else echo "Error: Maksimal dua jenjang level analisa, sistem tidak memperbolehkan melebihi 2 jenjang analisa.";
				} else echo "Tidak boleh memilih kode analisa yang sama!";
			} else echo "Data GAGAL disimpan, parent kode analia Kosong!";
		} else echo "Error - Post Data inComplete!";
	}
		
	function set_costogo_item_tree()
	{
		if($this->input->post('id_proyek') && $this->input->post('costogo_item_tree') && $this->input->post('kode_tree'))
		{
			/* CI session problem, so using native PHP session */
			$this->session->set_userdata('sess_id_proyek', $this->input->post('id_proyek'));
			$this->session->set_userdata('sess_costogo_item_tree', $this->input->post('costogo_item_tree'));
			$this->session->set_userdata('sess_kode_tree', $this->input->post('kode_tree'));
			$_SESSION['sess_id_proyek'] = $this->input->post('id_proyek');
			$_SESSION['sess_costogo_item_tree'] = $this->input->post('costogo_item_tree');
			$_SESSION['sess_kode_tree'] = $this->input->post('kode_tree');
		}
	}	
	
	function get_costogo_item_tree()
	{
		if(isset($_SESSION['sess_id_proyek']) && isset($_SESSION['sess_costogo_item_tree']))
		{
			$data = array(
				'id_proyek' => $_SESSION['sess_id_proyek'],
				'costogo_item_tree' => $_SESSION['sess_costogo_item_tree'],
				'kode_tree' => $_SESSION['sess_kode_tree']
			);
			return $data;
		}
	}	
	
	function tambah_apek_costogo($tgl_rab)
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_proyek'))
		{
			$is_data = 0;
			$var_item = $this->get_costogo_item_tree();			
			# cek data
			$q_cek = sprintf("SELECT * FROM simpro_costogo_analisa_item_apek WHERE costogo_item_tree='%d' AND id_proyek='%d'
								and tanggal_kendali = '$tgl_rab'",
							$var_item['costogo_item_tree'], $this->input->post('id_proyek')
							);						
			$is_data = $this->db->query($q_cek)->num_rows();

			$data = array(
				'id_proyek' => $this->input->post('id_proyek'),
				'id_data_analisa' => $this->input->post('id_data_analisa'),
				'kode_analisa' => $this->input->post('kode_analisa'),
				'harga' => $this->input->post('harga_satuan'),
				'costogo_item_tree' => $var_item['costogo_item_tree'],
				'kode_tree' => $var_item['kode_tree'],
				'tanggal_kendali' => $tgl_rab
			);			
						
			if($is_data > 0)
			{			
				$this->db->where('id_proyek', $this->input->post('id_proyek'));
				$this->db->where('costogo_item_tree', $var_item['costogo_item_tree']);
				if($this->db->update('simpro_costogo_analisa_item_apek', $data)) echo "Data telah diupdate.";
					else echo "Data GAGAL diupdate!";
					
			} else 
			{
				if($this->db->insert('simpro_costogo_analisa_item_apek',$data)) echo "Data telah disimpan.";
					else echo "Data GAGAL disimpan!";
			}			
		}
	}
	
	private function cek_level_apek($kd_analisa)
	{
		$q_cek_level = sprintf("SELECT * FROM simpro_costogo_analisa_asat WHERE kode_analisa = '%s'", $kd_analisa);
		$isdata = $this->db->query($q_cek_level)->num_rows();
		if($isdata >= 1) return TRUE;
			else return FALSE;
	}
	
	public function delete_apek()
	{
		if($this->input->post('id_analisa_apek'))
		{
			if($this->db->delete('simpro_costogo_analisa_apek', array('id_analisa_apek'=>$this->input->post('id_analisa_apek'))))
				echo "Data berhasil dihapus.";
					else echo "Data GAGAL dihapus!";
		}
	}
	
	private function _out($data)
	{
		$callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '';
		if ($data['total'] > 0)
		{
			$output = json_encode($data); //JSON_NUMERIC_CHECK
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

	function get_total_ctga()
	{
		if($this->input->post('id_proyek'))
		{
			$data = $this->mdl_ctg_analisa->total_rapa($this->input->post('id_proyek'));
			if(isset($data['data']['total_rapa'])) echo number_format($data['data']['total_rapa'],0);
			//$this->_out($data);		
		}
	}

	function delete_analisa($tgl_rab)
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_proyek'))
		{
			#analisa
			$kd_analisa = explode(",",$this->input->post('kode_analisa'));
			$id_analisa = explode(",",$this->input->post('id_data_analisa'));
			$idtender = $this->input->post('id_proyek');
			$i=0;
			$success = TRUE;
			foreach($kd_analisa as $k=>$v)
			{
				$arr_w = array(
					'kode_analisa' => $v, 
					'id_proyek' => $idtender,
					'tanggal_kendali'=>$tgl_rab
				);			
				$id_item_apek = $this->db->delete('simpro_costogo_analisa_item_apek',$arr_w);

				$where = array('kode_analisa'=>$v, 'id_proyek'=>$idtender,'tanggal_kendali'=>$tgl_rab);
				$del_daftar = $this->db->delete('simpro_costogo_analisa_daftar', $where);
				$del_asat = $this->db->delete('simpro_costogo_analisa_asat', $where);
				$del_asat = $this->db->delete('simpro_costogo_analisa_apek', $where);
				$del_apek = $this->db->delete('simpro_costogo_analisa_apek', array('parent_kode_analisa'=>$v, 'id_proyek'=>$idtender,'tanggal_kendali'=>$tgl_rab));
				if($del_daftar && $del_asat && $del_apek)
				{
					$success = TRUE;
				} else $success = FALSE;
				$i++;				
			}		
			if($success)
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus!";
		}
	}

	function delete_ansat($tgl_rab)
	{
		if($this->input->post('id_analisa_asat'))
		{
			$idasat = explode(",",$this->input->post('id_analisa_asat'));
			$kdanalisa = explode(",",$this->input->post('kode_analisa'));
			$i=0;
			$success = TRUE;
			foreach($idasat as $k=>$v)
			{
				
				if(substr($kdanalisa[$i],0,2) == 'AN')
				{
					if($this->db->delete('simpro_costogo_analisa_apek',array('id_analisa_apek'=>$v,'tanggal_kendali'=>$tgl_rab)))
						$success = TRUE;
							else $success = FALSE;
				} else
				{
					if($this->db->delete('simpro_costogo_analisa_asat',array('id_analisa_asat'=>$v,'tanggal_kendali'=>$tgl_rab)))
						$success = TRUE;
							else $success = FALSE;
				}
				$i++;
			}
			if($success) echo "Data Analisa Satuan berhasil dihapus.";
				else echo "Data analisa satuan GAGAL dihapus!";
		} else echo "Pilih item analisa yang mau dihapus!";
	}

	function get_total_ctg()
	{
		if($this->input->post('id_proyek'))
		{
			$data = $this->mdl_ctg_analisa->total_rapa($this->input->post('id_proyek'));
			if(isset($data['data']['total_rapa'])) echo number_format($data['data']['total_rapa'],0);
			//$this->_out($data);		
		}
	}

	function get_data_proyek_byid()
	{
		$query = sprintf("
			SELECT 
				simpro_tbl_proyek.*, 
				simpro_tbl_divisi.divisi_name 
			FROM 
				simpro_tbl_proyek 
			LEFT JOIN simpro_tbl_divisi ON simpro_tbl_divisi.divisi_id = simpro_tbl_proyek.divisi_kode			
			WHERE simpro_tbl_proyek.proyek_id = %d
			", $this->session->userdata('proyek_id'));
		$rs = $this->db->query($query);
		$tot = $rs->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot, 'data'=>$rs->row_array());
			return $data;
		}
	}

	function total_costogoa($tgl_rab)
	{
		$data = $this->mdl_ctg_analisa->total_costogoa($this->session->userdata('proyek_id'),$tgl_rab);
		if(isset($data['data']['total_costogoa'])) return $data['data']['total_costogoa'];
	}
	
	function costogoa_to_xls($tgl_rab="")
	{
		$this->load->library('excel');
		$dp = $this->get_data_proyek_byid(); //
		$costogoas = $this->mdl_ctg_analisa->costogo_costogoa($this->session->userdata('proyek_id'),$tgl_rab);		
		$costogoa = $costogoas['data'];
		$total_costogoa = $this->total_costogoa($tgl_rab);
		$persen_costogoa = 0; //$this->persen_rata($idtender);
		$data = array(
			'data_proyek' => $dp['data']
		);	
		
		$nama_proyek = $data['data_proyek']['proyek'];
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('costogoa');
		$this->excel->getActiveSheet()->setCellValue('A1', $nama_proyek);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		 
					
		# informasi data umum proyek
		$data_proyek = array(
			'Divisi' => $data['data_proyek']['divisi_name'],
			'Proyek' => $data['data_proyek']['proyek'], 
			'Nilai Kontrak (excl. PPN)' => $data['data_proyek']['nilai_kontrak_non_ppn'], 
			'Nilai Kontrak (incl. PPN)' => $data['data_proyek']['nilai_kontrak_ppn'], 
			'Waktu Pelaksanaan' => $data['data_proyek']['total_waktu_pelaksanaan']
			);
		
		$a = 3;
		foreach($data_proyek as $k=>$v)
		{
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $a), $k);
			$this->excel->getActiveSheet()->setCellValue(sprintf('B%d', $a), $v);
			$a++;
		}
		# end info proyek
		
		# data
		$this->excel->getActiveSheet()->setCellValue('A11','NO');
		$this->excel->getActiveSheet()->setCellValue('B11','KODE RAP');
		$this->excel->getActiveSheet()->setCellValue('C11','KODE MATERIAL');
		$this->excel->getActiveSheet()->setCellValue('D11','NAMA');
		$this->excel->getActiveSheet()->setCellValue('E11','SATUAN');
		$this->excel->getActiveSheet()->setCellValue('F11','VOLUME');
		$this->excel->getActiveSheet()->setCellValue('G11','HARGA SATUAN');
		$this->excel->getActiveSheet()->setCellValue('H11','SUBTOTAL');
		$lasta = 0;
		if(count($costogoa) > 0)
		{
			$i = 1;
			$subbidang = "";
			$subtotal = 0;
			$nextsub = "";
			$start_A = 12;
			for($a=0; $a < count($costogoa); $a++)
			{
				$sub = $costogoa[$a]['simpro_tbl_subbidang'];
				if($sub <> $subbidang)
				{				
					$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A),$costogoa[$a]['simpro_tbl_subbidang']);
					$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:H%d', $start_A, $start_A));
					$subbidang = $sub;
					$subtotal = $costogoa[$a]['subtotal'];
					$start_A++;					
				} else 
				{
					$subtotal = $subtotal + $costogoa[$a]['subtotal'];
				}
							
				$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A), $i);
				$this->excel->getActiveSheet()->setCellValue(sprintf('B%d', $start_A), $costogoa[$a]['kode_rap']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('C%d', $start_A), $costogoa[$a]['kd_material']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('D%d', $start_A), $costogoa[$a]['detail_material_nama']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('E%d', $start_A), $costogoa[$a]['detail_material_satuan']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('F%d', $start_A), number_format($costogoa[$a]['total_volume'],2));
				$this->excel->getActiveSheet()->setCellValue(sprintf('G%d', $start_A), number_format($costogoa[$a]['harga'],2));
				$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $start_A), number_format($costogoa[$a]['subtotal'],2));
				
				$nextsub = isset($costogoa[$a+1]['simpro_tbl_subbidang']) ? $costogoa[$a+1]['simpro_tbl_subbidang'] : '';
				if($nextsub <> $sub)
				{
					$start_A++;				
					$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:G%d', $start_A, $start_A));					
					$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A), 'SUBTOTAL');
					$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $start_A), number_format($subtotal,2));
					$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $start_A))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				}
				$i++;
				$start_A++;			
				$lasta = $start_A;
			}
			
			# footer		
			$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:G%d', $lasta, $lasta));		
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta))->getFont()->setBold(true);		
			$this->excel->getActiveSheet()->getStyle(sprintf('H%d', $lasta))->getFont()->setBold(true);		
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $lasta),'TOTAL costogo(A)');
			$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $lasta),$total_costogoa);
			$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:G%d', $lasta+1, $lasta+1));		
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $lasta+1),'PERSENTASE TERHADAP KONTRAK');
			$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $lasta+1),$persen_costogoa);
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta+1))->getFont()->setBold(true);		
			$this->excel->getActiveSheet()->getStyle(sprintf('H%d', $lasta+1))->getFont()->setBold(true);		
			
			# style
			$styleArray = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  )
			);
		  
			$this->excel->getActiveSheet()->getStyle(
				'A11:' . 
				$this->excel->getActiveSheet()->getHighestColumn() . 
				$this->excel->getActiveSheet()->getHighestRow()
			)->applyFromArray($styleArray);
			# end style		
		}
		# data			
		
		# output
		$filename = sprintf('costogoa-%s.xlsx', trim($nama_proyek)); 		
		//header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Type: application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
		header('Content-Disposition: attachment;filename="'.$filename.'"'); 
		header('Cache-Control: max-age=0'); 
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
		$objWriter->save('php://output');
	}

	function excel($page="",$tgl_rab="")
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
			$this->excel->getActiveSheet()->setCellValue('B4', 'Kode Analisa');
			$this->excel->getActiveSheet()->setCellValue('C4', 'Uraian');
			$this->excel->getActiveSheet()->setCellValue('D4', 'Satuan');
			$this->excel->getActiveSheet()->setCellValue('E4', 'Volume');
			$this->excel->getActiveSheet()->setCellValue('F4', 'Harga');
			$this->excel->getActiveSheet()->setCellValue('G4', 'Jumlah');

			switch ($page) {
				case 'costogo':					

					$this->db->query("TRUNCATE TABLE simpro_tmp_print_pekerjaan");

					$q_costogoa = $this->get_data_print($idpro,$tgl_rab);

					$x = 5;

					$tot = 0;
					
					if ($q_costogoa) {
						foreach ($q_costogoa as $row) {
							$kode_tree = $row->kode_tree;
							$kode_analisa = $row->kode_analisa;
							$tree_item = $row->tree_item;
							$tree_satuan = $row->tree_satuan;
							$volume = $row->volume;
							$hrg = $row->hrg;
							$sub = $row->sub;

							$this->excel->getActiveSheet()->setCellValueExplicit('A'.$x, $kode_tree, PHPExcel_Cell_DataType::TYPE_STRING);
							$this->excel->getActiveSheet()->setCellValue('B'.$x, $kode_analisa);
							$this->excel->getActiveSheet()->setCellValue('C'.$x, $tree_item);
							$this->excel->getActiveSheet()->setCellValue('D'.$x, $tree_satuan);
							$this->excel->getActiveSheet()->setCellValue('E'.$x, $volume);
							if (count(explode(".", $kode_tree)) == 1) {
								$this->excel->getActiveSheet()->getStyle('F'.$x)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('G'.$x)->getFont()->setBold(true);

								$tot = $tot + $sub;
							}
							$this->excel->getActiveSheet()->setCellValue('F'.$x, $hrg);
							$this->excel->getActiveSheet()->setCellValue('G'.$x, $sub);

							$x++;
						}
					} 
				break;
				default:
					echo "Gagal";
				break;
			}


			$this->excel->getActiveSheet()->setCellValue('A'.$x, 'Total');
			$this->excel->getActiveSheet()->setCellValue('G'.$x, $tot);

			$this->excel->getActiveSheet()->getStyle('A'.$x.':G'.$x)->getFont()->setBold(true);

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
			$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

			$this->excel->getActiveSheet()->getStyle('E1:G'.$this->excel->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00'); 

			$this->excel->getActiveSheet()->getStyle('A4:G'.$x)->applyFromArray($styleArray);
			unset($styleArray);

			$this->excel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleArray1);
			unset($styleArray1);

			$this->excel->getActiveSheet()->getStyle('C1:C'.$this->excel->getActiveSheet()->getHighestRow())
    		->getAlignment()->setWrapText(true);

			$this->excel->getActiveSheet()->mergeCells('A1:G1');
			$this->excel->getActiveSheet()->mergeCells('A2:G2');
			$this->excel->getActiveSheet()->mergeCells('A'.$x.':F'.$x);

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

	function get_data_print($idpro,$tgl_rab)
	{
		$arr = $this->tree($idpro, $depth=0,$tgl_rab,$param="");

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
					'kode_analisa'  => $row['kode_analisa'],
					'tree_item' => trim($row['tree_item']),
					'tree_satuan' => $row['tree_satuan'],
					'volume' => $row['volume'],
					'hrg' => $row['harga'],
					'sub' => $row['subtotal']
				);

				$this->db->insert('simpro_tmp_print_pekerjaan',$data);
				// fwrite($handle, $dat);			

				if (isset($row['children'])) {
					$this->get_array($row['children']);
				}
			}
		}
	}

	function print_data_analisa($page="",$tgl_rab="")
	{
		$idpro = $this->session->userdata('proyek_id'); 

		if ($page=="") {

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

			$this->excel->getActiveSheet()->mergeCells('A1:F1');
			$this->excel->getActiveSheet()->mergeCells('A2:F2');

			$get_item_pekerjaan = $this->db->query("select 
					a.kode_tree,
					a.tree_item,
					b.kode_analisa,
					c.*
					from 
					simpro_costogo_item_tree a 
					join simpro_costogo_analisa_item_apek b 
					on a.kode_tree = b.kode_tree
					and a.id_proyek = b.id_proyek
					and a.tanggal_kendali = b.tanggal_kendali
					join
					(
					SELECT simpro_tbl_subbidang.subbidang_kode,simpro_tbl_subbidang.subbidang_name,tbl_analisa_satuan.* FROM (
					(
						SELECT 					
							simpro_costogo_analisa_asat.id_analisa_asat,
							simpro_costogo_analisa_asat.kode_analisa,
							(simpro_costogo_analisa_asat.kode_analisa || ' - ' || simpro_costogo_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
							simpro_costogo_analisa_daftar.nama_item,
							simpro_costogo_analisa_daftar.id_satuan,
							simpro_tbl_satuan.satuan_nama,
							simpro_tbl_detail_material.detail_material_kode, 
							simpro_tbl_detail_material.detail_material_nama, 
							simpro_tbl_detail_material.detail_material_satuan,
							simpro_costogo_analisa_asat.harga,
							simpro_costogo_analisa_asat.koefisien,
							(simpro_costogo_analisa_asat.harga * simpro_costogo_analisa_asat.koefisien) AS subtotal
						FROM 
							simpro_costogo_analisa_asat
						LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_costogo_analisa_asat.kode_material
						LEFT JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_asat.kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_asat.id_proyek)
						LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
						WHERE simpro_costogo_analisa_asat.id_proyek= $idpro and simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
						ORDER BY 
							simpro_costogo_analisa_asat.kode_analisa,
							simpro_tbl_detail_material.detail_material_kode				
						ASC
					)
					UNION ALL 
					(
						SELECT 
							simpro_costogo_analisa_apek.id_analisa_apek AS id_analisa_asat,
							simpro_costogo_analisa_apek.parent_kode_analisa as kode_analisa,
							(simpro_costogo_analisa_apek.parent_kode_analisa || ' - ' || simpro_costogo_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
							ad.nama_item AS nama_item,
							simpro_costogo_analisa_daftar.id_satuan,
							simpro_tbl_satuan.satuan_nama,
							simpro_costogo_analisa_apek.kode_analisa AS detail_material_kode,
							ad.nama_item as detail_material_nama,
							simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
							COALESCE(tbl_harga.harga,0) AS harga,
							simpro_costogo_analisa_apek.koefisien,
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
							WHERE id_proyek= $idpro and tanggal_kendali = '$tgl_rab'
							GROUP BY kode_analisa			
						) as tbl_harga ON tbl_harga.kode_analisa = simpro_costogo_analisa_apek.kode_analisa			
						WHERE simpro_costogo_analisa_apek.id_proyek= $idpro and simpro_costogo_analisa_apek.tanggal_kendali = '$tgl_rab'
						ORDER BY 
							simpro_costogo_analisa_apek.parent_kode_analisa,				
							simpro_costogo_analisa_apek.kode_analisa
						ASC					
					)		
					) AS tbl_analisa_satuan
					left join simpro_tbl_subbidang
					on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
					) c
					on c.kode_analisa = b.kode_analisa
					where a.id_proyek = $idpro and a.tanggal_kendali = '$tgl_rab'
					order by a.kode_tree,c.subbidang_kode");

			$x = 3;
			$n = 1;
			$na = 0;

			$item = 4;
			$judul = 5;
			$subbidang_no = 6;

			$kode_tree = '';
			$subbidang = '';

			$jml = 0;

			// var_dump($get_item_pekerjaan->result_object());
			if ($get_item_pekerjaan->result()) {

				$styleArrayBorder = array(
					'borders' => array(
						'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				);

				foreach ($get_item_pekerjaan->result() as $rows) {

					if ($kode_tree != $rows->kode_tree) {
						if ($kode_tree != '') {
							$this->excel->getActiveSheet()->mergeCells('A'.$x.':E'.$x);
							$this->excel->getActiveSheet()->setCellValue('A'.$x, 'TOTAL');
							$this->excel->getActiveSheet()->setCellValue('F'.$x, $jml);

							$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->applyFromArray($styleArrayBorder);
							unset($styleArray);

							$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->getFont()->setBold(true);
						}

						$jml = 0;
						$jml_rab = 0;
						$n = 1;
						$item = $x+2;
						$judul = $x+3;
						$subbidang_no = $x+4;
						$x=$x+5;
					} else {
						if ($subbidang != $rows->subbidang_name) {
							$n = 1;
							$subbidang_no = $x;
							$x=$x+1;
						}
					}

					$kode_tree = $rows->kode_tree;
					$item_pekerjaan = $rows->tree_item;
					$subbidang = $rows->subbidang_name;

					$dm_nama = $rows->detail_material_nama;
					$dm_kode = $rows->detail_material_kode;
					$dm_satuan = $rows->detail_material_satuan;
					$dm_koefisien = $rows->koefisien;
					$dm_harga = $rows->harga;
					$dm_jumlah = $rows->subtotal;

					$this->excel->getActiveSheet()->getStyle('A'.$item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					// $this->excel->getActiveSheet()->getStyle('A'.$subbidang_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A'.$item)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->mergeCells('A'.$item.':F'.$item);
					$this->excel->getActiveSheet()->setCellValue('A'.$item, $kode_tree.' '.$item_pekerjaan);

					$this->excel->getActiveSheet()->mergeCells('A'.$subbidang_no.':F'.$subbidang_no);
					$this->excel->getActiveSheet()->setCellValue('A'.$subbidang_no, $subbidang);
					$this->excel->getActiveSheet()->getStyle('A'.$subbidang_no.':F'.$subbidang_no)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->setCellValue('A'.$judul, 'NO');
					$this->excel->getActiveSheet()->setCellValue('B'.$judul, 'SUMBER DAYA');
					$this->excel->getActiveSheet()->setCellValue('C'.$judul, 'SATUAN');
					$this->excel->getActiveSheet()->setCellValue('D'.$judul, 'HARGA SATUAN');
					$this->excel->getActiveSheet()->setCellValue('E'.$judul, 'KOEFISIEN');
					$this->excel->getActiveSheet()->setCellValue('F'.$judul, 'JUMLAH');
					$this->excel->getActiveSheet()->getStyle('A'.$judul.':F'.$judul)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->setCellValueExplicit('A'.$x, '1.'.$n, PHPExcel_Cell_DataType::TYPE_STRING);
					$this->excel->getActiveSheet()->setCellValue('B'.$x, $dm_nama.'('.$dm_kode.')');
					$this->excel->getActiveSheet()->setCellValue('C'.$x, $dm_satuan);
					$this->excel->getActiveSheet()->setCellValue('D'.$x, $dm_harga);
					$this->excel->getActiveSheet()->setCellValue('E'.$x, $dm_koefisien);
					$this->excel->getActiveSheet()->setCellValue('F'.$x, $dm_jumlah);

					$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$item.':F'.$item)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$subbidang_no.':F'.$subbidang_no)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$judul.':F'.$judul)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					// $this->excel->getActiveSheet()->setCellValue('G'.$x, $kode_tree);
					// $this->excel->getActiveSheet()->setCellValue('H'.$x, $subbidang);

					$x++;
					$n++;
					$na++;
					$jml+=$dm_jumlah;

				}

				$this->excel->getActiveSheet()->mergeCells('A'.$x.':E'.$x);
				$this->excel->getActiveSheet()->setCellValue('A'.$x, 'TOTAL');
				$this->excel->getActiveSheet()->setCellValue('F'.$x, $jml);

				$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->applyFromArray($styleArrayBorder);
				unset($styleArray);

				$this->excel->getActiveSheet()->getStyle('A'.$x.':F'.$x)->getFont()->setBold(true);
			}

			$this->excel->getActiveSheet()->setCellValue('A1', strtoupper($page));
			$this->excel->getActiveSheet()->setCellValue('A2', strtoupper($nama_proyek).' ('.$date.')');			

			$styleArray = array(
			// 'borders' => array(
			//     'allborders' => array(
			//       'style' => PHPExcel_Style_Border::BORDER_THIN
			//     )
			// ),
			  'font'  => array(
		        'size'  => 7,
		        'name'  => 'verdana'
		    )
			);

			$this->excel->getActiveSheet()->getStyle('A1:F'.$x)->applyFromArray($styleArray);
			unset($styleArray);

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

			$this->excel->getActiveSheet()->getStyle('D1:F'.$this->excel->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00'); 


			// $this->excel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleArray1);
			// unset($styleArray1);

			$this->excel->getActiveSheet()->getStyle('B1:B'.$this->excel->getActiveSheet()->getHighestRow())
    		->getAlignment()->setWrapText(true);

			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);

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
}

?>