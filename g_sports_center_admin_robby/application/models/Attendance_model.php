<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->ensure_schema();
    }

    /** Definisi resmi 3 shift operasional G Sports Center */
    public static function shift_definitions()
    {
        return [
            '07:00-15:00' => ['label' => 'Pagi', 'start' => '07:00', 'end' => '15:00'],
            '13:00-18:00' => ['label' => 'Middle', 'start' => '13:00', 'end' => '18:00'],
            '15:00-23:00' => ['label' => 'Malam', 'start' => '15:00', 'end' => '23:00'],
        ];
    }

    public function get_shift_label($shift_code)
    {
        if (!$shift_code || $shift_code === 'off') {
            return 'Libur';
        }

        $defs = self::shift_definitions();
        if (isset($defs[$shift_code])) {
            return $defs[$shift_code]['label'];
        }

        return $shift_code;
    }

    public function format_shift_display($shift_code)
    {
        if (!$shift_code || $shift_code === 'off') {
            return 'Libur';
        }

        $defs = self::shift_definitions();
        if (isset($defs[$shift_code])) {
            $d = $defs[$shift_code];
            return $d['label'] . ' (' . $d['start'] . '–' . $d['end'] . ')';
        }

        return $shift_code;
    }

    public function ensure_schema()
    {
        if ($this->db->table_exists('attendance_settings')) {
            if (!$this->db->field_exists('grace_period_minutes', 'attendance_settings')) {
                $this->db->query('ALTER TABLE `attendance_settings` ADD COLUMN `grace_period_minutes` SMALLINT UNSIGNED NOT NULL DEFAULT 15 AFTER `radius_meters`');
            }
            if (!$this->db->field_exists('alpha_cutoff_time', 'attendance_settings')) {
                $this->db->query("ALTER TABLE `attendance_settings` ADD COLUMN `alpha_cutoff_time` TIME NOT NULL DEFAULT '23:00:00' AFTER `grace_period_minutes`");
            }
        }

        if ($this->db->table_exists('attendances')) {
            if (!$this->db->field_exists('schedule_shift', 'attendances')) {
                $this->db->query('ALTER TABLE `attendances` ADD COLUMN `schedule_shift` VARCHAR(32) DEFAULT NULL AFTER `status`');
            }
            if (!$this->db->field_exists('schedule_start', 'attendances')) {
                $this->db->query('ALTER TABLE `attendances` ADD COLUMN `schedule_start` TIME DEFAULT NULL AFTER `schedule_shift`');
            }
            if (!$this->db->field_exists('schedule_end', 'attendances')) {
                $this->db->query('ALTER TABLE `attendances` ADD COLUMN `schedule_end` TIME DEFAULT NULL AFTER `schedule_start`');
            }
            if (!$this->db->field_exists('employee_uid', 'attendances')) {
                $this->db->query('ALTER TABLE `attendances` ADD COLUMN `employee_uid` VARCHAR(255) DEFAULT NULL AFTER `employee_id`');
            }
            if (!$this->db->field_exists('selfie_url', 'attendances')) {
                $this->db->query('ALTER TABLE `attendances` ADD COLUMN `selfie_url` LONGTEXT DEFAULT NULL');
            }
            if (!$this->db->field_exists('is_mocked', 'attendances')) {
                $this->db->query('ALTER TABLE `attendances` ADD COLUMN `is_mocked` TINYINT(1) DEFAULT 0');
            }
        }

        if ($this->db->table_exists('attendances_archive')) {
            if (!$this->db->field_exists('schedule_shift', 'attendances_archive')) {
                $this->db->query('ALTER TABLE `attendances_archive` ADD COLUMN `schedule_shift` VARCHAR(32) DEFAULT NULL AFTER `status`');
            }
            if (!$this->db->field_exists('schedule_start', 'attendances_archive')) {
                $this->db->query('ALTER TABLE `attendances_archive` ADD COLUMN `schedule_start` TIME DEFAULT NULL AFTER `schedule_shift`');
            }
            if (!$this->db->field_exists('schedule_end', 'attendances_archive')) {
                $this->db->query('ALTER TABLE `attendances_archive` ADD COLUMN `schedule_end` TIME DEFAULT NULL AFTER `schedule_start`');
            }
            if (!$this->db->field_exists('photoBase64', 'attendances_archive')) {
                $this->db->query('ALTER TABLE `attendances_archive` ADD COLUMN `photoBase64` LONGTEXT DEFAULT NULL AFTER `notes`');
            }
            if (!$this->db->field_exists('employee_uid', 'attendances_archive')) {
                $this->db->query('ALTER TABLE `attendances_archive` ADD COLUMN `employee_uid` VARCHAR(255) DEFAULT NULL AFTER `employee_id`');
            }
            if (!$this->db->field_exists('selfie_url', 'attendances_archive')) {
                $this->db->query('ALTER TABLE `attendances_archive` ADD COLUMN `selfie_url` LONGTEXT DEFAULT NULL');
            }
            if (!$this->db->field_exists('is_mocked', 'attendances_archive')) {
                $this->db->query('ALTER TABLE `attendances_archive` ADD COLUMN `is_mocked` TINYINT(1) DEFAULT 0');
            }
        }

        $this->load->model('Employee_model', 'employee_schema');
        $this->employee_schema->ensure_schema();
    }

    // Get attendance settings
    public function get_settings()
    {
        $this->ensure_schema();
        return $this->db->get_where('attendance_settings', ['is_active' => 1])->row_array();
    }

    public function get_grace_period_minutes()
    {
        $settings = $this->get_settings();
        $grace = isset($settings['grace_period_minutes']) ? (int) $settings['grace_period_minutes'] : 15;
        return $grace > 0 ? $grace : 15;
    }

    // Save attendance settings
    public function save_settings($data)
    {
        $this->ensure_schema();
        $this->db->trans_start();
        
        $settings = [
            'location_name' => $data['location_name'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'radius_meters' => $data['radius_meters'],
            'grace_period_minutes' => isset($data['grace_period_minutes']) ? (int) $data['grace_period_minutes'] : 15,
            'alpha_cutoff_time' => isset($data['alpha_cutoff_time']) ? $this->normalize_time($data['alpha_cutoff_time']) : '23:00:00',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (isset($data['created_by'])) {
            $settings['created_by'] = $data['created_by'];
        }
        
        // Check if settings exist
        $existing = $this->db->get('attendance_settings')->row_array();
        
        if ($existing) {
            $this->db->where('id', $existing['id']);
            $this->db->update('attendance_settings', $settings);
        } else {
            $this->db->insert('attendance_settings', $settings);
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return [
                'success' => false,
                'message' => 'Failed to save settings'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Settings saved successfully'
        ];
    }

    // Check if employee already attended today
    public function check_today_attendance($employee_id, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        return $this->db->get_where('attendances', [
            'employee_id' => $employee_id,
            'date' => $date
        ])->row_array();
    }

    // Save attendance (from mobile app)
    public function save_attendance($data)
    {
        $this->ensure_schema();
        $this->db->trans_start();
        
        $attendance_data = [
            'employee_id' => $data['employee_id'],
            'employee_uid' => isset($data['employee_uid']) ? $data['employee_uid'] : null,
            'employee_name' => $data['employee_name'],
            'employee_email' => isset($data['employee_email']) ? $data['employee_email'] : null,
            'date' => $data['date'],
            'check_in' => $data['check_in'],
            'status' => $data['status'],
            'schedule_shift' => isset($data['schedule_shift']) ? $data['schedule_shift'] : null,
            'schedule_start' => isset($data['schedule_start']) ? $data['schedule_start'] : null,
            'schedule_end' => isset($data['schedule_end']) ? $data['schedule_end'] : null,
            'check_in_latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'check_in_longitude' => isset($data['longitude']) ? $data['longitude'] : null,
            'distance_meters' => isset($data['distance_meters']) ? $data['distance_meters'] : null,
            'radius_meters' => isset($data['radius_meters']) ? $data['radius_meters'] : null,
            'location_name' => isset($data['location_name']) ? $data['location_name'] : null,
            'photoBase64' => isset($data['photoBase64']) ? $data['photoBase64'] : null,
            'selfie_url' => isset($data['selfie_url']) ? $data['selfie_url'] : null,
            'is_mocked' => isset($data['is_mocked']) ? (int)$data['is_mocked'] : 0,
            'notes' => isset($data['notes']) ? $data['notes'] : null
        ];
        
        // Check if already exists
        $existing = $this->check_today_attendance($data['employee_id'], $data['date']);
        
        if ($existing) {
            $this->db->where('id', $existing['id']);
            $this->db->update('attendances', $attendance_data);
            $attendance_id = $existing['id'];
        } else {
            $this->db->insert('attendances', $attendance_data);
            $attendance_id = $this->db->insert_id();
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return [
                'success' => false,
                'message' => 'Failed to save attendance',
                'attendance_id' => null
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Attendance saved successfully',
            'attendance_id' => $attendance_id
        ];
    }

    // Update check out
    public function update_checkout($employee_id, $check_out_time, $latitude = null, $longitude = null)
    {
        $update_data = [
            'check_out' => $check_out_time,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($latitude !== null) {
            $update_data['check_out_latitude'] = $latitude;
        }
        
        if ($longitude !== null) {
            $update_data['check_out_longitude'] = $longitude;
        }
        
        $this->db->where('employee_id', $employee_id);
        $this->db->where('date', date('Y-m-d'));
        $this->db->update('attendances', $update_data);
        
        return $this->db->affected_rows() > 0;
    }

    // Get attendances with filters
    public function get_attendances($date_from = null, $date_to = null, $status = null, $search = null, $limit = 50, $offset = 0)
    {
        $this->db->select('a.*, e.nama as employee_name_from_db, e.jabatan');
        $this->db->from('attendances a');
        $this->db->join('employees e', 'a.employee_id = e.id', 'left');
        
        if ($date_from) {
            $this->db->where('a.date >=', $date_from);
        }
        
        if ($date_to) {
            $this->db->where('a.date <=', $date_to);
        }
        
        if ($status && $status !== 'all') {
            $this->db->where('a.status', $status);
        }
        
        if ($search) {
            $this->db->like('a.employee_name', $search);
            $this->db->or_like('e.nama', $search);
        }
        
        $this->db->order_by('a.date DESC, a.check_in DESC');
        $this->db->limit($limit, $offset);
        
        $result = $this->db->get()->result_array();
        
        // Use real employee name from master_user if available
        foreach ($result as &$row) {
            if (!empty($row['employee_name_from_db'])) {
                $row['display_name'] = $row['employee_name_from_db'];
            } else {
                $row['display_name'] = $row['employee_name'];
            }
        }
        
        return $result;
    }

    /**
     * Karyawan yang wajib absen (punya jadwal operasional aktif).
     */
    public function get_attendance_eligible_employees()
    {
        $this->ensure_schema();
        return $this->db->query("
            SELECT id, nama, email, firebase_uid, jadwal_operasional
            FROM employees
            WHERE status = 'active'
              AND jadwal_operasional IS NOT NULL
              AND TRIM(jadwal_operasional) != ''
              AND TRIM(jadwal_operasional) != '{}'
        ")->result_array();
    }

    /**
     * Hari ini karyawan dijadwalkan kerja (hari kerja + shift bukan libur).
     */
    public function employee_expects_attendance_on_date($employee_id, $date_str)
    {
        $ctx = $this->get_schedule_context($employee_id, null, $date_str);

        if (!$ctx['has_schedule'] || !$ctx['is_work_day']) {
            return false;
        }

        if ($ctx['shift'] === 'off' || empty($ctx['schedule_start'])) {
            return false;
        }

        return true;
    }

    /**
     * Jam selesai shift dari string "07:00-15:00".
     */
    public function parse_shift_end_time($shift_code)
    {
        if (!$shift_code || $shift_code === 'off') {
            return null;
        }

        if (preg_match('/\d{1,2}:\d{2}-(\d{1,2}):(\d{2})/', $shift_code, $m)) {
            return sprintf('%02d:%02d:00', (int) $m[1], (int) $m[2]);
        }

        return null;
    }

    /**
     * Sudah boleh menandai alpha (hari lampau, atau hari ini setelah shift selesai / cutoff global).
     */
    public function is_alpha_cutoff_passed($date_str, $employee_id, $employee_uid = null)
    {
        $today = date('Y-m-d');

        if ($date_str < $today) {
            return true;
        }

        if ($date_str > $today) {
            return false;
        }

        $now = new DateTime();
        $settings = $this->get_settings();
        $global_cutoff = isset($settings['alpha_cutoff_time']) ? $settings['alpha_cutoff_time'] : '23:00:00';
        $global_dt = DateTime::createFromFormat('Y-m-d H:i:s', $date_str . ' ' . $this->normalize_time($global_cutoff));

        if ($global_dt && $now >= $global_dt) {
            return true;
        }

        $ctx = $this->get_schedule_context($employee_id, $employee_uid, $date_str);
        $shift_end = $this->parse_shift_end_time($ctx['shift']);

        if ($shift_end) {
            $end_dt = DateTime::createFromFormat('Y-m-d H:i:s', $date_str . ' ' . $shift_end);
            if ($end_dt) {
                $end_dt->modify('+' . $this->get_grace_period_minutes() . ' minutes');
                if ($now >= $end_dt) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Buat/update record alpha untuk karyawan berjadwal yang tidak absen.
     *
     * @param string $date_str Tanggal (Y-m-d)
     * @param array $options force => true abaikan cutoff (untuk arsip/manual)
     */
    public function sync_alpha_for_date($date_str, $options = [])
    {
        $this->ensure_schema();
        $force = !empty($options['force']);
        $employees = $this->get_attendance_eligible_employees();
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($employees as $emp) {
            $employee_id = (int) $emp['id'];

            if (!$this->employee_expects_attendance_on_date($employee_id, $date_str)) {
                $skipped++;
                continue;
            }

            if (!$force && !$this->is_alpha_cutoff_passed($date_str, $employee_id)) {
                $skipped++;
                continue;
            }

            $existing = $this->db->get_where('attendances', [
                'employee_id' => $employee_id,
                'date' => $date_str,
            ])->row_array();

            if ($existing) {
                if (!empty($existing['check_in'])) {
                    $skipped++;
                    continue;
                }
                if (in_array($existing['status'], ['izin', 'alpha'], true)) {
                    $skipped++;
                    continue;
                }
                if (in_array($existing['status'], ['hadir', 'terlambat', 'fake gps'], true)) {
                    $skipped++;
                    continue;
                }
            }

            $ctx = $this->get_schedule_context($employee_id, null, $date_str);
            $alpha_data = [
                'employee_id' => $employee_id,
                'employee_uid' => $emp['firebase_uid'] ?? null,
                'employee_name' => $emp['nama'],
                'employee_email' => $emp['email'] ?? null,
                'date' => $date_str,
                'check_in' => null,
                'check_out' => null,
                'status' => 'alpha',
                'schedule_shift' => $ctx['shift'],
                'schedule_start' => $ctx['schedule_start'],
                'schedule_end' => $ctx['schedule_end'],
                'notes' => 'Alpha otomatis — tidak melakukan absensi pada hari kerja terjadwal',
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $this->db->where('id', $existing['id']);
                $this->db->update('attendances', $alpha_data);
                $updated++;
            } else {
                $alpha_data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('attendances', $alpha_data);
                $created++;
            }
        }

        return [
            'success' => true,
            'date' => $date_str,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'total' => $created + $updated,
        ];
    }

    /**
     * Sinkron alpha untuk rentang tanggal (maks 31 hari).
     */
    public function sync_alpha_for_range($date_from, $date_to, $options = [])
    {
        $start = new DateTime($date_from);
        $end = new DateTime($date_to);

        if ($start > $end) {
            return ['success' => false, 'message' => 'Rentang tanggal tidak valid'];
        }

        $days = (int) $start->diff($end)->days + 1;
        if ($days > 31) {
            return ['success' => false, 'message' => 'Maksimal 31 hari per proses'];
        }

        $summary = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'days' => 0];

        for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
            $result = $this->sync_alpha_for_date($d->format('Y-m-d'), $options);
            $summary['created'] += $result['created'];
            $summary['updated'] += $result['updated'];
            $summary['skipped'] += $result['skipped'];
            $summary['days']++;
        }

        $summary['success'] = true;
        $summary['total'] = $summary['created'] + $summary['updated'];

        return $summary;
    }

    // Get today's attendances
    public function get_today_attendances()
    {
        $today = date('Y-m-d');
        
        $result = $this->db->query("
            SELECT a.*, e.nama as employee_name_from_db, e.jabatan
            FROM attendances a
            LEFT JOIN employees e ON a.employee_id = e.id
            WHERE a.date = ?
            ORDER BY a.check_in DESC
        ", [$today])->result_array();
        
        // Use real employee name from master_user if available
        foreach ($result as &$row) {
            if (!empty($row['employee_name_from_db'])) {
                $row['display_name'] = $row['employee_name_from_db'];
            } else {
                $row['display_name'] = $row['employee_name'];
            }
        }
        
        return $result;
    }

    // Get attendance statistics
    public function get_attendance_stats($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $stats = [];
        
        // Status counts for today
        $q = $this->db->query("
            SELECT status, COUNT(*) as count
            FROM attendances 
            WHERE date = ?
            GROUP BY status
        ", [$date])->result_array();
        
        $status_counts = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'alpha' => 0,
            'fake gps' => 0
        ];
        
        foreach ($q as $row) {
            $status_counts[$row['status']] = (int)$row['count'];
        }
        
        $stats['today'] = $status_counts;
        
        // Total employees
        $this->load->model('Employee_model', 'employee_m');
        $this->employee_m->ensure_schema();
        $stats['total_employees'] = $this->employee_m->count_active();
        
        // Completion rate
        $stats['completion_rate'] = $stats['total_employees'] > 0 
            ? round((array_sum($status_counts) / $stats['total_employees']) * 100, 1) 
            : 0;
        
        // Weekly trend
        $q = $this->db->query("
            SELECT date, status, COUNT(*) as count
            FROM attendances 
            WHERE date >= DATE_SUB(?, INTERVAL 7 DAY)
            GROUP BY date, status
            ORDER BY date DESC
        ", [$date])->result_array();
        
        $stats['weekly_trend'] = $q;
        
        return $stats;
    }

    // Archive attendances
    public function archive_attendances($date)
    {
        $this->ensure_schema();
        $this->sync_alpha_for_date($date, ['force' => true]);

        $this->db->trans_start();
        
        // Get attendances to archive
        $attendances = $this->db->get_where('attendances', ['date' => $date])->result_array();
        
        if (empty($attendances)) {
            return [
                'success' => false,
                'message' => 'No attendances found for this date',
                'count' => 0
            ];
        }
        
        // Move to archive
        foreach ($attendances as $attendance) {
            $archive_data = $attendance;
            $archive_data['original_attendance_id'] = $attendance['id'];
            $archive_data['archived_by'] = $this->session->userdata('id_user');
            unset($archive_data['id']);
            
            $this->db->insert('attendances_archive', $archive_data);
        }
        
        // Delete from main table
        $this->db->where('date', $date);
        $this->db->delete('attendances');
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return [
                'success' => false,
                'message' => 'Failed to archive attendances',
                'count' => 0
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Attendances archived successfully',
            'count' => count($attendances)
        ];
    }

    // Get archived attendances
    public function get_archived_attendances($date_from = null, $date_to = null, $limit = 50, $offset = 0)
    {
        $this->db->select('aa.*, e.nama as employee_name_from_db');
        $this->db->from('attendances_archive aa');
        $this->db->join('employees e', 'aa.employee_id = e.id', 'left');
        
        if ($date_from) {
            $this->db->where('aa.date >=', $date_from);
        }
        
        if ($date_to) {
            $this->db->where('aa.date <=', $date_to);
        }
        
        $this->db->order_by('aa.date DESC, aa.archived_at DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
    }

    // Calculate distance between two coordinates (Haversine formula)
    public function calculate_distance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371000; // Earth's radius in meters
        
        $lat_diff = deg2rad($lat2 - $lat1);
        $lon_diff = deg2rad($lon2 - $lon1);
        
        $a = sin($lat_diff / 2) * sin($lat_diff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lon_diff / 2) * sin($lon_diff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earth_radius * $c; // Distance in meters
    }

    // Check if user is within radius
    public function check_radius($user_lat, $user_lon, $settings = null)
    {
        if (!$settings) {
            $settings = $this->get_settings();
        }
        
        if (!$settings) {
            return [
                'within_radius' => false,
                'distance' => null,
                'message' => 'Attendance settings not configured'
            ];
        }
        
        $distance = $this->calculate_distance(
            $user_lat, $user_lon,
            $settings['latitude'], $settings['longitude']
        );
        
        return [
            'within_radius' => $distance <= $settings['radius_meters'],
            'distance' => $distance,
            'radius' => $settings['radius_meters'],
            'settings' => $settings
        ];
    }

    /**
     * Resolve employee SQL id from numeric id or Firebase UID.
     */
    public function resolve_employee_id($employee_id, $employee_uid = null)
    {
        if (is_numeric($employee_id) && (int) $employee_id > 0) {
            return (int) $employee_id;
        }
        if ($employee_uid) {
            $this->load->model('Employee_model', 'employee_m');
            $this->employee_m->ensure_schema();
            $row = $this->employee_m->get_by_firebase_uid($employee_uid);
            if ($row) {
                return (int) $row['id'];
            }
        }
        return 0;
    }

    /**
     * Calendar week number (selaras dengan rolling jadwal di BookingService mobile).
     */
    private function calendar_week_number($date_str)
    {
        $target = new DateTime($date_str);
        $year = (int) $target->format('Y');
        $start = new DateTime($year . '-01-01');
        if ($target < $start) {
            return 1;
        }
        $days = (int) $start->diff($target)->days;
        return max(1, (int) ceil($days / 7));
    }

    /**
     * Parse jadwal_operasional JSON dari tabel employees.
     */
    public function get_employee_schedule_config($employee_id)
    {
        $employee_id = (int) $employee_id;
        if ($employee_id <= 0) {
            return null;
        }

        $this->load->model('Employee_model', 'employee_m');
        $this->employee_m->ensure_schema();

        $row = $this->db->select('jadwal_operasional, nama')
            ->get_where('employees', ['id' => $employee_id])
            ->row_array();

        if (!$row || empty($row['jadwal_operasional'])) {
            return null;
        }

        $config = json_decode($row['jadwal_operasional'], true);
        if (!is_array($config)) {
            return null;
        }

        return $config;
    }

    /**
     * Shift aktif untuk tanggal tertentu (2 minggu / 3 minggu rolling).
     * Return: ['shift' => '07:00-15:00'|'off', 'pattern' => ..., 'week' => n]
     */
    public function resolve_shift_for_date($schedule, $date_str)
    {
        if (!$schedule || !is_array($schedule)) {
            return ['shift' => null, 'pattern' => null, 'week' => null];
        }

        $pattern = isset($schedule['pattern_type']) ? $schedule['pattern_type'] : '2week';
        $week = $this->calendar_week_number($date_str);

        $m1 = $schedule['m1'] ?? ($schedule['ganjil'] ?? '07:00-15:00');
        $m2 = $schedule['m2'] ?? ($schedule['genap'] ?? '15:00-23:00');
        $m3 = $schedule['m3'] ?? '07:00-15:00';

        if ($pattern === '3week') {
            $idx = ($week - 1) % 3;
            $keys = [$m1, $m2, $m3];
            $shift = $keys[$idx];
        } else {
            $is_odd_week = ($week % 2 !== 0);
            $shift = $is_odd_week ? $m1 : $m2;
        }

        return [
            'shift' => $shift,
            'pattern' => $pattern,
            'week' => $week,
        ];
    }

    /**
     * Ambil jam masuk dari string shift "07:00-15:00".
     */
    public function parse_shift_start_time($shift_code)
    {
        if (!$shift_code || $shift_code === 'off') {
            return null;
        }

        if (preg_match('/^(\d{1,2}):(\d{2})/', $shift_code, $m)) {
            return sprintf('%02d:%02d:00', (int) $m[1], (int) $m[2]);
        }

        return null;
    }

    /**
     * Konteks jadwal lengkap untuk absensi pada tanggal tertentu.
     */
    public function get_schedule_context($employee_id, $employee_uid, $date_str)
    {
        $resolved_id = $this->resolve_employee_id($employee_id, $employee_uid);
        $config = $this->get_employee_schedule_config($resolved_id);
        $day_of_week = (int) date('w', strtotime($date_str));
        $work_days = [];

        if ($config && isset($config['work_days']) && is_array($config['work_days'])) {
            $work_days = array_map('intval', $config['work_days']);
        }

        $is_work_day = empty($work_days) ? true : in_array($day_of_week, $work_days, true);
        $resolved = $this->resolve_shift_for_date($config, $date_str);
        $shift = $resolved['shift'];
        $schedule_start = $this->parse_shift_start_time($shift);
        $schedule_end = $this->parse_shift_end_time($shift);

        return [
            'employee_id' => $resolved_id,
            'is_work_day' => $is_work_day,
            'shift' => $shift,
            'shift_label' => $this->get_shift_label($shift),
            'schedule_start' => $schedule_start,
            'schedule_end' => $schedule_end,
            'pattern' => $resolved['pattern'],
            'week' => $resolved['week'],
            'work_days' => $work_days,
            'has_schedule' => ($config !== null),
        ];
    }

    /**
     * Validasi absen pulang: hanya setelah jam selesai shift (bebas kapan setelah itu).
     */
    public function can_checkout($employee_id, $employee_uid = null, $date_str = null, $time_str = null)
    {
        $resolved_id = $this->resolve_employee_id($employee_id, $employee_uid);
        $date_str = $date_str ?: date('Y-m-d');
        $time_str = $this->normalize_time($time_str ?: date('H:i:s'));

        $existing = $this->db->get_where('attendances', [
            'employee_id' => $resolved_id,
            'date' => $date_str,
        ])->row_array();

        if (!$existing || empty($existing['check_in'])) {
            return [
                'allowed' => false,
                'reason' => 'Anda belum melakukan absen masuk hari ini',
                'schedule_end' => null,
            ];
        }

        if (!empty($existing['check_out'])) {
            return [
                'allowed' => false,
                'reason' => 'Anda sudah melakukan absen pulang hari ini',
                'schedule_end' => null,
                'check_out' => $existing['check_out'],
            ];
        }

        $ctx = $this->get_schedule_context($resolved_id, $employee_uid, $date_str);
        $shift_end = $ctx['schedule_end'];

        if (!$shift_end) {
            return [
                'allowed' => true,
                'reason' => '',
                'schedule_end' => null,
                'shift_label' => $ctx['shift_label'],
            ];
        }

        $now = DateTime::createFromFormat('Y-m-d H:i:s', $date_str . ' ' . $time_str);
        $end_dt = DateTime::createFromFormat('Y-m-d H:i:s', $date_str . ' ' . $shift_end);

        if ($now && $end_dt && $now >= $end_dt) {
            return [
                'allowed' => true,
                'reason' => '',
                'schedule_end' => substr($shift_end, 0, 5),
                'shift_label' => $ctx['shift_label'],
            ];
        }

        return [
            'allowed' => false,
            'reason' => sprintf(
                'Absen pulang tersedia setelah jam %s (akhir shift %s)',
                substr($shift_end, 0, 5),
                $ctx['shift_label']
            ),
            'schedule_end' => substr($shift_end, 0, 5),
            'shift_label' => $ctx['shift_label'],
            'available_at' => substr($shift_end, 0, 5),
        ];
    }

    /**
     * Status absensi hari ini + jadwal (untuk mobile).
     */
    public function get_today_attendance_status($employee_id, $employee_uid = null)
    {
        $resolved_id = $this->resolve_employee_id($employee_id, $employee_uid);
        $date_str = date('Y-m-d');
        $ctx = $this->get_schedule_context($resolved_id, $employee_uid, $date_str);
        $grace = $this->get_grace_period_minutes();

        $deadline = null;
        if ($ctx['schedule_start']) {
            $deadline_dt = DateTime::createFromFormat('H:i:s', $ctx['schedule_start']);
            if ($deadline_dt) {
                $deadline_dt->modify('+' . $grace . ' minutes');
                $deadline = $deadline_dt->format('H:i');
            }
        }

        $record = $this->db->get_where('attendances', [
            'employee_id' => $resolved_id,
            'date' => $date_str,
        ])->row_array();

        $checkout_check = $this->can_checkout($resolved_id, $employee_uid, $date_str);

        return [
            'date' => $date_str,
            'shift' => $ctx['shift'],
            'shift_label' => $ctx['shift_label'],
            'shift_display' => $this->format_shift_display($ctx['shift']),
            'schedule_start' => $ctx['schedule_start'] ? substr($ctx['schedule_start'], 0, 5) : null,
            'schedule_end' => $ctx['schedule_end'] ? substr($ctx['schedule_end'], 0, 5) : null,
            'deadline' => $deadline,
            'grace_period_minutes' => $grace,
            'is_work_day' => $ctx['is_work_day'],
            'pattern' => $ctx['pattern'],
            'week' => $ctx['week'],
            'check_in' => $record['check_in'] ?? null,
            'check_out' => $record['check_out'] ?? null,
            'status' => $record['status'] ?? null,
            'can_check_in' => !$record || empty($record['check_in']),
            'can_checkout' => $checkout_check['allowed'],
            'checkout_message' => $checkout_check['reason'],
            'checkout_available_at' => $checkout_check['available_at'] ?? ($ctx['schedule_end'] ? substr($ctx['schedule_end'], 0, 5) : null),
        ];
    }

    /**
     * Tentukan status hadir / terlambat berdasarkan jadwal shift karyawan.
     *
     * Aturan:
     * - Absen sebelum jam masuk shift → hadir
     * - Absen sampai jam masuk + toleransi (default 15 menit) → hadir
     * - Absen setelah toleransi → terlambat
     */
    public function determine_status($check_in_time, $employee_id = 0, $date_str = null, $employee_uid = null, $grace_period_minutes = null)
    {
        if ($grace_period_minutes === null) {
            $grace_period_minutes = $this->get_grace_period_minutes();
        }

        $date_str = $date_str ?: date('Y-m-d');
        $ctx = $this->get_schedule_context($employee_id, $employee_uid, $date_str);

        $meta = [
            'status' => 'hadir',
            'schedule_shift' => $ctx['shift'],
            'schedule_start' => $ctx['schedule_start'],
            'schedule_end' => $ctx['schedule_end'],
            'shift_label' => $ctx['shift_label'],
            'reason' => '',
        ];

        if (!$ctx['has_schedule'] || !$ctx['schedule_start']) {
            $meta['reason'] = 'Jadwal shift belum diatur — fallback hadir';
            return $meta;
        }

        if (!$ctx['is_work_day']) {
            $meta['reason'] = 'Di luar hari kerja terjadwal — tetap hadir';
            return $meta;
        }

        if ($ctx['shift'] === 'off') {
            $meta['reason'] = 'Minggu libur rolling — tetap hadir';
            return $meta;
        }

        $check_in = DateTime::createFromFormat('H:i:s', $this->normalize_time($check_in_time));
        $start = DateTime::createFromFormat('H:i:s', $ctx['schedule_start']);
        if (!$check_in || !$start) {
            $meta['reason'] = 'Format waktu tidak valid — fallback hadir';
            return $meta;
        }

        $deadline = clone $start;
        $deadline->modify('+' . (int) $grace_period_minutes . ' minutes');

        if ($check_in <= $deadline) {
            $meta['status'] = 'hadir';
            $meta['reason'] = sprintf(
                'Masuk %s (jadwal %s + toleransi %d menit sampai %s)',
                $check_in->format('H:i'),
                $start->format('H:i'),
                $grace_period_minutes,
                $deadline->format('H:i')
            );
        } else {
            $meta['status'] = 'terlambat';
            $meta['reason'] = sprintf(
                'Masuk %s melewati batas %s (jadwal %s + %d menit)',
                $check_in->format('H:i'),
                $deadline->format('H:i'),
                $start->format('H:i'),
                $grace_period_minutes
            );
        }

        return $meta;
    }

    private function normalize_time($time_str)
    {
        $time_str = trim((string) $time_str);
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $time_str)) {
            return $time_str;
        }
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $time_str, $m)) {
            return sprintf('%02d:%02d:00', (int) $m[1], (int) $m[2]);
        }
        return date('H:i:s');
    }
}
