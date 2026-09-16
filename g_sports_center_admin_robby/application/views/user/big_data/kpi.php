<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-black/60 border border-gscRed/30 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Total Karyawan Dinilai</div>
    <div class="text-2xl font-semibold text-gscRed"><?php echo isset($stats['completion_rate']['assessed_count']) ? $stats['completion_rate']['assessed_count'] : 0; ?></div>
    <div class="text-[11px] text-emerald-400 mt-1">
      <?php 
      if (isset($stats['completion_rate'])) {
        $rate = $stats['completion_rate']['total_employees'] > 0 
          ? round(($stats['completion_rate']['assessed_count'] / $stats['completion_rate']['total_employees']) * 100, 1) 
          : 0;
        echo "Completion: {$rate}%";
      }
      ?>
    </div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Rata-rata Skor KPI</div>
    <div class="text-2xl font-semibold text-emerald-400">
      <?php 
      if (isset($stats['division_performance']) && !empty($stats['division_performance'])) {
        $total = array_sum(array_column($stats['division_performance'], 'avg_score'));
        $count = count($stats['division_performance']);
        echo round($total / $count, 1);
      } else {
        echo '0';
      }
      ?>
    </div>
    <div class="text-[11px] text-gray-400 mt-1">Periode: <?php echo $current_period; ?></div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Divisi Terbaik</div>
    <div class="text-2xl font-semibold text-white">
      <?php 
      if (isset($stats['division_performance']) && !empty($stats['division_performance'])) {
        echo $stats['division_performance'][0]['division'];
      } else {
        echo '-';
      }
      ?>
    </div>
    <div class="text-[11px] text-emerald-400 mt-1">
      <?php 
      if (isset($stats['division_performance']) && !empty($stats['division_performance'])) {
        echo 'Skor: ' . round($stats['division_performance'][0]['avg_score'], 1);
      }
      ?>
    </div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Top Performer</div>
    <div class="text-lg font-semibold text-yellow-300">
      <?php 
      if (isset($stats['top_performers']) && !empty($stats['top_performers'])) {
        echo $stats['top_performers'][0]['employee_name'];
      } else {
        echo '-';
      }
      ?>
    </div>
    <div class="text-[11px] text-gray-400 mt-1">
      <?php 
      if (isset($stats['top_performers']) && !empty($stats['top_performers'])) {
        echo 'Skor: ' . $stats['top_performers'][0]['total_score'];
      }
      ?>
    </div>
  </div>
</div>

<!-- Filters and Actions -->
<div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-white">Filter Data</h3>
    <a href="<?php echo base_url('user/big_data/kpi_assessment'); ?>" class="bg-gscRed hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
      + Penilaian Baru
    </a>
  </div>
  <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label class="block text-xs text-gray-400 mb-1">Periode (Bulan)</label>
      <input type="month" name="period" value="<?php echo $current_period; ?>" 
             class="w-full bg-black/20 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-gscRed focus:outline-none">
    </div>
    <div>
      <label class="block text-xs text-gray-400 mb-1">Divisi</label>
      <select name="division" class="w-full bg-black/20 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-gscRed focus:outline-none">
        <?php foreach ($divisions as $key => $value): ?>
          <option value="<?php echo $key; ?>" <?php echo ($current_division == $key) ? 'selected' : ''; ?>>
            <?php echo $value; ?>
          </option>
        <?php endforeach; ?>
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
    <div class="md:col-span-3">
      <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        Filter
      </button>
    </div>
  </form>
</div>

