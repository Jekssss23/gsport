# Sistem Absensi Berbasis Lokasi/Radius

## Overview
Sistem absensi yang menggunakan GPS/lokasi untuk memastikan karyawan berada di area yang ditentukan saat melakukan absensi.

## Fitur Utama

### 1. Website (Admin - Manage Absen)
- ✅ Set lokasi absensi dengan koordinat GPS
- ✅ Tentukan radius area absensi (dalam meter)
- ✅ Preview lokasi di Google Maps
- ✅ Gunakan lokasi saat ini (browser location)
- ✅ Lihat data absensi real-time
- ✅ Filter dan search absensi
- ✅ Export data absensi

### 2. Mobile App (Karyawan)
- ✅ Check-in berbasis lokasi
- ✅ Check-out otomatis
- ✅ Validasi radius area
- ✅ Status absensi (Hadir/Terlambat)
- ✅ Riwayat absensi hari ini
- ✅ Visual indicator in/out of range

## Setup

### A. Website - Pengaturan Lokasi Absensi

1. Login sebagai admin
2. Masuk ke Big Data → Manage Absen
3. Klik "Pengaturan Lokasi"
4. Isi data:
   - **Nama Lokasi**: Nama tempat (contoh: G Sports Center)
   - **Latitude**: Koordinat latitude
   - **Longitude**: Koordinat longitude
   - **Radius**: Jarak dalam meter (contoh: 100)
5. Atau klik "Gunakan Lokasi Saat Ini" untuk auto-detect
6. Preview lokasi di map
7. Klik "Simpan Pengaturan"

### B. Mobile App - Install Dependencies

```bash
cd GSC
npm install
# atau
npx expo install expo-location
```

### C. Permissions (Mobile)

App akan otomatis request permission:
- **Android**: Location permission
- **iOS**: Location permission

## Cara Kerja

### 1. Admin Set Lokasi (Website)
```
Admin → Big Data → Manage Absen → Pengaturan Lokasi
↓
Set koordinat (lat, long) + radius
↓
Simpan ke Firebase: settings/attendance
```

### 2. Karyawan Absen (Mobile App)
```
Karyawan → Dashboard → Attendance
↓
App ambil lokasi GPS karyawan
↓
Hitung jarak ke lokasi absensi
↓
Jika dalam radius → Bisa check-in
Jika di luar radius → Tidak bisa check-in
↓
Check-in berhasil → Data masuk Firebase
↓
Tampil di website Manage Absen
```

## Struktur Database Firebase

### Collection: `settings`
```javascript
{
  attendance: {
    latitude: -6.200000,
    longitude: 106.816666,
    radius: 100,
    locationName: "G Sports Center"
  }
}
```

### Collection: `attendances`
```javascript
{
  employeeId: "user_uid",
  employeeName: "John Doe",
  date: "2024-01-15",
  checkIn: "08:00",
  checkOut: "17:00", // optional
  status: "hadir", // hadir, terlambat, izin, alpha
  location: {
    latitude: -6.200000,
    longitude: 106.816666
  },
  createdAt: Timestamp
}
```

## Status Absensi

1. **Hadir**: Check-in sebelum atau tepat jam 08:00
2. **Terlambat**: Check-in setelah jam 08:00
3. **Izin**: Manual input oleh admin
4. **Alpha**: Tidak ada check-in

## Formula Perhitungan Jarak

Menggunakan **Haversine Formula**:
```javascript
const R = 6371e3; // Earth radius in meters
const φ1 = (lat1 * Math.PI) / 180;
const φ2 = (lat2 * Math.PI) / 180;
const Δφ = ((lat2 - lat1) * Math.PI) / 180;
const Δλ = ((lon2 - lon1) * Math.PI) / 180;

const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
          Math.cos(φ1) * Math.cos(φ2) *
          Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

const distance = R * c; // Distance in meters
```

## Troubleshooting

### Mobile App

**Error: "Location permission denied"**
- Solution: Buka Settings → Apps → GSC → Permissions → Enable Location

**Error: "Out of Range"**
- Solution: Pastikan berada dalam radius yang ditentukan
- Cek jarak di app (ditampilkan dalam meter)

**Error: "Failed to get location"**
- Solution: 
  - Pastikan GPS aktif
  - Pastikan di area terbuka (bukan basement)
  - Restart app

### Website

**Map tidak muncul**
- Solution: Cek koneksi internet
- Pastikan browser support iframe

**Lokasi tidak akurat**
- Solution: 
  - Gunakan desktop/laptop untuk set lokasi
  - Atau input koordinat manual dari Google Maps

## Tips

1. **Radius Optimal**: 50-200 meter
   - Terlalu kecil: Karyawan susah check-in
   - Terlalu besar: Kurang akurat

2. **Koordinat Akurat**:
   - Buka Google Maps
   - Klik kanan di lokasi
   - Copy koordinat (lat, long)

3. **Testing**:
   - Test di lokasi sebenarnya
   - Coba dari berbagai titik dalam radius
   - Pastikan GPS akurat

## Security

- ✅ Lokasi karyawan tersimpan di database
- ✅ Validasi radius di client & server
- ✅ Timestamp otomatis
- ✅ Tidak bisa check-in 2x di hari yang sama
- ✅ Data terenkripsi (Firebase)

## Future Improvements

- [ ] Face recognition saat check-in
- [ ] Geofencing notification
- [ ] Multiple location support
- [ ] Shift management
- [ ] Overtime calculation
- [ ] Leave request integration
