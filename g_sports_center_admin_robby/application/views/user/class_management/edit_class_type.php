<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?= $judul ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('class_management') ?>">Class Management</a></li>
                        <li class="breadcrumb-item active"><?= $judul ?></li>
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
                        <div class="card-header">
                            <h3 class="card-title">Class Information</h3>
                        </div>
                        <form action="<?= base_url('class_management/edit_class_type/' . $class_type['id']) ?>" method="post">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">Category <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id'] ?>" <?= $class_type['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Class Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($class_type['name']) ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" rows="3" class="form-control"><?= htmlspecialchars($class_type['description']) ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instructor_name">Instructor Name <span class="text-danger">*</span></label>
                                            <input type="text" name="instructor_name" id="instructor_name" class="form-control" value="<?= htmlspecialchars($class_type['instructor_name']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instructor_phone">Instructor Phone</label>
                                            <input type="text" name="instructor_phone" id="instructor_phone" class="form-control" value="<?= htmlspecialchars($class_type['instructor_phone']) ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duration_minutes">Duration (minutes) <span class="text-danger">*</span></label>
                                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" value="<?= $class_type['duration_minutes'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_participants">Max Participants <span class="text-danger">*</span></label>
                                            <input type="number" name="max_participants" id="max_participants" class="form-control" value="<?= $class_type['max_participants'] ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="total_meetings">Jumlah Pertemuan (Kuota) <span class="text-danger">*</span></label>
                                            <input
                                                type="number"
                                                name="total_meetings"
                                                id="total_meetings"
                                                class="form-control"
                                                min="1"
                                                max="100"
                                                value="<?= htmlspecialchars($class_type['total_meetings'] ?? 0) ?>"
                                                required
                                            >
                                            <small class="text-muted">Contoh: Renang 8 pertemuan.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Class
                                </button>
                                <a href="<?= base_url('class_management') ?>" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Jadwal pertemuan &amp; QR absensi</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('class_management/add_session?class_type_id=' . (int)$class_type['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Tambah pertemuan
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <?php if (empty($sessions)): ?>
                                <p class="p-3 text-muted mb-0">Belum ada pertemuan. Tambah manual atau buat kelas baru dengan jadwal.</p>
                            <?php else: ?>
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ke-</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sessions as $s): ?>
                                            <tr>
                                                <td><?= isset($s['session_number']) ? (int)$s['session_number'] : '—' ?></td>
                                                <td><?= htmlspecialchars($s['session_date'] ?? '') ?></td>
                                                <td><?= htmlspecialchars(substr($s['start_time'] ?? '', 0, 5)) ?> – <?= htmlspecialchars(substr($s['end_time'] ?? '', 0, 5)) ?></td>
                                                <td><?= htmlspecialchars($s['status'] ?? '') ?></td>
                                                <td>
                                                    <a href="<?= base_url('class_management/session_qr/' . (int)$s['id']) ?>" class="btn btn-xs btn-success">QR</a>
                                                    <a href="<?= base_url('class_management/edit_session/' . (int)$s['id']) ?>" class="btn btn-xs btn-default">Edit</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
