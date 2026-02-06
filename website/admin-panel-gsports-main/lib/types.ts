// Firebase Collections Structure for GSC Booking System

export interface Facility {
  id: string;
  name: string; // 'futsal' | 'badminton' | 'pickleball' | 'billiard' | 'playstation'
  type: 'court' | 'table' | 'console';
  courts: Court[];
  pricePerHour: number;
  dpPercentage: number; // Down payment percentage (e.g., 50 for 50%)
  isActive: boolean;
  createdAt: string;
}

export interface Court {
  id: string;
  name: string; // 'Court 1', 'Table 1', 'PS 1', etc.
  facilityId: string;
  isActive: boolean;
}

export interface TimeSlot {
  hour: number; // 7-23 (7 AM to 11 PM)
  isAvailable: boolean;
  bookedBy?: string; // booking ID if booked
}

export interface Booking {
  id: string;
  userName: string;
  userPhone: string;
  facilityId: string;
  facilityName: string;
  courtId: string;
  courtName: string;
  date: string; // YYYY-MM-DD format
  timeSlots: number[]; // Array of hours [13, 14, 15] for 1-3 PM
  totalHours: number;
  pricePerHour: number;
  totalAmount: number;
  dpAmount: number;
  remainingAmount: number;
  paymentProof: string; // URL to uploaded image
  status: 'pending' | 'confirmed' | 'rejected' | 'cancelled';
  createdAt: string;
  updatedAt: string;
  notes?: string;
}

export interface BookingSlot {
  id: string;
  facilityId: string;
  courtId: string;
  date: string;
  hour: number;
  bookingId: string;
  isLocked: boolean;
  lockedUntil: string; // ISO timestamp when lock expires
}

// Firestore Collections:
// - facilities: Facility[]
// - courts: Court[]
// - bookings: Booking[]
// - booking_slots: BookingSlot[]