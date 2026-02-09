'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import { useParams, useRouter, useSearchParams } from 'next/navigation';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Save } from 'lucide-react';
import { db } from '@/lib/firebase';
import Image from 'next/image';
import {
  doc,
  getDoc,
  setDoc,
  serverTimestamp,
} from 'firebase/firestore';
import { toast } from 'sonner';

type Division = 'Sports' | 'Caffe' | 'HK' | 'GRO' | 'Entertain' | 'Security' | 'Maintenance' | 'Marketing' | string;

type KPIFieldType = 'range' | 'fixed';

interface KPIField {
  key: string;
  label: string;
  type: KPIFieldType;
  min?: number;
  max?: number;
  fixedValue?: number;
}

interface Employee {
  id: string;
  name: string;
  divisi: Division;
  imageUrl?: string;
}

const normalizeDivision = (divisi?: string) => {
  const v = (divisi || '').trim().toLowerCase();
  if (v === 'cafe') return 'Caffe';
  if (v === 'caffe') return 'Caffe';
  if (v === 'housekeeping') return 'HK';
  if (v === 'hk') return 'HK';
  if (v === 'entertainment') return 'Entertain';
  if (v === 'entertain') return 'Entertain';
  if (v === 'sports') return 'Sports';
  if (v === 'gro') return 'GRO';
  if (v === 'security') return 'Security';
  if (v === 'maintenance') return 'Maintenance';
  if (v === 'marketing') return 'Marketing';
  return divisi || '';
};

const KPI_FIELDS_BY_DIVISION: Record<string, KPIField[]> = {
  Sports: [
    { key: 'kedisiplinan', label: 'Kedisiplinan', type: 'range', min: 1, max: 5 },
    { key: 'standarLayanan', label: 'Standar Layanan', type: 'range', min: 1, max: 2 },
    { key: 'kepatuhanSOP', label: 'Kepatuhan SOP', type: 'range', min: 1, max: 2 },
    { key: 'kualitasKerja', label: 'Kualitas Kerja', type: 'range', min: 1, max: 2 },
    { key: 'error', label: 'Error', type: 'range', min: 0, max: 1 },
  ],
  Caffe: [
    { key: 'kedisiplinan', label: 'Kedisiplinan', type: 'range', min: 1, max: 5 },
    { key: 'standarLayanan', label: 'Standar Layanan', type: 'range', min: 1, max: 2 },
    { key: 'kepatuhanSOP', label: 'Kepatuhan SOP', type: 'range', min: 1, max: 2 },
    { key: 'kualitasKerja', label: 'Kualitas Kerja', type: 'range', min: 1, max: 2 },
    { key: 'error', label: 'Error', type: 'range', min: 0, max: 1 },
  ],
  GRO: [
    { key: 'kedisiplinan', label: 'Kedisiplinan', type: 'range', min: 1, max: 5 },
    { key: 'standarLayanan', label: 'Standar Layanan', type: 'range', min: 1, max: 2 },
    { key: 'kepatuhanSOP', label: 'Kepatuhan SOP', type: 'range', min: 1, max: 2 },
    { key: 'kualitasKerja', label: 'Kualitas Kerja', type: 'range', min: 1, max: 2 },
    { key: 'error', label: 'Error', type: 'range', min: 0, max: 1 },
  ],
  Entertain: [
    { key: 'kedisiplinan', label: 'Kedisiplinan', type: 'range', min: 1, max: 5 },
    { key: 'standarLayanan', label: 'Standar Layanan', type: 'range', min: 1, max: 2 },
    { key: 'kepatuhanSOP', label: 'Kepatuhan SOP', type: 'range', min: 1, max: 2 },
    { key: 'kualitasKerja', label: 'Kualitas Kerja', type: 'range', min: 1, max: 2 },
    { key: 'error', label: 'Error', type: 'range', min: 0, max: 1 },
  ],
  HK: [
    { key: 'kedisiplinan', label: 'Kedisiplinan', type: 'range', min: 1, max: 5 },
    { key: 'standarKebersihanFasilitas', label: 'Standar kebersihan fasilitas', type: 'range', min: 1, max: 2 },
    { key: 'kepatuhanSOP', label: 'Kepatuhan SOP', type: 'range', min: 1, max: 2 },
    { key: 'kualitasKerja', label: 'Kualitas kerja', type: 'range', min: 1, max: 2 },
    { key: 'kecepatanRespons', label: 'Kecepatan respons', type: 'range', min: 1, max: 2 },
    { key: 'standarPerlengkapanPakaian', label: 'Standar perlengkapan pakaian', type: 'range', min: 1, max: 2 },
    { key: 'komplen', label: 'Komplen', type: 'range', min: 1, max: 5 },
  ],
  Security: [
    { key: 'kedisiplinan', label: 'Kedisiplinan', type: 'range', min: 1, max: 5 },
    { key: 'standarLayanan', label: 'Standar layanan', type: 'range', min: 1, max: 2 },
    { key: 'kepatuhanSOP', label: 'Kepatuhan SOP', type: 'range', min: 1, max: 2 },
    { key: 'kualitasKerja', label: 'Kualitas Kerja', type: 'range', min: 1, max: 2 },
    { key: 'kecepatanRespons', label: 'Kecepatan respons', type: 'range', min: 1, max: 2 },
    { key: 'frekuensiPatroli', label: 'Frekuensi Patroli', type: 'range', min: 1, max: 2 },
    { key: 'komplen', label: 'Komplen', type: 'range', min: 1, max: 5 },
  ],
  Maintenance: [
    { key: 'kecepatanResponsKendala', label: 'Kecepatan respons terhadap kendala di lapangan', type: 'range', min: 1, max: 2 },
    { key: 'tanggungJawab', label: 'Tanggung jawab', type: 'range', min: 1, max: 2 },
    { key: 'kebersihanAirKolamAtauKetepatanRencana', label: 'Kebersihan air kolam / ketepatan pengerjaan rencana perbaikan bulanan', type: 'range', min: 1, max: 2 },
  ],
};

