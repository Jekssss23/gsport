// Script to seed facilities data to Firestore
// Run: node scripts/seed-facilities.js

const { initializeApp } = require('firebase/app');
const { getFirestore, collection, addDoc, getDocs, deleteDoc } = require('firebase/firestore');

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

const facilities = [
  {
    name: "Futsal",
    type: "Court",
    pricePerHour: 150000,
    dpPercentage: 50,
    courts: [
      { id: "futsal-1", name: "Futsal Court 1" },
      { id: "futsal-2", name: "Futsal Court 2" }
    ]
  },
  {
    name: "Badminton",
    type: "Court",
    pricePerHour: 80000,
    dpPercentage: 50,
    courts: [
      { id: "badminton-1", name: "Badminton Court 1" }
    ]
  },
  {
    name: "Pickleball",
    type: "Court",
    pricePerHour: 100000,
    dpPercentage: 50,
    courts: [
      { id: "pickleball-1", name: "Pickleball Court 1" },
      { id: "pickleball-2", name: "Pickleball Court 2" }
    ]
  }
];

async function seedFacilities() {
  try {
    console.log('🌱 Starting to seed facilities...');
    
    const facilitiesRef = collection(db, 'facilities');
    const snapshot = await getDocs(facilitiesRef);
    
    console.log(`🗑️  Deleting ${snapshot.size} existing facilities...`);
    for (const doc of snapshot.docs) {
      await deleteDoc(doc.ref);
    }
    
    console.log('➕ Adding new facilities...');
    for (const facility of facilities) {
      const docRef = await addDoc(facilitiesRef, facility);
      console.log(`✅ Added ${facility.name} with ID: ${docRef.id}`);
    }
    
    console.log('🎉 Seeding completed successfully!');
    console.log('\nFacilities Summary:');
    console.log('- Futsal: 2 courts, Rp 150,000/hour, 50% DP');
    console.log('- Badminton: 1 court, Rp 80,000/hour, 50% DP');
    console.log('- Pickleball: 2 courts, Rp 100,000/hour, 50% DP');
    console.log('\nTime slots: 7:00 AM - 11:00 PM (17 slots per day)');
    
    process.exit(0);
  } catch (error) {
    console.error('❌ Error seeding facilities:', error);
    process.exit(1);
  }
}

seedFacilities();
