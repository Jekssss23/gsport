<div class="row">

                          <?php
                           $q_lapangan = $this->db->query("SELECT lapangan from lapangan_atas where diperuntukan='Pickle'")->result_array();
                            $tgl = date('Y-m-d');//$this->input->get('tgl') ?>   
                                <div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">
                                Tanggal : <?php echo @show_tanggal($this->input->get('tgl')) ?>
                              
                            </div>
                                     

                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">List Order pickle
                                          <div class="btn-actions-pane-right">
                                                <div role="group" class="btn-group-sm nav btn-group">
                                                    <a data-toggle="tab" href="#tab_pickle_harian" class="btn-shadow active btn btn-primary" onclick="data_order_pickle('harian')">Harian</a>
                                                    <a data-toggle="tab" href="#tab_pickle_bulanan" class="btn-shadow  btn btn-primary" onclick="data_order_pickle('bulanan')">Member Bulanan</a>
                                                    <a data-toggle="tab" href="#tab_pickle_turnamen" class="btn-shadow  btn btn-primary" onclick="data_order_pickle('turnamen')">Turnamen</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                           <div class="tab-content">
                                                  <div class="tab-pane active" id="tab_pickle_harian" role="tabpanel">
                                                      <table class="table" width="100%" id="list_order_pickle_harian" >
                                                       <thead>
                                                          <tr>
                                                          <th>No</th>
                                                          <th>Nama</th>
                                                          <th>No HP</th>
                                                          <th>Fasilitas</th>
                                                          <th>Option</th>
                                                        </tr>
                                                       </thead>
                                                      </table>
                                                    
                                                    </div>
                                                  <div class="tab-pane show" id="tab_pickle_bulanan" role="tabpanel">
                                                      <table class="table" width="100%" id="list_order_pickle_bulanan" >
                                                       <thead>
                                                          <tr>
                                                          <th>No</th>
                                                          <th>Nama</th>
                                                          <th>No HP</th>
                                                          <th>Fasilitas</th>
                                                          <th>Option</th>
                                                        </tr>
                                                       </thead>
                                                      </table>
                                                    </div>
                                                  <div class="tab-pane show" id="tab_pickle_turnamen" role="tabpanel">
                                                      <table class="table" width="100%" id="list_order_pickle_turnamen" >
                                                       <thead>
                                                          <tr>
                                                          <th>No</th>
                                                          <th>Nama</th>
                                                          <th>No HP</th>
                                                          <th>Fasilitas</th>
                                                          <th>Option</th>
                                                        </tr>
                                                       </thead>
                                                      </table>
                                                    </div>
                                              </div>
                                         









                                            <div class="col-md-12">
                                            </div>
                                          






                                        </div>

                                    </div>

                                    <div class="main-card mb-3 card">
                                        
                                        <div class="card-header" id="caption_rekap_pickle_bulanan">Rekap Member pickle Bulanan<br>
                                          
                                          <?php echo nama_bulan($bulan).'  '.$tahun ?>
                                         
                                          <div class="btn-actions-pane-right">
                                                <div role="group" class="btn-group-sm nav btn-group">
                                                    <a href="javascript:void(0)" class="btn-shadow btn btn-primary" onclick="rekap_pickle_semua('semua','','')">Semua Data</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                           <div class="tab-content">
                                                  <div class="tab-pane active" id="rekap_pickle_bulanan" role="tabpanel">
                                                      <table class="table" width="100%" id="list_rekap_pickle_bulanan" >
                                                       <thead>
                                                          <tr>
                                                          <th>No</th>
                                                          <th>Nama / No HP</th>
                                                          <th>Status</th>
                                                          <th>Option</th>
                                                        </tr>
                                                       </thead>
                                                      </table>
                                                    
                                                    </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-md-7">
                                  <div class="row xxxx">
                                 <?php   foreach ($q_lapangan as $k => $v) { ?>
                                    <div class="col-md-12">
                                      



                                       <div class="main-card mb-3 card">
                                        <div class="card-header">Lapangan <?php echo $v['lapangan'] ?>
                                        </div>
                                        <div class="card-body">
                                         <table class="table table-bordered peta_lapangan_booking_pickle_<?php echo $v['lapangan'] ?>" id="">
                                         </table>
                                        
                                          
                                        </div>
                                      </div>
                                    </div>
                                  <?php } ?>
                                  </div>
                                  
                                </div>
                               
                            </div>
                         

  <script type="text/javascript">
showAutoCurrency();
    
  function showAutoCurrency(){
    $('input.currency').number( true, 0 );
  }

    $('#metode_pembayaran').change(function(){
      var opsi_tambah_metode_pembayaran = $('#opsi_tambah_metode_pembayaran').val();
      if (opsi_tambah_metode_pembayaran=='1') {
        tambah_metode_pembayaran_pickle();
      }
    });
    function tambah_metode_pembayaran_pickle(){
      $('#opsi_tambah_metode_pembayaran').val(1);
      var metode_pembayaran_1 = $('#metode_pembayaran').val();
      var metode_pembayaran = '<?php echo json_encode(metode_pembayaran()) ?>';
      var parse_json = JSON.parse(metode_pembayaran);
      $('#form_tambah_metode_pembayaran_pickle').html(`<div class="col-sm-6 col-md-6 col-xl-6">
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
                           <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="batal_tambah_metode_pembayaran_pickle()">Batal Tambah Metode Pembayaran</button>
                         </div>
                      </div>`);

      $('#konfirmasi_pembayaran_booking_pickle').find('#metode_pembayaran_2').html(``);
      $.each(parse_json, function(k,v){
        if (v.id_metode_pembayaran!=metode_pembayaran_1) {
      
          $('#konfirmasi_pembayaran_booking_pickle').find('#metode_pembayaran_2').append(`<option value="`+v.id_metode_pembayaran+`">`+v.metode_pembayaran+`</option>`);
        }

      });
      // $('#form_tambah_metode_pembayaran_pickle').append(` `);




    // $('#input_dibayar_2').keyup(function(){
    //  var input_dibayar_1 = $('#input_dibayar').val();
    //  var input_dibayar_2 = $('#input_dibayar_2').val();
    //  var total_dibayar = parseInt(input_dibayar_1) + parseInt(input_dibayar_2);
    //  $('#simpan_dibayar').val(total_dibayar);

    // });


                $('#form_konfirmasi_booking_pickle').find('#input_dibayar_2').keyup(function(){
                  var input_dibayar_1 = $('#form_konfirmasi_booking_pickle').find('#input_dibayar').val();
      var input_dibayar_2 = $('#form_konfirmasi_booking_pickle').find('#input_dibayar_2').val();
      var dibayar_pelanggan = parseInt(input_dibayar_1) + parseInt(input_dibayar_2);
      $('#form_konfirmasi_booking_pickle').find('#simpan_dibayar').val(dibayar_pelanggan);

                  var tagihan = $('#form_konfirmasi_booking_pickle').find('#sisa').val();
                  if (input_dibayar_1=='') {
                    // Swal.fire('Warning','Pembayaran pada metode pembayaran pertama harus di input dulu','error');
                          var sisa = input_dibayar_2 - tagihan;
                          $('#form_konfirmasi_booking_pickle').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#form_konfirmasi_booking_pickle').find('#show_dibayar').html("Rp. "+number_format(input_dibayar_2));
                  }else{
                    if (input_dibayar_2=='') {
                          var sisa = input_dibayar_1 - tagihan;
                          $('#form_konfirmasi_booking_pickle').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#form_konfirmasi_booking_pickle').find('#show_dibayar').html("Rp. "+number_format(input_dibayar_1));

                    }else{

                     if (dibayar_pelanggan >0) {

                          var sisa = dibayar_pelanggan - tagihan;
                          $('#form_konfirmasi_booking_pickle').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#form_konfirmasi_booking_pickle').find('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                          $('#form_konfirmasi_booking_pickle').find('#simpan_dibayar').val(dibayar_pelanggan);
                          $('#form_konfirmasi_booking_pickle').find('#simpan_kembalian').val(sisa);



                           if (sisa<0) {
                            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('class','btn btn-block btn-outline-danger mt-3');
                            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('class','btn btn-block btn-outline-danger mt-3');
                            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('onclick','Swal.fire("Error","Harap masukan nilai pembayaran dengan benar","error")');
                            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('onclick','Swal.fire("Error","Harap masukan nilai pembayaran dengan benar","error")');

                        }else{
                            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('class','btn btn-block btn-success mt-3');
                            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('class','btn btn-block btn-info mt-3');
                            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('onclick','simpan_visit_pickle_harian()');
                            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('onclick','simpan_visit_pickle_turnamen()');
                        }




                     
                      }else{
                         $('#form_konfirmasi_booking_pickle').find('#show_dibayar').html("Rp. "+ number_format(tagihan));
                        $('#form_konfirmasi_booking_pickle').find('#simpan_dibayar').val(tagihan);
                        $('#form_konfirmasi_booking_pickle').find('#simpan_kembalian').val(0);
                        $('#form_konfirmasi_booking_pickle').find('#show_kembalian').html('Rp. 0');
                      }

                    }
                } 





                });








    }
    function batal_tambah_metode_pembayaran_pickle(){
      
      $('#opsi_tambah_metode_pembayaran').val(0);
      $('#form_tambah_metode_pembayaran_pickle').html(`

                        <div class="col-sm-12 col-md-12 col-xl-12">
                          <div class="form-group">
                           <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="tambah_metode_pembayaran_pickle()" id="tombol_tambah_metode_pembayaran">Tambah Metode Pembayaran</button>
                         </div>
                      </div>`);

      $('konfirmasi_pembayaran_booking_pickle').find('#metode_pembayaran_2').html(``);
      
  var input_dibayar_1 = $('#form_konfirmasi_booking_pickle').find('#input_dibayar').val();
      var dibayar_pelanggan = parseInt(input_dibayar_1);

                  var tagihan = $('#form_konfirmasi_booking_pickle').find('#sisa').val();

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

   <?php 
  
   foreach ($q_lapangan as $k => $v) { ?>
       show_peta_booking_pickle('<?php echo $v['lapangan'] ?>');
   <?php } ?>

   // show_peta_booking_pickle('1');
  function show_peta_booking_pickle(lapangan){
  $('.peta_lapangan_booking_pickle_'+ lapangan).html('');


     $.ajax(
            {
              url     : '<?php echo base_url() ?>/user/gro/booking/jadwal_pickle',
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
                lapangan : lapangan,
                tgl : '<?php echo $this->input->get('tgl') ?>',
              },
              success : function(data)
              {
                $.each(data, function (k,v){

                  $('.peta_lapangan_booking_pickle_'+ lapangan).append(`<tr class="show_jam_pickle_`+ lapangan+ '_' +k+`">`);
                  $.each(v, function(k_jam, v_jam){
                    if (v_jam.pembooking=='Booking Kosong') {
                      var dibooking = v_jam.pembooking;
                    }else{
                      var dibooking = '<a href="javascript:void(0)" >'+v_jam.pembooking+'</a>';
                      // var dibooking = '<a href="javascript:void(0)" onclick="cek_lapangan_pickle(`'+v_jam.id_jadwal+'`,`'+v_jam.id_order+'`)">'+v_jam.pembooking+'</a>';

                    }
                    $('.show_jam_pickle_'+ lapangan+ '_' +k ).append('<td style="'+v_jam.warna+'">'+v_jam.jam+'<br>'+dibooking+'</td>');

                  })

                  $('.peta_lapangan_booking_pickle_'+ lapangan).append(`</tr>`);
                });


              },
              error : function (){
                alert('ee');
              }
      });
  }