const getDefaultPeriod = () => {
  const d = new Date();
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  return `${y}-${m}`;
};

const clamp = (value: number, min: number, max: number) => Math.max(min, Math.min(max, value));

export default function KPIEmployeeAssessmentPage() {
  const params = useParams<{ employeeId: string }>();
  const router = useRouter();
  const searchParams = useSearchParams();
  const employeeId = params?.employeeId;

  const [employee, setEmployee] = useState<Employee | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  const [period, setPeriod] = useState(searchParams.get('period') || getDefaultPeriod());
  const [scores, setScores] = useState<Record<string, number>>({});

  useEffect(() => {
    const p = searchParams.get('period');
    if (p && p !== period) setPeriod(p);
  }, [period, searchParams]);

  const fields = useMemo(() => {
    const div = normalizeDivision(employee?.divisi);
    return KPI_FIELDS_BY_DIVISION[div || ''] || [];
  }, [employee?.divisi]);

  const totalScore = useMemo(() => {
    return fields.reduce((sum, f) => {
      if (f.type === 'fixed') return sum + (f.fixedValue ?? 0);
      const v = scores[f.key];
      return sum + (typeof v === 'number' ? v : 0);
    }, 0);
  }, [fields, scores]);

  const loadEmployeeAndAssessment = useCallback(async () => {
    if (!employeeId) return;
    setLoading(true);
    try {
      const empSnap = await getDoc(doc(db, 'employees', employeeId));
      if (!empSnap.exists()) {
        toast.error('Karyawan tidak ditemukan');
        router.push('/bigdata/kpi');
        return;
      }

      const emp = { id: empSnap.id, ...(empSnap.data() as any) } as Employee;
      setEmployee(emp);

      const assessId = `${employeeId}_${period}`;
      const assessSnap = await getDoc(doc(db, 'kpi_assessments', assessId));
      if (assessSnap.exists()) {
        const data = assessSnap.data() as any;
        setScores((data?.scores as Record<string, number>) || {});
      } else {
        setScores({});
      }
    } catch (e) {
      console.error('Load KPI assessment error:', e);
      toast.error('Gagal memuat data penilaian');
    } finally {
      setLoading(false);
    }
  }, [employeeId, period, router]);

  useEffect(() => {
    loadEmployeeAndAssessment();
  }, [loadEmployeeAndAssessment]);

  const handleChange = (field: KPIField, value: string) => {
    if (field.type === 'fixed') return;
    const num = Number(value);
    if (Number.isNaN(num)) {
      setScores((prev) => ({ ...prev, [field.key]: 0 }));
      return;
    }
    const min = field.min ?? 0;
    const max = field.max ?? 999;
    setScores((prev) => ({ ...prev, [field.key]: clamp(num, min, max) }));
  };

  const saveAssessment = async () => {
    if (!employeeId || !employee) return;

    const normalizedDivisi = normalizeDivision(employee.divisi);
    if (!normalizedDivisi || fields.length === 0) {
      toast.error('Divisi karyawan belum sesuai template KPI');
      return;
    }

    for (const f of fields) {
      if (f.type === 'range') {
        const v = scores[f.key];
        if (typeof v !== 'number') {
          toast.error(`Nilai ${f.label} wajib diisi`);
          return;
        }
      }
    }

    setSaving(true);
    try {
      const assessId = `${employeeId}_${period}`;

      const assessRef = doc(db, 'kpi_assessments', assessId);
      const existingSnap = await getDoc(assessRef);

      await setDoc(
        assessRef,
        {
          employeeId,
          employeeName: employee.name,
          divisi: normalizedDivisi,
          period,
          scores,
          totalScore,
          updatedAt: serverTimestamp(),
          ...(existingSnap.exists() ? {} : { createdAt: serverTimestamp() }),
        },
        { merge: true }
      );

      toast.success('Penilaian KPI tersimpan');
    } catch (e) {
      console.error('Save KPI assessment error:', e);
      toast.error('Gagal menyimpan penilaian');
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="flex flex-col items-center gap-4">
          <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
          <p className="text-gray-400">Memuat penilaian...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <div className="space-y-1">
          <div className="flex items-center gap-3">
            <Button
              variant="outline"
              className="border-red-500/30 text-white hover:bg-red-500/20 gap-2"
              onClick={() => router.push('/bigdata/kpi')}
            >
              <ArrowLeft size={16} />
              Kembali
            </Button>
            <h1 className="text-3xl font-bold text-white">Penilaian KPI</h1>
          </div>
          {employee && (
            <div className="flex items-center gap-3">
              <div className="relative w-10 h-10 rounded-lg overflow-hidden bg-gray-700 border border-red-500/20">
                {employee.imageUrl ? (
                  <Image src={employee.imageUrl} alt={employee.name} fill className="object-contain" />
                ) : (
                  <div className="w-full h-full flex items-center justify-center text-gray-300 text-sm font-semibold">
                    {employee.name?.charAt(0) || '?'}
                  </div>
                )}
              </div>
              <div className="flex items-center gap-2">
                <span className="text-gray-300">{employee.name}</span>
                <Badge className="bg-red-500/20 text-red-400 border border-red-500/30">{employee.divisi}</Badge>
              </div>
            </div>
          )}
        </div>
        <Button
          className="bg-red-500 hover:bg-red-600 text-white gap-2"
          onClick={saveAssessment}
          disabled={saving}
        >
          <Save size={16} />
          {saving ? 'Menyimpan...' : 'Simpan'}
        </Button>
      </div>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <CardTitle className="text-white">Periode Penilaian</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="max-w-xs">
            <Label className="text-white">Bulan</Label>
            <Input
              type="month"
              value={period}
              onChange={(e) => setPeriod(e.target.value)}
              className="bg-black/20 border-red-500/30 text-white"
            />
            <p className="text-xs text-gray-400 mt-2">Data tersimpan per karyawan per bulan.</p>
          </div>
        </CardContent>
      </Card>

      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle className="text-white">Form Penilaian</CardTitle>
            <Badge className="bg-black/40 text-white border border-red-500/20">Total: {totalScore}</Badge>
          </div>
        </CardHeader>
        <CardContent>
          {fields.length === 0 ? (
            <div className="text-gray-400">Template KPI untuk divisi ini belum ada.</div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {fields.map((f) => (
                <div key={f.key} className="bg-black/20 p-4 rounded-lg border border-red-500/20">
                  <div className="flex items-center justify-between mb-2">
                    <Label className="text-white">{f.label}</Label>
                    {f.type === 'range' ? (
                      <span className="text-xs text-gray-400">{f.min}-{f.max}</span>
                    ) : (
                      <span className="text-xs text-gray-400">Fixed</span>
                    )}
                  </div>

                  {f.type === 'fixed' ? (
                    <div className="text-white font-semibold">{f.fixedValue}</div>
                  ) : (
                    <Input
                      type="number"
                      min={f.min}
                      max={f.max}
                      value={typeof scores[f.key] === 'number' ? scores[f.key] : ''}
                      onChange={(e) => handleChange(f, e.target.value)}
                      className="bg-black/20 border-red-500/30 text-white"
                      placeholder={`Masukkan nilai (${f.min}-${f.max})`}
                      required
                    />
                  )}
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
