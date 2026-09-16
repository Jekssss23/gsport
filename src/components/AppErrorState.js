import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

export default function AppErrorState({
  title = 'Terjadi Gangguan',
  message = 'Ada masalah pada aplikasi. Coba lagi.',
  icon = 'alert-circle-outline',
  actionLabel = 'Coba Lagi',
  onPress,
}) {
  return (
    <View style={styles.container}>
      <LinearGradient colors={['#1A1A1A', '#0B0B0B']} style={styles.card}>
        <View style={styles.iconCircle}>
          <Ionicons name={icon} size={42} color="#ff2f2f" />
        </View>
        <Text style={styles.title}>{title}</Text>
        <Text style={styles.message}>{message}</Text>
        {onPress ? (
          <TouchableOpacity style={styles.button} onPress={onPress} activeOpacity={0.85}>
            <Text style={styles.buttonText}>{actionLabel}</Text>
          </TouchableOpacity>
        ) : null}
      </LinearGradient>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: 24,
  },
  card: {
    width: '100%',
    borderRadius: 18,
    padding: 24,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: 'rgba(255, 0, 0, 0.18)',
  },
  iconCircle: {
    width: 76,
    height: 76,
    borderRadius: 38,
    backgroundColor: 'rgba(255, 0, 0, 0.08)',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 14,
  },
  title: {
    color: 'white',
    fontSize: 20,
    fontWeight: '800',
    marginBottom: 8,
  },
  message: {
    color: 'rgba(255,255,255,0.75)',
    fontSize: 14,
    textAlign: 'center',
    lineHeight: 20,
    marginBottom: 18,
  },
  button: {
    backgroundColor: '#C50000',
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 10,
  },
  buttonText: {
    color: 'white',
    fontWeight: '700',
    fontSize: 13,
  },
});

