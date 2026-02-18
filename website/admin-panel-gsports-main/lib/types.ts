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
  status: 'pending' | 'confirmed' | 'rejected' | 'cancelled' | 'cancellation_requested' | 'completed';
  createdAt: string;
  updatedAt: string;
  notes?: string;
  cancellationRequested?: boolean;
  cancellationReason?: string;
  cancellationRequestedAt?: string;
  staffOnDutyId?: string;
  staffOnDutyName?: string;
  staffOnDutyImageUrl?: string;
  staffRating?: number; // 1-5 stars
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
// - employees: Employee[]
// - employee_schedules: EmployeeSchedule[]

export interface Employee {
  id: string;
  name: string;
  divisi: string;
  imageUrl: string;
  email?: string;
  authUid?: string;
  hasAccount?: boolean;
  createdAt: any;
  rollingConfig?: {
    enabled: boolean;
    startDate: string; // Reference Monday (YYYY-MM-DD)
    weeks: {
      shift: 'morning' | 'night' | 'middle';
      startTime: string;
      endTime: string;
      outlet?: 'futsal' | 'swimming' | 'pickleball_badminton';
      days: number[]; // 0-6 (Mon-Sun)
    }[];
  };
}

export interface EmployeeSchedule {
  id: string;
  employeeId: string;
  employeeName: string;
  employeeImageUrl: string;
  divisi: string;
  date: string; // YYYY-MM-DD
  shift: 'morning' | 'middle' | 'night';
  outlet?: 'swimming' | 'futsal' | 'pickleball_badminton'; // Only for Sports
  startTime: string; // HH:mm
  endTime: string; // HH:mm
  createdAt: string;
}