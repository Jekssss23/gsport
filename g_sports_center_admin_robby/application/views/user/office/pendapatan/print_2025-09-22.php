<head>
  
    <link rel="stylesheet" href="<?php echo   base_url('assets/user_template/dist/') ?>assets/css/base.min.css">
</head>

<p style="text-align:center"><?php echo $judul ?></p>
<?php   

$kumpul_metode_pembayaran = [];
$kumpul_total_pemasukan_per_metode = [];
$kumpul_total_pengeluaran_per_metode = [];
foreach (metode_pembayaran() as $k => $v) {
   $kumpul_metode_pembayaran[$v['id_metode_pembayaran']] = $v['metode_pembayaran'];
   $data = [
    'id_metode_pembayaran' =>$v['id_metode_pembayaran'],
    'nilai' => [],

   ];
   $kumpul_total_pemasukan_per_metode[$k] = $data;
   $kumpul_total_pengeluaran_per_metode[$k] = $data;
   // array_push($kumpul_total_pemasukan_per_metode, $data);
 } ?>
<table class="table table-striped table-bordered mt-3">
    <tr>
      <th rowspan="2" width="100px">Fasilitas</th>
      <th colspan="<?php echo count($kumpul_metode_pembayaran) +1 ?>">Pemasukan</th>
      <th colspan="<?php echo count($kumpul_metode_pembayaran) +1 ?>">Pengeluaran</th>
      <th rowspan="2"  style="color:blue">Total</th>
    </tr>
    <tr>
      <?php foreach ($kumpul_metode_pembayaran as $k => $v) { ?>
      <th><?php echo  $v ?></th>
      <?php } ?>
      <th style="color:green">Total</th>
      <?php foreach ($kumpul_metode_pembayaran as $k => $v) { ?>
      <th><?php echo  $v ?></th>
      <?php } ?>
      <th  style="color:red">Total</th>
     
    </tr>




<?php 
  $total_pemasukan_semua = 0;
  $total_pengeluaran_semua = 0;
  $total_pendapatan_semua = 0;
