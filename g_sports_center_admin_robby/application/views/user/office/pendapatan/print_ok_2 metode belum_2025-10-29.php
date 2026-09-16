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
 } ?>
<table class="table table-striped table-bordered mt-3">
    <tr>
      <th rowspan="2" width="100px">Fasilitas</th>
      <th colspan="<?php echo count($kumpul_metode_pembayaran) ?>" align="center">Pendapatan</th>
      <th colspan="2" align="center">Total</th>
    </tr>
    <tr>
      <?php foreach ($kumpul_metode_pembayaran as $k => $v) { ?>
      <th><?php echo  $v ?></th>
      <?php } ?>
      <th style="color:green">Pendapatan Murni</th>
      <th style="color:blue">Pendapatan Pajak (40%)</th>
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
    // kodingan baru (tabel pembayaran)
    if ($filter=='bulanan') {
      $q_transaksi = $this->db->query("SELECT t.id_transaksi, t.id_akun_pendapatan, t.pembayaran, t.fasilitas, t.tagihan from transaksi t where t.id_akun_pendapatan like '%$id_akun_pendapatan%' and (month(tgl_transaksi) ='$bulan' and year(tgl_transaksi) ='$tahun') and t.status!='Preview' and t.id_metode_pembayaran like '%$id_metode_pembayaran%'")->result_array();

    }
    elseif ($filter=='tahunan') {
   
      $q_transaksi = $this->db->query("SELECT t.id_transaksi, t.id_akun_pendapatan, t.pembayaran, t.fasilitas, t.tagihan from transaksi t where t.id_akun_pendapatan like '%$id_akun_pendapatan%' and (year(tgl_transaksi) ='$tahun') and t.status!='Preview' and t.id_metode_pembayaran like '%$id_metode_pembayaran%'")->result_array();

    }
    elseif ($filter=='periode') {
    $jam_mulai = '00:00:00';
    $jam_akhir = '23:59:59';
      // $q_pendapatan = $this->db->query("SELECT  sum(IF(p.kategori='Masuk', p.nilai, 0)) as pemasukan,sum(IF(p.kategori='Keluar', p.nilai, 0)) as pengeluaran  from pendapatan p where id_akun_pendapatan='$id_akun_pendapatan' and ( tgl_transaksi BETWEEN '$tgl_awal $jam_mulai' and '$tgl_akhir $jam_akhir') and p.status='Settlement'  and p.id_metode_pembayaran='$id_metode_pembayaran'")->row_array();


      $q_transaksi = $this->db->query("SELECT t.id_transaksi, t.id_akun_pendapatan, t.pembayaran, t.fasilitas, t.tagihan from transaksi t where t.id_akun_pendapatan like '%$id_akun_pendapatan%' and  ( t.tgl_transaksi BETWEEN '$tgl_awal $jam_mulai' and '$tgl_akhir $jam_akhir') and t.status!='Preview' and t.id_metode_pembayaran like '%$id_metode_pembayaran%'")->result_array();



    }else{
      $q_transaksi = $this->db->query("SELECT t.id_transaksi, t.id_akun_pendapatan, t.pembayaran, t.fasilitas, t.tagihan from transaksi t where t.id_akun_pendapatan like '%$id_akun_pendapatan%' and t.tgl_transaksi like '%$tgl%' and t.status!='Preview' and t.id_metode_pembayaran like '%$id_metode_pembayaran%'")->result_array();
    }
      $hitung_nilai_pemasukan = 0;
      foreach ($q_transaksi as $k_t => $v_t) {
        $id_transaksi = $v_t['id_transaksi'];
        if ($v_t['pembayaran']=='1 Metode Pembayaran') {
          if ($v_t['fasilitas']=='Gym - Special Membership') {
            // $q_pendapatan = 
              $q_pendapatan = $this->db->query("SELECT p.nilai, p.kategori, p.id_akun_pendapatan  from pendapatan p  where p.id_transaksi='$id_transaksi'")->result_array();
              $hitung_pemasukan_gym = 0;
              $hitung_pengeluaran_gym = 0;
              $hitung_pemasukan_swim = 0;
              $hitung_pengeluaran_swim = 0;
              foreach ($q_pendapatan as $k_pd => $v_pd) {
                if ($v_pd['kategori']=='Masuk') {
                  if ($v_pd['id_akun_pendapatan']==3) {
                    $hitung_pemasukan_swim +=$v_pd['nilai'];
                  }else{
                    $hitung_pemasukan_gym +=$v_pd['nilai'];
                  }
                }else{
                  if ($v_pd['id_akun_pendapatan']==3) {
                    $hitung_pengeluaran_swim +=$v_pd['nilai'];
                  }else{
                    $hitung_pengeluaran_gym +=$v_pd['nilai'];
                  }
                }
              }
            $hasil_gym = $hitung_pemasukan_gym - $hitung_pengeluaran_gym  ;
            $hasil_swim = $hitung_pemasukan_swim - $hitung_pengeluaran_swim  ;
            if ($id_akun_pendapatan == 3) {
              // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_swim);
              $hitung_nilai_pemasukan += $hasil_swim;
            }else{
              // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_gym);
              $hitung_nilai_pemasukan += $hasil_gym ;
            }
          }
          else if($v_t['fasilitas']=='Futsal - Turnamen' ){

              $hitung_pemasukan_futsal = 0;
              $hitung_pengeluaran_futsal = 0;
              $hitung_pemasukan_lainlain = 0;
              $hitung_pengeluaran_lainlain = 0;

              $q_pendapatan = $this->db->query("SELECT p.nilai, p.kategori, p.id_akun_pendapatan  from pendapatan p  where p.id_transaksi='$id_transaksi'")->result_array();
              foreach ($q_pendapatan as $k_pd => $v_pd) {
                if ($v_pd['kategori']=='Masuk') {
                  if ($v_pd['id_akun_pendapatan']==1) {
                    $hitung_pemasukan_futsal +=$v_pd['nilai'];
                  }else{
                    $hitung_pemasukan_lainlain +=$v_pd['nilai'];
                  }
                }else{
                  if ($v_pd['id_akun_pendapatan']==1) {
                    $hitung_pengeluaran_futsal +=$v_pd['nilai'];
                  }else{
                    $hitung_pengeluaran_lainlain +=$v_pd['nilai'];
                  }
                }
              }
            $hasil_futsal = $hitung_pemasukan_futsal - $hitung_pengeluaran_futsal  ;
            $hasil_lainlain = $hitung_pemasukan_lainlain - $hitung_pengeluaran_lainlain  ;
            if ($id_akun_pendapatan == 1) {
              // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_swim);
              $hitung_nilai_pemasukan += $hasil_futsal;
            }else{
              // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_gym);
              $hitung_nilai_pemasukan += $hasil_lainlain ;
            }
          }
          else if($v_t['fasilitas']=='Badminton - Turnamen' ){

              $hitung_pemasukan_badminton = 0;
              $hitung_pengeluaran_badminton = 0;
              $hitung_pemasukan_lainlain = 0;
              $hitung_pengeluaran_lainlain = 0;

              $q_pendapatan = $this->db->query("SELECT p.nilai, p.kategori, p.id_akun_pendapatan  from pendapatan p  where p.id_transaksi='$id_transaksi'")->result_array();
              foreach ($q_pendapatan as $k_pd => $v_pd) {
                if ($v_pd['kategori']=='Masuk') {
                  if ($v_pd['id_akun_pendapatan']==2) {
                    $hitung_pemasukan_badminton +=$v_pd['nilai'];
                  }else{
                    $hitung_pemasukan_lainlain +=$v_pd['nilai'];
                  }
                }else{
                  if ($v_pd['id_akun_pendapatan']==2) {
                    $hitung_pengeluaran_badminton +=$v_pd['nilai'];
                  }else{
                    $hitung_pengeluaran_lainlain +=$v_pd['nilai'];
                  }
                }
              }
            $hasil_badminton = $hitung_pemasukan_badminton - $hitung_pengeluaran_badminton  ;
            $hasil_lainlain = $hitung_pemasukan_lainlain - $hitung_pengeluaran_lainlain  ;
            if ($id_akun_pendapatan == 2) {
              // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_swim);
              $hitung_nilai_pemasukan += $hasil_badminton;
            }else{
              // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_gym);
              $hitung_nilai_pemasukan += $hasil_lainlain ;
            }
          }

          else{


              $q_pembayaran = $this->db->query("SELECT p.pendapatan, p.id_metode_pembayaran from pembayaran p  where p.id_transaksi='$id_transaksi' group by p.id_pembayaran")->result_array();
            
            foreach ($q_pembayaran as $k_pb => $v_pb) {
              if ($v_pb['id_metode_pembayaran']==$id_metode_pembayaran) {
                  $hitung_nilai_pemasukan += $v_pb['pendapatan'];
                # code...
              }
            }



            
          // $hitung_nilai_pemasukan += $v_t['tagihan'];
          // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hitung_nilai_pemasukan);
          }
        }else{ //if ($v_t['pembayaran']=='1 Metode Pembayaran') { line 97
          
          if ($v_t['fasilitas']=='Gym - Special Membership') {
              $q_pendapatan = $this->db->query("SELECT p.nilai, p.kategori, p.id_akun_pendapatan  from pendapatan p  where p.id_transaksi='$id_transaksi'")->result_array();
              $hitung_pemasukan_gym = 0;
              $hitung_pengeluaran_gym = 0;
              $hitung_pemasukan_swim = 0;
              $hitung_pengeluaran_swim = 0;
              foreach ($q_pendapatan as $k_pd => $v_pd) {
                if ($v_pd['kategori']=='Masuk') {
                  if ($v_pd['id_akun_pendapatan']==3) {
                    $hitung_pemasukan_swim +=$v_pd['nilai'];
                  }else{
                    $hitung_pemasukan_gym +=$v_pd['nilai'];
                  }
                }else{
                  if ($v_pd['id_akun_pendapatan']==3) {
                    $hitung_pengeluaran_swim +=$v_pd['nilai'];
                  }else{
                    $hitung_pengeluaran_gym +=$v_pd['nilai'];
                  }
                }
              }
            $hasil_gym = $hitung_pemasukan_gym - $hitung_pengeluaran_gym  ;
            $hasil_swim = $hitung_pemasukan_swim - $hitung_pengeluaran_swim  ;
            $q_pembayaran = $this->db->query("SELECT p.pendapatan, p.dibayar, p.id_metode_pembayaran   from pembayaran p  where p.id_transaksi='$id_transaksi'")->result_array();
            foreach ($q_pembayaran as $k_pb => $v_pb) {
              if ($v_pb['id_metode_pembayaran']==$id_metode_pembayaran) {
                  if ($id_akun_pendapatan == 3) {
                    if ($k_pb==0) {
                      if ($v_pb['pendapatan'] == $v_pb['dibayar']) {
                        if ($hasil_swim==$v_pb['pendapatan']) {
                          $hitung_nilai_pemasukan += $hasil_swim;
                          // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_swim);

                        }else{
                          if ($hasil_swim > $v_pb['pendapatan']) {
                              $disetor = $v_pb['pendapatan'];
                              $hitung_nilai_pemasukan += $disetor;
                          } else  {
                              $disetor = $hasil_swim;
                              $hitung_nilai_pemasukan += $disetor;
                          }

                          // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $disetor);
                        }
                      }
                    }else{
                      if ($v_pb['pendapatan'] == $v_pb['dibayar']) {
                        if ($hasil_swim==$v_pb['pendapatan']) {
                          $hitung_nilai_pemasukan += $hasil_swim;

                          // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_swim);
                        } 
                        elseif ($hasil_swim > $v_pb['pendapatan']) {
                            $disetor = $v_pb['pendapatan'];
                            $hitung_nilai_pemasukan += $disetor;
                            // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $disetor);
                        } else  {
                            $disetor = $hasil_swim;
                            $hitung_nilai_pemasukan += $disetor;
                            // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $disetor);
                        }
                      }else{
                        $pendapatan_pertama = $q_pembayaran[0]['pendapatan'];
                        if ($pendapatan_pertama > $hasil_swim) {
                            $disetor = 0;
                            $hitung_nilai_pemasukan += $disetor;
                            // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $disetor);
                        }else{
                            $setelah_pendapatan_pertama = $hasil_swim - $pendapatan_pertama;
                            // $disetor = $v_pb['pendapatan'];
                            $hitung_nilai_pemasukan += $setelah_pendapatan_pertama;
                            // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $setelah_pendapatan_pertama);
                        }
                      }
                    }
                  }else{ // if ($id_akun_pendapatan == 3) {
                    if ($k_pb==0) {
                      if ($v_pb['pendapatan'] == $v_pb['dibayar']) {
                        if ($hasil_gym==$v_pb['pendapatan']) {
                          $hitung_nilai_pemasukan += $hasil_gym;
                            // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_gym);
                        }else{
                          if ($hasil_gym > $v_pb['pendapatan']) {
                              $disetor = $v_pb['pendapatan'];
                              $hitung_nilai_pemasukan += $disetor;
                          } else  {
                              $disetor = $hasil_gym;
                              $hitung_nilai_pemasukan += $disetor;
                          }
                            // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $disetor);
                        }
                      }
                    }else{
                      if ($v_pb['pendapatan'] == $v_pb['dibayar']) {
                        if ($hasil_gym==$v_pb['pendapatan']) {
                          $hitung_nilai_pemasukan += $hasil_gym;
                          // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hasil_gym);
                        } 
                        elseif ($hasil_gym > $v_pb['pendapatan']) {
                            $disetor = $v_pb['pendapatan'];
                            $hitung_nilai_pemasukan += $disetor;
                        } else  {
                            $disetor = $hasil_gym;
                            $hitung_nilai_pemasukan += $disetor;
                        }
                            // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $disetor);
                      }else{
                        $pendapatan_pertama = $q_pembayaran[0]['pendapatan'];
                        if ($pendapatan_pertama > $hasil_gym) {
                            $disetor = 0;
                            $hitung_nilai_pemasukan += $disetor;
                        }else{
                            $setelah_pendapatan_pertama = $hasil_gym - $pendapatan_pertama;
                            $disetor = $v_pb['pendapatan'];
                            $hitung_nilai_pemasukan += $setelah_pendapatan_pertama;
                            // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $setelah_pendapatan_pertama);
                        }
                      }
                    }
                  } // end of else of if ($id_akun_pendapatan == 3) {






                # code...
              } // end of if ($v_pb['id_metode_pembayaran']==$id_metode_pembayaran) {
                
              
            } //end foreach ($q_pembayaran as $k_pb => $v_pb) {
          }else{
            $q_pembayaran = $this->db->query("SELECT p.pendapatan, p.id_metode_pembayaran from pembayaran p  where p.id_transaksi='$id_transaksi' group by p.id_pembayaran")->result_array();
            
            foreach ($q_pembayaran as $k_pb => $v_pb) {
              if ($v_pb['id_metode_pembayaran']==$id_metode_pembayaran) {
                  $hitung_nilai_pemasukan += $v_pb['pendapatan'];
                # code...
              }
            }
          }
          // $hitung_nilai_pemasukan += 33;
          // array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $hitung_nilai_pemasukan);

        }
        # code...
      } // end foreach ($q_transaksi as $k_t => $v_t) {

      $nilai_pemasukan = $hitung_nilai_pemasukan ;//$q_pendapatan['pemasukan'] == '' ? 0 : $q_pendapatan['pemasukan'];

      array_push($kumpul_total_pemasukan_per_metode[$index]['nilai'], $nilai_pemasukan);

      $index++;

      array_push($kumpul_pemasukan, $nilai_pemasukan ); 
    

