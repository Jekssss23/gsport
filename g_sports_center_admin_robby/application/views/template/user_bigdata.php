<?php
if ($this->session->userdata('login') != true) {
    $this->session->set_flashdata('pesan','<div class="alert alert-danger"> Harap melakukan login dulu</div>');
    redirect('auth/login');
}

$id_user     = id_user();
$data_user   = data_user($id_user);
$nama_user   = $data_user['nama'];
$jabatan_user= $data_user['jabatan'];
$id_hak_akses= $this->session->userdata('id_hak_akses');
$foto        = $data_user['foto'];
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>G-Sports Center - Big Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              gscRed: '#e11d48',
              gscRedDark: '#9f1239',
              gscBlack: '#050816'
            }
          }
        }
      }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="h-full bg-gscBlack text-gray-100">
<div class="min-h-screen flex flex-col">
  <!-- Top bar -->
  <header class="border-b border-gscRedDark bg-gradient-to-r from-black via-gscBlack to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <img src="<?php echo base_url('assets/gambar/logo.png'); ?>" class="h-10" alt="GSC Logo">
        <div>
          <div class="text-sm uppercase tracking-widest text-gscRed">G-Sports Center</div>
          <div class="text-xs text-gray-400">Big Data & Reservation Console</div>
        </div>
      </div>
      <div class="flex items-center space-x-4">
        <div class="hidden sm:block text-right">
          <div class="text-xs text-gray-400">
            <?php
              $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu','Minggu'];
              echo $hari[date('w')].', '.intval(date('d')).' '.bulan_global(date('n')).' '.date('Y');
            ?>
          </div>
          <div class="font-mono text-sm">
            <span id="jam"><?php echo date('H'); ?></span>:
            <span id="menit"><?php echo date('i'); ?></span>:
            <span id="detik"><?php echo date('s'); ?></span>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <img src="<?php echo base_url('file/user/'.$foto); ?>" class="h-9 w-9 rounded-full border border-gscRed object-cover" alt="User">
          <div class="text-xs">
            <div class="font-semibold"><?php echo $nama_user; ?></div>
            <div class="text-gray-400"><?php echo $jabatan_user; ?></div>
          </div>
        </div>
        <a href="<?php echo base_url('auth/login/logout'); ?>" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-gscRed hover:bg-gscRedDark transition">
          Logout
        </a>
      </div>
    </div>
  </header>

  <div class="flex-1 flex">
    <!-- Sidebar khusus Big Data / Reservation -->
    <aside class="w-60 bg-black/80 border-r border-gscRedDark">
      <nav class="h-full flex flex-col">
        <div class="px-4 py-4 border-b border-gray-800">
          <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Big Data Menu</div>
        </div>
        <div class="flex-1 overflow-y-auto py-3 space-y-1">
          <a href="<?php echo base_url('user/big_data/kpi'); ?>" class="block px-4 py-2 text-sm hover:bg-gscRed/20 hover:text-gscRed">
            KPI
          </a>
          <a href="<?php echo base_url('user/big_data/employee'); ?>" class="block px-4 py-2 text-sm hover:bg-gscRed/20 hover:text-gscRed">
            Employee Management
          </a>
          <a href="<?php echo base_url('user/big_data/attendance'); ?>" class="block px-4 py-2 text-sm hover:bg-gscRed/20 hover:text-gscRed">
            Attendance Management
          </a>
          <a href="<?php echo base_url('user/big_data/reservation'); ?>" class="block px-4 py-2 text-sm hover:bg-gscRed/20 hover:text-gscRed">
            Reservation Management
          </a>
          <a href="<?php echo base_url('user/big_data/monitoring'); ?>" class="block px-4 py-2 text-sm hover:bg-gscRed/20 hover:text-gscRed">
            Monitoring
          </a>
          <a href="<?php echo base_url('user/big_data/rating'); ?>" class="block px-4 py-2 text-sm hover:bg-gscRed/20 hover:text-gscRed">
            View Rating
          </a>
          <a href="<?php echo base_url('user/big_data/event_organizer'); ?>" class="block px-4 py-2 text-sm hover:bg-gscRed/20 hover:text-gscRed">
            Event Organizer
          </a>
        </div>
        <div class="border-t border-gray-800 px-4 py-3 space-y-2">
          <a href="<?php echo base_url('user/big_data/data_reset'); ?>" class="block text-xs text-orange-400/80 hover:text-orange-300">
            ⚠ Reset Data (Testing)
          </a>
          <a href="<?php echo base_url('user/user/dashboard'); ?>" class="block text-xs text-gray-400 hover:text-gscRed">
            ← Kembali ke Dashboard Utama
          </a>
        </div>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 bg-gradient-to-br from-gscBlack via-black to-gscBlack">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <?php if (isset($judul)) : ?>
          <div class="mb-4">
            <h1 class="text-xl font-semibold text-white"><?php echo $judul; ?></h1>
            <?php if (isset($deskripsi) && $deskripsi != '') : ?>
              <p class="text-xs text-gray-400 mt-1"><?php echo $deskripsi; ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('pesan')) : ?>
          <div class="mb-4 rounded-lg overflow-hidden">
            <?php echo $this->session->flashdata('pesan'); ?>
          </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('pesan_fb')) : ?>
          <div class="mb-4 rounded-lg overflow-hidden">
            <?php echo $this->session->flashdata('pesan_fb'); ?>
          </div>
        <?php endif; ?>

        <div>
          <?php echo $konten; ?>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
  window.setTimeout(function waktu() {
    var waktu = new Date();
    document.getElementById("jam").innerHTML = String(waktu.getHours()).padStart(2,'0');
    document.getElementById("menit").innerHTML = String(waktu.getMinutes()).padStart(2,'0');
    document.getElementById("detik").innerHTML = String(waktu.getSeconds()).padStart(2,'0');
    setTimeout(waktu, 1000);
  }, 1000);
</script>
</body>
</html>