function detail_jam_booking_pickle(id_jadwal, id_booking){

   $('#cek_lapangan_pickle').find('#list_lapangan_pickle').html('');
     $.ajax(
            {
              url     : '<?php echo base_url() ?>/user/gro/booking/cek_lapangan_pickle',
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
                id_booking : id_booking,
                tgl : '<?php echo $this->input->get('tgl') ?>',
                id_jadwal : id_jadwal,
              },
              success : function(data)
              {
                $('#cek_lapangan_pickle').find('#list_jadwal_pickle').html('');
                $('#cek_lapangan_pickle').find('#fasilitas').html(data.data_jadwal_terpilih.fasilitas);
                $('#cek_lapangan_pickle').find('#lapangan').html("Lapangan "+data.data_jadwal_terpilih.lapangan);
                $('#cek_lapangan_pickle').find('#nama').html(data.data_jadwal_terpilih.nama);
                $('#cek_lapangan_pickle').find('#nohp').html(data.data_jadwal_terpilih.no_hp);
                $('#cek_lapangan_pickle').find('#tgl_main').html(format_tanggal(data.data_jadwal_terpilih.tgl_main));

                // var tombol_action_cancel = '<button type="button" class="btn btn-outline-info btn-xs" onclick="cancel_pickle('+"'"+data.data_jadwal_terpilih.id_detail_booking_pickle+"','"+data.data_jadwal_terpilih.lapangan+"','"+data.data_jadwal_terpilih.tgl_main+"','"+data.data_jadwal_terpilih.jam_main+"','"+data.data_jadwal_terpilih.nama+"','"+data.data_jadwal_terpilih.id_identitas_order+"','"+data.data_jadwal_terpilih.fasilitas+"'"+')">Cancel</button>';
                // var tombol_action_selesai = '<button class="btn btn-outline-success btn-xs" type="button" onclick="selesai_main_pickle('+"'"+ data.data_jadwal_terpilih.id_detail_booking_pickle + "','"+ data.data_jadwal_terpilih.tgl_main + "','"+ data.data_jadwal_terpilih.jam_main + "','"+ data.data_jadwal_terpilih.id_identias_order + "','"+ data.data_jadwal_terpilih.lapangan + "'"+')">Visit</button>';

                // if (data.data_jadwal_terpilih.status=='Booking') {
                //   $('#cek_lapangan_pickle').find('#status').html(data.data_jadwal_terpilih.status + '<br>' + tombol_action_cancel + ' '+ tombol_action_selesai);

                // }else{
                //   $('#cek_lapangan_pickle').find('#status').html(data.data_jadwal_terpilih.status );

                // }


                var total
                $.each(data.data_jadwal_lainnya, function(k,v){
                  if (v.jam_main==data.data_jadwal_terpilih.jam_main) {

                  var status = v.status + ``;
                  }else{
                  var status = v.status + '';

                  }




                var tombol_action_cancel = '<button type="button" class="btn btn-info btn-xs" onclick="cancel_pickle('+"'"+v.id_detail_booking_pickle+"','"+v.lapangan+"','"+v.tgl_main+"','"+v.jam_main+"','"+data.data_jadwal_terpilih.nama+"','"+v.id_identitas_order_pickle+"','"+v.id_transaksi+"'"+')">Cancel</button>';
                var tombol_action_selesai = ' <button class="btn btn-outline-success btn-xs" type="button" onclick="selesai_main_pickle('+"'"+ v.id_detail_booking_pickle + "','"+ v.tgl_main + "','"+ v.jam_main + "','"+ v.id_identitas_order + "','"+ v.lapangan + "'"+')">Visit</button>';



                  if (v.status=='Booking') {
                     var tbl_action = tombol_action_cancel + tombol_action_selesai;

                  }else{
                     var tbl_action = '';

                  }
                  var isi = `<tr>
                    <td>`+(k+1)+`</td>
                    <td>Lapangan `+v.lapangan+`</td>
                    <td>`+format_tanggal(v.tgl_main)+`</td>
                    <td>`+v.jam_main+`</td>
                    <td>`+status+`</td>
                    <td>`+tbl_action+`</td>
                  </tr>`
                $('#cek_lapangan_pickle').find('#list_jadwal_pickle').append(isi);
                })
                


              },
              error : function (){
                alert('ee');
              }
      });


}



  function cancel_pickle(id_booking, lapangan, tgl_main, jam_main, nama, id_identitas_order, id_transaksi, id_booking_new, jenis_pickle){
    Swal.fire({
        title: 'Cancel ?',
        text: 'Apakah anda akan mencancel booking pickle atas nama '+ nama+ ' tanggal '+ format_tanggal(tgl_main)+' jam '+jam_main +'.?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Cancel',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
              url     : '<?php echo base_url() ?>/user/gro/booking/cancel_pickle',
              type: 'POST',
              // dataType: 'JSON',
              data: {    
                id_booking : id_booking,
                tgl_main : tgl_main, 
                jam_main : jam_main, 
                id_identitas_order : id_identitas_order, 
                id_transaksi : id_transaksi, 
              },
              success: function(data) {
                if (jenis_pickle=='harian') {
                  identitas_jadwal_pickle_harian(id_booking_new);

                }else{
                  identitas_jadwal_pickle_turnamen(id_booking_new);

                }
                Swal.fire('Diperbaharui','Jadwal pickle telah di cancel','success');
               
                // detail_jam_booking_pickle(id_booking, id_identitas_order)
                 show_peta_booking_pickle(lapangan);

              },
              error: function(jqXHR, textStatus, errorThrown) {
                alert('error');
              }
          });

        
        }
      });
   
  }

  function cek_jadwal_pickle(id_booking, jenis){
    $('#cek_jadwal_pickle').modal('show');
    $('#cek_jadwal_pickle').find('#id_identias_order').val(id_booking);

    if (jenis=='harian') {
      identitas_jadwal_pickle_harian(id_booking);
    }
    else if (jenis=='bulanan') {
      identitas_jadwal_pickle_bulanan(id_booking);
    }
    else {
      identitas_jadwal_pickle_turnamen(id_booking);
    }
   
  }
  function cek_lapangan_pickle(id_jadwal, id_booking){
    $('#cek_lapangan_pickle').modal('show');
    $('#cek_lapangan_pickle').find('#id_jadwal').val(id_booking);
    $('#cek_lapangan_pickle').find('#id_identias_order').val(id_booking);
    detail_jam_booking_pickle(id_jadwal, id_booking);
   
  }


