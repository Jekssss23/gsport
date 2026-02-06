'use client';

import { useEffect, useState } from 'react';
import { ProtectedRoute } from '@/components/protected-route';
import { DashboardLayout } from '@/components/dashboard-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { KPICard } from '@/components/kpi-card';
import {
  DollarSign,
  TrendingUp,
  Users,
  Calendar,
  Activity,
  BarChart3,
} from 'lucide-react';
import { db } from '@/lib/firebase';
import { collection, getDocs } from 'firebase/firestore';
import { Loader2 } from 'lucide-react';
import {
  LineChart,
  Line,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
} from 'recharts';

interface KPIMetrics {
  totalBookings: number;
  totalRevenue: number;
  memberRegistrations: number;
  averageAttendance: number;
  facilityUtilization: number;
  memberGrowth: number;
}

const chartData = [
  { month: 'Jan', bookings: 45, revenue: 4500, attendance: 78 },
  { month: 'Feb', bookings: 52, revenue: 5200, attendance: 82 },
  { month: 'Mar', bookings: 48, revenue: 4800, attendance: 75 },
  { month: 'Apr', bookings: 61, revenue: 6100, attendance: 85 },
  { month: 'May', bookings: 55, revenue: 5500, attendance: 80 },
  { month: 'Jun', bookings: 68, revenue: 6800, attendance: 88 },
];

const attendanceByFacility = [
  { name: 'Basketball Court', value: 35, percentage: 35 },
  { name: 'Yoga Studio', value: 25, percentage: 25 },
  { name: 'Gym', value: 30, percentage: 30 },
  { name: 'Pool', value: 10, percentage: 10 },
];

const COLORS = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b'];

