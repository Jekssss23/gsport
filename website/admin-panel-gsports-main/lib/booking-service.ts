import { db } from './firebase';
import {
  collection,
  doc,
  getDocs,
  getDoc,
  addDoc,
  updateDoc,
  query,
  where,
  orderBy,
  Timestamp,
  writeBatch
} from 'firebase/firestore';
import { Booking, Facility, Court, BookingSlot } from './types';

export class BookingService {
  // Get all facilities with their courts
  static async getFacilities(): Promise<Facility[]> {
    const facilitiesRef = collection(db, 'facilities');
    const snapshot = await getDocs(facilitiesRef);

    const facilities = await Promise.all(
      snapshot.docs.map(async (doc) => {
        const facilityData = { id: doc.id, ...doc.data() } as Facility;

        // Get courts for this facility
        const courtsRef = collection(db, 'courts');
        const courtsQuery = query(courtsRef, where('facilityId', '==', doc.id));
        const courtsSnapshot = await getDocs(courtsQuery);

        facilityData.courts = courtsSnapshot.docs.map(courtDoc => ({
          id: courtDoc.id,
          ...courtDoc.data()
        })) as Court[];

        return facilityData;
      })
    );

    return facilities;
  }

  // Get available time slots for a specific court on a date
  static async getAvailableSlots(courtId: string, date: string): Promise<number[]> {
    const slotsRef = collection(db, 'booking_slots');
    const slotsQuery = query(
      slotsRef,
      where('courtId', '==', courtId),
      where('date', '==', date)
    );

    const snapshot = await getDocs(slotsQuery);
    const bookedHours = new Set<number>();
    const now = new Date();

    snapshot.docs.forEach(doc => {
      const slot = doc.data() as BookingSlot;

      // Check if slot is still locked and not expired
      if (slot.isLocked && new Date(slot.lockedUntil) > now) {
        bookedHours.add(slot.hour);
      }
    });

    // CHECK MOBILE BOOKINGS (bookings collection)
    const bookingsRef = collection(db, 'bookings');
    const bookingsQuery = query(
      bookingsRef,
      where('courtId', '==', courtId),
      where('date', '==', date),
      where('status', 'in', ['pending', 'confirmed'])
    );

    const bookingsSnapshot = await getDocs(bookingsQuery);
    bookingsSnapshot.docs.forEach(doc => {
      const booking = doc.data() as Booking;
      if (booking.timeSlots && Array.isArray(booking.timeSlots)) {
        booking.timeSlots.forEach(slot => bookedHours.add(slot));
      }
    });

    // Generate all possible hours (7 AM to 11 PM)
    const allHours = Array.from({ length: 17 }, (_, i) => i + 7); // 7-23
    return allHours.filter(hour => !bookedHours.has(hour));
  }

  // Create a new booking
  static async createBooking(bookingData: Omit<Booking, 'id' | 'createdAt' | 'updatedAt'>): Promise<string> {
    const batch = writeBatch(db);

    // Add booking
    const bookingRef = doc(collection(db, 'bookings'));
    const booking: Booking = {
      ...bookingData,
      id: bookingRef.id,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    };

    batch.set(bookingRef, booking);

    // Lock time slots
    const lockUntil = new Date();
    lockUntil.setHours(lockUntil.getHours() + 2); // Lock for 2 hours

    bookingData.timeSlots.forEach(hour => {
      const slotRef = doc(collection(db, 'booking_slots'));
      const slot: BookingSlot = {
        id: slotRef.id,
        facilityId: bookingData.facilityId,
        courtId: bookingData.courtId,
        date: bookingData.date,
        hour,
        bookingId: bookingRef.id,
        isLocked: true,
        lockedUntil: lockUntil.toISOString()
      };
      batch.set(slotRef, slot);
    });

    await batch.commit();
    return bookingRef.id;
  }

  // Get all bookings for admin
  static async getAllBookings(): Promise<Booking[]> {
    const bookingsRef = collection(db, 'bookings');
    const bookingsQuery = query(bookingsRef, orderBy('createdAt', 'desc'));
    const snapshot = await getDocs(bookingsQuery);

    return snapshot.docs.map(doc => ({
      id: doc.id,
      ...doc.data()
    })) as Booking[];
  }

  // Update booking status
  static async updateBookingStatus(
    bookingId: string,
    status: Booking['status'],
    notes?: string
  ): Promise<void> {
    const bookingRef = doc(db, 'bookings', bookingId);
    const updateData: Partial<Booking> = {
      status,
      updatedAt: new Date().toISOString()
    };

    if (notes) {
      updateData.notes = notes;
    }

    await updateDoc(bookingRef, updateData);

    // If rejected or cancelled, unlock the slots
    if (status === 'rejected' || status === 'cancelled') {
      await this.unlockBookingSlots(bookingId);
    }
  }

  // Unlock booking slots
  static async unlockBookingSlots(bookingId: string): Promise<void> {
    const slotsRef = collection(db, 'booking_slots');
    const slotsQuery = query(slotsRef, where('bookingId', '==', bookingId));
    const snapshot = await getDocs(slotsQuery);

    const batch = writeBatch(db);
    snapshot.docs.forEach(doc => {
      batch.delete(doc.ref);
    });

    await batch.commit();
  }

  // Archive booking (move to bookings_archive collection)
  static async archiveBooking(bookingId: string): Promise<void> {
    try {
      // Get the booking data first
      const bookingRef = doc(db, 'bookings', bookingId);
      const bookingSnap = await getDoc(bookingRef);

      if (!bookingSnap.exists()) {
        throw new Error('Booking not found');
      }

      const bookingData = bookingSnap.data();

      const batch = writeBatch(db);

      // Add to archive collection
      const archiveRef = doc(collection(db, 'bookings_archive'));
      batch.set(archiveRef, {
        ...bookingData,
        originalBookingId: bookingId,
        archivedAt: new Date().toISOString(),
        archivedBy: 'admin'
      });

      // Delete from original collection
      batch.delete(bookingRef);

      // Also delete any booking slots
      const slotsRef = collection(db, 'booking_slots');
      const slotsQuery = query(slotsRef, where('bookingId', '==', bookingId));
      const slotsSnapshot = await getDocs(slotsQuery);
      slotsSnapshot.docs.forEach(doc => {
        batch.delete(doc.ref);
      });

      await batch.commit();
    } catch (error) {
      console.error('Error archiving booking:', error);
      throw error;
    }
  }

  // Clean expired locks (should be run periodically)
  static async cleanExpiredLocks(): Promise<void> {
    const now = new Date().toISOString();
    const slotsRef = collection(db, 'booking_slots');
    const expiredQuery = query(slotsRef, where('lockedUntil', '<', now));
    const snapshot = await getDocs(expiredQuery);

    const batch = writeBatch(db);
    snapshot.docs.forEach(doc => {
      batch.delete(doc.ref);
    });

    await batch.commit();
  }
}