<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Badminton extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        // $this->form_validation->CI = &$this;
        $this->load->model([
            'datatables_model'                         => 'datatables_model', 
        ]);
        
        $id_hak_akses = $this->session->userdata('id_hak_akses'); 
       
            if (!in_array(2,$this->session->userdata('id_hak_akses'))) {
                redirect('auth/login/kick');
                
            }
    }





    public function index()
    {
        $data['judul'] = 'Master Data - Badminton';
        $data['deskripsi'] = 'Untuk mengelola Harga Badminton';


        $data['breadchumb'] = '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tambah">
                Tambah Jenis Member
              </button>';
        $q = $this->db->query("SELECT id_badminton, keterangan,harga,jam_mulai,jam_berakhir from master_badminton ")->result_array();
        $q_badminton_turnamen = $this->db->query("SELECT  id_badminton_turnamen,keterangan,harga,paket  from master_badminton_turnamen ")->result_array();
       
        $q_galeri  = $this->db->query("SELECT id_foto_galery, file, status from foto_galery where fasilitas='badminton' ")->result_array();
        $q_thumbnail  = $this->db->query("SELECT  file from foto_galery where fasilitas='badminton' and status='Thumbnail'")->row_array();
        $q_diskon_badminton  = $this->db->query("SELECT diskon from diskon_member_badminton")->row_array();
        $shoutlecook  = $this->db->query("SELECT selisih_tanpa_shoutlecook from  badminton_shoutlecook ")->row_array();
        $data['shoutlecook'] = $shoutlecook;
        $data['badminton'] = $q;
        $data['badminton_turnamen'] = $q_badminton_turnamen;
        $data['diskon_badminton'] = $q_diskon_badminton['diskon'];
     
        $data['galerry'] = $q_galeri;
        $data['thumbnail'] = $q_thumbnail['file'];
        $data['modal']                      = $this->load->view('user/admin/badminton/modal', $data, true);
        $this->template->load('template/user_adminlte','user/admin/badminton/data_badminton', $data);


        // $this->load->view('template/user_adminlte');
    }

	public function edit()
	{
		
            $id_badminton = $this->input->post('id_badminton');
            $q = $this->db->query("SELECT id_badminton,keterangan,harga,jam_mulai,jam_berakhir  from master_badminton where id_badminton='$id_badminton'")->row_array();
            echo json_encode($q);
		// $this->load->view('template/admin');
	}
  public function edit_diskon()
  {
    
            $q = $this->db->query("SELECT diskon from diskon_member_badminton")->row_array();
            echo json_encode($q);
    // $this->load->view('template/admin');
  }

    public function edit_turnamen()
    {
        
            $id_badminton = $this->input->post('id_badminton_turnamen');
            $q = $this->db->query("SELECT  id_badminton_turnamen,keterangan,harga,paket   from master_badminton_turnamen where id_badminton_turnamen='$id_badminton'")->row_array();
            echo json_encode($q);
        // $this->load->view('template/admin');
    }




    public function simpanedit(){
     
     $id_badminton = $this->input->post('id_badminton');
     $keterangan = $this->input->post('keterangan');
     $biaya = str_replace(',', '', $this->input->post('biaya'));
     $mulai = $this->input->post('mulai');
     $berakhir = $this->input->post('berakhir');
    
     $data = [
        'keterangan'=>$keterangan,
        'harga'=>$biaya,
        'jam_mulai'=>$mulai,
        'jam_berakhir'=>$berakhir,
    ];
     $where = [
        'id_badminton'=>$id_badminton,
     ];
     $this->db->update('master_badminton', $data, $where);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data badminton Diperbaharui</div>');
            redirect('user/admin/badminton');

    }

    public function simpanedit_diskon_member(){
     
     $diskon = $this->input->post('diskon');
    
     $data = [
        'diskon'=>$diskon,
    ];
     $this->db->update('diskon_member_badminton', $data);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Diskon member bulanan badminton Diperbaharui</div>');
            redirect('user/admin/badminton');

    }

    public function simpanedit_selisih_shoutlecook(){
     
     $selisih = str_replace(',', '', $this->input->post('selisih'));
    
     $data = [
        'selisih_tanpa_shoutlecook'=>$selisih,
    ];
     $this->db->update('badminton_shoutlecook', $data);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Harga selisih shoutlecook badminton Diperbaharui</div>');
            redirect('user/admin/badminton');

    }


    public function simpanedit_turnamen(){
     
     $id_badminton_turnamen = $this->input->post('id_badminton_turnamen');
     $paket = $this->input->post('paket');
     $keterangan = nl2br($this->input->post('keterangan'));
     $biaya = str_replace(',', '', $this->input->post('biaya'));
  
     $data = [
        'keterangan'=>$keterangan,
        'harga'=>$biaya,
        'paket'=>$paket,
    ];
     $where = [
        'id_badminton_turnamen'=>$id_badminton_turnamen,
     ];
     $this->db->update('master_badminton_turnamen', $data, $where);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data badminton Diperbaharui</div>');
            redirect('user/admin/badminton');

    }

    public function simpan_turnamen(){
     
     $paket = $this->input->post('paket');
     $keterangan = nl2br($this->input->post('keterangan'));
     $biaya = str_replace(',', '', $this->input->post('biaya'));
  
     $data = [
        'keterangan'=>$keterangan,
        'harga'=>$biaya,
        'paket'=>$paket,
    ];
     $this->db->insert('master_badminton_turnamen', $data);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Turnamen badminton Disimpan</div>');
            redirect('user/admin/badminton');

    }


    public function simpan_foto(){
        $config['upload_path']          = './file/public/badminton';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        // $config['max_size']             = 100;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;
        $new_file_name=date("Ymdhis");
        // $config['file_name']            = $new_file_name;
        $nama_file = $_FILES['berkas']['name'] ;
        $pecah = explode(".", $nama_file);
        $extensi = end($pecah);
        $new_file_name=date("Ymdhis").'.'.$extensi;
        $config['file_name']            = $new_file_name;
            $config['max_size']         = '20000';



      

        $this->load->library('upload', $config);
      
    
        if ( ! $this->upload->do_upload('berkas')){
                $pesan_ERROR = $this->upload->display_errors();
                $pesan = "<strong>Upload Failed ! </strong><br>".$pesan_ERROR;
                    $warna = 'alert alert-danger';



            $redirect = 'user/admin/badminton?tab=galeri';
        }else{
            array('upload_data' => $this->upload->data());
            $error = 0;
            $pesan = "<strong>Upload Success ! </strong><br>Data foto disimpan";
            $warna = 'alert alert-success';
            // $this->load->view('v_upload_sukses', $data);
            $redirect = 'user/admin/badminton?tab=galeri';

             $data_insert = [
                    'file'=>$new_file_name ,
                    'fasilitas'=>'badminton' ,
                   
               
              
            ];

                $this->db->insert('foto_galery', $data_insert);
        }


           
          $this->session->set_flashdata('pesan_galeri','<div class="'.$warna.'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$pesan.'</div>');

            redirect($redirect);

    }



    public function hapus_galery()
    {
            $id_foto_galery = $this->input->post('id_galerry');
            $file = $this->input->post('file');
            $q = $this->db->query("DELETE from foto_galery where id_foto_galery='$id_foto_galery'");
            unlink('./file/public/badminton/'.$file);
             $this->session->set_flashdata('pesan_galeri','<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Foto dihapus</div>');


        
    }

    public function update_thumbnail()
    {
            $id_foto_galery = $this->input->post('id_galerry');
            $q = $this->db->query("UPDATE foto_galery set status='' where fasilitas='badminton'");
            $q = $this->db->query("UPDATE foto_galery set status='Thumbnail' where id_foto_galery='$id_foto_galery'");
             $this->session->set_flashdata('pesan_galeri','<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Foto telah dijadikan thumbnail</div>');


        
    }




    public function hapus_turnamen()
    {
            $id_turnamen = $this->input->post('id_badminton_turnamen');
            $q = $this->db->query("DELETE from master_badminton_turnamen where id_badminton_turnamen='$id_turnamen'");
             $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Turnamen badminton Dihapus</div>');
            
        
    }

}
