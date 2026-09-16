import React, { useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, TextInput, Alert } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../../styles/theme';

export default function ClassSchedulingScreen() {
  const [classes, setClasses] = useState([
    { id: 1, name: 'Sepak Bola Pagi', instructor: 'Pelatih Mike', day: 'Senin', time: '08:00 - 10:00', capacity: 20, enrolled: 15 },
    { id: 2, name: 'Basket Sore', instructor: 'Pelatih Sarah', day: 'Selasa', time: '16:00 - 18:00', capacity: 15, enrolled: 12 },
    { id: 3, name: 'Latihan Pemula', instructor: 'Pelatih John', day: 'Rabu', time: '14:00 - 16:00', capacity: 25, enrolled: 20 },
    { id: 4, name: 'Tenis Lanjutan', instructor: 'Pelatih Emily', day: 'Kamis', time: '10:00 - 12:00', capacity: 12, enrolled: 10 },
  ]);

  const [showForm, setShowForm] = useState(false);
  const [newClass, setNewClass] = useState({
    name: '',
    instructor: '',
    day: '',
    time: '',
    capacity: '',
  });

  const handleAddClass = () => {
    if (!newClass.name || !newClass.instructor || !newClass.day || !newClass.time || !newClass.capacity) {
      Alert.alert('Error', 'Harap isi semua kolom');
      return;
    }

    const classToAdd = {
      id: classes.length + 1,
      name: newClass.name,
      instructor: newClass.instructor,
      day: newClass.day,
      time: newClass.time,
      capacity: parseInt(newClass.capacity),
      enrolled: 0,
    };

    setClasses([...classes, classToAdd]);
    setNewClass({ name: '', instructor: '', day: '', time: '', capacity: '' });
    setShowForm(false);
    Alert.alert('Berhasil', 'Kelas berhasil dibuat!');
  };

  const handleDeleteClass = (id) => {
    Alert.alert(
      'Hapus Kelas',
      'Apakah Anda yakin ingin menghapus kelas ini?',
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Hapus',
          style: 'destructive',
          onPress: () => setClasses(classes.filter(c => c.id !== id))
        }
      ]
    );
  };

  const ClassCard = ({ classItem }) => {
    const enrollmentPercentage = (classItem.enrolled / classItem.capacity) * 100;
    const isFull = classItem.enrolled >= classItem.capacity;

    return (
      <View style={styles.cardContainer}>
        <LinearGradient
          colors={isFull ? ['#FF6B6B', '#CC0000'] : ['#FF0000', '#990000']}
          style={styles.card}
          start={{ x: 0, y: 0 }}
          end={{ x: 1, y: 1 }}
        >
          <View style={styles.cardHeader}>
            <View style={styles.classInfo}>
              <Text style={styles.className}>{classItem.name}</Text>
              <View style={styles.instructorRow}>
                <Ionicons name="person" size={14} color="rgba(255,255,255,0.7)" />
                <Text style={styles.instructor}>{classItem.instructor}</Text>
              </View>
            </View>
            <TouchableOpacity onPress={() => handleDeleteClass(classItem.id)} style={styles.deleteButton}>
              <Ionicons name="trash-outline" size={20} color="white" />
            </TouchableOpacity>
          </View>

          <View style={styles.scheduleRow}>
            <View style={styles.scheduleItem}>
              <Ionicons name="calendar-outline" size={16} color="rgba(255,255,255,0.8)" />
              <Text style={styles.scheduleText}>{classItem.day}</Text>
            </View>
            <View style={styles.scheduleItem}>
              <Ionicons name="time-outline" size={16} color="rgba(255,255,255,0.8)" />
              <Text style={styles.scheduleText}>{classItem.time}</Text>
            </View>
          </View>

          <View style={styles.capacityContainer}>
            <View style={styles.capacityInfo}>
              <Text style={styles.capacityText}>
                {classItem.enrolled} / {classItem.capacity} terdaftar
              </Text>
              <Text style={styles.capacityPercentage}>{enrollmentPercentage.toFixed(0)}%</Text>
            </View>
            <View style={styles.progressBar}>
              <View style={[styles.progressFill, { width: `${enrollmentPercentage}%` }]} />
            </View>
          </View>

          {isFull && (
            <View style={styles.fullBadge}>
              <Text style={styles.fullText}>PENUH</Text>
            </View>
          )}
        </LinearGradient>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Ionicons name="calendar" size={24} color={theme.colors.primary} />
        <Text style={styles.headerTitle}>Jadwal Kelas</Text>
      </View>

      <View style={styles.statsBar}>
        <Text style={styles.statsText}>
          <Text style={styles.statsNumber}>{classes.length}</Text> Kelas Aktif
        </Text>
        <TouchableOpacity 
          style={styles.addButton}
          onPress={() => setShowForm(!showForm)}
        >
          <Ionicons name={showForm ? "close" : "add-circle"} size={20} color="white" />
          <Text style={styles.addButtonText}>{showForm ? 'Batal' : 'Kelas Baru'}</Text>
        </TouchableOpacity>
      </View>

      {showForm && (
        <View style={styles.formContainer}>
          <LinearGradient
            colors={['#1a1a1a', '#000000']}
            style={styles.form}
          >
            <Text style={styles.formTitle}>Buat Kelas Baru</Text>
            
            <TextInput
              style={styles.input}
              placeholder="Nama Kelas"
              placeholderTextColor={theme.colors.textSecondary}
              value={newClass.name}
              onChangeText={(text) => setNewClass({ ...newClass, name: text })}
            />
            <TextInput
              style={styles.input}
              placeholder="Nama Pelatih"
              placeholderTextColor={theme.colors.textSecondary}
              value={newClass.instructor}
              onChangeText={(text) => setNewClass({ ...newClass, instructor: text })}
            />
            <TextInput
              style={styles.input}
              placeholder="Hari (contoh: Senin)"
              placeholderTextColor={theme.colors.textSecondary}
              value={newClass.day}
              onChangeText={(text) => setNewClass({ ...newClass, day: text })}
            />
            <TextInput
              style={styles.input}
              placeholder="Waktu (contoh: 08:00 - 10:00)"
              placeholderTextColor={theme.colors.textSecondary}
              value={newClass.time}
              onChangeText={(text) => setNewClass({ ...newClass, time: text })}
            />
            <TextInput
              style={styles.input}
              placeholder="Kapasitas"
              placeholderTextColor={theme.colors.textSecondary}
              keyboardType="numeric"
              value={newClass.capacity}
              onChangeText={(text) => setNewClass({ ...newClass, capacity: text })}
            />

            <TouchableOpacity style={styles.submitButton} onPress={handleAddClass}>
              <LinearGradient
                colors={['#FF0000', '#CC0000']}
                style={styles.submitGradient}
              >
                <Text style={styles.submitText}>Buat Kelas</Text>
              </LinearGradient>
            </TouchableOpacity>
          </LinearGradient>
        </View>
      )}

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        {classes.map(classItem => <ClassCard key={classItem.id} classItem={classItem} />)}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 15,
    backgroundColor: theme.colors.cardBackground,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,0,0,0.2)',
  },
  headerTitle: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginLeft: 10,
  },
  statsBar: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: 'rgba(255,0,0,0.1)',
    paddingVertical: 12,
    paddingHorizontal: 20,
  },
  statsText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  statsNumber: {
    color: theme.colors.primary,
    fontSize: 18,
    fontWeight: 'bold',
  },
  addButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.colors.primary,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
    gap: 6,
  },
  addButtonText: {
    color: 'white',
    fontSize: 13,
    fontWeight: 'bold',
  },
  formContainer: {
    margin: 20,
    borderRadius: 12,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,0,0,0.3)',
  },
  form: {
    padding: 20,
  },
  formTitle: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 16,
  },
  input: {
    backgroundColor: 'rgba(255,255,255,0.05)',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
    borderRadius: 8,
    paddingHorizontal: 15,
    paddingVertical: 12,
    color: theme.colors.text,
    fontSize: 14,
    marginBottom: 12,
  },
  submitButton: {
    borderRadius: 8,
    overflow: 'hidden',
    marginTop: 8,
  },
  submitGradient: {
    paddingVertical: 14,
    alignItems: 'center',
  },
  submitText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
  content: {
    padding: 20,
  },
  cardContainer: {
    marginBottom: 16,
    borderRadius: 12,
    overflow: 'hidden',
    elevation: 4,
  },
  card: {
    padding: 16,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  classInfo: {
    flex: 1,
  },
  className: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  instructorRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  instructor: {
    color: 'rgba(255,255,255,0.7)',
    fontSize: 13,
  },
  deleteButton: {
    padding: 8,
    backgroundColor: 'rgba(0,0,0,0.3)',
    borderRadius: 8,
  },
  scheduleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  scheduleItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  scheduleText: {
    color: 'rgba(255,255,255,0.8)',
    fontSize: 13,
  },
  capacityContainer: {
    marginTop: 8,
  },
  capacityInfo: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 6,
  },
  capacityText: {
    color: 'rgba(255,255,255,0.9)',
    fontSize: 12,
  },
  capacityPercentage: {
    color: 'white',
    fontSize: 12,
    fontWeight: 'bold',
  },
  progressBar: {
    height: 6,
    backgroundColor: 'rgba(0,0,0,0.3)',
    borderRadius: 3,
    overflow: 'hidden',
  },
  progressFill: {
    height: '100%',
    backgroundColor: 'white',
    borderRadius: 3,
  },
  fullBadge: {
    position: 'absolute',
    top: 12,
    right: 12,
    backgroundColor: 'rgba(255,255,255,0.3)',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  fullText: {
    color: 'white',
    fontSize: 10,
    fontWeight: 'bold',
  },
});
