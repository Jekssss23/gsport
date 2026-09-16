const { initializeApp } = require('firebase/app');
const { getFirestore, collection, getDocs, deleteDoc, doc } = require('firebase/firestore');

// Firebase configuration
const firebaseConfig = {
  apiKey: process.env.NEXT_PUBLIC_FIREBASE_API_KEY,
  authDomain: process.env.NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN,
  projectId: process.env.NEXT_PUBLIC_FIREBASE_PROJECT_ID,
  storageBucket: process.env.NEXT_PUBLIC_FIREBASE_STORAGE_BUCKET,
  messagingSenderId: process.env.NEXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
  appId: process.env.NEXT_PUBLIC_FIREBASE_APP_ID,
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

const collections = ['users', 'facilities', 'members', 'classes', 'bookings', 'attendance'];

async function clearCollection(collectionName) {
  console.log(`Clearing ${collectionName} collection...`);
  
  try {
    const querySnapshot = await getDocs(collection(db, collectionName));
    const deletePromises = [];
    
    querySnapshot.forEach((document) => {
      deletePromises.push(deleteDoc(doc(db, collectionName, document.id)));
    });
    
    await Promise.all(deletePromises);
    console.log(`✓ Cleared ${querySnapshot.size} documents from ${collectionName}`);
  } catch (error) {
    console.error(`✗ Error clearing ${collectionName}:`, error.message);
  }
}

async function cleanup() {
  try {
    console.log('🧹 Starting database cleanup...\n');
    
    for (const collectionName of collections) {
      await clearCollection(collectionName);
    }
    
    console.log('\n🎉 Database cleanup completed!');
    console.log('\nNote: This only clears Firestore collections.');
    console.log('Firebase Auth users need to be deleted manually from the Firebase Console.');
    
  } catch (error) {
    console.error('❌ Cleanup failed:', error);
  } finally {
    process.exit(0);
  }
}

cleanup();