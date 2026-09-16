# G-SPORTS CENTER ADMIN ROBBY — DOKUMENTASI PROYEK (KESIMPULAN DETAIL)

---

## 1. IDENTITAS & GAMBARAN UMUM PROYEK

| Item | Detail |
|---|---|
| **Nama Proyek** | G-Sports Center Admin (Robby) — Management System Pusat Olahraga |
| **Nama Brand** | GSC (G Sports Center) |
| **Production URL** | `https://management.g-sportscenter.com/` |
| **Default Timezone** | `Asia/Jakarta` |
| **Tipe Proyek** | **HYBRID MULTI-PLATFORM**: Web Admin Panel (desktop) + Mobile App (Android & iOS) |
| **Owner Account EAS Build** | `rbsoftware` |

Proyek ini adalah sistem manajemen terintegrasi untuk sebuah pusat olahraga (G-Sports Center) yang mencakup **2 output utama**:
1. **Web Application** — Untuk internal tim (Admin, GRO, Office/Pimpinan) mengelola seluruh operasional
2. **Mobile Application** — Untuk member (user) bertransaksi & karyawan (admin) untuk operasional lapangan

---

## 2. OUTPUT SISTEM (2 PLATFORM)

### 2.1 Output 1 — WEB ADMIN PANEL (CodeIgniter 3.x)

| Aspek | Teknologi / Detail |
|---|---|
| **Framework Backend** | CodeIgniter 3.x (PHP ≥ 5.3.7) |
| **Frontend UI** | AdminLTE Template + jQuery + CKEditor + SweetAlert + Lightbox2 + Custom CSS (gym template) |
| **Entry Point** | `index.php` (root) |
| **Default Controller** | `Redirect_halaman_utama` |
| **Base URL (Prod)** | `https://management.g-sportscenter.com/` |
| **Local Dev** | XAMPP Apache (`c:\xampp\htdocs\...`) |
| **Session Storage** | File-based (`application/cache/sessions`), expire 7200 detik (2 jam) |
| **Template Engine** | Custom `Template.php` library dengan 3 layout: `template/user.php`, `template/user_adminlte.php`, `template/user_bigdata.php` |
| **Auth Web** | Session PHP + `password_hash()` / `password_verify()` dari tabel `master_user` |
| **RBAC Web** | Multi-role via tabel `hak_akses` + `hak_akses_user` (1 user bisa punya banyak hak akses) |

**Struktur Direktori Web Utama:**
```
g_sports_center_admin_robby/
├── index.php                    (CI front controller)
├── .htaccess
├── composer.json                (PHP dependencies)
├── application/
│   ├── config/                  (config.php, routes.php, database.php, hooks.php)
│   ├── controllers/             (40+ Controllers — lihat bab 5)
│   │   ├── auth/
│   │   ├── api/                 (REST API untuk Mobile App)
│   │   └── user/
│   │       ├── admin/
│   │       ├── gro/
│   │       ├── office/
│   │       └── user/
│   ├── models/                  (11 Models)
│   ├── libraries/               (5 Custom Libraries — lihat bab 6)
│   ├── hooks/                   (CORS Hook)
│   ├── views/                   (200+ View Files)
│   └── cache/                   (Session & Cache)
├── assets/                      (AdminLTE, CKEditor, Images, Templates)
├── db/
│   └── gsc_robby.sql            (MySQL Dump — 69 tabel)
└── file/                        (Uploaded files: QR, kartu member, foto, ringtone)
```

---

### 2.2 Output 2 — MOBILE APP (React Native + Expo)

| Aspek | Teknologi / Detail |
|---|---|
| **Framework** | React Native 0.81.5 (**New Architecture Enabled**) |
| **Expo SDK** | Expo 54.0.33 |
| **React Version** | 19.1.0 |
| **TypeScript** | Support aktif (typescript 5.9.2) |
| **Entry Point** | `App/index.js` → `App/App.js` |
| **Package Name (Android)** | `com.gsportscenter.app` |
| **Bundle ID (iOS)** | `com.gsportscenter.app` |
| **EAS Project ID** | `439d4ceb-cfc4-4e47-b66a-2dffa3677bd7` |
| **Navigasi** | React Navigation 7.x (Native Stack Navigator) |
| **State/Data Fetching** | TanStack React Query 5.101.2 + Persist Client (Async Storage) |
| **Fonts Custom** | `Humane-Regular`, `Humane-Medium` |
| **Lottie** | `lottie-react-native` 7.3.5 untuk animasi premium |

**Struktur Direktori Mobile Utama:**
```
g_sports_center_admin_robby/App/
├── App.js                       (Root Navigator + Auth Listener)
├── app.json                     (EAS Expo Config)
├── eas.json                     (Build Profiles)
├── package.json
├── firestore.rules              (Firebase Security Rules)
├── google-services.json         (Android Firebase)
├── GoogleService-Info.plist     (iOS Firebase)
├── assets/
│   ├── LOGO/                    (ICON APP, Logo GSC, Backgrounds)
│   ├── images/                  (Sport images: gym, swim, pickle, futsal, etc.)
│   ├── FONT/                    (Humane font family)
│   └── ui/                      (Reusable UI: auth.jsx, loading, error)
└── src/
    ├── config/                  (api.js, firebase.js, queryClient.js)
    ├── screens/
    │   ├── SplashScreen, Login, Register, Landing
    │   ├── admin/               (Dashboard, Attendance, QR Scan, Schedule, Booking History, Rating, Package Manage)
    │   └── user/                (Dashboard, Reservation, Class, Attendance, Packages, Notification, ETicket, Profile)
    ├── services/                (Booking, Notification, ClassSchedule, Cloudinary)
    ├── hooks/                   (useBooking, usePackages, useClassSchedule)
    ├── api/                     (attendance.js)
    ├── components/              (AppModalAlert, AppErrorState)
    ├── styles/                  (theme.js)
    ├── utils/                   (errorMessages.js)
    └── constants/               (assets.js)
```

