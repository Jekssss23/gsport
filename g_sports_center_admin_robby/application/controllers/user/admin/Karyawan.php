<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan extends CI_Controller {


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
        $data['judul'] = 'Master Data - Akses Login';
        $data['deskripsi'] = 'Untuk mengelola user yang bisa login ke dalam aplikasi';

        $data['breadchumb'] = '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tambah">
                Tambah User
              </button>';
        $q = $this->db->query("SELECT id_user,   id_hak_akses, nama, alamat, nohp, email,jabatan, foto, status, status_akses   from master_user where status !='delete' ")->result_array();
        $q_ha = $this->db->query("SELECT id_hak_akses, nama_hak_akses  from hak_akses")->result_array();
        $q_outlet_fnb = $this->db->query("SELECT id_outlet, nama_outlet  from outlet_fnb")->result_array();
        $q_outlet_proshop = $this->db->query("SELECT id_outlet, nama_outlet  from outlet_proshop")->result_array();
        $kumpul_ha = [];
        foreach ($q_ha as $k => $v) { 
          $kumpul_ha[$v['id_hak_akses']] = $v['nama_hak_akses'];
        }

        $kumpul_outlet_fnb = [];
        foreach ($q_outlet_fnb as $k => $v) { 
          $kumpul_outlet_fnb[$v['id_outlet']] = $v['nama_outlet'];
        }
        $kumpul_outlet_proshop = [];
        foreach ($q_outlet_proshop as $k => $v) { 
          $kumpul_outlet_proshop[$v['id_outlet']] = $v['nama_outlet'];
        }
        $data['user'] = $q;
        $data['hak_akses'] = $kumpul_ha;
        $data['outlet_fnb'] = $kumpul_outlet_fnb;
        $data['outlet_proshop'] = $kumpul_outlet_proshop;
    
        $data['modal']                      = $this->load->view('user/admin/user/modal', $data, true);
        $this->template->load('template/user_adminlte','user/admin/user/data_user', $data);


        // $this->load->view('template/user_adminlte');
    }

	public function edit()
	{
		
            $id_user = $this->input->post('id_user');
            $q = $this->db->query("SELECT id_user, id_hak_akses, nama, alamat, nohp, email,jabatan, username,password, status, status_akses from master_user where id_user='$id_user'")->row_array();
            $q_hs = $this->db->query("SELECT id_hak_akses from hak_akses_user where id_user='$id_user'")->result_array();
            $output = ['user'=>$q , 'hak_akses'=>$q_hs];
            echo json_encode($output);
		// $this->load->view('template/admin');
	}
 
  
	public function atur_outlet()
	{
		
            $id_user = $this->input->post('id_user');
            $q = $this->db->query("SELECT id_user, id_hak_akses, nama, alamat, nohp, email,jabatan, username,password, status, status_akses from master_user where id_user='$id_user'")->row_array();
            $q_outlet_fnb = $this->db->query("SELECT id_outlet from outlet_fnb_user where id_user='$id_user'")->result_array();
            $q_outlet_proshop = $this->db->query("SELECT id_outlet from outlet_proshop_user where id_user='$id_user'")->result_array();
            $output = ['user'=>$q , 'outlet_fnb'=>$q_outlet_fnb, 'outlet_proshop'=>$q_outlet_proshop];
            echo json_encode($output);
		// $this->load->view('template/admin');
	}
 
  





    public function simpan(){
        $config['upload_path']          = './file/user';
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
            $config['max_size']         = '10000';


        $nama = $this->input->post('nama');
        $status = $this->input->post('status');
        $alamat = $this->input->post('alamat');
        $nohp = $this->input->post('nohp');
        $jabatan = $this->input->post('jabatan');
        $email = $this->input->post('email');
        $hak_akses = $this->input->post('hak_akses');
        $username = $this->input->post('username');
        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);

        $this->db->trans_start();
        

        $this->load->library('upload', $config);
      
    

        if ( ! $this->upload->do_upload('berkas')){


            if ($nama_file=='') {
                 $data_insert = [
                    'nama'=>$nama,
                    'alamat'=>$alamat,
                    'nohp'=>$nohp,
                    'jabatan'=>$jabatan,
                    'email'=>$email,
                    // 'id_hak_akses'=>$hak_akses,
                    'username'=>$username,
                    'password'=>$password,
                    'status'=>1,
                    'status_akses'=>$status,
                   
                ];
                $error = 0;
                $pesan = "Data user disimpan tanpa foto";

            $warna = 'alert alert-success';
              
            }else{
                if ($this->upload->display_errors()) {
                    $pesan_ERROR = $this->upload->display_errors();
                    $error = 1;
                    $warna = 'alert alert-danger';
                    $pesan = "<strong>Upload Failed ! </strong><br>".$pesan_ERROR;
                    $data_insert = [
                        'nama'=>$nama,
                        'alamat'=>$alamat,
                        'nohp'=>$nohp,
                        'jabatan'=>$jabatan,
                        'email'=>$email,
                        // 'id_hak_akses'=>$hak_akses,
                        'username'=>$username,
                        'password'=>$password,
                        'status'=>1,
                    'status_akses'=>$status,
                       
                      
                    ];
                     
                }else{
                    $pesan_ERROR = '';
                    $error = 0;
                    $warna = 'alert alert-success';
                    $data_insert = [
                        'nama'=>$nama,
                        'alamat'=>$alamat,
                        'nohp'=>$nohp,
                        'jabatan'=>$jabatan,
                        'email'=>$email,
                        // 'id_hak_akses'=>$hak_akses,
                        'username'=>$username,
                        'password'=>$password,
                        'status'=>1,
                    'status_akses'=>$status,
                       
                      
                    ];
                  $pesan = "<strong>Upload Failed ! </strong><br>".$pesan_ERROR;

                }

            }

          
        }else{
            array('upload_data' => $this->upload->data());
            $error = 0;
            $pesan = "<strong>Upload Success ! </strong><br>Data user disimpan dengan foto";
            $warna = 'alert alert-success';
            // $this->load->view('v_upload_sukses', $data);
            $redirect = "";

            
                 $data_insert = [
                    'nama'=>$nama,
                    'alamat'=>$alamat,
                    'nohp'=>$nohp,
                    'jabatan'=>$jabatan,
                    'email'=>$email,
                    // 'id_hak_akses'=>$hak_akses,
                    'username'=>$username,
                    'password'=>$password,
                    'status'=>1,
                    'foto'=>$new_file_name,
                    'status_akses'=>$status,
               
              
              
            ];
        }
            if ($error>0) {
                $redirect = 'user/admin/user';
            }else{
              $q_cek_password = $this->db->query("SELECT username from master_user where username='$username'")->num_rows();
              $q_cek_email = $this->db->query("SELECT email from master_user where email='$email'")->num_rows();

              if ($q_cek_password>0) {
                          $this->session->set_flashdata('pesan','<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal menyimpan user. Username sudah digunakan. silahkan gunakan username lain</div>');
              } else if ($q_cek_email > 0) {
                          $this->session->set_flashdata('pesan','<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal menyimpan user. Email sudah digunakan.</div>');
              } else {

                if (count($hak_akses)>0) {
                    $this->load->library('firebase_admin');
                    
                    // 1. Create Firebase Auth User
                    $firebase_user = $this->firebase_admin->create_user([
                        'email' => $email,
                        'password' => $this->input->post('password'), // Use raw password for Firebase
                        'displayName' => $nama
                    ]);

                    if (isset($firebase_user->error)) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('pesan','<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal membuat akun Firebase: '.$firebase_user->error.'</div>');
                        redirect("user/admin/user");
                        return;
                    }

                    $firebase_uid = $firebase_user->localId;
                    
                    if (empty($firebase_uid)) {
                        $this->db->trans_rollback();
                        log_message('error', 'Karyawan: Firebase UID is empty for user: ' . $email);
                        $this->session->set_flashdata('pesan','<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal mendapatkan UID dari Firebase. Silakan coba lagi.</div>');
                        redirect("user/admin/user");
                        return;
                    }

                    $data_insert['firebase_uid'] = $firebase_uid;

                    // 2. Create Firestore Document
                    $firestore_data = [
                        'email' => $email,
                        'name' => $nama,
                        'role' => 'admin', // Staff log in as admin in mobile app
                        'firebase_uid' => $firebase_uid,
                        'jabatan' => $jabatan,
                        'nohp' => $nohp,
                        'status' => 'active',
                        'createdAt' => date('Y-m-d H:i:s')
                    ];
                    
                    if (isset($new_file_name) && $nama_file != '') {
                        $firestore_data['foto'] = $new_file_name;
                    }

                    $fs_result = $this->firebase_admin->create_firestore_document('users', $firebase_uid, $firestore_data);
                    
                    if (!$fs_result) {
                        $this->db->trans_rollback();
                        log_message('error', 'Karyawan: Failed to create Firestore document for UID: ' . $firebase_uid);
                        $this->session->set_flashdata('pesan','<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal sinkronisasi ke Cloud Firestore. Silakan coba lagi.</div>');
                        redirect("user/admin/user");
                        return;
                    }

                    $kumpul_hs_user = [];
                      $this->db->insert('master_user', $data_insert);
                      $id_user = $this->db->insert_id();
                    foreach ($hak_akses as $key => $value) {
                       $data = [
                        'id_user'=>$id_user,
                        'id_hak_akses'=>$value
                       ];
                       array_push($kumpul_hs_user, $data);
                    }

                    $this->db->insert_batch('hak_akses_user', $kumpul_hs_user);
                    $this->db->trans_commit();
                          $this->session->set_flashdata('pesan','<div class="'.$warna.'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$pesan.' dan akun Firebase berhasil dibuat.</div>');

                  
                }else{
                    $this->db->trans_rollback();

                          $this->session->set_flashdata('pesan','<div class="'.$warna.'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal menyimpan data. Anda belum memilih hak akses</div>');

                }


              }






                $redirect = "user/admin/user";



            }

            redirect($redirect);

    }



    public function simpanedit(){
        $config['upload_path']          = './file/user';
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
            $config['max_size']         = '10000';


        $nama = $this->input->post('nama');
        $alamat = $this->input->post('alamat');
        $nohp = $this->input->post('nohp');
        $jabatan = $this->input->post('jabatan');
        $email = $this->input->post('email');
        $id_user = $this->input->post('id_user');

        $where = ['id_user'=>$id_user];
        $this->db->trans_start();
   

      

        $this->load->library('upload', $config);
      
    

        if ( ! $this->upload->do_upload('berkas')){


            if ($nama_file=='') {
                 $data_insert = [
                    'nama'=>$nama,
                    'alamat'=>$alamat,
                    'nohp'=>$nohp,
                    'jabatan'=>$jabatan,
                    'email'=>$email,
                    'status'=>1,
                   
                ];
                $error = 0;
                $pesan = "Data user disimpan tanpa foto";

            $warna = 'alert alert-success';
              
            }else{
                if ($this->upload->display_errors()) {
                    $pesan_ERROR = $this->upload->display_errors();
                    $error = 1;
                    $warna = 'alert alert-danger';
                    $pesan = "<strong>Upload Failed ! </strong><br>".$pesan_ERROR;
                    $data_insert = [
                        'nama'=>$nama,
                        'alamat'=>$alamat,
                        'nohp'=>$nohp,
                        'jabatan'=>$jabatan,
                        'email'=>$email,
                        'status'=>1,
                       
                      
                    ];
                     
                }else{
                    $pesan_ERROR = '';
                    $error = 0;
                    $warna = 'alert alert-success';
                    $data_insert = [
                        'nama'=>$nama,
                        'alamat'=>$alamat,
                        'nohp'=>$nohp,
                        'jabatan'=>$jabatan,
                        'email'=>$email,
                        'status'=>1,
                       
                      
                    ];
                  $pesan = "<strong>Upload Failed ! </strong><br>".$pesan_ERROR;

                }

            }

          
        }else{
            array('upload_data' => $this->upload->data());
            $error = 0;
            $pesan = "<strong>Upload Success ! </strong><br>Data user disimpan dengan foto";
            $warna = 'alert alert-success';
            // $this->load->view('v_upload_sukses', $data);
            $redirect = "";

            
                 $data_insert = [
                    'nama'=>$nama,
                    'alamat'=>$alamat,
                    'nohp'=>$nohp,
                    'jabatan'=>$jabatan,
                    'email'=>$email,
                    'status'=>1,
                    'foto'=>$new_file_name,
               
              
              
            ];
        }
            if ($error>0) {
                $redirect = 'user/admin/user';
            }else{
                $this->db->update('master_user', $data_insert, $where);
                
                // Sync to Firestore if firebase_uid exists
                $user = $this->db->get_where('master_user', $where)->row_array();
                if ($user && !empty($user['firebase_uid'])) {
                    $this->load->library('firebase_admin');
                    
                    // Sync to Firebase Auth (email & display name)
                    $this->firebase_admin->update_user($user['firebase_uid'], [
                        'email' => $email,
                        'displayName' => $nama
                    ]);

                    // Sync to Firestore
                    $firestore_update = [
                        'name' => $nama,
                        'email' => $email,
                        'jabatan' => $jabatan,
                        'nohp' => $nohp
                    ];
                    if (isset($data_insert['foto'])) {
                        $firestore_update['foto'] = $data_insert['foto'];
                    }
                    $this->firebase_admin->create_firestore_document('users', $user['firebase_uid'], $firestore_update);
                }

                $this->session->set_flashdata('pesan','<div class="'.$warna.'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$pesan.'</div>');
                $this->db->trans_commit();
                $redirect = "user/admin/user";
            }

            redirect($redirect);

    }









 
    public function simpanedit_login(){
     
     $id_user = $this->input->post('id_user');

        $hak_akses = $this->input->post('hak_akses');
        $status = $this->input->post('status');
        $username = $this->input->post('username');
        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        
        $this->db->trans_start();
    
    $where = ['id_user'=>$id_user];
     

      $q_cek_password = $this->db->query("SELECT username from master_user where username='$username' and id_user !='$id_user'")->num_rows();
              if ($q_cek_password>0) {
                          $this->session->set_flashdata('pesan','<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal menyimpan user. Username sudah digunakan. silahkan gunakan username lain</div>');
                # code...
              }else{
                if ($this->input->post('password')=='') {
                   $data = [
                      'username'=>$username,
                      // 'id_hak_akses'=>$hak_akses,
                      'status_akses'=>$status,
                  ];
                    if (count($hak_akses)>0) {
                    $kumpul_hs_user = [];
                         $this->db->update('master_user', $data, $where);
                        $this->db->delete('hak_akses_user', $where);
                    foreach ($hak_akses as $key => $value) {
                       $data = [
                        'id_user'=>$id_user,
                        'id_hak_akses'=>$value
                       ];
                       array_push($kumpul_hs_user, $data);
                    }
                    $this->db->insert_batch('hak_akses_user', $kumpul_hs_user);
                    $this->db->trans_commit();
                    $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data login user diperbaharui tanpa mengubah password</div>');
                    }else{
                        $this->db->trans_rollback();
                          $this->session->set_flashdata('pesan','<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal menyimpan data. Anda belum memilih hak akses</div>');
                    }
                }else{
                   $data = [
                      'username'=>$username,
                      'password'=>$password,
                      // 'id_hak_akses'=>$hak_akses,                   
                      'status_akses'=>$status,
                  ];
                    if (count($hak_akses)>0) {
                        $kumpul_hs_user = [];
                             $this->db->update('master_user', $data, $where);
                            $this->db->delete('hak_akses_user', $where);
                        foreach ($hak_akses as $key => $value) {
                           $data = [
                            'id_user'=>$id_user,
                            'id_hak_akses'=>$value
                           ];
                           array_push($kumpul_hs_user, $data);
                        }

                        $this->db->insert_batch('hak_akses_user', $kumpul_hs_user);

                        // Sync to Firebase Auth if firebase_uid exists
                        $user = $this->db->get_where('master_user', $where)->row_array();
                        if ($user && !empty($user['firebase_uid'])) {
                            $this->load->library('firebase_admin');
                            $this->firebase_admin->update_user($user['firebase_uid'], [
                                'password' => $this->input->post('password')
                            ]);
                        }

                        $this->db->trans_commit();
                        $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data login user diperbaharui dengan mengubah password</div>');

                      
                    }else{
                        $this->db->trans_rollback();

                              $this->session->set_flashdata('pesan','<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Gagal menyimpan data. Anda belum memilih hak akses</div>');

                    }

                }

              }


// echo $this->session->flashdata('pesan');
            redirect('user/admin/user');

    }


 
    public function simpanedit_outlet_user(){
     
     $id_user = $this->input->post('id_user');

        $outlet_fnb = $this->input->post('outlet_fnb');
        $outlet_proshop = $this->input->post('outlet_proshop');
        $status = $this->input->post('status');
        $username = $this->input->post('username');
        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        
        $this->db->trans_start();
    
    $where = ['id_user'=>$id_user];
     
                  
                        $kumpul_outlet_fnb = [];
                        $kumpul_outlet_proshop = [];
                            $this->db->delete('outlet_fnb_user', $where);
                            $this->db->delete('outlet_proshop_user', $where);
                        foreach ($outlet_fnb as $key => $value) {
                           $data_outlet_fnb = [
                            'id_user'=>$id_user,
                            'id_outlet'=>$value
                           ];
                          
                           array_push($kumpul_outlet_fnb, $data_outlet_fnb);
                        }
                        foreach ($outlet_proshop as $key => $value) {
                           $data_outlet_proshop = [
                            'id_user'=>$id_user,
                            'id_outlet'=>$value
                           ];
                          
                           array_push($kumpul_outlet_proshop, $data_outlet_proshop);
                        }


                    if (count($outlet_fnb)>0) {
                        $this->db->insert_batch('outlet_fnb_user', $kumpul_outlet_fnb);
                    }

                    if (count($outlet_proshop)>0) {
                        $this->db->insert_batch('outlet_proshop_user', $kumpul_outlet_proshop);
                    }
                        $this->db->trans_commit();
                        $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data login user diperbaharui dengan mengubah password</div>');

                 

               


// echo $this->session->flashdata('pesan');
            redirect('user/admin/user');

    }



    public function hapus()
    {
            $id = $this->input->post('id');
            
            // Get user data to get firebase_uid
            $user = $this->db->get_where('master_user', ['id_user' => $id])->row_array();
            
            if ($user && !empty($user['firebase_uid'])) {
                $this->load->library('firebase_admin');
                $this->firebase_admin->delete_firestore_document('users', $user['firebase_uid']);
            }

            $q = $this->db->query("UPDATE master_user set status='delete' where id_user='$id'");
            $q = $this->db->query("DELETE  FROM hak_akses_user where id_user='$id'");
             $this->session->set_flashdata('pesan','<div style="margin-bottom:10px" class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data User Dihapus</div>');
            
        
    }

}
