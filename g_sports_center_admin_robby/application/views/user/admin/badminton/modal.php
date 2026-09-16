
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/badminton/simpanedit') ?>" method='post'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Badminton</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Keterangan</label>

                  <input type="hidden" class="form-control" name="id_badminton" required id="id_badminton">
                  <input type="text" class="form-control" name="keterangan" id="keterangan" required>
                </div>

                <div class="form-group">
                  <label>Harga</label>
                  <input type="text" class="form-control currency" name="biaya"  id="biaya" required   onblur="if(value==''){value='0'}" value="0">
                </div>
                <div class="form-group">
                  

                  <div class="row">
                    <div class="col-md-6">
                      <label>Mulai</label>
                      <input type="time" class="form-control" name="mulai" id="mulai">
                    </div>
                    <div class="col-md-6">
                      <label>Berakhir</label>
                      <input type="time" class="form-control" name="berakhir" id="berakhir">
                    </div>
                  
                  </div>



                </div>
               
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="edit_badminton_turnamen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/badminton/simpanedit_turnamen') ?>" method='post'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Paket Turnamen Badminton</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Nama Paket</label>

                  <input type="hidden" class="form-control" name="id_badminton_turnamen" required id="id_badminton_turnamen">
                  <input type="text" class="form-control" name="paket" id="paket" required>
                </div>

                <div class="form-group">
                  <label>Keterangan</label>
                  <textarea class="form-control" name="keterangan" id="keterangan" required rows="5"></textarea>
                </div>

                <div class="form-group">
                  <label>Harga</label>
                  <input type="text" class="form-control currency" name="biaya"  id="biaya" required   onblur="if(value==''){value='0'}" value="0">
                </div>
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="edit_diskon_member" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/badminton/simpanedit_diskon_member') ?>" method='post'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Diskon Member Bulanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Diskon (%)</label>
                  <input type="text" class="form-control currency" name="diskon"  id="diskon" required   onblur="if(value==''){value='0'}" value="0">

                </div>

              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
  </form>
</div>


<div class="modal fade" id="edit_selisih_shoutlecook" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/badminton/simpanedit_selisih_shoutlecook') ?>" method='post'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Selisih Shoutlecook</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Selisih (Rp.)</label>
                  <input type="text" class="form-control currency" name="selisih"  id="selisih" required   onblur="if(value==''){value='0'}" value="<?php echo $shoutlecook['selisih_tanpa_shoutlecook'] ?>">

                </div>

              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
  </form>
</div>



<div class="modal fade" id="tambah_badminton_turnamen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/badminton/simpan_turnamen') ?>" method='post'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Paket Turnamen Badminton</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Nama Paket</label>

                  <input type="text" class="form-control" name="paket" id="paket" required>
                </div>

                <div class="form-group">
                  <label>Keterangan</label>
                  <textarea class="form-control" name="keterangan" id="keterangan" required rows="5"></textarea>
                </div>

                <div class="form-group">
                  <label>Harga</label>
                  <input type="text" class="form-control currency" name="biaya"  id="biaya" required   onblur="if(value==''){value='0'}" value="0">
                </div>
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
  </form>
</div>







<div class="modal fade" id="tambah_foto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/badminton/simpan_foto') ?>" method='post'  enctype="multipart/form-data" > 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Foto Badminton</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                  <div class="form-group">
                    <label>File Foto</label>
                    <input type="file" name="berkas" class="form-control" required>
                  </div>
               


               
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="lihat_thumbnail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/badminton/simpan_foto') ?>" method='post'  enctype="multipart/form-data" > 
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Thumbnail Fasilitas Badminton</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                  <div class="form-group">
                    <?php if ($thumbnail!=''){ ?>
                        <label>Foto Thumbnail</label>
                        <img src="<?php echo base_url('/file/public/badminton/'.$thumbnail) ?>" width="100%">
                      
                    <?php }else{ ?>
                        <label>Foto thumbnail belum ditentukan</label>

                    <?php } ?>
                    <img src="" width="">
                  </div>
               


               
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </form>
</div>


<script src="<?php echo base_url(); ?>assets/jquery_number/jquery.number.js"></script>
<script>
  jenis_badminton();
  showAutoCurrency();
  function showAutoCurrency(){
    $('input.currency').number( true, 0 );
  }

