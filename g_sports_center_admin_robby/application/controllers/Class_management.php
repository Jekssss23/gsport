<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_management extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Class_management_model', 'class_mgmt');
        $this->load->helper('url');
        $this->load->library('session');
        
        // Check user access - only GRO, Admin, and Pimpinan can access
        if (!$this->session->userdata('login')) {
            redirect('auth');
        }
        
        $hakAkses = (array) $this->session->userdata('id_hak_akses');
        $allowed = in_array(1, $hakAkses) || in_array(2, $hakAkses) || in_array(3, $hakAkses); // Pimpinan, Admin, or GRO
        if (!$allowed) {
            redirect('dashboard');
        }
    }

    public function index() {
        $data['judul'] = 'Class Management';
        $data['deskripsi'] = 'Kelola kelas les dan jadwal pelatihan';
        $data['modal'] = ''; // Initialize modal variable
        
        try {
            // Get all categories with their class types
            $categories = $this->class_mgmt->get_categories();
            foreach ($categories as &$cat) {
                $cat['class_types'] = $this->class_mgmt->get_class_types($cat['id']);
            }
            $data['categories'] = $categories;
            
            $this->template->load('template/user_adminlte', 'user/class_management/index', $data);
        } catch (Exception $e) {
            // Handle error gracefully
            $data['categories'] = [];
            $data['error'] = 'Error loading class data: ' . $e->getMessage();
            $this->template->load('template/user_adminlte', 'user/class_management/index', $data);
        }
    }

    public function category($id) {
        $data['judul'] = 'Class Category';
        $data['category'] = $this->class_mgmt->get_category($id);
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/index', $data);
    }

    // Categories
    public function categories() {
        $data['judul'] = 'Class Categories';
        $data['deskripsi'] = 'Kelola kategori kelas';
        $data['categories'] = $this->class_mgmt->get_categories();
        $data['modal'] = '';
        
        $this->template->load('template/user_adminlte', 'user/class_management/categories', $data);
    }

    public function add_category() {
        if ($this->input->post()) {
            $data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'status' => 'active'
            ];
            
            if ($this->class_mgmt->insert_category($data)) {
                $this->session->set_flashdata('success', 'Kategori berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan kategori');
            }
            redirect('class_management');
        }
        
        $data['judul'] = 'Add Category';
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/add_category', $data);
    }

    public function edit_category($id) {
        if ($this->input->post()) {
            $data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description')
            ];
            
            if ($this->class_mgmt->update_category($id, $data)) {
                $this->session->set_flashdata('success', 'Kategori berhasil diperbarui');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui kategori');
            }
            redirect('class_management');
        }
        
        $data['judul'] = 'Edit Category';
        $data['category'] = $this->class_mgmt->get_category($id);
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/edit_category', $data);
    }

    public function delete_category($id) {
        if ($this->class_mgmt->delete_category($id)) {
            $this->session->set_flashdata('success', 'Kategori berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus kategori');
        }
        redirect('class_management');
    }

    public function add_class_type() {
        if ($this->input->post()) {
            $data = [
                'category_id' => $this->input->post('category_id'),
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'instructor_name' => $this->input->post('instructor_name'),
                'instructor_phone' => $this->input->post('instructor_phone'),
                'duration_minutes' => $this->input->post('duration_minutes'),
                'max_participants' => $this->input->post('max_participants'),
                // total_meetings is handled in scheduling logic, not stored in class_types table
                'status' => 'active'
            ];
            
            $class_type_id = $this->class_mgmt->insert_class_type($data);
            if ($class_type_id) {
                $duration = (int) $this->input->post('duration_minutes');
                $day_of_week = $this->input->post('day_of_week');
                $start_time = $this->input->post('start_time');
                $room = $this->input->post('room') ?: null;
                $start_date = $this->input->post('start_date');
                $total_meetings = (int) $this->input->post('total_meetings');
                if ($total_meetings < 1) $total_meetings = 1;
                if ($total_meetings > 10) $total_meetings = 10;

                // Normalize time format
                if (strlen((string) $start_time) === 5) {
                    $start_time .= ':00';
                }
                $end_time = date('H:i:s', strtotime($start_time) + $duration * 60);

                // Determine first date >= start_date that matches chosen day_of_week
                $base = $start_date ? new DateTime($start_date) : new DateTime();
                $base->setTime(0, 0, 0);
                $targetDow = $day_of_week ?: null;
                $n = 0;
                if ($targetDow) {
                    // Move forward until day matches
                    for ($i = 0; $i < 7; $i++) {
                        if ($base->format('l') === $targetDow) break;
                        $base->modify('+1 day');
                    }
                    for ($k = 1; $k <= $total_meetings; $k++) {
                        $n++;
                        $this->class_mgmt->insert_class_session([
                            'class_type_id' => $class_type_id,
                            'session_number' => $k,
                            'session_date' => $base->format('Y-m-d'),
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'room' => $room,
                            'status' => 'scheduled',
                        ]);
                        $base->modify('+7 day');
                    }
                }

                $this->session->set_flashdata('success', 'Kelas berhasil ditambahkan' . ($n > 0 ? ' beserta ' . $n . ' jadwal pertemuan.' : '.'));
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan kelas');
            }
            redirect('class_management');
        }
        
        $data['judul'] = 'Add Class';
        $data['categories'] = $this->class_mgmt->get_categories();
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/add_class_type', $data);
    }

    public function edit_class_type($id) {
        if ($this->input->post()) {
            $data = [
                'category_id' => $this->input->post('category_id'),
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'instructor_name' => $this->input->post('instructor_name'),
                'instructor_phone' => $this->input->post('instructor_phone'),
                'duration_minutes' => $this->input->post('duration_minutes'),
                'max_participants' => $this->input->post('max_participants'),
                // total_meetings is not a DB column; keep it for scheduling if needed elsewhere
            ];
            
            if ($this->class_mgmt->update_class_type($id, $data)) {
                $this->session->set_flashdata('success', 'Kelas berhasil diperbarui');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui kelas');
            }
            redirect('class_management');
        }
        
        $data['judul'] = 'Edit Class';
        $data['class_type'] = $this->class_mgmt->get_class_type($id);
        $data['categories'] = $this->class_mgmt->get_categories();
        $data['sessions'] = $this->class_mgmt->get_class_sessions(null, $id);
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/edit_class_type', $data);
    }

    public function delete_class_type($id) {
        if ($this->class_mgmt->delete_class_type($id)) {
            $this->session->set_flashdata('success', 'Kelas berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus kelas');
        }
        redirect('class_management');
    }

    // Schedules
    public function schedules() {
        $data['judul'] = 'Class Schedules';
        $data['deskripsi'] = 'Kelola jadwal rutin kelas';
        $data['class_types'] = $this->class_mgmt->get_class_types();
        $data['schedules'] = $this->class_mgmt->get_class_schedules();
        $data['modal'] = '';
        
        $this->template->load('template/user_adminlte', 'user/class_management/schedules', $data);
    }

    public function add_schedule() {
        if ($this->input->post()) {
            $data = [
                'class_type_id' => $this->input->post('class_type_id'),
                'day_of_week' => $this->input->post('day_of_week'),
                'start_time' => $this->input->post('start_time'),
                'end_time' => $this->input->post('end_time'),
                'room' => $this->input->post('room'),
                'status' => 'active'
            ];
            
            if ($this->class_mgmt->insert_class_schedule($data)) {
                $this->session->set_flashdata('success', 'Jadwal berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan jadwal');
            }
            redirect('class_management/schedules');
        }
        
        $data['judul'] = 'Add Schedule';
        $data['class_types'] = $this->class_mgmt->get_class_types();
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/add_schedule', $data);
    }

    public function edit_schedule($id) {
        if ($this->input->post()) {
            $data = [
                'class_type_id' => $this->input->post('class_type_id'),
                'day_of_week' => $this->input->post('day_of_week'),
                'start_time' => $this->input->post('start_time'),
                'end_time' => $this->input->post('end_time'),
                'room' => $this->input->post('room')
            ];
            
            if ($this->class_mgmt->update_class_schedule($id, $data)) {
                $this->session->set_flashdata('success', 'Jadwal berhasil diperbarui');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui jadwal');
            }
            redirect('class_management/schedules');
        }
        
        $data['judul'] = 'Edit Schedule';
        $data['schedule'] = $this->class_mgmt->get_class_schedule($id);
        $data['class_types'] = $this->class_mgmt->get_class_types();
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/edit_schedule', $data);
    }

    public function delete_schedule($id) {
        if ($this->class_mgmt->delete_schedule($id)) {
            $this->session->set_flashdata('success', 'Jadwal berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus jadwal');
        }
        redirect('class_management/schedules');
    }

    // Sessions (actual scheduled classes)
    public function sessions() {
        $data['judul'] = 'Class Sessions';
        $data['deskripsi'] = 'Kelola sesi kelas yang dijadwalkan';
        $data['sessions'] = $this->class_mgmt->get_class_sessions();
        $data['class_types'] = $this->class_mgmt->get_class_types();
        $data['modal'] = '';
        
        $this->template->load('template/user_adminlte', 'user/class_management/sessions', $data);
    }

    public function add_session() {
        if ($this->input->post()) {
            $class_type_id = (int) $this->input->post('class_type_id');
            $session_number = (int) $this->input->post('session_number');
            if ($session_number < 1) {
                $session_number = $this->class_mgmt->get_next_session_number($class_type_id);
            }
            $start = $this->input->post('start_time');
            if (strlen((string) $start) === 5) {
                $start .= ':00';
            }
            $end = $this->input->post('end_time');
            if (strlen((string) $end) === 5) {
                $end .= ':00';
            }
            $data = [
                'class_type_id' => $class_type_id,
                'session_number' => $session_number,
                'session_date' => $this->input->post('session_date'),
                'start_time' => $start,
                'end_time' => $end,
                'room' => $this->input->post('room') ?: null,
                'status' => 'scheduled'
            ];
            
            if ($this->class_mgmt->insert_class_session($data)) {
                $this->session->set_flashdata('success', 'Sesi kelas berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan sesi kelas');
            }
            redirect('class_management/sessions');
        }
        
        $data['judul'] = 'Add Session';
        $data['class_types'] = $this->class_mgmt->get_class_types();
        $pre_ct = $this->input->get('class_type_id');
        $data['pre_class_type_id'] = $pre_ct ? (int) $pre_ct : null;
        $data['next_session_number'] = $data['pre_class_type_id']
            ? $this->class_mgmt->get_next_session_number($data['pre_class_type_id'])
            : 1;
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/add_session', $data);
    }

    public function edit_session($id) {
        if ($this->input->post()) {
            $st = $this->input->post('start_time');
            if (strlen((string) $st) === 5) {
                $st .= ':00';
            }
            $et = $this->input->post('end_time');
            if (strlen((string) $et) === 5) {
                $et .= ':00';
            }
            $data = [
                'class_type_id' => $this->input->post('class_type_id'),
                'session_number' => (int) $this->input->post('session_number'),
                'session_date' => $this->input->post('session_date'),
                'start_time' => $st,
                'end_time' => $et,
                'room' => $this->input->post('room'),
                'current_participants' => $this->input->post('current_participants'),
                'status' => $this->input->post('status'),
                'notes' => $this->input->post('notes')
            ];
            
            if ($this->class_mgmt->update_class_session($id, $data)) {
                $this->session->set_flashdata('success', 'Sesi kelas berhasil diperbarui');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui sesi kelas');
            }
            redirect('class_management/sessions');
        }
        
        $data['judul'] = 'Edit Session';
        $data['session'] = $this->class_mgmt->get_class_session($id);
        $data['class_types'] = $this->class_mgmt->get_class_types();
        $data['attendance_rows'] = $this->class_mgmt->get_session_attendance_rows($id);
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/edit_session', $data);
    }

    /** Tampilkan QR absensi untuk satu pertemuan (instruktur/proyektor) */
    public function session_qr($session_id) {
        $session_id = (int) $session_id;
        $session = $this->class_mgmt->get_class_session($session_id);
        if (!$session) {
            show_404();
            return;
        }
        $secret = $this->config->item('class_qr_secret');
        $payload = gsc_attendance_build_payload($session_id, $secret);

        $builder = \Endroid\QrCode\Builder\Builder::create()
            // SVG writer does not require GD extension
            ->writer(new \Endroid\QrCode\Writer\SvgWriter())
            ->data($payload)
            ->size(320)
            ->margin(10)
            ->build();

        $data['judul'] = 'QR Absensi Pertemuan';
        $data['session'] = $session;
        $data['qr_base64'] = base64_encode($builder->getString()); // SVG string
        $data['payload_hint'] = $payload;
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/session_qr', $data);
    }

    public function member_qr($id) {
        $data['judul'] = 'Member QR Code';
        $data['member'] = $this->class_mgmt->get_member($id);
        if (!$data['member']) {
            $this->session->set_flashdata('error', 'Member tidak ditemukan');
            redirect('class_management');
        }
        $data['class_type'] = $this->class_mgmt->get_class_type($data['member']['class_type_id']);
        
        $this->load->helper('class_attendance');
        $secret = $this->config->item('class_qr_secret');
        
        // If QR payload doesn't exist, generate it
        if (empty($data['member']['qr_payload'])) {
            $qr_payload = gsc_member_build_payload((int) $id, $secret);
            $this->db->where('id', (int) $id)->update('class_members', [
                'qr_payload' => $qr_payload,
            ]);
            $data['member']['qr_payload'] = $qr_payload;
        }
        
        // Generate QR Base64 for view using SVG (no GD required)
        $builder = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\SvgWriter())
            ->data($data['member']['qr_payload'])
            ->size(320)
            ->margin(10)
            ->build();
        
        $data['qr_base64'] = base64_encode($builder->getString());
        $data['payload_hint'] = $data['member']['qr_payload'];
        $data['modal'] = '';
        
        $this->template->load('template/user_adminlte', 'user/class_management/member_qr', $data);
    }

    public function delete_session($id) {
        if ($this->class_mgmt->delete_class_session((int) $id)) {
            $this->session->set_flashdata('success', 'Sesi dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus sesi');
        }
        redirect('class_management/sessions');
    }

    // Class Members/Students Management
    public function class_members($category_id) {
        $data['judul'] = 'Class Members';
        $data['deskripsi'] = 'Manage students/members for classes';
        $data['category'] = $this->class_mgmt->get_category($category_id);
        $data['class_types'] = $this->class_mgmt->get_class_types_by_category($category_id);
        
        // Get members for each class type
        foreach ($data['class_types'] as &$class_type) {
            $class_type['members'] = $this->class_mgmt->get_class_members($class_type['id']);
        }
        
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/class_members', $data);
    }

    public function add_member($class_type_id) {
        if ($this->input->post()) {
            $user_id = $this->input->post('user_id');
            $generate_qr = $this->input->post('generate_qr') ? true : false;
            
            // Get all app users from Firebase to find the selected one
            $app_users = $this->class_mgmt->get_app_users();
            $selected_user = null;
            foreach ($app_users as $u) {
                if ($u['id_user'] == $user_id) {
                    $selected_user = $u;
                    break;
                }
            }
            
            if ($selected_user) {
                // Check if class is full
                $class_type = $this->class_mgmt->get_class_type($class_type_id);
                $current_members = $this->class_mgmt->get_class_members($class_type_id);
                
                if (count($current_members) >= $class_type['max_participants']) {
                    $this->session->set_flashdata('error', 'Gagal: Kapasitas kelas sudah penuh!');
                } else {
                    $data = [
                        'class_type_id' => $class_type_id,
                        'member_name' => $selected_user['nama'],
                        'member_phone' => $selected_user['nohp'],
                        'email' => strtolower(trim($selected_user['email'])),
                        'firebase_uid' => $selected_user['id_user'],
                        'join_date' => date('Y-m-d'),
                        'status' => 'active'
                    ];
                    
                    $member_id = $this->class_mgmt->insert_member($data);
                    if ($member_id) {
                        if ($generate_qr) {
                            $this->load->helper('class_attendance');
                            $secret = $this->config->item('class_qr_secret');
                            $qr_payload = gsc_member_build_payload((int) $member_id, $secret);
                            $this->db->where('id', (int) $member_id)->update('class_members', [
                                'qr_payload' => $qr_payload,
                            ]);
                        }
                        $this->session->set_flashdata('success', 'Member berhasil ditambahkan' . ($generate_qr ? ' + QR berhasil digenerate.' : '.'));
                    } else {
                        $this->session->set_flashdata('error', 'Gagal menambahkan member');
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'User tidak ditemukan di Firebase');
            }
            
            // Get category_id to redirect back
            $class_type = $this->class_mgmt->get_class_type($class_type_id);
            redirect('class_management/class_members/' . $class_type['category_id']);
        }
        
        $data['judul'] = 'Add Member';
        $data['class_type'] = $this->class_mgmt->get_class_type($class_type_id);
        $data['app_users'] = $this->class_mgmt->get_app_users();
        $data['modal'] = '';
        $this->template->load('template/user_adminlte', 'user/class_management/add_member', $data);
    }

    public function delete_member($id) {
        $member = $this->class_mgmt->get_member($id);
        if ($this->class_mgmt->delete_member($id)) {
            $this->session->set_flashdata('success', 'Member berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus member');
        }
        
        redirect('class_management/class_members/' . $member['category_id']);
    }
}
