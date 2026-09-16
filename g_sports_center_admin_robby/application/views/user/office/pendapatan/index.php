

<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
                        <li class="nav-item">
                            <a role="tab" class="nav-link active" id="btn_tab_cek_data" data-toggle="tab" href="#laporan_pendapatan_semua">
                                <span>Semua Pendapatan</span>
                            </a>
                        </li>
  <?php 
  $kumpul_metode_pembayaran = [] ; 
foreach (metode_pembayaran() as $k => $v) {
  $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'] ; 
}

  foreach ($akun_pendapatan as $k => $v) { ?>
                        <li class="nav-item">
                            <a role="tab" class="nav-link" id="btn_tab_cek_data" data-toggle="tab" href="#laporan_pendapatan_<?php echo $v['id_akun_pendapatan'] ?>">
                                <span><?php echo $v['nama_akun'] ?></span>
                            </a>
                        </li>
<?php   } ?>
                       

                        <li class="nav-item">
                            <a href="#" class="nav-link"  aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"  >
                                <span>Filter</span>
                            </a>
                              <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu-hover-primary dropdown-menu"><h6 tabindex="-1" class="dropdown-header">Jenis Filter</h6>
                                                    <button type="button" tabindex="0" class="dropdown-item"  data-toggle="modal" data-target="#filter_harian" >Harian</button>
                                                    <button type="button" tabindex="0" class="dropdown-item"  data-toggle="modal" data-target="#filter_bulanan" >Bulanan</button>
                                                    <button type="button" tabindex="0" class="dropdown-item"  data-toggle="modal" data-target="#filter_tahunan" >Tahunan</button>
                                                    <!-- <div tabindex="-1" class="dropdown-divider"></div> -->
                                                    <button type="button" tabindex="0" class="dropdown-item"  data-toggle="modal" data-target="#filter_periode" >Periode</button>
                                                </div>
                        </li> 
                          <li class="nav-item">
                            <a class="nav-link"  href="<?php echo $link_print ?>" target="_blank">
                                <span>Print</span>
                            </a>
                        </li>
                     
                    </ul>


                    <div class="tab-content">

  <?php 
  $kumpul_pendapatan_semua = [];
