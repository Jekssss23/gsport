<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Big_data extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['cloudinarylib', 'firebase_admin']);
        $this->load->model([
            'Event_model' => 'event_model',
            'Employee_model' => 'employee_m',
            'Attendance_model' => 'attendance_m',
        ]);
        $this->attendance_m->ensure_schema();

        $hakAkses = (array) $this->session->userdata('id_hak_akses');
        $allowed = in_array(1, $hakAkses) || in_array(2, $hakAkses); // Pimpinan or Admin
        if (!$allowed) {
            redirect('auth/login/kick');
        }
    }

    public function index()
    {
        // redirect ke KPI sebagai landing default
        redirect('user/big_data/kpi');
    }

    public function kpi()
    {
        $data['judul'] = 'KPI';
        $data['deskripsi'] = 'Key Performance Indicator G-Sports Center.';
        
        // Load KPI model
        $this->load->model('Kpi_model', 'kpi');
        
        // Get filters
        $period = $this->input->get('period') ?: date('Y-m');
        $division = $this->input->get('division') ?: 'All';
        $search = $this->input->get('search');
        
        // Get data
        $data['assessments'] = $this->kpi->get_assessments($period, $division, $search);
        $data['divisions'] = ['All' => 'Semua Divisi'] + array_combine(
            $this->kpi->get_divisions(), 
            $this->kpi->get_divisions()
        );
        $data['current_period'] = $period;
        $data['current_division'] = $division;
        $data['search_term'] = $search;
        
        // Get statistics
        $data['stats'] = $this->kpi->get_kpi_stats($period);
        
        $data['modal'] = '';
        $this->template->load('template/user_bigdata', 'user/big_data/kpi', $data);
    }
    
    public function kpi_assessment()
    {
        $data['judul'] = 'KPI Assessment';
        $data['deskripsi'] = 'Penilaian KPI karyawan.';
        
        // Load models
        $this->load->model('Kpi_model', 'kpi');
        
        $employee_id = $this->input->get('employee_id');
        $period = $this->input->get('period') ?: date('Y-m');
        
        if (!$employee_id) {
            // Show employee selection
            $employees = $this->db->query("
                SELECT id, nama, jabatan 
                FROM employees 
                WHERE status = 'active' 
                ORDER BY nama ASC
            ")->result_array();
            
            $data['employees'] = $employees;
            $data['view_type'] = 'selection';
        } else {
            // Show assessment form
            $employee = $this->employee_m->get_by_id($employee_id);
            
            if (!$employee) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Karyawan tidak ditemukan</div>');
                redirect('user/big_data/kpi_assessment');
                return;
            }
            
            // Normalize division name
            $division = $this->normalize_division($employee['jabatan']);
            $templates = $this->kpi->get_kpi_templates($division);
            $existing_assessment = $this->kpi->get_assessment($employee_id, $period);
            
            $data['employee'] = $employee;
            $data['division'] = $division;
            $data['templates'] = $templates;
            $data['period'] = $period;
            $data['existing_assessment'] = $existing_assessment;
            $data['view_type'] = 'assessment';
        }
        
        $data['modal'] = '';
        $this->template->load('template/user_bigdata', 'user/big_data/kpi_assessment', $data);
    }
    
    public function kpi_save()
    {
        $this->load->model('Kpi_model', 'kpi');
        
        $employee_id = $this->input->post('employee_id');
        $period = $this->input->post('period');
        
        if (!$employee_id || !$period) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Data tidak lengkap</div>');
            redirect('user/big_data/kpi_assessment');
            return;
        }
        
        // Get employee data
        $employee = $this->employee_m->get_by_id($employee_id);
        if (!$employee) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Karyawan tidak ditemukan</div>');
            redirect('user/big_data/kpi_assessment');
            return;
        }
        
        $division = $this->normalize_division($employee['jabatan']);
        $templates = $this->kpi->get_kpi_templates($division);
        
        // Calculate scores
        $scores = [];
        $total_score = 0;
        
        foreach ($templates as $template) {
            $value = $this->input->post($template['field_key']);
            $scores[$template['field_key']] = (float) $value;
            $total_score += (float) $value;
        }
        
        $assessment_data = [
            'employee_id' => $employee_id,
            'employee_name' => $employee['nama'],
            'division' => $division,
            'period' => $period,
            'scores' => $scores,
            'total_score' => $total_score,
            'notes' => $this->input->post('notes')
        ];
        
        $result = $this->kpi->save_assessment($assessment_data);
        
        if ($result['success']) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Penilaian KPI berhasil disimpan</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menyimpan penilaian</div>');
        }
        
        redirect('user/big_data/kpi_assessment?employee_id=' . $employee_id . '&period=' . $period);
    }
    
    public function kpi_delete($assessment_id)
    {
        $this->load->model('Kpi_model', 'kpi');
        
        $deleted = $this->kpi->delete_assessment($assessment_id);
        if ($deleted) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Penilaian KPI berhasil dihapus</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menghapus penilaian</div>');
        }
        
        redirect('user/big_data/kpi');
    }
    
    private function normalize_division($jabatan)
    {
        $jabatan = strtolower(trim($jabatan));
        
        $mapping = [
            'cafe' => 'Caffe',
            'caffe' => 'Caffe',
            'housekeeping' => 'HK',
            'hk' => 'HK',
            'entertainment' => 'Entertain',
            'entertain' => 'Entertain',
            'sports' => 'Sports',
            'gro' => 'GRO',
            'security' => 'Security',
            'maintenance' => 'Maintenance',
            'marketing' => 'Marketing'
        ];
        
        return $mapping[$jabatan] ?? ucfirst($jabatan);
    }

    public function attendance()
    {
        $data['judul'] = 'Attendance Management';
        $data['deskripsi'] = 'Kelola data kehadiran karyawan dengan sistem GPS-based attendance.';
        
        // Load attendance model
        $this->load->model('Attendance_model', 'attendance');

        // Alpha otomatis: kemarin + hari ini (jika sudah lewat batas shift/cutoff)
        $this->attendance->sync_alpha_for_date(date('Y-m-d', strtotime('-1 day')));
        $this->attendance->sync_alpha_for_date(date('Y-m-d'));
        
        // Get filters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $status = $this->input->get('status') ?: 'all';
        $search = $this->input->get('search');
        
        // Get data
        $data['attendances'] = $this->attendance->get_attendances($date_from, $date_to, $status, $search);
        $data['settings'] = $this->attendance->get_settings();
        $data['today_attendances'] = $this->attendance->get_today_attendances();
        $data['stats'] = $this->attendance->get_attendance_stats();
        
        // Filter values
        $data['current_date_from'] = $date_from;
        $data['current_date_to'] = $date_to;
        $data['current_status'] = $status;
        $data['search_term'] = $search;
        $data['shift_definitions'] = Attendance_model::shift_definitions();
        
        $data['modal'] = '';
        $this->template->load('template/user_bigdata', 'user/big_data/attendance', $data);
    }
    
    public function attendance_settings()
    {
        $data['judul'] = 'Attendance Settings';
        $data['deskripsi'] = 'Pengaturan lokasi dan radius untuk absensi GPS.';
        
        // Load attendance model
        $this->load->model('Attendance_model', 'attendance');
        
        if ($this->input->post()) {
            $settings_data = [
                'location_name' => $this->input->post('location_name'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'radius_meters' => $this->input->post('radius_meters'),
                'grace_period_minutes' => $this->input->post('grace_period_minutes'),
                'alpha_cutoff_time' => $this->input->post('alpha_cutoff_time'),
                'created_by' => $this->session->userdata('id_user')
            ];
            
            $result = $this->attendance->save_settings($settings_data);
            
            if ($result['success']) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success">Pengaturan berhasil disimpan</div>');
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menyimpan pengaturan</div>');
            }
            
            redirect('user/big_data/attendance_settings');
        }
        
        $data['settings'] = $this->attendance->get_settings();
        $data['modal'] = '';
        $this->template->load('template/user_bigdata', 'user/big_data/attendance_settings', $data);
    }
    
    public function attendance_delete($id)
    {
        $this->load->model('Attendance_model', 'attendance');
        
        // Get attendance data
        $attendance = $this->db->get_where('attendances', ['id' => $id])->row_array();
        
        if (!$attendance) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Data absensi tidak ditemukan</div>');
            redirect('user/big_data/attendance');
        }
        
        $this->db->where('id', (int) $id);
        $this->db->delete('attendances');
        $deleted = $this->db->affected_rows();

        if ($deleted > 0) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Data absensi berhasil dihapus</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menghapus — data tidak ditemukan atau sudah terhapus</div>');
        }
        redirect('user/big_data/attendance');
    }
    
    public function attendance_sync_alpha()
    {
        $this->load->model('Attendance_model', 'attendance');

        $date = $this->input->post('sync_date') ?: date('Y-m-d', strtotime('-1 day'));
        $force = $this->input->post('force') === '1';

        $result = $this->attendance->sync_alpha_for_date($date, ['force' => $force]);

        if ($result['success']) {
            $this->session->set_flashdata(
                'pesan',
                '<div class="alert alert-success">Alpha otomatis: ' . $result['total'] . ' record (' . $result['created'] . ' baru, ' . $result['updated'] . ' diperbarui) untuk tanggal ' . date('d M Y', strtotime($date)) . '.</div>'
            );
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal memproses alpha otomatis</div>');
        }

        redirect('user/big_data/attendance');
    }

    public function attendance_archive()
    {
        $this->load->model('Attendance_model', 'attendance');
        
        $date = $this->input->post('archive_date') ?: date('Y-m-d');
        
        $result = $this->attendance->archive_attendances($date);
        
        if ($result['success']) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">' . $result['message'] . ' (' . $result['count'] . ' data)</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">' . $result['message'] . '</div>');
        }
        
        redirect('user/big_data/attendance');
    }
    
    public function employee()
    {
        $data['judul'] = 'Employee Management';
        $data['deskripsi'] = 'Data karyawan dan peran di G-Sports Center.';
        
        $data['employees'] = $this->employee_m->get_all_active();
        
        $data['modal'] = '';
        $this->template->load('template/user_bigdata', 'user/big_data/employee', $data);
    }

    public function employee_save()
    {
        $id_employee = $this->input->post('id_employee') ?: $this->input->post('id_user');
        $nama = $this->input->post('nama');
        $jabatan = $this->input->post('jabatan');
        $email = $this->input->post('email');
        $nohp = $this->input->post('nohp');
        $alamat = $this->input->post('alamat') ?: '';
        $jadwal_operasional = $this->input->post('jadwal_operasional') ?: '';
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $is_app_admin = $this->input->post('is_app_admin');

        $data = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'email' => $email,
            'nohp' => $nohp,
            'alamat' => $alamat,
            'jadwal_operasional' => $jadwal_operasional,
            'app_username' => $username ?: $email,
            'has_app_login' => $is_app_admin ? 1 : 0,
            'status' => 'active',
        ];

        if (!empty($_FILES['foto']['tmp_name'])) {
            $upload = $this->cloudinarylib->upload($_FILES['foto']['tmp_name']);
            if ($upload) {
                $data['foto'] = $upload['secure_url'];
            }
        }

        $uid_to_set = null;
        $must_sync_app = ($is_app_admin && $email);

        if ($must_sync_app) {
            if ($id_employee) {
                $current = $this->employee_m->get_by_id($id_employee);
                $uid_to_set = ($current && !empty($current['firebase_uid'])) ? $current['firebase_uid'] : null;
            }

            // If we still don't have a UID, lookup by email or create a new Firebase user
            if (!$uid_to_set) {
                $fb_user = $this->firebase_admin->get_user_by_email($email);
                if ($fb_user && isset($fb_user->localId) && !empty($fb_user->localId)) {
                    $uid_to_set = $fb_user->localId;
                    log_message('info', 'Big_data employee_save: Found existing Firebase user by email. UID: ' . $uid_to_set);
                } else {
                    if (!$password) {
                        $this->session->set_flashdata('pesan_fb', '<div class="alert alert-danger">Password wajib diisi untuk membuat akun App baru.</div>');
                        redirect('user/big_data/employee');
                        return;
                    }

                    $fb_user = $this->firebase_admin->create_user([
                        'email' => $email,
                        'password' => $password,
                        'displayName' => $nama
                    ]);

                    if ($fb_user && isset($fb_user->localId) && !empty($fb_user->localId)) {
                        $uid_to_set = $fb_user->localId;
                        log_message('info', 'Big_data employee_save: Created Firebase Auth user. UID: ' . $uid_to_set);
                    } else if (isset($fb_user->error)) {
                        log_message('error', 'Big_data employee_save: Firebase create_user failed: ' . $fb_user->error);
                        $this->session->set_flashdata('pesan_fb', '<div class="alert alert-danger">Gagal membuat akun Firebase: ' . $fb_user->error . '</div>');
                        redirect('user/big_data/employee');
                        return;
                    } else {
                        log_message('error', 'Big_data employee_save: Firebase create_user failed: unknown response');
                        $this->session->set_flashdata('pesan_fb', '<div class="alert alert-danger">Gagal menghubungi Firebase Server. Silakan coba lagi.</div>');
                        redirect('user/big_data/employee');
                        return;
                    }
                }
            }

            // 2) Firestore must succeed before we touch SQL firebase_uid
            $firestore_success = $this->firebase_admin->create_firestore_document('users', $uid_to_set, [
                'id' => $uid_to_set,
                'name' => $nama,
                'email' => $email,
                'role' => 'admin',
                'jabatan' => $jabatan,
                'phone' => $nohp,
                'imageUrl' => $data['foto'] ?? '',
                // sqlEmployeeId will be attached after SQL write (for new employees)
                'updatedAt' => date('Y-m-d H:i:s')
            ]);

            if (!Firebase_admin::is_write_success($firestore_success)) {
                log_message('error', 'Big_data employee_save: Firestore users write failed for UID: ' . $uid_to_set . ' | email: ' . $email . ' | err: ' . (is_string($firestore_success) ? $firestore_success : $this->firebase_admin->get_last_error()));
                $this->session->set_flashdata('pesan_fb', '<div class="alert alert-danger">Gagal membuat akun di Cloud Firestore (users). SQL tidak diproses.</div>');
                redirect('user/big_data/employee');
                return;
            }
        }

        if ($uid_to_set) {
            $data['firebase_uid'] = $uid_to_set;
        }

        try {
            $emp_id = $this->employee_m->save($data, $id_employee ?: null);
        } catch (Exception $e) {
            if (!empty($uid_to_set)) {
                $this->firebase_admin->delete_firestore_document('users', $uid_to_set);
            }
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menyimpan data karyawan: ' . htmlspecialchars($e->getMessage()) . '</div>');
            redirect('user/big_data/employee');
            return;
        }

        if (!$emp_id) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menyimpan data karyawan ke tabel employees.</div>');
            redirect('user/big_data/employee');
            return;
        }

        $this->session->set_flashdata('pesan', '<div class="alert alert-success">Karyawan berhasil ' . ($id_employee ? 'diperbarui' : 'ditambahkan') . ' (tabel employees)</div>');

        if ($must_sync_app && !empty($uid_to_set) && !empty($emp_id)) {
            $this->firebase_admin->create_firestore_document('users', $uid_to_set, [
                'sqlEmployeeId' => (string)$emp_id,
                'updatedAt' => date('Y-m-d H:i:s')
            ]);

            // KPI collection used by this module (best-effort)
            $this->firebase_admin->create_firestore_document('employees', 'EMP_'.$emp_id, [
                'id' => 'EMP_'.$emp_id,
                'name' => $nama,
                'divisi' => $jabatan,
                'imageUrl' => $data['foto'] ?? '',
                'email' => $email,
                'authUid' => $uid_to_set,
                'hasAccount' => true,
                'createdAt' => date('Y-m-d H:i:s')
            ]);

            $this->session->set_flashdata('pesan_fb', '<div class="alert alert-success">Akun App berhasil dibuat & tersinkron. Firebase UID: ' . $uid_to_set . '</div>');
        }

        redirect('user/big_data/employee');
    }

    public function employee_delete($id)
    {
        $employee = $this->employee_m->get_by_id($id);
        if (!$employee) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Karyawan tidak ditemukan</div>');
            redirect('user/big_data/employee');
            return;
        }

        $firebase_uid = $employee['firebase_uid'] ?? null;

        // 2) Cleanup Firebase Firestore docs (idempotent)
        $firebase_ok = true;
        if (!empty($firebase_uid)) {
            // Delete users/{uid} (role/login profile)
            $firebase_ok = $this->firebase_admin->delete_firestore_document('users', $firebase_uid) && $firebase_ok;
        }

        // Also delete employees/EMP_{id} if it exists (KPI collection used by this project)
        $firebase_ok = $this->firebase_admin->delete_firestore_document('employees', 'EMP_' . $id) && $firebase_ok;

        $deleted = $this->employee_m->hard_delete($id);

        if (!$deleted) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menghapus karyawan dari tabel employees</div>');
            redirect('user/big_data/employee');
            return;
        }

        if ($firebase_ok) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Karyawan berhasil dihapus (employees + Firebase)</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-warning">SQL employees berhasil dihapus, tapi ada data Firebase yang gagal dihapus. Cek log server.</div>');
        }

        redirect('user/big_data/employee');
    }

    public function data_reset()
    {
        $this->load->model('Bigdata_reset_model', 'reset_m');

        if ($this->input->post('confirm_reset') === '1') {
            $typed = trim($this->input->post('confirm_text'));
            $pin = trim($this->input->post('confirm_pin'));

            if ($pin !== '1029') {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">PIN Reset salah!</div>');
                redirect('user/big_data/data_reset');
                return;
            }

            if ($typed !== 'RESET BIG DATA') {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Konfirmasi gagal. Ketik tepat: <strong>RESET BIG DATA</strong></div>');
                redirect('user/big_data/data_reset');
                return;
            }

            $result = $this->reset_m->reset_operational_data([
                'clear_firestore_reviews' => $this->input->post('clear_firestore_reviews') === '1',
                'clear_firestore_events' => $this->input->post('clear_firestore_events') === '1',
                'clear_firestore_employees' => $this->input->post('clear_firestore_employees') === '1',
            ]);

            if ($result['success']) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success">' . $result['message'] . '. MySQL: ' . json_encode($result['mysql']) . '</div>');
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">' . ($result['message'] ?? 'Reset gagal') . '</div>');
            }
            redirect('user/big_data/data_reset');
            return;
        }

        $data['judul'] = 'Reset Data Big Data';
        $data['deskripsi'] = 'Kosongkan HANYA data transaksi menu Big Data. Pengaturan absensi, template KPI, dan master_user TIDAK dihapus.';
        $data['preview'] = $this->reset_m->get_reset_preview();
        $data['protected'] = $this->reset_m->get_protected_preview();
        $data['modal'] = '';
        $this->template->load('template/user_bigdata', 'user/big_data/data_reset', $data);
    }

    public function reservation()
    {
        $data['judul'] = 'Reservation Management';
        $data['deskripsi'] = 'Ringkasan data reservasi dari berbagai channel (web/mobile).';
        
        // Load reservation model
        $this->load->model('Reservation_model', 'reservation');
        
        // Get real data
        $data['pending_reservations'] = $this->reservation->get_all_pending_reservations();
        $data['confirmed_reservations'] = $this->reservation->get_confirmed_reservations();
        $data['archived_reservations'] = $this->reservation->get_archived_reservations(10, 0); // Latest 10 archived
        
        // Get statistics
        $data['stats'] = $this->get_reservation_stats();
        
        $data['modal'] = '';
        $this->template->load('template/user_bigdata', 'user/big_data/reservation', $data);
    }
    
    private function get_reservation_stats()
    {
        $stats = [];
        
        $in = "'confirmed','completed','selesai'";
        $q = $this->db->query("
            SELECT 
                CASE WHEN combined.firebase_uid IS NOT NULL AND TRIM(combined.firebase_uid) <> '' THEN 'Mobile App' ELSE 'Web / Admin / Walk-in' END as channel,
                COUNT(*) as count
            FROM (
                SELECT firebase_uid, status, created_at FROM reservations
                UNION ALL
                SELECT firebase_uid, status, created_at FROM reservations_archive
            ) AS combined
            WHERE combined.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
              AND combined.status IN ({$in})
            GROUP BY channel
        ")->result_array();
        
        $stats['channels'] = [
            'labels' => [],
            'data' => []
        ];
        foreach ($q as $row) {
            $stats['channels']['labels'][] = $row['channel'];
            $stats['channels']['data'][] = (int)$row['count'];
        }
        
        // Facility popularity
        $q = $this->db->query("
            SELECT name, COUNT(*) as bookings
            FROM (
                SELECT r.facility_id, r.status, r.created_at FROM reservations r
                UNION ALL
                SELECT ra.facility_id, ra.status, ra.created_at FROM reservations_archive ra
            ) AS combined_r
            JOIN reservation_facilities f ON combined_r.facility_id = f.id
            WHERE combined_r.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
              AND combined_r.status IN ({$in})
            GROUP BY f.id, f.name
            ORDER BY bookings DESC
            LIMIT 5
        ")->result_array();
        
        $stats['facilities'] = [
            'labels' => [],
            'data' => []
        ];
        foreach ($q as $row) {
            $stats['facilities']['labels'][] = $row['name'];
            $stats['facilities']['data'][] = (int)$row['bookings'];
        }
        
        return $stats;
    }

    public function monitoring()
    {
        $data['judul'] = 'Monitoring';
        $data['deskripsi'] = 'Dashboard operasional — data diambil langsung dari MySQL & Firestore (reviews).';
        $data['modal'] = '';
        $data['monitoring_year'] = (int) date('Y');

        $this->load->model('Monitoring_model', 'monitoring');
        $this->load->model('Kpi_model', 'kpi');
        $this->load->library('firebase_admin');

        $data['reservation_stats'] = $this->monitoring->get_reservation_stats_for_monitoring();
        $data['attendance_stats'] = $this->monitoring->get_attendance_stats_current_month();
        $data['kpi_stats'] = $this->kpi->get_kpi_stats(date('Y-m'));
        $data['rating_stats'] = $this->monitoring->get_rating_stats($this->firebase_admin);

        $finance = $this->monitoring->get_finance_dashboard($data['monitoring_year']);
        $data['finance_stats'] = $finance['finance_stats'];
        $data['package_finance_stats'] = $finance['package_finance_stats'];
        $data['finance_breakdown'] = $finance['finance_breakdown'];
        $data['finance_reservation_monthly'] = $finance['reservation_monthly'];

        $this->template->load('template/user_bigdata', 'user/big_data/monitoring', $data);
    }

    public function rating()
    {
        $data['judul'] = 'View Rating';
        $data['deskripsi'] = 'Feedback & rating dari pengunjung.';
        $data['modal'] = '';
        
        // Load Firebase Admin library
        $this->load->library('firebase_admin');
        
        try {
            // Fetch all reviews from Firestore
            $reviews = $this->firebase_admin->get_all_documents('reviews');
            
            // Calculate statistics
            $total_ratings = count($reviews);
            $sum_ratings = 0;
            $positive_count = 0;
            $critical_count = 0;
            
            foreach ($reviews as $review) {
                if (isset($review['rating'])) {
                    $rating = (int)$review['rating'];
                    $sum_ratings += $rating;
                    if ($rating >= 4) {
                        $positive_count++;
                    } elseif ($rating <= 2) {
                        $critical_count++;
                    }
                }
            }
            
            $avg_rating = $total_ratings > 0 ? round($sum_ratings / $total_ratings, 1) : 0;
            $positive_percentage = $total_ratings > 0 ? round(($positive_count / $total_ratings) * 100) : 0;
            $critical_percentage = $total_ratings > 0 ? round(($critical_count / $total_ratings) * 100) : 0;
            
            // Prepare data for view
            $data['stats'] = [
                'total_ratings' => $total_ratings,
                'avg_rating' => $avg_rating,
                'positive_percentage' => $positive_percentage,
                'critical_percentage' => $critical_percentage
            ];
            
            $data['reviews'] = $reviews;
            
        } catch (Exception $e) {
            // Fallback to empty data if Firebase fails
            log_message('error', 'Failed to fetch ratings from Firestore: ' . $e->getMessage());
            $data['stats'] = [
                'total_ratings' => 0,
                'avg_rating' => 0,
                'positive_percentage' => 0,
                'critical_percentage' => 0
            ];
            $data['reviews'] = [];
        }
        
        $this->template->load('template/user_bigdata', 'user/big_data/rating', $data);
    }

    private function normalize_event_time($time)
    {
        $time = trim((string) $time);
        if (preg_match('/^(\d{1,2}):(\d{2})/', $time, $m)) {
            return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
        }
        return $time;
    }

    private function dispatch_event_notifications($event_id, $name, $start_date, $start_time)
    {
        $users = $this->firebase_admin->get_all_documents('users');
        if (empty($users)) {
            return;
        }

        $notifTitle = 'Event Baru G Sports Center';
        $notifBody = $name . ' (' . $start_date . ' ' . $start_time . ')';
        $stamp = date('YmdHis');

        foreach ($users as $u) {
            $uid = (string) ($u['id'] ?? '');
            if ($uid === '') {
                continue;
            }

            $role = strtolower((string) ($u['role'] ?? 'user'));
            if ($role !== 'user') {
                continue;
            }

            $notifId = 'EVN' . $stamp . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $uid);
            $this->firebase_admin->create_firestore_document('notifications', $notifId, [
                'id' => $notifId,
                'user_id' => $uid,
                'type' => 'event',
                'title' => $notifTitle,
                'message' => $notifBody,
                'event_id' => (string) $event_id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $pushToken = $u['pushToken'] ?? $u['expoPushToken'] ?? '';
            if (!empty($pushToken)) {
                $this->firebase_admin->send_fcm_notification($pushToken, $notifTitle, $notifBody, [
                    'type' => 'event',
                    'event_id' => (string) $event_id,
                ]);
            }
        }
    }

    private function merge_event_lists($firebase_events, $mysql_events)
    {
        $by_id = [];
        foreach ($mysql_events as $row) {
            if (!empty($row['id'])) {
                $by_id[$row['id']] = $row;
            }
        }
        foreach ($firebase_events as $row) {
            if (!empty($row['id'])) {
                $by_id[$row['id']] = $row;
            }
        }

        $merged = array_values($by_id);
        usort($merged, function ($a, $b) {
            $aTime = strtotime(($a['start_date'] ?? '') . ' ' . ($a['start_time'] ?? '00:00'));
            $bTime = strtotime(($b['start_date'] ?? '') . ' ' . ($b['start_time'] ?? '00:00'));
            return $bTime <=> $aTime;
        });

        return $merged;
    }

    public function event_organizer()
    {
        $data['judul'] = 'Event Organizer';
        $data['deskripsi'] = 'Kelola event untuk ditampilkan di mobile app.';

        if ($this->input->post()) {
            $name = trim($this->input->post('name'));
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $start_time = $this->normalize_event_time($this->input->post('start_time'));
            $end_time = $this->normalize_event_time($this->input->post('end_time'));

            if ($name === '' || !$start_date || !$end_date || !$start_time || !$end_time) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Semua field wajib diisi.</div>');
                redirect('user/big_data/event_organizer');
                return;
            }

            if (empty($_FILES['image']['tmp_name'])) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gambar event wajib diupload.</div>');
                redirect('user/big_data/event_organizer');
                return;
            }

            $upload = $this->cloudinarylib->upload($_FILES['image']['tmp_name'], 'gsc/events');
            if (!$upload || empty($upload['secure_url'])) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal upload gambar event.</div>');
                redirect('user/big_data/event_organizer');
                return;
            }

            if (!$this->firebase_admin->has_service_account()) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Firebase service account tidak ditemukan di server. Upload <code>application/config/firebase_credentials.json</code> lalu coba lagi.</div>');
                redirect('user/big_data/event_organizer');
                return;
            }

            $event_id = 'EV' . date('YmdHis') . rand(100, 999);
            $payload = [
                'id' => $event_id,
                'name' => $name,
                'image_url' => $upload['secure_url'],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'status' => 'active',
                'created_by' => (string) $this->session->userdata('id_user'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->event_model->upsert(array_merge($payload, ['synced_firebase' => 0]));

            $ok = $this->firebase_admin->create_firestore_document('events', $event_id, $payload);
            if (Firebase_admin::is_write_success($ok)) {
                $this->event_model->mark_synced($event_id);
                
                try {
                    $this->dispatch_event_notifications($event_id, $name, $start_date, $start_time);
                    $this->session->set_flashdata('pesan', '<div class="alert alert-success">Event berhasil ditambahkan dan notifikasi dikirim ke member app.</div>');
                } catch (Exception $e) {
                    log_message('error', 'Failed to dispatch event notifications: ' . $e->getMessage());
                    $this->session->set_flashdata('pesan', '<div class="alert alert-success">Event tersimpan di Firebase. Notifikasi push sebagian gagal — cek log server.</div>');
                }
            } else {
                $error_msg = is_string($ok) ? $ok : $this->firebase_admin->get_last_error();
                if ($error_msg === '') {
                    $error_msg = 'Unknown error — cek log PHP/Firestore di server hosting.';
                }
                
                // Jika Firebase gagal, kita tetap katakan sukses di MySQL tapi peringatkan soal notifikasi
                $this->session->set_flashdata('pesan', '<div class="alert alert-warning">Event disimpan di MySQL tetapi <strong>gagal sinkron ke Firebase</strong>: ' . htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8') . '. <br><strong>Efek:</strong> Notifikasi tidak terkirim otomatis ke HP member.</div>');
            }

            redirect('user/big_data/event_organizer');
            return;
        }

        try {
            $data['events'] = $this->merge_event_lists(
                $this->firebase_admin->get_all_documents('events'),
                $this->event_model->get_all_active()
            );
        } catch (Exception $e) {
            $data['events'] = $this->event_model->get_all_active();
        }

        $data['modal'] = '';
        $this->template->load('template/user_bigdata', 'user/big_data/event_organizer', $data);
    }

    public function event_delete($event_id = '')
    {
        if ($event_id === '') {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">ID event tidak valid.</div>');
            redirect('user/big_data/event_organizer');
            return;
        }

        $event = $this->firebase_admin->get_firestore_document('events', $event_id);
        if (!$event) {
            $event = $this->event_model->get_by_id($event_id);
        }
        if (!$event) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Event tidak ditemukan.</div>');
            redirect('user/big_data/event_organizer');
            return;
        }

        if (!empty($event['image_url'])) {
            $publicId = $this->cloudinarylib->get_public_id_from_url($event['image_url']);
            if ($publicId) {
                $this->cloudinarylib->delete($publicId);
            }
        }

        $deleted = $this->firebase_admin->delete_firestore_document('events', $event_id);
        $this->event_model->delete_by_id($event_id);

        if ($deleted) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Event berhasil dihapus (Firebase + Cloudinary + MySQL).</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-warning">Event dihapus dari MySQL. Firebase mungkin sudah tidak ada atau gagal dihapus.</div>');
        }

        redirect('user/big_data/event_organizer');
    }

    public function event_resend_notif($event_id = '')
    {
        if ($event_id === '') {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">ID event tidak valid.</div>');
            redirect('user/big_data/event_organizer');
            return;
        }

        $event = $this->event_model->get_by_id($event_id);
        if (!$event) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Event tidak ditemukan di MySQL.</div>');
            redirect('user/big_data/event_organizer');
            return;
        }

        $this->load->library('firebase_admin');

        // 1. Cek apakah di Firebase sudah ada, jika belum coba sinkronkan dulu
        $fb_event = $this->firebase_admin->get_firestore_document('events', $event_id);
        if (!$fb_event) {
            $payload = [
                'id' => $event['id'],
                'name' => $event['name'],
                'image_url' => $event['image_url'],
                'start_date' => $event['start_date'],
                'end_date' => $event['end_date'],
                'start_time' => $event['start_time'],
                'end_time' => $event['end_time'],
                'status' => $event['status'],
                'created_by' => $event['created_by'],
                'created_at' => $event['created_at'],
            ];
            $ok = $this->firebase_admin->create_firestore_document('events', $event_id, $payload);
            if (Firebase_admin::is_write_success($ok)) {
                $this->event_model->mark_synced($event_id);
            } else {
                $err = is_string($ok) ? $ok : $this->firebase_admin->get_last_error();
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal sinkronisasi data event ke Firebase: ' . $err . '</div>');
                redirect('user/big_data/event_organizer');
                return;
            }
        }

        // 2. Kirim Notifikasi
        try {
            $this->dispatch_event_notifications($event['id'], $event['name'], $event['start_date'], $event['start_time']);
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Notifikasi berhasil dikirim ulang ke member app.</div>');
        } catch (Exception $e) {
            log_message('error', 'Failed to resend event notifications: ' . $e->getMessage());
            $this->session->set_flashdata('pesan', '<div class="alert alert-warning">Gagal mengirim beberapa notifikasi push. Cek log server.</div>');
        }

        redirect('user/big_data/event_organizer');
    }
}

