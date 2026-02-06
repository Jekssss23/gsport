'use client';

import { useEffect, useState } from 'react';
import { ProtectedRoute } from '@/components/protected-route';
import { DashboardLayout } from '@/components/dashboard-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { Loader2, Search, Check, X, Clock, Eye, FileImage } from 'lucide-react';
import { BookingService } from '@/lib/booking-service';
import { Booking } from '@/lib/types';
import Image from 'next/image';

export default function BookingsPage() {
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [filteredBookings, setFilteredBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [selectedBooking, setSelectedBooking] = useState<Booking | null>(null);
  const [actionLoading, setActionLoading] = useState<string | null>(null);
  const [notes, setNotes] = useState('');

  useEffect(() => {
    fetchBookings();
  }, []);

  const fetchBookings = async () => {
    try {
      const bookingsData = await BookingService.getAllBookings();
      setBookings(bookingsData);
      setFilteredBookings(bookingsData);
    } catch (error) {
      console.error('Error fetching bookings:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    let filtered = bookings;

    if (searchTerm) {
      filtered = filtered.filter(
        (booking) =>
          booking.userName.toLowerCase().includes(searchTerm.toLowerCase()) ||
          booking.facilityName.toLowerCase().includes(searchTerm.toLowerCase()) ||
          booking.courtName.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }

    if (statusFilter !== 'all') {
      filtered = filtered.filter((booking) => booking.status === statusFilter);
    }

    setFilteredBookings(filtered);
  }, [searchTerm, statusFilter, bookings]);

  const handleStatusUpdate = async (bookingId: string, newStatus: Booking['status']) => {
    setActionLoading(bookingId);
    try {
      await BookingService.updateBookingStatus(bookingId, newStatus, notes);
      setBookings(bookings.map(b => 
        b.id === bookingId 
          ? { ...b, status: newStatus, notes, updatedAt: new Date().toISOString() }
          : b
      ));
      setSelectedBooking(null);
      setNotes('');
    } catch (error) {
      console.error('Error updating booking:', error);
    } finally {
      setActionLoading(null);
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'confirmed':
        return <Check className="w-4 h-4 text-green-400" />;
      case 'pending':
        return <Clock className="w-4 h-4 text-yellow-400" />;
      case 'rejected':
        return <X className="w-4 h-4 text-red-400" />;
      case 'cancelled':
        return <X className="w-4 h-4 text-gray-400" />;
      default:
        return null;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'confirmed':
        return 'bg-green-500/20 text-green-400 border border-green-500/30';
      case 'pending':
        return 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30';
      case 'rejected':
        return 'bg-red-500/20 text-red-400 border border-red-500/30';
      case 'cancelled':
        return 'bg-gray-500/20 text-gray-400 border border-gray-500/30';
      default:
        return '';
    }
  };

  const formatTimeSlots = (timeSlots: number[]) => {
    if (timeSlots.length === 0) return '';
    if (timeSlots.length === 1) return `${timeSlots[0]}:00`;
    return `${Math.min(...timeSlots)}:00 - ${Math.max(...timeSlots) + 1}:00`;
  };

  return (
    <ProtectedRoute requiredRole="admin">
      <DashboardLayout>
        <div className="space-y-6">
          <Card className="bg-black/40 border-red-500/20">
            <CardHeader>
              <CardTitle className="text-white flex items-center gap-2">
                <Search className="w-5 h-5 text-red-500" />
                Booking Management
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex flex-col md:flex-row gap-4">
                <div className="flex-1 relative">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                  <Input
                    placeholder="Search by name, facility, or court..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="pl-10 bg-black/20 border-red-500/30 text-white placeholder:text-gray-400"
                  />
                </div>
                <Select value={statusFilter} onValueChange={setStatusFilter}>
                  <SelectTrigger className="w-full md:w-48 bg-black/20 border-red-500/30 text-white">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent className="bg-black border-red-500/30">
                    <SelectItem value="all" className="text-white hover:bg-red-500/20">All Status</SelectItem>
                    <SelectItem value="pending" className="text-white hover:bg-red-500/20">Pending</SelectItem>
                    <SelectItem value="confirmed" className="text-white hover:bg-red-500/20">Confirmed</SelectItem>
                    <SelectItem value="rejected" className="text-white hover:bg-red-500/20">Rejected</SelectItem>
                    <SelectItem value="cancelled" className="text-white hover:bg-red-500/20">Cancelled</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              {loading ? (
                <div className="flex items-center justify-center py-12">
                  <div className="flex flex-col items-center gap-4">
                    <div className="w-12 h-12 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                    <p className="text-gray-400">Loading bookings...</p>
                  </div>
                </div>
              ) : filteredBookings.length === 0 ? (
                <div className="text-center py-12">
                  <p className="text-gray-400">No bookings found</p>
                </div>
              ) : (
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b border-red-500/20">
                        <th className="text-left py-3 px-4 font-semibold text-white">Customer</th>
                        <th className="text-left py-3 px-4 font-semibold text-white">Facility & Court</th>
                        <th className="text-left py-3 px-4 font-semibold text-white">Date & Time</th>
                        <th className="text-left py-3 px-4 font-semibold text-white">Amount</th>
                        <th className="text-left py-3 px-4 font-semibold text-white">Status</th>
                        <th className="text-left py-3 px-4 font-semibold text-white">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      {filteredBookings.map((booking) => (
                        <tr key={booking.id} className="border-b border-red-500/10 hover:bg-red-500/5">
                          <td className="py-4 px-4">
                            <div>
                              <p className="font-medium text-white">{booking.userName}</p>
                              <p className="text-sm text-gray-400">{booking.userPhone}</p>
                            </div>
                          </td>
                          <td className="py-4 px-4">
                            <div>
                              <p className="text-white">{booking.facilityName}</p>
                              <p className="text-sm text-gray-400">{booking.courtName}</p>
                            </div>
                          </td>
                          <td className="py-4 px-4">
                            <div>
                              <p className="text-sm text-white">{booking.date}</p>
                              <p className="text-xs text-gray-400">
                                {formatTimeSlots(booking.timeSlots)} ({booking.totalHours}h)
                              </p>
                            </div>
                          </td>
                          <td className="py-4 px-4">
                            <div>
                              <p className="text-white">Rp {booking.totalAmount.toLocaleString()}</p>
                              <p className="text-xs text-gray-400">DP: Rp {booking.dpAmount.toLocaleString()}</p>
                            </div>
                          </td>
                          <td className="py-4 px-4">
                            <Badge className={`gap-1 ${getStatusColor(booking.status)}`}>
                              {getStatusIcon(booking.status)}
                              <span className="capitalize">{booking.status}</span>
                            </Badge>
                          </td>
                          <td className="py-4 px-4">
                            <div className="flex gap-2">
                              <Dialog>
                                <DialogTrigger asChild>
                                  <Button
                                    size="sm"
                                    variant="outline"
                                    className="bg-blue-600/20 border-blue-500/30 text-blue-400 hover:bg-blue-600/30"
                                    onClick={() => setSelectedBooking(booking)}
                                  >
                                    <Eye className="w-4 h-4" />
                                  </Button>
                                </DialogTrigger>
                                <DialogContent className="bg-black border-red-500/30 max-w-2xl">
                                  <DialogHeader>
                                    <DialogTitle className="text-white">Booking Details</DialogTitle>
                                  </DialogHeader>
                                  {selectedBooking && (
                                    <div className="space-y-4">
                                      <div className="grid grid-cols-2 gap-4">
                                        <div>
                                          <label className="text-sm text-gray-400">Customer</label>
                                          <p className="text-white">{selectedBooking.userName}</p>
                                          <p className="text-sm text-gray-400">{selectedBooking.userPhone}</p>
                                        </div>
                                        <div>
                                          <label className="text-sm text-gray-400">Facility</label>
                                          <p className="text-white">{selectedBooking.facilityName} - {selectedBooking.courtName}</p>
                                        </div>
                                        <div>
                                          <label className="text-sm text-gray-400">Date & Time</label>
                                          <p className="text-white">{selectedBooking.date}</p>
                                          <p className="text-sm text-gray-400">{formatTimeSlots(selectedBooking.timeSlots)}</p>
                                        </div>
                                        <div>
                                          <label className="text-sm text-gray-400">Payment</label>
                                          <p className="text-white">Total: Rp {selectedBooking.totalAmount.toLocaleString()}</p>
                                          <p className="text-sm text-gray-400">DP: Rp {selectedBooking.dpAmount.toLocaleString()}</p>
                                        </div>
                                      </div>
                                      
                                      {selectedBooking.paymentProof && (
                                        <div>
                                          <label className="text-sm text-gray-400">Payment Proof</label>
                                          <div className="mt-2 border border-red-500/30 rounded-lg p-2">
                                            <Image
                                              src={selectedBooking.paymentProof}
                                              alt="Payment Proof"
                                              width={300}
                                              height={200}
                                              className="rounded object-cover"
                                            />
                                          </div>
                                        </div>
                                      )}
                                      
                                      {selectedBooking.status === 'pending' && (
                                        <div className="space-y-4">
                                          <div>
                                            <label className="text-sm text-gray-400">Notes (Optional)</label>
                                            <Textarea
                                              value={notes}
                                              onChange={(e) => setNotes(e.target.value)}
                                              placeholder="Add notes for this booking..."
                                              className="bg-black/20 border-red-500/30 text-white placeholder:text-gray-400"
                                            />
                                          </div>
                                          <div className="flex gap-2">
                                            <Button
                                              className="bg-green-600 hover:bg-green-700 text-white"
                                              onClick={() => handleStatusUpdate(selectedBooking.id, 'confirmed')}
                                              disabled={actionLoading === selectedBooking.id}
                                            >
                                              {actionLoading === selectedBooking.id ? (
                                                <Loader2 className="w-4 h-4 animate-spin" />
                                              ) : (
                                                <Check className="w-4 h-4" />
                                              )}
                                              Confirm
                                            </Button>
                                            <Button
                                              className="bg-red-600 hover:bg-red-700 text-white"
                                              onClick={() => handleStatusUpdate(selectedBooking.id, 'rejected')}
                                              disabled={actionLoading === selectedBooking.id}
                                            >
                                              <X className="w-4 h-4" />
                                              Reject
                                            </Button>
                                            <Button
                                              className="bg-gray-600 hover:bg-gray-700 text-white"
                                              onClick={() => handleStatusUpdate(selectedBooking.id, 'cancelled')}
                                              disabled={actionLoading === selectedBooking.id}
                                            >
                                              <X className="w-4 h-4" />
                                              Cancel
                                            </Button>
                                          </div>
                                        </div>
                                      )}
                                      
                                      {selectedBooking.notes && (
                                        <div>
                                          <label className="text-sm text-gray-400">Notes</label>
                                          <p className="text-white bg-black/20 p-3 rounded border border-red-500/30">
                                            {selectedBooking.notes}
                                          </p>
                                        </div>
                                      )}
                                    </div>
                                  )}
                                </DialogContent>
                              </Dialog>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </DashboardLayout>
    </ProtectedRoute>
  );
}
