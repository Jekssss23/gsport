<head>
  
    <link rel="stylesheet" href="<?php echo   base_url('assets/user_template/dist/') ?>assets/css/base.min.css">
</head>

<?php foreach ($akun_pendapatan as $k => $v) { 
   if ($v['id_akun_pendapatan'] % 2 == 0) {
        $float =  'float:right'; 
        $clearfix = '<div style="clear:both"></div>';
    }else{
        $float =  'float:left'; // Menampilkan bilangan genap
        $clearfix = '';

    }
    ?>
<div class=" mb-3" style="border:solid; border-width: 1px; width:45%;  <?php echo $float ?>">
                                        <div class="card-body">
                                          <b><?php echo $v['nama_akun'] ?></b>

                                          <?php if ($v['id_akun_pendapatan']=='1') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>Futsal Harian</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Futsal Bulanan</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Futsal Turnamen</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                            </table>
                                          <?php }
                                          elseif ($v['id_akun_pendapatan']=='2') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>Badminton Harian</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Badminton Bulanan</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Badminton Turnamen</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                            </table>
                                          <?php } 
                                          elseif ($v['id_akun_pendapatan']=='3') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>Swimming Harian</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Swimming Bulanan</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Swimming Spesial</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Swimming Pelajar</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Swimming Club</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                            </table>
                                          <?php }
                                          elseif ($v['id_akun_pendapatan']=='4') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>Gym Harian</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Gym Bulanan</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Registrasi Gym</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                            </table>
                                          <?php }
                                          elseif ($v['id_akun_pendapatan']=='5') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>Student Card</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                             
                                            </table>
                                          <?php }
                                          elseif ($v['id_akun_pendapatan']=='6') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>F & B</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                             
                                            </table>
                                          <?php }
                                          elseif ($v['id_akun_pendapatan']=='7') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>Proshop</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                             
                                            </table>
                                          <?php }
                                          elseif ($v['id_akun_pendapatan']=='8') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>Les Futsal Academy</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Les Silat Harimau</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Les Renang</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                              <tr>
                                                <td>Les Aikido</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                             
                                            </table>
                                          <?php } 
                                          elseif ($v['id_akun_pendapatan']=='9') { ?>
                                            <table class="table mt-3">
                                              <tr>
                                                <td>Pendapatan lain lain</td>
                                                <td>:</td>
                                                <td> ?? </td>
                                              </tr>
                                             
                                             
                                            </table>
                                          <?php } ?>
                                          </div>
                                     
                                    </div>

                                    <?php echo $clearfix ?>
<?php } ?>