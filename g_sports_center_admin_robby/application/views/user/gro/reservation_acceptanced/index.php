<div class="row">
  <div class="col-md-12">
    <div class="main-card mb-3 card">
      <div class="card-header">
        Reservation Acceptanced
        <div class="btn-actions-pane-right">
          <div class="d-flex" style="gap: 8px;">
            <a href="<?php echo base_url('user/gro/reservation_acceptanced/sync_firebase') ?>" class="btn btn-info btn-sm" onclick="return confirm('Sync data dari Firebase?')">
              <i class="fa fa-refresh"></i> Sync Firebase
            </a>
            <span class="badge badge-danger">GRO Console</span>
          </div>
        </div>
      </div>
      <div class="card-body">
        <?php echo $this->session->flashdata('pesan') ?>
        <p class="mb-4">
          Panel untuk meng-<strong>accept</strong> atau <strong>reject</strong> reservasi yang datang dari aplikasi mobile.
        </p>

        <div class="row">
          <div class="col-md-4">
            <div class="card mb-3">
              <div class="card-body">
                <div class="text-muted">Waiting Approval</div>
                <div style="font-size: 26px; font-weight: 700; color: #f0ad4e;">
                  <?php echo isset($pending) ? count($pending) : 0; ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card mb-3">
              <div class="card-body">
                <div class="text-muted">Cancellation Requests</div>
                <div style="font-size: 26px; font-weight: 700; color: #dc3545;">
                  <?php echo isset($cancellation_requests) ? count($cancellation_requests) : 0; ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card mb-3">
              <div class="card-body">
                <div class="text-muted">Accepted Today</div>
                <div style="font-size: 26px; font-weight: 700; color: #28a745;">
                  <?php echo isset($accepted_today) ? $accepted_today : 0; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="main-card mb-3 card">
      <div class="card-header">
        Daftar Reservasi Masuk (Pending & Confirmed)
        <div class="btn-actions-pane-right">
          <div class="d-flex" style="gap: 8px;">
            <input type="date" class="form-control form-control-sm">
            <select class="form-control form-control-sm">
              <option>Semua Fasilitas</option>
              <option>Futsal</option>
              <option>Badminton</option>
              <option>Pickle</option>
              <option>Swimming</option>
              <option>Gym</option>
            </select>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Nama</th>
                <th>Fasilitas</th>
                <th>Lapangan</th>
                <th>Payment</th>
                <th>Source</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($reservations)) { ?>
                <?php foreach ($reservations as $row) { ?>
                  <tr>
                    <td>
                      <?php echo $row['created_at']; ?><br>
                      <small class="text-muted"><?php echo $row['booking_date']; ?> | <?php echo implode(', ', $row['time_slots']); ?></small>
                    </td>
                    <td>
                      <?php echo $row['user_name']; ?><br>
                      <small class="text-muted"><?php echo $row['firebase_uid']; ?></small>
                    </td>
                    <td><?php echo $row['facility_name']; ?></td>
                    <td><?php echo $row['court_name']; ?></td>
                    <td>
                      <?php if (!empty($row['payment_proof_url'])) { ?>
                        <button class="btn btn-sm btn-info" onclick="viewPaymentProof('<?php echo $row['payment_proof_url']; ?>', '<?php echo $row['reservation_code']; ?>')">
                          <i class="fa fa-image"></i> Lihat
                        </button>
                      <?php } else { ?>
                        <span class="text-muted">-</span>
                      <?php } ?>
                      <br>
                      <small class="text-muted">Total: Rp <?php echo number_format($row['total_amount']); ?></small>
                    </td>
                    <td>
                      <?php if (isset($row['data_source'])) { ?>
                        <span class="badge badge-<?php echo $row['data_source'] == 'Firebase' ? 'info' : 'secondary'; ?>">
                          <?php echo $row['data_source']; ?>
                        </span>
                      <?php } else { ?>
                        <span class="badge badge-secondary">Local</span>
                      <?php } ?>
                    </td>
                    <td>
                      <span class="badge badge-warning"><?php echo $row['status']; ?></span>
                    </td>
                    <td class="text-right">
                      <button class="btn btn-info btn-sm" onclick="viewDetails(<?php echo $row['id']; ?>)">
                        <i class="fa fa-eye"></i> Detail
                      </button>
                      
                      <?php if ($row['status'] == 'pending') { ?>
                        <a href="<?php echo base_url('user/gro/reservation_acceptanced/accept/'.$row['id']) ?>" class="btn btn-success btn-sm" onclick="return confirm('Accept reservasi ini?')" style="margin-left: 6px;">
                          <i class="fa fa-check"></i> Accept
                        </a>
                      <?php } ?>

                      <?php if ($row['status'] == 'pending') { ?>
                        <form method="post" action="<?php echo base_url('user/gro/reservation_acceptanced/reject/'.$row['id']) ?>" style="display:inline-block; margin-left:6px;">
                          <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason Cancel" style="width:140px; display:inline-block; vertical-align:middle;" required>
                          <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan/Tolak reservasi ini?')">Tolak / Cancel</button>
                        </form>
                      <?php } ?>
                      
                      <?php if ($row['status'] == 'confirmed') { ?>
                        <button class="btn btn-warning btn-sm" onclick="archiveReservation(<?php echo $row['id']; ?>)" style="margin-left: 6px;">
                          <i class="fa fa-archive"></i> Arsip
                        </button>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="8" class="text-center text-muted">Tidak ada reservasi masuk</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Formmerly Confirmed Reservations Section is removed since it's merged above -->

    <!-- Cancellation Requests Section -->
    <?php if (!empty($cancellation_requests)) { ?>
      <div class="main-card mb-3 card">
        <div class="card-header">
          Permintaan Pembatalan
          <div class="btn-actions-pane-right">
            <span class="badge badge-danger">Menunggu Persetujuan</span>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>Waktu Request</th>
                  <th>Nama</th>
                  <th>Fasilitas</th>
                  <th>Alasan</th>
                  <th class="text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cancellation_requests as $row) { ?>
                  <tr>
                    <td>
                      <?php echo $row['cancellation_requested_at']; ?><br>
                      <small class="text-muted"><?php echo $row['booking_date']; ?></small>
                    </td>
                    <td>
                      <?php echo $row['user_name']; ?><br>
                      <small class="text-muted"><?php echo $row['user_phone']; ?></small>
                    </td>
                    <td>
                      <?php echo $row['facility_name']; ?> - <?php echo $row['court_name']; ?><br>
                      <small class="text-muted"><?php echo $row['reservation_code']; ?></small>
                    </td>
                    <td>
                      <span class="text-muted"><?php echo $row['cancellation_reason']; ?></span>
                    </td>
                    <td class="text-right">
                      <button class="btn btn-success btn-sm" onclick="approveCancellation(<?php echo $row['id']; ?>)">
                        <i class="fa fa-check"></i> Setujui
                      </button>
                      <button class="btn btn-danger btn-sm" onclick="rejectCancellation(<?php echo $row['id']; ?>)" style="margin-left: 5px;">
                        <i class="fa fa-times"></i> Tolak
                      </button>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>

<!-- Payment Proof Modal -->
<div class="modal fade" id="paymentProofModal" tabindex="-1" role="dialog" data-backdrop="true" data-keyboard="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bukti Pembayaran - <span id="modalReservationCode"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img id="paymentProofImage" src="" alt="Payment Proof" class="img-fluid" style="max-height: 400px;">
        <div class="mt-3">
          <a id="downloadPaymentProof" href="" target="_blank" class="btn btn-primary">
            <i class="fa fa-download"></i> Download
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Reservation Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" data-backdrop="true" data-keyboard="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Reservasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="detailsContent">
        <div class="text-center">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function viewPaymentProof(imageUrl, reservationCode) {
  $('#paymentProofModal').appendTo('body').off('shown.bs.modal hidden.bs.modal');
  // Reset modal content first
  $('#modalReservationCode').text(reservationCode);
  $('#paymentProofImage').attr('src', '');
  $('#downloadPaymentProof').attr('href', '');
  
  // Show modal
  $('#paymentProofModal').modal('show');
  
  // Load image after modal is shown
  $('#paymentProofModal').on('shown.bs.modal', function () {
    $('#paymentProofImage').attr('src', imageUrl);
    $('#downloadPaymentProof').attr('href', imageUrl);
  });
  
  // Clean up when modal is hidden
  $('#paymentProofModal').on('hidden.bs.modal', function () {
    $('#paymentProofImage').attr('src', '');
    $('#downloadPaymentProof').attr('href', '');
    $(this).off('shown.bs.modal hidden.bs.modal');
  });
}

function viewDetails(reservationId) {
  $('#detailsContent').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
  $('#detailsModal').appendTo('body').off('shown.bs.modal hidden.bs.modal');
  
  $('#detailsModal').modal('show');
  
  $.ajax({
    url: '<?php echo base_url("user/gro/reservation_acceptanced/get_details"); ?>/' + reservationId,
    method: 'GET',
    success: function(response) {
      if (response.ok) {
        var data = response.data;
        var html = '<div class="row">' +
          '<div class="col-md-6"><strong>Kode Reservasi:</strong></div><div class="col-md-6">' + data.reservation_code + '</div>' +
          '<div class="col-md-6"><strong>Nama:</strong></div><div class="col-md-6">' + data.user_name + '</div>' +
          '<div class="col-md-6"><strong>Telepon:</strong></div><div class="col-md-6">' + data.user_phone + '</div>' +
          '<div class="col-md-6"><strong>Firebase UID:</strong></div><div class="col-md-6">' + data.firebase_uid + '</div>' +
          '<div class="col-md-6"><strong>Fasilitas:</strong></div><div class="col-md-6">' + data.facility_name + '</div>' +
          '<div class="col-md-6"><strong>Lapangan:</strong></div><div class="col-md-6">' + data.court_name + '</div>' +
          '<div class="col-md-6"><strong>Tanggal:</strong></div><div class="col-md-6">' + data.booking_date + '</div>' +
          '<div class="col-md-6"><strong>Waktu:</strong></div><div class="col-md-6">' + data.time_range + ' (' + data.total_hours + ' jam)</div>' +
          '<div class="col-md-6"><strong>Total:</strong></div><div class="col-md-6">Rp ' + formatNumber(data.total_amount) + '</div>' +
          '<div class="col-md-6"><strong>DP:</strong></div><div class="col-md-6">Rp ' + formatNumber(data.dp_amount) + '</div>' +
          '<div class="col-md-6"><strong>Sisa:</strong></div><div class="col-md-6">Rp ' + formatNumber(data.remaining_amount) + '</div>' +
          '<div class="col-md-6"><strong>Status:</strong></div><div class="col-md-6"><span class="badge badge-warning">' + data.status + '</span></div>' +
          '<div class="col-md-6"><strong>Sumber:</strong></div><div class="col-md-6"><span class="badge badge-info">' + data.data_source + '</span></div>' +
          '</div>';
          
        if (data.payment_proof_url) {
          html += '<div class="mt-3"><strong>Bukti Pembayaran:</strong><br>' +
            '<img src="' + data.payment_proof_url + '" class="img-thumbnail" style="max-height: 200px; cursor: pointer;" onclick="viewPaymentProof(\'' + data.payment_proof_url + '\', \'' + data.reservation_code + '\')">' +
            '</div>';
        }
        
        $('#detailsContent').html(html);
      } else {
        $('#detailsContent').html('<div class="alert alert-danger">Gagal memuat detail reservasi</div>');
      }
    },
    error: function() {
      $('#detailsContent').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
    }
  });
}

function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function approveCancellation(reservationId) {
  if (confirm('Apakah Anda yakin ingin menyetujui pembatalan reservasi ini?')) {
    window.location.href = '<?php echo base_url("user/gro/reservation_acceptanced/approve_cancellation"); ?>/' + reservationId;
  }
}

function rejectCancellation(reservationId) {
  if (confirm('Apakah Anda yakin ingin menolak pembatalan reservasi ini?')) {
    window.location.href = '<?php echo base_url("user/gro/reservation_acceptanced/reject_cancellation"); ?>/' + reservationId;
  }
}

function archiveReservation(reservationId) {
  if (confirm('Apakah Anda yakin ingin mengarsipkan reservasi ini? Reservasi akan dipindah ke Big Data Reservation Management dan bisa dicetak PDF.')) {
    window.location.href = '<?php echo base_url("user/gro/reservation_acceptanced/archive_reservation"); ?>/' + reservationId;
  }
}

// Desktop/browser notification for new reservations
let lastReservationId = null;
async function pollIncomingReservation() {
  try {
    const res = await fetch('<?php echo base_url("user/gro/reservation_acceptanced/booking_ping"); ?>');
    const json = await res.json();
    if (!json.ok || !json.data || !json.data.id) return;
    if (!lastReservationId) {
      lastReservationId = json.data.id;
      return;
    }
    if (json.data.id !== lastReservationId) {
      lastReservationId = json.data.id;
      const msg = `Booking baru: ${json.data.user_name} - ${json.data.facility_name} (${json.data.reservation_code})`;
      if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('GSC Booking Masuk', { body: msg });
      }
      console.log(msg);
    }
  } catch (e) {}
}

document.addEventListener('DOMContentLoaded', function() {
  $('#detailsModal').appendTo('body');
  $('#paymentProofModal').appendTo('body');
  if ('Notification' in window && Notification.permission !== 'granted') {
    Notification.requestPermission();
  }
  pollIncomingReservation();
  setInterval(pollIncomingReservation, 10000);
});
</script>

