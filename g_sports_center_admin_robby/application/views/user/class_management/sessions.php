<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?= htmlspecialchars($judul) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('class_management') ?>">Class Management</a></li>
                        <li class="breadcrumb-item active">Pertemuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar pertemuan (untuk QR absensi per sesi)</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('class_management/add_session') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah pertemuan
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kelas</th>
                                <th>Pertemuan ke-</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Ruang</th>
                                <th>Status</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sessions)): ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada jadwal pertemuan. Tambah dari sini atau saat buat kelas baru.</td></tr>
                            <?php else: ?>
                                <?php foreach ($sessions as $s): ?>
                                    <tr>
                                        <td><?= (int) $s['id'] ?></td>
                                        <td><?= htmlspecialchars($s['class_name'] ?? '') ?></td>
                                        <td><?= isset($s['session_number']) ? (int) $s['session_number'] : '—' ?></td>
                                        <td><?= htmlspecialchars($s['session_date'] ?? '') ?></td>
                                        <td><?= htmlspecialchars(substr($s['start_time'] ?? '', 0, 5)) ?> – <?= htmlspecialchars(substr($s['end_time'] ?? '', 0, 5)) ?></td>
                                        <td><?= htmlspecialchars($s['room'] ?? '—') ?></td>
                                        <td><span class="badge badge-info"><?= htmlspecialchars($s['status'] ?? '') ?></span></td>
                                        <td>
                                            <a href="<?= base_url('class_management/session_qr/' . (int) $s['id']) ?>" class="btn btn-xs btn-success" title="QR absensi">
                                                <i class="fas fa-qrcode"></i> QR
                                            </a>
                                            <a href="<?= base_url('class_management/edit_session/' . (int) $s['id']) ?>" class="btn btn-xs btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('class_management/delete_session/' . (int) $s['id']) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Hapus pertemuan ini?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
