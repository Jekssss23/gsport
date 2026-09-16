const { initializeApp } = require('firebase/app');
const { getFirestore, collection, doc, setDoc, serverTimestamp } = require('firebase/firestore');
const { getAuth, createUserWithEmailAndPassword } = require('firebase/auth');
const { start } = require('repl');

// Firebase configuration - make sure to set these environment variables
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
const auth = getAuth(app);

// Sample data
const users = [
  {
    id: 'admin-001',
    email: 'admin@gsportscenter.com',
    password: 'Admin@123',
    role: 'admin',
    name: 'Admin User',
  },
  {
    id: 'instructor-001',
    email: 'instructor@gsportscenter.com',
    password: 'Instructor@123',
    role: 'instructor',
    name: 'John Smith',
  },
  {
    id: 'instructor-002',
    email: 'coach@gsportscenter.com',
    password: 'Coach@123',
    role: 'instructor',
    name: 'Sarah Johnson',
  },
];

const facilities = [
  {
    id: 'facility-001',
    name: 'Basketball Court A',
    type: 'court',
    capacity: 50,
    status: 'available',
    occupancy: 0,
  },
  {
    id: 'facility-002',
    name: 'Basketball Court B',
    type: 'court',
    capacity: 50,
    status: 'busy',
    occupancy: 35,
  },
  {
    id: 'facility-003',
    name: 'Fitness Studio',
    type: 'studio',
    capacity: 30,
    status: 'available',
    occupancy: 0,
  },
  {
    id: 'facility-004',
    name: 'Weight Training Area',
    type: 'gym',
    capacity: 40,
    status: 'busy',
    occupancy: 25,
  },
  {
    id: 'facility-005',
    name: 'Swimming Pool',
    type: 'pool',
    capacity: 60,
    status: 'maintenance',
    occupancy: 0,
  },
];

const members = [
  {
    id: 'member-001',
    name: 'Michael Chen',
    email: 'michael.chen@email.com',
    phone: '08123456789',
    membershipType: 'monthly',
    joinDate: '2024-01-15',
    status: 'active',
  },
  {
    id: 'member-002',
    name: 'Lisa Rodriguez',
    email: 'lisa.rodriguez@email.com',
    phone: '08234567890',
    membershipType: 'yearly',
    joinDate: '2023-12-01',
    status: 'active',
  },
  {
    id: 'member-003',
    name: 'David Kim',
    email: 'david.kim@email.com',
    phone: '08345678901',
    membershipType: 'monthly',
    joinDate: '2024-01-20',
    status: 'active',
  },
  {
    id: 'member-004',
    name: 'Emma Wilson',
    email: 'emma.wilson@email.com',
    phone: '08456789012',
    membershipType: 'yearly',
    joinDate: '2023-11-15',
    status: 'active',
  },
  {
    id: 'member-005',
    name: 'James Brown',
    email: 'james.brown@email.com',
    phone: '08567890123',
    membershipType: 'monthly',
    joinDate: '2024-01-10',
    status: 'inactive',
  },
];

const classes = [
  {
    id: 'class-001',
    name: 'Morning Yoga',
    day: 'Monday',
    startTime: '06:00',
    endTime: '07:00',
    enrolled: 0,
    capacity: 25,
    instructor: 'John Smith',
    instructorId: 'instructor-001',
    status: 'active',
    price: 75000,
  },
  {
    id: 'class-002',
    name: 'HIIT Training',
    day: 'Tuesday',
    startTime: '19:00',
    endTime: '20:00',
    enrolled: 0,
    capacity: 20,
    instructor: 'Sarah Johnson',
    instructorId: 'instructor-002',
    status: 'active',
    price: 100000,
  },
  {
    id: 'class-003',
    name: 'Pilates',
    day: 'Wednesday',
    startTime: '06:30',
    endTime: '07:30',
    enrolled: 0,
    capacity: 15,
    instructor: 'John Smith',
    instructorId: 'instructor-001',
    status: 'active',
    price: 85000,
  },
  {
    id: 'class-004',
    name: 'Zumba Dance',
    day: 'Thursday',
    startTime: '18:00',
    endTime: '19:00',
    enrolled: 0,
    capacity: 30,
    instructor: 'Sarah Johnson',
    instructorId: 'instructor-002',
    status: 'active',
    price: 80000,
  },
  {
    id: 'class-005',
    name: 'Strength Training',
    day: 'Friday',
    startTime: '17:00',
    endTime: '18:00',
    enrolled: 0,
    capacity: 18,
    instructor: 'John Smith',
    instructorId: 'instructor-001',
    status: 'inactive',
    price: 120000,
  },
];

