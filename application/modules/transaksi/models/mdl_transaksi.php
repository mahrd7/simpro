<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_transaksi extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	function getdata($tbl_get)
	{
		$q = $this->db->get($tbl_get);
		return $q;
	}

	function get_data_toko($limit,$offset,$text)
	{
		if ($text) {
			$sql_jumlah = "select * from simpro_tbl_toko where lower(toko_nama) LIKE lower('%$text%')";
			$sql = "SELECT * FROM simpro_tbl_toko  where lower(toko_nama) LIKE lower('%$text%') LIMIT $limit OFFSET $offset";
		} else {
			$sql_jumlah = "select * from simpro_tbl_toko";
			$sql = "SELECT * FROM simpro_tbl_toko LIMIT $limit OFFSET $offset";
		}

		$q = $this->db->query($sql);
		$q_total = $this->db->query($sql_jumlah);
		$totaldata = $q_total->num_rows();

		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$data['toko_id'] = $row->toko_id;
				$data['toko_kode'] = $row->toko_kode;
				$data['toko_nama'] = $row->toko_nama;
				$data['toko_alamat'] = $row->toko_alamat;
				$data['toko_produk'] = $row->toko_produk;

				$dat[] = $data;
			}
		} else {
			$dat="";
		}

		return '{"total":"'.$totaldata.'","data":'.json_encode($dat).'}';
	}

	function insert_pilih_toko($data)
	{
		$this->db->insert('simpro_tbl_pilih_toko',$data);
	}

	function cek_pilih_toko($toko_id,$proyek_id)
	{
		$this->db->where('toko_id', $toko_id);
		$this->db->where('proyek_id', $proyek_id);
		$q = $this->db->get('simpro_tbl_pilih_toko');

		if ($q->result()) {
			$data = 'ada';
		} else {
			$data = 'kosong';
		}

		return $data;
	}

	function get_data_pilih_toko($limit,$offset,$proyek_id)
	{
		$sql_jumlah = "select * from simpro_tbl_pilih_toko a join simpro_tbl_toko b on a.toko_id = b.toko_id join simpro_tbl_proyek c on a.proyek_id = c.proyek_id join simpro_tbl_user d on a.user_id = d.user_id join simpro_tbl_divisi e on a.divisi_id = e.divisi_id where a.proyek_id=$proyek_id";
		$sql = "select a.*,b.toko_id,b.toko_kode,b.toko_nama,d.user_name,e.divisi_name from simpro_tbl_pilih_toko a join simpro_tbl_toko b on a.toko_id = b.toko_id join simpro_tbl_proyek c on a.proyek_id = c.proyek_id join simpro_tbl_user d on a.user_id = d.user_id join simpro_tbl_divisi e on a.divisi_id = e.divisi_id where a.proyek_id=$proyek_id LIMIT $limit OFFSET $offset";
		
		$q = $this->db->query($sql);
		$q_total = $this->db->query($sql_jumlah);
		$totaldata = $q_total->num_rows();

		if ($q->num_rows() > 0) {
			$dat = $q->result_object();
		} else {
			$dat="";
		}

		return '{"total":"'.$totaldata.'","data":'.json_encode($dat).'}';
	}

	function deletedata($id)
	{
		$this->db->where('pilih_toko_id', $id);
		$this->db->delete('simpro_tbl_pilih_toko');
	}

	function deletedataall($proyek_id)
	{
		$this->db->where('proyek_id', $proyek_id);
		$this->db->delete('simpro_tbl_pilih_toko');
	}

	function get_data_divisi()
	{
		$sql = "select 
		divisi_id as value,
		divisi_name as text from simpro_tbl_divisi";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_proyek($divisi)
	{
		$sql = "select 
		proyek_id as value,
		proyek as text from simpro_tbl_proyek where divisi_kode = $divisi";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_tanggal($proyek)
	{
		$sql = "select 
		tgl_update as value,
		tgl_update as text from simpro_tbl_pilih_toko where proyek_id = $proyek group by tgl_update";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function insertdatacopy($divisi,$proyek,$tanggal)
	{
		$sql = "select toko_id,user_id from simpro_tbl_pilih_toko where proyek_id = '$proyek' and tgl_update = '$tanggal'";

		$q = $this->db->query($sql);

		//$proyek_id = $this->session->userdata('proyek_id'); 
		$proyek_id = $proyek_id;
		$ip_update = $this->session->userdata('ip_address');
		$divisi_id = $this->session->userdata('divisi_id');
		$user_update = $this->session->userdata('uid');
		$waktu_update=date('H:i:s');		
		$tgl_update=date('Y-m-d');
		
		$ada = 0;

		if ($q->result()) {
			foreach ($q->result_array() as $key => $val) {
				$cek_pilih_toko = $this->cek_pilih_toko($val['toko_id'],$proyek_id);
		
				if ($cek_pilih_toko == 'kosong') {
				$sqlin = "INSERT INTO simpro_tbl_pilih_toko (proyek_id,toko_id,user_id,tgl_update,ip_update,divisi_id,waktu_update) VALUES
				('$proyek_id','$val[toko_id]','$val[user_id]','$tgl_update','$ip_update','$divisi_id','$waktu_update')";

				$this->db->query($sqlin);
				$status = "Berhasil Mencopy semua Data!";
				$ada = 1;
				} else {
					if($ada == 1) $status = "Berhasil Mencopy Data tetapi ada sebagian Data yang Sudah ada!";
					else $status = "Data Sudah ada";
				}
			}
		} else {
			$status = "Gagal Mencopy Data!";
		}

		return '{"success":"true","data":'.json_encode($status).'}';
	}

	function get_data_cashtodate($proyek_id,$get)
	{
		$sort = $get['sort'];
		$pilihan_sort = $get['pilihan_sort'];
		$tgl_awal = $get['tgl_awal'];
		$tgl_akhir = $get['tgl_akhir'];

		$sql = "WITH cash as (SELECT
				a.pic,
				a.proyek_id,
				a.cashtodateid as id,
				a.detail_material_id,
				a.pilih_toko_id,
				a.no_bukti,
				CASE WHEN e.toko_kode ISNULL
				THEN '-'
				ELSE e.toko_kode
				END,
				CASE WHEN e.toko_nama ISNULL
				THEN '-'
				ELSE e.toko_nama
				END,
				CASE WHEN a.kode_rap ISNULL
				THEN '-'
				ELSE a.kode_rap
				END,
				CASE WHEN c.detail_material_kode ISNULL
				THEN '-'
				ELSE c.detail_material_kode
				END,
				CASE WHEN c.detail_material_nama ISNULL
				THEN '-'
				ELSE c.detail_material_nama
				END,
				a.tanggal,
				a.uraian,
				a.volume,
				a.jumlah,
				a.pilihan,
				CASE WHEN f.pilihan_nama ISNULL
				THEN '-'
				ELSE f.pilihan_nama
				END,
				CASE WHEN g.subbidang_name ISNULL
				THEN 'PEMASUKAN'
				ELSE g.subbidang_name
				END,
				a.debet,
				a.keterangan_item,
				CASE WHEN a.jumlah = 0
				THEN (a.jumlah + a.debet)
				ELSE -(a.jumlah + a.debet)
				END as cek_saldo
				FROM
				simpro_tbl_cashtodate a
				left JOIN simpro_tbl_detail_material c
				on a.detail_material_kode = c.detail_material_kode
				left JOIN simpro_tbl_pilih_toko b
				on a.pilih_toko_id = b.pilih_toko_id
				left JOIN simpro_tbl_toko e
				on b.toko_id = e.toko_id
				left JOIN simpro_m_pilihan f
				on a.pilihan = f.pilihan_id
				left JOIN simpro_tbl_subbidang g
				on left(c.detail_material_kode,3) = g.subbidang_kode
				ORDER BY id asc
				),
				combine AS
				(select * from (
				SELECT 
				pic,
				proyek_id,
				id,
				detail_material_id,
				pilih_toko_id,
				no_bukti,
				toko_kode, 
				toko_nama, 
				kode_rap, 
				detail_material_kode, 
				detail_material_nama,
				tanggal,
				uraian,
				volume,
				jumlah,
				pilihan, 
				pilihan_nama, 
				subbidang_name, 
				debet, 
				keterangan_item, 
				cek_saldo,									
				case when subbidang_name = 'PEMASUKAN'
				then 1
				else 2
				end as urutan
				FROM cash
				union
				select
				a.pic as pic,
				a.proyek_id as proyek_id,
				0 as id,
				0 as detail_material_id,
				0 as pilih_toko_id,
				a.no_bukti_bayar_hutang as no_bukti,
				'-' as toko_kode, 
				'-' as toko_nama, 
				'-' as kode_rap, 
				'-' as detail_material_kode, 
				'-' as detail_material_nama,
				a.tanggal as tanggal,
				'-' as uraian,
				0 as volume,
				sum(a.bayar) as jumlah,
				0 as pilihan, 
				'-' as pilihan_nama, 
				'PEMBAYARAN HUTANG' as subbidang_name, 
				0 as debet, 
				'Pembayaran Hutang' as keterangan_item, 
				-sum(a.bayar) as cek_saldo,
				3 as urutan
				from
				simpro_tbl_bayar_hutang a
				group by
				a.pic,
				a.no_bukti,
				a.tanggal,
				a.no_bukti_bayar_hutang,
				a.proyek_id) result
				where proyek_id = $proyek_id and tanggal >= '$tgl_awal' and tanggal <= '$tgl_akhir'
				order by tanggal, urutan, subbidang_name
				),
				cteRanked as (
				select *,
				ROW_NUMBER() OVER(order by tanggal, urutan, subbidang_name) rownum
				from combine
				)
				SELECT 
				pic,
				proyek_id,
				id,
				detail_material_id,
				pilih_toko_id,
				no_bukti,
				toko_kode, 
				toko_nama, 
				kode_rap, 
				detail_material_kode, 
				detail_material_nama,
				tanggal,
				uraian,
				volume,
				jumlah,
				pilihan, 
				pilihan_nama, 
				subbidang_name, 
				debet, 
				keterangan_item, 
				cek_saldo,
				rownum,
				urutan,
				urutan || ' ' || subbidang_name as urutan_subbidang_name,
				(SELECT 
				SUM(cek_saldo) FROM cteRanked c2 WHERE c2.rownum <= c1.rownum) as saldo
				FROM cteRanked c1
				where c1.proyek_id = $proyek_id
				and lower($pilihan_sort) like lower('%$sort%')
				and c1.tanggal >= '$tgl_awal'
				and c1.tanggal <= '$tgl_akhir'";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_kladbank($proyek_id,$get)
	{
		$sort = $get['sort'];
		$pilihan_sort = $get['pilihan_sort'];
		$tgl_awal = $get['tgl_awal'];
		$tgl_akhir = $get['tgl_akhir'];

		$sql = " WITH cash as (SELECT
				a.pic,
				a.proyek_id,
				a.kladbankid as id,
				a.detail_material_id,
				a.pilih_toko_id,
				a.no_bukti,
				CASE WHEN e.toko_kode ISNULL
				THEN '-'
				ELSE e.toko_kode
				END,
				CASE WHEN e.toko_nama ISNULL
				THEN '-'
				ELSE e.toko_nama
				END,
				CASE WHEN a.kode_rap ISNULL
				THEN '-'
				ELSE a.kode_rap
				END,
				CASE WHEN c.detail_material_kode ISNULL
				THEN '-'
				ELSE c.detail_material_kode
				END,
				CASE WHEN c.detail_material_nama ISNULL
				THEN '-'
				ELSE c.detail_material_nama
				END,
				a.tanggal,
				a.uraian,
				a.volume,
				a.jumlah,
				a.pilihan,
				CASE WHEN f.pilihan_nama ISNULL
				THEN '-'
				ELSE f.pilihan_nama
				END,
				CASE WHEN g.subbidang_name ISNULL
				THEN 'PEMASUKAN'
				ELSE g.subbidang_name
				END,
				a.debet,
				a.keterangan_item,
				CASE WHEN a.jumlah = 0
				THEN (a.jumlah + a.debet)
				ELSE -(a.jumlah + a.debet)
				END as cek_saldo
				FROM
				simpro_tbl_kladbank a
				left JOIN simpro_tbl_detail_material c
				on a.detail_material_kode = c.detail_material_kode
				left JOIN simpro_tbl_pilih_toko b
				on a.pilih_toko_id = b.pilih_toko_id
				left JOIN simpro_tbl_toko e
				on b.toko_id = e.toko_id
				left JOIN simpro_m_pilihan f
				on a.pilihan = f.pilihan_id
				left JOIN simpro_tbl_subbidang g
				on left(c.detail_material_kode,3) = g.subbidang_kode
				where a.proyek_id = $proyek_id and a.tanggal >= '$tgl_awal' and a.tanggal <= '$tgl_akhir'
				ORDER BY id asc),
				cteRanked AS
				(
				SELECT 
				pic,
				proyek_id,
				id,
				detail_material_id,
				pilih_toko_id,
				no_bukti,
				toko_kode, 
				toko_nama, 
				kode_rap, 
				detail_material_kode, 
				detail_material_nama,
				tanggal,
				uraian,
				volume,
				jumlah,
				pilihan, 
				pilihan_nama, 
				subbidang_name, 
				debet, 
				keterangan_item, 
				cek_saldo,
				ROW_NUMBER() OVER(ORDER BY id) rownum
				FROM cash
				)
				SELECT 
				pic,
				proyek_id,
				id,
				detail_material_id,
				pilih_toko_id,
				no_bukti,
				toko_kode, 
				toko_nama, 
				kode_rap, 
				detail_material_kode, 
				detail_material_nama,
				tanggal,
				uraian,
				volume,
				jumlah,
				pilihan, 
				pilihan_nama, 
				subbidang_name, 
				debet, 
				keterangan_item, 
				cek_saldo,
				(SELECT 
				SUM(cek_saldo) FROM cteRanked c2 WHERE c2.rownum <= c1.rownum) as saldo
				FROM cteRanked c1
				where c1.proyek_id = $proyek_id
				and lower($pilihan_sort) like lower('%$sort%')
				and c1.tanggal >= '$tgl_awal'
				and c1.tanggal <= '$tgl_akhir'
				ORDER BY id asc";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}
	
	function get_data_hutang($proyek_id,$get)
	{
		$sort = $get['sort'];
		$pilihan_sort = $get['pilihan_sort'];
		$tgl_awal = $get['tgl_awal'];
		$tgl_akhir = $get['tgl_akhir'];

		$sql = "SELECT
				a.hutangonkeu_id as id,
				a.detail_material_id,
				a.pilih_toko_id,
				a.no_bukti,
				e.toko_kode,
				e.toko_nama,
				a.kode_rap,
				c.detail_material_kode,
				c.detail_material_nama,
				a.tanggal,
				a.uraian,
				a.volume,
				a.jumlah,
				a.pilihan,
				f.pilihan_nama,
				g.subbidang_name,
				a.keterangan_item,
				a.pic
				FROM
				simpro_tbl_hutangonkeu a
				JOIN simpro_tbl_pilih_toko b
				on a.pilih_toko_id = b.pilih_toko_id
				JOIN simpro_tbl_detail_material c
				on a.detail_material_kode = c.detail_material_kode
				JOIN simpro_tbl_toko e
				on b.toko_id = e.toko_id
				join simpro_m_pilihan f
				on a.pilihan = f.pilihan_id
				JOIN simpro_tbl_subbidang g
				on left(c.detail_material_kode,3) = g.subbidang_kode
				where a.proyek_id = $proyek_id
				and lower($pilihan_sort) like lower('%$sort%')
				and a.tanggal >= '$tgl_awal'
				and a.tanggal <= '$tgl_akhir'
				ORDER BY subbidang_name asc";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_antisipasi($proyek_id,$get)
	{
		$sort = $get['sort'];
		$pilihan_sort = $get['pilihan_sort'];
		$tgl_awal = $get['tgl_awal'];
		$tgl_akhir = $get['tgl_akhir'];

		$sql = "SELECT
				a.hutang_proses_id as id,
				a.detail_material_id,
				a.pilih_toko_id,
				a.no_bukti,
				e.toko_kode,
				e.toko_nama,
				a.kode_rap,
				c.detail_material_kode,
				c.detail_material_nama,
				a.tanggal,
				a.uraian,
				a.volume,
				a.jumlah,
				a.pilihan,
				f.pilihan_nama,
				g.subbidang_name,
				a.keterangan_item,
				a.pic
				FROM
				simpro_tbl_hutang_proses a
				JOIN simpro_tbl_pilih_toko b
				on a.pilih_toko_id = b.pilih_toko_id
				JOIN simpro_tbl_detail_material c
				on a.detail_material_kode = c.detail_material_kode
				JOIN simpro_tbl_toko e
				on b.toko_id = e.toko_id
				join simpro_m_pilihan f
				on a.pilihan = f.pilihan_id
				JOIN simpro_tbl_subbidang g
				on left(c.detail_material_kode,3) = g.subbidang_kode
				where a.proyek_id = $proyek_id
				and lower($pilihan_sort) like lower('%$sort%')
				and a.tanggal >= '$tgl_awal'
				and a.tanggal <= '$tgl_akhir'
				ORDER BY subbidang_name asc";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_combo_item_material($proyek_id)
	{
		$sql = "select 
				a.kode_rap as value,
				b.detail_material_nama || ' => ' ||a.kode_rap as text,
				b.detail_material_satuan as satuan,
				b.detail_material_nama as nama,
				b.detail_material_kode as kode,
				a.id_detail_material,
				a.kode_rap,
				a.harga as harga
				from simpro_rap_analisa_asat a
				join simpro_tbl_detail_material b 
				on a.kode_material = b.detail_material_kode
				where a.id_proyek = $proyek_id
				group by
				value,
				text,
				a.kode_material,
				b.detail_material_nama,
				b.detail_material_satuan,
				a.kode_rap,
				a.harga,
				a.id_detail_material,
				b.detail_material_kode
				order by a.kode_rap, text";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_combo_item_material_antisipasi($proyek_id)
	{
		$sql = "select 
				a.kode_rap as value,
				b.detail_material_nama || ' => ' ||a.kode_rap as text,
				b.detail_material_satuan as satuan,
				b.detail_material_nama as nama,
				b.detail_material_kode as kode,
				a.id_detail_material,
				a.kode_rap,
				a.harga as harga
				from simpro_rap_analisa_asat a
				join simpro_tbl_detail_material b 
				on a.kode_material = b.detail_material_kode
				where a.id_proyek = $proyek_id and left(b.detail_material_kode,3) = '508'
				group by
				value,
				text,
				a.kode_material,
				b.detail_material_nama,
				b.detail_material_satuan,
				a.kode_rap,
				a.harga,
				a.id_detail_material,
				b.detail_material_kode
				order by a.kode_rap, text";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_combo_item_toko($proyek_id)
	{
		$sql = "SELECT
				a.pilih_toko_id as value,
				b.toko_kode || ' => ' || b.toko_nama as text
				FROM
				simpro_tbl_pilih_toko a
				join simpro_tbl_toko b
				on a.toko_id = b.toko_id
				where a.proyek_id = $proyek_id";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_combo_pilihan()
	{
		$sql = "SELECT
				pilihan_id as value,
				pilihan_nama as text
				FROM
				simpro_m_pilihan";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function insertdata($info,$data)
	{
		switch ($info) {
			case 'cashtodate':
				$this->db->insert('simpro_tbl_cashtodate',$data);
			break;
			case 'hutang':
				$this->db->insert('simpro_tbl_hutangonkeu',$data);
			break;
			case 'antisipasi':
				$this->db->insert('simpro_tbl_hutang_proses',$data);
			break;
			case 'kladbank':
				$this->db->insert('simpro_tbl_kladbank',$data);
			break;
			case 'bayar_hutang':
				$this->db->insert('simpro_tbl_bayar_hutang',$data);
			break;
		}
	}

	function delete_data($info,$data)
	{
		switch ($info) {
			case 'cashtodate':
				$id = $data['id'];
				$var = array(
					'cashtodateid' => $id
				);
				$this->db->delete('simpro_tbl_cashtodate',$var);
			break;
			case 'hutang':
				$id = $data['id'];
				$no_bukti = $data['no_bukti'];
				$var = array(
					'hutangonkeu_id' => $id
				);
				$var_bayar = array(
					'no_bukti' => $no_bukti
				);

				$this->db->delete('simpro_tbl_bayar_hutang',$var_bayar);
				$this->db->delete('simpro_tbl_hutangonkeu',$var);
			break;
			case 'antisipasi':
				$id = $data['id'];
				$var = array(
					'hutang_proses_id' => $id
				);
				$this->db->delete('simpro_tbl_hutang_proses',$var);
			break;
			case 'kladbank':
				$id = $data['id'];
				$var = array(
					'kladbankid' => $id
				);
				$this->db->delete('simpro_tbl_kladbank',$var);
			break;
			case 'bayar_hutang':
				$id = $data['id'];
				$var = array(
					'bayar_hutang_id' => $id
				);
				$this->db->delete('simpro_tbl_bayar_hutang',$var);
			break;
			case 'piutang':
				$id = $data['id'];
				$var = array(
					'id_piutang' => $id
				);
				$this->db->delete('simpro_tbl_piutang',$var);
			break;
		}
	}

	function editdata($info,$data,$id)
	{
		switch ($info) {
			case 'cashtodate':
				$this->db->where('cashtodateid',$id);
				$this->db->update('simpro_tbl_cashtodate',$data);
			break;
			case 'kladbank':
				$this->db->where('kladbankid',$id);
				$this->db->update('simpro_tbl_kladbank',$data);
			break;
			case 'hutang':
				$this->db->where('hutangonkeu_id',$id);
				$this->db->update('simpro_tbl_hutangonkeu',$data);
			break;
			case 'antisipasi':
				$this->db->where('hutang_proses_id',$id);
				$this->db->update('simpro_tbl_hutang_proses',$data);
			break;
		}
	}

	function get_combo_no_bukti($proyek_id)
	{
		$sql = "SELECT
				no_bukti as text,
				no_bukti as value,
				sum(jumlah) as jumlah
				FROM
				simpro_tbl_hutangonkeu
				where proyek_id = $proyek_id
				GROUP BY no_bukti";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_bayar_hutang($proyek_id,$no_bukti)
	{
		$sql = "SELECT
				a.bayar_hutang_id as id,
				a.no_bukti,
				a.bayar,
				a.tanggal,
				a.pic,
				a.no_bukti_bayar_hutang,
				(
				SELECT
				SUM(bayar)
				FROM simpro_tbl_bayar_hutang b WHERE b.no_bukti = a.no_bukti
				) as jml
				FROM
				simpro_tbl_bayar_hutang a
				WHERE no_bukti = '$no_bukti'
				and proyek_id = $proyek_id";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_laporan_hutang($proyek_id,$get)
	{
		$tgl_awal = $get['tgl_awal'];
		$tgl_akhir = $get['tgl_akhir'];

		$sql = "WITH hutang as (
				SELECT
				a.proyek_id,
				a.tanggal,
				a.no_bukti,
				a.pic,
				sum(a.jumlah) as jumlah,
				array_to_string(array(
				select d.detail_material_kode || 
				' : ' || 
				d.detail_material_nama || 
				' = Rp. ' ||
				c.jumlah ||
				' (' ||
				c.keterangan_item||
				') ' ||
				'<br>'
				from simpro_tbl_hutangonkeu c
				JOIN simpro_tbl_detail_material d
				on d.detail_material_id =  c.detail_material_id
				where c.no_bukti = a.no_bukti order by d.detail_material_kode
				), ''
				) detail,
				case when array_to_string(array(
				select 
				e.tanggal || ' = Rp. ' || e.bayar || '<br>'
				from simpro_tbl_bayar_hutang e
				where e.no_bukti = a.no_bukti
				), ''
				) = ''
				then '-'
				else array_to_string(array(
				select 
				e.tanggal || ' = Rp. ' || e.bayar || '<br>'
				from simpro_tbl_bayar_hutang e
				where e.no_bukti = a.no_bukti
				), ''
				)
				end pembayaran,
				case when (
				select 
				sum(f.bayar)
				from simpro_tbl_bayar_hutang f
				where f.no_bukti = a.no_bukti
				) ISNULL
				THEN 0
				else (
				select 
				sum(f.bayar)
				from simpro_tbl_bayar_hutang f
				where f.no_bukti = a.no_bukti
				)
				end telah_dibayar
				FROM
				simpro_tbl_hutangonkeu a
				GROUP BY
				a.tanggal,
				a.no_bukti,
				a.pic,
				a.proyek_id
				)
				SELECT *, (jumlah - telah_dibayar) as sisa_hutang FROM hutang
				where proyek_id = $proyek_id
				";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_saldo_kas($proyek_id,$get)
	{
		$tgl_akhir = $get['tgl_akhir'];

		$sql_klad_bank = "SELECT
				sum(a.debet - a.jumlah) as klad_bank
				FROM
				simpro_tbl_kladbank a
				where a.proyek_id = $proyek_id and a.tanggal <= '$tgl_akhir'";

		$sql_klad_kas = "select 
				(case when a.cash isnull
				then 0
				else a.cash
				end- 
				case when b.hutang isnull
				then 0
				else b.hutang
				end)
				as klad_kas 
				from 
				(select sum(debet - jumlah) as cash from simpro_tbl_cashtodate where proyek_id = $proyek_id and tanggal <= '$tgl_akhir') a,
				(select sum(bayar) as hutang from simpro_tbl_bayar_hutang where proyek_id = $proyek_id and tanggal <= '$tgl_akhir') b";

		$q_klad_bank = $this->db->query($sql_klad_bank);
		$q_klad_kas = $this->db->query($sql_klad_kas);

		if ($q_klad_bank->result()) {
			$row_klad_bank = $q_klad_bank->row();
			$bank = $row_klad_bank->klad_bank;
			if ($bank == '') {
				$bank = 0;
			}
		} else {
			$bank = 0;
		}

		if ($q_klad_kas->result()) {
			$row_klad_kas = $q_klad_kas->row();
			$cash = $row_klad_kas->klad_kas;
			if ($cash == '') {
				$cash = 0;
			}
		} else {
			$cash = 0;
		}

		for ($i=0; $i < 3; $i++) { 
			switch ($i) {
				case 0:
					$data['tanggal'] = $tgl_akhir;
					$data['uraian'] = 'Jumlah Saldo Klad Kas';
					$data['saldo'] = $cash;
				break;
				case 1:
					$data['tanggal'] = $tgl_akhir;
					$data['uraian'] = 'Jumlah Saldo Klad Bank';
					$data['saldo'] = $bank;
				break;
				case 2:
					$data['tanggal'] = '';
					$data['uraian'] = 'Jumlah Total';
					$data['saldo'] = $cash+$bank;
				break;
			}
			
			$dat[] = $data;
		}


		return '{"data":'.json_encode($dat).'}';
	}

	function get_data_piutang($proyek_id)
	{
		$sql = "SELECT * FROM simpro_tbl_piutang where proyek_id = $proyek_id";

		$q = $this->db->query($sql);

		if ($q->result()) {
			$dat = $q->result_object();
		} else {
			$dat = "";
		}

		return '{"data":'.json_encode($dat).'}';
	}

	function kartu_piutang_action($info,$data,$id)
	{
		switch ($info) {
			case 'tambah':
				$this->db->insert('simpro_tbl_piutang',$data);
			break;
			case 'edit':
				$this->db->where('id_piutang',$id);
				$this->db->update('simpro_tbl_piutang',$data);
			break;
		}
	}

	// function get_data_klad_bank($proyek_id)
	// {
	// 	$sql = "WITH cte
	// 			AS
	// 			(
	// 			SELECT
	// 			a.klad_bank_id,
	// 			CASE WHEN a.klad_bank_id ISNULL
	// 			THEN 'non_klad_bank'
	// 			ELSE 'klad_bank'
	// 			END as status,
	// 			CASE WHEN a.proyek_id ISNULL and c.proyek_id ISNULL and d.proyek_id ISNULL
	// 			THEN b.proyek_id
	// 			WHEN a.proyek_id ISNULL and b.proyek_id ISNULL and d.proyek_id ISNULL
	// 			THEN c.proyek_id
	// 			WHEN a.proyek_id ISNULL and b.tanggal_cash ISNULL and c.proyek_id ISNULL
	// 			THEN d.proyek_id
	// 			ELSE a.proyek_id
	// 			END as proyek_id,
	// 			CASE WHEN a.tanggal ISNULL and c.tanggal_hutang ISNULL and d.tanggal_ant ISNULL
	// 			THEN b.tanggal_cash
	// 			WHEN a.tanggal ISNULL and b.tanggal_cash ISNULL and d.tanggal_ant ISNULL
	// 			THEN c.tanggal_hutang
	// 			WHEN a.tanggal ISNULL and b.tanggal_cash ISNULL and c.tanggal_hutang ISNULL
	// 			THEN d.tanggal_ant
	// 			ELSE a.tanggal
	// 			END as tanggal_klad,
	// 			CASE WHEN a.pic ISNULL and c.pic_hutang ISNULL and d.pic_ant ISNULL
	// 			THEN b.pic_cash
	// 			WHEN a.pic ISNULL and b.pic_cash ISNULL and d.pic_ant ISNULL
	// 			THEN c.pic_hutang
	// 			WHEN a.pic ISNULL and b.pic_cash ISNULL and c.pic_hutang ISNULL
	// 			THEN d.pic_ant
	// 			ELSE a.pic
	// 			END as pic_klad,
	// 			CASE WHEN a.keterangan ISNULL and c.keterangan_hutang ISNULL and d.keterangan_ant ISNULL
	// 			THEN b.keterangan_cash
	// 			WHEN a.keterangan ISNULL and b.keterangan_cash ISNULL and d.keterangan_ant ISNULL
	// 			THEN c.keterangan_hutang
	// 			WHEN a.keterangan ISNULL and b.keterangan_cash ISNULL and c.keterangan_hutang ISNULL
	// 			THEN d.keterangan_ant
	// 			ELSE a.keterangan
	// 			END as keterangan_klad,
	// 			CASE WHEN a.no_bukti ISNULL and c.no_bukti_hutang ISNULL and d.no_bukti_ant ISNULL
	// 			THEN b.no_bukti_cash
	// 			WHEN a.no_bukti ISNULL and b.no_bukti_cash ISNULL and d.no_bukti_ant ISNULL
	// 			THEN c.no_bukti_hutang
	// 			WHEN a.no_bukti ISNULL and b.no_bukti_cash ISNULL and c.no_bukti_hutang ISNULL
	// 			THEN d.no_bukti_ant
	// 			ELSE a.no_bukti
	// 			END as no_bukti_klad,
	// 			CASE WHEN a.debet ISNULL
	// 			THEN 0
	// 			ELSE a.debet
	// 			END,
	// 			CASE WHEN a.kredit ISNULL and c.kredit_hutang ISNULL and d.kredit_ant ISNULL and a.debet ISNULL
	// 			THEN b.kredit_cash
	// 			WHEN a.kredit ISNULL and b.kredit_cash ISNULL and d.kredit_ant ISNULL and a.debet ISNULL
	// 			THEN c.kredit_hutang
	// 			WHEN a.kredit ISNULL and b.kredit_cash ISNULL and c.kredit_hutang ISNULL and a.debet ISNULL
	// 			THEN d.kredit_ant
	// 			ELSE a.kredit
	// 			END as kredit,
	// 			CASE WHEN a.kredit ISNULL and c.kredit_hutang ISNULL and d.kredit_ant ISNULL and a.debet ISNULL
	// 			THEN -b.kredit_cash
	// 			WHEN a.kredit ISNULL and b.kredit_cash ISNULL and d.kredit_ant ISNULL and a.debet ISNULL
	// 			THEN -c.kredit_hutang
	// 			WHEN a.kredit ISNULL and b.kredit_cash ISNULL and c.kredit_hutang ISNULL and a.debet ISNULL
	// 			THEN -d.kredit_ant
	// 			ELSE -a.kredit
	// 			END as kredit_klad,
	// 			(CASE WHEN a.debet ISNULL
	// 			THEN 0
	// 			ELSE a.debet
	// 			END) + 
	// 			(CASE WHEN a.kredit ISNULL and c.kredit_hutang ISNULL and d.kredit_ant ISNULL and a.debet ISNULL
	// 			THEN -b.kredit_cash
	// 			WHEN a.kredit ISNULL and b.kredit_cash ISNULL and d.kredit_ant ISNULL and a.debet ISNULL
	// 			THEN -c.kredit_hutang
	// 			WHEN a.kredit ISNULL and b.kredit_cash ISNULL and c.kredit_hutang ISNULL and a.debet ISNULL
	// 			THEN -d.kredit_ant
	// 			ELSE -a.kredit
	// 			END)
	// 			 as jmlh,
	// 			ROW_NUMBER() OVER(ORDER BY 
	// 			(CASE WHEN a.tanggal ISNULL and c.tanggal_hutang ISNULL and d.tanggal_ant ISNULL
	// 			THEN b.tanggal_cash
	// 			WHEN a.tanggal ISNULL and b.tanggal_cash ISNULL and d.tanggal_ant ISNULL
	// 			THEN c.tanggal_hutang
	// 			WHEN a.tanggal ISNULL and b.tanggal_cash ISNULL and c.tanggal_hutang ISNULL
	// 			THEN d.tanggal_ant
	// 			ELSE a.tanggal
	// 			END)
	// 			) as row
	// 			FROM
	// 			simpro_tbl_klad_bank a
	// 			FULL JOIN 
	// 			(SELECT
	// 			tanggal as tanggal_cash,
	// 			pic as pic_cash,
	// 			uraian as keterangan_cash,
	// 			no_bukti as no_bukti_cash,
	// 			proyek_id as proyek_id,
	// 			SUM(jumlah) as kredit_cash
	// 			FROM
	// 			simpro_tbl_cashtodate
	// 			GROUP BY no_bukti, tanggal, pic, uraian, proyek_id) b
	// 			on a.no_bukti = b.no_bukti_cash and a.proyek_id = b.proyek_id
	// 			FULL JOIN 
	// 			(SELECT
	// 			tanggal as tanggal_hutang,
	// 			pic as pic_hutang,
	// 			uraian as keterangan_hutang,
	// 			no_bukti as no_bukti_hutang,
	// 			proyek_id as proyek_id,
	// 			SUM(jumlah) as kredit_hutang
	// 			FROM
	// 			simpro_tbl_hutangonkeu
	// 			GROUP BY no_bukti, tanggal, pic, uraian, proyek_id) c
	// 			on a.no_bukti = c.no_bukti_hutang and a.proyek_id = c.proyek_id
	// 			FULL JOIN
	// 			(SELECT
	// 			tanggal as tanggal_ant,
	// 			pic as pic_ant,
	// 			uraian as keterangan_ant,
	// 			no_bukti as no_bukti_ant,
	// 			proyek_id as proyek_id,
	// 			SUM(jumlah) as kredit_ant
	// 			FROM
	// 			simpro_tbl_hutang_proses
	// 			GROUP BY no_bukti, tanggal, pic, uraian, proyek_id) d
	// 			on a.no_bukti = d.no_bukti_ant and a.proyek_id = d.proyek_id
	// 			ORDER BY tanggal_klad
	// 			),
	// 			cteRanked AS
	// 			(
	// 			   SELECT proyek_id, klad_bank_id, status, tanggal_klad, pic_klad, keterangan_klad, no_bukti_klad, debet, kredit, kredit_klad, jmlh, ROW_NUMBER() OVER(ORDER BY tanggal_klad) rownum
	// 			   FROM cte
	// 			) 
	// 			SELECT proyek_id, klad_bank_id as id, status, tanggal_klad as tanggal, pic_klad as pic, keterangan_klad as keterangan, no_bukti_klad as no_bukti, debet, kredit as kredit,(SELECT 
	// 			SUM(jmlh) FROM cteRanked c2 WHERE c2.rownum <= c1.rownum) as saldo
	// 			FROM cteRanked c1
	// 			WHERE c1.proyek_id = $proyek_id";

	// 	$q = $this->db->query($sql);

	// 	if ($q->result()) {
	// 		$dat = $q->result_object();
	// 	} else {
	// 		$dat = "";
	// 	}

	// 	return '{"data":'.json_encode($dat).'}';
	// }
}