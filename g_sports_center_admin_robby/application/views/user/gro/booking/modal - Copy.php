<!-- futsal -->
  <form action="#" id="form_konfirmasi_booking_futsal" method='post'> 
<div class="modal fade" id="cek_jadwal_futsal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Order <span id="fasilitas"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_booking" id="id_booking">
                <input type="hidden" name="id_identias_order" id="id_identias_order">
                <input type="hidden" name="id_transaksi_sementara" id="id_transaksi_sementara">
                <input type="hidden" name="simpan_total" id="total">
                <input type="hidden" name="simpan_tagihan" id="tagihan">
                <input type="hidden" name="dp" id="dp">
                <input type="hidden" name="sisa" id="sisa">

                <input type="hidden" name="nama" id="simpan_nama">

                                          <input type="hidden" name="id_student_card" id="id_student_card" value="">
                                          <input type="hidden" name="kategori_diskon" id="kategori_diskon" value="">
                                          <input type="hidden" name="id_diskon" id="id_diskon" value="">
                                          <input type="hidden" name="nama_diskon" id="nama_diskon" value="">
                                          <input type="hidden" name="jenis_potongan" id="jenis_potongan" value="">
                                          <input type="hidden" name="besar_potongan" id="besar_potongan" value="">
                                          <input type="hidden" name="rp_nilai_potongan" id="rp_nilai_potongan" value="">




                                          <input type="hidden" name="jenis_kembalian" id="jenis_kembalian" value="">
                                          <input type="hidden" name="simpan_dibayar" id="simpan_dibayar" value="">
                                          <input type="hidden" name="simpan_kembalian" id="simpan_kembalian" value="">

                                          <input type="hidden" name="opsi_tambah_metode_pembayaran" id="opsi_tambah_metode_pembayaran">





               <div class="row">
                  <div class="col-md-6">
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
                    
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                     
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-12">
                  <hr>  

                    <table class="table">
                     
                      <thead>
                        <tr>
                        <td colspan="7"><b>Jadwal Futsal</b> <span id="nama_paket"></span></td>
                       
                      </tr>
                        <tr>
                        <td>No</td>
                        <td>Lapangan</td>
                        <td>Tanggal Main</td>
                        <td>Jam Main</td>
                        <td>Status</td>
                        <td>Biaya</td>
                        <td>Option</td>
                       
                      </tr>
                      </thead>
                      <tbody id="list_jadwal_futsal">
                        
                      </tbody>
                     
                    </table>
                  </div>
                 
                  <div class="col-md-12">
                    <div class="btn-group btn-block">
                        
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    <!-- <button class="btn  btn-outline-info" onclick="selesai_main_semua_jadwal()" type="button">Selesai main semua jadwal</button> -->
                    </div>
                  </div>
                </div>





                </div>
               
        
        </div>
    </div>
</div>







<div class="modal fade" id="konfirmasi_pembayaran_booking_futsal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Pembayaran <br> <span class="nama"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
               
                  



                <input type="hidden" name="" id="jenis_futsal">





                  <div id="form_pembayaran">

                  <div class="row">

                      <div class="col-sm-12 col-md-12 col-xl-12 mb-3">

                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-nuted">Grand Total</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="total_biaya">0</span></div>
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
                                            <div class="text-nuted" id="caption_dp">DP</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="dp">0</span></div>
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
                                            <div class="text-nuted" id="caption_sisa_pembayaran">Sisa pembayaran</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="sisa_pembayaran">0</span></div>
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
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span id="show_kembalian">Rp. 0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>

                        <div class="col-md-12 col-lg-12">
                            <button class="btn btn-block btn-success mt-3" type="button" onclick="simpan_visit_futsal_harian()" id="tombol_visit_harian">Simpan Visit</button>
                            <button class="btn btn-block btn-info mt-3" type="button" onclick="simpan_visit_futsal_turnamen()" id="tombol_visit_turnamen">Simpan Visit</button>
                        </div>
                    
                  </div>
                </div>



              </div>
           
        </div>
    </div>
</div>
  </form>








<div class="modal fade" id="konfirmasi_visit_futsal_bulanan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Pembayaran <br> <span class="nama"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
               
                  







                  <div id="form_pembayaran">

                  <div class="row">

                      <div class="col-sm-6 col-md-6 col-xl-6 mb-3">

                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-nuted">Biaya Sebelumnya</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="total_biaya">0</span></div>
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
                                            <div class="text-nuted">Biaya Perubahan Jadwal</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="total_biaya">0</span></div>
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
                                            <div class="text-nuted" id="caption_dp">Telah Dibayar</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="dp">0</span></div>
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
                                            <div class="text-nuted" id="caption_sisa_pembayaran">Sisa pembayaran</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="sisa_pembayaran">0</span></div>
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
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span id="show_kembalian">Rp. 0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>

                        <div class="col-md-12 col-lg-12">
                           
                            <button class="btn btn-block btn-info mt-3" type="button" onclick="simpan_visit_futsal_turnamen()" id="tombol_visit_turnamen">Visit</button>
                        </div>
                    
                  </div>
                </div>



              </div>
           
        </div>
    </div>
