<?php if ($view_type == 'selection'): ?>
<!-- Employee Selection -->
<div class="bg-black/60 border border-gray-800 rounded-xl p-6 shadow-lg shadow-black/40">
  <div class="mb-6">
    <h2 class="text-xl font-bold text-white mb-2">Pilih Karyawan untuk Penilaian KPI</h2>
    <p class="text-gray-400">Pilih karyawan yang akan dinilai KPI-nya</p>
  </div>
  
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($employees as $employee): ?>
      <a href="<?php echo base_url('user/big_data/kpi_assessment?employee_id=' . $employee['id']); ?>" 
         class="bg-black/40 border border-gray-700 rounded-lg p-4 hover:border-gscRed hover:bg-gscRed/10 transition block">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold">
            <?php echo strtoupper(substr($employee['nama'], 0, 1)); ?>
          </div>
          <div>
            <div class="font-semibold text-white"><?php echo $employee['nama']; ?></div>
            <div class="text-sm text-gray-400"><?php echo $employee['jabatan']; ?></div>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php else: ?>
<!-- Assessment Form -->
<div class="space-y-6">
  <!-- Employee Info -->
  <div class="bg-black/60 border border-gray-800 rounded-xl p-6 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-bold text-white">Penilaian KPI</h2>
      <a href="<?php echo base_url('user/big_data/kpi_assessment'); ?>" 
         class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        ← Kembali
      </a>
    </div>
    
    <div class="flex items-center gap-4 mb-4">
      <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold text-xl">
        <?php echo strtoupper(substr($employee['nama'], 0, 1)); ?>
      </div>
      <div>
        <div class="text-lg font-semibold text-white"><?php echo $employee['nama']; ?></div>
        <div class="text-sm text-gray-400"><?php echo $employee['jabatan']; ?></div>
        <div class="text-sm text-gscRed"><?php echo $division; ?></div>
      </div>
    </div>
  </div>

  <!-- Period Selection -->
  <div class="bg-black/60 border border-gray-800 rounded-xl p-6 shadow-lg shadow-black/40">
    <h3 class="text-lg font-semibold text-white mb-4">Periode Penilaian</h3>
    <form method="GET" class="flex items-center gap-4">
      <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
      <div>
        <label class="block text-sm text-gray-400 mb-1">Bulan</label>
        <input type="month" name="period" value="<?php echo $period; ?>" 
               class="bg-black/20 border border-gray-700 rounded-lg px-3 py-2 text-white focus:border-gscRed focus:outline-none">
      </div>
      <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition mt-6">
        Ganti Periode
      </button>
    </form>
  </div>

  <!-- Assessment Form -->
  <form method="POST" action="<?php echo base_url('user/big_data/kpi_save'); ?>" 
        class="bg-black/60 border border-gray-800 rounded-xl p-6 shadow-lg shadow-black/40">
    <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
    <input type="hidden" name="period" value="<?php echo $period; ?>">
    
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-white">Form Penilaian</h3>
      <div class="text-right">
        <div class="text-sm text-gray-400">Total Skor</div>
        <div id="totalScore" class="text-2xl font-bold text-gscRed">0</div>
      </div>
    </div>

    <?php if (!empty($templates)): ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($templates as $template): ?>
          <div class="bg-black/20 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center justify-between mb-2">
              <label class="text-white font-medium"><?php echo $template['field_label']; ?></label>
              <span class="text-xs text-gray-400"><?php echo $template['min_value'] . ' - ' . $template['max_value']; ?></span>
            </div>
            <input type="number" 
                   name="<?php echo $template['field_key']; ?>" 
                   min="<?php echo $template['min_value']; ?>" 
                   max="<?php echo $template['max_value']; ?>"
                   step="0.1"
                   value="<?php echo isset($existing_assessment['scores'][$template['field_key']]) ? $existing_assessment['scores'][$template['field_key']] : ''; ?>"
                   class="w-full bg-black/40 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-gscRed focus:outline-none"
                   onchange="calculateTotal()"
                   required>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Notes -->
      <div class="mt-6">
        <label class="block text-white font-medium mb-2">Catatan Penilaian (Opsional)</label>
        <textarea name="notes" rows="3" 
                  placeholder="Tambahkan catatan atau komentar tentang penilaian ini..."
                  class="w-full bg-black/40 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-gscRed focus:outline-none"><?php echo isset($existing_assessment['assessment_notes']) ? $existing_assessment['assessment_notes'] : ''; ?></textarea>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end mt-6">
        <button type="submit" class="bg-gscRed hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition">
          Simpan Penilaian
        </button>
      </div>
    <?php else: ?>
      <div class="text-center text-gray-400 py-8">
        Template KPI untuk divisi <?php echo $division; ?> belum tersedia.
      </div>
    <?php endif; ?>
  </form>
</div>

<script>
  function calculateTotal() {
    let total = 0;
    const inputs = document.querySelectorAll('input[type="number"]');
    
    inputs.forEach(input => {
      const value = parseFloat(input.value) || 0;
      total += value;
    });
    
    document.getElementById('totalScore').textContent = total.toFixed(1);
  }
  
  // Calculate on load
  document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
  });
</script>
<?php endif; ?>