const bookings = [
  {
    id: 'booking-001',
    memberId: 'member-001',
    facilityId: 'facility-001',
    memberName: 'Michael Chen',
    facilityName: 'Basketball Court A',
    date: '2024-01-25',
    startTime: '10:00 AM',
    endTime: '12:00 PM',
    status: 'confirmed',
    amount: 150000,
  },
  {
    id: 'booking-002',
    memberId: 'member-002',
    facilityId: 'facility-003',
    memberName: 'Lisa Rodriguez',
    facilityName: 'Fitness Studio',
    date: '2024-01-26',
    startTime: '2:00 PM',
    endTime: '4:00 PM',
    status: 'pending',
    amount: 120000,
  },
  {
    id: 'booking-003',
    memberId: 'member-003',
    facilityId: 'facility-002',
    memberName: 'David Kim',
    facilityName: 'Basketball Court B',
    date: '2024-01-27',
    startTime: '8:00 AM',
    endTime: '10:00 AM',
    status: 'confirmed',
    amount: 150000,
  },
  {
    id: 'booking-004',
    memberId: 'member-004',
    facilityId: 'facility-004',
    memberName: 'Emma Wilson',
    facilityName: 'Weight Training Area',
    date: '2024-01-28',
    startTime: '6:00 PM',
    endTime: '8:00 PM',
    status: 'pending',
    amount: 100000,
  },
  {
    id: 'booking-005',
    memberId: 'member-001',
    facilityId: 'facility-003',
    memberName: 'Michael Chen',
    facilityName: 'Fitness Studio',
    date: '2024-01-24',
    startTime: '7:00 PM',
    endTime: '9:00 PM',
    status: 'cancelled',
    amount: 120000,
  },
];

const attendance = [
  {
    id: 'attendance-001',
    classId: 'class-001',
    memberId: 'member-001',
    memberName: 'Michael Chen',
    className: 'Morning Yoga',
    date: '2024-01-22',
    status: 'present',
    checkInTime: '6:05 AM',
  },
  {
    id: 'attendance-002',
    classId: 'class-001',
    memberId: 'member-002',
    memberName: 'Lisa Rodriguez',
    className: 'Morning Yoga',
    date: '2024-01-22',
    status: 'late',
    checkInTime: '6:15 AM',
  },
  {
    id: 'attendance-003',
    classId: 'class-002',
    memberId: 'member-003',
    memberName: 'David Kim',
    className: 'HIIT Training',
    date: '2024-01-23',
    status: 'present',
    checkInTime: '7:00 PM',
  },
  {
    id: 'attendance-004',
    classId: 'class-002',
    memberId: 'member-004',
    memberName: 'Emma Wilson',
    className: 'HIIT Training',
    date: '2024-01-23',
    status: 'absent',
    checkInTime: null,
  },
  {
    id: 'attendance-005',
    classId: 'class-003',
    memberId: 'member-001',
    memberName: 'Michael Chen',
    className: 'Pilates',
    date: '2024-01-24',
    status: 'present',
    checkInTime: '6:30 AM',
  },
];

async function seedCollection(collectionName, data) {
  console.log(`Seeding ${collectionName} collection...`);
  
  for (const item of data) {
    try {
      const { id, ...itemData } = item;
      await setDoc(doc(db, collectionName, id), {
        ...itemData,
        createdAt: new Date().toISOString(),
      });
      console.log(`✓ Created ${collectionName}: ${item.name || item.email || id}`);
    } catch (error) {
      console.error(`✗ Error creating ${collectionName} ${item.id}:`, error.message);
    }
  }
}

async function createAuthUsers() {
  console.log('Creating authentication users...');
  
  for (const user of users) {
    try {
      const userCredential = await createUserWithEmailAndPassword(auth, user.email, user.password);
      console.log(`✓ Created auth user: ${user.email}`);
      
      // Create user document with the actual Firebase UID
      await setDoc(doc(db, 'users', userCredential.user.uid), {
        email: user.email,
        role: user.role,
        name: user.name,
        createdAt: new Date().toISOString(),
      });
      console.log(`✓ Created user document: ${user.email}`);
    } catch (error) {
      if (error.code === 'auth/email-already-in-use') {
        console.log(`⚠ User already exists: ${user.email}`);
        console.log('Please manually create the user document in Firestore with the correct UID');
      } else {
        console.error(`✗ Error creating auth user ${user.email}:`, error.message);
      }
    }
  }
}

async function seed() {
  try {
    console.log('🌱 Starting G Sports Center database seeding...\n');

    // Create authentication users first
    await createAuthUsers();
    console.log('');

    // Seed all collections
    await seedCollection('facilities', facilities);
    console.log('');
    
    await seedCollection('members', members);
    console.log('');
    
    await seedCollection('classes', classes);
    console.log('');
    
    await seedCollection('bookings', bookings);
    console.log('');
    
    await seedCollection('attendance', attendance);
    console.log('');

    console.log('🎉 Database seeding completed successfully!');
    console.log('\nYou can now login with:');
    console.log('Admin: admin@gsportscenter.com / Admin@123');
    console.log('Instructor: instructor@gsportscenter.com / Instructor@123');
    console.log('Coach: coach@gsportscenter.com / Coach@123');
    
  } catch (error) {
    console.error('❌ Seeding failed:', error);
  } finally {
    process.exit(0);
  }
}

// Run the seeder
seed();