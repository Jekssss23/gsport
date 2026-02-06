import { initializeApp } from 'firebase/app';
import { getFirestore, collection, addDoc, doc, setDoc } from 'firebase/firestore';

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

const seedData = async () => {
  try {
    console.log('Starting to seed GSC facilities and courts...');

    // Facilities data
    const facilities = [
      {
        id: 'futsal',
        name: 'Futsal',
        type: 'court',
        pricePerHour: 100000,
        dpPercentage: 50,
        isActive: true,
        courts: [
          { name: 'Futsal Court 1' },
          { name: 'Futsal Court 2' }
        ]
      },
      {
        id: 'badminton',
        name: 'Badminton',
        type: 'court',
        pricePerHour: 50000,
        dpPercentage: 50,
        isActive: true,
        courts: [
          { name: 'Badminton Court 1' },
          { name: 'Badminton Court 2' },
          { name: 'Badminton Court 3' },
          { name: 'Badminton Court 4' }
        ]
      },
      {
        id: 'pickleball',
        name: 'Pickle Ball',
        type: 'court',
        pricePerHour: 60000,
        dpPercentage: 50,
        isActive: true,
        courts: [
          { name: 'Pickle Ball Court 1' },
          { name: 'Pickle Ball Court 2' }
        ]
      },
      {
        id: 'billiard',
        name: 'Billiard',
        type: 'table',
        pricePerHour: 40000,
        dpPercentage: 50,
        isActive: true,
        courts: [
          { name: 'Billiard Table 1' },
          { name: 'Billiard Table 2' },
          { name: 'Billiard Table 3' }
        ]
      },
      {
        id: 'playstation',
        name: 'PlayStation',
        type: 'console',
        pricePerHour: 25000,
        dpPercentage: 50,
        isActive: true,
        courts: [
          { name: 'PS Console 1' },
          { name: 'PS Console 2' },
          { name: 'PS Console 3' },
          { name: 'PS Console 4' }
        ]
      }
    ];

    // Add facilities
    for (const facility of facilities) {
      const { courts, ...facilityData } = facility;
      
      // Add facility
      await setDoc(doc(db, 'facilities', facility.id), {
        ...facilityData,
        createdAt: new Date().toISOString()
      });
      
      console.log(`Added facility: ${facility.name}`);

      // Add courts for this facility
      for (const court of courts) {
        const courtRef = await addDoc(collection(db, 'courts'), {
          name: court.name,
          facilityId: facility.id,
          isActive: true,
          createdAt: new Date().toISOString()
        });
        
        console.log(`Added court: ${court.name} for ${facility.name}`);
      }
    }

    console.log('✅ Successfully seeded all facilities and courts!');
    console.log('📋 Summary:');
    console.log('- Futsal: 2 courts');
    console.log('- Badminton: 4 courts');
    console.log('- Pickle Ball: 2 courts');
    console.log('- Billiard: 3 tables');
    console.log('- PlayStation: 4 consoles');
    
  } catch (error) {
    console.error('❌ Error seeding data:', error);
  }
};

seedData();