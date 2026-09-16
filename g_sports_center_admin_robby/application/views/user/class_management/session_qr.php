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
                        <li class="breadcrumb-item active">QR</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4><?= htmlspecialchars($session['class_name'] ?? '') ?></h4>
                            <p class="text-muted mb-1">
                                Pertemuan ke-<?= isset($session['session_number']) ? (int)$session['session_number'] : '?' ?>
                                &middot; <?= htmlspecialchars($session['session_date'] ?? '') ?>
                            </p>
                            <p class="small text-muted mb-4">
                                <?= htmlspecialchars(substr($session['start_time'] ?? '', 0, 5)) ?> – <?= htmlspecialchars(substr($session['end_time'] ?? '', 0, 5)) ?>
                            </p>
                            <p class="text-sm">Tampilkan QR ini di kelas. Member yang sudah terdaftar scan lewat menu <strong>Jadwal Kelas → Scan absensi</strong> di aplikasi.</p>
                            <div class="my-4">
                                <img src="data:image/svg+xml;base64,<?= $qr_base64 ?>" alt="QR" class="img-fluid" style="max-width:320px;">
                            </div>
                            <p class="small text-muted text-break">Payload: <code><?= htmlspecialchars($payload_hint) ?></code></p>
                            <a href="<?= base_url('class_management/edit_session/' . (int)$session['id']) ?>" class="btn btn-default">Edit sesi</a>
                            <a href="<?= base_url('class_management/sessions') ?>" class="btn btn-primary">Daftar pertemuan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
