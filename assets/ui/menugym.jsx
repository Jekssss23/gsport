import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../../src/styles/theme';

/**
 * MenuGymCard — kartu daftar menu (adaptasi dari assets/ui/menugym.jsx web design).
 * Props:
 *  - items: Array<{ key, icon, label }>
 *  - onSelect(key): dipanggil saat baris ditekan
 */
export function MenuGymCard({ items, onSelect }) {
  const [focused, setFocused] = useState(null);

  return (
    <View style={styles.card}>
      <View style={styles.list}>
        {items.map((item, i) => {
          const isFocused = focused === item.key;
          return (
            <TouchableOpacity
              key={item.key}
              style={styles.rowWrap}
              activeOpacity={0.9}
              onPress={() => {
                setFocused(item.key);
                onSelect && onSelect(item.key);
              }}
            >
              {isFocused ? (
                <LinearGradient
                  colors={theme.gradients.primary}
                  start={{ x: 0, y: 0 }}
                  end={{ x: 1, y: 0 }}
                  style={styles.row}
                >
                  <View style={[styles.iconWrap, styles.iconWrapActive]}>
                    <Ionicons name={item.icon} size={22} color="#fff" />
                  </View>
                  <Text style={[styles.label, styles.labelActive]}>{item.label}</Text>
                  <Ionicons name="chevron-forward" size={18} color="rgba(255,255,255,0.8)" />
                </LinearGradient>
              ) : (
                <View style={styles.row}>
                  <View style={styles.iconWrap}>
                    <Ionicons name={item.icon} size={22} color={theme.colors.primary} />
                  </View>
                  <Text style={styles.label}>{item.label}</Text>
                  <Ionicons name="chevron-forward" size={18} color={theme.colors.textTertiary} />
                </View>
              )}
              {i < items.length - 1 && <View style={styles.divider} />}
            </TouchableOpacity>
          );
        })}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.large,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    paddingHorizontal: 10,
    paddingVertical: 8,
    ...theme.shadows.medium,
  },
  list: {
    flexDirection: 'column',
  },
  rowWrap: {
    borderRadius: theme.borderRadius.round,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 12,
    gap: 12,
    borderRadius: theme.borderRadius.round,
    borderWidth: 2,
    borderColor: 'transparent',
  },
  iconWrap: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: 'rgba(230, 0, 0, 0.12)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  iconWrapActive: {
    backgroundColor: 'rgba(255,255,255,0.2)',
  },
  label: {
    flex: 1,
    color: '#ffffff',
    fontSize: 15,
    fontWeight: '700',
  },
  labelActive: {
    color: '#ffffff',
  },
  divider: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: theme.colors.glassBorder,
    marginHorizontal: 14,
    marginLeft: 62,
  },
});