<?php 

  $status = ['Tidak Aktif','Aktif'];

   ?>


  <div class="main-card mb-3 card">
                                          <div class="card-header">
                                            Metode pembayaran                                            
                                            <div class="btn-actions-pane-right">
                                             
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content">

                                               <a href="#" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#tambah" style="margin-bottom:10px">Tambah Metode Pembayaran</a> 
                                                <div class="tab-pane active" id="tab_jenis_member" role="tabpanel">
                                                  <?php echo $this->session->flashdata('pesan') ?>
                                                  <div class="row">
                                                    <div class="col-md-12">
                                                      <table class="table table-striped table-bordered">
                                                        <thead>
                                                          <tr>
                                                            <td width="20px">No</td>
                                                            <td>Metode Pembayaran</td>
                                                            <td>Status</td>
                                                          
                                                            <td width="90px">Option</td>
                                                          </tr>
                                                        </thead>
                                                        <?php 
                                                        $no=1;
                                                        foreach ($metode_pembayaran as $d1) { ?>
                                                          <tr>
                                                            <td><?php echo $no++ ?></td>

                                                            <td><?php echo $d1['metode_pembayaran'] ?></td>
                                                            <td><?php echo @$status[$d1['status']] ?></td>
                                                            <td>


                                                                <div role="group" class="btn-group-sm btn-group">
                                                                    <button type="button" class="btn btn-outline-info btn-xs" data-toggle="modal" data-target="#edit" onclick="edit('<?php echo $d1['id_metode_pembayaran'] ?>')">
                                                            Edit
                                                          </button>
                                                                    <button type="button" class="btn btn-outline-info btn-xs" onclick="hapus('<?php echo $d1['id_metode_pembayaran'] ?>','<?php echo $d1['metode_pembayaran'] ?>')">
                                                            Hapus
                                                          </button>
                                                                </div>


                                                             
                                                            
                                                            </td>
                                                          </tr>
                                                        <?php } ?>
                                                        
                                                      </table>
                                                    </div>
                                                    
                                                  </div>
                                                
                                                   
                                               
                                                   
                                                </div>
                                              
                                              
                                            </div>
                                        </div>
                                       <!--  <div class="d-block text-right card-footer">
                                            <a href="javascript:void(0);" class="btn-wide btn btn-success">Tambah Jenis Member</a>
                                        </div> -->
                                    </div>








