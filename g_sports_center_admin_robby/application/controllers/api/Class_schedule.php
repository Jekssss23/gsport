<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_schedule extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Class_management_model', 'class_mgmt');
        $this->load->helper('class_attendance');
        
        // Set CORS headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }

        // Handle JSON POST requests
        $json_data = json_decode(file_get_contents('php://input'), true);
        if(!empty($json_data)) {
            $_POST = array_merge($_POST, $json_data);
        }
    }

    private function response($data, $status = 200) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    // Get available classes
    public function available() {
        // Clear any previous output buffers to avoid PHP errors/notices leaking into JSON
        if (ob_get_length()) ob_clean();
        
        $category_id = $this->input->get('category_id');
        $email = $this->input->get('email'); // Optional email to check membership
        
        // Trim and lowercase email to avoid mismatch
        $email = !empty($email) ? strtolower(trim($email)) : null;
        
        try {
            $classes = $this->class_mgmt->get_class_types($category_id);
            
            $formatted_classes = [];
            if (!empty($classes)) {
                foreach ($classes as $class) {
                    $class['real_time_status'] = 'active';
                    unset($class['price_per_session']);
                    
                    // Check if the user is a member of this class
                    $class['is_member'] = false;
                    if ($email) {
                        $class['is_member'] = $this->class_mgmt->is_class_member($class['id'], $email);
                    }

                    // Get members of this class (ONLY visible to members themselves)
                    // Non-members should not see the member list or attendance progress.
                    $class['members'] = [];
                    if ($email && $class['is_member']) {
                        $class['members'] = $this->class_mgmt->get_class_members($class['id']);
                    }

                    // Attach "my_member" progress for mobile app (so user can see quota progress)
                    if ($email && $class['is_member'] && is_array($class['members'])) {
                        $my = null;
                        foreach ($class['members'] as $m) {
                            if (strtolower(trim($m['email'] ?? '')) === $email) {
                                $my = $m;
                                break;
                            }
                        }
                        if ($my) {
                            $total = isset($class['total_meetings']) ? (int) $class['total_meetings'] : 0;
                            $att = isset($my['meetings_attended']) ? (int) $my['meetings_attended'] : 0;
                            $class['my_member'] = [
                                'member_id' => (int) ($my['id'] ?? 0),
                                'member_name' => $my['member_name'] ?? '',
                                'meetings_attended' => $att,
                                'total_meetings' => $total,
                                'remaining_meetings' => ($total > 0) ? max(0, $total - $att) : null,
                            ];
                        } else {
                            $class['my_member'] = null;
                        }
                    } else {
                        $class['my_member'] = null;
                    }
                    
                    // Calculate current participants and remaining capacity
                    $class['current_participants'] = is_array($class['members']) ? count($class['members']) : 0;
                    $class['remaining_capacity'] = max(0, (int)($class['max_participants'] ?? 0) - $class['current_participants']);
                    
                    $formatted_classes[] = $class;
                }
            }
            
            $this->response([
                'ok' => true,
                'data' => $formatted_classes
            ]);
        } catch (Exception $e) {
            $this->response([
                'ok' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /** GET: today_sessions (for admin scanning UI) */
    public function today_sessions() {
        if (ob_get_length()) ob_clean();
        try {
            $today = date('Y-m-d');
            $sessions = $this->class_mgmt->get_class_sessions($today, null);
            $this->response([
                'ok' => true,
                'data' => $sessions,
            ]);
        } catch (Exception $e) {
            $this->response(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Get class categories
    public function categories() {
        if (ob_get_length()) ob_clean();
        try {
            $categories = $this->class_mgmt->get_categories();
            $this->response([
                'ok' => true,
                'data' => $categories
            ]);
        } catch (Exception $e) {
            $this->response([
                'ok' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get class types
    public function class_types() {
        if (ob_get_length()) ob_clean();
        $category_id = $this->input->get('category_id');
        if (!$category_id) {
            $this->response([
                'ok' => false,
                'message' => 'Category ID is required'
            ], 400);
            return;
        }

        try {
            $class_types = $this->class_mgmt->get_class_types_by_category($category_id);
            $this->response([
                'ok' => true,
                'data' => $class_types
            ]);
        } catch (Exception $e) {
            $this->response([
                'ok' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get my bookings
    public function my_bookings() {
        if (ob_get_length()) ob_clean();
        $firebase_uid = $this->input->get('firebase_uid');
        if (!$firebase_uid) {
            $this->response([
                'ok' => false,
                'message' => 'Firebase UID is required'
            ], 400);
            return;
        }

        try {
            $bookings = $this->class_mgmt->get_user_class_bookings($firebase_uid);
            $this->response([
                'ok' => true,
                'data' => $bookings
            ]);
        } catch (Exception $e) {
            $this->response([
                'ok' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Book a class
    public function book_class() {
        // Clear any previous output buffers
        if (ob_get_length()) ob_clean();

        $data = [
            'class_session_id' => $this->input->post('class_session_id'),
            'firebase_uid' => $this->input->post('firebase_uid'),
            'participant_name' => $this->input->post('participant_name'),
            'participant_phone' => $this->input->post('participant_phone'),
            'booking_date' => date('Y-m-d H:i:s'),
            'status' => 'booked'
        ];

        if (!$data['class_session_id'] || !$data['firebase_uid'] || !$data['participant_name']) {
            $this->response([
                'ok' => false,
                'message' => 'Missing required fields'
            ], 400);
            return;
        }

        try {
            // Since we're using class_types now, we check against class_type_id
            $class_type = $this->class_mgmt->get_class_type($data['class_session_id']);
            if (!$class_type) {
                $this->response([
                    'ok' => false,
                    'message' => 'Class not found'
                ], 404);
                return;
            }

            $current_members = $this->class_mgmt->get_class_members($data['class_session_id']);
            if (count($current_members) >= $class_type['max_participants']) {
                $this->response([
                    'ok' => false,
                    'message' => 'Class is already full'
                ], 400);
                return;
            }

            // In this version, booking might just be joining as a member
            // or we use class_bookings table. Let's stick to the current logic.
            $this->db->insert('class_bookings', $data);
            $booking_id = $this->db->insert_id();

            $this->response([
                'ok' => true,
                'data' => ['booking_id' => $booking_id]
            ]);
        } catch (Exception $e) {
            $this->response([
                'ok' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Cancel booking
    public function cancel_booking() {
        if (ob_get_length()) ob_clean();
        $booking_id = $this->input->post('booking_id');
        $firebase_uid = $this->input->post('firebase_uid');

        if (!$booking_id || !$firebase_uid) {
            $this->response([
                'ok' => false,
                'message' => 'Booking ID and Firebase UID are required'
            ], 400);
            return;
        }

        try {
            // Check booking ownership
            $this->db->where('id', $booking_id);
            $this->db->where('firebase_uid', $firebase_uid);
            $booking = $this->db->get('class_bookings')->row_array();

            if (!$booking) {
                $this->response([
                    'ok' => false,
                    'message' => 'Booking not found or access denied'
                ], 404);
                return;
            }

            if ($booking['status'] == 'cancelled') {
                $this->response([
                    'ok' => false,
                    'message' => 'Booking is already cancelled'
                ], 400);
                return;
            }

            // Cancel booking
            $this->db->where('id', $booking_id);
            $this->db->update('class_bookings', ['status' => 'cancelled']);
            
            $this->response([
                'ok' => true,
                'message' => 'Booking cancelled successfully'
            ]);
        } catch (Exception $e) {
            $this->response([
                'ok' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST: payload (QR string), email, firebase_uid
     * Member must be registered in class_members for this session's class_type; absensi 1x per pertemuan.
     */
    public function attendance_checkin() {
        if (ob_get_length()) {
            ob_clean();
        }
        if ($this->input->method(TRUE) !== 'POST') {
            $this->response(['ok' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        $payload = $this->input->post('payload');
        $email = $this->input->post('email');
        $firebase_uid = $this->input->post('firebase_uid');

        if (empty($payload) || empty($email)) {
            $this->response(['ok' => false, 'message' => 'payload dan email wajib'], 400);
            return;
        }

        $email = strtolower(trim($email));
        $secret = $this->config->item('class_qr_secret');
        $session_id = gsc_attendance_parse_payload($payload, $secret);

        if ($session_id === false) {
            $this->response(['ok' => false, 'message' => 'QR tidak valid atau kedaluwarsa'], 400);
            return;
        }

        $session = $this->class_mgmt->get_class_session($session_id);
        if (!$session || (isset($session['status']) && $session['status'] === 'cancelled')) {
            $this->response(['ok' => false, 'message' => 'Sesi tidak ditemukan'], 404);
            return;
        }

        $class_type_id = (int) $session['class_type_id'];
        if (!$this->class_mgmt->is_class_member($class_type_id, $email)) {
            $this->response(['ok' => false, 'message' => 'Anda belum terdaftar sebagai member kelas ini'], 403);
            return;
        }

        $today = date('Y-m-d');
        if ($session['session_date'] !== $today) {
            $this->response([
                'ok' => false,
                'message' => 'Absensi hanya untuk pertemuan hari ini (' . $session['session_date'] . ')',
            ], 400);
            return;
        }

        if ($this->class_mgmt->has_session_attendance($session_id, $email)) {
            $this->response([
                'ok' => true,
                'already' => true,
                'message' => 'Anda sudah absen untuk pertemuan ini',
                'data' => [
                    'class_session_id' => $session_id,
                    'session_number' => isset($session['session_number']) ? (int) $session['session_number'] : null,
                    'class_name' => $session['class_name'] ?? '',
                ],
            ]);
            return;
        }

        if (!$this->class_mgmt->insert_session_attendance($session_id, $email, $firebase_uid)) {
            $this->response(['ok' => false, 'message' => 'Gagal menyimpan absensi'], 500);
            return;
        }

        $this->response([
            'ok' => true,
            'message' => 'Absensi berhasil',
            'data' => [
                'class_session_id' => $session_id,
                'session_number' => isset($session['session_number']) ? (int) $session['session_number'] : null,
                'class_name' => $session['class_name'] ?? '',
                'session_date' => $session['session_date'],
            ],
        ]);
    }

    /**
     * Admin/Employee scans MEMBER QR to mark attendance for a specific class session.
     *
     * POST JSON:
     * - class_session_id (int)
     * - payload (string) = GSCMEMv1|member_id|hmac32
     */
    public function attendance_checkin_member() {
        if (ob_get_length()) {
            ob_clean();
        }
        if ($this->input->method(TRUE) !== 'POST') {
            $this->response(['ok' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        $class_session_id = (int) $this->input->post('class_session_id');
        $payload = $this->input->post('payload');

        if ($class_session_id < 1 || empty($payload)) {
            $this->response(['ok' => false, 'message' => 'class_session_id dan payload wajib'], 400);
            return;
        }

        // Parse member QR
        $secret = $this->config->item('class_qr_secret');
        $member_id = gsc_member_parse_payload($payload, $secret);
        if ($member_id === false) {
            $this->response(['ok' => false, 'message' => 'QR member tidak valid'], 400);
            return;
        }

        $session = $this->class_mgmt->get_class_session($class_session_id);
        if (!$session || (isset($session['status']) && $session['status'] === 'cancelled')) {
            $this->response(['ok' => false, 'message' => 'Sesi tidak ditemukan'], 404);
            return;
        }

        // Attendance only for today's meeting
        $today = date('Y-m-d');
        if (isset($session['session_date']) && $session['session_date'] !== $today) {
            $this->response([
                'ok' => false,
                'message' => 'Absensi hanya untuk pertemuan hari ini (' . $session['session_date'] . ')',
            ], 400);
            return;
        }

        $member = $this->class_mgmt->get_member_by_id($member_id);
        if (!$member) {
            $this->response(['ok' => false, 'message' => 'Member tidak ditemukan / sudah nonaktif'], 404);
            return;
        }

        // Must belong to the same class type as the session
        if ((int)($member['class_type_id'] ?? 0) !== (int)($session['class_type_id'] ?? 0)) {
            $this->response(['ok' => false, 'message' => 'Member ini bukan untuk kelas pertemuan ini'], 403);
            return;
        }

        // Prevent exceeding total meetings (anti-cheat)
        $class_type = $this->class_mgmt->get_class_type((int) $session['class_type_id']);
        $total_meetings = isset($class_type['total_meetings']) ? (int) $class_type['total_meetings'] : null;
        $attended = isset($member['meetings_attended']) ? (int) $member['meetings_attended'] : 0;
        if ($total_meetings !== null && $total_meetings > 0 && $attended >= $total_meetings) {
            $this->response([
                'ok' => false,
                'message' => 'Jatah pertemuan member ini sudah habis (' . $attended . '/' . $total_meetings . ')',
            ], 400);
            return;
        }

        $email = strtolower(trim($member['email'] ?? ''));
        if ($email === '') {
            $this->response(['ok' => false, 'message' => 'Data member tidak lengkap (email kosong)'], 400);
            return;
        }

        if ($this->class_mgmt->has_session_attendance($class_session_id, $email)) {
            $this->response([
                'ok' => true,
                'already' => true,
                'message' => 'Member sudah absen untuk pertemuan ini',
                'data' => [
                    'class_session_id' => $class_session_id,
                    'session_number' => isset($session['session_number']) ? (int) $session['session_number'] : null,
                    'class_name' => $session['class_name'] ?? '',
                    'member_name' => $member['member_name'] ?? '',
                    'meetings_attended' => isset($member['meetings_attended']) ? (int)$member['meetings_attended'] : null,
                ],
            ]);
            return;
        }

        // Insert attendance row and increment member counter
        $ok = $this->class_mgmt->insert_session_attendance($class_session_id, $email, $member['firebase_uid'] ?? null);
        if (!$ok) {
            $this->response(['ok' => false, 'message' => 'Gagal menyimpan absensi'], 500);
            return;
        }
        $this->class_mgmt->increment_member_meetings_attended($member_id);

        $this->response([
            'ok' => true,
            'message' => 'Absensi berhasil',
            'data' => [
                'class_session_id' => $class_session_id,
                'session_number' => isset($session['session_number']) ? (int) $session['session_number'] : null,
                'class_name' => $session['class_name'] ?? '',
                'session_date' => $session['session_date'] ?? null,
                'member_name' => $member['member_name'] ?? '',
                'meetings_attended' => isset($member['meetings_attended']) ? (int)$member['meetings_attended'] + 1 : null,
            ],
        ]);
    }

    /**
     * Admin/Employee scans MEMBER QR and system auto-picks today's session for that member's class.
     *
     * POST JSON:
     * - payload (string) = GSCMEMv1|member_id|hmac32
     */
    public function attendance_checkin_member_auto() {
        if (ob_get_length()) {
            ob_clean();
        }
        if ($this->input->method(TRUE) !== 'POST') {
            $this->response(['ok' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        $payload = $this->input->post('payload');
        if (empty($payload)) {
            $this->response(['ok' => false, 'message' => 'payload wajib'], 400);
            return;
        }

        $secret = $this->config->item('class_qr_secret');
        $member_id = gsc_member_parse_payload($payload, $secret);
        if ($member_id === false) {
            $this->response(['ok' => false, 'message' => 'QR member tidak valid'], 400);
            return;
        }

        $member = $this->class_mgmt->get_member_by_id((int) $member_id);
        if (!$member) {
            $this->response(['ok' => false, 'message' => 'Member tidak ditemukan / sudah nonaktif'], 404);
            return;
        }

        $class_type_id = (int) ($member['class_type_id'] ?? 0);
        if ($class_type_id < 1) {
            $this->response(['ok' => false, 'message' => 'Data member tidak lengkap (class_type_id kosong)'], 400);
            return;
        }

        $today = date('Y-m-d');
        $session = $this->class_mgmt->get_today_session_for_class_type($class_type_id, $today);
        if (!$session) {
            $this->response([
                'ok' => false,
                'message' => 'Tidak ada sesi pertemuan untuk kelas ini hari ini (' . $today . ')',
            ], 404);
            return;
        }

        // Prevent exceeding total meetings (anti-cheat)
        $class_type = $this->class_mgmt->get_class_type($class_type_id);
        $total_meetings = isset($class_type['total_meetings']) ? (int) $class_type['total_meetings'] : null;
        $attended = isset($member['meetings_attended']) ? (int) $member['meetings_attended'] : 0;
        if ($total_meetings !== null && $total_meetings > 0 && $attended >= $total_meetings) {
            $this->response([
                'ok' => false,
                'message' => 'Jatah pertemuan member ini sudah habis (' . $attended . '/' . $total_meetings . ')',
            ], 400);
            return;
        }

        $email = strtolower(trim($member['email'] ?? ''));
        if ($email === '') {
            $this->response(['ok' => false, 'message' => 'Data member tidak lengkap (email kosong)'], 400);
            return;
        }

        $class_session_id = (int) ($session['id'] ?? 0);
        if ($class_session_id < 1) {
            $this->response(['ok' => false, 'message' => 'Sesi tidak valid'], 500);
            return;
        }

        if ($this->class_mgmt->has_session_attendance($class_session_id, $email)) {
            $this->response([
                'ok' => true,
                'already' => true,
                'message' => 'Member sudah absen untuk pertemuan ini',
                'data' => [
                    'class_session_id' => $class_session_id,
                    'session_number' => isset($session['session_number']) ? (int) $session['session_number'] : null,
                    'class_name' => $session['class_name'] ?? '',
                    'session_date' => $session['session_date'] ?? null,
                    'member_name' => $member['member_name'] ?? '',
                    'meetings_attended' => $attended,
                ],
            ]);
            return;
        }

        $ok = $this->class_mgmt->insert_session_attendance($class_session_id, $email, $member['firebase_uid'] ?? null);
        if (!$ok) {
            $this->response(['ok' => false, 'message' => 'Gagal menyimpan absensi'], 500);
            return;
        }
        $this->class_mgmt->increment_member_meetings_attended((int) $member_id);

        $this->response([
            'ok' => true,
            'message' => 'Absensi berhasil',
            'data' => [
                'class_session_id' => $class_session_id,
                'session_number' => isset($session['session_number']) ? (int) $session['session_number'] : null,
                'class_name' => $session['class_name'] ?? '',
                'session_date' => $session['session_date'] ?? null,
                'member_name' => $member['member_name'] ?? '',
                'meetings_attended' => $attended + 1,
            ],
        ]);
    }
}
