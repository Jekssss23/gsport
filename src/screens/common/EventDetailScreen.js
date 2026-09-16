import React from 'react';
import { View, Text, StyleSheet, ScrollView, Image, Dimensions, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';

const { width } = Dimensions.get('window');
const SIDE_PADDING = 20;

const BRUTALIST = {
  bg: '#0a0a0a',
  cardBg: '#f5f5f0',
  yellow: '#f5e642',
  black: '#0a0a0a',
  border: 4,
  shadow: { shadowColor: '#0a0a0a', shadowOffset: { width: 6, height: 6 }, shadowOpacity: 1, shadowRadius: 0, elevation: 8 },
};

export default function EventDetailScreen({ navigation, route }) {
  const event = route.params?.event;

  if (!event) {
    return (
      <View style={styles.container}>
        <View style={styles.header}>
          <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
            <Ionicons name="arrow-back" size={20} color={BRUTALIST.black} />
          </TouchableOpacity>
        </View>
        <View style={styles.emptyContainer}>
          <Text style={styles.emptyText}>Event tidak ditemukan</Text>
        </View>
      </View>
    );
  }

  const formatDate = (dateStr) => {
    if (!dateStr) return '--';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <Ionicons name="arrow-back" size={20} color={BRUTALIST.black} />
        </TouchableOpacity>
        <Text style={styles.headerTag}>DETAIL EVENT</Text>
        <View style={styles.backBtn} />
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* Image Section */}
        <View style={styles.imageSection}>
          {event.image_url ? (
            <Image
              source={{ uri: event.image_url }}
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
            <Text style={styles.dateDay}>{event.start_date ? event.start_date.split('-')[2] : '--'}</Text>
            <Text style={styles.dateMonth}>
              {event.start_date ? new Date(event.start_date + 'T00:00:00').toLocaleDateString('id-ID', { month: 'short' }).toUpperCase() : '--'}
            </Text>
          </View>
        </View>

        {/* Info Card */}
        <View style={styles.infoCard}>
          <Text style={styles.eventTag}>@G-SPORTSCENTER</Text>
          <Text style={styles.eventTitle}>{event.name}</Text>

          {event.description ? (
            <View style={styles.descSection}>
              <View style={styles.descHeader}>
                <View style={styles.descDot} />
                <Text style={styles.descLabel}>DESKRIPSI</Text>
              </View>
              <Text style={styles.eventDesc}>{event.description}</Text>
            </View>
          ) : null}

          {/* Time Grid */}
          <View style={styles.timeGrid}>
            <View style={styles.timeBox}>
              <View style={styles.timeIconBox}>
                <Ionicons name="time" size={16} color={BRUTALIST.black} />
              </View>
              <Text style={styles.timeLabel}>MULAI</Text>
              <Text style={styles.timeValue}>{event.start_time || '--:--'}</Text>
            </View>
            <View style={styles.timeDivider} />
            <View style={styles.timeBox}>
              <View style={styles.timeIconBox}>
                <Ionicons name="time" size={16} color={BRUTALIST.black} />
              </View>
              <Text style={styles.timeLabel}>SELESAI</Text>
              <Text style={styles.timeValue}>{event.end_time || '--:--'}</Text>
            </View>
          </View>

          {/* Date Section */}
          <View style={styles.dateSection}>
            <View style={styles.dateItem}>
              <Ionicons name="calendar-outline" size={14} color={BRUTALIST.black} />
              <View>
                <Text style={styles.dateItemLabel}>MULAI</Text>
                <Text style={styles.dateItemValue}>{formatDate(event.start_date)}</Text>
              </View>
            </View>
            <View style={styles.dateDivider} />
            <View style={styles.dateItem}>
              <Ionicons name="calendar-outline" size={14} color={BRUTALIST.black} />
              <View>
                <Text style={styles.dateItemLabel}>SELESAI</Text>
                <Text style={styles.dateItemValue}>{formatDate(event.end_date)}</Text>
              </View>
            </View>
          </View>
        </View>
      </ScrollView>

      {/* Bottom Bar */}
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

  header: {
    paddingTop: 60,
    paddingBottom: 14,
    paddingHorizontal: SIDE_PADDING,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: BRUTALIST.cardBg,
    borderBottomWidth: BRUTALIST.border,
    borderBottomColor: BRUTALIST.black,
  },
  backBtn: {
    width: 38,
    height: 38,
    backgroundColor: BRUTALIST.yellow,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    justifyContent: 'center',
    alignItems: 'center',
    ...BRUTALIST.shadow,
  },
  headerTag: {
    color: BRUTALIST.black,
    fontSize: 12,
    fontWeight: '900',
    letterSpacing: 3,
  },

  scrollContent: {
    padding: SIDE_PADDING,
    paddingBottom: 100,
  },

  imageSection: {
    width: '100%',
    backgroundColor: BRUTALIST.yellow,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    ...BRUTALIST.shadow,
    position: 'relative',
    overflow: 'hidden',
    marginBottom: 16,
  },
  eventImage: {
    width: '100%',
    aspectRatio: 4 / 3,
  },
  imagePlaceholder: {
    width: '100%',
    height: 200,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: BRUTALIST.yellow,
  },
  dateBadge: {
    position: 'absolute',
    top: 12,
    right: 12,
    backgroundColor: BRUTALIST.black,
    borderWidth: 3,
    borderColor: BRUTALIST.black,
    paddingHorizontal: 12,
    paddingVertical: 8,
    alignItems: 'center',
  },
  dateDay: {
    color: BRUTALIST.yellow,
    fontSize: 22,
    fontWeight: '900',
    lineHeight: 24,
  },
  dateMonth: {
    color: 'rgba(245, 230, 66, 0.8)',
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 2,
  },

  infoCard: {
    backgroundColor: BRUTALIST.cardBg,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    padding: 20,
    ...BRUTALIST.shadow,
  },
  eventTag: {
    color: '#999',
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 2,
    textTransform: 'uppercase',
    marginBottom: 6,
  },
  eventTitle: {
    color: BRUTALIST.black,
    fontSize: 24,
    fontWeight: '900',
    lineHeight: 28,
    letterSpacing: -0.5,
    marginBottom: 16,
  },

  descSection: {
    marginBottom: 16,
  },
  descHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 8,
  },
  descDot: {
    width: 8,
    height: 8,
    backgroundColor: BRUTALIST.yellow,
    borderWidth: 2,
    borderColor: BRUTALIST.black,
  },
  descLabel: {
    color: '#888',
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 2,
  },
  eventDesc: {
    color: '#333',
    fontSize: 14,
    lineHeight: 21,
    paddingLeft: 14,
    borderLeftWidth: 4,
    borderLeftColor: BRUTALIST.yellow,
  },

  timeGrid: {
    flexDirection: 'row',
    borderTopWidth: BRUTALIST.border,
    borderTopColor: BRUTALIST.black,
  },
  timeBox: {
    flex: 1,
    alignItems: 'center',
    paddingVertical: 16,
    gap: 6,
  },
  timeDivider: {
    width: BRUTALIST.border,
    backgroundColor: BRUTALIST.black,
  },
  timeIconBox: {
    width: 32,
    height: 32,
    backgroundColor: BRUTALIST.yellow,
    borderWidth: 2,
    borderColor: BRUTALIST.black,
    justifyContent: 'center',
    alignItems: 'center',
  },
  timeLabel: {
    color: '#888',
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 2,
  },
  timeValue: {
    color: BRUTALIST.black,
    fontSize: 18,
    fontWeight: '900',
  },

  dateSection: {
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 2,
    borderTopColor: '#ddd',
    gap: 12,
  },
  dateItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  dateItemLabel: {
    color: '#888',
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 1.5,
  },
  dateItemValue: {
    color: BRUTALIST.black,
    fontSize: 13,
    fontWeight: '700',
  },
  dateDivider: {
    height: 2,
    backgroundColor: '#eee',
  },

  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyText: {
    color: '#888',
    fontSize: 14,
  },

  bottomBar: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    paddingHorizontal: SIDE_PADDING,
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
