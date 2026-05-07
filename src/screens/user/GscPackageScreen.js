import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, Dimensions, ActivityIndicator, Image } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import QRCode from 'react-native-qrcode-svg';
import { auth, db } from '../../config/firebase';
import { collection, query, where, onSnapshot } from 'firebase/firestore';
import { theme } from '../../styles/theme';
import { StatusBar } from 'expo-status-bar';

const { width } = Dimensions.get('window');

export default function GscPackageScreen({ navigation }) {
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState([]);

  useEffect(() => {
    if (!auth.currentUser) return;
    
    const q = query(
      collection(db, 'gsc_packages'),
      where('userId', '==', auth.currentUser.uid)
    );

    const unsubscribe = onSnapshot(q, (snapshot) => {
      const packageList = snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
      packageList.sort((a, b) => (b.createdAt?.toDate() || 0) - (a.createdAt?.toDate() || 0));
      setData(packageList);
      setLoading(false);
    });

    return unsubscribe;
  }, []);

  const renderPackageItem = ({ item }) => {
    const progress = item.remainingHours / item.totalHours;
    
    return (
      <View style={styles.cardContainer}>
        <LinearGradient
          colors={theme.gradients.premium}
          style={styles.card}
        >
          <View style={styles.cardHeader}>
            <View>
              <Text style={styles.sportType}>{item.sportType.toUpperCase()}</Text>
              <Text style={styles.packageId}>Kode paket disembunyikan untuk privasi</Text>
            </View>
            <View style={[styles.statusBadge, { backgroundColor: item.status === 'active' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }]}>
              <View style={[styles.statusDot, { backgroundColor: item.status === 'active' ? '#10b981' : '#f59e0b' }]} />
              <Text style={[styles.statusText, { color: item.status === 'active' ? '#10b981' : '#f59e0b' }]}>{item.status.toUpperCase()}</Text>
            </View>
          </View>

          <View style={styles.hoursSection}>
            <View style={styles.hourInfo}>
              <View>
                <Text style={styles.hourLabel}>Remaining</Text>
                <Text style={styles.hourValue}>{item.remainingHours} <Text style={styles.hourUnit}>Hrs</Text></Text>
              </View>
              <View style={styles.divider} />
              <View>
                <Text style={styles.hourLabel}>Total</Text>
                <Text style={styles.totalValue}>{item.totalHours} Hrs</Text>
              </View>
            </View>
            
            <View style={styles.progressBackground}>
              <LinearGradient
                colors={theme.gradients.primary}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 0 }}
                style={[styles.progressBar, { width: `${progress * 100}%` }]}
              />
            </View>
          </View>

          {item.status === 'active' && (
            <TouchableOpacity style={styles.qrToggle}>
              <LinearGradient
                colors={['rgba(255,255,255,0.05)', 'rgba(255,255,255,0.01)']}
                style={styles.qrContainer}
              >
                <Text style={styles.qrLabel}>SCAN TO USE</Text>
                <View style={styles.qrWrapper}>
                  <QRCode
                    value={item.id}
                    size={120}
                    color="black"
                    backgroundColor="transparent"
                  />
                </View>
                <Text style={styles.qrHint}>Present this to the staff</Text>
              </LinearGradient>
            </TouchableOpacity>
          )}
        </LinearGradient>
      </View>
    );
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <StatusBar style="light" />
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <LinearGradient
        colors={[theme.colors.background, '#000000']}
        style={StyleSheet.absoluteFill}
      />
      
      <FlatList
        data={data}
        keyExtractor={(item) => item.id}
        renderItem={renderPackageItem}
        contentContainerStyle={styles.listContent}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={
          <View style={styles.header}>
            <Text style={styles.headerTitle}>My Packages</Text>
            <Text style={styles.headerSubtitle}>Manage your active GSC sports packages</Text>
          </View>
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <View style={styles.emptyIconCircle}>
              <Ionicons name="cube-outline" size={40} color={theme.colors.textTertiary} />
            </View>
            <Text style={styles.emptyText}>No Active Packages</Text>
            <Text style={styles.emptySubtext}>Purchase a package to enjoy premium sports facilities at better rates.</Text>
            <TouchableOpacity 
              style={styles.emptyButton}
              onPress={() => navigation.navigate('BuyPackage')}
            >
              <Text style={styles.emptyButtonText}>Explore Packages</Text>
            </TouchableOpacity>
          </View>
        }
      />

      <TouchableOpacity 
        style={styles.fab}
        activeOpacity={0.9}
        onPress={() => navigation.navigate('BuyPackage')}
      >
        <LinearGradient
          colors={theme.gradients.primary}
          style={styles.fabGradient}
        >
          <Ionicons name="add" size={32} color="white" />
        </LinearGradient>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: theme.colors.background,
  },
  header: {
    marginBottom: 25,
    marginTop: 20,
  },
  headerTitle: {
    color: 'white',
    fontSize: 28,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  headerSubtitle: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  listContent: {
    padding: 25,
    paddingBottom: 120,
  },
  cardContainer: {
    marginBottom: 25,
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    ...theme.shadows.heavy,
  },
  card: {
    padding: 24,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 25,
  },
  sportType: {
    color: 'white',
    fontSize: 20,
    fontWeight: '900',
    letterSpacing: 1,
  },
  packageId: {
    color: theme.colors.textTertiary,
    fontSize: 10,
    marginTop: 4,
    letterSpacing: 1,
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
  },
  statusDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    marginRight: 8,
  },
  statusText: {
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 1,
  },
  hoursSection: {
    marginBottom: 25,
  },
  hourInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 15,
  },
  divider: {
    width: 1,
    height: 30,
    backgroundColor: 'rgba(255,255,255,0.1)',
    marginHorizontal: 25,
  },
  hourLabel: {
    color: theme.colors.textSecondary,
    fontSize: 11,
    textTransform: 'uppercase',
    letterSpacing: 1,
    marginBottom: 5,
  },
  hourValue: {
    color: 'white',
    fontSize: 32,
    fontWeight: 'bold',
  },
  hourUnit: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    fontWeight: 'normal',
  },
  totalValue: {
    color: theme.colors.textSecondary,
    fontSize: 18,
    fontWeight: '600',
  },
  progressBackground: {
    height: 6,
    backgroundColor: 'rgba(255,255,255,0.05)',
    borderRadius: 3,
    overflow: 'hidden',
  },
  progressBar: {
    height: '100%',
    borderRadius: 3,
  },
  qrToggle: {
    marginTop: 10,
  },
  qrContainer: {
    alignItems: 'center',
    padding: 20,
    borderRadius: theme.borderRadius.medium,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
  },
  qrLabel: {
    color: theme.colors.textSecondary,
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 3,
    marginBottom: 20,
  },
  qrWrapper: {
    padding: 15,
    backgroundColor: 'white',
    borderRadius: 15,
    ...theme.shadows.medium,
  },
  qrHint: {
    color: theme.colors.textTertiary,
    fontSize: 11,
    marginTop: 20,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 60,
    paddingHorizontal: 40,
  },
  emptyIconCircle: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 25,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  emptyText: {
    color: 'white',
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 12,
  },
  emptySubtext: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    textAlign: 'center',
    lineHeight: 22,
    marginBottom: 30,
  },
  emptyButton: {
    paddingVertical: 12,
    paddingHorizontal: 25,
    borderRadius: theme.borderRadius.medium,
    backgroundColor: theme.colors.surface,
    borderWidth: 1,
    borderColor: theme.colors.primary,
  },
  emptyButtonText: {
    color: theme.colors.primary,
    fontSize: 14,
    fontWeight: 'bold',
  },
  fab: {
    position: 'absolute',
    bottom: 35,
    right: 25,
    width: 64,
    height: 64,
    borderRadius: 32,
    ...theme.shadows.heavy,
  },
  fabGradient: {
    flex: 1,
    borderRadius: 32,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: 'rgba(255,255,255,0.2)',
  },
});
