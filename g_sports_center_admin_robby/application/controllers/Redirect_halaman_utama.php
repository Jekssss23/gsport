<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Redirect_halaman_utama extends CI_Controller {

	public function index()
	{
		redirect('auth/login');
	}
}
