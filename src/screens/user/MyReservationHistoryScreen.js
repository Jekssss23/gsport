import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator, RefreshControl, Image, TouchableOpacity, Alert } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { auth } from '../../config/firebase';
import { BookingService } from '../../services/BookingService';
import { theme } from '../../styles/theme';
import RatingScreen from './RatingScreen';
import CancellationRequestScreen from './CancellationRequestScreen';

export default function MyReservationHistoryScreen() {
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedBooking, setSelectedBooking] = useState(null);
  const [showRatingModal, setShowRatingModal] = useState(false);
  const [showCancellationModal, setShowCancellationModal] = useState(false);

  useEffect(() => {
    loadBookings();
  }, []);

  useEffect(() => {
    checkExpiredBookings();
    checkForCompletedBookings();
  }, [bookings]);

  const checkForCompletedBookings = () => {
    const completedBookings = bookings.filter(booking => 
      isBookingCompleted(booking) && !booking.hasRated && !booking.ratingShown
    );
    
    if (completedBookings.length > 0) {
      // Show rating for the most recent completed booking
      const latestBooking = completedBookings[0];
      setSelectedBooking(latestBooking);
      setShowRatingModal(true);
      
      // Mark as shown to prevent repeated popups
      markRatingAsShown(latestBooking.id);
    }
  };

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

  const getStatusColor = (status) => {
    switch (status) {
      case 'confirmed': return theme.colors.success;
      case 'pending': return theme.colors.warning;
      case 'rejected': return theme.colors.error;
      case 'cancelled': return theme.colors.error;
      case 'completed': return theme.colors.success;
      case 'selesai': return theme.colors.success;
      case 'cancellation_requested': return theme.colors.warning;
      default: return theme.colors.textSecondary;
    }
  };

  const getStatusText = (status) => {
    switch (status) {
      case 'pending': return 'Menunggu';
      case 'confirmed': return 'Dikonfirmasi';
      case 'rejected': return 'Ditolak';
      case 'cancelled': return 'Dibatalkan';
      case 'completed': return 'Selesai';
      case 'selesai': return 'Selesai';
      case 'cancellation_requested': return 'Menunggu Pembatalan';
      default: return status.charAt(0).toUpperCase() + status.slice(1);
    }
  };

  const handleRatingPress = (booking) => {
    setSelectedBooking(booking);
    setShowRatingModal(true);
  };

  const handleCancellationPress = (booking) => {
    setSelectedBooking(booking);
    setShowCancellationModal(true);
  };

  const handleRatingSubmit = () => {
    loadBookings();
    setSelectedBooking(null);
    setShowRatingModal(false);
  };

  const handleRatingSkip = () => {
    // User skipped rating, don't show again for this booking
    setSelectedBooking(null);
    setShowRatingModal(false);
  };

  const handleCancellationSubmit = () => {
    loadBookings();
    setSelectedBooking(null);
    setShowCancellationModal(false);
  };

  const isBookingCompleted = (booking) => {
    return booking.status === 'completed' || booking.status === 'selesai';
  };

  const canRateBooking = (booking) => {
    return isBookingCompleted(booking) && !booking.hasRated;
  };

  const shouldShowRatingPrompt = (booking) => {
    return isBookingCompleted(booking) && !booking.hasRated && !booking.ratingShown;
  };

  const canCancelBooking = (booking) => {
    return booking.status === 'confirmed';
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <>
      <ScrollView 
        style={styles.container}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
      >
        <View style={styles.header}>
          <Text style={styles.headerTitle}>Riwayat Reservasi Saya</Text>
          <Text style={styles.headerSubtitle}>{bookings.length} total reservasi</Text>
        </View>

        {bookings.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyText}>Belum ada reservasi</Text>
            <Text style={styles.emptySubtext}>Mulai pesan fasilitas olahraga favoritmu!</Text>
          </View>
        ) : (
          bookings.map((booking) => (
            <View key={booking.id} style={styles.bookingCard}>
              <View style={styles.cardHeader}>
                <Text style={styles.facilityName}>{booking.facilityName}</Text>
                <View style={[styles.statusBadge, { backgroundColor: getStatusColor(booking.status) }]}>
                  <Text style={styles.statusText}>{getStatusText(booking.status)}</Text>
                </View>
              </View>

              <View style={styles.cardBody}>
                <View style={styles.infoRow}>
                  <Text style={styles.label}>Lapangan:</Text>
                  <Text style={styles.value}>{booking.courtName}</Text>
                </View>

                <View style={styles.infoRow}>
                  <Text style={styles.label}>Tanggal:</Text>
                  <Text style={styles.value}>{booking.date}</Text>
                </View>

                <View style={styles.infoRow}>
                  <Text style={styles.label}>Waktu:</Text>
                  <Text style={styles.value}>
                    {booking.timeSlots?.sort((a, b) => a - b).map(slot => `${slot}:00`).join(', ')}
                  </Text>
                </View>

                <View style={styles.infoRow}>
                  <Text style={styles.label}>Durasi:</Text>
                  <Text style={styles.value}>{booking.totalHours} jam</Text>
                </View>

                <View style={styles.divider} />

                <View style={styles.infoRow}>
                  <Text style={styles.label}>Total Bayar:</Text>
                  <Text style={styles.valuePrice}>Rp {booking.totalAmount?.toLocaleString()}</Text>
                </View>

                <View style={styles.infoRow}>
                  <Text style={styles.label}>DP Dibayar:</Text>
                  <Text style={styles.valuePrice}>Rp {booking.dpAmount?.toLocaleString()}</Text>
                </View>

                <View style={styles.infoRow}>
                  <Text style={styles.label}>Sisa:</Text>
                  <Text style={styles.valuePriceHighlight}>Rp {booking.remainingAmount?.toLocaleString()}</Text>
                </View>

                {booking.paymentProof && (
                  <View style={styles.proofContainer}>
                    <Text style={styles.label}>Bukti Pembayaran:</Text>
                    <Image source={{ uri: booking.paymentProof }} style={styles.proofImage} />
                  </View>
                )}

                {canCancelBooking(booking) && (
                  <TouchableOpacity 
                    style={styles.cancelButton}
                    onPress={() => handleCancellationPress(booking)}
                  >
                    <Ionicons name="close-circle" size={16} color="white" />
                    <Text style={styles.cancelButtonText}>Batalkan Booking</Text>
                  </TouchableOpacity>
                )}

                {canRateBooking(booking) && (
                  <TouchableOpacity 
                    style={styles.rateButton}
                    onPress={() => handleRatingPress(booking)}
                  >
                    <Ionicons name="star" size={16} color="white" />
                    <Text style={styles.rateButtonText}>Beri Rating</Text>
                  </TouchableOpacity>
                )}

                {isBookingCompleted(booking) && booking.hasRated && (
                  <View style={styles.ratedBadge}>
                    <Ionicons name="star" size={16} color="#FFD700" />
                    <Text style={styles.ratedText}>Sudah Diberi Rating</Text>
                  </View>
                )}

                {booking.status === 'cancellation_requested' && (
                  <View style={styles.cancellationBadge}>
                    <Ionicons name="time" size={16} color="#FFA500" />
                    <Text style={styles.cancellationText}>Menunggu Persetujuan Pembatalan</Text>
                  </View>
                )}

              </View>
            </View>
          ))
        )}
      </ScrollView>

      <RatingScreen
        visible={showRatingModal}
        booking={selectedBooking}
        onClose={handleRatingSkip}
        onSubmit={handleRatingSubmit}
      />

      <CancellationRequestScreen
        visible={showCancellationModal}
        booking={selectedBooking}
        onClose={() => {
          setShowCancellationModal(false);
          setSelectedBooking(null);
        }}
        onSubmit={handleCancellationSubmit}
      />
    </>
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
    padding: theme.spacing.medium,
    backgroundColor: theme.colors.cardBackground,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.1)',
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  headerSubtitle: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginTop: 4,
  },
  emptyContainer: {
    padding: theme.spacing.xlarge,
    alignItems: 'center',
  },
  emptyText: {
    fontSize: 18,
    color: theme.colors.text,
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: theme.colors.textSecondary,
  },
  bookingCard: {
    backgroundColor: theme.colors.cardBackground,
    margin: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
    overflow: 'hidden',
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: theme.spacing.medium,
    backgroundColor: 'rgba(255,0,0,0.1)',
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.1)',
  },
  facilityName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#000',
  },
  cardBody: {
    padding: theme.spacing.medium,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  label: {
    fontSize: 14,
    color: theme.colors.textSecondary,
  },
  value: {
    fontSize: 14,
    color: theme.colors.text,
    fontWeight: '500',
    flex: 1,
    textAlign: 'right',
  },
  valuePrice: {
    fontSize: 14,
    color: theme.colors.text,
    fontWeight: 'bold',
  },
  valuePriceHighlight: {
    fontSize: 14,
    color: theme.colors.primary,
    fontWeight: 'bold',
  },
  divider: {
    height: 1,
    backgroundColor: 'rgba(255,255,255,0.1)',
    marginVertical: theme.spacing.small,
  },
  proofContainer: {
    marginTop: theme.spacing.small,
  },
  proofImage: {
    width: '100%',
    height: 150,
    borderRadius: theme.borderRadius.small,
    marginTop: 8,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalContent: {
    backgroundColor: theme.colors.cardBackground,
    padding: 20,
    borderRadius: 12,
    width: '90%',
    maxHeight: '80%',
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 16,
    textAlign: 'center',
  },
  modalInfo: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginBottom: 16,
    textAlign: 'center',
  },
  modalLabel: {
    fontSize: 16,
    color: theme.colors.text,
    marginBottom: 8,
  },
  modalInput: {
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.2)',
    borderRadius: 8,
    padding: 12,
    color: theme.colors.text,
    backgroundColor: 'rgba(255,255,255,0.05)',
    textAlignVertical: 'top',
    marginBottom: 16,
  },
  modalButtons: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  modalCancelButton: {
    backgroundColor: 'rgba(255,255,255,0.1)',
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 8,
    flex: 1,
    marginRight: 8,
    alignItems: 'center',
  },
  modalConfirmButton: {
    backgroundColor: theme.colors.primary,
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 8,
    flex: 1,
    marginLeft: 8,
    alignItems: 'center',
  },
  modalButtonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
  rateButton: {
    backgroundColor: theme.colors.primary,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    borderRadius: 8,
    marginTop: 16,
    gap: 8,
  },
  rateButtonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
  ratedBadge: {
    backgroundColor: 'rgba(255, 215, 0, 0.2)',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    borderRadius: 8,
    marginTop: 16,
    gap: 6,
    borderWidth: 1,
    borderColor: 'rgba(255, 215, 0, 0.3)',
  },
  ratedText: {
    color: '#FFD700',
    fontSize: 14,
    fontWeight: '600',
  },
  cancelButton: {
    backgroundColor: '#FF4444',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    borderRadius: 8,
    marginTop: 16,
    gap: 8,
  },
  cancelButtonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
  cancellationBadge: {
    backgroundColor: 'rgba(255, 165, 0, 0.2)',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    borderRadius: 8,
    marginTop: 16,
    gap: 6,
    borderWidth: 1,
    borderColor: 'rgba(255, 165, 0, 0.3)',
  },
  cancellationText: {
    color: '#FFA500',
    fontSize: 14,
    fontWeight: '600',
  },
});
