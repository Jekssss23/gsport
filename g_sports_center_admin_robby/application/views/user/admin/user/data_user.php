<?php 

  $status = ['Tidak Aktif','Aktif'];
  $kumpul_hs = [];
foreach ($hak_akses as $k => $v) { 
  $kumpul_hs[$k]=$v;
}
   ?>


  <div class="main-card mb-3 card">
                                          <div class="card-header">
                                            User                                            
                                            <div class="btn-actions-pane-right">
                                             
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content">

                                               <a href="#" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#tambah" style="margin-bottom:10px">Tambah User</a> 
                                                <div class="tab-pane active" id="tab_jenis_member" role="tabpanel">
                                                  <?php echo $this->session->flashdata('pesan') ?>
                                                  <div class="row">
                                                    <div class="col-md-12">
                                                      <table class="table table-striped table-bordered data_tabel">
                                                        <thead>
                                                          <tr>
                                                            <td width="20px">No</td>
                                                            <td>Foto</td>
                                                            <td>Nama</td>
                                                            <td>Alamat</td>
                                                            <td>No HP</td>
                                                            <td>Email</td>
                                                            <td>Jabatan</td>
                                                            <td>Hak Akses</td>
                                                            <td>Status</td>
                                                          
                                                            <td width="90px">Option</td>
                                                          </tr>
                                                        </thead>
                                                        <?php 
                                                        $no=1;
                                                        foreach ($user as $d1) {  
                                                          $id_user = $d1['id_user'];
                                                        $q_ha = $this->db->query("SELECT id_hak_akses from hak_akses_user where id_user ='$id_user'")->result_array(); 
                                                        $q_o_fnb = $this->db->query("SELECT id_outlet from outlet_fnb_user where id_user ='$id_user'")->result_array(); 
                                                        $q_o_proshop = $this->db->query("SELECT id_outlet from outlet_proshop_user where id_user ='$id_user'")->result_array(); 
                                                        ?>
                                                          <tr>
                                                            <td><?php echo $no++ ?></td>
                                                            <td>
                                                              <?php if ($d1['foto']=='') { ?>
                                                                Tidak ada foto
                                                              <?php }else{ ?>
                                                                <img src="<?php echo base_url('file/user/'.$d1['foto']) ?>" width="170px">
                                                              <?php } ?>
                                                                
                                                              </td>

                                                            <td><?php echo $d1['nama'] ?></td>
                                                            <td><?php echo $d1['alamat'] ?></td>
                                                            <td><?php echo $d1['nohp'] ?></td>
                                                            <td><?php echo $d1['email'] ?></td>
                                                            <td><?php echo $d1['jabatan'] ?></td>
                                                            <td>
                                                              <ol>
                                                                <?php 
                                                                $kumpul_ha = [];
                                                                foreach ($q_ha as $k => $v) { 
                                                                  array_push($kumpul_ha, $v['id_hak_akses'])?>
                                                                  <li><?php echo $kumpul_hs[$v['id_hak_akses']] ?></li>
                                                                <?php } ?>
                                                              </ol>

                                                              </td>

                                                            <td><?php echo @$status[$d1['status_akses']] ?></td>
                                                            <td>


                                                                <div role="group" class="btn-group-sm btn-group" >
                                                                
                                                                    <button type="button" class="btn btn-outline-info btn-xs" data-toggle="modal" data-target="#edit" onclick="edit('<?php echo $d1['id_user'] ?>')">
                                                            Edit
                                                          </button>
                                                                    <button type="button" class="btn btn-outline-info btn-xs" data-toggle="modal" data-target="#edit_login" onclick="edit('<?php echo $d1['id_user'] ?>')" >
                                                            Edit Login
                                                          </button>
                                                                    <button type="button" class="btn btn-outline-info btn-xs" onclick="hapus('<?php echo $d1['id_user'] ?>','<?php echo $d1['nama'] ?>')">
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








