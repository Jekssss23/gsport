<div class="row">
<div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">Swlamat datang di halaman GRO
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
                                                
                                                    </tbody>
                                                  </table>
                                                  </div>
                                                  <div class="col-md-5">
                                                    





                                                      <h5 class="card-title">Silahkan pilih Aktivitas Anda</h5>
                                                    <div class="grid-menu grid-menu-1col">
                                                <div class="no-gutters row">
                                                    <div class="col-sm-12 col-xl-12">
                                                         <a  href="#" onclick="Swal.fire('Development','Fitur ini masih dalam pengembangan','info')" class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>Absensi</a>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                                  </div>
                                              </div>
                                          


                                          </div>
                                        </div>

                                    </div>
                                </div>