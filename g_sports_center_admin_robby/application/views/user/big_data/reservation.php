<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-white">Channel Distribusi Reservasi</h3>
      <span class="text-[10px] text-gray-400">Mobile vs Web/Walk-in</span>
    </div>
    <div class="h-40">
      <canvas id="chartReservationChannel"></canvas>
    </div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-white">Fasilitas Paling Populer</h3>
      <span class="text-[10px] text-gray-400">Total Booking Minggu Ini</span>
    </div>
    <div class="h-40">
      <canvas id="chartReservationFacility"></canvas>
    </div>
  </div>
</div>

<div class="bg-black/60 border border-gray-800 rounded-xl overflow-hidden shadow-lg shadow-black/40">
  <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Antrian Reservasi Masuk (App)</h3>
    <a href="<?php echo base_url('user/gro/reservation_acceptanced'); ?>" class="text-gscRed hover:text-white text-xs font-bold transition">
      Kelola Semua →
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-[11px] text-gray-400 uppercase bg-black/40">
          <th class="px-6 py-3 font-medium border-b border-gray-800">Kode</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Pelanggan</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Fasilitas</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Jadwal</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Status</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Sumber</th>
        </tr>
      </thead>
      <tbody class="text-sm divide-y divide-gray-800">
        <?php if (isset($pending_reservations) && !empty($pending_reservations)): ?>
          <?php foreach (array_slice($pending_reservations, 0, 5) as $res): ?>
            <tr class="hover:bg-white/5 transition">
              <td class="px-6 py-4 font-mono text-xs text-gscRed"><?php echo $res['reservation_code']; ?></td>
              <td class="px-6 py-4">
                <div class="font-medium text-white"><?php echo $res['user_name']; ?></div>
                <div class="text-[10px] text-gray-500"><?php echo $res['user_phone'] ? $res['user_phone'] : 'No phone'; ?></div>
              </td>
              <td class="px-6 py-4">
                <div class="text-white"><?php echo $res['facility_name']; ?></div>
                <div class="text-[10px] text-gray-500 italic"><?php echo count($res['time_slots']); ?> Jam</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-white"><?php echo date('d M Y', strtotime($res['booking_date'])); ?></div>
                <div class="text-[10px] text-gray-500">
                  <?php 
                  if (!empty($res['time_slots'])) {
                    $start = min($res['time_slots']);
                    $end = max($res['time_slots']) + 1;
                    echo sprintf("%02d:00 - %02d:00", $start, $end);
                  }
                  ?>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-0.5 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded-full text-[10px] font-bold">WAITING APPROVAL</span>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full text-[10px] font-bold">
                  <?php echo $res['data_source']; ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
              Tidak ada reservasi pending saat ini
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Archived Reservations Section -->
<div class="bg-black/60 border border-gray-800 rounded-xl overflow-hidden shadow-lg shadow-black/40 mt-6">
  <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Reservasi Terarsip (Big Data)</h3>
    <span class="text-[10px] text-gray-400">10 reservasi terakhir yang diarsipkan</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-[11px] text-gray-400 uppercase bg-black/40">
          <th class="px-6 py-3 font-medium border-b border-gray-800">Kode</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Pelanggan</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Fasilitas</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Jadwal</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Status</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Diarsipkan</th>
        </tr>
      </thead>
      <tbody class="text-sm divide-y divide-gray-800">
        <?php if (isset($archived_reservations) && !empty($archived_reservations)): ?>
          <?php foreach ($archived_reservations as $res): ?>
            <tr class="hover:bg-white/5 transition">
              <td class="px-6 py-4 font-mono text-xs text-gray-400"><?php echo $res['reservation_code']; ?></td>
              <td class="px-6 py-4">
                <div class="font-medium text-white"><?php echo $res['user_name']; ?></div>
                <div class="text-[10px] text-gray-500"><?php echo $res['user_phone'] ? $res['user_phone'] : 'No phone'; ?></div>
              </td>
              <td class="px-6 py-4">
                <div class="text-white"><?php echo $res['facility_name']; ?></div>
                <div class="text-[10px] text-gray-500 italic"><?php echo count($res['time_slots']); ?> Jam</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-white"><?php echo date('d M Y', strtotime($res['booking_date'])); ?></div>
                <div class="text-[10px] text-gray-500">
                  <?php 
                  if (!empty($res['time_slots'])) {
                    $start = min($res['time_slots']);
                    $end = max($res['time_slots']) + 1;
                    echo sprintf("%02d:00 - %02d:00", $start, $end);
                  }
                  ?>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-0.5 bg-green-500/10 text-green-400 border border-green-500/20 rounded-full text-[10px] font-bold">
                  <?php echo ucfirst($res['status']); ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="text-white"><?php echo date('d M Y', strtotime($res['archived_at'])); ?></div>
                <div class="text-[10px] text-gray-500">oleh <?php echo $res['archived_by_name'] ?? 'System'; ?></div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
              Belum ada reservasi yang diarsipkan
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Channel Distribution Chart
    const ctx1 = document.getElementById('chartReservationChannel')?.getContext('2d');
    if (ctx1 && <?php echo isset($stats['channels']) ? 'true' : 'false'; ?>) {
      new Chart(ctx1, {
        type: 'pie',
        data: {
          labels: <?php echo json_encode(isset($stats['channels']['labels']) ? $stats['channels']['labels'] : ['Mobile App', 'Web / Walk-in']); ?>,
          datasets: [{
            data: <?php echo json_encode(isset($stats['channels']['data']) ? $stats['channels']['data'] : [65, 35]); ?>,
            backgroundColor: ['#e11d48', '#374151']
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right', labels: { color: '#e5e7eb', font: { size: 10 } } }
          }
        }
      });
    }

    // Facility Popularity Chart
    const ctx2 = document.getElementById('chartReservationFacility')?.getContext('2d');
    if (ctx2 && <?php echo isset($stats['facilities']) ? 'true' : 'false'; ?>) {
      new Chart(ctx2, {
        type: 'bar',
        data: {
          labels: <?php echo json_encode(isset($stats['facilities']['labels']) ? $stats['facilities']['labels'] : ['Futsal','Badm.','Pickle','Gym','Swim']); ?>,
          datasets: [{
            label: 'Bookings',
            data: <?php echo json_encode(isset($stats['facilities']['data']) ? $stats['facilities']['data'] : [42, 58, 15, 22, 12]); ?>,
            backgroundColor: '#e11d48'
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { ticks: { color: '#9ca3af', font: { size: 10 } } },
            y: { ticks: { color: '#9ca3af', font: { size: 10 } } }
          }
        }
      });
    }
  });
</script>