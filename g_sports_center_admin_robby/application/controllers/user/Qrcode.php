<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qrcode extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
       
        
    
    }





    public function index()
    {
          $data['judul'] = 'QR Code Web GSC';
          $data['deskripsi'] = 'Untuk menampilkan QR Code yang jika di scan akan diarahkan pada link g-sportscenter.com';
          $data['file'] = 'file/qrpublic/QR_Code_WEB_GSC.png';
          $data['modal'] = '';
        $this->template->load('template/user_adminlte','user/user/qr_code/qr_code',$data);


    }

  public function download_qr()
        {
          $file = 'file/qrpublic/QR_Code_WEB_GSC.png';
          force_download($file, NULL);
        }
}
