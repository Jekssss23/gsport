<link href="<?php echo base_url() ?>assets/lightbox2/dist/css/lightbox.css" rel="stylesheet" />
<script src="<?php echo base_url() ?>assets/lightbox2/src/js/lightbox.js"></script>


  <div class="main-card mb-3 card">
                                          <div class="card-header">
                                            <div id="header"></div>
                                            <div class="btn-actions-pane-right">
                                                <div class="nav">
                                                    <a href="<?php echo base_url('user/admin/atur_lapangan_atas') ?>" class="border-0 btn-transition btn btn-outline-primary">Atur Lapangan </a>
                                                    <a data-toggle="tab" href="#tab_jenis_member" id="tombol_tab_membership" class="border-0 btn-transition active btn btn-outline-primary" onclick="jenis_pickle()">Setting</a>
                                                    <a data-toggle="tab" href="#tab_galeri"  id="tombol_tab_galeri" class="mr-1 ml-1 border-0 btn-transition  btn btn-outline-primary" onclick="galeri()">Galeri Foto</a>
                                            
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_jenis_member" role="tabpanel">
                                                  <?php echo $this->session->flashdata('pesan') ?>
                                                  <div class="row">
                                                    <div class="col-md-8">
                                                      <table class="table table-striped table-bordered">
                                                        <thead>
                                                          <tr>
                                                            <td width="20px">No</td>
                                                            <td>Keterangan</td>
                                                            <td>Harga</td>
                                                            <td>Jam Mulai</td>
                                                            <td>Jam Akhir</td>
                                                          
                                                            <td width="90px">Option</td>
                                                          </tr>
                                                        </thead>
                                                        <?php 
                                                        $no=1;
                                                        foreach ($pickle as $d1) { ?>
                                                          <tr>
                                                            <td><?php echo $no++ ?></td>

                                                            <td><?php echo $d1['keterangan'] ?></td>
                                                            <td><?php echo number_format($d1['harga']) ?></td>
                                                            <td><?php echo $d1['jam_mulai'] ?></td>
                                                            <td><?php echo $d1['jam_berakhir'] ?></td>
                                                            <td>


                                                                <div role="group" class="btn-group-sm btn-group">
                                                                    <button type="button" class="btn btn-outline-info btn-xs" data-toggle="modal" data-target="#edit" onclick="edit('<?php echo $d1['id_pickle'] ?>')">
                                                            Edit
                                                          </button>
                                                                </div>


                                                             
                                                            
                                                            </td>
                                                          </tr>
                                                        <?php } ?>
                                                        
                                                      </table>
                                                    </div>
                                                    <div class="col-md-4">
                                                      



                                                      <ul class="list-group">
                                                <li class="list-group-item">
                                                    <div class="widget-content p-0">
                                                        <div class="widget-content-wrapper">
                                                           
                                                            <div class="widget-content-left">
                                                                <div class="widget-heading">Diskon Member Bulanan</div>
                                                                <div class="widget-subheading"><?php echo $diskon_pickle ?> %</div>
                                                            </div>
                                                            <div class="widget-content-right">
                                                                <div role="group" class="btn-group-sm btn-group">
                                                                    <button type="button" class="btn-shadow btn btn-outline-primary"  data-toggle="modal" data-target="#edit_diskon_member" onclick="edit_diskon()">Edit</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                
                                            </ul>



                                                    </div>
                                                  </div>
                                                
                                                   
                                                  <hr>
                                                  <b>Paket turnamen</b> <br>
                                                  <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#tambah_pickle_turnamen">Tambah</button>
                                                  <br>
                                                  <br>
                                                   <table class="table table-striped table-bordered">
                                                    <thead>
                                                      <tr>
                                                        <td width="20px">No</td>
                                                        <td>Paket</td>
                                                        <td>Keterangan</td>
                                                        <td>Harga</td>
                                                      
                                                        <td width="90px">Option</td>
                                                      </tr>
                                                    </thead>
                                                    <?php 
                                                    $no=1;
                                                    foreach ($pickle_turnamen as $d1) { ?>
                                                      <tr>
                                                        <td><?php echo $no++ ?></td>

                                                        <td><?php echo $d1['paket'] ?></td>
                                                        <td><?php echo $d1['keterangan'] ?></td>
                                                        <td><?php echo number_format($d1['harga']) ?></td>
                                                        <td>

                                                          <div role="group" class="btn-group-sm btn-group">
                                                           <button type="button" class="btn btn-outline-info btn-xs" data-toggle="modal" data-target="#edit_pickle_turnamen" onclick="edit_turnamen('<?php echo $d1['id_pickle_turnamen'] ?>')">
                                                           Edit</button>
                                                           <button type="button" class="btn btn-outline-info btn-xs" onclick="hapus_turnamen('<?php echo $d1['id_pickle_turnamen'] ?>','<?php echo $d1['paket'] ?>')">
                                                          Hapus
                                                        </button>
                                                      </div>
                                                        
                                                        </td>
                                                      </tr>
                                                    <?php } ?>
                                                    
                                                  </table>
                                                </div>
                                              
                                                <div class="tab-pane" id="tab_galeri" role="tabpanel">
                                                   <a href="#" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#tambah_foto" style="margin-bottom:10px">Tambah Foto</a> 
                                                   <a href="#" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#lihat_thumbnail" style="margin-bottom:10px">Thumbnail</a> 

                                                  <?php echo $this->session->flashdata('pesan_galeri') ?>


                                                    <div class="row">

                                                  <?php foreach ($galerry as $k => $v) { 
                                                    ?>
                                                      <div class="col-md-3 mb-3 justify-content-center">
                                                        <div class="justify-content-center">
                                                          
                                                       <a href="<?php echo base_url() ?>file/public/pickle/<?php echo $v['file'] ?>"  data-lightbox="image-1" data-title="My Foto" >
                                                         <img width="100%" class="rounded img-thumbnail" src="<?php echo base_url() ?>file/public/pickle/<?php echo $v['file'] ?>" alt="" >
                                                       </a>
                                                       <br>
                                                        <a href="javascript:void(0)" onclick="hapus_galerry('<?php echo $v['id_foto_galery'] ?>','<?php echo $v['file'] ?>')" class="btn btn-outline-danger btn-xs" title="Hapus foto ini" style="left:5px;bottom:31px"><i class="fa fa-trash"></i></a>
                                                           <a href="javascript:void(0)" onclick="jadikan_thumbnail('<?php echo $v['id_foto_galery'] ?>','<?php echo $v['file'] ?>')" class="btn btn-outline-<?php echo $v['status']=='Thumbnail' ? 'success' : 'info' ?> btn-xs" title="<?php echo $v['status']=='Thumbnail' ? 'Thumbnail Aktif' : 'Jadikan Thumbnail' ?> " style="left:5px;bottom:31px"><i class="fa fa-camera"></i></a>
                                                        </div>
                                                      </div>
                                                    <?php } ?>
                                                    </div>
                                                </div>
                                              
                                            </div>
                                        </div>
                                       <!--  <div class="d-block text-right card-footer">
                                            <a href="javascript:void(0);" class="btn-wide btn btn-success">Tambah Jenis Member</a>
                                        </div> -->
                                    </div>