if ($id_kasir=='Semua') {
  $where_user = "";
}else{
  $where_user = "and p.id_user='$id_kasir'";
}

  foreach ($akun_pendapatan as $k => $v) { 
    $id_akun_pendapatan = $v['id_akun_pendapatan']; 
    if ($filter=='bulanan') {
      $q_pendapatan = $this->db->query("SELECT
          t.no_transaksi, p.id_pendapatan, p.id_transaksi,t.id_metode_pembayaran,p.kategori,p.nilai,p.id_akun_pendapatan,p.akun,p.item_transaksi,p.keterangan, t.pembayaran, p.tgl_transaksi,p.id_user,p.status ,
        mu.nama, t.fitur, t.fasilitas  from pendapatan p left join master_user mu on p.id_user = mu.id_user
        left join transaksi t on p.id_transaksi= t.id_transaksi
       where p.id_akun_pendapatan='$id_akun_pendapatan' and (month(p.tgl_transaksi) ='$bulan' and year(p.tgl_transaksi) ='$tahun') and p.status='Settlement' $where_user  order by p.id_pendapatan desc")->result_array();
    }
    elseif ($filter=='tahunan') {
      $q_pendapatan = $this->db->query("SELECT
          t.no_transaksi, p.id_pendapatan, p.id_transaksi,t.id_metode_pembayaran,p.kategori,p.nilai,p.id_akun_pendapatan,p.akun,p.item_transaksi,p.keterangan, t.pembayaran, p.tgl_transaksi,p.id_user,p.status ,
        mu.nama, t.fitur, t.fasilitas  from pendapatan p left join master_user mu on p.id_user = mu.id_user
        left join transaksi t on p.id_transaksi= t.id_transaksi
       where p.id_akun_pendapatan='$id_akun_pendapatan' and (year(p.tgl_transaksi) ='$tahun') and p.status='Settlement' $where_user order by p.id_pendapatan desc")->result_array();
    }
    elseif ($filter=='periode') {
    $jam_mulai = '00:00:00';
    $jam_akhir = '23:59:59';
      $q_pendapatan = $this->db->query("SELECT
          t.no_transaksi, p.id_pendapatan, p.id_transaksi,t.id_metode_pembayaran,p.kategori,p.nilai,p.id_akun_pendapatan,p.akun,p.item_transaksi,p.keterangan, t.pembayaran, p.tgl_transaksi,p.id_user,p.status ,
        mu.nama, t.fitur, t.fasilitas  from pendapatan p left join master_user mu on p.id_user = mu.id_user
        left join transaksi t on p.id_transaksi= t.id_transaksi
       where p.id_akun_pendapatan='$id_akun_pendapatan' and ( p.tgl_transaksi BETWEEN '$tgl_awal $jam_mulai' and '$tgl_akhir $jam_akhir') and p.status='Settlement' $where_user order by p.id_pendapatan desc")->result_array();
    }else{
      $q_pendapatan = $this->db->query("SELECT
          t.no_transaksi, p.id_pendapatan, p.id_transaksi,t.id_metode_pembayaran,p.kategori,p.nilai,p.id_akun_pendapatan,p.akun,p.item_transaksi,p.keterangan, t.pembayaran, p.tgl_transaksi,p.id_user,p.status ,
        mu.nama, t.fitur, t.fasilitas  from pendapatan p left join master_user mu on p.id_user = mu.id_user
        left join transaksi t on p.id_transaksi= t.id_transaksi
       where p.id_akun_pendapatan='$id_akun_pendapatan' and p.tgl_transaksi like '$tgl%' and p.status='Settlement' $where_user order by p.id_pendapatan desc")->result_array();

    }

    ?>
                         <div class="tab-pane tabs-animation fade" id="laporan_pendapatan_<?php echo $v['id_akun_pendapatan'] ?>" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">Laporan Pendapatan <?php echo $v['nama_akun'] ?>
                                        </div>
                                        <div class="card-body">
                                          <table class="table data_tabel table-striped table-bordered" style="width:100%">
                                            <thead>
                                              <tr>
                                                <td rowspan="2" width="10px">No</td>
                                                <td rowspan="2">No Transaksi</td>
                                                <td rowspan="2">Waktu Transaksi</td>
                                                <td rowspan="2">Fasilitas</td>
                                                <td rowspan="2">Item Transaksi</td>
                                                <td rowspan="2">Keterangan</td>
                                                <td rowspan="2">Kasir</td>
                                                <td rowspan="2">Metode Pembayaran</td>
                                                <td colspan="2">Nilai</td>
                                                <td rowspan="2">Option</td>
                                              </tr>
                                              <tr>
                                                <td>Masuk</td>
                                                <td>Keluar</td>
                                             
                                              </tr>
                                            </thead>
                                          <?php 
                                          $no=1;
                                          $total_masuk = 0;
                                          $total_keluar= 0;
                                          foreach ($q_pendapatan as $k_p => $v_p) { 
                                            $data_pendapatan = [
                                              'id_pendapatan' => $v_p['id_pendapatan'],
                                              'status' => $v_p['status'],
                                              'no_transaksi' => $v_p['no_transaksi'],
                                              'tgl_transaksi' => $v_p['tgl_transaksi'],
                                              'fasilitas' => $v_p['fasilitas'],
                                              'item_transaksi' => $v_p['item_transaksi'],
                                              'keterangan' => $v_p['keterangan'],
                                              'pembayaran' => $v_p['pembayaran'],
                                              'kategori' => $v_p['kategori'],
                                              'nilai' => $v_p['nilai'],
                                              'kasir' => $v_p['nama'],
                                              'id_metode_pembayaran' => $v_p['id_metode_pembayaran'],
                                              'fitur' => $v_p['fitur'],
                                              'id_transaksi' => $v_p['id_transaksi'],
                                            ];

                                            array_push($kumpul_pendapatan_semua, $data_pendapatan);
                                            ?>
                                              <tr>
                                                <td><?php echo $no++ ?></td>
                                                <td><?php echo $v_p['no_transaksi'] ?></td>
                                                <td><?php echo $v_p['tgl_transaksi'] ?></td>
                                                <td><?php echo $v_p['fasilitas'] ?></td>
                                                <td><?php echo $v_p['item_transaksi'] ?></td>
                                                <td><?php echo $v_p['keterangan'] ?></td>
                                                <td><?php echo $v_p['nama'] ?></td>
                                                <td><?php echo @$kumpul_metode_pembayaran[$v_p['id_metode_pembayaran']] ?></td>
                                                <?php if ($v_p['kategori']=='Masuk') { 
                                                  $total_masuk += $v_p['nilai'];
                                                  ?>
                                                <td align="right"><?php echo number_format($v_p['nilai']) ?></td>
                                                <td align="right">-</td>
                                                <?php }else{

                                                  $total_keluar += $v_p['nilai']; ?>
                                                <td align="right">-</td>
                                                <td align="right"><?php echo number_format($v_p['nilai']) ?></td>
                                              <?php } ?>
                                              <td>
                                                <div class="btn-group">

                                                        <a href="javascript:void(0)" onclick="edit_metode_pembayaran('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['id_pendapatan'] ?>','<?php echo $v_p['keterangan'] ?>')" class="btn btn-outline-danger btn-sm" data-toggle="tooltip" title="Ganti metode pembayaran <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-pen"></i></a>



                                                    <?php if ($v_p['fitur']=='F & B') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/fnb/print_transaksi/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                         <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','F & B','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi F & B"><i class="fa fa-trash"></i></a>
                                                     
                                                    <?php }elseif ($v_p['fitur']=='Proshop') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/proshop/print_transaksi/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','Proshop','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi proshop"><i class="fa fa-trash"></i></a>
                                                     
                                                    <?php } else{
                                                      if ($v_p['fasilitas']=='Gym') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/gym/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Gym - Special Membership') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/gym_spesial/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Futsal - Harian') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/futsal/print_transaksi_futsal_harian/') ?>')" class="btn btn-outline-info   btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Futsal - Member Bulanan') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/futsal_bulanan/print_transaksi_futsal_bulanan/') ?>')" class="btn btn-outline-info  btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Futsal - Turnamen') { 
                                                        if ($v_p['status']=='Settlement') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/booking/print_visit_futsal_turnamen/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                        <?php }else{ ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/futsal_turnamen/print_transaksi_futsal_turnamen/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>

                                                        <?php }
                                                        ?>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Badminton - Harian') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/badminton/print_transaksi_badminton_harian/') ?>')" class="btn btn-outline-info   btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Badminton - Member Bulanan') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/badminton_bulanan/print_transaksi_badminton_bulanan/') ?>')" class="btn btn-outline-info  btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Badminton - Turnamen') { 
                                                        if ($v_p['status']=='Settlement') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/booking/print_visit_badminton_turnamen/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                        <?php }else{ ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/badminton_turnamen/print_transaksi_badminton_turnamen/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>

                                                        <?php }
                                                        ?>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Harian') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming/print_transaksi_swimming_harian/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Membership') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming_membership/print_transaksi_swimming_membership/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Club') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming_club/print_transaksi_swimming_club/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Pelajar') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming_pelajar/print_transaksi_swimming_pelajar/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Spesial Membership') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming_spesial/print_transaksi_swimming_spesial/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }






                                                      else if (strpos($v_p['fasilitas'], 'Les - ')!== false) {  ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/les/print_transaksi_les/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }

                                                   



                                                      else if ($v_p['fasilitas']=='Private - Gym') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/private_gym/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Private - Muaythai') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/private_muaythai/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Private - Swimming') { ?>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/private_swimming/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Student Card') { ?>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/student_card/print_transaksi_sc/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                      <?php }
                                                      else { ?>
                                                         <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/les/print_transaksi_les/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                      <?php } ?>
                                                      
                                                    <?php } ?>
                                                  </div>
                                                  </td>
                                              </tr>
                                          <?php } ?>
                                          <tfoot>
                                            <tr>
                                              <td colspan="8">Total</td>
                                              <td align="right"><?php echo number_format($total_masuk) ?></td>
                                              <td align="right"><?php echo number_format($total_keluar) ?></td>
                                              <td>-</td>
                                            </tr>
                                            <tr>
                                              <td colspan="8"><b>Total</b></td>
                                              <td colspan="2" align="center"><b><?php echo number_format($pendapatan_total_perfasilitas =$total_masuk - $total_keluar) ?></b></td>
                                              <td>-</td>
                                            </tr>
                                            <tr>
                                              <td colspan="8"><b>Pendapatan Pajak</b></td>
                                              <td colspan="2" align="center"><b><?php 
                                              $enampuluhpersen = $pendapatan_total_perfasilitas * 0.4 ; 
                                              echo number_format($enampuluhpersen) ?></b></td>
                                              <td>Ket : 40% Pendapatan</td>
                                            </tr>
                                          
                                          </tfoot>
                                          </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                  <?php } ?>



                         <div class="tab-pane active tabs-animation fade show" id="laporan_pendapatan_semua" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">Laporan Pendapatan Semua
                                        </div>
                                        <div class="card-body">
                                          <?php echo $this->session->flashdata('pesan') ?>
                                          <table class="table data_tabel table-striped table-bordered" style="width:100%">
                                            <thead>
                                              <tr>
                                                <td rowspan="2" width="10px">No</td>
                                                <td rowspan="2">No Transaksi</td>
                                                <td rowspan="2">Waktu Transaksi</td>
                                                <td rowspan="2">Fasilitas</td>
                                                <td rowspan="2">Item Transaksi</td>
                                                <td rowspan="2">Keterangan</td>
                                                <td rowspan="2">Kasir</td>
                                                <td rowspan="2">Metode Pembayaran</td>
                                                <td colspan="2">Nilai</td>
                                                <td rowspan="2">Option</td>
                                              </tr>
                                              <tr>
                                                <td>Masuk</td>
                                                <td>Keluar</td>
                                             
                                              </tr>
                                            </thead>
                                          <?php 
                                          $no=1;

                                          arsort($kumpul_pendapatan_semua);
                                          $total_masuk_all =0;
                                          $total_keluar_all =0;
                                          foreach ($kumpul_pendapatan_semua as $k_p => $v_p) { 
                                           $pecah_mp = explode(',', $v_p['id_metode_pembayaran']);
                                           $kumpul_mp = [];
                                           foreach ($pecah_mp as $key => $value) {
                                             // $kumpul_mp[$key] = $kumpul_metode_pembayaran[$value];
                                             array_push($kumpul_mp, $kumpul_metode_pembayaran[$value]);
                                           }
                                            ?>
                                              <tr>
                                                <td><?php echo $no++ ?></td>
                                                <td><?php echo $v_p['no_transaksi'] ?></td>
                                                <td><?php echo $v_p['tgl_transaksi'] ?></td>
                                                <td><?php echo $v_p['fasilitas'] ?></td>
                                                <td><?php echo $v_p['item_transaksi'] ?></td>
                                                <td><?php echo $v_p['keterangan'] ?></td>
                                                <td><?php echo $v_p['kasir'] ?></td>

                                                <td><?php echo $v_p['pembayaran'].'<br>'.join(', ',$kumpul_mp) ?></td>
                                                <?php if ($v_p['kategori']=='Masuk') { 
                                                  $total_masuk_all += $v_p['nilai'];
                                                  ?>
                                                <td align="right"><?php echo number_format($v_p['nilai']) ?></td>
                                                <td align="right">-</td>
                                                <?php }else{  
                                                  $total_keluar_all += $v_p['nilai'];?>
                                                <td align="right">-</td>
                                                <td align="right"><?php echo number_format($v_p['nilai']) ?></td>
                                              <?php } ?>

                                              <td>
                                                <div class="btn-group">

                                                        <a href="javascript:void(0)" onclick="edit_metode_pembayaran('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['id_pendapatan'] ?>','<?php echo $v_p['keterangan'] ?>')" class="btn btn-outline-danger btn-sm" data-toggle="tooltip" title="Ganti metode pembayaran <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-pen"></i></a>


                                                    <?php if ($v_p['fitur']=='F & B') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/fnb/print_transaksi/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','F & B','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi F & B"><i class="fa fa-trash"></i></a>
                                                     
                                                    <?php }elseif ($v_p['fitur']=='Proshop') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/proshop/print_transaksi/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','Proshop','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi proshop"><i class="fa fa-trash"></i></a>
                                                     
                                                    <?php } else{
                                                      if ($v_p['fasilitas']=='Gym') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/gym/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>

                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Gym - Special Membership') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/gym_spesial/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Futsal - Harian') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/futsal/print_transaksi_futsal_harian/') ?>')" class="btn btn-outline-info   btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Futsal - Member Bulanan') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/futsal_bulanan/print_transaksi_futsal_bulanan/') ?>')" class="btn btn-outline-info  btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>

                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Futsal - Turnamen') { 
                                                        if ($v_p['status']=='Settlement') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/booking/print_visit_futsal_turnamen/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <?php }else{ ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/futsal_turnamen/print_transaksi_futsal_turnamen/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>

                                                        <?php }
                                                        ?>

                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>


                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Badminton - Harian') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/badminton/print_transaksi_badminton_harian/') ?>')" class="btn btn-outline-info   btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>

                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Badminton - Member Bulanan') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/badminton_bulanan/print_transaksi_badminton_bulanan/') ?>')" class="btn btn-outline-info  btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>

                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Badminton - Turnamen') { 
                                                        if ($v_p['status']=='Settlement') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/booking/print_visit_badminton_turnamen/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <?php }else{ ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/badminton_turnamen/print_transaksi_badminton_turnamen/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>

                                                        <?php }
                                                        ?>

                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }

                                                      else if ($v_p['fasilitas']=='Pickle - Harian') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/pickle/print_transaksi_pickle_harian/') ?>')" class="btn btn-outline-info   btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>

                                                      <?php }

                                                      else if ($v_p['fasilitas']=='Swimming - Harian') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming/print_transaksi_swimming_harian/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Membership') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming_membership/print_transaksi_swimming_membership/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Club') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming_club/print_transaksi_swimming_club/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Pelajar') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming_pelajar/print_transaksi_swimming_pelajar/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Swimming - Spesial Membership') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/swimming_spesial/print_transaksi_swimming_spesial/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                     






                                                      else if (strpos($v_p['fasilitas'], 'Les - ')!== false) { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/les/print_transaksi_les/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }

                                                   



                                                      else if ($v_p['fasilitas']=='Private - Gym') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/private_gym/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Private - Muaythai') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/private_muaythai/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Private - Swimming') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/private_swimming/print_transaksi_gym/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else if ($v_p['fasilitas']=='Student Card') { ?>
                                                        <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/student_card/print_transaksi_sc/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                        <a href="javascript:void(0)" onclick="hapus_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo $v_p['fasilitas'] ?>','No. <?php echo $v_p['no_transaksi'].' pada '.$v_p['tgl_transaksi'] ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Hapus transaksi <?php echo $v_p['fasilitas'] ?>"><i class="fa fa-trash"></i></a>
                                                      <?php }
                                                      else { ?>
                                                         <a href="javascript:void(0)" onclick="print_transaksi('<?php echo $v_p['id_transaksi'] ?>','<?php echo base_url('/user/gro/transaction/les/print_transaksi_les/') ?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" title="Re-Print"><i class="fa fa-print"></i></a>
                                                      <?php }
                                                    } ?>
                                                  </td>
                                                </div>
                                              </tr>
                                          <?php } ?>
                                          <tfoot>
                                            <tr>
                                              <td colspan="8">Total</td>
                                              <td align="right"><?php echo number_format($total_masuk_all) ?></td>
                                              <td align="right"><?php echo number_format($total_keluar_all) ?></td>
                                              <td>-</td>
                                            </tr>
                                            <tr>
                                              <td colspan="8"><b>Pendapatan</b></td>
                                              <td colspan="2" align="center"><b><?php echo number_format($pendapatan_total =$total_masuk_all - $total_keluar_all) ?></b></td>
                                              <td>-</td>
                                            </tr>
                                            <tr>
                                              <td colspan="8"><b>Pendapatan Pajak</b></td>
                                              <td colspan="2" align="center"><b><?php 
                                              $enampuluhpersen = $pendapatan_total * 0.4 ; 
                                              echo number_format($enampuluhpersen) ?></b></td>
                                              <td>Ket : 40% Pendapatan</td>
                                            </tr>
                                          
                                          </tfoot>
                                          </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                      </div>




