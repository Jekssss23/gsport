


<div class="modal fade" id="filter_harian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/office/pendapatan') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Pilih Tanggal</label>
                <input type="hidden" name="filter" class="form-control" value="harian">
                <input type="date" name="tgl" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Kasir</label>
                <select name="id_kasir" class="form-control">
                  <option value="Semua">Semua Kasir</option>
                  <?php   foreach ($kasir as $k => $v) { ?>
                    <option value="<?php echo $v['id_user'] ?>"><?php echo $v['nama'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="filter_periode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/office/pendapatan') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter periode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Pilih Tanggal Awal</label>
                <input type="hidden" name="filter" class="form-control" value="periode">
                <input type="date" name="tgl_awal" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Pilih Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Kasir</label>
                <select name="id_kasir" class="form-control">
                  <option value="Semua">Semua Kasir</option>
                  <?php   foreach ($kasir as $k => $v) { ?>
                    <option value="<?php echo $v['id_user'] ?>"><?php echo $v['nama'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="filter_bulanan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/office/pendapatan') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Pilih Bulan</label>
                <input type="hidden" name="filter" class="form-control" value="bulanan">
                 <select class="form-control" name="bulan">
                  <?php for ($i=1; $i <= 12 ; $i++) { ?>
                  <option value="<?php echo $i ?>"><?php echo bulan_global($i) ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Pilih Tahun</label>
                 <select class="form-control" name="tahun">
                  <?php for ($i=date('Y'); $i >= 2020 ; $i--) { ?>
                  <option><?php echo $i ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Kasir</label>
                <select name="id_kasir" class="form-control">
                  <option value="Semua">Semua Kasir</option>
                  <?php   foreach ($kasir as $k => $v) { ?>
                    <option value="<?php echo $v['id_user'] ?>"><?php echo $v['nama'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="filter_tahunan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/office/pendapatan') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              
              <div class="form-group">
                <input type="hidden" name="filter" class="form-control" value="tahunan">
                <label>Pilih Tahun</label>
                 <select class="form-control" name="tahun">
                  <?php for ($i=date('Y'); $i >= 2020 ; $i--) { ?>
                  <option><?php echo $i ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Kasir</label>
                <select name="id_kasir" class="form-control">
                  <option value="Semua">Semua Kasir</option>
                  <?php   foreach ($kasir as $k => $v) { ?>
                    <option value="<?php echo $v['id_user'] ?>"><?php echo $v['nama'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>







<div class="modal fade" id="filter_grafik_harian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/office/grafik') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Pilih Tanggal</label>
                <input type="hidden" name="filter" class="form-control" value="harian">
                <input type="date" name="tgl" class="form-control" required>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="filter_grafik_periode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/office/grafik') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter periode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Pilih Tanggal Awal</label>
                <input type="hidden" name="filter" class="form-control" value="periode">
                <input type="date" name="tgl_awal" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Pilih Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" required>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="filter_grafik_bulanan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/office/grafik') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Pilih Bulan</label>
                <input type="hidden" name="filter" class="form-control" value="bulanan">
                 <select class="form-control" name="bulan">
                  <?php for ($i=1; $i <= 12 ; $i++) { ?>
                  <option value="<?php echo $i ?>"><?php echo bulan_global($i) ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Pilih Tahun</label>
                 <select class="form-control" name="tahun">
                  <?php for ($i=date('Y'); $i >= 2020 ; $i--) { ?>
                  <option><?php echo $i ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>

<div class="modal fade" id="filter_grafik_tahunan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?php echo base_url('user/office/grafik') ?>" method='get'> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              
              <div class="form-group">
                <input type="hidden" name="filter" class="form-control" value="tahunan">
                <label>Pilih Tahun</label>
                 <select class="form-control" name="tahun">
                  <?php for ($i=date('Y'); $i >= 2020 ; $i--) { ?>
                  <option><?php echo $i ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <button class="btn  btn-outline-info btn-block">Filter</button>
              </div>
                </div>
               
        
        </div>
    </div>
  </form>
</div>



<div class="modal fade" id="edit_metode_pembayaran" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Metode Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <b>Identitas Transaksi</b>
                  <div class="form-group">
                    <table class="table">
                      <tr>
                        <td>No Transaksi</td>
                        <td>:</td>
                        <td id="no_transaksi"></td>
                      </tr>
                      <tr>
                        <td>Waktu Transaksi</td>
                        <td>:</td>
                        <td id="waktu_transaksi"></td>
                      </tr>
                      <tr>
                        <td>Fasilitas</td>
                        <td>:</td>
                        <td id="fasilitas"></td>
                      </tr>
                      <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td id="keterangan"></td>
                      </tr>
                      <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td id="pembayaran"></td>
                      </tr>
                     <!--  <tr>
                        <td>Pendapatan</td>
                        <td>:</td>
                        <td id="pendapatan"></td>
                      </tr> -->
                      <tr>
                        <td>Metode Pembayaran Sebelumnya</td>
                        <td>:</td>
                        <td id="metode_pembayaran"></td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class="col-md-6">
                  <b>Edit Metode Pembayaran</b>
                  <form id="form_edit_metode_pembayaran">
                  </form>
                </div>
              </div>
              
            </div>
               
        
        </div>
    </div>
</div>




<!-- moda print transaksi online -->
<div class="modal fade" id="print_transaksi" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Print</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_konfirmasi_reg_online">
               <div class="modal-body" id="struk">
              
              </div>
            </form>
           
        </div>
    </div>
</div>


<!-- modal fasilitas gym -->
<script src="<?php echo base_url(); ?>assets/jquery_number/jquery.number.js"></script>
<script type="text/javascript">
showAutoCurrency();
    
  function showAutoCurrency(){
    $('input.currency').number( true, 0 );
  }

</script>