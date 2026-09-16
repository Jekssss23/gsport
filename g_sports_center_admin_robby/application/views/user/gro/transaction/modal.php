
<div class="modal fade" id="edit_tgll" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Ganti Tanggal Lahir karena Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url('user/gro/transaction/cek_data_member/simpanedit_tgll') ?>" method="post">
              <div class="modal-body" style="overflow-x:  scroll; max-height: 700px">
                
                                            <div class="form-group">
                                              <label>Tanggal Lahir</label>
                                              <div class="row">
                                                <div class="col-md-3">
                                                  <select class="form-control" name="tgll" id="input_tgll">
                                                    <?php for ($i=1; $i <= 31 ; $i++) { ?>
                                                    <option><?php echo $i ?></option>
                                                    <?php } ?>
                                                  </select>
                                                  
                                                </div>
                                                <div class="col-md-6">
                                                  <select class="form-control" name="blll" id="input_blll">
                                                    <?php for ($i=1; $i <= 12 ; $i++) { ?>
                                                    <option value="<?php echo $i ?>"><?php echo bulan_global($i) ?></option>
                                                    <?php } ?>
                                                  </select>
                                                </div>
                                                <div class="col-md-3">
                                                  <select class="form-control" name="thll" id="input_thll">
                                                    <?php for ($i=date('Y'); $i >= 1945 ; $i--) { ?>
                                                    <option><?php echo $i ?></option>
                                                    <?php } ?>
                                                  </select>
                                                  
                                                </div>
                                                
                                              </div>
                                            </div>
                <input type="hidden" name="id_member" id="id_member">



              </div>

              <div class="modal-footer">
                <button class="btn btn-info btn-sm">Simpan</button>
              </div>
            </form>
           
        </div>
    </div>
</div>


<!-- modal konfirmasi member online -->
<div class="modal fade" id="modal_pembayaran_member_online" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Pembayaran <br> <span class="nama"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_konfirmasi_reg_online">
              <div class="modal-body" style="overflow-x:  scroll; max-height: 700px">
               
                  









                  <div id="form_pembayaran">

                  <div class="row">

                      <div class="col-sm-12 col-md-12 col-xl-12 mb-3">

                                              <input type="hidden" name="total" class="form-control harus_dibayar_reg_online" >
                                              <input type="hidden" name="registrasi" class="form-control biaya_regis_reg_online" >
                                              <input type="hidden" name="biaya" class="form-control biaya_member_reg_online" >
                                              <input type="hidden" name="paket_member" class="form-control paket_member" >
                                              <input type="hidden" name="id_member" class="form-control id_member" >
                                              <input type="hidden" name="id_jenis_member" class="form-control id_jenis_member" >
                                              
                                              <input type="hidden" name="akhir_masa_aktif" class="form-control akhir_masa_aktif" >
                                              <input type="hidden" name="opsi_tambah_metode_pembayaran" id="opsi_tambah_metode_pembayaran">


                                              <!-- khusus gym spesial -->
                                          <input type="hidden" name="harga_gym" class="form-control harga_gym" >
                                          <input type="hidden" name="harga_swim" class="form-control harga_swim" >
                                              <!-- khusus gym spesial -->

                                          <!-- untuk diskon -->
                                          <input type="hidden" name="kategori_diskon" id="kategori_diskon" value="">
                                          <input type="hidden" name="id_diskon" id="id_diskon" value="">
                                          <input type="hidden" name="nama_diskon" id="nama_diskon" value="">
                                          <input type="hidden" name="jenis_potongan" id="jenis_potongan" value="">
                                          <input type="hidden" name="besar_potongan" id="besar_potongan" value="">
                                          <input type="hidden" name="rp_nilai_potongan" id="rp_nilai_potongan" value="">
                                          <!-- untuk diskon -->
                                          <!-- untuk pembayaran -->
                                          <input type="hidden" name="simpan_tagihan" id="simpan_tagihan" value="">
                                          <input type="hidden" name="simpan_dibayar" id="simpan_dibayar" value="">
                                          <input type="hidden" name="simpan_kembalian" id="simpan_kembalian" value="">


                                          <input type="hidden" name="id_student_card" id="id_student_card" value="">
                                          <input type="hidden" name="fasilitas" id="fasilitas" value="">
                                          <!-- untuk pembayaran -->




                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-nuted">Total</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="total_reg_online">0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>

              <div class="col-sm-12 col-md-12 col-xl-12">
                         <div class="form-group">
                           <label>Potongan Harga</label>
                           <select class="form-control" name="potongan_harga" id="potongan_harga">
                           
                              <option value="">--Tanpa Potongan Harga--</option>
                              <option value="Student Card">Student Card</option>
                              <?php foreach (diskon() as $k => $v) { 
                                $potongan = $v['jenis_potongan'] == 'Persentase' ? $v['besar_diskon'].'%' : 'Rp. '.number_format($v['besar_diskon']); 
                                ?>
                                <option value="<?php echo $v['kategori'].'|'.$v['nama_diskon'].'|'.$v['id_diskon'].'|'.$v['jenis_potongan'].'|'.$v['besar_diskon'] ?>"><?php echo $v['kategori'].' | '.$v['nama_diskon'].' | Diskon : '.$potongan ?></option>
                              <?php } ?>
                             
                           </select>
                         </div>
                      </div>
              <div class="col-sm-12 col-md-12 col-xl-12" id="">
                         <div class="form-group form_student_card" style="display: none">
                           <label>Student Card</label>
                                          <div class="pull-right ">
                                            <a class="btn-shadow btn btn-outline-info btn-sm" onclick="show_student_card()">Pilih Student Card</a>
                                          </div>
                          </div>
                         <div class="form-group form_student_card mt-3" style="display: none">
                            <div class="alert alert-info alert_student_card  mt-3">Pilih data student card </div>
                          <div class="data_student_card" style="display: none">
                            <table class="table identitas_student_card">
                              <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td  id="student_card_nama"></td>
                              </tr>
                              <tr>
                                <td>Alamat</td>
                                <td>:</td>
                                <td id="student_card_alamat"></td>
                              </tr>
                              <tr>
                                <td>No HP</td>
                                <td>:</td>
                                <td id="student_card_nohp"></td>
                              </tr>
                            </table>
                          </div>
                         </div>
                      </div>

                        <div class="col-sm-6 col-md-6 col-xl-6 mb-3">


                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100" id="caption_potongan">
                                    <div class="widget-content-outer">
                                       
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-2"><small class="total_diskon text-dark"><b>Tidak ada potongan harga</b></small></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>
                        <div class="col-sm-6 col-md-6 col-xl-6 mb-3">


                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6">Tagihan</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="tagihan">0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>


                      <div class="col-sm-6 col-md-6 col-xl-6">
                         <div class="form-group">
                           <label>Metode Pembayaran</label>
                           <select class="form-control" name="metode_pembayaran" id="metode_pembayaran">
                             <?php foreach (metode_pembayaran() as $k => $v) { ?>
                              <option value="<?php echo $v['id_metode_pembayaran'] ?>"><?php echo $v['metode_pembayaran'] ?></option>
                             <?php } ?>
                           </select>
                         </div>
                      </div>
                      <div class="col-sm-6 col-md-6 col-xl-6">
                          <div class="form-group">
                           <label>Pembayaran</label>
                           <input class="form-control currency" placeholder="masukan jumlah pembayaran" name="input_dibayar" id="input_dibayar">
                           <input class="form-control" type="hidden" placeholder="masukan jumlah pembayaran" name="input_kembalian" id="input_kembalian">
                         </div>
                      </div>
                      
                      <div class="col-md-12">
                        <div class="row" id="form_tambah_metode_pembayaran" >
                          <div class="col-sm-12 col-md-12 col-xl-12">
                          <div class="form-group">
                           <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="tambah_metode_pembayaran()" id="tombol_tambah_metode_pembayaran" style="display:none">Tambah Metode Pembayaran</button>
                         </div>
                      </div>
                        </div>

                      </div>




                      <div class="col-sm-6 col-md-3 col-xl-6">


                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6">Dibayar</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span id="show_dibayar">0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>
                      <div class="col-sm-6 col-md-3 col-xl-6">


                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-nuted">Kembalian</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span id="show_kembalian">0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>

                        <div class="col-md-12 col-lg-12">
                            <button class="btn btn-block btn-success mt-3" type="button" onclick="simpan_transaksi_online()" id="simpan_member_reg_online">Simpan Member</button>
                        </div>
                    
                  </div>
                </div>



              </div>
            </form>
           
        </div>
    </div>