**Native Modules Mobile (Permissions & Hardware):**
- `expo-camera` — Kamera untuk **scan QR** (absensi kelas, scan paket)
- `expo-location` — **GPS radius check** untuk absensi karyawan
- `expo-notifications` — Push notification (FCM via Expo)
- `expo-image-picker` — Upload foto selfie untuk absensi
- `expo-av` — Audio/Video player
- `react-native-qrcode-svg` — Generate QR di device (e-ticket)
- `@react-native-async-storage/async-storage` — Local persistence (Firebase Auth session + React Query cache)

**Mobile Navigator Stack (3 Stack):**
```
AuthStack:
  Splash → Login → Register

AdminStack (role: admin):
  AdminDashboard → Attendance → ClassAttendanceScan → ClassScheduling
  → UserReservationHistory → RatingMe → GscPackageManage → Events

UserStack (role: member/user):
  UserDashboard → FieldReservation → ClassSchedule → MyReservationHistory
  → Attendance → GscPackage → BuyPackage → Notifications → ETicket
  → Profile → CancellationRequest → Rating → ClassAttendanceScan → Events
```

---

## 3. DATABASE YANG DIGUNAKAN (3 SUMBER HYBRID)

### 3.1 Database Utama — MySQL / MariaDB

| Item | Detail |
|---|---|
| **Nama Database** | `gsc_robby` |
| **Engine** | MySQLi Driver (CodeIgniter) |
| **Host** | `localhost:3306` (XAMPP default) |
| **Username Dev** | `root` |
| **Password Dev** | (kosong) |
| **Charset** | `utf8 / utf8_general_ci` |
| **Jumlah Tabel** | **69 TABEL** (teridentifikasi dari SQL dump) |

**Kategori Tabel MySQL (69):**

**A. Sistem & Auth (7)**
| Tabel | Fungsi |
|---|---|
| `ci_sessions` | Session CodeIgniter |
| `master_user` | User login web (username, password_hash, jabatan, status_akses) |
| `hak_akses` | Daftar peran/level akses |
| `hak_akses_user` | Mapping many-to-many user ↔ hak akses |
| `menu` | Menu sidebar web |
| `menu_groups` | Grup menu |
| `master_modules`, `modules_groups` | Modul sistem |

**B. Data Master Olahraga (12)**
| Tabel | Fungsi |
|---|---|
| `master_futsal`, `master_badminton`, `master_pickle` | Harga sewa harian per jam |
| `master_futsal_turnamen`, `master_badminton_turnamen`, `master_pickle_turnamen` | Harga paket turnamen |
| `futsal_turnamen_biaya_tambahan`, `badminton_turnamen_biaya_tambahan`, `pickle_turnamen_biaya_tambahan` | Biaya tambahan turnamen |
| `diskon_member_futsal`, `diskon_member_badminton`, `diskon_member_pickle` | Diskon khusus member per cabang |
| `master_diskon` | Master diskon umum |
| `master_peralatan` | Daftar peralatan untuk disewa |
| `lapangan_atas` | Data lapangan |
| `badminton_shoutlecook` | Spesifik badminton |

**C. Member & Kartu (6)**
| Tabel | Fungsi |
|---|---|
| `member` | Data lengkap member (nama, no_telepon, alamat, foto, QR string) |
| `master_student_card` | Kartu pelajar |
| `student_card` | Transaksi kartu pelajar |
| `history_student_card` | Riwayat kartu pelajar |
| `master_poin`, `poin_member` | Poin reward member |
| `master_penukaran_poin` | Katalog penukaran poin |

**D. Jadwal & Booking Lapangan (10)**
| Tabel | Fungsi |
|---|---|
| `jadwal_futsal`, `jadwal_badminton` | Jadwal sewa harian lapangan |
| `reservations` | **TABEL UTAMA RESERVASI** (unified booking: user firebase_uid, facility, court, date, slots, status) |
| `reservations_archive` | Arsip reservation lama |
| `reservation_facilities` | Daftar fasilitas (Futsal, Badminton, Pickleball, dll) |
| `reservation_courts` | Daftar lapangan per fasilitas |
| `reservation_slots` | Slot waktu yang dibooking per reservation |
| `reservation_slots_archive` | Arsip slot |
| `identitas_order_futsal`, `identitas_order_badminton`, `identitas_order_pickle` | Detail order per cabang olahraga |

**E. Pembayaran & Keuangan (7)**
| Tabel | Fungsi |
|---|---|
| `transaksi` | Catatan transaksi global |
| `pembayaran` | Detail pembayaran (metode, jumlah, tanggal) |
| `master_metode_pembayaran` | Daftar metode bayar (Cash, QRIS, Transfer, dll) |
| `pendapatan` | Rekap pendapatan per departemen |
| `akun_pendapatan` | Kode akun pendapatan (COA) |
| `financial_transactions` | **JEMBATAN MYSQL ↔ FIREBASE** — log sinkronisasi keuangan dari mobile app |
| `print_setting` | Konfigurasi printer/print PDF |

