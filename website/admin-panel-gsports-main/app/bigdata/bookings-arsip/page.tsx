'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Download, Search, Trash2 } from 'lucide-react';
import { db } from '@/lib/firebase';
import {
  collection,
  deleteDoc,
  doc,
  getDocs,
  orderBy,
  query,
  where,
  writeBatch,
} from 'firebase/firestore';
import { toast } from 'sonner';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

type FilterMode = 'range' | 'monthly' | 'weekly' | 'daily';

interface BookingArchive {
  id: string;
  userName: string;
  field: string;
  date: string;
  time: string;
  status: 'confirmed' | 'rejected' | 'cancelled';
  archivedAt?: string;
}

const toIsoDate = (d: Date) => d.toISOString().split('T')[0];

const getWeekRangeIso = (anyDay: Date) => {
  const d = new Date(anyDay);
  d.setHours(0, 0, 0, 0);
  const day = d.getDay();
  const diffToMonday = (day + 6) % 7;
  const start = new Date(d);
  start.setDate(d.getDate() - diffToMonday);
  const end = new Date(start);
  end.setDate(start.getDate() + 6);
  return { start: toIsoDate(start), end: toIsoDate(end) };
};

const getMonthRangeIso = (yearMonth: string) => {
  const [y, m] = yearMonth.split('-').map((v) => parseInt(v, 10));
  const start = new Date(y, m - 1, 1);
  const end = new Date(y, m, 0);
  return { start: toIsoDate(start), end: toIsoDate(end) };
};