</div>



<!-- modal konfirmasi member online -->




<!-- modal transaksi baru -->
<div class="modal fade" id="modal_new_transaction" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"><small>Transaksi Baru</small><br>Pilih Fasilitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_konfirmasi_reg_online">
              <div class="modal-body">
                <div class="row">
                  

                            <div class="col-md-3 mb-3">
                              <a  data-dismiss="modal" onclick="new_transaction('Futsal - Harian')">
                                <div class="card border" style="background:#E0E7FF">
                                  <img src="<?php echo base_url('assets/logo_fasilitas/FUTSAL1.png') ?>" width="100%">
                                </div>
                                <!-- Futsal - Booking -->
                              </a>
                            </div>
                            <div class="col-md-3 mb-3">
                              <a  data-dismiss="modal" onclick="new_transaction('Badminton - Harian')">
                                <div class="card border" style="background:#E0E7FF">
                                  <img src="<?php echo base_url('assets/logo_fasilitas/BD1.png') ?>" width="100%">
                                </div>
                                <!-- Badminton - Booking -->
                              </a>
                            </div>


                                <!-- Pickle - Booking -->
                            <div class="col-md-3 mb-3">
                              <a  data-dismiss="modal" onclick="new_transaction('Pickle - Harian')">
                                <div class="card border" style="background:#E0E7FF">
                                  <img src="<?php echo base_url('assets/logo_fasilitas/pickle_harian.png') ?>" width="100%" alt="Pickle - Harian">
                                </div>
                              
                              </a>
                            </div>


                         
                </div>
           

                <hr>


           






              </div>
            </form>
           
        </div>
    </div>
</div>





<!-- moda print transaksi online -->
<div class="modal fade" id="print_transaksi_gym" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Print</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_konfirmasi_reg_online">
               <div class="modal-body" id="struk_order_member_gym">
              
              </div>
            </form>
           
        </div>
    </div>
</div>



<!-- moda print transaksi online -->





<!-- moda print transaksi online -->
<div class="modal fade" id="print_transaksi_online" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Print</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_konfirmasi_reg_online">
              <div class="modal-body">
              Selanjutnya
                  

              </div>
            </form>
           
        </div>
    </div>
</div>



<!-- moda print transaksi online -->







  <div class="modal fade" id="print_order_member">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                
                <h4 class="modal-title">Member diaktifkan</h4>
              </div>
              <div class="modal-body" >
                <div class="row">
                  <div class="col-md-6">
                    <label><b>Kartu Member</b></label>


                    <div id="get_capture" class="example1" width="100%">
                 <div class="header" style="margin:5px; border-bottom:  solid; height:30px">
                  <div style="float:right; font-family:'calibri';color:white; font-size:16px">
                    Gym Membership Card 
                  </div>
                <!--   <div  style="float:right">
                    <img src="<?php echo base_url('assets/gambar/logo.png') ?>" width="50px">     
                  </div> -->
                  <div  style="clear:both">
                        
                  </div>
                 </div>


                 <div class="body">
                  <div style="float:left; margin-left : 1%; margin-right : 2%; width:32%;">
                    <img src="<?php echo base_url()  ?>file/kartu/202312310618.jpg" width="100%" style="border:solid; border-color:white">     
                  </div>
                  <div  style="float:left; width:65%;  font-family:'calibri'; color:white">
                    <span style="font-size: 20px; text-align: justify; text-justify: inter-word;"><b>Nama Member</b></span> <br>
                    <span style="font-size: 16px; ">No HP</span> <br>
                    <span style="font-size: 16px; ">Jenis member</span> <br>
                  </div>
                  <div  style="clear:both">
                        
                  </div>

                 </div>
                 <div class="footer">

                    <div style="float:left; font-family:'calibri';background:white; margin-top:5px">
                    Barcode
                  </div>
                <div  style="float:right">
                    <div style=" text-align: right; margin-bottom:30px; color:white;font-size: 16px;">Akhir Masa Aktif</div>    
                  </div>
                  <div  style="clear:both">
                        
                  </div>
                 </div>
              </div>
              <div id="result"></div>
              <a href="#" class="download btn btn-info btn-sm btn-block" style="margin-top:5px">Download</a>




                  </div>
                  <div class="col-md-6">
                    <label><b>Struk</b></label>
                    <div id="struk_order_member"></div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <a href="#" class="btn btn-primary" onclick="close_order_member()">Tutup</a>
              </div>
            </div>
          </div>
        </div>

<!-- modal fasilitas gym -->
<div class="modal fade" id="data_member" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">List Data Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <table class="table" id="list_member" style="width:100%">
                  <thead>
                    <tr>
                      <td>No</td>
                      <td>Kode Member</td>
                      <td>Nama</td>
                      <td>Alamat</td>
                      <td>No HP</td>
                      <td>Option</td>
                    </tr>
                  </thead>
                
                </table>
              </div>
           
        </div>
    </div>
</div>
<div class="modal fade" id="data_member_futsal_badminton" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">List Data Member <span id="jenis_fasilitas"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <table class="table" id="list_member_futsal_badminton" style="width:100%">
                  <thead>
                    <tr>
                      <td>No</td>
                      <td>Nama</td>
                      <td>Alamat</td>
                      <td>No HP</td>
                      <td>Option</td>
                    </tr>
                  </thead>
                
                </table>
              </div>
           
        </div>
    </div>
</div>
<div class="modal fade" id="student_card" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Data Student Card</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancel_student_card()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <table class="table" id="list_student_card" style="width:100%">
                  <thead>
                    <tr>
                      <td>No</td>
                      <td>Nama</td>
                      <td>Alamat</td>
                      <td>No HP</td>
                      <td>Option</td>
                    </tr>
                  </thead>
                
                </table>
              </div>
           
        </div>
    </div>
</div>




