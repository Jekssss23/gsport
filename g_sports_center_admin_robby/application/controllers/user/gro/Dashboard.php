<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
       
        $id_hak_akses = $this->session->userdata('id_hak_akses'); 
        //   if ($id_hak_akses!='3') {
        //     redirect('auth/login/kick');
           
        // }
    }




    public function index()
    {
        $data['judul'] = 'Welcome di halaman GRO';
        $data['deskripsi'] = 'Welcome di halaman GRO';
        $id_user = id_user();
        $user = data_user($id_user);
        $data['user'] = $user;

    
        $data['modal']                      = '';
        $this->template->load('template/user_adminlte','user/gro/dashboard/dashboard', $data);


    }

}
