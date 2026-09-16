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
                        <li class="breadcrumb-item active"><?= $category['name'] ?> - Members</li>
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
                <?php foreach ($class_types as $class_type): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h4 class="card-title"><?= htmlspecialchars($class_type['name']) ?></h4>
                                <div class="card-tools">
                                    <a href="<?= base_url('class_management/add_member/' . $class_type['id']) ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-user-plus"></i> Add Member
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-sm text-gray-600">
                                    <strong>Instructor:</strong> <?= htmlspecialchars($class_type['instructor_name']) ?>
                                </p>
                                
                                <?php if (empty($class_type['members'])): ?>
                                    <div class="text-center py-3">
                                        <i class="fas fa-users fa-2x text-gray-300 mb-2"></i>
                                        <p class="text-gray-500 mb-0">No members yet</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Progress</th>
                                                    <th>Join Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($class_type['members'] as $member): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($member['member_name']) ?></td>
                                                        <td><?= htmlspecialchars($member['member_phone'] ?: '-') ?></td>
                                                        <td>
                                                            <?php
                                                                $att = isset($member['meetings_attended']) ? (int)$member['meetings_attended'] : 0;
                                                                $total = isset($class_type['total_meetings']) ? (int)$class_type['total_meetings'] : 0;
                                                            ?>
                                                            <span class="badge badge-info"><?= $att ?>/<?= $total > 0 ? $total : '?' ?></span>
                                                        </td>
                                                        <td><?= date('d M Y', strtotime($member['join_date'])) ?></td>
                                                        <td>
                                                            <a href="<?= base_url('class_management/member_qr/' . (int)$member['id']) ?>" class="btn btn-xs btn-success" title="QR Member">
                                                                <i class="fas fa-qrcode"></i>
                                                            </a>
                                                            <a href="<?= base_url('class_management/delete_member/' . $member['id']) ?>" 
                                                               class="btn btn-xs btn-danger" 
                                                               onclick="return confirm('Delete this member?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">
                                    <i class="fas fa-users"></i> 
                                    <?= count($class_type['members']) ?> members
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($class_types)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-dumbbell fa-3x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-600">No classes found</h4>
                    <p class="text-gray-500">Add some classes first to manage members.</p>
                    <a href="<?= base_url('class_management/add_class_type') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Class
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