function jenis_badminton(){
  $('#header').html(`Setting Harga badminton`);
}  


function galeri(){
  $('#header').html('Galeri Foto');
}  




function edit(id_badminton)
  {
    
            $.ajax(
            {
              url     : baseUrl('/user/admin/badminton/edit'),
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_badminton : id_badminton,
                
              },
              success : function(data)
              {

                // $('input.currency').number( true, 0 );
                // console.log(data);
                  $('#edit').find('#id_badminton').val(data.id_badminton);
                  $('#edit').find('#keterangan').val(data.keterangan);
                  $('#edit').find('#biaya').val(data.harga);
                  $('#edit').find('#mulai').val(data.jam_mulai);
                  $('#edit').find('#berakhir').val(data.jam_berakhir);
              

              },
              error : function(){
              }
            });
          }
      



function edit_turnamen(id_badminton_turnamen)
  {
    
            $.ajax(
            {
              url     : baseUrl('/user/admin/badminton/edit_turnamen'),
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_badminton_turnamen : id_badminton_turnamen,
                
              },
              success : function(data)
              {

                // $('input.currency').number( true, 0 );
                var keterangan = data.keterangan.replaceAll("<br />","");
                  $('#edit_badminton_turnamen').find('#id_badminton_turnamen').val(data.id_badminton_turnamen);
                  $('#edit_badminton_turnamen').find('#keterangan').val(keterangan);
                  $('#edit_badminton_turnamen').find('#biaya').val(data.harga);
                  $('#edit_badminton_turnamen').find('#paket').val(data.paket);
              

              },
              error : function(){
              }
            });
          }
      

function edit_diskon()
  {
    
            $.ajax(
            {
              url     : baseUrl('/user/admin/badminton/edit_diskon'),
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                
              },
              success : function(data)
              {
                  $('#edit_diskon_member').find('#diskon').val(data.diskon);
              },
              error : function(){
              }
            });
          }
      


      

  function hapus_galerry(id_galerry, file){
     
    Swal.fire({
        title: 'Hapus ?',
        text: 'Hapus foto.?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
          url: '<?php echo base_url() ?>user/admin/badminton/hapus_galery/',
              type: 'POST',
              // dataType: 'JSON',
              data: {    
                id_galerry : id_galerry,
                file : file,
              },
              success: function(data) {
                
                window.location.href=baseUrl('user/admin/badminton?tab=galeri');
              },
              error: function(jqXHR, textStatus, errorThrown) {
                console.log('e');
              }
          });

        
        }
      });


    
  }

  function jadikan_thumbnail(id_galerry, file){
     
    Swal.fire({
        title: 'Update Thumbnail ?',
        text: 'Jadikan foto ini sebagai thumbnail.?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Update',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
          url: '<?php echo base_url() ?>user/admin/badminton/update_thumbnail/',
              type: 'POST',
              // dataType: 'JSON',
              data: {    
                id_galerry : id_galerry,
                file : file,
              },
              success: function(data) {
                
                window.location.href=baseUrl('user/admin/badminton?tab=galeri');
              },
              error: function(jqXHR, textStatus, errorThrown) {
                console.log('e');
              }
          });

        
        }
      });


    
  }



function hapus_turnamen(id_badminton_turnamen, badminton_turnamen)
  {
    Swal.fire({
        title: 'Warning',
        html: 'Hapus turnamen paket '+badminton_turnamen+' .?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax(
            {
              url     : baseUrl('/user/admin/badminton/hapus_turnamen'),
              type    : 'POST',
              data    : { 
                id_badminton_turnamen : id_badminton_turnamen,
                
              },
              success : function(data)
              {
                window.location.href=baseUrl('user/admin/badminton');
              },
              error : function(){
                alert('ee');
                
              }
            });
      

        
        }
      }); 
  }


  <?php 
  $tab=$this->input->get('tab');
  if ($tab=='badminton_spesial') { ?>
     $('#tombol_tab_badminton_spesial').click();

  <?php }else if ($tab=='galeri') { ?>
     $('#tombol_tab_galeri').click();

  <?php }else{ ?>
     $('#tombol_tab_badmintonship').click();

  <?php } ?>

</script>









