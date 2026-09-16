<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cors {
    public function enable_cors() {
        // Allow any origin – you can restrict to your domain if needed
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        // If the request is an OPTIONS preflight, end request early
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    }
}
?>
