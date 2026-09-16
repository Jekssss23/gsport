  <style>
  .example1 {
  border: 2px  black;
  background: url('<?php echo base_url()  ?>file/kartu/202312310618.jpg');
  background-repeat: no-repeat;
    background-size: 100% 100%;
    width : 100%;
    height : auto;
    /*opacity: 0.5;*/
}
</style>

<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
                        <li class="nav-item">
                            <a role="tab" class="nav-link active" id="btn_tab_cek_data" data-toggle="tab" href="#futsal_bookinf" onclick="data_order_futsal('harian');data_order_futsal('bulanan');data_order_futsal('turnamen')">
                                <span>Futsal</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a role="tab" class="nav-link" id="btn_tab_new_transaction"  data-toggle="tab" href="#badminton_booking" onclick="data_order_badminton('harian');data_order_badminton('bulanan');data_order_badminton('turnamen')">
                                <span>Badminton</span>
                            </a>
                        </li> 
                        <li class="nav-item">
                          <a role="tab" class="nav-link" id="btn_tab_new_transaction"  data-toggle="tab" href="#pickle_booking" onclick="data_order_pickle('harian');data_order_pickle('bulanan');data_order_pickle('turnamen')">
                                <span>Pickle</span>
                            </a>
                        </li> 
                        <li class="nav-item">
                            <a role="tab" class="nav-link" data-toggle="modal" data-target="#filter"  onclick="show_pilihan_fasilitas()">
                                <span>Filter</span>
                            </a>
                        </li> 
                      
                     
                    </ul>
                    <div class="tab-content">
                    <div class="tab-pane tabs-animation active fade show " id="futsal_bookinf" role="tabpanel">
                      <?php $this->load->view('user/gro/booking/futsal') ?>
                    </div>
                         <div class="tab-pane tabs-animation fade" id="badminton_booking" role="tabpanel">
                          
                      <?php $this->load->view('user/gro/booking/badminton') ?>
                          
                        </div>
                         <div class="tab-pane tabs-animation fade" id="pickle_booking" role="tabpanel">
                          
                      <?php $this->load->view('user/gro/booking/pickle') ?>
                          
                        </div>
                    
                       
                    
                  
                      </div>




<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.10.19/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>





<script type="text/javascript">
// dt_member_reg_online();
function konfirmasi_gym(){

    $('#modal_member').modal('show');
}
function show_pilihan_fasilitas(){

    $('#modal_new_transaction').modal('show');
    $('#tombol_ganti_fasilitas').html('Pilih Fasilitas');
}
    function audio_ditemukan() {
      // song.volume = 0.7;
      const song = document.querySelector('#song_ditemukan');
      song.play();
      isPlaying = true;
    }
    function audio_tidak_ditemukan() {
      // song.volume = 0.7;
      const song = document.querySelector('#song_tidak_ditemukan');
      song.play();
      isPlaying = true;
    }


 function show_student_card(){
    $('#student_card').modal('show');
    dt_student_card();
  }

  function pilih_student_card(id, nama, alamat, nohp){
    $('#student_card_nama').html(nama);
    $('#student_card_alamat').html(alamat);
    $('#student_card_nohp').html(nohp);
    $('#id_diskon').val(id);
    $('#nama_diskon').val('Student Card : '+ nama);

    $('.alert_student_card').hide();
    $('.data_student_card').show();


  }

   function dt_student_card(){
    $('#list_student_card').DataTable(
    {
          processing  : true,
          serverSide  : true,
          bDestroy  : true,
          responsive  : true,
          ajax    : {
                   url: '<?php echo base_url() ?>user/gro/new_transaction/dt_student_card',
                    type  : "POST",
                    data  : {
                    },
                  },
      });
  }




function print_order_member(id_transaksi)
  {

    $('#konfirmasi_member').modal('hide');
    $('#print_order_member').modal('show');
    $('#print_order_member').find('#struk_order_member').html(`
      <iframe src="`+ baseUrl('file/Print.pdf') +`" width="100%" height="400px"></iframe>
      `);
           
                       $('#total_biaya').html(``);
                       $('#form_total_belanja').html(``);
                       $('#id_jenis_member').val(``).change();
             $('#show_list_order_keranjang').html(`

                  <div class="small-box bg-gray">
                                <div class="inner">

                                  <h4>Transaksi Disimpan <br>Belum ada order baru</h4>
                                </div>
                              </div>
              `);
                       $('#input_nama').val(``);
                       $('#input_no_identitas').val(``);
                       $('#input_tmpl').val(``);
                       $('#input_pekerjaan').val(``);
                       $('#input_alamat').val(``);
                       $('#input_nohp').val(``);
                       $('#input_email').val(``);
                       $('#input_ig').val(``);


          }



var  elemen = document.querySelector("#get_capture");
// html2canvas(elemen).then(canvas => {
//     document.body.appendChild(canvas)
// });
// html2canvas(elemen).then(function(canvas){
//   document.querySelector("#result").append(canvas);
//   var cvs = document.querySelector("canvas");
//   var unduh = document.querySelector(".download");
//   unduh.href = cvs.toDataURL();
//   unduh.download="Membercard_namamember.jpg";
//   $('#get_capture').attr('style','display:none');
// })


</script>