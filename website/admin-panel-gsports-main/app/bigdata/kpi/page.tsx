'use client';

import { useEffect, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { db } from '@/lib/firebase';
import { collection, getDocs } from 'firebase/firestore';
import Image from 'next/image';
import { Badge } from '@/components/ui/badge';
import Link from 'next/link';

interface Employee {
  id: string;
  name: string;
  divisi: string;
  imageUrl: string;
}

const DIVISIONS = ['All', 'Sports', 'Caffe', 'Entertain', 'GRO', 'HK', 'Marketing', 'Security', 'Maintenance'];

export default function BigDataKPIPage() {
  const [employees, setEmployees] = useState<Employee[]>([]);
  const [selectedDivision, setSelectedDivision] = useState('All');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const employeesRef = collection(db, 'employees');
        const employeesSnap = await getDocs(employeesRef);
        const employeesData = employeesSnap.docs.map(doc => ({
          id: doc.id,
          ...doc.data()
        })) as Employee[];
        setEmployees(employeesData);
      } catch (error) {
        console.error('Error fetching KPI employees:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const filteredEmployees = selectedDivision === 'All'
    ? employees
    : employees.filter(emp => emp.divisi === selectedDivision);

  const employeesByDivision = DIVISIONS.slice(1).reduce((acc, div) => {
    acc[div] = employees.filter(emp => emp.divisi === div).length;
    return acc;
  }, {} as Record<string, number>);

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="flex flex-col items-center gap-4">
          <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
          <p className="text-gray-400">Loading analytics...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold text-white mb-2">KPI Analytics</h1>
        <p className="text-gray-400">Key Performance Indicators</p>
      </div>

      {/* Employee Count by Division */}
      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <CardTitle className="text-white">Karyawan per Divisi</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            {DIVISIONS.slice(1).map((div) => (
              <div key={div} className="bg-black/40 p-4 rounded-lg border border-red-500/20 text-center">
                <p className="text-2xl font-bold text-white">{employeesByDivision[div] || 0}</p>
                <p className="text-sm text-gray-400">{div}</p>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Team Karyawan with Filter */}
      <Card className="bg-black/40 border-red-500/20">
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle className="text-white">Team Karyawan</CardTitle>
            <Select value={selectedDivision} onValueChange={setSelectedDivision}>
              <SelectTrigger className="w-48 bg-black/20 border-red-500/30 text-white">
                <SelectValue />
              </SelectTrigger>
              <SelectContent className="bg-black border-red-500/30">
                {DIVISIONS.map((div) => (
                  <SelectItem key={div} value={div} className="text-white hover:bg-red-500/20">
                    {div === 'All' ? 'Semua Divisi' : div}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </CardHeader>
        <CardContent>
          {filteredEmployees.length === 0 ? (
            <div className="text-center py-12">
              <p className="text-gray-400">
                {selectedDivision === 'All' ? 'Belum ada data karyawan' : `Tidak ada karyawan di divisi ${selectedDivision}`}
              </p>
            </div>
          ) : (
            <div className="space-y-6">
              {selectedDivision === 'All' ? (
                // Group by division when showing all
                DIVISIONS.slice(1).map((div) => {
                  const divEmployees = employees.filter(emp => emp.divisi === div);
                  if (divEmployees.length === 0) return null;
                  return (
                    <div key={div}>
                      <h3 className="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                        {div}
                        <Badge className="bg-red-500/20 text-red-400 border border-red-500/30">
                          {divEmployees.length}
                        </Badge>
                      </h3>
                      <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        {divEmployees.map((emp) => (
                          <Link
                            key={emp.id}
                            href={`/bigdata/kpi/${emp.id}`}
                            className="flex flex-col items-center text-center space-y-2 hover:opacity-90"
                          >
                            <div className="relative w-24 h-24 rounded-lg overflow-hidden bg-gray-700">
                              {emp.imageUrl ? (
                                <Image src={emp.imageUrl} alt={emp.name} fill className="object-contain" />
                              ) : (
                                <div className="w-full h-full flex items-center justify-center text-gray-400">
                                  <span className="text-2xl">{emp.name.charAt(0)}</span>
                                </div>
                              )}
                            </div>
                            <div>
                              <p className="text-sm font-medium text-white">{emp.name}</p>
                              <p className="text-xs text-gray-400">{emp.divisi}</p>
                            </div>
                          </Link>
                        ))}
                      </div>
                    </div>
                  );
                })
              ) : (
                // Show filtered division
                <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                  {filteredEmployees.map((emp) => (
                    <Link
                      key={emp.id}
                      href={`/bigdata/kpi/${emp.id}`}
                      className="flex flex-col items-center text-center space-y-2 hover:opacity-90"
                    >
                      <div className="relative w-24 h-24 rounded-lg overflow-hidden bg-gray-700">
                        {emp.imageUrl ? (
                          <Image src={emp.imageUrl} alt={emp.name} fill className="object-contain" />
                        ) : (
                          <div className="w-full h-full flex items-center justify-center text-gray-400">
                            <span className="text-2xl">{emp.name.charAt(0)}</span>
                          </div>
                        )}
                      </div>
                      <div>
                        <p className="text-sm font-medium text-white">{emp.name}</p>
                        <p className="text-xs text-gray-400">{emp.divisi}</p>
                      </div>
                    </Link>
                  ))}
                </div>
              )}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
