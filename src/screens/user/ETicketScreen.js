import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Dimensions, Image } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import Svg, { Path } from 'react-native-svg';

const GSC_LOGO = require('../../../assets/LOGO/LOGO (GSC).png');
const { width: SCREEN_W } = Dimensions.get('window');
const INK = '#5a3520';

const getSportIcon = (name = '') => {
  const n = String(name).toLowerCase();
  if (n.includes('futsal')) return 'football';
  if (n.includes('badminton')) return 'tennisball';
  if (n.includes('pickle')) return 'baseball';
  return 'ticket-outline';
};

export default function ETicketScreen({ navigation, route }) {
  const booking = route?.params?.booking || {};
  const slots = (booking.timeSlots || []).slice().sort((a, b) => a - b);
  const timeRange = slots.length ? `${slots[0]}:00 - ${slots[slots.length - 1] + 1}:00` : '-';
  const code = booking.reservationCode || `RSV-${booking.id || '-'}`;

  const W = SCREEN_W - 40;
  const H = W / 0.62;
  const CR = 20;
  const PERF_Y = H * 0.75;
  const PAD = 24;

  return (
    <View style={s.container}>
      <View style={s.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={s.hdrBtn}>
          <Ionicons name="arrow-back" size={22} color="white" />
        </TouchableOpacity>
        <Text style={s.hdrTitle}>E-TICKET</Text>
        <View style={s.hdrBtn} />
      </View>

      <ScrollView contentContainerStyle={s.scroll} showsVerticalScrollIndicator={false}>

        {/* ===== MAIN SECTION (top75%) ===== */}
        <View style={[s.card, { width: W, height: PERF_Y, borderRadius: CR }]}>
          <LinearGradient
            colors={['#ffc691', '#fe9046', '#ef671c']}
            start={{ x: 0.5, y: 0 }} end={{ x: 0, y: 1 }}
            style={[StyleSheet.absoluteFill, { borderRadius: CR }]}
          />
          <View style={[StyleSheet.absoluteFill, { borderRadius: CR, borderWidth: 1.5, borderColor: 'rgba(0,0,0,0.12)' }]} />

          {/* Notch cutouts */}
          <View style={[s.notch, { top: PERF_Y - 6, left: -6 }]} />
          <View style={[s.notch, { top: PERF_Y - 6, right: -6 }]} />

          <View style={s.mainContent}>
            <Ionicons name={getSportIcon(booking.facilityName)} size={28} color={INK} />
            <Text style={s.facilityName}>{booking.facilityName || '-'}</Text>
            <Text style={s.courtName}>{booking.courtName || '-'}</Text>

            <View style={s.divider} />

            <View style={s.detailGrid}>
              <View style={s.detailItem}>
                <Ionicons name="ticket-outline" size={16} color={INK} />
                <Text style={s.detailLabel}>Kode</Text>
                <Text style={s.detailValue} numberOfLines={1}>{code}</Text>
              </View>
              <View style={s.detailItem}>
                <Ionicons name="calendar-outline" size={16} color={INK} />
                <Text style={s.detailLabel}>Tanggal</Text>
                <Text style={s.detailValue} numberOfLines={1}>{booking.date || '-'}</Text>
              </View>
              <View style={s.detailItem}>
                <Ionicons name="time-outline" size={16} color={INK} />
                <Text style={s.detailLabel}>Jam</Text>
                <Text style={s.detailValue} numberOfLines={1}>{timeRange}</Text>
              </View>
              <View style={s.detailItem}>
                <Ionicons name="hourglass-outline" size={16} color={INK} />
                <Text style={s.detailLabel}>Durasi</Text>
                <Text style={s.detailValue}>{booking.totalHours || 0} Jam</Text>
              </View>

              <View style={s.divider} />

              <View style={s.detailItem}>
                <Ionicons name="wallet-outline" size={16} color={INK} />
                <Text style={s.detailLabel}>Remaining</Text>
                <Text style={s.detailValue}>Rp {((booking.totalAmount || 0) - (booking.dpAmount || 0)).toLocaleString('id-ID')}</Text>
              </View>
            </View>
          </View>
        </View>

        {/* ===== STUB SECTION (bottom25%) ===== */}
        <View style={[s.card, s.stubCard, { width: W, height: H - PERF_Y, borderRadius: CR }]}>
          <LinearGradient
            colors={['#ef671c', '#fe9046', '#ffc691']}
            start={{ x: 0, y: 0 }} end={{ x: 0.5, y: 1 }}
            style={[StyleSheet.absoluteFill, { borderRadius: CR }]}
          />
          <View style={[StyleSheet.absoluteFill, { borderRadius: CR, borderWidth: 1.5, borderColor: 'rgba(0,0,0,0.12)' }]} />

          {/* Notch cutouts */}
          <View style={[s.notch, { top: -6, left: -6 }]} />
          <View style={[s.notch, { top: -6, right: -6 }]} />

          <View style={s.stubContent}>
            <Image source={GSC_LOGO} style={s.logo} resizeMode="contain" />
            <Text style={s.stubCode}>{code}</Text>
          </View>
        </View>

        <Text style={s.noteTitle}>HARAP DI BACA</Text>
        <Text style={s.note}>
          Silahkan datang ke lokasi sesuai jam yang ada di E-ticket dan tunjukkan E-ticket ini ke kasir dan bayar sesuai nominal sisa yang ada di E-ticket.{'\n'}
          <Text style={s.noteBold}>Jadwal bermain sesuai dengan jam yang sudah di booking, tidak ada kompensasi tambahan waktu sesuai keterlambatan !!</Text>
        </Text>
      </ScrollView>
    </View>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0a0a0a' },
  header: {
    paddingTop: 56, paddingBottom: 12, paddingHorizontal: 16,
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
  },
  hdrBtn: { width: 38, height: 38, alignItems: 'center', justifyContent: 'center' },
  hdrTitle: { color: 'white', fontWeight: '800', fontSize: 18, letterSpacing: 2 },
  scroll: { padding: 16, paddingBottom: 40, alignItems: 'center', gap: 4 },

  card: { overflow: 'hidden', position: 'relative' },
  stubCard: {},

  notch: {
    position: 'absolute', width: 14, height: 14, borderRadius: 7,
    backgroundColor: '#0a0a0a', zIndex: 5,
  },

  mainContent: {
    flex: 1, paddingHorizontal: 22, paddingTop: 22, paddingBottom: 16,
    alignItems: 'flex-start',
  },
  facilityName: {
    color: INK, fontSize: 28, fontWeight: '900', lineHeight: 32,
    textTransform: 'uppercase', letterSpacing: -0.5, marginTop: 8,
  },
  courtName: {
    color: INK, fontSize: 16, fontWeight: '600', marginTop: 2,
    opacity: 0.7, textTransform: 'uppercase',
  },

  divider: {
    width: '100%', height: 1.5, backgroundColor: INK + '30',
    marginVertical: 14,
  },

  detailGrid: {
    width: '100%', gap: 12,
  },
  detailItem: {
    flexDirection: 'row', alignItems: 'center', gap: 10,
  },
  detailLabel: {
    color: INK, fontSize: 13, fontWeight: '600', opacity: 0.55, width: 55,
  },
  detailValue: {
    color: INK, fontSize: 15, fontWeight: '800', flex: 1,
  },

  stubContent: {
    flex: 1, alignItems: 'center', justifyContent: 'center', gap: 8,
  },
  logo: { width: 60, height: 60, opacity: 0.9 },
  stubCode: {
    color: INK, fontSize: 14, fontWeight: '800', letterSpacing: 2, opacity: 0.7,
  },

  noteTitle: { color: 'rgba(255,255,255,0.8)', fontSize: 13, fontWeight: '900', letterSpacing: 1.5, marginBottom: 8, marginTop: 20 },
  note: { color: 'rgba(255,255,255,0.5)', fontSize: 12, textAlign: 'center', lineHeight: 18 },
  noteBold: { color: 'rgba(255,255,255,0.85)', fontSize: 12, fontWeight: '900', lineHeight: 18 },
});
