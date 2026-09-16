<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct(){
		date_default_timezone_set('Asia/Jakarta');
		parent:: __construct();
		$this->load->model('admin_query');
	  

		if($this->session->userdata('status') != "login"){
			redirect('home');
		}
	}


	public function index()
	{
		// $this->load->view('admin/template');
		$bulanini = date('m');
		$tahunini = date('Y');
		$data['timeline_bulan_ini']=$this->db->query("SELECT kegiatan from timeline where status='0' and month(deadline)='$bulanini' and year(deadline)='$tahunini'")->num_rows();
		$qproject = "SELECT project.*, bidang.nama_bidang, client.nama_client, client.jenis_client from project 
			left join bidang on project.bidang=bidang.id_bidang
			left join client on project.id_project = client.id_project
			where project.status in ('Menunggu Data','On Progress','Pending')";
		$data['list_project']  = $this->perintah->query($qproject);
		$data['project_active']= $this->perintah->num_rows($qproject);
		$data['agenda_active']=$this->db->get_where('kegiatan',['status'=>''])->num_rows();
		$data['timeline_all']=$this->db->query("SELECT * from timeline t left join project p on t.id_project = p.id_project where t.status='0'")->num_rows();
		$data['list_timeline'] = $this->perintah->query("SELECT 
			t.jenis_aktivitas, t.kegiatan, t.deadline, t.rencana_pelaksanaan, t.deskripsi_kegiatan, t.id_project, t.status, t.id,
			p.nama_project, p.lokasi_project,
			c.nama_client
		 from timeline t
		 left join project p on t.id_project = p.id_project
		 left join client c on t.id_project = c.id_project
		  where t.status='0' order by deadline asc ");

		$thn = date('Y');
		$bulan = date('m');


		$data['list_timeline_bulan_ini'] = $this->perintah->query("SELECT 
			t.jenis_aktivitas, t.kegiatan, t.deadline, t.rencana_pelaksanaan, t.deskripsi_kegiatan, t.id_project, t.status, t.id,
			p.nama_project, p.lokasi_project,
			c.nama_client
		 from timeline t
		 left join project p on t.id_project = p.id_project
		 left join client c on t.id_project = c.id_project
		  where t.status='0' and year(t.deadline)='$thn' and month(t.deadline)='$bulan' order by deadline asc ");
		$data['judul'] = 'Selamat Datang ';
		$this->template->load('admin/template','admin/form/dashboard/dashboard', $data);
	}
	public function timeline()
	{
		  $namabulan = array(
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
        ); 

		if (isset($_GET['bulan']) && isset($_GET['tahun'])) {
			$bulanini = $_GET['bulan'];
			$tahunini = $_GET['tahun'];
			$query_timeline = "SELECT 
			t.jenis_aktivitas, t.kegiatan, t.deadline, t.rencana_pelaksanaan, t.deskripsi_kegiatan, t.id_project, t.status, t.id,
			p.nama_project, p.lokasi_project,
			c.nama_client
		 from timeline t
		 left join project p on t.id_project = p.id_project
		 left join client c on t.id_project = c.id_project
		  where month(t.deadline)='$bulanini' and year(t.deadline)='$tahunini'
		   order by status, deadline asc ";
		   $query_timeline_selesai = "SELECT 
				t.id
			 from timeline t
			 where t.status='1' and month(t.deadline)='$bulanini' and year(t.deadline)='$tahunini'
			";
			  $caption_timeline = $namabulan[$bulanini].' '.$tahunini;
			  $bulan = $bulanini;
			  $tahun = $tahunini;
			  $caption_list_timeline = 'Timeline List <br>'.$caption_timeline;
			
		}else{
			
			$query_timeline = "SELECT 
				t.jenis_aktivitas, t.kegiatan, t.deadline, t.rencana_pelaksanaan, t.deskripsi_kegiatan, t.id_project, t.status, t.id,
				p.nama_project, p.lokasi_project,
				c.nama_client
			 from timeline t
			 left join project p on t.id_project = p.id_project
			 left join client c on t.id_project = c.id_project
			   order by status,deadline asc ";
			$query_timeline_selesai = "SELECT 
				t.id
			 from timeline t
			 where status='1'
			";
			  $caption_timeline = "Semua Data";
			  $bulan = '';
			  $tahun = '';
			  $caption_list_timeline = 'Semua Data Timeline List';

		}
		$data['list_timeline'] = $this->perintah->query($query_timeline);
		$data['jumlah_timeline'] = $this->perintah->num_rows($query_timeline);
		$data['caption_timeline'] = $caption_timeline;
		$data['caption_list_timeline'] = $caption_list_timeline;
		$data['timeline_selesai'] = $this->perintah->num_rows($query_timeline_selesai);
		$data['bulan'] = $bulan;
		$data['tahun'] = $tahun;

		$data['timeline_all']=$this->db->get_where('timeline',['status'=>'0'])->num_rows();

		$data['judul'] = 'Timeline';
		$this->template->load('admin/template','admin/form/dashboard/timeline', $data);
	}
	public function kalender()
	{
		// $this->load->view('admin/template');
		
		$data['judul'] = '';
		$this->template->load('admin/template','admin/form/kalender/kalender', $data);
	}
	public function portofolio()
	{
		$data['menu'] = '
		<li><a href="#" id="menu">Portofolio</a></li>
		<li><a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#addproject" >Tambah Portofolio</a></li>';
		$data['data'] = $this->perintah->query("SELECT project.*, bidang.nama_bidang, client.nama_client, client.jenis_client, client.kode_client from project 
			left join bidang on project.bidang=bidang.id_bidang
			left join client on project.id_project = client.id_project");
		$data['judul'] = "Data Portofolio";
		$data['bidang']= $this->perintah->get('bidang');
		$data['action_portofolio'] = $this->perintah->get('action_portofolio');
		$this->template->load('admin/template','admin/form/portofolio/data_portofolio', $data);
	}
	public function planning()
	{
		$data['menu'] = '
		<li><a href="#" id="menu">Planning</a></li>
		<li><a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#addplanning" >Tambah Planning</a></li>';
		$data['data'] = $this->perintah->query("SELECT * from planning");
		$data['fitur_plan'] = $this->perintah->query("SELECT * from fitur_planning");
		$data['judul'] = "Data Planning";
		$this->template->load('admin/template','admin/form/planning/data_planning', $data);
	}
	public function schedule()
	{
		$data['menu'] = '
		<li><a href="#" id="menu">Time Schedule</a></li>
		<li><a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#addplanning" >Tambah Schedule	</a></li>';
		$data['data'] = $this->perintah->query("SELECT * from time_schedule ts left join planning p on ts.id_planning=p.id_planning order by ts.hari, ts.jam_mulai asc");
		$hariini = date('N') +1;
		$data['data_hari_ini'] = $this->perintah->query("SELECT * from time_schedule ts left join planning p on ts.id_planning=p.id_planning where hari='$hariini' order by ts.hari, ts.jam_mulai asc");
		$data['planning'] = $this->perintah->query("SELECT * from planning");
		$data['judul'] = "Time Schedule ";
		$this->template->load('admin/template','admin/form/time_schedule/index', $data);
	}
	public function add_note()
	{
		$data['menu'] = '
		<li><a href="#" id="menu">Tambah Note</a></li>
		<li><a href="'.base_url('admin/note').'" class="btn btn-info btn-xs">Kembali</a></li>';

		$data['judul'] = "Tambah Note ";
		$this->template->load('admin/template','admin/form/note/add', $data);
	}
	public function edit_note($id)
	{
		$data['menu'] = '
		<li><a href="#" id="menu">Edit Note</a></li>
		<li><a href="'.base_url('admin/note').'" class="btn btn-info btn-xs">Kembali</a></li>';
		$data['note'] = $this->perintah->get_where_1_rows('note',['id_note'=>$id]);
		$data['judul'] = "Edit Note ";
		$this->template->load('admin/template','admin/form/note/edit', $data);
	}
	public function detail_note($id)
	{
		$note = $this->perintah->get_where_1_rows('note',['id_note'=>$id]);
		$data['note'] = $note;
		$data['menu'] = '
		<li><a href="#" id="menu">'.$note['judul'].'</a></li>
		<li style="color:whie">
		<a href="'.base_url('admin/note').'" class="btn btn-info btn-xs" style="color:white">Kembali</a> 
		<a href="'.base_url('admin/delete_note/'.$note['id_note']).'" class="btn btn-danger btn-xs" style="color:white" data-toggle="tooltip" title="Hapus Note" onclick="return confirm('."'Hapus Note.?"."'".')">Hapus Note</a>
		<a href="'.base_url('admin/edit_note/'.$note['id_note']).'" class="btn btn-warning btn-xs" style="color:white" data-toggle="tooltip" title="Edit Note" >Edit Note</a>
		</li>
		';
		$data['judul'] = "Detail Note ";
		$this->template->load('admin/template','admin/form/note/detail', $data);
	}
	public function keuangan()
	{
		$data['link_redirect'] = 'sekarang';
		$bulanini = date('m');
		$tahunini = date('Y');
		$data['data'] = $this->perintah->query("SELECT * from keuangan where month(tgl)='$bulanini' and year(tgl)='$tahunini' order by tgl desc, jam desc");
		$data['judul'] = "Keuangan ";
		$data['menu'] = '
		<li><a href="#" id="menu">Keuangan Bulan '.$this->nama_bulan($bulanini).' Tahun '.$tahunini.'</a></li>
		<li>
		<a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#addkeuangan" style="color:white" >Tambah Catatan Keuangan</a>
		<a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#filter_bulanan" style="color:white" >Filter By Bulan</a>
		<a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#filter_harian" style="color:white" >Filter By Tanggal</a>
		<a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#filter_rentang_waktu" style="color:white" >Filter By Rentang Waktu</a>
		<a href="'.base_url('admin/filter_keuangan/semua').'" class="btn btn-info btn-xs" style="color:white" >Semua Data</a>
		</li>';
		
		$this->template->load('admin/template','admin/form/keuangan/index', $data);
	}

	public function nama_bulan($bulan){
		$nama_bulan = [
		    '01'=>'January',
		    '02'=>'February',
		    '03'=>'Maret',
		    '04'=>'April',
		    '05'=>'Mei',
		    '06'=>'Juni',
		    '07'=>'Juli',
		    '08'=>'Agustus',
		    '09'=>'September',
		    '10'=>'Oktober',
		    '11'=>'November',
		    '12'=>'Desember'
		  ];
		  return $nama_bulan[$bulan];
	}
	public function filter_keuangan($filter)
	{
		$data['filter'] = $filter;
		
		if ($filter=='bulanan') {
			$bulan = $this->input->get('bulan');
			$tahun = $this->input->get('tahun');
			$caption_filter = 'Filter keuangan bulan '.$this->nama_bulan($bulan).' '.$tahun;
			$query = $this->perintah->query("SELECT * from keuangan where month(tgl)='$bulan' and year(tgl)='$tahun' order by tgl desc, jam desc");
			$data['link_redirect'] = $filter.'?bulan='.$bulan.'&tahun='.$tahun;
		}
		elseif ($filter=='harian') {
			$tgl = $this->input->get('tgl_filter');
			$caption_filter = 'Filter keuangan tanggal '.$tgl;
			$query = $this->perintah->query("SELECT * from keuangan where tgl='$tgl' order by tgl desc, jam desc");
			$data['link_redirect'] = $filter.'?tgl_filter='.$tgl;
		}
		elseif ($filter=='rentang_waktu') {
			$tgl_awal = $this->input->get('tgl_awal');
			$tgl_akhir = $this->input->get('tgl_akhir');
			$caption_filter = 'Filter keuangan dari tanggal '.$tgl_awal.' sampai '.$tgl_akhir;
			$query = $this->perintah->query("SELECT * from keuangan where tgl between '$tgl_awal' and '$tgl_akhir' order by tgl desc, jam desc");
			$data['link_redirect'] = $filter.'?tgl_awal='.$tgl_awal.'&tgl_akhir='.$tgl_akhir;
		}
		else{ 
			
			$caption_filter = 'Semua Data Keuangan';
			$query = $this->perintah->query("SELECT * from keuangan order by tgl desc, jam desc");
			$data['link_redirect'] = $filter;
		}
		$data['data'] = $query;
		$hariini = date('N') +1;
		$data['judul'] = "Keuangan ";
		$data['menu'] = '
		<li><a href="#" id="menu">'.$caption_filter.'</a></li>
		<li>
		<a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#addkeuangan" style="color:white" >Tambah Catatan Keuangan</a>
		<a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#filter_bulanan" style="color:white" >Filter By Bulan</a>
		<a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#filter_harian" style="color:white" >Filter By Tanggal</a>
		<a href="#" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#filter_rentang_waktu" style="color:white" >Filter By Rentang Waktu</a>
		<a href="'.base_url('admin/filter_keuangan/semua').'" class="btn btn-info btn-xs" style="color:white" >Semua Data</a>
		</li>';
		$this->template->load('admin/template','admin/form/keuangan/index', $data);
	}


	public function detail_planning($id)
	{
		$tgls = date('Y-m-d');
		$data['judul'] = 'Detail Planning';
		$data['data'] = $this->perintah->query_1_rows("SELECT * from planning where id_planning='$id'");
		$data['fitur_plan'] = $this->perintah->query("SELECT * from fitur_planning");
		$data['rab'] = $this->perintah->query("SELECT * from rab_planning where id_planning = '$id' order by status asc");
		$data['target'] = $this->perintah->query("SELECT * from target_planning where id_planning = '$id' order by status asc");
		$data['rules'] = $this->perintah->query("SELECT * from rules_planning where id_planning = '$id'");
		$data['aktivitas'] = $this->perintah->query("SELECT * from aktivitas_planning where id_planning = '$id' order by tgl, jam asc");
		$data['tdl'] = $this->perintah->query("SELECT * from to_do_list_planning where id_planning = '$id' order by status, rencana_pelaksanaan asc");
		$data['aktivitas_hariini'] = $this->perintah->query("SELECT * from aktivitas_planning where id_planning = '$id' and tgl='$tgls' order by tgl, jam asc");


		$hariini = date('N') +1;
		$query_schedule = "SELECT * from time_schedule  where id_planning='$id' and  hari='$hariini' order by jam_mulai asc";
		$data['schedule'] = $this->perintah->query($query_schedule);
		$data['banyak_schedule'] = $this->perintah->num_rows($query_schedule);
		

		$this->template->load('admin/template','admin/form/planning/detail_planning', $data);
	}
	public function save_portofolio()
	{	
		$action = implode(',', $this->input->post('action'));
		$isi=[
			'nama_project'=>$this->input->post('np'),
			'jenis_project'=>$this->input->post('jp'),
			'keterangan_project'=>$this->input->post('kp'),
			'bidang'=>$this->input->post('bidang'),
			'biaya_project'=>$this->input->post('bp'),
			'tgl_mulai'=>date('Y-m-d'),
			'menggunakan'=>$this->input->post('m'),
			'lokasi_project'=>$this->input->post('file_project'),
			'status'=>'Belum Diketahui',
			'action'=>$action,
		

		];
		$data=$this->perintah->insert('project', $isi);



 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Portofolio ditambahkan</h4>
                Portofolio baru anda berhasil ditambahkan
              </div>');
			redirect('admin/portofolio');
	}
	public function save_planning()
	{	
		$fitur = implode(',', $this->input->post('fitur'));
		$save_fitur = $fitur ==null ? '' : $fitur;
		$isi=[
			'nama_planning'=>$this->input->post('np'),
			'keterangan'=>nl2br($this->input->post('kp')),
			'kategori'=>$this->input->post('jp'),
			'target_pencapaian'=>$this->input->post('target'),
			'fitur'=>$save_fitur,
			'status'=>'1'
			
		

		];
		$data=$this->perintah->insert('planning', $isi);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Planning ditambahkan</h4>
                Planning baru anda berhasil ditambahkan
              </div>');
			redirect('admin/planning');
	}
	public function saveedit_planning($id)
	{	
		$fitur = implode(',', $this->input->post('fitur'));
		$save_fitur = $fitur ==null ? '' : $fitur;
		$isi=[
			'nama_planning'=>$this->input->post('np'),
			'keterangan'=>nl2br($this->input->post('kp')),
			'kategori'=>$this->input->post('jp'),
			'target_pencapaian'=>$this->input->post('target'),
			'fitur' =>$save_fitur
		];
		$data=$this->perintah->update('planning', $isi, ['id_planning'=>$id]);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Planning diperbaharui</h4>
                Planning baru anda berhasil diperbaharui
              </div>');
			redirect('admin/detail_planning/'.$id);
	}
	public function save_rule($id)
	{	
		$isi=[
			'id_planning'=>$id,
			'rules'=>$this->input->post('rule'),
			'keterangan'=>nl2br($this->input->post('ket'))
		];
		$data=$this->perintah->insert('rules_planning', $isi);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Rules Disimpan</h4>
                
              </div>');
			redirect('admin/detail_planning/'.$id);
	}
	public function saveedit_rule($id, $idp)
	{	
		$isi=[
			'rules'=>$this->input->post('rule'),
			'keterangan'=>nl2br($this->input->post('ket'))
		];
		$where = ['id_rules'=>$id];
		$data=$this->perintah->update('rules_planning', $isi, $where);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Rules Duperbaharui</h4>
                
              </div>');
			redirect('admin/detail_planning/'.$idp);
	}
	public function save_target($id)
	{	
		$isi=[
			'id_planning'=>$id,
			'target'=>$this->input->post('target'),
			'tgl_target'=>$this->input->post('goal'),
			'keterangan'=>nl2br($this->input->post('ket')),
			'status'=>0
		];
		$data=$this->perintah->insert('target_planning', $isi);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Target Disimpan</h4>
                
              </div>');
			redirect('admin/detail_planning/'.$id);
	}
	public function save_rab($id)
	{	
		$isi=[
			'id_planning'=>$id,
			'keterangan'=>$this->input->post('item'),
			'perkiraan_harga'=>$this->input->post('perkiraan'),
			'status'=>0
		];
		$data=$this->perintah->insert('rab_planning', $isi);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>RAB Disimpan</h4>
                
              </div>');
			redirect('admin/detail_planning/'.$id);
	}
	public function save_aktivitas($id)
	{	
		$isi=[
			'id_planning'=>$id,
			'tgl'=>$this->input->post('tgl'),
			'jam'=>$this->input->post('jam'),
			'jenis'=>$this->input->post('jenis'),
			'keterangan '=>nl2br($this->input->post('ket'))
		];
		$data=$this->perintah->insert('aktivitas_planning', $isi);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Aktivitas Disimpan</h4>
                Aktivitas telah disimpan
                
              </div>');
			redirect('admin/detail_planning/'.$id);
	}
	public function saveedit_aktivitas($id, $idp='')
	{	
		$isi=[
			'tgl'=>$this->input->post('tgl'),
			'jam'=>$this->input->post('jam'),
			'jenis'=>$this->input->post('jenis'),
			'keterangan '=>nl2br($this->input->post('ket'))
		];
		$where = ['id_aktivitas'=>$id];
		$data=$this->perintah->update('aktivitas_planning', $isi, $where);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Aktivitas Disimpan</h4>
                Aktivitas telah disimpan
                
              </div>');
			redirect('admin/detail_planning/'.$idp);
	}
	public function saveedit_tdl($id, $idp='')
	{	
		$isi=[
			'nama_tdl'=>$this->input->post('tdl'),
			'keterangan'=>$this->input->post('ket'),
			'rencana_pelaksanaan'=>$this->input->post('tgl')
		];
		$where = ['id_tdl'=>$id];
		$data=$this->perintah->update('to_do_list_planning', $isi, $where);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                
                To do list diperbaharui
                
              </div>');
			redirect('admin/detail_planning/'.$idp);
	}
	public function save_tdl($id)
	{	
		$isi=[
			'id_planning'=>$id,
			'nama_tdl'=>$this->input->post('tdl'),
			'keterangan'=>nl2br($this->input->post('ket')),
			'rencana_pelaksanaan'=>$this->input->post('tgl'),
			'status'=>0
		];
		$data=$this->perintah->insert('to_do_list_planning', $isi);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>To Do List Disimpan</h4>
                To Do List telah disimpan
                
              </div>');
			redirect('admin/detail_planning/'.$id);
	}
	public function saveedit_portofolio($id)
	{	

		$action = implode(',', $this->input->post('action'));
		$isi=[
			'nama_project'=>$this->input->post('np'),
			'jenis_project'=>$this->input->post('jp'),
			'keterangan_project'=>$this->input->post('kp'),
			'bidang'=>$this->input->post('bidang'),
			'biaya_project'=>$this->input->post('bp'),
			
			'menggunakan'=>$this->input->post('m'),
			'lokasi_project'=>$this->input->post('file_project'),
			'action'=>$action,
			
		

		];
		$data=$this->perintah->update('project', $isi,['id_project'=>$id]);



 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Portofolio ditambahkan</h4>
                Portofolio baru anda berhasil ditambahkan
              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
	public function list_portofolio()
	{
		$data['menu'] = '
		<li><a href="#" id="menu">Portofolio</a></li>
		<li><a href="" class="btn btn-primary btn-xs"  data-toggle="modal" data-target="#openmodal" id="addportofolio" >Tambah Portofolio</a></li>';
		$data['data'] = $this->perintah->query("SELECT project.*, bidang.*, client.nama_client from project 
			left join bidang on project.bidang=bidang.id_bidang
			left join client on project.id_project = client.id_project");
		$this->load->view('admin/form/portofolio/data_portofolio', $data);
	}
	public function detail_portofolio($id)
	{
		$data['judul'] = 'Detail Portofolio';
		$data['data'] = $this->perintah->query_1_rows("SELECT project.*, bidang.*, client.nama_client, client.jenis_client, client.asal, client.nama_alias, client.kode_client, client.kode_akses from project left join bidang on project.bidang=bidang.id_bidang
		left join client on project.id_project = client.id_project where project.id_project='$id'");
		$idproject = $data['data']['id_project'];

	$data['bidang']= $this->perintah->get('bidang');

		$data['hosting'] = $this->perintah->get_where_1_rows('data_hosting_web', ['id_project'=>$idproject]);
		$data['client'] = $this->perintah->get_where_1_rows('client', ['id_project'=>$idproject]);


		$data['file_support'] = $this->perintah->get_where('file_support_project', ['id_project'=>$idproject]);
		$data['fitur'] = $this->perintah->get_where('fitur_project', ['id_project'=>$idproject]);
		$data['jobdesk'] = $this->perintah->get_where('jobdesk', ['id_project'=>$idproject]);
		$data['dokumentasi'] = $this->perintah->get_where('dokumentasi', ['id_project'=>$idproject]);
		$data['gambars'] = $this->db->get_where('gambar_project', ['id_project'=>$idproject])->result_array();
		$data['jgambars'] = $this->db->get_where('gambar_project', ['id_project'=>$idproject])->num_rows();
		$data['timeline'] = $this->perintah->query("SELECT * from timeline where id_project='$idproject' order by status, deadline asc");
		$data['timeline_aktif'] = $this->perintah->num_rows("SELECT id from timeline where id_project='$idproject' and status='0'");
		$data['timeline_selesai'] = $this->perintah->num_rows("SELECT id from timeline where id_project='$idproject' and status='1'");
		$data['timeline_total'] = $this->perintah->num_rows("SELECT id from timeline where id_project='$idproject' ");
		$data['aktivitas'] = $this->perintah->get_where('aktivitas_project', ['id_project'=>$idproject]);
		$data['deadline'] = $this->perintah->get_where_1_rows('deadline', ['id_project'=>$idproject]);
		$data['action_portofolio'] = $this->perintah->get('action_portofolio');

		$data['pembayaran']= $this->admin_query->data_pembayaran($id);
		$data['totalbayar']= $this->admin_query->totalbayar($id);



		$data['pembayaran'] = $this->perintah->get_where('pembayaran', ['id_project'=>$idproject]);

		$this->template->load('admin/template','admin/form/portofolio/detail_portofolio', $data);
		// $this->load->view('admin/form/portofolio/detail_portofolio', $data);
	}
public function lihat_bukti($id)
	{	
 			$data = $this->db->get_where('pembayaran', ['id_pembayaran'=>$id])->row_array();
 			echo json_encode($data);
 			
	}
public function detail_kegiatan($id)
	{	
 			$data = $this->db->get_where('kegiatan', ['id_kegiatan'=>$id])->row_array();
 			echo json_encode($data);
 			
	}
public function data_kalender()
	{	
 		$q_deadline = $this->perintah->query("SELECT deadline, COUNT(deadline) jml_data FROM timeline GROUP BY deadline HAVING COUNT(jml_data)  > 0");
 		$q_planning = $this->perintah->query("SELECT rencana_pelaksanaan, COUNT(rencana_pelaksanaan) jml_data FROM timeline GROUP BY rencana_pelaksanaan HAVING COUNT(jml_data)  > 0");

 		$kumpulkan_jadwal = [];
 		foreach ($q_deadline as  $value) {
 			$data = [
 			 'kategori' =>'deadline',
 			 'start' =>$value['deadline'],
 			 'allDay' =>true,
 			 'title' =>$value['jml_data'].' Deadline Target Timeline',
 			 'backgroundColor'=>'00c0ef',
 			 'borderColor'=>'0073b7',
 			];
 			array_push($kumpulkan_jadwal, $data);
 		}
 		foreach ($q_planning as  $value) {
 			$data = [
 			 'kategori' =>'rencana_pelaksanaan',
 			 'start' =>$value['rencana_pelaksanaan'],
 			 'allDay' =>true,
 			 'title' =>$value['jml_data'].' Rencana Kegiatan Timeline',
 			 'backgroundColor'=>'00c0ef',
 			 'borderColor'=>'00c0ef',
 			];
 			array_push($kumpulkan_jadwal, $data);
 		}
 		header('Content-Type: application/json');
 			echo json_encode($kumpulkan_jadwal);
 			
	}
public function kalender_aktivitas_planning($id_planning)
	{	
 		$q_aktivitas = $this->perintah->query("SELECT tgl, COUNT(tgl) jml_data FROM aktivitas_planning where id_planning='$id_planning' GROUP BY tgl HAVING COUNT(tgl)  > 0");
 	
 		$kumpulkan_jadwal = [];
 		foreach ($q_aktivitas as  $value) {
 			$data = [
 			 'kategori' =>'aktivitas',
 			 'start' =>$value['tgl'],
 			 'allDay' =>true,
 			 'title' =>$value['jml_data'].' Aktivitas',
 			 'backgroundColor'=>'00c0ef',
 			 'borderColor'=>'0073b7',
 			];
 			array_push($kumpulkan_jadwal, $data);
 		}

 		header('Content-Type: application/json');
 			echo json_encode($kumpulkan_jadwal);
 			
	}
public function detail_kalender_aktivitas()
	{	
		
		$tanggal = $this->input->post('tanggal');
		$id = $this->input->post('id_planning');
		$q = $this->perintah->query("SELECT * from aktivitas_planning where id_planning = '$id' and tgl='$tanggal' order by tgl, jam asc");


		
 		
 		$output= [
 			'data' => [],
 			'cek' =>$id
 			
 		];

 		
 		foreach ($q as  $value) {
 			
 			$data = [
 			'kategori'=>$value['jenis'],
 			'keterangan'=>$value['keterangan'],
 			'jam'=>$value['jam'],
 			
 			
 			];
 			array_push($output['data'], $data);
 		}
 		
 		
 			echo json_encode($output);
 			
	}
public function detail_kalender()
	{	
		$kategori = $this->input->post('kategori');
		$tanggal = $this->input->post('tanggal');

		$q = $this->perintah->query("SELECT 
			t.jenis_aktivitas, t.kegiatan, t.$kategori, t.deskripsi_kegiatan, t.id_project, t.status, t.id,
			p.nama_project, p.lokasi_project,
			c.nama_client
		 from timeline t
		 left join project p on t.id_project = p.id_project
		 left join client c on t.id_project = c.id_project
		  where t.$kategori='$tanggal' order by $kategori asc ");
 		
 		$output= [
 			'data' => [],
 			'kategori'=>'',
 			'tanggal'=>''
 		];

 		$tgls = strtotime(date('Y-m-d'));
 		foreach ($q as  $value) {
 			$kategori_klik = $value[$kategori];
 			$jadwal = strtotime($kategori_klik);

 			if ($jadwal  < $tgls) {
		        if ($value['status']==0) {
		          $style = 'color:red';
		        }else{
		          $style = 'color:green';
		        }
		      }else{
		        if ($value['status']==0) {
		          $style = 'color:black';
		        }else{
		          $style = 'color:green';
		        }
		      }

 			$warna = $style;
 			$data = [
 			'project'=>$value['nama_project'],
 			'client'=>$value['nama_client'],
 			'warna'=>$warna,
 			'kegiatan'=>$value['kegiatan'],
 			'keterangan'=>$value['deskripsi_kegiatan'],
 			'detail_project'=>'<a href="'.base_url().'admin/detail_portofolio/'.$value['id_project'].'" class="btn btn-info btn-xs"><i class="fa fa-folder-open"></i></a>',
 			];
 			array_push($output['data'], $data);
 		}
 		
 		
 			echo json_encode($output);
 			
	}
public function konfirmasi_kegiatan()
	{	
 			$id = $this->input->post('idkegiatan');
 			$stat = $this->input->post('tblkonfirm');
 			$this->perintah->update('kegiatan',['status'=>$stat] , ['id_kegiatan'=>$id]);
 			redirect('admin/agenda');
 			
	}

		public function simpan_hosting($id)
	{	
 			$data = [
 				'id_project'=>$id,
 				'nama_server'=>$this->input->post('ns'),
 				'jenis_server'=>$this->input->post('js'),
 				'hostname'=>$this->input->post('hn'),
 				'username'=>$this->input->post('u'),
 				'password'=>$this->input->post('p'),
 				'port'=>$this->input->post('port'),
 				'biaya'=>$this->input->post('bd'),
 				'mulai_aktif'=>$this->input->post('ma'),
 				'habis_aktif'=>$this->input->post('ha'),
 				'email_akun_hosting'=>$this->input->post('emailakun'),
 				'url'=>$this->input->post('url'),
 				'status_akun_hosting'=>$this->input->post('status')
 			];
 			$this->db->insert('data_hosting_web', $data);

 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Data hostingan telah ditambahkan</h4>

              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
		public function simpan_client($id)
	{	

			$cek_max_kode = $this->db->query("SELECT max(kode_client)as maxkode from client")->row()->maxkode;
			$kode_client_didapatkan = $cek_max_kode=='' ? 0 : $cek_max_kode;
			$kode_ok  = intval($kode_client_didapatkan) +1;
			if ($kode_ok>0 && $kode_ok <10) {
				$simpan_kode = '000'.$kode_ok;
			}
			elseif ($kode_ok>=10 && $kode_ok <100) {
				$simpan_kode = '00'.$kode_ok;
			}
			elseif ($kode_ok>=100 && $kode_ok <1000) {
				$simpan_kode = '0'.$kode_ok;
			}
			else {
				$simpan_kode = $kode_ok;
			}

			$kode_akses = crc32($kode_ok);

 			$data = [
 				'id_project'=>$id,
 				'nama_client'=>$this->input->post('nama'),
 				'asal'=>$this->input->post('asal'),
 				'alamat'=>$this->input->post('alamat'),
 				'nohp'=>$this->input->post('nohp'),
 				'jenis_client'=>$this->input->post('jenis'),
 				'nama_alias'=>$this->input->post('alias'),
 				'catatan'=>nl2br($this->input->post('catatan')),
 				'kode_client'=>$simpan_kode,
 				'kode_akses'=>$kode_akses
 			];
 			$this->db->insert('client', $data);

 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Data client telah ditambahkan</h4>

              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
		public function simpanedit_client($id)
	{	
		$cek_kode_client = $this->db->query("SELECT kode_client from client where id_project ='$id'")->row()->kode_client;
		if ($cek_kode_client=='') {
				
			$cek_max_kode = $this->db->query("SELECT max(kode_client)as maxkode from client")->row()->maxkode;
			$kode_client_didapatkan = $cek_max_kode=='' ? 0 : $cek_max_kode;
			$kode_ok  = intval($kode_client_didapatkan) +1;
			if ($kode_ok>0 && $kode_ok <10) {
				$simpan_kode = '000'.$kode_ok;
			}
			elseif ($kode_ok>=10 && $kode_ok <100) {
				$simpan_kode = '00'.$kode_ok;
			}
			elseif ($kode_ok>=100 && $kode_ok <1000) {
				$simpan_kode = '0'.$kode_ok;
			}
			else {
				$simpan_kode = $kode_ok;
			}

			$kode_akses = crc32($kode_ok);




 			$data = [
 				'nama_client'=>$this->input->post('nama'),
 				'asal'=>$this->input->post('asal'),
 				'alamat'=>$this->input->post('alamat'),
 				'nohp'=>$this->input->post('nohp'),
 				'jenis_client'=>$this->input->post('jenis'),
 				'nama_alias'=>$this->input->post('alias'),
 				'catatan'=>$this->input->post('catatan'),
 				'kode_client'=>$simpan_kode,
 				'kode_akses'=>$kode_akses	
 			];
		}else{
 			$data = [
 				'nama_client'=>$this->input->post('nama'),
 				'asal'=>$this->input->post('asal'),
 				'alamat'=>$this->input->post('alamat'),
 				'nohp'=>$this->input->post('nohp'),
 				'jenis_client'=>$this->input->post('jenis'),
 				'nama_alias'=>$this->input->post('alias'),
 				'catatan'=>$this->input->post('catatan')
 			];

		}
 			$this->db->update('client', $data, ['id_project'=>$id]);

 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Data client telah ditambahkan</h4>

              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
		public function simpanedit_deadline($id)
	{	
		$cek = $this->db->get_where('deadline', ['id_project'=>$id])->num_rows();





			$deadline =strtotime($this->input->post('deadline'));
			$tgls =strtotime(date('Y-m-d'));
 			if ($deadline  < $tgls) {
 				$this->session->set_flashdata('pesan','<div class="alert alert-danger alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Data deadline gagal diperbaharui</h4>
	                Anda tidak bisa memilih tanggal deadline lebih kecil dari tanggal hari ini.
	              </div>');
 			}
 			else {
 				
				if ($cek==0) {
					$data = [
		 				'id_project'=>$id,
		 				'deadline'=>$this->input->post('deadline'),
		 				'keterangan'=>nl2br($this->input->post('ket'))
		 			];
		 			$this->db->insert('deadline', $data);
				}else{
					$data = [
		 				'id_project'=>$id,
		 				'deadline'=>$this->input->post('deadline'),
		 				'keterangan'=>nl2br($this->input->post('ket'))
		 			];
		 			$this->db->update('deadline', $data, ['id_project'=>$id]);
				}
		 			
		 			

		 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
		                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		                <h4><i class="icon fa fa-check"></i>Data deadline telah diperbaharui</h4>

		              </div>');
				}




			redirect('admin/detail_portofolio/'.$id);
	}


	public function simpanedit_hosting($id)
	{	
 			$data = [
 				'id_project'=>$id,
 				'nama_server'=>$this->input->post('ns'),
 				'jenis_server'=>$this->input->post('js'),
 				'hostname'=>$this->input->post('hn'),
 				'username'=>$this->input->post('u'),
 				'password'=>$this->input->post('p'),
 				'port'=>$this->input->post('port'),
 				'biaya'=>$this->input->post('bd'),
 				
 				'mulai_aktif'=>$this->input->post('ma'),
 				'habis_aktif'=>$this->input->post('ha'),
 				'email_akun_hosting'=>$this->input->post('emailakun'),
 				'password_akun_hosting'=>$this->input->post('passakun'),
 				'url'=>$this->input->post('url'),
 				'status_akun_hosting'=>$this->input->post('status')
 			];
 			$this->db->where('id_project', $id);
 			$this->db->update('data_hosting_web', $data);

 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Data Hostingan telah diperbaharui</h4>

              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
	public function simpan_fitur($id)
	{	
 			$this->admin_query->simpan_fitur($id);

 			 $project=$this->admin_query->detail_project($id);

		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Penambahan fitur berhasil</h4>
                Anda telah manambahkan  fitur dari project '.$project["nama_project"].'
              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
	public function simpan_pembayaran($id)
	{	
		$data = [
			'id_project'=>$id,
			'tanggal_pembayaran'=>$this->input->post('tgl'),
			'keterangan_pembayaran'=>$this->input->post('ket'),
			'jumlah_pembayaran'=>$this->input->post('jml')
		];
		$this->db->insert('pembayaran', $data);
 		

 			 $project=$this->admin_query->detail_project($id);

		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Pembayaran disim;an</h4>
                Anda telah manambahkan  pembayaran dari project '.$project["nama_project"].'
              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
	public function save_keuangan($filter)
	{	
		$data = [
			'tgl'=>$this->input->post('tgl'),
			'jam'=>$this->input->post('jam'),
			'jenis'=>$this->input->post('kategori'),
			'keterangan'=>nl2br($this->input->post('ket')),
			'jumlah'=>$this->input->post('jml')
		];
		$this->db->insert('keuangan', $data);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Success</h4>
                Data keuangan telah disimpan.!
              </div>');

		if ($filter=='sekarang') {
			redirect('admin/keuangan/');
		}
		elseif ($filter=='bulanan') {
			$bulan = $this->input->get('bulan');
			$tahun = $this->input->get('tahun');
			redirect('admin/filter_keuangan/'.$filter.'?bulan='.$bulan.'&tahun='.$tahun);
		}
		elseif ($filter=='harian') {
			$tgl = $this->input->get('tgl_filter');
			redirect('admin/filter_keuangan/'.$filter.'?tgl_filter='.$tgl);
		}else if ($filter=='rentang_waktu') {
			$tgl_awal = $this->input->get('tgl_awal');
			$tgl_akhir = $this->input->get('tgl_akhir');
			redirect('admin/filter_keuangan/'.$filter.'?tgl_awal='.$tgl_awal.'&tgl_akhir='.$tgl_akhir);
		}else{
			redirect('admin/filter_keuangan/semua');
		}
	}
	public function saveedit_keuangan($id, $filter)
	{	
		$where = ['id_keuangan'=>$id];
		$data = [
			'tgl'=>$this->input->post('tgl'),
			'jam'=>$this->input->post('jam'),
			'jenis'=>$this->input->post('kategori'),
			'keterangan'=>nl2br($this->input->post('ket')),
			'jumlah'=>$this->input->post('jml')
		];
		$this->db->update('keuangan', $data, $where);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Success</h4>
                Data keuangan telah disimpan.!
              </div>');


			
		if ($filter=='sekarang') {
			redirect('admin/keuangan/');
		}
		elseif ($filter=='bulanan') {
			$bulan = $this->input->get('bulan');
			$tahun = $this->input->get('tahun');
			redirect('admin/filter_keuangan/'.$filter.'?bulan='.$bulan.'&tahun='.$tahun);
		}
		elseif ($filter=='harian') {
			$tgl = $this->input->get('tgl_filter');
			redirect('admin/filter_keuangan/'.$filter.'?tgl_filter='.$tgl);
		}else if ($filter=='rentang_waktu') {
			$tgl_awal = $this->input->get('tgl_awal');
			$tgl_akhir = $this->input->get('tgl_akhir');
			redirect('admin/filter_keuangan/'.$filter.'?tgl_awal='.$tgl_awal.'&tgl_akhir='.$tgl_akhir);
		}else{
			redirect('admin/filter_keuangan/semua');
		}
	}
	public function simpan_taks($id)
	{	
 			$data = [
 				'id_project'=>$id,
 				'jenis_aktivitas'=>$this->input->post('jenis'),
 				'kegiatan'=>$this->input->post('kegiatan'),
 				'deskripsi_kegiatan'=>nl2br($this->input->post('ket')),
 				'deadline'=>$this->input->post('target'),
 				'tgl_input'=>date('Y-m-d h:i:s'),
 				'status'=>'0'
 				];
 				$deadline =strtotime($this->input->post('target'));
 				$tgls =strtotime(date('Y-m-d'));
 			if ($deadline  < $tgls) {
 				$this->session->set_flashdata('pesan','<div class="alert alert-danger alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Penambahan timeline gagal</h4>
	                Anda tidak bisa memilih tanggal deadline lebih kecil dari tanggal hari ini.
	              </div>');
 			}else{
 				$this->perintah->insert('timeline', $data);
				$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Penambahan taks berhasil</h4>
	                Anda telah manambahkan  timeline dari project '.$project["nama_project"].'
	              </div>');
 			}
			redirect('admin/detail_portofolio/'.$id);
	}
	public function simpan_catatan_timeline($id_timeline, $id)
	{	
 			$data = [
 				'id_timeline'=>$id_timeline,
 				'id_project'=>$id,
 				'keterangan'=>nl2br($this->input->post('ket')),
 				'tgl_input'=>date('Y-m-d h:i:s'),
 				
 				];
 			
			$this->perintah->insert('catatan_timeline', $data);
			redirect('admin/detail_portofolio/'.$id);
	}
	public function save_schedule()
	{	
		$tipe = $this->input->post('kategori');
		$hari = $this->input->post('hari');
		$id_planning = $tipe=='0' ? '' : $this->input->post('planning');

		$mulai =strtotime($this->input->post('mulai'));
		$selesai =strtotime($this->input->post('selesai'));
		$tgls =strtotime(date('Y-m-d'));
 			if ($mulai  > $selesai) {
 				$this->session->set_flashdata('pesan','<div class="alert alert-danger alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-close"></i>Perbaharui Schedule gagal</h4>
	                Anda tidak bisa memilih jam selesai lebih kecil dari jam mulai .
	              </div>');
 			}
 			else{

				foreach ($hari as $v) { 
					# code...
		 			$data = [
		 				'nama_schedule'=>$this->input->post('ns'),
		 				'keterangan'=>nl2br($this->input->post('ket')),
		 				'hari'=>$v,
		 				'jam_mulai'=>$this->input->post('mulai'),
		 				'jam_selesai'=>$this->input->post('selesai'),
		 				'type'=>$tipe,
		 				'id_planning'=>$id_planning,
		 				];
 				$this->perintah->insert('time_schedule', $data);
				}
				$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Penambahan Schedule berhasil</h4>
	                Anda telah manambahkan  time schedule 
	              </div>');
 			}
			redirect('admin/schedule/');
	}

	public function saveedit_schedule($id)
	{	
		$tipe = $this->input->post('kategori');
		$id_planning = $tipe=='Bebas' ? '' : $this->input->post('planning');
 			$data = [
 				'nama_schedule'=>$this->input->post('ns'),
 				'keterangan'=>nl2br($this->input->post('ket')),
 				'hari'=>$this->input->post('hari'),
 				'jam_mulai'=>$this->input->post('mulai'),
 				'jam_selesai'=>$this->input->post('selesai'),
 				'type'=>$tipe,
 				'id_planning'=>$id_planning,
 				];
 			$where = ['id_ts'=>$id];
 				$mulai =strtotime($this->input->post('mulai'));
 				$selesai =strtotime($this->input->post('selesai'));
 				$tgls =strtotime(date('Y-m-d'));
 			if ($mulai  > $selesai) {
 				$this->session->set_flashdata('pesan','<div class="alert alert-danger alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-close"></i>Perbaharui Schedule gagal</h4>
	                Anda tidak bisa memilih jam selesai lebih kecil dari jam mulai .
	              </div>');
 			}
 			else{
 				$this->perintah->update('time_schedule', $data, $where);
				$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Schedule Diperbaharui</h4>
	                Anda telah memperbaharui  time schedule 
	              </div>');
 			}
			redirect('admin/schedule/');
	}

	public function simpanedit_taks($id_project, $id_timeline)
	{	
 			$data = [
 				'kegiatan'=>$this->input->post('kegiatan'),
 				'deskripsi_kegiatan'=>nl2br($this->input->post('ket')),
 				'deadline'=>$this->input->post('target'),
 				'tgl_input'=>date('Y-m-d h:i:s'),
 				'status'=>'0'
 				];
 				$deadline =strtotime($this->input->post('target'));
 				$tgls =strtotime(date('Y-m-d'));
 			if ($deadline  < $tgls) {
 				$this->session->set_flashdata('pesan','<div class="alert alert-danger alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Penambahan timeline gagal</h4>
	                Anda tidak bisa memilih tanggal deadline lebih kecil dari tanggal hari ini.
	              </div>');
 			}else{
 				$where = ['id'=>$id_timeline];
 				$this->perintah->update('timeline', $data, $where);
				$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Timeline Diperbaharui</h4>
	                Anda telah memperbaharui  timeline dari project '.$project["nama_project"].'
	              </div>');
 			}
			redirect('admin/detail_portofolio/'.$id_project);
	}

	public function simpanedit_dokumentasi($id_project, $id_dokumentasi)
	{	
 			$data = [
 				'dokumentasi'=>$this->input->post('dokumentasi'),
 				'keterangan'=>nl2br($this->input->post('ket'))
 				];
 				$where = ['id_dokumentasi'=>$id_dokumentasi];
 				$this->perintah->update('dokumentasi', $data, $where);
				$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Timeline Diperbaharui</h4>
	                Anda telah memperbaharui  dokumentasi dari project '.$project["nama_project"].'
	              </div>');
 			
			redirect('admin/detail_portofolio/'.$id_project);
	}
	public function simpanedit_file_support($id_project, $id_fs)
	{	
 			$data = [
 				'nama_file'=>$this->input->post('nama_file'),
 				'keterangan'=>nl2br($this->input->post('ket'))
 				];
 				$where = ['id_fs'=>$id_fs];
 				$this->perintah->update('file_support_project', $data, $where);
				$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>File Support Diperbaharui</h4>
	                Anda telah memperbaharui  file support
	              </div>');
 			
			redirect('admin/detail_portofolio/'.$id_project);
	}


	public function simpan_jobdesk($id)
	{	



 			$data = [
 				'id_project'=>$id,
 				'jobdesk'=>$this->input->post('jobdesk'),
 				'keterangan'=>$this->input->post('keterangan')
 				];
 			
 				$this->perintah->insert('jobdesk', $data);
				$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Penambahan jobdesk berhasil</h4>
	                Anda telah manambahkan  jobdesk dari project '.$project["nama_project"].'
	              </div>');
 			
			redirect('admin/detail_portofolio/'.$id);
	}
	public function simpan_dokumentasi($id)
	{	



 			$data = [
 				'id_project'=>$id,
 				'dokumentasi'=>$this->input->post('dokumentasi'),
 				'keterangan'=>nl2br($this->input->post('ket'))
 				];
 			
 				$this->perintah->insert('dokumentasi', $data);
				$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-check"></i>Penambahan jobdesk berhasil</h4>
	                Anda telah manambahkan  dokumentasi dari project '.$project["nama_project"].'
	              </div>');
 			
			redirect('admin/detail_portofolio/'.$id);
	}
	public function simpan_aktivitas($id)
	{	



 			$data = [
 				'id_project'=>$id,
 				'tanggal'=>$this->input->post('tgl'),
 				'waktu_mulai'=>$this->input->post('mulai'),
 				'waktu_selesai'=>$this->input->post('selesai'),
 				'keterangan'=>$this->input->post('ket')
 				];
 				$this->perintah->insert('aktivitas_project', $data);


		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Penambahan aktivitas berhasil</h4>
                
              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
	public function delete_fitur($id)
	{	
 			$data = $this->admin_query->ambil_fitur($id);
 			$id_project=$data['id_project'];
 			 $project=$this->admin_query->detail_project($id_project);
 		$hapus =	$this->admin_query->delete_fitur($id); 
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Hapus fitur berhasil</h4>
                Anda telah menghapus fitur dari project '.$project["nama_project"].'
              </div>');
		redirect('admin/detail_portofolio/'.$id_project);
	}
	public function delete_jobdesk($id, $id_project)
	{	
 			$data = $this->db->delete('jobdesk', ['id_jobdesk'=>$id]);
 			 
 		
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Hapus fitur berhasil</h4>
                Anda telah menghapus fitur dari project '.$project["nama_project"].'
              </div>');
		redirect('admin/detail_portofolio/'.$id_project);
	}
	public function delete_pembayaran($id, $id_project)
	{	
 			$data = $this->db->delete('pembayaran', ['id_pembayaran'=>$id]);
 			 
 		
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Hapus fitur berhasil</h4>
                Anda telah menghapus data pembayaran dari project '.$project["nama_project"].'
              </div>');
		redirect('admin/detail_portofolio/'.$id_project);
	}
	public function hapus_timeline($id, $idp='')
	{	
 			$this->perintah->delete('timeline', ['id'=>$id]);
 			$this->perintah->delete('catatan_timeline', ['id_timeline'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Timeline Dihapus
              </div>');
		redirect('admin/detail_portofolio/'.$idp);
	}
	public function hapus_catatan_timeline($id, $idp='')
	{	
 			$this->perintah->delete('catatan_timeline', ['id_catatan'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
               Catatan Timeline Dihapus
              </div>');
		redirect('admin/detail_portofolio/'.$idp);
	}
	public function hapus_schedule($id)
	{	
 			$this->perintah->delete('time_schedule', ['id_ts'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Time Schedule Dihapus
              </div>');
		redirect('admin/schedule/');
	}
	public function hapus_keuangan($id, $filter)
	{	
 			$this->perintah->delete('keuangan', ['id_keuangan'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Data Keuangan Dihapus
              </div>');
		

		if ($filter=='sekarang') {
			redirect('admin/keuangan/');
		}
		elseif ($filter=='bulanan') {
			$bulan = $this->input->get('bulan');
			$tahun = $this->input->get('tahun');
			redirect('admin/filter_keuangan/'.$filter.'?bulan='.$bulan.'&tahun='.$tahun);
		}
		elseif ($filter=='harian') {
			$tgl = $this->input->get('tgl_filter');
			redirect('admin/filter_keuangan/'.$filter.'?tgl_filter='.$tgl);
		}else if ($filter=='rentang_waktu') {
			$tgl_awal = $this->input->get('tgl_awal');
			$tgl_akhir = $this->input->get('tgl_akhir');
			redirect('admin/filter_keuangan/'.$filter.'?tgl_awal='.$tgl_awal.'&tgl_akhir='.$tgl_akhir);
		}else{
			redirect('admin/filter_keuangan/semua');
		}
	}
	public function hapus_rab($id, $idp='')
	{	
 			$this->perintah->delete('rab_planning', ['id_rab'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                RAB Dihapus
              </div>');
		redirect('admin/detail_planning/'.$idp);
	}
	public function hapus_target($id, $idp='')
	{	
 			$this->perintah->delete('target_planning', ['id_target'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Target Dihapus
              </div>');
		redirect('admin/detail_planning/'.$idp);
	}
	public function hapus_rule($id, $idp='')
	{	
 			$this->perintah->delete('rules_planning', ['id_rules'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Rules Dihapus
              </div>');
		redirect('admin/detail_planning/'.$idp);
	}
	public function hapus_tdl($id, $idp='')
	{	
 			$this->perintah->delete('to_do_list_planning', ['id_tdl'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                To Do List Dihapus
              </div>');
		redirect('admin/detail_planning/'.$idp);
	}
	public function hapus_aktivitas($id, $idp='')
	{	
 			$this->perintah->delete('aktivitas_planning', ['id_aktivitas'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Aktivitas Dihapus
              </div>');
		redirect('admin/detail_planning/'.$idp);
	}
	public function hapus_dokumentasi($id, $idp='')
	{	
 			$this->perintah->delete('dokumentasi', ['id_dokumentasi'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Dokumentasi Dihapus
              </div>');
		redirect('admin/detail_portofolio/'.$idp);
	}
	public function hapus_file_support($id, $idp, $file)
	{	
 			$this->perintah->delete('file_support_project', ['id_fs'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                File Support Dihapus
              </div>');
		unlink('file_support/'.$file);
		redirect('admin/detail_portofolio/'.$idp);
	}
	public function timeline_selesai($id, $idp='')
	{	
		$via = $this->input->post('via');
		$kegiatan = $this->input->post('keg');
		$data = ['status'=>'1', 'selesai_pada'=>date('Y-m-d h:i:s')];
 			$this->perintah->update('timeline', $data, ['id'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Timeline kegiatan '.$kegiatan.' selesai.!
              </div>');
		if ($via=='timeline') {
			if ($this->input->post('bulan')!='' && $this->input->post('tahun')!='') {
				$redirect='timeline/?bulan='.$this->input->post('bulan').'&tahun='.$this->input->post('tahun');
			}else{
				$redirect='timeline';
			}
		}else{
			$redirect = $via =='index' ? $via : $via.'/'.$idp ; 
		}

		redirect('admin/'.$redirect);
	}
	public function selesai_ratget($id, $idp='')
	{	
		$target = $this->input->post('target');
		$catatan = $this->input->post('catatan');
		$data = ['status'=>'1','catatan'=>nl2br($catatan)];
 			$this->perintah->update('target_planning', $data, ['id_target'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Target '.$target.' selesai.!
              </div>'); 
		redirect('admin/detail_planning/'.$idp);
	}

	public function eksekusi_rab($id, $idp='')
	{	
		$harga = $this->input->post('harga');
		$tgl_exekusi = $this->input->post('tgl_exekusi');
		$jam_exekusi = $this->input->post('jam_exekusi');
		$item = $this->input->post('item');
		$debit = $this->input->post('debit');

		if ($debit=='on') {
			$catatan = "Di Input ke kas keluar";
			$data_keuangan = [
				'tgl'=>$tgl_exekusi,
				'jam'=>$jam_exekusi,
				'jenis'=>'Uang Keluar',
				'keterangan'=>'Belanja '.$item,
				'jumlah'=>$harga,
			];
			$this->db->insert('keuangan', $data_keuangan);

		}else{
			$catatan = "Tidak di input";
		}
		$data = ['status'=>'1', 'harga_sebenarnya'=>$harga, 'tgl_eksekusi'=>$tgl_exekusi,'jam_eksekusi'=>$jam_exekusi, 'catatan'=>$catatan];
 			$this->perintah->update('rab_planning', $data, ['id_rab'=>$id]);
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Item '.$itam.' di eksekusi.!
              </div>');
		
		redirect('admin/detail_planning/'.$idp);
	}

	public function delete_portofolio($id)
	{	
 			$this->perintah->delete('project',['id_project'=>$id]);
 			$this->perintah->delete('client',['id_project'=>$id]);
 			$this->perintah->delete('data_hosting_web',['id_project'=>$id]);
 			$this->perintah->delete('fitur_project',['id_project'=>$id]);
 			
 			$pembayaran = $this->perintah->get_where('pembayaran',['id_project'=>$id]);
 			foreach ($pembayaran as $pb) {
 				$bukti = $pb['bukti_pembayaran'];
 				unlink('./bukti_pembayaran/'.$bukti);
 			}

 			$gambar = $this->perintah->get_where('gambar_project',['id_project'=>$id]);
 			foreach ($gambar as $gb) {
 				$gambar = $gb['gambar'];
 				unlink('./gambar_project/'.$gambar);
 				
 			}

 			$this->session->set_flashdata('pesan','<div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
           
                Data portofolio telah dihapus
              </div>');
			
		 redirect('admin/portofolio');
	}


	public function delete_planning($id)
	{	
 			$this->perintah->delete('planning',['id_planning'=>$id]);
 			$this->perintah->delete('aktivitas_planning',['id_planning'=>$id]);
 			$this->perintah->delete('rab_planning',['id_planning'=>$id]);
 			$this->perintah->delete('rules_planning',['id_planning'=>$id]);
 			$this->perintah->delete('target_planning',['id_planning'=>$id]);
 			$this->perintah->delete('to_do_list_planning',['id_planning'=>$id]);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
           
                Data planning telah dihapus
              </div>');
			
		 // redirect('admin/planning');
	}
	public function delete_gambar($id)
	{	
		$ngb=$this->input->post('ngb');
		$idg=$this->input->post('idg');
		$idp=$this->input->post('idp');
		
//echo $id;
$data = $this->admin_query->info_gambar($id);
$gambar=$data['gambar'];
$idpr=$data['id_project'];
unlink('./gambar_project/'.$gambar);
$this->admin_query->delete_gambar($id);

 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Gambar  dihapus</h4>
               
              </div>');
			
	 redirect('admin/detail_portofolio/'.$idpr);
	}

	public function simpanupdate_statusproject($id)
	{	
 			$this->admin_query->simpanupdate_statusproject($id);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Status Project diperbaharui</h4>
                Status  project anda berhasil diperbaharui
              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
	public function simpanupdate_statusplanning($id)
	{	
		$data = ['status'=>$this->input->post('st')];
 			$this->perintah->update('planning',$data, ['id_planning'=>$id]);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Status Project diperbaharui</h4>
                Status  planning anda berhasil diperbaharui
              </div>');
			redirect('admin/detail_planning/'.$id);
	}
	public function publikasikan($id, $value)
	{	
 			$this->db->update('project', ['publikasikan'=>$value], ['id_project'=>$id]);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Publikasi Portofolio diperbaharui</h4>
                Publikasi portofolio anda berhasil diperbaharui
              </div>');
			redirect('admin/detail_portofolio/'.$id);
	}
	public function skill()
	{	
		$data=[
			'skill'=> $this->admin_query->skill(),
			'judul'=>'Manajemen Skill',
			

		];
		$data['menu'] = '
		<li><a href="#" id="menu">Skill</a></li>
		<li><a href="" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#addskill">Tambah Skill</a>';
		$this->template->load('admin/template','admin/form/skill/skill', $data);
	}
	public function note()
	{	
		$data=[
			'note'=> $this->perintah->get('note'),
			'judul'=>'Note',
			

		];
		$data['menu'] = '
		<li><a href="#" id="menu">Note</a></li>
		<li><a href="'.base_url('admin/add_note').'" class="btn btn-info btn-xs"  >Tambah Note</a>';
		$this->template->load('admin/template','admin/form/note/note', $data);
	}
	
	public function materi_skill($id)
	{	
		$data=[
			'detail_skill'=> $this->admin_query->detail_skill($id),
			'materi'=> $this->admin_query->materi($id),
			
			'judul'=>'Manajemen Skill',
			'menu'=>'Skill'

		];
		$this->template->load('admin/template','admin/form/skill/materi', $data);
	}
	
	public function saveedit_skill($id)
	{	
 			$this->admin_query->simpanedit_skill($id);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Skill anda telah diperbaharui</h4>

              </div>');
		
			redirect('admin/materi_skill/'.$id);
	}

	public function simpan_materi($id)
	{	
 			$this->admin_query->simpan_materi($id);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Materi ditambahkan</h4>

              </div>');
			redirect('admin/materi_skill/'.$id);
	}

	public function delete_materi($id)
	{	
 			$data = $this->admin_query->ambil_materi($id);
 			$idm=$data['id_skill'];
 			 $del=$this->admin_query->delete_materi($id);
 	
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Materi dihapus</h4>

              </div>');
		redirect('admin/materi_skill/'.$idm);
	}
	public function delete_note($id)
	{	
 			$data = $this->perintah->delete('note', ['id_note'=>$id]);
 	
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Note dihapus</h4>

              </div>');
		redirect('admin/note');
	}

	public function delete_kegiatan($id)
	{	
 			$data = $this->perintah->delete('kegiatan', ['id_kegiatan'=>$id]);
 	
		$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Kegiatan dihapus</h4>

              </div>');
		redirect('admin/agenda');
	}

	public function save_skill()
	{	
 			$this->admin_query->simpan_skill();
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Skill anda telah ditambahkan</h4>

              </div>');
			redirect('admin/skill');
	}
	public function save_note()
	{	
 			$this->perintah->insert('note',['judul'=>$this->input->post('judul'),'isi'=>$this->input->post('isi')]);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Note anda telah ditambahkan</h4>

              </div>');
			redirect('admin/note');
	}
	public function saveedit_note($id)
	{	
 			$this->perintah->update('note',['judul'=>$this->input->post('judul'),'isi'=>$this->input->post('isi')],['id_note'=>$id]);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Note anda telah ditambahkan</h4>

              </div>');
			redirect('admin/note');
	}

	public function delete_skill($id)
	{	
 			$this->admin_query->delete_skill($id);
 			$this->db->delete('materi_skill', ['id_skill'=>$id]);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Skill dihapus</h4>
               
              </div>');
			
		 redirect('admin/skill');
	}

	public function file()
	{	
		$data=[
			'file'=> $this->admin_query->file(),
			'judul'=>'Management File',
			'menu'=>'
			<li>FIle</li>
			<li><a href="" class="btn btn-info btn-xs" data-toggle="modal" data-target="#addfile" >Tambah File</a></li>'

		];
		$this->template->load('admin/template','admin/form/file/file', $data);
	}
	public function agenda()
	{	
		
		$jam = date('H:i:s');
		$data=[
			'agenda'=> $this->perintah->query("SELECT * from kegiatan where tgl_kegiatan>=current_date"),
			'agenda_expired'=> $this->perintah->query("SELECT * from kegiatan where tgl_kegiatan<current_date"),
			'agenda_all'=> $this->perintah->query("SELECT * from kegiatan"),
			'judul'=>'Management Agenda Kegiatan',
			'menu'=>'
			<li>FIle</li>
			<li><a href="" class="btn btn-info btn-xs" data-toggle="modal" data-target="#addkegiatan" >Tambah Kegiatan</a></li>'

		];
		$this->template->load('admin/template','admin/form/agenda/agenda',$data);
	}

	public function save_kegiatan()
	{		
		$data = [
			'nama_kegiatan'=>$this->input->post('nama'),
			'keterangan'=>$this->input->post('ket'),
			'tgl_input'=>date('Y-m-d h:i:s'),
			'tgl_kegiatan'=>$this->input->post('tgl'),
			'jam_kegiatan'=>$this->input->post('jam'),
			'status'=>''
		];
 			$this->perintah->insert('kegiatan',$data);
 			$this->session->set_flashdata('pesan','<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>Kegiatan telah ditambahkan</h4>

              </div>');
			redirect('admin/agenda');
	}




    public function backup_db(){
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->helper('download');
        $this->load->library('zip');


        $this->load->dbutil();
        $db_format = array('format'=>'zip', 'filename'=>'personal_web_management.sql');
        $backup = $this->dbutil->backup($db_format);
        $dbname = date('Y-m-d h.i.s').'.zip';
        $save = 'file/backup_db/';
        // write_file($save, $backup);
        force_download($dbname, $backup);
        
    }



    public function download_file_support($file){
        $this->load->helper('download');
        $this->load->helper('file'); 
        $this->load->helper('directory');
		$map = read_file("./assets/file_support/".$file);

		force_download('file_support/'.$file,  NULL);
  //       $pth    =   "/assets/file_support/".$file;
		// $nme    =   "sample_file.pdf";
		// force_download($nme, $pth);    

        
    }
}
