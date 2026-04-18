import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator, RefreshControl, Image, TouchableOpacity, Alert, Dimensions } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { StatusBar } from 'expo-status-bar';
import { auth } from '../../config/firebase';
import { BookingService } from '../../services/BookingService';
import { theme } from '../../styles/theme';
import RatingScreen from './RatingScreen';
import CancellationRequestScreen from './CancellationRequestScreen';

const { width } = Dimensions.get('window');

export default function MyReservationHistoryScreen({ navigation }) {
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedBooking, setSelectedBooking] = useState(null);
  const [showRatingModal, setShowRatingModal] = useState(false);
  const [showCancellationModal, setShowCancellationModal] = useState(false);
  const [userId, setUserId] = useState('');

  useEffect(() => {
    loadBookings();
  }, []);

  useEffect(() => {
    checkExpiredBookings();
  }, [bookings]);

  const markRatingAsShown = async (bookingId) => {
    try {
      await BookingService.updateBooking(bookingId, { ratingShown: true });
    } catch (error) {
      console.error('Error marking rating as shown:', error);
    }
  };

  const checkExpiredBookings = async () => {
    try {
      await BookingService.checkAndUpdateExpiredBookings();
      loadBookings();
    } catch (error) {
      console.error('Error checking expired bookings:', error);
    }
  };

  const loadBookings = async () => {
    try {
      const userId = auth.currentUser?.uid || auth.currentUser?.email;
      if (userId) {
        setUserId(userId);
        const userBookings = await BookingService.getUserBookings(userId);
        setBookings(userBookings);
      }
    } catch (error) {
      console.error('Error loading bookings:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadBookings();
  };

  const getStatusConfig = (status) => {
    switch (status) {
      case 'confirmed': return { color: theme.colors.success, text: 'Confirmed', icon: 'checkmark-circle' };
      case 'pending': return { color: theme.colors.warning, text: 'Pending', icon: 'time' };
      case 'rejected': return { color: theme.colors.error, text: 'Rejected', icon: 'close-circle' };
      case 'cancelled': return { color: theme.colors.error, text: 'Cancelled', icon: 'ban' };
      case 'completed': 
      case 'selesai': return { color: theme.colors.success, text: 'Completed', icon: 'ribbon' };
      case 'cancellation_requested': return { color: theme.colors.warning, text: 'Cancelling...', icon: 'hourglass' };
      default: return { color: theme.colors.textSecondary, text: status, icon: 'help-circle' };
    }
  };

  const handleRatingPress = async (booking) => {
    try {
      // Enrich booking with staff-on-duty (computed from schedule) so RatingScreen can rate the staff.
      const facilityName = booking.facilityName || booking.facility_name || '';
      const bookingDate = booking.date || booking.booking_date || '';
      const timeSlots = booking.timeSlots || booking.time_slots || [];
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
      console.error('Failed to resolve staff on duty:', e);
      setSelectedBooking(booking);
      setShowRatingModal(true);
    }
  };

  const handleCancellationPress = (booking) => {
    setSelectedBooking(booking);
    setShowCancellationModal(true);
  };

  const handleRatingSubmit = () => {
    if (selectedBooking) markRatingAsShown(selectedBooking.id);
    setSelectedBooking(null);
    setShowRatingModal(false);
    loadBookings();
  };

  const handleRatingSkip = () => {
    if (selectedBooking) markRatingAsShown(selectedBooking.id);
    setSelectedBooking(null);
    setShowRatingModal(false);
  };

  const handleCancellationSubmit = () => {
    loadBookings();
    setSelectedBooking(null);
    setShowCancellationModal(false);
  };

  const isBookingCompleted = (booking) => booking.status === 'completed' || booking.status === 'selesai';
  const canRateBooking = (booking) => isBookingCompleted(booking) && (booking.has_rated === 0 || !booking.has_rated);
  const canCancelBooking = (booking) => booking.status === 'confirmed';

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <StatusBar style="light" />
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <LinearGradient colors={[theme.colors.background, '#000000']} style={StyleSheet.absoluteFill} />
      
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()}>
          <Ionicons name="arrow-back" size={24} color="white" />
        </TouchableOpacity>
        <View>
          <Text style={styles.headerTitle}>My Bookings</Text>
          <Text style={styles.headerSubtitle}>{bookings.length} reservations found</Text>
        </View>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={theme.colors.primary} />}
        showsVerticalScrollIndicator={false}
      >
        {bookings.length === 0 ? (
          <View style={styles.emptyContainer}>
            <View style={styles.emptyIconCircle}>
              <Ionicons name="calendar-outline" size={40} color={theme.colors.textTertiary} />
            </View>
            <Text style={styles.emptyText}>No Bookings Yet</Text>
            <Text style={styles.emptySubtext}>Start booking your favorite sports facilities now!</Text>
            <TouchableOpacity style={styles.emptyButton} onPress={() => navigation.navigate('FieldReservation')}>
              <Text style={styles.emptyButtonText}>Book a Field</Text>
            </TouchableOpacity>
          </View>
        ) : (
          bookings.map((booking) => {
            const status = getStatusConfig(booking.status);
            return (
              <View key={booking.id} style={styles.bookingCard}>
                <LinearGradient colors={['#2A2A2A', '#1A1A1A']} style={styles.cardGradient}>
                  <View style={styles.cardHeader}>
                    <View style={styles.facilityInfo}>
                      <Text style={styles.facilityName}>{booking.facilityName}</Text>
                      <View style={styles.courtRow}>
                        <Ionicons name="location-outline" size={12} color={theme.colors.textSecondary} />
                        <Text style={styles.courtName}>{booking.courtName}</Text>
                      </View>
                    </View>
                    <View style={[styles.statusBadge, { backgroundColor: `${status.color}20` }]}>
                      <Ionicons name={status.icon} size={12} color={status.color} />
                      <Text style={[styles.statusText, { color: status.color }]}>{status.text}</Text>
                    </View>
                  </View>

                  <View style={styles.cardDivider} />

                  <View style={styles.detailsRow}>
                    <View style={styles.detailItem}>
                      <Text style={styles.detailLabel}>DATE</Text>
                      <Text style={styles.detailValue}>{booking.date}</Text>
                    </View>
                    <View style={styles.detailItem}>
                      <Text style={styles.detailLabel}>TIME</Text>
                      <Text style={styles.detailValue}>
                        {booking.timeSlots?.sort((a, b) => a - b)[0]}:00 - {booking.timeSlots?.sort((a, b) => a - b).slice(-1)[0] + 1}:00
                      </Text>
                    </View>
                    <View style={styles.detailItem}>
                      <Text style={styles.detailLabel}>DURATION</Text>
                      <Text style={styles.detailValue}>{booking.totalHours} Hrs</Text>
                    </View>
                  </View>

                  <View style={styles.priceSection}>
                    <View style={styles.priceRow}>
                      <Text style={styles.priceLabel}>Total Amount</Text>
                      <Text style={styles.priceValue}>Rp {booking.totalAmount?.toLocaleString()}</Text>
                    </View>
                    <View style={styles.priceRow}>
                      <Text style={styles.priceLabel}>Paid DP</Text>
                      <Text style={styles.priceValue}>Rp {booking.dpAmount?.toLocaleString()}</Text>
                    </View>
                    <View style={styles.priceRow}>
                      <Text style={styles.priceLabelHighlight}>Remaining</Text>
                      <Text style={styles.priceValueHighlight}>Rp {booking.remainingAmount?.toLocaleString()}</Text>
                    </View>
                  </View>

                  {booking.paymentProof && (
                    <TouchableOpacity style={styles.proofToggle} activeOpacity={0.9}>
                       <Text style={styles.proofLabel}>PAYMENT PROOF</Text>
                       <Image source={{ uri: booking.paymentProof }} style={styles.proofImage} />
                    </TouchableOpacity>
                  )}

                  <View style={styles.actionRow}>
                    {canCancelBooking(booking) && (
                      <TouchableOpacity style={styles.cancelBtn} onPress={() => handleCancellationPress(booking)}>
                        <Ionicons name="close-circle-outline" size={18} color={theme.colors.error} />
                        <Text style={styles.cancelBtnText}>Cancel Booking</Text>
                      </TouchableOpacity>
                    )}
                    {canRateBooking(booking) && (
                      <TouchableOpacity style={styles.rateBtn} onPress={() => handleRatingPress(booking)}>
                        <LinearGradient colors={theme.gradients.primary} style={styles.rateGradient}>
                          <Ionicons name="star" size={16} color="white" />
                          <Text style={styles.rateBtnText}>Rate Session</Text>
                        </LinearGradient>
                      </TouchableOpacity>
                    )}
                    {isBookingCompleted(booking) && booking.has_rated === 1 && (
                      <View style={styles.completedBadge}>
                        <Ionicons name="checkmark-done-circle" size={18} color={theme.colors.success} />
                        <Text style={styles.completedText}>Rated & Completed</Text>
                      </View>
                    )}
                  </View>
                </LinearGradient>
              </View>
            );
          })
        )}
      </ScrollView>

      <RatingScreen visible={showRatingModal} booking={selectedBooking} onClose={handleRatingSkip} onSubmit={handleRatingSubmit} />
      <CancellationRequestScreen visible={showCancellationModal} booking={selectedBooking} onClose={() => { setShowCancellationModal(false); setSelectedBooking(null); }} onSubmit={handleCancellationSubmit} />
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
    paddingTop: 60,
    paddingBottom: 20,
    paddingHorizontal: 25,
    flexDirection: 'row',
    alignItems: 'center',
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  headerTitle: {
    color: 'white',
    fontSize: 24,
    fontWeight: 'bold',
  },
  headerSubtitle: {
    color: theme.colors.textSecondary,
    fontSize: 12,
  },
  scrollContent: {
    padding: 25,
    paddingTop: 10,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 80,
    paddingHorizontal: 40,
  },
  emptyIconCircle: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 25,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  emptyText: {
    color: 'white',
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 12,
  },
  emptySubtext: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    textAlign: 'center',
    lineHeight: 22,
    marginBottom: 30,
  },
  emptyButton: {
    paddingVertical: 12,
    paddingHorizontal: 25,
    borderRadius: theme.borderRadius.medium,
    backgroundColor: theme.colors.primary,
  },
  emptyButtonText: {
    color: 'white',
    fontSize: 14,
    fontWeight: 'bold',
  },
  bookingCard: {
    marginBottom: 20,
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    ...theme.shadows.medium,
  },
  cardGradient: {
    padding: 20,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 15,
  },
  facilityInfo: {
    flex: 1,
  },
  facilityName: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  courtRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  courtName: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    marginLeft: 4,
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 20,
  },
  statusText: {
    fontSize: 10,
    fontWeight: 'bold',
    marginLeft: 4,
    textTransform: 'uppercase',
  },
  cardDivider: {
    height: 1,
    backgroundColor: 'rgba(255,255,255,0.05)',
    marginVertical: 15,
  },
  detailsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 20,
  },
  detailItem: {
    flex: 1,
  },
  detailLabel: {
    color: theme.colors.textTertiary,
    fontSize: 9,
    fontWeight: '800',
    letterSpacing: 1,
    marginBottom: 6,
  },
  detailValue: {
    color: 'white',
    fontSize: 13,
    fontWeight: '600',
  },
  priceSection: {
    backgroundColor: 'rgba(0,0,0,0.2)',
    padding: 15,
    borderRadius: theme.borderRadius.medium,
    marginBottom: 15,
  },
  priceRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 6,
  },
  priceLabel: {
    color: theme.colors.textSecondary,
    fontSize: 12,
  },
  priceValue: {
    color: 'white',
    fontSize: 12,
    fontWeight: '600',
  },
  priceLabelHighlight: {
    color: 'white',
    fontSize: 14,
    fontWeight: 'bold',
  },
  priceValueHighlight: {
    color: theme.colors.primary,
    fontSize: 16,
    fontWeight: 'bold',
  },
  proofToggle: {
    marginBottom: 15,
  },
  proofLabel: {
    color: theme.colors.textTertiary,
    fontSize: 9,
    fontWeight: '800',
    letterSpacing: 1,
    marginBottom: 10,
  },
  proofImage: {
    width: '100%',
    height: 150,
    borderRadius: theme.borderRadius.medium,
    backgroundColor: 'rgba(255,255,255,0.02)',
  },
  actionRow: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    alignItems: 'center',
  },
  cancelBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: theme.borderRadius.medium,
    borderWidth: 1,
    borderColor: theme.colors.error,
    marginRight: 10,
  },
  cancelBtnText: {
    color: theme.colors.error,
    fontSize: 12,
    fontWeight: 'bold',
    marginLeft: 6,
  },
  rateBtn: {
    borderRadius: theme.borderRadius.medium,
    overflow: 'hidden',
  },
  rateGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingVertical: 10,
  },
  rateBtnText: {
    color: 'white',
    fontSize: 12,
    fontWeight: 'bold',
    marginLeft: 6,
  },
  completedBadge: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  completedText: {
    color: theme.colors.success,
    fontSize: 12,
    fontWeight: 'bold',
    marginLeft: 6,
  },
});
