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
  <tr>
    <td valign="top">Kasir</td>
    <td valign="top">:</td>
    <td valign="top"> <?php echo $transaksi['kasir'] ?> </td>
  </tr>
  <tr>
    <td colspan="3">[<?php echo $status_print ?>]</td>
 
  </tr>
</table>


<hr>


<table class="header" width="100%">
  <tr>
    <td valign="top">Nama</td>
    <td valign="top">:</td>
    <td valign="top"><?php echo $transaksi['nama_pengunjung']; ?> </td>
  </tr>
  <tr>
    <td valign="top">No HP</td>
    <td valign="top">:</td>
    <td valign="top"><?php echo $transaksi['no_hp']; ?> </td>
  </tr>
  <tr>
    <td valign="top">Usia</td>
    <td valign="top">:</td>
    <td valign="top"><?php echo $transaksi['usia']; ?> Tahun </td>
  </tr>
</table>

<hr>  

<table class="font_laporan table" width="100%"> 
  <?php 
  $total_semua = 0;
  foreach ($produk as $k => $v) { 


    $total_semua +=$v['nilai'];
    ?>
    <tr>
      <td><?php echo $v['item_transaksi'] ?></td>
      <td align="right" valign="bottom"><?php echo number_format($v['nilai']) ?></td>
    </tr>
  <?php } ?>

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

                        $caption_potongan = 'Diskon<br><small>'.$transaksi['nama_diskon'].'</small>';
                      }else if ($transaksi['kategori_potongan']=='Student Card') {
                        if ($transaksi['jenis_potongan']=='Persentase') {
                          $potongan = $transaksi['rp_nilai_diskon'];
                          $show_potongan = $transaksi['besar_diskon'].'% ['.number_format($potongan).']';
                        }else{
                          $potongan = 435;//$transaksi['besar_diskon'] ;
                          $show_potongan = 435;//number_format($potongan);

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
  <?php } ?>
  <tr>
      <td valign="top">Grand Total</td>
      <td align="right" valign="top"><?php echo number_format($grand_total) ?></td>
    </tr>
    <tr>
      <td>Pembayaran Via</td>
      <td align="right"> <?php echo $transaksi['metode_pembayaran'] ?> </td>
    </tr>
    <tr>
      <td>Cash</td>
      <td align="right">  <?php echo number_format($transaksi['dibayar'] == '' ? 0 : $transaksi['dibayar']) ?> </td>
    </tr>
    <tr>
      <td>Change</td>
      <td align="right">  <?php echo number_format($transaksi['kembalian'] == '' ? 0 : $transaksi['kembalian']) ?> </td>
    </tr>
</table>

  

  <center>
<p class="footer" style="margin-top : -10px; text-align: center;">
  Thank You for Your Visit <br><br>
  Jl. Gajah Mada Kelurahan No.105 B, Gn. Pangilun,  Kec. Padang Utara, Kota Padang, Sumatera Barat 25173 <br>
  Telp / WA : 0811-6664-666
</p>
</center>
