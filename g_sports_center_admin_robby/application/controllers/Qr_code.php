<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qr_code extends CI_Controller {

	public function index()
	{
		
        $q = $this->db->query("SELECT id_jenis_member, jenis_member,biaya,masa_aktif,satuan_masa_aktif,status,  wajib_register  from master_gym ")->result_array();
        $q_gym_spesial  = $this->db->query("SELECT id_jenis_member, jenis_member,harga_gym, harga_swim,masa_aktif,satuan_masa_aktif,status  from master_gym_spesial ")->result_array();
            
        $q_galeri  = $this->db->query("SELECT id_foto_galery, file, status from foto_galery where fasilitas='Gym' ")->result_array();

        $data['galerry'] = $q_galeri;
        $data['jenis_member'] = $q;
        $data['jenis_member_spesial'] = $q_gym_spesial;
        $this->template->load('template/homepage','home/gym/gym' , $data);
	}
	public function register()
	{
		               $q = $this->db->query("SELECT *
         from master_gym where status='1'")->result_array();
              
        $data['jenis_member'] = $q;
        $this->template->load('template/homepage','home/gym/register', $data);
	}

function qr_create($url = "")
{
    


                     $this->load->library('ciqrcode'); //pemanggilan library QR CODE
 
                $config['cacheable']    = true; //boolean, the default is true
                $config['cachedir']             = './assets/'; //string, the default is application/cache/
                $config['errorlog']             = './assets/'; //string, the default is application/logs/
                $config['imagedir']             = './file/qrpublic/'; //direktori penyimpanan qr code
                $config['quality']              = true; //boolean, the default is true
                $config['size']                 = '1024'; //interger, the default is 1024
                $config['black']                = array(224,255,255); // array, default is array(255,255,255)
                $config['white']                = array(70,130,180); // array, default is array(0,0,0)
                $this->ciqrcode->initialize($config);
 
                $image_name=date("Ymdhis").'.png'; //buat name dari qr code sesuai dengan nim
 
                $params['data'] = $url == '' ? "https://g-sportscenter.com/" : $url.'/g_sports_center'; //data yang akan di jadikan QR CODE
                $params['level'] = 'H'; //H=High
                $params['size'] = 10;
                $params['savename'] = FCPATH.$config['imagedir'].$image_name; //simpan image QR CODE ke folder assets/images/
                $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

                redirect('qr_code/qr_read/'.$image_name);




}
function qr_read($image)
{


            $this->session->set_flashdata('pesan','<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Transaksi member disimpan</div>');
            $data['image'] = $image;
        $this->template->load('template/homepage','home/qr_public', $data);
}

}
