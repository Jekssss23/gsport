import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  Image
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { auth } from '../../config/firebase';
import { API_BASE_URL } from '../../config/api';
import { theme } from '../../styles/theme';
import { LinearGradient } from 'expo-linear-gradient';
import RatingScreen from './RatingScreen';
import { BookingService } from '../../services/BookingService';

export default function NotificationScreen({ navigation }) {
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedBooking, setSelectedBooking] = useState(null);
  const [showRatingModal, setShowRatingModal] = useState(false);

  const fetchNotifications = async () => {
    if (!auth.currentUser) return;

    try {
      const userId = auth.currentUser.uid || auth.currentUser.email;
      const res = await fetch(`${API_BASE_URL}/reservation/my?firebase_uid=${userId}`);
      const json = await res.json();
      
      if (json.ok && json.data) {
        // Filter reservations that are completed but not rated yet
        const unrated = json.data.filter(booking => 
          (booking.status === 'completed' || booking.status === 'selesai') && 
          (booking.has_rated === 0 || !booking.has_rated)
        );
        
        // Map these to a "notification" format
        const notifs = unrated.map(booking => ({
          id: `rate_${booking.id}`,
          type: 'rating_prompt',
          title: 'Beri Rating Reservasi! 🏆',
          message: `Reservasi ${booking.reservation_code || 'Anda'} di ${booking.facility_name} (${booking.court_name}) telah selesai. Bagikan pengalaman Anda!`,
          date: booking.booking_date,
          booking: {
            ...booking,
            facilityName: booking.facility_name,
            courtName: booking.court_name,
            date: booking.booking_date
          }
        }));
        
        let eventNotifs = [];
        try {
          const eventRes = await fetch(`${API_BASE_URL}/notification/my?user_id=${userId}`);
          const eventJson = await eventRes.json();
          if (eventRes.ok && eventJson.ok) {
            eventNotifs = (eventJson.data || []).map((n) => ({
              id: n.id,
              type: n.type || 'event',
              title: n.title || 'Info',
              message: n.message || '',
              date: (n.created_at || '').slice(0, 10),
            }));
          }
        } catch (e) {}

        setNotifications([...eventNotifs, ...notifs]);
      }
    } catch (error) {
      console.error("Error fetching notifications:", error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchNotifications();
  }, []);

  const onRefresh = () => {
    setRefreshing(true);
    fetchNotifications();
  };

  const handleNotificationPress = async (notif) => {
    if (notif.type === 'rating_prompt') {
      const booking = notif.booking;
      try {
        // Enrich booking with staff-on-duty so RatingScreen can show staff rating section.
        const facilityName = booking?.facilityName || booking?.facility_name || '';
        const bookingDate = booking?.date || booking?.booking_date || '';
        const timeSlots = booking?.timeSlots || booking?.time_slots || [];
        const firstSlot = Array.isArray(timeSlots) && timeSlots.length > 0 ? Math.min(...timeSlots) : null;

        if (facilityName && bookingDate && Number.isInteger(firstSlot)) {
          // getStaffBySlot(date, hour, facilityName)
          const staff = await BookingService.getStaffBySlot(bookingDate, firstSlot, facilityName);
          if (staff) {
            setSelectedBooking({
              ...booking,
              staffOnDutyId: staff.employeeId,
              staffOnDutyName: staff.employeeName,
            });
            setShowRatingModal(true);
            return;
          }
        }

        setSelectedBooking(booking);
        setShowRatingModal(true);
      } catch (e) {
        console.error('Failed to resolve staff on duty for notification:', e);
        setSelectedBooking(booking);
        setShowRatingModal(true);
      }
    } else if (notif.type === 'event') {
      navigation.navigate('Events');
    }
  };

  const handleRatingSubmit = () => {
    setShowRatingModal(false);
    setSelectedBooking(null);
    fetchNotifications(); // Refresh list after rating
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <LinearGradient
        colors={[theme.colors.surface, theme.colors.background]}
        style={styles.header}
      >
        <Text style={styles.headerTitle}>Notifikasi</Text>
        <Text style={styles.headerSubtitle}>
          {notifications.length > 0 
            ? `Ada ${notifications.length} pesan yang perlu perhatianmu` 
            : 'Belum ada notifikasi baru'}
        </Text>
      </LinearGradient>

      <ScrollView
        contentContainerStyle={styles.content}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
      >
        {notifications.length === 0 ? (
          <View style={styles.emptyContainer}>
            <View style={styles.emptyIconContainer}>
              <Ionicons name="notifications-off-outline" size={80} color="rgba(255,255,255,0.1)" />
            </View>
            <Text style={styles.emptyText}>Tidak ada notifikasi</Text>
            <Text style={styles.emptySubtext}>Semua pesan sudah kamu baca!</Text>
          </View>
        ) : (
          notifications.map((notif) => (
            <TouchableOpacity
              key={notif.id}
              style={styles.notificationCard}
              onPress={() => handleNotificationPress(notif)}
              activeOpacity={0.7}
            >
              <View style={styles.notifIconContainer}>
                <Ionicons name={notif.type === 'event' ? 'images-outline' : 'star'} size={24} color={notif.type === 'event' ? theme.colors.primary : '#FFD700'} />
              </View>
              <View style={styles.notifTextContainer}>
                <View style={styles.notifHeader}>
                  <Text style={styles.notifTitle}>{notif.title}</Text>
                  <Text style={styles.notifDate}>{notif.date}</Text>
                </View>
                <Text style={styles.notifMessage}>{notif.message}</Text>
                <View style={styles.actionPrompt}>
                  <Text style={styles.actionPromptText}>{notif.type === 'event' ? 'Ketuk untuk lihat event' : 'Ketuk untuk memberi rating'}</Text>
                  <Ionicons name="chevron-forward" size={14} color={theme.colors.primary} />
                </View>
              </View>
            </TouchableOpacity>
          ))
        )}
      </ScrollView>

      <RatingScreen
        visible={showRatingModal}
        booking={selectedBooking}
        onClose={() => {
          setShowRatingModal(false);
          setSelectedBooking(null);
        }}
        onSubmit={handleRatingSubmit}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: theme.colors.background,
  },
  header: {
    paddingHorizontal: 20,
    paddingTop: 60,
    paddingBottom: 30,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.05)',
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  headerSubtitle: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginTop: 4,
  },
  content: {
    padding: 20,
    flexGrow: 1,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 50,
  },
  emptyIconContainer: {
    marginBottom: 20,
  },
  emptyText: {
    fontSize: 20,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    textAlign: 'center',
  },
  notificationCard: {
    backgroundColor: theme.colors.cardBackground,
    borderRadius: 15,
    padding: 16,
    flexDirection: 'row',
    marginBottom: 16,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  notifIconContainer: {
    width: 45,
    height: 45,
    borderRadius: 22.5,
    backgroundColor: 'rgba(255, 215, 0, 0.1)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  notifTextContainer: {
    flex: 1,
  },
  notifHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 6,
  },
  notifTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
    flex: 1,
    marginRight: 10,
  },
  notifDate: {
    fontSize: 11,
    color: theme.colors.textSecondary,
  },
  notifMessage: {
    fontSize: 14,
    color: 'rgba(255,255,255,0.7)',
    lineHeight: 20,
    marginBottom: 10,
  },
  actionPrompt: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 4,
  },
  actionPromptText: {
    fontSize: 12,
    fontWeight: '600',
    color: theme.colors.primary,
    marginRight: 4,
  },
});
