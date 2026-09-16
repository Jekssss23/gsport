<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('firebase_admin');
        $this->load->model('Event_model', 'event_model');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    }

    private function response($data, $status = 200) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    private function merge_active_events($firebase_rows, $mysql_rows)
    {
        $by_id = [];

        foreach ($mysql_rows as $row) {
            if (!empty($row['id'])) {
                $by_id[$row['id']] = $row;
            }
        }

        foreach ($firebase_rows as $row) {
            if (!empty($row['id'])) {
                $by_id[$row['id']] = $row;
            }
        }

        $active = array_values(array_filter($by_id, function ($r) {
            return !isset($r['status']) || $r['status'] === 'active';
        }));

        usort($active, function ($a, $b) {
            $aTime = strtotime(($a['start_date'] ?? '') . ' ' . ($a['start_time'] ?? '00:00'));
            $bTime = strtotime(($b['start_date'] ?? '') . ' ' . ($b['start_time'] ?? '00:00'));
            return $aTime <=> $bTime;
        });

        return $active;
    }

    public function list() {
        try {
            $firebase_rows = [];
            if ($this->firebase_admin->has_service_account()) {
                $firebase_rows = $this->firebase_admin->get_all_documents('events');
            }

            $mysql_rows = $this->event_model->get_all_active();
            $data = $this->merge_active_events($firebase_rows, $mysql_rows);

            $this->response(['ok' => true, 'data' => $data]);
        } catch (Exception $e) {
            $this->response(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