<div class="modal fade" id="konfirmasi_gym" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_gym_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_nohp"></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_email"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Paket Member</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_paket"></td>
                      </tr>
                      <tr>
                        <td>Registrasi</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_registrasi"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_biaya_paket"></td>
                      </tr>
                      <tr>
                        <td>Total</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_biaya_total"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_gym()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>




<div class="modal fade" id="konfirmasi_les" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_les_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_les_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_les_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_les_nohp"></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td id="konfirmasi_les_email"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Les</td>
                        <td>:</td>
                        <td id="konfirmasi_les_jenis_les"></td>
                      </tr>
                      <tr>
                        <td>Paket</td>
                        <td>:</td>
                        <td id="konfirmasi_les_paket"></td>
                      </tr>
                      <tr>
                        <td>Registrasi</td>
                        <td>:</td>
                        <td id="konfirmasi_les_registrasi"></td>
                      </tr>
                      <tr>
                        <td>Biaya les</td>
                        <td>:</td>
                        <td id="konfirmasi_les_biaya_les"></td>
                      </tr>
                      <tr>
                        <td>Total</td>
                        <td>:</td>
                        <td id="konfirmasi_les_totalbiaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_les_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_les_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_les_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_les_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_les()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>
<div class="modal fade" id="konfirmasi_gym_spesial" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_gym_spesial_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_nohp"></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_email"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Paket Member</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_paket"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_gym_spesial_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_gym()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>



<div class="modal fade" id="konfirmasi_swimming_spesial" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_swimming_spesial_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_nohp"></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_email"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Paket Member</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_paket"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_spesial_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_swimming_spesial()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>

<div class="modal fade" id="konfirmasi_swimming_membership" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_swimming_membership_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_nohp"></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_email"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Paket Member</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_paket"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_membership_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_gym()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>
<div class="modal fade" id="konfirmasi_swimming_harian" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_nohp"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Kategori</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_kategori"></td>
                      </tr>
                      <tr>
                        <td>Biaya Perorang</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_biaya_perorang"></td>
                      </tr>
                      <tr>
                        <td>Jumlah Orang</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_jumlah_orang"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_biaya_total"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_harian_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_swimming_harian()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>


<div class="modal fade" id="konfirmasi_swimming_pelajar" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_nohp"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Kategori</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_kategori"></td>
                      </tr>
                      <tr>
                        <td>Biaya Perorang</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_biaya_perorang"></td>
                      </tr>
                      <tr>
                        <td>Jumlah Orang</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_jumlah_orang"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_biaya_total"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_swimming_pelajar_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_swimming_pelajar()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>


<div class="modal fade" id="konfirmasi_private_gym" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_private_gym_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_nohp"></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_email"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Paket Member</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_paket"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_private_gym_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_private_gym()"  id="tombol_simpan_private_gym">Simpan</button>
                    <button class="btn btn-block btn-outline-info" onclick="simpan_transaksi_private_muaythai()" id="tombol_simpan_private_muaythay">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>






<div class="modal fade" id="konfirmasi_futsal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_futsal_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_status"></td>
                      </tr>
                      <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_tgl_main"></td>
                      </tr>
                      <tr>
                        <td>Jam Main</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_jam_main"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_tagihan"></td>
                      </tr>
                      <tr>
                        <td id="konfirmasi_futsal_caption_dibayar">Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_dibayar"></td>
                      </tr>
                      <tr>
                        <td id="konfirmasi_futsal_caption_kembalian">Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_futsal()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>






<div class="modal fade" id="konfirmasi_pickle" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_pickle_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_status"></td>
                      </tr>
                      <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_tgl_main"></td>
                      </tr>
                      <tr>
                        <td>Jam Main</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_jam_main"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_tagihan"></td>
                      </tr>
                      <tr>
                        <td id="konfirmasi_pickle_caption_dibayar">Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_dibayar"></td>
                      </tr>
                      <tr>
                        <td id="konfirmasi_pickle_caption_kembalian">Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_pickle()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>




<div class="modal fade" id="konfirmasi_futsal_bulanan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_bulanan_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_bulanan_alamat"></td>
                      </tr>
                    
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                     
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_bulanan_nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_bulanan_keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-12">
                  <hr>  

                    <table class="table">
                     
                      <tr>
                        <td colspan="5"><b>Jadwal Futsal</b></td>
                       
                      </tr>
                      <tr>
                        <td valign="top">No</td>
                        <td valign="top">Tgl</td>
                        <td valign="top">Jam</td>
                        <td valign="top">Biaya</td>
                        <td valign="top">Status</td>
                      </tr>
                      <tr>
                        <td valign="top">1</td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_tgl_1"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_jam_1"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_biaya_1"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_status_1"></td>
                      </tr>
                      <tr>
                        <td valign="top">2</td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_tgl_2"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_jam_2"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_biaya_2"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_status_2"></td>
                      </tr>
                      <tr>
                        <td valign="top">3</td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_tgl_3"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_jam_3"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_biaya_3"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_status_3"></td>
                      </tr>
                      <tr>
                        <td valign="top">4</td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_tgl_4"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_jam_4"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_biaya_4"></td>
                        <td valign="top" id="konfirmasi_futsal_bulanan_status_4"></td>
                      </tr>
                      <tr>
                        <td valign="top" colspan="3">Biaya</td>
                        <td valign="top" colspan="2" id="biaya_futsal"></td>
                      </tr>
                      <tr>
                        <td valign="top" colspan="3">Diskon Bulanan <span id="diskon_bulanan_biaya_futsal"></span></td>
                        <td valign="top" colspan="2" id="nilai_diskon_bulanan_biaya_futsal"></td>
                      </tr>
                      <tr>
                        <td valign="top" colspan="3">Total</td>
                        <td valign="top" colspan="2" id="total_biaya_futsal"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <table class="table">
                    
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_bulanan_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_bulanan_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_bulanan_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_bulanan_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_futsal_bulanan()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>



