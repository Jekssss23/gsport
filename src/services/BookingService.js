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
  getDoc,
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

  // Get facility data by court ID
  static async getFacilityByCourtId(courtId) {
    try {
      const facilities = await this.getFacilities();
      for (const facility of facilities) {
        const court = facility.courts.find(c => c.id === courtId);
        if (court) {
          return { ...facility, selectedCourt: court };
        }
      }
      throw new Error('Facility not found for court ID: ' + courtId);
    } catch (error) {
      console.error('Error fetching facility by court ID:', error);
      throw error;
    }
  }

  // Calculate total price and DP for a booking
  static calculateBookingPrice(facility, timeSlots) {
    let totalPrice = 0;
    for (const slot of timeSlots) {
      if (facility.name === 'Pickleball') {
        totalPrice += facility.pricePerHour;
      } else {
        if (slot <= facility.priceSplitHour) {
          totalPrice += facility.morningPrice;
        } else {
          totalPrice += facility.eveningPrice;
        }
      }
    }
    const dpAmount = totalPrice * (facility.dpPercentage / 100);
    return { totalPrice, dpAmount };
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

      // Get facility data
      const facility = await this.getFacilityByCourtId(bookingData.courtId);

      // Calculate price
      const { totalPrice, dpAmount } = this.calculateBookingPrice(facility, bookingData.timeSlots);

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
        totalPrice,
        dpAmount,
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

      // Add facilityName and userName
      const enrichedBookings = await Promise.all(
        bookings.map(async (booking) => {
          const user = await this.getUser(booking.userId);
          const facility = await this.getFacilityByCourtId(booking.courtId);
          return {
            ...booking,
            userName: user ? user.name || user.email : 'Unknown',
            facilityName: facility ? facility.name : 'Unknown',
            courtName: facility ? facility.selectedCourt.name : booking.courtId,
          };
        })
      );

      // Sort by createdAt manually
      enrichedBookings.sort((a, b) => {
        const timeA = a.createdAt?.toMillis() || 0;
        const timeB = b.createdAt?.toMillis() || 0;
        return timeB - timeA; // Descending
      });

      return enrichedBookings;
    } catch (error) {
      console.error('Error fetching user bookings:', error);
      throw error;
    }
  }

  // Get all bookings for admin
  static async getAllBookings() {
    try {
      const bookingsRef = collection(db, 'bookings');
      const snapshot = await getDocs(bookingsRef);
      const bookings = snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));

      // Sort by createdAt descending
      bookings.sort((a, b) => {
        const timeA = a.createdAt?.toMillis() || 0;
        const timeB = b.createdAt?.toMillis() || 0;
        return timeB - timeA;
      });

      return bookings;
    } catch (error) {
      console.error('Error fetching all bookings:', error);
      throw error;
    }
  }

  // Update booking status
  static async updateBookingStatus(bookingId, status) {
    try {
      const bookingRef = doc(db, 'bookings', bookingId);
      await updateDoc(bookingRef, {
        status,
        updatedAt: serverTimestamp()
      });
    } catch (error) {
      console.error('Error updating booking status:', error);
      throw error;
    }
  }

  // Update booking with additional data
  static async updateBooking(bookingId, updateData) {
    try {
      const bookingRef = doc(db, 'bookings', bookingId);
      await updateDoc(bookingRef, {
        ...updateData,
        updatedAt: serverTimestamp()
      });
    } catch (error) {
      console.error('Error updating booking:', error);
      throw error;
    }
  }

  // Get user data by ID
  static async getUser(userId) {
    try {
      const userRef = doc(db, 'users', userId);
      const userSnap = await getDoc(userRef);
      if (userSnap.exists()) {
        return { id: userSnap.id, ...userSnap.data() };
      }
      return null;
    } catch (error) {
      console.error('Error fetching user:', error);
      throw error;
    }
  }

  // Request cancellation (user initiates)
  static async requestCancellation(bookingId, reason) {
    try {
      const bookingRef = doc(db, 'bookings', bookingId);
      await updateDoc(bookingRef, {
        status: 'cancellation_requested',
        cancellationReason: reason,
        cancellationRequestedAt: serverTimestamp(),
        updatedAt: serverTimestamp()
      });
    } catch (error) {
      console.error('Error requesting cancellation:', error);
      throw error;
    }
  }

  // Approve cancellation (admin approves)
  static async approveCancellation(bookingId) {
    try {
      const bookingRef = doc(db, 'bookings', bookingId);
      await updateDoc(bookingRef, {
        status: 'cancelled',
        cancellationApprovedAt: serverTimestamp(),
        updatedAt: serverTimestamp()
      });
    } catch (error) {
      console.error('Error approving cancellation:', error);
      throw error;
    }
  }

  // Reject cancellation (admin rejects)
  static async rejectCancellation(bookingId) {
    try {
      const bookingRef = doc(db, 'bookings', bookingId);
      await updateDoc(bookingRef, {
        status: 'confirmed',
        cancellationRejectedAt: serverTimestamp(),
        updatedAt: serverTimestamp()
      });
    } catch (error) {
      console.error('Error rejecting cancellation:', error);
      throw error;
    }
  }

  // Get bookings with cancellation requests for admin
  static async getCancellationRequests() {
    try {
      const bookingsRef = collection(db, 'bookings');
      const q = query(
        bookingsRef,
        where('status', '==', 'cancellation_requested')
      );

      const snapshot = await getDocs(q);
      const bookings = snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));

      // Sort by cancellation request time
      bookings.sort((a, b) => {
        const timeA = a.cancellationRequestedAt?.toMillis() || 0;
        const timeB = b.cancellationRequestedAt?.toMillis() || 0;
        return timeB - timeA;
      });

      return bookings;
    } catch (error) {
      console.error('Error fetching cancellation requests:', error);
      throw error;
    }
  }

  // Archive booking (move to bookings_archive collection)
  static async archiveBooking(bookingId) {
    try {
      // Get the booking data first
      const bookingRef = doc(db, 'bookings', bookingId);
      const bookingSnap = await getDoc(bookingRef);

      if (!bookingSnap.exists()) {
        throw new Error('Booking not found');
      }

      const bookingData = bookingSnap.data();

      // Add to archive collection
      const archiveRef = collection(db, 'bookings_archive');
      await addDoc(archiveRef, {
        ...bookingData,
        originalBookingId: bookingId,
        archivedAt: serverTimestamp(),
        archivedBy: 'admin'
      });

      // Delete from original collection
      await deleteDoc(bookingRef);

    } catch (error) {
      console.error('Error archiving booking:', error);
      throw error;
    }
  }

  // Add review
  static async addReview(reviewData) {
    try {
      const reviewsRef = collection(db, 'reviews');
      await addDoc(reviewsRef, {
        ...reviewData,
        createdAt: serverTimestamp()
      });
    } catch (error) {
      console.error('Error adding review:', error);
      throw error;
    }
  }

  // Get reviews for a facility
  static async getFacilityReviews(facilityId) {
    try {
      const reviewsRef = collection(db, 'reviews');
      const q = query(
        reviewsRef,
        where('facilityId', '==', facilityId),
        orderBy('createdAt', 'desc')
      );

      const snapshot = await getDocs(q);
      return snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
    } catch (error) {
      console.error('Error fetching facility reviews:', error);
      throw error;
    }
  }

  // Update booking status to completed
  static async markBookingCompleted(bookingId) {
    try {
      const bookingRef = doc(db, 'bookings', bookingId);
      await updateDoc(bookingRef, {
        status: 'completed',
        completedAt: serverTimestamp(),
        updatedAt: serverTimestamp()
      });
    } catch (error) {
      console.error('Error marking booking as completed:', error);
      throw error;
    }
  }

  // Check and auto-complete expired bookings
  static async checkAndUpdateExpiredBookings() {
    try {
      const bookingsRef = collection(db, 'bookings');
      const q = query(
        bookingsRef,
        where('status', 'in', ['confirmed', 'pending'])
      );

      const snapshot = await getDocs(q);
      const now = new Date();

      for (const docSnapshot of snapshot.docs) {
        const booking = docSnapshot.data();
        const bookingDateTime = new Date(`${booking.date}T${booking.endTime || '23:59'}`);

        if (bookingDateTime < now) {
          await this.markBookingCompleted(docSnapshot.id);
        }
      }
    } catch (error) {
      console.error('Error checking expired bookings:', error);
      throw error;
    }
  }
}