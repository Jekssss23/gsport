import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Alert, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import * as Location from 'expo-location';
import { auth, db } from '../../config/firebase';
import { collection, addDoc, doc, getDoc, query, where, getDocs, updateDoc } from 'firebase/firestore';
import { theme } from '../../styles/theme';

export default function AttendanceScreen({ navigation }) {
  const [loading, setLoading] = useState(false);
  const [locationLoading, setLocationLoading] = useState(true);
  const [currentLocation, setCurrentLocation] = useState(null);
  const [attendanceSettings, setAttendanceSettings] = useState(null);
  const [todayAttendance, setTodayAttendance] = useState(null);
  const [userName, setUserName] = useState('');

  useEffect(() => {
    fetchUserData();
    fetchAttendanceSettings();
    checkTodayAttendance();
    getCurrentLocation();
  }, []);

  const fetchUserData = async () => {
    try {
      const userDoc = await getDoc(doc(db, 'users', auth.currentUser.uid));
      if (userDoc.exists()) {
        setUserName(userDoc.data().name);
      }
    } catch (error) {
      console.error('Error fetching user data:', error);
    }
  };

  const fetchAttendanceSettings = async () => {
    try {
      const settingsDoc = await getDoc(doc(db, 'settings', 'attendance'));
      if (settingsDoc.exists()) {
        setAttendanceSettings(settingsDoc.data());
      }
    } catch (error) {
      console.error('Error fetching settings:', error);
    }
  };

  const checkTodayAttendance = async () => {
    try {
      const today = new Date().toISOString().split('T')[0];
      const q = query(
        collection(db, 'attendances'),
        where('employeeId', '==', auth.currentUser.uid),
        where('date', '==', today)
      );
      const querySnapshot = await getDocs(q);
      if (!querySnapshot.empty) {
        setTodayAttendance(querySnapshot.docs[0].data());
      }
    } catch (error) {
      console.error('Error checking attendance:', error);
    }
  };

  const getCurrentLocation = async () => {
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        Alert.alert('Permission Denied', 'Location permission is required for attendance');
        setLocationLoading(false);
        return;
      }

      const location = await Location.getCurrentPositionAsync({});
      setCurrentLocation({
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
      });
      setLocationLoading(false);
    } catch (error) {
      console.error('Error getting location:', error);
      Alert.alert('Error', 'Failed to get current location');
      setLocationLoading(false);
    }
  };

  const calculateDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371e3; // Earth radius in meters
    const φ1 = (lat1 * Math.PI) / 180;
    const φ2 = (lat2 * Math.PI) / 180;
    const Δφ = ((lat2 - lat1) * Math.PI) / 180;
    const Δλ = ((lon2 - lon1) * Math.PI) / 180;

    const a =
      Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
      Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c; // Distance in meters
  };

  const handleCheckIn = async () => {
    if (!currentLocation || !attendanceSettings) {
      Alert.alert('Error', 'Location data not available');
      return;
    }

    const distance = calculateDistance(
      currentLocation.latitude,
      currentLocation.longitude,
      attendanceSettings.latitude,
      attendanceSettings.longitude
    );

    if (distance > attendanceSettings.radius) {
      Alert.alert(
        'Out of Range',
        `You are ${Math.round(distance)}m away from the attendance location. You must be within ${attendanceSettings.radius}m to check in.`
      );
      return;
    }

    setLoading(true);
    try {
      const now = new Date();
      const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
      const date = now.toISOString().split('T')[0];
      
      // Determine status based on time (assuming work starts at 08:00)
      const hour = now.getHours();
      const minute = now.getMinutes();
      const status = (hour > 8 || (hour === 8 && minute > 0)) ? 'terlambat' : 'hadir';

      await addDoc(collection(db, 'attendances'), {
        employeeId: auth.currentUser.uid,
        employeeName: userName,
        date,
        checkIn: time,
        status,
        location: currentLocation,
        createdAt: now,
      });

      Alert.alert('Success', 'Check-in successful!');
      checkTodayAttendance();
    } catch (error) {
      console.error('Error checking in:', error);
      Alert.alert('Error', 'Failed to check in');
    } finally {
      setLoading(false);
    }
  };

  const handleCheckOut = async () => {
    if (!todayAttendance) return;

    setLoading(true);
    try {
      const now = new Date();
      const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
      const today = now.toISOString().split('T')[0];

      const q = query(
        collection(db, 'attendances'),
        where('employeeId', '==', auth.currentUser.uid),
        where('date', '==', today)
      );
      const querySnapshot = await getDocs(q);
      
      if (!querySnapshot.empty) {
        const docRef = querySnapshot.docs[0].ref;
        await updateDoc(docRef, {
          checkOut: time,
          updatedAt: now,
        });

        Alert.alert('Success', 'Check-out successful!');
        checkTodayAttendance();
      }
    } catch (error) {
      console.error('Error checking out:', error);
      Alert.alert('Error', 'Failed to check out');
    } finally {
      setLoading(false);
    }
  };

  if (locationLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
        <Text style={styles.loadingText}>Getting your location...</Text>
      </View>
    );
  }

  const distance = currentLocation && attendanceSettings
    ? calculateDistance(
        currentLocation.latitude,
        currentLocation.longitude,
        attendanceSettings.latitude,
        attendanceSettings.longitude
      )
    : null;

  const isInRange = distance && distance <= attendanceSettings?.radius;

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Attendance</Text>
        <View style={{ width: 40 }} />
      </View>

      <View style={styles.content}>
        {/* Location Status Card */}
        <View style={styles.statusCard}>
          <LinearGradient
            colors={isInRange ? ['#10b981', '#059669'] : ['#ef4444', '#dc2626']}
            style={styles.statusGradient}
          >
            <Ionicons 
              name={isInRange ? 'checkmark-circle' : 'close-circle'} 
              size={48} 
              color="white" 
            />
            <Text style={styles.statusTitle}>
              {isInRange ? 'In Range' : 'Out of Range'}
            </Text>
            <Text style={styles.statusSubtitle}>
              {distance ? `${Math.round(distance)}m from ${attendanceSettings?.locationName}` : 'Calculating...'}
            </Text>
          </LinearGradient>
        </View>

        {/* Today's Attendance Info */}
        {todayAttendance && (
          <View style={styles.infoCard}>
            <Text style={styles.infoTitle}>Today's Attendance</Text>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Check In:</Text>
              <Text style={styles.infoValue}>{todayAttendance.checkIn}</Text>
            </View>
            {todayAttendance.checkOut && (
              <View style={styles.infoRow}>
                <Text style={styles.infoLabel}>Check Out:</Text>
                <Text style={styles.infoValue}>{todayAttendance.checkOut}</Text>
              </View>
            )}
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Status:</Text>
              <View style={[styles.statusBadge, { backgroundColor: getStatusColor(todayAttendance.status) }]}>
                <Text style={styles.statusBadgeText}>
                  {todayAttendance.status.toUpperCase()}
                </Text>
              </View>
            </View>
          </View>
        )}

        {/* Action Buttons */}
        <View style={styles.buttonContainer}>
          {!todayAttendance ? (
            <TouchableOpacity
              onPress={handleCheckIn}
              disabled={!isInRange || loading}
              activeOpacity={0.8}
            >
              <LinearGradient
                colors={isInRange && !loading ? ['#FF0000', '#990000'] : ['#666', '#444']}
                style={styles.actionButton}
              >
                {loading ? (
                  <ActivityIndicator color="white" />
                ) : (
                  <>
                    <Ionicons name="log-in" size={24} color="white" />
                    <Text style={styles.buttonText}>Check In</Text>
                  </>
                )}
              </LinearGradient>
            </TouchableOpacity>
          ) : !todayAttendance.checkOut ? (
            <TouchableOpacity
              onPress={handleCheckOut}
              disabled={loading}
              activeOpacity={0.8}
            >
              <LinearGradient
                colors={['#FF0000', '#990000']}
                style={styles.actionButton}
              >
                {loading ? (
                  <ActivityIndicator color="white" />
                ) : (
                  <>
                    <Ionicons name="log-out" size={24} color="white" />
                    <Text style={styles.buttonText}>Check Out</Text>
                  </>
                )}
              </LinearGradient>
            </TouchableOpacity>
          ) : (
            <View style={styles.completedCard}>
              <Ionicons name="checkmark-done-circle" size={48} color="#10b981" />
              <Text style={styles.completedText}>Attendance Complete</Text>
            </View>
          )}
        </View>

        {!isInRange && (
          <View style={styles.warningCard}>
            <Ionicons name="warning" size={24} color="#f59e0b" />
            <Text style={styles.warningText}>
              You must be within {attendanceSettings?.radius}m of {attendanceSettings?.locationName} to check in
            </Text>
          </View>
        )}
      </View>
    </View>
  );
}

