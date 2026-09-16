<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gsc_package extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('firebase_admin');

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

    public function get_package() {
        $id = $this->input->get('id');
        if (!$id) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'message' => 'ID paket diperlukan']));
            return;
        }

        $package = $this->firebase_admin->get_firestore_document('gsc_packages', $id);
        if ($package) {
            $package['id'] = $id;
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $package]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'message' => 'Paket tidak ditemukan']));
        }
    }

    public function get_all_packages() {
        $packages = $this->firebase_admin->get_all_documents('gsc_packages');
        // Filter out those without valid data if needed
        $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $packages]));
    }

    public function deduct_hours() {
        $package_id = $this->input->post('package_id');
        $hours_to_deduct = (int)$this->input->post('hours');
        $admin_id = $this->input->post('admin_id');
        $admin_name = $this->input->post('admin_name');

        if (!$package_id || $hours_to_deduct <= 0) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'message' => 'Data tidak valid']));
            return;
        }

        $package = $this->firebase_admin->get_firestore_document('gsc_packages', $package_id);
        if (!$package) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'message' => 'Paket tidak ditemukan']));
            return;
        }

        $current_remaining = isset($package['remainingHours']) ? (int)$package['remainingHours'] : 0;
        if ($hours_to_deduct > $current_remaining) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'message' => 'Jam tidak mencukupi']));
            return;
        }

        $new_remaining = $current_remaining - $hours_to_deduct;
        $status = ($new_remaining <= 0) ? 'completed' : 'active';

        $update_data = [
            'remainingHours' => $new_remaining,
            'status' => $status,
            'updatedAt' => date('c')
        ];

        $success = $this->firebase_admin->create_firestore_document('gsc_packages', $package_id, $update_data);

        if ($success === true) {
            // Log usage to firestore
            $usage_id = uniqid('usage_');
            $usage_data = [
                'packageId' => $package_id,
                'userId' => $package['userId'] ?? '',
                'userName' => $package['userName'] ?? '',
                'sportType' => $package['sportType'] ?? '',
                'hoursDeducted' => $hours_to_deduct,
                'remainingHours' => $new_remaining,
                'deductedBy' => $admin_id,
                'deductedByName' => $admin_name,
                'timestamp' => date('c')
            ];
            $this->firebase_admin->create_firestore_document('gsc_package_usage', $usage_id, $usage_data);

            // Log to MySQL financial transactions for monitoring (Amount 0, type: package_usage)
            $this->db->insert('financial_transactions', [
                'firebase_uid' => $package['userId'] ?? '',
                'user_email' => $package['userName'] ?? 'Member',
                'amount' => 0,
                'transaction_type' => 'package_usage',
                'description' => "Paket " . ($package['sportType'] ?? '') . " digunakan {$hours_to_deduct} jam oleh {$admin_name}",
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'message' => 'Berhasil memotong jam']));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false, 'message' => 'Gagal update paket: ' . $success]));
        }
    }
}