export default function BigDataBookingsArsipPage() {
  const [items, setItems] = useState<BookingArchive[]>([]);
  const [loading, setLoading] = useState(false);
  const [deletingAll, setDeletingAll] = useState(false);

  const [filterMode, setFilterMode] = useState<FilterMode>('monthly');
  const [searchTerm, setSearchTerm] = useState('');
  const [filterStatus, setFilterStatus] = useState('all');

  const todayIso = useMemo(() => toIsoDate(new Date()), []);
  const [dayIso, setDayIso] = useState(todayIso);
  const [weekIso, setWeekIso] = useState(todayIso);
  const [monthIso, setMonthIso] = useState(todayIso.slice(0, 7));
  const [rangeStart, setRangeStart] = useState(todayIso);
  const [rangeEnd, setRangeEnd] = useState(todayIso);

  const { startIso, endIso } = useMemo(() => {
    if (filterMode === 'daily') {
      return { startIso: dayIso, endIso: dayIso };
    }
    if (filterMode === 'weekly') {
      const r = getWeekRangeIso(new Date(weekIso));
      return { startIso: r.start, endIso: r.end };
    }
    if (filterMode === 'monthly') {
      const r = getMonthRangeIso(monthIso);
      return { startIso: r.start, endIso: r.end };
    }
    return {
      startIso: rangeStart || '0000-01-01',
      endIso: rangeEnd || '9999-12-31',
    };
  }, [dayIso, filterMode, monthIso, rangeEnd, rangeStart, weekIso]);

  const fetchArchive = useCallback(async () => {
    setLoading(true);
    try {
      const colRef = collection(db, 'bookings_archive');
      const q = query(
        colRef,
        where('date', '>=', startIso),
        where('date', '<=', endIso),
        orderBy('date', 'desc')
      );
      const snap = await getDocs(q);
      const data = snap.docs.map((d) => ({ ...(d.data() as any), id: d.id })) as BookingArchive[];
      setItems(data);
    } catch (e) {
      console.error('Fetch archive error:', e);
      toast.error('Gagal mengambil data arsip booking');
    } finally {
      setLoading(false);
    }
  }, [endIso, startIso]);

  useEffect(() => {
    fetchArchive();
  }, [fetchArchive]);

  const filtered = useMemo(() => {
    return items.filter((booking) => {
      const matchSearch = (booking.userName || '').toLowerCase().includes(searchTerm.toLowerCase());
      const matchStatus = filterStatus === 'all' || booking.status === filterStatus;
      return matchSearch && matchStatus;
    });
  }, [filterStatus, items, searchTerm]);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'confirmed':
        return 'bg-green-500/20 text-green-400 border border-green-500/30';
      case 'rejected':
        return 'bg-red-500/20 text-red-400 border border-red-500/30';
      case 'cancelled':
        return 'bg-orange-500/20 text-orange-400 border border-orange-500/30';
      default:
        return '';
    }
  };

  const exportToPDF = () => {
    try {
      const docPdf = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });
      const printedAt = new Date().toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });

      docPdf.setFontSize(16);
      docPdf.text('Arsip Booking', 40, 50);
      docPdf.setFontSize(10);
      docPdf.text(`Tanggal cetak: ${printedAt}`, 40, 68);
      docPdf.text(`Periode: ${startIso} s/d ${endIso}`, 40, 82);

      const rows = filtered.map((booking) => [
        booking.userName,
        booking.field,
        booking.date,
        booking.time,
        booking.status,
      ]);

      autoTable(docPdf, {
        startY: 100,
        head: [['Nama User', 'Lapangan', 'Tanggal', 'Waktu', 'Status']],
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
      docPdf.save(`arsip-booking-${fileDate}.pdf`);
      toast.success('PDF berhasil diunduh');
    } catch (e) {
      console.error('Export archive PDF error:', e);
      toast.error('Gagal export PDF');
    }
  };

  const deleteOne = async (id: string) => {
    if (!confirm('Hapus data arsip booking ini secara permanen?')) return;
    try {
      await deleteDoc(doc(db, 'bookings_archive', id));
      toast.success('Data arsip booking berhasil dihapus');
      setItems((prev) => prev.filter((x) => x.id !== id));
    } catch (e) {
      console.error('Delete archive item error:', e);
      toast.error('Gagal menghapus data arsip booking');
    }
  };

  const deleteFiltered = async () => {
    if (filtered.length === 0) {
      toast.error('Tidak ada data untuk dihapus');
      return;
    }

    if (!confirm(`Hapus ${filtered.length} data arsip booking yang sedang tampil secara permanen?`)) return;

    setDeletingAll(true);
    try {
      const batch = writeBatch(db);
      filtered.forEach((booking) => {
        batch.delete(doc(db, 'bookings_archive', booking.id));
      });
      await batch.commit();
      toast.success('Data arsip booking terhapus');
      await fetchArchive();
    } catch (e) {
      console.error('Bulk delete archive error:', e);
      toast.error('Gagal menghapus data arsip booking');
    } finally {
      setDeletingAll(false);
    }
  };

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-white mb-2">Arsip Booking</h1>
          <p className="text-gray-400">Lihat histori booking berdasarkan periode</p>
        </div>
        <div className="flex gap-2">
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
            className="border-red-500/30 text-white hover:bg-red-500/20 gap-2"
            onClick={deleteFiltered}
            disabled={deletingAll}
          >
            <Trash2 size={16} />
            {deletingAll ? 'Menghapus...' : 'Hapus (Filter)'}
          </Button>
        </div>
      </div>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <CardTitle className="text-white">Filter Periode</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <Label className="text-white">Mode</Label>
              <Select value={filterMode} onValueChange={(v) => setFilterMode(v as FilterMode)}>
                <SelectTrigger className="w-full bg-black/20 border-red-500/30 text-white">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent className="bg-black border-red-500/30">
                  <SelectItem value="daily" className="text-white hover:bg-red-500/20">Harian</SelectItem>
                  <SelectItem value="weekly" className="text-white hover:bg-red-500/20">Mingguan</SelectItem>
                  <SelectItem value="monthly" className="text-white hover:bg-red-500/20">Bulanan</SelectItem>
                  <SelectItem value="range" className="text-white hover:bg-red-500/20">Range</SelectItem>
                </SelectContent>
              </Select>
            </div>

            {filterMode === 'daily' && (
              <div>
                <Label className="text-white">Tanggal</Label>
                <Input
                  type="date"
                  value={dayIso}
                  onChange={(e) => setDayIso(e.target.value)}
                  className="bg-black/20 border-red-500/30 text-white"
                />
              </div>
            )}

            {filterMode === 'weekly' && (
              <div>
                <Label className="text-white">Pilih Tanggal (dalam minggu)</Label>
                <Input
                  type="date"
                  value={weekIso}
                  onChange={(e) => setWeekIso(e.target.value)}
                  className="bg-black/20 border-red-500/30 text-white"
                />
              </div>
            )}

            {filterMode === 'monthly' && (
              <div>
                <Label className="text-white">Bulan</Label>
                <Input
                  type="month"
                  value={monthIso}
                  onChange={(e) => setMonthIso(e.target.value)}
                  className="bg-black/20 border-red-500/30 text-white"
                />
              </div>
            )}

            {filterMode === 'range' && (
              <>
                <div>
                  <Label className="text-white">Dari</Label>
                  <Input
                    type="date"
                    value={rangeStart}
                    onChange={(e) => setRangeStart(e.target.value)}
                    className="bg-black/20 border-red-500/30 text-white"
                  />
                </div>
                <div>
                  <Label className="text-white">Sampai</Label>
                  <Input
                    type="date"
                    value={rangeEnd}
                    onChange={(e) => setRangeEnd(e.target.value)}
                    className="bg-black/20 border-red-500/30 text-white"
                  />
                </div>
              </>
            )}

            <div className="md:col-span-4 text-xs text-gray-400">
              Periode aktif: {startIso} s/d {endIso}
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <div className="flex flex-col md:flex-row gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
              <Input
                placeholder="Cari user..."
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
                <SelectItem value="confirmed" className="text-white hover:bg-red-500/20">Dikonfirmasi</SelectItem>
                <SelectItem value="rejected" className="text-white hover:bg-red-500/20">Ditolak</SelectItem>
                <SelectItem value="cancelled" className="text-white hover:bg-red-500/20">Dibatalkan</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardHeader>
        <CardContent>
          <div className="flex items-center justify-between mb-3">
            <div className="text-xs text-gray-400">
              {loading ? 'Memuat...' : `Total: ${filtered.length} data`}
            </div>
            <Button
              variant="outline"
              className="border-red-500/30 text-white hover:bg-red-500/20"
              onClick={fetchArchive}
              disabled={loading}
            >
              Refresh
            </Button>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-red-500/20">
                  <th className="text-left py-3 px-4 font-semibold text-white">Nama User</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Lapangan</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Tanggal</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Waktu</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Status</th>
                  <th className="text-right py-3 px-4 font-semibold text-white">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {filtered.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="text-center py-12 text-gray-400">
                      Belum ada data arsip booking
                    </td>
                  </tr>
                ) : (
                  filtered.map((booking) => (
                    <tr key={booking.id} className="border-b border-red-500/10 hover:bg-red-500/5">
                      <td className="py-4 px-4">
                        <p className="font-medium text-white">{booking.userName}</p>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{booking.field}</p>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{booking.date}</p>
                      </td>
                      <td className="py-4 px-4">
                        <p className="text-sm text-gray-300">{booking.time}</p>
                      </td>
                      <td className="py-4 px-4">
                        <Badge className={getStatusColor(booking.status)}>
                          {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                        </Badge>
                      </td>
                      <td className="py-4 px-4 text-right">
                        <Button
                          variant="outline"
                          className="border-red-500/30 text-white hover:bg-red-500/20 gap-2"
                          onClick={() => deleteOne(booking.id)}
                        >
                          <Trash2 size={16} />
                          Hapus
                        </Button>
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
