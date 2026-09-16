     <div class="row">
                                <div class="col-md-3">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">Cek Data Member / Check in / checkout
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
                                    <div class="main-card mb-3 card" id="card_foto" style="display:none">
                                        <div class="card-header">Foto Member
                                        </div>
                                        <div class="card-body">
                                            <img src="" width="100%" id="foto_member" class="img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">Profile Member
                                        </div>
                                        <div class="card-body">
                                          <div  class="input_alert">
                                            <div class="alert alert-info">
                                              Masukan kode Registrasi / Scan QR code <br>untuk melakukan pengecekan data
                                            </div>
                                          </div>
                                            <div class="row preview" style="display:none" >
                                            <div class="col-md-12">
                                              <div class="row">
                                                 <div class="col-md-12">
                                                  <h5 class="menu-header-title nama mb-3 text-center">Nama Member</h5>
                                                </div>
                                                  <div class="col-md-12">
                                                    <table class="table">
                                                      <tr>
                                                        <td valign="top">Kode</td>
                                                        <td valign="top">:</td>
                                                        <td valign="top" id="kode_member"></td>
                                                      </tr>
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
                                          </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">Kunjungan 
                                        </div>
                                        <div class="card-body">
                                          <div  class="input_alert">
                                            <div class="alert alert-info">
                                              Masukan kode Registrasi / Scan QR code <br>untuk melakukan pengecekan data
                                            </div>
                                          </div>
                                            <div class="row preview"  style="display:none" >
                                            <div class="col-md-12">
                                                <ul class="list-group list-group-flush">
                                                <li class="active list-group-item">
                                                  <div class="widget-content p-0">
                                                      <div class="widget-content-wrapper">
                                                          <div class="widget-content-left" id="paket_member_aktif">
                                                          </div>
                                                          <div class="widget-content-right">
                                                              
                                                          </div>
                                                      </div>
                                                  </div>
                                                </li>
                                                <li class="list-group-item">
                                                  <div class="alert_absensi"></div>
                                                 <div id="list_absensi"></div>
                                                </li>
                                              </ul>

                                            
                                            </div>
                      







                                          </div>
                                        </div>

                                    </div>
                                </div>
                               
                            </div>



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
                console.log(data);
                if (data.found==0) {
                  Swal.fire('Error','Data tidak ditemukan','error');
                  setTimeout(close_swal, 3000);
                  audio_tidak_ditemukan();
                  $('#card_foto').hide();
                  $('.input_alert').show();
                  $('.preview').hide();
                }else{ 
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



function simpan_transaksi_online(){
 var formdata = $('#form_konfirmasi_reg_online').serialize();
 var id_member = $('.id_member').val();
 console.log(id_member);
 // console.log(formdata);
  $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gro/transaction/cek_data_member/submit_aktifkan_member',
              dataType: 'JSON',
              type    : 'POST',
              data    :  formdata,
              success : function(data)
              {
                console.log(data.responcode);
                if (data.responcode==200) {

                  toastr.success('Data pembayaran disimpan'); 
                  $('#modal_pembayaran_member_online').modal('hide');
                  print_transaksi(data.id_transaksi);
                  preview_member(id_member);
                }else{
                  Swal.fire('Error','Terjadi kesalahan','error');
                }
                
              }, 
              error : function(){
                console.log('error')
              }
            });

}


  function print_transaksi(id_transaksi){
     $('#konfirmasi_gym').modal('hide');
    $('#print_transaksi_gym').modal('show');
    $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
      <iframe src="`+ baseUrl('/user/gro/transaction/cek_data_member/print_transaksi_gym/') + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
      `);
  }
function preview_member(id_member){
  $('.input_alert').hide();
  $('.preview').show();
  $('#card_foto').show();

     $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gym/cek_data_member/detail_member',
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_member : id_member,
                
              },
              success : function(data)
              {
                // alert('ditemukan');
                console.log(data.member.foto);
                $('.nama').html('<b>'+data.member.nama + '</b>');
                $('.preview').find('#kode_member').html(data.member.kode_unik_member);
                $('.preview').find('#identitas').html(data.member.jenis_identitas + ' - '+data.member.no_identitas);
                $('.preview').find('#jk').html(data.member.jk);
                $('.preview').find('#fasilitas').html('Terdaftar pada fasilitas : '+data.member.fasilitas);
                $('.preview').find('#alamat').html(data.member.alamat);
                $('.preview').find('#nohp').html(data.member.no_hp);
                $('.preview').find('#email').html(data.member.email);
                $('.preview').find('#pekerjaan').html(data.member.pekerjaan);
                $('.preview').find('#ttl').html(data.member.tmpl+', '+data.member.tgll + '<br>' + data.usia +' Tahun ');
                $('.preview').find('#ig').html(data.member.ig);
                $('.preview').find('#paket').html(data.paket);
                $('#foto_member').attr('src', '<?php echo base_url() ?>file/member/' + data.member.foto);

                var tgls = data.tgls;
                if (data.count_data==0) {
                   $('#paket_member_aktif').html(`
                                                                <h5 class="list-group-item-heading" id="">
                                                                  Member tidak terdaftar pada fasilitas Gym</h5>
                                                                <span class="badge badge-danger">Silahkan melakukan registrasi dulu</span>
                                                                `);

                }else{
                  $.each(data.keanggotaan_member, function(k,v){
                    if (v.akhir_masa_aktif < tgls) {
                    $('#paket_member_aktif').html(`
                                                                <div class="widget-subheading"> 
                                                                  Paket Aktif Saat Ini
                                                                </div>
                                                                <h5 class="list-group-item-heading" id="">`+v.fasilitas+` | `+v.jenis_member+`</h5>
                                                                <h6 class="list-group-item-heading" id="">Masa Aktif : `+format_tanggal(v.awal_masa_aktif)+` s.d. `+format_tanggal(v.akhir_masa_aktif)+`</h6>
                                                                <span class="badge badge-danger">Expired</span>
                                                                `);

                    }else{
                      if (tgls >= v.awal_masa_aktif) {
                      $('#paket_member_aktif').html(`
                                                                  <div class="widget-subheading"> 
                                                                    Paket Aktif Saat Ini
                                                                  </div>
                                                                  <h5 class="list-group-item-heading" id="">`+v.fasilitas+` | `+v.jenis_member+`</h5>
                                                                  <h6 class="list-group-item-heading" id="">Masa Aktif : `+format_tanggal(v.awal_masa_aktif)+` s.d. `+format_tanggal(v.akhir_masa_aktif)+`</h6>
                                                                  <span class="badge badge-success">Aktif</span>
                                                                  `);

                      simpan_kunjungan(v.id_member, v.id_keanggotaan_member);

                      }else{
                      $('#paket_member_aktif').html(`
                                                                  <div class="widget-subheading"> 
                                                                    Paket Aktif Saat Ini
                                                                  </div>
                                                                  <h5 class="list-group-item-heading" id="">`+v.fasilitas+` | `+v.jenis_member+`</h5>
                                                                  <h6 class="list-group-item-heading" id="">Masa Aktif : `+format_tanggal(v.awal_masa_aktif)+` s.d. `+format_tanggal(v.akhir_masa_aktif)+`</h6>
                                                                  <span class="badge badge-warning">Belum Aktif</span>
                                                                  `);

                      }




                    }
                  });
                }



              



              }, 
              error : function(){
                alert('e');
              }
            });
}


function simpan_kunjungan(id_member, id_keanggotaan){
  $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gym/cek_data_member/simpan_kunjungan',
              dataType: 'JSON',
              type    : 'POST',
              data    :  {
                id_member : id_member , 
                id_keanggotaan : id_keanggotaan , 
              },
              success : function(data)
              {
              Swal.fire(data.action,data.caption,'success');
              $('.alert_absensi').html(`<div class="alert alert-info">`+data.alert+`</div>`);

                list_kunjungan(id_member, id_keanggotaan);
                  setTimeout(close_swal, 4000);
                // console.log(id_member);
             
              }, 
              error : function(){
                console.log('error cuy');
              }
            });
}

function list_kunjungan(id_member, id_keanggotaan){


  $.ajax(
            {
              url     : '<?php echo base_url() ?>user/gym/cek_data_member/kunjungan_member_hariini',
              dataType: 'JSON',
              type    : 'POST',
              data    :  {
                id_member : id_member , 
                id_keanggotaan : id_keanggotaan , 
              },
              success : function(data)
              {
                tabel_kunjungan = `<table class="table">
                  <tr>
                  <td>No</td>
                  <td>Checkin</td>
                  <td>Check Out</td>
                  <td>Check Durasi</td>
                  </tr>
                  `;
              $.each(data.data, function(k,v){
                var checkout = v.tgl_check_out =='' ? 'Belum checkout' : v.tgl_check_out+` | `+v.jam_check_out;
                var durasi = v.tgl_check_out =='' ? 'Checkout dulu untuk menghitung durasi' : v.durasi;
                tabel_kunjungan += `<tr>
                  <td>`+(k+1)+`</td>
                  <td>`+v.tgl_check_in+` | `+v.jam_check_in+`</td>
                  <td>`+checkout+`</td>
                  <td>`+durasi+`</td>
                  </tr>
                  `;
                  });
                tabel_kunjungan += `</table>`;
                // tabel_kunjungan += `<button class="btn btn-info btn-sm" onclick="riwayat_absensi('`+id_member+`')">Riwayat Absensi</button>`;
                $('#list_absensi').html(tabel_kunjungan);
              }, 
              error : function(){
                console.log('error list');
              }
            });
}
function riwayat_absensi(id_member){

alert('selanjutnya');
  // $.ajax(
  //           {
  //             url     : '<?php echo base_url() ?>user/gym/cek_data_member/kunjungan_member_hariini',
  //             dataType: 'JSON',
  //             type    : 'POST',
  //             data    :  {
  //               id_member : id_member , 
  //               id_keanggotaan : id_keanggotaan , 
  //             },
  //             success : function(data)
  //             {
  //               tabel_kunjungan = `<table class="table">
  //                 <tr>
  //                 <td>No</td>
  //                 <td>Checkin</td>
  //                 <td>Check Out</td>
  //                 <td>Check Durasi</td>
  //                 </tr>
  //                 `;
  //             $.each(data.data, function(k,v){
  //               var checkout = v.tgl_check_out =='' ? 'Belum checkout' : v.tgl_check_out+` | `+v.jam_check_out;
  //               var durasi = v.tgl_check_out =='' ? 'Checkout dulu untuk menghitung durasi' : v.durasi;
  //               tabel_kunjungan += `<tr>
  //                 <td>`+(k+1)+`</td>
  //                 <td>`+v.tgl_check_in+` | `+v.jam_check_in+`</td>
  //                 <td>`+checkout+`</td>
  //                 <td>`+durasi+`</td>
  //                 </tr>
  //                 `;
  //                 });
  //               tabel_kunjungan += `</table>`;
  //               tabel_kunjungan += `<button class="btn btn-info btn-sm" onclick="riwayat_absensi('`+id_member+`')">Riwayat Absensi</button>`;
  //               $('#list_absensi').html(tabel_kunjungan);
  //             }, 
  //             error : function(){
  //               console.log('error list');
  //             }
  //           });
}
</script>