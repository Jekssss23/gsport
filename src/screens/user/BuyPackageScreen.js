import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Alert, ActivityIndicator, Image, Dimensions } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import * as ImagePicker from 'expo-image-picker';
import { API_BASE_URL } from '../../config/api';
import { auth, db } from '../../config/firebase';
import { collection, addDoc, serverTimestamp } from 'firebase/firestore';
import { theme } from '../../styles/theme';
import { StatusBar } from 'expo-status-bar';
import AppModalAlert from '../../components/AppModalAlert';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';

const { width } = Dimensions.get('window');

const SPORTS = [
  { id: 'badminton', name: 'Badminton', pricePerHour: 40000, icon: 'tennisball' },
  { id: 'futsal', name: 'Futsal', pricePerHour: 80000, icon: 'football' },
  { id: 'pickleball', name: 'Pickleball', pricePerHour: 50000, icon: 'baseball' },
];

const PACKAGES = [
  { id: 'paket_a', name: 'Paket A', hours: 30, validityMonths: 3, price: 1000000 },
  { id: 'paket_b', name: 'Paket B', hours: 50, validityMonths: 6, price: 1500000 },
  { id: 'paket_c', name: 'Paket C', hours: 100, validityMonths: 12, price: 2500000 },
];

export default function BuyPackageScreen({ navigation }) {
  const [step, setStep] = useState(1);
  const [selectedSport, setSelectedSport] = useState(null);
  const [selectedPackage, setSelectedPackage] = useState(null);
  const [paymentProof, setPaymentProof] = useState(null);
  const [loading, setLoading] = useState(false);
  const [modalError, setModalError] = useState({ visible: false, title: 'Error', message: '' });

  const handleNext = () => {
    if (!selectedSport) {
      setModalError({ visible: true, title: 'Pilih Olahraga', message: 'Silakan pilih olahraga dulu.' });
      return;
    }
    if (!selectedPackage) {
      setModalError({ visible: true, title: 'Pilih Paket', message: 'Silakan pilih paket terlebih dahulu.' });
      return;
    }
    setStep(2);
  };

  const pickImage = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      setModalError({ visible: true, title: 'Akses Ditolak', message: 'Izin galeri dibutuhkan untuk upload bukti pembayaran.' });
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      quality: 0.5,
      base64: true
    });

    if (!result.canceled) {
      setPaymentProof(`data:image/jpeg;base64,${result.assets[0].base64}`);
    }
  };

  const calculateTotal = () => {
    if (!selectedPackage) return 0;
    return selectedPackage.price;
  };

  const formatCurrency = (amount) => {
    return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  };

  const handleSubmit = async () => {
    if (!paymentProof) {
      setModalError({ visible: true, title: 'Bukti Pembayaran Belum Ada', message: 'Silakan upload screenshot pembayaran dulu.' });
      return;
    }

    setLoading(true);
    try {
      if (!auth.currentUser) throw new Error('User not authenticated');

      const recordFinancials = async (amount, type, desc) => {
        try {
          await addDoc(collection(db, 'financial_transactions'), {
            userId: auth.currentUser.uid,
            userEmail: auth.currentUser.email,
            amount: amount,
            transactionType: type,
            description: desc,
            createdAt: serverTimestamp()
          });

          const payload = {
            firebase_uid: auth.currentUser.uid,
            user_email: auth.currentUser.email,
            amount: amount,
            transaction_type: type,
            description: desc
          };

          await fetch(`${API_BASE_URL}/finance/record_transaction`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
          });
        } catch (e) {
          console.error("Error logging finance:", e);
        }
      };

      await addDoc(collection(db, 'gsc_packages'), {
        userId: auth.currentUser.uid,
        userEmail: auth.currentUser.email,
        sportType: selectedSport.name,
        packageName: selectedPackage.name,
        pricePerHour: Math.round(selectedPackage.price / selectedPackage.hours),
        totalHours: selectedPackage.hours,
        remainingHours: selectedPackage.hours,
        totalPrice: calculateTotal(),
        status: 'active',
        validityMonths: selectedPackage.validityMonths,
        paymentProofBase64: paymentProof,
        createdAt: serverTimestamp(),
        updatedAt: serverTimestamp()
      });

      await recordFinancials(calculateTotal(), 'package', `GSC Package Purchase ${selectedPackage.name} for ${selectedSport.name}`);

      Alert.alert(
        'Success', 
        'Package purchased successfully. Your barcode is now active.',
        [{ text: 'Great', onPress: () => navigation.goBack() }]
      );
    } catch (error) {
      console.error('Error buying package:', error);
      setModalError({ visible: true, title: 'Pembelian Gagal', message: getUserFriendlyErrorMessage(error, 'Gagal memproses pembelian paket.') });
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <LinearGradient
        colors={[theme.colors.background, '#000000']}
        style={StyleSheet.absoluteFill}
      />
      
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => step === 1 ? navigation.goBack() : setStep(1)}>
          <Ionicons name="arrow-back" size={24} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>{step === 1 ? 'Buy GSC Package' : 'Payment'}</Text>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {step === 1 ? (
          <View>
            <Text style={styles.sectionHeader}>Select Sport</Text>
            <View style={styles.sportList}>
              {SPORTS.map((sport) => (
                <TouchableOpacity
                  key={sport.id}
                  style={[
                    styles.sportCard,
                    selectedSport?.id === sport.id && styles.sportCardActive
                  ]}
                  onPress={() => setSelectedSport(sport)}
                  activeOpacity={0.8}
                >
                  <LinearGradient
                    colors={selectedSport?.id === sport.id ? theme.gradients.primary : ['#1A1A1A', '#0F0F0F']}
                    style={styles.sportGradient}
                  >
                    <View style={[styles.sportIconCircle, { backgroundColor: selectedSport?.id === sport.id ? 'rgba(255,255,255,0.2)' : 'rgba(255,255,255,0.05)' }]}>
                      <Ionicons name={sport.icon} size={28} color="white" />
                    </View>
                    <Text style={styles.sportNameText}>{sport.name}</Text>
                    <Text style={[styles.sportPriceText, { color: selectedSport?.id === sport.id ? 'rgba(255,255,255,0.8)' : theme.colors.textSecondary }]}>
                      {formatCurrency(sport.pricePerHour)} / hr
                    </Text>
                    {selectedSport?.id === sport.id && (
                      <View style={styles.checkBadge}>
                        <Ionicons name="checkmark" size={12} color="white" />
                      </View>
                    )}
                  </LinearGradient>
                </TouchableOpacity>
              ))}
            </View>

            <Text style={styles.sectionHeader}>Select Package</Text>
            <View style={styles.packageList}>
              {PACKAGES.map((pkg) => (
                <TouchableOpacity
                  key={pkg.id}
                  style={[
                    styles.pkgCard,
                    selectedPackage?.id === pkg.id && styles.pkgCardActive
                  ]}
                  onPress={() => setSelectedPackage(pkg)}
                  activeOpacity={0.8}
                >
                  <LinearGradient
                    colors={selectedPackage?.id === pkg.id ? ['#10b981', '#059669'] : ['#242424', '#151515']}
                    style={styles.pkgGradient}
                  >
                    <View style={styles.pkgInfoLeft}>
                      <Text style={styles.pkgNameText}>{pkg.name}</Text>
                      <Text style={styles.pkgDetailsText}>{pkg.hours} Hours • Valid {pkg.validityMonths} Months</Text>
                    </View>
                    <View style={styles.pkgInfoRight}>
                      <Text style={[styles.pkgPriceText, { color: selectedPackage?.id === pkg.id ? 'white' : theme.colors.textSecondary }]}>
                        {formatCurrency(pkg.price)}
                      </Text>
                    </View>
                    {selectedPackage?.id === pkg.id && (
                      <View style={styles.checkBadge}>
                        <Ionicons name="checkmark" size={12} color="white" />
                      </View>
                    )}
                  </LinearGradient>
                </TouchableOpacity>
              ))}
            </View>

            <View style={styles.summaryContainer}>
              <View style={styles.summaryRow}>
                <Text style={styles.summaryLabel}>Total Payment</Text>
                <Text style={styles.summaryValue}>{formatCurrency(calculateTotal())}</Text>
              </View>
              <TouchableOpacity style={styles.nextBtn} onPress={handleNext}>
                <LinearGradient
                  colors={theme.gradients.primary}
                  style={styles.nextGradient}
                >
                  <Text style={styles.nextText}>Proceed to Payment</Text>
                  <Ionicons name="arrow-forward" size={20} color="white" />
                </LinearGradient>
              </TouchableOpacity>
            </View>
          </View>
        ) : (
          <View>
            <View style={styles.paymentCard}>
              <LinearGradient
                colors={['#2A2A2A', '#0F0F0F']}
                style={styles.paymentGradient}
              >
                <Text style={styles.paymentTitle}>Scan GSC QRIS</Text>
                <Text style={styles.paymentDesc}>Scan this code with any e-wallet or mobile banking app to pay.</Text>
                
                <View style={styles.qrisWrapper}>
                  <View style={styles.qrisBox}>
                    <Image 
                      source={require('../../../assets/qris-gsc-bni.jpeg')} 
                      style={{ width: 180, height: 180, borderRadius: 10 }} 
                      resizeMode="contain" 
                    />
                  </View>
                  <Text style={styles.paymentAmount}>{formatCurrency(calculateTotal())}</Text>
                </View>

                <View style={styles.bankInfo}>
                  <Ionicons name="card-outline" size={20} color={theme.colors.textSecondary} />
                  <Text style={styles.bankText}>BCA: 123-456-789 (G Sports Center)</Text>
                </View>
              </LinearGradient>
            </View>

            <Text style={styles.sectionHeader}>Upload Proof</Text>
            <TouchableOpacity style={styles.uploadArea} onPress={pickImage} activeOpacity={0.7}>
              {paymentProof ? (
                <View style={styles.imageWrapper}>
                  <Image source={{ uri: paymentProof }} style={styles.uploadedImg} />
                  <View style={styles.changeOverlay}>
                    <Ionicons name="camera" size={24} color="white" />
                    <Text style={styles.changeText}>Change Photo</Text>
                  </View>
                </View>
              ) : (
                <View style={styles.uploadPlaceholder}>
                  <View style={styles.uploadCircle}>
                    <Ionicons name="cloud-upload-outline" size={32} color={theme.colors.primary} />
                  </View>
                  <Text style={styles.uploadMainText}>Tap to upload proof</Text>
                  <Text style={styles.uploadSubText}>JPEG or PNG (Max 5MB)</Text>
                </View>
              )}
            </TouchableOpacity>

            <TouchableOpacity 
              style={styles.submitBtn} 
              onPress={handleSubmit}
              disabled={loading}
            >
              <LinearGradient
                colors={loading ? ['#333', '#222'] : ['#10b981', '#059669']}
                style={styles.submitGradient}
              >
                {loading ? (
                  <ActivityIndicator color="white" />
                ) : (
                  <>
                    <Text style={styles.submitText}>Confirm Payment</Text>
                    <Ionicons name="checkmark-done" size={22} color="white" />
                  </>
                )}
              </LinearGradient>
            </TouchableOpacity>
          </View>
        )}
      </ScrollView>
      <AppModalAlert
        visible={modalError.visible}
        title={modalError.title}
        message={modalError.message}
        onClose={() => setModalError({ visible: false, title: 'Error', message: '' })}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  header: {
    paddingTop: 60,
    paddingBottom: 20,
    paddingHorizontal: 25,
    flexDirection: 'row',
    alignItems: 'center',
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  headerTitle: {
    color: 'white',
    fontSize: 20,
    fontWeight: 'bold',
  },
  scrollContent: {
    padding: 25,
    paddingTop: 10,
  },
  sectionHeader: {
    color: 'white',
    fontSize: 16,
    fontWeight: '800',
    marginBottom: 20,
    textTransform: 'uppercase',
    letterSpacing: 1.5,
  },
  sportList: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    marginBottom: 30,
  },
  sportCard: {
    width: (width - 70) / 3,
    height: 140,
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    marginBottom: 10,
    ...theme.shadows.medium,
  },
  sportGradient: {
    flex: 1,
    padding: 12,
    alignItems: 'center',
    justifyContent: 'center',
  },
  sportIconCircle: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 12,
  },
  sportNameText: {
    color: 'white',
    fontSize: 12,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 4,
  },
  sportPriceText: {
    fontSize: 9,
    textAlign: 'center',
  },
  checkBadge: {
    position: 'absolute',
    top: 8,right: 8,
    width: 18,
    height: 18,
    borderRadius: 9,
    backgroundColor: 'white',
    justifyContent: 'center',
    alignItems: 'center',
    ...theme.shadows.light,
  },
  packageList: {
    marginBottom: 30,
  },
  pkgCard: {
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    marginBottom: 15,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.08)',
    ...theme.shadows.medium,
  },
  pkgCardActive: {
    borderColor: theme.colors.primary,
    borderWidth: 1,
  },
  pkgGradient: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 20,
  },
  pkgInfoLeft: {
    flex: 1,
  },
  pkgNameText: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  pkgDetailsText: {
    color: 'rgba(255,255,255,0.78)',
    fontSize: 13,
  },
  pkgInfoRight: {
    alignItems: 'flex-end',
    justifyContent: 'center',
  },
  pkgPriceText: {
    fontSize: 16,
    fontWeight: 'bold',
  },
  summaryContainer: {
    backgroundColor: 'rgba(255,255,255,0.03)',
    borderRadius: theme.borderRadius.large,
    padding: 20,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  summaryLabel: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  summaryValue: {
    color: 'white',
    fontSize: 22,
    fontWeight: 'bold',
  },
  nextBtn: {
    borderRadius: theme.borderRadius.medium,
    overflow: 'hidden',
    ...theme.shadows.medium,
  },
  nextGradient: {
    height: 55,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  nextText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
    marginRight: 10,
  },
  paymentCard: {
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    marginBottom: 30,
    ...theme.shadows.heavy, 
  },
  paymentGradient: {
    padding: 25,
    alignItems: 'center',
  },
  paymentTitle: {
    color: 'white',
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 8,
  },
  paymentDesc: {
    color: theme.colors.textSecondary,
    fontSize: 13,
    textAlign: 'center',
    lineHeight: 18,
    marginBottom: 25,
  },
  qrisWrapper: {
    alignItems: 'center',
    marginBottom: 25,
  },
  qrisBox: {
    padding: 20,
    backgroundColor: 'white',
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    ...theme.shadows.heavy,
  },
  qrisOverlay: {
    position: 'absolute',
    backgroundColor: 'white',
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 5,
  },
  qrisBrand: {
    fontSize: 10,
    fontWeight: '900',
    color: 'black',
  },
  paymentAmount: {
    color: 'white',
    fontSize: 28,
    fontWeight: 'bold',
    marginTop: 20,
  },
  bankInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.05)',
    paddingVertical: 10,
    paddingHorizontal: 15,
    borderRadius: 10, 
  },
  bankText: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    marginLeft: 10, 
  },
  uploadArea: {
    height: 200,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.large,
    borderStyle: 'dashed',
    borderWidth: 2,
    borderColor: 'rgba(255,255,255,0.1)',
    overflow: 'hidden',
    marginBottom: 30,
  },
  uploadPlaceholder: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  uploadCircle: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: 'rgba(230, 0, 0, 0.1)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 15,
  },
  uploadMainText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  uploadSubText: {
    color: theme.colors.textTertiary,
    fontSize: 12,
  },
  imageWrapper: {
    flex: 1,
  },
  uploadedImg: {
    width: '100%',
    height: '100%',
  },
  changeOverlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: 'rgba(0,0,0,0.6)',
    height: 50,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  changeText: {
    color: 'white',
    fontSize: 12,
    fontWeight: 'bold',
    marginLeft: 10,
  },
  submitBtn: {
    borderRadius: theme.borderRadius.medium,
    overflow: 'hidden',
    marginBottom: 40,
    ...theme.shadows.medium,
  },
  submitGradient: {
    height: 60,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  submitText: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
    marginRight: 10,
  },
});
