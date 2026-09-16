'use client';

import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { MapPin, Download, Search, Settings } from 'lucide-react';
import { db } from '@/lib/firebase';
import { collection, getDocs, doc, setDoc, getDoc, query, orderBy, writeBatch } from 'firebase/firestore';
import { toast } from 'sonner';
import dynamic from 'next/dynamic';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

const LocationMap = dynamic(
  () => import('@/components/location-map').then((mod) => ({ default: mod.LocationMap })),
  { ssr: false }
);

interface AttendanceSettings {
  latitude: number;
  longitude: number;
  radius: number;
  locationName: string;
}

interface Attendance {
  id: string;
  employeeId: string;
  employeeName: string;
  date: string;
  checkIn: string;
  checkOut?: string;
  status: 'hadir' | 'terlambat' | 'izin' | 'alpha';
  location?: {
    latitude: number;
    longitude: number;
  };
}

export default function BigDataAbsenPage() {
  const [attendances, setAttendances] = useState<Attendance[]>([]);
  const [settings, setSettings] = useState<AttendanceSettings>({
    latitude: -6.200000,
    longitude: 106.816666,
    radius: 100,
    locationName: 'G Sports Center'
  });
  const [searchTerm, setSearchTerm] = useState('');
  const [filterStatus, setFilterStatus] = useState('all');
  const [settingsOpen, setSettingsOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const [archiving, setArchiving] = useState(false);

  useEffect(() => {
    fetchSettings();
    fetchAttendances();
  }, []);

  const fetchSettings = async () => {
    try {
      const docRef = doc(db, 'settings', 'attendance');
      const docSnap = await getDoc(docRef);
      if (docSnap.exists()) {
        setSettings(docSnap.data() as AttendanceSettings);
      }
    } catch (error) {
      console.error('Error fetching settings:', error);
    }
  };

  const fetchAttendances = async () => {
    try {
      const q = query(collection(db, 'attendances'), orderBy('date', 'desc'));
      const querySnapshot = await getDocs(q);
      const data = querySnapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      })) as Attendance[];
      setAttendances(data);
    } catch (error) {
      console.error('Error fetching attendances:', error);
    }
  };

  const handleSaveSettings = async () => {
    setLoading(true);
    try {
      await setDoc(doc(db, 'settings', 'attendance'), settings);
      toast.success('Pengaturan lokasi berhasil disimpan!');
      setSettingsOpen(false);
    } catch (error) {
      console.error('Error saving settings:', error);
      toast.error('Gagal menyimpan pengaturan');
    } finally {
      setLoading(false);
    }
  };

  const getCurrentLocation = () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          setSettings({
            ...settings,
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
          });
          toast.success('Lokasi berhasil diambil!');
        },
        (error) => {
          toast.error('Gagal mengambil lokasi');
        }
      );
    } else {
      toast.error('Browser tidak mendukung geolocation');
    }
  };

  const handleLocationSelect = (lat: number, lng: number) => {
    setSettings({
      ...settings,
      latitude: lat,
      longitude: lng,
    });
    toast.success('Lokasi berhasil dipilih!');
  };

  const filteredAttendances = attendances.filter(att => {
    const matchSearch = att.employeeName.toLowerCase().includes(searchTerm.toLowerCase());
    const matchStatus = filterStatus === 'all' || att.status === filterStatus;
    return matchSearch && matchStatus;
  });

  const exportToPDF = () => {
    try {
      const docPdf = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });
      const today = new Date();
      const dateLabel = today.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });

      docPdf.setFontSize(16);
      docPdf.text('Laporan Absensi Karyawan', 40, 50);
      docPdf.setFontSize(10);
      docPdf.text(`Tanggal cetak: ${dateLabel}`, 40, 68);
      docPdf.text(`Lokasi: ${settings.locationName} | Radius: ${settings.radius}m`, 40, 82);

      const rows = filteredAttendances.map((att) => [
        att.employeeName,
        att.date,
        att.checkIn,
        att.checkOut || '-',
        att.status,
      ]);

      autoTable(docPdf, {
        startY: 100,
        head: [['Nama Karyawan', 'Tanggal', 'Check In', 'Check Out', 'Status']],
        body: rows,
        theme: 'grid',
        styles: {
          fontSize: 9,
          cellPadding: 6,
        },
        headStyles: {
          fillColor: [220, 38, 38],
          textColor: [255, 255, 255],
        },
      });

      const fileDate = new Date().toISOString().split('T')[0];
      docPdf.save(`absensi-${fileDate}.pdf`);
      toast.success('PDF berhasil diunduh');
    } catch (e) {
      console.error('Export PDF error:', e);
      toast.error('Gagal export PDF');
    }
  };

  const archiveToday = async () => {
    const todayStr = new Date().toISOString().split('T')[0];
    const todays = attendances.filter((a) => a.date === todayStr);

    if (todays.length === 0) {
      toast.error('Tidak ada data absensi hari ini untuk diarsipkan');
      return;
    }

    if (!confirm(`Arsipkan ${todays.length} data absensi hari ini? Data akan dipindahkan ke arsip.`)) return;

    setArchiving(true);
    try {
      const batch = writeBatch(db);
      todays.forEach((att) => {
        const srcRef = doc(db, 'attendances', att.id);
        const dstRef = doc(db, 'attendances_archive', `${todayStr}_${att.id}`);
        batch.set(dstRef, {
          ...att,
          archivedAt: new Date().toISOString(),
        });
        batch.delete(srcRef);
      });
      await batch.commit();
      toast.success('Absensi hari ini berhasil diarsipkan');
      fetchAttendances();
    } catch (e) {
      console.error('Archive error:', e);
      toast.error('Gagal mengarsipkan absensi');
    } finally {
      setArchiving(false);
    }
  };

  const todayAttendances = attendances.filter(att => att.date === new Date().toISOString().split('T')[0]);
  const statusCounts = {
    hadir: todayAttendances.filter(a => a.status === 'hadir').length,
    terlambat: todayAttendances.filter(a => a.status === 'terlambat').length,
    izin: todayAttendances.filter(a => a.status === 'izin').length,
    alpha: todayAttendances.filter(a => a.status === 'alpha').length,
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'hadir':
        return 'bg-green-500/20 text-green-400 border border-green-500/30';
      case 'terlambat':
        return 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30';
      case 'izin':
        return 'bg-blue-500/20 text-blue-400 border border-blue-500/30';
      case 'alpha':
        return 'bg-red-500/20 text-red-400 border border-red-500/30';
      default:
        return '';
    }
  };

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-white mb-2">Manage Absen</h1>
          <p className="text-gray-400">Kelola data kehadiran karyawan</p>
        </div>
        <div className="flex gap-2">
          <Dialog open={settingsOpen} onOpenChange={setSettingsOpen}>
            <DialogTrigger asChild>
              <Button className="bg-red-500 hover:bg-red-600 text-white gap-2">
                <Settings size={16} />
                Pengaturan Lokasi
              </Button>
            </DialogTrigger>
            <DialogContent className="bg-black border-red-500/20 max-w-3xl max-h-[90vh] overflow-y-auto">
              <DialogHeader>
                <DialogTitle className="text-white">Pengaturan Lokasi Absensi</DialogTitle>
              </DialogHeader>
              <div className="space-y-4">
                <div>
                  <Label className="text-white">Nama Lokasi</Label>
                  <Input
                    value={settings.locationName}
                    onChange={(e) => setSettings({ ...settings, locationName: e.target.value })}
                    className="bg-black/20 border-red-500/30 text-white"
                    placeholder="Nama lokasi"
                  />
                </div>
                
                <div>
                  <Label className="text-white">Pilih Lokasi di Map</Label>
                  <p className="text-xs text-gray-400 mb-2">Klik di map untuk memilih lokasi absensi</p>
                  <LocationMap
                    latitude={settings.latitude}
                    longitude={settings.longitude}
                    radius={settings.radius}
                    onLocationSelect={handleLocationSelect}
                  />
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label className="text-white">Latitude</Label>
                    <Input
                      type="number"
                      step="0.000001"
                      value={settings.latitude}
                      onChange={(e) => setSettings({ ...settings, latitude: parseFloat(e.target.value) })}
                      className="bg-black/20 border-red-500/30 text-white"
                    />
                  </div>
                  <div>
                    <Label className="text-white">Longitude</Label>
                    <Input
                      type="number"
                      step="0.000001"
                      value={settings.longitude}
                      onChange={(e) => setSettings({ ...settings, longitude: parseFloat(e.target.value) })}
                      className="bg-black/20 border-red-500/30 text-white"
                    />
                  </div>
                </div>

                <div>
                  <Label className="text-white">Radius (meter)</Label>
                  <Input
                    type="number"
                    value={settings.radius}
                    onChange={(e) => setSettings({ ...settings, radius: parseInt(e.target.value) })}
                    className="bg-black/20 border-red-500/30 text-white"
                    placeholder="Radius dalam meter"
                  />
                  <p className="text-xs text-gray-400 mt-1">Karyawan hanya bisa absen dalam radius ini</p>
                </div>

                <Button
                  type="button"
                  variant="outline"
                  onClick={getCurrentLocation}
                  className="w-full border-red-500/30 text-white hover:bg-red-500/20"
                >
                  <MapPin size={16} className="mr-2" />
                  Gunakan Lokasi Saya Saat Ini
                </Button>

                <div className="bg-black/40 p-4 rounded-lg border border-red-500/20">
                  <p className="text-sm text-white mb-1">Koordinat Terpilih:</p>
                  <p className="text-xs text-gray-400">
                    Lat: {settings.latitude.toFixed(6)}, Long: {settings.longitude.toFixed(6)}
                  </p>
                  <p className="text-xs text-gray-400 mt-1">
                    Radius: {settings.radius} meter
                  </p>
                </div>

                <Button
                  onClick={handleSaveSettings}
                  disabled={loading}
                  className="w-full bg-red-500 hover:bg-red-600 text-white"
                >
                  {loading ? 'Menyimpan...' : 'Simpan Pengaturan'}
                </Button>
              </div>
            </DialogContent>
          </Dialog>
          <Button
            variant="outline"
            className="border-red-500/30 text-white hover:bg-red-500/20 gap-2"
            onClick={exportToPDF}
          >
            <Download size={16} />
            Export PDF
          </Button>
          <Button
            variant="outline"
            className="border-red-500/30 text-white hover:bg-red-500/20"
            onClick={archiveToday}
            disabled={archiving}
          >
            {archiving ? 'Mengarsipkan...' : 'Arsipkan Hari Ini'}
          </Button>
        </div>
      </div>

      <Card className="bg-black/40 border-red-500/20">
        <CardContent className="pt-6">
          <div className="flex items-center gap-4">
            <div className="p-3 bg-red-500/20 rounded-lg">
              <MapPin className="w-6 h-6 text-red-500" />
            </div>
            <div className="flex-1">
              <p className="text-sm text-gray-400">Lokasi Absensi Aktif</p>
              <p className="text-lg font-semibold text-white">{settings.locationName}</p>
              <p className="text-xs text-gray-400">
                Radius: {settings.radius}m | Koordinat: {settings.latitude.toFixed(6)}, {settings.longitude.toFixed(6)}
              </p>
            </div>
          </div>
        </CardContent>
      </Card>

      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card className="bg-black/40 border-red-500/20">
          <CardContent className="pt-6">
            <p className="text-sm text-gray-400">Hadir Hari Ini</p>
            <p className="text-2xl font-bold text-green-400">{statusCounts.hadir}</p>
          </CardContent>
        </Card>
        <Card className="bg-black/40 border-red-500/20">
          <CardContent className="pt-6">
            <p className="text-sm text-gray-400">Terlambat</p>
            <p className="text-2xl font-bold text-yellow-400">{statusCounts.terlambat}</p>
          </CardContent>
        </Card>
        <Card className="bg-black/40 border-red-500/20">
          <CardContent className="pt-6">
            <p className="text-sm text-gray-400">Izin</p>
            <p className="text-2xl font-bold text-blue-400">{statusCounts.izin}</p>
          </CardContent>
        </Card>
        <Card className="bg-black/40 border-red-500/20">
          <CardContent className="pt-6">
            <p className="text-sm text-gray-400">Alpha</p>
            <p className="text-2xl font-bold text-red-400">{statusCounts.alpha}</p>
          </CardContent>
        </Card>
      </div>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <div className="flex flex-col md:flex-row gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
              <Input
                placeholder="Cari karyawan..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10 bg-black/20 border-red-500/30 text-white"
              />
            </div>
            <Select value={filterStatus} onValueChange={setFilterStatus}>
              <SelectTrigger className="w-full md:w-48 bg-black/20 border-red-500/30 text-white">
                <SelectValue />
              </SelectTrigger>
              <SelectContent className="bg-black border-red-500/30">
                <SelectItem value="all" className="text-white hover:bg-red-500/20">Semua Status</SelectItem>
                <SelectItem value="hadir" className="text-white hover:bg-red-500/20">Hadir</SelectItem>
                <SelectItem value="terlambat" className="text-white hover:bg-red-500/20">Terlambat</SelectItem>
                <SelectItem value="izin" className="text-white hover:bg-red-500/20">Izin</SelectItem>
                <SelectItem value="alpha" className="text-white hover:bg-red-500/20">Alpha</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-red-500/20">
                  <th className="text-left py-3 px-4 font-semibold text-white">Nama Karyawan</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Tanggal</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Check In</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Check Out</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Status</th>
                </tr>
              </thead>
              <tbody>
                {filteredAttendances.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="text-center py-12 text-gray-400">
                      Belum ada data absensi
                    </td>
                  </tr>
                ) : (
                  filteredAttendances.map((att) => (
                    <tr key={att.id} className="border-b border-red-500/10 hover:bg-red-500/5">
                      <td className="py-4 px-4">
                        <p className="font-medium text-white">{att.employeeName}</p>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{att.date}</p>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{att.checkIn}</p>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{att.checkOut || '-'}</p>
                      </td>
                      <td className="py-4 px-4">
                        <Badge className={getStatusColor(att.status)}>
                          {att.status.charAt(0).toUpperCase() + att.status.slice(1)}
                        </Badge>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
