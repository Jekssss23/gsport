<div class="mb-3 card">
                                        <div class="card-header-tab card-header">
                                            <div class="card-header-title">
                                                Registrasi Gym Online
                                            </div>
                                            <ul class="nav">
                                                <li class="nav-item"><a data-toggle="tab" href="#cek_kode_unik" class="nav-link show active">Cari data by Kode</a></li>
                                                <li class="nav-item"><a data-toggle="tab" href="#list_registrasi_online" class="nav-link show">List Registrasi Online</a></li>
                                            </ul>
                                        </div>
                                        <div class="card-body">
                                          <?php echo $this->session->flashdata('pesan') ?>
                                            <div class="tab-content">
                                                <div class="tab-pane show active" id="cek_kode_unik" role="tabpanel">
                                                   <div class="row">
                                                                              <div class="col-md-12">
                                                                                  <div class="main-card mb-3 card">
                                                                                      <div class="card-header">Cek Data Member
                                                                                      </div>
                                                                                      <div class="card-body">
                                                                                          <div class="form-group">
                                                                                            <input type="text" name="" class="form-control cek_data_member" id="" autofocus placeholder="Masukan kode Registrasi / Scan QR code">
                                                                                          </div>
                                                                                          <div class="form-group">
                                                                                            <button type="button" class="btn btn-block btn-info" onclick="cari_data()">Cari Data</button>
                                                                                          </div>
                                                                                      </div>
                                                                                  </div>
                                                                              </div>
                                                                              <div class="col-md-12">
                                                                                  <div class="main-card mb-3 card">
                                                                                      <div class="card-header">Profile Member
                                                                                      </div>
                                                                                      <div class="card-body">
                                                                                        <div  id="input_alert">
                                                                                          <div class="alert alert-info">
                                                                                            Masukan kode Registrasi / Scan QR code <br>untuk melakukan pengecekan data
                                                                                          </div>
                                                                                        </div>
                                                                                          <div class="row"  id="preview"  style="display:none" >
                                                                                          <div class="col-md-7">
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
                                                                                          <div class="col-md-5">
                                                                                            <div id="form_tagihan" style="display:none">
                                                                                            <ul class="list-group list-group-flush">
                                                                                              <li class="active list-group-item">
                                                                                                <div class="widget-content p-0">
                                                                                                    <div class="widget-content-wrapper">
                                                                                                        <div class="widget-content-left">
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
                                                  









                                                  </div>
                                                <div class="tab-pane show" id="list_registrasi_online" role="tabpanel">
                                                  <table class="table" id="list_data_member_reg" width="100%">
                                                     <thead>
                                                       <tr>
                                                            <td width="20px">No</td>
                                                            <td>Kode Member</td>
                                                            <td>Nama</td>
                                                            <td>Tempat / Tgl Lahir</td>
                                                            <td>Jenis kelamin</td>
                                                            <td>Alamat</td>
                                                            <td>No HP</td>
                                                            <td>Fasilitas </td>
                                                            <td>Tgl Register </td>
                                                          
                                                            <td width="90px">Option</td>
                                                          </tr>
                                                     </thead>
                                                  </table>
                                                  <a href="javascript:void(0)" class="btn btn-info btn-sm" onclick="bersihkan_data()">Bersihkan</a>
                                                  </div>
                                              
                                            </div>
                                        </div>
                                     
                                    </div>







  <script src="<?php echo base_url(); ?>assets/jquery_number/jquery.number.js"></script>

  <script type="text/javascript">
