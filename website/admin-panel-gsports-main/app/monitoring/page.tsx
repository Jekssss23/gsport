'use client';

import { useEffect, useState } from 'react';
import { ProtectedRoute } from '@/components/protected-route';
import { DashboardLayout } from '@/components/dashboard-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Loader2, AlertCircle, CheckCircle, Clock, Zap } from 'lucide-react';
import { db } from '@/lib/firebase';
import { collection, getDocs, query, where, orderBy } from 'firebase/firestore';
import { useAuth } from '@/lib/auth-context';

interface MonitoringData {
  timestamp: string;
  facilityName: string;
  occupancy: number;
  capacity: number;
  status: 'available' | 'busy' | 'maintenance';
  lastUpdated: string;
}

interface AttendanceRecord {
  id: string;
  memberName: string;
  className: string;
  checkInTime: string;
  status: 'present' | 'absent' | 'late';
  date: string;
}

export default function MonitoringPage() {
  const { userData } = useAuth();
  const [monitoringData, setMonitoringData] = useState<MonitoringData[]>([]);
  const [attendanceRecords, setAttendanceRecords] = useState<AttendanceRecord[]>([]);
  const [loading, setLoading] = useState(true);
  const [facilityFilter, setFacilityFilter] = useState('all');
  const [dateFilter, setDateFilter] = useState('today');

  useEffect(() => {
    const fetchData = async () => {
      try {
        // Fetch facility monitoring data
        const facilitiesRef = collection(db, 'facilities');
        const facilitiesSnap = await getDocs(facilitiesRef);
        const facilities = facilitiesSnap.docs.map((doc) => {
          const data = doc.data();
          return {
            id: doc.id,
            facilityName: data.name, // Map 'name' field to 'facilityName'
            occupancy: data.occupancy || 0,
            capacity: data.capacity || 0,
            status: data.status || 'available',
            lastUpdated: new Date().toLocaleString(), // Generate current timestamp
            ...data,
          };
        }) as unknown as MonitoringData[];
        setMonitoringData(facilities);

        // Fetch attendance records
        const attendanceRef = collection(db, 'attendance');
        let attendanceQuery;

        if (dateFilter === 'today') {
          const today = new Date().toISOString().split('T')[0];
          attendanceQuery = query(attendanceRef, where('date', '==', today));
        } else {
          attendanceQuery = attendanceRef;
        }

        const attendanceSnap = await getDocs(attendanceQuery);
        const records = attendanceSnap.docs.map((doc) => ({
          id: doc.id,
          ...doc.data(),
        })) as AttendanceRecord[];
        setAttendanceRecords(records);
      } catch (error) {
        console.error('Error fetching monitoring data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [dateFilter]);

  const filteredMonitoring =
    facilityFilter === 'all'
      ? monitoringData
      : monitoringData.filter((f) => f.facilityName === facilityFilter);

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'available':
        return <CheckCircle className="w-4 h-4 text-green-600" />;
      case 'busy':
        return <Zap className="w-4 h-4 text-yellow-600" />;
      case 'maintenance':
        return <AlertCircle className="w-4 h-4 text-red-600" />;
      default:
        return null;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'available':
        return 'bg-green-500/20 text-green-400 border border-green-500/30';
      case 'busy':
        return 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30';
      case 'maintenance':
        return 'bg-red-500/20 text-red-400 border border-red-500/30';
      default:
        return '';
    }
  };

  const getAttendanceColor = (status: string) => {
    switch (status) {
      case 'present':
        return 'bg-green-500/20 text-green-400 border border-green-500/30';
      case 'absent':
        return 'bg-red-500/20 text-red-400 border border-red-500/30';
      case 'late':
        return 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30';
      default:
        return '';
    }
  };

  return (
    <ProtectedRoute>
      <DashboardLayout>
        <div className="space-y-8">
          {/* Facility Monitoring (Admin Only) */}
          {userData?.role === 'admin' && (
            <Card className="bg-black/40 border-red-500/20">
              <CardHeader>
                <CardTitle className="text-white">Facility Monitoring</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="mb-4">
                  <Select value={facilityFilter} onValueChange={setFacilityFilter}>
                    <SelectTrigger className="w-full md:w-48 bg-black/20 border-red-500/30 text-white">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent className="bg-black border-red-500/30">
                      <SelectItem value="all" className="text-white hover:bg-red-500/20">All Facilities</SelectItem>
                      <SelectItem value="Basketball Court" className="text-white hover:bg-red-500/20">Basketball Court</SelectItem>
                      <SelectItem value="Yoga Studio" className="text-white hover:bg-red-500/20">Yoga Studio</SelectItem>
                      <SelectItem value="Gym" className="text-white hover:bg-red-500/20">Gym</SelectItem>
                      <SelectItem value="Pool" className="text-white hover:bg-red-500/20">Pool</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                {loading ? (
                  <div className="flex items-center justify-center py-12">
                    <div className="flex flex-col items-center gap-4">
                      <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                      <p className="text-gray-400">Loading monitoring data...</p>
                    </div>
                  </div>
                ) : filteredMonitoring.length === 0 ? (
                  <div className="text-center py-12">
                    <p className="text-gray-400">No facility data available</p>
                  </div>
                ) : (
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {filteredMonitoring.map((facility, index) => (
                      <Card key={`${facility.facilityName}-${index}`} className="bg-black/20 border-red-500/30">
                        <CardContent className="pt-6">
                          <div className="space-y-4">
                            <div className="flex items-center justify-between">
                              <h3 className="font-semibold text-lg text-white">
                                {facility.facilityName}
                              </h3>
                              <Badge className={`gap-1 ${getStatusColor(facility.status)}`}>
                                {getStatusIcon(facility.status)}
                                <span className="capitalize">{facility.status}</span>
                              </Badge>
                            </div>

                            <div className="space-y-3">
                              <div>
                                <div className="flex items-center justify-between mb-2">
                                  <span className="text-sm font-medium text-white">Occupancy</span>
                                  <span className="text-sm font-semibold text-white">
                                    {facility.occupancy}/{facility.capacity}
                                  </span>
                                </div>
                                <div className="w-full bg-gray-700 rounded-full h-2">
                                  <div
                                    className="bg-red-500 h-2 rounded-full transition-all"
                                    style={{
                                      width: `${(facility.occupancy / facility.capacity) * 100}%`,
                                    }}
                                  />
                                </div>
                                <p className="text-xs text-gray-400 mt-1">
                                  {Math.round((facility.occupancy / facility.capacity) * 100)}% full
                                </p>
                              </div>

                              <div className="bg-black/40 p-3 rounded-lg border border-red-500/20">
                                <p className="text-xs text-gray-400">
                                  Last Updated:{' '}
                                  <span className="font-medium text-white">
                                    {facility.lastUpdated}
                                  </span>
                                </p>
                              </div>
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>
          )}

          {/* Attendance Records */}
          <Card className="bg-black/40 border-red-500/20">
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle className="text-white">
                {userData?.role === 'admin'
                  ? 'Attendance Records'
                  : 'My Attendance'}
              </CardTitle>
              {userData?.role === 'admin' && (
                <Select value={dateFilter} onValueChange={setDateFilter}>
                  <SelectTrigger className="w-full md:w-48 bg-black/20 border-red-500/30 text-white">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent className="bg-black border-red-500/30">
                    <SelectItem value="today" className="text-white hover:bg-red-500/20">Today</SelectItem>
                    <SelectItem value="week" className="text-white hover:bg-red-500/20">This Week</SelectItem>
                    <SelectItem value="month" className="text-white hover:bg-red-500/20">This Month</SelectItem>
                  </SelectContent>
                </Select>
              )}
            </CardHeader>
            <CardContent>
              {loading ? (
                <div className="flex items-center justify-center py-12">
                  <div className="flex flex-col items-center gap-4">
                    <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                    <p className="text-gray-400">Loading attendance...</p>
                  </div>
                </div>
              ) : attendanceRecords.length === 0 ? (
                <div className="text-center py-12">
                  <p className="text-gray-400">
                    No attendance records found
                  </p>
                </div>
              ) : (
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b border-red-500/20">
                        <th className="text-left py-3 px-4 font-semibold text-white">
                          {userData?.role === 'admin' ? 'Member' : 'Class'}
                        </th>
                        <th className="text-left py-3 px-4 font-semibold text-white">
                          {userData?.role === 'admin' ? 'Class' : 'Date'}
                        </th>
                        <th className="text-left py-3 px-4 font-semibold text-white">
                          Check-in Time
                        </th>
                        <th className="text-left py-3 px-4 font-semibold text-white">
                          Status
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      {attendanceRecords.map((record) => (
                        <tr key={record.id} className="border-b border-red-500/10 hover:bg-red-500/5">
                          <td className="py-4 px-4">
                            <p className="font-medium text-white">{record.memberName}</p>
                          </td>
                          <td className="py-4 px-4">
                            <p className="text-sm text-gray-300">
                              {userData?.role === 'admin'
                                ? record.className
                                : record.date}
                            </p>
                          </td>
                          <td className="py-4 px-4">
                            <p className="text-sm text-gray-300">
                              {record.checkInTime}
                            </p>
                          </td>
                          <td className="py-4 px-4">
                            <Badge
                              className={`gap-1 ${getAttendanceColor(record.status)}`}
                            >
                              {record.status === 'present' && (
                                <CheckCircle className="w-3 h-3" />
                              )}
                              {record.status === 'absent' && (
                                <AlertCircle className="w-3 h-3" />
                              )}
                              {record.status === 'late' && (
                                <Clock className="w-3 h-3" />
                              )}
                              <span className="capitalize">{record.status}</span>
                            </Badge>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </CardContent>
          </Card>

          {/* System Status (Admin Only) */}
          {userData?.role === 'admin' && (
            <Card className="bg-black/40 border-red-500/20">
              <CardHeader>
                <CardTitle className="text-white">System Status</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="flex items-center justify-between p-4 border border-red-500/30 rounded-lg bg-black/20">
                    <div>
                      <p className="font-medium text-white">Database</p>
                      <p className="text-sm text-gray-400">Firestore</p>
                    </div>
                    <Badge className="bg-green-500/20 text-green-400 border border-green-500/30 gap-1">
                      <CheckCircle className="w-3 h-3" />
                      Operational
                    </Badge>
                  </div>
                  <div className="flex items-center justify-between p-4 border border-red-500/30 rounded-lg bg-black/20">
                    <div>
                      <p className="font-medium text-white">API Services</p>
                      <p className="text-sm text-gray-400">All endpoints</p>
                    </div>
                    <Badge className="bg-green-500/20 text-green-400 border border-green-500/30 gap-1">
                      <CheckCircle className="w-3 h-3" />
                      Operational
                    </Badge>
                  </div>
                  <div className="flex items-center justify-between p-4 border border-red-500/30 rounded-lg bg-black/20">
                    <div>
                      <p className="font-medium text-white">Authentication</p>
                      <p className="text-sm text-gray-400">Firebase Auth</p>
                    </div>
                    <Badge className="bg-green-500/20 text-green-400 border border-green-500/30 gap-1">
                      <CheckCircle className="w-3 h-3" />
                      Operational
                    </Badge>
                  </div>
                  <div className="flex items-center justify-between p-4 border border-red-500/30 rounded-lg bg-black/20">
                    <div>
                      <p className="font-medium text-white">Real-time Updates</p>
                      <p className="text-sm text-gray-400">Websocket</p>
                    </div>
                    <Badge className="bg-green-500/20 text-green-400 border border-green-500/30 gap-1">
                      <CheckCircle className="w-3 h-3" />
                      Operational
                    </Badge>
                  </div>
                </div>
              </CardContent>
            </Card>
          )}
        </div>
      </DashboardLayout>
    </ProtectedRoute>
  );
}