<div class="modal fade" id="konfirmasi_pickle_bulanan" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row">

          <div class="col-md-6">
            <table class="table">
              <tr><td>Nama</td><td>:</td><td id="konfirmasi_pickle_bulanan_nama"></td></tr>
              <tr><td>Alamat</td><td>:</td><td id="konfirmasi_pickle_bulanan_alamat"></td></tr>
            </table>
          </div>

          <div class="col-md-6">
            <table class="table">
              <tr><td>No HP</td><td>:</td><td id="konfirmasi_pickle_bulanan_nohp"></td></tr>
              <tr><td>Keterangan</td><td>:</td><td id="konfirmasi_pickle_bulanan_keterangan"></td></tr>
            </table>
          </div>

          <div class="col-md-12">
            <hr>
            <table class="table">
              <tr><td colspan="5"><b>Jadwal pickle</b></td></tr>
              <tr>
                <td valign="top">No</td>
                <td valign="top">Tgl</td>
                <td valign="top">Jam</td>
                <td valign="top">Biaya</td>
                <td valign="top">Status</td>
              </tr>

              <tr>
                <td valign="top">1</td>
                <td valign="top" id="konfirmasi_pickle_bulanan_tgl_1"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_jam_1"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_biaya_1"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_status_1"></td>
              </tr>

              <tr>
                <td valign="top">2</td>
                <td valign="top" id="konfirmasi_pickle_bulanan_tgl_2"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_jam_2"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_biaya_2"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_status_2"></td>
              </tr>

              <tr>
                <td valign="top">3</td>
                <td valign="top" id="konfirmasi_pickle_bulanan_tgl_3"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_jam_3"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_biaya_3"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_status_3"></td>
              </tr>

              <tr>
                <td valign="top">4</td>
                <td valign="top" id="konfirmasi_pickle_bulanan_tgl_4"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_jam_4"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_biaya_4"></td>
                <td valign="top" id="konfirmasi_pickle_bulanan_status_4"></td>
              </tr>

              <tr><td valign="top" colspan="3">Biaya</td><td valign="top" colspan="2" id="biaya_pickle"></td></tr>
              <tr><td valign="top" colspan="3">Diskon Bulanan <span id="diskon_bulanan_biaya_pickle"></span></td><td valign="top" colspan="2" id="nilai_diskon_bulanan_biaya_pickle"></td></tr>
              <tr><td valign="top" colspan="3">Total</td><td valign="top" colspan="2" id="total_biaya_pickle"></td></tr>
            </table>
          </div>

          <div class="col-md-12">
            <table class="table">
              <tr><td>Potongan Harga</td><td>:</td><td id="konfirmasi_pickle_bulanan_potongan"></td></tr>
              <tr><td>Tagihan</td><td>:</td><td id="konfirmasi_pickle_bulanan_tagihan"></td></tr>
              <tr><td>Dibayar</td><td>:</td><td id="konfirmasi_pickle_bulanan_dibayar"></td></tr>
              <tr><td>Kembalian</td><td>:</td><td id="konfirmasi_pickle_bulanan_kembalian"></td></tr>
            </table>
          </div>

          <div class="col-md-12">
            <button class="btn btn-block btn-info" onclick="simpan_transaksi_pickle_bulanan()">Simpan</button>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>



<div class="modal fade" id="konfirmasi_futsal_turnamen" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_futsal_turnamen_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_status"></td>
                      </tr>
                      <tr>
                        <td>Paket</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_paket"></td>
                      </tr>
                      <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_tgl_main"></td>
                      </tr>
                      <tr>
                        <td>Jam Main</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_jam_main"></td>
                      </tr>
                      <tr>
                        <td>Biaya Lapangan</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_biaya_lapangan"></td>
                      </tr>
                      <tr>
                        <td>Biaya Tambahan</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_biaya_tambahan"></td>
                      </tr>
                   
                      <tr>
                        <td>Total</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_biaya_total"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_futsal_turnamen_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_futsal()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>




<div class="modal fade" id="konfirmasi_badminton_bulanan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_bulanan_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_bulanan_alamat"></td>
                      </tr>
                    
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                     
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_bulanan_nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_bulanan_keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-12">
                  <hr>  

                    <table class="table">
                     
                      <tr>
                        <td colspan="5"><b>Jadwal badminton</b></td>
                       
                      </tr>
                      <tr>
                        <td valign="top">No</td>
                        <td valign="top">Tgl</td>
                        <td valign="top">Jam</td>
                        <td valign="top">Biaya</td>
                        <td valign="top">Status</td>
                      </tr>
                      <tr>
                        <td valign="top">1</td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_tgl_1"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_jam_1"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_biaya_1"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_status_1"></td>
                      </tr>
                      <tr>
                        <td valign="top">2</td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_tgl_2"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_jam_2"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_biaya_2"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_status_2"></td>
                      </tr>
                      <tr>
                        <td valign="top">3</td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_tgl_3"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_jam_3"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_biaya_3"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_status_3"></td>
                      </tr>
                      <tr>
                        <td valign="top">4</td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_tgl_4"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_jam_4"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_biaya_4"></td>
                        <td valign="top" id="konfirmasi_badminton_bulanan_status_4"></td>
                      </tr>
                      <tr>
                        <td valign="top" colspan="3">Biaya</td>
                        <td valign="top" colspan="2" id="biaya_badminton"></td>
                      </tr>
                      <tr>
                        <td valign="top" colspan="3">Diskon Bulanan <span id="diskon_bulanan_biaya_badminton"></span></td>
                        <td valign="top" colspan="2" id="nilai_diskon_bulanan_biaya_badminton"></td>
                      </tr>
                      <tr>
                        <td valign="top" colspan="3">Total</td>
                        <td valign="top" colspan="2" id="total_biaya_badminton"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <table class="table">
                    
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_bulanan_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_bulanan_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_bulanan_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_bulanan_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_badminton_bulanan()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>



<div class="modal fade" id="konfirmasi_badminton_turnamen" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_badminton_turnamen_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_status"></td>
                      </tr>
                      <tr>
                        <td>Paket</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_paket"></td>
                      </tr>
                      <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_tgl_main"></td>
                      </tr>
                      <tr>
                        <td>Jam Main</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_jam_main"></td>
                      </tr>
                      <tr>
                        <td>Biaya Lapangan</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_biaya_lapangan"></td>
                      </tr>
                      <tr>
                        <td>Biaya Tambahan</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_biaya_tambahan"></td>
                      </tr>
                   
                      <tr>
                        <td>Total</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_biaya_total"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_turnamen_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_badminton()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>




<div class="modal fade" id="konfirmasi_pickle_turnamen" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_pickle_turnamen_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_status"></td>
                      </tr>
                      <tr>
                        <td>Paket</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_paket"></td>
                      </tr>
                      <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_tgl_main"></td>
                      </tr>
                      <tr>
                        <td>Jam Main</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_jam_main"></td>
                      </tr>
                      <tr>
                        <td>Biaya Lapangan</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_biaya_lapangan"></td>
                      </tr>
                      <tr>
                        <td>Biaya Tambahan</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_biaya_tambahan"></td>
                      </tr>
                   
                      <tr>
                        <td>Total</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_biaya_total"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_pickle_turnamen_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_pickle()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>






