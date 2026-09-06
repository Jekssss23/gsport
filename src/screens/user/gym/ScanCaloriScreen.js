import React, { useRef, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ActivityIndicator, Alert, Image } from 'react-native';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../../../styles/theme';
import { analyzeFoodPhoto, saveCalorieRecord } from '../../../services/CalorieService';

export default function ScanCaloriScreen({ navigation }) {
  const [permission, requestPermission] = useCameraPermissions();
  const cameraRef = useRef(null);
  const [phase, setPhase] = useState('idle'); // idle | processing | result
  const [preview, setPreview] = useState(null);
  const [result, setResult] = useState(null);

  const capture = async () => {
    if (!cameraRef.current) return;
    try {
      const photo = await cameraRef.current.takePictureAsync({ base64: true, quality: 0.7 });
      setPreview(photo.uri);
      setPhase('processing');

      const analysis = await analyzeFoodPhoto(photo.base64);
      if (analysis.isError) {
        Alert.alert('Tidak terdeteksi', analysis.source, [
          { text: 'Scan Lagi', onPress: () => reset() },
          { text: 'Tutup', style: 'cancel', onPress: () => navigation.goBack() },
        ]);
        return;
      }
      if (!analysis.items.length) {
        Alert.alert('Tidak terdeteksi', 'Coba foto lebih jelas dan dekat makanan.', [
          { text: 'Scan Lagi', onPress: () => reset() },
        ]);
        return;
      }

      await saveCalorieRecord(analysis);
      setResult(analysis);
      setPhase('result');
    } catch (e) {
      console.error('Scan error:', e);
      Alert.alert('Gagal memproses', e.message || String(e), [{ text: 'Coba lagi', onPress: () => reset() }]);
    }
  };

  const reset = () => {
    setPreview(null);
    setResult(null);
    setPhase('idle');
  };

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
        <Text style={styles.hint}>Kamera diperlukan untuk scan makanan.</Text>
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
        ref={cameraRef}
        style={StyleSheet.absoluteFillObject}
        facing="back"
        enableTorch={false}
      />

      <View style={styles.overlayTop}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()} accessibilityLabel="Kembali">
          <Ionicons name="close" size={28} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.overlayTitle}>Foto makanan untuk scan kalori</Text>
      </View>

      {phase === 'idle' && (
        <View style={styles.shutterArea}>
          <TouchableOpacity style={styles.shutter} onPress={capture} accessibilityLabel="Ambil foto">
            <View style={styles.shutterInner} />
          </TouchableOpacity>
        </View>
      )}

      {phase === 'processing' && (
        <View style={styles.busy}>
          <ActivityIndicator size="large" color="#fff" />
          <Text style={styles.busyText}>Memproses AI…</Text>
          {preview ? <Image source={{ uri: preview }} style={styles.previewThumb} /> : null}
        </View>
      )}

      {phase === 'result' && result && (
        <View style={styles.resultPanel}>
          <View style={styles.resultHeader}>
            <View>
              <Text style={styles.resultTag}>ESTIMASI KALORI</Text>
              <Text style={styles.resultTotal}>{result.totalCalories} kkal</Text>
            </View>
            <Ionicons name="checkmark-circle" size={30} color="#10b981" />
          </View>
          <Text style={styles.savedNote}>Tersimpan otomatis ke riwayat</Text>
          <View style={styles.rows}>
            {result.items.map((it, i) => (
              <View key={i} style={styles.row}>
                <View style={styles.rowIcon}>
                  <Ionicons name="restaurant-outline" size={18} color={theme.colors.primary} />
                </View>
                <View style={styles.rowBody}>
                  <Text style={styles.rowName}>{it.name}</Text>
                  <Text style={styles.rowMeta}>
                    {it.portionGrams} g · {it.sourceDB === 'usda' ? 'USDA' : 'Gemini'}
                  </Text>
                </View>
                <Text style={styles.rowCals}>{it.calories} kkal</Text>
              </View>
            ))}
          </View>
          <View style={styles.resultActions}>
            <TouchableOpacity style={styles.primaryBtn} onPress={reset}>
              <Text style={styles.primaryBtnText}>Scan Lagi</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[styles.primaryBtn, styles.ghostBtn]} onPress={() => navigation.replace('GymCaloriesHistory')}>
              <Text style={styles.ghostBtnText}>Selesai</Text>
            </TouchableOpacity>
          </View>
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
  shutterArea: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    alignItems: 'center',
    paddingBottom: 44,
    backgroundColor: 'rgba(0,0,0,0.35)',
  },
  shutter: {
    width: 74,
    height: 74,
    borderRadius: 37,
    borderWidth: 4,
    borderColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  shutterInner: {
    width: 58,
    height: 58,
    borderRadius: 29,
    backgroundColor: theme.colors.primary,
  },
  busy: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.55)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  busyText: { color: '#fff', marginTop: 12, fontSize: 16 },
  previewThumb: {
    width: 90,
    height: 90,
    borderRadius: 12,
    marginTop: 18,
    borderWidth: 2,
    borderColor: 'rgba(255,255,255,0.4)',
  },
  resultPanel: {
    position: 'absolute',
    left: 12,
    right: 12,
    bottom: 16,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.large,
    padding: 20,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  resultHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  resultTag: {
    color: theme.colors.textSecondary,
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 2,
  },
  resultTotal: {
    color: '#fff',
    fontSize: 30,
    fontWeight: '900',
  },
  savedNote: {
    color: '#10b981',
    fontSize: 12,
    fontWeight: '700',
    marginTop: 4,
  },
  rows: {
    marginTop: 14,
    gap: 10,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  rowIcon: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: 'rgba(230,0,0,0.12)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  rowBody: { flex: 1 },
  rowName: { color: '#fff', fontSize: 15, fontWeight: '700' },
  rowMeta: { color: theme.colors.textTertiary, fontSize: 11, marginTop: 2 },
  rowCals: { color: '#fff', fontSize: 15, fontWeight: '800' },
  resultActions: {
    flexDirection: 'row',
    gap: 12,
    marginTop: 18,
  },
  primaryBtn: {
    flex: 1,
    backgroundColor: theme.colors.primary,
    paddingVertical: 13,
    borderRadius: theme.borderRadius.medium,
    alignItems: 'center',
  },
  primaryBtnText: { color: '#fff', fontWeight: '800' },
  ghostBtn: { backgroundColor: 'transparent', borderWidth: 1, borderColor: theme.colors.inputBorder },
  ghostBtnText: { color: theme.colors.text, fontWeight: '700' },
});