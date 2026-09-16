<?php
$res_fac_labels = $reservation_stats['facilities']['labels'] ?? [];
$res_fac_data = $reservation_stats['facilities']['data'] ?? [];

$res_ch_labels = $reservation_stats['channels']['labels'] ?? [];
$res_ch_data = $reservation_stats['channels']['data'] ?? [];

$att_stats = $attendance_stats ?? ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'alpha' => 0, 'fake gps' => 0];
$att_period = $att_stats['period_label'] ?? date('F Y');

$rating_dist_labels = ['1 Bintang', '2 Bintang', '3 Bintang', '4 Bintang', '5 Bintang'];
$rating_dist_data = [0, 0, 0, 0, 0];
if (!empty($rating_stats['distribution'])) {
    $rating_dist_data = array_values($rating_stats['distribution']);
}

$kpi_labels = [];
$kpi_data = [];
if (!empty($kpi_stats['division_performance'])) {
    foreach ($kpi_stats['division_performance'] as $p) {
        $kpi_labels[] = $p['division'];
        $kpi_data[] = round((float) $p['avg_score'], 1);
    }
}

$finance_labels = $finance_stats['labels'] ?? [];
$finance_data = $finance_stats['data'] ?? [];
$finance_reservation_monthly = $finance_reservation_monthly ?? [];
$finance_breakdown_labels = $finance_breakdown['labels'] ?? [];
$finance_breakdown_data = $finance_breakdown['data'] ?? [];
$package_total = (float) ($finance_breakdown['package_total'] ?? 0);
$reservation_revenue_total = (float) ($finance_breakdown['reservation_total'] ?? 0);
$total_revenue = (float) ($finance_breakdown['total_revenue'] ?? 0);
$package_share = $finance_breakdown['package_share'] ?? 0;
$package_finance_data = $package_finance_stats['data'] ?? [];
$monitoring_year = (int) ($monitoring_year ?? date('Y'));
?>

<div class="mb-4 text-xs text-gray-500">
  Data diperbarui saat halaman dimuat · Tahun keuangan: <span class="text-gray-300"><?= $monitoring_year ?></span>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Overview Rating</h3>
      <span class="text-[10px] text-gray-500">Firestore · reviews</span>
    </div>
    <div class="grid grid-cols-2 gap-3 mb-4">
      <div class="text-center p-3 bg-black/40 rounded-lg">
        <div class="text-2xl font-bold text-yellow-400"><?= isset($rating_stats['avg_rating']) ? $rating_stats['avg_rating'] : '0' ?></div>
        <div class="text-xs text-gray-400 mt-1">Rata-rata (1–5)</div>
      </div>
      <div class="text-center p-3 bg-black/40 rounded-lg">
        <div class="text-2xl font-bold text-white"><?= isset($rating_stats['total_ratings']) ? number_format($rating_stats['total_ratings']) : '0' ?></div>
        <div class="text-xs text-gray-400 mt-1">Total Ulasan</div>
      </div>
      <div class="text-center p-3 bg-black/40 rounded-lg border border-emerald-500/20">
        <div class="text-xl font-bold text-emerald-400"><?= $rating_stats['positive_percentage'] ?? 0 ?>%</div>
        <div class="text-xs text-gray-400 mt-1">Positif (≥4★)</div>
      </div>
      <div class="text-center p-3 bg-black/40 rounded-lg border border-gscRed/20">
        <div class="text-xl font-bold text-gscRed"><?= $rating_stats['critical_percentage'] ?? 0 ?>%</div>
        <div class="text-xs text-gray-400 mt-1">Kritis (≤2★)</div>
      </div>
    </div>
    <div class="h-2 w-full bg-gray-800 rounded-full overflow-hidden flex">
      <?php
        $pos = (float) ($rating_stats['positive_percentage'] ?? 0);
        $cri = (float) ($rating_stats['critical_percentage'] ?? 0);
        $mid = max(0, 100 - $pos - $cri);
      ?>
      <div class="bg-emerald-500 h-full" style="width: <?= $pos ?>%"></div>
      <div class="bg-gray-600 h-full" style="width: <?= $mid ?>%"></div>
      <div class="bg-gscRed h-full" style="width: <?= $cri ?>%"></div>
    </div>
  </div>

  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Distribusi Rating</h3>
      <span class="text-[10px] text-gray-500">Per level bintang</span>
    </div>
    <div class="h-64">
      <canvas id="chartRatingDistribution"></canvas>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Booking Olahraga (7 Hari Terakhir)</h3>
    </div>
    <p class="text-[10px] text-gray-500 mb-4">Hanya reservasi sukses: confirmed, completed, selesai</p>
    <div class="h-64">
      <canvas id="chartMonitoringBooking"></canvas>
    </div>
  </div>

  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Statistik Kehadiran (Bulan Ini)</h3>
    </div>
    <p class="text-[10px] text-gray-500 mb-4"><?= htmlspecialchars($att_period, ENT_QUOTES, 'UTF-8') ?> · tabel <code class="text-gray-400">attendances</code></p>
    <div class="h-64">
      <canvas id="chartMonitoringAttendance"></canvas>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Kinerja KPI Per Divisi (Bulan Ini)</h3>
    </div>
    <p class="text-[10px] text-gray-500 mb-4">Periode <?= date('F Y') ?> · rata-rata skor KPI</p>
    <div class="h-64">
      <canvas id="chartMonitoringKPI"></canvas>
    </div>
  </div>

  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Distribusi Channel Reservasi</h3>
    </div>
    <p class="text-[10px] text-gray-500 mb-4">Tahun <?= $monitoring_year ?> · reservasi sukses · Mobile = ada Firebase UID</p>
    <div class="h-64">
      <canvas id="chartMonitoringChannel"></canvas>
    </div>
  </div>
