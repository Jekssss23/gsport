<div class="space-y-6">
  <?php if ($this->session->flashdata('pesan')): ?>
    <?php echo $this->session->flashdata('pesan'); ?>
  <?php endif; ?>

  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Tambah Event</h3>
    <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="text-xs text-gray-400">Nama Event</label>
        <input type="text" name="name" class="w-full mt-1 bg-black/40 border border-gray-700 rounded px-3 py-2 text-sm text-white" required>
      </div>
      <div>
        <label class="text-xs text-gray-400">Tanggal Mulai</label>
        <input type="date" name="start_date" class="w-full mt-1 bg-black/40 border border-gray-700 rounded px-3 py-2 text-sm text-white" required>
      </div>
      <div>
        <label class="text-xs text-gray-400">Tanggal Selesai</label>
        <input type="date" name="end_date" class="w-full mt-1 bg-black/40 border border-gray-700 rounded px-3 py-2 text-sm text-white" required>
      </div>
      <div>
        <label class="text-xs text-gray-400">Jam Mulai</label>
        <input type="time" name="start_time" class="w-full mt-1 bg-black/40 border border-gray-700 rounded px-3 py-2 text-sm text-white" required>
      </div>
      <div>
        <label class="text-xs text-gray-400">Jam Selesai</label>
        <input type="time" name="end_time" class="w-full mt-1 bg-black/40 border border-gray-700 rounded px-3 py-2 text-sm text-white" required>
      </div>
      <div class="md:col-span-2">
        <label class="text-xs text-gray-400">Gambar Event (Wajib)</label>
        <input type="file" accept="image/*" name="image" class="w-full mt-1 bg-black/40 border border-gray-700 rounded px-3 py-2 text-sm text-white" required>
      </div>
      <div class="md:col-span-2">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold bg-gscRed hover:bg-gscRedDark transition">
          Simpan Event
        </button>
      </div>
    </form>
  </div>

  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Daftar Event</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php if (!empty($events)): ?>
        <?php foreach ($events as $ev): ?>
          <div class="bg-black/40 border border-gray-800 rounded-lg overflow-hidden">
            <img src="<?php echo htmlspecialchars($ev['image_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full h-44 object-cover" alt="Event image">
            <div class="p-3">
              <div class="text-white font-semibold text-sm"><?php echo htmlspecialchars($ev['name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
              <div class="text-xs text-gray-400 mt-1">
                <?php echo htmlspecialchars(($ev['start_date'] ?? '-') . ' ' . ($ev['start_time'] ?? '-') . ' - ' . ($ev['end_date'] ?? '-') . ' ' . ($ev['end_time'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?>
              </div>
              <div class="mt-3 flex flex-wrap gap-2">
                <a href="<?php echo base_url('user/big_data/event_resend_notif/' . urlencode($ev['id'] ?? '')); ?>"
                  onclick="return confirm('Kirim ulang notifikasi ke semua member untuk event ini?');"
                  class="inline-flex items-center px-3 py-1.5 rounded text-xs font-semibold bg-blue-700 hover:bg-blue-800 transition">
                  Kirim Notif
                </a>
                <a href="<?php echo base_url('user/big_data/event_delete/' . urlencode($ev['id'] ?? '')); ?>"
                  onclick="return confirm('Hapus event ini? Data event dan gambar cloudinary akan dihapus.');"
                  class="inline-flex items-center px-3 py-1.5 rounded text-xs font-semibold bg-red-700 hover:bg-red-800 transition">
                  Hapus Event
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-sm text-gray-400">Belum ada event.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

