<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_management_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->ensure_tables();
    }

    public function ensure_tables() {
        // 1. class_categories
        if (!$this->db->table_exists('class_categories')) {
            $this->db->query("CREATE TABLE `class_categories` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `description` text DEFAULT NULL,
              `status` enum('active','inactive') NOT NULL DEFAULT 'active',
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }

        // 2. class_types
        if (!$this->db->table_exists('class_types')) {
            $this->db->query("CREATE TABLE `class_types` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `category_id` int(11) NOT NULL,
              `name` varchar(100) NOT NULL,
              `description` text DEFAULT NULL,
              `instructor_name` varchar(100) DEFAULT NULL,
              `instructor_phone` varchar(20) DEFAULT NULL,
              `price_per_session` decimal(10,2) DEFAULT 0.00,
              `duration_minutes` int(11) DEFAULT 60,
              `max_participants` int(11) DEFAULT 10,
              `status` enum('active','inactive') NOT NULL DEFAULT 'active',
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }

        // 3. class_members
        if (!$this->db->table_exists('class_members')) {
            $this->db->query("CREATE TABLE `class_members` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `class_type_id` int(11) NOT NULL,
              `member_name` varchar(100) NOT NULL,
              `member_phone` varchar(20) DEFAULT NULL,
              `email` varchar(255) DEFAULT NULL,
              `firebase_uid` varchar(128) DEFAULT NULL,
              `qr_payload` varchar(255) DEFAULT NULL,
              `meetings_attended` int(11) NOT NULL DEFAULT 0,
              `join_date` date NOT NULL,
              `status` enum('active','inactive') NOT NULL DEFAULT 'active',
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }

        // 4. class_schedules
        if (!$this->db->table_exists('class_schedules')) {
            $this->db->query("CREATE TABLE `class_schedules` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `class_type_id` int(11) NOT NULL,
              `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
              `start_time` time NOT NULL,
              `end_time` time NOT NULL,
              `room` varchar(50) DEFAULT NULL,
              `status` enum('active','inactive') NOT NULL DEFAULT 'active',
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }

        // 5. class_sessions
        if (!$this->db->table_exists('class_sessions')) {
            $this->db->query("CREATE TABLE `class_sessions` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `class_type_id` int(11) NOT NULL,
              `session_number` int(11) NOT NULL DEFAULT 1,
              `session_date` date NOT NULL,
              `start_time` time NOT NULL,
              `end_time` time NOT NULL,
              `room` varchar(50) DEFAULT NULL,
              `current_participants` int(11) DEFAULT 0,
              `status` enum('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled',
              `notes` text DEFAULT NULL,
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }

        // 6. class_session_attendance
        if (!$this->db->table_exists('class_session_attendance')) {
            $this->db->query("CREATE TABLE `class_session_attendance` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `class_session_id` int(11) NOT NULL,
              `email` varchar(255) NOT NULL,
              `firebase_uid` varchar(128) DEFAULT NULL,
              `checked_in_at` datetime NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }

        // 7. class_bookings
        if (!$this->db->table_exists('class_bookings')) {
            $this->db->query("CREATE TABLE `class_bookings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `class_session_id` int(11) NOT NULL,
              `firebase_uid` varchar(128) NOT NULL,
              `participant_name` varchar(100) NOT NULL,
              `participant_phone` varchar(20) DEFAULT NULL,
              `booking_date` datetime NOT NULL DEFAULT current_timestamp(),
              `payment_status` enum('pending','paid','cancelled','refunded') NOT NULL DEFAULT 'pending',
              `payment_amount` decimal(10,2) DEFAULT 0.00,
              `payment_proof_url` text DEFAULT NULL,
              `status` enum('booked','attended','cancelled','no_show') NOT NULL DEFAULT 'booked',
              `notes` text DEFAULT NULL,
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }
    }

    // Class Categories
    public function get_categories() {
        return $this->db->get_where('class_categories', ['status' => 'active'])->result_array();
    }

    public function get_category($id) {
        return $this->db->get_where('class_categories', ['id' => $id])->row_array();
    }

    public function insert_category($data) {
        $this->db->insert('class_categories', $data);
        return $this->db->insert_id();
    }

    public function update_category($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('class_categories', $data);
    }

    public function delete_category($id) {
        $this->db->where('id', $id);
        return $this->db->update('class_categories', ['status' => 'inactive']);
    }

    // Class Types
    public function get_class_types($category_id = null) {
        $this->db->select('ct.*, cc.name as category_name');
        $this->db->from('class_types ct');
        $this->db->join('class_categories cc', 'cc.id = ct.category_id', 'left');
        $this->db->where('ct.status', 'active');
        
        if ($category_id) {
            $this->db->where('ct.category_id', $category_id);
        }
        
        return $this->db->get()->result_array();
    }

    public function get_class_type($id) {
        $this->db->select('ct.*, cc.name as category_name');
        $this->db->from('class_types ct');
        $this->db->join('class_categories cc', 'cc.id = ct.category_id', 'left');
        $this->db->where('ct.id', $id);
        
        return $this->db->get()->row_array();
    }

    public function insert_class_type($data) {
        $this->db->insert('class_types', $data);
        return $this->db->insert_id();
    }

    public function update_class_type($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('class_types', $data);
    }

    public function delete_class_type($id) {
        $this->db->where('id', $id);
        return $this->db->update('class_types', ['status' => 'inactive']);
    }

    // Class Schedules
    public function get_class_schedules($class_type_id = null) {
        $this->db->select('cs.*, ct.name as class_name, ct.duration_minutes');
        $this->db->from('class_schedules cs');
        $this->db->join('class_types ct', 'ct.id = cs.class_type_id', 'left');
        $this->db->where('cs.status', 'active');
        
        if ($class_type_id) {
            $this->db->where('cs.class_type_id', $class_type_id);
        }
        
        $this->db->order_by('cs.day_of_week', 'asc');
        $this->db->order_by('cs.start_time', 'asc');
        
        return $this->db->get()->result_array();
    }

    public function get_class_schedule($id) {
        $this->db->select('cs.*, ct.name as class_name');
        $this->db->from('class_schedules cs');
        $this->db->join('class_types ct', 'ct.id = cs.class_type_id', 'left');
        $this->db->where('cs.id', $id);
        
        return $this->db->get()->row_array();
    }

    public function insert_class_schedule($data) {
        $this->db->insert('class_schedules', $data);
        return $this->db->insert_id();
    }

    public function update_class_schedule($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('class_schedules', $data);
    }

    public function delete_class_schedule($id) {
        $this->db->where('id', $id);
        return $this->db->update('class_schedules', ['status' => 'inactive']);
    }

    // Class Sessions (actual scheduled classes)
    public function get_class_sessions($date = null, $class_type_id = null) {
        $this->db->select('csess.*, ct.name as class_name, ct.instructor_name, ct.max_participants');
        $this->db->from('class_sessions csess');
        $this->db->join('class_types ct', 'ct.id = csess.class_type_id', 'left');
        
        if ($date) {
            $this->db->where('csess.session_date', $date);
        }
        
        if ($class_type_id) {
            $this->db->where('csess.class_type_id', $class_type_id);
        }
        
        $this->db->order_by('csess.session_date', 'asc');
        $this->db->order_by('csess.start_time', 'asc');
        
        return $this->db->get()->result_array();
    }

    public function get_class_session($id) {
        $this->db->select('csess.*, ct.name as class_name, ct.instructor_name, ct.max_participants');
        $this->db->from('class_sessions csess');
        $this->db->join('class_types ct', 'ct.id = csess.class_type_id', 'left');
        $this->db->where('csess.id', $id);
        
        return $this->db->get()->row_array();
    }

    /** Get today's session for a class type (earliest by start_time) */
    public function get_today_session_for_class_type($class_type_id, $date = null) {
        $class_type_id = (int) $class_type_id;
        $date = $date ?: date('Y-m-d');
        $this->db->select('csess.*, ct.name as class_name, ct.instructor_name, ct.max_participants');
        $this->db->from('class_sessions csess');
        $this->db->join('class_types ct', 'ct.id = csess.class_type_id', 'left');
        $this->db->where('csess.class_type_id', $class_type_id);
        $this->db->where('csess.session_date', $date);
        $this->db->where('csess.status !=', 'cancelled');
        $this->db->order_by('csess.start_time', 'asc');
        return $this->db->get()->row_array();
    }

    public function insert_class_session($data) {
        $this->db->insert('class_sessions', $data);
        return $this->db->insert_id();
    }

    public function update_class_session($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('class_sessions', $data);
    }

    // Class Bookings
    public function get_class_bookings($session_id = null, $firebase_uid = null) {
        $this->db->select('cb.*, csess.session_date, csess.start_time, ct.name as class_name');
        $this->db->from('class_bookings cb');
        $this->db->join('class_sessions csess', 'csess.id = cb.class_session_id', 'left');
        $this->db->join('class_types ct', 'ct.id = csess.class_type_id', 'left');
        
        if ($session_id) {
            $this->db->where('cb.class_session_id', $session_id);
        }
        
        if ($firebase_uid) {
            $this->db->where('cb.firebase_uid', $firebase_uid);
        }
        
        $this->db->order_by('cb.booking_date', 'desc');
        
        return $this->db->get()->result_array();
    }

    public function insert_class_booking($data) {
        $this->db->insert('class_bookings', $data);
        return $this->db->insert_id();
    }

    public function update_class_booking($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('class_bookings', $data);
    }

    // Get available classes for mobile app
    public function get_available_classes($date = null) {
        $this->db->select('csess.*, ct.name as class_name, ct.description, ct.instructor_name, ct.max_participants, ct.duration_minutes, ct.category_id, cc.name as category_name');
        $this->db->from('class_sessions csess');
        $this->db->join('class_types ct', 'ct.id = csess.class_type_id', 'left');
        $this->db->join('class_categories cc', 'cc.id = ct.category_id', 'left');
        $this->db->where('csess.status', 'scheduled');
        $this->db->where('csess.session_date >=', date('Y-m-d'));
        
        if ($date) {
            $this->db->where('csess.session_date', $date);
        }
        
        $this->db->order_by('csess.session_date', 'asc');
        $this->db->order_by('csess.start_time', 'asc');
        
        return $this->db->get()->result_array();
    }

    // Get user's class bookings for mobile app
    public function get_user_class_bookings($firebase_uid) {
        $this->db->select('cb.*, csess.session_date, csess.start_time, csess.end_time, csess.room, ct.name as class_name, ct.instructor_name');
        $this->db->from('class_bookings cb');
        $this->db->join('class_sessions csess', 'csess.id = cb.class_session_id', 'left');
        $this->db->join('class_types ct', 'ct.id = csess.class_type_id', 'left');
        $this->db->where('cb.firebase_uid', $firebase_uid);
        $this->db->where('cb.status !=', 'cancelled');
        $this->db->order_by('cb.booking_date', 'desc');
        
        return $this->db->get()->result_array();
    }

    // Class Members/Students Management
    public function get_class_members($class_type_id) {
        return $this->db->get_where('class_members', ['class_type_id' => $class_type_id, 'status' => 'active'])->result_array();
    }

    public function get_member($id) {
        $this->db->select('cm.*, ct.category_id');
        $this->db->from('class_members cm');
        $this->db->join('class_types ct', 'ct.id = cm.class_type_id', 'left');
        $this->db->where('cm.id', $id);
        
        return $this->db->get()->row_array();
    }

    public function insert_member($data) {
        $this->db->insert('class_members', $data);
        return $this->db->insert_id();
    }

    public function get_member_by_id($id) {
        return $this->db->get_where('class_members', ['id' => (int) $id, 'status' => 'active'])->row_array();
    }

    public function increment_member_meetings_attended($id) {
        $this->db->set('meetings_attended', 'meetings_attended + 1', false);
        $this->db->where('id', (int) $id);
        return $this->db->update('class_members');
    }

    public function delete_member($id) {
        $this->db->where('id', $id);
        return $this->db->update('class_members', ['status' => 'inactive']);
    }

    public function get_app_users() {
        // Fetch users from Firebase Firestore 'users' collection
        $this->load->library('firebase_admin');
        $firebase_users = $this->firebase_admin->get_all_documents('users');
        
        $filtered_users = [];
        if (is_array($firebase_users)) {
            foreach ($firebase_users as $user) {
                // If user data is missing essential fields, skip or handle gracefully
                $uid = $user['id'] ?? $user['uid'] ?? '';
                if (empty($uid)) continue;

                $email = $user['email'] ?? '';
                $name = $user['name'] ?? $user['displayName'] ?? 'No Name';
                $phone = $user['phone'] ?? $user['phoneNumber'] ?? '';

                // Check if role is 'user' (default to 'user' if not set, case-insensitive)
                $role = strtolower((string) ($user['role'] ?? 'user'));
                
                // Allow empty role to be treated as user too, as some old users might not have it
                if ($role === 'user' || $role === '') {
                    $filtered_users[] = [
                        'id_user' => $uid,
                        'nama' => $name,
                        'email' => $email,
                        'nohp' => $phone
                    ];
                }
            }
        } else {
            log_message('error', 'Class_management_model: get_all_documents("users") did not return an array. Check Firebase connection/credentials.');
        }
        
        return $filtered_users;
    }

    public function is_class_member($class_type_id, $email) {
        $email = strtolower(trim($email));
        $query = $this->db->get_where('class_members', [
            'class_type_id' => $class_type_id,
            'email' => $email,
            'status' => 'active'
        ]);
        return $query->num_rows() > 0;
    }

    public function get_class_types_by_category($category_id) {
        $this->db->select('ct.*, cc.name as category_name');
        $this->db->from('class_types ct');
        $this->db->join('class_categories cc', 'cc.id = ct.category_id', 'left');
        $this->db->where('ct.category_id', $category_id);
        $this->db->where('ct.status', 'active');
        
        return $this->db->get()->result_array();
    }

    /** Next pertemuan number for a class_type */
    public function get_next_session_number($class_type_id) {
        $row = $this->db->select_max('session_number')
            ->where('class_type_id', $class_type_id)
            ->get('class_sessions')
            ->row_array();
        $max = isset($row['session_number']) ? (int) $row['session_number'] : 0;
        return $max + 1;
    }

    public function has_session_attendance($class_session_id, $email) {
        $email = strtolower(trim($email));
        $n = $this->db->get_where('class_session_attendance', [
            'class_session_id' => $class_session_id,
            'email' => $email,
        ])->num_rows();
        return $n > 0;
    }

    public function insert_session_attendance($class_session_id, $email, $firebase_uid = null) {
        $email = strtolower(trim($email));
        $data = [
            'class_session_id' => $class_session_id,
            'email' => $email,
            'firebase_uid' => $firebase_uid,
            'checked_in_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->insert('class_session_attendance', $data);
    }

    public function get_session_attendance_rows($class_session_id) {
        $this->db->where('class_session_id', $class_session_id);
        $this->db->order_by('checked_in_at', 'asc');
        return $this->db->get('class_session_attendance')->result_array();
    }

    public function delete_class_session($id) {
        $this->db->where('id', $id);
        return $this->db->delete('class_sessions');
    }
}
