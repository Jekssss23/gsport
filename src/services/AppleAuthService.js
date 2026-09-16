import { Platform } from 'react-native';
import { OAuthProvider, signInWithCredential } from 'firebase/auth';
import { doc, getDoc, setDoc } from 'firebase/firestore';
import { auth, db } from '../config/firebase';
import * as Crypto from 'expo-crypto';

// Dynamic import for expo-apple-authentication to avoid runtime errors on unsupported environments
const getAppleAuthenticationModule = () => {
  try {
    const AppleAuthentication = require('expo-apple-authentication');
    return AppleAuthentication;
  } catch (e) {
    console.warn('expo-apple-authentication module is not installed or unavailable:', e.message);
    return null;
  }
};

/**
 * Checks if Apple Authentication is available on this device/platform.
 */
export const isAppleAuthAvailable = async () => {
  if (Platform.OS !== 'ios') {
    return false;
  }
  const AppleAuth = getAppleAuthenticationModule();
  if (!AppleAuth || typeof AppleAuth.isAvailableAsync !== 'function') {
    return false;
  }
  try {
    return await AppleAuth.isAvailableAsync();
  } catch (error) {
    console.warn('Error checking Apple Auth availability:', error);
    return false;
  }
};

/**
 * Handles Apple Sign-In authentication flow with Firebase Auth & Firestore.
 */
export const handleAppleSignIn = async () => {
  try {
    const AppleAuth = getAppleAuthenticationModule();
    if (!AppleAuth) {
      throw new Error('Apple Sign-In is not supported or module is not installed.');
    }

    const available = await AppleAuth.isAvailableAsync();
    if (!available) {
      throw new Error('Apple Sign-In is not available on this device.');
    }

    // Generate random nonce for Firebase OAuth credential verification
    const rawNonce = Math.random().toString(36).substring(2, 10);
    const hashedNonce = await Crypto.digestStringAsync(
      Crypto.CryptoDigestAlgorithm.SHA256,
      rawNonce
    );

    const credential = await AppleAuth.signInAsync({
      requestedScopes: [
        AppleAuth.AppleAuthenticationScope.FULL_NAME,
        AppleAuth.AppleAuthenticationScope.EMAIL,
      ],
      nonce: hashedNonce,
    });

    const { identityToken } = credential;
    if (!identityToken) {
      throw new Error('Apple Sign-In failed: No identity token returned.');
    }

    // Create Firebase OAuth credential for apple.com provider
    const provider = new OAuthProvider('apple.com');
    const firebaseCredential = provider.credential({
      idToken: identityToken,
      rawNonce: rawNonce,
    });

    const userCredential = await signInWithCredential(auth, firebaseCredential);
    const user = userCredential.user;

    // Build user profile name from Apple credential or fallback
    let displayName = user.displayName;
    if (!displayName && credential.fullName) {
      const givenName = credential.fullName.givenName || '';
      const familyName = credential.fullName.familyName || '';
      displayName = `${givenName} ${familyName}`.trim();
    }
    if (!displayName) {
      displayName = user.email ? user.email.split('@')[0] : 'Apple User';
    }

    const userDocRef = doc(db, 'users', user.uid);
    const userDoc = await getDoc(userDocRef);

    if (!userDoc.exists()) {
      const userData = {
        email: user.email || credential.email || '',
        name: displayName,
        phoneNumber: user.phoneNumber || '',
        photoURL: user.photoURL || '',
        role: 'user',
        provider: 'apple',
        createdAt: new Date().toISOString(),
      };

      await setDoc(userDocRef, userData);
    }

    return { user, isNewUser: !userDoc.exists() };
  } catch (error) {
    if (error.code === 'ERR_REQUEST_CANCELED') {
      console.log('Apple Sign-In was cancelled by user');
      return { cancelled: true };
    }
    console.error('Apple Sign-In Error:', error);
    throw error;
  }
};
