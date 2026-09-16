import React, { useEffect, useState, useRef } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Dimensions, RefreshControl, Image } from 'react-native';
import { signOut } from 'firebase/auth';
import { doc, getDoc } from 'firebase/firestore';
import { auth, db } from '../../config/firebase.js';
import { API_BASE_URL } from '../../config/api';
import { theme } from '../../styles/theme';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { StatusBar } from 'expo-status-bar';
import { APP_LOGO_PRIMARY } from '../../constants/assets';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';
import AppModalAlert from '../../components/AppModalAlert';

const { width } = Dimensions.get('window');

export default function UserDashboard({ navigation }) {
  const [userName, setUserName] = useState('');
  const [unratedCount, setUnratedCount] = useState(0);
  const [refreshing, setRefreshing] = useState(false);
  const [events, setEvents] = useState([]);
  const [lastEventNotifId, setLastEventNotifId] = useState(null);
  const [modalState, setModalState] = useState({ visible: false, title: 'Info', message: '', type: 'warning', onCloseAction: null });
  const eventScrollRef = useRef(null);
  const eventIndexRef = useRef(0);

  const fetchUserData = async () => {
    if (auth.currentUser) {
      try {
        const userDoc = await getDoc(doc(db, 'users', auth.currentUser.uid));
        if (userDoc.exists()) {
          setUserName(userDoc.data().name);
        }
      } catch (error) {
        console.error("Error fetching user data:", error);
        setModalState({ visible: true, title: 'Error', message: getUserFriendlyErrorMessage(error, 'Gagal memuat data user.'), type: 'warning', onCloseAction: null });
      }
    }
  };

  const fetchUnratedReservations = async () => {
    if (!auth.currentUser) return;

    try {
      const userId = auth.currentUser.uid || auth.currentUser.email;
      const res = await fetch(`${API_BASE_URL}/reservation/my?firebase_uid=${userId}`);
      const json = await res.json();
      
      if (json.ok && json.data) {
        const unrated = json.data.filter(booking => 
          (booking.status === 'completed' || booking.status === 'selesai') && 
          (booking.has_rated === 0 || !booking.has_rated)
        );
        setUnratedCount(unrated.length);
      }
    } catch (error) {
      console.error("Error fetching unrated reservations:", error);
    }
  };

  const getCurrentUserPayload = () =>
    auth.currentUser
      ? {
          uid: auth.currentUser.uid,
          email: auth.currentUser.email,
          displayName: auth.currentUser.displayName,
          phoneNumber: auth.currentUser.phoneNumber,
        }
      : null;

  useEffect(() => {
    fetchUserData();
    fetchUnratedReservations();
    fetchEvents();

    const interval = setInterval(fetchUnratedReservations, 30000);
    const notifInterval = setInterval(checkEventNotifications, 15000);
    checkEventNotifications();
    return () => {
      clearInterval(interval);
      clearInterval(notifInterval);
    };
  }, []);

  useEffect(() => {
    if (!events.length || !eventScrollRef.current) return;
    const timer = setInterval(() => {
      if (!eventScrollRef.current || events.length === 0) return;
      eventIndexRef.current = (eventIndexRef.current + 1) % events.length;
      eventScrollRef.current.scrollTo({ x: eventIndexRef.current * (width - 50 + 10), animated: true });
    }, 3000);
    return () => clearInterval(timer);
  }, [events]);

  const fetchEvents = async () => {
    try {
      const res = await fetch(`${API_BASE_URL}/event/list`);
      const json = await res.json();
      if (res.ok && json.ok) setEvents(json.data || []);
    } catch (e) {}
  };

  const checkEventNotifications = async () => {
    if (!auth.currentUser) return;
    try {
      const res = await fetch(`${API_BASE_URL}/notification/my?user_id=${auth.currentUser.uid}`);
      const json = await res.json();
      if (!res.ok || !json.ok) return;
      const latestEvent = (json.data || []).find((n) => n.type === 'event');
      if (!latestEvent) return;
      if (!lastEventNotifId) {
        setLastEventNotifId(latestEvent.id);
        return;
      }
      if (latestEvent.id !== lastEventNotifId) {
        setLastEventNotifId(latestEvent.id);
        setModalState({
          visible: true,
          title: latestEvent.title || 'Event Baru',
          message: latestEvent.message || 'Ada event baru dari G Sports Center.',
          type: 'success',
          onCloseAction: () => navigation.navigate('Notifications'),
        });
      }
    } catch (e) {}
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await fetchUserData();
    setRefreshing(false);
  };

  const handleLogout = () => {
    setModalState({
      visible: true,
      title: 'Logout',
      message: 'Are you sure you want to logout?',
      type: 'warning',
      onCloseAction: () => signOut(auth).catch(error => console.error('Error signing out: ', error)),
    });
  };

  const ActionCard = ({ title, subtitle, icon, route, colors, isWide = false }) => (
    <TouchableOpacity 
      style={[styles.cardContainer, isWide && styles.cardWide]}
      onPress={() => {
        if (route === 'FieldReservation') {
          navigation.navigate(route, { 
            user: getCurrentUserPayload()
          });
        } else {
          navigation.navigate(route);
        }
      }}
      activeOpacity={0.8}
    >
      <LinearGradient
        colors={colors}
        style={styles.cardGradient}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <View style={styles.cardHeader}>
          <View style={styles.cardIconContainer}>
            <Ionicons name={icon} size={24} color="white" />
          </View>
          <Ionicons name="chevron-forward" size={20} color="rgba(255,255,255,0.5)" />
        </View>
        <View style={styles.cardContent}>
          <Text style={styles.cardTitle}>{title}</Text>
          <Text style={styles.cardSubtitle}>{subtitle}</Text>
        </View>
      </LinearGradient>
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <LinearGradient
        colors={[theme.colors.background, '#000000']}
        style={StyleSheet.absoluteFill}
      />
      
      {/* Premium Header */}
      <View style={styles.header}>
        <View style={styles.headerTop}>
          <View style={styles.profileSection}>
            <View style={styles.avatarContainer}>
              <LinearGradient
                colors={theme.gradients.primary}
                style={styles.avatarGradient}
              >
                <Text style={styles.avatarText}>{userName ? userName[0].toUpperCase() : 'U'}</Text>
              </LinearGradient>
            </View>
            <View>
              <Text style={styles.welcomeText}>Welcome back,</Text>
              <Text style={styles.userName}>{userName || 'User'}</Text>
            </View>
          </View>
          <View style={styles.headerActions}>
            <TouchableOpacity 
              onPress={() => navigation.navigate('Profile')}
              style={styles.iconButton}
            >
              <Ionicons name="person-circle-outline" size={24} color={theme.colors.text} />
            </TouchableOpacity>
            <TouchableOpacity 
              onPress={() => navigation.navigate('Notifications')} 
              style={styles.iconButton}
            >
              <Ionicons name="notifications-outline" size={24} color={theme.colors.text} />
              {unratedCount > 0 && (
                <View style={styles.badge}>
                  <Text style={styles.badgeText}>{unratedCount}</Text>
                </View>
              )}
            </TouchableOpacity>
            <TouchableOpacity onPress={handleLogout} style={[styles.iconButton, styles.logoutBtn]}>
              <Ionicons name="log-out-outline" size={24} color={theme.colors.primary} />
            </TouchableOpacity>
          </View>
        </View>
      </View>

      <ScrollView 
        contentContainerStyle={styles.scrollContent} 
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={theme.colors.primary} />
        }
      >
        {/* Hero Section */}
        <View style={styles.heroSection}>
          <LinearGradient
            colors={theme.gradients.premium}
            style={styles.heroGradient}
          >
            <View style={styles.heroInfo}>
              <Image source={APP_LOGO_PRIMARY} style={styles.heroLogo} resizeMode="contain" />
              <Text style={styles.heroTitle}>Book Your Perfect Court</Text>
              <Text style={styles.heroDesc}>High-end facilities for your sports lifestyle</Text>
              <TouchableOpacity 
                style={styles.heroButton}
                onPress={() => navigation.navigate('FieldReservation', { user: getCurrentUserPayload() })}
              >
                <Text style={styles.heroButtonText}>Book Now</Text>
              </TouchableOpacity>
            </View>
            <Ionicons name="tennisball" size={100} color="rgba(255,255,255,0.05)" style={styles.heroBgIcon} />
          </LinearGradient>
        </View>

        <View style={styles.eventSection}>
          <Text style={styles.eventHeadText}>EVENT</Text>
          <ScrollView ref={eventScrollRef} horizontal pagingEnabled showsHorizontalScrollIndicator={false}>
            {(events.length ? events : [{ id: 'empty' }]).map((ev) => (
              <TouchableOpacity
                key={ev.id}
                activeOpacity={0.85}
                style={styles.eventBanner}
                onPress={() => navigation.navigate('Events')}
              >
                {ev.image_url ? (
                  <Image source={{ uri: ev.image_url }} style={styles.eventImage} />
                ) : (
                  <LinearGradient colors={['#2a0000', '#0f0f0f']} style={styles.eventGradient}>
                    <View style={{ flex: 1 }}>
                      <Text style={styles.eventTitle}>EVENT G SPORTS CENTER</Text>
                      <Text style={styles.eventSub}>Belum ada event aktif saat ini.</Text>
                    </View>
                  </LinearGradient>
                )}
                {ev.image_url ? (
                  <LinearGradient colors={['transparent', 'rgba(0,0,0,0.75)']} style={styles.eventOverlay}>
                    <Text style={styles.eventTitle}>{ev.name}</Text>
                    <Text style={styles.eventSub}>{ev.start_date} - {ev.end_date}</Text>
                  </LinearGradient>
                ) : null}
              </TouchableOpacity>
            ))}
          </ScrollView>
        </View>

        <Text style={styles.sectionHeader}>Main Services</Text>
        
        <View style={styles.grid}>
          <ActionCard 
            title="Field Booking" 
            subtitle="Reserve your court"
            icon="calendar" 
            route="FieldReservation"
            colors={theme.gradients.primary}
            isWide={true}
          />
          
          <ActionCard 
            title="My History" 
            subtitle="Check your bookings"
            icon="time" 
            route="MyReservationHistory"
            colors={['#2A2A2A', '#1A1A1A']}
          />

          <ActionCard 
            title="Schedules" 
            subtitle="Class & sessions"
            icon="list" 
            route="ClassSchedule"
            colors={['#333333', '#000000']}
          />

          <ActionCard 
            title="GSC Packages" 
            subtitle="Exclusive deals"
            icon="cube" 
            route="GscPackage"
            colors={['#004D40', '#00251A']}
            isWide={true}
          />
        </View>

      </ScrollView>
      <AppModalAlert
        visible={modalState.visible}
        title={modalState.title}
        message={modalState.message}
        type={modalState.type}
        onClose={() => {
          const action = modalState.onCloseAction;
          setModalState({ visible: false, title: 'Info', message: '', type: 'warning', onCloseAction: null });
          if (typeof action === 'function') action();
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
    paddingTop: 60,
    paddingBottom: 20,
    paddingHorizontal: 25,
    backgroundColor: 'rgba(15, 15, 15, 0.8)',
  },
  headerTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  profileSection: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
    marginRight: 8,
  },
  avatarContainer: {
    width: 48,
    height: 48,
    borderRadius: 24,
    marginRight: 15,
    ...theme.shadows.medium,
  },
  avatarGradient: {
    flex: 1,
    borderRadius: 24,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: 'rgba(255,255,255,0.1)',
  },
  avatarText: {
    color: 'white',
    fontSize: 20,
    fontWeight: 'bold',
  },
  welcomeText: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    fontWeight: '500',
  },
  userName: {
    color: theme.colors.text,
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.5,
    maxWidth: width * 0.34,
  },
  headerActions: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  iconButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    marginLeft: 8,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  logoutBtn: {
    backgroundColor: 'rgba(230, 0, 0, 0.05)',
  },
  badge: {
    position: 'absolute',
    top: 8,
    right: 8,
    backgroundColor: theme.colors.primary,
    borderRadius: 10,
    minWidth: 16,
    height: 16,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1.5,
    borderColor: theme.colors.surface,
  },
  badgeText: {
    color: 'white',
    fontSize: 8,
    fontWeight: 'bold',
  },
  scrollContent: {
    padding: 25,
    paddingTop: 10,
  },
  heroSection: {
    marginBottom: 30,
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    ...theme.shadows.heavy,
  },
  heroGradient: {
    padding: 25,
    flexDirection: 'row',
    alignItems: 'center',
    minHeight: 180,
  },
  heroInfo: {
    flex: 1,
    zIndex: 1,
  },
  heroTag: {
    color: theme.colors.primary,
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 2,
    marginBottom: 8,
  },
  heroLogo: {
    width: 120,
    height: 36,
    marginBottom: 8,
  },
  heroTitle: {
    color: 'white',
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 8,
    lineHeight: 30,
  },
  heroDesc: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    marginBottom: 20,
    lineHeight: 20,
  },
  heroButton: {
    backgroundColor: 'white',
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: theme.borderRadius.medium,
    alignSelf: 'flex-start',
  },
  heroButtonText: {
    color: 'black',
    fontSize: 14,
    fontWeight: 'bold',
  },
  heroBgIcon: {
    position: 'absolute',
    right: -20,
    bottom: -20,
  },
  sectionHeader: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: '800',
    marginBottom: 20,
    letterSpacing: 1,
    textTransform: 'uppercase',
  },
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  cardContainer: {
    width: (width - 65) / 2,
    height: 150,
    marginBottom: 15,
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    ...theme.shadows.medium,
  },
  cardWide: {
    width: '100%',
    height: 120,
  },
  cardGradient: {
    flex: 1,
    padding: 18,
    justifyContent: 'space-between',
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  cardIconContainer: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: 'rgba(255,255,255,0.15)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  cardContent: {
    marginTop: 10,
  },
  cardTitle: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  cardSubtitle: {
    color: 'rgba(255,255,255,0.6)',
    fontSize: 11,
  },
  infoCard: {
    flexDirection: 'row',
    backgroundColor: theme.colors.surface,
    padding: 20,
    borderRadius: theme.borderRadius.large,
    marginTop: 20,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    alignItems: 'center',
  },
  eventSection: {
    marginBottom: 18,
  },
  eventHeadText: {
    color: 'white',
    fontSize: 15,
    fontWeight: '800',
    marginBottom: 8,
    letterSpacing: 1,
  },
  eventBanner: {
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    marginBottom: 2,
    borderWidth: 1,
    borderColor: 'rgba(230,0,0,0.25)',
    width: width - 50,
    height: 165,
    marginRight: 10,
  },
  eventImage: {
    width: '100%',
    height: '100%',
  },
  eventGradient: {
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    width: '100%',
    height: '100%',
  },
  eventOverlay: {
    position: 'absolute',
    left: 0,
    right: 0,
    bottom: 0,
    padding: 12,
  },
  eventTitle: {
    color: 'white',
    fontSize: 14,
    fontWeight: '800',
    marginBottom: 4,
    letterSpacing: 1,
  },
  eventSub: {
    color: theme.colors.textSecondary,
    fontSize: 12,
  },
  infoIconContainer: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(230, 0, 0, 0.1)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  infoTextContainer: {
    flex: 1,
  },
  infoTitle: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  infoDesc: {
    color: theme.colors.textSecondary,
    fontSize: 13,
    lineHeight: 18,
  },
});
