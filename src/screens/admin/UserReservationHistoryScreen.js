import React, { useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, TextInput } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../../styles/theme';

export default function UserReservationHistoryScreen() {
  const [searchQuery, setSearchQuery] = useState('');
  const [filterStatus, setFilterStatus] = useState('all'); // all, completed, cancelled

  const [reservations, setReservations] = useState([
    { id: 1, userName: 'John Doe', userId: 'USR001', field: 'Lapangan A', date: '2026-02-01', time: '14:00 - 16:00', status: 'completed' },
    { id: 2, userName: 'Jane Smith', userId: 'USR002', field: 'Lapangan B', date: '2026-02-02', time: '10:00 - 12:00', status: 'completed' },
    { id: 3, userName: 'Mike Johnson', userId: 'USR003', field: 'Lapangan C', date: '2026-01-30', time: '16:00 - 18:00', status: 'cancelled' },
    { id: 4, userName: 'Sarah Williams', userId: 'USR004', field: 'Lapangan A', date: '2026-02-01', time: '08:00 - 10:00', status: 'completed' },
    { id: 5, userName: 'David Brown', userId: 'USR005', field: 'Lapangan B', date: '2026-01-29', time: '12:00 - 14:00', status: 'cancelled' },
    { id: 6, userName: 'John Doe', userId: 'USR001', field: 'Lapangan C', date: '2026-01-28', time: '18:00 - 20:00', status: 'completed' },
  ]);

  const filteredReservations = reservations.filter(r => {
    const matchesSearch = r.userName.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         r.userId.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         r.field.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesFilter = filterStatus === 'all' || r.status === filterStatus;
    return matchesSearch && matchesFilter;
  });

  const completedCount = reservations.filter(r => r.status === 'completed').length;
  const cancelledCount = reservations.filter(r => r.status === 'cancelled').length;

  const ReservationCard = ({ reservation }) => (
    <View style={styles.cardContainer}>
      <LinearGradient
        colors={reservation.status === 'completed' ? ['#00AA00', '#006600'] : ['#666666', '#333333']}
        style={styles.card}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <View style={styles.cardHeader}>
          <View style={styles.userInfo}>
            <Ionicons name="person-circle" size={28} color="white" />
            <View style={styles.userDetails}>
              <Text style={styles.userName}>{reservation.userName}</Text>
              <Text style={styles.userId}>{reservation.userId}</Text>
            </View>
          </View>
          <View style={[styles.statusBadge, reservation.status === 'cancelled' && styles.cancelledBadge]}>
            <Text style={styles.statusText}>{reservation.status === 'completed' ? 'SELESAI' : 'DIBATALKAN'}</Text>
          </View>
        </View>

        <View style={styles.detailsContainer}>
          <View style={styles.detailRow}>
            <Ionicons name="location" size={16} color="rgba(255,255,255,0.7)" />
            <Text style={styles.detailText}>{reservation.field}</Text>
          </View>
          <View style={styles.detailRow}>
            <Ionicons name="calendar-outline" size={16} color="rgba(255,255,255,0.7)" />
            <Text style={styles.detailText}>{reservation.date}</Text>
          </View>
          <View style={styles.detailRow}>
            <Ionicons name="time-outline" size={16} color="rgba(255,255,255,0.7)" />
            <Text style={styles.detailText}>{reservation.time}</Text>
          </View>
        </View>
      </LinearGradient>
    </View>
  );

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Ionicons name="time" size={24} color={theme.colors.primary} />
        <Text style={styles.headerTitle}>Riwayat Reservasi User</Text>
      </View>

      <View style={styles.statsContainer}>
        <LinearGradient
          colors={['rgba(255,0,0,0.2)', 'rgba(0,0,0,0.3)']}
          style={styles.statsCard}
        >
          <View style={styles.statItem}>
            <Ionicons name="checkmark-circle" size={20} color="#00FF00" />
            <Text style={styles.statNumber}>{completedCount}</Text>
            <Text style={styles.statLabel}>Selesai</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statItem}>
            <Ionicons name="close-circle" size={20} color="#FF6B6B" />
            <Text style={styles.statNumber}>{cancelledCount}</Text>
            <Text style={styles.statLabel}>Dibatalkan</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statItem}>
            <Ionicons name="list" size={20} color={theme.colors.primary} />
            <Text style={styles.statNumber}>{reservations.length}</Text>
            <Text style={styles.statLabel}>Total</Text>
          </View>
        </LinearGradient>
      </View>

      <View style={styles.searchContainer}>
        <Ionicons name="search" size={20} color={theme.colors.textSecondary} style={styles.searchIcon} />
        <TextInput
          style={styles.searchInput}
          placeholder="Cari nama, ID atau lapangan..."
          placeholderTextColor={theme.colors.textSecondary}
          value={searchQuery}
          onChangeText={setSearchQuery}
        />
      </View>

      <View style={styles.filterContainer}>
        <TouchableOpacity 
          style={[styles.filterButton, filterStatus === 'all' && styles.activeFilter]}
          onPress={() => setFilterStatus('all')}
        >
          <Text style={[styles.filterText, filterStatus === 'all' && styles.activeFilterText]}>Semua</Text>
        </TouchableOpacity>
        <TouchableOpacity 
          style={[styles.filterButton, filterStatus === 'completed' && styles.activeFilter]}
          onPress={() => setFilterStatus('completed')}
        >
          <Text style={[styles.filterText, filterStatus === 'completed' && styles.activeFilterText]}>Selesai</Text>
        </TouchableOpacity>
        <TouchableOpacity 
          style={[styles.filterButton, filterStatus === 'cancelled' && styles.activeFilter]}
          onPress={() => setFilterStatus('cancelled')}
        >
          <Text style={[styles.filterText, filterStatus === 'cancelled' && styles.activeFilterText]}>Dibatalkan</Text>
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        <Text style={styles.sectionTitle}>Riwayat ({filteredReservations.length})</Text>
        {filteredReservations.length === 0 ? (
          <View style={styles.emptyState}>
            <Ionicons name="document-text-outline" size={64} color={theme.colors.textSecondary} />
            <Text style={styles.emptyText}>Tidak ada riwayat ditemukan</Text>
          </View>
        ) : (
          filteredReservations.map(reservation => <ReservationCard key={reservation.id} reservation={reservation} />)
        )}
      </ScrollView>
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
  statsContainer: {
    padding: 20,
    paddingBottom: 10,
  },
  statsCard: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.2)',
  },
  statItem: {
    alignItems: 'center',
  },
  statNumber: {
    color: theme.colors.text,
    fontSize: 22,
    fontWeight: 'bold',
    marginTop: 6,
  },
  statLabel: {
    color: theme.colors.textSecondary,
    fontSize: 11,
    marginTop: 2,
  },
  statDivider: {
    width: 1,
    backgroundColor: 'rgba(255,255,255,0.1)',
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.colors.cardBackground,
    marginHorizontal: 20,
    marginBottom: 10,
    borderRadius: 10,
    paddingHorizontal: 15,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
  },
  searchIcon: {
    marginRight: 10,
  },
  searchInput: {
    flex: 1,
    color: theme.colors.text,
    paddingVertical: 12,
    fontSize: 14,
  },
  filterContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    marginBottom: 10,
    gap: 10,
  },
  filterButton: {
    flex: 1,
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: 'rgba(255,255,255,0.05)',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
    alignItems: 'center',
  },
  activeFilter: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  filterText: {
    color: theme.colors.textSecondary,
    fontSize: 13,
    fontWeight: '600',
  },
  activeFilterText: {
    color: 'white',
  },
  content: {
    padding: 20,
    paddingTop: 10,
  },
  sectionTitle: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    marginBottom: 12,
    marginLeft: 5,
  },
  cardContainer: {
    marginBottom: 12,
    borderRadius: 12,
    overflow: 'hidden',
    elevation: 3,
  },
  card: {
    padding: 16,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  userInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  userDetails: {
    marginLeft: 12,
  },
  userName: {
    color: 'white',
    fontSize: 15,
    fontWeight: 'bold',
  },
  userId: {
    color: 'rgba(255,255,255,0.6)',
    fontSize: 12,
    marginTop: 2,
  },
  statusBadge: {
    backgroundColor: 'rgba(0,255,0,0.3)',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  cancelledBadge: {
    backgroundColor: 'rgba(255,0,0,0.3)',
  },
  statusText: {
    color: 'white',
    fontSize: 10,
    fontWeight: 'bold',
  },
  detailsContainer: {
    gap: 6,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  detailText: {
    color: 'rgba(255,255,255,0.8)',
    fontSize: 13,
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyText: {
    color: theme.colors.textSecondary,
    fontSize: 16,
    marginTop: 12,
  },
});
