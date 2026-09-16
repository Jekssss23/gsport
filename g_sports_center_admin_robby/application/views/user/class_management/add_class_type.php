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
                        <li class="breadcrumb-item"><a href="<?= base_url('class_management/class_types') ?>">Class Types</a></li>
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
                        <form action="<?= base_url('class_management/add_class_type') ?>" method="post">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">Category <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="">Select Category</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Class Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" rows="3" class="form-control" 
                                              placeholder="Describe the class content, requirements, etc."></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instructor_name">Instructor Name <span class="text-danger">*</span></label>
                                            <input type="text" name="instructor_name" id="instructor_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instructor_phone">Instructor Phone</label>
                                            <input type="text" name="instructor_phone" id="instructor_phone" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duration_minutes">Duration (minutes) <span class="text-danger">*</span></label>
                                            <input type="number" name="duration_minutes" id="duration_minutes" 
                                                   class="form-control" min="30" max="240" value="60" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_participants">Max Participants <span class="text-danger">*</span></label>
                                            <input type="number" name="max_participants" id="max_participants" 
                                                   class="form-control" min="1" max="50" value="10" required>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5 class="mb-3">Jadwal pertemuan</h5>
                                <p class="text-muted small">
                                    Pilih <strong>hari</strong>, <strong>jam</strong>, dan <strong>jumlah pertemuan</strong> (1–10).
                                    Sistem akan otomatis membuat sesi pertemuan mingguan.
                                </p>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="day_of_week">Hari <span class="text-danger">*</span></label>
                                            <select name="day_of_week" id="day_of_week" class="form-control" required>
                                                <option value="">Pilih Hari</option>
                                                <option value="Monday">Senin</option>
                                                <option value="Tuesday">Selasa</option>
                                                <option value="Wednesday">Rabu</option>
                                                <option value="Thursday">Kamis</option>
                                                <option value="Friday">Jumat</option>
                                                <option value="Saturday">Sabtu</option>
                                                <option value="Sunday">Minggu</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_time">Jam Mulai <span class="text-danger">*</span></label>
                                            <input type="time" name="start_time" id="start_time" class="form-control" value="09:00" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="room">Ruang (opsional)</label>
                                            <input type="text" name="room" id="room" class="form-control" placeholder="-">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Mulai dari tanggal (opsional)</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control">
                                            <small class="text-muted">Kalau kosong, sistem pakai tanggal hari ini untuk mencari pertemuan terdekat.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="total_meetings">Jumlah Pertemuan <span class="text-danger">*</span></label>
                                            <select name="total_meetings" id="total_meetings" class="form-control" required>
                                                <?php for ($i=1; $i<=10; $i++): ?>
                                                    <option value="<?= $i ?>" <?= $i===4 ? 'selected' : '' ?>><?= $i ?> pertemuan</option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Class
                                </button>
                                <a href="<?= base_url('class_management/class_types') ?>" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Guidelines</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box bg-blue">
                                <span class="info-box-icon"><i class="fas fa-info"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tips</span>
                                    <span class="info-box-number">Class Setup</span>
                                </div>
                            </div>
                            
                            <h5>Class Information</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Choose appropriate category</li>
                                <li><i class="fas fa-check text-success"></i> Use descriptive class names</li>
                                <li><i class="fas fa-check text-success"></i> Provide clear description</li>
                                <li><i class="fas fa-check text-success"></i> Include instructor details</li>
                            </ul>

                            <h5>Pricing & Schedule</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Set competitive pricing</li>
                                <li><i class="fas fa-check text-success"></i> Reasonable duration (30-240 min)</li>
                                <li><i class="fas fa-check text-success"></i> Appropriate class size</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
