import React, { useState, useRef, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Animated, Dimensions, Alert, ActivityIndicator } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import * as Location from 'expo-location';
import { theme } from '../../styles/theme';
import { doc, getDoc } from 'firebase/firestore';
import { db } from '../../config/firebase';

const { width } = Dimensions.get('window');

export default function AttendanceScreen() {
  const [isAttended, setIsAttended] = useState(false);
  const [attendanceTime, setAttendanceTime] = useState(null);
  const [locationStatus, setLocationStatus] = useState('checking'); // checking, granted, denied, error
  const [currentLocation, setCurrentLocation] = useState(null);
  const [attendanceSettings, setAttendanceSettings] = useState(null);
  const [distance, setDistance] = useState(null);
  const [isWithinRadius, setIsWithinRadius] = useState(false);
  const [loading, setLoading] = useState(true);
  
  // Animation values
  const scaleAnim = useRef(new Animated.Value(1)).current;
  const pulseAnim = useRef(new Animated.Value(1)).current;
  const rotateAnim = useRef(new Animated.Value(0)).current;

  // Fetch attendance settings from Firebase
  useEffect(() => {
    const fetchAttendanceSettings = async () => {
      try {
        const settingsDoc = await getDoc(doc(db, 'settings', 'attendance'));
        if (settingsDoc.exists()) {
          const settings = settingsDoc.data();
          setAttendanceSettings(settings);
          console.log('Attendance settings loaded:', settings);
        } else {
          console.log('No attendance settings found');
          Alert.alert('Error', 'Pengaturan lokasi absensi belum diatur di website admin');
        }
      } catch (error) {
        console.error('Error fetching attendance settings:', error);
        Alert.alert('Error', 'Gagal mengambil pengaturan lokasi');
      } finally {
        setLoading(false);
      }
    };

    fetchAttendanceSettings();
  }, []);

  // Check location permissions and get current location
  useEffect(() => {
    const checkLocationPermissions = async () => {
      try {
        let { status } = await Location.requestForegroundPermissionsAsync();
        
        if (status !== 'granted') {
          setLocationStatus('denied');
          Alert.alert(
            'Izin Lokasi Diperlukan',
            'Aplikasi memerlukan izin lokasi untuk absensi. Silakan aktifkan izin lokasi di pengaturan.',
            [{ text: 'OK' }]
          );
          return;
        }

        setLocationStatus('granted');
        
        // Get current location
        const location = await Location.getCurrentPositionAsync({
          accuracy: Location.Accuracy.High,
        });
        
        setCurrentLocation({
          latitude: location.coords.latitude,
          longitude: location.coords.longitude,
        });
        
        console.log('Current location:', location.coords);
        
      } catch (error) {
        console.error('Error getting location:', error);
        setLocationStatus('error');
        Alert.alert('Error', 'Gagal mendapatkan lokasi. Pastikan GPS aktif.');
      }
    };

    checkLocationPermissions();
  }, []);

  // Calculate distance and check if within radius
  useEffect(() => {
    if (currentLocation && attendanceSettings) {
      const calculatedDistance = calculateDistance(
        currentLocation.latitude,
        currentLocation.longitude,
        attendanceSettings.latitude,
        attendanceSettings.longitude
      );
      
      setDistance(calculatedDistance);
      setIsWithinRadius(calculatedDistance <= attendanceSettings.radius);
      
      console.log('Distance:', calculatedDistance, 'meters');
      console.log('Within radius:', calculatedDistance <= attendanceSettings.radius);
    }
  }, [currentLocation, attendanceSettings]);

  // Haversine formula to calculate distance between two points
  const calculateDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c; // Distance in meters
  };

  // Pulse animation for the button
  useEffect(() => {
    if (!isAttended && isWithinRadius) {
      const pulse = Animated.loop(
        Animated.sequence([
          Animated.timing(pulseAnim, {
            toValue: 1.1,
            duration: 1000,
            useNativeDriver: true,
          }),
          Animated.timing(pulseAnim, {
            toValue: 1,
            duration: 1000,
            useNativeDriver: true,
          }),
        ])
      );
      pulse.start();
      return () => pulse.stop();
    }
  }, [isAttended, isWithinRadius]);

  const handleAttendance = async () => {
    if (isAttended) return;

    // Check if within radius
    if (!isWithinRadius) {
      Alert.alert(
        'Lokasi Tidak Valid',
        `Anda berada di luar radius absensi. Jarak Anda: ${Math.round(distance)}m (Max: ${attendanceSettings.radius}m). \n\nSilakan mendekat ke lokasi yang telah ditentukan.`,
        [{ text: 'OK' }]
      );
      return;
    }

    // Check location status
    if (locationStatus !== 'granted') {
      Alert.alert(
        'Lokasi Tidak Tersedia',
        'Tidak dapat memverifikasi lokasi Anda. Pastikan GPS aktif dan izin lokasi diberikan.',
        [{ text: 'OK' }]
      );
      return;
    }

    // Scale down animation
    Animated.sequence([
      Animated.timing(scaleAnim, {
        toValue: 0.9,
        duration: 100,
        useNativeDriver: true,
      }),
      Animated.parallel([
        Animated.spring(scaleAnim, {
          toValue: 1,
          friction: 3,
          tension: 40,
          useNativeDriver: true,
        }),
        Animated.timing(rotateAnim, {
          toValue: 1,
          duration: 500,
          useNativeDriver: true,
        }),
      ]),
    ]).start();

    // Set attended
    setIsAttended(true);
    const now = new Date();
    setAttendanceTime(now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }));

    // Here you would save attendance to Firebase
    try {
      // TODO: Save attendance data to Firebase
      console.log('Attendance recorded successfully');
    } catch (error) {
      console.error('Error saving attendance:', error);
    }
  };

  const handleReset = () => {
    setIsAttended(false);
    setAttendanceTime(null);
    rotateAnim.setValue(0);
    scaleAnim.setValue(1);
    pulseAnim.setValue(1);
  };

  const refreshLocation = async () => {
    setLoading(true);
    try {
      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.High,
      });
      
      setCurrentLocation({
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
      });
      
      Alert.alert('Success', 'Lokasi berhasil diperbarui!');
    } catch (error) {
      Alert.alert('Error', 'Gagal memperbarui lokasi');
    } finally {
      setLoading(false);
    }
  };

  const rotation = rotateAnim.interpolate({
    inputRange: [0, 1],
    outputRange: ['0deg', '360deg'],
  });

  if (loading) {
    return (
      <View style={styles.container}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={theme.colors.primary} />
          <Text style={styles.loadingText}>Memuat pengaturan lokasi...</Text>
        </View>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Ionicons name="finger-print" size={24} color={theme.colors.primary} />
        <Text style={styles.headerTitle}>Sistem Absensi GPS</Text>
      </View>

      <View style={styles.content}>
        {/* Location Status Card */}
        <View style={styles.locationCard}>
          <Text style={styles.locationTitle}>Status Lokasi</Text>
          
          {locationStatus === 'granted' && currentLocation && attendanceSettings ? (
            <View style={styles.locationInfo}>
              <View style={styles.locationRow}>
                <Ionicons name="location" size={20} color={isWithinRadius ? "#00FF00" : "#FF0000"} />
                <Text style={[styles.locationText, { color: isWithinRadius ? "#00FF00" : "#FF0000" }]}>
                  {isWithinRadius ? "ANDA DALAM RADIUS" : "ANDA DI LUAR RADIUS"}
                </Text>
              </View>
              
              <Text style={styles.distanceText}>
                Jarak: {Math.round(distance)}m / {attendanceSettings.radius}m
              </Text>
              
              <Text style={styles.coordsText}>
                Lokasi: {currentLocation.latitude.toFixed(6)}, {currentLocation.longitude.toFixed(6)}
              </Text>
              
              <Text style={styles.targetText}>
                Target: {attendanceSettings.locationName}
              </Text>
            </View>
          ) : (
            <View style={styles.locationInfo}>
              <Ionicons name="warning" size={20} color="#FFA500" />
              <Text style={styles.warningText}>
                {locationStatus === 'denied' ? 'Izin lokasi ditolak' : 
                 locationStatus === 'error' ? 'Gagal mendapatkan lokasi' : 
                 'Memeriksa lokasi...'}
              </Text>
            </View>
          )}
          
          <TouchableOpacity style={styles.refreshButton} onPress={refreshLocation}>
            <Ionicons name="refresh" size={16} color={theme.colors.primary} />
            <Text style={styles.refreshText}>Refresh Lokasi</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.infoCard}>
          <Text style={styles.infoTitle}>Status Absensi Hari Ini</Text>
          <Text style={styles.infoDate}>{new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</Text>
        </View>

        <View style={styles.buttonContainer}>
          <Text style={styles.instruction}>
            {isAttended ? 'Absensi Berhasil Dicatat!' : 
             !isWithinRadius ? 'Dekat ke lokasi absensi terlebih dahulu!' :
             'Tekan Tombol Untuk Absen'}
          </Text>

          <Animated.View 
            style={[
              styles.buttonWrapper,
              { 
                transform: [
                  { scale: isAttended ? scaleAnim : (isWithinRadius ? Animated.multiply(scaleAnim, pulseAnim) : scaleAnim) },
                  { rotate: rotation }
                ]
              }
            ]}
          >
            <TouchableOpacity 
              onPress={handleAttendance}
              disabled={isAttended || !isWithinRadius}
              activeOpacity={0.8}
            >
              <LinearGradient
                colors={
                  isAttended ? ['#00FF00', '#00AA00'] : 
                  !isWithinRadius ? ['#666666', '#444444'] :
                  ['#FF0000', '#CC0000']
                }
                style={styles.attendanceButton}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 1 }}
              >
                <Ionicons 
                  name={
                    isAttended ? "checkmark-circle" : 
                    !isWithinRadius ? "close-circle" :
                    "finger-print"
                  } 
                  size={80} 
                  color="white" 
                />
              </LinearGradient>
            </TouchableOpacity>
          </Animated.View>

          {isAttended && (
            <Animated.View style={[styles.successInfo, { opacity: scaleAnim }]}>
              <View style={styles.successCard}>
                <Ionicons name="checkmark-circle" size={40} color="#00FF00" />
                <Text style={styles.successText}>Absensi Tercatat</Text>
                <Text style={styles.timeText}>Waktu: {attendanceTime}</Text>
                <Text style={styles.locationVerifiedText}>📍 Lokasi Terverifikasi</Text>
              </View>
            </Animated.View>
          )}
        </View>

        <View style={styles.statsContainer}>
          <View style={styles.statBox}>
            <LinearGradient
              colors={['rgba(255,0,0,0.2)', 'rgba(0,0,0,0.3)']}
              style={styles.statGradient}
            >
              <Ionicons name="people" size={32} color={theme.colors.primary} />
              <Text style={styles.statNumber}>45</Text>
              <Text style={styles.statLabel}>Total Hari Ini</Text>
            </LinearGradient>
          </View>
          <View style={styles.statBox}>
            <LinearGradient
              colors={['rgba(0,255,0,0.2)', 'rgba(0,0,0,0.3)']}
              style={styles.statGradient}
            >
              <Ionicons name="checkmark-circle" size={32} color="#00FF00" />
              <Text style={styles.statNumber}>156</Text>
              <Text style={styles.statLabel}>Total Minggu Ini</Text>
            </LinearGradient>
          </View>
        </View>

        {isAttended && (
          <TouchableOpacity style={styles.resetButton} onPress={handleReset}>
            <Text style={styles.resetButtonText}>Reset Absensi</Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    color: theme.colors.text,
    fontSize: 16,
    marginTop: 10,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 15,
    backgroundColor: theme.colors.cardBackground,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,0,0,0.2)',
  },
  headerTitle: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginLeft: 10,
  },
  content: {
    flex: 1,
    padding: 20,
  },
  locationCard: {
    backgroundColor: theme.colors.cardBackground,
    padding: 20,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.2)',
    marginBottom: 20,
  },
  locationTitle: {
    color: theme.colors.text,
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  locationInfo: {
    marginBottom: 15,
  },
  locationRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  locationText: {
    fontSize: 14,
    fontWeight: 'bold',
    marginLeft: 8,
  },
  distanceText: {
    color: theme.colors.textSecondary,
    fontSize: 13,
    marginBottom: 4,
  },
  coordsText: {
    color: theme.colors.textSecondary,
    fontSize: 11,
    marginBottom: 4,
  },
  targetText: {
    color: theme.colors.textSecondary,
    fontSize: 12,
  },
  warningText: {
    color: '#FFA500',
    fontSize: 14,
    marginLeft: 8,
  },
  refreshButton: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    paddingVertical: 8,
    paddingHorizontal: 12,
    backgroundColor: 'rgba(255,0,0,0.1)',
    borderRadius: 6,
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.3)',
  },
  refreshText: {
    color: theme.colors.primary,
    fontSize: 12,
    marginLeft: 4,
  },
  infoCard: {
    backgroundColor: theme.colors.cardBackground,
    padding: 20,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.2)',
    marginBottom: 30,
  },
  infoTitle: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 8,
  },
  infoDate: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  buttonContainer: {
    alignItems: 'center',
    marginVertical: 40,
  },
  instruction: {
    color: theme.colors.text,
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 30,
    textAlign: 'center',
  },
  buttonWrapper: {
    shadowColor: "#FF0000",
    shadowOffset: {
      width: 0,
      height: 10,
    },
    shadowOpacity: 0.5,
    shadowRadius: 20,
    elevation: 15,
  },
  attendanceButton: {
    width: 200,
    height: 200,
    borderRadius: 100,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 5,
    borderColor: 'rgba(255,255,255,0.3)',
  },
  successInfo: {
    marginTop: 30,
  },
  successCard: {
    backgroundColor: 'rgba(0,255,0,0.1)',
    padding: 20,
    borderRadius: 12,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: 'rgba(0,255,0,0.3)',
  },
  successText: {
    color: '#00FF00',
    fontSize: 20,
    fontWeight: 'bold',
    marginTop: 10,
  },
  timeText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    marginTop: 5,
  },
  locationVerifiedText: {
    color: '#00FF00',
    fontSize: 12,
    marginTop: 8,
    fontWeight: '600',
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 12,
    marginTop: 20,
  },
  statBox: {
    flex: 1,
    borderRadius: 12,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
  },
  statGradient: {
    padding: 20,
    alignItems: 'center',
  },
  statNumber: {
    color: theme.colors.text,
    fontSize: 28,
    fontWeight: 'bold',
    marginTop: 8,
  },
  statLabel: {
    color: theme.colors.textSecondary,
    fontSize: 11,
    marginTop: 4,
    textAlign: 'center',
  },
  resetButton: {
    backgroundColor: 'rgba(255,0,0,0.1)',
    borderWidth: 1,
    borderColor: theme.colors.primary,
    paddingVertical: 12,
    borderRadius: 8,
    marginTop: 20,
  },
  resetButtonText: {
    color: theme.colors.primary,
    fontSize: 14,
    fontWeight: 'bold',
    textAlign: 'center',
  },
});
