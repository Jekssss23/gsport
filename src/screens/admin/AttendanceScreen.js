import React, { useState, useRef, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Animated, Dimensions } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../../styles/theme';

const { width } = Dimensions.get('window');

export default function AttendanceScreen() {
  const [isAttended, setIsAttended] = useState(false);
  const [attendanceTime, setAttendanceTime] = useState(null);
  
  // Animation values
  const scaleAnim = useRef(new Animated.Value(1)).current;
  const pulseAnim = useRef(new Animated.Value(1)).current;
  const rotateAnim = useRef(new Animated.Value(0)).current;

  // Pulse animation for the button
  useEffect(() => {
    if (!isAttended) {
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
  }, [isAttended]);

  const handleAttendance = () => {
    if (isAttended) return;

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
  };

  const handleReset = () => {
    setIsAttended(false);
    setAttendanceTime(null);
    rotateAnim.setValue(0);
    scaleAnim.setValue(1);
    pulseAnim.setValue(1);
  };

  const rotation = rotateAnim.interpolate({
    inputRange: [0, 1],
    outputRange: ['0deg', '360deg'],
  });

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Ionicons name="finger-print" size={24} color={theme.colors.primary} />
        <Text style={styles.headerTitle}>Sistem Absensi</Text>
      </View>

      <View style={styles.content}>
        <View style={styles.infoCard}>
          <Text style={styles.infoTitle}>Status Absensi Hari Ini</Text>
          <Text style={styles.infoDate}>{new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</Text>
        </View>

        <View style={styles.buttonContainer}>
          <Text style={styles.instruction}>
            {isAttended ? 'Absensi Berhasil Dicatat!' : 'Tekan Tombol Untuk Absen'}
          </Text>

          <Animated.View 
            style={[
              styles.buttonWrapper,
              { 
                transform: [
                  { scale: isAttended ? scaleAnim : Animated.multiply(scaleAnim, pulseAnim) },
                  { rotate: rotation }
                ]
              }
            ]}
          >
            <TouchableOpacity 
              onPress={handleAttendance}
              disabled={isAttended}
              activeOpacity={0.8}
            >
              <LinearGradient
                colors={isAttended ? ['#00FF00', '#00AA00'] : ['#FF0000', '#CC0000']}
                style={styles.attendanceButton}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 1 }}
              >
                <Ionicons 
                  name={isAttended ? "checkmark-circle" : "finger-print"} 
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
