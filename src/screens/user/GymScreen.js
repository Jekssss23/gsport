import React from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, StatusBar } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../../styles/theme';

const GYM_MENUS = [
  { key: 'GymTutorial', icon: 'book-outline', title: 'Tutorial Alat Gym', desc: 'Panduan pakai alat gym' },
  { key: 'GymCalories', icon: 'flame-outline', title: 'Count Calories', desc: 'Hitung kalori latihan' },
  { key: 'GymTrainer', icon: 'person-outline', title: 'Personal Trainer', desc: 'Latihan bersama trainer' },
  { key: 'GymActivities', icon: 'pulse-outline', title: 'Activities', desc: 'Aktivitas gym hari ini' },
];

export default function GymScreen({ navigation }) {
  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <LinearGradient
        colors={[theme.colors.background, '#000000']}
        style={StyleSheet.absoluteFill}
      />

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        <View style={styles.header}>
          <Text style={styles.headerTag}>GSC MOBILE</Text>
          <Text style={styles.headerTitle}>Gym</Text>
          <Text style={styles.headerSubtitle}>Pusat kebugaran G-Sports Center</Text>
        </View>
        <View style={styles.grid}>
          {GYM_MENUS.map((item) => (
            <TouchableOpacity
              key={item.key}
              style={styles.menuCard}
              activeOpacity={0.85}
              onPress={() => navigation.navigate(item.key)}
            >
              <LinearGradient
                colors={theme.gradients.premium}
                style={styles.menuGradient}
              >
                <View style={styles.menuIconWrap}>
                  <Ionicons name={item.icon} size={24} color={theme.colors.primary} />
                </View>
                <Text style={styles.menuTitle}>{item.title}</Text>
                <Text style={styles.menuDesc}>{item.desc}</Text>
                <View style={styles.menuArrow}>
                  <Ionicons name="arrow-forward" size={14} color="#fff" />
                </View>
              </LinearGradient>
            </TouchableOpacity>
          ))}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  scrollContent: {
    padding: 20,
    paddingBottom: 120,
  },
  header: {
    marginTop: 20,
    marginBottom: 25,
  },
  headerTag: {
    color: theme.colors.textTertiary,
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 3,
    textTransform: 'uppercase',
    marginBottom: 5,
  },
  headerTitle: {
    color: '#ffffff',
    fontSize: 30,
    fontWeight: '900',
    letterSpacing: -0.5,
    marginBottom: 4,
  },
  headerSubtitle: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  heroCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 20,
    borderRadius: theme.borderRadius.large,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    marginBottom: 25,
    overflow: 'hidden',
    ...theme.shadows.heavy,
  },
  heroIconWrap: {
    width: 56,
    height: 56,
    borderRadius: 28,
    backgroundColor: 'rgba(230, 0, 0, 0.15)',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: 'rgba(230, 0, 0, 0.4)',
    marginRight: 16,
  },
  heroBody: {
    flex: 1,
  },
  heroTitle: {
    color: '#ffffff',
    fontSize: 18,
    fontWeight: '900',
    letterSpacing: 1,
    marginBottom: 5,
  },
  heroDesc: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    lineHeight: 17,
  },
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 14,
  },
  menuCard: {
    width: '47%',
    flexGrow: 1,
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    ...theme.shadows.medium,
  },
  menuGradient: {
    padding: 16,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    minHeight: 150,
  },
  menuIconWrap: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(230, 0, 0, 0.12)',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  menuTitle: {
    color: '#ffffff',
    fontSize: 14,
    fontWeight: '800',
    lineHeight: 18,
    marginBottom: 4,
  },
  menuDesc: {
    color: theme.colors.textSecondary,
    fontSize: 11,
    lineHeight: 15,
    flexShrink: 1,
  },
  menuArrow: {
    position: 'absolute',
    right: 12,
    bottom: 12,
    width: 24,
    height: 24,
    borderRadius: 12,
    backgroundColor: theme.colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
  },
});