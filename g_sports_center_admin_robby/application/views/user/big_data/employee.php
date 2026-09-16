<?php if ($this->session->flashdata('pesan')): ?>
  <div class="mb-4">
    <?php echo $this->session->flashdata('pesan'); ?>
  </div>
<?php endif; ?>

<?php if ($this->session->flashdata('pesan_fb')): ?>
  <div class="mb-4">
    <?php echo $this->session->flashdata('pesan_fb'); ?>
  </div>
<?php endif; ?>

<div class="bg-black/60 border border-gray-800 rounded-xl overflow-hidden shadow-lg shadow-black/40">
  <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between bg-gradient-to-r from-transparent to-gscRed/5">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Daftar Karyawan G-Sports Center</h3>
    <button onclick="openModal()" class="px-3 py-1.5 bg-gscRed hover:bg-gscRedDark text-white text-xs font-bold rounded-lg transition flex items-center space-x-2">
      <span>+ Tambah Karyawan</span>
    </button>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-[11px] text-gray-400 uppercase bg-black/40">
          <th class="px-6 py-3 font-medium border-b border-gray-800">Nama</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Jabatan</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Email / HP</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Jadwal Shift</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="text-sm divide-y divide-gray-800">
        <?php if (!empty($employees)): ?>
          <?php foreach ($employees as $e): ?>
            <tr class="hover:bg-white/5 transition">
              <td class="px-6 py-4 flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-gscRed/20 border border-gscRed/40 flex items-center justify-center text-gscRed font-bold text-xs overflow-hidden">
                  <?php if ($e['foto']): ?>
                    <img src="<?php echo $e['foto']; ?>" class="w-full h-full object-cover">
                  <?php else: ?>
                    <?php echo substr($e['nama'], 0, 1); ?>
                  <?php endif; ?>
                </div>
                <div>
                  <div class="font-medium text-white"><?php echo $e['nama']; ?></div>
                  <div class="text-[10px] text-gray-500 italic"><?php echo $e['app_username'] ?: ($e['has_app_login'] ? 'App Login' : 'No Account'); ?></div>
                </div>
              </td>
              <td class="px-6 py-4 text-gray-300"><?php echo $e['jabatan']; ?></td>
              <td class="px-6 py-4 text-gray-300">
                <div class="text-xs"><?php echo $e['email']; ?></div>
                <div class="text-[10px] text-gray-500"><?php echo $e['nohp']; ?></div>
              </td>
              <td class="px-6 py-4 text-gray-300">
                <?php
                  $jtext = 'Belum diatur';
                  if(!empty($e['jadwal_operasional'])) {
                    $js = json_decode($e['jadwal_operasional'], true);
                    if($js) {
                      if(isset($js['pattern_type']) && $js['pattern_type'] === '3week') {
                        // 3 minggu rolling
                        $m1 = $js['m1'] ?? 'off';
                        $m2 = $js['m2'] ?? 'off'; 
                        $m3 = $js['m3'] ?? 'off';
                        $m1_text = $m1 === 'off' ? 'Libur' : $m1;
                        $m2_text = $m2 === 'off' ? 'Libur' : $m2;
                        $m3_text = $m3 === 'off' ? 'Libur' : $m3;
                        $jtext = "3M: {$m1_text}|{$m2_text}|{$m3_text}";
                      } else {
                        // 2 minggu rolling (default atau old format)
                        if(isset($js['ganjil'])) {
                          // Old format
                          $ganjil = $js['ganjil'];
                          $genap = $js['genap'] ?? '15:00-23:00';
                          $libur = $js['libur'] ?? 0;
                          $jtext = "2M: {$ganjil}|{$genap} | Libur: " . ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'][$libur];
                        } else {
                          // New 2week format
                          $m1 = $js['m1'] ?? '07:00-15:00';
                          $m2 = $js['m2'] ?? '15:00-23:00';
                          $work_days = $js['work_days'] ?? [3,4,5];
                          $day_names = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
                          $selected_days = array_map(function($d) use ($day_names) { return $day_names[$d]; }, $work_days);
                          $jtext = "2M: {$m1}|{$m2} | " . implode(',', $selected_days);
                        }
                      }
                    }
                  }
                ?>
                <div class="text-xs text-emerald-400 font-mono bg-emerald-400/10 px-2 py-1 rounded inline-block"><?php echo $jtext; ?></div>
              </td>
              <td class="px-6 py-4 text-right">
                <button onclick='editEmployee(<?php echo json_encode($e); ?>)' class="text-emerald-400 hover:text-white transition mx-2 text-xs font-bold">Edit</button>
                <a href="<?php echo base_url('user/big_data/employee_delete/'.$e['id']); ?>" onclick="return confirm('Hapus karyawan ini? Data absensi & KPI terkait ikut terhapus.')" class="text-gscRed hover:text-white transition text-xs font-bold">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data karyawan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Karyawan -->
