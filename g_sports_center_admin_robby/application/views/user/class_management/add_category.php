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
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Category Information</h3>
                        </div>
                        <form action="<?= base_url('class_management/add_category') ?>" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g., Renang, Taekwondo" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" rows="4" class="form-control" placeholder="Briefly describe the category..."></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Category
                                </button>
                                <a href="<?= base_url('class_management') ?>" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Guidelines</h3>
                        </div>
                        <div class="card-body">
                            <h5>What is a Category?</h5>
                            <p>A category represents a broad group of classes or training programs offered at G Sports Center.</p>
                            <ul>
                                <li><strong>Examples:</strong> Renang, Futsal, Silat Harimau, Taekwondo.</li>
                                <li><strong>Classes:</strong> Once a category is created, you can add specific classes within it (e.g., "Renang Basic", "Renang Advanced").</li>
                            </ul>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i>
                                Each category will be displayed as a card on the main Class Management page.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
