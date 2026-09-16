<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendapatan extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
       
        $id_hak_akses = $this->session->userdata('id_hak_akses'); 
       
          if (!in_array(5,$this->session->userdata('id_hak_akses'))) {
                redirect('auth/login/kick');
            }
    }


	public function index()
	{
        date_default_timezone_set("Asia/Bangkok");
        $filter = $this->input->get('filter');
        $id_kasir = $this->input->get('id_kasir');
            $akun_pendapatan = $this->db->query("SELECT id_akun_pendapatan, nama_akun from akun_pendapatan");

            $q_kasir = $this->db->query("SELECT hau.id_user, mu.nama from hak_akses_user hau
                left join master_user mu on hau.id_user = mu.id_user
             where hau.id_hak_akses='3'")->result_array();

        if ($filter=='bulanan') {
            $bulan = $this->input->get('bulan');
            $tahun = $this->input->get('tahun');
            $data['judul'] ='Pendapatan <br>Bulan : '.bulan_global($bulan).' '.$tahun;
            $data['bulan'] =$bulan;
            $data['tahun'] =$tahun;
            $data['link_print'] = base_url().'user/office/pendapatan/print?filter=bulanan&bulan='.$bulan.'&tahun='.$tahun.'&id_kasir='.$id_kasir;
            $data['redirect'] = base_url().'user/office/pendapatan/?filter=bulanan&bulan='.$bulan.'&tahun='.$tahun.'&id_kasir='.$id_kasir;
        }
        elseif ($filter=='tahunan') {
            $tahun = $this->input->get('tahun');
            $data['judul'] ='Pendapatan <br>Tahun : '.$tahun;
            $data['tahun'] =$tahun;
            $data['link_print'] = base_url().'user/office/pendapatan/print?filter=tahunan&tahun='.$tahun.'&id_kasir='.$id_kasir;
            $data['redirect'] = base_url().'user/office/pendapatan/?filter=tahunan&tahun='.$tahun.'&id_kasir='.$id_kasir;

        }
        elseif ($filter=='periode') {
            $tgl_awal = $this->input->get('tgl_awal');
            $tgl_akhir = $this->input->get('tgl_akhir');
            $data['judul'] ='Pendapatan <br>Tanggal : '.$tgl_awal.' sampai '.$tgl_akhir;
            $data['tgl_awal'] =$tgl_awal;
            $data['tgl_akhir'] =$tgl_akhir;
            $data['link_print'] = base_url().'user/office/pendapatan/print?filter=periode&tgl_awal='.$tgl_awal.'&tgl_akhir='.$tgl_akhir.'&id_kasir='.$id_kasir;
            $data['redirect'] = base_url().'user/office/pendapatan/print?filter=periode&tgl_awal='.$tgl_awal.'&tgl_akhir='.$tgl_akhir.'&id_kasir='.$id_kasir;

        }else{
            $tgl = $this->input->get('tgl');

            $data['judul'] ='Pendapatan <br>Tanggal : '.show_tanggal($tgl);
            $data['tgl'] =$tgl;
            $data['link_print'] = base_url().'user/office/pendapatan/print?filter=harian&tgl='.$tgl.'&id_kasir='.$id_kasir;
            $data['redirect'] = base_url().'user/office/pendapatan/?filter=harian&tgl='.$tgl.'&id_kasir='.$id_kasir;

        }
        $data['kasir'] =$q_kasir;
        $data['id_kasir'] =$id_kasir;
        $data['filter'] =$filter;
        $data['akun_pendapatan'] =$akun_pendapatan->result_array();
        $data['modal']    = $this->load->view('user/office/pendapatan/modal', $data, true);
		$this->template->load('template/user_adminlte','user/office/pendapatan/index', $data);
	}

    public function print()
    {
        date_default_timezone_set("Asia/Bangkok");
        $filter = $this->input->get('filter');
            $akun_pendapatan = $this->db->query("SELECT id_akun_pendapatan, nama_akun from akun_pendapatan");
            

        if ($filter=='bulanan') {
            $bulan = $this->input->get('bulan');
            $tahun = $this->input->get('tahun');
            $data['judul'] ='Laporan Pendapatan <br>Bulan : '.bulan_global($bulan).' '.$tahun;
            $data['bulan'] =$bulan;
            $data['tahun'] =$tahun;
        }
        elseif ($filter=='tahunan') {
            $tahun = $this->input->get('tahun');
            $data['judul'] ='Laporan Pendapatan <br>Tahun : '.$tahun;
            $data['tahun'] =$tahun;
        }
        elseif ($filter=='periode') {
            $tgl_awal = $this->input->get('tgl_awal');
            $tgl_akhir = $this->input->get('tgl_akhir');
            $data['judul'] ='Laporan Pendapatan <br>Tanggal : '.$tgl_awal.' sampai '.$tgl_akhir;
            $data['tgl_awal'] =$tgl_awal;
            $data['tgl_akhir'] =$tgl_akhir;
        }else{
            $tgl = $this->input->get('tgl');

            $data['judul'] ='Laporan Pendapatan <br>Tanggal : '.show_tanggal($tgl);
            $data['tgl'] =$tgl;
        }



        $data['id_kasir'] =$this->input->get('id_kasir');
        $data['akun_pendapatan'] =$akun_pendapatan->result_array();
        $data['filter'] =$filter;
 $print_setting = $this->db->query("SELECT 
             mpdf_margin_left, mpdf_margin_right, mpdf_margin_top, mpdf_margin_bottom, mpdf_format_paper_width, mpdf_format_paper_height_header, mpdf_format_paper_height_content, mpdf_format_paper_height_footer, preview_font_size_content, preview_font_size_header, preview_font_size_footer, preview_font_family 
        from print_setting 
        where id=2
        ")->row_array();

          $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            "margin_left" => 5,
            "margin_right" => 5,
            "margin_top" => 10,
            "margin_bottom" => 15,
            'orientation' => 'L',
            'tempDir' => '/tmp'
        ]);
  $html = $this->load->view('user/office/pendapatan/print', $data, true);
   // $mpdf->SetJS('this.print();');

        $mpdf->WriteHTML($html);


        $mpdf->Output('Print.pdf', 'I');
    }


    public function detail_pendapatan(){
        $id_akun = $this->input->post('id_akun');
        $tgl = $this->input->post('tgl');
        $q = $this->db->query("SELECT * from pendapatan where tgl_transaksi like '%$tgl%' and id_akun_pendapatan = '$id_akun' order by tgl_transaksi desc")->result_array();
        $output = ['data'=>$q];
        echo json_encode($output);
    }
    public function hapus_transaksi(){
        $id_transaksi = $this->input->post('id_transaksi');
        $fasilitas = $this->input->post('fasilitas');

        $where_member = ['id_transaksi_member'=>$id_transaksi];
        $where = ['id_transaksi'=>$id_transaksi];

        $this->db->trans_begin();
        if ($fasilitas=='F & B') {
            
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->db->delete('penjualan_fnb', $where);
            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data F & B Dihapus</div>');
        }
        else if ($fasilitas=='Proshop') {
            
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->db->delete('penjualan_proshop', $where);
            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data Proshop Dihapus</div>');
        }
        else if ($fasilitas=='Gym' || $fasilitas=='Gym - Special Membership' || $fasilitas=='Swimming - Membership' || $fasilitas=='Swimming - Club' || $fasilitas=='Swimming - Spesial Membership' || $fasilitas=='Private - Gym' || $fasilitas=='Private - Muaythai' || $fasilitas=='Private - Swimming') {
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->db->delete('keanggotaan_member', $where_member);

            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data '.$fasilitas.' Dihapus</div>');
        }
        else if (strpos($fasilitas, 'Les - ')!== false) {
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->db->delete('keanggotaan_member', $where_member);
            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data '.$fasilitas.' Dihapus</div>');
        }
        else if ($fasilitas=='Student Card') {
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->db->delete('history_student_card', $where);
            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data Student Card Dihapus</div>');
        }
        else if ($fasilitas=='Futsal - Harian' || $fasilitas=='Futsal - Member Bulanan' || $fasilitas=='Futsal - Turnamen') {
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->db->delete('jadwal_futsal', $where);
            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data '.$fasilitas.' Dihapus</div>');
        }

        else if ($fasilitas=='Badminton - Harian' || $fasilitas=='Badminton - Member Bulanan' || $fasilitas=='Badminton - Turnamen') {
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->db->delete('jadwal_badminton', $where);
            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data '.$fasilitas.' Dihapus</div>');
        }
        else if ($fasilitas=='Pickle - Harian' || $fasilitas=='Pickle - Member Bulanan' || $fasilitas=='Pickle - Turnamen') {
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->db->delete('jadwal_badminton', $where);
            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data '.$fasilitas.' Dihapus</div>');
        }
        else if ($fasilitas=='Swimming - Harian' || $fasilitas=='Swimming - Pelajar') {
            $this->db->delete('transaksi', $where);
            $this->db->delete('pendapatan', $where);
            $this->db->delete('pembayaran', $where);
            $this->session->set_flashdata('pesan','<div class="alert alert-info">Data '.$fasilitas.' Dihapus</div>');
        }



        if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                $output = ['response'=>500, 'pesan'=>'Transaksi gagal dihapus'];
        }
        else
        {
                $output = ['response'=>200, 'pesan'=>'Transaksi berhasil dihapus'];
                $this->db->trans_commit();
        }

        echo json_encode($output);
    }

    public function simpanedit_metode_pembayaran(){
        $id_transaksi = $this->input->post('id_transaksi');
        $pembayaran = $this->input->post('pembayaran');

        $where = ['id_transaksi'=>$id_transaksi];
        $q_transaksi = $this->db->get_where('transaksi', $where)->row_array();
        if ($pembayaran=='2 Metode Pembayaran') {
            $metode_pembayaran_1 = $this->input->post('metode_pembayaran_1');
            $metode_pembayaran_2 = $this->input->post('metode_pembayaran_2');


            // $this->db->update('pendapatan', ['id_metode_pembayaran'=>$simpan_id_metode_pembayaran], $where);

            // $id_pembayaran_1_sebelumnya = $this->input->post('id_pembayaran_1_sebelumnya');
            // $where_1 = ['id_pembayaran'=>$id_pembayaran_1_sebelumnya, 'id_transaksi'=>$id_transaksi];
            // $x1 = $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where_1);


            // $id_pembayaran_2_sebelumnya = $this->input->post('id_pembayaran_2_sebelumnya');
            // $where_2 = ['id_pembayaran'=>$id_pembayaran_2_sebelumnya, 'id_transaksi'=>$id_transaksi];
            // $x2 = $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_2], $where_2);
            
           



            if ($q_transaksi['fasilitas']=='Futsal - Harian' || $q_transaksi['fasilitas']=='Badminton - Harian' || $q_transaksi['fasilitas']=='Pickle - Harian' || $q_transaksi['fasilitas']=='Futsal - Turnamen' || $q_transaksi['fasilitas']=='Badminton - Turnamen' || $q_transaksi['fasilitas']=='Pickle - Turnamen') {
                if ($q_transaksi['dp']=='') {
                    // $q_transaksi_sebelumnya = $this->db->get_where('transaksi', ['id_transaksi_pelunasan'=>$id_transaksi])->row_array();
                    // $id_transaksi_dp =  $q_transaksi_sebelumnya['id_transaksi'];

                    // $this->db->update('transaksi', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);
                    // $this->db->update('transaksi', ['id_metode_pembayaran'=>$metode_pembayaran_1], ['id_transaksi_pelunasan'=>$id_transaksi]);
                    // // $this->db->update('pendapatan', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);
                    // $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_1], ['id_transaksi_pelunasan'=>$id_transaksi]);

                    $where_pelunasan = ['id_transaksi_pelunasan'=>$id_transaksi];
                    $simpan_id_metode_pembayaran = $metode_pembayaran_1.','.$metode_pembayaran_2;
                    $this->db->update('transaksi', ['id_metode_pembayaran'=>$simpan_id_metode_pembayaran], $where);
                    $this->db->update('transaksi', ['id_metode_pembayaran'=>$simpan_id_metode_pembayaran], $where_pelunasan);

                    $id_pembayaran_1_sebelumnya = $this->input->post('id_pembayaran_1_sebelumnya');
                    $where_1 = ['id_pembayaran'=>$id_pembayaran_1_sebelumnya, 'id_transaksi_pelunasan'=>$id_transaksi];
                    $x1 = $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where_1);

                    $id_pembayaran_2_sebelumnya = $this->input->post('id_pembayaran_2_sebelumnya');
                    $where_2 = ['id_pembayaran'=>$id_pembayaran_2_sebelumnya, 'id_transaksi_pelunasan'=>$id_transaksi];
                    $x2 = $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_2], $where_2);






                }else{

                    $simpan_id_metode_pembayaran = $metode_pembayaran_1.','.$metode_pembayaran_2;
                    $this->db->update('transaksi', ['id_metode_pembayaran'=>$simpan_id_metode_pembayaran], $where);

                    $id_pembayaran_1_sebelumnya = $this->input->post('id_pembayaran_1_sebelumnya');
                    $where_1 = ['id_pembayaran'=>$id_pembayaran_1_sebelumnya, 'id_transaksi'=>$id_transaksi];
                    $x1 = $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where_1);

                    $id_pembayaran_2_sebelumnya = $this->input->post('id_pembayaran_2_sebelumnya');
                    $where_2 = ['id_pembayaran'=>$id_pembayaran_2_sebelumnya, 'id_transaksi'=>$id_transaksi];
                    $x2 = $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_2], $where_2);

                }
                
            }else{

                    $simpan_id_metode_pembayaran = $metode_pembayaran_1.','.$metode_pembayaran_2;
                    $this->db->update('transaksi', ['id_metode_pembayaran'=>$simpan_id_metode_pembayaran], $where);

                $id_pembayaran_1_sebelumnya = $this->input->post('id_pembayaran_1_sebelumnya');
                $where_1 = ['id_pembayaran'=>$id_pembayaran_1_sebelumnya, 'id_transaksi'=>$id_transaksi];
                $x1 = $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where_1);

                $id_pembayaran_2_sebelumnya = $this->input->post('id_pembayaran_2_sebelumnya');
                $where_2 = ['id_pembayaran'=>$id_pembayaran_2_sebelumnya, 'id_transaksi'=>$id_transaksi];
                $x2 = $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_2], $where_2);
            }   




        }else{
            $metode_pembayaran_1_sebelumnya = $this->input->post('metode_pembayaran_1_sebelumnya');
            $where_1 = ['id_metode_pembayaran'=>$metode_pembayaran_1_sebelumnya, 'id_transaksi'=>$id_transaksi];
            $metode_pembayaran_1 = $this->input->post('metode_pembayaran_1');

            if ($q_transaksi['fasilitas']=='Futsal - Harian' || $q_transaksi['fasilitas']=='Badminton - Harian' || $q_transaksi['fasilitas']=='Pickle - Harian' || $q_transaksi['fasilitas']=='Futsal - Turnamen' || $q_transaksi['fasilitas']=='Badminton - Turnamen' || $q_transaksi['fasilitas']=='Pickle - Turnamen') {
                if ($q_transaksi['dp']=='') {
                    $q_transaksi_sebelumnya = $this->db->get_where('transaksi', ['id_transaksi_pelunasan'=>$id_transaksi])->row_array();
                    $id_transaksi_dp =  $q_transaksi_sebelumnya['id_transaksi'];

                    $this->db->update('transaksi', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);
                    $this->db->update('transaksi', ['id_metode_pembayaran'=>$metode_pembayaran_1], ['id_transaksi_pelunasan'=>$id_transaksi]);
                    // $this->db->update('pendapatan', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);
                    $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_1], ['id_transaksi_pelunasan'=>$id_transaksi]);

                }else{
                    $this->db->update('transaksi', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);
                    // $this->db->update('pendapatan', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);
                    $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);

                }
                
            }else{
                $this->db->update('transaksi', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);
                // $this->db->update('pendapatan', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);
                $this->db->update('pembayaran', ['id_metode_pembayaran'=>$metode_pembayaran_1], $where);

            }          
        }



        $this->session->set_flashdata('pesan', '<div class="alert alert-info">Data metode pembayaran berhasil diperbaharui</div>');


    }
    public function detail_transaksi(){

        $id_transaksi = $this->input->post('id_transaksi');

        $where = ['id_transaksi'=>$id_transaksi];
        $q_transaksi = $this->db->select('no_transaksi, tgl_transaksi, jam_transaksi,  fasilitas, id_metode_pembayaran, pembayaran',)->get_where('transaksi',$where)->row_array();
        $q_pendapatan = $this->db->select('id_transaksi, kategori, nilai',)->get_where('pendapatan',$where)->result_array();
        $j_pembayaran = $this->db->select('id_pembayaran',)->get_where('pembayaran',$where)->num_rows();
        if ($j_pembayaran==0) {
            $q_pembayaran = $this->db->select('id_pembayaran',)->get_where('pembayaran',['id_transaksi_pelunasan'=>$id_transaksi])->result_array();
        }else{
            $q_pembayaran = $this->db->select('id_pembayaran',)->get_where('pembayaran',$where)->result_array();

        }
        $pendapatan = 0;
        $pengeluaran = 0;
        foreach ($q_pendapatan as $k => $v) {
            if ($v['kategori']=='Masuk') {
                $pendapatan +=$v['nilai'];
            }else{
                $pengeluaran +=$v['nilai'];

            }
        }
        $kumpul_metode_pembayaran = [];
        foreach (metode_pembayaran() as $k => $v) {
            $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
        }

        $pecah_metode = explode(',', $q_transaksi['id_metode_pembayaran']);
        $show_mp = [];
        foreach ($pecah_metode as $k => $v) {
            $show_mp[$k] = $kumpul_metode_pembayaran[$v];
        }



            $kumpul_pembayaran = [];

        foreach ($q_pembayaran as $k => $v) {
            array_push($kumpul_pembayaran, $v['id_pembayaran']);
        }


        $nilai_pendapatan = $pendapatan - $pengeluaran;
        $output = [
            'transaksi'=>$q_transaksi,
            'pendapatan'=>$nilai_pendapatan,
            'metode_pembayaran'=>join(',',$show_mp),
            'metode_pembayaran_terpilih'=>$pecah_metode,
            'id_pembayaran'=>$kumpul_pembayaran,
            'pilihan_metode_pembayaran'=>metode_pembayaran(),
        ];
        echo json_encode($output);
    }
}
