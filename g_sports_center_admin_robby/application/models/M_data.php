<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_data extends CI_Model{
    function data($tabel,$number,$offset){
        // $tabel = 'dokumen_publik';?
        return $query = $this->db->get($tabel,$number,$offset)->result_array();       
    }
 
    function jumlah_data($tabel){
        return $this->db->get($tabel)->num_rows();
    }
}


  
