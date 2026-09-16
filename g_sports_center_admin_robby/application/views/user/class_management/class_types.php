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
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Class Types</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('class_management/add_class_type') ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Class
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($class_types)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-dumbbell fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No class types found. Add your first class to get started.</p>
                                    <a href="<?= base_url('class_management/add_class_type') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Class
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Class Name</th>
                                                <th>Category</th>
                                                <th>Instructor</th>
                                                <th>Price</th>
                                                <th>Duration</th>
                                                <th>Max Participants</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($class_types as $class_type): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($class_type['name']) ?></strong>
                                                        <?php if ($class_type['description']): ?>
                                                            <br><small class="text-gray-500"><?= htmlspecialchars(substr($class_type['description'], 0, 50)) ?>...</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <?= htmlspecialchars($class_type['category_name']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($class_type['instructor_name']) ?>
                                                        <?php if ($class_type['instructor_phone']): ?>
                                                            <br><small class="text-gray-500"><?= htmlspecialchars($class_type['instructor_phone']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>Rp <?= number_format($class_type['price_per_session'], 0, ',', '.') ?></td>
                                                    <td><?= $class_type['duration_minutes'] ?> min</td>
                                                    <td><?= $class_type['max_participants'] ?></td>
                                                    <td>
                                                        <?php if ($class_type['status'] == 'active'): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="<?= base_url('class_management/edit_class_type/' . $class_type['id']) ?>" 
                                                               class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="<?= base_url('class_management/delete_class_type/' . $class_type['id']) ?>" 
                                                               class="btn btn-danger btn-sm"
                                                               onclick="return confirm('Are you sure you want to delete this class type?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
