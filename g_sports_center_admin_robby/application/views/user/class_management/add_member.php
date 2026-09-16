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
                        <li class="breadcrumb-item"><a href="<?= base_url('class_management/class_members/' . $class_type['category_id']) ?>"><?= $class_type['category_name'] ?></a></li>
                        <li class="breadcrumb-item active"><?= $class_type['name'] ?> - Add Member</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Member</h3>
                        </div>
                        <form action="<?= base_url('class_management/add_member/' . $class_type['id']) ?>" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="user_id">Select User Account (Role: User) <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control select2" required style="width: 100%;">
                                        <option value="">-- Select User Email --</option>
                                        <?php foreach ($app_users as $user): ?>
                                            <option value="<?= $user['id_user'] ?>">
                                                <?= htmlspecialchars($user['email']) ?> - <?= htmlspecialchars($user['nama']) ?> (Phone: <?= htmlspecialchars($user['nohp']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Pilih akun user yang sudah terdaftar untuk login di aplikasi mobile.</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="generate_qr" name="generate_qr" value="1" checked>
                                        <label class="custom-control-label" for="generate_qr">
                                            Generate QR/Barcode untuk member ini
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        QR ini adalah identitas member untuk absensi. Nanti di-scan oleh karyawan/admin saat pertemuan kelas.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Class Details</label>
                                    <div class="alert alert-info">
                                        <strong>Class:</strong> <?= htmlspecialchars($class_type['name']) ?><br>
                                        <strong>Instructor:</strong> <?= htmlspecialchars($class_type['instructor_name']) ?><br>
                                        <strong>Duration:</strong> <?= $class_type['duration_minutes'] ?> minutes<br>
                                        <strong>Max Participants:</strong> <?= $class_type['max_participants'] ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Member
                                </button>
                                <a href="<?= base_url('class_management/class_members/' . $class_type['category_id']) ?>" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
