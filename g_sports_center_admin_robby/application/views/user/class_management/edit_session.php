<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?= htmlspecialchars($judul) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('class_management/sessions') ?>">Pertemuan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <form action="<?= base_url('class_management/edit_session/' . (int) $session['id']) ?>" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="class_type_id" class="form-control" required>
                                        <?php foreach ($class_types as $ct): ?>
                                            <option value="<?= (int) $ct['id'] ?>" <?= (int)$session['class_type_id'] === (int)$ct['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($ct['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Pertemuan ke-</label>
                                    <input type="number" name="session_number" class="form-control" min="1" value="<?= isset($session['session_number']) ? (int)$session['session_number'] : 1 ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="session_date" class="form-control" value="<?= htmlspecialchars($session['session_date'] ?? '') ?>" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mulai</label>
                                            <input type="time" name="start_time" class="form-control" value="<?= htmlspecialchars(substr($session['start_time'] ?? '', 0, 5)) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Selesai</label>
                                            <input type="time" name="end_time" class="form-control" value="<?= htmlspecialchars(substr($session['end_time'] ?? '', 0, 5)) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Ruang</label>
                                    <input type="text" name="room" class="form-control" value="<?= htmlspecialchars($session['room'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <?php foreach (['scheduled','ongoing','completed','cancelled'] as $st): ?>
                                            <option value="<?= $st ?>" <?= ($session['status'] ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Catatan</label>
                                    <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($session['notes'] ?? '') ?></textarea>
                                </div>
                                <input type="hidden" name="current_participants" value="<?= (int)($session['current_participants'] ?? 0) ?>">
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="<?= base_url('class_management/session_qr/' . (int)$session['id']) ?>" class="btn btn-success"><i class="fas fa-qrcode"></i> QR absensi</a>
                                <a href="<?= base_url('class_management/sessions') ?>" class="btn btn-default">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Yang sudah absen</h3></div>
                        <div class="card-body p-0">
                            <?php if (empty($attendance_rows)): ?>
                                <p class="p-3 text-muted mb-0">Belum ada absensi.</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($attendance_rows as $row): ?>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span><?= htmlspecialchars($row['email']) ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($row['checked_in_at'] ?? '') ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
