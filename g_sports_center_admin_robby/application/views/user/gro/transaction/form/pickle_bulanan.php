<form id="form_pickle">

  <div class="row ">
  <div class="col-md-4">
    
  <input type="hidden" name="id_pengunjung" id="id_pengunjung">
    <div class="main-card mb-3 card">
                                        <div class="card-header">jadwal pickle</div>
                                        <div class="card-body">

                                            <ul class="list-group list-group-flush">
                                              <li class="list-group-item">
                                                <div class="row justify-align-center">
                                                  
                                                  <div class="col-md-6 mb-3">
                                                    <a href="javascript:void(0)" onclick="pilih_jadwal(1)" id="jadwal_1">Pilih Jadwal 1</a>
                                                    <input type="hidden" name="" placeholder="tgl 1" id="tgl_1_terpilih">
                                                    <input type="hidden" name="" placeholder="jam 1" id="jam_1_terpilih">
                                                    <input type="hidden" name="" placeholder="biaya 1" id="biaya_1_terpilih">
                                                    <input type="hidden" name="" placeholder="jumlah_jam 1" id="jumlah_jam_1_terpilih">
                                                    <input type="hidden" name="" placeholder="status 1" id="status_1_terpilih">
                                                  </div>
                                                  <div class="col-md-6 mb-3">
                                                    <a href="javascript:void(0)" onclick="pilih_jadwal(2)" id="jadwal_2">Pilih Jadwal 2</a>
                                                    <input type="hidden" name="" placeholder="tgl 2" id="tgl_2_terpilih">
                                                    <input type="hidden" name="" placeholder="jam 2" id="jam_2_terpilih">
                                                    <input type="hidden" name="" placeholder="biaya 2" id="biaya_2_terpilih">
                                                    <input type="hidden" name="" placeholder="jumlah_jam 2" id="jumlah_jam_2_terpilih">
                                                    <input type="hidden" name="" placeholder="status 2" id="status_2_terpilih">
                                                  </div>
                                                  <div class="col-md-6 mb-3">
                                                    <a href="javascript:void(0)" onclick="pilih_jadwal(3)" id="jadwal_3">Pilih Jadwal 3</a>
                                                    <input type="hidden" name="" placeholder="tgl 3" id="tgl_3_terpilih">
                                                    <input type="hidden" name="" placeholder="jam 3" id="jam_3_terpilih">
                                                    <input type="hidden" name="" placeholder="biaya 3" id="biaya_3_terpilih">
                                                    <input type="hidden" name="" placeholder="jumlah_jam 3" id="jumlah_jam_3_terpilih">
                                                    <input type="hidden" name="" placeholder="status 3" id="status_3_terpilih">
                                                  </div>
                                                  <div class="col-md-6 mb-3">
                                                    <a href="javascript:void(0)" onclick="pilih_jadwal(4)" id="jadwal_4">Pilih Jadwal 4</a>
                                                    <input type="hidden" name="" placeholder="tgl 4" id="tgl_4_terpilih">
                                                    <input type="hidden" name="" placeholder="jam 4" id="jam_4_terpilih">
                                                    <input type="hidden" name="" placeholder="biaya 4" id="biaya_4_terpilih">
                                                    <input type="hidden" name="" placeholder="jumlah_jam 4" id="jumlah_jam_4_terpilih">
                                                    <input type="hidden" name="" placeholder="status 4" id="status_4_terpilih">




                                          <input type="hidden" name="kategori_order" id="kategori_order" value="New">
                                          <input type="hidden" name="simpan_biaya_pickle" id="simpan_biaya_pickle" value="">
                                          <input type="hidden" name="simpan_total" id="simpan_total" value="">
                                          <input type="hidden" name="diskon_bulanan" id="diskon_bulanan" value="">
                                          <input type="hidden" name="besar_diskon_bulanan" id="besar_diskon_bulanan" value="">
                                          <input type="hidden" name="simpan_diskon_bulanan" id="simpan_diskon_bulanan" value="">
                                          <!-- untuk biaya biaya -->
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

                                          
                                          <input type="hidden" name="opsi_tambah_metode_pembayaran" id="opsi_tambah_metode_pembayaran">


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
                                                                      <div class="text-muted opacity-6">Biaya</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left pr-2">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-success total_harga">0</div>
                                                                      </div>
                                                                     
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-6 col-lg-6">
                                                      <div class="card-shadow-warning mt-3 mb-2 border widget-chart widget-chart2 text-left card">
                                                          <div class="widget-content p-0 w-100">
                                                              <div class="widget-content-outer">
                                                                  <div class="widget-content-left fsize-1">
                                                                      <div class="text-muted opacity-6">Diskon Member Bulanan</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left pr-2 fsize-1">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-warning diskon_bulanan"></div>
                                                                          <small class="nilai_diskon_bulanan"></small>
                                                                      </div>
                                                                   
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-6 col-lg-6">
                                                      <div class="card-shadow-warning mt-3 mb-2 border widget-chart widget-chart2 text-left card">
                                                          <div class="widget-content p-0 w-100">
                                                              <div class="widget-content-outer">
                                                                  <div class="widget-content-left fsize-1">
                                                                      <div class="text-muted opacity-6">Total</div>
                                                                  </div>
                                                                  <div class="widget-content-wrapper">
                                                                      <div class="widget-content-left pr-2 fsize-1">
                                                                          <div class="widget-numbers mt-0 fsize-3 text-warning total_setelah_diskon">0</div>
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
                                              <input type="text" name="nohp" id="input_nohp" class="form-control" required value="">
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
                              <?php foreach (diskon() as $k => $v) { 
                                $potongan = $v['jenis_potongan'] == 'Persentase' ? $v['besar_diskon'].'%' : 'Rp. '.number_format($v['besar_diskon']); 
                                ?>
                                <option value="<?php echo $v['kategori'].'|'.$v['nama_diskon'].'|'.$v['id_diskon'].'|'.$v['jenis_potongan'].'|'.$v['besar_diskon'] ?>"><?php echo $v['kategori'].' | '.$v['nama_diskon'].' | Diskon : '.$potongan ?></option>
                              <?php } ?>
                             
                           </select>
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
    </div>
  </div>
    <div class="col-md-12 col-lg-12">
    
                            <button class="btn btn-block btn-info" type="button" onclick="konfirmasi();">Simpan</button>
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
        if (v.id_metode_pembayaran!=metode_pembayaran_1) {
          $('#metode_pembayaran_2').append(`<option value="`+v.id_metode_pembayaran+`">`+v.metode_pembayaran+`</option>`);
        }

      });
      // $('#form_tambah_metode_pembayaran').append(` `);




    // $('#input_dibayar_2').keyup(function(){
    //  var input_dibayar_1 = $('#input_dibayar').val();
    //  var input_dibayar_2 = $('#input_dibayar_2').val();
    //  var total_dibayar = parseInt(input_dibayar_1) + parseInt(input_dibayar_2);
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
    jadwal_pickle_tersetting();
    function jadwal_pickle_tersetting(){
      var diskon = parseInt(<?php echo $diskon ?>);
     $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/pickle_bulanan/jadwal_pickle_tersetting'),
              type    : 'POST',
              dataType : 'JSON',
              data    : {}, 
              success : function(data)
              {
                if (data.total_jam==0) {
                  $('.lama_main').html('Pilih jadwal main');

                }else{
                  $('.lama_main').html(data.total_jam + " Jam");

                }
                $('.total_harga').html(number_format(data.total_biaya));
                $('#simpan_biaya_pickle').val(data.total_biaya);
                $('.diskon_bulanan').html(diskon + '%');
                var nilai_diskon =  (data.total_biaya * (diskon / 100 ));
                var total_akhir = data.total_biaya - nilai_diskon
                $('.nilai_diskon_bulanan').html("Potongan harga "+number_format(nilai_diskon));
                $('.total_setelah_diskon').html(number_format(total_akhir));

                $('.total').html(number_format(total_akhir));

                $('.tagihan').html(number_format(total_akhir));
                $('#show_dibayar').html(number_format(total_akhir));
                $('#show_kembalian').html(number_format(0));
             
                $('#simpan_total').val(total_akhir);
                $('#diskon_bulanan').val('('+diskon+'%)');
                $('#besar_diskon_bulanan').val(diskon);
                $('#simpan_diskon_bulanan').val(nilai_diskon);
                $('#simpan_tagihan').val(total_akhir);
                $('#simpan_dibayar').val(total_akhir);
                $('#simpan_kembalian').val(0);
              },
              error : function (){
                alert('ee');
              }
      });




    }



    function konfirmasi(){
        var tgl_1_terpilih = $('#tgl_1_terpilih').val();
        var tgl_2_terpilih = $('#tgl_2_terpilih').val();
        var tgl_3_terpilih = $('#tgl_3_terpilih').val();
        var tgl_4_terpilih = $('#tgl_4_terpilih').val();

        var nama = $('#input_nama').val();
        var alamat = $('#input_alamat').val();
        var nohp = $('#input_nohp').val();
        var keterangan = $('#keterangan').val();

        if (tgl_1_terpilih=='') {
          Swal.fire('Error','Anda belum memilih jadwal 1','error');
        }
        else if (tgl_2_terpilih=='') {
          Swal.fire('Error','Anda belum memilih jadwal 2','error');
        }
        else if (tgl_3_terpilih=='') {
          Swal.fire('Error','Anda belum memilih Jadwal 3','error');
        }
        else if (tgl_4_terpilih=='') {
          Swal.fire('Error','Anda belum memilih Jadwal 4','error');
        }
        else if (nama=='') {
          Swal.fire('Error','Anda belum menginputkan nama','error');
        }
        // else if (alamat=='') {
        //   Swal.fire('Error','Anda belum menginputkan alamat','error');
        // }
        else if (nohp=='') {
          Swal.fire('Error','Anda belum menginputkan nohp','error');
        }else{
          $('#konfirmasi_pickle_bulanan').modal('show');
        }





    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_tgl_1').html(format_tanggal(tgl_1_terpilih));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_tgl_2').html(format_tanggal(tgl_2_terpilih));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_tgl_3').html(format_tanggal(tgl_3_terpilih));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_tgl_4').html(format_tanggal(tgl_4_terpilih));



        var jam_1_terpilih = $('#jam_1_terpilih').val();
        var jam_2_terpilih = $('#jam_2_terpilih').val();
        var jam_3_terpilih = $('#jam_3_terpilih').val();
        var jam_4_terpilih = $('#jam_4_terpilih').val();


    var pecah_jam_1 = jam_1_terpilih.split(",");
    var html_jam_1 = "<ol>";
    $.each(pecah_jam_1, function(k,v){
    html_jam_1 += "<li>"+ v +"</li>";
    });
    html_jam_1 += "<ol>";
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_jam_1').html(html_jam_1);


    var pecah_jam_2 = jam_2_terpilih.split(",");
    var html_jam_2 = "<ol>";
    $.each(pecah_jam_2, function(k,v){
    html_jam_2 += "<li>"+ v +"</li>";
    });
    html_jam_2 += "<ol>";
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_jam_2').html(html_jam_2);



    var pecah_jam_3 = jam_3_terpilih.split(",");
    var html_jam_3 = "<ol>";
    $.each(pecah_jam_3, function(k,v){
    html_jam_3 += "<li>"+ v +"</li>";
    });
    html_jam_3 += "<ol>";
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_jam_3').html(html_jam_3);



    var pecah_jam_4 = jam_4_terpilih.split(",");
    var html_jam_4 = "<ol>";
    $.each(pecah_jam_4, function(k,v){
    html_jam_4 += "<li>"+ v +"</li>";
    });
    html_jam_4 += "<ol>";
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_jam_4').html(html_jam_4);





        var biaya_1_terpilih = $('#biaya_1_terpilih').val();
        var biaya_2_terpilih = $('#biaya_2_terpilih').val();
        var biaya_3_terpilih = $('#biaya_3_terpilih').val();
        var biaya_4_terpilih = $('#biaya_4_terpilih').val();

    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_biaya_1').html(number_format(biaya_1_terpilih));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_biaya_2').html(number_format(biaya_2_terpilih));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_biaya_3').html(number_format(biaya_3_terpilih));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_biaya_4').html(number_format(biaya_4_terpilih));


        var status_1_terpilih = $('#status_1_terpilih').val();
        var status_2_terpilih = $('#status_2_terpilih').val();
        var status_3_terpilih = $('#status_3_terpilih').val();
        var status_4_terpilih = $('#status_4_terpilih').val();

    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_status_1').html(status_1_terpilih);
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_status_2').html(status_2_terpilih);
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_status_3').html(status_3_terpilih);
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_status_4').html(status_4_terpilih);


        var simpan_total = $('#simpan_total').val();
        var simpan_biaya_pickle = $('#simpan_biaya_pickle').val();
        var diskon_bulanan = $('#diskon_bulanan').val();
        var simpan_diskon_bulanan = $('#simpan_diskon_bulanan').val();



    $('#konfirmasi_pickle_bulanan').find('#biaya_pickle').html(number_format(simpan_biaya_pickle));
    $('#konfirmasi_pickle_bulanan').find('#diskon_bulanan_biaya_pickle').html(diskon_bulanan);
    $('#konfirmasi_pickle_bulanan').find('#nilai_diskon_bulanan_biaya_pickle').html(number_format(simpan_diskon_bulanan));
    $('#konfirmasi_pickle_bulanan').find('#total_biaya_pickle').html(number_format(simpan_total));




    var jenis_potongan = $('#jenis_potongan').val() ;
    var kategori_diskon = $('#kategori_diskon').val() ;
    var nama_diskon = $('#nama_diskon').val() ;
    var besar_diskon =$('#besar_potongan').val();
    var biaya_paket = parseInt($('#harga_paket').val()) ;

    var simpan_total = $('#simpan_total').val();

    var simpan_tagihan = $('#simpan_tagihan').val();
    var simpan_dibayar = $('#simpan_dibayar').val();
    var simpan_kembalian = $('#simpan_kembalian').val();
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







    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_biaya').html(number_format(simpan_total));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_potongan').html(diskon);
    // $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_student_card').html(formdata[25].value);
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_tagihan').html(number_format(simpan_tagihan));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_dibayar').html(number_format(simpan_dibayar));
    $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_kembalian').html(number_format(simpan_kembalian));





        $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_tgl_main').html(tgl_main + '<br>Lapangan '+ lapangan);
        $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_jam_main').html(`<ol>`);
        $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_nama').html(nama);
        $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_alamat').html(alamat);
        $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_nohp').html(nohp);
        $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_keterangan').html(keterangan);
        $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_status').html(status);
      $('#jadwal_pickle_bulanan').find('input[type=checkbox]').each(function () {
           if (this.checked) {
            var value = $(this).val();
           let pecah_val = value.split("|");

              $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_jam_main').append(`<li>`+pecah_val[0]+` - Rp. `+number_format(pecah_val[1])+`</li>`);
           }
});
        $('#konfirmasi_pickle_bulanan').find('#konfirmasi_pickle_bulanan_jam_main').append(`</ol>`);


    }

    function pilih_jadwal(ke){
      var lapangan = '<?php echo json_encode($lapangan) ?>';
      var json_lapangan = JSON.parse(lapangan);
      $('#jadwal_pickle_bulanan').find('#lapangan').html('');
      $.each(json_lapangan, function (k,v){

      $('#jadwal_pickle_bulanan').find('#lapangan').append('<option value="'+v.lapangan+'">Lapangan '+v.lapangan+'</option>');

      })
      $('#jadwal_pickle_bulanan').find('#tgl_main').val('').change();
      $('#jadwal_pickle_bulanan').find('#simpan_lama_main').val(0)
      $('#jadwal_pickle_bulanan').find('#total_biaya').val(0)

        $('#jadwal_pickle_bulanan').find('.biaya_total').html(number_format(0));
        $('#jadwal_pickle_bulanan').find('.biaya_perjam').html(number_format(0));

        $('#jadwal_pickle_bulanan').find('.lama_main').html('Pilih Jam Main');
        $('#jadwal_pickle_bulanan').find('#jam_main_dipilih').html("Pilih jam main");

      $('#jadwal_pickle_bulanan').modal('show');
       if ($('#jadwal_pickle_bulanan').find('#tgl_main').val()=='') {
          $('#jadwal_pickle_bulanan').find('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Pilih tanggal dulu untuk menampilkan jam tersedia</div>');
      }else{
        tampilkan_jadwal_main();
      }

      
      // $('#jadwal_pickle_bulanan').find('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Pilih tanggal dulu untuk menampilkan jam tersedia</div>');
      $('#jadwal_pickle_bulanan').find('#jadwal_pickle_bulanan_ke').html(ke);
      $('#jadwal_pickle_bulanan').find('#jadwal_ke').val(ke);
      $('#jadwal_pickle_bulanan').find('#tgl_main').val('').change();



    $('#jadwal_pickle_bulanan').find('#tgl_main').change(function(){
      var tgls = '<?php echo date('Y-m-d') ?>';
      var tgl_dipilih = $('#jadwal_pickle_bulanan').find('#tgl_main').val();
      if (tgl_dipilih<tgls) {
        if (tgl_dipilih!='') {
          Swal.fire('Error','Harap pilih tanggal diatas tanggal hari ini','error');
        }
          $('#jadwal_pickle_bulanan').find('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Pilih tanggal dulu untuk menampilkan jam tersedia</div>');
      }else{
        tampilkan_jadwal_main();

      }
      // hitung_jam(this);
    });
    $('#jadwal_pickle_bulanan').find('#lapangan').change(function(){
      if ($('#jadwal_pickle_bulanan').find('#tgl_main').val()=='') {
          $('#jadwal_pickle_bulanan').find('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Pilih tanggal dulu untuk menampilkan jam tersedia</div>');
      }else{
        tampilkan_jadwal_main();
      }
    });



    }






  function dt_member(){

    $('#data_member_futsal_badminton').modal('show');
    $('#data_member_futsal_badminton').find('#jenis_fasilitas').html('pickle');
    $('#list_member_pickle_badminton').DataTable(
    {
          processing  : true,
          serverSide  : true,
          bDestroy  : true,
          responsive  : true,
          ajax    : {
                   url: '<?php echo base_url() ?>user/gro/transaction/pickle/dt_member',
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

                $('#tombol_simpanedit_member').show();
                $('#tombol_action_identitas_member').attr('onclick','member_baru()');
                $('#tombol_action_identitas_member').html('Member baru');
  }

  function member_baru(){

        $('#kategori_order').val('New');
        $('#tombol_simpanedit_member').hide();
        $('#input_nama').val('');
        $('#input_alamat').val('');
        $('#input_nohp').val('');
        $('#id_pengunjung').val('');
    $('#tombol_action_identitas_member').attr('onclick','dt_member()');
    $('#tombol_action_identitas_member').html('Perpanjangan Member');
  }







  function set_jadwal_pickle(){

        var tgl_main = $('#jadwal_pickle_bulanan').find('#tgl_main').val();
        var input_jam_main = $('#jadwal_pickle_bulanan').find('#jam_main').val();
        var lapangan = $('#jadwal_pickle_bulanan').find('#lapangan').val();
        var jadwal_ke = $('#jadwal_pickle_bulanan').find('#jadwal_ke').val();
        var lama_main = $('#jadwal_pickle_bulanan').find('#simpan_lama_main').val();
        var biaya = $('#jadwal_pickle_bulanan').find('#total_biaya').val();
        var status = $('#jadwal_pickle_bulanan').find('#status').val();

               $('#jam_'+jadwal_ke+'_terpilih').val('');
        if (tgl_main=='') {
          Swal.fire('Error','Anda belum memilih tanggal main','error');
        }
        else if (lama_main==0) {
          Swal.fire('Error','Anda belum memilih jam main','error');
        }
        else{


      var kumpul_jadwal = [];
      $('#jadwal_pickle_bulanan').find('input[type=checkbox]').each(function () {
           if (this.checked) {
            var value = $(this).val();
           let pecah_val = value.split("|");
           var jam_main = pecah_val[0]
            kumpul_jadwal.push(jam_main); 



              // $('#konfirmasi_pickle').find('#konfirmasi_pickle_jam_main').append(`<li>`+pecah_val[0]+` - Rp. `+number_format(pecah_val[1])+`</li>`);
           }
});
               $('#jadwal_'+jadwal_ke).html("Jadwal "+ jadwal_ke +'<br>'+format_tanggal(tgl_main) +'<br>'+ kumpul_jadwal );
               // $('#jadwal_'+jadwal_ke).html("<div class='badge badge-info'>" +format_tanggal(tgl_main) +'<br>'+ kumpul_jadwal + "</div>");
               // $('#jadwal_'+jadwal_ke).html("<div class='badge badge-info'>" +format_tanggal(tgl_main) +'<br>'+ kumpul_jadwal + "</div>");
               $('#tgl_'+jadwal_ke+'_terpilih').val(tgl_main);
               $('#jam_'+jadwal_ke+'_terpilih').val(kumpul_jadwal);
               $('#biaya_'+jadwal_ke+'_terpilih').val(biaya);
               $('#status_'+jadwal_ke+'_terpilih').val(status);
               $('#jumlah_jam_'+jadwal_ke+'_terpilih').val(lama_main);



    var formdata = $('#jadwal_pickle_bulanan').find('#form_pilihan_jadwal_pickle').serialize();

     $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/pickle_bulanan/set_jadwal_main'),
              type    : 'POST',
              dataType : 'JSON',
              data    : formdata, 
              success : function(data)
              {
              jadwal_pickle_tersetting();
              if (data.response=='200') {
                toastr.success('Jadwal pickle Disimpan');

              }else{
                toastr.success('Error');
                  // Swal.fire('Error','Data tidak ditemukan','error');
                  setTimeout(close_swal, 3000);

              }
              },
              error : function (){
                alert('ee');
              }
      });





      $('#jadwal_pickle_bulanan').modal('hide');

        }



}


    

    function tampilkan_jadwal_main(){

      var tgl_main = $('#jadwal_pickle_bulanan').find('#tgl_main').val();
      var lapangan = $('#jadwal_pickle_bulanan').find('#lapangan').val();


        $('#jadwal_pickle_bulanan').find('.lama_main').html('Pilih Jam Main');
        $('.biaya_total').html(0);
        $('#total_biaya').val(0);


        $('#potongan_harga').val('').change();

        $('#jadwal_pickle_bulanan').find('#jam_main_dipilih').html("Pilih jam main");
        $('#jadwal_pickle_bulanan').find('.biaya_total').html(number_format(0));
        $('#jadwal_pickle_bulanan').find('.biaya_perjam').html(number_format(0));

         if (tgl_main=='') {
      
          $('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Pilih tanggal dulu untuk menampilkan jam tersedia</div>')
    }else{



     $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/pickle_bulanan/jadwal_tersedia'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
                tgl_main : tgl_main,
                lapangan : lapangan,
              },
              success : function(data)
              {
                  $('#jadwal_pickle_bulanan').find('#pilihan_jam').html(``);
                $.each(data, function (k,v){
                  $('#jadwal_pickle_bulanan').find('#pilihan_jam').append(`<div class="col-md-4 show_jam_`+k+`">`);
                  $.each(v, function(k_jam, v_jam){
                     if (v_jam.tersedia=='0') {
                        $('.show_jam_' +k ).append('<i class="fa fa-times"></i><span style="color:red; margin-left:10px; text-decoration: line-through double;">'+v_jam.jam+'</span><br>');
                      }else{
                        $('.show_jam_' +k ).append('<input type="checkbox" name="jam_main[]" class="checkbok_jam" value="'+v_jam.jam+'|'+v_jam.harga+'" style="margin-right:5px" onclick="hitung_jam(this)" biaya="'+v_jam.harga+'" jam_main="'+v_jam.jam+'"><span style="color:green;">'+v_jam.jam+'</span><br>');
                      }

                  })

                  $('#jadwal_pickle_bulanan').find('#pilihan_jam').append(`</div>`);
                });


              },
              error : function (){

              }
      });

   }
}
    var kumpul_jam = [];
    function hitung_jam(x){

        $('#potongan_harga').val('').change();
      jumlah_jam = $('#jadwal_pickle_bulanan').find('.checkbok_jam').filter(':checked').length;
      var biaya = $(x).attr('biaya');
      var jam_main = $(x).attr('jam_main');
      var biaya_sebelumnya = $('#total_biaya').val();
      var biaya_setelahnya = parseInt(biaya_sebelumnya) + parseInt(biaya);

      var value = $(x).val();
   

      if (jumlah_jam==0) {
        $('#jadwal_pickle_bulanan').find('.lama_main').html('Pilih Jam Main');
        $('.biaya_total').html(0);
        $('#total_biaya').val(0);

     

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
    

        $('#jadwal_pickle_bulanan').find('.lama_main').html(jumlah_jam + " Jam");
        $('#jadwal_pickle_bulanan').find('#simpan_lama_main').val(jumlah_jam);
        $('#jadwal_pickle_bulanan').find('#total_biaya').val(biaya_setelahnya);

        // $('.tagihan').html(number_format(biaya_setelahnya));
        // $('#show_dibayar').html(number_format(biaya_setelahnya));
        function checked_jam_main() {
          return jam_main;
        }
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
                        $('#show_kembalian').html("Rp. "+number_format(sisa));
                        $('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                        $('#simpan_dibayar').val(dibayar_pelanggan);
                        $('#simpan_kembalian').val(sisa);

                        if (sisa<0) {
                          $('#tombol_tambah_metode_pembayaran').show();
                        }else{
                          $('#tombol_tambah_metode_pembayaran').hide();

                        }
                    }

                });





    }





  $('#potongan_harga').change(function(){
    $('#input_dibayar').val('');
    var kategori = $('#potongan_harga').val();
    var total = parseInt($('#simpan_total').val());
    
    if (kategori=='') {

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


    }
  });






  function simpanedit_identitas(){

    var formdata = $('#form_pickle').serialize();
     $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/pickle/simpanedit_identitas'),
              type    : 'POST',
              data    : formdata,
              dataType : 'JSON',
              success : function(data)
              {
                  Swal.fire('Sukses',data.pesan,'success');
              },
              error : function (){
                alert('ee');
              }
      });

  }



  function simpan_transaksi_pickle_bulanan(){

    var formdata = $('#form_pickle').serialize();
     $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/pickle_bulanan/simpan_transaksi_pickle_bulanan'),
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
<div class="alert alert-info">Transaksi pickle - Member Bulanan Disimpan <br>
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
     $('#konfirmasi_pickle_bulanan').modal('hide');
    $('#print_transaksi_gym').modal('show');
    $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
      <iframe src="`+ baseUrl('/user/gro/transaction/pickle_bulanan/print_transaksi_pickle_bulanan/') + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
      `);
  }

  </script>
