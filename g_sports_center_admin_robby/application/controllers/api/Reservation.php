<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reservation extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Reservation_model', 'reservation');
        
        // Set CORS headers for mobile app
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }
    }

    private function respond_json($data, $status_code = 200)
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }

    private function get_json_input()
    {
        $raw = file_get_contents('php://input');
        if (!$raw) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function facilities()
    {
        try {
            $data = $this->reservation->get_facilities_with_courts();
            $this->respond_json(['ok' => true, 'data' => $data]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function courts()
    {
        try {
            $facility_id = (int) $this->input->get('facility_id');
            if ($facility_id <= 0) {
                return $this->respond_json(['ok' => false, 'message' => 'facility_id is required'], 400);
            }
            $data = $this->reservation->get_courts($facility_id);
            $this->respond_json(['ok' => true, 'data' => $data]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function availability()
    {
        try {
            $court_id = (int) $this->input->get('court_id');
            $date = $this->input->get('date');
            if ($court_id <= 0 || !$date) {
                return $this->respond_json(['ok' => false, 'message' => 'court_id and date are required'], 400);
            }

            $available = $this->reservation->get_available_hours($court_id, $date);
            $this->respond_json(['ok' => true, 'data' => ['available_hours' => $available]]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        try {
            $input = $this->get_json_input();

            $required = ['firebase_uid', 'user_name', 'facility_id', 'court_id', 'booking_date', 'time_slots'];
            foreach ($required as $key) {
                if (!isset($input[$key]) || $input[$key] === '') {
                    return $this->respond_json(['ok' => false, 'message' => $key.' is required'], 400);
                }
            }

            if (!is_array($input['time_slots']) || count($input['time_slots']) === 0) {
                return $this->respond_json(['ok' => false, 'message' => 'time_slots must be a non-empty array'], 400);
            }

            $payload = [
                'firebase_uid' => (string) $input['firebase_uid'],
                'user_name' => (string) $input['user_name'],
                'user_phone' => isset($input['user_phone']) ? (string) $input['user_phone'] : '',
                'facility_id' => (int) $input['facility_id'],
                'court_id' => (int) $input['court_id'],
                'booking_date' => (string) $input['booking_date'],
                'time_slots' => array_values(array_map('intval', $input['time_slots'])),
                'payment_proof_url' => isset($input['payment_proof_url']) ? (string) $input['payment_proof_url'] : null,
                'total_amount' => isset($input['total_amount']) ? (int) $input['total_amount'] : 0,
                'dp_amount' => isset($input['dp_amount']) ? (int) $input['dp_amount'] : 0,
                'remaining_amount' => isset($input['remaining_amount']) ? (int) $input['remaining_amount'] : 0,
            ];

            $result = $this->reservation->create_reservation($payload);
            if (!$result['ok']) {
                return $this->respond_json([
                    'ok' => false,
                    'message' => 'Failed to create reservation',
                    'error' => $result['db_error'],
                ], 500);
            }

            $this->respond_json([
                'ok' => true,
                'data' => [
                    'reservation_id' => $result['reservation_id'],
                    'reservation_code' => $result['reservation_code'],
                    'status' => 'pending',
                ]
            ], 201);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function my()
    {
        try {
            $firebase_uid = $this->input->get('firebase_uid');
            if (!$firebase_uid) {
                return $this->respond_json(['ok' => false, 'message' => 'firebase_uid is required'], 400);
            }

            // Auto-complete expired bookings first
            $this->reservation->auto_complete_expired_reservations();
            
            $data = $this->reservation->get_user_reservations($firebase_uid);
            $this->respond_json(['ok' => true, 'data' => $data]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Save rating + review (MySQL flag + Firestore reviews via service account)
    public function rate()
    {
        try {
            $this->load->library('firebase_admin');
            
            // Ensure column exists safely
            try {
                $this->reservation->ensure_has_rated_column();
            } catch (Exception $db_e) {
                log_message('error', 'DB Alter error in rate(): ' . $db_e->getMessage());
            }

            $input = $this->get_json_input();
            
            // Handle both JSON and POST
            $reservation_id = 0;
            if (isset($input['reservation_id'])) {
                $reservation_id = (int) $input['reservation_id'];
            } elseif ($this->input->post('reservation_id')) {
                $reservation_id = (int) $this->input->post('reservation_id');
            }

            if ($reservation_id <= 0) {
                return $this->respond_json(['ok' => false, 'message' => 'ID Reservasi tidak valid (reservation_id required)'], 400);
            }

            $row = $this->db->get_where('reservations', ['id' => $reservation_id])->row_array();
            if (!$row) {
                return $this->respond_json(['ok' => false, 'message' => 'Data reservasi tidak ditemukan di database'], 404);
            }

            $rating = isset($input['rating']) ? (int) $input['rating'] : 0;
            $staff_rating = isset($input['staff_rating']) ? (int) $input['staff_rating'] : 0;
            $review_text = isset($input['review']) ? trim((string) $input['review']) : '';

            if ($rating < 1 || $rating > 5) {
                return $this->respond_json(['ok' => false, 'message' => 'Rating harus diisi (1-5)'], 400);
            }

            // Get additional info for payload
            $facility = $this->db->get_where('reservation_facilities', ['id' => (int) $row['facility_id']])->row_array();
            $court = $this->db->get_where('reservation_courts', ['id' => (int) $row['court_id']])->row_array();

            $review_id = 'REV' . date('YmdHis') . rand(100, 999);
            $review_payload = [
                'id' => $review_id,
                'bookingId' => (string) $reservation_id,
                'userId' => (string) ($input['user_id'] ?? $row['firebase_uid'] ?? ''),
                'userName' => (string) ($input['user_name'] ?? $row['user_name'] ?? 'User'),
                'facilityId' => (string) ($row['facility_id'] ?? ''),
                'courtId' => (string) ($row['court_id'] ?? ''),
                'facilityName' => (string) ($input['facility_name'] ?? ($facility['name'] ?? 'Fasilitas')),
                'courtName' => (string) ($court['name'] ?? ''),
                'rating' => (int) $rating,
                'staffRating' => (int) $staff_rating,
                'staffOnDutyId' => (string) ($input['staff_on_duty_id'] ?? ''),
                'staffOnDutyName' => (string) ($input['staff_on_duty_name'] ?? 'Petugas'),
                'review' => $review_text,
                'bookingDate' => (string) ($row['booking_date'] ?? date('Y-m-d')),
                'ratingType' => 'emote_with_staff_star',
                'createdAt' => date('Y-m-d H:i:s'),
                'source' => 'mobile_api'
            ];

            // 1. Save to Firestore
            $fs = $this->firebase_admin->create_firestore_document('reviews', $review_id, $review_payload);
            
            if (!Firebase_admin::is_write_success($fs)) {
                $err = is_string($fs) ? $fs : $this->firebase_admin->get_last_error();
                log_message('error', 'Firestore Write Error: ' . $err);
                // We still try to update MySQL even if Firestore fails, but we return error to user
                return $this->respond_json(['ok' => false, 'message' => 'Gagal sinkronisasi rating ke cloud: ' . $err], 500);
            }

            // 2. Update MySQL status
            $this->db->where('id', $reservation_id);
            $update_ok = $this->db->update('reservations', [
                'has_rated' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if (!$update_ok) {
                log_message('error', 'Failed to update has_rated for reservation ' . $reservation_id);
            }

            $this->respond_json([
                'ok' => true, 
                'message' => 'Rating berhasil disimpan', 
                'review_id' => $review_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Exception in Reservation::rate: ' . $e->getMessage());
            $this->respond_json(['ok' => false, 'message' => 'Internal Error: ' . $e->getMessage()], 500);
        }
    }

    public function cancel()
    {
        try {
            $input = $this->get_json_input();
            
            $required = ['reservation_id', 'reason'];
            foreach ($required as $key) {
                if (!isset($input[$key]) || $input[$key] === '') {
                    return $this->respond_json(['ok' => false, 'message' => $key.' is required'], 400);
                }
            }

            $reservation_id = (int) $input['reservation_id'];
            $reason = (string) $input['reason'];

            // Check if reservation exists and belongs to user
            $reservation = $this->db->get_where('reservations', ['id' => $reservation_id])->row_array();
            if (!$reservation) {
                return $this->respond_json(['ok' => false, 'message' => 'Reservation not found'], 404);
            }

            // Only allow cancellation requests for pending reservations.
            if ($reservation['status'] !== 'pending') {
                return $this->respond_json(['ok' => false, 'message' => 'Only pending reservations can request cancellation'], 400);
            }

            // Additional check: Prevent cancellation of completed bookings
            if (in_array($reservation['status'], ['completed', 'selesai', 'cancelled', 'cancellation_requested'])) {
                return $this->respond_json(['ok' => false, 'message' => 'This reservation cannot be cancelled'], 400);
            }

            // Update reservation status
            $update_data = [
                'status' => 'cancellation_requested',
                'cancellation_reason' => $reason,
                'cancellation_requested_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $reservation_id);
            $result = $this->db->update('reservations', $update_data);

            if (!$result) {
                return $this->respond_json(['ok' => false, 'message' => 'Failed to update reservation'], 500);
            }

            $this->respond_json([
                'ok' => true,
                'data' => [
                    'reservation_id' => $reservation_id,
                    'status' => 'cancellation_requested',
                    'message' => 'Cancellation request submitted successfully'
                ]
            ]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function approve_cancel()
    {
        try {
            $input = $this->get_json_input();
            $reservation_id = isset($input['reservation_id']) ? (int) $input['reservation_id'] : 0;

            if (!$reservation_id) {
                return $this->respond_json(['ok' => false, 'message' => 'reservation_id is required'], 400);
            }

            $reservation = $this->db->get_where('reservations', ['id' => $reservation_id])->row_array();
            if (!$reservation) {
                return $this->respond_json(['ok' => false, 'message' => 'Reservation not found'], 404);
            }

            if ($reservation['status'] !== 'cancellation_requested') {
                return $this->respond_json(['ok' => false, 'message' => 'This reservation is not pending cancellation approval'], 400);
            }

            $update_data = [
                'status' => 'cancelled',
                'cancellation_approved_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $reservation_id);
            $result = $this->db->update('reservations', $update_data);

            if (!$result) {
                return $this->respond_json(['ok' => false, 'message' => 'Failed to approve cancellation'], 500);
            }

            $this->respond_json(['ok' => true, 'data' => ['reservation_id' => $reservation_id, 'status' => 'cancelled', 'message' => 'Cancellation approved successfully']]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reject_cancel()
    {
        try {
            $input = $this->get_json_input();
            $reservation_id = isset($input['reservation_id']) ? (int) $input['reservation_id'] : 0;

            if (!$reservation_id) {
                return $this->respond_json(['ok' => false, 'message' => 'reservation_id is required'], 400);
            }

            $reservation = $this->db->get_where('reservations', ['id' => $reservation_id])->row_array();
            if (!$reservation) {
                return $this->respond_json(['ok' => false, 'message' => 'Reservation not found'], 404);
            }

            if ($reservation['status'] !== 'cancellation_requested') {
                return $this->respond_json(['ok' => false, 'message' => 'This reservation is not pending cancellation approval'], 400);
            }

            $update_data = [
                'status' => 'pending',
                'cancellation_requested_at' => null,
                'cancellation_reason' => null,
                'cancellation_requested_by' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $reservation_id);
            $result = $this->db->update('reservations', $update_data);

            if (!$result) {
                return $this->respond_json(['ok' => false, 'message' => 'Failed to reject cancellation request'], 500);
            }

            $this->respond_json(['ok' => true, 'data' => ['reservation_id' => $reservation_id, 'status' => 'pending', 'message' => 'Cancellation request rejected']]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function staff_schedules()
    {
        try {
            $this->load->model('Employee_model', 'employee_m');
            $this->employee_m->ensure_schema();
            $users = $this->employee_m->get_all_active();
            
            $staff = [];
            foreach ($users as $u) {
                $staff[] = [
                    'id' => $u['id'],
                    'name' => $u['nama'],
                    'divisi' => $u['jabatan'],
                    'imageUrl' => $u['foto'],
                    'jadwal_operasional' => $u['jadwal_operasional']
                ];
            }

            $this->respond_json(['ok' => true, 'data' => $staff]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Get single booking details
    public function get_booking()
    {
        try {
            $booking_id = $this->input->get('id');
            if (!$booking_id) {
                return $this->respond_json(['ok' => false, 'message' => 'booking_id is required'], 400);
            }

            $booking = $this->db->get_where('reservations', ['id' => $booking_id])->row_array();
            if (!$booking) {
                return $this->respond_json(['ok' => false, 'message' => 'Booking not found'], 404);
            }

            $slots = $this->db->query(
                'SELECT hour FROM reservation_slots WHERE reservation_id = ? ORDER BY hour ASC',
                [$booking['id']]
            )->result_array();
            $booking['time_slots'] = array_map(function ($s) {
                return (int) $s['hour'];
            }, $slots);

            $facility = $this->db->get_where('reservation_facilities', ['id' => (int) $booking['facility_id']])->row_array();
            $court = $this->db->get_where('reservation_courts', ['id' => (int) $booking['court_id']])->row_array();
            $booking['facility_name'] = $facility['name'] ?? '';
            $booking['court_name'] = $court['name'] ?? '';

            $this->respond_json(['ok' => true, 'data' => $booking]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Auto-complete expired bookings (API version)
    public function auto_complete_expired()
    {
        try {
            $result = $this->reservation->auto_complete_expired_reservations();
            $this->respond_json([
                'ok' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->respond_json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
