<div class="max-w-3xl">
  <div class="bg-red-950/50 border border-red-500/40 rounded-xl p-6 mb-6">
    <h2 class="text-lg font-bold text-red-300 mb-2">⚠ Reset Data Operasional Big Data</h2>
    <p class="text-sm text-red-100/90 leading-relaxed mb-3">
      Hanya menghapus <strong class="text-white">data transaksi</strong> dari menu Big Data (absensi record, hasil KPI, reservasi, dll.).
      Bukan reset sistem, bukan reset login web.
    </p>
    <p class="text-xs text-emerald-300/90 font-medium">
      ✓ Lokasi absensi GPS · ✓ Template poin KPI · ✓ master_user (login web) — <strong>TIDAK DIHAPUS</strong>
    </p>
  </div>

  <!-- PROTECTED — tidak akan dihapus -->
  <div class="bg-emerald-950/30 border border-emerald-500/40 rounded-xl p-5 mb-6">
    <h3 class="text-sm font-semibold text-emerald-300 mb-1 uppercase tracking-wider">🛡 Dilindungi — tidak akan dihapus</h3>
    <p class="text-[11px] text-gray-400 mb-3">Tabel konfigurasi & akun sistem di luar scope reset Big Data</p>
    <table class="w-full text-sm">
      <thead>
        <tr class="text-[11px] text-emerald-400/80 uppercase border-b border-emerald-500/20">
          <th class="text-left py-2">Data</th>
          <th class="text-right py-2">Baris saat ini</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-emerald-500/10">
        <?php if (!empty($protected)): ?>
          <?php foreach ($protected as $row): ?>
            <tr>
              <td class="py-2 text-gray-200">
                <?php echo htmlspecialchars($row['label']); ?>
                <span class="text-[10px] text-gray-500 block font-mono"><?php echo htmlspecialchars($row['table']); ?></span>
              </td>
              <td class="py-2 text-right font-mono text-emerald-300"><?php echo number_format($row['count']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        <tr>
          <td class="py-2 text-gray-200">Akun login mobile (Firestore <code class="text-[10px]">users</code>)</td>
          <td class="py-2 text-right text-emerald-300 text-xs">Dilindungi</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- WILL BE DELETED -->
  <div class="bg-black/60 border border-orange-500/30 rounded-xl p-5 mb-6">
    <h3 class="text-sm font-semibold text-orange-300 mb-1 uppercase tracking-wider">🗑 Yang akan dikosongkan</h3>
    <p class="text-[11px] text-gray-400 mb-3">Hanya data operasional menu Big Data</p>
    <table class="w-full text-sm">
      <thead>
        <tr class="text-[11px] text-gray-400 uppercase border-b border-gray-800">
          <th class="text-left py-2">Modul Big Data</th>
          <th class="text-right py-2">Jumlah saat ini</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-800">
        <?php if (!empty($preview)): ?>
          <?php foreach ($preview as $row): ?>
            <tr>
              <td class="py-2 text-gray-300"><?php echo htmlspecialchars($row['label']); ?></td>
              <td class="py-2 text-right font-mono text-white"><?php echo number_format($row['count']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="2" class="py-4 text-gray-500">Tidak ada data operasional terdeteksi</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <form method="POST" action="<?php echo base_url('user/big_data/data_reset'); ?>" class="bg-black/60 border border-orange-500/30 rounded-xl p-6 space-y-4" onsubmit="return confirm('Yakin reset data operasional Big Data di atas?\n\nPengaturan lokasi absensi, template KPI, dan master_user TIDAK akan dihapus.');">
    <input type="hidden" name="confirm_reset" value="1">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm text-white mb-2">Ketik <code class="text-orange-300 bg-black px-1 rounded">RESET BIG DATA</code></label>
        <input type="text" name="confirm_text" required autocomplete="off"
               class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-orange-500 outline-none"
               placeholder="RESET BIG DATA">
      </div>
      <div>
        <label class="block text-sm text-white mb-2">Masukkan PIN Reset</label>
        <input type="password" name="confirm_pin" required autocomplete="off" maxlength="4"
               class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-orange-500 outline-none font-mono text-center tracking-[1em]"
               placeholder="****">
      </div>
    </div>

    <div class="space-y-2 text-sm text-gray-300">
      <p class="text-xs text-gray-500 uppercase tracking-wider">Opsional — hapus juga di Firestore (bukan akun login):</p>
      <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="clear_firestore_reviews" value="1" class="accent-orange-500">
        <span>Reviews / rating pengunjung</span>
      </label>
      <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="clear_firestore_events" value="1" class="accent-orange-500">
        <span>Events (event organizer)</span>
      </label>
      <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="clear_firestore_employees" value="1" class="accent-orange-500">
        <span>Firestore employees/EMP_* (mirror KPI, bukan users login)</span>
      </label>
    </div>

    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-lg transition">
      Reset Data Operasional Big Data
    </button>
  </form>
</div>
