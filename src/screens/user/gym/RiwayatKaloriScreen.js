import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, TouchableOpacity } from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { collection, query, where, onSnapshot } from 'firebase/firestore';
import { auth, db } from '../../../config/firebase';
import { theme } from '../../../styles/theme';
import { GymSubMenuHeader } from './GymSubMenuScreen';

export default function RiwayatKaloriScreen({ navigation }) {
  const [loading, setLoading] = useState(true);
  const [records, setRecords] = useState([]);

  useEffect(() => {
    if (!auth.currentUser) return;

    const q = query(
      collection(db, 'calorie_history'),
      where('uid', '==', auth.currentUser.uid)
    );

    const unsubscribe = onSnapshot(q, (snapshot) => {
      const list = snapshot.docs.map((doc) => ({ id: doc.id, ...doc.data() }));
      list.sort((a, b) => (b.createdAt?.toDate?.() || 0) - (a.createdAt?.toDate?.() || 0));
      setRecords(list);
      setLoading(false);
    });

    return unsubscribe;
  }, []);

  const formatDate = (ts) => {
    if (!ts?.toDate) return '';
    const d = ts.toDate();
    const pad = (n) => String(n).padStart(2, '0');
    return `${pad(d.getDate())} ${['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][d.getMonth()]} ${d.getFullYear()} · ${pad(d.getHours())}:${pad(d.getMinutes())}`;
  };

  const renderItem = ({ item, index }) => (
    <View style={styles.card}>
      <View style={styles.cardHeaderRow}>
        <View style={styles.dateBadge}>
          <Ionicons name="restaurant-outline" size={16} color={theme.colors.primary} />
          <Text style={styles.dateText}>{formatDate(item.createdAt)}</Text>
        </View>
        <Text style={styles.cardIndex}>#{records.length - index}</Text>
      </View>

      <View style={styles.foods}>
        {(item.foods || []).map((f, i) => (
          <TouchableOpacity key={i} style={styles.chip} activeOpacity={0.8}>
            <Text style={styles.chipName} numberOfLines={1}>{f.name}</Text>
            <Text style={styles.chipCals}>{f.calories} kkal</Text>
          </TouchableOpacity>
        ))}
      </View>

      <View style={styles.cardFooter}>
        <Text style={styles.totalLabel}>TOTAL KALORI</Text>
        <Text style={styles.totalValue}>{item.totalCalories} <Text style={styles.totalUnit}>kkal</Text></Text>
      </View>
    </View>
  );

  if (loading) {
    return (
      <View style={styles.centered}>
        <StatusBar style="light" />
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <LinearGradient colors={[theme.colors.background, '#000000']} style={StyleSheet.absoluteFill} />
      <GymSubMenuHeader
        title="Riwayat Scan"
        subtitle="Catatan kalori makananmu"
        onBack={() => navigation.goBack()}
      />

      <FlatList
        data={records}
        keyExtractor={(item) => item.id}
        renderItem={renderItem}
        contentContainerStyle={styles.listContent}
        showsVerticalScrollIndicator={false}
        ListEmptyComponent={
          <View style={styles.empty}>
            <View style={styles.emptyIconCircle}>
              <Ionicons name="time-outline" size={40} color={theme.colors.textTertiary} />
            </View>
            <Text style={styles.emptyTitle}>Belum ada riwayat</Text>
            <Text style={styles.emptySub}>
              Scan makananmu untuk mulai mencatat kalori secara otomatis.
            </Text>
            <TouchableOpacity
              style={styles.emptyButton}
              onPress={() => navigation.navigate('GymScanCalories')}
            >
              <Text style={styles.emptyButtonText}>Scan Kalori</Text>
            </TouchableOpacity>
          </View>
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  centered: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: theme.colors.background,
  },
  listContent: {
    padding: 20,
    paddingBottom: 40,
  },
  card: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.large,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    padding: 18,
    marginBottom: 14,
  },
  cardHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 14,
  },
  dateBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  dateText: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    fontWeight: '600',
  },
  cardIndex: {
    color: theme.colors.textTertiary,
    fontSize: 12,
    fontWeight: '800',
  },
  foods: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
    marginBottom: 16,
  },
  chip: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(230,0,0,0.12)',
    borderWidth: 1,
    borderColor: 'rgba(230,0,0,0.35)',
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 8,
    gap: 8,
  },
  chipName: {
    color: '#fff',
    fontSize: 13,
    fontWeight: '700',
    maxWidth: 160,
  },
  chipCals: {
    color: theme.colors.primary,
    fontSize: 12,
    fontWeight: '800',
  },
  cardFooter: {
    flexDirection: 'row',
    alignItems: 'baseline',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    borderTopColor: theme.colors.glassBorder,
    paddingTop: 12,
  },
  totalLabel: {
    color: theme.colors.textTertiary,
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 2,
  },
  totalValue: {
    color: '#fff',
    fontSize: 22,
    fontWeight: '900',
  },
  totalUnit: {
    fontSize: 13,
    color: theme.colors.textSecondary,
    fontWeight: '700',
  },
  empty: {
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 60,
    paddingHorizontal: 36,
  },
  emptyIconCircle: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 22,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  emptyTitle: {
    color: '#fff',
    fontSize: 19,
    fontWeight: '900',
    marginBottom: 10,
  },
  emptySub: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    textAlign: 'center',
    lineHeight: 21,
    marginBottom: 26,
  },
  emptyButton: {
    paddingVertical: 13,
    paddingHorizontal: 28,
    borderRadius: theme.borderRadius.medium,
    backgroundColor: theme.colors.primary,
  },
  emptyButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '800',
  },
});