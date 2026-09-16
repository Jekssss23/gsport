<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('firebase_admin');
        
        // Set CORS headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }
    }

    public function get_email_by_phone() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        $phone = $this->input->get('phone');
        if (!$phone) {
            echo json_encode(['ok' => false, 'message' => 'Phone required']);
            return;
        }

        // Search by new phoneNumber field
        $docs = $this->firebase_admin->query_documents_by_field('users', 'phoneNumber', $phone);
        if (!empty($docs)) {
            $email = $docs[0]['email'] ?? '';
            echo json_encode(['ok' => true, 'email' => $email]);
            return;
        }

        // Fallback to legacy phone field
        $docs = $this->firebase_admin->query_documents_by_field('users', 'phone', $phone);
        if (!empty($docs)) {
            $email = $docs[0]['email'] ?? '';
            echo json_encode(['ok' => true, 'email' => $email]);
            return;
        }

        echo json_encode(['ok' => false, 'message' => 'Not found']);
    }
}
