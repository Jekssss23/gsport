import React from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, StatusBar } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../../../styles/theme';
import { MenuGymCard } from '../../../../assets/ui/menugym';

export function GymSubMenuHeader({ title, subtitle, onBack }) {
  return (
    <View style={styles.header}>
      <TouchableOpacity style={styles.backBtn} onPress={onBack} activeOpacity={0.8}>
        <Ionicons name="arrow-back" size={22} color="#fff" />
      </TouchableOpacity>
      <View style={styles.headerText}>
        <Text style={styles.headerTitle}>{title}</Text>
        {subtitle ? <Text style={styles.headerSubtitle}>{subtitle}</Text> : null}
      </View>
    </View>
  );
}

export function GymSubMenuScreen({ title, subtitle, items, onSelect, onBack }) {
  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <LinearGradient colors={[theme.colors.background, '#000000']} style={StyleSheet.absoluteFill} />
      <GymSubMenuHeader title={title} subtitle={subtitle} onBack={onBack} />
      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false}>
        <MenuGymCard items={items} onSelect={onSelect} />
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  scroll: {
    padding: 20,
    paddingBottom: 40,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: 55,
    paddingBottom: 16,
    paddingHorizontal: 20,
    backgroundColor: 'rgba(15,15,15,0.8)',
    borderBottomWidth: 1,
    borderBottomColor: theme.colors.glassBorder,
  },
  backBtn: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: theme.colors.surface,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    marginRight: 14,
  },
  headerText: {
    flex: 1,
  },
  headerTitle: {
    color: '#ffffff',
    fontSize: 20,
    fontWeight: '900',
    letterSpacing: 0.3,
  },
  headerSubtitle: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    marginTop: 2,
  },
});