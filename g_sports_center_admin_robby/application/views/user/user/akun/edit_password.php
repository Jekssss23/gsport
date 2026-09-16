<?php 
$foto = $user['foto'] == '' ? base_url().'/file/user/user.webp' : base_url().'/file/user/'.$user['foto'];
 ?> <div class="row">
    <div class="col-md-12">
      <div class="box">
       
        <div class="box-body">
          <?php echo $this->session->flashdata('pesan') ?>
          <form method="post" action="<?php echo base_url() ?>auth/profile/simpanedit_password">
            <div class="col-md-3">
              <h4>Foto</h4>
              <img src="<?php echo $foto ?>" alt="User Image">
            </div>
            <div class="col-md-4">
              <h4>Identitas</h4>
              <table class="table">
                <tr>
                  <td>Nama</td>
                  <td>:</td>
                  <td><?php echo $user['nama'] ?></td>
                </tr>
                <tr>
                  <td>Jabatan</td>
                  <td>:</td>
                  <td><?php echo $user['jabatan'] ?></td>
                </tr>
                <tr>
                  <td>Alamat</td>
                  <td>:</td>
                  <td><?php echo $user['alamat'] ?></td>
                </tr>
                <tr>
                  <td>No HP</td>
                  <td>:</td>
                  <td><?php echo $user['nohp'] ?></td>
                </tr>
                <tr>
                  <td>Email</td>
                  <td>:</td>
                  <td><?php echo $user['email'] ?></td>
                </tr>
                <tr>
                  <td>Hak Akses</td>
                  <td>:</td>
                  <td><?php echo $user['nama_hak_akses'] ?></td>
                </tr>
              </table>
            </div>
            <div class="col-md-5">
              <h4>Ubah Login</h4>
            
              <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" name="username" value="<?php echo $user['username'] ?>">
                
              </div>
              <div class="form-group">
                <label>Password Lama</label>
                <input type="password" class="form-control" name="password_lama">
                
              </div>
              <div class="form-group">
                <label>Password Baru</label>
                <input type="password" class="form-control" name="password_baru">
                
              </div>
              <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" class="form-control" name="password_baru_konfirm">
                
              </div>
              <div class="form-group">
                <button class="btn btn-info">Simpan</button>
              </div>
            </div>
          </form>

        </div>


      </div>
    </div>


  </div>
