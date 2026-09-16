<style>
  .font_laporan{
    font-size:<?php echo $print_setting['preview_font_size_content'] ?>px;
    font-family: <?php echo $print_setting['preview_font_family'] ?>;
  }



  .header{
    font-size:<?php echo $print_setting['preview_font_size_header'] ?>px;
    font-family: <?php echo $print_setting['preview_font_family'] ?>;
  }
  .footer{
    font-size:<?php echo $print_setting['preview_font_size_footer'] ?>px;
    font-family: <?php echo $print_setting['preview_font_family'] ?>;
  }
</style>


<table class="header" width="100%">
  <tr>
    <td colspan="3">
      <center><img src="<?php echo base_url('assets/logo.png') ?>" width="80px"> </center>
      <br><br>
    </td>
  </tr>
  <tr>
    <td valign="top">No Transaksi</td>
    <td valign="top">:</td>
    <td valign="top"> <?php echo $transaksi['no_transaksi'] ?> </td>
  </tr>
  <tr>
    <td valign="top">Date</td>
    <td valign="top">:</td>
    <td valign="top"> <?php echo $transaksi['tgl_transaksi'].'<br>'.$transaksi['jam_transaksi'] ?> </td>
  </tr>
  <tr>
    <td valign="top">Kasir</td>
    <td valign="top">:</td>
    <td valign="top"> <?php echo $transaksi['kasir'] ?> </td>
  </tr>
  <tr>
    <td colspan="3">[<?php echo $transaksi['status'] ?>]</td>
 
  </tr>
</table>


<hr>


<table class="header" width="100%">
  <tr>
    <td valign="top">Status</td>
    <td valign="top">:</td>
    <td valign="top"><?php echo $transaksi['kategori_transaksi'].' - Lapangan '.$transaksi['lapangan']; ?> </td>
  </tr>
  <tr>
    <td valign="top">Nama</td>
    <td valign="top">:</td>
    <td valign="top"><?php echo $transaksi['nama']; ?> </td>
  </tr>
  <tr>
    <td valign="top">No HP</td>
    <td valign="top">:</td>
    <td valign="top"><?php echo $transaksi['no_hp']; ?> </td>
  </tr>

</table>

<hr>  

