<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class pickle extends CI_Controller {


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
        $data['judul'] = 'Master Data - pickle';
        $data['deskripsi'] = 'Untuk mengelola Harga pickle';

        $data['breadchumb'] = '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tambah">
                Tambah Jenis Member
              </button>';
        $q = $this->db->query("SELECT id_pickle, keterangan,harga,jam_mulai,jam_berakhir from master_pickle ")->result_array();
        $q_pickle_turnamen = $this->db->query("SELECT  id_pickle_turnamen,keterangan,harga,paket  from master_pickle_turnamen ")->result_array();
       
        $q_galeri  = $this->db->query("SELECT id_foto_galery, file, status from foto_galery where fasilitas='pickle' ")->result_array();
        $q_thumbnail  = $this->db->query("SELECT  file from foto_galery where fasilitas='pickle' and status='Thumbnail'")->row_array();
        $q_diskon_pickle  = $this->db->query("SELECT diskon from diskon_member_pickle")->row_array();
        $data['pickle'] = $q;
        $data['pickle_turnamen'] = $q_pickle_turnamen;
        $data['diskon_pickle'] = $q_diskon_pickle['diskon'];
     
        $data['galerry'] = $q_galeri;
        $data['thumbnail'] = $q_thumbnail['file'];
        $data['modal']                      = $this->load->view('user/admin/pickle/modal', $data, true);
        $this->template->load('template/user_adminlte','user/admin/pickle/data_pickle', $data);


        // $this->load->view('template/user_adminlte');
    }

	public function edit()
	{
		
            $id_pickle = $this->input->post('id_pickle');
            $q = $this->db->query("SELECT id_pickle,keterangan,harga,jam_mulai,jam_berakhir  from master_pickle where id_pickle='$id_pickle'")->row_array();
            echo json_encode($q);
		// $this->load->view('template/admin');
	}
  public function edit_diskon()
  {
    
            $q = $this->db->query("SELECT diskon from diskon_member_pickle")->row_array();
            echo json_encode($q);
    // $this->load->view('template/admin');
  }

    public function edit_turnamen()
    {
        
            $id_pickle = $this->input->post('id_pickle_turnamen');
            $q = $this->db->query("SELECT  id_pickle_turnamen,keterangan,harga,paket   from master_pickle_turnamen where id_pickle_turnamen='$id_pickle'")->row_array();
            echo json_encode($q);
        // $this->load->view('template/admin');
    }




    public function simpanedit(){
     
     $id_pickle = $this->input->post('id_pickle');
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
        'id_pickle'=>$id_pickle,
     ];
     $this->db->update('master_pickle', $data, $where);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data pickle Diperbaharui</div>');
            redirect('user/admin/pickle');

    }

    public function simpanedit_diskon_member(){
     
     $diskon = $this->input->post('diskon');
    
     $data = [
        'diskon'=>$diskon,
    ];
     $this->db->update('diskon_member_pickle', $data);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Diskon member bulanan pickle Diperbaharui</div>');
            redirect('user/admin/pickle');

    }


    public function simpanedit_turnamen(){
     
     $id_pickle_turnamen = $this->input->post('id_pickle_turnamen');
     $paket = $this->input->post('paket');
     $keterangan = nl2br($this->input->post('keterangan'));
     $biaya = str_replace(',', '', $this->input->post('biaya'));
  
     $data = [
        'keterangan'=>$keterangan,
        'harga'=>$biaya,
        'paket'=>$paket,
    ];
     $where = [
        'id_pickle_turnamen'=>$id_pickle_turnamen,
     ];
     $this->db->update('master_pickle_turnamen', $data, $where);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data pickle Diperbaharui</div>');
            redirect('user/admin/pickle');

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
     $this->db->insert('master_pickle_turnamen', $data);
     $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Turnamen pickle Disimpan</div>');
            redirect('user/admin/pickle');

    }


    public function simpan_foto(){
        $config['upload_path']          = './file/public/pickle';
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



            $redirect = 'user/admin/pickle?tab=galeri';
        }else{
            array('upload_data' => $this->upload->data());
            $error = 0;
            $pesan = "<strong>Upload Success ! </strong><br>Data foto disimpan";
            $warna = 'alert alert-success';
            // $this->load->view('v_upload_sukses', $data);
            $redirect = 'user/admin/pickle?tab=galeri';

             $data_insert = [
                    'file'=>$new_file_name ,
                    'fasilitas'=>'pickle' ,
                   
               
              
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
            unlink('./file/public/pickle/'.$file);
             $this->session->set_flashdata('pesan_galeri','<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Foto dihapus</div>');


        
    }

    public function update_thumbnail()
    {
            $id_foto_galery = $this->input->post('id_galerry');
            $q = $this->db->query("UPDATE foto_galery set status='' where fasilitas='pickle'");
            $q = $this->db->query("UPDATE foto_galery set status='Thumbnail' where id_foto_galery='$id_foto_galery'");
             $this->session->set_flashdata('pesan_galeri','<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Foto telah dijadikan thumbnail</div>');


        
    }




    public function hapus_turnamen()
    {
            $id_turnamen = $this->input->post('id_pickle_turnamen');
            $q = $this->db->query("DELETE from master_pickle_turnamen where id_pickle_turnamen='$id_turnamen'");
             $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Turnamen pickle Dihapus</div>');
            
        
    }

}
