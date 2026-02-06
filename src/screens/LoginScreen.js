import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, Alert, ActivityIndicator, TouchableOpacity, ImageBackground } from 'react-native';
import { signInWithEmailAndPassword, createUserWithEmailAndPassword } from 'firebase/auth';
import { doc, getDoc, setDoc } from 'firebase/firestore';
import { auth, db } from '../config/firebase.js';
import { theme } from '../styles/theme.js';
import LottieView from 'lottie-react-native';

export default function LoginScreen({ navigation }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [isEmailFocused, setIsEmailFocused] = useState(false);
  const [isPasswordFocused, setIsPasswordFocused] = useState(false);

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Error', 'Please enter both email and password');
      return;
    }

    setLoading(true);
    try {
      // Cek apakah ini akun admin khusus
      if (email === 'admin@gsports.com' && password === 'admin123') {
        try {
          // Coba login dulu
          const userCredential = await signInWithEmailAndPassword(auth, email, password);
          const user = userCredential.user;

          // Pastikan data admin ada di Firestore
          const userDoc = await getDoc(doc(db, 'users', user.uid));
          if (!userDoc.exists()) {
            await setDoc(doc(db, 'users', user.uid), {
              email: user.email,
              name: 'Administrator',
              role: 'admin',
              createdAt: new Date().toISOString(),
            });
          }
        } catch (loginError) {
          // Jika akun belum ada, buat akun admin baru
          if (loginError.code === 'auth/user-not-found' || loginError.code === 'auth/invalid-credential') {
            const userCredential = await createUserWithEmailAndPassword(auth, email, password);
            const user = userCredential.user;

            await setDoc(doc(db, 'users', user.uid), {
              email: user.email,
              name: 'Administrator',
              role: 'admin',
              createdAt: new Date().toISOString(),
            });
          } else {
            throw loginError;
          }
        }
      } else {
        // Login normal untuk user biasa
        const userCredential = await signInWithEmailAndPassword(auth, email, password);
        const user = userCredential.user;

        const userDoc = await getDoc(doc(db, 'users', user.uid));
        if (!userDoc.exists()) {
          Alert.alert('Error', 'User data not found');
        }
      }
    } catch (error) {
      Alert.alert('Login Error', error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      
      <View style={styles.loginBox}>
        <Text style={styles.title}>Login</Text>
        
        <View style={styles.userBox}>
          <TextInput
            style={[styles.input, (isEmailFocused || email) && styles.inputFocused]}
            value={email}
            onChangeText={setEmail}
            autoCapitalize="none"
            keyboardType="email-address"
            onFocus={() => setIsEmailFocused(true)}
            onBlur={() => setIsEmailFocused(false)}
            placeholderTextColor="transparent"
          />
          <Text style={[styles.label, (isEmailFocused || email) && styles.labelFocused]}>Email</Text>
        </View>

        <View style={styles.userBox}>
          <TextInput
            style={[styles.input, (isPasswordFocused || password) && styles.inputFocused]}
            value={password}
            onChangeText={setPassword}
            secureTextEntry
            onFocus={() => setIsPasswordFocused(true)}
            onBlur={() => setIsPasswordFocused(false)}
            placeholderTextColor="transparent"
          />
          <Text style={[styles.label, (isPasswordFocused || password) && styles.labelFocused]}>Password</Text>
        </View>

        {loading ? (
          <ActivityIndicator size="large" color={theme.colors.primary} />
        ) : (
          <TouchableOpacity onPress={handleLogin} style={styles.buttonContainer}>
             <View style={styles.button}>
                {/* Simulated CSS animations with simple borders for now */}
                <View style={[styles.borderSpan, styles.borderTop]} />
                <View style={[styles.borderSpan, styles.borderRight]} />
                <View style={[styles.borderSpan, styles.borderBottom]} />
                <View style={[styles.borderSpan, styles.borderLeft]} />
                <Text style={styles.buttonText}>Submit</Text>
             </View>
          </TouchableOpacity>
        )}

        <View style={styles.footer}>
            <Text style={styles.footerText}>Don't have an account? </Text>
            <TouchableOpacity onPress={() => navigation.navigate('Register')}>
                <Text style={styles.link}>Sign up!</Text>
            </TouchableOpacity>
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: theme.colors.background, // #1a1a1a
  },
  lottieContainer: {
    width: 150,
    height: 150,
    marginBottom: 20,
  },
  lottie: {
    width: '100%',
    height: '100%',
  },
  loginBox: {
    width: 300,
    padding: 40,
    backgroundColor: theme.colors.cardBackground, // rgba(0,0,0,.9)
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 15 },
    shadowOpacity: 0.6,
    shadowRadius: 25,
    elevation: 10,
  },
  title: {
    margin: 0,
    padding: 0,
    color: theme.colors.text,
    textAlign: 'center',
    fontSize: 24,
    fontWeight: 'bold',
    letterSpacing: 1,
    marginBottom: 30,
  },
  userBox: {
    position: 'relative',
    marginBottom: 30,
  },
  input: {
    width: '100%',
    paddingVertical: 10,
    fontSize: 16,
    color: theme.colors.text,
    borderBottomWidth: 1,
    borderBottomColor: theme.colors.inputBorder,
    backgroundColor: 'transparent',
  },
  inputFocused: {
    // Input styles when focused if needed
  },
  label: {
    position: 'absolute',
    top: 0,
    left: 0,
    paddingVertical: 10,
    fontSize: 16,
    color: theme.colors.text,
    pointerEvents: 'none', // Not supported in RN, but logic handled by state
  },
  labelFocused: {
    top: -20,
    left: 0,
    color: theme.colors.primary, // Red or Theme Color
    fontSize: 12,
  },
  buttonContainer: {
    marginTop: 40,
    alignItems: 'center',
  },
  button: {
    position: 'relative',
    paddingVertical: 10,
    paddingHorizontal: 20,
    overflow: 'hidden',
  },
  buttonText: {
    color: theme.colors.buttonText,
    fontSize: 16,
    fontWeight: 'bold',
    textTransform: 'uppercase',
    letterSpacing: 3,
  },
  // Simple borders to mimic the span animations statically for now
  borderSpan: {
    position: 'absolute',
    backgroundColor: theme.colors.primary, 
  },
  borderTop: {
    top: 0,
    left: 0,
    width: '100%',
    height: 2,
  },
  borderRight: {
    top: 0,
    right: 0,
    width: 2,
    height: '100%',
  },
  borderBottom: {
    bottom: 0,
    right: 0,
    width: '100%',
    height: 2,
  },
  borderLeft: {
    bottom: 0,
    left: 0,
    width: 2,
    height: '100%',
  },
  footer: {
      flexDirection: 'row',
      marginTop: 20,
      justifyContent: 'center',
  },
  footerText: {
      color: '#aaa',
      fontSize: 14,
  },
  link: {
      color: theme.colors.text,
      fontSize: 14,
      textDecorationLine: 'none', // 'none' is default but explicit
  },
});