**F. Kelas & Instruktur (10)**
| Tabel | Fungsi |
|---|---|
| `class_categories` | Kategori kelas (Gym, Swim, Martial, dll) |
| `class_types` | Tipe kelas (contoh: Private Gym, Swimming Club, Muay Thai Private) |
| `class_schedules` | Jadwal kelas mingguan |
| `class_sessions` | Sesi kelas per tanggal spesifik |
| `class_session_attendance` | Absensi member per sesi kelas (berbasis QR) |
| `class_members` | Member yang terdaftar di tipe kelas |
| `class_bookings` | Booking member per sesi kelas |

**G. KPI & SDM (6)**
| Tabel | Fungsi |
|---|---|
| `employees` | Data karyawan (id, nama, jabatan, firebase_uid, status) |
| `attendances` | **Absensi karyawan** (check-in GPS, check-out, selfie URL, status) |
| `attendances_archive` | Arsip absensi |
| `attendance_settings` | Pengaturan lokasi absen (lat, long, radius) |
| `kpi_templates` | Template KPI per jabatan/divisi |
| `kpi_assessments` | Penilaian KPI periode per karyawan |

**H. F&B & Proshop (6)**
| Tabel | Fungsi |
|---|---|
| `master_fnb` | Menu makanan & minuman |
| `outlet_fnb` | Daftar outlet F&B |
| `outlet_fnb_user` | User penjaga outlet F&B |
| `outlet_proshop` | Daftar toko perlengkapan |
| `outlet_proshop_user` | User penjaga proshop |

**I. Lainnya (5)**
| Tabel | Fungsi |
|---|---|
| `app_events` | Event/promo GSC (untuk mobile dan web) |
| `foto_galery` | Galeri foto |
| `foto_galery` | Galeri |

---

### 3.2 Database Hybrid 2 — Firebase (BaaS Google Cloud)

| Item | Detail |
|---|---|
| **Nama Project** | `g-sports-center` |
| **Project ID** | `g-sports-center` |
| **Lokasi** | Default (US Multi-region / Firebase Default) |
| **Web App ID** | `1:253539109830:web:4e1cd4855084a457a800c7` |
| **Messaging ID** | `253539109830` |
| **Measurement ID** | `G-G5JP1R7V1F` |
| **Auth Method** | Email/Password + Phone Number (hybrid lookup) |
| **Firestore Rules** | `App/firestore.rules` |
| **File iOS** | `App/GoogleService-Info.plist` |
| **File Android** | `App/google-services.json` |

**Firebase Services yang Digunakan:**

| Service | Fungsi di Proyek |
|---|---|
| **Firebase Auth** | Login/Register Mobile App (Email & Password). Session disimpan via `ReactNativePersistence` ke AsyncStorage |
| **Firestore DB** | Koleksi data terpadu untuk mobile, **disinkronkan dua arah dengan MySQL** |
| **Firebase Storage** | Upload file (foto profil, dokumen) via SDK mobile |
| **Cloud Messaging (FCM)** | Push notification ke semua device via `expo-notifications` |
| **Firebase Admin SDK (PHP)** | Backend PHP mengakses Firestore melalui library `Firebase_admin.php` |

**Firestore Collections (Teridentifikasi):**

| Collection | Isi | Keterangan |
|---|---|---|
| `users` | Profil pengguna mobile app | **Role-based**: field `role` = `admin` atau `user`. Ada `phoneNumber`, `email`, `displayName`, `photoURL`, `jabatan`, `employee_id` |
| `attendances` | Absensi karyawan dari mobile | Mirip tabel MySQL `attendances` — disync via `sync_alpha` API |
| `settings` | Dokumen `attendance` (lat, long, radius, locationName) | Pengaturan lokasi absen (sumber truth bisa Firebase atau MySQL — lihat `attendance_settings`) |
| `gsc_packages` | Paket pre-paid hour (membership olahraga) | Field: `userId`, `userName`, `sportType`, `totalHours`, `remainingHours`, `status` (active/completed), `purchaseDate`, `expiryDate` |
| `gsc_package_usage` | Riwayat pemakaian paket per jam | Setiap deduct hour dicatat di sini oleh admin scan |
| `notifications` | Push notifications payload | Riwayat notifikasi user |
| `class_attendance` | Absensi kelas (dari scan QR mobile) | Sinkron ke `class_session_attendance` MySQL |
| `events` | Event/promo untuk ditampilkan mobile | Sync dengan `app_events` MySQL |
| `fcm_tokens` | Token perangkat FCM untuk push notif | Didaftarkan saat user login mobile |

---

### 3.3 Database Hybrid 3 — Cloudinary (Media CDN)

| Item | Detail |
|---|---|
| **Fungsi** | Cloud-hosted image CDN untuk upload & transform gambar |
| **Integrasi PHP** | `application/libraries/Cloudinarylib.php` |
| **Integrasi Mobile** | `App/src/services/CloudinaryService.js` |
| **Kegunaan** | Upload foto **selfie attendance** karyawan, foto member, dokumentasi event |

---

## 4. ARSITEKTUR HYBRID & FLOW SINKRONISASI DATA

### 4.1 Diagram Aliran Data Umum