<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.10.19/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>




<script type="text/javascript">


  function print_transaksi(id_transaksi, link){
     $('#modal_alert').modal('hide');
    $('#print_transaksi').modal('show');
    $('#print_transaksi').find('#struk').html(`
      <iframe src="`+ link + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
      `);
  }


  function hapus_transaksi(id_transaksi, fasilitas, caption_tambahan){
    var caption = '';
    var warning = '';
    if (fasilitas=='F & B') {
      warning ='Hapus Transaksi F & B <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi F & B data berikut ini akan ikuit terhapus <br>';
      caption  += '<b>[Pendapatan F & B, Transaksi F & B, Item produk terjual pada F & B</b>]<br>Tetap lanjutkan?';
     
    } 
    else if (fasilitas=='Proshop') {
      warning ='Hapus Transaksi Proshop <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi proshop data berikut ini akan ikuit terhapus <br>';

      caption  += '<b>[Pendapatan proshop, transaksi proshop, Item produk terjual pada proshop</b>]<br>Tetap lanjutkan?';
    
    } 
    else if (fasilitas=='Gym' || fasilitas=='Gym - Special Membership' || fasilitas=='Swimming - Membership' || fasilitas=='Swimming - Club' || fasilitas=='Swimming - Spesial Membership' || fasilitas=='Private - Gym' || fasilitas=='Private - Muaythai' || fasilitas=='Private - Swimming') {
      warning ='Hapus Transaksi '+fasilitas+' <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi '+fasilitas+' data berikut ini akan ikuit terhapus <br>';

      caption  += '<b>[Pendapatan, transaksi, pembayaran, dan keanggotaan member pada fasilitas '+fasilitas+'</b>]<br>Tetap lanjutkan?';
    
    }
    else if (fasilitas=='Les - Renang' || fasilitas=='Les - Futsal Academy' || fasilitas=='Les - Aikido' || fasilitas=='Les - Silat Harimau') {
      warning ='Hapus Transaksi '+fasilitas+' <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi '+fasilitas+' data berikut ini akan ikuit terhapus <br>';

      caption  += '<b>[Pendapatan, transaksi, pembayaran, dan keanggotaan member pada fasilitas '+fasilitas+'</b>]<br>Tetap lanjutkan?';
    
    } 
    else if (fasilitas=='Student Card' ) {
      warning ='Hapus Transaksi '+fasilitas+' <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi '+fasilitas+' data berikut ini akan ikuit terhapus <br>';

      caption  += '<b>[Pendapatan, transaksi, pembayaran, dan history student card</b>]<br>Tetap lanjutkan?';
    
    } 
    else if (fasilitas=='Futsal - Harian' || fasilitas=='Futsal - Member Bulanan' || fasilitas=='Futsal - Turnamen' ) {
      warning ='Hapus Transaksi '+fasilitas+' <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi '+fasilitas+' data berikut ini akan ikuit terhapus <br>';

      caption  += '<b>[Pendapatan, transaksi, pembayaran, dan jadwal booking futsal / visited pada semua jadwal futsal bulanan yang terkait dengan transaksi ini</b>]<br>Tetap lanjutkan?';
    
    } 
    else if (fasilitas=='Badminton - Harian' || fasilitas=='Badminton - Member Bulanan' || fasilitas=='Badminton - Turnamen' ) {
      warning ='Hapus Transaksi '+fasilitas+' <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi '+fasilitas+' data berikut ini akan ikuit terhapus <br>';

      caption  += '<b>[Pendapatan, transaksi, pembayaran, dan jadwal booking badminton / visited pada semua jadwal badminton bulanan yang terkait dengan transaksi ini</b>]<br>Tetap lanjutkan?';
    
    } 
    
    else if (fasilitas=='Pickle - Harian' || fasilitas=='Pickle - Member Bulanan' || fasilitas=='Pickle - Turnamen' ) {
      warning ='Hapus Transaksi '+fasilitas+' <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi '+fasilitas+' data berikut ini akan ikuit terhapus <br>';

      caption  += '<b>[Pendapatan, transaksi, pembayaran, dan jadwal booking Pickle / visited pada semua jadwal Pickle bulanan yang terkait dengan transaksi ini</b>]<br>Tetap lanjutkan?';
    
    } 
    else if (fasilitas=='Swimming - Harian' || fasilitas=='Swimming - Pelajar' ) {
      warning ='Hapus Transaksi '+fasilitas+' <br>' + caption_tambahan;
      caption ='Jika anda menghapus transaksi '+fasilitas+' data berikut ini akan ikuit terhapus <br>';

      caption  += '<b>[Pendapatan, transaksi, pembayaran</b>]<br>Tetap lanjutkan?';
    
    } 
    else{
      warning ='Belum di setting';
      caption ='Belum di setting';
    } 

    Swal.fire({
        title: warning,
        html: caption,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax(
            {
              url     : baseUrl('/user/office/pendapatan/hapus_transaksi'),
              type    : 'POST',
              data    : { 
                id_transaksi : id_transaksi,
                fasilitas : fasilitas,
                
              },
              success : function(data)
              {
                console.log(data);
                 // $(x).parents("tr").remove();
 
                window.location.href='<?php echo $redirect ?>';
              },
              error : function(){
                alert('ee');
                
              }
            });
      

        
        }
      }); 
  

  }


function simpanedit_metode_pembayaran(){
  var form_data = $('#edit_metode_pembayaran').find('#form_edit_metode_pembayaran').serialize();
  console.log(form_data);

    $.ajax(
            {
              url     : baseUrl('/user/office/pendapatan/simpanedit_metode_pembayaran'),
              type    : 'POST',
              // dataType : 'JSON',
              data    : form_data,
              success : function(data)
              {
                window.location.href='<?php echo $redirect ?>';
              },
              error : function(){
                alert('ee');
                
              }
            });

}



function edit_metode_pembayaran(id_transaksi, id_pendapatan, keterangan){
  $('#edit_metode_pembayaran').modal('show');

            $.ajax(
            {
              url     : baseUrl('/user/office/pendapatan/detail_transaksi'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
                id_transaksi : id_transaksi,
                id_pendapatan : id_pendapatan,
                
              },
              success : function(data)
              {
                console.log(data);
                $('#edit_metode_pembayaran').find('#keterangan').html(keterangan);
                $('#edit_metode_pembayaran').find('#metode_pembayaran').html(data.metode_pembayaran);
                $('#edit_metode_pembayaran').find('#no_transaksi').html(data.transaksi.no_transaksi);
                $('#edit_metode_pembayaran').find('#waktu_transaksi').html(data.transaksi.tgl_transaksi + ' '+ data.transaksi.jam_transaksi);
                $('#edit_metode_pembayaran').find('#fasilitas').html(data.transaksi.fasilitas);
                $('#edit_metode_pembayaran').find('#pembayaran').html(data.transaksi.pembayaran);
                $('#edit_metode_pembayaran').find('#pendapatan').html(data.pendapatan);

                var selected_1 = data.transaksi.pembayaran == '1 Metode Pembayaran' ? `<option selected>1 Metode Pembayaran</option>` : `<option>1 Metode Pembayaran</option>`;
                var selected_2 = data.transaksi.pembayaran == '2 Metode Pembayaran' ? `<option selected>2 Metode Pembayaran</option>` : `<option>2 Metode Pembayaran</option>`;
                var form_mp = `
                    <div class="form-group">
                      <label>Pembayaran</label>
                      <select class="form-control" name="pilih_metode" id="pilih_metode"> 
                        `+selected_1 + selected_2+`
                      </select>
                    </div>`;
                if (data.transaksi.pembayaran=='2 Metode Pembayaran') {
                  form_mp += `
                    <div class="form-group">
                      <label>Metode Pembayaran 1</label>
                      <input type="" name="id_pembayaran_1_sebelumnya" value="`+data.id_pembayaran[0]+`">
                      <select class="form-control" name="metode_pembayaran_1">`;
                        $.each(data.pilihan_metode_pembayaran, function(k,v){
                          var selected_1 = data.metode_pembayaran_terpilih[0] ==v.id_metode_pembayaran ? 'selected' : '';
                          form_mp +=`<option value='`+v.id_metode_pembayaran+`' `+selected_1+`>`+v.metode_pembayaran+`</option>`
                        });
                      form_mp +=`
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Metode Pembayaran 2</label>
                      <input type="" name="id_pembayaran_2_sebelumnya" value="`+data.id_pembayaran[1]+`">
                      <select class="form-control" name="metode_pembayaran_2">`;
                      $.each(data.pilihan_metode_pembayaran, function(k,v){
                        var selected_2 = data.metode_pembayaran_terpilih[1] ==v.id_metode_pembayaran ? 'selected' : '';
                          form_mp +=`<option value='`+v.id_metode_pembayaran+`' `+selected_2+`>`+v.metode_pembayaran+`</option>`
                      });
                          form_mp +=`
                      </select>
                    </div>`;
                }else{
                  form_mp += `
                    <div class="form-group">
                      <label>Metode Pembayaran</label>
                      <input type="hidden" name="id_pembayaran_1_sebelumnya" value="`+data.id_pembayaran[0]+`">
                      <select class="form-control" name="metode_pembayaran_1"  id="metode_pembayaran_1">`;
                      $.each(data.pilihan_metode_pembayaran, function(k,v){
                        var selected_2 = data.metode_pembayaran_terpilih[0] ==v.id_metode_pembayaran ? 'selected' : '';
                          form_mp +=`<option value='`+v.id_metode_pembayaran+`' `+selected_2+`>`+v.metode_pembayaran+`</option>`
                      });
                          form_mp +=`
                      </select>
                    </div>`;
                }

                form_mp +=`<input type="hidden" name="id_transaksi" value="`+id_transaksi+`">
                <input type="hidden" name="pembayaran" value="`+data.transaksi.pembayaran+`">`; 

                $('#edit_metode_pembayaran').find('#form_edit_metode_pembayaran').html(form_mp);



                /*
                $('#edit_metode_pembayaran').find('#pilih_metode').change(function(){
                  var metode_dipilih = $('#edit_metode_pembayaran').find('#pilih_metode').val();




                  console.log(metode_dipilih);



                if (metode_dipilih=='2 Metode Pembayaran') {
                


                 var form_mp_change = `
                    <div class="form-group">
                      <label>Pembayaran</label>
                      <select class="form-control" name="pilih_metode" id="pilih_metode"> 
                       <option>1 Metode Pembayaran</option>
                      <option>2 Metode Pembayaran</option>
                      </select>
                    </div>

                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-6">
                        <label>Metode Pembayaran 1</label>
                        <select class="form-control">
                          
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label>Jumlah Pembayaran 1 </label>
                      </div>
                    </div>
                  </div>
                    `;



                $('#edit_metode_pembayaran').find('#form_edit_metode_pembayaran').html(form_mp_change);
                  alert('2 MP');
                }else{


                    alert(0);

                //    var selected_1 =  `<option>1 Metode Pembayaran</option>`;
                // var selected_2 = `<option selected>2 Metode Pembayaran</option>`;


                //  var form_mp_change = `
                //       <input type="hidden" name="id_pembayaran_1_sebelumnya" value="`+data.id_pembayaran[0]+`">
                //     <div class="form-group">
                //       <label>Pembayaran</label>
                //       <select class="form-control" name="pilih_metode" id="pilih_metode"> 
                //         `+selected_1 + selected_2+`
                //       </select>
                //     </div>`;




                //   form_mp_change += `
                //     <div class="form-group">
                //       <label>Metode Pembayaran</label>
                //       <input type="hidden" name="id_pembayaran_1_sebelumnya" value="`+data.id_pembayaran[0]+`">
                //       <select class="form-control" name="metode_pembayaran_1"  id="metode_pembayaran_1">`;
                //       $.each(data.pilihan_metode_pembayaran, function(k,v){
                //         var selected_2 = data.metode_pembayaran_terpilih[0] ==v.id_metode_pembayaran ? 'selected' : '';
                //           form_mp_change +=`<option value='`+v.id_metode_pembayaran+`' `+selected_2+`>`+v.metode_pembayaran+`</option>`
                //       });
                //           form_mp_change +=`
                //       </select>
                //     </div>`;
                }







                });


                */
                var tombol_simpan = `
                    <button class="btn btn-outline-info btn-sm" onclick="simpanedit_metode_pembayaran()" type="button">Simpan Perubahan</button>`;
                $('#edit_metode_pembayaran').find('#form_edit_metode_pembayaran').append(tombol_simpan);
              },
              error : function(){
                alert('ee');
                
              }
            });
      

}

</script>