<?php
$format_shift = function($code) use ($shift_definitions) {
  if (!$code || $code === 'off') return 'Libur';
  if (isset($shift_definitions[$code])) {
    $d = $shift_definitions[$code];
    return $d['label'] . ' (' . $d['start'] . '–' . $d['end'] . ')';
  }
  return htmlspecialchars($code);
};
?>
<!-- Location Settings Card -->
<div class="bg-black/60 border border-gscRed/30 rounded-xl p-5 shadow-lg shadow-black/40 mb-6">
  <div class="flex items-center gap-4 mb-4">
    <div class="p-3 bg-gscRed/20 rounded-lg">
      <svg class="w-6 h-6 text-gscRed" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
      </svg>
    </div>
    <div class="flex-1">
      <h3 class="text-lg font-semibold text-white">Lokasi Absensi Aktif</h3>
      <p class="text-sm text-gray-400">
        <?php if ($settings): ?>
          <?php echo $settings['location_name']; ?> | Radius: <?php echo $settings['radius_meters']; ?>m | Toleransi telat: <?php echo isset($settings['grace_period_minutes']) ? (int)$settings['grace_period_minutes'] : 15; ?> menit
          <br>
          <span class="text-xs">Status hadir/terlambat mengikuti jadwal shift per karyawan (Employee Management)</span>
          <br>
          <span class="text-xs">Koordinat: <?php echo $settings['latitude']; ?>, <?php echo $settings['longitude']; ?></span>
        <?php else: ?>
          Belum diatur
        <?php endif; ?>
      </p>
    </div>
    <a href="<?php echo base_url('user/big_data/attendance_settings'); ?>" class="bg-gscRed hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
      Pengaturan Lokasi
    </a>
  </div>
</div>

<!-- Attendance Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-black/60 border border-green-500/30 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Hadir Hari Ini</div>
    <div class="text-2xl font-semibold text-green-400"><?php echo isset($stats['today']['hadir']) ? $stats['today']['hadir'] : 0; ?></div>
    <div class="text-[11px] text-gray-400 mt-1">Check-in berhasil</div>
  </div>
  <div class="bg-black/60 border border-yellow-500/30 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Terlambat</div>
    <div class="text-2xl font-semibold text-yellow-400"><?php echo isset($stats['today']['terlambat']) ? $stats['today']['terlambat'] : 0; ?></div>
    <div class="text-[11px] text-gray-400 mt-1">Melewati batas waktu</div>
  </div>
  <div class="bg-black/60 border border-blue-500/30 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Izin</div>
    <div class="text-2xl font-semibold text-blue-400"><?php echo isset($stats['today']['izin']) ? $stats['today']['izin'] : 0; ?></div>
    <div class="text-[11px] text-gray-400 mt-1">Sedang izin</div>
  </div>
  <div class="bg-black/60 border border-red-500/30 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Alpha</div>
    <div class="text-2xl font-semibold text-red-400"><?php echo isset($stats['today']['alpha']) ? $stats['today']['alpha'] : 0; ?></div>
    <div class="text-[11px] text-gray-400 mt-1">Tidak hadir</div>
  </div>
</div>

<!-- Weekly Trend Chart -->
<div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40 mb-6">
  <h3 class="text-sm font-semibold text-white mb-4">Trend Kehadiran 7 Hari Terakhir</h3>
  <div class="relative h-48">
    <canvas id="attendanceTrendChart"></canvas>
  </div>
</div>

<!-- Alpha otomatis info -->
<div class="bg-red-950/40 border border-red-500/30 rounded-xl p-4 shadow-lg shadow-black/40 mb-6">
  <p class="text-sm text-red-200">
    <strong class="text-white">Alpha otomatis:</strong> Karyawan dengan jadwal shift di hari kerja yang tidak melakukan absensi akan dicatat status <span class="text-red-400 font-semibold">ALPHA</span>
    setelah shift selesai (+ toleransi) atau jam cutoff malam (pengaturan absensi).
    Diproses otomatis saat halaman ini dibuka (kemarin &amp; hari ini). Gunakan tombol <em>Proses Alpha Kemarin</em> untuk paksa ulang.
  </p>
</div>

