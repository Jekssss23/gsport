import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator, RefreshControl, Image } from 'react-native';
import { auth } from '../../config/firebase';
import { BookingService } from '../../services/BookingService';
import { theme } from '../../styles/theme';

export default function MyReservationHistoryScreen() {
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadBookings();
  }, []);

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
      default: return theme.colors.textSecondary;
    }
  };

  const getStatusText = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <ScrollView 
      style={styles.container}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
      }
    >
      <View style={styles.header}>
        <Text style={styles.headerTitle}>My Reservations</Text>
        <Text style={styles.headerSubtitle}>{bookings.length} total bookings</Text>
      </View>

      {bookings.length === 0 ? (
        <View style={styles.emptyContainer}>
          <Text style={styles.emptyText}>No reservations yet</Text>
          <Text style={styles.emptySubtext}>Start booking your favorite sports facility!</Text>
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
                <Text style={styles.label}>Court:</Text>
                <Text style={styles.value}>{booking.courtName}</Text>
              </View>

              <View style={styles.infoRow}>
                <Text style={styles.label}>Date:</Text>
                <Text style={styles.value}>{booking.date}</Text>
              </View>

              <View style={styles.infoRow}>
                <Text style={styles.label}>Time:</Text>
                <Text style={styles.value}>
                  {booking.timeSlots?.sort((a, b) => a - b).map(slot => `${slot}:00`).join(', ')}
                </Text>
              </View>

              <View style={styles.infoRow}>
                <Text style={styles.label}>Duration:</Text>
                <Text style={styles.value}>{booking.totalHours} hour(s)</Text>
              </View>

              <View style={styles.divider} />

              <View style={styles.infoRow}>
                <Text style={styles.label}>Total Amount:</Text>
                <Text style={styles.valuePrice}>Rp {booking.totalAmount?.toLocaleString()}</Text>
              </View>

              <View style={styles.infoRow}>
                <Text style={styles.label}>DP Paid:</Text>
                <Text style={styles.valuePrice}>Rp {booking.dpAmount?.toLocaleString()}</Text>
              </View>

              <View style={styles.infoRow}>
                <Text style={styles.label}>Remaining:</Text>
                <Text style={styles.valuePriceHighlight}>Rp {booking.remainingAmount?.toLocaleString()}</Text>
              </View>

              {booking.paymentProof && (
                <View style={styles.proofContainer}>
                  <Text style={styles.label}>Payment Proof:</Text>
                  <Image source={{ uri: booking.paymentProof }} style={styles.proofImage} />
                </View>
              )}
            </View>
          </View>
        ))
      )}
    </ScrollView>
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
});
