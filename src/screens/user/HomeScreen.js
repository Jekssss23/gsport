import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, Image, ActivityIndicator, RefreshControl, Dimensions, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { API_BASE_URL } from '../../config/api';
import { theme } from '../../styles/theme';

const { width } = Dimensions.get('window');
const CARD_PADDING = 16;
const GAP = 12;
const CARD_WIDTH = (width - CARD_PADDING * 2 - GAP) / 2;
const IMAGE_HEIGHT = 100;

const BRUTALIST = {
  bg: '#0a0a0a',
  cardBg: '#f5f5f0',
  yellow: '#f5e642',
  black: '#0a0a0a',
  border: 3,
  shadow: { shadowColor: '#0a0a0a', shadowOffset: { width: 4, height: 4 }, shadowOpacity: 1, shadowRadius: 0, elevation: 6 },
};

function RealtimeClock() {
  const [time, setTime] = useState(new Date());

  useEffect(() => {
    const timer = setInterval(() => setTime(new Date()), 1000);
    return () => clearInterval(timer);
  }, []);

  const hours = String(time.getHours()).padStart(2, '0');
  const minutes = String(time.getMinutes()).padStart(2, '0');
  const seconds = String(time.getSeconds()).padStart(2, '0');

  return (
    <View style={clockStyles.container}>
      <Text style={clockStyles.time}>{hours}</Text>
      <Text style={clockStyles.colon}>:</Text>
      <Text style={clockStyles.time}>{minutes}</Text>
      <Text style={clockStyles.colon}>:</Text>
      <Text style={clockStyles.time}>{seconds}</Text>
    </View>
  );
}

const clockStyles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: BRUTALIST.yellow,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    paddingHorizontal: 10,
    paddingVertical: 6,
  },
  time: {
    color: BRUTALIST.black,
    fontSize: 16,
    fontWeight: '900',
    fontVariant: ['tabular-nums'],
    letterSpacing: 1,
  },
  colon: {
    color: BRUTALIST.black,
    fontSize: 16,
    fontWeight: '900',
    marginHorizontal: 1,
  },
});

