import { initializeApp, getApps } from 'firebase/app';
import { getAuth } from 'firebase/auth';
import { getFirestore } from 'firebase/firestore';

const firebaseConfig = {
  apiKey: "AIzaSyAVwhIU5U3JFNB_TfQDjcKYoZyBPLxHaCM",
  authDomain: "g-sports-center.firebaseapp.com",
  projectId: "g-sports-center",
  storageBucket: "g-sports-center.firebasestorage.app",
  messagingSenderId: "253539109830",
  appId: "1:253539109830:web:4e1cd4855084a457a800c7",
  measurementId: "G-G5JP1R7V1F"
};

const app = getApps().length === 0 ? initializeApp(firebaseConfig) : getApps()[0];
export const auth = getAuth(app);
export const db = getFirestore(app);

export default app;