<!-- Filters and Actions -->
<div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-white">Filter Data</h3>
    <div class="flex flex-wrap gap-2">
      <form method="POST" action="<?php echo base_url('user/big_data/attendance_sync_alpha'); ?>" class="inline">
        <input type="hidden" name="sync_date" value="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
        <input type="hidden" name="force" value="1">
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
          Proses Alpha Kemarin
        </button>
      </form>
      <form method="POST" action="<?php echo base_url('user/big_data/attendance_archive'); ?>" class="inline">
        <input type="hidden" name="archive_date" value="<?php echo date('Y-m-d'); ?>">
        <button type="submit" onclick="return confirm('Arsipkan data absensi hari ini? Alpha otomatis akan diproses terlebih dahulu.')" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
          Arsipkan Hari Ini
        </button>
      </form>
    </div>
  </div>
  <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
      <label class="block text-xs text-gray-400 mb-1">Dari Tanggal</label>
      <input type="date" name="date_from" value="<?php echo $current_date_from; ?>" 
             class="w-full bg-black/20 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-gscRed focus:outline-none">
    </div>
    <div>
      <label class="block text-xs text-gray-400 mb-1">Sampai Tanggal</label>
      <input type="date" name="date_to" value="<?php echo $current_date_to; ?>" 
             class="w-full bg-black/20 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-gscRed focus:outline-none">
    </div>
    <div>
      <label class="block text-xs text-gray-400 mb-1">Status</label>
      <select name="status" class="w-full bg-black/20 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-gscRed focus:outline-none">
        <option value="all" <?php echo ($current_status == 'all') ? 'selected' : ''; ?>>Semua Status</option>
        <option value="hadir" <?php echo ($current_status == 'hadir') ? 'selected' : ''; ?>>Hadir</option>
        <option value="terlambat" <?php echo ($current_status == 'terlambat') ? 'selected' : ''; ?>>Terlambat</option>
        <option value="izin" <?php echo ($current_status == 'izin') ? 'selected' : ''; ?>>Izin</option>
        <option value="alpha" <?php echo ($current_status == 'alpha') ? 'selected' : ''; ?>>Alpha</option>
      </select>
    </div>
    <div>
      <label class="block text-xs text-gray-400 mb-1">Cari Nama</label>
      <div class="relative">
        <input type="text" name="search" value="<?php echo $search_term; ?>" 
               placeholder="Cari karyawan..." 
               class="w-full bg-black/20 border border-gray-700 rounded-lg pl-8 pr-3 py-2 text-white text-sm focus:border-gscRed focus:outline-none">
        <svg class="absolute left-2 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </div>
    <div class="md:col-span-4">
      <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        Filter
      </button>
    </div>
  </form>
</div>

