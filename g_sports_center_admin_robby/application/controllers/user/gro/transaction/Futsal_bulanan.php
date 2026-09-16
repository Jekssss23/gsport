<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Futsal_bulanan extends CI_Controller {


    public function jadwal_tersedia()
    {
        $tgl = $this->input->post('tgl_main');
        $lapangan = $this->input->post('lapangan');
        $q = $this->db->query("SELECT jam_main from jadwal_futsal  where tgl_main='$tgl' and lapangan='$lapangan'  and status !='Cancel'")->result_array();
        $q_master = $this->db->query("SELECT jam_mulai, jam_berakhir from master_futsal")->result_array();


        $jam_siang_mulai = explode(':', $q_master[0]['jam_mulai']);
        $jam_siang_selesai = explode(':', $q_master[0]['jam_berakhir']);

        $jam_malam_mulai = explode(':', $q_master[1]['jam_mulai']);
        $jam_malam_selesai = explode(':', $q_master[1]['jam_berakhir']);

        $kumpul_jam_terpakai= [];
        foreach ($q as $k => $v) {
            $kumpul_jam_terpakai[] = $v['jam_main'];
        }
        $kumpul_jam_tersedia = [];
        $kumpul_harga = [];
        $biaya = $this->harga();

        for ($i=7; $i < 24 ; $i++) { 
            if ($i==23) {
                $jam_jext = '00';
                # code...
            }else{
                $jam_jext = $i+1;

            }

            if ($i >=$jam_siang_mulai[0] && $i <$jam_siang_selesai[0]) {
                $harga = $biaya['siang']['harga'];
            }
            // else if ($i >=$jam_malam_mulai[0] && $i <$jam_malam_selesai[0]) {
            //     $harga = $biaya['malam']['harga'];
            // }
            else{
                // $harga = 'Error';
                $harga = $biaya['malam']['harga'];

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

        


            if (in_array($jam, $kumpul_jam_terpakai)) {

                $data = [
                    'jam'=>$jam,
                    'harga'=>$harga,
                    'tersedia'=>'0',
                ];
                # code...
            }else{
                $data = [
                    'jam'=>$jam,
                    'harga'=>$harga,
                    'tersedia'=>'1',
                ];

            }
                array_push($kumpul_jam_tersedia, $data);
        }

        $show_jam_tersedia = array_chunk($kumpul_jam_tersedia, 6);
        echo json_encode($show_jam_tersedia);
    }
    public function jadwal_futsal_tersetting()
    {
        $tgl = $this->input->post('tgl_main');
        $lapangan = $this->input->post('lapangan');
        $q = $this->db->query("SELECT biaya from jadwal_futsal where status='Set'")->result_array();


        $kumpul_jadwal_terpilih= [];
        foreach ($q as $k => $v) {
            $kumpul_jadwal_terpilih[] = $v['biaya'];
        }
       
        $output = [
            'total_biaya' =>array_sum($kumpul_jadwal_terpilih),
            'total_jam' => count($kumpul_jadwal_terpilih)
        ];
        echo json_encode($output);
    }
    public function harga()
    {
        
        $q_biaya = $this->db->query("SELECT harga from master_futsal")->result_array();
        $harga = [
            'siang'=> [
                'harga'=>$q_biaya[0]['harga']
            ], 
            'malam'=> [
                'harga'=>$q_biaya[1]['harga']
            ], 
        ];
        return $harga;

    }





 public function update_jadwal_main(){

        $tgl_main = $this->input->post('tgl_main');
        $lapangan = $this->input->post('lapangan');
        $id_jadwal = $this->input->post('id_jadwal');
        $jam_main = $this->input->post('jam_main');
        $total_biaya = $this->input->post('total_biaya');
        $where = ['id_detail_booking_futsal'=>$id_jadwal];
        $this->db->trans_begin();
        $data = [
            'jam_main'=>$jam_main, 
            'tgl_main'=>$tgl_main, 
            'lapangan'=>$lapangan, 
            'biaya_perubahan'=>$total_biaya, 
        ];
        $this->db->update('jadwal_futsal', $data, $where);


    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data'];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan'];
            $this->db->trans_commit();
    }
    echo json_encode($output);



    }




 public function set_jadwal_main(){

        $tgl_main = $this->input->post('tgl_main');
        $jadwal_ke = $this->input->post('jadwal_ke');
        $lapangan = $this->input->post('lapangan');


        $jam_main = $this->input->post('jam_main');


        $this->db->trans_begin();

        $delete = $this->db->query("DELETE FROM jadwal_futsal where status='Set' and tgl_main='$tgl_main' and jadwal_ke='$jadwal_ke'");
      
        $kumpul_jam_main = [];
        foreach ($jam_main as $k => $v) {   
            $pecah_jam = explode('|', $v);
            $jam =  $pecah_jam[0];
            $biaya = $pecah_jam[1];

            $data = [
                // 'id_identitas_order_futsal' => $id_identitas_order_futsal,
                'lapangan' => $lapangan,
                'tgl_main' => $tgl_main,
                'jam_main' => $jam,
                'biaya' => $biaya,
                'jadwal_ke' => $jadwal_ke,
                'fasilitas'=>'Futsal - Member Bulanan',
                'status' => 'Set',
            ];

          
            array_push($kumpul_jam_main, $data);
        }

     
        $this->db->insert_batch('jadwal_futsal', $kumpul_jam_main );


       //  echo json_encode($data_keanggotaan);
           // $output = '<div class="alert alert-info">sukses</div>';
    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menyimpan data'];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan'];
            $this->db->trans_commit();
    }
    echo json_encode($output);



    }






 public function simpan_transaksi_futsal_bulanan(){
        

        $simpan_total = $this->input->post('simpan_total');


        $kategori_diskon = $this->input->post('kategori_diskon');
        $id_diskon = $this->input->post('id_diskon');
        $nama_diskon = $this->input->post('nama_diskon');
        $jenis_potongan = $this->input->post('jenis_potongan');
        $besar_potongan = $this->input->post('besar_potongan');
        $rp_nilai_potongan = $this->input->post('rp_nilai_potongan');
        $simpan_tagihan = $this->input->post('simpan_tagihan');
        $simpan_dibayar = $this->input->post('simpan_dibayar');
        $simpan_kembalian = $this->input->post('simpan_kembalian');


        $metode_pembayaran = $this->input->post('metode_pembayaran');
        $simpan_diskon_bulanan = $this->input->post('simpan_diskon_bulanan');
        $besar_diskon_bulanan = $this->input->post('besar_diskon_bulanan');
        $diskon_futsal_bulanan   = $this->input->post('diskon_bulanan');
        
        $kategori_order = $this->input->post('kategori_order');
       


        $status = 'Booking';
        $keterangan = $this->input->post('keterangan');
        $nama = $this->input->post('nama');
        $alamat = $this->input->post('alamat');
        $nohp = $this->input->post('nohp');



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



        if ($this->input->post('metode_pembayaran_2')!=null) {
            $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
            $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
            $simpan_dibayar = str_replace(',', '', $this->input->post('input_dibayar')).','.str_replace(',', '', $this->input->post('input_dibayar_2'));
            $pembayaran = '2 Metode Pembayaran';

        }else{
            $simpan_metode_pembayaran = $metode_pembayaran;
            $simpan_dibayar = $this->input->post('simpan_dibayar');
            $pembayaran = '1 Metode Pembayaran';

        }

        $data_transaksi = [
            'id_user' => id_user(),
            'no_transaksi' => $no_transaksi,
            'id_metode_pembayaran' => $simpan_metode_pembayaran,
            'id_akun_pendapatan' => 1,
            'pembayaran' => $pembayaran,
            'kategori_transaksi' => $status,
            'keterangan' => nl2br($keterangan),
            'nama_pengunjung' => $nama,
            'nohp_pengunjung' => $nohp,
            'fasilitas' => 'Futsal - Member Bulanan',
            'kelompok_fasilitas' => 'Futsal',
            'tgl_transaksi' => tgls(),
            'jam_transaksi' => jams(),
            'order_ke' => $next_order,
            'order_ke_fasilitas' => next_order_fasilitas('Futsal - Member Bulanan'),
            'total' => $simpan_total,
            'kategori_potongan' => $kategori_diskon,
            'jenis_potongan' => $jenis_potongan,
            'id_diskon' => $id_diskon,
            'nama_diskon' => $nama_diskon,
            'besar_diskon' => $besar_potongan,
            'rp_nilai_diskon' => $rp_nilai_potongan,
            'tagihan' => $simpan_tagihan,
            'dibayar' => $simpan_dibayar,
            'kembalian ' => $simpan_kembalian,
            'status ' => 'Settlement',
        ];

        header('Content-Type: application/json');
        $this->db->insert('transaksi', $data_transaksi);
        $id_transaksi = $this->db->insert_id();

        $data_identitas_order = [
            'id_transaksi' =>$id_transaksi, 
            'nama' =>$nama, 
            'no_hp' =>$nohp, 
            'fasilitas' => 'Futsal - Member Bulanan',
            'alamat' =>$alamat, 
            'status' =>$status, 
            'keterangan' =>nl2br($keterangan),  
            'kategori_order' =>$kategori_order, 
        ];


        $kumpul_pendapatan = [];

        
                $this->db->insert('identitas_order_futsal', $data_identitas_order);
        $id_identitas_order_futsal = $this->db->insert_id();
              $q = $this->db->update('jadwal_futsal',['status'=>$status,'id_transaksi'=>$id_transaksi,'id_identitas_order_futsal'=>$id_identitas_order_futsal], ['status'=>'Set']);
  
        $q_data = $this->db->select('jadwal_ke, lapangan, tgl_main,jam_main,biaya,status, jadwal_ke ')->where(['id_transaksi'=>$id_transaksi])->get('jadwal_futsal')->result_array();

        $jumlah_jam_main = count($q_data);
        foreach ($q_data as $k => $v) {   
            $biaya = $v['biaya'];
            $jam = $v['jam_main'];
            $tgl = $v['tgl_main'];
            $lapangan = $v['lapangan'];
            $jadwal_ke = $v['jadwal_ke'];

            $diskon_bulanan = $biaya * ($besar_diskon_bulanan /100 );
            $biaya_setelah_diskon = $biaya - $diskon_bulanan;
             $pendapatan = [
                'id_transaksi'=>$id_transaksi,
                'id_metode_pembayaran' => $metode_pembayaran,

                'fasilitas'=>'Futsal - Member Bulanan',
                'kelompok_fasilitas'=>'Futsal',
                'kategori'=>'Masuk',
                'nilai'=>$biaya_setelah_diskon,
                'id_akun_pendapatan'=>'1',
                'akun'=>'',
                'item_transaksi'=>'Main Jam : '.$jam,
                'keterangan'=> 'Futsal - Member Bulanan '.$nama.'<br>Jadwal ke : '.$jadwal_ke.' <br>tanggal '.$tgl.'<br>jam '.$jam,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'status'=>'Settlement',
                'id_identitas_order' => $id_identitas_order_futsal,
                'lapangan' => $lapangan,
                'tgl_main' => $tgl,
                'jam_main' => $jam,
                'jadwal_ke' => $jadwal_ke,


            ];
            array_push($kumpul_pendapatan, $pendapatan);
        }



        if ($kategori_diskon !='') {
            $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;
              $pendapatan_keluad_diskon = [


                'id_transaksi'=>$id_transaksi,
                
                'id_metode_pembayaran' => $metode_pembayaran,
                'fasilitas'=>'Futsal - Member Bulanan',
                'kelompok_fasilitas'=>'Futsal',
                'kategori'=>'Keluar',
                'nilai'=>$rp_nilai_potongan,
                'id_akun_pendapatan'=>'1',
                'akun'=>'',
                'item_transaksi'=>'Potongan harga ',
                'keterangan'=> $keterangan_diskon,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),

                'status'=>'Settlement',
                'id_identitas_order' => '',
                'lapangan' => '',
                'tgl_main' => '',
                'jam_main' => '',
                'jadwal_ke'=>''
            ];
            array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
        }
       
        $this->db->insert_batch('pendapatan', $kumpul_pendapatan);






        $pendapatan_semua = $simpan_tagihan;
        if ($this->input->post('metode_pembayaran_2')!=null) {
            $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');
            $simpan_metode_pembayaran = $metode_pembayaran.','.$metode_pembayaran_2;
            $input_dibayar_2 = str_replace(',', '', $this->input->post('input_dibayar_2'));
            $input_dibayar = str_replace(',', '', $this->input->post('input_dibayar'));
            $simpan_dibayar = $input_dibayar.','.$input_dibayar_2;
            $masuk_pendapatan_1 = $input_dibayar;

            if ($simpan_kembalian < 0 ) {
                $masuk_pendapatan_2 = $rp_simpan_dibayar - $input_dibayar;

            }else{
                $masuk_pendapatan_2 = $pendapatan_semua  - $input_dibayar;

            }



            $data_pembayaran = [
                [
                    'id_transaksi' => $id_transaksi, 
                    'id_metode_pembayaran' => $metode_pembayaran, 
                    'fasilitas' => 'Futsal - Member Bulanan', 
                    'kategori' => '2 Metode Pembayaran', 
                    'dibayar' => $input_dibayar, 
                    'pendapatan' => $masuk_pendapatan_1, 
                    'id_akun_pendapatan' => 1,

                    'item_transaksi' => 'Futsal - Member Bulanan', 
                    'keterangan'=>'Pembayaran Futsal - Member Bulanan a.n. '.$nama,
            
                    'tgl_transaksi' => timestamp(), 
                    'id_user' => id_user(), 
                    'status' => 'Settlement',  
                ],
                [
                    'id_transaksi' => $id_transaksi, 
                    'id_metode_pembayaran' => $metode_pembayaran_2, 
                    'fasilitas' => 'Futsal - Member Bulanan', 
                    'kategori' => '2 Metode Pembayaran', 
                    'dibayar' => $input_dibayar_2, 
                    'pendapatan' => $masuk_pendapatan_2, 
                    'id_akun_pendapatan' => 1,

                    'item_transaksi' => 'Futsal - Member Bulanan', 
                    'keterangan'=>'Pembayaran Futsal - Member Bulanan a.n. '.$nama,
            
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
                    'fasilitas' => 'Futsal - Member Bulanan', 
                    'kategori' => '1 Metode Pembayaran', 
                    'dibayar' => $simpan_dibayar, 
                    'pendapatan' => $pendapatan_semua, 
                    'id_akun_pendapatan' => 1,

                    'item_transaksi' => 'Futsal - Member Bulanan', 
                    'keterangan'=>'Pembayaran Futsal - Member Bulanan a.n. '.$nama,
                    'tgl_transaksi' => timestamp(), 
                    'id_user' => id_user(), 
                    'status' => 'Settlement',  
            ];
            $this->db->insert('pembayaran', $data_pembayaran);


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





 public function print_transaksi_futsal_bulanan($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.kategori_transaksi,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung, t.fasilitas, t.paket,
            t.pembayaran, t.id_metode_pembayaran,
            mu.nama as kasir,
            m.nama, m.qrcode, m.kode_unik_member,
            idf.nama, idf.lapangan, idf.no_hp, idf.tgl_main, idf.status
         from transaksi t 
         left join identitas_order_futsal idf on t.id_transaksi = idf.id_transaksi
         left join master_user mu on t.id_user = mu.id_user
         left join member m on t.id_pengunjung = m.id_member
         where t.id_transaksi='$id_transaksi'")->row_array();


        
        $detail_transaksi = $this->db->query("SELECT  biaya, status,
jam_main, tgl_main 
          from jadwal_futsal jf 
          where jf.id_transaksi='$id_transaksi'");

        $q_diskon = $this->db->query("SELECT diskon from diskon_member_futsal")->row_array();

        
        $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $data['metode_pembayaran']=$kumpul_metode_pembayaran;
        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * 20;//$print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer; 
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
        $data['diskon']=$q_diskon['diskon'];
        $data['transaksi']='Gym';
        $data['id_transaksi']=$id_transaksi;
        $data['produk']=$detail_transaksi->result_array();

        $caption_status_print = ['settlement'=>'Settlement','reprint'=>'Re-Print','preview'=>'Preview'];
        $data['status_print']=$caption_status_print[$status_print];
        $data['action']=$action;
        $data['transaksi']=$transaksi;
        $data['print_setting']=$print_setting;
        $data['t']='';
        $html = $this->load->view('user/gro/transaction/print/futsal_bulanan', $data, true);
         // $html =  $this->load->view('user/kassa/member/print_order_member', $data, true);


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);

     

        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }
}
