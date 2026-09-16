import React, { useCallback, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  TextInput,
  Modal
} from 'react-native';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { ClassScheduleService } from '../../services/ClassScheduleService';
import { theme } from '../../styles/theme';
import { auth } from '../../config/firebase';
import { API_BASE_URL, fetchWithTimeout } from '../../config/api';
import AppModalAlert from '../../components/AppModalAlert';

export default function AdminClassAttendanceScanScreen({ navigation }) {
  const [permission, requestPermission] = useCameraPermissions();
  const [scanned, setScanned] = useState(false);
  const [busy, setBusy] = useState(false);
  const [scanMode, setScanMode] = useState(null); // 'class' or 'gsc'
  
  // GSC Scan Modal State
  const [gscModalVisible, setGscModalVisible] = useState(false);
  const [gscPackageId, setGscPackageId] = useState(null);
  const [gscPackageData, setGscPackageData] = useState(null);
  const [hoursToDeduct, setHoursToDeduct] = useState('');
  const [modalState, setModalState] = useState({
    visible: false,
    title: 'Info',
    message: '',
    type: 'warning',
    onCloseAction: null,
  });

  const showModal = ({ title, message, type = 'warning', onCloseAction = null }) => {
    setModalState({ visible: true, title, message, type, onCloseAction });
  };

  const onBarcodeScanned = useCallback(
    async ({ data }) => {
      if (scanned || busy || !data) return;
      setBusy(true);
      setScanned(true);
      
      const qrData = String(data).trim();

      // AUTO-DETECTION FLOW -> SPLIT FLOW
      // 1. Scan Mode: CLASS
      if (scanMode === 'class') {
        if (!qrData.startsWith('GSCMEMv1|')) {
          showModal({
            title: 'Salah QR Code',
            message: 'Ini bukan QR Member Kelas. Gunakan Scan Paket GSC untuk tiket paket.',
            onCloseAction: () => { setScanned(false); setBusy(false); },
          });
          return;
        }
        try {
          const result = await ClassScheduleService.attendanceCheckInMemberAuto(qrData);
          const title = result.already ? 'Sudah absen' : 'Berhasil';
          const msg = result.message || (result.data?.member_name ? `${result.data.member_name} — OK` : 'OK');
          showModal({
            title,
            message: msg,
            type: result.already ? 'warning' : 'success',
            onCloseAction: () => {
              setScanned(false);
              setBusy(false);
            },
          });
        } catch (e) {
          showModal({
            title: 'Tidak Bisa Absen',
            message: e.message || String(e),
            onCloseAction: () => {
              setScanned(false);
              setBusy(false);
            },
          });
        } finally {
          setBusy(false);
        }
        return;
      }

      // 2. Scan Mode: GSC
      if (scanMode === 'gsc') {
        if (qrData.startsWith('GSCMEMv1|')) {
          showModal({
            title: 'Salah QR Code',
            message: 'Ini adalah QR Member Kelas. Gunakan mode Scan Absen Les.',
            onCloseAction: () => { setScanned(false); setBusy(false); },
          });
          return;
        }
        try {
          const res = await fetchWithTimeout(
            `${API_BASE_URL}/gsc_package/get_package?id=${encodeURIComponent(qrData)}`,
            { headers: { Accept: 'application/json' } },
            15000
          );
          const json = await res.json();
          
          if (res.ok && json.ok && json.data) {
            const pkgData = json.data;
            if (pkgData.remainingHours <= 0) {
              showModal({
                title: 'Paket Habis',
                message: 'Paket GSC ini sudah habis (0 jam).',
                onCloseAction: () => {
                  setScanned(false);
                  setBusy(false);
                },
              });
            } else {
              setGscPackageId(qrData);
              setGscPackageData(pkgData);
              setHoursToDeduct('');
              setGscModalVisible(true);
              setBusy(false); // Modal takes over
            }
          } else {
            showModal({
              title: 'Tidak Ditemukan',
              message: json.message || 'QR Code Paket GSC tidak valid.',
              onCloseAction: () => {
                setScanned(false);
                setBusy(false);
              },
            });
          }
        } catch (e) {
          showModal({
            title: 'Error',
            message: e.message || 'Gagal memproses QR Code dari server. Silakan coba lagi.',
            onCloseAction: () => {
              setScanned(false);
              setBusy(false);
            },
          });
          setBusy(false);
        }
        return;
      }

      // scanMode tidak dikenali — reset state (safety net)
      setScanned(false);
      setBusy(false);
    },
    [scanned, busy, scanMode]
  );

  const handleDeductHours = async () => {
    const hours = parseInt(hoursToDeduct);
    if (isNaN(hours) || hours <= 0) {
      showModal({ title: 'Input Tidak Valid', message: 'Masukkan angka jam yang valid (minimal 1).' });
      return;
    }
    if (hours > gscPackageData.remainingHours) {
      showModal({ title: 'Jam Tidak Cukup', message: `Sisa jam: ${gscPackageData.remainingHours}` });
      return;
    }

    setBusy(true);
    try {
      const payload = {
        package_id: gscPackageId,
        hours: hours,
        admin_id: auth.currentUser?.uid || '',
        admin_name: auth.currentUser?.email || 'Admin',
      };

      const res = await fetchWithTimeout(`${API_BASE_URL}/gsc_package/deduct_hours`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      }, 15000);
      const json = await res.json();

      if (res.ok && json.ok) {
        const newRemaining = gscPackageData.remainingHours - hours;
        
        setGscModalVisible(false);
        showModal({
          title: 'Berhasil',
          message: `Berhasil memotong ${hours} jam. Sisa: ${newRemaining}.`,
          type: 'success',
          onCloseAction: () => {
            setScanned(false);
            setBusy(false);
            setGscPackageId(null);
            setGscPackageData(null);
          },
        });
      } else {
        throw new Error(json.message || 'Gagal menyimpan potongan jam dari server.');
      }
    } catch (e) {
      console.error("Error updating package:", e);
      showModal({ title: 'Error', message: e.message || 'Gagal menyimpan potongan jam.' });
      setBusy(false);
    }
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
        <Text style={styles.hint}>Kamera diperlukan untuk scan QR.</Text>
        <TouchableOpacity style={styles.btn} onPress={requestPermission}>
          <Text style={styles.btnText}>Izinkan kamera</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[styles.btn, styles.btnGhost]} onPress={() => navigation.goBack()}>
          <Text style={styles.btnGhostText}>Batal</Text>
        </TouchableOpacity>
      </View>
    );
  }

  if (scanMode === null) {
    return (
      <View style={styles.container}>
        <View style={styles.overlayTopMode}>
          <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()} accessibilityLabel="Kembali">
            <Ionicons name="arrow-back" size={28} color="#fff" />
          </TouchableOpacity>
          <View style={{ flex: 1 }}>
            <Text style={styles.overlayTitle}>Scanner GSC</Text>
          </View>
        </View>

        <View style={styles.modeContainer}>
           <TouchableOpacity style={styles.modeCard} onPress={() => setScanMode('class')} activeOpacity={0.8}>
              <LinearGradient colors={['#1a2a6c', '#b21f1f', '#fdbb2d']} style={styles.modeGradient}>
                <Ionicons name="people-circle" size={56} color="white" />
                <Text style={styles.modeTitle}>Scan Absen Les</Text>
                <Text style={styles.modeDesc}>Scan QR kehadiran Member Kelas</Text>
              </LinearGradient>
           </TouchableOpacity>

           <TouchableOpacity style={styles.modeCard} onPress={() => setScanMode('gsc')} activeOpacity={0.8}>
              <LinearGradient colors={['#232526', '#414345']} style={styles.modeGradient}>
                <Ionicons name="card" size={56} color="white" />
                <Text style={styles.modeTitle}>Scan Paket GSC</Text>
                <Text style={styles.modeDesc}>Scan QR untuk potong jam paket</Text>
              </LinearGradient>
           </TouchableOpacity>
        </View>
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
        <TouchableOpacity style={styles.backBtn} onPress={() => { setScanMode(null); setScanned(false); }} accessibilityLabel="Kembali">
          <Ionicons name="arrow-back" size={28} color="#fff" />
        </TouchableOpacity>
        <View style={{ flex: 1 }}>
          <Text style={styles.overlayTitle}>{scanMode === 'class' ? 'Scan Absen Les' : 'Scan Paket GSC'}</Text>
          <Text style={styles.overlaySubTitle} numberOfLines={1}>
             {scanMode === 'class' ? 'Arahkan ke QR Member Kelas' : 'Arahkan ke QR Paket GSC'}
          </Text>
        </View>
      </View>

      {busy && (
        <View style={styles.busy}>
          <ActivityIndicator size="large" color="#fff" />
          <Text style={styles.busyText}>Memproses…</Text>
        </View>
      )}

      {/* GSC Input Modal */}
      <Modal
        visible={gscModalVisible}
        transparent={true}
        animationType="fade"
        onRequestClose={() => {
           setGscModalVisible(false);
           setScanned(false);
        }}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Potong Jam Paket</Text>
              <TouchableOpacity onPress={() => { setGscModalVisible(false); setScanned(false); }}>
                <Ionicons name="close" size={24} color={theme.colors.textSecondary} />
              </TouchableOpacity>
            </View>
            
            {gscPackageData && (
              <View style={styles.modalInfo}>
                <Text style={styles.modalInfoText}>Paket: {gscPackageData.packageName || gscPackageData.sportType}</Text>
                <Text style={styles.modalInfoText}>Sisa Jam: {gscPackageData.remainingHours}</Text>
              </View>
            )}

            <Text style={styles.inputLabel}>Berapa jam yang ingin dipotong?</Text>
            <TextInput
              style={styles.hourInput}
              keyboardType="number-pad"
              placeholder="Contoh: 2"
              placeholderTextColor={theme.colors.textTertiary}
              value={hoursToDeduct}
              onChangeText={setHoursToDeduct}
              autoFocus={true}
            />

            <TouchableOpacity style={styles.submitModalBtn} onPress={handleDeductHours}>
              <Text style={styles.submitModalBtnText}>Konfirmasi Potongan</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
      <AppModalAlert
        visible={modalState.visible}
        title={modalState.title}
        message={modalState.message}
        type={modalState.type}
        onClose={() => {
          const action = modalState.onCloseAction;
          setModalState({ visible: false, title: 'Info', message: '', type: 'warning', onCloseAction: null });
          if (typeof action === 'function') action();
        }}
      />

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
  overlayTopMode: {
    paddingTop: 48,
    paddingHorizontal: 16,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#000',
    paddingBottom: 12,
    gap: 12,
  },
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
  
  // Mode Selection Styles
  modeContainer: {
    flex: 1,
    padding: 24,
    justifyContent: 'center',
    gap: 20,
  },
  modeCard: {
    height: 180,
    borderRadius: 20,
    overflow: 'hidden',
    ...theme.shadows.heavy,
  },
  modeGradient: {
    flex: 1,
    padding: 24,
    justifyContent: 'center',
    alignItems: 'center',
  },
  modeTitle: {
    color: 'white',
    fontSize: 22,
    fontWeight: 'bold',
    marginTop: 12,
    marginBottom: 4,
  },
  modeDesc: {
    color: 'rgba(255,255,255,0.8)',
    fontSize: 14,
    textAlign: 'center',
  },

  // Modal Styles
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.7)',
    justifyContent: 'center',
    padding: 24,
  },
  modalContent: {
    backgroundColor: theme.colors.surface,
    borderRadius: 20,
    padding: 24,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  modalTitle: {
    color: 'white',
    fontSize: 20,
    fontWeight: 'bold',
  },
  modalInfo: {
    backgroundColor: 'rgba(255,255,255,0.05)',
    padding: 15,
    borderRadius: 10,
    marginBottom: 20,
  },
  modalInfoText: {
    color: theme.colors.textSecondary,
    fontSize: 16,
    marginBottom: 4,
  },
  inputLabel: {
    color: 'white',
    fontSize: 14,
    marginBottom: 10,
  },
  hourInput: {
    backgroundColor: 'rgba(255,255,255,0.05)',
    borderRadius: 10,
    color: 'white',
    fontSize: 24,
    padding: 15,
    textAlign: 'center',
    marginBottom: 24,
    borderWidth: 1,
    borderColor: theme.colors.primary,
  },
  submitModalBtn: {
    backgroundColor: theme.colors.primary,
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  submitModalBtnText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
});

