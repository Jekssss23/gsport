<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kpi_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->ensure_tables();
    }

    public function ensure_tables()
    {
        // 1. kpi_templates
        if (!$this->db->table_exists('kpi_templates')) {
            $this->db->query("CREATE TABLE `kpi_templates` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `division` varchar(50) NOT NULL,
              `field_key` varchar(100) NOT NULL,
              `field_label` varchar(200) NOT NULL,
              `field_type` enum('range','fixed') DEFAULT 'range',
              `min_value` decimal(5,2) DEFAULT 0.00,
              `max_value` decimal(5,2) DEFAULT 5.00,
              `fixed_value` decimal(5,2) DEFAULT 0.00,
              `is_active` tinyint(1) DEFAULT 1,
              `created_at` datetime DEFAULT current_timestamp(),
              `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

            // Insert defaults
            $this->db->query("INSERT INTO `kpi_templates` (`division`, `field_key`, `field_label`, `field_type`, `min_value`, `max_value`) VALUES
                ('Sports', 'kedisiplinan', 'Kedisiplinan', 'range', 1.00, 5.00),
                ('Sports', 'standarLayanan', 'Standar Layanan', 'range', 1.00, 2.00),
                ('Sports', 'kepatuhanSOP', 'Kepatuhan SOP', 'range', 1.00, 2.00),
                ('Sports', 'kualitasKerja', 'Kualitas Kerja', 'range', 1.00, 2.00),
                ('Sports', 'error', 'Error', 'range', 0.00, 1.00),
                ('GRO', 'kedisiplinan', 'Kedisiplinan', 'range', 1.00, 5.00),
                ('GRO', 'standarLayanan', 'Standar Layanan', 'range', 1.00, 2.00),
                ('GRO', 'kepatuhanSOP', 'Kepatuhan SOP', 'range', 1.00, 2.00),
                ('GRO', 'kualitasKerja', 'Kualitas Kerja', 'range', 1.00, 2.00),
                ('GRO', 'error', 'Error', 'range', 0.00, 1.00),
                ('HK', 'kedisiplinan', 'Kedisiplinan', 'range', 1.00, 5.00),
                ('HK', 'standarLayanan', 'Standar Layanan', 'range', 1.00, 2.00),
                ('HK', 'kepatuhanSOP', 'Kepatuhan SOP', 'range', 1.00, 2.00),
                ('HK', 'kualitasKerja', 'Kualitas Kerja', 'range', 1.00, 2.00),
                ('HK', 'error', 'Error', 'range', 0.00, 1.00),
                ('Caffe', 'kedisiplinan', 'Kedisiplinan', 'range', 1.00, 5.00),
                ('Caffe', 'standarLayanan', 'Standar Layanan', 'range', 1.00, 2.00),
                ('Caffe', 'kepatuhanSOP', 'Kepatuhan SOP', 'range', 1.00, 2.00),
                ('Caffe', 'kualitasKerja', 'Kualitas Kerja', 'range', 1.00, 2.00),
                ('Caffe', 'error', 'Error', 'range', 0.00, 1.00),
                ('Security', 'kedisiplinan', 'Kedisiplinan', 'range', 1.00, 5.00),
                ('Security', 'standarLayanan', 'Standar Layanan', 'range', 1.00, 2.00),
                ('Security', 'kepatuhanSOP', 'Kepatuhan SOP', 'range', 1.00, 2.00),
                ('Security', 'kualitasKerja', 'Kualitas Kerja', 'range', 1.00, 2.00),
                ('Security', 'error', 'Error', 'range', 0.00, 1.00),
                ('Maintenance', 'kedisiplinan', 'Kedisiplinan', 'range', 1.00, 5.00),
                ('Maintenance', 'standarLayanan', 'Standar Layanan', 'range', 1.00, 2.00),
                ('Maintenance', 'kepatuhanSOP', 'Kepatuhan SOP', 'range', 1.00, 2.00),
                ('Maintenance', 'kualitasKerja', 'Kualitas Kerja', 'range', 1.00, 2.00),
                ('Maintenance', 'error', 'Error', 'range', 0.00, 1.00)");
        }

        // 2. kpi_assessments
        if (!$this->db->table_exists('kpi_assessments')) {
            $this->db->query("CREATE TABLE `kpi_assessments` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `employee_id` int(11) NOT NULL,
              `employee_name` varchar(255) NOT NULL,
              `division` varchar(50) NOT NULL,
              `period` varchar(7) NOT NULL,
              `total_score` decimal(10,2) DEFAULT 0.00,
              `assessment_data` longtext DEFAULT NULL,
              `assessed_by` int(11) DEFAULT NULL,
              `assessment_notes` text DEFAULT NULL,
              `created_at` datetime DEFAULT current_timestamp(),
              `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              KEY `idx_kpi_employee_period` (`employee_id`,`period`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        }
    }

    // Get KPI templates by division
    public function get_kpi_templates($division = null)
    {
        if ($division) {
            return $this->db->get_where('kpi_templates', [
                'division' => $division,
                'is_active' => 1
            ])->result_array();
        }
        
        return $this->db->get_where('kpi_templates', ['is_active' => 1])->result_array();
    }

    // Get all divisions with KPI templates
    public function get_divisions()
    {
        $q = $this->db->query("
            SELECT DISTINCT division 
            FROM kpi_templates 
            WHERE is_active = 1 
            ORDER BY division ASC
        ")->result_array();
        
        return array_column($q, 'division');
    }

    // Save KPI assessment
    public function save_assessment($data)
    {
        $this->db->trans_start();
        
        $employee_id = $data['employee_id'];
        $period = $data['period'];
        
        // Check if assessment exists
        $existing = $this->db->get_where('kpi_assessments', [
            'employee_id' => $employee_id,
            'period' => $period
        ])->row_array();
        
        $assessment_data = [
            'employee_id' => $employee_id,
            'employee_name' => $data['employee_name'],
            'division' => $data['division'],
            'period' => $period,
            'total_score' => $data['total_score'],
            'assessment_data' => json_encode($data['scores']),
            'assessed_by' => $this->session->userdata('id_user'),
            'assessment_notes' => isset($data['notes']) ? $data['notes'] : null
        ];
        
        if ($existing) {
            // Update existing
            $this->db->where('id', $existing['id']);
            $this->db->update('kpi_assessments', $assessment_data);
            $assessment_id = $existing['id'];
        } else {
            // Insert new
            $this->db->insert('kpi_assessments', $assessment_data);
            $assessment_id = $this->db->insert_id();
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return [
                'success' => false,
                'message' => 'Failed to save assessment',
                'assessment_id' => null
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Assessment saved successfully',
            'assessment_id' => $assessment_id
        ];
    }

    // Get KPI assessments with filters
    public function get_assessments($period = null, $division = null, $search = null, $limit = 50, $offset = 0)
    {
        $this->db->select('ka.*, mu.nama as assessed_by_name');
        $this->db->from('kpi_assessments ka');
        $this->db->join('master_user mu', 'ka.assessed_by = mu.id_user', 'left');
        
        if ($period) {
            $this->db->where('ka.period', $period);
        }
        
        if ($division && $division !== 'All') {
            $this->db->where('ka.division', $division);
        }
        
        if ($search) {
            $this->db->like('ka.employee_name', $search);
        }
        
        $this->db->order_by('ka.updated_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
    }

    // Get assessment by employee and period
    public function get_assessment($employee_id, $period)
    {
        $assessment = $this->db->get_where('kpi_assessments', [
            'employee_id' => $employee_id,
            'period' => $period
        ])->row_array();
        
        if ($assessment && isset($assessment['assessment_data'])) {
            $assessment['scores'] = json_decode($assessment['assessment_data'], true);
        }
        
        return $assessment;
    }

    // Delete assessment
    public function delete_assessment($assessment_id)
    {
        $this->db->where('id', $assessment_id);
        $this->db->delete('kpi_assessments');
        
        return $this->db->affected_rows() > 0;
    }

    // Get KPI statistics
    public function get_kpi_stats($period = null)
    {
        $stats = [];
        
        if (!$period) {
            $period = date('Y-m');
        }
        
        // Average scores by division
        $q = $this->db->query("
            SELECT division, AVG(total_score) as avg_score, COUNT(*) as total_assessments
            FROM kpi_assessments 
            WHERE period = ?
            GROUP BY division
            ORDER BY avg_score DESC
        ", [$period])->result_array();
        
        $stats['division_performance'] = $q;
        
        // Top performers
        $q = $this->db->query("
            SELECT employee_name, division, total_score
            FROM kpi_assessments 
            WHERE period = ?
            ORDER BY total_score DESC 
            LIMIT 5
        ", [$period])->result_array();
        
        $stats['top_performers'] = $q;
        
        // Assessment completion rate
        $q = $this->db->query("
            SELECT 
                COUNT(DISTINCT ka.employee_id) as assessed_count,
                (SELECT COUNT(*) FROM employees WHERE status = 'active') as total_employees
            FROM kpi_assessments ka 
            WHERE ka.period = ?
        ", [$period])->row_array();
        
        $stats['completion_rate'] = $q;
        
        return $stats;
    }

    // Get employee KPI history
    public function get_employee_kpi_history($employee_id, $limit = 12)
    {
        return $this->db->query("
            SELECT period, total_score, division, updated_at
            FROM kpi_assessments 
            WHERE employee_id = ?
            ORDER BY period DESC
            LIMIT ?
        ", [$employee_id, $limit])->result_array();
    }
}