```
┌─────────────────────────────────────────────────────────────────────────┐
│                             USER LAYER                                  │
│  ┌──────────────────────┐        ┌────────────────────────────────┐    │
│  │  MOBILE APP          │        │  WEB ADMIN PANEL               │    │
│  │  (Expo/React Native) │◄─CORS─►│  (CodeIgniter 3 / PHP)         │    │
│  │                      │        │  AdminLTE + jQuery             │    │
│  └─────┬─────────────┬──┘        └──┬──────────────┬──────────────┘    │
│        │             │              │              │                   │
│   Firebase SDK   REST API          Auth          Render Views          │
│   (direct)       (fetch)           Session       + Ajax/JQuery          │
│        │             │              │              │                   │
└────────┼─────────────┼──────────────┼──────────────┼───────────────────┘
         │             │              │              │
         ▼             ▼              ▼              │
┌──────────────────────────────────────────────────┐  │
│              FIREBASE ECOSYSTEM                  │  │
│  ┌───────────┐  ┌────────────┐  ┌────────────┐  │  │
│  │  Auth     │  │  Firestore │  │  FCM Push  │  │  │
│  └───────────┘  └─────┬──────┘  └────────────┘  │  │
│                       │      ▲                   │  │
│     Firebase Admin SDK│      │Read/Write         │  │
│     (PHP Library)     │      │                   │  │
│                       ▼      │                   │  │
│                 ┌────────────┴───────────┐       │  │
│                 │  Cloudinary (Image CDN)│       │  │
│                 └────────────┬───────────┘       │  │
└──────────────────────────────┼───────────────────┘  │
                               │                      │
                               ▼                      ▼
                    ┌─────────────────────────────────────────┐
                    │     MYSQL (MariaDB) — gsc_robby         │
                    │     69 tables via CodeIgniter DB Driver │
                    └─────────────────────────────────────────┘
```

### 4.2 Flow Sinkronisasi Spesifik (Contoh Nyata)

#### A. Flow Absensi Karyawan (Hybrid GPS + QR + Selfie)
```
Mobile (Karyawan)                Web (Big Data Admin)
      │                                   │
      ├─ Ambil GPS (expo-location)        │
      ├─ Cek radius ke Server ───────────►├─ api/attendance/check_radius
      │   (bandingkan lat/long)           ├─ Query attendance_settings
      │                                   │
      ├─ Jika DALAM RADIUS:               │
      │   1. Ambil foto selfie ──────────►├─ Cloudinary upload
      │   2. api/attendance/save ────────►├─ INSERT attendances (MySQL)
      │                                   ├─ INSERT attendances (Firestore)
      │                                   │  via Firebase_admin.php SDK
      │◄──────── Berhasil + status ───────┤
      │                                   │
      ├─ Check-out (manual/auto) ────────►├─ UPDATE attendances MySQL
      │                                   ├─ UPDATE Firestore (sync)
      │                                   │
      │                            ┌──────┤
      │                            │ View │ attendance_settings (set GPS)
      │                            │ View │ attendance (realtime list)
      │                            │ View │ attendance_archive
      │                            └──────┤
```

#### B. Flow Booking Lapangan (Mobile → MySQL via REST)
```
Member Mobile App                    Backend CI API
      │                                   │
      ├─ FieldReservationScreen          │
      ├─ Pilih fasilitas ───────────────►├─ api/reservation/facilities
      │                                   ├─ reservation_facilities (MySQL)
      │                                   │
      ├─ Pilih lapangan ────────────────►├─ api/reservation/courts
      │                                   ├─ reservation_courts
      │                                   │
      ├─ Pilih tanggal ─────────────────►├─ api/reservation/availability
      │   (cek slot kosong)               ├─ jadwal_* + reservation_slots
      │                                   │
      ├─ Confirm Booking ───────────────►├─ api/reservation/create
      │   (firebase_uid, user_name,       ├─ INSERT reservations (MySQL)
      │    time_slots[], dll)             ├─ INSERT reservation_slots
      │                                   ├─ Log financial_transactions
      │◄───── Return reservation_id ──────┤
      │         + E-Ticket QR             │
      │                                   │
      ├─ ETicketScreen (tampil QR)        │
      │    └─ generate via qrcode-svg     │
```

#### C. Flow GSC Package (Hourly Membership) — **HYBRID 100%**
```
Mobile Admin Scan Paket:             Backend API                  Firestore ↔ MySQL
      │                                   │                           │
      ├─ GscPackageManageScreen           │                           │
      ├─ Scan QR member (expo-camera)     │                           │
      │                                   │                           │
      ├─ GET api/gsc_package/get_all ────►├─ Firebase_admin.php ─────►├─ gsc_packages collection
      │◄── List paket user aktif ─────────┤◄──────────────────────────┤ (Read only)
      │                                   │                           │
      ├─ Deduct Hours (POST) ────────────►├─ Validasi remainingHours  │
      │   (package_id, hours, admin_id)   │                           │
      │                                   ├─ UPDATE Firestore ────────►├─ gsc_packages SET remainingHours
      │                                   │                           │   status='completed' if 0
      │                                   │                           │
      │                                   ├─ INSERT Firestore ────────►├─ gsc_package_usage (log)
      │                                   │                           │
      │                                   ├─ INSERT MySQL             │
      │                                   │   financial_transactions  │
      │                                   │   (type: package_usage)   │
      │◄─────── Success response ─────────┤                           │
```

---

## 5. MODUL FITUR UTAMA (LENGKAP)

### 5.1 Hak Akses / Role Sistem (Multi-Role Per User)

