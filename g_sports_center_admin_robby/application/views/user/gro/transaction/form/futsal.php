<form id="form_futsal">
  <div class="row ">
  <div class="col-md-4">
    
    <div class="main-card mb-3 card">
                                        <div class="card-header">jadwal futsal</div>
                                        <div class="card-body">
                                          


                                          <input type="hidden" name="id_pengunjung" id="id_pengunjung">
                                          <input type="hidden" name="kategori_order" id="kategori_order" value="New">
                                          <!-- <input type="hidden" name="kumpul_jam" id="kumpul_jam" value=""> -->
                                          <input type="hidden" name="simpan_lama_main" id="simpan_lama_main" value="0">
                                          <!-- untuk biaya biaya -->
                                          <input type="hidden" name="simpan_total" id="simpan_total" value="">
                                          <input type="hidden" name="total_biaya" id="total_biaya" value="0">
                                          <!-- untuk biaya biaya -->
                                          <!-- untuk diskon -->
                                          <input type="hidden" name="id_student_card" id="id_student_card" value="">
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

                                          <input type="hidden" name="opsi_tambah_metode_pembayaran" id="opsi_tambah_metode_pembayaran">



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
                                                            <div class="col-md-12 alert alert-info">Harap pilih tanggal main untuk menampilkan jam booking tersedia</div>
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
                                    </div>
  </div>
  <div class="col-md-4">
    


    <div class="main-card mb-3 card">
                                        <div class="card-header">Identitas Pengunjung
                                          <div class="btn-actions-pane-right actions-icon-btn">
                                            <a  id="tombol_simpanedit_member" class="btn-shadow btn btn-outline-info" onclick="simpanedit_identitas()" style="display:none">Perbaharui Data Member</a>
                                            <a  id="tombol_action_identitas_member" class="btn-shadow btn btn-outline-info" onclick="dt_member()">Perpanjangan Member</a>
                                          </div>
                                        </div>
                                        <div class="card-body">
                                         
                                           <div id="form_member_baru" style="max-height:450px; overflow-x: scroll">

                                             <div class="row">
                                              <div class="col-md-12">
                                                
                                               <div class="form-group">
                                                <label>Nama</label>
                                                <input type="text" name="nama" id="input_nama" class="form-control" value="" required>
                                              </div>
                                           
                                            <div class="form-group">
                                              <label>Alamat</label>
                                              <input type="text" name="alamat" id="input_alamat" class="form-control" >
                                            </div>
                                            <div class="form-group">
                                              <label>No HP</label>
                                              <input type="text" name="nohp" id="input_nohp" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                              <label>Keterangan</label>
                                              <textarea name="keterangan" id="keterangan" class="form-control" rows="5"></textarea>
                                            </div>
                                               
                                              </div>
                                             </div>










                                           </div>
                                        </div>
                                    </div>
  </div>






  <div class="col-md-4">
    
    <div class="main-card mb-3 card">
        <div class="card-header">Pembayaran</div>
        <div class="card-body">
          <div class="row">
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


















                    <div class="row">
                    
                    
                
                    
                      <div class="col-sm-6 col-md-6 col-xl-6">


                            <div class="card-shadow-warning border widget-chart widget-chart2 text-left card">
                                <div class="widget-content p-0 w-100">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6" id="caption_dibayar">Dibayar</div>
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
                                            <div class="text-nuted"  id="caption_kembalian">Kembalian</div>
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
    </div>
  </div>





    <div class="col-md-12 col-lg-12">
    
                            <button class="btn btn-block btn-info" onclick="konfirmasi()" type="button">Simpan</button>
                        </div>
</div>

</form>
  <script src="<?php echo base_url(); ?>assets/jquery_number/jquery.number.js"></script>

  <script type="text/javascript">
