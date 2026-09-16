<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MySQL backup / mirror for mobile app events (Firestore remains primary for push).
 */
class Event_model extends CI_Model {

    public function ensure_table()
    {
        if ($this->db->table_exists('app_events')) {
            return true;
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `app_events` (
            `id` varchar(64) NOT NULL,
            `name` varchar(255) NOT NULL,
            `image_url` text DEFAULT NULL,
            `start_date` date NOT NULL,
            `end_date` date NOT NULL,
            `start_time` varchar(8) NOT NULL DEFAULT '00:00',
            `end_time` varchar(8) NOT NULL DEFAULT '00:00',
            `status` varchar(20) NOT NULL DEFAULT 'active',
            `created_by` varchar(64) DEFAULT NULL,
            `created_at` datetime DEFAULT NULL,
            `synced_firebase` tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `idx_app_events_status` (`status`),
            KEY `idx_app_events_dates` (`start_date`, `end_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        return $this->db->table_exists('app_events');
    }

    public function upsert($row)
    {
        $this->ensure_table();

        $id = (string) ($row['id'] ?? '');
        if ($id === '') {
            return false;
        }

        $data = [
            'id' => $id,
            'name' => (string) ($row['name'] ?? ''),
            'image_url' => (string) ($row['image_url'] ?? ''),
            'start_date' => (string) ($row['start_date'] ?? ''),
            'end_date' => (string) ($row['end_date'] ?? ''),
            'start_time' => (string) ($row['start_time'] ?? '00:00'),
            'end_time' => (string) ($row['end_time'] ?? '00:00'),
            'status' => (string) ($row['status'] ?? 'active'),
            'created_by' => (string) ($row['created_by'] ?? ''),
            'created_at' => (string) ($row['created_at'] ?? date('Y-m-d H:i:s')),
            'synced_firebase' => !empty($row['synced_firebase']) ? 1 : 0,
        ];

        $existing = $this->db->get_where('app_events', ['id' => $id])->row_array();
        if ($existing) {
            $this->db->where('id', $id)->update('app_events', $data);
        } else {
            $this->db->insert('app_events', $data);
        }

        return true;
    }

    public function mark_synced($id)
    {
        $this->ensure_table();
        $this->db->where('id', (string) $id)->update('app_events', ['synced_firebase' => 1]);
    }

    public function get_all_active()
    {
        $this->ensure_table();
        $rows = $this->db
            ->where('status', 'active')
            ->order_by('start_date', 'DESC')
            ->order_by('start_time', 'DESC')
            ->get('app_events')
            ->result_array();

        return array_map(function ($r) {
            return [
                'id' => $r['id'],
                'name' => $r['name'],
                'image_url' => $r['image_url'],
                'start_date' => $r['start_date'],
                'end_date' => $r['end_date'],
                'start_time' => substr((string) $r['start_time'], 0, 5),
                'end_time' => substr((string) $r['end_time'], 0, 5),
                'status' => $r['status'],
                'created_by' => $r['created_by'],
                'created_at' => $r['created_at'],
            ];
        }, $rows);
    }

    public function delete_by_id($id)
    {
        $this->ensure_table();
        $this->db->where('id', (string) $id)->delete('app_events');
        return $this->db->affected_rows() > 0;
    }

    public function get_by_id($id)
    {
        $this->ensure_table();
        $row = $this->db->get_where('app_events', ['id' => (string) $id])->row_array();
        return $row ?: null;
    }
}