| ID Hak Akses | Role | Dashboard Utama | Modul Utama |
|---|---|---|---|
| 1 | **Pimpinan / Director** | user/big_data (redirect KPI) | KPI assessment, Attendance settings, Monitoring, Event Organizer, Rating, Data Reset, Employee management |
| 2 | **Admin Pusat** | user/admin/Dashboard | CRUD User, Karyawan, Futsal/Badminton/Pickle master, Metode Pembayaran |
| 3 | **GRO (Front Desk)** | user/gro/Dashboard | Booking (sewa lap), New Transaction (membership), Reservation Acceptance |
| 4 | **Gym Instructor** | user/user/Dashboard | Lihat QR, Profile, Change Password |
| 5 | **Office / Finance** | user/office/Dashboard | Input Pendapatan, Grafik Keuangan, Print Laporan PDF |
| 6 | **Swimming Coach** | user/user/Dashboard | Lihat QR, Profile |

*(Catatan: satu user bisa punya multiple hak akses. Contoh: Admin sekaligus Pimpinan = id_hak_akses `[1,2]`)*

---

### 5.2 Daftar Modul Fitur per Platform

#### ▶️ WEB ADMIN PANEL (37+ Controller)

**A. Auth & Profile**
| Controller | Path | Fungsi |
|---|---|---|
| `auth/Login` | `/auth/login` | Login username/password (web only) |
| `auth/Profile` | `/auth/profile` | Ubah profil web user |
| `user/Account` | `/user/account` | Ubah password akun |

**B. Master Data (Admin)**
| Controller | Path | Fungsi |
|---|---|---|
| `user/admin/User` | `/user/admin/user` | CRUD `master_user` + hak akses |
| `user/admin/Karyawan` | `/user/admin/karyawan` | CRUD data karyawan |
| `user/admin/Futsal` | `/user/admin/futsal` | CRUD harga master futsal |
| `user/admin/Badminton` | `/user/admin/badminton` | CRUD harga master badminton |
| `user/admin/Pickle` | `/user/admin/pickle` | CRUD harga master pickleball |

**C. Operasional GRO (Front Desk)**
| Controller | Path | Fungsi |
|---|---|---|
| `user/gro/Booking` | `/user/gro/booking` | Booking manual 3 cabang: Futsal/Badminton/Pickle (harian, bulanan, turnamen) |
| `user/gro/New_transaction` | `/user/gro/new_transaction` | Input membership baru (Gym, Swimming, Private, dll) + cetak struk PDF |
| `user/gro/Reservation_acceptanced` | `/user/gro/reservation_acceptanced` | Menerima/menyetujui booking yang masuk dari mobile app |
| `user/gro/transaction/Pickle (dan Futsal, Badminton, turunan bulanan/turnamen)` | Banyak controller | Form transaksi spesifik per cabang olahraga + print PDF |
| `user/gro/transaction/Cek_data_member` | `/user/gro/transaction/cek_data_member` | Cek validitas data member sebelum transaksi |

**D. Keuangan / Office**
| Controller | Path | Fungsi |
|---|---|---|
| `user/office/Pendapatan` | `/user/office/pendapatan` | Input, cari, rekap pendapatan per departemen + **export PDF via mPDF** |
| `user/office/Dashboard` | `/user/office/dashboard` | Ringkasan finansial + grafik |

**E. Big Data / Pimpinan (Super Admin)**
| Controller | Path | Fungsi |
|---|---|---|
| `user/Big_data` → `kpi` | `/user/big_data/kpi` | **KPI Dashboard** — penilaian karyawan per periode/bulan/divisi |
| `user/Big_data` → `kpi_assessment` | `/user/big_data/kpi_assessment` | Form assessment KPI (bobot, nilai, rekomendasi) |
| `user/Big_data` → `attendance_settings` | `/user/big_data/attendance_settings` | Set lokasi GPS + radius absen (peta interaktif) |
| `user/Big_data` → `attendance` | `/user/big_data/attendance` | Live monitoring absen karyawan (hadir/terlambat/izin) |
| `user/Big_data` → `attendance_archive` | `/user/big_data/attendance_archive` | Arsip absensi lama |
| `user/Big_data` → `attendance_sync_alpha` | `/user/big_data/attendance_sync_alpha` | **Sinkronisasi manual MySQL ↔ Firebase** |
| `user/Big_data` → `employee` | `/user/big_data/employee` | Kelola data karyawan + mapping Firebase UID |
| `user/Big_data` → `monitoring` | `/user/big_data/monitoring` | Dashboard monitoring seluruh aktivitas sistem |
| `user/Big_data` → `event_organizer` | `/user/big_data/event_organizer` | CRUD event/promo (sync ke mobile) |
| `user/Big_data` → `rating` | `/user/big_data/rating` | Lihat rating & review dari member |
| `user/Big_data` → `reservation` | `/user/big_data/reservation` | Monitor & kelola semua booking |
| `user/Big_data` → `data_reset` | `/user/big_data/data_reset` | Factory reset data spesifik per modul (berbahaya!) |

**F. Class Management (Sistem Kelas & Absensi QR)**
| Controller | Path | Fungsi |
|---|---|---|
| `Class_management` | `/class_management` | Dashboard kelas |
| `Class_management/categories` | Kategori kelas | CRUD class_categories |
| `Class_management/class_types` | Tipe kelas | CRUD class_types (private/group, durasi, harga) |
| `Class_management/sessions` | Sesi kelas | Jadwal sesi spesifik per tanggal |
| `Class_management/class_members` | Member per tipe | Enroll member ke kelas (gym/swim/private) |
| `Class_management/session_qr` | Generate QR | Cetak QR per sesi untuk ditempel di kelas |
| `Class_management/member_qr` | QR member | Lihat QR kartu member untuk scan |

