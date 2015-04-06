<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Daftar_Analisa extends MX_Controller {

	var $idpro = '';
	
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('main/login');				
		$this->load->model('mdl_analisa');
		$this->load->model('mdl_rencana');		
	}
	
	public function index()
	{
		$this->load->view('home_rab');
	}

	public function home($idtender)
	{
		$_SESSION['id_tender_analisa'] = $idtender;
		$data_tender = $this->mdl_rencana->get_data_tender($idtender);
		$data = array(
			'id_tender' => $idtender,
			'data_tender' => $data_tender['data']
		);
		$this->load->view('daftar_analisa', $data);
	}
			
	function set_analisa_itemid()
	{
		if($this->input->post('id_tender') && $this->input->post('kode_analisa'))
		{
			/* CI session problem, so using native PHP session */
			$this->session->set_userdata('id_tender_analisa', $this->input->post('id_tender'));
			$this->session->set_userdata('id_data_analisa', $this->input->post('id_data_analisa'));
			$this->session->set_userdata('kode_analisa', $this->input->post('kode_analisa'));
			$_SESSION['id_tender_analisa'] = $this->input->post('id_tender');
			$_SESSION['id_data_analisa'] = $this->input->post('id_data_analisa');
			$_SESSION['kode_analisa'] = $this->input->post('kode_analisa');
		}
	}	

	function get_analisa_itemid()
	{
		if(isset($_SESSION['id_tender_analisa']) && isset($_SESSION['kode_analisa']))
		{
			$data = array(
				'id_tender' => $_SESSION['id_tender_analisa'],
				'id_data_analisa' => $_SESSION['id_data_analisa'],
				'kode_analisa' => $_SESSION['kode_analisa']
			);
			return $data;
		}
	}	
	
	function set_idtender()
	{
		if($this->input->post('id_tender'))
		{
			$this->session->set_userdata('id_tender_analisa', $this->input->post('id_tender'));
			$_SESSION['id_tender_analisa'] = $this->input->post('id_tender');
		}
	}	
	
	function get_idtender()
	{
		// isset($_SESSION['id_tender_analisa']) || 
		if(isset($_SESSION['idtender']) || $_SESSION['sess_id_tender'])
		{
			//return $_SESSION['id_tender_analisa'];
			return (isset($_SESSION['sess_id_tender'])) ? $_SESSION['sess_id_tender'] : $_SESSION['idtender'];
		} else return false;
	}

	function clear_search_data_analisa()
	{
		unset($_SESSION['id_kat_analisa_daftar']);
		unset($_SESSION['id_kat_analisa_daftar_apek']);
	}
	
	public function get_daftar_analisa($id)
	{
		$idtender = isset($id) ? $id : $this->get_idtender();	
		//$idtender = ($this->get_idtender() > 0) ? $this->get_idtender() : $id;
		if($this->input->get('id_kat_analisa'))
		{
			$_SESSION['id_kat_analisa_daftar'] = $this->input->get('id_kat_analisa');
			$idkat = $this->input->get('id_kat_analisa');
		} else {
			$idkat = isset($_SESSION['id_kat_analisa_daftar']) ? $_SESSION['id_kat_analisa_daftar'] : FALSE;
		}
		$data = $this->mdl_analisa->get_daftar_analisa_pekerjaan($idtender, $idkat);
		$this->_out($data);
	}

	public function get_daftar_analisa_koef($id)
	{
		$idtender = isset($id) ? $id : $this->get_idtender();	
		//$idtender = $this->get_idtender(); 
		if($this->input->get('id_kat_analisa'))
		{
			$_SESSION['id_kat_analisa_daftar_apek'] = $this->input->get('id_kat_analisa');
			$idkat = $this->input->get('id_kat_analisa');
		} else {
			$idkat = isset($_SESSION['id_kat_analisa_daftar_apek']) ? $_SESSION['id_kat_analisa_daftar_apek'] : false;
		}		
		$data = $this->mdl_analisa->get_daftar_analisa_pekerjaan_koef($idtender, $idkat);
		$this->_out($data);
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
	
	public function get_daftar_analisa_bahan($idtender)
	{
		$idtender = isset($idtender) ? $idtender : $this->get_idtender();	
		$data = $this->mdl_analisa->get_daftar_analisa_bahan($idtender);
		$this->_out($data);
	}
	
	public function get_asat($idtender)
	{
		$idtender = isset($idtender) ? $idtender : $this->get_idtender();
		$data = $this->mdl_analisa->get_daftar_analisa_satuan($idtender);		
		$this->_out($data);
	}
	
	function delete_asat()
	{
		if($this->input->post('id_analisa_asat'))
		{
			if(substr($this->input->post('kode_analisa'),0,2) == 'AN')
			{
				if($this->db->delete('simpro_rat_analisa_apek',array('id_analisa_apek'=>$this->input->post('id_analisa_asat'))))
					echo "Data berhasil dihapus.";
						else echo "Data GAGAL dihapus!";				
			} else
			{
				if($this->db->delete('simpro_rat_analisa_asat',array('id_analisa_asat'=>$this->input->post('id_analisa_asat'))))
					echo "Data berhasil dihapus.";
						else echo "Data GAGAL dihapus!";
			}
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
					if($this->db->delete('simpro_rat_analisa_apek',array('id_analisa_apek'=>$v)))
						$success = TRUE;
							else $success = FALSE;
				} else
				{
					if($this->db->delete('simpro_rat_analisa_asat',array('id_analisa_asat'=>$v)))
						$success = TRUE;
							else $success = FALSE;
				}
				$i++;
			}
			if($success) echo "Data Analisa Satuan berhasil dihapus.";
				else echo "Data analisa satuan GAGAL dihapus!";
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
	
	function tambah_apek()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_tender'))
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
					'id_tender' => $this->input->post('id_tender')
				);
				if ($this->db->get_where('simpro_rat_analisa_apek',$var_w)->num_rows() > 0) {
					echo "Analisa Sudah Ditambahkan Sebelumnya..";
					$data = "";
				} else {
					$data[] = array(
						'kode_analisa' => strtoupper($v),
						'id_data_analisa' => $dmid[$i],
						'parent_kode_analisa' => strtoupper($var_analisa['kode_analisa']),
						'parent_id_analisa' => $var_analisa['id_data_analisa'],
						'id_tender' => $this->input->post('id_tender'),
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
							if($this->db->insert_batch('simpro_rat_analisa_apek',$data)) echo "Data telah disimpan.";
							else echo "Data GAGAL disimpan!";
						}
					} else echo "Error: Maksimal dua jenjang level analisa, sistem tidak memperbolehkan melebihi 2 jenjang analisa.";
				} else echo "Tidak boleh memilih kode analisa yang sama!";
			} else echo "Data GAGAL disimpan, parent kode analia Kosong!";
		} else echo "Error - Post Data inComplete!";
	}
	
	private function cek_level_apek($kd_analisa)
	{
		$q_cek_level = sprintf("SELECT * FROM simpro_rat_analisa_asat WHERE kode_analisa = '%s'", $kd_analisa);
		$isdata = $this->db->query($q_cek_level)->num_rows();
		if($isdata >= 1)
			return TRUE;
				else return FALSE;
	}
	
	public function delete_apek()
	{
		if($this->input->post('id_analisa_apek'))
		{
			if($this->db->delete('simpro_rat_analisa_apek', array('id_analisa_apek'=>$this->input->post('id_analisa_apek'))))
				echo "Data berhasil dihapus.";
					else echo "Data GAGAL dihapus!";
		}
	}
	
	function update_harga_asat()
	{
		if($this->input->post('id_tender') && $this->input->post('kode_material'))
		{
			if($this->input->post('harga')) $harga = $this->input->post('harga');
				else $harga = 0;
			$postdata = array(
				'kode_material' => $this->input->post('kode_material'),
				'id_tender' => $this->input->post('id_tender'),
				'keterangan' => $this->input->post('keterangan'),
				'harga' => $harga
			);
			$this->db->where('kode_material', $this->input->post('kode_material'));
			$this->db->where('id_tender', $this->input->post('id_tender'));
			if($this->db->update('simpro_rat_analisa_asat', $postdata))
			{
				if($this->update_kode_rap($this->input->post('id_tender'))) echo "Harga berhasil diupdate.";
					else echo "GAGAL update kode RAP";
			} else echo "Harga satuan GAGAL diupdate!";
		}
	}
	
	function edit_harga_satuan_asat($idtender)
	{
		$this->update_kode_rap($idtender);
		$idtender = isset($idtender) ? $idtender : $this->get_idtender();			
		$data = $this->mdl_analisa->get_harga_satuan_asat($idtender);
		$this->_out($data);	
	}
	
	function edit_koefisien_apek()
	{
		if($this->input->post('id_analisa_apek'))
		{
			$this->db->where('id_analisa_apek', $this->input->post('id_analisa_apek'));
			if($this->db->update('simpro_rat_analisa_apek', array('koefisien'=>$this->input->post('koefisien'))))
				echo "Data berhasil diupdate.";
					else echo "data GAGAL diupdate!";
		}
	}
	
	function get_data_analisa_pekerjaan($idtender)
	{
		$data = $this->mdl_analisa->get_data_analisa_pekerjaan($idtender);
		$this->_out($data);	
	}

	function get_data_analisa_pekerjaan_copy()
	{
		$id = $this->input->get('id');
		if ($id == '') {
			$id = 0;
		}
		$param = $this->input->get('param');
		$search = $this->input->get('search');
		$data = $this->mdl_analisa->get_data_analisa_pekerjaan_copy($id,$param,$search);
		$this->_out($data);	
	}
	
	function tambah_ansat()
	{
		if($this->input->post('kode_material') && $this->input->post('id_detail_material') 
		&& $this->input->post('koefisien') && $this->input->post('id_tender'))
		{

			$km = explode(',',$this->input->post('kode_material'));
			$dmid = explode(',',$this->input->post('id_detail_material'));
			$koef = explode(',',$this->input->post('koefisien'));
			$var_analisa = $this->get_analisa_itemid();
			$i=0;
			$msg = '';
			$cek_jml = 0;
			foreach($km as $k=>$v)
			{
				$cek_data = $this->db->query("SELECT simpro_tbl_detail_material.detail_material_nama from simpro_rat_analisa_asat join simpro_tbl_detail_material on simpro_rat_analisa_asat.kode_material = simpro_tbl_detail_material.detail_material_kode where simpro_rat_analisa_asat.kode_material = '$v' and simpro_rat_analisa_asat.id_tender = $var_analisa[id_tender] and simpro_rat_analisa_asat.id_data_analisa = $var_analisa[id_data_analisa]");
				
				if ($cek_data->result()) {
					$msg = sprintf("%s,<br>%s",$msg,$cek_data->row()->detail_material_nama);
					$cek_jml++;
				} else {
					$data = array(
						'kode_material' => $v,
						'id_detail_material' => $dmid[$i],
						'id_data_analisa' => $var_analisa['id_data_analisa'],
						'kode_analisa' => $var_analisa['kode_analisa'],
						'id_tender' => $var_analisa['id_tender'],
						'koefisien' => $koef[$i],
						'harga' => 0
					);	
					$this->db->insert('simpro_rat_analisa_asat',$data);
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
			
			// if($this->db->insert_batch('simpro_rat_analisa_asat',$data)) 
			// {
				if($this->update_kode_rap($this->input->post('id_tender'))) echo $m;
					else echo "GAGAL update kode RAP";
			// } else echo "Data GAGAL disimpan!";
		}
	}
	
	function update_kode_rap($idtender)
	{
		$error = FALSE;
		$id = isset($idtender) ? $idtender : $this->get_idtender();
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
			// 		DISTINCT(simpro_rat_analisa_asat.kode_material) as kode_material,
			// 		LEFT(simpro_rat_analisa_asat.kode_material,3),
			// 		simpro_rat_analisa_asat.id_analisa_asat,
			// 		simpro_rat_analisa_kategori.kode_kat,
			// 		COALESCE(simpro_rat_analisa_asat.harga, 0) AS harga
			// 	FROM simpro_rat_analisa_asat
			// 	LEFT JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_rat_analisa_asat.kode_material,3)
			// 	WHERE simpro_rat_analisa_asat.id_tender = %d
			// 		AND simpro_rat_analisa_kategori.kode_kat = '%s'
			// 	GROUP BY simpro_rat_analisa_asat.kode_material, 
			// 		simpro_rat_analisa_kategori.kode_kat, 
			// 		simpro_rat_analisa_asat.harga,
			// 		simpro_rat_analisa_asat.id_analisa_asat
			// 	ORDER BY simpro_rat_analisa_asat.kode_material ASC			
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
			// 		$updata = array('kode_rap'=>$kode);
			// 		$this->db->where('kode_material', $v['kode_material']);
			// 		if(!$this->db->update('simpro_rat_analisa_asat', $updata)) $error = TRUE;
			// 		$i++;
			// 	}
			// }

			$kd_kat = $v['kode_kat'];
			$sql_kode_null = "				select *,
				case when keterangan isnull
				then (select keterangan from simpro_rat_analisa_asat where kode_material = analisa.kode_material and id_tender = analisa.id_tender and keterangan is not null group by keterangan)
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
				when (kode_rap isnull) and (select count(kode_rap) from simpro_rat_analisa_asat where kode_rap is not null and id_tender = analisa_asat.id_tender) = 0
				then (ROW_NUMBER() OVER (ORDER BY 'a')) - coalesce((select count(kode_rap) from simpro_rat_analisa_asat where kode_rap is not null and id_tender = analisa_asat.id_tender),0) + coalesce((select (right(kode_rap,4)::int) as int_rap from simpro_rat_analisa_asat where kode_rap is not null and id_tender = analisa_asat.id_tender order by int_rap desc limit 1),0)
				when (kode_rap isnull) and (select count(kode_rap) from simpro_rat_analisa_asat where kode_rap is not null and id_tender = analisa_asat.id_tender) > 0
				then (ROW_NUMBER() OVER (ORDER BY 'a'))
				end::text as kd_rap_int
				from (SELECT 
					simpro_rat_analisa_asat.kode_material as kode_material,
					simpro_rat_analisa_asat.id_tender,
					simpro_rat_analisa_asat.keterangan,
					case when (select count(a.kode_material) from simpro_rat_analisa_asat a where a.kode_material = simpro_rat_analisa_asat.kode_material and a.id_tender = id_tender) > 1
					then right((select b.kode_rap from simpro_rat_analisa_asat b where b.kode_material = simpro_rat_analisa_asat.kode_material and b.id_tender = simpro_rat_analisa_asat.id_tender and b.kode_rap is not null group by b.kode_rap),4)
					else right(kode_rap,4)
					end kode_rap_numb,
					case when (select count(a.kode_material) from simpro_rat_analisa_asat a where a.kode_material = simpro_rat_analisa_asat.kode_material and a.id_tender = id_tender) > 1
					then (select b.kode_rap from simpro_rat_analisa_asat b where b.kode_material = simpro_rat_analisa_asat.kode_material and b.id_tender = simpro_rat_analisa_asat.id_tender and b.kode_rap is not null group by b.kode_rap)
					else kode_rap
					end kode_rap,
					simpro_rat_analisa_kategori.kode_kat
				FROM simpro_rat_analisa_asat
				JOIN simpro_rat_analisa_kategori ON simpro_rat_analisa_kategori.subbidang_kode = LEFT(simpro_rat_analisa_asat.kode_material,3)
				WHERE simpro_rat_analisa_asat.id_tender = $id
					AND simpro_rat_analisa_kategori.kode_kat = '$kd_kat'
				) analisa_asat
				GROUP BY kode_material, 
					keterangan,
					kode_kat,
					kode_rap,
					kode_rap_numb,
					id_tender
				order by kode_rap) analisa";

			$q_cek_material = $this->db->query($sql_kode_null);

			if ($q_cek_material->result()) {
				foreach ($q_cek_material->result() as $r) {
					$updata = array('kode_rap'=>$r->rap_kd,'keterangan'=>$r->keterangan_ad);
					$this->db->where(array('kode_material' => $r->kode_material,'id_tender' => $r->id_tender));
					if(!$this->db->update('simpro_rat_analisa_asat', $updata)) $error = TRUE;
				}
			}			
		}
		if(!$error) return true;
			else return false;
	}
	
	function tambah_daftar_analisa()
	{
		if($this->input->post('id_tender') && $this->input->post('kode_analisa'))
		{
			$is_analisa_exists = $this->db->query(sprintf("SELECT * FROM simpro_rat_analisa_daftar WHERE kode_analisa ='%s' AND id_tender = '%d'", $this->input->post('kode_analisa'), $this->input->post('id_tender')))->num_rows();
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
				FROM simpro_rat_analisa_daftar
				WHERE id_tender = '%d'", $this->input->post('id_tender'));
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
				'id_tender' => $this->input->post('id_tender'),
				'id_kat_analisa' => $kategori,
				'kode_analisa' => $lastkode, 
				'nama_item' => $this->input->post('nama_item'),
				'id_satuan' => $satuan
			);			
			
			if($is_analisa_exists)
			{
				$wdu = array(
					'id_tender'=>$this->input->post('id_tender'),
					'kode_analisa'=>$this->input->post('kode_analisa')
				);
				$this->db->update('simpro_rat_analisa_daftar', $data, $wdu);			
				echo "Data berhasil diupdate.";
			} else
			{				
				$this->db->insert('simpro_rat_analisa_daftar',$data);
				echo "Data berhasil ditambah.";
			}		
		} else 
		{
			echo "Update daftar Analisa GAGAL!";
		}
	}
		
		
	function delete_asat_apek()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_tender'))
		{
			$del_asat = $this->db->delete('simpro_rat_analisa_asat', array('kode_analisa'=>$this->input->post('kode_analisa'), 'id_tender'=>$this->input->post('id_tender')));
			$del_apek = $this->db->delete('simpro_rat_analisa_apek', array('parent_kode_analisa'=>$this->input->post('kode_analisa'), 'id_tender'=>$this->input->post('id_tender')));
			// $arr_w = array(
			// 	'kode_analisa' => $this->input->post('kode_analisa'), 
			// 	'id_proyek_rat' => $this->input->post('id_tender')
			// );			
			// $id_item_apek = $this->db->delete('simpro_rat_analisa_item_apek',$arr_w);

			if($del_asat && $del_apek)
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus!";
			
		}
	}
	
	function delete_analisa_pekerjaan()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_tender'))
		{
			$where = array('kode_analisa'=>$this->input->post('kode_analisa'), 'id_tender'=>$this->input->post('id_tender'));
			$del_daftar = $this->db->delete('simpro_rat_analisa_daftar', $where);
			$del_asat = $this->db->delete('simpro_rat_analisa_asat', $where);
			$del_apek = $this->db->delete('simpro_rat_analisa_apek', array('parent_kode_analisa'=>$this->input->post('kode_analisa'), 'id_tender'=>$this->input->post('id_tender')));
			if($del_daftar && $del_asat && $del_apek)
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus!";			
		}
	}
	
	public function get_apek($idtender)	
	{
		$idtender = isset($idtender) ? $idtender : $this->get_idtender();
		//$idtender = $this->get_idtender(); 
		$data = $this->mdl_analisa->get_data_apek($idtender);		
		$this->_out($data);
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
	
	function get_rata($idtender)
	{
		$data = $this->mdl_analisa->rat_rata($idtender);
		$this->_out($data);		
	}
	
	function upload_daftar_analisa()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv|txt';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);					
		$this->db->query("TRUNCATE TABLE simpro_tmp_upload_daftar_analisa");
		
		try {
			if(!$this->upload->do_upload('upload_analisa'))
			{
				throw new Exception(sprintf('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: csv|txt.\nErrors: %s', $this->upload->display_errors()));
			} else
			{
				$data = $this->upload->data();
				$this->db->query("SET CLIENT_ENCODING TO 'SQL_ASCII'");
				$sql = sprintf("COPY simpro_tmp_upload_daftar_analisa FROM '%s' DELIMITER ',' CSV HEADER ", realpath("./uploads/".$data['file_name']));
				if($this->db->query($sql))
				{
					$qdel = sprintf("DELETE FROM simpro_rat_analisa_daftar WHERE id_tender = '%d'",$this->input->post('id_proyek_rat'));
					$this->db->query($qdel);
					$sql_insert = sprintf("
						INSERT INTO simpro_rat_analisa_daftar(kode_analisa, nama_item, id_kat_analisa, id_satuan, id_tender)
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
					", 10, $this->input->post('id_proyek_rat'));
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

	function delete_analisa()
	{
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_tender'))
		{
			#analisa
			$kd_analisa = explode(",",$this->input->post('kode_analisa'));
			$id_analisa = explode(",",$this->input->post('id_data_analisa'));
			$idtender = $this->input->post('id_tender');
			$i=0;
			$success = TRUE;
			foreach($kd_analisa as $k=>$v)
			{
				$arr_w = array(
					'kode_analisa' => $v, 
					'id_proyek_rat' => $idtender
				);			
				$id_item_apek = $this->db->delete('simpro_rat_analisa_item_apek',$arr_w);
				$where = array('kode_analisa'=>$v, 'id_tender'=>$idtender);
				$del_daftar = $this->db->delete('simpro_rat_analisa_daftar', $where);
				$del_asat = $this->db->delete('simpro_rat_analisa_asat', $where);
				$del_asat_apek = $this->db->delete('simpro_rat_analisa_apek', $where);
				$del_apek = $this->db->delete('simpro_rat_analisa_apek', array('parent_kode_analisa'=>$v, 'id_tender'=>$idtender));
				if($del_daftar && $del_asat && $del_apek && $id_item_apek && $del_asat_apek)
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
		if($this->input->post('kode_analisa') && $this->input->post('id_data_analisa') && $this->input->post('id_tender'))
		{
			if(isset($_SESSION['copy_asat']['kode_artikel']))
			{
				$art = explode(",", $_SESSION['copy_asat']['kode_artikel']);
				//$idart = explode(",",$_SESSION['copy_asat']['id_artikel']);
				$koef = explode(",",$_SESSION['copy_asat']['koefisien']);
				$harga = explode(",",$_SESSION['copy_asat']['harga']);
				$id_tender = $_SESSION['copy_asat']['id_tender'];
				
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
							'id_tender' => $id_tender,
							'koefisien' => $koef[$i],
							'harga' => $harga[$i]
						);			
						$i++;
					}
					$a++;
				}		
				
				# insert
				if($this->db->insert_batch('simpro_rat_analisa_asat',$data)) 
				{
					if($this->update_kode_rap($this->input->post('id_tender'))) echo "Data telah disimpan.";
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
			$_SESSION['copy_asat']['id_tender'] = $this->input->post('id_tender');
		}	
	}
	
	function unset_copy_asat()
	{
		unset($_SESSION['copy_asat']);
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

			$this->excel->getActiveSheet()->mergeCells('A1:F1');
			$this->excel->getActiveSheet()->mergeCells('A2:F2');

			$get_item_pekerjaan = $this->db->query("select 
					a.kode_tree,
					a.tree_item,
					b.kode_analisa,
					left(c.kode_material,3) as subbidang_kode,
					i.subbidang_name,
					c.kode_material,
					h.detail_material_nama,
					h.detail_material_satuan,
					c.koefisien,
					c.harga,
					(c.koefisien * c.harga) as jumlah
					from 
					simpro_rat_item_tree a 
					join simpro_rat_analisa_item_apek b 
					on a.rat_item_tree = b.rat_item_tree 
					inner join
					(
					select kode_analisa,
					kode_material,
					koefisien,
					harga 
					from 
					simpro_rat_analisa_asat where id_tender = $idpro
					union all
					select 
					f.parent_kode_analisa as kode_analisa,
					g.kode_material,
					g.koefisien,g.harga 
					from simpro_rat_analisa_apek f 
					join 
					simpro_rat_analisa_asat g 
					on g.kode_analisa = f.kode_analisa  
					and g.id_tender = f.id_tender where f.id_tender = $idpro
					) AS c on b.kode_analisa = c.kode_analisa
					join simpro_tbl_detail_material h
					on h.detail_material_kode = c.kode_material
					join simpro_tbl_subbidang i
					on i.subbidang_kode = left(c.kode_material,3)
					where a.id_proyek_rat = $idpro
					order by kode_tree,c.kode_material");

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
					$dm_kode = $rows->kode_material;
					$dm_satuan = $rows->detail_material_satuan;
					$dm_koefisien = $rows->koefisien;
					$dm_harga = $rows->harga;
					$dm_jumlah = $rows->jumlah;

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