function identitas_jadwal_pickle_harian(id_booking){
        $('#form_tambah_metode_pembayaran_pickle').html(``);

        
   $('#cek_jadwal_pickle').find('#list_jadwal_pickle').html('');
     $.ajax(
            {
              url     : '<?php echo base_url() ?>/user/gro/booking/cek_jadwal_pickle_harian',
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
                id_booking : id_booking,
              },
              success : function(data)
              {
                $('#cek_jadwal_pickle').find('#opsi_tambah_metode_pembayaran').val("");
                $('#cek_jadwal_pickle').find('#fasilitas').html("<br>"+data.data_pelanggan.fasilitas);
                $('#cek_jadwal_pickle').find('#nama').html(data.data_pelanggan.nama);
                $('#cek_jadwal_pickle').find('#alamat').html(data.data_pelanggan.alamat);
                $('#cek_jadwal_pickle').find('#nohp').html(data.data_pelanggan.no_hp);
                $('#cek_jadwal_pickle').find('#keterangan').html(data.data_pelanggan.keterangan);
                var total_biaya = 0;
                var jumlah_order = 0;
                var jumlah_booking = 0;
                $.each(data.data_jadwal, function(k,v){
                  if (v.status!='Cancel') {
                  total_biaya +=parseInt(v.biaya);
                    jumlah_order++;
                    if (v.status=='Booking') {
                    jumlah_booking++;

                    var tombol_action = `<a href="javascript:void(0)" onclick="selesai_main_pickle('`+v.id_detail_booking_pickle+`','`+v.tgl_main+`','`+v.jam_main+`','`+id_booking+`','`+v.lapangan+`')">Cancel</a>`;
                    var tombol_action = `<a href="javascript:void(0)" onclick="cancel_pickle('`+v.id_detail_booking_pickle+`', '`+v.lapangan+`', '`+v.tgl_main+`', '`+v.jam_main+`', '`+data.data_pelanggan.nama+`',  '`+v.id_identitas_order_pickle+`',  '`+v.id_transaksi_sementara+`','`+id_booking+`','harian')">Cancel</a>`;
                    }else{
                    var tombol_action = '';

                    }
                  }else{
                  var tombol_action = '';

                  }
                  var isi = `<tr>
                    <td>`+(k+1)+`</td>
                    <td>Lapangan `+v.lapangan+`</td>
                    <td>`+format_tanggal(v.tgl_main)+`</td>
                    <td>`+v.jam_main+`</td>
                    <td>`+v.status+`</td>
                    <td>`+number_format(v.biaya)+`</td>
                    <td>`+tombol_action+`</td>
                  </tr>`
                $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(isi);
                });
                

                  var footer_total = `<tr>
                    <td colspan="5">Total</td>
                    <td colspan="2">`+number_format(total_biaya)+`</td>
                  </tr>`
                $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_total);



                 var kategori_potongan= data.data_transaksi.kategori_potongan;
                  var besar_diskon = data.data_transaksi.besar_diskon
                if (kategori_potongan=='Kerjasama' || kategori_potongan=='Diskon') {
                                      if (data.data_transaksi.jenis_potongan=='Persentase') {
                                          var potongan   = total_biaya  * (besar_diskon / 100);
                                         var show_potongan = data.data_transaksi.besar_diskon + '% ['+number_format(potongan)+']';
                                      }else{
                                        var potongan = data.data_transaksi.besar_diskon;
                                        var show_potongan = number_format(potongan);

                                      }

                                      var caption_potongan = 'Diskon<br><small>' + kategori_potongan + ' - ' + data.data_transaksi.nama_diskon + '</small>';
                                    }else if (kategori_potongan=='Student Card') {
                                      if (data.data_transaksi.jenis_potongan=='Persentase') {
                                          var potongan   = total_biaya  * (besar_diskon / 100);
                                          // var potongan = data.data_transaksi.rp_nilai_diskon;
                                          var show_potongan = data.data_transaksi.besar_diskon + '% [' + number_format(potongan) + ']';
                                      }else{
                                          var potongan   = besar_diskon ;
                                          // var potongan = data.data_transaksi.besar_diskon ;
                                          var show_potongan = number_format(potongan);

                                      }
                                      var caption_potongan = 'Diskon '+ kategori_potongan+ '<br><small>'+ data.data_transaksi.nama_diskon + '</small>';
                                    }else{
                                      var potongan =0;
                                      var show_potongan ='';
                                      var caption_potongan = '';
                                    }

                    var grand_total = total_biaya - potongan ;


                  if (jumlah_order>0) {
                    if (kategori_potongan!='') {
                      var footer_potongan = `<tr>
                        <td colspan="5">`+caption_potongan  +`</td>
                        <td colspan="2">`+show_potongan +`</td>
                      </tr>`;
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_potongan);
                      var footer_tagihan = `<tr>
                        <td colspan="5">Grand Total</td>
                        <td colspan="2">`+number_format(grand_total) +`</td>
                      </tr>`;
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_tagihan);
                      var footer_dp = `<tr>
                        <td colspan="5">DP</td>
                        <td colspan="2">`+number_format(data.data_transaksi.dp) +`</td>
                      </tr>`;


                      var sisa_pembayaran = parseInt(grand_total) - parseInt(data.data_transaksi.dp);
                      var footer_sisa = `<tr>
                        <td colspan="5">Sisa</td>
                        <td colspan="2">`+number_format(sisa_pembayaran) +`</td>
                      </tr>`;

                    }else{
                    
                      var footer_tagihan = `<tr>
                        <td colspan="5">Grand Total</td>
                        <td colspan="2">`+number_format(grand_total) +`</td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_tagihan);
                      var footer_dp = `<tr>
                        <td colspan="5" id="caption_dp">DP</td>
                        <td colspan="2">`+number_format(data.data_transaksi.dp) +`</td>
                      </tr>`;


                      var sisa_pembayaran = parseInt(grand_total) - parseInt(data.data_transaksi.dp);
                      var footer_sisa = `<tr>
                        <td colspan="5">Sisa</td>
                        <td colspan="2">`+number_format(sisa_pembayaran) +`</td>
                      </tr>`;

                    }
                    if (jumlah_booking>0) {

                      if (sisa_pembayaran>0) {
                        $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_dp);
                        // $('#cek_jadwal_pickle').find('#caption_dp').html('DP');
                        $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_sisa);
                      }else{
                        if (data.data_transaksi.sudah_dibayar>0) {
                            var footer_gt = `<tr>
                          <td colspan="5" id="caption_dp">Telah Dibayar</td>
                          <td colspan="2">`+number_format(data.data_transaksi.sudah_dibayar ) +`</td>
                        </tr>`;

                        }else{
                            var footer_gt = `<tr>
                          <td colspan="5" id="caption_dp">Telah Dibayar (DP)</td>
                          <td colspan="2">`+number_format(data.data_transaksi.dp ) +`</td>
                        </tr>`;

                        }




                        $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_gt);
                        // $('#cek_jadwal_pickle').find('#caption_dp').html('Dibayar');

                      }
                      var footer_visit = `<tr>
                        <td colspan="7"><button class="btn btn-block btn-info" type="button" onclick="konfirmasi_pembayaran_pickle('`+data.data_pelanggan.id_transaksi_sementara+`','harian','`+data.data_transaksi.metode_pembayaran+`')">Visit</button></td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_visit);
                    }else{  
                      var footer_Print = `<tr>
                        <td colspan="7"><button class="btn btn-info" type="button" onclick="print_transaksi_pickle('`+data.data_pelanggan.id_transaksi+`','harian')">Print Struk</button></td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_Print);

                    }

                      $('#cek_jadwal_pickle').find('#id_booking').val(id_booking);
                      $('#cek_jadwal_pickle').find('#total').val(total_biaya);
                      $('#cek_jadwal_pickle').find('#tagihan').val(grand_total);
                      $('#cek_jadwal_pickle').find('#dp').val(data.data_transaksi.dp);
                      $('#cek_jadwal_pickle').find('#sisa').val(sisa_pembayaran);
                      $('#cek_jadwal_pickle').find('#simpan_dibayar').val(data.data_transaksi.sudah_dibayar);
                      $('#cek_jadwal_pickle').find('#simpan_kembalian').val(0);
                    
                      // $('#cek_jadwal_pickle').find('#id_student_card').val(sisa_pembayaran);
                      $('#cek_jadwal_pickle').find('#kategori_diskon').val(data.data_transaksi.kategori_potongan);
                      $('#cek_jadwal_pickle').find('#id_diskon').val(data.data_transaksi.id_diskon);
                      $('#cek_jadwal_pickle').find('#nama_diskon').val(data.data_transaksi.nama_diskon);
                      $('#cek_jadwal_pickle').find('#jenis_potongan').val(data.data_transaksi.jenis_potongan);
                      $('#cek_jadwal_pickle').find('#besar_potongan').val(data.data_transaksi.besar_diskon);
                      $('#cek_jadwal_pickle').find('#rp_nilai_potongan').val(data.data_transaksi.rp_nilai_diskon );
                      $('#cek_jadwal_pickle').find('#id_transaksi_sementara').val(data.data_transaksi.id_transaksi );
                      $('#cek_jadwal_pickle').find('#simpan_nama').val(data.data_pelanggan.nama);
                      $('#cek_jadwal_pickle').find('#id_identias_order').val(id_booking);



                    }



              },
              error : function (){
                alert('ee');
              }
      });


}


function identitas_jadwal_pickle_turnamen(id_booking){

        $('#form_tambah_metode_pembayaran_pickle').html(``);

   $('#cek_jadwal_pickle').find('#list_jadwal_pickle').html('');
     $.ajax(
            {
              url     : '<?php echo base_url() ?>/user/gro/booking/cek_jadwal_pickle_turnamen',
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
                id_booking : id_booking,
              },
              success : function(data)
              {
                $('#cek_jadwal_pickle').find('#fasilitas').html("<br>"+data.data_pelanggan.fasilitas);
                $('#cek_jadwal_pickle').find('#nama').html(data.data_pelanggan.nama);
                $('#cek_jadwal_pickle').find('#alamat').html(data.data_pelanggan.alamat);
                $('#cek_jadwal_pickle').find('#nohp').html(data.data_pelanggan.no_hp);
                $('#cek_jadwal_pickle').find('#keterangan').html(data.data_pelanggan.keterangan);

                $('#cek_jadwal_pickle').find('#nama_paket').html('<br>Paket : '+data.data_pelanggan.paket);
                var total_biaya = 0;
                var jumlah_order = 0;
                var jumlah_booking = 0;
                var no=1;
                $.each(data.data_jadwal, function(k,v){
                  if (v.status!='Cancel') {
                  total_biaya +=parseInt(v.biaya);
                    jumlah_order++;
                    if (v.status=='Booking') {
                    jumlah_booking++;

                    var tombol_action = `<a href="javascript:void(0)" onclick="selesai_main_pickle('`+v.id_detail_booking_pickle+`','`+v.tgl_main+`','`+v.jam_main+`','`+id_booking+`','`+v.lapangan+`')">Cancel</a>`;
                    var tombol_action = `<a href="javascript:void(0)" onclick="cancel_pickle('`+v.id_detail_booking_pickle+`', '`+v.lapangan+`', '`+v.tgl_main+`', '`+v.jam_main+`', '`+data.data_pelanggan.nama+`',  '`+v.id_identitas_order_pickle+`',  '`+v.id_transaksi_sementara+`','`+id_booking+`','turnamen')">Cancel</a>`;
                    }else{
                    var tombol_action = '';

                    }
                  }else{
                  var tombol_action = '';

                  }
                  var isi = `<tr>
                    <td>`+(no++)+`</td>
                    <td>Lapangan `+v.lapangan+`</td>
                    <td>`+format_tanggal(v.tgl_main)+`</td>
                    <td>`+v.jam_main+`</td>
                    <td>`+v.status+`</td>
                    <td>`+number_format(v.biaya)+`</td>
                    <td>`+tombol_action+`</td>
                  </tr>`
                $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(isi);
                });
                  $.each(data.data_biaya_tambahan, function(k,v){
                  total_biaya +=parseInt(v.nilai);
                 
                  var isi = `<tr>
                    <td>`+(no++)+`</td>
                    <td colspan="4">`+v.item_transaksi+`</td>
                    <td>`+number_format(v.nilai)+`</td>
                    <td></td>
                  </tr>`
                $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(isi);
                });

                  var footer_total = `<tr>
                    <td colspan="5">Total</td>
                    <td colspan="2">`+number_format(total_biaya)+`</td>
                  </tr>`
                $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_total);



                 var kategori_potongan= data.data_transaksi.kategori_potongan;
                  var besar_diskon = data.data_transaksi.besar_diskon
                if (kategori_potongan=='Kerjasama' || kategori_potongan=='Diskon') {
                                      if (data.data_transaksi.jenis_potongan=='Persentase') {
                                          var potongan   = total_biaya  * (besar_diskon / 100);
                                         var show_potongan = data.data_transaksi.besar_diskon + '% ['+number_format(potongan)+']';
                                      }else{
                                        var potongan = data.data_transaksi.besar_diskon;
                                        var show_potongan = number_format(potongan);

                                      }

                                      var caption_potongan = 'Diskon<br><small>' + kategori_potongan + ' - ' + data.data_transaksi.nama_diskon + '</small>';
                                    }else if (kategori_potongan=='Student Card') {
                                      if (data.data_transaksi.jenis_potongan=='Persentase') {
                                          var potongan   = total_biaya  * (besar_diskon / 100);
                                          // var potongan = data.data_transaksi.rp_nilai_diskon;
                                          var show_potongan = data.data_transaksi.besar_diskon + '% [' + number_format(potongan) + ']';
                                      }else{
                                          var potongan   = besar_diskon ;
                                          // var potongan = data.data_transaksi.besar_diskon ;
                                          var show_potongan = number_format(potongan);

                                      }
                                      var caption_potongan = 'Diskon '+ kategori_potongan+ '<br><small>'+ data.data_transaksi.nama_diskon + '</small>';
                                    }else{
                                      var potongan =0;
                                      var show_potongan ='';
                                      var caption_potongan = '';
                                    }

                    var grand_total = total_biaya - potongan ;


                  if (jumlah_order>0) {
                    if (kategori_potongan!='') {
                      var footer_potongan = `<tr>
                        <td colspan="5">`+caption_potongan  +`</td>
                        <td colspan="2">`+show_potongan +`</td>
                      </tr>`;
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_potongan);
                      var footer_tagihan = `<tr>
                        <td colspan="5">Grand Total</td>
                        <td colspan="2">`+number_format(grand_total) +`</td>
                      </tr>`;
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_tagihan);
                      var footer_dp = `<tr>
                        <td colspan="5">DP</td>
                        <td colspan="2">`+number_format(data.data_transaksi.dp) +`</td>
                      </tr>`;


                      var sisa_pembayaran = parseInt(grand_total) - parseInt(data.data_transaksi.dp);
                      var footer_sisa = `<tr>
                        <td colspan="5">Sisa</td>
                        <td colspan="2">`+number_format(sisa_pembayaran) +`</td>
                      </tr>`;

                    }else{
                    
                      var footer_tagihan = `<tr>
                        <td colspan="5">Grand Total</td>
                        <td colspan="2">`+number_format(grand_total) +`</td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_tagihan);
                      var footer_dp = `<tr>
                        <td colspan="5" id="caption_dp">DP</td>
                        <td colspan="2">`+number_format(data.data_transaksi.dp) +`</td>
                      </tr>`;


                      var sisa_pembayaran = parseInt(grand_total) - parseInt(data.data_transaksi.dp);
                      var footer_sisa = `<tr>
                        <td colspan="5">Sisa</td>
                        <td colspan="2">`+number_format(sisa_pembayaran) +`</td>
                      </tr>`;

                    }
                    if (jumlah_booking>0) {

                      if (sisa_pembayaran>0) {
                        $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_dp);
                        // $('#cek_jadwal_pickle').find('#caption_dp').html('DP');
                        $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_sisa);
                      }else{


                        if (data.data_transaksi.sudah_dibayar>0) {
                            var footer_gt = `<tr>
                          <td colspan="5" id="caption_dp">Telah Dibayar</td>
                          <td colspan="2">`+number_format(data.data_transaksi.sudah_dibayar ) +`</td>
                        </tr>`;

                        }else{
                            var footer_gt = `<tr>
                          <td colspan="5" id="caption_dp">Telah Dibayar (DP)</td>
                          <td colspan="2">`+number_format(data.data_transaksi.dp ) +`</td>
                        </tr>`;

                        }


                        


                        $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_gt);
                        // $('#cek_jadwal_pickle').find('#caption_dp').html('Dibayar');

                      }
                      var footer_visit = `<tr>
                        <td colspan="7"><button class="btn btn-block btn-info" type="button" onclick="konfirmasi_pembayaran_pickle('`+data.data_pelanggan.id_transaksi_sementara+`','turnamen','`+data.data_transaksi.metode_pembayaran+`')">Visit</button></td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_visit);
                    }else{  
                      var footer_Print = `<tr>
                        <td colspan="7"><button class="btn btn-info" type="button" onclick="print_transaksi_pickle_turnamen('`+data.data_pelanggan.id_transaksi+`','turnamen')">Print Struk</button></td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_Print);

                    }


                      $('#cek_jadwal_pickle').find('#id_booking').val(id_booking);
                      $('#cek_jadwal_pickle').find('#total').val(total_biaya);
                      $('#cek_jadwal_pickle').find('#tagihan').val(grand_total);
                      $('#cek_jadwal_pickle').find('#dp').val(data.data_transaksi.dp);
                      $('#cek_jadwal_pickle').find('#sisa').val(sisa_pembayaran);
                      $('#cek_jadwal_pickle').find('#simpan_dibayar').val(data.data_transaksi.sudah_dibayar);
                      $('#cek_jadwal_pickle').find('#simpan_kembalian').val(0);
                    
                      // $('#cek_jadwal_pickle').find('#id_student_card').val(sisa_pembayaran);
                      $('#cek_jadwal_pickle').find('#kategori_diskon').val(data.data_transaksi.kategori_potongan);
                      $('#cek_jadwal_pickle').find('#id_diskon').val(data.data_transaksi.id_diskon);
                      $('#cek_jadwal_pickle').find('#nama_diskon').val(data.data_transaksi.nama_diskon);
                      $('#cek_jadwal_pickle').find('#jenis_potongan').val(data.data_transaksi.jenis_potongan);
                      $('#cek_jadwal_pickle').find('#besar_potongan').val(data.data_transaksi.besar_diskon);
                      $('#cek_jadwal_pickle').find('#rp_nilai_potongan').val(data.data_transaksi.rp_nilai_diskon );
                      $('#cek_jadwal_pickle').find('#id_transaksi_sementara').val(data.data_transaksi.id_transaksi );
                      $('#cek_jadwal_pickle').find('#simpan_nama').val(data.data_pelanggan.nama);
                      $('#cek_jadwal_pickle').find('#id_identias_order').val(id_booking);



                    }



              },
              error : function (){
                alert('ee');
              }
      });


}