**G. QR & Lainnya**
| Controller | Path | Fungsi |
|---|---|---|
| `Qr_code` | QR generator umum | Generate QR via `Ciqrcode` library |
| `user/Qrcode` | QR user spesifik | Kartu member QR, transaksi QR |
| `Redirect_halaman_utama` | `/` | Auto redirect sesuai role setelah login |
| `Open_file` | Viewer file | Buka file dari folder `file/` |
| `Maintenance` | Halaman maintenance | Tampilkan jika sistem off |

---

#### ▶️ MOBILE APP (40+ Screen)

**A. Auth Flow**
| Screen | Role | Fungsi |
|---|---|---|
| `SplashScreen` | All | Preload asset, cek session Firebase Auth |
| `LoginScreen` | All | Login email/password via Firebase Auth |
| `RegisterScreen` | All | Register user baru ke Firebase Auth + create Firestore doc `users/{uid}` |
| `LandingScreen` | User | Landing page setelah login |

**B. Role Admin (Karyawan / GRO / Trainer)**
| Screen | Fungsi |
|---|---|
| `AdminDashboard` | Menu utama admin |
| `AttendanceScreen` | Lihat daftar absensi karyawan hari ini |
| `ClassAttendanceScanScreen` | **Scan QR member** untuk absen di kelas (via `expo-camera`) → simpan ke `class_session_attendance` |
| `ClassSchedulingScreen` | Atur jadwal kelas mingguan |
| `UserReservationHistoryScreen` | Lihat semua booking member & status |
| `RatingMeScreen` | Lihat rating yang diberikan member ke trainer/admin |
| `GscPackageManageScreen` | **Scan paket member** → deduct jam pakai → update Firestore + log MySQL |
| `EventScreen` | Lihat event/promo |

**C. Role User (Member / Pelanggan)**
| Screen | Fungsi |
|---|---|
| `UserDashboard` | Beranda member: ringkasan paket, event, menu cepat |
| `FieldReservationScreen` | Booking lapangan online (pilih fasilitas → lapangan → tanggal → slot jam → booking) |
| `ClassScheduleScreen` | Lihat jadwal kelas & book sesi |
| `MyReservationHistoryScreen` | Riwayat booking (upcoming + selesai + dibatalkan) |
| `AttendanceScreen` | **Absensi karyawan GPS**: ambil lokasi, validasi radius, upload selfie |
| `GscPackageScreen` | Daftar paket membership yang dimiliki (sisa jam, masa berlaku) |
| `BuyPackageScreen` | Pembelian paket baru |
| `NotificationScreen` | Inbox push notifications (FCM) |
| `ETicketScreen` | Tampilkan QR code e-ticket booking (generate via `react-native-qrcode-svg`) |
| `ProfileScreen` | Edit profil + menu hapus akun |
| `RatingScreen` | Beri rating & ulasan setelah pemakaian |
| `CancellationRequestScreen` | Ajukan pembatalan booking |
| `ClassAttendanceScanScreen` | Scan QR sesi kelas untuk absen mandiri |
| `EventScreen` | Lihat promo & event GSC |

---

## 6. LIBRARY, DEPENDENSI, & 3RD-PARTY KEY

### 6.1 PHP Dependencies (Composer — `composer.json`)

| Package | Version | Fungsi |
|---|---|---|
| `mpdf/mpdf` | `^8.0` | **PDF Generator** — print struk booking, transaksi membership, laporan pendapatan, kartu member |
| `picqer/php-barcode-generator` | `*` | Generate barcode untuk transaksi/kartu |
| `endroid/qr-code` | `*` | Generate QR Code PHP (alternatif library custom `Ciqrcode`) |
| `firebase/php-jwt` | `*` | JWT encode/decode untuk verifikasi token Firebase di backend |
| `phpunit/phpunit` | dev | Unit testing |

### 6.2 CodeIgniter Custom Libraries (5 buah — folder `application/libraries/`)

| Library File | Fungsi Penting |
|---|---|
| `Template.php` | Custom view renderer: `$this->template->load($layout, $view, $data)` dengan 3 layout berbeda |
| `Firebase_admin.php` | **JEMBATAN HYBRID UTAMA** — wrapper Firestore REST API (Guzzle/HTTP) untuk PHP: `get_firestore_document()`, `get_all_documents()`, `create_firestore_document()`, `query_documents_by_field()` |
| `Firebase_sync.php` | Helper sinkronisasi batch MySQL ↔ Firestore untuk attendance, employee, dll |
| `Cloudinarylib.php` | Upload & delete asset ke Cloudinary via SDK PHP |
| `Ciqrcode.php` | Wrapper QR Code generator untuk CodeIgniter (generate PNG) |

### 6.3 CodeIgniter Custom Models (11 buah — folder `application/models/`)

| Model File | Tanggung Jawab |
|---|---|
| `M_data.php` | Generic pagination & count (base model) |
| `Datatables_model.php` | Server-side processing untuk jQuery DataTables |
| `Reservation_model.php` | CRUD booking: `get_facilities_with_courts()`, `get_available_hours()`, dll |
| `Attendance_model.php` | Absensi: `check_radius()`, `get_settings()`, `ensure_schema()` (migrasi kolom) |
| `Employee_model.php` | Data karyawan: `get_by_firebase_uid()` mapping employee ↔ Firebase |
| `Kpi_model.php` | KPI: `get_assessments()`, `get_kpi_stats()`, `get_divisions()` |
| `Class_management_model.php` | Seluruh CRUD sistem kelas (category/type/session/member) |
| `Event_model.php` | Event & promo (sync ke Firestore `events`) |
| `Monitoring_model.php` | Aggregate data untuk dashboard monitoring Big Data |
| `Bigdata_reset_model.php` | Truncate/reset tabel berdasarkan modul terpilih |

