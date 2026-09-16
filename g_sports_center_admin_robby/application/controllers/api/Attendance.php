<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_model', 'attendance');
        
        // Handle JSON POST requests for mobile app (React Native fetch sends JSON)
        $json_data = json_decode(file_get_contents('php://input'), true);
        if(!empty($json_data)) {
            $_POST = array_merge($_POST, $json_data);
        }

        // Set CORS headers for mobile app
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }
    }

    // Get attendance settings
    public function settings()
    {
        $settings = $this->attendance->get_settings();
        
        if ($settings) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data' => $settings
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Attendance settings not found'
                ]));
        }
    }

    // Check if user is within radius
    public function check_radius()
    {
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        
        if (!$latitude || !$longitude) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Latitude and longitude required'
                ]));
            return;
        }
        
        $result = $this->attendance->check_radius($latitude, $longitude);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'data' => $result
            ]));
    }

    // Save attendance (from mobile app)
    public function save()
    {
        // Get input data
        $employee_uid = $this->input->post('employee_uid');
        $employee_id = $this->input->post('employee_id');
        $employee_name = $this->input->post('employee_name');
        $employee_email = $this->input->post('employee_email');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $is_mocked = $this->input->post('is_mocked');
        $selfie_url = $this->input->post('selfie_url');
        
        // Handle firebase UID as employee_id string error (Firebase UID is string, MySQL expects INT)
        if ($employee_uid && !is_numeric($employee_id)) {
            $this->load->model('Employee_model', 'employee_m');
            $this->employee_m->ensure_schema();
            $user_query = $this->employee_m->get_by_firebase_uid($employee_uid);
            if ($user_query) {
                $employee_id = $user_query['id'];
                if (!$employee_name) $employee_name = $user_query['nama'];
            } else {
                $employee_id = 0;
            }
        }
        
        if (!$employee_uid || !$employee_name || !$latitude || !$longitude) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Missing required fields'
                ]));
            return;
        }
        
        // Check radius
        $radius_check = $this->attendance->check_radius($latitude, $longitude);
        
        if (!$radius_check['within_radius']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Anda berada di luar radius absensi. Jarak: ' . round($radius_check['distance']) . 'm (Max: ' . $radius_check['radius'] . 'm)',
                    'data' => $radius_check
                ]));
            return;
        }
        
        // Check if already attended today
        $existing_attendance = $this->attendance->check_today_attendance($employee_id);
        
        if ($existing_attendance && $existing_attendance['check_in']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check-in hari ini',
                    'data' => $existing_attendance
                ]));
            return;
        }
        
        // Prepare attendance data
        $now = new DateTime();
        $date_str = $now->format('Y-m-d');
        $time_str = $now->format('H:i:s');
        
        // Status hadir/terlambat dari jadwal shift karyawan (server-side, abaikan client)
        $status_meta = $this->attendance->determine_status($time_str, $employee_id, $date_str, $employee_uid);
        $status = $status_meta['status'];

        // Force fake gps if mock detected (client or server-supplied)
        $is_mocked_bool = ($is_mocked === true || $is_mocked === 'true' || $is_mocked === '1' || $is_mocked === 1);
        if ($is_mocked_bool) {
            $status = 'fake gps';
        }
        
        $attendance_data = [
            'employee_id' => $employee_id,
            'employee_uid' => $employee_uid,
            'employee_name' => $employee_name,
            'employee_email' => $employee_email,
            'date' => $date_str,
            'check_in' => $time_str,
            'status' => $status,
            'schedule_shift' => $status_meta['schedule_shift'],
            'schedule_start' => $status_meta['schedule_start'],
            'schedule_end' => $status_meta['schedule_end'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance_meters' => $radius_check['distance'],
            'radius_meters' => $radius_check['radius'],
            'location_name' => $radius_check['settings']['location_name'],
            'photoBase64' => $this->input->post('photoBase64'),
            'selfie_url' => $selfie_url,
            'is_mocked' => $is_mocked_bool ? 1 : 0
        ];
        
        // Save attendance
        $result = $this->attendance->save_attendance($attendance_data);
        
        if ($result['success']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Absen masuk berhasil dicatat',
                    'data' => [
                        'attendance_id' => $result['attendance_id'],
                        'date' => $date_str,
                        'check_in' => $time_str,
                        'status' => $status,
                        'schedule_shift' => $status_meta['schedule_shift'],
                        'schedule_start' => $status_meta['schedule_start'] ? substr($status_meta['schedule_start'], 0, 5) : null,
                        'schedule_end' => $status_meta['schedule_end'] ? substr($status_meta['schedule_end'], 0, 5) : null,
                        'shift_label' => $status_meta['shift_label'] ?? null,
                        'status_reason' => $status_meta['reason'],
                        'location' => [
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'distance' => $radius_check['distance'],
                            'within_radius' => true
                        ]
                    ]
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Gagal menyimpan absensi'
                ]));
        }
    }

    // Check out
    public function checkout()
    {
        $employee_uid = $this->input->post('employee_uid');
        $employee_id = $this->input->post('employee_id');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        
        // Handle firebase UID as employee_id string error
        if ($employee_uid && !is_numeric($employee_id)) {
            $this->load->model('Employee_model', 'employee_m');
            $this->employee_m->ensure_schema();
            $user_query = $this->employee_m->get_by_firebase_uid($employee_uid);
            if ($user_query) $employee_id = $user_query['id'];
            else $employee_id = 0;
        }

        if (!$employee_uid || !$employee_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Employee ID and UID required'
                ]));
            return;
        }
        
        // Check if already checked in today
        $existing_attendance = $this->attendance->check_today_attendance($employee_id);
        
        if (!$existing_attendance || !$existing_attendance['check_in']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Anda belum melakukan check-in hari ini'
                ]));
            return;
        }
        
        if ($existing_attendance['check_out']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini'
                ]));
            return;
        }

        $now = new DateTime();
        $date_str = $now->format('Y-m-d');
        $time_str = $now->format('H:i:s');

        $checkout_gate = $this->attendance->can_checkout($employee_id, $employee_uid, $date_str, $time_str);
        if (!$checkout_gate['allowed']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $checkout_gate['reason'],
                    'data' => [
                        'schedule_end' => $checkout_gate['schedule_end'] ?? null,
                        'available_at' => $checkout_gate['available_at'] ?? null,
                        'shift_label' => $checkout_gate['shift_label'] ?? null,
                    ]
                ]));
            return;
        }
        
        // Check radius (optional for checkout)
        $radius_check = null;
        if ($latitude && $longitude) {
            $radius_check = $this->attendance->check_radius($latitude, $longitude);
            
            if (!$radius_check['within_radius']) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Check-out harus dilakukan dalam radius absensi',
                        'data' => $radius_check
                    ]));
                return;
            }
        }
        
        $success = $this->attendance->update_checkout(
            $employee_id, 
            $time_str, 
            $latitude, 
            $longitude
        );
        
        if ($success) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Absen pulang berhasil dicatat',
                    'data' => [
                        'check_out' => $time_str,
                        'check_in' => $existing_attendance['check_in'],
                        'schedule_end' => $checkout_gate['schedule_end'] ?? null,
                        'shift_label' => $checkout_gate['shift_label'] ?? null,
                        'location' => $radius_check ? [
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'distance' => $radius_check['distance'],
                            'within_radius' => true
                        ] : null
                    ]
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Gagal melakukan check-out'
                ]));
        }
    }

    // Jadwal shift hari ini (untuk preview di mobile)
    public function schedule_today()
    {
        $employee_uid = $this->input->get('employee_uid') ?: $this->input->post('employee_uid');
        $employee_id = $this->input->get('employee_id') ?: $this->input->post('employee_id');

        if ($employee_uid && !is_numeric($employee_id)) {
            $employee_id = $this->attendance->resolve_employee_id($employee_id, $employee_uid);
        }

        $status = $this->attendance->get_today_attendance_status($employee_id, $employee_uid);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'data' => $status,
            ]));
    }

    // Sinkron alpha otomatis (opsional cron / admin tool)
    public function sync_alpha()
    {
        $date = $this->input->get('date') ?: $this->input->post('date') ?: date('Y-m-d', strtotime('-1 day'));
        $force = ($this->input->get('force') === '1' || $this->input->post('force') === '1');

        $result = $this->attendance->sync_alpha_for_date($date, ['force' => $force]);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Alpha otomatis diproses',
                'data' => $result,
            ]));
    }

    // Get attendance history for employee
    public function history()
    {
        $employee_uid = $this->input->get('employee_uid');
        $employee_id = $this->input->get('employee_id');
        $limit = $this->input->get('limit') ?: 30;
        $offset = $this->input->get('offset') ?: 0;
        
        if (!$employee_uid && !$employee_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Employee ID or UID required'
                ]));
            return;
        }
        
        $attendances = $this->attendance->get_attendances(
            null, null, null, null, $limit, $offset
        );
        
        // Filter for specific employee
        if ($employee_id) {
            $attendances = array_filter($attendances, function($att) use ($employee_id) {
                return $att['employee_id'] == $employee_id;
            });
        } elseif ($employee_uid) {
            $attendances = array_filter($attendances, function($att) use ($employee_uid) {
                return $att['employee_uid'] == $employee_uid;
            });
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'data' => array_values($attendances)
            ]));
    }
}
