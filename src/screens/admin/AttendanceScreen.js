import React, { useState, useRef, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Animated, Dimensions, Alert, ActivityIndicator, ScrollView } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import * as Location from 'expo-location';
import * as ImagePicker from 'expo-image-picker';
import { theme } from '../../styles/theme';
import { attendanceAPI, getCurrentUserData } from '../../api/attendance';
import { CloudinaryService } from '../../services/CloudinaryService';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';
import AppModalAlert from '../../components/AppModalAlert';

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
  const [selfieUri, setSelfieUri] = useState(null);
  const [selfieUploading, setSelfieUploading] = useState(false);
  const [modalError, setModalError] = useState({ visible: false, title: 'Info', message: '', type: 'warning' });

  const showModal = (title, message, type = 'warning') => {
    setModalError({ visible: true, title, message, type });
  };

  // Animation values
  const scaleAnim = useRef(new Animated.Value(1)).current;
  const pulseAnim = useRef(new Animated.Value(1)).current;
  const rotateAnim = useRef(new Animated.Value(0)).current;

  // Fetch attendance settings from API
  useEffect(() => {
    const fetchAttendanceSettings = async () => {
      try {
        const response = await attendanceAPI.getSettings();
        
        if (response.success && response.data) {
          setAttendanceSettings(response.data);
          console.log('Attendance settings loaded:', response.data);
        } else {
          console.log('No attendance settings found');
          showModal('Pengaturan Belum Ada', 'Pengaturan lokasi absensi belum diatur di website admin.');
        }
      } catch (error) {
        console.error('Error fetching attendance settings:', error);
        
        if (error.message.includes('Network request failed')) {
          showModal('Koneksi Error', getUserFriendlyErrorMessage(error, 'Tidak dapat terhubung ke server. Cek koneksi internet lalu coba lagi.'));
        } else {
          showModal('Error', getUserFriendlyErrorMessage(error, 'Gagal mengambil pengaturan lokasi.'));
        }
      } finally {
        setLoading(false);
      }
    };

    const checkHistory = async () => {
      try {
        const userData = await getCurrentUserData();
        if (userData) {
          const res = await attendanceAPI.getHistory(userData, 1, 0);
          if (res.success && res.data && res.data.length > 0) {
            const lastRecord = res.data[0];
            const today = new Date().toISOString().split('T')[0];
            if (lastRecord.date === today) {
              setIsAttended(true);
              setAttendanceTime(lastRecord.check_in);
            }
          }
        }
      } catch (e) {
        console.error("Check history err", e);
      }
    };

    fetchAttendanceSettings();
    checkHistory();
  }, []);

  // Check location permissions and get current location
  useEffect(() => {
    const checkLocationPermissions = async () => {
      try {
        let { status } = await Location.requestForegroundPermissionsAsync();

        if (status !== 'granted') {
          setLocationStatus('denied');
          showModal('Izin Lokasi Diperlukan', 'Aplikasi memerlukan izin lokasi untuk absensi. Aktifkan izin lokasi di pengaturan.');
          return;
        }

        setLocationStatus('granted');

        // Get current location with better settings
        const location = await Location.getCurrentPositionAsync({
          accuracy: Location.Accuracy.High,
          maximumAge: 10000, // Accept location up to 10 seconds old
          timeout: 15000, // 15 seconds timeout
        });

        setCurrentLocation({
          latitude: location.coords.latitude,
          longitude: location.coords.longitude,
          isMocked: location.mocked || false,
        });

        if (location.mocked) {
          showModal('Peringatan Keamanan', 'Terdeteksi penggunaan Fake GPS! Status absen akan tercatat sebagai Fake GPS.');
        }

        console.log('Current location:', location.coords);

      } catch (error) {
        console.error('Error getting location:', error);
        setLocationStatus('error');
        
        // More specific error handling
        if (error.message.includes('Location services are disabled')) {
          showModal('GPS Tidak Aktif', 'GPS tidak aktif. Silakan aktifkan GPS di pengaturan perangkat.');
        } else if (error.message.includes('Location request timed out')) {
          showModal('Timeout', 'Gagal mendapatkan lokasi dalam waktu yang ditentukan. Silakan coba lagi.');
        } else {
          showModal('Error', 'Gagal mendapatkan lokasi. Pastikan GPS aktif dan sinyal baik.');
        }
      }
    };

    checkLocationPermissions();
  }, []);

  // Calculate distance and check if within radius using API
  useEffect(() => {
    const checkRadiusWithAPI = async () => {
      if (currentLocation && attendanceSettings) {
        try {
          const response = await attendanceAPI.checkRadius(
            currentLocation.latitude,
            currentLocation.longitude
          );

          if (response.success && response.data) {
            setDistance(response.data.distance);
            setIsWithinRadius(response.data.within_radius);

            console.log('Distance:', response.data.distance, 'meters');
            console.log('Within radius:', response.data.within_radius);
          }
        } catch (error) {
          console.error('Error checking radius:', error);
          // Fallback to local calculation
          const calculatedDistance = calculateDistance(
            currentLocation.latitude,
            currentLocation.longitude,
            attendanceSettings.latitude,
            attendanceSettings.longitude
          );

          setDistance(calculatedDistance);
          setIsWithinRadius(calculatedDistance <= attendanceSettings.radius_meters);
        }
      }
    };

    checkRadiusWithAPI();
  }, [currentLocation, attendanceSettings]);

  // Haversine formula to calculate distance between two points
  const calculateDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
      Math.cos(φ1) * Math.cos(φ2) *
      Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

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
      showModal(
        'Lokasi Tidak Valid',
        `Anda berada di luar radius absensi. Jarak: ${Math.round(distance)}m (Max: ${attendanceSettings.radius_meters}m).`
      );
      return;
    }

    // Check location status
    if (locationStatus !== 'granted') {
      showModal('Lokasi Tidak Tersedia', 'Tidak dapat memverifikasi lokasi. Pastikan GPS aktif dan izin lokasi diberikan.');
      return;
    }

    // Save attendance to API first before rendering animation
    try {
      const userData = await getCurrentUserData();
      
      if (!userData) {
        throw new Error('User data not found');
      }

      if (!selfieUri) {
        showModal('Selfie Wajib', 'Ambil selfie dulu (ikon kamera) sebelum absen.');
        return;
      }

      const locationData = {
        latitude: currentLocation.latitude,
        longitude: currentLocation.longitude,
        is_mocked: currentLocation.isMocked === true,
      };

      // Handle Mock Location Fake GPS
      if (currentLocation.isMocked) {
        locationData.status = 'fake gps';
      }

      // Upload selfie to Cloudinary (preferred)
      setSelfieUploading(true);
      const selfieUrl = await CloudinaryService.uploadImage(selfieUri, {
        folder: 'gsc/attendance-selfies',
        filenamePrefix: 'attendance-selfie',
      });
      locationData.selfie_url = selfieUrl;

      const response = await attendanceAPI.saveAttendance(userData, locationData);

      if (response.success) {
        // Set attended locally
        setIsAttended(true);
        setSelfieUri(null);
        const now = new Date();
        setAttendanceTime(now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }));

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

        console.log('Attendance recorded successfully:', response.data);
      } else {
        // Handle explicit server failures cleanly without throwing an exception
        showModal('Informasi', response.message || 'Gagal memproses absensi.');
        
        // Auto-sync UI if server says they already checked in independently of frontend state
        if (response.message && response.message.toLowerCase().includes('sudah melakukan check-in')) {
          setIsAttended(true);
        }
      }
    } catch (error) {
      console.error('Error saving attendance:', error);
      showModal('Koneksi Gagal', getUserFriendlyErrorMessage(error, 'Gagal memproses absensi. Coba lagi.'));
    } finally {
      setSelfieUploading(false);
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
        accuracy: Location.Accuracy.BestForNavigation,
      });

      setCurrentLocation({
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
        isMocked: location.mocked || false,
      });

      if (location.mocked) {
        showModal('Peringatan Keamanan', 'Terdeteksi penggunaan Fake GPS! Status absen akan tercatat sebagai Fake GPS.');
      }

      showModal('Berhasil', 'Lokasi berhasil diperbarui!', 'success');
    } catch (error) {
      showModal('Error', 'Gagal memperbarui lokasi');
    } finally {
      setLoading(false);
    }
  };

  const handleTakeSelfie = async () => {
    try {
      const { status: cameraStatus } = await ImagePicker.requestCameraPermissionsAsync();
      if (cameraStatus !== 'granted') {
        showModal('Akses Ditolak', 'Akses kamera dibutuhkan untuk mengambil selfie absensi.');
        return;
      }

      const imageResult = await ImagePicker.launchCameraAsync({
        cameraType: ImagePicker.CameraType.front,
        allowsEditing: true,
        aspect: [3, 4],
        quality: 0.5,
      });

      if (imageResult.canceled) return;

      const uri = imageResult.assets?.[0]?.uri;
      if (!uri) {
        showModal('Error', 'Gagal mengambil foto.');
        return;
      }
      setSelfieUri(uri);
    } catch (e) {
      console.error(e);
      showModal('Error', 'Gagal membuka kamera.');
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

      <ScrollView style={{ flex: 1 }} contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
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
                Jarak: {Math.round(distance)}m / {attendanceSettings.radius_meters}m
              </Text>

              <Text style={styles.coordsText}>
                Lokasi: {currentLocation.latitude.toFixed(6)}, {currentLocation.longitude.toFixed(6)}
              </Text>

              <Text style={styles.targetText}>
                Target: {attendanceSettings.location_name}
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
              disabled={isAttended || !isWithinRadius || selfieUploading}
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

          {!isAttended && (
            <View style={styles.selfieRow}>
              <TouchableOpacity
                onPress={handleTakeSelfie}
                disabled={!isWithinRadius || selfieUploading}
                style={[
                  styles.selfieButton,
                  (!isWithinRadius || selfieUploading) && { opacity: 0.6 }
                ]}
              >
                <Ionicons name="camera" size={18} color="white" />
                <Text style={styles.selfieButtonText}>
                  {selfieUri ? 'Selfie siap' : 'Ambil selfie'}
                </Text>
              </TouchableOpacity>
              {selfieUploading && (
                <View style={styles.selfieUploading}>
                  <ActivityIndicator color="white" />
                  <Text style={styles.selfieUploadingText}>Upload...</Text>
                </View>
              )}
            </View>
          )}

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

        {/* isAttended State has no reset button anymore because 1 day = 1 attendance */}
      </ScrollView>
      <AppModalAlert
        visible={modalError.visible}
        title={modalError.title}
        message={modalError.message}
        type={modalError.type}
        onClose={() => setModalError({ visible: false, title: 'Info', message: '', type: 'warning' })}
      />
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
  scrollContent: {
    flexGrow: 1,
    padding: 20,
    paddingBottom: 80,
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
  selfieRow: {
    marginTop: 14,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  selfieButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: 'rgba(255,0,0,0.85)',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.2)',
  },
  selfieButtonText: {
    color: 'white',
    fontSize: 12,
    fontWeight: '700',
  },
  selfieUploading: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingHorizontal: 10,
    paddingVertical: 8,
    backgroundColor: 'rgba(0,0,0,0.35)',
    borderRadius: 10,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.12)',
  },
  selfieUploadingText: {
    color: 'white',
    fontSize: 12,
    fontWeight: '600',
  },
});
