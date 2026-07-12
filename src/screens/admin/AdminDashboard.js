import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Dimensions } from 'react-native';
import { signOut } from 'firebase/auth';
import { doc, getDoc } from 'firebase/firestore';
import { auth, db } from '../../config/firebase.js';
import { theme } from '../../styles/theme';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import AppModalAlert from '../../components/AppModalAlert';
import { API_BASE_URL } from '../../config/api';

const { width } = Dimensions.get('window');

export default function AdminDashboard({ navigation }) {
  const [adminName, setAdminName] = useState('');
  const [logoutPopup, setLogoutPopup] = useState(false);
  const [events, setEvents] = useState([]);

  useEffect(() => {
    const fetchAdminData = async () => {
      if (auth.currentUser) {
        try {
          const userDoc = await getDoc(doc(db, 'users', auth.currentUser.uid));
          if (userDoc.exists()) {
            setAdminName(userDoc.data().name);
          }
        } catch (error) {
          console.error("Error fetching admin data:", error);
        }
      }
    };
    fetchAdminData();
    fetchEvents();
  }, []);

  const fetchEvents = async () => {
    try {
      const res = await fetch(`${API_BASE_URL}/event/list`);
      const json = await res.json();
      if (res.ok && json.ok) setEvents(json.data || []);
    } catch (e) {}
  };

  const handleLogout = () => {
    setLogoutPopup(true);
  };

  const AdminCard = ({ title, icon, route, gradientColors, description }) => (
    <TouchableOpacity
      style={styles.cardContainer}
      onPress={() => navigation.navigate(route)}
      activeOpacity={0.9}
    >
      <LinearGradient
        colors={gradientColors}
        style={styles.card}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <View style={styles.iconContainer}>
          <Ionicons name={icon} size={36} color="white" />
        </View>
        <View style={styles.cardContent}>
          <Text style={styles.cardTitle}>{title}</Text>
          <Text style={styles.cardDescription}>{description}</Text>
        </View>
        <Ionicons name="chevron-forward" size={24} color="rgba(255,255,255,0.6)" style={styles.arrowIcon} />
      </LinearGradient>
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <View>
          <Text style={styles.welcomeText}>Panel Admin</Text>
          <Text style={styles.adminName}>{adminName || 'Administrator'}</Text>
        </View>
        <TouchableOpacity onPress={handleLogout} style={styles.logoutButton}>
          <Ionicons name="log-out-outline" size={24} color={theme.colors.primary} />
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        <Text style={styles.sectionTitle}>Manajemen Dashboard</Text>

        <AdminCard
          title="Absensi"
          description="Catat kehadiran member"
          icon="finger-print-outline"
          route="Attendance"
          gradientColors={['#333333', '#000000']}
        />

        <AdminCard
          title="SCAN FEATURE"
          description="Scan QR member kelas atau paket GSC"
          icon="qr-code-outline"
          route="ClassAttendanceScan"
          gradientColors={['#111827', '#000000']}
        />

        <AdminCard
          title="Package Manage"
          description="Kelola paket GSC & potong jam manual"
          icon="briefcase-outline"
          route="GscPackageManage"
          gradientColors={['#1a2a6c', '#b21f1f']}
        />

        <AdminCard
          title="Rating Me"
          description="Lihat penilaian performa Anda"
          icon="star"
          route="RatingMe"
          gradientColors={['#B22222', '#8B0000']}
        />

        <TouchableOpacity onPress={() => navigation.navigate('Events')} style={styles.eventBox} activeOpacity={0.85}>
          <LinearGradient colors={['#2a0000', '#0f0f0f']} style={styles.eventGradient}>
            <Text style={styles.eventLabel}>EVENT</Text>
            <Text style={styles.eventName}>{events[0]?.name || 'Belum ada event aktif'}</Text>
            <Text style={styles.eventDesc}>Tap untuk lihat slideshow event</Text>
          </LinearGradient>
        </TouchableOpacity>
      </ScrollView>
      <AppModalAlert
        visible={logoutPopup}
        title="Keluar"
        message="Apakah kamu yakin ingin keluar?"
        type="warning"
        buttonLabel="Ya, Keluar"
        onClose={() => {
          setLogoutPopup(false);
          signOut(auth).catch(error => console.error('Error signing out: ', error));
        }}
      />
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
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 60,
    paddingBottom: 20,
    backgroundColor: theme.colors.surface,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,0,0,0.2)',
  },
  welcomeText: {
    color: theme.colors.primary,
    fontSize: 14,
    fontWeight: '600',
    letterSpacing: 1,
  },
  adminName: {
    color: theme.colors.text,
    fontSize: 22,
    fontWeight: 'bold',
  },
  logoutButton: {
    padding: 10,
    backgroundColor: 'rgba(255,0,0,0.1)',
    borderRadius: 50,
  },
  content: {
    padding: 20,
    paddingBottom: 40,
  },
  sectionTitle: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 20,
    marginLeft: 5,
  },
  cardContainer: {
    marginBottom: 16,
    borderRadius: 15,
    shadowColor: "#FF0000",
    shadowOffset: {
      width: 0,
      height: 4,
    },
    shadowOpacity: 0.2,
    shadowRadius: 4.65,
    elevation: 8,
  },
  card: {
    padding: 20,
    borderRadius: 15,
    flexDirection: 'row',
    alignItems: 'center',
    minHeight: 100,
  },
  iconContainer: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: 'rgba(255,255,255,0.15)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  cardContent: {
    flex: 1,
  },
  cardTitle: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  cardDescription: {
    color: 'rgba(255,255,255,0.7)',
    fontSize: 13,
  },
  arrowIcon: {
    marginLeft: 10,
  },
  eventBox: {
    marginTop: 2,
    borderRadius: 15,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.25)',
  },
  eventGradient: {
    padding: 16,
  },
  eventLabel: {
    color: theme.colors.primary,
    fontSize: 12,
    fontWeight: '800',
    letterSpacing: 1.5,
    marginBottom: 6,
  },
  eventName: {
    color: 'white',
    fontSize: 18,
    fontWeight: '800',
    marginBottom: 5,
  },
  eventDesc: {
    color: theme.colors.textSecondary,
    fontSize: 12,
  },
  statsCard: {
    marginTop: 20,
    borderRadius: 15,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.2)',
  },
  statsContent: {
    padding: 20,
  },
  statsTitle: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 15,
  },
  statsRow: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  statItem: {
    alignItems: 'center',
  },
  statNumber: {
    color: theme.colors.primary,
    fontSize: 32,
    fontWeight: 'bold',
  },
  statLabel: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    marginTop: 4,
  },
});
