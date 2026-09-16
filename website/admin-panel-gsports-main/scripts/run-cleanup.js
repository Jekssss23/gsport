#!/usr/bin/env node

require('dotenv').config({ path: '.env.local' });

// Check if environment variables are set
const requiredEnvVars = [
  'NEXT_PUBLIC_FIREBASE_API_KEY',
  'NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN',
  'NEXT_PUBLIC_FIREBASE_PROJECT_ID',
  'NEXT_PUBLIC_FIREBASE_STORAGE_BUCKET',
  'NEXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID',
  'NEXT_PUBLIC_FIREBASE_APP_ID'
];

const missingVars = requiredEnvVars.filter(varName => !process.env[varName]);

if (missingVars.length > 0) {
  console.error('❌ Missing required environment variables:');
  missingVars.forEach(varName => console.error(`   - ${varName}`));
  console.error('\nPlease create a .env.local file with your Firebase configuration.');
  process.exit(1);
}

console.log('⚠️  WARNING: This will delete ALL data from your Firestore collections!');
console.log('✓ Environment variables loaded');
console.log(`✓ Project ID: ${process.env.NEXT_PUBLIC_FIREBASE_PROJECT_ID}`);
console.log('');

// Run the cleanup
require('./cleanup.js');