showAutoCurrency();
    
  function showAutoCurrency(){
    $('input.currency').number( true, 0 );
  }

    $('#metode_pembayaran').change(function(){
      var opsi_tambah_metode_pembayaran = $('#opsi_tambah_metode_pembayaran').val();
      if (opsi_tambah_metode_pembayaran=='1') {
        tambah_metode_pembayaran();
      }
    });
    function tambah_metode_pembayaran(){
      $('#opsi_tambah_metode_pembayaran').val(1);
      var metode_pembayaran_1 = $('#metode_pembayaran').val();
      var metode_pembayaran = '<?php echo json_encode(metode_pembayaran()) ?>';
      var parse_json = JSON.parse(metode_pembayaran);
      $('#form_tambah_metode_pembayaran').html(`<div class="col-sm-6 col-md-6 col-xl-6">
                         <div class="form-group">
                           <label>Metode Pembayaran</label>
                           <select class="form-control" name="metode_pembayaran_2" id="metode_pembayaran_2">
                           </select>
                         </div>
                      </div>
                        <div class="col-sm-6 col-md-6 col-xl-6">
                          <div class="form-group">
                           <label>Pembayaran</label>
                           <input class="form-control currency" placeholder="masukan jumlah yang dibayar pelanggan" id="input_dibayar_2" name="input_dibayar_2" onclick="showAutoCurrency()">
                         </div>
                      </div>
                        <div class="col-sm-12 col-md-12 col-xl-12">
                          <div class="form-group">
                           <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="batal_tambah_metode_pembayaran()">Batal Tambah Metode Pembayaran</button>
                         </div>
                      </div>`);

      $('#metode_pembayaran_2').html(``);
      $.each(parse_json, function(k,v){
        console.log(v);
        if (v.id_metode_pembayaran!=metode_pembayaran_1) {
          $('#metode_pembayaran_2').append(`<option value="`+v.id_metode_pembayaran+`">`+v.metode_pembayaran+`</option>`);
        }

      });
      // $('#form_tambah_metode_pembayaran').append(` `);




    // $('#input_dibayar_2').keyup(function(){
    //  var input_dibayar_1 = $('#input_dibayar').val();
    //  var input_dibayar_2 = $('#input_dibayar_2').val();
    //  var total_dibayar = parseInt(input_dibayar_1) + parseInt(input_dibayar_2);
    //  console.log(total_dibayar)
    //  $('#simpan_dibayar').val(total_dibayar);

    // });


                $('#input_dibayar_2').keyup(function(){
                  var input_dibayar_1 = $('#input_dibayar').val();
      var input_dibayar_2 = $('#input_dibayar_2').val();
      var dibayar_pelanggan = parseInt(input_dibayar_1) + parseInt(input_dibayar_2);
      $('#simpan_dibayar').val(dibayar_pelanggan);

                  var tagihan = $('#simpan_tagihan').val();
                  if (input_dibayar_1=='') {
                    // Swal.fire('Warning','Pembayaran pada metode pembayaran pertama harus di input dulu','error');
                          var sisa = input_dibayar_2 - tagihan;
                          $('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#show_dibayar').html("Rp. "+number_format(input_dibayar_2));
                  }else{
                    if (input_dibayar_2=='') {
                          var sisa = input_dibayar_1 - tagihan;
                          $('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#show_dibayar').html("Rp. "+number_format(input_dibayar_1));

                    }else{

                     if (dibayar_pelanggan >0) {

                          var sisa = dibayar_pelanggan - tagihan;
                          $('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                          $('#simpan_dibayar').val(dibayar_pelanggan);
                          $('#simpan_kembalian').val(sisa);
                     
                      }else{
                         $('#show_dibayar').html("Rp. "+ number_format(tagihan));
                        $('#simpan_dibayar').val(tagihan);
                        $('#simpan_kembalian').val(0);
                        $('#show_kembalian').html('Rp. 0');
                      }

                    }
                } 





                });








    }
    function batal_tambah_metode_pembayaran(){
      
      $('#opsi_tambah_metode_pembayaran').val(0);
      $('#form_tambah_metode_pembayaran').html(`

                        <div class="col-sm-12 col-md-12 col-xl-12">
                          <div class="form-group">
                           <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="tambah_metode_pembayaran()" id="tombol_tambah_metode_pembayaran">Tambah Metode Pembayaran</button>
                         </div>
                      </div>`);

      $('#metode_pembayaran_2').html(``);
      
  var input_dibayar_1 = $('#input_dibayar').val();
      var dibayar_pelanggan = parseInt(input_dibayar_1);

                  var tagihan = $('#simpan_tagihan').val();

                   if (dibayar_pelanggan >0) {

                        var sisa = dibayar_pelanggan - tagihan;
                        $('#show_kembalian').html("Rp. "+number_format(sisa));
                        $('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                        $('#simpan_dibayar').val(dibayar_pelanggan);
                        $('#simpan_kembalian').val(sisa);
                   
                    }else{
                       $('#show_dibayar').html("Rp. "+ number_format(tagihan));
                      $('#simpan_dibayar').val(tagihan);
                      $('#simpan_kembalian').val(0);
                      $('#show_kembalian').html('Rp. 0');
                    }



    }
  </script>

  <script type="text/javascript">

    function konfirmasi(){
        var tgl_main = $('#tgl_main').val();
        var lapangan = $('#lapangan').val();
        var nama = $('#input_nama').val();
        var alamat = $('#input_alamat').val();
        var nohp = $('#input_nohp').val();
        var keterangan = $('#keterangan').val();
        var lama_main = $('#simpan_lama_main').val();
        var status = $('#status').val();
        var kategori_diskon = $('#kategori_diskon').val() ;
        var input_dibayar = $('#input_dibayar').val() ;
        var simpan_total = $('#simpan_total').val();
        var simpan_tagihan = $('#simpan_tagihan').val();
        var simpan_dibayar = $('#simpan_dibayar').val();
        var simpan_kembalian = $('#simpan_kembalian').val();


        if (tgl_main=='') {
          Swal.fire('Error','Anda belum memilih tanggal main','error');
        }
        else if (lama_main==0) {
          Swal.fire('Error','Anda belum memilih jam main','error');
        }
        else if (nama=='') {
          Swal.fire('Error','Anda belum menginputkan nama','error');
        }
        // else if (alamat=='') {
        //   Swal.fire('Error','Anda belum menginputkan alamat','error');
        // }
        else if (nohp=='') {
          Swal.fire('Error','Anda belum menginputkan nohp','error');
        }
        else if (input_dibayar=='') {
          Swal.fire('Error','Harap input jumlah dibayar pelanggan','error');
        }else{
          var minimal_dp = simpan_tagihan * 0.5 ;
          if (simpan_dibayar < minimal_dp) {
            Swal.fire('Error','Transaksi tidak bisa dilanjutkan. minimal pembayaraan 50% ('+number_format(minimal_dp)+')','error');

          }else{
             if (kategori_diskon=='Student Card') {
                var id_student_card = $('#id_student_card').val();
              if (id_student_card=='') {
                Swal.fire('Error','Anda memilih potongan harga '+kategori_diskon+'. Silahkan pilih data student cardnya terlebih dahulu','error');
              }else{
                $('#konfirmasi_futsal').modal('show');
              }
            }else{
              $('#konfirmasi_futsal').modal('show');

            }
          }

        }



        if (simpan_kembalian<0) {
          $('#konfirmasi_futsal_caption_dibayar').html('DP');
          $('#konfirmasi_futsal_caption_kembalian').html('Sisa Pembayaran');
        }else{
          $('#konfirmasi_futsal_caption_dibayar').html('Dibayar');
          $('#konfirmasi_futsal_caption_kembalian').html('Kembalian');

        }




    var jenis_potongan = $('#jenis_potongan').val() ;
    var nama_diskon = $('#nama_diskon').val() ;
    var besar_diskon =$('#besar_potongan').val();
    var biaya_paket = parseInt($('#harga_paket').val()) ;


         if (jenis_potongan=='Persentase') {
          var nilai_diskon = simpan_total * (besar_diskon / 100);
          var tagihan = simpan_total - nilai_diskon;
          var caption_diskon = "Diskon "+ besar_diskon + '%<br>Rp. ' + number_format(nilai_diskon);
        }else{
          var nilai_diskon = besar_diskon;
          var caption_diskon = "Diskon Rp. "+ number_format(besar_diskon);
          var tagihan = simpan_total - nilai_diskon;

        }



    var diskon = kategori_diskon == '' ? 'Tidak ada potongan harga' : kategori_diskon +' - '+ nama_diskon  + '<br>' + caption_diskon  ;







    $('#konfirmasi_futsal').find('#konfirmasi_futsal_biaya').html(number_format(simpan_total));
    $('#konfirmasi_futsal').find('#konfirmasi_futsal_potongan').html(diskon);
    // $('#konfirmasi_futsal').find('#konfirmasi_futsal_student_card').html(formdata[25].value);
    $('#konfirmasi_futsal').find('#konfirmasi_futsal_tagihan').html(number_format(simpan_tagihan));
    $('#konfirmasi_futsal').find('#konfirmasi_futsal_dibayar').html(number_format(simpan_dibayar));
    $('#konfirmasi_futsal').find('#konfirmasi_futsal_kembalian').html(number_format(simpan_kembalian));





        $('#konfirmasi_futsal').find('#konfirmasi_futsal_tgl_main').html(tgl_main + '<br>Lapangan '+ lapangan);
        $('#konfirmasi_futsal').find('#konfirmasi_futsal_jam_main').html(`<ol>`);
        $('#konfirmasi_futsal').find('#konfirmasi_futsal_nama').html(nama);
        $('#konfirmasi_futsal').find('#konfirmasi_futsal_alamat').html(alamat);
        $('#konfirmasi_futsal').find('#konfirmasi_futsal_nohp').html(nohp);
        $('#konfirmasi_futsal').find('#konfirmasi_futsal_keterangan').html(keterangan);
        $('#konfirmasi_futsal').find('#konfirmasi_futsal_status').html(status);
      $('input[type=checkbox]').each(function () {
           if (this.checked) {
            var value = $(this).val();
           let pecah_val = value.split("|");

              $('#konfirmasi_futsal').find('#konfirmasi_futsal_jam_main').append(`<li>`+pecah_val[0]+` - Rp. `+number_format(pecah_val[1])+`</li>`);
           }
});
        $('#konfirmasi_futsal').find('#konfirmasi_futsal_jam_main').append(`</ol>`);


    }

    $('#tgl_main').change(function(){
      tampilkan_jadwal_main();
      // hitung_jam(this);
    });
    $('#lapangan').change(function(){
      if ($('#tgl_main').val()=='') {
          $('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Harap pilih tanggal main untuk menampilkan jam booking tersedia</div>')
      }else{
        tampilkan_jadwal_main();
      }
    });

    function tampilkan_jadwal_main(){

      var tgl_main = $('#tgl_main').val();
      var lapangan = $('#lapangan').val();


        $('.lama_main').html('Pilih Jam Main');
        $('.biaya_total').html(0);
        $('#total_biaya').val(0);

        $('#simpan_total').val(0);
        $('#simpan_tagihan').val(0);
        $('#simpan_dibayar').val(0);
        $('#simpan_kembalian').val(0);
        $('#simpan_lama_main').val(0);

        $('#potongan_harga').val('').change();

        $('#jam_main_dipilih').html("Pilih jam main");
        $('.biaya_total').html(number_format(0));
        $('.biaya_perjam').html(number_format(0));
        $('.tagihan').html(number_format(0));
        $('#show_dibayar').html(number_format(0));


     $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/futsal/jadwal_tersedia'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
                tgl_main : tgl_main,
                lapangan : lapangan,
              },
              success : function(data)
              {
                  $('#pilihan_jam').html(``);
                $.each(data, function (k,v){
                  $('#pilihan_jam').append(`<div class="col-md-4 show_jam_`+k+`">`);
                  $.each(v, function(k_jam, v_jam){
                    if (v_jam.tersedia=='0') {
                      $('.show_jam_' +k ).append('<i class="fa fa-times"></i><span style="color:red; margin-left:5px; text-decoration: line-through double;">'+v_jam.jam+'</span><br>');

                    }else{
                      $('.show_jam_' +k ).append('<input type="checkbox" name="jam_main[]" class="checkbok_jam" value="'+v_jam.jam+'|'+v_jam.harga+'" style="margin-right:5px" onclick="hitung_jam(this)" biaya="'+v_jam.harga+'" jam_main="'+v_jam.jam+'"><span style="color:green ; margin-left:5px">'+v_jam.jam+'</span><br>');

                    }

                  });

                  $('#pilihan_jam').append(`</div>`);
                });


              },
              error : function (){

              }
      });


    }
    var kumpul_jam = [];
    function hitung_jam(x){

        $('#potongan_harga').val('').change();
      jumlah_jam = $('.checkbok_jam').filter(':checked').length;
      var biaya = $(x).attr('biaya');
      var jam_main = $(x).attr('jam_main');
      var biaya_sebelumnya = $('#total_biaya').val();
      var biaya_setelahnya = parseInt(biaya_sebelumnya) + parseInt(biaya);

      var value = $(x).val();
   

      if (jumlah_jam==0) {
        $('.lama_main').html('Pilih Jam Main');
        $('.biaya_total').html(0);
        $('#total_biaya').val(0);

        $('#simpan_total').val(0);
        $('#simpan_tagihan').val(0);
        $('#simpan_dibayar').val(0);
        $('#simpan_kembalian').val(0);
        $('#simpan_lama_main').val(0);

      }else{
        if ($(x).is(':checked')) {
          var biaya_setelahnya = parseInt(biaya_sebelumnya) + parseInt(biaya);
          var arr_baru = kumpul_jam.push(jam_main); 
          $('#kumpul_jam').val(kumpul_jam);

        }else{
          var biaya_setelahnya = parseInt(biaya_sebelumnya) - parseInt(biaya);
          kumpul_jam.filter(checked_jam_main);

        }

        $('.biaya_total').html(number_format(biaya_setelahnya));
        $('.biaya_perjam').html(number_format(biaya));
        $('#jam_main_dipilih').html("Biaya jam "+jam_main);
        $('#total_biaya').val(biaya_setelahnya);
        $('#simpan_total').val(biaya_setelahnya);
        $('#simpan_tagihan').val(biaya_setelahnya);
        $('#simpan_dibayar').val(biaya_setelahnya);
        $('#simpan_kembalian').val(0);
        $('.lama_main').html(jumlah_jam + " Jam");
        $('#simpan_lama_main').val(jumlah_jam);

        $('.tagihan').html(number_format(biaya_setelahnya));
        $('#show_dibayar').html(number_format(biaya_setelahnya));
        function checked_jam_main() {
          return jam_main;
        }
      }




                // $('#input_dibayar').keyup(function(){
                //   var dibayar_pelanggan = $('#input_dibayar').val();
                //   var tagihan = $('#simpan_tagihan').val();

                //    if (dibayar_pelanggan =='') {
                //     $('#show_dibayar').html("Rp. "+ number_format(tagihan));
                //     $('#simpan_dibayar').val(tagihan);
                //     $('#simpan_kembalian').val(0);
                //     $('#show_kembalian').html('Rp. 0');
                //     }else{

                //         var sisa = dibayar_pelanggan - tagihan;
                //         if (sisa>0) {

                //         }else{

                //         }
                //         $('#show_kembalian').html("Rp. "+number_format(sisa));
                //         $('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                //         $('#simpan_dibayar').val(dibayar_pelanggan);
                //         $('#simpan_kembalian').val(sisa);
                //     }








                // });



    }






