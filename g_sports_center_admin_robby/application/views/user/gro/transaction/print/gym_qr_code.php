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
    <td colspan="3">
      <center><img src="<?php echo base_url('file/qrcode/'.$transaksi['qrcode']) ?>" width="100%"> </center>
      
    </td>
  </tr>
  
</table>



  <center>
<p class="footer" style="margin-top : -10px; text-align: center;">
  Scan Qr code ini pada scanner
</p>
</center>
