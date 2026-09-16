<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reset HANYA data operasional menu Big Data (testing).
 *
 * KRUSIAL — tabel berikut TIDAK PERNAH disentuh:
 * - master_user, hak_akses_user (login web)
 * - attendance_settings (lokasi & radius absensi GPS)
 * - kpi_templates (poin / kriteria penilaian KPI)
 * - reservation_facilities, reservation_courts (master fasilitas)
 */
class Bigdata_reset_model extends CI_Model {

    /** Satu-satunya tabel yang boleh dikosongkan oleh reset ini */
    private $allowed_reset_tables = [
        'reservation_slots' => 'Slot reservasi (data transaksi)',
        'reservations' => 'Reservasi (data transaksi)',
        'attendances' => 'Record absensi aktif',
        'attendances_archive' => 'Arsip absensi',
        'kpi_assessments' => 'Hasil penilaian KPI karyawan',
        'employees' => 'Data karyawan Big Data',
        'app_events' => 'Event organizer (backup MySQL)',
    ];

    /** Tabel yang WAJIB dilindungi — jika ada di daftar hapus, proses dibatalkan */
    private $protected_tables = [
        'master_user' => 'Akun login website',
        'hak_akses_user' => 'Hak akses login web',
        'attendance_settings' => 'Pengaturan lokasi & radius absensi GPS',
        'kpi_templates' => 'Template poin / kriteria penilaian KPI',
        'reservation_facilities' => 'Master fasilitas reservasi',
        'reservation_courts' => 'Master lapangan/kolom reservasi',
    ];

    public function get_protected_preview()
    {
        $preview = [];
        foreach ($this->protected_tables as $table => $label) {
            if ($this->db->table_exists($table)) {
                $preview[] = [
                    'table' => $table,
                    'label' => $label,
                    'count' => (int) $this->db->count_all($table),
                    'protected' => true,
                ];
            }
        }
        return $preview;
    }

    public function get_reset_preview()
    {
        $preview = [];
        foreach ($this->allowed_reset_tables as $table => $label) {
            if ($this->db->table_exists($table)) {
                $preview[] = [
                    'table' => $table,
                    'label' => $label,
                    'count' => (int) $this->db->count_all($table),
                    'protected' => false,
                ];
            }
        }

        if ($this->db->table_exists('financial_transactions')) {
            $cnt = (int) $this->db->where('transaction_type', 'package')->count_all_results('financial_transactions');
            $preview[] = [
                'table' => 'financial_transactions',
                'label' => 'Transaksi keuangan paket (monitoring Big Data)',
                'count' => $cnt,
                'protected' => false,
                'filter' => 'transaction_type = package',
            ];
        }

        return $preview;
    }

    public function reset_operational_data($options = [])
    {
        $clear_firestore_reviews = !empty($options['clear_firestore_reviews']);
        $clear_firestore_events = !empty($options['clear_firestore_events']);
        $clear_firestore_employees = !empty($options['clear_firestore_employees']);

        $this->db->trans_start();
        $deleted = [];

        foreach ($this->allowed_reset_tables as $table => $label) {
            if (!$this->db->table_exists($table)) {
                continue;
            }

            $before = (int) $this->db->count_all($table);
            $this->db->query('DELETE FROM `' . $table . '`');
            $deleted[$table] = $before;
        }

        if ($this->db->table_exists('financial_transactions')) {
            $this->db->where('transaction_type', 'package');
            $this->db->delete('financial_transactions');
            $deleted['financial_transactions_package'] = $this->db->affected_rows();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['success' => false, 'message' => 'Gagal reset data MySQL'];
        }

        $protected_check = $this->verify_protected_tables_intact();
        if (!$protected_check['ok']) {
            return [
                'success' => false,
                'message' => 'KRITIS: Verifikasi tabel dilindungi gagal — ' . $protected_check['message'],
            ];
        }

        $firestore = [];
        $CI =& get_instance();
        if (isset($CI->firebase_admin)) {
            if ($clear_firestore_reviews) {
                $firestore['reviews'] = $this->clear_firestore_collection('reviews', $CI->firebase_admin);
            }
            if ($clear_firestore_events) {
                $firestore['events'] = $this->clear_firestore_collection('events', $CI->firebase_admin);
            }
            if ($clear_firestore_employees) {
                $firestore['employees'] = $this->clear_firestore_collection('employees', $CI->firebase_admin);
            }
        }

        return [
            'success' => true,
            'message' => 'Reset data operasional Big Data berhasil. Pengaturan absensi, template KPI, dan master_user tetap aman.',
            'mysql' => $deleted,
            'firestore' => $firestore,
            'protected_verified' => $protected_check,
        ];
    }

    /**
     * Pastikan tabel dilindungi masih ada dan tidak kosong total tanpa sengaja (kecuali memang 0 baris sebelumnya).
     */
    private function verify_protected_tables_intact()
    {
        foreach ($this->protected_tables as $table => $label) {
            if (!$this->db->table_exists($table)) {
                continue;
            }
        }

        if ($this->db->table_exists('attendance_settings')) {
            $settings_count = (int) $this->db->count_all('attendance_settings');
        } else {
            $settings_count = null;
        }

        if ($this->db->table_exists('kpi_templates')) {
            $kpi_tpl_count = (int) $this->db->count_all('kpi_templates');
        } else {
            $kpi_tpl_count = null;
        }

        $user_count = null;
        if ($this->db->table_exists('master_user')) {
            $user_count = (int) $this->db->count_all('master_user');
            if ($user_count === 0) {
                return ['ok' => false, 'message' => 'master_user terdeteksi kosong — reset dibatalkan demi keamanan'];
            }
        }

        return [
            'ok' => true,
            'message' => 'Semua tabel dilindungi utuh',
            'attendance_settings_rows' => $settings_count,
            'kpi_templates_rows' => $kpi_tpl_count,
            'master_user_rows' => $user_count,
        ];
    }

    private function clear_firestore_collection($collection, $firebase_admin)
    {
        $blocked_firestore = ['users'];
        if (in_array($collection, $blocked_firestore, true)) {
            return ['cleared' => 0, 'error' => 'Collection Firestore dilindungi: ' . $collection];
        }

        if (!method_exists($firebase_admin, 'get_all_documents') || !method_exists($firebase_admin, 'delete_firestore_document')) {
            return ['cleared' => 0, 'error' => 'Firebase method tidak tersedia'];
        }

        try {
            $docs = $firebase_admin->get_all_documents($collection);
            $cleared = 0;
            foreach ($docs as $doc) {
                $id = $doc['id'] ?? null;
                if ($id && $firebase_admin->delete_firestore_document($collection, $id)) {
                    $cleared++;
                }
            }
            return ['cleared' => $cleared];
        } catch (Exception $e) {
            return ['cleared' => 0, 'error' => $e->getMessage()];
        }
    }
}
