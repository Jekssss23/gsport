import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, TextInput, ScrollView, Alert, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { doc, getDoc, setDoc } from 'firebase/firestore';
import { auth, db } from '../../config/firebase';
import { theme } from '../../styles/theme';
import { getUserFriendlyErrorMessage } from '../../utils/errorMessages';

export default function ProfileScreen({ navigation }) {
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState({
    name: '',
    email: '',
    phoneNumber: '',
    bio: '',
    address: '',
    emergencyContact: '',
  });

  useEffect(() => {
    fetchProfile();
  }, []);

  const fetchProfile = async () => {
    try {
      const user = auth.currentUser;
      if (!user) return;
      const snap = await getDoc(doc(db, 'users', user.uid));
      if (snap.exists()) {
        const data = snap.data();
        setForm({
          name: data.name || '',
          email: data.email || user.email || '',
          phoneNumber: data.phoneNumber || '',
          bio: data.bio || '',
          address: data.address || '',
          emergencyContact: data.emergencyContact || '',
        });
      }
    } catch (error) {
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal memuat profil.'));
    } finally {
      setLoading(false);
    }
  };

  const onSave = async () => {
    try {
      const user = auth.currentUser;
      if (!user) return;
      setSaving(true);
      await setDoc(
        doc(db, 'users', user.uid),
        {
          ...form,
          updatedAt: new Date().toISOString(),
        },
        { merge: true }
      );
      Alert.alert('Berhasil', 'Profil berhasil diperbarui.');
    } catch (error) {
      Alert.alert('Error', getUserFriendlyErrorMessage(error, 'Gagal menyimpan profil.'));
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.iconBtn}>
          <Ionicons name="arrow-back" size={22} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Profile Saya</Text>
        <View style={styles.iconBtn} />
      </View>
      <ScrollView contentContainerStyle={styles.content}>
        <Text style={styles.note}>Foto profil belum bisa diubah untuk saat ini.</Text>

        <Text style={styles.label}>Nama</Text>
        <TextInput style={styles.input} value={form.name} onChangeText={(v) => setForm((p) => ({ ...p, name: v }))} />

        <Text style={styles.label}>Email</Text>
        <TextInput style={[styles.input, styles.readOnly]} value={form.email} editable={false} />

        <Text style={styles.label}>Nomor HP</Text>
        <TextInput style={styles.input} value={form.phoneNumber} onChangeText={(v) => setForm((p) => ({ ...p, phoneNumber: v }))} />

        <Text style={styles.label}>Bio</Text>
        <TextInput style={[styles.input, styles.multiline]} multiline value={form.bio} onChangeText={(v) => setForm((p) => ({ ...p, bio: v }))} />

        <Text style={styles.label}>Alamat</Text>
        <TextInput style={[styles.input, styles.multiline]} multiline value={form.address} onChangeText={(v) => setForm((p) => ({ ...p, address: v }))} />

        <Text style={styles.label}>Kontak Darurat</Text>
        <TextInput style={styles.input} value={form.emergencyContact} onChangeText={(v) => setForm((p) => ({ ...p, emergencyContact: v }))} />

        <TouchableOpacity style={styles.saveBtn} onPress={onSave} disabled={saving}>
          <Text style={styles.saveText}>{saving ? 'Menyimpan...' : 'Simpan Profil'}</Text>
        </TouchableOpacity>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: theme.colors.background },
  header: { paddingTop: 56, paddingHorizontal: 18, paddingBottom: 14, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  headerTitle: { color: 'white', fontSize: 18, fontWeight: '800' },
  iconBtn: { width: 38, height: 38, alignItems: 'center', justifyContent: 'center' },
  content: { padding: 20, paddingBottom: 44 },
  note: { color: theme.colors.textSecondary, marginBottom: 14, fontSize: 12 },
  label: { color: 'white', fontSize: 12, fontWeight: '700', marginBottom: 6, marginTop: 10 },
  input: {
    backgroundColor: '#151515',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
    borderRadius: 10,
    color: 'white',
    paddingHorizontal: 12,
    paddingVertical: 10,
  },
  readOnly: { opacity: 0.7 },
  multiline: { minHeight: 80, textAlignVertical: 'top' },
  saveBtn: {
    marginTop: 20,
    backgroundColor: theme.colors.primary,
    borderRadius: 10,
    paddingVertical: 13,
    alignItems: 'center',
  },
  saveText: { color: 'white', fontWeight: '800' },
});

