<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class New_transaction extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
       
        $id_hak_akses = $this->session->userdata('id_hak_akses'); 
            if (!in_array(3,$this->session->userdata('id_hak_akses'))) {
                redirect('auth/login/kick');
            }
    }



	public function index()
	{

        $data['judul'] ='Transaksi Baru';
        $data['deskripsi'] ='Transaksi Baru pada GRO';
   
        $peralatan = $this->db->query("SELECT  id_peralatan, nama_peralatan, biaya_sewa, status, kode_peralatan, id_akun_pendapatan  from master_peralatan where status='1'")->result_array();
        $data['peralatan']    = $peralatan;
        $data['modal']    = $this->load->view('user/gro/transaction/modal', $data, true);
		$this->template->load('template/user_adminlte','user/gro/transaction/index', $data);
	}


    public function form_transaction()
    {
        $fasilitas = $this->input->post('fasilitas');
        if ($fasilitas=='Gym') {
            $q = $this->db->query("SELECT id_jenis_member, jenis_member from master_gym where status='1'")->result_array();
            $q_member = $this->db->query("SELECT  kode_unik_member,nama,alamat,no_hp,email,foto,fasilitas  from member where status='Aktif'")->result_array();
            $data['jenis_member'] = $q;
            $data['member'] = $q_member;
            $this->load->view('/user/gro/transaction/form/gym', $data);
        }
        elseif ($fasilitas=='Gym - Special Membership') {
            $q = $this->db->query("SELECT id_jenis_member, jenis_member from master_gym_spesial where status='1'")->result_array();
            $q_member = $this->db->query("SELECT  kode_unik_member,nama,alamat,no_hp,email,foto,fasilitas  from member where status='Aktif'")->result_array();
            $data['jenis_member'] = $q;
            $data['member'] = $q_member;
            $this->load->view('/user/gro/transaction/form/gym_spesial', $data);
        }
        elseif ($fasilitas=='Swimming - Membership') {
            $q = $this->db->query("SELECT id_swimming, jenis_member from master_swimming_bulanan where status='1'")->result_array();
            $q_member = $this->db->query("SELECT  kode_unik_member,nama,alamat,no_hp,email,foto,fasilitas  from member where status='Aktif'")->result_array();
            $data['jenis_member'] = $q;
            $data['member'] = $q_member;
            $this->load->view('/user/gro/transaction/form/swimming_bulanan', $data);
        }
        elseif ($fasilitas=='Swimming - Club') {
            $q = $this->db->query("SELECT id_swimming, jenis_member from master_swimming_club where status='1'")->result_array();
            $q_member = $this->db->query("SELECT  kode_unik_member,nama,alamat,no_hp,email,foto,fasilitas  from member where status='Aktif'")->result_array();
            $data['jenis_member'] = $q;
            $data['member'] = $q_member;    
            $this->load->view('/user/gro/transaction/form/swimming_club', $data);
        }
        elseif ($fasilitas=='Swimming - Spesial Membership') {
            $q = $this->db->query("SELECT id_swimming, jenis_member from master_swimming_spesial where status='1'")->result_array();
            $q_member = $this->db->query("SELECT  kode_unik_member,nama,alamat,no_hp,email,foto,fasilitas  from member where status='Aktif'")->result_array();
            $data['jenis_member'] = $q;
            $data['member'] = $q_member;
            $this->load->view('/user/gro/transaction/form/swimming_spesial', $data);
        }
        elseif ($fasilitas=='Swimming - Harian') {
            $q = $this->db->query("SELECT  harga_dewasa, harga_anak2 from  master_swimming_harian")->row_array();
            $harga = [
                ['kategori'=>'Dewasa','harga'=>$q['harga_dewasa']],
                ['kategori'=>'Anak-anak','harga'=>$q['harga_anak2']],
            ];
            $q_member = $this->db->query("SELECT  kode_unik_member,nama,alamat,no_hp,email,foto,fasilitas  from member where status='Aktif'")->result_array();
            $data['biaya'] = $harga;
            $data['member'] = $q_member;
            $this->load->view('/user/gro/transaction/form/swimming_harian', $data);
        }
        elseif ($fasilitas=='Swimming - Pelajar') {
            $q = $this->db->query("SELECT  tingkat, id_swimming, biaya from  master_swimming_pelajar")->result_array();
        
            $data['jenis'] = $q;
            $this->load->view('/user/gro/transaction/form/swimming_pelajar', $data);
        }
        else if ($fasilitas=='Futsal - Harian') {
          
            $data['member'] = '';
            $this->load->view('/user/gro/transaction/form/futsal', $data);
        }
        else if ($fasilitas=='Pickle - Harian') {
          
            $data['member'] = '';
            $q_lapangan = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='Pickle'")->result_array();
            $data['lapangan'] = $q_lapangan;
            $this->load->view('/user/gro/transaction/form/pickle', $data);
        }
        else if ($fasilitas=='Badminton - Harian') {
          
            $data['member'] = '';
            $q_lapangan = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='Badminton'")->result_array();
            $data['lapangan'] = $q_lapangan;
            $this->load->view('/user/gro/transaction/form/badminton', $data);
        }
        else if ($fasilitas=='Private - Gym') {
          
            $data['member'] = '';
            $q = $this->db->query("SELECT id_private, jumlah_pertemuan from master_private where status='1' and fasilitas='Private Gym'")->result_array();
            $data['jenis_member'] = $q;
            $this->load->view('/user/gro/transaction/form/private_gym', $data);
        }
        else if ($fasilitas=='Private - Muaythai') {
          
            $data['member'] = '';
            $q = $this->db->query("SELECT id_private, jumlah_pertemuan from master_private where status='1' and fasilitas='Private Muaythai'")->result_array();
            $data['jenis_member'] = $q;
            $this->load->view('/user/gro/transaction/form/private_muaythai', $data);
        }
        else if ($fasilitas=='Private - Swimming') {
          
            $data['member'] = '';
            $q = $this->db->query("SELECT id_private, jumlah_pertemuan, nama_paket from master_private where status='1' and fasilitas='Private Swimming'")->result_array();
            $data['jenis_member'] = $q;
            $this->load->view('/user/gro/transaction/form/private_swimming', $data);
        }
        // else if ($fasilitas=='Les - Futsal Academy') {
          
        //     $data['member'] = '';
        //     $q = $this->db->query("SELECT id_private, jumlah_pertemuan, nama_paket from master_private where status='1' and fasilitas='Private Swimming'")->result_array();
        //     $data['jenis_member'] = $q;
        //     $this->load->view('/user/gro/transaction/form/private_swimming', $data);
        // }
        else if ($fasilitas=='Futsal - Turnamen') {
            
            $q = $this->db->query("SELECT id_futsal_turnamen, paket, harga, include_kebersihan, include_sound_system from master_futsal_turnamen where status='1'")->result_array();
            $q_biaya_tambahan = $this->db->query("SELECT biaya_kebersihan, biaya_sound_system from futsal_turnamen_biaya_tambahan")->row_array();
            $data['futsal'] = $q;
            $data['biaya_tambahan'] = $q_biaya_tambahan;
            $this->load->view('/user/gro/transaction/form/futsal_turnamen', $data);
        }
        else if ($fasilitas=='Badminton - Turnamen') {
            
            $q = $this->db->query("SELECT id_badminton_turnamen, paket, harga, include_kebersihan, include_sound_system from master_badminton_turnamen where status='1'")->result_array();
            $q_biaya_tambahan = $this->db->query("SELECT biaya_kebersihan, biaya_sound_system from badminton_turnamen_biaya_tambahan")->row_array();
            $data['badminton'] = $q;
            $data['biaya_tambahan'] = $q_biaya_tambahan;
            $q_lapangan = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='Badminton'")->result_array();
            $data['lapangan'] = $q_lapangan;
            $this->load->view('/user/gro/transaction/form/badminton_turnamen', $data);
        }

        else if ($fasilitas=='Pickle - Turnamen') {
            
            $q = $this->db->query("SELECT id_pickle_turnamen, paket, harga, include_kebersihan, include_sound_system from master_pickle_turnamen where status='1'")->result_array();
            $q_biaya_tambahan = $this->db->query("SELECT biaya_kebersihan, biaya_sound_system from pickle_turnamen_biaya_tambahan")->row_array();
            $data['pickle'] = $q;
            $data['biaya_tambahan'] = $q_biaya_tambahan;
            $q_lapangan = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='pickle'")->result_array();
            $data['lapangan'] = $q_lapangan;
            $this->load->view('/user/gro/transaction/form/pickle_turnamen', $data);
        }
        else if ($fasilitas=='Futsal - Member Bulanan') {
            $q_delete = $this->db->query("DELETE from jadwal_futsal where status='Set'");
            $q = $this->db->query("SELECT diskon from diskon_member_futsal")->row_array();
            $data['diskon'] = $q['diskon'];
            $data['member'] = '';
            $this->load->view('/user/gro/transaction/form/futsal_bulanan', $data);
        }
        else if ($fasilitas=='Pickle - Member Bulanan') {
            $q_delete = $this->db->query("DELETE from jadwal_badminton where status='Set'");
            $q = $this->db->query("SELECT diskon from diskon_member_pickle")->row_array();
            $data['diskon'] = $q['diskon'];

            $q_lapangan = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='Pickle'")->result_array();
            $data['lapangan'] = $q_lapangan;
            $data['member'] = '';
            $this->load->view('/user/gro/transaction/form/pickle_bulanan', $data);
        }
        else if ($fasilitas=='Badminton - Member Bulanan') {
            $q_delete = $this->db->query("DELETE from jadwal_badminton where status='Set'");
            $q = $this->db->query("SELECT diskon from diskon_member_badminton")->row_array();
            $data['diskon'] = $q['diskon'];
            $data['member'] = '';
            $q_lapangan = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='Badminton'")->result_array();
            $data['lapangan'] = json_encode($q_lapangan);
            $this->load->view('/user/gro/transaction/form/badminton_bulanan', $data);
        }else{
            echo "Fitur belum tersedia";
        }
    }

    public function form_transaction_les()
    {
        $fasilitas = $this->input->post('fasilitas');
            // if ($fasilitas=='Les - Futsal Academy') {
          
            $id_les = $this->input->post('id_les');
            $q_les = $this->db->query("SELECT kelas_exclusive from master_les where id_les='$id_les'")->row_array();


            $data['member'] = '';
            $q = $this->db->query("SELECT id_les_bulanan, nama_paket, jumlah_pertemuan from master_les_bulanan where status='1' and id_les='$id_les'")->result_array();
            $q_regis = $this->db->query("SELECT  biaya_perlengkapan, keterangan_perlengkapan, biaya_registrasi  from master_les_registrasi where id_les='$id_les'")->row_array();
            $data['jenis_member'] = $q;
            $data['tanpa_perlengkapan'] = $q_regis['biaya_registrasi'].'|0|Tanpa Perlengkapan';
            $data['dengan_perlengkapan'] = $q_regis['biaya_registrasi'].'|'.$q_regis['biaya_perlengkapan'].'|Perlengkapan : '.$q_regis['keterangan_perlengkapan'];
            $data['les'] = $fasilitas;
            $data['id_les'] = $id_les;
            if ($q_les['kelas_exclusive']=='Ya') {
                $this->load->view('/user/gro/transaction/form/les_exclusive', $data);
            }else{
                $this->load->view('/user/gro/transaction/form/les', $data);
            }
           //  }
           // else{
           //      echo "Fitur belum tersedia <br>Di diskusikan dahulu";
           //  }
    }

    public function tes()
    {
        $dir = "/laragon/www/";
        opendir($dir);

// Open a directory, and read its contents
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($file = readdir($dh)) !== false){
      echo "filename:" . $file . "<br>";
    }
    closedir($dh);
  }
}
    }



    public function dt_student_card()
    {
        $data = [];
        $tgls = tgls();
         $no             = $_POST['start'];
         $start = $no;
         $length             = $_POST['length'];
         $key = $_POST['search']['value'];
         // untuk order by
         $order = $_POST['order'];
         $col =0; 
         $dir = "";
         if (!empty($order)) {
             foreach ($order as $o) {
                 $col = $o['column'];
                 $dir = $o['dir'];
             }
         }

         if ($dir!='asc' && $dir!='desc') {
             $dir='desc';
         }
         $valid_columns = [
            0=>'id_student_card',
         ];

         if (!isset($valid_columns[$col])) {
             $order= null;
         }else{
            $order = $valid_columns[$col];
         }

         if ($order!=null) {
             $order_by = "order by id_student_card desc, $order $dir";
             # code...
         }else{
             $order_by = "";

         }
         // untuk order by
         if ($key) {
            $q = $this->db->query("SELECT   id_student_card, nama,alamat,no_hp    from v_student_card where akhir_masa_aktif > '$tgls' and (nama like'%$key%') limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT id_student_card, nama,alamat,no_hp from v_student_card where  akhir_masa_aktif > '$tgls' $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT id_student_card, nama,alamat,no_hp from v_student_card ")->num_rows();

        foreach ($q as $k => $v) {
            $no++;


            $row    = [];
            $row[]  = $no;
            $row[]  = $v['nama'];
            $row[]  = $v['alamat'];
            $row[]  = $v['no_hp'];
            // $row[]  = '<a href="'.$params.'/'.$parameter_tamu.'" target="_blank">'.$params.'/'.$parameter_tamu.'</a>';
            $row[]  = '
            <div class="btn-group">
            <a href="javascript:void(0)" data-dismiss="modal" onclick="pilih_student_card('."'".$v['id_student_card']."'".','."'".$v['nama']."'".','."'".$v['alamat']."'".','."'".$v['no_hp']."'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-check"></i></a>
            </div>';
        


                $data[] = $row;
            # code...
        }

           
            $output = [
                        "draw"              => $_POST['draw'],
                        "recordsTotal"      => $all_data,
                        "recordsFiltered"   => $all_data,
                        "data"              => $data,
                      ];

            echo json_encode($output);
        
    }







    public function metode_pembayaran()
    {
        
        $q = $this->db->query("SELECT * from master_metode_pembayaran where status = '1'")->result_array();
        echo json_encode($q);
    }









    public function dt_member_reg_online()
    {
        $params = $this->input->post('params');
        $data = [];
         $no             = $_POST['start'];
         $start = $no;
         $length             = $_POST['length'];
         $key = $_POST['search']['value'];
         // untuk order by
         $order = $_POST['order'];
         $col =0; 
         $dir = "";
         if (!empty($order)) {
             foreach ($order as $o) {
                 $col = $o['column'];
                 $dir = $o['dir'];
             }
         }

         if ($dir!='asc' && $dir!='desc') {
             $dir='desc';
         }
         $valid_columns = [
            0=>'id_member',
         ];

         if (!isset($valid_columns[$col])) {
             $order= null;
         }else{
            $order = $valid_columns[$col];
         }

         if ($order!=null) {
             $order_by = "order by id_member desc, $order $dir";
             # code...
         }else{
             $order_by = "";

         }
         // untuk order by
         if ($key) {
            $q = $this->db->query("SELECT nama, id_member, id_jenis_member, alamat, no_hp, pekerjaan, kode_unik_member from member  where reg_via='Online' and nama like '%$key%' limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT nama, id_member, id_jenis_member, alamat, no_hp, pekerjaan, kode_unik_member from member  where reg_via='Online'   $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT id_member from member where reg_via='Online'")->num_rows();

         $jm = $this->db->query("SELECT jenis_member, id_jenis_member from jenis_member")->result_array();
         $kumpul_jenis_member = [];
         foreach ($jm as $k => $v) {
             $kumpul_jenis_member[$v['id_jenis_member']] = $v['jenis_member'];
         }


        foreach ($q as $k => $v) {
            $no++;
            $parameter_tamu = str_replace(' ', '%20', $v['nama']);



            $row    = [];
            $row[]  = $no;
            $row[]  = $v['kode_unik_member'];
            $row[]  = $v['nama'];
            $row[]  = @$kumpul_jenis_member[$v['id_jenis_member']];
            $row[]  = $v['alamat'];
            $row[]  = $v['no_hp'];
            // $row[]  = $v['pekerjaan'];
            // $row[]  = '<a href="'.$params.'/'.$parameter_tamu.'" target="_blank">'.$params.'/'.$parameter_tamu.'</a>';
            $row[]  = '<a href="javascript:void(0)" onclick="preview_member('."'".$v['id_member']."'".')" class="btn btn-info btn-xs"><i class="fa fa-folder-open"></i></a>';
        


                $data[] = $row;
            # code...
        }
           
           
            $output = [
                        "draw"              => $_POST['draw'],
                        "recordsTotal"      => $all_data,
                        "recordsFiltered"   => $all_data,
                        "data"              => $data,
                      ];

            echo json_encode($output);
        
    }



    public function cek_id_member()
    {
        
            $kode = $this->input->post('kode');
            $q = $this->db->query("SELECT id_member
         from member where kode_unik_member='$kode'");
          
            $output =[
                'found'=>$q->num_rows(),
                'id_member'=>$q->row_array()['id_member'],
            ];
            echo json_encode($output);
    }



    public function submit_aktifkan_member()
    {

        $this->db->trans_start();
          $tgls = date('Y-m-d');
     $q_cek_order_ke = $this->db->query("SELECT max(order_ke) as order_terakhir from transaksi_member where tgl_transaksi='$tgls'")->row_array();
     $order_ke = $q_cek_order_ke['order_terakhir'] == '' ? 0 : $q_cek_order_ke['order_terakhir'];
     $next_order = $order_ke +1;

    $metode_pembayaran = $this->input->post('metode_pembayaran');
    $input_dibayar = $this->input->post('input_dibayar');
    $simpan_dibayar = $this->input->post('simpan_dibayar');
    $simpan_kembalian_pembayaran = $this->input->post('simpan_kembalian_pembayaran');
    $simpan_total = $this->input->post('simpan_total');
    $simpan_id_member = $this->input->post('simpan_id_member');
    $simpan_id_jenis_member = $this->input->post('simpan_id_jenis_member');
    $simpan_akhir_masa_aktif = $this->input->post('simpan_akhir_masa_aktif');
    $simpan_awal_masa_aktif = $this->input->post('simpan_awal_masa_aktif');
    $simpan_tgl_register = $this->input->post('simpan_tgl_register');
    $simpan_jam_register = $this->input->post('simpan_jam_register');
    $id_user = '';
    $data_transaksi = [
        // 'no_transaksi'=>$next_order,
        'id_metode_pembayaran'=>$metode_pembayaran,
        'id_user'=>$id_user,
        'id_member'=>$simpan_id_member,
        'tgl_transaksi'=>date('Y-m-d'),
        'jam_transaksi'=>date('H:i'),
        'order_ke'=>$next_order,
        'total'=>$simpan_total,
        'status'=>1,
        'dibayar'=>$simpan_dibayar,
        'kembalian '=>$simpan_kembalian_pembayaran,
    ];
    $this->db->insert('transaksi_member', $data_transaksi);
     $id_transaksi = $this->db->insert_id();
     $data_keanggotaan = [
        'id_member'=>$simpan_id_member,
        'id_jenis_member'=>$simpan_id_jenis_member,
        'include_register'=>'Ya',
        'id_transaksi_member'=>$id_transaksi,
        'tgl_daftar'=>$simpan_tgl_register,
        'jam_daftar'=>$simpan_jam_register,
        'awal_masa_aktif'=>$simpan_awal_masa_aktif,
        'akhir_masa_aktif'=>$simpan_akhir_masa_aktif,
        'id_metode_pembayaran'=>$metode_pembayaran,
        // 'status'=>$ffgfgdgdfgdf,
        'jenis_keanggotaan'=>'Gym',
     ];
    $this->db->insert('keanggotaan_member', $data_keanggotaan);
    $this->db->update('member', ['status'=>'Aktif'], ['id_member'=>$simpan_id_member]);

      if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
                 $output = [
                    'responcode'=>500,
                    'id_transaksi'=>$id_transaksi,
                    // 'message'=>'Synchronize Gagal. Ditemukan kesalahan pada aplikasi',
                ];
        }else{
            $this->db->trans_commit();
                 $output = [
                    'responcode'=>200,
                    'id_transaksi'=>$id_transaksi,
                    // 'message'=>'Keanggotaan member diper',
                ];

        }

        echo json_encode($output);
        
    }



    public function detail_member()
    {
        
            $id_member = $this->input->post('id_member');

            $tgl_sekarang = date('Y-m-d');
            $q = $this->db->query("SELECT *
         from member where id_member='$id_member'")->row_array();
            $id_jenis_member = $q['id_jenis_member'];
            $q_jenis_member = $this->db->query("SELECT id_jenis_member, jenis_member, biaya, masa_aktif, satuan_masa_aktif from master_gym ")->result_array();
            $kumpul_jenis_member = [];
            foreach ($q_jenis_member as $k => $v) {

                $masa_aktif = $v['masa_aktif'];
                $satuan_masa_aktif = $v['satuan_masa_aktif'];
                $pecah_tgll = explode('-', $q['tgll']);
                // $tahun_tgll = $pecah_tgll[0];
                $usia =  date('Y') - $pecah_tgll[0];

                if ($satuan_masa_aktif=='Hari') {
                    $tgl_masa_aktif    =date('Y-m-d', strtotime("+" .($masa_aktif-1). " day", strtotime($tgl_sekarang)));
                }else{
                    $tgl_masa_aktif    =date('Y-m-d', strtotime("+" .$masa_aktif. " month", strtotime($tgl_sekarang)));
                }




                 $kumpul_jenis_member[$v['id_jenis_member']] = [
                    'paket'=>$v['jenis_member'],
                    'biaya'=>$v['biaya'],
                    'masa_aktif'=>$masa_aktif.' '.$satuan_masa_aktif,
                    'tgl_akhir_masa_aktif'=>$tgl_masa_aktif,
                    'tgl_awal_masa_aktif'=>$tgl_sekarang,


                ];
            }
            $output =[
                'member'=>$q,
                'paket'=>$kumpul_jenis_member[$id_jenis_member]['paket'],
                'biaya'=>$kumpul_jenis_member[$id_jenis_member]['biaya'],
                'masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                'tgl_akhir_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                'tgl_awal_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                'id_jm'=>$id_jenis_member,
                'usia'=>$usia,

            ];
            echo json_encode($output);
    }



}
