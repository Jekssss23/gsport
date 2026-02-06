import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, Alert, TouchableOpacity, ActivityIndicator } from 'react-native';
import { createUserWithEmailAndPassword } from 'firebase/auth';
import { doc, setDoc } from 'firebase/firestore';
import { auth, db } from '../config/firebase.js';
import { theme } from '../styles/theme.js';

export default function RegisterScreen({ navigation }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [name, setName] = useState('');
  const [loading, setLoading] = useState(false);
  const [isNameFocused, setIsNameFocused] = useState(false);
  const [isEmailFocused, setIsEmailFocused] = useState(false);
  const [isPasswordFocused, setIsPasswordFocused] = useState(false);

  const handleRegister = async () => {
    if (!email || !password || !name) {
      Alert.alert('Error', 'Please fill in all fields');
      return;
    }

    setLoading(true);
    try {
      const userCredential = await createUserWithEmailAndPassword(auth, email, password);
      const user = userCredential.user;

      // Save user data to Firestore - semua registrasi otomatis jadi user
      await setDoc(doc(db, 'users', user.uid), {
        email: user.email,
        name: name,
        role: 'user', // Semua registrasi baru otomatis jadi user
        createdAt: new Date().toISOString(),
      });

      Alert.alert('Success', 'Account created successfully!');
    } catch (error) {
      Alert.alert('Registration Error', error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.loginBox}>
        <Text style={styles.title}>Register</Text>
        
        <View style={styles.userBox}>
          <TextInput
            style={[styles.input, (isNameFocused || name) && styles.inputFocused]}
            value={name}
            onChangeText={setName}
            onFocus={() => setIsNameFocused(true)}
            onBlur={() => setIsNameFocused(false)}
            placeholderTextColor="transparent"
          />
          <Text style={[styles.label, (isNameFocused || name) && styles.labelFocused]}>Full Name</Text>
        </View>

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
          <TouchableOpacity onPress={handleRegister} style={styles.buttonContainer}>
             <View style={styles.button}>
                <View style={[styles.borderSpan, styles.borderTop]} />
                <View style={[styles.borderSpan, styles.borderRight]} />
                <View style={[styles.borderSpan, styles.borderBottom]} />
                <View style={[styles.borderSpan, styles.borderLeft]} />
                <Text style={styles.buttonText}>Submit</Text>
             </View>
          </TouchableOpacity>
        )}

        <View style={styles.footer}>
            <Text style={styles.footerText}>Already have an account? </Text>
            <TouchableOpacity onPress={() => navigation.navigate('Login')}>
                <Text style={styles.link}>Login</Text>
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
    backgroundColor: theme.colors.background,
  },
  loginBox: {
    width: 300,
    padding: 40,
    backgroundColor: theme.colors.cardBackground,
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
  label: {
    position: 'absolute',
    top: 0,
    left: 0,
    paddingVertical: 10,
    fontSize: 16,
    color: theme.colors.text,
    pointerEvents: 'none',
  },
  labelFocused: {
    top: -20,
    left: 0,
    color: theme.colors.primary,
    fontSize: 12,
  },

  buttonContainer: {
    marginTop: 20,
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
      textDecorationLine: 'none',
  },
});