</div>

<!-- futsal -->





<!-- badminton -->

  <form action="#" id="form_konfirmasi_booking_badminton" method='post'> 
<div class="modal fade" id="cek_jadwal_badminton" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Order <span id="fasilitas"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_booking" id="id_booking">
                <input type="hidden" name="id_identias_order" id="id_identias_order">
                <input type="hidden" name="id_transaksi_sementara" id="id_transaksi_sementara">
                <input type="hidden" name="simpan_total" id="total">
                <input type="hidden" name="simpan_tagihan" id="tagihan">
                <input type="hidden" name="dp" id="dp">
                <input type="hidden" name="sisa" id="sisa">

                <input type="hidden" name="nama" id="simpan_nama">

                                          <input type="hidden" name="id_student_card" id="id_student_card" value="">
                                          <input type="hidden" name="kategori_diskon" id="kategori_diskon" value="">
                                          <input type="hidden" name="id_diskon" id="id_diskon" value="">
                                          <input type="hidden" name="nama_diskon" id="nama_diskon" value="">
                                          <input type="hidden" name="jenis_potongan" id="jenis_potongan" value="">
                                          <input type="hidden" name="besar_potongan" id="besar_potongan" value="">
                                          <input type="hidden" name="rp_nilai_potongan" id="rp_nilai_potongan" value="">




                                          <input type="hidden" name="jenis_kembalian" id="jenis_kembalian" value="">
                                          <input type="hidden" name="simpan_dibayar" id="simpan_dibayar" value="">
                                          <input type="hidden" name="simpan_kembalian" id="simpan_kembalian" value="">





               <div class="row">
                  <div class="col-md-6">
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
                    
                    </table>



                  </div>
                  <div class="col-md-6">
                    <table class="table">
                     
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-12">
                  <hr>  

                    <table class="table">
                     
                      <thead>
                        <tr>
                        <td colspan="7"><b>Jadwal badminton</b> <span id="nama_paket"></span></td>
                       
                      </tr>
                        <tr>
                        <td>No</td>
                        <td>Lapangan</td>
                        <td>Tanggal Main</td>
                        <td>Jam Main</td>
                        <td>Status</td>
                        <td>Biaya</td>
                        <td>Option</td>
                       
                      </tr>
                      </thead>
                      <tbody id="list_jadwal_badminton">
                        
                      </tbody>
                     
                    </table>
                  </div>
                 
                  <div class="col-md-12">
                    <div class="btn-group btn-block">
                        
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    <!-- <button class="btn  btn-outline-info" onclick="selesai_main_semua_jadwal()" type="button">Selesai main semua jadwal</button> -->
                    </div>
                  </div>
                </div>





                </div>
               
        
        </div>
    </div>
</div>







<div class="modal fade" id="konfirmasi_pembayaran_booking_badminton" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Pembayaran <br> <span class="nama"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
              <div class="modal-body">
               
                  



                <input type="hidden" name="" id="jenis_badminton">





                  <div id="form_pembayaran">

                  <div class="row">

                      <div class="col-sm-12 col-md-12 col-xl-12 mb-3">

                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-nuted">Grand Total</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="total_biaya">0</span></div>
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
                                            <div class="text-nuted" id="caption_dp">DP</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="dp">0</span></div>
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
                                            <div class="text-nuted" id="caption_sisa_pembayaran">Sisa pembayaran</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span class="sisa_pembayaran">0</span></div>
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
                                                <div class="widget-numbers mt-0 fsize-3 text-warning"><span id="show_kembalian">Rp. 0</span></div>
                                            </div>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>


                      </div>

                        <div class="col-md-12 col-lg-12">
                            <button class="btn btn-block btn-success mt-3" type="button" onclick="simpan_visit_badminton_harian()" id="tombol_visit_harian">Simpan Visit</button>
                            <button class="btn btn-block btn-info mt-3" type="button" onclick="simpan_visit_badminton_turnamen()" id="tombol_visit_turnamen">Simpan Visit</button>
                        </div>
                    
                  </div>
                </div>



              </div>
           
        </div>
    </div>
</div>
  </form>

<!-- badminton -->





