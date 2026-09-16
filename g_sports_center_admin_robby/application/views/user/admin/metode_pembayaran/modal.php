<?php 

  $status = ['Tidak Aktif','Aktif'];

   ?>
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/metode_pembayaran/simpan') ?>" method='post'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Metode Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Metode Pembayaran</label>

                  <input type="text" class="form-control" name="metode_pembayaran" id="metode_pembayaran" required>
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


<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/metode_pembayaran/simpanedit') ?>" method='post'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Metode Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Metode Pembayaran</label>

                  <input type="hidden" class="form-control" name="id_metode_pembayaran" required id="id_metode_pembayaran">
                  <input type="text" class="form-control" name="metode_pembayaran" id="metode_pembayaran" required>
                </div>
                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control" name="status" id="status">
                        <?php foreach ($status as $k => $v) { ?>
                          <option value="<?php echo $k ?>"><?php echo $v ?></option>
                        <?php } ?>
                      </select>
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



<script>
function edit(id_metode_pembayaran)
  {
    
            $.ajax(
            {
              url     : baseUrl('/user/admin/metode_pembayaran/edit'),
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_metode_pembayaran : id_metode_pembayaran,
                
              },
              success : function(data)
              {

                // $('input.currency').number( true, 0 );
                // console.log(data);
                  $('#edit').find('#id_metode_pembayaran').val(data.id_metode_pembayaran);
                  $('#edit').find('#metode_pembayaran').val(data.metode_pembayaran);
                  $('#edit').find('#status').val(data.status).change();
              

              },
              error : function(){
              }
            });
          }
      


  function hapus(id, metode_pembayaran){
     
    Swal.fire({
        title: 'Hapus ?',
        text: 'Hapus metode pembayaran '+metode_pembayaran+'.?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
          url: '<?php echo base_url() ?>user/admin/metode_pembayaran/hapus/',
              type: 'POST',
              // dataType: 'JSON',
              data: {    
                id : id,
              },
              success: function(data) {
                
                window.location.href=baseUrl('user/admin/metode_pembayaran');
              },
              error: function(jqXHR, textStatus, errorThrown) {
                console.log('e');
              }
          });

        
        }
      });


    
  }

</script>









