<div class="space-y-6">
  <!-- Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-white mb-2">Pengaturan Lokasi Absensi</h1>
      <p class="text-gray-400">Konfigurasi lokasi dan radius untuk absensi GPS karyawan</p>
    </div>
    <a href="<?php echo base_url('user/big_data/attendance'); ?>" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
      ← Kembali
    </a>
  </div>

  <!-- Settings Form -->
  <div class="bg-black/60 border border-gray-800 rounded-xl p-6 shadow-lg shadow-black/40">
    <form method="POST" class="space-y-6">
      <!-- Location Name -->
      <div>
        <label class="block text-sm font-medium text-white mb-2">Nama Lokasi</label>
        <input type="text" name="location_name" value="<?php echo $settings['location_name'] ?? 'G Sports Center'; ?>" 
               class="w-full bg-black/20 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-gscRed focus:outline-none"
               placeholder="Masukkan nama lokasi">
        <p class="text-xs text-gray-400 mt-1">Nama lokasi yang akan ditampilkan di mobile app</p>
      </div>

      <!-- Coordinates -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-white mb-2">Latitude</label>
          <input type="number" name="latitude" step="0.000001" value="<?php echo $settings['latitude'] ?? '-6.200000'; ?>" 
                 class="w-full bg-black/20 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-gscRed focus:outline-none"
                 placeholder="Contoh: -6.200000">
          <p class="text-xs text-gray-400 mt-1">Koordinat latitude lokasi</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-white mb-2">Longitude</label>
          <input type="number" name="longitude" step="0.000001" value="<?php echo $settings['longitude'] ?? '106.816666'; ?>" 
                 class="w-full bg-black/20 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-gscRed focus:outline-none"
                 placeholder="Contoh: 106.816666">
          <p class="text-xs text-gray-400 mt-1">Koordinat longitude lokasi</p>
        </div>
      </div>

      <!-- Radius -->
      <div>
        <label class="block text-sm font-medium text-white mb-2">Radius (meter)</label>
        <input type="number" name="radius_meters" value="<?php echo $settings['radius_meters'] ?? '100'; ?>" 
               class="w-full bg-black/20 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-gscRed focus:outline-none"
               placeholder="Radius dalam meter">
        <p class="text-xs text-gray-400 mt-1">Jarak maksimal karyawan bisa melakukan absensi (dalam meter)</p>
      </div>

      <!-- Grace period for late attendance -->
      <div>
        <label class="block text-sm font-medium text-white mb-2">Toleransi Keterlambatan (menit)</label>
        <input type="number" name="grace_period_minutes" min="1" max="120" value="<?php echo isset($settings['grace_period_minutes']) ? (int)$settings['grace_period_minutes'] : 15; ?>" 
               class="w-full bg-black/20 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-gscRed focus:outline-none"
               placeholder="15">
        <p class="text-xs text-gray-400 mt-1">Status <strong class="text-green-400">hadir</strong> jika absen sebelum/sampai jam masuk shift + toleransi ini. Contoh: shift 07:00 + 15 menit → batas 07:15 masih hadir, 07:16 terlambat.</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-white mb-2">Jam Cutoff Alpha Otomatis</label>
        <input type="time" name="alpha_cutoff_time" value="<?php
          $cutoff = isset($settings['alpha_cutoff_time']) ? $settings['alpha_cutoff_time'] : '23:00:00';
          echo substr($cutoff, 0, 5);
        ?>" class="w-full bg-black/20 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-gscRed focus:outline-none">
        <p class="text-xs text-gray-400 mt-1">Jika karyawan belum absen sampai jam ini (hari yang sama), sistem bisa menandai <strong class="text-red-400">ALPHA</strong>. Alpha juga diproses setelah shift selesai + toleransi.</p>
      </div>

      <!-- Current Location Button -->
      <div class="bg-black/40 p-4 rounded-lg border border-gray-700">
        <p class="text-sm text-gray-400 mb-2">💡 Tips: Untuk mendapatkan koordinat lokasi saat ini:</p>
        <ol class="text-xs text-gray-500 space-y-1 list-decimal list-inside">
          <li>Buka Google Maps di lokasi yang diinginkan</li>
          <li>Klik kanan pada peta dan pilih koordinat</li>
          <li>Atau gunakan GPS smartphone untuk dapatkan koordinat akurat</li>
        </ol>
      </div>

      <!-- Current Stats -->
      <?php if ($settings): ?>
      <div class="bg-gscRed/10 border border-gscRed/30 rounded-lg p-4 mb-4">
        <h3 class="text-sm font-semibold text-white mb-2">📊 Statistik Pengaturan:</h3>
        <div class="space-y-1 text-xs text-gray-300">
          <p><strong>Lokasi:</strong> <?php echo $settings['location_name']; ?></p>
          <p><strong>Koordinat:</strong> <?php echo $settings['latitude']; ?>, <?php echo $settings['longitude']; ?></p>
          <p><strong>Radius:</strong> <?php echo $settings['radius_meters']; ?> meter</p>
          <p><strong>Toleransi telat:</strong> <?php echo isset($settings['grace_period_minutes']) ? (int)$settings['grace_period_minutes'] : 15; ?> menit (berdasarkan jadwal shift karyawan)</p>
          <p><strong>Cutoff alpha:</strong> <?php echo isset($settings['alpha_cutoff_time']) ? substr($settings['alpha_cutoff_time'], 0, 5) : '23:00'; ?> (alpha otomatis untuk yang tidak absen)</p>
          <p><strong>Terakhir Update:</strong> <?php echo date('d M Y H:i', strtotime($settings['updated_at'])); ?></p>
        </div>
      </div>
      <?php endif; ?>

      <!-- Submit Button -->
      <div class="flex justify-end">
        <button type="submit" class="bg-gscRed hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition">
          Simpan Pengaturan
        </button>
      </div>
    </form>
  </div>

  <!-- Mobile App Integration Info -->
  <div class="bg-black/60 border border-gray-800 rounded-xl p-6 shadow-lg shadow-black/40">
    <h3 class="text-lg font-semibold text-white mb-4">📱 Integrasi Mobile App</h3>
    <div class="space-y-4">
      <div class="bg-black/40 p-4 rounded-lg border border-gray-700">
        <h4 class="text-sm font-medium text-white mb-2">API Endpoints:</h4>
        <div class="space-y-1 text-xs text-gray-400 font-mono">
          <p>GET /api/attendance/settings</p>
          <p>POST /api/attendance/check_radius</p>
          <p>POST /api/attendance/save</p>
          <p>POST /api/attendance/checkout</p>
          <p>GET /api/attendance/history</p>
        </div>
      </div>
      
      <div class="bg-black/40 p-4 rounded-lg border border-gray-700">
        <h4 class="text-sm font-medium text-white mb-2">Mobile App Features:</h4>
        <ul class="text-xs text-gray-400 space-y-1">
          <li>✅ GPS location tracking</li>
          <li>✅ Radius validation</li>
          <li>✅ Real-time attendance</li>
          <li>✅ Check-in/Check-out</li>
          <li>✅ Attendance history</li>
        </ul>
      </div>
    </div>
  </div>
</div>
