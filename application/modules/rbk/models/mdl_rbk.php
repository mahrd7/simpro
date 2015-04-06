<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_rbk extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	function insertdata($tbl_get,$data)
	{
		$this->db->insert($tbl_get,$data);
	}
	function deletedata($tbl_get,$id)
	{
		switch($tbl_get){
			case 'simpro_tbl_detail_material':			
			$this->db->where('detail_material_id', $id);
			break;
			
		}
		$this->db->delete($tbl_get);
	}
	function editdata($tbl_get,$data,$id)
	{
		switch($tbl_get){
			case 'simpro_tbl_detail_material':			
			$this->db->where('detail_material_id', $id);
			break;
			
		}		
		$this->db->update($tbl_get,$data);
	}
	function rapi()
	{
	}
	function getdata2($tbl_info,$start,$limit,$bln,$thn){
		$data = array();
		$dat = array();
		switch ($tbl_info) {
			case 'rapa_detail':
				$proyek_id = "1";
				//$proyek_id = $this->session->userdata('proyek_id');
				$query = "select * from simpro_tbl_tahap_kendali where tahap_kode_induk_kendali is null and proyek_id =$proyek_id and extract(month from tahap_tanggal_kendali) = $bln and extract(year from tahap_tanggal_kendali) = $thn";
				$result = $this->db->query($query);
				if($result->num_rows() > 0){
					foreach($result->result() as $row){
						$data['tahap_kendali_id']=$row->tahap_kendali_id;
						$data['tahap_kode_kendali']=$row->tahap_kode_kendali."-".$row->tahap_nama_kendali;
						$data['tahap_satuan_kendali']=$row->tahap_satuan_kendali;
						$data['tahap_volume_kendali']=$row->tahap_volume_kendali;
						$data['tahap_harga_satuan_kendali']=$row->tahap_harga_satuan_kendali;
						$data['tahap_total_kendali']=$row->tahap_total_kendali;
						//child($proyek_id,$bln,$thn,$row->);
						$child = $this->child($proyek_id,$bln,$thn,$row->tahap_kode_induk_kendali);

						
						if ($child=='') {
							$data['leaf'] = 'true';
						} else {
							$data['children'] = $child;
						}

						$dat[]=$data;
						$data='';
						
					}
					$dats ='{"data":".","children":'.json_encode($dat).'}';
				}else{
					$dat = '';
					
				}
			break;
		}
		echo $dats;
	}
	
	function child($proyek_id,$bln,$thn,$kode){
		$query = "select * from simpro_tbl_tahap_kendali where tahap_kode_induk_kendali = '$kode' and proyek_id =$proyek_id and extract(month from tahap_tanggal_kendali) = $bln and extract(year from tahap_tanggal_kendali) = $thn";
		$result = $this->db->query($query);
		if($result->num_rows() > 0){
			foreach($result->result() as $row){
				$data['tahap_kendali_id']=$row->tahap_kendali_id;
				$data['tahap_kode_kendali']=$row->tahap_kode_kendali."-".$row->tahap_nama_kendali;
				$data['tahap_satuan_kendali']=$row->tahap_satuan_kendali;
				$data['tahap_volume_kendali']=$row->tahap_volume_kendali;
				$data['tahap_harga_satuan_kendali']=$row->tahap_harga_satuan_kendali;
				$data['tahap_total_kendali']=$row->tahap_total_kendali;
				//child($proyek_id,$bln,$thn,$row->);
				$child = $this->child($proyek_id,$bln,$thn,$row->tahap_kode_kendali);

				// var_dump($child);
				if ($child=='') {
					$data['leaf'] = 'true';
				} else {
					$data['children'] = $child;
				}

				$dat[]=$data;
				$data='';
				// $datchild='';
			}
		}else {
			$dat='';
		}
		
		return $dat;
	}
	
		function bulan($no){
		if ($no == 1){
			$bulan = 'Januari';
		} elseif ($no == 2) {
			$bulan = 'Februari';
		} elseif ($no == 3) {
			$bulan = 'Maret';
		} elseif ($no == 4) {
			$bulan = 'April';
		} elseif ($no == 5) {
			$bulan = 'Mei';
		} elseif ($no == 6) {
			$bulan = 'Juni';
		} elseif ($no == 7) {
			$bulan = 'Juli';
		} elseif ($no == 8) {
			$bulan = 'Agustus';
		} elseif ($no == 9) {
			$bulan = 'September';
		} elseif ($no == 10) {
			$bulan = 'Oktober';
		} elseif ($no == 11) {
			$bulan = 'November';
		} elseif ($no == 12) {
			$bulan = 'Desember';
		} elseif ($no == 0) {
			$bulan = 'Desember';
		}
		return $bulan;
	}

	function getdata($tbl_info,$start,$limit)
	{
		$data = array();
		$dat = array();
		switch ($tbl_info) {
			
			case 'simpro_tbl_detail_material':
				$item = $this->input->get("txtitem");	
				$sumberdaya = $this->input->get("sumberdaya");			
				$where = "";
				if($item and $sumberdaya){$where .= " where lower(a.detail_material_nama) ilike lower('$item%') and a.subbidang_kode = '$sumberdaya' ";}
				if($item){$where .= " where lower(a.detail_material_nama) ilike lower('$item%') ";}
				if($sumberdaya){$where .= " where a.subbidang_kode = '$sumberdaya' ";}
				$query="select * from $tbl_info a inner join simpro_tbl_subbidang b on a.subbidang_id = b.subbidang_id inner join simpro_tbl_user c on a.user_update = c.user_id left join simpro_tbl_provinsi d on a.detail_material_propinsi = d.nama_provinsi $where";
				//echo $query;
				$q = $this->db->query($query);
				if ($q->num_rows() > 0) {
					$i = 0;
					foreach($q->result() as $row) {						
						$data['id'] = $row->detail_material_id;
						$data['no'] = $i++;
						$data['kode'] = $row->detail_material_kode;
						$data['nama'] = $row->detail_material_nama;
						$data['spesifikasi'] = $row->detail_material_spesifikasi;
						$data['sumber_daya'] = $row->subbidang_name;
						$data['satuan'] = $row->detail_material_satuan;
						$data['harga'] = $row->detail_material_harga;
						$data['provinsi'] = $row->nama_provinsi;
						$data['provinsi_id'] = $row->id_provinsi;
						$data['user'] = $row->user_name;
						$data['tanggal'] = $row->tgl_update;
						$data['ip'] = $row->ip_update;
						$data['divisi'] = $row->divisi_update;
						$data['waktu'] = $row->waktu_update;

						$dat[] = $data;
					}
				}
			break;
			case 'rap':
				$proyek_id = "1";
				//$proyek_id = $this->session->userdata('proyek_id');
				$query="select extract(year from tahap_tanggal_kendali) as tahun,extract(month from tahap_tanggal_kendali) as bulan from simpro_tbl_tahap_kendali where proyek_id = $proyek_id group by extract(year from tahap_tanggal_kendali),extract(month from tahap_tanggal_kendali) order by extract(year from tahap_tanggal_kendali),extract(month from tahap_tanggal_kendali) asc";
				$q = $this->db->query($query);
				if ($q->num_rows() > 0) {
					foreach($q->result() as $row) {
						$thn =  $row->tahun;
						$bln =  $row->bulan;
						$data['bulan'] = $this->bulan($bln);
						$data['tahun'] =$thn;
						$query2="select * from simpro_tbl_approve a inner join simpro_tbl_user b on a.user_id = b.user_id where extract(year from tgl_approve) =".$row->tahun." and extract(month from tgl_approve) =".$row->bulan."  and proyek_id = $proyek_id and form_approve='RAB'";
						$q2 = $this->db->query($query2);
						if ($q2->num_rows() >= 1) {
							foreach($q2->result() as $row){
								if($row->kuncitutup=='1'){
									$data['status'] .= " APPROVED BY ".$row->user_name."<br>";   
								}else{
									$data['status'] = "NOT APPROVED";   
								}
							}
						}else{
							$data['status'] = "NOT APPROVE";
							
						}
						
						$query3 = "select * from simpro_tbl_approve where proyek_id=$proyek_id and tgl_approve='$thn-$bln-01' and form_approve='RAB'";
						$res=$this->db->query($query3);
						if($res->num_rows() >=2){
							if ($res->row()->status == "Close"){
								$data['status_del'] = 1;
							}else{
								$data['status_del'] = 0;
							}
							$data['status_app'] = 1;
						}else{
							$data['status_del'] = 0;
							$data['status_app'] = 0;
						}
						$dat[] = $data;
					}
				}
			break;
			case 'rapi':
				$proyek_id = "1";
				//$proyek_id = $this->session->userdata('proyek_id');
				/*$query="select extract(year from tahap_tanggal_kendali) as tahun,extract(month from tahap_tanggal_kendali) as bulan from simpro_tbl_tahap_kendali where proyek_id = $proyek_id group by extract(year from tahap_tanggal_kendali),extract(month from tahap_tanggal_kendali) order by extract(year from tahap_tanggal_kendali),extract(month from tahap_tanggal_kendali) asc";
				$result = $this->db->query($query);
				if($result->num_rows() > 0 ){
					foreach($result->result() as $row){
						$bln = $row->bulan;
						$thn = $row->tahun;
					}					
				}
				

				$query="select distinct(extract(year from tahap_tanggal_kendali)) as tahun,extract(month from tahap_tanggal_kendali) as bulan  from simpro_tbl_input_kontrak where where proyek_id=$proyek_id";
				$result = $this->db->query($query);
				if($result->num_rows() > 0 ){					
						$bln = $result->row()->bulan;
						$thn = $result->row()->tahun;					
				}
				*/
				$bulan = $this->input->get("bulan");
				$tahun = $this->input->get("tahun");
				if(empty($bulan) && empty($tahun)){
					
					$query = "select *,extract(year from tahap_tanggal_kendali) as tahun,extract(month from tahap_tanggal_kendali) as bulan from simpro_tbl_komposisi_kendali a 
					inner join simpro_tbl_detail_material b on a.detail_material_id = b.detail_material_id 
					inner join simpro_tbl_subbidang c on b.subbidang_kode = c.subbidang_kode 
					where a.proyek_id = $proyek_id and tahap_tanggal_kendali = (select distinct(tahap_tanggal_kendali) from simpro_tbl_input_kontrak where proyek_id = $proyek_id ) order by b.subbidang_kode";
					/*$query = "select *,extract(year from tahap_tanggal_kendali) as tahun,extract(month from tahap_tanggal_kendali) as bulan from tbl_komposisi_kendali a 
inner join simpro_tbl_detail_material b on a.detail_material_kode = b.detail_material_kode 
inner join simpro_tbl_subbidang c on b.subbidang_kode = c.subbidang_kode 
where a.no_spk = '01/div-1/sira-sira/03/2012' and tahap_tanggal_kendali = (select distinct(tahap_tanggal_kendali) from tbl_input_kontrak where no_spk ='01/div-1/sira-sira/03/2012' ) order by b.subbidang_kode";
//echo $query;*/
				}else{
					
					$query = "select *,extract(year from tahap_tanggal_kendali) as tahun,extract(month from tahap_tanggal_kendali) as bulan from simpro_tbl_komposisi_kendali a 
					inner join simpro_tbl_detail_material b on a.detail_material_id = b.detail_material_id 
					inner join simpro_tbl_subbidang c on b.subbidang_kode = c.subbidang_kode 
					where a.proyek_id = $proyek_id and extract(year from tahap_tanggal_kendali) = $tahun and extract(month from tahap_tanggal_kendali) = $bulan order by b.subbidang_kode";
					
				}
				$result = $this->db->query($query);
				if($result->num_rows() > 0 ){
					$query2 = "select sum(komposisi_volume_total_kendali+komposisi_harga_satuan_kendali) as total from simpro_tbl_komposisi_kendali a 
					inner join simpro_tbl_detail_material b on a.detail_material_id = b.detail_material_id 
					inner join simpro_tbl_subbidang c on b.subbidang_kode = c.subbidang_kode 
					where a.proyek_id = $proyek_id and tahap_tanggal_kendali = (select distinct(tahap_tanggal_kendali) from simpro_tbl_input_kontrak where proyek_id =$proyek_id ) 
					";
					$result2 = $this->db->query($query2);
					foreach($result->result() as $row){
						//$data['komposisi_kendali_id'] = $row->komposisi_kendali_id;
						$data['detail_material_kode'] = substr($row->detail_material_kode,0,3);
						$data['detail_material_nama'] = $row->detail_material_nama;
						$data['detail_material_satuan'] = $row->detail_material_satuan;
						$data['komposisi_volume_total_kendali'] = $row->komposisi_volume_total_kendali;
						$data['komposisi_harga_satuan_kendali'] = $row->komposisi_harga_satuan_kendali;
						$data['total'] = $row->komposisi_harga_satuan_kendali*$data['komposisi_volume_total_kendali'] = $row->komposisi_volume_total_kendali;;
						$data['keterangan'] = $row->keterangan;
						$data['kode_simpro'] = $row->detail_material_kode;
						
						
						$data['totalall'] = $result2->row()->total;
						$dat[] = $data;
					}
					
						
				}			
			break;
			
		}
		return $dat;
	}
	
	function getlistsatuan()
	{
		$query = "select * from simpro_tbl_satuan";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->satuan_id;
    		$data['text'] = $row->satuan_nama;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function getsubbidang()
	{
		$query = "select * from simpro_tbl_subbidang where subbidang_kode<>'509' and length(subbidang_kode)=3 order by urutan";
		$q = $this->db->query($query);
		return $q->result_object(); 
	}

	function getprovinsi()
	{
		$query = "select * from simpro_tbl_provinsi";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->nama_provinsi;
    		$data['text'] = $row->nama_provinsi;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}
	
	function getedithargasatuan($no_spk,$tgl_rab)
	{
		$query = "SELECT detail_material_kode,detail_material_nama,avg(komposisi_harga_satuan_kendali) as harga FROM qry_komposisi_budget where no_spk='$no_spk' and tahap_tanggal_kendali='$tgl_rab' group by detail_material_kode,detail_material_nama order by detail_material_kode asc";
		$q = $this->db->query($query);
		return $q->result_object(); 
	}

	function getsubbidangkode()
	{
		$query = "select * from simpro_tbl_subbidang";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->subbidang_kode;
    		$data['text'] = $row->subbidang_name;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function getdivisicombo()
	{
		$query = "select * from simpro_tbl_divisi order by divisi_name";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->divisi_kode;
    		$data['text'] = $row->divisi_name;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function get_proyek_daftar_analisa()
	{
		$query = "select no_spk, (select proyek from tbl_proyek where no_spk = a.no_spk) as proyek from tbl_input_kontrak a group by no_spk";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->no_spk;
    		$data['text'] = $row->proyek;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function get_tanggal_daftar_analisa($proyek_id)
	{
		$sql = "select tahap_tanggal_kendali, 
		extract(year from tahap_tanggal_kendali) as year,
		extract(month from tahap_tanggal_kendali) as month
		from tbl_input_kontrak where no_spk = '$proyek_id'
		group by tahap_tanggal_kendali 
		order by tahap_tanggal_kendali asc";

		$q = $this->db->query($sql);

		if ($q->num_rows()>0) {
			foreach ($q->result() as $row) {
				$data['tgl_rab'] = $row->tahap_tanggal_kendali;
				$data['year'] = $row->year;
				$data['month'] = $row->month;
				$data['month_name'] = $this->bulan($row->month);
				$data['proyek']=$proyek_id;

				$dat[] = $data;
			}
		} else {
			$dat[]="";
		}

		return $dat;
	}

	function get_data_daftar_analisa($proyek_id,$tgl_rab){
		$sql = "select * from tbl_input_kontrak
		where no_spk='$proyek_id' 
		and tahap_tanggal_kendali='$tgl_rab' 
		and tahap_kode_induk_kendali= '' 
		order by tahap_kode_kendali";

		$query_sql = $this->db->query($sql);

		if ($query_sql->num_rows > 0) {
			foreach ($query_sql->result() as $row) {
				$key = $row->tahap_kode_kendali;
				$data['task'] = $key;

				$data['tahap_kode_kendali']= $row->tahap_kode_kendali;
				$data['tahap_nama_kendali']= $row->tahap_nama_kendali;
				$data['tahap_satuan_kendali']= $row->tahap_satuan_kendali;
				$data['expanded'] = 'true';

				$child = $this->query_child_daftar_analisa($proyek_id,$tgl_rab,$key);

				// var_dump($child);
				if ($child=='') {
					$data['leaf'] = 'true';
				} else {
					$data['children'] = $child;
				}

				$dat[]=$data;
				$data='';

				$return = '{"text":".","children":'.json_encode($dat).'}';
				// $data['children']=$child;
				// $datchild='';
			}
		} else {
			$return='';
		}

		return $return;

	}

	function query_child_daftar_analisa($proyek_id,$tgl_rab,$key)
	{
		$sqlchild = "select * from tbl_input_kontrak
		where no_spk='$proyek_id' 
		and tahap_tanggal_kendali='$tgl_rab' 
		and tahap_kode_induk_kendali= '$key' 
		order by tahap_kode_kendali";

		$query_sqlchild = $this->db->query($sqlchild);

		if ($query_sqlchild->num_rows > 0) {
			foreach ($query_sqlchild->result() as $rowchild) {

				$keychild = $rowchild->tahap_kode_kendali;
				$datachild['task'] = $keychild;

				$datachild['tahap_kode_kendali']= $rowchild->tahap_kode_kendali;
				$datachild['tahap_nama_kendali']= $rowchild->tahap_nama_kendali;
				$datachild['tahap_satuan_kendali']= $rowchild->tahap_satuan_kendali;
				$datachild['tahap_volume_kendali']= $rowchild->tahap_volume_kendali;
				$datachild['tahap_harga_satuan_kendali']= $rowchild->tahap_harga_satuan_kendali;
				$datachild['tahap_total_kendali']= $rowchild->tahap_total_kendali;
				// $datachild['expanded'] = 'true';

				$childs = $this->query_child_daftar_analisa($proyek_id,$tgl_rab,$keychild);

				if ($childs=='') {
					$datachild['leaf'] = 'true';
				} else {
					$datachild['children'] = $childs;
				}

				$datchild[]=$datachild;

				$datachild='';
			}					
		} else {
			$datchild='';
		}

		return $datchild;
	}

	function get_id_satuan($data)
	{
		$this->db->where('satuan_nama',$data);
		$q = $this->db->get('simpro_tbl_satuan');

		foreach ($q->result() as $row) {
			$id_satuan = $row->satuan_id;
		}

		return $id_satuan;
	}

	function cek_data_induk_togo($kode,$proyek_id,$tgl_rab)
	{
		$sql = "select * from simpro_tbl_induk_sd_kendali 
				where kode_komposisi='X' 
				and proyek_id='$proyek_id' 
				and kode_tahap_kendali='$kode' 
				and tahap_tanggal_kendali='$tgl_rab'";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$data = 'ada';
		} else {
			$data = 'kosong';
		}

		return $data;
	}

	function get_data_analisa($limit,$offset,$text,$cbo)
	{
		if ($text != '' && $cbo != '') {
			$sql = "select * from simpro_tbl_detail_material where subbidang_kode = '$cbo' and lower(detail_material_nama) LIKE lower('%$text%')";		

			$sql2 = "SELECT * FROM simpro_tbl_detail_material  where subbidang_kode = '$cbo' and lower(detail_material_nama) LIKE lower('%$text%') LIMIT $limit OFFSET $offset";
		} else if($text == '' && $cbo != '') {
			$sql = "select * from simpro_tbl_detail_material where subbidang_kode = '$cbo'";		

			$sql2 = "SELECT * FROM simpro_tbl_detail_material  where subbidang_kode = '$cbo' LIMIT $limit OFFSET $offset";
		}
		else {
			$sql = "select * from simpro_tbl_detail_material";		

			$sql2 = "SELECT * FROM simpro_tbl_detail_material LIMIT $limit OFFSET $offset";
		}

		$q = $this->db->query($sql2);

		$q_total = $this->db->query($sql);

		$totaldata = $q_total->num_rows();

		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$data['id'] = $row->detail_material_id;
				$data['kode'] = $row->detail_material_kode;
				$data['nama'] = $row->detail_material_nama;
				$data['spesifikasi'] = $row->detail_material_spesifikasi;
				$data['propinsi'] = $row->detail_material_propinsi;
				$data['harga'] = $row->detail_material_harga;
				$data['koefisien'] = '1';

				$dat[] = $data;
			}
		} else {
			$dat="";
		}

		// echo $totaldata;
		return '{"total":"'.$totaldata.'","data":'.json_encode($dat).'}';
	}

	function get_sub_ctg($proyek_id,$kode,$tgl_rab)
	{
		$subsql = "select * from simpro_tbl_tahap_kendali a join simpro_tbl_satuan b on a.tahap_satuan_kendali = b.satuan_id 
					where a.tahap_kode_induk_kendali='$kode' 
					and a.proyek_id=$proyek_id  
					and a.tahap_tanggal_kendali='$tgl_rab' 
					order by tahap_kode_kendali";

		$q = $this->db->query($subsql);
		$total=0;
		if ($q->result()) {
			foreach ($q->result() as $row) {
				$data['tahap_kode_kendali']=$row->tahap_kode_kendali;
				$data['tahap_nama_kendali']=$row->tahap_nama_kendali;
				$data['tahap_satuan_kendali']=$row->satuan_nama;
				$data['tahap_volume_kendali']=$row->tahap_volume_kendali;
				$data['tahap_harga_satuan_kendali']=$row->tahap_harga_satuan_kendali;
				$data['harga_sub']=$data['tahap_harga_satuan_kendali']*$data['tahap_volume_kendali'];
				$data['tahap_kendali_id']=$row->tahap_kendali_id;

				$dat[]=$data;
			}
		}
		
		return $dat;
	}

	function getdata_sub_ctg($proyek_id,$tgl_rab,$kode_kendali)
	{
		$sql = "SELECT a.komposisi_kendali_id,	
				b.detail_material_nama, 
				b.detail_material_satuan,
				a.komposisi_harga_satuan_kendali, 
				a.komposisi_koefisien_kendali,
				(a.komposisi_harga_satuan_kendali * a.komposisi_koefisien_kendali) as total,
				c.subbidang_name, 
				c.inisial_rap FROM
				simpro_tbl_komposisi_kendali A
				JOIN simpro_tbl_detail_material b ON A .detail_material_id = b.detail_material_id
				JOIN simpro_tbl_subbidang C ON substr(b.subbidang_kode, 1, 3) = c.subbidang_kode
				where proyek_id=$proyek_id
				and tahap_tanggal_kendali = '$tgl_rab'
				and tahap_kode_kendali = '$kode_kendali'
				ORDER BY c.inisial_rap, b.detail_material_nama";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$rap="";
			$i=1;
			foreach ($q->result() as $row) {
				$data['id']=$row->komposisi_kendali_id;
				$data['nama']=$row->detail_material_nama;
				$data['satuan']=$row->detail_material_satuan;
				$data['harga']=$row->komposisi_harga_satuan_kendali;
				$data['koefisien']=$row->komposisi_koefisien_kendali;
				$data['total']=$row->total;
				$data['sub_nama']=$row->subbidang_name;

				$inisial_rap=$row->inisial_rap;
				if ($rap != $inisial_rap) {
					$rap = $inisial_rap;
					$i = 1;			
				}
				$data['kode_rap']=$inisial_rap.sprintf('%03d',$i++);
				$dat[]=$data;
			}
			
		} else {
			$dat='';
		}
		
		return '{"data":'.json_encode($dat).'}';		
	}

	function get_sub_kode($proyek_id,$tbl_info,$kode,$tgl_rab)
	{
		switch ($tbl_info) {
			case 'rapa':
				$sql = "select count(tahap_kode_kendali) as jml from simpro_tbl_tahap_kendali where tahap_kode_induk_kendali='$kode' and proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab'";
			break;
		}
		$q = $this->db->query($sql);

		foreach($q->result() as $row) {
			$jml = $row->jml;
			switch ($jml) {
			 	case '0':
			 		$jml = 1;
			 	break;			 	
			 	default:
			 		$jml = $jml +1;
			 	break;
			 } 
			$data['value'] = $jml;
			$dat[] = $data;
		}
		return $dat;
	}

	function get_kode_ctg($proyek_id,$tgl_rab)
	{
		$sql = "select count(tahap_kode_kendali) as jml from simpro_tbl_tahap_kendali where tahap_kode_induk_kendali='' and proyek_id=$proyek_id and tahap_tanggal_kendali='$tgl_rab'";
		$q = $this->db->query($sql);

		foreach($q->result() as $row) {
			$jml = $row->jml;
			switch ($jml) {
			 	case '0':
			 		$jml = 1;
			 	break;			 	
			 	default:
			 		$jml = $jml +1;
			 	break;
			 } 
			$data['value'] = $jml;
			$dat[] = $data;
		}
		return $dat;
	}

	function get_data_cost_to_go($proyek_id,$tgl_rab){
		$sql = "select * from simpro_tbl_tahap_kendali a join simpro_tbl_satuan b on a.tahap_satuan_kendali = b.satuan_id 
		where a.proyek_id='$proyek_id' 
		and a.tahap_tanggal_kendali='$tgl_rab' 
		and a.tahap_kode_induk_kendali= '' 
		order by tahap_kode_kendali";

		$query_sql = $this->db->query($sql);

		if ($query_sql->num_rows > 0) {
			foreach ($query_sql->result() as $row) {
				$key = $row->tahap_kode_kendali;
				$data['task'] = $key;

				$data['tahap_kendali_id']= $row->tahap_kendali_id;
				$data['tahap_kode_kendali']= $row->tahap_kode_kendali;
				$data['tahap_nama_kendali']= $row->tahap_nama_kendali;
				$data['tahap_satuan_kendali']= $row->satuan_nama;
				$data['tahap_volume_kendali']= $row->tahap_volume_kendali;
				$data['tahap_harga_satuan_kendali']= $row->tahap_harga_satuan_kendali;
				$data['tahap_total_kendali']= $row->tahap_total_kendali;
				$data['expanded'] = 'true';

				$child = $this->query_child_ctg($proyek_id,$tgl_rab,$key);

				// var_dump($child);
				if ($child=='') {
					$data['leaf'] = 'true';
				} else {
					$data['children'] = $child;
				}

				$dat[]=$data;
				$data='';

				$return = '{"text":".","children":'.json_encode($dat).'}';
				// $data['children']=$child;
				// $datchild='';
			}
		} else {
			$return='';
		}

		return $return;

	}

	function query_child_ctg($proyek_id,$tgl_rab,$key)
	{
		$sqlchild = "select * from simpro_tbl_tahap_kendali a join simpro_tbl_satuan b on a.tahap_satuan_kendali = b.satuan_id 
		where a.proyek_id='$proyek_id' 
		and a.tahap_tanggal_kendali='$tgl_rab' 
		and a.tahap_kode_induk_kendali= '$key' 
		order by a.tahap_kode_kendali";

		$query_sqlchild = $this->db->query($sqlchild);

		if ($query_sqlchild->num_rows > 0) {
			foreach ($query_sqlchild->result() as $rowchild) {

				$keychild = $rowchild->tahap_kode_kendali;
				$datachild['task'] = $keychild;

				$datachild['tahap_kendali_id']= $rowchild->tahap_kendali_id;
				$datachild['tahap_kode_kendali']= $rowchild->tahap_kode_kendali;
				$datachild['tahap_nama_kendali']= $rowchild->tahap_nama_kendali;
				$datachild['tahap_satuan_kendali']= $rowchild->satuan_nama;
				$datachild['tahap_volume_kendali']= $rowchild->tahap_volume_kendali;
				$datachild['tahap_harga_satuan_kendali']= $rowchild->tahap_harga_satuan_kendali;
				$datachild['tahap_total_kendali']= $rowchild->tahap_total_kendali;
				// $datachild['expanded'] = 'true';

				$childs = $this->query_child_ctg($proyek_id,$tgl_rab,$keychild);

				if ($childs=='') {
					$datachild['leaf'] = 'true';
				} else {
					$datachild['children'] = $childs;
				}

				$datchild[]=$datachild;

				$datachild='';
			}					
		} else {
			$datchild='';
		}

		return $datchild;
	}

	function getproyekcombo($divisi_kode)
	{
		$tgl_sekarang = date('Y-m-d');
		$query = "select * from simpro_tbl_proyek where simpro_tgl_pengumuman>='$tgl_sekarang' and divisi_kode='$divisi_kode' and (proyek_status='MENANG TENDER' or proyek_status='PENUNJUKAN')";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->proyek_id;
    		$data['text'] = $row->proyek;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function gettanggalcombo($no_spk)
	{
		$query = "select distinct tahap_tanggal_kendali from simpro_tbl_tahap_kendali where no_spk='$no_spk' order by tahap_tanggal_kendali desc";
		$q = $this->db->query($query);
		if ($q->num_rows() > 0) {
		foreach($q->result() as $row) {
			$data['value'] = $row->tahap_tanggal_kendali;
    		$data['text'] = $row->tahap_tanggal_kendali;

    		$dat[] = $data;
    		}
		}
		return $dat; 
	}

	function getdata_edit_hs_ctg($proyek_id,$tgl_rab)
	{
		$sql = "SELECT b.detail_material_kode,	
				a.keterangan,
				b.detail_material_nama, 
				 avg(a.komposisi_harga_satuan_kendali) as rata_harga_satuan, 
				c.subbidang_name, 
				c.inisial_rap FROM
					simpro_tbl_komposisi_kendali A
				JOIN simpro_tbl_detail_material b ON A .detail_material_id = b.detail_material_id
				JOIN simpro_tbl_subbidang C ON substr(b.subbidang_kode, 1, 3) = c.subbidang_kode
				GROUP BY a.keterangan, b.detail_material_kode, b.detail_material_nama, c.subbidang_name, c.inisial_rap
				ORDER BY c.inisial_rap, b.detail_material_nama";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$rap="";
			$i=1;
			foreach ($q->result() as $row) {
				$data['kode']=$row->detail_material_kode;
				$data['nama']=$row->detail_material_nama;
				$data['harga']=$row->rata_harga_satuan;
				$data['sub_nama']=$row->subbidang_name;
				$data['keterangan']=$row->keterangan;

				$inisial_rap=$row->inisial_rap;
				if ($rap != $inisial_rap) {
					$rap = $inisial_rap;
					$i = 1;			
				}
				$data['kode_rap']=$inisial_rap.sprintf('%03d',$i++);
				$dat[]=$data;
			}
			
		} else {
			$dat='';
		}
		
		return '{"data":'.json_encode($dat).'}';		
	}

	function update_ctg($id,$data)
	{
		$this->db->where('tahap_kendali_id',$id);
		$this->db->update('simpro_tbl_tahap_kendali',$data);
	}

	function insert_ctg($data)
	{
		$this->db->insert('simpro_tbl_tahap_kendali',$data);
	}

	function insert_sub_ctg($data)
	{
		$this->db->insert('simpro_tbl_tahap_kendali',$data);
	}

	function update_analisa_ctg($id,$data)
	{
		$this->db->where('komposisi_kendali_id', $id);
		$this->db->update('simpro_tbl_komposisi_kendali', $data);
	}

	function insert_induk_togo_induk($data)
	{
		$this->db->insert('simpro_tbl_induk_sd_kendali',$data);
	}

	function insert_induk_komposisi_togo($data)
	{
		$this->db->insert('simpro_tbl_komposisi_kendali',$data);
	}

	function update_hs_ctg($proyek_id,$tgl_rab,$id,$data)
	{
		$var = array('proyek_id' => $proyek_id, 'tahap_tanggal_kendali' => $tgl_rab, 'detail_material_kode' => $id);
		$this->db->where($var);
		$this->db->update('simpro_tbl_komposisi_kendali', $data);
	}

	function get_tanggal_ctg($proyek_id)
	{
		$sql = " 
		SELECT tahap_tanggal_kendali, 
			EXTRACT(year from tahap_tanggal_kendali) as year,
			EXTRACT(month from tahap_tanggal_kendali) as month
		FROM simpro_tbl_tahap_kendali 
		WHERE proyek_id=$proyek_id 
		GROUP BY tahap_tanggal_kendali 
		ORDER BY tahap_tanggal_kendali asc
		";

		$q = $this->db->query($sql);

		foreach ($q->result() as $row) {
			$data['status'] = 'NOT APPROVE';
			$data['tgl_rab'] = $row->tahap_tanggal_kendali;
			$data['year'] = $row->year;
			$data['month'] = $row->month;
			$data['month_name'] = $this->bulan($row->month);

			$dat[] = $data;
		}

		return $dat;
	}

	function cek_data_komposisi_togo($id_material,$kode,$proyek_id,$tgl_rab)
	{
		$sql = "select * from simpro_tbl_komposisi_kendali 
				where detail_material_id=$id_material 
				and proyek_id='$proyek_id' 
				and tahap_kode_kendali='$kode' 
				and tahap_tanggal_kendali='$tgl_rab'";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$data = 'ada';
		} else {
			$data = 'kosong';
		}

		return $data;
	}

	function get_hs_pwd($proyek_id)
	{
		$sql = "select trim(username) as data2, 
				pass_edit_id as datajumlah,
				trim(password) as data1
				from simpro_tbl_pass_edit where proyek_id=$proyek_id";

		$q =  $this->db->query($sql);
		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = '';
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function update_hs_ctg_pwd($id,$data,$proyek_id)
	{
		$var = array('proyek_id' => $proyek_id, 'pass_edit_id' => $id);
		$this->db->where($var);
		$this->db->update('simpro_tbl_pass_edit', $data);
	}

	function cek_pwd_hs($uname,$pwd)
	{
		$var = array('username' => $uname, 'password' => $pwd);
		$this->db->where($var);
		$q = $this->db->get('simpro_tbl_pass_edit');

		if ($q->result()) {
			$data['value'] = 'true' ;
		} else {
			$data['value'] = 'false' ;
		}
		$dat[] = $data;
		return '{"data":'.json_encode($dat).'}';
	}
	
	function pilih_proyek($div_id)
	{
		$sql = sprintf("
			SELECT 
				proyek_id,
				proyek,
				lokasi_proyek,
				no_spk,
				mulai,
				berakhir,
				total_waktu_pelaksanaan,
				tgl_tender
			FROM simpro_tbl_proyek
			WHERE divisi_kode = '%d'
			ORDER BY mulai DESC
		", $div_id);
		$tot = $this->db->query($sql)->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot,'data'=>$this->db->query($sql)->result_array());
			return $data;
		} else return false;
	}
	
	function pilih_proyek_rap($pid, $div_id)
	{
		if ($div_id == 6 || $div_id == 21) {
			$sql = sprintf("
				SELECT 
					proyek_id,
					proyek,
					lokasi_proyek,
					no_spk,
					mulai,
					berakhir,
					total_waktu_pelaksanaan,
					tgl_tender
				FROM simpro_tbl_proyek
				WHERE proyek_id = '%d'
				ORDER BY mulai DESC
			", $pid);
		} else {
			$sql = sprintf("
				SELECT 
					proyek_id,
					proyek,
					lokasi_proyek,
					no_spk,
					mulai,
					berakhir,
					total_waktu_pelaksanaan,
					tgl_tender
				FROM simpro_tbl_proyek
				WHERE divisi_kode = '%d' AND proyek_id = '%d'
				ORDER BY mulai DESC
			", $div_id, $pid);
		}
		$tot = $this->db->query($sql)->num_rows();
		if($tot > 0)
		{
			$data = array('total'=>$tot,'data'=>$this->db->query($sql)->result_array());
			return $data;
		} else return false;
	}
	
	function get_data_skbdn($data_var)
	{
		$proyek_id = $data_var['proyek_id'];
		// $tgl_awal = $data_var['tgl_awal'];
		// $tgl_akhir = $data_var['tgl_akhir'];
		$limit = $data_var['limit'];
		$start = $data_var['start'];
		$sort = $data_var['sort'];

		$sql_total = "select 
					*
					from simpro_tbl_skbdn a
					JOIN simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					where proyek_id = $proyek_id
					and lower(b.detail_material_nama) LIKE lower('%$sort%')";		

		$sql = "select 
					a.skbdn_id as id,
					b.detail_material_kode as kode_meterial,
					b.detail_material_nama as jenis_meterial,
					b.detail_material_spesifikasi as spesifikasi_meterial,
					b.detail_material_satuan as satuan,
					a.volume as volume,
					a.harga_satuan as harga_satuan,
					(a.volume * a.harga_satuan) as jumlah_harga,
					c.subbidang_name as subbidang_nama
					from simpro_tbl_skbdn a
					JOIN simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					JOIN simpro_tbl_subbidang c
					on left(b.subbidang_kode,3) = c.subbidang_kode
					where proyek_id = $proyek_id
					and lower(b.detail_material_nama) LIKE lower('%$sort%')
					order by subbidang_nama, jenis_meterial
					LIMIT $limit 
					OFFSET $start";
		

		$q = $this->db->query($sql);

		$q_total = $this->db->query($sql_total);

		$totaldata = $q_total->num_rows();

		if ($q->num_rows() > 0) {
			$dat = $q->result_object();
		} else {
			$dat="";
		}

		return '{"total":"'.$totaldata.'","data":'.json_encode($dat).'}';
	}

	function get_data_rrp($data_var)
	{
		$proyek_id = $data_var['proyek_id'];
		// $tgl_awal = $data_var['tgl_awal'];
		// $tgl_akhir = $data_var['tgl_akhir'];
		$limit = $data_var['limit'];
		$start = $data_var['start'];
		$sort = $data_var['sort'];

		$sql_total = "select 
					*
					from simpro_tbl_rincian_rencana_pengadaan a
					JOIN simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					where proyek_id = $proyek_id
					and lower(b.detail_material_nama) LIKE lower('%$sort%')";		

		$sql = "select 
					a.rincian_rencana_pengadaan_id as id,
					b.detail_material_kode as kode_meterial,
					b.detail_material_nama as jenis_meterial,
					b.detail_material_spesifikasi as spesifikasi_meterial,
					b.detail_material_satuan as satuan,
					a.volume as volume,
					a.harga_satuan as harga_satuan,
					(a.volume * a.harga_satuan) as jumlah_harga,
					c.subbidang_name as subbidang_nama
					from simpro_tbl_rincian_rencana_pengadaan a
					JOIN simpro_tbl_detail_material b
					on a.detail_material_id = b.detail_material_id
					JOIN simpro_tbl_subbidang c
					on left(b.subbidang_kode,3) = c.subbidang_kode
					where proyek_id = $proyek_id
					and lower(b.detail_material_nama) LIKE lower('%$sort%')
					order by subbidang_nama, jenis_meterial
					LIMIT $limit 
					OFFSET $start";
		

		$q = $this->db->query($sql);

		$q_total = $this->db->query($sql_total);

		$totaldata = $q_total->num_rows();

		if ($q->num_rows() > 0) {
			$dat = $q->result_object();
		} else {
			$dat="";
		}

		return '{"total":"'.$totaldata.'","data":'.json_encode($dat).'}';
	}

	function get_data_checklist_dokumen($data_var)
	{
		$proyek_id = $data_var['proyek_id'];
		$limit = $data_var['limit'];
		$start = $data_var['start'];
		$sort = $data_var['sort'];

		$sql_total = "SELECT
				*
				FROM
				simpro_tbl_checklist_dokumen
				WHERE proyek_id = $proyek_id
				and lower(uraian_pekerjaan) LIKE lower('%$sort%')
				or lower(suplier) LIKE lower('%$sort%')";

		$sql = "SELECT
				checklist_dokumen_id as id,
				uraian_pekerjaan,
				suplier,
				satuan_id,
				harga_satuan,
				status_penawaran,
				rekan_usul,
				CASE WHEN status_penawaran = 0
				THEN '<b>&#x2610;</b>'
				ELSE '<b>&#x2611;</b>'
				END as spen_ya,
				CASE WHEN status_penawaran = 0
				THEN '<b>&#x2611;</b>'
				ELSE '<b>&#x2610;</b>'
				END as spen_tidak,
				CASE WHEN rekan_usul = 0
				THEN '<b>&#x2610;</b>'
				ELSE '<b>&#x2611;</b>'
				END as rekan,
				keterangan
				FROM
				simpro_tbl_checklist_dokumen
				WHERE proyek_id = $proyek_id
				and (lower(uraian_pekerjaan) LIKE lower('%$sort%')
				or lower(suplier) LIKE lower('%$sort%'))
				order by uraian_pekerjaan
				LIMIT $limit 
				OFFSET $start";

		$q = $this->db->query($sql);

		$q_total = $this->db->query($sql_total);

		$totaldata = $q_total->num_rows();

		if ($q->num_rows() > 0) {
			$dat = $q->result_object();
		} else {
			$dat="";
		}

		return '{"total":"'.$totaldata.'","data":'.json_encode($dat).'}';
	}

	function get_sumberdaya($data_var)
	{
		$proyek_id = $this->session->userdata('proyek_id');
		$sort = $data_var['sort'];
		$cbosort = $data_var['cbosort'];
		// $limit = $data_var['limit'];
		// $start = $data_var['start'];

		$sql_total = "select * 
					from 
					simpro_tbl_detail_material a join simpro_rap_analisa_asat b on a.detail_material_kode = b.kode_material
					where a.subbidang_kode = '$cbosort' and b.id_proyek = $proyek_id
					and lower(detail_material_nama)	LIKE lower('%$sort%')";		

		// $sql = "SELECT * FROM simpro_tbl_detail_material  where subbidang_kode = $cbo and lower(detail_material_nama) LIKE lower('%$text%') LIMIT $limit OFFSET $start";
		

		$q = $this->db->query($sql_total);

		// $q_total = $this->db->query($sql_total);

		// $totaldata = $q_total->num_rows();

		if ($q->result()) {
			foreach ($q->result() as $row) {
				$data['id'] = $row->detail_material_id;
				$data['kode'] = $row->detail_material_kode;
				$data['nama'] = $row->detail_material_nama;
				$data['spesifikasi'] = $row->detail_material_spesifikasi;

				$dat[] = $data;
			}
		} else {
			$dat="";
		}

		// return '{"total":"'.$totaldata.'","data":'.json_encode($dat).'}';
		return '{"data":'.json_encode($dat).'}';
	}

	function get_subbidang()
	{
		$sql = "select * from simpro_tbl_subbidang order by subbidang_kode";
		$q = $this->db->query($sql);
		if ($q->result()) {
			foreach($q->result() as $row) {
				$data['value'] = $row->subbidang_kode;
	    		$data['text'] = $row->subbidang_name;

	    		$dat[] = $data;
    		}
		}
		return '{"data":'.json_encode($dat).'}';
	}

	function insert($page,$data)
	{
		switch ($page) {
			case 'skbdn':
				$proyek_id = $data['proyek_id'];
				$detail_material_id = $data['detail_material_id'];

				$cek = $this->db->query("select * from simpro_tbl_skbdn where proyek_id=$proyek_id and detail_material_id = $detail_material_id");
				if (!$cek->result()) {
					$this->db->insert('simpro_tbl_skbdn',$data);
				}
			break;
			case 'rrp':
				$proyek_id = $data['proyek_id'];
				$detail_material_id = $data['detail_material_id'];

				$cek = $this->db->query("select * from simpro_tbl_rincian_rencana_pengadaan where proyek_id=$proyek_id and detail_material_id = $detail_material_id");
				if (!$cek->result()) {
					$this->db->insert('simpro_tbl_rincian_rencana_pengadaan',$data);
				}
			break;
			case 'checklist_dokumen':
				$this->db->insert('simpro_tbl_checklist_dokumen',$data);
			break;
		}
	}

	function delete($page,$data)
	{
		switch ($page) {
			case 'skbdn':
				$id = $data['id'];
				$var = array(
					'skbdn_id' => $id
				);
				$this->db->delete('simpro_tbl_skbdn',$var);
			break;
			case 'rrp':
				$id = $data['id'];
				$var = array(
					'rincian_rencana_pengadaan_id' => $id
				);
				$this->db->delete('simpro_tbl_rincian_rencana_pengadaan',$var);
			break;
			case 'checklist_dokumen':
				$id = $data['id'];
				$var = array(
					'checklist_dokumen_id' => $id
				);
				$this->db->delete('simpro_tbl_checklist_dokumen',$var);
			break;
		}
	}

	function edit($page,$data)
	{
		switch ($page) {
			case 'skbdn':
				$id = $data['id'];
				$volume = $data['volume'];
				$harga_satuan = $data['harga_satuan'];
				$var = array(
					'skbdn_id' => $id
				);
				$data_update = array(
					'volume' => $volume, 
					'harga_satuan' => $harga_satuan
				);
				$this->db->where($var);
				$this->db->update('simpro_tbl_skbdn',$data_update);
			break;
			case 'rrp':
				$id = $data['id'];
				$volume = $data['volume'];
				$harga_satuan = $data['harga_satuan'];
				$var = array(
					'rincian_rencana_pengadaan_id' => $id
				);
				$data_update = array(
					'volume' => $volume, 
					'harga_satuan' => $harga_satuan
				);
				$this->db->where($var);
				$this->db->update('simpro_tbl_rincian_rencana_pengadaan',$data_update);
			break;
			case 'checklist_dokumen':
				$id=$data['id'];
				$suplier=$data['suplier'];
				$harga_satuan=$data['harga_satuan'];
				$status_penawaran=$data['status_penawaran'];
				$keterangan=$data['keterangan'];
				$rekan_usul=$data['rekan_usul'];
				$satuan_id=$data['satuan_id'];
				$uraian_pekerjaan=$data['uraian_pekerjaan'];

				$var = array(
					'checklist_dokumen_id' => $id
				);

				$data_update = array(
					'suplier' => $suplier,
					'harga_satuan' => $harga_satuan,
					'status_penawaran' => $status_penawaran,
					'keterangan' => $keterangan,
					'rekan_usul' => $rekan_usul,
					'satuan_id' => $satuan_id,
					'uraian_pekerjaan' => $uraian_pekerjaan
				);

				$this->db->where($var);
				$this->db->update('simpro_tbl_checklist_dokumen',$data_update);
			break;
		}
	}

	function get_user($user_id)
	{
		$sql = "SELECT
				first_name || ' ' ||last_name as fullname,
				b.jabatan,
				case when a.alamat isnull or a.alamat = ''
				then '-'
				else a.alamat
				end
				FROM
				simpro_tbl_user a
				JOIN simpro_tbl_jabatan b
				on a.jabatan = b.id_jabatan
				WHERE
				user_id = $user_id";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$row = $q->row();
			$data['fullname'] = $row->fullname;
			$data['jabatan'] = $row->jabatan;
			$data['alamat'] = $row->alamat;
		} else {
			$data['fullname'] = '-';
			$data['jabatan'] = '-';
			$data['alamat'] = '-';
		}

		return $data;
	}

	function data_proyek_by_id($id)
	{
		$query = sprintf("
				select 
				proyek_id,
				no_spk,
				proyek,
				lingkup_pekerjaan,
				proyek_alamat,
				proyek_telp,
				lokasi_proyek,
				pemberi_kerja,
				kepala_proyek,
				proyek_konsultan_pengawas,
				no_spk,
				proyek_nama_sumber_1,
				proyek_nilai_sumber_1,
				bast_1,
				bast_2,
				termijn,
				jaminan_pelaksanaan,
				asuransi_pekerjaan,
				sifat_kontrak,
				manajemen_pelaksanaan,
				eskalasi,
				beda_kurs,
				pek_tambah_kurang,
				mulai,
				berakhir,
				tgl_tender,
				case when tgl_pengumuman isnull
				then tgl_tender
		        else tgl_pengumuman
		        end,
		        user_update,
		        tgl_update,
		        ip_update,
		        divisi_update,
		        waktu_update,
		        wo,
		        kode_wilayah,
		        kode_propinsi1,
		        no_kontrak,
		        status_pekerjaan,
		        struktur_organisasi,
		        sketsa_proyek,
		        lokasi_latitude,
		        lokasi_longitude,
		        no_kontrak2,
		        no_spk_2,
		        id_excel,
		        sbu_kode,
		        divisi_kode,
		        proyek_status,
		        propinsi,
		        coalesce(nilai_kontrak_ppn,0) as nilai_kontrak_ppn,
		        coalesce(nilai_kontrak_non_ppn,0) as nilai_kontrak_non_ppn,
		        coalesce(uang_muka,0) as uang_muka,
		        coalesce(retensi,0) as retensi,
		        coalesce(denda_minimal,0) as denda_minimal,
		        coalesce(denda_maksimal,0) as denda_maksimal,
		        coalesce(rap_usulan,0) as rap_usulan,
		        coalesce(rap_ditetapkan,0) as rap_ditetapkan,
		        coalesce(jangka_waktu,0) as jangka_waktu,
		        coalesce(perpanjangan_waktu,0) as perpanjangan_waktu,
		        coalesce(masa_pemeliharaan,0) as masa_pemeliharaan,
		        coalesce(total_waktu_pelaksanaan,0) as total_waktu_pelaksanaan,
		        coalesce(pph_final,0) as pph_final,
		        coalesce(sts_pekerjaan,0) as sts_pekerjaan, 
				coalesce(DATE_PART('day', berakhir::timestamp - mulai::timestamp),0) as jangka_selisih, 
				coalesce((DATE_PART('day', berakhir::timestamp - mulai::timestamp) + perpanjangan_waktu),0) as total_tambah_waktu 
				from simpro_tbl_proyek 
				where proyek_id = '%d' order by proyek_id;
			", $id);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return $rs->row_array();
		} else return false;
	}

	function get_data_tender($id)
	{
		$query = sprintf("
			SELECT 
				simpro_tbl_proyek.*, 
				simpro_tbl_divisi.divisi_kode as div_kod, 
				simpro_tbl_divisi.divisi_name
			FROM simpro_tbl_proyek 
			INNER JOIN simpro_tbl_divisi on simpro_tbl_divisi.divisi_id = simpro_tbl_proyek.divisi_kode
			WHERE simpro_tbl_proyek.proyek_id = '%d'
			", $id);
		$rs = $this->db->query($query);
		$totdata = $rs->num_rows();
		if($totdata)
		{
			return array('total'=>$totdata, 'data'=>$rs->row_array());
		} else return false;
	}

	function get_data_sketsa_proyek($id)
	{
		$query = sprintf("SELECT * FROM simpro_tbl_sketsa_proyek WHERE proyek_id = '%d'", $id);
		return $this->_retdata($query);			
	}

	function get_data_dokumen_proyek($id)
	{
		$query = sprintf("SELECT * FROM simpro_tbl_dokumen_proyek WHERE proyek_id = '%d'", $id);
		return $this->_retdata($query);			
	}

	function _retdata($qry)
	{
		$rs = $this->db->query($qry);
		$totdata = $rs->num_rows();
		if($totdata > 0)
		{
			return array('total'=>$totdata, 'success'=>true, 'data'=>$rs->result_array());
		} else return false;	
	}

	function get_divisi()
	{
		$query = "
			SELECT 				
				divisi_id, divisi_kode, divisi_name, 
				CONCAT('[',divisi_kode,'] - ', divisi_name) as divisi
			FROM  simpro_tbl_divisi order by urut
			";
		return $this->_retdata($query);
	}	

	function pilih_proyek_pilih($div_id)
	{
			$sql = sprintf("
				SELECT 
				proyek_id,
				proyek,
				lokasi_proyek,
				no_spk,
				mulai,
				berakhir,
				total_waktu_pelaksanaan,
				tgl_tender
				FROM simpro_tbl_proyek
				WHERE divisi_kode = '%d'
				ORDER BY mulai DESC
				", $div_id);
		
		$tot = $this->db->query($sql)->num_rows();
		if($tot > 0)
		{
			$data = $this->reqcari($sql, array('proyek', 'no_spk', 'lokasi_proyek'));			
			return $data;
		} else return false;
	}

	public function reqcari($qry, $cari)
	{
		if(isset($_REQUEST['_dc']))
		{
			$offset = ($_REQUEST['page'] - 1) * $_REQUEST['limit'];
			$limit = $_REQUEST['limit'];
			$rs = $this->db->query($qry);
			$total_data = $rs->num_rows();			
			if(!empty($_REQUEST['query']))
			{
				$tcari = count($cari);
				if($tcari > 0)
				{
					if(preg_match("/where/i", $qry)) 
					{					
						$newq = preg_split('/(\swhere\s)/i', trim(preg_replace('/\s+/',' ', strtolower($qry))), -1, PREG_SPLIT_NO_EMPTY);
						$qry = $newq[0] . " WHERE (";
						for($i=0; $i < $tcari; $i++)
						{
							if($i > 0) $qry .= " OR LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%' ";
								else $qry .= " LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%'";
						}					
						$qry .= " )";	
						$qry .= " AND ".$newq[1]." ";
					} else 
					{
						for($i=0; $i < $tcari; $i++)
						{
							if($i > 0) $qry .= " OR LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%' ";
								else $qry .= " WHERE LOWER(".$cari[$i].") LIKE '%".strtolower(addslashes($_REQUEST['query']))."%'";
						}
					}
				}
				$ress = $this->db->query($qry);				
				$ress_tot = $ress->num_rows();
			}
			$limits = sprintf('LIMIT %d OFFSET %d', $limit, $offset);
			$qry .= $limits;
			$res = $this->db->query($qry);
			$data = $res->result_array();
			$total_data = !empty($_REQUEST['query']) ? $ress_tot : $total_data;
			return array('total'=>$total_data, 'data'=>$data, '_dc'=>$_REQUEST['_dc']);
		}	
	}
}