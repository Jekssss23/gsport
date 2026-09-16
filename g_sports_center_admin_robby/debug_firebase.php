<?php
// Mock HTTP Request environment for CodeIgniter 3
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTP_HOST'] = 'localhost';

define('ENVIRONMENT', 'development');

$system_path = 'system';
$application_folder = 'application';
$view_folder = '';

if (realpath($system_path) !== FALSE) {
    $system_path = realpath($system_path) . DIRECTORY_SEPARATOR;
}

$system_path = rtrim($system_path, '/\\') . DIRECTORY_SEPARATOR;
define('BASEPATH', $system_path);
define('FCPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('SYSDIR', basename(BASEPATH));
define('APPPATH', rtrim($application_folder, '/\\') . DIRECTORY_SEPARATOR);
define('VIEWPATH', APPPATH . 'views' . DIRECTORY_SEPARATOR);

require_once BASEPATH . 'core/CodeIgniter.php';

$CI =& get_instance();
$CI->load->library('firebase_admin');

echo "Service account: " . ($CI->firebase_admin->has_service_account() ? 'OK' : 'MISSING') . "\n";

$event_id = 'EV_TEST_' . time();
$payload = [
    'id' => $event_id,
    'name' => 'Debug Event',
    'image_url' => 'https://example.com/test.jpg',
    'start_date' => '2026-10-10',
    'end_date' => '2026-10-11',
    'start_time' => '10:00',
    'end_time' => '12:00',
    'status' => 'active',
    'created_by' => 'admin_debug',
    'created_at' => date('Y-m-d H:i:s'),
];

echo "Testing Firestore insert (events/{$event_id})...\n";
$ok = $CI->firebase_admin->create_firestore_document('events', $event_id, $payload);

if (Firebase_admin::is_write_success($ok)) {
    echo "SUCCESS\n";
} else {
    echo "FAILED\n";
    echo is_string($ok) ? $ok : $CI->firebase_admin->get_last_error();
    echo "\n";
}