function identitas_jadwal_pickle_bulanan(id_booking){
   $('#cek_jadwal_pickle').find('#list_jadwal_pickle').html('');
    // $('#list_rekap_pickle_bulanan').DataTable().ajax.reload(null, false);
     $.ajax(
            {
              url     : '<?php echo base_url() ?>/user/gro/booking/cek_jadwal_pickle_bulanan',
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
                id_booking : id_booking,
              },
              success : function(data)
              {
                $('#cek_jadwal_pickle').find('#fasilitas').html("<br>"+data.data_pelanggan.fasilitas);
                $('#cek_jadwal_pickle').find('#nama').html(data.data_pelanggan.nama);
                $('#cek_jadwal_pickle').find('#alamat').html(data.data_pelanggan.alamat);
                $('#cek_jadwal_pickle').find('#nohp').html(data.data_pelanggan.no_hp);
                $('#cek_jadwal_pickle').find('#keterangan').html(data.data_pelanggan.keterangan);
                var total_biaya = 0;
                var total_biaya_after = 0;
                var jumlah_booking = 0;
                $.each(data.data_jadwal, function(k,v){
                  total_biaya +=parseInt(v.biaya);
                  if (v.status=='Booking') {
                  jumlah_booking++;   



                  var tombol_ganti_jadwal = `<a href="javascript:void(0)" class="btn btn-warning btn-sm" onclick="ganti_jadwal_pickle('`+v.id_detail_booking_badminton+`','`+v.tgl_main+`','`+v.jam_main+`','`+id_booking+`','`+v.biaya+`')">Ganti Jadwal</a>`;


                  if (v.biaya_perubahan=='') {
                    var tombol_visit = `<a href="javascript:void(0)"  class="btn btn-success btn-sm" onclick="visit_pickle_bulanan('`+v.id_detail_booking_badminton+`','`+v.tgl_main+`','`+v.jam_main+`','`+id_booking+`','`+data.data_pelanggan.nama+`','`+v.lapangan+`','visit')">Visit</a>`;

                      var show_biaya = number_format(v.biaya);
                      total_biaya_after += parseInt(v.biaya);

                  }else{
                    if (v.biaya_perubahan==v.biaya) {
                      var tombol_visit = `<a href="javascript:void(0)"  class="btn btn-success btn-sm" onclick="visit_pickle_bulanan('`+v.id_detail_booking_badminton+`','`+v.tgl_main+`','`+v.jam_main+`','`+id_booking+`','`+data.data_pelanggan.nama+`','`+v.lapangan+`','visit')">Visit</a>`;
                      var show_biaya = number_format(v.biaya);
                      total_biaya_after += parseInt(v.biaya);

                    }else{
                      var tombol_visit = `<a href="javascript:void(0)"  class="btn btn-danger btn-sm" onclick="alert('ketika biaya beda maka ada penambahan biaya dan pengembalian dana')">Visit</a>`;
                      var tombol_visit = `<a href="javascript:void(0)"  class="btn btn-info btn-sm" onclick="visit_pickle_bulanan('`+v.id_detail_booking_badminton+`','`+v.tgl_main+`','`+v.jam_main+`','`+id_booking+`','`+data.data_pelanggan.nama+`','`+v.lapangan+`','konfirmasi')">Visit</a>`;
                      var show_biaya = '<span style="text-decoration:line-through">'+number_format(v.biaya)+'</span><br>' + number_format(v.biaya_perubahan);
                      total_biaya_after += parseInt(v.biaya_perubahan);

                    }

                  }


                  var tgl_sekarang = '<?php echo $tgl ?>';
                    if (v.tgl_main<tgl_sekarang) {
                      var tombol_visit_aktif =  `<a href="javascript:void(0)"  class="btn btn-danger btn-sm" onclick="Swal.fire('Expired','Jadwal main sudah terlewat.','error')">Visit</a>`;
                    }else{
                      var tombol_visit_aktif = tombol_visit;

                    }
                      tombol_action = tombol_ganti_jadwal +' '+ tombol_visit_aktif  ;
                  }else{
                  var tombol_action = 'Sudah Visit';
                      var show_biaya = number_format(v.biaya);
                      total_biaya_after += parseInt(v.biaya);

                  }


                   


                  var isi = `<tr>
                    <td>`+(k+1)+`</td>
                    <td>Jadwal Ke : `+v.jadwal_ke+`<br>Lapangan `+v.lapangan+`</td>
                    <td>`+format_tanggal(v.tgl_main)+`</td>
                    <td>`+v.jam_main+`</td>
                    <td>`+v.status+`</td>
                    <td>`+show_biaya+`</td>
                    <td>`+tombol_action+`</td>
                  </tr>`
                $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(isi);
                });
                


                if (total_biaya==total_biaya_after) {
                  var footer_total = `<tr>
                    <td colspan="5">Total</td>
                    <td colspan="2">`+number_format(total_biaya)+`</td>
                  </tr>`
                  
                }else{
                  var footer_total = `<tr>
                    <td colspan="5">Total</td>
                    <td colspan="2"><span style="text-decoration:line-through">`+total_biaya+`</span><br>`+total_biaya_after+`</td>
                  </tr>`

                }
                $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_total);



                 var kategori_potongan= data.data_transaksi.kategori_potongan;
                  var besar_diskon = data.data_transaksi.besar_diskon
                if (kategori_potongan=='Kerjasama' || kategori_potongan=='Diskon') {
                                      if (data.data_transaksi.jenis_potongan=='Persentase') {
                                          var potongan   = total_biaya  * (besar_diskon / 100);
                                         var show_potongan = data.data_transaksi.besar_diskon + '% ['+number_format(potongan)+']';
                                      }else{
                                        var potongan = data.data_transaksi.besar_diskon;
                                        var show_potongan = number_format(potongan);

                                      }

                                      var caption_potongan = 'Diskon<br><small>' + kategori_potongan + ' - ' + data.data_transaksi.nama_diskon + '</small>';
                                    }else if (kategori_potongan=='Student Card') {
                                      if (data.data_transaksi.jenis_potongan=='Persentase') {
                                          var potongan   = total_biaya  * (besar_diskon / 100);
                                          // var potongan = data.data_transaksi.rp_nilai_diskon;
                                          var show_potongan = data.data_transaksi.besar_diskon + '% [' + number_format(potongan) + ']';
                                      }else{
                                          var potongan   = besar_diskon ;
                                          // var potongan = data.data_transaksi.besar_diskon ;
                                          var show_potongan = number_format(potongan);

                                      }
                                      var caption_potongan = 'Diskon '+ kategori_potongan+ '<br><small>'+ data.data_transaksi.nama_diskon + '</small>';
                                    }else{
                                      var potongan =0;
                                      var show_potongan ='';
                                      var caption_potongan = '';
                                    }

                    var grand_total = total_biaya - potongan ;


                  if (jumlah_booking>0) {
                    if (kategori_potongan!='') {
                      var footer_potongan = `<tr>
                        <td colspan="5">`+caption_potongan  +`</td>
                        <td colspan="2">`+show_potongan +`</td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_potongan);
                      var footer_tagihan = `<tr>
                        <td colspan="5">Grand Total</td>
                        <td colspan="2">`+number_format(grand_total) +`</td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_tagihan);
                      var footer_dp = `<tr>
                        <td colspan="5">DP</td>
                        <td colspan="2">`+number_format(data.data_transaksi.dp) +`</td>
                      </tr>`
                      // $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_dp);


                      var sisa_pembayaran = parseInt(grand_total) - parseInt(data.data_transaksi.dp);
                      var footer_sisa = `<tr>
                        <td colspan="5">Sisa</td>
                        <td colspan="2">`+number_format(sisa_pembayaran) +`</td>
                      </tr>`
                      // $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_sisa);

                    }else{
                    
                      var footer_tagihan = `<tr>
                        <td colspan="5">Grand Total</td>
                        <td colspan="2">`+number_format(grand_total) +`</td>
                      </tr>`
                      $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_tagihan);
                      var footer_dp = `<tr>
                        <td colspan="5">DP</td>
                        <td colspan="2">`+number_format(data.data_transaksi.dp) +`</td>
                      </tr>`
                      // $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_dp);


                      var sisa_pembayaran = parseInt(grand_total) - parseInt(data.data_transaksi.dp);
                      var footer_sisa = `<tr>
                        <td colspan="5">Sisa</td>
                        <td colspan="2">`+number_format(sisa_pembayaran) +`</td>
                      </tr>`
                      // $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_sisa);

                    }


                      $('#cek_jadwal_pickle').find('#id_booking').val(id_booking);
                      $('#cek_jadwal_pickle').find('#total').val(total_biaya);
                      $('#cek_jadwal_pickle').find('#tagihan').val(grand_total);
                      $('#cek_jadwal_pickle').find('#dp').val(data.data_transaksi.dp);
                      $('#cek_jadwal_pickle').find('#sisa').val(sisa_pembayaran);
                      $('#cek_jadwal_pickle').find('#simpan_dibayar').val(sisa_pembayaran);
                      $('#cek_jadwal_pickle').find('#simpan_kembalian').val(0);
                    
                      // $('#cek_jadwal_pickle').find('#id_student_card').val(sisa_pembayaran);
                      $('#cek_jadwal_pickle').find('#kategori_diskon').val(data.data_transaksi.kategori_potongan);
                      $('#cek_jadwal_pickle').find('#id_diskon').val(data.data_transaksi.id_diskon);
                      $('#cek_jadwal_pickle').find('#nama_diskon').val(data.data_transaksi.nama_diskon);
                      $('#cek_jadwal_pickle').find('#jenis_potongan').val(data.data_transaksi.jenis_potongan);
                      $('#cek_jadwal_pickle').find('#besar_potongan').val(data.data_transaksi.besar_diskon);
                      $('#cek_jadwal_pickle').find('#rp_nilai_potongan').val(data.data_transaksi.rp_nilai_diskon );
                      $('#cek_jadwal_pickle').find('#id_transaksi_sementara').val(data.data_transaksi.id_transaksi );
                      $('#cek_jadwal_pickle').find('#simpan_nama').val(data.data_pelanggan.nama);
                      $('#cek_jadwal_pickle').find('#id_identias_order').val(id_booking);


                      var footer_visit = `<tr>
                        <td colspan="7"><button class="btn btn-block btn-info" type="button" onclick="konfirmasi_pembayaran_pickle('`+data.data_pelanggan.id_transaksi_sementara+`','bulanan')">Visit</button></td>
                      </tr>`
                      // $('#cek_jadwal_pickle').find('#list_jadwal_pickle').append(footer_visit);

                    }



              },
              error : function (){
                alert('ee');
              }
      });


}