showAutoCurrency();
    
  function showAutoCurrency(){
    $('input.currency').number( true, 0 );
  }

    $('#modal_pembayaran_member_online').find('#metode_pembayaran').change(function(){
      var opsi_tambah_metode_pembayaran = $('#opsi_tambah_metode_pembayaran').val();
      if (opsi_tambah_metode_pembayaran=='1') {
        tambah_metode_pembayaran();
      }
    });
    function tambah_metode_pembayaran(){
      $('#modal_pembayaran_member_online').find('#opsi_tambah_metode_pembayaran').val(1);
      var metode_pembayaran_1 = $('#modal_pembayaran_member_online').find('#metode_pembayaran').val();
      var metode_pembayaran = '<?php echo json_encode(metode_pembayaran()) ?>';
      var parse_json = JSON.parse(metode_pembayaran);
      $('#modal_pembayaran_member_online').find('#form_tambah_metode_pembayaran').html(`<div class="col-sm-6 col-md-6 col-xl-6">
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

      $('#modal_pembayaran_member_online').find('#metode_pembayaran_2').html(``);
      $.each(parse_json, function(k,v){
        if (v.id_metode_pembayaran!=metode_pembayaran_1) {
          $('#modal_pembayaran_member_online').find('#metode_pembayaran_2').append(`<option value="`+v.id_metode_pembayaran+`">`+v.metode_pembayaran+`</option>`);
        }

      });
      // $('#modal_pembayaran_member_online').find('#form_tambah_metode_pembayaran').append(` `);




    // $('#modal_pembayaran_member_online').find('#input_dibayar_2').keyup(function(){
    //  var input_dibayar_1 = $('#modal_pembayaran_member_online').find('#input_dibayar').val();
    //  var input_dibayar_2 = $('#modal_pembayaran_member_online').find('#input_dibayar_2').val();
    //  var total_dibayar = parseInt(input_dibayar_1) + parseInt(input_dibayar_2);
    //  $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(total_dibayar);

    // });


                $('#modal_pembayaran_member_online').find('#input_dibayar_2').keyup(function(){
                  var input_dibayar_1 = $('#modal_pembayaran_member_online').find('#input_dibayar').val();
      var input_dibayar_2 = $('#modal_pembayaran_member_online').find('#input_dibayar_2').val();
      var dibayar_pelanggan = parseInt(input_dibayar_1) + parseInt(input_dibayar_2);
      $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(dibayar_pelanggan);

                  var tagihan = $('#modal_pembayaran_member_online').find('#simpan_tagihan').val();
                  if (input_dibayar_1=='') {
                    // Swal.fire('Warning','Pembayaran pada metode pembayaran pertama harus di input dulu','error');
                          var sisa = input_dibayar_2 - tagihan;
                          $('#modal_pembayaran_member_online').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#modal_pembayaran_member_online').find('#show_dibayar').html("Rp. "+number_format(input_dibayar_2));
                  }else{
                    if (input_dibayar_2=='') {
                          var sisa = input_dibayar_1 - tagihan;
                          $('#modal_pembayaran_member_online').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#modal_pembayaran_member_online').find('#show_dibayar').html("Rp. "+number_format(input_dibayar_1));

                    }else{

                     if (dibayar_pelanggan >0) {

                          var sisa = dibayar_pelanggan - tagihan;
                          $('#modal_pembayaran_member_online').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#modal_pembayaran_member_online').find('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                          $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(dibayar_pelanggan);
                          $('#modal_pembayaran_member_online').find('#simpan_kembalian').val(sisa);


                             if (sisa<0) {

                                $('#modal_pembayaran_member_online').find('#simpan_member_reg_online').attr('onclick','Swal.fire("Error","Mohon masukan nilai pembayaran dengan benar","error")');
                                $('#modal_pembayaran_member_online').find('#simpan_member_reg_online').attr('class','btn btn-block btn-danger mt-3');

                                $('#modal_pembayaran_member_online').find('#tombol_tambah_metode_pembayaran').show();
                              }else{
                                $('#modal_pembayaran_member_online').find('#simpan_member_reg_online').attr('onclick','simpan_transaksi_online()');
                                $('#modal_pembayaran_member_online').find('#simpan_member_reg_online').attr('class','btn btn-block btn-success mt-3');
                                $('#modal_pembayaran_member_online').find('#tombol_tambah_metode_pembayaran').hide();

                              }
                     
                      }else{
                         $('#modal_pembayaran_member_online').find('#show_dibayar').html("Rp. "+ number_format(tagihan));
                        $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(tagihan);
                        $('#modal_pembayaran_member_online').find('#simpan_kembalian').val(0);
                        $('#modal_pembayaran_member_online').find('#show_kembalian').html('Rp. 0');
                      }

                    }
                } 





                });








    }
    function batal_tambah_metode_pembayaran(){
      
      $('#modal_pembayaran_member_online').find('#opsi_tambah_metode_pembayaran').val(0);
      $('#modal_pembayaran_member_online').find('#form_tambah_metode_pembayaran').html(`

                        <div class="col-sm-12 col-md-12 col-xl-12">
                          <div class="form-group">
                           <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="tambah_metode_pembayaran()" id="tombol_tambah_metode_pembayaran">Tambah Metode Pembayaran</button>
                         </div>
                      </div>`);

      $('#modal_pembayaran_member_online').find('#metode_pembayaran_2').html(``);
      
  var input_dibayar_1 = $('#modal_pembayaran_member_online').find('#input_dibayar').val();
      var dibayar_pelanggan = parseInt(input_dibayar_1);

                  var tagihan = $('#modal_pembayaran_member_online').find('#simpan_tagihan').val();

                   if (dibayar_pelanggan >0) {

                        var sisa = dibayar_pelanggan - tagihan;
                        $('#modal_pembayaran_member_online').find('#show_kembalian').html("Rp. "+number_format(sisa));
                        $('#modal_pembayaran_member_online').find('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                        $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(dibayar_pelanggan);
                        $('#modal_pembayaran_member_online').find('#simpan_kembalian').val(sisa);


                   
                    }else{
                       $('#modal_pembayaran_member_online').find('#show_dibayar').html("Rp. "+ number_format(tagihan));
                      $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(tagihan);
                      $('#modal_pembayaran_member_online').find('#simpan_kembalian').val(0);
                      $('#modal_pembayaran_member_online').find('#show_kembalian').html('Rp. 0');
                    }



    }
  </script>

<script type="text/javascript">
  
      $( ".cek_data_member" ).trigger( "focus" );
    function cari_data(){
    cek_id_member($('.cek_data_member').val());
    $( ".cek_data_member" ).val( "" );

      $( ".cek_data_member" ).trigger( "focus" );
     }








    $('.cek_data_member').keypress(function(e){
    if(e.which == 13){
      cek_id_member($('.cek_data_member').val());
      $( ".cek_data_member" ).trigger( "focus" );

      $( ".cek_data_member" ).val( "" );
    }

  });





    function cek_id_member(kode){

       $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/cek_id_member',
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                kode : kode,
                
              },
              success : function(data)
              {
                if (data.found==0) {
                  Swal.fire('Error','Data tidak ditemukan','error');
                  setTimeout(close_swal, 3000);
                  audio_tidak_ditemukan();
                }else{
                  toastr.success('Data ditemukan'); 
                  // toastr.success('Click Button', 'ButtonClick', 'positionclass:toast-bottom-full-width');
                  preview_member(data.id_member);
                  audio_ditemukan();
                  // setTimeout(close_preview_member, 5000);


                }
              }
    });
     }


    function close_preview_member(){
      $( ".cek_data_member" ).trigger( "focus" );
     }
    function close_swal(){

      $( ".cek_data_member" ).trigger( "focus" );
      $( ".cek_data_member" ).val( "" );
     swal.close();
     }


     function pembayaran_online(){
      $('#modal_pembayaran_member_online').modal('show');
$('#modal_pembayaran_member_online').find('#potongan_harga').val('').change();
  $('#prewiew_member_online').modal('hide');
      $('#modal_pembayaran_member_online').find('#input_dibayar').val('');



        var id_member = $('#preview').find('#simpan_id_member').val();
        var id_jenis_member = $('#preview').find('#simpan_id_jenis_member').val();
        var tgl_akhir_masa_aktif = $('#preview').find('#simpan_akhir_masa_aktif').val();
        var tgl_awal_masa_aktif = $('#preview').find('#simpan_awal_masa_aktif').val();
        var tgl_register = $('#preview').find('#simpan_tgl_register').val();
        var jam_register = $('#preview').find('#simpan_jam_register').val();
        var total = $('#modal_pembayaran_member_online').find('#simpan_tagihan').val();
        var fasilitas_member = $('#prewiew_member_online').find('#simpan_fasilitas').val();

        $('#modal_pembayaran_member_online').find('#fasilitas').val(fasilitas_member);


      $('#modal_pembayaran_member_online').find('#form_tambah_metode_pembayaran').html(``);



                var harus_dibayar = parseInt(total);

                $('#modal_pembayaran_member_online').find('#show_kembalian').html("Rp. 0");

                $('#modal_pembayaran_member_online').find('#total').html("Rp. "+number_format(harus_dibayar));
                $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(harus_dibayar);
                $('#modal_pembayaran_member_online').find('#simpan_kembalian_pembayaran').val(0);

                $('#modal_pembayaran_member_online').find('#simpan_total').val(total);


                $('#modal_pembayaran_member_online').find('#input_dibayar').keyup(function(){
                   $('#modal_pembayaran_member_online').find('#form_tambah_metode_pembayaran').html(`

                        <div class="col-sm-12 col-md-12 col-xl-12">
                          <div class="form-group">
                           <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="tambah_metode_pembayaran()" id="tombol_tambah_metode_pembayaran">Tambah Metode Pembayaran</button>
                         </div>
                      </div>`);

                   var total = $('#modal_pembayaran_member_online').find('#simpan_tagihan').val();
                    var harus_dibayar = parseInt(total);


        






                  var dibayar_pelanggan = $('#modal_pembayaran_member_online').find('#input_dibayar').val();

                   if (dibayar_pelanggan =='') {
                    $('#modal_pembayaran_member_online').find('#show_dibayar').html("Rp. "+ number_format(harus_dibayar));
                    $('#modal_pembayaran_member_online').find('#show_kembalian').html('Rp. 0');
                    $('#modal_pembayaran_member_online').find('#simpan_kembalian_pembayaran').val(0);
                    $('#modal_pembayaran_member_online').find('#kembalian_pembayaran').val(0);
                    $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(harus_dibayar);
                        var sisa = 0;

                          $('#modal_pembayaran_member_online').find('#opsi_tambah_metode_pembayaran').val(0);
                          batal_tambah_metode_pembayaran();
                          $('#modal_pembayaran_member_online').find('#tombol_tambah_metode_pembayaran').hide();
                    }else{

                        var sisa = dibayar_pelanggan - harus_dibayar;
                        $('#modal_pembayaran_member_online').find('#show_kembalian').html("Rp. "+number_format(sisa));
                        $('#modal_pembayaran_member_online').find('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                        $('#modal_pembayaran_member_online').find('#simpan_kembalian_pembayaran').val(sisa);
                        $('#modal_pembayaran_member_online').find('#simpan_kembalian').val(sisa);
                        $('#modal_pembayaran_member_online').find('#simpan_dibayar').val(dibayar_pelanggan);
                      

                    }

                $('#modal_pembayaran_member_online').find('#input_kembalian').val(sisa);
                if (sisa<0) {

                  $('#modal_pembayaran_member_online').find('#simpan_member_reg_online').attr('onclick','Swal.fire("Error","Mohon masukan nilai pembayaran dengan benar","error")');
                  $('#modal_pembayaran_member_online').find('#simpan_member_reg_online').attr('class','btn btn-block btn-danger mt-3');

                  $('#modal_pembayaran_member_online').find('#tombol_tambah_metode_pembayaran').show();
                }else{
                  $('#modal_pembayaran_member_online').find('#simpan_member_reg_online').attr('onclick','simpan_transaksi_online()');
                  $('#modal_pembayaran_member_online').find('#simpan_member_reg_online').attr('class','btn btn-block btn-success mt-3');
                  $('#modal_pembayaran_member_online').find('#tombol_tambah_metode_pembayaran').hide();

                }


                });









     }


function simpan_transaksi_online(){
 var formdata = $('#modal_pembayaran_member_online').find('#form_konfirmasi_reg_online').serialize();
 var id_member = $('#modal_pembayaran_member_online').find('.id_member').val();
  $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/submit_aktifkan_member',
              dataType: 'JSON',
              type    : 'POST',
              data    :  formdata,
              success : function(data)
              {
                if (data.responcode==200) {

                  toastr.success('Data pembayaran disimpan'); 
                  $('#modal_pembayaran_member_online').modal('hide');
                  $('#list_data_member_reg').DataTable().ajax.reload();
                  print_transaksi(data.id_transaksi, data.fasilitas);

                }else{
                  Swal.fire('Error','Terjadi kesalahan','error');
                }
                
              }, 
              error : function(){
              }
            });

}


  function print_transaksi(id_transaksi, fasilitas){
     $('#konfirmasi_gym').modal('hide');
    $('#print_transaksi_gym').modal('show');
    
        if (fasilitas=='Gym') {
            $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
              <iframe src="`+ baseUrl('/user/gro/transaction/gym/print_transaksi_gym/') + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
              `);
            
        }
        else if (fasilitas=='Swimming - Membership') {
              $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
                <iframe src="`+ baseUrl('/user/gro/transaction/swimming_membership/print_transaksi_swimming_membership/') + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
      `);
        }
        else if (fasilitas=='Swimming - Spesial Membership') {
              $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
                <iframe src="`+ baseUrl('/user/gro/transaction/swimming_spesial/print_transaksi_swimming_spesial/') + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
      `);
        }else{
              $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
                <iframe src="`+ baseUrl('/user/gro/transaction/gym_spesial/print_transaksi_gym/') + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
      `);

        }
  }
function preview_member(id_member){
  $('#input_alert').hide();
  $('#preview').show();
  // $('#form_pembayaran').hide();

     $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/detail_member',
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_member : id_member,
                
              },
              success : function(data)
              {
                // alert('ditemukan');
                if (data.member.id_jenis_member==1) {
                  var total = parseInt(data.biaya) ; 
                $('#preview').find('#biaya_regis').html(number_format(0));
                $('.biaya_regis_reg_online').val(0);

                }else{
                  var total = parseInt(data.biaya) + 100000 ; 
                $('#preview').find('#biaya_regis').html(number_format(100000));
                $('.biaya_regis_reg_online').val(100000);

                }
                $('.nama').html(data.member.nama);
                $('#preview').find('#identitas').html(data.member.jenis_identitas + ' - '+data.member.no_identitas);
                $('#preview').find('#jk').html(data.member.jk);
                $('#preview').find('#fasilitas').html('Merdaftar pada fasilitas : <br>'+data.member.fasilitas);
                $('#preview').find('#alamat').html(data.member.alamat);
                $('#preview').find('#nohp').html(data.member.no_hp);
                $('#preview').find('#email').html(data.member.email);
                $('#preview').find('#pekerjaan').html(data.member.pekerjaan);
                $('#preview').find('#ttl').html(data.member.tmpl+', '+data.member.tgll + '<br>' + data.usia +' Tahun ');
                $('#preview').find('#ig').html(data.member.ig);
                $('#preview').find('#paket').html(data.paket);
                $('#preview').find('#biaya_paket').html(number_format(data.biaya));
                $('#preview').find('#foto_member').attr('src', '<?php echo base_url() ?>file/member/' + data.member.foto);


                $('.harus_dibayar_reg_online').val(total);
                $('.paket_member').val(data.paket);
                $('.biaya_member_reg_online').val(data.biaya);
                $('.akhir_masa_aktif').val(data.tgl_akhir_masa_aktif);
                $('.id_member').val(data.member.id_member);
                $('.id_jenis_member').val(data.member.id_jenis_member);
                $('.total_reg_online').html(number_format(total));
                $('#preview').find('.kode_regis').html(data.member.kode_unik_member);
                $('#preview').find('.waktu_regis').html(format_tanggal(data.member.tgl_register) +' '+data.member.jam_register);
                $('#preview').find('#masa_aktif').html(data.masa_aktif);
                $('#preview').find('#status').html(data.member.status);
                $('#preview').find('#tgl_masa_aktif').html("Status : "+data.member.status+"<br>Aktif Sampai : "+format_tanggal(data.tgl_akhir_masa_aktif));

                $('#modal_pembayaran_member_online').find('#show_dibayar').html('Rp. '+number_format(total));
                $('#preview').find('#show_kembalian').html('Rp. '+number_format(0));
                if (data.member.status=='Aktif') {
                    // $('#preview').find('#keanggotaan_paket').html(data.keanggotaan_member.jenis_member);
                    // $('#preview').find('#keanggotaan_masa_aktif').html(data.keanggotaan_member.masa_aktif);
                    // $('#preview').find('#keanggotaan_tgl_berakhir_aktif').html("Aktif sampai <br>" +format_tanggal(data.keanggotaan_member.akhir_masa_aktif));
                    $('#preview').find('#form_keanggotaan').show();
                    $('#preview').find('#form_tagihan').hide();
                    $('#preview').find('#list_keanggontaan').html('');
                    $.each(data.keanggotaan_member, function(k,v){
                      $('#preview').find('#list_keanggontaan').append(`

                                                <li class="list-group-item">
                                                    <div class="widget-content p-0">
                                                        <div class="widget-content-wrapper">
                                                            <div class="widget-content-left">
                                                                <div class="widget-heading">`+v.kelompok_fasilitas+`<br>`+v.fasilitas+`</div>
                                                                <div class="widget-subheading"> Paket : `+v.jenis_member+`</div>
                                                                <!-- <a href="javascript:void(0)" onclick="Swal.fire('Info','Pada bagian ini ketika di klik akan menampilkan modal xl yang berisikan informasi List keanggotaan, dan absensi member terkait')">Lihat Detail Member Gym</a> -->
                                                            </div>
                                                            <div class="widget-content-right">
                                                                Aktif Sampai <br>`+format_tanggal(v.akhir_masa_aktif)+`
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                `);

                    });
                    
                }else{
                    $('#preview').find('#form_tagihan').show();
                    $('#preview').find('#form_keanggotaan').hide();

                }



              



              }, 
              error : function(){
                alert('e');
              }
            });
}




function preview_member_gym(id_member){
  // $('#input_alert').hide();
  $('#prewiew_member_online').modal('show');
  // $('#form_pembayaran').hide();

     $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/detail_member_gym',
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_member : id_member,
                
              },
              success : function(data)
              {
                // alert('ditemukan');
                if (data.member.id_jenis_member=='1') {
                  var total = parseInt(data.biaya) ; 
                $('#prewiew_member_online').find('#biaya_regis').html(number_format(0));
                $('.biaya_regis_reg_online').val(0);
                $('#prewiew_member_online').find('#identitas_member').hide();
                $('#prewiew_member_online').find('#tagihan_member').attr('class','col-md-12');
                $('#prewiew_member_online').find('#nama_member').html(data.member.nama);

                }else{
                $('#prewiew_member_online').find('#nama_member').html('');
                $('#prewiew_member_online').find('#tagihan_member').attr('class','col-md-5');
                $('#prewiew_member_online').find('#identitas_member').show();
                  var total = parseInt(data.biaya) + 100000 ; 
                $('#prewiew_member_online').find('#biaya_regis').html(number_format(100000));
                $('.biaya_regis_reg_online').val(100000);

                }


                $('.nama').html(data.member.nama);
                $('#prewiew_member_online').find('#identitas').html(data.member.jenis_identitas + ' - '+data.member.no_identitas);
                $('#prewiew_member_online').find('#jk').html(data.member.jk);
                $('#prewiew_member_online').find('#fasilitas').html('Merdaftar pada fasilitas : <br>'+data.member.fasilitas);
                $('#prewiew_member_online').find('#alamat').html(data.member.alamat);
                $('#prewiew_member_online').find('#nohp').html(data.member.no_hp);
                $('#prewiew_member_online').find('#email').html(data.member.email);
                $('#prewiew_member_online').find('#pekerjaan').html(data.member.pekerjaan);
                $('#prewiew_member_online').find('#ttl').html(data.member.tmpl+', '+data.member.tgll + '<br>' + data.usia +' Tahun ');
                $('#prewiew_member_online').find('#ig').html(data.member.ig);
                $('#prewiew_member_online').find('#paket').html(data.paket);
                $('#prewiew_member_online').find('#biaya_paket').html(number_format(data.biaya));
                $('#prewiew_member_online').find('#foto_member').attr('src', '<?php echo base_url() ?>file/member/' + data.member.foto);
                $('#prewiew_member_online').find('#simpan_fasilitas').val(data.member.fasilitas);


                $('.harus_dibayar_reg_online').val(total);
                $('.paket_member').val(data.paket);
                $('.biaya_member_reg_online').val(data.biaya);
                $('.akhir_masa_aktif').val(data.tgl_akhir_masa_aktif);
                $('.id_member').val(data.member.id_member);
                $('.id_jenis_member').val(data.member.id_jenis_member);
                $('.total_reg_online').html(number_format(total));
                $('#prewiew_member_online').find('.kode_regis').html(data.member.kode_unik_member);
                $('#prewiew_member_online').find('.waktu_regis').html(format_tanggal(data.member.tgl_register) +' '+data.member.jam_register);
                $('#prewiew_member_online').find('#masa_aktif').html(data.masa_aktif);
                $('#prewiew_member_online').find('#status').html(data.member.status);
                $('#prewiew_member_online').find('#tgl_masa_aktif').html("Status : "+data.member.status+"<br>Aktif Sampai : "+format_tanggal(data.tgl_akhir_masa_aktif));

                $('#modal_pembayaran_member_online').find('#show_dibayar').html('Rp. '+number_format(total));
                $('#prewiew_member_online').find('#show_kembalian').html('Rp. '+number_format(0));
                if (data.member.status=='Aktif') {
                    // $('#prewiew_member_online').find('#keanggotaan_paket').html(data.keanggotaan_member.jenis_member);
                    // $('#prewiew_member_online').find('#keanggotaan_masa_aktif').html(data.keanggotaan_member.masa_aktif);
                    // $('#prewiew_member_online').find('#keanggotaan_tgl_berakhir_aktif').html("Aktif sampai <br>" +format_tanggal(data.keanggotaan_member.akhir_masa_aktif));
                    $('#prewiew_member_online').find('#form_keanggotaan').show();
                    $('#prewiew_member_online').find('#form_tagihan').hide();
                    $('#prewiew_member_online').find('#list_keanggontaan').html('');
                    $.each(data.keanggotaan_member, function(k,v){
                      $('#prewiew_member_online').find('#list_keanggontaan').append(`

                                                <li class="list-group-item">
                                                    <div class="widget-content p-0">
                                                        <div class="widget-content-wrapper">
                                                            <div class="widget-content-left">
                                                                <div class="widget-heading">`+v.kelompok_fasilitas+`<br>`+v.fasilitas+`</div>
                                                                <div class="widget-subheading"> Paket : `+v.jenis_member+`</div>
                                                                <!-- <a href="javascript:void(0)" onclick="Swal.fire('Info','Pada bagian ini ketika di klik akan menampilkan modal xl yang berisikan informasi List keanggotaan, dan absensi member terkait')">Lihat Detail Member Gym</a> -->
                                                            </div>
                                                            <div class="widget-content-right">
                                                                Aktif Sampai <br>`+format_tanggal(v.akhir_masa_aktif)+`
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                `);

                    });
                    
                }else{
                    $('#prewiew_member_online').find('#form_tagihan').show();
                    $('#prewiew_member_online').find('#form_keanggotaan').hide();

                }



              



              }, 
              error : function(){
                alert('Kesalahan Sistem');
              }
            });
}


function preview_member_gym_spesial(id_member){
  // $('#input_alert').hide();
  $('#prewiew_member_online').modal('show');
  // $('#form_pembayaran').hide();

     $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/detail_member_gym_spesial',
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_member : id_member,
                
              },
              success : function(data)
              {
                // alert('ditemukan');

                $('#prewiew_member_online').find('#nama_member').html('');
                $('#prewiew_member_online').find('#tagihan_member').attr('class','col-md-5');
                $('#prewiew_member_online').find('#identitas_member').show();
                  var total = parseInt(data.biaya) ; 
                $('#prewiew_member_online').find('#biaya_regis').html(number_format(0));
                $('.biaya_regis_reg_online').val(0);

         


                $('.nama').html(data.member.nama);
                $('#prewiew_member_online').find('#identitas').html(data.member.jenis_identitas + ' - '+data.member.no_identitas);
                $('#prewiew_member_online').find('#jk').html(data.member.jk);
                $('#prewiew_member_online').find('#fasilitas').html('Merdaftar pada fasilitas : <br>'+data.member.fasilitas);
                $('#prewiew_member_online').find('#alamat').html(data.member.alamat);
                $('#prewiew_member_online').find('#nohp').html(data.member.no_hp);
                $('#prewiew_member_online').find('#email').html(data.member.email);
                $('#prewiew_member_online').find('#pekerjaan').html(data.member.pekerjaan);
                $('#prewiew_member_online').find('#ttl').html(data.member.tmpl+', '+data.member.tgll + '<br>' + data.usia +' Tahun ');
                $('#prewiew_member_online').find('#ig').html(data.member.ig);
                $('#prewiew_member_online').find('#paket').html(data.paket);
                $('#prewiew_member_online').find('#biaya_paket').html(number_format(data.biaya));
                $('#prewiew_member_online').find('#foto_member').attr('src', '<?php echo base_url() ?>file/member/' + data.member.foto);
                $('#prewiew_member_online').find('#simpan_fasilitas').val(data.member.fasilitas);



                $('.harga_gym').val(data.harga_gym);
                $('.harga_swim').val(data.harga_swim);
                $('.harus_dibayar_reg_online').val(total);
                $('.paket_member').val(data.paket);
                $('.biaya_member_reg_online').val(data.biaya);
                $('.akhir_masa_aktif').val(data.tgl_akhir_masa_aktif);
                $('.id_member').val(data.member.id_member);
                $('.id_jenis_member').val(data.member.id_jenis_member);
                $('.total_reg_online').html(number_format(total));
                $('#prewiew_member_online').find('.kode_regis').html(data.member.kode_unik_member);
                $('#prewiew_member_online').find('.waktu_regis').html(format_tanggal(data.member.tgl_register) +' '+data.member.jam_register);
                $('#prewiew_member_online').find('#masa_aktif').html(data.masa_aktif);
                $('#prewiew_member_online').find('#status').html(data.member.status);
                $('#prewiew_member_online').find('#tgl_masa_aktif').html("Status : "+data.member.status+"<br>Aktif Sampai : "+format_tanggal(data.tgl_akhir_masa_aktif));

                $('#modal_pembayaran_member_online').find('#show_dibayar').html('Rp. '+number_format(total));
                $('#prewiew_member_online').find('#show_kembalian').html('Rp. '+number_format(0));
                if (data.member.status=='Aktif') {
                    // $('#prewiew_member_online').find('#keanggotaan_paket').html(data.keanggotaan_member.jenis_member);
                    // $('#prewiew_member_online').find('#keanggotaan_masa_aktif').html(data.keanggotaan_member.masa_aktif);
                    // $('#prewiew_member_online').find('#keanggotaan_tgl_berakhir_aktif').html("Aktif sampai <br>" +format_tanggal(data.keanggotaan_member.akhir_masa_aktif));
                    $('#prewiew_member_online').find('#form_keanggotaan').show();
                    $('#prewiew_member_online').find('#form_tagihan').hide();
                    $('#prewiew_member_online').find('#list_keanggontaan').html('');
                    $.each(data.keanggotaan_member, function(k,v){
                      $('#prewiew_member_online').find('#list_keanggontaan').append(`

                                                <li class="list-group-item">
                                                    <div class="widget-content p-0">
                                                        <div class="widget-content-wrapper">
                                                            <div class="widget-content-left">
                                                                <div class="widget-heading">`+v.kelompok_fasilitas+`<br>`+v.fasilitas+`</div>
                                                                <div class="widget-subheading"> Paket : `+v.jenis_member+`</div>
                                                                <!-- <a href="javascript:void(0)" onclick="Swal.fire('Info','Pada bagian ini ketika di klik akan menampilkan modal xl yang berisikan informasi List keanggotaan, dan absensi member terkait')">Lihat Detail Member Gym</a> -->
                                                            </div>
                                                            <div class="widget-content-right">
                                                                Aktif Sampai <br>`+format_tanggal(v.akhir_masa_aktif)+`
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                `);

                    });
                    
                }else{
                    $('#prewiew_member_online').find('#form_tagihan').show();
                    $('#prewiew_member_online').find('#form_keanggotaan').hide();

                }



              



              }, 
              error : function(){
                alert('Kesalahan Sistem');
              }
            });
}



function preview_member_swimming_bulanan(id_member){
  // $('#input_alert').hide();
  $('#prewiew_member_online').modal('show');
  // $('#form_pembayaran').hide();

     $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/detail_member_swimming_bulanan',
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_member : id_member,
                
              },
              success : function(data)
              {
                // alert('ditemukan');
                if (data.member.id_jenis_member=='1') {
                  var total = parseInt(data.biaya) ; 
                $('#prewiew_member_online').find('#biaya_regis').html(number_format(0));
                $('.biaya_regis_reg_online').val(0);
                $('#prewiew_member_online').find('#identitas_member').hide();
                $('#prewiew_member_online').find('#tagihan_member').attr('class','col-md-12');
                $('#prewiew_member_online').find('#nama_member').html(data.member.nama);

                }else{
                $('#prewiew_member_online').find('#nama_member').html('');
                $('#prewiew_member_online').find('#tagihan_member').attr('class','col-md-5');
                $('#prewiew_member_online').find('#identitas_member').show();
                  var total = parseInt(data.biaya) ; 
                $('#prewiew_member_online').find('#biaya_regis').html(number_format(0));
                $('.biaya_regis_reg_online').val(0);

                }


                $('.nama').html(data.member.nama);
                $('#prewiew_member_online').find('#identitas').html(data.member.jenis_identitas + ' - '+data.member.no_identitas);
                $('#prewiew_member_online').find('#jk').html(data.member.jk);
                $('#prewiew_member_online').find('#fasilitas').html('Merdaftar pada fasilitas : <br>'+data.member.fasilitas);
                $('#prewiew_member_online').find('#alamat').html(data.member.alamat);
                $('#prewiew_member_online').find('#nohp').html(data.member.no_hp);
                $('#prewiew_member_online').find('#email').html(data.member.email);
                $('#prewiew_member_online').find('#pekerjaan').html(data.member.pekerjaan);
                $('#prewiew_member_online').find('#ttl').html(data.member.tmpl+', '+data.member.tgll + '<br>' + data.usia +' Tahun ');
                $('#prewiew_member_online').find('#ig').html(data.member.ig);
                $('#prewiew_member_online').find('#paket').html(data.paket);
                $('#prewiew_member_online').find('#biaya_paket').html(number_format(data.biaya));
                $('#prewiew_member_online').find('#foto_member').attr('src', '<?php echo base_url() ?>file/member/' + data.member.foto);
                $('#prewiew_member_online').find('#simpan_fasilitas').val(data.member.fasilitas);


                $('.harus_dibayar_reg_online').val(total);
                $('.paket_member').val(data.paket);
                $('.biaya_member_reg_online').val(data.biaya);
                $('.akhir_masa_aktif').val(data.tgl_akhir_masa_aktif);
                $('.id_member').val(data.member.id_member);
                $('.id_jenis_member').val(data.member.id_jenis_member);
                $('.total_reg_online').html(number_format(total));
                $('#prewiew_member_online').find('.kode_regis').html(data.member.kode_unik_member);
                $('#prewiew_member_online').find('.waktu_regis').html(format_tanggal(data.member.tgl_register) +' '+data.member.jam_register);
                $('#prewiew_member_online').find('#masa_aktif').html(data.masa_aktif);
                $('#prewiew_member_online').find('#status').html(data.member.status);
                $('#prewiew_member_online').find('#tgl_masa_aktif').html("Status : "+data.member.status+"<br>Aktif Sampai : "+format_tanggal(data.tgl_akhir_masa_aktif));

                $('#modal_pembayaran_member_online').find('#show_dibayar').html('Rp. '+number_format(total));
                $('#prewiew_member_online').find('#show_kembalian').html('Rp. '+number_format(0));
                if (data.member.status=='Aktif') {
                    // $('#prewiew_member_online').find('#keanggotaan_paket').html(data.keanggotaan_member.jenis_member);
                    // $('#prewiew_member_online').find('#keanggotaan_masa_aktif').html(data.keanggotaan_member.masa_aktif);
                    // $('#prewiew_member_online').find('#keanggotaan_tgl_berakhir_aktif').html("Aktif sampai <br>" +format_tanggal(data.keanggotaan_member.akhir_masa_aktif));
                    $('#prewiew_member_online').find('#form_keanggotaan').show();
                    $('#prewiew_member_online').find('#form_tagihan').hide();
                    $('#prewiew_member_online').find('#list_keanggontaan').html('');
                    $.each(data.keanggotaan_member, function(k,v){
                      $('#prewiew_member_online').find('#list_keanggontaan').append(`

                                                <li class="list-group-item">
                                                    <div class="widget-content p-0">
                                                        <div class="widget-content-wrapper">
                                                            <div class="widget-content-left">
                                                                <div class="widget-heading">`+v.kelompok_fasilitas+`<br>`+v.fasilitas+`</div>
                                                                <div class="widget-subheading"> Paket : `+v.jenis_member+`</div>
                                                                <!-- <a href="javascript:void(0)" onclick="Swal.fire('Info','Pada bagian ini ketika di klik akan menampilkan modal xl yang berisikan informasi List keanggotaan, dan absensi member terkait')">Lihat Detail Member Gym</a> -->
                                                            </div>
                                                            <div class="widget-content-right">
                                                                Aktif Sampai <br>`+format_tanggal(v.akhir_masa_aktif)+`
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                `);

                    });
                    
                }else{
                    $('#prewiew_member_online').find('#form_tagihan').show();
                    $('#prewiew_member_online').find('#form_keanggotaan').hide();

                }



              



              }, 
              error : function(){
                alert('Kesalahan Sistem');
              }
            });
}



function preview_member_swimming_spesial(id_member){
  // $('#input_alert').hide();
  $('#prewiew_member_online').modal('show');
  // $('#form_pembayaran').hide();

     $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/detail_member_swimming_spesial',
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_member : id_member,
                
              },
              success : function(data)
              {
                // alert('ditemukan');
                if (data.member.id_jenis_member=='1') {
                  var total = parseInt(data.biaya) ; 
                $('#prewiew_member_online').find('#biaya_regis').html(number_format(0));
                $('.biaya_regis_reg_online').val(0);
                $('#prewiew_member_online').find('#identitas_member').hide();
                $('#prewiew_member_online').find('#tagihan_member').attr('class','col-md-12');
                $('#prewiew_member_online').find('#nama_member').html(data.member.nama);

                }else{
                $('#prewiew_member_online').find('#nama_member').html('');
                $('#prewiew_member_online').find('#tagihan_member').attr('class','col-md-5');
                $('#prewiew_member_online').find('#identitas_member').show();
                  var total = parseInt(data.biaya) ; 
                $('#prewiew_member_online').find('#biaya_regis').html(number_format(0));
                $('.biaya_regis_reg_online').val(0);

                }


                $('.nama').html(data.member.nama);
                $('#prewiew_member_online').find('#identitas').html(data.member.jenis_identitas + ' - '+data.member.no_identitas);
                $('#prewiew_member_online').find('#jk').html(data.member.jk);
                $('#prewiew_member_online').find('#fasilitas').html('Merdaftar pada fasilitas : <br>'+data.member.fasilitas);
                $('#prewiew_member_online').find('#alamat').html(data.member.alamat);
                $('#prewiew_member_online').find('#nohp').html(data.member.no_hp);
                $('#prewiew_member_online').find('#email').html(data.member.email);
                $('#prewiew_member_online').find('#pekerjaan').html(data.member.pekerjaan);
                $('#prewiew_member_online').find('#ttl').html(data.member.tmpl+', '+data.member.tgll + '<br>' + data.usia +' Tahun ');
                $('#prewiew_member_online').find('#ig').html(data.member.ig);
                $('#prewiew_member_online').find('#paket').html(data.paket);
                $('#prewiew_member_online').find('#biaya_paket').html(number_format(data.biaya));
                $('#prewiew_member_online').find('#foto_member').attr('src', '<?php echo base_url() ?>file/member/' + data.member.foto);
                $('#prewiew_member_online').find('#simpan_fasilitas').val(data.member.fasilitas);


                $('.harus_dibayar_reg_online').val(total);
                $('.paket_member').val(data.paket);
                $('.biaya_member_reg_online').val(data.biaya);
                $('.akhir_masa_aktif').val(data.tgl_akhir_masa_aktif);
                $('.id_member').val(data.member.id_member);
                $('.id_jenis_member').val(data.member.id_jenis_member);
                $('.total_reg_online').html(number_format(total));
                $('#prewiew_member_online').find('.kode_regis').html(data.member.kode_unik_member);
                $('#prewiew_member_online').find('.waktu_regis').html(format_tanggal(data.member.tgl_register) +' '+data.member.jam_register);
                $('#prewiew_member_online').find('#masa_aktif').html(data.masa_aktif);
                $('#prewiew_member_online').find('#status').html(data.member.status);
                $('#prewiew_member_online').find('#tgl_masa_aktif').html("Status : "+data.member.status+"<br>Aktif Sampai : "+format_tanggal(data.tgl_akhir_masa_aktif));

                $('#modal_pembayaran_member_online').find('#show_dibayar').html('Rp. '+number_format(total));
                $('#prewiew_member_online').find('#show_kembalian').html('Rp. '+number_format(0));
                if (data.member.status=='Aktif') {
                    // $('#prewiew_member_online').find('#keanggotaan_paket').html(data.keanggotaan_member.jenis_member);
                    // $('#prewiew_member_online').find('#keanggotaan_masa_aktif').html(data.keanggotaan_member.masa_aktif);
                    // $('#prewiew_member_online').find('#keanggotaan_tgl_berakhir_aktif').html("Aktif sampai <br>" +format_tanggal(data.keanggotaan_member.akhir_masa_aktif));
                    $('#prewiew_member_online').find('#form_keanggotaan').show();
                    $('#prewiew_member_online').find('#form_tagihan').hide();
                    $('#prewiew_member_online').find('#list_keanggontaan').html('');
                    $.each(data.keanggotaan_member, function(k,v){
                      $('#prewiew_member_online').find('#list_keanggontaan').append(`

                                                <li class="list-group-item">
                                                    <div class="widget-content p-0">
                                                        <div class="widget-content-wrapper">
                                                            <div class="widget-content-left">
                                                                <div class="widget-heading">`+v.kelompok_fasilitas+`<br>`+v.fasilitas+`</div>
                                                                <div class="widget-subheading"> Paket : `+v.jenis_member+`</div>
                                                                <!-- <a href="javascript:void(0)" onclick="Swal.fire('Info','Pada bagian ini ketika di klik akan menampilkan modal xl yang berisikan informasi List keanggotaan, dan absensi member terkait')">Lihat Detail Member Gym</a> -->
                                                            </div>
                                                            <div class="widget-content-right">
                                                                Aktif Sampai <br>`+format_tanggal(v.akhir_masa_aktif)+`
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                `);

                    });
                    
                }else{
                    $('#prewiew_member_online').find('#form_tagihan').show();
                    $('#prewiew_member_online').find('#form_keanggotaan').hide();

                }



              



              }, 
              error : function(){
                alert('Kesalahan Sistem');
              }
            });
}




  function bersihkan_data(){
     
    Swal.fire({
        title: 'Hapus Semua ?',
        text: 'Hapus semua data calon member yang registrasi pada g-sportscenter.com .?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/hapus_calon_member',
              type: 'POST',
              dataType: 'JSON',
              data: {    
               
              },
              success: function(data) {
                if (data.response==200) {
                  Swal.fire('Data dibersihkan','Semua data calon member dihapus','success');
                }else{
                  Swal.fire('Data Gagal Dihapus','Semua data calon member gagal dihapus','error');
                }
                  $('#list_data_member_reg').DataTable().ajax.reload();
              },
              error: function(jqXHR, textStatus, errorThrown) {
              }
          });

        
        }
      });


    
  }


function edit_tgll(id_member, tgll){
  // $('#input_alert').hide();
  $('#edit_tgll').modal('show');

  // var arrayKata = tgll.split("-");
  // var tgl = parseInt(arrayKata[1]) > 9 ? arrayKata[2] : '0' +arrayKata[2]; 
  // var bln = parseInt(arrayKata[1]) > 9 ? arrayKata[1] : '0' +arrayKata[1];
  // var tgl_val =  arrayKata[0] +'-'+ bln+'-'+ tgl;
  // $('#edit_tgll').find('#tgll').val(tgl_val);
  $('#edit_tgll').find('#id_member').val(id_member);
}
</script>