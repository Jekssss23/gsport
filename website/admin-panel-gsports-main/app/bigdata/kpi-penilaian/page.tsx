'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Download, Search, Eye, Trash2 } from 'lucide-react';
import { db } from '@/lib/firebase';
import {
  collection,
  deleteDoc,
  doc,
  getDocs,
  query,
  where,
} from 'firebase/firestore';
import { toast } from 'sonner';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

type Division = string;

interface Employee {
  id: string;
  name: string;
  divisi: Division;
  imageUrl?: string;
}

interface KPIAssessment {
  id: string;
  employeeId: string;
  employeeName: string;
  divisi: Division;
  period: string;
  totalScore: number;
  scores?: Record<string, number>;
  updatedAt?: any;
}

const getDefaultPeriod = () => {
  const d = new Date();
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  return `${y}-${m}`;
};

const formatDateTime = (v: any) => {
  try {
    const dateObj = v?.toDate?.() ? v.toDate() : (typeof v === 'string' ? new Date(v) : null);
    if (!dateObj || Number.isNaN(dateObj.getTime())) return '-';
    return dateObj.toLocaleString('id-ID');
  } catch {
    return '-';
  }
};

export default function BigDataKPIPenilaianPage() {
  const [employees, setEmployees] = useState<Employee[]>([]);
  const [assessments, setAssessments] = useState<KPIAssessment[]>([]);
  const [loading, setLoading] = useState(true);

  const [period, setPeriod] = useState(getDefaultPeriod());
  const [selectedDivision, setSelectedDivision] = useState('All');
  const [searchTerm, setSearchTerm] = useState('');

  const divisionOptions = useMemo(() => {
    const uniq = Array.from(new Set(employees.map((e) => e.divisi).filter(Boolean)));
    uniq.sort((a, b) => a.localeCompare(b));
    return ['All', ...uniq];
  }, [employees]);

  const employeeById = useMemo(() => {
    return employees.reduce((acc, e) => {
      acc[e.id] = e;
      return acc;
    }, {} as Record<string, Employee>);
  }, [employees]);

  const fetchEmployees = useCallback(async () => {
    const snap = await getDocs(collection(db, 'employees'));
    const data = snap.docs.map((d) => ({ id: d.id, ...(d.data() as any) })) as Employee[];
    setEmployees(data);
  }, []);

  const fetchAssessments = useCallback(async () => {
    try {
      const q = query(
        collection(db, 'kpi_assessments'),
        where('period', '==', period)
      );
      const snap = await getDocs(q);
      const data = snap.docs.map((d) => ({ id: d.id, ...(d.data() as any) })) as KPIAssessment[];
      data.sort((a, b) => (a.employeeName || '').localeCompare(b.employeeName || ''));
      setAssessments(data);
    } catch (e) {
      console.error('Fetch KPI assessments error:', e);
      const err = e as any;
      const code = err?.code ? String(err.code) : '';
      const msg = err?.message ? String(err.message) : '';
      toast.error(`Gagal memuat data penilaian KPI${code ? ` (${code})` : ''}`);
      if (msg) console.error('Firestore error message:', msg);
      setAssessments([]);
    }
  }, [period]);

  const deleteAssessment = async (assessment: KPIAssessment) => {
    const label = `${assessment.employeeName} (${assessment.divisi}) - ${assessment.period}`;
    if (!confirm(`Hapus penilaian KPI ini secara permanen?\n\n${label}`)) return;

    try {
      await deleteDoc(doc(db, 'kpi_assessments', assessment.id));
      toast.success('Penilaian KPI berhasil dihapus');
      await fetchAssessments();
    } catch (e) {
      console.error('Delete KPI assessment error:', e);
      const err = e as any;
      const code = err?.code ? String(err.code) : '';
      toast.error(`Gagal menghapus penilaian${code ? ` (${code})` : ''}`);
    }
  };

  useEffect(() => {
    const run = async () => {
      setLoading(true);
      try {
        await fetchEmployees();
        await fetchAssessments();
      } finally {
        setLoading(false);
      }
    };
    run();
  }, [fetchAssessments, fetchEmployees]);

  useEffect(() => {
    fetchAssessments();
  }, [fetchAssessments]);

  const filtered = useMemo(() => {
    return assessments.filter((a) => {
      const matchDiv = selectedDivision === 'All' || a.divisi === selectedDivision;
      const matchSearch = (a.employeeName || '').toLowerCase().includes(searchTerm.toLowerCase());
      return matchDiv && matchSearch;
    });
  }, [assessments, searchTerm, selectedDivision]);

  const exportToPDF = () => {
    try {
      const docPdf = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });
      const printedAt = new Date().toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });

      docPdf.setFontSize(16);
      docPdf.text('Rekap Penilaian KPI', 40, 50);
      docPdf.setFontSize(10);
      docPdf.text(`Tanggal cetak: ${printedAt}`, 40, 68);
      docPdf.text(`Periode: ${period} | Divisi: ${selectedDivision === 'All' ? 'Semua' : selectedDivision}`, 40, 82);

      const rows = filtered.map((a) => [
        a.employeeName,
        a.divisi,
        a.period,
        String(a.totalScore ?? 0),
        formatDateTime(a.updatedAt),
      ]);

      autoTable(docPdf, {
        startY: 100,
        head: [['Nama Karyawan', 'Divisi', 'Periode', 'Total', 'Update']],
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
      docPdf.save(`rekap-kpi-${period}-${fileDate}.pdf`);
      toast.success('PDF berhasil diunduh');
    } catch (e) {
      console.error('Export KPI recap PDF error:', e);
      toast.error('Gagal export PDF');
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="flex flex-col items-center gap-4">
          <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
          <p className="text-gray-400">Memuat rekap KPI...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-white mb-2">Rekap KPI (Penilaian)</h1>
          <p className="text-gray-400">History penilaian KPI per periode dan divisi</p>
        </div>
        <Button
          variant="outline"
          className="border-red-500/30 text-white hover:bg-red-500/20 gap-2"
          onClick={exportToPDF}
        >
          <Download size={16} />
          Export PDF
        </Button>
      </div>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <CardTitle className="text-white">Filter</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <Label className="text-white">Periode (Bulan)</Label>
              <Input
                type="month"
                value={period}
                onChange={(e) => setPeriod(e.target.value)}
                className="bg-black/20 border-red-500/30 text-white"
              />
            </div>

            <div>
              <Label className="text-white">Divisi</Label>
              <Select value={selectedDivision} onValueChange={setSelectedDivision}>
                <SelectTrigger className="w-full bg-black/20 border-red-500/30 text-white">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent className="bg-black border-red-500/30">
                  {divisionOptions.map((div) => (
                    <SelectItem key={div} value={div} className="text-white hover:bg-red-500/20">
                      {div === 'All' ? 'Semua Divisi' : div}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div>
              <Label className="text-white">Cari Nama</Label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={18} />
                <Input
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  placeholder="Cari karyawan..."
                  className="pl-10 bg-black/20 border-red-500/30 text-white"
                />
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle className="text-white">Data Penilaian</CardTitle>
            <Badge className="bg-red-500/20 text-red-400 border border-red-500/30">{filtered.length}</Badge>
          </div>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-red-500/20">
                  <th className="text-left py-3 px-4 font-semibold text-white">Karyawan</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Divisi</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Periode</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Total</th>
                  <th className="text-left py-3 px-4 font-semibold text-white">Update</th>
                  <th className="text-right py-3 px-4 font-semibold text-white">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {filtered.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="text-center py-12 text-gray-400">
                      Belum ada penilaian untuk periode ini
                    </td>
                  </tr>
                ) : (
                  filtered.map((a) => {
                    const emp = employeeById[a.employeeId];
                    const imageUrl = emp?.imageUrl;
                    return (
                      <tr key={a.id} className="border-b border-red-500/10 hover:bg-red-500/5">
                        <td className="py-4 px-4">
                          <div className="flex items-center gap-3">
                            <div className="relative w-10 h-10 rounded-lg overflow-hidden bg-gray-700 border border-red-500/20">
                              {imageUrl ? (
                                <Image src={imageUrl} alt={a.employeeName} fill className="object-contain" />
                              ) : (
                                <div className="w-full h-full flex items-center justify-center text-gray-300 text-sm font-semibold">
                                  {a.employeeName?.charAt(0) || '?'}
                                </div>
                              )}
                            </div>
                            <div>
                              <p className="font-medium text-white">{a.employeeName}</p>
                              <p className="text-xs text-gray-400">{a.employeeId}</p>
                            </div>
                          </div>
                        </td>
                        <td className="py-4 px-4">
                          <Badge className="bg-red-500/20 text-red-400 border border-red-500/30">{a.divisi}</Badge>
                        </td>
                        <td className="py-4 px-4">
                          <p className="text-sm text-gray-300">{a.period}</p>
                        </td>
                        <td className="py-4 px-4">
                          <p className="text-sm text-gray-300">{typeof a.totalScore === 'number' ? a.totalScore : 0}</p>
                        </td>
                        <td className="py-4 px-4">
                          <p className="text-xs text-gray-400">{formatDateTime(a.updatedAt)}</p>
                        </td>
                        <td className="py-4 px-4 text-right">
                          <div className="flex justify-end gap-2">
                            <Link href={`/bigdata/kpi/${a.employeeId}?period=${encodeURIComponent(a.period)}`}>
                              <Button
                                variant="outline"
                                className="border-red-500/30 text-white hover:bg-red-500/20 gap-2"
                              >
                                <Eye size={16} />
                                Lihat
                              </Button>
                            </Link>
                            <Button
                              variant="outline"
                              className="border-red-500/30 text-red-400 hover:bg-red-500/20 gap-2"
                              onClick={() => deleteAssessment(a)}
                            >
                              <Trash2 size={16} />
                              Hapus
                            </Button>
                          </div>
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