<div id="employeeModal" class="hidden fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4">
  <div class="bg-gscBlack border border-gscRedDark rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-2xl flex flex-col">
    <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between flex-shrink-0">
      <h3 id="modalTitle" class="text-white font-semibold">Tambah Karyawan Baru</h3>
      <button onclick="closeModal()" class="text-gray-400 hover:text-white">✕</button>
    </div>
    <div class="flex-1 overflow-y-auto">
      <div class="p-6 space-y-4">
      <form action="<?php echo base_url('user/big_data/employee_save'); ?>" method="POST" enctype="multipart/form-data">
      
      <input type="hidden" name="id_employee" id="id_employee">
      
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-[10px] text-gray-400 uppercase font-bold">Nama Lengkap</label>
          <input type="text" name="nama" id="nama" required class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:border-gscRed outline-none">
        </div>
        <div class="space-y-1">
          <label class="text-[10px] text-gray-400 uppercase font-bold">Jabatan</label>
          <select name="jabatan" id="jabatan" required class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:border-gscRed outline-none appearance-none">
            <option value="" disabled selected>Pilih Jabatan</option>
            <option value="Sports">Sports</option>
            <option value="GRO">GRO</option>
            <option value="Housekeeping">Housekeeping</option>
            <option value="Security">Security</option>
            <option value="Caffe">Caffe</option>
            <option value="Maintenance">Maintenance</option>
            <option value="Entertainment">Entertainment</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-[10px] text-gray-400 uppercase font-bold">Email</label>
          <input type="email" name="email" id="email" class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:border-gscRed outline-none">
        </div>
        <div class="space-y-1">
          <label class="text-[10px] text-gray-400 uppercase font-bold">Nomor HP</label>
          <input type="text" name="nohp" id="nohp" class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:border-gscRed outline-none">
        </div>
      </div>

      <div class="space-y-1">
        <label class="text-[10px] text-gray-400 uppercase font-bold">Alamat</label>
        <textarea name="alamat" id="alamat" rows="2" class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:border-gscRed outline-none resize-none"></textarea>
      </div>

      <div class="p-4 bg-gray-900 border border-gray-800 rounded-xl space-y-4">
        <label class="text-[10px] text-gray-400 uppercase font-bold border-b border-gray-800 pb-2 block">Set Jadwal Operasional (Rolling Mingguan)</label>
        
        <!-- Pattern Selection -->
        <div class="space-y-2">
          <label class="text-[10px] text-gray-400">Pattern Jadwal</label>
          <div class="grid grid-cols-2 gap-2">
            <label class="flex items-center space-x-2 cursor-pointer">
              <input type="radio" name="pattern_type" value="2week" checked class="accent-gscRed">
              <span class="text-xs text-white">2 Minggu Rolling (M1-M4)</span>
            </label>
            <label class="flex items-center space-x-2 cursor-pointer">
              <input type="radio" name="pattern_type" value="3week" class="accent-gscRed">
              <span class="text-xs text-white">3 Minggu Rolling</span>
            </label>
          </div>
        </div>

        <!-- Shift Configuration -->
        <div class="grid grid-cols-3 gap-3">
          <div class="space-y-1">
            <label class="text-[10px] text-gray-400">Shift M1 (Minggu 1)</label>
            <select id="shift_m1" class="w-full bg-black border border-gray-800 rounded-lg px-2 py-1.5 text-xs text-white focus:border-gscRed outline-none appearance-none">
              <option value="07:00-15:00">Pagi (07:00–15:00)</option>
              <option value="13:00-18:00">Middle (13:00–18:00)</option>
              <option value="15:00-23:00">Malam (15:00–23:00)</option>
              <option value="off">Libur</option>
            </select>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] text-gray-400">Shift M2 (Minggu 2)</label>
            <select id="shift_m2" class="w-full bg-black border border-gray-800 rounded-lg px-2 py-1.5 text-xs text-white focus:border-gscRed outline-none appearance-none">
              <option value="15:00-23:00">Malam (15:00–23:00)</option>
              <option value="07:00-15:00">Pagi (07:00–15:00)</option>
              <option value="13:00-18:00">Middle (13:00–18:00)</option>
              <option value="off">Libur</option>
            </select>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] text-gray-400">Shift M3 (Minggu 3)</label>
            <select id="shift_m3" class="w-full bg-black border border-gray-800 rounded-lg px-2 py-1.5 text-xs text-white focus:border-gscRed outline-none appearance-none">
              <option value="07:00-15:00">Pagi (07:00–15:00)</option>
              <option value="13:00-18:00">Middle (13:00–18:00)</option>
              <option value="15:00-23:00">Malam (15:00–23:00)</option>
              <option value="off">Libur</option>
            </select>
          </div>
        </div>

        <!-- Multiple Day Selection -->
        <div class="space-y-2">
          <label class="text-[10px] text-gray-400">Pilih Hari Kerja (Multiple Select)</label>
          <div class="grid grid-cols-4 gap-2">
            <label class="flex items-center space-x-1 cursor-pointer">
              <input type="checkbox" name="work_days" value="1" class="accent-gscRed">
              <span class="text-xs text-white">Sen</span>
            </label>
            <label class="flex items-center space-x-1 cursor-pointer">
              <input type="checkbox" name="work_days" value="2" class="accent-gscRed">
              <span class="text-xs text-white">Sel</span>
            </label>
            <label class="flex items-center space-x-1 cursor-pointer">
              <input type="checkbox" name="work_days" value="3" checked class="accent-gscRed">
              <span class="text-xs text-white">Rab</span>
            </label>
            <label class="flex items-center space-x-1 cursor-pointer">
              <input type="checkbox" name="work_days" value="4" checked class="accent-gscRed">
              <span class="text-xs text-white">Kam</span>
            </label>
            <label class="flex items-center space-x-1 cursor-pointer">
              <input type="checkbox" name="work_days" value="5" checked class="accent-gscRed">
              <span class="text-xs text-white">Jum</span>
            </label>
            <label class="flex items-center space-x-1 cursor-pointer">
              <input type="checkbox" name="work_days" value="6" class="accent-gscRed">
              <span class="text-xs text-white">Sab</span>
            </label>
            <label class="flex items-center space-x-1 cursor-pointer">
              <input type="checkbox" name="work_days" value="0" class="accent-gscRed">
              <span class="text-xs text-white">Min</span>
            </label>
            <button type="button" onclick="selectAllDays()" class="text-xs text-gscRed hover:text-white">Select All</button>
          </div>
        </div>

        <!-- Location -->
        <div class="space-y-1">
          <label class="text-[10px] text-gray-400">Lokasi Penjagaan</label>
          <select id="lokasi_tugas" class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:border-gscRed outline-none appearance-none">
            <option value="futsal">Futsal</option>
            <option value="pickleball_badminton">Badminton / Pickleball</option>
            <option value="swimming">Swimming</option>
            <option value="all">Semua (Bebas)</option>
          </select>
        </div>

        <!-- Preview Schedule -->
        <div class="p-3 bg-black/50 border border-gray-700 rounded-lg">
          <div class="text-[10px] text-gray-400 mb-2">Preview Jadwal:</div>
          <div id="schedulePreview" class="text-xs text-emerald-400 font-mono"></div>
        </div>

        <input type="hidden" name="jadwal_operasional" id="jadwal_operasional">
      </div>

      <div class="space-y-1">
        <label class="text-[10px] text-gray-400 uppercase font-bold">Foto (Cloudinary)</label>
        <input type="file" name="foto" class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-xs text-gray-400 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-gscRed file:text-white hover:file:bg-gscRedDark">
      </div>

      <div class="p-4 bg-gscRed/5 border border-gscRed/20 rounded-xl space-y-3">
        <div class="flex items-center justify-between">
          <label class="text-xs font-bold text-white">Buat Akun Login App?</label>
          <input type="checkbox" name="is_app_admin" id="is_app_admin" value="1" class="accent-gscRed">
        </div>
        <div id="accountFields" class="grid grid-cols-2 gap-4 hidden">
          <div class="space-y-1">
            <label class="text-[10px] text-gray-400 uppercase">Username</label>
            <input type="text" name="username" id="username" class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-sm text-white outline-none">
          </div>
          <div class="space-y-1">
            <label class="text-[10px] text-gray-400 uppercase">Password</label>
            <input type="password" name="password" id="password" class="w-full bg-black border border-gray-800 rounded-lg px-3 py-2 text-sm text-white outline-none">
          </div>
        </div>
      </div>

      <div class="pt-4">
        <button type="submit" class="w-full bg-gscRed hover:bg-gscRedDark text-white font-bold py-2.5 rounded-lg transition shadow-lg shadow-gscRed/20">
          Simpan Data Karyawan
        </button>
      </div>
    </form>
    </div>
  </div>