function ganti_jadwal_pickle(id_detail_booking_pickle, tgl_main, jam_main, id_booking, biaya_sebelumnya){
  pilih_jadwal_pickle(id_detail_booking_pickle, tgl_main, jam_main, id_booking, biaya_sebelumnya);

      var lapangan = '<?php echo json_encode($lapangan_pickle) ?>';
      var json_lapangan = JSON.parse(lapangan);
      $('#jadwal_pickle_bulanan').find('#lapangan').html('');
      $.each(json_lapangan, function (k,v){

      $('#jadwal_pickle_bulanan').find('#lapangan').append('<option value="'+v.lapangan+'">Lapangan '+v.lapangan+'</option>');

      })
}



    function pilih_jadwal_pickle(id_detail_booking_pickle, tgl_main, jam_main, id_booking, biaya_sebelumnya){

      $('#jadwal_pickle_bulanan').modal('show');
      $('#jadwal_pickle_bulanan').find('#tgl_main').val(tgl_main).change();
      $('#jadwal_pickle_bulanan').find('#id_jadwal').val(id_detail_booking_pickle);
      $('#jadwal_pickle_bulanan').find('#id_booking').val(id_booking);
      $('#jadwal_pickle_bulanan').find('#simpan_lama_main').val(0)
      $('#jadwal_pickle_bulanan').find('#total_biaya').val(0)
      $('#jadwal_pickle_bulanan').find('#biaya_sebelumnya').val(biaya_sebelumnya)

        $('#jadwal_pickle_bulanan').find('.biaya_total').html(number_format(0));
        $('#jadwal_pickle_bulanan').find('.biaya_perjam').html(number_format(0));

        $('#jadwal_pickle_bulanan').find('.lama_main').html('Pilih Jam Main');
        $('#jadwal_pickle_bulanan').find('#jam_main_dipilih').html("Pilih jam main");


     
        tampilkan_jadwal_main_pickle(jam_main, tgl_main);
    

      
      // $('#jadwal_pickle_bulanan').find('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Pilih tanggal dulu untuk menampilkan jam tersedia</div>');
      // $('#jadwal_pickle_bulanan').find('#jadwal_pickle_bulanan_ke').html(ke);
      // $('#jadwal_pickle_bulanan').find('#jadwal_ke').val(ke);



    $('#jadwal_pickle_bulanan').find('#tgl_main').change(function(){
      var tgls = '<?php echo date('Y-m-d') ?>';
      var tgl_dipilih = $('#jadwal_pickle_bulanan').find('#tgl_main').val();
      if (tgl_dipilih<tgls) {
        if (tgl_dipilih!='') {
          Swal.fire('Error','Harap pilih tanggal diatas tanggal hari ini','error');
        }
          $('#jadwal_pickle_bulanan').find('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Pilih tanggal dulu untuk menampilkan jam tersedia</div>');
      }else{
        tampilkan_jadwal_main_pickle(jam_main, tgl_main);

      }
      // hitung_jam(this);
    });



    // $('#jadwal_pickle_bulanan').find('#lapangan').change(function(){
    //   if ($('#jadwal_pickle_bulanan').find('#tgl_main').val()=='') {
    //       $('#jadwal_pickle_bulanan').find('#pilihan_jam').html('<div class="col-md-12 alert alert-info">Pilih tanggal dulu untuk menampilkan jam tersedia</div>');
    //   }else{
    //     tampilkan_jadwal_main_pickle();
    //   }
    // });



    }




    function tampilkan_jadwal_main_pickle(jam_main, tgl_main){

      var tgl_main = $('#jadwal_pickle_bulanan').find('#tgl_main').val();
      var lapangan = $('#jadwal_pickle_bulanan').find('#lapangan').val();


        $('#jadwal_pickle_bulanan').find('#jam_main_terpilih').html('<div class="col-md-12" style="color:blue">Tgl : '+tgl_main+' | Jam : '+jam_main+'</div>');
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
                    $('.show_jam_' +k ).append('<input type="radio" name="jam_main"  id="jam_main" class="checkbok_jam" value="'+v_jam.jam+'" style="margin-right:5px" onclick="memilih_jam_pickle_bulanan(this)" biaya="'+v_jam.harga+'" jam_main="'+v_jam.jam+'">'+v_jam.jam+'<br>');

                  })

                  $('#jadwal_pickle_bulanan').find('#pilihan_jam').append(`</div>`);
                });


              },
              error : function (){

              }
      });

   }



}


    function memilih_jam_pickle_bulanan(x){
      var jam_main = $(x).attr('jam_main');
      var biaya = $(x).attr('biaya');

      var value = $(x).val();
        $('#jadwal_pickle_bulanan').find('#jam_dipilih').val(value);
        $('#jadwal_pickle_bulanan').find('#total_biaya').val(biaya);
        var biaya_sebelumnya = $('#jadwal_pickle_bulanan').find('#biaya_sebelumnya').val();

        if (biaya==biaya_sebelumnya) {
          $('#jadwal_pickle_bulanan').find('#tombol_update_jadwal').attr('class','btn btn-block btn-info');
          $('#jadwal_pickle_bulanan').find('#tombol_update_jadwal').attr('onclick','set_jadwal_pickle()');

        }else{
          $('#jadwal_pickle_bulanan').find('#tombol_update_jadwal').attr('class','btn btn-block btn-danger');
          $('#jadwal_pickle_bulanan').find('#tombol_update_jadwal').attr('onclick','Swal.fire("Error","Anda tidak boleh memilih jadwal dengan harga yang berbeda","error")');

        }

    }


  function set_jadwal_pickle(){

        var tgl_main = $('#jadwal_pickle_bulanan').find('#tgl_main').val();
        var input_jam_main = $('#jadwal_pickle_bulanan').find('#jam_dipilih').val();
        var lapangan = $('#jadwal_pickle_bulanan').find('#lapangan').val();
        var id_jadwal = $('#jadwal_pickle_bulanan').find('#id_jadwal').val();
        var id_booking = $('#jadwal_pickle_bulanan').find('#id_booking').val();
        var total_biaya = $('#jadwal_pickle_bulanan').find('#total_biaya').val();

        if (input_jam_main=='') {

                  Swal.fire('Error','Harap Pilih Jam Main','error');
        }else{     
          $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/pickle_bulanan/update_jadwal_main'),
              type    : 'POST',
              dataType : 'JSON',
              data    : {
                tgl_main : tgl_main, 
                jam_main : input_jam_main, 
                lapangan : lapangan, 
                id_jadwal : id_jadwal, 
                total_biaya : total_biaya, 
              }, 
              success : function(data)
              {


                  var tgl_main = $('#jadwal_pickle_bulanan').modal('hide');
                  identitas_jadwal_pickle_bulanan(id_booking);

                  <?php for ($lapangan=1; $lapangan <=3  ; $lapangan++) { ?>
                   show_peta_booking_pickle('<?php echo $lapangan ?>');
                 <?php } ?>


              },
              error : function (){
                alert('ee');
              }
      });
      }

}

