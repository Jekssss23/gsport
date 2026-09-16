<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reservation_acceptanced extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Reservation_model', 'reservation');

        $hak = (array) $this->session->userdata('id_hak_akses');
        if (!in_array(1, $hak) && !in_array(2, $hak) && !in_array(3, $hak)) {
            redirect('auth/login/kick');
        }
    }

    public function index()
    {
        $data['judul'] = 'Reservation Acceptanced';
        $data['deskripsi'] = 'Halaman untuk menerima/menolak reservasi (untuk kebutuhan integrasi mobile).';
        $data['modal'] = '';

        // Proactively auto-complete expired reservations and send push notifications
        $this->reservation->auto_complete_expired_reservations();

        $data['pending'] = $this->reservation->get_all_pending_reservations();
        $data['confirmed'] = $this->reservation->get_confirmed_reservations();
        $data['reservations'] = array_merge($data['pending'], $data['confirmed']);
        $data['accepted_today'] = $this->reservation->get_accepted_today_count();
        $data['cancellation_requests'] = $this->reservation->get_cancellation_requests();

        $this->template->load('template/user_adminlte', 'user/gro/reservation_acceptanced/index', $data);
    }

    public function sync_firebase()
    {
        try {
            $result = $this->reservation->sync_firebase_reservations();
            
            $message = '<div class="alert alert-info">';
            $message .= '<strong>Sync Result:</strong><br>';
            $message .= 'Total processed: ' . $result['total_processed'] . '<br>';
            $message .= 'Successfully synced: ' . $result['synced_count'] . '<br>';
            
            if (!empty($result['errors'])) {
                $message .= '<strong>Errors:</strong><br>';
                foreach ($result['errors'] as $error) {
                    $message .= '- ' . $error . '<br>';
                }
            }
            
            $message .= '</div>';
            
            $this->session->set_flashdata('pesan', $message);
            
        } catch (Exception $e) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Sync failed: ' . $e->getMessage() . '</div>');
        }
        
        redirect('user/gro/reservation_acceptanced');
    }

    public function accept($reservation_id)
    {
        $ok = $this->reservation->accept_reservation((int) $reservation_id, (int) id_user());
        if ($ok) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Reservasi berhasil di-accept</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal accept reservasi</div>');
        }
        redirect('user/gro/reservation_acceptanced');
    }

    public function reject($reservation_id)
    {
        $reason = $this->input->post('reason');
        $ok = $this->reservation->reject_reservation((int) $reservation_id, (int) id_user(), (string) $reason);
        if ($ok) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Reservasi berhasil di-reject</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal reject reservasi</div>');
        }
        redirect('user/gro/reservation_acceptanced');
    }

    public function get_details($reservation_id)
    {
        $details = $this->reservation->get_reservation_details((int) $reservation_id);
        
        if (!$details) {
            $this->respond_json(['ok' => false, 'message' => 'Reservation not found'], 404);
        }
        
        $this->respond_json(['ok' => true, 'data' => $details]);
    }
    
    private function respond_json($data, $status_code = 200)
    {
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data));
    }

    public function approve_cancellation($reservation_id)
    {
        $ok = $this->reservation->approve_cancellation((int) $reservation_id, (int) id_user());
        if ($ok) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Pembatalan disetujui</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menyetujui pembatalan</div>');
        }
        redirect('user/gro/reservation_acceptanced');
    }

    public function reject_cancellation($reservation_id)
    {
        $ok = $this->reservation->reject_cancellation((int) $reservation_id, (int) id_user());
        if ($ok) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Pembatalan ditolak</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal menolak pembatalan</div>');
        }
        redirect('user/gro/reservation_acceptanced');
    }

    public function archive_reservation($reservation_id)
    {
        $ok = $this->reservation->archive_reservation((int) $reservation_id, (int) id_user());
        if ($ok) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success">Reservasi berhasil diarsipkan! <a href="' . base_url('user/big_data') . '" class="btn btn-sm btn-info">Lihat di Big Data</a></div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">Gagal mengarsipkan reservasi</div>');
        }
        redirect('user/gro/reservation_acceptanced');
    }

    public function booking_ping()
    {
        $latest = $this->db->query("SELECT id, reservation_code, user_name, facility_id, created_at FROM reservations ORDER BY created_at DESC LIMIT 1")->row_array();
        $facility_name = '';
        if ($latest && !empty($latest['facility_id'])) {
            $f = $this->db->get_where('reservation_facilities', ['id' => (int)$latest['facility_id']])->row_array();
            $facility_name = $f['name'] ?? '';
        }
        $this->respond_json([
            'ok' => true,
            'data' => [
                'id' => $latest['id'] ?? null,
                'reservation_code' => $latest['reservation_code'] ?? '',
                'user_name' => $latest['user_name'] ?? '',
                'facility_name' => $facility_name,
                'created_at' => $latest['created_at'] ?? null,
            ]
        ]);
    }
}

