<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class futsal extends CI_Controller {


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
        $data['judul'] = 'Master Data - Futsal';
        $data['deskripsi'] = 'Untuk mengelola Harga Futsal';

        $data['breadchumb'] = '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tambah">
                Tambah Jenis Member
              </button>';
        $q = $this->db->query("SELECT id_futsal, keterangan,harga,jam_mulai,jam_berakhir from master_futsal ")->result_array();
        $q_futsal_turnamen = $this->db->query("SELECT  id_futsal_turnamen,keterangan,harga,paket  from master_futsal_turnamen ")->result_array();
       
        $q_galeri  = $this->db->query("SELECT id_foto_galery, file, status from foto_galery where fasilitas='futsal' ")->result_array();
        $q_thumbnail  = $this->db->query("SELECT  file from foto_galery where fasilitas='Futsal' and status='Thumbnail'")->row_array();
        $q_diskon_futsal  = $this->db->query("SELECT diskon from diskon_member_futsal")->row_array();
        $data['futsal'] = $q;
        $data['futsal_turnamen'] = $q_futsal_turnamen;
        $data['diskon_futsal'] = $q_diskon_futsal['diskon'];
     
        $data['galerry'] = $q_galeri;
        $data['thumbnail'] = $q_thumbnail['file'];
        $data['modal']                      = $this->load->view('user/admin/futsal/modal', $data, true);
        $this->template->load('template/user_adminlte','user/admin/futsal/data_futsal', $data);


        // $this->load->view('template/user_adminlte');
    }

	public function edit()
	{
		
            $id_futsal = $this->input->post('id_futsal');
            $q = $this->db->query("SELECT id_futsal,keterangan,harga,jam_mulai,jam_berakhir  from master_futsal where id_futsal='$id_futsal'")->row_array();
            echo json_encode($q);
		// $this->load->view('template/admin');
	}
  public function edit_diskon()
  {
    
            $q = $this->db->query("SELECT diskon from diskon_member_futsal")->row_array();
            echo json_encode($q);
    // $this->load->view('template/admin');
  }

    public function edit_turnamen()
    {
        
            $id_futsal = $this->input->post('id_futsal_turnamen');
            $q = $this->db->query("SELECT  id_futsal_turnamen,keterangan,harga,paket   from master_futsal_turnamen where id_futsal_turnamen='$id_futsal'")->row_array();
            echo json_encode($q);
        // $this->load->view('template/admin');
    }




    public function simpanedit(){
     
     $id_futsal = $this->input->post('id_futsal');
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
        'id_futsal'=>$id_futsal,
     ];
     $this->db->update('master_futsal', $data, $where);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Futsal Diperbaharui</div>');
            redirect('user/admin/futsal');

    }

    public function simpanedit_diskon_member(){
     
     $diskon = $this->input->post('diskon');
    
     $data = [
        'diskon'=>$diskon,
    ];
     $this->db->update('diskon_member_futsal', $data);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Diskon member bulanan futsal Diperbaharui</div>');
            redirect('user/admin/futsal');

    }


    public function simpanedit_turnamen(){
     
     $id_futsal_turnamen = $this->input->post('id_futsal_turnamen');
     $paket = $this->input->post('paket');
     $keterangan = nl2br($this->input->post('keterangan'));
     $biaya = str_replace(',', '', $this->input->post('biaya'));
  
     $data = [
        'keterangan'=>$keterangan,
        'harga'=>$biaya,
        'paket'=>$paket,
    ];
     $where = [
        'id_futsal_turnamen'=>$id_futsal_turnamen,
     ];
     $this->db->update('master_futsal_turnamen', $data, $where);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Futsal Diperbaharui</div>');
            redirect('user/admin/futsal');

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
     $this->db->insert('master_futsal_turnamen', $data);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Turnamen Futsal Disimpan</div>');
            redirect('user/admin/futsal');

    }


    public function simpan_foto(){
        $config['upload_path']          = './file/public/futsal';
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



            $redirect = 'user/admin/futsal?tab=galeri';
        }else{
            array('upload_data' => $this->upload->data());
            $error = 0;
            $pesan = "<strong>Upload Success ! </strong><br>Data foto disimpan";
            $warna = 'alert alert-success';
            // $this->load->view('v_upload_sukses', $data);
            $redirect = 'user/admin/futsal?tab=galeri';

             $data_insert = [
                    'file'=>$new_file_name ,
                    'fasilitas'=>'Futsal' ,
                   
               
              
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
            unlink('./file/public/futsal/'.$file);
             $this->session->set_flashdata('pesan_galeri','<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Foto dihapus</div>');


        
    }

    public function update_thumbnail()
    {
            $id_foto_galery = $this->input->post('id_galerry');
            $q = $this->db->query("UPDATE foto_galery set status='' where fasilitas='futsal'");
            $q = $this->db->query("UPDATE foto_galery set status='Thumbnail' where id_foto_galery='$id_foto_galery'");
             $this->session->set_flashdata('pesan_galeri','<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Foto telah dijadikan thumbnail</div>');


        
    }




    public function hapus_turnamen()
    {
            $id_turnamen = $this->input->post('id_futsal_turnamen');
            $q = $this->db->query("DELETE from master_futsal_turnamen where id_futsal_turnamen='$id_turnamen'");
             $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Turnamen Futsal Dihapus</div>');
            
        
    }

}
