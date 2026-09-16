import { initializeApp, getApps, getApp } from 'firebase/app';
import { getFirestore } from 'firebase/firestore';
import { getStorage } from 'firebase/storage';
import { initializeAuth, getReactNativePersistence, getAuth } from 'firebase/auth';
import ReactNativeAsyncStorage from '@react-native-async-storage/async-storage';

const firebaseConfig = {
  apiKey: "AIzaSyAVwhIU5U3JFNB_TfQDjcKYoZyBPLxHaCM",
  authDomain: "g-sports-center.firebaseapp.com",
  projectId: "g-sports-center",
  storageBucket: "g-sports-center.firebasestorage.app",
  messagingSenderId: "253539109830",
  appId: "1:253539109830:web:4e1cd4855084a457a800c7",
  measurementId: "G-G5JP1R7V1F"
};

const app = getApps().length === 0 ? initializeApp(firebaseConfig) : getApp();
export const db = getFirestore(app);
export const storage = getStorage(app);

// Selalu initializeAuth dengan persistence (aman untuk hot reload & production)
let firebaseAuth;
try {
  firebaseAuth = initializeAuth(app, {
    persistence: getReactNativePersistence(ReactNativeAsyncStorage)
  });
} catch (e) {
  // Jika sudah di-initialize, ambil instance yang sudah ada
  firebaseAuth = getAuth(app);
}

export const auth = firebaseAuth;