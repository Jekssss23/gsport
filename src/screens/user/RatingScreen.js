import React, { useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  TextInput,
  Alert,
  StyleSheet,
  ScrollView,
  ActivityIndicator,
  Modal
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../../styles/theme';
import { auth } from '../../config/firebase';
import { BookingService } from '../../services/BookingService';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';

const RatingScreen = ({
  visible,
  booking,
  onClose,
  onSubmit
}) => {
  const [rating, setRating] = useState('');
  const [staffRating, setStaffRating] = useState(0); // Star rating 1-5
  const [review, setReview] = useState('');
  const [loading, setLoading] = useState(false);

  const [staffName, setStaffName] = useState(
    booking?.staffOnDutyName || booking?.assigned_staff_name || 'Petugas Jaga'
  );
  const [staffId, setStaffId] = useState(booking?.staffOnDutyId || booking?.assigned_staff_id || '');
  const [staffLoading, setStaffLoading] = useState(false);

  const emoteOptions = [
    { value: 1, emote: '😡', label: 'Sangat Buruk' },
    { value: 2, emote: '😑', label: 'Kurang Baik' },
    { value: 3, emote: '☺️', label: 'Cukup Baik' },
    { value: 4, emote: '😁', label: 'Baik' },
    { value: 5, emote: '😍', label: 'Luar Biasa' }
  ];

  React.useEffect(() => {
    if (!visible || !booking) return;

    setRating('');
    setStaffRating(0);
    setReview('');
    setStaffName(booking.staffOnDutyName || booking.assigned_staff_name || 'Petugas Jaga');
    setStaffId(booking.staffOnDutyId || booking.assigned_staff_id || '');

    const fetchStaff = async () => {
      if (booking.staffOnDutyName || booking.assigned_staff_name) return;

      setStaffLoading(true);
      try {
        const date = booking.date || booking.booking_date;
        const slots = booking.timeSlots || booking.time_slots || [];
        const hour = Array.isArray(slots) && slots.length > 0 ? Math.min(...slots.map(Number)) : null;
        const facility = booking.facilityName || booking.facility_name || '';

        if (date && hour !== null && facility) {
          const staff = await BookingService.getStaffBySlot(date, hour, facility);
          if (staff?.employeeName) {
            setStaffName(staff.employeeName);
            setStaffId(staff.employeeId || '');
          }
        }
      } catch (e) {
        console.error('Error fetching staff for rating:', e);
      } finally {
        setStaffLoading(false);
      }
    };

    fetchStaff();
  }, [visible, booking?.id]);

  const handleRating = (value) => {
    setRating(value);
  };

  const handleSubmit = async () => {
    if (!rating) {
      Alert.alert('Error', 'Silakan pilih rating pengalaman (emote) terlebih dahulu');
      return;
    }

    if (!staffRating || staffRating < 1) {
      Alert.alert('Error', 'Silakan beri rating bintang untuk petugas jaga (1–5 bintang)');
      return;
    }

    if (!booking || !booking.id) {
      Alert.alert('Error', 'Data booking tidak valid. Silakan coba lagi.');
      return;
    }

    setLoading(true);
    try {
      const reviewData = {
        bookingId: String(booking.id),
        userId: booking.userId || booking.firebase_uid || auth.currentUser?.uid,
        facilityId: booking.courtId || booking.facility_id,
        facilityName: booking.facilityName || booking.facility_name,
        rating: Number(rating),
        staffRating: Number(staffRating),
        staffOnDutyId: staffId || '',
        staffOnDutyName: staffName || 'Petugas Jaga',
        review: (review && typeof review === 'string') ? review.trim() : '',
        userName: booking.userName || booking.user_name || auth.currentUser?.email || '',
        bookingDate: booking.date || booking.booking_date,
        ratingType: 'emote_with_staff_star',
      };

      await BookingService.addReview(reviewData);
      onSubmit && onSubmit();
      onClose();
      Alert.alert('Terima Kasih!', 'Rating dan ulasan Anda berhasil disimpan.');
    } catch (error) {
      console.error('Error submitting review:', error);
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal menyimpan rating. Silakan coba lagi.'));
    } finally {
      setLoading(false);
    }
  };

  const renderEmotes = () => {
    return (
      <View style={styles.emoteContainer}>
        {emoteOptions.map((option) => (
          <TouchableOpacity
            key={option.value}
            style={[styles.emoteButton, rating === option.value && styles.selectedEmoteButton]}
            onPress={() => handleRating(option.value)}
          >
            <Text style={styles.emoteText}>{option.emote}</Text>
            <Text style={[styles.emoteLabel, rating === option.value && styles.selectedEmoteLabel]}>
              {option.label}
            </Text>
          </TouchableOpacity>
        ))}
      </View>
    );
  };

  return (
    <Modal
      visible={visible}
      animationType="slide"
      presentationStyle="pageSheet"
    >
      <View style={styles.container}>
        <LinearGradient
          colors={[theme.colors.primary, theme.colors.secondary]}
          style={styles.header}
        >
          <View style={styles.headerContent}>
            <TouchableOpacity onPress={onClose} style={styles.closeButton}>
              <Ionicons name="close" size={24} color="white" />
            </TouchableOpacity>
            <Text style={styles.headerTitle}>Beri Rating</Text>
            <View style={styles.placeholder} />
          </View>
        </LinearGradient>

        <ScrollView contentContainerStyle={styles.content}>
          <View style={styles.bookingInfo}>
            <Text style={styles.facilityName}>{booking?.facilityName || booking?.facility_name}</Text>
            <Text style={styles.bookingDetails}>
              {(booking?.courtName || booking?.court_name || '-')} • {(booking?.date || booking?.booking_date || '-')}
            </Text>
          </View>

          <View style={styles.ratingSection}>
            <Text style={styles.sectionTitle}>Bagaimana pengalaman Anda?</Text>
            {renderEmotes()}
            <Text style={styles.ratingLabel}>
              {rating && emoteOptions.find(opt => opt.value === rating)?.label}
            </Text>
          </View>

          <View style={styles.staffRatingSection}>
            <Text style={styles.sectionTitle}>Rating Petugas Jaga (Bintang)</Text>
            {staffLoading ? (
              <ActivityIndicator color={theme.colors.primary} style={{ marginBottom: 12 }} />
            ) : (
              <Text style={styles.staffNameText}>{staffName}</Text>
            )}
            <View style={styles.starContainer}>
              {[1, 2, 3, 4, 5].map((star) => (
                <TouchableOpacity
                  key={star}
                  onPress={() => setStaffRating(star)}
                  style={styles.starButton}
                  accessibilityLabel={`${star} bintang`}
                >
                  <Ionicons
                    name={star <= staffRating ? 'star' : 'star-outline'}
                    size={40}
                    color={star <= staffRating ? '#FFD700' : '#999'}
                  />
                </TouchableOpacity>
              ))}
            </View>
            <Text style={styles.ratingLabel}>
              {staffRating > 0 ? `${staffRating} dari 5 bintang` : 'Wajib: ketuk bintang untuk petugas jaga'}
            </Text>
          </View>

          <View style={styles.reviewSection}>
            <Text style={styles.sectionTitle}>Tulis Ulasan (Opsional)</Text>
            <TextInput
              style={styles.reviewInput}
              placeholder="Bagikan pengalaman Anda..."
              placeholderTextColor="#999"
              multiline
              numberOfLines={4}
              value={review}
              onChangeText={setReview}
              textAlignVertical="top"
            />
          </View>

          <TouchableOpacity
            style={[styles.submitButton, loading && styles.disabledButton]}
            onPress={handleSubmit}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="white" size="small" />
            ) : (
              <Text style={styles.submitButtonText}>Kirim Rating</Text>
            )}
          </TouchableOpacity>
        </ScrollView>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  header: {
    paddingTop: 50,
    paddingBottom: 20,
    paddingHorizontal: 20,
  },
  headerContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  closeButton: {
    padding: 5,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: 'white',
  },
  placeholder: {
    width: 34,
  },
  content: {
    padding: 20,
  },
  bookingInfo: {
    backgroundColor: theme.colors.cardBackground,
    padding: 16,
    borderRadius: 12,
    marginBottom: 24,
    alignItems: 'center',
  },
  facilityName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 4,
  },
  bookingDetails: {
    fontSize: 14,
    color: theme.colors.textSecondary,
  },
  ratingSection: {
    alignItems: 'center',
    marginBottom: 32,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: theme.colors.text,
    marginBottom: 16,
    textAlign: 'center',
  },
  emoteContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginBottom: 16,
  },
  emoteButton: {
    alignItems: 'center',
    padding: 12,
    borderRadius: 12,
    backgroundColor: 'rgba(255,255,255,0.1)',
    minWidth: 60,
  },
  selectedEmoteButton: {
    backgroundColor: 'rgba(255,0,0,0.2)',
    borderWidth: 2,
    borderColor: theme.colors.primary,
  },
  emoteText: {
    fontSize: 32,
    marginBottom: 4,
  },
  emoteLabel: {
    fontSize: 10,
    color: theme.colors.textSecondary,
    textAlign: 'center',
  },
  selectedEmoteLabel: {
    color: theme.colors.primary,
    fontWeight: 'bold',
  },
  ratingLabel: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    textAlign: 'center',
    marginTop: 8,
  },
  staffRatingSection: {
    alignItems: 'center',
    marginBottom: 32,
    padding: 16,
    backgroundColor: 'rgba(255,255,255,0.05)',
    borderRadius: 12,
  },
  staffNameText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.primary,
    marginBottom: 12,
  },
  starContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 8,
  },
  starButton: {
    padding: 4,
  },
  reviewSection: {
    marginBottom: 32,
  },
  reviewInput: {
    backgroundColor: theme.colors.cardBackground,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: 12,
    padding: 16,
    fontSize: 16,
    color: theme.colors.text,
    minHeight: 100,
  },
  submitButton: {
    backgroundColor: theme.colors.primary,
    paddingVertical: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  disabledButton: {
    opacity: 0.6,
  },
  submitButtonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
});

export default RatingScreen;
