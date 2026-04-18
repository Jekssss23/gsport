import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, Alert, TouchableOpacity, ActivityIndicator } from 'react-native';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { Ionicons } from '@expo/vector-icons';
import { ClassScheduleService } from '../../services/ClassScheduleService';
import { theme } from '../../styles/theme';

export default function ClassAttendanceScanScreen({ navigation }) {
  const [permission, requestPermission] = useCameraPermissions();
  const [scanned, setScanned] = useState(false);
  const [busy, setBusy] = useState(false);

  const onBarcodeScanned = useCallback(
    async ({ data }) => {
      if (scanned || busy || !data) return;
      setBusy(true);
      setScanned(true);
      try {
        const result = await ClassScheduleService.attendanceCheckIn(data);
        const title = result.already ? 'Sudah absen' : 'Berhasil';
        const msg =
          result.message ||
          (result.data?.class_name
            ? `${result.data.class_name} — pertemuan ${result.data.session_number ?? ''}`
            : 'OK');
        Alert.alert(title, msg, [
          {
            text: 'OK',
            onPress: () => navigation.goBack(),
          },
        ]);
      } catch (e) {
        Alert.alert('Tidak bisa absen', e.message || String(e), [
          { text: 'Coba lagi', onPress: () => { setScanned(false); setBusy(false); } },
          { text: 'Tutup', style: 'cancel', onPress: () => navigation.goBack() },
        ]);
      } finally {
        setBusy(false);
      }
    },
    [scanned, busy, navigation]
  );

  if (!permission) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  if (!permission.granted) {
    return (
      <View style={styles.centered}>
        <Text style={styles.hint}>Kamera diperlukan untuk scan QR absensi.</Text>
        <TouchableOpacity style={styles.btn} onPress={requestPermission}>
          <Text style={styles.btnText}>Izinkan kamera</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[styles.btn, styles.btnGhost]} onPress={() => navigation.goBack()}>
          <Text style={styles.btnGhostText}>Batal</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <CameraView
        style={StyleSheet.absoluteFillObject}
        facing="back"
        barcodeScannerSettings={{ barcodeTypes: ['qr'] }}
        onBarcodeScanned={scanned ? undefined : onBarcodeScanned}
      />
      <View style={styles.overlayTop}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()} accessibilityLabel="Kembali">
          <Ionicons name="close" size={28} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.overlayTitle}>Arahkan ke QR di kelas</Text>
      </View>
      {busy && (
        <View style={styles.busy}>
          <ActivityIndicator size="large" color="#fff" />
          <Text style={styles.busyText}>Memproses…</Text>
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#000' },
  centered: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
    backgroundColor: theme.colors.background,
  },
  hint: { fontSize: 16, textAlign: 'center', marginBottom: 16, color: theme.colors.text },
  btn: {
    backgroundColor: theme.colors.primary,
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
    marginBottom: 12,
  },
  btnText: { color: '#fff', fontWeight: '600' },
  btnGhost: { backgroundColor: 'transparent', borderWidth: 1, borderColor: theme.colors.border },
  btnGhostText: { color: theme.colors.text },
  overlayTop: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    paddingTop: 48,
    paddingHorizontal: 16,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(0,0,0,0.45)',
    paddingBottom: 12,
  },
  backBtn: { marginRight: 12, padding: 4 },
  overlayTitle: { color: '#fff', fontSize: 16, fontWeight: '600', flex: 1 },
  busy: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  busyText: { color: '#fff', marginTop: 12, fontSize: 16 },
});
