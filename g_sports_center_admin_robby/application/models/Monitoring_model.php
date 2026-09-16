<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Aggregasi data akurat untuk halaman Big Data → Monitoring.
 */
class Monitoring_model extends CI_Model {

    /** Status reservasi yang dihitung sebagai pendapatan / booking sukses */
    private $successful_reservation_statuses = ['confirmed', 'completed', 'selesai'];

    public function get_successful_status_sql_in()
    {
        $escaped = array_map(function ($s) {
            return $this->db->escape($s);
        }, $this->successful_reservation_statuses);
        return implode(',', $escaped);
    }

    /**
     * Statistik rating dari Firestore collection `reviews`.
     */
    public function get_rating_stats($firebase_admin)
    {
        $empty = [
            'total_ratings' => 0,
            'avg_rating' => 0,
            'positive_percentage' => 0,
            'critical_percentage' => 0,
            'distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
        ];

        if (!$firebase_admin || !method_exists($firebase_admin, 'get_all_documents')) {
            return $empty;
        }

        try {
            $reviews = $firebase_admin->get_all_documents('reviews');
            $total = 0;
            $sum = 0;
            $positive = 0;
            $critical = 0;
            $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

            foreach ($reviews as $review) {
                if (!isset($review['rating'])) {
                    continue;
                }
                $rating = (int) $review['rating'];
                if ($rating < 1 || $rating > 5) {
                    continue;
                }
                $total++;
                $sum += $rating;
                $distribution[$rating]++;
                if ($rating >= 4) {
                    $positive++;
                } elseif ($rating <= 2) {
                    $critical++;
                }
            }

            return [
                'total_ratings' => $total,
                'avg_rating' => $total > 0 ? round($sum / $total, 1) : 0,
                'positive_percentage' => $total > 0 ? round(($positive / $total) * 100, 1) : 0,
                'critical_percentage' => $total > 0 ? round(($critical / $total) * 100, 1) : 0,
                'distribution' => $distribution,
            ];
        } catch (Exception $e) {
            log_message('error', 'Monitoring_model rating: ' . $e->getMessage());
            return $empty;
        }
    }