### 6.4 Mobile — Dependencies Kritis (`App/package.json`)

| Package | Versi | Fungsi |
|---|---|---|
| `expo` | 54.0.33 | Managed workflow core |
| `expo-camera` | 17.0.10 | Kamera: scan QR absen & paket |
| `expo-location` | 18.0.4 | GPS untuk radius attendance |
| `expo-notifications` | 0.32.16 | Push notif FCM via Expo |
| `expo-image-picker` | 17.0.10 | Pilih/upload foto (selfie) |
| `firebase` | 12.9.0 | Firebase SDK Web 9 modular (Auth, Firestore, Storage) |
| `@react-navigation/*` | 7.x | Native stack navigation |
| `@tanstack/react-query` | 5.101.2 | Server state + caching dengan persist (Async Storage) |
| `react-native-qrcode-svg` | 6.3.21 | Generate SVG QR untuk e-ticket & kartu member di layar |
| `lottie-react-native` | 7.3.5 | Animasi JSON Lottie (loading, success, dll) |
| `@react-native-async-storage/async-storage` | 2.2.0 | Local storage untuk session dan query cache |
| `expo-linear-gradient` | 15.0.8 | Gradient background untuk tampilan premium |
| `@expo/vector-icons` | 14.0.0 | Ikon premium (Ionicons, MaterialCommunity, dll) |

### 6.5 Frontend Web Assets (Bukan Dependency NPM murni, via CDN/Local Copy)

| Asset Folder | Fungsi |
|---|---|
| `assets/adminlte` | Dashboard template utama (AdminLTE 2.x) + jQuery |
| `assets/ckeditor` | WYSIWYG editor untuk text field panjang |
| `assets/lightbox2` | Preview gambar fullscreen (galeri) |
| `assets/sweetalert` | SweetAlert v1 — popup notifikasi interaktif |
| `assets/js/orgchart.js` | Bagan organisasi (untuk struktur karyawan) |
| `assets/gym` | Static HTML template landing page (referensi desain) |
| `assets/logo_fasilitas` | Logo PNG untuk tiap cabang olahraga |
| `assets/gambar` | Gambar barcode, logo, avatar user default |

---

## 7. ENDPOINT API REST (Mobile App ↔ Backend CodeIgniter)

Semua endpoint ada di folder `application/controllers/api/`. Semua otomatis punya CORS header `Access-Control-Allow-Origin: *` + handling preflight `OPTIONS`.

### 7.1 Auth API (`api/Auth.php`)
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/auth/get_email_by_phone?phone=` | Cari email akun Firebase berdasarkan nomor HP (untuk login via HP) |

### 7.2 Reservation API (`api/Reservation.php`)
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/reservation/facilities` | Daftar fasilitas olahraga (Futsal, Badminton, Pickle, dll) |
| GET | `/api/reservation/courts?facility_id=` | Daftar lapangan per fasilitas |
| GET | `/api/reservation/availability?court_id=&date=` | Slot jam tersedia pada tanggal & lapangan spesifik |
| POST | `/api/reservation/create` | **Create booking** (firebase_uid + detail lap + time_slots[]) |

### 7.3 Attendance API (`api/Attendance.php`)
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/attendance/settings` | Ambil konfigurasi lokasi + radius absensi |
| POST | `/api/attendance/check_radius` | Hitung jarak Haversine user vs lokasi absen → bool isWithinRange |
| POST | `/api/attendance/save` | **Check-in**: insert data absen (selfie_url, GPS, employee_uid) |
| POST | `/api/attendance/checkout` | Check-out (update jam pulang) |
| GET | `/api/attendance/history?employee_uid=&date=` | Riwayat absensi per karyawan |
| GET | `/api/attendance/schedule_today` | Jadwal hari ini |
| GET | `/api/attendance/sync_alpha` | Sinkron MySQL ↔ Firestore (full sync batch) |

### 7.4 GSC Package API (`api/Gsc_package.php`)
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/gsc_package/get_all_packages` | List seluruh paket (Firestore `gsc_packages`) |
| GET | `/api/gsc_package/get_package?id=` | Detail paket by ID |
| POST | `/api/gsc_package/deduct_hours` | **Kurangi jam pakai** → update Firestore + log MySQL `financial_transactions` + insert `gsc_package_usage` |

### 7.5 Class Schedule API (`api/Class_schedule.php`)
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/class_schedule` | List semua jadwal kelas mingguan |
| GET | `/api/class_schedule?date=` | Jadwal kelas untuk tanggal spesifik |
| POST | `/api/class_schedule/create` | Tambah jadwal |
| POST | `/api/class_schedule/scan_attendance` | Scan QR absen member per sesi |

### 7.6 Event API (`api/Event.php`)
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/event` | List event aktif (untuk mobile) |
| POST | `/api/event/create` | Tambah event |

