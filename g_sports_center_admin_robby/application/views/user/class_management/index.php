<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?= $judul ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
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
                            <h3 class="card-title">Class Management</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('class_management/sessions') ?>" class="btn btn-secondary btn-sm" title="QR absensi per pertemuan">
                                    <i class="fas fa-calendar-check"></i> Pertemuan &amp; QR
                                </a>
                                <a href="<?= base_url('class_management/add_category') ?>" class="btn btn-primary btn-sm ml-1">
                                    <i class="fas fa-plus"></i> Add Category
                                </a>
                                <a href="<?= base_url('class_management/add_class_type') ?>" class="btn btn-success btn-sm ml-1">
                                    <i class="fas fa-plus"></i> Add Class
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($categories)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-layer-group fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No class categories found. Add your first category to get started.</p>
                                    <a href="<?= base_url('class_management/add_category') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Category
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($categories as $category): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card card-outline card-primary">
                                                <div class="card-header">
                                                    <h4 class="card-title"><?= htmlspecialchars($category['name']) ?></h4>
                                                    <div class="card-tools">
                                                        <a href="<?= base_url('class_management/edit_category/' . $category['id']) ?>" class="btn btn-tool btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= base_url('class_management/delete_category/' . $category['id']) ?>" 
                                                           class="btn btn-tool btn-sm" 
                                                           onclick="return confirm('Are you sure you want to delete this category?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <?php if ($category['description']): ?>
                                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($category['description']) ?></p>
                                                    <?php endif; ?>
                                                    
                                                    <div class="mb-2">
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-dumbbell"></i> 
                                                            <?= count($category['class_types']) ?> Classes
                                                        </span>
                                                    </div>

                                                    <?php if (!empty($category['class_types'])): ?>
                                                        <div class="class-list mt-3">
                                                            <h6 class="text-bold mb-2">Daftar Kelas:</h6>
                                                            <?php foreach ($category['class_types'] as $class_type): ?>
                                                                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-1">
                                                                    <div>
                                                                        <i class="fas fa-circle text-xs text-success mr-1"></i>
                                                                        <span class="text-sm"><?= htmlspecialchars($class_type['name']) ?></span>
                                                                        <br>
                                                                        <small class="text-muted"><?= htmlspecialchars($class_type['instructor_name']) ?></small>
                                                                    </div>
                                                                    <div class="btn-group">
                                                                        <a href="<?= base_url('class_management/edit_class_type/' . $class_type['id']) ?>" 
                                                                           class="btn btn-xs btn-outline-primary" title="Edit Kelas">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <a href="<?= base_url('class_management/delete_class_type/' . $class_type['id']) ?>" 
                                                                           class="btn btn-xs btn-outline-danger ml-1" 
                                                                           onclick="return confirm('Hapus kelas ini?')" title="Hapus Kelas">
                                                                            <i class="fas fa-trash"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="text-center py-3">
                                                            <small class="text-muted">Belum ada kelas di kategori ini.</small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-footer">
                                                    <a href="<?= base_url('class_management/class_members/' . $category['id']) ?>" 
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-users"></i> Manage Members
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.class-list {
    max-height: 120px;
    overflow-y: auto;
}
</style>