foreach ($akun_pendapatan as $k => $v) {  
    $id_akun_pendapatan = $v['id_akun_pendapatan']; 
  ?>
  <tr>
      <td><?php echo $v['nama_akun'] ?></td>
    <?php 
    $kumpul_pemasukan = [];
    $kumpul_pengeluaran = [];
    $index = 0;
    foreach($kumpul_metode_pembayaran as $k_mp =>$v_mp){
        $id_metode_pembayaran = $k_mp;

    //  kodingan lama (tabel pendapatan)
    /*if ($filter=='bulanan') {
      $q_pendapatan = $this->db->query("SELECT  sum(IF(p.kategori='Masuk', p.nilai, 0)) as pemasukan,sum(IF(p.kategori='Keluar', p.nilai, 0)) as pengeluaran  from pendapatan p  where id_akun_pendapatan='$id_akun_pendapatan' and (month(tgl_transaksi) ='$bulan' and year(tgl_transaksi) ='$tahun') and p.status='Settlement' and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();
    }
    elseif ($filter=='tahunan') {
      $q_pendapatan = $this->db->query("SELECT  sum(IF(p.kategori='Masuk', p.nilai, 0)) as pemasukan,sum(IF(p.kategori='Keluar', p.nilai, 0)) as pengeluaran  from pendapatan p where id_akun_pendapatan='$id_akun_pendapatan' and (year(tgl_transaksi) ='$tahun') and p.status='Settlement' and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();
    }
    elseif ($filter=='periode') {
    $jam_mulai = '00:00:00';
    $jam_akhir = '23:59:59';
      $q_pendapatan = $this->db->query("SELECT  sum(IF(p.kategori='Masuk', p.nilai, 0)) as pemasukan,sum(IF(p.kategori='Keluar', p.nilai, 0)) as pengeluaran  from pendapatan p where id_akun_pendapatan='$id_akun_pendapatan' and ( tgl_transaksi BETWEEN '$tgl_awal $jam_mulai' and '$tgl_akhir $jam_akhir') and p.status='Settlement'  and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();
    }else{
      $q_pendapatan = $this->db->query("SELECT sum(IF(p.kategori='Masuk', p.nilai, 0)) as pemasukan,sum(IF(p.kategori='Keluar', p.nilai, 0)) as pengeluaran  from pendapatan p  where id_akun_pendapatan='$id_akun_pendapatan' and tgl_transaksi like '%$tgl%' and p.status='Settlement' and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();

    }
  */

    // kodingan baru (tabel pembayaran)
    if ($filter=='bulanan') {
      $q_pendapatan = $this->db->query("SELECT  sum(pendapatan) as pemasukan from pembayaran p  where id_akun_pendapatan='$id_akun_pendapatan' and (month(tgl_transaksi) ='$bulan' and year(tgl_transaksi) ='$tahun') and p.status='Settlement' and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();
    }
    elseif ($filter=='tahunan') {
      $q_pendapatan = $this->db->query("SELECT  sum(IF(p.kategori='Masuk', p.nilai, 0)) as pemasukan,sum(IF(p.kategori='Keluar', p.nilai, 0)) as pengeluaran  from pendapatan p where id_akun_pendapatan='$id_akun_pendapatan' and (year(tgl_transaksi) ='$tahun') and p.status='Settlement' and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();
    }
    elseif ($filter=='periode') {
    $jam_mulai = '00:00:00';
    $jam_akhir = '23:59:59';
      $q_pendapatan = $this->db->query("SELECT  sum(IF(p.kategori='Masuk', p.nilai, 0)) as pemasukan,sum(IF(p.kategori='Keluar', p.nilai, 0)) as pengeluaran  from pendapatan p where id_akun_pendapatan='$id_akun_pendapatan' and ( tgl_transaksi BETWEEN '$tgl_awal $jam_mulai' and '$tgl_akhir $jam_akhir') and p.status='Settlement'  and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();
    }else{
      // $q_pendapatan = $this->db->query("SELECT sum(p2.pendapatan) as pemasukan from pendapatan p1  
      //   left join pembayaran p2 on p1.id_transaksi = p2.id_transaksi
      //   where p1.id_akun_pendapatan='$id_akun_pendapatan' and p1.tgl_transaksi like '%$tgl%' and p1.status='Settlement' and p2.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();
      $q_pendapatan = $this->db->query("SELECT sum(IF(p.kategori='Masuk', p.nilai, 0)) as pemasukan,sum(IF(p.kategori='Keluar', p.nilai, 0)) as pengeluaran  from pendapatan p  where id_akun_pendapatan='$id_akun_pendapatan' and tgl_transaksi like '%$tgl%' and p.status='Settlement' and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();

    }

      $nilai_pemasukan = $q_pendapatan['pemasukan'] == '' ? 0 : $q_pendapatan['pemasukan'];
      $nilai_pengeluaran = $q_pendapatan['pengeluaran'] == '' ? 0 : $q_pendapatan['pengeluaran'];


      array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $nilai_pemasukan);
      array_push($kumpul_total_pengeluaran_per_metode[$index]['nilai'], $nilai_pengeluaran);
      $index++;

      array_push($kumpul_pemasukan, $nilai_pemasukan ); 
      array_push($kumpul_pengeluaran, $nilai_pengeluaran); 

?>
     
<?php
    }


$total_pemasukan = array_sum($kumpul_pemasukan);
$total_pengeluaran = array_sum($kumpul_pengeluaran);
$total_pendapatan = $total_pemasukan - $total_pengeluaran ;


$total_pemasukan_semua += $total_pemasukan;
$total_pengeluaran_semua += $total_pengeluaran;
$total_pendapatan_semua += $total_pendapatan  ;
    foreach ($kumpul_pemasukan as $k => $v) { ?>
      <td><?php echo number_format($v) ?></td>
    <?php }
  ?>
      <td style="color:green"><?php echo number_format($total_pemasukan) ?></td>


  <?php   foreach ($kumpul_pengeluaran as $k => $v) { ?>
      <td><?php echo number_format($v) ?></td>
    <?php }
    
   ?>
      <td  style="color:red"><?php echo number_format($total_pengeluaran) ?></td>
      <td  style="color:blue"><?php echo number_format($total_pendapatan) ?> </td>
    </tr>
<?php } ?>

<tr>
  <td>Total</td>
  <?php   foreach ($kumpul_total_pemasukan_per_metode as $k => $v) {  ?>
    <td> <?php echo number_format(array_sum($v['nilai'])) ?> </td>
  <?php   } ?>
  <td style="color:green"> <?php echo number_format($total_pemasukan_semua) ?> </td>
  <?php   foreach ($kumpul_total_pengeluaran_per_metode as $k => $v) {  ?>
    <td> <?php echo number_format(array_sum($v['nilai'])) ?> </td>
  <?php   } ?>
  <td style="color:red"> <?php echo number_format($total_pengeluaran_semua) ?> </td>
  
  <td style="color:blue"> <?php echo number_format($total_pendapatan_semua) ?> </td>
 
</tr>
<tr>
  <td>Pendapatan Pajak</td>
  <td colspan="<?php echo (count($kumpul_metode_pembayaran) +1) *2  ?>"></td>
  <td style="color:blue">
    <?php 
    $pendapatan_akhir= $total_pendapatan_semua * 0.6;

    echo number_format($pendapatan_akhir); ?>
  </td>
</tr>
  </table>
