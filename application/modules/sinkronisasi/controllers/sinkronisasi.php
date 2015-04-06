<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sinkronisasi extends MX_Controller {
    function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in'))
        redirect('main/login');
        if($this->session->userdata('proyek_id') <= 0) die("<h2 align='center'>Silahkan pilih proyek terlebih dahulu!</h2>");
        $this->load->library("nuSoap_lib");
    }

    function get_url($url)
    {
      if ($url == 'soap') {
        // $val = "http://simpro.nindyakarya.co.id/simpro-d/sync/index.php?wsdl";
         $val = "http://localhost/simpro/sinkronisasi_buat_server_simpro/index.php?wsdl";
      } elseif ($url == 'con') {
        // $val = 'http://simpro.nindyakarya.co.id/';
        $val = 'http://localhost/simpro/';
      }

      return $val;
    }

    function getRealIpAddr()
    {
      exec("ipconfig /all", $out, $res);

      foreach (preg_grep('/^\s*IPv4 Address[^:]*:\s*([0-9a-f-]+)/i', $out) as $line) {
          $ipA = print_r(substr(strrchr($line, ' '), 1), PHP_EOL);
          $ip = substr($ipA, 0,-11);
      }

      if (!isset($ip)) {
        $ip = $this->session->userdata('ip_address');
      }
      return $ip;

    }

    function sinkron()
    {

      $id_arr = explode(',', $this->input->post('id'));
      foreach ($id_arr as $k => $val) {
        $this->sync(trim($val));
        // echo trim($val).'<br>';
      }
    }

    function insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk)
    {
      $url_soap = $this->get_url('soap');
      $client = new SoapClient($url_soap);

      $sql_insert_log = "
        insert into tbl_log_sinkronisasi (
          page_sync,
          user_update,
          tgl_update,
          ip_update,
          divisi_update,
          waktu_update,
          no_spk
        ) values (
          '$arg',
          '$uname_update',
          '$tgl_update',
          '$ip_update',
          '$divisi_update',
          '$waktu_update',
          '$no_spk'
        )
      ";
      $client->query($sql_insert_log);
    }

    function sync($arg=""){
      // ini_set('MAX_EXECUTION_TIME', -1);

      $url_soap = $this->get_url('soap');
		  $client = new SoapClient($url_soap);



      // $client->query("delete from tbl_cost_togo where no_spk = 'spk/Dermaga1/2013/10'");
      // $client->query("delete from tbl_komposisi_togo where no_spk = 'spk/Dermaga1/2013/10'");
      // $client->query("delete from tbl_induk_togo where no_spk = 'spk/Dermaga1/2013/10'");
      // $client->query("delete from tbl_tahap_kendali where no_spk = '[EDIT] SPK ID Tender 31[/EDIT]'");
      // $client->query("delete from tbl_komposisi_kendali where no_spk = '[EDIT] SPK ID Tender 31[/EDIT]'");
      // $client->query("delete from tbl_induk_sd_kendali where no_spk = '[EDIT] SPK ID Tender 31[/EDIT]'");
      // $client->query("delete from tbl_input_kontrak where no_spk = '[EDIT] SPK ID Tender 31[/EDIT]'");
      // $client->query("delete from tbl_proyek where no_spk = '[EDIT] SPK ID Tender 31[/EDIT]'");
      // $client->query("delete from tbl_kontrak_terkini where no_spk = 'spk/Dermaga1/2013/10'");


    //   if ($this->input->post('q_simpro_s') && $arg == 'simpro_query_sync') {   
  		//     $q = $this->input->post('q_simpro_s');
  		//     $data_proyek = "";
  		// } elseif ($this->input->post('q_simpro_s') && $arg == 'simpro_get_sync') {
  		//     $tbl = $this->input->post('q_simpro_s');
  		//     $data_proyek = $client->getData($tbl);
  		//     echo $data_proyek;
  		// }

      // echo json_decode($client->query("select count('a') as count from tbl_detail_material"))->{'data'}[0]->{'count'};

      $uid = $this->session->userdata('uid');
      $divisi_id = $this->session->userdata('divisi_id');
      $divisi_update= trim($this->session->userdata('divisi'));
      $user_name_data= $this->db->query("select user_name from simpro_tbl_user where user_id = $uid")->row()->user_name;
      $uname_update=trim($this->session->userdata('fullname'));
      $ip_update= $this->getRealIpAddr(); //trim($this->session->userdata('ip_address'));
      $tgl_update=date('Y-m-d');
      $tgl_kendali=date('Y-m').'-01';
      $waktu_update=date('H:i:s');
      $proyek_id = $this->session->userdata('proyek_id');
      $no_spk = $this->db->query("select no_spk from simpro_tbl_proyek where proyek_id = $proyek_id")->row()->no_spk;
      $divisi_kode_proyek=trim($this->db->query("select
      b.divisi_kode
      from
      simpro_tbl_proyek a 
      join simpro_tbl_divisi b
      on a.divisi_kode = b.divisi_id
      where a.proyek_id = $proyek_id")->row()->divisi_kode);

      $msc=microtime(true);
      // $q = $this->db->get('simpro_tbl_detail_material');
      // $sql_get_tahap_kendali = "select * from simpro_tbl_user";

      // $q = $this->db->query($sql_get_tahap_kendali);
      // $msc=microtime(true)-$msc;
      // echo $proyek_id."=> 1 record = ".number_format($msc / ($q->num_rows()), 8, ',', '').' second<br>';
      // echo $q->num_rows().' = '.$msc.' seconds<br>'; // in seconds
      // echo $q->num_rows().' = '.($msc*1000).' milliseconds<br>';

      // echo $client->query("select * from tbl_proyek where no_spk = '$no_spk'");

      if($arg == 'sumber_daya'){

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $data_detail_material_S = $client->query("select * from tbl_detail_material"); // WHERE tgl_update='now()'
        $data_decode_S = json_decode($data_detail_material_S);
        $data_S = $data_decode_S->{'data'};
        $data_S_total = $data_decode_S->{'total'};

        $no1_S = 0;
        $no2_S = 0;

        $qounter = 1;
          if ($data_S) {
            for ($i=0; $i < count($data_S); $i++) { 
              // echo $qounter." ===> percent : ". ($qounter/$data_S_total) * 100 ."<br>";
              $pecah_user_update = explode(" ", trim($data_S[$i]->{'user_update'}));

              $kd_detail_material_S = $data_S[$i]->{'detail_material_kode'};
              $cek_data_S = $this->db->query("select detail_material_kode from simpro_tbl_detail_material where detail_material_kode='". $kd_detail_material_S ."'");
              // $cek_data_S2 = $this->db->query("select detail_material_kode from tbl_detail_material where detail_material_kode='". $kd_detail_material_S ."'");
              
              $qounter++;
              // echo $qounter;

              $user_update_S = $this->session->userdata('uid');
              $divisi_update_S = $this->session->userdata('divisi_id');

              $q_subbidang_id_S = $this->db->query("select * from simpro_tbl_subbidang WHERE subbidang_kode = '" . $data_S[$i]->{'subbidang_kode'} . "'");
              if ($q_subbidang_id_S) {
                $r_subbidang_id_S = $q_subbidang_id_S->row();
                $subbidang_id_S = $r_subbidang_id_S->subbidang_id;
              }

              $jml_data_S = $cek_data_S->num_rows();
              // $jml_data_S2 = $cek_data_S2->num_rows();

              // if($jml_data_S2 == 0){

              //   $insert_S2 = 
              //   "INSERT INTO tbl_detail_material 
              //   (
              //   detail_material_kode,
              //   detail_material_nama,
              //   detail_material_spesifikasi,
              //   subbidang_kode,
              //   detail_material_satuan,
              //   detail_material_harga,
              //   detail_material_propinsi,
              //   user_update,
              //   tgl_update,
              //   ip_update,
              //   divisi_update,
              //   waktu_update
              //   ) VALUES (
              //   '". $data_S[$i]->{'detail_material_kode'} ."',
              //   '". $data_S[$i]->{'detail_material_nama'} ."',
              //   '". $data_S[$i]->{'detail_material_spesifikasi'} ."',
              //   '". $data_S[$i]->{'subbidang_kode'} ."',
              //   '". $data_S[$i]->{'detail_material_satuan'} ."',
              //   '". $data_S[$i]->{'detail_material_harga'} ."',
              //   '". $data_S[$i]->{'detail_material_propinsi'} ."',
              //   '". $uname_update ."',
              //   '". $data_S[$i]->{'tgl_update'} ."',
              //   '". $ip_update ."',
              //   '". $divisi_update ."',
              //   '". $data_S[$i]->{'waktu_update'} ."'
              //   )";
                
              //   $this->db->query($insert_S2);

              //   $no1_S = $no1_S+1;

              // } else {

              //   $update_S2 = 
              //   "UPDATE tbl_detail_material 
              //   SET
              //   detail_material_nama = '". $data_S[$i]->{'detail_material_nama'} ."',
              //   detail_material_spesifikasi = '". $data_S[$i]->{'detail_material_spesifikasi'} ."',
              //   subbidang_kode = '". $data_S[$i]->{'subbidang_kode'} ."',
              //   detail_material_satuan = '". $data_S[$i]->{'detail_material_satuan'} ."',
              //   detail_material_harga = '". $data_S[$i]->{'detail_material_harga'} ."',
              //   detail_material_propinsi = '". $data_S[$i]->{'detail_material_propinsi'} ."',
              //   user_update = '". $uname_update ."',
              //   tgl_update = '". $data_S[$i]->{'tgl_update'} ."',
              //   ip_update = '". $ip_update ."',
              //   divisi_update = '". $divisi_update ."',
              //   waktu_update = '". $data_S[$i]->{'waktu_update'} ."'
              //   WHERE detail_material_kode='". $kd_detail_material_S ."'";

              //   $this->db->query($update_S2);

              //   $no2_S = $no2_S+1;
              // }

              if($jml_data_S == 0){

                $insert_S = 
                "INSERT INTO simpro_tbl_detail_material 
                (
                detail_material_kode,
                detail_material_nama,
                detail_material_spesifikasi,
                subbidang_id,
                subbidang_kode,
                detail_material_satuan,
                detail_material_harga,
                detail_material_propinsi,
                user_update,
                tgl_update,
                ip_update,
                divisi_update,
                waktu_update
                ) VALUES (
                '". $data_S[$i]->{'detail_material_kode'} ."',
                '". $data_S[$i]->{'detail_material_nama'} ."',
                '". $data_S[$i]->{'detail_material_spesifikasi'} ."',
                '". $subbidang_id_S ."',
                '". $data_S[$i]->{'subbidang_kode'} ."',
                '". $data_S[$i]->{'detail_material_satuan'} ."',
                '". $data_S[$i]->{'detail_material_harga'} ."',
                '". $data_S[$i]->{'detail_material_propinsi'} ."',
                '". $user_update_S ."',
                '". $data_S[$i]->{'tgl_update'} ."',
                '". $ip_update ."',
                '". $divisi_update_S ."',
                '". $data_S[$i]->{'waktu_update'} ."'
                )";
                
                $this->db->query($insert_S);

                $no1_S = $no1_S+1;

              } else {

                $update_S = 
                "UPDATE simpro_tbl_detail_material 
                SET
                detail_material_nama = '". $data_S[$i]->{'detail_material_nama'} ."',
                detail_material_spesifikasi = '". $data_S[$i]->{'detail_material_spesifikasi'} ."',
                subbidang_id = '". $subbidang_id_S ."',
                subbidang_kode = '". $data_S[$i]->{'subbidang_kode'} ."',
                detail_material_satuan = '". $data_S[$i]->{'detail_material_satuan'} ."',
                detail_material_harga = '". $data_S[$i]->{'detail_material_harga'} ."',
                detail_material_propinsi = '". $data_S[$i]->{'detail_material_propinsi'} ."',
                user_update = '". $user_update_S ."',
                tgl_update = '". $data_S[$i]->{'tgl_update'} ."',
                ip_update = '". $ip_update ."',
                divisi_update = '". $divisi_update_S ."',
                waktu_update = '". $data_S[$i]->{'waktu_update'} ."'
                WHERE detail_material_kode='". $kd_detail_material_S ."'";

                $this->db->query($update_S);

                $no2_S = $no2_S+1;
              }
            }
          }
          
        // echo "Sinkronisasi Insert ke Client $no1_S <br>Sinkronisasi Update ke Client $no2_S<br>";  

        $data_detail_material_C = $this->db->query("select * from simpro_tbl_detail_material WHERE tgl_update='now()'");
        

        if ($data_detail_material_C->result()) {
          $data_C = $data_detail_material_C->result_array();

          $no1 = 0;

          foreach($data_C as $k=>$v){
              $kd_detail_material = $v['detail_material_kode'];
              $cek_data = $client->query("select count(detail_material_kode) as jml from tbl_detail_material where detail_material_kode='". $kd_detail_material ."'");
              
              $data_count = json_decode($cek_data);
              $d = $data_count->{'data'};
              $jml_data = $d[0]->{'jml'};

              if($jml_data == 0){

                $insert = 
                "INSERT INTO tbl_detail_material 
                (
                detail_material_kode,
                detail_material_nama,
                detail_material_spesifikasi,
                subbidang_kode,
                detail_material_satuan,
                detail_material_harga,
                detail_material_propinsi,
                user_update,
                tgl_update,
                ip_update,
                divisi_update,
                waktu_update
                ) VALUES (
                '". $v['detail_material_kode'] ."',
                '". $v['detail_material_nama'] ."',
                '". $v['detail_material_spesifikasi'] ."',
                '". $v['subbidang_kode'] ."',
                '". $v['detail_material_satuan'] ."',
                '". $v['detail_material_harga'] ."',
                '". $v['detail_material_propinsi'] ."',
                '". $uname_update ."',
                '". $tgl_update ."',
                '". $ip_update ."',
                '". $divisi_update ."',
                '". $waktu_update ."'
                )";
                
                $client->query($insert);

                $no1 = $no1+1;

              }
            }
        }

          

          // echo "Sinkronisasi Insert ke Server $no1";


      }

      if ($arg=='proyek') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);


        $q_proyek = $this->db->query("select 
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
          then berakhir
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
          (select sbu_kode from simpro_tbl_sbu where sbu_id = a.sbu_kode) as sbu_kode,
          (select divisi_kode from simpro_tbl_divisi where divisi_id = a.divisi_kode) as divisi_kode,
          (select status_proyek from simpro_tbl_status_proyek where id_proyek_status = a.proyek_status) as proyek_status,
          (select nama_provinsi from simpro_tbl_provinsi where id_provinsi = a.propinsi) as propinsi,
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
          coalesce(sts_pekerjaan,0) as sts_pekerjaan
          from
          simpro_tbl_proyek a
          where a.proyek_id = '$proyek_id'");

        $noSpkOffline = $q_proyek->row();
        $q = "select count(no_spk) as count from tbl_proyek where no_spk = '$noSpkOffline->no_spk'";
        $q_data = json_decode($client->query($q));
        $data = $q_data->{'data'};
        $count = $data[0]->{'count'};
        // var_dump($_SERVER);

        if ($count == 0) {
          $q_insert = "Insert into tbl_proyek
          (
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
          tgl_pengumuman,
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
          nilai_kontrak_ppn,
          nilai_kontrak_non_ppn,
          uang_muka,
          retensi,
          denda_minimal,
          denda_maksimal,
          rap_usulan,
          rap_ditetapkan,
          jangka_waktu,
          perpanjangan_waktu,
          masa_pemeliharaan,
          total_waktu_pelaksanaan,
          pph_final,
          bast_1,
          bast_2,
          sts_pekerjaan          
          ) values 
          (
          '$noSpkOffline->proyek',
          '$noSpkOffline->lingkup_pekerjaan',
          '$noSpkOffline->proyek_alamat',
          '$noSpkOffline->proyek_telp',
          '$noSpkOffline->lokasi_proyek',
          '$noSpkOffline->pemberi_kerja',
          '$noSpkOffline->kepala_proyek',
          '$noSpkOffline->proyek_konsultan_pengawas',
          '$noSpkOffline->no_spk',
          '$noSpkOffline->proyek_nama_sumber_1',
          '$noSpkOffline->proyek_nilai_sumber_1',
          '$noSpkOffline->termijn',
          '$noSpkOffline->jaminan_pelaksanaan',
          '$noSpkOffline->asuransi_pekerjaan',
          '$noSpkOffline->sifat_kontrak',
          '$noSpkOffline->manajemen_pelaksanaan',
          '$noSpkOffline->eskalasi',
          '$noSpkOffline->beda_kurs',
          '$noSpkOffline->pek_tambah_kurang',
          '$noSpkOffline->mulai',
          '$noSpkOffline->berakhir',
          '$noSpkOffline->tgl_tender',
          '$noSpkOffline->tgl_pengumuman',
          '$uname_update',
          '$tgl_update',
          '$ip_update',
          '$divisi_update',
          '$waktu_update',
          '$noSpkOffline->wo',
          '$noSpkOffline->kode_wilayah',
          '$noSpkOffline->kode_propinsi1',
          '$noSpkOffline->no_kontrak',
          '$noSpkOffline->status_pekerjaan',
          '$noSpkOffline->struktur_organisasi',
          '$noSpkOffline->sketsa_proyek',
          '$noSpkOffline->lokasi_latitude',
          '$noSpkOffline->lokasi_longitude',
          '$noSpkOffline->no_kontrak2',
          '$noSpkOffline->no_spk_2',
          '$noSpkOffline->id_excel',
          '$noSpkOffline->sbu_kode',
          '$noSpkOffline->divisi_kode',
          '$noSpkOffline->proyek_status',
          '$noSpkOffline->propinsi',
          $noSpkOffline->nilai_kontrak_ppn,
          $noSpkOffline->nilai_kontrak_non_ppn,
          $noSpkOffline->uang_muka,
          $noSpkOffline->retensi,
          $noSpkOffline->denda_minimal,
          $noSpkOffline->denda_maksimal,
          $noSpkOffline->rap_usulan,
          $noSpkOffline->rap_ditetapkan,
          $noSpkOffline->jangka_waktu,
          $noSpkOffline->perpanjangan_waktu,
          $noSpkOffline->masa_pemeliharaan,
          $noSpkOffline->total_waktu_pelaksanaan,
          $noSpkOffline->pph_final,
          '$noSpkOffline->bast_1',
          '$noSpkOffline->bast_2',
          $noSpkOffline->sts_pekerjaan
          )";

          $client->query($q_insert);

          // echo $q_insert;
        } else {
          $q_update = "Update tbl_proyek set
            proyek   = '$noSpkOffline->proyek',
            lingkup_pekerjaan  = '$noSpkOffline->lingkup_pekerjaan',
            proyek_alamat  = '$noSpkOffline->proyek_alamat',
            proyek_telp  = '$noSpkOffline->proyek_telp',
            lokasi_proyek  = '$noSpkOffline->lokasi_proyek',
            pemberi_kerja  = '$noSpkOffline->pemberi_kerja',
            kepala_proyek  = '$noSpkOffline->kepala_proyek',
            proyek_konsultan_pengawas  = '$noSpkOffline->proyek_konsultan_pengawas',
            no_spk   = '$noSpkOffline->no_spk',
            proyek_nama_sumber_1   = '$noSpkOffline->proyek_nama_sumber_1',
            proyek_nilai_sumber_1  = '$noSpkOffline->proyek_nilai_sumber_1',
            termijn  = '$noSpkOffline->termijn',
            jaminan_pelaksanaan  = '$noSpkOffline->jaminan_pelaksanaan',
            asuransi_pekerjaan   = '$noSpkOffline->asuransi_pekerjaan',
            sifat_kontrak  = '$noSpkOffline->sifat_kontrak',
            manajemen_pelaksanaan  = '$noSpkOffline->manajemen_pelaksanaan',
            eskalasi   = '$noSpkOffline->eskalasi',
            beda_kurs  = '$noSpkOffline->beda_kurs',
            pek_tambah_kurang  = '$noSpkOffline->pek_tambah_kurang',
            mulai  = '$noSpkOffline->mulai',
            berakhir   = '$noSpkOffline->berakhir',
            tgl_tender   = '$noSpkOffline->tgl_tender',
            tgl_pengumuman   = '$noSpkOffline->tgl_pengumuman',
            user_update  = '$uname_update',
            tgl_update   = '$tgl_update',
            ip_update  = '$ip_update',
            divisi_update  = '$divisi_update',
            waktu_update   = '$waktu_update',
            wo   = '$noSpkOffline->wo',
            kode_wilayah   = '$noSpkOffline->kode_wilayah',
            kode_propinsi1   = '$noSpkOffline->kode_propinsi1',
            no_kontrak   = '$noSpkOffline->no_kontrak',
            status_pekerjaan   = '$noSpkOffline->status_pekerjaan',
            struktur_organisasi  = '$noSpkOffline->struktur_organisasi',
            sketsa_proyek  = '$noSpkOffline->sketsa_proyek',
            lokasi_latitude  = '$noSpkOffline->lokasi_latitude',
            lokasi_longitude   = '$noSpkOffline->lokasi_longitude',
            no_kontrak2  = '$noSpkOffline->no_kontrak2',
            no_spk_2   = '$noSpkOffline->no_spk_2',
            id_excel   = '$noSpkOffline->id_excel',
            sbu_kode   = '$noSpkOffline->sbu_kode',
            divisi_kode  = '$noSpkOffline->divisi_kode',
            proyek_status  = '$noSpkOffline->proyek_status',
            propinsi   = '$noSpkOffline->propinsi',
            nilai_kontrak_ppn  = $noSpkOffline->nilai_kontrak_ppn,
            nilai_kontrak_non_ppn  = $noSpkOffline->nilai_kontrak_non_ppn,
            uang_muka  = $noSpkOffline->uang_muka,
            retensi  = $noSpkOffline->retensi,
            denda_minimal  = $noSpkOffline->denda_minimal,
            denda_maksimal   = $noSpkOffline->denda_maksimal,
            rap_usulan   = $noSpkOffline->rap_usulan,
            rap_ditetapkan   = $noSpkOffline->rap_ditetapkan,
            jangka_waktu   = $noSpkOffline->jangka_waktu,
            perpanjangan_waktu   = $noSpkOffline->perpanjangan_waktu,
            masa_pemeliharaan  = $noSpkOffline->masa_pemeliharaan,
            total_waktu_pelaksanaan  = $noSpkOffline->total_waktu_pelaksanaan,
            pph_final  = $noSpkOffline->pph_final,
            bast_1  = '$noSpkOffline->bast_1',
            bast_2  = '$noSpkOffline->bast_2',
            sts_pekerjaan  = $noSpkOffline->sts_pekerjaan
            where no_spk = '$noSpkOffline->no_spk'
          ";
          $client->query($q_update);

          // echo $q_update;
        }
      }

      if ($arg == 'rab') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_rab = "select 
                    x.tahap_kode_kendali,
                    x.tahap_nama_kendali,
                    x.tahap_satuan_kendali, 
                    (select no_spk from simpro_tbl_proyek where proyek_id = x.proyek_id) as no_spk,
                    x.tahap_volume_kendali,
                    case when sum(totals.subtotal) = 0 or x.tahap_volume_kendali = 0 then
                    0
                    else
                    sum(totals.subtotal) / x.tahap_volume_kendali
                    end as tahap_harga_satuan_kendali
                    ,
                    (
                    sum(totals.subtotal)
                    ) as tahap_total_kendali,
                    tahap_kode_induk_kendali,
                    now() as tahap_tanggal_kendali,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                    from
                    simpro_tbl_input_kontrak x
                    left join
                    (select
                    tahap_kode_kendali,
                    (tahap_volume_kendali * tahap_harga_satuan_kendali) as subtotal
                    from
                    simpro_tbl_input_kontrak
                    where proyek_id = $proyek_id) as totals
                    on left(totals.tahap_kode_kendali,length(x.tahap_kode_kendali)) = x.tahap_kode_kendali
                    WHERE x.proyek_id = $proyek_id
                    group by x.input_kontrak_id     
                    ORDER BY x.tahap_kode_kendali";
        $q_rab = $this->db->query($sql_rab);
        foreach ($q_rab->result() as $row_rab) {
          $get_rab_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_input_kontrak where tahap_kode_kendali = '$row_rab->tahap_kode_kendali' and no_spk = '$row_rab->no_spk'"));
          $data = $get_rab_decode->{'data'};
          $count = $data[0]->{'count'};
          if ($count == 0) {
            $sql_insert_rab = "
              insert into tbl_input_kontrak (
                tahap_kode_kendali,
                tahap_nama_kendali,
                tahap_satuan_kendali,
                no_spk,
                tahap_volume_kendali,
                tahap_harga_satuan_kendali,
                tahap_total_kendali,
                tahap_kode_induk_kendali,
                tahap_tanggal_kendali,
                user_update,
                tgl_update,
                ip_update,
                divisi_update,
                waktu_update
              ) values (
                '$row_rab->tahap_kode_kendali',
                '$row_rab->tahap_nama_kendali',
                '$row_rab->tahap_satuan_kendali',
                '$row_rab->no_spk',
                $row_rab->tahap_volume_kendali,
                $row_rab->tahap_harga_satuan_kendali,
                $row_rab->tahap_total_kendali,
                '$row_rab->tahap_kode_induk_kendali',
                '$tgl_kendali',
                '$uname_update',
                '$tgl_update',
                '$ip_update',
                '$divisi_update',
                '$waktu_update'
              )
            ";

            $client->query($sql_insert_rab);
          } else {
            $sql_update_rab = "update tbl_input_kontrak set
            tahap_kode_kendali =  '$row_rab->tahap_kode_kendali',
            tahap_nama_kendali = '$row_rab->tahap_nama_kendali',
            tahap_satuan_kendali = '$row_rab->tahap_satuan_kendali',
            no_spk = '$row_rab->no_spk',
            tahap_volume_kendali = $row_rab->tahap_volume_kendali,
            tahap_harga_satuan_kendali = $row_rab->tahap_harga_satuan_kendali,
            tahap_total_kendali  = $row_rab->tahap_total_kendali,
            tahap_kode_induk_kendali = '$row_rab->tahap_kode_induk_kendali',
            user_update  = '$uname_update',
            tgl_update = '$tgl_update',
            ip_update  = '$ip_update',
            divisi_update  = '$divisi_update',
            waktu_update  = '$waktu_update'
            where tahap_kode_kendali = '$row_rab->tahap_kode_kendali' and no_spk = '$row_rab->no_spk'
            ";

            $client->query($sql_update_rab);
          }  

          $get_rab_del = json_decode($client->query("select * from tbl_input_kontrak where no_spk = '$row_rab->no_spk'"));  
          $data_rab_del = $get_rab_del->{'data'};
          foreach ($data_rab_del as $row_rab_del) {
            $kode_kendali_server = $row_rab_del->{'tahap_kode_kendali'};
            $data_rab_local = $this->db->query("select * from simpro_tbl_input_kontrak where proyek_id = $proyek_id and tahap_kode_kendali = '$kode_kendali_server'");
            if ($data_rab_local->num_rows() == 0) {
              $client->query("delete from tbl_input_kontrak where no_spk = '$row_rab->no_spk' and tahap_kode_kendali = '$kode_kendali_server'");
            }
          } 
        }
      }

      if ($arg == "rap") {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);
        echo "RAP";

        $sql_get_tahap_kendali = "select 
          x.kode_tree as tahap_kode_kendali,
          x.tree_item as tahap_nama_kendali,
          x.tree_satuan as tahap_satuan_kendali,
          ''::text as tahap_keterangan_kendali,
          (select no_spk from simpro_tbl_proyek where proyek_id = x.id_proyek) as no_spk, 
          x.volume as tahap_volume_kendali, 
          case when sum(totals.subtotal) = 0 or x.volume = 0 then
          0
          else
          sum(totals.subtotal) / x.volume
          end as tahap_harga_satuan_kendali,
          (
          sum(totals.subtotal)
          ) as tahap_total_kendali,
          x.tree_parent_kode as tahap_kode_induk_kendali
          from
          simpro_rap_item_tree x
          left join
          (SELECT 
            simpro_rap_item_tree.kode_tree,
            simpro_rap_analisa_item_apek.kode_analisa,
            COALESCE(tbl_harga.harga, 0) AS harga,
            (COALESCE(tbl_harga.harga, 0) * simpro_rap_item_tree.volume) as subtotal
          FROM simpro_rap_item_tree 
          LEFT JOIN simpro_rap_analisa_item_apek ON simpro_rap_analisa_item_apek.kode_tree = simpro_rap_item_tree.kode_tree and simpro_rap_analisa_item_apek.id_proyek = $proyek_id
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
              WHERE simpro_rap_analisa_asat.id_proyek= $proyek_id
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
                WHERE id_proyek= $proyek_id
                
                GROUP BY kode_analisa     
              ) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_apek.kode_analisa     
              WHERE simpro_rap_analisa_apek.id_proyek= $proyek_id
              
              ORDER BY 
                simpro_rap_analisa_apek.parent_kode_analisa,        
                simpro_rap_analisa_apek.kode_analisa
              ASC         
            )   
            ) AS tbl_analisa_satuan
            GROUP BY kode_analisa       
          ) as tbl_harga ON tbl_harga.kode_analisa = simpro_rap_analisa_item_apek.kode_analisa            
          WHERE simpro_rap_item_tree.id_proyek = $proyek_id
          ORDER BY simpro_rap_item_tree.kode_tree ASC) as totals
          on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
          WHERE x.id_proyek = $proyek_id
          group by x.rap_item_tree      
          ORDER BY x.kode_tree";

        $q_get_tahap_kendali = $this->db->query($sql_get_tahap_kendali);

        if ($q_get_tahap_kendali->result()) {
          foreach ($q_get_tahap_kendali->result() as $row_rap_tahap) {
            $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_tahap_kendali where tahap_kode_kendali = '$row_rap_tahap->tahap_kode_kendali' and no_spk = '$row_rap_tahap->no_spk'"));
            $data = $get_decode->{'data'};
            $count = $data[0]->{'count'};
            if ($count == 0) {
              $sql_insert_tahap_kendali = "
                insert into tbl_tahap_kendali (
                  tahap_kode_kendali,
                  tahap_nama_kendali,
                  tahap_satuan_kendali,
                  tahap_keterangan_kendali,
                  no_spk,
                  tahap_volume_kendali,
                  tahap_harga_satuan_kendali,
                  tahap_total_kendali,
                  tahap_kode_induk_kendali,
                  tahap_tanggal_kendali,
                  user_update,
                  tgl_update,
                  ip_update,
                  divisi_update,
                  waktu_update
                ) values (
                  '$row_rap_tahap->tahap_kode_kendali',
                  '$row_rap_tahap->tahap_nama_kendali',
                  '$row_rap_tahap->tahap_satuan_kendali',
                  '$row_rap_tahap->tahap_keterangan_kendali',
                  '$row_rap_tahap->no_spk',
                  $row_rap_tahap->tahap_volume_kendali,
                  $row_rap_tahap->tahap_harga_satuan_kendali,
                  $row_rap_tahap->tahap_total_kendali,
                  '$row_rap_tahap->tahap_kode_induk_kendali',
                  '$tgl_kendali',
                  '$uname_update',
                  '$tgl_update',
                  '$ip_update',
                  '$divisi_update',
                  '$waktu_update'
                )
                ";

              $client->query($sql_insert_tahap_kendali);
            } else {
              $sql_update_tahap_kendali = "
                update tbl_tahap_kendali set
                tahap_nama_kendali = '$row_rap_tahap->tahap_nama_kendali',
                tahap_satuan_kendali = '$row_rap_tahap->tahap_satuan_kendali',
                tahap_keterangan_kendali = '$row_rap_tahap->tahap_keterangan_kendali',
                tahap_volume_kendali = $row_rap_tahap->tahap_volume_kendali,
                tahap_harga_satuan_kendali = $row_rap_tahap->tahap_harga_satuan_kendali,
                tahap_total_kendali  = $row_rap_tahap->tahap_total_kendali,
                tahap_kode_induk_kendali = '$row_rap_tahap->tahap_kode_induk_kendali',
                tahap_tanggal_kendali  = '$tgl_kendali',
                user_update  = '$uname_update',
                tgl_update = '$tgl_update',
                ip_update  = '$ip_update',
                divisi_update  = '$divisi_update',
                waktu_update  = '$waktu_update'
                where
                no_spk = '$row_rap_tahap->no_spk' and
                tahap_kode_kendali = '$row_rap_tahap->tahap_kode_kendali'
              ";

              $client->query($sql_update_tahap_kendali);
            }
          }
        }

        $sql_get_induk_kendali = "select 
          'X' as kode_komposisi,
          1 as volume_komposisi,
          1 as koefisien_komposisi,
          (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
          a.kode_tree as kode_tahap_kendali,
          sum(c.subtotal) as total_harga_satuan,
          'ANALISA' as nama_komposisi,
          ''::text as komposisi_satuan,
          sum(c.harga) as harga_satuan,
          0 as no
          from 
          simpro_rap_item_tree a 
          join simpro_rap_analisa_item_apek b 
          on a.rap_item_tree = b.rap_item_tree
          join
          (
          SELECT tbl_analisa_satuan.* FROM (
          (
            SELECT          
              simpro_rap_analisa_asat.kode_analisa,
              simpro_tbl_detail_material.detail_material_kode, 
              simpro_tbl_detail_material.detail_material_nama, 
              simpro_tbl_detail_material.detail_material_satuan,
              simpro_rap_analisa_asat.harga,
              simpro_rap_analisa_asat.koefisien,
              (simpro_rap_analisa_asat.harga * simpro_rap_analisa_asat.koefisien) AS subtotal,
              simpro_rap_analisa_asat.kode_rap,
              simpro_rap_analisa_asat.keterangan
            FROM 
              simpro_rap_analisa_asat
            LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
            LEFT JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_asat.kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_asat.id_proyek)
            LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
            WHERE simpro_rap_analisa_asat.id_proyek= $proyek_id
            ORDER BY 
              simpro_rap_analisa_asat.kode_analisa,
              simpro_tbl_detail_material.detail_material_kode        
            ASC
          )
          UNION ALL 
          (
            select 
            a.parent_kode_analisa as kode_analisa,
            b.kode_material,
            c.detail_material_nama,
            c.detail_material_satuan,
            coalesce(b.harga,0) as harga,
            sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
            sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
            b.kode_rap,
            b.keterangan
            from simpro_rap_analisa_apek a
            join simpro_rap_analisa_asat b
            on a.id_data_analisa = b.id_data_analisa
            join simpro_tbl_detail_material c
            on b.kode_material = c.detail_material_kode
            where a.id_proyek = $proyek_id
            group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
          )   
          ) AS tbl_analisa_satuan
          left join simpro_tbl_subbidang
          on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
          ) c
          on c.kode_analisa = b.kode_analisa
          where a.id_proyek = $proyek_id
          group by a.id_proyek,a.kode_tree";

        $q_get_induk_kendali = $this->db->query($sql_get_induk_kendali);

        if ($q_get_induk_kendali->result()) {
          foreach ($q_get_induk_kendali->result() as $row_rap_induk) {
            $get_decode = json_decode($client->query("select count(kode_tahap_kendali) from tbl_induk_sd_kendali where kode_tahap_kendali = '$row_rap_induk->kode_tahap_kendali' and no_spk = '$row_rap_induk->no_spk'"));
            $data = $get_decode->{'data'};
            $count = $data[0]->{'count'};
            if ($count == 0) {
              $sql_insert_induk_kendali = "
                insert into tbl_induk_sd_kendali (
                  kode_komposisi,
                  volume_komposisi,
                  koefisien_komposisi,
                  no_spk,
                  kode_tahap_kendali,
                  total_harga_satuan,
                  nama_komposisi,
                  komposisi_satuan,
                  harga_satuan,
                  no,
                  tahap_tanggal_kendali
                ) values (
                  '$row_rap_induk->kode_komposisi',
                  $row_rap_induk->volume_komposisi,
                  $row_rap_induk->koefisien_komposisi,
                  '$row_rap_induk->no_spk',
                  '$row_rap_induk->kode_tahap_kendali',
                  $row_rap_induk->total_harga_satuan,
                  '$row_rap_induk->nama_komposisi',
                  '$row_rap_induk->komposisi_satuan',
                  $row_rap_induk->harga_satuan,
                  $row_rap_induk->no,
                  '$tgl_kendali'
                )
                ";

              $client->query($sql_insert_induk_kendali);
            } else {
              $sql_update_induk_kendali = "
                update tbl_induk_sd_kendali set
                kode_komposisi = '$row_rap_induk->kode_komposisi',
                volume_komposisi = $row_rap_induk->volume_komposisi,
                koefisien_komposisi  = $row_rap_induk->koefisien_komposisi,
                total_harga_satuan = $row_rap_induk->total_harga_satuan,
                nama_komposisi = '$row_rap_induk->nama_komposisi',
                komposisi_satuan = '$row_rap_induk->komposisi_satuan',
                harga_satuan = $row_rap_induk->harga_satuan,
                no = $row_rap_induk->no
                where 
                no_spk = '$row_rap_induk->no_spk' and
                kode_tahap_kendali = '$row_rap_induk->kode_tahap_kendali'
              ";
              $client->query($sql_update_induk_kendali);
            }
          }
        }

        $sql_get_komposisi_kendali = "select 
          (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
          c.detail_material_kode,
          a.kode_tree as tahap_kode_kendali,
          1 as komposisi_volume_kendali,
          c.harga as komposisi_harga_satuan_kendali,
          c.subtotal as komposisi_total_kendali,
          c.koefisien as komposisi_koefisien_kendali,
          a.volume * c.koefisien as komposisi_volume_total_kendali,
          'X' as kode_komposisi_kendali,
          c.keterangan as keterangan,
          c.kode_rap
          from 
          simpro_rap_item_tree a 
          join simpro_rap_analisa_item_apek b 
          on a.rap_item_tree = b.rap_item_tree
          join
          (
          SELECT tbl_analisa_satuan.* FROM (
          (
            SELECT          
              simpro_rap_analisa_asat.kode_analisa,
              simpro_tbl_detail_material.detail_material_kode, 
              simpro_tbl_detail_material.detail_material_nama, 
              simpro_tbl_detail_material.detail_material_satuan,
              simpro_rap_analisa_asat.harga,
              simpro_rap_analisa_asat.koefisien,
              (simpro_rap_analisa_asat.harga * simpro_rap_analisa_asat.koefisien) AS subtotal,
              simpro_rap_analisa_asat.kode_rap,
              simpro_rap_analisa_asat.keterangan
            FROM 
              simpro_rap_analisa_asat
            LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
            LEFT JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_asat.kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_asat.id_proyek)
            LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
            WHERE simpro_rap_analisa_asat.id_proyek= $proyek_id
            ORDER BY 
              simpro_rap_analisa_asat.kode_analisa,
              simpro_tbl_detail_material.detail_material_kode        
            ASC
          )
          UNION ALL 
          (
            select 
            a.parent_kode_analisa as kode_analisa,
            b.kode_material,
            c.detail_material_nama,
            c.detail_material_satuan,
            coalesce(b.harga,0) as harga,
            sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
            sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
            b.kode_rap,
            b.keterangan
            from simpro_rap_analisa_apek a
            join simpro_rap_analisa_asat b
            on a.id_data_analisa = b.id_data_analisa
            join simpro_tbl_detail_material c
            on b.kode_material = c.detail_material_kode
            where a.id_proyek = $proyek_id
            group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
          )   
          ) AS tbl_analisa_satuan
          left join simpro_tbl_subbidang
          on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
          ) c
          on c.kode_analisa = b.kode_analisa
          where a.id_proyek = $proyek_id
          order by a.kode_tree";

          $q_get_komposisi_kendali = $this->db->query($sql_get_komposisi_kendali);


          if ($q_get_komposisi_kendali->result()) {
            foreach ($q_get_komposisi_kendali->result() as $row_rap_komposisi) {
              $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_komposisi_kendali where tahap_kode_kendali = '$row_rap_komposisi->tahap_kode_kendali' and no_spk = '$row_rap_komposisi->no_spk' and detail_material_kode = '$row_rap_komposisi->detail_material_kode'"));
              $data = $get_decode->{'data'};
              $count = $data[0]->{'count'};
              if ($count == 0) {
                $sql_insert_komposisi_kendali = "
                  insert into tbl_komposisi_kendali (
                    no_spk,
                    detail_material_kode,
                    tahap_kode_kendali,
                    komposisi_volume_kendali,
                    komposisi_harga_satuan_kendali,
                    komposisi_total_kendali,
                    komposisi_koefisien_kendali,
                    komposisi_volume_total_kendali,
                    kode_komposisi_kendali,
                    keterangan,
                    kode_rap,
                    tahap_tanggal_kendali,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$row_rap_komposisi->no_spk',
                    '$row_rap_komposisi->detail_material_kode',
                    '$row_rap_komposisi->tahap_kode_kendali',
                    '$row_rap_komposisi->komposisi_volume_kendali',
                    '$row_rap_komposisi->komposisi_harga_satuan_kendali',
                    '$row_rap_komposisi->komposisi_total_kendali',
                    '$row_rap_komposisi->komposisi_koefisien_kendali',
                    '$row_rap_komposisi->komposisi_volume_total_kendali',
                    '$row_rap_komposisi->kode_komposisi_kendali',
                    '$row_rap_komposisi->keterangan',
                    '$row_rap_komposisi->kode_rap',
                    '$tgl_kendali',
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )
                  ";

                $client->query($sql_insert_komposisi_kendali);
              } else {
                $sql_update_komposisi_kendali = "
                  update tbl_komposisi_kendali set
                  komposisi_volume_kendali = '$row_rap_komposisi->komposisi_volume_kendali',
                  komposisi_harga_satuan_kendali = '$row_rap_komposisi->komposisi_harga_satuan_kendali',
                  komposisi_total_kendali  = '$row_rap_komposisi->komposisi_total_kendali',
                  komposisi_koefisien_kendali  = '$row_rap_komposisi->komposisi_koefisien_kendali',
                  komposisi_volume_total_kendali = '$row_rap_komposisi->komposisi_volume_total_kendali',
                  kode_komposisi_kendali = '$row_rap_komposisi->kode_komposisi_kendali',
                  keterangan = '$row_rap_komposisi->keterangan',
                  kode_rap = '$row_rap_komposisi->kode_rap',
                  user_update  = '$uname_update',
                  tgl_update = '$tgl_update',
                  ip_update  = '$ip_update',
                  divisi_update  = '$divisi_update',
                  waktu_update  = '$waktu_update'
                  where 
                  no_spk = '$row_rap_komposisi->no_spk' and 
                  tahap_kode_kendali = '$row_rap_komposisi->tahap_kode_kendali' and 
                  detail_material_kode = '$row_rap_komposisi->detail_material_kode'
                ";

                $client->query($sql_update_komposisi_kendali);
              }
            }
          }

          $get_rap_tahap_server = json_decode($client->query("select * from tbl_tahap_kendali where no_spk = '$no_spk'"));
          $data_rap_tahap_server = $get_rap_tahap_server->{'data'};
          foreach ($data_rap_tahap_server as $r_rap_tahap_server) {
            $kode = $r_rap_tahap_server->{'tahap_kode_kendali'};
            $data_rap_tahap_local = $this->db->query("select * from simpro_rap_item_tree where kode_tree = '$kode' and id_proyek = $proyek_id");
            if ($data_rap_tahap_local->num_rows() == 0) {
              $client->query("delete from tbl_tahap_kendali where no_spk = '$no_spk' and tahap_kode_kendali = '$kode'");
              $client->query("delete from tbl_komposisi_kendali where no_spk = '$no_spk' and tahap_kode_kendali = '$kode'");
              $client->query("delete from tbl_induk_sd_kendali where no_spk = '$no_spk' and kode_tahap_kendali = '$kode'");
            }
          }

          $get_rap_komposisi_server = json_decode($client->query("select distinct(tahap_kode_kendali) from tbl_komposisi_kendali where no_spk = '$no_spk'"));
          $data_rap_komposisi_server = $get_rap_komposisi_server->{'data'};
          foreach ($data_rap_komposisi_server as $r_rap_komposisi_server) {
            $kode = $r_rap_komposisi_server->{'tahap_kode_kendali'};

            $get_kode_local = $this->db->query("select 
              (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
              c.detail_material_kode,
              a.kode_tree as tahap_kode_kendali,
              1 as komposisi_volume_kendali,
              c.harga as komposisi_harga_satuan_kendali,
              c.subtotal as komposisi_total_kendali,
              c.koefisien as komposisi_koefisien_kendali,
              a.volume * c.koefisien as komposisi_volume_total_kendali,
              'X' as kode_komposisi_kendali,
              c.keterangan as keterangan,
              c.kode_rap
              from 
              simpro_rap_item_tree a 
              join simpro_rap_analisa_item_apek b 
              on a.rap_item_tree = b.rap_item_tree
              join
              (
              SELECT tbl_analisa_satuan.* FROM (
              (
                SELECT          
                  simpro_rap_analisa_asat.kode_analisa,
                  simpro_tbl_detail_material.detail_material_kode, 
                  simpro_tbl_detail_material.detail_material_nama, 
                  simpro_tbl_detail_material.detail_material_satuan,
                  simpro_rap_analisa_asat.harga,
                  simpro_rap_analisa_asat.koefisien,
                  (simpro_rap_analisa_asat.harga * simpro_rap_analisa_asat.koefisien) AS subtotal,
                  simpro_rap_analisa_asat.kode_rap,
                  simpro_rap_analisa_asat.keterangan
                FROM 
                  simpro_rap_analisa_asat
                LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
                LEFT JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_asat.kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_asat.id_proyek)
                LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
                WHERE simpro_rap_analisa_asat.id_proyek= $proyek_id
                ORDER BY 
                  simpro_rap_analisa_asat.kode_analisa,
                  simpro_tbl_detail_material.detail_material_kode        
                ASC
              )
              UNION ALL 
              (
                select 
                a.parent_kode_analisa as kode_analisa,
                b.kode_material,
                c.detail_material_nama,
                c.detail_material_satuan,
                coalesce(b.harga,0) as harga,
                sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                b.kode_rap,
                b.keterangan
                from simpro_rap_analisa_apek a
                join simpro_rap_analisa_asat b
                on a.id_data_analisa = b.id_data_analisa
                join simpro_tbl_detail_material c
                on b.kode_material = c.detail_material_kode
                where a.id_proyek = $proyek_id
                group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
              )   
              ) AS tbl_analisa_satuan
              left join simpro_tbl_subbidang
              on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
              ) c
              on c.kode_analisa = b.kode_analisa
              where a.id_proyek = $proyek_id and a.kode_tree = '$kode'
              order by a.kode_tree");
            
            if ($get_kode_local->num_rows() == 0) {
              $client->query("delete from tbl_komposisi_kendali where no_spk = '$no_spk' and tahap_kode_kendali = '$kode'");
              $client->query("delete from tbl_induk_sd_kendali where no_spk = '$no_spk' and kode_tahap_kendali = '$kode'");
            } else {
              $get_rap_material_server = json_decode($client->query("select * from tbl_komposisi_kendali where no_spk = '$no_spk' and tahap_kode_kendali = '$kode'"));
              $data_rap_material = $get_rap_material_server->{'data'};
              if ($data_rap_material != '') {
                foreach ($data_rap_material as $r_rap_material) {
                  $kode_material = $r_rap_material->{'detail_material_kode'};

                  $data_rap_komposisi_local = $this->db->query("select 
                    (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                    c.detail_material_kode,
                    a.kode_tree as tahap_kode_kendali,
                    1 as komposisi_volume_kendali,
                    c.harga as komposisi_harga_satuan_kendali,
                    c.subtotal as komposisi_total_kendali,
                    c.koefisien as komposisi_koefisien_kendali,
                    a.volume * c.koefisien as komposisi_volume_total_kendali,
                    'X' as kode_komposisi_kendali,
                    c.keterangan as keterangan,
                    c.kode_rap
                    from 
                    simpro_rap_item_tree a 
                    join simpro_rap_analisa_item_apek b 
                    on a.rap_item_tree = b.rap_item_tree
                    join
                    (
                    SELECT tbl_analisa_satuan.* FROM (
                    (
                      SELECT          
                        simpro_rap_analisa_asat.kode_analisa,
                        simpro_tbl_detail_material.detail_material_kode, 
                        simpro_tbl_detail_material.detail_material_nama, 
                        simpro_tbl_detail_material.detail_material_satuan,
                        simpro_rap_analisa_asat.harga,
                        simpro_rap_analisa_asat.koefisien,
                        (simpro_rap_analisa_asat.harga * simpro_rap_analisa_asat.koefisien) AS subtotal,
                        simpro_rap_analisa_asat.kode_rap,
                        simpro_rap_analisa_asat.keterangan
                      FROM 
                        simpro_rap_analisa_asat
                      LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_rap_analisa_asat.kode_material
                      LEFT JOIN simpro_rap_analisa_daftar ON (simpro_rap_analisa_daftar.kode_analisa = simpro_rap_analisa_asat.kode_analisa AND simpro_rap_analisa_daftar.id_proyek= simpro_rap_analisa_asat.id_proyek)
                      LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_rap_analisa_daftar.id_satuan
                      WHERE simpro_rap_analisa_asat.id_proyek= $proyek_id
                      ORDER BY 
                        simpro_rap_analisa_asat.kode_analisa,
                        simpro_tbl_detail_material.detail_material_kode        
                      ASC
                    )
                    UNION ALL 
                    (
                      select 
                      a.parent_kode_analisa as kode_analisa,
                      b.kode_material,
                      c.detail_material_nama,
                      c.detail_material_satuan,
                      coalesce(b.harga,0) as harga,
                      sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                      sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                      b.kode_rap,
                      b.keterangan
                      from simpro_rap_analisa_apek a
                      join simpro_rap_analisa_asat b
                      on a.id_data_analisa = b.id_data_analisa
                      join simpro_tbl_detail_material c
                      on b.kode_material = c.detail_material_kode
                      where a.id_proyek = $proyek_id
                      group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                    )   
                    ) AS tbl_analisa_satuan
                    left join simpro_tbl_subbidang
                    on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                    ) c
                    on c.kode_analisa = b.kode_analisa
                    where a.id_proyek = $proyek_id and a.kode_tree = '$kode' and c.detail_material_kode = '$kode_material'
                    order by a.kode_tree");

                  if ($data_rap_komposisi_local->num_rows() == 0) {
                    $client->query("delete from tbl_komposisi_kendali where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and detail_material_kode = '$kode_material'");
                  }
                }                
              }              
            }
          }
      }

      if ($arg == 'kontrak_terkini_cek') {
        $get_decode = json_decode($client->query("select * from tbl_kontrak_terkini where no_spk = '$no_spk'"));
        $data = $get_decode->{'data'};
        if ($data != '') {          
          for ($i=0; $i < count($data); $i++) { 
            $tgl_rab = $data[$i]->{'tahap_tanggal_kendali'};
            $kode = $data[$i]->{'tahap_kode_kendali'};
            $get_kk_local = $this->db->query("select * from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id and tahap_tanggal_kendali <= '$tgl_rab' and tgl_akhir >= '$tgl_rab'");
            if ($get_kk_local->num_rows() == 0) {
              $client->query("delete from tbl_kontrak_terkini where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab' and tahap_kode_kendali = '$kode'");
            }
          }
          // foreach ($data as $kdata) {
          //   $ttk = $kdata->{'tahap_tanggal_kendali'};
          //   $get_cek_tanggal = $this->db->query("select * from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id and tahap_tanggal_kendali <= '$ttk' and tgl_akhir >= '$ttk'");
          //   if ($get_cek_tanggal->num_rows() == 0) {
          //     $client->query("delete from tbl_kontrak_terkini where no_spk = '$no_spk' and tahap_tanggal_kendali = '$ttk'");
          //   } else {
          //     $get_decode_data = json_decode($client->query("select * from tbl_kontrak_terkini where no_spk = '$no_spk' and tahap_tanggal_kendali = '$ttk' order by tahap_kode_kendali"));
          //     $tda = $get_decode_data->{'data'};
          //     if ($tda != '') {
          //       foreach ($tda as $kdata_kode) {        
          //         $ttk_tgl = $kdata_kode->{'tahap_tanggal_kendali'};       
          //         $ttk_kode = $kdata_kode->{'tahap_kode_kendali'};      
          //         $ttk_nospk = $kdata_kode->{'tahap_kode_kendali'};
          //         $get_cek_data_kode = $this->db->query("select * from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id and tahap_tanggal_kendali <= '$ttk' and tgl_akhir >= '$ttk' and tahap_kode_kendali = '$ttk_kode'");
          //         if ($get_cek_data_kode->num_rows == 0) {
          //           $client->query("delete from tbl_kontrak_terkini where no_spk = '$no_spk' and tahap_tanggal_kendali = '$ttk' and tahap_kode_kendali = '$ttk_kode'");
          //         }
          //       }
          //     }
          //   }
          // }
        }
      }

      if ($arg == 'kontrak_terkini') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_tgl_rab_kk = "select min(tahap_tanggal_kendali) as tahap_tanggal_kendali, max(tgl_akhir) as tgl_akhir from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id"; //limit 1
        // echo $sql_get_tgl_rab_kk;
        $q_get_tgl_rab_kk = $this->db->query($sql_get_tgl_rab_kk);
        if ($q_get_tgl_rab_kk->result() && ($q_get_tgl_rab_kk->row()->tahap_tanggal_kendali != '' || $q_get_tgl_rab_kk->row()->tahap_tanggal_kendali != null)) {
          foreach ($q_get_tgl_rab_kk->result() as $r_rab_kk) {
            $startdate=$r_rab_kk->tahap_tanggal_kendali;
            $enddate=$r_rab_kk->tgl_akhir;
            $timestamp=  strtotime($startdate);

            if ($enddate == $startdate) {
              $tgl_rab = $startdate;
              $tgl_akk = date('Y-m-d',strtotime('+1 month', strtotime($tgl_rab)));

              $sql_get_kk = "with j as (SELECT 
                  a.tahap_kode_induk_kendali,
                  a.tahap_kode_kendali,
                  CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END,
                  CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END,
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) * 
                  (CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END)
                  ) as jml_kontrak_kini,
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) +
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) -
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang 
                  END)
                  ) as vol_total,
                  (
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) +
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) -
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang 
                  END)
                  ) * 
                  (CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END)
                  ) as jml_total
                  FROM
                  simpro_tbl_kontrak_terkini a
                  where a.proyek_id = $proyek_id and a.tgl_akhir = '$r_rab_kk->tgl_akhir'
                  ORDER BY a.tahap_kode_kendali asc)

                  SELECT 
                  a.tahap_kode_kendali,
                  trim(a.tahap_nama_kendali) as tahap_nama_kendali,
                  (select satuan_nama from simpro_tbl_satuan where satuan_id = a.tahap_satuan_kendali) as tahap_satuan_kendali,
                  (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as no_spk,
                  CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END,
                  case when a.tahap_volume_kendali = 0 then
                  0
                  else
                  (
                  (
                  select
                  sum(jml_kontrak_kini)
                  from
                  j
                  where
                  left(j.tahap_kode_kendali,length(a.tahap_kode_kendali)) = a.tahap_kode_kendali
                  group by left(j.tahap_kode_kendali,length(a.tahap_kode_kendali))
                  )/
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END)
                  )
                  end as tahap_harga_satuan_kendali,
                  (
                  select
                  sum(jml_kontrak_kini)
                  from
                  j
                  where
                  left(j.tahap_kode_kendali,length(a.tahap_kode_kendali)) = a.tahap_kode_kendali
                  group by left(j.tahap_kode_kendali,length(a.tahap_kode_kendali))
                  ) as tahap_total_kendali,
                  '$tgl_rab' as tahap_tanggal_kendali,
                  a.tahap_kode_induk_kendali,
                  CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END,
                  (
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) *
                  ((CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END))
                  ) as tahap_total_kendali_new,
                  CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang
                  END,
                  (
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang
                  END) *
                  (CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END)
                  ) as tahap_total_kendali_kurang,
                  CASE WHEN a.volume_eskalasi is null
                  THEN 0
                  ELSE a.volume_eskalasi
                  END,
                  CASE WHEN a.harga_satuan_eskalasi is null
                  THEN 0
                  ELSE a.harga_satuan_eskalasi
                  END,
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) +
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) -
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang 
                  END)
                  ) as vol_tambah_kurang,
                  (
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) +
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) -
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang 
                  END)
                  ) * 
                  (CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END)
                  ) as total_tambah_kurang,
                  '$tgl_akk' as tgl_rencana_aak
                  FROM
                  simpro_tbl_kontrak_terkini a
                  where a.proyek_id = $proyek_id and a.tgl_akhir = '$r_rab_kk->tgl_akhir'";

                $q_get_kk = $this->db->query($sql_get_kk);
                if ($q_get_kk->result()) {
                  foreach ($q_get_kk->result() as $r_kk) {
                    $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_kontrak_terkini where tahap_kode_kendali = '$r_kk->tahap_kode_kendali' and no_spk = '$r_kk->no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
                    $data = $get_decode->{'data'};
                    $count = $data[0]->{'count'};
                    if ($count == 0) {
                      $sql_insert_kk = "insert into tbl_kontrak_terkini (
                        tahap_kode_kendali,
                        tahap_nama_kendali,
                        tahap_satuan_kendali,
                        no_spk,
                        tahap_volume_kendali,
                        tahap_harga_satuan_kendali,
                        tahap_total_kendali,
                        tahap_kode_induk_kendali,
                        tahap_tanggal_kendali,
                        tahap_volume_kendali_new,
                        tahap_total_kendali_new,
                        tahap_volume_kendali_kurang,
                        tahap_total_kendali_kurang,
                        volume_eskalasi,
                        harga_satuan_eskalasi,
                        vol_tambah_kurang,
                        total_tambah_kurang,
                        tgl_rencana_aak,
                        user_update,
                        tgl_update,
                        ip_update,
                        divisi_update,
                        waktu_update
                      ) values (
                        '$r_kk->tahap_kode_kendali',
                        '$r_kk->tahap_nama_kendali',
                        '$r_kk->tahap_satuan_kendali',
                        '$r_kk->no_spk',
                        $r_kk->tahap_volume_kendali,
                        $r_kk->tahap_harga_satuan_kendali,
                        $r_kk->tahap_total_kendali,
                        '$r_kk->tahap_kode_induk_kendali',
                        '$r_kk->tahap_tanggal_kendali',
                        $r_kk->tahap_volume_kendali_new,
                        $r_kk->tahap_total_kendali_new,
                        $r_kk->tahap_volume_kendali_kurang,
                        $r_kk->tahap_total_kendali_kurang,
                        $r_kk->volume_eskalasi,
                        $r_kk->harga_satuan_eskalasi,
                        $r_kk->vol_tambah_kurang,
                        $r_kk->total_tambah_kurang,
                        '$r_kk->tgl_rencana_aak',
                        '$uname_update',
                        '$tgl_update',
                        '$ip_update',
                        '$divisi_update',
                        '$waktu_update'
                      )";

                      $client->query($sql_insert_kk);
                    } else {
                      $sql_update_kk = "update tbl_kontrak_terkini set
                        tahap_nama_kendali = '$r_kk->tahap_nama_kendali',
                        tahap_satuan_kendali = '$r_kk->tahap_satuan_kendali',
                        tahap_volume_kendali = $r_kk->tahap_volume_kendali,
                        tahap_harga_satuan_kendali = $r_kk->tahap_harga_satuan_kendali,
                        tahap_total_kendali  = $r_kk->tahap_total_kendali,
                        tahap_kode_induk_kendali = '$r_kk->tahap_kode_induk_kendali',
                        tahap_volume_kendali_new = $r_kk->tahap_volume_kendali_new,
                        tahap_total_kendali_new  = $r_kk->tahap_total_kendali_new,
                        tahap_volume_kendali_kurang  = $r_kk->tahap_volume_kendali_kurang,
                        tahap_total_kendali_kurang = $r_kk->tahap_total_kendali_kurang,
                        volume_eskalasi  = $r_kk->volume_eskalasi,
                        harga_satuan_eskalasi  = $r_kk->harga_satuan_eskalasi,
                        vol_tambah_kurang  = $r_kk->vol_tambah_kurang,
                        total_tambah_kurang  = $r_kk->total_tambah_kurang,
                        tgl_rencana_aak  = '$r_kk->tgl_rencana_aak',
                        user_update  = '$uname_update',
                        tgl_update = '$tgl_update',
                        ip_update  = '$ip_update',
                        divisi_update  = '$divisi_update',
                        waktu_update  = '$waktu_update'
                        where 
                        tahap_kode_kendali = '$r_kk->tahap_kode_kendali' and
                        no_spk = '$r_kk->no_spk' and
                        tahap_tanggal_kendali  = '$r_kk->tahap_tanggal_kendali'
                      ";

                      // echo $sql_update_kk;
                      $client->query($sql_update_kk);
                    }
                  }
                }
            } else {
              while ($startdate < $enddate)
              { 
                $startdate = date('Y-m-d', $timestamp);
                $tgl_rab = $startdate;
                $tgl_akk = date('Y-m-d',strtotime('+1 month', strtotime($tgl_rab)));
                $timestamp = strtotime('+1 month', strtotime($startdate));

                // echo $tgl_rab."+".$tgl_akk."<br>";

                $sql_get_kk = "with j as (SELECT 
                  a.tahap_kode_induk_kendali,
                  a.tahap_kode_kendali,
                  CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END,
                  CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END,
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) * 
                  (CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END)
                  ) as jml_kontrak_kini,
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) +
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) -
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang 
                  END)
                  ) as vol_total,
                  (
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) +
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) -
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang 
                  END)
                  ) * 
                  (CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END)
                  ) as jml_total
                  FROM
                  simpro_tbl_kontrak_terkini a
                  where a.proyek_id = $proyek_id and a.tgl_akhir = '$r_rab_kk->tgl_akhir'
                  ORDER BY a.tahap_kode_kendali asc)

                  SELECT 
                  a.tahap_kode_kendali,
                  trim(a.tahap_nama_kendali) as tahap_nama_kendali,
                  (select satuan_nama from simpro_tbl_satuan where satuan_id = a.tahap_satuan_kendali) as tahap_satuan_kendali,
                  (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as no_spk,
                  CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END,
                  case when a.tahap_volume_kendali = 0 then
                  0
                  else
                  (
                  (
                  select
                  sum(jml_kontrak_kini)
                  from
                  j
                  where
                  left(j.tahap_kode_kendali,length(a.tahap_kode_kendali)) = a.tahap_kode_kendali
                  group by left(j.tahap_kode_kendali,length(a.tahap_kode_kendali))
                  )/
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END)
                  )
                  end as tahap_harga_satuan_kendali,
                  (
                  select
                  sum(jml_kontrak_kini)
                  from
                  j
                  where
                  left(j.tahap_kode_kendali,length(a.tahap_kode_kendali)) = a.tahap_kode_kendali
                  group by left(j.tahap_kode_kendali,length(a.tahap_kode_kendali))
                  ) as tahap_total_kendali,
                  '$tgl_rab' as tahap_tanggal_kendali,
                  a.tahap_kode_induk_kendali,
                  CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END,
                  (
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) *
                  ((CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END))
                  ) as tahap_total_kendali_new,
                  CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang
                  END,
                  (
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang
                  END) *
                  (CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END)
                  ) as tahap_total_kendali_kurang,
                  CASE WHEN a.volume_eskalasi is null
                  THEN 0
                  ELSE a.volume_eskalasi
                  END,
                  CASE WHEN a.harga_satuan_eskalasi is null
                  THEN 0
                  ELSE a.harga_satuan_eskalasi
                  END,
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) +
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) -
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang 
                  END)
                  ) as vol_tambah_kurang,
                  (
                  (
                  (CASE WHEN a.tahap_volume_kendali is null
                  THEN 0
                  ELSE a.tahap_volume_kendali
                  END) +
                  (CASE WHEN a.tahap_volume_kendali_new is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_new
                  END) -
                  (CASE WHEN a.tahap_volume_kendali_kurang is null
                  THEN 0
                  ELSE a.tahap_volume_kendali_kurang 
                  END)
                  ) * 
                  (CASE WHEN a.tahap_harga_satuan_kendali is null
                  THEN 0
                  ELSE a.tahap_harga_satuan_kendali
                  END)
                  ) as total_tambah_kurang,
                  '$tgl_akk' as tgl_rencana_aak
                  FROM
                  simpro_tbl_kontrak_terkini a
                  where a.proyek_id = $proyek_id and a.tgl_akhir = '$r_rab_kk->tgl_akhir'";

                $q_get_kk = $this->db->query($sql_get_kk);
                if ($q_get_kk->result()) {
                  foreach ($q_get_kk->result() as $r_kk) {
                    $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_kontrak_terkini where tahap_kode_kendali = '$r_kk->tahap_kode_kendali' and no_spk = '$r_kk->no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
                    $data = $get_decode->{'data'};
                    $count = $data[0]->{'count'};
                    if ($count == 0) {
                      $sql_insert_kk = "insert into tbl_kontrak_terkini (
                        tahap_kode_kendali,
                        tahap_nama_kendali,
                        tahap_satuan_kendali,
                        no_spk,
                        tahap_volume_kendali,
                        tahap_harga_satuan_kendali,
                        tahap_total_kendali,
                        tahap_kode_induk_kendali,
                        tahap_tanggal_kendali,
                        tahap_volume_kendali_new,
                        tahap_total_kendali_new,
                        tahap_volume_kendali_kurang,
                        tahap_total_kendali_kurang,
                        volume_eskalasi,
                        harga_satuan_eskalasi,
                        vol_tambah_kurang,
                        total_tambah_kurang,
                        tgl_rencana_aak,
                        user_update,
                        tgl_update,
                        ip_update,
                        divisi_update,
                        waktu_update
                      ) values (
                        '$r_kk->tahap_kode_kendali',
                        '$r_kk->tahap_nama_kendali',
                        '$r_kk->tahap_satuan_kendali',
                        '$r_kk->no_spk',
                        $r_kk->tahap_volume_kendali,
                        $r_kk->tahap_harga_satuan_kendali,
                        $r_kk->tahap_total_kendali,
                        '$r_kk->tahap_kode_induk_kendali',
                        '$r_kk->tahap_tanggal_kendali',
                        $r_kk->tahap_volume_kendali_new,
                        $r_kk->tahap_total_kendali_new,
                        $r_kk->tahap_volume_kendali_kurang,
                        $r_kk->tahap_total_kendali_kurang,
                        $r_kk->volume_eskalasi,
                        $r_kk->harga_satuan_eskalasi,
                        $r_kk->vol_tambah_kurang,
                        $r_kk->total_tambah_kurang,
                        '$r_kk->tgl_rencana_aak',
                        '$uname_update',
                        '$tgl_update',
                        '$ip_update',
                        '$divisi_update',
                        '$waktu_update'
                      )";

                      $client->query($sql_insert_kk);
                    } else {
                      $sql_update_kk = "update tbl_kontrak_terkini set
                        tahap_nama_kendali = '$r_kk->tahap_nama_kendali',
                        tahap_satuan_kendali = '$r_kk->tahap_satuan_kendali',
                        tahap_volume_kendali = $r_kk->tahap_volume_kendali,
                        tahap_harga_satuan_kendali = $r_kk->tahap_harga_satuan_kendali,
                        tahap_total_kendali  = $r_kk->tahap_total_kendali,
                        tahap_kode_induk_kendali = '$r_kk->tahap_kode_induk_kendali',
                        tahap_volume_kendali_new = $r_kk->tahap_volume_kendali_new,
                        tahap_total_kendali_new  = $r_kk->tahap_total_kendali_new,
                        tahap_volume_kendali_kurang  = $r_kk->tahap_volume_kendali_kurang,
                        tahap_total_kendali_kurang = $r_kk->tahap_total_kendali_kurang,
                        volume_eskalasi  = $r_kk->volume_eskalasi,
                        harga_satuan_eskalasi  = $r_kk->harga_satuan_eskalasi,
                        vol_tambah_kurang  = $r_kk->vol_tambah_kurang,
                        total_tambah_kurang  = $r_kk->total_tambah_kurang,
                        tgl_rencana_aak  = '$r_kk->tgl_rencana_aak',
                        user_update  = '$uname_update',
                        tgl_update = '$tgl_update',
                        ip_update  = '$ip_update',
                        divisi_update  = '$divisi_update',
                        waktu_update  = '$waktu_update'
                        where 
                        tahap_kode_kendali = '$r_kk->tahap_kode_kendali' and
                        no_spk = '$r_kk->no_spk' and
                        tahap_tanggal_kendali  = '$r_kk->tahap_tanggal_kendali'
                      ";

                      // echo $sql_update_kk;
                      $client->query($sql_update_kk);
                    }
                  }
                }
              }
            }              
          }
        }
        
        $this->sync('kontrak_terkini_cek');
      }

      if ($arg == 'lpf') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_tgl_lpf = "select distinct(tahap_tanggal_kendali) from simpro_tbl_total_pekerjaan where proyek_id = $proyek_id";
        $q_tgl_lpf = $this->db->query($sql_get_tgl_lpf);
        if ($q_tgl_lpf->result()) {
          foreach ($q_tgl_lpf->result() as $r_tgl_lpf) {
            $tgl_rab = $r_tgl_lpf->tahap_tanggal_kendali;
            $sql_get_lpf = "with j as (SELECT
              b.tahap_kode_kendali,
              (
              (CASE WHEN b.tahap_volume_kendali is null
              THEN 0
              ELSE b.tahap_volume_kendali
              END) +
              (CASE WHEN b.tahap_volume_kendali_new is null
              THEN 0
              ELSE b.tahap_volume_kendali_new
              END) -
              (CASE WHEN b.tahap_volume_kendali_kurang is null
              THEN 0
              ELSE b.tahap_volume_kendali_kurang
              END)
              ) as vol_kk,
              CASE WHEN b.tahap_harga_satuan_kendali is null
              THEN 0
              ELSE b.tahap_harga_satuan_kendali
              END,
              (
              ((CASE WHEN b.tahap_volume_kendali is null
              THEN 0
              ELSE b.tahap_volume_kendali
              END) +
              (CASE WHEN b.tahap_volume_kendali_new is null
              THEN 0
              ELSE b.tahap_volume_kendali_new
              END) -
              (CASE WHEN b.tahap_volume_kendali_kurang is null
              THEN 0
              ELSE b.tahap_volume_kendali_kurang
              END)) *
              (CASE WHEN b.tahap_harga_satuan_kendali is null
              THEN 0
              ELSE b.tahap_harga_satuan_kendali
              END)
              ) as jml_lpf_kini
              FROM
              simpro_tbl_total_pekerjaan a 
              JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
              WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab' 
              ORDER BY b.tahap_kode_kendali)
              
              SELECT
              b.tahap_kode_kendali,
              b.tahap_nama_kendali,
              (select satuan_nama from simpro_tbl_satuan where satuan_id = b.tahap_satuan_kendali) as tahap_satuan_kendali,
              (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as no_spk,
              (
              (CASE WHEN b.tahap_volume_kendali is null
              THEN 0
              ELSE b.tahap_volume_kendali
              END) +
              (CASE WHEN b.tahap_volume_kendali_new is null
              THEN 0
              ELSE b.tahap_volume_kendali_new
              END) -
              (CASE WHEN b.tahap_volume_kendali_kurang is null
              THEN 0
              ELSE b.tahap_volume_kendali_kurang
              END)
              ) as tahap_volume_kendali,
              case when ((CASE WHEN b.tahap_volume_kendali is null
              THEN 0
              ELSE b.tahap_volume_kendali
              END) +
              (CASE WHEN b.tahap_volume_kendali_new is null
              THEN 0
              ELSE b.tahap_volume_kendali_new
              END) -
              (CASE WHEN b.tahap_volume_kendali_kurang is null
              THEN 0
              ELSE b.tahap_volume_kendali_kurang
              END)) = 0 then
              0
              else
              (
              (select
              sum(jml_lpf_kini)
              from
              j
              where
              left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
              group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))) /
              ((CASE WHEN b.tahap_volume_kendali is null
              THEN 0
              ELSE b.tahap_volume_kendali
              END) +
              (CASE WHEN b.tahap_volume_kendali_new is null
              THEN 0
              ELSE b.tahap_volume_kendali_new
              END) -
              (CASE WHEN b.tahap_volume_kendali_kurang is null
              THEN 0
              ELSE b.tahap_volume_kendali_kurang
              END))
              ) end as tahap_harga_satuan_kendali,        
              (
              select
              sum(jml_lpf_kini)
              from
              j
              where
              left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
              group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))
              ) as tahap_total_kendali,
              b.tahap_kode_induk_kendali,
              a.tahap_tanggal_kendali,
              a.tahap_diakui_bobot,
              a.tahap_diakui_bobot * b.tahap_harga_satuan_kendali as tahap_diakui_jumlah,       
              CASE WHEN a.tagihan_cair is null
              THEN 0
              ELSE a.tagihan_cair
              END,
              CASE WHEN a.vol_total_tagihan is null
              THEN 0
              ELSE a.vol_total_tagihan
              END,
              CASE WHEN a.tagihan_rencana_piutang is null
              THEN 0
              ELSE a.tagihan_rencana_piutang
              END,
              0 as tahap_volume_kendali_new,
              0 as tahap_total_kendali_new,
              0 as tahap_harga_satuan_kendali_new
              FROM
              simpro_tbl_total_pekerjaan a 
              JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
              WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab'";
            $q_lpf = $this->db->query($sql_get_lpf);
            if ($q_lpf->result()) {
              foreach ($q_lpf->result() as $r_data_lpf) {
                $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_total_pekerjaan where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab' and tahap_kode_kendali = '$r_data_lpf->tahap_kode_kendali'"));
                $data = $get_decode->{'data'};
                $count = $data[0]->{'count'};
                if ($count == 0) {
                  $sql_insert_lpf = "insert into tbl_total_pekerjaan (
                    tahap_kode_kendali,
                    tahap_nama_kendali,
                    tahap_satuan_kendali,
                    no_spk,
                    tahap_volume_kendali,
                    tahap_harga_satuan_kendali,
                    tahap_total_kendali,
                    tahap_kode_induk_kendali,
                    tahap_tanggal_kendali,
                    tahap_diakui_bobot,
                    tahap_diakui_jumlah,
                    tagihan_cair,
                    vol_total_tagihan,
                    tagihan_rencana_piutang,
                    tahap_volume_kendali_new,
                    tahap_total_kendali_new,
                    tahap_harga_satuan_kendali_new,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$r_data_lpf->tahap_kode_kendali',
                    '$r_data_lpf->tahap_nama_kendali',
                    '$r_data_lpf->tahap_satuan_kendali',
                    '$r_data_lpf->no_spk',
                    $r_data_lpf->tahap_volume_kendali,
                    $r_data_lpf->tahap_harga_satuan_kendali,
                    $r_data_lpf->tahap_total_kendali,
                    '$r_data_lpf->tahap_kode_induk_kendali',
                    '$r_data_lpf->tahap_tanggal_kendali',
                    $r_data_lpf->tahap_diakui_bobot,
                    $r_data_lpf->tahap_diakui_jumlah,
                    $r_data_lpf->tagihan_cair,
                    $r_data_lpf->vol_total_tagihan,
                    $r_data_lpf->tagihan_rencana_piutang,
                    $r_data_lpf->tahap_volume_kendali_new,
                    $r_data_lpf->tahap_total_kendali_new,
                    $r_data_lpf->tahap_harga_satuan_kendali_new,
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )";

                  $client->query($sql_insert_lpf);

                } else {
                  $sql_update_lpf = "update tbl_total_pekerjaan set
                    tahap_nama_kendali = '$r_data_lpf->tahap_nama_kendali',
                    tahap_satuan_kendali = '$r_data_lpf->tahap_satuan_kendali',
                    tahap_volume_kendali = $r_data_lpf->tahap_volume_kendali,
                    tahap_harga_satuan_kendali = $r_data_lpf->tahap_harga_satuan_kendali,
                    tahap_total_kendali  = $r_data_lpf->tahap_total_kendali,
                    tahap_kode_induk_kendali = '$r_data_lpf->tahap_kode_induk_kendali',
                    tahap_diakui_bobot = $r_data_lpf->tahap_diakui_bobot,
                    tahap_diakui_jumlah  = $r_data_lpf->tahap_diakui_jumlah,
                    tagihan_cair = $r_data_lpf->tagihan_cair,
                    vol_total_tagihan  = $r_data_lpf->vol_total_tagihan,
                    tagihan_rencana_piutang  = $r_data_lpf->tagihan_rencana_piutang,
                    tahap_volume_kendali_new = $r_data_lpf->tahap_volume_kendali_new,
                    tahap_total_kendali_new  = $r_data_lpf->tahap_total_kendali_new,
                    tahap_harga_satuan_kendali_new = $r_data_lpf->tahap_harga_satuan_kendali_new,
                    user_update  = '$uname_update',
                    tgl_update = '$tgl_update',
                    ip_update  = '$ip_update',
                    divisi_update  = '$divisi_update',
                    waktu_update  = '$waktu_update'
                    where
                    no_spk = '$r_data_lpf->no_spk' and 
                    tahap_tanggal_kendali  = '$r_data_lpf->tahap_tanggal_kendali' and 
                    tahap_kode_kendali = '$r_data_lpf->tahap_kode_kendali'
                  ";

                  $client->query($sql_update_lpf);
                }
              }
            }
          }
        }

        $server_del_tgl_lpf = json_decode($client->query("select * from tbl_total_pekerjaan where no_spk = '$no_spk'"));
        $data_server_tgl_lpf = $server_del_tgl_lpf->{'data'};
        if ($data_server_tgl_lpf != '') {   
          for ($i=0; $i < count($data_server_tgl_lpf); $i++) { 
            $tgl_rab = $data_server_tgl_lpf[$i]->{'tahap_tanggal_kendali'};
            $kode = $data_server_tgl_lpf[$i]->{'tahap_kode_kendali'};
            $get_lpf_local = $this->db->query("select * from simpro_tbl_total_pekerjaan a join simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini where a.proyek_id = $proyek_id and a.tahap_tanggal_kendali = '$tgl_rab' and b.tahap_kode_kendali = '$kode'");
            if ($get_lpf_local->num_rows() == 0) {
              $client->query("delete from tbl_total_pekerjaan where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab' and tahap_kode_kendali = '$kode'");
            }
          }
          // foreach ($data_server_tgl_lpf as $server_tgl) {   
          //   $tgl_rab = $server_tgl->{'tahap_tanggal_kendali'};
          //   $data_tgl_local = $this->db->query("select * from simpro_tbl_total_pekerjaan where proyek_id = $proyek_id and tahap_tanggal_kendali = '$tgl_rab'");
          //   if ($data_tgl_local->num_rows() == 0) {
          //     $client->query("delete from tbl_total_pekerjaan where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab'");
          //   } else {
          //     $q_lpf_server = json_decode($client->query("select * from tbl_total_pekerjaan where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab' order by tahap_kode_kendali"));
          //     $data_lpf_server = $q_lpf_server->{'data'};
          //     if ($data_lpf_server != '') {
          //       foreach ($data_lpf_server as $server_data) {
          //         $kode = $server_data->{'tahap_kode_kendali'};
          //         $data_lpf_local = $this->db->query("select * from simpro_tbl_total_pekerjaan a join simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini where a.proyek_id = $proyek_id and a.tahap_tanggal_kendali = '$tgl_rab' and b.tahap_kode_kendali = '$kode'");
          //         if ($data_lpf_local->num_rows() == 0) {
          //           $client->query("delete from tbl_total_pekerjaan where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab' and tahap_kode_kendali='$kode'");
          //         }
          //       }
          //     }
          //   }
          // } 
        }
      }

      if ($arg == 'costogo') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_tgl_ctg = "select distinct(tanggal_kendali) from simpro_costogo_item_tree where id_proyek = $proyek_id";
        $q_tgl_ctg = $this->db->query($sql_tgl_ctg);
        if ($q_tgl_ctg->result()) {
          foreach ($q_tgl_ctg->result() as $r_tgl_ctg) {
            $tgl_rab = $r_tgl_ctg->tanggal_kendali;

            $sql_get_tahap_kendali = "select 
              x.kode_tree as tahap_kode_kendali,
              x.tree_item as tahap_nama_kendali,
              x.tree_satuan as tahap_satuan_kendali,
              ''::text as tahap_keterangan_kendali,
              (select no_spk from simpro_tbl_proyek where proyek_id = x.id_proyek) as no_spk, 
              x.volume as tahap_volume_kendali, 
              case when sum(totals.subtotal) = 0 or x.volume = 0 then
              0
              else
              sum(totals.subtotal) / x.volume
              end as tahap_harga_satuan_kendali,
              (
              sum(totals.subtotal)
              ) as tahap_total_kendali,
              x.tree_parent_kode as tahap_kode_induk_kendali,
              tanggal_kendali as tahap_tanggal_kendali
              from
              simpro_costogo_item_tree x
              left join
              (SELECT 
                simpro_costogo_item_tree.kode_tree,
                simpro_costogo_analisa_item_apek.kode_analisa,
                COALESCE(tbl_harga.harga, 0) AS harga,
                (COALESCE(tbl_harga.harga, 0) * simpro_costogo_item_tree.volume) as subtotal
              FROM simpro_costogo_item_tree 
              LEFT JOIN simpro_costogo_analisa_item_apek ON simpro_costogo_analisa_item_apek.kode_tree = simpro_costogo_item_tree.kode_tree and simpro_costogo_analisa_item_apek.id_proyek = $proyek_id and simpro_costogo_analisa_item_apek.tanggal_kendali = '$tgl_rab'
              LEFT JOIN (
                SELECT 
                DISTINCT ON(kode_analisa)
                          kode_analisa,
                          SUM(subtotal) AS harga
                FROM (
                (
                  SELECT          
                    (simpro_costogo_analisa_asat.kode_analisa) AS kode_analisa, 
                    (simpro_costogo_analisa_asat.harga * simpro_costogo_analisa_asat.koefisien) AS subtotal
                  FROM 
                    simpro_costogo_analisa_asat
                  LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_costogo_analisa_asat.kode_material
                  LEFT JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_asat.kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_asat.id_proyek AND simpro_costogo_analisa_daftar.tanggal_kendali= simpro_costogo_analisa_asat.tanggal_kendali)
                  LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
                  WHERE simpro_costogo_analisa_asat.id_proyek= $proyek_id and simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
                  ORDER BY 
                    simpro_costogo_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode        
                  ASC
                )
                UNION ALL 
                (
                  SELECT 
                    (simpro_costogo_analisa_apek.parent_kode_analisa) AS kode_analisa, 
                    COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
                  FROM 
                    simpro_costogo_analisa_apek
                  INNER JOIN simpro_costogo_analisa_daftar ad ON (ad.kode_analisa = simpro_costogo_analisa_apek.kode_analisa AND ad.id_proyek = simpro_costogo_analisa_apek.id_proyek and ad.tanggal_kendali = simpro_costogo_analisa_apek.tanggal_kendali)
                  INNER JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_apek.parent_kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_apek.id_proyek AND simpro_costogo_analisa_daftar.tanggal_kendali= simpro_costogo_analisa_apek.tanggal_kendali)     
                  INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
                  LEFT JOIN (
                    SELECT 
                      DISTINCT ON(kode_analisa)
                      kode_analisa,
                      SUM(harga * koefisien) AS harga
                    FROM simpro_costogo_analisa_asat 
                    WHERE id_proyek= $proyek_id and tanggal_kendali = '$tgl_rab'
                    
                    GROUP BY kode_analisa     
                  ) as tbl_harga ON tbl_harga.kode_analisa = simpro_costogo_analisa_apek.kode_analisa     
                  WHERE simpro_costogo_analisa_apek.id_proyek= $proyek_id and simpro_costogo_analisa_apek.tanggal_kendali = '$tgl_rab'
                  
                  ORDER BY 
                    simpro_costogo_analisa_apek.parent_kode_analisa,        
                    simpro_costogo_analisa_apek.kode_analisa
                  ASC         
                )   
                ) AS tbl_analisa_satuan
                GROUP BY kode_analisa       
              ) as tbl_harga ON tbl_harga.kode_analisa = simpro_costogo_analisa_item_apek.kode_analisa            
              WHERE simpro_costogo_item_tree.id_proyek = $proyek_id and simpro_costogo_item_tree.tanggal_kendali = '$tgl_rab'
              ORDER BY simpro_costogo_item_tree.kode_tree ASC) as totals
              on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
              WHERE x.id_proyek = $proyek_id and x.tanggal_kendali = '$tgl_rab'
              group by x.costogo_item_tree      
              ORDER BY x.kode_tree";

            $q_get_tahap_kendali = $this->db->query($sql_get_tahap_kendali);

            if ($q_get_tahap_kendali->result()) {
              foreach ($q_get_tahap_kendali->result() as $row_costogo_tahap) {
                $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_cost_togo where tahap_kode_kendali = '$row_costogo_tahap->tahap_kode_kendali' and no_spk = '$row_costogo_tahap->no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
                $data = $get_decode->{'data'};
                $count = $data[0]->{'count'};
                if ($count == 0) {
                  $sql_insert_tahap_kendali = "
                    insert into tbl_cost_togo (
                      tahap_kode_kendali,
                      tahap_nama_kendali,
                      tahap_satuan_kendali,
                      tahap_keterangan_kendali,
                      no_spk,
                      tahap_volume_kendali,
                      tahap_harga_satuan_kendali,
                      tahap_total_kendali,
                      tahap_kode_induk_kendali,
                      tahap_tanggal_kendali,
                      user_update,
                      tgl_update,
                      ip_update,
                      divisi_update,
                      waktu_update
                    ) values (
                      '$row_costogo_tahap->tahap_kode_kendali',
                      '$row_costogo_tahap->tahap_nama_kendali',
                      '$row_costogo_tahap->tahap_satuan_kendali',
                      '$row_costogo_tahap->tahap_keterangan_kendali',
                      '$row_costogo_tahap->no_spk',
                      $row_costogo_tahap->tahap_volume_kendali,
                      $row_costogo_tahap->tahap_harga_satuan_kendali,
                      $row_costogo_tahap->tahap_total_kendali,
                      '$row_costogo_tahap->tahap_kode_induk_kendali',
                      '$row_costogo_tahap->tahap_tanggal_kendali',
                      '$uname_update',
                      '$tgl_update',
                      '$ip_update',
                      '$divisi_update',
                      '$waktu_update'
                    )
                    ";

                  $client->query($sql_insert_tahap_kendali);
                } else {
                  $sql_update_tahap_kendali = "
                    update tbl_cost_togo set
                    tahap_nama_kendali = '$row_costogo_tahap->tahap_nama_kendali',
                    tahap_satuan_kendali = '$row_costogo_tahap->tahap_satuan_kendali',
                    tahap_keterangan_kendali = '$row_costogo_tahap->tahap_keterangan_kendali',
                    tahap_volume_kendali = $row_costogo_tahap->tahap_volume_kendali,
                    tahap_harga_satuan_kendali = $row_costogo_tahap->tahap_harga_satuan_kendali,
                    tahap_total_kendali  = $row_costogo_tahap->tahap_total_kendali,
                    tahap_kode_induk_kendali = '$row_costogo_tahap->tahap_kode_induk_kendali',
                    user_update  = '$uname_update',
                    tgl_update = '$tgl_update',
                    ip_update  = '$ip_update',
                    divisi_update  = '$divisi_update',
                    waktu_update  = '$waktu_update'
                    where
                    no_spk = '$row_costogo_tahap->no_spk' and
                    tahap_kode_kendali = '$row_costogo_tahap->tahap_kode_kendali' and
                    tahap_tanggal_kendali  = '$row_costogo_tahap->tahap_tanggal_kendali'
                  ";

                  $client->query($sql_update_tahap_kendali);
                }
              }
            }

            $sql_get_induk_kendali = "select 
              'X' as kode_komposisi,
              1 as volume_komposisi,
              1 as koefisien_komposisi,
              (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
              a.kode_tree as kode_tahap_kendali,
              sum(c.subtotal) as total_harga_satuan,
              'ANALISA' as nama_komposisi,
              ''::text as komposisi_satuan,
              sum(c.harga) as harga_satuan,
              0 as no,
              a.tanggal_kendali as tahap_tanggal_kendali
              from 
              simpro_costogo_item_tree a 
              join simpro_costogo_analisa_item_apek b 
              on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
              join
              (
              SELECT tbl_analisa_satuan.* FROM (
              (
                SELECT          
                  simpro_costogo_analisa_asat.kode_analisa,
                  simpro_tbl_detail_material.detail_material_kode, 
                  simpro_tbl_detail_material.detail_material_nama, 
                  simpro_tbl_detail_material.detail_material_satuan,
                  simpro_costogo_analisa_asat.harga,
                  simpro_costogo_analisa_asat.koefisien,
                  (simpro_costogo_analisa_asat.harga * simpro_costogo_analisa_asat.koefisien) AS subtotal,
                  simpro_costogo_analisa_asat.kode_rap,
                  simpro_costogo_analisa_asat.keterangan
                FROM 
                  simpro_costogo_analisa_asat
                LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_costogo_analisa_asat.kode_material
                LEFT JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_asat.kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_asat.id_proyek AND simpro_costogo_analisa_daftar.tanggal_kendali= simpro_costogo_analisa_asat.tanggal_kendali)
                LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
                WHERE simpro_costogo_analisa_asat.id_proyek= $proyek_id and simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
                ORDER BY 
                  simpro_costogo_analisa_asat.kode_analisa,
                  simpro_tbl_detail_material.detail_material_kode        
                ASC
              )
              UNION ALL 
              (
                select 
                a.parent_kode_analisa as kode_analisa,
                b.kode_material,
                c.detail_material_nama,
                c.detail_material_satuan,
                coalesce(b.harga,0) as harga,
                sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                b.kode_rap,
                b.keterangan
                from simpro_costogo_analisa_apek a
                join simpro_costogo_analisa_asat b
                on a.id_data_analisa = b.id_data_analisa
                join simpro_tbl_detail_material c
                on b.kode_material = c.detail_material_kode
                where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
              )   
              ) AS tbl_analisa_satuan
              left join simpro_tbl_subbidang
              on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
              ) c
              on c.kode_analisa = b.kode_analisa
              where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
              group by a.id_proyek,a.kode_tree,
              tahap_tanggal_kendali";

            $q_get_induk_kendali = $this->db->query($sql_get_induk_kendali);

            if ($q_get_induk_kendali->result()) {
              foreach ($q_get_induk_kendali->result() as $row_costogo_induk) {
                $get_decode = json_decode($client->query("select count(kode_tahap_kendali) from tbl_induk_togo where kode_tahap_kendali = '$row_costogo_induk->kode_tahap_kendali' and no_spk = '$row_costogo_induk->no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
                $data = $get_decode->{'data'};
                $count = $data[0]->{'count'};
                if ($count == 0) {
                  $sql_insert_induk_kendali = "
                    insert into tbl_induk_togo (
                      kode_komposisi,
                      volume_komposisi,
                      koefisien_komposisi,
                      no_spk,
                      kode_tahap_kendali,
                      total_harga_satuan,
                      nama_komposisi,
                      komposisi_satuan,
                      harga_satuan,
                      tahap_tanggal_kendali
                    ) values (
                      '$row_costogo_induk->kode_komposisi',
                      $row_costogo_induk->volume_komposisi,
                      $row_costogo_induk->koefisien_komposisi,
                      '$row_costogo_induk->no_spk',
                      '$row_costogo_induk->kode_tahap_kendali',
                      $row_costogo_induk->total_harga_satuan,
                      '$row_costogo_induk->nama_komposisi',
                      '$row_costogo_induk->komposisi_satuan',
                      $row_costogo_induk->harga_satuan,
                      '$row_costogo_induk->tahap_tanggal_kendali'
                    )
                    ";

                  $client->query($sql_insert_induk_kendali);
                } else {
                  $sql_update_induk_kendali = "
                    update tbl_induk_togo set
                    kode_komposisi = '$row_costogo_induk->kode_komposisi',
                    volume_komposisi = $row_costogo_induk->volume_komposisi,
                    koefisien_komposisi  = $row_costogo_induk->koefisien_komposisi,
                    total_harga_satuan = $row_costogo_induk->total_harga_satuan,
                    nama_komposisi = '$row_costogo_induk->nama_komposisi',
                    komposisi_satuan = '$row_costogo_induk->komposisi_satuan',
                    harga_satuan = $row_costogo_induk->harga_satuan
                    where 
                    no_spk = '$row_costogo_induk->no_spk' and
                    kode_tahap_kendali = '$row_costogo_induk->kode_tahap_kendali'and
                    tahap_tanggal_kendali = '$row_costogo_induk->tahap_tanggal_kendali'
                  ";
                  $client->query($sql_update_induk_kendali);
                }
              }
            }

            $sql_get_komposisi_kendali = "select 
              (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
              c.detail_material_kode,
              a.kode_tree as tahap_kode_kendali,
              1 as komposisi_volume_kendali,
              c.harga as komposisi_harga_satuan_kendali,
              c.subtotal as komposisi_total_kendali,
              c.koefisien as komposisi_koefisien_kendali,
              a.volume * c.koefisien as komposisi_volume_total_kendali,
              'X' as kode_komposisi_kendali,
              c.keterangan as keterangan,
              c.kode_rap,
              a.tanggal_kendali as tahap_tanggal_kendali
              from 
              simpro_costogo_item_tree a 
              join simpro_costogo_analisa_item_apek b 
              on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
              join
              (
              SELECT tbl_analisa_satuan.* FROM (
              (
                SELECT          
                  simpro_costogo_analisa_asat.kode_analisa,
                  simpro_tbl_detail_material.detail_material_kode, 
                  simpro_tbl_detail_material.detail_material_nama, 
                  simpro_tbl_detail_material.detail_material_satuan,
                  simpro_costogo_analisa_asat.harga,
                  simpro_costogo_analisa_asat.koefisien,
                  (simpro_costogo_analisa_asat.harga * simpro_costogo_analisa_asat.koefisien) AS subtotal,
                  simpro_costogo_analisa_asat.kode_rap,
                  simpro_costogo_analisa_asat.keterangan
                FROM 
                  simpro_costogo_analisa_asat
                LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_costogo_analisa_asat.kode_material
                LEFT JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_asat.kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_asat.id_proyek AND simpro_costogo_analisa_daftar.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali)
                LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
                WHERE simpro_costogo_analisa_asat.id_proyek= $proyek_id and simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
                ORDER BY 
                  simpro_costogo_analisa_asat.kode_analisa,
                  simpro_tbl_detail_material.detail_material_kode        
                ASC
              )
              UNION ALL 
              (
                select 
                a.parent_kode_analisa as kode_analisa,
                b.kode_material,
                c.detail_material_nama,
                c.detail_material_satuan,
                coalesce(b.harga,0) as harga,
                sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                b.kode_rap,
                b.keterangan
                from simpro_costogo_analisa_apek a
                join simpro_costogo_analisa_asat b
                on a.id_data_analisa = b.id_data_analisa
                join simpro_tbl_detail_material c
                on b.kode_material = c.detail_material_kode
                where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
              )   
              ) AS tbl_analisa_satuan
              left join simpro_tbl_subbidang
              on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
              ) c
              on c.kode_analisa = b.kode_analisa
              where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
              order by a.kode_tree, tahap_tanggal_kendali";

              $q_get_komposisi_kendali = $this->db->query($sql_get_komposisi_kendali);


              if ($q_get_komposisi_kendali->result()) {
                foreach ($q_get_komposisi_kendali->result() as $row_costogo_komposisi) {
                  $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_komposisi_togo where tahap_kode_kendali = '$row_costogo_komposisi->tahap_kode_kendali' and no_spk = '$row_costogo_komposisi->no_spk' and detail_material_kode = '$row_costogo_komposisi->detail_material_kode' and tahap_tanggal_kendali = '$row_costogo_komposisi->tahap_tanggal_kendali'"));
                  $data = $get_decode->{'data'};
                  $count = $data[0]->{'count'};
                  if ($count == 0) {
                    $sql_insert_komposisi_kendali = "
                      insert into tbl_komposisi_togo (
                        no_spk,
                        detail_material_kode,
                        tahap_kode_kendali,
                        komposisi_volume_kendali,
                        komposisi_harga_satuan_kendali,
                        komposisi_total_kendali,
                        komposisi_koefisien_kendali,
                        komposisi_volume_total_kendali,
                        kode_komposisi_kendali,
                        keterangan,
                        kode_rap,
                        tahap_tanggal_kendali,
                        user_update,
                        tgl_update,
                        ip_update,
                        divisi_update,
                        waktu_update
                      ) values (
                        '$row_costogo_komposisi->no_spk',
                        '$row_costogo_komposisi->detail_material_kode',
                        '$row_costogo_komposisi->tahap_kode_kendali',
                        '$row_costogo_komposisi->komposisi_volume_kendali',
                        '$row_costogo_komposisi->komposisi_harga_satuan_kendali',
                        '$row_costogo_komposisi->komposisi_total_kendali',
                        '$row_costogo_komposisi->komposisi_koefisien_kendali',
                        '$row_costogo_komposisi->komposisi_volume_total_kendali',
                        '$row_costogo_komposisi->kode_komposisi_kendali',
                        '$row_costogo_komposisi->keterangan',
                        '$row_costogo_komposisi->kode_rap',
                        '$row_costogo_komposisi->tahap_tanggal_kendali',
                        '$uname_update',
                        '$tgl_update',
                        '$ip_update',
                        '$divisi_update',
                        '$waktu_update'
                      )
                      ";

                    $client->query($sql_insert_komposisi_kendali);
                  } else {
                    $sql_update_komposisi_kendali = "
                      update tbl_komposisi_togo set
                      komposisi_volume_kendali = '$row_costogo_komposisi->komposisi_volume_kendali',
                      komposisi_harga_satuan_kendali = '$row_costogo_komposisi->komposisi_harga_satuan_kendali',
                      komposisi_total_kendali  = '$row_costogo_komposisi->komposisi_total_kendali',
                      komposisi_koefisien_kendali  = '$row_costogo_komposisi->komposisi_koefisien_kendali',
                      komposisi_volume_total_kendali = '$row_costogo_komposisi->komposisi_volume_total_kendali',
                      kode_komposisi_kendali = '$row_costogo_komposisi->kode_komposisi_kendali',
                      keterangan = '$row_costogo_komposisi->keterangan',
                      kode_rap = '$row_costogo_komposisi->kode_rap',
                      user_update  = '$uname_update',
                      tgl_update = '$tgl_update',
                      ip_update  = '$ip_update',
                      divisi_update  = '$divisi_update',
                      waktu_update  = '$waktu_update'
                      where 
                      no_spk = '$row_costogo_komposisi->no_spk' and 
                      tahap_kode_kendali = '$row_costogo_komposisi->tahap_kode_kendali' and 
                      detail_material_kode = '$row_costogo_komposisi->detail_material_kode' and 
                      tahap_tanggal_kendali = '$row_costogo_komposisi->tahap_tanggal_kendali'
                    ";

                    $client->query($sql_update_komposisi_kendali);
                  }
                }
              }
          }
        }

        $q_tgl_ctg_server = json_decode($client->query("select distinct(tahap_tanggal_kendali) from tbl_cost_togo where no_spk = '$no_spk'"));
        $data_tgl_ctg_server = $q_tgl_ctg_server->{'data'};
        if ($data_tgl_ctg_server != '') {
          foreach ($data_tgl_ctg_server as $r_tgl_ctg_server) {
            $tgl_rab = $r_tgl_ctg_server->{'tahap_tanggal_kendali'};

            $get_costogo_tahap_server = json_decode($client->query("select * from tbl_cost_togo where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
            $data_costogo_tahap_server = $get_costogo_tahap_server->{'data'};
            foreach ($data_costogo_tahap_server as $r_costogo_tahap_server) {
              $kode = $r_costogo_tahap_server->{'tahap_kode_kendali'};
              $data_costogo_tahap_local = $this->db->query("select * from simpro_costogo_item_tree where kode_tree = '$kode' and id_proyek = $proyek_id and tanggal_kendali = '$tgl_rab'");
              if ($data_costogo_tahap_local->num_rows() == 0) {
                $client->query("delete from tbl_cost_togo where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                $client->query("delete from tbl_komposisi_togo where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                $client->query("delete from tbl_induk_togo where no_spk = '$no_spk' and kode_tahap_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
              }
            }

            $get_costogo_komposisi_server = json_decode($client->query("select distinct(tahap_kode_kendali) from tbl_komposisi_togo where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
            $data_costogo_komposisi_server = $get_costogo_komposisi_server->{'data'};
            if ($data_costogo_komposisi_server != '') {
              foreach ($data_costogo_komposisi_server as $r_costogo_komposisi_server) {
                $kode = $r_costogo_komposisi_server->{'tahap_kode_kendali'};

                $get_kode_local = $this->db->query("select 
                (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                c.detail_material_kode,
                a.kode_tree as tahap_kode_kendali,
                1 as komposisi_volume_kendali,
                c.harga as komposisi_harga_satuan_kendali,
                c.subtotal as komposisi_total_kendali,
                c.koefisien as komposisi_koefisien_kendali,
                a.volume * c.koefisien as komposisi_volume_total_kendali,
                'X' as kode_komposisi_kendali,
                c.keterangan as keterangan,
                c.kode_rap,
                a.tanggal_kendali as tahap_tanggal_kendali
                from 
                simpro_costogo_item_tree a 
                join simpro_costogo_analisa_item_apek b 
                on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
                join
                (
                SELECT tbl_analisa_satuan.* FROM (
                (
                  SELECT          
                    simpro_costogo_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode, 
                    simpro_tbl_detail_material.detail_material_nama, 
                    simpro_tbl_detail_material.detail_material_satuan,
                    simpro_costogo_analisa_asat.harga,
                    simpro_costogo_analisa_asat.koefisien,
                    (simpro_costogo_analisa_asat.harga * simpro_costogo_analisa_asat.koefisien) AS subtotal,
                    simpro_costogo_analisa_asat.kode_rap,
                    simpro_costogo_analisa_asat.keterangan
                  FROM 
                    simpro_costogo_analisa_asat
                  LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_costogo_analisa_asat.kode_material
                  LEFT JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_asat.kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_asat.id_proyek AND simpro_costogo_analisa_daftar.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali)
                  LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
                  WHERE simpro_costogo_analisa_asat.id_proyek= $proyek_id and simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
                  ORDER BY 
                    simpro_costogo_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode        
                  ASC
                )
                UNION ALL 
                (
                  select 
                  a.parent_kode_analisa as kode_analisa,
                  b.kode_material,
                  c.detail_material_nama,
                  c.detail_material_satuan,
                  coalesce(b.harga,0) as harga,
                  sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                  sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                  b.kode_rap,
                  b.keterangan
                  from simpro_costogo_analisa_apek a
                  join simpro_costogo_analisa_asat b
                  on a.id_data_analisa = b.id_data_analisa
                  join simpro_tbl_detail_material c
                  on b.kode_material = c.detail_material_kode
                  where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                  group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                )   
                ) AS tbl_analisa_satuan
                left join simpro_tbl_subbidang
                on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                ) c
                on c.kode_analisa = b.kode_analisa
                where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab' and a.kode_tree = '$kode'
                order by a.kode_tree, tahap_tanggal_kendali");
                
                if ($get_kode_local->num_rows() == 0) {
                  $client->query("delete from tbl_komposisi_togo where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                  $client->query("delete from tbl_induk_togo where no_spk = '$no_spk' and kode_tahap_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                } else {
                  $get_costogo_material_server = json_decode($client->query("select * from tbl_komposisi_togo where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'"));
                  $data_costogo_material = $get_costogo_material_server->{'data'};
                  if ($data_costogo_material != '') {
                    foreach ($data_costogo_material as $r_costogo_material) {
                      $kode_material = $r_costogo_material->{'detail_material_kode'};

                      $data_costogo_komposisi_local = $this->db->query("select 
                      (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                      c.detail_material_kode,
                      a.kode_tree as tahap_kode_kendali,
                      1 as komposisi_volume_kendali,
                      c.harga as komposisi_harga_satuan_kendali,
                      c.subtotal as komposisi_total_kendali,
                      c.koefisien as komposisi_koefisien_kendali,
                      a.volume * c.koefisien as komposisi_volume_total_kendali,
                      'X' as kode_komposisi_kendali,
                      c.keterangan as keterangan,
                      c.kode_rap,
                      a.tanggal_kendali as tahap_tanggal_kendali
                      from 
                      simpro_costogo_item_tree a 
                      join simpro_costogo_analisa_item_apek b 
                      on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
                      join
                      (
                      SELECT tbl_analisa_satuan.* FROM (
                      (
                        SELECT          
                          simpro_costogo_analisa_asat.kode_analisa,
                          simpro_tbl_detail_material.detail_material_kode, 
                          simpro_tbl_detail_material.detail_material_nama, 
                          simpro_tbl_detail_material.detail_material_satuan,
                          simpro_costogo_analisa_asat.harga,
                          simpro_costogo_analisa_asat.koefisien,
                          (simpro_costogo_analisa_asat.harga * simpro_costogo_analisa_asat.koefisien) AS subtotal,
                          simpro_costogo_analisa_asat.kode_rap,
                          simpro_costogo_analisa_asat.keterangan
                        FROM 
                          simpro_costogo_analisa_asat
                        LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_costogo_analisa_asat.kode_material
                        LEFT JOIN simpro_costogo_analisa_daftar ON (simpro_costogo_analisa_daftar.kode_analisa = simpro_costogo_analisa_asat.kode_analisa AND simpro_costogo_analisa_daftar.id_proyek= simpro_costogo_analisa_asat.id_proyek AND simpro_costogo_analisa_daftar.tanggal_kendali = simpro_costogo_analisa_asat.tanggal_kendali)
                        LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_costogo_analisa_daftar.id_satuan
                        WHERE simpro_costogo_analisa_asat.id_proyek= $proyek_id and simpro_costogo_analisa_asat.tanggal_kendali = '$tgl_rab'
                        ORDER BY 
                          simpro_costogo_analisa_asat.kode_analisa,
                          simpro_tbl_detail_material.detail_material_kode        
                        ASC
                      )
                      UNION ALL 
                      (
                        select 
                        a.parent_kode_analisa as kode_analisa,
                        b.kode_material,
                        c.detail_material_nama,
                        c.detail_material_satuan,
                        coalesce(b.harga,0) as harga,
                        sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                        sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                        b.kode_rap,
                        b.keterangan
                        from simpro_costogo_analisa_apek a
                        join simpro_costogo_analisa_asat b
                        on a.id_data_analisa = b.id_data_analisa
                        join simpro_tbl_detail_material c
                        on b.kode_material = c.detail_material_kode
                        where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                        group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                      )   
                      ) AS tbl_analisa_satuan
                      left join simpro_tbl_subbidang
                      on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                      ) c
                      on c.kode_analisa = b.kode_analisa
                      where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab' and a.kode_tree = '$kode' and c.detail_material_kode = '$kode_material'
                      order by a.kode_tree, tahap_tanggal_kendali");

                      if ($data_costogo_komposisi_local->num_rows() == 0) {
                        $client->query("delete from tbl_komposisi_togo where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and detail_material_kode = '$kode_material' and tahap_tanggal_kendali = '$tgl_rab'");
                      }
                    }                
                  }              
                }
              }
            }            
          }
        }
      }

      if ($arg == 'current_budget') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);


        $get_tgl_cb = "select distinct(tanggal_kendali), c.* from (select 
            CASE WHEN 
            (SELECT
            count(*) as jml_data
            FROM
            (SELECT
            tanggal_kendali
            FROM
            simpro_current_budget_item_tree
            where id_proyek = $proyek_id
            GROUP BY tanggal_kendali) as q_tgl) = 1
            THEN 
            (SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id)
            ELSE 
            (CASE WHEN (SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
            WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = $proyek_id
            ORDER BY tanggal_kendali desc limit 1)::date is null
            THEN (SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id)
            ELSE (SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
            WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = $proyek_id
            ORDER BY tanggal_kendali desc limit 1)::date
            END)
            END as tgl_awal,
            a.tanggal_kendali as tgl_akhir
            from 
            simpro_current_budget_item_tree a
            left join simpro_tbl_kontrak_terkini d on d.tgl_akhir >= a.tanggal_kendali and d.tahap_tanggal_kendali <= a.tanggal_kendali and d.proyek_id = a.id_proyek
            where a.id_proyek = $proyek_id
            group by a.tanggal_kendali, d.tahap_tanggal_kendali
            order by a.tanggal_kendali asc) c
            join simpro_current_budget_item_tree d
            on d.tanggal_kendali = c.tgl_akhir
            where id_proyek = $proyek_id";

        $q_tgl_cb = $this->db->query($get_tgl_cb);
        if ($q_tgl_cb->result()) {
          foreach ($q_tgl_cb->result() as $r_tgl_cb) {
            $startdate = $r_tgl_cb->tgl_awal;
            $enddate = $r_tgl_cb->tgl_akhir;
            $timestamp=  strtotime($startdate);

            if ($startdate == $enddate) {  

                $tgl_rab = $enddate;

                $sql_get_tahap_kendali = "select 
                x.kode_tree as tahap_kode_kendali,
                x.tree_item as tahap_nama_kendali,
                x.tree_satuan as tahap_satuan_kendali,
                ''::text as tahap_keterangan_kendali,
                (select no_spk from simpro_tbl_proyek where proyek_id = x.id_proyek) as no_spk, 
                x.volume as tahap_volume_kendali, 
                case when sum(totals.subtotal) = 0 or x.volume = 0 then
                0
                else
                sum(totals.subtotal) / x.volume
                end as tahap_harga_satuan_kendali,
                (
                sum(totals.subtotal)
                ) as tahap_total_kendali,
                x.tree_parent_kode as tahap_kode_induk_kendali,
                tanggal_kendali as tahap_tanggal_kendali
                from
                simpro_current_budget_item_tree x
                left join
                (SELECT 
                  simpro_current_budget_item_tree.kode_tree,
                  simpro_current_budget_analisa_item_apek.kode_analisa,
                  COALESCE(tbl_harga.harga, 0) AS harga,
                  (COALESCE(tbl_harga.harga, 0) * simpro_current_budget_item_tree.volume) as subtotal
                FROM simpro_current_budget_item_tree 
                LEFT JOIN simpro_current_budget_analisa_item_apek ON simpro_current_budget_analisa_item_apek.kode_tree = simpro_current_budget_item_tree.kode_tree and simpro_current_budget_analisa_item_apek.id_proyek = $proyek_id and simpro_current_budget_analisa_item_apek.tanggal_kendali = '$tgl_rab'
                LEFT JOIN (
                  SELECT 
                  DISTINCT ON(kode_analisa)
                            kode_analisa,
                            SUM(subtotal) AS harga
                  FROM (
                  (
                    SELECT          
                      (simpro_current_budget_analisa_asat.kode_analisa) AS kode_analisa, 
                      (simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal
                    FROM 
                      simpro_current_budget_analisa_asat
                    LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
                    LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali= simpro_current_budget_analisa_asat.tanggal_kendali)
                    LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                    WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_rab'
                    ORDER BY 
                      simpro_current_budget_analisa_asat.kode_analisa,
                      simpro_tbl_detail_material.detail_material_kode        
                    ASC
                  )
                  UNION ALL 
                  (
                    SELECT 
                      (simpro_current_budget_analisa_apek.parent_kode_analisa) AS kode_analisa, 
                      COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
                    FROM 
                      simpro_current_budget_analisa_apek
                    INNER JOIN simpro_current_budget_analisa_daftar ad ON (ad.kode_analisa = simpro_current_budget_analisa_apek.kode_analisa AND ad.id_proyek = simpro_current_budget_analisa_apek.id_proyek and ad.tanggal_kendali = simpro_current_budget_analisa_apek.tanggal_kendali)
                    INNER JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_apek.parent_kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_apek.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali= simpro_current_budget_analisa_apek.tanggal_kendali)     
                    INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                    LEFT JOIN (
                      SELECT 
                        DISTINCT ON(kode_analisa)
                        kode_analisa,
                        SUM(harga * koefisien) AS harga
                      FROM simpro_current_budget_analisa_asat 
                      WHERE id_proyek= $proyek_id and tanggal_kendali = '$tgl_rab'
                      
                      GROUP BY kode_analisa     
                    ) as tbl_harga ON tbl_harga.kode_analisa = simpro_current_budget_analisa_apek.kode_analisa     
                    WHERE simpro_current_budget_analisa_apek.id_proyek= $proyek_id and simpro_current_budget_analisa_apek.tanggal_kendali = '$tgl_rab'
                    
                    ORDER BY 
                      simpro_current_budget_analisa_apek.parent_kode_analisa,        
                      simpro_current_budget_analisa_apek.kode_analisa
                    ASC         
                  )   
                  ) AS tbl_analisa_satuan
                  GROUP BY kode_analisa       
                ) as tbl_harga ON tbl_harga.kode_analisa = simpro_current_budget_analisa_item_apek.kode_analisa            
                WHERE simpro_current_budget_item_tree.id_proyek = $proyek_id and simpro_current_budget_item_tree.tanggal_kendali = '$tgl_rab'
                ORDER BY simpro_current_budget_item_tree.kode_tree ASC) as totals
                on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
                WHERE x.id_proyek = $proyek_id and x.tanggal_kendali = '$tgl_rab'
                group by x.current_budget_item_tree      
                ORDER BY x.kode_tree";

              $q_get_tahap_kendali = $this->db->query($sql_get_tahap_kendali);

              if ($q_get_tahap_kendali->result()) {
                foreach ($q_get_tahap_kendali->result() as $row_current_budget_tahap) {
                  $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_current_budget where tahap_kode_kendali = '$row_current_budget_tahap->tahap_kode_kendali' and no_spk = '$row_current_budget_tahap->no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
                  $data = $get_decode->{'data'};
                  $count = $data[0]->{'count'};
                  if ($count == 0) {
                    $sql_insert_tahap_kendali = "
                      insert into tbl_current_budget (
                        tahap_kode_kendali,
                        tahap_nama_kendali,
                        tahap_satuan_kendali,
                        tahap_keterangan_kendali,
                        no_spk,
                        tahap_volume_kendali,
                        tahap_harga_satuan_kendali,
                        tahap_total_kendali,
                        tahap_kode_induk_kendali,
                        tahap_tanggal_kendali,
                        user_update,
                        tgl_update,
                        ip_update,
                        divisi_update,
                        waktu_update
                      ) values (
                        '$row_current_budget_tahap->tahap_kode_kendali',
                        '$row_current_budget_tahap->tahap_nama_kendali',
                        '$row_current_budget_tahap->tahap_satuan_kendali',
                        '$row_current_budget_tahap->tahap_keterangan_kendali',
                        '$row_current_budget_tahap->no_spk',
                        $row_current_budget_tahap->tahap_volume_kendali,
                        $row_current_budget_tahap->tahap_harga_satuan_kendali,
                        $row_current_budget_tahap->tahap_total_kendali,
                        '$row_current_budget_tahap->tahap_kode_induk_kendali',
                        '$row_current_budget_tahap->tahap_tanggal_kendali',
                        '$uname_update',
                        '$tgl_update',
                        '$ip_update',
                        '$divisi_update',
                        '$waktu_update'
                      )
                      ";

                    $client->query($sql_insert_tahap_kendali);
                  } else {
                    $sql_update_tahap_kendali = "
                      update tbl_current_budget set
                      tahap_nama_kendali = '$row_current_budget_tahap->tahap_nama_kendali',
                      tahap_satuan_kendali = '$row_current_budget_tahap->tahap_satuan_kendali',
                      tahap_keterangan_kendali = '$row_current_budget_tahap->tahap_keterangan_kendali',
                      tahap_volume_kendali = $row_current_budget_tahap->tahap_volume_kendali,
                      tahap_harga_satuan_kendali = $row_current_budget_tahap->tahap_harga_satuan_kendali,
                      tahap_total_kendali  = $row_current_budget_tahap->tahap_total_kendali,
                      tahap_kode_induk_kendali = '$row_current_budget_tahap->tahap_kode_induk_kendali',
                      user_update  = '$uname_update',
                      tgl_update = '$tgl_update',
                      ip_update  = '$ip_update',
                      divisi_update  = '$divisi_update',
                      waktu_update  = '$waktu_update'
                      where
                      no_spk = '$row_current_budget_tahap->no_spk' and
                      tahap_kode_kendali = '$row_current_budget_tahap->tahap_kode_kendali' and
                      tahap_tanggal_kendali  = '$row_current_budget_tahap->tahap_tanggal_kendali'
                    ";

                    $client->query($sql_update_tahap_kendali);
                  }
                }
              }

              $sql_get_induk_kendali = "select 
                'X' as kode_komposisi,
                1 as volume_komposisi,
                1 as koefisien_komposisi,
                (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                a.kode_tree as kode_tahap_kendali,
                sum(c.subtotal) as total_harga_satuan,
                'ANALISA' as nama_komposisi,
                ''::text as komposisi_satuan,
                sum(c.harga) as harga_satuan,
                0 as no,
                a.tanggal_kendali as tahap_tanggal_kendali
                from 
                simpro_current_budget_item_tree a 
                join simpro_current_budget_analisa_item_apek b 
                on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
                join
                (
                SELECT tbl_analisa_satuan.* FROM (
                (
                  SELECT          
                    simpro_current_budget_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode, 
                    simpro_tbl_detail_material.detail_material_nama, 
                    simpro_tbl_detail_material.detail_material_satuan,
                    simpro_current_budget_analisa_asat.harga,
                    simpro_current_budget_analisa_asat.koefisien,
                    (simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal,
                    simpro_current_budget_analisa_asat.kode_rap,
                    simpro_current_budget_analisa_asat.keterangan
                  FROM 
                    simpro_current_budget_analisa_asat
                  LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
                  LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali= simpro_current_budget_analisa_asat.tanggal_kendali)
                  LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                  WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_rab'
                  ORDER BY 
                    simpro_current_budget_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode        
                  ASC
                )
                UNION ALL 
                (
                  select 
                  a.parent_kode_analisa as kode_analisa,
                  b.kode_material,
                  c.detail_material_nama,
                  c.detail_material_satuan,
                  coalesce(b.harga,0) as harga,
                  sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                  sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                  b.kode_rap,
                  b.keterangan
                  from simpro_current_budget_analisa_apek a
                  join simpro_current_budget_analisa_asat b
                  on a.id_data_analisa = b.id_data_analisa
                  join simpro_tbl_detail_material c
                  on b.kode_material = c.detail_material_kode
                  where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                  group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                )   
                ) AS tbl_analisa_satuan
                left join simpro_tbl_subbidang
                on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                ) c
                on c.kode_analisa = b.kode_analisa
                where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                group by a.id_proyek,a.kode_tree,
                tahap_tanggal_kendali";

              $q_get_induk_kendali = $this->db->query($sql_get_induk_kendali);

              if ($q_get_induk_kendali->result()) {
                foreach ($q_get_induk_kendali->result() as $row_current_budget_induk) {
                  $get_decode = json_decode($client->query("select count(kode_tahap_kendali) from tbl_induk_budget where kode_tahap_kendali = '$row_current_budget_induk->kode_tahap_kendali' and no_spk = '$row_current_budget_induk->no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
                  $data = $get_decode->{'data'};
                  $count = $data[0]->{'count'};
                  if ($count == 0) {
                    $sql_insert_induk_kendali = "
                      insert into tbl_induk_budget (
                        kode_komposisi,
                        volume_komposisi,
                        koefisien_komposisi,
                        no_spk,
                        kode_tahap_kendali,
                        total_harga_satuan,
                        nama_komposisi,
                        komposisi_satuan,
                        harga_satuan,
                        tahap_tanggal_kendali
                      ) values (
                        '$row_current_budget_induk->kode_komposisi',
                        $row_current_budget_induk->volume_komposisi,
                        $row_current_budget_induk->koefisien_komposisi,
                        '$row_current_budget_induk->no_spk',
                        '$row_current_budget_induk->kode_tahap_kendali',
                        $row_current_budget_induk->total_harga_satuan,
                        '$row_current_budget_induk->nama_komposisi',
                        '$row_current_budget_induk->komposisi_satuan',
                        $row_current_budget_induk->harga_satuan,
                        '$row_current_budget_induk->tahap_tanggal_kendali'
                      )
                      ";

                    $client->query($sql_insert_induk_kendali);
                  } else {
                    $sql_update_induk_kendali = "
                      update tbl_induk_budget set
                      kode_komposisi = '$row_current_budget_induk->kode_komposisi',
                      volume_komposisi = $row_current_budget_induk->volume_komposisi,
                      koefisien_komposisi  = $row_current_budget_induk->koefisien_komposisi,
                      total_harga_satuan = $row_current_budget_induk->total_harga_satuan,
                      nama_komposisi = '$row_current_budget_induk->nama_komposisi',
                      komposisi_satuan = '$row_current_budget_induk->komposisi_satuan',
                      harga_satuan = $row_current_budget_induk->harga_satuan
                      where 
                      no_spk = '$row_current_budget_induk->no_spk' and
                      kode_tahap_kendali = '$row_current_budget_induk->kode_tahap_kendali'and
                      tahap_tanggal_kendali = '$row_current_budget_induk->tahap_tanggal_kendali'
                    ";
                    $client->query($sql_update_induk_kendali);
                  }
                }
              }

              $sql_get_komposisi_kendali = "select 
                (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                c.detail_material_kode,
                a.kode_tree as tahap_kode_kendali,
                1 as komposisi_volume_kendali,
                c.harga as komposisi_harga_satuan_kendali,
                c.subtotal as komposisi_total_kendali,
                c.koefisien as komposisi_koefisien_kendali,
                a.volume * c.koefisien as komposisi_volume_total_kendali,
                'X' as kode_komposisi_kendali,
                c.keterangan as keterangan,
                c.kode_rap,
                a.tanggal_kendali as tahap_tanggal_kendali
                from 
                simpro_current_budget_item_tree a 
                join simpro_current_budget_analisa_item_apek b 
                on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
                join
                (
                SELECT tbl_analisa_satuan.* FROM (
                (
                  SELECT          
                    simpro_current_budget_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode, 
                    simpro_tbl_detail_material.detail_material_nama, 
                    simpro_tbl_detail_material.detail_material_satuan,
                    simpro_current_budget_analisa_asat.harga,
                    simpro_current_budget_analisa_asat.koefisien,
                    (simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal,
                    simpro_current_budget_analisa_asat.kode_rap,
                    simpro_current_budget_analisa_asat.keterangan
                  FROM 
                    simpro_current_budget_analisa_asat
                  LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
                  LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali = simpro_current_budget_analisa_asat.tanggal_kendali)
                  LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                  WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_rab'
                  ORDER BY 
                    simpro_current_budget_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode        
                  ASC
                )
                UNION ALL 
                (
                  select 
                  a.parent_kode_analisa as kode_analisa,
                  b.kode_material,
                  c.detail_material_nama,
                  c.detail_material_satuan,
                  coalesce(b.harga,0) as harga,
                  sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                  sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                  b.kode_rap,
                  b.keterangan
                  from simpro_current_budget_analisa_apek a
                  join simpro_current_budget_analisa_asat b
                  on a.id_data_analisa = b.id_data_analisa
                  join simpro_tbl_detail_material c
                  on b.kode_material = c.detail_material_kode
                  where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                  group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                )   
                ) AS tbl_analisa_satuan
                left join simpro_tbl_subbidang
                on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                ) c
                on c.kode_analisa = b.kode_analisa
                where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                order by a.kode_tree, tahap_tanggal_kendali";

                $q_get_komposisi_kendali = $this->db->query($sql_get_komposisi_kendali);


                if ($q_get_komposisi_kendali->result()) {
                  foreach ($q_get_komposisi_kendali->result() as $row_current_budget_komposisi) {
                    $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_komposisi_budget where tahap_kode_kendali = '$row_current_budget_komposisi->tahap_kode_kendali' and no_spk = '$row_current_budget_komposisi->no_spk' and detail_material_kode = '$row_current_budget_komposisi->detail_material_kode' and tahap_tanggal_kendali = '$row_current_budget_komposisi->tahap_tanggal_kendali'"));
                    $data = $get_decode->{'data'};
                    $count = $data[0]->{'count'};
                    if ($count == 0) {
                      $sql_insert_komposisi_kendali = "
                        insert into tbl_komposisi_budget (
                          no_spk,
                          detail_material_kode,
                          tahap_kode_kendali,
                          komposisi_volume_kendali,
                          komposisi_harga_satuan_kendali,
                          komposisi_total_kendali,
                          komposisi_koefisien_kendali,
                          komposisi_volume_total_kendali,
                          kode_komposisi_kendali,
                          keterangan,
                          kode_rap,
                          tahap_tanggal_kendali,
                          user_update,
                          tgl_update,
                          ip_update,
                          divisi_update,
                          waktu_update
                        ) values (
                          '$row_current_budget_komposisi->no_spk',
                          '$row_current_budget_komposisi->detail_material_kode',
                          '$row_current_budget_komposisi->tahap_kode_kendali',
                          '$row_current_budget_komposisi->komposisi_volume_kendali',
                          '$row_current_budget_komposisi->komposisi_harga_satuan_kendali',
                          '$row_current_budget_komposisi->komposisi_total_kendali',
                          '$row_current_budget_komposisi->komposisi_koefisien_kendali',
                          '$row_current_budget_komposisi->komposisi_volume_total_kendali',
                          '$row_current_budget_komposisi->kode_komposisi_kendali',
                          '$row_current_budget_komposisi->keterangan',
                          '$row_current_budget_komposisi->kode_rap',
                          '$row_current_budget_komposisi->tahap_tanggal_kendali',
                          '$uname_update',
                          '$tgl_update',
                          '$ip_update',
                          '$divisi_update',
                          '$waktu_update'
                        )
                        ";

                      $client->query($sql_insert_komposisi_kendali);
                    } else {
                      $sql_update_komposisi_kendali = "
                        update tbl_komposisi_budget set
                        komposisi_volume_kendali = '$row_current_budget_komposisi->komposisi_volume_kendali',
                        komposisi_harga_satuan_kendali = '$row_current_budget_komposisi->komposisi_harga_satuan_kendali',
                        komposisi_total_kendali  = '$row_current_budget_komposisi->komposisi_total_kendali',
                        komposisi_koefisien_kendali  = '$row_current_budget_komposisi->komposisi_koefisien_kendali',
                        komposisi_volume_total_kendali = '$row_current_budget_komposisi->komposisi_volume_total_kendali',
                        kode_komposisi_kendali = '$row_current_budget_komposisi->kode_komposisi_kendali',
                        keterangan = '$row_current_budget_komposisi->keterangan',
                        kode_rap = '$row_current_budget_komposisi->kode_rap',
                        user_update  = '$uname_update',
                        tgl_update = '$tgl_update',
                        ip_update  = '$ip_update',
                        divisi_update  = '$divisi_update',
                        waktu_update  = '$waktu_update'
                        where 
                        no_spk = '$row_current_budget_komposisi->no_spk' and 
                        tahap_kode_kendali = '$row_current_budget_komposisi->tahap_kode_kendali' and 
                        detail_material_kode = '$row_current_budget_komposisi->detail_material_kode' and 
                        tahap_tanggal_kendali = '$row_current_budget_komposisi->tahap_tanggal_kendali'
                      ";

                      $client->query($sql_update_komposisi_kendali);
                    }
                  }
                }
            } else {
              while ($startdate < $enddate) {
                $startdate = date('Y-m-d', $timestamp);
                $tgl_rab = $enddate;
                $timestamp = strtotime('+1 month', strtotime($startdate));

                $sql_get_tahap_kendali = "select 
                x.kode_tree as tahap_kode_kendali,
                x.tree_item as tahap_nama_kendali,
                x.tree_satuan as tahap_satuan_kendali,
                ''::text as tahap_keterangan_kendali,
                (select no_spk from simpro_tbl_proyek where proyek_id = x.id_proyek) as no_spk, 
                x.volume as tahap_volume_kendali, 
                case when sum(totals.subtotal) = 0 or x.volume = 0 then
                0
                else
                sum(totals.subtotal) / x.volume
                end as tahap_harga_satuan_kendali,
                (
                sum(totals.subtotal)
                ) as tahap_total_kendali,
                x.tree_parent_kode as tahap_kode_induk_kendali,
                tanggal_kendali as tahap_tanggal_kendali
                from
                simpro_current_budget_item_tree x
                left join
                (SELECT 
                  simpro_current_budget_item_tree.kode_tree,
                  simpro_current_budget_analisa_item_apek.kode_analisa,
                  COALESCE(tbl_harga.harga, 0) AS harga,
                  (COALESCE(tbl_harga.harga, 0) * simpro_current_budget_item_tree.volume) as subtotal
                FROM simpro_current_budget_item_tree 
                LEFT JOIN simpro_current_budget_analisa_item_apek ON simpro_current_budget_analisa_item_apek.kode_tree = simpro_current_budget_item_tree.kode_tree and simpro_current_budget_analisa_item_apek.id_proyek = $proyek_id and simpro_current_budget_analisa_item_apek.tanggal_kendali = '$tgl_rab'
                LEFT JOIN (
                  SELECT 
                  DISTINCT ON(kode_analisa)
                            kode_analisa,
                            SUM(subtotal) AS harga
                  FROM (
                  (
                    SELECT          
                      (simpro_current_budget_analisa_asat.kode_analisa) AS kode_analisa, 
                      (simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal
                    FROM 
                      simpro_current_budget_analisa_asat
                    LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
                    LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali= simpro_current_budget_analisa_asat.tanggal_kendali)
                    LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                    WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_rab'
                    ORDER BY 
                      simpro_current_budget_analisa_asat.kode_analisa,
                      simpro_tbl_detail_material.detail_material_kode        
                    ASC
                  )
                  UNION ALL 
                  (
                    SELECT 
                      (simpro_current_budget_analisa_apek.parent_kode_analisa) AS kode_analisa, 
                      COALESCE(tbl_harga.harga * koefisien, 0) AS subtotal
                    FROM 
                      simpro_current_budget_analisa_apek
                    INNER JOIN simpro_current_budget_analisa_daftar ad ON (ad.kode_analisa = simpro_current_budget_analisa_apek.kode_analisa AND ad.id_proyek = simpro_current_budget_analisa_apek.id_proyek and ad.tanggal_kendali = simpro_current_budget_analisa_apek.tanggal_kendali)
                    INNER JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_apek.parent_kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_apek.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali= simpro_current_budget_analisa_apek.tanggal_kendali)     
                    INNER JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                    LEFT JOIN (
                      SELECT 
                        DISTINCT ON(kode_analisa)
                        kode_analisa,
                        SUM(harga * koefisien) AS harga
                      FROM simpro_current_budget_analisa_asat 
                      WHERE id_proyek= $proyek_id and tanggal_kendali = '$tgl_rab'
                      
                      GROUP BY kode_analisa     
                    ) as tbl_harga ON tbl_harga.kode_analisa = simpro_current_budget_analisa_apek.kode_analisa     
                    WHERE simpro_current_budget_analisa_apek.id_proyek= $proyek_id and simpro_current_budget_analisa_apek.tanggal_kendali = '$tgl_rab'
                    
                    ORDER BY 
                      simpro_current_budget_analisa_apek.parent_kode_analisa,        
                      simpro_current_budget_analisa_apek.kode_analisa
                    ASC         
                  )   
                  ) AS tbl_analisa_satuan
                  GROUP BY kode_analisa       
                ) as tbl_harga ON tbl_harga.kode_analisa = simpro_current_budget_analisa_item_apek.kode_analisa            
                WHERE simpro_current_budget_item_tree.id_proyek = $proyek_id and simpro_current_budget_item_tree.tanggal_kendali = '$tgl_rab'
                ORDER BY simpro_current_budget_item_tree.kode_tree ASC) as totals
                on left(totals.kode_tree || '.',length(x.kode_tree || '.')) = x.kode_tree || '.'
                WHERE x.id_proyek = $proyek_id and x.tanggal_kendali = '$tgl_rab'
                group by x.current_budget_item_tree      
                ORDER BY x.kode_tree";

              $q_get_tahap_kendali = $this->db->query($sql_get_tahap_kendali);

              if ($q_get_tahap_kendali->result()) {
                foreach ($q_get_tahap_kendali->result() as $row_current_budget_tahap) {
                  $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_current_budget where tahap_kode_kendali = '$row_current_budget_tahap->tahap_kode_kendali' and no_spk = '$row_current_budget_tahap->no_spk' and tahap_tanggal_kendali = '$startdate'"));
                  $data = $get_decode->{'data'};
                  $count = $data[0]->{'count'};
                  if ($count == 0) {
                    $sql_insert_tahap_kendali = "
                      insert into tbl_current_budget (
                        tahap_kode_kendali,
                        tahap_nama_kendali,
                        tahap_satuan_kendali,
                        tahap_keterangan_kendali,
                        no_spk,
                        tahap_volume_kendali,
                        tahap_harga_satuan_kendali,
                        tahap_total_kendali,
                        tahap_kode_induk_kendali,
                        tahap_tanggal_kendali,
                        user_update,
                        tgl_update,
                        ip_update,
                        divisi_update,
                        waktu_update
                      ) values (
                        '$row_current_budget_tahap->tahap_kode_kendali',
                        '$row_current_budget_tahap->tahap_nama_kendali',
                        '$row_current_budget_tahap->tahap_satuan_kendali',
                        '$row_current_budget_tahap->tahap_keterangan_kendali',
                        '$row_current_budget_tahap->no_spk',
                        $row_current_budget_tahap->tahap_volume_kendali,
                        $row_current_budget_tahap->tahap_harga_satuan_kendali,
                        $row_current_budget_tahap->tahap_total_kendali,
                        '$row_current_budget_tahap->tahap_kode_induk_kendali',
                        '$startdate',
                        '$uname_update',
                        '$tgl_update',
                        '$ip_update',
                        '$divisi_update',
                        '$waktu_update'
                      )
                      ";

                    $client->query($sql_insert_tahap_kendali);
                  } else {
                    // echo "ubah";
                    $sql_update_tahap_kendali = "
                      update tbl_current_budget set
                      tahap_nama_kendali = '$row_current_budget_tahap->tahap_nama_kendali',
                      tahap_satuan_kendali = '$row_current_budget_tahap->tahap_satuan_kendali',
                      tahap_keterangan_kendali = '$row_current_budget_tahap->tahap_keterangan_kendali',
                      tahap_volume_kendali = $row_current_budget_tahap->tahap_volume_kendali,
                      tahap_harga_satuan_kendali = $row_current_budget_tahap->tahap_harga_satuan_kendali,
                      tahap_total_kendali  = $row_current_budget_tahap->tahap_total_kendali,
                      tahap_kode_induk_kendali = '$row_current_budget_tahap->tahap_kode_induk_kendali',
                      user_update  = '$uname_update',
                      tgl_update = '$tgl_update',
                      ip_update  = '$ip_update',
                      divisi_update  = '$divisi_update',
                      waktu_update  = '$waktu_update'
                      where
                      no_spk = '$row_current_budget_tahap->no_spk' and
                      tahap_kode_kendali = '$row_current_budget_tahap->tahap_kode_kendali' and
                      tahap_tanggal_kendali  = '$startdate'
                    ";

                    // echo $sql_update_tahap_kendali;
                    $client->query($sql_update_tahap_kendali);
                  }
                }
              }

              $sql_get_induk_kendali = "select 
                'X' as kode_komposisi,
                1 as volume_komposisi,
                1 as koefisien_komposisi,
                (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                a.kode_tree as kode_tahap_kendali,
                sum(c.subtotal) as total_harga_satuan,
                'ANALISA' as nama_komposisi,
                ''::text as komposisi_satuan,
                sum(c.harga) as harga_satuan,
                0 as no,
                a.tanggal_kendali as tahap_tanggal_kendali
                from 
                simpro_current_budget_item_tree a 
                join simpro_current_budget_analisa_item_apek b 
                on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
                join
                (
                SELECT tbl_analisa_satuan.* FROM (
                (
                  SELECT          
                    simpro_current_budget_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode, 
                    simpro_tbl_detail_material.detail_material_nama, 
                    simpro_tbl_detail_material.detail_material_satuan,
                    simpro_current_budget_analisa_asat.harga,
                    simpro_current_budget_analisa_asat.koefisien,
                    (simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal,
                    simpro_current_budget_analisa_asat.kode_rap,
                    simpro_current_budget_analisa_asat.keterangan
                  FROM 
                    simpro_current_budget_analisa_asat
                  LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
                  LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali= simpro_current_budget_analisa_asat.tanggal_kendali)
                  LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                  WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_rab'
                  ORDER BY 
                    simpro_current_budget_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode        
                  ASC
                )
                UNION ALL 
                (
                  select 
                  a.parent_kode_analisa as kode_analisa,
                  b.kode_material,
                  c.detail_material_nama,
                  c.detail_material_satuan,
                  coalesce(b.harga,0) as harga,
                  sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                  sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                  b.kode_rap,
                  b.keterangan
                  from simpro_current_budget_analisa_apek a
                  join simpro_current_budget_analisa_asat b
                  on a.id_data_analisa = b.id_data_analisa
                  join simpro_tbl_detail_material c
                  on b.kode_material = c.detail_material_kode
                  where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                  group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                )   
                ) AS tbl_analisa_satuan
                left join simpro_tbl_subbidang
                on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                ) c
                on c.kode_analisa = b.kode_analisa
                where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                group by a.id_proyek,a.kode_tree,
                tahap_tanggal_kendali";

              $q_get_induk_kendali = $this->db->query($sql_get_induk_kendali);

              if ($q_get_induk_kendali->result()) {
                foreach ($q_get_induk_kendali->result() as $row_current_budget_induk) {
                  $get_decode = json_decode($client->query("select count(kode_tahap_kendali) from tbl_induk_budget where kode_tahap_kendali = '$row_current_budget_induk->kode_tahap_kendali' and no_spk = '$row_current_budget_induk->no_spk' and tahap_tanggal_kendali = '$startdate'"));
                  $data = $get_decode->{'data'};
                  $count = $data[0]->{'count'};
                  if ($count == 0) {
                    $sql_insert_induk_kendali = "
                      insert into tbl_induk_budget (
                        kode_komposisi,
                        volume_komposisi,
                        koefisien_komposisi,
                        no_spk,
                        kode_tahap_kendali,
                        total_harga_satuan,
                        nama_komposisi,
                        komposisi_satuan,
                        harga_satuan,
                        tahap_tanggal_kendali
                      ) values (
                        '$row_current_budget_induk->kode_komposisi',
                        $row_current_budget_induk->volume_komposisi,
                        $row_current_budget_induk->koefisien_komposisi,
                        '$row_current_budget_induk->no_spk',
                        '$row_current_budget_induk->kode_tahap_kendali',
                        $row_current_budget_induk->total_harga_satuan,
                        '$row_current_budget_induk->nama_komposisi',
                        '$row_current_budget_induk->komposisi_satuan',
                        $row_current_budget_induk->harga_satuan,
                        '$startdate'
                      )
                      ";

                    $client->query($sql_insert_induk_kendali);
                  } else {
                    $sql_update_induk_kendali = "
                      update tbl_induk_budget set
                      kode_komposisi = '$row_current_budget_induk->kode_komposisi',
                      volume_komposisi = $row_current_budget_induk->volume_komposisi,
                      koefisien_komposisi  = $row_current_budget_induk->koefisien_komposisi,
                      total_harga_satuan = $row_current_budget_induk->total_harga_satuan,
                      nama_komposisi = '$row_current_budget_induk->nama_komposisi',
                      komposisi_satuan = '$row_current_budget_induk->komposisi_satuan',
                      harga_satuan = $row_current_budget_induk->harga_satuan
                      where 
                      no_spk = '$row_current_budget_induk->no_spk' and
                      kode_tahap_kendali = '$row_current_budget_induk->kode_tahap_kendali' and
                      tahap_tanggal_kendali = '$startdate'
                    ";
                    // echo $sql_update_induk_kendali;
                    $client->query($sql_update_induk_kendali);
                  }
                }
              }

              $sql_get_komposisi_kendali = "select 
                (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                c.detail_material_kode,
                a.kode_tree as tahap_kode_kendali,
                1 as komposisi_volume_kendali,
                c.harga as komposisi_harga_satuan_kendali,
                c.subtotal as komposisi_total_kendali,
                c.koefisien as komposisi_koefisien_kendali,
                a.volume * c.koefisien as komposisi_volume_total_kendali,
                'X' as kode_komposisi_kendali,
                c.keterangan as keterangan,
                c.kode_rap,
                a.tanggal_kendali as tahap_tanggal_kendali
                from 
                simpro_current_budget_item_tree a 
                join simpro_current_budget_analisa_item_apek b 
                on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
                join
                (
                SELECT tbl_analisa_satuan.* FROM (
                (
                  SELECT          
                    simpro_current_budget_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode, 
                    simpro_tbl_detail_material.detail_material_nama, 
                    simpro_tbl_detail_material.detail_material_satuan,
                    simpro_current_budget_analisa_asat.harga,
                    simpro_current_budget_analisa_asat.koefisien,
                    (simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal,
                    simpro_current_budget_analisa_asat.kode_rap,
                    simpro_current_budget_analisa_asat.keterangan
                  FROM 
                    simpro_current_budget_analisa_asat
                  LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
                  LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali = simpro_current_budget_analisa_asat.tanggal_kendali)
                  LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                  WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_rab'
                  ORDER BY 
                    simpro_current_budget_analisa_asat.kode_analisa,
                    simpro_tbl_detail_material.detail_material_kode        
                  ASC
                )
                UNION ALL 
                (
                  select 
                  a.parent_kode_analisa as kode_analisa,
                  b.kode_material,
                  c.detail_material_nama,
                  c.detail_material_satuan,
                  coalesce(b.harga,0) as harga,
                  sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                  sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                  b.kode_rap,
                  b.keterangan
                  from simpro_current_budget_analisa_apek a
                  join simpro_current_budget_analisa_asat b
                  on a.id_data_analisa = b.id_data_analisa
                  join simpro_tbl_detail_material c
                  on b.kode_material = c.detail_material_kode
                  where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                  group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                )   
                ) AS tbl_analisa_satuan
                left join simpro_tbl_subbidang
                on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                ) c
                on c.kode_analisa = b.kode_analisa
                where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_rab'
                order by a.kode_tree, tahap_tanggal_kendali";

                $q_get_komposisi_kendali = $this->db->query($sql_get_komposisi_kendali);


                if ($q_get_komposisi_kendali->result()) {
                  foreach ($q_get_komposisi_kendali->result() as $row_current_budget_komposisi) {
                    $get_decode = json_decode($client->query("select count(tahap_kode_kendali) from tbl_komposisi_budget where tahap_kode_kendali = '$row_current_budget_komposisi->tahap_kode_kendali' and no_spk = '$row_current_budget_komposisi->no_spk' and detail_material_kode = '$row_current_budget_komposisi->detail_material_kode' and tahap_tanggal_kendali = '$startdate'"));
                    $data = $get_decode->{'data'};
                    $count = $data[0]->{'count'};
                    if ($count == 0) {
                      $sql_insert_komposisi_kendali = "
                        insert into tbl_komposisi_budget (
                          no_spk,
                          detail_material_kode,
                          tahap_kode_kendali,
                          komposisi_volume_kendali,
                          komposisi_harga_satuan_kendali,
                          komposisi_total_kendali,
                          komposisi_koefisien_kendali,
                          komposisi_volume_total_kendali,
                          kode_komposisi_kendali,
                          keterangan,
                          kode_rap,
                          tahap_tanggal_kendali,
                          user_update,
                          tgl_update,
                          ip_update,
                          divisi_update,
                          waktu_update
                        ) values (
                          '$row_current_budget_komposisi->no_spk',
                          '$row_current_budget_komposisi->detail_material_kode',
                          '$row_current_budget_komposisi->tahap_kode_kendali',
                          '$row_current_budget_komposisi->komposisi_volume_kendali',
                          '$row_current_budget_komposisi->komposisi_harga_satuan_kendali',
                          '$row_current_budget_komposisi->komposisi_total_kendali',
                          '$row_current_budget_komposisi->komposisi_koefisien_kendali',
                          '$row_current_budget_komposisi->komposisi_volume_total_kendali',
                          '$row_current_budget_komposisi->kode_komposisi_kendali',
                          '$row_current_budget_komposisi->keterangan',
                          '$row_current_budget_komposisi->kode_rap',
                          '$startdate',
                          '$uname_update',
                          '$tgl_update',
                          '$ip_update',
                          '$divisi_update',
                          '$waktu_update'
                        )
                        ";

                      $client->query($sql_insert_komposisi_kendali);
                    } else {
                      $sql_update_komposisi_kendali = "
                        update tbl_komposisi_budget set
                        komposisi_volume_kendali = '$row_current_budget_komposisi->komposisi_volume_kendali',
                        komposisi_harga_satuan_kendali = '$row_current_budget_komposisi->komposisi_harga_satuan_kendali',
                        komposisi_total_kendali  = '$row_current_budget_komposisi->komposisi_total_kendali',
                        komposisi_koefisien_kendali  = '$row_current_budget_komposisi->komposisi_koefisien_kendali',
                        komposisi_volume_total_kendali = '$row_current_budget_komposisi->komposisi_volume_total_kendali',
                        kode_komposisi_kendali = '$row_current_budget_komposisi->kode_komposisi_kendali',
                        keterangan = '$row_current_budget_komposisi->keterangan',
                        kode_rap = '$row_current_budget_komposisi->kode_rap',
                        user_update  = '$uname_update',
                        tgl_update = '$tgl_update',
                        ip_update  = '$ip_update',
                        divisi_update  = '$divisi_update',
                        waktu_update  = '$waktu_update'
                        where 
                        no_spk = '$row_current_budget_komposisi->no_spk' and 
                        tahap_kode_kendali = '$row_current_budget_komposisi->tahap_kode_kendali' and 
                        detail_material_kode = '$row_current_budget_komposisi->detail_material_kode' and 
                        tahap_tanggal_kendali = '$startdate'
                      ";

                      $client->query($sql_update_komposisi_kendali);
                    }
                  }
                }
              }
            }
          }
        }

        // $sql_tgl_ctg = "select distinct(tanggal_kendali) from simpro_current_budget_item_tree where id_proyek = $proyek_id";
        // $q_tgl_ctg = $this->db->query($sql_tgl_ctg);
        // if ($q_tgl_ctg->result()) {
        //   foreach ($q_tgl_ctg->result() as $r_tgl_ctg) {
        //     $tgl_rab = $r_tgl_ctg->tanggal_kendali;

            
        //   }
        // }

        /////////////// Delete cb /////////

        $q_tgl_ctg_server = json_decode($client->query("select distinct(tahap_tanggal_kendali) from tbl_current_budget where no_spk = '$no_spk'"));
        $data_tgl_ctg_server = $q_tgl_ctg_server->{'data'};
        if ($data_tgl_ctg_server != '') {
          foreach ($data_tgl_ctg_server as $r_tgl_ctg_server) {
            $tgl_rab = $r_tgl_ctg_server->{'tahap_tanggal_kendali'};

            $get_cek_tgl_cb_local = "select distinct(tanggal_kendali), c.* from (select 
            CASE WHEN 
            (SELECT
            count(*) as jml_data
            FROM
            (SELECT
            tanggal_kendali
            FROM
            simpro_current_budget_item_tree
            where id_proyek = $proyek_id
            GROUP BY tanggal_kendali) as q_tgl) = 1
            THEN 
            (SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id)
            ELSE 
            (CASE WHEN (SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
            WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = $proyek_id
            ORDER BY tanggal_kendali desc limit 1)::date is null
            THEN (SELECT min(tahap_tanggal_kendali) from simpro_tbl_kontrak_terkini where proyek_id = $proyek_id)
            ELSE (SELECT tanggal_kendali + interval '1 month' from simpro_current_budget_item_tree 
            WHERE tanggal_kendali < a.tanggal_kendali and id_proyek = $proyek_id
            ORDER BY tanggal_kendali desc limit 1)::date
            END)
            END as tgl_awal,
            a.tanggal_kendali as tgl_akhir
            from 
            simpro_current_budget_item_tree a
            left join simpro_tbl_kontrak_terkini d on d.tgl_akhir >= a.tanggal_kendali and d.tahap_tanggal_kendali <= a.tanggal_kendali and d.proyek_id = a.id_proyek
            where a.id_proyek = $proyek_id
            group by a.tanggal_kendali, d.tahap_tanggal_kendali
            order by a.tanggal_kendali asc) c
            join simpro_current_budget_item_tree d
            on d.tanggal_kendali = c.tgl_akhir
            where id_proyek = $proyek_id and c.tgl_awal <= '$tgl_rab' and c.tgl_akhir >= '$tgl_rab'";

            $q_get_cek_tgl_cb_local = $this->db->query($get_cek_tgl_cb_local);

            if ($q_get_cek_tgl_cb_local->result()) {
              $tgl_awal = $q_get_cek_tgl_cb_local->row()->tgl_awal;
              $tgl_akhir = $q_get_cek_tgl_cb_local->row()->tgl_akhir;

              $get_current_budget_tahap_server = json_decode($client->query("select * from tbl_current_budget where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
              $data_current_budget_tahap_server = $get_current_budget_tahap_server->{'data'};
              foreach ($data_current_budget_tahap_server as $r_current_budget_tahap_server) {
                $kode = $r_current_budget_tahap_server->{'tahap_kode_kendali'};
                $data_current_budget_tahap_local = $this->db->query("select * from simpro_current_budget_item_tree where kode_tree = '$kode' and id_proyek = $proyek_id and tanggal_kendali = '$tgl_akhir'");
                if ($data_current_budget_tahap_local->num_rows() == 0) {
                  $client->query("delete from tbl_current_budget where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                  $client->query("delete from tbl_komposisi_budget where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                  $client->query("delete from tbl_induk_budget where no_spk = '$no_spk' and kode_tahap_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                }
              }

              $get_current_budget_komposisi_server = json_decode($client->query("select distinct(tahap_kode_kendali) from tbl_komposisi_budget where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab'"));
              $data_current_budget_komposisi_server = $get_current_budget_komposisi_server->{'data'};
              if ($data_current_budget_komposisi_server != '') {
                foreach ($data_current_budget_komposisi_server as $r_current_budget_komposisi_server) {
                  $kode = $r_current_budget_komposisi_server->{'tahap_kode_kendali'};

                  $get_kode_local = $this->db->query("select 
                  (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                  c.detail_material_kode,
                  a.kode_tree as tahap_kode_kendali,
                  1 as komposisi_volume_kendali,
                  c.harga as komposisi_harga_satuan_kendali,
                  c.subtotal as komposisi_total_kendali,
                  c.koefisien as komposisi_koefisien_kendali,
                  a.volume * c.koefisien as komposisi_volume_total_kendali,
                  'X' as kode_komposisi_kendali,
                  c.keterangan as keterangan,
                  c.kode_rap,
                  a.tanggal_kendali as tahap_tanggal_kendali
                  from 
                  simpro_current_budget_item_tree a 
                  join simpro_current_budget_analisa_item_apek b 
                  on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
                  join
                  (
                  SELECT tbl_analisa_satuan.* FROM (
                  (
                    SELECT          
                      simpro_current_budget_analisa_asat.kode_analisa,
                      simpro_tbl_detail_material.detail_material_kode, 
                      simpro_tbl_detail_material.detail_material_nama, 
                      simpro_tbl_detail_material.detail_material_satuan,
                      simpro_current_budget_analisa_asat.harga,
                      simpro_current_budget_analisa_asat.koefisien,
                      (simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal,
                      simpro_current_budget_analisa_asat.kode_rap,
                      simpro_current_budget_analisa_asat.keterangan
                    FROM 
                      simpro_current_budget_analisa_asat
                    LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
                    LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali = simpro_current_budget_analisa_asat.tanggal_kendali)
                    LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                    WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_akhir'
                    ORDER BY 
                      simpro_current_budget_analisa_asat.kode_analisa,
                      simpro_tbl_detail_material.detail_material_kode        
                    ASC
                  )
                  UNION ALL 
                  (
                    select 
                    a.parent_kode_analisa as kode_analisa,
                    b.kode_material,
                    c.detail_material_nama,
                    c.detail_material_satuan,
                    coalesce(b.harga,0) as harga,
                    sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                    sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                    b.kode_rap,
                    b.keterangan
                    from simpro_current_budget_analisa_apek a
                    join simpro_current_budget_analisa_asat b
                    on a.id_data_analisa = b.id_data_analisa
                    join simpro_tbl_detail_material c
                    on b.kode_material = c.detail_material_kode
                    where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_akhir'
                    group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                  )   
                  ) AS tbl_analisa_satuan
                  left join simpro_tbl_subbidang
                  on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                  ) c
                  on c.kode_analisa = b.kode_analisa
                  where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_akhir' and a.kode_tree = '$kode'
                  order by a.kode_tree, tahap_tanggal_kendali");
                  
                  if ($get_kode_local->num_rows() == 0) {
                    $client->query("delete from tbl_komposisi_budget where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                    $client->query("delete from tbl_induk_budget where no_spk = '$no_spk' and kode_tahap_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'");
                  } else {
                    $get_current_budget_material_server = json_decode($client->query("select * from tbl_komposisi_budget where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and tahap_tanggal_kendali = '$tgl_rab'"));
                    $data_current_budget_material = $get_current_budget_material_server->{'data'};
                    if ($data_current_budget_material != '') {
                      foreach ($data_current_budget_material as $r_current_budget_material) {
                        $kode_material = $r_current_budget_material->{'detail_material_kode'};

                        $data_current_budget_komposisi_local = $this->db->query("select 
                        (select no_spk from simpro_tbl_proyek where proyek_id = a.id_proyek) as no_spk,
                        c.detail_material_kode,
                        a.kode_tree as tahap_kode_kendali,
                        1 as komposisi_volume_kendali,
                        c.harga as komposisi_harga_satuan_kendali,
                        c.subtotal as komposisi_total_kendali,
                        c.koefisien as komposisi_koefisien_kendali,
                        a.volume * c.koefisien as komposisi_volume_total_kendali,
                        'X' as kode_komposisi_kendali,
                        c.keterangan as keterangan,
                        c.kode_rap,
                        a.tanggal_kendali as tahap_tanggal_kendali
                        from 
                        simpro_current_budget_item_tree a 
                        join simpro_current_budget_analisa_item_apek b 
                        on a.kode_tree = b.kode_tree and a.id_proyek = b.id_proyek and a.tanggal_kendali = b.tanggal_kendali
                        join
                        (
                        SELECT tbl_analisa_satuan.* FROM (
                        (
                          SELECT          
                            simpro_current_budget_analisa_asat.kode_analisa,
                            simpro_tbl_detail_material.detail_material_kode, 
                            simpro_tbl_detail_material.detail_material_nama, 
                            simpro_tbl_detail_material.detail_material_satuan,
                            simpro_current_budget_analisa_asat.harga,
                            simpro_current_budget_analisa_asat.koefisien,
                            (simpro_current_budget_analisa_asat.harga * simpro_current_budget_analisa_asat.koefisien) AS subtotal,
                            simpro_current_budget_analisa_asat.kode_rap,
                            simpro_current_budget_analisa_asat.keterangan
                          FROM 
                            simpro_current_budget_analisa_asat
                          LEFT JOIN simpro_tbl_detail_material ON simpro_tbl_detail_material.detail_material_kode = simpro_current_budget_analisa_asat.kode_material
                          LEFT JOIN simpro_current_budget_analisa_daftar ON (simpro_current_budget_analisa_daftar.kode_analisa = simpro_current_budget_analisa_asat.kode_analisa AND simpro_current_budget_analisa_daftar.id_proyek= simpro_current_budget_analisa_asat.id_proyek AND simpro_current_budget_analisa_daftar.tanggal_kendali = simpro_current_budget_analisa_asat.tanggal_kendali)
                          LEFT JOIN simpro_tbl_satuan ON simpro_tbl_satuan.satuan_id = simpro_current_budget_analisa_daftar.id_satuan
                          WHERE simpro_current_budget_analisa_asat.id_proyek= $proyek_id and simpro_current_budget_analisa_asat.tanggal_kendali = '$tgl_akhir'
                          ORDER BY 
                            simpro_current_budget_analisa_asat.kode_analisa,
                            simpro_tbl_detail_material.detail_material_kode        
                          ASC
                        )
                        UNION ALL 
                        (
                          select 
                          a.parent_kode_analisa as kode_analisa,
                          b.kode_material,
                          c.detail_material_nama,
                          c.detail_material_satuan,
                          coalesce(b.harga,0) as harga,
                          sum(coalesce((a.koefisien * b.koefisien),0))as koefisien,
                          sum(coalesce((coalesce((a.koefisien * b.koefisien),0) * b.harga),0)) as subtotal,
                          b.kode_rap,
                          b.keterangan
                          from simpro_current_budget_analisa_apek a
                          join simpro_current_budget_analisa_asat b
                          on a.id_data_analisa = b.id_data_analisa
                          join simpro_tbl_detail_material c
                          on b.kode_material = c.detail_material_kode
                          where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_akhir'
                          group by kode_material,a.parent_kode_analisa,c.detail_material_nama,c.detail_material_satuan,b.harga,b.kode_rap,b.keterangan
                        )   
                        ) AS tbl_analisa_satuan
                        left join simpro_tbl_subbidang
                        on left(tbl_analisa_satuan.detail_material_kode,3) = simpro_tbl_subbidang.subbidang_kode
                        ) c
                        on c.kode_analisa = b.kode_analisa
                        where a.id_proyek = $proyek_id and a.tanggal_kendali = '$tgl_akhir' and a.kode_tree = '$kode' and c.detail_material_kode = '$kode_material'
                        order by a.kode_tree, tahap_tanggal_kendali");

                        if ($data_current_budget_komposisi_local->num_rows() == 0) {
                          $client->query("delete from tbl_komposisi_budget where no_spk = '$no_spk' and tahap_kode_kendali = '$kode' and detail_material_kode = '$kode_material' and tahap_tanggal_kendali = '$tgl_rab'");
                        }
                      }                
                    }              
                  }
                }
              } 
            } else {
              $client->query("delete from tbl_current_budget where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab'");
              $client->query("delete from tbl_komposisi_budget where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab'");
              $client->query("delete from tbl_induk_budget where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab'");
            }                       
          }
        }

        /////////////// end delete cb /////
      }

      if ($arg == 'mos') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_mos = "SELECT  
        (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as no_spk,
        a.mos_tgl,
        a.detail_material_kode as mos_kode,
        b.detail_material_nama as mos_uraian,
        b.detail_material_satuan as mos_satuan,
        a.mos_total_harsat,
        (a.mos_total_volume * c.harga) as mos_total_jumlah,
        a.mos_total_harsat as mos_diakui_harsat,
        (a.mos_diakui_volume * c.harga) as mos_diakui_jumlah,
        a.mos_belum_volume,
        (a.mos_belum_volume * c.harga) as mos_belum_jumlah,           
        a.mos_keterangan,         
        a.mos_diakui_volume,        
        a.mos_total_volume, 
        1 as mos_pilihan
        FROM 
        simpro_tbl_mos a 
        join simpro_tbl_detail_material b 
        on a.detail_material_kode = b.detail_material_kode
        join (select kode_rap, kode_material, harga, id_proyek from simpro_rap_analisa_asat where id_proyek = $proyek_id group by kode_rap, kode_material, harga, id_proyek) c
        on a.kode_rap = c.kode_rap and a.detail_material_kode = c.kode_material and c.id_proyek = a.proyek_id
        where a.proyek_id = $proyek_id";

        $q_get_mos = $this->db->query($sql_mos);
        if ($q_get_mos->result()) {
          foreach ($q_get_mos->result() as $r_mos) {
            $mos_server = json_decode($client->query("select count('a') as count from tbl_mos where no_spk = '$no_spk' and mos_tgl = '$r_mos->mos_tgl' and mos_kode = '$r_mos->mos_kode'"));
            $data_mos_server = $mos_server->{'data'};
            if ($data_mos_server != '') {
              foreach ($data_mos_server as $dr_mos) {
                if ($dr_mos->count == 0) {
                  $sql_insert_mos = "
                  insert into tbl_mos (
                    no_spk,
                    mos_tgl,
                    mos_kode,
                    mos_uraian,
                    mos_satuan,
                    mos_total_harsat,
                    mos_total_jumlah,
                    mos_diakui_harsat,
                    mos_diakui_jumlah,
                    mos_belum_volume,
                    mos_belum_jumlah,
                    mos_keterangan,
                    mos_diakui_volume,
                    mos_total_volume,
                    mos_pilihan
                  ) values (
                    '$r_mos->no_spk',
                    '$r_mos->mos_tgl',
                    '$r_mos->mos_kode',
                    '$r_mos->mos_uraian',
                    '$r_mos->mos_satuan',
                    $r_mos->mos_total_harsat,
                    $r_mos->mos_total_jumlah,
                    $r_mos->mos_diakui_harsat,
                    $r_mos->mos_diakui_jumlah,
                    $r_mos->mos_belum_volume,
                    $r_mos->mos_belum_jumlah,
                    '$r_mos->mos_keterangan',
                    $r_mos->mos_diakui_volume,
                    $r_mos->mos_total_volume,
                    '$r_mos->mos_pilihan'
                  )
                  ";

                  $client->query($sql_insert_mos);
                } else {
                  $sql_update_mos = "
                  update tbl_mos set
                  mos_uraian = '$r_mos->mos_uraian',
                  mos_satuan = '$r_mos->mos_satuan',
                  mos_total_harsat = $r_mos->mos_total_harsat,
                  mos_total_jumlah = $r_mos->mos_total_jumlah,
                  mos_diakui_harsat  = $r_mos->mos_diakui_harsat,
                  mos_diakui_jumlah  = $r_mos->mos_diakui_jumlah,
                  mos_belum_volume = $r_mos->mos_belum_volume,
                  mos_belum_jumlah = $r_mos->mos_belum_jumlah,
                  mos_keterangan = '$r_mos->mos_keterangan',
                  mos_diakui_volume  = $r_mos->mos_diakui_volume,
                  mos_total_volume = $r_mos->mos_total_volume,
                  mos_pilihan = '$r_mos->mos_pilihan'
                  where
                  no_spk = '$r_mos->no_spk' and 
                  mos_tgl  = '$r_mos->mos_tgl' and 
                  mos_kode = '$r_mos->mos_kode'
                  ";
                  $client->query($sql_update_mos);
                }
              }
            }
          }
        }

        $q_mos_server = json_decode($client->query("select * from tbl_mos where no_spk = '$no_spk'"));
        $d_mos_server = $q_mos_server->{'data'};
        if ($d_mos_server != '') {
          foreach ($d_mos_server as $r_mos_server) {
            $mos_tgl = $r_mos_server->mos_tgl;
            $mos_kode = $r_mos_server->mos_kode;

            $d_mos_local = $this->db->query("select * from simpro_tbl_mos where proyek_id = $proyek_id and detail_material_kode = '$mos_kode' and mos_tgl = '$mos_tgl'");
            if ($d_mos_local->num_rows() == 0) {
              $client->query("delete from tbl_mos where no_spk = '$no_spk' and mos_tgl = '$mos_tgl' and mos_kode = '$mos_kode'");
            }
          }
        }
      }

      if ($arg == 'rencana_kontrak_terkini') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $get_rkk_local = "select
        (select no_spk from simpro_tbl_proyek where proyek_id = simpro_tbl_rencana_kontrak_terkini.proyek_id) as no_spk,
        tahap_tanggal_kendali,
        tahap_kode_kendali,
        tahap_volume_kendali_new as volume_rencana,
        tahap_volume_kendali_kurang as volume_rencana1,
        volume_eskalasi as rencana_volume_eskalasi,
        harga_satuan_eskalasi as rencana_harga_satuan_eskalasi
        from
        simpro_tbl_rencana_kontrak_terkini
        where proyek_id = $proyek_id
        and tahap_volume_kendali_new is not null";

        $q_rkk_local = $this->db->query($get_rkk_local);

        if ($q_rkk_local->result()) {
          foreach ($q_rkk_local->result() as $r_rkk_local) {
            $sql_update_rkk = "
            update tbl_kontrak_terkini set
              volume_rencana = $r_rkk_local->volume_rencana,
              volume_rencana1 = $r_rkk_local->volume_rencana1,
              rencana_volume_eskalasi = $r_rkk_local->rencana_volume_eskalasi,
              rencana_harga_satuan_eskalasi = $r_rkk_local->rencana_harga_satuan_eskalasi
            where
            no_spk = '$r_rkk_local->no_spk' and 
            tahap_tanggal_kendali = '$r_rkk_local->tahap_tanggal_kendali' and 
            tahap_kode_kendali = '$r_rkk_local->tahap_kode_kendali'
            ";

            $client->query($sql_update_rkk);
          }
        }
      }

      if ($arg == 'rpbk') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $get_rpbk_local = "select
        (select no_spk from simpro_tbl_proyek where proyek_id = simpro_tbl_rpbk.proyek_id) as no_spk,
        detail_material_kode,
        tahap_tanggal_kendali,
        volume_rencana_pbk,
        rpbk_rrk1,
        tahap_kode_kendali,
        kode_komposisi_kendali,
        komposisi_harga_satuan_kendali
        from
        simpro_tbl_rpbk
        where proyek_id = $proyek_id";

        $q_rpbk_local = $this->db->query($get_rpbk_local);
        if ($q_rpbk_local->result()) {
          foreach ($q_rpbk_local->result() as $r_rpbk_local) {
            $get_cek_rpbk_server = json_decode($client->query("select count('a') as count from tbl_rpbk where no_spk = '$no_spk' and detail_material_kode = '$r_rpbk_local->detail_material_kode' and tahap_tanggal_kendali = '$r_rpbk_local->tahap_tanggal_kendali'"));
            $data_cek_rpbk_server = $get_cek_rpbk_server->{'data'};
            if ($data_cek_rpbk_server != '') {
              for ($i=0; $i < count($data_cek_rpbk_server); $i++) { 
                if ($data_cek_rpbk_server[$i]->{'count'} == 0) {
                  $sql_insert_rpbk = "
                  insert into tbl_rpbk (
                    no_spk,
                    detail_material_kode,
                    tahap_tanggal_kendali,
                    volume_rencana_pbk,
                    rpbk_rrk1,
                    tahap_kode_kendali,
                    kode_komposisi_kendali,
                    komposisi_harga_satuan_kendali,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$r_rpbk_local->no_spk',
                    '$r_rpbk_local->detail_material_kode',
                    '$r_rpbk_local->tahap_tanggal_kendali',
                    '$r_rpbk_local->volume_rencana_pbk',
                    '$r_rpbk_local->rpbk_rrk1',
                    '$r_rpbk_local->tahap_kode_kendali',
                    '$r_rpbk_local->kode_komposisi_kendali',
                    '$r_rpbk_local->komposisi_harga_satuan_kendali',
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )
                  ";

                  $client->query($sql_insert_rpbk);
                } else {
                  $sql_update_rpbk = "
                  update tbl_rpbk set
                    volume_rencana_pbk  = '$r_rpbk_local->volume_rencana_pbk',
                    rpbk_rrk1 = '$r_rpbk_local->rpbk_rrk1',
                    tahap_kode_kendali  = '$r_rpbk_local->tahap_kode_kendali',
                    kode_komposisi_kendali  = '$r_rpbk_local->kode_komposisi_kendali',
                    komposisi_harga_satuan_kendali  = '$r_rpbk_local->komposisi_harga_satuan_kendali',
                    user_update = '$uname_update',
                    tgl_update  = '$tgl_update',
                    ip_update = '$ip_update',
                    divisi_update = '$divisi_update',
                    waktu_update  = '$waktu_update'
                  where
                    no_spk  = '$r_rpbk_local->no_spk' and 
                    detail_material_kode  = '$r_rpbk_local->detail_material_kode' and 
                    tahap_tanggal_kendali = '$r_rpbk_local->tahap_tanggal_kendali'
                  ";

                  $client->query($sql_update_rpbk);
                }
              }
            }
          }
        }

        $get_rpbk_server = json_decode($client->query("select * from tbl_rpbk where no_spk = '$no_spk'"));
        $data_rpbk_server = $get_rpbk_server->{'data'};
        if ($data_rpbk_server != '') {
          for ($i=0; $i < count($data_rpbk_server); $i++) { 
            $tanggal = $data_rpbk_server[$i]->{'tahap_tanggal_kendali'};
            $kode_material = $data_rpbk_server[$i]->{'detail_material_kode'};

            $get_cek_rpbk_local = $this->db->query("select * from simpro_tbl_rpbk where proyek_id = $proyek_id and detail_material_kode = '$kode_material' and tahap_tanggal_kendali = '$tanggal'");
            if ($get_cek_rpbk_local->num_rows() == 0) {
              $client->query("delete from tbl_rpbk where no_spk = '$no_spk' and detail_material_kode = '$kode_material' and tahap_tanggal_kendali = '$tanggal'");
            }
          }
        }
      }

      if ($arg == 'schedule_kerja') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $get_sch_proyek = "with x as
        (select
        (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as time_spk,
        (extract('year' from tgl_sch_parent)::text || '-' || extract('month' from tgl_sch_parent)::text || '-01')::date as time_tgl,
        sum(bobot_parent) as time_progres
        from
        simpro_tbl_sch_proyek a
        join simpro_tbl_sch_proyek_parent b
        on a.tahap_kendali_id = b.tahap_kendali_id
        where a.proyek_id = $proyek_id
        group by extract('month' from tgl_sch_parent), extract('year' from tgl_sch_parent), time_spk)

        select 
        x.time_tgl,
        y.time_spk,
        x.time_progres,
        extract(epoch from (x.time_tgl - interval '1 month')) as time_progres_timestamp,
        sum(y.time_progres) as time_total_progress 
        from (select
        (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as time_spk,
        (extract('year' from tgl_sch_parent)::text || '-' || extract('month' from tgl_sch_parent)::text || '-01')::date as time_tgl,
        sum(bobot_parent) as time_progres
        from
        simpro_tbl_sch_proyek a
        join simpro_tbl_sch_proyek_parent b
        on a.tahap_kendali_id = b.tahap_kendali_id
        where a.proyek_id = $proyek_id
        group by extract('month' from tgl_sch_parent), extract('year' from tgl_sch_parent), time_spk) y
        join x on y.time_tgl <= x.time_tgl 
        group by x.time_tgl,y.time_spk,x.time_progres";

        $q_sch_proyek = $this->db->query($get_sch_proyek);
        if ($q_sch_proyek->result()) {
          foreach ($q_sch_proyek->result() as $r_sch_proyek_local) {
            $get_cek_sch_proyek_server = json_decode($client->query("select count('a') as count from tbl_time_schedule where time_spk = '$r_sch_proyek_local->time_spk' and time_tgl = '$r_sch_proyek_local->time_tgl'"));
            $data_cek_sch_proyek_server = $get_cek_sch_proyek_server->{'data'};
            if ($data_cek_sch_proyek_server != '') {
              for ($i=0; $i < count($data_cek_sch_proyek_server); $i++) { 
                if ($data_cek_sch_proyek_server[$i]->{'count'} == 0) {
                  $sql_insert_sch = "
                  insert into tbl_time_schedule (
                    time_spk,
                    time_tgl,
                    time_progres,
                    time_progres_timestamp,
                    time_total_progress
                  ) values (
                    '$r_sch_proyek_local->time_spk',
                    '$r_sch_proyek_local->time_tgl',
                    $r_sch_proyek_local->time_progres,
                    $r_sch_proyek_local->time_progres_timestamp,
                    $r_sch_proyek_local->time_total_progress
                  )";

                  $client->query($sql_insert_sch);
                } else {
                  $sql_update_sch = "
                  update tbl_time_schedule set
                    time_progres  = $r_sch_proyek_local->time_progres,
                    time_progres_timestamp  = $r_sch_proyek_local->time_progres_timestamp,
                    time_total_progress  = $r_sch_proyek_local->time_total_progress
                  where
                    time_spk  = '$r_sch_proyek_local->time_spk' and
                    time_tgl  = '$r_sch_proyek_local->time_tgl'
                  ";

                  $client->query($sql_update_sch);
                }
              }
            }
          }
        }

        $get_sch_server = json_decode($client->query("select * from tbl_time_schedule where time_spk = '$no_spk'"));
        $data_sch_server = $get_sch_server->{'data'};
        if ($data_sch_server != '') {
          for ($i=0; $i < count($data_sch_server); $i++) { 
            $time_tgl = $data_sch_server[$i]->{'time_tgl'};

            $sql_get_cek_sch_local = "with x as
            (select
            (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as time_spk,
            (extract('year' from tgl_sch_parent)::text || '-' || extract('month' from tgl_sch_parent)::text || '-01')::date as time_tgl,
            sum(bobot_parent) as time_progres
            from
            simpro_tbl_sch_proyek a
            join simpro_tbl_sch_proyek_parent b
            on a.tahap_kendali_id = b.tahap_kendali_id
            where a.proyek_id = $proyek_id
            group by extract('month' from tgl_sch_parent), extract('year' from tgl_sch_parent), time_spk)

            select 
            x.time_tgl,
            y.time_spk,
            x.time_progres,
            extract(epoch from (x.time_tgl - interval '1 month')) as time_progres_timestamp,
            sum(y.time_progres) as time_total_progress 
            from (select
            (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as time_spk,
            (extract('year' from tgl_sch_parent)::text || '-' || extract('month' from tgl_sch_parent)::text || '-01')::date as time_tgl,
            sum(bobot_parent) as time_progres
            from
            simpro_tbl_sch_proyek a
            join simpro_tbl_sch_proyek_parent b
            on a.tahap_kendali_id = b.tahap_kendali_id
            where a.proyek_id = $proyek_id
            group by extract('month' from tgl_sch_parent), extract('year' from tgl_sch_parent), time_spk) y
            join x on y.time_tgl <= x.time_tgl 
            where x.time_tgl = '$time_tgl'
            group by x.time_tgl,y.time_spk,x.time_progres";

            $get_cek_sch_local = $this->db->query($sql_get_cek_sch_local);
            if ($get_cek_sch_local->num_rows() == 0) {
              $client->query("delete from tbl_time_schedule where time_spk = '$no_spk' and time_tgl = '$time_tgl'");
            }
          }
        }
      }

      if ($arg == 'proses_pelaporan') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);


        $sql_get_po2 = "
        select
        (select no_spk from simpro_tbl_proyek where proyek_id = simpro_tbl_po2.proyek_id) as no_spk,
        tahap_tanggal_kendali as tanggal,
        detail_material_kode,
        detail_material_nama,
        detail_material_satuan,
        volume_ob,
        harga_sat_ob,
        jumlah_ob,
        volume_cash_td,
        jumlah_cash_td,
        volume_hutang,
        jumlah_hutang,
        volume_hp,
        jumlah_hp,
        volume_cost_td,
        jumlah_cost_td,
        volume_cost_tg,
        hargasat_cost_tg,
        jumlah_cost_tg,
        volume_cf,
        jumlah_cf,
        trend,
        volume_tot_hutang,
        total_hutang,
        vol_cash_hutang,
        jum_cash_hutang,
        volume_cb,
        hargasat_cb,
        jumlah_cb,
        coalesce(uraian,'') as uraian,
        coalesce(volume_rencana,0) as volume_rencana,
        coalesce(total_volume_rencana,0) as total_volume_rencana,
        kode_rap,
        jlh_tambah,
        jlh_kurang
        from
        simpro_tbl_po2 
        where 
        proyek_id = $proyek_id
        ";

        $q_get_po2 = $this->db->query($sql_get_po2);
        if ($q_get_po2->result()) {
          foreach ($q_get_po2->result() as $r_po2) {
            $q_get_po2_server = json_decode($client->query("select count('a') as count from tbl_po2 where no_spk = '$no_spk' and tanggal = '$r_po2->tanggal' and detail_material_kode = '$r_po2->detail_material_kode'"));
            $data_po2_server = $q_get_po2_server->{'data'};
            if ($data_po2_server != '') {
              for ($i=0; $i < count($data_po2_server); $i++) { 
                if ($data_po2_server[$i]->count == 0) {
                  $sql_insert_po2 = "
                  insert into tbl_po2 (
                    no_spk,
                    tanggal,
                    detail_material_kode,
                    detail_material_nama,
                    detail_material_satuan,
                    volume_ob,
                    harga_sat_ob,
                    jumlah_ob,
                    volume_cash_td,
                    jumlah_cash_td,
                    volume_hutang,
                    jumlah_hutang,
                    volume_hp,
                    jumlah_hp,
                    volume_cost_td,
                    jumlah_cost_td,
                    volume_cost_tg,
                    hargasat_cost_tg,
                    jumlah_cost_tg,
                    volume_cf,
                    jumlah_cf,
                    trend,
                    volume_tot_hutang,
                    total_hutang,
                    vol_cash_hutang,
                    jum_cash_hutang,
                    volume_cb,
                    hargasat_cb,
                    jumlah_cb,
                    uraian,
                    volume_rencana,
                    total_volume_rencana,
                    kode_rap,
                    jlh_tambah,
                    jlh_kurang
                  ) values (
                    '$r_po2->no_spk',
                    '$r_po2->tanggal',
                    '$r_po2->detail_material_kode',
                    '$r_po2->detail_material_nama',
                    '$r_po2->detail_material_satuan',
                    $r_po2->volume_ob,
                    $r_po2->harga_sat_ob,
                    $r_po2->jumlah_ob,
                    $r_po2->volume_cash_td,
                    $r_po2->jumlah_cash_td,
                    $r_po2->volume_hutang,
                    $r_po2->jumlah_hutang,
                    $r_po2->volume_hp,
                    $r_po2->jumlah_hp,
                    $r_po2->volume_cost_td,
                    $r_po2->jumlah_cost_td,
                    $r_po2->volume_cost_tg,
                    $r_po2->hargasat_cost_tg,
                    $r_po2->jumlah_cost_tg,
                    $r_po2->volume_cf,
                    $r_po2->jumlah_cf,
                    $r_po2->trend,
                    $r_po2->volume_tot_hutang,
                    $r_po2->total_hutang,
                    $r_po2->vol_cash_hutang,
                    $r_po2->jum_cash_hutang,
                    $r_po2->volume_cb,
                    $r_po2->hargasat_cb,
                    $r_po2->jumlah_cb,
                    '$r_po2->uraian',
                    $r_po2->volume_rencana,
                    $r_po2->total_volume_rencana,
                    '$r_po2->kode_rap',
                    $r_po2->jlh_tambah,
                    $r_po2->jlh_kurang
                  )
                  ";

                  $client->query($sql_insert_po2);
                } else {
                  $sql_update_po2 = "
                  update tbl_po2 set
                    detail_material_nama  = '$r_po2->detail_material_nama',
                    detail_material_satuan  = '$r_po2->detail_material_satuan',
                    volume_ob = $r_po2->volume_ob,
                    harga_sat_ob  = $r_po2->harga_sat_ob,
                    jumlah_ob = $r_po2->jumlah_ob,
                    volume_cash_td  = $r_po2->volume_cash_td,
                    jumlah_cash_td  = $r_po2->jumlah_cash_td,
                    volume_hutang = $r_po2->volume_hutang,
                    jumlah_hutang = $r_po2->jumlah_hutang,
                    volume_hp = $r_po2->volume_hp,
                    jumlah_hp = $r_po2->jumlah_hp,
                    volume_cost_td  = $r_po2->volume_cost_td,
                    jumlah_cost_td  = $r_po2->jumlah_cost_td,
                    volume_cost_tg  = $r_po2->volume_cost_tg,
                    hargasat_cost_tg  = $r_po2->hargasat_cost_tg,
                    jumlah_cost_tg  = $r_po2->jumlah_cost_tg,
                    volume_cf = $r_po2->volume_cf,
                    jumlah_cf = $r_po2->jumlah_cf,
                    trend = $r_po2->trend,
                    volume_tot_hutang = $r_po2->volume_tot_hutang,
                    total_hutang  = $r_po2->total_hutang,
                    vol_cash_hutang = $r_po2->vol_cash_hutang,
                    jum_cash_hutang = $r_po2->jum_cash_hutang,
                    volume_cb = $r_po2->volume_cb,
                    hargasat_cb = $r_po2->hargasat_cb,
                    jumlah_cb = $r_po2->jumlah_cb,
                    uraian  = '$r_po2->uraian',
                    volume_rencana  = $r_po2->volume_rencana,
                    total_volume_rencana  = $r_po2->total_volume_rencana,
                    kode_rap  = '$r_po2->kode_rap',
                    jlh_tambah  = $r_po2->jlh_tambah,
                    jlh_kurang = $r_po2->jlh_kurang
                  where
                    no_spk  = '$r_po2->no_spk' and
                    tanggal = '$r_po2->tanggal' and
                    detail_material_kode  = '$r_po2->detail_material_kode'                  
                  ";

                  $client->query($sql_update_po2);
                }
              }
            }
          }
        }

        $get_po2_server = json_decode($client->query("select * from tbl_po2 where no_spk = '$no_spk'"));
        $data_po2_server = $get_po2_server->{'data'};
        if ($data_po2_server != '') {
          for ($i=0; $i < count($data_po2_server) ; $i++) { 
            $kode = $data_po2_server[$i]->{'detail_material_kode'};
            $tanggal = $data_po2_server[$i]->{'tanggal'};

            $data_po2_local = $this->db->query("select * from simpro_tbl_po2 where proyek_id = $proyek_id and tahap_tanggal_kendali = '$tanggal' and detail_material_kode = '$kode'");
            if ($data_po2_local->num_rows() == 0) {
              $client->query("delete from tbl_po2 where no_spk = '$no_spk' and tanggal = '$tanggal' and detail_material_kode = '$kode'");
            }
          }
        }
      }

      if ($arg == 'kkp') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $get_kkp_local = "select
        (select no_spk from simpro_tbl_proyek where proyek_id = simpro_tbl_kkp.proyek_id) as no_spk,
        kkp_uraian,
        kkp_tempat,
        kkp_rencana,
        kkp_tgl,
        jabatan as kkp_pelaku
        from
        simpro_tbl_kkp
        where proyek_id = $proyek_id";

        $q_kkp_local = $this->db->query($get_kkp_local);
        if ($q_kkp_local->result()) {
          foreach ($q_kkp_local->result() as $r_kkp_local) {
            $get_cek_kkp_server = json_decode($client->query("select count('a') as count from tbl_kkp where no_spk = '$no_spk' and kkp_tgl = '$r_kkp_local->kkp_tgl' and kkp_uraian = '$r_kkp_local->kkp_uraian'"));
            $data_cek_kkp_server = $get_cek_kkp_server->{'data'};
            if ($data_cek_kkp_server != '') {
              for ($i=0; $i < count($data_cek_kkp_server); $i++) { 
                if ($data_cek_kkp_server[$i]->{'count'} == 0) {
                  $sql_insert_kkp = "
                  insert into tbl_kkp (
                    no_spk,
                    kkp_uraian,
                    kkp_tempat,
                    kkp_rencana,
                    kkp_tgl,
                    kkp_pelaku
                  ) values (
                    '$r_kkp_local->no_spk',
                    '$r_kkp_local->kkp_uraian',
                    '$r_kkp_local->kkp_tempat',
                    '$r_kkp_local->kkp_rencana',
                    '$r_kkp_local->kkp_tgl',
                    '$r_kkp_local->kkp_pelaku'
                  )
                  ";

                  $client->query($sql_insert_kkp);
                } else {
                  $sql_update_kkp = "
                  update tbl_kkp set
                    kkp_tempat  = '$r_kkp_local->kkp_tempat',
                    kkp_rencana = '$r_kkp_local->kkp_rencana',
                    kkp_pelaku = '$r_kkp_local->kkp_pelaku'
                  where
                    no_spk  = '$r_kkp_local->no_spk' and 
                    kkp_uraian  = '$r_kkp_local->kkp_uraian' and 
                    kkp_tgl = '$r_kkp_local->kkp_tgl'                  
                  ";

                  $client->query($sql_update_kkp);
                }
              }
            }
          }
        }

        $get_kkp_server = json_decode($client->query("select * from tbl_kkp where no_spk = '$no_spk'"));
        $data_kkp_server = $get_kkp_server->{'data'};
        if ($data_kkp_server != '') {
          for ($i=0; $i < count($data_kkp_server); $i++) { 
            $tanggal = $data_kkp_server[$i]->kkp_tgl;
            $uraian = $data_kkp_server[$i]->kkp_uraian;

            $get_cek_kkp_local = $this->db->query("select * from simpro_tbl_kkp where proyek_id = $proyek_id and kkp_tgl = '$tanggal' and kkp_uraian = '$uraian'");
            if ($get_cek_kkp_local->num_rows() == 0) {
              $client->query("delete from tbl_kkp where no_spk = '$no_spk' and kkp_tgl = '$tanggal' and kkp_uraian = '$uraian'");
            }
          }
        }
      }

      if ($arg == 'cashflow') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $get_cashflow_local = "select 
        ket_id::text as id,
        (select no_spk from simpro_tbl_proyek where proyek_id = simpro_tbl_cashin.proyek_id) as no_spk,
        uraian,
        tahap_tanggal_kendali,
        realisasi,
        rproyeksi1,
        rproyeksi2,
        rproyeksi3,
        rproyeksi4,
        rproyeksi5,
        rproyeksi6,
        curentbuget,
        sbp,
        spp
        from simpro_tbl_cashin where proyek_id = $proyek_id";

        $q_cashflow_local = $this->db->query($get_cashflow_local);
        if ($q_cashflow_local->result()) {
          foreach ($q_cashflow_local->result() as $r_cashflow_local) {
            $get_cek_cashflow_server = json_decode($client->query("select count('a') as count from tbl_cashin where no_spk = '$no_spk' and tahap_tanggal_kendali = '$r_cashflow_local->tahap_tanggal_kendali' and id = '$r_cashflow_local->id'"));
            $data_cek_cashflow_server = $get_cek_cashflow_server->{'data'};
            if ($data_cek_cashflow_server != '') {
              for ($i=0; $i < count($data_cek_cashflow_server); $i++) { 
                if ($data_cek_cashflow_server[$i]->{'count'} == 0) {
                  $sql_insert_cashflow = "
                  insert into tbl_cashin (
                    id,
                    no_spk,
                    uraian,
                    tahap_tanggal_kendali,
                    realisasi,
                    rproyeksi1,
                    rproyeksi2,
                    rproyeksi3,
                    rproyeksi4,
                    rproyeksi5,
                    rproyeksi6,
                    curentbuget,
                    sbp,
                    spp,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$r_cashflow_local->id',
                    '$r_cashflow_local->no_spk',
                    '$r_cashflow_local->uraian',
                    '$r_cashflow_local->tahap_tanggal_kendali',
                    $r_cashflow_local->realisasi,
                    $r_cashflow_local->rproyeksi1,
                    $r_cashflow_local->rproyeksi2,
                    $r_cashflow_local->rproyeksi3,
                    $r_cashflow_local->rproyeksi4,
                    $r_cashflow_local->rproyeksi5,
                    $r_cashflow_local->rproyeksi6,
                    $r_cashflow_local->curentbuget,
                    $r_cashflow_local->sbp,
                    $r_cashflow_local->spp,
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )
                  ";

                  $client->query($sql_insert_cashflow);
                } else {
                  $sql_update_cashflow = "
                  update tbl_cashin set
                    uraian  = '$r_cashflow_local->uraian',
                    realisasi = $r_cashflow_local->realisasi,
                    rproyeksi1  = $r_cashflow_local->rproyeksi1,
                    rproyeksi2  = $r_cashflow_local->rproyeksi2,
                    rproyeksi3  = $r_cashflow_local->rproyeksi3,
                    rproyeksi4  = $r_cashflow_local->rproyeksi4,
                    rproyeksi5  = $r_cashflow_local->rproyeksi5,
                    rproyeksi6  = $r_cashflow_local->rproyeksi6,
                    curentbuget = $r_cashflow_local->curentbuget,
                    sbp = $r_cashflow_local->sbp,
                    spp = $r_cashflow_local->spp,
                    user_update = '$uname_update',
                    tgl_update  = '$tgl_update',
                    ip_update = '$ip_update',
                    divisi_update = '$divisi_update',
                    waktu_update = '$waktu_update'
                  where
                    id  = '$r_cashflow_local->id' and 
                    tahap_tanggal_kendali = '$r_cashflow_local->tahap_tanggal_kendali' and 
                    no_spk  = '$r_cashflow_local->no_spk'
                  ";

                  $client->query($sql_update_cashflow);
                }
              }
            }
          }
        }

        $get_cashflow_server = json_decode($client->query("select * from tbl_cashin where no_spk = '$no_spk'"));
        $data_cashflow_server = $get_cashflow_server->{'data'};
        if ($data_cashflow_server != '') {
          for ($i=0; $i < count($data_cashflow_server); $i++) { 
            $id_var = $data_cashflow_server[$i]->id; 
            $id = intval($data_cashflow_server[$i]->id); 
            $tanggal = $data_cashflow_server[$i]->tahap_tanggal_kendali;

            $get_cek_cashflow_local = $this->db->query("select * from simpro_tbl_cashin where proyek_id = $proyek_id and ket_id = $id and tahap_tanggal_kendali = '$tanggal'");
            if ($get_cek_cashflow_local->num_rows() == 0) {
              $client->query("delete from tbl_cashin where no_spk = '$no_spk' and id = '$id_var' and tahap_tanggal_kendali = '$tanggal'");
            }
          }
        }
      }

      if ($arg == 'daftar_alat') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $get_daftar_alat_local = "select 
        (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as no_spk,
        a.tgl,
        (select user_name from simpro_tbl_user where user_id = a.user_id) as user_tambah,
        a.keterangan,
        b.id_alat as alat_id,
        (a.status_kepemilikan - 1) as status_kepemilikan,
        (a.kondisi - 1) as kondisi,
        (a.status_operasi - 1) as status_operasi
        from simpro_tbl_daftar_peralatan a
        join simpro_tbl_master_peralatan b on a.master_peralatan_id = b.master_peralatan_id
        where a.proyek_id = $proyek_id";

        $q_get_daftar_alat = $this->db->query($get_daftar_alat_local);
        if ($q_get_daftar_alat->result()) {
          foreach ($q_get_daftar_alat->result() as $r_daftar_alat_local) {
            $get_cek_daftar_alat_server = json_decode($client->query("select count('a') as count from tbl_daftar_peralatan where no_spk = '$no_spk' and tgl = '$r_daftar_alat_local->tgl' and alat_id = '$r_daftar_alat_local->alat_id'"));
            $data_cek_daftar_alat_server = $get_cek_daftar_alat_server->{'data'};
            if ($data_cek_daftar_alat_server != '') {
              for ($i=0; $i < count($data_cek_daftar_alat_server); $i++) { 
                if ($data_cek_daftar_alat_server[$i]->count == 0) {
                  $sql_insert_daftar_alat = "
                  insert into tbl_daftar_peralatan (
                    no_spk,
                    tgl,
                    user_tambah,
                    keterangan,
                    alat_id,
                    status_kepemilikan,
                    kondisi,
                    status_operasi
                  ) values (
                    '$r_daftar_alat_local->no_spk',
                    '$r_daftar_alat_local->tgl',
                    '$r_daftar_alat_local->user_tambah',
                    '$r_daftar_alat_local->keterangan',
                    $r_daftar_alat_local->alat_id,
                    $r_daftar_alat_local->status_kepemilikan,
                    $r_daftar_alat_local->kondisi,
                    $r_daftar_alat_local->status_operasi
                  )
                  ";

                  $client->query($sql_insert_daftar_alat);
                } else {
                  $sql_update_daftar_alat = "
                  update tbl_daftar_peralatan set
                    user_tambah = '$r_daftar_alat_local->user_tambah',
                    keterangan  = '$r_daftar_alat_local->keterangan',
                    status_kepemilikan  = $r_daftar_alat_local->status_kepemilikan,
                    kondisi = $r_daftar_alat_local->kondisi,
                    status_operasi = $r_daftar_alat_local->status_operasi
                  where
                    no_spk  = '$r_daftar_alat_local->no_spk' and
                    tgl = '$r_daftar_alat_local->tgl' and
                    alat_id = $r_daftar_alat_local->alat_id
                  ";

                  $client->query($sql_update_daftar_alat);
                }
              }
            }
          }
        }

        $get_daftar_alat_server = json_decode($client->query("select * from tbl_daftar_peralatan where no_spk = '$no_spk'"));
        $data_daftar_alat_server = $get_daftar_alat_server->{'data'};
        if ($data_daftar_alat_server != '') {
          for ($i=0; $i < count($data_daftar_alat_server); $i++) { 
            $tanggal = $data_daftar_alat_server[$i]->{'tgl'};
            $alat_id = $data_daftar_alat_server[$i]->{'alat_id'};

            $get_cek_daftar_alat_local = $this->db->query("select * from simpro_tbl_daftar_peralatan a join simpro_tbl_master_peralatan b on a.master_peralatan_id = b.master_peralatan_id where a.proyek_id = $proyek_id and a.tgl = '$tanggal' and b.id_alat = $alat_id");
            if ($get_cek_daftar_alat_local->num_rows() == 0) {
              $client->query("delete from tbl_daftar_peralatan where no_spk = '$no_spk' and alat_id = $alat_id and tgl = '$tanggal'");
            }
          }
        }
      }

      if ($arg == 'pilih_toko') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $get_pilih_toko_local = "
        select
        (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as no_spk,
        '$divisi_kode_proyek' AS divisi_kode,
        b.toko_kode,
        b.toko_nama
        from
        simpro_tbl_pilih_toko a
        join simpro_tbl_toko b
        on a.toko_id = b.toko_id
        where a.proyek_id = $proyek_id"; /// (select * from unnest(string_to_array(b.toko_kode, '-')) limit 1)

        $q_pilih_toko_local = $this->db->query($get_pilih_toko_local);
        if ($q_pilih_toko_local->result()) {
          foreach ($q_pilih_toko_local->result() as $r_pilih_toko_local) {
            $get_cek_pilih_toko_server = json_decode($client->query("select count('a') as count from tbl_pilih_toko where no_spk = '$no_spk' and toko_kode = '$r_pilih_toko_local->toko_kode'"));
            $data_cek_pilih_toko = $get_cek_pilih_toko_server->{'data'};
            if ($data_cek_pilih_toko != '') {
              for ($i=0; $i < count($data_cek_pilih_toko); $i++) {
                if ($data_cek_pilih_toko[$i]->{'count'} == 0) {
                  $sql_insert_pilih_toko = "
                  insert into tbl_pilih_toko (
                    no_spk,
                    divisi_kode,
                    toko_kode,
                    toko_nama,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$r_pilih_toko_local->no_spk',
                    '$r_pilih_toko_local->divisi_kode',
                    '$r_pilih_toko_local->toko_kode',
                    '$r_pilih_toko_local->toko_nama',
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )
                  ";

                  $client->query($sql_insert_pilih_toko);
                }
              }
            }
          }
        }

        $q_get_pilih_toko_server = json_decode($client->query("select * from tbl_pilih_toko where no_spk = '$no_spk'"));
        $data_pilih_toko_server = $q_get_pilih_toko_server->{'data'};
        if ($data_pilih_toko_server != '') {
          for ($i=0; $i < count($data_pilih_toko_server); $i++) { 
            $kode_toko = $data_pilih_toko_server[$i]->toko_kode;

            $get_cek_pilih_toko_local = $this->db->query("select * from simpro_tbl_pilih_toko a join simpro_tbl_toko b on a.toko_id = b.toko_id where a.proyek_id = '$proyek_id' and b.toko_kode = '$kode_toko'");
            if ($get_cek_pilih_toko_local->num_rows() == 0) {
              $client->query("delete from tbl_pilih_toko where no_spk = '$no_spk' and toko_kode = '$kode_toko'");
            }
          }
        }
      }

      if ($arg == 'cashtodate') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_ctd_l = "select 
          a.no_bukti,
          e.no_spk,
          a.detail_material_kode,
          a.uraian,
          a.volume,
          a.jumlah,
          a.tanggal,
          d.detail_material_nama,
          d.detail_material_satuan,
          c.toko_kode as kode_toko,
          c.toko_nama as nama_toko,
          a.pilihan::text
          from simpro_tbl_cashtodate a
          join simpro_tbl_pilih_toko b
          on a.pilih_toko_id = b.pilih_toko_id
          join simpro_tbl_toko c
          on b.toko_id = c.toko_id
          join simpro_tbl_detail_material d
          on a.detail_material_kode = d.detail_material_kode
          join simpro_tbl_proyek e
          on a.proyek_id = e.proyek_id
          where a.proyek_id = $proyek_id";

        $q_ctd = $this->db->query($sql_get_ctd_l);
        if ($q_ctd->result()) {
          foreach ($q_ctd->result() as $r_ctd_l) {
            $d_ctd_s = json_decode($client->query("select count('a') as count from tbl_cashtodate where no_spk = '$r_ctd_l->no_spk' and no_bukti = '$r_ctd_l->no_bukti' and detail_material_kode = '$r_ctd_l->detail_material_kode' and kode_toko = '$r_ctd_l->kode_toko' and pilihan = '$r_ctd_l->pilihan'"))->{'data'};
            if ($d_ctd_s != '') {
              for ($i=0; $i < count($d_ctd_s); $i++) { 
                if ($d_ctd_s[$i]->{'count'} == 0) {
                  $sql_insert_ctd = "
                  insert into tbl_cashtodate (
                    no_bukti,
                    no_spk,
                    detail_material_kode,
                    uraian,
                    volume,
                    jumlah,
                    tanggal,
                    detail_material_nama,
                    detail_material_satuan,
                    kode_toko,
                    nama_toko,
                    pilihan
                  ) values (
                    '$r_ctd_l->no_bukti',
                    '$r_ctd_l->no_spk',
                    '$r_ctd_l->detail_material_kode',
                    '$r_ctd_l->uraian',
                    $r_ctd_l->volume,
                    $r_ctd_l->jumlah,
                    '$r_ctd_l->tanggal',
                    '$r_ctd_l->detail_material_nama',
                    '$r_ctd_l->detail_material_satuan',
                    '$r_ctd_l->kode_toko',
                    '$r_ctd_l->nama_toko',
                    '$r_ctd_l->pilihan'
                  )
                  ";

                  $client->query($sql_insert_ctd);
                } else {
                  $sql_update_ctd = "
                  update tbl_cashtodate set
                    uraian  = '$r_ctd_l->uraian',
                    volume  = $r_ctd_l->volume,
                    jumlah  = $r_ctd_l->jumlah,
                    tanggal = '$r_ctd_l->tanggal',
                    detail_material_nama  = '$r_ctd_l->detail_material_nama',
                    detail_material_satuan  = '$r_ctd_l->detail_material_satuan',
                    nama_toko = '$r_ctd_l->nama_toko'
                  where
                    no_bukti  = '$r_ctd_l->no_bukti' and
                    no_spk  = '$r_ctd_l->no_spk' and
                    detail_material_kode  = '$r_ctd_l->detail_material_kode' and
                    kode_toko = '$r_ctd_l->kode_toko' and
                    pilihan = '$r_ctd_l->pilihan'
                  ";

                  $client->query($sql_update_ctd);
                }
              }
            }
          }
        }

        $q_ctd_s = json_decode($client->query("select *,coalesce(pilihan::int,0) as pilihan_int from tbl_cashtodate where no_spk = '$no_spk'"))->{'data'};
        if ($q_ctd_s != '') {
          for ($i=0; $i < count($q_ctd_s); $i++) { 
            $kode = $q_ctd_s[$i]->{'detail_material_kode'};
            $no_bukti = $q_ctd_s[$i]->{'no_bukti'};
            $toko = $q_ctd_s[$i]->{'kode_toko'};
            $pilihan_int = $q_ctd_s[$i]->{'pilihan_int'};
            $pilihan = $q_ctd_s[$i]->{'pilihan'};

            $sql_get_cek_ctd_l = "
            select 
            a.no_bukti,
            e.no_spk,
            a.detail_material_kode,
            a.uraian,
            a.volume,
            a.jumlah,
            a.tanggal,
            d.detail_material_nama,
            d.detail_material_satuan,
            c.toko_kode as kode_toko,
            c.toko_nama as nama_toko,
            a.pilihan
            from simpro_tbl_cashtodate a
            join simpro_tbl_pilih_toko b
            on a.pilih_toko_id = b.pilih_toko_id
            join simpro_tbl_toko c
            on b.toko_id = c.toko_id
            join simpro_tbl_detail_material d
            on a.detail_material_kode = d.detail_material_kode
            join simpro_tbl_proyek e
            on a.proyek_id = e.proyek_id
            where a.proyek_id = $proyek_id
            and a.detail_material_kode = '$kode'
            and a.no_bukti = '$no_bukti'
            and c.toko_kode = '$toko'
            and a.pilihan = $pilihan_int
            ";

            $q_get_cek_ctd = $this->db->query($sql_get_cek_ctd_l);
            if ($q_get_cek_ctd->num_rows() == 0) {
              $client->query("delete from tbl_cashtodate where
                no_spk = '$no_spk' and
                detail_material_kode = '$kode' and
                no_bukti = '$no_bukti' and
                kode_toko = '$toko' and
                pilihan = '$pilihan'
              ");
            }
          }
        }
      }

      if ($arg == 'hutang') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_ctd_l = "select 
          a.no_bukti,
          e.no_spk,
          a.detail_material_kode,
          a.uraian,
          a.volume,
          a.jumlah,
          a.tanggal,
          d.detail_material_nama,
          d.detail_material_satuan,
          c.toko_kode as kode_toko,
          c.toko_nama as nama_toko,
          a.pilihan::text
          from simpro_tbl_hutangonkeu a
          join simpro_tbl_pilih_toko b
          on a.pilih_toko_id = b.pilih_toko_id
          join simpro_tbl_toko c
          on b.toko_id = c.toko_id
          join simpro_tbl_detail_material d
          on a.detail_material_kode = d.detail_material_kode
          join simpro_tbl_proyek e
          on a.proyek_id = e.proyek_id
          where a.proyek_id = $proyek_id";

        $q_ctd = $this->db->query($sql_get_ctd_l);
        if ($q_ctd->result()) {
          foreach ($q_ctd->result() as $r_ctd_l) {
            $d_ctd_s = json_decode($client->query("select count('a') as count from tbl_hutangonkeu where no_spk = '$r_ctd_l->no_spk' and no_bukti = '$r_ctd_l->no_bukti' and detail_material_kode = '$r_ctd_l->detail_material_kode' and kode_toko = '$r_ctd_l->kode_toko' and pilihan = '$r_ctd_l->pilihan'"))->{'data'};
            if ($d_ctd_s != '') {
              for ($i=0; $i < count($d_ctd_s); $i++) { 
                if ($d_ctd_s[$i]->{'count'} == 0) {
                  $sql_insert_ctd = "
                  insert into tbl_hutangonkeu (
                    no_bukti,
                    no_spk,
                    detail_material_kode,
                    uraian,
                    volume,
                    jumlah,
                    tanggal,
                    detail_material_nama,
                    detail_material_satuan,
                    kode_toko,
                    nama_toko,
                    pilihan
                  ) values (
                    '$r_ctd_l->no_bukti',
                    '$r_ctd_l->no_spk',
                    '$r_ctd_l->detail_material_kode',
                    '$r_ctd_l->uraian',
                    $r_ctd_l->volume,
                    $r_ctd_l->jumlah,
                    '$r_ctd_l->tanggal',
                    '$r_ctd_l->detail_material_nama',
                    '$r_ctd_l->detail_material_satuan',
                    '$r_ctd_l->kode_toko',
                    '$r_ctd_l->nama_toko',
                    '$r_ctd_l->pilihan'
                  )
                  ";

                  $client->query($sql_insert_ctd);
                } else {
                  $sql_update_ctd = "
                  update tbl_hutangonkeu set
                    uraian  = '$r_ctd_l->uraian',
                    volume  = $r_ctd_l->volume,
                    jumlah  = $r_ctd_l->jumlah,
                    tanggal = '$r_ctd_l->tanggal',
                    detail_material_nama  = '$r_ctd_l->detail_material_nama',
                    detail_material_satuan  = '$r_ctd_l->detail_material_satuan',
                    nama_toko = '$r_ctd_l->nama_toko'
                  where
                    no_bukti  = '$r_ctd_l->no_bukti' and
                    no_spk  = '$r_ctd_l->no_spk' and
                    detail_material_kode  = '$r_ctd_l->detail_material_kode' and
                    kode_toko = '$r_ctd_l->kode_toko' and
                    pilihan = '$r_ctd_l->pilihan'
                  ";

                  $client->query($sql_update_ctd);
                }
              }
            }
          }
        }

        $q_ctd_s = json_decode($client->query("select *,coalesce(pilihan::int,0) as pilihan_int from tbl_hutangonkeu where no_spk = '$no_spk'"))->{'data'};
        if ($q_ctd_s != '') {
          for ($i=0; $i < count($q_ctd_s); $i++) { 
            $kode = $q_ctd_s[$i]->{'detail_material_kode'};
            $no_bukti = $q_ctd_s[$i]->{'no_bukti'};
            $toko = $q_ctd_s[$i]->{'kode_toko'};
            $pilihan_int = $q_ctd_s[$i]->{'pilihan_int'};
            $pilihan = $q_ctd_s[$i]->{'pilihan'};

            $sql_get_cek_ctd_l = "
            select 
            a.no_bukti,
            e.no_spk,
            a.detail_material_kode,
            a.uraian,
            a.volume,
            a.jumlah,
            a.tanggal,
            d.detail_material_nama,
            d.detail_material_satuan,
            c.toko_kode as kode_toko,
            c.toko_nama as nama_toko,
            a.pilihan
            from simpro_tbl_hutangonkeu a
            join simpro_tbl_pilih_toko b
            on a.pilih_toko_id = b.pilih_toko_id
            join simpro_tbl_toko c
            on b.toko_id = c.toko_id
            join simpro_tbl_detail_material d
            on a.detail_material_kode = d.detail_material_kode
            join simpro_tbl_proyek e
            on a.proyek_id = e.proyek_id
            where a.proyek_id = $proyek_id
            and a.detail_material_kode = '$kode'
            and a.no_bukti = '$no_bukti'
            and c.toko_kode = '$toko'
            and a.pilihan = $pilihan_int
            ";

            $q_get_cek_ctd = $this->db->query($sql_get_cek_ctd_l);
            if ($q_get_cek_ctd->num_rows() == 0) {
              $client->query("delete from tbl_hutangonkeu where
                no_spk = '$no_spk' and
                detail_material_kode = '$kode' and
                no_bukti = '$no_bukti' and
                kode_toko = '$toko' and
                pilihan = '$pilihan'
              ");
            }
          }
        }
      }

      if ($arg == 'antisipasi') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_ctd_l = "select 
          a.no_bukti,
          e.no_spk,
          a.detail_material_kode,
          a.uraian,
          a.volume,
          a.jumlah,
          a.tanggal,
          d.detail_material_nama,
          d.detail_material_satuan,
          c.toko_kode as kode_toko,
          c.toko_nama as nama_toko,
          a.pilihan::text
          from simpro_tbl_hutang_proses a
          join simpro_tbl_pilih_toko b
          on a.pilih_toko_id = b.pilih_toko_id
          join simpro_tbl_toko c
          on b.toko_id = c.toko_id
          join simpro_tbl_detail_material d
          on a.detail_material_kode = d.detail_material_kode
          join simpro_tbl_proyek e
          on a.proyek_id = e.proyek_id
          where a.proyek_id = $proyek_id";

        $q_ctd = $this->db->query($sql_get_ctd_l);
        if ($q_ctd->result()) {
          foreach ($q_ctd->result() as $r_ctd_l) {
            $d_ctd_s = json_decode($client->query("select count('a') as count from tbl_hutang_proses where no_spk = '$r_ctd_l->no_spk' and no_bukti = '$r_ctd_l->no_bukti' and detail_material_kode = '$r_ctd_l->detail_material_kode' and kode_toko = '$r_ctd_l->kode_toko' and pilihan = '$r_ctd_l->pilihan'"))->{'data'};
            if ($d_ctd_s != '') {
              for ($i=0; $i < count($d_ctd_s); $i++) { 
                if ($d_ctd_s[$i]->{'count'} == 0) {
                  $sql_insert_ctd = "
                  insert into tbl_hutang_proses (
                    no_bukti,
                    no_spk,
                    detail_material_kode,
                    uraian,
                    volume,
                    jumlah,
                    tanggal,
                    detail_material_nama,
                    detail_material_satuan,
                    kode_toko,
                    nama_toko,
                    pilihan
                  ) values (
                    '$r_ctd_l->no_bukti',
                    '$r_ctd_l->no_spk',
                    '$r_ctd_l->detail_material_kode',
                    '$r_ctd_l->uraian',
                    $r_ctd_l->volume,
                    $r_ctd_l->jumlah,
                    '$r_ctd_l->tanggal',
                    '$r_ctd_l->detail_material_nama',
                    '$r_ctd_l->detail_material_satuan',
                    '$r_ctd_l->kode_toko',
                    '$r_ctd_l->nama_toko',
                    '$r_ctd_l->pilihan'
                  )
                  ";

                  $client->query($sql_insert_ctd);
                } else {
                  $sql_update_ctd = "
                  update tbl_hutang_proses set
                    uraian  = '$r_ctd_l->uraian',
                    volume  = $r_ctd_l->volume,
                    jumlah  = $r_ctd_l->jumlah,
                    tanggal = '$r_ctd_l->tanggal',
                    detail_material_nama  = '$r_ctd_l->detail_material_nama',
                    detail_material_satuan  = '$r_ctd_l->detail_material_satuan',
                    nama_toko = '$r_ctd_l->nama_toko'
                  where
                    no_bukti  = '$r_ctd_l->no_bukti' and
                    no_spk  = '$r_ctd_l->no_spk' and
                    detail_material_kode  = '$r_ctd_l->detail_material_kode' and
                    kode_toko = '$r_ctd_l->kode_toko' and
                    pilihan = '$r_ctd_l->pilihan'
                  ";

                  $client->query($sql_update_ctd);
                }
              }
            }
          }
        }

        $q_ctd_s = json_decode($client->query("select *,coalesce(pilihan::int,0) as pilihan_int from tbl_hutang_proses where no_spk = '$no_spk'"))->{'data'};
        if ($q_ctd_s != '') {
          for ($i=0; $i < count($q_ctd_s); $i++) { 
            $kode = $q_ctd_s[$i]->{'detail_material_kode'};
            $no_bukti = $q_ctd_s[$i]->{'no_bukti'};
            $toko = $q_ctd_s[$i]->{'kode_toko'};
            $pilihan_int = $q_ctd_s[$i]->{'pilihan_int'};
            $pilihan = $q_ctd_s[$i]->{'pilihan'};

            $sql_get_cek_ctd_l = "
            select 
            a.no_bukti,
            e.no_spk,
            a.detail_material_kode,
            a.uraian,
            a.volume,
            a.jumlah,
            a.tanggal,
            d.detail_material_nama,
            d.detail_material_satuan,
            c.toko_kode as kode_toko,
            c.toko_nama as nama_toko,
            a.pilihan
            from simpro_tbl_hutang_proses a
            join simpro_tbl_pilih_toko b
            on a.pilih_toko_id = b.pilih_toko_id
            join simpro_tbl_toko c
            on b.toko_id = c.toko_id
            join simpro_tbl_detail_material d
            on a.detail_material_kode = d.detail_material_kode
            join simpro_tbl_proyek e
            on a.proyek_id = e.proyek_id
            where a.proyek_id = $proyek_id
            and a.detail_material_kode = '$kode'
            and a.no_bukti = '$no_bukti'
            and c.toko_kode = '$toko'
            and a.pilihan = $pilihan_int
            ";

            $q_get_cek_ctd = $this->db->query($sql_get_cek_ctd_l);
            if ($q_get_cek_ctd->num_rows() == 0) {
              $client->query("delete from tbl_hutang_proses where
                no_spk = '$no_spk' and
                detail_material_kode = '$kode' and
                no_bukti = '$no_bukti' and
                kode_toko = '$toko' and
                pilihan = '$pilihan'
              ");
            }
          }
        }
      }

      if ($arg == 'rencana_realisasi_mutu') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_rr_l = "select
          c.no_spk,
          a.rr_uraian_rencana,
          a.rr_uraian_realisasi,
          a.rr_tgl,
          b.user_name as rr_pelaku,
          (rencana_realisasi_mutu_jenis_id - 1) as rr_jenis
          from
          simpro_tbl_rencana_realisasi_mutu a
          join simpro_tbl_user b
          on a.user_id = b.user_id
          join simpro_tbl_proyek c
          on a.proyek_id = c.proyek_id
          where a.proyek_id = $proyek_id";

        $q_get_rr_l = $this->db->query($sql_get_rr_l);
        if ($q_get_rr_l->result()) {
          foreach ($q_get_rr_l->result() as $r_rr_l) {
            $d_cek_rr_s = json_decode($client->query("select count('a') as count from tbl_rencana_realisasi_mutu where no_spk = '$r_rr_l->no_spk' and rr_tgl = '$r_rr_l->rr_tgl' and rr_uraian_rencana = '$r_rr_l->rr_uraian_rencana'"))->{'data'};
            if ($d_cek_rr_s != '') {
              for ($i=0; $i < count($d_cek_rr_s); $i++) { 
                if ($d_cek_rr_s[$i]->{'count'} == 0) {
                  $sql_insert_rr = "
                  insert into tbl_rencana_realisasi_mutu (
                    no_spk,
                    rr_uraian_rencana,
                    rr_uraian_realisasi,
                    rr_tgl,
                    rr_pelaku,
                    rr_jenis
                  ) values (
                    '$r_rr_l->no_spk',
                    '$r_rr_l->rr_uraian_rencana',
                    '$r_rr_l->rr_uraian_realisasi',
                    '$r_rr_l->rr_tgl',
                    '$r_rr_l->rr_pelaku',
                    $r_rr_l->rr_jenis
                  )
                  ";

                  $client->query($sql_insert_rr);
                } else {
                  $sql_update_rr = "
                  update tbl_rencana_realisasi_mutu set
                    rr_uraian_realisasi = '$r_rr_l->rr_uraian_realisasi',
                    rr_pelaku = '$r_rr_l->rr_pelaku',
                    rr_jenis  = $r_rr_l->rr_jenis
                  where
                    no_spk  = '$r_rr_l->no_spk' and
                    rr_uraian_rencana = '$r_rr_l->rr_uraian_rencana' and
                    rr_tgl  = '$r_rr_l->rr_tgl'                     
                  ";

                  $client->query($sql_update_rr);
                }
              }
            }
          }
        }

        $d_rr_s = json_decode($client->query("select * from tbl_rencana_realisasi_mutu where no_spk = '$no_spk'"))->{'data'};
        if ($d_rr_s != '') {
          for ($i=0; $i < count($d_rr_s); $i++) { 
            $tanggal = $d_rr_s[$i]->{'rr_tgl'};
            $uraian = $d_rr_s[$i]->{'rr_uraian_rencana'};

            $q_cek_rr_l = $this->db->query("select * from simpro_tbl_rencana_realisasi_mutu where proyek_id = $proyek_id and rr_tgl = '$tanggal' and rr_uraian_rencana = '$uraian'");
            if ($q_cek_rr_l->num_rows() == 0) {
              $client->query("delete from tbl_rencana_realisasi_mutu where no_spk = '$no_spk' and rr_tgl = '$tanggal' and rr_uraian_rencana = '$uraian'");
            }
          }
        }
      }

      if ($arg == 'identifikasi_risiko') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_ar_l = "
        select
          a.ar_id,
          c.no_spk,
          a.risiko,
          a.akibat,
          a.tgl,
          b.user_name as user_tambah,
          a.analisis,
          a.rencana_penanganan,
          a.batas_waktu,
          a.keputusan,
          (a.tingkat_akibat - 1) as tingkat_akibat,
          (a.tingkat_kemungkinan - 1) as tingkat_kemungkinan,
          (a.tingkat_risiko - 1) as tingkat_risiko,
          (a.sisa_risiko - 1) as sisa_risiko,
          a.pic
          from
          simpro_tbl_analisis_risiko a
          join simpro_tbl_user b
          on a.user_id = b.user_id
          join simpro_tbl_proyek c
          on a.proyek_id = c.proyek_id
          where a.proyek_id = $proyek_id";

        $q_get_ar_l = $this->db->query($sql_get_ar_l);
        if ($q_get_ar_l->result()) {
          foreach ($q_get_ar_l->result() as $r_ar_l) {
            $d_cek_ar_s = json_decode($client->query("select count('a') as count from tbl_analisis_risiko where no_spk = '$r_ar_l->no_spk' and tgl = '$r_ar_l->tgl' and risiko = '$r_ar_l->risiko'"))->{'data'};
            if ($d_cek_ar_s != '') {
              for ($i=0; $i < count($d_cek_ar_s); $i++) { 
                if ($d_cek_ar_s[$i]->{'count'} == 0) {
                  $sql_insert_ar = "
                  with get_last as (insert into tbl_analisis_risiko (
                    no_spk,
                    risiko,
                    akibat,
                    tgl,
                    user_tambah,
                    analisis,
                    rencana_penanganan,
                    batas_waktu,
                    keputusan,
                    tingkat_akibat,
                    tingkat_kemungkinan,
                    tingkat_risiko,
                    sisa_risiko,
                    pic
                  ) values (
                    '$r_ar_l->no_spk',
                    '$r_ar_l->risiko',
                    '$r_ar_l->akibat',
                    '$r_ar_l->tgl',
                    '$r_ar_l->user_tambah',
                    '$r_ar_l->analisis',
                    '$r_ar_l->rencana_penanganan',
                    '$r_ar_l->batas_waktu',
                    '$r_ar_l->keputusan',
                    $r_ar_l->tingkat_akibat,
                    $r_ar_l->tingkat_kemungkinan,
                    $r_ar_l->tingkat_risiko,
                    $r_ar_l->sisa_risiko,
                    '$r_ar_l->pic'
                  ) returning ar_id)
                  select ar_id from get_last
                  ";

                  $get_last = json_decode($client->query($sql_insert_ar))->{'data'};

                  if ($get_last != '') {
                    for ($i=0; $i < count($get_last); $i++) { 
                      $parent_id = $get_last[$i]->{'ar_id'};

                      $sql_insert_daftar_risiko = "
                      insert into tbl_daftar_risiko (
                        no_spk,
                        konteks,
                        akibat,
                        tgl,
                        user_tambah,
                        tingkat_akibat,
                        tingkat_kemungkinan,
                        tingkat_risiko,
                        parent_id
                      ) values (
                        '$r_ar_l->no_spk',
                        '$r_ar_l->risiko',
                        '$r_ar_l->akibat',
                        '$r_ar_l->tgl',
                        '$r_ar_l->user_tambah',
                        $r_ar_l->tingkat_akibat,
                        $r_ar_l->tingkat_kemungkinan,
                        $r_ar_l->tingkat_risiko,
                        $parent_id
                      )
                      ";

                      $client->query($sql_insert_daftar_risiko);
                    }
                  }
                } else {
                  $sql_update_ar = "
                  with get_last as (update tbl_analisis_risiko set
                    akibat  = '$r_ar_l->akibat',
                    user_tambah = '$r_ar_l->user_tambah',
                    analisis  = '$r_ar_l->analisis',
                    rencana_penanganan  = '$r_ar_l->rencana_penanganan',
                    batas_waktu = '$r_ar_l->batas_waktu',
                    keputusan = '$r_ar_l->keputusan',
                    tingkat_akibat  = $r_ar_l->tingkat_akibat,
                    tingkat_kemungkinan = $r_ar_l->tingkat_kemungkinan,
                    tingkat_risiko  = $r_ar_l->tingkat_risiko,
                    sisa_risiko = $r_ar_l->sisa_risiko,
                    pic = '$r_ar_l->pic'
                  where
                    no_spk  = '$r_ar_l->no_spk' and
                    risiko  = '$r_ar_l->risiko' and
                    tgl = '$r_ar_l->tgl' returning ar_id)
                  select ar_id from get_last
                  ";

                  $get_last = json_decode($client->query($sql_update_ar))->{'data'};

                  if ($get_last != '') {
                    for ($i=0; $i < count($get_last); $i++) { 
                      $parent_id = $get_last[$i]->{'ar_id'};

                      $r_daftar_risiko = $this->db->query("select * from simpro_tbl_daftar_risiko where ar_id = $r_ar_l->ar_id")->row();

                      $sql_update_daftar_risiko = "
                      update tbl_daftar_risiko set 
                        no_spk  = '$r_ar_l->no_spk',
                        konteks = '$r_ar_l->risiko',
                        akibat  = '$r_ar_l->akibat',
                        tgl = '$r_ar_l->tgl',
                        user_tambah = '$r_ar_l->user_tambah',
                        tingkat_akibat  = $r_ar_l->tingkat_akibat,
                        tingkat_kemungkinan = $r_ar_l->tingkat_kemungkinan,
                        tingkat_risiko  = $r_ar_l->tingkat_risiko,
                        penyebab  = '$r_daftar_risiko->penyebab',
                        kemungkinan_terjadi  = '$r_daftar_risiko->kemungkinan_terjadi',
                        faktor_positif  = '$r_daftar_risiko->faktor_positif',
                        prioritas  = '$r_daftar_risiko->prioritas'
                      where
                        parent_id = $parent_id
                      ";

                      $client->query($sql_update_daftar_risiko);
                    }
                  }
                }
              }
            }
          }
        }

        $d_ar_s = json_decode($client->query("select * from tbl_analisis_risiko where no_spk = '$no_spk'"))->{'data'};
        if ($d_ar_s != '') {
          for ($i=0; $i < count($d_ar_s); $i++) { 
            $tanggal = $d_ar_s[$i]->{'tgl'};
            $risiko = $d_ar_s[$i]->{'risiko'};

            $q_cek_ar_l = $this->db->query("select * from simpro_tbl_analisis_risiko where proyek_id = $proyek_id and tgl = '$tanggal' and risiko = '$risiko'");
            if ($q_cek_ar_l->num_rows() == 0) {
              $get_last = json_decode($client->query("with get_last as (delete from tbl_analisis_risiko where no_spk = '$no_spk' and tgl = '$tanggal' and risiko = '$risiko' returning ar_id) select ar_id from get_last"))->{'data'};
              if ($get_last != '') {
                for ($i=0; $i < count($get_last); $i++) { 
                  $parent_id = $get_last[$i]->{'ar_id'};

                  $client->query("delete from tbl_daftar_risiko where parent_id = $parent_id");
                }
              }
            }
          }
        }
      }

      if ($arg == 'daftar_risiko') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $this->sync('identifikasi_risiko');
      }

      if ($arg == 'lapbul_risiko') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_pr_l = "select
          c.no_spk,
          a.risiko,
          a.nilai_risiko,
          a.tgl,
          b.user_name as user_tambah,
          a.realisasi_tindakan,
          a.rencana_penanganan,
          (a.tingkat_risiko_id - 1) as tingkat_risiko,
          (a.status_risiko_id - 1) as status_risiko,
          (a.realisasi_sisa_risiko_id - 1) as realisasi_sisa_risiko,
          (a.target_sisa_risiko_id - 1) as target_sisa_risiko,
          a.pic,
          coalesce(a.biaya_memitigasi,0) as biaya_memitigasi,
          coalesce(a.biaya_sisa_risiko,0) as biaya_sisa_risiko,
          a.tgl_aak,
          a.ar_id as parent_id
          from
          simpro_tbl_penanganan_risiko a
          join simpro_tbl_user b
          on a.user_id = b.user_id
          join simpro_tbl_proyek c
          on a.proyek_id = c.proyek_id
          where a.proyek_id = $proyek_id";

        $q_pr_l = $this->db->query($sql_get_pr_l);
        if ($q_pr_l->result()) {
          foreach ($q_pr_l->result() as $r_pr_l) {
            $d_cek_pr_s = json_decode($client->query("select count('a') as count from tbl_penanganan_risiko where no_spk = '$r_pr_l->no_spk' and tgl_aak = '$r_pr_l->tgl_aak' and risiko = '$r_pr_l->risiko'"))->{'data'};
            if ($d_cek_pr_s != '') {
              for ($i=0; $i < count($d_cek_pr_s); $i++) { 
                if ($d_cek_pr_s[$i]->{'count'} == 0) {
                  $get_parent_id = json_decode($client->query("select ar_id as ar_id from tbl_analisis_risiko where no_spk = '$r_pr_l->no_spk' and tgl = '$r_pr_l->tgl' and risiko = '$r_pr_l->risiko'"))->{'data'};
                  if ($get_parent_id != '') {
                    $parent_id = $get_parent_id[0]->{'ar_id'};
                  } else {
                    $parent_id = 0;
                  }
                  $sql_insert_pr = "
                  insert into tbl_penanganan_risiko (
                    no_spk,
                    risiko,
                    nilai_risiko,
                    tgl,
                    user_tambah,
                    realisasi_tindakan,
                    rencana_penanganan,
                    tingkat_risiko,
                    status_risiko,
                    realisasi_sisa_risiko,
                    target_sisa_risiko,
                    pic,
                    biaya_memitigasi,
                    biaya_sisa_risiko,
                    tgl_aak,
                    parent_id
                  ) values (
                    '$r_pr_l->no_spk',
                    '$r_pr_l->risiko',
                    '$r_pr_l->nilai_risiko',
                    '$r_pr_l->tgl',
                    '$r_pr_l->user_tambah',
                    '$r_pr_l->realisasi_tindakan',
                    '$r_pr_l->rencana_penanganan',
                    $r_pr_l->tingkat_risiko,
                    $r_pr_l->status_risiko,
                    $r_pr_l->realisasi_sisa_risiko,
                    $r_pr_l->target_sisa_risiko,
                    '$r_pr_l->pic',
                    $r_pr_l->biaya_memitigasi,
                    $r_pr_l->biaya_sisa_risiko,
                    '$r_pr_l->tgl_aak',
                    $parent_id
                  )
                  ";

                  $client->query($sql_insert_pr);
                } else {
                  $sql_update_pr = "
                  update tbl_penanganan_risiko set
                    nilai_risiko  = '$r_pr_l->nilai_risiko',
                    user_tambah = '$r_pr_l->user_tambah',
                    realisasi_tindakan  = '$r_pr_l->realisasi_tindakan',
                    rencana_penanganan  = '$r_pr_l->rencana_penanganan',
                    tingkat_risiko  = $r_pr_l->tingkat_risiko,
                    status_risiko = $r_pr_l->status_risiko,
                    realisasi_sisa_risiko = $r_pr_l->realisasi_sisa_risiko,
                    target_sisa_risiko  = $r_pr_l->target_sisa_risiko,
                    pic = '$r_pr_l->pic',
                    biaya_memitigasi  = $r_pr_l->biaya_memitigasi,
                    biaya_sisa_risiko = $r_pr_l->biaya_sisa_risiko
                  where
                    no_spk  = '$r_pr_l->no_spk' and
                    risiko  = '$r_pr_l->risiko' and
                    tgl_aak = '$r_pr_l->tgl_aak'
                  ";

                  $client->query($sql_update_pr);
                }
              }
            }
          }
        }

        $d_pr_s = json_decode($client->query("select * from tbl_penanganan_risiko where no_spk = '$no_spk'"))->{'data'};
        if ($d_pr_s != '') {
          for ($i=0; $i < count($d_pr_s); $i++) { 
            $tgl_aak = $d_pr_s[$i]->tgl_aak;
            $risiko = $d_pr_s[$i]->risiko;

            $d_cek_pr_l = $this->db->query("select * from simpro_tbl_penanganan_risiko where proyek_id = $proyek_id and tgl_aak = '$tgl_aak' and risiko = '$risiko'");
            if ($d_cek_pr_l->num_rows() == 0) {
              $client->query("delete from tbl_penanganan_risiko where no_spk = '$no_spk' and tgl_aak = '$tgl_aak' and risiko = '$risiko'");
            }
          }
        }
      }

      if ($arg == 'dokFoto') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_dokfoto_l = "
        select
        a.foto_proyek_tgl,
        a.foto_proyek_file,
        a.foto_proyek_keterangan,
        b.no_spk,
        a.foto_proyek_judul
        from
        simpro_tbl_foto_proyek a
        join simpro_tbl_proyek b
        on a.proyek_id = b.proyek_id
        where a.proyek_id = $proyek_id";

        $q_get_dokfoto_l = $this->db->query($sql_get_dokfoto_l);
        if ($q_get_dokfoto_l->result()) {
          foreach ($q_get_dokfoto_l->result() as $r_dokfoto_l) {
            $d_dokfoto_s = json_decode($client->query("select count('a') as count from tbl_foto_proyek where no_spk = '$no_spk' and foto_proyek_tgl = '$r_dokfoto_l->foto_proyek_tgl' and foto_proyek_keterangan = '$r_dokfoto_l->foto_proyek_keterangan'"))->{'data'};
            if ($d_dokfoto_s != '') {
              for ($i=0; $i < count($d_dokfoto_s); $i++) { 
                if ($d_dokfoto_s[$i]->{'count'} == 0) {
                  $sql_insert_dokfoto = "
                  insert into tbl_foto_proyek (
                    foto_proyek_tgl,
                    foto_proyek_file,
                    foto_proyek_keterangan,
                    no_spk,
                    foto_proyek_judul,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$r_dokfoto_l->foto_proyek_tgl',
                    '$r_dokfoto_l->foto_proyek_file',
                    '$r_dokfoto_l->foto_proyek_keterangan',
                    '$r_dokfoto_l->no_spk',
                    '$r_dokfoto_l->foto_proyek_judul',
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )
                  ";

                  $client->query($sql_insert_dokfoto);
                } else {
                  $sql_update_dokfoto = "
                  update tbl_foto_proyek set
                    foto_proyek_file  = '$r_dokfoto_l->foto_proyek_file',
                    foto_proyek_judul  = '$r_dokfoto_l->foto_proyek_judul',
                    user_update = '$uname_update',
                    tgl_update  = '$tgl_update',
                    ip_update = '$ip_update',
                    divisi_update = '$divisi_update',
                    waktu_update  = '$waktu_update'
                  where
                    no_spk  = '$r_dokfoto_l->no_spk' and
                    foto_proyek_tgl = '$r_dokfoto_l->foto_proyek_tgl' and
                    foto_proyek_keterangan = '$r_dokfoto_l->foto_proyek_keterangan'
                  ";

                  $client->query($sql_update_dokfoto);
                }
              }
            }
          }
        }

        $data_dokfoto_s = json_decode($client->query("select * from tbl_foto_proyek where no_spk = '$no_spk'"))->{'data'};
        if ($data_dokfoto_s != '') {
          for ($i=0; $i < count($data_dokfoto_s); $i++) { 
            $tanggal = $data_dokfoto_s[$i]->{'foto_proyek_tgl'};
            $judul = $data_dokfoto_s[$i]->{'foto_proyek_keterangan'};

            $d_cek_dokfoto_l = $this->db->query("select * from simpro_tbl_foto_proyek where proyek_id = $proyek_id and foto_proyek_tgl = '$tanggal' and foto_proyek_keterangan = '$judul'");
            if ($d_cek_dokfoto_l->num_rows() == 0) {
              $client->query("delete from tbl_foto_proyek where no_spk = '$no_spk' and foto_proyek_tgl = '$tanggal' and foto_proyek_keterangan = '$judul'");
            }
          }
        }
      }

      if ($arg == 'dokK3') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_dokfoto_l = "
        select
        a.foto_proyek_tgl,
        a.foto_proyek_file,
        a.foto_proyek_keterangan,
        b.no_spk,
        a.foto_proyek_judul
        from
        simpro_tbl_dok_k3 a
        join simpro_tbl_proyek b
        on a.proyek_id = b.proyek_id
        where a.proyek_id = $proyek_id";

        $q_get_dokfoto_l = $this->db->query($sql_get_dokfoto_l);
        if ($q_get_dokfoto_l->result()) {
          foreach ($q_get_dokfoto_l->result() as $r_dokfoto_l) {
            $d_dokfoto_s = json_decode($client->query("select count('a') as count from tbl_dok_k3 where no_spk = '$no_spk' and foto_proyek_tgl = '$r_dokfoto_l->foto_proyek_tgl' and foto_proyek_keterangan = '$r_dokfoto_l->foto_proyek_keterangan'"))->{'data'};
            if ($d_dokfoto_s != '') {
              for ($i=0; $i < count($d_dokfoto_s); $i++) { 
                if ($d_dokfoto_s[$i]->{'count'} == 0) {
                  $sql_insert_dokfoto = "
                  insert into tbl_dok_k3 (
                    foto_proyek_tgl,
                    foto_proyek_file,
                    foto_proyek_keterangan,
                    no_spk,
                    foto_proyek_judul,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$r_dokfoto_l->foto_proyek_tgl',
                    '$r_dokfoto_l->foto_proyek_file',
                    '$r_dokfoto_l->foto_proyek_keterangan',
                    '$r_dokfoto_l->no_spk',
                    '$r_dokfoto_l->foto_proyek_judul',
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )
                  ";

                  $client->query($sql_insert_dokfoto);
                } else {
                  $sql_update_dokfoto = "
                  update tbl_dok_k3 set
                    foto_proyek_file  = '$r_dokfoto_l->foto_proyek_file',
                    foto_proyek_judul  = '$r_dokfoto_l->foto_proyek_judul',
                    user_update = '$uname_update',
                    tgl_update  = '$tgl_update',
                    ip_update = '$ip_update',
                    divisi_update = '$divisi_update',
                    waktu_update  = '$waktu_update'
                  where
                    no_spk  = '$r_dokfoto_l->no_spk' and
                    foto_proyek_tgl = '$r_dokfoto_l->foto_proyek_tgl' and
                    foto_proyek_keterangan = '$r_dokfoto_l->foto_proyek_keterangan'
                  ";

                  $client->query($sql_update_dokfoto);
                }
              }
            }
          }
        }

        $data_dokfoto_s = json_decode($client->query("select * from tbl_dok_k3 where no_spk = '$no_spk'"))->{'data'};
        if ($data_dokfoto_s != '') {
          for ($i=0; $i < count($data_dokfoto_s); $i++) { 
            $tanggal = $data_dokfoto_s[$i]->{'foto_proyek_tgl'};
            $judul = $data_dokfoto_s[$i]->{'foto_proyek_keterangan'};

            $d_cek_dokfoto_l = $this->db->query("select * from simpro_tbl_dok_k3 where proyek_id = $proyek_id and foto_proyek_tgl = '$tanggal' and foto_proyek_keterangan = '$judul'");
            if ($d_cek_dokfoto_l->num_rows() == 0) {
              $client->query("delete from tbl_dok_k3 where no_spk = '$no_spk' and foto_proyek_tgl = '$tanggal' and foto_proyek_keterangan = '$judul'");
            }
          }
        }
      }

      if ($arg == 'inovasi') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_dokfoto_l = "
        select
        a.foto_proyek_tgl,
        a.foto_proyek_file,
        a.foto_proyek_keterangan,
        b.no_spk,
        a.foto_proyek_judul
        from
        simpro_tbl_inovasi a
        join simpro_tbl_proyek b
        on a.proyek_id = b.proyek_id
        where a.proyek_id = $proyek_id";

        $q_get_dokfoto_l = $this->db->query($sql_get_dokfoto_l);
        if ($q_get_dokfoto_l->result()) {
          foreach ($q_get_dokfoto_l->result() as $r_dokfoto_l) {
            $d_dokfoto_s = json_decode($client->query("select count('a') as count from tbl_inovasi where no_spk = '$no_spk' and foto_proyek_tgl = '$r_dokfoto_l->foto_proyek_tgl' and foto_proyek_keterangan = '$r_dokfoto_l->foto_proyek_keterangan'"))->{'data'};
            if ($d_dokfoto_s != '') {
              for ($i=0; $i < count($d_dokfoto_s); $i++) { 
                if ($d_dokfoto_s[$i]->{'count'} == 0) {
                  $sql_insert_dokfoto = "
                  insert into tbl_inovasi (
                    foto_proyek_tgl,
                    foto_proyek_file,
                    foto_proyek_keterangan,
                    no_spk,
                    foto_proyek_judul,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$r_dokfoto_l->foto_proyek_tgl',
                    '$r_dokfoto_l->foto_proyek_file',
                    '$r_dokfoto_l->foto_proyek_keterangan',
                    '$r_dokfoto_l->no_spk',
                    '$r_dokfoto_l->foto_proyek_judul',
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )
                  ";

                  $client->query($sql_insert_dokfoto);
                } else {
                  $sql_update_dokfoto = "
                  update tbl_inovasi set
                    foto_proyek_file  = '$r_dokfoto_l->foto_proyek_file',
                    foto_proyek_judul  = '$r_dokfoto_l->foto_proyek_judul',
                    user_update = '$uname_update',
                    tgl_update  = '$tgl_update',
                    ip_update = '$ip_update',
                    divisi_update = '$divisi_update',
                    waktu_update  = '$waktu_update'
                  where
                    no_spk  = '$r_dokfoto_l->no_spk' and
                    foto_proyek_tgl = '$r_dokfoto_l->foto_proyek_tgl' and
                    foto_proyek_keterangan = '$r_dokfoto_l->foto_proyek_keterangan'
                  ";

                  $client->query($sql_update_dokfoto);
                }
              }
            }
          }
        }

        $data_dokfoto_s = json_decode($client->query("select * from tbl_inovasi where no_spk = '$no_spk'"))->{'data'};
        if ($data_dokfoto_s != '') {
          for ($i=0; $i < count($data_dokfoto_s); $i++) { 
            $tanggal = $data_dokfoto_s[$i]->{'foto_proyek_tgl'};
            $judul = $data_dokfoto_s[$i]->{'foto_proyek_keterangan'};

            $d_cek_dokfoto_l = $this->db->query("select * from simpro_tbl_inovasi where proyek_id = $proyek_id and foto_proyek_tgl = '$tanggal' and foto_proyek_keterangan = '$judul'");
            if ($d_cek_dokfoto_l->num_rows() == 0) {
              $client->query("delete from tbl_inovasi where no_spk = '$no_spk' and foto_proyek_tgl = '$tanggal' and foto_proyek_keterangan = '$judul'");
            }
          }
        }
      }

      if ($arg == 'level') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_level_s = json_decode($client->query("select nama_peran from tbl_peran"))->{'data'};
        if ($d_level_s != '') {
          for ($i=0; $i < count($d_level_s); $i++) { 
            $peran = $d_level_s[$i]->{'nama_peran'};
            $q_peran_l = $this->db->query("select * from simpro_tbl_peran where nama_peran = '$peran'");
            if ($q_peran_l->num_rows() == 0) {
              $arr_peran = array(
                'nama_peran' => $peran 
              );

              $this->db->insert('simpro_tbl_peran',$arr_peran);
            }
          }
        }

        $d_level_l = $this->db->get('simpro_tbl_peran');
        if ($d_level_l->result()) {
          foreach ($d_level_l->result() as $r_peran) {
            $d_cek_level = json_decode($client->query("select count('a') as count from tbl_peran where nama_peran = '$r_peran->nama_peran'"))->{'data'};
            if ($d_cek_level != '') {
              for ($i=0; $i < count($d_cek_level); $i++) { 
                if ($d_cek_level[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_peran',array('nama_peran' => $r_peran->nama_peran));
                }
              }
            }
          }
        }
      }

      if ($arg == 'satuan') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_satuan_s = json_decode($client->query("select satuan_nama from tbl_satuan"))->{'data'};
        if ($d_satuan_s != '') {
          for ($i=0; $i < count($d_satuan_s); $i++) { 
            $satuan = $d_satuan_s[$i]->{'satuan_nama'};
            $q_satuan_l = $this->db->query("select * from simpro_tbl_satuan where satuan_nama = '$satuan'");
            if ($q_satuan_l->num_rows() == 0) {
              $arr_satuan = array(
                'satuan_nama' => $satuan,
                'user_update' => $uid,
                'tgl_update' => $tgl_update,
                'ip_update' => $ip_update,
                'divisi_update' => $divisi_id,
                'waktu_update' => $waktu_update
              );

              $this->db->insert('simpro_tbl_satuan',$arr_satuan);
            }
          }
        }

        $d_satuan_l = $this->db->get('simpro_tbl_satuan');
        if ($d_satuan_l->result()) {
          foreach ($d_satuan_l->result() as $r_satuan) {
            $d_cek_satuan_s = json_decode($client->query("select count('a') as count from tbl_satuan where satuan_nama = '$r_satuan->satuan_nama'"))->{'data'};
            if ($d_cek_satuan_s != '') {
              for ($i=0; $i < count($d_cek_satuan_s); $i++) { 
                if ($d_cek_satuan_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_satuan',array('satuan_nama' => $r_satuan->satuan_nama));
                }
              }
            }
          }
        }
      }

      if ($arg == 'user') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_user_s = json_decode($client->query("select *,(tgl_update::text || ' ' || waktu_update::text)::timestamp as waktu from tbl_user"))->{'data'};
        if ($d_user_s != '') {
          for ($i=0; $i < count($d_user_s); $i++) { 
            $u_id = $d_user_s[$i]->{'user_id'};
            $user_name = $d_user_s[$i]->{'user_name'};
            $arr_spk = '';
            $no_spk_aar = explode(',', $d_user_s[$i]->{'no_spk'});

            foreach ($no_spk_aar as $row) {
              $arr_spk = $arr_spk.','.$this->get_data('simpro_tbl_proyek','proyek_id','no_spk',$row);
            }

            $arr_user = array(
              'user_name' => $d_user_s[$i]->{'user_name'},
              'first_name' => $d_user_s[$i]->{'first_name'},
              'last_name' => $d_user_s[$i]->{'last_name'},
              'password' => $d_user_s[$i]->{'password'},
              'alamat' => $d_user_s[$i]->{'alamat'},
              'jenis_kelamin' => $d_user_s[$i]->{'jenis_kelamin'},
              'tanggal_lahir' => $d_user_s[$i]->{'tanggal_lahir'},
              'nip' => $d_user_s[$i]->{'nip'},
              'foto' => $d_user_s[$i]->{'foto'},
              'divisi' => $d_user_s[$i]->{'divisi'},
              'tanggal_masuk' => $d_user_s[$i]->{'tanggal_masuk'},      
              'status_add' => $d_user_s[$i]->{'status_add'},
              'user_jenis' => $d_user_s[$i]->{'user_jenis'},
              'jenis_user' => $d_user_s[$i]->{'jenis_user'},
              'proyek_check' => $d_user_s[$i]->{'proyek_check'},
              'email' => $d_user_s[$i]->{'email'},
              'no_hp' => $d_user_s[$i]->{'no_hp'},
              'lastactivity' => $d_user_s[$i]->{'lastactivity'},
              'user_update' => $uid,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_update' => $divisi_id,
              'waktu_update' => $waktu_update,
              'no_spk' => substr($arr_spk, 1),
              'jabatan' => $this->get_data('simpro_tbl_jabatan','id_jabatan','jabatan',$d_user_s[$i]->{'jabatan'}),
              'level_akses' => $d_user_s[$i]->{'level_akses'},
              'kode_entitas' => $this->get_data('simpro_tbl_divisi','divisi_id','divisi_kode',$d_user_s[$i]->{'kode_entitas'})
            );

            // $arr_user_old = array(
            //   'user_name' => $d_user_s[$i]->{'user_name'},
            //   'first_name' => $d_user_s[$i]->{'first_name'},
            //   'last_name' => $d_user_s[$i]->{'last_name'},
            //   'password' => $d_user_s[$i]->{'password'},
            //   'alamat' => $d_user_s[$i]->{'alamat'},
            //   'jenis_kelamin' => $d_user_s[$i]->{'jenis_kelamin'},
            //   'tanggal_lahir' => $d_user_s[$i]->{'tanggal_lahir'},
            //   'nip' => $d_user_s[$i]->{'nip'},
            //   'foto' => $d_user_s[$i]->{'foto'},
            //   'divisi' => $d_user_s[$i]->{'divisi'},
            //   'tanggal_masuk' => $d_user_s[$i]->{'tanggal_masuk'},      
            //   'status_add' => $d_user_s[$i]->{'status_add'},
            //   'user_jenis' => $d_user_s[$i]->{'user_jenis'},
            //   'jenis_user' => $d_user_s[$i]->{'jenis_user'},
            //   'proyek_check' => $d_user_s[$i]->{'proyek_check'},
            //   'email' => $d_user_s[$i]->{'email'},
            //   'no_hp' => $d_user_s[$i]->{'no_hp'},
            //   'lastactivity' => $d_user_s[$i]->{'lastactivity'},
            //   'user_update' => $uid,
            //   'tgl_update' => $tgl_update,
            //   'ip_update' => $ip_update,
            //   'divisi_update' => $divisi_id,
            //   'waktu_update' => $waktu_update,
            //   'no_spk' => $d_user_s[$i]->{'no_spk'},
            //   'jabatan' => $d_user_s[$i]->{'jabatan'},
            //   'level_akses' => $d_user_s[$i]->{'level_akses'},
            //   'kode_entitas' => $d_user_s[$i]->{'kode_entitas'}
            // );

            $q_cek_user_l = $this->db->query("select *,(tgl_update::text || ' ' || waktu_update::text)::timestamp as waktu from simpro_tbl_user where lower(trim(user_name)) = lower(trim('$user_name'))");
            
            if ($q_cek_user_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_user',$arr_user);
            } else {
              if (trim($d_user_s[$i]->{'user_name'}) == trim($user_name_data)) {
                if ($d_user_s[$i]->{'waktu'} > $q_cek_user_l->row()->waktu) {                  
                  $this->db->where(array('lower(trim(user_name))' => strtolower(trim($user_name))));
                  $this->db->update('simpro_tbl_user',$arr_user);
                } else {
                  $arr_user_up = array(
                    'user_update' => $uid,
                    'tgl_update' => $tgl_update,
                    'ip_update' => $ip_update,
                    'divisi_update' => $divisi_id,
                    'waktu_update' => $waktu_update
                  );
                  $this->db->where(array('lower(trim(user_name))' => strtolower(trim($user_name))));
                  $this->db->update('simpro_tbl_user',$arr_user_up);

                  $arr_spk_l= '';
                  $jabatan = $this->get_data_from_id('simpro_tbl_jabatan','jabatan','id_jabatan',$q_cek_user_l->row()->jabatan);
                  $kode_entitas = $this->get_data_from_id('simpro_tbl_divisi','divisi_kode','divisi_id',$q_cek_user_l->row()->kode_entitas);
                  $no_spk_aar_l = explode(',', $q_cek_user_l->row()->no_spk);

                  foreach ($no_spk_aar_l as $row) {
                    $arr_spk_l = $arr_spk_l.','.$this->get_data_from_id('simpro_tbl_proyek','no_spk','proyek_id',$row);
                  }

                  $sql_up_to_server_user = "
                    update tbl_user set
                    user_name = '".$q_cek_user_l->row()->user_name."',
                    first_name = '".$q_cek_user_l->row()->first_name."',
                    last_name = '".$q_cek_user_l->row()->last_name."',
                    password = '".$q_cek_user_l->row()->password."',
                    alamat = '".$q_cek_user_l->row()->alamat."',
                    jenis_kelamin = '".$q_cek_user_l->row()->jenis_kelamin."',
                    tanggal_lahir = '".$q_cek_user_l->row()->tanggal_lahir."',
                    nip = '".$q_cek_user_l->row()->nip."',
                    foto = '".$q_cek_user_l->row()->foto."',
                    divisi = '".$q_cek_user_l->row()->divisi."',
                    tanggal_masuk = '".$q_cek_user_l->row()->tanggal_masuk."',      
                    status_add = '".$q_cek_user_l->row()->status_add."',
                    user_jenis = '".$q_cek_user_l->row()->user_jenis."',
                    jenis_user = '".$q_cek_user_l->row()->jenis_user."',
                    proyek_check = '".$q_cek_user_l->row()->proyek_check."',
                    email = '".$q_cek_user_l->row()->email."',
                    no_hp = '".$q_cek_user_l->row()->no_hp."',
                    lastactivity = ".$q_cek_user_l->row()->lastactivity.",
                    user_update = '".$uname_update."',
                    tgl_update = '".$tgl_update."',
                    ip_update = '".$ip_update."',
                    divisi_update = '".$divisi_id."',
                    waktu_update = '".$waktu_update."',
                    no_spk = '".$arr_spk_l."',
                    jabatan = '".$jabatan."',
                    level_akses = ".$q_cek_user_l->row()->level_akses.",
                    kode_entitas = '".$kode_entitas."'
                    where
                    lower(trim(user_name)) = lower(trim('$user_name'))
                  ";

                  // echo $sql_up_to_server_user;
                  $client->query($sql_up_to_server_user);
                }
              }
            }

            // $q_cek_user_l_old = $this->db->query("select *,(tgl_update::text || ' ' || waktu_update::text)::timestamp as waktu from tbl_user where lower(trim(user_name)) = lower(trim('$user_name'))");
            
            // if ($q_cek_user_l_old->num_rows() == 0) {
            //   $this->db->insert('tbl_user',$arr_user_old);
            // } else {
            //   if ($d_user_s[$i]->{'user_name'} == $user_name_data) {
            //     if ($d_user_s[$i]->{'waktu'} > $q_cek_user_l_old->row()->waktu) {
            //       $this->db->where(array('lower(trim(user_name))' => strtolower(trim($user_name))));
            //       $this->db->update('tbl_user',$arr_user_old);
            //     }
            //   }
            // }
          }
        }

        // $d_user_l = $this->db->get('simpro_tbl_user');
        // if ($d_user_l->result()) {
        //   foreach ($d_user_l->result() as $r_user_l) {
        //     $q_cek_user_s = json_decode($client->query("select count('a') as count from tbl_user where trim(user_name) = trim('$r_user_l->user_name')"))->{'data'};
        //     if ($q_cek_user_s != '') {
        //       for ($i=0; $i < count($q_cek_user_s); $i++) { 
        //         if ($q_cek_user_s[$i]->{'count'} == 0) {
        //           $this->db->delete('simpro_tbl_user',array('user_id' => $r_user_l->user_id));
        //         }
        //       }
        //     }
        //   }
        // }

        // $d_user_l_old = $this->db->get('tbl_user');
        // if ($d_user_l_old->result()) {
        //   foreach ($d_user_l_old->result() as $r_user_l_old) {
        //     $q_cek_user_s = json_decode($client->query("select count('a') as count from tbl_user where trim(user_name) = trim('$r_user_l_old->user_name')"))->{'data'};
        //     if ($q_cek_user_s != '') {
        //       for ($i=0; $i < count($q_cek_user_s); $i++) { 
        //         if ($q_cek_user_s[$i]->{'count'} == 0) {
        //           $this->db->delete('tbl_user',array('user_id' => $r_user_l_old->user_id));
        //         }
        //       }
        //     }
        //   }
        // }
      }

      if ($arg == 'user_current') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);


        $d_user_s = json_decode($client->query("select *,(tgl_update::text || ' ' || waktu_update::text)::timestamp as waktu from tbl_user where trim(user_name) = trim('$user_name_data')"))->{'data'};
        
        // echo "select *,(tgl_update::text || ' ' || waktu_update::text)::timestamp as waktu from tbl_user where trim(user_name) = trim('$user_name_data')";
        if ($d_user_s != '') {
          for ($i=0; $i < count($d_user_s); $i++) { 
            $u_id = $d_user_s[$i]->{'user_id'};
            $user_name = $d_user_s[$i]->{'user_name'};
            $arr_spk = '';
            $no_spk_aar = explode(',', $d_user_s[$i]->{'no_spk'});

            foreach ($no_spk_aar as $row) {
              $arr_spk = $arr_spk.','.$this->get_data('simpro_tbl_proyek','proyek_id','no_spk',$row);
            }

            $arr_user = array(
              'user_name' => $d_user_s[$i]->{'user_name'},
              'first_name' => $d_user_s[$i]->{'first_name'},
              'last_name' => $d_user_s[$i]->{'last_name'},
              'password' => $d_user_s[$i]->{'password'},
              'alamat' => $d_user_s[$i]->{'alamat'},
              'jenis_kelamin' => $d_user_s[$i]->{'jenis_kelamin'},
              'tanggal_lahir' => $d_user_s[$i]->{'tanggal_lahir'},
              'nip' => $d_user_s[$i]->{'nip'},
              'foto' => $d_user_s[$i]->{'foto'},
              'divisi' => $d_user_s[$i]->{'divisi'},
              'tanggal_masuk' => $d_user_s[$i]->{'tanggal_masuk'},      
              'status_add' => $d_user_s[$i]->{'status_add'},
              'user_jenis' => $d_user_s[$i]->{'user_jenis'},
              'jenis_user' => $d_user_s[$i]->{'jenis_user'},
              'proyek_check' => $d_user_s[$i]->{'proyek_check'},
              'email' => $d_user_s[$i]->{'email'},
              'no_hp' => $d_user_s[$i]->{'no_hp'},
              'lastactivity' => $d_user_s[$i]->{'lastactivity'},
              'user_update' => $uid,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_update' => $divisi_id,
              'waktu_update' => $waktu_update,
              'no_spk' => substr($arr_spk, 1),
              'jabatan' => $this->get_data('simpro_tbl_jabatan','id_jabatan','jabatan',$d_user_s[$i]->{'jabatan'}),
              'level_akses' => $d_user_s[$i]->{'level_akses'},
              'kode_entitas' => $this->get_data('simpro_tbl_divisi','divisi_id','divisi_kode',$d_user_s[$i]->{'kode_entitas'})
            );

            // $arr_user_old = array(
            //   'user_name' => $d_user_s[$i]->{'user_name'},
            //   'first_name' => $d_user_s[$i]->{'first_name'},
            //   'last_name' => $d_user_s[$i]->{'last_name'},
            //   'password' => $d_user_s[$i]->{'password'},
            //   'alamat' => $d_user_s[$i]->{'alamat'},
            //   'jenis_kelamin' => $d_user_s[$i]->{'jenis_kelamin'},
            //   'tanggal_lahir' => $d_user_s[$i]->{'tanggal_lahir'},
            //   'nip' => $d_user_s[$i]->{'nip'},
            //   'foto' => $d_user_s[$i]->{'foto'},
            //   'divisi' => $d_user_s[$i]->{'divisi'},
            //   'tanggal_masuk' => $d_user_s[$i]->{'tanggal_masuk'},      
            //   'status_add' => $d_user_s[$i]->{'status_add'},
            //   'user_jenis' => $d_user_s[$i]->{'user_jenis'},
            //   'jenis_user' => $d_user_s[$i]->{'jenis_user'},
            //   'proyek_check' => $d_user_s[$i]->{'proyek_check'},
            //   'email' => $d_user_s[$i]->{'email'},
            //   'no_hp' => $d_user_s[$i]->{'no_hp'},
            //   'lastactivity' => $d_user_s[$i]->{'lastactivity'},
            //   'user_update' => $user_name_data,
            //   'tgl_update' => $tgl_update,
            //   'ip_update' => $ip_update,
            //   'divisi_update' => $divisi_id,
            //   'waktu_update' => $waktu_update,
            //   'no_spk' => $d_user_s[$i]->{'no_spk'},
            //   'jabatan' => $d_user_s[$i]->{'jabatan'},
            //   'level_akses' => $d_user_s[$i]->{'level_akses'},
            //   'kode_entitas' => $d_user_s[$i]->{'kode_entitas'}
            // );

            $q_cek_user_l = $this->db->query("select *,(tgl_update::text || ' ' || waktu_update::text)::timestamp as waktu from simpro_tbl_user where lower(trim(user_name)) = lower(trim('$user_name'))");
            
            if ($q_cek_user_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_user',$arr_user);
            } else {

              if ($d_user_s[$i]->{'waktu'} == $q_cek_user_l->row()->waktu) {
                $this->db->where(array('lower(trim(user_name))' => strtolower(trim($user_name))));
                $this->db->update('simpro_tbl_user',$arr_user);
              } else {
                $arr_spk_l= '';
                $jabatan = $this->get_data_from_id('simpro_tbl_jabatan','jabatan','id_jabatan',$q_cek_user_l->row()->jabatan);
                $kode_entitas = $this->get_data_from_id('simpro_tbl_divisi','divisi_kode','divisi_id',$q_cek_user_l->row()->kode_entitas);
                $no_spk_aar_l = explode(',', $q_cek_user_l->row()->no_spk);

                foreach ($no_spk_aar_l as $row) {
                  $arr_spk_l = $arr_spk_l.','.$this->get_data_from_id('simpro_tbl_proyek','no_spk','proyek_id',$row);
                }

                $sql_up_to_server_user = "
                  update tbl_user set
                  user_name = '".$q_cek_user_l->row()->user_name."',
                  first_name = '".$q_cek_user_l->row()->first_name."',
                  last_name = '".$q_cek_user_l->row()->last_name."',
                  password = '".$q_cek_user_l->row()->password."',
                  alamat = '".$q_cek_user_l->row()->alamat."',
                  jenis_kelamin = '".$q_cek_user_l->row()->jenis_kelamin."',
                  tanggal_lahir = '".$q_cek_user_l->row()->tanggal_lahir."',
                  nip = '".$q_cek_user_l->row()->nip."',
                  foto = '".$q_cek_user_l->row()->foto."',
                  divisi = '".$q_cek_user_l->row()->divisi."',
                  tanggal_masuk = '".$q_cek_user_l->row()->tanggal_masuk."',      
                  status_add = '".$q_cek_user_l->row()->status_add."',
                  user_jenis = '".$q_cek_user_l->row()->user_jenis."',
                  jenis_user = '".$q_cek_user_l->row()->jenis_user."',
                  proyek_check = '".$q_cek_user_l->row()->proyek_check."',
                  email = '".$q_cek_user_l->row()->email."',
                  no_hp = '".$q_cek_user_l->row()->no_hp."',
                  lastactivity = ".$q_cek_user_l->row()->lastactivity.",
                  user_update = '".$uname_update."',
                  tgl_update = '".$tgl_update."',
                  ip_update = '".$ip_update."',
                  divisi_update = '".$divisi_id."',
                  waktu_update = '".$waktu_update."',
                  no_spk = '".$arr_spk_l."',
                  jabatan = '".$jabatan."',
                  level_akses = ".$q_cek_user_l->row()->level_akses.",
                  kode_entitas = '".$kode_entitas."'
                  where
                  lower(trim(user_name)) = lower(trim('$user_name'))
                ";

                // echo $sql_up_to_server_user;
                $client->query($sql_up_to_server_user);
              }
              
            }

            // $q_cek_user_l_old = $this->db->query("select *,(tgl_update::text || ' ' || waktu_update::text)::timestamp as waktu from tbl_user where lower(trim(user_name)) = lower(trim('$user_name'))");
            
            // if ($q_cek_user_l_old->num_rows() == 0) {
            //   $this->db->insert('tbl_user',$arr_user_old);
            // } else {

            //   if ($d_user_s[$i]->{'waktu'} == $q_cek_user_l->row()->waktu) {                
            //     $this->db->where(array('lower(trim(user_name))' => strtolower(trim($user_name))));
            //     $this->db->update('tbl_user',$arr_user_old);
            //   }

            // }
          }
        }
      }

      if ($arg == 'divisi') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_divisi_s = json_decode($client->query("select * from tbl_divisi"))->{'data'};
        if ($d_divisi_s != '') {
          for ($i=0; $i < count($d_divisi_s); $i++) { 
            $divisi_kode = $d_divisi_s[$i]->{'divisi_kode'};

            $arr_divisi = array(
              'divisi_name' => $d_divisi_s[$i]->{'divisi_name'},
              'divisi_kode' => $d_divisi_s[$i]->{'divisi_kode'},
              'divisi_account' => $d_divisi_s[$i]->{'divisi_account'},
              'urut' => $d_divisi_s[$i]->{'urut'},
              'user_update' => $uid,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_update' => $divisi_id,
              'waktu_update' => $waktu_update
            );

            $q_cek_divisi_l = $this->db->query("select * from simpro_tbl_divisi where lower(trim(divisi_kode)) = lower(trim('$divisi_kode'))");
            if ($q_cek_divisi_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_divisi',$arr_divisi);
            } else {
              $this->db->where(array('lower(trim(divisi_kode))' => strtolower(trim($divisi_kode))));
              $this->db->update('simpro_tbl_divisi',$arr_divisi);
            }
          }
        }

        $q_divisi_l = $this->db->get('simpro_tbl_divisi');
        if ($q_divisi_l->result()) {
          foreach ($q_divisi_l->result() as $r_divisi_l) {
            $q_cek_divisi_s = json_decode($client->query("select count('a') as count from tbl_divisi where lower(trim(divisi_kode)) = lower(trim('$r_divisi_l->divisi_kode'))"))->{'data'};
            if ($q_cek_divisi_s != '') {
              for ($i=0; $i < count($q_cek_divisi_s); $i++) { 
                if ($q_cek_divisi_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_divisi',array('divisi_kode' => $r_divisi_l->divisi_kode));
                }
              }
            }
          }
        }
      }

      if ($arg == 'sumber_dana') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_sumber_dana_s = json_decode($client->query("select * from tbl_sumber_dana"))->{'data'};
        if ($d_sumber_dana_s != '') {
          for ($i=0; $i < count($d_sumber_dana_s); $i++) { 
            $sumberdana_kode = $d_sumber_dana_s[$i]->{'sumberdana_kode'};

            $arr_sumber_dana = array(
              'sumberdana_kode' => $d_sumber_dana_s[$i]->{'sumberdana_kode'},
              'sumberdana_nama' => $d_sumber_dana_s[$i]->{'sumberdana_nama'},
              'user_update' => $uid,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_update' => $divisi_id,
              'waktu_update' => $waktu_update
            );

            $q_cek_sumber_dana_l = $this->db->query("select * from simpro_tbl_sumber_dana where lower(trim(sumberdana_kode)) = lower(trim('$sumberdana_kode'))");
            if ($q_cek_sumber_dana_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_sumber_dana',$arr_sumber_dana);
            } else {
              $this->db->where(array('sumberdana_kode' => $sumberdana_kode));
              $this->db->update('simpro_tbl_sumber_dana',$arr_sumber_dana);
            }
          }
        }

        $d_sumber_dana_l = $this->db->get('simpro_tbl_sumber_dana');
        if ($d_sumber_dana_l->result()) {
          foreach ($d_sumber_dana_l->result() as $r_sumber_dana_l) {
            $q_cek_sumber_dana_s = json_decode($client->query("select count('a') as count from tbl_sumber_dana where lower(trim(sumberdana_kode)) = lower(trim('$r_sumber_dana_l->sumberdana_kode'))"))->{'data'};
            if ($q_cek_sumber_dana_s != '') {
              for ($i=0; $i < count($q_cek_sumber_dana_s); $i++) { 
                if ($q_cek_sumber_dana_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_sumber_dana',array('sumberdana_kode' => $r_sumber_dana_l->sumberdana_kode));
                }
              }
            }
          }
        }
      }

      if ($arg == 'pemilik') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_pemilik_proyek_s = json_decode($client->query("select * from tbl_pemilik_proyek"))->{'data'};
        if ($d_pemilik_proyek_s != '') {
          for ($i=0; $i < count($d_pemilik_proyek_s); $i++) { 
            $pemilik_kode = $d_pemilik_proyek_s[$i]->{'pemilik_kode'};

            $arr_pemilik_proyek = array(
              'pemilik_kode' => $d_pemilik_proyek_s[$i]->{'pemilik_kode'},
              'pemilik_nama' => $d_pemilik_proyek_s[$i]->{'pemilik_nama'},
              'user_update' => $uid,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_update' => $divisi_id,
              'waktu_update' => $waktu_update
            );

            $q_cek_pemilik_proyek_l = $this->db->query("select * from simpro_tbl_pemilik_proyek where lower(trim(pemilik_kode)) = lower(trim('$pemilik_kode'))");
            if ($q_cek_pemilik_proyek_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_pemilik_proyek',$arr_pemilik_proyek);
            } else {
              $this->db->where(array('pemilik_kode' => $pemilik_kode));
              $this->db->update('simpro_tbl_pemilik_proyek',$arr_pemilik_proyek);
            }
          }
        }

        $d_pemilik_proyek_l = $this->db->get('simpro_tbl_pemilik_proyek');
        if ($d_pemilik_proyek_l->result()) {
          foreach ($d_pemilik_proyek_l->result() as $r_pemilik_proyek_l) {
            $q_cek_pemilik_proyek_s = json_decode($client->query("select count('a') as count from tbl_pemilik_proyek where lower(trim(pemilik_kode)) = lower(trim('$r_pemilik_proyek_l->pemilik_kode'))"))->{'data'};
            if ($q_cek_pemilik_proyek_s != '') {
              for ($i=0; $i < count($q_cek_pemilik_proyek_s); $i++) { 
                if ($q_cek_pemilik_proyek_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_pemilik_proyek',array('pemilik_kode' => $r_pemilik_proyek_l->pemilik_kode));
                }
              }
            }
          }
        }
      }

      if ($arg == 'sbu') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_sbu_s = json_decode($client->query("select * from tbl_sbu"))->{'data'};
        if ($d_sbu_s != '') {
          for ($i=0; $i < count($d_sbu_s); $i++) { 
            $sbu_kode = $d_sbu_s[$i]->{'sbu_kode'};

            $arr_sbu = array(
              'sbu_kode' => $d_sbu_s[$i]->{'sbu_kode'},
              'sbu_nama' => $d_sbu_s[$i]->{'sbu_nama'},
              'sbu_divisi' => $d_sbu_s[$i]->{'sbu_divisi'},
              'user_update' => $uid,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_update' => $divisi_id,
              'waktu_update' => $waktu_update
            );

            $q_cek_sbu_l = $this->db->query("select * from simpro_tbl_sbu where lower(trim(sbu_kode)) = lower(trim('$sbu_kode'))");
            if ($q_cek_sbu_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_sbu',$arr_sbu);
            } else {
              $this->db->where(array('sbu_kode' => $sbu_kode));
              $this->db->update('simpro_tbl_sbu',$arr_sbu);
            }
          }
        }

        $d_sbu_l = $this->db->get('simpro_tbl_sbu');
        if ($d_sbu_l->result()) {
          foreach ($d_sbu_l->result() as $r_sbu_l) {
            $q_cek_sbu_s = json_decode($client->query("select count('a') as count from tbl_sbu where lower(trim(sbu_kode)) = lower(trim('$r_sbu_l->sbu_kode'))"))->{'data'};
            if ($q_cek_sbu_s != '') {
              for ($i=0; $i < count($q_cek_sbu_s); $i++) { 
                if ($q_cek_sbu_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_sbu',array('sbu_kode' => $r_sbu_l->sbu_kode));
                }
              }
            }
          }
        }
      }

      if ($arg == 'daftar_peralatan') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_daftar_peralatan_s = json_decode($client->query("select * from tbl_master_peralatan"))->{'data'};
        if ($d_daftar_peralatan_s != '') {
          for ($i=0; $i < count($d_daftar_peralatan_s); $i++) { 
            $id_alat = $d_daftar_peralatan_s[$i]->{'alat_id'};

            $arr_daftar_peralatan = array(
              'uraian_jenis_alat' => $d_daftar_peralatan_s[$i]->{'uraian_jenis_alat'},
              'merk_model' => $d_daftar_peralatan_s[$i]->{'merk_model'},
              'type_penggerak' => $d_daftar_peralatan_s[$i]->{'type_penggerak'},
              'kapasitas' => $d_daftar_peralatan_s[$i]->{'kapasitas'},
              'tgl' => $tgl_update,
              'user_id' => $uid,
              'proyek_id' => $proyek_id,
              'id_alat' => $id_alat
            );

            $q_cek_daftar_peralatan_l = $this->db->query("select * from simpro_tbl_master_peralatan where 
              id_alat = $id_alat
            ");
            if ($q_cek_daftar_peralatan_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_master_peralatan',$arr_daftar_peralatan);
            } else {
              $this->db->where(
                array(
                  'id_alat' => $id_alat
                )
              );
              $this->db->update('simpro_tbl_master_peralatan',$arr_daftar_peralatan);
            }
          }
        }

        $d_daftar_peralatan_l = $this->db->get('simpro_tbl_master_peralatan');
        if ($d_daftar_peralatan_l->result()) {
          foreach ($d_daftar_peralatan_l->result() as $r_daftar_peralatan_l) {
            $q_cek_daftar_peralatan_s = json_decode($client->query("select count('a') as count from tbl_master_peralatan where 
              alat_id = $r_daftar_peralatan_l->id_alat
            "))->{'data'};
            if ($q_cek_daftar_peralatan_s != '') {
              for ($i=0; $i < count($q_cek_daftar_peralatan_s); $i++) { 
                if ($q_cek_daftar_peralatan_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_master_peralatan',
                    array(
                      'id_alat' => $r_daftar_peralatan_l->id_alat
                    )
                  );
                }
              }
            }
          }
        }
      }

      if ($arg == 'provinsi') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_provinsi_s = json_decode($client->query("select propinsi,kode_propinsi from tbl_propinsi where  (propinsi_induk='' or propinsi_induk is null ) order by propinsi"))->{'data'};
        if ($d_provinsi_s != '') {
          for ($i=0; $i < count($d_provinsi_s); $i++) { 
            $kode_prov = $d_provinsi_s[$i]->{'kode_propinsi'};
            $nama_prov = $d_provinsi_s[$i]->{'propinsi'};
            // echo ($i+1).'- kode = '.$kode_prov.' - '.$nama_prov.'<br>';

            $arr_prov = array(
              'nama_provinsi' => $nama_prov,
              'kode_provinsi' => $kode_prov
            );

            $q_cek_prov_l = $this->db->query("select * from simpro_tbl_provinsi where trim(kode_provinsi) = trim('$kode_prov')");
            if ($q_cek_prov_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_provinsi',$arr_prov);

              $id_provinsi = $this->db->insert_id();

              $sql_induk_prov = "select 
                a.propinsi as nama_provinsi,
                b.kode_propinsi as kode_provinsi_induk,
                a.kode_propinsi as kode_provinsi
                from tbl_propinsi a
                join (
                select * from tbl_propinsi group by kode_propinsi order by kode_propinsi desc
                ) b
                on a.propinsi_induk = b.propinsi
                where a.propinsi_induk !='' and b.kode_propinsi = '$kode_prov' order by a.propinsi";

              $q_cek_prov_induk_s = json_decode($client->query($sql_induk_prov))->{'data'};

              if ($q_cek_prov_induk_s != '') {
                for ($n=0; $n < count($q_cek_prov_induk_s); $n++) { 

                  $nama_provinsi = $q_cek_prov_induk_s[$n]->nama_provinsi;
                  $kode_provinsi = $q_cek_prov_induk_s[$n]->kode_provinsi;
                  $kode_provinsi_induk= $q_cek_prov_induk_s[$n]->kode_provinsi_induk;

                  $arr_prov_induk = array(
                    'id_provinsi' => $id_provinsi,
                    'nama_provinsi' => $nama_provinsi,
                    'kode_provinsi' => $kode_provinsi,
                    'kode_provinsi_induk'=> $kode_provinsi_induk
                  );

                  $q_cek_prov_induk_l = $this->db->query("select * from simpro_tbl_provinsi_induk where kode_provinsi = '$kode_provinsi'");
                  if ($q_cek_prov_induk_l->num_rows() == 0) {
                    $this->db->insert('simpro_tbl_provinsi_induk',$arr_prov_induk);
                  }
                }
              }
            } else {
              $this->db->update('simpro_tbl_provinsi',$arr_prov,array('trim(kode_provinsi)' => trim($kode_prov)));

              $id_provinsi = $q_cek_prov_l->row()->id_provinsi;

              $sql_induk_prov = "select 
                a.propinsi as nama_provinsi,
                b.kode_propinsi as kode_provinsi_induk,
                a.kode_propinsi as kode_provinsi
                from tbl_propinsi a
                join (
                select * from tbl_propinsi group by kode_propinsi order by kode_propinsi desc
                ) b
                on a.propinsi_induk = b.propinsi
                where a.propinsi_induk !='' and b.kode_propinsi = '$kode_prov' order by a.propinsi";

              $q_cek_prov_induk_s = json_decode($client->query($sql_induk_prov))->{'data'};

              if ($q_cek_prov_induk_s != '') {
                for ($n=0; $n < count($q_cek_prov_induk_s); $n++) { 

                  $nama_provinsi = $q_cek_prov_induk_s[$n]->nama_provinsi;
                  $kode_provinsi = $q_cek_prov_induk_s[$n]->kode_provinsi;
                  $kode_provinsi_induk= $q_cek_prov_induk_s[$n]->kode_provinsi_induk;

                  $arr_prov_induk = array(
                    'id_provinsi' => $id_provinsi,
                    'nama_provinsi' => $nama_provinsi,
                    'kode_provinsi' => $kode_provinsi,
                    'kode_provinsi_induk'=> $kode_provinsi_induk
                  );

                  $q_cek_prov_induk_l = $this->db->query("select * from simpro_tbl_provinsi_induk where trim(kode_provinsi) = trim('$kode_provinsi')");
                  if ($q_cek_prov_induk_l->num_rows() == 0) {
                    $this->db->insert('simpro_tbl_provinsi_induk',$arr_prov_induk);
                  } else {
                    $this->db->update('simpro_tbl_provinsi_induk',$arr_prov_induk,array('kode_provinsi' => $kode_provinsi));
                  }
                }
              }
            }
          }
        }

        // $q_prov_l = $this->db->get('simpro_tbl_provinsi');
        // if ($q_prov_l->result()) {
        //   foreach ($q_prov_l->result() as $r_prov_l) {
        //     // echo "select count('a') a count from tbl_propinsi where (propinsi_induk='' or propinsi_induk is null ) and kode_propinsi = '$r_prov_l->kode_provinsi'";
           
        //     $q_cek_prov_s = json_decode($client->query("select count('a') as count from tbl_propinsi where (propinsi_induk='' or propinsi_induk is null ) and kode_propinsi = '$r_prov_l->kode_provinsi'"))->{'data'};
        //      if ($q_cek_prov_s != '') {
        //       for ($i=0; $i < count($q_cek_prov_s); $i++) { 
        //         if ($q_cek_prov_s[$i]->{'count'} == 0) {
        //           $this->db->delete('simpro_tbl_provinsi',array('id_provinsi' => $r_prov_l->id_provinsi));
        //           $this->db->delete('simpro_tbl_provinsi_induk',array('id_provinsi' => $r_prov_l->id_provinsi));
        //         } else {
        //           $q_cek_prov_induk_s = json_decode($client->query("select count('a') as count from tbl_propinsi a
        //             join (
        //             select * from tbl_propinsi group by kode_propinsi order by kode_propinsi desc
        //             ) b
        //             on a.propinsi_induk = b.propinsi
        //             where a.propinsi_induk !='' and b.kode_propinsi = '$r_prov_l->kode_provinsi' order by a.propinsi "))->{'data'};
        //           if ($q_cek_prov_induk_s != '') {
        //             for ($n=0; $n < count($q_cek_prov_induk_s); $n++) { 
        //               if ($q_cek_prov_induk_s[$n]->{'count'} == 0) {
        //                 $this->db->delete('simpro_tbl_provinsi_induk',array('id_provinsi' => $r_prov_l->id_provinsi));
        //               }
        //             }
        //           }
        //         }
        //       }
        //     }
        //   }
        // }
      }

      if ($arg == 'direktorat') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_direktorat_s = json_decode($client->query("select * from tbl_direktorat"))->{'data'};
        if ($d_direktorat_s != '') {
          for ($i=0; $i < count($d_direktorat_s); $i++) { 
            $kode_dir = $d_direktorat_s[$i]->{'kode_dir'};
            $nama_dir = $d_direktorat_s[$i]->{'nama_dir'};

            $arr_dir = array(
              'kode_dir' => $kode_dir, 
              'nama_dir' => $nama_dir,
              'user_update' => $uname_update,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_update' => $divisi_update,
              'waktu_update' => $waktu_update
            );

            $q_cek_dir_l = $this->db->query("select * from simpro_tbl_direktorat where kode_dir = '$kode_dir'");
            if ($q_cek_dir_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_direktorat',$arr_dir);

              $q_dir_div_s = json_decode($client->query("select * from tbl_direktorat_divisi where kode_dir = '$kode_dir'"))->{'data'};
              if ($q_dir_div_s != '') {
                for ($n=0; $n < count($q_dir_div_s); $n++) { 
                  $kode_dir_div = $q_dir_div_s[$n]->{'kode_dir'};
                  $divisi_kode = $q_dir_div_s[$n]->{'divisi_kode'};

                  $arr_dir_div = array(
                    'kode_dir' => $kode_dir_div, 
                    'divisi_kode' => $divisi_kode,
                    'user_update' => $uname_update,
                    'tgl_update' => $tgl_update,
                    'ip_update' => $ip_update,
                    'divisi_update' => $divisi_update,
                    'waktu_update' => $waktu_update
                  );

                  $q_cek_dir_div_l = $this->db->query("select * from simpro_tbl_direktorat_divisi where kode_dir = '$kode_dir_div'");
                  if ($q_cek_dir_div_l->num_rows() == 0) {
                    $this->db->insert('simpro_tbl_direktorat_divisi',$arr_dir_div);
                  }
                }
              }
            } else {
              $this->db->update('simpro_tbl_direktorat',$arr_dir,array('kode_dir' => $kode_dir));

              $q_dir_div_s = json_decode($client->query("select * from tbl_direktorat_divisi where kode_dir = '$kode_dir'"))->{'data'};
              if ($q_dir_div_s != '') {
                for ($n=0; $n < count($q_dir_div_s); $n++) { 
                  $kode_dir_div = $q_dir_div_s[$n]->{'kode_dir'};
                  $divisi_kode = $q_dir_div_s[$n]->{'divisi_kode'};

                  $arr_dir_div = array(
                    'kode_dir' => $kode_dir_div, 
                    'divisi_kode' => $divisi_kode,
                    'user_update' => $uname_update,
                    'tgl_update' => $tgl_update,
                    'ip_update' => $ip_update,
                    'divisi_update' => $divisi_update,
                    'waktu_update' => $waktu_update
                  );

                  $q_cek_dir_div_l = $this->db->query("select * from simpro_tbl_direktorat_divisi where kode_dir = '$kode_dir_div'");
                  if ($q_cek_dir_div_l->num_rows() == 0) {
                    $this->db->insert('simpro_tbl_direktorat_divisi',$arr_dir_div);
                  } else {
                    $this->db->update('simpro_tbl_direktorat_divisi',$arr_dir_div,array('kode_dir' => $kode_dir_div));
                  }
                }
              }
            }
          }
        }

        $q_dir_l = $this->db->get('simpro_tbl_direktorat');
        if ($q_dir_l->result()) {
          foreach ($q_dir_l->result() as $r_dir_l) {
            $q_cek_dir_s = json_decode($client->query("select count('a') as count from tbl_direktorat where kode_dir = '$r_dir_l->kode_dir'"))->{'data'};
            if ($q_cek_dir_s != '') {
              for ($i=0; $i < count($q_cek_dir_s); $i++) { 
                if ($q_cek_dir_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_direktorat',array('kode_dir' => $r_dir_l->kode_dir));
                  $this->db->delete('simpro_tbl_direktorat_divisi',array('kode_dir' => $r_dir_l->kode_dir));
                } else {
                  $q_cek_dir_div_s = json_decode($client->query("select count('a') as count from tbl_direktorat_divisi where kode_dir = '$r_dir_l->kode_dir'"))->{'data'};
                  if ($q_cek_dir_div_s != '') {
                    for ($n=0; $n < count($q_cek_dir_div_s); $n++) { 
                      if ($q_cek_dir_div_s[$n]->{'count'} == 0) {
                        $this->db->delete('simpro_tbl_direktorat_divisi',array('kode_dir' => $r_dir_l->kode_dir));
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }

      if ($arg == 'master_analisa') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_m_analisa_s = "select 
          a.tahap_kode_kendali,
          10 as id_kat_analisa,
          trim(b.tahap_nama_kendali) as tahap_nama_kendali,
          b.tahap_satuan_kendali
          from tbl_komposisi_daftar_analisa a
          join tbl_daftar_analisa b
          on a.tahap_kode_kendali = b.tahap_kode_kendali
          group by id_kat_analisa,tahap_nama_kendali,tahap_satuan_kendali,a.tahap_kode_kendali
          order by tahap_nama_kendali";

        $q_m_analisa_s = json_decode($client->query($sql_get_m_analisa_s))->{'data'};

        $q_get_last_analisa = $this->db->query("select (right(kode_analisa,3))::int as kode_analisa_numb from simpro_master_analisa_daftar order by kode_analisa desc limit 1");
        if ($q_get_last_analisa->result()) {
          $numb = $q_get_last_analisa->row()->kode_analisa_numb + 1;
        } else {
          $numb = 1;
        }

        if ($q_m_analisa_s != '') {
          for ($i=0; $i < count($q_m_analisa_s); $i++) { 
            $kode = $q_m_analisa_s[$i]->{'tahap_kode_kendali'};
            $id_kat_analisa = $q_m_analisa_s[$i]->{'id_kat_analisa'};
            $nama = $q_m_analisa_s[$i]->{'tahap_nama_kendali'};
            $satuan = $this->get_data('simpro_tbl_satuan','satuan_id','satuan_nama',$q_m_analisa_s[$i]->{'tahap_satuan_kendali'});
            $kode_analisa = "AN".sprintf("%03d",$numb);

            $q_cek_m_analisa_l = $this->db->query("select * from simpro_master_analisa_daftar where trim(nama_item) = trim('$nama')");
            if ($q_cek_m_analisa_l->num_rows() == 0) {
              $arr_m_analisa = array(
                'kode_analisa' => $kode_analisa,
                'id_kat_analisa' => $id_kat_analisa,
                'nama_item' => $nama,
                'id_satuan' => $satuan
              );


              $numb++;

              $this->db->insert('simpro_master_analisa_daftar',$arr_m_analisa);

              $kode_analisa_m = $kode_analisa;
              $id_analisa_m = $this->db->insert_id();
            } else {
              $arr_m_analisa = array(
                'id_satuan' => $satuan
              );
              
              $this->db->update('simpro_master_analisa_daftar',$arr_m_analisa,array('nama_item' => $nama));

              $kode_analisa_m = $q_cek_m_analisa_l->row()->kode_analisa;
              $id_analisa_m = $q_cek_m_analisa_l->row()->id_data_analisa;
            }

            $sql_daftar_material = "select
              tahap_kode_kendali,
              detail_material_kode,
              avg(komposisi_koefisien_kendali) as komposisi_koefisien_kendali,
              avg(komposisi_harga_satuan_kendali) as komposisi_harga_satuan_kendali,
              keterangan,
              kode_rap
              from tbl_komposisi_daftar_analisa
              where tahap_kode_kendali = '$kode'
              group by 
              tahap_kode_kendali,
              detail_material_kode,
              keterangan,
              kode_rap
              order by detail_material_kode";

              // echo $sql_daftar_material.'<br>';

            $q_cek_material_s = json_decode($client->query($sql_daftar_material))->{'data'};
            if ($q_cek_material_s != '') {
              for ($v=0; $v < count($q_cek_material_s); $v++) { 
                $kode_m = $q_cek_material_s[$v]->{'detail_material_kode'};

                $arr_material_an = array(
                  'id_data_analisa' => $id_analisa_m,
                  'id_detail_material' => $this->get_data('simpro_tbl_detail_material','detail_material_id','detail_material_nama',$kode_m),
                  'kode_analisa' => $kode_analisa_m,
                  'kode_material' => $kode_m,
                  'koefisien' => $q_cek_material_s[$v]->{'komposisi_koefisien_kendali'},
                  'harga' => $q_cek_material_s[$v]->{'komposisi_harga_satuan_kendali'},
                  'keterangan' => $q_cek_material_s[$v]->{'keterangan'},
                  'kode_rap' => $q_cek_material_s[$v]->{'kode_rap'}
                );

                $q_cek_material_l = $this->db->query("select * from simpro_master_analisa_asat where kode_material = '$kode_m' and kode_analisa = '$kode_analisa_m'");

                if ($q_cek_material_l->num_rows() == 0) {
                  $this->db->insert('simpro_master_analisa_asat',$arr_material_an);
                } else {
                  $this->db->update('simpro_master_analisa_asat',$arr_material_an,array('kode_material' => $kode_m, 'kode_analisa' => $kode_analisa_m));
                }

              }
            }

            
          }
        }
      }

      if ($arg == 'sketsa_pekerjaan') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_get_dokfoto_l = "
        select
        a.foto_proyek_tgl,
        a.foto_proyek_file,
        a.foto_proyek_keterangan,
        b.no_spk,
        a.foto_proyek_judul
        from
        simpro_tbl_sketsa_proyek a
        join simpro_tbl_proyek b
        on a.proyek_id = b.proyek_id
        where a.proyek_id = $proyek_id";

        $q_get_dokfoto_l = $this->db->query($sql_get_dokfoto_l);
        if ($q_get_dokfoto_l->result()) {
          foreach ($q_get_dokfoto_l->result() as $r_dokfoto_l) {
            $d_dokfoto_s = json_decode($client->query("select count('a') as count from tbl_sketsa_proyek where no_spk = '$no_spk' and foto_proyek_tgl = '$r_dokfoto_l->foto_proyek_tgl' and foto_proyek_keterangan = '$r_dokfoto_l->foto_proyek_keterangan'"))->{'data'};
            if ($d_dokfoto_s != '') {
              for ($i=0; $i < count($d_dokfoto_s); $i++) { 
                if ($d_dokfoto_s[$i]->{'count'} == 0) {
                  $sql_insert_dokfoto = "
                  insert into tbl_sketsa_proyek (
                    foto_proyek_tgl,
                    foto_proyek_file,
                    foto_proyek_keterangan,
                    no_spk,
                    foto_proyek_judul,
                    user_update,
                    tgl_update,
                    ip_update,
                    divisi_update,
                    waktu_update
                  ) values (
                    '$r_dokfoto_l->foto_proyek_tgl',
                    '$r_dokfoto_l->foto_proyek_file',
                    '$r_dokfoto_l->foto_proyek_keterangan',
                    '$r_dokfoto_l->no_spk',
                    '$r_dokfoto_l->foto_proyek_judul',
                    '$uname_update',
                    '$tgl_update',
                    '$ip_update',
                    '$divisi_update',
                    '$waktu_update'
                  )
                  ";

                  $client->query($sql_insert_dokfoto);
                } else {
                  $sql_update_dokfoto = "
                  update tbl_sketsa_proyek set
                    foto_proyek_file  = '$r_dokfoto_l->foto_proyek_file',
                    foto_proyek_judul  = '$r_dokfoto_l->foto_proyek_judul',
                    user_update = '$uname_update',
                    tgl_update  = '$tgl_update',
                    ip_update = '$ip_update',
                    divisi_update = '$divisi_update',
                    waktu_update  = '$waktu_update'
                  where
                    no_spk  = '$r_dokfoto_l->no_spk' and
                    foto_proyek_tgl = '$r_dokfoto_l->foto_proyek_tgl' and
                    foto_proyek_keterangan = '$r_dokfoto_l->foto_proyek_keterangan'
                  ";

                  $client->query($sql_update_dokfoto);
                }
              }
            }
          }
        }

        $data_dokfoto_s = json_decode($client->query("select * from tbl_sketsa_proyek where no_spk = '$no_spk'"))->{'data'};
        if ($data_dokfoto_s != '') {
          for ($i=0; $i < count($data_dokfoto_s); $i++) { 
            $tanggal = $data_dokfoto_s[$i]->{'foto_proyek_tgl'};
            $judul = $data_dokfoto_s[$i]->{'foto_proyek_keterangan'};

            $d_cek_dokfoto_l = $this->db->query("select * from simpro_tbl_sketsa_proyek where proyek_id = $proyek_id and foto_proyek_tgl = '$tanggal' and foto_proyek_keterangan = '$judul'");
            if ($d_cek_dokfoto_l->num_rows() == 0) {
              $client->query("delete from tbl_sketsa_proyek where no_spk = '$no_spk' and foto_proyek_tgl = '$tanggal' and foto_proyek_keterangan = '$judul'");
            }
          }
        }
      }

      if ($arg == 'approve') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_approve_s = json_decode($client->query("select * from tbl_approve where no_spk = '$no_spk'"))->{'data'};
        if ($d_approve_s != '') {
          for ($i=0; $i < count($d_approve_s); $i++) { 
            $form_approve = $d_approve_s[$i]->{'form_approve'};
            $tgl_approve = $d_approve_s[$i]->{'tgl_approve'};
            $username = $d_approve_s[$i]->{'username'};
            $uids = $this->get_data('simpro_tbl_user','user_id','user_name',$d_approve_s[$i]->{'username'});

            $arr_approve = array(
              'proyek_id' => $proyek_id,
              'tgl_approve' => $tgl_approve,
              'username' => $d_approve_s[$i]->{'username'},
              'user_id' => $this->get_data('simpro_tbl_user','user_id','user_name',$d_approve_s[$i]->{'username'}),
              'form_approve' => $form_approve,
              'kuncitutup' => $d_approve_s[$i]->{'kuncitutup'},
              'kuncibuka' => $d_approve_s[$i]->{'kuncibuka'},
              'status' => $d_approve_s[$i]->{'status'}
            );

            $q_cek_approve_l = $this->db->query("select * from simpro_tbl_approve where proyek_id = $proyek_id and tgl_approve = '$tgl_approve' and form_approve = '$form_approve' and user_id = $uids");
            if ($q_cek_approve_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_approve',$arr_approve);
            } else {
              $this->db->where(array('proyek_id' => $proyek_id,'form_approve' => $form_approve,'tgl_approve' => $tgl_approve, 'user_id'=>$uids));
              $this->db->update('simpro_tbl_approve',$arr_approve);
            }
          }
        }

        $q_approve_l = $this->db->query("select * from simpro_tbl_approve where proyek_id = $proyek_id");
        if ($q_approve_l->result()) {
          foreach ($q_approve_l->result() as $r_approve_l) {
            $q_cek_approve_s = json_decode($client->query("select count('a') as count from tbl_approve where no_spk = '$no_spk' and form_approve = '$r_approve_l->form_approve' and tgl_approve = '$r_approve_l->tgl_approve' and username = '$r_approve_l->username'"))->{'data'};
            if ($q_cek_approve_s != '') {
              for ($i=0; $i < count($q_cek_approve_s); $i++) { 
                if ($q_cek_approve_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_approve',array('proyek_id' => $proyek_id,'form_approve'=>$r_approve_l->form_approve,'tgl_approve'=>$r_approve_l->tgl_approve,'user_id'=>$r_approve_l->user_id));
                }
              }
            }
          }
        }
      }

      if ($arg == 'subbidang') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_subbidang_s = json_decode($client->query("select * from subbidang"))->{'data'};
        if ($d_subbidang_s != '') {
          for ($i=0; $i < count($d_subbidang_s); $i++) { 
            $subbidang_kode = $d_subbidang_s[$i]->{'subbidang_kode'};

            $arr_subbidang = array(
              'subbidang_kode' => $d_subbidang_s[$i]->{'subbidang_kode'},
              'subbidang_name' => $d_subbidang_s[$i]->{'subbidang_name'},
              'user_update' => $uid,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_update' => $divisi_id,
              'waktu_update' => $waktu_update
            );

            $q_cek_subbidang_l = $this->db->query("select * from simpro_tbl_subbidang where lower(trim(subbidang_kode)) = lower(trim('$subbidang_kode'))");
            if ($q_cek_subbidang_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_subbidang',$arr_subbidang);
            } else {
              $this->db->where(array('subbidang_kode' => $subbidang_kode));
              $this->db->update('simpro_tbl_subbidang',$arr_subbidang);
            }
          }
        }

        $d_subbidang_l = $this->db->get('simpro_tbl_subbidang');
        if ($d_subbidang_l->result()) {
          foreach ($d_subbidang_l->result() as $r_subbidang_l) {
            $q_cek_subbidang_s = json_decode($client->query("select count('a') as count from subbidang where lower(trim(subbidang_kode)) = lower(trim('$r_subbidang_l->subbidang_kode'))"))->{'data'};
            if ($q_cek_subbidang_s != '') {
              for ($i=0; $i < count($q_cek_subbidang_s); $i++) { 
                if ($q_cek_subbidang_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_subbidang',array('subbidang_kode' => $r_subbidang_l->subbidang_kode));
                }
              }
            }
          }
        }
      }

      if ($arg == 'input_pubklk') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_pubklk_s = json_decode($client->query("select * from tbl_pendukung"))->{'data'};
        if ($d_pubklk_s != '') {
          for ($i=0; $i < count($d_pubklk_s); $i++) { 
            $no_spk_p = $this->get_data('simpro_tbl_proyek','proyek_id','no_spk',$d_pubklk_s[$i]->{'no_spk'});

            $arr_pubklk = array(
              'proyek_id' => $this->get_data('simpro_tbl_proyek','proyek_id','no_spk',$d_pubklk_s[$i]->{'no_spk'}),
              'pu' => $d_pubklk_s[$i]->{'pu'},
              'bk' => $d_pubklk_s[$i]->{'bk'},
              'pu_bk' => $d_pubklk_s[$i]->{'pu_bk'}
            );

            $q_cek_pubklk_l = $this->db->query("select * from simpro_tbl_pendukung where proyek_id = $no_spk_p");
            if ($q_cek_pubklk_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_pendukung',$arr_pubklk);
            } else {
              $this->db->where(array('proyek_id' => $no_spk_p));
              $this->db->update('simpro_tbl_pendukung',$arr_pubklk);
            }
          }
        }

        $this->db->where(array('proyek_id' => $proyek_id));
        $d_pubklk_l = $this->db->get('simpro_tbl_pendukung');
        if ($d_pubklk_l->result()) {
          foreach ($d_pubklk_l->result() as $r_pubklk_l) {
            $proyek_id_p = $this->db->query("select no_spk from simpro_tbl_proyek where proyek_id = $r_pubklk_l->proyek_id")->row()->no_spk;
            $q_cek_pubklk_s = json_decode($client->query("select count('a') as count from tbl_pendukung where lower(trim(no_spk)) = lower(trim('$proyek_id_p'))"))->{'data'};
            if ($q_cek_pubklk_s != '') {
              for ($i=0; $i < count($q_cek_pubklk_s); $i++) { 
                if ($q_cek_pubklk_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_pendukung',array('proyek_id' => $r_pubklk_l->proyek_id));
                }
              }
            }
          }
        }
      }

      if ($arg == 'kalendar_kerja') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_calendar_kerja_s = json_decode($client->query("select * from tbl_calendar_kerja"))->{'data'};
        if ($d_calendar_kerja_s != '') {
          for ($i=0; $i < count($d_calendar_kerja_s); $i++) { 
            $cal_minggu = $d_calendar_kerja_s[$i]->{'cal_minggu'};
            $cal_bulan = $d_calendar_kerja_s[$i]->{'cal_bulan'};
            $cal_tahun = $d_calendar_kerja_s[$i]->{'cal_tahun'};
            $cal_awal = $d_calendar_kerja_s[$i]->{'cal_awal'};
            $cal_akhir = $d_calendar_kerja_s[$i]->{'cal_akhir'};

            $arr_calendar_kerja = array(
              'cal_minggu' => $d_calendar_kerja_s[$i]->{'cal_minggu'},
              'cal_bulan' => $d_calendar_kerja_s[$i]->{'cal_bulan'},
              'cal_tahun' => $d_calendar_kerja_s[$i]->{'cal_tahun'},
              'cal_awal' => $d_calendar_kerja_s[$i]->{'cal_awal'},
              'cal_akhir' => $d_calendar_kerja_s[$i]->{'cal_akhir'},
              'user_id' => $uid,
              'tgl_update' => $tgl_update,
              'ip_update' => $ip_update,
              'divisi_id' => $divisi_id,
              'waktu_update' => $waktu_update
            );

            $q_cek_calendar_kerja_l = $this->db->query("select * from simpro_tbl_calendar_kerja where 
              cal_minggu = $cal_minggu and
              cal_bulan = $cal_bulan and
              cal_tahun = $cal_tahun and
              cal_awal = '$cal_awal' and
              cal_akhir = '$cal_akhir'
            ");
            if ($q_cek_calendar_kerja_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_calendar_kerja',$arr_calendar_kerja);
            } 
            // else {
            //   $this->db->where(array('cal_minggu' => $cal_minggu));
            //   $this->db->update('simpro_tbl_calendar_kerja',$arr_calendar_kerja);
            // }
          }
        }

        $d_calendar_kerja_l = $this->db->get('simpro_tbl_calendar_kerja');
        if ($d_calendar_kerja_l->result()) {
          foreach ($d_calendar_kerja_l->result() as $r_calendar_kerja_l) {
            $q_cek_calendar_kerja_s = json_decode($client->query("select count('a') as count from tbl_calendar_kerja where 
              cal_minggu = $r_calendar_kerja_l->cal_minggu and
              cal_bulan = $r_calendar_kerja_l->cal_bulan and
              cal_tahun = $r_calendar_kerja_l->cal_tahun and
              cal_awal = '$r_calendar_kerja_l->cal_awal' and
              cal_akhir = '$r_calendar_kerja_l->cal_akhir'
            "))->{'data'};
            if ($q_cek_calendar_kerja_s != '') {
              for ($i=0; $i < count($q_cek_calendar_kerja_s); $i++) { 
                if ($q_cek_calendar_kerja_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_calendar_kerja',
                    array(
                    'cal_minggu' => $r_calendar_kerja_l->cal_minggu,
                    'cal_bulan' => $r_calendar_kerja_l->cal_bulan,
                    'cal_tahun' => $r_calendar_kerja_l->cal_tahun,
                    'cal_awal' => $r_calendar_kerja_l->cal_awal,
                    'cal_akhir' => $r_calendar_kerja_l->cal_akhir
                    )
                  );
                }
              }
            }
          }
        }
      }

      if ($arg == 'toko') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $sql_toko_server = "select
        toko_kode,
        toko_nama,
        toko_alamat,
        toko_contact,
        toko_telp,
        toko_produk,
        user_update,
        tgl_update,
        ip_update,
        divisi_update,
        waktu_update
        from
        tbl_toko";

        $data_toko_server = json_decode($client->query($sql_toko_server))->{'data'};
        if ($data_toko_server != '') {
          $jum = count($data_toko_server);
          $n = 1;
          for ($i=0; $i < count($data_toko_server); $i++) { 
            $toko_kode = $data_toko_server[$i]->toko_kode;
            $toko_nama = $data_toko_server[$i]->toko_nama;
            $toko_alamat = $data_toko_server[$i]->toko_alamat;
            $toko_contact = $data_toko_server[$i]->toko_contact;
            $toko_telp = $data_toko_server[$i]->toko_telp;
            $toko_produk = $data_toko_server[$i]->toko_produk;
            $tgl_update_l = $data_toko_server[$i]->tgl_update;
            $waktu_update_l = $data_toko_server[$i]->waktu_update;

            $get_cek_toko_local = $this->db->query("select * from simpro_tbl_toko where trim(toko_kode) = trim('$toko_kode')");
            // echo "select count('a') as count from simpro_tbl_toko where trim(toko_kode) = trim('$toko_kode')";
            // echo 'jml ==> '. ($n / $jum) * 100 .'<br>';
            $n++;
            if ($get_cek_toko_local->num_rows() == 0) {
              $sql_insert_toko_local = "
              insert into simpro_tbl_toko (
                toko_kode,
                toko_nama,
                toko_alamat,
                toko_contact,
                toko_telp,
                toko_produk,
                user_id,
                tgl_update,
                ip_update,
                divisi_id,
                waktu_update
              ) values (
                '$toko_kode',
                '$toko_nama',
                '$toko_alamat',
                '$toko_contact',
                '$toko_telp',
                '$toko_produk',
                '".$this->session->userdata('uid')."',
                '$tgl_update_l',
                '$ip_update',
                '".$this->session->userdata('divisi_id')."',
                '$waktu_update_l'
              )";
              
              $this->db->query($sql_insert_toko_local);
            } else {
              $sql_update_toko_local = "
              update simpro_tbl_toko set
                toko_nama = '$toko_nama',
                toko_alamat = '$toko_alamat',
                toko_contact  = '$toko_contact',
                toko_telp = '$toko_telp',
                toko_produk = '$toko_produk',
                user_id = '".$this->session->userdata('uid')."',
                tgl_update  = '$tgl_update_l',
                ip_update = '$ip_update',
                divisi_id = '".$this->session->userdata('divisi_id')."',
                waktu_update = '$waktu_update_l'
              where
                toko_kode = '$toko_kode'
              ";

              $this->db->query($sql_update_toko_local);
            }
          }
        }

        $get_toko_local = $this->db->query("select * from simpro_tbl_toko");
        if ($get_toko_local->result()) {
          foreach ($get_toko_local->result() as $r_toko_local) {
            $get_cek_toko_server = json_decode($client->query("select count('a') as count from tbl_toko where toko_kode = '$r_toko_local->toko_kode'"))->{'data'}[0]->{'count'};
            if ($get_cek_toko_server == 0) {
              $this->db->query("delete from simpro_tbl_toko where toko_id = $r_toko_local->toko_id");
            }
          }
        }
      }

      if ($arg == 'target_dashboard') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $d_target_dashboard_s = json_decode($client->query("select * from tbl_target_dashboard"))->{'data'};
        if ($d_target_dashboard_s != '') {
          for ($i=0; $i < count($d_target_dashboard_s); $i++) { 
            $tahun = $d_target_dashboard_s[$i]->{'tahun'};
            $kategori = $d_target_dashboard_s[$i]->{'kategori'};

            // echo $tahun."->".$kategori."->".$divisi."<br>";
            $arr_target_dashboard = array(
              'tahun' => $d_target_dashboard_s[$i]->{'tahun'},
              'kategori' => $d_target_dashboard_s[$i]->{'kategori'},
              'jumlah' => $d_target_dashboard_s[$i]->{'jumlah'}
            );

            $q_cek_target_dashboard_l = $this->db->query("select * from simpro_tbl_target_dashboard where 
              tahun = $tahun and
              kategori = '$kategori'
            ");
            if ($q_cek_target_dashboard_l->num_rows() == 0) {
              $this->db->insert('simpro_tbl_target_dashboard',$arr_target_dashboard);
            } else {
              $this->db->where(
                array(
                  'tahun' => $tahun,
                  'kategori' => $kategori
                )
              );
              $this->db->update('simpro_tbl_target_dashboard',$arr_target_dashboard);
            }
          }
        }

        $d_target_dashboard_l = $this->db->get('simpro_tbl_target_dashboard');
        if ($d_target_dashboard_l->result()) {
          foreach ($d_target_dashboard_l->result() as $r_target_dashboard_l) {
            $q_cek_target_dashboard_s = json_decode($client->query("select count('a') as count from tbl_target_dashboard where 
              tahun = $r_target_dashboard_l->tahun and
              kategori = '$r_target_dashboard_l->kategori'
            "))->{'data'};
            if ($q_cek_target_dashboard_s != '') {
              for ($i=0; $i < count($q_cek_target_dashboard_s); $i++) { 
                if ($q_cek_target_dashboard_s[$i]->{'count'} == 0) {
                  $this->db->delete('simpro_tbl_target_dashboard',
                    array(
                    'tahun' => $r_target_dashboard_l->tahun,
                    'kategori' => $r_target_dashboard_l->kategori
                    )
                  );
                }
              }
            }
          }
        }
      }

      if ($arg == 'rencana_kerja') {

        $this->insert_log($arg,$uname_update,$tgl_update,$ip_update,$divisi_update,$waktu_update,$no_spk);

        $get_rab_rrk = $this->db->query("select distinct(tahap_tanggal_kendali) from simpro_tbl_total_rkp where proyek_id = $proyek_id");
        if ($get_rab_rrk->result()) {
          foreach ($get_rab_rrk->result() as $r_rab_rrk) {
            $tgl_rab = $r_rab_rrk->tahap_tanggal_kendali;

            $sql_rrk = "with j as (SELECT
            b.tahap_kode_kendali,
            (
            (CASE WHEN b.tahap_volume_kendali is null
            THEN 0
            ELSE b.tahap_volume_kendali
            END) +
            (CASE WHEN b.tahap_volume_kendali_new is null
            THEN 0
            ELSE b.tahap_volume_kendali_new
            END) -
            (CASE WHEN b.tahap_volume_kendali_kurang is null
            THEN 0
            ELSE b.tahap_volume_kendali_kurang
            END)
            ) as vol_kk,
            CASE WHEN b.tahap_harga_satuan_kendali is null
            THEN 0
            ELSE b.tahap_harga_satuan_kendali
            END,
            (
            ((CASE WHEN b.tahap_volume_kendali is null
            THEN 0
            ELSE b.tahap_volume_kendali
            END) +
            (CASE WHEN b.tahap_volume_kendali_new is null
            THEN 0
            ELSE b.tahap_volume_kendali_new
            END) -
            (CASE WHEN b.tahap_volume_kendali_kurang is null
            THEN 0
            ELSE b.tahap_volume_kendali_kurang
            END)) *
            (CASE WHEN b.tahap_harga_satuan_kendali is null
            THEN 0
            ELSE b.tahap_harga_satuan_kendali
            END)
            ) as jml_rkp_kini
            FROM
            simpro_tbl_total_rkp a
            JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
            WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab'
            ORDER BY b.tahap_kode_kendali)

            SELECT
            b.tahap_kode_kendali,
            b.tahap_nama_kendali,
            (select satuan_nama from simpro_tbl_satuan where satuan_id = b.tahap_satuan_kendali) as tahap_satuan_kendali,
            (select no_spk from simpro_tbl_proyek where proyek_id = a.proyek_id) as no_spk,
            (
            (CASE WHEN b.tahap_volume_kendali is null
            THEN 0
            ELSE b.tahap_volume_kendali
            END) +
            (CASE WHEN b.tahap_volume_kendali_new is null
            THEN 0
            ELSE b.tahap_volume_kendali_new
            END) -
            (CASE WHEN b.tahap_volume_kendali_kurang is null
            THEN 0
            ELSE b.tahap_volume_kendali_kurang
            END)
            ) as tahap_volume_kendali,
            (
            case when (
            select
            sum(jml_rkp_kini)
            from
            j
            where
            left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
            group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))
            ) = 0
            then 0
            else
            (
            select
            sum(jml_rkp_kini)
            from
            j
            where
            left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
            group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))
            )/
            ((CASE WHEN b.tahap_volume_kendali is null
            THEN 0
            ELSE b.tahap_volume_kendali
            END) +
            (CASE WHEN b.tahap_volume_kendali_new is null
            THEN 0
            ELSE b.tahap_volume_kendali_new
            END) -
            (CASE WHEN b.tahap_volume_kendali_kurang is null
            THEN 0
            ELSE b.tahap_volume_kendali_kurang
            END)) end
            )  as tahap_harga_satuan_kendali,
            (
            select
            sum(jml_rkp_kini)
            from
            j
            where
            left(j.tahap_kode_kendali,length(b.tahap_kode_kendali)) = b.tahap_kode_kendali
            group by left(j.tahap_kode_kendali,length(b.tahap_kode_kendali))
            ) as tahap_total_kendali,
            b.tahap_kode_induk_kendali,
            a.tahap_tanggal_kendali,
            CASE WHEN a.tahap_volume_bln1 is null
            THEN 0
            ELSE a.tahap_volume_bln1
            END,
            (
            (CASE WHEN a.tahap_volume_bln1 is null
            THEN 0
            ELSE a.tahap_volume_bln1
            END) *
            (CASE WHEN b.tahap_harga_satuan_kendali is null
            THEN 0
            ELSE b.tahap_harga_satuan_kendali
            END)
            ) as tahap_jumlah_bln1,
            CASE WHEN a.tahap_volume_bln2 is null
            THEN 0
            ELSE a.tahap_volume_bln2
            END,
            (
            (CASE WHEN a.tahap_volume_bln2 is null
            THEN 0
            ELSE a.tahap_volume_bln2
            END) *
            (CASE WHEN b.tahap_harga_satuan_kendali is null
            THEN 0
            ELSE b.tahap_harga_satuan_kendali
            END)
            ) as tahap_jumlah_bln2,
            CASE WHEN a.tahap_volume_bln3 is null
            THEN 0
            ELSE a.tahap_volume_bln3
            END,
            (
            (CASE WHEN a.tahap_volume_bln3 is null
            THEN 0
            ELSE a.tahap_volume_bln3
            END) *
            (CASE WHEN b.tahap_harga_satuan_kendali is null
            THEN 0
            ELSE b.tahap_harga_satuan_kendali
            END)
            ) as tahap_jumlah_bln3,
            CASE WHEN a.tahap_volume_bln4 is null
            THEN 0
            ELSE a.tahap_volume_bln4
            END,
            (
            (CASE WHEN a.tahap_volume_bln4 is null
            THEN 0
            ELSE a.tahap_volume_bln4
            END) *
            (CASE WHEN b.tahap_harga_satuan_kendali is null
            THEN 0
            ELSE b.tahap_harga_satuan_kendali
            END)
            ) as tahap_jumlah_bln4
            FROM
            simpro_tbl_total_rkp a
            JOIN simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini
            WHERE a.proyek_id = $proyek_id AND a.tahap_tanggal_kendali = '$tgl_rab'";

            $get_rrk = $this->db->query($sql_rrk);
            if ($get_rrk->result()) {
              foreach ($get_rrk->result() as $r_get_rrk) {
                $get_rrk_server = json_decode($client->query("select count('a') as count from tbl_total_rkp where no_spk = '$no_spk' and tahap_tanggal_kendali = '$r_get_rrk->tahap_tanggal_kendali' and tahap_kode_kendali = '$r_get_rrk->tahap_kode_kendali'"));
                $data_rrk_server = $get_rrk_server->{'data'};
                if ($data_rrk_server != '') {
                  foreach ($data_rrk_server as $r_rrk_server) {
                    if ($r_rrk_server->{'count'} == 0) {
                      $sql_insert_rrk = "
                      insert into tbl_total_rkp (
                        tahap_kode_kendali,
                        tahap_nama_kendali,
                        tahap_satuan_kendali,
                        no_spk,
                        tahap_volume_kendali,
                        tahap_harga_satuan_kendali,
                        tahap_total_kendali,
                        tahap_kode_induk_kendali,
                        tahap_tanggal_kendali,
                        tahap_volume_bln1,
                        tahap_jumlah_bln1,
                        tahap_volume_bln2,
                        tahap_jumlah_bln2,
                        tahap_volume_bln3,
                        tahap_jumlah_bln3,
                        tahap_volume_bln4,
                        tahap_jumlah_bln4,
                        user_update,
                        tgl_update,
                        ip_update,
                        divisi_update,
                        waktu_update
                      ) values (
                        '$r_get_rrk->tahap_kode_kendali',
                        '$r_get_rrk->tahap_nama_kendali',
                        '$r_get_rrk->tahap_satuan_kendali',
                        '$r_get_rrk->no_spk',
                        '$r_get_rrk->tahap_volume_kendali',
                        '$r_get_rrk->tahap_harga_satuan_kendali',
                        '$r_get_rrk->tahap_total_kendali',
                        '$r_get_rrk->tahap_kode_induk_kendali',
                        '$r_get_rrk->tahap_tanggal_kendali',
                        '$r_get_rrk->tahap_volume_bln1',
                        '$r_get_rrk->tahap_jumlah_bln1',
                        '$r_get_rrk->tahap_volume_bln2',
                        '$r_get_rrk->tahap_jumlah_bln2',
                        '$r_get_rrk->tahap_volume_bln3',
                        '$r_get_rrk->tahap_jumlah_bln3',
                        '$r_get_rrk->tahap_volume_bln4',
                        '$r_get_rrk->tahap_jumlah_bln4',
                        '$uname_update',
                        '$tgl_update',
                        '$ip_update',
                        '$divisi_update',
                        '$waktu_update'
                      )
                      ";

                      $client->query($sql_insert_rrk);
                    } else {
                      $sql_update_rrk = "
                      update tbl_total_rkp set
                      tahap_nama_kendali = '$r_get_rrk->tahap_nama_kendali',
                      tahap_satuan_kendali = '$r_get_rrk->tahap_satuan_kendali',
                      tahap_volume_kendali = '$r_get_rrk->tahap_volume_kendali',
                      tahap_harga_satuan_kendali = '$r_get_rrk->tahap_harga_satuan_kendali',
                      tahap_total_kendali  = '$r_get_rrk->tahap_total_kendali',
                      tahap_kode_induk_kendali = '$r_get_rrk->tahap_kode_induk_kendali',
                      tahap_volume_bln1  = '$r_get_rrk->tahap_volume_bln1',
                      tahap_jumlah_bln1  = '$r_get_rrk->tahap_jumlah_bln1',
                      tahap_volume_bln2  = '$r_get_rrk->tahap_volume_bln2',
                      tahap_jumlah_bln2  = '$r_get_rrk->tahap_jumlah_bln2',
                      tahap_volume_bln3  = '$r_get_rrk->tahap_volume_bln3',
                      tahap_jumlah_bln3  = '$r_get_rrk->tahap_jumlah_bln3',
                      tahap_volume_bln4  = '$r_get_rrk->tahap_volume_bln4',
                      tahap_jumlah_bln4  = '$r_get_rrk->tahap_jumlah_bln4',
                      user_update  = '$uname_update',
                      tgl_update = '$tgl_update',
                      ip_update  = '$ip_update',
                      divisi_update  = '$divisi_update',
                      waktu_update  = '$waktu_update'
                      where                      
                      no_spk = '$r_get_rrk->no_spk' and
                      tahap_tanggal_kendali  = '$r_get_rrk->tahap_tanggal_kendali' and
                      tahap_kode_kendali = '$r_get_rrk->tahap_kode_kendali'
                      ";

                      $client->query($sql_update_rrk);
                    }
                  }
                }
              }
            }
          }
        }
        
        $get_tgl_rab_rrk = json_decode($client->query("select * from tbl_total_rkp where no_spk = '$no_spk'"));
        $data_tgl_rab_server = $get_tgl_rab_rrk->{'data'};
        if ($data_tgl_rab_server!='') {
          for ($i=0; $i < count($data_tgl_rab_server); $i++) { 
            $tgl_rab = $data_tgl_rab_server[$i]->{'tahap_tanggal_kendali'};
            $kode = $data_tgl_rab_server[$i]->{'tahap_kode_kendali'};

            $get_rrk_local = $this->db->query("select * from simpro_tbl_total_rkp a join simpro_tbl_kontrak_terkini b on a.kontrak_terkini_id = b.id_kontrak_terkini where a.proyek_id = $proyek_id and a.tahap_tanggal_kendali = '$tgl_rab' and b.tahap_kode_kendali = '$kode'");
            if ($get_rrk_local->num_rows() == 0) {
              $client->query("delete from tbl_total_rkp where no_spk = '$no_spk' and tahap_tanggal_kendali = '$tgl_rab' and tahap_kode_kendali = '$kode'");
            }
          }
        }
      }   

	}

  function get_ip()
  {
    // echo "<script type='text/javascript'>window.location = 'http://localhost/simprod_lama/get_ip.php';</script>";
  }

  function retrieve_ip()
  {
    if ($this->input->get('ip')) {
      $ip = $this->input->get('ip');
      echo $ip;
    }
  }

  function is_connected()
  {
      $url_con = $this->get_url('con');
      if ($this->is_valid_url($url_con))
      {
          $con = true;
      }
      else
      {
          $con = false;
      }
      echo json_encode($con);
  }

  function is_valid_url($url)
  {
      if (!($url = @parse_url($url)))
      {
          return false;
      }
   
      $url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
      $url['path'] = (!empty($url['path'])) ? $url['path'] : '/';
      $url['path'] .= (isset($url['query'])) ? "?$url[query]" : '';
   
      if (isset($url['host']) AND $url['host'] != @gethostbyname($url['host']))
      {
          if (PHP_VERSION >= 5)
          {
              $headers = @implode('', @get_headers("$url[scheme]://$url[host]:$url[port]$url[path]"));
          }
          else
          {
              if (!($fp = @fsockopen($url['host'], $url['port'], $errno, $errstr, 10)))
              {
                  return false;
              }
              fputs($fp, "HEAD $url[path] HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
              $headers = fread($fp, 4096);
              fclose($fp);
          }
          return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
      }
      return false;
  }

  function get_data($tbl="",$select="",$where="",$param="")
  {
    $this->db->select($select);
    $this->db->where(array('lower(trim('.$where.'))' => strtolower(trim($param))));
    $data = $this->db->get($tbl);
    if ($data->result()) {
      $row = $data->row()->$select;
    } else {
      $row = 0;
    }

    return $row;
  }

  function get_data_from_id($tbl="",$select="",$where="",$param="")
  {
    $this->db->select($select);
    $this->db->where(array($where => $param));
    $data = $this->db->get($tbl);
    if ($data->result()) {
      $row = $data->row()->$select;
    } else {
      $row = '';
    }

    return $row;
  }

}