</div>

<div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40 mb-6">
  <div class="flex items-center justify-between mb-2">
    <h3 class="text-sm font-semibold text-emerald-400 uppercase tracking-wider">Grafik Keuangan Keseluruhan (Tahun Ini)</h3>
  </div>
  <p class="text-[10px] text-gray-500 mb-4">
    Garis hijau = Reservasi sukses (<code class="text-gray-400">total_amount</code>) + Paket GSC (<code class="text-gray-400">financial_transactions</code>)
  </p>
  <div class="h-72">
    <canvas id="chartMonitoringFinance"></canvas>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 uppercase tracking-wider mb-3">Total Pendapatan Tahun Ini</div>
    <div class="text-3xl font-bold text-white">Rp <?= number_format($total_revenue, 0, ',', '.') ?></div>
    <div class="text-[10px] text-gray-500 mt-2">Reservasi + Paket GSC</div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 uppercase tracking-wider mb-3">Pendapatan Reservasi Sukses</div>
    <div class="text-3xl font-bold text-emerald-400">Rp <?= number_format($reservation_revenue_total, 0, ',', '.') ?></div>
  </div>
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 uppercase tracking-wider mb-3">Pendapatan Paket GSC</div>
    <div class="text-2xl font-bold text-yellow-400">Rp <?= number_format($package_total, 0, ',', '.') ?></div>
    <div class="text-sm text-gray-400 mt-2">Bagian <?= $package_share ?>% dari total</div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Breakdown Tipe Transaksi</h3>
    </div>
    <p class="text-[10px] text-gray-500 mb-4">Komposisi pendapatan tahun <?= $monitoring_year ?></p>
    <div class="h-64">
      <canvas id="chartFinanceTypeBreakdown"></canvas>
    </div>
  </div>

  <div class="bg-black/60 border border-gray-800 rounded-xl p-5 shadow-lg shadow-black/40">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Pendapatan Paket GSC per Bulan</h3>
    </div>
    <p class="text-[10px] text-gray-500 mb-4">Sumber: transaksi tipe <code class="text-gray-400">package</code></p>
    <div class="h-64">
      <canvas id="chartPackageRevenue"></canvas>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const fmtRp = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    const chartFont = { size: 10 };
    const gridColor = '#1f2937';
    const tickColor = '#9ca3af';

    const ctxFinance = document.getElementById('chartMonitoringFinance')?.getContext('2d');
    if (ctxFinance) {
      new Chart(ctxFinance, {
        type: 'line',
        data: {
          labels: <?= json_encode($finance_labels) ?>,
          datasets: [
            {
              label: 'Total (Reservasi + Paket)',
              data: <?= json_encode($finance_data) ?>,
              borderColor: '#10b981',
              backgroundColor: 'rgba(16, 185, 129, 0.12)',
              borderWidth: 2,
              fill: true,
              tension: 0.35,
              pointRadius: 3
            },
            {
              label: 'Reservasi Sukses',
              data: <?= json_encode($finance_reservation_monthly) ?>,
              borderColor: '#3b82f6',
              backgroundColor: 'transparent',
              borderWidth: 1.5,
              borderDash: [4, 4],
              tension: 0.35,
              pointRadius: 0
            },
            {
              label: 'Paket GSC',
              data: <?= json_encode($package_finance_data) ?>,
              borderColor: '#f59e0b',
              backgroundColor: 'transparent',
              borderWidth: 1.5,
              borderDash: [2, 2],
              tension: 0.35,
              pointRadius: 0
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          interaction: { mode: 'index', intersect: false },
          plugins: {
            legend: {
              labels: { color: tickColor, font: chartFont }
            },
            tooltip: {
              callbacks: {
                label: (ctx) => `${ctx.dataset.label}: ${fmtRp(ctx.parsed.y)}`
              }
            }
          },
          scales: {
            x: { grid: { color: gridColor }, ticks: { color: tickColor, font: chartFont } },
            y: {
              grid: { color: gridColor },
              ticks: {
                color: tickColor,
                font: chartFont,
                callback: (v) => fmtRp(v)
              }
            }
          }
        }
      });
    }

    const ctxFinanceType = document.getElementById('chartFinanceTypeBreakdown')?.getContext('2d');
    if (ctxFinanceType) {
      new Chart(ctxFinanceType, {
        type: 'doughnut',
        data: {
          labels: <?= json_encode($finance_breakdown_labels) ?>,
          datasets: [{
            data: <?= json_encode($finance_breakdown_data) ?>,
            backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ef4444', '#14b8a6'],
            borderWidth: 0,
            hoverOffset: 6
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right', labels: { color: tickColor, font: chartFont } },
            tooltip: {
              callbacks: {
                label: (ctx) => {
                  const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                  const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                  return `${ctx.label}: ${fmtRp(ctx.parsed)} (${pct}%)`;
                }
              }
            }
          },
          cutout: '58%'
        }
      });
    }

    const ctxPackageRevenue = document.getElementById('chartPackageRevenue')?.getContext('2d');
    if (ctxPackageRevenue) {
      new Chart(ctxPackageRevenue, {
        type: 'bar',
        data: {
          labels: <?= json_encode($finance_labels) ?>,
          datasets: [{
            label: 'Paket GSC',
            data: <?= json_encode($package_finance_data) ?>,
            backgroundColor: 'rgba(245, 158, 11, 0.85)',
            borderRadius: 4
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { color: tickColor, font: chartFont } },
            y: {
              grid: { color: gridColor },
              ticks: { color: tickColor, font: chartFont, callback: (v) => fmtRp(v) }
            }
          }
        }
      });
    }

    const ctxBooking = document.getElementById('chartMonitoringBooking')?.getContext('2d');
    if (ctxBooking) {
      new Chart(ctxBooking, {
        type: 'bar',
        data: {
          labels: <?= json_encode($res_fac_labels) ?>,
          datasets: [{
            label: 'Reservasi Sukses',
            data: <?= json_encode($res_fac_data) ?>,
            backgroundColor: 'rgba(225,29,72,0.85)',
            borderRadius: 4
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { color: tickColor, font: chartFont } },
            y: {
              grid: { color: gridColor },
              ticks: { color: tickColor, font: chartFont, stepSize: 1, precision: 0 }
            }
          }
        }
      });
    }

    const ctxAtt = document.getElementById('chartMonitoringAttendance')?.getContext('2d');
    if (ctxAtt) {
      const stats = <?= json_encode($att_stats) ?>;
      const attData = [
        stats.hadir || 0,
        stats.terlambat || 0,
        stats.izin || 0,
        stats.alpha || 0,
        stats['fake gps'] || 0
      ];
      const attLabels = ['Hadir', 'Terlambat', 'Izin', 'Alpha', 'Fake GPS'];
      const hasAtt = attData.some((v) => v > 0);

      new Chart(ctxAtt, {
        type: 'doughnut',
        data: {
          labels: attLabels,
          datasets: [{
            data: hasAtt ? attData : [1],
            backgroundColor: hasAtt
              ? ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#a855f7']
              : ['#374151'],
            borderWidth: 0,
            hoverOffset: 4
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right', labels: { color: tickColor, font: chartFont } },
            tooltip: {
              filter: () => hasAtt,
              callbacks: {
                label: (ctx) => {
                  const total = attData.reduce((a, b) => a + b, 0);
                  const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                  return `${ctx.label}: ${ctx.parsed} (${pct}%)`;
                }
              }
            }
          },
          cutout: '68%'
        }
      });
    }

    const ctxKPI = document.getElementById('chartMonitoringKPI')?.getContext('2d');
    if (ctxKPI) {
      new Chart(ctxKPI, {
        type: 'bar',
        data: {
          labels: <?= json_encode($kpi_labels) ?>,
          datasets: [{
            label: 'Rata-rata Skor',
            data: <?= json_encode($kpi_data) ?>,
            backgroundColor: 'rgba(59,130,246,0.85)',
            borderRadius: 4
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { color: tickColor, font: chartFont } },
            y: {
              grid: { color: gridColor },
              ticks: { color: tickColor, font: chartFont },
              suggestedMax: 100
            }
          }
        }
      });
    }

    const ctxChannel = document.getElementById('chartMonitoringChannel')?.getContext('2d');
    if (ctxChannel) {
      new Chart(ctxChannel, {
        type: 'doughnut',
        data: {
          labels: <?= json_encode($res_ch_labels) ?>,
          datasets: [{
            data: <?= json_encode($res_ch_data) ?>,
            backgroundColor: ['#8b5cf6', '#ec4899', '#14b8a6', '#f59e0b'],
            borderWidth: 0,
            hoverOffset: 4
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right', labels: { color: tickColor, font: chartFont } },
            tooltip: {
              callbacks: {
                label: (ctx) => {
                  const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                  const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                  return `${ctx.label}: ${ctx.parsed} (${pct}%)`;
                }
              }
            }
          },
          cutout: '68%'
        }
      });
    }

    const ctxRating = document.getElementById('chartRatingDistribution')?.getContext('2d');
    if (ctxRating) {
      const dist = <?= json_encode($rating_dist_data) ?>;
      const hasRating = dist.some((v) => v > 0);

      new Chart(ctxRating, {
        type: 'bar',
        data: {
          labels: <?= json_encode($rating_dist_labels) ?>,
          datasets: [{
            label: 'Jumlah Ulasan',
            data: hasRating ? dist : [0, 0, 0, 0, 0],
            backgroundColor: ['#ef4444', '#f59e0b', '#eab308', '#22c55e', '#10b981'],
            borderRadius: 4
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { color: tickColor, font: chartFont } },
            y: {
              grid: { color: gridColor },
              ticks: { color: tickColor, font: chartFont, stepSize: 1, precision: 0 }
            }
          }
        }
      });
    }
  });
</script>
