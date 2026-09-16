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
                        <li class="breadcrumb-item"><a href="<?= base_url('class_management/sessions') ?>">Pertemuan</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
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
                        <form action="<?= base_url('class_management/add_session') ?>" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Kelas <span class="text-danger">*</span></label>
                                    <select name="class_type_id" class="form-control" required>
                                        <option value="">— Pilih —</option>
                                        <?php foreach ($class_types as $ct): ?>
                                            <option value="<?= (int) $ct['id'] ?>" <?= !empty($pre_class_type_id) && (int)$pre_class_type_id === (int)$ct['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($ct['name']) ?> (<?= htmlspecialchars($ct['category_name'] ?? '') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Pertemuan ke-</label>
                                    <input type="number" name="session_number" class="form-control" min="1" value="<?= (int) ($next_session_number ?? 1) ?>" placeholder="Otomatis jika dikosongkan">
                                    <small class="text-muted">Nomor urut pertemuan untuk kelas tersebut.</small>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="session_date" class="form-control" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mulai <span class="text-danger">*</span></label>
                                            <input type="time" name="start_time" class="form-control" value="09:00" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Selesai <span class="text-danger">*</span></label>
                                            <input type="time" name="end_time" class="form-control" value="10:00" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Ruang</label>
                                    <input type="text" name="room" class="form-control" placeholder="Opsional">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="<?= base_url('class_management/sessions') ?>" class="btn btn-default">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