<div class="modal fade" id="jadwal_futsal_bulanan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form id="form_pilihan_jadwal_futsal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Pilih jadwal futsal <span id="jadwal_futsal_bulanan_ke"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12">



                                          <input type="hidden" name="simpan_lama_main" id="simpan_lama_main" value="0">
                                          <input type="hidden" name="total_biaya" id="total_biaya" value="0">
                                          <input type="hidden" name="jadwal_ke" id="jadwal_ke">






                                            <ul class="list-group list-group-flush">
                                              <li class="list-group-item">
                                                <div class="row">
                                                  <div class="col-md-12">
                                                    
                                                    <div class="form-group" id="form_tgl_main">
                                                          <label>Pilih Status</label>
                                                          <select class="form-control" name="status" id="status">
                                                            <option>Booking</option>
                                                            <!-- <option>Sudah Main</option> -->
                                                           </select>
                                                        </div>

                                                  </div>
                                                </div>
                                              </li>
                                              <li class="list-group-item">
                                                <div class="row">
                                                  <div class="col-md-6">
                                                    
                                                    <div class="form-group" id="form_tgl_main">
                                                          <label>Pilih Lapangan</label>
                                                          <select class="form-control" name="lapangan" id="lapangan">
                                                            <option value="1">Lapangan 1</option>
                                                            <option value="2">Lapangan 2</option>
                                                           </select>
                                                        </div>

                                                  </div>
                                                  <div class="col-md-6">
                                                    <div class="form-group" id="form_tgl_main">
                                                          <label>Tanggal Main</label>
                                                          <input type="date" class="form-control" name="tgl_main" id="tgl_main">
                                                           
                                                        </div>
                                                  </div>
                                                </div>
                                              </li>


                                              <li class="list-group-item">
                                                    <div class="form-group" id="form_jam_main">
                                                          <label>Pilih Jam Main</label>
                                                           <div class="row" id="pilihan_jam">
                                                            <div class="col-md-12 alert alert-info">Pilih tanggal dulu</div>
                                                           </div>
                                                        </div>
                                              </li>
                                             
                                              <li class="list-group-item">
                                                <div class="row">
                                                  <div class="col-md-6 col-lg-6" style="border">
                                                      <div class="card-shadow-primary border widget-chart widget-chart2 text-left card">
                                                          <div class="widget-content p-0 w-100">
                                                              <div class="widget-content-outer">
                                                                  <div class="widget-content-left">
                                                                      <div class="text-muted opacity-6">Lama Main</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-danger lama_main">Pilih jam main</div>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-6 col-lg-6">
                                                      <div class="card-shadow-success border widget-chart widget-chart2 text-left card">
                                                          <div class="widget-content p-0 w-100">
                                                              <div class="widget-content-outer">
                                                                  <div class="widget-content-left">
                                                                      <div class="text-muted opacity-6" id="jam_main_dipilih">Pilih Jam Main</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left pr-2">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-success biaya_perjam">0</div>
                                                                      </div>
                                                                     
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-6 col-lg-12">
                                                      <div class="card-shadow-warning mt-3 mb-2 border widget-chart widget-chart2 text-left card">
                                                          <div class="widget-content p-0 w-100">
                                                              <div class="widget-content-outer">
                                                                  <div class="widget-content-left fsize-1">
                                                                      <div class="text-muted opacity-6">Total</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left pr-2 fsize-1">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-warning biaya_total">0</div>
                                                                      </div>
                                                                   
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                </div>
                                              </li>


                                            </ul>


                  </div>



                 
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="set_jadwal_futsal()" type="button">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
  </form>
</div>





<div class="modal fade" id="jadwal_badminton_bulanan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form id="form_pilihan_jadwal_badminton">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Pilih jadwal badminton <span id="jadwal_badminton_bulanan_ke"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12">



                                          <input type="hidden" name="simpan_lama_main" id="simpan_lama_main" value="0">
                                          <input type="hidden" name="total_biaya" id="total_biaya" value="0">
                                          <input type="hidden" name="jadwal_ke" id="jadwal_ke">






                                            <ul class="list-group list-group-flush">
                                              <li class="list-group-item">
                                                <div class="row">
                                                  <div class="col-md-12">
                                                    
                                                    <div class="form-group" id="form_tgl_main">
                                                          <label>Pilih Status</label>
                                                          <select class="form-control" name="status" id="status">
                                                            <option>Booking</option>
                                                            <!-- <option>Sudah Main</option> -->
                                                           </select>
                                                        </div>

                                                  </div>
                                                </div>
                                              </li>
                                              <li class="list-group-item">
                                                <div class="row">
                                                  <div class="col-md-6">
                                                    
                                                    <div class="form-group" id="form_tgl_main">
                                                          <label>Pilih Lapangan</label>
                                                          <select class="form-control" name="lapangan" id="lapangan">
                                                         
                                                           </select>
                                                        </div>

                                                  </div>
                                                  <div class="col-md-6">
                                                    <div class="form-group" id="form_tgl_main">
                                                          <label>Tanggal Main</label>
                                                          <input type="date" class="form-control" name="tgl_main" id="tgl_main">
                                                           
                                                        </div>
                                                  </div>
                                                </div>
                                              </li>


                                              <li class="list-group-item">
                                                    <div class="form-group" id="form_jam_main">
                                                          <label>Pilih Jam Main</label>
                                                           <div class="row" id="pilihan_jam">
                                                            <div class="col-md-12 alert alert-info">Pilih tanggal dulu</div>
                                                           </div>
                                                        </div>
                                              </li>
                                             
                                              <li class="list-group-item">
                                                <div class="row">
                                                  <div class="col-md-6 col-lg-6" style="border">
                                                      <div class="card-shadow-primary border widget-chart widget-chart2 text-left card">
                                                          <div class="widget-content p-0 w-100">
                                                              <div class="widget-content-outer">
                                                                  <div class="widget-content-left">
                                                                      <div class="text-muted opacity-6">Lama Main</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-danger lama_main">Pilih jam main</div>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-6 col-lg-6">
                                                      <div class="card-shadow-success border widget-chart widget-chart2 text-left card">
                                                          <div class="widget-content p-0 w-100">
                                                              <div class="widget-content-outer">
                                                                  <div class="widget-content-left">
                                                                      <div class="text-muted opacity-6" id="jam_main_dipilih">Pilih Jam Main</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left pr-2">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-success biaya_perjam">0</div>
                                                                      </div>
                                                                     
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-6 col-lg-12">
                                                      <div class="card-shadow-warning mt-3 mb-2 border widget-chart widget-chart2 text-left card">
                                                          <div class="widget-content p-0 w-100">
                                                              <div class="widget-content-outer">
                                                                  <div class="widget-content-left fsize-1">
                                                                      <div class="text-muted opacity-6">Total</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left pr-2 fsize-1">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-warning biaya_total">0</div>
                                                                      </div>
                                                                   
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                </div>
                                              </li>


                                            </ul>


                  </div>



                 
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="set_jadwal_badminton()" type="button">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
  </form>
</div>


