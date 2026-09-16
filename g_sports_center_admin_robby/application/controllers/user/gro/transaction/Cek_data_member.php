<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cek_data_member extends CI_Controller {


	public function index()
	{
        $data['metode_pembayaran'] = $this->db->query("SELECT * from master_metode_pembayaran where status = '1'")->result_array();
        $data['modal']    = $this->load->view('user/gro/gym/modal',$data, true);
		$this->template->load('template/user_adminlte','user/gro/gym/member', $data);
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


    public function hapus_calon_member()
    {
        
        $this->db->trans_start();
            $q = $this->db->query("DELETE from  member where reg_via = 'Online' and status='Belum Aktif'");
          
          
    if ($this->db->trans_status() === FALSE)
    {
            $this->db->trans_rollback();
            $output = ['response'=>500, 'pesan'=>'Kesalahan sistem, Gagal menghapus data' ];
    }
    else
    {
            $output = ['response'=>200, 'pesan'=>'Data berhasil disimpan'];
            $this->db->trans_commit();
    }
    echo json_encode($output);
    }



    public function submit_aktifkan_member()
    {

        $this->db->trans_start();
      $tgls = date('Y-m-d');
     $q_cek_order_ke = $this->db->query("SELECT max(order_ke) as order_terakhir from transaksi where tgl_transaksi='$tgls'")->row_array();
     $order_ke = $q_cek_order_ke['order_terakhir'] == '' ? 0 : $q_cek_order_ke['order_terakhir'];
     $next_order = $order_ke +1;

    $metode_pembayaran = $this->input->post('metode_pembayaran');
    $total = $this->input->post('total');
    $registrasi = $this->input->post('registrasi');
    $biaya = $this->input->post('biaya');
    $id_member = $this->input->post('id_member');
    $id_jenis_member = $this->input->post('id_jenis_member');
    $input_dibayar = $this->input->post('input_dibayar');
    $input_kembalian = $this->input->post('input_kembalian');
    $akhir_masa_aktif = $this->input->post('akhir_masa_aktif');
    $paket_member = $this->input->post('paket_member');



        $kategori_diskon = $this->input->post('kategori_diskon');
        $id_diskon = $this->input->post('id_diskon');
        $nama_diskon = $this->input->post('nama_diskon');
        $jenis_potongan = $this->input->post('jenis_potongan');
        $besar_potongan = $this->input->post('besar_potongan');
        $rp_nilai_potongan = $this->input->post('rp_nilai_potongan');
        $simpan_tagihan = $this->input->post('simpan_tagihan');
        $simpan_kembalian = $this->input->post('simpan_kembalian');

        $potongan_harga = $this->input->post('potongan_harga');
        $metode_pembayaran = $this->input->post('metode_pembayaran');
        $fasilitas = $this->input->post('fasilitas');



    $kategori_member = 'baru';

    $id_user = id_user();
    $q_member = $this->db->query("SELECT id_member, nama, no_hp from member where id_member='$id_member'")->row_array();


    $simpan_tgl_register = date('Y-m-d');
    $simpan_jam_register = date('Y-m-d');

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
            $next_no_transaksi = ''.$next_order;
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


    if ($fasilitas=='Gym') {
        $id_akun_pendapatan = 4;
    }
    elseif ($fasilitas=='Gym - Special Membership') {
        # code...
        $id_akun_pendapatan = '3,4';
    }else{
        $id_akun_pendapatan = '3';

    }
    $data_transaksi = [
           'id_user' => id_user(),
            'no_transaksi' => $no_transaksi,
            'id_metode_pembayaran' => $simpan_metode_pembayaran,
            'id_akun_pendapatan' => $id_akun_pendapatan,
            'pembayaran' => $pembayaran,
            'id_pengunjung' => $id_member,
            'id_fasilitas' => $id_jenis_member,

            'nama_pengunjung' => $q_member['nama'],
            'nohp_pengunjung' => $q_member['no_hp'],
            'fasilitas' => $fasilitas,
            'kelompok_fasilitas' => $fasilitas,
            'tgl_transaksi' => tgls(),
            'jam_transaksi' => jams(),
            'order_ke' => $next_order,
            'order_ke_fasilitas' => next_order_fasilitas($fasilitas),
            'total' =>$total,
            
            // 'kategori_potongan' => $kategori_diskon,
            // 'jenis_potongan' => $jenis_potongan,
            // 'id_diskon' => $id_diskon,
            // 'nama_diskon' => $nama_diskon,
            // 'besar_diskon' => $besar_potongan,
            // 'rp_nilai_diskon' => $rp_nilai_potongan,
              
            'kategori_potongan' => $kategori_diskon,
            'jenis_potongan' => $jenis_potongan,
            'id_diskon' => $id_diskon,
            'nama_diskon' => $nama_diskon,
            'besar_diskon' => $besar_potongan,
            'rp_nilai_diskon' => $rp_nilai_potongan,
            'tagihan' => $simpan_tagihan,
            'status' => 'Settlement',
            'dibayar' => $simpan_dibayar,
            'kembalian ' => $simpan_kembalian,




    ];
    $this->db->insert('transaksi', $data_transaksi);
     $id_transaksi = $this->db->insert_id();






        $pendapatan_semua = $simpan_tagihan;
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
                    'fasilitas' => $fasilitas, 
                    'kategori' => '2 Metode Pembayaran', 
                    'dibayar' => $input_dibayar, 
                    'pendapatan' => $masuk_pendapatan_1, 
                    'id_akun_pendapatan' => $id_akun_pendapatan,

                    'item_transaksi' => 'Pembayaran Gym', 
                    'keterangan'=>$kategori_member == 'baru' ? ' Pembayaran '.$fasilitas.' baru : '.$paket_member.' A.n. '.$q_member['nama'] : ' perpanjangan '.$fasilitas.' : '.$paket_member.' A.n. '.$q_member['nama'],
            
                    'tgl_transaksi' => timestamp(), 
                    'id_user' => id_user(), 
                    'status' => 'Settlement',  
                ],
                [
                    'id_transaksi' => $id_transaksi, 
                    'id_metode_pembayaran' => $metode_pembayaran_2, 
                    'fasilitas' => $fasilitas, 
                    'kategori' => '2 Metode Pembayaran', 
                    'dibayar' => $input_dibayar_2, 
                    'pendapatan' => $masuk_pendapatan_2, 
                    'id_akun_pendapatan' => $id_akun_pendapatan,

                    'item_transaksi' => 'Pembayaran Gym', 
                    'keterangan'=>$kategori_member == 'baru' ? ' Pembayaran '.$fasilitas.' baru : '.$paket_member.' A.n. '.$q_member['nama'] : ' perpanjangan '.$fasilitas.' : '.$paket_member.' A.n. '.$q_member['nama'],
            
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
                    'fasilitas' => $fasilitas, 
                    'kategori' => '1 Metode Pembayaran', 
                    'dibayar' => $simpan_dibayar, 
                    'pendapatan' => $pendapatan_semua, 
                    'id_akun_pendapatan' => $id_akun_pendapatan,

                    'item_transaksi' => 'Pembayaran Gym', 
                    'keterangan'=>$kategori_member == 'baru' ? ' Pembayaran '.$fasilitas.' baru : '.$paket_member.' A.n. '.$q_member['nama'] : 'perpanjangan'.$fasilitas.' : '.$paket_member.' A.n. '.$nama,
                    'tgl_transaksi' => timestamp(), 
                    'id_user' => id_user(), 
                    'status' => 'Settlement',  
            ];
            $this->db->insert('pembayaran', $data_pembayaran);


        }




        $kumpul_pendapatan = [];


        if ($fasilitas=='Gym - Special Membership') {
            // $id_jenis_member =
            // $cek_harga = $this->db->query("SELECT * from master_gym_spesial where id_jenis_member='$id_jenis_member'")->row_array();
            $harga_gym =$this->input->post('harga_gym');
            $harga_swim =$this->input->post('harga_swim');
            
            $pendapatan_masuk_gym = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>$fasilitas,
                'kelompok_fasilitas'=>'Gym',
                'kategori'=>'Masuk',
                'nilai'=>$harga_gym,
                'id_akun_pendapatan'=>4,
                'item_transaksi'=>'Paket Gym '.$paket_member,
                'keterangan'=>$kategori_member == 'baru' ? ''.$fasilitas.' baru : '.$paket_member.' A.n. '.$q_member['nama'] : 'perpanjangan'.$fasilitas.' : '.$paket_member.' A.n. '.$q_member['nama'],
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'id_metode_pembayaran' => $metode_pembayaran,
                'status'=>'Settlement'
            ];
            $pendapatan_masuk_swim = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>$fasilitas,
                'kelompok_fasilitas'=>'Swimming',
                'kategori'=>'Masuk',
                'nilai'=>$harga_swim,
                'id_akun_pendapatan'=>3,

                'item_transaksi'=>'Registrasi Member',
                'keterangan'=> 'register member : '.$paket_member.' A.n. '.$q_member['nama'],
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'id_metode_pembayaran' => $metode_pembayaran,
                'status'=>'Settlement'
            ];
            array_push($kumpul_pendapatan, $pendapatan_masuk_gym);
            array_push($kumpul_pendapatan, $pendapatan_masuk_swim);



                if ($kategori_diskon !='') {
                    $bagi_diskon = $rp_nilai_potongan / 2;
                    if ($jenis_potongan =='Persentase') {
                        # code...
                    $diskon_gym = $harga_gym * ($besar_potongan / 100);
                    $diskon_swim = $harga_swim * ($besar_potongan / 100);
                    }else{
                    $diskon_gym = $bagi_diskon;
                    $diskon_swim = $bagi_diskon;

                    }

                    $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;
                      $pendapatan_keluad_diskon_gym = [
                        'id_transaksi'=>$id_transaksi,
                        'fasilitas'=>'Gym - Special Membership',
                        'kelompok_fasilitas'=>'Gym',
                        'kategori'=>'Keluar',
                        'nilai'=>$diskon_gym,
                        'id_akun_pendapatan'=>4,

                        'item_transaksi'=>'Potongan Harga Gym dari Gym Spesial',
                        'keterangan'=> $keterangan_diskon,
                        'tgl_transaksi'=>timestamp(),
                        'id_user '=>id_user(),
                        'status' => 'Settlement',
                        'id_metode_pembayaran' => $metode_pembayaran,
                    ];
                    array_push($kumpul_pendapatan, $pendapatan_keluad_diskon_gym);

                      $pendapatan_keluad_diskon_swim = [
                        'id_transaksi'=>$id_transaksi,
                        'fasilitas'=>'Gym - Special Membership',
                        'kelompok_fasilitas'=>'Swimming',
                        'kategori'=>'Keluar',
                        'nilai'=>$diskon_swim,
                        'id_akun_pendapatan'=>3,

                        'item_transaksi'=>'Potongan Harga Swimming dari Gym Spesial',
                        'keterangan'=> $keterangan_diskon,
                        'tgl_transaksi'=>timestamp(),
                        'id_user '=>id_user(),
                        'status' => 'Settlement',
                        'id_metode_pembayaran' => $metode_pembayaran,
                    ];
                    array_push($kumpul_pendapatan, $pendapatan_keluad_diskon_swim);
                }



        }else if($fasilitas=='Gym'){

            $pendapatan_masuk_paket = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Gym',
                'kelompok_fasilitas'=>'Gym',
                'kategori'=>'Masuk',
                'nilai'=>$biaya,
                'id_akun_pendapatan'=>4,
                'item_transaksi'=>'Paket Gym '.$paket_member,
                'keterangan'=>$kategori_member == 'baru' ? ''.$fasilitas.' baru : '.$paket_member.' A.n. '.$q_member['nama'] : 'perpanjangan'.$fasilitas.' : '.$paket_member.' A.n. '.$q_member['nama'],
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'id_metode_pembayaran' => $metode_pembayaran,
                'status'=>'Settlement'
            ];
            $pendapatan_register_paket = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>'Gym',
                'kelompok_fasilitas'=>'Gym',
                'kategori'=>'Masuk',
                'nilai'=>$registrasi,
                'id_akun_pendapatan'=>4,

                'item_transaksi'=>'Registrasi Member',
                'keterangan'=> 'register member : '.$paket_member.' A.n. '.$q_member['nama'],
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'id_metode_pembayaran' => $metode_pembayaran,
                'status'=>'Settlement'
            ];
            array_push($kumpul_pendapatan, $pendapatan_masuk_paket);
            if ($registrasi>0) {
            array_push($kumpul_pendapatan, $pendapatan_register_paket);
            }


            if ($kategori_diskon !='') {
                $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;
                  $pendapatan_keluad_diskon = [
                    'id_transaksi'=>$id_transaksi,
                    'fasilitas'=>'Gym',
                    'kelompok_fasilitas'=>'Gym',
                    'kategori'=>'Keluar',
                    'nilai'=>$rp_nilai_potongan,
                    'id_akun_pendapatan'=>4,
                    'item_transaksi'=>'',
                    'keterangan'=> $keterangan_diskon,
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                'status' => 'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                ];
                array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
            }



        }else{
            
            $pendapatan_masuk_paket = [
                'id_transaksi'=>$id_transaksi,
                'fasilitas'=>$fasilitas,
                'kelompok_fasilitas'=>'Swimming',
                'kategori'=>'Masuk',
                'nilai'=>$biaya,
                'id_akun_pendapatan'=>3,
                'item_transaksi'=>'Paket '.$fasilitas.' | '.$paket_member,
                'keterangan'=>$kategori_member == 'baru' ? ''.$fasilitas.' baru : '.$paket_member.' A.n. '.$q_member['nama'] : 'perpanjangan'.$fasilitas.' : '.$paket_member.' A.n. '.$q_member['nama'],
                'tgl_transaksi'=>timestamp(),
                'id_user '=>id_user(),
                'id_metode_pembayaran' => $metode_pembayaran,
                'status'=>'Settlement'
            ];
            array_push($kumpul_pendapatan, $pendapatan_masuk_paket);

            if ($kategori_diskon !='') {
                $keterangan_diskon = $kategori_diskon =='Student Card' ? 'Diskon '.$nama_diskon : 'Diskon '.$kategori_diskon.' : '.$nama_diskon ;
                  $pendapatan_keluad_diskon = [
                    'id_transaksi'=>$id_transaksi,
                    'fasilitas'=>$fasilitas,
                    'kelompok_fasilitas'=>'Swimming',
                    'kategori'=>'Keluar',
                    'nilai'=>$rp_nilai_potongan,
                    'id_akun_pendapatan'=>3,
                    'item_transaksi'=>'',
                    'keterangan'=> $keterangan_diskon,
                    'tgl_transaksi'=>timestamp(),
                    'id_user '=>id_user(),
                'status' => 'Settlement',
                'id_metode_pembayaran' => $metode_pembayaran,
                ];
                array_push($kumpul_pendapatan, $pendapatan_keluad_diskon);
            }



        }








        $this->db->insert_batch('pendapatan', $kumpul_pendapatan);




        if ($fasilitas=='Gym - Special Membership') {
            $data_keanggotaan_gym = [
                'id_member'=>$id_member,
                'id_jenis_member'=>$id_jenis_member,
                'jenis_member'=>$paket_member,
                'include_register'=>'Ya',
                'id_transaksi_member'=>$id_transaksi,
                'id_metode_pembayaran'=>$metode_pembayaran,


                'tgl_daftar'=>$simpan_tgl_register,
                'jam_daftar'=>$simpan_jam_register,
                'awal_masa_aktif'=>date('Y-m-d'),
                'akhir_masa_aktif'=>$akhir_masa_aktif,
                'fasilitas'=>$fasilitas,
                'kelompok_fasilitas'=>'Gym',
             ];
            $data_keanggotaan_swim = [
                'id_member'=>$id_member,
                'id_jenis_member'=>$id_jenis_member,
                'jenis_member'=>$paket_member,
                'include_register'=>'Ya',
                'id_transaksi_member'=>$id_transaksi,
                'id_metode_pembayaran'=>$metode_pembayaran,


                'tgl_daftar'=>$simpan_tgl_register,
                'jam_daftar'=>$simpan_jam_register,
                'awal_masa_aktif'=>date('Y-m-d'),
                'akhir_masa_aktif'=>$akhir_masa_aktif,
                'fasilitas'=>$fasilitas,
                'kelompok_fasilitas'=>'Swimming',
             ];

             $kumpul_keanggotaan = [$data_keanggotaan_gym, $data_keanggotaan_swim];
             $this->db->insert_batch('keanggotaan_member',$kumpul_keanggotaan);
			    $this->db->update('member', ['status'=>'Aktif'], ['id_member'=>$id_member]);

         }else if($fasilitas=='Gym'){
            $data_keanggotaan = [
                'id_member'=>$id_member,
                'id_jenis_member'=>$id_jenis_member,
                'jenis_member'=>$paket_member,
                'include_register'=>'Ya',
                'id_transaksi_member'=>$id_transaksi,
                'id_metode_pembayaran'=>$metode_pembayaran,


                'tgl_daftar'=>$simpan_tgl_register,
                'jam_daftar'=>$simpan_jam_register,
                'awal_masa_aktif'=>date('Y-m-d'),
                'akhir_masa_aktif'=>$akhir_masa_aktif,
                'fasilitas'=>$fasilitas,
                'kelompok_fasilitas'=>'Gym',
             ];

		    if ($id_jenis_member==1) {

			    $this->db->delete('member',['id_member'=>$id_member]);
		    }else{
	            $this->db->insert('keanggotaan_member', $data_keanggotaan);
			    $this->db->update('member', ['status'=>'Aktif'], ['id_member'=>$id_member]);

		    }
        }else{
            $data_keanggotaan = [
                'id_member'=>$id_member,
                'id_jenis_member'=>$id_jenis_member,
                'jenis_member'=>$paket_member,
                'include_register'=>'Ya',
                'id_transaksi_member'=>$id_transaksi,
                'id_metode_pembayaran'=>$metode_pembayaran,


                'tgl_daftar'=>$simpan_tgl_register,
                'jam_daftar'=>$simpan_jam_register,
                'awal_masa_aktif'=>date('Y-m-d'),
                'akhir_masa_aktif'=>$akhir_masa_aktif,
                'fasilitas'=>$fasilitas,
                'kelompok_fasilitas'=>'Swimming',
             ];
            $this->db->insert('keanggotaan_member', $data_keanggotaan);
		    $this->db->update('member', ['status'=>'Aktif'], ['id_member'=>$id_member]);
         }
         


    

      if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
                 $output = [
                    'responcode'=>500,
                    'id_transaksi'=>$id_transaksi,
                    'id_jenis_member'=>$id_jenis_member,
                    'fasilitas'=>$fasilitas,
                    // 'message'=>'Synchronize Gagal. Ditemukan kesalahan pada aplikasi',
                ];
        }else{
            $this->db->trans_commit();
                 $output = [
                    'responcode'=>200,
                    'id_transaksi'=>$id_transaksi,        
                    'id_jenis_member'=>$id_jenis_member,
                    'fasilitas'=>$fasilitas,
                    // 'message'=>'Keanggotaan member diper',
                ];

        }

        echo json_encode($output);
        
    }



    public function detail_member_gym()
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
                $usia =  $q['id_jenis_member'] =='1' ? '' : date('Y') - $pecah_tgll[0];

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


            $keanggotaan_member = $this->db->query("SELECT id_jenis_member,awal_masa_aktif,akhir_masa_aktif, fasilitas, kelompok_fasilitas, jenis_member from keanggotaan_member where id_member='$id_member' order by id_keanggotaan_member desc")->result_array();

            if ($q['status']=='Aktif') {
                $output =[
                    'member'=>$q,
                    'paket'=>'',//$kumpul_jenis_member[$id_jenis_member]['paket'],
                    'biaya'=>'',//$kumpul_jenis_member[$id_jenis_member]['biaya'],
                    'masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                    'tgl_akhir_masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                    'tgl_awal_masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                    'id_jm'=>$id_jenis_member,
                    'usia'=>$usia,
                    'keanggotaan_member'=>$keanggotaan_member,
                ];
            }else{
                $output =[
                    'member'=>$q,
                    'paket'=>$kumpul_jenis_member[$id_jenis_member]['paket'],
                    'biaya'=>$kumpul_jenis_member[$id_jenis_member]['biaya'],
                    'masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                    'tgl_akhir_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                    'tgl_awal_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                    'id_jm'=>$id_jenis_member,
                    'usia'=>$usia,
                    'keanggotaan_member'=>'',
                ];
            }
            echo json_encode($output);
    }


    public function detail_member_gym_spesial()
    {
        
            $id_member = $this->input->post('id_member');

            $tgl_sekarang = date('Y-m-d');
            $q = $this->db->query("SELECT *
         from member where id_member='$id_member'")->row_array();
            $id_jenis_member = $q['id_jenis_member'];
            $q_jenis_member = $this->db->query("SELECT id_jenis_member, jenis_member, harga_gym, harga_swim, masa_aktif, satuan_masa_aktif from master_gym_spesial ")->result_array();
            $kumpul_jenis_member = [];
            foreach ($q_jenis_member as $k => $v) {

                $masa_aktif = $v['masa_aktif'];
                $satuan_masa_aktif = $v['satuan_masa_aktif'];
                $pecah_tgll = explode('-', $q['tgll']);
                // $tahun_tgll = $pecah_tgll[0];
                $usia =  $q['id_jenis_member'] =='1' ? '' : date('Y') - $pecah_tgll[0];

                if ($satuan_masa_aktif=='Hari') {
                    $tgl_masa_aktif    =date('Y-m-d', strtotime("+" .($masa_aktif-1). " day", strtotime($tgl_sekarang)));
                }else{
                    $tgl_masa_aktif    =date('Y-m-d', strtotime("+" .$masa_aktif. " month", strtotime($tgl_sekarang)));
                }




                 $kumpul_jenis_member[$v['id_jenis_member']] = [
                    'paket'=>$v['jenis_member'],
                    'biaya'=>($v['harga_gym'] + $v['harga_swim']),
                    'harga_swim'=>$v['harga_swim'],
                    'harga_gym'=>$v['harga_gym'],
                    'masa_aktif'=>$masa_aktif.' '.$satuan_masa_aktif,
                    'tgl_akhir_masa_aktif'=>$tgl_masa_aktif,
                    'tgl_awal_masa_aktif'=>$tgl_sekarang,


                ];
            }


            $keanggotaan_member = $this->db->query("SELECT id_jenis_member,awal_masa_aktif,akhir_masa_aktif, fasilitas, kelompok_fasilitas, jenis_member from keanggotaan_member where id_member='$id_member' order by id_keanggotaan_member desc")->result_array();

            if ($q['status']=='Aktif') {
                $output =[
                    'member'=>$q,
                    'paket'=>'',//$kumpul_jenis_member[$id_jenis_member]['paket'],
                    'biaya'=>'',//$kumpul_jenis_member[$id_jenis_member]['biaya'],
                    'masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                    'tgl_akhir_masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                    'tgl_awal_masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                    'id_jm'=>$id_jenis_member,
                    'usia'=>$usia,
                    'keanggotaan_member'=>$keanggotaan_member,
                ];
            }else{
                $output =[
                    'member'=>$q,
                    'paket'=>$kumpul_jenis_member[$id_jenis_member]['paket'],
                    'biaya'=>$kumpul_jenis_member[$id_jenis_member]['biaya'],
                    'harga_swim'=>$kumpul_jenis_member[$id_jenis_member]['harga_swim'],
                    'harga_gym'=>$kumpul_jenis_member[$id_jenis_member]['harga_gym'],
                    'masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                    'tgl_akhir_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                    'tgl_awal_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                    'id_jm'=>$id_jenis_member,
                    'usia'=>$usia,
                    'keanggotaan_member'=>'',
                ];
            }
            echo json_encode($output);
    }




    public function detail_member_swimming_bulanan()
    {
        
            $id_member = $this->input->post('id_member');

            $tgl_sekarang = date('Y-m-d');
            $q = $this->db->query("SELECT *
         from member where id_member='$id_member'")->row_array();
            $id_jenis_member = $q['id_jenis_member'];
            $q_jenis_member = $this->db->query("SELECT id_swimming, jenis_member, biaya, masa_aktif, satuan_masa_aktif from master_swimming_bulanan ")->result_array();
            $kumpul_jenis_member = [];
            foreach ($q_jenis_member as $k => $v) {

                $masa_aktif = $v['masa_aktif'];
                $satuan_masa_aktif = $v['satuan_masa_aktif'];
                $pecah_tgll = explode('-', $q['tgll']);
                // $tahun_tgll = $pecah_tgll[0];
                $usia =  $q['id_jenis_member'] =='1' ? '' : date('Y') - $pecah_tgll[0];

                if ($satuan_masa_aktif=='Hari') {
                    $tgl_masa_aktif    =date('Y-m-d', strtotime("+" .($masa_aktif-1). " day", strtotime($tgl_sekarang)));
                }else{
                    $tgl_masa_aktif    =date('Y-m-d', strtotime("+" .$masa_aktif. " month", strtotime($tgl_sekarang)));
                }




                 $kumpul_jenis_member[$v['id_swimming']] = [
                    'paket'=>$v['jenis_member'],
                    'biaya'=>($v['biaya']),
                    'masa_aktif'=>$masa_aktif.' '.$satuan_masa_aktif,
                    'tgl_akhir_masa_aktif'=>$tgl_masa_aktif,
                    'tgl_awal_masa_aktif'=>$tgl_sekarang,


                ];
            }


            $keanggotaan_member = $this->db->query("SELECT id_jenis_member,awal_masa_aktif,akhir_masa_aktif, fasilitas, kelompok_fasilitas, jenis_member from keanggotaan_member where id_member='$id_member' order by id_keanggotaan_member desc")->result_array();

            if ($q['status']=='Aktif') {
                $output =[
                    'member'=>$q,
                    'paket'=>'',//$kumpul_jenis_member[$id_jenis_member]['paket'],
                    'biaya'=>'',//$kumpul_jenis_member[$id_jenis_member]['biaya'],
                    'masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                    'tgl_akhir_masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                    'tgl_awal_masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                    'id_jm'=>$id_jenis_member,
                    'usia'=>$usia,
                    'keanggotaan_member'=>$keanggotaan_member,
                ];
            }else{
                $output =[
                    'member'=>$q,
                    'paket'=>$kumpul_jenis_member[$id_jenis_member]['paket'],
                    'biaya'=>$kumpul_jenis_member[$id_jenis_member]['biaya'],
                    'masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                    'tgl_akhir_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                    'tgl_awal_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                    'id_jm'=>$id_jenis_member,
                    'usia'=>$usia,
                    'keanggotaan_member'=>'',
                ];
            }
            echo json_encode($output);
    }



    public function detail_member_swimming_spesial()
    {
        
            $id_member = $this->input->post('id_member');

            $tgl_sekarang = date('Y-m-d');
            $q = $this->db->query("SELECT *
         from member where id_member='$id_member'")->row_array();
            $id_jenis_member = $q['id_jenis_member'];
            $q_jenis_member = $this->db->query("SELECT id_swimming, jenis_member, biaya, masa_aktif, satuan_masa_aktif from master_swimming_spesial ")->result_array();
            $kumpul_jenis_member = [];
            foreach ($q_jenis_member as $k => $v) {

                $masa_aktif = $v['masa_aktif'];
                $satuan_masa_aktif = $v['satuan_masa_aktif'];
                $pecah_tgll = explode('-', $q['tgll']);
                // $tahun_tgll = $pecah_tgll[0];
                $usia =  $q['id_jenis_member'] =='1' ? '' : date('Y') - $pecah_tgll[0];

                if ($satuan_masa_aktif=='Hari') {
                    $tgl_masa_aktif    =date('Y-m-d', strtotime("+" .($masa_aktif-1). " day", strtotime($tgl_sekarang)));
                }else{
                    $tgl_masa_aktif    =date('Y-m-d', strtotime("+" .$masa_aktif. " month", strtotime($tgl_sekarang)));
                }




                 $kumpul_jenis_member[$v['id_swimming']] = [
                    'paket'=>$v['jenis_member'],
                    'biaya'=>($v['biaya']),
                    'masa_aktif'=>$masa_aktif.' '.$satuan_masa_aktif,
                    'tgl_akhir_masa_aktif'=>$tgl_masa_aktif,
                    'tgl_awal_masa_aktif'=>$tgl_sekarang,


                ];
            }


            $keanggotaan_member = $this->db->query("SELECT id_jenis_member,awal_masa_aktif,akhir_masa_aktif, fasilitas, kelompok_fasilitas, jenis_member from keanggotaan_member where id_member='$id_member' order by id_keanggotaan_member desc")->result_array();

            if ($q['status']=='Aktif') {
                $output =[
                    'member'=>$q,
                    'paket'=>'',//$kumpul_jenis_member[$id_jenis_member]['paket'],
                    'biaya'=>'',//$kumpul_jenis_member[$id_jenis_member]['biaya'],
                    'masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                    'tgl_akhir_masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                    'tgl_awal_masa_aktif'=>'',//$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                    'id_jm'=>$id_jenis_member,
                    'usia'=>$usia,
                    'keanggotaan_member'=>$keanggotaan_member,
                ];
            }else{
                $output =[
                    'member'=>$q,
                    'paket'=>$kumpul_jenis_member[$id_jenis_member]['paket'],
                    'biaya'=>$kumpul_jenis_member[$id_jenis_member]['biaya'],
                    'masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['masa_aktif'],
                    'tgl_akhir_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_akhir_masa_aktif'],
                    'tgl_awal_masa_aktif'=>$kumpul_jenis_member[$id_jenis_member]['tgl_awal_masa_aktif'],
                    'id_jm'=>$id_jenis_member,
                    'usia'=>$usia,
                    'keanggotaan_member'=>'',
                ];
            }
            echo json_encode($output);
    }



 public function print_transaksi($id_transaksi)
    {
           $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();


               $transaksi = $this->db->query("SELECT 
          t.no_transaksi, t.dibayar, t.kembalian, t.tgl_transaksi, t.jam_transaksi, t.order_ke, t.total, t.kategori_potongan, t.id_fasilitas,
            t.jenis_potongan, t.id_diskon, t.nama_diskon, t.jenis_potongan, t.rp_nilai_diskon, t.besar_diskon, t.nama_pengunjung,
            mu.nama as kasir,
            mp.metode_pembayaran,
            m.nama, m.qrcode, m.kode_unik_member
         from transaksi t 
         left join master_metode_pembayaran mp on t.id_metode_pembayaran = mp.id_metode_pembayaran
         left join master_user mu on t.id_user = mu.id_user
         left join member m on t.id_pengunjung = m.id_member
         where t.id_transaksi='$id_transaksi'")->row_array();


        $detail_transaksi = $this->db->query("SELECT  nilai,
item_transaksi 
          from pendapatan pd 
          where pd.id_transaksi='$id_transaksi' and kategori='Masuk'");



        $status_print = "settlement";//$this->input->get('status_print');
        $action = "";//$this->input->get('action');
            $header = $print_setting['mpdf_format_paper_height_header'];
        $konten = $detail_transaksi->num_rows() * $print_setting['mpdf_format_paper_height_content'] ;
        $footer = $print_setting['mpdf_format_paper_height_footer'];

        $height = $header + $konten + $footer ; 
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
        if ($transaksi['fasilitas']=='Gym') {
            $html = $this->load->view('user/gro/transaction/print/gym', $data, true);
        }
        elseif ($transaksi['fasilitas']=='Swimming - Membership') {
            $html = $this->load->view('user/gro/transaction/print/swimming_membership', $data, true);
        }
        elseif ($transaksi['fasilitas']=='Swimming - Spesial Membership') {
            $html = $this->load->view('user/gro/transaction/print/swimming_spesial', $data, true);
        }else{
            $html = $this->load->view('user/gro/transaction/print/gym_spesial', $data, true);

        }


        // $mpdf->SetMargins(0, 0, 40);
        // $mpdf->SetHTMLHeader($header);
         $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);

      


        // $mpdf->AutoPrint(true);
        $mpdf->Output('Print.pdf', 'I');



    }


    public function dt_member_reg_online()
    {
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
            $q = $this->db->query("SELECT kode_unik_member, fasilitas, nama, tmpl, tgll, jk, alamat, no_hp, email, tgl_register, jam_register, id_member from member where reg_via='Online' and status='Belum Aktif' and (nama like'%$key%') limit $start, $length")->result_array();
         }else{
            $q = $this->db->query("SELECT kode_unik_member, fasilitas,nama, tmpl, tgll, jk, alamat, no_hp, email, tgl_register, jam_register, id_member from member  where reg_via='Online' and status='Belum Aktif'  $order_by limit $start, $length ")->result_array();
         }
         $all_data = $this->db->query("SELECT  id_member   from member where reg_via='Online' and status='Belum Aktif'")->num_rows();

        foreach ($q as $k => $v) {
            $no++;


            $row    = [];
            $row[]  = $no;
            $row[]  = $v['kode_unik_member'];
            $row[]  = $v['nama'];
           
            $row[]  = $v['tgll']=='' ? '' : $v['tmpl'].' / <a href="javascript:void(0)" onclick="edit_tgll('."'".$v['id_member']."','".$v['tgll']."'".')">'.show_tanggal_tgll($v['tgll']).'</a>';
            $row[]  = $v['jk'];
            $row[]  = $v['alamat'];
            $row[]  = $v['no_hp'];
            $row[]  = $v['fasilitas'];
            $row[]  = $v['tgl_register'].' '.$v['jam_register'];
            // $row[]  = '<a href="'.$params.'/'.$parameter_tamu.'" target="_blank">'.$params.'/'.$parameter_tamu.'</a>';

            $tombol_gym = '<a href="javascript:void(0)" onclick="preview_member_gym('."'".$v['id_member']."'".'); " class="btn btn-outline-info btn-xs"><i class="fa fa-info"></i></a>';
            $tombol_gym_spesial = '<a href="javascript:void(0)" onclick="preview_member_gym_spesial('."'".$v['id_member']."'".'); " class="btn btn-outline-danger btn-xs"><i class="fa fa-info"></i></a>';
            $tombol_swimming_bulanan = '<a href="javascript:void(0)" onclick="preview_member_swimming_bulanan('."'".$v['id_member']."'".'); " class="btn btn-outline-danger btn-xs"><i class="fa fa-info"></i></a>';
            $tombol_swimming_spesial = '<a href="javascript:void(0)" onclick="preview_member_swimming_spesial('."'".$v['id_member']."'".'); " class="btn btn-outline-danger btn-xs"><i class="fa fa-info"></i></a>';

            if ($v['fasilitas']=='Gym') {
            $row[]  = $tombol_gym;
              # code...
            }
            elseif ($v['fasilitas']=='Gym - Special Membership') {
            $row[]  = $tombol_gym_spesial;
              # code...
            }
            elseif ($v['fasilitas']=='Swimming - Membership') {
            $row[]  = $tombol_swimming_bulanan;
              # code...
            }else{
            $row[]  = $tombol_swimming_spesial;

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

    public function simpanedit_tgll(){
        $id_member = $this->input->post('id_member');      
        $tgll = $this->input->post('tgll');      
        $blll = $this->input->post('blll');      
        $thll = $this->input->post('thll');      
        $simpan_tgll =  $thll.'-'.$blll.'-'.$tgll;
        $where = ['id_member'=>$id_member];
        $data = ['tgll'=>$simpan_tgll];
        $this->db->update('member',$data,$where);    
        $this->session->set_flashdata('pesan','<div class="alert alert-info">Tanggal lahir member di telah diubah. silahkan selesaikan transaksi</div>');
        redirect('user/gro/new_transaction');
    }

}
