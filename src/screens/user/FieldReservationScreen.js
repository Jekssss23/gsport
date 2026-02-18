import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  Image,
  TextInput,
  ActivityIndicator,
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { collection, getDocs, query, where } from 'firebase/firestore';
import { db } from '../../config/firebase.js';
import { BookingService } from '../../services/BookingService';
import { CloudinaryService } from '../../services/CloudinaryService';
import { theme } from '../../styles/theme';

const FieldReservationScreen = ({ navigation, route }) => {
  const { user } = route.params || {};

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
  const [staffMap, setStaffMap] = useState({}); // Mapping hour -> staff object
  const [step, setStep] = useState(1); // 1: Facility, 2: Court & Time, 3: Payment

  useEffect(() => {
    if (!user) {
      Alert.alert('Error', 'User not found', [
        { text: 'OK', onPress: () => navigation.goBack() }
      ]);
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
      console.log('Loading facilities...');
      const facilities = await BookingService.getFacilities();
      console.log('Facilities loaded:', facilities.length);
      setFacilities(facilities);
    } catch (error) {
      console.error('Error loading facilities:', error);
      Alert.alert('Error', 'Failed to load facilities: ' + error.message);
    } finally {
      setLoadingFacilities(false);
    }
  };

  const loadAvailableSlots = async () => {
    try {
      setLoadingSlots(true);
      // Get all slots (7-23)
      const allSlots = Array.from({ length: 17 }, (_, i) => i + 7);

      // Get booked slots
      const availableSlots = await BookingService.getAvailableSlots(selectedCourt.id, selectedDate);

      // Create slot objects with availability status
      const slotsWithStatus = allSlots.map(hour => ({
        hour,
        isAvailable: availableSlots.includes(hour)
      }));

      setAvailableSlots(slotsWithStatus);
      setSelectedSlots([]); // Reset selected slots when loading new availability
      setStaffMap({}); // Reset staff map

      // Pre-load staff for available slots
      const newStaffMap = {};
      for (const slot of availableSlots) {
        const staff = await BookingService.getStaffBySlot(selectedDate, slot, selectedFacility.name);
        if (staff) {
          newStaffMap[slot] = staff;
        }
      }
      setStaffMap(newStaffMap);

    } catch (error) {
      console.error('Error loading slots:', error);
      Alert.alert('Error', 'Failed to load available slots');
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
    // Don't change step - stay on same page
  };

  const toggleTimeSlot = (slotObj) => {
    // Don't allow selecting unavailable slots
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
      throw new Error('Failed to upload payment proof: ' + error.message);
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
      Alert.alert('Error', 'Please upload payment proof');
      return;
    }

    if (selectedSlots.length === 0) {
      Alert.alert('Error', 'Please select at least one time slot');
      return;
    }

    setLoading(true);
    try {
      // Check if slots are still available before uploading
      const currentAvailableSlots = await BookingService.getAvailableSlots(selectedCourt.id, selectedDate);
      const allSlotsStillAvailable = selectedSlots.every(slot => currentAvailableSlots.includes(slot));

      if (!allSlotsStillAvailable) {
        Alert.alert('Error', 'Some time slots are no longer available. Please select different slots.');
        setLoading(false);
        setStep(3);
        return;
      }

      const paymentProofUrl = await uploadPaymentProof();
      const { total, dp, remaining } = calculateTotal();

      // Ambil staff dari slot pertama yang dipilih (asumsi staff sama jika slot berurutan)
      const firstSlot = selectedSlots[0];
      const assignedStaff = staffMap[firstSlot];

      const bookingData = {
        userId: user.uid || user.email,
        userName: user.displayName || user.email,
        userPhone: user.phoneNumber || '',
        facilityId: selectedFacility.id,
        facilityName: selectedFacility.name,
        courtId: selectedCourt.id,
        courtName: selectedCourt.name,
        date: selectedDate,
        timeSlots: selectedSlots,
        totalHours: selectedSlots.length,
        pricePerHour: selectedFacility.pricePerHour,
        totalAmount: total,
        dpAmount: dp,
        remainingAmount: remaining,
        paymentProof: paymentProofUrl,
        status: 'pending',
        staffOnDutyId: assignedStaff?.employeeId || null,
        staffOnDutyName: assignedStaff?.employeeName || 'No staff assigned',
        staffOnDutyImageUrl: assignedStaff?.employeeImageUrl || ''
      };

      await BookingService.createBooking(bookingData);

      Alert.alert(
        'Success!',
        'Your booking has been submitted and is pending approval. Check your reservation history for updates.',
        [{
          text: 'View History',
          onPress: () => navigation.navigate('MyReservationHistory')
        },
        {
          text: 'OK',
          onPress: () => navigation.goBack()
        }]
      );
    } catch (error) {
      console.error('Booking error:', error);
      Alert.alert('Error', error.message || 'Failed to submit booking. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const renderFacilitySelection = () => (
    <View style={styles.stepContainer}>
      <Text style={styles.stepTitle}>Select Facility</Text>
      {loadingFacilities ? (
        <ActivityIndicator size="large" color={theme.colors.primary} style={{ marginTop: 20 }} />
      ) : facilities.length === 0 ? (
        <View>
          <Text style={styles.emptyText}>No facilities available</Text>
          <TouchableOpacity
            style={styles.retryButton}
            onPress={loadFacilities}
          >
            <Text style={styles.retryButtonText}>Retry</Text>
          </TouchableOpacity>
        </View>
      ) : (
        facilities.map(facility => (
          <TouchableOpacity
            key={facility.id}
            style={styles.facilityCard}
            onPress={() => selectFacility(facility)}
          >
            <Text style={styles.facilityName}>{facility.name}</Text>
            <Text style={styles.facilityPrice}>Rp {facility.pricePerHour?.toLocaleString() || 0}/hour</Text>
            <Text style={styles.facilityType}>{facility.courts?.length || 0} {facility.type}s available</Text>
          </TouchableOpacity>
        ))
      )}
    </View>
  );

  const renderCourtAndTimeSelection = () => (
    <View style={styles.stepContainer}>
      <Text style={styles.stepTitle}>{selectedFacility.name}</Text>
      <Text style={styles.subtitle}>Select Court & Time Slots</Text>

      {/* Court Selection */}
      <Text style={styles.sectionLabel}>Choose Court:</Text>
      <View style={styles.courtGrid}>
        {selectedFacility.courts.map(court => (
          <TouchableOpacity
            key={court.id}
            style={[
              styles.courtCard,
              selectedCourt?.id === court.id && styles.selectedCourtCard
            ]}
            onPress={() => selectCourt(court)}
          >
            <Text style={[
              styles.courtName,
              selectedCourt?.id === court.id && styles.selectedCourtName
            ]}>
              {court.name}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Date Selection */}
      {selectedCourt && (
        <>
          <Text style={styles.sectionLabel}>Date:</Text>
          <View style={styles.dateContainer}>
            <Text style={styles.dateText}>{selectedDate}</Text>
          </View>

          {/* Time Slots */}
          <Text style={styles.sectionLabel}>Available Time Slots:</Text>

          {/* Legend */}
          <View style={styles.legendContainer}>
            <View style={styles.legendItem}>
              <View style={[styles.legendBox, styles.legendAvailable]} />
              <Text style={styles.legendText}>Available</Text>
            </View>
            <View style={styles.legendItem}>
              <View style={[styles.legendBox, styles.legendSelected]} />
              <Text style={styles.legendText}>Selected</Text>
            </View>
            <View style={styles.legendItem}>
              <View style={[styles.legendBox, styles.legendBooked]} />
              <Text style={styles.legendText}>Booked</Text>
            </View>
          </View>

          {loadingSlots ? (
            <ActivityIndicator size="large" color={theme.colors.primary} style={{ marginTop: 20 }} />
          ) : availableSlots.length === 0 ? (
            <Text style={styles.emptyText}>No time slots available</Text>
          ) : (
            <View style={styles.timeGrid}>
              {availableSlots.map(slotObj => (
                <TouchableOpacity
                  key={slotObj.hour}
                  style={[
                    styles.timeSlot,
                    selectedSlots.includes(slotObj.hour) && styles.selectedTimeSlot,
                    !slotObj.isAvailable && styles.bookedTimeSlot
                  ]}
                  onPress={() => toggleTimeSlot(slotObj)}
                  disabled={!slotObj.isAvailable}
                  activeOpacity={slotObj.isAvailable ? 0.7 : 1}
                >
                  <Text style={[
                    styles.timeSlotText,
                    selectedSlots.includes(slotObj.hour) && styles.selectedTimeSlotText,
                    !slotObj.isAvailable && styles.bookedTimeSlotText
                  ]}>
                    {slotObj.hour}:00
                  </Text>
                  {!slotObj.isAvailable && (
                    <Text style={styles.bookedLabel}>Booked</Text>
                  )}
                </TouchableOpacity>
              ))}
            </View>
          )}

          {/* Booking Summary */}
          {selectedSlots.length > 0 && (
            <View style={styles.summaryCard}>
              <Text style={styles.summaryTitle}>Booking Summary</Text>
              <Text style={styles.summaryText}>Court: {selectedCourt.name}</Text>
              <Text style={styles.summaryText}>Date: {selectedDate}</Text>
              <Text style={styles.summaryText}>Time: {selectedSlots.sort((a, b) => a - b).map(h => `${h}:00`).join(', ')}</Text>
              <Text style={styles.summaryStaffText}>
                Staff Jaga: {selectedSlots.map(h => staffMap[h]?.employeeName || 'Pending').filter((v, i, a) => a.indexOf(v) === i).join(', ')}
              </Text>
              <Text style={styles.summaryText}>Duration: {selectedSlots.length} hour(s)</Text>
              <View style={styles.divider} />
              <Text style={styles.summaryText}>Total: Rp {calculateTotal().total.toLocaleString()}</Text>
              <Text style={styles.summaryTextHighlight}>DP Required: Rp {calculateTotal().dp.toLocaleString()}</Text>

              <TouchableOpacity
                style={styles.continueButton}
                onPress={() => setStep(3)}
              >
                <Text style={styles.continueButtonText}>Continue to Payment</Text>
              </TouchableOpacity>
            </View>
          )}
        </>
      )}
    </View>
  );

  const renderPayment = () => (
    <View style={styles.stepContainer}>
      <Text style={styles.stepTitle}>Payment</Text>

      <View style={styles.paymentCard}>
        <Text style={styles.paymentTitle}>Down Payment Required</Text>
        <Text style={styles.paymentAmount}>Rp {calculateTotal().dp.toLocaleString()}</Text>

        <View style={styles.qrisContainer}>
          <Text style={styles.qrisTitle}>Scan QRIS Code</Text>
          <Image
            source={require('../../../assets/images/qris.jpg')}
            style={styles.qrisImage}
            resizeMode="contain"
          />
        </View>

        <Text style={styles.uploadTitle}>Upload Payment Proof</Text>
        <TouchableOpacity style={styles.uploadButton} onPress={pickImage}>
          <Text style={styles.uploadButtonText}>
            {paymentProof ? 'Change Image' : 'Select Image'}
          </Text>
        </TouchableOpacity>

        {paymentProof && (
          <Image source={{ uri: paymentProof.uri }} style={styles.proofImage} />
        )}

        <TouchableOpacity
          style={[styles.submitButton, (!paymentProof || loading) && styles.disabledButton]}
          onPress={submitBooking}
          disabled={!paymentProof || loading}
        >
          {loading ? (
            <ActivityIndicator color="white" />
          ) : (
            <Text style={styles.submitButtonText}>Submit Booking</Text>
          )}
        </TouchableOpacity>
      </View>
    </View>
  );

  return (
    <ScrollView style={styles.container}>
      {!user ? (
        <View style={styles.centerContainer}>
          <Text style={styles.errorText}>User not found</Text>
        </View>
      ) : (
        <>
          {step === 1 && renderFacilitySelection()}
          {step === 2 && renderCourtAndTimeSelection()}
          {step === 3 && renderPayment()}

          {step > 1 && (
            <TouchableOpacity
              style={styles.backButton}
              onPress={() => setStep(step - 1)}
            >
              <Text style={styles.backButtonText}>Back</Text>
            </TouchableOpacity>
          )}
        </>
      )}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: theme.spacing.xlarge,
  },
  errorText: {
    fontSize: 18,
    color: theme.colors.error,
    textAlign: 'center',
  },
  stepContainer: {
    padding: theme.spacing.medium,
  },
  stepTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.medium,
  },
  subtitle: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.medium,
  },
  facilityCard: {
    backgroundColor: theme.colors.cardBackground,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    marginBottom: theme.spacing.small,
    borderWidth: 1,
    borderColor: theme.colors.primary,
  },
  facilityName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  facilityPrice: {
    fontSize: 16,
    color: theme.colors.primary,
    marginTop: 4,
  },
  facilityType: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginTop: 4,
  },
  courtGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
    marginBottom: theme.spacing.medium,
  },
  courtCard: {
    backgroundColor: theme.colors.cardBackground,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    flex: 1,
    minWidth: '45%',
    borderWidth: 2,
    borderColor: theme.colors.inputBorder,
  },
  selectedCourtCard: {
    borderColor: theme.colors.primary,
    backgroundColor: 'rgba(255, 0, 0, 0.1)',
  },
  courtName: {
    fontSize: 16,
    color: theme.colors.text,
    textAlign: 'center',
  },
  selectedCourtName: {
    color: theme.colors.primary,
    fontWeight: 'bold',
  },
  sectionLabel: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginTop: theme.spacing.medium,
    marginBottom: theme.spacing.small,
  },
  dateContainer: {
    backgroundColor: theme.colors.cardBackground,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    borderWidth: 1,
    borderColor: theme.colors.primary,
    marginBottom: theme.spacing.small,
  },
  dateText: {
    fontSize: 16,
    color: theme.colors.text,
    textAlign: 'center',
  },
  legendContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginBottom: theme.spacing.medium,
    paddingVertical: theme.spacing.small,
    backgroundColor: 'rgba(255,255,255,0.05)',
    borderRadius: theme.borderRadius.small,
  },
  legendItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  legendBox: {
    width: 16,
    height: 16,
    borderRadius: 4,
    marginRight: 6,
    borderWidth: 1,
  },
  legendAvailable: {
    backgroundColor: theme.colors.cardBackground,
    borderColor: theme.colors.inputBorder,
  },
  legendSelected: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  legendBooked: {
    backgroundColor: '#1a1a1a',
    borderColor: '#444',
    opacity: 0.5,
  },
  legendText: {
    fontSize: 12,
    color: theme.colors.textSecondary,
  },
  timeGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  timeSlot: {
    width: '22%',
    backgroundColor: theme.colors.cardBackground,
    padding: theme.spacing.small,
    borderRadius: theme.borderRadius.small,
    marginBottom: theme.spacing.small,
    borderWidth: 1,
    borderColor: theme.colors.inputBorder,
    minHeight: 50,
    justifyContent: 'center',
    alignItems: 'center',
  },
  selectedTimeSlot: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  bookedTimeSlot: {
    backgroundColor: '#1a1a1a',
    borderColor: '#444',
    opacity: 0.5,
  },
  timeSlotText: {
    color: theme.colors.text,
    textAlign: 'center',
    fontSize: 14,
    fontWeight: '600',
  },
  selectedTimeSlotText: {
    color: theme.colors.buttonText,
  },
  bookedTimeSlotText: {
    color: '#666',
    textDecorationLine: 'line-through',
  },
  bookedLabel: {
    fontSize: 10,
    color: '#888',
    marginTop: 2,
    fontWeight: 'bold',
  },
  summaryCard: {
    backgroundColor: theme.colors.cardBackground,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    marginTop: theme.spacing.medium,
    borderWidth: 1,
    borderColor: theme.colors.primary,
  },
  summaryTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.small,
  },
  summaryText: {
    fontSize: 16,
    color: theme.colors.text,
    marginBottom: 4,
  },
  summaryTextHighlight: {
    fontSize: 18,
    color: theme.colors.primary,
    fontWeight: 'bold',
    marginTop: 4,
  },
  summaryStaffText: {
    fontSize: 14,
    color: '#FFD700', // Gold color for staff info
    fontWeight: 'bold',
    marginVertical: 4,
  },
  divider: {
    height: 1,
    backgroundColor: 'rgba(255,255,255,0.2)',
    marginVertical: theme.spacing.small,
  },
  continueButton: {
    backgroundColor: theme.colors.primary,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    marginTop: theme.spacing.medium,
  },
  continueButtonText: {
    color: theme.colors.buttonText,
    textAlign: 'center',
    fontSize: 16,
    fontWeight: 'bold',
  },
  paymentCard: {
    backgroundColor: theme.colors.cardBackground,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    borderWidth: 1,
    borderColor: theme.colors.primary,
  },
  paymentTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
    textAlign: 'center',
  },
  paymentAmount: {
    fontSize: 24,
    fontWeight: 'bold',
    color: theme.colors.primary,
    textAlign: 'center',
    marginVertical: theme.spacing.small,
  },
  qrisContainer: {
    alignItems: 'center',
    marginVertical: theme.spacing.medium,
  },
  qrisTitle: {
    fontSize: 16,
    color: theme.colors.text,
    marginBottom: theme.spacing.small,
  },
  qrisImage: {
    width: 200,
    height: 200,
    borderRadius: theme.borderRadius.small,
  },
  uploadTitle: {
    fontSize: 16,
    color: theme.colors.text,
    marginBottom: theme.spacing.small,
  },
  uploadButton: {
    backgroundColor: theme.colors.secondary,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    marginBottom: theme.spacing.small,
  },
  uploadButtonText: {
    color: theme.colors.text,
    textAlign: 'center',
    fontSize: 16,
  },
  proofImage: {
    width: '100%',
    height: 200,
    borderRadius: theme.borderRadius.small,
    marginBottom: theme.spacing.medium,
  },
  submitButton: {
    backgroundColor: theme.colors.primary,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
  },
  disabledButton: {
    opacity: 0.6,
  },
  submitButtonText: {
    color: theme.colors.buttonText,
    textAlign: 'center',
    fontSize: 16,
    fontWeight: 'bold',
  },
  backButton: {
    backgroundColor: theme.colors.secondary,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    margin: theme.spacing.medium,
  },
  backButtonText: {
    color: theme.colors.text,
    textAlign: 'center',
    fontSize: 16,
  },
  emptyText: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    textAlign: 'center',
    marginTop: theme.spacing.large,
  },
  retryButton: {
    backgroundColor: theme.colors.primary,
    padding: theme.spacing.medium,
    borderRadius: theme.borderRadius.medium,
    marginTop: theme.spacing.medium,
    alignSelf: 'center',
    paddingHorizontal: theme.spacing.xlarge,
  },
  retryButtonText: {
    color: theme.colors.buttonText,
    fontSize: 16,
    fontWeight: 'bold',
  },
});

export default FieldReservationScreen;