<div class="modal fade" id="jadwal_pickle_bulanan" role="dialog" aria-hidden="true">
  <form id="form_pilihan_jadwal_pickle">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="title">Pilih jadwal pickle <span id="jadwal_pickle_bulanan_ke"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">

              <input type="hidden" name="simpan_lama_main" id="simpan_lama_main" value="0">
              <input type="hidden" name="total_biaya" id="total_biaya" value="0">
              <input type="hidden" name="jadwal_ke" id="jadwal_ke">

              <ul class="list-group list-group-flush">

                <li class="list-group-item">
                  <div class="form-group" id="form_status">
                    <label>Pilih Status</label>
                    <select class="form-control" name="status" id="status">
                      <option>Booking</option>
                    </select>
                  </div>
                </li>

                <li class="list-group-item">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group" id="form_lapangan">
                        <label>Pilih Lapangan</label>
                        <select class="form-control" name="lapangan" id="lapangan">
                          <option value="1">Lapangan 1</option>
                          <option value="2">Lapangan 2</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group" id="form_tgl_main">
                        <label>Tanggal Main</label>
                        <input type="date" class="form-control" name="tgl_main" id="tgl_main">
                      </div>
                    </div>
                  </div>
                </li>

                <li class="list-group-item">
                  <div class="form-group" id="form_jam_main">
                    <label>Pilih Jam Main</label>
                    <div class="row" id="pilihan_jam">
                      <div class="col-md-12 alert alert-info">Pilih tanggal dulu</div>
                    </div>
                  </div>
                </li>

                <li class="list-group-item">
                  <div class="row">

                    <div class="col-md-6 col-lg-6">
                      <div class="card-shadow-primary border widget-chart widget-chart2 text-left card">
                        <div class="widget-content p-0 w-100">
                          <div class="widget-content-outer">
                            <div class="widget-content-left">
                              <div class="text-muted opacity-6">Lama Main</div>
                            </div>
                            <div class="widget-content-wrapper">
                              <div class="widget-content-left">
                                <div class="widget-numbers mt-0 fsize-3 text-danger lama_main">Pilih jam main</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                      <div class="card-shadow-success border widget-chart widget-chart2 text-left card">
                        <div class="widget-content p-0 w-100">
                          <div class="widget-content-outer">
                            <div class="widget-content-left">
                              <div class="text-muted opacity-6" id="jam_main_dipilih">Pilih Jam Main</div>
                            </div>
                            <div class="widget-content-wrapper">
                              <div class="widget-content-left pr-2">
                                <div class="widget-numbers mt-0 fsize-3 text-success biaya_perjam">0</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12 mt-3">
                      <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                        <div class="widget-content p-0 w-100">
                          <div class="widget-content-outer">
                            <div class="widget-content-left fsize-1">
                              <div class="text-muted opacity-6">Total</div>
                            </div>
                            <div class="widget-content-wrapper">
                              <div class="widget-content-left pr-2 fsize-1">
                                <div class="widget-numbers mt-0 fsize-3 text-warning biaya_total">0</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </li>

              </ul>
            </div>

            <div class="col-md-12 mt-3">
              <button class="btn btn-block btn-info" onclick="set_jadwal_pickle()" type="button">Simpan</button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </form>
</div>






<div class="modal fade" id="konfirmasi_badminton" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_badminton_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_status"></td>
                      </tr>
                      <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_tgl_main"></td>
                      </tr>
                      <tr>
                        <td>Jam Main</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_jam_main"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_badminton_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_badminton()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>



<div class="modal fade" id="konfirmasi_private_swimming" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_private_swimming_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_nohp"></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_email"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Paket Member</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_paket"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_private_swimming_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_private_swimming()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>



<div class="modal fade" id="konfirmasi_private_muaythai" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div  id="konfirmasi_private_muaythai_foto"></div>
                    <table class="table">
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_nama"></td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_alamat"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_nohp"></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_email"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table">
                      <tr>
                        <td>Paket Member</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_paket"></td>
                      </tr>
                      <tr>
                        <td>Biaya</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_biaya"></td>
                      </tr>
                      <tr>
                        <td>Potongan Harga</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_potongan"></td>
                      </tr>
                      <tr>
                        <td>Tagihan</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_tagihan"></td>
                      </tr>
                      <tr>
                        <td>Dibayar</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_dibayar"></td>
                      </tr>
                      <tr>
                        <td>Kembalian</td>
                        <td>:</td>
                        <td id="konfirmasi_private_muaythai_kembalian"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="simpan_transaksi_private_muaythai()">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
</div>










<div class="modal fade" id="prewiew_member_online" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi <span id="konfirmasi_kategori_member"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
               <div class="row justify-align-center">
        <div class="col-md-7" id="identitas_member">
          <div class="row">
             <div class="col-md-12">
              <h5 class="menu-header-title nama mb-3 text-center">Nama Member</h5>
            </div>
              <div class="col-md-5">
              <img src="" width="100%" id="foto_member" class="img-thumbnail">
              </div>
              <div class="col-md-7">
                <table class="table">
                  <tr>
                    <td valign="top">No Identitas</td>
                    <td valign="top">:</td>
                    <td valign="top" id="identitas"></td>
                  </tr>
                  <tr>
                    <td valign="top">Jenis Kelamin</td>
                    <td valign="top">:</td>
                    <td valign="top" id="jk"></td>
                  </tr>
                  <tr>
                    <td valign="top">Tempat / Tgl Lahir / Usia</td>
                    <td valign="top">:</td>
                    <td valign="top" id="ttl"></td>
                  </tr>
                  <tr>
                    <td valign="top">Alamat</td>
                    <td valign="top">:</td>
                    <td valign="top" id="alamat"></td>
                  </tr>
                  <tr>
                    <td valign="top">No Hp / Email</td>
                    <td valign="top">:</td>
                    <td valign="top"><span  id="nohp"></span> / <span  id="email"></span></td>
                  </tr>
                  <tr>
                    <td valign="top">Pekerjaan</td>
                    <td valign="top">:</td>
                    <td valign="top" id="pekerjaan"></td>
                  </tr>
                  <tr>
                    <td valign="top">Instagram</td>
                    <td valign="top">:</td>
                    <td valign="top" id="ig"></td>
                  </tr>
                </table>
              </div>
          </div>
        </div>
        <div class="col-md-5" id="tagihan_member">
          <div id="form_tagihan" style="display:none">
          <ul class="list-group list-group-flush">
            <li class="active list-group-item">
              <div class="widget-content p-0">
                  <div class="widget-content-wrapper">
                      <div class="widget-content-left">
                          <h5 class="list-group-item-heading" id="nama_member"></h5>
                          <h5 class="list-group-item-heading" id="fasilitas">Fasilitas : Gym</h5>
                          <div class="widget-subheading"> 
                            Terdaftar Pada <span class="waktu_regis"></span> <br>Kode : <span   class="kode_regis"></span>
                          </div>
                      </div>
                      <div class="widget-content-right">
                          
                      </div>
                  </div>
              </div>
            </li>


            <li class="list-group-item">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Paket : <span  id="paket"></span></div>
                            <div class="widget-subheading"> Masa Aktif : <span id="masa_aktif"></span></div>
                        </div>
                        <div class="widget-content-right">
                            <span id="tgl_masa_aktif"></span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <div class="col-md-6 col-lg-6" style="border">
                    <div class="card-shadow-primary border widget-chart widget-chart2 text-left card">
                        <div class="widget-content p-0 w-100">
                            <div class="widget-content-outer">
                                <div class="widget-content-left">
                                    <div class="text-muted opacity-6">Biaya Paket</div>
                                </div>
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left">
                                        <div class="widget-numbers mt-0 fsize-3 text-danger" id="biaya_paket">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <div class="card-shadow-success border widget-chart widget-chart2 text-left card">
                        <div class="widget-content p-0 w-100">
                            <div class="widget-content-outer">
                                <div class="widget-content-left">
                                    <div class="text-muted opacity-6">Registrasi</div>
                                </div>
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left pr-2">
                                        <div class="widget-numbers mt-0 fsize-3 text-success" id="biaya_regis">0</div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-12">
                    <div class="card-shadow-warning mt-3 mb-2 border widget-chart widget-chart2 text-left card">
                        <div class="widget-content p-0 w-100">
                            <div class="widget-content-outer">
                                <div class="widget-content-left fsize-1">
                                    <div class="text-muted opacity-6">Total</div>
                                </div>
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left pr-2 fsize-1">
                                        <div class="widget-numbers mt-0 fsize-3 text-warning total_reg_online" >0</div>
                                    </div>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-lg-12">
                  <input type="hidden" name="" id="simpan_fasilitas">
                    <button class="btn btn-block btn-info mt-3" onclick="pembayaran_online()">Simpan Member</button>
                </div>

                </div>
            </li>
          </ul>
        </div>
        <div id="form_keanggotaan"  style="display:none">
          <ul class="list-group list-group-flush">
            <li class="active list-group-item">
              <div class="widget-content p-0">
                  <div class="widget-content-wrapper">
                      <div class="widget-content-left">
                          <h5 class="list-group-item-heading">Member sudah aktif</h5>
                          <div class="widget-subheading"> 
                            Registrasi Online Pada <span class="waktu_regis"></span> <br>Kode : <span   class="kode_regis"></span>
                          </div>
                      </div>
                      <div class="widget-content-right">
                          
                      </div>
                  </div>
              </div>
            </li>
          </ul>
          <ul class="list-group list-group-flush" id="list_keanggontaan">
          </ul>
        </div>
      </div>