export default function HomeScreen({ navigation }) {
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchEvents = async () => {
    try {
      const res = await fetch(`${API_BASE_URL}/event/list`);
      const json = await res.json();
      if (res.ok && json.ok) setEvents(json.data || []);
    } catch (e) {
      console.error('Error fetching events:', e);
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
      <View style={styles.header}>
        <View>
          <Text style={styles.headerTag}>GSC MOBILE</Text>
          <Text style={styles.headerTitle}>HOME</Text>
        </View>
        <RealtimeClock />
        <TouchableOpacity style={styles.notifBtn} onPress={() => navigation.navigate('Notifications')}>
          <Ionicons name="notifications-outline" size={18} color="white" />
        </TouchableOpacity>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={BRUTALIST.yellow} />}
      >
        {events.length === 0 ? (
          <View style={styles.emptyCard}>
            <View style={styles.emptyImageArea}>
              <Ionicons name="calendar-outline" size={40} color={BRUTALIST.black} />
            </View>
            <View style={styles.emptyBody}>
              <Text style={styles.emptyTag}>NO EVENTS</Text>
              <Text style={styles.emptyTitle}>Belum Ada Event</Text>
              <Text style={styles.emptyDesc}>Event terbaru akan muncul di sini</Text>
            </View>
          </View>
        ) : (
          <View style={styles.grid}>
            {events.map((ev) => (
              <TouchableOpacity
                key={ev.id}
                style={styles.eventCard}
                activeOpacity={0.85}
                onPress={() => navigation.navigate('EventDetail', { event: ev })}
              >
                {/* Image Area */}
                <View style={styles.imageArea}>
                  {ev.image_url ? (
                    <Image source={{ uri: ev.image_url }} style={styles.eventImage} resizeMode="cover" />
                  ) : (
                    <View style={styles.imagePlaceholder}>
                      <Ionicons name="image-outline" size={28} color={BRUTALIST.black} />
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

                {/* Body Section */}
                <View style={styles.cardBody}>
                  <Text style={styles.cardTag}>@G-SPORTSCENTER</Text>
                  <Text style={styles.cardTitle} numberOfLines={2}>{ev.name}</Text>

                  {/* Stats Row */}
                  <View style={styles.statsRow}>
                    <View style={styles.statItem}>
                      <Ionicons name="time-outline" size={11} color={BRUTALIST.black} />
                      <Text style={styles.statValue}>{ev.start_time || '--:--'}</Text>
                    </View>
                    <View style={styles.statDivider} />
                    <View style={styles.statItem}>
                      <Ionicons name="time-outline" size={11} color={BRUTALIST.black} />
                      <Text style={styles.statValue}>{ev.end_time || '--:--'}</Text>
                    </View>
                  </View>

                  {/* CTA Button */}
                  <View style={styles.ctaButton}>
                    <Text style={styles.ctaText}>LIHAT</Text>
                  </View>
                </View>
              </TouchableOpacity>
            ))}
          </View>
        )}
      </ScrollView>
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
  notifBtn: {
    width: 36,
    height: 36,
    borderRadius: 0,
    backgroundColor: theme.colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    ...BRUTALIST.shadow,
  },

  scrollContent: {
    padding: CARD_PADDING,
    paddingBottom: 120,
  },

  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: GAP,
  },

  emptyCard: {
    width: CARD_WIDTH,
    backgroundColor: BRUTALIST.cardBg,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    ...BRUTALIST.shadow,
  },
  emptyImageArea: {
    width: '100%',
    height: IMAGE_HEIGHT,
    backgroundColor: BRUTALIST.yellow,
    justifyContent: 'center',
    alignItems: 'center',
    borderBottomWidth: BRUTALIST.border,
    borderBottomColor: BRUTALIST.black,
  },
  emptyBody: { padding: 10 },
  emptyTag: { color: '#888', fontSize: 8, fontWeight: '700', letterSpacing: 2, marginBottom: 2 },
  emptyTitle: { color: BRUTALIST.black, fontSize: 14, fontWeight: '900', marginBottom: 2 },
  emptyDesc: { color: '#666', fontSize: 10, lineHeight: 14 },

  eventCard: {
    width: CARD_WIDTH,
    backgroundColor: BRUTALIST.cardBg,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    ...BRUTALIST.shadow,
  },

  imageArea: {
    width: '100%',
    height: IMAGE_HEIGHT,
    backgroundColor: BRUTALIST.yellow,
    borderBottomWidth: BRUTALIST.border,
    borderBottomColor: BRUTALIST.black,
    position: 'relative',
    overflow: 'hidden',
  },
  eventImage: {
    width: '100%',
    height: '100%',
  },
  imagePlaceholder: {
    width: '100%',
    height: '100%',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: BRUTALIST.yellow,
  },
  dateBadge: {
    position: 'absolute',
    bottom: 6,
    right: 6,
    backgroundColor: BRUTALIST.black,
    borderWidth: 2,
    borderColor: BRUTALIST.black,
    paddingHorizontal: 8,
    paddingVertical: 4,
    alignItems: 'center',
  },
  dateDay: {
    color: BRUTALIST.yellow,
    fontSize: 16,
    fontWeight: '900',
    lineHeight: 18,
  },
  dateMonth: {
    color: 'rgba(245, 230, 66, 0.8)',
    fontSize: 8,
    fontWeight: '800',
    letterSpacing: 1.5,
  },

  cardBody: {
    padding: 10,
  },
  cardTag: {
    color: '#999',
    fontSize: 7,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
    marginBottom: 2,
  },
  cardTitle: {
    color: BRUTALIST.black,
    fontSize: 13,
    fontWeight: '900',
    lineHeight: 16,
    letterSpacing: -0.3,
    marginBottom: 8,
  },

  statsRow: {
    flexDirection: 'row',
    borderTopWidth: BRUTALIST.border,
    borderTopColor: BRUTALIST.black,
  },
  statItem: {
    flex: 1,
    alignItems: 'center',
    paddingVertical: 6,
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 4,
  },
  statDivider: {
    width: BRUTALIST.border,
    backgroundColor: BRUTALIST.black,
  },
  statValue: {
    color: BRUTALIST.black,
    fontSize: 10,
    fontWeight: '900',
  },

  ctaButton: {
    backgroundColor: BRUTALIST.black,
    borderTopWidth: BRUTALIST.border,
    borderTopColor: BRUTALIST.black,
    paddingVertical: 7,
    alignItems: 'center',
  },
  ctaText: {
    color: BRUTALIST.yellow,
    fontSize: 10,
    fontWeight: '900',
    letterSpacing: 2,
  },
});