<!-- KPI Assessments Table -->
<div class="bg-black/60 border border-gray-800 rounded-xl overflow-hidden shadow-lg shadow-black/40">
  <div class="px-6 py-4 border-b border-gray-800">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Data Penilaian KPI</h3>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-[11px] text-gray-400 uppercase bg-black/40">
          <th class="px-6 py-3 font-medium border-b border-gray-800">Karyawan</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Divisi</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Periode</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Total Skor</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Dinilai Oleh</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Update</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="text-sm divide-y divide-gray-800">
        <?php if (isset($assessments) && !empty($assessments)): ?>
          <?php foreach ($assessments as $assessment): ?>
            <tr class="hover:bg-white/5 transition">
              <td class="px-6 py-4">
                <div class="font-medium text-white"><?php echo $assessment['employee_name']; ?></div>
                <div class="text-[10px] text-gray-500">ID: <?php echo $assessment['employee_id']; ?></div>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full text-[10px] font-bold">
                  <?php echo $assessment['division']; ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="text-white"><?php echo date('M Y', strtotime($assessment['period'] . '-01')); ?></div>
              </td>
              <td class="px-6 py-4">
                <div class="text-lg font-semibold text-gscRed"><?php echo number_format($assessment['total_score'], 1); ?></div>
              </td>
              <td class="px-6 py-4">
                <div class="text-white"><?php echo $assessment['assessed_by_name'] ?? 'System'; ?></div>
              </td>
              <td class="px-6 py-4">
                <div class="text-[10px] text-gray-500"><?php echo date('d M Y H:i', strtotime($assessment['updated_at'])); ?></div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <a href="<?php echo base_url('user/big_data/kpi_assessment?employee_id=' . $assessment['employee_id'] . '&period=' . $assessment['period']); ?>" 
                     class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs font-medium transition">
                    Edit
                  </a>
                  <a href="<?php echo base_url('user/big_data/kpi_delete/' . $assessment['id']); ?>" 
                     onclick="return confirm('Hapus penilaian KPI ini?')" 
                     class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-medium transition">
                    Hapus
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
              Belum ada data penilaian KPI untuk periode ini
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Division Performance Chart -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-white">Performa per Divisi</h3>
      <span class="text-[11px] text-gray-400">Rata-rata skor KPI</span>
    </div>
    <div class="relative h-48">
      <canvas id="chartDivisionPerformance" class="w-full h-full"></canvas>
    </div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-white">Top Performers</h3>
      <span class="text-[11px] text-gray-400">5 karyawan terbaik</span>
    </div>
    <div class="space-y-2">
      <?php if (isset($stats['top_performers']) && !empty($stats['top_performers'])): ?>
        <?php foreach ($stats['top_performers'] as $index => $performer): ?>
          <div class="flex items-center justify-between p-2 bg-black/20 rounded-lg">
            <div class="flex items-center gap-3">
              <span class="text-gscRed font-bold"><?php echo $index + 1; ?></span>
              <div>
                <div class="text-white text-sm"><?php echo $performer['employee_name']; ?></div>
                <div class="text-[10px] text-gray-500"><?php echo $performer['division']; ?></div>
              </div>
            </div>
            <span class="text-gscRed font-semibold"><?php echo $performer['total_score']; ?></span>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center text-gray-500 py-4">Belum ada data</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Division Performance Chart
    const ctx = document.getElementById('chartDivisionPerformance')?.getContext('2d');
    if (ctx) {
      const divisionData = <?php echo json_encode(isset($stats['division_performance']) ? $stats['division_performance'] : []); ?>;
      
      // Validate data
      const validData = divisionData.filter(d => d && d.division && !isNaN(parseFloat(d.avg_score)));
      
      if (validData.length === 0) {
        // Show no data message
        ctx.font = '14px sans-serif';
        ctx.fillStyle = '#9ca3af';
        ctx.textAlign = 'center';
        ctx.fillText('Belum ada data penilaian', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
      }
      
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: validData.map(d => d.division),
          datasets: [{
            label: 'Rata-rata Skor',
            data: validData.map(d => parseFloat(d.avg_score)),
            backgroundColor: '#e11d48',
            borderRadius: 4,
            barThickness: 30
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { 
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderColor: '#e11d48',
              borderWidth: 1,
              callbacks: {
                label: function(context) {
                  return 'Skor: ' + context.parsed.y.toFixed(1);
                }
              }
            }
          },
          scales: {
            x: { 
              ticks: { 
                color: '#9ca3af', 
                font: { size: 10 },
                maxRotation: 45,
                minRotation: 0
              },
              grid: { display: false }
            },
            y: { 
              beginAtZero: true,
              max: 20, // Set max score to prevent chart from being too tall
              ticks: { 
                color: '#9ca3af', 
                font: { size: 10 },
                stepSize: 2
              },
              grid: { 
                color: '#374151',
                drawBorder: false
              }
            }
          }
        }
      });
    }
  });
</script>
