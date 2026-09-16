<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('firebase_admin');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);
    }

    private function response($data, $status = 200) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public function my() {
        $user_id = $this->input->get('user_id');
        if (!$user_id) {
            $this->response(['ok' => false, 'message' => 'user_id is required'], 400);
        }
        try {
            $rows = $this->firebase_admin->query_documents_by_field('notifications', 'user_id', $user_id);
            usort($rows, function($a, $b) {
                return strtotime($b['created_at'] ?? '1970-01-01') <=> strtotime($a['created_at'] ?? '1970-01-01');
            });
            $this->response(['ok' => true, 'data' => $rows]);
        } catch (Exception $e) {
            $this->response(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

