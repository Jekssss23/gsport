<div class="row">
<div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">Selamat datang di halaman GRO
                                        </div>
                                        <div class="card-body">
                                         
                                              <div class="row">
                                                 <div class="col-md-12">
                                                  <?php echo $this->session->flashdata('pesan') ?>
                                                </div>
                                                  <div class="col-md-3">
                                                  <img src="<?php echo  base_url('file/user/'.$user['foto']) ?>" width="100%">
                                                  </div>
                                                  <div class="col-md-4">
                                                    <table class="table">
                                                      <tbody>
                                                        
                                                        <tr>
                                                        <td valign="top">Nama</td>
                                                        <td valign="top">:</td>
                                                        <td valign="top" id="identitas"><?php echo $user['nama'] ?></td>
                                                      </tr>
                                                      <tr>
                                                        <td valign="top">Alamat</td>
                                                        <td valign="top">:</td>
                                                        <td valign="top" id="jk"><?php echo $user['alamat'] ?></td>
                                                      </tr>
                                                      <tr>
                                                        <td valign="top">No HP</td>
                                                        <td valign="top">:</td>
                                                        <td valign="top" id="jk"><?php echo $user['nohp'] ?></td>
                                                      </tr>
                                                      <tr>
                                                        <td valign="top">Email</td>
                                                        <td valign="top">:</td>
                                                        <td valign="top" id="jk"><?php echo $user['email'] ?></td>
                                                      </tr>
                                                      <tr>
                                                        <td valign="top">Jabatan </td>
                                                        <td valign="top">:</td>
                                                        <td valign="top" id="jk"><?php echo $user['jabatan'] ?></td>
                                                      </tr>
                                                
                                                    </tbody></table>
                                                  </div>
                                                  <div class="col-md-5">
                                                    


                                                    <form method="post" action="<?php echo base_url('user/account/simpanedit_password') ?>">
                                                      


                                                      <h5 class="card-title">Ganti username / password</h5>
                                                      <div class="form-group">
                                                        <label>Uername</label>
                                                        <input type="text" name="username" class="form-control" value="<?php echo $user['username'] ?>" required>
                                                      </div>
                                                      <div class="form-group">
                                                        <label>Password Lama</label>
                                                        <input type="password" name="pass_lama" class="form-control" required>
                                                      </div>
                                                      <div class="form-group">
                                                        <label>Password Baru</label>
                                                        <input type="password" name="pass_baru" class="form-control" required>
                                                      </div>
                                                      <div class="form-group">
                                                        <button class="btn btn-info">Perbaharui Password</button>
                                                      </div>
                                                    </form>
                                                  
                                                  </div>
                                              </div>
                                          


                                          </div>
                                        </div>

                                    </div>
                                </div>