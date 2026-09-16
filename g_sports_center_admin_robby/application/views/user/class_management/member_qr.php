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
                        <li class="breadcrumb-item active">QR Member</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="mb-1"><?= htmlspecialchars($class_type['name'] ?? '') ?></h4>
                            <p class="text-muted mb-1">
                                Member: <strong><?= htmlspecialchars($member['member_name'] ?? '') ?></strong>
                            </p>
                            <p class="small text-muted mb-3">
                                Email: <?= htmlspecialchars($member['email'] ?? '-') ?>
                            </p>

                            <div class="my-4">
                                <img src="data:image/svg+xml;base64,<?= $qr_base64 ?>" alt="QR Member" class="img-fluid" style="max-width:320px;">
                            </div>

                            <p class="small text-muted mb-3">
                                QR ini di-scan oleh karyawan melalui menu <strong>Scan Absensi Kelas</strong>.
                            </p>

                            <p class="small text-muted text-break">Payload: <code><?= htmlspecialchars($payload_hint) ?></code></p>

                            <div class="mt-3">
                                <button class="btn btn-primary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print
                                </button>
                                <a href="<?= base_url('class_management/class_members/' . (int)($class_type['category_id'] ?? 0)) ?>" class="btn btn-default">
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
@media print {
  .main-sidebar, .main-header, .content-header, .card-footer, .btn { display: none !important; }
  .content-wrapper { margin: 0 !important; padding: 0 !important; }
  .card { border: none !important; box-shadow: none !important; }
}
</style>

