'use client';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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

export default function BigDataMonitoringPage() {
  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold text-white mb-2">Monitoring & Analytics</h1>
        <p className="text-gray-400">Real-time data visualization</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
    </div>
  );
}
