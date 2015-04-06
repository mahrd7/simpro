<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Rbk_Analisa extends MX_Controller {
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');
		$this->load->model('mdl_rbk');
		$this->load->model('mdl_rbk_analisa');		
		if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");
	}
	
	public function get_asat($idproyek)
	{
		//$idproyek = isset($idproyek) ? $idproyek : $this->get_idproyek();
		$data = $this->mdl_rbk_analisa->get_daftar_analisa_satuan($idproyek);		
		$this->_out($data);
	}
	
	function delete_asat()
	{
		if($this->input->post('id_analisa_asat'))
		{
			if(substr($this->input->post('kode_analisa'),0,2) == 'AN')
			{
				if($this->db->delete('simpro_rap_analisa_apek',array('id_analisa_apek'=>$this->input->post('id_analisa_asat'))))
					echo "Data berhasil dihapus.";
						else echo "Data GAGAL dihapus!";				
			} else
			{
				if($this->db->delete('simpro_rap_analisa_asat',array('id_analisa_asat'=>$this->input->post('id_analisa_asat'))))
					echo "Data berhasil dihapus.";
						else echo "Data GAGAL dihapus!";
			}
		} else echo "Pilih item yang mau dihapus!";
	}
	
	function get_rata($idproyek)
	{
		$data = $this->mdl_rbk_analisa->rap_rapa($idproyek);
		$this->_out($data);		
	}

	function get_total_rapa()
	{
		if($this->input->post('id_proyek'))
		{
			$data = $this->mdl_rbk_analisa->total_rapa($this->input->post('id_proyek'));
			if(isset($data['data']['total_rapa'])) echo number_format($data['data']['total_rapa'],0);
			//$this->_out($data);		
		}
	}

	function get_total_rap()
	{
		if($this->input->post('id_proyek'))
		{
			$data = $this->mdl_rbk_analisa->total_rapa($this->input->post('id_proyek'));
			if(isset($data['data']['total_rapa'])) echo number_format($data['data']['total_rapa'],0);
			//$this->_out($data);		
		}
	}
	
	function edit_koefisien_satuan()
	{	
		if($this->input->post('id_proyek') && $this->input->post('detail_material_kode') 
		&& $this->input->post('kode_analisa') && $this->input->post('koefisien') && $this->input->post('id_analisa_asat')
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
				$this->db->where('id_analisa_asat', $this->input->post('id_analisa_asat'));
				if($this->db->update('simpro_rap_analisa_asat', $update))
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
				$this->db->where('id_analisa_apek', $this->input->post('id_analisa_asat'));
				if($this->db->update('simpro_rap_analisa_apek', $update))
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
	
	function get_parent_kode_tree()
	{
		$parent = @$_SESSION['sess_rap_parent_kode_tree'];
		return $this->session->userdata('rap_parent_kode_tree') <> '' ? $this->session->userdata('rap_parent_kode_tree') : $parent;
	}
	
		
	public function tambah_rap_tree_item($idproyek)
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
				'tree_item' => $this->input->post('tree_item'),
				'tree_parent_kode' => $pkd,
				'tree_satuan' => $this->input->post('tree_satuan'),
				'id_proyek' =>  $this->input->post('id_proyek'),
				'volume' => $this->input->post('volume') <> '' ? $this->input->post('volume') : 0,
				'kode_tree' =>  $new_kt
			);
			//$this->_dump($datainsert);
			$resin = $this->db->insert('simpro_rap_item_tree', $datainsert);
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
		$data = $this->mdl_rbk_analisa->get_detailmaterial_kode($this->input->get('query'));
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

	function paste_tree()	
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
						'id_proyek' => $cp['id_proyek'],
						'tree_item' => $cp['tree_item'],
						'tree_satuan' => $cp['tree_satuan'],
						'volume' => $cp['volume'],
						'kode_tree' => $new_kt,
						'tree_parent_id' => $parentid[0]
					);
					if(!$this->db->insert("simpro_rap_item_tree", $data)) $error=TRUE;
					$i++;
				}
				//$this->_dump($data);
				if(!$error) echo "Data berhasil di-paste";
					else echo "Data GAGAL dipaste!";
			} else echo "Tidak ada data yang di-copy, silahkan pilih item kemudian klik tombol 'Copy'";
		}	
	}

	# extract menu tree json ke array biasa
	function super_unique($array)
	{
	  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
	  foreach ($result as $key => $value)
	  {
		if ( is_array($value) )
		{
		  $result[$key] = $this->super_unique($value);
		}
	  }
	  return $result;
	}	

	function sort_tree($arr)
	{
		foreach($arr as $k=>$v)
		{
			$data[] = $v;
		}
		return $data;
	}
		
	function tree_rab($idpro, $depth=0)
	{
		$this->load->model('rencana/mdl_rencana');
		$this->load->model('rencana/mdl_rencana');
		$result=array();
		$temp=array();
		$temp = $this->mdl_rencana->get_tree_item($idpro, $depth)->result();
		if(count($temp))
		{			
			$i = 0;
			foreach($temp as $row){
				$harga_rab = $this->mdl_rencana->harga_analisa_rab($idpro, $row->kode_analisa);
				$temp_harga = $this->mdl_rencana->get_tree_item_rab($idpro, $row->rat_item_tree)->row_array();				
				$subtotal_rab = $this->mdl_rencana->subtotal_rab($idpro, $row->kode_tree);
				// $temp_harga['volume_rab'] $row->volume_rab
				$sub_rab = ($temp_harga['volume_rab'] * $harga_rab['harga_jadi_rab']);
				$selisih = ($subtotal_rab - ($row->sub)); 
								
				$data[] = array(
					'rat_item_tree' => $row->rat_item_tree,
					'id_proyek_rat' => $row->id_proyek_rat,
					'id_satuan' => $row->id_satuan,
					'kode_tree' => $row->kode_tree,
					'tree_item' => utf8_encode($row->tree_item),
					'tree_satuan' => $row->tree_satuan,
					'kode_analisa' => $row->kode_analisa,
					'volume_rat' => $row->volume,
					'harga_rat' => $row->hrg, 
					'harga_rab' => $harga_rab['harga_jadi_rab'],
					'volume_rab' => $temp_harga['volume_rab'],
					'subtotal_rat' => $row->sub,
					'subtotal_rab' => $subtotal_rab,
					'selisih' => $selisih,
					'tree_parent_id' => $row->tree_parent_id,
				);
				
				if($depth == 0) $data[$i] = array_merge($data[$i], array('expanded' => true));
				
				## check if have a child
				$q = sprintf("SELECT * FROM simpro_rat_item_tree WHERE id_proyek_rat = '%d' AND tree_parent_id = '%d'", $row->id_proyek_rat, $row->rat_item_tree);
				$query = $this->db->query($q);
				$is_child = $query->num_rows();
				if($is_child)
				{				
					$result[] = array_merge(
						$data[$i],
						array(
							'iconCls' => 'task-folder',
							'ishaschild' => 1,
							'children'=>$this->tree_rab($idpro, $row->rat_item_tree)
						)
					);
				} else 
				{
					$result[] = array_merge(
						$data[$i],
						array(
							'iconCls' => 'task-folder',
							'leaf' => true
						)
					);
				}				
				$i++;
			}
		}
		return array_filter($result);
	}
		
	function ext_tree($tree, $cur_key=0, $level=0, &$push_arr=NULL)
	{	
		if(!is_array($push_arr)) $push_arr = array();
		$level++;
		if(is_array($tree))
		{
			foreach($tree as $k=>$v)
			{
				$val_exist = array('kode_tree' => $v['kode_tree']);
				if(!in_array($val_exist, $push_arr))
				{
					$push_arr[] = array(
						'kode_tree' => $v['kode_tree'],
						'uraian' => $v['tree_item'],
						'tree_item' => $v['kode_tree'] . '. '.$v['tree_item'],
						'tree_satuan' => $v['tree_satuan'],
						'volume' => $v['volume'],
						'harga' => number_format($v['harga'],0),
						'subtotal' => number_format($v['subtotal'],0)
					);				
				}
			   if(array_key_exists('ishaschild', $v) && !in_array($val_exist, $push_arr))
			   {
				   foreach($v as $key=>$val)
				   {
					   $this->ext_tree($val, $key, $level, $push_arr);
				   }	   
			   }
			}		
		} else
		{
			//$this->_dump($tree);
		}
	   return $push_arr;
	}

	function ext_tree_rab($tree, $cur_key=0, $level=0, &$push_arr=NULL)
	{	
		if(!is_array($push_arr)) $push_arr = array();
		$level++;
		if(is_array($tree))
		{
			foreach($tree as $k=>$v)
			{
				$val_exist = array('kode_tree' => $v['kode_tree']);
				if(!in_array($val_exist, $push_arr))
				{
					$push_arr[] = array(
						'kode_tree' => $v['kode_tree'],
						'uraian' => $v['tree_item'],
						'tree_item' => $v['kode_tree'] . '. '.$v['tree_item'],
						'tree_satuan' => $v['tree_satuan'],
						'volume_rat' => $v['volume_rat'],
						'harga_rat' => number_format($v['harga_rat'],0),
						'subtotal_rat' => number_format($v['subtotal_rat'],0),
						'volume_rab' => $v['volume_rab'],
						'harga_rab' => number_format($v['harga_rab'],0),
						'subtotal_rab' => number_format($v['subtotal_rab'],0)
					);				
				}
			   if(array_key_exists('ishaschild', $v) && !in_array($val_exist, $push_arr))
			   {
				   foreach($v as $key=>$val)
				   {
					   $this->ext_tree_rab($val, $key, $level, $push_arr);
				   }	   
			   }
			}		
		} else
		{
			//$this->_dump($tree);
		}
	   return $push_arr;
	}
	
	function get_task_tree_items()
	{
		$idpro = $this->session->userdata('proyek_id');
		$q_id_tender = $this->db->query("select id_tender from simpro_tbl_proyek where proyek_id = $idpro");
		if ($q_id_tender->result()) {
			$id_tender = $q_id_tender->row()->id_tender;
		} else {
			$id_tender = 0;
		}

		$q_evaluasi = "with rap as (select 
						x.*, 
						case when right(x.tree_parent_kode,1) = '.' then
						left(x.tree_parent_kode,(length(x.tree_parent_kode)-1))
						else
						x.tree_parent_kode
						end as xnm,
						(select a::int from (select a, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) as row from (select unnest as a from unnest ( string_to_array ( trim ( x.kode_tree, '.'), '.' ) )) x) r order by row desc limit 1) as urut,
						case when sum(totals.subtotal) = 0 or x.volume = 0 then
						0
						else
						sum(totals.subtotal) / x.volume
						end as hrg
						,
						(
						sum(totals.subtotal)
						) as sub
						from
						simpro_rap_item_tree x
						left join
						(SELECT 
							simpro_rap_item_tree.kode_tree,
							simpro_rap_analisa_item_apek.kode_analisa,
							COALESCE(tbl_harga.harga, 0) AS harga,
							(COALESCE(tbl_harga.harga, 0) * simpro_rap_item_tree.volume) as subtotal
						FROM simpro_rap_item_tree 
						LEFT JOIN simpro_rap_analisa_item_apek ON simpro_rap_analisa_item_apek.kode_tree = simpro_rap_item_tree.kode_tree and simpro_rap_analisa_item_apek.id_proyek = $idpro
						LEFT JOIN (
							SELECT 
							DISTINCT ON(kode_analisa)
												kode_analisa,
												SUM(subtotal) AS harga
							FROM (
							(
								SELECT 					
									(simpro_rap_analisa_asat.kode_analisa) AS kode_analisa, 
									(simpro_rap_analisa_asat.harga * simpro_rap_analisa_asat.koefisien) AS subtotal
								FROM 
									simpro_rap_analisa_asat
								LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
								LEFT JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_asat.kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_asat.id_proyek)
								LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
								WHERE simpro_rap_analisa_asat.id_proyek= $idpro
								ORDER BY 
									simpro_rap_analisa_asat.kode_analisa,
									simpro_tbl_detail_material.detail_material_kode				
								ASC
							)
							UNION ALL 
							(
								SELECT 
									(simpro_rap_analisa_apek.parent_kode_analisa) AS kode_analisa, 
									COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
								FROM 
									simpro_rap_analisa_apek
								INNER JOIN simpro_rap_analisa_daftar ad ON (ad.kode_analisa = simpro_rap_analisa_apek.kode_analisa AND ad.id_proyek = simpro_rap_analisa_apek.id_proyek)
								INNER JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_apek.parent_kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_apek.id_proyek)			
								INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
								LEFT JOIN (
									SELECT 
										DISTINCT ON(kode_analisa)
										kode_analisa,
										SUM(harga * koefisien) AS harga
									FROM simpro_rap_analisa_asat 
									WHERE id_proyek= $idpro
									
									GROUP BY kode_analisa			
								) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_apek.kode_analisa			
								WHERE simpro_rap_analisa_apek.id_proyek= $idpro
								
								ORDER BY 
									simpro_rap_analisa_apek.parent_kode_analisa,				
									simpro_rap_analisa_apek.kode_analisa
								ASC					
							)		
							) AS tbl_analisa_satuan
							GROUP BY kode_analisa				
						) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_item_apek.kode_analisa						
						WHERE simpro_rap_item_tree.id_proyek = $idpro
						ORDER BY simpro_rap_item_tree.kode_tree ASC) as totals
						on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
						WHERE x.id_proyek = $idpro
						group by x.rap_item_tree			
						ORDER BY xnm, urut),
			rab as (
			select 
						x.tahap_volume_kendali as volume,
						'1.' || x.tahap_kode_kendali as kode_tree,
						coalesce((case when sum(totals.subtotal) = 0 or x.tahap_volume_kendali = 0 then
						0
						else
						sum(totals.subtotal) / x.tahap_volume_kendali
						end),0) as hrg
						,
						coalesce((
						sum(totals.subtotal)
						),0) as sub
						from
						simpro_tbl_input_kontrak x
						left join
						(select
						tahap_kode_kendali,
						(tahap_volume_kendali * tahap_harga_satuan_kendali) as subtotal
						from
						simpro_tbl_input_kontrak
						where proyek_id = $idpro) as totals
						on left(totals.tahap_kode_kendali,length(x.tahap_kode_kendali)) = x.tahap_kode_kendali
						WHERE x.proyek_id = $idpro
						group by x.input_kontrak_id
			),
			rat as (
			select 
						x.volume,
						'1.' || x.kode_tree as kd,
						case when sum(totals.subtotal) = 0 or x.volume = 0 then
						0
						else
						sum(totals.subtotal) / x.volume
						end as hrg
						,
						(
						sum(totals.subtotal)
						) as sub
						from
						simpro_rat_item_tree x
						left join
						(SELECT 
							simpro_rat_item_tree.kode_tree,
							simpro_rat_analisa_item_apek.kode_analisa,
							COALESCE(tbl_harga.harga, 0) AS harga,
							(COALESCE(tbl_harga.harga, 0) * simpro_rat_item_tree.volume) as subtotal
						FROM simpro_rat_item_tree 
						LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.kode_tree = simpro_rat_item_tree.kode_tree and simpro_rat_analisa_item_apek.id_proyek_rat = $id_tender
						LEFT JOIN (
							SELECT 
							DISTINCT ON(kode_analisa)
												kode_analisa,
												SUM(subtotal) AS harga
							FROM (
							(
								SELECT 					
									(simpro_rat_analisa_asat.kode_analisa) AS kode_analisa, 
									(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
								FROM 
									simpro_rat_analisa_asat
								LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
								LEFT JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender= simpro_rat_analisa_asat.id_tender)
								LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
								WHERE simpro_rat_analisa_asat.id_tender= $id_tender
								ORDER BY 
									simpro_rat_analisa_asat.kode_analisa,
									simpro_tbl_detail_material.detail_material_kode				
								ASC
							)
							UNION ALL 
							(
								SELECT 
									(simpro_rat_analisa_apek.parent_kode_analisa) AS kode_analisa, 
									COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
								FROM 
									simpro_rat_analisa_apek
								INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
								INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender= simpro_rat_analisa_apek.id_tender)			
								INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
								LEFT JOIN (
									SELECT 
										DISTINCT ON(kode_analisa)
										kode_analisa,
										SUM(harga * koefisien) AS harga
									FROM simpro_rat_analisa_asat 
									WHERE id_tender= $id_tender
									
									GROUP BY kode_analisa			
								) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
								WHERE simpro_rat_analisa_apek.id_tender= $id_tender
								
								ORDER BY 
									simpro_rat_analisa_apek.parent_kode_analisa,				
									simpro_rat_analisa_apek.kode_analisa
								ASC					
							)		
							) AS tbl_analisa_satuan
							GROUP BY kode_analisa				
						) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_item_apek.kode_analisa						
						WHERE simpro_rat_item_tree.id_proyek_rat = $id_tender 
						ORDER BY simpro_rat_item_tree.kode_tree ASC) as totals
						on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
						WHERE x.id_proyek_rat = $id_tender 
						group by x.rat_item_tree
			)
			select 
			rap.rap_item_tree as rap_rap_tree_item,
						rap.id_proyek as rap_id_proyek,
						rap.kode_tree as kode_tree,
						rap.tree_item as tree_item,
						rap.tree_satuan as tree_satuan,
						rap.tree_parent_id as rap_tree_parent_id,
						rap.volume as volume,
						rap.tree_parent_kode as rap_tree_parent_kode,
						rap.hrg as harga,
						rap.sub as subtotal,
						rab.kode_tree as rab_kode_tree,
						rab.volume as volume_rab,
						rab.hrg as harga_rab,
						rab.sub as subtotal_rab,
						rat.kd as rat_kode_tree,
						rat.volume as volume_rat,
						rat.hrg as harga_rat,
						rat.sub as subtotal_rat
			from rap left join rab on rap.kode_tree = rab.kode_tree left join rat on rap.kode_tree=rat.kd order by rap.kode_tree";


		$qr_evaluasi = $this->db->query($q_evaluasi);

		if ($qr_evaluasi->result()) {
			$data = $qr_evaluasi->result_object();
			$total = $qr_evaluasi->num_rows();
		} else {
			$data = "";
			$total = 0;
		}
		# rap
		// $arr = $this->tree($this->session->userdata('proyek_id'), $depth=0,$param="");
		// $hasil = $this->ext_tree($arr);			
		// $data_rap = $this->sort_tree($this->super_unique($hasil));				
		
		// # rat_rab
		// $get_id_tender = $this->db->query(sprintf("SELECT id_proyek_rat FROM simpro_m_rat_proyek_tender WHERE proyek_id = %d", $this->session->userdata('proyek_id')))->row_array();
		// $rat = $this->tree_rab($get_id_tender['id_proyek_rat']);
		// $hasil_rat = $this->ext_tree_rab($rat);			
		// $data_rat = $this->sort_tree($this->super_unique($hasil_rat));		
		
		// $data = array_merge($data_rat, $data_rap);
		echo json_encode(array('data'=>$data, 'total'=>count($total)));
		//$this->_dump($data_rat);
	}
	# end extract 
	

	function get_task_tree_item($idpro)
	{
		$param = $this->input->get('param');
		$arr = $this->tree($idpro, $depth=0,$param);
		echo json_encode(array('text'=>'.', 'children'=>$arr));
		// $data = array('text'=>'.', 'children'=>$arr);
		// var_dump($data);
	}
	
	function tree($idpro, $depth=0,$param)
	{
		$result=array();
		$temp=array();
		$temp = $this->mdl_rbk_analisa->get_tree_item($idpro, $depth, $param)->result();
		if(count($temp))
		{			
			$i = 0;
			$n = 1;
			foreach($temp as $row){
				$kode_tree = $row->kode_tree;

				if ($row->tree_parent_id) {
					$tree_kode_parent = $this->db->query("select kode_tree from simpro_rap_item_tree where rap_item_tree = $row->tree_parent_id")->row()->kode_tree;
				} else {
					$tree_kode_parent = "";
				}

				if ($row->tree_parent_kode.'.'.$n <> $row->kode_tree && strlen($row->tree_parent_kode) <> 0 || $tree_kode_parent <> $row->tree_parent_kode) {
					if ($tree_kode_parent == null || $tree_kode_parent == '') {
						$kode_tree = $n;
					} else {
						$kode_tree = $tree_kode_parent.'.'.$n;
					}

					// $data_del = "delete from simpro_rap_item_tree where kode_tree = '$row->kode_tree' and id_proyek = $idpro";
					// $this->db->query($data_del);

					$data_up_ap = "update simpro_rap_analisa_item_apek set kode_tree = '$kode_tree' 
					where kode_tree = '$row->kode_tree' and id_proyek = $idpro";
					$this->db->query($data_up_ap);

					$data_up = "update simpro_rap_item_tree set kode_tree = '$kode_tree',
					tree_parent_kode = '$tree_kode_parent' 
					where rap_item_tree = $row->rap_item_tree";
					$this->db->query($data_up);
				}

				//$temp_harga = $this->mdl_rbk_analisa->get_tree_item_harga($idpro, $row->rap_item_tree)->row_array();
				$data[] = array(
					'rap_item_tree' => $row->rap_item_tree,
					'id_proyek' => $row->id_proyek,
					'id_satuan' => $row->id_satuan,
					'kode_tree' => $kode_tree,
					'kode_analisa' => $row->kode_analisa,
					'tree_item' => utf8_encode($row->tree_item),
					'tree_satuan' => $row->tree_satuan,
					'volume' => $row->volume,
					'harga' => $row->hrg,
					'subtotal' => $row->sub,
					'tree_parent_id' => $row->tree_parent_id,
					'kdt' => $row->tree_parent_kode.'.'.$n
					/* 
					'harga' => $temp_harga['harga'], 
					'subtotal' => $temp_harga['subtotal'],
					*/
				);
				
				if($depth == 0) $data[$i] = array_merge($data[$i], array('expanded' => true));
				
				## check if have a child
				$q = sprintf("SELECT * FROM simpro_rap_item_tree WHERE id_proyek = '%d' AND tree_parent_id = '%d'", $row->id_proyek, $row->rap_item_tree);
				$query = $this->db->query($q);
				$is_child = $query->num_rows();
				if($is_child)
				{			
						$result[] = array_merge(
							$data[$i],
							array(
								'iconCls' => 'task-folder',
								'ishaschild' => 1,
								'children'=>$this->tree($idpro, $row->rap_item_tree, $param)
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
		
	function gen_kode_tree($proyekid, $parentid, $kode_tree)
	{
		$sql_tree = sprintf("
					SELECT 
						kode_tree 
					FROM simpro_rap_item_tree 
					WHERE 
						id_proyek = '%d' 
						AND tree_parent_id = '%d'
					ORDER BY rap_item_tree DESC", 
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
	
	function del_tree_item()
	{
		if($this->input->post('tree_item_id'))
		{	
			$qry2 = $this->db->query(sprintf("DELETE FROM simpro_rap_analisa_item_apek WHERE kode_tree = (SELECT kode_tree from simpro_rap_item_tree WHERE rap_item_tree = '%d')", $this->input->post('tree_item_id')));
			$qry = $this->db->query(sprintf("DELETE FROM simpro_rap_item_tree WHERE rap_item_tree = '%d'", $this->input->post('tree_item_id')));
			if($qry && $qry2) echo json_encode(array("success"=>true, "message"=>"Data berhasil dihapus!"));
		}
	}
	
	function update_tree_item()
	{		
		if($this->input->post('kode_tree') && $this->input->post('tree_item') && $this->input->post('satuan_id') 
		&& $this->input->post('id_proyek') && $this->input->post('rap_item_tree'))
		{
			$du = array(
				'rap_item_tree' => $this->input->post('rap_item_tree'),
				'tree_parent_id' => $this->input->post('tree_parent_id'),
				'id_proyek' => $this->input->post('id_proyek'),
				'kode_tree' => $this->input->post('kode_tree'),
				'tree_satuan' => $this->input->post('satuan_id'),
				'tree_item' => $this->input->post('tree_item'),
				'volume' => $this->input->post('volume')
			);
			$this->db->where('rap_item_tree', $this->input->post('rap_item_tree'));
			$this->db->where('id_proyek', $this->input->post('id_proyek'));
			$this->db->where('tree_parent_id', $this->input->post('tree_parent_id'));
			if($this->db->update('simpro_rap_item_tree', $du)) echo "Data berhasil diupdate";
				else echo "Data GAGAL diupdate!";
		}
	}
		
	function get_parent_tree_id()
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
	
	function del_analisa_satuan_item()
	{
		if($this->input->post('tree_item_id'))
		{
			$ratid = $this->input->post('tree_item_id');
			// $query = sprintf("
			// 	DELETE FROM simpro_rap_analisa_asat WHERE kode_analisa = 
			// 	(
			// 		SELECT kode_analisa FROM simpro_rap_analisa_item_apek
			// 		WHERE rap_item_tree = '%d'
			// 	);

			// 	DELETE FROM simpro_rap_analisa_apek where kode_analisa = 
			// 	(
			// 		SELECT kode_analisa FROM simpro_rap_analisa_item_apek
			// 		WHERE rap_item_tree = '%d'
			// 	);			
				
			// 	DELETE FROM simpro_rap_analisa_item_apek WHERE rap_item_tree = '%d';				
			// ", $ratid, $ratid, $ratid);	
			$qry2 = $this->db->query(sprintf("DELETE FROM simpro_rap_analisa_item_apek WHERE kode_tree = (SELECT kode_tree from simpro_rap_item_tree WHERE rap_item_tree = '%d')", $ratid));
				
			if($qry2) echo "Data Analisa satuan berhasil dihapus.";
				else echo "Data Analisa satuan GAGAL dihapus!";
		} 
	}
	
	public function get_daftar_analisa($id)
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
		$data = $this->mdl_rbk_analisa->get_daftar_analisa_pekerjaan($idproyek, $idkat);
		$this->_out($data);
	}
	
	function tambah_daftar_analisa()
	{
		if($this->input->post('id_proyek') && $this->input->post('kode_analisa'))
		{
			$is_analisa_exists = $this->db->query(sprintf("SELECT * FROM simpro_rap_analisa_daftar WHERE kode_analisa ='%s' AND id_proyek = '%d'", $this->input->post('kode_analisa'), $this->input->post('id_proyek')))->num_rows();
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
				FROM simpro_rap_analisa_daftar
				WHERE id_proyek = '%d'", $this->input->post('id_proyek'));
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
				'id_satuan' => $satuan
			);			
			
			if($is_analisa_exists)
			{
				$wdu = array(
					'id_proyek'=>$this->input->post('id_proyek'),
					'kode_analisa'=>$this->input->post('kode_analisa')
				);
				$this->db->update('simpro_rap_analisa_daftar', $data, $wdu);			
				echo "Data berhasil diupdate.";
			} else
			{				
				$this->db->insert('simpro_rap_analisa_daftar',$data);
				echo "Data berhasil ditambah.";
			}		
		} else 
		{
			echo "Update daftar Analisa GAGAL!";
		}
	}
	
	function edit_harga_satuan_asat($idproyek)
	{
		$this->update_kode_rap($idproyek);
		$idproyek = isset($idproyek) ? $idproyek : $this->get_idproyek();	
		$data = $this->mdl_rbk_analisa->get_harga_satuan_asat($idproyek);		
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
	
	function update_harga_asat()
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
			if($this->db->update('simpro_rap_analisa_asat', $postdata))
			{
				if($this->update_kode_rap($this->input->post('id_proyek'))) echo "Harga berhasil diupdate.";
					else echo "GAGAL update kode RAP";
			} else echo "Harga satuan GAGAL diupdate!";
		}
	}
	
	function delete_tree_item()
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
	
	private function delete_tree_recursive($id_proyek, $id_tree)
	{				
		$q = sprintf("SELECT * FROM simpro_rap_item_tree WHERE id_proyek = '%d' AND tree_parent_id = '%d'", $id_proyek, $id_tree);
		$query = $this->db->query($q);
		$is_data = $query->result_array();
		if(count($is_data) > 0)
		{				
			foreach($is_data as $k=>$v)
			{
				$this->delete_tree_recursive($id_proyek, $v['rap_item_tree']);
			}
		} else
		{
			//rap_item_tree
			$query = sprintf("				
				DELETE FROM simpro_rap_analisa_item_apek WHERE kode_tree = (select kode_tree from simpro_rap_item_tree where rap_item_tree = '%d');				
			", $id_tree);
			$qry_2 = $this->db->query($query);
			if($this->db->delete("simpro_rap_item_tree",array("rap_item_tree"=>$id_tree)) && $qry_2) return true;
				else return false;
		}
	}
	
	function upload_daftar_analisa()
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
				$this->db->query("SET CLIENT_ENCODING TO 'UTF8'");				
				$sql = sprintf("COPY simpro_tmp_upload_daftar_analisa FROM '%s' DELIMITER ',' CSV HEADER", realpath("./uploads/".$data['file_name']));
				// cek dulu ada yang duplikat nggak ? mana saja yang duplikat?
				if($this->db->query($sql))
				{
					$qdel = sprintf("DELETE FROM simpro_rap_analisa_daftar WHERE id_proyek = '%d'",$this->input->post('id_proyek'));
					$this->db->query($qdel);
					$sql_insert = sprintf("
						INSERT INTO simpro_rap_analisa_daftar(kode_analisa, nama_item, id_kat_analisa, id_satuan, id_proyek)
						(
							SELECT 
							kode_analisa, 
							nama_item, 
							'%d' AS id_kat_analisa,
							case when (select count('a') as count from simpro_tbl_satuan where trim(lower(satuan_nama)) = trim(lower(simpro_tmp_upload_daftar_analisa.id_satuan))) > 0 
							then (select satuan_id from simpro_tbl_satuan where trim(lower(satuan_nama)) = trim(lower(simpro_tmp_upload_daftar_analisa.id_satuan)))
							else
							31
							end
							AS id_satuan,
							'%d' AS id_tender 
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
	
	function delete_analisa_pekerjaan()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_proyek'))
		{
			$where = array('kode_analisa'=>$this->input->post('kode_analisa'), 'id_proyek'=>$this->input->post('id_proyek'));
			$del_daftar = $this->db->delete('simpro_rap_analisa_daftar', $where);
			$del_asat = $this->db->delete('simpro_rap_analisa_asat', $where);
			$del_apek = $this->db->delete('simpro_rap_analisa_apek', array('parent_kode_analisa'=>$this->input->post('kode_analisa'), 'id_proyek'=>$this->input->post('id_proyek')));
			if($del_daftar && $del_asat && $del_apek)
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus!";
			
		}
	}	
	
	function delete_asat_apek()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_proyek'))
		{
			$del_asat = $this->db->delete('simpro_rap_analisa_asat', array('kode_analisa'=>$this->input->post('kode_analisa'), 'id_proyek'=>$this->input->post('id_proyek')));
			$del_apek = $this->db->delete('simpro_rap_analisa_apek', array('parent_kode_analisa'=>$this->input->post('kode_analisa'), 'id_proyek'=>$this->input->post('id_proyek')));
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
	
	function get_data_analisa_pekerjaan($idtender)
	{
		$data = $this->mdl_rbk_analisa->get_data_analisa_pekerjaan($idtender);
		$this->_out($data);	
	}

	function get_data_analisa_pekerjaan_copy()
	{
		$id = $this->input->get('id');
		$data = $this->mdl_rbk_analisa->get_data_analisa_pekerjaan_copy($id);
		$this->_out($data);	
	}
	
	function edit_koefisien_apek()
	{
		if($this->input->post('id_analisa_apek'))
		{
			$this->db->where('id_analisa_apek', $this->input->post('id_analisa_apek'));
			if($this->db->update('simpro_rap_analisa_apek', array('koefisien'=>$this->input->post('koefisien'))))
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
			$data = $this->mdl_rbk_analisa->get_ansat($this->input->get('subbidang_kode'));
		} else $data = $this->mdl_rbk_analisa->get_ansat('');
		$this->_out($data);	
	}
	
		
	function tambah_ansat()
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
					from simpro_rap_analisa_asat 
					join simpro_tbl_detail_material 
					on simpro_rap_analisa_asat.kode_material = simpro_tbl_detail_material.detail_material_kode 
					where simpro_rap_analisa_asat.kode_material = '$v' 
					and simpro_rap_analisa_asat.id_proyek = $var_analisa[id_proyek] 
					and simpro_rap_analisa_asat.id_data_analisa = $var_analisa[id_data_analisa]");
				
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
						'harga' => 0
					);	
					$this->db->insert('simpro_rap_analisa_asat',$data);
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
			
			// if($this->db->insert_batch('simpro_rap_analisa_asat',$data)) 
			// {
				if($this->update_kode_rap($this->input->post('id_proyek'))) echo $m;
					else echo "GAGAL update kode RAP";
			// } else echo "Data GAGAL disimpan!";
		}
	}
	
	function update_kode_rap($id)
	{
		$error = FALSE;
		$sql_kode = "
			SELECT kode_kat FROM simpro_rat_analisa_kategori
			WHERE (subbidang_kode <> '' OR subbidang_kode IS NOT NULL)
			ORDER BY kode_kat ASC		
		";
		$krap = $this->db->query($sql_kode)->result_array();
		foreach($krap as $k=>$v)
		{
			// $sql = sprintf("
			// 	SELECT 
			// 		DISTINCT(simpro_rap_analisa_asat.kode_material) as kode_material,
			// 		LEFT(simpro_rap_analisa_asat.kode_material,3),
			// 		simpro_rap_analisa_asat.id_analisa_asat,
			// 		simpro_rat_analisa_kategori.kode_kat,
			// 		COALESCE(simpro_rap_analisa_asat.harga, 0) AS harga
			// 	FROM simpro_rap_analisa_asat
			// 	LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_rap_analisa_asat.kode_material,3)
			// 	WHERE simpro_rap_analisa_asat.id_proyek = %d
			// 		AND simpro_rat_analisa_kategori.kode_kat = '%s'
			// 	GROUP BY simpro_rap_analisa_asat.kode_material, 
			// 		simpro_rat_analisa_kategori.kode_kat, 
			// 		simpro_rap_analisa_asat.harga,
			// 		simpro_rap_analisa_asat.id_analisa_asat
			// 	ORDER BY simpro_rap_analisa_asat.kode_material ASC			
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
			// 		$this->db->where('id_analisa_asat', $v['id_analisa_asat']);
			// 		if(!$this->db->update('simpro_rap_analisa_asat', $updata)) $error = TRUE;					
			// 		/* end update data */
			// 		$i++;
			// 	}
			// }	

			$kd_kat = $v['kode_kat'];
			$sql_kode_null = "				select *,
				case when keterangan isnull
				then (select keterangan from simpro_rap_analisa_asat where kode_material = analisa.kode_material and id_proyek = analisa.id_proyek and keterangan is not null group by keterangan)
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
				when (kode_rap isnull) and (select count(kode_rap) from simpro_rap_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek) = 0
				then (ROW_NUMBER() OVER (ORDER BY 'a')) - coalesce((select count(kode_rap) from simpro_rap_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek),0) + coalesce((select (right(kode_rap,4)::int) as int_rap from simpro_rap_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek order by int_rap desc limit 1),0)
				when (kode_rap isnull) and (select count(kode_rap) from simpro_rap_analisa_asat where kode_rap is not null and id_proyek = analisa_asat.id_proyek) > 0
				then (ROW_NUMBER() OVER (ORDER BY 'a'))
				end::text as kd_rap_int
				from (SELECT 
					simpro_rap_analisa_asat.kode_material as kode_material,
					simpro_rap_analisa_asat.id_proyek,
					simpro_rap_analisa_asat.keterangan,
					case when (select count(a.kode_material) from simpro_rap_analisa_asat a where a.kode_material = simpro_rap_analisa_asat.kode_material and a.id_proyek = id_proyek) > 1
					then right((select b.kode_rap from simpro_rap_analisa_asat b where b.kode_material = simpro_rap_analisa_asat.kode_material and b.id_proyek = simpro_rap_analisa_asat.id_proyek and b.kode_rap is not null group by b.kode_rap),4)
					else right(kode_rap,4)
					end kode_rap_numb,
					case when (select count(a.kode_material) from simpro_rap_analisa_asat a where a.kode_material = simpro_rap_analisa_asat.kode_material and a.id_proyek = id_proyek) > 1
					then (select b.kode_rap from simpro_rap_analisa_asat b where b.kode_material = simpro_rap_analisa_asat.kode_material and b.id_proyek = simpro_rap_analisa_asat.id_proyek and b.kode_rap is not null group by b.kode_rap)
					else kode_rap
					end kode_rap,
					simpro_rat_analisa_kategori.kode_kat
				FROM simpro_rap_analisa_asat
				JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_rap_analisa_asat.kode_material,3)
				WHERE simpro_rap_analisa_asat.id_proyek = $id
					AND simpro_rat_analisa_kategori.kode_kat = '$kd_kat'
				) analisa_asat
				GROUP BY kode_material, 
					keterangan,
					kode_kat,
					kode_rap,
					kode_rap_numb,
					id_proyek
				order by kode_rap) analisa";

			$q_cek_material = $this->db->query($sql_kode_null);

			if ($q_cek_material->result()) {
				foreach ($q_cek_material->result() as $r) {
					$updata = array('kode_rap'=>$r->rap_kd,'keterangan'=>$r->keterangan_ad);
					$this->db->where(array('kode_material' => $r->kode_material,'id_proyek' => $r->id_proyek));
					if(!$this->db->update('simpro_rap_analisa_asat', $updata)) $error = TRUE;
				}
			}		
		}
		if(!$error) return true;
			else return false;
	}
	
	function tambah_apek()
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
					'id_proyek' => $this->input->post('id_proyek')
				);
				if ($this->db->get_where('simpro_rap_analisa_apek',$var_w)->num_rows() > 0) {
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
						'harga' => $harga[$i]
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
							if($this->db->insert_batch('simpro_rap_analisa_apek',$data)) echo "Data telah disimpan.";
							else echo "Data GAGAL disimpan!";
						}						
					} else echo "Error: Maksimal dua jenjang level analisa, sistem tidak memperbolehkan melebihi 2 jenjang analisa.";
				} else echo "Tidak boleh memilih kode analisa yang sama!";
			} else echo "Data GAGAL disimpan, parent kode analia Kosong!";
		} else echo "Error - Post Data inComplete!";
	}
		
	function set_rap_item_tree()
	{
		if($this->input->post('id_proyek') && $this->input->post('rap_item_tree') && $this->input->post('kode_tree'))
		{
			/* CI session problem, so using native PHP session */
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
	
	function tambah_apek_rap()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_proyek'))
		{
			$is_data = 0;
			$var_item = $this->get_rap_item_tree();			
			# cek data
			$q_cek = sprintf("SELECT * FROM simpro_rap_analisa_item_apek WHERE rap_item_tree='%d' AND id_proyek='%d'",
							$var_item['rap_item_tree'], $this->input->post('id_proyek')
							);						
			$is_data = $this->db->query($q_cek)->num_rows();

			$data = array(
				'id_proyek' => $this->input->post('id_proyek'),
				'id_data_analisa' => $this->input->post('id_data_analisa'),
				'kode_analisa' => $this->input->post('kode_analisa'),
				'harga' => $this->input->post('harga_satuan'),
				'rap_item_tree' => $var_item['rap_item_tree'],
				'kode_tree' => $var_item['kode_tree']
			);			
						
			if($is_data > 0)
			{			
				$this->db->where('id_proyek', $this->input->post('id_proyek'));
				$this->db->where('rap_item_tree', $var_item['rap_item_tree']);
				if($this->db->update('simpro_rap_analisa_item_apek', $data)) echo "Data telah diupdate.";
					else echo "Data GAGAL diupdate!";
					
			} else 
			{
				if($this->db->insert('simpro_rap_analisa_item_apek',$data)) echo "Data telah disimpan.";
					else echo "Data GAGAL disimpan!";
			}			
		}
	}
	
	private function cek_level_apek($kd_analisa)
	{
		$q_cek_level = sprintf("SELECT * FROM simpro_rap_analisa_asat WHERE kode_analisa = '%s'", $kd_analisa);
		$isdata = $this->db->query($q_cek_level)->num_rows();
		if($isdata >= 1) return TRUE;
			else return FALSE;
	}
	
	public function delete_apek()
	{
		if($this->input->post('id_analisa_apek'))
		{
			if($this->db->delete('simpro_rap_analisa_apek', array('id_analisa_apek'=>$this->input->post('id_analisa_apek'))))
				echo "Data berhasil dihapus.";
					else echo "Data GAGAL dihapus!";
		}
	}
	
	function delete_ansat()
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
					if($this->db->delete('simpro_rap_analisa_apek',array('id_analisa_apek'=>$v)))
						$success = TRUE;
							else $success = FALSE;
				} else
				{
					if($this->db->delete('simpro_rap_analisa_asat',array('id_analisa_asat'=>$v)))
						$success = TRUE;
							else $success = FALSE;
				}
				$i++;
			}
			if($success) echo "Data Analisa Satuan berhasil dihapus.";
				else echo "Data analisa satuan GAGAL dihapus!";
		} else echo "Pilih item analisa yang mau dihapus!";
	}

	function delete_analisa()
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
					'id_proyek' => $idtender
				);			
				$id_item_apek = $this->db->delete('simpro_rap_analisa_item_apek',$arr_w);

				$where = array('kode_analisa'=>$v, 'id_proyek'=>$idtender);
				$del_daftar = $this->db->delete('simpro_rap_analisa_daftar', $where);
				$del_asat = $this->db->delete('simpro_rap_analisa_asat', $where);
				$del_asat_apek = $this->db->delete('simpro_rap_analisa_apek', $where);
				$del_apek = $this->db->delete('simpro_rap_analisa_apek', array('parent_kode_analisa'=>$v, 'id_proyek'=>$idtender));
				if($del_daftar && $del_asat && $del_apek && $id_item_apek)
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
		
	function paste_asat()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_proyek'))
		{
			if(isset($_SESSION['copy_asat']['kode_artikel']))
			{
				$art = explode(",", $_SESSION['copy_asat']['kode_artikel']);
				//$idart = explode(",",$_SESSION['copy_asat']['id_artikel']);
				$koef = explode(",",$_SESSION['copy_asat']['koefisien']);
				$harga = explode(",",$_SESSION['copy_asat']['harga']);
				$id_tender = $_SESSION['copy_asat']['id_proyek'];
				
				# data analisa
				$id_analisa = explode(",", $this->input->post('id_data_analisa'));
				$kode_analisa = explode(",", $this->input->post('kode_analisa'));
								
				$a=0;
				foreach($id_analisa as $j=>$n)
				{
					$i=0;
					foreach($art as $k=>$v)
					{
						$data[] = array(
							'kode_material' => $v,
							//'id_detail_material' => $idart[$i],
							'id_data_analisa' => $n,
							'kode_analisa' => $kode_analisa[$a],
							'id_proyek' => $id_tender,
							'koefisien' => $koef[$i],
							'harga' => $harga[$i]
						);			
						$i++;
					}
					$a++;
				}		
				
				# insert
				if($this->db->insert_batch('simpro_rap_analisa_asat',$data)) 
				{
					if($this->update_kode_rap($this->input->post('id_proyek'))) echo "Data telah disimpan.";
						else echo "GAGAL update kode RAP";
				} else echo "Data GAGAL disimpan!";
			}
		}
	}
	
	function copy_asat()
	{
		if($this->input->post('kode_artikel') && $this->input->post('koefisien') && $this->input->post('harga'))
		{
			$this->unset_copy_asat();
			$_SESSION['copy_asat']['kode_artikel'] = $this->input->post('kode_artikel');
			$_SESSION['copy_asat']['id_artikel'] = $this->input->post('id_artikel');
			$_SESSION['copy_asat']['koefisien'] = $this->input->post('koefisien');
			$_SESSION['copy_asat']['harga'] = $this->input->post('harga');
			$_SESSION['copy_asat']['id_proyek'] = $this->input->post('id_proyek');
		}	
	}
	
	function unset_copy_asat()
	{
		unset($_SESSION['copy_asat']);
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
	
	function total_rapa()
	{
		$data = $this->mdl_rbk_analisa->total_rapa($this->session->userdata('proyek_id'));
		if(isset($data['data']['total_rapa'])) return $data['data']['total_rapa'];
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
	
	function cetak_rapa()
	{
		$rapa = $this->mdl_rbk_analisa->rap_rapa($this->session->userdata('proyek_id'));
		$dp = $this->get_data_proyek_byid();
		$data = array(
			'idproyek' => $this->session->userdata('proyek_id'),
			'data_proyek' => $dp['data'],
			'rapa' => $rapa['data'],
			'total_rapa' => $this->total_rapa(),
			'persen_rapa' => 00 //$this->persen_rapa($id)			
		);
		$this->load->view('print_rapa', $data);		
	}
	
	function rapa_to_xls()
	{
		$this->load->library('excel');
		$dp = $this->get_data_proyek_byid();
		$rapas = $this->mdl_rbk_analisa->rap_rapa($this->session->userdata('proyek_id'));		
		$rapa = $rapas['data'];
		$total_rapa = $this->total_rapa();
		$persen_rapa = 0; //$this->persen_rata($idtender);
		$data = array(
			'data_proyek' => $dp['data']
		);	
		
		$nama_proyek = $data['data_proyek']['proyek'];
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('rapa');
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
		if(count($rapa) > 0)
		{
			$i = 1;
			$subbidang = "";
			$subtotal = 0;
			$nextsub = "";
			$start_A = 12;
			for($a=0; $a < count($rapa); $a++)
			{
				$sub = $rapa[$a]['simpro_tbl_subbidang'];
				if($sub <> $subbidang)
				{				
					$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A),$rapa[$a]['simpro_tbl_subbidang']);
					$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:H%d', $start_A, $start_A));
					$subbidang = $sub;
					$subtotal = $rapa[$a]['subtotal'];
					$start_A++;					
				} else 
				{
					$subtotal = $subtotal + $rapa[$a]['subtotal'];
				}
							
				$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A), $i);
				$this->excel->getActiveSheet()->setCellValue(sprintf('B%d', $start_A), $rapa[$a]['kode_rap']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('C%d', $start_A), $rapa[$a]['kd_material']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('D%d', $start_A), $rapa[$a]['detail_material_nama']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('E%d', $start_A), $rapa[$a]['detail_material_satuan']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('F%d', $start_A), number_format($rapa[$a]['total_volume'],2));
				$this->excel->getActiveSheet()->setCellValue(sprintf('G%d', $start_A), number_format($rapa[$a]['harga'],2));
				$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $start_A), number_format($rapa[$a]['subtotal'],2));
				
				$nextsub = isset($rapa[$a+1]['simpro_tbl_subbidang']) ? $rapa[$a+1]['simpro_tbl_subbidang'] : '';
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
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $lasta),'TOTAL RAP(A)');
			$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $lasta),$total_rapa);
			$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:G%d', $lasta+1, $lasta+1));		
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $lasta+1),'PERSENTASE TERHADAP KONTRAK');
			$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $lasta+1),$persen_rapa);
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
		$filename = sprintf('rapa-%s.xlsx', trim($nama_proyek)); 		
		//header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Type: application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
		header('Content-Disposition: attachment;filename="'.$filename.'"'); 
		header('Cache-Control: max-age=0'); 
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
		$objWriter->save('php://output');
	}	
	

	private function _dump($d)
	{
		print('<pre>');
		print_r($d);
		print('</pre>');
	}
	
	
}

?>