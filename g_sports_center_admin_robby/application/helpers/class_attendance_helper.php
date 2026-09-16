<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * QR payload: GSCATTv1|{session_id}|{hmac32}
 */
if (!function_exists('gsc_attendance_build_payload')) {
    function gsc_attendance_build_payload($session_id, $secret) {
        $session_id = (int) $session_id;
        $sig = substr(hash_hmac('sha256', (string) $session_id, $secret), 0, 32);
        return 'GSCATTv1|' . $session_id . '|' . $sig;
    }
}

if (!function_exists('gsc_attendance_parse_payload')) {
    function gsc_attendance_parse_payload($payload, $secret) {
        $payload = trim((string) $payload);
        $parts = explode('|', $payload, 3);
        if (count($parts) !== 3 || $parts[0] !== 'GSCATTv1') {
            return false;
        }
        $session_id = (int) $parts[1];
        $sig32 = $parts[2];
        if ($session_id < 1 || strlen($sig32) !== 32) {
            return false;
        }
        $expected = substr(hash_hmac('sha256', (string) $session_id, $secret), 0, 32);
        if (!hash_equals($expected, $sig32)) {
            return false;
        }
        return $session_id;
    }
}

/**
 * Member QR payload: GSCMEMv1|{class_member_id}|{hmac32}
 * This QR represents an enrolled member of a class (not a session QR).
 */
if (!function_exists('gsc_member_build_payload')) {
    function gsc_member_build_payload($class_member_id, $secret) {
        $class_member_id = (int) $class_member_id;
        $sig = substr(hash_hmac('sha256', (string) $class_member_id, $secret), 0, 32);
        return 'GSCMEMv1|' . $class_member_id . '|' . $sig;
    }
}

if (!function_exists('gsc_member_parse_payload')) {
    function gsc_member_parse_payload($payload, $secret) {
        $payload = trim((string) $payload);
        $parts = explode('|', $payload, 3);
        if (count($parts) !== 3 || $parts[0] !== 'GSCMEMv1') {
            return false;
        }
        $member_id = (int) $parts[1];
        $sig32 = $parts[2];
        if ($member_id < 1 || strlen($sig32) !== 32) {
            return false;
        }
        $expected = substr(hash_hmac('sha256', (string) $member_id, $secret), 0, 32);
        if (!hash_equals($expected, $sig32)) {
            return false;
        }
        return $member_id;
    }
}
