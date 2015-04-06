<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Rab extends MX_Controller {

	var $idpro = '';
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_rencana');
		$this->load->model('mdl_analisa');
	}
	
	public function index()
	{
		$idtender = $this->session->userdata('id_tender');
		$cek_rab_analisa = $this->db->query("select * from simpro_rat_rab_analisa where id_tender = $idtender");
		if (!$cek_rab_analisa->num_rows() > 0) {
			$this->default_rab();
		}
		
		$data = array(
			'id_tender' => $idtender
		);
		$this->load->view('home_rab', $data);
	}

	function adjust_rab($idtender)
	{
		/* $idtender = isset($_SESSION['idtender']) ? $_SESSION['idtender'] : $this->session->userdata('id_tender'); */
		if(!$idtender) 
		{
			echo "<h2 align='center'>Silahkan pilih tender terlebih dahulu!</h2>";
		} else 
		{
			$data_tender = $this->mdl_rencana->get_data_tender($idtender);
			$data = array(
				'idtender' => $idtender,
				'data_tender' => $data_tender['data']
			);
			$this->load->view('rab', $data);		
		}	
	}
		
	function update_tree_item_rab()
	{		
		if($this->input->post('rat_item_tree') && $this->input->post('volume'))
		{
			$is_data = $this->db->query(sprintf("SELECT * FROM simpro_rat_rab_item_tree WHERE rat_item_tree='%d'", $this->input->post('rat_item_tree')))->num_rows();
			if($is_data)
			{
				$du = array(
					'rat_item_tree' => $this->input->post('rat_item_tree'),
					'volume' => $this->input->post('volume'),
					'harga_rat' => $this->input->post('harga_rat'),
					'volume_rat' => $this->input->post('volume_rat')
				);			
				$this->db->where('rat_item_tree', $this->input->post('rat_item_tree'));
				if($this->db->update('simpro_rat_rab_item_tree', $du)) echo "Data berhasil diupdate";
					else echo "Data GAGAL diupdate!";			
			} else
			{
				$di = array(
					'rat_item_tree' => $this->input->post('rat_item_tree'),
					'volume' => $this->input->post('volume'),
					'harga_rat' => $this->input->post('harga_rat'),
					'volume_rat' => $this->input->post('volume_rat')
				);			
				if($this->db->insert('simpro_rat_rab_item_tree', $di)) echo "Data berhasil diupdate";
					else echo "Data GAGAL diupdate!";			
			}
		}
	}
	
	function edit_koefisien_satuan()
	{	
		if($this->input->post('id_tender') && $this->input->post('detail_material_kode') 
		&& $this->input->post('kode_analisa') && $this->input->post('koefisien') && $this->input->post('id_analisa_asat')
		)
		{
			if(substr($this->input->post('detail_material_kode'),0,2) <> 'AN')
			{
				$update = array(
					'id_analisa_asat' => $this->input->post('id_analisa_asat'),
					'id_tender' => $this->input->post('id_tender'),
					'kode_analisa' => $this->input->post('kode_analisa'),
					'kode_material' => $this->input->post('detail_material_kode'),
					'koefisien' => $this->input->post('koefisien')
				);
				$this->db->where('id_analisa_asat', $this->input->post('id_analisa_asat'));
				if($this->db->update('simpro_rat_analisa_asat', $update))
					echo "Data berhasil diupdate.";
						else echo "Data GAGAL diupdate!";
			} else
			{
				$update = array(
					'id_analisa_apek' => $this->input->post('id_analisa_asat'),
					'id_tender' => $this->input->post('id_tender'),
					'kode_analisa' => $this->input->post('kode_analisa'),
					'koefisien' => $this->input->post('koefisien')
				);
				$this->db->where('id_analisa_apek', $this->input->post('id_analisa_asat'));
				if($this->db->update('simpro_rat_analisa_apek', $update))
					echo "Data berhasil diupdate.";
						else echo "Data GAGAL diupdate!";
			}
		} 
	}
			
	function get_task_tree_item_rab($idpro)
	{
		$param = $this->input->get('param');
		$this->insert_vol_harga_rab($idpro);
		$arr = $this->tree($idpro, $depth=0,$param);
		echo json_encode(array('text'=>'.', 'children'=>$arr));	
	}
	
	function get_total_raba()
	{
		if($this->input->post('id_tender'))
		{
			$data = $this->mdl_analisa->total_raba($this->input->post('id_tender'));
			if(isset($data['data'])) echo number_format($data['data']['total_raba'],0);
				else echo 0;
		}
	}
	
	function total_raba($id)
	{
		$data = $this->mdl_analisa->total_raba($id);
		if(isset($data['data'])) return number_format($data['data']['total_raba'],0);
			else return 0;
	}
	
	function get_total_rab()
	{
		if($this->input->post('id_tender'))
		{
			$data = $this->mdl_analisa->total_rab($this->input->post('id_tender'));
			if(isset($data['data'])) echo number_format($data['data']['total_rab'],0);
		}
	}

	function get_total_rat($idtender)
	{
		$totrat = $this->mdl_analisa->get_total_rat($idtender);
		echo ($totrat > 0) ? number_format($totrat,0) : 0;
	}
	
	function get_persen_raba($idtender)
	{
		$tot_rat = $this->mdl_analisa->get_total_rat($idtender);
		$tot_raba = $this->mdl_analisa->total_raba($idtender);
		if(($tot_raba['data']['total_raba'] > 0) && ($tot_rat > 0))
		{
			echo number_format(($tot_raba['data']['total_raba'] / $tot_rat) * 100, 2);
		} else echo 0;
	}

	function persen_raba($idtender)
	{
		$tot_rat = $this->mdl_analisa->get_total_rat($idtender);
		$tot_raba = $this->mdl_analisa->total_raba($idtender);
		if(($tot_raba['data']['total_raba'] > 0) && ($tot_rat > 0))
		{
			return number_format(($tot_raba['data']['total_raba'] / $tot_rat) * 100, 2);
		} else return 0;
	}
	
	function tree($idpro, $depth=0,$param)
	{
		$result=array();
		$temp=array();
		$temp = $this->mdl_rencana->get_tree_item_rab_new($idpro, $depth,$param)->result();
		// get_tree_item_rab
		if(count($temp))
		{			
			$i = 0;
			foreach($temp as $row){
				// $harga_rab = $this->mdl_rencana->harga_analisa_rab($idpro, $row->kode_analisa);
				// $temp_harga = $this->mdl_rencana->get_tree_item_rab($idpro, $row->rat_item_tree)->row_array();				
				// $subtotal_rab = $this->mdl_rencana->subtotal_rab($idpro, $row->kode_tree);
				// // $temp_harga['volume_rab'] $row->volume_rab
				// $sub_rab = ($temp_harga['volume_rab'] * $harga_rab['harga_jadi_rab']); //error
				// $selisih = ($subtotal_rab - ($row->sub)); //error $row->subtotal
								
				$data[] = array(
					'rat_item_tree' => $row->rat_item_tree,
					'id_proyek_rat' => $row->id_proyek_rat,
					'id_satuan' => $row->id_satuan,
					'kode_tree' => $row->kode_tree,
					'tree_item' => utf8_encode($row->tree_item),
					'tree_satuan' => $row->tree_satuan,
					'kode_analisa' => $row->kode_analisa,
					'volume' => $row->volume,
					'harga' => $row->hrg, //error
					'harga_rab' => $row->harga_rab, //$harga_rab['harga_jadi_rab'], //$temp_harga['harga_rab'],
					'volume_rab' => $row->volume_rab, //$temp_harga['volume_rab'], //$temp_harga['volume_rab'], error
					'subtotal_rab' => $row->subtotal_rab, //$subtotal_rab,
					'subtotal' => $row->sub,
					'selisih' => $row->selisih,
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
							'children'=>$this->tree($idpro, $row->rat_item_tree,$param)
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
		}
		return array_filter($result);
	}
	
	function update_koefisien_rab()
	{
		if($this->input->post('id_simpro_rat_analisa') && $this->input->post('koefisien_rab'))
		{
			$harga_rab = $this->input->post('harga_rat'); //$this->input->post('koefisien_rab') * 
			$post = array(
				'id_simpro_rat_analisa' => $this->input->post('id_simpro_rat_analisa'),
				'harga_rab' => $harga_rab,
				'koefisien_rab' => $this->input->post('koefisien_rab'),
				// 'nilai_pengali' => 1,
				'id_tender' => $this->input->post('id_tender'),
				'koefisien_rat' => $this->input->post('koefisien_rat'),
				'kode_analisa' => $this->input->post('kode_analisa'),
				'harga_rat' => $this->input->post('harga_rat')
			);
			$q_cek = sprintf("SELECT * FROM simpro_rat_rab_analisa WHERE id_rab_analisa = '%d'", $this->input->post('id_rab_analisa'));
			// echo $q_cek;
			$isdata = $this->db->query($q_cek)->num_rows();
			if($isdata <= 0)
			{
				if($this->db->insert('simpro_rat_rab_analisa', $post))
				{
					echo "Data berhasil ditambahkan. ";
				} else echo "Data koefisien GAGAL diupdate!";
			} else
			{
				$this->db->where('id_rab_analisa', $this->input->post('id_rab_analisa'));
				if($this->db->update('simpro_rat_rab_analisa', $post))
				{
					echo "Data berhasil diupdate.";
				} else echo "Data koefisien GAGAL diupdate!";
			}
		}
	}
	
	function get_asat($idtender)
	{	
		$data = $this->mdl_analisa->get_daftar_analisa_satuan_rab($idtender);
		$this->_out($data);
	}
	
	public function get_data_harga_satuan_rab($idtender)
	{	
		$data = $this->mdl_rencana->data_harga_satuan_rab($idtender);
		$this->_out($data);
	}
	
	function edit_harga_satuan_asat_rab($idtender)
	{
		//$idtender = isset($idtender) ? $idtender : $this->get_idtender();	
		$data = $this->mdl_analisa->get_harga_satuan_asat_rab($idtender);		
		$this->_out($data);		
	}
	
	function get_raba($idtender)
	{
		$data = $this->mdl_analisa->rab_raba($idtender);
		$this->_out($data);		
	}
	
	function update_harga_asat($idtender)
	{
		if($this->input->post('pengali') && $this->input->post('kode_material'))
		{
			# simpro_rat_analisa_asat
			$sql_insert = sprintf("
				INSERT INTO simpro_rat_rab_analisa(
					id_simpro_rat_analisa, koefisien_rab, nilai_pengali,id_tender,
					koefisien_rat, harga_rat,kode_analisa, harga_rab
				)
				SELECT
					id_analisa_asat, koefisien, %d AS pengali, id_tender, 
					koefisien, harga, kode_material, (harga * %d) as harga_rab
				FROM
				simpro_rat_analisa_asat
				WHERE id_tender =  %d
				AND kode_material = '%s'", 
				$this->input->post('pengali'),
				$this->input->post('pengali'),
				$idtender, 
				$this->input->post('kode_material')
				);
			$qcek = sprintf("
				SELECT
					simpro_rat_analisa_asat.id_data_analisa, 
					simpro_rat_analisa_asat.koefisien, 
					simpro_rat_analisa_asat.id_tender, 
					simpro_rat_analisa_asat.harga, 
					simpro_rat_analisa_asat.kode_material,
					simpro_rat_rab_analisa.id_simpro_rat_analisa,
					simpro_rat_rab_analisa.kode_analisa
				FROM
				simpro_rat_analisa_asat
				LEFT JOIN simpro_rat_rab_analisa ON simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa_asat.id_analisa_asat
				WHERE simpro_rat_analisa_asat.id_tender =  '%d'
				AND simpro_rat_analisa_asat.kode_material = '%s'
				AND simpro_rat_rab_analisa.id_simpro_rat_analisa IS NOT NULL		
			", $this->input->post('id_tender'), $this->input->post('kode_material'));
			$isdata = $this->db->query($qcek)->num_rows();
			$insert = array();
			if($isdata > 0)
			{
				# update
				$data = $this->db->query($qcek)->result_array();
				foreach($data as $k=>$v)
				{
					$du = array(
						'id_simpro_rat_analisa' => $v['id_simpro_rat_analisa'],
						'id_tender' => $v['id_tender'],
						'nilai_pengali' => $this->input->post('pengali'),
						'koefisien_rab' => $v['koefisien'],
						'kode_analisa' => $v['kode_analisa'],
						'harga_rab' => ($this->input->post('pengali') * $v['harga'])					
					);
					$this->db->where('id_simpro_rat_analisa', $v['id_simpro_rat_analisa']);
					$this->db->where('id_tender', $v['id_tender']);
					$this->db->update('simpro_rat_rab_analisa', $du);
				}
				echo "Harga RAB berhasil diupdate.";
			} else
			{
				$this->db->query($sql_insert);
				echo "Harga RAB berhasil ditambahkan.";			
			} 			
		}		
	}
	
	function update_harga_pengali()
	{
		if($this->input->post('nilai_pengali') && $this->input->post('id_tender'))
		{
			$pengali = $this->input->post('nilai_pengali');
			$idtender = $this->input->post('id_tender');			
			// $qcek = sprintf("
				// SELECT
				// 	simpro_rat_analisa_asat.id_data_analisa, 
				// 	simpro_rat_analisa_asat.koefisien, 
				// 	simpro_rat_analisa_asat.id_tender, 
				// 	simpro_rat_analisa_asat.harga, 
				// 	simpro_rat_analisa_asat.kode_material,
				// 	simpro_rat_rab_analisa.id_simpro_rat_analisa, 
				// 	simpro_rat_rab_analisa.kode_analisa
				// FROM
				// 	simpro_rat_analisa_asat
				// LEFT JOIN simpro_rat_rab_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa_asat.id_analisa_asat AND simpro_rat_rab_analisa.id_tender = simpro_rat_analisa_asat.id_tender)
				// WHERE simpro_rat_analisa_asat.id_tender =  %d
				// AND simpro_rat_rab_analisa.id_simpro_rat_analisa IS NOT NULL			
			// ", $idtender);

			$qcek = "select					
					a.id_analisa_asat,
					a.id_data_analisa, 
					a.koefisien, 
					a.id_tender,
					a.harga, 
					a.detail_material_kode as kode_material,					
					simpro_rat_rab_analisa.id_simpro_rat_analisa, 
					simpro_rat_rab_analisa.kode_analisa
					from (select * from (SELECT 					
						simpro_rat_analisa_asat.id_data_analisa,		
						simpro_rat_analisa_asat.id_analisa_asat,
						simpro_rat_analisa_asat.kode_analisa,
						simpro_rat_analisa_asat.id_tender,						
						(simpro_rat_analisa_asat.kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						simpro_rat_analisa_daftar.nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_tbl_detail_material.detail_material_kode, 
						simpro_tbl_detail_material.detail_material_nama, 
						simpro_tbl_detail_material.detail_material_satuan,
						simpro_rat_analisa_asat.harga,
						simpro_rat_analisa_asat.koefisien,
						(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
					FROM 
						simpro_rat_analisa_asat
					LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
					JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_asat.id_tender)
					LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					WHERE simpro_rat_analisa_asat.id_tender = $idtender
					ORDER BY 
						simpro_rat_analisa_asat.kode_analisa,
						simpro_tbl_detail_material.detail_material_kode				
					ASC
				) c
				UNION ALL 
				(
					SELECT 		
						simpro_rat_analisa_apek.id_data_analisa,
						simpro_rat_analisa_apek.id_analisa_apek AS id_analisa_asat,
						simpro_rat_analisa_apek.kode_analisa,
						simpro_rat_analisa_apek.id_tender,
						(simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						ad.nama_item AS nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_rat_analisa_apek.kode_analisa AS detail_material_kode,
						ad.nama_item as detail_material_nama,
						simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
						COALESCE(tbl_harga.harga,0) AS harga,
						simpro_rat_analisa_apek.koefisien,
						COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
					FROM 
						simpro_rat_analisa_apek
					INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
					INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_apek.id_tender)			
					INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					LEFT JOIN (
						SELECT 
							DISTINCT ON(kode_analisa)
							kode_analisa,
							SUM(harga * koefisien) AS harga
						FROM simpro_rat_analisa_asat 
						WHERE id_tender = $idtender
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
					WHERE simpro_rat_analisa_apek.id_tender = $idtender
					ORDER BY 
						simpro_rat_analisa_apek.parent_kode_analisa,				
						simpro_rat_analisa_apek.kode_analisa
					ASC					
				)) a
				LEFT JOIN simpro_rat_rab_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = a.id_analisa_asat AND simpro_rat_rab_analisa.id_tender = a.id_tender)
				WHERE a.id_tender =  $idtender
				AND simpro_rat_rab_analisa.id_simpro_rat_analisa IS NOT NULL";

			$isdata = $this->db->query($qcek)->num_rows();
			if($isdata > 0)
			{
				# update				
				$qupdate = 
				// sprintf(
					"
					UPDATE simpro_rat_rab_analisa AS tp 
					SET 
						harga_rat = tbl_update.harga, 
						koefisien_rat = tbl_update.koefisien,
						nilai_pengali = $pengali,
						harga_rab = (tbl_update.harga * $pengali)
					FROM (

						select					
					a.id_analisa_asat,
					a.id_data_analisa, 
					a.koefisien, 
					a.id_tender,
					a.harga, 
					a.detail_material_kode as kode_material,					
					simpro_rat_rab_analisa.id_simpro_rat_analisa, 
					simpro_rat_rab_analisa.kode_analisa
					from (select * from (SELECT 					
						simpro_rat_analisa_asat.id_data_analisa,		
						simpro_rat_analisa_asat.id_analisa_asat,
						simpro_rat_analisa_asat.kode_analisa,
						simpro_rat_analisa_asat.id_tender,						
						(simpro_rat_analisa_asat.kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						simpro_rat_analisa_daftar.nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_tbl_detail_material.detail_material_kode, 
						simpro_tbl_detail_material.detail_material_nama, 
						simpro_tbl_detail_material.detail_material_satuan,
						simpro_rat_analisa_asat.harga,
						simpro_rat_analisa_asat.koefisien,
						(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
					FROM 
						simpro_rat_analisa_asat
					LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
					JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_asat.id_tender)
					LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					WHERE simpro_rat_analisa_asat.id_tender = $idtender
					ORDER BY 
						simpro_rat_analisa_asat.kode_analisa,
						simpro_tbl_detail_material.detail_material_kode				
					ASC
				) c
				UNION ALL 
				(
					SELECT 		
						simpro_rat_analisa_apek.id_data_analisa,
						simpro_rat_analisa_apek.id_analisa_apek AS id_analisa_asat,
						simpro_rat_analisa_apek.kode_analisa,
						simpro_rat_analisa_apek.id_tender,
						(simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						ad.nama_item AS nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_rat_analisa_apek.kode_analisa AS detail_material_kode,
						ad.nama_item as detail_material_nama,
						simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
						COALESCE(tbl_harga.harga,0) AS harga,
						simpro_rat_analisa_apek.koefisien,
						COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
					FROM 
						simpro_rat_analisa_apek
					INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
					INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_apek.id_tender)			
					INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					LEFT JOIN (
						SELECT 
							DISTINCT ON(kode_analisa)
							kode_analisa,
							SUM(harga * koefisien) AS harga
						FROM simpro_rat_analisa_asat 
						WHERE id_tender = $idtender
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
					WHERE simpro_rat_analisa_apek.id_tender = $idtender
					ORDER BY 
						simpro_rat_analisa_apek.parent_kode_analisa,				
						simpro_rat_analisa_apek.kode_analisa
					ASC					
				)) a
				LEFT JOIN simpro_rat_rab_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = a.id_analisa_asat AND simpro_rat_rab_analisa.id_tender = a.id_tender)
				WHERE a.id_tender =  $idtender
				AND simpro_rat_rab_analisa.id_simpro_rat_analisa IS NOT NULL
									
					) AS tbl_update
					WHERE tp.id_tender = $idtender
					AND tbl_update.id_tender = tp.id_tender AND tbl_update.id_analisa_asat = tp.id_simpro_rat_analisa				
					AND LEFT(tp.kode_analisa,2) <> 'AN'
				"
				// , $pengali, $pengali, $idtender)
;
				$this->db->query($qupdate);				
				
				# update untuk kode AN
				$q_an = 
				// sprintf(
					"
					UPDATE simpro_rat_rab_analisa SET nilai_pengali = $pengali 
					WHERE id_tender = $idtender
					AND LEFT(kode_analisa,2) = 'AN'
					"
					// , $pengali, $idtender)
				;
				$this->db->query($q_an);

				/*SELECT
							simpro_rat_analisa_asat.id_data_analisa, 
							simpro_rat_analisa_asat.id_analisa_asat,							
							simpro_rat_analisa_asat.koefisien, 
							simpro_rat_analisa_asat.id_tender, 
							simpro_rat_analisa_asat.harga, 
							simpro_rat_analisa_asat.kode_material,
							simpro_rat_rab_analisa.id_simpro_rat_analisa, 
							simpro_rat_rab_analisa.kode_analisa
						FROM
							simpro_rat_analisa_asat
						LEFT JOIN simpro_rat_rab_analisa ON simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa_asat.id_analisa_asat
						AND simpro_rat_rab_analisa.id_simpro_rat_analisa IS NOT NULL */
			}
			# insert
			// $sql_insert = sprintf("
			// 	SELECT
			// 		simpro_rat_analisa_asat.id_analisa_asat,
			// 		simpro_rat_analisa_asat.id_data_analisa, 
			// 		simpro_rat_analisa_asat.koefisien, 
			// 		simpro_rat_analisa_asat.id_tender,
			// 		simpro_rat_analisa_asat.harga, 
			// 		simpro_rat_analisa_asat.kode_material,
			// 		simpro_rat_rab_analisa.id_simpro_rat_analisa, simpro_rat_rab_analisa.kode_analisa
			// 	FROM
			// 		simpro_rat_analisa_asat
			// 	LEFT JOIN simpro_rat_rab_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa_asat.id_analisa_asat AND simpro_rat_rab_analisa.id_tender = simpro_rat_analisa_asat.id_tender)
			// 	WHERE simpro_rat_analisa_asat.id_tender =  %d
			// 	AND simpro_rat_rab_analisa.id_simpro_rat_analisa IS NULL
			// ", $idtender);

			$sql_insert = "
				select					
					a.id_analisa_asat,
					a.id_data_analisa, 
					a.koefisien, 
					a.id_tender,
					a.harga, 
					a.detail_material_kode as kode_material,					
					simpro_rat_rab_analisa.id_simpro_rat_analisa, 
					simpro_rat_rab_analisa.kode_analisa
					from (select * from (SELECT 					
						simpro_rat_analisa_asat.id_data_analisa,		
						simpro_rat_analisa_asat.id_analisa_asat,
						simpro_rat_analisa_asat.kode_analisa,
						simpro_rat_analisa_asat.id_tender,						
						(simpro_rat_analisa_asat.kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						simpro_rat_analisa_daftar.nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_tbl_detail_material.detail_material_kode, 
						simpro_tbl_detail_material.detail_material_nama, 
						simpro_tbl_detail_material.detail_material_satuan,
						simpro_rat_analisa_asat.harga,
						simpro_rat_analisa_asat.koefisien,
						(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
					FROM 
						simpro_rat_analisa_asat
					LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
					JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_asat.id_tender)
					LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					WHERE simpro_rat_analisa_asat.id_tender = $idtender
					ORDER BY 
						simpro_rat_analisa_asat.kode_analisa,
						simpro_tbl_detail_material.detail_material_kode				
					ASC
				) c
				UNION ALL 
				(
					SELECT 		
						simpro_rat_analisa_apek.id_data_analisa,
						simpro_rat_analisa_apek.id_analisa_apek AS id_analisa_asat,
						simpro_rat_analisa_apek.kode_analisa,
						simpro_rat_analisa_apek.id_tender,
						(simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						ad.nama_item AS nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_rat_analisa_apek.kode_analisa AS detail_material_kode,
						ad.nama_item as detail_material_nama,
						simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
						COALESCE(tbl_harga.harga,0) AS harga,
						simpro_rat_analisa_apek.koefisien,
						COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
					FROM 
						simpro_rat_analisa_apek
					INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
					INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_apek.id_tender)			
					INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					LEFT JOIN (
						SELECT 
							DISTINCT ON(kode_analisa)
							kode_analisa,
							SUM(harga * koefisien) AS harga
						FROM simpro_rat_analisa_asat 
						WHERE id_tender = $idtender
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
					WHERE simpro_rat_analisa_apek.id_tender = $idtender
					ORDER BY 
						simpro_rat_analisa_apek.parent_kode_analisa,				
						simpro_rat_analisa_apek.kode_analisa
					ASC					
				)) a
				LEFT JOIN simpro_rat_rab_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = a.id_analisa_asat AND simpro_rat_rab_analisa.id_tender = a.id_tender)
				WHERE a.id_tender =  $idtender
				AND simpro_rat_rab_analisa.id_simpro_rat_analisa IS NULL
			";
			$data = $this->db->query($sql_insert)->result_array();
			foreach($data as $k=>$v)
			{
				$insert = array(
						'id_simpro_rat_analisa' => $v['id_analisa_asat'],
						'id_tender' => $v['id_tender'],
						'harga_rat' => $v['harga'],
						'koefisien_rat' => $v['koefisien'],
						'nilai_pengali' => $pengali,
						'koefisien_rab' => $v['koefisien'],
						'kode_analisa' => $v['kode_material'],
						'harga_rab' => ($pengali * $v['harga'])					
				);
				$this->db->insert('simpro_rat_rab_analisa', $insert);
			}

			echo "Harga RAB berhasil diupdate.";
		}
	}
	
	function get_data_ansat_rab($idpro)
	{
		if($this->input->get('subbidang_kode'))
		{
			$data = $this->mdl_rencana->get_ansat($this->input->get('subbidang_kode'));
		} else $data = $this->mdl_rencana->get_ansat('');
		$this->_out($data);	
	}

	function set_satuan_induk($proyek_id)
	{
		$sql_set_satuan_induk = "
		update simpro_tbl_input_kontrak set
		tahap_satuan_kendali = 'Ls',
		tahap_volume_kendali = 1,
		tahap_harga_satuan_kendali = 0
		from (
		select * from (select
		a.*,
		(SELECT 
		count('a') as count
		FROM simpro_tbl_input_kontrak
		WHERE proyek_id = a.proyek_id
		and tahap_kode_induk_kendali = a.tahap_kode_kendali) as count
		from
		simpro_tbl_input_kontrak a
		where a.proyek_id = $proyek_id) n
		where n.count != 0
		) x
		where simpro_tbl_input_kontrak.tahap_kode_kendali = x.tahap_kode_kendali
		";

		$this->db->query($sql_set_satuan_induk);
	}
	
	function approve_rab()
	{
		$is_update_rab = $this->db->query(sprintf("SELECT * FROM simpro_rab_approve WHERE id_tender = '%d'", $this->input->post('id_tender')))->num_rows();
		if($this->input->post('id_tender') && !$is_update_rab)
		{					
			$tender_id = $this->input->post('id_tender');
			$sql_insert_proyek = sprintf("
				with rows as (INSERT INTO simpro_tbl_proyek(
				proyek, 
				no_spk,
				lingkup_pekerjaan, 
				tgl_tender, 
				user_update, 
				tgl_update, 
				ip_update, 
				waktu_update,
				status_pekerjaan, 
				divisi_kode, 
				proyek_status,
				sts_pekerjaan, 
				mulai, 
				berakhir, 
				jangka_waktu,
				masa_pemeliharaan, 
				total_waktu_pelaksanaan,
				id_tender,
				sbu_kode,

				lokasi_proyek,
				proyek_konsultan_pengawas,
				pemberi_kerja,
				kepala_proyek,
				nilai_kontrak_ppn,
				nilai_kontrak_non_ppn,
				lokasi_latitude,
				lokasi_longitude,
				sketsa_proyek
				)
				SELECT			
					nama_proyek, 
					'[EDIT] SPK ID Tender %d[/EDIT]', 
					lingkup_pekerjaan, 
					tanggal_tender, 
					'%s', 
					NOW(), 
					'%s', 
					NOW(), 
					0, 
					divisi_id, 
					2, 
					0, 
					mulai, 
					akhir,
					waktu_pelaksanaan, 
					waktu_pemeliharaan,
					(waktu_pelaksanaan+waktu_pemeliharaan) as total_waktu,
					$tender_id,
					jenis_proyek,

					lokasi_proyek,
					konsultan_pengawas,
					pemilik_proyek,
					konsultan_pelaksana,
					nilai_kontrak_ppn,
					nilai_kontrak_excl_ppn,
					xlong,
					xlat,
					peta_lokasi_proyek
					FROM 
				simpro_m_rat_proyek_tender
				WHERE id_proyek_rat = '%d' RETURNING proyek_id)
				SELECT proyek_id FROM rows
			", $this->input->post('id_tender'), 
			$this->session->userdata('uname'),
			$this->input->ip_address(),
			$this->input->post('id_tender'));

			$xns = $this->db->query($sql_insert_proyek);
			if ($xns->result()) {
				foreach ($xns->result() as $rowxx) {
					$asd = $rowxx->proyek_id;
				}
			}

			$id_proyek = $asd;

			if($id_proyek)
			{			
				$id_tender = $this->input->post('id_tender');				
				
				$q_rab_to_rab_proyek = "with rab_subtotal as (
	SELECT DISTINCT(kode_tree), subtotal_rab,
(select count(kode_tree) from simpro_rat_item_tree where simpro_rat_item_tree.tree_parent_kode = tbl_harga_rab.kode_tree and simpro_rat_item_tree.id_proyek_rat = $id_tender) as count_kode
 FROM (
					SELECT 
						simpro_rat_item_tree.kode_tree,
						SUM(harga_analisa.harga_rab * simpro_rat_rab_item_tree.volume) as subtotal_rab
					FROM 
					simpro_rat_item_tree
					LEFT JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.id_proyek_rat = simpro_rat_item_tree.id_proyek_rat AND simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN (
						SELECT 
							kode_analisa_rat,
							SUM(harga_rab * koefisien_rab) harga_rab
						FROM (
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $id_tender
							AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
						)
						UNION ALL
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $id_tender
						)
						) as tbl_rab_analisa
						GROUP BY kode_analisa_rat
					) as harga_analisa on harga_analisa.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
					WHERE simpro_rat_item_tree.id_proyek_rat = $id_tender	
					GROUP BY simpro_rat_item_tree.kode_tree

				UNION ALL

					SELECT 
						simpro_rat_item_tree.tree_parent_kode,
						SUM(harga_analisa.harga_rab * simpro_rat_rab_item_tree.volume) as subtotal_rab
					FROM 
					simpro_rat_item_tree
					INNER JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.id_proyek_rat = simpro_rat_item_tree.id_proyek_rat AND simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_item_tree.rat_item_tree
					LEFT JOIN (
						SELECT 
							kode_analisa_rat,
							SUM(harga_rab * koefisien_rab) harga_rab
						FROM (
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $id_tender
							AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
						)
						UNION ALL
						(
							SELECT 
							simpro_rat_rab_analisa.*,
							simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
							FROM simpro_rat_rab_analisa
							INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
							WHERE simpro_rat_rab_analisa.id_tender = $id_tender
						)
						) as tbl_rab_analisa
						GROUP BY kode_analisa_rat
					) as harga_analisa on harga_analisa.kode_analisa_rat = simpro_rat_analisa_item_apek.kode_analisa
					WHERE simpro_rat_item_tree.id_proyek_rat = $id_tender
				GROUP BY simpro_rat_item_tree.tree_parent_kode
				) as tbl_harga_rab 
				where (select count(kode_tree) from simpro_rat_item_tree where simpro_rat_item_tree.tree_parent_kode = tbl_harga_rab.kode_tree and simpro_rat_item_tree.id_proyek_rat = $id_tender) = 0
				and subtotal_rab IS NOT NULL
				GROUP BY kode_tree, subtotal_rab
				order by kode_tree
),
rab_harga_analisa as (
SELECT 
				kode_analisa_rat,
				SUM(harga_rab * koefisien_rab) harga_jadi_rab
			FROM (
			(
				SELECT 
				simpro_rat_rab_analisa.*,
				simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa_rat
				FROM simpro_rat_rab_analisa
				INNER JOIN simpro_rat_analisa_apek ON simpro_rat_analisa_apek.id_analisa_apek = simpro_rat_rab_analisa.id_simpro_rat_analisa
				WHERE simpro_rat_rab_analisa.id_tender = $id_tender
				AND LEFT(simpro_rat_rab_analisa.kode_analisa,2)='AN' 
			)
			UNION ALL
			(
				SELECT 
				simpro_rat_rab_analisa.*,
				simpro_rat_analisa_asat.kode_analisa as kode_analisa_rat
				FROM simpro_rat_rab_analisa
				INNER JOIN simpro_rat_analisa_asat ON simpro_rat_analisa_asat.id_analisa_asat = simpro_rat_rab_analisa.id_simpro_rat_analisa
				WHERE simpro_rat_rab_analisa.id_tender = $id_tender
			)
			) as tbl_rab_analisa
			GROUP BY kode_analisa_rat
),
rab_koefisien as (
SELECT 
				(
					SELECT
						SUM(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) as subtotal
					FROM 
						simpro_rat_analisa
					WHERE 
						simpro_rat_analisa.id_proyek_rat = $id_tender
					GROUP BY 
						simpro_rat_analisa.id_proyek_rat, 
						simpro_rat_analisa.rat_item_tree						
				) AS harga,				
				(
					SELECT
						SUM(simpro_rat_analisa.aharga * simpro_rat_analisa.akoefisien) as subtotal
					FROM 
						simpro_rat_analisa
					WHERE 
						simpro_rat_analisa.id_proyek_rat = $id_tender
					GROUP BY 
						simpro_rat_analisa.id_proyek_rat, 
						simpro_rat_analisa.rat_item_tree									
				) * simpro_rat_item_tree.volume AS subtotal,
				(
					SELECT
						SUM(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) AS subtotal
					FROM 
						simpro_rat_rab_analisa
					INNER JOIN simpro_rat_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa)						
					WHERE 
						simpro_rat_analisa.id_proyek_rat = $id_tender AND 
						simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa
					GROUP BY 
						simpro_rat_analisa.rat_item_tree
				) AS harga_rab,				
				(
					SELECT
						SUM(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab) AS subtotal_rab					
					FROM 
						simpro_rat_rab_analisa						
					INNER JOIN simpro_rat_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa)
					WHERE 
						simpro_rat_analisa.id_proyek_rat = $id_tender AND 
						simpro_rat_rab_analisa.id_simpro_rat_analisa = simpro_rat_analisa.id_simpro_rat_analisa						
					GROUP BY 
						simpro_rat_analisa.rat_item_tree
				) * simpro_rat_rab_item_tree.volume AS subtotal_rab,
				simpro_rat_item_tree.volume AS volume,
				simpro_rat_rab_item_tree.volume AS volume_rab,
				simpro_rat_rab_item_tree.id_rat_rab_item_tree,
				simpro_rat_item_tree.rat_item_tree,
				CONCAT(simpro_rat_item_tree.kode_tree,' ',simpro_rat_item_tree.tree_item) AS task 
			FROM simpro_rat_item_tree 
			LEFT JOIN simpro_rat_rab_item_tree ON simpro_rat_rab_item_tree.rat_item_tree = simpro_rat_item_tree.rat_item_tree			
			WHERE simpro_rat_item_tree.id_proyek_rat = $id_tender 
)
insert into simpro_tbl_input_kontrak (proyek_id,
		tahap_kode_kendali,
		tahap_nama_kendali,
		tahap_satuan_kendali,
		tahap_kode_induk_kendali,
		tahap_volume_kendali,
		tahap_harga_satuan_kendali)
select 
			$id_proyek,
					x.kode_tree, 
					x.tree_item,
					x.tree_satuan,
					x.tree_parent_kode, 
			coalesce((select volume_rab from rab_koefisien where rat_item_tree = x.rat_item_tree),1) as volume_rab,
			case when (select count(kode_tree) from simpro_rat_item_tree where simpro_rat_item_tree.tree_parent_kode = x.kode_tree and simpro_rat_item_tree.id_proyek_rat = 28) = 0 then
				case when (select volume_rab from rab_koefisien where rat_item_tree = x.rat_item_tree) = 0 or sum(totals.subtotal_rab) = 0
				then 0
				else coalesce(sum(totals.subtotal_rab),0) / coalesce((select volume_rab from rab_koefisien where rat_item_tree = x.rat_item_tree),1)
				end
			else 0
			end as harga_rab
			from
			simpro_rat_item_tree x
			left join
			(SELECT 
				simpro_rat_item_tree.kode_tree,
				simpro_rat_analisa_item_apek.kode_analisa,
				coalesce((select subtotal_rab from rab_subtotal where kode_tree = simpro_rat_item_tree.kode_tree),0) as subtotal_rab,
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
			ORDER BY x.kode_tree";

				$this->db->query($q_rab_to_rab_proyek);

				$this->update_tree_parent_id_rab($id_proyek);

				$this->set_satuan_induk($id_proyek);

				$q_dok_migrate = "insert into simpro_tbl_dokumen_proyek(
					du_proyek_tgl_upload,
					du_proyek_judul,
					du_proyek_keterangan,
					du_proyek_file,
					du_proyek_file_type,
					proyek_id)
					select 
					du_proyek_tgl_upload,
					du_proyek_judul,
					du_proyek_keterangan,
					du_proyek_file,
					du_proyek_file_type,
					$id_proyek
					from simpro_rat_dokumen_proyek where id_proyek_rat = $id_tender";

				$this->db->query($q_dok_migrate);

				$q_sketsa_migrate = "insert into simpro_tbl_sketsa_proyek(
					foto_proyek_tgl,
					foto_proyek_judul,
					foto_proyek_keterangan,
					foto_proyek_file,
					foto_proyek_file_type,
					proyek_id
					)
					select
					du_proyek_tgl_upload,
					du_proyek_judul,
					du_proyek_keterangan,
					du_proyek_file,
					du_proyek_file_type,
					$id_proyek
					from simpro_rat_sketsa_proyek where id_proyek_rat = $id_tender";

				$this->db->query($q_sketsa_migrate);

				$sketsa = $this->db->query("select peta_lokasi_proyek from simpro_m_rat_proyek_tender where id_proyek_rat = $id_tender")->row()->peta_lokasi_proyek;
				
				$data_peta_lokasi = array(
					'sketsa_proyek' => $sketsa
				);

				$this->db->where('proyek_id',$id_proyek);
				$this->db->update('simpro_tbl_proyek',$data_peta_lokasi);

				# update proyek_id di tabel tender 
				$du_tender = array('proyek_id' => $id_proyek);
				$this->db->where('id_proyek_rat', $this->input->post('id_tender'));
				$this->db->update('simpro_m_rat_proyek_tender', $du_tender);
				
				# insert ke tabel 
				$this->db->query(sprintf("INSERT INTO simpro_rap_item_tree(id_proyek, kode_tree, tree_parent_id, tree_item, tree_satuan, volume) VALUES(%d, '11111', 10000, 'Biaya Langsung', 'Ls', 1)", $id_proyek));
				$parent_id = $this->db->insert_id();				
				$q_item_tree = sprintf("							
					INSERT INTO simpro_rap_item_tree(id_proyek, id_satuan, kode_tree, tree_item, tree_satuan, volume, tree_parent_kode)
					SELECT 
						'%d', id_satuan, kode_tree, tree_item, tree_satuan, volume, tree_parent_kode
					FROM 
					simpro_rat_item_tree 
					WHERE id_proyek_rat = '%d';
						
					UPDATE simpro_rap_item_tree AS tp 
					set tree_parent_id = tbl_update.rap_item_tree
					FROM (
						SELECT 
						srit.kode_tree,
						COALESCE(srit.rap_item_tree,0) as rap_item_tree
						FROM
						simpro_rap_item_tree
						LEFT JOIN simpro_rap_item_tree srit ON srit.kode_tree = simpro_rap_item_tree.tree_parent_kode
						WHERE simpro_rap_item_tree.id_proyek = '%d'
						AND simpro_rap_item_tree.id_proyek = srit.id_proyek
						ORDER BY simpro_rap_item_tree.rap_item_tree ASC
					) AS tbl_update
					WHERE tp.id_proyek = '%d'
					AND tbl_update.kode_tree = tp.tree_parent_kode;

					UPDATE simpro_rap_item_tree
					set tree_parent_id = 0 
					WHERE id_proyek = '%d'
					AND tree_parent_id IS NULL;
				", $id_proyek, $id_tender, $id_proyek, $id_proyek, $id_proyek);
				$this->db->query($q_item_tree);
				$this->db->query(sprintf("UPDATE simpro_rap_item_tree SET tree_parent_id='%d' WHERE tree_parent_id=0 AND id_proyek='%d'", $parent_id, $id_proyek));
				$this->db->query(sprintf("UPDATE simpro_rap_item_tree SET tree_parent_id='0' WHERE tree_parent_id=10000 AND id_proyek='%d'", $id_proyek));
				
				# insert ke tabel simpro_rap_analisa_daftar
				$q_analisa_daftar = sprintf("
					INSERT INTO simpro_rap_analisa_daftar(kode_analisa, id_kat_analisa, nama_item, id_satuan, id_proyek)
					SELECT 
						kode_analisa, id_kat_analisa, nama_item, id_satuan, '%d'
					FROM simpro_rat_analisa_daftar
					WHERE id_tender = '%d'
				", $id_proyek, $id_tender);			
				$this->db->query($q_analisa_daftar);
				
				# insert ke tabel simpro_rap_analisa_asat
				$q_analisa_asat = sprintf("
					INSERT INTO simpro_rap_analisa_asat(kode_material, id_detail_material, koefisien, harga, kode_analisa, id_proyek, keterangan, kode_rap)
					SELECT 
						kode_material, 
						id_detail_material, koefisien, harga, 
						kode_analisa, '%d', keterangan, kode_rap
					FROM simpro_rat_analisa_asat
					WHERE id_tender = '%d'
				", $id_proyek, $id_tender);
				$this->db->query($q_analisa_asat);
				$q_up_asat = sprintf("
					UPDATE simpro_rap_analisa_asat AS ta
					SET id_data_analisa = tbl_update.id_data_analisa
					FROM (
						SELECT id_data_analisa, kode_analisa
						FROM simpro_rap_analisa_daftar
						WHERE id_proyek = '%d'
					) AS tbl_update
					WHERE ta.id_proyek = '%d'
					AND tbl_update.kode_analisa = ta.kode_analisa
				", $id_proyek, $id_proyek);
				$this->db->query($q_up_asat);
				
				# insert ke tabel simpro_rap_analisa_apek
				$q_analisa_apek = sprintf("
					INSERT INTO simpro_rap_analisa_apek(kode_analisa,koefisien,harga,id_proyek,parent_kode_analisa)
					SELECT 
						kode_analisa,koefisien,
						harga,'%d',parent_kode_analisa
					FROM simpro_rat_analisa_apek
					WHERE id_tender = '%d'
				", $id_proyek, $id_tender);
				$this->db->query($q_analisa_apek);
				$q_up_apek = sprintf("
					UPDATE simpro_rap_analisa_apek AS ta 
					SET id_data_analisa = tbl_update.id_data_analisa
					FROM (
						SELECT id_data_analisa, kode_analisa
						FROM simpro_rap_analisa_daftar
						WHERE id_proyek = '%d'
					) AS tbl_update
					WHERE ta.id_proyek = '%d'
					AND tbl_update.kode_analisa = ta.kode_analisa
				", $id_proyek, $id_proyek);
				$this->db->query($q_up_apek);
				// parent_id_analisa
				$q_up_apek2 = sprintf("
					UPDATE simpro_rap_analisa_apek AS ta 
					SET parent_id_analisa = tbl_update.id_data_analisa
					FROM (
						SELECT id_data_analisa, kode_analisa
						FROM simpro_rap_analisa_daftar
						WHERE id_proyek = '%d'
					) AS tbl_update
					WHERE ta.id_proyek = '%d'
					AND tbl_update.kode_analisa = ta.parent_kode_analisa
				", $id_proyek, $id_proyek);
				$this->db->query($q_up_apek2);
				
				# insert ke tabel simpro_rap_analisa_item_apek
				# rap_item_tree
				$q_analisa_item_apek = sprintf("
					INSERT INTO simpro_rap_analisa_item_apek(id_proyek, kode_analisa, harga, kode_tree)
					SELECT 
						'%d', kode_analisa, harga, kode_tree
					FROM simpro_rat_analisa_item_apek
					WHERE id_proyek_rat = '%d'
				", $id_proyek, $id_tender);				
				$this->db->query($q_analisa_item_apek);
				$q_uo_item_apek = sprintf("
					UPDATE simpro_rap_analisa_item_apek AS ta 
					set id_data_analisa = tbl_update.id_data_analisa
					FROM (
						SELECT id_data_analisa, kode_analisa
						FROM simpro_rap_analisa_daftar
						WHERE id_proyek = '%d'
					) AS tbl_update
					WHERE ta.id_proyek = '%d'
					AND tbl_update.kode_analisa = ta.kode_analisa;					
				", $id_proyek, $id_proyek);
				$this->db->query($q_uo_item_apek);
				$q_up_rao_itt = sprintf("
					UPDATE simpro_rap_analisa_item_apek AS tp 
					set rap_item_tree = tbl_update.rap_item_tree
					FROM (
						SELECT 
						simpro_rap_item_tree.kode_tree,
						COALESCE(rap_item_tree,0) as rap_item_tree
						FROM
						simpro_rap_item_tree
						WHERE simpro_rap_item_tree.id_proyek = '%d'
						ORDER BY simpro_rap_item_tree.rap_item_tree ASC
					) AS tbl_update
					WHERE tp.id_proyek = '%d'
					AND tbl_update.kode_tree = tp.kode_tree;				
				", $id_proyek, $id_proyek);
				$this->db->query($q_up_rao_itt);

			} else echo "Error insert ke tabel Proyek";
			
			$insert_approve_rab = array(
				'id_tender' => $id_tender,
				'tgl_approve' => date('Y-m-d'),
				'user_id' => $this->session->userdata('uid')
			);
			$this->db->insert('simpro_rab_approve', $insert_approve_rab);
			
			# update kode BL
			$this->update_kode_rap_bl($id_proyek);						
			
			# update kode biaya langsung
			$update_tit = array(
				'id_proyek' => $id_proyek,
				'kode_tree' => '1'
			);
			$varwhere = array(
				'kode_tree'=>'11111',
				'id_proyek'=>$id_proyek
			 );
			$this->db->where($varwhere);
			$this->db->update('simpro_rap_item_tree', $update_tit);
			# end update 			
			
			# Proses Biaya Tidak Langsung
			$this->insert_btl_rap($id_proyek);
			$this->insert_biaya_umum_rap($id_tender, $id_proyek);
			$this->insert_bank_rap($id_tender, $id_proyek);
			$this->insert_asuransi_rap($id_tender, $id_proyek);

			
			# tabel 
			// $this->insert_detail_analisa_rap($id_tender, $id_proyek);
			$q_up_rao_kode = "
				UPDATE simpro_rap_analisa_item_apek
				set kode_tree = 
				(SELECT 
					kode_tree 
				FROM 
				simpro_rap_item_tree 
				where simpro_rap_item_tree.rap_item_tree = simpro_rap_analisa_item_apek.rap_item_tree)
				where id_proyek = $id_proyek;				
			";
			$this->db->query($q_up_rao_kode);

			echo "RAB berhasil diapprove. Proses bisa berlanjut ke RAP.";
		} else echo "RAB sudah diapprove sebelumnya!";
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

	function insert_asat_rap()
	{
		/*
		* Biaya Tidak Langsung
		* Bank
		- Bunga Bank (504.0002)
		- provisi Jaminan (504.0012)
		* Asuransi
		- 505.0160 (Asuransi)
		- 504.1003 Asuransi CAR		
		*/
	}
	
	function update_parent_id_rap($idpro)
	{
		$sql = sprintf("
				UPDATE simpro_rap_item_tree AS tp 
				set tree_parent_id = tbl_update.rap_item_tree
				FROM (
					SELECT 
					srit.kode_tree,
					COALESCE(srit.rap_item_tree,0) as rap_item_tree
					FROM
					simpro_rap_item_tree
					LEFT JOIN simpro_rap_item_tree srit ON srit.kode_tree = simpro_rap_item_tree.tree_parent_kode
					WHERE simpro_rap_item_tree.id_proyek = '%d'
					AND simpro_rap_item_tree.id_proyek = srit.id_proyek
					ORDER BY simpro_rap_item_tree.rap_item_tree ASC
				) AS tbl_update
				WHERE tp.id_proyek = '%d'
				AND tbl_update.kode_tree = tp.tree_parent_kode
			", $idpro, $idpro);
		if($this->db->query($sql)) return true;
			else return false;
	}
	
	function insert_btl_rap($idpro)
	{
		$insert_btl = array(
			array(
				'id_proyek' => $idpro,
				'kode_tree' => '2',
				'tree_item' => 'Biaya Tidak Langsung',
				'tree_satuan' => 'Ls',
				'tree_parent_id' => '0',
				'tree_parent_kode' => NULL,
				'volume' => 1
			),
			array(
				'id_proyek' => $idpro,
				'kode_tree' => '2.1',
				'tree_item' => 'Bank',
				'tree_satuan' => 'Ls',
				'tree_parent_id' => NULL,
				'tree_parent_kode' => '2',
				'volume' => 1
			),
			// array(
			// 	'id_proyek' => $idpro,
			// 	'kode_tree' => '2.1.1',
			// 	'tree_item' => 'Bunga',
			// 	'tree_satuan' => 'Ls',
			// 	'tree_parent_id' => NULL,
			// 	'tree_parent_kode' => '2.1',
			// 	'volume' => 1
			// ),
			// array(
			// 	'id_proyek' => $idpro,
			// 	'kode_tree' => '2.1.2',
			// 	'tree_item' => 'Provisi Jaminan',
			// 	'tree_satuan' => 'Ls',
			// 	'tree_parent_id' => NULL,
			// 	'tree_parent_kode' => '2.1',
			// 	'volume' => 1
			// ),	
			array(
				'id_proyek' => $idpro,
				'kode_tree' => '2.2',
				'tree_item' => 'Asuransi',
				'tree_satuan' => 'Ls',
				'tree_parent_id' => NULL,
				'tree_parent_kode' => '2',
				'volume' => 1
			),
			// array(
			// 	'id_proyek' => $idpro,
			// 	'kode_tree' => '2.2.1',
			// 	'tree_item' => 'C.A.R',
			// 	'tree_satuan' => 'Ls',
			// 	'tree_parent_id' => NULL,
			// 	'tree_parent_kode' => '2.2',
			// 	'volume' => 1
			// ),	
			// array(
			// 	'id_proyek' => $idpro,
			// 	'kode_tree' => '2.2.2',
			// 	'tree_item' => 'Asuransi ASTEK',
			// 	'tree_satuan' => 'Ls',
			// 	'tree_parent_id' => NULL,
			// 	'tree_parent_kode' => '2.2',
			// 	'volume' => 1
			// ),		
			array(
				'id_proyek' => $idpro,
				'kode_tree' => '2.3',
				'tree_item' => 'Biaya Umum',
				'tree_satuan' => 'Ls',
				'tree_parent_id' => NULL,
				'tree_parent_kode' => '2',
				'volume' => 1
			)
		);	
		$this->db->insert_batch('simpro_rap_item_tree', $insert_btl);
		$this->update_parent_id_rap($idpro);
	}
	
	function insert_biaya_umum_rap($idtender, $idproyek)
	{
		$q_get_biaya_umum_tender = sprintf("
			SELECT 
				id_proyek_rat,
				icitem,
				satuan_id,
				satuan,
				kode_material,
				icvolume,
				icharga,
				c.detail_material_id
			FROM 
				simpro_t_rat_idc_biaya_umum
				join simpro_tbl_detail_material c on simpro_t_rat_idc_biaya_umum.kode_material = c.detail_material_kode
			WHERE id_proyek_rat = '%d'
		", $idtender);	
		$isdata = $this->db->query($q_get_biaya_umum_tender)->num_rows();
		if($isdata > 0)
		{
			# insert ke tabel 
			$data = $this->db->query($q_get_biaya_umum_tender)->result_array();
			$i = 0;
			foreach($data as $k=>$v)
			{
				$a= $i + 1;
				$kode_tree = '2.3.'.$a;
				$data_rap_tree = array(
					'id_proyek' => $idproyek,
					'kode_tree' => sprintf('%s',$kode_tree),
					'tree_item' => sprintf('%s', $v['icitem']),
					'tree_satuan' => sprintf('%s', $v['satuan']),
					'tree_parent_kode' => '2.3',
					'volume' => 1
				);

				$this->db->insert('simpro_rap_item_tree', $data_rap_tree);

				$rap_item_tree = $this->db->insert_id();

				$last_kode_analisa = $this->get_last_kode_analisa($idproyek);

				$arr_ins_daftar = array(
					'kode_analisa' => $last_kode_analisa,
					'id_kat_analisa' => 10,
					'nama_item' => sprintf('%s', $v['icitem']),
					'id_satuan' => $v['satuan_id'],
					'id_proyek' => $idproyek
				);

				$this->db->insert('simpro_rap_analisa_daftar', $arr_ins_daftar);

				$id_data_analisa = $this->db->insert_id();

				$arr_ins_item_apek = array(
					'id_proyek' => $idproyek,
					'id_data_analisa' => $id_data_analisa,
					'kode_analisa' => $last_kode_analisa,
					'harga' => 0,
					'rap_item_tree' => $rap_item_tree,
					'kode_tree' => $kode_tree
				);

				$this->db->insert('simpro_rap_analisa_item_apek', $arr_ins_item_apek);

				$arr_ins_asat = array(
					'id_data_analisa' => $id_data_analisa,
					'kode_material' => $v['kode_material'],
					'id_detail_material' => $v['detail_material_id'],
					'koefisien' => 1,
					'harga' => $v['icharga'],
					'kode_analisa' => $last_kode_analisa,
					'id_proyek' => $idproyek
				);

				$this->db->insert('simpro_rap_analisa_asat', $arr_ins_asat);

				$this->update_kode_rap($idproyek);

				$i++;
			}
			$this->update_parent_id_rap($idproyek);
		}
	}

	function insert_bank_rap($idtender, $idproyek)
	{
		$nilai_kontrak = $this->get_nilai_kontrak($idtender);

		$q_get_biaya_umum_tender = sprintf("
			SELECT 
				a.id_proyek_rat,
				a.icitem_bank as icitem,
				coalesce(b.satuan_id,0) satuan_id,
				a.satuan,
				a.kode_material,
				1 as volume,
				(a.persentase * $nilai_kontrak) / 100 as icharga,
				c.detail_material_id
			FROM simpro_t_rat_idc_bank a join simpro_tbl_satuan b on trim(lower(a.satuan)) = trim(lower(satuan_nama))
			join simpro_tbl_detail_material c on a.kode_material = c.detail_material_kode
			WHERE 
				a.id_proyek_rat = '%d'
			ORDER BY a.id_rat_idc_bank DESC
		", $idtender);	
		$isdata = $this->db->query($q_get_biaya_umum_tender)->num_rows();
		if($isdata > 0)
		{
			# insert ke tabel 
			$data = $this->db->query($q_get_biaya_umum_tender)->result_array();
			$i = 0;
			foreach($data as $k=>$v)
			{
				$a= $i + 1;
				$kode_tree = '2.1.'.$a;
				$data_rap_tree = array(
					'id_proyek' => $idproyek,
					'kode_tree' => sprintf('%s',$kode_tree),
					'tree_item' => sprintf('%s', $v['icitem']),
					'tree_satuan' => sprintf('%s', $v['satuan']),
					'tree_parent_kode' => '2.1',
					'volume' => 1
				);

				$this->db->insert('simpro_rap_item_tree', $data_rap_tree);

				$rap_item_tree = $this->db->insert_id();

				$last_kode_analisa = $this->get_last_kode_analisa($idproyek);

				$arr_ins_daftar = array(
					'kode_analisa' => $last_kode_analisa,
					'id_kat_analisa' => 10,
					'nama_item' => sprintf('%s', $v['icitem']),
					'id_satuan' => $v['satuan_id'],
					'id_proyek' => $idproyek
				);

				$this->db->insert('simpro_rap_analisa_daftar', $arr_ins_daftar);

				$id_data_analisa = $this->db->insert_id();

				$arr_ins_item_apek = array(
					'id_proyek' => $idproyek,
					'id_data_analisa' => $id_data_analisa,
					'kode_analisa' => $last_kode_analisa,
					'harga' => 0,
					'rap_item_tree' => $rap_item_tree,
					'kode_tree' => $kode_tree
				);

				$this->db->insert('simpro_rap_analisa_item_apek', $arr_ins_item_apek);

				$arr_ins_asat = array(
					'id_data_analisa' => $id_data_analisa,
					'kode_material' => $v['kode_material'],
					'id_detail_material' => $v['detail_material_id'],
					'koefisien' => 1,
					'harga' => $v['icharga'],
					'kode_analisa' => $last_kode_analisa,
					'id_proyek' => $idproyek
				);

				$this->db->insert('simpro_rap_analisa_asat', $arr_ins_asat);

				$this->update_kode_rap($idproyek);

				$i++;
			}
			$this->update_parent_id_rap($idproyek);
		}
	}

	function insert_asuransi_rap($idtender, $idproyek)
	{
		$nilai_kontrak = $this->get_nilai_kontrak($idtender);

		$q_get_biaya_umum_tender = sprintf("
			SELECT 
				a.id_proyek_rat,
				a.icitem_asuransi as icitem,
				coalesce(b.satuan_id,0) satuan_id,
				a.satuan,
				a.kode_material,
				1 volume,
				(a.persentase * $nilai_kontrak) / 100 as icharga,
				c.detail_material_id
			FROM simpro_t_rat_idc_asuransi a join simpro_tbl_satuan b on trim(lower(a.satuan)) = trim(lower(satuan_nama))
			join simpro_tbl_detail_material c on a.kode_material = c.detail_material_kode
			WHERE 
				a.id_proyek_rat = 75
			ORDER BY a.id_rat_idc_asuransi DESC
		", $idtender);	
		$isdata = $this->db->query($q_get_biaya_umum_tender)->num_rows();
		if($isdata > 0)
		{
			# insert ke tabel 
			$data = $this->db->query($q_get_biaya_umum_tender)->result_array();
			$i = 0;
			foreach($data as $k=>$v)
			{
				$a= $i + 1;
				$kode_tree = '2.2.'.$a;
				$data_rap_tree = array(
					'id_proyek' => $idproyek,
					'kode_tree' => sprintf('%s',$kode_tree),
					'tree_item' => sprintf('%s', $v['icitem']),
					'tree_satuan' => sprintf('%s', $v['satuan']),
					'tree_parent_kode' => '2.2',
					'volume' => 1
				);

				$this->db->insert('simpro_rap_item_tree', $data_rap_tree);

				$rap_item_tree = $this->db->insert_id();

				$last_kode_analisa = $this->get_last_kode_analisa($idproyek);

				$arr_ins_daftar = array(
					'kode_analisa' => $last_kode_analisa,
					'id_kat_analisa' => 10,
					'nama_item' => sprintf('%s', $v['icitem']),
					'id_satuan' => $v['satuan_id'],
					'id_proyek' => $idproyek
				);

				$this->db->insert('simpro_rap_analisa_daftar', $arr_ins_daftar);

				$id_data_analisa = $this->db->insert_id();

				$arr_ins_item_apek = array(
					'id_proyek' => $idproyek,
					'id_data_analisa' => $id_data_analisa,
					'kode_analisa' => $last_kode_analisa,
					'harga' => 0,
					'rap_item_tree' => $rap_item_tree,
					'kode_tree' => $kode_tree
				);

				$this->db->insert('simpro_rap_analisa_item_apek', $arr_ins_item_apek);

				$arr_ins_asat = array(
					'id_data_analisa' => $id_data_analisa,
					'kode_material' => $v['kode_material'],
					'id_detail_material' => $v['detail_material_id'],
					'koefisien' => 1,
					'harga' => $v['icharga'],
					'kode_analisa' => $last_kode_analisa,
					'id_proyek' => $idproyek
				);

				$this->db->insert('simpro_rap_analisa_asat', $arr_ins_asat);

				$this->update_kode_rap($idproyek);

				$i++;
			}
			$this->update_parent_id_rap($idproyek);
		}
	}

	function get_last_kode_analisa($id_proyek)
	{
		if ($id_proyek) {
			$sql = "select right(kode_analisa,(length(kode_analisa) - 2))::numeric + 1 last_kode_analisa from simpro_rap_analisa_daftar where id_proyek = $id_proyek order by last_kode_analisa desc limit 1";
			$q = $this->db->query($sql);
			if ($q->result()) {
				$val = $q->row()->last_kode_analisa;
			} else {
				$val = 1;
			}

			$v = sprintf("AN%03d",$val);
			return $v;
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

	function get_nilai_kontrak($idtender)
	{
		$nilai_kontrak = $this->mdl_rencana->hitung_nilai_kontrak($idtender);
		$nilai_kontrak = ($nilai_kontrak > 0) ? $nilai_kontrak : 0;

		return $nilai_kontrak;
	}

	function insert_detail_analisa_rap($idtender, $idproyek)
	{
		$id_proyek = $idproyek;
		# insert ke tabel simpro_rap_analisa_asat
		$sql_analisa_asat = sprintf("
			INSERT INTO simpro_rap_analisa_asat(kode_material, id_detail_material,koefisien, harga, kode_analisa, id_proyek, keterangan, kode_rap)
			SELECT 
				kode_material, id_detail_material,koefisien, harga, kode_analisa, %d AS id_proyek, keterangan, kode_rap
			FROM simpro_rat_analisa_asat
			WHERE id_tender = %d		
		", $idproyek, $idtender);
		$this->db->query($sql_analisa_asat);
		$update_id_data_analisa = sprintf("
			UPDATE simpro_rap_analisa_asat AS tp 
			SET id_data_analisa = tbl_analisa.id_data_analisa 
			FROM (
				SELECT id_data_analisa, kode_analisa 
				FROM simpro_rap_analisa_daftar 
				WHERE id_proyek = %d 
				ORDER BY kode_analisa ASC
			) AS tbl_analisa 
			WHERE tp.id_proyek = '%d' 
			AND tbl_analisa.kode_analisa = tp.kode_analisa;
		", $id_proyek, $id_proyek);
		$this->db->query($update_id_data_analisa);		
		# insert ke tabel simpro_rap_analisa_apek
		$sql_analisa_apek = sprintf("
			INSERT INTO simpro_rap_analisa_apek(kode_analisa, koefisien, harga, id_proyek, parent_kode_analisa)
			SELECT kode_analisa, koefisien, harga, %d as id_proyek, parent_kode_analisa
			FROM 
			simpro_rat_analisa_apek
			WHERE id_tender = %d
		", $idproyek, $idtender);
		$this->db->query($sql_analisa_apek);
		$update_id_data_analisa_apek = sprintf("
			UPDATE simpro_rap_analisa_apek AS tp 
			SET id_data_analisa = tbl_analisa.id_data_analisa
			FROM (
				SELECT id_data_analisa, kode_analisa
				FROM simpro_rap_analisa_daftar
				WHERE id_proyek = %d
				ORDER BY kode_analisa ASC
			) AS tbl_analisa
			WHERE tp.id_proyek = '%d'
			AND tbl_analisa.kode_analisa = tp.kode_analisa;
		", $id_proyek, $id_proyek);
		$this->db->query($update_id_data_analisa_apek);
		$update_parent_id_data_analisa_apek = sprintf("
			UPDATE simpro_rap_analisa_apek AS tp 
			SET  parent_id_analisa = tbl_analisa.id_data_analisa
			FROM (
				SELECT id_data_analisa, kode_analisa
				FROM simpro_rap_analisa_daftar
				WHERE id_proyek = %d
				ORDER BY kode_analisa ASC
			) AS tbl_analisa
			WHERE tp.id_proyek = '%d'
			AND tbl_analisa.kode_analisa = tp.parent_kode_analisa;
		", $id_proyek, $id_proyek);
		$this->db->query($update_parent_id_data_analisa_apek);
		# insert ke tabel simpro_rap_analisa_item_apek
		$insert_item_apek = sprintf("
			INSERT INTO simpro_rap_analisa_item_apek(id_proyek, kode_analisa, harga, kode_tree)
			SELECT %d as id_proyek, kode_analisa, harga, kode_tree
			FROM 
			simpro_rat_analisa_item_apek
			WHERE id_proyek_rat = %d
		", $idproyek, $idtender);
		$this->db->query($insert_item_apek);
		$update_item_apek = sprintf("
			UPDATE simpro_rap_analisa_item_apek AS tp 
			SET id_data_analisa = tbl_analisa.id_data_analisa
			FROM (
				SELECT id_data_analisa, kode_analisa
				FROM simpro_rap_analisa_daftar
				WHERE id_proyek = %d
				ORDER BY kode_analisa ASC
			) AS tbl_analisa
			WHERE tp.id_proyek = '%d'
			AND tbl_analisa.kode_analisa = tp.kode_analisa;
		", $id_proyek, $id_proyek);
		$this->db->query($update_item_apek);
		$update_rap_item_apek = sprintf("
			UPDATE simpro_rap_analisa_item_apek AS tp 
			SET rap_item_tree = tbl_rap_item.rap_item_tree
			FROM (
				SELECT rap_item_tree, kode_tree
				FROM simpro_rap_item_tree
				WHERE id_proyek = %d
				ORDER BY kode_tree ASC
			) AS tbl_rap_item
			WHERE tp.id_proyek = '%d'
			AND tbl_rap_item.kode_tree = tp.kode_tree;
		", $id_proyek, $id_proyek);
		$this->db->query($update_rap_item_apek);
	}
	
	function update_kode_rap_bl($idpro)
	{
		$sql = sprintf("
				SELECT 
					rap_item_tree, id_proyek, kode_tree, tree_parent_id, tree_parent_kode 
				FROM simpro_rap_item_tree 
				WHERE id_proyek = '%d' 
					AND kode_tree IS NOT NULL
				ORDER BY LENGTH(kode_tree) DESC
				", $idpro);
		$data = $this->db->query($sql)->result_array();
		foreach($data as $k=>$v)
		{
			$upd = '1.' . $v['kode_tree'];
			$pid = isset($v['tree_parent_kode']) ? '1.'.$v['tree_parent_kode'] : '1'; 
			$data = array(
				'id_proyek' => $v['id_proyek'],
				'tree_parent_kode' => $pid,
				'kode_tree' => $upd
			);
			if($v['tree_parent_id'] <> 0)
			{
				$this->db->where('rap_item_tree', $v['rap_item_tree']);
				$this->db->update('simpro_rap_item_tree', $data);
			}
		}
		/*
		AND kode_tree <> '1'
		$this->db->query(sprintf("UPDATE simpro_rap_item_tree set tree_parent_kode = '1', volume=1 WHERE tree_parent_kode IS NULL AND id_proyek = '%d' AND kode_tree <> '1'", $idpro));		
		*/
	}
	
	function cetak_raba($id)
	{	
		$dp = $this->mdl_rencana->get_data_tender($id);
		$raba = $this->mdl_analisa->rab_raba($id);
		$data = array(
			'idtender' => $id,
			'data_proyek' => $dp['data'],
			'raba' => $raba['data'],
			'total_raba' => $this->total_raba($id),
			'persen_raba' => $this->persen_raba($id)			
		);
		$this->load->view('print_raba', $data);
	}
	
	function raba_to_xls($idtender)
	{
		$this->load->library('excel');
		$dp = $this->mdl_rencana->get_data_tender($idtender);
		$rabas = $this->mdl_analisa->rab_raba($idtender);
		$raba = $rabas['data'];
		$total_raba = $this->total_raba($idtender);
		$persen_raba = $this->persen_raba($idtender);
		$data = array(
			'idtender' => $idtender,
			'data_proyek' => $dp['data']
		);
			
		$nama_proyek = $data['data_proyek']['nama_proyek'];
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('raba');
		$this->excel->getActiveSheet()->setCellValue('A1', $nama_proyek);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		 
		
		# informasi data umum proyek
		$data_proyek = array(
			'Divisi' => $data['data_proyek']['divisi_name'],
			'Proyek' => $data['data_proyek']['nama_proyek'], 
			'Pagu' => $data['data_proyek']['nilai_pagu_proyek'], 
			'Nilai Penawaran' => $data['data_proyek']['nilai_penawaran'], 
			'Nilai Kontrak (excl. PPN)' => $data['data_proyek']['nilai_kontrak_excl_ppn'], 
			'Nilai Kontrak (incl. PPN)' => $data['data_proyek']['nilai_kontrak_ppn'], 
			'Waktu Pelaksanaan' => $data['data_proyek']['waktu_pelaksanaan']
			);
		
		$a = 3;;
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
		if(count($raba) > 0)
		{
			$i = 1;
			$subbidang = "";
			$subtotal = 0;
			$nextsub = "";
			$start_A = 12;

			for($a=0; $a < count($raba); $a++)
			{
				$sub = $raba[$a]['simpro_tbl_subbidang'];
				if($sub <> $subbidang)
				{				
					$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A),$raba[$a]['simpro_tbl_subbidang']);
					$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:H%d', $start_A, $start_A));
					$subbidang = $sub;
					$subtotal = $raba[$a]['subtotal'];
					$start_A++;					
				} else 
				{
					$subtotal = $subtotal + $raba[$a]['subtotal'];
				}
				
				$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $start_A), $i);
				$this->excel->getActiveSheet()->setCellValue(sprintf('B%d', $start_A), $raba[$a]['kode_rap']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('C%d', $start_A), $raba[$a]['kd_material']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('D%d', $start_A), $raba[$a]['detail_material_nama']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('E%d', $start_A), $raba[$a]['detail_material_satuan']);
				$this->excel->getActiveSheet()->setCellValue(sprintf('F%d', $start_A), number_format($raba[$a]['total_volume'],2));
				$this->excel->getActiveSheet()->setCellValue(sprintf('G%d', $start_A), number_format($raba[$a]['harga'],2));
				$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $start_A), number_format($raba[$a]['subtotal'],2));
				
				$nextsub = isset($raba[$a+1]['simpro_tbl_subbidang']) ? $raba[$a+1]['simpro_tbl_subbidang'] : '';
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
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $lasta),'TOTAL RAB(A)');
			$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $lasta),$total_raba);
			$this->excel->getActiveSheet()->mergeCells(sprintf('A%d:G%d', $lasta+1, $lasta+1));		
			$this->excel->getActiveSheet()->getStyle(sprintf('A%d', $lasta+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$this->excel->getActiveSheet()->setCellValue(sprintf('A%d', $lasta+1),'PERSENTASE TERHADAP KONTRAK');
			$this->excel->getActiveSheet()->setCellValue(sprintf('H%d', $lasta+1),$persen_raba);
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
		$filename = sprintf('raba-%s.xlsx', trim($nama_proyek)); 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="'.$filename.'"'); 
		header('Cache-Control: max-age=0'); 
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
		$objWriter->save('php://output');								
	}	
	
	function insert_vol_harga_rab($idtender)
	{
		$sql = "
			INSERT INTO simpro_rat_rab_item_tree(rat_item_tree, volume, harga_rat, volume_rat,id_tender)
			(
			SELECT 
				simpro_rat_item_tree.rat_item_tree,
				simpro_rat_item_tree.volume,
				tbl_kode_analisa.harga,
				simpro_rat_item_tree.volume,
				simpro_rat_item_tree.id_proyek_rat
			FROM simpro_rat_item_tree
			INNER JOIN simpro_rat_analisa_item_apek ON simpro_rat_analisa_item_apek.id_proyek_rat = simpro_rat_item_tree.id_proyek_rat AND simpro_rat_analisa_item_apek.rat_item_tree = simpro_rat_item_tree.rat_item_tree
			LEFT JOIN (
				SELECT id_tender, kode_analisa, SUM(harga) AS harga FROM (
						(
							SELECT id_tender, kode_analisa, 
							COALESCE(SUM(harga),0) AS harga 
							FROM simpro_rat_analisa_asat
							GROUP BY id_tender, kode_analisa
						)
						UNION ALL
						(
							SELECT 
								simpro_rat_analisa_apek.id_tender,
								simpro_rat_analisa_apek.parent_kode_analisa, 
								SUM(simpro_rat_analisa_asat.harga) * simpro_rat_analisa_apek.koefisien AS harga
							FROM simpro_rat_analisa_apek
							INNER JOIN simpro_rat_analisa_asat ON (simpro_rat_analisa_asat.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND simpro_rat_analisa_asat.id_tender = simpro_rat_analisa_apek.id_tender)
							GROUP BY simpro_rat_analisa_apek.id_tender, simpro_rat_analisa_apek.parent_kode_analisa,simpro_rat_analisa_apek.koefisien 
						)	
				) as tbl_analisa
				GROUP BY id_tender, kode_analisa
			) AS tbl_kode_analisa ON tbl_kode_analisa.kode_analisa = simpro_rat_analisa_item_apek.kode_analisa AND tbl_kode_analisa.id_tender = simpro_rat_analisa_item_apek.id_proyek_rat
			WHERE simpro_rat_item_tree.id_proyek_rat = ".$idtender."
			AND simpro_rat_item_tree.rat_item_tree NOT IN (SELECT rat_item_tree FROM simpro_rat_rab_item_tree)
			)		
		";
		$this->db->query($sql);
	}
	
	function reset_data_rapa($idpro)
	{
		/*
		TRUNCATE TABLE simpro_rab_approve;
		TRUNCATE TABLE simpro_rap_analisa_apek;
		TRUNCATE TABLE simpro_rap_analisa_asat;
		TRUNCATE TABLE simpro_rap_analisa_daftar;
		TRUNCATE TABLE simpro_rap_analisa_item_apek;
		TRUNCATE TABLE simpro_rap_item_tree;
		DELETE FROM simpro_tbl_proyek where proyek_id in (17,18,19);	
		*/
	}
	
    function callbackFunction($value, $key) {
        echo "$key: $value<br />\n";
    }
	
    function printArray($foo) {
        array_walk_recursive($foo, array($this, 'callbackFunction'));
    }
		
	private function _dump($d)
	{
		print('<pre>');
		print_r($d);
		print('</pre>');
	}

	function get_session()
	{
		$this->_dump($this->session->all_userdata());
		$this->_dump($_SESSION);
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
	
	function export_rab($page="")
	{
		$idpro = $this->session->userdata('id_tender'); 

		if ($page=="") {

		} else {

			$nama_proyek = $this->db->query("select nama_proyek from simpro_m_rat_proyek_tender where id_proyek_rat = $idpro")
			->row()->nama_proyek;

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
			$this->excel->getActiveSheet()->setCellValue('F4', 'Harga RAT');
			$this->excel->getActiveSheet()->setCellValue('G4', 'Subtotal RAT');
			$this->excel->getActiveSheet()->setCellValue('H4', 'Volume RAB');
			$this->excel->getActiveSheet()->setCellValue('I4', 'Harga RAB');
			$this->excel->getActiveSheet()->setCellValue('J4', 'Subtotal RAB');
			$this->excel->getActiveSheet()->setCellValue('K4', 'Selisih');

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

			switch ($page) {
				case 'rab':					

					$this->db->query("TRUNCATE TABLE simpro_tmp_print_pekerjaan_rab_pengajuan");

					$q_rapa = $this->get_data_print($idpro);

					$x = 5;

					$tot = 0;
					$tot_rab = 0;
					$tot_selisih = 0;
					
					if ($q_rapa) {
						foreach ($q_rapa as $row) {
							$kode_tree = $row->kode_tree;
							$kode_analisa = $row->kode_analisa;
							$tree_item = $row->tree_item;
							$tree_satuan = $row->tree_satuan;
							$volume = $row->volume;
							$hrg = $row->hrg;
							$sub = $row->sub;
							$volume_rab = $row->volume_rab;
							$hrg_rab = $row->hrg_rab;
							$sub_rab = $row->sub_rab;
							$selisih = $row->selisih;

							$this->excel->getActiveSheet()->setCellValueExplicit('A'.$x, $kode_tree, PHPExcel_Cell_DataType::TYPE_STRING);
							$this->excel->getActiveSheet()->setCellValue('B'.$x, $kode_analisa);
							$this->excel->getActiveSheet()->setCellValue('C'.$x, $tree_item);
							$this->excel->getActiveSheet()->setCellValue('D'.$x, $tree_satuan);
							$this->excel->getActiveSheet()->setCellValue('E'.$x, $volume);
							if (count(explode(".", $kode_tree)) == 1) {
								$this->excel->getActiveSheet()->getStyle('F'.$x)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('G'.$x)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('I'.$x)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('J'.$x)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('K'.$x)->getFont()->setBold(true);

								$tot = $tot + $sub;
								$tot_rab = $tot_rab + $sub_rab;
								$tot_selisih = $tot_selisih + $selisih;
							}
							$this->excel->getActiveSheet()->setCellValue('F'.$x, $hrg);
							$this->excel->getActiveSheet()->setCellValue('G'.$x, $sub);
							$this->excel->getActiveSheet()->setCellValue('H'.$x, $volume_rab);
							$this->excel->getActiveSheet()->setCellValue('I'.$x, $hrg_rab);
							$this->excel->getActiveSheet()->setCellValue('J'.$x, $sub_rab);
							$this->excel->getActiveSheet()->setCellValue('K'.$x, $selisih);

							$x++;
						}

					} 

					$this->excel->getActiveSheet()->setCellValue('A'.$x, 'Total RAT');
					$this->excel->getActiveSheet()->setCellValue('G'.$x, $tot);
					$this->excel->getActiveSheet()->setCellValue('H'.$x, 'Total RAB / Selisih');
					$this->excel->getActiveSheet()->setCellValue('J'.$x, $tot_rab);
					$this->excel->getActiveSheet()->setCellValue('K'.$x, $tot_selisih);
					$this->excel->getActiveSheet()->getStyle('A'.$x.':K'.$x)->getFont()->setBold(true);
	
					$this->excel->getActiveSheet()->getStyle('A4:K'.$x)->applyFromArray($styleArray);
					unset($styleArray);
	
					$this->excel->getActiveSheet()->mergeCells('A'.$x.':F'.$x);
					$this->excel->getActiveSheet()->mergeCells('H'.$x.':I'.$x);
				break;
			}

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
			$this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

			$this->excel->getActiveSheet()->getStyle('E1:K'.$this->excel->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00'); 

			$this->excel->getActiveSheet()->getStyle('A1:K4')->applyFromArray($styleArray1);
			unset($styleArray1);

			$this->excel->getActiveSheet()->getStyle('C1:C'.$this->excel->getActiveSheet()->getHighestRow())
    		->getAlignment()->setWrapText(true);

			$this->excel->getActiveSheet()->mergeCells('A1:K1');
			$this->excel->getActiveSheet()->mergeCells('A2:K2');

			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$this->db->query("TRUNCATE TABLE simpro_tmp_print_pekerjaan_rab_pengajuan");

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
		$arr = $this->tree($idpro, $depth=0,$param="");

		$this->get_array($arr);

		$q_data = $this->db->query('select * from simpro_tmp_print_pekerjaan_rab_pengajuan order by id_print');

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
					'sub' => $row['subtotal'],
					'volume_rab' => $row['volume_rab'],
					'hrg_rab' => $row['harga_rab'],
					'sub_rab' => $row['subtotal_rab'],
					'selisih' => $row['selisih']
				);

				$this->db->insert('simpro_tmp_print_pekerjaan_rab_pengajuan',$data);
				// fwrite($handle, $dat);			

				if (isset($row['children'])) {
					$this->get_array($row['children']);
				}
			}
		}
	}

	function print_data_analisa($page="")
	{
		$idpro = $this->session->userdata('id_tender'); 

		if ($page=="") {

		} else {

			$nama_proyek = $this->db->query("select nama_proyek from simpro_m_rat_proyek_tender where id_proyek_rat = $idpro")
			->row()->nama_proyek;

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

			$this->excel->getActiveSheet()->mergeCells('A1:J1');
			$this->excel->getActiveSheet()->mergeCells('A2:J2');

			$get_item_pekerjaan = $this->db->query("select 
					a.kode_tree,
					a.tree_item,
					b.kode_analisa,
					c.*
					from 
					simpro_rat_item_tree a 
					join simpro_rat_analisa_item_apek b 
					on a.rat_item_tree = b.rat_item_tree
					join
					(
					SELECT 
						case when simpro_tbl_subbidang.subbidang_kode isnull
						then 'AN'
						else simpro_tbl_subbidang.subbidang_kode
						end,
						case when simpro_tbl_subbidang.subbidang_name isnull
						then 'Analisa'
						else simpro_tbl_subbidang.subbidang_name
						end,
						tbl_analisa_satuan.kode_analisa,
						tbl_analisa_satuan.detail_material_kode,
						tbl_analisa_satuan.detail_material_nama,
						tbl_analisa_satuan.detail_material_satuan,
						tbl_analisa_satuan.koefisien,
						tbl_analisa_satuan.harga,
						tbl_analisa_satuan.subtotal,
						simpro_rat_rab_analisa.nilai_pengali,
						simpro_rat_rab_analisa.koefisien_rab,
						simpro_rat_rab_analisa.harga_rab,
						COALESCE(simpro_rat_rab_analisa.harga_rab * simpro_rat_rab_analisa.koefisien_rab, 0) as subtotal_rab				
					FROM (
						(
							SELECT 					
								simpro_rat_analisa_asat.id_analisa_asat,
								simpro_rat_analisa_asat.kode_analisa,
								simpro_rat_analisa_asat.id_tender,						
								(simpro_rat_analisa_asat.kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
								simpro_rat_analisa_daftar.nama_item,
								simpro_rat_analisa_daftar.id_satuan,
								simpro_tbl_satuan.satuan_nama,
								simpro_tbl_detail_material.detail_material_kode, 
								simpro_tbl_detail_material.detail_material_nama, 
								simpro_tbl_detail_material.detail_material_satuan,
								simpro_rat_analisa_asat.harga,
								simpro_rat_analisa_asat.koefisien,
								(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
							FROM 
								simpro_rat_analisa_asat
							LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
							JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_asat.id_tender)
							LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
							WHERE simpro_rat_analisa_asat.id_tender = $idpro
							ORDER BY 
								simpro_rat_analisa_asat.kode_analisa,
								simpro_tbl_detail_material.detail_material_kode				
							ASC
						)
						UNION ALL 
						(
							SELECT 
								simpro_rat_analisa_apek.id_analisa_apek AS id_analisa_asat,
								simpro_rat_analisa_apek.parent_kode_analisa as kode_analisa,
								simpro_rat_analisa_apek.id_tender,
								(simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
								ad.nama_item AS nama_item,
								simpro_rat_analisa_daftar.id_satuan,
								simpro_tbl_satuan.satuan_nama,
								simpro_rat_analisa_apek.kode_analisa AS detail_material_kode,
								ad.nama_item as detail_material_nama,
								simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
								COALESCE(tbl_harga.harga,0) AS harga,
								simpro_rat_analisa_apek.koefisien,
								COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
							FROM 
								simpro_rat_analisa_apek
							INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
							INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_apek.id_tender)			
							INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
							LEFT JOIN (
								SELECT 
									DISTINCT ON(kode_analisa)
									kode_analisa,
									SUM(harga * koefisien) AS harga
								FROM simpro_rat_analisa_asat 
								WHERE id_tender = $idpro
								GROUP BY kode_analisa			
							) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
							WHERE simpro_rat_analisa_apek.id_tender = $idpro
							ORDER BY 
								simpro_rat_analisa_apek.parent_kode_analisa,				
								simpro_rat_analisa_apek.kode_analisa
							ASC					
						)		
					) as tbl_analisa_satuan 
					LEFT JOIN simpro_rat_rab_analisa ON simpro_rat_rab_analisa.id_simpro_rat_analisa = tbl_analisa_satuan.id_analisa_asat AND simpro_rat_rab_analisa.id_tender = tbl_analisa_satuan.id_tender
					left join simpro_tbl_subbidang on simpro_tbl_subbidang.subbidang_kode = left(tbl_analisa_satuan.detail_material_kode,3)
					order by kode_analisa
					) c
					on c.kode_analisa = b.kode_analisa
					where a.id_proyek_rat = $idpro
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
			$jml_rab = 0;

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
							$this->excel->getActiveSheet()->setCellValue('A'.$x, 'TOTAL RAT');
							$this->excel->getActiveSheet()->setCellValue('F'.$x, $jml);
							
							$this->excel->getActiveSheet()->mergeCells('G'.$x.':I'.$x);
							$this->excel->getActiveSheet()->setCellValue('G'.$x, 'TOTAL RAB');
							$this->excel->getActiveSheet()->setCellValue('J'.$x, $jml_rab);

							$this->excel->getActiveSheet()->getStyle('A'.$x.':J'.$x)->applyFromArray($styleArrayBorder);
							unset($styleArray);

							$this->excel->getActiveSheet()->getStyle('A'.$x.':J'.$x)->getFont()->setBold(true);
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
					$dm_pengali_rab = $rows->nilai_pengali;
					$dm_koefisien_rab = $rows->koefisien_rab;
					$dm_harga_rab = $rows->harga_rab;
					$dm_jumlah_rab = $rows->subtotal_rab;

					$this->excel->getActiveSheet()->getStyle('A'.$item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					// $this->excel->getActiveSheet()->getStyle('A'.$subbidang_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A'.$item)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->mergeCells('A'.$item.':J'.$item);
					$this->excel->getActiveSheet()->setCellValue('A'.$item, $kode_tree.' '.$item_pekerjaan);

					$this->excel->getActiveSheet()->mergeCells('A'.$subbidang_no.':J'.$subbidang_no);
					$this->excel->getActiveSheet()->setCellValue('A'.$subbidang_no, $subbidang);
					$this->excel->getActiveSheet()->getStyle('A'.$subbidang_no.':J'.$subbidang_no)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->setCellValue('A'.$judul, 'NO');
					$this->excel->getActiveSheet()->setCellValue('B'.$judul, 'SUMBER DAYA');
					$this->excel->getActiveSheet()->setCellValue('C'.$judul, 'SATUAN');
					$this->excel->getActiveSheet()->setCellValue('D'.$judul, 'HARGA SATUAN');
					$this->excel->getActiveSheet()->setCellValue('E'.$judul, 'KOEFISIEN');
					$this->excel->getActiveSheet()->setCellValue('F'.$judul, 'JUMLAH');
					$this->excel->getActiveSheet()->setCellValue('G'.$judul, 'Nilai Pengali');
					$this->excel->getActiveSheet()->setCellValue('H'.$judul, 'HARGA SATUAN RAB');
					$this->excel->getActiveSheet()->setCellValue('I'.$judul, 'KOEFISIEN RAB');
					$this->excel->getActiveSheet()->setCellValue('J'.$judul, 'JUMLAH RAB');
					$this->excel->getActiveSheet()->getStyle('A'.$judul.':J'.$judul)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->setCellValueExplicit('A'.$x, '1.'.$n, PHPExcel_Cell_DataType::TYPE_STRING);
					$this->excel->getActiveSheet()->setCellValue('B'.$x, $dm_nama.'('.$dm_kode.')');
					$this->excel->getActiveSheet()->setCellValue('C'.$x, $dm_satuan);
					$this->excel->getActiveSheet()->setCellValue('D'.$x, $dm_harga);
					$this->excel->getActiveSheet()->setCellValue('E'.$x, $dm_koefisien);
					$this->excel->getActiveSheet()->setCellValue('F'.$x, $dm_jumlah);
					$this->excel->getActiveSheet()->setCellValue('G'.$x, $dm_pengali_rab);
					$this->excel->getActiveSheet()->setCellValue('H'.$x, $dm_harga_rab);
					$this->excel->getActiveSheet()->setCellValue('I'.$x, $dm_koefisien_rab);
					$this->excel->getActiveSheet()->setCellValue('J'.$x, $dm_jumlah_rab);

					$this->excel->getActiveSheet()->getStyle('A'.$x.':J'.$x)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$item.':J'.$item)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$subbidang_no.':J'.$subbidang_no)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					$this->excel->getActiveSheet()->getStyle('A'.$judul.':J'.$judul)->applyFromArray($styleArrayBorder);
					unset($styleArray);
					// $this->excel->getActiveSheet()->setCellValue('G'.$x, $kode_tree);
					// $this->excel->getActiveSheet()->setCellValue('H'.$x, $subbidang);

					$x++;
					$n++;
					$na++;
					$jml+=$dm_jumlah;
					$jml_rab+=$dm_jumlah_rab;

				}

				$this->excel->getActiveSheet()->mergeCells('A'.$x.':E'.$x);
				$this->excel->getActiveSheet()->setCellValue('A'.$x, 'TOTAL RAT');
				$this->excel->getActiveSheet()->setCellValue('F'.$x, $jml);

				$this->excel->getActiveSheet()->mergeCells('G'.$x.':I'.$x);
				$this->excel->getActiveSheet()->setCellValue('G'.$x, 'TOTAL RAB');
				$this->excel->getActiveSheet()->setCellValue('J'.$x, $jml_rab);

				$this->excel->getActiveSheet()->getStyle('A'.$x.':J'.$x)->applyFromArray($styleArrayBorder);
				unset($styleArray);

				$this->excel->getActiveSheet()->getStyle('A'.$x.':J'.$x)->getFont()->setBold(true);
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

			$this->excel->getActiveSheet()->getStyle('A1:J'.$x)->applyFromArray($styleArray);
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
			$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

			$this->excel->getActiveSheet()->getStyle('D1:J'.$this->excel->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00'); 


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

	function default_rab()
	{
		$idtender = $this->session->userdata('id_tender');
		$pengali = 1;
		$sql_insert = "
				select					
					a.id_analisa_asat,
					a.id_data_analisa, 
					a.koefisien, 
					a.id_tender,
					a.harga, 
					a.detail_material_kode as kode_material,					
					simpro_rat_rab_analisa.id_simpro_rat_analisa, 
					simpro_rat_rab_analisa.kode_analisa
					from (select * from (SELECT 					
						simpro_rat_analisa_asat.id_data_analisa,		
						simpro_rat_analisa_asat.id_analisa_asat,
						simpro_rat_analisa_asat.kode_analisa,
						simpro_rat_analisa_asat.id_tender,						
						(simpro_rat_analisa_asat.kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						simpro_rat_analisa_daftar.nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_tbl_detail_material.detail_material_kode, 
						simpro_tbl_detail_material.detail_material_nama, 
						simpro_tbl_detail_material.detail_material_satuan,
						simpro_rat_analisa_asat.harga,
						simpro_rat_analisa_asat.koefisien,
						(simpro_rat_analisa_asat.harga * simpro_rat_analisa_asat.koefisien) AS subtotal
					FROM 
						simpro_rat_analisa_asat
					LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rat_analisa_asat.kode_material
					JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_asat.kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_asat.id_tender)
					LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					WHERE simpro_rat_analisa_asat.id_tender = $idtender
					ORDER BY 
						simpro_rat_analisa_asat.kode_analisa,
						simpro_tbl_detail_material.detail_material_kode				
					ASC
				) c
				UNION ALL 
				(
					SELECT 		
						simpro_rat_analisa_apek.id_data_analisa,
						simpro_rat_analisa_apek.id_analisa_apek AS id_analisa_asat,
						simpro_rat_analisa_apek.kode_analisa,
						simpro_rat_analisa_apek.id_tender,
						(simpro_rat_analisa_apek.parent_kode_analisa || ' - ' || simpro_rat_analisa_daftar.nama_item || ' (' || simpro_tbl_satuan.satuan_nama || ')') AS asat_kat, 
						ad.nama_item AS nama_item,
						simpro_rat_analisa_daftar.id_satuan,
						simpro_tbl_satuan.satuan_nama,
						simpro_rat_analisa_apek.kode_analisa AS detail_material_kode,
						ad.nama_item as detail_material_nama,
						simpro_tbl_satuan.satuan_nama AS detail_material_satuan,
						COALESCE(tbl_harga.harga,0) AS harga,
						simpro_rat_analisa_apek.koefisien,
						COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
					FROM 
						simpro_rat_analisa_apek
					INNER JOIN simpro_rat_analisa_daftar ad ON (ad.kode_analisa = simpro_rat_analisa_apek.kode_analisa AND ad.id_tender = simpro_rat_analisa_apek.id_tender)
					INNER JOIN simpro_rat_analisa_daftar ON (simpro_rat_analisa_daftar.kode_analisa = simpro_rat_analisa_apek.parent_kode_analisa AND simpro_rat_analisa_daftar.id_tender = simpro_rat_analisa_apek.id_tender)			
					INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rat_analisa_daftar.id_satuan
					LEFT JOIN (
						SELECT 
							DISTINCT ON(kode_analisa)
							kode_analisa,
							SUM(harga * koefisien) AS harga
						FROM simpro_rat_analisa_asat 
						WHERE id_tender = $idtender
						GROUP BY kode_analisa			
					) as tbl_harga ON tbl_harga.kode_analisa = simpro_rat_analisa_apek.kode_analisa			
					WHERE simpro_rat_analisa_apek.id_tender = $idtender
					ORDER BY 
						simpro_rat_analisa_apek.parent_kode_analisa,				
						simpro_rat_analisa_apek.kode_analisa
					ASC					
				)) a
				LEFT JOIN simpro_rat_rab_analisa ON (simpro_rat_rab_analisa.id_simpro_rat_analisa = a.id_analisa_asat AND simpro_rat_rab_analisa.id_tender = a.id_tender)
				WHERE a.id_tender =  $idtender
				AND simpro_rat_rab_analisa.id_simpro_rat_analisa IS NULL
			";

			$data = $this->db->query($sql_insert)->result_array();
			foreach($data as $k=>$v)
			{
				$insert = array(
						'id_simpro_rat_analisa' => $v['id_analisa_asat'],
						'id_tender' => $v['id_tender'],
						'harga_rat' => $v['harga'],
						'koefisien_rat' => $v['koefisien'],
						'nilai_pengali' => $pengali,
						'koefisien_rab' => $v['koefisien'],
						'kode_analisa' => $v['kode_material'],
						'harga_rab' => ($pengali * $v['harga'])					
				);
				$this->db->insert('simpro_rat_rab_analisa', $insert);
			}
	}
}