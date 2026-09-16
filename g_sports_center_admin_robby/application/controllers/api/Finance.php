<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Handle JSON POST requests for mobile app
        $json_data = json_decode(file_get_contents('php://input'), true);
        if(!empty($json_data)) {
            $_POST = array_merge($_POST, $json_data);
        }

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }
    }

    public function record_transaction()
    {
        $firebase_uid = $this->input->post('firebase_uid');
        $user_email = $this->input->post('user_email');
        $amount = $this->input->post('amount');
        $transaction_type = $this->input->post('transaction_type');
        $description = $this->input->post('description');

        if (!$firebase_uid || !$amount || !$transaction_type) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Missing required fields'
                ]));
            return;
        }

        $data = [
            'firebase_uid' => $firebase_uid,
            'user_email' => $user_email,
            'amount' => $amount,
            'transaction_type' => $transaction_type,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('financial_transactions', $data);

        if ($this->db->affected_rows() > 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Transaction recorded successfully'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Failed to record transaction'
                ]));
        }
    }
}
