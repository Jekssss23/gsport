<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load Composer autoloader if present
if (file_exists(FCPATH . 'vendor/autoload.php')) {
    require_once FCPATH . 'vendor/autoload.php';
}

// Fallback: manually require JWT classes if not loaded by autoloader
if (!class_exists('Firebase\JWT\JWT')) {
    $jwt_path = FCPATH . 'vendor/firebase/php-jwt/src/JWT.php';
    $key_path = FCPATH . 'vendor/firebase/php-jwt/src/Key.php';
    
    if (file_exists($jwt_path)) {
        require_once $jwt_path;
        require_once $key_path;
    } else {
        // Try APPPATH fallback
        $jwt_path = APPPATH . '../vendor/firebase/php-jwt/src/JWT.php';
        $key_path = APPPATH . '../vendor/firebase/php-jwt/src/Key.php';
        if (file_exists($jwt_path)) {
            require_once $jwt_path;
            require_once $key_path;
        }
    }
}

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Firebase_admin {

    private $project_id;
    private $client_email;
    private $private_key;
    private $access_token;
    private $last_error = '';
    private $credentials_path = '';

    private $api_key;

    public function __construct()
    {
        // API Key from mobile config
        $this->api_key = 'AIzaSyAVwhIU5U3JFNB_TfQDjcKYoZyBPLxHaCM';
        $this->load_service_account();
    }

    private function log($level, $message)
    {
        if (function_exists('log_message')) {
            log_message($level, $message);
        }
    }

    /**
     * Resolve service account JSON (hosting often only uploads application/config/).
     */
    private function load_service_account()
    {
        $candidates = [
            APPPATH . 'config/firebase_credentials.json',
            FCPATH . 'admin-panel-gsports-main/g-sports-center-firebase-adminsdk-fbsvc-37f4a3bb30.json',
            FCPATH . 'g-sports-center-firebase-adminsdk-fbsvc-37f4a3bb30.json',
        ];

        foreach ($candidates as $sa_path) {
            if (!file_exists($sa_path)) {
                continue;
            }

            $sa = json_decode(file_get_contents($sa_path), true);
            if (!is_array($sa) || empty($sa['project_id']) || empty($sa['client_email']) || empty($sa['private_key'])) {
                $this->log('error', 'Firebase: Invalid service account JSON at ' . $sa_path);
                continue;
            }

            $this->credentials_path = $sa_path;
            $this->project_id = trim($sa['project_id']);
            $this->client_email = trim($sa['client_email']);
            $this->private_key = str_replace('\n', "\n", $sa['private_key']);
            $this->log('info', 'Firebase Service Account loaded from ' . $sa_path);
            return;
        }

        $this->log('error', 'Firebase Service Account JSON not found. Checked: ' . implode(', ', $candidates));
        $this->project_id = 'g-sports-center';
        $this->client_email = null;
        $this->private_key = null;
    }

    public function get_last_error()
    {
        return $this->last_error;
    }

    public function has_service_account()
    {
        return !empty($this->client_email) && !empty($this->private_key);
    }

    public static function is_write_success($result)
    {
        return $result === true;
    }

    private function set_last_error($message)
    {
        $this->last_error = (string) $message;
        return $this->last_error;
    }

    private function parse_firestore_error_response($response, $http_code, $curl_error = '')
    {
        $message = 'Firestore request failed (HTTP ' . $http_code . ')';
        if ($curl_error) {
            $message .= ' — cURL: ' . $curl_error;
        }

        $decoded = json_decode((string) $response, true);
        if (is_array($decoded) && isset($decoded['error']['message'])) {
            $message = $decoded['error']['message'];
            if (!empty($decoded['error']['status'])) {
                $message = $decoded['error']['status'] . ': ' . $message;
            }
        } elseif (is_string($response) && trim($response) !== '') {
            $snippet = trim($response);
            if (strlen($snippet) > 280) {
                $snippet = substr($snippet, 0, 280) . '...';
            }
            $message .= ' — ' . $snippet;
        }

        return $message;
    }

    private function format_firestore_fields($fields)
    {
        $formatted_fields = [];

        foreach ($fields as $key => $value) {
            if (is_bool($value)) {
                $formatted_fields[$key] = ['booleanValue' => (bool) $value];
            } elseif (is_int($value)) {
                $formatted_fields[$key] = ['integerValue' => (string) $value];
            } elseif (is_float($value)) {
                $formatted_fields[$key] = ['doubleValue' => (float) $value];
            } elseif (is_array($value)) {
                $array_values = [];
                foreach ($value as $v) {
                    if (is_bool($v)) {
                        $array_values[] = ['booleanValue' => (bool) $v];
                    } elseif (is_int($v) || is_float($v)) {
                        $array_values[] = ['doubleValue' => (float) $v];
                    } else {
                        $array_values[] = ['stringValue' => (string) $v];
                    }
                }
                $formatted_fields[$key] = ['arrayValue' => ['values' => $array_values]];
            } else {
                // Default: string (IDs, dates, URLs, times, UIDs)
                $formatted_fields[$key] = ['stringValue' => (string) ($value ?? '')];
            }
        }

        return $formatted_fields;
    }

    private function firestore_request($method, $url, $payload = null)
    {
        $token = $this->get_access_token();
        if (!$token) {
            return [
                'ok' => false,
                'http_code' => 0,
                'body' => '',
                'error' => $this->get_last_error() ?: 'No Firebase access token',
            ];
        }

        $attempts = [
            ['verify' => true],
            ['verify' => false],
        ];

        $last = [
            'ok' => false,
            'http_code' => 0,
            'body' => '',
            'error' => 'unknown',
        ];

        foreach ($attempts as $idx => $opts) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $opts['verify']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $opts['verify'] ? 2 : 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 45);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ]);

            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }

            $response = curl_exec($ch);
            $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($response !== false) {
                if ($idx === 1) {
                    $this->log('error', 'Firestore cURL used SSL verify fallback (host CA bundle may be outdated).');
                }
                return [
                    'ok' => ($http_code >= 200 && $http_code < 300),
                    'http_code' => $http_code,
                    'body' => $response,
                    'error' => $curl_error,
                ];
            }

            $last = [
                'ok' => false,
                'http_code' => $http_code,
                'body' => '',
                'error' => 'cURL error: ' . ($curl_error ?: 'unknown'),
            ];

            // Only retry without SSL verify for SSL-related failures
            if (stripos($curl_error, 'ssl') === false && stripos($curl_error, 'certificate') === false) {
                break;
            }
        }

        return $last;
    }

    private function get_access_token()
    {
        if ($this->access_token) return $this->access_token;

        if (!$this->client_email || !$this->private_key) {
            $msg = 'Service account Firebase tidak ditemukan. Upload firebase_credentials.json ke application/config/ di server hosting.';
            $this->set_last_error($msg);
            $this->log('error', 'Firebase: ' . $msg);
            return false;
        }

        if (!class_exists('Firebase\JWT\JWT')) {
            $msg = 'Library JWT tidak ada. Pastikan folder vendor/ ter-upload ke server.';
            $this->set_last_error($msg);
            $this->log('error', 'Firebase: ' . $msg);
            return false;
        }

        $now = time();
        $payload = [
            'iss' => $this->client_email,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/cloud-platform'
        ];

        try {
            $jwt = JWT::encode($payload, $this->private_key, 'RS256');
        } catch (Exception $e) {
            $this->log('error', 'JWT Encode failed: ' . $e->getMessage());
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && isset($result['access_token'])) {
            $this->access_token = $result['access_token'];
            return $this->access_token;
        }

        // If we got an id_token but no access_token, it might still work for Firestore
        if ($http_code === 200 && isset($result['id_token'])) {
            $this->access_token = $result['id_token'];
            return $this->access_token;
        }

        $this->set_last_error($this->parse_firestore_error_response($response, $http_code));
        $this->log('error', 'Firebase OAuth Failed. Response: ' . $response);
        return false;
    }

    public function get_user_by_email($email)
    {
        try {
            // Using Identity Toolkit API to get user by email
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=" . $this->api_key;
            
            $payload = [
                'email' => [$email]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $result = json_decode($response, true);
                if (isset($result['users']) && count($result['users']) > 0) {
                    $user = $result['users'][0];
                    return (object) ['localId' => $user['localId']];
                }
                return false; // User not found
            } else {
                $res_data = json_decode($response, true);
                $msg = $res_data['error']['message'] ?? 'Unknown Firebase error';
                $this->log('error', 'Firebase lookup error (HTTP '.$http_code.'): ' . $response);
                return false;
            }
        } catch (Exception $e) {
            $this->log('error', 'Firebase lookup exception: ' . $e->getMessage());
            return false;
        }
    }

    public function create_user($user_properties)
    {
        try {
            // Using Public Identity Toolkit API with API Key for user creation
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=" . $this->api_key;
            
            $payload = [
                'email' => $user_properties['email'],
                'password' => $user_properties['password'],
                'displayName' => $user_properties['displayName'] ?? '',
                'returnSecureToken' => true
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($http_code === 200 && isset($result['localId'])) {
                return (object) ['localId' => $result['localId']];
            } else {
                $msg = $result['error']['message'] ?? 'Unknown Firebase error';
                $this->log('error', 'Firebase signUp error (HTTP '.$http_code.'): ' . $response);
                
                // If user already exists, try to get their UID
                if ($msg === 'EMAIL_EXISTS') {
                    $existing_user = $this->get_user_by_email($user_properties['email']);
                    if ($existing_user && isset($existing_user->localId)) {
                        $this->log('info', 'Firebase: User already exists, retrieved UID: ' . $existing_user->localId);
                        return (object) ['localId' => $existing_user->localId, 'already_existed' => true];
                    }
                }
                
                return (object) ['error' => $msg];
            }
        } catch (Exception $e) {
            $this->log('error', 'Firebase signUp exception: ' . $e->getMessage());
            return (object) ['error' => $e->getMessage()];
        }
    }

    public function update_user($localId, $user_properties)
    {
        try {
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:update?key=" . $this->api_key;
            
            $payload = [
                'localId' => $localId,
                'returnSecureToken' => true
            ];

            if (isset($user_properties['email'])) $payload['email'] = $user_properties['email'];
            if (isset($user_properties['password'])) $payload['password'] = $user_properties['password'];
            if (isset($user_properties['displayName'])) $payload['displayName'] = $user_properties['displayName'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                return true;
            } else {
                $res_data = json_decode($response, true);
                $msg = $res_data['error']['message'] ?? 'Unknown Firebase error';
                $this->log('error', 'Firebase update account error (HTTP '.$http_code.'): ' . $response);
                return false;
            }
        } catch (Exception $e) {
            $this->log('error', 'Firebase update account exception: ' . $e->getMessage());
            return false;
        }
    }

    public function create_firestore_document($collection, $document_id, $fields)
    {
        try {
            $collection = trim((string) $collection, '/');
            $document_id = trim((string) $document_id);
            if ($collection === '' || $document_id === '') {
                return $this->set_last_error('Collection atau document ID kosong.');
            }

            $encoded_id = rawurlencode($document_id);
            $formatted_fields = $this->format_firestore_fields($fields);
            $body = ['fields' => (object) $formatted_fields];

            // 1) POST create (avoids long updateMask query strings that break on some hostings)
            $create_url = "https://firestore.googleapis.com/v1/projects/{$this->project_id}/databases/(default)/documents/{$collection}?documentId={$encoded_id}";
            $result = $this->firestore_request('POST', $create_url, $body);

            if ($result['ok']) {
                $this->log('info', "Firestore create OK: {$collection}/{$document_id}");
                $this->last_error = '';
                return true;
            }

            // 2) PATCH upsert when document already exists (HTTP 409) or POST not allowed
            if (in_array($result['http_code'], [409, 400, 405], true)) {
                $patch_url = "https://firestore.googleapis.com/v1/projects/{$this->project_id}/databases/(default)/documents/{$collection}/{$encoded_id}";
                $masks = [];
                foreach (array_keys($fields) as $key) {
                    $masks[] = 'updateMask.fieldPaths=' . rawurlencode($key);
                }
                if (!empty($masks)) {
                    $patch_url .= '?' . implode('&', $masks);
                }

                $patch_result = $this->firestore_request('PATCH', $patch_url, $body);
                if ($patch_result['ok']) {
                    $this->log('info', "Firestore patch OK: {$collection}/{$document_id}");
                    $this->last_error = '';
                    return true;
                }

                $result = $patch_result;
            }

            $error_msg = $this->parse_firestore_error_response(
                $result['body'] ?? '',
                $result['http_code'] ?? 0,
                $result['error'] ?? ''
            );
            $this->set_last_error($error_msg);
            $this->log('error', "Firestore write failed {$collection}/{$document_id}: {$error_msg}");
            return $error_msg;
        } catch (Exception $e) {
            $msg = 'Exception: ' . $e->getMessage();
            $this->set_last_error($msg);
            $this->log('error', 'Firebase Firestore Exception: ' . $e->getMessage());
            return $msg;
        }
    }

    public function delete_firestore_document($collection, $document_id)
    {
        try {
            $token = $this->get_access_token();
            if (!$token) {
                $this->log('error', 'Firestore: No access token available, cannot delete document');
                return false;
            }

            $url = "https://firestore.googleapis.com/v1/projects/{$this->project_id}/databases/(default)/documents/{$collection}/{$document_id}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Firestore delete returns 200 with empty body on success
            if ($http_code === 200) {
                $this->log('info', "Firestore: Document {$collection}/{$document_id} deleted successfully");
                return true;
            }

            // If it's already gone, treat as success to keep delete idempotent
            if ($http_code === 404) {
                $this->log('info', "Firestore: Document {$collection}/{$document_id} not found (already deleted)");
                return true;
            }

            $this->log('error', "Firestore: Failed to delete document. HTTP {$http_code}: {$response}");
            return false;
        } catch (Exception $e) {
            $this->log('error', 'Firebase firestore delete exception: ' . $e->getMessage());
            return false;
        }
    }

    // Get all documents from a collection
    public function get_all_documents($collection) {
        try {
            $token = $this->get_access_token();
            if (!$token) {
                $this->log('error', 'Firebase: No access token available for get_all_documents');
                return [];
            }

            $url = "https://firestore.googleapis.com/v1/projects/{$this->project_id}/databases/(default)/documents/{$collection}";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $data = json_decode($response, true);
                $documents = [];
                
                if (isset($data['documents'])) {
                    foreach ($data['documents'] as $doc) {
                        $documents[] = $this->format_firestore_doc($doc);
                    }
                }
                
                return $documents;
            } else {
                $this->log('error', "Firestore: Failed to get documents from {$collection}. HTTP {$http_code}: {$response}");
                return [];
            }
        } catch (Exception $e) {
            $this->log('error', 'Firebase firestore get_all exception: ' . $e->getMessage());
            return [];
        }
    }

    public function get_firestore_document($collection, $document_id)
    {
        try {
            $token = $this->get_access_token();
            if (!$token) return false;

            $url = "https://firestore.googleapis.com/v1/projects/{$this->project_id}/databases/(default)/documents/{$collection}/{$document_id}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $doc = json_decode($response, true);
                return $this->format_firestore_doc($doc);
            }
            return false;
        } catch (Exception $e) {
            $this->log('error', 'Firebase firestore get_doc exception: ' . $e->getMessage());
            return false;
        }
    }

    public function query_documents_by_field($collection, $field, $value)
    {
        try {
            $all = $this->get_all_documents($collection);
            $matches = [];
            foreach ($all as $doc) {
                if (isset($doc[$field]) && (string)$doc[$field] === (string)$value) {
                    $matches[] = $doc;
                }
            }
            return $matches;
        } catch (Exception $e) {
            $this->log('error', 'Firebase query_documents_by_field exception: ' . $e->getMessage());
            return [];
        }
    }

    private function format_firestore_doc($doc)
    {
        $document_data = [];
        if (isset($doc['fields'])) {
            foreach ($doc['fields'] as $key => $field) {
                if (isset($field['stringValue'])) {
                    $document_data[$key] = $field['stringValue'];
                } else if (isset($field['integerValue'])) {
                    $document_data[$key] = (int)$field['integerValue'];
                } else if (isset($field['doubleValue'])) {
                    $document_data[$key] = (float)$field['doubleValue'];
                } else if (isset($field['booleanValue'])) {
                    $document_data[$key] = $field['booleanValue'];
                } else if (isset($field['arrayValue'])) {
                    $document_data[$key] = $field['arrayValue']['values'] ?? [];
                } else if (isset($field['timestampValue'])) {
                    $document_data[$key] = $field['timestampValue'];
                }
            }
        }
        $document_data['id'] = basename($doc['name']);
        return $document_data;
    }

    public function send_fcm_notification($token, $title, $body, $data = [])
    {
        // Check if this is an Expo token
        if (strpos($token, 'ExponentPushToken') === 0 || strpos($token, 'ExpoPushToken') === 0) {
            return $this->send_expo_notification($token, $title, $body, $data);
        }

        try {
            $access_token = $this->get_access_token();
            if (!$access_token) {
                $this->log('error', 'FCM: Failed to get access token');
                return false;
            }

            $url = "https://fcm.googleapis.com/v1/projects/{$this->project_id}/messages:send";
            
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'data' => (object)$data,
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => 'default',
                            'sound' => 'default'
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1
                            ]
                        ]
                    ]
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $this->log('info', "FCM: Notification sent successfully to token {$token}");
                return true;
            } else {
                $this->log('error', "FCM: Failed to send notification. HTTP {$http_code}: {$response}");
                return false;
            }
        } catch (Exception $e) {
            $this->log('error', 'FCM: Exception while sending notification: ' . $e->getMessage());
            return false;
        }
    }

    public function send_expo_notification($token, $title, $body, $data = [])
    {
        try {
            $url = "https://exp.host/--/api/v2/push/send";

            $channelId = 'default';
            if (!empty($data['type'])) {
                if ($data['type'] === 'event') {
                    $channelId = 'events';
                } elseif (in_array($data['type'], ['rating_prompt', 'reservation_confirmed', 'reservation'], true)) {
                    $channelId = 'reservations';
                }
            }

            $payload = [
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sound' => 'default',
                'priority' => 'high',
                'channelId' => $channelId,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Accept-encoding: gzip, deflate'
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $result = json_decode($response, true);
                if (isset($result['data']['status']) && $result['data']['status'] === 'ok') {
                    return true;
                } else {
                    $this->log('error', 'Expo Push Error: ' . $response);
                    return false;
                }
            } else {
                $this->log('error', 'Expo Push API Failed (HTTP '.$http_code.'): ' . $response);
                return false;
            }
        } catch (Exception $e) {
            $this->log('error', 'Expo Push Exception: ' . $e->getMessage());
            return false;
        }
    }
}
