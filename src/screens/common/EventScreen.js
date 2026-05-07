import React, { useEffect, useState, useRef } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Image, ActivityIndicator, RefreshControl, Dimensions } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { API_BASE_URL } from '../../config/api';
import { theme } from '../../styles/theme';
import AppErrorState from '../../components/AppErrorState';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';

const { width } = Dimensions.get('window');

export default function EventScreen({ navigation }) {
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');
  const sliderRef = useRef(null);
  const currentIndexRef = useRef(0);

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

  useEffect(() => {
    fetchEvents();
  }, []);

  useEffect(() => {
    if (!events.length || !sliderRef.current) return;
    const timer = setInterval(() => {
      if (!sliderRef.current || events.length === 0) return;
      currentIndexRef.current = (currentIndexRef.current + 1) % events.length;
      sliderRef.current.scrollTo({ x: currentIndexRef.current * width, animated: true });
    }, 3000);
    return () => clearInterval(timer);
  }, [events]);

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <LinearGradient colors={[theme.colors.background, '#000']} style={StyleSheet.absoluteFill} />
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Ionicons name="arrow-back" size={22} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Event</Text>
        <View style={styles.backBtn} />
      </View>

      {errorMsg ? (
        <AppErrorState title="Event Error" message={errorMsg} onPress={fetchEvents} />
      ) : (
        <ScrollView
          ref={sliderRef}
          horizontal
          pagingEnabled
          showsHorizontalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); fetchEvents(); }} />}
          contentContainerStyle={styles.sliderWrap}
        >
          {events.length === 0 ? (
            <View style={[styles.slide, styles.emptySlide]}>
              <Ionicons name="images-outline" size={54} color={theme.colors.textTertiary} />
              <Text style={styles.emptyText}>Belum ada event aktif.</Text>
            </View>
          ) : (
            events.map((ev) => (
              <View key={ev.id} style={styles.slide}>
                <Image source={{ uri: ev.image_url }} style={styles.image} resizeMode="cover" />
                <LinearGradient colors={['transparent', 'rgba(0,0,0,0.88)']} style={styles.caption}>
                  <Text style={styles.title}>{ev.name}</Text>
                  <Text style={styles.sub}>
                    {ev.start_date} {ev.start_time} - {ev.end_date} {ev.end_time}
                  </Text>
                </LinearGradient>
              </View>
            ))
          )}
        </ScrollView>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#000' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: theme.colors.background },
  header: {
    paddingTop: 56,
    paddingBottom: 10,
    paddingHorizontal: 16,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  backBtn: { width: 38, height: 38, alignItems: 'center', justifyContent: 'center' },
  headerTitle: { color: 'white', fontSize: 20, fontWeight: '800' },
  sliderWrap: { paddingHorizontal: 0 },
  slide: { width, height: '100%', position: 'relative' },
  image: { width: '100%', height: '100%' },
  caption: { position: 'absolute', left: 0, right: 0, bottom: 0, padding: 18 },
  title: { color: 'white', fontSize: 22, fontWeight: '800', marginBottom: 6 },
  sub: { color: 'rgba(255,255,255,0.86)', fontSize: 13 },
  emptySlide: { justifyContent: 'center', alignItems: 'center' },
  emptyText: { color: theme.colors.textSecondary, marginTop: 10 },
});

