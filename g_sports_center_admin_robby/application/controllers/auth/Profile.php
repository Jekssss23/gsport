<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
       
        $this->id_tupoksi = 1;
    }
	public function update_password()
	{
		
		
	     $id_user = $this->session->userdata('id_user');
	     $user = $this->db->query("SELECT * from user u left join hak_akses ha on u.id_hak_akses = ha.id_hak_akses where u.id_user = '$id_user'")->row_array();
		$data['judul'] = "Ubah Password";
		$data['user'] = $user;
		$this->template->load('template/user_adminlte','user/user/akun/edit_password', $data);
	}
	
   
	public function simpanedit_password()
	{
		
		
	     $id_user = $this->session->userdata('id_user');
	     $username = $this->input->post('username');
	     $password_lama = $this->input->post('password_lama');
	     $password_baru = $this->input->post('password_baru');
	     $password_baru_konfirm = $this->input->post('password_baru_konfirm');
	     $cek_username = $this->db->query("SELECT username, password from user where username='$username' and id_user!='$id_user'");
	     $user = $cek_username->row_array();
               if ($cek_username->num_rows()>0) {

			        $this->session->set_flashdata('pesan','<div class="alert alert-danger">Gagal mengubah login.!<br>Username sudah digunakan<br>Silahkan gunakan username lain</div>');
                   // $this->session->set_flashdata('pesan','dddddd');
                   redirect('auth/profile/update_password');
		               // echo "strssing";
               }else{
				     $cek_password = $this->db->query("SELECT id_hak_akses, password from user where id_user='$id_user'")->row_array();

               		if (password_verify($password_lama, $cek_password['password']) ) {

               			if ($password_baru==$password_baru_konfirm) {
               				$update = $this->db->update('user',['password'=>password_hash($password_baru, PASSWORD_DEFAULT)], ['id_user'=>$id_user]);
               			  $this->session->set_flashdata('pesan','<div class="alert alert-success">Berhasil mengubah login.!</div>');
		                   redirect('/user/user/dashboard');
               				
               			}else{ 
               				$this->session->set_flashdata('pesan','<div class="alert alert-danger">Gagal mengubah login.!<br>konfirmasi password baru tidak cocok</div>');
		                   redirect('auth/profile/update_password');
							
               			}
               			  // $this->session->set_flashdata('pesan','<div class="alert alert-danger">Gagal mengubah login.!<br>Password lama salah<br>Silahkan masukan password yang benar<br>'.json_encode($cek_password).'</div>');
               			
               		}else{
               			  $this->session->set_flashdata('pesan','<div class="alert alert-danger">Gagal mengubah login.!<br>Password lama salah<br>Silahkan masukan password yang benar</div>');
	                   redirect('auth/profile/update_password');
               		}


               }
	}
	
	public function proses_login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$user = $this->db->query("SELECT * from user where username='$username'")->row_array();
		if ($user) {
				if ($user['status_akses'] == 1) {
					if (password_verify($password, $user['password']) ) {
						$output['status'] 	= true;
						$output['message']	= 'Success !';
						
						$session = [
							'login' 		=> true,
							'id_user' 		=> $user['id_user'],
							'full_name' 	=> $user['nama'],
							'jabatan' 	=> $user['jabatan'],
							
						
							// 'is_active'	=> $user['is_active'],
							'id_session' 	=> session_id()
						];
						$this->session->set_userdata($session);
						$this->session->set_flashdata('pesan','<div class="alert alert-secondary dark alert-dismissible fade show" role="alert"> Login Berhasil.!
	                  <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
	                </div>');
						if ($user['id_hak_akses']==1) {
							$redirect = '/user/master/dashboard';
							# code...
						}else if ($user['id_hak_akses']==2) {
							$redirect = '/user/admin/dashboard';

						}else{
							$redirect = '/user/kassa/dashboard';

						}
					} else {
						// $this->attempts($post['email']);
						$this->session->set_flashdata('pesan','<div class="alert alert-danger dark alert-dismissible fade show" role="alert"> Password Salah.!
	                      
	                    </div>');
						$redirect = '/auth/login';
					}
				} else {
					$redirect = '/auth/login';
					$this->session->set_flashdata('pesan','<div class="alert alert-danger dark alert-dismissible fade show" role="alert"> User tidak aktif.!
                      
                    </div>');
				}
			} else {
				$redirect = '/auth/login';
				$this->session->set_flashdata('pesan','<div class="alert alert-danger dark alert-dismissible fade show" role="alert"> Username dan Password Salah.!
                  
                </div>');
			}

			redirect($redirect);
	}
	

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('/');
	}
}
