import { NextResponse } from 'next/server';
import { getAdminAuth, getAdminDb } from '@/lib/firebase-admin';

export async function POST(req: Request) {
  try {
    const authHeader = req.headers.get('authorization') || '';
    const match = authHeader.match(/^Bearer (.+)$/);
    const idToken = match?.[1];

    if (!idToken) {
      return NextResponse.json({ error: 'Missing Authorization token' }, { status: 401 });
    }

    const body = await req.json();
    const { employeeId, email, password } = body || {};

    if (!employeeId || !email || !password) {
      return NextResponse.json({ error: 'employeeId, email, password are required' }, { status: 400 });
    }

    const adminAuth = getAdminAuth();
    const adminDb = getAdminDb();

    const decoded = await adminAuth.verifyIdToken(idToken);

    const requesterUserDoc = await adminDb.collection('users').doc(decoded.uid).get();
    const requesterRole = requesterUserDoc.exists ? requesterUserDoc.data()?.role : null;

    if (requesterRole !== 'admin') {
      return NextResponse.json({ error: 'Forbidden' }, { status: 403 });
    }

    const employeeRef = adminDb.collection('employees').doc(employeeId);
    const employeeSnap = await employeeRef.get();

    if (!employeeSnap.exists) {
      return NextResponse.json({ error: 'Employee not found' }, { status: 404 });
    }

    const employeeData = employeeSnap.data() || {};

    if (employeeData.authUid) {
      return NextResponse.json({ error: 'Employee already has an account', authUid: employeeData.authUid }, { status: 409 });
    }

    const userRecord = await adminAuth.createUser({
      email,
      password,
      displayName: employeeData.name || undefined,
    });

    const now = new Date().toISOString();

    await adminDb.collection('users').doc(userRecord.uid).set(
      {
        id: userRecord.uid,
        email,
        name: employeeData.name || email,
        role: 'admin',
        employeeId,
        createdAt: now,
      },
      { merge: true }
    );

    await employeeRef.set(
      {
        authUid: userRecord.uid,
        email,
        hasAccount: true,
        updatedAt: now,
      },
      { merge: true }
    );

    return NextResponse.json({ ok: true, uid: userRecord.uid });
  } catch (error: any) {
    console.error('Create employee account error:', error);
    return NextResponse.json(
      {
        error: error?.message || 'Internal error',
        code: error?.code,
      },
      { status: 500 }
    );
  }
}
