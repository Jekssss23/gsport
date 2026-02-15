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
import { BookingService } from '../../services/BookingService';

const RatingScreen = ({ 
  visible, 
  booking, 
  onClose, 
  onSubmit 
}) => {
  const [rating, setRating] = useState('');
  const [review, setReview] = useState('');
  const [loading, setLoading] = useState(false);

  const emoteOptions = [
    { value: 'angry', emote: '😡', label: 'Sangat Buruk' },
    { value: 'neutral', emote: '😑', label: 'Cukup Baik' },
    { value: 'happy', emote: '☺️', label: 'Baik' },
    { value: 'very_happy', emote: '😁', label: 'Sangat Baik' },
    { value: 'love', emote: '😍', label: 'Luar Biasa' }
  ];

  const handleRating = (value) => {
    setRating(value);
  };

  const handleSubmit = async () => {
    if (!rating) {
      Alert.alert('Error', 'Silakan pilih rating terlebih dahulu');
      return;
    }

    setLoading(true);
    try {
      const reviewData = {
        bookingId: booking.id,
        userId: booking.userId,
        facilityId: booking.courtId,
        facilityName: booking.facilityName,
        rating,
        review: review.trim(),
        userName: booking.userName,
        bookingDate: booking.date,
        ratingType: 'emote'
      };

      await BookingService.addReview(reviewData);
      onSubmit && onSubmit();
      onClose();
      Alert.alert('Terima Kasih!', 'Rating dan ulasan Anda berhasil disimpan.');
    } catch (error) {
      console.error('Error submitting review:', error);
      Alert.alert('Error', 'Gagal menyimpan rating. Silakan coba lagi.');
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
            <Text style={styles.facilityName}>{booking?.facilityName}</Text>
            <Text style={styles.bookingDetails}>
              {booking?.courtName} • {booking?.date}
            </Text>
          </View>

          <View style={styles.ratingSection}>
            <Text style={styles.sectionTitle}>Bagaimana pengalaman Anda?</Text>
            {renderEmotes()}
            <Text style={styles.ratingLabel}>
              {rating && emoteOptions.find(opt => opt.value === rating)?.label}
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
