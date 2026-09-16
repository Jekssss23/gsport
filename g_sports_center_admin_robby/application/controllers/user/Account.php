<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        // $this->form_validation->CI = &$this;
        $this->load->model([
            'datatables_model'                         => 'datatables_model', 
        ]);
        
        $id_hak_akses = $this->session->userdata('id_hak_akses'); 
        //   if ($id_hak_akses!='2') {
        //     redirect('auth/login/kick');
           
        // }
    }





    public function edit_password()
    {
        $data['judul'] = 'Edit Password';
        $data['deskripsi'] = 'Ganti Username dan password login';
        $id_user = id_user();
        $user = data_user($id_user);
        $data['user'] = $user;

    
        $data['modal']                      = '';
        $this->template->load('template/user_adminlte','user/user/account/edit_password', $data);


    }

  public function simpanedit_password()
        {
               $id_user = id_user();
               $username = $this->input->post('username');
               $pass_lama = $this->input->post('pass_lama');
               $pass_baru = $this->input->post('pass_baru');

               $cek_username = $this->db->query("SELECT username, password from master_user where username='$username' and id_user !='$id_user'");
               $akun_saya = $this->db->query("SELECT password, id_hak_akses from master_user where id_user ='$id_user'");
               if ($cek_username->num_rows()>0) {
                $this->session->set_flashdata('pesan','<div class="alert alert-info">Gagal mengubah password. Username "'.$username.'" sudah digunakan</div>');
                   redirect('user/account/edit_password');
               }else{
                $user = $akun_saya->row_array();
                if (strlen($username)<8) {
                     $this->session->set_flashdata('pesan','<div class="alert alert-info">Gagal mengubah username. Username minimal 8 karakter</div>');
                   redirect('user/account/edit_password');
                }
                elseif (strlen($pass_baru)<8) {
                     $this->session->set_flashdata('pesan','<div class="alert alert-info">Gagal mengubah password. Password minimal 8 karakter</div>');
                   redirect('user/account/edit_password');
                }else{
                    if (password_verify($pass_lama, $user['password']) ) {
                           $data = ['password'=>password_hash($pass_baru, PASSWORD_DEFAULT), 'username'=>$username];
                           $where = ['id_user'=>$id_user];
                           $this->db->update('master_user',$data, $where);
                            $this->session->set_flashdata('pesan','<div class="alert alert-info">Username dan password berhasil diperbaharui</div>');
                            if ($user['id_hak_akses']=='1') {
                               redirect('user/pimpinan/dashboard');
                            }
                            elseif ($user['id_hak_akses']=='2') {
                               redirect('user/admin/dashboard');
                            }
                            elseif ($user['id_hak_akses']=='3') {
                               redirect('user/gro/dashboard');
                            }
                            elseif ($user['id_hak_akses']=='4') {
                               redirect('user/gym/dashboard');
                            }else{
                               redirect('user/office/dashboard');

                            }
                    }else{
                        $this->session->set_flashdata('pesan','<div class="alert alert-info">Password lama tidak cocok<br>Harap masukan password lama dengan benar</div>');
                           redirect('user/account/edit_password');

                    }

                }




               }
        }
}