<div class="col-md-12 col-lg-12 col-xl-12">
</div>
      </div>








      
              </div>
           
        </div>
    </div>
</div>






<!-- modal konfirmasi member online -->
<div class="modal fade" id="konfirmasi_sewa_peralatan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form id="form_sewa_peralatan">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Konfirmasi Sewa Peralatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
              
                                          <input type="hidden" name="simpan_dibayar" id="simpan_dibayar">
                                          <input type="hidden" name="opsi_tambah_metode_pembayaran" id="opsi_tambah_metode_pembayaran">
                                          <input type="hidden" name="nama_penyewa" id="nama_penyewa">
                                          <input type="hidden" name="tgl_sewa" id="tgl_sewa">
                                          <input type="hidden" name="tagihan" id="tagihan" value="0">

                <div class="form-group">
                  <label>Identitas Penyewa</label>
                  <table class="table">
                    <tr>
                      <td>Nama</td>
                      <td>:</td>
                      <td id="nama"></td>
                    </tr>
                    <tr>
                      <td>Alamat</td>
                      <td>:</td>
                      <td id="alamat"></td>
                    </tr>
                    <tr>
                      <td>No HP</td>
                      <td>:</td>
                      <td id="nohp"></td>
                    </tr>
                  </table>
                </div>


                <div class="form-group">

                  <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6">Tagihan</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="rp_tagihan_sewa">0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>





                  <!-- <label>Jumlah Pembayaran</label> -->
                  <!-- <input type="text" readonly name="" id="rp_penjualan_fnb" class="form-control currency"> -->
                </div>
                <?php   if (in_array(3, $this->session->userdata('id_hak_akses'))) { ?>
                <div class="form-group">
                  <label>Status</label>
                 <select class="form-control" id="status" name="status">
                     <!-- <option value="Preview">Preview</option> -->
                     <option value="Settlement">Settlement</option>
                 </select>
                </div>
                <div id="form_metode_pembayaran">
                  

              <div class="row">
                
                <div class="col-sm-6 col-md-6 col-xl-6">
                           <div class="form-group">
                             <label>Metode Pembayaran</label>
                             <select class="form-control" name="metode_pembayaran" id="metode_pembayaran">
                               <?php foreach (metode_pembayaran() as $k => $v) { ?>
                                <option value="<?php echo $v['id_metode_pembayaran'] ?>"><?php echo $v['metode_pembayaran'] ?></option>
                               <?php } ?>
                             </select>
                           </div>
                        </div>
                          <div class="col-sm-6 col-md-6 col-xl-6">
                            <div class="form-group">
                             <label>Pembayaran</label>
                             <input class="form-control currency" placeholder="masukan jumlah yang dibayar pelanggan" id="input_dibayar" name="input_dibayar">
                           </div>
                        </div>
                          
                        <div class="col-md-12">
                          <div class="row" id="form_tambah_metode_pembayaran" >
                            <div class="col-sm-12 col-md-12 col-xl-12">
                            <div class="form-group">
                             <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="tambah_metode_pembayaran()" id="tombol_tambah_metode_pembayaran" style="display:none">Tambah Metode Pembayaran</button>
                           </div>
                        </div>
                          </div>

                        </div>




                  </div>
                  <div class="row mb-3">
                    
                    
                      <div class="col-sm-6 col-md-6 col-xl-6">


                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6">Dibayar</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-danger"><span id="show_dibayar">0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>
                        <div class="col-sm-6 col-md-6 col-xl-6">


                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-nuted">Kembalian</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-success"><span id="show_kembalian">0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>

                  </div>
              </div>
              <?php }
              else{ ?>
                  <input type="hidden"  class="form-control" id="status" name="status" value="Preview">
                  <input type="hidden"  class="form-control" id="metode_pembayaran" name="metode_pembayaran" value="">
              <?php } ?>
                <div class="form-group">
                 <a href="javascript:void(0)" class="btn btn-info btn-sm btn-block" onclick="simpan_transaksi_sewa()" id="tombol_simpan_transaksi_sewa">Simpan</a>
                </div>
              </div>



              </div>
           
        </div>
      </form>
    </div>
<!-- modal fasilitas gym -->
<script src="<?php echo base_url(); ?>assets/jquery_number/jquery.number.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script type="text/javascript">
  
    function edit_keranjang(x) {
        $.fn.editableform.buttons = '<button type="submit" class="btn btn-primary btn-sm editable-submit">OK</button>' +
            '<button type="button" class="btn btn-default btn-sm editable-cancel">Batal</button>';

        let id = $(x).attr('pk');
        // let qty= $(x).attr('jumlah');
        let qty = $(x).val();
        let produk= $(x).attr('produk');
        $(x).editable({
            mode: 'inline',
            pk: id,
            savenochange: true,
            url: `<?php echo base_url('/user/gro/sewa_peralatan/edit_keranjang/?id_sewa_peralatan=') ?>`+ id + `&qty=`+qty  + `&produk=`+produk ,
            success: function(c) {
                keranjang();
            },
        });
    }
