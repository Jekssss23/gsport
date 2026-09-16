import admin from 'firebase-admin';
import fs from 'node:fs';
import path from 'node:path';

function getServiceAccount() {
  const raw = process.env.FIREBASE_SERVICE_ACCOUNT_KEY;
  const serviceAccountPath = process.env.FIREBASE_SERVICE_ACCOUNT_PATH;

  const normalize = (svc: any) => {
    if (!svc) return null;
    if (typeof svc.private_key === 'string') {
      let key = svc.private_key;
      // Strip accidental wrapping quotes
      key = key.replace(/^"|"$/g, '');
      // Normalize CRLF -> LF
      key = key.replace(/\r\n/g, '\n');
      // Convert literal \n sequences to real new lines
      key = key.replace(/\\n/g, '\n');
      key = key.trim();

      // Ensure standard PEM header/footer formatting
      if (key.includes('-----BEGIN PRIVATE KEY-----') && !key.includes('-----END PRIVATE KEY-----')) {
        // leave as-is, but will likely fail validation below
      }
      if (!key.endsWith('\n')) key = key + '\n';

      svc.private_key = key;
    }
    return svc;
  };

  if (raw) {
    try {
      return normalize(JSON.parse(raw));
    } catch (_e) {
      // fall through
    }
  }

  if (serviceAccountPath) {
    try {
      const file = fs.readFileSync(serviceAccountPath, 'utf8');
      return normalize(JSON.parse(file));
    } catch (_e) {
      return null;
    }
  }

  // Optional fallback for local development: look for a service account file in the project root
  try {
    const localPath = path.join(process.cwd(), 'g-sports-center-firebase-adminsdk-fbsvc-37f4a3bb30.json');
    if (!fs.existsSync(localPath)) return null;
    const file = fs.readFileSync(localPath, 'utf8');
    return normalize(JSON.parse(file));
  } catch (_e) {
    return null;
  }
}

export function getAdminApp() {
  if (admin.apps.length > 0) return admin.app();

  const serviceAccount = getServiceAccount();
  if (!serviceAccount) {
    throw new Error(
      'Missing FIREBASE_SERVICE_ACCOUNT_KEY env var. Provide the Firebase service account JSON as a string.'
    );
  }

  return admin.initializeApp({
    credential: admin.credential.cert(serviceAccount as admin.ServiceAccount),
  });
}

export function getAdminAuth() {
  return admin.auth(getAdminApp());
}

export function getAdminDb() {
  return admin.firestore(getAdminApp());
}
