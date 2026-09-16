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
  <?php   if ($transaksi['status'] == 'Preview') { ?>
  <tr>
    <td valign="top">
      <?php echo in_array(3, $this->session->userdata('id_hak_akses')) ? 'Kasir' : 'Di Input Oleh'; ?>
    </td>
    <td valign="top">:</td>
    <td valign="top"> <?php echo $transaksi['status'] == 'Preview' ? $transaksi['penginput'] : $transaksi['kasir'] ?> </td>
  </tr>
     
  <?php }else{ ?>

  <tr>
    <td valign="top">
     Di Input Oleh
    </td>
    <td valign="top">:</td>
    <td valign="top"> <?php echo $transaksi['penginput']  ?> </td>
  </tr>
  <tr>
    <td valign="top">
     Kasir
    </td>
    <td valign="top">:</td>
    <td valign="top"> <?php echo $transaksi['kasir']  ?> </td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="3">[<?php echo $transaksi['status'] ?>]</td>
 
  </tr>
</table>


<hr>



<table class="font_laporan table" width="100%" border="0" style="border-collapse:collapse;"> 
   <tr>
      <td colspan="2"><?php echo $transaksi['keterangan'] ?></td>
      <!-- <td align="right" valign="bottom"></td> -->
    </tr>

    <?php 
    $total_semua=0;
    foreach ($produk as $k => $v) {
    $total = $v['qty'] * $v['harga_satuan']; 
      ?>
   <tr>
      <td><?php echo $v['qty'].' - '.$v['nama_peralatan'] ?></td>
      <td align="right" valign="bottom"><?php echo number_format($total) ?></td>
    </tr>
 
    <?php 
    $total_semua +=$total;

        } 
    ?>
   </table>
   <hr>



   <table class="font_laporan table" width="100%">
    <tr>
      <td valign="top">Sub Total</td>
      <td align="right" valign="top"><?php echo number_format($total_semua) ?></td>
    </tr>

     <?php 
     ?>


  <tr>
      <td valign="top">Grand Total</td>
      <td align="right" valign="top"><?php echo number_format($total_semua) ?></td>
    </tr>
    <?php if ($transaksi['status']=='Settlement') { ?>
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
            echo join(',' , $kumpul_mp);
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
