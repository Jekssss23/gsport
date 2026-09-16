<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {


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





    public function index()
    {
        $data['judul'] = 'Wellcome di halaman Admin';
        $data['deskripsi'] = 'Wellcome di halaman Admin';
        $id_user = id_user();
        $user = data_user($id_user);
        $data['user'] = $user;

    
        $data['modal']                      = '';
        $this->template->load('template/user_adminlte','user/admin/dashboard/dashboard', $data);


    }

}