<!-- Today's Attendances -->
<?php if (!empty($today_attendances)): ?>
<div class="bg-black/60 border border-gray-800 rounded-xl overflow-hidden shadow-lg shadow-black/40 mb-6">
  <div class="px-6 py-4 border-b border-gray-800">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Absensi Hari Ini</h3>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-[11px] text-gray-400 uppercase bg-black/40">
          <th class="px-6 py-3 font-medium border-b border-gray-800">Karyawan</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Check In</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Check Out</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Status</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Lokasi</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Aksi</th>
        </tr>
      </thead>
      <tbody class="text-sm divide-y divide-gray-800">
        <?php foreach ($today_attendances as $attendance): ?>
          <tr class="hover:bg-white/5 transition">
            <td class="px-6 py-4">
              <div class="font-medium text-white"><?php echo $attendance['display_name'] ?? $attendance['employee_name']; ?></div>
              <div class="text-[10px] text-gray-500"><?php echo $attendance['jabatan'] ?? ''; ?></div>
            </td>
            <td class="px-6 py-4">
              <?php if ($attendance['status'] === 'alpha' && empty($attendance['check_in'])): ?>
                <div class="text-red-400 text-xs font-medium">Tidak absen</div>
              <?php else: ?>
                <div class="text-white"><?php echo $attendance['check_in'] ?: '-'; ?></div>
              <?php endif; ?>
              <?php if (!empty($attendance['schedule_start']) || !empty($attendance['schedule_shift'])): ?>
                <div class="text-[10px] text-gray-500 mt-0.5">
                  Jadwal: <?php echo !empty($attendance['schedule_shift']) ? $format_shift($attendance['schedule_shift']) : '-'; ?>
                  <?php if (!empty($attendance['schedule_start']) || !empty($attendance['schedule_end'])): ?>
                    · Masuk <?php echo !empty($attendance['schedule_start']) ? substr($attendance['schedule_start'], 0, 5) : '-'; ?>
                    / Pulang <?php echo !empty($attendance['schedule_end']) ? substr($attendance['schedule_end'], 0, 5) : '-'; ?>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              <?php if ($attendance['status'] === 'alpha' && !empty($attendance['notes'])): ?>
                <div class="text-[10px] text-red-400/80 mt-0.5">Otomatis</div>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <?php if (!empty($attendance['check_out'])): ?>
                <div class="text-white"><?php echo substr($attendance['check_out'], 0, 5); ?></div>
              <?php elseif ($attendance['status'] === 'alpha'): ?>
                <div class="text-gray-500">-</div>
              <?php elseif (!empty($attendance['check_in']) && empty($attendance['check_out'])): ?>
                <div class="text-yellow-400 text-xs">Belum pulang</div>
              <?php else: ?>
                <div class="text-white">-</div>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <?php 
                if ($attendance['status'] == 'fake gps') {
                  $status_bg = 'bg-red-600/30';
                  $status_text = 'text-red-500';
                  $status_border = 'border-red-600/50';
                  $status_label = 'FAKE GPS';
                } elseif ($attendance['status'] == 'alpha') {
                  $status_bg = 'bg-red-500/10';
                  $status_text = 'text-red-400';
                  $status_border = 'border-red-500/20';
                  $status_label = 'ALPHA';
                } else {
                  $status_bg = 'bg-' . ($attendance['status'] == 'hadir' ? 'green' : ($attendance['status'] == 'terlambat' ? 'yellow' : ($attendance['status'] == 'izin' ? 'blue' : 'red'))) . '-500/10';
                  $status_text = 'text-' . ($attendance['status'] == 'hadir' ? 'green' : ($attendance['status'] == 'terlambat' ? 'yellow' : ($attendance['status'] == 'izin' ? 'blue' : 'red'))) . '-400';
                  $status_border = 'border-' . ($attendance['status'] == 'hadir' ? 'green' : ($attendance['status'] == 'terlambat' ? 'yellow' : ($attendance['status'] == 'izin' ? 'blue' : 'red'))) . '-500/20';
                  $status_label = ucfirst($attendance['status']);
                }
              ?>
              <span class="px-2 py-0.5 <?php echo "$status_bg $status_text $status_border"; ?> rounded-full text-[10px] font-bold tracking-widest uppercase">
                <?php echo $status_label; ?>
              </span>
            </td>
            <td class="px-6 py-4">
              <?php if ($attendance['status'] == 'fake gps'): ?>
                <div class="text-[10px] text-red-500 font-bold bg-red-500/10 inline-block px-2 py-1 rounded">FAKE GPS</div>
              <?php elseif ($attendance['distance_meters']): ?>
                <div class="text-[10px] text-gray-400"><?php echo round($attendance['distance_meters']); ?>m dari <?php echo $attendance['location_name'] ?: 'G Sports Center'; ?></div>
              <?php else: ?>
                <div class="text-[10px] text-gray-500">-</div>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2 items-center">
                <?php if (!empty($attendance['selfie_url']) || !empty($attendance['photoBase64'])): ?>
                  <button onclick="viewSelfie('<?php echo !empty($attendance['selfie_url']) ? $attendance['selfie_url'] : $attendance['photoBase64']; ?>')" class="text-blue-400 hover:text-blue-300 text-xs font-medium transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"></path></svg>
                    Selfie
                  </button>
                <?php endif; ?>
                <form method="POST" action="<?php echo base_url('user/big_data/attendance_delete/' . $attendance['id']); ?>" class="inline" onsubmit="return confirm('Hapus data absensi <?php echo htmlspecialchars($attendance['display_name'] ?? $attendance['employee_name']); ?> hari ini?');">
                  <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-medium transition">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- All Attendances Table -->
