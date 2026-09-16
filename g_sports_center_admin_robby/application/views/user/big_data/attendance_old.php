<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="bg-black/60 border border-emerald-500/20 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Hadir Tepat Waktu (Hari Ini)</div>
    <div class="text-2xl font-semibold text-emerald-400">38</div>
    <div class="text-[11px] text-gray-500 mt-1">Dari total 42 karyawan masuk</div>
  </div>
  <div class="bg-black/60 border border-yellow-500/20 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Terlambat / Izin</div>
    <div class="text-2xl font-semibold text-yellow-400">4</div>
    <div class="text-[11px] text-gray-500 mt-1">2 Terlambat, 2 Izin/Sakit</div>
  </div>
  <div class="bg-black/60 border border-gscRed/20 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Absen Tanpa Keterangan</div>
    <div class="text-2xl font-semibold text-gscRed">0</div>
    <div class="text-[11px] text-gray-500 mt-1">Alhamdulillah, nihil</div>
  </div>
</div>

<div class="bg-black/60 border border-gray-800 rounded-xl overflow-hidden shadow-lg shadow-black/40">
  <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Log Absensi Terbaru</h3>
    <div class="flex space-x-2">
       <input type="date" class="bg-black border border-gray-700 text-[11px] px-2 py-1 rounded text-white" value="<?php echo date('Y-m-d'); ?>">
       <button class="bg-gray-800 hover:bg-gray-700 px-3 py-1 text-[11px] rounded transition">Filter</button>
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-[11px] text-gray-400 uppercase bg-black/40">
          <th class="px-6 py-3 font-medium border-b border-gray-800">Karyawan</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Check In</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Check Out</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Status</th>
          <th class="px-6 py-3 font-medium border-b border-gray-800">Lokasi / IP</th>
        </tr>
      </thead>
      <tbody class="text-sm divide-y divide-gray-800">
        <tr class="hover:bg-white/5 transition">
          <td class="px-6 py-4">
            <div class="font-medium text-white">Robby Pratama</div>
            <div class="text-[10px] text-gray-500">IT Division</div>
          </td>
          <td class="px-6 py-4 text-emerald-400 font-mono">08:02:15</td>
          <td class="px-6 py-4 text-gray-500 font-mono">--:--:--</td>
          <td class="px-6 py-4">
            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full text-[10px] font-bold tracking-tight">TEPAT WAKTU</span>
          </td>
          <td class="px-6 py-4 text-gray-500 text-xs">192.168.1.15 (Office WiFi)</td>
        </tr>
        <tr class="hover:bg-white/5 transition">
          <td class="px-6 py-4">
            <div class="font-medium text-white">Siska Amelia</div>
            <div class="text-[10px] text-gray-500">Operations</div>
          </td>
          <td class="px-6 py-4 text-emerald-400 font-mono">07:55:01</td>
          <td class="px-6 py-4 text-gray-500 font-mono">--:--:--</td>
          <td class="px-6 py-4">
            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full text-[10px] font-bold tracking-tight">TEPAT WAKTU</span>
          </td>
          <td class="px-6 py-4 text-gray-500 text-xs">192.168.1.12 (Front Desk)</td>
        </tr>
        <tr class="hover:bg-white/5 transition">
          <td class="px-6 py-4">
            <div class="font-medium text-white">Budi Santoso</div>
            <div class="text-[10px] text-gray-500">Maintenance</div>
          </td>
          <td class="px-6 py-4 text-yellow-400 font-mono">08:35:10</td>
          <td class="px-6 py-4 text-gray-500 font-mono">--:--:--</td>
          <td class="px-6 py-4">
            <span class="px-2 py-0.5 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded-full text-[10px] font-bold tracking-tight">TERLAMBAT (35m)</span>
          </td>
          <td class="px-6 py-4 text-gray-500 text-xs">Mobile (Field Check-in)</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>