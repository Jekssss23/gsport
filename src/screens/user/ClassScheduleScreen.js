import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  ActivityIndicator,
  Image,
  TouchableOpacity,
  ScrollView
} from 'react-native';
import { collection, getDocs, query, orderBy } from 'firebase/firestore';
import { db } from '../../config/firebase';
import { theme } from '../../styles/theme';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

export default function ClassScheduleScreen() {
  const [classes, setClasses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [expandedClass, setExpandedClass] = useState(null);
  const [membersMap, setMembersMap] = useState({});

  const categories = [
    'Les renang',
    'Karate',
    'Aikido',
    'Silat',
    'Taekwondo',
    'Akademi Futsal'
  ];

  useEffect(() => {
    fetchClasses();
  }, []);

  const fetchClasses = async () => {
    try {
      setLoading(true);
      const q = query(collection(db, 'classes'), orderBy('category'));
      const snap = await getDocs(q);
      const data = snap.docs.map(doc => ({ id: doc.id, ...doc.data() }));
      setClasses(data);
    } catch (error) {
      console.error('Error fetching classes:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchMembers = async (classId) => {
    if (membersMap[classId]) return;
    try {
      const snap = await getDocs(collection(db, 'classes', classId, 'members'));
      const data = snap.docs.map(d => ({ id: d.id, ...d.data() }));
      setMembersMap(prev => ({ ...prev, [classId]: data }));
    } catch (e) {
      console.error(e);
    }
  };

  const toggleExpand = (classId) => {
    if (expandedClass === classId) {
      setExpandedClass(null);
    } else {
      setExpandedClass(classId);
      fetchMembers(classId);
    }
  };

  const renderClassItem = ({ item }) => (
    <View style={styles.classCard}>
      <TouchableOpacity
        onPress={() => toggleExpand(item.id)}
        activeOpacity={0.7}
      >
        <LinearGradient
          colors={['rgba(255,255,255,0.05)', 'rgba(255,255,255,0.02)']}
          style={styles.cardContent}
        >
          <View style={styles.cardHeader}>
            <View style={styles.coachInfo}>
              {item.coachImage ? (
                <Image source={{ uri: item.coachImage }} style={styles.coachAvatar} />
              ) : (
                <View style={[styles.coachAvatar, styles.placeholderAvatar]}>
                  <Ionicons name="person" size={20} color="#666" />
                </View>
              )}
              <View>
                <Text style={styles.className}>{item.name}</Text>
                <Text style={styles.coachName}>Coach: {item.coachName}</Text>
              </View>
            </View>
            <Ionicons
              name={expandedClass === item.id ? "chevron-up" : "chevron-down"}
              size={20}
              color={theme.colors.primary}
            />
          </View>

          <View style={styles.scheduleRow}>
            <View style={styles.scheduleBadge}>
              <Ionicons name="calendar-outline" size={14} color="white" />
              <Text style={styles.badgeText}>{item.days?.join(', ') || item.day || '-'}</Text>
            </View>
            <View style={[styles.scheduleBadge, { backgroundColor: 'rgba(255,0,0,0.1)' }]}>
              <Ionicons name="time-outline" size={14} color={theme.colors.primary} />
              <Text style={[styles.badgeText, { color: theme.colors.primary }]}>
                {item.startTime} - {item.endTime}
              </Text>
            </View>
          </View>

          {expandedClass === item.id && (
            <View style={styles.expandedContent}>
              <View style={styles.divider} />
              <Text style={styles.membersTitle}>Daftar Murid ({membersMap[item.id]?.length || 0})</Text>
              {membersMap[item.id] ? (
                membersMap[item.id].length > 0 ? (
                  membersMap[item.id].map(m => (
                    <View key={m.id} style={styles.memberItem}>
                      <View style={styles.memberMain}>
                        <Text style={styles.memberName}>{m.name}</Text>
                        <Text style={styles.memberSub}>{m.age} thn • {m.gender}</Text>
                      </View>
                      <Ionicons
                        name={m.gender === 'Laki-laki' ? 'male' : 'female'}
                        size={14}
                        color={m.gender === 'Laki-laki' ? '#00BFFF' : '#FF69B4'}
                      />
                    </View>
                  ))
                ) : (
                  <Text style={styles.emptyMembers}>Belum ada murid terdaftar.</Text>
                )
              ) : (
                <ActivityIndicator size="small" color={theme.colors.primary} style={{ marginVertical: 10 }} />
              )}
            </View>
          )}
        </LinearGradient>
      </TouchableOpacity>
    </View>
  );

  const renderCategory = (category) => {
    const categoryClasses = classes.filter(c => c.category === category);
    if (categoryClasses.length === 0) return null;

    return (
      <View key={category} style={styles.categorySection}>
        <View style={styles.categoryHeader}>
          <View style={styles.categoryDot} />
          <Text style={styles.categoryTitle}>{category}</Text>
        </View>
        {categoryClasses.map(item => renderClassItem({ item }))}
      </View>
    );
  };

  if (loading && classes.length === 0) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
        <Text style={styles.loadingText}>Memuat jadwal kelas...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <LinearGradient
        colors={[theme.colors.cardBackground, theme.colors.background]}
        style={styles.header}
      >
        <Text style={styles.headerTitle}>Jadwal Kelas & Les</Text>
        <Text style={styles.headerSub}>G-Sports Center Academy</Text>
      </LinearGradient>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {categories.map(cat => renderCategory(cat))}

        {classes.length === 0 && !loading && (
          <View style={styles.emptyContainer}>
            <Ionicons name="calendar-outline" size={60} color="#333" />
            <Text style={styles.emptyText}>Belum ada jadwal kelas yang tersedia.</Text>
          </View>
        )}
      </ScrollView>
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
  loadingText: {
    color: '#666',
    marginTop: 10,
  },
  header: {
    padding: 25,
    paddingTop: 50,
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: 'white',
  },
  headerSub: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginTop: 4,
  },
  scrollContent: {
    padding: 20,
    paddingTop: 10,
  },
  categorySection: {
    marginBottom: 25,
  },
  categoryHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 15,
  },
  categoryDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: theme.colors.primary,
    marginRight: 10,
  },
  categoryTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: 'white',
    letterSpacing: 0.5,
  },
  classCard: {
    marginBottom: 12,
    borderRadius: 15,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
  },
  cardContent: {
    padding: 15,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  coachInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  coachAvatar: {
    width: 40,
    height: 40,
    borderRadius: 20,
    marginRight: 12,
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.3)',
  },
  placeholderAvatar: {
    backgroundColor: '#222',
    justifyContent: 'center',
    alignItems: 'center',
  },
  className: {
    fontSize: 16,
    fontWeight: 'bold',
    color: 'white',
  },
  coachName: {
    fontSize: 12,
    color: theme.colors.textSecondary,
    marginTop: 2,
  },
  scheduleRow: {
    flexDirection: 'row',
    marginTop: 12,
    gap: 10,
  },
  scheduleBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.05)',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
    gap: 5,
  },
  badgeText: {
    fontSize: 11,
    color: theme.colors.text,
    fontWeight: '600',
  },
  expandedContent: {
    marginTop: 15,
  },
  divider: {
    height: 1,
    backgroundColor: 'rgba(255,255,255,0.05)',
    marginBottom: 12,
  },
  membersTitle: {
    fontSize: 12,
    fontWeight: 'bold',
    color: theme.colors.textSecondary,
    marginBottom: 10,
    textTransform: 'uppercase',
  },
  memberItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.02)',
    padding: 10,
    borderRadius: 8,
    marginBottom: 6,
  },
  memberMain: {
    flex: 1,
  },
  memberName: {
    fontSize: 13,
    color: 'white',
    fontWeight: '500',
  },
  memberSub: {
    fontSize: 11,
    color: '#666',
    marginTop: 2,
  },
  emptyMembers: {
    fontSize: 12,
    color: '#444',
    textAlign: 'center',
    paddingVertical: 5,
  },
  emptyContainer: {
    alignItems: 'center',
    marginTop: 100,
  },
  emptyText: {
    color: '#333',
    marginTop: 20,
    textAlign: 'center',
  },
});
