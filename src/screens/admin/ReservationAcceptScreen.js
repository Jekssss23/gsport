import React, { useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, Alert } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../../styles/theme';

export default function ReservationAcceptScreen() {
  const [reservations, setReservations] = useState([
    { id: 1, userName: 'John Doe', field: 'Lapangan A', date: '2026-02-05', time: '14:00 - 16:00', status: 'pending' },
    { id: 2, userName: 'Jane Smith', field: 'Lapangan B', date: '2026-02-06', time: '10:00 - 12:00', status: 'pending' },
    { id: 3, userName: 'Mike Johnson', field: 'Lapangan C', date: '2026-02-07', time: '16:00 - 18:00', status: 'pending' },
    { id: 4, userName: 'Sarah Williams', field: 'Lapangan A', date: '2026-02-08', time: '08:00 - 10:00', status: 'pending' },
  ]);

  const handleAccept = (id) => {
    Alert.alert(
      'Terima Reservasi',
      'Apakah Anda yakin ingin menerima reservasi ini?',
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Terima',
          onPress: () => {
            setReservations(prev => prev.map(r => r.id === id ? { ...r, status: 'accepted' } : r));
            Alert.alert('Berhasil', 'Reservasi diterima!');
          }
        }
      ]
    );
  };

  const handleReject = (id) => {
    Alert.alert(
      'Tolak Reservasi',
      'Apakah Anda yakin ingin menolak reservasi ini?',
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Tolak',
          style: 'destructive',
          onPress: () => {
            setReservations(prev => prev.filter(r => r.id !== id));
            Alert.alert('Ditolak', 'Reservasi telah ditolak.');
          }
        }
      ]
    );
  };

  const ReservationCard = ({ item }) => (
    <View style={styles.cardContainer}>
      <LinearGradient
        colors={item.status === 'accepted' ? ['#00AA00', '#006600'] : ['#333333', '#000000']}
        style={styles.card}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <View style={styles.cardHeader}>
          <View style={styles.userInfo}>
            <Ionicons name="person-circle" size={32} color="white" />
            <View style={styles.userDetails}>
              <Text style={styles.userName}>{item.userName}</Text>
              <Text style={styles.field}>{item.field}</Text>
            </View>
          </View>
          <View style={[styles.statusBadge, item.status === 'accepted' && styles.acceptedBadge]}>
            <Text style={styles.statusText}>{item.status === 'accepted' ? 'DITERIMA' : 'MENUNGGU'}</Text>
          </View>
        </View>
        
        <View style={styles.detailsRow}>
          <View style={styles.detailItem}>
            <Ionicons name="calendar-outline" size={16} color="rgba(255,255,255,0.7)" />
            <Text style={styles.detailText}>{item.date}</Text>
          </View>
          <View style={styles.detailItem}>
            <Ionicons name="time-outline" size={16} color="rgba(255,255,255,0.7)" />
            <Text style={styles.detailText}>{item.time}</Text>
          </View>
        </View>

        {item.status === 'pending' && (
          <View style={styles.actionButtons}>
            <TouchableOpacity 
              style={[styles.actionButton, styles.rejectButton]}
              onPress={() => handleReject(item.id)}
            >
              <Ionicons name="close-circle" size={20} color="white" />
              <Text style={styles.buttonText}>Tolak</Text>
            </TouchableOpacity>
            <TouchableOpacity 
              style={[styles.actionButton, styles.acceptButton]}
              onPress={() => handleAccept(item.id)}
            >
              <Ionicons name="checkmark-circle" size={20} color="white" />
              <Text style={styles.buttonText}>Terima</Text>
            </TouchableOpacity>
          </View>
        )}
      </LinearGradient>
    </View>
  );

  const pendingCount = reservations.filter(r => r.status === 'pending').length;

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Ionicons name="checkmark-circle" size={24} color={theme.colors.primary} />
        <Text style={styles.headerTitle}>Permintaan Reservasi</Text>
      </View>

      <View style={styles.statsBar}>
        <Text style={styles.statsText}>
          <Text style={styles.statsNumber}>{pendingCount}</Text> Permintaan Menunggu
        </Text>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        {reservations.length === 0 ? (
          <View style={styles.emptyState}>
            <Ionicons name="checkmark-done-circle" size={64} color={theme.colors.textSecondary} />
            <Text style={styles.emptyText}>Tidak ada reservasi menunggu</Text>
          </View>
        ) : (
          reservations.map(item => <ReservationCard key={item.id} item={item} />)
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
  statsBar: {
    backgroundColor: 'rgba(255,0,0,0.1)',
    paddingVertical: 12,
    paddingHorizontal: 20,
  },
  statsText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  statsNumber: {
    color: theme.colors.primary,
    fontSize: 18,
    fontWeight: 'bold',
  },
  content: {
    padding: 20,
  },
  cardContainer: {
    marginBottom: 16,
    borderRadius: 12,
    overflow: 'hidden',
    elevation: 4,
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
    fontSize: 16,
    fontWeight: 'bold',
  },
  field: {
    color: 'rgba(255,255,255,0.7)',
    fontSize: 13,
    marginTop: 2,
  },
  statusBadge: {
    backgroundColor: 'rgba(255,165,0,0.3)',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  acceptedBadge: {
    backgroundColor: 'rgba(0,255,0,0.3)',
  },
  statusText: {
    color: 'white',
    fontSize: 10,
    fontWeight: 'bold',
  },
  detailsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 16,
  },
  detailItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  detailText: {
    color: 'rgba(255,255,255,0.8)',
    fontSize: 13,
    marginLeft: 6,
  },
  actionButtons: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 10,
  },
  actionButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: 8,
    gap: 6,
  },
  acceptButton: {
    backgroundColor: theme.colors.primary,
  },
  rejectButton: {
    backgroundColor: 'rgba(255,255,255,0.1)',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.2)',
  },
  buttonText: {
    color: 'white',
    fontSize: 14,
    fontWeight: 'bold',
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
