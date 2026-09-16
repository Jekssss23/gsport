<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
       
        $this->id_tupoksi = 1;
    }
	public function index()
	{
		
		// $this->template->load('template/auth','auth/login');
		$this->load->view('auth/login/login');
	}
	
	public function proses_login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$user = $this->db->query("SELECT status_akses, password, id_user, nama, id_hak_akses, jabatan from master_user where username='$username'")->row_array();
		if ($user) {
				if ($user['status_akses'] == 1) {
					if (password_verify($password, $user['password']) ) {
						$output['status'] 	= true;
						$output['message']	= 'Success !';
						$id_user = $user['id_user'];
						$q_ha = $this->db->query("SELECT id_hak_akses from hak_akses_user where id_user='$id_user'");
						$kumpul_ha = [];
						foreach ($q_ha->result_array() as $k => $v) {
							array_push($kumpul_ha, $v['id_hak_akses']);
						}

						
						$session = [
							'login' 		=> true,
							'id_user' 		=> $user['id_user'],
							'full_name' 	=> $user['nama'],
							'id_hak_akses' 	=> $kumpul_ha,
							'jabatan' 	=> $user['jabatan'],
							
						
							// 'is_active'	=> $user['is_active'],
							'id_session' 	=> session_id()
						];
						if ($q_ha->num_rows()==0) {
						$this->session->set_flashdata('pesan','<div class="alert alert-danger"> Hak akses tidak ada<br>Silahkan hubungi admin untuk memberikan hak akses.!
	                </div>');
						$redirect = '/auth/login';

						}else{
						$this->session->set_userdata($session);
						$this->session->set_flashdata('pesan','<div class="alert alert-success"> Login Berhasil.!
	                </div>');
						$redirect = '/user/user/dashboard';
						}
						// if ($user['id_hak_akses']==1) {
						// 	$redirect = '/user/master/dashboard';
						// 	# code...
						// }else if ($user['id_hak_akses']==2) {
						// 	$redirect = '/user/admin/dashboard';

						// }else if ($user['id_hak_akses']==3) {
						// 	$redirect = '/user/gro/dashboard';

						// }else if ($user['id_hak_akses']==4) {
						// 	$redirect = '/user/gym/dashboard';

						// }else if ($user['id_hak_akses']==5) {
						// 	$redirect = '/user/office/dashboard';

						// }else if ($user['id_hak_akses']==6) {
						// 	$redirect = '/user/swimming/dashboard';

						// }else{
						// 	$redirect = '/user/gro/dashboard';

						// }

					} else {
						// $this->attempts($post['email']);
						$this->session->set_flashdata('pesan','<div class="alert alert-danger"> Password Salah.!
	                      
	                    </div>');
						$redirect = '/auth/login';
					}
				} else {
					$redirect = '/auth/login';
					$this->session->set_flashdata('pesan','<div class="alert alert-danger"> User tidak aktif.!
                      
                    </div>');
				}
			} else {
				$redirect = '/auth/login';
				$this->session->set_flashdata('pesan','<div class="alert alert-danger"> Username dan Password Salah.!
                  
                </div>');
			}
				// echo $redirect;
			redirect($redirect);
	}
	

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('/');
	}
	public function kick()
	{
		$this->session->sess_destroy();
		 $this->session->set_flashdata('pesan','<div class="alert alert-danger"> Akses menu ini tidak dibolehkan <br>Silahkan login ulang
                </div>');
		redirect('auth/login');
	}
}
