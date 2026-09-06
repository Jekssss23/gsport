import React from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../../styles/theme';

const BRUTALIST = {
  bg: '#0a0a0a',
  cardBg: '#f5f5f0',
  yellow: '#f5e642',
  black: '#0a0a0a',
  border: 3,
  shadow: { shadowColor: '#0a0a0a', shadowOffset: { width: 4, height: 4 }, shadowOpacity: 1, shadowRadius: 0, elevation: 6 },
};

const GYM_MENUS = [
  { icon: 'book', title: 'Tutorial Alat GYM' },
  { icon: 'flame', title: 'Count Calories' },
  { icon: 'person', title: 'Personal Trainer' },
  { icon: 'pulse', title: 'Activities' },
];

export default function GymScreen({ navigation }) {
  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <View>
          <Text style={styles.headerTag}>GSC MOBILE</Text>
          <Text style={styles.headerTitle}>GYM</Text>
        </View>
        <View style={styles.headerBadge}>
          <Ionicons name="barbell" size={18} color={BRUTALIST.yellow} />
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.heroCard}>
          <Text style={styles.heroTag}>G-SPORTS CENTER</Text>
          <Text style={styles.heroTitle}>GYM CENTER</Text>
          <Text style={styles.heroDesc}>Latihan bebas & kelas gym untuk semua member</Text>
        </View>

        <View style={styles.grid}>
          {GYM_MENUS.map((item, i) => (
            <TouchableOpacity key={i} style={styles.menuCard} activeOpacity={0.85}>
              <View style={styles.menuIcon}>
                <Ionicons name={item.icon} size={26} color={BRUTALIST.black} />
              </View>
              <Text style={styles.menuTitle}>{item.title}</Text>
              <View style={styles.menuArrow}>
                <Ionicons name="arrow-forward" size={14} color={BRUTALIST.yellow} />
              </View>
            </TouchableOpacity>
          ))}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: BRUTALIST.bg },
  header: {
    paddingTop: 60,
    paddingBottom: 14,
    paddingHorizontal: 16,
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
    padding: 16,
    paddingBottom: 120,
    gap: 12,
  },
  heroCard: {
    backgroundColor: BRUTALIST.yellow,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    padding: 16,
    ...BRUTALIST.shadow,
  },
  heroTag: {
    color: '#555',
    fontSize: 8,
    fontWeight: '700',
    letterSpacing: 2,
    textTransform: 'uppercase',
    marginBottom: 4,
  },
  heroTitle: {
    color: BRUTALIST.black,
    fontSize: 26,
    fontWeight: '900',
    letterSpacing: -0.5,
    marginBottom: 4,
  },
  heroDesc: {
    color: '#333',
    fontSize: 11,
    fontWeight: '600',
  },
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  menuCard: {
    width: '47%',
    flexGrow: 1,
    backgroundColor: BRUTALIST.cardBg,
    borderWidth: BRUTALIST.border,
    borderColor: BRUTALIST.black,
    padding: 12,
    ...BRUTALIST.shadow,
  },
  menuIcon: {
    width: 46,
    height: 46,
    backgroundColor: BRUTALIST.yellow,
    borderWidth: 2,
    borderColor: BRUTALIST.black,
    borderRadius: 0,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 10,
  },
  menuTitle: {
    color: BRUTALIST.black,
    fontSize: 13,
    fontWeight: '900',
    lineHeight: 17,
    flexShrink: 1,
  },
  menuArrow: {
    marginTop: 10,
    alignSelf: 'flex-end',
    backgroundColor: BRUTALIST.black,
    width: 24,
    height: 24,
    borderRadius: 0,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 2,
    borderColor: BRUTALIST.black,
  },
});