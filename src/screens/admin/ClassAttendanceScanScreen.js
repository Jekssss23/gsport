import React, { useCallback, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Alert,
  TouchableOpacity,
  ActivityIndicator,
} from 'react-native';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { Ionicons } from '@expo/vector-icons';
import { ClassScheduleService } from '../../services/ClassScheduleService';
import { theme } from '../../styles/theme';

export default function AdminClassAttendanceScanScreen({ navigation }) {
  const [permission, requestPermission] = useCameraPermissions();
  const [scanned, setScanned] = useState(false);
  const [busy, setBusy] = useState(false);

  const onBarcodeScanned = useCallback(
    async ({ data }) => {
      if (scanned || busy || !data) return;
      setBusy(true);
      setScanned(true);
      try {
        const result = await ClassScheduleService.attendanceCheckInMemberAuto(data);
        const title = result.already ? 'Sudah absen' : 'Berhasil';
        const msg =
          result.message ||
          (result.data?.member_name ? `${result.data.member_name} — OK` : 'OK');
        Alert.alert(title, msg, [
          {
            text: 'Scan lagi',
            onPress: () => {
              setScanned(false);
              setBusy(false);
            },
          },
          {
            text: 'Tutup',
            style: 'cancel',
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
        <Text style={styles.hint}>Kamera diperlukan untuk scan QR absensi kelas.</Text>
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
        <View style={{ flex: 1 }}>
          <Text style={styles.overlayTitle}>Scan QR Member</Text>
          <Text style={styles.overlaySubTitle} numberOfLines={1}>
            Sistem akan otomatis memilih sesi hari ini sesuai kelas member.
          </Text>
        </View>
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
    gap: 12,
  },
  backBtn: { padding: 4 },
  overlayTitle: { color: '#fff', fontSize: 16, fontWeight: '700' },
  overlaySubTitle: { color: 'rgba(255,255,255,0.85)', fontSize: 12, marginTop: 2 },
  busy: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  busyText: { color: '#fff', marginTop: 12, fontSize: 16 },
});

