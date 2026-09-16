  <div class="row" id="form_transaksi_gym" >
    <div class="col-md-12">
      <ul class="list-group list-group-flush">
        <li class="active list-group-item">
          <div class="widget-content p-0">
            <div class="widget-content-wrapper">
              <div class="widget-content-left">
                  <h5 class="list-group-item-heading"><span id="nama_fasilitas">Pilih Fasilitas Anda</span></h5>
              </div>
              <div class="widget-content-right">
                <input type="hidden" id="input_fasilitas" name="" readonly>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" href="#modal_new_transaction" id="tombol_ganti_fasilitas">Ganti Fasilitas</button>
                  
              </div>
            </div>
          </div>
         </li>
       </ul>

       <div id="detail_order_fasilitas" class="mt-3">
         
       </div>
  
    </div>
  </div>
                                                    
<?php $this->load->view('user/gro/transaction/js_gym') ?>