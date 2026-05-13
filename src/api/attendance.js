import AsyncStorage from '@react-native-async-storage/async-storage';
import { API_BASE_URL } from '../config/api';
import { auth, db } from '../config/firebase';
import { doc, getDoc } from 'firebase/firestore';

// Attendance API endpoint
const ATTENDANCE_API_URL = `${API_BASE_URL}/attendance`;

// Helper function for API calls
const apiCall = async (endpoint, method = 'GET', data = null) => {
  try {
    const config = {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'ngrok-skip-browser-warning': '69420'
      },
      timeout: 10000, // 10 seconds timeout
    };

    if (data) {
      config.body = JSON.stringify(data);
    }

    console.log('API Call:', method, `${ATTENDANCE_API_URL}${endpoint}`, data ? 'with data:' : '', data);

    const response = await fetch(`${ATTENDANCE_API_URL}${endpoint}`, config);

    console.log('API Response Status:', response.status);

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const result = await response.json();
    console.log('API Response Data:', result);

    return result;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
};

// Attendance API functions
export const attendanceAPI = {
  // Get attendance settings
  getSettings: async () => {
    return await apiCall('/settings');
  },

  // Check if user is within radius
  checkRadius: async (latitude, longitude) => {
    return await apiCall('/check_radius', 'POST', {
      latitude,
      longitude,
    });
  },

  // Save attendance (check-in)
  saveAttendance: async (userData, locationData) => {
    const attendanceData = {
      employee_uid: userData.uid,
      employee_id: userData.employeeId || userData.uid,
      employee_name: userData.name || userData.displayName,
      employee_email: userData.email,
      latitude: locationData.latitude,
      longitude: locationData.longitude,
      status: locationData.status,
      // mock/fake gps flag for server-side consistency
      is_mocked: locationData.is_mocked === true,

      // selfie proof (preferred: Cloudinary URL)
      selfie_url: locationData.selfie_url,

      // backward compatibility (older flow stored base64)
      photoBase64: locationData.photoBase64,
    };

    return await apiCall('/save', 'POST', attendanceData);
  },

  // Check out
  checkOut: async (userData, locationData = null) => {
    const checkoutData = {
      employee_uid: userData.uid,
      employee_id: userData.employeeId || userData.uid,
    };

    if (locationData) {
      checkoutData.latitude = locationData.latitude;
      checkoutData.longitude = locationData.longitude;
    }

    return await apiCall('/checkout', 'POST', checkoutData);
  },

  // Get attendance history
  getHistory: async (userData, limit = 30, offset = 0) => {
    const params = new URLSearchParams({
      employee_uid: userData.uid,
      employee_id: userData.employeeId || userData.uid,
      limit: limit.toString(),
      offset: offset.toString(),
    });

    return await apiCall(`/history?${params}`);
  },
};

export const getCurrentUserData = async () => {
  try {
    // 1. Get user from Firebase Auth
    if (auth.currentUser) {
      const userDoc = await getDoc(doc(db, 'users', auth.currentUser.uid));
      if (userDoc.exists()) {
        const data = userDoc.data();
        return {
          uid: auth.currentUser.uid,
          email: auth.currentUser.email,
          name: data.name || data.displayName,
          employeeId: data.employeeId || auth.currentUser.uid
        };
      }
    }

    // 2. Fallback
    const userStr = await AsyncStorage.getItem('user');
    if (userStr) {
      return JSON.parse(userStr);
    }
    return null;
  } catch (error) {
    console.error('Error getting user data:', error);
    return null;
  }
};

export default attendanceAPI;