<table class="font_laporan table" width="100%"> 

    <tr>
      <td><?php echo $transaksi['fasilitas'] ?></td>
      <td align="right" valign="bottom"></td>
    </tr>

    <?php foreach ($produk as $k => $v) { ?>
    <tr>
      <td><?php echo $v['tgl_main'].' jam '.$v['jam_main'] ?></td>
      <td align="right" valign="bottom"><?php echo number_format($v['biaya']) ?></td>
    </tr>
    <?php } ?>
  <?php 
  $total_semua = $transaksi['total']; ?>
  
   </table>
   <hr>



   <table class="font_laporan table" width="100%">
    <tr>
      <td valign="top">Sub Total</td>
      <td align="right" valign="top"><?php echo number_format($total_semua) ?></td>
    </tr>

     <?php 
   if ($transaksi['kategori_potongan']=='Kerjasama' || $transaksi['kategori_potongan']=='Diskon') {
                        if ($transaksi['jenis_potongan']=='Persentase') {
                          $potongan = $transaksi['rp_nilai_diskon'];
                          $show_potongan = $transaksi['besar_diskon'].'% ['.number_format($potongan).']';
                        }else{
                          $potongan = $transaksi['besar_diskon'] ;
                          $show_potongan = number_format($potongan);

                        }

                        $caption_potongan = 'Diskon<br><small>'.$transaksi['kategori_potongan'].' - '.$transaksi['nama_diskon'].'</small>';
                      }else if ($transaksi['kategori_potongan']=='Student Card') {
                        if ($transaksi['jenis_potongan']=='Persentase') {
                          $potongan = $transaksi['rp_nilai_diskon'];
                          $show_potongan = $transaksi['besar_diskon'].'% ['.number_format($potongan).']';
                        }else{
                          $potongan = $transaksi['besar_diskon'] ;
                          $show_potongan = number_format($potongan);

                        }
                        $caption_potongan = 'Diskon '.$transaksi['kategori_potongan'].'<br><small>'.$transaksi['nama_diskon'].'</small>';
                      }else{
                        $potongan =0;
                        $show_potongan ='';
                        $caption_potongan = '';
                      }

                        $grand_total =$total_semua - $potongan ;


 ?>

 <?php if ($transaksi['kategori_potongan']!='') {  ?>
    <tr>
      <td valign="top"><?php echo $caption_potongan ?></td>
      <td align="right" valign="top"><?php echo $show_potongan ?></td>
    </tr>
  <?php } 
      $sisa_pembayaran = $grand_total - ($transaksi['dp'] == '' ? 0 : $transaksi['dp']);
  ?>
  <tr>
      <td valign="top">Grand Total</td>
      <td align="right" valign="top"><?php echo number_format($grand_total) ?></td>
    </tr>


    <?php   if ($transaksi['dp']!='') { 
      $sisa_pembayaran = $grand_total - ($transaksi['dp'] == '' ? 0 : $transaksi['dp']);
      $kembalian = $transaksi['kembalian'] == '' ? 0 : $transaksi['kembalian'];
      ?>
  <tr>
      <td valign="top">DP</td>
      <td align="right" valign="top"><?php echo number_format($transaksi['dp'] ) ?></td>
    </tr>
  <tr>
      <td valign="top">Sisa Pembayaran</td>
      <td align="right" valign="top"><?php echo number_format($sisa_pembayaran) ?></td>
    </tr>
  <tr>
      <td colspan="3"><hr></td>
    </tr>
  <?php   }else{

      // $kembalian = $transaksi['dibayar'] - $grand_total;
  } ?>



    <?php if ($transaksi['dibayar']=='') { ?>


          <?php if ($transaksi['pembayaran']=='1 Metode Pembayaran') { ?>
    <tr>
      <td>Pembayaran DP Via</td>
      <td align="right"> <?php echo $metode_pembayaran[$transaksi['id_metode_pembayaran']] ?> </td>
    </tr>
    <tr>
      <td>DP (<?php echo $metode_pembayaran[$transaksi['id_metode_pembayaran']] ?>)</td>
      <td align="right">  <?php echo number_format($transaksi['dp'] == '' ? 0 : $transaksi['dp']) ?> </td>
    </tr>
   
  <tr>
      <td valign="top">Sisa Pembayaran</td>
      <td align="right" valign="top"><?php echo number_format($transaksi['sisa_pembayaran'] ) ?></td>
    </tr>
  <?php }else{ ?>
     <tr>
      <td>Pembayaran DP Via</td>
      <td align="right"> 
        <?php 
          $pecah_mp = explode(',', $transaksi['id_metode_pembayaran'] );
          $pecah_dibayar = explode(',', $transaksi['dp'] );
          $kumpul_mp = [];
          foreach ($pecah_mp as $k => $v) {
            array_push($kumpul_mp, $metode_pembayaran[$v]);
          }
          echo join(',',$kumpul_mp);
        ?> 
      </td>
    </tr>
    <?php 
    $total_pembayaran = 0;
      foreach ($pecah_mp as $k => $v) { 
        $total_pembayaran += $pecah_dibayar[$k];
        ?>
          <tr>
          <td><?php echo $metode_pembayaran[$v] ?></td>
          <td align="right">  <?php echo number_format($pecah_dibayar[$k]) ?> </td>
        </tr>
     <?php  } ?> 

    <tr>
      <td>Total DP</td>
      <td align="right">  <?php echo number_format($total_pembayaran) ?> </td>
    </tr>
    
  <tr>
      <td valign="top">Sisa Pembayaran</td>
      <td align="right" valign="top"><?php echo number_format($transaksi['sisa_pembayaran'] ) ?></td>
    </tr>
    <?php } ?>






    <?php }else{ ?>


          <?php if ($transaksi['pembayaran']=='1 Metode Pembayaran') { ?>
    <tr>
      <td>Pembayaran Via</td>
      <td align="right"> <?php echo $metode_pembayaran[$transaksi['id_metode_pembayaran']] ?> </td>
    </tr>
    <tr>
      <td><?php echo $metode_pembayaran[$transaksi['id_metode_pembayaran']] ?></td>
      <td align="right">  <?php echo number_format($transaksi['dibayar'] == '' ? 0 : $transaksi['dibayar']) ?> </td>
    </tr>
    <tr>
      <td>Change</td>
      <td align="right">  <?php echo number_format($transaksi['kembalian'] == '' ? 0 : $transaksi['kembalian']) ?> </td>
    </tr>
  <?php }else{ ?>
     <tr>
      <td>Pembayaran Via</td>
      <td align="right"> 
        <?php 
          $pecah_mp = explode(',', $transaksi['id_metode_pembayaran'] );
          $pecah_dibayar = explode(',', $transaksi['dibayar'] );
          $kumpul_mp = [];
          foreach ($pecah_mp as $k => $v) {
            array_push($kumpul_mp, $metode_pembayaran[$v]);
          }
          echo join(',',$kumpul_mp);
        ?> 
      </td>
    </tr>
    <?php 
    $total_pembayaran = 0;
      foreach ($pecah_mp as $k => $v) { 
        $total_pembayaran += $pecah_dibayar[$k];
        ?>
          <tr>
          <td><?php echo $metode_pembayaran[$v] ?></td>
          <td align="right">  <?php echo number_format($pecah_dibayar[$k]) ?> </td>
        </tr>
     <?php  } ?> 

    <tr>
      <td>Total Pembayaran</td>
      <td align="right">  <?php echo number_format($total_pembayaran) ?> </td>
    </tr>
    <tr>
      <td>Change</td>
      <td align="right">  <?php echo number_format($transaksi['kembalian'] == '' ? 0 : $transaksi['kembalian']) ?> </td>
    </tr>
    <?php } ?>



    <?php } ?>

  


  
  
</table>

  

  <center>
<p class="footer" style="margin-top : -10px; text-align: center;">
  Thank You for Your Visit <br><br>
  Jl. Gajah Mada Kelurahan No.105 B, Gn. Pangilun,  Kec. Padang Utara, Kota Padang, Sumatera Barat 25173 <br>
  Telp / WA : 0811-6664-666
</p>
</center>
