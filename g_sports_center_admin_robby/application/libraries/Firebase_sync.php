<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Firebase_sync {
    
    private $CI;
    private $firebase_config;
    private $project_id;
    
    public function __construct() {
        $this->CI =& get_instance();
        
        // Firebase config - sesuai dengan sistem lama
        $this->firebase_config = [
            'project_id' => 'gsports-57a81',
            'api_key' => 'AIzaSyCC3Z0GmvFyafz3t177_b_WrGPbGuqtGQw',
            'auth_domain' => 'gsports-57a81.firebaseapp.com',
            'database_url' => 'https://gsports-57a81-default-rtdb.firebaseio.com', // Jika pakai Realtime Database
            'storage_bucket' => 'gsports-57a81.firebasestorage.app'
        ];
        
        $this->project_id = $this->firebase_config['project_id'];
    }
    
    /**
     * Get reservations from Firebase Firestore
     */
    public function get_firebase_reservations() {
        // Karena tidak ada Firebase Admin SDK untuk PHP yang mudah, 
        // kita akan menggunakan REST API
        $url = "https://firestore.googleapis.com/v1/projects/{$this->project_id}/databases/(default)/documents/reservations";
        
        $headers = [
            'Content-Type: application/json'
        ];
        
        $response = $this->make_request($url, 'GET', null, $headers);
        
        if ($response && isset($response['documents'])) {
            return $this->format_firestore_data($response['documents']);
        }
        
        return [];
    }
    
    /**
     * Get reservations from Firebase Realtime Database
     */
    public function get_realtime_reservations() {
        $url = "https://{$this->project_id}-default-rtdb.firebaseio.com/reservations.json";
        
        $response = $this->make_request($url);
        
        return $response ? $response : [];
    }
    
    /**
     * Sync Firebase reservations to MySQL
     */
    public function sync_reservations() {
        // Coba dulu dari Firestore
        $firebase_reservations = $this->get_firebase_reservations();
        
        // Jika kosong, coba dari Realtime Database
        if (empty($firebase_reservations)) {
            $firebase_reservations = $this->get_realtime_reservations();
        }
        
        $synced_count = 0;
        $errors = [];
        
        foreach ($firebase_reservations as $reservation) {
            try {
                $result = $this->sync_single_reservation($reservation);
                if ($result['success']) {
                    $synced_count++;
                } else {
                    $errors[] = $result['error'];
                }
            } catch (Exception $e) {
                $errors[] = "Error syncing reservation: " . $e->getMessage();
            }
        }
        
        return [
            'synced_count' => $synced_count,
            'errors' => $errors,
            'total_processed' => count($firebase_reservations)
        ];
    }
    
    /**
     * Sync single reservation to MySQL
     */
    private function sync_single_reservation($firebase_data) {
        // Extract data dari Firebase
        $reservation_data = $this->extract_reservation_data($firebase_data);
        
        // Cek apakah sudah ada di MySQL
        $existing = $this->CI->db->get_where('reservations', [
            'firebase_reservation_id' => $reservation_data['firebase_reservation_id']
        ])->row();
        
        if ($existing) {
            // Update jika sudah ada
            $this->CI->db->where('id', $existing->id);
            $this->CI->db->update('reservations', $reservation_data);
            
            return ['success' => true, 'action' => 'updated'];
        } else {
            // Insert baru
            $this->CI->db->insert('reservations', $reservation_data);
            $reservation_id = $this->CI->db->insert_id();
            
            // Insert time slots
            if (isset($firebase_data['time_slots']) && is_array($firebase_data['time_slots'])) {
                foreach ($firebase_data['time_slots'] as $slot) {
                    $this->CI->db->insert('reservation_slots', [
                        'reservation_id' => $reservation_id,
                        'court_id' => $reservation_data['court_id'],
                        'booking_date' => $reservation_data['booking_date'],
                        'hour' => $slot
                    ]);
                }
            }
            
            return ['success' => true, 'action' => 'inserted'];
        }
    }
    
    /**
     * Extract and format reservation data from Firebase
     */
    private function extract_reservation_data($firebase_data) {
        // Handle Firestore format
        if (isset($firebase_data['fields'])) {
            return $this->extract_from_firestore($firebase_data);
        }
        
        // Handle Realtime Database format
        return $this->extract_from_realtime($firebase_data);
    }
    
    /**
     * Extract data from Firestore format
     */
    private function extract_from_firestore($data) {
        $fields = $data['fields'];
        
        return [
            'firebase_reservation_id' => $data['name'] ?? '',
            'reservation_code' => $this->get_field_value($fields, 'reservation_code', 'stringValue') ?? 'FB' . date('YmdHis'),
            'firebase_uid' => $this->get_field_value($fields, 'firebase_uid', 'stringValue') ?? '',
            'user_name' => $this->get_field_value($fields, 'user_name', 'stringValue') ?? '',
            'user_phone' => $this->get_field_value($fields, 'user_phone', 'stringValue') ?? '',
            'facility_id' => (int) $this->get_field_value($fields, 'facility_id', 'integerValue') ?? 0,
            'court_id' => (int) $this->get_field_value($fields, 'court_id', 'integerValue') ?? 0,
            'booking_date' => $this->get_field_value($fields, 'booking_date', 'stringValue') ?? date('Y-m-d'),
            'status' => 'pending', // Default status untuk data dari Firebase
            'payment_proof_url' => $this->get_field_value($fields, 'payment_proof_url', 'stringValue') ?? '',
            'total_amount' => (int) $this->get_field_value($fields, 'total_amount', 'integerValue') ?? 0,
            'dp_amount' => (int) $this->get_field_value($fields, 'dp_amount', 'integerValue') ?? 0,
            'remaining_amount' => (int) $this->get_field_value($fields, 'remaining_amount', 'integerValue') ?? 0,
            'created_at' => $this->get_field_value($fields, 'created_at', 'stringValue') ?? date('Y-m-d H:i:s'),
            'firebase_synced' => 1,
            'firebase_synced_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Extract data from Realtime Database format
     */
    private function extract_from_realtime($data) {
        return [
            'firebase_reservation_id' => $data['id'] ?? '',
            'reservation_code' => $data['reservation_code'] ?? 'FB' . date('YmdHis'),
            'firebase_uid' => $data['firebase_uid'] ?? '',
            'user_name' => $data['user_name'] ?? '',
            'user_phone' => $data['user_phone'] ?? '',
            'facility_id' => (int) ($data['facility_id'] ?? 0),
            'court_id' => (int) ($data['court_id'] ?? 0),
            'booking_date' => $data['booking_date'] ?? date('Y-m-d'),
            'status' => 'pending',
            'payment_proof_url' => $data['payment_proof_url'] ?? '',
            'total_amount' => (int) ($data['total_amount'] ?? 0),
            'dp_amount' => (int) ($data['dp_amount'] ?? 0),
            'remaining_amount' => (int) ($data['remaining_amount'] ?? 0),
            'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
            'firebase_synced' => 1,
            'firebase_synced_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Get field value from Firestore fields
     */
    private function get_field_value($fields, $field_name, $type) {
        if (isset($fields[$field_name]) && isset($fields[$field_name][$type])) {
            return $fields[$field_name][$type];
        }
        return null;
    }
    
    /**
     * Format Firestore documents to array
     */
    private function format_firestore_data($documents) {
        $formatted = [];
        foreach ($documents as $doc) {
            $formatted[] = $doc;
        }
        return $formatted;
    }
    
    /**
     * Make HTTP request to Firebase REST API
     */
    private function make_request($url, $method = 'GET', $data = null, $headers = []) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($data && ($method == 'POST' || $method == 'PUT')) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            log_message('error', 'Firebase API Error: ' . $error);
            return null;
        }
        
        if ($http_code >= 200 && $http_code < 300) {
            return json_decode($response, true);
        }
        
        log_message('error', 'Firebase API HTTP Error: ' . $http_code . ' - ' . $response);
        return null;
    }
}
