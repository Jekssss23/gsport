<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Karyawan operasional Big Data — terpisah dari master_user (login web).
 */
class Employee_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->ensure_schema();
    }

    public function ensure_schema()
    {
        if (!$this->db->table_exists('employees')) {
            $this->db->query("
                CREATE TABLE `employees` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `nama` varchar(255) NOT NULL,
                  `email` varchar(255) DEFAULT NULL,
                  `nohp` varchar(25) DEFAULT NULL,
                  `alamat` text DEFAULT NULL,
                  `jabatan` varchar(100) NOT NULL DEFAULT '',
                  `foto` text DEFAULT NULL,
                  `jadwal_operasional` text DEFAULT NULL,
                  `firebase_uid` varchar(255) DEFAULT NULL,
                  `app_username` varchar(255) DEFAULT NULL,
                  `has_app_login` tinyint(1) NOT NULL DEFAULT 0,
                  `status` varchar(20) NOT NULL DEFAULT 'active',
                  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_employees_firebase` (`firebase_uid`),
                  KEY `idx_employees_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }

        // No migration from master_user as requested
    }

    /**
     * Salin karyawan Big Data dari master_user (sekali, saat tabel baru dibuat).
     */
    public function migrate_from_master_user()
    {
        if (!$this->db->table_exists('master_user')) {
            return 0;
        }

        $this->db->query("
            INSERT IGNORE INTO employees (
                id, nama, email, nohp, alamat, jabatan, foto, jadwal_operasional,
                firebase_uid, app_username, has_app_login, status, created_at, updated_at
            )
            SELECT
                id_user,
                nama,
                NULLIF(email, '-'),
                NULLIF(nohp, '-'),
                alamat,
                jabatan,
                NULLIF(foto, ''),
                jadwal_operasional,
                firebase_uid,
                username,
                CASE WHEN firebase_uid IS NOT NULL AND TRIM(firebase_uid) <> '' THEN 1 ELSE 0 END,
                CASE WHEN status = 'delete' THEN 'deleted' ELSE 'active' END,
                NOW(),
                NOW()
            FROM master_user
            WHERE (
                (jadwal_operasional IS NOT NULL AND TRIM(jadwal_operasional) <> '' AND TRIM(jadwal_operasional) <> '{}')
                OR (firebase_uid IS NOT NULL AND TRIM(firebase_uid) <> '')
            )
        ");

        return $this->db->affected_rows();
    }

    /**
     * Pastikan AUTO_INCREMENT employees di atas ID terbesar (setelah migrasi id_user).
     */
    public function sync_employee_auto_increment()
    {
        if (!$this->db->table_exists('employees')) {
            return;
        }

        $row = $this->db->query('SELECT COALESCE(MAX(id), 0) AS max_id FROM employees')->row_array();
        $next = ((int) ($row['max_id'] ?? 0)) + 1;
        $this->db->query('ALTER TABLE `employees` AUTO_INCREMENT = ' . (int) $next);
    }

    /**
     * Hapus FK employee_id lama (master_user) lalu pasang ke employees.
     */
    public function update_employee_foreign_keys()
    {
        if (!$this->db->table_exists('employees')) {
            return;
        }

        $tables = [
            'attendances' => 'attendances_employee_fk',
            'kpi_assessments' => 'kpi_assessments_employee_fk',
        ];

        foreach ($tables as $table => $target_fk) {
            if (!$this->db->table_exists($table)) {
                continue;
            }

            $this->drop_foreign_keys_on_column($table, 'employee_id', 'employees');
            $this->cleanup_orphan_employee_refs($table);

            $has_fk = $this->db->query("
                SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND CONSTRAINT_NAME = ?
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ", [$table, $target_fk])->row_array();

            if (!$has_fk) {
                $this->db->query("
                    ALTER TABLE `{$table}`
                    ADD CONSTRAINT `{$target_fk}`
                    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
                ");
            }
        }
    }

    private function drop_foreign_keys_on_column($table, $column, $keep_referenced_table = null)
    {
        $fks = $this->db->query("
            SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table, $column])->result_array();

        foreach ($fks as $fk) {
            if ($keep_referenced_table && $fk['REFERENCED_TABLE_NAME'] === $keep_referenced_table) {
                continue;
            }
            $name = $fk['CONSTRAINT_NAME'];
            $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
        }
    }

    /**
     * Hapus baris dengan employee_id yang tidak ada di employees (sebelum FK dipasang).
     */
    private function cleanup_orphan_employee_refs($table)
    {
        if (!$this->db->table_exists($table) || !$this->db->table_exists('employees')) {
            return;
        }

        $this->db->query("
            DELETE t FROM `{$table}` t
            LEFT JOIN employees e ON e.id = t.employee_id
            WHERE e.id IS NULL
        ");
    }

    public function get_all_active()
    {
        $this->ensure_schema();
        return $this->db
            ->where('status', 'active')
            ->order_by('id', 'DESC')
            ->get('employees')
            ->result_array();
    }

    public function get_by_id($id)
    {
        $this->ensure_schema();
        return $this->db->get_where('employees', ['id' => (int) $id])->row_array();
    }

    public function get_by_firebase_uid($uid)
    {
        $this->ensure_schema();
        if (!$uid) {
            return null;
        }
        return $this->db->get_where('employees', ['firebase_uid' => $uid, 'status' => 'active'])->row_array();
    }

    public function resolve_id($employee_id, $firebase_uid = null)
    {
        if (is_numeric($employee_id) && (int) $employee_id > 0) {
            return (int) $employee_id;
        }
        if ($firebase_uid) {
            $row = $this->get_by_firebase_uid($firebase_uid);
            if ($row) {
                return (int) $row['id'];
            }
        }
        return 0;
    }

    public function count_active()
    {
        $this->ensure_schema();
        return (int) $this->db->where('status', 'active')->count_all_results('employees');
    }

    public function save($data, $id = null)
    {
        $this->ensure_schema();
        
        $row = [
            'nama' => $data['nama'],
            'email' => $data['email'] ?? null,
            'nohp' => $data['nohp'] ?? null,
            'alamat' => $data['alamat'] ?? '',
            'jabatan' => $data['jabatan'] ?? '',
            'foto' => $data['foto'] ?? null,
            'jadwal_operasional' => $data['jadwal_operasional'] ?? null,
            'firebase_uid' => $data['firebase_uid'] ?? null,
            'app_username' => $data['app_username'] ?? null,
            'has_app_login' => !empty($data['has_app_login']) ? 1 : 0,
            'status' => $data['status'] ?? 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            $this->db->where('id', (int) $id);
            $this->db->update('employees', $row);
            return (int) $id;
        }

        $row['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('employees', $row);
        return (int) $this->db->insert_id();
    }

    public function hard_delete($id)
    {
        $this->ensure_schema();
        $this->db->where('id', (int) $id);
        $this->db->delete('employees');
        return $this->db->affected_rows() > 0;
    }
}
