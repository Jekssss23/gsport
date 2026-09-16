<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Open_file extends CI_Controller {

	public function index()
	{
		
		$path = "E:\\laragon\www\g_sports_center\application";
		exec("EXPLORER /E,$path");

	}
}
