<div class="row">
<div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-header">QR Code Web G-Sports Center
                                        </div>
                                        <div class="card-body">
                                         
                                              <div class="row justify-content-center">
                                                 <div class="col-md-12">
                                                  <?php echo $this->session->flashdata('pesan') ?>
                                                </div>
                                                  <div class="col-md-4">
                                                  <img src="<?php echo  base_url($file) ?>" width="100%">
                                                  <a href="<?php echo base_url('user/qrcode/download_qr') ?>" class="btn btn-info btn-block">Download QR Code</a>
                                                  </div>
                                                
                                                
                                              </div>
                                          


                                          </div>
                                        </div>

                                    </div>
                                </div>