const getStatusColor = (status) => {
  switch (status) {
    case 'hadir': return '#10b981';
    case 'terlambat': return '#f59e0b';
    case 'izin': return '#3b82f6';
    case 'alpha': return '#ef4444';
    default: return '#666';
  }
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: theme.colors.background,
  },
  loadingText: {
    color: theme.colors.text,
    marginTop: 10,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 60,
    paddingBottom: 20,
    backgroundColor: theme.colors.cardBackground,
  },
  backButton: {
    padding: 8,
  },
  headerTitle: {
    color: theme.colors.text,
    fontSize: 20,
    fontWeight: 'bold',
  },
  content: {
    flex: 1,
    padding: 20,
  },
  statusCard: {
    marginBottom: 20,
    borderRadius: 15,
    overflow: 'hidden',
  },
  statusGradient: {
    padding: 30,
    alignItems: 'center',
  },
  statusTitle: {
    color: 'white',
    fontSize: 24,
    fontWeight: 'bold',
    marginTop: 10,
  },
  statusSubtitle: {
    color: 'rgba(255,255,255,0.9)',
    fontSize: 14,
    marginTop: 5,
  },
  infoCard: {
    backgroundColor: theme.colors.cardBackground,
    padding: 20,
    borderRadius: 15,
    marginBottom: 20,
  },
  infoTitle: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 15,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  infoLabel: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  infoValue: {
    color: theme.colors.text,
    fontSize: 16,
    fontWeight: '600',
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusBadgeText: {
    color: 'white',
    fontSize: 12,
    fontWeight: 'bold',
  },
  buttonContainer: {
    marginTop: 20,
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 20,
    borderRadius: 15,
    gap: 10,
  },
  buttonText: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
  },
  completedCard: {
    alignItems: 'center',
    padding: 30,
    backgroundColor: theme.colors.cardBackground,
    borderRadius: 15,
  },
  completedText: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginTop: 10,
  },
  warningCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(245, 158, 11, 0.1)',
    padding: 15,
    borderRadius: 10,
    marginTop: 20,
    gap: 10,
  },
  warningText: {
    color: '#f59e0b',
    fontSize: 14,
    flex: 1,
  },
});
