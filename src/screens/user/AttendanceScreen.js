import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Alert, ActivityIndicator, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import * as Location from 'expo-location';
import * as ImagePicker from 'expo-image-picker';
import { auth, db } from '../../config/firebase';
import { collection, addDoc, doc, getDoc, query, where, getDocs, updateDoc } from 'firebase/firestore';
import { theme } from '../../styles/theme';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';

export default function AttendanceScreen({ navigation }) {
  const [loading, setLoading] = useState(false);
  const [locationLoading, setLocationLoading] = useState(true);
  const [currentLocation, setCurrentLocation] = useState(null);
  const [attendanceSettings, setAttendanceSettings] = useState(null);
  const [todayAttendance, setTodayAttendance] = useState(null);
  const [userName, setUserName] = useState('');
  const [selfieBase64, setSelfieBase64] = useState(null);

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
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal memuat data user.'));
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
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal memuat pengaturan absensi.'));
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
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal mengecek absensi hari ini.'));
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

      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.BestForNavigation,
      });

      if (location.mocked) {
        Alert.alert(
          'Peringatan Keamanan',
          'Terdeteksi penggunaan Fake GPS! Status absen Anda akan tercatat sebagai Fake GPS.'
        );
      }

      setCurrentLocation({
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
        isMocked: location.mocked || false,
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
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  };

  const handleTakeSelfie = async () => {
    const { status } = await ImagePicker.requestCameraPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permission Denied', 'Camera permission is required');
      return;
    }

    const result = await ImagePicker.launchCameraAsync({
      allowsEditing: true,
      aspect: [4, 3],
      quality: 0.5,
      base64: true,
    });

    if (!result.canceled) {
      setSelfieBase64(result.assets[0].base64);
    }
  };

  const handleCheckIn = async () => {
    if (!selfieBase64) {
      Alert.alert('Error', 'Please take a selfie first');
      return;
    }

    setLoading(true);
    try {
      const now = new Date();
      const date = now.toISOString().split('T')[0];
      const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

      // Determine status
      let status = 'hadir';
      const checkInTime = now.getHours() * 60 + now.getMinutes();
      const [limitH, limitM] = attendanceSettings.checkInLimit.split(':').map(Number);
      const limitTime = limitH * 60 + limitM;

      if (currentLocation.isMocked) {
        status = 'fake gps';
      } else if (checkInTime > limitTime) {
        status = 'terlambat';
      }

      const attendanceData = {
        employeeId: auth.currentUser.uid,
        employeeName: userName,
        date,
        checkIn: time,
        status,
        checkInLocation: currentLocation,
        selfie: selfieBase64,
        createdAt: new Date().toISOString(),
      };

      await addDoc(collection(db, 'attendances'), attendanceData);
      setTodayAttendance(attendanceData);
      Alert.alert('Success', 'Check in successful');
    } catch (error) {
      console.error('Error check in:', error);
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal check-in.'));
    } finally {
      setLoading(false);
    }
  };

  const handleCheckOut = async () => {
    setLoading(true);
    try {
      const now = new Date();
      const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      const today = now.toISOString().split('T')[0];

      const q = query(
        collection(db, 'attendances'),
        where('employeeId', '==', auth.currentUser.uid),
        where('date', '==', today)
      );
      const querySnapshot = await getDocs(q);

      if (!querySnapshot.empty) {
        const docId = querySnapshot.docs[0].id;
        await updateDoc(doc(db, 'attendances', docId), {
          checkOut: time,
          checkOutLocation: currentLocation,
        });
        setTodayAttendance({ ...todayAttendance, checkOut: time });
        Alert.alert('Success', 'Check out successful');
      }
    } catch (error) {
      console.error('Error check out:', error);
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal check-out.'));
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
          <Ionicons name="arrow-back-outline" size={24} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Absensi Kehadiran</Text>
        <View style={{ width: 40 }} />
      </View>

      <ScrollView style={{ flex: 1 }} contentContainerStyle={styles.contentContainer}>
        <View style={styles.statusCard}>
          <LinearGradient
            colors={isInRange ? ['#10b981', '#059669'] : ['#ef4444', '#dc2626']}
            style={styles.statusGradient}
          >
            <Ionicons
              name={isInRange ? 'checkmark-circle-outline' : 'close-circle-outline'}
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

        {!isInRange && (
          <View style={styles.warningCard}>
            <Ionicons name="warning-outline" size={24} color="#f59e0b" />
            <Text style={styles.warningText}>
              You must be within {attendanceSettings?.radius}m of {attendanceSettings?.locationName} to check in
            </Text>
          </View>
        )}
      </ScrollView>

      {/* Fixed Bottom Action Area */}
      <View style={styles.fixedBottom}>
        {!todayAttendance ? (
          // BEFORE CHECK IN (Show Selfie & Check In Button side-by-side)
          <View style={{ flexDirection: 'row', alignItems: 'stretch' }}>
            <TouchableOpacity
              onPress={handleTakeSelfie}
              disabled={!isInRange || loading}
              activeOpacity={0.85}
              style={[
                styles.selfieAction,
                (!isInRange || loading) && { opacity: 0.6 },
                selfieBase64 && { backgroundColor: 'rgba(16, 185, 129, 0.1)', borderColor: '#10b981', borderWidth: 1 }
              ]}
            >
              <Ionicons
                name={selfieBase64 ? "checkmark-circle" : "camera"}
                size={24}
                color={selfieBase64 ? "#10b981" : "white"}
              />
              <Text style={[styles.selfieActionText, selfieBase64 && { color: '#10b981' }]}>
                {selfieBase64 ? 'Siap' : 'Selfie'}
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              onPress={handleCheckIn}
              disabled={!isInRange || loading}
              activeOpacity={0.8}
              style={{ flex: 1, marginLeft: 10 }}
            >
              <LinearGradient
                colors={isInRange && !loading ? ['#FF0000', '#990000'] : ['#666', '#444']}
                style={styles.actionButton}
              >
                {loading ? (
                  <ActivityIndicator color="white" />
                ) : (
                  <>
                    <Ionicons name="log-in-outline" size={24} color="white" />
                    <Text style={styles.buttonText}>Check In</Text>
                  </>
                )}
              </LinearGradient>
            </TouchableOpacity>
          </View>
        ) : !todayAttendance.checkOut ? (
          // BEFORE CHECK OUT
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
                  <Ionicons name="log-out-outline" size={24} color="white" />
                  <Text style={styles.buttonText}>Check Out</Text>
                </>
              )}
            </LinearGradient>
          </TouchableOpacity>
        ) : (
          // COMPLETE
          <View style={styles.completedCard}>
            <Ionicons name="checkmark-done-circle-outline" size={48} color="#10b981" />
            <Text style={styles.completedText}>Attendance Complete</Text>
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
    case 'fake gps': return '#ff0000'; // Pure red for fake gps
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
  contentContainer: {
    flexGrow: 1,
    padding: 20,
    paddingBottom: 40,
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
    marginBottom: 0,
  },
  actionButton: {
    height: 60,
    borderRadius: 15,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  buttonText: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
    marginLeft: 10,
  },
  completedCard: {
    backgroundColor: 'rgba(16, 185, 129, 0.1)',
    padding: 20,
    borderRadius: 15,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#10b981',
  },
  completedText: {
    color: '#10b981',
    fontSize: 18,
    fontWeight: 'bold',
    marginTop: 10,
  },
  selfieAction: {
    flex: 0.8,
    backgroundColor: '#444',
    height: 60,
    borderRadius: 15,
    justifyContent: 'center',
    alignItems: 'center',
  },
  selfieActionText: {
    color: 'white',
    fontSize: 14,
    fontWeight: 'bold',
    marginTop: 4,
  },
  warningCard: {
    backgroundColor: 'rgba(245, 158, 11, 0.1)',
    padding: 15,
    borderRadius: 12,
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#f59e0b',
  },
  warningText: {
    color: '#f59e0b',
    fontSize: 12,
    marginLeft: 10,
    flex: 1,
  },
  fixedBottom: {
    padding: 20,
    paddingBottom: 30,
    backgroundColor: theme.colors.background,
    borderTopWidth: 1,
    borderTopColor: 'rgba(0,0,0,0.05)',
  },
});
