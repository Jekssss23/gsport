<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $id_hak_akses = $this->session->userdata('id_hak_akses'); 
        //   if ($id_hak_akses!='3') {
        //     redirect('auth/login/kick');
        // }
    }
    public function index()
    {
        $tgl= $this->input->get('tgl');
        $pecah = explode('-', $tgl);
        $data['bulan'] = $pecah[1];
        $data['tahun'] = $pecah[0];
        $data['judul'] = 'Booking';
        $data['deskripsi'] = 'Booking Futsal / Badminton tanggal : '.show_tanggal($tgl);

        $data['breadchumb'] = '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tambah">
                Tambah Metode Pembayaran
              </button>';
        

            $q_lapangan_pickle = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='Pickle'")->result_array();
            $data['lapangan_pickle'] = $q_lapangan_pickle;
            $q_lapangan_badminton = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='Pickle'")->result_array();
            $data['lapangan_badminton'] = $q_lapangan_badminton;
        $data['modal']                      = $this->load->view('user/gro/booking/modal', $data, true);
        $this->template->load('template/user_adminlte','user/gro/booking/index', $data);
        // $this->load->view('template/user_adminlte');
    }
    public function jadwal_futsal()
    {
        $tgl = $this->input->post('tgl');
        $lapangan = $this->input->post('lapangan');
        $q = $this->db->query("SELECT jf.id_detail_booking_futsal, jf.id_identitas_order_futsal ,jf.jam_main, jf.fasilitas, jf.status, iof.nama, iof.no_hp from jadwal_futsal jf left join identitas_order_futsal iof on jf.id_identitas_order_futsal = iof.id_booking_futsal where jf.tgl_main='$tgl' and jf.lapangan='$lapangan' and jf.status !='Cancel'")->result_array();
        $kumpul_jam_terpakai= [];
        $kumpul_pembooking = [];
        foreach ($q as $k => $v) {
            $kumpul_jam_terpakai[] = $v['jam_main'];
        $kumpul_pembooking[$v['jam_main']] = [
            'pembooking'=>$v['status'].'<br>'.$v['fasilitas'].'<br>'.$v['nama'].' - '.$v['no_hp'],
            'warna'=>$v['status'] == 'Booking' ? 'background: aqua ' : 'background: #e2baf5 ',
            'id'=>$v['id_detail_booking_futsal'] ,
            'id_order'=>$v['id_identitas_order_futsal'] ,
        ];
        }
        $kumpul_jam_tersedia = [];
        $kumpul_harga = [];

        for ($i=7; $i < 24 ; $i++) { 
            if ($i==23) {
                $jam_jext = '00';
                # code...
            }else{
                $jam_jext = $i+1;

            }

          
            if ($i<10) {
                if ($jam_jext<10) {
                    # code...
                   $jam = '0'.$i.'.00-0'.$jam_jext.'.00';
                }else{
                   $jam = '0'.$i.'.00-'.$jam_jext.'.00';
                }
            }else{
               $jam = $i.'.00-'.$jam_jext.'.00';

            }


            if (!in_array($jam, $kumpul_jam_terpakai)) {
                $data = [
                    'id_jadwal'=>'',
                    'id_order'=>'',
                    'jam'=>show_tanggal($tgl).' / '.$jam,
                    'pembooking'=>'Booking Kosong',
                    'warna'=>'background: #dafbe9 ',
                ];
                array_push($kumpul_jam_tersedia, $data);
                # code...
            }else{
                $data = [
                    'id_jadwal'=>$kumpul_pembooking[$jam]['id'],
                    'id_order'=>$kumpul_pembooking[$jam]['id_order'],
                    'jam'=>show_tanggal($tgl).' / '.$jam,
                    'pembooking'=>$kumpul_pembooking[$jam]['pembooking'],
                    'warna'=>$kumpul_pembooking[$jam]['warna'],
                ];
                array_push($kumpul_jam_tersedia, $data);

            }
        }

        $show_jam_tersedia = array_chunk($kumpul_jam_tersedia, 3);
        echo json_encode($show_jam_tersedia);
    }







    public function jadwal_badminton()
    {
        $tgl = $this->input->post('tgl');
        $lapangan = $this->input->post('lapangan');
        $q = $this->db->query("SELECT jf.id_detail_booking_badminton, jf.id_identitas_order_badminton ,jf.jam_main, jf.fasilitas, jf.status, iof.nama, iof.no_hp from jadwal_badminton jf left join identitas_order_badminton iof on jf.id_identitas_order_badminton = iof.id_booking_badminton where jf.tgl_main='$tgl' and jf.lapangan='$lapangan' and jf.status !='Cancel'")->result_array();


        $kumpul_jam_terpakai= [];
        $kumpul_pembooking = [];
        foreach ($q as $k => $v) {
            $kumpul_jam_terpakai[] = $v['jam_main'];
        $kumpul_pembooking[$v['jam_main']] = [
            'pembooking'=>$v['status'].'<br>'.$v['fasilitas'].'<br>'.$v['nama'].' - '.$v['no_hp'],
            'warna'=>$v['status'] == 'Booking' ? 'background: aqua ' : 'background: #e2baf5 ',
            'id'=>$v['id_detail_booking_badminton'] ,
            'id_order'=>$v['id_identitas_order_badminton'] ,
        ];
        }
        $kumpul_jam_tersedia = [];
        $kumpul_harga = [];

        for ($i=7; $i < 24 ; $i++) { 
            if ($i==23) {
                $jam_jext = '00';
                # code...
            }else{
                $jam_jext = $i+1;

            }

          
            if ($i<10) {
                if ($jam_jext<10) {
                    # code...
                   $jam = '0'.$i.'.00-0'.$jam_jext.'.00';
                }else{
                   $jam = '0'.$i.'.00-'.$jam_jext.'.00';
                }
            }else{
               $jam = $i.'.00-'.$jam_jext.'.00';

            }


            if (!in_array($jam, $kumpul_jam_terpakai)) {
                $data = [
                    'id_jadwal'=>'',
                    'id_order'=>'',
                    'jam'=>show_tanggal($tgl).' / '.$jam,
                    'pembooking'=>'Booking Kosong',
                    'warna'=>'background: #dafbe9 ',
                ];
                array_push($kumpul_jam_tersedia, $data);
                # code...
            }else{
                $data = [
                    'id_jadwal'=>$kumpul_pembooking[$jam]['id'],
                    'id_order'=>$kumpul_pembooking[$jam]['id_order'],
                    'jam'=>show_tanggal($tgl).' / '.$jam,
                    'pembooking'=>$kumpul_pembooking[$jam]['pembooking'],
                    'warna'=>$kumpul_pembooking[$jam]['warna'],
                ];
                array_push($kumpul_jam_tersedia, $data);

            }
        }

        $show_jam_tersedia = array_chunk($kumpul_jam_tersedia, 3);
        echo json_encode($show_jam_tersedia);
    }




    public function jadwal_pickle()
    {
        $tgl = $this->input->post('tgl');
        $lapangan = $this->input->post('lapangan');
        $q = $this->db->query("SELECT jf.id_detail_booking_badminton, jf.id_identitas_order_badminton ,jf.jam_main, jf.fasilitas, jf.status, iof.nama, iof.no_hp from jadwal_badminton jf left join identitas_order_badminton iof on jf.id_identitas_order_badminton = iof.id_booking_badminton where jf.tgl_main='$tgl' and jf.lapangan='$lapangan' and jf.status !='Cancel'")->result_array();


        $kumpul_jam_terpakai= [];
        $kumpul_pembooking = [];
        foreach ($q as $k => $v) {
            $kumpul_jam_terpakai[] = $v['jam_main'];
        $kumpul_pembooking[$v['jam_main']] = [
            'pembooking'=>$v['status'].'<br>'.$v['fasilitas'].'<br>'.$v['nama'].' - '.$v['no_hp'],
            'warna'=>$v['status'] == 'Booking' ? 'background: aqua ' : 'background: #e2baf5 ',
            'id'=>$v['id_detail_booking_badminton'] ,
            'id_order'=>$v['id_identitas_order_badminton'] ,
        ];
        }
        $kumpul_jam_tersedia = [];
        $kumpul_harga = [];

        for ($i=7; $i < 24 ; $i++) { 
            if ($i==23) {
                $jam_jext = '00';
                # code...
            }else{
                $jam_jext = $i+1;

            }

          
            if ($i<10) {
                if ($jam_jext<10) {
                    # code...
                   $jam = '0'.$i.'.00-0'.$jam_jext.'.00';
                }else{
                   $jam = '0'.$i.'.00-'.$jam_jext.'.00';
                }
            }else{
               $jam = $i.'.00-'.$jam_jext.'.00';

            }


            if (!in_array($jam, $kumpul_jam_terpakai)) {
                $data = [
                    'id_jadwal'=>'',
                    'id_order'=>'',
                    'jam'=>show_tanggal($tgl).' / '.$jam,
                    'pembooking'=>'Booking Kosong',
                    'warna'=>'background: #dafbe9 ',
                ];
                array_push($kumpul_jam_tersedia, $data);
                # code...
            }else{
                $data = [
                    'id_jadwal'=>$kumpul_pembooking[$jam]['id'],
                    'id_order'=>$kumpul_pembooking[$jam]['id_order'],
                    'jam'=>show_tanggal($tgl).' / '.$jam,
                    'pembooking'=>$kumpul_pembooking[$jam]['pembooking'],
                    'warna'=>$kumpul_pembooking[$jam]['warna'],
                ];
                array_push($kumpul_jam_tersedia, $data);

            }
        }

        $show_jam_tersedia = array_chunk($kumpul_jam_tersedia, 3);
        echo json_encode($show_jam_tersedia);
    }

    public function dt_order_futsal()
    {
        $data = [];
         $no             = $_POST['start'];
         $tgl             = $_POST['tgl'];
         $jenis             = $_POST['jenis'];

         if ($jenis=='harian') {
             $fasilitas_fipilih = 'Futsal - Harian';
         }else if ($jenis=='bulanan') {
             $fasilitas_fipilih = 'Futsal - Member Bulanan';
         }else{
             $fasilitas_fipilih = 'Futsal - Turnamen';

         }

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
            0=>'id_booking_futsal',
         ];

         if (!isset($valid_columns[$col])) {
             $order= null;
         }else{
            $order = $valid_columns[$col];
         }

         if ($order!=null) {
             $order_by = "order by id_booking_futsal desc, $order $dir";
             # code...
         }else{
             $order_by = "";

         }
         // untuk order by
         if ($key) {
            $q = $this->db->query("SELECT  id_booking_futsal, paket, nama, no_hp, iof.fasilitas from identitas_order_futsal iof left join jadwal_futsal jf on iof.id_booking_futsal=jf.id_identitas_order_futsal where jf.tgl_main='$tgl' and jf.fasilitas='$fasilitas_fipilih' and (nama like'%$key%') group by jf.id_identitas_order_futsal  limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT  id_booking_futsal, paket, nama, no_hp, iof.fasilitas from identitas_order_futsal iof left join jadwal_futsal jf on iof.id_booking_futsal=jf.id_identitas_order_futsal where jf.tgl_main='$tgl' and jf.fasilitas='$fasilitas_fipilih' group by jf.id_identitas_order_futsal $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT  id_detail_booking_futsal from jadwal_futsal where fasilitas='$fasilitas_fipilih' and tgl_main='$tgl' group by id_identitas_order_futsal")->num_rows();

        foreach ($q as $k => $v) {
            $no++;


            $row    = [];
            $row[]  = $no;
            $row[]  = $v['nama'];
            $row[]  = $v['no_hp'];
            // $row[]  = '<a href="'.$params.'/'.$parameter_tamu.'" target="_blank">'.$params.'/'.$parameter_tamu.'</a>';
            if ($jenis=='harian') {
                $row[]  = $v['fasilitas'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_futsal('."'".$v['id_booking_futsal']."','harian'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';
                # code...
            }else if($jenis=='bulanan'){ 
                $row[]  = $v['fasilitas'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_futsal('."'".$v['id_booking_futsal']."','bulanan'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';

            }else{
                $row[]  = $v['fasilitas'].'<br>Paket : '.$v['paket'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_futsal('."'".$v['id_booking_futsal']."','turnamen'".')" class="btn btn-outline-danger btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';

            }
        


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


   public function dt_order_pickle()
    {
        $data = [];
         $no             = $_POST['start'];
         $tgl             = $_POST['tgl'];
         $jenis             = $_POST['jenis'];

         if ($jenis=='harian') {
             $fasilitas_fipilih = 'Pickle - Harian';
         }else if ($jenis=='bulanan') {
             $fasilitas_fipilih = 'Pickle - Member Bulanan';
         }else{
             $fasilitas_fipilih = 'Pickle - Turnamen';

         }

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
            0=>'id_booking_pickle',
         ];

         if (!isset($valid_columns[$col])) {
             $order= null;
         }else{
            $order = $valid_columns[$col];
         }

         if ($order!=null) {
             $order_by = "order by id_booking_pickle desc, $order $dir";
             # code...
         }else{
             $order_by = "";

         }
         // untuk order by
         if ($key) {
            $q = $this->db->query("SELECT  id_booking_pickle, paket, nama, no_hp, iof.fasilitas from identitas_order_pickle iof left join jadwal_badminton jf on iof.id_booking_pickle=jf.id_identitas_order_pickle where jf.tgl_main='$tgl' and jf.fasilitas='$fasilitas_fipilih' and (nama like'%$key%') group by jf.id_identitas_order_pickle  limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT  id_booking_pickle, paket, nama, no_hp, iof.fasilitas from identitas_order_pickle iof left join jadwal_badminton jf on iof.id_booking_pickle=jf.id_identitas_order_pickle where jf.tgl_main='$tgl' and jf.fasilitas='$fasilitas_fipilih' group by jf.id_identitas_order_pickle $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT  id_detail_booking_badminton from jadwal_badminton where fasilitas='$fasilitas_fipilih' and tgl_main='$tgl' group by id_identitas_order_pickle")->num_rows();

        foreach ($q as $k => $v) {
            $no++;


            $row    = [];
            $row[]  = $no;
            $row[]  = $v['nama'];
            $row[]  = $v['no_hp'];
            // $row[]  = '<a href="'.$params.'/'.$parameter_tamu.'" target="_blank">'.$params.'/'.$parameter_tamu.'</a>';
            if ($jenis=='harian') {
                $row[]  = $v['fasilitas'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_pickle('."'".$v['id_booking_pickle']."','harian'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';
                # code...
            }else if($jenis=='bulanan'){ 
                $row[]  = $v['fasilitas'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_pickle('."'".$v['id_booking_pickle']."','bulanan'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';

            }else{
                $row[]  = $v['fasilitas'].'<br>Paket : '.$v['paket'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_pickle('."'".$v['id_booking_pickle']."','turnamen'".')" class="btn btn-outline-danger btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';

            }
        


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




    public function dt_rekap_futsal()
    {
        $data = [];
         $no             = $_POST['start'];
         $tgl             = date('Y-m-d');//$_POST['tgl'];
         $jenis             = $_POST['jenis'];

         
             $fasilitas_fipilih = 'Futsal - Member Bulanan';
        
         $bulan             = $_POST['bulan'];
         $tahun             = $_POST['tahun'];

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
            0=>'id_booking_futsal',
         ];

         if (!isset($valid_columns[$col])) {
             $order= null;
         }else{
            $order = $valid_columns[$col];
         }

         if ($order!=null) {
             $order_by = "order by id_booking_futsal desc, $order $dir";
             # code...
         }else{
             $order_by = "";

         }
         // untuk order by


         if ($jenis=='semua') {
             $where = "";
         }else{
             $where = " and (month(jf.tgl_main)='$bulan' and year(jf.tgl_main)='$tahun')";

         }


         if ($key) {
            $q = $this->db->query("SELECT   id_booking_futsal, paket, nama, no_hp, iof.fasilitas from identitas_order_futsal iof left join jadwal_futsal jf on iof.id_booking_futsal=jf.id_identitas_order_futsal where jf.fasilitas='$fasilitas_fipilih' $where and (nama like'%$key%') group by jf.id_identitas_order_futsal  limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT  id_booking_futsal, paket, nama, no_hp, iof.fasilitas from identitas_order_futsal iof left join jadwal_futsal jf on iof.id_booking_futsal=jf.id_identitas_order_futsal where jf.fasilitas='$fasilitas_fipilih' $where group by jf.id_identitas_order_futsal $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT  id_booking_futsal, paket, nama, no_hp, iof.fasilitas from identitas_order_futsal iof left join jadwal_futsal jf on iof.id_booking_futsal=jf.id_identitas_order_futsal where jf.fasilitas='$fasilitas_fipilih' $where group by jf.id_identitas_order_futsal")->num_rows();

        foreach ($q as $k => $v) {
            $no++;

            $id_identitas_order_futsal  = $v['id_booking_futsal'];

            $q_jadwal = $this->db->query("SELECT   tgl_main, jam_main, status  from jadwal_futsal where id_identitas_order_futsal='$id_identitas_order_futsal'")->result_array();
            $jumlah_jadwal = count($q_jadwal);
            $show_jadwal = '<ol>';
            $jumlah_visit = 0;
            $jumlah_expired = 0;
            $jumlah_aktif = 0;
            foreach ($q_jadwal as $k_j => $v_j) {

                if ($v_j['status']=='Visit') {
                        $warnafont = 'style="color:black"';
                        $jumlah_visit++;
                }else{
                    $tgl_main = $v_j['tgl_main'];
                    if ($tgl == $v_j['tgl_main']) {
                        $warnafont = 'style="color:blue"';
                        $jumlah_aktif++;
                    }
                    elseif ($tgl > $v_j['tgl_main']) {
                        $warnafont = 'style="color:red"';
                        $jumlah_expired++;
                    }else{
                        $warnafont = 'style="color:green"';
                        $jumlah_aktif++;
                    }
                }

                $show_jadwal .='<li '.$warnafont.'> '.$v_j['status'].' | '.$v_j['tgl_main'].' - '.$v_j['jam_main'].'</li>';
            }


            if ($jumlah_visit==$jumlah_jadwal) {
                $show_status = "Sudah visit semua jadwal";
            }else{

                if ($jumlah_expired==$jumlah_jadwal) {
                    $show_status = "Expired";
                }
                elseif ($jumlah_aktif==$jumlah_jadwal) {
                    $show_status = $show_status = "Aktif <br>[".$jumlah_aktif.' Jadwal]';;
                }else{
                    if ($jumlah_aktif >0) {
                        $show_status = "Aktif <br>[".$jumlah_aktif.' Jadwal]';
                        # code...
                    }else{
                        $show_status = "Sudah visit semua jadwal <br>[".$jumlah_expired.' Expired]';

                    }

                }
            }
            $show_jadwal .='</ol>';

            $row    = [];
            $row[]  = $no;
            $row[]  = $v['nama'].'<br>'. $v['no_hp'];

            $row[]  = $show_status  ;


                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_futsal('."'".$v['id_booking_futsal']."','bulanan'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
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



    public function dt_rekap_badminton()
    {
        $data = [];
         $no             = $_POST['start'];
         $tgl             = date('Y-m-d');//$_POST['tgl'];
         $jenis             = $_POST['jenis'];

         
             $fasilitas_fipilih = 'Badminton - Member Bulanan';
        
         $bulan             = $_POST['bulan'];
         $tahun             = $_POST['tahun'];

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
            0=>'id_booking_badminton',
         ];

         if (!isset($valid_columns[$col])) {
             $order= null;
         }else{
            $order = $valid_columns[$col];
         }

         if ($order!=null) {
             $order_by = "order by id_booking_badminton desc, $order $dir";
             # code...
         }else{
             $order_by = "";

         }
         // untuk order by




         if ($jenis=='semua') {
             $where = "";
         }else{
             $where = " and (month(jf.tgl_main)='$bulan' and year(jf.tgl_main)='$tahun')";

         }




         if ($key) {
            $q = $this->db->query("SELECT  id_booking_badminton, paket, nama, no_hp, iof.fasilitas from identitas_order_badminton iof left join jadwal_badminton jf on iof.id_booking_badminton=jf.id_identitas_order_badminton where jf.fasilitas='$fasilitas_fipilih' $where and (nama like'%$key%') group by jf.id_identitas_order_badminton  limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT  id_booking_badminton, paket, nama, no_hp, iof.fasilitas from identitas_order_badminton iof left join jadwal_badminton jf on iof.id_booking_badminton=jf.id_identitas_order_badminton where jf.fasilitas='$fasilitas_fipilih' $where group by jf.id_identitas_order_badminton $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT  id_booking_badminton, paket, nama, no_hp, iof.fasilitas from identitas_order_badminton iof left join jadwal_badminton jf on iof.id_booking_badminton=jf.id_identitas_order_badminton where jf.fasilitas='$fasilitas_fipilih' $where group by jf.id_identitas_order_badminton")->num_rows();

        foreach ($q as $k => $v) {
            $no++;

            $id_identitas_order_badminton  = $v['id_booking_badminton'];

            $q_jadwal = $this->db->query("SELECT   tgl_main, jam_main, status  from jadwal_badminton where id_identitas_order_badminton='$id_identitas_order_badminton'")->result_array();




            $jumlah_jadwal = count($q_jadwal);
            $show_jadwal = '<ol>';
            $jumlah_visit = 0;
            $jumlah_expired = 0;
            $jumlah_aktif = 0;
            foreach ($q_jadwal as $k_j => $v_j) {

                if ($v_j['status']=='Visit') {
                        $warnafont = 'style="color:black"';
                        $jumlah_visit++;
                }else{
                    $tgl_main = $v_j['tgl_main'];
                    if ($tgl == $v_j['tgl_main']) {
                        $warnafont = 'style="color:blue"';
                        $jumlah_aktif++;
                    }
                    elseif ($tgl > $v_j['tgl_main']) {
                        $warnafont = 'style="color:red"';
                        $jumlah_expired++;
                    }else{
                        $warnafont = 'style="color:green"';
                        $jumlah_aktif++;
                    }
                }

                $show_jadwal .='<li '.$warnafont.'> '.$v_j['status'].' | '.$v_j['tgl_main'].' - '.$v_j['jam_main'].'</li>';
            }


            
            if ($jumlah_visit==$jumlah_jadwal) {
                $show_status = "Sudah visit semua jadwal";
            }else{

                if ($jumlah_expired==$jumlah_jadwal) {
                    $show_status = "Expired";
                }
                elseif ($jumlah_aktif==$jumlah_jadwal) {
                    $show_status = $show_status = "Aktif <br>[".$jumlah_aktif.' Jadwal]';;
                }else{
                    if ($jumlah_aktif >0) {
                        $show_status = "Aktif <br>[".$jumlah_aktif.' Jadwal]';
                        # code...
                    }else{
                        $show_status = "Sudah visit semua jadwal <br>[".$jumlah_expired.' Expired]';

                    }

                }
            }
            $show_jadwal .='</ol>';

            $row    = [];
            $row[]  = $no;
            $row[]  = $v['nama'].'<br>'. $v['no_hp'];
            $row[]  = $show_status  ;






                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_badminton('."'".$v['id_booking_badminton']."','bulanan'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
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




    public function dt_rekap_pickle()
    {
        $data = [];
         $no             = $_POST['start'];
         $tgl             = date('Y-m-d');//$_POST['tgl'];
         $jenis             = $_POST['jenis'];

         
             $fasilitas_fipilih = 'Pickle - Member Bulanan';
        
         $bulan             = $_POST['bulan'];
         $tahun             = $_POST['tahun'];

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
            0=>'id_booking_pickle',
         ];

         if (!isset($valid_columns[$col])) {
             $order= null;
         }else{
            $order = $valid_columns[$col];
         }

         if ($order!=null) {
             $order_by = "order by id_booking_pickle desc, $order $dir";
             # code...
         }else{
             $order_by = "";

         }
         // untuk order by




         if ($jenis=='semua') {
             $where = "";
         }else{
             $where = " and (month(jf.tgl_main)='$bulan' and year(jf.tgl_main)='$tahun')";

         }




         if ($key) {
            $q = $this->db->query("SELECT  id_booking_pickle, paket, nama, no_hp, iof.fasilitas from identitas_order_pickle iof left join jadwal_badminton jf on iof.id_booking_pickle=jf.id_identitas_order_pickle where jf.fasilitas='$fasilitas_fipilih' $where and (nama like'%$key%') group by jf.id_identitas_order_pickle  limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT  id_booking_pickle, paket, nama, no_hp, iof.fasilitas from identitas_order_pickle iof left join jadwal_badminton jf on iof.id_booking_pickle=jf.id_identitas_order_pickle where jf.fasilitas='$fasilitas_fipilih' $where group by jf.id_identitas_order_pickle $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT  id_booking_pickle, paket, nama, no_hp, iof.fasilitas from identitas_order_pickle iof left join jadwal_badminton jf on iof.id_booking_pickle=jf.id_identitas_order_pickle where jf.fasilitas='$fasilitas_fipilih' $where group by jf.id_identitas_order_pickle")->num_rows();

        foreach ($q as $k => $v) {
            $no++;

            $id_identitas_order_pickle  = $v['id_booking_pickle'];

            $q_jadwal = $this->db->query("SELECT   tgl_main, jam_main, status  from jadwal_badminton where id_identitas_order_pickle='$id_identitas_order_pickle'")->result_array();




            $jumlah_jadwal = count($q_jadwal);
            $show_jadwal = '<ol>';
            $jumlah_visit = 0;
            $jumlah_expired = 0;
            $jumlah_aktif = 0;
            foreach ($q_jadwal as $k_j => $v_j) {

                if ($v_j['status']=='Visit') {
                        $warnafont = 'style="color:black"';
                        $jumlah_visit++;
                }else{
                    $tgl_main = $v_j['tgl_main'];
                    if ($tgl == $v_j['tgl_main']) {
                        $warnafont = 'style="color:blue"';
                        $jumlah_aktif++;
                    }
                    elseif ($tgl > $v_j['tgl_main']) {
                        $warnafont = 'style="color:red"';
                        $jumlah_expired++;
                    }else{
                        $warnafont = 'style="color:green"';
                        $jumlah_aktif++;
                    }
                }

                $show_jadwal .='<li '.$warnafont.'> '.$v_j['status'].' | '.$v_j['tgl_main'].' - '.$v_j['jam_main'].'</li>';
            }


            
            if ($jumlah_visit==$jumlah_jadwal) {
                $show_status = "Sudah visit semua jadwal";
            }else{

                if ($jumlah_expired==$jumlah_jadwal) {
                    $show_status = "Expired";
                }
                elseif ($jumlah_aktif==$jumlah_jadwal) {
                    $show_status = $show_status = "Aktif <br>[".$jumlah_aktif.' Jadwal]';;
                }else{
                    if ($jumlah_aktif >0) {
                        $show_status = "Aktif <br>[".$jumlah_aktif.' Jadwal]';
                        # code...
                    }else{
                        $show_status = "Sudah visit semua jadwal <br>[".$jumlah_expired.' Expired]';

                    }

                }
            }
            $show_jadwal .='</ol>';

            $row    = [];
            $row[]  = $no;
            $row[]  = $v['nama'].'<br>'. $v['no_hp'];
            $row[]  = $show_status  ;
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_pickle('."'".$v['id_booking_pickle']."','bulanan'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
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




    public function cek_jadwal_futsal_harian()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_futsal where id_booking_futsal='$id_booking'")->row_array();
            $id_transaksi_sementara =$q_pelanggan['id_transaksi_sementara'];
            $q_jadwal = $this->db->query("SELECT id_detail_booking_futsal, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_futsal where id_identitas_order_futsal='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT id_transaksi,  kategori_potongan, jenis_potongan, id_metode_pembayaran,id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran, dibayar, kembalian, (dibayar - kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();
          
  $cek_dp = $q_transaksi['dp'];
            $pecah_dp = explode(',', $cek_dp);
            if (count($pecah_dp)>1) {
                $dp = $pecah_dp[0] + $pecah_dp[1];
            }else{
                $dp = $pecah_dp[0];
            }
                
  $cek_dibayar = $q_transaksi['dibayar'];
            $pecah_dibayar = explode(',', $cek_dibayar);
            if (count($pecah_dibayar)>1) {
                $dibayar = $pecah_dibayar[0] + $pecah_dibayar[1];
            }else{
                $dibayar = $pecah_dibayar[0];
            }
            
            if ($dp>0) {
                $show_dibayar = $dp;
                # code...
            }else{
                $show_dibayar = $dibayar - ($q_transaksi['kembalian'] == '' ? 0 : $q_transaksi['kembalian']) ;

            }
                



             $kumpul_metode_pembayaran = [];
            foreach (metode_pembayaran() as $k => $v) {
                $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
            }

          $pecah_mp = explode(',', $q_transaksi['id_metode_pembayaran'] );
        
        $kumpul_mp = [];
          foreach ($pecah_mp as $k => $v) {
            array_push($kumpul_mp, $kumpul_metode_pembayaran[$v]);
          }
          $metode_pembayaran =  count($kumpul_mp) > 0 ? join(',',$kumpul_mp) : '';






            $data_transaksi = [
                'id_transaksi'=>$q_transaksi['id_transaksi'],
                'kategori_potongan'=>$q_transaksi['kategori_potongan'],
                'jenis_potongan'=>$q_transaksi['jenis_potongan'],
                'metode_pembayaran'=>$metode_pembayaran,
                'id_diskon'=>$q_transaksi['id_diskon'],
                'nama_diskon'=>$q_transaksi['nama_diskon'],
                'besar_diskon'=>$q_transaksi['besar_diskon'],
                'rp_nilai_diskon'=>$q_transaksi['rp_nilai_diskon'],
                'total'=>$q_transaksi['total'],
                'tagihan'=>$q_transaksi['tagihan'],
                'status'=>$q_transaksi['status'],
                'dp'=>$dp,
                'sisa_pembayaran'=>$q_transaksi['sisa_pembayaran'],
                'sudah_dibayar'=>$show_dibayar,
            ];
            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$data_transaksi   ,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }


 
    public function cek_jadwal_badminton_harian()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_badminton where id_booking_badminton='$id_booking'")->row_array();
            $id_transaksi_sementara =$q_pelanggan['id_transaksi_sementara'];
            $q_jadwal = $this->db->query("SELECT id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_badminton='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT id_transaksi,  kategori_potongan, id_metode_pembayaran, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran, dibayar, kembalian, (dibayar - kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();
          
  $cek_dp = $q_transaksi['dp'];
            $pecah_dp = explode(',', $cek_dp);
            if (count($pecah_dp)>1) {
                $dp = $pecah_dp[0] + $pecah_dp[1];
            }else{
                $dp = $pecah_dp[0];
            }
                
  $cek_dibayar = $q_transaksi['dibayar'];
            $pecah_dibayar = explode(',', $cek_dibayar);
            if (count($pecah_dibayar)>1) {
                $dibayar = $pecah_dibayar[0] + $pecah_dibayar[1];
            }else{
                $dibayar = $pecah_dibayar[0];
            }
            
            if ($dp>0) {
                $show_dibayar = $dp;
                # code...
            }else{
                $show_dibayar = $dibayar - ($q_transaksi['kembalian'] == '' ? 0 : $q_transaksi['kembalian']) ;

            }
            



             $kumpul_metode_pembayaran = [];
            foreach (metode_pembayaran() as $k => $v) {
                $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
            }

          $pecah_mp = explode(',', $q_transaksi['id_metode_pembayaran'] );
        
        $kumpul_mp = [];
          foreach ($pecah_mp as $k => $v) {
            array_push($kumpul_mp, $kumpul_metode_pembayaran[$v]);
          }
          $metode_pembayaran =  count($kumpul_mp) > 0 ? join(',',$kumpul_mp) : '';






            $data_transaksi = [
                'id_transaksi'=>$q_transaksi['id_transaksi'],
                'kategori_potongan'=>$q_transaksi['kategori_potongan'],
                'jenis_potongan'=>$q_transaksi['jenis_potongan'],
                'id_diskon'=>$q_transaksi['id_diskon'],
                'nama_diskon'=>$q_transaksi['nama_diskon'],
                'metode_pembayaran'=>$metode_pembayaran,
                'besar_diskon'=>$q_transaksi['besar_diskon'],
                'rp_nilai_diskon'=>$q_transaksi['rp_nilai_diskon'],
                'total'=>$q_transaksi['total'],
                'tagihan'=>$q_transaksi['tagihan'],
                'status'=>$q_transaksi['status'],
                'dp'=>$dp,
                'sisa_pembayaran'=>$q_transaksi['sisa_pembayaran'],
                'sudah_dibayar'=>$show_dibayar,
            ];
            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$data_transaksi   ,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }



 
    public function cek_jadwal_pickle_harian()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_pickle where id_booking_pickle='$id_booking'")->row_array();
            $id_transaksi_sementara =$q_pelanggan['id_transaksi_sementara'];
            $q_jadwal = $this->db->query("SELECT id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_pickle='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT id_transaksi,  kategori_potongan, id_metode_pembayaran, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran, dibayar, kembalian, (dibayar - kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();
          
  $cek_dp = $q_transaksi['dp'];
            $pecah_dp = explode(',', $cek_dp);
            if (count($pecah_dp)>1) {
                $dp = $pecah_dp[0] + $pecah_dp[1];
            }else{
                $dp = $pecah_dp[0];
            }
                
  $cek_dibayar = $q_transaksi['dibayar'];
            $pecah_dibayar = explode(',', $cek_dibayar);
            if (count($pecah_dibayar)>1) {
                $dibayar = $pecah_dibayar[0] + $pecah_dibayar[1];
            }else{
                $dibayar = $pecah_dibayar[0];
            }
            
            if ($dp>0) {
                $show_dibayar = $dp;
                # code...
            }else{
                $show_dibayar = $dibayar - ($q_transaksi['kembalian'] == '' ? 0 : $q_transaksi['kembalian']) ;

            }
            



             $kumpul_metode_pembayaran = [];
            foreach (metode_pembayaran() as $k => $v) {
                $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
            }

          $pecah_mp = explode(',', $q_transaksi['id_metode_pembayaran'] );
        
        $kumpul_mp = [];
          foreach ($pecah_mp as $k => $v) {
            array_push($kumpul_mp, $kumpul_metode_pembayaran[$v]);
          }
          $metode_pembayaran =  count($kumpul_mp) > 0 ? join(',',$kumpul_mp) : '';






            $data_transaksi = [
                'id_transaksi'=>$q_transaksi['id_transaksi'],
                'kategori_potongan'=>$q_transaksi['kategori_potongan'],
                'jenis_potongan'=>$q_transaksi['jenis_potongan'],
                'id_diskon'=>$q_transaksi['id_diskon'],
                'nama_diskon'=>$q_transaksi['nama_diskon'],
                'metode_pembayaran'=>$metode_pembayaran,
                'besar_diskon'=>$q_transaksi['besar_diskon'],
                'rp_nilai_diskon'=>$q_transaksi['rp_nilai_diskon'],
                'total'=>$q_transaksi['total'],
                'tagihan'=>$q_transaksi['tagihan'],
                'status'=>$q_transaksi['status'],
                'dp'=>$dp,
                'sisa_pembayaran'=>$q_transaksi['sisa_pembayaran'],
                'sudah_dibayar'=>$show_dibayar,
            ];
            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$data_transaksi   ,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }

    public function cek_jadwal_futsal_bulanan()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_futsal where id_booking_futsal='$id_booking'")->row_array();
            $id_transaksi =$q_pelanggan['id_transaksi'];
            $q_jadwal = $this->db->query("SELECT biaya_perubahan, id_detail_booking_futsal, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_futsal where id_identitas_order_futsal='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT   kategori_potongan, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran from transaksi where id_transaksi='$id_transaksi'")->row_array();
          

         
            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$q_transaksi,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }



    public function cek_jadwal_pickle_bulanan()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_pickle where id_booking_pickle='$id_booking'")->row_array();
            $id_transaksi =$q_pelanggan['id_transaksi'];
            $q_jadwal = $this->db->query("SELECT biaya_perubahan, id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_pickle='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT   kategori_potongan, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran from transaksi where id_transaksi='$id_transaksi'")->row_array();
          

         
            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$q_transaksi,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }




    public function cek_jadwal_badminton_bulanan()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_badminton where id_booking_badminton='$id_booking'")->row_array();
            $id_transaksi =$q_pelanggan['id_transaksi'];
            $q_jadwal = $this->db->query("SELECT biaya_perubahan, id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_badminton='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT   kategori_potongan, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran from transaksi where id_transaksi='$id_transaksi'")->row_array();
          

         
            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$q_transaksi,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }



    public function cek_jadwal_futsal_turnamen()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, paket, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_futsal where id_booking_futsal='$id_booking'")->row_array();
            $id_transaksi_sementara =$q_pelanggan['id_transaksi_sementara'];
            $q_jadwal = $this->db->query("SELECT id_detail_booking_futsal, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_futsal where id_identitas_order_futsal='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT id_transaksi,  kategori_potongan, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran,id_metode_pembayaran, dibayar, kembalian,(dibayar - kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();
          
            @$id_transaksi = $q_transaksi['id_transaksi'];
            $q_biaya_tambahan = $this->db->query("SELECT item_transaksi, nilai from pendapatan where id_transaksi='$id_transaksi'")->result_array();

         
  $cek_dp = $q_transaksi['dp'];
            $pecah_dp = explode(',', $cek_dp);
            if (count($pecah_dp)>1) {
                $dp = $pecah_dp[0] + $pecah_dp[1];
            }else{
                $dp = $pecah_dp[0];
            }
                
  $cek_dibayar = $q_transaksi['dibayar'];
            $pecah_dibayar = explode(',', $cek_dibayar);
            if (count($pecah_dibayar)>1) {
                $dibayar = $pecah_dibayar[0] + $pecah_dibayar[1];
            }else{
                $dibayar = $pecah_dibayar[0];
            }
            
            if ($dp>0) {
                $show_dibayar = $dp;
                # code...
            }else{
                $show_dibayar =$dibayar - ($q_transaksi['kembalian'] == '' ? 0 : $q_transaksi['kembalian']) ;

            }
               




             $kumpul_metode_pembayaran = [];
            foreach (metode_pembayaran() as $k => $v) {
                $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
            }

              $pecah_mp = explode(',', $q_transaksi['id_metode_pembayaran'] );
            
            $kumpul_mp = [];
              foreach ($pecah_mp as $k => $v) {
                array_push($kumpul_mp, $kumpul_metode_pembayaran[$v]);
              }
              $metode_pembayaran =  count($kumpul_mp) > 0 ? join(',',$kumpul_mp) : '';





 
            $data_transaksi = [
                'id_transaksi'=>$id_transaksi,
                'kategori_potongan'=>$q_transaksi['kategori_potongan'],
                'jenis_potongan'=>$q_transaksi['jenis_potongan'],
                'id_diskon'=>$q_transaksi['id_diskon'],
                'nama_diskon'=>$q_transaksi['nama_diskon'],

                'metode_pembayaran'=>$metode_pembayaran,
                'besar_diskon'=>$q_transaksi['besar_diskon'],
                'rp_nilai_diskon'=>$q_transaksi['rp_nilai_diskon'],
                'total'=>$q_transaksi['total'],
                'tagihan'=>$q_transaksi['tagihan'],
                'status'=>$q_transaksi['status'],
                'dp'=>$dp,
                'sisa_pembayaran'=>$q_transaksi['sisa_pembayaran'],
                'sudah_dibayar'=>$show_dibayar,
            ];




            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$data_transaksi,
                'data_biaya_tambahan'=>$q_biaya_tambahan,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }

    // public function cek_jadwal_futsal_turnamen()
    // {
        
    //         $id_booking = $this->input->post('id_booking');
    //         $q_pelanggan = $this->db->query("SELECT paket,  id_transaksi_sementara, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_futsal where id_booking_futsal='$id_booking'")->row_array();
    //         $id_transaksi_sementara =$q_pelanggan['id_transaksi_sementara'];
    //         $q_jadwal = $this->db->query("SELECT id_detail_booking_futsal, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_futsal where id_identitas_order_futsal='$id_booking'")->result_array();
    //         $q_transaksi = $this->db->query("SELECT  id_transaksi, kategori_potongan, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();

    //         $id_transaksi = $q_transaksi['id_transaksi'];
    //         $q_biaya_tambahan = $this->db->query("SELECT item_transaksi, nilai from pendapatan where id_transaksi='$id_transaksi'")->result_array();
          

         
    //         $output =[
    //             'data_pelanggan'=>$q_pelanggan,
    //             'data_jadwal'=>$q_jadwal,
    //             'data_transaksi'=>$q_transaksi,
    //             'data_biaya_tambahan'=>$q_biaya_tambahan,
    //         ];
    //         echo json_encode($output);
    //     // $this->load->view('template/admin');
    // }



    public function cek_jadwal_badminton_turnamen()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, paket, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_badminton where id_booking_badminton='$id_booking'")->row_array();
            $id_transaksi_sementara =$q_pelanggan['id_transaksi_sementara'];
            $q_jadwal = $this->db->query("SELECT id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_badminton='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT id_transaksi,  kategori_potongan, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran, dibayar, id_metode_pembayaran, kembalian,(dibayar - kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();
          
            @$id_transaksi = $q_transaksi['id_transaksi'];
            $q_biaya_tambahan = $this->db->query("SELECT item_transaksi, nilai from pendapatan where id_transaksi='$id_transaksi'")->result_array();

         
  $cek_dp = $q_transaksi['dp'];
            $pecah_dp = explode(',', $cek_dp);
            if (count($pecah_dp)>1) {
                $dp = $pecah_dp[0] + $pecah_dp[1];
            }else{
                $dp = $pecah_dp[0];
            }
                
  $cek_dibayar = $q_transaksi['dibayar'];
            $pecah_dibayar = explode(',', $cek_dibayar);
            if (count($pecah_dibayar)>1) {
                $dibayar = $pecah_dibayar[0] + $pecah_dibayar[1];
            }else{
                $dibayar = $pecah_dibayar[0];
            }
            
            if ($dp>0) {
                $show_dibayar = $dp;
                # code...
            }else{
                $show_dibayar =$dibayar - ($q_transaksi['kembalian'] == '' ? 0 : $q_transaksi['kembalian']) ;

            }
                



             $kumpul_metode_pembayaran = [];
            foreach (metode_pembayaran() as $k => $v) {
                $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
            }

          $pecah_mp = explode(',', $q_transaksi['id_metode_pembayaran'] );
        
        $kumpul_mp = [];
          foreach ($pecah_mp as $k => $v) {
            array_push($kumpul_mp, $kumpul_metode_pembayaran[$v]);
          }
          $metode_pembayaran =  count($kumpul_mp) > 0 ? join(',',$kumpul_mp) : '';



            $data_transaksi = [
                'id_transaksi'=>$id_transaksi,
                'kategori_potongan'=>$q_transaksi['kategori_potongan'],
                'jenis_potongan'=>$q_transaksi['jenis_potongan'],
                'id_diskon'=>$q_transaksi['id_diskon'],
                'nama_diskon'=>$q_transaksi['nama_diskon'],
                'metode_pembayaran'=>$metode_pembayaran,
                'besar_diskon'=>$q_transaksi['besar_diskon'],
                'rp_nilai_diskon'=>$q_transaksi['rp_nilai_diskon'],
                'total'=>$q_transaksi['total'],
                'tagihan'=>$q_transaksi['tagihan'],
                'status'=>$q_transaksi['status'],
                'dp'=>$dp,
                'sisa_pembayaran'=>$q_transaksi['sisa_pembayaran'],
                'sudah_dibayar'=>$show_dibayar,
            ];




            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$data_transaksi,
                'data_biaya_tambahan'=>$q_biaya_tambahan,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }


    public function cek_jadwal_pickle_turnamen()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT id_transaksi, id_transaksi_sementara, paket, kategori_order, lapangan, nama, no_hp, alamat, fasilitas, keterangan  from identitas_order_pickle where id_booking_pickle='$id_booking'")->row_array();
            $id_transaksi_sementara =$q_pelanggan['id_transaksi_sementara'];
            $q_jadwal = $this->db->query("SELECT id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_pickle='$id_booking'")->result_array();
            $q_transaksi = $this->db->query("SELECT id_transaksi,  kategori_potongan, jenis_potongan, id_diskon, nama_diskon, besar_diskon, rp_nilai_diskon, total, tagihan, status, dp, sisa_pembayaran, dibayar, id_metode_pembayaran, kembalian,(dibayar - kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();
         
            $id_transaksi = $q_transaksi['id_transaksi'];
            $q_biaya_tambahan = $this->db->query("SELECT item_transaksi, nilai from pendapatan where id_transaksi='$id_transaksi'")->result_array();
           
  $cek_dp = $q_transaksi['dp'];
            $pecah_dp = explode(',', $cek_dp);
            if (count($pecah_dp)>1) {
                $dp = $pecah_dp[0] + $pecah_dp[1];
            }else{
                $dp = $pecah_dp[0];
            }
                
  $cek_dibayar = $q_transaksi['dibayar'];
            $pecah_dibayar = explode(',', $cek_dibayar);
            if (count($pecah_dibayar)>1) {
                $dibayar = $pecah_dibayar[0] + $pecah_dibayar[1];
            }else{
                $dibayar = $pecah_dibayar[0];
            }
            
            if ($dp>0) {
                $show_dibayar = $dp;
                # code...
            }else{
                $show_dibayar =$dibayar - ($q_transaksi['kembalian'] == '' ? 0 : $q_transaksi['kembalian']) ;

            }
                



             $kumpul_metode_pembayaran = [];
            foreach (metode_pembayaran() as $k => $v) {
                $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
            }

          $pecah_mp = explode(',', $q_transaksi['id_metode_pembayaran'] );
        
        $kumpul_mp = [];
          foreach ($pecah_mp as $k => $v) {
            array_push($kumpul_mp, $kumpul_metode_pembayaran[$v]);
          }
          $metode_pembayaran =  count($kumpul_mp) > 0 ? join(',',$kumpul_mp) : '';



            $data_transaksi = [
                'id_transaksi'=>$id_transaksi,
                'kategori_potongan'=>$q_transaksi['kategori_potongan'],
                'jenis_potongan'=>$q_transaksi['jenis_potongan'],
                'id_diskon'=>$q_transaksi['id_diskon'],
                'nama_diskon'=>$q_transaksi['nama_diskon'],
                'metode_pembayaran'=>$metode_pembayaran,
                'besar_diskon'=>$q_transaksi['besar_diskon'],
                'rp_nilai_diskon'=>$q_transaksi['rp_nilai_diskon'],
                'total'=>$q_transaksi['total'],
                'tagihan'=>$q_transaksi['tagihan'],
                'status'=>$q_transaksi['status'],
                'dp'=>$dp,
                'sisa_pembayaran'=>$q_transaksi['sisa_pembayaran'],
                'sudah_dibayar'=>$show_dibayar,
            ];




            $output =[
                'cek'=>$q_transaksi['id_metode_pembayaran'],
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
                'data_transaksi'=>$data_transaksi,
                'data_biaya_tambahan'=>$q_biaya_tambahan,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }


    public function simpan_visit_futsal_harian()
    {
        
        $id_booking = $this->input->post('id_booking');
        $id_transaksi_sementara = $this->input->post('id_transaksi_sementara');
        $simpan_total = $this->input->post('simpan_total');
        $biaya_perorang = $this->input->post('biaya_perorang');
        $kategori_diskon = $this->input->post('kategori_diskon');
        $id_diskon = $this->input->post('id_diskon');
        $nama_diskon = $this->input->post('nama_diskon');
        $jenis_potongan = $this->input->post('jenis_potongan');
        $besar_potongan = $this->input->post('besar_potongan');
        $rp_nilai_potongan = $this->input->post('rp_nilai_potongan');
        $simpan_tagihan = $this->input->post('simpan_tagihan');
        $simpan_dibayar = $this->input->post('simpan_dibayar');
        $simpan_kembalian = $this->input->post('simpan_kembalian');

        $dp = $this->input->post('dp');
        $sisa = $this->input->post('sisa');


        $nama = $this->input->post('nama');
        $id_identitas_order_futsal = $this->input->post('id_identias_order');


        $this->db->trans_begin();


        $next_order = next_order();
        if ($next_order>0 && $next_order <10) {
            $next_no_transaksi = '000'.$next_order;
         }
         else if ($next_order>=10 && $next_order <100) {
            $next_no_transaksi = '00'.$next_order;
         }
         else if ($next_order>=100 && $next_order <1000) {
            $next_no_transaksi = '0'.$next_order;
         }else{
            $next_no_transaksi = $next_order;
         }
        $no_transaksi = date('Ymd').$next_no_transaksi;

         $q_transaksi_sebelumnya = $this->db->query("SELECT id_metode_pembayaran, pembayaran,jenis_potongan, dibayar,  besar_diskon, tagihan, (dibayar-kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();

        $jenis_kembalian = $this->input->post('jenis_kembalian');
        $metode_pembayaran = $this->input->post('metode_pembayaran');

        if ($jenis_kembalian=='Return') {
            if ($simpan_tagihan < $dp) {
                
                $kembalian  = $simpan_kembalian;
                $dibayar_plg = $simpan_dibayar;

            }else{
                // $pembayaran  = $q_transaksi_sebelumnya['pembayaran'];
                // $simpan_dibayar_plg  = $q_transaksi_sebelumnya['tagihan'];
                $kembalian  = 0;//$q_transaksi_sebelumnya['sudah_dibayar'] - ($dp == '' ? $dp : 0 );
                $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
                // $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];

            }

                // if ($this->input->post('metode_pembayaran_2')!=null) {
                //     $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                //     $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                //     $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                //     $pembayaran = '2 Metode Pembayaran';
                // }else{
                //     $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];//$metode_pembayaran;
                //     $simpan_dibayar_plg = $simpan_dibayar;
                //     $pembayaran = '1 Metode Pembayaran';
                // }

                    $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];//$metode_pembayaran;
                    // $simpan_dibayar_plg = $simpan_dibayar;

                    $simpan_dibayar_plg = $q_transaksi_sebelumnya['dibayar'];
                    $pembayaran = $q_transaksi_sebelumnya['pembayaran'];;

            # code...
        }else{
            $kembalian  = $simpan_kembalian;
            $dibayar_plg = $simpan_dibayar;

            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                $pembayaran = '2 Metode Pembayaran';
            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar_plg = $simpan_dibayar;
                $pembayaran = '1 Metode Pembayaran';
            }


        }


            $simpan_sisa = $sisa == 'NaN' ? 0 : $sisa;

            $data_transaksi = [
                'id_user' => id_user(),
                'no_transaksi' => $no_transaksi,
                'id_metode_pembayaran' => $simpan_metode_pembayaran,
                'pembayaran' => $pembayaran,
                'id_akun_pendapatan' => 1,
                'kategori_transaksi' => 'Visited',
                'keterangan' => '',
                'nama_pengunjung' => $nama,
                'nohp_pengunjung' => '',
                'fasilitas' => 'Futsal - Harian',
                'kelompok_fasilitas' => 'Futsal',
                'tgl_transaksi' => tgls(),
                'jam_transaksi' => jams(),
                'order_ke' => $next_order,
                'order_ke_fasilitas' => next_order_fasilitas('Futsal - Harian'),
                'total' => $simpan_total,
                'kategori_potongan' => $kategori_diskon,
                'jenis_potongan' => $jenis_potongan,
                'id_diskon' => $id_diskon,
                'nama_diskon' => $nama_diskon,
                'besar_diskon' => $besar_potongan,
                'rp_nilai_diskon' => $rp_nilai_potongan,
                'tagihan' => $simpan_tagihan,
                'dp' => $dp,
                'sisa_pembayaran' => $sisa,
                'dibayar' => $simpan_dibayar_plg,
                'kembalian ' => $kembalian,
                'status ' => 'Settlement',
            ];

               $this->db->insert('transaksi', $data_transaksi);
                $id_transaksi = $this->db->insert_id();
               $this->db->update('transaksi',['status'=>'-','id_transaksi_pelunasan'=>$id_transaksi], ['id_transaksi'=>$id_transaksi_sementara]);

            $q_jadwal = $this->db->query("SELECT id_detail_booking_futsal, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_futsal where id_identitas_order_futsal='$id_booking' and status='Booking'")->result_array();

            $kumpul_pendapatan = [];
            $total_biaya = 0;

            // $master_futsal = $this->db->query("SELECT harga from master_futsal")->result_array();
            // foreach ($master_futsal as $k => $v) {
                
            // }




            $hitung_jam_siang = 0;
            $hitung_jam_malam = 0;
            $kumpul_jam_main = [];
             foreach ($q_jadwal as $k => $v) {   
            $lapangan =  $v['lapangan'];
            $tgl_main =  $v['tgl_main'];
            $jam =  $v['jam_main'];
            $biaya = $v['biaya'];
            $total_biaya += $biaya;
            array_push($kumpul_jam_main, $jam);

            // if ($biaya==$master_futsal[0]['harga']) {
            //    $hitung_jam_siang++
            // }else{
            //    $hitung_jam_malam++

            // }
            //  $pendapatan = [
            //     'id_transaksi'=>$id_transaksi,
            //     'fasilitas'=>'Futsal - Harian',
            //     'kelompok_fasilitas'=>'Futsal',
            //     'kategori'=>'Masuk',
            //     'id_akun_pendapatan'=>1,
            //     'nilai'=>$biaya,
            //     'item_transaksi'=>'Futsal - Harian',
            //     'keterangan'=> 'Futsal - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>jam '.$jam,
            //     'tgl_transaksi'=>timestamp(),
            //     'id_user '=>id_user(),
            //     'status'=>'Settlement',
            //     'id_metode_pembayaran' => $metode_pembayaran,
            //     'id_identitas_order' => $id_identitas_order_futsal,
            //     'lapangan' => $lapangan,
            //     'tgl_main' => $tgl_main,
            //     'jam_main' => $jam,
            // ];
            // array_push($kumpul_pendapatan, $pendapatan);
        }

        $simpan_jam_main = join(',', $kumpul_jam_main);

            // $caption_jam_siang = $hitung_jam_siang >0 ? $hitung_jam_siang .' jam siang ,' : '';
            // $caption_jam_malam = $hitung_jam_malam >0 ? $hitung_jam_malam .' jam malam' : '';


            if ($simpan_sisa>0) {

                $pendapatan_dp = [
                    'id_transaksi'=>$id_transaksi_sementara,
                    'id_transaksi_pelunasan'=>$id_transaksi,
                    'fasilitas'=>'Futsal - Harian',
                    'kelompok_fasilitas'=>'Futsal',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>1,
                    'nilai'=>$dp,
                    'item_transaksi'=>'DP Futsal - Harian',
                    'keterangan'=> 'Pendapatan DP Futsal - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>jam  '.$simpan_jam_main,//.' ('.$caption_jam_siang.$caption_jam_malam.')',
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $q_transaksi_sebelumnya['id_metode_pembayaran'],
                    'id_identitas_order' => $id_identitas_order_futsal,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan_dp);
                $nilai_sisa_pelunasan = $simpan_total   - $dp;
                $pendapatan_pelunasan = [
                    'id_transaksi'=>$id_transaksi,
                    'id_transaksi_pelunasan'=>'',
                    'fasilitas'=>'Futsal - Harian',
                    'kelompok_fasilitas'=>'Futsal',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>1,
                    'nilai'=>$nilai_sisa_pelunasan,
                    'item_transaksi'=>'Pelunasan Futsal - Harian',
                    'keterangan'=> 'Pendapatan pelunasan Futsal - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>selama  '.$simpan_jam_main,
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $metode_pembayaran,
                    'id_identitas_order' => $id_identitas_order_futsal,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan_pelunasan);
            }else{
                $pendapatan = [
                    'id_transaksi'=>$id_transaksi,
                    'id_transaksi_pelunasan'=>'',
                    'fasilitas'=>'Futsal - Harian',
                    'kelompok_fasilitas'=>'Futsal',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>1,
                    'nilai'=>$simpan_total,
                    'item_transaksi'=>'Futsal - Harian',
                    'keterangan'=> 'Pendapatan Futsal - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>selama  '.$simpan_jam_main,
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $metode_pembayaran,
                    'id_identitas_order' => $id_identitas_order_futsal,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan);

            }


            // if ($sisa>0) {



            // array_push($kumpul_pendapatan, $pendapatan);
            // }









        if ($kategori_diskon !='') {
            $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;

           

        if ($q_transaksi_sebelumnya['jenis_potongan']=='Persentase') {
              $potongan   = $total_biaya  * ($q_transaksi_sebelumnya['besar_diskon'] / 100);
        }else{
            $potongan = $q_transaksi_sebelumnya['besar_diskon'];
        }





              $pendapatan_keluad_diskon = [
                'id_transaksi'=>$id_transaksi,
                'id_transaksi_pelunasan'=>'',
                'fasilitas'=>'Futsal - Harian',
                'kelompok_fasilitas'=>'Futsal',
                'kategori'=>'Keluar',
                'nilai'=>$potongan,
                'id_akun_pendapatan'=>1,
                'item_transaksi'=>'Potongan Harga',
                'keterangan'=> $keterangan_diskon,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),

                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' =>'',
                'lapangan' =>'',
                'tgl_main' =>'',
                'jam_main' =>'',
            ];
            array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
        }



        $this->db->insert_batch('pendapatan', $kumpul_pendapatan);
        $this->db->update('identitas_order_futsal',['id_transaksi'=>$id_transaksi],['id_booking_futsal'=>$id_booking]);
        $this->db->update('jadwal_futsal',['id_transaksi'=>$id_transaksi,'status'=>'Visited'],['id_identitas_order_futsal'=>$id_booking,'status'=>'Booking']);







        if ($simpan_sisa>0) {



            $pendapatan_semua = $sisa;
            $jumlah_jam_main = count($q_jadwal);
            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $input_dibayar_2 = str_replace(',', '', $this->input->post('input_dibayar_2'));
                $input_dibayar = str_replace(',', '', $this->input->post('input_dibayar'));
                $simpan_dibayar = $input_dibayar.','.$input_dibayar_2;
                $masuk_pendapatan_1 = $input_dibayar;

                $masuk_pendapatan_2 = $pendapatan_semua - $input_dibayar;
                $data_pembayaran = [
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran, 
                        'fasilitas' => 'Futsal - Harian', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar, 
                        'pendapatan' => $masuk_pendapatan_1, 
		                'id_akun_pendapatan' => 1,

                        'item_transaksi' => 'Futsal - Harian', 
                        'keterangan'=>'Pembayaran Pelunasan Futsal - Harian selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran_2, 
                        'fasilitas' => 'Futsal - Harian', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar_2, 
                        'pendapatan' => $masuk_pendapatan_2, 
		                'id_akun_pendapatan' => 1,

                        'item_transaksi' => 'Futsal - Harian', 
                        'keterangan'=>'Pembayaran Futsal - Harian selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                   
                ];
                $this->db->insert_batch('pembayaran', $data_pembayaran);

            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar = $this->input->post('simpan_dibayar');
                $data_pembayaran = [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $simpan_metode_pembayaran, 
                        'fasilitas' => 'Futsal - Harian', 
                        'kategori' => '1 Metode Pembayaran', 
                        'dibayar' => $simpan_dibayar, 
                        'pendapatan' => $pendapatan_semua, 
		                'id_akun_pendapatan' => 1,
                        'item_transaksi' => 'Futsal - Harian', 
                        'keterangan'=>'Pembayaran Futsal - Harian selama '.$jumlah_jam_main.' Jam'.$simpan_jam_main,
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                ];
                $this->db->insert('pembayaran', $data_pembayaran);


            }

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }else{

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }








    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data','id_transaksi'=>''];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan','id_transaksi'=>$id_transaksi];
            $this->db->trans_commit();
    }
    echo json_encode($output);



    


    }





    public function simpan_visit_badminton_harian()
    {
        
            $id_booking = $this->input->post('id_booking');
            $id_transaksi_sementara = $this->input->post('id_transaksi_sementara');
        $simpan_total = $this->input->post('simpan_total');
        $biaya_perorang = $this->input->post('biaya_perorang');
        $kategori_diskon = $this->input->post('kategori_diskon');
        $id_diskon = $this->input->post('id_diskon');
        $nama_diskon = $this->input->post('nama_diskon');
        $jenis_potongan = $this->input->post('jenis_potongan');
        $besar_potongan = $this->input->post('besar_potongan');
        $rp_nilai_potongan = $this->input->post('rp_nilai_potongan');
        $simpan_tagihan = $this->input->post('simpan_tagihan');
        $simpan_dibayar = $this->input->post('simpan_dibayar');
        $simpan_kembalian = $this->input->post('simpan_kembalian');

        $dp = $this->input->post('dp');
        $sisa = $this->input->post('sisa');

        $metode_pembayaran = $this->input->post('metode_pembayaran');

        $nama = $this->input->post('nama');
        $id_identitas_order_badminton = $this->input->post('id_identias_order');


        $this->db->trans_begin();


        $next_order = next_order();
        if ($next_order>0 && $next_order <10) {
            $next_no_transaksi = '000'.$next_order;
         }
         else if ($next_order>=10 && $next_order <100) {
            $next_no_transaksi = '00'.$next_order;
         }
         else if ($next_order>=100 && $next_order <1000) {
            $next_no_transaksi = '0'.$next_order;
         }else{
            $next_no_transaksi = $next_order;
         }
        $no_transaksi = date('Ymd').$next_no_transaksi;

         $q_transaksi_sebelumnya = $this->db->query("SELECT id_metode_pembayaran, pembayaran,jenis_potongan, dibayar, besar_diskon, tagihan, (dibayar-kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();

        $jenis_kembalian = $this->input->post('jenis_kembalian');

        if ($jenis_kembalian=='Return') {
            if ($simpan_tagihan < $dp) {
                
                $kembalian  = $simpan_kembalian;
                $dibayar_plg = $simpan_dibayar;

            }else{
                // $pembayaran  = $q_transaksi_sebelumnya['pembayaran'];
                // $simpan_dibayar_plg  = $q_transaksi_sebelumnya['tagihan'];
                $kembalian  = 0;//$q_transaksi_sebelumnya['sudah_dibayar'] - ($dp == '' ? $dp : 0 );
                $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
                // $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];

            }

                // if ($this->input->post('metode_pembayaran_2')!=null) {
                //     $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                //     $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                //     $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                //     $pembayaran = '2 Metode Pembayaran';
                // }else{
                //     $simpan_metode_pembayaran = $metode_pembayaran;
                //     $simpan_dibayar_plg = $simpan_dibayar;
                //     $pembayaran = '1 Metode Pembayaran';
                // }

                 $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];
                    $simpan_dibayar_plg = $q_transaksi_sebelumnya['dibayar'];
                    $pembayaran = $q_transaksi_sebelumnya['pembayaran'];




            # code...
        }else{
            // $kembalian  = $simpan_kembalian;
            // $dibayar_plg = $simpan_dibayar;

            // if ($this->input->post('metode_pembayaran_2')!=null) {
            //     $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
            //     $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
            //     $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
            //     $pembayaran = '2 Metode Pembayaran';
            // }else{
            //     $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];//$metode_pembayaran;
            //     $simpan_dibayar_plg = $simpan_dibayar;
            //     $pembayaran = $q_transaksi_sebelumnya['pembayaran'];
            // }




            $kembalian  = $simpan_kembalian;
            $dibayar_plg = $simpan_dibayar;

            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                $pembayaran = '2 Metode Pembayaran';
            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar_plg = $simpan_dibayar;
                $pembayaran = '1 Metode Pembayaran';
            }


            

        }


            $simpan_sisa = $sisa == 'NaN' ? 0 : $sisa;

            $data_transaksi = [
                'id_user' => id_user(),
                'no_transaksi' => $no_transaksi,
                'id_metode_pembayaran' => $simpan_metode_pembayaran,
                'pembayaran' => $pembayaran,
                'id_akun_pendapatan' => 2,
                'kategori_transaksi' => 'Visited',
                'keterangan' => '',
                'nama_pengunjung' => $nama,
                'nohp_pengunjung' => '',
                'fasilitas' => 'Badminton - Harian',
                'kelompok_fasilitas' => 'badminton',
                'tgl_transaksi' => tgls(),
                'jam_transaksi' => jams(),
                'order_ke' => $next_order,
                'order_ke_fasilitas' => next_order_fasilitas('Badminton - Harian'),
                'total' => $simpan_total,
                'kategori_potongan' => $kategori_diskon,
                'jenis_potongan' => $jenis_potongan,
                'id_diskon' => $id_diskon,
                'nama_diskon' => $nama_diskon,
                'besar_diskon' => $besar_potongan,
                'rp_nilai_diskon' => $rp_nilai_potongan,
                'tagihan' => $simpan_tagihan,
                'dp' => $dp,
                'sisa_pembayaran' => $sisa,
                'dibayar' => $simpan_dibayar_plg,
                'kembalian ' => $kembalian,
                'status ' => 'Settlement',
            ];

               $this->db->insert('transaksi', $data_transaksi);
                $id_transaksi = $this->db->insert_id();
               $this->db->update('transaksi',['status'=>'-','id_transaksi_pelunasan'=>$id_transaksi], ['id_transaksi'=>$id_transaksi_sementara]);

            $q_jadwal = $this->db->query("SELECT id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_badminton='$id_booking' and status='Booking'")->result_array();

            $kumpul_pendapatan = [];
            $total_biaya = 0;

            // $master_badminton = $this->db->query("SELECT harga from master_badminton")->result_array();
            // foreach ($master_badminton as $k => $v) {
                
            // }




            $hitung_jam_siang = 0;
            $hitung_jam_malam = 0;
            $kumpul_jam_main = [];
             foreach ($q_jadwal as $k => $v) {   
            $lapangan =  $v['lapangan'];
            $tgl_main =  $v['tgl_main'];
            $jam =  $v['jam_main'];
            $biaya = $v['biaya'];
            $total_biaya += $biaya;
            array_push($kumpul_jam_main, $jam);

            // if ($biaya==$master_badminton[0]['harga']) {
            //    $hitung_jam_siang++
            // }else{
            //    $hitung_jam_malam++

            // }
            //  $pendapatan = [
            //     'id_transaksi'=>$id_transaksi,
            //     'fasilitas'=>'Badminton - Harian',
            //     'kelompok_fasilitas'=>'badminton',
            //     'kategori'=>'Masuk',
            //     'id_akun_pendapatan'=>2,
            //     'nilai'=>$biaya,
            //     'item_transaksi'=>'Badminton - Harian',
            //     'keterangan'=> 'Badminton - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>jam '.$jam,
            //     'tgl_transaksi'=>timestamp(),
            //     'id_user '=>id_user(),
            //     'status'=>'Settlement',
            //     'id_metode_pembayaran' => $metode_pembayaran,
            //     'id_identitas_order' => $id_identitas_order_badminton,
            //     'lapangan' => $lapangan,
            //     'tgl_main' => $tgl_main,
            //     'jam_main' => $jam,
            // ];
            // array_push($kumpul_pendapatan, $pendapatan);
        }

        $simpan_jam_main = join(',', $kumpul_jam_main);

            // $caption_jam_siang = $hitung_jam_siang >0 ? $hitung_jam_siang .' jam siang ,' : '';
            // $caption_jam_malam = $hitung_jam_malam >0 ? $hitung_jam_malam .' jam malam' : '';


            if ($simpan_sisa>0) {

                $pendapatan_dp = [
                    'id_transaksi'=>$id_transaksi_sementara,
                    'id_transaksi_pelunasan'=>$id_transaksi,
                    'fasilitas'=>'Badminton - Harian',
                    'kelompok_fasilitas'=>'badminton',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>2,
                    'nilai'=>$dp,
                    'item_transaksi'=>'DP Badminton - Harian',
                    'keterangan'=> 'Pendapatan DP Badminton - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>jam  '.$simpan_jam_main,//.' ('.$caption_jam_siang.$caption_jam_malam.')',
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $q_transaksi_sebelumnya['id_metode_pembayaran'],
                    'id_identitas_order' => $id_identitas_order_badminton,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan_dp);
                $nilai_sisa_pelunasan = $simpan_total   - $dp;
                $pendapatan_pelunasan = [
                    'id_transaksi'=>$id_transaksi,
                    'id_transaksi_pelunasan'=>'',
                    'fasilitas'=>'Badminton - Harian',
                    'kelompok_fasilitas'=>'badminton',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>2,
                    'nilai'=>$nilai_sisa_pelunasan,
                    'item_transaksi'=>'Pelunasan Badminton - Harian',
                    'keterangan'=> 'Pendapatan pelunasan Badminton - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>selama  '.$simpan_jam_main,
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $metode_pembayaran,
                    'id_identitas_order' => $id_identitas_order_badminton,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan_pelunasan);
            }else{
                $pendapatan = [
                    'id_transaksi'=>$id_transaksi,
                    'id_transaksi_pelunasan'=>'',
                    'fasilitas'=>'Badminton - Harian',
                    'kelompok_fasilitas'=>'badminton',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>2,
                    'nilai'=>$simpan_total,
                    'item_transaksi'=>'Badminton - Harian',
                    'keterangan'=> 'Pendapatan Badminton - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>selama  '.$simpan_jam_main,
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $metode_pembayaran,
                    'id_identitas_order' => $id_identitas_order_badminton,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan);

            }


            // if ($sisa>0) {



            // array_push($kumpul_pendapatan, $pendapatan);
            // }









        if ($kategori_diskon !='') {
            $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;

           

        if ($q_transaksi_sebelumnya['jenis_potongan']=='Persentase') {
              $potongan   = $total_biaya  * ($q_transaksi_sebelumnya['besar_diskon'] / 100);
        }else{
            $potongan = $q_transaksi_sebelumnya['besar_diskon'];
        }





              $pendapatan_keluad_diskon = [
                'id_transaksi'=>$id_transaksi,
                'id_transaksi_pelunasan'=>'',
                'fasilitas'=>'Badminton - Harian',
                'kelompok_fasilitas'=>'badminton',
                'kategori'=>'Keluar',
                'nilai'=>$potongan,
                'id_akun_pendapatan'=>2,
                'item_transaksi'=>'Potongan Harga',
                'keterangan'=> $keterangan_diskon,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),

                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' =>'',
                'lapangan' =>'',
                'tgl_main' =>'',
                'jam_main' =>'',
            ];
            array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
        }



        $this->db->insert_batch('pendapatan', $kumpul_pendapatan);
        $this->db->update('identitas_order_badminton',['id_transaksi'=>$id_transaksi],['id_booking_badminton'=>$id_booking]);
        $this->db->update('jadwal_badminton',['id_transaksi'=>$id_transaksi,'status'=>'Visited'],['id_identitas_order_badminton'=>$id_booking,'status'=>'Booking']);







        if ($simpan_sisa>0) {



            $pendapatan_semua = $sisa;
            $jumlah_jam_main = count($q_jadwal);
            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $input_dibayar_2 = str_replace(',', '', $this->input->post('input_dibayar_2'));
                $input_dibayar = str_replace(',', '', $this->input->post('input_dibayar'));
                $simpan_dibayar = $input_dibayar.','.$input_dibayar_2;
                $masuk_pendapatan_1 = $input_dibayar;

                $masuk_pendapatan_2 = $pendapatan_semua - $input_dibayar;
                $data_pembayaran = [
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran, 
                        'fasilitas' => 'Badminton - Harian', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar, 
                        'pendapatan' => $masuk_pendapatan_1, 
		                'id_akun_pendapatan' => 2,

                        'item_transaksi' => 'Badminton - Harian', 
                        'keterangan'=>'Pembayaran Pelunasan Badminton - Harian selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran_2, 
                        'fasilitas' => 'Badminton - Harian', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar_2, 
                        'pendapatan' => $masuk_pendapatan_2, 
		                'id_akun_pendapatan' => 2,

                        'item_transaksi' => 'Badminton - Harian', 
                        'keterangan'=>'Pembayaran Badminton - Harian selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                   
                ];
                $this->db->insert_batch('pembayaran', $data_pembayaran);

            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar = $this->input->post('simpan_dibayar');
                $data_pembayaran = [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $simpan_metode_pembayaran, 
                        'fasilitas' => 'Badminton - Harian', 
                        'kategori' => '1 Metode Pembayaran', 
                        'dibayar' => $simpan_dibayar, 
                        'pendapatan' => $pendapatan_semua, 
		                'id_akun_pendapatan' => 2,

                        'item_transaksi' => 'Badminton - Harian', 
                        'keterangan'=>'Pembayaran Badminton - Harian selama '.$jumlah_jam_main.' Jam'.$simpan_jam_main,
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                ];
                $this->db->insert('pembayaran', $data_pembayaran);


            }

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }else{

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }








    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data','id_transaksi'=>''];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan','id_transaksi'=>$id_transaksi];
            $this->db->trans_commit();
    }
    echo json_encode($output);



    


    }










    public function simpan_visit_pickle_harian()
    {
        $id_booking = $this->input->post('id_booking');
        $id_transaksi_sementara = $this->input->post('id_transaksi_sementara');
        $simpan_total = $this->input->post('simpan_total');
        $biaya_perorang = $this->input->post('biaya_perorang');
        $kategori_diskon = $this->input->post('kategori_diskon');
        $id_diskon = $this->input->post('id_diskon');
        $nama_diskon = $this->input->post('nama_diskon');
        $jenis_potongan = $this->input->post('jenis_potongan');
        $besar_potongan = $this->input->post('besar_potongan');
        $rp_nilai_potongan = $this->input->post('rp_nilai_potongan');
        $simpan_tagihan = $this->input->post('simpan_tagihan');
        $simpan_dibayar = $this->input->post('simpan_dibayar');
        $simpan_kembalian = $this->input->post('simpan_kembalian');
        $dp = $this->input->post('dp');
        $sisa = $this->input->post('sisa');
        $metode_pembayaran = $this->input->post('metode_pembayaran');
        $nama = $this->input->post('nama');
        $id_identitas_order_pickle = $this->input->post('id_identias_order');
        $this->db->trans_begin();
        $next_order = next_order();
        if ($next_order>0 && $next_order <10) {
            $next_no_transaksi = '000'.$next_order;
         }
         else if ($next_order>=10 && $next_order <100) {
            $next_no_transaksi = '00'.$next_order;
         }
         else if ($next_order>=100 && $next_order <1000) {
            $next_no_transaksi = '0'.$next_order;
         }else{
            $next_no_transaksi = $next_order;
         }
        $no_transaksi = date('Ymd').$next_no_transaksi;

         $q_transaksi_sebelumnya = $this->db->query("SELECT id_metode_pembayaran, pembayaran,jenis_potongan, dibayar, besar_diskon, tagihan, (dibayar-kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();
        $jenis_kembalian = $this->input->post('jenis_kembalian');
        if ($jenis_kembalian=='Return') {
            if ($simpan_tagihan < $dp) {
                
                $kembalian  = $simpan_kembalian;
                $dibayar_plg = $simpan_dibayar;
            }else{
                // $pembayaran  = $q_transaksi_sebelumnya['pembayaran'];
                // $simpan_dibayar_plg  = $q_transaksi_sebelumnya['tagihan'];
                $kembalian  = 0;//$q_transaksi_sebelumnya['sudah_dibayar'] - ($dp == '' ? $dp : 0 );
                $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
                // $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];
            }

                // if ($this->input->post('metode_pembayaran_2')!=null) {
                //     $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                //     $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                //     $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                //     $pembayaran = '2 Metode Pembayaran';
                // }else{
                //     $simpan_metode_pembayaran = $metode_pembayaran;
                //     $simpan_dibayar_plg = $simpan_dibayar;
                //     $pembayaran = '1 Metode Pembayaran';
                // }
                 $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];
                    $simpan_dibayar_plg = $q_transaksi_sebelumnya['dibayar'];
                    $pembayaran = $q_transaksi_sebelumnya['pembayaran'];
            # code...
        }else{
            $kembalian  = $simpan_kembalian;
            $dibayar_plg = $simpan_dibayar;

            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                $pembayaran = '2 Metode Pembayaran';
            }else{
                $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];//$metode_pembayaran;
                $simpan_dibayar_plg = $simpan_dibayar;
                $pembayaran = $q_transaksi_sebelumnya['pembayaran'];
            }


        }


            $simpan_sisa = $sisa == 'NaN' ? 0 : $sisa;

            $data_transaksi = [
                'id_user' => id_user(),
                'no_transaksi' => $no_transaksi,
                'id_metode_pembayaran' => $simpan_metode_pembayaran,
                'pembayaran' => $pembayaran,
                'id_akun_pendapatan' => 10,
                'kategori_transaksi' => 'Visited',
                'keterangan' => '',
                'nama_pengunjung' => $nama,
                'nohp_pengunjung' => '',
                'fasilitas' => 'Pickle - Harian',
                'kelompok_fasilitas' => 'pickle',
                'tgl_transaksi' => tgls(),
                'jam_transaksi' => jams(),
                'order_ke' => $next_order,
                'order_ke_fasilitas' => next_order_fasilitas('pickle - Harian'),
                'total' => $simpan_total,
                'kategori_potongan' => $kategori_diskon,
                'jenis_potongan' => $jenis_potongan,
                'id_diskon' => $id_diskon,
                'nama_diskon' => $nama_diskon,
                'besar_diskon' => $besar_potongan,
                'rp_nilai_diskon' => $rp_nilai_potongan,
                'tagihan' => $simpan_tagihan,
                'dp' => $dp,
                'sisa_pembayaran' => $sisa,
                'dibayar' => $simpan_dibayar_plg,
                'kembalian ' => $kembalian,
                'status ' => 'Settlement',
            ];

               $this->db->insert('transaksi', $data_transaksi);
                $id_transaksi = $this->db->insert_id();
               $this->db->update('transaksi',['status'=>'-','id_transaksi_pelunasan'=>$id_transaksi], ['id_transaksi'=>$id_transaksi_sementara]);

            $q_jadwal = $this->db->query("SELECT id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_pickle='$id_booking' and status='Booking'")->result_array();

            $kumpul_pendapatan = [];
            $total_biaya = 0;

            // $master_pickle = $this->db->query("SELECT harga from master_pickle")->result_array();
            // foreach ($master_pickle as $k => $v) {
                
            // }




            $hitung_jam_siang = 0;
            $hitung_jam_malam = 0;
            $kumpul_jam_main = [];
             foreach ($q_jadwal as $k => $v) {   
            $lapangan =  $v['lapangan'];
            $tgl_main =  $v['tgl_main'];
            $jam =  $v['jam_main'];
            $biaya = $v['biaya'];
            $total_biaya += $biaya;
            array_push($kumpul_jam_main, $jam);

            // if ($biaya==$master_pickle[0]['harga']) {
            //    $hitung_jam_siang++
            // }else{
            //    $hitung_jam_malam++

            // }
            //  $pendapatan = [
            //     'id_transaksi'=>$id_transaksi,
            //     'fasilitas'=>'Pickle - Harian',
            //     'kelompok_fasilitas'=>'Pickle',
            //     'kategori'=>'Masuk',
            //     'id_akun_pendapatan'=>2,
            //     'nilai'=>$biaya,
            //     'item_transaksi'=>'pickle - Harian',
            //     'keterangan'=> 'pickle - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>jam '.$jam,
            //     'tgl_transaksi'=>timestamp(),
            //     'id_user '=>id_user(),
            //     'status'=>'Settlement',
            //     'id_metode_pembayaran' => $metode_pembayaran,
            //     'id_identitas_order' => $id_identitas_order_pickle,
            //     'lapangan' => $lapangan,
            //     'tgl_main' => $tgl_main,
            //     'jam_main' => $jam,
            // ];
            // array_push($kumpul_pendapatan, $pendapatan);
        }

        $simpan_jam_main = join(',', $kumpul_jam_main);

            // $caption_jam_siang = $hitung_jam_siang >0 ? $hitung_jam_siang .' jam siang ,' : '';
            // $caption_jam_malam = $hitung_jam_malam >0 ? $hitung_jam_malam .' jam malam' : '';


            if ($simpan_sisa>0) {

                $pendapatan_dp = [
                    'id_transaksi'=>$id_transaksi_sementara,
                    'id_transaksi_pelunasan'=>$id_transaksi,
                    'fasilitas'=>'Pickle - Harian',
                    'kelompok_fasilitas'=>'Pickle',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>10,
                    'nilai'=>$dp,
                    'item_transaksi'=>'DP pickle - Harian',
                    'keterangan'=> 'Pendapatan DP pickle - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>jam  '.$simpan_jam_main,//.' ('.$caption_jam_siang.$caption_jam_malam.')',
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $q_transaksi_sebelumnya['id_metode_pembayaran'],
                    'id_identitas_order' => $id_identitas_order_pickle,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan_dp);
                $nilai_sisa_pelunasan = $simpan_total   - $dp;
                $pendapatan_pelunasan = [
                    'id_transaksi'=>$id_transaksi,
                    'id_transaksi_pelunasan'=>'',
                    'fasilitas'=>'Pickle - Harian',
                    'kelompok_fasilitas'=>'Pickle',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>10,
                    'nilai'=>$nilai_sisa_pelunasan,
                    'item_transaksi'=>'Pelunasan pickle - Harian',
                    'keterangan'=> 'Pendapatan pelunasan pickle - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>selama  '.$simpan_jam_main,
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $metode_pembayaran,
                    'id_identitas_order' => $id_identitas_order_pickle,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan_pelunasan);
            }else{
                $pendapatan = [
                    'id_transaksi'=>$id_transaksi,
                    'id_transaksi_pelunasan'=>'',
                    'fasilitas'=>'Pickle - Harian',
                    'kelompok_fasilitas'=>'Pickle',
                    'kategori'=>'Masuk',
                    'id_akun_pendapatan'=>10,
                    'nilai'=>$simpan_total,
                    'item_transaksi'=>'pickle - Harian',
                    'keterangan'=> 'Pendapatan pickle - Harian '.$nama.'<br>tgl '.$tgl_main.'<br>selama  '.$simpan_jam_main,
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                    'status'=>'Settlement',
                    'id_metode_pembayaran' => $metode_pembayaran,
                    'id_identitas_order' => $id_identitas_order_pickle,
                    'lapangan' => $lapangan,
                    'tgl_main' => $tgl_main,
                    'jam_main' => $jam,
                ];
                array_push($kumpul_pendapatan, $pendapatan);

            }


            // if ($sisa>0) {



            // array_push($kumpul_pendapatan, $pendapatan);
            // }









        if ($kategori_diskon !='') {
            $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;

           

        if ($q_transaksi_sebelumnya['jenis_potongan']=='Persentase') {
              $potongan   = $total_biaya  * ($q_transaksi_sebelumnya['besar_diskon'] / 100);
        }else{
            $potongan = $q_transaksi_sebelumnya['besar_diskon'];
        }





              $pendapatan_keluad_diskon = [
                'id_transaksi'=>$id_transaksi,
                'id_transaksi_pelunasan'=>'',
                'fasilitas'=>'Pickle - Harian',
                'kelompok_fasilitas'=>'Pickle',
                'kategori'=>'Keluar',
                'nilai'=>$potongan,
                'id_akun_pendapatan'=>10,
                'item_transaksi'=>'Potongan Harga',
                'keterangan'=> $keterangan_diskon,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),

                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' =>'',
                'lapangan' =>'',
                'tgl_main' =>'',
                'jam_main' =>'',
            ];
            array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
        }



        $this->db->insert_batch('pendapatan', $kumpul_pendapatan);
        $this->db->update('identitas_order_pickle',['id_transaksi'=>$id_transaksi],['id_booking_pickle'=>$id_booking]);
        $this->db->update('jadwal_badminton',['id_transaksi'=>$id_transaksi,'status'=>'Visited'],['id_identitas_order_pickle'=>$id_booking,'status'=>'Booking']);







        if ($simpan_sisa>0) {



            $pendapatan_semua = $sisa;
            $jumlah_jam_main = count($q_jadwal);
            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $input_dibayar_2 = str_replace(',', '', $this->input->post('input_dibayar_2'));
                $input_dibayar = str_replace(',', '', $this->input->post('input_dibayar'));
                $simpan_dibayar = $input_dibayar.','.$input_dibayar_2;
                $masuk_pendapatan_1 = $input_dibayar;

                $masuk_pendapatan_2 = $pendapatan_semua - $input_dibayar;
                $data_pembayaran = [
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran, 
                        'fasilitas' => 'Pickle - Harian', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar, 
                        'pendapatan' => $masuk_pendapatan_1, 
                        'id_akun_pendapatan' => 10,

                        'item_transaksi' => 'pickle - Harian', 
                        'keterangan'=>'Pembayaran Pelunasan pickle - Harian selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran_2, 
                        'fasilitas' => 'Pickle - Harian', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar_2, 
                        'pendapatan' => $masuk_pendapatan_2, 
                        'id_akun_pendapatan' => 10,

                        'item_transaksi' => 'pickle - Harian', 
                        'keterangan'=>'Pembayaran pickle - Harian selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                   
                ];
                $this->db->insert_batch('pembayaran', $data_pembayaran);

            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar = $this->input->post('simpan_dibayar');
                $data_pembayaran = [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $simpan_metode_pembayaran, 
                        'fasilitas' => 'Pickle - Harian', 
                        'kategori' => '1 Metode Pembayaran', 
                        'dibayar' => $simpan_dibayar, 
                        'pendapatan' => $pendapatan_semua, 
                        'id_akun_pendapatan' => 10,

                        'item_transaksi' => 'pickle - Harian', 
                        'keterangan'=>'Pembayaran pickle - Harian selama '.$jumlah_jam_main.' Jam'.$simpan_jam_main,
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                ];
                $this->db->insert('pembayaran', $data_pembayaran);


            }

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }else{

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }








    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data','id_transaksi'=>''];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan','id_transaksi'=>$id_transaksi];
            $this->db->trans_commit();
    }
    echo json_encode($output);



    


    }









    public function simpan_visit_futsal_turnamen()
    {
        
            $id_booking = $this->input->post('id_booking');
            $id_transaksi_sementara = $this->input->post('id_transaksi_sementara');
           
        $simpan_total = $this->input->post('simpan_total');
        $biaya_perorang = $this->input->post('biaya_perorang');
        $kategori_diskon = $this->input->post('kategori_diskon');
        $id_diskon = $this->input->post('id_diskon');
        $nama_diskon = $this->input->post('nama_diskon');
        $jenis_potongan = $this->input->post('jenis_potongan');
        $besar_potongan = $this->input->post('besar_potongan');
        $rp_nilai_potongan = $this->input->post('rp_nilai_potongan');
        $simpan_tagihan = $this->input->post('simpan_tagihan');
        $simpan_dibayar = $this->input->post('simpan_dibayar');
        $simpan_kembalian = $this->input->post('simpan_kembalian');
        $jenis_kembalian = $this->input->post('jenis_kembalian');

        $dp = $this->input->post('dp');
        $sisa = $this->input->post('sisa');

        $metode_pembayaran = $this->input->post('metode_pembayaran');

        $nama = $this->input->post('nama');
        $id_identitas_order_futsal = $this->input->post('id_identias_order');


        $this->db->trans_begin();


        $next_order = next_order();
        if ($next_order>0 && $next_order <10) {
            $next_no_transaksi = '000'.$next_order;
         }
         else if ($next_order>=10 && $next_order <100) {
            $next_no_transaksi = '00'.$next_order;
         }
         else if ($next_order>=100 && $next_order <1000) {
            $next_no_transaksi = '0'.$next_order;
         }else{
            $next_no_transaksi = '555'.$next_order;
         }
        $no_transaksi = date('Ymd').$next_no_transaksi;
        
         $q_transaksi_sebelumnya = $this->db->query("SELECT jenis_potongan, besar_diskon, tagihan, pembayaran, id_metode_pembayaran, (dibayar-kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();

        // if ($jenis_kembalian=='Return') {
        //     $kembalian  = $q_transaksi_sebelumnya['sudah_dibayar'] - $simpan_total;
        //     $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
        //     # code...
        // }else{
        //     $kembalian  = $simpan_kembalian;
        //     $dibayar_plg = $simpan_dibayar;

        // }



        if ($jenis_kembalian=='Return') {
            if ($simpan_tagihan < $dp) {
                
                $kembalian  = $simpan_kembalian;
                $dibayar_plg = $simpan_dibayar;


                if ($this->input->post('metode_pembayaran_2')!=null) {
                    $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                    $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                    $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                    $pembayaran = '2 Metode Pembayaran';
                }else{
                    $simpan_metode_pembayaran = $metode_pembayaran;
                    $simpan_dibayar_plg = $simpan_dibayar;
                    $pembayaran = '1 Metode Pembayaran';
                }


            }else{
                $pembayaran  = $q_transaksi_sebelumnya['pembayaran'];
                $simpan_dibayar_plg  = $q_transaksi_sebelumnya['tagihan'];
                $kembalian  = $q_transaksi_sebelumnya['sudah_dibayar'] - $simpan_total;
                $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
                $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];

            }
            # code...
        }else{
            $kembalian  = $simpan_kembalian;
            $dibayar_plg = $simpan_dibayar;

            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                $pembayaran = '2 Metode Pembayaran';
            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar_plg = $simpan_dibayar;
                $pembayaran = '1 Metode Pembayaran';
            }


        }


            $simpan_sisa = $sisa == 'NaN' ? 0 : $sisa;



            $data_transaksi = [
                'id_user' => id_user(),
                'no_transaksi' => $no_transaksi,
                'id_metode_pembayaran' => $simpan_metode_pembayaran,
                'pembayaran' => $pembayaran,
                'id_akun_pendapatan' => 1,
                'kategori_transaksi' => 'Visited',
                'keterangan' => '',
                'nama_pengunjung' => $nama,
                'nohp_pengunjung' => '',
                'fasilitas' => 'Futsal - Turnamen',
                'kelompok_fasilitas' => 'Futsal',
                'tgl_transaksi' => tgls(),
                'jam_transaksi' => jams(),
                'order_ke' => $next_order,
                'order_ke_fasilitas' => next_order_fasilitas('Futsal - Turnamen'),
                'total' => $simpan_total,
                'kategori_potongan' => $kategori_diskon,
                'jenis_potongan' => $jenis_potongan,
                'id_diskon' => $id_diskon,
                'nama_diskon' => $nama_diskon,
                'besar_diskon' => $besar_potongan,
                'rp_nilai_diskon' => $rp_nilai_potongan,
                'tagihan' => $simpan_tagihan,
                'dp' => $dp,
                'sisa_pembayaran' => $sisa,
                'dibayar' => $simpan_dibayar_plg ,
                'kembalian ' => $kembalian,
                'status ' => 'Settlement',
            ];



               $this->db->insert('transaksi', $data_transaksi);
                $id_transaksi = $this->db->insert_id();
               $this->db->update('transaksi',['status'=>'-','id_transaksi_pelunasan'=>$id_transaksi], ['id_transaksi'=>$id_transaksi_sementara]);




               // $this->db->delete('transaksi', ['id_transaksi'=>$id_transaksi_sementara]);
               // $this->db->insert('transaksi', $data_transaksi);
               //  $id_transaksi = $this->db->insert_id();

            $q_jadwal = $this->db->query("SELECT id_detail_booking_futsal, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_futsal where id_identitas_order_futsal='$id_booking' and status='Booking'")->result_array();


            $q_biaya_tambahan = $this->db->query("SELECT * from pendapatan where id_transaksi='$id_transaksi_sementara'")->result_array();
            $tgl_main = '';
            $kumpul_pendapatan = [];


            $kumpul_jam_main = [];

             foreach ($q_jadwal as $k => $v) {   
            $lapangan =  $v['lapangan'];
            $tgl_main =  $v['tgl_main'];
            $jam =  $v['jam_main'];
            $biaya = $v['biaya'];

            array_push($kumpul_jam_main, $jam);

             $pendapatan = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Futsal - Turnamen',
                'kelompok_fasilitas'=>'Futsal',
                'kategori'=>'Masuk',
                'id_akun_pendapatan'=>1,
                'nilai'=>$biaya,
                'item_transaksi'=>'Futsal - Turnamen',
                'keterangan'=> 'Futsal - Turnamen '.$nama.'<br>tgl '.$tgl_main.'<br>jam '.$jam,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' => $id_identitas_order_futsal,
                'lapangan' => $lapangan,
                'tgl_main' => $tgl_main,
                'jam_main' => $jam,
            ];
            array_push($kumpul_pendapatan, $pendapatan);
        }
        foreach ($q_biaya_tambahan as $k => $v) { 
            $biaya = $v['nilai'];
            $keterangan = $v['keterangan'];
            $item_transaksi = $v['item_transaksi'];


             $pendapatan = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Futsal - Turnamen',
                'kelompok_fasilitas'=>'Pendapatan Lain-lain',
                'kategori'=>'Masuk',
                'id_akun_pendapatan'=>9,
                'nilai'=>$biaya,
                'item_transaksi'=>$item_transaksi,
                'keterangan'=> $keterangan,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' => $id_identitas_order_futsal,
                'lapangan' => '',
                'tgl_main' => '',
                'jam_main' => '',
            ];
            array_push($kumpul_pendapatan, $pendapatan);
        }




        if ($kategori_diskon !='') {
            $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;
              $pendapatan_keluad_diskon = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Futsal - Harian',
                'kelompok_fasilitas'=>'Futsal',
                'kategori'=>'Keluar',
                'nilai'=>$rp_nilai_potongan,
                'id_akun_pendapatan'=>1,
                'item_transaksi'=>'Potongan Harga',
                'keterangan'=> $keterangan_diskon,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),

                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' =>'',
                'lapangan' =>'',
                'tgl_main' =>'',
                'jam_main' =>'',
            ];
            array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
        }



        $this->db->insert_batch('pendapatan', $kumpul_pendapatan);
        $this->db->update('identitas_order_futsal',['id_transaksi'=>$id_transaksi],['id_booking_futsal'=>$id_booking]);
        $this->db->update('jadwal_futsal',['id_transaksi'=>$id_transaksi,'status'=>'Visited'],['id_identitas_order_futsal'=>$id_booking,'status'=>'Booking']);






        $simpan_jam_main = join(',', $kumpul_jam_main);

        if ($simpan_sisa>0) {



            $pendapatan_semua = $sisa;
            $jumlah_jam_main = 1;//count($q_jadwal);
            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $input_dibayar_2 = str_replace(',', '', $this->input->post('input_dibayar_2'));
                $input_dibayar = str_replace(',', '', $this->input->post('input_dibayar'));
                $simpan_dibayar = $input_dibayar.','.$input_dibayar_2;
                $masuk_pendapatan_1 = $input_dibayar;

                $masuk_pendapatan_2 = $pendapatan_semua - $input_dibayar;
                $data_pembayaran = [
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran, 
                        'fasilitas' => 'Futsal - Turnamen', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar, 
                        'pendapatan' => $masuk_pendapatan_1, 
		                'id_akun_pendapatan' => 1,

                        'item_transaksi' => 'Futsal - Turnamen', 
                        'keterangan'=>'Pembayaran Pelunasan Futsal - Turnamen selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran_2, 
                        'fasilitas' => 'Futsal - Turnamen', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar_2, 
                        'pendapatan' => $masuk_pendapatan_2, 
		                'id_akun_pendapatan' => 1,

                        'item_transaksi' => 'Futsal - Turnamen', 
                        'keterangan'=>'Pembayaran Futsal - Turnamen selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                   
                ];
                $this->db->insert_batch('pembayaran', $data_pembayaran);

            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar = $this->input->post('simpan_dibayar');
                $data_pembayaran = [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $simpan_metode_pembayaran, 
                        'fasilitas' => 'Futsal - Turnamen', 
                        'kategori' => '1 Metode Pembayaran', 
                        'dibayar' => $simpan_dibayar, 
                        'pendapatan' => $pendapatan_semua, 
		                'id_akun_pendapatan' => 1,

                        'item_transaksi' => 'Futsal - Turnamen', 
                        'keterangan'=>'Pembayaran Futsal - Turnamen selama '.$jumlah_jam_main.' Jam'.$simpan_jam_main,
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                ];
                $this->db->insert('pembayaran', $data_pembayaran);


            }

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }else{

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }






    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data','id_transaksi'=>''];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan','id_transaksi'=>$id_transaksi];
            $this->db->trans_commit();
    }
    echo json_encode($output);



    


    }




  

    public function simpan_visit_badminton_turnamen()
    {
        
            $id_booking = $this->input->post('id_booking');
            $id_transaksi_sementara = $this->input->post('id_transaksi_sementara');
           
        $simpan_total = $this->input->post('simpan_total');
        $biaya_perorang = $this->input->post('biaya_perorang');
        $kategori_diskon = $this->input->post('kategori_diskon');
        $id_diskon = $this->input->post('id_diskon');
        $nama_diskon = $this->input->post('nama_diskon');
        $jenis_potongan = $this->input->post('jenis_potongan');
        $besar_potongan = $this->input->post('besar_potongan');
        $rp_nilai_potongan = $this->input->post('rp_nilai_potongan');
        $simpan_tagihan = $this->input->post('simpan_tagihan');
        $simpan_dibayar = $this->input->post('simpan_dibayar');
        $simpan_kembalian = $this->input->post('simpan_kembalian');
        $jenis_kembalian = $this->input->post('jenis_kembalian');

        $dp = $this->input->post('dp');
        $sisa = $this->input->post('sisa');

        $metode_pembayaran = $this->input->post('metode_pembayaran');

        $nama = $this->input->post('nama');
        $id_identitas_order_badminton = $this->input->post('id_identias_order');


        $this->db->trans_begin();


        $next_order = next_order();
        if ($next_order>0 && $next_order <10) {
            $next_no_transaksi = '000'.$next_order;
         }
         else if ($next_order>=10 && $next_order <100) {
            $next_no_transaksi = '00'.$next_order;
         }
         else if ($next_order>=100 && $next_order <1000) {
            $next_no_transaksi = '0'.$next_order;
         }else{
            $next_no_transaksi = '555'.$next_order;
         }
        $no_transaksi = date('Ymd').$next_no_transaksi;
        
         $q_transaksi_sebelumnya = $this->db->query("SELECT jenis_potongan, besar_diskon,pembayaran, id_metode_pembayaran, tagihan, (dibayar-kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();

        // if ($jenis_kembalian=='Return') {
        //     $kembalian  = $q_transaksi_sebelumnya['sudah_dibayar'] - $simpan_total;
        //     $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
        //     # code...
        // }else{
        //     $kembalian  = $simpan_kembalian;
        //     $dibayar_plg = $simpan_dibayar;

        // }



        if ($jenis_kembalian=='Return') {
            if ($simpan_tagihan < $dp) {
                
                $kembalian  = $simpan_kembalian;
                $dibayar_plg = $simpan_dibayar;


                if ($this->input->post('metode_pembayaran_2')!=null) {
                    $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                    $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                    $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                    $pembayaran = '2 Metode Pembayaran';
                }else{
                    $simpan_metode_pembayaran = $metode_pembayaran;
                    $simpan_dibayar_plg = $simpan_dibayar;
                    $pembayaran = '1 Metode Pembayaran';
                }


            }else{
                $pembayaran  = $q_transaksi_sebelumnya['pembayaran'];
                $simpan_dibayar_plg  = $q_transaksi_sebelumnya['tagihan'];
                $kembalian  = $q_transaksi_sebelumnya['sudah_dibayar'] - $simpan_total;
                $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
                $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];

            }
            # code...
        }else{
            $kembalian  = $simpan_kembalian;
            $dibayar_plg = $simpan_dibayar;

            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                $pembayaran = '2 Metode Pembayaran';
            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar_plg = $simpan_dibayar;
                $pembayaran = '1 Metode Pembayaran';
            }


        }


            $simpan_sisa = $sisa == 'NaN' ? 0 : $sisa;



            $data_transaksi = [
                'id_user' => id_user(),
                'no_transaksi' => $no_transaksi,
                'id_metode_pembayaran' => $simpan_metode_pembayaran,
                'pembayaran' => $pembayaran,
                'id_akun_pendapatan' => 2,
                'kategori_transaksi' => 'Visited',
                'keterangan' => '',
                'nama_pengunjung' => $nama,
                'nohp_pengunjung' => '',
                'fasilitas' => 'Badminton - Turnamen',
                'kelompok_fasilitas' => 'Badminton',
                'tgl_transaksi' => tgls(),
                'jam_transaksi' => jams(),
                'order_ke' => $next_order,
                'order_ke_fasilitas' => next_order_fasilitas('Badminton - Turnamen'),
                'total' => $simpan_total,
                'kategori_potongan' => $kategori_diskon,
                'jenis_potongan' => $jenis_potongan,
                'id_diskon' => $id_diskon,
                'nama_diskon' => $nama_diskon,
                'besar_diskon' => $besar_potongan,
                'rp_nilai_diskon' => $rp_nilai_potongan,
                'tagihan' => $simpan_tagihan,
                'dp' => $dp,
                'sisa_pembayaran' => $sisa,
                'dibayar' => $simpan_dibayar_plg ,
                'kembalian ' => $kembalian,
                'status ' => 'Settlement',
            ];



               $this->db->insert('transaksi', $data_transaksi);
                $id_transaksi = $this->db->insert_id();
               $this->db->update('transaksi',['status'=>'-','id_transaksi_pelunasan'=>$id_transaksi], ['id_transaksi'=>$id_transaksi_sementara]);




               // $this->db->delete('transaksi', ['id_transaksi'=>$id_transaksi_sementara]);
               // $this->db->insert('transaksi', $data_transaksi);
               //  $id_transaksi = $this->db->insert_id();

            $q_jadwal = $this->db->query("SELECT id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_badminton='$id_booking' and status='Booking'")->result_array();


            $q_biaya_tambahan = $this->db->query("SELECT * from pendapatan where id_transaksi='$id_transaksi_sementara'")->result_array();
            $tgl_main = '';
            $kumpul_pendapatan = [];


            $kumpul_jam_main = [];

             foreach ($q_jadwal as $k => $v) {   
            $lapangan =  $v['lapangan'];
            $tgl_main =  $v['tgl_main'];
            $jam =  $v['jam_main'];
            $biaya = $v['biaya'];

            array_push($kumpul_jam_main, $jam);

             $pendapatan = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Badminton - Turnamen',
                'kelompok_fasilitas'=>'Badminton',
                'kategori'=>'Masuk',
                'id_akun_pendapatan'=>2,
                'nilai'=>$biaya,
                'item_transaksi'=>'Badminton - Turnamen',
                'keterangan'=> 'Badminton - Turnamen '.$nama.'<br>tgl '.$tgl_main.'<br>jam '.$jam,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' => $id_identitas_order_badminton,
                'lapangan' => $lapangan,
                'tgl_main' => $tgl_main,
                'jam_main' => $jam,
            ];
            array_push($kumpul_pendapatan, $pendapatan);
        }
        foreach ($q_biaya_tambahan as $k => $v) { 
            $biaya = $v['nilai'];
            $keterangan = $v['keterangan'];
            $item_transaksi = $v['item_transaksi'];


             $pendapatan = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Badminton - Turnamen',
                'kelompok_fasilitas'=>'Pendapatan Lain-lain',
                'kategori'=>'Masuk',
                'id_akun_pendapatan'=>9,
                'nilai'=>$biaya,
                'item_transaksi'=>$item_transaksi,
                'keterangan'=> $keterangan,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' => $id_identitas_order_badminton,
                'lapangan' => '',
                'tgl_main' => '',
                'jam_main' => '',
            ];
            array_push($kumpul_pendapatan, $pendapatan);
        }




        if ($kategori_diskon !='') {
            $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;
              $pendapatan_keluad_diskon = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Badminton - Harian',
                'kelompok_fasilitas'=>'Badminton',
                'kategori'=>'Keluar',
                'nilai'=>$rp_nilai_potongan,
                'id_akun_pendapatan'=>2,
                'item_transaksi'=>'Potongan Harga',
                'keterangan'=> $keterangan_diskon,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),

                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' =>'',
                'lapangan' =>'',
                'tgl_main' =>'',
                'jam_main' =>'',
            ];
            array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
        }



        $this->db->insert_batch('pendapatan', $kumpul_pendapatan);
        $this->db->update('identitas_order_badminton',['id_transaksi'=>$id_transaksi],['id_booking_badminton'=>$id_booking]);
        $this->db->update('jadwal_badminton',['id_transaksi'=>$id_transaksi,'status'=>'Visited'],['id_identitas_order_badminton'=>$id_booking,'status'=>'Booking']);






        $simpan_jam_main = join(',', $kumpul_jam_main);

        if ($simpan_sisa>0) {



            $pendapatan_semua = $sisa;
            $jumlah_jam_main = 1;//count($q_jadwal);
            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $input_dibayar_2 = str_replace(',', '', $this->input->post('input_dibayar_2'));
                $input_dibayar = str_replace(',', '', $this->input->post('input_dibayar'));
                $simpan_dibayar = $input_dibayar.','.$input_dibayar_2;
                $masuk_pendapatan_1 = $input_dibayar;

                $masuk_pendapatan_2 = $pendapatan_semua - $input_dibayar;
                $data_pembayaran = [
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran, 
                        'fasilitas' => 'Badminton - Turnamen', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar, 
                        'pendapatan' => $masuk_pendapatan_1,
		                'id_akun_pendapatan' => 2, 

                        'item_transaksi' => 'Badminton - Turnamen', 
                        'keterangan'=>'Pembayaran Pelunasan badminton - Turnamen selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran_2, 
                        'fasilitas' => 'Badminton - Turnamen', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar_2, 
                        'pendapatan' => $masuk_pendapatan_2, 
		                'id_akun_pendapatan' => 2,

                        'item_transaksi' => 'Badminton - Turnamen', 
                        'keterangan'=>'Pembayaran badminton - Turnamen selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                   
                ];
                $this->db->insert_batch('pembayaran', $data_pembayaran);

            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar = $this->input->post('simpan_dibayar');
                $data_pembayaran = [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $simpan_metode_pembayaran, 
                        'fasilitas' => 'Badminton - Turnamen', 
                        'kategori' => '1 Metode Pembayaran', 
                        'dibayar' => $simpan_dibayar, 
                        'pendapatan' => $pendapatan_semua, 

                        'item_transaksi' => 'Badminton - Turnamen', 
                        'keterangan'=>'Pembayaran badminton - Turnamen selama '.$jumlah_jam_main.' Jam'.$simpan_jam_main,
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                ];
                $this->db->insert('pembayaran', $data_pembayaran);


            }

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }else{

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }






    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data','id_transaksi'=>''];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan','id_transaksi'=>$id_transaksi];
            $this->db->trans_commit();
    }
    echo json_encode($output);



    


    }

    public function simpan_visit_pickle_turnamen()
    {
        
            $id_booking = $this->input->post('id_booking');
            $id_transaksi_sementara = $this->input->post('id_transaksi_sementara');
           
        $simpan_total = $this->input->post('simpan_total');
        $biaya_perorang = $this->input->post('biaya_perorang');
        $kategori_diskon = $this->input->post('kategori_diskon');
        $id_diskon = $this->input->post('id_diskon');
        $nama_diskon = $this->input->post('nama_diskon');
        $jenis_potongan = $this->input->post('jenis_potongan');
        $besar_potongan = $this->input->post('besar_potongan');
        $rp_nilai_potongan = $this->input->post('rp_nilai_potongan');
        $simpan_tagihan = $this->input->post('simpan_tagihan');
        $simpan_dibayar = $this->input->post('simpan_dibayar');
        $simpan_kembalian = $this->input->post('simpan_kembalian');
        $jenis_kembalian = $this->input->post('jenis_kembalian');

        $dp = $this->input->post('dp');
        $sisa = $this->input->post('sisa');

        $metode_pembayaran = $this->input->post('metode_pembayaran');

        $nama = $this->input->post('nama');
        $id_identitas_order_pickle = $this->input->post('id_identias_order');


        $this->db->trans_begin();


        $next_order = next_order();
        if ($next_order>0 && $next_order <10) {
            $next_no_transaksi = '000'.$next_order;
         }
         else if ($next_order>=10 && $next_order <100) {
            $next_no_transaksi = '00'.$next_order;
         }
         else if ($next_order>=100 && $next_order <1000) {
            $next_no_transaksi = '0'.$next_order;
         }else{
            $next_no_transaksi = '555'.$next_order;
         }
        $no_transaksi = date('Ymd').$next_no_transaksi;
        
         $q_transaksi_sebelumnya = $this->db->query("SELECT jenis_potongan, besar_diskon,pembayaran, id_metode_pembayaran, tagihan, (dibayar-kembalian) as sudah_dibayar from transaksi where id_transaksi='$id_transaksi_sementara'")->row_array();

        // if ($jenis_kembalian=='Return') {
        //     $kembalian  = $q_transaksi_sebelumnya['sudah_dibayar'] - $simpan_total;
        //     $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
        //     # code...
        // }else{
        //     $kembalian  = $simpan_kembalian;
        //     $dibayar_plg = $simpan_dibayar;

        // }



        if ($jenis_kembalian=='Return') {
            if ($simpan_tagihan < $dp) {
                
                $kembalian  = $simpan_kembalian;
                $dibayar_plg = $simpan_dibayar;


                if ($this->input->post('metode_pembayaran_2')!=null) {
                    $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                    $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                    $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                    $pembayaran = '2 Metode Pembayaran';
                }else{
                    $simpan_metode_pembayaran = $metode_pembayaran;
                    $simpan_dibayar_plg = $simpan_dibayar;
                    $pembayaran = '1 Metode Pembayaran';
                }


            }else{
                $pembayaran  = $q_transaksi_sebelumnya['pembayaran'];
                $simpan_dibayar_plg  = $q_transaksi_sebelumnya['tagihan'];
                $kembalian  = $q_transaksi_sebelumnya['sudah_dibayar'] - $simpan_total;
                $dibayar_plg = $q_transaksi_sebelumnya['sudah_dibayar'];
                $simpan_metode_pembayaran = $q_transaksi_sebelumnya['id_metode_pembayaran'];

            }
            # code...
        }else{
            $kembalian  = $simpan_kembalian;
            $dibayar_plg = $simpan_dibayar;

            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $simpan_dibayar_plg = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
                $pembayaran = '2 Metode Pembayaran';
            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar_plg = $simpan_dibayar;
                $pembayaran = '1 Metode Pembayaran';
            }


        }


            $simpan_sisa = $sisa == 'NaN' ? 0 : $sisa;



            $data_transaksi = [
                'id_user' => id_user(),
                'no_transaksi' => $no_transaksi,
                'id_metode_pembayaran' => $simpan_metode_pembayaran,
                'pembayaran' => $pembayaran,
                'id_akun_pendapatan' => 10,
                'kategori_transaksi' => 'Visited',
                'keterangan' => '',
                'nama_pengunjung' => $nama,
                'nohp_pengunjung' => '',
                'fasilitas' => 'Pickle - Turnamen',
                'kelompok_fasilitas' => 'Pickle',
                'tgl_transaksi' => tgls(),
                'jam_transaksi' => jams(),
                'order_ke' => $next_order,
                'order_ke_fasilitas' => next_order_fasilitas('pickle - Turnamen'),
                'total' => $simpan_total,
                'kategori_potongan' => $kategori_diskon,
                'jenis_potongan' => $jenis_potongan,
                'id_diskon' => $id_diskon,
                'nama_diskon' => $nama_diskon,
                'besar_diskon' => $besar_potongan,
                'rp_nilai_diskon' => $rp_nilai_potongan,
                'tagihan' => $simpan_tagihan,
                'dp' => $dp,
                'sisa_pembayaran' => $sisa,
                'dibayar' => $simpan_dibayar_plg ,
                'kembalian ' => $kembalian,
                'status ' => 'Settlement',
            ];



               $this->db->insert('transaksi', $data_transaksi);
                $id_transaksi = $this->db->insert_id();
               $this->db->update('transaksi',['status'=>'-','id_transaksi_pelunasan'=>$id_transaksi], ['id_transaksi'=>$id_transaksi_sementara]);




               // $this->db->delete('transaksi', ['id_transaksi'=>$id_transaksi_sementara]);
               // $this->db->insert('transaksi', $data_transaksi);
               //  $id_transaksi = $this->db->insert_id();

            $q_jadwal = $this->db->query("SELECT id_detail_booking_badminton, id_transaksi_sementara, jadwal_ke, fasilitas, lapangan, tgl_main, jam_main, biaya, status  from jadwal_badminton where id_identitas_order_pickle='$id_booking' and status='Booking'")->result_array();


            $q_biaya_tambahan = $this->db->query("SELECT * from pendapatan where id_transaksi='$id_transaksi_sementara'")->result_array();
            $tgl_main = '';
            $kumpul_pendapatan = [];


            $kumpul_jam_main = [];

             foreach ($q_jadwal as $k => $v) {   
            $lapangan =  $v['lapangan'];
            $tgl_main =  $v['tgl_main'];
            $jam =  $v['jam_main'];
            $biaya = $v['biaya'];

            array_push($kumpul_jam_main, $jam);

             $pendapatan = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Pickle - Turnamen',
                'kelompok_fasilitas'=>'Pickle',
                'kategori'=>'Masuk',
                'id_akun_pendapatan'=>10,
                'nilai'=>$biaya,
                'item_transaksi'=>'pickle - Turnamen',
                'keterangan'=> 'pickle - Turnamen '.$nama.'<br>tgl '.$tgl_main.'<br>jam '.$jam,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' => $id_identitas_order_pickle,
                'lapangan' => $lapangan,
                'tgl_main' => $tgl_main,
                'jam_main' => $jam,
            ];
            array_push($kumpul_pendapatan, $pendapatan);
        }
        foreach ($q_biaya_tambahan as $k => $v) { 
            $biaya = $v['nilai'];
            $keterangan = $v['keterangan'];
            $item_transaksi = $v['item_transaksi'];


             $pendapatan = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Pickle - Turnamen',
                'kelompok_fasilitas'=>'Pendapatan Lain-lain',
                'kategori'=>'Masuk',
                'id_akun_pendapatan'=>9,
                'nilai'=>$biaya,
                'item_transaksi'=>$item_transaksi,
                'keterangan'=> $keterangan,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' => $id_identitas_order_pickle,
                'lapangan' => '',
                'tgl_main' => '',
                'jam_main' => '',
            ];
            array_push($kumpul_pendapatan, $pendapatan);
        }




        if ($kategori_diskon !='') {
            $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;
              $pendapatan_keluad_diskon = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'pickle - Harian',
                'kelompok_fasilitas'=>'Pickle',
                'kategori'=>'Keluar',
                'nilai'=>$rp_nilai_potongan,
                'id_akun_pendapatan'=>10,
                'item_transaksi'=>'Potongan Harga',
                'keterangan'=> $keterangan_diskon,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),

                'status'=>'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                'id_identitas_order' =>'',
                'lapangan' =>'',
                'tgl_main' =>'',
                'jam_main' =>'',
            ];
            array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
        }



        $this->db->insert_batch('pendapatan', $kumpul_pendapatan);
        $this->db->update('identitas_order_pickle',['id_transaksi'=>$id_transaksi],['id_booking_pickle'=>$id_booking]);
        $this->db->update('jadwal_badminton',['id_transaksi'=>$id_transaksi,'status'=>'Visited'],['id_identitas_order_pickle'=>$id_booking,'status'=>'Booking']);






        $simpan_jam_main = join(',', $kumpul_jam_main);

        if ($simpan_sisa>0) {



            $pendapatan_semua = $sisa;
            $jumlah_jam_main = 1;//count($q_jadwal);
            if ($this->input->post('metode_pembayaran_2')!=null) {
                $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
                $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
                $input_dibayar_2 = str_replace(',', '', $this->input->post('input_dibayar_2'));
                $input_dibayar = str_replace(',', '', $this->input->post('input_dibayar'));
                $simpan_dibayar = $input_dibayar.','.$input_dibayar_2;
                $masuk_pendapatan_1 = $input_dibayar;

                $masuk_pendapatan_2 = $pendapatan_semua - $input_dibayar;
                $data_pembayaran = [
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran, 
                        'fasilitas' => 'Pickle - Turnamen', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar, 
                        'pendapatan' => $masuk_pendapatan_1,
                        'id_akun_pendapatan' => 10, 

                        'item_transaksi' => 'pickle - Turnamen', 
                        'keterangan'=>'Pembayaran Pelunasan pickle - Turnamen selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                    [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $metode_pembayaran_2, 
                        'fasilitas' => 'Pickle - Turnamen', 
                        'kategori' => '2 Metode Pembayaran', 
                        'dibayar' => $input_dibayar_2, 
                        'pendapatan' => $masuk_pendapatan_2, 
                        'id_akun_pendapatan' => 10,

                        'item_transaksi' => 'pickle - Turnamen', 
                        'keterangan'=>'Pembayaran pickle - Turnamen selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main.'  jam :'.$simpan_jam_main,
                
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                    ],
                   
                ];
                $this->db->insert_batch('pembayaran', $data_pembayaran);

            }else{
                $simpan_metode_pembayaran = $metode_pembayaran;
                $simpan_dibayar = $this->input->post('simpan_dibayar');
                $data_pembayaran = [
                        'id_transaksi' => $id_transaksi, 
                        'id_metode_pembayaran' => $simpan_metode_pembayaran, 
                        'fasilitas' => 'Pickle - Turnamen', 
                        'kategori' => '1 Metode Pembayaran', 
                        'dibayar' => $simpan_dibayar, 
                        'pendapatan' => $pendapatan_semua, 

                        'item_transaksi' => 'pickle - Turnamen', 
                        'keterangan'=>'Pembayaran pickle - Turnamen selama '.$jumlah_jam_main.' Jam'.$simpan_jam_main,
                        'tgl_transaksi' => timestamp(), 
                        'id_user' => id_user(), 
                        'status' => 'Settlement',  
                ];
                $this->db->insert('pembayaran', $data_pembayaran);


            }

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }else{

            $this->db->update('pembayaran',['status'=>'Settlement','id_transaksi_pelunasan'=>$id_transaksi],['id_transaksi'=>$id_transaksi_sementara]);
        }






    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data','id_transaksi'=>''];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan','id_transaksi'=>$id_transaksi];
            $this->db->trans_commit();
    }
    echo json_encode($output);



    


    }





    

 public function print_visit_futsal_harian($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.kategori_transaksi,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung, t.fasilitas, t.status,
            t.dp, t.sisa_pembayaran, t.pembayaran, t.id_metode_pembayaran,
            mu.nama as kasir,
            idf.nama, idf.lapangan, idf.no_hp, idf.id_transaksi_sementara, idf.id_transaksi
         from transaksi t 
         left join identitas_order_futsal idf on (t.id_transaksi = idf.id_transaksi or t.id_transaksi = idf.id_transaksi_sementara)
         left join master_user mu on t.id_user = mu.id_user
         where t.id_transaksi='$id_transaksi'")->row_array();
               
               // 
        if ($transaksi['id_transaksi']=='') {
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_futsal jf 
          where jf.id_transaksi_sementara='$id_transaksi'");

        }else{
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_futsal jf 
          where jf.id_transaksi='$id_transaksi'");


        }

 $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $data['metode_pembayaran']=$kumpul_metode_pembayaran;
        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * $print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer +50; 
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [ $print_setting['mpdf_format_paper_width'], $height],
            "margin_left" => $print_setting['mpdf_margin_left'],
            "margin_right" => $print_setting['mpdf_margin_right'],
            "margin_top" => $print_setting['mpdf_margin_top'],
            "margin_bottom" => $print_setting['mpdf_margin_bottom'],
            // 'orientation' => 'P',
            'tempDir' => '/tmp'
        ]);
        $data['transaksi']='Gym';
        $data['id_transaksi']=$id_transaksi;
        $data['produk']=$detail_transaksi->result_array();

        $caption_status_print = ['settlement'=>'Settlement','reprint'=>'Re-Print','preview'=>'Preview'];
        $data['status_print']=$caption_status_print[$status_print];
        $data['action']=$action;
        $data['transaksi']=$transaksi;
        $data['print_setting']=$print_setting;
        $data['t']='';
        $html = $this->load->view('user/gro/booking/print/futsal_harian', $data, true);
         // $html =  $this->load->view('user/kassa/member/print_order_member', $data, true);


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         // $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);


        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }




 public function print_visit_badminton_harian($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.kategori_transaksi,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung, t.fasilitas, t.status,
            t.dp, t.sisa_pembayaran, t.pembayaran, t.id_metode_pembayaran,
            mu.nama as kasir,
            idf.nama, idf.lapangan, idf.no_hp, idf.id_transaksi_sementara, idf.id_transaksi
         from transaksi t 
         left join identitas_order_badminton idf on (t.id_transaksi = idf.id_transaksi or t.id_transaksi = idf.id_transaksi_sementara)
         left join master_user mu on t.id_user = mu.id_user
         where t.id_transaksi='$id_transaksi'")->row_array();
               
               // 
        if ($transaksi['id_transaksi']=='') {
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_badminton jf 
          where jf.id_transaksi_sementara='$id_transaksi'");

        }else{
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_badminton jf 
          where jf.id_transaksi='$id_transaksi'");


        }

 $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $data['metode_pembayaran']=$kumpul_metode_pembayaran;
        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * $print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer +50; 
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [ $print_setting['mpdf_format_paper_width'], $height],
            "margin_left" => $print_setting['mpdf_margin_left'],
            "margin_right" => $print_setting['mpdf_margin_right'],
            "margin_top" => $print_setting['mpdf_margin_top'],
            "margin_bottom" => $print_setting['mpdf_margin_bottom'],
            // 'orientation' => 'P',
            'tempDir' => '/tmp'
        ]);
        $data['transaksi']='Gym';
        $data['id_transaksi']=$id_transaksi;
        $data['produk']=$detail_transaksi->result_array();

        $caption_status_print = ['settlement'=>'Settlement','reprint'=>'Re-Print','preview'=>'Preview'];
        $data['status_print']=$caption_status_print[$status_print];
        $data['action']=$action;
        $data['transaksi']=$transaksi;
        $data['print_setting']=$print_setting;
        $data['t']='';
        $html = $this->load->view('user/gro/booking/print/badminton_harian', $data, true);
         // $html =  $this->load->view('user/kassa/member/print_order_member', $data, true);


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         // $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);


        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }



 public function print_visit_pickle_harian($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.kategori_transaksi,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung, t.fasilitas, t.status,
            t.dp, t.sisa_pembayaran, t.pembayaran, t.id_metode_pembayaran,
            mu.nama as kasir,
            idf.nama, idf.lapangan, idf.no_hp, idf.id_transaksi_sementara, idf.id_transaksi
         from transaksi t 
         left join identitas_order_pickle idf on (t.id_transaksi = idf.id_transaksi or t.id_transaksi = idf.id_transaksi_sementara)
         left join master_user mu on t.id_user = mu.id_user
         where t.id_transaksi='$id_transaksi'")->row_array();
               
               // 
        if ($transaksi['id_transaksi']=='') {
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_badminton jf 
          where jf.id_transaksi_sementara='$id_transaksi'");

        }else{
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_badminton jf 
          where jf.id_transaksi='$id_transaksi'");


        }

 $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $data['metode_pembayaran']=$kumpul_metode_pembayaran;
        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * $print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer +50; 
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [ $print_setting['mpdf_format_paper_width'], $height],
            "margin_left" => $print_setting['mpdf_margin_left'],
            "margin_right" => $print_setting['mpdf_margin_right'],
            "margin_top" => $print_setting['mpdf_margin_top'],
            "margin_bottom" => $print_setting['mpdf_margin_bottom'],
            // 'orientation' => 'P',
            'tempDir' => '/tmp'
        ]);
        $data['transaksi']='Gym';
        $data['id_transaksi']=$id_transaksi;
        $data['produk']=$detail_transaksi->result_array();

        $caption_status_print = ['settlement'=>'Settlement','reprint'=>'Re-Print','preview'=>'Preview'];
        $data['status_print']=$caption_status_print[$status_print];
        $data['action']=$action;
        $data['transaksi']=$transaksi;
        $data['print_setting']=$print_setting;
        $data['t']='';
        $html = $this->load->view('user/gro/booking/print/pickle_harian', $data, true);
         // $html =  $this->load->view('user/kassa/member/print_order_member', $data, true);


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         // $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);


        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }




 public function print_visit_futsal_turnamen($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.kategori_transaksi,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung, t.fasilitas, t.status,
            t.dp, t.sisa_pembayaran, t.pembayaran, t.id_metode_pembayaran,
            mu.nama as kasir,
            mp.metode_pembayaran,
            idf.nama, idf.lapangan, idf.no_hp, idf.id_transaksi_sementara, idf.id_transaksi
         from transaksi t 
         left join master_metode_pembayaran mp on t.id_metode_pembayaran = mp.id_metode_pembayaran
         left join identitas_order_futsal idf on (t.id_transaksi = idf.id_transaksi or t.id_transaksi = idf.id_transaksi_sementara)
         left join master_user mu on t.id_user = mu.id_user
         where t.id_transaksi='$id_transaksi'")->row_array();
               
               // 
        if ($transaksi['id_transaksi']=='') {
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_futsal jf 
          where jf.id_transaksi_sementara='$id_transaksi'");

        }else{
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_futsal jf 
          where jf.id_transaksi='$id_transaksi'");


        }


        $q_biaya_tambahan = $this->db->query("SELECT item_transaksi, nilai from pendapatan where id_transaksi='$id_transaksi' and id_akun_pendapatan='9'")->result_array();

 $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $data['metode_pembayaran']=$kumpul_metode_pembayaran;
        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * $print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer +50; 
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [ $print_setting['mpdf_format_paper_width'], $height],
            "margin_left" => $print_setting['mpdf_margin_left'],
            "margin_right" => $print_setting['mpdf_margin_right'],
            "margin_top" => $print_setting['mpdf_margin_top'],
            "margin_bottom" => $print_setting['mpdf_margin_bottom'],
            // 'orientation' => 'P',
            'tempDir' => '/tmp'
        ]);
        $data['transaksi']='Gym';
        $data['id_transaksi']=$id_transaksi;
        $data['produk']=$detail_transaksi->result_array();
        $data['biaya_tambahan']=$q_biaya_tambahan;

        $caption_status_print = ['settlement'=>'Settlement','reprint'=>'Re-Print','preview'=>'Preview'];
        $data['status_print']=$caption_status_print[$status_print];
        $data['action']=$action;
        $data['transaksi']=$transaksi;
        $data['print_setting']=$print_setting;
        $data['t']='';
        $html = $this->load->view('user/gro/booking/print/futsal_turnamen', $data, true);
         // $html =  $this->load->view('user/kassa/member/print_order_member', $data, true);


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         // $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);


        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }



 public function print_visit_badminton_turnamen($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.kategori_transaksi,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung, t.fasilitas, t.status,
            t.dp, t.sisa_pembayaran, t.pembayaran, t.id_metode_pembayaran,
            mu.nama as kasir,
            mp.metode_pembayaran,
            idf.nama, idf.lapangan, idf.no_hp, idf.id_transaksi_sementara, idf.id_transaksi
         from transaksi t 
         left join master_metode_pembayaran mp on t.id_metode_pembayaran = mp.id_metode_pembayaran
         left join identitas_order_badminton idf on (t.id_transaksi = idf.id_transaksi or t.id_transaksi = idf.id_transaksi_sementara)
         left join master_user mu on t.id_user = mu.id_user
         where t.id_transaksi='$id_transaksi'")->row_array();
               
               // 
        if ($transaksi['id_transaksi']=='') {
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_badminton jf 
          where jf.id_transaksi_sementara='$id_transaksi'");

        }else{
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_badminton jf 
          where jf.id_transaksi='$id_transaksi'");


        }


        $q_biaya_tambahan = $this->db->query("SELECT item_transaksi, nilai from pendapatan where id_transaksi='$id_transaksi' and id_akun_pendapatan='9'")->result_array();

 $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $data['metode_pembayaran']=$kumpul_metode_pembayaran;
        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * $print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer +50; 
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [ $print_setting['mpdf_format_paper_width'], $height],
            "margin_left" => $print_setting['mpdf_margin_left'],
            "margin_right" => $print_setting['mpdf_margin_right'],
            "margin_top" => $print_setting['mpdf_margin_top'],
            "margin_bottom" => $print_setting['mpdf_margin_bottom'],
            // 'orientation' => 'P',
            'tempDir' => '/tmp'
        ]);
        $data['transaksi']='Gym';
        $data['id_transaksi']=$id_transaksi;
        $data['produk']=$detail_transaksi->result_array();
        $data['biaya_tambahan']=$q_biaya_tambahan;

        $caption_status_print = ['settlement'=>'Settlement','reprint'=>'Re-Print','preview'=>'Preview'];
        $data['status_print']=$caption_status_print[$status_print];
        $data['action']=$action;
        $data['transaksi']=$transaksi;
        $data['print_setting']=$print_setting;
        $data['t']='';
        $html = $this->load->view('user/gro/booking/print/badminton_turnamen', $data, true);
         // $html =  $this->load->view('user/kassa/member/print_order_member', $data, true);


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         // $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);


        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }




 public function print_visit_pickle_turnamen($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.kategori_transaksi,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung, t.fasilitas, t.status,
            t.dp, t.sisa_pembayaran, t.pembayaran, t.id_metode_pembayaran,
            mu.nama as kasir,
            mp.metode_pembayaran,
            idf.nama, idf.lapangan, idf.no_hp, idf.id_transaksi_sementara, idf.id_transaksi
         from transaksi t 
         left join master_metode_pembayaran mp on t.id_metode_pembayaran = mp.id_metode_pembayaran
         left join identitas_order_pickle idf on (t.id_transaksi = idf.id_transaksi or t.id_transaksi = idf.id_transaksi_sementara)
         left join master_user mu on t.id_user = mu.id_user
         where t.id_transaksi='$id_transaksi'")->row_array();
               
               // 
        if ($transaksi['id_transaksi']=='') {
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_badminton jf 
          where jf.id_transaksi_sementara='$id_transaksi'");

        }else{
        $detail_transaksi = $this->db->query("SELECT  biaya, status, tgl_main,
jam_main 
          from jadwal_badminton jf 
          where jf.id_transaksi='$id_transaksi'");


        }


        $q_biaya_tambahan = $this->db->query("SELECT item_transaksi, nilai from pendapatan where id_transaksi='$id_transaksi' and id_akun_pendapatan='9'")->result_array();

 $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $data['metode_pembayaran']=$kumpul_metode_pembayaran;
        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * $print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer +50; 
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [ $print_setting['mpdf_format_paper_width'], $height],
            "margin_left" => $print_setting['mpdf_margin_left'],
            "margin_right" => $print_setting['mpdf_margin_right'],
            "margin_top" => $print_setting['mpdf_margin_top'],
            "margin_bottom" => $print_setting['mpdf_margin_bottom'],
            // 'orientation' => 'P',
            'tempDir' => '/tmp'
        ]);
        $data['transaksi']='Gym';
        $data['id_transaksi']=$id_transaksi;
        $data['produk']=$detail_transaksi->result_array();
        $data['biaya_tambahan']=$q_biaya_tambahan;

        $caption_status_print = ['settlement'=>'Settlement','reprint'=>'Re-Print','preview'=>'Preview'];
        $data['status_print']=$caption_status_print[$status_print];
        $data['action']=$action;
        $data['transaksi']=$transaksi;
        $data['print_setting']=$print_setting;
        $data['t']='';
        $html = $this->load->view('user/gro/booking/print/pickle_turnamen', $data, true);
         // $html =  $this->load->view('user/kassa/member/print_order_member', $data, true);


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         // $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);


        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }


    public function cek_lapangan_futsal()
    {
        
            $id_booking = $this->input->post('id_booking');
            $id_jadwal = $this->input->post('id_jadwal');
            $tgl = $this->input->post('tgl');
            $q_jadwal_terpilih = $this->db->query("SELECT  jf.id_detail_booking_futsal, jf.id_identitas_order_futsal as id_identitas_order, jf.fasilitas, jf.lapangan, jf.tgl_main, jf.jam_main , jf.status, 
            iof.nama, iof.no_hp, iof.keterangan, iof.id_transaksi
             from jadwal_futsal jf 
            left join identitas_order_futsal iof on jf.id_identitas_order_futsal = iof.id_booking_futsal
            where jf.id_detail_booking_futsal='$id_jadwal'")->row_array();
          
          
            $q_jadwal_lain = $this->db->query("SELECT id_detail_booking_futsal, id_identitas_order_futsal, lapangan, id_transaksi, jam_main, tgl_main, status,fasilitas from jadwal_futsal where  id_identitas_order_futsal='$id_booking' and tgl_main='$tgl' ")->result_array();
         
            $output =[
                'data_jadwal_terpilih'=>$q_jadwal_terpilih,
                'data_jadwal_lainnya'=>$q_jadwal_lain,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }


    public function cek_lapangan_badminton()
    {
        
            $id_booking = $this->input->post('id_booking');
            $id_jadwal = $this->input->post('id_jadwal');
            $q_jadwal_terpilih = $this->db->query("SELECT  jf.id_detail_booking_badminton, jf.id_identitas_order_badminton as id_identitas_order, jf.fasilitas, jf.lapangan, jf.tgl_main, jf.jam_main , jf.status, 
            iof.nama, iof.no_hp, iof.keterangan, iof.id_transaksi
             from jadwal_badminton jf 
            left join identitas_order_badminton iof on jf.id_identitas_order_badminton = iof.id_booking_badminton
            where jf.id_detail_booking_badminton='$id_jadwal'")->row_array();
          
          
            $q_jadwal_lain = $this->db->query("SELECT lapangan, jam_main, tgl_main, status,fasilitas from jadwal_badminton where  id_identitas_order_badminton='$id_booking' ")->result_array();
         
            $output =[
                'data_jadwal_terpilih'=>$q_jadwal_terpilih,
                'data_jadwal_lainnya'=>$q_jadwal_lain,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }
    public function selesai_main_futsal()
    {
        
            $id_jadwal = $this->input->post('id_jadwal');
            $q_pelanggan = $this->db->query("UPDATE jadwal_futsal set status='Visit' where id_detail_booking_futsal='$id_jadwal'");
          

         
            // $output =[
            //     'success'=>true,
            // ];
            // echo json_encode($output);
        // $this->load->view('template/admin');
    }
    public function selesai_main_futsal_bulanan()
    {
        
            $id_jadwal = $this->input->post('id_jadwal');
            $q_pelanggan = $this->db->query("UPDATE jadwal_futsal set status='Visit' where id_detail_booking_futsal='$id_jadwal'");
          

         
            // $output =[
            //     'success'=>true,
            // ];
            // echo json_encode($output);
        // $this->load->view('template/admin');
    }

    public function selesai_main_pickle_bulanan()
    {
        
            $id_jadwal = $this->input->post('id_jadwal');
            $q_pelanggan = $this->db->query("UPDATE jadwal_badminton set status='Visit' where id_detail_booking_badminton='$id_jadwal'");
          

         
            // $output =[
            //     'success'=>true,
            // ];
            // echo json_encode($output);
        // $this->load->view('template/admin');
    }




    public function selesai_main_badminton_bulanan()
    {
        
            $id_jadwal = $this->input->post('id_jadwal');
            $q_pelanggan = $this->db->query("UPDATE jadwal_badminton set status='Visit' where id_detail_booking_badminton='$id_jadwal'");
          

         
            // $output =[
            //     'success'=>true,
            // ];
            // echo json_encode($output);
        // $this->load->view('template/admin');
    }

    
    public function cancel_futsal()
    {
        
            $id_transaksi = $this->input->post('id_transaksi');
            $id_booking = $this->input->post('id_booking');
            $tgl_main = $this->input->post('tgl_main');
            $jam_main = $this->input->post('jam_main');
            $id_identitas_order = $this->input->post('id_identitas_order');
            $this->db->trans_begin();
            $q_pelanggan = $this->db->query("UPDATE jadwal_futsal set status='Cancel' where id_detail_booking_futsal='$id_booking'");

          
             if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data' , 'id_transaksi'=> ''];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan' , 'id_transaksi'=> $id_transaksi , 'id_booking'=> $id_booking];
            $this->db->trans_commit();
    }

            echo json_encode($output);

    }



    public function cancel_badminton()
    {
        
            $id_transaksi = $this->input->post('id_transaksi');
            $id_booking = $this->input->post('id_booking');
            $tgl_main = $this->input->post('tgl_main');
            $jam_main = $this->input->post('jam_main');
            $id_identitas_order = $this->input->post('id_identitas_order');
            $this->db->trans_begin();
            $q_pelanggan = $this->db->query("UPDATE jadwal_badminton set status='Cancel' where id_detail_booking_badminton='$id_booking'");

          
             if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data' , 'id_transaksi'=> ''];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan' , 'id_transaksi'=> $id_transaksi , 'id_booking'=> $id_booking];
            $this->db->trans_commit();
    }

            echo json_encode($output);

    }



    public function selesai_main_futsal_semua_jadwal()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("UPDATE jadwal_futsal set status='Visit' where id_identitas_order_futsal='$id_booking'");
          

         
            // $output =[
            //     'success'=>true,
            // ];
            // echo json_encode($output);
        // $this->load->view('template/admin');
    }




    public function dt_order_badminton()
    {
        $data = [];
         $no             = $_POST['start'];
         $tgl             = $_POST['tgl'];
         $jenis             = $_POST['jenis'];

         if ($jenis=='harian') {
             $fasilitas_fipilih = 'Badminton - Harian';
         }else if ($jenis=='bulanan') {
             $fasilitas_fipilih = 'Badminton - Member Bulanan';
         }else{
             $fasilitas_fipilih = 'Badminton - Turnamen';

         }

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
            0=>'id_booking_badminton',
         ];

         if (!isset($valid_columns[$col])) {
             $order= null;
         }else{
            $order = $valid_columns[$col];
         }

         if ($order!=null) {
             $order_by = "order by id_booking_badminton desc, $order $dir";
             # code...
         }else{
             $order_by = "";

         }
         // untuk order by
         if ($key) {
            $q = $this->db->query("SELECT  id_booking_badminton, paket, nama, no_hp, iof.fasilitas from identitas_order_badminton iof left join jadwal_badminton jf on iof.id_booking_badminton=jf.id_identitas_order_badminton where jf.tgl_main='$tgl' and jf.fasilitas='$fasilitas_fipilih' and (nama like'%$key%') group by jf.id_identitas_order_badminton  limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT  id_booking_badminton, paket, nama, no_hp, iof.fasilitas from identitas_order_badminton iof left join jadwal_badminton jf on iof.id_booking_badminton=jf.id_identitas_order_badminton where jf.tgl_main='$tgl' and jf.fasilitas='$fasilitas_fipilih' group by jf.id_identitas_order_badminton $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT  id_detail_booking_badminton from jadwal_badminton where fasilitas='$fasilitas_fipilih' and tgl_main='$tgl' group by id_identitas_order_badminton")->num_rows();

        foreach ($q as $k => $v) {
            $no++;


            $row    = [];
            $row[]  = $no;
            $row[]  = $v['nama'];
            $row[]  = $v['no_hp'];
            // $row[]  = '<a href="'.$params.'/'.$parameter_tamu.'" target="_blank">'.$params.'/'.$parameter_tamu.'</a>';
            if ($jenis=='harian') {
                $row[]  = $v['fasilitas'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_badminton('."'".$v['id_booking_badminton']."','harian'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';
                # code...
            }else if($jenis=='bulanan'){ 
                $row[]  = $v['fasilitas'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_badminton('."'".$v['id_booking_badminton']."','bulanan'".')" class="btn btn-outline-info btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';

            }else{
                $row[]  = $v['fasilitas'].'<br>Paket : '.$v['paket'];
                $row[]  = '
                <div class="btn-group">
                <a href="javascript:void(0)" onclick="cek_jadwal_badminton('."'".$v['id_booking_badminton']."','turnamen'".')" class="btn btn-outline-danger btn-xs"><i class="fa fa-folder-open"></i></a>
                </div>';

            }
        


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

    public function cek_jadwal_badminton()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("SELECT * from identitas_order_badminton where id_booking_badminton='$id_booking'")->row_array();
            $q_jadwal = $this->db->query("SELECT * from jadwal_badminton where id_identitas_order_badminton='$id_booking'")->result_array();
          

         
            $output =[
                'data_pelanggan'=>$q_pelanggan,
                'data_jadwal'=>$q_jadwal,
            ];
            echo json_encode($output);
        // $this->load->view('template/admin');
    }
    public function selesai_main_badminton()
    {
        
            $id_jadwal = $this->input->post('id_jadwal');
            $q_pelanggan = $this->db->query("UPDATE jadwal_badminton set status='Visit' where id_detail_booking_badminton='$id_jadwal'");
          

         
            // $output =[
            //     'success'=>true,
            // ];
            // echo json_encode($output);
        // $this->load->view('template/admin');
    }
    public function selesai_main_badminton_semua_jadwal()
    {
        
            $id_booking = $this->input->post('id_booking');
            $q_pelanggan = $this->db->query("UPDATE jadwal_badminton set status='Visit' where id_identitas_order_badminton='$id_booking'");
          

         
            // $output =[
            //     'success'=>true,
            // ];
            // echo json_encode($output);
        // $this->load->view('template/admin');
    }
}
