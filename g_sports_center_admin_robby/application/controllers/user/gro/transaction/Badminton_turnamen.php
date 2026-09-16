<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Badminton_turnamen extends CI_Controller {


    public function jadwal_tersedia()
    {
        $tgl = $this->input->post('tgl_main');
        $lapangan = $this->input->post('lapangan');
        $q = $this->db->query("SELECT jam_main from jadwal_badminton  where tgl_main='$tgl' and lapangan='$lapangan'  and status !='Cancel'")->result_array();


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

            if ($i >=7 && $i <=19) {
                $harga = $biaya['siang']['harga'];
            }else{
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
    public function harga()
    {
        
        $q_biaya = $this->db->query("SELECT harga from master_badminton")->result_array();
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





 public function simpan_transaksi_badminton(){
        

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

        $rp_simpan_dibayar = $simpan_dibayar;

        $metode_pembayaran = $this->input->post('metode_pembayaran');
        $jenis_pilihan = $this->input->post('jenis_pilihan');
        
        $status = $this->input->post('status');
        $lapangan = $this->input->post('lapangan');
        $tgl_main = $this->input->post('tgl_main');
        $nama_paket = $this->input->post('nama_paket');

       
        $keterangan = $this->input->post('keterangan');
        $kategori_order = $this->input->post('kategori_order');



        $nama = $this->input->post('nama');
        $alamat = $this->input->post('alamat');
        $nohp = $this->input->post('nohp');


        $jam_main = $this->input->post('jam_main');


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


        if ($simpan_kembalian< 0 ) {
            $data_transaksi = [
                'id_user' => id_user(),
                'no_transaksi' => $no_transaksi,
                'id_metode_pembayaran' => $simpan_metode_pembayaran,
                'id_akun_pendapatan' => '2,9',
                'pembayaran' => $pembayaran,
                'paket' => $nama_paket,
                'kategori_transaksi' => $status,
                'keterangan' => nl2br($keterangan),
                'nama_pengunjung' => $nama,
                'nohp_pengunjung' => $nohp,
                'fasilitas' => 'Badminton - Turnamen',
                'kelompok_fasilitas' => 'badminton',
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
                'dp' => $simpan_dibayar,
                'sisa_pembayaran' => $simpan_kembalian,
                'status ' => 'Preview',
            ];

               $this->db->insert('transaksi', $data_transaksi);
        $id_transaksi = $this->db->insert_id();

        $data_identitas_order = [
            'id_transaksi_sementara' =>$id_transaksi, 
            'lapangan' =>$lapangan, 
            'paket' => $nama_paket,
            'nama' =>$nama, 
            'fasilitas' => 'Badminton - Turnamen',
            'no_hp' =>$nohp, 
            'alamat' =>$alamat, 
            'kategori_order' =>$kategori_order, 
            'keterangan' =>nl2br($keterangan),  
            'tgl_main' =>$tgl_main, 
            'status' =>$status, 
        ];
        $this->db->insert('identitas_order_badminton', $data_identitas_order);




        }else{
            $data_transaksi = [
                'id_user' => id_user(),
                'no_transaksi' => $no_transaksi,
                'id_metode_pembayaran' => $simpan_metode_pembayaran,
                'id_akun_pendapatan' => '2,9',
                'pembayaran' => $pembayaran,
                'paket' => $nama_paket,
                'kategori_transaksi' => $status,
                'keterangan' => nl2br($keterangan),
                'nama_pengunjung' => $nama,
                'nohp_pengunjung' => $nohp,
                'fasilitas' => 'Badminton - Turnamen',
                'kelompok_fasilitas' => 'badminton',
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
                'dibayar' => $simpan_dibayar,
                'kembalian ' => $simpan_kembalian,
                'status ' => 'Preview',
            ];
        $this->db->insert('transaksi', $data_transaksi);
        $id_transaksi = $this->db->insert_id();

        $data_identitas_order = [
            'id_transaksi_sementara' =>$id_transaksi, 
            'lapangan' =>$lapangan, 
            'nama' =>$nama, 
            'paket' => $nama_paket,
            'fasilitas' => 'Badminton - Turnamen',
            'no_hp' =>$nohp, 
            'alamat' =>$alamat, 
            'keterangan' =>nl2br($keterangan), 
            'kategori_order' =>$kategori_order, 
            'tgl_main' =>$tgl_main, 
            'status' =>$status,  
        ];
        $this->db->insert('identitas_order_badminton', $data_identitas_order);
        }



        // $data_transaksi = [
        //     'id_user' => id_user(),
        //     'no_transaksi' => $no_transaksi,
        //     'id_metode_pembayaran' => $metode_pembayaran,
        //     'kategori_transaksi' => $status,
        //     'paket' => $nama_paket,
        //     'keterangan' => nl2br($keterangan),
        //     'nama_pengunjung' => $nama,
        //     'nohp_pengunjung' => $nohp,
        //     'fasilitas' => 'Badminton - Turnamen',
        //     'kelompok_fasilitas' => 'badminton',
        //     'tgl_transaksi' => tgls(),
        //     'jam_transaksi' => jams(),
        //     'order_ke' => $next_order,
        //     'order_ke_fasilitas' => next_order_fasilitas('Badminton - Turnamen'),
        //     'total' => $simpan_total,
        //     'kategori_potongan' => $kategori_diskon,
        //     'jenis_potongan' => $jenis_potongan,
        //     'id_diskon' => $id_diskon,
        //     'nama_diskon' => $nama_diskon,
        //     'besar_diskon' => $besar_potongan,
        //     'rp_nilai_diskon' => $rp_nilai_potongan,
        //     'tagihan' => $simpan_tagihan,
        //     'dibayar' => $simpan_dibayar,
        //     'kembalian ' => $simpan_kembalian,
        // ];

        header('Content-Type: application/json');
        $id_identitas_order_badminton = $this->db->insert_id();

        $kumpul_pendapatan = [];
        $kumpul_jam_main = [];
        foreach ($jam_main as $k => $v) {   
            $pecah_jam = explode('|', $v);
            $jam =  $pecah_jam[0];
            $biaya = $pecah_jam[1];

            $data = [
                'id_transaksi_sementara' => $id_transaksi,
                'id_identitas_order_badminton' => $id_identitas_order_badminton,
                'lapangan' => $lapangan,
                'tgl_main' => $tgl_main,
                'jam_main' => $jam,
                'biaya' => $biaya,
                'fasilitas'=>'Badminton - Turnamen',
                'status' => $status,
            ];

            //  $pendapatan = [
            //     'id_transaksi'=>$id_transaksi,
            //     'fasilitas'=>'Badminton - Turnamen',
            //     'kelompok_fasilitas'=>'badminton',
            //     'kategori'=>'Masuk',
            //     'nilai'=>$biaya,
            //     'id_akun_pendapatan'=>'1',
            //     'akun'=>'',
            //     'item_transaksi'=>'Main Jam : '.$jam,
            //     'keterangan'=> 'Badminton - Turnamen '.$nama.' | jam '.$jam,
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
            array_push($kumpul_jam_main, $data);
        }

        $biaya_kebersihan = $this->input->post('simpan_biaya_kebersihan');
        $biaya_sound_system = $this->input->post('simpan_biaya_sound_system');


             $pendapatan_kebersihan = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Badminton - Turnamen',
                'kelompok_fasilitas'=>'Pendapatan Lain lain',
                'kategori'=>'Masuk',
                'nilai'=>$biaya_kebersihan,
                'id_akun_pendapatan'=>'9',
                'akun'=>'',
                'item_transaksi'=>'Biaya Kebersihan',
                'keterangan'=> 'Biaya Kebersihan Badminton - Turnamen '.$nama,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),

                'status'=>'Pending',
                'id_metode_pembayaran' =>'',
                'id_identitas_order' => $id_identitas_order_badminton,
                'lapangan' =>'',
                'tgl_main' =>'',
                'jam_main' =>'',
            ];
             $pendapatan_sound_system = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Badminton - Turnamen',
                'kelompok_fasilitas'=>'Pendapatan Lain lain',
                'kategori'=>'Masuk',
                'nilai'=>$biaya_sound_system,
                'id_akun_pendapatan'=>'9',
                'akun'=>'',
                'item_transaksi'=>'Biaya Sound System',
                'keterangan'=> 'Biaya Sound System Badminton - Turnamen '.$nama,
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'status'=>'Pending',
                'id_metode_pembayaran' =>'',
                'id_identitas_order' => $id_identitas_order_badminton,
                'lapangan' =>'',
                'tgl_main' =>'',
                'jam_main' =>'',
            ];

            if ($biaya_sound_system>0) {
                array_push($kumpul_pendapatan, $pendapatan_sound_system);
                
            }
            if ($biaya_kebersihan>0) {
                array_push($kumpul_pendapatan, $pendapatan_kebersihan);
                
            }


        // if ($kategori_diskon !='') {
        //     $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;
        //       $pendapatan_keluad_diskon = [

        //         'id_transaksi'=>$id_transaksi,
        //         'fasilitas'=>'Badminton - Turnamen',
        //         'kelompok_fasilitas'=>'Pendapatan Lain lain',
        //         'kategori'=>'Keluar',
        //         'nilai'=>$rp_nilai_potongan,
        //         'id_akun_pendapatan'=>'1',
        //         'akun'=>'',
        //         'item_transaksi'=>'Potongan harga',
        //         'keterangan'=> $keterangan_diskon,
        //         'tgl_transaksi'=>timestamp(),
        //         'id_user '=>id_user(),
        //         'status'=>'Settlement',
        //         'id_metode_pembayaran' =>'',
        //         'id_identitas_order' => $id_identitas_order_badminton,
        //         'lapangan' =>'',
        //         'tgl_main' =>'',
        //         'jam_main' =>'',

        //     ];
        //     array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
        // }




        // echo json_encode($kumpul_jam_main);



       // // echo $jumlah_orang;
        if (count($kumpul_pendapatan)>0) {
            $this->db->insert_batch('pendapatan', $kumpul_pendapatan);
        }





            $pendapatan_semua = $simpan_tagihan;
        $jumlah_jam_main = count($jam_main);
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
                    'fasilitas' => 'Badminton - Turnamen', 
                    'kategori' => '2 Metode Pembayaran', 
                    'dibayar' => $input_dibayar, 
                    'pendapatan' => $masuk_pendapatan_1, 
                    'id_akun_pendapatan' => '2,9',

                    'item_transaksi' => 'Badminton - Turnamen', 
                    'keterangan'=>'Pembayaran Badminton - Turnamen selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main,
            
                    'tgl_transaksi' => timestamp(), 
                    'id_user' => id_user(), 
                    'status' => 'Preview',  
                ],
                [
                    'id_transaksi' => $id_transaksi, 
                    'id_metode_pembayaran' => $metode_pembayaran_2, 
                    'fasilitas' => 'Badminton - Turnamen', 
                    'kategori' => '2 Metode Pembayaran', 
                    'dibayar' => $input_dibayar_2, 
                    'pendapatan' => $masuk_pendapatan_2, 
                    'id_akun_pendapatan' => '2,9',

                    'item_transaksi' => 'Badminton - Turnamen', 
                    'keterangan'=>'Pembayaran Badminton - Turnamen selama '.$jumlah_jam_main.' Jam pada tanggal '.$tgl_main,
            
                    'tgl_transaksi' => timestamp(), 
                    'id_user' => id_user(), 
                    'status' => 'Preview',  
                ],
               
            ];
            $this->db->insert_batch('pembayaran', $data_pembayaran);

        }else{
            $simpan_metode_pembayaran = $metode_pembayaran;
            $simpan_dibayar = $this->input->post('simpan_dibayar');


            if ($simpan_kembalian < 0 ) {
                $masuk_pendapatan = $simpan_dibayar;

            }else{
                $masuk_pendapatan = $pendapatan_semua;

            }


            $data_pembayaran = [
                    'id_transaksi' => $id_transaksi, 
                    'id_metode_pembayaran' => $simpan_metode_pembayaran, 
                    'fasilitas' => 'Badminton - Turnamen', 
                    'kategori' => '1 Metode Pembayaran', 
                    'dibayar' => $simpan_dibayar, 
                    'pendapatan' => $masuk_pendapatan, 

                    'item_transaksi' => 'Badminton - Turnamen', 
                    'keterangan'=>'Pembayaran Badminton - Turnamen selama '.$jumlah_jam_main.' Jam',
                    'tgl_transaksi' => timestamp(), 
                    'id_user' => id_user(), 
                    'status' => 'Preview',  
            ];
            $this->db->insert('pembayaran', $data_pembayaran);


        }

        $this->db->insert_batch('jadwal_badminton', $kumpul_jam_main );


       //  echo json_encode($data_keanggotaan);
           // $output = '<div class="alert alert-info">sukses</div>';
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





 public function print_transaksi_badminton_turnamen($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.kategori_transaksi,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung, t.fasilitas, t.paket, t.dp,
            t.sisa_pembayaran, idf.id_transaksi, t.pembayaran, t.id_metode_pembayaran,
            mu.nama as kasir,
            m.nama, m.qrcode, m.kode_unik_member,
            idf.nama, idf.lapangan, idf.no_hp, idf.tgl_main, idf.status, (SELECT count(jam_main) from jadwal_badminton where id_transaksi_sementara=t.id_transaksi) as jumlah_jam_main
         from transaksi t 
         left join identitas_order_badminton idf on t.id_transaksi = idf.id_transaksi_sementara
         left join master_user mu on t.id_user = mu.id_user
         left join member m on t.id_pengunjung = m.id_member
         where t.id_transaksi='$id_transaksi'")->row_array();


        $detail_transaksi = $this->db->query("SELECT  nilai, item_transaksi
          from pendapatan jf 
          where jf.id_transaksi='$id_transaksi' and item_transaksi like 'Biaya %'");

        $jam_main = $this->db->query("SELECT id_detail_booking_badminton , biaya
          from jadwal_badminton  jf 
          where jf.id_transaksi_sementara='$id_transaksi'");



        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * $print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer + 25; 
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
        $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $data['metode_pembayaran']=$kumpul_metode_pembayaran;
        $data['id_transaksi']=$id_transaksi;
        $data['jam_main']=$jam_main->result_array();
        $data['produk']=$detail_transaksi->result_array();

        $caption_status_print = ['settlement'=>'Settlement','reprint'=>'Re-Print','preview'=>'Preview'];
        $data['status_print']=$caption_status_print[$status_print];
        $data['action']=$action;
        $data['transaksi']=$transaksi;
        $data['print_setting']=$print_setting;
        $data['t']='';
        $html = $this->load->view('user/gro/transaction/print/badminton_turnamen', $data, true);
         // $html =  $this->load->view('user/kassa/member/print_order_member', $data, true);


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);

      


        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }
}