### 7.7 Notification API (`api/Notification.php`)
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/notification?user_uid=` | List notifikasi user dari Firestore `notifications` |
| POST | `/api/notification/send` | Kirim push via FCM |

### 7.8 Finance API (`api/Finance.php`)
| — | `/api/finance/*` | Endpoint keuangan (laporan, log transaksi) |

---

## 8. STRUKTUR FILE UPLOAD (Static Storage Lokal)

| Folder Path | Isi File | Jumlah Estimasi |
|---|---|---|
| `/file/qrcode/` | QR code generate per transaksi/absen | **160+ file PNG** (format: `YYMMDDHHMMSSIDX.png`) |
| `/file/kartu/` | Output cetak kartu member (PNG/JPG overlay dengan frame) | 40+ file |
| `/file/member/` | Foto upload member terbaru | ~19 file PNG |
| `/file/user/` | Dokumen pendukung user (PDF/JPG KTP, dll) | 8 file |
| `/file/qrkartu/`, `/file/qrpublic/` | Asset frame QR & public QR | 3 file |
| `/file/ringtone/` | Audio notifikasi custom: `dana.mp3`, `gojek.mp3`, `wrong.wav` | 3 file |
| `/file/cover/` | Banner cover untuk halaman print | 1 file |
| `/db/gsc_robby.sql` | Full dump database MySQL | 1 file (sumber truth skema) |

---

## 9. KEY KONFIGURASI LINGKUNGAN

| File | Konfigurasi Kunci |
|---|---|
| `application/config/config.php` | `$config['base_url']` = `https://management.g-sportscenter.com/`<br>`$config['class_qr_secret']` = HMAC secret untuk payload QR absensi kelas **(GANTI DI PRODUKSI!)**<br>`$config['sess_driver']` = `files`<br>`$config['enable_hooks']` = `TRUE` (untuk CORS hook) |
| `application/config/database.php` | Host=localhost, DB=`gsc_robby`, User=`root`, Pass=`` (dev kosong) |
| `application/config/routes.php` | Mapping endpoint REST API + custom route KPI, Absen, dll |
| `application/hooks/Cors.php` | Hook `enable_cors` — global Allow-Origin: * (sebaiknya di-restrict domain di production) |
| `App/src/config/api.js` | Resolver base URL API priority: 1) `EXPO_PUBLIC_API_BASE_URL` env → 2) IP Metro bundler Expo → 3) `10.0.2.2` (Android Emu) → 4) production `https://management.g-sportscenter.com/api` |
| `App/src/config/firebase.js` | FirebaseConfig object (apiKey, projectId, dll) + initializeAuth with AsyncStorage persistence |
| `App/app.json` | Bundle ID/Package, permissions Android (CAMERA, RECORD_AUDIO), EAS project |
| `.htaccess` | URL rewrite + security headers |

---

## 10. CATATAN PENTING UNTUK PENGEMBANG / MAINTENANCE

1. **HYBRID ARCHITECTURE TRUTH SOURCE**: Data keuangan & transaksi inti **sumber truth = MySQL**. Data realtime & mobile-only **sumber truth = Firebase**. Semua cross-writing melewati Backend CodeIgniter, JANGAN mobile app langsung tulis ke MySQL (tidak mungkin, tidak ada driver).

2. **Sync Dua Arah Manual**: Gunakan menu `Big Data → Sync Alpha` atau endpoint `/api/attendance/sync_alpha` jika ada inkonsistensi data absen.

3. **Gantilah Secrets Production**:
   - `$config['class_qr_secret']` di `application/config/config.php`
   - `$config['encryption_key']` (kosong — HARUS diisi jika pakai Encryption Class)
   - Batasi `Access-Control-Allow-Origin` dari `*` menjadi domain spesifik
   - Database username/password production JANGAN `root` tanpa password

4. **Upload Limit PHP**: Pastikan `upload_max_filesize` dan `post_max_size` cukup besar untuk foto member dan file PDF.

5. **Build Mobile APK/IPA**:
   ```bash
   cd App
   npm install
   # Development
   npx expo start
   # Production build via EAS
   npx eas build -p android
   npx eas build -p ios
   ```

6. **Cara Menambah Hak Akses Baru**:
   - Insert ke tabel `hak_akses` (nama + deskripsi)
   - Berikan ke user via menu User Management atau insert `hak_akses_user`
   - Update logic redirect di `Redirect_halaman_utama.php` dan dashboard template

7. **Modul Transaksi GRO**: Tiap cabang olahraga (Futsal, Badminton, Pickle) punya 3 jalur transaksi terpisah: **Harian**, **Bulanan**, **Turnamen** — masing-masing punya controller, model, dan print PDF sendiri. Perubahan harga harap cek **3 tempat**: `master_*`, form transaksi, dan controller print.

8. **Sistem Kelas via QR**: Setiap `class_sessions` punya token QR unik. Member/admin scan QR → backend validasi session aktif → insert `class_session_attendance`. HMAC secret wajib diganti untuk mencegah QR palsu.

---

## 11. RINGKASAN TEKNOLOGI DALAM 1 KALIMAT

> **Sistem Manajemen G-Sports Center (Robby) adalah aplikasi hybrid multi-platform berbasis CodeIgniter 3 (PHP Web Admin) + Expo React Native 54 (Mobile App) dengan arsitektur multi-database hybrid MySQL (69 tabel transaksional) + Firebase Firestore/Auth/FCM (realtime mobile sync) + Cloudinary (media CDN), mencakup 3 cabang olahraga utama (Futsal/Badminton/Pickleball) + Gym/Swimming/Martial Classes + 6 Role RBAC (Pimpinan/Admin/GRO/Gym/Office/Swim) + 40+ modul fitur (Booking, Membership, Absensi GPS QR, KPI, Keuangan, Kelas, Rating, Event, GSC Hourly Package) yang terintegrasi melalui REST API dengan sinkronisasi dua arah.**

---

> **Saat ini google sign auth sudah berhasil dan sempurna tanpa kendala**

*Dokumen ini adalah kesimpulan detail hasil analisa seluruh struktur kode, konfigurasi, database, dan flow sistem. Semua referensi berdasarkan file yang ada di proyek pada tanggal analisa.*
