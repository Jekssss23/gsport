import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, Alert, ActivityIndicator, TouchableOpacity, Dimensions, Image, ImageBackground, KeyboardAvoidingView, Platform, ScrollView } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { signInWithEmailAndPassword } from 'firebase/auth';
import { doc, getDoc, collection, query, where, getDocs } from 'firebase/firestore';
import { auth, db } from '../config/firebase.js';
import { API_BASE_URL } from '../config/api.js';
import { theme } from '../styles/theme.js';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';
import { APP_LOGO_PRIMARY } from '../constants/assets';
import { getUserFriendlyErrorMessage } from '../utils/errorMessages';
import { useGoogleAuthRequest, handleGoogleSignIn } from '../services/GoogleAuthService';
import { handleAppleSignIn } from '../services/AppleAuthService';

const { width } = Dimensions.get('window');

export default function LoginScreen(props) {
  const navigation = useNavigation();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [googleLoading, setGoogleLoading] = useState(false);
  const [appleLoading, setAppleLoading] = useState(false);
  const { request, promptAsync } = useGoogleAuthRequest();

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Error', 'Please enter your Email or Phone Number and Password');
      return;
    }

    setLoading(true);
    try {
      let finalEmail = email.trim();
      
      // If the input doesn't look like an email, assume it's a phone number
      if (!finalEmail.includes('@')) {
        const res = await fetch(`${API_BASE_URL}/auth/get_email_by_phone?phone=${encodeURIComponent(finalEmail)}`);
        const json = await res.json();
        
        if (json.ok && json.email) {
          finalEmail = json.email;
        } else {
          Alert.alert('Error', 'No account found with this phone number.');
          setLoading(false);
          return;
        }
      }

      const userCredential = await signInWithEmailAndPassword(auth, finalEmail, password);
      const user = userCredential.user;

      const userDoc = await getDoc(doc(db, 'users', user.uid));
      if (!userDoc.exists()) {
        Alert.alert('Error', 'User data not found');
        return;
      }

      const role = userDoc.data()?.role;
      if (role !== 'admin' && role !== 'user') {
        Alert.alert('Error', 'Invalid account role');
        return;
      }
    } catch (error) {
      Alert.alert('Login Error', getUserFriendlyErrorMessage(error, 'Login gagal. Cek email/nomor HP dan password.'));
    } finally {
      setLoading(false);
    }
  };

  const handleGoogleLogin = async () => {
    if (!request) {
      Alert.alert('Error', 'Google Sign-In is not available on this device');
      return;
    }

    setGoogleLoading(true);
    try {
      await handleGoogleSignIn(promptAsync);
    } catch (error) {
      Alert.alert('Google Login Error', getUserFriendlyErrorMessage(error, 'Google login failed. Please try again.'));
    } finally {
      setGoogleLoading(false);
    }
  };

  const handleAppleLogin = async () => {
    setAppleLoading(true);
    try {
      const res = await handleAppleSignIn();
      if (res?.cancelled) {
        return;
      }
    } catch (error) {
      Alert.alert('Apple Login Error', getUserFriendlyErrorMessage(error, 'Apple login failed. Please try again.'));
    } finally {
      setAppleLoading(false);
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
        style={styles.keyboardView}
      >
        <ScrollView
          style={{ flex: 1 }}
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
          keyboardShouldPersistTaps="handled"
        >
          <TouchableOpacity 
            style={styles.backButton} 
            onPress={() => navigation.replace('Splash')}
            hitSlop={{ top: 15, bottom: 15, left: 15, right: 15 }}
          >
            <Ionicons name="arrow-back" size={24} color={theme.colors.text} />
          </TouchableOpacity>

          <View style={styles.header}>
            <Image source={APP_LOGO_PRIMARY} style={styles.logoImage} resizeMode="contain" />
            <Text style={styles.welcomeTitle}>Welcome Back</Text>
          </View>

          <View style={styles.formContainer}>
            <View style={styles.inputWrapper}>
              <Text style={styles.inputLabel}>Email or Phone Number</Text>
              <View style={styles.inputContainer}>
                <Ionicons name="person-outline" size={20} color={theme.colors.textSecondary} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  value={email}
                  onChangeText={setEmail}
                  autoCapitalize="none"
                  keyboardType="email-address"
                  placeholder="Enter email or phone"
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
                  placeholder="Enter your password"
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

            <TouchableOpacity style={styles.forgotPassword}>
              <Text style={styles.forgotPasswordText}>Forgot Password?</Text>
            </TouchableOpacity>

            {loading ? (
              <ActivityIndicator size="large" color={theme.colors.primary} style={styles.loader} />
            ) : (
              <TouchableOpacity onPress={handleLogin} activeOpacity={0.8} style={styles.loginButton}>
                <LinearGradient
                  colors={theme.gradients.primary}
                  start={{ x: 0, y: 0 }}
                  end={{ x: 1, y: 0 }}
                  style={styles.gradientButton}
                >
                  <Text style={styles.loginButtonText}>LOGIN</Text>
                </LinearGradient>
              </TouchableOpacity>
            )}

            <View style={styles.footer}>
              <Text style={styles.footerText}>New to GSC? </Text>
              <TouchableOpacity onPress={() => navigation.navigate('Register')}>
                <Text style={styles.signUpText}>Create Account</Text>
              </TouchableOpacity>
            </View>

            {/* Social Login Buttons */}
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
                  onPress={handleGoogleLogin}
                  disabled={googleLoading || !request}
                >
                  {googleLoading ? (
                    <ActivityIndicator size="small" color={theme.colors.text} />
                  ) : (
                    <Ionicons name="logo-google" size={22} color={theme.colors.text} style={styles.socialIcon} />
                  )}
                  <Text style={styles.socialButtonText}>Google</Text>
                </TouchableOpacity>

                <TouchableOpacity 
                  style={styles.socialButton} 
                  activeOpacity={0.7}
                  onPress={handleAppleLogin}
                  disabled={appleLoading || googleLoading || loading}
                >
                  {appleLoading ? (
                    <ActivityIndicator size="small" color={theme.colors.text} />
                  ) : (
                    <Ionicons name="logo-apple" size={22} color={theme.colors.text} style={styles.socialIcon} />
                  )}
                  <Text style={styles.socialButtonText}>Apple</Text>
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
  keyboardView: {
    flex: 1,
    zIndex: 2,
  },
  scrollContent: {
    flexGrow: 1,
    paddingHorizontal: 28,
    paddingTop: 50,
    paddingBottom: 40,
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: theme.colors.surface,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
    borderWidth: 1,
    borderColor: theme.colors.glassBorder,
  },
  header: {
    alignItems: 'center',
    marginBottom: 36,
  },
  logoImage: {
    width: 140,
    height: 60,
    marginBottom: 20,
  },
  welcomeTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: 8,
  },
  welcomeSubtitle: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    textAlign: 'center',
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
  forgotPassword: {
    alignSelf: 'flex-end',
    marginBottom: 24,
  },
  forgotPasswordText: {
    color: theme.colors.primary,
    fontSize: 13,
    fontWeight: '500',
  },
  loginButton: {
    borderRadius: theme.borderRadius.medium,
    overflow: 'hidden',
    ...theme.shadows.light,
  },
  gradientButton: {
    height: 48,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loginButtonText: {
    color: 'white',
    fontSize: 15,
    fontWeight: '700',
    letterSpacing: 1.5,
  },
  loader: {
    marginVertical: 8,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 28,
  },
  footerText: {
    color: theme.colors.textSecondary,
    fontSize: 13,
  },
  signUpText: {
    color: theme.colors.text,
    fontSize: 13,
    fontWeight: '600',
  },
  socialSection: {
    marginTop: 24,
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
    minWidth: (width - 60 - 24) / 2, // half screen minus padding and gap
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
