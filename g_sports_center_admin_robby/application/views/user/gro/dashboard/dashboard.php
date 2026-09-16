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
                                                
                                                    </tbody>
                                                  </table>
                                                  </div>
                                                  <div class="col-md-5">
                                                    





                                                      <h5 class="card-title">Silahkan pilih Aktivitas Anda</h5>
                                                    <div class="grid-menu grid-menu-3col">
                                                <div class="no-gutters row">
                                                    <div class="col-sm-6 col-xl-4">
                                                        <a href="<?php echo base_url('user/gro/new_transaction') ?>" class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>Transaksi Baru</a>
                                                    </div>
                                                    <div class="col-sm-6 col-xl-4">
                                                        <a href="<?php echo base_url('user/gro/student_card') ?>" class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>Student Card</a>
                                                    </div>
                                                    <div class="col-sm-6 col-xl-4">
                                                        <a href="<?php echo base_url('user/gro/booking?tgl='.date('Y-m-d')) ?>" class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>Booking</a>
                                                    </div>
                                                    <div class="col-sm-6 col-xl-4">
                                                        <a href="<?php echo base_url('user/gro/membership') ?>" class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>Membership</a>
                                                    </div> <div class="col-sm-6 col-xl-4">
                                                        <a class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">-</a>
                                                    </div>
                                                    <div class="col-sm-6 col-xl-4">
                                                        <a  href="<?php echo base_url('user/gro/rekap?tgl='.date('Y-m-d')) ?>"  class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>Rekap Transaksi</a>
                                                    </div>
                                                    <div class="col-sm-6 col-xl-4">
                                                        <a  href="<?php echo base_url('user/gro/fnb?tgl='.date('Y-m-d')) ?>"  class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>F & B</a>
                                                    </div>
                                                   
                                                    <div class="col-sm-6 col-xl-4">
                                                        <a  href="<?php echo base_url('user/gro/proshop?tgl='.date('Y-m-d')) ?>" class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>Proshop</a>
                                                    </div>
                                                    <div class="col-sm-6 col-xl-4">
                                                        <a  href="<?php echo base_url('user/qrcode') ?>" class="btn-icon-vertical btn-square btn-transition btn btn-outline-link"><i class="fa fa-book btn-icon-wrapper btn-icon-lg mb-3"> </i>QR Code Web GSC</a>
                                                    </div>
                                                </div>
                                            </div>
                                                  </div>
                                              </div>
                                          


                                          </div>
                                        </div>

                                    </div>
                                </div>