<?php

/**
 * Author     : Alfikri, M.Kom
 * Created By : Alfikri, M.Kom
 * License    : Pemerintahan Provinsi Sumatera Barat
 * Class 	  : global_function_helper.php
 */
defined('BASEPATH') or exit('No direct script access allowed');

/* CI Get Instance */
function CI()
{
	$CI = &get_instance();
	return $CI;
}


function enkripsi($string, $action = 'E')
{
	$secret_key = 'my_simple_secret_key';
	$secret_iv = 'my_simple_secret_iv';

	$output = false;
	$encrypt_method = "AES-256-CBC";
	$key = hash('sha256', $secret_key);
	$iv = substr(hash('sha256', $secret_iv), 0, 16);

	if ($action == 'E') {
		$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
	} else if ($action == 'D') {
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}

	return $output;
}

function bulan_global($x)
{
	$bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	return $bulan[$x];
}

function metode_pembayaran()
{
	
        $mp = CI()->db->query("SELECT id_metode_pembayaran, metode_pembayaran from master_metode_pembayaran where status = '1'")->result_array();

	return $mp;
}
function diskon()
{
	
        $mp = CI()->db->query("SELECT  id_diskon, kategori,jenis_potongan,nama_diskon,besar_diskon  from master_diskon where status = '1'")->result_array();

	return $mp;
}
function student_card()
{
        $sc = CI()->db->query("SELECT  jenis_potongan, diskon, harga, masa_aktif, satuan_masa_aktif  from master_student_card where status = '1'")->result_array();
        
	return json_encode($sc);
}


function show_tanggal($tgl){
	$pecah = explode('-', $tgl);
	$show = $pecah[2].' '.nama_bulan($pecah[1]).' '.$pecah[0];
	return $show;
}
function balikkan_tanggal($tgl){
	$pecah = explode('-', $tgl);
	$show = $pecah[2].'-'.$pecah[1].'-'.$pecah[0];
	return $show;
}

function show_tanggal_tgll($tgl){
	$pecah = explode('-', $tgl);
	$show = $pecah[2].' '.bulan_global($pecah[1]).' '.$pecah[0];
	return $show;
}
function nama_bulan($x)
{
	$bulan = array(
	'01'=>'Januari',
	'02'=> 'Februari',
	'03'=> 'Maret',
	'04'=> 'April',
	'05'=> 'Mei',
	'06'=> 'Juni',
	'07'=> 'Juli',
	'08'=> 'Agustus',
	'09'=> 'September',
	'10'=> 'Oktober',
	'11'=> 'November',
	'12'=> 'Desember');
	return $bulan[$x];
}


function jml_hari_dalam_bulan($bulan, $tahun)
{
	$kalender = CAL_GREGORIAN;
	$jml_hari = cal_days_in_month($kalender, $bulan, $tahun);
	return $jml_hari;
}
function timestamp()
{
	$tgls = date('Y-m-d H:i:s');
	return $tgls;
}
function tgls()
{
	$tgls = date('Y-m-d');
	return $tgls;
}

function jams()
{
	$jams = date('H:i');
	return $jams;
}

function id_user()
{
	// return 1;
	return CI()->session->userdata('id_user');
}

function data_user($id_user){
		 $sc = CI()->db->query("SELECT id_user, id_hak_akses, nama, alamat, nohp, email,jabatan, username,password, status, status_akses , foto from master_user where id_user='$id_user'")->row_array();
		 return $sc ; 
}

function slug($text)
{
	// replace non letter or digits by -
	$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
	// trim
	$text = trim($text, '-');
	// transliterate
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	// Upper Case
	$text = strtoupper($text);
	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);

	if (empty($text)) {
		return 'n-a';
	}

	return $text;
}


function next_order(){
	  $tgls = date('Y-m-d');
     $q_cek_order_ke = CI()->db->query("SELECT max(order_ke) as order_terakhir from transaksi where tgl_transaksi='$tgls'")->row_array();
     $order_ke = $q_cek_order_ke['order_terakhir'] == '' ? 0 : $q_cek_order_ke['order_terakhir'];
     $next_order = $order_ke +1;	
     return $next_order;


}
function next_order_fasilitas($fasilitas){
	  $tgls = date('Y-m-d');
     $q_cek_order_ke = CI()->db->query("SELECT max(order_ke_fasilitas) as order_terakhir from transaksi where tgl_transaksi='$tgls' and fasilitas='$fasilitas'")->row_array();
     $order_ke = $q_cek_order_ke['order_terakhir'] == '' ? 0 : $q_cek_order_ke['order_terakhir'];
     $next_order = $order_ke +1;
     return $next_order;


}
function next_order_outlet($outlet){
	  $tgls = date('Y-m-d');
     $q_cek_order_ke = CI()->db->query("SELECT max(order_ke_fasilitas) as order_terakhir from transaksi where tgl_transaksi='$tgls' and fitur='$outlet'")->row_array();
     $order_ke = $q_cek_order_ke['order_terakhir'] == '' ? 0 : $q_cek_order_ke['order_terakhir'];
     $next_order = $order_ke +1;
     return $next_order;


}
function list_hak_akses(){
      $ha = CI()->db->query("SELECT id_hak_akses, nama_hak_akses from hak_akses")->result_array();

	return $ha;


}