    /**
     * Booking per fasilitas olahraga — 7 hari terakhir, hanya reservasi sukses.
     * Menggabungkan data dari `reservations` dan `reservations_archive`.
     */
    public function get_booking_by_facility_last_7_days()
    {
        $in = $this->get_successful_status_sql_in();
        $rows = $this->db->query("
            SELECT name, COUNT(*) AS bookings
            FROM (
                SELECT r.id, f.name, r.created_at, r.status
                FROM reservations r
                INNER JOIN reservation_facilities f ON r.facility_id = f.id
                UNION ALL
                SELECT ra.id, f2.name, ra.created_at, ra.status
                FROM reservations_archive ra
                INNER JOIN reservation_facilities f2 ON ra.facility_id = f2.id
            ) AS combined
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
              AND status IN ({$in})
            GROUP BY name
            ORDER BY bookings DESC
        ")->result_array();

        $labels = [];
        $data = [];
        foreach ($rows as $row) {
            $labels[] = $row['name'];
            $data[] = (int) $row['bookings'];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Distribusi channel reservasi — tahun berjalan, hanya sukses.
     * Menggabungkan data dari `reservations` dan `reservations_archive`.
     */
    public function get_channel_distribution_year()
    {
        $in = $this->get_successful_status_sql_in();
        $year = (int) date('Y');
        $rows = $this->db->query("
            SELECT
                CASE
                    WHEN firebase_uid IS NOT NULL AND TRIM(firebase_uid) <> '' THEN 'Mobile App'
                    ELSE 'Web / Admin / Walk-in'
                END AS channel,
                COUNT(*) AS total
            FROM (
                SELECT firebase_uid, status, approved_at, created_at FROM reservations
                UNION ALL
                SELECT firebase_uid, status, approved_at, created_at FROM reservations_archive
            ) AS combined
            WHERE status IN ({$in})
              AND YEAR(COALESCE(approved_at, created_at)) = ?
            GROUP BY channel
            ORDER BY total DESC
        ", [$year])->result_array();

        $labels = [];
        $data = [];
        foreach ($rows as $row) {
            $labels[] = $row['channel'];
            $data[] = (int) $row['total'];
        }

        if (empty($labels)) {
            $labels = ['Mobile App', 'Web / Admin / Walk-in'];
            $data = [0, 0];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Kehadiran karyawan — bulan berjalan (tabel attendances).
     */
    public function get_attendance_stats_current_month()
    {
        $start = date('Y-m-01');
        $end = date('Y-m-t');

        $rows = $this->db->query("
            SELECT status, COUNT(*) AS total
            FROM attendances
            WHERE date >= ? AND date <= ?
            GROUP BY status
        ", [$start, $end])->result_array();

        $stats = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'alpha' => 0,
            'fake gps' => 0,
        ];

        foreach ($rows as $row) {
            $key = $row['status'];
            if (array_key_exists($key, $stats)) {
                $stats[$key] = (int) $row['total'];
            }
        }

        $stats['period_label'] = date('F Y');
        
        // Fix Warning: array_sum() on string type. 
        // We filter only numeric values to avoid issues with 'period_label' which is a string.
        $stats['total_records'] = array_sum(array_filter($stats, 'is_numeric'));

        return $stats;
    }

    private function month_labels_id()
    {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    }

    private function empty_month_series()
    {
        return array_fill(0, 12, 0.0);
    }

    /**
     * Pendapatan reservasi sukses per bulan (total_amount) — tahun berjalan.
     * Menggabungkan data dari `reservations` dan `reservations_archive`.
     */
    public function get_reservation_revenue_by_month($year = null)
    {
        $year = $year ? (int) $year : (int) date('Y');
        $in = $this->get_successful_status_sql_in();
        $data = $this->empty_month_series();

        $rows = $this->db->query("
            SELECT MONTH(COALESCE(combined.approved_at, combined.created_at)) AS m, SUM(combined.total_amount) AS total
            FROM (
                SELECT total_amount, status, approved_at, created_at FROM reservations
                UNION ALL
                SELECT total_amount, status, approved_at, created_at FROM reservations_archive
            ) AS combined
            WHERE combined.status IN ({$in})
              AND YEAR(COALESCE(combined.approved_at, combined.created_at)) = ?
            GROUP BY m
            ORDER BY m
        ", [$year])->result_array();

        foreach ($rows as $row) {
            $m = (int) $row['m'];
            if ($m >= 1 && $m <= 12) {
                $data[$m - 1] = (float) $row['total'];
            }
        }

        return $data;
    }

    /**
     * Pendapatan paket GSC per bulan — financial_transactions.
     */
    public function get_package_revenue_by_month($year = null)
    {
        $year = $year ? (int) $year : (int) date('Y');
        $data = $this->empty_month_series();

        if (!$this->db->table_exists('financial_transactions')) {
            return $data;
        }

        $rows = $this->db->query("
            SELECT MONTH(created_at) AS m, SUM(amount) AS total
            FROM financial_transactions
            WHERE transaction_type = 'package'
              AND YEAR(created_at) = ?
            GROUP BY m
            ORDER BY m
        ", [$year])->result_array();

        foreach ($rows as $row) {
            $m = (int) $row['m'];
            if ($m >= 1 && $m <= 12) {
                $data[$m - 1] = (float) $row['total'];
            }
        }

        return $data;
    }

    /**
     * Grafik keuangan keseluruhan + kartu ringkasan + breakdown tipe.
     */
    public function get_finance_dashboard($year = null)
    {
        $year = $year ? (int) $year : (int) date('Y');
        $labels = $this->month_labels_id();
        $reservation_monthly = $this->get_reservation_revenue_by_month($year);
        $package_monthly = $this->get_package_revenue_by_month($year);

        $combined_monthly = [];
        for ($i = 0; $i < 12; $i++) {
            $combined_monthly[] = $reservation_monthly[$i] + $package_monthly[$i];
        }

        $reservation_total = array_sum($reservation_monthly);
        $package_total = array_sum($package_monthly);
        $total_revenue = $reservation_total + $package_total;
        $package_share = $total_revenue > 0 ? round(($package_total / $total_revenue) * 100, 1) : 0;

        $breakdown_labels = [];
        $breakdown_data = [];
        if ($reservation_total > 0) {
            $breakdown_labels[] = 'Reservasi (Sukses)';
            $breakdown_data[] = $reservation_total;
        }
        if ($package_total > 0) {
            $breakdown_labels[] = 'Paket GSC';
            $breakdown_data[] = $package_total;
        }

        // Transaksi lain di financial_transactions (mis. booking DP) — opsional, tidak digabung ke total utama
        if ($this->db->table_exists('financial_transactions')) {
            $others = $this->db->query("
                SELECT transaction_type, SUM(amount) AS total
                FROM financial_transactions
                WHERE YEAR(created_at) = ?
                  AND transaction_type NOT IN ('package')
                GROUP BY transaction_type
                HAVING total > 0
            ", [$year])->result_array();

            foreach ($others as $row) {
                $type = $row['transaction_type'];
                if ($type === 'booking') {
                    $breakdown_labels[] = 'DP Booking (Tercatat)';
                    $breakdown_data[] = (float) $row['total'];
                } elseif ($type !== '') {
                    $breakdown_labels[] = ucfirst(str_replace('_', ' ', $type));
                    $breakdown_data[] = (float) $row['total'];
                }
            }
        }

        if (empty($breakdown_labels)) {
            $breakdown_labels = ['Belum ada data'];
            $breakdown_data = [0];
        }

        return [
            'year' => $year,
            'labels' => $labels,
            'combined_monthly' => $combined_monthly,
            'reservation_monthly' => $reservation_monthly,
            'package_monthly' => $package_monthly,
            'finance_stats' => [
                'labels' => $labels,
                'data' => $combined_monthly,
            ],
            'package_finance_stats' => [
                'labels' => $labels,
                'data' => $package_monthly,
            ],
            'finance_breakdown' => [
                'labels' => $breakdown_labels,
                'data' => $breakdown_data,
                'reservation_total' => $reservation_total,
                'package_total' => $package_total,
                'total_revenue' => $total_revenue,
                'package_share' => $package_share,
            ],
        ];
    }

    public function get_reservation_stats_for_monitoring()
    {
        return [
            'facilities' => $this->get_booking_by_facility_last_7_days(),
            'channels' => $this->get_channel_distribution_year(),
        ];
    }
}
