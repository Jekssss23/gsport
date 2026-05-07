import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  Alert,
  ActivityIndicator,
  Modal,
  TextInput,
  FlatList,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../../styles/theme';
import { ClassScheduleService } from '../../services/ClassScheduleService';
import { auth } from '../../config/firebase';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';

const ClassScheduleScreen = ({ navigation }) => {
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [classes, setClasses] = useState([]);
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [showBookingModal, setShowBookingModal] = useState(false);
  const [selectedClass, setSelectedClass] = useState(null);
  const [bookingLoading, setBookingLoading] = useState(false);
  const [participantName, setParticipantName] = useState('');
  const [participantPhone, setParticipantPhone] = useState('');
  const [expandedClass, setExpandedClass] = useState(null);
  const [loadError, setLoadError] = useState('');

  useEffect(() => {
    loadData();
  }, [selectedCategory]);

  const loadData = async () => {
    try {
      setLoading(true);
      setLoadError('');
      await Promise.all([
        loadCategories(),
        loadClasses(),
      ]);
    } catch (error) {
      console.error('Error loading data:', error);
      setLoadError(error?.message || 'Gagal memuat data kelas.');
    } finally {
      setLoading(false);
    }
  };

  const loadCategories = async () => {
    try {
      const cats = await ClassScheduleService.getCategories();
      setCategories(cats);
    } catch (error) {
      console.error('Error loading categories:', error);
    }
  };

  const loadClasses = async () => {
    try {
      const user = auth.currentUser;
      const availableClasses = await ClassScheduleService.getAvailableClasses(
        null,
        selectedCategory,
        user?.email
      );
      setClasses(availableClasses);
    } catch (error) {
      console.error('Error loading classes:', error);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadData();
    setRefreshing(false);
  };

  const handleBookClass = (classItem) => {
    setSelectedClass(classItem);
    const user = auth.currentUser;
    setParticipantName(user.displayName || user.email || '');
    setShowBookingModal(true);
  };

  const handleBookingSubmit = async () => {
    if (!participantName.trim()) {
      Alert.alert('Error', 'Nama peserta harus diisi');
      return;
    }

    setBookingLoading(true);
    try {
      const user = auth.currentUser;
      const bookingData = {
        firebase_uid: user.uid,
        class_session_id: selectedClass.id,
        participant_name: participantName.trim(),
        participant_phone: participantPhone.trim() || null,
      };

      await ClassScheduleService.bookClass(bookingData);
      
      Alert.alert(
        'Berhasil!',
        'Kelas berhasil dibooking. Silakan melakukan pembayaran untuk konfirmasi.',
        [
          {
            text: 'OK',
            onPress: () => {
              setShowBookingModal(false);
              setSelectedClass(null);
              setParticipantName('');
              setParticipantPhone('');
              loadClasses();
            },
          },
        ]
      );
    } catch (error) {
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal melakukan booking kelas.'));
    } finally {
      setBookingLoading(false);
    }
  };

  const renderClassItem = ({ item }) => {
    const isExpanded = expandedClass === item.id;
    
    return (
      <TouchableOpacity 
        style={[styles.classCard, item.is_member && styles.memberClassCard]}
        onPress={() => setExpandedClass(isExpanded ? null : item.id)}
        activeOpacity={0.7}
      >
        <View style={styles.classHeader}>
          <View style={styles.classTitleRow}>
            <View style={styles.statusIndicator}>
              <View style={[
                styles.statusDot, 
                { backgroundColor: ClassScheduleService.getRealTimeStatusColor(item.real_time_status) }
              ]} />
              <Text style={styles.className}>{item.class_name}</Text>
            </View>
            {item.is_member && (
              <View style={styles.memberBadge}>
                <Text style={styles.memberBadgeText}>MEMBER</Text>
              </View>
            )}
          </View>
          <View style={styles.categoryBadge}>
            <Text style={styles.categoryText}>{item.category_name}</Text>
          </View>
        </View>

        <View style={styles.classInfo}>
          <View style={styles.infoRow}>
            <Ionicons name="person-outline" size={16} color={theme.colors.textSecondary} />
            <Text style={styles.infoText}>{item.instructor_name}</Text>
          </View>

          {item.description && (
            <View style={styles.infoRow}>
              <Ionicons name="information-circle-outline" size={16} color={theme.colors.textSecondary} />
              <Text style={styles.infoText}>{item.description}</Text>
            </View>
          )}

          <View style={styles.infoRow}>
            <Ionicons name="people-outline" size={16} color={theme.colors.textSecondary} />
            <Text style={styles.infoText}>
              Peserta: {item.current_participants}/{item.max_participants}
            </Text>
            {item.remaining_capacity === 0 && (
              <View style={styles.fullBadge}>
                <Text style={styles.fullBadgeText}>PENUH</Text>
              </View>
            )}
          </View>
        </View>

        {isExpanded && (
          <View style={styles.expandedContent}>
            {item.is_member ? (
              <>
                <View style={styles.memberListSection}>
                  <Text style={styles.memberListTitle}>Daftar Member:</Text>
                  {item.members && item.members.length > 0 ? (
                    item.members.map((member, index) => (
                      <View key={index} style={styles.memberItem}>
                        <Ionicons name="person-circle-outline" size={20} color={theme.colors.textSecondary} />
                        <View style={{ flex: 1 }}>
                          <Text style={styles.memberNameText}>{member.member_name}</Text>
                          <Text style={styles.memberProgressText}>
                            Pertemuan: {Number(member.meetings_attended || 0)}/{Number(item.total_meetings || 0) || '?'}
                          </Text>
                        </View>
                      </View>
                    ))
                  ) : (
                    <Text style={styles.emptyMembersText}>Belum ada member terdaftar.</Text>
                  )}
                </View>
              </>
            ) : (
              <View style={styles.nonMemberInfo}>
                <Ionicons name="information-circle-outline" size={16} color={theme.colors.textSecondary} />
                <Text style={styles.nonMemberInfoText}>
                  Hanya member terdaftar yang dapat melihat daftar member & absensi kelas.
                </Text>
              </View>
            )}

            {item.is_member && item.my_member && (
              <View style={styles.progressBox}>
                <View style={styles.progressRow}>
                  <Text style={styles.progressLabel}>Progress Pertemuan</Text>
                  <Text style={styles.progressValue}>
                    {item.my_member.meetings_attended}/{item.my_member.total_meetings || '?'}
                  </Text>
                </View>
                <View style={styles.progressBarOuter}>
                  <View
                    style={[
                      styles.progressBarInner,
                      {
                        width:
                          item.my_member.total_meetings > 0
                            ? `${Math.min(
                                100,
                                Math.round(
                                  (item.my_member.meetings_attended / item.my_member.total_meetings) * 100
                                )
                              )}%`
                            : '0%',
                      },
                    ]}
                  />
                </View>
                {item.my_member.remaining_meetings !== null && (
                  <Text style={styles.progressHint}>Sisa {item.my_member.remaining_meetings} pertemuan</Text>
                )}
              </View>
            )}
          </View>
        )}
        
        {!isExpanded && (
          <View style={styles.expandHint}>
            <Text style={styles.expandHintText}>Klik untuk lihat detail & member</Text>
            <Ionicons name="chevron-down" size={16} color={theme.colors.textSecondary} />
          </View>
        )}
      </TouchableOpacity>
    );
  };

  const renderCategoryItem = ({ item }) => (
    <TouchableOpacity
      style={[
        styles.categoryItem,
        selectedCategory === item.id && styles.selectedCategoryItem,
      ]}
      onPress={() => setSelectedCategory(selectedCategory === item.id ? null : item.id)}
    >
      <Text
        style={[
          styles.categoryItemText,
          selectedCategory === item.id && styles.selectedCategoryItemText,
        ]}
      >
        {item.name}
      </Text>
    </TouchableOpacity>
  );

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
        <Text style={styles.loadingText}>Memuat kelas...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <LinearGradient
        colors={[theme.colors.primary, theme.colors.secondary]}
        style={styles.header}
      >
        <View style={styles.headerContent}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Ionicons name="arrow-back" size={24} color="white" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Jadwal Kelas</Text>
          <TouchableOpacity onPress={onRefresh}>
            <Ionicons name="refresh" size={24} color="white" />
          </TouchableOpacity>
        </View>
      </LinearGradient>

      <ScrollView
        style={styles.content}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
      >
        {loadError ? (
          <View style={styles.errorInline}>
            <Ionicons name="warning-outline" size={16} color="#f59e0b" />
            <Text style={styles.errorInlineText}>{loadError}</Text>
          </View>
        ) : null}
        {/* Categories */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Kategori</Text>
          <FlatList
            data={[{ id: null, name: 'Semua' }, ...categories]}
            renderItem={renderCategoryItem}
            keyExtractor={(item) => item.id?.toString() || 'all'}
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.categoriesList}
          />
        </View>

        {/* Classes List */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>
            Daftar Kelas Tersedia
          </Text>
          
          {classes.length === 0 ? (
            <View style={styles.emptyContainer}>
              <Ionicons name="calendar" size={64} color={theme.colors.textSecondary} />
              <Text style={styles.emptyText}>
                Tidak ada kelas tersedia untuk tanggal ini
              </Text>
            </View>
          ) : (
            <FlatList
              data={classes}
              renderItem={renderClassItem}
              keyExtractor={(item) => item.id.toString()}
              contentContainerStyle={styles.classesList}
              scrollEnabled={false}
            />
          )}
        </View>
      </ScrollView>

      {/* Booking Modal */}
      <Modal
        visible={showBookingModal}
        animationType="slide"
        presentationStyle="pageSheet"
      >
        <View style={styles.modalContainer}>
          <LinearGradient
            colors={[theme.colors.primary, theme.colors.secondary]}
            style={styles.modalHeader}
          >
            <View style={styles.modalHeaderContent}>
              <TouchableOpacity onPress={() => setShowBookingModal(false)}>
                <Ionicons name="close" size={24} color="white" />
              </TouchableOpacity>
              <Text style={styles.modalTitle}>Booking Kelas</Text>
              <View style={styles.placeholder} />
            </View>
          </LinearGradient>

          <ScrollView contentContainerStyle={styles.modalContent}>
            {selectedClass && (
              <View>
                <View style={styles.bookingClassInfo}>
                  <Text style={styles.bookingClassName}>{selectedClass.class_name}</Text>
                  <Text style={styles.bookingInstructor}>Instruktur: {selectedClass.instructor_name}</Text>
                  <Text style={styles.bookingCategory}>Kategori: {selectedClass.category_name}</Text>
                </View>

                <View style={styles.bookingForm}>
                  <Text style={styles.formLabel}>Nama Peserta *</Text>
                  <TextInput
                    style={styles.textInput}
                    value={participantName}
                    onChangeText={setParticipantName}
                    placeholder="Masukkan nama lengkap"
                  />

                  <Text style={styles.formLabel}>Nomor Telepon</Text>
                  <TextInput
                    style={styles.textInput}
                    value={participantPhone}
                    onChangeText={setParticipantPhone}
                    placeholder="Masukkan nomor telepon (opsional)"
                    keyboardType="phone-pad"
                  />
                </View>
              </View>
            )}
          </ScrollView>

          <View style={styles.modalActions}>
            <TouchableOpacity
              style={[styles.modalButton, styles.cancelButton]}
              onPress={() => setShowBookingModal(false)}
            >
              <Text style={styles.cancelButtonText}>Batal</Text>
            </TouchableOpacity>
            
            <TouchableOpacity
              style={[styles.modalButton, styles.confirmButton]}
              onPress={handleBookingSubmit}
              disabled={bookingLoading}
            >
              {bookingLoading ? (
                <ActivityIndicator size="small" color="white" />
              ) : (
                <Text style={styles.confirmButtonText}>Booking Sekarang</Text>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: theme.colors.textSecondary,
  },
  header: {
    paddingTop: 50,
    paddingBottom: 20,
  },
  headerContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: 'white',
  },
  content: {
    flex: 1,
  },
  section: {
    padding: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 15,
  },
  categoriesList: {
    paddingRight: 20,
  },
  categoryItem: {
    backgroundColor: theme.colors.surface,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    marginRight: 10,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  selectedCategoryItem: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  categoryItemText: {
    fontSize: 14,
    color: theme.colors.text,
  },
  selectedCategoryItemText: {
    color: 'white',
  },
  classesList: {
    paddingBottom: 20,
  },
  classCard: {
    backgroundColor: theme.colors.surface,
    borderRadius: 12,
    padding: 16,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  memberClassCard: {
    borderColor: '#8A0F0F',
    borderWidth: 2,
    backgroundColor: '#191919',
  },
  memberBadge: {
    backgroundColor: theme.colors.primary,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 4,
    marginLeft: 8,
  },
  memberBadgeText: {
    color: 'white',
    fontSize: 10,
    fontWeight: 'bold',
  },
  fullBadge: {
    backgroundColor: '#dc3545',
    paddingHorizontal: 6,
    paddingVertical: 1,
    borderRadius: 4,
    marginLeft: 8,
  },
  fullBadgeText: {
    color: 'white',
    fontSize: 10,
    fontWeight: 'bold',
  },
  expandedContent: {
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: theme.colors.border,
  },
  memberListSection: {
    marginBottom: 16,
  },
  memberListTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 8,
  },
  memberItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 6,
    paddingLeft: 4,
  },
  memberNameText: {
    fontSize: 14,
    color: '#f2f2f2',
    marginLeft: 8,
  },
  memberProgressText: {
    fontSize: 12,
    color: '#cfcfcf',
    marginLeft: 8,
    marginTop: 2,
  },
  emptyMembersText: {
    fontSize: 13,
    color: theme.colors.textSecondary,
    fontStyle: 'italic',
  },
  progressBox: {
    marginTop: 12,
    padding: 12,
    borderRadius: 10,
    backgroundColor: 'rgba(255,255,255,0.06)',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  progressRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  progressLabel: {
    fontSize: 12,
    color: theme.colors.textSecondary,
    fontWeight: '700',
  },
  progressValue: {
    fontSize: 12,
    color: 'white',
    fontWeight: '800',
  },
  progressBarOuter: {
    height: 8,
    borderRadius: 999,
    backgroundColor: 'rgba(255,255,255,0.10)',
    overflow: 'hidden',
  },
  progressBarInner: {
    height: 8,
    borderRadius: 999,
    backgroundColor: theme.colors.primary,
  },
  progressHint: {
    marginTop: 8,
    fontSize: 11,
    color: theme.colors.textSecondary,
  },
  nonMemberInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#1f1f1f',
    padding: 12,
    borderRadius: 8,
    marginTop: 8,
  },
  nonMemberInfoText: {
    fontSize: 12,
    color: '#c2c2c2',
    marginLeft: 8,
    flex: 1,
  },
  errorInline: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(245,158,11,0.12)',
    borderColor: 'rgba(245,158,11,0.3)',
    borderWidth: 1,
    borderRadius: 10,
    marginHorizontal: 20,
    marginTop: 20,
    padding: 10,
  },
  errorInlineText: {
    color: '#f5d38a',
    fontSize: 12,
    marginLeft: 8,
    flex: 1,
  },
  expandHint: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
  },
  expandHintText: {
    fontSize: 12,
    color: theme.colors.textSecondary,
    marginRight: 4,
  },
  classHeader: {
    marginBottom: 12,
  },
  classTitleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  statusIndicator: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  statusDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    marginRight: 8,
  },
  className: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
    flex: 1,
  },
  liveBadge: {
    backgroundColor: '#dc3545',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  liveText: {
    fontSize: 10,
    color: 'white',
    fontWeight: 'bold',
  },
  categoryBadge: {
    backgroundColor: theme.colors.primary,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  categoryText: {
    fontSize: 12,
    color: 'white',
    fontWeight: 'bold',
  },
  classInfo: {
    marginBottom: 16,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  infoText: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginLeft: 8,
  },
  bookButton: {
    backgroundColor: theme.colors.primary,
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 8,
  },
  bookButtonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
  emptyContainer: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  emptyText: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    textAlign: 'center',
    marginTop: 10,
  },
  modalContainer: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  modalHeader: {
    paddingTop: 50,
    paddingBottom: 20,
  },
  modalHeaderContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: 'white',
  },
  placeholder: {
    width: 24,
  },
  modalContent: {
    padding: 20,
  },
  bookingClassInfo: {
    backgroundColor: theme.colors.surface,
    borderRadius: 12,
    padding: 16,
    marginBottom: 20,
  },
  bookingClassName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 8,
  },
  bookingInstructor: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    marginBottom: 4,
  },
  bookingCategory: {
    fontSize: 16,
    color: theme.colors.textSecondary,
  },
  bookingForm: {
    marginBottom: 20,
  },
  formLabel: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 8,
  },
  textInput: {
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: 8,
    paddingHorizontal: 16,
    paddingVertical: 12,
    fontSize: 16,
    color: theme.colors.text,
    backgroundColor: theme.colors.surface,
    marginBottom: 16,
  },
  modalActions: {
    flexDirection: 'row',
    padding: 20,
    gap: 10,
  },
  modalButton: {
    flex: 1,
    paddingVertical: 14,
    borderRadius: 8,
    alignItems: 'center',
  },
  cancelButton: {
    backgroundColor: theme.colors.surface,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  confirmButton: {
    backgroundColor: theme.colors.primary,
  },
  cancelButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  confirmButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: 'white',
  },
});

export default ClassScheduleScreen;
