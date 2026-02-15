import React, { useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  TextInput,
  Alert,
  StyleSheet,
  Modal,
  ScrollView,
  ActivityIndicator
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../../styles/theme';
import { BookingService } from '../../services/BookingService';

const CancellationRequestScreen = ({
  visible,
  booking,
  onClose,
  onSubmit
}) => {
  const [reason, setReason] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async () => {
    if (!reason.trim()) {
      Alert.alert('Error', 'Silakan isi alasan pembatalan terlebih dahulu');
      return;
    }

    Alert.alert(
      'Konfirmasi Pembatalan',
      'Pembatalan dilakukan maka pengembalian DP hanya setengah. Apakah Anda yakin ingin membatalkan booking ini?',
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Ya, Batalkan',
          style: 'destructive',
          onPress: async () => {
            setLoading(true);
            try {
              await BookingService.requestCancellation(booking.id, reason.trim());
              onSubmit && onSubmit();
              onClose();
              Alert.alert(
                'Pengajuan Pembatalan Terkirim',
                'Pengajuan pembatalan Anda telah dikirim. Menunggu persetujuan admin.'
              );
            } catch (error) {
              console.error('Error requesting cancellation:', error);
              Alert.alert('Error', 'Gagal mengajukan pembatalan. Silakan coba lagi.');
            } finally {
              setLoading(false);
            }
          }
        }
      ]
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
            <Text style={styles.headerTitle}>Ajukan Pembatalan</Text>
            <View style={styles.placeholder} />
          </View>
        </LinearGradient>

        <ScrollView contentContainerStyle={styles.content}>
          <View style={styles.bookingInfo}>
            <Text style={styles.facilityName}>{booking?.facilityName}</Text>
            <Text style={styles.bookingDetails}>
              {booking?.courtName} • {booking?.date}
            </Text>
            <Text style={styles.bookingDetails}>
              {booking?.timeSlots?.sort((a, b) => a - b).map(slot => `${slot}:00`).join(', ')}
            </Text>
          </View>

          <View style={styles.warningBox}>
            <Ionicons name="warning" size={24} color="#FF4444" />
            <Text style={styles.warningText}>
              Pembatalan dilakukan maka pengembalian DP hanya setengah
            </Text>
          </View>

          <View style={styles.reasonSection}>
            <Text style={styles.sectionTitle}>Alasan Pembatalan</Text>
            <Text style={styles.sectionSubtitle}>
              Jelaskan mengapa Anda ingin membatalkan booking ini
            </Text>
            <TextInput
              style={styles.reasonInput}
              placeholder="Masukkan alasan pembatalan..."
              placeholderTextColor="#999"
              multiline
              numberOfLines={4}
              value={reason}
              onChangeText={setReason}
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
              <Text style={styles.submitButtonText}>Ajukan Pembatalan</Text>
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
    marginBottom: 2,
  },
  warningBox: {
    backgroundColor: 'rgba(255, 68, 68, 0.1)',
    borderWidth: 1,
    borderColor: '#FF4444',
    borderRadius: 12,
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 24,
    gap: 12,
  },
  warningText: {
    fontSize: 15,
    color: '#FF4444',
    flex: 1,
    fontWeight: 'bold',
  },
  reasonSection: {
    marginBottom: 32,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: theme.colors.text,
    marginBottom: 8,
  },
  sectionSubtitle: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginBottom: 16,
  },
  reasonInput: {
    backgroundColor: theme.colors.cardBackground,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: 12,
    padding: 16,
    fontSize: 16,
    color: theme.colors.text,
    minHeight: 120,
  },
  submitButton: {
    backgroundColor: '#FF4444',
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

export default CancellationRequestScreen;
