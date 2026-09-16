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
        $id_user = id_user();
        $user = data_user($id_user);
        $data['judul'] = 'Welcome di halaman User '. $user['nama'];
        $data['deskripsi'] = 'Welcome di halaman User '. $user['nama'];
        $data['user'] = $user;

        $kumpul_ha= [];
        foreach (list_hak_akses() as $k => $v) {
            $kumpul_ha[$v['id_hak_akses']] = $v['nama_hak_akses'];
        }
    
        $data['hak_akses']                      = $kumpul_ha;
        $data['modal']                      = '';
        $this->template->load('template/user_adminlte','user/user/dashboard/dashboard', $data);
    }

}
