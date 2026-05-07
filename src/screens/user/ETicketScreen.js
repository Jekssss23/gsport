import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Image } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../../styles/theme';
import { APP_LOGO_PRIMARY } from '../../constants/assets';

const getSportIcon = (facilityName = '') => {
  const name = String(facilityName).toLowerCase();
  if (name.includes('futsal')) return 'football';
  if (name.includes('badminton')) return 'tennisball';
  if (name.includes('pickle')) return 'baseball';
  return 'ticket-outline';
};

export default function ETicketScreen({ navigation, route }) {
  const booking = route?.params?.booking || {};
  const slots = (booking.timeSlots || []).slice().sort((a, b) => a - b);
  const timeRange = slots.length ? `${slots[0]}:00 - ${slots[slots.length - 1] + 1}:00` : '-';

  return (
    <View style={styles.container}>
      <LinearGradient colors={['#0A0A0A', '#120000']} style={StyleSheet.absoluteFill} />
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.headerBtn}>
          <Ionicons name="arrow-back" size={22} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>E-Ticket</Text>
        <View style={styles.headerBtn} />
      </View>

      <ScrollView contentContainerStyle={styles.content}>
        <LinearGradient colors={['#1a1a1a', '#0d0d0d']} style={styles.ticket}>
          <View style={styles.topRow}>
            <Image source={APP_LOGO_PRIMARY} style={styles.logo} resizeMode="contain" />
            <Ionicons name={getSportIcon(booking.facilityName)} size={32} color={theme.colors.primary} />
          </View>
          <Text style={styles.title}>G SPORTS CENTER</Text>
          <Text style={styles.subtitle}>Reservation E-Ticket</Text>

          <View style={styles.divider} />

          <Row label="Kode Reservasi" value={booking.reservationCode || `RSV-${booking.id || '-'}`} />
          <Row label="Fasilitas" value={booking.facilityName || '-'} />
          <Row label="Lapangan" value={booking.courtName || '-'} />
          <Row label="Tanggal" value={booking.date || '-'} />
          <Row label="Jam" value={timeRange} />
          <Row label="Durasi" value={`${booking.totalHours || 0} Jam`} />
          <Row label="Staff Jaga" value={booking.assignedStaffName || 'Akan ditentukan'} />
          <Row label="Total" value={`Rp ${(booking.totalAmount || 0).toLocaleString('id-ID')}`} />
          <Row label="DP" value={`Rp ${(booking.dpAmount || 0).toLocaleString('id-ID')}`} />
          <Row label="Sisa Bayar" value={`Rp ${(booking.remainingAmount || 0).toLocaleString('id-ID')}`} />

          <View style={styles.divider} />
          <Text style={styles.note}>Tunjukkan e-ticket ini saat datang ke lokasi.</Text>
        </LinearGradient>
      </ScrollView>
    </View>
  );
}

function Row({ label, value }) {
  return (
    <View style={styles.row}>
      <Text style={styles.label}>{label}</Text>
      <Text style={styles.value}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#000' },
  header: {
    paddingTop: 56,
    paddingBottom: 12,
    paddingHorizontal: 16,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerBtn: { width: 38, height: 38, alignItems: 'center', justifyContent: 'center' },
  headerTitle: { color: 'white', fontWeight: '800', fontSize: 18 },
  content: { padding: 18, paddingBottom: 40 },
  ticket: {
    borderRadius: 18,
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.25)',
    padding: 18,
  },
  topRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  logo: { width: 100, height: 38 },
  title: { color: 'white', fontSize: 18, fontWeight: '900', marginTop: 8 },
  subtitle: { color: '#b3b3b3', fontSize: 12, marginTop: 2 },
  divider: { height: 1, backgroundColor: 'rgba(255,255,255,0.1)', marginVertical: 14 },
  row: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 10 },
  label: { color: '#a0a0a0', fontSize: 12 },
  value: { color: 'white', fontSize: 12, fontWeight: '700', maxWidth: '58%', textAlign: 'right' },
  note: { color: theme.colors.textSecondary, fontSize: 11, textAlign: 'center' },
});

