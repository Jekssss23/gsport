<?php 

  $status = ['Tidak Aktif','Aktif'];

   ?>
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/user/simpan') ?>" method='post' enctype="multipart/form-data"> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Nama</label>
                  <input type="text" class="form-control" name="nama" id="nama" required>
                </div>
                <div class="form-group">
                  <label>Alamat</label>
                  <input type="text" class="form-control" name="alamat" id="alamat" >
                </div>
                <div class="form-group">
                  <label>No HP</label>
                  <input type="text" class="form-control" name="nohp" id="nohp" required>
                </div>
                <div class="form-group">
                  <label>Jabatan</label>
                  <input type="text" class="form-control" name="jabatan" id="jabatan" required>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="text" class="form-control" name="email" id="email" required>
                </div>
                <div class="form-group">
                  <label>Foto</label>
                  <input type="file" class="form-control" name="berkas" id="berkas" >
                </div>
                <div class="form-group">
                  <hr>
                </div>
                <div class="form-group mb-3">
                  <label>Hak Akses</label> <br>
                   <?php foreach ($hak_akses as $k => $v) { ?>
                    <div><input type="checkbox" name="hak_akses[]" id="hak_akses" value="<?php echo $k ?>"> <?php echo $v; ?></div>
                    <?php } ?>
                 
                </div>
                <div class="form-group">
                  <label>Username</label>
                  <input type="text" class="form-control" name="username" id="username" >
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input type="text" class="form-control" name="password" id="password" >
                </div>
                <div class="form-group">
                  <label>Status Login</label>
                  <select class="form-control" name="status" id="status" >
                    <?php foreach ($status as $k => $v) { ?>
                      <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
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



<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/user/simpanedit') ?>" method='post' enctype="multipart/form-data"> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label>Nama</label>
                  <input type="hidden" class="form-control" name="id_user" id="id_user" >
                  <input type="text" class="form-control" name="nama" id="nama" >
                </div>
                <div class="form-group">
                  <label>Alamat</label>
                  <input type="text" class="form-control" name="alamat" id="alamat" >
                </div>
                <div class="form-group">
                  <label>No HP</label>
                  <input type="text" class="form-control" name="nohp" id="nohp" >
                </div>
                <div class="form-group">
                  <label>Jabatan</label>
                  <input type="text" class="form-control" name="jabatan" id="jabatan" >
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="text" class="form-control" name="email" id="email" >
                </div>
                <div class="form-group">
                  <label>Foto</label>
                  <input type="file" class="form-control" name="berkas" id="berkas" >
                </div>
                <div class="form-group">
                  <hr>
                </div>

                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control" name="status" id="status" >
                    <?php foreach ($status as $k => $v) { ?>
                      <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
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


<div class="modal fade" id="edit_login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/admin/user/simpanedit_login') ?>" method='post' enctype="multipart/form-data"> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Login user</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                  <input type="hidden" class="form-control" name="id_user" id="id_user" >
              

                <div class="form-group mb-3">
                  <label>Hak Akses</label> <br>
                   <?php foreach ($hak_akses as $k => $v) { ?>
                    <div><input type="checkbox" name="hak_akses[]" id="hak_akses_<?php echo $k ?>" value="<?php echo $k ?>" class="input_hak_akses"> <?php echo $v; ?></div>
                    <?php } ?>
                 
                </div>


                
                <div class="form-group">
                  <label>Username</label>
                  <input type="text" class="form-control" name="username" id="username" >
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input type="text" class="form-control" name="password" id="password" >
                </div>
                <div class="form-group">
                  <label>Status Login</label>
                  <select class="form-control" name="status" id="status" >
                    <?php foreach ($status as $k => $v) { ?>
                      <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
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

function edit(id_user)
  {
    
                  $('#edit_login').find('.input_hak_akses').removeAttr('checked');
            $.ajax(
            {
              url     : baseUrl('/user/admin/user/edit'),
              dataType: 'JSON',
              type    : 'POST',
              data    : { 
                id_user : id_user,
                
              },
              success : function(data)
              {

                // $('input.currency').number( true, 0 );
                  $('#edit').find('#id_user').val(data.user.id_user);
                  $('#edit').find('#nama').val(data.user.nama);
                  $('#edit').find('#alamat').val(data.user.alamat);
                  $('#edit').find('#nohp').val(data.user.nohp);
                  $('#edit').find('#jabatan').val(data.user.jabatan);
                  $('#edit').find('#email').val(data.user.email);
                  $('#edit').find('#status').val(data.user.status).change();
                  $('#edit_login').find('#id_user').val(data.user.id_user);
                  $('#edit_login').find('#username').val(data.user.username);
                  $('#edit_login').find('#status').val(data.user.status_akses).change();

                  $.each(data.hak_akses, function(k,v){
                  $('#edit_login').find('#hak_akses_'+v.id_hak_akses).attr('checked','checked');

                  });
              

              },
              error : function(){
                alert('33');
              }
            });
          }
      


  function hapus(id, user){
     
    Swal.fire({
        title: 'Hapus ?',
        text: 'Hapus user '+user+'.?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          
            $.ajax({
          url: '<?php echo base_url() ?>user/admin/user/hapus/',
              type: 'POST',
              // dataType: 'JSON',
              data: {    
                id : id,
              },
              success: function(data) {
                
                window.location.href=baseUrl('user/admin/user');
              },
              error: function(jqXHR, textStatus, errorThrown) {
              }
          });

        
        }
      });


    
  }

</script>









