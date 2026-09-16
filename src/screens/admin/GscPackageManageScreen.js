import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, Image, Modal, TextInput, ActivityIndicator, Alert, ScrollView } from 'react-native';
import { collection, addDoc } from 'firebase/firestore';
import { db, auth } from '../../config/firebase';
import { API_BASE_URL, fetchWithTimeout } from '../../config/api';
import { theme } from '../../styles/theme';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import AppModalAlert from '../../components/AppModalAlert';

export default function GscPackageManageScreen({ navigation }) {
  const [packages, setPackages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedPkg, setSelectedPkg] = useState(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [hoursToDeduct, setHoursToDeduct] = useState('');
  const [actionBusy, setActionBusy] = useState(false);
  const [alertState, setAlertState] = useState({ visible: false, title: '', message: '', type: 'warning' });

  useEffect(() => {
    fetchPackages();
  }, []);

  const fetchPackages = async () => {
    setLoading(true);
    try {
      const res = await fetchWithTimeout(`${API_BASE_URL}/gsc_package/get_all_packages`, {
        headers: { Accept: 'application/json' },
      }, 20000);
      const json = await res.json();
      if (res.ok && json.ok) {
        const pkgs = json.data || [];
        pkgs.sort((a, b) => {
          const timeA = new Date(a.createdAt || 0).getTime();
          const timeB = new Date(b.createdAt || 0).getTime();
          return timeB - timeA;
        });
        setPackages(pkgs);
      } else {
        throw new Error(json.message || 'Gagal memuat paket GSC dari server');
      }
    } catch (e) {
      console.error(e);
      setAlertState({ visible: true, title: 'Error', message: 'Gagal memuat paket GSC', type: 'error' });
    } finally {
      setLoading(false);
    }
  };

  const openPackageDetails = (pkg) => {
    setSelectedPkg(pkg);
    setHoursToDeduct('');
    setModalVisible(true);
  };

  const handleDeduct = async () => {
    const hours = parseInt(hoursToDeduct);
    if (isNaN(hours) || hours <= 0) {
      setAlertState({ visible: true, title: 'Input Tidak Valid', message: 'Masukkan angka jam yang valid (minimal 1)', type: 'warning' });
      return;
    }
    if (hours > selectedPkg.remainingHours) {
      setAlertState({ visible: true, title: 'Jam Tidak Cukup', message: `Sisa jam hanya ${selectedPkg.remainingHours}`, type: 'warning' });
      return;
    }

    setActionBusy(true);
    try {
      const payload = {
        package_id: selectedPkg.id,
        hours: hours,
        admin_id: auth.currentUser?.uid || '',
        admin_name: auth.currentUser?.email || 'Admin'
      };

      const res = await fetchWithTimeout(`${API_BASE_URL}/gsc_package/deduct_hours`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
      }, 15000);
      const json = await res.json();

      if (res.ok && json.ok) {
        setAlertState({ 
          visible: true, 
          title: 'Berhasil', 
          message: `Berhasil memotong ${hours} jam dari paket ${selectedPkg.userEmail}`, 
          type: 'success' 
        });
        
        setModalVisible(false);
        fetchPackages(); // refresh list
      } else {
        throw new Error(json.message || 'Gagal memotong jam paket di server');
      }
    } catch (error) {
      console.error("Deduct Error:", error);
      setAlertState({ visible: true, title: 'Gagal', message: error.message || 'Gagal memotong jam paket', type: 'error' });
    } finally {
      setActionBusy(false);
    }
  };

  const formatCurrency = (amount) => {
    if (!amount) return '0';
    return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  };

  const renderPackage = ({ item }) => {
    return (
      <TouchableOpacity 
        style={styles.cardContainer}
        activeOpacity={0.8}
        onPress={() => openPackageDetails(item)}
      >
        <LinearGradient
          colors={['#1a1a1a', '#0f0f0f']}
          style={styles.card}
        >
          <View style={styles.cardHeader}>
            <Text style={styles.sportText}>{item.sportType}</Text>
            <View style={[styles.statusBadge, { backgroundColor: item.remainingHours > 0 ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' }]}>
              <Text style={[styles.statusText, { color: item.remainingHours > 0 ? '#10b981' : '#ef4444' }]}>
                {item.remainingHours > 0 ? 'Aktif' : 'Habis'}
              </Text>
            </View>
          </View>
          
          <Text style={styles.emailText} numberOfLines={1}>{item.userEmail}</Text>
          
          <View style={styles.packageDetails}>
            <View style={styles.detailItem}>
              <Text style={styles.detailLabel}>Paket</Text>
              <Text style={styles.detailValue}>{item.packageName}</Text>
            </View>
            <View style={styles.detailItem}>
              <Text style={styles.detailLabel}>Sisa Jam</Text>
              <Text style={styles.detailValue}>{item.remainingHours} / {item.totalHours}</Text>
            </View>
          </View>
        </LinearGradient>
      </TouchableOpacity>
    );
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>GSC Packages</Text>
        <TouchableOpacity onPress={fetchPackages} style={styles.refreshButton}>
          <Ionicons name="refresh" size={24} color={theme.colors.primary} />
        </TouchableOpacity>
      </View>

      {loading ? (
        <View style={styles.center}>
          <ActivityIndicator size="large" color={theme.colors.primary} />
        </View>
      ) : packages.length === 0 ? (
        <View style={styles.center}>
          <Ionicons name="folder-open-outline" size={60} color={theme.colors.textSecondary} />
          <Text style={styles.emptyText}>Belum ada pembelian paket GSC</Text>
        </View>
      ) : (
        <FlatList
          data={packages}
          keyExtractor={(item) => item.id}
          renderItem={renderPackage}
          contentContainerStyle={styles.listContainer}
        />
      )}

      {/* Package Detail Modal */}
      <Modal
        visible={modalVisible}
        transparent={true}
        animationType="slide"
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Detail & Bukti Bayar</Text>
              <TouchableOpacity onPress={() => setModalVisible(false)}>
                <Ionicons name="close" size={24} color={theme.colors.textSecondary} />
              </TouchableOpacity>
            </View>

            {selectedPkg && (
              <ScrollView showsVerticalScrollIndicator={false}>
                <View style={styles.infoBox}>
                  <Text style={styles.infoText}>Email: <Text style={{color:'white'}}>{selectedPkg.userEmail}</Text></Text>
                  <Text style={styles.infoText}>Paket: <Text style={{color:'white'}}>{selectedPkg.packageName} ({selectedPkg.sportType})</Text></Text>
                  <Text style={styles.infoText}>Total Bayar: <Text style={{color:'white'}}>{formatCurrency(selectedPkg.totalPrice)}</Text></Text>
                  <Text style={styles.infoText}>Sisa Jam: <Text style={{color: theme.colors.primary, fontWeight:'bold'}}>{selectedPkg.remainingHours} Jam</Text></Text>
                </View>

                {selectedPkg.paymentProofBase64 ? (
                  <View style={styles.proofContainer}>
                    <Text style={styles.proofLabel}>Bukti Transfer:</Text>
                    <Image 
                      source={{ uri: selectedPkg.paymentProofBase64 }} 
                      style={styles.proofImage} 
                      resizeMode="contain"
                    />
                  </View>
                ) : (
                  <View style={styles.proofContainer}>
                    <Text style={styles.proofLabel}>Bukti Transfer tidak tersedia.</Text>
                  </View>
                )}

                {selectedPkg.remainingHours > 0 ? (
                  <View style={styles.actionContainer}>
                    <Text style={styles.actionLabel}>Potong Jam Manual</Text>
                    <TextInput
                      style={styles.hourInput}
                      keyboardType="number-pad"
                      placeholder="Jumlah Jam (Cth: 2)"
                      placeholderTextColor={theme.colors.textTertiary}
                      value={hoursToDeduct}
                      onChangeText={setHoursToDeduct}
                    />
                    <TouchableOpacity 
                      style={[styles.deductBtn, actionBusy && { opacity: 0.7 }]} 
                      onPress={handleDeduct}
                      disabled={actionBusy}
                    >
                      {actionBusy ? (
                        <ActivityIndicator color="white" />
                      ) : (
                        <Text style={styles.deductBtnText}>Konfirmasi Potong Jam</Text>
                      )}
                    </TouchableOpacity>
                  </View>
                ) : (
                  <View style={styles.emptyStateContainer}>
                    <Text style={styles.emptyStateText}>Paket GSC ini sudah habis.</Text>
                  </View>
                )}
              </ScrollView>
            )}
          </View>
        </View>
      </Modal>

      <AppModalAlert
        visible={alertState.visible}
        title={alertState.title}
        message={alertState.message}
        type={alertState.type}
        onClose={() => setAlertState({ ...alertState, visible: false })}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#000' },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 60,
    paddingBottom: 20,
    paddingHorizontal: 20,
    backgroundColor: theme.colors.surface,
  },
  backButton: { padding: 5 },
  refreshButton: { padding: 5 },
  headerTitle: { color: 'white', fontSize: 18, fontWeight: 'bold' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  emptyText: { color: theme.colors.textSecondary, marginTop: 15, fontSize: 14 },
  listContainer: { padding: 20 },
  cardContainer: {
    marginBottom: 15,
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  card: { padding: 20 },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  sportText: { color: theme.colors.primary, fontWeight: 'bold', fontSize: 16 },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: { fontSize: 12, fontWeight: 'bold' },
  emailText: { color: 'white', fontSize: 18, fontWeight: 'bold', marginBottom: 15 },
  packageDetails: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    backgroundColor: 'rgba(255,255,255,0.05)',
    padding: 15,
    borderRadius: 10,
  },
  detailItem: { flex: 1 },
  detailLabel: { color: theme.colors.textSecondary, fontSize: 12, marginBottom: 4 },
  detailValue: { color: 'white', fontSize: 14, fontWeight: 'bold' },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.85)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: theme.colors.surface,
    borderTopLeftRadius: 25,
    borderTopRightRadius: 25,
    height: '85%',
    padding: 25,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  modalTitle: { color: 'white', fontSize: 20, fontWeight: 'bold' },
  infoBox: {
    backgroundColor: 'rgba(255,255,255,0.05)',
    padding: 15,
    borderRadius: 10,
    marginBottom: 20,
  },
  infoText: { color: theme.colors.textSecondary, fontSize: 14, marginBottom: 6 },
  proofContainer: {
    alignItems: 'center',
    marginBottom: 30,
    backgroundColor: 'rgba(0,0,0,0.5)',
    padding: 15,
    borderRadius: 15,
  },
  proofLabel: { color: 'white', alignSelf: 'flex-start', marginBottom: 10, fontWeight: 'bold' },
  proofImage: { width: '100%', height: 300, borderRadius: 10 },
  actionContainer: {
    backgroundColor: 'rgba(16, 185, 129, 0.1)',
    padding: 20,
    borderRadius: 15,
    borderWidth: 1,
    borderColor: 'rgba(16, 185, 129, 0.3)',
    marginBottom: 30,
  },
  actionLabel: { color: '#10b981', fontSize: 16, fontWeight: 'bold', marginBottom: 15 },
  hourInput: {
    backgroundColor: 'rgba(255,255,255,0.05)',
    borderRadius: 10,
    color: 'white',
    fontSize: 18,
    padding: 15,
    marginBottom: 15,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
  },
  deductBtn: {
    backgroundColor: '#10b981',
    padding: 15,
    borderRadius: 10,
    alignItems: 'center',
  },
  deductBtnText: { color: 'white', fontWeight: 'bold', fontSize: 16 },
  emptyStateContainer: {
    padding: 20,
    alignItems: 'center',
    backgroundColor: 'rgba(239, 68, 68, 0.1)',
    borderRadius: 15,
    marginBottom: 30,
  },
  emptyStateText: { color: '#ef4444', fontWeight: 'bold' }
});