</script>
<script type="text/javascript">
showAutoCurrency();
    
  function showAutoCurrency(){
    $('input.currency').number( true, 0 );
  }




 



  dt_member_reg_online();
  function dt_member_reg_online(){

    // $('#data_member_reg_online').modal('show');
    $('#list_data_member_reg').DataTable(
    {
          processing  : true,
          serverSide  : true,
          bDestroy  : true,
          // responsive  : true,
          ajax    : {
                   url: '<?php echo base_url() ?>user/gro/transaction/cek_data_member/dt_member_reg_online',
                    type  : "POST",
                    data  : {
                      tgl : '<?php echo $this->input->get('tgl') ?>',
                    },
                  },
      });
  }




  $('#modal_pembayaran_member_online').find('#potongan_harga').change(function(){

    // $('#modal_pembayaran_member_online').find('#opsi_tambah_metode_pembayaran').val(0);
    // batal_tambah_metode_pembayaran();
    // $('#modal_pembayaran_member_online').find('#tombol_tambah_metode_pembayaran').hide();


    var kategori = $('#modal_pembayaran_member_online').find('#potongan_harga').val();
    var total = parseInt($('#modal_pembayaran_member_online').find('.harus_dibayar_reg_online').val());
    var biaya_register = parseInt($('#modal_pembayaran_member_online').find('.biaya_regis_reg_online').val());
    var biaya_paket = parseInt($('#modal_pembayaran_member_online').find('.biaya_member_reg_online').val());
    if (kategori=='') {
      
    $('#modal_pembayaran_member_online').find('#id_student_card').val('');
        $('#modal_pembayaran_member_online').find('#kategori_diskon').val('');
        $('#modal_pembayaran_member_online').find('#nama_diskon').val('');
        $('#modal_pembayaran_member_online').find('#id_diskon').val('');
        $('#modal_pembayaran_member_online').find('#jenis_potongan').val('');
        $('#modal_pembayaran_member_online').find('#besar_potongan').val('');
        $('#modal_pembayaran_member_online').find('#rp_nilai_potongan').val('');

        $('#modal_pembayaran_member_online').find('#simpan_tagihan').val(total);
        $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(total);
        $('#modal_pembayaran_member_online').find('#simpan_kembalian').val(0);


        $('#modal_pembayaran_member_online').find('#caption_potongan').html(`

                                    <div class="widget-content-outer">
                                       
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-2"><small class="total_diskon text-dark"><b>Tidak ada potongan harga</b></small></div>
                                            </div>
                                         
                                        </div>
                                    </div>
        `);
        $('#modal_pembayaran_member_online').find('.tagihan').html(number_format(total));
        $('#modal_pembayaran_member_online').find('#show_dibayar').html(number_format(total));
        
        $('#modal_pembayaran_member_online').find('#show_kembalian').html(0);

        $('#modal_pembayaran_member_online').find('#input_dibayar').val('');



      $('#modal_pembayaran_member_online').find('.form_student_card').hide();
      
      $('#modal_pembayaran_member_online').find('.alert_student_card').hide();
      $('#modal_pembayaran_member_online').find('.data_student_card').hide();
    }
    else if (kategori=='Student Card') {
      $('#modal_pembayaran_member_online').find('.form_student_card').show();
      var student_card = '<?php echo student_card() ?>';
      var json_sc = JSON.parse(student_card);
      var besar_diskon = json_sc[0].diskon ; 
      var jenis_potongan_sc = json_sc[0].jenis_potongan;
         if (jenis_potongan_sc=='Persentase') {
          var nilai_diskon = biaya_paket * (besar_diskon / 100);
          var tagihan = biaya_paket - nilai_diskon + biaya_register;
          var caption_diskon = "Diskon "+ besar_diskon + '%<br>Rp. ' + number_format(nilai_diskon);
        }else{
          var nilai_diskon = besar_diskon;
          var caption_diskon = "Diskon Rp. "+ number_format(besar_diskon);
          var tagihan = biaya_paket - nilai_diskon + biaya_register;

        }

      show_student_card();

        $('#modal_pembayaran_member_online').find('#caption_potongan').html(`

                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6">Diskon Student Card</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-2">
                                                <small class="text-danger" style="text-decoration: line-through;">
                                                `+ number_format(biaya_paket)+ `
                                                </small> <br>
                                                <small class="text-success">
                                                <b>`+caption_diskon+`</b>
                                                </small>
                                                </div>
                                            </div>
                                         
                                        </div>
        `);

        $('#modal_pembayaran_member_online').find('#kategori_diskon').val('Student Card');
        $('#modal_pembayaran_member_online').find('#nama_diskon').val('');
        $('#modal_pembayaran_member_online').find('#id_diskon').val('');
        $('#modal_pembayaran_member_online').find('#jenis_potongan').val(json_sc[0].jenis_potongan);
        $('#modal_pembayaran_member_online').find('#besar_potongan').val(json_sc[0].diskon);
        $('#modal_pembayaran_member_online').find('#rp_nilai_potongan').val(nilai_diskon);

        $('#modal_pembayaran_member_online').find('#simpan_tagihan').val(tagihan);
        $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(tagihan);
        $('#modal_pembayaran_member_online').find('#simpan_kembalian').val(0);

        
        $('#modal_pembayaran_member_online').find('.tagihan').html(number_format(tagihan));
        $('#modal_pembayaran_member_online').find('#show_dibayar').html(number_format(tagihan));

        $('#modal_pembayaran_member_online').find('#show_kembalian').html(0);
        $('#modal_pembayaran_member_online').find('#input_dibayar').val('');

    }else{
      
    $('#modal_pembayaran_member_online').find('#id_student_card').val('');
      $('#modal_pembayaran_member_online').find('.form_student_card').hide();
      $('#modal_pembayaran_member_online').find('.alert_student_card').hide();
      $('#modal_pembayaran_member_online').find('.data_student_card').hide();
       let arrayKata = kategori.split("|");
        var kelompok_potongan = arrayKata[0];
        var nama_diskon = arrayKata[1];
        var id_diskon = arrayKata[2];
        var jenis_potongan = arrayKata[3];
        var besar_diskon = arrayKata[4];

        if (jenis_potongan=='Persentase') {
          var nilai_diskon = biaya_paket * (besar_diskon / 100);
          var tagihan = biaya_paket - nilai_diskon + biaya_register;
          var caption_diskon = "Diskon "+ besar_diskon + '%<br>Rp. ' + number_format(nilai_diskon);
        }else{
          var nilai_diskon = besar_diskon;
          var caption_diskon = "Diskon Rp. "+ number_format(besar_diskon);
          var tagihan = biaya_paket - nilai_diskon + biaya_register;

        }
        $('#modal_pembayaran_member_online').find('#caption_potongan').html(`

                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6">`+kelompok_potongan+` - `+nama_diskon+`</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-2">
                                                <small class="text-danger" style="text-decoration: line-through;">
                                                `+ number_format(biaya_paket)+ `
                                                </small> <br>
                                                <small class="text-success">
                                                <b>`+caption_diskon+`</b>
                                                </small>
                                                </div>
                                            </div>
                                         
                                        </div>
        `);
        $('#modal_pembayaran_member_online').find('.tagihan').html(number_format(tagihan));
        $('#modal_pembayaran_member_online').find('#show_dibayar').html(number_format(tagihan));
        $('#modal_pembayaran_member_online').find('#show_kembalian').html(0);

        $('#modal_pembayaran_member_online').find('#kategori_diskon').val(kelompok_potongan);
        $('#modal_pembayaran_member_online').find('#nama_diskon').val(nama_diskon);
        $('#modal_pembayaran_member_online').find('#id_diskon').val(id_diskon);
        $('#modal_pembayaran_member_online').find('#jenis_potongan').val(jenis_potongan);
        $('#modal_pembayaran_member_online').find('#besar_potongan').val(besar_diskon);
        $('#modal_pembayaran_member_online').find('#input_dibayar').val('');
        $('#modal_pembayaran_member_online').find('#rp_nilai_potongan').val(nilai_diskon);

        $('#modal_pembayaran_member_online').find('#simpan_tagihan').val(tagihan);
        $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(tagihan);
        $('#modal_pembayaran_member_online').find('#simpan_kembalian').val(0);
    }
  });


</script>