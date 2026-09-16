  <div class="row" id="form_transaksi_gym" >
    <div class="col-md-12">
      <ul class="list-group list-group-flush">
        <li class="active list-group-item">
          <div class="widget-content p-0">
            <div class="widget-content-wrapper">
              <div class="widget-content-left">
                  <h5 class="list-group-item-heading"><span id="nama_fasilitas"></span></h5>
              </div>
              <div class="widget-content-right">
                <input type="hidden" id="input_fasilitas" name="" readonly>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" href="#modal_new_transaction">Ganti Fasilitas</button>
                  
              </div>
            </div>
          </div>
         </li>
       </ul>

       <div id="detail_order_fasilitas">
         
       </div>
     <!--  <ul class="list-group list-group-flush">
          <li class="list-group-item">
                <div class="form-group" id="form_jenis_pilihan">
                      <label id="nama_pilihan"></label>
                      <select class="form-control" name="jenis_pilihan" id="jenis_pilihan">
                        
                      </select>
                    </div>
          </li>
          <li class="list-group-item" id="form_1">
              <div class="widget-content p-0">
                  <div class="widget-content-wrapper">
                      <div class="widget-content-left">
                          <div class="widget-heading">Paket : <span  id="nama_paket_fasilitas"></span></div> 
                          <div class="widget-subheading"> Masa Aktif : <span id="masa_aktif"></span></div>
                      </div>
                      <div class="widget-content-right">
                          Aktif Sampai : <span id="tgl_masa_aktif"></span>
                      </div>
                  </div>
              </div>
          </li>
          <li class="list-group-item">
            <span style="color:blue">Jika futsal dan badminton</span>
               <div class="form-group" id="form_jenis_member">
                      <label>Tanggal</label>
                      <input class="form-control" type="date" name="jenis_member_gym" id="jenis_member_gym">
                      
                    </div>
               <div class="form-group" id="form_jenis_member">
                      <label>Jam</label> <br>
                      List Jam
                      
                    </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <div class="col-md-6 col-lg-6" style="border">
                  <div class="card-shadow-primary border widget-chart widget-chart2 text-left card">
                      <div class="widget-content p-0 w-100">
                          <div class="widget-content-outer">
                              <div class="widget-content-left">
                                  <div class="text-muted opacity-6">Biaya Paket</div>
                              </div>
                              <div class="widget-content-wrapper">
                                  <div class="widget-content-left">
                                      <div class="widget-numbers mt-0 fsize-3 text-danger" id="biaya_paket">0</div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6 col-lg-6">
                  <div class="card-shadow-success border widget-chart widget-chart2 text-left card">
                      <div class="widget-content p-0 w-100">
                          <div class="widget-content-outer">
                              <div class="widget-content-left">
                                  <div class="text-muted opacity-6">Registrasi</div>
                              </div>
                              <div class="widget-content-wrapper">
                                  <div class="widget-content-left pr-2">
                                      <div class="widget-numbers mt-0 fsize-3 text-success" id="biaya_regis">0</div>
                                  </div>
                                 
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6 col-lg-12">
                  <div class="card-shadow-warning mt-3 mb-2 border widget-chart widget-chart2 text-left card">
                      <div class="widget-content p-0 w-100">
                          <div class="widget-content-outer">
                              <div class="widget-content-left fsize-1">
                                  <div class="text-muted opacity-6">Total</div>
                              </div>
                              <div class="widget-content-wrapper">
                                  <div class="widget-content-left pr-2 fsize-1">
                                      <div class="widget-numbers mt-0 fsize-3 text-warning" id="total">0</div>
                                  </div>
                               
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
          </li>
        </ul> -->
    </div>
    <div class="col-md-7">


          <ul class="list-group list-group-flush">

            <li class="active list-group-item">
              <div class="widget-content p-0">
                  <div class="widget-content-wrapper">
                      <div class="widget-content-left">
                          <h5 class="list-group-item-heading">Identitas Member</h5>
                      </div>
                  </div>
              </div>
            </li>
            <li class="list-group-item">


                        


            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>No Identitas</label>
                  <div class="row">
                    <div class="col-md-4">
                      <select class="form-control" name="jenis_identitas" id="input_jenis_identitas">
                        <option>KTP</option>
                        <option>SIM</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <input type="text" name="no_identitas" id="input_no_identitas" class="form-control">
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Jenis Kelamin</label>
                  <select name="jk" id="input_jk" class="form-control">
                    <option>Laki laki</option>
                    <option>Perempuan</option>
                  </select>
                </div>
                 <div class="form-group">
                  <label>Tempat Lahir</label>
                  <input type="text" name="tmpl" id="input_tmpl" class="form-control">
                </div>
                <div class="form-group">
                  <label>Tanggal Lahir</label>
                  <div class="row">
                    <div class="col-md-3">
                      <select class="form-control" name="tgll" id="input_tgll">
                        <?php for ($i=1; $i <= 31 ; $i++) { ?>
                        <option><?php echo $i ?></option>
                        <?php } ?>
                      </select>
                      
                    </div>
                    <div class="col-md-6">
                      <select class="form-control" name="blll" id="input_blll">
                        <?php for ($i=1; $i <= 12 ; $i++) { ?>
                        <option value="<?php echo $i ?>"><?php echo bulan_global($i) ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <select class="form-control" name="thll" id="input_thll">
                        <?php for ($i=date('Y'); $i >= 1945 ; $i--) { ?>
                        <option><?php echo $i ?></option>
                        <?php } ?>
                      </select>
                      
                    </div>
                  </div>
                </div>
              <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat" id="input_alamat" class="form-control">
              </div>
            </div>

            <div class="col-md-6">

              <div class="form-group">
                <label>No HP</label>
                <input type="text" name="nohp" id="input_nohp" class="form-control">
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" id="input_email" class="form-control">
              </div>
              <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" name="pekerjaan" id="input_pekerjaan" class="form-control">
              </div>
              <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="ig" id="input_ig" class="form-control">
              </div>
               <div class="form-group">
                <label>Upload Foto</label>
                <button class="btn btn-danger btn-xs" onclick="ambil_foto()" type="button">Ambil Foto</button>
                <input type="file" name="berkas" id="berkas" accept="image/*" > 
                <br>
                <img src="" class="show_foto" width="100%">
                <input type="hidden" name="mode_foto" id="mode_foto">
              </div>
            </div>
          </div>

        </li>
              
      </ul>
    </div>
     <div class="col-md-12 col-lg-12">
        <button class="btn btn-block btn-info mt-3" onclick="konfirmasi_gym()" >Konfirmasi</button>
    </div>
  </div>
                                                    
<?php $this->load->view('user/gro/transaction/js_gym') ?>