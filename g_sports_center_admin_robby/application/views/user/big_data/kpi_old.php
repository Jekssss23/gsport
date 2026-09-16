<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-black/60 border border-gscRed/30 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Total Reservation (Today)</div>
    <div class="text-2xl font-semibold text-gscRed">128</div>
    <div class="text-[11px] text-emerald-400 mt-1">+12% vs kemarin</div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Utilization Rate</div>
    <div class="text-2xl font-semibold text-emerald-400">82%</div>
    <div class="text-[11px] text-gray-400 mt-1">Target: 75%</div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Revenue (Today)</div>
    <div class="text-2xl font-semibold text-white">Rp 28.450.000</div>
    <div class="text-[11px] text-emerald-400 mt-1">+7% vs rata-rata</div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">NPS (Rating Pengunjung)</div>
    <div class="text-2xl font-semibold text-yellow-300">4.6 / 5</div>
    <div class="text-[11px] text-gray-400 mt-1">Last 30 days</div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-white">Trend Reservasi 7 Hari Terakhir</h3>
      <span class="text-[11px] text-gray-400">Dummy chart (bisa dihubungkan ke data nyata nanti)</span>
    </div>
    <canvas id="chartKpiReservation" class="w-full h-48"></canvas>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-white">Kontribusi Fasilitas ke Pendapatan</h3>
      <span class="text-[11px] text-gray-400">Futsal / Badminton / Pickle / Gym / Swimming</span>
    </div>
    <canvas id="chartKpiRevenue" class="w-full h-48"></canvas>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const ctx1 = document.getElementById('chartKpiReservation')?.getContext('2d');
    if (ctx1) {
      new Chart(ctx1, {
        type: 'line',
        data: {
          labels: ['Sen','Sel','Rab','Kam','Jum','Sab','Min'],
          datasets: [{
            label: 'Reservasi',
            data: [85, 92, 88, 101, 110, 140, 128],
            borderColor: '#e11d48',
            backgroundColor: 'rgba(225,29,72,0.15)',
            tension: 0.3,
            fill: true
          }]
        },
        options: {
          plugins: { legend: { display: false } },
          scales: {
            x: { ticks: { color: '#9ca3af', font: { size: 10 } } },
            y: { ticks: { color: '#9ca3af', font: { size: 10 } } }
          }
        }
      });
    }

    const ctx2 = document.getElementById('chartKpiRevenue')?.getContext('2d');
    if (ctx2) {
      new Chart(ctx2, {
        type: 'doughnut',
        data: {
          labels: ['Futsal','Badminton','Pickle','Gym','Swimming'],
          datasets: [{
            data: [32, 28, 12, 18, 10],
            backgroundColor: ['#e11d48','#f97316','#22c55e','#0ea5e9','#a855f7']
          }]
        },
        options: {
          plugins: {
            legend: {
              labels: { color: '#e5e7eb', font: { size: 10 } }
            }
          }
        }
      });
    }
  });
</script>