<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('/user/gro/booking') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter Jadwal Futsal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Pilih Tanggal</label>
                <input type="date" name="tgl" class="form-control" required>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block" onclick="filter()">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>


<div class="modal fade" id="cek_lapangan_futsal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="#" method='post'> 
    <div class="modal-dialog modal-xl " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Order <span id="fasilitas"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="" id="id_identias_order">

               <div class="row">
                  <div class="col-md-6">
                    Identitas lapangan 
                    <table class="table">
                      <tr>
                        <td>Fasilitas</td>
                        <td>:</td>
                        <td id="fasilitas"></td>
                      </tr>
                      <tr>
                        <td>Lapangan</td>
                        <td>:</td>
                        <td id="lapangan"></td>
                      </tr>
                      <tr>
                        <td>Tgl Main</td>
                        <td>:</td>
                        <td id="tgl_main"></td>
                      </tr>
                   
                    </table>



                  </div>
                  <div class="col-md-6">

                    Identitas Pembooking 
                    <table class="table">
                     
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="nama"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-12">
                  <hr>  

                    <table class="table">
                     
                      <thead>
                        <tr>
                        <td colspan="7"><b>Jadwal Futsal dari pembooking</b></td>
                       
                      </tr>
                        <tr>
                        <td>No</td>
                        <td>Lapangan</td>
                        <td>Tanggal Main</td>
                        <td>Jam Main</td>
                        <td>Status</td>
                        <td>Option</td>
                       
                      </tr>
                      </thead>
                      <tbody id="list_jadwal_futsal">
                        
                      </tbody>
                     
                    </table>
                  </div>
                 
                  <div class="col-md-12">
                  </div>
                </div>





                </div>
               
        
        </div>
    </div>
  </form>
</div>



<div class="modal fade" id="cek_lapangan_badminton" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="#" method='post'> 
    <div class="modal-dialog modal-xl " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Order <span id="fasilitas"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="" id="id_identias_order">

               <div class="row">
                  <div class="col-md-6">
                    Identitas lapangan 
                    <table class="table">
                      <tr>
                        <td>Fasilitas</td>
                        <td>:</td>
                        <td id="fasilitas"></td>
                      </tr>
                      <tr>
                        <td>Lapangan</td>
                        <td>:</td>
                        <td id="lapangan"></td>
                      </tr>
                      <tr>
                        <td>Tgl Main</td>
                        <td>:</td>
                        <td id="tgl_main"></td>
                      </tr>
                      <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td id="status"></td>
                      </tr>
                    
                    </table>



                  </div>
                  <div class="col-md-6">

                    Identitas Pembooking 
                    <table class="table">
                     
                      <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td id="nama"></td>
                      </tr>
                      <tr>
                        <td>No HP</td>
                        <td>:</td>
                        <td id="nohp"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="keterangan"></td>
                      </tr>
                    </table>



                  </div>
                  <div class="col-md-12">
                  <hr>  

                    <table class="table">
                     
                      <thead>
                        <tr>
                        <td colspan="7"><b>Jadwal badminton lain dari pembooking</b></td>
                       
                      </tr>
                        <tr>
                        <td>No</td>
                        <td>Lapangan</td>
                        <td>Tanggal Main</td>
                        <td>Jam Main</td>
                        <td>Status</td>
                       
                      </tr>
                      </thead>
                      <tbody id="list_jadwal_badminton">
                        
                      </tbody>
                     
                    </table>
                  </div>
                 
                  <div class="col-md-12">
                  </div>
                </div>





                </div>
               
        
        </div>
    </div>
  </form>
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
                                          <input type="hidden" name="biaya_sebelumnya" id="biaya_sebelumnya" value="0">
                                          <input type="hidden" name="jadwal_ke" id="jadwal_ke">
                                          <input type="hidden" name="id_jadwal" id="id_jadwal">
                                          <input type="hidden" name="jam_dipilih" id="jam_dipilih">
                                          <input type="hidden" name="id_booking" id="id_booking">






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
                                                          <label>Jam Main Sebelumnya</label>
                                                           <div class="row" id="jam_main_terpilih">
                                                            
                                                           </div>
                                                        </div>
                                              </li>
                                              <li class="list-group-item">
                                                    <div class="form-group" id="form_jam_main">
                                                          <label>Pilihan Jam Main yang tersedia</label>
                                                           <div class="row" id="pilihan_jam">
                                                            <div class="col-md-12 alert alert-info">Pilih tanggal dulu</div>
                                                           </div>
                                                        </div>
                                              </li>
                                             
                                              

                                            </ul>


                  </div>



                 
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="set_jadwal_futsal()" type="button" id="tombol_update_jadwal">Simpan</button>
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
                                          <input type="hidden" name="biaya_sebelumnya" id="biaya_sebelumnya" value="0">
                                          <input type="hidden" name="jadwal_ke" id="jadwal_ke">
                                          <input type="hidden" name="id_jadwal" id="id_jadwal">
                                          <input type="hidden" name="jam_dipilih" id="jam_dipilih">
                                          <input type="hidden" name="id_booking" id="id_booking">






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
                                                            <option value="3">Lapangan 3</option>
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
                                                          <label>Jam Main Sebelumnya</label>
                                                           <div class="row" id="jam_main_terpilih">
                                                            
                                                           </div>
                                                        </div>
                                              </li>
                                              <li class="list-group-item">
                                                    <div class="form-group" id="form_jam_main">
                                                          <label>Pilihan Jam Main yang tersedia</label>
                                                           <div class="row" id="pilihan_jam">
                                                            <div class="col-md-12 alert alert-info">Pilih tanggal dulu</div>
                                                           </div>
                                                        </div>
                                              </li>
                                             
                                              

                                            </ul>


                  </div>



                 
                  <div class="col-md-12">
                    <button class="btn btn-block btn-info" onclick="set_jadwal_badminton()" type="button" id="tombol_update_jadwal">Simpan</button>
                  </div>
                </div>
              </div>
           
        </div>
    </div>
  </form>
