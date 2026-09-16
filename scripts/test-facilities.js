// Test script to verify facilities in Firestore
const { initializeApp } = require('firebase/app');
const { getFirestore, collection, getDocs } = require('firebase/firestore');

const firebaseConfig = {
  apiKey: "AIzaSyAVwhIU5U3JFNB_TfQDjcKYoZyBPLxHaCM",
  authDomain: "g-sports-center.firebaseapp.com",
  projectId: "g-sports-center",
  storageBucket: "g-sports-center.firebasestorage.app",
  messagingSenderId: "253539109830",
  appId: "1:253539109830:web:4e1cd4855084a457a800c7",
  measurementId: "G-G5JP1R7V1F"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

async function testFacilities() {
  try {
    console.log('📡 Fetching facilities from Firestore...\n');
    
    const facilitiesRef = collection(db, 'facilities');
    const snapshot = await getDocs(facilitiesRef);
    
    console.log(`✅ Found ${snapshot.size} facilities\n`);
    
    snapshot.docs.forEach((doc, index) => {
      const data = doc.data();
      console.log(`${index + 1}. ${data.name}`);
      console.log(`   ID: ${doc.id}`);
      console.log(`   Price: Rp ${data.pricePerHour?.toLocaleString()}/hour`);
      console.log(`   DP: ${data.dpPercentage}%`);
      console.log(`   Courts: ${data.courts?.length || 0}`);
      data.courts?.forEach(court => {
        console.log(`     - ${court.name} (${court.id})`);
      });
      console.log('');
    });
    
    process.exit(0);
  } catch (error) {
    console.error('❌ Error:', error);
    process.exit(1);
  }
}

testFacilities();
