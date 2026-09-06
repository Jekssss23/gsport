import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, Image, ActivityIndicator, RefreshControl, Dimensions, TouchableOpacity, ImageBackground } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { API_BASE_URL } from '../../config/api';
import { theme } from '../../styles/theme';
import AppErrorState from '../../components/AppErrorState';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';

const { width } = Dimensions.get('window');
const CARD_PADDING = 16;

const BRUTALIST = {
  bg: '#0a0a0a',
  cardBg: '#f5f5f0',
  yellow: '#f5e642',
  black: '#0a0a0a',
  border: 3,
  shadow: { shadowColor: '#0a0a0a', shadowOffset: { width: 4, height: 4 }, shadowOpacity: 1, shadowRadius: 0, elevation: 6 },
};

export default function EventScreen({ navigation }) {
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');

  const fetchEvents = async () => {
    try {
      setErrorMsg('');
      const res = await fetch(`${API_BASE_URL}/event/list`);
      const json = await res.json();
      if (!res.ok || !json.ok) throw new Error(json.message || 'Failed');
      setEvents(json.data || []);
    } catch (error) {
      setErrorMsg(getUserFriendlyErrorMessage(error, 'Gagal memuat event.'));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => { fetchEvents(); }, []);

  const onRefresh = async () => {
    setRefreshing(true);
    await fetchEvents();
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator color={BRUTALIST.yellow} size="large" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <View>
          <Text style={styles.headerTag}>GSC MOBILE</Text>
          <Text style={styles.headerTitle}>EVENTS</Text>
        </View>
        <View style={styles.headerBadge}>
          <Ionicons name="calendar" size={14} color={BRUTALIST.black} />
          <Text style={styles.headerBadgeText}>{events.length} EVENT</Text>
        </View>
      </View>

      {/* Content */}
      {errorMsg ? (
        <AppErrorState title="Event Error" message={errorMsg} onPress={fetchEvents} />
      ) : (
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={BRUTALIST.yellow} />}
        >
          {events.length === 0 ? (
            <View style={styles.emptyCard}>
              <View style={styles.emptyImageArea}>
                <Ionicons name="images-outline" size={48} color={BRUTALIST.black} />
              </View>
              <View style={styles.emptyBody}>
                <Text style={styles.emptyTag}>NO EVENTS</Text>
                <Text style={styles.emptyTitle}>Belum Ada Event</Text>
                <Text style={styles.emptyDesc}>Event terbaru akan muncul di sini</Text>
              </View>
            </View>
          ) : (
            events.map((ev) => (
              <View key={ev.id} style={styles.eventCard}>
                {/* Image - maintain aspect ratio */}
                <View style={styles.imageContainer}>
                  {ev.image_url ? (
                    <Image
                      source={{ uri: ev.image_url }}
                      style={styles.eventImage}
                      resizeMode="contain"
                    />
                  ) : (
                    <View style={styles.imagePlaceholder}>
                      <Ionicons name="image-outline" size={48} color={BRUTALIST.black} />
                    </View>
                  )}
                  {/* Date Badge */}
                  <View style={styles.dateBadge}>
                    <Text style={styles.dateDay}>{ev.start_date ? ev.start_date.split('-')[2] : '--'}</Text>
                    <Text style={styles.dateMonth}>
                      {ev.start_date ? new Date(ev.start_date + 'T00:00:00').toLocaleDateString('id-ID', { month: 'short' }).toUpperCase() : '--'}
                    </Text>
                  </View>
                </View>

                {/* Event Info */}
                <View style={styles.infoSection}>
                  <Text style={styles.eventTag}>@G-SPORTSCENTER</Text>
                  <Text style={styles.eventTitle}>{ev.name}</Text>

                  {ev.description ? (
                    <View style={styles.descRow}>
                      <View style={styles.descBorder} />
                      <Text style={styles.eventDesc}>{ev.description}</Text>
                    </View>
                  ) : null}

                  {/* Time Info */}
                  <View style={styles.timeRow}>
                    <View style={styles.timeItem}>
                      <Ionicons name="time-outline" size={14} color={BRUTALIST.black} />
                      <View>
                        <Text style={styles.timeLabel}>MULAI</Text>
                        <Text style={styles.timeValue}>{ev.start_time || '--:--'}</Text>
                      </View>
                    </View>
                    <View style={styles.timeDivider} />
                    <View style={styles.timeItem}>
                      <Ionicons name="time-outline" size={14} color={BRUTALIST.black} />
                      <View>
                        <Text style={styles.timeLabel}>SELESAI</Text>
                        <Text style={styles.timeValue}>{ev.end_time || '--:--'}</Text>
                      </View>
                    </View>
                  </View>

                  {/* Date Range */}
                  <View style={styles.dateRange}>
                    <Ionicons name="calendar-outline" size={14} color={BRUTALIST.black} />
                    <Text style={styles.dateRangeText}>
                      {ev.start_date || '--'} - {ev.end_date || '--'}
                    </Text>
                  </View>
                </View>
              </View>
            ))
          )}
        </ScrollView>
      )}

      {/* Back Button - Bottom Center */}
      <View style={styles.bottomBar}>
        <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()} activeOpacity={0.8}>
          <Ionicons name="arrow-back" size={18} color={BRUTALIST.yellow} />
          <Text style={styles.backText}>KEMBALI</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: BRUTALIST.bg },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: BRUTALIST.bg },

  header: {
    paddingTop: 60,
    paddingBottom: 14,
    paddingHorizontal: CARD_PADDING,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-end',
    backgroundColor: BRUTALIST.black,
    borderBottomWidth: BRUTALIST.border,
    borderBottomColor: BRUTALIST.black,
  },
  headerTag: {
    color: '#888',
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 2,
    textTransform: 'uppercase',
    marginBottom: 2,
  },
  headerTitle: {
    color: '#ffffff',
    fontSize: 22,
    fontWeight: '900',
    letterSpacing: -0.5,
  },
  headerBadge: {
    backgroundColor: BRUTALIST.yellow,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    paddingHorizontal: 10,
    paddingVertical: 5,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  headerBadgeText: {
    color: BRUTALIST.black,
    fontSize: 10,
    fontWeight: '900',
    letterSpacing: 1,
  },

  scrollContent: {
    padding: CARD_PADDING,
    paddingBottom: 100,
    gap: 16,
  },

  emptyCard: {
    backgroundColor: BRUTALIST.cardBg,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    ...BRUTALIST.shadow,
  },
  emptyImageArea: {
    width: '100%',
    height: 180,
    backgroundColor: BRUTALIST.yellow,
    justifyContent: 'center',
    alignItems: 'center',
    borderBottomWidth: BRUTALIST.border,
    borderBottomColor: BRUTALIST.black,
  },
  emptyBody: { padding: 16 },
  emptyTag: { color: '#888', fontSize: 9, fontWeight: '700', letterSpacing: 2, marginBottom: 4 },
  emptyTitle: { color: BRUTALIST.black, fontSize: 18, fontWeight: '900', marginBottom: 4 },
  emptyDesc: { color: '#666', fontSize: 12, lineHeight: 17 },

  eventCard: {
    backgroundColor: BRUTALIST.cardBg,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    ...BRUTALIST.shadow,
  },

  imageContainer: {
    width: '100%',
    backgroundColor: BRUTALIST.yellow,
    borderBottomWidth: BRUTALIST.border,
    borderBottomColor: BRUTALIST.black,
    position: 'relative',
    minHeight: 180,
  },
  eventImage: {
    width: '100%',
    aspectRatio: 16 / 9,
  },
  imagePlaceholder: {
    width: '100%',
    height: 180,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: BRUTALIST.yellow,
  },
  dateBadge: {
    position: 'absolute',
    top: 10,
    right: 10,
    backgroundColor: BRUTALIST.black,
    borderWidth: 2,
    borderColor: BRUTALIST.black,
    paddingHorizontal: 10,
    paddingVertical: 6,
    alignItems: 'center',
  },
  dateDay: {
    color: BRUTALIST.yellow,
    fontSize: 18,
    fontWeight: '900',
    lineHeight: 20,
  },
  dateMonth: {
    color: 'rgba(245, 230, 66, 0.8)',
    fontSize: 9,
    fontWeight: '800',
    letterSpacing: 1.5,
  },

  infoSection: {
    padding: 16,
  },
  eventTag: {
    color: '#999',
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 2,
    textTransform: 'uppercase',
    marginBottom: 4,
  },
  eventTitle: {
    color: BRUTALIST.black,
    fontSize: 20,
    fontWeight: '900',
    lineHeight: 24,
    letterSpacing: -0.3,
    marginBottom: 12,
  },
  descRow: {
    flexDirection: 'row',
    marginBottom: 14,
    gap: 10,
  },
  descBorder: {
    width: 4,
    backgroundColor: BRUTALIST.yellow,
    borderRadius: 2,
  },
  eventDesc: {
    flex: 1,
    color: '#333',
    fontSize: 13,
    lineHeight: 19,
  },

  timeRow: {
    flexDirection: 'row',
    borderTopWidth: BRUTALIST.border,
    borderTopColor: BRUTALIST.black,
  },
  timeItem: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 12,
    gap: 8,
  },
  timeDivider: {
    width: BRUTALIST.border,
    backgroundColor: BRUTALIST.black,
  },
  timeLabel: {
    color: '#888',
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  timeValue: {
    color: BRUTALIST.black,
    fontSize: 14,
    fontWeight: '900',
  },

  dateRange: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#ddd',
  },
  dateRangeText: {
    color: '#333',
    fontSize: 12,
    fontWeight: '600',
  },

  bottomBar: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    paddingHorizontal: CARD_PADDING,
    paddingBottom: 30,
    paddingTop: 16,
    backgroundColor: 'rgba(10, 10, 10, 0.95)',
    borderTopWidth: BRUTALIST.border,
    borderTopColor: BRUTALIST.black,
    alignItems: 'center',
  },
  backButton: {
    backgroundColor: BRUTALIST.black,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.yellow,
    paddingVertical: 12,
    paddingHorizontal: 40,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    ...BRUTALIST.shadow,
  },
  backText: {
    color: BRUTALIST.yellow,
    fontSize: 12,
    fontWeight: '900',
    letterSpacing: 2,
  },
});