</div>

  <script src="<?php echo base_url(); ?>assets/jquery_number/jquery.number.js"></script> 
<script type="text/javascript">
   showAutoCurrency();
  function showAutoCurrency(){
    $('input.currency').number( true, 0 );
  }


  data_order_futsal('harian');
  function data_order_futsal(jenis){
    dt_order_futsal(jenis);
  }
  function dt_order_futsal(jenis){

    // $('#data_student_card').modal('show');
    $('#list_order_futsal_' + jenis).DataTable(
    {
          processing  : true,
          serverSide  : true,
          bDestroy  : true,
          // responsive  : true,
          ajax    : {
                   url: '<?php echo base_url() ?>user/gro/booking/dt_order_futsal',
                    type  : "POST",
                    data  : {
                      tgl : '<?php echo $this->input->get('tgl') ?>',
                      jenis : jenis
                    },
                  },
      });
  }

  data_order_badminton('harian');
  function data_order_badminton(jenis){
    dt_order_badminton(jenis);
  }
  function dt_order_badminton(jenis){

    // $('#data_student_card').modal('show');
    $('#list_order_badminton_' + jenis).DataTable(
    {
          processing  : true,
          serverSide  : true,
          bDestroy  : true,
          // responsive  : true,
          ajax    : {
                   url: '<?php echo base_url() ?>user/gro/booking/dt_order_badminton',
                    type  : "POST",
                    data  : {
                      tgl : '<?php echo $this->input->get('tgl') ?>',
                      jenis : jenis
                    },
                  },
      });
  }




  rekap_futsal('bulanan', '<?php echo date('m') ?>', '<?php echo date('Y') ?>');
  function rekap_futsal(jenis, bulan, tahun){
    dt_rekap_futsal(jenis, bulan, tahun);
  }
  function dt_rekap_futsal(jenis, bulan, tahun){

    // $('#data_student_card').modal('show');
    $('#list_rekap_futsal_' + jenis).DataTable(
    {
          processing  : true,
          serverSide  : true,
          bDestroy  : true,
          // responsive  : true,
          ajax    : {
                   url: '<?php echo base_url() ?>user/gro/booking/dt_rekap_futsal',
                    type  : "POST",
                    data  : {
                      tgl : '<?php echo $this->input->get('tgl') ?>',
                      bulan : bulan,
                      tahun : tahun,
                      jenis : jenis
                    },
                  },
      });
  }




  
  rekap_badminton('bulanan', '<?php echo date('m') ?>', '<?php echo date('Y') ?>');
  function rekap_badminton(jenis, bulan, tahun){
    dt_rekap_badminton(jenis, bulan, tahun);
  }
  function dt_rekap_badminton(jenis, bulan, tahun){

    // $('#data_student_card').modal('show');
    $('#list_rekap_badminton_' + jenis).DataTable(
    {
          processing  : true,
          serverSide  : true,
          bDestroy  : true,
          // responsive  : true,
          ajax    : {
                   url: '<?php echo base_url() ?>user/gro/booking/dt_rekap_badminton',
                    type  : "POST",
                    data  : {
                      tgl : '<?php echo $this->input->get('tgl') ?>',
                      bulan : bulan,
                      tahun : tahun,
                      jenis : jenis
                    },
                  },
      });
  }
</script>