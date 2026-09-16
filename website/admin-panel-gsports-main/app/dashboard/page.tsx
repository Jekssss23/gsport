'use client';

import { useEffect, useState } from 'react';
import { useAuth } from '@/lib/auth-context';
import { ProtectedRoute } from '@/components/protected-route';
import { DashboardLayout } from '@/components/dashboard-layout';
import { KPICard } from '@/components/kpi-card';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Calendar, Users, DollarSign, TrendingUp, CheckSquare, Activity } from 'lucide-react';
import { db } from '@/lib/firebase';
import { collection, query, where, getDocs, doc, getDoc } from 'firebase/firestore';
import { Loader2 } from 'lucide-react';

interface DashboardStats {
  totalBookings: number;
  totalRevenue: number;
  memberCount: number;
  classesScheduled: number;
  attendanceRate: number;
}

interface RecentBooking {
  id: string;
  userName: string;
  facilityName: string;
  courtName: string;
  date: string;
  timeSlots: number[];
  status: 'pending' | 'confirmed' | 'rejected';
  totalAmount: number;
  createdAt: any;
}

export default function DashboardPage() {
  const { userData } = useAuth();
  const [stats, setStats] = useState<DashboardStats>({
    totalBookings: 0,
    totalRevenue: 0,
    memberCount: 0,
    classesScheduled: 0,
    attendanceRate: 0,
  });
  const [recentBookings, setRecentBookings] = useState<RecentBooking[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        // Fetch bookings
        const bookingsRef = collection(db, 'bookings');
        const bookingsSnap = await getDocs(bookingsRef);
        const totalBookings = bookingsSnap.size;
        const totalRevenue = bookingsSnap.docs.reduce((sum, doc) => {
          const data = doc.data();
          return sum + (data.totalAmount || 0);
        }, 0);

        // Get recent bookings (last 5)
        const bookingsData = bookingsSnap.docs.map(doc => ({
          id: doc.id,
          ...doc.data()
        })) as RecentBooking[];
        
        // Sort by createdAt descending
        const sortedBookings = bookingsData.sort((a, b) => {
          const timeA = a.createdAt?.toMillis?.() || 0;
          const timeB = b.createdAt?.toMillis?.() || 0;
          return timeB - timeA;
        }).slice(0, 5);
        
        setRecentBookings(sortedBookings);

        // Fetch members (users collection)
        const usersRef = collection(db, 'users');
        const usersSnap = await getDocs(usersRef);
        const memberCount = usersSnap.size;

        // Fetch classes
        const classesRef = collection(db, 'classes');
        const classesSnap = await getDocs(classesRef);
        const classesScheduled = classesSnap.size;

        // Calculate attendance rate
        const attendanceRef = collection(db, 'attendance');
        const attendanceSnap = await getDocs(attendanceRef);
        const attendanceRate = attendanceSnap.size > 0 ? 85 : 0; // Placeholder

        setStats({
          totalBookings,
          totalRevenue,
          memberCount,
          classesScheduled,
          attendanceRate,
        });
      } catch (error) {
        console.error('Error fetching stats:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  return (
    <ProtectedRoute>
      <DashboardLayout>
        <div className="space-y-8">
          {/* KPI Cards */}
          {loading ? (
            <div className="flex items-center justify-center py-12">
              <div className="flex flex-col items-center gap-4">
                <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                <p className="text-gray-400">Loading dashboard...</p>
              </div>
            </div>
          ) : (
            <>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <KPICard
                  title="Total Bookings"
                  value={stats.totalBookings}
                  subtitle="This month"
                  icon={CheckSquare}
                  change={{ value: 12, isPositive: true }}
                />
                <KPICard
                  title="Total Revenue"
                  value={`Rp ${(stats.totalRevenue / 1000).toFixed(0)}K`}
                  subtitle="This month"
                  icon={DollarSign}
                  change={{ value: 8, isPositive: true }}
                />
                <KPICard
                  title="Active Members"
                  value={stats.memberCount}
                  subtitle="Total registered"
                  icon={Users}
                  change={{ value: 5, isPositive: true }}
                />
                <KPICard
                  title="Classes Scheduled"
                  value={stats.classesScheduled}
                  subtitle="This month"
                  icon={Calendar}
                  change={{ value: 3, isPositive: false }}
                />
                <KPICard
                  title="Attendance Rate"
                  value={`${stats.attendanceRate}%`}
                  subtitle="Average"
                  icon={Activity}
                  change={{ value: 2, isPositive: true }}
                />
                <KPICard
                  title="Facility Utilization"
                  value="78%"
                  subtitle="Current week"
                  icon={TrendingUp}
                  change={{ value: 4, isPositive: true }}
                />
              </div>

              {/* Recent Activity */}
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Card className="bg-black/40 border-red-500/20">
                  <CardHeader>
                    <CardTitle className="text-lg text-white flex items-center gap-2">
                      <CheckSquare className="w-5 h-5 text-red-500" />
                      Recent Bookings
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    {recentBookings.length === 0 ? (
                      <p className="text-gray-400 text-center py-4">No bookings yet</p>
                    ) : (
                      <div className="space-y-4">
                        {recentBookings.map((booking) => (
                          <div key={booking.id} className="flex items-center justify-between pb-3 border-b border-red-500/20 last:border-0">
                            <div className="flex-1">
                              <p className="font-medium text-white">
                                {booking.userName} - {booking.facilityName}
                              </p>
                              <p className="text-sm text-gray-400">
                                {booking.courtName} | {booking.date}
                              </p>
                              <p className="text-xs text-gray-500">
                                {booking.timeSlots?.sort((a, b) => a - b).map(h => `${h}:00`).join(', ')}
                              </p>
                            </div>
                            <div className="flex flex-col items-end gap-1">
                              <span className={`text-xs px-2 py-1 rounded border ${
                                booking.status === 'confirmed' 
                                  ? 'bg-green-500/20 text-green-400 border-green-500/30'
                                  : booking.status === 'pending'
                                  ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30'
                                  : 'bg-red-500/20 text-red-400 border-red-500/30'
                              }`}>
                                {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                              </span>
                              <span className="text-xs text-gray-400">
                                Rp {(booking.totalAmount / 1000).toFixed(0)}K
                              </span>
                            </div>
                          </div>
                        ))}
                      </div>
                    )}
                  </CardContent>
                </Card>

                <Card className="bg-black/40 border-red-500/20">
                  <CardHeader>
                    <CardTitle className="text-lg text-white flex items-center gap-2">
                      <Calendar className="w-5 h-5 text-red-500" />
                      Upcoming Classes
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-4">
                      <div className="flex items-center justify-between pb-3 border-b border-red-500/20">
                        <div>
                          <p className="font-medium text-white">Morning Fitness</p>
                          <p className="text-sm text-gray-400">Coach: Alex | 6:00 AM</p>
                        </div>
                        <span className="text-sm font-semibold text-red-400">24/30</span>
                      </div>
                      <div className="flex items-center justify-between pb-3 border-b border-red-500/20">
                        <div>
                          <p className="font-medium text-white">Evening Yoga</p>
                          <p className="text-sm text-gray-400">Coach: Sarah | 5:00 PM</p>
                        </div>
                        <span className="text-sm font-semibold text-red-400">18/25</span>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </>
          )}
        </div>
      </DashboardLayout>
    </ProtectedRoute>
  );
}