function konfirmasi_pembayaran_pickle(id_transaksi_sementara, jenis, metode_pembayaran){
$('#konfirmasi_pembayaran_booking_pickle').find('#input_dibayar').val('');
$('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('onclick','simpan_visit_pickle_harian()');
$('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('onclick','simpan_visit_pickle_turnamen()');
$('#konfirmasi_pembayaran_booking_pickle').find('#show_kembalian').html('Rp. 0');
$('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('class','btn btn-block btn-success mt-3');
$('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('class','btn btn-block btn-info mt-3');




                      total_biaya = $('#cek_jadwal_pickle').find('#total').val();
                      grand_total = $('#cek_jadwal_pickle').find('#tagihan').val();
                      dp = $('#cek_jadwal_pickle').find('#dp').val();
                      dibayar = $('#cek_jadwal_pickle').find('#simpan_dibayar').val();
                      $('#konfirmasi_pembayaran_booking_pickle').find('.total_biaya').html("Rp. " + number_format(grand_total));

                      if (dp=='') {
                        sisa_pembayaran = dibayar -grand_total; //$('#cek_jadwal_pickle').find('#sisa').val();
                        $('#konfirmasi_pembayaran_booking_pickle').find('#caption_dp').html('Telah Dibayar<br>Melalui : '+ metode_pembayaran);
                      $('#konfirmasi_pembayaran_booking_pickle').find('.dp').html("Rp. " + number_format(dibayar));
                      $('#konfirmasi_pembayaran_booking_pickle').find('.sisa_pembayaran').html("Rp. " + number_format(sisa_pembayaran));
                      $('#konfirmasi_pembayaran_booking_pickle').find('#caption_sisa_pembayaran').html('Return');
                      $('#cek_jadwal_pickle').find('#jenis_kembalian').val('Return');
                        // $('#konfirmasi_pembayaran_booking_pickle').find('#input_dibayar').removeAttr('disabled');
                        $('#konfirmasi_pembayaran_booking_pickle').find('#input_dibayar').attr('disabled','disabled');
                        $('#konfirmasi_pembayaran_booking_pickle').find('.form_pembayaran_visit').attr('style','display:none');

                        $('#konfirmasi_pembayaran_booking_pickle').find('#show_dibayar').html("Rp. "+number_format(sisa_pembayaran));
                      }else{

                        if (parseInt(grand_total) < parseInt(dp)) {
                        $('#konfirmasi_pembayaran_booking_pickle').find('#caption_sisa_pembayaran').html('Return');
                        $('#cek_jadwal_pickle').find('#jenis_kembalian').val('Return');
                          sisa_pembayaran =  dp - grand_total; //$('#cek_jadwal_pickle').find('#sisa').val();
                          
                          $('#konfirmasi_pembayaran_booking_pickle').find('#caption_dp').html('Dibayar');
                          $('#cek_jadwal_pickle').find('#simpan_dibayar').val(dp);
                          $('#konfirmasi_pembayaran_booking_pickle').find('.dp').html("Rp. " + number_format(dp));
                          $('#konfirmasi_pembayaran_booking_pickle').find('.sisa_pembayaran').html("Rp. "+number_format(sisa_pembayaran));

                          $('#cek_jadwal_pickle').find('#simpan_kembalian').val(sisa_pembayaran);

                        $('#konfirmasi_pembayaran_booking_pickle').find('#input_dibayar').attr('disabled','disabled');
                        $('#konfirmasi_pembayaran_booking_pickle').find('#show_dibayar').html("Rp. 0");


                        }else{
                          $('#konfirmasi_pembayaran_booking_pickle').find('#caption_sisa_pembayaran').html('Sisa Pembayaran');

                          $('#cek_jadwal_pickle').find('#jenis_kembalian').val('Sisa Pembayaran');
                          sisa_pembayaran = grand_total - dp; //$('#cek_jadwal_pickle').find('#sisa').val();
                          
                          $('#konfirmasi_pembayaran_booking_pickle').find('#caption_dp').html('DP<br>Melalui : '+ metode_pembayaran);
                        $('#konfirmasi_pembayaran_booking_pickle').find('.form_pembayaran_visit').attr('style','');
                          $('#konfirmasi_pembayaran_booking_pickle').find('.dp').html("Rp. " + number_format(dp));
                          $('#konfirmasi_pembayaran_booking_pickle').find('.sisa_pembayaran').html("Rp. "+number_format(sisa_pembayaran));
                          $('#cek_jadwal_pickle').find('#simpan_dibayar').val(sisa_pembayaran);
                          $('#konfirmasi_pembayaran_booking_pickle').find('#show_dibayar').html("Rp. "+number_format(sisa_pembayaran));
                        $('#konfirmasi_pembayaran_booking_pickle').find('#input_dibayar').removeAttr('disabled');


                        }
                        
                      }
                      $('#konfirmasi_pembayaran_booking_pickle').find('#jenis_pickle').val(jenis);
                      if (jenis=='harian') {
                        $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').show();
                        $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').hide();
                      }else if (jenis=='turnamen'){
                        $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').hide();
                        $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').show();
                      }else{
                        $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').hide();
                        $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').hide();

                      }
                      $('#konfirmasi_pembayaran_booking_pickle').modal('show');

 $('#konfirmasi_pembayaran_booking_pickle').find('#input_dibayar').keyup(function(){
batal_tambah_metode_pembayaran_pickle();
  var dibayar_pelanggan =  $('#konfirmasi_pembayaran_booking_pickle').find('#input_dibayar').val();
  var tagihan = sisa_pembayaran;//$('#cek_jadwal_pickle').find('#tagihan').val();
   if (dibayar_pelanggan =='') {
    $('#konfirmasi_pembayaran_booking_pickle').find('#show_dibayar').html("Rp. "+ number_format(tagihan));
    $('#cek_jadwal_pickle').find('#simpan_dibayar').val(tagihan);
    $('#cek_jadwal_pickle').find('#simpan_kembalian').val(0);
    $('#konfirmasi_pembayaran_booking_pickle').find('#show_kembalian').html('Rp. 0');



    
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('class','btn btn-block btn-success mt-3');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('class','btn btn-block btn-info mt-3');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('onclick','simpan_visit_pickle_harian()');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('onclick','simpan_visit_pickle_turnamen()');
      batal_tambah_metode_pembayaran_pickle();
      $('#konfirmasi_pembayaran_booking_pickle').find('#opsi_tambah_metode_pembayaran').val(0);
      $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_tambah_metode_pembayaran').hide();
    }else{
        var cek_metode_pembayaran_2 = $('#cek_jadwal_pickle').find('#opsi_tambah_metode_pembayaran').val();
        if (cek_metode_pembayaran_2==1) {
          var input_dibayar_2 = $('#konfirmasi_pembayaran_booking_pickle').find('#input_dibayar_2').val();
          var sisa = (dibayar_pelanggan + input_dibayar_2 ) - tagihan;

        }else{
          var sisa = dibayar_pelanggan - tagihan;

        }
        $('#konfirmasi_pembayaran_booking_pickle').find('#show_kembalian').html("Rp. "+number_format(sisa));
        $('#konfirmasi_pembayaran_booking_pickle').find('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
        $('#cek_jadwal_pickle').find('#simpan_dibayar').val(dibayar_pelanggan);
        $('#cek_jadwal_pickle').find('#simpan_kembalian').val(sisa);
        if (sisa<0) {
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_tambah_metode_pembayaran').show();
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('class','btn btn-block btn-outline-danger mt-3');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('class','btn btn-block btn-outline-danger mt-3');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('onclick','Swal.fire("Error","Harap masukan nilai pembayaran dengan benar","error")');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('onclick','Swal.fire("Error","Harap masukan nilai pembayaran dengan benar","error")');

        }else{
              batal_tambah_metode_pembayaran_pickle();
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_tambah_metode_pembayaran').hide();
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('class','btn btn-block btn-success mt-3');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('class','btn btn-block btn-info mt-3');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_harian').attr('onclick','simpan_visit_pickle_harian()');
            $('#konfirmasi_pembayaran_booking_pickle').find('#tombol_visit_turnamen').attr('onclick','simpan_visit_pickle_turnamen()');
        }

    }








});



}


function simpan_visit_pickle_harian(){
  var formdata = $('#form_konfirmasi_booking_pickle').serialize();

            $.ajax({
              url     : '<?php echo base_url() ?>/user/gro/booking/simpan_visit_pickle_harian',
              type: 'POST',
              dataType: 'JSON',
              data: formdata,
              success: function(data) {
                   $('#konfirmasi_pembayaran_booking_pickle').modal('hide');
                   $('#cek_jadwal_pickle').modal('hide');
                  <?php for ($lapangan=1; $lapangan <=3  ; $lapangan++) { ?>
                   show_peta_booking_pickle('<?php echo $lapangan ?>');
                 <?php } ?>

                  print_transaksi_pickle(data.id_transaksi);
                  Swal.fire('Visited','Data visit pickle disimpan','success');
                  setTimeout(close_swal, 3000);


                // identitas_jadwal_pickle(id_booking);

                //  show_peta_booking_pickle(lapangan);
                // $('#cek_lapangan_pickle').modal('hide');

              },
              error: function(jqXHR, textStatus, errorThrown) {
              }
          });



}


function simpan_visit_pickle_turnamen(){
  var formdata = $('#form_konfirmasi_booking_pickle').serialize();

            $.ajax({
              url     : '<?php echo base_url() ?>/user/gro/booking/simpan_visit_pickle_turnamen',
              type: 'POST',
              dataType: 'JSON',
              data: formdata,
              success: function(data) {
                   $('#konfirmasi_pembayaran_booking_pickle').modal('hide');
                   $('#cek_jadwal_pickle').modal('hide');
                  <?php for ($lapangan=1; $lapangan <=3  ; $lapangan++) { ?>
                   show_peta_booking_pickle('<?php echo $lapangan ?>');
                 <?php } ?>

                  print_transaksi_pickle_turnamen(data.id_transaksi);
                  Swal.fire('Visited','Data visit pickle disimpan','success');
                  setTimeout(close_swal, 3000);


                // identitas_jadwal_pickle(id_booking);

                //  show_peta_booking_pickle(lapangan);
                // $('#cek_lapangan_pickle').modal('hide');

              },
              error: function(jqXHR, textStatus, errorThrown) {
              }
          });



}





  function visit_pickle_bulanan(id_jadwal, tgl,jam, id_booking, pelanggan, lapangan, jenis_visit){

    if (jenis_visit=='konfirmasi') {
      konfirmasi_visit_pickle_bulanan();
    }else{
     
    Swal.fire({
        title: 'Selesai ?',
        text: 'Selesai main pada '+format_tanggal(tgl)+' jam '+jam +' atas nama '+pelanggan+'.?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Selesai Main',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
              url     : '<?php echo base_url() ?>/user/gro/booking/selesai_main_pickle_bulanan',
              type: 'POST',
              // dataType: 'JSON',
              data: {    
                id_jadwal : id_jadwal,
              },
              success: function(data) {
                Swal.fire('Diperbaharui','Status jadwal pickle di perbaharui jadi sudah main','success');
                  
                  identitas_jadwal_pickle_bulanan(id_booking);

                 show_peta_booking_pickle(lapangan);
                $('#cek_lapangan_pickle').modal('hide');

              },
              error: function(jqXHR, textStatus, errorThrown) {
              }
          });

        
        }
      });


    }
  }

  function konfirmasi_visit_pickle_bulanan(){
    $('#konfirmasi_visit_pickle_bulanan').modal('show');
  }

  function selesai_main_pickle(id_jadwal, tgl,jam, id_booking, lapangan){
     
    Swal.fire({
        title: 'Selesai ?',
        text: 'Selesai main pada '+format_tanggal(tgl)+' jam '+jam +'.?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Selesai Main',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
              url     : '<?php echo base_url() ?>/user/gro/booking/selesai_main_pickle',
              type: 'POST',
              // dataType: 'JSON',
              data: {    
                id_jadwal : id_jadwal,
              },
              success: function(data) {
                Swal.fire('Diperbaharui','Status jadwal pickle di perbaharui jadi sudah main','success');
                identitas_jadwal_pickle(id_booking);

                 show_peta_booking_pickle(lapangan);
                $('#cek_lapangan_pickle').modal('hide');

              },
              error: function(jqXHR, textStatus, errorThrown) {
              }
          });

        
        }
      });


    
  }
  function selesai_main_semua_jadwal(){
     var id_booking = $('#cek_jadwal_pickle').find('#id_identias_order').val();
    Swal.fire({
        title: 'Selesai ?',
        text: 'Selesai main pada semia jadwal.?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Selesai Semua',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
              url     : '<?php echo base_url() ?>/user/gro/booking/selesai_main_pickle_semua_jadwal',
              type: 'POST',
              // dataType: 'JSON',
              data: {    
                id_booking : id_booking,
              },
              success: function(data) {
                Swal.fire('Diperbaharui','Status jadwal pickle di perbaharui jadi sudah main','success');
                identitas_jadwal_pickle(id_booking);
                 <?php for ($lapangan=1; $lapangan <=3  ; $lapangan++) { ?>
                 show_peta_booking_pickle('<?php echo $lapangan ?>');
               <?php } ?>




              },
              error: function(jqXHR, textStatus, errorThrown) {
                alert('error');
              }
          });

        
        }
      });


    
  }



  function print_transaksi_pickle(id_transaksi){
    $('#print_transaksi_gym').modal('show');
    $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
      <iframe src="`+ baseUrl('//user/gro/booking/print_visit_pickle_harian/') + id_transaksi +`/" width="100%" height="400px"></iframe>
      `);
  }
  function print_transaksi_pickle_turnamen(id_transaksi){
    $('#print_transaksi_gym').modal('show');
    $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
      <iframe src="`+ baseUrl('//user/gro/booking/print_visit_pickle_turnamen/') + id_transaksi +`/" width="100%" height="400px"></iframe>
      `);
  }
</script>
