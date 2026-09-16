
<div class="alert alert-info">
    Sewa Peralatan Fasilitas
</div>






<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">List Peralatan </div>
      <div class="card-body">
        <table class="table table-striped table-bordered data_tabel">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Peralatan</th>
              <th>Nama Alat</th>
              <th>Harga Sewa</th>
              <th>Masukan Jumlah Disewa</th>
              <th>Option</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no=1;
            foreach ($peralatan as $k => $v) { ?>
            <tr>
              <td><?php echo $no++ ?></td>
              <td><?php echo $v['kode_peralatan'] ?></td>
              <td><?php echo $v['nama_peralatan'] ?></td>
              <td><?php echo number_format($v['biaya_sewa']) ?></td>
              <td>
                <input type="text" id="qty" name="" class="form-control">
                <input type="hidden" id="kode_peralatan" name="" value="<?php echo $v['kode_peralatan'] ?>">
                <input type="hidden" id="id_peralatan" name="" value="<?php echo $v['id_peralatan'] ?>">
                <input type="hidden" id="nama_peralatan" name="" value="<?php echo $v['nama_peralatan'] ?>">
                <input type="hidden" id="biaya_sewa" name="" value="<?php echo $v['biaya_sewa'] ?>">
                <input type="hidden" id="id_akun_pendapatan" name="" value="<?php echo $v['id_akun_pendapatan'] ?>">
              </td>
              <td>
                <?php 
                if (in_array(3,$this->session->userdata('id_hak_akses')) ) { ?>
                   
                    <a href="javascript:void(0)"  onclick="masuk_ke_keranjang(this, '<?php echo $v['id_peralatan'] ?>')" class="btn btn-info btn-sm tombol_masuk_keranjang">Masuk Ke <br>Keranjang</a>
                   
                <?php }else{
                  echo "-";
              
                } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">Peralatan yang disewa</div>
      <div class="card-body" id="keranjang"></div>
      <div class="card-body" id="tombol_print"></div>
    </div>

<br>
    <div class="card" id="div_form_pelanggan" style="display: none">
      <div class="card-header">Identitas Penyewa</div>
      



      <div class="card-body">
        <form id="form_pelanggan">
          <div class="row">
                <input type="hidden" class="form-control" name="" id="total_biaya_sewa" value="0">
            <div class="col-md-6">
               <div class="form-group">
                <label>Tanggal Sewa</label>
                <input type="date" value="<?php echo date('Y-m-d') ?>" class="form-control" name="" id="tgl_penyewaan">
              </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                <label>Nama</label>
                <input type="text" class="form-control" name="" id="nama_penyewa">
              </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                <label>No HP</label>
                <input type="text" class="form-control" name="" id="nohp_penyewa">
              </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                <label>Alamat</label>
                <input type="text" class="form-control" name="" id="alamat_penyewa">
              </div>
            </div>
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-info btn-block" onclick="konfirmasi_sewa_peralatan()">Simpan</button>
          </div>
        </form>
      </div>




    </div>
    
  </div>
</div>


                          

  <script type="text/javascript">



  
    function tambah_metode_pembayaran(){
      $('#konfirmasi_sewa_peralatan').find('#opsi_tambah_metode_pembayaran').val(1);
      var metode_pembayaran_1 = $('#konfirmasi_sewa_peralatan').find('#metode_pembayaran').val();
      var metode_pembayaran = '<?php echo json_encode(metode_pembayaran()) ?>';
      var parse_json = JSON.parse(metode_pembayaran);
      $('#konfirmasi_sewa_peralatan').find('#form_tambah_metode_pembayaran').html(`<div class="col-sm-6 col-md-6 col-xl-6">
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

      $('#konfirmasi_sewa_peralatan').find('#metode_pembayaran_2').html(``);
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


                $('#konfirmasi_sewa_peralatan').find('#input_dibayar_2').keyup(function(){
                  var input_dibayar_1 = $('#konfirmasi_sewa_peralatan').find('#input_dibayar').val();
      var input_dibayar_2 = $('#konfirmasi_sewa_peralatan').find('#input_dibayar_2').val();
      var dibayar_pelanggan = parseInt(input_dibayar_1) + parseInt(input_dibayar_2);
      $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(dibayar_pelanggan);

                  var tagihan = parseInt( $('#total_biaya_sewa').val());
                  if (input_dibayar_1=='') {
                    // Swal.fire('Warning','Pembayaran pada metode pembayaran pertama harus di input dulu','error');
                          var sisa = input_dibayar_2 - tagihan;
                          $('#konfirmasi_sewa_peralatan').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html("Rp. "+number_format(input_dibayar_2));
                  }else{
                    if (input_dibayar_2=='') {
                          var sisa = input_dibayar_1 - tagihan;
                          $('#konfirmasi_sewa_peralatan').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html("Rp. "+number_format(input_dibayar_1));

                    }else{

                     if (dibayar_pelanggan >0) {

                          var sisa = dibayar_pelanggan - tagihan;
                          $('#konfirmasi_sewa_peralatan').find('#show_kembalian').html("Rp. "+number_format(sisa));
                          $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                          $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(dibayar_pelanggan);
                          $('#konfirmasi_sewa_peralatan').find('#simpan_kembalian').val(sisa);


                        if (parseInt(dibayar_pelanggan) < parseInt(tagihan)) {
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('class','btn btn-outline-danger btn-block');
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('onclick','Swal.fire("Error","Harap masukan jumlah pembayaran dengan benar. jumlah pembayaran tidak boleh kurang dari tagihan","error")');

                        }else{
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('class','btn btn-info btn-block');
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('onclick','simpan_transaksi_sewa()');

                        }



                     
                      }else{
                         $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html("Rp. "+ number_format(tagihan));
                        $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(tagihan);
                        $('#konfirmasi_sewa_peralatan').find('#simpan_kembalian').val(0);
                        $('#konfirmasi_sewa_peralatan').find('#show_kembalian').html('Rp. 0');
                      }

                    }
                } 





                });








    }
    function batal_tambah_metode_pembayaran(){
      
      $('#konfirmasi_sewa_peralatan').find('#opsi_tambah_metode_pembayaran').val(0);
       $('#konfirmasi_sewa_peralatan').find('#form_tambah_metode_pembayaran').html(`

                        <div class="col-sm-12 col-md-12 col-xl-12">
                          <div class="form-group">
                           <button class="btn btn-outline-info btn-block btn-sm" type="button" onclick="tambah_metode_pembayaran()" id="tombol_tambah_metode_pembayaran">Tambah Metode Pembayaran</button>
                         </div>
                      </div>`);

       $('#konfirmasi_sewa_peralatan').find('#metode_pembayaran_2').html(``);
      
  var input_dibayar_1 =  $('#konfirmasi_sewa_peralatan').find('#input_dibayar').val();
      var dibayar_pelanggan = parseInt(input_dibayar_1);

                  var tagihan =  $('#total_biaya_sewa').val();

                   if (dibayar_pelanggan >0) {

                        var sisa = dibayar_pelanggan - tagihan;
                         $('#konfirmasi_sewa_peralatan').find('#show_kembalian').html("Rp. "+number_format(sisa));
                         $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                         $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(dibayar_pelanggan);
                         $('#konfirmasi_sewa_peralatan').find('#simpan_kembalian').val(sisa);
                   
                    }else{
                        $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html("Rp. "+ number_format(tagihan));
                       $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(tagihan);
                       $('#konfirmasi_sewa_peralatan').find('#simpan_kembalian').val(0);
                       $('#konfirmasi_sewa_peralatan').find('#show_kembalian').html('Rp. 0');
                    }



    }
  </script>
<script type="text/javascript">
 $(document).ready(function(){
  keranjang();
 });



    $('#konfirmasi_sewa_peralatan').find('#metode_pembayaran').change(function(){
     
    var opsi_tambah_metode_pembayaran = $('#konfirmasi_sewa_peralatan').find('#opsi_tambah_metode_pembayaran').val();
    if (opsi_tambah_metode_pembayaran=='1') {
      tambah_metode_pembayaran();
    }
  });



function konfirmasi_sewa_peralatan(){
  var nama_penyewa =$('#nama_penyewa').val();
  var nohp_penyewa =$('#nohp_penyewa').val();
  var alamat_penyewa =$('#alamat_penyewa').val();
  var tgl_sewa =$('#tgl_penyewaan').val();
  var tagihan =$('#total_biaya_sewa').val();

  if (nama_penyewa=='') {
    Swal.fire('Error','Harap masukan nama penyewa','error');
  }else{
    $('#konfirmasi_sewa_peralatan').modal('show');
  }
  $('#konfirmasi_sewa_peralatan').find('#input_dibayar').val('');
  $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(tagihan);
  $('#konfirmasi_sewa_peralatan').find('#nama').html(nama_penyewa);
  $('#konfirmasi_sewa_peralatan').find('#nama_penyewa').val(nama_penyewa);
  $('#konfirmasi_sewa_peralatan').find('#tgl_sewa').val(tgl_sewa);
  $('#konfirmasi_sewa_peralatan').find('#nohp').html(nohp_penyewa);
  $('#konfirmasi_sewa_peralatan').find('#alamat').html(alamat_penyewa);
  $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(tagihan);
  $('#konfirmasi_sewa_peralatan').find('.rp_tagihan_sewa').html(number_format(tagihan));
  $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html(number_format(tagihan));







                $('#konfirmasi_sewa_peralatan').find('#input_dibayar').keyup(function(){
                  var dibayar_pelanggan = $('#konfirmasi_sewa_peralatan').find('#input_dibayar').val();
                  var tagihan = $('#total_biaya_sewa').val();

                          $('#konfirmasi_sewa_peralatan').find('#tombol_tambah_metode_pembayaran').hide();
                   if (dibayar_pelanggan =='') {
                    $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html("Rp. "+ number_format(tagihan));
                    $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(tagihan);
                    $('#konfirmasi_sewa_peralatan').find('#simpan_kembalian').val(0);
                    $('#konfirmasi_sewa_peralatan').find('#show_kembalian').html('Rp. 0');
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('class','btn btn-info btn-block');
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('onclick','simpan_transaksi_sewa()');
                          // $('#konfirmasi_sewa_peralatan').find('#opsi_tambah_metode_pembayaran').val(0);
                          
                          $('#konfirmasi_sewa_peralatan').find('#tombol_tambah_metode_pembayaran').hide();
                    }else{

                        var sisa = dibayar_pelanggan - tagihan;
                        $('#konfirmasi_sewa_peralatan').find('#show_kembalian').html("Rp. "+number_format(sisa));
                        $('#konfirmasi_sewa_peralatan').find('#show_dibayar').html("Rp. "+number_format(dibayar_pelanggan));
                        $('#konfirmasi_sewa_peralatan').find('#simpan_dibayar').val(dibayar_pelanggan);
                        $('#konfirmasi_sewa_peralatan').find('#simpan_kembalian').val(sisa);

                        if (sisa<0) {
                          $('#konfirmasi_sewa_peralatan').find('#tombol_tambah_metode_pembayaran').show();
                        }else{
                          $('#konfirmasi_sewa_peralatan').find('#tombol_tambah_metode_pembayaran').hide();

                        }
                        if (parseInt(dibayar_pelanggan) < parseInt(tagihan)) {
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('class','btn btn-outline-danger btn-block');
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('onclick','Swal.fire("Error","Harap masukan jumlah pembayaran dengan benar. jumlah pembayaran tidak boleh kurang dari tagihan","error")');

                        }else{
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('class','btn btn-info btn-block');
                          $('#konfirmasi_sewa_peralatan').find('#tombol_simpan_transaksi_sewa').attr('onclick','simpan_transaksi_sewa()');

                        }
                    }

                });


}





    function simpan_transaksi_sewa(){
      var status = $('#konfirmasi_sewa_peralatan').find('#status').val();
      var metode_pembayaran = $('#konfirmasi_sewa_peralatan').find('#metode_pembayaran').val();
      var penjualan_fnb = $('#konfirmasi_sewa_peralatan').find('#rp_penjualan_fnb').val();
      var form_data = $('#konfirmasi_sewa_peralatan').find('#form_sewa_peralatan').serialize();
            $.ajax(
              {
                url     : '<?php echo base_url('user/gro/sewa_peralatan/simpan_transaksi_sewa') ?>',
                dataType: 'JSON',
                type    : 'POST',
                data    : form_data,
                success : function(data)
                {
                   // $('#status').val('Selesai');
                   // keranjang();
                  $('#nama_penyewa').val('');
                  $('#nohp_penyewa').val('');
                  $('#alamat_penyewa').val('');
                  $('#tgl_penyewaan').val('<?php echo date('Y-m-d') ?>');
                   
                  // $('#list_transaksi_fnb').DataTable().ajax.reload();
                  // $('#rekap_produk_terjual').DataTable().ajax.reload();
                   print_transaksi(data.id_transaksi);

                            $('#keranjang').html(`<div class="alert alert-info">Transaksi disimpan  <br> </div>`);
                  $('#tombol_print').html(`<button type="button" class="btn btn-info btn-block" onclick="print_transaksi('`+data.id_transaksi+`')">Print Struk</button>`);

                      $('#div_form_pelanggan').hide();
                   // $('.tombol_masuk_keranjang').attr('class','btn btn-danger btn-sm');
                   // $('.tombol_masuk_keranjang').attr('onclick',"Swal.fire('Warning','Sudah di submit. tidak bisa diubah lagi','warning')");
                }, 
                error : function(){

                }
              });
    }

 function masuk_ke_keranjang(x){
  status = $('#status').val();
 var qty = $(x).parents("tr").find('#qty').val();
 var kode_peralatan = $(x).parents("tr").find('#kode_peralatan').val();
 var id_peralatan = $(x).parents("tr").find('#id_peralatan').val();
 var nama_peralatan = $(x).parents("tr").find('#nama_peralatan').val();
 var biaya_sewa = $(x).parents("tr").find('#biaya_sewa').val();
 var id_akun_pendapatan = $(x).parents("tr").find('#id_akun_pendapatan').val();
// var pendapatan_sebelumnya = $('#simpan_pendapatan_penjualan').val();
var total = parseInt(biaya_sewa) * parseInt(qty);
// var pendapatan_baru = parseInt(pendapatan_sebelumnya) +  total; 
 if (qty=='' || qty==0) {
  Swal.fire('Error','Harap masukan jumlah alat yang disewa','error');
 }else{
 
         $.ajax(
              {
                url     : '<?php echo base_url('/user/gro/sewa_peralatan/masuk_ke_keranjang') ?>',
                dataType: 'JSON',
                type    : 'POST',
                data    : {
                  qty : qty, 
                  id_peralatan : id_peralatan, 
                  kode_peralatan : kode_peralatan, 
                  nama_peralatan : nama_peralatan, 
                  biaya_sewa : biaya_sewa, 
                  id_akun_pendapatan : id_akun_pendapatan, 
                },
                success : function(data)
                {
                  keranjang();
                  // $('#simpan_pendapatan_penjualan').val(pendapatan_baru);
                  $(x).parents("tr").find('#qty').val('');
                  toastr.success(data.pesan);
                },
                error : function(){
                  alert('error');
                }
      });
       }
    
 }

 function keranjang(){
  $('#tombol_print').html('');
 var tgl = '<?php echo date('Y-m-d') ?>';
   $.ajax(
              {
                url     : '<?php echo base_url('/user/gro/sewa_peralatan/keranjang') ?>',
                dataType: 'JSON',
                type    : 'POST',
                data    : {
                
                  tgl : tgl, 
                },
                success : function(data)
                {



                var isi_tabel = `
                      <table class="table table-striped table-bordered data_tabel">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Kode Peralatan</th>
                            <th>Nama Alat</th>
                            <th>Biaya Sewa</th>
                            <th>Jumlah Alat</th>
                            <th>Total</th>
                            <th>Option</th>
                          </tr>
                        </thead>
                        <tbody>`;


                  var total_semua = 0;
                  var jumlah_item =0;
                  $.each(data.data, function(k,v){
                    var total = parseInt(v.harga_satuan) * parseInt(v.qty);
                    total_semua +=total;
                      qty_edit = `<a href="javascript:void(0)" id="" pk="`+v.id_sewa_peralatan+`"  jumlah="`+v.qty+`"  produk="`+v.nama_peralatan+`" class="edit" data-type="text" onclick="edit_keranjang(this)"> ${number_format(v.qty)}</a>`;

                        var tombol_hapus=`<a href="javascript:void(0)" onclick="hapus_keranjang('`+v.id_sewa_peralatan+`','`+v.nama_peralatan+`')" class="btn btn-info btn-xs"><i class="fa fa-trash"></i></a>`;
                        var edit_fnb = qty_edit;
                     
                  isi_tabel += `
                            <tr>
                              <td>`+(k+1)+`</td>
                              <td>`+v.kode_peralatan+`</td>
                              <td>`+v.nama_peralatan+`</td>
                              <td>`+number_format(v.harga_satuan)+`</td>
                              <td>`+edit_fnb+`</td>
                              <td>`+number_format(total)+`</td>
                              <td>
                                `+tombol_hapus+`
                              </td>
                            </tr>
                           `;
                  jumlah_item++;
                  });


                    if (jumlah_item ==0) {
                      var tombol_aksi = `Keranjang Kosong`;
                      $('#div_form_pelanggan').hide();

                    }else{
                      $('#div_form_pelanggan').show();
                      var tombol_aksi = `<a href="#" data-toggle="modal" data-target="#konfirmasi_sewa_peralatan" class="btn btn-info btn-sm" onclick="selesai_input()" >Simpan</a>`;

                    }

                

                    $('#tombol_selesai_input').hide();



                $('#simpan_pendapatan_penjualan').val(total_semua);

                $('#pendapatan_penjualan').html(number_format(total_semua));
                  isi_tabel += `
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="5">Total</td>
                              <td>`+number_format(number_format(total_semua))+`</td>
                              <td>
                               -
                              </td>
                            </tr>
                          </tfoot>
                          </table>`;
                  isi_tabel += `
                          </table>`;
                           if (data.count=='0') {
                            $('#keranjang').html('<div class="alert alert-info">Belum ada peralatan yang akan disewa</div>');
                            $('#nama_penyewa').val('');
                            $('#nohp_penyewa').val('');
                            $('#alamat_penyewa').val('');
                            $('#tgl_penyewaan').val('<?php echo date('Y-m-d') ?>');

                            $('#total_biaya_sewa').val(0);
                           }
                         else{
                            $('#keranjang').html(isi_tabel);
                            $('#total_biaya_sewa').val(total_semua);
                           }
                },
                error : function(){
                  alert('error');
                }
      });





 }



 function hapus_keranjang(id_sewa_peralatan, nama_peralatan){

 
         $.ajax(
              {
                url     : '<?php echo base_url('/user/gro/sewa_peralatan/hapus_keranjang') ?>',
                dataType: 'JSON',
                type    : 'POST',
                data    : {
                  id_sewa_peralatan : id_sewa_peralatan, 
                  nama_peralatan : nama_peralatan, 
              
                },
                success : function(data)
                {
                  keranjang();
                  toastr.success(data.pesan);
                },
                error : function(){
                  alert('error');
                }
      });
       
 }


  function print_transaksi(id_transaksi){
     $('#konfirmasi_sewa_peralatan').modal('hide');
    $('#print_transaksi_gym').modal('show');
    $('#print_transaksi_gym').find('#struk_order_member_gym').html(`
      <iframe src="`+ baseUrl('/user/gro/sewa_peralatan/print_transaksi/') + id_transaksi +`/?status_print=settlement&action=Lunas" width="100%" height="400px"></iframe>
      `);
  }


</script>