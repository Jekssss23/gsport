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
import { API_BASE_URL } from '../config/api.js';

export class BookingService {
  static isMysqlReservationId(bookingId) {
    if (typeof bookingId === 'number') return true;
    if (typeof bookingId !== 'string') return false;
    return /^\d+$/.test(bookingId);
  }

  // Get all facilities (Futsal, Badminton, Pickleball)
  static async getFacilities() {
    try {
      const res = await fetch(`${API_BASE_URL}/reservation/facilities`);
      const json = await res.json();
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to fetch facilities');
      }

      return (json.data || []).map((f) => ({
        id: Number(f.id),
        name: f.name,
        pricePerHour: Number(f.price_per_hour || 0),
        dpPercentage: Number(f.dp_percentage || 0),
        courts: (f.courts || []).map((c) => ({
          id: Number(c.id),
          name: c.name,
        })),
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
      const res = await fetch(`${API_BASE_URL}/reservation/availability?court_id=${encodeURIComponent(courtId)}&date=${encodeURIComponent(date)}`);
      const json = await res.json();
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to fetch availability');
      }
      return (json.data?.available_hours || []).map((h) => Number(h));
    } catch (error) {
      console.error('Error fetching available slots:', error);
      throw error;
    }
  }

  // Get employees on duty for a specific date
  static async getEmployeesOnDuty(date) {
    try {
      const schedulesRef = collection(db, 'employee_schedules');
      const q = query(
        schedulesRef,
        where('date', '==', date)
      );

      const snapshot = await getDocs(q);
      return snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
    } catch (error) {
      console.error('Error fetching employees on duty:', error);
      throw error;
    }
  }

  // Get specific staff by date, hour and facility (Sports)
  static async getStaffBySlot(date, hour, facilityName) {
    try {
      // 1. Check for manual schedule first (Override)
      const manualSchedules = await this.getEmployeesOnDuty(date);
      const manualStaff = manualSchedules.find(s => {
        // Strict check: Only Sports division
        if (s.divisi !== 'Sports') return false;

        const startHour = parseInt(s.startTime.split(':')[0]);
        const endHour = parseInt(s.endTime.split(':')[0]);
        const isTimeMatch = hour >= startHour && hour < endHour;
        if (!isTimeMatch) return false;

        // Location match based on facility
        const lowerFacility = facilityName.toLowerCase();
        let outletMatch = false;
        if (lowerFacility.includes('futsal')) outletMatch = s.outlet === 'futsal' || s.outlet === 'all';
        else if (lowerFacility.includes('swimming') || lowerFacility.includes('renang')) outletMatch = s.outlet === 'swimming' || s.outlet === 'all';
        else if (lowerFacility.includes('badminton') || lowerFacility.includes('pickleball')) outletMatch = s.outlet === 'pickleball_badminton' || s.outlet === 'all';
        
        return outletMatch;
      });

      if (manualStaff) return manualStaff;

      // 2. If no manual schedule, check Rolling Patterns
      const res = await fetch(`${API_BASE_URL}/reservation/staff_schedules`);
      const json = await res.json();
      const employees = json.ok ? json.data : [];

      const targetDate = new Date(date);
      const dayOfWeek = targetDate.getDay() === 0 ? 6 : targetDate.getDay() - 1; // 0: Mon, ..., 6: Sun

      for (const emp of employees) {
        // Strict check: Only Sports division
        if (emp.divisi !== 'Sports') continue;

        if (emp.jadwal_operasional && typeof emp.jadwal_operasional === 'string' && emp.jadwal_operasional.trim() !== '') {
          try {
            const config = JSON.parse(emp.jadwal_operasional);
            
            // Check if today is the regular day off
            if (parseInt(config.libur) === dayOfWeek) {
              continue;
            }

            // Determine if current week is odd or even mathematically
            const startDate = new Date(targetDate.getFullYear(), 0, 1);
            const days = Math.floor((targetDate - startDate) / (24 * 60 * 60 * 1000));
            const weekNumber = Math.ceil(days / 7);
            const isGanjil = weekNumber % 2 !== 0;

            const currentShiftStr = isGanjil ? config.ganjil : config.genap;
            if (!currentShiftStr) continue;
            
            const [startStr, endStr] = currentShiftStr.split('-');
            const startHour = parseInt(startStr.split(':')[0]);
            const endHour = parseInt(endStr.split(':')[0]);

            const isTimeMatch = hour >= startHour && hour < endHour;
            if (!isTimeMatch) continue;

            // Location match based on facility
            const lowerFacility = facilityName.toLowerCase();
            let outletMatch = false;
            if (lowerFacility.includes('futsal')) outletMatch = config.lokasi === 'futsal' || config.lokasi === 'all';
            else if (lowerFacility.includes('swimming') || lowerFacility.includes('renang')) outletMatch = config.lokasi === 'swimming' || config.lokasi === 'all';
            else if (lowerFacility.includes('badminton') || lowerFacility.includes('pickleball')) outletMatch = config.lokasi === 'pickleball_badminton' || config.lokasi === 'all';

            if (outletMatch) {
              return {
                employeeId: 'EMP_' + emp.id,
                employeeName: emp.name,
                employeeImageUrl: emp.imageUrl || '',
                divisi: emp.divisi,
                isRolling: true
              };
            }
          } catch(e) {}
        }
      }

      return null;
    } catch (error) {
      console.error('Error getting staff by slot:', error);
      return null;
    }
  }

  // Get ratings for a specific employee
  static async getEmployeeRatings(employeeId) {
    try {
      const reviewsRef = collection(db, 'reviews');
      const q = query(
        reviewsRef,
        where('staffOnDutyId', '==', employeeId),
        orderBy('createdAt', 'desc')
      );

      const snapshot = await getDocs(q);
      return snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
    } catch (error) {
      console.error('Error fetching employee ratings:', error);
      // Fallback: If no index yet or field doesn't exist in early docs, 
      // try a simpler query or handle error gracefully
      return [];
    }
  }

  // Create new booking with all required data
  static async createBooking(bookingData) {
    try {
      // Normalize payload keys (some screens use different names)
      const timeSlots = bookingData.timeSlots || bookingData.slots || bookingData.time_slots;
      const paymentProof =
        bookingData.paymentProof || bookingData.paymentProofUrl || bookingData.payment_proof_url;

      // Validate required fields
      if (!bookingData.userId || !bookingData.courtId || !bookingData.date ||
        !Array.isArray(timeSlots) || timeSlots.length === 0 ||
        !paymentProof) {
        throw new Error('Missing required booking data');
      }

      const facilityId = bookingData.facilityId;
      if (!facilityId) {
        throw new Error('Missing facilityId');
      }

      const payload = {
        firebase_uid: bookingData.userId,
        user_name: bookingData.userName || bookingData.userId,
        user_phone: bookingData.userPhone || '',
        facility_id: Number(facilityId),
        court_id: Number(bookingData.courtId),
        booking_date: bookingData.date,
        time_slots: timeSlots,
        payment_proof_url: paymentProof,
        total_amount: Number(bookingData.totalAmount || 0),
        dp_amount: Number(bookingData.dpAmount || 0),
        remaining_amount: Number(bookingData.remainingAmount || 0),
      };

      const res = await fetch(`${API_BASE_URL}/reservation/create`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      });
      const json = await res.json();
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to create reservation');
      }

      return json.data?.reservation_id;
    } catch (error) {
      console.error('Error creating booking:', error);
      throw error;
    }
  }

  // Get user's booking history
  static async getUserBookings(userId) {
    try {
      const res = await fetch(`${API_BASE_URL}/reservation/my?firebase_uid=${encodeURIComponent(userId)}`);
      const json = await res.json();
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to fetch reservations');
      }

      return (json.data || []).map((r) => ({
        id: Number(r.id),
        reservationCode: r.reservation_code,
        userId: r.firebase_uid,
        userName: r.user_name,
        userPhone: r.user_phone,
        facilityId: Number(r.facility_id),
        facilityName: r.facility_name,
        courtId: Number(r.court_id),
        courtName: r.court_name,
        date: r.booking_date,
        timeSlots: (r.time_slots || []).map((h) => Number(h)),
        totalHours: (r.time_slots || []).length,
        totalAmount: Number(r.total_amount || 0),
        dpAmount: Number(r.dp_amount || 0),
        remainingAmount: Number(r.remaining_amount || 0),
        paymentProof: r.payment_proof_url,
        status: r.status,
        createdAt: r.created_at,
        updatedAt: r.updated_at,
        assignedStaffName: r.assigned_staff_name || null,
      }));
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
      if (this.isMysqlReservationId(bookingId)) {
        return;
      }
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
      if (this.isMysqlReservationId(bookingId)) {
        return;
      }
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
      // Handle both MySQL reservations (number) and Firestore bookings (string)
      if (this.isMysqlReservationId(bookingId)) {
        // For MySQL reservations, use API to update status
        const res = await fetch(`${API_BASE_URL}/reservation/cancel`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            reservation_id: Number(bookingId),
            reason: reason
          }),
        });
        const json = await res.json();
        if (!res.ok || !json.ok) {
          throw new Error(json.message || 'Failed to request cancellation');
        }
        return json.data;
      } else {
        // For Firestore bookings, update document directly
        const bookingRef = doc(db, 'bookings', String(bookingId));
        await updateDoc(bookingRef, {
          status: 'cancellation_requested',
          cancellationReason: reason,
          cancellationRequestedAt: serverTimestamp(),
          updatedAt: serverTimestamp()
        });
      }
    } catch (error) {
      console.error('Error requesting cancellation:', error);
      throw error;
    }
  }

  // Approve cancellation (admin approves)
  static async approveCancellation(bookingId) {
    try {
      // Handle both MySQL reservations (number) and Firestore bookings (string)
      if (this.isMysqlReservationId(bookingId)) {
        // For MySQL reservations, use API to update status
        const res = await fetch(`${API_BASE_URL}/reservation/approve_cancel`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            reservation_id: Number(bookingId)
          }),
        });
        const json = await res.json();
        if (!res.ok || !json.ok) {
          throw new Error(json.message || 'Failed to approve cancellation');
        }
        return json.data;
      } else {
        // For Firestore bookings, update document directly
        const bookingRef = doc(db, 'bookings', String(bookingId));
        await updateDoc(bookingRef, {
          status: 'cancelled',
          cancellationApprovedAt: serverTimestamp(),
          updatedAt: serverTimestamp()
        });
      }
    } catch (error) {
      console.error('Error approving cancellation:', error);
      throw error;
    }
  }

  // Reject cancellation (admin rejects)
  static async rejectCancellation(bookingId) {
    try {
      // Handle both MySQL reservations (number) and Firestore bookings (string)
      if (this.isMysqlReservationId(bookingId)) {
        // For MySQL reservations, use API to update status
        const res = await fetch(`${API_BASE_URL}/reservation/reject_cancel`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            reservation_id: Number(bookingId)
          }),
        });
        const json = await res.json();
        if (!res.ok || !json.ok) {
          throw new Error(json.message || 'Failed to reject cancellation');
        }
        return json.data;
      } else {
        // For Firestore bookings, update document directly
        const bookingRef = doc(db, 'bookings', String(bookingId));
        await updateDoc(bookingRef, {
          status: 'confirmed',
          cancellationRejectedAt: serverTimestamp(),
          updatedAt: serverTimestamp()
        });
      }
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
      // First, sync MySQL booking to Firestore if needed
      await this.syncBookingToFirestore(reviewData.bookingId);

      const reviewsRef = collection(db, 'reviews');
      await addDoc(reviewsRef, {
        ...reviewData,
        bookingId: String(reviewData.bookingId),
        createdAt: serverTimestamp()
      });

      // Update hasRated in Firestore
      const bookingRef = doc(db, 'bookings', String(reviewData.bookingId));
      await updateDoc(bookingRef, {
        hasRated: true,
        updatedAt: serverTimestamp()
      });

      // Update has_rated in MySQL via API
      if (this.isMysqlReservationId(reviewData.bookingId)) {
        try {
          await fetch(`${API_BASE_URL}/reservation/rate`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              reservation_id: Number(reviewData.bookingId),
              has_rated: 1
            }),
          });
        } catch (e) {
          console.error('Error updating MySQL rating status:', e);
        }
      }
    } catch (error) {
      console.error('Error adding review:', error);
      throw error;
    }
  }

  // Sync MySQL booking to Firestore
  static async syncBookingToFirestore(bookingId) {
    try {
      // Check if booking exists in Firestore
      const bookingRef = doc(db, 'bookings', String(bookingId));
      const bookingDoc = await getDoc(bookingRef);
      
      if (!bookingDoc.exists()) {
        console.log('DEBUG: Syncing MySQL booking to Firestore:', bookingId);
        
        // Get booking data from API
        const res = await fetch(`${API_BASE_URL}/reservation/get_booking?id=${bookingId}`);
        const json = await res.json();
        
        if (res.ok && json.data) {
          const booking = json.data;
          await setDoc(bookingRef, {
            id: String(booking.id),
            firebase_uid: booking.firebase_uid,
            user_name: booking.user_name,
            user_phone: booking.user_phone,
            facility_id: booking.facility_id,
            facility_name: booking.facility_name,
            court_id: booking.court_id,
            court_name: booking.court_name,
            booking_date: booking.booking_date,
            time_slots: booking.time_slots || [],
            total_amount: booking.total_amount || 0,
            dp_amount: booking.dp_amount || 0,
            remaining_amount: booking.remaining_amount || 0,
            payment_proof_url: booking.payment_proof_url,
            status: booking.status,
            created_at: booking.created_at,
            updated_at: booking.updated_at,
            hasRated: false,
            createdAt: serverTimestamp()
          });
          console.log('DEBUG: MySQL booking synced to Firestore successfully');
        }
      }
    } catch (error) {
      console.error('Error syncing booking to Firestore:', error);
      // Don't throw error, continue with review submission
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
        where('status', '==', 'confirmed')
      );

      const snapshot = await getDocs(q);
      const now = new Date();

      for (const docSnapshot of snapshot.docs) {
        const booking = docSnapshot.data();
        // Get the last time slot to determine the actual end time
        const lastTimeSlot = booking.timeSlots && booking.timeSlots.length > 0 
          ? Math.max(...booking.timeSlots) 
          : 23;
        
        // Create booking end datetime
        const bookingEndDateTime = new Date(`${booking.bookingDate}T${String(lastTimeSlot + 1).padStart(2, '0')}:00:00`);

        if (bookingEndDateTime < now) {
          await this.markBookingCompleted(docSnapshot.id);
        }
      }
    } catch (error) {
      console.error('Error checking expired bookings:', error);
      throw error;
    }
  }
}