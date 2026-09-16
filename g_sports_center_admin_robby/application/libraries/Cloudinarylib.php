<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cloudinarylib {

    private $cloud_name;
    private $upload_preset;
    private $api_url;

    public function __construct()
    {
        // Cloudinary configuration
        $this->cloud_name = 'dzlyiowkh'; 
        $this->upload_preset = 'gsc_employees';
        $this->api_url = 'https://api.cloudinary.com/v1_1/' . $this->cloud_name . '/image/upload';
    }

    public function upload($file_path, $folder = 'gsc/employees')
    {
        try {
            if (!file_exists($file_path)) {
                return false;
            }

            // Using unsigned upload with upload_preset
            if (function_exists('curl_file_create')) {
                $curlFile = curl_file_create($file_path);
            } elseif (class_exists('CURLFile')) {
                $curlFile = new CURLFile($file_path);
            } else {
                $curlFile = '@' . $file_path;
            }

            $data = [
                'file' => $curlFile,
                'upload_preset' => $this->upload_preset,
                'folder' => $folder
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (!function_exists('curl_file_create') && !class_exists('CURLFile')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $result = json_decode($response, true);
                if (isset($result['secure_url'])) {
                    return [
                        'secure_url' => $result['secure_url'],
                        'public_id' => $result['public_id']
                    ];
                }
            }

            log_message('error', 'Cloudinary upload failed: ' . $response);
            return false;
        } catch (Exception $e) {
            log_message('error', 'Cloudinary upload error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete($public_id)
    {
        try {
            $data = [
                'public_id' => $public_id,
                'resource_type' => 'image'
            ];

            $timestamp = time();
            $data['timestamp'] = $timestamp;
            $data['signature'] = $this->generate_signature($data);

            $delete_url = 'https://api.cloudinary.com/v1_1/' . $this->cloud_name . '/image/destroy';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $delete_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $http_code === 200;
        } catch (Exception $e) {
            log_message('error', 'Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }

    public function get_public_id_from_url($url)
    {
        try {
            // Extract public_id from Cloudinary URL
            // Example: https://res.cloudinary.com/cloud_name/image/upload/v1234567890/folder/public_id.jpg
            $pattern = '/\/upload\/v\d+\/(.+)\.\w+$/';
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error extracting public_id from URL: ' . $e->getMessage());
            return false;
        }
    }

    private function generate_signature($params)
    {
        // Remove signature from params if exists
        unset($params['signature']);
        
        // Sort parameters alphabetically
        ksort($params);
        
        // Create query string
        $query_string = '';
        foreach ($params as $key => $value) {
            $query_string .= $key . '=' . $value . '&';
        }
        $query_string = rtrim($query_string, '&');
        
        // Generate signature
        return sha1($query_string . $this->api_secret);
    }

    public function get_config()
    {
        return [
            'cloud_name' => $this->cloud_name,
            'api_key' => $this->api_key,
            'api_url' => $this->api_url
        ];
    }

    public function update_config($cloud_name, $api_key, $api_secret)
    {
        $this->cloud_name = $cloud_name;
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->api_url = 'https://api.cloudinary.com/v1_1/' . $this->cloud_name . '/image/upload';
    }
}