$('#input_dibayar').keyup(function(){
  var dibayar_pelanggan = $('#input_dibayar').val();
  var tagihan = $('#simpan_tagihan').val();

   if (dibayar_pelanggan =='') {
    $('#show_dibayar').html("Rp. "+ number_format(tagihan));
    $('#simpan_dibayar').val(tagihan);
    $('#simpan_kembalian').val(0);
    $('#show_kembalian').html('Rp. 0');

    $('#opsi_tambah_metode_pembayaran').val(0);
    batal_tambah_metode_pembayaran();
    $('#tombol_tambah_metode_pembayaran').hide();

    }else{



        var sisa = dibayar_pelanggan - tagihan;

      if (sisa < 0) {
        $('#caption_dibayar').html('DP');
        $('#caption_kembalian').html('Sisa');
        $('#tombol_tambah_metode_pembayaran').show();

      }else{
        $('#caption_dibayar').html('Dibayar');
        $('#caption_kembalian').html('Kembalian');
        $('#tombol_tambah_metode_pembayaran').hide();
      }

        $('#show_kembalian').html("Rp. "+number_format(sisa));
        $('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
        $('#simpan_dibayar').val(dibayar_pelanggan);
        $('#simpan_kembalian').val(sisa);
    }

});




  function dt_member(){

    $('#data_member_futsal_badminton').modal('show');
    $('#data_member_futsal_badminton').find('#jenis_fasilitas').html('Futsal');
    $('#list_member_futsal_badminton').DataTable(
    {
          processing  : true,
          serverSide  : true,
          bDestroy  : true,
          responsive  : true,
          ajax    : {
                   url: '<?php echo base_url() ?>user/gro/transaction/futsal/dt_member',
                    type  : "POST",
                    data  : {
                    },
                  },
      });
  }


  function pilih_member(nama, alamat, nohp, id_pengunjung){
        $('#id_pengunjung').val(id_pengunjung);
        $('#input_nama').val(nama);
        $('#input_alamat').val(alamat);
        $('#input_nohp').val(nohp);
        $('#kategori_order').val('Repeat');
        $('#data_member_futsal_badminton').modal('hide');

                $('#tombol_action_identitas_member').attr('onclick','member_baru()');
                $('#tombol_simpanedit_member').show();
                $('#tombol_action_identitas_member').html('Member baru');
  }

  function member_baru(){

                $('#tombol_simpanedit_member').hide();
        $('#kategori_order').val('New');
        $('#input_nama').val('');
        $('#input_alamat').val('');
        $('#input_nohp').val('');
        $('#id_pengunjung').val('');
    $('#tombol_action_identitas_member').attr('onclick','dt_member()');
    $('#tombol_action_identitas_member').html('Perpanjangan Member');
  }



  
  $('#potongan_harga').change(function(){
    $('#input_dibayar').val('');
    var kategori = $('#potongan_harga').val();
    var total = parseInt($('#simpan_total').val());
    var biaya_paket = parseInt($('#harga_paket').val());
    
    if (kategori=='') {

    $('#id_student_card').val('');
        $('#kategori_diskon').val('');
        $('#nama_diskon').val('');
        $('#id_diskon').val('');
        $('#jenis_potongan').val('');
        $('#besar_potongan').val('');
        $('#rp_nilai_potongan').val('');

        $('#simpan_tagihan').val(total);
        $('#simpan_dibayar').val('');
        $('#simpan_kembalian').val('');
        $('#show_kembalian').html(number_format(0));


        $('#caption_potongan').html(`

                                    <div class="widget-content-outer">
                                       
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-2"><small class="total_diskon text-dark"><b>Tidak ada potongan harga</b></small></div>
                                            </div>
                                         
                                        </div>
                                    </div>
        `);
        $('.tagihan').html(number_format(total));
        $('#show_dibayar').html(number_format(total));



      $('.form_student_card').hide();
      
      $('.alert_student_card').hide();
      $('.data_student_card').hide();
    }
    else if (kategori=='Student Card') {
      $('.form_student_card').show();

      var student_card = '<?php echo student_card() ?>';
      var json_sc = JSON.parse(student_card);
      var besar_diskon = json_sc[0].diskon ; 
      var jenis_potongan_sc = json_sc[0].jenis_potongan;
         if (jenis_potongan_sc=='Persentase') {
          var nilai_diskon = total * (besar_diskon / 100);
          var tagihan = total - nilai_diskon;
          var caption_diskon = "Diskon "+ besar_diskon + '%<br>Rp. ' + number_format(nilai_diskon);
        }else{
          var nilai_diskon = besar_diskon;
          var caption_diskon = "Diskon Rp. "+ number_format(besar_diskon);
          var tagihan = total - nilai_diskon;

        }

      show_student_card();

        $('#caption_potongan').html(`

                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6">Diskon Student Card</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-2">
                                                <small class="text-danger" style="text-decoration: line-through;">
                                                `+ number_format(total)+ `
                                                </small> <br>
                                                <small class="text-success">
                                                <b>`+caption_diskon+`</b>
                                                </small>
                                                </div>
                                            </div>
                                         
                                        </div>
        `);

        $('#kategori_diskon').val('Student Card');
        $('#nama_diskon').val('');
        $('#id_diskon').val('');
        $('#jenis_potongan').val(json_sc[0].jenis_potongan);
        $('#besar_potongan').val(json_sc[0].diskon);
        $('#rp_nilai_potongan').val(nilai_diskon);

        $('#simpan_tagihan').val(tagihan);
        $('#simpan_dibayar').val(tagihan);
        $('#simpan_kembalian').val(0);
        $('#show_kembalian').html(number_format(0));

        
        $('.tagihan').html(number_format(tagihan));
        $('#show_dibayar').html(number_format(tagihan));

    }else{

      $('.form_student_card').hide();
      $('.alert_student_card').hide();
      $('.data_student_card').hide();
       let arrayKata = kategori.split("|");
        var kelompok_potongan = arrayKata[0];
        var nama_diskon = arrayKata[1];
        var id_diskon = arrayKata[2];
        var jenis_potongan = arrayKata[3];
        var besar_diskon = arrayKata[4];


        if (jenis_potongan=='Persentase') {
          var nilai_diskon = total * (besar_diskon / 100);
          var tagihan = total - nilai_diskon;
          var caption_diskon = "Diskon "+ besar_diskon + '%<br>Rp. ' + number_format(nilai_diskon);
        }else{
          var nilai_diskon = besar_diskon;
          var caption_diskon = "Diskon Rp. "+ number_format(besar_diskon);
          var tagihan = total - nilai_diskon;

        }
        $('#caption_potongan').html(`

                                        <div class="widget-content-left">
                                            <div class="text-muted opacity-6">`+kelompok_potongan+` - `+nama_diskon+`</div>
                                        </div>
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left pr-2">
                                                <div class="widget-numbers mt-0 fsize-2">
                                                <small class="text-danger" style="text-decoration: line-through;">
                                                `+ number_format(total)+ `
                                                </small> <br>
                                                <small class="text-success">
                                                <b>`+caption_diskon+`</b>
                                                </small>
                                                </div>
                                            </div>
                                         
                                        </div>
        `);
        $('.tagihan').html(number_format(tagihan));
        $('#show_dibayar').html(number_format(tagihan));

        $('#kategori_diskon').val(kelompok_potongan);
        $('#nama_diskon').val(nama_diskon);
        $('#id_diskon').val(id_diskon);
        $('#jenis_potongan').val(jenis_potongan);
        $('#besar_potongan').val(besar_diskon);
        $('#rp_nilai_potongan').val(nilai_diskon);

        $('#simpan_tagihan').val(tagihan);
        $('#simpan_dibayar').val(tagihan);
        $('#simpan_kembalian').val(0);
        $('#show_kembalian').html(number_format(0));

    $('#id_student_card').val('');


    }
  });




  function simpanedit_identitas(){

    var formdata = $('#form_futsal').serialize();
     $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/futsal/simpanedit_identitas'),
              type    : 'POST',
              data    : formdata,
              dataType : 'JSON',
              success : function(data)
              {
                  console.log(data);
                  Swal.fire('Sukses',data.pesan,'success');
              },
              error : function (){
                alert('ee');
              }
      });

  }



  function simpan_transaksi_futsal(){

    var formdata = $('#form_futsal').serialize();
     $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/futsal/simpan_transaksi_futsal'),
              type    : 'POST',
              // dataType : 'JSON',
              // mimeType: "multipart/form-data",
              // enctype: 'multipart/form-data',
              // cache: false,
              // contentType: false,
              // processData: false,
              data    : formdata,
              success : function(data)
              {
              if (data.response=='200') {
                // toastr.success('Sukses');
                  Swal.fire('Sukses','Data disimpan','success');
                  setTimeout(close_swal, 3000);
                  print_transaksi(data.id_transaksi);
                  $('#detail_order_fasilitas').html(`
<div class="alert alert-info">Transaksi Futsal - Harian Disimpan <br>
<button type="button" onclick="print_transaksi(`+data.id_transaksi+`)" class="btn btn-info ">Print Struk</button>
<button id="btn_tab_new_transaction" class="btn btn-info" data-toggle="tab" href="#new_transaction" onclick="show_pilihan_fasilitas()">
                                <span>Transaksi Baru</span>
                            </button>
</div>`);
              }else{
                toastr.success('Sukses');
                  Swal.fire('Error','Data tidak ditemukan','error');
                  setTimeout(close_swal, 3000);

              }
              },
              error : function (){
                alert('ee');
              }
      });

  }


  function print_transaksi(id_transaksi){
     $('#konfirmasi_futsal').modal('hide');
    $('#print_transaksi_gym').modal('show');
    $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
      <iframe src="`+ baseUrl('/user/gro/transaction/futsal/print_transaksi_futsal_harian/') + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
      `);
  }

  </script>