?>
     
<?php
    }


$total_pemasukan = array_sum($kumpul_pemasukan);
$total_pengeluaran = array_sum($kumpul_pengeluaran);
$total_pendapatan = $total_pemasukan - $total_pengeluaran ;




$total_pemasukan_semua += $total_pemasukan;
$total_pendapatan_semua += $total_pendapatan;
    foreach ($kumpul_pemasukan as $k => $v) { ?>
      <td><?php echo number_format($v) ?></td>
    <?php }
  ?>
      <td style="color:green"><?php echo number_format($total_pemasukan) ?></td>
   ?>

      <td>
        <?php 
        $pendapatan_pajak = $total_pemasukan * 0.4;
        echo number_format($pendapatan_pajak); ?>
      </td>
    </tr>
<?php } ?>

<tr>
  <td>Total</td>
  <?php   foreach ($kumpul_total_pemasukan_per_metode as $k => $v) {  ?>
    <td> <?php echo number_format(array_sum($v['nilai'])) ?> </td>
  <?php   } ?>
  <td style="color:green"> <?php echo number_format($total_pemasukan_semua) ?> </td>
   <td style="color:blue">
    <?php 
    $pendapatan_akhir= $total_pendapatan_semua * 0.4;

    echo number_format($pendapatan_akhir); ?>
  </td>

 
</tr>

  </table>
