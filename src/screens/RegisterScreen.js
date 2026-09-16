import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, Alert, TouchableOpacity, ActivityIndicator, KeyboardAvoidingView, Platform, ScrollView, Dimensions, ImageBackground } from 'react-native';
import { createUserWithEmailAndPassword } from 'firebase/auth';
import { doc, setDoc } from 'firebase/firestore';
import { auth, db } from '../config/firebase.js';
import { theme } from '../styles/theme.js';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';
import { getUserFriendlyErrorMessage } from '../utils/errorMessages';
import { useGoogleAuthRequest, handleGoogleSignIn } from '../services/GoogleAuthService';

const { width } = Dimensions.get('window');

export default function RegisterScreen({ navigation }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [name, setName] = useState('');
  const [phoneNumber, setPhoneNumber] = useState('');
  const [loading, setLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [googleLoading, setGoogleLoading] = useState(false);
  const { request, promptAsync } = useGoogleAuthRequest();

  const handleRegister = async () => {
    if (!email || !password || !name || !phoneNumber) {
      Alert.alert('Error', 'Please fill in all fields (Full Name, Phone Number, Email, Password)');
      return;
    }

    setLoading(true);
    try {
      const userCredential = await createUserWithEmailAndPassword(auth, email, password);
      const user = userCredential.user;

      const userData = {
        email: user.email,
        name: name,
        phoneNumber: phoneNumber,
        role: 'user',
        createdAt: new Date().toISOString(),
      };
      
      await setDoc(doc(db, 'users', user.uid), userData);
      Alert.alert('Success', 'Account created successfully!');
    } catch (error) {
      let errorMessage = 'Registration failed';
      if (error.code === 'auth/email-already-in-use') {
        errorMessage = 'Email already in use';
      } else if (error.code === 'auth/weak-password') {
        errorMessage = 'Password is too weak';
      } else if (error.code === 'auth/invalid-email') {
        errorMessage = 'Invalid email address';
      } else {
        errorMessage = getUserFriendlyErrorMessage(error, 'Registrasi gagal. Coba lagi.');
      }
      Alert.alert('Registration Error', errorMessage);
    } finally {
      setLoading(false);
    }
  };

  const handleGoogleRegister = async () => {
    if (!request) {
      Alert.alert('Error', 'Google Sign-In is not available on this device');
      return;
    }

    setGoogleLoading(true);
    try {
      const result = await handleGoogleSignIn(promptAsync);
      if (result.isNewUser) {
        Alert.alert('Success', 'Account created successfully with Google!');
      }
    } catch (error) {
      Alert.alert('Google Registration Error', getUserFriendlyErrorMessage(error, 'Google registration failed. Please try again.'));
    } finally {
      setGoogleLoading(false);
    }
  };

  return (
    <ImageBackground
      source={require('../../assets/LOGO/BG3.jpg')}
      style={styles.container}
      resizeMode="cover"
    >
      <StatusBar style="light" translucent backgroundColor="transparent" />

      {/* Dark Overlay */}
      <View style={styles.overlay} pointerEvents="none" />

      <KeyboardAvoidingView 
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={{ flex: 1, zIndex: 2 }}
      >
        <ScrollView style={{ flex: 1 }} contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
          <TouchableOpacity 
            style={styles.backButton} 
            onPress={() => navigation.goBack()}
          >
            <Ionicons name="arrow-back" size={24} color={theme.colors.text} />
          </TouchableOpacity>

          <View style={styles.header}>
            <Text style={styles.title}>Create Account</Text>
            <Text style={styles.subtitle}>Join GSC Sports Center today</Text>
          </View>

          <View style={styles.formContainer}>
            <View style={styles.inputWrapper}>
              <Text style={styles.inputLabel}>Full Name</Text>
              <View style={styles.inputContainer}>
                <Ionicons name="person-outline" size={20} color={theme.colors.textSecondary} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  value={name}
                  onChangeText={setName}
                  placeholder="Enter your full name"
                  placeholderTextColor={theme.colors.textTertiary}
                />
              </View>
            </View>

            <View style={styles.inputWrapper}>
              <Text style={styles.inputLabel}>Email Address</Text>
              <View style={styles.inputContainer}>
                <Ionicons name="mail-outline" size={20} color={theme.colors.textSecondary} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  value={email}
                  onChangeText={setEmail}
                  autoCapitalize="none"
                  keyboardType="email-address"
                  placeholder="Enter your email"
                  placeholderTextColor={theme.colors.textTertiary}
                />
              </View>
            </View>

            <View style={styles.inputWrapper}>
              <Text style={styles.inputLabel}>Phone Number</Text>
              <View style={styles.inputContainer}>
                <Ionicons name="call-outline" size={20} color={theme.colors.textSecondary} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  value={phoneNumber}
                  onChangeText={setPhoneNumber}
                  keyboardType="phone-pad"
                  placeholder="Enter your phone (e.g. 0812...)"
                  placeholderTextColor={theme.colors.textTertiary}
                />
              </View>
            </View>

            <View style={styles.inputWrapper}>
              <Text style={styles.inputLabel}>Password</Text>
              <View style={styles.inputContainer}>
                <Ionicons name="lock-closed-outline" size={20} color={theme.colors.textSecondary} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  value={password}
                  onChangeText={setPassword}
                  secureTextEntry={!showPassword}
                  placeholder="Create a password"
                  placeholderTextColor={theme.colors.textTertiary}
                />
                <TouchableOpacity onPress={() => setShowPassword(!showPassword)}>
                  <Ionicons 
                    name={showPassword ? "eye-off-outline" : "eye-outline"} 
                    size={20} 
                    color={theme.colors.textSecondary} 
                  />
                </TouchableOpacity>
              </View>
            </View>

            {loading ? (
              <ActivityIndicator size="large" color={theme.colors.primary} style={styles.loader} />
            ) : (
              <TouchableOpacity onPress={handleRegister} activeOpacity={0.8} style={styles.registerButton}>
                <LinearGradient
                  colors={theme.gradients.primary}
                  start={{ x: 0, y: 0 }}
                  end={{ x: 1, y: 0 }}
                  style={styles.gradientButton}
                >
                  <Text style={styles.registerButtonText}>CREATE ACCOUNT</Text>
                  <Ionicons name="person-add-outline" size={20} color="white" />
                </LinearGradient>
              </TouchableOpacity>
            )}

            <View style={styles.footer}>
              <Text style={styles.footerText}>Already have an account? </Text>
              <TouchableOpacity onPress={() => navigation.navigate('Login')}>
                <Text style={styles.loginLinkText}>Sign In</Text>
              </TouchableOpacity>
            </View>

            <View style={styles.socialSection}>
              <View style={styles.socialDivider}>
                <View style={styles.dividerLine} />
                <Text style={styles.dividerText}>or continue with</Text>
                <View style={styles.dividerLine} />
              </View>

              <View style={styles.socialButtonsRow}>
                <TouchableOpacity 
                  style={styles.socialButton} 
                  activeOpacity={0.7}
                  onPress={handleGoogleRegister}
                  disabled={googleLoading || !request}
                >
                  {googleLoading ? (
                    <ActivityIndicator size="small" color={theme.colors.text} />
                  ) : (
                    <Ionicons name="logo-google" size={22} color={theme.colors.text} style={styles.socialIcon} />
                  )}
                  <Text style={styles.socialButtonText}>Google</Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
    width: '100%',
    height: '100%',
  },
  overlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.65)',
    zIndex: 1,
  },
  scrollContent: {
    flexGrow: 1,
    paddingHorizontal: 30,
    paddingTop: 60,
    paddingBottom: 40,
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 30,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  header: {
    marginBottom: 40,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 16,
    color: theme.colors.textSecondary,
  },
  formContainer: {
    width: '100%',
  },
  inputWrapper: {
    marginBottom: 20,
  },
  inputLabel: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    fontWeight: '600',
    marginBottom: 8,
    marginLeft: 4,
    textTransform: 'uppercase',
    letterSpacing: 1,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.medium,
    paddingHorizontal: 15,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    height: 55,
  },
  inputIcon: {
    marginRight: 12,
  },
  input: {
    flex: 1,
    color: theme.colors.text,
    fontSize: 16,
  },
  registerButton: {
    marginTop: 20,
    borderRadius: theme.borderRadius.medium,
    overflow: 'hidden',
    ...theme.shadows.medium,
  },
  gradientButton: {
    height: 55,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  registerButtonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
    letterSpacing: 2,
    marginRight: 10,
  },
  loader: {
    marginVertical: 10,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 40,
  },
  footerText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  loginLinkText: {
    color: theme.colors.text,
    fontSize: 14,
    fontWeight: 'bold',
  },
  socialSection: {
    marginTop: 30,
  },
  socialDivider: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: theme.colors.glassBorder,
    maxWidth: 90,
  },
  dividerText: {
    color: theme.colors.textTertiary,
    fontSize: 11,
    fontWeight: '500',
    letterSpacing: 0.5,
    textTransform: 'uppercase',
    marginHorizontal: 12,
  },
  socialButtonsRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 12,
  },
  socialButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: theme.colors.surface,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
    borderRadius: theme.borderRadius.medium,
    paddingVertical: 12,
    paddingHorizontal: 18,
    gap: 8,
    minWidth: (width - 60 - 24) / 2,
  },
  socialIcon: {
    marginRight: 2,
  },
  socialButtonText: {
    color: theme.colors.text,
    fontSize: 13,
    fontWeight: '500',
    letterSpacing: 0.3,
  },
});