<div class="bg-black/60 border border-gray-800 rounded-xl overflow-hidden shadow-lg shadow-black/40">
  <div class="px-6 py-4 border-b border-gray-800">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Data Absensi</h3>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-[11px] text-gray-400 uppercase bg-black/40">
          <th class="px-6 py-3 font-medium border-b border-gray-800">Karyawan</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Tanggal</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Check In</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Check Out</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Status</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Lokasi</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Aksi</th>
        </tr>
      </thead>
      <tbody class="text-sm divide-y divide-gray-800">
        <?php if (isset($attendances) && !empty($attendances)): ?>
          <?php foreach ($attendances as $attendance): ?>
            <tr class="hover:bg-white/5 transition">
              <td class="px-6 py-4">
                <div class="font-medium text-white"><?php echo $attendance['display_name'] ?? $attendance['employee_name']; ?></div>
                <div class="text-[10px] text-gray-500"><?php echo $attendance['employee_name_from_db'] ?? ''; ?></div>
              </td>
              <td class="px-6 py-4">
                <div class="text-white"><?php echo date('d M Y', strtotime($attendance['date'])); ?></div>
              </td>
              <td class="px-6 py-4">
                <?php if ($attendance['status'] === 'alpha' && empty($attendance['check_in'])): ?>
                  <div class="text-red-400 text-xs font-medium">Tidak absen</div>
                <?php else: ?>
                  <div class="text-white"><?php echo $attendance['check_in'] ?: '-'; ?></div>
                <?php endif; ?>
                <?php if (!empty($attendance['schedule_start']) || !empty($attendance['schedule_shift'])): ?>
                  <div class="text-[10px] text-gray-500 mt-0.5">
                    Jadwal: <?php echo !empty($attendance['schedule_shift']) ? $format_shift($attendance['schedule_shift']) : '-'; ?>
                    <?php if (!empty($attendance['schedule_start']) || !empty($attendance['schedule_end'])): ?>
                      · Masuk <?php echo !empty($attendance['schedule_start']) ? substr($attendance['schedule_start'], 0, 5) : '-'; ?>
                      / Pulang <?php echo !empty($attendance['schedule_end']) ? substr($attendance['schedule_end'], 0, 5) : '-'; ?>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4">
                <?php if (!empty($attendance['check_out'])): ?>
                  <div class="text-white"><?php echo substr($attendance['check_out'], 0, 5); ?></div>
                <?php elseif ($attendance['status'] === 'alpha'): ?>
                  <div class="text-gray-500">-</div>
                <?php elseif (!empty($attendance['check_in']) && empty($attendance['check_out'])): ?>
                  <div class="text-yellow-400 text-xs">Belum pulang</div>
                <?php else: ?>
                  <div class="text-white">-</div>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4">
                <?php 
                  if ($attendance['status'] == 'fake gps') {
                    $status_bg = 'bg-red-600/30';
                    $status_text = 'text-red-500';
                    $status_border = 'border-red-600/50';
                    $status_label = 'FAKE GPS';
                  } elseif ($attendance['status'] == 'alpha') {
                    $status_bg = 'bg-red-500/10';
                    $status_text = 'text-red-400';
                    $status_border = 'border-red-500/20';
                    $status_label = 'ALPHA';
                  } else {
                    $status_bg = 'bg-' . ($attendance['status'] == 'hadir' ? 'green' : ($attendance['status'] == 'terlambat' ? 'yellow' : ($attendance['status'] == 'izin' ? 'blue' : 'red'))) . '-500/10';
                    $status_text = 'text-' . ($attendance['status'] == 'hadir' ? 'green' : ($attendance['status'] == 'terlambat' ? 'yellow' : ($attendance['status'] == 'izin' ? 'blue' : 'red'))) . '-400';
                    $status_border = 'border-' . ($attendance['status'] == 'hadir' ? 'green' : ($attendance['status'] == 'terlambat' ? 'yellow' : ($attendance['status'] == 'izin' ? 'blue' : 'red'))) . '-500/20';
                    $status_label = ucfirst($attendance['status']);
                  }
                ?>
                <span class="px-2 py-0.5 <?php echo "$status_bg $status_text $status_border"; ?> rounded-full text-[10px] font-bold tracking-widest uppercase">
                  <?php echo $status_label; ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <?php if ($attendance['status'] == 'fake gps'): ?>
                  <div class="text-[10px] text-red-500 font-bold bg-red-500/10 inline-block px-2 py-1 rounded">FAKE GPS</div>
                <?php elseif ($attendance['distance_meters']): ?>
                  <div class="text-[10px] text-gray-400"><?php echo round($attendance['distance_meters']); ?>m dari <?php echo $attendance['location_name'] ?: 'G Sports Center'; ?></div>
                <?php else: ?>
                  <div class="text-[10px] text-gray-500">-</div>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4">
                <div class="flex gap-2 items-center">
                  <?php if (!empty($attendance['selfie_url']) || !empty($attendance['photoBase64'])): ?>
                    <button onclick="viewSelfie('<?php echo !empty($attendance['selfie_url']) ? $attendance['selfie_url'] : $attendance['photoBase64']; ?>')" class="text-blue-400 hover:text-blue-300 text-xs font-medium transition flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                      Selfie
                    </button>
                  <?php endif; ?>
                  <form method="POST" action="<?php echo base_url('user/big_data/attendance_delete/' . $attendance['id']); ?>" class="inline" onsubmit="return confirm('Hapus data absensi <?php echo htmlspecialchars($attendance['display_name'] ?? $attendance['employee_name']); ?> pada tanggal <?php echo date('d M Y', strtotime($attendance['date'])); ?>?');">
                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-medium transition">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
              Belum ada data absensi
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Selfie -->
<div id="selfieModal" class="hidden fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4">
  <div class="bg-gray-900 border border-gray-700 rounded-xl p-4 max-w-sm w-full shadow-2xl relative">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-white font-bold text-sm">Bukti Selfie Kehadiran</h3>
      <button onclick="closeSelfieModal()" class="text-red-500 hover:text-red-400">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>
    </div>
    <div class="bg-black rounded-lg overflow-hidden flex items-center justify-center border border-gray-800">
      <img id="selfieImg" src="" alt="Selfie Absen" class="max-h-[60vh] object-contain">
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function viewSelfie(base64) {
  document.getElementById('selfieImg').src = base64;
  document.getElementById('selfieModal').classList.remove('hidden');
}
function closeSelfieModal() {
  document.getElementById('selfieModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // Weekly Trend Chart
    const ctx = document.getElementById('attendanceTrendChart');
    if (ctx) {
        <?php if (isset($stats['weekly_trend']) && !empty($stats['weekly_trend'])): ?>
            // Process weekly trend data
            const trendData = <?php echo json_encode($stats['weekly_trend']); ?>;
            const last7Days = [];
            const hadirData = [];
            const terlambatData = [];
            
            // Generate last 7 days
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toISOString().split('T')[0];
                last7Days.push(date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' }));
                
                // Find data for this date
                const dayData = trendData.filter(item => item.date === dateStr);
                const hadirCount = dayData.filter(item => item.status === 'hadir').reduce((sum, item) => sum + item.count, 0);
                const terlambatCount = dayData.filter(item => item.status === 'terlambat').reduce((sum, item) => sum + item.count, 0);
                
                hadirData.push(hadirCount);
                terlambatData.push(terlambatCount);
            }
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: last7Days,
                    datasets: [{
                        label: 'Hadir',
                        data: hadirData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Terlambat',
                        data: terlambatData,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#e5e7eb',
                                font: { size: 10 }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { 
                                color: '#9ca3af', 
                                font: { size: 10 } 
                            },
                            grid: { 
                                color: 'rgba(156, 163, 175, 0.1)' 
                            }
                        },
                        y: {
                            ticks: { 
                                color: '#9ca3af', 
                                font: { size: 10 } 
                            },
                            grid: { 
                                color: 'rgba(156, 163, 175, 0.1)' 
                            }
                        }
                    }
                }
            });
        <?php else: ?>
            // Show no data message
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Belum ada data trend',
                            color: '#9ca3af'
                        }
                    }
                }
            });
        <?php endif; ?>
    }
});
</script>
