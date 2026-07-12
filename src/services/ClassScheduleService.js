import { API_BASE_URL, fetchWithTimeout } from '../config/api';
import { auth } from '../config/firebase';

export class ClassScheduleService {
  // Get available classes
  static async getAvailableClasses(date = null, categoryId = null, email = null) {
    try {
      let url = `${API_BASE_URL}/class_schedule/available`;
      const params = new URLSearchParams();
      
      if (date) params.append('date', date);
      if (categoryId) params.append('category_id', categoryId);
      if (email) params.append('email', email);
      
      if (params.toString()) {
        url += `?${params.toString()}`;
      }
      
      const res = await fetch(url);
      const json = await res.json();
      
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to fetch available classes');
      }
      
      return json.data || [];
    } catch (error) {
      console.error('Error fetching available classes:', error);
      throw error;
    }
  }

  // Get class categories
  static async getCategories() {
    try {
      const res = await fetch(`${API_BASE_URL}/class_schedule/categories`);
      const json = await res.json();
      
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to fetch categories');
      }
      
      return json.data || [];
    } catch (error) {
      console.error('Error fetching categories:', error);
      throw error;
    }
  }

  // Get class types by category
  static async getClassTypes(categoryId) {
    try {
      const res = await fetch(`${API_BASE_URL}/class_schedule/class_types?category_id=${categoryId}`);
      const json = await res.json();
      
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to fetch class types');
      }
      
      return json.data || [];
    } catch (error) {
      console.error('Error fetching class types:', error);
      throw error;
    }
  }

  // Get user's class bookings
  static async getUserBookings(firebaseUid) {
    try {
      const res = await fetch(`${API_BASE_URL}/class_schedule/my_bookings?firebase_uid=${encodeURIComponent(firebaseUid)}`);
      const json = await res.json();
      
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to fetch user bookings');
      }
      
      return json.data || [];
    } catch (error) {
      console.error('Error fetching user bookings:', error);
      throw error;
    }
  }

  // Book a class
  static async bookClass(bookingData) {
    try {
      const res = await fetch(`${API_BASE_URL}/class_schedule/book_class`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(bookingData),
      });
      
      const json = await res.json();
      
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to book class');
      }
      
      return json.data;
    } catch (error) {
      console.error('Error booking class:', error);
      throw error;
    }
  }

  // Cancel class booking
  static async cancelBooking(bookingId, firebaseUid) {
    try {
      const res = await fetch(`${API_BASE_URL}/class_schedule/cancel_booking`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          booking_id: bookingId,
          firebase_uid: firebaseUid,
        }),
      });
      
      const json = await res.json();
      
      if (!res.ok || !json.ok) {
        throw new Error(json.message || 'Failed to cancel booking');
      }
      
      return json.data;
    } catch (error) {
      console.error('Error cancelling booking:', error);
      throw error;
    }
  }

  // Format date for display
  static formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  }

  // Format time for display
  static formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const time = new Date();
    time.setHours(parseInt(hours), parseInt(minutes), 0);
    
    return time.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  // Check if class is full
  static isClassFull(currentParticipants, maxParticipants) {
    return currentParticipants >= maxParticipants;
  }

  // Get available spots
  static getAvailableSpots(currentParticipants, maxParticipants) {
    return Math.max(0, maxParticipants - currentParticipants);
  }

  // Get status color
  static getStatusColor(status) {
    switch (status) {
      case 'scheduled':
        return '#28a745';
      case 'ongoing':
        return '#ffc107';
      case 'completed':
        return '#6c757d';
      case 'cancelled':
        return '#dc3545';
      default:
        return '#6c757d';
    }
  }

  // Get payment status color
  static getPaymentStatusColor(status) {
    switch (status) {
      case 'paid':
        return '#28a745';
      case 'pending':
        return '#ffc107';
      case 'cancelled':
        return '#dc3545';
      case 'refunded':
        return '#6c757d';
      default:
        return '#6c757d';
    }
  }

  // Get real-time status color
  static getRealTimeStatusColor(status) {
    switch (status) {
      case 'ongoing':
        return '#dc3545'; // Red for live
      case 'starting_soon':
        return '#ffc107'; // Yellow for starting soon
      case 'completed':
        return '#6c757d'; // Gray for completed
      case 'pending':
      default:
        return '#28a745'; // Green for pending
    }
  }

  // Get real-time status text
  static getRealTimeStatusText(status) {
    switch (status) {
      case 'ongoing':
        return 'Sedang Berlangsung';
      case 'starting_soon':
        return 'Akan Dimulai';
      case 'completed':
        return 'Selesai';
      case 'pending':
      default:
        return 'Menunggu';
    }
  }

  // Check if class is live
  static isClassLive(classItem) {
    return classItem.is_live === true;
  }

  // Get status indicator style
  static getStatusIndicatorStyle(isLive) {
    return {
      width: 8,
      height: 8,
      borderRadius: 4,
      backgroundColor: isLive ? '#dc3545' : '#28a745',
      marginRight: 8,
    };
  }

  /** Scan QR payload → POST attendance_checkin */
  static async attendanceCheckIn(qrPayload) {
    const user = auth.currentUser;
    if (!user?.email) {
      throw new Error('Anda harus login untuk absensi');
    }
    const res = await fetch(`${API_BASE_URL}/class_schedule/attendance_checkin`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({
        payload: String(qrPayload).trim(),
        email: user.email,
        firebase_uid: user.uid,
      }),
    });
    const text = await res.text();
    let json;
    try {
      json = JSON.parse(text);
    } catch {
      throw new Error('Respons server tidak valid (bukan JSON). Periksa URL API.');
    }
    if (!json.ok) {
      throw new Error(json.message || 'Absensi gagal');
    }
    return json;
  }

  /** Admin: get today's class sessions */
  static async getTodaySessions() {
    const res = await fetch(`${API_BASE_URL}/class_schedule/today_sessions`);
    const json = await res.json();
    if (!res.ok || !json.ok) {
      throw new Error(json.message || 'Failed to fetch today sessions');
    }
    return json.data || [];
  }

  /** Admin: scan MEMBER QR payload → POST attendance_checkin_member */
  static async attendanceCheckInMember(classSessionId, memberQrPayload) {
    const res = await fetch(`${API_BASE_URL}/class_schedule/attendance_checkin_member`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({
        class_session_id: classSessionId,
        payload: String(memberQrPayload).trim(),
      }),
    });
    const text = await res.text();
    let json;
    try {
      json = JSON.parse(text);
    } catch {
      throw new Error('Respons server tidak valid (bukan JSON). Periksa URL API.');
    }
    if (!json.ok) {
      throw new Error(json.message || 'Absensi gagal');
    }
    return json;
  }

  /** Admin/Employee: scan MEMBER QR payload → auto-pick today's session for that member's class */
  static async attendanceCheckInMemberAuto(memberQrPayload) {
    const res = await fetchWithTimeout(`${API_BASE_URL}/class_schedule/attendance_checkin_member_auto`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({
        payload: String(memberQrPayload).trim(),
      }),
    }, 15000);
    const text = await res.text();
    let json;
    try {
      json = JSON.parse(text);
    } catch {
      throw new Error('Respons server tidak valid (bukan JSON). Periksa URL API.');
    }
    if (!json.ok) {
      throw new Error(json.message || 'Absensi gagal');
    }
    return json;
  }
}