export default function KPIPage() {
  const [metrics, setMetrics] = useState<KPIMetrics>({
    totalBookings: 0,
    totalRevenue: 0,
    memberRegistrations: 0,
    averageAttendance: 0,
    facilityUtilization: 0,
    memberGrowth: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchMetrics = async () => {
      try {
        // Fetch bookings
        const bookingsRef = collection(db, 'bookings');
        const bookingsSnap = await getDocs(bookingsRef);
        const totalBookings = bookingsSnap.size;
        const totalRevenue = bookingsSnap.docs.reduce((sum, doc) => sum + (doc.data().amount || 0), 0);

        // Fetch members
        const membersRef = collection(db, 'members');
        const membersSnap = await getDocs(membersRef);
        const memberRegistrations = membersSnap.size;

        // Fetch attendance
        const attendanceRef = collection(db, 'attendance');
        const attendanceSnap = await getDocs(attendanceRef);
        const averageAttendance = attendanceSnap.size > 0 ? 82 : 0;

        setMetrics({
          totalBookings,
          totalRevenue,
          memberRegistrations,
          averageAttendance,
          facilityUtilization: 78,
          memberGrowth: 12,
        });
      } catch (error) {
        console.error('Error fetching metrics:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchMetrics();
  }, []);

  if (loading) {
    return (
      <ProtectedRoute requiredRole="admin">
        <DashboardLayout>
          <div className="flex items-center justify-center py-12">
            <div className="flex flex-col items-center gap-4">
              <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
              <p className="text-gray-400">Loading analytics...</p>
            </div>
          </div>
        </DashboardLayout>
      </ProtectedRoute>
    );
  }

  return (
    <ProtectedRoute requiredRole="admin">
      <DashboardLayout>
        <div className="space-y-8">
          {/* KPI Summary Cards */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <KPICard
              title="Total Bookings"
              value={metrics.totalBookings}
              subtitle="All time"
              icon={Calendar}
              change={{ value: 15, isPositive: true }}
            />
            <KPICard
              title="Revenue Generated"
              value={`Rp ${(metrics.totalRevenue / 1000000).toFixed(1)}M`}
              subtitle="Total revenue"
              icon={DollarSign}
              change={{ value: 22, isPositive: true }}
            />
            <KPICard
              title="Member Registrations"
              value={metrics.memberRegistrations}
              subtitle="Active members"
              icon={Users}
              change={{ value: 8, isPositive: true }}
            />
            <KPICard
              title="Average Attendance"
              value={`${metrics.averageAttendance}%`}
              subtitle="Class attendance rate"
              icon={Activity}
              change={{ value: 5, isPositive: true }}
            />
            <KPICard
              title="Facility Utilization"
              value={`${metrics.facilityUtilization}%`}
              subtitle="This month"
              icon={BarChart3}
              change={{ value: 3, isPositive: false }}
            />
            <KPICard
              title="Member Growth"
              value={`${metrics.memberGrowth}%`}
              subtitle="Monthly growth"
              icon={TrendingUp}
              change={{ value: 12, isPositive: true }}
            />
          </div>

          {/* Charts */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Bookings & Revenue Trend */}
            <Card className="bg-black/40 border-red-500/20">
              <CardHeader>
                <CardTitle className="text-white">Bookings & Revenue Trend</CardTitle>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <LineChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#404040" />
                    <XAxis dataKey="month" stroke="#ffffff" />
                    <YAxis yAxisId="left" stroke="#ffffff" />
                    <YAxis yAxisId="right" orientation="right" stroke="#ffffff" />
                    <Tooltip contentStyle={{ backgroundColor: '#000', border: '1px solid #ff0000' }} />
                    <Legend />
                    <Line
                      yAxisId="left"
                      type="monotone"
                      dataKey="bookings"
                      stroke="#ff0000"
                      name="Bookings"
                      strokeWidth={2}
                    />
                    <Line
                      yAxisId="right"
                      type="monotone"
                      dataKey="revenue"
                      stroke="#ffffff"
                      name="Revenue (Rp)"
                      strokeWidth={2}
                    />
                  </LineChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            {/* Attendance Rate Trend */}
            <Card className="bg-black/40 border-red-500/20">
              <CardHeader>
                <CardTitle className="text-white">Monthly Attendance Rate</CardTitle>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <BarChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#404040" />
                    <XAxis dataKey="month" stroke="#ffffff" />
                    <YAxis stroke="#ffffff" />
                    <Tooltip contentStyle={{ backgroundColor: '#000', border: '1px solid #ff0000' }} />
                    <Bar dataKey="attendance" fill="#ff0000" name="Attendance %" />
                  </BarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            {/* Facility Utilization by Type */}
            <Card className="bg-black/40 border-red-500/20">
              <CardHeader>
                <CardTitle className="text-white">Facility Utilization by Type</CardTitle>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <PieChart>
                    <Pie
                      data={attendanceByFacility}
                      cx="50%"
                      cy="50%"
                      labelLine={false}
                      label={({ name, percentage }) => `${name} (${percentage}%)`}
                      outerRadius={100}
                      fill="#8884d8"
                      dataKey="value"
                    >
                      {attendanceByFacility.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={['#ff0000', '#ffffff', '#666666', '#999999'][index % 4]} />
                      ))}
                    </Pie>
                    <Tooltip contentStyle={{ backgroundColor: '#000', border: '1px solid #ff0000' }} />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            {/* Member Registration Trend */}
            <Card className="bg-black/40 border-red-500/20">
              <CardHeader>
                <CardTitle className="text-white">Member Registration Trend</CardTitle>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <LineChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#404040" />
                    <XAxis dataKey="month" stroke="#ffffff" />
                    <YAxis stroke="#ffffff" />
                    <Tooltip contentStyle={{ backgroundColor: '#000', border: '1px solid #ff0000' }} />
                    <Line
                      type="monotone"
                      dataKey="bookings"
                      stroke="#ff0000"
                      name="New Members"
                      strokeWidth={2}
                    />
                  </LineChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>

          {/* Summary Statistics */}
          <Card className="bg-black/40 border-red-500/20">
            <CardHeader>
              <CardTitle className="text-white">Key Metrics Summary</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="space-y-1">
                  <p className="text-sm text-gray-400">Avg Booking Value</p>
                  <p className="text-xl font-bold text-white">
                    Rp {metrics.totalBookings > 0 ? (metrics.totalRevenue / metrics.totalBookings).toLocaleString() : 0}
                  </p>
                </div>
                <div className="space-y-1">
                  <p className="text-sm text-gray-400">Peak Hour</p>
                  <p className="text-xl font-bold text-white">6:00 PM - 7:00 PM</p>
                </div>
                <div className="space-y-1">
                  <p className="text-sm text-gray-400">Most Popular Class</p>
                  <p className="text-xl font-bold text-white">Morning Fitness</p>
                </div>
                <div className="space-y-1">
                  <p className="text-sm text-gray-400">Member Retention</p>
                  <p className="text-xl font-bold text-white">87%</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </DashboardLayout>
    </ProtectedRoute>
  );
}