</div>

<script>
  // Helper functions
  function selectAllDays() {
    const checkboxes = document.querySelectorAll('input[name="work_days"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    updateSchedulePreview();
  }

  const SHIFT_LABELS = {
    '07:00-15:00': 'Pagi (07:00–15:00)',
    '13:00-18:00': 'Middle (13:00–18:00)',
    '15:00-23:00': 'Malam (15:00–23:00)',
    'off': 'Libur'
  };

  function formatShiftLabel(code) {
    return SHIFT_LABELS[code] || code;
  }

  function updateSchedulePreview() {
    const workDays = Array.from(document.querySelectorAll('input[name="work_days"]:checked')).map(cb => parseInt(cb.value));
    const shiftM1 = document.getElementById('shift_m1').value;
    const shiftM2 = document.getElementById('shift_m2').value;
    const shiftM3 = document.getElementById('shift_m3').value;
    const patternType = document.querySelector('input[name="pattern_type"]:checked').value;
    
    const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const selectedDays = workDays.map(day => dayNames[day]).join(', ');
    
    let preview = '';
    if (patternType === '2week') {
      preview = `Minggu 1: ${formatShiftLabel(shiftM1)} | Minggu 2: ${formatShiftLabel(shiftM2)}`;
    } else {
      preview = `M1: ${formatShiftLabel(shiftM1)} | M2: ${formatShiftLabel(shiftM2)} | M3: ${formatShiftLabel(shiftM3)}`;
    }
    preview += ` | Hari: ${selectedDays || 'Tidak ada'}`;
    
    document.getElementById('schedulePreview').textContent = preview;
  }

  function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Karyawan Baru';
    document.getElementById('id_employee').value = '';
    document.getElementById('nama').value = '';
    document.getElementById('jabatan').value = '';
    document.getElementById('email').value = '';
    document.getElementById('nohp').value = '';
    document.getElementById('alamat').value = '';
    document.getElementById('jadwal_operasional').value = '';
    
    // Reset new schedule fields
    document.getElementById('shift_m1').value = '07:00-15:00';
    document.getElementById('shift_m2').value = '15:00-23:00';
    document.getElementById('shift_m3').value = '07:00-15:00';
    document.getElementById('lokasi_tugas').value = 'futsal';
    document.querySelector('input[name="pattern_type"][value="2week"]').checked = true;
    
    // Reset work days (default: Rab, Kam, Jum)
    document.querySelectorAll('input[name="work_days"]').forEach(cb => cb.checked = false);
    document.querySelector('input[name="work_days"][value="3"]').checked = true;
    document.querySelector('input[name="work_days"][value="4"]').checked = true;
    document.querySelector('input[name="work_days"][value="5"]').checked = true;
    
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
    document.getElementById('is_app_admin').checked = false;
    document.getElementById('accountFields').classList.add('hidden');
    document.getElementById('employeeModal').classList.remove('hidden');
    
    updateSchedulePreview();
  }

  function closeModal() {
    document.getElementById('employeeModal').classList.add('hidden');
  }

  function editEmployee(data) {
    document.getElementById('modalTitle').innerText = 'Edit Karyawan';
    document.getElementById('id_employee').value = data.id;
    document.getElementById('nama').value = data.nama;
    document.getElementById('jabatan').value = data.jabatan;
    document.getElementById('email').value = data.email;
    document.getElementById('nohp').value = data.nohp;
    document.getElementById('alamat').value = data.alamat;
    document.getElementById('jadwal_operasional').value = data.jadwal_operasional || '';
    
    // Load existing schedule data
    let scheduleData = {};
    if (data.jadwal_operasional) {
      try {
        scheduleData = JSON.parse(data.jadwal_operasional);
      } catch(e) {
        // Handle old format for backward compatibility
        scheduleData = {
          pattern_type: '2week',
          m1: data.jadwal_operasional.includes('ganjil') && scheduleData.ganjil || '07:00-15:00',
          m2: data.jadwal_operasional.includes('genap') && scheduleData.genap || '15:00-23:00',
          m3: '07:00-15:00',
          work_days: [3, 4, 5], // Default Rab, Kam, Jum
          lokasi: scheduleData.lokasi || 'futsal'
        };
      }
    }
    
    // Set schedule values
    document.getElementById('shift_m1').value = scheduleData.m1 || '07:00-15:00';
    document.getElementById('shift_m2').value = scheduleData.m2 || '15:00-23:00';
    document.getElementById('shift_m3').value = scheduleData.m3 || '07:00-15:00';
    document.getElementById('lokasi_tugas').value = scheduleData.lokasi || 'futsal';
    
    const patternType = scheduleData.pattern_type || '2week';
    document.querySelector(`input[name="pattern_type"][value="${patternType}"]`).checked = true;
    
    // Set work days
    document.querySelectorAll('input[name="work_days"]').forEach(cb => cb.checked = false);
    const workDays = scheduleData.work_days || [3, 4, 5];
    workDays.forEach(day => {
      const checkbox = document.querySelector(`input[name="work_days"][value="${day}"]`);
      if (checkbox) checkbox.checked = true;
    });

    document.getElementById('username').value = data.app_username || data.username || '';
    document.getElementById('password').value = '';
    
    if (parseInt(data.has_app_login, 10) === 1 || data.app_username || data.username || data.firebase_uid) {
      document.getElementById('is_app_admin').checked = true;
      document.getElementById('accountFields').classList.remove('hidden');
    } else {
      document.getElementById('is_app_admin').checked = false;
      document.getElementById('accountFields').classList.add('hidden');
    }
    
    document.getElementById('employeeModal').classList.remove('hidden');
    updateSchedulePreview();
  }

  // Event listeners
  document.addEventListener('DOMContentLoaded', function() {
    // Account fields toggle
    document.getElementById('is_app_admin').addEventListener('change', function() {
      const fields = document.getElementById('accountFields');
      if (this.checked) {
        fields.classList.remove('hidden');
      } else {
        fields.classList.add('hidden');
      }
    });

    // Schedule preview updates
    const scheduleInputs = ['shift_m1', 'shift_m2', 'shift_m3', 'lokasi_tugas'];
    scheduleInputs.forEach(id => {
      const element = document.getElementById(id);
      if (element) {
        element.addEventListener('change', updateSchedulePreview);
      }
    });

    // Pattern type change
    document.querySelectorAll('input[name="pattern_type"]').forEach(radio => {
      radio.addEventListener('change', updateSchedulePreview);
    });

    // Work days change
    document.querySelectorAll('input[name="work_days"]').forEach(checkbox => {
      checkbox.addEventListener('change', updateSchedulePreview);
    });

    // Form submission
    const form = document.querySelector('form');
    if (form) {
      form.addEventListener('submit', function() {
        const workDays = Array.from(document.querySelectorAll('input[name="work_days"]:checked')).map(cb => parseInt(cb.value));
        const patternType = document.querySelector('input[name="pattern_type"]:checked').value;
        
        const config = {
          pattern_type: patternType,
          m1: document.getElementById('shift_m1').value,
          m2: document.getElementById('shift_m2').value,
          m3: document.getElementById('shift_m3').value,
          work_days: workDays,
          lokasi: document.getElementById('lokasi_tugas').value,
          // Keep old format for backward compatibility
          ganjil: document.getElementById('shift_m1').value,
          genap: document.getElementById('shift_m2').value,
          libur: workDays.length > 0 ? 7 - Math.max(...workDays) : 0 // Calculate rest day
        };
        
        document.getElementById('jadwal_operasional').value = JSON.stringify(config);
      });
    }
  });

  // Close modal on outside click
  window.onclick = function(event) {
    const modal = document.getElementById('employeeModal');
    if (event.target == modal) closeModal();
  }
</script>