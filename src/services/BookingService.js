import { 
  collection, 
  getDocs, 
  addDoc, 
  query, 
  where, 
  orderBy,
  doc,
  updateDoc,
  deleteDoc,
  serverTimestamp,
  Timestamp 
} from 'firebase/firestore';
import { db } from '../config/firebase.js';

export class BookingService {
  // Get all facilities (Futsal, Badminton, Pickleball)
  static async getFacilities() {
    try {
      const facilitiesRef = collection(db, 'facilities');
      const snapshot = await getDocs(facilitiesRef);
      return snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
    } catch (error) {
      console.error('Error fetching facilities:', error);
      throw error;
    }
  }

  // Get available time slots (7 AM - 11 PM) for specific court and date
  static async getAvailableSlots(courtId, date) {
    try {
      // Generate all possible slots from 7 AM to 11 PM (7-23)
      const allSlots = Array.from({ length: 17 }, (_, i) => i + 7);
      
      // Get existing bookings for this court and date
      const bookingsRef = collection(db, 'bookings');
      const q = query(
        bookingsRef,
        where('courtId', '==', courtId),
        where('date', '==', date),
        where('status', 'in', ['confirmed', 'pending'])
      );
      
      const snapshot = await getDocs(q);
      const bookedSlots = new Set();
      
      // Collect all booked time slots
      snapshot.docs.forEach(doc => {
        const booking = doc.data();
        if (booking.timeSlots && Array.isArray(booking.timeSlots)) {
          booking.timeSlots.forEach(slot => bookedSlots.add(slot));
        }
      });
      
      // Return only available slots
      return allSlots.filter(slot => !bookedSlots.has(slot));
    } catch (error) {
      console.error('Error fetching available slots:', error);
      throw error;
    }
  }

  // Create new booking with all required data
  static async createBooking(bookingData) {
    try {
      // Validate required fields
      if (!bookingData.userId || !bookingData.courtId || !bookingData.date || 
          !bookingData.timeSlots || bookingData.timeSlots.length === 0 || 
          !bookingData.paymentProof) {
        throw new Error('Missing required booking data');
      }

      // Check if slots are still available
      const availableSlots = await this.getAvailableSlots(bookingData.courtId, bookingData.date);
      const requestedSlots = bookingData.timeSlots;
      const allSlotsAvailable = requestedSlots.every(slot => availableSlots.includes(slot));
      
      if (!allSlotsAvailable) {
        throw new Error('Some time slots are no longer available');
      }

      const bookingsRef = collection(db, 'bookings');
      const docRef = await addDoc(bookingsRef, {
        ...bookingData,
        createdAt: serverTimestamp(),
        updatedAt: serverTimestamp(),
        bookingDate: Timestamp.now()
      });
      
      return docRef.id;
    } catch (error) {
      console.error('Error creating booking:', error);
      throw error;
    }
  }

  // Get user's booking history
  static async getUserBookings(userId) {
    try {
      console.log('Fetching bookings for userId:', userId);
      const bookingsRef = collection(db, 'bookings');
      
      // Simple query without orderBy first
      const q = query(
        bookingsRef,
        where('userId', '==', userId)
      );
      
      const snapshot = await getDocs(q);
      console.log('Found bookings:', snapshot.size);
      
      // Sort manually in JavaScript
      const bookings = snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
      
      // Sort by createdAt manually
      bookings.sort((a, b) => {
        const timeA = a.createdAt?.toMillis() || 0;
        const timeB = b.createdAt?.toMillis() || 0;
        return timeB - timeA; // Descending
      });
      
      return bookings;
    } catch (error) {
      console.error('Error fetching user bookings:', error);
      throw error;
    }
  }
}