<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reservation_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->ensure_tables();
    }

    public function ensure_tables()
    {
        // 1. reservations
        if (!$this->db->table_exists('reservations')) {
            $this->db->query("CREATE TABLE `reservations` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `reservation_code` varchar(30) NOT NULL,
              `firebase_uid` varchar(128) NOT NULL,
              `user_name` varchar(150) NOT NULL,
              `user_phone` varchar(30) NOT NULL DEFAULT '',
              `facility_id` int(11) NOT NULL,
              `court_id` int(11) NOT NULL,
              `booking_date` date NOT NULL,
              `status` varchar(30) NOT NULL DEFAULT 'pending',
              `has_rated` tinyint(1) NOT NULL DEFAULT 0,
              `payment_proof_url` text DEFAULT NULL,
              `total_amount` int(11) NOT NULL DEFAULT 0,
              `dp_amount` int(11) NOT NULL DEFAULT 0,
              `remaining_amount` int(11) NOT NULL DEFAULT 0,
              `firebase_reservation_id` varchar(255) DEFAULT NULL,
              `firebase_synced` tinyint(1) DEFAULT 0,
              `firebase_synced_at` datetime DEFAULT NULL,
              `cancellation_requested_at` datetime DEFAULT NULL,
              `cancellation_requested_by` varchar(255) DEFAULT NULL,
              `cancellation_reason` text DEFAULT NULL,
              `cancellation_approved_at` datetime DEFAULT NULL,
              `cancellation_approved_by` varchar(255) DEFAULT NULL,
              `approved_by` int(11) DEFAULT NULL,
              `approved_at` datetime DEFAULT NULL,
              `rejected_by` int(11) DEFAULT NULL,
              `rejected_at` datetime DEFAULT NULL,
              `rejected_reason` text DEFAULT NULL,
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              KEY `idx_reservations_status` (`status`),
              KEY `idx_reservations_code` (`reservation_code`),
              KEY `idx_reservations_firebase` (`firebase_uid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }

        // 2. reservation_slots
        if (!$this->db->table_exists('reservation_slots')) {
            $this->db->query("CREATE TABLE `reservation_slots` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `reservation_id` bigint(20) NOT NULL,
              `court_id` int(11) NOT NULL,
              `booking_date` date NOT NULL,
              `hour` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_slots_reservation` (`reservation_id`),
              KEY `idx_slots_court_date` (`court_id`,`booking_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }

        // 3. reservation_facilities
        if (!$this->db->table_exists('reservation_facilities')) {
            $this->db->query("CREATE TABLE `reservation_facilities` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `price_per_hour` int(11) NOT NULL DEFAULT 0,
              `dp_percentage` int(11) NOT NULL DEFAULT 0,
              `is_active` tinyint(1) NOT NULL DEFAULT 1,
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

            // Insert defaults
            $this->db->insert('reservation_facilities', ['id' => 1, 'name' => 'Futsal', 'price_per_hour' => 150000, 'dp_percentage' => 30]);
            $this->db->insert('reservation_facilities', ['id' => 2, 'name' => 'Badminton', 'price_per_hour' => 75000, 'dp_percentage' => 30]);
            $this->db->insert('reservation_facilities', ['id' => 3, 'name' => 'Pickleball', 'price_per_hour' => 100000, 'dp_percentage' => 30]);
        }

        // 4. reservation_courts
        if (!$this->db->table_exists('reservation_courts')) {
            $this->db->query("CREATE TABLE `reservation_courts` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `facility_id` int(11) NOT NULL,
              `name` varchar(100) NOT NULL,
              `is_active` tinyint(1) NOT NULL DEFAULT 1,
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

            // Insert defaults
            $this->db->insert('reservation_courts', ['id' => 1, 'facility_id' => 1, 'name' => 'Lapangan 1']);
            $this->db->insert('reservation_courts', ['id' => 2, 'facility_id' => 1, 'name' => 'Lapangan 2']);
            $this->db->insert('reservation_courts', ['id' => 3, 'facility_id' => 2, 'name' => 'Lapangan 1']);
            $this->db->insert('reservation_courts', ['id' => 4, 'facility_id' => 3, 'name' => 'Lapangan 1']);
            $this->db->insert('reservation_courts', ['id' => 5, 'facility_id' => 3, 'name' => 'Lapangan 2']);
        }

        // 5. reservations_archive
        if (!$this->db->table_exists('reservations_archive')) {
            $this->db->query("CREATE TABLE `reservations_archive` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `original_reservation_id` int(11) DEFAULT NULL,
              `reservation_code` varchar(50) DEFAULT NULL,
              `user_name` varchar(255) DEFAULT NULL,
              `user_phone` varchar(20) DEFAULT NULL,
              `firebase_uid` varchar(255) DEFAULT NULL,
              `facility_id` int(11) DEFAULT NULL,
              `court_id` int(11) DEFAULT NULL,
              `booking_date` date DEFAULT NULL,
              `total_amount` int(11) DEFAULT 0,
              `dp_amount` int(11) DEFAULT 0,
              `remaining_amount` int(11) DEFAULT 0,
              `payment_proof_url` text DEFAULT NULL,
              `status` varchar(50) DEFAULT NULL,
              `has_rated` tinyint(1) NOT NULL DEFAULT 0,
              `firebase_reservation_id` varchar(255) DEFAULT NULL,
              `firebase_synced` tinyint(1) DEFAULT NULL,
              `firebase_synced_at` datetime DEFAULT NULL,
              `cancellation_requested_at` datetime DEFAULT NULL,
              `cancellation_requested_by` varchar(255) DEFAULT NULL,
              `cancellation_reason` text DEFAULT NULL,
              `cancellation_approved_at` datetime DEFAULT NULL,
              `cancellation_approved_by` varchar(255) DEFAULT NULL,
              `approved_by` int(11) DEFAULT NULL,
              `approved_at` datetime DEFAULT NULL,
              `rejected_by` int(11) DEFAULT NULL,
              `rejected_at` datetime DEFAULT NULL,
              `rejected_reason` text DEFAULT NULL,
              `archived_by` int(11) DEFAULT NULL,
              `archived_at` datetime DEFAULT NULL,
              `data_source` varchar(50) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        } else {
            // Ensure has_rated exists in archive too
            if (!$this->db->field_exists('has_rated', 'reservations_archive')) {
                $this->db->query('ALTER TABLE `reservations_archive` ADD COLUMN `has_rated` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`');
            }
        }

        // 6. reservation_slots_archive
        if (!$this->db->table_exists('reservation_slots_archive')) {
            $this->db->query("CREATE TABLE `reservation_slots_archive` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `reservation_id` int(11) DEFAULT NULL,
              `court_id` int(11) DEFAULT NULL,
              `booking_date` date DEFAULT NULL,
              `hour` int(11) DEFAULT NULL,
              `created_at` datetime DEFAULT current_timestamp(),
              `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }
    }

    public function ensure_has_rated_column()
    {
        if (!$this->db->field_exists('has_rated', 'reservations')) {
            $this->db->query('ALTER TABLE `reservations` ADD COLUMN `has_rated` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`');
        }
    }

    /**
     * Send in-app notification doc + Expo/FCM push to a Firebase user.
     */
    public function notify_mobile_user($firebase_uid, $title, $body, $data = [])
    {
        if (empty($firebase_uid)) {
            return false;
        }

        $CI =& get_instance();
        $CI->load->library('firebase_admin');

        $type = isset($data['type']) ? (string) $data['type'] : 'general';
        $suffix = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $firebase_uid);
        $notifId = strtoupper(substr($type, 0, 3)) . 'N' . date('YmdHis') . rand(100, 999) . '_' . substr($suffix, 0, 40);

        $payload = array_merge([
            'id' => $notifId,
            'user_id' => (string) $firebase_uid,
            'type' => $type,
            'title' => $title,
            'message' => $body,
            'created_at' => date('Y-m-d H:i:s'),
        ], $data);

        $CI->firebase_admin->create_firestore_document('notifications', $notifId, $payload);

        $user_data = $CI->firebase_admin->get_firestore_document('users', $firebase_uid);
        $pushToken = '';
        if (is_array($user_data)) {
            $pushToken = $user_data['pushToken'] ?? $user_data['expoPushToken'] ?? '';
        }

        if ($pushToken !== '') {
            $CI->firebase_admin->send_fcm_notification($pushToken, $title, $body, $data);
        }

        return true;
    }

    public function get_facilities()
    {
        return $this->db->get_where('reservation_facilities', ['is_active' => 1])->result_array();
    }

    public function get_facilities_with_courts()
    {
        $facilities = $this->get_facilities();
        foreach ($facilities as &$f) {
            $f['courts'] = $this->get_courts((int) $f['id']);
        }
        return $facilities;
    }

    public function get_courts($facility_id)
    {
        return $this->db->get_where('reservation_courts', ['facility_id' => $facility_id, 'is_active' => 1])->result_array();
    }

    public function get_booked_hours($court_id, $booking_date)
    {
        $q = $this->db->query(
            "SELECT rs.hour 
             FROM reservation_slots rs
             JOIN reservations r ON rs.reservation_id = r.id
             WHERE rs.court_id = ? AND rs.booking_date = ? AND r.status NOT IN ('rejected', 'cancelled')",
            [$court_id, $booking_date]
        )->result_array();

        return array_map(function ($r) { return (int) $r['hour']; }, $q);
    }

    public function get_available_hours($court_id, $booking_date)
    {
        $all = range(7, 23);
        $booked = $this->get_booked_hours($court_id, $booking_date);
        return array_values(array_diff($all, $booked));
    }

    public function create_reservation($payload)
    {
        $this->db->trans_start();

        $reservation_code = 'RSV' . date('YmdHis') . random_int(100, 999);

        $header = [
            'reservation_code' => $reservation_code,
            'firebase_uid' => $payload['firebase_uid'],
            'user_name' => $payload['user_name'],
            'user_phone' => $payload['user_phone'],
            'facility_id' => (int) $payload['facility_id'],
            'court_id' => (int) $payload['court_id'],
            'booking_date' => $payload['booking_date'],
            'status' => 'pending',
            'payment_proof_url' => $payload['payment_proof_url'],
            'total_amount' => (int) $payload['total_amount'],
            'dp_amount' => (int) $payload['dp_amount'],
            'remaining_amount' => (int) $payload['remaining_amount'],
        ];

        $this->db->insert('reservations', $header); 
        $reservation_id = $this->db->insert_id();

        foreach ($payload['time_slots'] as $hour) {
            $this->db->insert('reservation_slots', [
                'reservation_id' => $reservation_id,
                'court_id' => (int) $payload['court_id'],
                'booking_date' => $payload['booking_date'],
                'hour' => (int) $hour,
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return [
                'ok' => false,
                'reservation_id' => null,
                'reservation_code' => null,
                'db_error' => $this->db->error(),
            ];
        }

        return [
            'ok' => true,
            'reservation_id' => $reservation_id,
            'reservation_code' => $reservation_code,
            'db_error' => null,
        ];
    }

    public function get_user_reservations($firebase_uid)
    {
        $q = $this->db->query(
            "SELECT r.*, f.name AS facility_name, c.name AS court_name
             FROM reservations r
             LEFT JOIN reservation_facilities f ON r.facility_id = f.id
             LEFT JOIN reservation_courts c ON r.court_id = c.id
             WHERE r.firebase_uid = ?
             ORDER BY r.created_at DESC",
            [$firebase_uid]
        )->result_array();

        foreach ($q as &$row) {
            $slots = $this->db->query(
                "SELECT hour FROM reservation_slots WHERE reservation_id = ? ORDER BY hour ASC",
                [$row['id']]
            )->result_array();
            $row['time_slots'] = array_map(function ($s) { return (int) $s['hour']; }, $slots);
        }

        return $q;
    }

    public function get_pending_reservations()
    {
        $q = $this->db->query(
            "SELECT r.*, f.name AS facility_name, c.name AS court_name
             FROM reservations r
             LEFT JOIN reservation_facilities f ON r.facility_id = f.id
             LEFT JOIN reservation_courts c ON r.court_id = c.id
             WHERE r.status = 'pending'
             ORDER BY r.created_at ASC"
        )->result_array();

        foreach ($q as &$row) {
            $slots = $this->db->query(
                "SELECT hour FROM reservation_slots WHERE reservation_id = ? ORDER BY hour ASC",
                [$row['id']]
            )->result_array();
            $row['time_slots'] = array_map(function ($s) { return (int) $s['hour']; }, $slots);
        }

        return $q;
    }

    public function get_all_pending_reservations()
    {
        // Get both local and Firebase synced reservations
        $q = $this->db->query(
            "SELECT r.*, f.name AS facility_name, c.name AS court_name,
                    CASE WHEN r.firebase_synced = 1 THEN 'Firebase' ELSE 'Local' END as data_source
             FROM reservations r
             LEFT JOIN reservation_facilities f ON r.facility_id = f.id
             LEFT JOIN reservation_courts c ON r.court_id = c.id
             WHERE r.status = 'pending'
             ORDER BY r.created_at ASC"
        )->result_array();

        foreach ($q as &$row) {
            $slots = $this->db->query(
                "SELECT hour FROM reservation_slots WHERE reservation_id = ? ORDER BY hour ASC",
                [$row['id']]
            )->result_array();
            $row['time_slots'] = array_map(function ($s) { return (int) $s['hour']; }, $slots);
        }

        return $q;
    }

    public function get_confirmed_reservations()
    {
        $q = $this->db->query(
            "SELECT r.*, f.name AS facility_name, c.name AS court_name,
                    CASE WHEN r.firebase_synced = 1 THEN 'Firebase' ELSE 'Local' END as data_source
             FROM reservations r
             LEFT JOIN reservation_facilities f ON r.facility_id = f.id
             LEFT JOIN reservation_courts c ON r.court_id = c.id
             WHERE r.status = 'confirmed'
             ORDER BY r.approved_at DESC
             LIMIT 20"
        )->result_array();

        foreach ($q as &$row) {
            $slots = $this->db->query(
                "SELECT hour FROM reservation_slots WHERE reservation_id = ? ORDER BY hour ASC",
                [$row['id']]
            )->result_array();
            $row['time_slots'] = array_map(function ($s) { return (int) $s['hour']; }, $slots);
        }

        return $q;
    }

    public function get_accepted_today_count()
    {
        $q = $this->db->query(
            "SELECT COUNT(*) AS total
             FROM reservations
             WHERE status = 'confirmed'
             AND DATE(booking_date) = CURDATE()"
        )->row_array();

        return isset($q['total']) ? (int) $q['total'] : 0;
    }

    public function sync_firebase_reservations()
    {
        $this->load->library('firebase_sync');
        return $this->firebase_sync->sync_reservations();
    }

    public function get_reservation_details($reservation_id)
    {
        $q = $this->db->query(
            "SELECT r.*, f.name AS facility_name, c.name AS court_name,
                    CASE WHEN r.firebase_synced = 1 THEN 'Firebase' ELSE 'Local' END as data_source
             FROM reservations r
             LEFT JOIN reservation_facilities f ON r.facility_id = f.id
             LEFT JOIN reservation_courts c ON r.court_id = c.id
             WHERE r.id = ?",
            [$reservation_id]
        )->row_array();

        if (!$q) {
            return null;
        }

        // Get time slots
        $slots = $this->db->query(
            "SELECT hour FROM reservation_slots WHERE reservation_id = ? ORDER BY hour ASC",
            [$reservation_id]
        )->result_array();
        $q['time_slots'] = array_map(function ($s) { return (int) $s['hour']; }, $slots);
        
        // Calculate duration
        $q['total_hours'] = count($q['time_slots']);
        
        // Format time range
        if (!empty($q['time_slots'])) {
            $start_hour = min($q['time_slots']);
            $end_hour = max($q['time_slots']) + 1;
            $q['time_range'] = sprintf("%02d:00 - %02d:00", $start_hour, $end_hour);
        } else {
            $q['time_range'] = '-';
        }

        return $q;
    }

    public function accept_reservation($reservation_id, $approved_by)
    {
        $row = $this->db->get_where('reservations', ['id' => (int) $reservation_id])->row_array();

        $this->db->update(
            'reservations',
            [
                'status' => 'confirmed',
                'approved_by' => $approved_by,
                'approved_at' => date('Y-m-d H:i:s'),
            ],
            ['id' => $reservation_id]
        );

        $ok = $this->db->affected_rows() > 0;

        if ($ok && !empty($row['firebase_uid'])) {
            $code = $row['reservation_code'] ?? ('#' . $reservation_id);
            $this->notify_mobile_user(
                $row['firebase_uid'],
                'Reservasi Dikonfirmasi ✅',
                'Reservasi ' . $code . ' telah dikonfirmasi. Siap bermain!',
                [
                    'type' => 'reservation_confirmed',
                    'reservation_id' => (string) $reservation_id,
                    'reservation_code' => (string) $code,
                ]
            );
        }

        return $ok;
    }

    public function reject_reservation($reservation_id, $rejected_by, $reason)
    {
        $this->db->trans_start();

        $this->db->update(
            'reservations',
            [
                'status' => 'rejected',
                'rejected_by' => $rejected_by,
                'rejected_at' => date('Y-m-d H:i:s'),
                'rejected_reason' => $reason,
            ],
            ['id' => $reservation_id]
        );

        $this->db->delete('reservation_slots', ['reservation_id' => $reservation_id]);

        $this->db->trans_complete();

        return $this->db->trans_status() !== FALSE;
    }

    public function request_cancellation($reservation_id, $reason, $requested_by)
    {
        $this->db->update(
            'reservations',
            [
                'status' => 'cancellation_requested',
                'cancellation_requested_at' => date('Y-m-d H:i:s'),
                'cancellation_requested_by' => $requested_by,
                'cancellation_reason' => $reason,
            ],
            ['id' => $reservation_id]
        );

        return $this->db->affected_rows() > 0;
    }

    public function approve_cancellation($reservation_id, $approved_by)
    {
        $this->db->trans_start();

        $this->db->update(
            'reservations',
            [
                'status' => 'cancelled',
                'cancellation_approved_at' => date('Y-m-d H:i:s'),
                'cancellation_approved_by' => $approved_by,
            ],
            ['id' => $reservation_id]
        );

        $this->db->delete('reservation_slots', ['reservation_id' => $reservation_id]);

        $this->db->trans_complete();

        return $this->db->trans_status() !== FALSE;
    }

    public function reject_cancellation($reservation_id, $rejected_by)
    {
        $this->db->update(
            'reservations',
            [
                'status' => 'confirmed',
                'cancellation_requested_at' => NULL,
                'cancellation_requested_by' => NULL,
                'cancellation_reason' => NULL,
            ],
            ['id' => $reservation_id]
        );

        return $this->db->affected_rows() > 0;
    }

    public function get_cancellation_requests()
    {
        $q = $this->db->query(
            "SELECT r.*, f.name AS facility_name, c.name AS court_name,
                    CASE WHEN r.firebase_synced = 1 THEN 'Firebase' ELSE 'Local' END as data_source
             FROM reservations r
             LEFT JOIN reservation_facilities f ON r.facility_id = f.id
             LEFT JOIN reservation_courts c ON r.court_id = c.id
             WHERE r.status = 'cancellation_requested'
             ORDER BY r.cancellation_requested_at ASC"
        )->result_array();

        foreach ($q as &$row) {
            $slots = $this->db->query(
                "SELECT hour FROM reservation_slots WHERE reservation_id = ? ORDER BY hour ASC",
                [$row['id']]
            )->result_array();
            $row['time_slots'] = array_map(function ($s) { return (int) $s['hour']; }, $slots);
        }

        return $q;
    }

    public function archive_reservation($reservation_id, $archived_by)
    {
        $reservation = $this->db->get_where('reservations', ['id' => $reservation_id])->row_array();
        if (!$reservation) {
            return false;
        }

        $this->db->trans_start();

        // Ensure tables exist (redundant but safe)
        $this->ensure_tables();

        // Insert into archive
        $archive_data = $reservation;
        $archive_data['original_reservation_id'] = $reservation['id'];
        $archive_data['archived_by'] = $archived_by;
        $archive_data['archived_at'] = date('Y-m-d H:i:s');
        $archive_data['data_source'] = $reservation['firebase_synced'] ? 'Firebase' : 'Local';
        
        // Remove original id to avoid conflict
        unset($archive_data['id']);
        
        $this->db->insert('reservations_archive', $archive_data);
        $archive_id = $this->db->insert_id();

        // Archive slots
        $slots = $this->db->get_where('reservation_slots', ['reservation_id' => $reservation_id])->result_array();
        foreach ($slots as $slot) {
            $archive_slot = $slot;
            $archive_slot['reservation_id'] = $archive_id;
            unset($archive_slot['id']);
            $this->db->insert('reservation_slots_archive', $archive_slot);
        }

        // Delete from original tables
        $this->db->delete('reservation_slots', ['reservation_id' => $reservation_id]);
        $this->db->delete('reservations', ['id' => $reservation_id]);

        $this->db->trans_complete();

        return $this->db->trans_status() !== FALSE;
    }

    public function get_archived_reservations($limit = 50, $offset = 0)
    {
        $q = $this->db->query(
            "SELECT ra.*, f.name AS facility_name, c.name AS court_name,
                    u.nama AS archived_by_name
             FROM reservations_archive ra
             LEFT JOIN reservation_facilities f ON ra.facility_id = f.id
             LEFT JOIN reservation_courts c ON ra.court_id = c.id
             LEFT JOIN master_user u ON ra.archived_by = u.id_user
             ORDER BY ra.archived_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        )->result_array();

        foreach ($q as &$row) {
            $slots = $this->db->query(
                "SELECT hour FROM reservation_slots_archive WHERE reservation_id = ? ORDER BY hour ASC",
                [$row['id']]
            )->result_array();
            $row['time_slots'] = array_map(function ($s) { return (int) $s['hour']; }, $slots);
        }

        return $q;
    }

    public function get_archived_reservation_details($reservation_id)
    {
        $q = $this->db->query(
            "SELECT ra.*, f.name AS facility_name, c.name AS court_name,
                    u.nama AS archived_by_name
             FROM reservations_archive ra
             LEFT JOIN reservation_facilities f ON ra.facility_id = f.id
             LEFT JOIN reservation_courts c ON ra.court_id = c.id
             LEFT JOIN master_user u ON ra.archived_by = u.id_user
             WHERE ra.id = ?",
            [$reservation_id]
        )->row_array();

        if (!$q) {
            return null;
        }

        // Get time slots
        $slots = $this->db->query(
            "SELECT hour FROM reservation_slots_archive WHERE reservation_id = ? ORDER BY hour ASC",
            [$reservation_id]
        )->result_array();
        $q['time_slots'] = array_map(function ($s) { return (int) $s['hour']; }, $slots);
        
        // Calculate duration
        $q['total_hours'] = count($q['time_slots']);
        
        // Format time range
        if (!empty($q['time_slots'])) {
            $start_hour = min($q['time_slots']);
            $end_hour = max($q['time_slots']) + 1;
            $q['time_range'] = sprintf("%02d:00 - %02d:00", $start_hour, $end_hour);
        } else {
            $q['time_range'] = '-';
        }

        return $q;
    }

    public function auto_complete_expired_reservations()
    {
        // Get current datetime
        $current_datetime = date('Y-m-d H:i:s');
        
        // Find confirmed bookings with slots that have ended
        $this->db->select('reservations.id, reservations.firebase_uid, reservations.reservation_code, reservations.booking_date, MAX(reservation_slots.hour) as last_hour');
        $this->db->from('reservations');
        $this->db->join('reservation_slots', 'reservation_slots.reservation_id = reservations.id', 'inner');
        $this->db->where('reservations.status', 'confirmed');
        $this->db->group_by('reservations.id');
        $this->db->having("CONCAT(reservations.booking_date, ' ', DATE_ADD(STR_TO_DATE(last_hour, '%k'), INTERVAL 1 HOUR)) <", $current_datetime);
        
        $expired_bookings = $this->db->get()->result_array();
        
        $completed_count = 0;

        foreach ($expired_bookings as $booking) {
            $this->db->where('id', $booking['id']);
            $this->db->update('reservations', [
                'status' => 'selesai',
                'updated_at' => $current_datetime
            ]);

            if (!empty($booking['firebase_uid'])) {
                $title = 'Reservasi Selesai! 🏆';
                $body = 'Reservasi ' . $booking['reservation_code'] . ' telah selesai. Yuk, beri rating untuk layanan kami!';
                $this->notify_mobile_user(
                    $booking['firebase_uid'],
                    $title,
                    $body,
                    [
                        'type' => 'rating_prompt',
                        'reservation_id' => (string) $booking['id'],
                        'reservation_code' => (string) $booking['reservation_code'],
                    ]
                );
            }

            $completed_count++;
        }
        
        return [
            'completed_count' => $completed_count,
            'message' => "Auto-completed {$completed_count} expired bookings and sent notifications"
        ];
    }
}
