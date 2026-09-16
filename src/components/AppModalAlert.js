import React from 'react';
import { Modal, View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

export default function AppModalAlert({
  visible,
  title = 'Peringatan',
  message = '',
  type = 'error',
  buttonLabel = 'Tutup',
  onClose,
}) {
  const color = type === 'success' ? '#00C851' : type === 'warning' ? '#FFBB33' : '#E60000';
  const icon = type === 'success' ? 'checkmark-circle' : type === 'warning' ? 'warning' : 'alert-circle';

  return (
    <Modal transparent animationType="fade" visible={visible} onRequestClose={onClose}>
      <View style={styles.overlay}>
        <LinearGradient colors={['#171717', '#070707']} style={styles.card}>
          <View style={[styles.iconWrap, { borderColor: `${color}50` }]}>
            <Ionicons name={icon} size={30} color={color} />
          </View>
          <Text style={styles.title}>{title}</Text>
          <Text style={styles.message}>{message}</Text>
          <TouchableOpacity style={[styles.btn, { backgroundColor: color }]} onPress={onClose}>
            <Text style={styles.btnText}>{buttonLabel}</Text>
          </TouchableOpacity>
        </LinearGradient>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.65)',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 24,
  },
  card: {
    width: '100%',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
    padding: 20,
    alignItems: 'center',
  },
  iconWrap: {
    width: 58,
    height: 58,
    borderRadius: 29,
    borderWidth: 1,
    backgroundColor: 'rgba(255,255,255,0.03)',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 10,
  },
  title: { color: 'white', fontSize: 18, fontWeight: '800', marginBottom: 8, textAlign: 'center' },
  message: { color: 'rgba(255,255,255,0.8)', fontSize: 13, lineHeight: 19, textAlign: 'center', marginBottom: 16 },
  btn: { borderRadius: 10, paddingVertical: 10, paddingHorizontal: 20 },
  btnText: { color: 'white', fontWeight: '700', fontSize: 13 },
});

