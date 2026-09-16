import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Image,
  TextInput,
  ActivityIndicator,
  Dimensions,
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { serverTimestamp } from 'firebase/firestore';
import { auth } from '../../config/firebase.js';
import { API_BASE_URL } from '../../config/api.js';
import { BookingService } from '../../services/BookingService';
import { CloudinaryService } from '../../services/CloudinaryService';
import { theme } from '../../styles/theme';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';
import AppModalAlert from '../../components/AppModalAlert';
import LoadingAnimation from '../../../assets/ui/LoadingAnimation';

const { width } = Dimensions.get('window');

const FieldReservationScreen = ({ navigation, route }) => {
  const routeUser = route.params?.user;
  const user = routeUser || (auth.currentUser ? {
    uid: auth.currentUser.uid,
    email: auth.currentUser.email,
    displayName: auth.currentUser.displayName,
    phoneNumber: auth.currentUser.phoneNumber,
  } : null);

  const [facilities, setFacilities] = useState([]);
  const [selectedFacility, setSelectedFacility] = useState(null);
  const [selectedCourt, setSelectedCourt] = useState(null);
  const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]);
  const [availableSlots, setAvailableSlots] = useState([]);
  const [selectedSlots, setSelectedSlots] = useState([]);
  const [paymentProof, setPaymentProof] = useState(null);
  const [loading, setLoading] = useState(false);
  const [loadingFacilities, setLoadingFacilities] = useState(true);
  const [loadingSlots, setLoadingSlots] = useState(false);
  const [staffMap, setStaffMap] = useState({});
  const [step, setStep] = useState(1);
  const [dateOptions, setDateOptions] = useState([]);
  const [modalError, setModalError] = useState({ visible: false, title: 'Error', message: '', type: 'warning', onCloseAction: null });

  useEffect(() => {
    const dates = [];
    for (let i = 0; i < 14; i++) {
      const d = new Date();
      d.setDate(d.getDate() + i);
      
      const dayName = d.toLocaleDateString('en-US', { weekday: 'short' });
      const dayDate = d.getDate();
      const monthName = d.toLocaleDateString('en-US', { month: 'short' });
      const fullDate = d.toISOString().split('T')[0];
      
      dates.push({
        dayName,
        dayDate,
        monthName,
        fullDate,
        isToday: i === 0
      });
    }
    setDateOptions(dates);
  }, []);

  const getRealTimeAvailableSlots = (allSlots) => {
    const tzOffset = (new Date()).getTimezoneOffset() * 60000;
    const localISOTime = (new Date(Date.now() - tzOffset)).toISOString().slice(0, -1);
    const todayStr = localISOTime.split('T')[0];
    
    if (selectedDate === todayStr) {
      const currentHour = new Date().getHours();
      return allSlots.filter(hour => hour > currentHour);
    }
    return allSlots;
  };

  useEffect(() => {
    if (!user) {
      setModalError({
        visible: true,
        title: 'User Tidak Ditemukan',
        message: 'Silakan login ulang.',
        onCloseAction: () => navigation.goBack(),
      });
      return;
    }
    loadFacilities();
  }, []);

  useEffect(() => {
    if (selectedCourt && selectedDate) {
      loadAvailableSlots();
    } else {
      setAvailableSlots([]);
      setSelectedSlots([]);
    }
  }, [selectedCourt, selectedDate]);

  const loadFacilities = async () => {
    try {
      setLoadingFacilities(true);
      const facilities = await BookingService.getFacilities();
      setFacilities(facilities);
    } catch (error) {
      console.error('Error loading facilities:', error);
      setModalError({ visible: true, title: 'Error', message: getUserFriendlyErrorMessage(error, 'Gagal memuat fasilitas.') });
    } finally {
      setLoadingFacilities(false);
    }
  };

  const loadAvailableSlots = async () => {
    try {
      setLoadingSlots(true);
      const allSlots = Array.from({ length: 17 }, (_, i) => i + 7);
      const apiAvailableSlots = await BookingService.getAvailableSlots(selectedCourt.id, selectedDate);
      const realTimeSlots = getRealTimeAvailableSlots(apiAvailableSlots);

      const tzOffset = (new Date()).getTimezoneOffset() * 60000;
      const localISOTime = (new Date(Date.now() - tzOffset)).toISOString().slice(0, -1);
      const todayStr = localISOTime.split('T')[0];
      const currentHour = new Date().getHours();

      const slotsWithStatus = allSlots.map(hour => {
        const isPassed = selectedDate === todayStr && hour <= currentHour;
        const isBooked = !apiAvailableSlots.includes(hour) && !isPassed;
        return {
          hour,
          isAvailable: realTimeSlots.includes(hour),
          isPassed,
          isBooked,
        };
      });

      setAvailableSlots(slotsWithStatus);
      setSelectedSlots([]);
      setStaffMap({});

      const newStaffMap = {};
      for (const slot of realTimeSlots) {
        const staff = await BookingService.getStaffBySlot(selectedDate, slot, selectedFacility.name);
        if (staff) {
          newStaffMap[slot] = staff;
        }
      }
      setStaffMap(newStaffMap);
    } catch (error) {
      console.error('Error loading slots:', error);
      setModalError({ visible: true, title: 'Error', message: getUserFriendlyErrorMessage(error, 'Gagal memuat jadwal slot.') });
    } finally {
      setLoadingSlots(false);
    }
  };

  const selectFacility = (facility) => {
    setSelectedFacility(facility);
    setSelectedCourt(null);
    setSelectedSlots([]);
    setAvailableSlots([]);
    setStep(2);
  };

  const selectCourt = (court) => {
    setSelectedCourt(court);
    setSelectedSlots([]);
  };

  const toggleTimeSlot = (slotObj) => {
    if (!slotObj.isAvailable) return;

    const hour = slotObj.hour;
    setSelectedSlots(prev => {
      if (prev.includes(hour)) {
        return prev.filter(h => h !== hour);
      } else {
        return [...prev, hour].sort((a, b) => a - b);
      }
    });
  };

  const pickImage = async () => {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [4, 3],
      quality: 0.8,
    });

    if (!result.canceled) {
      setPaymentProof(result.assets[0]);
    }
  };

  const uploadPaymentProof = async () => {
    if (!paymentProof) return null;
    try {
      const userId = user.uid || user.email?.replace(/[@.]/g, '_') || Date.now();
      const imageUrl = await CloudinaryService.uploadImage(paymentProof.uri, userId);
      return imageUrl;
    } catch (error) {
      console.error('Upload error:', error);
      throw new Error('Failed to upload payment proof');
    }
  };

  const calculateTotal = () => {
    if (!selectedFacility || selectedSlots.length === 0) return { total: 0, dp: 0 };
    const total = selectedFacility.pricePerHour * selectedSlots.length;
    const dp = Math.round(total * (selectedFacility.dpPercentage / 100));
    return { total, dp, remaining: total - dp };
  };

  const submitBooking = async () => {
    if (!paymentProof) {
      setModalError({
        visible: true,
        title: 'Bukti Pembayaran Belum Ada',
        message: 'Silakan upload bukti pembayaran dulu sebelum lanjut booking.',
      });
      return;
    }
    if (selectedSlots.length === 0) {
      setModalError({
        visible: true,
        title: 'Pilih Jam Dulu',
        message: 'Silakan pilih minimal satu slot waktu.',
      });
      return;
    }

    setLoading(true);
    try {
      const currentAvailableSlots = await BookingService.getAvailableSlots(selectedCourt.id, selectedDate);
      const allSlotsStillAvailable = selectedSlots.every(slot => currentAvailableSlots.includes(slot));

      if (!allSlotsStillAvailable) {
        setModalError({
          visible: true,
          title: 'Slot Sudah Terisi',
          message: 'Ada jam yang baru saja dibooking orang lain. Pilih slot lain ya.',
        });
        setLoading(false);
        setStep(3);
        return;
      }

      const paymentProofUrl = await uploadPaymentProof();
      const { total, dp, remaining } = calculateTotal();
      const firstSlot = selectedSlots[0];
      const assignedStaff = staffMap[firstSlot];

      const bookingData = {
        userId: user.uid,
        userEmail: user.email,
        userName: user.displayName || user.email.split('@')[0],
        userPhone: user.phoneNumber || '',
        facilityId: selectedFacility.id,
        facilityName: selectedFacility.name,
        courtId: selectedCourt.id,
        courtName: selectedCourt.name,
        date: selectedDate,
        slots: selectedSlots,
        // Keep both naming styles to avoid future mismatches.
        totalPrice: total,
        downPayment: dp,
        remainingPayment: remaining,
        totalAmount: total,
        dpAmount: dp,
        remainingAmount: remaining,
        paymentProofUrl: paymentProofUrl,
        status: 'pending',
        assignedStaffId: assignedStaff?.id || null,
        assignedStaffName: assignedStaff?.name || null,
        assignedStaffEmail: assignedStaff?.email || null,
        createdAt: serverTimestamp()
      };

      await BookingService.createBooking(bookingData);

      // Record financial transaction via MySQL API
      try {
        const payload = {
          firebase_uid: user.uid,
          user_email: user.email,
          amount: dp,
          transaction_type: 'booking',
          description: `Booking DP: ${selectedFacility.name} - ${selectedCourt.name} (${selectedDate})`
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

      setLoading(false);
      setModalError({
        visible: true,
        title: 'Booking Berhasil',
        message: 'Reservasi berhasil dibuat. Kamu bisa cek detailnya di My History.',
        type: 'success',
        onCloseAction: () => navigation.navigate('History'),
      });
    } catch (error) {
      console.error('Booking error:', error);
      setModalError({
        visible: true,
        title: 'Booking Gagal',
        message: getUserFriendlyErrorMessage(error, 'Gagal membuat booking.'),
      });
      setLoading(false);
    }
  };

  const renderFacilityStep = () => (
    <View style={styles.stepContainer}>
      <Text style={styles.sectionHeader}>Choose Facility</Text>
      <Text style={styles.sectionSubtext}>Silahkan pilih fasilitas lapangan olahraga yang ingin di booking</Text>
      {loadingFacilities ? (
        <ActivityIndicator size="large" color={theme.colors.primary} style={{ marginTop: 50 }} />
      ) : (
        <View style={styles.facilityGrid}>
          {facilities.map((facility) => {
            const fName = facility.name?.toLowerCase() || '';
            let imgSrc = null;
            if (fName.includes('futsal')) imgSrc = require('../../../assets/images/futsal.png');
            else if (fName.includes('badminton')) imgSrc = require('../../../assets/images/badminton.png');
            else if (fName.includes('pickle')) imgSrc = require('../../../assets/images/pickleball.png');

            return (
              <TouchableOpacity
                key={facility.id}
                style={styles.facilityCardOuter}
                onPress={() => selectFacility(facility)}
                activeOpacity={0.9}
              >
                <LinearGradient
                  colors={['#ffffff', '#0c0d0d']}
                  start={{ x: 0, y: 0 }}
                  end={{ x: 1, y: 1 }}
                  style={styles.facilityCardBorder}
                >
                  <View style={styles.facilityCardInner}>
                    <View style={styles.facilityRay} />
                    <View style={styles.facilityLineTop} />
                    <View style={styles.facilityLineBottom} />
                    <View style={styles.facilityLineLeft} />
                    <View style={styles.facilityLineRight} />
                    <View style={styles.facilityDot} />
                    {imgSrc ? (
                      <Image source={imgSrc} style={styles.facilityIconImg} resizeMode="contain" />
                    ) : (
                      <Ionicons name="help-outline" size={28} color="#ffffff" style={styles.facilityIconTiny} />
                    )}
                    <Text style={styles.facilityNameText}>{facility.name}</Text>
                  </View>
                </LinearGradient>
              </TouchableOpacity>
            );
          })}
        </View>
      )}
    </View>
  );

  const renderSlotStep = () => (
    <View style={styles.stepContainer}>
      {/* Date Picker */}
      <Text style={styles.sectionHeader}>Select Date</Text>
      <Text style={styles.sectionSubtext}>Silahkan Pilih Tanggal Bookingan sesuai ketersediaan tanggal di bawah ini</Text>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.dateScroll}>
        {dateOptions.map((date) => (
          <TouchableOpacity 
            key={date.fullDate} 
            style={[
              styles.dateCard,
              selectedDate === date.fullDate && styles.dateCardActive
            ]}
            onPress={() => setSelectedDate(date.fullDate)}
          >
            <Text style={[styles.dayName, selectedDate === date.fullDate && styles.dateTextActive]}>{date.dayName}</Text>
            <Text style={[styles.dayDate, selectedDate === date.fullDate && styles.dateTextActive]}>{date.dayDate}</Text>
            <Text style={[styles.monthName, selectedDate === date.fullDate && styles.dateTextActive]}>{date.monthName}</Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Court Selection */}
      <Text style={[styles.sectionHeader, { marginTop: 25 }]}>Select Court</Text>
      <Text style={styles.sectionSubtext}>Silahkan pilih lapangan yang ingin anda tempati</Text>
      <View style={styles.courtGrid}>
        {selectedFacility?.courts?.map((court) => (
          <TouchableOpacity 
            key={court.id} 
            style={[
              styles.courtBtn,
              selectedCourt?.id === court.id && styles.courtBtnActive
            ]}
            onPress={() => selectCourt(court)}
          >
            {(() => {
              const fName = selectedFacility?.name?.toLowerCase() || '';
              const cName = court.name?.toLowerCase() || '';
              let imgSrc = null;
              if (fName.includes('futsal')) {
                imgSrc = cName.includes('2') ? require('../../../assets/images/futsal2.jpeg') : require('../../../assets/images/futsal1.jpeg');
              } else if (fName.includes('pickleball')) {
                imgSrc = cName.includes('2') ? require('../../../assets/images/pickle2.jpeg') : require('../../../assets/images/pickle1.jpeg');
              } else if (fName.includes('badminton')) {
                imgSrc = require('../../../assets/images/badminton.jpeg');
              }

              return imgSrc ? (
                <Image source={imgSrc} style={styles.courtImage} resizeMode="cover" />
              ) : (
                <View style={styles.courtPlaceholder}>
                  <Ionicons name="image-outline" size={20} color="rgba(255,255,255,0.45)" />
                </View>
              );
            })()}
            <Text style={[styles.courtBtnText, selectedCourt?.id === court.id && styles.courtBtnTextActive]} numberOfLines={1}>
              {court.name}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Time Slots */}
      {selectedCourt && (
        <View style={{ marginTop: 25 }}>
          <Text style={styles.sectionHeader}>Available Slots</Text>
          <Text style={styles.sectionSubtext}>Pilih jam sesuai ketersediaan</Text>
          {loadingSlots ? (
            <LoadingAnimation />
          ) : (
            <View style={styles.slotGrid}>
              {availableSlots.map((slotObj) => {
                const isSelected = selectedSlots.includes(slotObj.hour);
                const isDisabled = !slotObj.isAvailable || slotObj.isPassed;
                return (
                  <TouchableOpacity 
                    key={slotObj.hour} 
                    style={[
                      styles.slotBtn,
                      isSelected && styles.slotBtnActive,
                      slotObj.isBooked && styles.slotBtnBooked,
                      isDisabled && styles.slotBtnDisabled
                    ]}
                    onPress={() => toggleTimeSlot(slotObj)}
                    disabled={isDisabled}
                  >
                    <Text style={[
                      styles.slotBtnText,
                      isSelected && styles.slotBtnTextActive,
                      (isDisabled) && styles.slotBtnTextDisabled
                    ]}>
                      {slotObj.hour}:00
                    </Text>
                    {(slotObj.isPassed || slotObj.isBooked) && (
                      <Text style={styles.slotHintText}>
                        {slotObj.isPassed ? 'Passed' : 'Booked'}
                      </Text>
                    )}
                  </TouchableOpacity>
                );
              })}
            </View>
          )}
        </View>
      )}

      {/* Staff on duty summary (from Employee Management schedules) */}
      {selectedCourt && !loadingSlots && (
        <View style={{ marginTop: 18 }}>
          <Text style={styles.sectionHeader}>Staff On Duty</Text>
          {selectedSlots.length === 0 ? (
            <Text style={styles.staffEmptyText}>Pilih jam dulu untuk melihat petugas jaga.</Text>
          ) : (
            <View style={styles.staffPillsWrap}>
              {selectedSlots.map((h) => {
                const staff = staffMap[h];
                return (
                  <View key={h} style={[styles.staffPill, !staff && styles.staffPillMissing]}>
                    <Ionicons name="person-circle-outline" size={18} color={staff ? theme.colors.primary : 'rgba(255,255,255,0.35)'} />
                    <Text style={styles.staffPillText} numberOfLines={1}>
                      {h}:00 • {staff?.name || staff?.employeeName || 'Belum ada jadwal'}
                    </Text>
                  </View>
                );
              })}
            </View>
          )}
        </View>
      )}

      {selectedSlots.length > 0 && (
        <TouchableOpacity 
          style={styles.primaryBtn} 
          onPress={() => setStep(3)}
        >
          <LinearGradient colors={theme.gradients.primary} style={styles.primaryBtnGradient}>
            <Text style={styles.primaryBtnText}>Bayar Sekarang</Text>
            <Ionicons name="arrow-forward" size={20} color="white" />
          </LinearGradient>
        </TouchableOpacity>
      )}
    </View>
  );

  const renderPaymentStep = () => {
    const { total, dp, remaining } = calculateTotal();
    return (
      <View style={styles.stepContainer}>
        <Text style={styles.sectionHeader}>Rincian Booking</Text>
        <Text style={styles.sectionSubtext}>Pembayaran di aplikasi di lakukan untuk pembayaran DP terlebih dahulu sesuai nominal rincian di bawah ( WAJIB!!) , ketika sudah selesai maka kamu akan mendapatkan E-ticket nantinya.</Text>
        <View style={styles.summaryCard}>
          <LinearGradient colors={['#2A2A2A', '#1A1A1A']} style={styles.summaryGradient}>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Facility</Text>
              <Text style={styles.summaryValue}>{selectedFacility.name} - {selectedCourt.name}</Text>
            </View>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Date</Text>
              <Text style={styles.summaryValue}>{selectedDate}</Text>
            </View>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Time</Text>
              <Text style={styles.summaryValue}>{selectedSlots.map(s => `${s}:00`).join(', ')}</Text>
            </View>
            <View style={styles.summaryDivider} />
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Total Amount</Text>
              <Text style={styles.totalPrice}>Rp {total.toLocaleString('id-ID')}</Text>
            </View>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>DP ({selectedFacility.dpPercentage}%)</Text>
              <Text style={styles.dpPrice}>Rp {dp.toLocaleString('id-ID')}</Text>
            </View>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Remaining</Text>
              <Text style={styles.remainingPrice}>Rp {remaining.toLocaleString('id-ID')}</Text>
            </View>
          </LinearGradient>
        </View>

        <View style={styles.paymentCard}>
          <LinearGradient
            colors={['#2A2A2A', '#0F0F0F']}
            style={styles.paymentGradient}
          >
            <Text style={styles.paymentTitle}>Scan QRIS</Text>
            <Text style={styles.paymentDesc}>
              Scan QR di bawah ini di E-wallet atau E-bank lainnya dan wajib input nominal sesuai DP rincian di atas!.
            </Text>
            <Text style={styles.paymentDescBold}>
              Jika ada kesalahan nominal bayar maka tidak berkenan melakukan pembayaran ulang maupun pengembalian DP.
            </Text>
            
            <View style={styles.qrisWrapper}>
              <View style={styles.qrisBox}>
                <Image 
                  source={require('../../../assets/QRIS GSC.jpeg')} 
                  style={{ width: 180, height: 180, borderRadius: 10 }} 
                  resizeMode="contain" 
                />
              </View>
              <Text style={styles.paymentAmount}>Rp {dp.toLocaleString('id-ID')}</Text>
            </View>

            <View style={styles.bankInfo}>
              <Ionicons name="card-outline" size={20} color={theme.colors.textSecondary} />
              <Text style={styles.bankText}>BNI (G Sports Center)</Text>
            </View>
          </LinearGradient>
        </View>

        <Text style={[styles.sectionHeader, { marginTop: 25 }]}>Upload Payment Proof</Text>
        <Text style={styles.sectionSubtext}>Upload Bukti pembayaran Kamu di sini ya</Text>
        <TouchableOpacity style={styles.uploadArea} onPress={pickImage} activeOpacity={0.8}>
          {paymentProof ? (
            <Image source={{ uri: paymentProof.uri }} style={styles.uploadedImg} />
          ) : (
            <View style={styles.uploadPlaceholder}>
              <Ionicons name="cloud-upload-outline" size={40} color={theme.colors.primary} />
              <Text style={styles.uploadMainText}>Tap to upload proof</Text>
              <Text style={styles.uploadSubText}>JPEG or PNG (Max 5MB)</Text>
            </View>
          )}
        </TouchableOpacity>

        <TouchableOpacity 
          style={styles.primaryBtn} 
          onPress={submitBooking}
          disabled={loading}
        >
          <LinearGradient colors={loading ? ['#333', '#222'] : theme.gradients.primary} style={styles.primaryBtnGradient}>
            {loading ? (
              <ActivityIndicator color="white" />
            ) : (
              <>
                <Text style={styles.primaryBtnText}>Confirm Booking</Text>
                <Ionicons name="checkmark-done" size={22} color="white" />
              </>
            )}
          </LinearGradient>
        </TouchableOpacity>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <LinearGradient colors={[theme.colors.background, '#000000']} style={StyleSheet.absoluteFill} />
      
      <View style={styles.header}>
        {step > 1 ? (
          <TouchableOpacity style={styles.backButton} onPress={() => setStep(step - 1)}>
            <Ionicons name="arrow-back" size={24} color="white" />
          </TouchableOpacity>
        ) : (
          <View style={styles.headerSpacer} />
        )}
        <Text style={styles.headerTitle}>Reserve Field</Text>
        <View style={styles.headerSpacer} />
      </View>

      {/* Step Indicators */}
      <View style={styles.stepIndicator}>
        {[1, 2, 3].map((s) => (
          <View key={s} style={styles.indicatorWrapper}>
            <View style={[styles.indicatorCircle, step >= s && styles.indicatorCircleActive]}>
              <Text style={[styles.indicatorText, step >= s && styles.indicatorTextActive]}>{s}</Text>
            </View>
            {s < 3 && <View style={[styles.indicatorLine, step > s && styles.indicatorLineActive]} />}
          </View>
        ))}
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {step === 1 && renderFacilityStep()}
        {step === 2 && renderSlotStep()}
        {step === 3 && renderPaymentStep()}
      </ScrollView>
      <AppModalAlert
        visible={modalError.visible}
        title={modalError.title}
        message={modalError.message}
        type={modalError.type || 'warning'}
        onClose={() => {
          const action = modalError.onCloseAction;
          setModalError({ visible: false, title: 'Error', message: '', type: 'warning', onCloseAction: null });
          if (typeof action === 'function') action();
        }}
      />
    </View>
  );
};

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
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  headerSpacer: {
    width: 40,
    height: 40,
  },
  headerTitle: {
    color: 'white',
    fontSize: 20,
    fontWeight: 'bold',
    textAlign: 'center',
    flex: 1,
    textTransform: 'uppercase',
    letterSpacing: 1,
  },
  stepIndicator: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 10,
    marginBottom: 10,
  },
  indicatorWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  indicatorCircle: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  indicatorCircleActive: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  indicatorText: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    fontWeight: 'bold',
  },
  indicatorTextActive: {
    color: 'white',
  },
  indicatorLine: {
    width: 40,
    height: 2,
    backgroundColor: theme.colors.surface,
    marginHorizontal: 10,
  },
  indicatorLineActive: {
    backgroundColor: theme.colors.primary,
  },
  scrollContent: {
    padding: 25,
    paddingTop: 10,
    paddingBottom: 100,
  },
  stepContainer: {
    flex: 1,
  },
  sectionHeader: {
    color: 'white',
    fontSize: 16,
    fontWeight: '800',
    marginBottom: 8,
    textTransform: 'uppercase',
    letterSpacing: 1.5,
  },
  sectionSubtext: {
    color: 'rgba(255,255,255,0.5)',
    fontSize: 12,
    marginBottom: 20,
    lineHeight: 18,
  },
  facilityGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  facilityCardOuter: {
    width: (width - 65) / 2,
    height: 160,
    borderRadius: 10,
    marginBottom: 15,
  },
  facilityCardBorder: {
    flex: 1,
    borderRadius: 10,
    padding: 1,
  },
  facilityCardInner: {
    flex: 1,
    borderRadius: 9,
    borderWidth: 1,
    borderColor: '#202222',
    background: 'radial-gradient(circle at 0% 0%, #444444, #0c0d0d)',
    backgroundColor: '#0c0d0d',
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
  },
  facilityRay: {
    position: 'absolute',
    width: 220,
    height: 45,
    borderRadius: 100,
    backgroundColor: 'rgba(199, 199, 199, 0.15)',
    top: -10,
    left: -40,
    transform: [{ rotate: '40deg' }],
  },
  facilityLineTop: {
    position: 'absolute',
    top: '10%',
    left: 0,
    right: 0,
    height: 1,
    backgroundColor: '#888',
    opacity: 0.3,
  },
  facilityLineBottom: {
    position: 'absolute',
    bottom: '10%',
    left: 0,
    right: 0,
    height: 1,
    backgroundColor: '#2c2c2c',
  },
  facilityLineLeft: {
    position: 'absolute',
    top: 0,
    bottom: 0,
    left: '10%',
    width: 1,
    backgroundColor: '#747474',
    opacity: 0.3,
  },
  facilityLineRight: {
    position: 'absolute',
    top: 0,
    bottom: 0,
    right: '10%',
    width: 1,
    backgroundColor: '#2c2c2c',
  },
  facilityDot: {
    position: 'absolute',
    width: 5,
    height: 5,
    borderRadius: 100,
    backgroundColor: '#fff',
    top: '10%',
    right: '10%',
    shadowColor: '#ffffff',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 1,
    shadowRadius: 10,
    elevation: 5,
  },
  facilityIconTiny: {
    marginBottom: 8,
  },
  facilityIconImg: {
    width: 40,
    height: 40,
    marginBottom: 8,
  },
  facilityNameText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  dateScroll: {
    paddingBottom: 10,
  },
  dateCard: {
    width: 54,
    height: 70,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.medium,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 8,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  dateCardActive: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  dayName: {
    color: theme.colors.textSecondary,
    fontSize: 10,
    textTransform: 'uppercase',
  },
  dayDate: {
    color: 'white',
    fontSize: 24,
    fontWeight: 'bold',
    marginVertical: 4,
  },
  monthName: {
    color: theme.colors.textSecondary,
    fontSize: 10,
  },
  dateTextActive: {
    color: 'white',
  },
  courtGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
  },
  courtBtn: {
    width: (width - 75) / 2,
    height: 126,
    borderRadius: theme.borderRadius.medium,
    backgroundColor: theme.colors.surface,
    marginRight: 10,
    marginBottom: 10,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    overflow: 'hidden',
    alignItems: 'center',
    justifyContent: 'flex-end',
  },
  courtBtnActive: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  courtBtnText: {
    color: 'white',
    fontWeight: '600',
    marginBottom: 10,
    textShadowColor: 'rgba(0,0,0,0.75)',
    textShadowOffset: { width: 0, height: 1 },
    textShadowRadius: 4,
  },
  courtBtnTextActive: {
    color: 'white',
  },
  courtImage: {
    ...StyleSheet.absoluteFillObject,
    width: null,
    height: null,
  },
  courtPlaceholder: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: '#171717',
    justifyContent: 'center',
    alignItems: 'center',
  },
  slotGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  slotBtn: {
    width: (width - 70) / 4,
    height: 58,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.small,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 10,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  slotBtnActive: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  slotBtnDisabled: {
    backgroundColor: 'rgba(255,255,255,0.02)',
    borderColor: 'rgba(255,255,255,0.05)',
  },
  slotBtnBooked: {
    backgroundColor: 'rgba(255, 59, 48, 0.08)',
    borderColor: 'rgba(255, 59, 48, 0.25)',
  },
  slotBtnText: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    fontWeight: '600',
  },
  slotBtnTextActive: {
    color: 'white',
  },
  slotBtnTextDisabled: {
    color: 'rgba(255,255,255,0.1)',
  },
  slotHintText: {
    marginTop: 4,
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 0.5,
    color: 'rgba(255,255,255,0.35)',
    textTransform: 'uppercase',
  },
  primaryBtn: {
    marginTop: 30,
    borderRadius: theme.borderRadius.medium,
    overflow: 'hidden',
    ...theme.shadows.medium,
  },
  primaryBtnGradient: {
    height: 44,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  primaryBtnText: {
    color: 'white',
    fontSize: 14,
    fontWeight: 'bold',
    marginRight: 10,
  },
  summaryCard: {
    borderRadius: theme.borderRadius.large,
    overflow: 'hidden',
    ...theme.shadows.heavy,
  },
  summaryGradient: {
    padding: 16,
  },
  summaryDivider: {
    height: 1,
    backgroundColor: 'rgba(255,255,255,0.1)',
    marginVertical: 10,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 6,
  },
  summaryLabel: {
    color: theme.colors.textSecondary,
    fontSize: 12,
  },
  summaryValue: {
    color: 'white',
    fontSize: 12,
    fontWeight: '600',
    flex: 1,
    textAlign: 'right',
    marginLeft: 10,
  },
  totalPrice: {
    color: 'white',
    fontSize: 14,
    fontWeight: 'bold',
  },
  dpPrice: {
    color: theme.colors.primary,
    fontSize: 14,
    fontWeight: 'bold',
  },
  remainingPrice: {
    color: 'rgba(255,255,255,0.85)',
    fontSize: 13,
    fontWeight: '800',
  },
  staffEmptyText: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    marginTop: -8,
    marginBottom: 8,
  },
  staffPillsWrap: {
    gap: 10,
  },
  staffPill: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: 12,
    backgroundColor: 'rgba(255,255,255,0.03)',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  staffPillMissing: {
    backgroundColor: 'rgba(255, 149, 0, 0.06)',
    borderColor: 'rgba(255, 149, 0, 0.18)',
  },
  staffPillText: {
    flex: 1,
    color: 'white',
    fontSize: 12,
    fontWeight: '700',
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
  uploadMainText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
    marginTop: 15,
    marginBottom: 5,
  },
  uploadSubText: {
    color: theme.colors.textTertiary,
    fontSize: 12,
  },
  uploadedImg: {
    width: '100%',
    height: '100%',
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
    fontSize: 12,
    textAlign: 'center',
    lineHeight: 18,
    marginBottom: 10,
  },
  paymentDescBold: {
    color: theme.colors.primary,
    fontSize: 12,
    textAlign: 'center',
    lineHeight: 18,
    fontWeight: 'bold',
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
});

export default FieldReservationScreen;
